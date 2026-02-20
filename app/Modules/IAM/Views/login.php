<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideFlow | Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary-color: #f59e0b; /* Amber 500 */
            --primary-hover: #d97706; /* Amber 600 */
            --teal-gradient: linear-gradient(135deg, #34d399 0%, #059669 100%);
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-page: #10b981; /* Fallback */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--teal-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 900px;
            display: flex;
            overflow: hidden;
            position: relative;
            min-height: 500px; /* Ensure height matches reference */
        }

        /* Left Side - Image Area */
        .login-image {
            flex: 1;
            background: linear-gradient(135deg, #6ee7b7 0%, #34d399 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Placeholder for Car Image */
        .car-placeholder {
            font-size: 8rem;
            color: white;
            text-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* Right Side - Form Area */
        .login-form-container {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-btn:hover { color: var(--text-main); }

        h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
        }

        .login-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .link-green {
            color: #059669; /* Emerald 600 */
            text-decoration: none;
            font-weight: 500;
        }
        .link-green:hover { text-decoration: underline; }

        .link-grey {
            color: var(--text-muted);
            text-decoration: none;
        }
        .link-grey:hover { color: var(--text-main); }

        .signup-text {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        #errorMsg {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 1rem;
            text-align: center;
            display: none;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
            }
            .login-image {
                display: none; /* Hide image on small screens or adjust height */
            }
            .login-form-container {
                padding: 2rem;
            }
        }
    </style>
    <script>
        if (localStorage.getItem('jwt_token')) {
            window.location.href = '<?= base_url('dispatch') ?>';
        }
    </script>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            
            <!-- Left Side - Visual -->
            <div class="login-image">
                <!-- Replace with actual car image -->
                <div class="car-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:white; opacity:0.8"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="login-form-container">
                <button class="close-btn" onclick="history.back()">&times;</button>
                
                <h2>Login your Account</h2>
                <p class="subtitle">Since this is your first trip, you'll need to provide us with some information before you can check out.</p>

                <form id="loginForm">
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Email Id" required value="admin@rideflow.app">
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-input" placeholder="Password" required>
                    </div>

                    <button type="submit" class="btn-login">
                        Login
                    </button>
                    
                    <div class="login-footer">
                        <a href="#" class="link-green">Login with phone instead</a>
                        <a href="#" class="link-grey">Forgot password?</a>
                    </div>
                    
                    <div class="signup-text">
                        New member? <a href="<?= base_url('register') ?>" class="link-green">Sign Up</a>
                    </div>

                    <div id="errorMsg"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            const errorDiv = document.getElementById('errorMsg');
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const originalText = btn.innerText;
            btn.innerText = 'Signing in...';
            btn.style.opacity = '0.7';
            btn.disabled = true;
            errorDiv.style.display = 'none';

            try {
                const response = await fetch('<?= base_url('api/auth/login') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                let result;
                const text = await response.text();
                try {
                    result = JSON.parse(text);
                } catch (e) {
                    throw new Error("Server error: " + text.substring(0, 100));
                }

                if (response.ok) {
                    localStorage.setItem('jwt_token', result.token);
                    window.location.href = '<?= base_url('dispatch') ?>';
                } else {
                    throw new Error(result.messages?.error || result.message || 'Login failed');
                }
            } catch (error) {
                errorDiv.innerText = error.message;
                errorDiv.style.display = 'block';
                btn.innerText = originalText;
                btn.style.opacity = '1';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
