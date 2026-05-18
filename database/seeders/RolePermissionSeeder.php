<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Define Common Permissions
        $marketingPerms = [
            'marketing.dashboard',
            'marketing.customers',
            'marketing.area_sales',
            'marketing.direct_sales',
            'marketing.ads_promo',
            'marketing.ecom',
            'marketing.book_mgmt',
            'marketing.supplier_mgmt',
            'marketing.approval_queue',
            'marketing.my_requests'
        ];

        $productionPerms = [
            'production.dashboard',
            'production.inventory',
            'production.logistic',
            'production.dto',
            'production.ford',
            'production.printing',
            'production.approval_queue',
            'production.my_requests'
        ];

        $adminFinancePerms = [
            'admin_finance.dashboard',
            'admin_finance.mis',
            'admin_finance.gsd',
            'admin_finance.credit_collection',
            'admin_finance.accounting',
            'admin_finance.expense_management',
            'admin_finance.hr',
            'admin_finance.approval_queue',
            'admin_finance.my_requests'
        ];

        // 2. Create Roles with Permissions
        
        // COMMON / MISC
        $this->createRole('Super Admin', 'All Divisions', array_merge($adminFinancePerms, $marketingPerms, $productionPerms));
        $this->createRole('Director', 'All Divisions', array_merge($adminFinancePerms, $marketingPerms, $productionPerms));
        $this->createRole('Manager', 'All Divisions', array_merge($adminFinancePerms, $marketingPerms));

        // ADMIN AND FINANCE
        $this->createRole('MIS Super Admin', 'Admin & Finance Division', array_merge($adminFinancePerms, $marketingPerms, $productionPerms));
        $this->createRole('A&F Manager', 'Admin & Finance Division', array_merge($adminFinancePerms, ['admin_finance.dashboard']));
        
        // Accounting
        $accountingStaffPerms = ['admin_finance.dashboard', 'admin_finance.accounting', 'admin_finance.approval_queue', 'admin_finance.my_requests'];
        $this->createRole('Senior Accounting Staff', 'Admin & Finance Division', $accountingStaffPerms);
        $this->createRole('Accounting Staff', 'Admin & Finance Division', $accountingStaffPerms);
        $this->createRole('Inventory Staff', 'Admin & Finance Division', $accountingStaffPerms);
        $this->createRole('Cashier', 'Admin & Finance Division', $accountingStaffPerms);

        // Credit and Collection
        $ccStaffPerms = ['admin_finance.dashboard', 'admin_finance.credit_collection', 'admin_finance.approval_queue', 'admin_finance.my_requests'];
        $this->createRole('Credit and Collection Staff', 'Admin & Finance Division', $ccStaffPerms);
        $this->createRole('Senior Inventory and Collection Staff', 'Admin & Finance Division', $ccStaffPerms);
        $this->createRole('Inventory and Collection Staff', 'Admin & Finance Division', $ccStaffPerms);
        $this->createRole('Billing Staff', 'Admin & Finance Division', $ccStaffPerms);

        // MIS / GSD / HR
        $this->createRole('MIS Supervisor', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.mis', 'admin_finance.my_requests']);
        $this->createRole('MIS Staff', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.mis', 'admin_finance.my_requests']);
        $this->createRole('GSD Supervisor', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.gsd', 'admin_finance.my_requests']);
        $this->createRole('HR Staff', 'Admin & Finance Division', ['admin_finance.dashboard', 'admin_finance.hr', 'admin_finance.my_requests']);

        // MARKETING
        $this->createRole('Marketing Manager', 'Marketing Division', array_merge($marketingPerms, ['marketing.dashboard']));
        $this->createRole('Marketing Coordinator', 'Marketing Division', ['marketing.dashboard', 'marketing.area_sales', 'marketing.customers', 'marketing.my_requests']);
        $this->createRole('Senior Supervisor', 'Marketing Division', ['marketing.dashboard', 'marketing.area_sales', 'marketing.customers', 'marketing.my_requests']);
        $this->createRole('Account Executive', 'Marketing Division', ['marketing.dashboard', 'marketing.area_sales', 'marketing.customers', 'marketing.my_requests']);
        
        $directSalesPerms = ['marketing.dashboard', 'marketing.direct_sales', 'marketing.customers', 'marketing.my_requests'];
        $this->createRole('Bookstore Staff', 'Marketing Division', $directSalesPerms);
        $this->createRole('Bookschain Staff', 'Marketing Division', $directSalesPerms);
        $this->createRole('Booksales Staff', 'Marketing Division', $directSalesPerms);
        $this->createRole('Marketing Staff', 'Marketing Division', ['marketing.dashboard', 'marketing.ads_promo', 'marketing.my_requests']);

        // PRODUCTION
        $this->createRole('DTO Supervisor', 'Production Division', ['production.dashboard', 'production.dto', 'production.inventory', 'production.my_requests']);
        
        $fordPerms = ['production.dashboard', 'production.ford', 'production.approval_queue', 'production.my_requests'];
        $this->createRole('Senior Ford Staff', 'Production Division', $fordPerms);
        $this->createRole('Ford Staff', 'Production Division', $fordPerms);
        
        $logisticsPerms = ['production.dashboard', 'production.logistic', 'production.inventory', 'production.my_requests'];
        $this->createRole('Senior Logistics Staff', 'Production Division', $logisticsPerms);
        $this->createRole('Logistics Staff', 'Production Division', $logisticsPerms);
        
        $this->createRole('Printing Services Staff', 'Production Division', ['production.dashboard', 'production.printing', 'production.my_requests']);

    }

    private function createRole($name, $division, $permissions)
    {
        Role::updateOrCreate(
            ['name' => $name],
            [
                'division' => $division,
                'permissions' => $permissions,
                'is_active' => true,
                'description' => $name . ' role in ' . $division
            ]
        );
    }
}
