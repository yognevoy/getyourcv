<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ResumeStatisticsController extends Controller
{
    private const CHART_DAYS = 60;

    public function index(Resume $resume): Response
    {
        $this->authorize('view', $resume);

        $since = Carbon::today()->subDays(self::CHART_DAYS - 1);

        $viewsByDay = $resume->views()
            ->where('created_at', '>=', $since)
            ->selectRaw('date(created_at) as day, count(*) as views')
            ->groupBy('day')
            ->pluck('views', 'day');

        $dailyViews = collect(range(0, self::CHART_DAYS - 1))
            ->map(function (int $offset) use ($since, $viewsByDay) {
                $date = $since->copy()->addDays($offset)->toDateString();

                return [
                    'date' => $date,
                    'views' => (int) ($viewsByDay[$date] ?? 0),
                ];
            });

        $totalViews = $resume->views()->count();

        $uniqueViews = $resume->views()
            ->distinct('viewer_hash')
            ->count('viewer_hash');

        $viewsLast7Days = $resume->views()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $lastViewedAt = optional(
            $resume->views()->latest()->first()
        )->created_at;

        $recentViews = $resume->views()
            ->latest()
            ->limit(10)
            ->get(['created_at'])
            ->pluck('created_at');

        return Inertia::render('Resume/Statistics', [
            'resume' => $resume->only(['full_name']),
            'totalViews' => $totalViews,
            'uniqueViews' => $uniqueViews,
            'viewsLast7Days' => $viewsLast7Days,
            'lastViewedAt' => $lastViewedAt,
            'recentViews' => $recentViews,
            'dailyViews' => $dailyViews,
        ]);
    }
}
