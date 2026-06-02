# COD Payment System - Quick Reference Guide

## System Overview

Your COD payment system is now live with 3 checkpoints:

```
DELIVERY → COLLECTION → VERIFICATION
   ↓           ↓           ↓
Rider    Hands Over    Cashier
         to Cashier    Verifies
```

---

## For Riders

### Access Point
- URL: `/rider/collections`
- Shows: Pending & collected orders

### Workflow
1. **View Pending Collections** - See orders to deliver & collect
2. **Record Collection** - After customer pays:
   - Enter amount received
   - Add notes (if needed)
   - Upload customer signature/proof photo
   - Click "Record Collection"
3. **Hand Over to Cashier** - When ready:
   - Click "Mark as Handed Over"
   - Cashier will verify the payment

### Key Info
- Amount to collect is pre-filled
- You can upload photo proof
- Daily summary available at `/rider/collections/daily/summary`

---

## For Cashier (Admin & Finance)

### Access Point
- URL: `/admin-finance/cashier/collections`
- Shows: All collections awaiting verification

### Workflow
1. **Review Collection** - Click "Verify" button
   - See all order details & items
   - View rider-collected amount
   - Check for discrepancies
2. **Verify Payment**:
   - Enter amount received from rider (usually matches rider's amount)
   - If discrepancy exists, add explanation
   - Upload verification photo (optional)
   - Click "Approve & Record"
3. **Reject if Issues** - Use "Reject" button to send back to rider

### Key Features
- Auto-detects discrepancies
- Posts to General Ledger automatically
- Can export for accounting
- Daily report available

---

## Database Tables

### rider_collections
Tracks each rider's cash collection:
- `sales_order_id` - Links to Sales Order
- `rider_id` - Driver who collected
- `amount_to_collect` - Expected amount
- `amount_collected` - Actual amount
- `status` - pending → collected → handed_over → verified

### sales_orders (new fields)
- `transaction_type` - COD, Credit, Prepaid, etc.
- `collection_status` - pending_collection → reconciled

### payments (updated)
- `rider_collection_id` - Links to collection
- `collected_by` - Rider ID
- `handed_over_by` - Cashier ID
- `verified_by` - Accounting ID

---

## API Endpoints

### Rider Routes
```
GET  /rider/collections                    - List collections
GET  /rider/collections/{id}               - View collection
POST /rider/collections/{id}/record        - Record collection
POST /rider/collections/{id}/hand-over     - Mark handed over
GET  /rider/collections/daily/summary      - Daily summary
```

### Cashier Routes
```
GET  /admin-finance/cashier/collections           - List collections
GET  /admin-finance/cashier/collections/{id}      - View collection
POST /admin-finance/cashier/collections/{id}/verify  - Verify payment
POST /admin-finance/cashier/collections/{id}/reject  - Reject collection
GET  /admin-finance/cashier/daily-report          - Daily report
GET  /admin-finance/cashier/export                - Export for accounting
```

---

## Creating a COD Sales Order

When creating a Sales Order:

1. Set **Transaction Type** = `COD`
2. Assign **Rider** to delivery
3. System automatically creates RiderCollection record
4. SO → `Rider Collected Cash` → `Handed Over` → `Verified` → `Reconciled`

---

## Discrepancy Handling

If amount collected ≠ amount received:

1. Cashier sees **red alert** showing difference
2. Cashier adds **explanation** (short payment, overpayment, etc.)
3. Still approves payment with **notes for audit**
4. Difference tracked in `amount_discrepancy` field
5. Finance can review discrepancies in daily report

---

## Integration with Accounting

When cashier verifies payment:

✅ Payment record created
✅ GL post automatically done:
   - Dr: Cash/Bank account (increases)
   - Cr: Accounts Receivable (decreases)
✅ SO marked as "paid"
✅ Can be exported for reconciliation

---

## Troubleshooting

**Rider Collection Not Showing:**
- Check that SO has `transaction_type = 'COD'`
- Verify `driver_id` is assigned
- RiderCollection should be auto-created

**Discrepancy Tracking:**
- Check `amount_discrepancy` in rider_collections table
- Review notes in `discrepancy_notes` field

**GL Not Posting:**
- Verify cashier has `admin_finance.accounting` permission
- Check AccountingService is working
- Review system logs for errors

---

## Statistics & Reporting

**Rider Dashboard Shows:**
- Pending collections count
- Collected count  
- Total to collect
- Total collected today

**Cashier Dashboard Shows:**
- Awaiting verification count
- Total pending amount
- Verified today count
- Total verified today amount

**Export CSV:**
- Date, SO Number, Customer, Rider, Amount, Discrepancy, Status
- Ready for accounting import

---

## Status Codes

| Status | Meaning | Actor |
|--------|---------|-------|
| pending | Awaiting rider delivery | System |
| collected | Rider collected cash | Rider |
| handed_over | Rider handed to cashier | Rider |
| verified | Cashier verified & recorded | Cashier |
| reconciled | GL posted, AR updated | System |

---

## File Locations

### Controllers
- `app/Http/Controllers/RiderCollectionController.php`
- `app/Http/Controllers/Accounting/CashierPaymentController.php`

### Models
- `app/Models/RiderCollection.php`

### Views
- `resources/views/rider/collections/index.blade.php`
- `resources/views/rider/collections/show.blade.php`
- `resources/views/accounting/cashier/collections-index.blade.php`
- `resources/views/accounting/cashier/collection-show.blade.php`

### Routes
- Added in `routes/web.php` (bottom section)

---

## Next Steps

1. ✅ Database tables created
2. ✅ Models & Controllers implemented
3. ✅ Routes configured
4. ✅ Views created
5. ⏳ Test the workflow:
   - Create a COD sales order
   - Record collection as rider
   - Verify as cashier
   - Check GL posting

6. ⏳ Add permissions check in role seeder if needed
7. ⏳ Update SO creation form to include transaction_type & rider selection
