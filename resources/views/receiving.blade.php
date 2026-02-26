<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receiving Entry</title>
    <!-- <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('css/form-entry.css') }}"> -->
    <link rel="manifest" href="//{{ request()->getHost() }}/manifest.json">
    <link rel="stylesheet" href="//{{ request()->getHost() }}/css/form-entry.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<!-- <header>
    <div class="logo">Receive<span>.</span>IQ</div>
    <div class="header-badge">Warehouse Portal</div>
</header> -->

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
                <button type="button" class="cancel-btn" onclick="window.location.href='index.html'">Cancel</button>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-section">
        <div class="table-header-row">
            <h2>Receiving Records</h2>
            <!-- <span class="record-count" id="recordCount">0 records</span> -->
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
    <script src="//{{ request()->getHost() }}/js/db.js" defer></script>
    <script src="//{{ request()->getHost() }}/js/receiving.js" defer></script>
    
    <script>
    window.addEventListener('DOMContentLoaded', () => {
    if (!window.MES_DB || typeof MES_DB.init !== 'function') {
        console.error('MES_DB.init is not defined; skipping offline setup.');
        return;
    }

    MES_DB.init()
    .then((database) => {
        window.db = database; // <-- important
        console.log('DB initialized for Receiving module', database);

            // render table
            renderTable();

            const form = document.getElementById("receivingForm");
            form.addEventListener("submit", e => {
                e.preventDefault();

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

                renderTable(true); // append offline
                syncData(); // try sync immediately if online
            });
        })
        .catch((err) => {
            console.error('DB init failed, running in online-only mode:', err);
        });
});
</script>
    
    <!-- <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('Service Worker Registered'))
                .catch(err => console.error('SW registration failed:', err));
        }
    </script> -->
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