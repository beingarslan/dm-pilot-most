<?php

namespace InstagramAPI\Request;

use InstagramAPI\Response;
use InstagramAPI\Constants;
use InstagramAPI\Signatures;
use InstagramAPI\Utils;

/**
 * Functions for exploring and interacting with live broadcasts.
 */
class Live extends RequestCollection
{
    /**
     * Get suggested broadcasts.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\SuggestedBroadcastsResponse
     */
    public function getSuggestedBroadcasts()
    {
        $endpoint = 'live/get_suggested_broadcasts/';
        if ($this->ig->isExperimentEnabled('ig_android_live_suggested_live_expansion', 'is_enabled')) {
            $endpoint = 'live/get_suggested_live_and_post_live/';
        }

        return $this->ig->request($endpoint)
            ->getResponse(new Response\SuggestedBroadcastsResponse());
    }

    /**
     * Get broadcast information.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastInfoResponse
     */
    public function getInfo(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/info/")
            ->getResponse(new Response\BroadcastInfoResponse());
    }

    /**
     * Get the viewer list of a broadcast.
     *
     * WARNING: You MUST be the owner of the broadcast. Otherwise Instagram won't send any API reply!
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ViewerListResponse
     */
    public function getViewerList(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/get_viewer_list/")
            ->getResponse(new Response\ViewerListResponse());
    }

    /**
     * Get the final viewer list of a broadcast after it has ended.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\FinalViewerListResponse
     */
    public function getFinalViewerList(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/get_final_viewer_list/")
            ->getResponse(new Response\FinalViewerListResponse());
    }

