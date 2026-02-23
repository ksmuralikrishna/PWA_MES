<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receiving Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Receiving Entry</h2>

        <form id="receivingForm" class="space-y-4">

            <div>
                <label class="block text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Supplier</label>
                <select name="supplier" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Select Supplier</option>
                    <option value="Supplier A">Supplier A</option>
                    <option value="Supplier B">Supplier B</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Material</label>
                <select name="material" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Select Material</option>
                    <option value="Material X">Material X</option>
                    <option value="Material Y">Material Y</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Invoice Qty</label>
                <input type="number" name="invoice_qty" placeholder="Invoice Qty" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Received Qty</label>
                <input type="number" name="received_qty" placeholder="Received Qty" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Unit</label>
                <select name="unit" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="KG">KG</option>
                    <option value="MT">MT</option>
                    <option value="NOS">NOS</option>
                    <option value="BOX">BOX</option>
                    <option value="LTR">LTR</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Vehicle No</label>
                <input type="text" name="vehicle_number" placeholder="Vehicle No" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Lot No</label>
                <input type="text" name="lot_no" placeholder="Lot No" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Remarks</label>
                <input type="text" name="remarks" placeholder="Remarks"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <input type="hidden" name="operator_id" value="1">

            <button type="submit"
                    class="w-full bg-blue-500 text-white font-semibold py-2 rounded hover:bg-blue-600 transition duration-200">
                Save
            </button>

        </form>
    </div>
    <script src="{{ asset('js/receiving.js') }}"></script>
    <script>
        initDB().then(() => {
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
                    operator_id: parseInt(form.operator_id.value)
                });
                form.reset();
                syncData(); // try sync immediately if online
            });
        });
    </script>
</body>
</html>