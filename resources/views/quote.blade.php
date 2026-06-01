@php
$identifier = $quote->identifier;
$title = "Cotización - $identifier";

function formatInEntriesString(string $csv)
{
    $html = '';
    $stringArray = explode(',', $csv);
    $collection = collect($stringArray);
    $chunked = $collection->chunk($collection->count() > 9 ? 2 : 3);
    $totalChunks = $chunked->count();
    $chunked->each(function($strArr, $i) use (&$html, $totalChunks) {
        $str = $strArr->join(',');
        if ($totalChunks > $i + 1) {
            $str .= ',';
        }
        $html .= "<div>$str</div>";
    });

    return $html;
}
@endphp

<x-formats.pdf :$title format-version="v1">
    <h1 style="text-align: center; font-size: 1rem;">Cotización Técnico Económica</h1>
    <div class="grid grid-cols-2 gap-4" style="align-items: center;">
        <table class="border-lightgrey w-full">
            <thead>
                <tr>
                    <td colspan="2" class="bg-gainsboro text-center">Datos del cliente</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Empresa</td>
                    <td @style(['font-size: 8pt' => strlen($quote->client->name) >= 25])>
                        <b>{{ $quote->client->name }}</b>
                    </td>
                </tr>
                <tr>
                    <td>Dirección</td>
                    <td @style(['font-size: 8pt' => strlen($address) >= 25])>
                        {{ $address }}
                    </td>
                </tr>
                <tr>
                    <td>At'n</td>
                    <td>{{ $contact->name }}</td>
                </tr>
                <tr>
                    <td>Teléfono</td>
                    <td>{{ $contact->phone ?? $contact->cellphone }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $contact->email }}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td>Cotización #</td>
                    <td><b>{{ $identifier }}</b></td>
                </tr>
                <tr>
                    <td>Fecha</td>
                    <td>{{ $quote->created_at->timeZone('America/Mexico_City')->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Vigencia</td>
                    <td>{{ $quote->validity }}</td>
                </tr>
                <tr>
                    <td>Forma de pago</td>
                    <td>{{ $quote->payment_method }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>Estimado(a): {{ $contact->name }}</p>
    <p class="text-justify">
        Por medio de la presente me es grato saludarle, y en base a su solicitud, enviarle por escrito nuestra
        propuesta técnica y económica, para llevar a cabo los servicios de muestreo y análisis de
        {{ $analyzedMatrices }}, los cuales se anexan en la siguiente página.
    </p>
    <p>El lugar de muestreo es en:</p>
    <table class="border-lightgrey w-full">
        <thead>
            <tr>
                <td colspan="2" class="bg-gainsboro text-center">Lugar de muestreo</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Empresa</td>
                <td><b>{{ $samplingSite->name }}</b></td>
            </tr>
            <tr>
                <td>Dirección</td>
                <td>{{ $samplingSite->address }}</td>
            </tr>
            <tr>
                <td>At'n</td>
                <td>{{ $samplingSite->contact_name }}</td>
            </tr>
            <tr>
                <td>Teléfono</td>
                <td>{{ $samplingSite->contact_phone ?? $samplingSite->contact_cellphone }}</td>
            </tr>
        </tbody>
    </table>
    <table class="w-full border-lightgrey mt-2">
        <thead>
            <td class="bg-gainsboro text-center">Objetivo del muestreo</td>
        </thead>
        <tbody>
            <td class="">{{ $quote->objective }}</td>
        </tbody>
    </table>
    <table class="w-full border-lightgrey my-2">
        <thead>
            <tr>
                <td colspan="6" class="bg-gainsboro text-center">Partidas y puntos de muestreo</td>
            </tr>
            <tr>
                <th style="width: 5%;">Partida</th>
                <th style="width: 30%;">Punto de muestreo</th>
                <th style="width: 30%;">Concepto</th>
                <th style="width: 5%">Cantidad</th>
                <th style="width: 15%;">Precio Unitario</th>
                <th style="width: 15%;">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quote->entries as $index => $entry)
                <tr @class([
                    'h-18 max-h-18',
                    'break-before-page' =>
                        ($totalEntries === 3 && $index === 2)
                        || ($index - 3) % 10 === 0
                ])
                >
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td @style(['font-size: 8pt' => strlen($entry->title) >= 25])>
                        {{ $entry->title }}
                    </td>
                    <td @style(['font-size: 8pt' => strlen($entry->concept) >= 65])>
                        {{ $entry->concept }}
                    </td>
                    <td>{{ $entry->quantity }}</td>
                    @php
                        $unitaryCost = $entry->bundle_price + $entry->extras + $entry->offset;
                    @endphp
                    <td>
                        {{ Number::currency($unitaryCost) }}
                    </td>
                    <td>
                        {{ Number::currency($unitaryCost * $entry->quantity) }}
                    </td>
                </tr>
            @endforeach
            @inject('expenses', 'App\Services\Quotes\QuoteExpenseService')
            @if($expenses->count($quote) > 0)
            <tr>
                <td></td>
                <td></td>
                <td>{{ $quote->global_expenses_concept }}</td>
                <td>{{ $quote->global_expenses_quantity }}</td>
                <td>{{ Number::currency($expenses->getUnitarycost($quote)) }}</td>
                <td>{{ Number::currency($expenses->getTotalCost($quote)) }}</td>
            </tr>
            @endif
            <tr class="bg-gainsboro">
                <td colspan="4"></td>
                <td class="text-right">Precio</td>
                <td>
                    <b>{{ Number::currency($quote->gross_cost) }}</b>
                </td>
            </tr>
            @if ($quote->price_adjustment)
                <tr class="bg-gainsboro">
                    <td colspan="4"></td>
                    <td class="text-right">
                        {{ $quote->gross_cost > $quote->subtotal ? 'Descuento' : 'Cargo' }}
                        {{ $quote->price_adjustment_percentage > 0 ? " ({$quote->price_adjustment_percentage}%)" : '' }}
                    </td>
                    <td>
                        <b>{{ Number::currency($quote->price_adjustment) }}</b>
                    </td>
                </tr>
                <tr class="bg-gainsboro">
                    <td colspan="4"></td>
                    <td class="text-right">Subtotal</td>
                    <td>
                        <b>{{ Number::currency($quote->subtotal) }}</b>
                    </td>
                </tr>
            @endif
            <tr class="bg-gainsboro">
                <td colspan="4"></td>
                <td class="text-right">IVA (16%)</td>
                <td>
                    <b>{{ Number::currency($quote->iva) }}</b>
                </td>
            </tr>
            <tr class="bg-gainsboro">
                <td colspan="4"></td>
                <td class="text-right">Total</td>
                <td>
                    <b>{{ Number::currency($quote->net_cost) }}</b>
                </td>
            </tr>
        </tbody>
        <tfoot class="border-b-lightgrey">
            <td colspan="6" style="padding: 0;"></td>
        </tfoot>
    </table>
    <p>

    </p>
    @php
        $counter = 1;
    @endphp
    @foreach ($parameterPages as $index => $page)
        <table
            @class([
                'parameters-table border-lightgrey',
                $loop->first ? 'break-inside-avoid-page' : 'break-before-page'
            ])>
            <thead>
                @if ($index === 0)
                    <tr class="w-full">
                        <td colspan="4">Los parámetros a determinar son los siguientes</td>
                    </tr>
                @endif
                <tr class="w-full">
                    <th style="width: 10%;">Partida</th>
                    <th style="width: 3%;">No.</th>
                    <th style="width: 47%;">Parámetro</th>
                    <th style="width: 40%;">Método</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $inEntries => $params)
                    @foreach ($params as $i => $p)
                        <tr @class(['border-t-lightgrey' => $i === 0])>
                            @if ($i === 0)
                                <td rowspan="{{ count($params) }}" class="text-center">
                                    {!! formatInEntriesString($inEntries) !!}
                                </td>
                            @endif

                            <td>{{ $counter }}</td>
                            <td @style(['font-size: 8pt' => strlen($p['name']) >= 40])>
                                {{ $p['name'] }} <sup>{{ join(',', $p['quote_remarks']->toArray()) }}</sup>
                            </td>
                            <td>{{ $p['methodology'] }}</td>
                        </tr>
                        @php
                            $counter++;
                        @endphp
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach
    @if ($quoteRemarks->count() > 0)
        <table class="mt-2" style="font-size: 8pt;">
            <thead>
                <tr>
                    <th colspan=2>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quoteRemarks as $remark)
                    <tr class="break-inside-avoid-page">
                        <td style="vertical-align: top;"><sup>{{ $remark['code'] }}</sup></td>
                        <td class="text-justify">{{ $remark['description'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <table style="font-size: 8pt;" class="mt-2">
        <thead>
            <tr>
                <th colspan="2">Notas importantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notes as $index => $note)
                <tr class="break-inside-avoid-page">
                    <td style="vertical-align: top;">{{ $index + 1 }}.</td>
                    <td class="text-justify">{{ $note->text }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex mt-2">
        <div style="width: 60%;" class="pr-2">
            <table style="font-size: 8pt;" class="w-full">
                <thead>
                    <tr>
                        <th colspan="2">Terminos comerciales</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commercialTerms as $index => $term)
                        <tr class="break-inside-avoid-page">
                            <td style="vertical-align: top;">{{ $index + 1 }}.</td>
                            <td class="text-justify">{{ $term }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if ($quote->notes)
        <p><b>{{ $quote->notes }}</b></p>
    @endif
    <div class="break-inside-avoid-page">
        <div class="border-lightgrey p-1">
            <table class="border-lightgrey w-full" style="table-layout: fixed;">
                <tbody>
                    <tr>
                        <td colspan="3" class="text-justify">
                            <div class="mb-2">
                                COMPRA Y ACEPTACIÓN DE LAS CARACTERÍSTICAS Y
                                CONDICIONES DESCRITAS PARA EL SERVICIO SOLICITADO.
                                PARA ASI PODER PROGRAMAR SU SERVICIO, FAVOR DE
                                ENVIARLO POR CORREO ELECTRÓNICO Y ADJUNTAR:
                                1. CLIENTES CON CRÉDITO AUTORIZADO; ESTA COTIZACIÓN
                                FIRMADA Y/O ÓRDEN DE COMPRA. 2. CLIENTES QUE NO
                                CUENTAN CON CREDITO; EVIDENCIA DEL PAGO POR EL
                                MONTO TOTAL COTIZADO.
                            </div>
                            <div>
                                <u>
                                    SI LA FIRMA SE ENCUENTRA FUERA DEL RECURADRO,
                                    SE CONSIDERA ÚNICAMENTE ACUSE DE ENTERADO Y RECIBIDO.
                                </u>
                            </div>
                        </td>
                    </tr>
                    <tr style="font-size: 8pt;">
                        <td class="text-center border-lightgrey"><b>ACEPTO COTIZACIÓN</b></td>
                        <td class="text-center border-lightgrey"><b>FECHA DE ACEPTACIÓN</b></td>
                        <td class="text-center border-lightgrey"><b>FECHA DE PROGRAMACIÓN DE PROPUESTA</b></td>
                    </tr>
                    <tr>
                        <td class="h-24 border-lightgrey"></td>
                        <td rowspan="2" class="border-lightgrey"></td>
                        <td rowspan="2" class="border-lightgrey"></td>
                    </tr>
                    <tr style="font-size: 8pt;">
                        <td class="border-lightgrey text-center"><b>NOMBER Y FIRMA</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>
            Sin más por el momento agradecemos la atención a la presente,
            esperando vernos favorecidos para poder servirle, me reitero
            a sus órdenes.
        </p>
        <div class="flex flex-col w-full" style="align-items: center;">
            <div class="text-center mb-2">Atentamente</div>
            <div style="width: 50%; height: 150px;">
                <img src="{{ public_path($signaturePath) }}" alt="firma" style="height: 100%; display: block; margin: auto;">
            </div>
            <hr style="border: none; height: 0.2rem; background: black; width: 50%;">
        </div>
    </div>
</x-formats.pdf>
