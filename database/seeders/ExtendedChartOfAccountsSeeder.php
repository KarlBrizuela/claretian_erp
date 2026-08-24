<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExtendedChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Parent / Group Accounts (is_postable = 0)
        $parentAccounts = [
            // Asset Parents
            ['code' => '1005', 'name' => 'Bank Accounts', 'type' => 'Asset', 'category' => 'Cash & Bank', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 10],
            ['code' => '1305', 'name' => 'Inventory', 'type' => 'Asset', 'category' => 'Current Asset', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 20],
            // Equity Parents
            ['code' => '3200', 'name' => 'Current Year Income', 'type' => 'Equity', 'category' => 'Equity', 'is_postable' => 0, 'normal_balance' => 'Credit', 'display_order' => 30],
            // Income Parents
            ['code' => '4005', 'name' => 'Publishing', 'type' => 'Income', 'category' => 'Revenue', 'is_postable' => 0, 'normal_balance' => 'Credit', 'display_order' => 10],
            ['code' => '4305', 'name' => 'Printing Services', 'type' => 'Income', 'category' => 'Revenue', 'is_postable' => 0, 'normal_balance' => 'Credit', 'display_order' => 20],
            ['code' => '4405', 'name' => 'Marketing', 'type' => 'Income', 'category' => 'Revenue', 'is_postable' => 0, 'normal_balance' => 'Credit', 'display_order' => 30],
            ['code' => '4705', 'name' => 'Other Income', 'type' => 'Income', 'category' => 'Other Income', 'is_postable' => 0, 'normal_balance' => 'Credit', 'display_order' => 40],
            // Expense Parents
            ['code' => '5005', 'name' => 'Production', 'type' => 'Expense', 'category' => 'COGS', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 10],
            ['code' => '5105', 'name' => 'Administration', 'type' => 'Expense', 'category' => 'Operating Expense', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 20],
            ['code' => '5205', 'name' => 'Marketing Expenses', 'type' => 'Expense', 'category' => 'Operating Expense', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 30],
            ['code' => '5305', 'name' => 'PRD', 'type' => 'Expense', 'category' => 'Operating Expense', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 40],
            ['code' => '5405', 'name' => 'Warehouse', 'type' => 'Expense', 'category' => 'Operating Expense', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 50],
            ['code' => '5505', 'name' => 'MIS', 'type' => 'Expense', 'category' => 'Operating Expense', 'is_postable' => 0, 'normal_balance' => 'Debit', 'display_order' => 60],
        ];

        foreach ($parentAccounts as $account) {
            ChartOfAccount::updateOrCreate(
                ['code' => $account['code']],
                array_merge($account, ['is_active' => 1])
            );
        }

        // Helper to resolve parent ID dynamically
        $getParentId = function($code) {
            $p = ChartOfAccount::where('code', $code)->first();
            return $p ? $p->id : null;
        };

        // 2. Seed Child / Posting Accounts (is_postable = 1)
        $childAccounts = [
            // ASSETS
            ['code' => '1010', 'name' => 'Cash on Hand', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 1],
            ['code' => '1015', 'name' => 'Petty Cash', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 2],
            ['code' => '1000', 'name' => 'BDO Unibank', 'type' => 'Asset', 'category' => 'Cash & Bank', 'parent_code' => '1005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 3],
            ['code' => '1006', 'name' => 'BPI', 'type' => 'Asset', 'category' => 'Cash & Bank', 'parent_code' => '1005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 4],
            ['code' => '1020', 'name' => 'E-Wallet', 'type' => 'Asset', 'category' => 'Cash & Bank', 'parent_code' => '1005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 5],
            ['code' => '1025', 'name' => 'Other Bank Account', 'type' => 'Asset', 'category' => 'Cash & Bank', 'parent_code' => '1005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 6],
            ['code' => '1200', 'name' => 'Receivables', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 7],
            ['code' => '1320', 'name' => 'Raw Materials', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => '1305', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 8],
            ['code' => '1330', 'name' => 'Work in Progress', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => '1305', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 9],
            ['code' => '1300', 'name' => 'Finished Goods', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => '1305', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 10],
            ['code' => '1600', 'name' => 'Fixed Assets', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 11],
            ['code' => '1700', 'name' => 'Investments', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 12],
            ['code' => '1800', 'name' => 'Deposits', 'type' => 'Asset', 'category' => 'Asset', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 13],

            // LIABILITIES
            ['code' => '2000', 'name' => 'Suppliers', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 1],
            ['code' => '2200', 'name' => 'Payables', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 2],
            ['code' => '2300', 'name' => 'Loans', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 3],
            ['code' => '2100', 'name' => 'Taxes', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 4],
            ['code' => '2400', 'name' => 'Government Contributions', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 5],
            ['code' => '2500', 'name' => 'Customer Deposits', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 6],
            ['code' => '2600', 'name' => 'Unearned Revenue', 'type' => 'Liability', 'category' => 'Liability', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 7],

            // EQUITY
            ['code' => '3100', 'name' => 'Capital', 'type' => 'Equity', 'category' => 'Equity', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 1],
            ['code' => '3000', 'name' => 'Retained Earnings', 'type' => 'Equity', 'category' => 'Equity', 'parent_code' => null, 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 2],

            // INCOME
            // Publishing
            ['code' => '4000', 'name' => 'Book Sales', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4005', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 1],
            ['code' => '4020', 'name' => 'Royalties', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4005', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 2],
            ['code' => '4030', 'name' => 'Rights Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4005', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 3],
            ['code' => '4040', 'name' => 'Licensing', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4005', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 4],
            ['code' => '4060', 'name' => 'E-books', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4005', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 5],
            // Printing Services
            ['code' => '4300', 'name' => 'Printing Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4305', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 6],
            ['code' => '4310', 'name' => 'Layout Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4305', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 7],
            ['code' => '4320', 'name' => 'Design Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4305', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 8],
            ['code' => '4330', 'name' => 'Binding', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4305', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 9],
            ['code' => '4340', 'name' => 'Lamination', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4305', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 10],
            // Marketing
            ['code' => '4430', 'name' => 'Direct Sales', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 11],
            ['code' => '4440', 'name' => 'Area Sales', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 12],
            ['code' => '4450', 'name' => 'COB Sales', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 13],
            ['code' => '4460', 'name' => 'Lazada', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 14],
            ['code' => '4470', 'name' => 'Shopee', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 15],
            ['code' => '4480', 'name' => 'TikTok', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 16],
            ['code' => '4490', 'name' => 'Facebook', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 17],
            ['code' => '4500', 'name' => 'Wholesale', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 18],
            ['code' => '4510', 'name' => 'Export', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 19],
            ['code' => '4520', 'name' => 'Claret Media', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4405', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 20],
            // Other Income
            ['code' => '4700', 'name' => 'Donations', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4705', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 21],
            ['code' => '4710', 'name' => 'Grants', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4705', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 22],
            ['code' => '4720', 'name' => 'Investment Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4705', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 23],
            ['code' => '4730', 'name' => 'Interest Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4705', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 24],
            ['code' => '4740', 'name' => 'Rental Income', 'type' => 'Income', 'category' => 'Income', 'parent_code' => '4705', 'is_postable' => 1, 'normal_balance' => 'Credit', 'display_order' => 25],

            // EXPENSES
            // Production
            ['code' => '5010', 'name' => 'Paper', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 2],
            ['code' => '5020', 'name' => 'Ink', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 3],
            ['code' => '5030', 'name' => 'Plates', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 4],
            ['code' => '5040', 'name' => 'Binding', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 5],
            ['code' => '5050', 'name' => 'Glue', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 6],
            ['code' => '5060', 'name' => 'Lamination', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 7],
            ['code' => '5070', 'name' => 'Outsourcing', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 8],
            ['code' => '5080', 'name' => 'Freight', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5005', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 9],
            // Administration
            ['code' => '5110', 'name' => 'Salaries', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5105', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 10],
            ['code' => '5120', 'name' => 'Benefits', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5105', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 11],
            ['code' => '5130', 'name' => 'Utilities', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5105', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 12],
            ['code' => '5140', 'name' => 'Office Supplies', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5105', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 13],
            ['code' => '5150', 'name' => 'Repairs', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5105', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 14],
            // Marketing Expenses
            ['code' => '5210', 'name' => 'Advertising', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5205', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 15],
            ['code' => '5220', 'name' => 'Events', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5205', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 16],
            ['code' => '5230', 'name' => 'Promotions', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5205', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 17],
            ['code' => '5240', 'name' => 'Online Ads', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5205', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 18],
            // PRD
            ['code' => '5310', 'name' => 'International Freight', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5305', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 19],
            ['code' => '5320', 'name' => 'Customs', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5305', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 20],
            ['code' => '5330', 'name' => 'Rights Acquisition', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5305', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 21],
            // Warehouse
            ['code' => '5410', 'name' => 'Storage', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5405', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 22],
            ['code' => '5420', 'name' => 'Delivery', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5405', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 23],
            ['code' => '5430', 'name' => 'Packaging', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5405', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 24],
            // MIS
            ['code' => '5510', 'name' => 'Equipment', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5505', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 25],
            ['code' => '5520', 'name' => 'Utilities', 'type' => 'Expense', 'category' => 'Expense', 'parent_code' => '5505', 'is_postable' => 1, 'normal_balance' => 'Debit', 'display_order' => 26],
        ];

        foreach ($childAccounts as $account) {
            $parentCode = $account['parent_code'];
            unset($account['parent_code']);
            $account['parent_id'] = $parentCode ? $getParentId($parentCode) : null;
            
            ChartOfAccount::updateOrCreate(
                ['code' => $account['code']],
                array_merge($account, ['is_active' => 1])
            );
        }
    }
}
