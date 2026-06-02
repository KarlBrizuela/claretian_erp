# COD Payment System - Visual Workflow

## Complete Payment Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SALES ORDER CREATION WITH COD TYPE                        │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  Marketing Manager Creates SO                                               │
│         ↓                                                                    │
│  Selects "Transaction Type = COD"                                           │
│         ↓                                                                    │
│  Assigns Rider/Driver to Delivery                                           │
│         ↓                                                                    │
│  System AUTOMATICALLY creates:                                              │
│    • RiderCollection record (status: pending)                               │
│    • Sets SO.transaction_type = 'COD'                                       │
│    • Sets SO.collection_status = 'pending_collection'                       │
│    • Links rider via driver_id                                              │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│                         CHECKPOINT 1: RIDER COLLECTS                         │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  [RIDER] Accesses /rider/collections                                        │
│         ↓                                                                    │
│  [RIDER] Views Pending Collections Dashboard                                │
│  ┌─────────────────────────────────────┐                                    │
│  │ Statistics:                         │                                    │
│  │ • Pending: 5                        │                                    │
│  │ • Collected: 3                      │                                    │
│  │ • Handed Over: 1                    │                                    │
│  │ • Total Collected Today: ₱15,000    │                                    │
│  └─────────────────────────────────────┘                                    │
│         ↓                                                                    │
│  [RIDER] Selects Order to Collect                                           │
│         ↓                                                                    │
│  [RIDER] Views: Order Items, Customer, Amount Due                           │
│         ↓                                                                    │
│  [RIDER] Records Collection:                                                │
│    • Enters amount collected                                                │
│    • Adds notes (if any issues)                                             │
│    • Uploads customer signature/proof photo                                 │
│         ↓                                                                    │
│  System Updates:                                                             │
│    • RiderCollection.amount_collected = {amount}                            │
│    • RiderCollection.status = 'collected'                                   │
│    • RiderCollection.collected_at = NOW()                                   │
│    • RiderCollection.customer_signature_photo = {path}                      │
│    • SO.collection_status = 'collected'                                     │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│                      CHECKPOINT 2: RIDER HANDS OVER                          │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  [RIDER] Returns to Office with Cash                                        │
│         ↓                                                                    │
│  [RIDER] Goes Back to Collection Detail Page                                │
│         ↓                                                                    │
│  [RIDER] Clicks "Mark as Handed Over"                                       │
│         ↓                                                                    │
│  System Updates:                                                             │
│    • RiderCollection.status = 'handed_over'                                 │
│    • RiderCollection.handed_over_at = NOW()                                 │
│    • SO.collection_status = 'handed_over'                                   │
│         ↓                                                                    │
│  ✓ Money is now in Cashier's hands                                          │
│  ✓ System waiting for cashier verification                                  │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│                    CHECKPOINT 3: CASHIER VERIFICATION                        │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  [CASHIER] Accesses /admin-finance/cashier/collections                      │
│         ↓                                                                    │
│  [CASHIER] Views Verification Queue                                         │
│  ┌─────────────────────────────────────┐                                    │
│  │ Awaiting Verification: 8             │                                   │
│  │ Total Pending: ₱48,500              │                                    │
│  │ Verified Today: 12                  │                                    │
│  │ Total Verified Today: ₱32,000       │                                    │
│  └─────────────────────────────────────┘                                    │
│         ↓                                                                    │
│  [CASHIER] Clicks "Verify" on Collection                                    │
│         ↓                                                                    │
│  [CASHIER] Views Collection Details:                                        │
│    • Customer name & contact                                                │
│    • SO number & items                                                      │
│    • Expected amount: ₱5,000                                                │
│    • Rider collected: ₱5,000                                                │
│    • Discrepancy: NONE ✓                                                    │
│         ↓                                                                    │
│  [CASHIER] Verifies Amount in Form:                                         │
│    • Amount Received: ₱5,000                                                │
│    • Adds verification note (optional)                                      │
│    • Uploads verification photo (optional)                                  │
│         ↓                                                                    │
│  [CASHIER] Clicks "Approve & Record"                                        │
│         ↓                                                                    │
│  SYSTEM AUTOMATICALLY:                                                       │
│                                                                               │
│  ✓ Step 1: Update RiderCollection                                           │
│    • status = 'verified'                                                    │
│    • verified_at = NOW()                                                    │
│    • verified_by = {cashier_id}                                             │
│                                                                               │
│  ✓ Step 2: Create Payment Record                                            │
│    • Payment.customer_id = {customer}                                       │
│    • Payment.sales_order_id = {so}                                          │
│    • Payment.rider_collection_id = {collection}                             │
│    • Payment.amount = ₱5,000                                                │
│    • Payment.payment_method = 'cod_cash'                                    │
│    • Payment.collected_by = {rider_id}                                      │
│    • Payment.verified_by = {cashier_id}                                     │
│                                                                               │
│  ✓ Step 3: POST TO GENERAL LEDGER                                           │
│    Debit:  1010 - Cash in Bank         ₱5,000                               │
│    Credit: 1200 - Accounts Receivable         ₱5,000                        │
│    Reference: SO#2024-001 - COD Payment from {customer}                     │
│                                                                               │
│  ✓ Step 4: Update Sales Order                                               │
│    • payment_status = 'paid'                                                │
│    • collection_status = 'reconciled'                                       │
│                                                                               │
│  ✓ Step 5: Reduce Customer AR                                               │
│    Customer balance reduced by ₱5,000                                       │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘


