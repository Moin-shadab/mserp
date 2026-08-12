<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MS ERP • Next-Gen Enterprise Operating System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Standalone Website Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/website/css/website.css') }}">
</head>
<body>

    <!-- 8️⃣ LIQUID GLASS CANVAS BACKGROUND -->
    <div class="liquid-glass-bg">
        <div class="liquid-orb liquid-orb-1"></div>
        <div class="liquid-orb liquid-orb-2"></div>
        <div class="liquid-orb liquid-orb-3"></div>
    </div>

    <!-- 3️⃣ GLASSMORPHISM NAVIGATION BAR -->
    <nav class="navbar-glass">
        <a href="{{ url('/website') }}" class="navbar-brand">
            <img src="{{ asset('images/favicon.png') }}" alt="MS ERP Logo">
            <span>MS ERP Studio</span>
        </a>

        <ul class="nav-links">
            <li><a href="#features" class="nav-link-item">Features</a></li>
            <li><a href="#paradigms" class="nav-link-item">10 Design Paradigms</a></li>
            <li><a href="#bento" class="nav-link-item">Bento Grid</a></li>
            <li><a href="#roi" class="nav-link-item">ROI Calculator</a></li>
        </ul>

        <div class="nav-actions">
            <a href="{{ url('/login') }}" class="nav-link-item">Sign In</a>
            <a href="{{ url('/login') }}" class="btn-clay"><i class="bi bi-rocket-takeoff-fill"></i> Launch Live ERP</a>
        </div>
    </nav>

    <!-- 7️⃣ BRUTALISM TICKER TAPE BANNER -->
    <div class="brutalist-ticker-wrapper">
        <div class="brutalist-ticker-content">
            • NEXT-GEN ENTERPRISE OPERATING SYSTEM • 10 UI DESIGN PARADIGMS • LOW-CODE METADATA ENGINE • AG GRID POWERED • DYNAMIC BILLING & GST INVOICING • LIVE DEPLOYMENT • NEXT-GEN ENTERPRISE OPERATING SYSTEM • 10 UI DESIGN PARADIGMS •
        </div>
    </div>

    <!-- HERO SECTION: 10 SPATIAL UI & 6 MAXIMALISM -->
    <section class="hero-section">
        <span class="clay-pill-badge"><i class="bi bi-stars"></i> Enterprise Low-Code & Metadata Architecture</span>
        
        <h1 class="hero-title">Operate Your Enterprise at the Speed of Thought.</h1>
        
        <p class="hero-subtitle">
            A unified, multi-tenant ERP platform featuring automated SQL-to-CRUD metadata generators, real-time GST invoicing, multi-account email client, and dynamic permission matrix.
        </p>

        <div class="hero-cta-group">
            <a href="{{ url('/login') }}" class="btn-clay"><i class="bi bi-box-arrow-in-right"></i> Open Workspace Demo</a>
            <a href="#paradigms" class="btn-clay" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);"><i class="bi bi-palette-fill"></i> Explore 10 Design Paradigms</a>
        </div>

        <!-- 🔟 SPATIAL UI HERO PREVIEW CARD -->
        <div class="spatial-card bento-card col-span-12" style="max-width: 1050px; margin: 0 auto; text-align: left;">
            <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="height: 12px; width: 12px; border-radius: 50%; background: #ef4444;"></div>
                    <div style="height: 12px; width: 12px; border-radius: 50%; background: #f59e0b;"></div>
                    <div style="height: 12px; width: 12px; border-radius: 50%; background: #10b981;"></div>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #94a3b8; margin-left: 0.5rem;">mserp-workspace-console v3.4.0</span>
                </div>
                <span class="clay-pill-badge" style="font-size: 0.75rem;"><i class="bi bi-activity"></i> System Operational</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.2rem; margin-bottom: 1.5rem;">
                <div style="background: rgba(255,255,255,0.05); padding: 1.2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">Registered Modules</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #60a5fa;">{{ $stats['total_modules'] ?? 8 }} Modules</div>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 1.2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">Dynamic Pages</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #a78bfa;">{{ $stats['total_pages'] ?? 14 }} Pages</div>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 1.2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">Active Staff</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #34d399;">{{ $stats['active_users'] ?? 12 }} Users</div>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 1.2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">Invoices Generated</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #fbbf24;">{{ $stats['total_invoices'] ?? 42 }} Docs</div>
                </div>
            </div>
        </div>
    </section>

    <!-- INTERACTIVE 10 DESIGN PARADIGMS LIVE SWITCHER WIDGET -->
    <section class="paradigm-switcher-section" id="paradigms">
        <div class="section-header">
            <span class="clay-pill-badge"><i class="bi bi-palette"></i> Design Trends Engine</span>
            <h2 class="section-title">Experience 10 Design Paradigms Live</h2>
            <p class="hero-subtitle" style="font-size: 1rem;">Click any paradigm button below to instantly morph the UI component style in real time:</p>
        </div>

        <!-- Paradigm Selector Tabs -->
        <div class="paradigm-tabs">
            <button class="paradigm-tab-btn active" data-style="glassmorphism">3️⃣ Glassmorphism</button>
            <button class="paradigm-tab-btn" data-style="skeuomorphism">1️⃣ Skeuomorphism</button>
            <button class="paradigm-tab-btn" data-style="neomorphism">2️⃣ Neomorphism</button>
            <button class="paradigm-tab-btn" data-style="claymorphism">4️⃣ Claymorphism</button>
            <button class="paradigm-tab-btn" data-style="minimalism">5️⃣ Minimalism</button>
            <button class="paradigm-tab-btn" data-style="maximalism">6️⃣ Maximalism</button>
            <button class="paradigm-tab-btn" data-style="brutalism">7️⃣ Brutalism</button>
            <button class="paradigm-tab-btn" data-style="liquidglass">8️⃣ Liquid Glass</button>
            <button class="paradigm-tab-btn" data-style="bentogrid">9️⃣ Bento Grid</button>
            <button class="paradigm-tab-btn" data-style="spatialui">🔟 Spatial UI</button>
        </div>

        <!-- Morphing Live Preview Container -->
        <div class="paradigm-preview-box style-glassmorphism" id="paradigm-preview-box">
            <h3 class="demo-title" id="paradigm-demo-title">3️⃣ Glassmorphism Interface</h3>
            <p class="demo-desc" id="paradigm-demo-desc">
                Frosted translucent glass panels, backdrop blur filters, and light-refracting specular borders for futuristic multi-layered spatial depth.
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="demo-btn"><i class="bi bi-cpu-fill"></i> Execute Low-Code Generator</button>
                <button class="demo-btn" style="opacity: 0.85;"><i class="bi bi-sliders"></i> View Settings</button>
            </div>
        </div>
    </section>

    <!-- 9️⃣ BENTO GRID FEATURE SHOWCASE SECTION -->
    <section class="bento-section" id="bento">
        <div class="section-header">
            <span class="clay-pill-badge"><i class="bi bi-grid-3x3-gap-fill"></i> Architectural Capabilities</span>
            <h2 class="section-title">Engineered with a Modern Bento Grid Layout</h2>
            <p class="hero-subtitle" style="font-size: 1rem;">Modular enterprise capabilities accessible from one dynamic dashboard.</p>
        </div>

        <div class="bento-grid">
            <!-- Bento Card 1: SQL to AG Grid Developer Studio -->
            <div class="bento-card col-span-8 spatial-card">
                <div class="bento-icon-wrapper"><i class="bi bi-code-slash"></i></div>
                <h3 class="bento-card-title">100% Visual SQL-to-CRUD Developer Studio</h3>
                <p class="bento-card-desc">
                    Paste any SQL query to auto-detect table schemas, data types, primary keys, and formatting rules. Publish low-code metadata pages or standalone PHP Controllers instantly.
                </p>
                <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 12px; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #a78bfa;">
                    $devService->generatePage(['generation_mode' => 'metadata' | 'isolated_code']);
                </div>
            </div>

            <!-- Bento Card 2: GST Invoicing & Billing -->
            <div class="bento-card col-span-4 spatial-card">
                <div class="bento-icon-wrapper"><i class="bi bi-receipt-cutoff"></i></div>
                <h3 class="bento-card-title">Automated GST Invoicing & Billing</h3>
                <p class="bento-card-desc">
                    Automatic Intra-state (CGST + SGST) vs Inter-state (IGST) tax calculation, stock level validation, and print-ready PDF generator.
                </p>
            </div>

            <!-- Bento Card 3: Multi-Account Email Client -->
            <div class="bento-card col-span-4 spatial-card">
                <div class="bento-icon-wrapper"><i class="bi bi-envelope-at-fill"></i></div>
                <h3 class="bento-card-title">Multi-Account IMAP Email Suite</h3>
                <p class="bento-card-desc">
                    Connect team email accounts, view threaded discussions, sync folders, draft messages, and attach files directly inside the ERP.
                </p>
            </div>

            <!-- Bento Card 4: AI Notification Routing Matrix -->
            <div class="bento-card col-span-4 spatial-card">
                <div class="bento-icon-wrapper"><i class="bi bi-diagram-3-fill"></i></div>
                <h3 class="bento-card-title">Smart Notification Routing Matrix</h3>
                <p class="bento-card-desc">
                    Configurable incoming/outgoing routing rules connecting team members, department leads, and system broadcasts.
                </p>
            </div>

            <!-- Bento Card 5: Internal Team Chat -->
            <div class="bento-card col-span-4 spatial-card">
                <div class="bento-icon-wrapper"><i class="bi bi-chat-left-dots-fill"></i></div>
                <h3 class="bento-card-title">Internal Team Messaging & Threads</h3>
                <p class="bento-card-desc">
                    Real-time team messaging, group channels, direct message rules, message forwarding, and reply threads.
                </p>
            </div>
        </div>
    </section>

    <!-- LIVE ROI CALCULATOR WIDGET -->
    <section class="paradigm-switcher-section" id="roi">
        <div class="roi-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="clay-pill-badge"><i class="bi bi-calculator-fill"></i> Efficiency Calculator</span>
                <h2 class="section-title" style="margin-top: 0.5rem;">Calculate Your Team's Productivity Gains</h2>
                <p class="hero-subtitle" style="font-size: 0.95rem; margin-bottom: 0;">Drag the slider to estimate time and cost savings with MS ERP automated workflows:</p>
            </div>

            <div style="text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 800; color: #60a5fa;" id="roi-user-count">25 Active Team Members</div>
                <input type="range" min="5" max="250" value="25" class="slider-range" id="roi-user-slider">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem; text-align: center;">
                <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Time Saved / Month</div>
                    <div style="font-size: 2.2rem; font-weight: 800; color: #34d399;" id="roi-hours-saved">450 hrs/month</div>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Estimated Cost Savings</div>
                    <div style="font-size: 2.2rem; font-weight: 800; color: #fbbf24;" id="roi-cost-saved">$12,600 / month</div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 2.5rem;">
                <a href="{{ url('/login') }}" class="btn-clay"><i class="bi bi-rocket-takeoff-fill"></i> Start Your Enterprise Trial</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="website-footer">
        <div class="footer-container">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="{{ asset('images/favicon.png') }}" alt="Logo" style="height: 32px; width: 32px;">
                <span style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #fff; font-size: 1.1rem;">MS ERP System</span>
            </div>

            <ul class="footer-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#paradigms">Design Paradigms</a></li>
                <li><a href="#bento">Bento Grid</a></li>
                <li><a href="{{ url('/login') }}">Sign In</a></li>
            </ul>

            <div>
                © {{ date('Y') }} MS ERP Enterprise. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Standalone Website JavaScript -->
    <script src="{{ asset('assets/website/js/website.js') }}"></script>
</body>
</html>
