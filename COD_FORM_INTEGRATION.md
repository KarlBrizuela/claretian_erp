<!-- Add these fields to your Sales Order creation form -->

<!-- 1. Transaction Type Selection -->
<div class="form-group">
    <label for="transaction_type">Transaction Type *</label>
    <select class="form-control @error('transaction_type') is-invalid @enderror" id="transaction_type" name="transaction_type" required>
        <option value="">-- Select Payment Type --</option>
        <option value="COD" {{ old('transaction_type') == 'COD' ? 'selected' : '' }}>
            Cash on Delivery (COD)
        </option>
        <option value="Credit" {{ old('transaction_type') == 'Credit' ? 'selected' : '' }}>
            Credit/Terms
        </option>
        <option value="Prepaid" {{ old('transaction_type') == 'Prepaid' ? 'selected' : '' }}>
            Pre-paid/Advance Payment
        </option>
        <option value="Check" {{ old('transaction_type') == 'Check' ? 'selected' : '' }}>
            Check Payment
        </option>
        <option value="Other" {{ old('transaction_type') == 'Other' ? 'selected' : '' }}>
            Other
        </option>
    </select>
    @error('transaction_type')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<!-- 2. Rider/Driver Selection (Only show if COD selected) -->
<div class="form-group" id="riderGroup" style="display: none;">
    <label for="rider_id">Assign Rider/Driver for COD *</label>
    <select class="form-control @error('rider_id') is-invalid @enderror" id="rider_id" name="rider_id">
        <option value="">-- Select Rider --</option>
        @foreach($riders as $rider)
            <option value="{{ $rider->id }}" {{ old('rider_id') == $rider->id ? 'selected' : '' }}>
                {{ $rider->name }} ({{ $rider->position }})
            </option>
        @endforeach
    </select>
    @error('rider_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
    <small class="form-text text-muted">Select the driver who will collect payment from customer</small>
</div>

<script>
// Show/hide rider selection based on transaction type
document.getElementById('transaction_type')?.addEventListener('change', function() {
    const riderGroup = document.getElementById('riderGroup');
    const riderSelect = document.getElementById('rider_id');
    
    if (this.value === 'COD') {
        riderGroup.style.display = 'block';
        riderSelect.setAttribute('required', 'required');
    } else {
        riderGroup.style.display = 'none';
        riderSelect.removeAttribute('required');
        riderSelect.value = '';
    }
});

// Check initial state on page load
document.addEventListener('DOMContentLoaded', function() {
    const transactionType = document.getElementById('transaction_type');
    if (transactionType && transactionType.value === 'COD') {
        document.getElementById('riderGroup').style.display = 'block';
    }
});
</script>

<!-- ============================================ -->
<!-- CONTROLLER UPDATE - MarketingController.php -->
<!-- ============================================ -->

/*
The storeSalesOrder() method in MarketingController already includes:

// 6. Create Rider Collection if transaction type is COD and rider is assigned
$transactionType = $request->input('transaction_type', 'COD');
$riderId = $request->input('rider_id');

if ($transactionType === 'COD' && $riderId) {
    \App\Models\RiderCollection::create([
        'sales_order_id' => $so->id,
        'rider_id' => $riderId,
        'amount_to_collect' => $totalAmount,
        'status' => 'pending',
    ]);

    // Set SO transaction type
    $so->update([
        'transaction_type' => 'COD',
        'collection_status' => 'pending_collection',
        'driver_id' => $riderId,
    ]);
}

So you just need to:
1. Add the form fields above
2. Add validation in your controller method
*/

// Add to MarketingController::storeSalesOrder() validation:
$validated = $request->validate([
    'customer_id' => 'required|exists:customers,customer_id',
    'type' => 'required',
    'so_number' => 'required|unique:sales_orders,so_number',
    'transaction_type' => 'nullable|in:COD,Credit,Prepaid,Check,Other',
    'rider_id' => 'nullable|exists:users,id|required_if:transaction_type,COD',
    'items' => 'required|array|min:1',
    'remarks' => 'nullable',
    'terms' => 'nullable',
    'ref_number' => 'nullable',
    'billing_address' => 'nullable',
    'attachment' => 'nullable|file|max:5120',
]);

// ============================================
// GET RIDERS LIST - Add to your controller
// ============================================

// In your SO creation/edit method, pass riders:
$riders = User::where('position', 'like', '%Driver%')
    ->orWhere('position', 'like', '%Rider%')
    ->where('is_active', true)
    ->get();

return view('your.view', [
    'riders' => $riders,
    // ... other data
]);

// ============================================
// EXAMPLE: Update createSalesOrder() method
// ============================================

public function createSalesOrder()
{
    $customers = Customer::all();
    $riders = User::whereIn('position', [
        'Driver',
        'Rider',
        'Delivery Driver',
        'Logistics Driver'
    ])->where('is_active', true)->get();
    
    return view('marketing.create-sales-order', compact('customers', 'riders'));
}

// ============================================
// BLADE: SO Creation Form Integration
// ============================================

@if($errors->has('transaction_type') || $errors->has('rider_id'))
    <div class="alert alert-danger">
        @if($errors->has('transaction_type'))
            <strong>Transaction Type Error:</strong> {{ $errors->first('transaction_type') }}<br>
        @endif
        @if($errors->has('rider_id'))
            <strong>Rider Error:</strong> {{ $errors->first('rider_id') }}<br>
        @endif
    </div>
@endif

<!-- Include the form fields from above -->

<!-- ============================================ -->
<!-- TESTING THE COD WORKFLOW -->
<!-- ============================================ -->

/*
Step 1: Create Sales Order with COD
- Set Transaction Type = COD
- Select a Rider
- Create order

Step 2: Verify RiderCollection created
- Check database: SELECT * FROM rider_collections WHERE sales_order_id = {id}
- Status should be 'pending'

Step 3: Rider Records Collection
- Go to: /rider/collections
- Click order
- Enter amount collected
- Upload photo
- Click "Record Collection"

Step 4: Rider Hands Over
- Click "Mark as Handed Over"
- Status changes to 'handed_over'

Step 5: Cashier Verifies
- Go to: /admin-finance/cashier/collections
- Click "Verify"
- Verify amount
- Click "Approve & Record"
- Payment created + GL posted

Step 6: Check Results
- Sales Order payment_status = 'paid'
- Sales Order collection_status = 'reconciled'
- Payment record created
- GL entries posted
*/
