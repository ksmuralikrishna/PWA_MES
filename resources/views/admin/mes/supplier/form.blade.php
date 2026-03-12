@extends('admin.layouts.app')

@section('title', isset($item_id) ? 'Edit Supplier' : 'New Supplier')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <a href="{{ route('admin.mes.supplier.index') }}" style="color:var(--text-muted);text-decoration:none;">Suppliers</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong id="breadcrumbTitle">{{ isset($item_id) ? 'Edit Supplier' : 'New Supplier' }}</strong>
@endsection

@push('styles')
    <style>
        :root {
            --green: #1a7a3a;
            --green-dark: #145f2d;
            --green-light: #e8f5ed;
            --green-xlight: #f2faf5;
            --white: #ffffff;
            --bg: #f4f7f5;
            --border: #dde8e2;
            --text: #1e2d26;
            --text-mid: #3d5449;
            --text-muted: #6b8a78;
            --error: #dc2626;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
            --radius: 12px;
        }

        .ph {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ph h2 {
            font-size: clamp(17px, 2.3vw, 22px);
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.3px;
        }

        .ph p {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 17px;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all .2s;
            white-space: nowrap;
        }

        .btn svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .btn-primary {
            background: var(--green);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--green-dark);
            box-shadow: 0 4px 14px rgba(26, 122, 58, .28);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: var(--white);
            color: var(--text-mid);
            border: 1.5px solid var(--border);
        }

        .btn-outline:hover {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-xlight);
        }

        .btn-sm {
            padding: 7px 13px;
            font-size: 12.5px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .card-head {
            padding: 11px 20px;
            background: var(--green-light);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-head svg {
            width: 14px;
            height: 14px;
            stroke: var(--green);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-head span {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--green);
        }

        .card-body {
            padding: 22px 20px;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .three-col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--text-mid);
        }

        .field label .req {
            color: var(--error);
        }

        .iw {
            position: relative;
        }

        .iw svg.ico {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 13px;
            height: 13px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
        }

        input[type=text],
        input[type=email],
        input[type=tel],
        input[type=number],
        select,
        textarea {
            width: 100%;
            padding: 9px 12px 9px 36px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: var(--green-xlight);
            font-family: inherit;
            font-size: 13px;
            color: var(--text);
            outline: none;
            appearance: none;
            transition: border-color .18s, box-shadow .18s, background .18s;
        }

        textarea {
            padding-left: 12px;
            resize: vertical;
            min-height: 80px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26, 122, 58, .09);
        }

        input[readonly] {
            background: #eef6f1;
            color: var(--text-mid);
            cursor: default;
            border-color: #c8dfd1;
        }

        input[readonly]:focus {
            box-shadow: none;
            border-color: #c8dfd1;
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-muted);
            font-size: 12px;
        }

        input.is-invalid,
        select.is-invalid,
        textarea.is-invalid {
            border-color: var(--error);
        }

        .sw::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid var(--text-muted);
            pointer-events: none;
        }

        .error-msg {
            font-size: 11.5px;
            color: var(--error);
            margin-top: 2px;
        }

        .form-alert {
            display: none;
            padding: 11px 15px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .form-alert.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            display: block;
        }

        .form-alert.success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            display: block;
        }

        .form-actions {
            position: sticky;
            bottom: 0;
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 13px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            z-index: 20;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, .06);
        }

        @media(max-width:768px) {
            .two-col,
            .three-col {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    <div class="ph">
        <div>
            <h2 id="pageTitle">{{ isset($item_id) ? 'Edit Supplier' : 'New Supplier' }}</h2>
            <p id="pageSubtitle">
                {{ isset($item_id) ? 'Update supplier details' : 'Add a new supplier to the master list' }}
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.mes.supplier.index') }}" class="btn btn-outline btn-sm">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
        </div>
    </div>

    <div id="formAlert" class="form-alert"></div>

    {{-- ── Section 1: Identification ── --}}
    <div class="card">
        <div class="card-head">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Identification</span>
        </div>
        <div class="card-body">
            <div class="three-col">

                <div class="field">
                    <label for="supplier_code">Supplier Code <span class="req">*</span></label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M9 9h6M9 13h6M9 17h4"/>
                        </svg>
                        <input type="text" id="supplier_code" name="supplier_code"
                            value="{{ old('supplier_code', isset($item_id) ? \App\Models\Supplier::find($item_id)?->supplier_code : '') }}"
                            placeholder="e.g. 1001" required>
                    </div>
                    <div class="error-msg" id="err_supplier_code"></div>
                </div>

                <div class="field" style="grid-column:span 2;">
                    <label for="supplier_name">Supplier Name <span class="req">*</span></label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                        <input type="text" id="supplier_name" name="supplier_name"
                            value="{{ old('supplier_name', isset($item_id) ? \App\Models\Supplier::find($item_id)?->supplier_name : '') }}"
                            placeholder="e.g. ACME Battery Supplies Sdn Bhd" required>
                    </div>
                    <div class="error-msg" id="err_supplier_name"></div>
                </div>

                <div class="field">
                    <label for="facts_supplier_code">FACTS Supplier Code</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                        </svg>
                        <input type="text" id="facts_supplier_code" name="facts_supplier_code"
                            value="{{ old('facts_supplier_code', isset($item_id) ? \App\Models\Supplier::find($item_id)?->facts_supplier_code : '') }}"
                            placeholder="e.g. SUP-F001">
                    </div>
                    <div class="error-msg" id="err_facts_supplier_code"></div>
                </div>

                <div class="field">
                    <label for="status">Status</label>
                    <div class="iw sw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <select id="status" name="status">
                            <option value="1"   {{ old('status', isset($item_id) ? \App\Models\Supplier::find($item_id)?->status : 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', isset($item_id) ? \App\Models\Supplier::find($item_id)?->status : '')              === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="error-msg" id="err_status"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Section 2: Contact Details ── --}}
    <div class="card">
        <div class="card-head">
            <svg viewBox="0 0 24 24">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.45 16.92z"/>
            </svg>
            <span>Contact Details</span>
        </div>
        <div class="card-body">
            <div class="two-col">

                <div class="field">
                    <label for="contact_number">Contact Number</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12"/>
                        </svg>
                        <input type="text" id="contact_number" name="contact_number"
                            value="{{ old('contact_number', isset($item_id) ? \App\Models\Supplier::find($item_id)?->contact_number : '') }}"
                            placeholder="e.g. +60 12-345 6789">
                    </div>
                    <div class="error-msg" id="err_contact_number"></div>
                </div>

                <div class="field">
                    <label for="supplier_email">Email Address</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input type="email" id="supplier_email" name="supplier_email"
                            value="{{ old('supplier_email', isset($item_id) ? \App\Models\Supplier::find($item_id)?->supplier_email : '') }}"
                            placeholder="e.g. procurement@acme.com">
                    </div>
                    <div class="error-msg" id="err_supplier_email"></div>
                </div>

                <div class="field" style="grid-column:span 2;">
                    <label for="supplier_address">Address</label>
                    <textarea id="supplier_address" name="supplier_address"
                        placeholder="Full supplier address…">{{ old('supplier_address', isset($item_id) ? \App\Models\Supplier::find($item_id)?->supplier_address : '') }}</textarea>
                    <div class="error-msg" id="err_supplier_address"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Sticky Footer ── --}}
    <div class="form-actions">
        <a href="{{ route('admin.mes.supplier.index') }}" class="btn btn-outline btn-sm">Cancel</a>
        <div style="display:flex;gap:10px;align-items:center;">
            <span id="autosaveStatus" style="font-size:12px;color:var(--text-muted);display:none;"></span>
            <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
                <svg viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span id="btnSaveLabel">{{ isset($item_id) ? 'Save Changes' : 'Create Supplier' }}</span>
            </button>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// ── Determine create vs edit ──────────────────────────────────────
const PATH_PARTS = window.location.pathname.split('/').filter(Boolean);
// URL pattern: /admin/mes/supplier/{id}/edit  OR  /admin/mes/supplier/create
const isCreate  = PATH_PARTS[PATH_PARTS.length - 1] === 'create';
const recordId  = isCreate ? null : PATH_PARTS[PATH_PARTS.length - 2];

let autosaveTimer;

// ── Helpers ───────────────────────────────────────────────────────
function showAlert(msg, type = 'error') {
    const el = document.getElementById('formAlert');
    el.className = `form-alert ${type}`;
    el.textContent = msg;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function clearAlert() {
    const el = document.getElementById('formAlert');
    el.className = 'form-alert';
    el.textContent = '';
}

function clearFieldErrors() {
    document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showFieldErrors(errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const errEl = document.getElementById('err_' + field);
        const input = document.getElementById(field);
        if (errEl) errEl.textContent = Array.isArray(messages) ? messages[0] : messages;
        if (input) input.classList.add('is-invalid');
    });
}

function getFormData() {
    return {
        supplier_code:       document.getElementById('supplier_code').value,
        supplier_name:       document.getElementById('supplier_name').value,
        facts_supplier_code: document.getElementById('facts_supplier_code').value,
        status:              document.getElementById('status').value,
        contact_number:      document.getElementById('contact_number').value,
        supplier_email:      document.getElementById('supplier_email').value,
        supplier_address:    document.getElementById('supplier_address').value,
    };
}

// ── Save (create or update) ───────────────────────────────────────
async function saveForm(silent = false) {
    clearAlert();
    clearFieldErrors();

    const btn = document.getElementById('btnSave');
    btn.disabled = true;

    const method   = isCreate ? 'POST' : 'PUT';
    const endpoint = isCreate ? '/supplier' : `/supplier/${recordId}`;
console.log("getFormData", getFormData())
    const res = await apiFetch(endpoint, {
        method,
        body: JSON.stringify(getFormData()),
    });

    btn.disabled = false;

    if (!res) return;

    const data = await res.json();

    if (res.ok && data.status === 'ok') {
        if (!silent) {
            if (isCreate) {
                // Redirect to edit page of newly created record
                window.location.href = `{{ url('/admin/mes/supplier') }}/${data.data.id}/edit`;
            } else {
                showAlert('Supplier saved successfully.', 'success');
            }
        } else {
            // Autosave — show timestamp
            const status = document.getElementById('autosaveStatus');
            status.style.display = 'inline';
            status.textContent = 'Autosaved at ' + new Date().toLocaleTimeString();
            setTimeout(() => status.style.display = 'none', 5000);
        }
    } else if (res.status === 422) {
        showFieldErrors(data.errors ?? {});
        if (!silent) showAlert(data.message ?? 'Please fix the errors below.');
    } else {
        if (!silent) showAlert(data.message ?? 'Something went wrong.');
    }
}

// ── Autosave ──────────────────────────────────────────────────────
function setupAutosave() {
    const fields = ['supplier_code','supplier_name','facts_supplier_code','status',
                    'contact_number','supplier_email','supplier_address'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', scheduleAutosave);
        if (['text','email','tel','number'].includes(el.type) || el.tagName === 'TEXTAREA') {
            el.addEventListener('keyup', scheduleAutosave);
        }
    });
}

function scheduleAutosave() {
    // Only autosave on edit; no record to autosave against on create
    if (isCreate) return;

    const status = document.getElementById('autosaveStatus');
    status.style.display = 'inline';
    status.style.color   = 'var(--text-muted)';
    status.textContent   = 'Saving...';
    clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(() => saveForm(true), 3000);
}

// ── Init ──────────────────────────────────────────────────────────
function init() {
    if (!isCreate) {
        setupAutosave();
    }
}

init();
</script>
@endpush