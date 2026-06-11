# Freight Quotation Workflow Implementation - Complete Guide

## Overview
A complete multi-step freight quotation workflow has been implemented allowing marketing to request freight quotes from logistics, logistics to approve and add pricing, and marketing to create sales orders with linked freight quotations.

## Workflow Summary

### Stage 1: Marketing Creates Freight Quotation Request
**URL:** `marketing.freight-quotations.create`  
**Location:** Marketing → Area Sales → Freight Quotations

**What Happens:**
1. Marketing user clicks "New Quotation" in the Freight Quotations tab
2. Fills in:
   - Origin details (contact, address, province)
   - Destination details (contact, address, province)
   - Service mode (Sea, Air, Land, Mixed)
   - Cargo items (quantity, package type, dimensions)
3. Submits and quotation enters **DRAFT** status
4. Quotation is now visible in Logistics → Freight Quotation (Review) tab

### Stage 2: Logistics Reviews and Quotes
**URL:** `production.logistic.pending-freight-quotations` → `production.logistic.show-freight-quotation`  
**Location:** Logistics → Freight Quotation (Review)

**What Happens:**
1. Logistics user sees list of pending quotations from marketing
2. Clicks "Review" to open the quotation
3. Reviews shipment details and cargo items
4. Adds freight pricing:
   - Estimated Freight Charge (₱)
   - Number of Boxes
   - Valuation Insurance % (default: 1%)
   - Handling Fee % (default: 20%)
   - Logistics Notes (optional)
5. Clicks "Approve & Quote" - system calculates total:
   - Total = Estimated Freight + (Estimated Freight × Valuation %) + (Estimated Freight × Handling %)
6. Quotation status changes to **APPROVED** and updates sent back to marketing
7. Logistics can also choose to reject with notes

### Stage 3: Marketing Proceeds to Sales Order
**URL:** `marketing.freight-quotations.proceed-to-so`  
**Location:** Freight Quotation Detail → "Proceed to Sales Order" button

**What Happens:**
1. Marketing sees the approved quotation with freight charges
2. Clicks "Proceed to Sales Order"
3. Opens sales order creation form pre-filled with:
   - Freight quotation information displayed
   - Freight charges pre-calculated and shown separately
4. Marketing fills in:
   - Customer (required)
   - Transaction Type (paid, charge, consignment, COD, evaluation, etc.)
   - Sales order items (products, quantities, prices)
5. System automatically:
   - Adds freight charges to SO total
   - Links freight quotation to sales order (via `sales_order_id` FK)
   - Sets quotation workflow_status to **LINKED_TO_SO**
   - Creates sales order with all items
6. Redirects to the created sales order for review/approval

### Stage 4: Normal Sales Order Processing Continues
- Marketing approves the SO
- Accounting approves the SO
- Picking, delivery, rider collection, etc. proceeds as normal
- Freight quotation is linked throughout the process

## Database Changes

### New Columns Added to `freight_quotations` Table
```sql
- workflow_status (enum: draft, pending_logistics, approved, linked_to_so)
- sales_order_id (FK to sales_orders)
- boxes_count (integer, nullable)
- logistics_notes (text, nullable)
- responded_by (FK to users, nullable)
- responded_at (timestamp, nullable)
```

## Files Created/Modified

### Controllers
- **NEW:** `app/Http/Controllers/Marketing/FreightQuotationController.php`
  - `list()` - Show marketing's freight quotations
  - `create()` - Show create form
  - `store()` - Store new quotation
  - `show()` - View single quotation
  - `proceedToSalesOrder()` - Show SO creation form
  - `createSalesOrderFromQuotation()` - Create SO from quotation
  - `viewLogisticsResponse()` - View logistics response

- **MODIFIED:** `app/Http/Controllers/Production/LogisticController.php`
  - `pendingFreightQuotations()` - List pending quotations for review
  - `showFreightQuotation()` - Show quotation for review
  - `approveFreightQuotation()` - Approve and add pricing
  - `rejectFreightQuotation()` - Reject with reason

### Models
- **MODIFIED:** `app/Models/FreightQuotation.php`
  - Added fillable fields: workflow_status, sales_order_id, boxes_count, logistics_notes, responded_by, responded_at
  - Added relationship: `respondedBy()`
  - Added relationship: `salesOrder()`
  - Updated casts to include responded_at

- **MODIFIED:** `app/Models/SalesOrder.php`
  - Added relationship: `freightQuotation()` (hasOne)

### Views - Marketing
- **NEW:** `resources/views/marketing/freight-quotations/create.blade.php`
  - Form to create new freight quotation request
  - Dynamic cargo items table
  
- **NEW:** `resources/views/marketing/freight-quotations/list.blade.php`
  - List of marketing's freight quotations
  - Status filters (all, draft, pending_logistics, approved, linked_to_so)
  - Quick actions

- **NEW:** `resources/views/marketing/freight-quotations/show.blade.php`
  - Detailed view of single quotation
  - Shows shipment details, cargo items
  - If approved: shows logistics response with freight charges
  - "Proceed to Sales Order" button for approved quotations

