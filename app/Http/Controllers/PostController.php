<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\PublishPost;
use App\Models\Account;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accounts = Account::all();

        $data = Post::with('account')
            ->whereHas('account', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });

        if ($request->filled('type')) {
            $data->where('type', $request->type);
        }

        if ($request->filled('account')) {
            $data->where('account_id', $request->account);
        }

        if ($request->filled('status')) {
            $data->where('status', $request->status);
        }

        if ($request->filled('sort')) {
            if ($request->sort == 'asc') {
                $data->orderBy('id');
            } else {
                $data->orderByDesc('id');
            }
        } else {
            $data->orderByDesc('id');
        }

        $data = $data->paginate(12);

        return view('post.index', compact(
            'accounts',
            'data'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $accounts      = Account::all();
        $used_space    = Helper::bytes_to_human($request->user()->getMedia()->sum('size'));
        $storage_limit = Helper::bytes_to_human($request->user()->package->storage_limit * 1024 * 1024);

        return view('post.create', compact(
            'accounts',
            'used_space',
            'storage_limit'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'account'      => 'required',
            'type'         => 'required|in:post,album,story',
            'scheduled_at' => 'required_if:scheduled,1|date',
        ]);

        if ($request->type == 'album') {
            $request->validate([
                'media' => 'required|array|between:2,10',
            ]);
        } else {
            $request->validate([
                'media' => 'required|array|size:1',
            ]);
        }

        if ($request->filled('scheduled')) {

            Post::create([
                'account_id'   => $request->account,
                'type'         => $request->type,
                'ig'           => [
                    'media'         => $request->media,
                    'location'      => $request->location,
                    'first_comment' => $request->first_comment,
                ],
                'caption'      => $request->caption,
                'status'       => config('pilot.POST_STATUS_SCHEDULED'),
                'scheduled_at' => $request->scheduled_at,
            ]);

            return redirect()->route('post.index')
                ->with('success', __('Your post has been scheduled'));

        } else {

            $post = new Post();

            $post->account_id = $request->account;
            $post->type       = $request->type;
            $post->caption    = $request->caption;
            $post->ig         = [
                'media'         => $request->media,
                'location'      => $request->location,
                'first_comment' => $request->first_comment,
            ];

            if (new PublishPost($post)) {
                return redirect()->route('post.index')
                    ->with('success', __('Post has been published'));
            } else {
                return redirect()->route('post.index')
                    ->with('error', __('Post failed to publish'));
            }

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('post.index')
            ->with('success', __('Deleted successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Post $post)
    {
        if ($post->isPublished) {
            return redirect()->route('post.index');
        }

        return view('post.edit', compact(
            'post'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'scheduled_at' => 'required_if:scheduled,1|date',
        ]);

        if ($request->filled('scheduled')) {

            $post->caption      = $request->caption;
            $post->scheduled_at = $request->scheduled_at;
            $post->status       = config('pilot.POST_STATUS_SCHEDULED');
            $post->ig           = array_merge($post->ig, [
                'first_comment' => $request->first_comment,
                'location'      => $request->location,
            ]);
            $post->save();

            return redirect()->route('post.index')
                ->with('success', __('Your post has been scheduled'));

        } else {

            $post->caption = $request->caption;
            $post->ig      = array_merge($post->ig, [
                'first_comment' => $request->first_comment,
            ]);
            $post->save();

            if (new PublishPost($post)) {
                return redirect()->route('post.index')
                    ->with('success', __('Post has been published'));
            } else {
                return redirect()->route('post.index')
                    ->with('error', __('Post failed to publish'));
            }

        }
    }

}