SCENARIO: DISCREPANCY DETECTED
───────────────────────────────

What if Rider Collected = ₱4,900 but Expected = ₱5,000?

  [CASHIER] Views Collection
         ↓
  SYSTEM SHOWS:
  ┌──────────────────────────────┐
  │ ⚠️  DISCREPANCY ALERT        │
  │ Expected: ₱5,000            │
  │ Received: ₱4,900            │
  │ Difference: -₱100 (SHORT)   │
  └──────────────────────────────┘
         ↓
  [CASHIER] Enters: "Customer short ₱100"
         ↓
  [CASHIER] Still Clicks "Approve & Record"
         ↓
  SYSTEM RECORDS:
  • amount_discrepancy = -100
  • discrepancy_notes = "Customer short ₱100"
  • Still creates Payment for ₱4,900
  • Still posts to GL for ₱4,900
  • Finance can follow up on shortfall later
         ↓
  AUDIT TRAIL: Discrepancy documented for review


SCENARIO: RIDER REJECTS (COLLECTION NOT HANDED OVER)
──────────────────────────────────────────────────────

What if Cashier finds issues with collection?

  [CASHIER] Reviews Collection
         ↓
  [CASHIER] Finds Receipts Don't Match
         ↓
  [CASHIER] Clicks "Reject"
         ↓
  [CASHIER] Explains: "Receipt count doesn't match rider report"
         ↓
  SYSTEM UPDATES:
  • RiderCollection stays at "handed_over" status
  • Adds note: "REJECTED: Receipt count doesn't match..."
  • SO.collection_status stays "handed_over"
         ↓
  [RIDER] Notified to review collection
         ↓
  [RIDER] Can view rejection reason
  [RIDER] Contacts cashier or supervisor to resolve


DAILY WORKFLOWS
───────────────

RIDER DAILY SUMMARY:
  [RIDER] → /rider/collections/daily/summary
  Shows:
    • Collections completed today: 5
    • Total amount collected: ₱28,500
    • List of all collections with status
    • Can download for personal record

CASHIER DAILY REPORT:
  [CASHIER] → /admin-finance/cashier/daily-report
  Shows:
    • Collections verified today: 12
    • Total verified: ₱45,000
    • Discrepancies found: 2
    • By rider breakdown
    • Can export to CSV for accounting


