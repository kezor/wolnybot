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
    public function fields()
    {
        return $this->hasMany(Field::class, 'space_id');
    }

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
        $field = $this->fields[$fieldData['teil_nr']];
        $field->product_pid = $fieldData['inhalt'];
        $field->offset_x = $fieldData['x'];
        $field->offset_y = $fieldData['y'];
        $field->phase = $fieldData['phase'];
        $field->planted = $fieldData['gepflanzt'];
        $field->time = $fieldData['zeit'];
        $field->water = (bool)$fieldData['iswater'];
    }

    public function getFieldsReadyToCollect()
    {
        $fieldsReadyToCollect = [];
        /** @var Field $finalFieldToReset */
        foreach ($this->fields as $finalFieldToReset) {
            if ($finalFieldToReset->canCollect()) {
                $this->resetRelatedFields($finalFieldToReset);

                $fieldsReadyToCollect[$finalFieldToReset->index] = $finalFieldToReset;
//                $finalFieldToReset->removeProduct();
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
                    $this->fields[$indexToRemove]->removeProduct();
                }
            }
        }
    }
}
