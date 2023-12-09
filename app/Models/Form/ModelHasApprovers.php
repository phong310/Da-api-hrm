<?php

namespace App\Models\Form;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasApprovers extends Model
{
    use HasFactory;

    const STATUS = [
        'PROCESSING' => 0,
        'APPROVED' => 1,
        'REJECTED' => 2,
    ];

    const ACTION_TIME = [
        ModelHasApprovers::STATUS['APPROVED'] => 'approval_time',
        ModelHasApprovers::STATUS['REJECTED'] => 'rejected_time',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'model_id',
        'approve_employee_id',
        'status',
        'model_type',
        'approval_time',
        'rejected_time',
    ];
    /**
     * @var string
     */
    protected $table = 'model_has_approvers';

    /**
     * @var bool
     */
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'approve_employee_id', 'id');
    }
}
