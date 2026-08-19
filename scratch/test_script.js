
    
    
    
        function switchConsignmentSubTab(btn, targetPaneId) {
            $('.c-sub-btn').removeClass('active btn-danger').addClass('btn-outline-danger');
            $(btn).addClass('active btn-danger').removeClass('btn-outline-danger');
            $('#area-consignment-pane, #direct-consignment-pane').removeClass('show active').css('display', 'none');
            $('#' + targetPaneId).addClass('show active').css('display', 'block');
        }
        var workflowBatchData = /*json*/[]$batchData ?? []);

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

        let currentBookName = null;
        let currentStock = 0;
        let maxStock = null;
        let currentBookId = null;
        let globalBookMaxStock = null;
        let stockMgmtAddHandler = null;
        let stockMgmtEditHandler = null;

        function onStockMgmtSiteChange() {
            if (!currentBookId) return;
            const siteId = parseInt(document.getElementById('mgmtSiteSelect').value);
            if (!siteId) return;

            const inventory = sitesInventoryData[siteId] || [];
            const item = inventory.find(i => i.book_id === currentBookId);
            
            const stockVal = item ? item.quantity : 0;
            const siteMaxStock = (item && item.max_stock !== null) ? item.max_stock : (globalBookMaxStock || null);

            currentStock = stockVal;
            maxStock = siteMaxStock;

            document.getElementById('mgmtCurrentStock').value = currentStock;
            document.getElementById('mgmtMaxStock').value = maxStock !== null ? maxStock : 'Not Set';

            // Trigger inputs update to refresh previews/warnings
            const addQtyInput = document.getElementById('mgmtAddQuantity');
            if (addQtyInput && addQtyInput.value) {
                addQtyInput.dispatchEvent(new Event('input'));
            }
            const editQtyInput = document.getElementById('mgmtEditQuantity');
            if (editQtyInput && editQtyInput.value) {
                editQtyInput.dispatchEvent(new Event('input'));
            }
        }

        function openStockManagementModal(bookId, bookName, stock, max) {
            currentBookId = bookId;
            currentBookName = bookName;
            globalBookMaxStock = max;

            document.getElementById('mgmtBookName').value = bookName;
            
            // Default select site to "Main Warehouse" if it exists
            const siteSelect = document.getElementById('mgmtSiteSelect');
            if (siteSelect) {
                let mainWarehouseOption = [...siteSelect.options].find(opt => opt.text.trim() === 'Main Warehouse');
                if (mainWarehouseOption) {
                    siteSelect.value = mainWarehouseOption.value;
                } else if (siteSelect.options.length > 0) {
                    siteSelect.selectedIndex = 0;
                }
            }

            // Sync values for selected site
            onStockMgmtSiteChange();
            
            document.getElementById('mgmtAddQuantity').value = '';
            document.getElementById('mgmtAddWarning').innerHTML = '';
            document.getElementById('mgmtAddPreview').style.display = 'none';
            document.getElementById('mgmtEditQuantity').value = '';
            document.getElementById('mgmtEditWarning').innerHTML = '';
            document.getElementById('mgmtEditPreview').style.display = 'none';

            const saveBtn = document.getElementById('mgmtSaveBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Changes';
            }

            // Force visual and functional reset of active tab to "Add Stock"
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

            if (stockMgmtAddHandler) {
                document.getElementById('mgmtAddQuantity').removeEventListener('input', stockMgmtAddHandler);
            }
            if (stockMgmtEditHandler) {
                document.getElementById('mgmtEditQuantity').removeEventListener('input', stockMgmtEditHandler);
            }

            stockMgmtAddHandler = function() {
                const quantity = parseInt(this.value) || 0;
                const newStock = currentStock + quantity;
                const warning = document.getElementById('mgmtAddWarning');
                const preview = document.getElementById('mgmtAddPreview');

                if (quantity > 0) {
                    preview.style.display = 'block';
                    document.getElementById('mgmtAddNewStock').textContent = newStock;

                    if (maxStock && newStock > maxStock) {
                        warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                    } else {
                        warning.innerHTML = '';
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    preview.style.display = 'none';
                    warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            stockMgmtEditHandler = function() {
                const newStock = parseInt(this.value);
                const warning = document.getElementById('mgmtEditWarning');
                const preview = document.getElementById('mgmtEditPreview');

                if (!isNaN(newStock) && newStock >= 0) {
                    preview.style.display = 'block';
                    document.getElementById('mgmtEditOldStock').textContent = currentStock;
                    document.getElementById('mgmtEditNewStock').textContent = newStock;

                    if (maxStock && newStock > maxStock) {
                        warning.innerHTML = `<span class="text-warning"><i class="las la-exclamation-triangle"></i> Notice: New stock (${newStock}) exceeds max stock limit (${maxStock})</span>`;
                    } else {
                        warning.innerHTML = '';
                    }
                    if (saveBtn) saveBtn.disabled = false;
                } else {
                    preview.style.display = 'none';
                    warning.innerHTML = '';
                    if (saveBtn) saveBtn.disabled = false;
                }
            };

            document.getElementById('mgmtAddQuantity').addEventListener('input', stockMgmtAddHandler);
            document.getElementById('mgmtEditQuantity').addEventListener('input', stockMgmtEditHandler);

            const modal = new bootstrap.Modal(document.getElementById('stockManagementModal'));
            modal.show();
        }

        function saveStockManagement() {
            const addPane = document.getElementById('addTabContent');
            const editPane = document.getElementById('editTabContent');
            
            const isAddActive = addPane && (addPane.classList.contains('active') || addPane.classList.contains('show'));
            const isEditActive = editPane && (editPane.classList.contains('active') || editPane.classList.contains('show'));

            const addQtyVal = document.getElementById('mgmtAddQuantity')?.value;
            const editQtyVal = document.getElementById('mgmtEditQuantity')?.value;

            if (isAddActive || (addQtyVal && !editQtyVal)) {
                saveAddStock();
            } else if (isEditActive || editQtyVal) {
                saveEditStock();
            } else {
                saveAddStock();
            }
        }

        function saveAddStock() {
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

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '1';

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
        }

        function saveEditStock() {
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
                    showNotification('Please enter a valid stock value', 'warning');
                    return;
                }



                if (!currentBookId) {
                    showNotification('No item selected', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '1';

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
                        action: 'set',
                        site_id: siteId,
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
        }

        // Site Management Functions
        document.getElementById('addSiteForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/production/sites/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Site added successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addSiteModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });

        document.querySelectorAll('.editSiteForm').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const siteId = this.getAttribute('data-site-id');
                const formData = new FormData(this);
                
                fetch(`/production/sites/update/${siteId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById(`editSiteModal${siteId}`)).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            });
        });
        
        // Delete Site Function
        function deleteSite(siteId, siteName) {
            if (confirm(`Are you sure you want to delete "${siteName}"? This action cannot be undone.`)) {
                const deleteBtn = event.target.closest('button');
                const originalHTML = deleteBtn.innerHTML;
                deleteBtn.innerHTML = '<i class="las la-spinner la-spin"></i>';
                deleteBtn.disabled = true;

                fetch(`/production/sites/delete/${siteId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Site deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                        deleteBtn.innerHTML = originalHTML;
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the site', 'error');
                    deleteBtn.innerHTML = originalHTML;
                    deleteBtn.disabled = false;
                });
            }
        }
        
        // Stock Transfer Variables
        let selectedBooksMap = {};
        let siteBooks = {};
        let nextRowId = 1;

        // Store master books and sites inventory data safely
        @php
            $formattedSitesInventory = [];
            foreach($allSites ?? $sites ?? [] as $site) {
                $formattedSitesInventory[$site->id] = collect($site->inventory ?? [])->map(function($inv) {
                    return [
                        'book_id' => $inv->book_id,
                        'book_index_id' => $inv->book_index_id,
                        'book_bundle_id' => $inv->book_bundle_id,
                        'book' => ['name' => $inv->item_name ?? 'Unknown'],
                        'quantity' => (int)($inv->quantity ?? 0),
                        'reorder_point' => $inv->reorder_point,
                        'max_stock' => $inv->max_stock
                    ];
                })->values()->toArray();
            }
            $formattedMasterBooks = collect($allBooks ?? $books ?? [])->map(function($book) {
                return [
                    'book_id' => $book->id,
                    'book' => ['name' => $book->name ?? 'Unknown'],
                    'quantity' => (int)($book->stock ?? 0)
                ];
            })->values()->toArray();
        @endphp
        const masterBooksData = /*json*/[]$formattedMasterBooks);
        const sitesInventoryData = /*json*/[]$formattedSitesInventory);

        // Initialize transfer modal from master inventory
        window.initTransferModalFromMaster = function() {
            selectedBooksMap = {};
            nextRowId = 1;
            siteBooks = {}; // Clear cache
            
            const fromSelect = document.getElementById('fromSiteSelect');
            const toSelect = document.getElementById('toSiteSelect');
            if (fromSelect) {
                fromSelect.value = '';
                $(fromSelect).trigger('change');
            }
            if (toSelect) {
                toSelect.value = '';
                $(toSelect).trigger('change');
            }
            const notesTextarea = document.querySelector('textarea[name="notes"]');
            if (notesTextarea) notesTextarea.value = '';
            
            const itemTypeSelect = document.getElementById('itemTypeSelect');
            if (itemTypeSelect) itemTypeSelect.value = 'book';

            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) {
                showAddBookBtn.disabled = true;
                showAddBookBtn.style.display = 'block';
            }
            if (showAddIndexBtn) {
                showAddIndexBtn.disabled = true;
                showAddIndexBtn.style.display = 'block';
            }
            if (showAddBundleBtn) {
                showAddBundleBtn.disabled = true;
                showAddBundleBtn.style.display = 'block';
            }
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select an Item --</option>';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            
            renderSelectedBooks();
            updateSubmitButton();
        };

        // Transfer Stock Functions — Batch Submit
        document.getElementById('transferStockForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const keys = Object.keys(selectedBooksMap);
            if (keys.length === 0) {
                showNotification('Please add at least one item', 'error');
                return;
            }

            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId   = document.getElementById('toSiteSelect').value;

            if (!fromSiteId) {
                showNotification('Please select source site', 'error');
                return;
            }
            if (!toSiteId) {
                showNotification('Please select destination site', 'error');
                return;
            }
            if (fromSiteId === toSiteId) {
                showNotification('Source and destination sites cannot be the same', 'error');
                return;
            }

            // Build items array — books, indices & bundles all together
            const items = keys.map(key => {
                const item = selectedBooksMap[key];
                return {
                    type:     item.type,      // 'book' | 'index' | 'bundle'
                    item_id:  item.itemId,
                    quantity: item.quantity
                };
            });

            const submitBtn   = document.getElementById('submitTransferBtn');
            const originalTxt = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Submitting…';
            submitBtn.disabled  = true;

            fetch('/production/sites/transfer-batch', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json'
                },
                body: JSON.stringify({
                    from_site_id: fromSiteId,
                    to_site_id:   toSiteId,
                    notes:        document.querySelector('textarea[name="notes"]')?.value || '',
                    items:        items
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const skippedMsg = data.skipped > 0 ? ` (${data.skipped} skipped due to insufficient stock)` : '';
                    showNotification(`${data.created} item(s) transfer submitted!${skippedMsg}`, data.skipped > 0 ? 'warning' : 'success');
                    bootstrap.Modal.getInstance(document.getElementById('transferStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Transfer failed', 'error');
                    submitBtn.innerHTML = originalTxt;
                    submitBtn.disabled  = false;
                }
            })
            .catch(err => {
                console.error('Batch transfer error:', err);
                showNotification('Network error. Please try again.', 'error');
                submitBtn.innerHTML = originalTxt;
                submitBtn.disabled  = false;
            });
        });

        // When source site is selected, load its inventory
        $('#transferStockModal').on('shown.bs.modal', function () {
            $('#fromSiteSelect, #toSiteSelect').select2({
                dropdownParent: $('#transferStockModal'),
                width: '100%'
            });
        });

        $(document).on('change', '#fromSiteSelect', function() {
            const siteId = this.value;
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (siteId) {
                selectedBooksMap = {};
                nextRowId = 1;
                renderSelectedBooks();
                updateSubmitButton();
                
                loadBooksForSite(siteId);
                if (showAddBookBtn) showAddBookBtn.disabled = false;
                if (showAddIndexBtn) showAddIndexBtn.disabled = false;
                if (showAddBundleBtn) showAddBundleBtn.disabled = false;
            } else {
                if (showAddBookBtn) showAddBookBtn.disabled = true;
                if (showAddIndexBtn) showAddIndexBtn.disabled = true;
                if (showAddBundleBtn) showAddBundleBtn.disabled = true;
                const bookSelect = document.getElementById('bookSelect');
                if (bookSelect) bookSelect.innerHTML = '<option value="">-- Select an Item --</option>';
            }
        });

        function loadBooksForSite(siteId) {
            console.log('Loading books for site:', siteId);
            
            // Always fetch real-time data from server
            console.log('Fetching real-time books from server for site:', siteId);
            fetch(`/production/sites/${siteId}/inventory`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Real-time books loaded from server:', data);
                if (data.success && data.inventory && Array.isArray(data.inventory)) {
                    siteBooks[siteId] = data.inventory;
                    populateBookSelect(siteId);
                } else {
                    const select = document.getElementById('bookSelect');
                    if (select) select.innerHTML = '<option value="">No books available</option>';
                    showNotification('No inventory found for this site', 'warning');
                }
            })
            .catch(error => {
                console.error('Error loading books:', error);
                showNotification('Could not load books: ' + error.message, 'error');
                const select = document.getElementById('bookSelect');
                if (select) select.innerHTML = '<option value="">Error loading books</option>';
            });
        }

        document.getElementById('itemTypeSelect')?.addEventListener('change', function() {
            const siteId = document.getElementById('fromSiteSelect').value;
            if (siteId) {
                populateBookSelect(parseInt(siteId));
            }
        });

        function populateBookSelect(siteId) {
            const select = document.getElementById('bookSelect');
            if (!select) return;
            
            const itemType = document.getElementById('itemTypeSelect')?.value || 'book';
            const inventory = siteBooks[siteId] || [];
            console.log('Populating dropdown with inventory:', inventory, 'filtered by type:', itemType);
            
            // Filter by item type
            const typeFiltered = inventory.filter(item => item.type === itemType);
            
            // Filter out items already in the selectedBooksMap
            const availableItems = typeFiltered.filter(item => {
                const key = itemType + '_' + item.item_id;
                return !selectedBooksMap[key];
            });

            const placeholderText = `-- Select a ${itemType.charAt(0).toUpperCase() + itemType.slice(1)} --`;
            select.innerHTML = `<option value="">${placeholderText}</option>`;
            
            if (availableItems.length === 0) {
                if (typeFiltered.length === 0) {
                    select.innerHTML = `<option value="">-- No ${itemType}s available --</option>`;
                } else {
                    select.innerHTML = `<option value="">-- All ${itemType}s added --</option>`;
                }
            } else {
                availableItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.item_id;
                    option.textContent = `${item.name} (Available: ${item.quantity})`;
                    option.dataset.available = item.quantity;
                    option.dataset.name = item.name;
                    option.dataset.itemId = item.item_id;
                    option.dataset.type = item.type;
                    select.appendChild(option);
                });
            }

            // Initialize or Refresh Select2 (with integrated search)
            if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
                const $select = $('#bookSelect');
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    dropdownParent: $('#transferStockModal'),
                    placeholder: placeholderText,
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0
                });

                $select.off('change.itemSelect').on('change.itemSelect', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const availableSpan = document.getElementById('selectedBookAvailable');
                    const quantityInput = document.getElementById('bookQuantity');
                    
                    if (selectedOption && selectedOption.value) {
                        if (availableSpan) availableSpan.textContent = selectedOption.dataset.available || '0';
                        if (quantityInput) {
                            quantityInput.max = selectedOption.dataset.available || 1;
                            quantityInput.value = '';
                            quantityInput.disabled = false;
                        }
                    } else {
                        if (availableSpan) availableSpan.textContent = '0';
                        if (quantityInput) {
                            quantityInput.max = 1;
                            quantityInput.value = '';
                            quantityInput.disabled = true;
                        }
                    }
                });
            } else {
                select.onchange = function() {
                    const selected = this.options[this.selectedIndex];
                    const availableSpan = document.getElementById('selectedBookAvailable');
                    const quantityInput = document.getElementById('bookQuantity');
                    
                    if (selected && selected.value) {
                        if (availableSpan) availableSpan.textContent = selected.dataset.available || '0';
                        if (quantityInput) {
                            quantityInput.max = selected.dataset.available || 1;
                            quantityInput.value = '';
                            quantityInput.disabled = false;
                        }
                    } else {
                        if (availableSpan) availableSpan.textContent = '0';
                        if (quantityInput) {
                            quantityInput.max = 1;
                            quantityInput.value = '';
                            quantityInput.disabled = true;
                        }
                    }
                };
                if (select.onchange) select.onchange();
            }
        }

        function openAddItemSubForm(type) {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'block';
            if (showAddBookBtn) showAddBookBtn.style.display = 'none';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'none';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'none';
            
            // Set type select value
            const itemTypeSelect = document.getElementById('itemTypeSelect');
            if (itemTypeSelect) itemTypeSelect.value = type;
            
            // Update title & select label
            const titleEl = document.getElementById('addBookFormTitle');
            const labelEl = document.getElementById('itemSelectLabel');

            if (type === 'book') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-book me-2"></i>Add New Book';
                if (labelEl) labelEl.textContent = 'Select Book *';
            } else if (type === 'index') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-pen me-2"></i>Add New Index';
                if (labelEl) labelEl.textContent = 'Select Index *';
            } else if (type === 'bundle') {
                if (titleEl) titleEl.innerHTML = '<i class="las la-cubes me-2"></i>Add New Bundle';
                if (labelEl) labelEl.textContent = 'Select Bundle *';
            }

            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        }

        document.getElementById('showAddBookBtn')?.addEventListener('click', function() {
            openAddItemSubForm('book');
        });
        document.getElementById('showAddIndexBtn')?.addEventListener('click', function() {
            openAddItemSubForm('index');
        });
        document.getElementById('showAddBundleBtn')?.addEventListener('click', function() {
            openAddItemSubForm('bundle');
        });

        document.getElementById('closeAddBookForm')?.addEventListener('click', function() {
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'block';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'block';
            
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');
            if (bookSelect) bookSelect.value = '';
            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.disabled = true;
            }
        });

        document.getElementById('confirmAddBookBtn')?.addEventListener('click', function() {
            const bookSelect = document.getElementById('bookSelect');
            const quantityInput = document.getElementById('bookQuantity');

            if (!bookSelect || !bookSelect.value) {
                showNotification('Please select an item', 'error');
                return;
            }

            if (!quantityInput || !quantityInput.value || parseInt(quantityInput.value) < 1) {
                showNotification('Please enter a valid quantity', 'error');
                return;
            }

            const itemId = parseInt(bookSelect.value);
            const selectedOption = bookSelect.options[bookSelect.selectedIndex];
            const bookName = selectedOption.dataset.name;
            const available = parseInt(selectedOption.dataset.available);
            const type = selectedOption.dataset.type;
            const quantity = parseInt(quantityInput.value);

            if (quantity > available) {
                showNotification(`Insufficient stock. Available: ${available}`, 'error');
                return;
            }

            addBookToTransfer(itemId, type, bookName, quantity, available);

            bookSelect.value = '';
            quantityInput.value = '';
            quantityInput.disabled = true;
            const availableSpan = document.getElementById('selectedBookAvailable');
            if (availableSpan) availableSpan.textContent = '0';
            
            const addBookForm = document.getElementById('addBookForm');
            const showAddBookBtn = document.getElementById('showAddBookBtn');
            const showAddIndexBtn = document.getElementById('showAddIndexBtn');
            const showAddBundleBtn = document.getElementById('showAddBundleBtn');
            if (addBookForm) addBookForm.style.display = 'none';
            if (showAddBookBtn) showAddBookBtn.style.display = 'block';
            if (showAddIndexBtn) showAddIndexBtn.style.display = 'block';
            if (showAddBundleBtn) showAddBundleBtn.style.display = 'block';
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        });

        function addBookToTransfer(itemId, type, bookName, quantity, available) {
            const key = type + '_' + itemId;
            if (selectedBooksMap[key]) {
                showNotification('This item is already added', 'error');
                return;
            }

            selectedBooksMap[key] = {
                itemId: itemId,
                type: type,
                name: bookName,
                quantity: quantity,
                available: available,
                rowId: nextRowId++
            };

            renderSelectedBooks();
            updateSubmitButton();
            showNotification(`${bookName} added to transfer list`, 'success');
        }

        window.removeBookFromTransfer = function(key) {
            delete selectedBooksMap[key];
            renderSelectedBooks();
            updateSubmitButton();
            
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            if (fromSiteId) {
                populateBookSelect(parseInt(fromSiteId));
            }
        };
        
        window.updateBookQuantity = function(key, newQuantity) {
            const book = selectedBooksMap[key];
            if (!book) return;
            
            if (newQuantity < 1) {
                showNotification('Quantity must be at least 1', 'error');
                return;
            }
            
            if (newQuantity > book.available) {
                showNotification(`Maximum quantity available: ${book.available}`, 'error');
                return;
            }
            
            book.quantity = newQuantity;
            renderSelectedBooks();
            updateSubmitButton();
        };

        function renderSelectedBooks() {
            const tbody = document.getElementById('transferBooksBody');
            if (!tbody) return;

            if (Object.keys(selectedBooksMap).length === 0) {
                tbody.innerHTML = '<tr id="emptyBooksRow"><td colspan="4" class="text-center text-muted py-3">No items added. Click "Add Book" to start.</td></tr>';
                return;
            }

            tbody.innerHTML = Object.entries(selectedBooksMap).map(([key, book]) => `
                <tr data-key="${key}">
                    <td><strong>${escapeHtml(book.name)} <span class="badge badge-xs bg-secondary text-uppercase">${book.type}</span></strong></td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm" 
                               value="${book.quantity}" 
                               min="1" 
                               max="${book.available}"
                               style="width: 100px;"
                               onchange="updateBookQuantity('${key}', parseInt(this.value))">
                    </td>
                    <td><span class="badge bg-info">${book.available} available</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBookFromTransfer('${key}')" title="Remove">
                            <i class="las la-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitTransferBtn');
            const bookCount = Object.keys(selectedBooksMap).length;
            const fromSiteId = document.getElementById('fromSiteSelect').value;
            const toSiteId = document.getElementById('toSiteSelect').value;
            
            if (submitBtn) {
                submitBtn.disabled = bookCount === 0 || !fromSiteId || !toSiteId || fromSiteId === toSiteId;
                submitBtn.innerHTML = `<i class="las la-check me-1"></i>Request Transfer (${bookCount} item${bookCount !== 1 ? 's' : ''})`;
            }
        }
        
        document.getElementById('toSiteSelect')?.addEventListener('change', function() {
            updateSubmitButton();
        });

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        window.approveTransfer = function(transferId) {
            if (confirm('Approve this transfer?')) {
                fetch(`/stock-transfers/${transferId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Transfer approved!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        window.rejectTransfer = function(transferId) {
            if (typeof window.openTwoStepRejectionFlow === 'function') {
                window.openTwoStepRejectionFlow('', function(reason) {
                    const formData = new FormData();
                    formData.append('rejection_reason', reason);
                    formData.append('remarks', reason);

                    fetch(`/stock-transfers/${transferId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Transfer rejected!', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification('Error: ' + data.message, 'error');
                        }
                    });
                });
            }
        };

        window.accountingApproveTransfer = function(transferId) {
            if (confirm('Approve this transfer from Accounting and forward to Logistics?')) {
                fetch(`/stock-transfers/${transferId}/accounting-approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Transfer forwarded to Logistics!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        window.assignLogisticsTransfer = function(transferId) {
            const select = document.getElementById(`assignLogistics${transferId}`);
            const logisticsUserId = select ? select.value : '';

            if (!logisticsUserId) {
                showNotification('Please select a logistics staff.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('logistics_assigned_to', logisticsUserId);

            fetch(`/stock-transfers/${transferId}/assign-logistics`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Transfer assigned!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(() => {
                showNotification('An error occurred', 'error');
            });
        };

        window.completeLogisticsTransfer = function(transferId) {
            if (confirm('Mark this stock transfer as completed? This will move the stock now.')) {
                fetch(`/stock-transfers/${transferId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Transfer completed!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('An error occurred', 'error');
                });
            }
        };

        // --- BOOK INDEX STOCK MANAGEMENT ---
        let currentIndexId = null;
        let currentIndexStock = 0;

        window.openIndexStockModal = function(indexId, bookName, indexValue, stock) {
            console.log("openIndexStockModal called", {indexId, bookName, indexValue, stock});
            try {
                currentIndexId = indexId;
                currentIndexStock = stock;

                const bookNameInput = document.getElementById('mgmtIndexBookName');
                if (bookNameInput) bookNameInput.value = bookName;

                const indexValueInput = document.getElementById('mgmtIndexValue');
                if (indexValueInput) indexValueInput.value = indexValue;

                const currentStockInput = document.getElementById('mgmtIndexCurrentStock');
                if (currentStockInput) currentStockInput.value = stock;

                const addQtyInput = document.getElementById('mgmtIndexAddQuantity');
                if (addQtyInput) addQtyInput.value = '';

                const addPreview = document.getElementById('mgmtIndexAddPreview');
                if (addPreview) addPreview.style.display = 'none';

                const editQtyInput = document.getElementById('mgmtIndexEditQuantity');
                if (editQtyInput) editQtyInput.value = '';

                const editPreview = document.getElementById('mgmtIndexEditPreview');
                if (editPreview) editPreview.style.display = 'none';

                // Hook preview events
                if (addQtyInput) {
                    addQtyInput.oninput = function() {
                        const qty = parseInt(this.value) || 0;
                        const preview = document.getElementById('mgmtIndexAddPreview');
                        if (qty > 0) {
                            if (preview) preview.style.display = 'block';
                            const addNewStock = document.getElementById('mgmtIndexAddNewStock');
                            if (addNewStock) addNewStock.textContent = currentIndexStock + qty;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                if (editQtyInput) {
                    editQtyInput.oninput = function() {
                        const val = parseInt(this.value);
                        const preview = document.getElementById('mgmtIndexEditPreview');
                        if (!isNaN(val) && val >= 0) {
                            if (preview) preview.style.display = 'block';
                            const editOldStock = document.getElementById('mgmtIndexEditOldStock');
                            if (editOldStock) editOldStock.textContent = currentIndexStock;
                            const editNewStock = document.getElementById('mgmtIndexEditNewStock');
                            if (editNewStock) editNewStock.textContent = val;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                const modalEl = document.getElementById('indexStockModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("indexStockModal show called successfully");
                } else {
                    console.error("indexStockModal element not found in DOM");
                }
            } catch (err) {
                console.error("Error in openIndexStockModal:", err);
            }
        };

        window.saveIndexStockManagement = function() {
            const activeTab = document.querySelector('#indexStockModal .nav-link.active');
            let action = 'add';
            let qty = 0;
            let newStock = 0;

            if (activeTab && activeTab.id === 'idxAddTab') {
                action = 'add';
                qty = parseInt(document.getElementById('mgmtIndexAddQuantity').value);
                if (!qty || qty < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }
            } else {
                action = 'set';
                newStock = parseInt(document.getElementById('mgmtIndexEditQuantity').value);
                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid new stock value', 'warning');
                    return;
                }
            }

            const saveBtn = document.getElementById('mgmtIndexSaveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            fetch(`/production/inventory/update-index-stock/${currentIndexId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    quantity: qty,
                    new_stock: newStock
                })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showNotification(data.message || 'Index stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('indexStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Failed to update index stock';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    showNotification('Error: ' + msg, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving index stock', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        };

        // --- BOOK BUNDLE STOCK MANAGEMENT ---
        let currentBundleId = null;
        let currentBundleStock = 0;

        window.openBundleStockModal = function(bundleId, bundleName, stock) {
            console.log("openBundleStockModal called", {bundleId, bundleName, stock});
            try {
                currentBundleId = bundleId;
                currentBundleStock = stock;

                const bundleNameInput = document.getElementById('mgmtBundleName');
                if (bundleNameInput) bundleNameInput.value = bundleName;

                const currentStockInput = document.getElementById('mgmtBundleCurrentStock');
                if (currentStockInput) currentStockInput.value = stock;

                const addQtyInput = document.getElementById('mgmtBundleAddQuantity');
                if (addQtyInput) addQtyInput.value = '';

                const addPreview = document.getElementById('mgmtBundleAddPreview');
                if (addPreview) addPreview.style.display = 'none';

                const editQtyInput = document.getElementById('mgmtBundleEditQuantity');
                if (editQtyInput) editQtyInput.value = '';

                const editPreview = document.getElementById('mgmtBundleEditPreview');
                if (editPreview) editPreview.style.display = 'none';

                // Hook preview events
                if (addQtyInput) {
                    addQtyInput.oninput = function() {
                        const qty = parseInt(this.value) || 0;
                        const preview = document.getElementById('mgmtBundleAddPreview');
                        if (qty > 0) {
                            if (preview) preview.style.display = 'block';
                            const addNewStock = document.getElementById('mgmtBundleAddNewStock');
                            if (addNewStock) addNewStock.textContent = currentBundleStock + qty;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                if (editQtyInput) {
                    editQtyInput.oninput = function() {
                        const val = parseInt(this.value);
                        const preview = document.getElementById('mgmtBundleEditPreview');
                        if (!isNaN(val) && val >= 0) {
                            if (preview) preview.style.display = 'block';
                            const editOldStock = document.getElementById('mgmtBundleEditOldStock');
                            if (editOldStock) editOldStock.textContent = currentBundleStock;
                            const editNewStock = document.getElementById('mgmtBundleEditNewStock');
                            if (editNewStock) editNewStock.textContent = val;
                        } else {
                            if (preview) preview.style.display = 'none';
                        }
                    };
                }

                const modalEl = document.getElementById('bundleStockModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    console.log("bundleStockModal show called successfully");
                } else {
                    console.error("bundleStockModal element not found in DOM");
                }
            } catch (err) {
                console.error("Error in openBundleStockModal:", err);
            }
        };

        window.saveBundleStockManagement = function() {
            const activeTab = document.querySelector('#bundleStockModal .nav-link.active');
            let action = 'add';
            let qty = 0;
            let newStock = 0;

            if (activeTab && activeTab.id === 'bndlAddTab') {
                action = 'add';
                qty = parseInt(document.getElementById('mgmtBundleAddQuantity').value);
                if (!qty || qty < 1) {
                    showNotification('Please enter a valid quantity to add', 'warning');
                    return;
                }
            } else {
                action = 'set';
                newStock = parseInt(document.getElementById('mgmtBundleEditQuantity').value);
                if (isNaN(newStock) || newStock < 0) {
                    showNotification('Please enter a valid new stock value', 'warning');
                    return;
                }
            }

            const saveBtn = document.getElementById('mgmtBundleSaveBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            fetch(`/production/inventory/update-bundle-stock/${currentBundleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    quantity: qty,
                    new_stock: newStock
                })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showNotification(data.message || 'Bundle stock updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('bundleStockModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let msg = data.message || 'Failed to update bundle stock';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join(' ');
                    }
                    showNotification('Error: ' + msg, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving bundle stock', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        };

        // --- TAB STATE PERSISTENCE ---
        document.addEventListener('DOMContentLoaded', function() {
            // Page-level tabs configuration
            const pageTabs = ['stocks-tab', 'sites-tab', 'transfer-workflow-tab'];
            pageTabs.forEach(tabId => {
                const button = document.getElementById(tabId);
                button?.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('active_inventory_overview_tab', event.target.id);
                });
            });

            // Restore page-level tab
            const urlParams = new URLSearchParams(window.location.search);
            const hasSiteSearch = urlParams.has('site_search') || urlParams.has('sites_page');
            const savedPageTabId = hasSiteSearch ? 'sites-tab' : localStorage.getItem('active_inventory_overview_tab');
            if (savedPageTabId && pageTabs.includes(savedPageTabId)) {
                const tabButton = document.getElementById(savedPageTabId);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Card-level nested registry tabs configuration
            const registryTabs = ['registry-books-tab', 'registry-nonbooks-tab', 'registry-indices-tab', 'registry-bundles-tab'];
            registryTabs.forEach(tabId => {
                const button = document.getElementById(tabId);
                button?.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('active_inventory_registry_tab', event.target.id);
                });
            });

            // Restore card-level nested registry tab
            const savedRegistryTabId = localStorage.getItem('active_inventory_registry_tab');
            if (savedRegistryTabId && registryTabs.includes(savedRegistryTabId)) {
                const tabButton = document.getElementById(savedRegistryTabId);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }

            // Site Inventory Modal Client-side Pagination (Sliding Window)
            function initSiteTablePagination(tableId, pageSize = 10) {
                const table = document.getElementById(tableId);
                if (!table) return;
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('tr.paginate-row'));
                if (rows.length === 0) return;

                let currentPage = 1;
                const totalPages = Math.ceil(rows.length / pageSize);

                let container = document.getElementById(tableId + '_pagination');
                if (!container) {
                    container = document.createElement('div');
                    container.id = tableId + '_pagination';
                    container.className = 'd-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top gap-2';
                    table.parentNode.appendChild(container);
                }

                function render() {
                    const start = (currentPage - 1) * pageSize;
                    const end = start + pageSize;

                    rows.forEach((row, idx) => {
                        row.style.display = (idx >= start && idx < end) ? '' : 'none';
                    });

                    const showingStart = Math.min(start + 1, rows.length);
                    const showingEnd = Math.min(end, rows.length);

                    let html = `<small class="text-muted fw-bold">Showing ${showingStart} to ${showingEnd} of ${rows.length} entries</small>`;
                    
                    if (totalPages > 1) {
                        html += `<ul class="pagination pagination-sm m-0 flex-wrap">`;
                        
                        // Previous button
                        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <button class="page-link py-1 px-2" type="button" data-page="${currentPage - 1}">Prev</button>
                        </li>`;

                        // Smart sliding window for page numbers
                        let pages = [];
                        if (totalPages <= 7) {
                            for (let i = 1; i <= totalPages; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            if (currentPage > 3) {
                                pages.push('...');
                            }
                            
                            let startPage = Math.max(2, currentPage - 1);
                            let endPage = Math.min(totalPages - 1, currentPage + 1);

                            if (currentPage <= 3) {
                                endPage = 4;
                            }
                            if (currentPage >= totalPages - 2) {
                                startPage = totalPages - 3;
                            }

                            for (let i = startPage; i <= endPage; i++) {
                                pages.push(i);
                            }

                            if (currentPage < totalPages - 2) {
                                pages.push('...');
                            }
                            pages.push(totalPages);
                        }

                        pages.forEach(p => {
                            if (p === '...') {
                                html += `<li class="page-item disabled"><span class="page-link py-1 px-2">...</span></li>`;
                            } else {
                                html += `<li class="page-item ${currentPage === p ? 'active' : ''}">
                                    <button class="page-link py-1 px-2" type="button" data-page="${p}">${p}</button>
                                </li>`;
                            }
                        });

                        // Next button
                        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <button class="page-link py-1 px-2" type="button" data-page="${currentPage + 1}">Next</button>
                        </li>`;
                        
                        html += `</ul>`;
                    }

                    container.innerHTML = html;

                    container.querySelectorAll('button.page-link').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const p = parseInt(this.getAttribute('data-page'));
                            if (p >= 1 && p <= totalPages) {
                                currentPage = p;
                                render();
                            }
                        });
                    });
                }

                render();
            }

            document.querySelectorAll('[id^="viewSiteInventory"]').forEach(modalEl => {
                const siteId = modalEl.id.replace('viewSiteInventory', '');
                
                function initAllTabs() {
                    initSiteTablePagination(`site-books-table-${siteId}`, 6);
                    initSiteTablePagination(`site-indices-table-${siteId}`, 6);
                    initSiteTablePagination(`site-bundles-table-${siteId}`, 6);
                }

                modalEl.addEventListener('shown.bs.modal', initAllTabs);

                modalEl.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabBtn => {
                    tabBtn.addEventListener('shown.bs.tab', initAllTabs);
                });
            });

            document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const titleEl = document.getElementById('registryHeaderTitle');
                    if (titleEl) {
                        const titleMap = {
                            'registry-allsites-tab': 'All Sites Breakdown',
                            'registry-consignment-tab': 'Consignment Inventory',
                        };
                        titleEl.textContent = titleMap[e.target.id] || 'Master Registry';
                    }
                });
            });

            // Initialize Stock Transfer Workflow DataTable (with search and pagination)
            if ($('#stockTransferWorkflowTable').length > 0 && typeof $.fn.DataTable !== 'undefined') {
                if (!$.fn.DataTable.isDataTable('#stockTransferWorkflowTable')) {
                    const stwTable = $('#stockTransferWorkflowTable').DataTable({
                        order: [[0, 'desc']],
                        pageLength: 10,
                        columnDefs: [{ orderable: false, targets: -1 }]
                    });

                    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
                        tab.addEventListener('shown.bs.tab', function(e) {
                            if (e.target.id === 'transfer-workflow-tab' || e.target.getAttribute('href') === '#transfer-workflow-content') {
                                stwTable.columns.adjust().draw();
                            }
                        });
                    });
                }
            }
        });
        window.switchConsignmentSubTab = function(btn, targetPaneId) {
            $('.c-sub-btn').removeClass('active btn-danger').addClass('btn-outline-danger');
            $(btn).addClass('active btn-danger').removeClass('btn-outline-danger');
            $('#area-consignment-pane, #direct-consignment-pane').removeClass('show active').css('display', 'none');
            $('#' + targetPaneId).addClass('show active').css('display', 'block');
        };

        window.reconcileStockUI = function() {
            if (!confirm('Recalculate and synchronize all Master Book Stock levels with Warehouse Inventory?')) {
                return;
            }
            fetch('1', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to reconcile stock'));
                }
            })
            .catch(err => {
                alert('Error: ' + err.message);
            });
        };
    
