<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Rss;
use Illuminate\Http\Request;

class RssController extends Controller
{
    public function setup(Request $request)
    {
        $accounts = Account::all();

        return view('rss.setup', compact(
            'accounts'
        ));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Account $account)
    {
        $data = Rss::withCount('items')
            ->where('account_id', $account->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('rss.index', compact(
            'account',
            'data'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Account $account)
    {
        return view('rss.create', compact(
            'account'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Account $account)
    {
        $request->validate([
            'name'      => 'required',
            'url'       => 'required|url',
            'template'  => 'required',
            'is_active' => 'sometimes|boolean',
        ]);

        if (!$request->filled('is_active')) {
            $request->request->add([
                'is_active' => false,
            ]);
        } else {
            $request->request->add([
                'is_active' => true,
            ]);
        }

        $request->request->add([
            'user_id' => $request->user()->id,
        ]);

        $account->rss()->create($request->all());

        return redirect()->route('rss.index', $account)
            ->with('success', __('Created successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rss  $rss
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account, Rss $rss)
    {
        return view('rss.edit', compact(
            'account',
            'rss'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rss  $rss
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, Rss $rss)
    {
        $request->validate([
            'name'      => 'required',
            'url'       => 'required|url',
            'template'  => 'required',
            'is_active' => 'sometimes|boolean',
        ]);

        if (!$request->filled('is_active')) {
            $request->request->add([
                'is_active' => false,
            ]);
        } else {
            $request->request->add([
                'is_active' => true,
            ]);
        }

        $rss->update($request->all());

        return redirect()->route('rss.edit', [$account, $rss])
            ->with('success', __('Updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rss  $rss
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account, Rss $rss)
    {
        $rss->items()->delete();
        $rss->delete();

        return redirect()->route('rss.index', $account)
            ->with('success', __('Deleted successfully'));
    }
}
