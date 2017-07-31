<?php

namespace App\Building;


use App\Product;

class Hovel extends AbstractBuilding
{

    protected $feedOptions = [];

    protected $remaining;
    protected $availableToFeed;

    public function process()
    {
        $this->updateData();
        $this->collect();
        $this->feed();
    }

    private function updateData()
    {
        $data = $this->connector->initHovel($this);

        $data = $data['datablock'][1][1][2];

        $feedOptions = $data['feed'];

        foreach ($feedOptions as $index =>  $feedOption){
            $this->feedOptions[$index] = $feedOption['time'];
        }

        $this->remaining = $data['remain'];
        $this->availableToFeed = $data['rest'];
    }

    private function collect()
    {
        if($this->remaining < 0){
            $this->connector->collectEggs($this);
        }
    }

    private function feed()
    {
        while($this->availableToFeed > 0 && $this->availableToFeed > $this->feedOptions[1]){
            $product = new Product(); // TODO
            $this->connector->feedChickens($this, $product);
            $this->availableToFeed -= $this->feedOptions[1];
        }

    }
}