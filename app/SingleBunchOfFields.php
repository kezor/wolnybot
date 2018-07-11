<?php

namespace App;


use Illuminate\Support\Collection;

class SingleBunchOfFields extends Collection
{

    public function getIndexes()
    {
        $returnString = '';

        /** @var Field $item */
        foreach ($this->items as $item) {
            $returnString .= $item->index;
        }

        return $returnString;
    }

    public function getRelatedFields()
    {
        $returnString = '';

        /** @var Field $item */
        foreach ($this->items as $item) {
            $returnString .= $item->getRelatedFields();
        }

        return $returnString;
    }

}