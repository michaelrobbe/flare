<?php

namespace App\Admin\Services;

use Mail;
use Facades\App\Flare\Values\UserOnlineValue;
use App\Admin\Mail\GenericMail;
use App\Flare\Events\ServerMessageEvent;
use App\Flare\Events\UpdateTopBarEvent;
use App\Flare\Models\Character;
use App\Flare\Models\GameClass;
use App\Flare\Models\GameRace;

class UpdateCharacterStatsService {

    /**
     * Updates the characters racial stats.
     * 
     * Will update all characters with their racial stats. We start by subtracting the old race modifier and apply the new racial modifier.
     * Because racial modifiers only get applied once, we don thave to worry about the characters stats being messed up.
     * 
     * Characters do not get told of racial stat adjustments, how ever their top bar will be updated.
     * 
     * @param GameRace $oldRace
     * @param GameRace $newRace
     * @return void
     */
    public function updateRacialStats(GameRace $oldRace, GameRace $newRace): void {
        Character::where('game_race_id', $newRace->id)->chunkById(1000, function($characters) use($oldRace, $newRace) {
            foreach ($characters as $character) {
                $character->update([
                    'str'           => ($character->str - $oldRace->str_mod) + $newRace->str_mod,
                    'dur'           => ($character->dur - $oldRace->dur_mod) + $newRace->dur_mod,
                    'dex'           => ($character->dex - $oldRace->dex_mod) + $newRace->dex_mod,
                    'chr'           => ($character->chr - $oldRace->chr_mod) + $newRace->chr_mod,
                    'int'           => ($character->int - $oldRace->int_mod) + $newRace->int_mod,
                    'ac'            => ($character->ac - $oldRace->deffense_mod) + $newRace->deffense_mod,
                ]);
                
                event(new UpdateTopBarEvent($character->refresh()));
            }
        });
    }

    /**
     * Update the characters class stats.
     * 
     * When a characters class is modifierd we will update the states, buy subtracting old and adding new.
     * 
     * The only time we alert the player of a change to their character class is if the base damage stat changes.
     * Once that happens we update the user via server message or mail if they are not logged in.
     * 
     * @param GameClass $oldClass
     * @param GameClass $newClass
     * @return void
     */
    public function updateClassStats(GameClass $oldClass, GameClass $newClass): void {
        Character::where('game_class_id', $newClass->id)->chunkById(1000, function($characters) use($oldClass, $newClass) {
            foreach ($characters as $character) {
                $character->update([
                    'str'           => ($character->str - $oldClass->str_mod) + $newClass->str_mod,
                    'dur'           => ($character->dur - $oldClass->dur_mod) + $newClass->dur_mod,
                    'dex'           => ($character->dex - $oldClass->dex_mod) + $newClass->dex_mod,
                    'chr'           => ($character->chr - $oldClass->chr_mod) + $newClass->chr_mod,
                    'int'           => ($character->int - $oldClass->int_mod) + $newClass->int_mod,
                    'ac'            => ($character->ac - $oldClass->deffense_mod) + $newClass->deffense_mod,
                ]);

                if ($oldClass->damage_stat !== $newClass->damage_stat) {
                    $totalToAdjust = ($character->level - 1) * 2;
                    
                    if ($character->level > 1) {
                        $character->{$oldClass->damage_stat} -= $totalToAdjust;
                        $character->{$oldClass->damage_stat} += $character->level - 1;

                        $character->{$newClass->damage_stat} -= $character->level - 1;
                        $character->{$newClass->damage_stat} += $totalToAdjust;
                    }

                    if (UserOnlineValue::isOnline($character->user)) {
                        event(new UpdateTopBarEvent($character->refresh()));
                        event(new ServerMessageEvent($character->user, 'new-damage-stat', $newClass->damage_stat));
                    } else {
                        $message = 'Your classes damage stat has been changed to: ' . $newClass->damage_stat . '.';
    
                        Mail::to($character->user->email)->send(new GenericMail($character->user, $message, 'Damage Stat Change'));
                    }
                }
            }
        });
    }
}