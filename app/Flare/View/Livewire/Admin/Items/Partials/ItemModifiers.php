<?php

namespace App\Flare\View\Livewire\Admin\Items\Partials;

use Livewire\Component;
use App\Flare\Models\Item;

class ItemModifiers extends Component
{

    public $item;

    protected $rules = [
        'item.base_damage_mod'  => 'nullable',
        'item.base_healing_mod' => 'nullable',
        'item.base_ac_mod'      => 'nullable',
        'item.str_mod'          => 'nullable',
        'item.dur_mod'          => 'nullable',
        'item.dex_mod'          => 'nullable',
        'item.chr_mod'          => 'nullable',
        'item.int_mod'          => 'nullable',
        'item.effect'           => 'nullable',
    ];

    protected $listeners = ['validateInput'];

    public function mount() {
        if (is_array($this->item)) {
            $this->item = Item::find($this->item['id']);
        }
    }

    public function validateInput(string $functionName, int $index) {
        $this->validate();

        $this->item->save();

        $this->emitTo('manage', 'storeModel', $this->item->refresh());
        $this->emitTo('manage', $functionName, $index, true);
    }

    public function render()
    {
        return view('components.livewire.admin.items.partials.item-modifiers');
    }
}