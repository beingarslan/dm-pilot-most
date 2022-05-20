<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Package::paginate(10);

        return view('packages.index', compact(
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
        $permissions = config('pilot.PERMISSIONS');
        asort($permissions);

        return view('packages.create', compact(
            'permissions'
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
            'title'                   => 'required',
            'price'                   => 'required',
            'interval'                => 'required',
            'settings.accounts_count' => 'required|numeric|min:1',
            'settings.messages_count' => 'required|numeric|min:1',
            'settings.storage'        => 'required|numeric|min:1',
        ]);

        if (!$request->filled('is_featured')) {
            $request->request->add([
                'is_featured' => false,
            ]);
        } else {
            $request->request->add([
                'is_featured' => true,
            ]);
        }

        if (!$request->filled('is_hidden')) {
            $request->request->add([
                'is_hidden' => true,
            ]);
        } else {
            $request->request->add([
                'is_hidden' => false,
            ]);
        }

        Package::create($request->all());

        return redirect()->route('settings.packages.index')
            ->with('success', __('Created successfully'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
        $permissions = config('pilot.PERMISSIONS');
        asort($permissions);

        return view('packages.edit', compact(
            'package',
            'permissions'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {
        $request->validate([
            'title'                   => 'required',
            'price'                   => 'required',
            'interval'                => 'required',
            'settings.accounts_count' => 'required|numeric|min:1',
            'settings.messages_count' => 'required|numeric|min:1',
            'settings.storage'        => 'required|numeric|min:1',
            'p'                       => 'array',
        ]);

        if (!$request->filled('is_featured')) {
            $request->request->add([
                'is_featured' => false,
            ]);
        } else {
            $request->request->add([
                'is_featured' => true,
            ]);
        }

        if (!$request->filled('is_hidden')) {
            $request->request->add([
                'is_hidden' => true,
            ]);
        } else {
            $request->request->add([
                'is_hidden' => false,
            ]);
        }

        $package->update($request->all());

        return redirect()->route('settings.packages.edit', $package)
            ->with('success', __('Updated successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
        $package->delete();

        // Should we remove post from the Instagram as well?

        return redirect()->route('settings.packages.index')
            ->with('success', __('Deleted successfully'));
    }
}
