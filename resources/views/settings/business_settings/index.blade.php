{{-- resources/views/settings/business/index.blade.php --}}
@extends('app')

@section('content')
<div class="page-layout">

    {{-- Orbs --}}
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    {{-- Page Header --}}
    <div class="card-header mb-6">
        <div>
            <h1 class="page-title">Business Settings</h1>
            <p class="text-muted" style="margin-top:4px;">Manage your company profile, contact info, and documents.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success mb-6">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error mb-6">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('business-settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

            {{-- ── LEFT COLUMN ── --}}
            <div style="display:grid;gap:24px;align-content:start;">

                {{-- Company Info --}}
                <div class="card-section">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-pale);border:1px solid var(--border);display:grid;place-items:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--teal-bright);fill:none;stroke-width:2">
                                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/>
                            </svg>
                        </div>
                        <h2 class="section-title" style="margin-bottom:0;">Company Information</h2>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>
                                <svg viewBox="0 0 24 24" class="input-icon" style="position:static;transform:none;width:13px;height:13px;">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/>
                                </svg>
                                Business Name
                            </label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" class="input-icon">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/>
                                </svg>
                                <input type="text" name="business_name" value="{{ old('business_name', $settings->business_name ?? '') }}" placeholder="Acme Corporation">
                            </div>
                        </div>

                        <div class="field">
                            <label>
                                <svg viewBox="0 0 24 24" class="input-icon" style="position:static;transform:none;width:13px;height:13px;">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                Owner Name
                            </label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" class="input-icon">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                <input type="text" name="owner_name" value="{{ old('owner_name', $settings->owner_name ?? '') }}" placeholder="John Doe">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label>Top Tagline</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" class="input-icon">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <input type="text" name="top_tagline" value="{{ old('top_tagline', $settings->top_tagline ?? '') }}" placeholder="Your trusted partner since 2010">
                        </div>
                    </div>

                    <div class="field" style="margin-bottom:0;">
                        <label>Tagline</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" class="input-icon">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <input type="text" name="tagline" value="{{ old('tagline', $settings->tagline ?? '') }}" placeholder="Quality you can count on">
                        </div>
                    </div>
                </div>

                {{-- Contact Details --}}
                <div class="card-section">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-pale);border:1px solid var(--border);display:grid;place-items:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--teal-bright);fill:none;stroke-width:2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.27 2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <h2 class="section-title" style="margin-bottom:0;">Contact Details</h2>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Phone 1</label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" class="input-icon">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.27 2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <input type="text" name="phone_1" value="{{ old('phone_1', $settings->phone_1 ?? '') }}" placeholder="+1 (555) 000-0000">
                            </div>
                        </div>

                        <div class="field">
                            <label>Phone 2</label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" class="input-icon">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.27 2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <input type="text" name="phone_2" value="{{ old('phone_2', $settings->phone_2 ?? '') }}" placeholder="+1 (555) 000-0001">
                            </div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Email</label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" class="input-icon">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                </svg>
                                <input type="email" name="email" value="{{ old('email', $settings->email ?? '') }}" placeholder="hello@acme.com">
                            </div>
                        </div>

                        <div class="field">
                            <label>Website</label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" class="input-icon">
                                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                                <input type="url" name="website" value="{{ old('website', $settings->website ?? '') }}" placeholder="https://acme.com">
                            </div>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom:0;">
                        <label>Address</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" class="input-icon" style="top:14px;transform:none;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <textarea name="address" rows="3" placeholder="123 Business Ave, Suite 100&#10;New York, NY 10001">{{ old('address', $settings->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Terms & Conditions --}}
                <div class="card-section">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-pale);border:1px solid var(--border);display:grid;place-items:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--teal-bright);fill:none;stroke-width:2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </div>
                        <h2 class="section-title" style="margin-bottom:0;">Terms &amp; Conditions</h2>
                    </div>

                    <div class="field" style="margin-bottom:0;">
                        <label>Footer Terms (printed on invoices)</label>
                        <textarea name="terms_conditions" rows="5" class="no-icon" style="padding-left:14px;" placeholder="1. Payment is due within 30 days of invoice date.&#10;2. Goods remain the property of the seller until full payment is received.&#10;3. ...">{{ old('terms_conditions', $settings->terms_conditions ?? '') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COLUMN ── --}}
            <div style="display:grid;gap:24px;align-content:start;">

                {{-- Logo Upload --}}
                <div class="card-section">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-pale);border:1px solid var(--border);display:grid;place-items:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--teal-bright);fill:none;stroke-width:2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                        <h2 class="section-title" style="margin-bottom:0;">Business Logo</h2>
                    </div>

                    {{-- Preview --}}
                    <div class="image-upload-box" id="logo-preview-box" style="height:200px;margin-bottom:16px;cursor:pointer;" onclick="document.getElementById('logo-input').click()">
                        @if(!empty($settings->logo))
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" id="logo-preview-img">
                        @else
                            <div id="logo-placeholder" style="text-align:center;color:var(--text-muted);">
                                <svg viewBox="0 0 24 24" style="width:40px;height:40px;stroke:var(--border);fill:none;stroke-width:1.5;margin:0 auto 10px;">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <p style="font-size:.8rem;">Click to upload logo</p>
                                <p style="font-size:.72rem;margin-top:4px;">PNG, JPG, SVG · max 2 MB</p>
                            </div>
                            <img src="" alt="" id="logo-preview-img" style="display:none;width:100%;height:100%;object-fit:contain;">
                        @endif
                    </div>

                    <input type="file" name="logo" id="logo-input" accept="image/*" style="display:none;">

                    <button type="button" class="btn btn-ghost btn-full" onclick="document.getElementById('logo-input').click()">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Upload Logo
                    </button>

                    @if(!empty($settings->logo))
                        <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--surface-2);border-radius:8px;border:1px solid var(--border);">
                            <span style="font-size:.78rem;color:var(--text-muted);">Current logo saved</span>
                            <span class="badge badge-teal">Active</span>
                        </div>
                    @endif
                </div>
                {{-- Favicon Upload --}}
