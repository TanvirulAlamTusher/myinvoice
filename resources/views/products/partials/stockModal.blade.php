<div id="stockModal" class="modal-overlay hidden">

    <div class="modal-box">

        <div class="modal-title text-teal">
            Add Stock
        </div>

        <div class="modal-text" id="stockProductName">
            Select product
        </div>

        <form id="stockForm" method="POST">

            @csrf

            <div class="field">
                <label>Quantity</label>

                <input type="number"
                       name="quantity"
                       min="1"
                       required
                       class="form-input">
            </div>

            <div class="modal-actions mt-4">

                <button type="button"
                        class="btn btn-ghost"
                        onclick="closeStockModal()">
                    Cancel
                </button>

                <button type="submit"
                        id="stockSubmitBtn"
                        class="btn btn-primary">
                    Increase Stock
                </button>

            </div>

        </form>

    </div>

</div>
@push('scripts')
<script>

let currentProductId = null;

function openStockModal(actionUrl, productName, productId)
{
    document.getElementById('stockForm').action = actionUrl;

    document.getElementById('stockProductName').innerText = productName;

    currentProductId = productId;

    document.getElementById('stockModal').classList.remove('hidden');
}

function closeStockModal()
{
    document.getElementById('stockModal').classList.add('hidden');
}

// AJAX
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('stockForm');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('stockSubmitBtn');
        btn.disabled = true;
        btn.innerText = "Updating...";

        try {

            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            });

            const data = await res.json();

            if (data.success) {

                // update stock instantly
                const stockCell = document.getElementById('stock-' + currentProductId);

                if (stockCell) {
                    stockCell.innerHTML = `
                        <span class="badge badge-teal">${data.new_stock}</span>
                    `;
                }

                closeStockModal();
                form.reset();


                   toast(data.message, 'success');

            } else {

                  toast(data.message, 'error');
            }

        } catch (err) {
            console.error(err);

            toast('Something went wrong!'+err.message, 'error');
        }

        btn.disabled = false;
        btn.innerText = "Increase Stock";
    });

});

// OPTIONAL: if you don't have toast function yet

</script>
@endPush
