@extends('admin.layouts.app')

@section('title', $page_title)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <a href="{{ route('admin.mes.receiving.index') }}" style="color:var(--text-muted);text-decoration:none;">Receiving</a>
    <span style="margin:0 8px;color:var(--border);">/</span>
    <strong>{{ $page_title }}</strong>
@endsection

@push('styles')
<style>
    .form-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .form-page-header h2 { font-size:clamp(18px,2.5vw,24px); font-weight:700; color:var(--text); margin-bottom:3px; }
    .form-page-header p { font-size:13px; color:var(--text-muted); }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:9px;
           font-family:'Outfit',sans-serif; font-size:13.5px; font-weight:600; cursor:pointer;
           text-decoration:none; border:none; transition:all 0.2s; white-space:nowrap; }
    .btn svg { width:15px; height:15px; stroke:currentColor; flex-shrink:0; }
    .btn-primary { background:var(--green); color:#fff; }
    .btn-primary:hover { background:var(--green-dark); box-shadow:0 4px 12px rgba(26,122,58,0.25); transform:translateY(-1px); }
    .btn-outline { background:var(--white); color:var(--text-mid); border:1.5px solid var(--border); }
    .btn-outline:hover { border-color:var(--green); color:var(--green); background:var(--green-xlight); }
    .btn-sm { padding:8px 16px; font-size:13px; }

    .form-card { background:var(--white); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:20px; }
    .form-section-head { padding:14px 24px; background:var(--green-light); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
    .form-section-head svg { width:16px; height:16px; stroke:var(--green); flex-shrink:0; }
    .form-section-head span { font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--green); }
    .form-section-body { padding:28px 24px 32px; }

    .form-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:20px 28px; }
    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px 28px; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px 28px; }
    .field { display:flex; flex-direction:column; }
    .field.full { grid-column:1/-1; }

    .field label { font-size:11px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase; color:var(--text-mid); margin-bottom:8px; }
    .field label .req { color:var(--error); }
    .field label .opt { color:var(--text-muted); font-weight:400; text-transform:none; letter-spacing:0; font-size:11px; }

    .input-wrap { position:relative; }
    .input-wrap .ico { position:absolute; left:13px; top:50%; transform:translateY(-50%); width:15px; height:15px; stroke:var(--text-muted); pointer-events:none; }

    input[type="text"], input[type="number"], input[type="date"], select, textarea {
        width:100%; padding:11px 14px 11px 40px; border:1.5px solid var(--border); border-radius:9px;
        background:var(--green-xlight); font-family:'Outfit',sans-serif; font-size:14px; color:var(--text);
        outline:none; appearance:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;
    }
    textarea { padding-left:14px; resize:vertical; min-height:80px; }
    input.no-icon, select.no-icon, textarea.no-icon { padding-left:14px; }
    input:focus, select:focus, textarea:focus { border-color:var(--green); background:var(--white); box-shadow:0 0 0 4px rgba(26,122,58,0.08); }
    input.is-invalid, select.is-invalid, textarea.is-invalid { border-color:var(--error); }
    input::placeholder, textarea::placeholder { color:var(--text-muted); }
    .select-wrap::after { content:''; position:absolute; right:13px; top:50%; transform:translateY(-50%);
                          border-left:5px solid transparent; border-right:5px solid transparent; border-top:5px solid var(--text-muted); pointer-events:none; }
    .error-msg { margin-top:5px; font-size:12px; color:var(--error); }

    .form-actions { position:sticky; bottom:0; background:var(--white); border-top:1px solid var(--border);
                    padding:16px 24px; display:flex; align-items:center; justify-content:space-between;
                    flex-wrap:wrap; gap:12px; z-index:10; box-shadow:0 -4px 16px rgba(0,0,0,0.06); }

    .optional-tag { display:inline-block; background:var(--green-light); color:var(--text-muted);
                    font-size:10px; font-weight:600; padding:2px 8px; border-radius:10px;
                    text-transform:uppercase; letter-spacing:1px; margin-left:6px; vertical-align:middle; }
                    
    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; margin-top:10px; }
    .status-badge.draft { background:#e0e7ff; color:#3730a3; }
    .status-badge.submitted { background:#d1fae5; color:#065f46; }

    @media (max-width:768px) {
        .form-grid-2, .form-grid-3 { grid-template-columns:1fr 1fr; }
        .form-section-body { padding:20px 16px 24px; }
    }
    @media (max-width:520px) {
        .form-grid, .form-grid-2, .form-grid-3 { grid-template-columns:1fr; }
        .field.full { grid-column:auto; }
        .form-actions { flex-direction:column; align-items:stretch; }
        .form-actions .btn { justify-content:center; }
    }
</style>
@endpush

@section('content')

@php 
    $isEdit = isset($item); 
    $isSubmitted = $isEdit && $item->status === 'submitted';
@endphp

<div class="form-page-header">
    <div>
        <h2>{{ $page_title }}</h2>
        <p>{{ $isEdit ? 'Update receiving details' : 'Record new raw material receiving log' }}</p>
        
        @if($isEdit)
            <div class="status-badge {{ $item->status == 'submitted' ? 'submitted' : 'draft' }}">
                Status: {{ ucfirst($item->status) }}
            </div>
        @endif
    </div>
    <div style="display:flex; gap:10px;">
        @if($isEdit && !$isSubmitted)
            <form method="POST" action="{{ route('admin.mes.receiving.submit', $item->id) }}" onsubmit="return confirm('Submit this record? It will be locked from further edits.')">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--text); border-color:var(--text-muted);">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Submit Record
                </button>
            </form>
        @endif
        <a href="{{ route('admin.mes.receiving.index') }}" class="btn btn-outline btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Back to List
        </a>
    </div>
</div>

<form method="POST"
    action="{{ $isEdit ? route('admin.mes.receiving.update', $item->id) : route('admin.mes.receiving.store') }}" 
    id="receivingForm">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-card">
        <div class="form-section-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Primary Details</span>
        </div>
        <div class="form-section-body">
            <div class="form-grid-3">

                <div class="field">
                    <label for="receipt_date">Receipt Date <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <input type="date" id="receipt_date" name="receipt_date"
                            value="{{ old('receipt_date', $item->receipt_date ?? now()->format('Y-m-d')) }}" required
                            class="@error('receipt_date') is-invalid @enderror"
                            {{ $isSubmitted ? 'readonly disabled' : '' }}>
                    </div>
                    @error('receipt_date') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="lot_no">Lot Number <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        </svg>
                        <input type="text" id="lot_no" name="lot_no"
                            value="{{ old('lot_no', $item->lot_no ?? '') }}"
                            placeholder="Unique lot identifier" required
                            class="@error('lot_no') is-invalid @enderror"
                            {{ $isSubmitted ? 'readonly disabled' : '' }}>
                    </div>
                    @error('lot_no') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="vehicle_number">Vehicle Number <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        <input type="text" id="vehicle_number" name="vehicle_number"
                            value="{{ old('vehicle_number', $item->vehicle_number ?? '') }}"
                            placeholder="Truck/Vehicle License" required
                            class="@error('vehicle_number') is-invalid @enderror"
                            {{ $isSubmitted ? 'readonly disabled' : '' }}>
                    </div>
                    @error('vehicle_number') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field full">
                    <label for="supplier_id">Supplier <span class="req">*</span></label>
                    <div class="input-wrap select-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <select id="supplier_id" name="supplier_id" class="@error('supplier_id') is-invalid @enderror" required {{ $isSubmitted ? 'disabled' : '' }}>
                            <option value="">Select a supplier...</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }} ({{ $supplier->supplier_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('supplier_id') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field full">
                    <label for="material_id">Material <span class="req">*</span></label>
                    <div class="input-wrap select-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                        </svg>
                        <select id="material_id" name="material_id" class="@error('material_id') is-invalid @enderror" required {{ $isSubmitted ? 'disabled' : '' }}>
                            <option value="">Select material...</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ old('material_id', $item->material_id ?? '') == $material->id ? 'selected' : '' }}>
                                    {{ $material->material_name }} ({{ $material->material_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('material_id') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="form-section-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            <span>Quantities</span>
        </div>
        <div class="form-section-body">
            <div class="form-grid-3">

                <div class="field">
                    <label for="invoice_qty">Invoice Quantity <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                        <input type="number" step="0.01" id="invoice_qty" name="invoice_qty"
                            value="{{ old('invoice_qty', $item->invoice_qty ?? '') }}"
                            placeholder="0.00" required
                            class="@error('invoice_qty') is-invalid @enderror"
                            {{ $isSubmitted ? 'readonly disabled' : '' }}>
                    </div>
                    @error('invoice_qty') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="received_qty">Received Quantity <span class="req">*</span></label>
                    <div class="input-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                            <path d="M16.5 9.4 7.55 4.24M3.29 7 12 12l8.71-5M12 22V12"/>
                        </svg>
                        <input type="number" step="0.01" id="received_qty" name="received_qty"
                            value="{{ old('received_qty', $item->received_qty ?? '') }}"
                            placeholder="0.00" required
                            class="@error('received_qty') is-invalid @enderror"
                            {{ $isSubmitted ? 'readonly disabled' : '' }}>
                    </div>
                    @error('received_qty') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="unit">Unit of Measurement <span class="req">*</span></label>
                    <div class="input-wrap select-wrap">
                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                        <select id="unit" name="unit" class="@error('unit') is-invalid @enderror" required {{ $isSubmitted ? 'disabled' : '' }}>
                            <option value="MT" {{ old('unit', $item->unit ?? 'MT') == 'MT' ? 'selected' : '' }}>Metric Tons (MT)</option>
                            <option value="KG" {{ old('unit', $item->unit ?? '') == 'KG' ? 'selected' : '' }}>Kilograms (KG)</option>
                        </select>
                    </div>
                    @error('unit') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="field full">
                    <label for="remarks">
                        Remarks
                        <span class="optional-tag">Optional</span>
                    </label>
                    <textarea id="remarks" name="remarks" class="no-icon @error('remarks') is-invalid @enderror" placeholder="Enter any additional notes..." {{ $isSubmitted ? 'readonly disabled' : '' }}>{{ old('remarks', $item->remarks ?? '') }}</textarea>
                    @error('remarks') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>
    </div>

    @if(!$isSubmitted)
    <div class="form-actions">
        <a href="{{ route('admin.mes.receiving.index') }}" class="btn btn-outline btn-sm">Cancel</a>
        
        <div style="display:flex; gap:10px; align-items:center;">
            @if($isEdit)
                <span id="autosaveStatus" style="font-size:12px; color:var(--text-muted); display:none;">Autosaved just now</span>
            @endif
            <button type="submit" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                {{ $isEdit ? 'Save Draft' : 'Create Record' }}
            </button>
        </div>
    </div>
    @endif

</form>
@endsection

@if($isEdit && !$isSubmitted)
@push('scripts')
<script>
    // Autosave functionality
    let timeoutId;
    const form = document.getElementById('receivingForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    const statusText = document.getElementById('autosaveStatus');
    
    inputs.forEach(input => {
        input.addEventListener('change', scheduleAutosave);
        if(input.type === 'text' || input.type === 'number' || input.tagName === 'TEXTAREA') {
            input.addEventListener('keyup', scheduleAutosave);
        }
    });

    function scheduleAutosave() {
        clearTimeout(timeoutId);
        statusText.style.display = 'inline';
        statusText.textContent = 'Saving...';
        timeoutId = setTimeout(performAutosave, 3000); // 3 seconds after last edit
    }

    async function performAutosave() {
        const formData = new FormData(form);
        formData.append('_token', '{{ csrf_token() }}');
        
        try {
            const response = await fetch("{{ route('admin.mes.receiving.autosave', $item->id) }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if(response.ok) {
                const data = await response.json();
                statusText.textContent = 'Autosaved at ' + new Date().toLocaleTimeString();
                setTimeout(() => statusText.style.display = 'none', 5000);
            } else {
                statusText.textContent = 'Autosave failed';
                statusText.style.color = 'var(--error)';
            }
        } catch(e) {
            statusText.textContent = 'Network error during autosave';
            statusText.style.color = 'var(--error)';
        }
    }
</script>
@endpush
@endif
