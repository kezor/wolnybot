<?php

namespace Tests\Feature;

use App\Building\Farmland;
use App\Building\Hovel;
use App\Farm;
use Tests\TestCase;

class FarmTest extends TestCase
{

    public function testFarmNoSpaces()
    {
        $farm = new Farm();
        $farm->process();
    }

    public function testFarmProcessFarmland()
    {
        $farm = new Farm();
        $farmland = new Farmland(['farm' => 1, 'position' => 1], $this->getTestPlayer());
        $farm->addFarmland($farmland);
        $farm->process();
    }

    public function testFarmProcessHovel()
    {
        $farm = new Farm();
        $hovel = new Hovel(['farm' => 1, 'position' => 1], $this->getTestPlayer());
        $farm->addBuilding($hovel);
        $farm->process();
    }
}
