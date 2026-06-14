<?php

namespace App\Models\Admin\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReq extends Model
{
    use HasFactory;

    protected $table = 'mis_material_reqs';
    protected $primaryKey = 'material_req_id';

    protected $fillable = [
        'user_id',
        'module',
        'requested_by',
        'request_date',
        'request_details',
        'status',
        'approved_by_manager',
        'manager_approved_at',
        'approved_by_director',
        'director_approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_manager');
    }

    public function director()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_director');
    }

    public function rejector()
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }

    /**
     * Check if the user can approve the request based on its current status.
     */
    public function canBeApprovedBy($user)
    {
        if ($user->position === 'Super Admin') {
            return true;
        }

        switch ($this->status) {
            case 'to submit':
                return $this->user_id === $user->id;
            case 'pending approval':
                return in_array($user->position, ['Manager', 'MIS Supervisor']);
            case 'Pending Final Approval':
                return $user->position === 'Director';
            default:
                return false;
        }
    }

    /**
     * Get the next status in the approval workflow.
     */
    public function getNextApprovalStatus()
    {
        $flow = [
            'to submit' => 'pending approval',
            'pending approval' => 'Pending Final Approval',
            'Pending Final Approval' => 'forwarded to accounting',
            'forwarded to accounting' => 'received',
        ];

        return $flow[$this->status] ?? null;
    }

    /**
     * Get the database field name for tracking the approver at the current stage.
     */
    public function getApproverFieldForCurrentStatus()
    {
        $mapping = [
            'pending approval' => 'approved_by_manager',
            'Pending Final Approval' => 'approved_by_director',
        ];

        return $mapping[$this->status] ?? null;
    }

    /**
     * Get the timestamp field name for the current stage.
     */
    public function getTimestampFieldForCurrentStatus()
    {
        $mapping = [
            'pending approval' => 'manager_approved_at',
            'Pending Final Approval' => 'director_approved_at',
        ];

        return $mapping[$this->status] ?? null;
    }
}
