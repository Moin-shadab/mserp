<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easy Sign In - MS ERP</title>

    <!-- Local Offline Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 960px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-side {
            background: linear-gradient(145deg, #2563eb, #1d4ed8, #1e40af);
            color: white;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 0.4rem 0.9rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            width: fit-content;
        }

        .form-side {
            padding: 3rem 2.5rem;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            border-color: #2563eb;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .role-card {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.65rem 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
        }

        .role-card:hover {
            background-color: #eff6ff;
            border-color: #93c5fd;
            transform: scale(1.02);
        }

        .role-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="row g-0">
        <!-- Left Side: Visual Hero & Zero-Config Tagline -->
        <div class="col-lg-5 hero-side d-none d-lg-flex">
            <div>
                <div class="hero-badge mb-4">
                    <i class="bi bi-magic"></i> Child-Simple ERP System
                </div>
                <h2 class="fw-extrabold display-6 text-white mb-3">Enterprise ERP Made Simple</h2>
                <p class="text-white-50 leading-relaxed mb-4">
                    Zero complexity, 100% dynamic architecture, and uncompromised enterprise security. Built so simple that anyone can use it instantly.
                </p>
            </div>
            
            <div class="pt-4 border-top border-white-20">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-shield-lock-fill fs-3 text-warning"></i>
                    <div>
                        <div class="fw-bold text-white small">Bank-Grade Security</div>
                        <div class="text-white-50 extra-small">Bcrypt Hashing, CSRF Protection & Access Control</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Interactive Login Form & 1-Click Role Accounts -->
        <div class="col-lg-7 form-side">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-box-seam-fill text-primary fs-3"></i>
                    <h4 class="fw-extrabold m-0 text-dark">Welcome to MS ERP</h4>
                </div>
                <p class="text-muted small">Sign in with your credentials or click any demo role below to jump right in.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger rounded-3 py-2 px-3 small mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/login" id="login-form" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold text-secondary">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" required placeholder="name@mserp.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" required placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 mt-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Workspace
                </button>
            </form>

            <!-- 1-Click Child-Simple Quick Logins -->
            <div class="border-top pt-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold extra-small text-uppercase text-secondary tracking-wider">⚡ 1-Click Quick Demo Sign In</span>
                    <span class="badge bg-light text-dark extra-small border">Password: password</span>
                </div>

                <div class="row g-2">
                    <div class="col-sm-6">
                        <button type="button" class="role-card" onclick="fillAndSubmit('admin@mserp.com', 'password')">
                            <div class="role-icon-box bg-primary text-white"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <div class="fw-bold small text-dark">CFO / Super Admin</div>
                                <div class="text-muted extra-small">Unrestricted System Control</div>
                            </div>
                        </button>
                    </div>

                    <div class="col-sm-6">
                        <button type="button" class="role-card" onclick="fillAndSubmit('north.head@mserp.com', 'password')">
                            <div class="role-icon-box bg-success text-white"><i class="bi bi-graph-up-arrow"></i></div>
                            <div>
                                <div class="fw-bold small text-dark">North Sales Head</div>
                                <div class="text-muted extra-small">Sales Department Supervisor</div>
                            </div>
                        </button>
                    </div>

                    <div class="col-sm-6">
                        <button type="button" class="role-card" onclick="fillAndSubmit('accounts.head@mserp.com', 'password')">
                            <div class="role-icon-box bg-warning text-dark"><i class="bi bi-bank"></i></div>
                            <div>
                                <div class="fw-bold small text-dark">Finance Head</div>
                                <div class="text-muted extra-small">Accounts Ledger Manager</div>
                            </div>
                        </button>
                    </div>

                    <div class="col-sm-6">
                        <button type="button" class="role-card" onclick="fillAndSubmit('rep.north1@mserp.com', 'password')">
                            <div class="role-icon-box bg-info text-white"><i class="bi bi-person-badge"></i></div>
                            <div>
                                <div class="fw-bold small text-dark">Sales Executive</div>
                                <div class="text-muted extra-small">Delhi Territory Officer</div>
                            </div>
                        </button>
                    </div>

                    <div class="col-sm-6">
                        <button type="button" class="role-card" onclick="fillAndSubmit('accounts.member@mserp.com', 'password')">
                            <div class="role-icon-box bg-purple text-white" style="background:#8b5cf6;"><i class="bi bi-receipt"></i></div>
                            <div>
                                <div class="fw-bold small text-dark">Accounts Assistant</div>
                                <div class="text-muted extra-small">Billing & Audit Entry</div>
                            </div>
                        </button>
                    </div>

                    <div class="col-sm-6">
                        <button type="button" class="role-card" onclick="fillAndSubmit('user@mserp.com', 'password')">
                            <div class="role-icon-box bg-secondary text-white"><i class="bi bi-person"></i></div>
                            <div>
                                <div class="fw-bold small text-dark">General Executive</div>
                                <div class="text-muted extra-small">Read-only Workspace</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillAndSubmit(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        document.getElementById('login-form').submit();
    }
</script>

</body>
</html>
