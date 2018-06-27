<?php

namespace App;

use App\Tasks\CollectPlants;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * @package App
 * @property integer player_id
 * @property string job
 * @property integer status
 * @property string job_name
 * @property integer space_id
 */
class Task extends Model
{
    public const TASK_STATUS_ACTIVE = 1;
    public const TASK_STATUS_CANCELLATON_PENDING = 2;
    public const TASK_STATUS_CANCELED = 3;
    public const TASK_STATUS_DONE = 4;

    private $jobObject = null;

    public $statuses = [
        self::TASK_STATUS_ACTIVE => 'Active',
        self::TASK_STATUS_CANCELLATON_PENDING => 'Cancellation Pending',
        self::TASK_STATUS_CANCELED => 'Canceled',
        self::TASK_STATUS_DONE => 'Done'
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

    /**
     * @return CollectPlants|null
     */
    private function getJobObject()
    {
        if (null === $this->jobObject) {
            $this->jobObject = unserialize($this->job);
        }
        return $this->jobObject;
    }

    public function getJobName()
    {
        $jobObject = $this->getJobObject();
        return $jobObject->getName() . '(plant: "' . $jobObject->productToSeed->getName() . '")';
    }

}
