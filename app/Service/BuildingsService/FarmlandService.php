<?php

namespace App\Service\BuildingsService;


use App\Building\Farmland;
use App\Facades\ActivitiesService;
use App\Field;
use App\Product;
use App\Service\GameService;
use Illuminate\Support\Collection;

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
        $fieldsReadyToCollect = $farmland->getFieldsReadyToCollect();
        ActivitiesService::foundReadyToCollect($farmland, count($fieldsReadyToCollect));

        $collectedPlantsCount = 0;
        /** @var Field $finalFieldToReset */
        foreach ($fieldsReadyToCollect as $finalFieldToReset) {
            $this->connector->collect($farmland, $finalFieldToReset);
            $finalFieldToReset->removeProduct();
            $collectedPlantsCount++;
        }
        ActivitiesService::collectedFields($farmland, $collectedPlantsCount);
    }

    public function seedPlants(Farmland $farmland, Product $productToSeed)
    {
        $emptyFields = $farmland->getEmptyFields();

        ActivitiesService::foundReadyToSeed($farmland, $emptyFields->count());

        $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);

        $responseData = null;

        /** @var Field[] $fieldsToSeed */
        foreach ($fieldsToSeed as $field) {

            $responseData = $this->connector->seed($farmland, $field);
            $farmland->updateField([
                'teil_nr'   => $field->getIndex(),
                'inhalt'    => $productToSeed->getPid(),
                'x'         => $productToSeed->getLength(),
                'y'         => $productToSeed->getHeight(),
                'phase'     => Product::PLANT_PHASE_BEGIN,
                'gepflanzt' => time(),
                'zeit'      => time(),
                'iswater'   => false,
            ]);
        }

        ActivitiesService::seededFields($farmland, count($fieldsToSeed), $productToSeed);

        if (null !== $responseData) {
            $remain = $responseData['updateblock']['farms']['farms'][$farmland->farm->id][$farmland->position]['production']['0']['remain'];
            $farmland->remain = time() + $remain;
            $farmland->push();
        }
    }

    /**
     * @param         $fields
     * @param Product $product
     * @return Collection
     */
    private function selectFields(Collection $fields, Product $product)
    {
        $finalFieldsAvailableToSeed = new Collection();

        while ($fields->isNotEmpty()) {

            /** @var Field $field */

            $field = $fields->first();
            $index = $field->index;

            for ($xIndex = 0; $xIndex < $product->getLength(); $xIndex++) {
                for ($yIndex = 0; $yIndex < $product->getHeight(); $yIndex++) {
                    $checkingIndex = $index + $xIndex + ($yIndex * 12);
                    if (!$fields->has($checkingIndex) || $this->isNextIndexInNextRow($index, $checkingIndex)) {
                        $fields->forget($checkingIndex);
                    } else {
                        $finalFieldsAvailableToSeed->put($index, $field);
                        $indexesToRemove = $this->getIndexesToRemove($index, $product);
                        foreach ($indexesToRemove as $indexToRemove) {
                            $fields->forget($indexToRemove);
                        }
                    }
                }
            }
            if ($product->getAmount() <= $finalFieldsAvailableToSeed->count()) {
                break;
            }
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