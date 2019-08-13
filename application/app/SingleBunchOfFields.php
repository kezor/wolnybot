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

    public function getUrlPartWithProduct(Product $product)
    {
        $returnString = '';

        /** @var Field $item */
        foreach ($this->items as $item) {
            //&pflanze[]=17&feld[]=36&felder[]=36
            $returnString .= '&pflanze[]=' . $product->pid;
            $returnString .= '&feld[]=' . $item->index;
            $returnString .= '&felder[]=' . $item->getRelatedFields();
        }

        return $returnString;
    }

}