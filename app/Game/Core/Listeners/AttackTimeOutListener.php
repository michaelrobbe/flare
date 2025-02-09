<?php

namespace App\Game\Core\Listeners;

use App\Flare\Models\Character;
use App\Game\Core\Events\AttackTimeOutEvent;
use App\Game\Core\Events\ShowTimeOutEvent;
use App\Game\Core\Jobs\AttackTimeOutJob;

class AttackTimeOutListener
{
    /**
     * Handle the event.
     *
     * @param  \App\Game\Battle\UpdateCharacterEvent  $event
     * @return void
     */
    public function handle(AttackTimeOutEvent $event)
    {
        $time = $event->character->is_dead ? 20 : 10;

        if ($time === 10) {
            $time = $time - ($time * $this->findTimeReductions($event->character));
        }

        $event->character->update([
            'can_attack'          => false,
            'can_attack_again_at' => now()->addSeconds($time),
        ]);

        broadcast(new ShowTimeOutEvent($event->character->user, true, false, $time));

        AttackTimeOutJob::dispatch($event->character)->delay(now()->addSeconds($time));
    }

    protected function findTimeReductions(Character $character) {
        $skill = $character->skills->filter(function($skill) {
            return $skill->currently_training && $skill->reduces_time;
        })->first();

        if (is_null($skill)) {
            return 0;
        }
        
        return $skill->fight_time_out_mod;
    }
}
