<?php

namespace App;


use Illuminate\Support\Collection;

class BunchesCollection extends Collection
{

    public function getSummaryCount()
    {
        $count = 0;

        foreach ($this->items as $item) {
            $count += $item->count();
        }

        return $count;
    }
}