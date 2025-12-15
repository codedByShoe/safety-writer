<?php

namespace App\Services;

use App\Models\Observation;
use App\Models\User;
use App\Services\Ai\AiService;
use Illuminate\Support\Facades\Log;

class ObservationService
{
    public function __construct(private AiService $service) {}

    public function new(array $input, User $user): Observation
    {
        $observation = $this->create($input, $user->id);

        defer(function () use ($observation, $input, $user) {
            try {
                $response = $this->service->generatateNewObservationText($input);
                $user->deductCredits($response['tokens'], ['observation_id' => $observation->id]);
                $observation->addResponse($response['content']);
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Observation generation failed', [
                    'observation_id' => $observation->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $observation->delete();
            }
        });

        return $observation;
    }

    public function update(Observation $observation, string $updateText, User $user): Observation
    {
        $response = $this->service->generateUpdatedObservationText($observation->response, $updateText);
        $user->deductCredits($response['tokens'], ['observation_id' => $observation->id]);
        $observation->addResponse($response['content']);

        return $observation;
    }

    private function create(array $input, int $userId): Observation
    {
        $title = $input['discipline'].' - '.$input['location'].' - '.now()->format('M d, Y');

        return Observation::create([
            'title' => $title,
            'form_data' => $input,
            'response' => null,
            'status' => 'draft',
            'user_id' => $userId,
        ]);
    }
}
