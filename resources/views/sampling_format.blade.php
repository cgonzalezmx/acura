<x-formats.pdf :$title format-version="v1">
    <h1 class="text-center" style="font-size: 1rem;">{{ $title }}</h1>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <x-pdf.display-table
            title="Datos del cliente"
            :rows="[
                [
                    'cells' => [
                        'Empresa' ,
                        [
                            'class' => ['text-sm' => strlen($client->name) >= 25],
                            'style' => 'font-weight: 900',
                            'text' => $client->name
                        ]
                    ]
                ],
                [
                    'cells' => [
                        'Dirección',
                        [
                            'class' => ['text-sm' => strlen($client->address) >= 25],
                            'text' => $client->address
                        ]
                    ]
                ],
                ['cells' => ['At&#39;n', $contact->name]],
                ['cells' => ['Teléfono', $contact->phone]],
                ['cells' => ['Email', $contact->email]]
            ]"/>
        <x-pdf.display-table
            title="Lugar de muestreo"
            :rows="[
                [
                    'cells' => [
                        'Empresa' ,
                        [
                            'class' => ['text-sm' => strlen($samplingSite->name) >= 25],
                            'style' => 'font-weight: 900',
                            'text' => $samplingSite->name
                        ]
                    ]
                ],
                [
                    'cells' => [
                        'Dirección',
                        [
                            'class' => ['text-sm' => strlen($samplingSite->address) >= 25],
                            'text' => $samplingSite->address
                        ]
                    ]
                ],
                ['cells' => ['At&#39;n', $samplingSite->contact_name]],
                ['cells' => ['Teléfono', $samplingSite->contact_phone]],
                ['cells' => ['Email', $samplingSite->contact_email]]
            ]"/>
    </div>
    <table class="border-lightgrey w-full mb-4">
        <thead class="bg-gainsboro">
            <tr>
                <td rowspan="2" class="border-r-white text-center" style="width: 33.33%;">Clave muestra</td>
                <td colspan="4" class="border-b-white text-center">Muestreo</td>
            </tr>
            <tr>
                <td colspan="2" class="border-r-white text-center" style="width: 33.33%;">Fecha</td>
                <td colspan="2" class="text-center" style="width: 33.33%;">Horario</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border-lightgrey h-24"></td>
                <td colspan="2" class="border-lightgrey"></td>
                <td colspan="2" class="border-lightgrey"></td>
            </tr>
        </tbody>
    </table>
    @if (!$deliveredByClient)
        <div class="flex border-lightgrey mb-4">
            <div class="text-center bg-gainsboro p-1 flex items-center">
                <span>Puntos de muestreo</span>
            </div>
            <div class="flex items-center p-1">{{ $points }}</div>
        </div>
    @endif
    <div class="flex border-lightgrey mb-4">
        <div class="text-center bg-gainsboro p-1 flex items-center">Objetivo del muestreo</div>
        <div class="flex items-center p-1">{{ $objective }}</div>
    </div>
    @if ($deliveredByClient)
        <table class="w-full border-lightgrey mb-4">
            <tbody>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Tipo de muestra</td>
                    <td colspan="3" class="border-lightgrey">{{ $sampleType }}</td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Nombre del punto de muestreo</td>
                    <td class="border-lightgrey">{{ $points }}</td>
                    <td class="bg-gainsboro border-lightgrey">Forma punto de muestreo</td>
                    <td class="border-lightgrey">{{ $formFactor }}</td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Fecha de recepción en laboratorio</td>
                    <td class="border-lightgrey">{{ $receptionDate->format('d/m/Y') }}</td>
                    <td class="bg-gainsboro border-lightgrey">Horario de recepción en laboratorio</td>
                    <td class="border-lightgrey">{{ $receptionDate->format('H:i') }}</td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Temperatura</td>
                    <td colspan="3" class="border-lightgrey">{{ $sampleTemperature }}</td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td colspan="4" class="bg-gainsboro border-lightgrey">Almacenamiento</td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Tipo de recipiente</td>
                    <td>{{ $sampleContainer }}</td>
                    <td class="bg-gainsboro border-lightgrey">Bien identificadas y refrigeradas</td>
                    <td class="border-lightgrey"></td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Total de recipientes</td>
                    <td class="border-lightgrey">{{ $sampleContainerNumber }}</td>
                    <td class="bg-gainsboro border-lightgrey">Volumen total de muestra</td>
                    <td class="border-lightgrey">{{ $sampleVolume }}</td>
                </tr>
                <tr class="break-inside-avoid-page">
                    <td class="bg-gainsboro border-lightgrey">Observaciones</td>
                    <td class="border-lightgrey text-sm" colspan="3">{{ $observation }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <table class="w-full border-lightgrey mb-4">
            <tbody>
                <tr>
                    <td class="bg-gainsboro border-lightgrey border-b-white">Tipo de muestreo</td>
                    <td class="border-lightgrey">{{ $takes > 1 ? 'Compuesta' : 'Simple' }}</td>
                    @if ($takes > 1)
                        <td class="border-lightgrey bg-gainsboro">No. Tomas</td>
                        <td class="border-lightgrey">{{ $takes }}</td>
                    @endif
                    <td class="bg-gainsboro border-lightgrey">Prioridad</td>
                    <td class="border-lightgrey">{{ $priority }}</td>
                    <td class="bg-gainsboro border-lightgrey border-b-white">Medición de flujo</td>
                    <td class="border-lightgrey">Si</td>
                    <td class="border-lightgrey">NO</td>
                </tr>
                <tr>
                    <td class="bg-gainsboro border-lightgrey">Tipo de muestra</td>
                    <td colspan="{{ 3 + ($takes > 1 ? 2 : 0) }}" class="border-lightgrey">{{ $sampleType }}</td>
                    <td class="bg-gainsboro border-lightgrey">Forma punto de muestreo</td>
                    <td colspan="2" class="border-lightgrey">{{ $formFactor }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <table
        @class([
            'border-r-lightgrey mb-4 w-full',
        ])
        class="border-r-lightgrey mb-4"
        style="border-collapse: separate; border-spacing: 0">
        <thead class="bg-gainsboro">
            <tr class="bg-gainsboro">
                <th rowspan="2" class="">Parámetro</th>
                <th rowspan="2" class="border-l-white">#</th>
                <th rowspan="2" class="border-l-white">Envase</th>
                <th rowspan="2" class="border-l-white">Vólumen mínimo</th>
                <th rowspan="2" class="border-l-white">Preservador</th>
                <th rowspan="2" class="border-l-white">Obs.</th>
                <th colspan="2" class="border-l-white border-b-white">Revisión de muestras</th>
            </tr>
            <tr>
                <th class="w-16 border-x-white">Entrega</th>
                <th class="w-16">Recepción</th>
            </tr>
        </thead>
        @foreach ($parameterPages as $block)
            @php
                $groupSize = count($block['parameters']);
                $group = $groups->where('id', '=', $block['id'])->first();
            @endphp
            @if ($block['id'] === 0)
                <tbody style="font-size: 8pt;">
                    @foreach ($block['parameters'] as $parameter)
                        <tr style="break-inside: avoid;">
                            <td class="border-b-lightgrey border-l-lightgrey">{{ $parameter['name'] }}</td>
                            <td class="border-b-lightgrey border-l-lightgrey">x{{ $parameter['quantity'] }}</td>
                            <td class="border-b-lightgrey border-l-lightgrey">{{ $parameter['sample_container'] }}</td>
                            <td class="border-b-lightgrey border-l-lightgrey">{!! $parameter['group_volume'] !!}</td>
                            <td class="border-b-lightgrey border-l-lightgrey">{{ $parameter['sample_preserver'] }}</td>
                            <td class="border-b-lightgrey border-l-lightgrey">{{ $parameter['sampling_remarks']->pluck('code')->join(',') }}</td>
                            <td class="border-b-lightgrey border-l-lightgrey"></td>
                            <td class="border-b-lightgrey border-l-lightgrey"></td>
                        </tr>
                    @endforeach
                </tbody>
            @else
                <tbody style="font-size: 8pt">
                    @foreach ($block['parameters'] as $parameter)
                        <tr style="break-inside: avoid;">
                            <td class="border-b-lightgrey border-l-lightgrey">{{ $parameter['name'] }}</td>
                            @if($loop->first)
                                <td rowspan="{{ $groupSize }}" class="border-b-lightgrey border-l-lightgrey" style="break-inside: avoid;">x{{ $parameter['quantity'] }}</td>
                                <td rowspan="{{ $groupSize }}" class="border-b-lightgrey border-l-lightgrey" style="break-inside: avoid;">{{ $group->container->name }}</td>
                                <td rowspan="{{ $groupSize }}" class="border-b-lightgrey border-l-lightgrey" style="break-inside: avoid;">{{ $group->required_sample_volume }}</td>
                                <td rowspan="{{ $groupSize }}" class="border-b-lightgrey border-l-lightgrey" style="break-inside: avoid;">{{ $group->preserver->name }}</td>
                            @endif
                            <td class="border-b-lightgrey border-l-lightgrey font-mono">{{ $parameter['sampling_remarks']->pluck('code')->join(',') }}</td>
                            <td class="border-b-lightgrey border-l-lightgrey"></td>
                            <td class="border-b-lightgrey border-l-lightgrey"></td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        @endforeach
    </table>

    <div class="flex gap-2 border-lightgrey p-2 mb-4 break-after-avoid-page">
        <div @style(['width: 50%' => count($remarks) > 0])>
            <div class="bg-gainsboro text-center p-1">Observaciones especiales</div>
            <div class="text-sm">
                <ul class="list">
                    <li>
                        El supervisor debe de firmar este plan de muestreo solo en
                        el caso de que el cliente requiera modificaciones del mismo.
                    </li>
                    <li>
                        Llevar equipo de seguridad completo, guantes, bata, tapones
                        auditivos y zapato de seguridad.
                    </li>
                    <li>
                        Llevar hoja de pago de IMSS vigente.
                    </li>
                    <li>
                        Cuando el cliente sea nuevo, hacer levantamiento del punto
                        de muestreo (Croquis).
                    </li>
                    <li>
                        Para registros llevar barreta y faja.
                    </li>
                    <li>
                        Mantener las muestras en refrigeración y/o con hielos a una
                        temperatura de 4°C.
                    </li>
                </ul>
            </div>
        </div>
        @if(count($remarks))
            <div style="width: 50%;">
                <div class="bg-gainsboro text-center p-1">Observaciones</div>
                <div class="grid gap-1 text-sm px-2" style="grid-template-columns: auto 1fr;">
                    @foreach ($remarks as $remark)
                        <div style="font-family: monospace;">
                            *({{ $remark['code'] }})
                        </div>
                        <div>
                            {{ $remark['description'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="break-inside-avoid-page">
        <div class="border-lightgrey bg-gainsboro text-center p-2 mb-4">
            Cadena de custioda
        </div>
        <div class="grid grid-cols-3 border-lightgrey">
            @for ($i = 0; $i < 3; $i++)
                <div class="grid" style="grid-template-columns: auto 1fr;">
                    <div @class(['bg-gainsboro border-b-white py-4 text-center'])>Entrega</div>
                    <div class="border-b-lightgrey"></div>
                    <div class="bg-gainsboro border-b-white py-4 text-center">Recibe</div>
                    <div class="border-b-lightgrey"></div>
                    <div class="bg-gainsboro py-4 text-center">
                        <div class="mx-2">Fecha/Hora</div>
                    </div>
                    <div class=""></div>
                </div>
            @endfor
        </div>
    </div>
</x-formats.pdf>
