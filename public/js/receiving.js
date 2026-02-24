// IndexedDB setup
let db;
const DB_NAME = "mes_db";
const DB_VERSION = 2;

// ===== UI SHELL (for offline + online) =====
document.addEventListener("DOMContentLoaded", () => {
    const app = document.getElementById("app");
    if (!app) return;

    app.innerHTML = `
        <h2>Receiving</h2>

        <form id="receivingForm">
            <input type="date" name="date" required>
            <input type="text" name="supplier" placeholder="Supplier" required>
            <input type="text" name="material" placeholder="Material" required>
            <input type="number" name="invoice_qty" placeholder="Invoice Qty" required>
            <input type="number" name="received_qty" placeholder="Received Qty" required>
            <input type="text" name="unit" placeholder="Unit" required>
            <input type="text" name="vehicle_number" placeholder="Vehicle No" required>
            <input type="text" name="lot_no" placeholder="Lot No" required>
            <input type="text" name="remarks" placeholder="Remarks">
            <button type="submit">Save</button>
        </form>

        <h3>Receiving Records</h3>
        <table border="1" id="receivingTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Material</th>
                    <th>Invoice Qty</th>
                    <th>Received Qty</th>
                    <th>Unit</th>
                    <th>Vehicle</th>
                    <th>Lot No</th>
                    <th>Remarks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    `;
});

function initDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = (event) => {
            db = event.target.result;
            if (!db.objectStoreNames.contains("receivings")) {
                db.createObjectStore("receivings", { keyPath: "id" });
            }
            if (!db.objectStoreNames.contains("sync_queue")) {
                db.createObjectStore("sync_queue", { keyPath: "id" });
            }
        };

        request.onsuccess = (event) => {
            db = event.target.result;
            resolve();
        };

        request.onerror = () => reject("DB init failed");
    });
}

// Save Receiving locally + add to sync queue
function saveReceiving(formData) {
    const id = crypto.randomUUID();
    const record = { id, ...formData, status: "pending", created_at: new Date().toISOString() };

    const tx = db.transaction(["receivings", "sync_queue"], "readwrite");
    tx.objectStore("receivings").put(record);
    tx.objectStore("sync_queue").put({
        id,
        api: "/api/receivings",
        method: "POST",
        payload: record,
        retry_count: 0
    });

    alert("✔ Saved locally! Will sync automatically when online.");
}

// Background sync to server
async function syncData() {
    if (!navigator.onLine) return;

    const readTx = db.transaction("sync_queue", "readonly");
    const readStore = readTx.objectStore("sync_queue");
    const request = readStore.getAll();

    request.onsuccess = async () => {
        const items = request.result.sort((a, b) => new Date(a.payload.created_at) - new Date(b.payload.created_at));

        for (const item of items) {
            try {
                const res = await fetch(item.api, {
                    method: item.method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(item.payload),
                });
                if (!res.ok) throw new Error("Server error");

                const delTx = db.transaction("sync_queue", "readwrite");
                delTx.objectStore("sync_queue").delete(item.id);

                markAsSynced(item.id);
            } catch (err) {
                item.retry_count++;

                const retryTx = db.transaction("sync_queue", "readwrite");
                retryTx.objectStore("sync_queue").put(item);
            }
        }
    };
}




// Mark record as synced in UI
function markAsSynced(id) {
    const tx = db.transaction("receivings", "readwrite");
    const store = tx.objectStore("receivings");
    const req = store.get(id);
    req.onsuccess = () => {
        const record = req.result;
        record.status = "synced";
        store.put(record);

        // Update row color in table
        const tr = document.querySelector(`tr[data-lot="${record.lot_no}"]`);
        if (tr) {
            tr.querySelector("td:last-child").textContent = "synced";
            tr.querySelector("td:last-child").style.color = "green";
        }
    };
}

// Auto-sync on reconnect
window.addEventListener("online", syncData);

// Render all receivings in the table
// Fetch data from Laravel API
async function fetchServerData() {
    try {
        const res = await fetch("/api/receivings");
        if (!res.ok) throw new Error("Failed to fetch server data");
        return await res.json(); // array of records
    } catch (err) {
        console.warn("Server offline or error:", err);
        return [];
    }
}

// Fetch only pending offline entries from sync_queue
function fetchPendingOfflineData() {
    return new Promise((resolve) => {
        const tx = db.transaction("sync_queue", "readonly");
        const store = tx.objectStore("sync_queue");
        const request = store.getAll();

        request.onsuccess = () => {
            // Convert to table-friendly format
            const data = request.result.map(item => ({
                ...item.payload,   // original record
                status: "pending"  // always pending in queue
            }));
            resolve(data);
        };

        request.onerror = () => resolve([]);
    });
}

// Render combined table
async function renderTable(offlineAppend = false) {
    const tbody = document.querySelector("#receivingTable tbody");

    if (!offlineAppend) {
        // Clear table only if not offline append
        tbody.innerHTML = "";
    }

    const serverData = offlineAppend ? [] : await fetchServerData(); // skip server fetch offline
    const pendingData = await fetchPendingOfflineData();

    // Combine records
    const combined = [...serverData, ...pendingData];

    // Sort newest first
    combined.sort((a,b)=> new Date(b.created_at) - new Date(a.created_at));

    // Append to table
    for (const r of combined) {
        // Check if the row for this lot_no already exists to prevent duplicates
        if (!tbody.querySelector(`tr[data-lot="${r.lot_no}"]`)) {
            const tr = document.createElement("tr");
            tr.setAttribute("data-lot", r.lot_no); // unique identifier
            tr.innerHTML = `
                <td>${r.date}</td>
                <td>${r.supplier}</td>
                <td>${r.material}</td>
                <td>${r.invoice_qty}</td>
                <td>${r.received_qty}</td>
                <td>${r.unit}</td>
                <td>${r.vehicle_number}</td>
                <td>${r.lot_no}</td>
                <td>${r.remarks || ""}</td>
                <td style="color: ${r.status === 'pending' ? 'red' : 'green'}">${r.status}</td>
            `;
            tbody.appendChild(tr);
        }
    }
}