ACCOUNTING INTEGRATION
─────────────────────

EXPORT FOR ACCOUNTING:
  [CASHIER/SUPERVISOR] → /admin-finance/cashier/export
  Downloads CSV with:
    • Date
    • SO Number
    • Customer Name
    • Rider Name
    • Amount Collected
    • Discrepancy
    • Verified By
    • Status

  This CSV goes to Accounting for:
    • Bank deposit reconciliation
    • AR aging analysis
    • Collection rate analysis
    • Discrepancy investigation


REAL-WORLD EXAMPLES
────────────────────

EXAMPLE 1: Normal COD Order
──────────────────────────
  Day 1, 10:00 AM
    Marketing creates SO#2024-001 for ₱10,000
    Assigns Rider Juan
    System creates RiderCollection (pending)

  Day 1, 2:00 PM
    Juan delivers books to customer
    Customer gives ₱10,000 cash
    Juan records collection with photo
    System: RiderCollection.status = 'collected'

  Day 1, 4:00 PM
    Juan returns to office
    Hands ₱10,000 to cashier Maria
    Juan marks as 'handed_over'

  Day 2, 9:00 AM
    Maria verifies ₱10,000 received from Juan
    No discrepancy
    Clicks "Approve & Record"
    System posts GL entry
    SO marked as paid ✓
    Customer AR reduced by ₱10,000 ✓


EXAMPLE 2: Discrepancy - Short Payment
─────────────────────────────────────
  Customer was supposed to pay ₱5,000
  Customer only paid ₱4,800 (₱200 short)
  
  Juan records ₱4,800 with note: "Customer said will pay next week"
  
  Maria verifies, sees ₱200 shortfall
  Maria notes: "Customer promised payment next week"
  
  System records everything
  Payment for ₱4,800 posted
  SO shows balance due: ₱200
  Finance follows up later ✓


EXAMPLE 3: Discrepancy - Over Payment
──────────────────────────────────────
  Customer was supposed to pay ₱3,000
  Customer accidentally paid ₱3,200 (₱200 extra)
  
  Juan records ₱3,200 with note: "Customer overpaid ₱200"
  
  Maria verifies, sees ₱200 extra
  Maria notes: "Overpayment - customer approved extra"
  
  System records everything
  Payment for ₱3,200 posted
  SO marked as paid
  Customer credit: ₱200 (for next purchase or refund) ✓


SYSTEM DASHBOARD METRICS
─────────────────────────

RIDER METRICS:
  ├─ Pending Collections Today: 8
  ├─ Collected: 5
  ├─ Handed Over: 2
  ├─ Total Amount Collected: ₱42,300
  └─ Success Rate: 95%

CASHIER METRICS:
  ├─ Awaiting Verification: 12
  ├─ Total Pending Amount: ₱58,500
  ├─ Verified Today: 28
  ├─ Total Verified: ₱85,200
  ├─ Average Time to Verify: 15 mins
  ├─ Discrepancies Found: 3
  └─ Error Rate: 2.1%

FINANCE METRICS:
  ├─ COD Collections Posted Today: ₱85,200
  ├─ Collections vs Credit Orders: 60% / 40%
  ├─ Average Collection Value: ₱3,043
  └─ Days Sales Outstanding: 2 days


═══════════════════════════════════════════════════════════════════════════════

KEY SUCCESS FACTORS
═══════════════════

✓ Clear Handoff Points: Rider → Cashier → Accounting
✓ Audit Trail: Every step logged with timestamps
✓ Photo Proof: Rider signature + verification photos
✓ Discrepancy Tracking: No lost money, documented exceptions
✓ Automatic GL Posting: No manual journal entry needed
✓ Real-Time Reporting: Visibility at every stage
✓ Mobile Friendly: Riders can record from field

═══════════════════════════════════════════════════════════════════════════════
