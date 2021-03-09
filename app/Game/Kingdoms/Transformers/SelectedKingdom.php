<?php

namespace App\Game\Kingdoms\Transformers;

use App\Flare\Models\GameUnit;
use League\Fractal\TransformerAbstract;
use App\Flare\Models\Kingdom;

class SelectedKingdom extends TransformerAbstract {

    /**
     * Gets the response data for the character sheet
     * 
     * @param Character $character
     * @return mixed
     */
    public function transform(Kingdom $kingdom) {
        return [
            'kingdom_name' => $kingdom->name,
            'x_position'   => $kingdom->x_position,
            'y_position'   => $kingdom->y_position,
            'units'        => $this->getUnits($kingdom),
        ];
    }

    /**
     * Fetches the unit information for the kingdom.
     * 
     * @param Kingdom $kingdom
     * @return array
     */
    protected function getUnits(Kingdom $kingdom): array {
        $units = [];
        
        foreach($kingdom->units as $unit) {
            $units[] = [
                'name'                 => $unit->gameUnit->name,
                'amount'               => $unit->amount,
                'attack'               => $unit->gameUnit->attack,
                'defence'              => $unit->gameUnit->defence,
                'can_heal'             => $unit->gameUnit->can_heal,
                'heal_amount'          => $unit->gameUnit->heal_amount,
                'siege_weapon'         => $unit->gameUnit->seige_weapon,
                'travel_time'          => $unit->gameUnit->travel_time,
                'weak_against_unit_id' => GameUnit::find($unit->gameUnit->weak_against_unit_id)->name,
                'primary_attack'       => $unit->gameUnit->primary_attack,
                'fall_back'            => $unit->gameUnit->fall_back,
            ];
        }

        return $units;
    }
}