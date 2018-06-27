<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * @package App
 * @property integer player_id
 * @property string job
 * @property integer status
 */
class Task extends Model
{
    public const TASK_STATUS_ACTIVE              = 1;
    public const TASK_STATUS_CANCELLATON_PENDING = 2;
    public const TASK_STATUS_CANCELED            = 3;
    public const TASK_STATUS_DONE                = 4;

    public $statuses = [
        self::TASK_STATUS_ACTIVE              => 'Active',
        self::TASK_STATUS_CANCELLATON_PENDING => 'Cancellation Pending',
        self::TASK_STATUS_CANCELED            => 'Canceled',
        self::TASK_STATUS_DONE                => 'Done'
    ];

    public function isActive()
    {
        return $this->status === self::TASK_STATUS_ACTIVE;
    }

    public function isCanceled()
    {
        return $this->status === self::TASK_STATUS_CANCELED;
    }

    public function isCancelationPending()
    {
        return $this->status === self::TASK_STATUS_CANCELLATON_PENDING;
    }

    public function getStatusName()
    {
        return $this->statuses[$this->status];
    }

}
