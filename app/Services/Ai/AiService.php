<?php

namespace App\Services\Ai;

use App\Services\Ai\Prompts\ObservationPrompt;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class AiService
{
    public function __construct(private ObservationPrompt $obsPrompt) {}

    public function generatateNewObservationText(array $data): array
    {
        $prompt = $this->obsPrompt->new($data);
        $response = Prism::text()
            ->using(Provider::XAI, 'grok-4-fast-non-reasoning')
            ->withPrompt($prompt)->asText();
        $tokensUsed = $this->handleTokens($response);

        return [
            'content' => $response->text,
            'tokens' => $tokensUsed,
        ];
    }

    public function generateUpdatedObservationText(string $currentText, string $updateText): array
    {

        $prompt = $this->obsPrompt->update($currentText, $updateText);

        $response = Prism::text()
            ->using(Provider::XAI, 'grok-4-fast-non-reasoning')
            ->withPrompt($prompt)->asText();
        $tokensUsed = $this->handleTokens($response);

        return [
            'content' => $response->text,
            'tokens' => $tokensUsed,
        ];
    }

    public function handleTokens($response): int
    {
        $promptTokens = $response->usage->promptTokens;
        $usageTokens = $response->usage->completionTokens;

        $amount = round(($promptTokens + $usageTokens) / 10, -1);

        return $amount;
    }
}
