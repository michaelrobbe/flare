<?php

namespace App\Game\Kingdoms\Jobs;

use App\Admin\Mail\GenericMail;
use App\Flare\Events\ServerMessageEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;;
use App\Flare\Models\User;
use App\Flare\Models\Building;
use App\Flare\Models\BuildingInQueue;
use App\Flare\Models\Kingdom;
use App\Flare\Transformers\KingdomTransformer;
use App\Game\Kingdoms\Events\UpdateKingdom;
use Facades\App\Flare\Values\UserOnlineValue;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Mail;

class RebuildBuilding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Building $building
     */
    protected $building;

    /**
     * @var int queueId
     */
    protected $queueId;

    /**
     * Create a new job instance.
     *
     * @param Building $building
     * @param User $user
     * @param int $queueId
     * @return void
     */
    public function __construct(Building $building, User $user, int $queueId) {
        $this->user     = $user;

        $this->building = $building;

        $this->queueId  = $queueId;
    }

    /**
     * Execute the job.
     *
     * @param Manager $manager
     * @param KingdomTransformer $kingdomTransformer
     * @return void
     */
    public function handle(Manager $manager, KingdomTransformer $kingdomTransformer)
    {

        $queue = BuildingInQueue::find($this->queueId);

        if (is_null($queue)) {
            return;
        }

        $this->building->update([
            'current_durability' => $this->building->max_durability,
        ]);

        if ($this->building->morale_increase > 0) {
            $kingdom = $this->building->kingdom;

            $kingdom->update([
                'current_morale' => $kingdom->current_morale + $this->building->morale_increase,
            ]);
        }

        BuildingInQueue::where('to_level', $this->building->level)
                       ->where('building_id', $this->building->id)
                       ->where('kingdom_id', $this->building->kingdoms_id)
                       ->first()
                       ->delete();
        
        if (UserOnlineValue::isOnline($this->user)) {
            $kingdom = Kingdom::find($this->building->kingdoms_id);
            $kingdom = new Item($kingdom, $kingdomTransformer);
            $kingdom = $manager->createData($kingdom)->toArray();

            event(new UpdateKingdom($this->user, $kingdom));
            event(new ServerMessageEvent($this->user, 'building-repair-finished', $this->building->name . ' finished being rebuilt for kingdom: ' . $this->building->kingdom->name . '.'));
        } else if ($this->user->upgraded_building_email) {
            Mail::to($this->user)->send(new GenericMail(
                $this->user,
                $this->building->name . ' finished being rebuilt for kingdom: ' . $this->building->kingdom->name . '.',
                'A Building Was Rebuilt',
            ));
        }
    }
}