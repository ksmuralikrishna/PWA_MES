@extends('admin.layouts.app')

@section('title', $page_title)

@push('styles')
<style>
    /* Green Ranches Style Variations */
    :root {
        --gr-primary: #1e7b51;
        --gr-primary-light: #e8f5ed;
        --gr-bg: #f8f9fc;
        --gr-border: #eef0f6;
        --gr-text-dark: #2d3748;
        --gr-text-muted: #718096;
        --gr-white: #ffffff;
        --radius-lg: 12px;
        --radius-md: 8px;
        --shadow-xs: 0 1px 2px rgba(0,0,0,0.02);
        --shadow-sm: 0 2px 6px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.06);
    }
    
    body { background: var(--gr-bg); }
    
    .page-wrapper {
        display: flex; flex-direction: column; gap: 20px;
    }

    /* Top Sub-header */
    .top-action-bar {
        background: var(--gr-white);
        border-radius: var(--radius-lg);
        padding: 16px 24px;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap; gap: 16px;
    }
    .top-action-left {
        display: flex; align-items: center; gap: 16px;
    }
    .page-icon-box {
        width: 48px; height: 48px;
        background: var(--gr-primary-light);
        border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        color: var(--gr-primary);
    }
    .page-icon-box svg { width: 24px; height: 24px; stroke: currentColor; }
    .page-info h2 { font-size: 18px; font-weight: 700; color: var(--gr-text-dark); margin-bottom: 2px; }
    .page-info p { font-size: 13px; color: var(--gr-text-muted); }

    .top-action-right {
        display: flex; align-items: center; gap: 12px;
    }
    .btn-white {
        background: var(--gr-white); border: 1px solid #dcdfe6; color: var(--gr-text-dark);
        padding: 9px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-white:hover { background: #f7f8fa; border-color: #c0c4cc; }
    .btn-primary {
        background: var(--gr-primary); border: 1px solid var(--gr-primary); color: #fff;
        padding: 9px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary:hover { background: #166140; border-color: #166140; }

    /* Summary Cards */
    .summary-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;
    }
    .summary-card {
        background: var(--gr-white); padding: 20px; border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;
    }
    .sc-icon {
        width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #f0f4f8; color: var(--gr-text-muted);
    }
    .sc-icon.primary { background: var(--gr-primary-light); color: var(--gr-primary); }
    .sc-icon.success { background: #e6f6f0; color: #059669; }
    .sc-icon.warning { background: #fff8e6; color: #d97706; }
    
    .sc-info h4 { font-size: 20px; font-weight: 700; color: var(--gr-text-dark); margin-bottom: 2px; }
    .sc-info p { font-size: 12px; color: var(--gr-text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

    /* Filters Section */
    .filter-card {
        background: var(--gr-white); border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .filter-header {
        padding: 14px 24px; border-bottom: 1px solid #edf2f7;
        display: flex; align-items: center; justify-content: space-between;
        cursor: pointer; user-select: none;
    }
    .fh-left { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: var(--gr-text-dark); }
    .fh-left svg { width: 16px; height: 16px; stroke: currentColor; }
    .fh-badge {
        background: var(--gr-primary-light); color: var(--gr-primary);
        font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 600;
    }
    .fh-right svg { width: 16px; height: 16px; stroke: var(--gr-text-muted); transition: transform 0.3s; }
    .filter-card.open .fh-right svg { transform: rotate(180deg); }
    
    .filter-body {
        padding: 20px 24px; display: none;
    }
    .filter-card.open .filter-body { display: block; }
    
    .filter-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: flex-end;
    }
    .f-group label { display: block; font-size: 12px; color: var(--gr-text-muted); margin-bottom: 6px; font-weight: 500; }
    .f-group input, .f-group select {
        width: 100%; border: 1px solid #e2e8f0; border-radius: var(--radius-md); font-family: inherit;
        padding: 9px 12px; font-size: 13px; color: var(--gr-text-dark); background: #fff; outline: none; transition: border 0.2s;
    }
    .f-group input:focus, .f-group select:focus { border-color: var(--gr-primary); }

    /* List Card & Tabs */
    .list-card {
        background: var(--gr-white); border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .list-tabs {
        display: flex; align-items: center; padding: 12px 24px; gap: 8px; overflow-x: auto;
        border-bottom: 1px solid #edf2f7;
    }
    .list-tab {
        padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;
        color: var(--gr-text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px;
        background: transparent; transition: all 0.2s;
    }
    .list-tab:hover { background: #f7fafc; color: var(--gr-text-dark); }
    .list-tab.active { background: var(--gr-primary); color: #fff; }
    .list-tab .t-badge {
        background: rgba(255,255,255,0.2); padding: 1px 6px; border-radius: 10px; font-size: 11px;
    }
    .list-tab:not(.active) .t-badge { background: #edf2f7; color: var(--gr-text-muted); }

    /* Search row in list card */
    .list-toolbar {
        padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .search-input {
        position: relative; max-width: 320px; width: 100%;
    }
    .search-input svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px; stroke: var(--gr-text-muted); pointer-events: none;
    }
    .search-input input {
        width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #e2e8f0; border-radius: 20px;
        font-size: 13px; background: #f8fafc; outline: none; transition: all 0.2s;
    }
    .search-input input:focus { border-color: var(--gr-primary); background: #fff; box-shadow: 0 0 0 3px var(--gr-primary-light); }
    
    .list-meta { font-size: 12px; color: var(--gr-text-muted); }

    /* Table Styling */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 800px; }
    thead { background: #f0f7f4; }
    th {
        padding: 14px 24px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;
        color: var(--gr-primary); text-transform: uppercase; border-bottom: 2px solid #e2e8f0;
    }
    td {
        padding: 16px 24px; font-size: 13px; color: var(--gr-text-dark);
        border-bottom: 1px solid #edf2f7; vertical-align: middle;
    }
    tr:hover td { background: #f8fafc; }
    tr:last-child td { border-bottom: none; }

    /* Checkbox styled */
    .checkbox-wrap {
        display: flex; align-items: center; justify-content: center;
    }
    .checkbox-wrap input {
        width: 16px; height: 16px; accent-color: var(--gr-primary); cursor: pointer;
    }

    /* Badges */
    .status-badge {
        display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .status-badge.draft { background: #e2e8f0; color: #4a5568; }
    .status-badge.submitted { background: #d1fae5; color: #065f46; }

    /* Action Menu */
    .action-dropdown { position: relative; display: inline-block; }
    .btn-action {
        width: 32px; height: 32px; background: #f1f5f9; border: none; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        color: var(--gr-text-muted); transition: all 0.2s;
    }
    .btn-action:hover { background: #e2e8f0; color: var(--gr-text-dark); }
    .btn-action svg { width: 16px; height: 16px; fill: currentColor; stroke: none; }
    
    .dropdown-content {
        display: none; position: absolute; right: 0; top: calc(100% + 4px);
        background: #fff; min-width: 140px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-radius: var(--radius-md); border: 1px solid #e2e8f0; z-index: 100;
        padding: 4px 0; animation: fadeUp 0.15s ease-out;
    }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
    .action-dropdown.show .dropdown-content { display: block; }
    
    .dm-item {
        display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px 16px;
        border: none; background: none; text-align: left; font-size: 13px; font-family: inherit;
        color: var(--gr-text-dark); cursor: pointer; transition: background 0.2s; text-decoration: none;
    }
    .dm-item:hover { background: #f8fafc; color: var(--gr-primary); }
    .dm-item svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; line-height: 1; }
    .dm-item.danger { color: #e53e3e; }
    .dm-item.danger:hover { background: #fff5f5; color: #c53030; }
    .dm-divider { height: 1px; background: #e2e8f0; margin: 4px 0; }

    /* Pagination */
    .pagination-wrapper {
        padding: 16px 24px; border-top: 1px solid #edf2f7; display: flex; align-items: center; justify-content: space-between;
    }
    .paginator { display: flex; gap: 4px; }
    .paginator a, .paginator span {
        width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
        border-radius: var(--radius-md); font-size: 13px; font-weight: 500; text-decoration: none;
        color: var(--gr-text-dark); background: transparent; transition: all 0.2s;
    }
    .paginator a:hover { background: #f1f5f9; }
    .paginator .active { background: var(--gr-primary-light); color: var(--gr-primary); border: 1px solid var(--gr-primary-light); }
    .paginator .disabled { color: #cbd5e0; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="page-wrapper">

    <!-- TOP NAV CARD -->
    <div class="top-action-bar">
        <div class="top-action-left">
            <div class="page-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                    <path d="M16.5 9.4 7.55 4.24M3.29 7 12 12l8.71-5M12 22V12"/>
                </svg>
            </div>
            <div class="page-info">
                <h2>{{ $page_title }}</h2>
                <p>Manage raw material receiving, drafts and submissions</p>
            </div>
        </div>
        <div class="top-action-right">
            <a href="{{ route('admin.dashboard') }}" class="btn-white">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Back to Dashboard
            </a>
            <a href="{{ route('admin.mes.receiving.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Create Receiving
            </a>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="sc-icon primary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="sc-info">
                <h4>{{ $list_items->total() }}</h4>
                <p>Total Records</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="sc-icon warning">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
            <div class="sc-info">
                <h4>—</h4>
                <p>Pending Drafts</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="sc-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="sc-info">
                <h4>—</h4>
                <p>Submitted Orders</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="sc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="sc-info">
                <h4>{{ date('M') }}</h4>
                <p>This Month</p>
            </div>
        </div>
    </div>

    <!-- FILTER COMPONENT -->
    @php
        $hasFilters = request()->anyFilled(['search', 'date_from', 'date_to', 'supplier_id', 'status']);
        $filterCount = count(array_filter([request('date_from'), request('date_to'), request('supplier_id'), request('status')]));
    @endphp
    <div class="filter-card {{ $hasFilters ? 'open' : '' }}" id="filterCard">
        <div class="filter-header" onclick="document.getElementById('filterCard').classList.toggle('open')">
            <div class="fh-left">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                Filter Records
                <span class="fh-badge">{{ $filterCount }} filter{{ $filterCount !== 1 ? 's' : '' }}</span>
            </div>
            <div class="fh-right">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
        </div>
        <div class="filter-body">
            <form method="GET" action="{{ route('admin.mes.receiving.index') }}" id="filterForm">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                
                <div class="filter-grid">
                    <div class="f-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        </select>
                    </div>
                    
                    <div class="f-group">
                        <label>Supplier</label>
                        <select name="supplier_id">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="f-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="f-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="f-group" style="display:flex; gap:10px;">
                        <button type="submit" class="btn-primary" style="flex:1; justify-content:center; padding:10px 16px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            Apply Filters
                        </button>
                        @if($filterCount > 0)
                            <a href="{{ route('admin.mes.receiving.index') }}" class="btn-white" style="padding:10px 16px;">Clear</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- LIST TABLE CARD -->
    <div class="list-card">
        
        <!-- Tabs -->
        <div class="list-tabs">
            @php $currentStatus = request('status', 'all'); @endphp
            <a href="{{ route('admin.mes.receiving.index', array_merge(request()->query(), ['status' => null])) }}" 
               class="list-tab {{ !$currentStatus || $currentStatus == 'all' ? 'active' : '' }}">
                All <span class="t-badge">{{ request('status') ? '' : $list_items->total() }}</span>
            </a>
            <a href="{{ route('admin.mes.receiving.index', array_merge(request()->query(), ['status' => 'draft'])) }}" 
               class="list-tab {{ $currentStatus == 'draft' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Draft <span class="t-badge">{{ current($list_items) && $currentStatus == 'draft' ? $list_items->total() : '' }}</span>
            </a>
            <a href="{{ route('admin.mes.receiving.index', array_merge(request()->query(), ['status' => 'submitted'])) }}" 
               class="list-tab {{ $currentStatus == 'submitted' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Submitted <span class="t-badge">{{ current($list_items) && $currentStatus == 'submitted' ? $list_items->total() : '' }}</span>
            </a>
        </div>

        <!-- Search & Info Row -->
        <div class="list-toolbar">
            <form method="GET" action="{{ route('admin.mes.receiving.index') }}" class="search-input">
                @foreach(request()->except(['search', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders by number, supplier, material...">
            </form>
            <div class="list-meta">
                {{ $list_items->total() }} result(s)
            </div>
        </div>

        <!-- The Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px; padding-right:0;"><div class="checkbox-wrap"><input type="checkbox"></div></th>
                        <th>Record / Lot no</th>
                        <th>Date</th>
                        <th>Supplier & Material</th>
                        <th>Vehicle</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list_items as $item)
                        <tr>
                            <td style="padding-right:0;"><div class="checkbox-wrap"><input type="checkbox"></div></td>
                            <td>
                                <strong style="color:var(--gr-text-dark);">{{ $item->lot_no }}</strong>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:2px;">
                                    <span style="font-weight:500;">{{ optional($item->receipt_date)->format('d M Y') ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:2px;">
                                    <span style="font-weight:600; color:var(--gr-text-dark);">{{ $item->supplier->supplier_name ?? '—' }}</span>
                                    <span style="font-size:11px; color:var(--gr-text-muted);">{{ $item->material->material_name ?? '—' }}</span>
                                </div>
                            </td>
                            <td>{{ $item->vehicle_number ?? '—' }}</td>
                            <td>
                                <strong>{{ number_format($item->received_qty, 2) }}</strong> <span style="font-size:11px; color:var(--gr-text-muted);">{{ $item->unit }}</span>
                            </td>
                            <td>
                                @if($item->status === 'submitted')
                                    <span class="status-badge submitted">Submitted</span>
                                @else
                                    <span class="status-badge draft">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-dropdown" id="dropdown-{{ $item->id }}">
                                    <button class="btn-action" onclick="toggleActionMenu({{ $item->id }}, event)">
                                        <svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="17" r="1.5"/></svg>
                                    </button>
                                    <div class="dropdown-content">
                                        
                                        <!-- View Route -->
                                        <a href="{{ route('admin.mes.receiving.edit', $item->id) }}" class="dm-item">
                                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            View Details
                                        </a>

                                        @if($item->status === 'draft')
                                            <!-- Edit Route -->
                                            <a href="{{ route('admin.mes.receiving.edit', $item->id) }}" class="dm-item">
                                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                Edit Record
                                            </a>
                                            <div class="dm-divider"></div>
                                            <!-- Delete Route -->
                                            <form method="POST" action="{{ route('admin.mes.receiving.destroy', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dm-item danger">
                                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:40px 20px;">
                                <div style="color:var(--gr-text-muted); margin-bottom:12px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </div>
                                <h3 style="font-size:16px; font-weight:600; color:var(--gr-text-dark); margin-bottom:4px;">No records found</h3>
                                <p style="font-size:13px; color:var(--gr-text-muted);">Try adjusting your search or filters to find what you're looking for.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($list_items->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size:13px; color:var(--gr-text-muted);">
                Showing {{ $list_items->firstItem() }} to {{ $list_items->lastItem() }} of {{ $list_items->total() }} results
            </div>
            <div class="paginator">
                @if($list_items->onFirstPage())
                    <span class="disabled">&laquo;</span>
                @else
                    <a href="{{ $list_items->previousPageUrl() }}">&laquo;</a>
                @endif
                
                @foreach($list_items->getUrlRange(max(1,$list_items->currentPage()-2), min($list_items->lastPage(),$list_items->currentPage()+2)) as $page => $url)
                    @if($page == $list_items->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($list_items->hasMorePages())
                    <a href="{{ $list_items->nextPageUrl() }}">&raquo;</a>
                @else
                    <span class="disabled">&raquo;</span>
                @endif
            </div>
        </div>
        @else
        <div class="pagination-wrapper">
             <div style="font-size:13px; color:var(--gr-text-muted);">
                Showing 1 to {{ $list_items->count() }} of {{ $list_items->total() }} results
            </div>           
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleActionMenu(id, e) {
        e.stopPropagation();
        const menu = document.getElementById('dropdown-' + id);
        const isOpen = menu.classList.contains('show');
        // Close all others
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('show'));
        // Open the clicked one if it wasn't open
        if (!isOpen) menu.classList.add('show');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('show'));
    });

    // Debounce search input
    let searchTimeout;
    const searchInput = document.querySelector('.search-input input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchInput.closest('form').submit();
            }, 600);
        });
    }
</script>
@endpush
