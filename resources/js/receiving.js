// IndexedDB setup
let db;
const DB_NAME = "mes_db";
const DB_VERSION = 1;

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
    const tx = db.transaction("sync_queue", "readwrite");
    const store = tx.objectStore("sync_queue");
    const request = store.getAll();

    request.onsuccess = async () => {
        for (const item of request.result) {
            try {
                const res = await fetch(item.api, {
                    method: item.method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(item.payload)
                });
                if (!res.ok) throw new Error("Server error");

                // Remove from queue if synced
                store.delete(item.id);
                markAsSynced(item.id);

            } catch (err) {
                item.retry_count++;
                store.put(item); // keep in queue to retry later
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
    };
}

// Auto-sync on reconnect
window.addEventListener("online", syncData);