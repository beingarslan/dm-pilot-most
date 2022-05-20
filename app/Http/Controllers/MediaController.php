<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\Models\Media;

class MediaController extends Controller
{

    public function index(Request $request)
    {
        $user          = $request->user();
        $used_space    = Helper::bytes_to_human($user->getMedia()->sum('size'));
        $storage_limit = Helper::bytes_to_human($user->package->storage_limit * 1024 * 1024);

        return view('media.index', compact(
            'user',
            'used_space',
            'storage_limit'
        ));
    }

    public function files(Request $request)
    {
        $allMedia = $request->user()->getMedia();

        return response()->json($allMedia->map(function ($value, $key) {

            $value->thumb = $value->getFullUrl('thumb');

            if (in_array($value->mime_type, [
                'video/x-flv',
                'video/mp4',
                'video/3gpp',
                'video/quicktime',
                'video/x-msvideo',
                'video/x-ms-wmv',
            ])) {
                $value->original = $value->getFullUrl('preview');
            } else {
                $value->original = $value->getFullUrl();
            }

            return $value->only([
                'id',
                'file_name',
                'mime_type',
                'size',
                'thumb',
                'original',
            ]);
        })->sortByDesc('id')->values());
    }

    public function upload(Request $request)
    {
        $validMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/x-flv',
            'video/mp4',
            'video/3gpp',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
        ];

        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimetypes:' . join(',', $validMimeTypes),
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => __('The files must be a file of type: ') . join(', ', $validMimeTypes),
            ]);

        } else {

            try {

                $used_space    = $request->user()->getMedia()->sum('size');
                $storage_limit = $request->user()->package->storage_limit * 1024 * 1024;

                if ($used_space <= $storage_limit) {
                    $request->user()->addAllMediaFromRequest()->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Exceed storage limit',
                    ]);
                }

            } catch (\Exception $e) {

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {

            if ($request->filled('id')) {
                $request->user()->media()->whereIn('id', $request->id)->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No media ID specified',
                ]);
            }

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
        ]);

    }

    public function clear(Request $request)
    {
        try {

            $request->user()->media()->delete();

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
        ]);

    }
}
