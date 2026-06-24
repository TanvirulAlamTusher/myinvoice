{{-- ================= EDIT MODAL ================= --}}
<div id="editModal" class="modal-overlay hidden">

    <div class="modal-box" style="width:min(720px, 92vw);">

        <h3 class="modal-title">Edit Customer</h3>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                <div class="field">
                    <label>Name</label>
                    <input type="text" id="edit_name" name="name" class="no-icon" required>
                </div>

                <div class="field">
                    <label>Business Name</label>
                    <input type="text" id="edit_business_name" name="business_name" class="no-icon" >
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="number" id="edit_phone" name="phone" class="no-icon" required>
                </div>

                <div class="field">
                    <label>Alternative Phone</label>
                    <input type="number" id="edit_alternative_phone" name="alternative_phone" class="no-icon">
                </div>

                <div class="field" style="grid-column:1 / -1;">
                    <label>Email</label>
                    <input type="email" id="edit_email" name="email" class="no-icon">
                </div>

                <div class="field" style="grid-column:1 / -1;">
                    <label>Address</label>
                    <textarea id="edit_address" name="address" class="no-icon"></textarea>
                </div>
            </div>

            <label class="remember mt-4">
                <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                <span>Active</span>
            </label>

            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                <button class="btn btn-primary">Update</button>
            </div>

        </form>

    </div>

</div>
