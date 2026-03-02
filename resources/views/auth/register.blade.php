<!DOCTYPE html>
{{--
    REGISTER VIEW — DUBATT NEXUS
    ─────────────────────────────────────────────────────────────────
    Controller must pass:
        $modules  →  Module::where('status','active')->orderBy('sequence_no')->get()

    Logo: place at  public/assets/images/dubatt-logo.png
    ─────────────────────────────────────────────────────────────────
--}}
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — DUBATT NEXUS</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green:        #1a7a3a;
            --green-dark:   #145f2d;
            --green-light:  #e8f5ed;
            --green-mid:    #c2e0cc;
            --green-xlight: #f4fbf6;
            --white:        #ffffff;
            --text:         #1a2e22;
            --text-mid:     #3d5a47;
            --text-muted:   #7a9985;
            --border:       #d0e8d8;
            --error:        #d93025;
        }

        html, body {
            font-family: 'Outfit', sans-serif;
            background: var(--green-xlight);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── TOP NAV BAR ── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 clamp(20px, 5vw, 60px);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-wrap {
            width: 40px; height: 40px;
            background: var(--white);
            border: 2px solid var(--green-mid);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .brand-logo-wrap img {
            width: 100%; height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--green);
            line-height: 1;
        }

        .brand-name span {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 10px;
            font-weight: 400;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .topbar-link {
            font-size: 13px;
            color: var(--text-muted);
        }

        .topbar-link a {
            color: var(--green);
            font-weight: 600;
            text-decoration: none;
        }

        .topbar-link a:hover { text-decoration: underline; }

        /* ── PAGE BODY ── */
        .page-body {
            padding: clamp(24px, 4vw, 48px) clamp(16px, 4vw, 40px);
            max-width: 860px;
            margin: 0 auto;
        }

        .page-heading {
            text-align: center;
            margin-bottom: 32px;
        }

        .page-heading h1 {
            font-size: clamp(22px, 3vw, 32px);
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .page-heading p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ── CARD ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(26,122,58,0.07);
        }

        /* Section headers inside card */
        .section-head {
            padding: 16px 32px;
            background: var(--green-light);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-head svg {
            width: 16px; height: 16px;
            stroke: var(--green);
            flex-shrink: 0;
        }

        .section-head span {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--green);
        }

        .section-body {
            padding: 28px 32px 32px;
        }

        /* ── FORM GRID ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 28px;
        }

        .field { display: flex; flex-direction: column; }
        .field.full { grid-column: 1 / -1; }

        .field label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-mid);
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .input-wrap .ico {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            stroke: var(--text-muted);
            pointer-events: none;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--green-xlight);
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: var(--text);
            outline: none;
            appearance: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input::placeholder { color: var(--text-muted); }

        input:focus, select:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(26,122,58,0.08);
        }

        input.is-invalid, select.is-invalid { border-color: var(--error); }
        input.is-invalid:focus, select.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(217,48,37,0.08);
        }

        /* Chevron for select */
        .select-wrap::after {
            content: '';
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--text-muted);
            pointer-events: none;
        }

        .error-msg {
            margin-top: 5px;
            font-size: 12px;
            color: var(--error);
        }

        /* ── FOOTER ACTION ── */
        .card-footer {
            padding: 20px 32px 28px;
            border-top: 1px solid var(--border);
            background: var(--green-xlight);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .footer-note {
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer-note a {
            color: var(--green);
            font-weight: 600;
            text-decoration: none;
        }

        .footer-note a:hover { text-decoration: underline; }

        .btn-register {
            padding: 13px 36px;
            background: var(--green);
            border: none;
            border-radius: 10px;
            color: var(--white);
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            white-space: nowrap;
        }

        .btn-register:hover {
            background: var(--green-dark);
            box-shadow: 0 8px 24px rgba(26,122,58,0.25);
            transform: translateY(-1px);
        }

        .btn-register:active { transform: translateY(0); }
        .btn-register svg { width: 18px; height: 18px; stroke: #fff; }

        /* page footer */
        .page-footer {
            text-align: center;
            padding: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── RESPONSIVE ── */

        /* Tablet 10.98" ≈ 820–1024px */
        @media (max-width: 1024px) {
            .form-grid { grid-template-columns: 1fr 1fr; gap: 16px 20px; }
            .section-body { padding: 24px 24px 28px; }
            .section-head { padding: 14px 24px; }
            .card-footer  { padding: 16px 24px 22px; }
        }

        /* Tablet portrait / large mobile (≤640px) */
        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: auto; }
            .section-body { padding: 20px 16px 24px; }
            .section-head { padding: 12px 16px; }
            .card-footer {
                flex-direction: column;
                align-items: stretch;
                padding: 16px;
            }
            .btn-register { justify-content: center; }
            .footer-note { text-align: center; }
        }

        /* Mobile (≤400px) */
        @media (max-width: 400px) {
            .topbar { padding: 0 16px; }
            .brand-name { font-size: 15px; }
            .page-body { padding: 16px 12px 32px; }
            .page-heading h1 { font-size: 20px; }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <nav class="topbar">
        <div class="brand">
            <div class="brand-logo-wrap">
                <img
                    src="{{ asset('assets/images/dubatt-logo.png') }}"
                    alt="DUBATT NEXUS"
                    onerror="this.outerHTML='<svg width=\'22\' height=\'22\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#1a7a3a\' stroke-width=\'2\'><path d=\'M12 2L2 7l10 5 10-5-10-5M2 17l10 5 10-5M2 12l10 5 10-5\'/></svg>'"
                >
            </div>
            <div class="brand-name">
                DUBATT NEXUS
                <span>Battery Management ERP</span>
            </div>
        </div>
        <div class="topbar-link">
            Already registered? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </nav>

    <!-- PAGE BODY -->
    <div class="page-body">

        <div class="page-heading">
            <h1>Create Your Account</h1>
            <p>Fill in the details below to register on the DUBATT NEXUS platform</p>
        </div>

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="card">

                <!-- SECTION 1: Personal Info -->
                <div class="section-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Personal Information</span>
                </div>
                <div class="section-body">
                    <div class="form-grid">

                        <div class="field full">
                            <label for="name">Full Name</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Your full name"
                                    required autofocus
                                    class="@error('name') is-invalid @enderror">
                            </div>
                            @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="email">Email Address</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email') }}"
                                    placeholder="your@email.com"
                                    required
                                    class="@error('email') is-invalid @enderror">
                            </div>
                            @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="phone">Phone Number</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 8.77 19.79 19.79 0 0 1 1.49 3a2 2 0 0 1 1.94-2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.9a16 16 0 0 0 6.29 6.29l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <input type="tel" id="phone" name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="+971 XX XXX XXXX"
                                    class="@error('phone') is-invalid @enderror">
                            </div>
                            @error('phone') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                <!-- SECTION 2: Work Details -->
                <div class="section-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                    <span>Work Details</span>
                </div>
                <div class="section-body">
                    <div class="form-grid">

                        <div class="field">
                            <label for="role_id">Role</label>
                            <div class="input-wrap select-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <select id="role_id" name="role_id" required
                                    class="@error('role_id') is-invalid @enderror">
                                    <option value="">Select your role</option>
                                    <option value="1" {{ old('role_id') == '1' ? 'selected' : '' }}>Receiver</option>
                                    <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Testing Incharge</option>
                                    <option value="3" {{ old('role_id') == '3' ? 'selected' : '' }}>BBSU Incharge</option>
                                    <option value="4" {{ old('role_id') == '4' ? 'selected' : '' }}>Smelting Incharge</option>
                                    <option value="5" {{ old('role_id') == '5' ? 'selected' : '' }}>Refinery Incharge</option>
                                </select>
                            </div>
                            @error('role_id') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="module_id">Department / Module</label>
                            <div class="input-wrap select-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                                </svg>
                                <select id="module_id" name="module_id"
                                    class="@error('module_id') is-invalid @enderror">
                                    <option value="">Select module</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                            {{ $module->module_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('module_id') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="plant_id">Plant ID</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <input type="text" id="plant_id" name="plant_id"
                                    value="{{ old('plant_id') }}"
                                    placeholder="e.g. PLT-001"
                                    class="@error('plant_id') is-invalid @enderror">
                            </div>
                            @error('plant_id') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="company_id">Company ID</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5M2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                                <input type="text" id="company_id" name="company_id"
                                    value="{{ old('company_id') }}"
                                    placeholder="e.g. DUB-001"
                                    class="@error('company_id') is-invalid @enderror">
                            </div>
                            @error('company_id') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                <!-- SECTION 3: Security -->
                <div class="section-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Security</span>
                </div>
                <div class="section-body">
                    <div class="form-grid">

                        <div class="field">
                            <label for="password">Password</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <input type="password" id="password" name="password"
                                    placeholder="Create a password"
                                    required
                                    class="@error('password') is-invalid @enderror">
                            </div>
                            @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-wrap">
                                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Confirm your password"
                                    required>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER ACTION -->
                <div class="card-footer">
                    <div class="footer-note">
                        Already have an account? <a href="{{ route('login') }}">Sign in here</a>
                    </div>
                    <button type="submit" class="btn-register">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                        Create Account
                    </button>
                </div>

            </div><!-- /card -->

        </form>

        <div class="page-footer">© {{ date('Y') }} DUBATT NEXUS. All rights reserved.</div>

    </div><!-- /page-body -->

</body>
</html>
