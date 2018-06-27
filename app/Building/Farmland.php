<?php

namespace App\Building;

use App\Connector\ConnectorInterface;
use App\Farm;
use App\Field;
use App\Player;
use App\Product;
use App\ProductCategoryMapper;
use App\Repository\FieldRepository;
use App\Space;

class Farmland extends Space
{
    private $usedSeeds = [];

    /**
     * @var Field[]
     */
    public $fields;

    protected $table = 'spaces';

    public function fillInFields()
    {
        for ($i = 1; $i <= 120; $i++) {
            $field = FieldRepository::getField($i, $this);
            $this->fields[$i] = $field;
        }

        return $this;
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function getEmptyFields()
    {
        $fieldsToSeed = [];

        foreach ($this->fields as $field) {
            if ($field->canSeed()) {
                $fieldsToSeed[$field->index] = $field;
            }
        }

        return $fieldsToSeed;
    }

//    private function selectFields($fields, Product $product)
//    {
//        reset($fields);
//        $index = key($fields);
//
//        $finalFieldsAvailableToSeed = [];
//
//        while (isset($fields[$index])) {
//            $availableToSeed = true;
//
//            for ($xIndex = 0; $xIndex < $product->getLength(); $xIndex++) {
//                for ($yIndex = 0; $yIndex < $product->getHeight(); $yIndex++) {
//                    $checkingIndex = $index + $xIndex + ($yIndex * 12);
//                    if (!isset($fields[$checkingIndex]) || $this->isNextIndexInNextRow($index, $checkingIndex)) {
//                        $availableToSeed = false;
//                    }
//                }
//            }
//
//            if (!$availableToSeed) {
//                unset($fields[$index]);
//            } else {
//                $finalFieldsAvailableToSeed[$index] = clone $fields[$index];
//                $indexesToRemove = $this->getIndexesToRemove($index, $product);
//                foreach ($indexesToRemove as $indexToRemove) {
//                    unset($fields[$indexToRemove]);
//                }
//                if ($product->getAmount() <= count($finalFieldsAvailableToSeed)) {
//                    break;
//                }
//            }
//            reset($fields);
//            $index = key($fields);
//        }
//
//        /** @var Field $field */
//        foreach ($finalFieldsAvailableToSeed as $field) {
//            $field->setProduct($product);
//        }
//
//        return $finalFieldsAvailableToSeed;
//    }

//    private function getIndexesToRemove($currentIndex, Product $plant)
//    {
//        $indexes = [];
//
//        for ($i = 0; $i < $plant->getLength(); $i++) {
//            for ($j = 0; $j < $plant->getHeight(); $j++) {
//                $indexToRemove = $currentIndex + $i + (12 * $j);
//                $indexes[$indexToRemove] = $indexToRemove;
//            }
//        }
//
//        return $indexes;
//    }

//    private function isNextIndexInNextRow($index, $nextIndex)
//    {
//        $currentColumn = $this->getColumn($index);
//        $nextColumn = $this->getColumn($nextIndex);
//
//        return $nextColumn < $currentColumn;
//    }

//    private function getColumn($index)
//    {
//        $column = $index % 12;
//        if ($column == 0) {
//            $column = 12;
//        }
//
//        return $column;
//    }

    private function getProductToSeed()
    {
        /** @var Product $stockProduct */
        $stockProduct = Product::where('player_id', $this->player_id)
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

    public function clearFields($updatedIndexed)
    {
        /** @var Field $field */
        foreach ($this->fields as $field){
            if(!in_array($field->index, $updatedIndexed)){
                $field->removeProduct()->save();
            }
        }
    }

    public function updateField($fieldData)
    {
        $field = $this->fields[$fieldData['teil_nr']];
        $field->product_pid = $fieldData['inhalt'];
        $field->offset_x = $fieldData['x'];
        $field->offset_y = $fieldData['y'];
        $field->phase = $fieldData['phase'];
        $field->planted = $fieldData['gepflanzt'];
        $field->time = $fieldData['zeit'];
        $field->save();
    }
}