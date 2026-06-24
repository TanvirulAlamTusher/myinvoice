@extends('app')

@section('title', 'Brands')

@section('content')

    <div class="page-layout">

        <div class="card card-full">

            {{-- ================= HEADER ================= --}}
            <div class="card-header">

                <div>
                    <h1 class="page-title">Brands</h1>
                    <p class="text-muted mt-4">Manage product brands</p>
                </div>

                @can('brand.create')
                    <button class="btn btn-primary" onclick="openModal('createModal')">
                        ➕ Add Brand
                    </button>
                @endcan

            </div>

            <div class="divider"></div>

            {{-- ================= EMPTY ================= --}}
            @if ($brands->isEmpty())

                <div style="text-align:center; padding: 60px 20px;">
                    <p class="text-muted">No brands found</p>
                    @can('brand.create')
                        <button class="btn btn-ghost mt-4" onclick="openModal('createModal')">
                            Create First Brand
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
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($brands as $key => $brand)
                                <tr id="row-{{ $brand->id }}">

                                    <td class="text-muted">{{ $key + 1 }}</td>

                                    {{-- IMAGE --}}
                                    <td>
                                        <img src="{{ $brand->image ? asset('storage/' . $brand->image) : asset('no-image.png') }}"
                                            style="width:45px;height:45px;border-radius:10px;object-fit:cover;border:1px solid var(--border);">
                                    </td>

                                    {{-- NAME --}}
                                    <td><strong>{{ $brand->name }}</strong></td>

                                    {{-- DESCRIPTION --}}
                                    <td class="text-muted">
                                        {{ $brand->description ?? '—' }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        @if ($brand->is_active)
                                            <span class="badge badge-teal">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td>

                                        <div class="flex gap-2">
                                            @can('brand.edit')
                                                {{-- EDIT --}}
                                                <button class="btn btn-ghost btn-icon" onclick="editBrand({{ $brand->id }})">
                                                    ✏️
                                                </button>
                                            @endcan
                                            {{-- DELETE (USING GLOBAL MODAL) --}}
                                            @can('brand.delete')
                                                <button class="btn btn-danger btn-icon"
                                                    onclick="openDeleteModal('{{ route('brands.destroy', $brand->id) }}','{{ $brand->name }}')">
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

            <h3 class="modal-title">Create Brand</h3>

            <form id="createForm" enctype="multipart/form-data">

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" class="no-icon" required>
                </div>

                <div class="field">
                    <label>Image</label>
                    <input type="file" name="image" class="no-icon" accept="image/*">
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea name="description" class="no-icon"></textarea>
                </div>

                <label class="remember">
                    <input type="checkbox" name="is_active" value="1" checked>
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

            <h3 class="modal-title">Edit Brand</h3>

            <form id="editForm" enctype="multipart/form-data">

                <input type="hidden" id="edit_id">

                <div class="field">
                    <label>Name</label>
                    <input type="text" id="edit_name" class="no-icon" required>
                </div>

                <div class="field">
                    <label>Current Image</label>

                    <img id="edit_preview"
                        style="width:70px;height:70px;border-radius:10px;object-fit:cover;border:1px solid var(--border);margin-bottom:10px;">

                    <input type="file" id="edit_image" class="no-icon" accept="image/*">
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
        const appBaseUrl = @json(rtrim(request()->getBaseUrl(), '/'));

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

            axios.post(`${appBaseUrl}/brands`, new FormData(this))
                .then(res => {
                    closeModal('createModal');
                    toast(res.data.message, 'success', 2000);
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(err => {
                    toast(err.response?.data?.message || 'Error', 'error');
                });
        });


        /* ================= EDIT LOAD ================= */
        function editBrand(id) {

            axios.get(`${appBaseUrl}/brands/${id}/edit`)
                .then(res => {

                    let b = res.data;

                    document.getElementById('edit_id').value = b.id;
                    document.getElementById('edit_name').value = b.name;
                    document.getElementById('edit_description').value = b.description ?? '';
                    document.getElementById('edit_is_active').checked = b.is_active == 1;

                    document.getElementById('edit_preview').src =
                        b.image ? `${appBaseUrl}/storage/${b.image}` : `${appBaseUrl}/no-image.png`;

                    openModal('editModal');

                })
                .catch(() => {
                    toast('Failed to load brand', 'error');
                });
        }


        /* ================= UPDATE ================= */
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let id = document.getElementById('edit_id').value;

            let formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('edit_name').value);
            formData.append('description', document.getElementById('edit_description').value);
            formData.append('is_active', document.getElementById('edit_is_active').checked ? 1 : 0);

            let img = document.getElementById('edit_image').files[0];
            if (img) {
                formData.append('image', img);
            }

            axios.post(`${appBaseUrl}/brands/${id}`, formData)
                .then(res => {
                    closeModal('editModal');
                    toast(res.data.message, 'success', 2000);
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(err => {
                    toast(err.response?.data?.message || 'Error', 'error');
                });
        });
    </script>
@endpush
