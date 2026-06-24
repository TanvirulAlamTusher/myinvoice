@extends('app')

@section('title', 'Product Units')

@section('content')

    <div class="page-layout">

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">Product Units</h1>
                    <p class="text-muted mt-4">Manage units like kg, pcs, box, liter</p>
                </div>
                @can('unit.create')
                    <button class="btn btn-primary" onclick="openModal('createModal')">
                        ➕ Add Unit
                    </button>
                @endcan

            </div>

            <div class="divider"></div>

            {{-- ================= EMPTY ================= --}}
            @if ($units->isEmpty())

                <div style="text-align:center; padding:60px 20px;">
                    <p class="text-muted">No units found</p>
                    @can('unit.create')
                        <button class="btn btn-ghost mt-4" onclick="openModal('createModal')">
                            Create First Unit
                        </button>
                    @endcan
                </div>
            @else
                {{-- ================= TABLE ================= --}}
                <div class="tbl-wrap">

                    <table class="tbl">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($units as $key => $unit)
                                <tr id="row-{{ $unit->id }}">

                                    <td class="text-muted">{{ $key + 1 }}</td>

                                    <td><strong>{{ $unit->name }}</strong></td>

                                    <td>
                                        @if ($unit->is_active)
                                            <span class="badge badge-teal">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        {{ $unit->created_at->format('d M Y') }}
                                    </td>

                                    <td>

                                        <div class="flex gap-2">

                                            {{-- EDIT --}}
                                            @can('unit.edit')
                                                <button class="btn btn-ghost btn-icon" onclick="editUnit({{ $unit->id }})">
                                                    ✏️
                                                </button>
                                            @endcan

                                            {{-- DELETE --}}
                                            @can('unit.delete')
                                                <button class="btn btn-danger btn-icon"
                                                    onclick="openDeleteModal('{{ route('product-units.destroy', $unit->id) }}','{{ $unit->name }}')">
                                                    🗑️
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

            <h3 class="modal-title">Create Unit</h3>

            <form id="createForm">

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" class="no-icon" required>
                </div>

                <label class="remember">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Active</span>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('createModal')">
                        Cancel
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>

    </div>

    {{-- ================= EDIT MODAL ================= --}}
    <div id="editModal" class="modal-overlay hidden">

        <div class="modal-box">

            <h3 class="modal-title">Edit Unit</h3>

            <form id="editForm">

                <input type="hidden" id="edit_id">

                <div class="field">
                    <label>Name</label>
                    <input type="text" id="edit_name" class="no-icon" required>
                </div>

                <label class="remember">
                    <input type="checkbox" id="edit_is_active">
                    <span>Active</span>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">
                        Cancel
                    </button>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>

    </div>

@endsection


{{-- ================= SCRIPT ================= --}}
@push('scripts')
    <script>
        /* ================= MODAL ================= */
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }


        /* ================= CREATE ================= */
        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();

            axios.post("{{ route('product-units.store') }}", new FormData(this))
                .then(res => {
                    toast(res.data.message, 'success');
                    location.reload();
                })
                .catch(err => {
                    toast(err.response?.data?.message || 'Error', 'error');
                });
        });


        /* ================= EDIT ================= */
        function editUnit(id) {

            axios.get(`/product-units/${id}/edit`)
                .then(res => {

                    document.getElementById('edit_id').value = res.data.id;
                    document.getElementById('edit_name').value = res.data.name;
                    document.getElementById('edit_is_active').checked = res.data.is_active == 1;

                    openModal('editModal');

                })
                .catch(() => {
                    toast('Failed to load unit', 'error');
                });
        }


        /* ================= UPDATE ================= */
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let id = document.getElementById('edit_id').value;

            axios.put(`/product-units/${id}`, {
                    name: document.getElementById('edit_name').value,
                    is_active: document.getElementById('edit_is_active').checked ? 1 : 0,
                })
                .then(res => {
                    toast(res.data.message, 'success');
                    location.reload();
                })
                .catch(err => {
                    toast(err.response?.data?.message || 'Error', 'error');
                });

        });
    </script>
@endpush
