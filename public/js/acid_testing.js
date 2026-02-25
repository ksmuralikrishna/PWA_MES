let db;

const request = indexedDB.open('mes_db', 4);

request.onupgradeneeded = function (e) {
    db = e.target.result;

    if (!db.objectStoreNames.contains('acid_testings')) {
        db.createObjectStore('acid_testings', { keyPath: 'id', autoIncrement: true });
    }
};

request.onsuccess = function (e) {
    db = e.target.result;
    // loadLotNumbers();
    syncPendingData();
};

document.addEventListener("DOMContentLoaded", async () => {
    const lotSelect = document.getElementById("lotNumber");
    const supplierInput = document.getElementById("supplier");
    const vehicleInput = document.getElementById("vehicle_number");
    const inhouseWeightInput = document.getElementById("inhouse_weighbridge_weight");

    // Fetch receivings from the existing API
    const receivings = await fetch("/api/receivings")
        .then(res => res.json())
        .catch(() => []);
    
    // Populate Lot Number dropdown
    receivings.forEach(r => {
        const option = document.createElement("option");
        option.value = r.id;           // receiving id
        option.textContent = r.lot_no; // display lot number
        lotSelect.appendChild(option);
    });

    // console.log('receivings after populating:', receivings);
    // Update dependent fields when Lot Number changes
    lotSelect.addEventListener("change", () => {
        const selectedLot = receivings.find(r => r.id == lotSelect.value);
        if (selectedLot) {
            supplierInput.value = selectedLot.supplier;
            vehicleInput.value = selectedLot.vehicle_number;
            inhouseWeightInput.value = selectedLot.received_qty;
        } else {
            supplierInput.value = "";
            vehicleInput.value = "";
            inhouseWeightInput.value = "";
        }
    });
});

document.getElementById('acidTestingForm').addEventListener('submit', e => {
    e.preventDefault();

    const formData = Object.fromEntries(new FormData(e.target));
    console.log('formData:', formData);
    if (navigator.onLine) {
        sendToServer(formData);
    } else {
        saveOffline(formData);
    }
});

function sendToServer(data) {
    fetch('/api/acid-testings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    });
    showToast('Record saved to server successfully!');
}

function saveOffline(data) {
    console.log('saveoffline started');
    const tx = db.transaction('acid_testings', 'readwrite');
    const store = tx.objectStore('acid_testings');
    store.add({ ...data, synced: false });
    showToast('Record saved to offline, Will sync automatically when online.', 'warning');
}

function syncPendingData() {
    if (!navigator.onLine) return;
console.log('syncPendingData started'); 
    const tx = db.transaction('acid_testings', 'readwrite');
    const store = tx.objectStore('acid_testings');

    store.getAll().onsuccess = e => {
        e.target.result.forEach(item => {
            if (!item.synced) {
                sendToServer(item);
                item.synced = true;
                store.put(item);
            }
        });
    };
    console.log('syncPendingData completed');
}

window.addEventListener('online', syncPendingData);


function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    // Set the message
    toastMessage.textContent = message;
    
    // Optional: change color based on type
    if (type === 'success') {
        toast.style.background = 'var(--success)'; // Green
    } else if (type === 'error') {
        toast.style.background = '#dc2626'; // Red
        toast.querySelector('.toast-icon').textContent = '✗';
    } else if (type === 'warning') {
        toast.style.background = 'var(--warning)'; // Yellow/Orange
        toast.querySelector('.toast-icon').textContent = '⚠';
    }
    
    // Show the toast
    toast.classList.add('show');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 5000);
}