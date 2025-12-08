<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();

        $baseQuery = Observation::where('user_id', $user->id);

        // 1 query for all the stats
        $stats = (clone $baseQuery)
            ->selectRaw("
        COUNT(*) as total,
        SUM(CASE
            WHEN json_extract(form_data, '$.observationType') = 'met'
            THEN 1 ELSE 0 END
        ) as positive_count,
        SUM(CASE
            WHEN json_extract(form_data, '$.observationType') = 'not-met'
            THEN 1 ELSE 0 END
        ) as delta_count
    ")
            ->first();

        $totalObservations = $stats->total;
        $positiveCount     = $stats->positive_count;
        $deltaCount        = $stats->delta_count;

        // 1 query for the paginated list
        $observations = $baseQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($observation) {
                $formData = is_string($observation->form_data)
                    ? json_decode($observation->form_data, true)
                    : ($observation->form_data ?? []);

                return [
                    'id'               => $observation->id,
                    'title'            => $observation->title,
                    'status'           => $observation->status,
                    'type'             => $formData['observationType'] ?? 'unknown',
                    'created_at'       => $observation->created_at->format('M d, Y'),
                    'created_at_human' => $observation->created_at->diffForHumans(),
                ];
            });
        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalObservations,
                'positive' => $positiveCount,
                'delta' => $deltaCount,
            ],
            'observations' => $observations,
            'credits' => $user->creditBalance(),
        ]);
    }
}
