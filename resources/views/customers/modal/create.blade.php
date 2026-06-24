{{-- ================= CREATE MODAL ================= --}}
<div id="createModal" class="modal-overlay hidden">

    <div class="modal-box" style="width:min(720px, 92vw);">

        <h3 class="modal-title">Create Customer</h3>

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" class="no-icon" value="{{ old('name') }}"
                        placeholder="Customer Name" required>
                </div>

                <div class="field">
                    <label>Business Name</label>
                    <input type="text" name="business_name" class="no-icon" value="{{ old('business_name') }}"
                        placeholder="Transparent Inc.">
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="number" name="phone" class="no-icon" value="{{ old('phone') }}"
                        placeholder="01612345678" required>

                </div>

                <div class="field">
                    <label>Alternative Phone</label>
                    <input type="number" name="alternative_phone" class="no-icon"
                        value="{{ old('alternative_phone') }}" placeholder="01612345678">
                </div>

                <div class="field" style="grid-column:1 / -1;">
                    <label>Email</label>
                    <input type="email" name="email" class="no-icon" value="{{ old('email') }}"
                        placeholder="customer@example.com">
                </div>

                <div class="field" style="grid-column:1 / -1;">
                    <label>Address</label>
                    <textarea name="address" class="no-icon" placeholder="Customer Address">{{ old('address') }}</textarea>
                </div>
            </div>

            <label class="remember mt-4">
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