<div class="card-section">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-pale);border:1px solid var(--border);display:grid;place-items:center;flex-shrink:0;">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--teal-bright);fill:none;stroke-width:2">
                <circle cx="12" cy="12" r="8"/>
            </svg>
        </div>
        <h2 class="section-title" style="margin-bottom:0;">Website Favicon</h2>
    </div>

    <div class="image-upload-box"
         id="favicon-preview-box"
         style="height:120px;margin-bottom:16px;cursor:pointer;"
         onclick="document.getElementById('favicon-input').click()">

        @if(!empty($settings->favicon))
            <img src="{{ asset('storage/'.$settings->favicon) }}"
                 alt="Favicon"
                 id="favicon-preview-img"
                 style="max-width:64px;max-height:64px;">
        @else
            <div id="favicon-placeholder" style="text-align:center;color:var(--text-muted);">
                <svg viewBox="0 0 24 24"
                     style="width:40px;height:40px;stroke:var(--border);fill:none;stroke-width:1.5;margin:0 auto 10px;">
                    <circle cx="12" cy="12" r="8"/>
                </svg>
                <p style="font-size:.8rem;">Click to upload favicon</p>
                <p style="font-size:.72rem;margin-top:4px;">
                    PNG, ICO, SVG · 32x32 recommended
                </p>
            </div>

            <img src=""
                 alt=""
                 id="favicon-preview-img"
                 style="display:none;width:64px;height:64px;object-fit:contain;">
        @endif
    </div>

    <input type="file"
           name="favicon"
           id="favicon-input"
           accept=".png,.jpg,.jpeg,.ico,.svg,image/*"
           style="display:none;">

    <button type="button"
            class="btn btn-ghost btn-full"
            onclick="document.getElementById('favicon-input').click()">
        <svg viewBox="0 0 24 24"
             style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        Upload Favicon
    </button>

    @if(!empty($settings->favicon))
        <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--surface-2);border-radius:8px;border:1px solid var(--border);">
            <span style="font-size:.78rem;color:var(--text-muted);">
                Current favicon saved
            </span>
            <span class="badge badge-teal">Active</span>
        </div>
    @endif
