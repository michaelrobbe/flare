<?php

namespace Tests\Unit\Game\Kingdoms\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Flare\Models\Building;
use App\Flare\Models\User;
use App\Game\Kingdoms\Jobs\UpgradeBuilding;
use Tests\Setup\Character\CharacterFactory;
use Tests\TestCase;
use Tests\Traits\CreateGameBuilding;
use Tests\Traits\CreateKingdom;

class UpgradeBuildingTest extends TestCase
{
    use RefreshDatabase, CreateKingdom, CreateGameBuilding;

    public function testJobReturnsEarlyWithNoQueue()
    {
        $kingdom = $this->createKingdom([
            'character_id'       => (new CharacterFactory)->createBaseCharacter()->givePlayerLocation()->getCharacter()->id,
            'game_map_id'        => 1,
        ]);

        $kingdom->buildings()->create([
            'game_building_id'   => $this->createGameBuilding()->id,
            'kingdoms_id'        => $kingdom->id,
            'level'              => 1,
            'current_defence'    => 300,
            'current_durability' => 300,
            'max_defence'        => 300,
            'max_durability'     => 300,
        ]);

        UpgradeBuilding::dispatch(Building::first(), User::first(), 1);

        $this->assertTrue($kingdom->refresh()->buildings->first()->level === 1);
    }

    public function testUpgradeFarm()
    {
        $kingdom = $this->createKingdom([
            'character_id'       => (new CharacterFactory)->createBaseCharacter()->givePlayerLocation()->getCharacter()->id,
            'game_map_id'        => 1,
        ]);

        $kingdom->buildings()->create([
            'game_building_id'   => $this->createGameBuilding(['is_farm' => true])->id,
            'kingdoms_id'        => $kingdom->id,
            'level'              => 1,
            'current_defence'    => 300,
            'current_durability' => 300,
            'max_defence'        => 300,
            'max_durability'     => 300,
        ]);

        $this->createBuildingQueue([
            'character_id' => 1,
            'kingdom_id'   => 1,
            'building_id'  => 1,
            'to_level'     => 2,
        ]);

        UpgradeBuilding::dispatch(Building::first(), User::first(), 1);
        
        $kingdom = $kingdom->refresh();

        $this->assertTrue($kingdom->buildings->first()->level === 2);
        $this->assertTrue($kingdom->max_population > 0);
    }

    public function testUpgradeBuildingWithInvalidResourceType()
    {
        $kingdom = $this->createKingdom([
            'character_id'       => (new CharacterFactory)->createBaseCharacter()->givePlayerLocation()->getCharacter()->id,
            'game_map_id'        => 1,
        ]);

        $kingdom->buildings()->create([
            'game_building_id'   => $this->createGameBuilding([
                'increase_wood_amount'  => 0,
                'increase_clay_amount'  => 0,
                'increase_stone_amount' => 0,
                'increase_iron_amount'  => 0,
                'is_resource_building'  => true,
            ])->id,
            'kingdoms_id'        => $kingdom->id,
            'level'              => 1,
            'current_defence'    => 300,
            'current_durability' => 300,
            'max_defence'        => 300,
            'max_durability'     => 300,
        ]);

        $this->createBuildingQueue([
            'character_id' => 1,
            'kingdom_id'   => 1,
            'building_id'  => 1,
            'to_level'     => 2,
        ]);

        UpgradeBuilding::dispatch(Building::first(), User::first(), 1);
        
        $kingdom = $kingdom->refresh();

        $this->assertTrue($kingdom->buildings->first()->level === 1);
    }
}
