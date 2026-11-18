<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageSectionRequest;
use App\Models\HomepageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', HomepageSection::class);

        return view('admin.content.sections.index', [
            'sections' => HomepageSection::ordered()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', HomepageSection::class);

        return view('admin.content.sections.form', [
            'section' => new HomepageSection,
            'action' => route('admin.homepage-sections.store'),
            'method' => 'POST',
            'heading' => 'Create homepage section',
        ]);
    }

    public function store(HomepageSectionRequest $request): RedirectResponse
    {
        $this->authorize('create', HomepageSection::class);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        HomepageSection::create($data);

        return redirect()->route('admin.homepage-sections.index')->with('status', 'Homepage section created.');
    }

    public function edit(HomepageSection $homepageSection): View
    {
        $this->authorize('update', $homepageSection);

        return view('admin.content.sections.form', [
            'section' => $homepageSection,
            'action' => route('admin.homepage-sections.update', $homepageSection),
            'method' => 'PUT',
            'heading' => 'Edit homepage section',
        ]);
    }

    public function update(HomepageSectionRequest $request, HomepageSection $homepageSection): RedirectResponse
    {
        $this->authorize('update', $homepageSection);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $homepageSection->update($data);

        return redirect()->route('admin.homepage-sections.index')->with('status', 'Homepage section updated.');
    }

    public function destroy(HomepageSection $homepageSection): RedirectResponse
    {
        $this->authorize('delete', $homepageSection);

        $homepageSection->delete();

        return redirect()->route('admin.homepage-sections.index')->with('status', 'Homepage section deleted.');
    }
}
