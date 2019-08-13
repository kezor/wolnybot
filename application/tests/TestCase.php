<?php

namespace Tests;

use App\Building\Farmland;
use App\Farm;
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
        $player            = new Player();
        $player->id        = 1;
        $player->server_id = 1;
        $player->username  = 'TestUser';
        $player->password  = 'testPass';
        $player->active    = true;
        $player->syncOriginal();

        return $player;
    }

    protected function getTestField(Product $product, $options = [])
    {
        $plantPhase = Product::PLANT_PHASE_FINAL;
        if (isset($options['phase'])) {
            $plantPhase = $options['phase'];
        }

        $index = 1;
        if (isset($options['index'])) {
            $index = $options['index'];
        }

        $field = new Field();
        $field->setProductPid($product->getPid());
        $field->setPhase($plantPhase);
        $field->time = $plantPhase > 1 ? time() : '';
        $field->setProduct($product);
        $field->setOffsetX(1);
        $field->setOffsetY(1);
        $field->index = $index;

        return $field;
    }

    protected function getTestFarm(Player $player, $customOptions = [])
    {
        $farm          = new Farm();
        $farm->id      = 1;
        $farm->farm_id = $customOptions['farm_id'] ?? 1;
        $farm->player  = $player;

        return $farm;
    }

    protected function getTestFarmland(Farm $farm, $customOptions = [])
    {
        $farmland           = new Farmland();
        $farmland->farm     = $farm;
        $farmland->position = $customOptions['position'] ?? 1;

        for ($i = 1; $i <= 120; $i++) {
            $field                = new Field();
            $field->index         = $i;
            $farmland->fields[$i] = $field;
        }

        return $farmland;
    }

    protected function getTestProduct(Player $player, $pid = 1, $amount = 120)
    {
        $product = new Product();
        $product->setAmount($amount);
        $product->setPid($pid);
        $product->player = $player->id;
        $product->syncOriginal();

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

    protected function getHovelData()
    {
        return json_decode($this->loadJSON('getHovelInitData'), true);
    }

    protected function getHovelData2()
    {
        return json_decode($this->loadJSON('getHovelInitData2'), true);
    }
}
