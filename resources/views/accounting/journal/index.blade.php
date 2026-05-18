<x-app-layout :title="'General Journal Entries'" :sidebar="'admin-finance'">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Recent Journal Entries</h4>
                    <a href="{{ route('accounting.journal.create') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-plus"></i>New Entry
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>Entry No.</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Memo</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr>
                                    <td><strong>{{ $entry->entry_no }}</strong></td>
                                    <td>{{ date('M d, Y', strtotime($entry->date)) }}</td>
                                    <td>{{ $entry->reference ?? 'N/A' }}</td>
                                    <td><span class="text-limit" title="{{ $entry->memo }}">{{ $entry->memo ?? '---' }}</span></td>
                                    <td>{{ $entry->creator->name ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="badge light badge-success">
                                            <i class="fa fa-circle text-success me-1"></i>
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('accounting.journal.show', $entry->id) }}" class="btn btn-primary shadow btn-xs sharp me-1" title="View"><i class="fas fa-eye"></i></a>
                                            <button type="button" class="btn btn-danger shadow btn-xs sharp" 
                                                data-toggle="modal"
                                                data-bs-toggle="modal" 
                                                data-target="#deleteModal"
                                                data-bs-target="#deleteModal" 
                                                data-url="{{ route('accounting.journal.destroy', $entry->id) }}"
                                                data-entry-no="{{ $entry->entry_no }}"
                                                title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No journal entries found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete journal entry <strong id="modalEntryNo"></strong>? This action will reverse all associated ledger postings and cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Yes, Delete Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                // Support both BS4 (show.bs.modal) and BS5 (show.bs.modal is the same, but let's be explicit if needed)
                const handleModalShow = function(event) {
                    const button = event.relatedTarget;
                    const url = button.getAttribute('data-url');
                    const entryNo = button.getAttribute('data-entry-no');
                    const form = deleteModal.querySelector('#deleteForm');
                    const entrySpan = deleteModal.querySelector('#modalEntryNo');
                    
                    form.setAttribute('action', url);
                    entrySpan.textContent = entryNo;
                };

                // Vanilla JS works for both if event is bubbled correctly
                deleteModal.addEventListener('show.bs.modal', handleModalShow);
                
                // If jQuery is available (common in many Laravel templates), use it to ensure compatibility
                if (window.jQuery) {
                    $('#deleteModal').on('show.bs.modal', handleModalShow);
                }
            }
        });
    </script>
    @endpush

    <style>
        .text-limit {
            display: inline-block;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</x-app-layout>
