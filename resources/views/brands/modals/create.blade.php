<div id="createBrandModal" class="modal-overlay hidden">

    <div class="modal-box">

        <h3 class="modal-title">Create Brand</h3>

        <form id="createBrandForm" enctype="multipart/form-data">

            @csrf

            <div class="field">
                <label>Name</label>
                <input type="text" name="name" required class="no-icon">
            </div>

            <div class="field">
                <label>Image</label>
                <input type="file" name="image" class="no-icon">
            </div>

            <!-- FIXED CHECKBOX -->
            <div class="field">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked>
                    Active
                </label>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" class="no-icon"></textarea>
            </div>

            <div class="modal-actions">

                <button type="button" class="btn btn-ghost" onclick="closeModal('createBrandModal')">
                    Cancel
                </button>

                <button type="submit" class="btn btn-primary">
                    Save
                </button>

            </div>

        </form>

    </div>

</div>
