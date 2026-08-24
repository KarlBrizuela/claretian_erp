<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ExtendedChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            // ASSETS
            ['code' => '1000', 'name' => 'Cash in Bank', 'type' => 'Asset', 'category' => 'Cash & Bank'],
            ['code' => '1010', 'name' => 'Cash in Bank (Other)', 'type' => 'Asset', 'category' => 'Cash & Bank'],
            ['code' => '1020', 'name' => 'Petty Cash Fund', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1030', 'name' => 'Undeposited Funds', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1040', 'name' => 'Cash on Hand', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1200', 'name' => 'Trade/Accounts Receivable', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1300', 'name' => 'Inventory - Books', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1310', 'name' => 'Inventory - Consignment', 'type' => 'Asset', 'category' => 'Current Asset'],
            ['code' => '1600', 'name' => 'Fixed Assets', 'type' => 'Asset', 'category' => 'Non-Current Asset'],
            ['code' => '1700', 'name' => 'Investments', 'type' => 'Asset', 'category' => 'Non-Current Asset'],
            ['code' => '1800', 'name' => 'Deposits', 'type' => 'Asset', 'category' => 'Current Asset'],

            // BANK ACCOUNTS
            ['code' => 'BANK-BDO-101', 'name' => 'BDO Unibank', 'type' => 'Asset', 'category' => 'Cash & Bank'],
            ['code' => 'BANK-BPI-102', 'name' => 'BPI', 'type' => 'Asset', 'category' => 'Cash & Bank'],
            ['code' => 'BANK-GCASH-103', 'name' => 'GCash / Merchant E-Wallet', 'type' => 'Asset', 'category' => 'Cash & Bank'],

            // LIABILITIES
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2100', 'name' => 'Withholding Tax Payable', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2200', 'name' => 'Payables', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2300', 'name' => 'Loans', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2400', 'name' => 'Government Contributions', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2500', 'name' => 'Customer Deposits', 'type' => 'Liability', 'category' => 'Current Liability'],
            ['code' => '2600', 'name' => 'Unearned Revenue', 'type' => 'Liability', 'category' => 'Current Liability'],

            // EQUITY
            ['code' => '3000', 'name' => 'Retained Earnings', 'type' => 'Equity', 'category' => 'Equity'],
            ['code' => '3100', 'name' => 'Capital', 'type' => 'Equity', 'category' => 'Equity'],

            // INCOME
            ['code' => '4000', 'name' => 'Sales - Books', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4010', 'name' => 'Sales - Consignment', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4020', 'name' => 'Royalties', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4030', 'name' => 'Rights Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4040', 'name' => 'Licensing', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4060', 'name' => 'E-books', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4100', 'name' => 'Sales Discount', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'Income', 'category' => 'Other Income'],
            ['code' => '4300', 'name' => 'Printing Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4310', 'name' => 'Layout Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4320', 'name' => 'Design Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4330', 'name' => 'Binding', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4340', 'name' => 'Lamination', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4400', 'name' => 'POS Sales', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4410', 'name' => 'Sales Orders (SO)', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4420', 'name' => 'E-Commerce Direct', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4430', 'name' => 'Direct Sales', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4440', 'name' => 'Area Sales', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4450', 'name' => 'COB Sales', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4460', 'name' => 'Lazada', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4470', 'name' => 'Shopee', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4480', 'name' => 'Tiktok', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4490', 'name' => 'Facebook', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4500', 'name' => 'Wholesale', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4510', 'name' => 'Export', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4520', 'name' => 'Claret Media', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4600', 'name' => 'Cash Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4610', 'name' => 'E-Wallet Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4620', 'name' => 'Bank & Check Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4630', 'name' => 'Card Income', 'type' => 'Income', 'category' => 'Revenue'],
            ['code' => '4700', 'name' => 'Donations', 'type' => 'Income', 'category' => 'Other Income'],
            ['code' => '4710', 'name' => 'Grants', 'type' => 'Income', 'category' => 'Other Income'],
            ['code' => '4720', 'name' => 'Investments', 'type' => 'Income', 'category' => 'Other Income'],
            ['code' => '4730', 'name' => 'Interest Income', 'type' => 'Income', 'category' => 'Other Income'],
            ['code' => '4740', 'name' => 'Rental Income', 'type' => 'Income', 'category' => 'Other Income'],

            // EXPENSES
            ['code' => '5000', 'name' => 'Cost of Sales', 'type' => 'Expense', 'category' => 'COGS'],
            ['code' => '5100', 'name' => 'Complimentary & Donation Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6000', 'name' => 'Supplies Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6010', 'name' => 'Travel & Transportation', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6020', 'name' => 'Communication Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6030', 'name' => 'Representation Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6100', 'name' => 'Fixed Assets Expense', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6200', 'name' => 'Operational Expenses', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6300', 'name' => 'Payroll & Administrative', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6400', 'name' => 'Utilities & Communication', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6500', 'name' => 'Marketing & Distribution', 'type' => 'Expense', 'category' => 'Operating Expense'],
            ['code' => '6600', 'name' => 'Petty Cash Disbursements', 'type' => 'Expense', 'category' => 'Operating Expense'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
