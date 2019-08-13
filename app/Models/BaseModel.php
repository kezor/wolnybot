<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getActivities($classname = null)
    {
        $entityId = $this->id;
        if (null === $classname) {
            $classname = get_class($this);
        }

        return Activity::where('entity_id', $entityId)
            ->where('class_name', $classname)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();
    }
}