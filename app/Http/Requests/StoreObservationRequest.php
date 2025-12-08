<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'discipline' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'intentionality' => 'required|in:intentional,convenience',
            'observationType' => 'required|in:met,not-met',
            'location' => 'required|string|max:255',
            'gap' => 'required|string',
            'whyDetails' => 'required|string',
            'consequence' => 'nullable|string',
            'impactfulAction' => 'required|string',
            'peerToPeer' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discipline.required' => 'Please enter the discipline.',
            'company.required' => 'Please enter the company name.',
            'location.required' => 'Please enter the location where the observation took place.',
            'gap.required' => 'Please describe what was observed.',
            'whyDetails.required' => 'Please explain why the behavior or standard was met or not met.',
            'impactfulAction.required' => 'Please describe the action taken.',
        ];
    }
}
