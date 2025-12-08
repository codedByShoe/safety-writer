<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreObservationRequest;
use App\Models\Observation;
use App\Services\ObservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ObservationController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('observations/Index');
    }

    public function store(StoreObservationRequest $request, ObservationService $service): RedirectResponse
    {
        $observation = $service->new($request->validated(), $request->user()->id);

        return redirect()->route('observation.show', $observation);
    }

    public function show(Observation $observation): InertiaResponse
    {
        return Inertia::render('observations/Show', [
            'id' => $observation->id,
            'title' => $observation->title,
            'status' => $observation->status,
            'formData' => $observation->form_data,
            'response' => $observation->response,
        ]);
    }

    public function update(Request $request, Observation $observation, ObservationService $service): RedirectResponse
    {
        // Handle finalize action
        if ($request->input('action') === 'finalize') {
            $observation->update(['status' => 'finalized']);

            return redirect()->route('observation.show', ['observation' => $observation])
                ->with('success', 'Observation finalized successfully.');
        }

        // Handle refinement request
        if ($request->has('refinement_request')) {
            $refinementRequest = $request->input('refinement_request');
            try {
                $observation = $service->update($observation, $refinementRequest);

                return redirect()->route('observation.show', ['observation' => $observation])
                    ->with('success', 'Observation updated successfully.');
                // TODO: add proper exception handling
            } catch (\Exception $e) {
                return redirect()->route('observation.show', ['observation' => $observation])
                    ->with('error', 'Failed to update observation. Please try again.');
            }
        }

        return redirect()->route('observation.show', ['observation' => $observation]);
    }
}
