<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Astrotomic\Translatable\Validation\RuleFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Page::orderByDesc('id');

        if ($request->filled('search')) {
            $data->whereTranslation('title', 'like', '%' . $request->search . '%')
                ->orWhereTranslation('description', 'like', '%' . $request->search . '%');
        }

        $data = $data->paginate(10);

        $data->map(function ($page) {
            $page->description = Str::limit(strip_tags($page->description));
            return $page;
        });

        return view('pages.index', compact(
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
        $languages        = config('languages');
        $enabled_locales  = config('pilot.ENABLED_LOCALES');
        $default_language = config('app.locale');
        $languages        = array_intersect_key($languages, array_flip($enabled_locales));

        return view('pages.create', compact(
            'languages',
            'default_language'
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
        config(['translatable.locales' => config('pilot.ENABLED_LOCALES')]);

        $request->request->add([
            'slug' => Str::slug($request->slug),
        ]);

        $request->validate(RuleFactory::make([
            '%title%'       => 'required|string|max:255',
            '%description%' => 'required|string',
            'slug'          => 'required|string|unique:pages',
            'is_active'     => 'required|boolean',
        ]));

        Page::create($request->all());

        return redirect()->route('settings.pages.index')
            ->with('success', __('Created successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        $languages        = config('languages');
        $enabled_locales  = config('pilot.ENABLED_LOCALES');
        $default_language = config('app.locale');
        $languages        = array_intersect_key($languages, array_flip($enabled_locales));

        return view('pages.edit', compact(
            'languages',
            'default_language',
            'page'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        config(['translatable.locales' => config('pilot.ENABLED_LOCALES')]);

        $request->request->add([
            'slug' => Str::slug($request->slug),
        ]);

        $request->validate(RuleFactory::make([
            '%title%'       => 'required|string|max:255',
            '%description%' => 'required|string',
            'slug'          => 'required|string|unique:pages,slug,' . $page->id,
            'is_active'     => 'required|boolean',
        ]));

        $page->update($request->all());

        return redirect()->route('settings.pages.edit', $page)
            ->with('success', __('Updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->deleteTranslations();
        $page->delete();

        return redirect()->route('settings.pages.index')
            ->with('success', __('Deleted successfully'));
    }
}
