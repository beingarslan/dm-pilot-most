<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Autopilot;
use App\Models\Lists;
use Illuminate\Http\Request;

class AutopilotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accounts = Account::all();
        $data     = Autopilot::with('account')->whereIn('account_id', $accounts->pluck('id'));

        if ($request->filled('search')) {
            $data->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('text', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('account')) {
            $data->where('account_id', $request->account);
        }

        if ($request->filled('action')) {
            $data->where('action', $request->action);
        }

        $data = $data->orderByDesc('id')->paginate(10);

        return view('autopilot.index', compact(
            'accounts',
            'data'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accounts = Account::all();
        $lists    = Lists::ofType('messages')->get();

        return view('autopilot.create', compact(
            'accounts',
            'lists'
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
            'action'       => 'required',
            'account_id'   => 'required',
            'name'         => 'required|max:255',
            'message_type' => 'required',
            'lists_id'     => 'required_if:message_type,list',
            'text'         => 'required_if:message_type,text',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date',
        ]);

        $account = Account::find($request->account_id);
        if (is_null($account)) {
            return redirect()->route('autopilot.create')
                ->with('error', __('Account not belongs to you!'));
        }

        Autopilot::create($request->all());

        return redirect()->route('autopilot.index')
            ->with('success', __('Created successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Autopilot  $autopilot
     * @return \Illuminate\Http\Response
     */
    public function edit(Autopilot $autopilot)
    {
        $accounts = Account::all();
        $lists    = Lists::ofType('messages')->get();

        return view('autopilot.edit', compact(
            'autopilot',
            'accounts',
            'lists'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Autopilot  $autopilot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Autopilot $autopilot)
    {
        $request->validate([
            'action'       => 'required',
            'account_id'   => 'required',
            'name'         => 'required|max:255',
            'message_type' => 'required',
            'lists_id'     => 'required_if:message_type,list',
            'text'         => 'required_if:message_type,text',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date',
        ]);

        $autopilot->update($request->all());

        return redirect()->route('autopilot.edit', $autopilot)
            ->with('success', __('Updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Autopilot  $autopilot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Autopilot $autopilot)
    {
        $autopilot->delete();

        return redirect()->route('autopilot.index')
            ->with('success', __('Deleted successfully'));
    }
}
