@extends('app')

@section('title', 'My Profile')

@section('content')

<div class="page-layout">

    <div class="card card-full">

        {{-- ================= HEADER ================= --}}
        <div class="card-header">
             <a href="{{ route('dashboard') }}" class="btn btn-ghost mb-3">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            Back
        </a>

            <div>
                <h1 class="page-title">My Profile</h1>
                <p class="text-muted mt-4">View and manage your account details</p>
            </div>

            <button class="btn btn-primary" onclick="openModal('profileModal')">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                </svg>
                Edit Profile
            </button>

        </div>

        <div class="divider"></div>

        {{-- ================= PROFILE GRID ================= --}}
        <div class="profile-grid">

            {{-- LEFT CARD --}}
            <div>

                <div style="
                    background: var(--surface-1);
                    border: 1px solid var(--border);
                    border-radius: var(--radius-xl);
                    padding: 24px;
                    text-align:center;
                    box-shadow: var(--shadow-card);
                ">

                    {{-- Avatar --}}
                    <div style="
                        width: 80px;
                        height: 80px;
                        margin: 0 auto 12px;
                        border-radius: 50%;
                        background: rgba(13,148,136,.15);
                        border: 2px solid rgba(13,148,136,.3);
                        display:grid;
                        place-items:center;
                        font-size: 28px;
                        font-weight: 700;
                        color: var(--teal-mid);
                        text-transform: uppercase;
                    ">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h3 style="margin-bottom:4px;">{{ $user->name }}</h3>

                    @php
                        $role = strtolower($user->getRoleNames()->first() ?? 'user');
                        $badge = $role === 'admin' ? 'badge-teal' : 'badge-warning';
                    @endphp

                    <span class="badge {{ $badge }}">
                        {{ ucfirst($role) }}
                    </span>

                    <p class="text-muted mt-4 text-sm">
                        Member since {{ $user->created_at->format('M Y') }}
                    </p>

                </div>

            </div>

            {{-- RIGHT SIDE --}}
            <div>

                <div class="tbl-wrap">
                    <table class="tbl">
                        <tbody>

                            <tr>
                                <th>Name</th>
                                <td>{{ $user->name }}</td>
                            </tr>

                            <tr>
                                <th>Phone</th>
                                <td>{{ $user->phone }}</td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email ?? '—' }}</td>
                            </tr>

                            <tr>
                                <th>Role</th>
                                <td>
                                    <span class="badge {{ $badge }}">
                                        {{ ucfirst($role) }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>Created</th>
                                <td>{{ $user->created_at->format('d M Y, h:i A') }}</td>
                            </tr>

                            <tr>
                                <th>Updated</th>
                                <td>{{ $user->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                {{-- ACTIONS --}}
                <div style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap;">

                    <button class="btn btn-primary" onclick="openModal('profileModal')">
                        Edit Profile
                    </button>

                    <button class="btn btn-ghost" onclick="openModal('passwordModal')">
                        Change Password
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ================= PROFILE MODAL ================= --}}
<div id="profileModal" class="modal-overlay hidden">

    <div class="modal-box">

        <h3 class="modal-title">Edit Profile</h3>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Name</label>
                <input type="text" name="name" value="{{ $user->name }}" class="no-icon">
            </div>

            <div class="field">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ $user->phone }}" class="no-icon">
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ $user->email }}" class="no-icon">
            </div>

            <div class="modal-actions">

                <button type="button" class="btn btn-ghost" onclick="closeModal('profileModal')">
                    Cancel
                </button>

                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

{{-- ================= PASSWORD MODAL ================= --}}
<div id="passwordModal" class="modal-overlay hidden">

    <div class="modal-box">

        <h3 class="modal-title">Change Password</h3>

        <form id="passwordForm" method="POST" action="{{ route('profile.changePassword') }}">

            @csrf
            @method('PUT')

              <div class="field">
        <label>New Password</label>
        <input type="password" name="password" required>
    </div>

    <div class="field">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-ghost" onclick="closeModal('passwordModal')">
            Cancel
        </button>

        <button type="submit" class="btn btn-primary">
            Update Password
        </button>
    </div>

        </form>
    </div>

</div>

{{-- ================= MODAL JS ================= --}}
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// ===== PASSWORD AJAX =====
document.getElementById('passwordForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);

    axios.post(form.action, formData)
        .then(res => {

            if (res.data.status === 'success') {
                toast(res.data.message, 'success');
                closeModal('passwordModal');
                form.reset();
            }

            if (res.data.status === 'error') {
                toast(res.data.message, 'error');
            }

        })
        .catch(error => {

            if (error.response && error.response.status === 422) {

                let errors = error.response.data.errors;

                Object.values(errors).forEach(err => {
                    toast(err[0], 'error');
                });

            } else {
                toast(
                    error.response?.data?.message || 'Server error occurred',
                    'error'
                );
            }

        });
});
</script>

@endsection
