<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BlamableResource extends JsonResource
{
    protected $useUserModel = false;

    protected function blamableAttributes(): array
    {
        $blame = function(string $attr) {
            return $this->when($this->$attr, $this->useUserModel ? $this->{"{$attr}By"} : $this->{"{$attr}_by"});
        };

        return [
            'created_by' => $blame('created'),
            'updated_by' => $blame('updated'),
            'deleted_by' => $blame('deleted')
        ];
    }
}
