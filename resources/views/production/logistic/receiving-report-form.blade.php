<x-app-layout :title="'Create Receiving Report'" :sidebar="'production'">
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <h4 class="fs-24 mb-0 text-black">Create Receiving Report</h4>
                </div>
                <div class="card-body">
                    @if(!$purchaseOrder)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Select Purchase Order to Receive</label>
                            <select class="form-control default-select" id="po_selector" onchange="window.location.href='{{ route('production.logistic.receiving-report.create') }}/' + this.value">
                                <option value="">-- Choose P.O. --</option>
                                @foreach($openPOs as $po)
                                <option value="{{ $po->id }}">PO #{{ $po->po_number }} - {{ $po->supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('production.logistic.receiving-report.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                        
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">RR Number</label>
                                <input type="text" name="rr_number" class="form-control" value="RR-{{ date('YmdHis') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Received</label>
                                <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-control" value="{{ $purchaseOrder->supplier->company_name }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PO Number</label>
                                <input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" readonly>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Description</th>
                                        <th>Ordered Qty</th>
                                        <th>Previously Received</th>
                                        <th>Remaining</th>
                                        <th style="width: 150px;">Today's Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $item)
                                    @php
                                        $remaining = $item->quantity - $item->received_quantity;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product ? $item->product->name : $item->description }}</strong>
                                            @if($item->isbn) <br><small class="text-muted">ISBN: {{ $item->isbn }}</small> @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">{{ $item->received_quantity }}</td>
                                        <td class="text-center text-danger">{{ $remaining }}</td>
                                        <td>
                                            <input type="number" name="items[{{ $item->id }}][quantity_received]" 
                                                   class="form-control text-center qty-input" 
                                                   max="{{ $remaining }}" min="0" 
                                                   value="{{ $remaining }}"
                                                   oninput="validateQty(this, {{ $remaining }})">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Enter any discrepancies or delivery notes..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('production.logistic.receiving-report-list') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Post Receiving Report</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function validateQty(input, max) {
            if (parseInt(input.value) > max) {
                input.value = max;
                alert('Cannot receive more than remaining quantity (' + max + ')');
            }
        }
    </script>
    @endpush
</x-app-layout>
