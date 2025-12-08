<?php

namespace App\Services;

use App\Models\Observation;
use App\Services\Ai\AiService;

class ObservationService
{
    public function __construct(private AiService $service) {}

    public function new(array $input, int $userId): Observation
    {
        $observation = $this->create($input, $userId);

        defer(
            function () use ($observation, $input) {
                $response = $this->service->generatateNewObservationText($input);
                $observation->addResponse($response);
            }
        );

        return $observation;
    }

    public function update(Observation $observation, string $updateText): Observation
    {
        $response = $this->service->generateUpdatedObservationText($observation->response, $updateText);
        $observation->addResponse($response);

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