- **NEW:** `resources/views/marketing/sales-orders/create-from-freight.blade.php`
  - Sales order creation form
  - Pre-displays freight quotation summary with charges
  - Form to add SO items
  - Calculates total = items subtotal + freight charges

### Views - Logistics
- **NEW:** `resources/views/production/logistic/pending-freight-quotations.blade.php`
  - List of pending quotations from marketing
  - Status filters (all, pending, responded)
  - Shows sender info, route, items, dates

- **NEW:** `resources/views/production/logistic/freight-quotation-show.blade.php`
  - Quotation review interface for logistics
  - Shows all shipment and cargo details
  - Form to add freight pricing (if not yet responded)
  - Shows approved quotation if already responded
  - Reject option with modal

### Navigation Updates
- **MODIFIED:** `resources/views/components/sidebar/marketing.blade.php`
  - Added "Freight Quotations" link under Area Sales

- **MODIFIED:** `resources/views/components/sidebar/production.blade.php`
  - Updated Freight Quotation section:
    - "Freight Quotation (Create)" - existing one
    - "Freight Quotation (Review)" - NEW pending quotations from marketing

### Routes
- **MODIFIED:** `routes/web.php`
  - Added marketing freight quotation routes (prefix: `/marketing/freight-quotations`)
    - GET `/` → list
    - GET `/create` → create form
    - POST `/store` → store
    - GET `/{freightQuotation}` → show
    - GET `/{freightQuotation}/proceed-to-so` → proceed to SO
    - POST `/{freightQuotation}/create-so` → create SO
    - GET `/{freightQuotation}/logistics-response` → view response
  
  - Added logistics freight quotation routes (prefix: `/production/logistic/`)
    - GET `/freight-quotations` → pending list
    - GET `/freight-quotations/{freightQuotation}` → show for review
    - POST `/freight-quotations/{freightQuotation}/approve` → approve
    - POST `/freight-quotations/{freightQuotation}/reject` → reject

### Migration
- **NEW:** `database/migrations/2026_06_11_000000_add_workflow_fields_to_freight_quotations.php`
  - Adds all workflow_status, FK, and response fields
  - Status: ✅ Migrated successfully

## Status Workflow

```
Draft (Marketing Created)
    ↓
Pending Logistics Review
    ↓
Approved (Logistics Added Pricing)
    ├→ Can Reject (back to Draft)
    ↓
Linked to SO (Marketing Created SO)
```

## Permission Requirements

- **Marketing:**
  - `marketing.area_sales` - To access Freight Quotations tab
  - `marketing.sales-orders` - To create SO from quotation

- **Logistics:**
  - `production.logistic` - To review and approve quotations

## Key Features

1. ✅ Multi-step workflow with clear status tracking
2. ✅ Timeline visualization on quotation detail view
3. ✅ Automatic freight charge calculation
4. ✅ Reject functionality with notes returned to marketing
5. ✅ Automatic SO creation with freight quotation linked
6. ✅ Freight charges added to SO total amount
7. ✅ Full audit trail (created_by, responded_by with timestamps)
8. ✅ Dynamic cargo items table in both marketing and logistics views
9. ✅ Status filters in both list views
10. ✅ Sidebar navigation updated for easy access

## Usage Example

### From Marketing's Perspective:
1. Go to Marketing Dashboard
2. Sidebar → Area Sales → Freight Quotations
3. Click "New Quotation"
4. Fill in shipment details, destination, cargo items
5. Submit - gets "Draft" status
6. Wait for logistics response
7. Once approved, click "Proceed to Sales Order"
8. Add customer, transaction type, and SO items
9. Click "Create Sales Order"
10. Normal SO workflow continues (approve, picking, delivery, etc.)

### From Logistics's Perspective:
1. Go to Production Dashboard
2. Sidebar → Logistics → Freight Quotation (Review)
3. See list of pending quotations from marketing
4. Click "Review"
5. Review shipment and cargo details
6. Add freight pricing (estimated charge, boxes, insurance %, handling %)
7. Add any notes
8. Click "Approve & Quote"
9. Quotation sent back to marketing for SO creation
10. Or click "Reject Quotation" with reason

## Testing Checklist

- [ ] Create freight quotation from marketing
- [ ] Verify it appears in logistics pending list
- [ ] Approve quotation with freight pricing
- [ ] Verify marketing sees approved status and charges
- [ ] Create SO from approved quotation
- [ ] Verify SO includes freight charges in total
- [ ] Verify quotation is linked to SO
- [ ] Test reject functionality
- [ ] Test all status filters in both lists
- [ ] Verify sidebar navigation works
- [ ] Check audit trail (created_by, responded_by timestamps)

## Future Enhancements

1. Email notifications when quotation status changes
2. Quotation template system for common routes
3. Historical rate tracking for pricing analysis
4. Bulk quotation creation for multiple shipments
5. Quotation comparison reports
6. Integration with actual freight carrier APIs
7. Automatic SO creation with pre-set defaults
8. Customer freight rate agreements
