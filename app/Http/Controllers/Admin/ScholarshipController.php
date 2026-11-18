<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ScholarshipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScholarshipRequest;
use App\Http\Requests\Admin\UpdateScholarshipRequest;
use App\Models\Scholarship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Scholarship::class);

        $query = Scholarship::query()->with('admin:id,name');

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('status')) {
            $query->withStatus($request->input('status'));
        }

        $scholarships = $query->latest('created_at')->paginate(10)->withQueryString();

        // Summary metrics
        $totalCount = Scholarship::count();
        $publishedCount = Scholarship::where('status', ScholarshipStatus::Published->value)->count();
        $totalQuota = Scholarship::where('status', ScholarshipStatus::Published->value)->sum('quota');
        $closedCount = Scholarship::where('status', ScholarshipStatus::Closed->value)->count();

        return view('admin.scholarships.index', compact(
            'scholarships',
            'totalCount',
            'publishedCount',
            'totalQuota',
            'closedCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Scholarship::class);

        return view('admin.scholarships.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScholarshipRequest $request): RedirectResponse
    {
        Gate::authorize('create', Scholarship::class);

        $scholarship = Scholarship::create([
            ...$request->validated(),
            'admin_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.scholarships.show', $scholarship)
            ->with('success', 'Info beasiswa berhasil dipublikasikan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Scholarship $scholarship): View
    {
        Gate::authorize('view', $scholarship);

        $scholarship->load('admin:id,name,email');

        return view('admin.scholarships.show', compact('scholarship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scholarship $scholarship): View
    {
        Gate::authorize('update', $scholarship);

        return view('admin.scholarships.edit', compact('scholarship'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScholarshipRequest $request, Scholarship $scholarship): RedirectResponse
    {
        Gate::authorize('update', $scholarship);

        $scholarship->update($request->validated());

        return redirect()->route('admin.scholarships.show', $scholarship)
            ->with('success', 'Info beasiswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scholarship $scholarship): RedirectResponse
    {
        Gate::authorize('delete', $scholarship);

        $scholarship->delete();

        return redirect()->route('admin.scholarships.index')
            ->with('success', 'Info beasiswa berhasil dihapus.');
    }

    /**
     * Restore the soft-deleted resource.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $scholarship = Scholarship::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $scholarship);

        $scholarship->restore();

        return redirect()->route('admin.scholarships.index')
            ->with('success', 'Info beasiswa berhasil dipulihkan.');
    }
}
