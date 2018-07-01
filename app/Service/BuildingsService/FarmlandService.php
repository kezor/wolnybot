<?php

namespace App\Service\BuildingsService;


use App\Building\Farmland;
use App\Field;
use App\Product;
use App\Service\GameService;

class FarmlandService extends GameService
{
    public function cropGarden(Farmland $farmland)
    {
        foreach ($farmland->fields as $finalFieldToReset) {
            if ($finalFieldToReset->canCollect()) {
                $responseData = $this->connector->cropGarden($farmland);
                //$farmland->
                break;
            }

        }
    }

    public function collectReadyPlants(Farmland $farmland)
    {
        /** @var Field $finalFieldToReset */
        foreach ($farmland->fields as $finalFieldToReset) {
            if ($finalFieldToReset->canCollect()) {
                $this->resetRelatedFields($farmland, $finalFieldToReset);
                $this->connector->collect($farmland, $finalFieldToReset);
                $finalFieldToReset->removeProduct();
            }
        }
    }

    private function resetRelatedFields(Farmland $farmland, Field $field)
    {
        for ($i = 0; $i < $field->getOffsetX(); $i++) {
            for ($j = 0; $j < $field->getOffsetY(); $j++) {
                $indexToRemove = $i + $field->getIndex() + ($j * 12);
                if ($indexToRemove !== $field->getIndex()) {
                    $farmland->fields[$indexToRemove]->removeProduct();
                }
            }
        }
    }

    public function seedPlants(Farmland $farmland, Product $productToSeed)
    {
        $emptyFields = $farmland->getEmptyFields();

        $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);

        $responseData = null;

//        while (!empty($fieldsToSeed)) {
//            reset($fieldsToSeed);
        /** @var Field[] $fieldsToSeed */
        foreach ($fieldsToSeed as $field) {
            $responseData = $this->connector->seed($farmland, $field);
            $farmland->updateField([
                'teil_nr' => $field->getIndex(),
                'inhalt' => $field->getProduct()->getPid(),
                'x' => $field->getProduct()->getLength(),
                'y' => $field->getProduct()->getHeight(),
                'phase' => Product::PLANT_PHASE_BEGIN,
                'gepflanzt' => time(),
                'zeit' => time(),
                'iswater' => false,
            ]);
        }

//            $fieldsToSeed = $farmland->getFieldsToSeed();
//        }
        if (null !== $responseData) {
            $remain = $responseData['updateblock']['farms']['farms']['1']['1']['production']['0']['remain'];
            $farmland->remain = time() + $remain;
            $farmland->save();
        }
    }

    private function selectFields($fields, Product $product)
    {
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
                if ($product->getAmount() <= count($finalFieldsAvailableToSeed)) {
                    break;
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

    public function waterPlants(Farmland $farmland)
    {
        /** @var Field $field */
        foreach ($farmland->fields as $field) {
            if ($field->canWater()) {
                $this->connector->waterField($farmland, $field);
            }
        }
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

    private function isNextIndexInNextRow($index, $nextIndex)
    {
        $currentColumn = $this->getColumn($index);
        $nextColumn = $this->getColumn($nextIndex);

        return $nextColumn < $currentColumn;
    }

    private function getColumn($index)
    {
        $column = $index % 12;
        if ($column == 0) {
            $column = 12;
        }

        return $column;
    }
}