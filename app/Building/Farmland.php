<?php

namespace App\Building;

use App\Connector\ConnectorInterface;
use App\Field;
use App\Product;
use App\ProductCategoryMapper;

class Farmland extends AbstractBuilding
{
    private $usedSeeds = [];

    /**
     * @var Field[]
     */
    private $fields;

    public function __construct($spaceData, $player)
    {
        parent::__construct($spaceData, $player);
        for ($i = 1; $i <= 120; $i++) {
            $this->fields[$i] = new Field($i);
        }
    }

    public function process()
    {
        $this->collectReady();
        $this->seed();
        $this->water();
    }

    private function drawFields()
    {
        foreach ($this->fields as $field) {
            echo '[' . $field->getProductPid() . ']';
            if ($field->getIndex() % 12 == 0) {
                echo PHP_EOL;
            }
        }
    }

    private function collectReady()
    {
        /** @var Field $finalFieldToReset */
        foreach ($this->fields as $finalFieldToReset) {
            if ($finalFieldToReset->canCollect()) {
                $this->resetRelatedFields($finalFieldToReset);
                $this->connector->collect($this, $finalFieldToReset);
                $finalFieldToReset->removeProduct();
            }
        }
    }

    private function resetRelatedFields(Field $field)
    {
        for ($i = 0; $i < $field->getOffsetX(); $i++) {
            for ($j = 0; $j < $field->getOffsetY(); $j++) {
                $indexToRemove = $i + $field->getIndex() + ($j * 12);
                if ($indexToRemove !== $field->getIndex()) {
                    $this->fields[$indexToRemove]->removeProduct();
                }
            }
        }
    }

    private function seed()
    {
        $fieldsToSeed = $this->getFieldsToSeed();

        while (!empty($fieldsToSeed)) {
            reset($fieldsToSeed);
            /** @var Field[] $fieldsToSeed */
            foreach ($fieldsToSeed as $field) {
                $this->connector->seed($this, $field);
                $this->updateField([
                    'teil_nr' => $field->getIndex(),
                    'inhalt' => $field->getProduct()->getPid(),
                    'x' => $field->getProduct()->getLength(),
                    'y' => $field->getProduct()->getHeight(),
                    'phase' => Product::PLANT_PHASE_BEGIN,
                    'gepflanzt' => time(),
                    'zeit' => time(),
                ]);
            }

            $fieldsToSeed = $this->getFieldsToSeed();
        }
    }

    private function getFieldsToSeed()
    {
        do {
            $productToSeed = $this->getProductToSeed();
            if (!$productToSeed) {
                return false;
            }
            $emptyFields = $this->getEmptyFields();

            $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);
        } while (empty($fieldsToSeed));

        return $fieldsToSeed;
    }

    private function getEmptyFields()
    {
        $fieldsToSeed = [];

        foreach ($this->fields as $field) {
            if ($field->canSeed()) {
                $fieldsToSeed[$field->getIndex()] = $field;
            }
        }

        return $fieldsToSeed;
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

    private function water()
    {
        /** @var Field $field */
        foreach ($this->fields as $field) {
            if ($field->canWater()) {
                $this->connector->waterField($this, $field);
            }
        }
    }

    public function updateField($fieldData)
    {
        $field = $this->fields[$fieldData['teil_nr']];
        $field->setProductPid($fieldData['inhalt']);
        $field->setOffsetX($fieldData['x']);
        $field->setOffsetY($fieldData['y']);
        $field->setPhase($fieldData['phase']);
        $field->setPlanted($fieldData['gepflanzt']);
        $field->setTime($fieldData['zeit']);
        return $this;
    }
}