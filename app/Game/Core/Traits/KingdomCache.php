<?php

namespace App\Game\Core\Traits;

use Cache;
use Illuminate\Database\Eloquent\Collection;
use App\Flare\Models\Character;
use App\Flare\Models\Kingdom;

trait KingdomCache {

    /**
     * Get the kingdom information from cache.
     * 
     * If the  character does not have a cache of kingdoms, we will
     * then create the cache for them, so next time it's easy to fetch.
     * 
     * @param Character $character
     * @return array
     */
    public function getKingdoms(Character $character): array {
        if (Cache::has('character-kingdoms-' . $character->id)) {
            return Cache::get('character-kingdoms-' . $character->id);
        }

        $kingdoms = Kingdom::select('id', 'x_position', 'y_position', 'color', 'name')
                            ->where('character_id', $character->id)
                            ->get();
        
        Cache::put('character-kingdoms-' . $character->id, $this->createKingdomArray($kingdoms));

        return Cache::get('character-kingdoms-' . $character->id);
    }

    /**
     * Adds a kingdom to the cache.
     * 
     * If the cache does not exist, we will create the cache.
     * 
     * @param Character $character
     * @param Kingdom $kingdom
     * @return array
     */
    public function addKingdomToCache(Character $character, Kingdom $kingdom): array {
        if (Cache::has('character-kingdoms-' . $character->id)) {
            $cache = Cache::get('character-kingdoms-' . $character->id);

            Cache::put('character-kingdoms-' . $character->id, $this->addKingdom($kingdom, $cache));
        }

        Cache::put('character-kingdoms-' . $character->id, $this->addKingdom($kingdom));

        return Cache::get('character-kingdoms-' . $character->id);
    }

    /**
     * Adds a kingdom to the array of cache.
     * 
     * If the cache is empty we will set a kingdom to it by pushing
     * the kingdom to the array.
     * 
     * @param Kingdom $kingdom
     * @param array $cache | []
     * @return array
     */
    protected function addKingdom(Kingdom $kingdom, array $cache = []): array {
        $cache[] = [
            'id'         => $kingdom->id,
            'name'       => $kingdom->name,
            'x_position' => $kingdom->x_position,
            'y_position' => $kingdom->y_position,
            'color'      => $kingdom->color,
        ];

        return $cache;
    }

    /**
     * Create the kingdom array for the cache.
     * 
     * @param Collection $kingdoms
     */
    protected function createKingdomArray(Collection $kingdoms): array {
        $kingdomData = [];
        
        if ($kingdoms->isEmpty()) {
            return $kingdomData;
        }

        foreach ($kingdoms as $kingdom) {
            $kingdomData[] = [
                'id'         => $kingdom->id,
                'name'       => $kingdom->name,
                'x_position' => $kingdom->x_position,
                'y_position' => $kingdom->y_position,
                'color'      => $kingdom->color,
            ];
        }

        return $kingdomData;
    }
}