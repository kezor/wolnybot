<?php

namespace Tests;

use App\Field;
use App\Player;
use App\Product;
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

    protected function getTestField(Product $product, $plantPhase = Product::PLANT_PHASE_FINAL)
    {
        $field = new Field(1);
        $field->setProductPid($product->getPid());
        $field->setPhase($plantPhase);
        $field->setTime(($plantPhase > 1) ? time() : '');
        $field->setProduct($product);
        $field->setOffsetX(1);
        $field->setOffsetY(1);
//        $field->save();
        return $field;
    }

    protected function getTestProduct(Player $player, $pid = 1, $amount = 120)
    {
        $product = new Product();
        $product->setAmount($amount);
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

    protected function getDashboardSuccessData()
    {
        return json_decode($this->loadJSON('getFarmSuccess'), true);
    }

    protected function getGardeninitSuccessData()
    {
        return json_decode($this->loadJSON('getGardeninitSuccess'), true);
    }
}
