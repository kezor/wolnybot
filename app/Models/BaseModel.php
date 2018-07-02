<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getActivities()
    {
        return Activity::where('entity_id', $this->id)
            ->where('class_name', get_class($this))
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();
    }
}