<?php

namespace App\Services\Formats;

use App\Models\Parameters\Group;
use App\Models\SamplingFormat;
use Illuminate\Support\Collection;

class SamplingFormatAdapter
{
    private const SIZE_OF_PAGE = 24.3;

    public function __construct(
        private ClientEntityAdapter $adapter,
        private SamplingFormatParameterAdapter $parameterAdapter,
        private RowsService $rowService
    )
    {}

    public function process(SamplingFormat $samplingFormat): array
    {
        $quote = $samplingFormat->quote;
        $deliveredByClient = $quote->sample_delivered_by_client;
        $title = $deliveredByClient ? 'Orden de trabajo' : 'Plan de muestreo';
        $title .= ': ' . $samplingFormat->identifier;
        $entry = $samplingFormat->entry;
        $priority = $entry->is_urgent ? 'Urgente' : 'Normal';
        $sampleType = explode(',',$entry->concept);
        $sampleType = $deliveredByClient
            ? $entry->sample_type
            : collect($sampleType)->slice(0, 2)->join(' - ');
        $parameters = $this->parameterAdapter->process($samplingFormat->entry);
        $groups = Group::with(['container', 'preserver'])->get();
        $remarks = $parameters
                    ->pluck('sampling_remarks')
                    ->flatten(1)
                    ->unique('code')
                    ->sortBy('code');
        $grouped = $parameters->groupBy(function($parameter) use($groups) {
            return $groups
                ->where('id', '=', $parameter['parameter_group_id'])
                ->pluck('id')
                ->first() ?? 0;
        })
        ->sortKeys()
        ->map(fn($parameters, $k) => [
            'id' => $k,
            'parameters' => $parameters
        ]);

        $occupiedSpace = [
            'size' => 0,
            'overflowed' => false
        ];

        if ($deliveredByClient) {
            $occupiedSpace = $this->occupiedSpaceWhenDeliveredByClient(
                $entry->title,
                $entry->objective,
                $entry->form_factor,
                $entry->observation
            );
        }
        else {
            $occupiedSpace = $this->occupiedSpace($entry->title, $entry->objective);
        }

        if ($occupiedSpace['overflowed']) {
            $availableHeight = self::SIZE_OF_PAGE - $occupiedSpace['size'];
            $firstPageOffset = floor(($availableHeight - 1) / 0.75);
        }
        else {
            $availableHeight = 13 - $occupiedSpace['size'];
            $firstPageOffset = floor(($availableHeight - 1) / 1.1);
        }

        $pages = $this->chunckParameters($grouped, $firstPageOffset);

        return [
            'identifier' => $samplingFormat->identifier,
            'quote' => $quote,
            'client' => $quote->client,
            'deliveredByClient' => $deliveredByClient,
            'clientDataAsSamplingSite' => $quote->client_data_as_sampling_site,
            'title' => $title,
            'contact' => $this->adapter->contact($quote),
            'samplingSite' => $this->adapter->samplingSite($quote),
            'objective' => $entry->objective,
            'takes' => $entry->takes,
            'priority' => $priority,
            'formFactor' => $entry->form_factor,
            'sampleType' => $sampleType,
            'parameters' => $parameters,
            'points' => $entry->title,
            'remarks' => $remarks,
            'groups' => $groups,
            'parameterPages' => $pages,
            'receptionDate' => $entry->sample_reception_date,
            'sampleTemperature' => $entry->sample_temperature,
            'sampleContainer' => $entry->sample_container_type,
            'observation' => $entry->observation,
            'sampleContainerNumber' => $entry->total_containers,
            'sampleVolume' => $entry->total_volume
        ];
    }

    public function totalLongTextCharacters(array $textBlocks)
    {
        $totalCharacters = 0;

        foreach($textBlocks as $tb) {
            $totalCharacters += mb_strlen($tb);
        }

        return $totalCharacters;
    }

    private function buildTable(Collection $grouped, Collection $ungrouped, int $totalRows)
    {
        $table = collect();
        $groupedArray = $grouped->toArray();

        foreach ($groupedArray as $key => $group) {
            if ((count($group['parameters']) + $table->sum(fn($g) => $g['parameters']->count())) > $totalRows) {
                continue;
            }

            $table->push($grouped->get($key));
            $grouped->forget($key);
        }

        $rowsInTable = $table->sum(fn($g) => $g['parameters']->count());

        if ($rowsInTable < $totalRows) {
            $padding = $totalRows - $rowsInTable + 3;
            $table->push([
                'id' => 0,
                'parameters' => collect($ungrouped['parameters']->splice(0, $padding))
            ]);
        }

        return $table;
    }

    public function chunckParameters(Collection $grouped, int $firstTableSize = 0)
    {
        $ungrouped = collect($grouped->shift());
        $sorted = $grouped->sortBy('parameters');
        $tables = [];
        $firstTableFlag = true;
        $tableSize = ceil(self::SIZE_OF_PAGE / 0.8);

        while ($sorted->isNotEmpty()) {
            $table = $this->buildTable($sorted, $ungrouped, $firstTableFlag ? $firstTableSize : $tableSize);

            if ($table->isNotEmpty()) {
                $tables[] = $table;
            }

            if ($firstTableFlag) {
                $firstTableFlag = false;
            }
        }

        if ($ungrouped['parameters']->isNotEmpty()) {
            $tables[] = collect([
                [
                    'id' => 0,
                    'parameters' => $ungrouped['parameters']
                ]
            ]);
        }

        $tables = collect($tables);

        return $tables->flatten(1);
    }

    private function occupiedSpace(string $points, string $objective): array
    {
        $sizeOfPoints = $this->rowService->totalRowsSizeByText($points, 92, 0.4) + 0.5;
        $sizeOfObjective = $this->rowService->totalRowsSizeByText($objective, 92, 0.4) + 0.5;

        return [
            'size' => $sizeOfPoints + $sizeOfObjective + 1,
            'overflowed' => false
        ];
    }

    private function occupiedSpaceWhenDeliveredByClient(string $points, string $objective, string $formFactor, string $observation): array
    {
        $threshold = 107;
        $pointsLength = mb_strlen($points);
        $formFactorLength = mb_strlen($formFactor);

        $totalCharacters = $pointsLength + $formFactorLength + 2;
        $sizeOfObservation = $this->rowService->totalRowsSizeByText($observation ?? '', 109, 0.3) + 0.2;

        if (($totalCharacters + 52) < $threshold) {
            return [
                'size' => 7.8 + (mb_strlen($observation) > 109 ? ($sizeOfObservation - 0.6) : 0),
                'overflowed' => false
            ];
        }

        $sizeOfObjective = $this->rowService->totalRowsSizeByText($objective, 96, 0.4) + 0.2;
        $sizeOfPoints = $this->rowService->totalRowsSizeByText($points, 56, 0.4) + 0.2;
        $rowSizes = collect([
            $sizeOfObjective,
            0.6,
            $sizeOfPoints,
            1.5,
            0.6,
            0.6,
            1.5,
            1,
            $sizeOfObservation
        ]);

        $occupiedSpace = $rowSizes->sum() + 0.5;
        $overflowed = false;

        if ($occupiedSpace > 14.0) {
            $occupiedSpace = 0.5;
            $overflowed = true;
            while (($rowSizes->sum() + 0.5) > 14) {
                $occupiedSpace += $rowSizes->pop();
            }
        }

        return [
            'size' => $occupiedSpace,
            'overflowed' => $overflowed
        ];
    }
}
