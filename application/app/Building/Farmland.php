<?php

namespace App\Building;

use App\Farm;
use App\Field;
use App\Repository\FieldRepository;
use App\Space;
use Illuminate\Support\Collection;

/**
 * Class Farmland
 * @package App\Building
 * @property Field[] fields
 */
class Farmland extends Space
{


    public function fields()
    {
        return $this->hasMany(Field::class, 'space_id');
    }

    protected $table = 'spaces';
//
//    public function fillInFields()
//    {
//        for ($i = 1; $i <= 120; $i++) {
//            $field = FieldRepository::getField($i, $this);
//            $this->fields[$i] = $field;
//        }
//
//        return $this;
//    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * @return Collection
     */
    public function getEmptyFields()
    {
        $fieldsToSeed = new Collection();
        $fields       = $this->fields()->orderBy('index')->get();

        foreach ($fields as $field) {
            if ($field->canSeed()) {
                $fieldsToSeed->put($field->index, $field);
            }
        }

        return $fieldsToSeed;
    }

    public function clearFields($updatedIndexed)
    {
        /** @var Field $field */
        foreach ($this->fields as $field) {
            if (!in_array($field->index, $updatedIndexed)) {
                $field->removeProduct();
            }
        }
    }

    public function updateField($fieldData)
    {
        $index              = $fieldData['teil_nr'];
        $field              = $this->getFieldAtIndex($index);
        $field->product_pid = $fieldData['inhalt'];
        $field->offset_x    = $fieldData['x'];
        $field->offset_y    = $fieldData['y'];
        $field->phase       = $fieldData['phase'];
        $field->planted     = $fieldData['gepflanzt'];
        $field->time        = $fieldData['zeit'];
        $field->water       = (bool)$fieldData['iswater'];
    }

    public function getFieldsReadyToCollect()
    {
        $fieldsReadyToCollect = [];
        /** @var Field $finalFieldToReset */
        foreach ($this->fields as $finalFieldToReset) {
            if ($finalFieldToReset->isReadyToCrop()) {
                $this->resetRelatedFields($finalFieldToReset);

                $fieldsReadyToCollect[$finalFieldToReset->index] = $finalFieldToReset;
            }
        }

        return $fieldsReadyToCollect;
    }

    private function resetRelatedFields(Field $field)
    {
        for ($i = 0; $i < $field->getOffsetX(); $i++) {
            for ($j = 0; $j < $field->getOffsetY(); $j++) {
                $indexToRemove = $i + $field->getIndex() + ($j * 12);
                if ($indexToRemove !== $field->getIndex()) {
                    $this->getFieldAtIndex($indexToRemove)->removeProduct();
                }
            }
        }
    }

    public function getFieldAtIndex($index)
    {
        foreach ($this->fields as $field) {
            if ($field->index === $index) {
                return $field;
            }
        }

        return $this->fields[] = FieldRepository::getField($index, $this);
    }

    /**
     * @return bool
     */
    public function isReadyToCrop()
    {
        foreach ($this->fields as $field) {
            if ($field->isReadyToCrop()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasFieldsRadyToSeed()
    {
        foreach ($this->fields as $field) {
            if ($field->canSeed()) {
                return true;
            }
        }

        return false;
    }
}
