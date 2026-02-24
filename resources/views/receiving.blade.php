<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receiving Entry</title>
    <link rel="manifest" href="//{{ request()->getHost() }}/manifest.json">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #0f1117;
            --paper: #f5f3ef;
            --accent: #e84f27;
            --accent2: #2563eb;
            --muted: #6b7280;
            --border: #d1cec8;
            --card: #ffffff;
            --success: #16a34a;
            --warning: #ca8a04;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            min-height: 100vh;
            color: var(--ink);
            background-image: 
                radial-gradient(circle at 20% 10%, rgba(232,79,39,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 90%, rgba(37,99,235,0.06) 0%, transparent 50%);
        }

        /* HEADER */
        header {
            background: var(--ink);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        }

        .logo span { color: var(--accent); }

        .header-badge {
            font-size: 0.75rem;
            background: rgba(255,255,255,0.1);
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* LAYOUT */
        .page-wrapper {
            max-width: 1280px;
            margin: 0 auto;
            
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 900px) {
            .page-wrapper {
                grid-template-columns: 1fr;
                padding: 1.25rem 1rem;
            }
        }

        /* FORM CARD */
        .form-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            position: sticky;
            top: 80px;
        }

        .form-header {
            background: var(--ink);
            padding: 1.5rem 1.75rem;
            color: white;
        }

        .form-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .form-header p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
            margin-top: 0.25rem;
        }

        .form-body {
            padding: 1.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-field.full { grid-column: 1 / -1; }

        label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
        }

        input, select, textarea {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            background: var(--paper);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            color: var(--ink);
            width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2rem;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent2);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: white;
        }

        input::placeholder { color: #b0aaa3; }

        .submit-btn {
            margin-top: 1.25rem;
            width: 100%;
            background: var(--accent);
            color: white;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(232,79,39,0.25);
        }

        .submit-btn:hover {
            background: #cf3d17;
            box-shadow: 0 6px 20px rgba(232,79,39,0.35);
        }

        .submit-btn:active { transform: scale(0.98); }

        /* TABLE SECTION */
        .table-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .table-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .table-header-row h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .record-count {
            font-size: 0.8rem;
            background: var(--ink);
            color: white;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
        }

        .table-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        thead {
            background: var(--ink);
            color: white;
        }

        th {
            padding: 0.85rem 1rem;
            text-align: left;
            font-family: 'Syne', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #faf9f7; }

        td {
            padding: 0.85rem 1rem;
            color: var(--ink);
            vertical-align: middle;
            white-space: nowrap;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }

        .status-badge.synced {
            background: #dcfce7;
            color: var(--success);
        }

        .status-badge.pending {
            background: #fef9c3;
            color: var(--warning);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--muted);
        }

        .empty-state svg { opacity: 0.3; margin-bottom: 0.75rem; }
        .empty-state p { font-size: 0.9rem; }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            background: var(--ink);
            color: white;
            padding: 0.85rem 1.25rem;
            border-radius: 10px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            z-index: 999;
            max-width: 320px;
        }

        .toast.show { transform: translateY(0); opacity: 1; }
        .toast .toast-icon { color: #4ade80; font-size: 1.1rem; }

        @media (max-width: 480px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-field.full { grid-column: 1; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Receive<span>.</span>IQ</div>
    <div class="header-badge">Warehouse Portal</div>
</header>

<div class="page-wrapper">

    <!-- FORM -->
    <div class="form-card">
        <div class="form-header">
            <h2>New Receiving Entry</h2>
            <p>Fill in all required fields to log a shipment</p>
        </div>
        <div class="form-body">
            <form id="receivingForm">
                <div class="form-grid">

                    <div class="form-field">
                        <label>Date</label>
                        <input type="date" name="date" required id="dateInput">
                    </div>

                    <div class="form-field">
                        <label>Unit</label>
                        <select name="unit" required>
                            <option value="KG">KG</option>
                            <option value="MT">MT</option>
                            <option value="NOS">NOS</option>
                            <option value="BOX">BOX</option>
                            <option value="LTR">LTR</option>
                        </select>
                    </div>

                    <div class="form-field full">
                        <label>Supplier</label>
                        <select name="supplier" required>
                            <option value="">Select Supplier</option>
                            <option value="Supplier A">Supplier A</option>
                            <option value="Supplier B">Supplier B</option>
                        </select>
                    </div>

                    <div class="form-field full">
                        <label>Material</label>
                        <select name="material" required>
                            <option value="">Select Material</option>
                            <option value="Material X">Material X</option>
                            <option value="Material Y">Material Y</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Invoice Qty</label>
                        <input type="number" name="invoice_qty" placeholder="0" required min="0">
                    </div>

                    <div class="form-field">
                        <label>Received Qty</label>
                        <input type="number" name="received_qty" placeholder="0" required min="0">
                    </div>

                    <div class="form-field">
                        <label>Vehicle No</label>
                        <input type="text" name="vehicle_number" placeholder="e.g. MH12AB1234" required>
                    </div>

                    <div class="form-field">
                        <label>Lot No</label>
                        <input type="text" name="lot_no" placeholder="e.g. LOT-001" required>
                    </div>

                    <div class="form-field full">
                        <label>Remarks</label>
                        <input type="text" name="remarks" placeholder="Optional notes...">
                    </div>

                    <input type="hidden" name="operator_id" value="1">
                </div>

                <button type="submit" class="submit-btn">＋ Save Entry</button>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-section">
        <div class="table-header-row">
            <h2>Receiving Records</h2>
            <span class="record-count" id="recordCount">0 records</span>
        </div>

        <div class="table-card">
            <div class="table-scroll">
                <table id="receivingTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Material</th>
                            <th>Inv. Qty</th>
                            <th>Rcv. Qty</th>
                            <th>Unit</th>
                            <th>Vehicle</th>
                            <th>Lot No</th>
                            <th>Remarks</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7l9-4 9 4v10l-9 4-9-4V7z"/><path d="M12 3v18M3 7l9 4 9-4"/></svg>
                                    <p>No entries yet. Submit the form to add records.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
    <!-- <script src="{{ asset('js/receiving.js') }}" defer></script> -->
    <script src="//{{ request()->getHost() }}/js/receiving.js" defer></script>
    <script>
        // Wait for receiving.js to load before calling initDB
        window.addEventListener('DOMContentLoaded', () => {
            initDB().then(() => {
                renderTable();
                document.getElementById("receivingForm").addEventListener("submit", e => {
                e.preventDefault();
                const form = e.target;
                saveReceiving({
                    date: form.date.value,
                    supplier: form.supplier.value,
                    material: form.material.value,
                    invoice_qty: parseInt(form.invoice_qty.value),
                    received_qty: parseInt(form.received_qty.value),
                    unit: form.unit.value,
                    vehicle_number: form.vehicle_number.value,
                    lot_no: form.lot_no.value,
                    remarks: form.remarks.value,
                    operator_id: parseInt(form.operator_id.value),
                    created_at: new Date().toISOString()
                });
                form.reset();
                // Append offline record without clearing table
                renderTable(true); // true = offline append
                syncData(); // try sync immediately if online
            });
        });
    });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('Service Worker Registered'))
                .catch(err => console.error('SW registration failed:', err));
        }
    </script>
    <script>
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            const btnInstall = document.createElement('button');
            btnInstall.innerText = 'Install MES';
            btnInstall.style = "position: fixed; bottom: 20px; right: 20px; z-index: 999;";
            document.body.appendChild(btnInstall);

            btnInstall.addEventListener('click', async () => {
                deferredPrompt.prompt();
                const choiceResult = await deferredPrompt.userChoice;
                if(choiceResult.outcome === 'accepted'){
                    console.log('App installed');
                }
                deferredPrompt = null;
                btnInstall.remove();
            });
        });
    </script>
</body>
</html>