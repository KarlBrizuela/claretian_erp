<?php

namespace Tests\Unit;

use App\Models\StockTransfer;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class StockTransferTest extends TestCase
{
    public function test_accounting_staff_cannot_see_pending_stock_transfer_until_marketing_approval(): void
    {
        $transfer = new StockTransfer(['status' => 'pending']);
        $user = new User([
            'division' => 'Admin & Finance Division',
            'department' => 'Finance',
            'position' => 'Finance Staff',
        ]);

        $this->assertFalse($transfer->canBeReviewedByAccounting($user));
    }

    public function test_accounting_staff_can_see_stock_transfer_after_marketing_approval(): void
    {
        $transfer = new StockTransfer(['status' => 'accounting_review']);
        $user = new User([
            'division' => 'Admin & Finance Division',
            'department' => 'Finance',
            'position' => 'Finance Staff',
        ]);

        $this->assertTrue($transfer->canBeReviewedByAccounting($user));
    }

    public function test_production_cannot_see_stock_transfer_until_admin_and_finance_review(): void
    {
        $pendingTransfer = new StockTransfer(['status' => 'pending']);
        $reviewedTransfer = new StockTransfer(['status' => 'logistics_assignment']);

        $this->assertFalse($pendingTransfer->canBeSeenByProduction());
        $this->assertTrue($reviewedTransfer->canBeSeenByProduction());
    }
}
