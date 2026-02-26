<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acid Testing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('css/form-entry.css') }}"> -->
    <link rel="manifest" href="//{{ request()->getHost() }}/manifest.json">
    <link rel="stylesheet" href="//{{ request()->getHost() }}/css/form-entry.css">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"> -->

</head>
<body>
    <!-- Header from receiving page -->
    <!-- <header>
        <div class="logo">ACID<span>TEST</span></div>
        <div class="header-badge">QUALITY CONTROL</div>
    </header> -->

    <div class="page-wrapper"  >
        <!-- FORM CARD -->
        <div class="form-card">
            <div class="form-header">
                <h2>Acid Testing</h2>
                <p>Enter acid test parameters for the selected lot</p>
            </div>
            <div class="form-body">
                <form id="acidTestingForm">
                    <div class="form-grid">
                        <!-- Date -->
                        <div class="form-field">
                            <label>Date</label>
                            <input type="date" name="test_date" id="date" required>
                        </div>

                        <!-- Lot Number -->
                        <div class="form-field">
                            <label>Lot Number</label>
                            <select name="lot_number" id="lotNumber" required>
                                <option value="">Select Lot</option>
                            </select>
                        </div>

                        <!-- Supplier (readonly) -->
                        <div class="form-field full">
                            <label>Supplier</label>
                            <input type="text" name="supplier" id="supplier" readonly placeholder="Auto-filled from lot">
                        </div>

                        <!-- Vehicle Number (readonly) -->
                        <div class="form-field">
                            <label>Vehicle Number</label>
                            <input type="text" name="vehicle_number" id="vehicle_number" readonly placeholder="Auto-filled">
                        </div>

                        <!-- In-house Weight (readonly) -->
                        <div class="form-field">
                            <label>In-house Weigh Bridge Weight</label>
                            <input type="number" name="inhouse_weighbridge_weight" id="inhouse_weighbridge_weight" readonly step="0.01" placeholder="Auto-filled">
                        </div>

                        <!-- Average Pallet Weight -->
                        <div class="form-field">
                            <label>Average Pallet Weight</label>
                            <input type="number" name="avg_pallet_weight" id="average_pallet_weight" step="0.01" required placeholder="0.00">
                        </div>

                        <!-- Foreign Material Weight -->
                        <div class="form-field">
                            <label>Foreign Material Weight</label>
                            <input type="number" name="foreign_material_weight" id="foreign_material_weight" step="0.01" required placeholder="0.00">
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="operator_id" value="1">
                    </div>

                    <button type="submit" class="submit-btn">＋ Submit Acid Test</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='index.html'">Cancel</button>
                </form>
            </div>
        </div>

        <!-- TABLE SECTION for Acid Test Records -->
        <div class="table-section">
            <div class="table-header-row">
                <h2>Acid Test Records</h2>
            </div>

            <div class="table-card">
                <div class="table-scroll">
                    <table id="acidTestingTable">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Pallet No</th>
                                <th>Gross Weight</th>
                                <th>Average Pallet & Foreign Weight</th>
                                <th>Net Weight</th>
                                <th>Remarks</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M3 7l9-4 9 4v10l-9 4-9-4V7z"/>
                                            <path d="M12 3v18M3 7l9 4 9-4"/>
                                        </svg>
                                        <p>No acid test records yet. Submit the form to add records.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast notification (hidden by default) -->
    <div class="toast" id="toast">
        <span class="toast-icon">✓</span>
        <span id="toastMessage">Record saved successfully</span>
    </div>

    <!-- <script src="{{ asset('js/acid_testing.js') }}" defer></script> -->
    <script src="//{{ request()->getHost() }}/js/db.js" defer></script>
    <script src="//{{ request()->getHost() }}/js/acid_testing.js" defer></script>
    
</body>
</html>