</div>

                {{-- Signature Upload --}}
                <div class="card-section">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-pale);border:1px solid var(--border);display:grid;place-items:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:var(--teal-bright);fill:none;stroke-width:2">
                                <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                            </svg>
                        </div>
                        <h2 class="section-title" style="margin-bottom:0;">Signature</h2>
                    </div>

                    <div class="image-upload-box" id="sig-preview-box" style="height:160px;margin-bottom:16px;cursor:pointer;" onclick="document.getElementById('sig-input').click()">
                        @if(!empty($settings->signature))
                            <img src="{{ asset('storage/' . $settings->signature) }}" alt="Signature" id="sig-preview-img">
                        @else
                            <div id="sig-placeholder" style="text-align:center;color:var(--text-muted);">
                                <svg viewBox="0 0 24 24" style="width:36px;height:36px;stroke:var(--border);fill:none;stroke-width:1.5;margin:0 auto 10px;">
                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                </svg>
                                <p style="font-size:.8rem;">Click to upload signature</p>
                                <p style="font-size:.72rem;margin-top:4px;">PNG with transparent background</p>
                            </div>
                            <img src="" alt="" id="sig-preview-img" style="display:none;width:100%;height:100%;object-fit:contain;">
                        @endif
                    </div>

                    <input type="file" name="signature" id="sig-input" accept="image/*" style="display:none;">

                    <button type="button" class="btn btn-ghost btn-full" onclick="document.getElementById('sig-input').click()">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Upload Signature
                    </button>

                    @if(!empty($settings->signature))
                        <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--surface-2);border-radius:8px;border:1px solid var(--border);">
                            <span style="font-size:.78rem;color:var(--text-muted);">Current signature saved</span>
                            <span class="badge badge-teal">Active</span>
                        </div>
                    @endif
                </div>

                {{-- Info card --}}
                <div style="border:1px solid var(--border);border-radius:var(--radius-xl);padding:18px;background:var(--surface-2);">
                    <p style="font-size:.78rem;font-weight:700;color:var(--teal-mid);letter-spacing:.06em;text-transform:uppercase;margin-bottom:12px;">Where this data is used</p>
                    <ul style="list-style:none;padding:0;display:grid;gap:9px;">
                        @foreach(['Invoice headers & footers','PDF / print documents','Customer-facing reports','Email templates'] as $use)
                        <li style="display:flex;align-items:center;gap:9px;font-size:.82rem;color:var(--text-muted);">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--teal-bright);fill:none;stroke-width:2.5;flex-shrink:0;">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            {{ $use }}
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>

        {{-- Save Bar --}}
        <div style="margin-top:28px;display:flex;align-items:center;justify-content:flex-end;gap:12px;padding:18px 24px;background:var(--surface-1);border:1px solid var(--border);border-radius:var(--radius-xl);box-shadow:var(--shadow-card);">
            <span style="font-size:.82rem;color:var(--text-muted);margin-right:auto;">
                Last updated:
                <strong style="color:var(--text-main);">
                    {{ isset($settings->updated_at) ? $settings->updated_at->format('d M Y, h:i A') : '—' }}
                </strong>
            </span>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary" style="min-width:160px;">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Settings
            </button>
        </div>

    </form>

</div>

{{-- Toast container --}}
<div id="toast-container"></div>

@push('scripts')
<script>
/* ── Image preview helper ── */
function wirePreview(inputId, imgId, placeholderId) {
    const input = document.getElementById(inputId);
    const img   = document.getElementById(imgId);
    const ph    = document.getElementById(placeholderId);

    if (!input) return;

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

wirePreview('logo-input', 'logo-preview-img', 'logo-placeholder');
wirePreview('favicon-input', 'favicon-preview-img', 'favicon-placeholder');
wirePreview('sig-input', 'sig-preview-img', 'sig-placeholder');


</script>
@endpush
@endsection
