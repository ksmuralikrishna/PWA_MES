@extends('admin.layouts.app')

@section('title', isset($item_id) ? 'Edit Material' : 'New Material')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <a href="{{ route('admin.mes.material.index') }}" style="color:var(--text-muted);text-decoration:none;">Materials</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong>{{ isset($item_id) ? 'Edit Material' : 'New Material' }}</strong>
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

        .four-col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
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

        .hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        @media(max-width:900px) {
            .four-col {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(max-width:768px) {

            .two-col,
            .three-col,
            .four-col {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $material = isset($item_id) ? \App\Models\Material::findOrFail($item_id) : null;
        $isEdit = $material !== null;
    @endphp

    <div class="ph">
        <div>
            <h2>{{ $isEdit ? 'Edit Material' : 'New Material' }}</h2>
            <p>{{ $isEdit ? 'Update material details — Code: ' . $material->material_code : 'Add a new material to the master list' }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.mes.material.index') }}" class="btn btn-outline btn-sm">
                <svg viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg> Back
            </a>
        </div>
    </div>

    <div id="formAlert" class="form-alert"></div>

    {{-- ── Section 1: Identification ── --}}
    <div class="card">
        <div class="card-head">
            <svg viewBox="0 0 24 24">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
            </svg>
            <span>Identification</span>
        </div>
        <div class="card-body">
            <div class="three-col">

                <div class="field">
                    <label for="material_code">Material Code</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M9 9h6M9 13h6M9 17h4" />
                        </svg>
                        <input type="text" id="material_code" name="material_code"
                            value="{{ old('material_code', $material?->material_code) }}" placeholder="e.g. 1001"
                            required>
                    </div>
                    <div class="error-msg" id="err_material_code"></div>
                </div>

                <div class="field" style="grid-column:span 2;">
                    <label for="material_name">Material Name <span class="req">*</span></label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        </svg>
                        <input type="text" id="material_name" name="material_name"
                            value="{{ old('material_name', $material?->material_name) }}"
                            placeholder="e.g. Lead Acid Battery Scrap" required>
                    </div>
                    <div class="error-msg" id="err_material_name"></div>
                </div>

                <div class="field" style="grid-column:span 2;">
                    <label for="secondary_name">Secondary Name</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <input type="text" id="secondary_name" name="secondary_name"
                            value="{{ old('secondary_name', $material?->secondary_name) }}"
                            placeholder="Alternative or local name">
                    </div>
                    <span class="hint">Optional alternative name or local description</span>
                    <div class="error-msg" id="err_secondary_name"></div>
                </div>

                <div class="field">
                    <label for="stock_code">Stock Code</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18" />
                        </svg>
                        <input type="text" id="stock_code" name="stock_code"
                            value="{{ old('stock_code', $material?->stock_code) }}"
                            placeholder="e.g. SC-0042">
                    </div>
                    <div class="error-msg" id="err_stock_code"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Section 2: Classification ── --}}
    <div class="card">
        <div class="card-head">
            <svg viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            <span>Classification</span>
        </div>
        <div class="card-body">
            <div class="four-col">

                <div class="field">
                    <label for="category">Category</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <input type="text" id="category" name="category"
                            value="{{ old('category', $material?->category) }}"
                            placeholder="e.g. Raw Material" list="categoryList">
                        <datalist id="categoryList">
                            @php
                                $cats = \App\Models\Material::where('is_active', true)->distinct()->pluck('category')->filter();
                            @endphp
                            @foreach($cats as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="error-msg" id="err_category"></div>
                </div>

                <div class="field">
                    <label for="section">Section</label>
                    <div class="iw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        <input type="text" id="section" name="section"
                            value="{{ old('section', $material?->section) }}"
                            placeholder="e.g. Smelting Input" list="sectionList">
                        <datalist id="sectionList">
                            @php
                                $sects = \App\Models\Material::where('is_active', true)->distinct()->pluck('section')->filter();
                            @endphp
                            @foreach($sects as $sec)
                                <option value="{{ $sec }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="error-msg" id="err_section"></div>
                </div>

                <div class="field">
                    <label for="unit">Unit</label>
                    <div class="iw sw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <line x1="12" y1="2" x2="12" y2="22" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        <select id="unit" name="unit">
                            <option value="">Select unit…</option>
                            @foreach(['KG', 'MT', 'TON', 'L', 'ML', 'PCS', 'SET', 'DRUM', 'BAG', 'UNIT'] as $u)
                                <option value="{{ $u }}" {{ old('unit', $material?->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="error-msg" id="err_unit"></div>
                </div>

                <div class="field">
                    <label for="status">Status</label>
                    <div class="iw sw">
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <select id="status" name="status">
                            <option value="1"   {{ old('status', $material?->status ?? '1') === '1'   ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $material?->status)              === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="error-msg" id="err_status"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Sticky Footer ── --}}
    <div class="form-actions">
        <a href="{{ route('admin.mes.material.index') }}" class="btn btn-outline btn-sm">Cancel</a>
        <div style="display:flex;gap:10px;align-items:center;">
            <span id="autosaveStatus" style="font-size:12px;color:var(--text-muted);display:none;"></span>
            <button type="button" class="btn btn-primary btn-sm" id="btnSave" onclick="saveForm()">
                <svg viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                <span id="btnSaveLabel">{{ $isEdit ? 'Save Changes' : 'Create Material' }}</span>
            </button>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// ── Determine create vs edit ──────────────────────────────────────
const PATH_PARTS = window.location.pathname.split('/').filter(Boolean);
// URL pattern: /admin/mes/material/{id}/edit  OR  /admin/mes/material/create
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
        material_code:  document.getElementById('material_code').value,
        material_name:  document.getElementById('material_name').value,
        secondary_name: document.getElementById('secondary_name').value,
        stock_code:     document.getElementById('stock_code').value,
        category:       document.getElementById('category').value,
        section:        document.getElementById('section').value,
        unit:           document.getElementById('unit').value,
        status:         document.getElementById('status').value,
    };
}

// ── Save (create or update) ───────────────────────────────────────
async function saveForm(silent = false) {
    clearAlert();
    clearFieldErrors();

    const btn = document.getElementById('btnSave');
    btn.disabled = true;

    const method   = isCreate ? 'POST' : 'PUT';
    const endpoint = isCreate ? '/materials' : `/materials/${recordId}`;

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
                window.location.href = `{{ url('/admin/mes/material') }}/${data.data.id}/edit`;
            } else {
                showAlert('Material saved successfully.', 'success');
            }
        } else {
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
    const fields = ['material_code','material_name','secondary_name','stock_code',
                    'category','section','unit','status'];
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