<?php

namespace Tests\Feature;

use App\Repository\FieldRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FieldRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetField()
    {
        $player = $this->getTestPlayer();
        $space = $this->getTestSpace($player);
        $fieldData = ['teil_nr' => 17];

        $field = FieldRepository::getField($fieldData, $space);
        $this->assertNotNull($field);
        $this->assertNull($field->id);

        $field->offset_x = 1;
        $field->offset_y = 1;
        $field->save();

        $this->assertNotNull($field->id);

        $existField = FieldRepository::getField($fieldData, $space);

        $this->assertNotNull($existField);
        $this->assertNotNull($existField->id);
        $this->assertEquals($existField->id, $field->id);
    }

}
