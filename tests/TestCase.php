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

    protected function getTestSpace(Player $player)
    {
        $space = new Space();
        $space->player = $player;
        $space->farm = 1;
        $space->position = 1;
        return $space;
    }

    protected function getTestField(Product $product)
    {
        $field = new Field();
        $field->index = 1;
        $field->phase = Product::PLANT_PHASE_FINAL;
        $field->setProduct($product);
        return $field;
    }

    protected function getTestProduct()
    {
        $product = new Product();
        $product->setAmount(123);
        $product->setPid(1);
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
