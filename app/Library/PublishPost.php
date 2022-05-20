<?php

namespace App\Library;

use App\Models\Account;
use App\Models\Post;
use InstagramAPI\Constants;
use InstagramAPI\Exception\AccountDisabledException;
use InstagramAPI\Exception\ChallengeRequiredException;
use InstagramAPI\Exception\CheckpointRequiredException;
use InstagramAPI\Exception\ConsentRequiredException;
use InstagramAPI\Exception\FeedbackRequiredException;
use InstagramAPI\Exception\IncorrectPasswordException;
use InstagramAPI\Exception\InvalidUserException;
use InstagramAPI\Exception\SentryBlockException;
use InstagramAPI\Exception\ServerMessageThrower;
use InstagramAPI\Instagram;
use InstagramAPI\Media\Photo\InstagramPhoto;
use InstagramAPI\Media\Video\InstagramVideo;
use InstagramAPI\Response\Model\Location;
use Spatie\MediaLibrary\Models\Media;

class PublishPost
{
    public function __construct(Post $post)
    {
        // Lookup for matching account
        $account = Account::withoutGlobalScopes()->active()->find($post->account_id);

        // If account/user deleted, but job still exists
        if (is_null($account)) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Account or user has been deleted';
            $post->save();

            return false;
        }

        // Delete job from queue if no subscription or trial expired
        if (!$account->user->subscribed() && !$account->user->onTrial()) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'No subscription or trial has been expired';
            $post->save();

