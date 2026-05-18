<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'middle_initial',
        'email',
        'password',
        'plain_password',
        'division',
        'department',
        'position',
        'permissions',
        'status',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_initial} {$this->last_name}");
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

	public function profile()
	{
		return $this->hasOne(UserProfile::class);
	}

	public function divisions()
	{
		return $this->hasMany(UserDivision::class);
	}

    /**
     * Check if the user is a Super Admin.
     * 
     * @return bool
     */
    public function isSuperAdmin()
    {
        // Check by position
        if ($this->position === 'Super Admin') {
            return true;
        }

        // List of authorized super admin emails
        $superAdminEmails = [
            'admin@claretianpublications.ph',
            'director@claretianpublications.ph',
            'admin@intra-code.com',
            'admin@clarentian.com', // Added from live server logs
        ];

        return in_array($this->email, $superAdminEmails);
    }

    /**
     * Check if user has specific permission.
     * 
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // 1. Check user-level permissions first (Manual override)
        // We only use the manual override if it's NOT NULL.
        // If it's an array (including empty []), it means you manually set their access.
        if (!is_null($this->permissions)) {
            return in_array($permission, $this->permissions);
        }

        // 2. Fallback to role-level permissions if permissions column is NULL
        $role = Role::where('name', $this->position)->first();
        if (!$role || !$role->is_active || !$role->permissions || !is_array($role->permissions)) {
            return false;
        }

        return in_array($permission, $role->permissions);
    }

    /**
     * Get the first available route name for the user based on their permissions.
     */
    public function getFirstAvailableRoute()
    {
        if ($this->isSuperAdmin()) {
            return 'dashboard';
        }

        // Define route priority (matches sidebar order)
        $priorities = [
            // Dashboards
            'marketing.dashboard' => 'marketing.dashboard',
            'production.dashboard' => 'production.dashboard',
            'admin_finance.dashboard' => 'admin-finance.dashboard',

            // Common Tools
            'marketing.my_requests' => 'marketing.my-requests',
            'production.my_requests' => 'production.my-requests',
            'admin_finance.my_requests' => 'admin-finance.my-requests',
            'marketing.approval_queue' => 'marketing.approval-queue',
            'production.approval_queue' => 'production.approval-queue',
            'admin_finance.approval_queue' => 'admin-finance.approval-queue',

            // Marketing
            'marketing.customers' => 'marketing.customers',
            'marketing.area_sales' => 'marketing.sales-orders.list',
            'marketing.direct_sales' => 'marketing.pos.sale',
            'marketing.ads_promo' => 'marketing.ads-promo.crpr',
            'marketing.ecom' => 'marketing.ecom.pos',
            'marketing.book_mgmt' => 'marketing.products',
            'marketing.supplier_mgmt' => 'marketing.suppliers',

            // Production
            'production.inventory' => 'production.inventory.overview',
            'production.logistic' => 'production.logistic.pick-list-list',
            'production.ford' => 'production.ford.client-payment-posting',
            'production.printing' => 'production.printing.request-payment-to-printer',

            // Admin & Finance
            'admin_finance.accounting' => 'admin-finance.accounting.sales-invoice',
            'admin_finance.credit_collection' => 'admin-finance.credit-collection.billing',
            'admin_finance.gsd' => 'admin-finance.gsd.asset-management',
            'admin_finance.mis' => 'admin-finance.mis.job-orders',
            'admin_finance.hr' => 'admin-finance.hr.job-orders',
        ];

        foreach ($priorities as $perm => $route) {
            if ($this->hasPermission($perm)) {
                return $route;
            }
        }

        return 'profile';
    }

    /**
     * Override delete to modify unique columns so they can be reused by new users.
     */
    public function delete()
    {
        // Prevent modifying it multiple times if soft deleted again
        if (!$this->trashed()) {
            $this->employee_number = $this->employee_number . '_del_' . $this->id;
            $this->email = $this->email . '_del_' . $this->id;
            $this->save();
        }

        return parent::delete();
    }
}
