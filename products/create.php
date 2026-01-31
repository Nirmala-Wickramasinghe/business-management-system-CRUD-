
<?php
session_start();

// 1. Check if logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Check if the user is an admin
if ($_SESSION["user_role"] !== 'admin') {
    // You can redirect with a message or stop execution
    echo "<script>alert('Access Denied: You do not have permission to view this page.'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Manager | Add Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-dark: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.7);
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
            color: #f8fafc;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(99, 102, 241, 0.2);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .form-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .input-group {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #64748b;
            padding-left: 1.2rem;
        }

        .form-control {
            background: transparent;
            border: none;
            color: white;
            padding: 12px 1rem;
            font-weight: 500;
        }

        .form-control:focus {
            background: transparent;
            box-shadow: none;
            color: white;
        }

        .form-control::placeholder {
            color: #475569;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            color: white;
            margin-top: 1rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
            color: white;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card">
                <div class="header-icon">
                    <i class="fas fa-plus"></i>
                </div>
                
                <h2 class="text-center h4 mb-2">New Catalog Item</h2>
                <p class="text-center text-muted small mb-4">Add high-value assets to your inventory.</p>

                <form method="POST" action="store.php">
                    <div class="mb-4">
                        <label class="form-label">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-box-open"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Perfume" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Unit Price (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit w-100">
                        Add to Inventory <i class="fas fa-arrow-right"></i>
                    </button>

                    <a href="index.php" class="back-link">
                        <i class="fas fa-chevron-left me-1"></i> Back to Dashboard
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>