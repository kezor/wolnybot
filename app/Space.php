<?php

namespace App;

use App\Repository\PlayerRepository;
use App\Repository\ProductRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer id
 * @property integer player
 * @property integer farm
 * @property integer position
 * @property integer building_type
 * @property boolean fields_in_database
 */
class Space extends Model
{

    public function isFieldsInDatabase()
    {
        if ($this->building_type != BuildingType::FARMLAND) {
            throw new \Exception('You can use this method only for farmland');
        }
        return $this->fields_in_database;
    }

    public function getFields()
    {
        if ($this->building_type != BuildingType::FARMLAND) {
            throw new \Exception('You can use this method only for farmland');
        }
        return Field::where('space', $this->id)->get();
    }

    public function getFieldsToCollect()
    {
        if ($this->building_type != BuildingType::FARMLAND) {
            throw new \Exception('You can use this method only for farmland');
        }
        $fields = Field::where('phase', 4)
            ->where('space', $this->id)
            ->where('time', '!=', 0)
            ->get();

        /** @var Field $field */
        foreach ($fields as $field) {
            $product = new Product();
            $product->setPid($field->product_pid);
            $field->setProduct($product);
        }
        return $fields;
    }

    public function getPlayer()
    {
        return PlayerRepository::getById($this->player);
    }
}
