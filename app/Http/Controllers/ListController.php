<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Lists;
use Illuminate\Http\Request;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type)
    {
        $data = Lists::ofType($type)->withCount('items');

        if ($request->filled('search')) {
            $data->where('name', 'like', '%' . $request->search . '%')
                ->orWhereHas('items', function ($q) use ($request) {
                    $q->where('text', 'like', '%' . $request->search . '%');
                });
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

        $data = $data->paginate(10);

        return view('lists.' . $type . '.index', compact(
            'data',
            'type'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        $accounts = Account::all();

        return view('lists.' . $type . '.create', compact(
            'type',
            'accounts'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $type)
    {
        $request->validate([
            'name'         => 'required|max:255',
            'items.*.text' => 'required',
            'items'        => 'required',
        ]);

        $list = Lists::create([
            'user_id' => $request->user()->id,
            'type'    => $type,
            'name'    => $request->name,
        ]);

        foreach ($request->items as $text) {
            if ($type == 'users') {
                $text = str_replace('@', '', $text);
            }
            $list->items()->create($text);
        }

        return redirect()->route('list.index', $type)
            ->with('success', __('Created successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function multiple(Request $request, $type)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $list = Lists::create([
            'user_id' => $request->user()->id,
            'type'    => $type,
            'name'    => __('Multiple list'),
        ]);

        $usernames = [];
        $tmp       = preg_split('/\r\n|\r|\n/', $request->text);

        if (count($tmp)) {

            $tmp = array_unique($tmp);

            foreach ($tmp as $username) {

                $username = trim(str_replace('@', '', $username));

                if (!empty($username)) {
                    $usernames[] = [
                        'lists_id' => $list->id,
                        'text'     => $username,
                    ];
                }
            }
        }

        if (count($usernames)) {

            $list->items()->insert($usernames);

            return redirect()->route('list.index', $type)
                ->with('success', __('Created successfully'));
        } else {

            $list->delete();

            return redirect()->route('list.index', $type);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($type, Lists $list)
    {
        $accounts = Account::all();

        return view('lists.' . $type . '.edit', compact(
            'type',
            'list',
            'accounts'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, Lists $list)
    {
        $request->validate([
            'name'         => 'required|max:255',
            'items.*.text' => 'required',
            'items'        => 'required',
        ]);

        $list->name = $request->name;
        $list->save();

        $list->items()->delete();

        foreach ($request->items as $text) {
            if ($type == 'users') {
                $text = str_replace('@', '', $text);
            }
            $list->items()->create($text);
        }

        return redirect()->route('list.edit', [$type, $list])
            ->with('success', __('Updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($type, Lists $list)
    {
        $list->items()->delete();
        $list->delete();

        return redirect()->route('list.index', $type)
            ->with('success', __('Deleted successfully'));
    }

}
