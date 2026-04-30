<?php
namespace App\Http\Resources\Traits;

trait Blamable {
    protected array $blameEvents = ['created', 'updated', 'deleted'];
    protected bool $useRelationship = false;

    protected function blamableAttributes(): array
    {
        return collect($this->blameEvents)
            ->mapWithKeys(fn($event) => [
                "{$event}_by" => $this->when(
                    isset($this->$event),
                    $this->useRelationship ? fn() => $this->{"{$event}By"} : $this->{"{$event}_by"}
                )
            ])
            ->toArray();
    }

    protected function useRelationship(bool $value = true): static
    {
        $this->useRelationship = $value;
        return $this;
    }
}