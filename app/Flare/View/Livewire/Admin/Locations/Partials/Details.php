<?php

namespace App\Flare\View\Livewire\Admin\Locations\Partials;

use Livewire\Component;
use App\Flare\Cache\CoordinatesCache;
use App\Flare\Models\GameMap;
use App\Flare\Models\Location;

class Details extends Component
{
    public $maps;

    public $location;

    public $coordinates;

    protected $rules = [
        'location.name'        => 'required',
        'location.description' => 'required',
        'location.x'           => 'required',
        'location.y'           => 'required',
        'location.game_map_id' => 'required',
    ];

    protected $messages = [
        'location.game_map_id.required' => 'You must select a map for this location.',
    ];

    protected $listeners = ['validateInput'];

    public function mount(CoordinatesCache $coordinatesCache) {
        $this->maps        = GameMap::all()->pluck('name', 'id')->toArray();
        $this->coordinates = $coordinatesCache->getFromCache();

        if (is_null($this->location)) {
            $this->location = new Location;
        } else if (is_array($this->location)) {
            $this->location = Location::find($this->location['id']);
        }
    }

    public function validateInput(string $functionName, int $index) {
        $this->validate();

        $this->location->save();

        $this->emitTo('manage', 'storeModel', $this->location->refresh());
        $this->emitTo('manage', $functionName, $index, true);
    }

    public function render()
    {
        return view('components.livewire.admin.locations.partials.details');
    }
}