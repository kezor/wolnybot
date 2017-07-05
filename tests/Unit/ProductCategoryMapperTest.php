<?php

namespace Tests\Unit;

use App\ProductCategoryMapper   ;
use Tests\TestCase;

class ProductCategoryMapperTest extends TestCase
{
    public function testGetVegetablesIds()
    {
        $vegetablesIds = [
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            17,
            18,
            19,
            20,
            21,
            22,
            23,
            24,
            26,
            29,
            31,
            32,
            33,
            34,
            35,
            36,
            37,
            38,
            39,
            40,
            41,
            42,
            43,
            44,
            97,
            104,
            107,
            108,
            109,
            112,
            113,
            114,
            115,
            126,
            127,
            128,
            129,
            153,
            154,
            158,
        ];
        $this->assertArraySubset($vegetablesIds, ProductCategoryMapper::getVegetablesPids());
    }
}
