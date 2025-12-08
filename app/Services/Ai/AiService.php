<?php

namespace App\Services\Ai;

use App\Services\Ai\Prompts\ObservationPrompt;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class AiService
{
    public function __construct(private ObservationPrompt $obsPrompt) {}

    protected function call(
        string $prompt,
        Provider $provider = Provider::XAI,
        string $model = 'grok-4-fast-non-reasoning'
    ) {
        return Prism::text()
            ->using($provider, $model)
            ->withPrompt($prompt)
            ->withMaxTokens(1000)
            ->withClientOptions([
                'timeout' => 120,
            ]);
    }

    public function generatateNewObservationText(array $data): string
    {
        $prompt = $this->obsPrompt->new($data);

        return $this->call($prompt)->asText()->text;
    }

    public function generateUpdatedObservationText(string $currentText, string $updateText): string
    {

        $prompt = $this->obsPrompt->update($currentText, $updateText);

        return $this->call($prompt)->asText()->text;
    }
}
