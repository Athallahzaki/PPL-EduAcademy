<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'admin_id' => ['nullable', 'integer', 'exists:users,id'],
            'period' => ['nullable', Rule::in(['7_days', '30_days', '3_months', '6_months'])],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $query = Announcement::query()
            ->with('admin:id,name')
            ->latest();

        if (! empty($filters['admin_id'])) {
            $query->where('admin_id', $filters['admin_id']);
        }

        if (! empty($filters['period'])) {
            $now = now();
            $periodStart = match ($filters['period']) {
                '7_days' => $now->copy()->subDays(7),
                '30_days' => $now->copy()->subDays(30),
                '3_months' => $now->copy()->subMonths(3),
                '6_months' => $now->copy()->subMonths(6),
            };

            $query->whereBetween('published_at', [$periodStart, $now]);
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        $announcements = $query->paginate(10)->appends($filters);
        $admins = User::withTrashed()
            ->whereIn('id', Announcement::query()->select('admin_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.announcements.index', compact('announcements', 'admins', 'filters'));
    }

    public function create(): View
    {
        return view('admin.announcements.create');
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        Announcement::create([
            ...$request->validated(),
            'admin_id' => Auth::id(),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(
        UpdateAnnouncementRequest $request,
        Announcement $announcement
    ): RedirectResponse {
        $announcement->update($request->validated());

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
