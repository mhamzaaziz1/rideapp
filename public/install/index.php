<?php
// Check if already installed
if (file_exists(__DIR__ . '/.installed') || file_exists(__DIR__ . '/../../.env')) {
    header("Location: ../");
    exit;
}

// Get standard url guess
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$dir = dirname(dirname($script)); // get the public folder path
if ($dir == '/' || $dir == '\\') {
    $dir = '';
}
$base_url = rtrim($protocol . "://" . $host . $dir, '/') . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideApp Installer</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .installer-container {
            max-width: 600px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        .card-body {
            padding: 30px;
        }
        .submit-btn {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
        }
        #loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container installer-container">
        <div class="card">
            <div class="card-header text-center">
                <h2>RideApp Installation</h2>
                <p class="mb-0">Please provide your database and application details</p>
            </div>
            <div class="card-body">
                <div id="alert-container"></div>
                
                <form id="install-form">
                    <h5 class="mb-3 border-bottom pb-2">Application Settings</h5>
                    
                    <div class="mb-3">
                        <label for="app_url" class="form-label">Base URL</label>
                        <input type="url" class="form-control" id="app_url" name="app_url" value="<?php echo htmlspecialchars($base_url); ?>" required>
                        <div class="form-text">The full URL to your application's public directory.</div>
                    </div>
                    
                    <h5 class="mb-3 mt-4 border-bottom pb-2">Database Settings</h5>
                    
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Database Host</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_name" class="form-label">Database Name</label>
                        <input type="text" class="form-control" id="db_name" name="db_name" placeholder="rideapp" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_user" class="form-label">Database Username</label>
                        <input type="text" class="form-control" id="db_user" name="db_user" placeholder="root" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_pass" class="form-label">Database Password</label>
                        <input type="password" class="form-control" id="db_pass" name="db_pass">
                    </div>

                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Admin User Password (admin@rideflow.app)</label>
                        <input type="text" class="form-control" id="admin_password" name="admin_password" value="password123" required>
                        <div class="form-text">Specify the password for the default admin user.</div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary submit-btn" id="install-btn">
                            Install Application
                        </button>
                    </div>
                </form>

                <div id="loading" class="text-center mt-4">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 fs-5" id="loading-text">Installing... This might take a few moments as we run migrations.<br/>Please do not close this window.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('install-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const btn = document.getElementById('install-btn');
            const loading = document.getElementById('loading');
            const alertContainer = document.getElementById('alert-container');
            
            // Hide previous alerts
            alertContainer.innerHTML = '';
            
            // Show loading, hide form
            form.style.display = 'none';
            loading.style.display = 'block';
            
            const formData = new FormData(form);
            
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (data.status === 'success') {
                    alertContainer.innerHTML = `<div class="alert alert-success">
                        <h4>Installation Successful!</h4>
                        <p>${data.message}</p>
                        <hr>
                        <a href="../" class="btn btn-success">Go to Application</a>
                    </div>`;
                } else {
                    form.style.display = 'block';
                    alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                form.style.display = 'block';
                alertContainer.innerHTML = `<div class="alert alert-danger">An error occurred during installation. Please check server logs. ${error}</div>`;
            });
        });
    </script>
</body>
</html>
