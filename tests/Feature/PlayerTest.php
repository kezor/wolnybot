<?php

namespace Tests\Feature;


use App\BuildingType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetSpace()
    {
        $player = $this->getTestPlayer();

        $spaces[] = $this->getTestSpace($player, BuildingType::FARMLAND);
        $spaces[] = $this->getTestSpace($player, BuildingType::HOVEL);
        $spaces[] = $this->getTestSpace($player, BuildingType::FARMLAND);

        $this->assertCount(3, $player->getSpaces());
    }

}
