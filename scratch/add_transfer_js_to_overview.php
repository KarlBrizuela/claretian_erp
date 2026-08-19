<?php

$filePath = __DIR__ . '/../resources/views/production/inventory/overview.blade.php';
$content = file_get_contents($filePath);

$target = '    </script>
@endpush';

$pos = strrpos($content, $target);
if ($pos === false) {
    die("Target closing tag not found in overview.blade.php\n");
}

$transferJs = <<<'JS'

        // --- STOCK TRANSFER MODAL LOGIC ---
        let transferItemsList = [];
        let currentSiteInventory = [];

        window.initTransferModalFromMaster = function() {
            const fromSelect = document.getElementById('fromSiteSelect');
            if (fromSelect) {
                let mainWhOpt = [...fromSelect.options].find(opt => opt.text.trim() === 'Main Warehouse');
                if (mainWhOpt) {
                    fromSelect.value = mainWhOpt.value;
                } else if (fromSelect.options.length > 1) {
                    fromSelect.selectedIndex = 1;
                }
                $(fromSelect).trigger('change');
            }
            resetTransferModal();
        };

        function resetTransferModal() {
            transferItemsList = [];
            renderTransferTable();
            const form = document.getElementById('addBookForm');
            if (form) form.style.display = 'none';
        }

        $('#fromSiteSelect').on('change', function() {
            const siteId = parseInt(this.value);
            const addBookBtn = document.getElementById('showAddBookBtn');
            const addIndexBtn = document.getElementById('showAddIndexBtn');
            const addBundleBtn = document.getElementById('showAddBundleBtn');

            if (!siteId) {
                if (addBookBtn) addBookBtn.disabled = true;
                if (addIndexBtn) addIndexBtn.disabled = true;
                if (addBundleBtn) addBundleBtn.disabled = true;
                currentSiteInventory = [];
                resetTransferModal();
                return;
            }

            if (addBookBtn) addBookBtn.disabled = false;
            if (addIndexBtn) addIndexBtn.disabled = false;
            if (addBundleBtn) addBundleBtn.disabled = false;

            currentSiteInventory = sitesInventoryData[siteId] || [];
            resetTransferModal();
        });

        function openAddItemForm(type) {
            const form = document.getElementById('addBookForm');
            const title = document.getElementById('addBookFormTitle');
            const label = document.getElementById('itemSelectLabel');
            const select = document.getElementById('bookSelect');
            const typeSelect = document.getElementById('itemTypeSelect');

            if (typeSelect) typeSelect.value = type;

            if (type === 'book') {
                if (title) title.innerHTML = '<i class="las la-plus-circle me-2"></i>Add Book / Non-Book';
                if (label) label.textContent = 'Select Book *';
            } else if (type === 'index') {
                if (title) title.innerHTML = '<i class="las la-plus-circle me-2"></i>Add Book Index';
                if (label) label.textContent = 'Select Index *';
            } else if (type === 'bundle') {
                if (title) title.innerHTML = '<i class="las la-plus-circle me-2"></i>Add Book Bundle';
                if (label) label.textContent = 'Select Bundle *';
            }

            if (select) {
                select.innerHTML = '<option value="">-- Select an Item --</option>';

                if (type === 'book') {
                    const books = @json($allBooks ?? []);
                    books.forEach(b => {
                        const invItem = currentSiteInventory.find(i => i.book_id === b.id);
                        const available = invItem ? invItem.quantity : b.stock;
                        select.innerHTML += `<option value="${b.id}" data-name="${escapeHtml(b.name)}" data-available="${available}">${b.name} (Stock: ${available})</option>`;
                    });
                } else if (type === 'index') {
                    const indices = @json($bookIndices ?? []);
                    indices.forEach(idx => {
                        const invItem = currentSiteInventory.find(i => i.book_index_id === idx.id);
                        const available = invItem ? invItem.quantity : idx.stock;
                        const idxName = idx.name || idx.title || ('Index #' + idx.id);
                        select.innerHTML += `<option value="${idx.id}" data-name="${escapeHtml(idxName)}" data-available="${available}">${idxName} (Stock: ${available})</option>`;
                    });
                } else if (type === 'bundle') {
                    const bundles = @json($bookBundles ?? []);
                    bundles.forEach(bd => {
                        const invItem = currentSiteInventory.find(i => i.book_bundle_id === bd.id);
                        const available = invItem ? invItem.quantity : bd.stock;
                        select.innerHTML += `<option value="${bd.id}" data-name="${escapeHtml(bd.name)}" data-available="${available}">${bd.name} (Stock: ${available})</option>`;
                    });
                }
            }

            const qtyInput = document.getElementById('bookQuantity');
            if (qtyInput) {
                qtyInput.value = '';
                qtyInput.disabled = true;
            }
            if (document.getElementById('selectedBookAvailable')) {
                document.getElementById('selectedBookAvailable').textContent = '0';
            }
            if (form) form.style.display = 'block';
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        document.getElementById('showAddBookBtn')?.addEventListener('click', () => openAddItemForm('book'));
        document.getElementById('showAddIndexBtn')?.addEventListener('click', () => openAddItemForm('index'));
        document.getElementById('showAddBundleBtn')?.addEventListener('click', () => openAddItemForm('bundle'));
        document.getElementById('closeAddBookForm')?.addEventListener('click', () => {
            const form = document.getElementById('addBookForm');
            if (form) form.style.display = 'none';
        });

        $('#bookSelect').on('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const qtyInput = document.getElementById('bookQuantity');
            const availSpan = document.getElementById('selectedBookAvailable');

            if (!this.value || !selectedOpt) {
                if (qtyInput) {
                    qtyInput.disabled = true;
                    qtyInput.value = '';
                }
                if (availSpan) availSpan.textContent = '0';
                return;
            }

            const available = parseInt(selectedOpt.getAttribute('data-available')) || 0;
            if (availSpan) availSpan.textContent = available;
            if (qtyInput) {
                qtyInput.disabled = false;
                qtyInput.max = available;
                qtyInput.value = 1;
            }
        });

        document.getElementById('confirmAddBookBtn')?.addEventListener('click', function() {
            const typeSelect = document.getElementById('itemTypeSelect');
            const type = typeSelect ? typeSelect.value : 'book';
            const select = document.getElementById('bookSelect');
            const itemId = select ? parseInt(select.value) : 0;
            const qtyInput = document.getElementById('bookQuantity');
            const qty = qtyInput ? parseInt(qtyInput.value) : 0;

            if (!itemId || !select) {
                showNotification('Please select an item', 'warning');
                return;
            }

            const selectedOpt = select.options[select.selectedIndex];
            const name = selectedOpt ? selectedOpt.getAttribute('data-name') : 'Item';
            const available = selectedOpt ? (parseInt(selectedOpt.getAttribute('data-available')) || 0) : 0;

            if (!qty || qty < 1) {
                showNotification('Please enter a valid quantity', 'warning');
                return;
            }

            if (qty > available) {
                showNotification(`Quantity (${qty}) exceeds available stock (${available})`, 'warning');
                return;
            }

            const existing = transferItemsList.find(i => i.id === itemId && i.type === type);
            if (existing) {
                existing.quantity = qty;
            } else {
                transferItemsList.push({
                    id: itemId,
                    type: type,
                    name: name,
                    quantity: qty,
                    available: available
                });
            }

            renderTransferTable();
            const form = document.getElementById('addBookForm');
            if (form) form.style.display = 'none';
            showNotification(`Added ${name} (${qty} pcs) to transfer list`, 'success');
        });

        function renderTransferTable() {
            const tbody = document.getElementById('transferBooksBody');
            const submitBtn = document.getElementById('submitTransferBtn');

            if (!tbody) return;

            if (transferItemsList.length === 0) {
                tbody.innerHTML = `<tr id="emptyBooksRow">
                    <td colspan="4" class="text-center text-muted py-3">Select a source site above, then click an Add button to start.</td>
                </tr>`;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="las la-check me-1"></i>Request Transfer (0 books)';
                }
                return;
            }

            let html = '';
            let totalQty = 0;
            transferItemsList.forEach((item, index) => {
                totalQty += item.quantity;
                const badgeColor = item.type === 'book' ? 'primary' : (item.type === 'index' ? 'info' : 'warning');
                html += `<tr>
                    <td>
                        <strong class="text-dark">${item.name}</strong>
                        <span class="badge bg-${badgeColor} ms-1" style="font-size: 0.7rem;">${item.type.toUpperCase()}</span>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" value="${item.quantity}" min="1" max="${item.available}" onchange="updateTransferItemQty(${index}, this.value)" style="width: 80px;">
                    </td>
                    <td class="align-middle fw-semibold text-muted">${item.available} pcs</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTransferItem(${index})">
                            <i class="las la-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });

            tbody.innerHTML = html;

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="las la-check me-1"></i>Request Transfer (${transferItemsList.length} items · ${totalQty} pcs)`;
            }
        }

        window.updateTransferItemQty = function(index, newQty) {
            const qty = parseInt(newQty);
            if (transferItemsList[index]) {
                if (qty > 0 && qty <= transferItemsList[index].available) {
                    transferItemsList[index].quantity = qty;
                } else if (qty > transferItemsList[index].available) {
                    showNotification(`Quantity cannot exceed available stock (${transferItemsList[index].available})`, 'warning');
                    transferItemsList[index].quantity = transferItemsList[index].available;
                }
                renderTransferTable();
            }
        };

        window.removeTransferItem = function(index) {
            transferItemsList.splice(index, 1);
            renderTransferTable();
        };

        document.getElementById('transferStockForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const fromSiteId = document.getElementById('fromSiteSelect')?.value;
            const toSiteId = document.getElementById('toSiteSelect')?.value;
            const notes = this.querySelector('[name="notes"]')?.value || '';

            if (!fromSiteId || !toSiteId) {
                showNotification('Please select both From and To sites', 'warning');
                return;
            }

            if (fromSiteId === toSiteId) {
                showNotification('Source and Destination sites cannot be the same', 'warning');
                return;
            }

            if (transferItemsList.length === 0) {
                showNotification('Please add at least one item to transfer', 'warning');
                return;
            }

            const submitBtn = document.getElementById('submitTransferBtn');
            const originalHTML = submitBtn ? submitBtn.innerHTML : 'Submit';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>Submitting...';
            }

            fetch('/production/sites/transfer-batch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    from_site_id: fromSiteId,
                    to_site_id: toSiteId,
                    notes: notes,
                    items: transferItemsList
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Stock transfer request submitted successfully!', 'success');
                    closeModal('transferStockModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error: ' + (data.message || 'Failed to submit transfer request'), 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHTML;
                    }
                }
            })
            .catch(err => {
                console.error('Transfer submit error:', err);
                showNotification('An error occurred while submitting transfer request', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            });
        });
JS;

$newContent = substr($content, 0, $pos) . $transferJs . "\n" . $target . "\n";
file_put_contents($filePath, $newContent);
echo "SUCCESSFULLY ADDED TRANSFER STOCK JS LOGIC TO overview.blade.php!\n";
