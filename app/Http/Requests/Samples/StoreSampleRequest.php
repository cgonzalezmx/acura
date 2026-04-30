<?php

namespace App\Http\Requests\Samples;

use Illuminate\Foundation\Http\FormRequest;

class StoreSampleRequest extends FormRequest
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
            'sample' => 'required|array',
            'sample.id' => 'nullable|integer',
            'sample.sampling_point' => 'required|string',
            'sample.sample_temperature' => 'required|integer',
            'sample.total_containers' => 'required|integer',
            'sample.refrigerator' => 'required|string',
            'sample.reception_date' => 'required|date',
            'sample.sampled_by' => 'required|integer',
            'sample.sampling_format_id' => 'required|integer',
            'sample.observation' =>'nullable|string',
            'takes' => 'required|array',
            'takes.*.id' => 'nullable|integer',
            'takes.*.timestamp' => 'required|date',
            'takes.*.color' => 'required|string',
            'takes.*.appearance' => 'required|string',
            'takes.*.odour' => 'required|string'
        ];
    }
}
