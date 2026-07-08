<?php

namespace App\Http\Requests\Quotes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Validator;

class StoreQuoteRequest extends FormRequest
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
        $rules = [
            'quote' => 'array|required',
            'quote.objective' => 'required|string',
            'quote.notes' => 'nullable|string',
            'quote.validity' => 'required|string',
            'quote.sample_delivered_by_client' => 'required|boolean',
            'quote.client_data_as_sampling_site' => 'required|boolean',
            'client' => 'required|array',
            'client.client_id' => 'nullable|integer',
            'client.name' => 'required|string',
            'client.industry_sector' => 'nullable|string',
            'client.address' => 'required|string',
            'client.neighborhood' => 'required|string',
            'client.city' => 'required|string',
            'client.state' => 'required|string',
            'client.zip_code' => 'required|string',
            'contact' => 'required|array',
            'contact.client_contact_id' => 'nullable|integer',
            'contact.name' => 'nullable|string',
            'contact.phone' => 'nullable|string',
            'contact.cellphone' => 'nullable|string',
            'contact.email' => 'nullable|string',
            'site' => 'sometimes|array',
            'site.id' => 'nullable|integer',
            'site.client_sampling_site_id' => 'nullable|integer',
            'site.name' => 'nullable|string',
            'site.industry_sector' => 'nullable|string',
            'site.address' => 'nullable|string',
            'site.neighborhood' => 'nullable|string',
            'site.city' => 'nullable|string',
            'site.state' => 'nullable|string',
            'site.zip_code' => 'nullable|string',
            'site.contact' => 'nullable|array',
            'site.contact.name' => 'nullable|string',
            'site.contact.phone' => 'nullable|string',
            'site.contact.email' => 'nullable|string',
            'entries' => 'required|array',
            'entries.*.id' => 'nullable|integer',
            'entries.*.entry_id' => 'required|string|ulid',
            'entries.*.title' => 'required|string',
            'entries.*.concept' => 'required|string',
            'entries.*.is_urgent' => 'required|boolean',
            'entries.*.form_factor' => 'required|string',
            'entries.*.objective' =>'required|string',
            'entries.*.result_time_lapse' => 'required|integer|min:1',
            'entries.*.matrix_id' => 'required|integer',
            'entries.*.bundle_price' => 'required|numeric',
            'entries.*.price_offset' => 'required|numeric',
            'entries.*.price_offset_notes' => 'required_unless:entries.*.price_offset,0',
            'entries.*.sample_type' => 'required_if:quote.sample_delivered_by_client,true|string',
            'entries.*.sample_reception_date' => 'nullable|date',
            'entries.*.sampling_date' => 'nullable|date',
            'entries.*.sample_temperature' => 'nullable|integer',
            'entries.*.sample_container_type' => 'nullable|string',
            'entries.*.total_containers' => 'nullable|integer',
            'entries.*.total_volume' => 'nullable|string',
            'entries.*.observation' => 'nullable|string',
            'entries.*.refrigerated' => 'required_if:quote.sample_delivered_by_client,true|boolean',
            'entries.*.extras' => 'required|numeric',
            'entries.*.takes' => 'required|integer',
            'entries.*.quantity' => 'required|integer|min:1',
            'entries.*.reports' => 'required|array',
            'entries.*.reports.*.id' => 'nullable|integer',
            'entries.*.reports.*.report_id' => 'required|string|ulid',
            'entries.*.reports.*.is_main_report' => 'required|boolean',
            'entries.*.reports.*.structure_expanded_keys' => 'required|array',
            'entries.*.reports.*.structure_selected_keys' => 'required|array',
            'entries.*.reports.*.instance_expanded_keys' => 'nullable|array',
            'entries.*.reports.*.instance_selected_keys' => 'nullable|array',
            'entries.*.reports.*.observation' => 'required|string',
            'entries.*.reports.*.thresholds' => 'required|array|min:1',
            'entries.*.reports.*.thresholds.*.id' => 'nullable|integer|min:1',
            'entries.*.reports.*.thresholds.*.parameter_id' => 'integer|min:1',
            'entries.*.reports.*.thresholds.*.min' => 'nullable|string',
            'entries.*.reports.*.thresholds.*.max' => 'nullable|string',
            'entries.*.reports.*.thresholds.*.custom_boundary' => 'string|in:none,min,max,both',
            'entries.*.included_parameters' => 'required|array|min:1',
            'entries.*.included_parameters.*.parameter_id' => 'required|integer|min:1',
            'entries.*.included_parameters.*.quantity' => 'required|integer',
            'entries.*.included_parameters.*.expected_quantity' => 'required|integer|min:1',
            'entries.*.included_parameters.*.from_system' => 'required|boolean',
            'entries.*.included_parameters.*.from_main_report' => 'required|boolean',
            'costs' => 'required|array',
            'costs.gross_cost' => 'required|numeric|gt:0',
            'costs.net_cost' => 'required|numeric|gt:0',
            'costs.subtotal' => 'required|numeric|gt:0',
            'costs.iva' => 'required|numeric',
            'costs.price_adjustment' => 'sometimes|numeric|gt:0',
            'costs.price_adjustment_percentage' => 'sometimes|numeric|gt:0',
            'costs.price_adjustment_notes' => 'sometimes|string',
            'costs.payment_method' => 'nullable|string',
            'costs.expenses' => 'sometimes|array|min:1',
            'costs.expenses.*.id' => 'nullable|integer|min:1',
            'costs.expenses.*.cost' => 'required_unless:costs.expenses,null|numeric|min:1',
            'costs.expenses.*.quantity' => 'required_unless:costs.expenses,null|integer|min:1',
            'costs.expenses.*.concept' => 'required_unless:costs.expenses,null|string',
            'costs.global_expenses_concept' => 'required_unless:costs.expenses,null|string',
            'costs.global_expenses_quantity' => 'required_unless:costs.expenses,null|integer|min:1'
        ];

        return $rules;
    }

    public function after() {
        return [
            function(Validator $validator) {
                $entries = collect($this->input('entries'));
                $inThresholds = $entries
                    ->pluck('reports')
                    ->flatten(1)
                    ->pluck('thresholds')
                    ->flatten(1)
                    ->pluck('parameter_id');

                $included = $entries
                    ->pluck('included_parameters')
                    ->flatten(1)
                    ->pluck('parameter_id');

                $diff = $included->diff($inThresholds);

                if ($diff->count() > 0) {
                    $validator->errors()->add('test', 'test message');
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'entries.*.title' => 'El título de la partida #:position no debe estar vacío',
            'client.address' => 'Hace falta la dirección',
            'client.name' => 'Hace falta el nombre del cliente o empresa',
            'client.neighborhood' => 'Hace falta la colonia',
            'client.zip_code' => 'Hace falta el código postal',
            'client.city' => 'Hace falta la ciudad',
            'client.state' => 'Hace falta el estado',
            'entries.*.form_factor' => 'Falta forma del punto de muestreo en la partida #:position',
            'entries.*.objective' => 'Falta objetivo en la partida #:position',
            'entries.*.result_time_lapse' => 'No se definió el tiempo de entrega de informes',
            'entries.*.sample_type' => 1,
            'entries.*.refrigerated' => 1,
            'entries.*.quantity' => 1,
            'entries.*.reports.*.selected_keys' => 'Alguno de los reportes no tiene selección en el árbol',
            'entries.*.reports.*.observation' => 'Alguno de los reportes no tiene observación',
            'entries.*.reports.*.thresholds' => 'Alguno de los límites máximos permisibles no esta definido',
            'entries.*.expenses.*.cost' => 'Los viáticos no tienen precio',
            'entries.*.included_parameters' => 'No se incluyeron los parámetros',
            'entries.*.included_parameters.*.quantity' => 'Alguno de los parámetros no tiene cantidad',
            'quote.objective' => 'Se requiere un objetivo para la cotización',
            'costs.gross_cost' => 'El costo bruto no puede ser 0',
            'costs.net_cost' => 'El costo neto no puede ser 0',
            'costs.subtotal' => 'El subtotal no puede ser 0',
        ];
    }
}
