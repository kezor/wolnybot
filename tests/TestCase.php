<?php

namespace Tests;

use App\Field;
use App\Player;
use App\Product;
use App\Space;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    /**
     * @return Player
     */
    protected function getTestPlayer()
    {
        $player = new Player();
        $player->server_id = 1;
        $player->username = 'TestUser';
        $player->password = 'testPass';
        $player->active = true;
        $player->save();
        return $player;
    }

    protected function getTestSpace(Player $player, $buildingType = null, $fieldsInDatabase = null)
    {
        $space = new Space();
        $space->player = $player->id;
        $space->farm = 1;
        $space->position = 1;
        if ($buildingType) {
            $space->building_type = $buildingType;
        }
        if ($fieldsInDatabase !== null) {
            $space->fields_in_database = $fieldsInDatabase;
        }
        $space->save();
        return $space;
    }

    protected function getTestField(Product $product, Space $space, $plantPhase = Product::PLANT_PHASE_FINAL)
    {
        $field = new Field();
        $field->index = 1;
        $field->product_pid = $product->getPid();
        $field->phase = $plantPhase;
        $field->time = ($plantPhase > 1) ? time() : '';
        $field->setProduct($product);
        $field->space = $space->id;
        $field->offset_y = 1;
        $field->offset_x = 1;
        $field->save();
        return $field;
    }

    protected function getTestProduct(Player $player, $pid = 1)
    {
        $product = new Product();
        $product->setAmount(123);
        $product->setPid($pid);
        $product->player = $player->id;
        $product->save();
        return $product;
    }

    protected function loadJSON($filename)
    {
        $path = storage_path() . "/../tests/Unit/jsonData/${filename}.json"; // ie: /var/www/laravel/app/storage/json/filename.json
        if (!File::exists($path)) {
            throw new \Exception("Invalid File");
        }

        return File::get($path); // string
    }
}
