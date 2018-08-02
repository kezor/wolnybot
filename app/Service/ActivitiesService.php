<?php

namespace App\Service;

use App\Building\Farmland;
use App\Models\Activity;
use App\Player;
use App\Product;
use App\Space;
use Illuminate\Database\Eloquent\Model;

class ActivitiesService
{

    public static function collectedFields(Farmland $farmland, $amount)
    {
        self::add($farmland, 'Collected ' . $amount . ' fields.');
    }

    public static function foundReadyToCollect(Farmland $farmland, $amount)
    {
        self::add($farmland, 'Found ' . $amount . ' fields ready to collect.');
    }

    public static function seededFields(Farmland $farmland, $amount, Product $product)
    {
        self::add($farmland, 'Seeded ' . $amount . ' fields with product: ' . $product->getName() . '.');
    }

    public static function foundReadyToSeed(Farmland $farmland, $amount)
    {
        self::add($farmland, 'Found ' . $amount . ' fields ready to seed.');
    }

    public static function stockUpdated(Player $player)
    {
        self::add($player, 'Stock updated.');
    }

    public static function farmlandNotReadyToCrop(Farmland $farmland)
    {
        self::add($farmland, 'Farmland is not ready to crop.');
    }

    private static function add(Model $entity, $message)
    {
        $classname = get_class($entity);
        if ($entity instanceof Space) {
            $classname = Space::class;
        }

        $activity             = new Activity();
        $activity->entity_id  = $entity->id;
        $activity->class_name = $classname;
        $activity->message    = $message;
        $activity->save();
    }
}