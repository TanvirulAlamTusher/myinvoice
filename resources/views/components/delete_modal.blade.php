<div id="deleteModal" class="modal-overlay hidden">

    <div class="modal-box">

        <h3 class="modal-title">Confirm Delete</h3>

        <p class="modal-text">
            Are you sure you want to delete <span id="deleteItemName">this item</span>?
        </p>

        <div class="modal-actions">

            <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">
                Cancel
            </button>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">
                    Yes, Delete
                </button>

            </form>

        </div>

    </div>

</div>