            return false;
        }

        // Create instance
        $instagram = new Instagram(config('pilot.debug'), config('pilot.truncatedDebug'), config('pilot.storageConfig'));

        // Set proxy if exists
        if ($account->proxy) {
            $instagram->setProxy($account->proxy->server);
        }

        $instagram->setPlatform($account->platform);

        // Login to Instagram
        try {

            $instagram->login($account->username, $account->password);

        } catch (IncorrectPasswordException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'The password you entered is incorrect. Please try again. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (InvalidUserException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'The username you entered doesn\'t appear to belong to an account. Please check your username and try again. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (SentryBlockException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Your account has been banned from Instagram API for spam behaviour or otherwise abusing. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (AccountDisabledException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Your account has been disabled for violating Instagram terms. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (FeedbackRequiredException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Feedback required. It looks like you were misusing this feature by going too fast. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (CheckpointRequiredException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Your account is subject to verification checkpoint. Please go to instagram.com and pass checkpoint. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (ChallengeRequiredException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Challenge required. Please re-add your account to confirm it. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (ConsentRequiredException $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'You should verify and agree terms using your mobile device. ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (ServerMessageThrower $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Something went wrong: ' . $e->getMessage();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;

        } catch (\Exception $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine();
            $post->save();

            $account->is_active = false;
            $account->save();

            return false;
        }

        // Set caption
        $metadata['caption'] = $post->caption;

        // Set location
        if (isset($post->ig['location'])) {
            $location = unserialize($post->ig['location']);

            if ($location instanceof Location) {
                $metadata['location'] = $location;
            }
        }

        // Get media and prepare for publishing
        $media = Media::where('model_id', $account->user->id)
            ->whereIn('id', $post->ig['media'])
            ->orderByRaw("FIELD(`id`, '" . join("', '", $post->ig['media']) . "')")
            ->get();

        // Size matters depending on post type
        switch ($post->type) {
            case 'album':
                $targetFeed = Constants::FEED_TIMELINE_ALBUM;
                break;
            case 'story':
                $targetFeed = Constants::FEED_STORY;
                break;
            case 'post':
                $targetFeed = Constants::FEED_TIMELINE;
                break;
            default:
                break;
        }

        // Prepare media before publishing
        $validMedia = [];

        // Prepare all media
        foreach ($media as $file) {

            $photo = $video = null;

            if (in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif'])) {

                // Resize photo files
                try {

                    $photo = new InstagramPhoto($file->getPath(), [
                        'targetFeed' => $targetFeed,
                    ]);

                    // We must prevent the InstagramMedia object from destructing too early,
                    // because the media class auto-deletes the processed file during their
                    // destructor's cleanup (so we wouldn't be able to upload those files).
                    $validMedia[] = [
                        'file'  => $photo->getFile(),
                        'type'  => 'photo',
                        '__tmp' => $photo, // Save object in an unused array key.
                    ];

                } catch (\Exception $e) {

                    $post->status  = config('pilot.POST_STATUS_FAILED');
                    $post->comment = 'Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine();
                    $post->save();

                    return false;
                }

            } else {

                // Resize video files
                try {

                    $video = new InstagramVideo($file->getPath(), [
                        'targetFeed' => $targetFeed,
                    ]);

                    // We must prevent the InstagramMedia object from destructing too early,
                    // because the media class auto-deletes the processed file during their
                    // destructor's cleanup (so we wouldn't be able to upload those files).
                    $validMedia[] = [
                        'file'  => $video->getFile(),
                        'type'  => 'video',
                        '__tmp' => $video, // Save object in an unused array key.
                    ];

                } catch (\Exception $e) {

                    $post->status  = config('pilot.POST_STATUS_FAILED');
                    $post->comment = 'Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine();
                    $post->save();

                    return false;

                }

            }
        }

        // Publish
        try {

            switch ($post->type) {

                case 'album':

                    $result = $instagram->timeline->uploadAlbum($validMedia, $metadata);

                    break;

                case 'story':

                    // Get first element
                    $validMedia = reset($validMedia);

                    if ($validMedia['type'] == 'photo') {
                        $result = $instagram->story->uploadPhoto($validMedia['file'], $metadata);
                    } else {
                        $result = $instagram->story->uploadVideo($validMedia['file'], $metadata);
                    }

                    break;

                case 'post':

                    // Get first element
                    $validMedia = reset($validMedia);

                    if ($validMedia['type'] == 'photo') {
                        $result = $instagram->timeline->uploadPhoto($validMedia['file'], $metadata);
                    } else {
                        $result = $instagram->timeline->uploadVideo($validMedia['file'], $metadata);
                    }

                    break;

                default:
                    break;
            }

            // Save response to post
            if ($result->hasMedia()) {

                // Get published media item
                $media = $result->getMedia();

                $ig         = $post->ig;
                $ig['pk']   = $media->getPk();
                $ig['id']   = $media->getId();
                $ig['code'] = $media->getCode();

                // Extract first media from carousel
                if ($carousel_media = $media->getCarouselMedia()) {
                    $mediaCollection = reset($carousel_media);
                }

                // On timeline posts media directly accessible
                if ($media->getImageVersions2()) {
                    $mediaCollection = $media;
                }

                // Get all image versions
                if ($image_versions = $mediaCollection->getImageVersions2()) {

                    if ($candidates = $image_versions->getCandidates()) {

                        foreach ($candidates as $candidate) {

                            $ig['image_versions'][] = [
                                'width'  => $candidate->getWidth(),
                                'height' => $candidate->getHeight(),
                                'url'    => $candidate->getUrl(),
                            ];
                        }

                    }
                }

                $post->ig        = $ig;
                $post->status    = config('pilot.POST_STATUS_PUBLISHED');
                $post->comment   = 'Post published successfully';
                $post->posted_at = now();
                $post->save();

                // First comment (optional)
                if (!empty($post->first_comment)) {

                    // Simulate real human behavior
                    sleep(rand(5, 10));

                    $instagram->media->comment($media->getId(), $post->first_comment);
                }

                return true;

            }

        } catch (\Exception $e) {

            $post->status  = config('pilot.POST_STATUS_FAILED');
            $post->comment = 'Something went wrong: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine();
            $post->save();

            return false;

        }

    }

}
