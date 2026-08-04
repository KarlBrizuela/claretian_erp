<x-app-layout :title="'Make General Journal Entries'" :sidebar="'admin-finance'">
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card journal-form-card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Make General Journal Entries</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('accounting.journal.store') }}" method="POST" id="journalEntryForm">
                        @csrf
                        
                        <div class="journal-header mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">CURRENCY</label>
                                        <select class="form-control default-select" name="currency" readonly>
                                            <option value="PHP">Philippine peso</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label">DATE</label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label">ENTRY NO.</label>
                                        <input type="text" name="entry_no" class="form-control" value="{{ $entryNo }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label">EXCHANGE RATE</label>
                                        <input type="text" name="exchange_rate" class="form-control" value="1.0000" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive journal-table-wrapper">
                            <table class="table journal-table" id="journalItemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 250px;">ACCOUNT</th>
                                        <th style="width: 150px;">DEBIT</th>
                                        <th style="width: 150px;">CREDIT</th>
                                        <th>MEMO</th>
                                        <!-- <th style="width: 200px;">NAME</th> -->
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="journalItemsBody">
                                    @for($i = 0; $i < 1; $i++)
                                    <tr class="journal-row">
                                        <td>
                                            <select name="items[{{ $i }}][account_id]" class="form-control select2-account">
                                                <option value="">Select Account</option>
                                                @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][debit]" class="form-control debit-input text-end" step="0.01" min="0" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][credit]" class="form-control credit-input text-end" step="0.01" min="0" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $i }}][memo]" class="form-control" placeholder="Memo">
                                        </td>
                                        <!--
                                        <td>
                                            <select name="items[{{ $i }}][name]" class="form-control select2-name">
                                                <option value="">Customer/Vendor</option>
                                                @foreach($names as $name)
                                                <option value="{{ $name }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        -->
                                        <td class="text-center">
                                            <a href="javascript:void(0)" class="text-danger remove-row"><i class="las la-times-circle fs-20"></i></a>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td class="text-end font-w600">Totals</td>
                                        <td class="text-end font-w600" id="totalDebit">0.00</td>
                                        <td class="text-end font-w600" id="totalCredit">0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="addRowBtn" class="btn btn-primary rounded shadow-sm p-0 px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px;">
                                <i class="las la-plus"></i>Add Line
                            </button>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">GENERAL MEMO</label>
                                    <textarea name="memo" class="form-control" rows="3" placeholder="Enter transaction memo..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-4 d-flex justify-content-between">
                            <div>
                                <a href="{{ route('accounting.journal.index') }}" class="btn btn-light rounded shadow-sm px-4 d-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;">Back to List</a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-warning rounded text-white shadow-sm px-4 d-flex align-items-center justify-content-center" style="height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important; border: none;">Clear</button>
                                <button type="submit" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;">Save & Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .journal-form-card {
            background: #f8faff;
            border: 1px solid #d4e3ff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .journal-table thead th {
            background: #e9efff;
            color: #2c3e50;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border: 1px solid #d0d9e8;
            padding: 10px;
        }

        .journal-table td {
            padding: 5px;
            border: 1px solid #e1e8f0;
            vertical-align: middle;
        }

        .journal-table .form-control {
            border: none;
            background: transparent;
            padding: 8px;
            font-size: 13px;
        }

        .journal-table .form-control:focus {
            background: #fff;
            box-shadow: inset 0 0 0 1px #3a7afe;
        }

        .journal-row:nth-child(even) {
            background-color: #fcfdff;
        }

        .journal-row:hover {
            background-color: #f2f7ff;
        }

        .total-row td {
            background: #f1f4f9;
            color: #2c3e50;
            padding: 12px;
            font-size: 14px;
        }

        .form-label {
            font-weight: 700;
            color: #555;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .btn-save {
            background: #3a7afe;
            border-color: #3a7afe;
            font-weight: 600;
        }

        /* Select2 Accounting Style */
        .select2-container--default .select2-selection--single {
            border: none;
            background: transparent;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
    <script>
        jQuery(document).ready(function($) {
            // Use DOM count to be safer than PHP variable
            let rowIdx = $('.journal-row').length;

            // Notification fallback helper
            function showNotification(message, type = 'error') {
                if (window.toastr) {
                    toastr[type](message);
                } else {
                    alert(message);
                }
            }

            function initSelect2(element) {
                try {
                    const target = element ? $(element).find('.select2-account') : $('.select2-account');
                    if (target.length && typeof target.select2 === 'function') {
                        target.each(function() {
                            if (!$(this).hasClass("select2-hidden-accessible")) {
                                $(this).select2({
                                    placeholder: "Select Account",
                                    allowClear: true,
                                    width: '100%'
                                });
                            }
                        });
                    }

                    const targetName = element ? $(element).find('.select2-name') : $('.select2-name');
                    if (targetName.length && typeof targetName.select2 === 'function') {
                        targetName.each(function() {
                            if (!$(this).hasClass("select2-hidden-accessible")) {
                                $(this).select2({
                                    placeholder: "Customer/Vendor",
                                    allowClear: true,
                                    width: '100%'
                                });
                            }
                        });
                    }
                } catch (e) {
                    console.warn('Select2 initialization failed:', e);
                }
            }

            function calculateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                $('.debit-input').each(function() {
                    totalDebit += parseFloat($(this).val()) || 0;
                });

                $('.credit-input').each(function() {
                    totalCredit += parseFloat($(this).val()) || 0;
                });

                $('#totalDebit').text(totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#totalCredit').text(totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2}));

                if (Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0) {
                    $('#totalDebit, #totalCredit').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#totalDebit, #totalCredit').addClass('text-danger').removeClass('text-success');
                }
            }

            // Attach listeners early to avoid being blocked by crashes
            $(document).on('click', '.remove-row', function() {
                if ($('.journal-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                }
            });

            $('#addRowBtn').on('click', function() {
                const newRow = `
                    <tr class="journal-row">
                        <td>
                            <select name="items[${rowIdx}][account_id]" class="form-control select2-account">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${rowIdx}][debit]" class="form-control debit-input text-end" step="0.01" min="0" placeholder="0.00">
                        </td>
                        <td>
                            <input type="number" name="items[${rowIdx}][credit]" class="form-control credit-input text-end" step="0.01" min="0" placeholder="0.00">
                        </td>
                        <td>
                            <input type="text" name="items[${rowIdx}][memo]" class="form-control" placeholder="Memo">
                        </td>
                        <!--
                        <td>
                            <select name="items[${rowIdx}][name]" class="form-control select2-name">
                                <option value="">Customer/Vendor</option>
                                @foreach($names as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        -->
                        <td class="text-center">
                            <a href="javascript:void(0)" class="text-danger remove-row"><i class="las la-times-circle fs-20"></i></a>
                        </td>
                    </tr>
                `;
                const $newRow = $(newRow);
                $('#journalItemsBody').append($newRow);
                initSelect2($newRow);
                rowIdx++;
            });

            $(document).on('input', '.debit-input, .credit-input', function() {
                calculateTotals();
            });

            $('#journalEntryForm').on('submit', function(e) {
                const totalDebit = parseFloat($('#totalDebit').text().replace(/,/g, '')) || 0;
                const totalCredit = parseFloat($('#totalCredit').text().replace(/,/g, '')) || 0;

                if (Math.abs(totalDebit - totalCredit) > 0.01) {
                    e.preventDefault();
                    showNotification('Journal entry must balance! (Difference: ' + (totalDebit - totalCredit).toFixed(2) + ')', 'error');
                } else if (totalDebit <= 0) {
                    e.preventDefault();
                    showNotification('Total amount must be greater than zero.', 'error');
                }
            });

            // Final initialization
            initSelect2();
        });
    </script>
    @endpush
</x-app-layout>