    /**
     * Get the viewer list of a post-live (saved replay) broadcast.
     *
     * @param string      $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string|null $maxId       Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PostLiveViewerListResponse
     */
    public function getPostLiveViewerList(
        $broadcastId,
        $maxId = null)
    {
        $request = $this->ig->request("live/{$broadcastId}/get_post_live_viewers_list/");
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\PostLiveViewerListResponse());
    }

    /**
     * Get a live broadcast's heartbeat and viewer count.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param bool   $isViewer    Indicates if this request is being ran as a viewer (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastHeartbeatAndViewerCountResponse
     */
    public function getHeartbeatAndViewerCount(
        $broadcastId,
        $isViewer = false)
    {
        $request = $this->ig->request("live/{$broadcastId}/heartbeat_and_get_viewer_count/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken());
        if ($isViewer) {
            $request->addPost('live_with_eligibility', 1);
        } else {
            $request->addPost('offset_to_video_start', 0);
        }

        return $request->getResponse(new Response\BroadcastHeartbeatAndViewerCountResponse());
    }

    /**
     * Get a live broadcast's join request counts.
     *
     * Note: This request **will** return null if there have been no pending
     * join requests have been made. Please have your code check for null.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $lastTotalCount Last join request count (optional).
     * @param int    $lastSeenTs     Last seen timestamp (optional).
     * @param int    $lastFetchTs    Last fetch timestamp (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastJoinRequestCountResponse|null
     */
    public function getJoinRequestCounts(
        $broadcastId,
        $lastTotalCount = 0,
        $lastSeenTs = 0,
        $lastFetchTs = 0)
    {
        try {
            return $this->ig->request("live/{$broadcastId}/get_join_request_counts/")
                ->addParam('last_total_count', $lastTotalCount)
                ->addParam('last_seen_ts', $lastSeenTs)
                ->addParam('last_fetch_ts', $lastFetchTs)
                ->getResponse(new Response\BroadcastJoinRequestCountResponse());
        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
            return null;
        }
    }

    /**
     * Join a live broadcast conference.
     *
     * This method requires a WebRTC implementation by the user. You will need to provide a valid SDP Offer and
     * Instagram will send you the SDP response, cluster, conference name and nonce, so you will be able to stablish
     * a WebRTC conference.
     *
     * @param string $broadcastId  The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $sdpOffer     SDP offer.
     * @param int    $targetHeight (optional) Target height.
     * @param int    $targetWidth  (optional) Target width.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastJoinResponse
     */
    public function join(
        $broadcastId,
        $sdpOffer,
        $targetHeight = 768,
        $targetWidth = 400)
    {
        return $this->ig->request("live/{$broadcastId}/join/")
            ->addPost('sdp_offer', $sdpOffer)
            ->addPost('target_video_height', $targetHeight)
            ->addPost('target_video_width', $targetWidth)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\BroadcastJoinResponse());
    }

    /**
     * Invite a friend to a live broadcast conference.
     *
     * @param string $broadcastId           The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $userId                Numerical UserPK ID.
     * @param string $encodedServerDataInfo Encoded server data info.
     * @param int    $videoOffset           Offset to video start.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function inviteFriend(
        $broadcastId,
        $userId,
        $encodedServerDataInfo,
        $videoOffset)
    {
        return $this->ig->request("live/{$broadcastId}/join/")
            ->addPost('invitees', $userId)
            ->addPost('offset_to_video_start', $videoOffset)
            ->addPost('encoded_server_data_info', $encodedServerDataInfo)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Kick friend from live broadcast conference.
     *
     * @param string $broadcastId           The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $userId                Numerical UserPK ID.
     * @param string $encodedServerDataInfo Encoded server data info.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function kickFriend(
        $broadcastId,
        $userId,
        $encodedServerDataInfo)
    {
        return $this->ig->request("live/{$broadcastId}/kickout/")
            ->addPost('users_to_be_removed', $userId)
            ->addPost('reason', 'remove_guest')
            ->addPost('encoded_server_data_info', $encodedServerDataInfo)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Leave a live broadcast conference.
     *
     * @param string $broadcastId           The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $encodedServerDataInfo Encoded server data info.
     * @param int    $numberOfParticipants  Number of participants.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function leaveConference(
        $broadcastId,
        $encodedServerDataInfo,
        $numberOfParticipants = 0)
    {
        return $this->ig->request("live/{$broadcastId}/kickout/")
            ->addPost('num_participants', $numberOfParticipants)
            ->addPost('reason', 'leave_broadcast')
            ->addPost('encoded_server_data_info', $encodedServerDataInfo)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Confirm live broadcast conference event.
     *
     * @param string $broadcastId           The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $encodedServerDataInfo Encoded server data info.
     * @param string $messageType           Message type. `CONFERENCE_STATE` or `SERVER_MEDIA_UPDATE`.
     * @param int    $curVersion
     * @param string $transactionId
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function confirmEvent(
        $broadcastId,
        $encodedServerDataInfo,
        $messageType,
        $curVersion,
        $transactionId)
    {
        $messageTypes = [
            'CONFERENCE_STATE',
            'SERVER_MEDIA_UPDATE',
        ];

        if (!in_array($messageType, $messageTypes, true)) {
            throw new \InvalidArgumentException('Invalid message type.');
        }

        return $this->ig->request("live/{$broadcastId}/confirm/")
            ->addPost('message_type', $messageType)
            ->addPost('cur_version', $curVersion)
            ->addPost('transaction_id', $transactionId)
            ->addPost('encoded_server_data_info', $encodedServerDataInfo)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('device_id', $this->ig->device_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Show question in a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $questionId  The question ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function showQuestion(
        $broadcastId,
        $questionId)
    {
        return $this->ig->request("live/{$broadcastId}/question/{$questionId}/activate/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Hide question in a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $questionId  The question ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function hideQuestion(
        $broadcastId,
        $questionId)
    {
        return $this->ig->request("live/{$broadcastId}/question/{$questionId}/deactivate/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Asks a question to the host of the broadcast.
     *
     * Note: This function is only used by the viewers of a broadcast.
     *
     * @param string $broadcastId  The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $questionText Your question text.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function question(
        $broadcastId,
        $questionText)
    {
        return $this->ig->request("live/{$broadcastId}/questions/")
            ->setSignedPost(false)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('text', $questionText)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Get all received responses from a story question.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastQuestionsResponse
     */
    public function getQuestions()
    {
        return $this->ig->request('live/get_questions/')
            ->getResponse(new Response\BroadcastQuestionsResponse());
    }

    /**
     * Get all received responses from the current broadcast and a story question.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastQuestionsResponse
     */
    public function getLiveBroadcastQuestions(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/questions/")
            ->addParam('sources', 'story_and_live')
            ->getResponse(new Response\BroadcastQuestionsResponse());
    }

    /**
     * Get live presence of your followers.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PresencesResponse
     */
    public function getLivePresence()
    {
        return $this->ig->request('live/get_live_presence/')
            ->addParam('presence_type', '10min_green_dot')
            ->addParam('min_active_count', 4)
            ->getResponse(new Response\PresencesResponse());
    }

    /**
     * Acknowledges (waves at) a new user after they join.
     *
     * Note: This can only be done once to a user, per stream. Additionally, the user must have joined the stream.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $viewerId    Numerical UserPK ID of the user to wave to.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function wave(
        $broadcastId,
        $viewerId)
    {
        return $this->ig->request("live/{$broadcastId}/wave/")
            ->addPost('viewer_id', $viewerId)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Post a comment to a live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentText Your comment text.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CommentBroadcastResponse
     */
    public function comment(
        $broadcastId,
        $commentText)
    {
        return $this->ig->request("live/{$broadcastId}/comment/")
            ->addPost('user_breadcrumb', Utils::generateUserBreadcrumb(mb_strlen($commentText)))
            ->addPost('live_or_vod', 1)
            ->addPost('idempotence_token', Signatures::generateUUID(true))
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('force_create', false)
            ->addPost('comment_text', $commentText)
            ->addPost('offset_to_video_start', 0)
            ->getResponse(new Response\CommentBroadcastResponse());
    }

    /**
     * Post a comment to a live broadcast via web API
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentText Your comment text.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CommentBroadcastResponse
     */
    public function commentGraph(
        $broadcastId,
        $commentText,
        $username = "")
    {
        $rollout_hash = $this->ig->settings->get('rollout_hash');

        if (empty($rollout_hash)) {
            throw new \InstagramAPI\Exception\InstagramException("Couldn't detect rollout_hash in commentGraph() function for live.");
        }

        $request = $this->ig->request("live/{$broadcastId}/comment/")
            ->setAddDefaultHeaders(false)
            ->setSignedPost(false)
            ->setIsBodyCompressed(false)
            ->addHeader('X-CSRFToken', $this->ig->client->getToken())
            ->addHeader('X-Instagram-AJAX', $rollout_hash)
            ->addHeader('X-IG-App-ID', Constants::IG_WEB_APPLICATION_ID);

        if (!empty($username)) {
            $request->addHeader('Referer', 'https://www.instagram.com/' . $username . '/');
        } else {
            $request->addHeader('Referer', 'https://www.instagram.com/');
        }

        if ($this->ig->getIsAndroid()) {
            $request->addHeader('User-Agent', sprintf('Mozilla/5.0 (Linux; Android %s; Google) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36', $this->ig->device->getAndroidRelease()));
        } else {
            $request->addHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS ' . Constants::IOS_VERSION . ' like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.4 Mobile/15E148 Safari/604.1');
        }

        $request->addPost('comment_text', $commentText);

        return $request->getResponse(new Response\CommentBroadcastResponse());
    }

    /**
     * Pin a comment on live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentId   Target comment ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PinCommentBroadcastResponse
     */
    public function pinComment(
        $broadcastId,
        $commentId)
    {
        return $this->ig->request("live/{$broadcastId}/pin_comment/")
            ->addPost('offset_to_video_start', 0)
            ->addPost('comment_id', $commentId)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\PinCommentBroadcastResponse());
    }

    /**
     * Unpin a comment on live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string $commentId   Pinned comment ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UnpinCommentBroadcastResponse
     */
    public function unpinComment(
        $broadcastId,
        $commentId)
    {
        return $this->ig->request("live/{$broadcastId}/unpin_comment/")
            ->addPost('offset_to_video_start', 0)
            ->addPost('comment_id', $commentId)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\UnpinCommentBroadcastResponse());
    }

    /**
     * Get broadcast comments.
     *
     * @param string $broadcastId       The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $lastCommentTs     Last comments timestamp (optional).
     * @param int    $commentsRequested Number of comments requested (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastCommentsResponse
     */
    public function getComments(
        $broadcastId,
        $lastCommentTs = 0,
        $commentsRequested = 3)
    {
        return $this->ig->request("live/{$broadcastId}/get_comment/")
            ->addParam('last_comment_ts', $lastCommentTs)
//            ->addParam('num_comments_requested', $commentsRequested)
            ->getResponse(new Response\BroadcastCommentsResponse());
    }

    /**
     * Get post-live (saved replay) broadcast comments.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $startingOffset (optional) The time-offset to start at when retrieving the comments.
     * @param string $encodingTag    (optional) TODO: ?.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PostLiveCommentsResponse
     */
    public function getPostLiveComments(
        $broadcastId,
        $startingOffset = 0,
        $encodingTag = 'instagram_dash_remuxed')
    {
        return $this->ig->request("live/{$broadcastId}/get_post_live_comments/")
            ->addParam('starting_offset', $startingOffset)
            ->addParam('encoding_tag', $encodingTag)
            ->getResponse(new Response\PostLiveCommentsResponse());
    }

    /**
     * Enable viewer comments on your live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\EnableDisableLiveCommentsResponse
     */
    public function enableComments(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/unmute_comment/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\EnableDisableLiveCommentsResponse());
    }

    /**
     * Disable viewer comments on your live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\EnableDisableLiveCommentsResponse
     */
    public function disableComments(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/mute_comment/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\EnableDisableLiveCommentsResponse());
    }

    /**
     * Like a broadcast.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $likeCount      Number of likes ("hearts") to send (optional).
     * @param int    $burstLikeCount Number of burst likes ("hearts") to send (optional).
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastLikeResponse
     */
    public function like(
        $broadcastId,
        $likeCount = 1,
        $burstLikeCount = 0)
    {
        if ($likeCount < 1 || $likeCount > 6) {
            throw new \InvalidArgumentException('Like count must be a number from 1 to 6.');
        }

        return $this->ig->request("live/{$broadcastId}/like/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('user_like_count', $likeCount)
            ->addPost('user_like_burst_count', $burstLikeCount)
            ->addPost('offset_to_video_start', 0)
            ->getResponse(new Response\BroadcastLikeResponse());
    }

    /**
     * Get a live broadcast's like count.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $likeTs      Like timestamp.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastLikeCountResponse
     */
    public function getLikeCount(
        $broadcastId,
        $likeTs = 0)
    {
        return $this->ig->request("live/{$broadcastId}/get_like_count/")
            ->addParam('like_ts', $likeTs)
            ->getResponse(new Response\BroadcastLikeCountResponse());
    }

    /**
     * Get post-live (saved replay) broadcast likes.
     *
     * @param string $broadcastId    The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param int    $startingOffset (optional) The time-offset to start at when retrieving the likes.
     * @param string $encodingTag    (optional) TODO: ?.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\PostLiveLikesResponse
     */
    public function getPostLiveLikes(
        $broadcastId,
        $startingOffset = 0,
        $encodingTag = 'instagram_dash_remuxed')
    {
        return $this->ig->request("live/{$broadcastId}/get_post_live_likes/")
            ->addParam('starting_offset', $startingOffset)
            ->addParam('encoding_tag', $encodingTag)
            ->getResponse(new Response\PostLiveLikesResponse());
    }

    /**
     * Create a live broadcast.
     *
     * Read the description of `start()` for proper usage.
     *
     * @param int $previewWidth  (optional) Width.
     * @param int $previewHeight (optional) Height.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CreateLiveResponse
     *
     * @see Live::start()
     * @see Live::end()
     */
    public function create(
        $previewWidth = 1080,
        $previewHeight = 2076,
		$message = "Hey!")
    {
        return $this->ig->request('live/create/')
            ->setSignedPost(false)
			->addPost('user_pay_enabled', false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('preview_height', $previewHeight)
            ->addPost('preview_width', $previewWidth)
			->addPost('broadcast_message', $message)
            ->addPost('broadcast_type', 'RTMP_SWAP_ENABLED')
			->addPost('should_use_rsys_rtc_infra', false)
            ->addPost('internal_only', 0)
			->addPost('visibility', 0)
            ->getResponse(new Response\CreateLiveResponse());
    }

    /**
     * Start a live broadcast.
     *
     * Note that you MUST first call `create()` to get a broadcast-ID and its
     * RTMP upload-URL. Next, simply begin sending your actual video broadcast
     * to the stream-upload URL. And then call `start()` with the broadcast-ID
     * to make the stream available to viewers.
     *
     * Also note that broadcasting to the video stream URL must be done via
     * other software, since it ISN'T (and won't be) handled by this library!
     *
     * Lastly, note that stopping the stream is done either via RTMP signals,
     * which your broadcasting software MUST output properly (FFmpeg DOESN'T do
     * it without special patching!), OR by calling the `end()` function.
     *
     * @param string      $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param string|null $latitude    (optional) Latitude.
     * @param string|null $longitude   (optional) Longitude.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\StartLiveResponse
     *
     * @see Live::create()
     * @see Live::end()
     */
    public function start(
        $broadcastId,
        $latitude = null,
        $longitude = null)
    {
        $response = $this->ig->request("live/{$broadcastId}/start/")
            ->setSignedPost(false)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken());

        if ($latitude !== null && $longitude !== null) {
            $response->addPost('latitude', $latitude)
                ->addPost('longitude', $longitude);
        }

        $response = $response->getResponse(new Response\StartLiveResponse());

        if ($this->ig->isExperimentEnabled('ig_android_live_qa_broadcaster_v1_universe', 'is_enabled')) {
            $this->_getQuestionStatus($broadcastId);
        }

        return $response;
    }

    /**
     * Get question status.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    private function _getQuestionStatus(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/question_status/")
            ->setSignedPost(false)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('allow_question_submission', true)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Acknowledges a copyright warning from Instagram after detected via a heartbeat request.
     *
     * `NOTE:` It is recommended that you view the `liveBroadcast` example
     * to see the proper usage of this function.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function resumeBroadcastAfterContentMatch(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/resume_broadcast_after_content_match/")
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * End a live broadcast.
     *
     * `NOTE:` To end your broadcast, you MUST use the `broadcast_id` value
     * which was assigned to you in the `create()` response.
     *
     * @param string $broadcastId      The broadcast ID in Instagram's internal format (ie "17854587811139572").
     * @param bool   $copyrightWarning True when broadcast is ended via a copyright notice (optional).
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     *
     * @see Live::create()
     * @see Live::start()
     */
    public function end(
        $broadcastId,
        $copyrightWarning = false)
    {
        return $this->ig->request("live/{$broadcastId}/end_broadcast/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('end_after_copyright_warning', $copyrightWarning)
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Add a finished broadcast to your post-live feed (saved replay).
     *
     * The broadcast must have ended before you can call this function.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function addToPostLive(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/add_to_post_live/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Delete a saved post-live broadcast.
     *
     * @param string $broadcastId The broadcast ID in Instagram's internal format (ie "17854587811139572").
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GenericResponse
     */
    public function deletePostLive(
        $broadcastId)
    {
        return $this->ig->request("live/{$broadcastId}/delete_post_live/")
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\GenericResponse());
    }

    /**
     * Get Live broadcasts on timeline.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\BroadcastInfoResponse
     */
    public function getReelsTrayLive()
    {
        return $this->ig->request("live/reels_tray_broadcasts/")
            ->getResponse(new Response\BroadcastInfoResponse());
    }
}
