@extends('app')

@section('title', 'Categories')

@section('content')

    <div class="page-layout">

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">Categories</h1>
                    <p class="text-muted mt-4">Manage product categories for invoices</p>
                </div>
                @can('category.create')
                    <button class="btn btn-primary" onclick="openModal('createModal')">

                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>

                        Add Category

                    </button>
                @endcan

            </div>

            <div class="divider"></div>

            {{-- ================= EMPTY ================= --}}
            @if ($categories->isEmpty())

                <div style="text-align:center; padding: 60px 20px;">
                    <p class="text-muted">No categories found</p>
                    @can('category.create')

                <button class="btn btn-ghost mt-4"
                            onclick="openModal('createModal')">
                            Create First Category
                            </button>
                        @endcan
                </div>
            @else
                {{-- ================= TABLE ================= --}}
                <div class="tbl-wrap">

                    <table class="tbl category-table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($categories as $key => $category)
                                <tr id="row-{{ $category->id }}">

                                    <td class="text-muted" data-label="#">{{ $key + 1 }}</td>

                                    <td data-label="Name"><strong>{{ $category->name }}</strong></td>

                                    <td class="text-muted" data-label="Description">{{ $category->description ?? '—' }}</td>

                                    <td data-label="Status">
                                        @if ($category->is_active)
                                            <span class="badge badge-teal">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="text-muted" data-label="Created">
                                        {{ $category->created_at->format('d M Y') }}
                                    </td>

                                    <td data-label="Actions">

                                        <div class="flex gap-2 category-actions">
                                            @can('category.edit')
                                                {{-- EDIT --}}
                                                <button class="btn btn-ghost btn-icon" type="button"
                                                    aria-label="Edit {{ $category->name }}"
                                                    onclick="editCategory({{ $category->id }})">

                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M12 20h9"></path>
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                                    </svg>

                                                </button>
                                            @endcan

                                            {{-- DELETE --}}
                                            @can('category.delete')
                                                <button class="btn btn-danger btn-icon" type="button"
                                                    aria-label="Delete {{ $category->name }}"
                                                    onclick="openDeleteModal('{{ route('category.destroy', $category->id) }}','{{ $category->name }}')">

                                                    <svg viewBox="0 0 24 24">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                    </svg>

                                                </button>
                                            @endcan

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>


    {{-- ================= CREATE MODAL ================= --}}
    <div id="createModal" class="modal-overlay hidden">

        <div class="modal-box">

            <h3 class="modal-title">Create Category</h3>

            <form id="createForm">

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" class="no-icon" required>
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea name="description" class="no-icon"></textarea>
                </div>

                <label class="remember">
                    <input type="checkbox" name="is_active" checked>
                    <span>Active</span>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('createModal')">Cancel</button>
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>

    </div>


    {{-- ================= EDIT MODAL ================= --}}
    <div id="editModal" class="modal-overlay hidden">

        <div class="modal-box">

            <h3 class="modal-title">Edit Category</h3>

            <form id="editForm">

                <input type="hidden" id="edit_id">

                <div class="field">
                    <label>Name</label>
                    <input type="text" id="edit_name" class="no-icon" required>
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea id="edit_description" class="no-icon"></textarea>
                </div>

                <label class="remember">
                    <input type="checkbox" id="edit_is_active">
                    <span>Active</span>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>

    </div>

@endsection


{{-- ================= SCRIPT ================= --}}
@push('scripts')
    <script>
        // open / close
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }


        // ================= CREATE =================
        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();

            axios.post("{{ route('category.store') }}", new FormData(this))
                .then(res => {
                    closeModal('createModal');
                    toast(res.data.message, 'success', 2000);
                    setTimeout(() => location.reload(), 500);
                })
                .catch(err => {
                    toast(err.response.data.message || 'Error', 'error');
                });
        });


        // ================= LOAD EDIT =================
        function editCategory(id) {

            axios.get(`/category/${id}/edit`)
                .then(res => {

                    document.getElementById('edit_id').value = res.data.id;
                    document.getElementById('edit_name').value = res.data.name;
                    document.getElementById('edit_description').value = res.data.description;
                    document.getElementById('edit_is_active').checked = res.data.is_active == 1;

                    openModal('editModal');

                });

        }


        // ================= UPDATE =================
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let id = document.getElementById('edit_id').value;

            axios.put(`/category/${id}`, {
                    name: document.getElementById('edit_name').value,
                    description: document.getElementById('edit_description').value,
                    is_active: document.getElementById('edit_is_active').checked ? 1 : 0,
                })
                .then(res => {
                    closeModal('editModal');
                    toast(res.data.message, 'success', 2000);
                    setTimeout(() => location.reload(), 500);
                })
                .catch(err => {
                    toast(err.response.data.message || 'Error', 'error');
                });
        });
    </script>
@endpush
