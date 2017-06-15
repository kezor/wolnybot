<?php

namespace App\Service;


use App\Connector\ConnectorInterface;
use App\Field;
use App\Player;
use App\Product;
use App\ProductCategoryMapper;
use App\Space;

class FarmlandService
{
    private $player;

    private $connector;

    private $usedSeeds = [];

    public function __construct(Player $player, ConnectorInterface $connector)
    {
        $this->connector = $connector;
        $this->player = $player;
    }

    public function run()
    {

    }

    public function collectReady()
    {
        $spaces = $this->player->getSpaces();
        /** @var Space $space */
        foreach ($spaces as $space) {
            $this->collectFromSpace($space);
        }
    }

    private function collectFromSpace(Space $space)
    {
        $fieldsToCollect = $space->getFieldsToCollect();

        $fields = [];
        /** @var Field $item */
        foreach ($fieldsToCollect as $item) {
            $fields[$item->index] = $item;
        }

        /** @var Field $finalFieldToReset */
        foreach ($fields as $key => &$finalFieldToReset) {
            for ($i = 0; $i < $finalFieldToReset->offset_x; $i++) {
                for ($j = 0; $j < $finalFieldToReset->offset_y; $j++) {
                    $indexToRemove = $i + $finalFieldToReset->index + ($j * 12);
                    if ($indexToRemove !== $finalFieldToReset->index) {
                        unset($fields[$indexToRemove]);
                    }
                }
            }
        }

        /** @var Field $field */
        foreach ($fields as $field) {
            $this->connector->collect($field);
            $field->setAsEmpty();
        }
        echo 'Collected ' . count($fields) . ' on space position: ' . $space->position . PHP_EOL;
    }

    public function seed()
    {
        $userSpaces = $this->player->getSpaces();
        foreach ($userSpaces as $space) {
            $seededFields = [];

            $fieldsToSeed = $this->getFieldsToSeed($space);
            while (!empty($fieldsToSeed)) {
                reset($fieldsToSeed);
                $index = key($fieldsToSeed);
                /** @var Field[] $fieldsToSeed */
                foreach ($fieldsToSeed as $field) {
                    $result = $this->connector->seed($field);

//                    if($result){
                    $seededFields[] = $field;
//                    }
                }

                $fieldsToSeed = $this->getFieldsToSeed($space);
            }
            $this->waterFields($seededFields);
        }
    }

    private function waterFields($fields)
    {
        /** @var Field $field */
        foreach ($fields as $field) {
            $this->connector->waterField($field);
        }
    }

    private function getFieldsToSeed(Space $space)
    {
        do {
            $productToSeed = $this->getProductToSeed();
            if (!$productToSeed) {
                return false;
            }
            $emptyFields = $this->getEmptyFields($space);

            $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);
        } while (empty($fieldsToSeed));

        return $fieldsToSeed;
    }


    private function getEmptyFields(Space $space)
    {
        return Field::whereNull('product_pid')
            ->where('space', $space->id)
            ->get();
    }

    private function getProductToSeed()
    {
        /** @var Product $stockProduct */
        $stockProduct = Product::where('player', $this->player->id)
            ->where('amount', '>', 0)
            ->whereIn('pid', ProductCategoryMapper::getVegetablesPids())
            ->whereNotIn('pid', $this->usedSeeds)
            ->orderBy('amount', 'ASC')
            ->first();
        if (!$stockProduct) {
            return null;
        }

        $this->usedSeeds[] = $stockProduct->pid;

        return $stockProduct;
    }

    private function selectFields($fieldsCollection, Product $product)
    {
        $fields = [];
        /** @var Field $field */
        foreach ($fieldsCollection as $field) {
            $fields[$field->index] = $field;
        }

        reset($fields);
        $index = key($fields);

        $finalFieldsAvailableToSeed = [];

        while (isset($fields[$index])) {
            $availableToSeed = true;

            for ($xIndex = 0; $xIndex < $product->getLength(); $xIndex++) {
                for ($yIndex = 0; $yIndex < $product->getHeight(); $yIndex++) {
                    $checkingIndex = $index + $xIndex + ($yIndex * 12);
                    if (!isset($fields[$checkingIndex]) || $this->isNextIndexInNextRow($index, $checkingIndex)) {
                        $availableToSeed = false;
                    }
                }
            }

            if (!$availableToSeed) {
                unset($fields[$index]);
            } else {
                $finalFieldsAvailableToSeed[$index] = clone $fields[$index];
                $indexesToRemove = $this->getIndexesToRemove($index, $product);
                foreach ($indexesToRemove as $indexToRemove) {
                    unset($fields[$indexToRemove]);
                }
            }
            reset($fields);
            $index = key($fields);
        }

        /** @var Field $field */
        foreach ($finalFieldsAvailableToSeed as $field) {
            $field->setProduct($product);
        }

        return $finalFieldsAvailableToSeed;
    }


    private function getIndexesToRemove($currentIndex, Product $plant)
    {
        $indexes = [];

        for ($i = 0; $i < $plant->getLength(); $i++) {
            for ($j = 0; $j < $plant->getHeight(); $j++) {
                $indexToRemove = $currentIndex + $i + (12 * $j);
                $indexes[$indexToRemove] = $indexToRemove;
            }
        }

        return $indexes;
    }

    protected function isNextIndexInNextRow($index, $nextIndex)
    {
        $currentColumn = $this->getColumn($index);
        $nextColumn = $this->getColumn($nextIndex);

        return $nextColumn < $currentColumn;
    }

    protected function getColumn($index)
    {
        $column = $index % 12;
        if ($column == 0) {
            $column = 12;
        }

        return $column;
    }
}