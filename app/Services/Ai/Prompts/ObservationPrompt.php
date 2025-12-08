<?php

namespace App\Services\Ai\Prompts;

class ObservationPrompt
{
    public function new(array $data): string
    {

        $observationType = $data['observationType'] === 'met' ? 'Positive (Met)' : 'Delta (Not Met)';
        $intentionality = $data['intentionality'] === 'intentional'
        ? 'Yes - Intentionally observed'
        : 'Convenience observation';

        $consequence = $data['consequence'] ?? 'Positive observation no consequence noted.';

        $prompt = <<<PROMPT
            You are a safety observation assistant.
            Format the following safety observation data into a clear,
            professional observation report based on these guidelines and examples.\n
            Observation Detail for Not Met (document in SafetyNet “Comments” section)
            1) Intentionality: Indicate whether the observation was an intentional or a convenience
            observation\n
            2) Gap: “What” behavior or standard was “Met” or “Not Met?” Include what activity was
            being observed.\n
            3) Consequence: Document the “Consequence” and discuss with the individuals\n
            4) Why: Ask “Why” the behavior or standard was “Met” or “Not Met.”\n
            a. You must know the “Why” to be able to fully address the gap\n
            b. Document the “Why” for standards/behaviors\n
            5) Impactful Action: “How” was the “Met” or “Not Met” condition addressed? Provide\n
            feedback to reinforce good performance or correct performance gaps. Reference Impactful
            Intervention Tool Kit (located on Farley Homepage) for action taken in response to
            positives or deltas in CORE 4, Industrial Safety/PPE, PU&A, or Peer-to-Peer Coaching.\n
            6) Peer to Peer: If possible, did the workers either 1) demonstrate peer coaching or 2) did the
            observer intervene to discuss a missed opportunity for peer coaching?\n
            Observation Example:
            Intentionality: Yes- Intentionally observed PU&A regarding NMP-MA-019, “Bolting and Torque Guidelines”
            Gap: During torquing activity related to pump reassembly, I observed that the correct torque value per
            procedure NMP-MA-019, “Bolting and Torque Guidelines” was not being used.
            Consequence:Using the wrong torque on bolts could cause the bolt to deform or be unable to provide as much
            clamping force as needed resulting in poor equipment performance, including leaks.
            Why: When asked why the bolts were being torqued to a different torque value than specified in the procedure,
            the individual responded that the torque value was the same used on the other same size bolts on this job.
            Further discussion revealed that the bolts were the same size but not the same grade, which requires a different
            torque value. The individual had not identified the difference and thought that all the bolts were the same
            grade.
            Impactful Action: The individual reviewed the torquing procedure NMP-MA-019 requirements and differences
            for grades and materials of bolts and discussed with supervisor.
            Coaching provided on lack of attention to detail,
            need to review the materials and bolting requirements during Task Preview and to use a peer check on verifying
            the proper torque value. CR (Condition Report) written for MNT procedure revision to show difference in bolting
            material.
            Peer to Peer: Peer did not provide Peer to Peer coaching or adequate peer check during activity per
            NMP-GM005-002.
            Peer was coached to the standards\n\n";

            Format the response with these sections:\n
            **Intentionality:** intentionality type\n
            **Gap:** gap description if any\n

            **Why:** why explanation\n
            **Impactful Action:** action taken. There should always be an action such as coaching or positive feedback\n
            **Peer to Peer:** peer coaching details if any provided. If not say no peer to peer coaching was observed\n\n

            Observation Data:\n
            - Discipline: {$data['discipline']}\n
            - Company: {$data['company']}\n
            - Location: {$data['location']}\n
            - Type: {$observationType}\n
            - Intentionality: {$intentionality}\n
            - Gap: {$data['gap']}\n
            - Why: {$data['whyDetails']}\n

            **Consequence:** {$consequence}\n
            - Impactful Action: {$data['impactfulAction']}\n
            - Peer to Peer: {$data['peerToPeer']}\n\n

            Write the observation in 4–8 sentences using professional language.
            Do not include headers or preamble. Start directly with the observation.
            Maintain the markdown formatting for section headers.

            PROMPT;

        return $prompt;
    }

    public function update(string $observationResponse, string $updateRequest): string
    {

        $prompt = <<<PROMPT
            You are a safety observation assistant.
            The user has requested changes to their observation.\n\n
            Current Observation:\n{$observationResponse}\n\n
            Requested Changes:\n{$updateRequest}\n\n
            Please update the observation based on the requested changes while
            maintaining the professional safety observation
            format with proper sections
            (Intentionality, Gap, Why, Consequence (if applicable), Impactful Action, Peer to Peer).
            Keep the markdown formatting with **bold** headers.';
            PROMPT;

        return $prompt;
    }
}
