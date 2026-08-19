<?php

$filePath = __DIR__ . '/../resources/views/production/inventory/overview.blade.php';
$content = file_get_contents($filePath);

// Find where viewWorkflowTransferModal footer button ends:
$targetStr = '<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>';

$pos = strpos($content, $targetStr);
if ($pos === false) {
    die("Target string not found in overview.blade.php\n");
}

$prefix = substr($content, 0, $pos + strlen($targetStr));

// Construct clean scripts section
$scriptsSection = <<<'HTML'

                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        window.switchConsignmentSubTab = function(btn, targetPaneId) {
            $('.c-sub-btn').removeClass('active btn-danger').addClass('btn-outline-danger');
            $(btn).addClass('active btn-danger').removeClass('btn-outline-danger');
            $('#area-consignment-pane, #direct-consignment-pane').removeClass('show active').css('display', 'none');
            $('#' + targetPaneId).addClass('show active').css('display', 'block');
        };

        var workflowBatchData = @json($batchData ?? []);

        $(document).on('show.bs.modal', '#viewWorkflowTransferModal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var ref = button.data('ref') || ('ST-' + String(id).padStart(5, '0'));
            var from = button.data('from') || 'N/A';
            var to = button.data('to') || 'N/A';
            var requested = button.data('requested') || 'N/A';
            var assigned = button.data('assigned') || 'Unassigned';
            var date = button.data('date') || 'N/A';
            var status = button.data('status') || 'Pending';
            var notes = button.data('notes') || '';

            var modal = $(this);
            modal.find('#wf-modal-ref-sub').text('Ref: ' + ref);
            modal.find('#wf-modal-requested').text(requested);
            modal.find('#wf-modal-route').text(from + ' → ' + to);
            modal.find('#wf-modal-assigned').text(assigned);
            modal.find('#wf-modal-date').text(date);
            modal.find('#wf-modal-status-badge').text(status);

            var itemsData = [];
            var batchInfo = workflowBatchData[id];
            if (batchInfo && Array.isArray(batchInfo.items) && batchInfo.items.length > 0) {
                itemsData = batchInfo.items;
            } else {
                itemsData = [{ name: 'Stock Item', type: 'Book', quantity: 1 }];
            }

            var rowsHtml = '';
            var totalQty = 0;
            itemsData.forEach(function(item) {
                var qty = parseInt(item.quantity) || 0;
                totalQty += qty;
                var typeColor = item.type === 'Book' ? 'success' : (item.type === 'Bundle' ? 'warning' : 'secondary');
                rowsHtml += `<tr>
                    <td class="fw-semibold text-dark">${item.name || 'Unknown Item'}</td>
                    <td><span class="badge bg-${typeColor}">${item.type || 'Item'}</span></td>
                    <td class="text-center fw-bold text-success">${qty} pcs</td>
                </tr>`;
            });
            if (itemsData.length > 1) {
                rowsHtml += `<tr class="table-light fw-bold">
                    <td colspan="2" class="text-end small">Total Batch Units:</td>
                    <td class="text-center text-success">${totalQty} pcs</td>
                </tr>`;
            }

            modal.find('#wf-modal-items-body').html(rowsHtml);
            modal.find('#wf-modal-total-summary').text(itemsData.length + ' title(s) · ' + totalQty + ' pcs total');

            if (notes && notes.trim() !== '') {
                modal.find('#wf-modal-notes').text(notes);
                modal.find('#wf-modal-notes-container').show();
            } else {
                modal.find('#wf-modal-notes-container').hide();
            }
        });

        function showNotification(message, type = 'success') {
            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;';
                document.body.appendChild(toastContainer);
            }
            
            const toastId = 'toast-' + Date.now();
            const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning';
            const icon = type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-exclamation-circle' : 'la-info-circle';
            
            const toastHTML = `
                <div id="${toastId}" class="toast show text-white ${bgColor}" role="alert" style="min-width: 280px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
                    <div class="toast-header ${bgColor} text-white border-0">
                        <i class="las ${icon} me-2"></i>
                        <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body pt-0">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            if (toastElement && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                try {
                    const bsToast = new bootstrap.Toast(toastElement);
                    bsToast.show();
                    toastElement.addEventListener('hidden.bs.toast', function() {
                        toastElement.remove();
                    });
                } catch(e) {}
            }
            
            setTimeout(() => {
                toastElement?.remove();
            }, 4000);
        }

        function closeModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (inst) inst.hide();
                }
                if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                    $(modalEl).modal('hide');
                }
            } catch (e) {}
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }

        var sitesInventoryData = {
            @foreach($sites as $s)
                {{ $s->id }}: @json($s->inventory),
            @endforeach
        };

        let currentBookName = null;
        let currentStock = 0;
        let maxStock = null;
        let currentBookId = null;
        let globalBookMaxStock = null;
        let stockMgmtAddHandler = null;
        let stockMgmtEditHandler = null;

        window.onStockMgmtSiteChange = function() {
            if (!currentBookId) return;
            const siteSelect = document.getElementById('mgmtSiteSelect');
            if (!siteSelect) return;
            const siteId = parseInt(siteSelect.value);
            if (!siteId) return;

            const inventory = sitesInventoryData[siteId] || [];
            const item = inventory.find(i => i.book_id === currentBookId);
            
            const stockVal = item ? item.quantity : 0;
            const siteMaxStock = (item && item.max_stock !== null) ? item.max_stock : (globalBookMaxStock || null);

            currentStock = stockVal;
            maxStock = siteMaxStock;

            if (document.getElementById('mgmtCurrentStock')) document.getElementById('mgmtCurrentStock').value = currentStock;
            if (document.getElementById('mgmtMaxStock')) document.getElementById('mgmtMaxStock').value = maxStock !== null ? maxStock : 'Not Set';

            const addQtyInput = document.getElementById('mgmtAddQuantity');
            if (addQtyInput && addQtyInput.value) {
                addQtyInput.dispatchEvent(new Event('input'));
            }
            const editQtyInput = document.getElementById('mgmtEditQuantity');
            if (editQtyInput && editQtyInput.value) {
                editQtyInput.dispatchEvent(new Event('input'));
            }
        };

        window.openStockManagementModal = function(bookId, bookName, stock, max) {
            currentBookId = bookId;
            currentBookName = bookName;
            globalBookMaxStock = max;

            const nameInput = document.getElementById('mgmtBookName');
            if (nameInput) nameInput.value = bookName;
            
            const siteSelect = document.getElementById('mgmtSiteSelect');
            if (siteSelect) {
                let mainWarehouseOption = [...siteSelect.options].find(opt => opt.text.trim() === 'Main Warehouse');
                if (mainWarehouseOption) {
                    siteSelect.value = mainWarehouseOption.value;
                } else if (siteSelect.options.length > 0) {
                    siteSelect.selectedIndex = 0;
                }
            }

            window.onStockMgmtSiteChange();
            
            if (document.getElementById('mgmtAddQuantity')) document.getElementById('mgmtAddQuantity').value = '';
            if (document.getElementById('mgmtAddWarning')) document.getElementById('mgmtAddWarning').innerHTML = '';
            if (document.getElementById('mgmtAddPreview')) document.getElementById('mgmtAddPreview').style.display = 'none';
            if (document.getElementById('mgmtEditQuantity')) document.getElementById('mgmtEditQuantity').value = '';
            if (document.getElementById('mgmtEditWarning')) document.getElementById('mgmtEditWarning').innerHTML = '';
            if (document.getElementById('mgmtEditPreview')) document.getElementById('mgmtEditPreview').style.display = 'none';

            const saveBtn = document.getElementById('mgmtSaveBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Changes';
            }

            const addTabBtn = document.getElementById('addTab');
            const editTabBtn = document.getElementById('editTab');
            const addTabPane = document.getElementById('addTabContent');
            const editTabPane = document.getElementById('editTabContent');

            if (addTabBtn && editTabBtn && addTabPane && editTabPane) {
                addTabBtn.classList.add('active');
                addTabBtn.setAttribute('aria-selected', 'true');
                editTabBtn.classList.remove('active');
                editTabBtn.setAttribute('aria-selected', 'false');

                addTabPane.classList.add('show', 'active');
                editTabPane.classList.remove('show', 'active');
            }

            if (stockMgmtAddHandler && document.getElementById('mgmtAddQuantity')) {
                document.getElementById('mgmtAddQuantity').removeEventListener('input', stockMgmtAddHandler);
            }
            if (stockMgmtEditHandler && document.getElementById('mgmtEditQuantity')) {
                document.getElementById('mgmtEditQuantity').removeEventListener('input', stockMgmtEditHandler);
            }

            stockMgmtAddHandler = function() {
                const quantity = parseInt(this.value) || 0;
                const newStock = currentStock + quantity;
                const warning = document.getElementById('mgmtAddWarning');
                const preview = document.getElementById('mgmtAddPreview');

                if (quantity > 0) {
                    if (preview) preview.style.display = 'block';
                    if (document.getElementById('mgmtAddNewStock')) document.getElementById('mgmtAddNewStock').textContent = newStock;

                    if (warning) {
                        if (maxStock && newStock > maxStock) {
                            warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                        } else {
                            warning.innerHTML = '';
                        }
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    if (preview) preview.style.display = 'none';
                    if (warning) warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            stockMgmtEditHandler = function() {
                const newStock = parseInt(this.value);
                const warning = document.getElementById('mgmtEditWarning');
                const preview = document.getElementById('mgmtEditPreview');

                if (!isNaN(newStock) && newStock >= 0) {
                    if (preview) preview.style.display = 'block';
                    if (document.getElementById('mgmtEditOldStock')) document.getElementById('mgmtEditOldStock').textContent = currentStock;
                    if (document.getElementById('mgmtEditNewStock')) document.getElementById('mgmtEditNewStock').textContent = newStock;

                    if (warning) {
                        if (maxStock && newStock > maxStock) {
                            warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                        } else {
                            warning.innerHTML = '';
                        }
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    if (preview) preview.style.display = 'none';
                    if (warning) warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            document.getElementById('mgmtAddQuantity')?.addEventListener('input', stockMgmtAddHandler);
            document.getElementById('mgmtEditQuantity')?.addEventListener('input', stockMgmtEditHandler);

            const modalEl = document.getElementById('stockManagementModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        };

        window.saveStockManagement = function() {
            const addPane = document.getElementById('addTabContent');
            const editPane = document.getElementById('editTabContent');
            
            const isAddActive = addPane && (addPane.classList.contains('active') || addPane.classList.contains('show'));
            const isEditActive = editPane && (editPane.classList.contains('active') || editPane.classList.contains('show'));

            const addQtyVal = document.getElementById('mgmtAddQuantity')?.value;
            const editQtyVal = document.getElementById('mgmtEditQuantity')?.value;

            if (isAddActive || (addQtyVal && !editQtyVal)) {
                window.saveAddStock();
            } else if (isEditActive || editQtyVal) {
                window.saveEditStock();
            } else {
                window.saveAddStock();
            }
        };

        window.saveAddStock = function() {
            const saveBtn = document.getElementById('mgmtSaveBtn');
            const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';

            try {
                const quantityInput = document.getElementById('mgmtAddQuantity');
                const quantity = parseInt(quantityInput ? quantityInput.value : '');
                const siteId = document.getElementById('mgmtSiteSelect')?.value;

                if (!siteId) {
                    showNotification('Please select a site', 'warning');
                    return;
                }

                if (!quantity || isNaN(quantity) || quantity < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }

                const newStock = currentStock + quantity;

                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Saving...';
                }

                fetch(`/production/inventory/update-stock/${currentBookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'add',
                        site_id: siteId,
                        quantity: quantity,
                        new_stock: newStock
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showNotification(data.message || 'Stock added successfully!', 'success');
                        closeModal('stockManagementModal');
                        setTimeout(() => location.reload(), 200);
                    } else {
                        showNotification('Error: ' + (data.message || 'Failed to update stock'), 'error');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred while adding stock', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    }
                });

            } catch (err) {
                console.error('saveAddStock exception:', err);
                showNotification('An unexpected error occurred', 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            }
        };

        window.saveEditStock = function() {
            const saveBtn = document.getElementById('mgmtSaveBtn');
            const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';

            try {
                const editInput = document.getElementById('mgmtEditQuantity');
                const newStock = parseInt(editInput ? editInput.value : '');
                const siteId = document.getElementById('mgmtSiteSelect')?.value;

                if (!siteId) {
                    showNotification('Please select a site', 'warning');
                    return;
                }

                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid stock quantity (0 or greater)', 'warning');
                    return;
                }

                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Saving...';
                }

                fetch(`/production/inventory/update-stock/${currentBookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'edit',
                        site_id: siteId,
                        quantity: newStock,
                        new_stock: newStock
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showNotification(data.message || 'Stock updated successfully!', 'success');
                        closeModal('stockManagementModal');
                        setTimeout(() => location.reload(), 200);
                    } else {
                        showNotification('Error: ' + (data.message || 'Failed to update stock'), 'error');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('An error occurred while updating stock', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    }
                });

            } catch (err) {
                console.error('saveEditStock exception:', err);
                showNotification('An unexpected error occurred', 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            }
        };
    </script>
@endpush
</x-app-layout>

HTML;

file_put_contents($filePath, $prefix . $scriptsSection);
echo "SUCCESSFULLY REBUILT CLEAN SCRIPTS SECTION IN overview.blade.php!\n";
