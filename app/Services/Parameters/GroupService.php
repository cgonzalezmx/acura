<?php

namespace App\Services\Parameters;

use Illuminate\Support\Facades\DB;

class GroupService
{
    public function sync($groupId, array $incomingIds)
    {
        DB::table('parameters')
            ->where('parameter_group_id', '=', $groupId)
            ->whereNotIn('id', $incomingIds)
            ->update(['parameter_group_id' => null]);
        
        DB::table('parameters')
            ->whereIn('id', $incomingIds)
            ->whereNull('parameter_group_id')
            ->update(['parameter_group_id' => $groupId]);
    }
}