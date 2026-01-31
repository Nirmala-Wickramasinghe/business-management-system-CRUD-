<?php
require_once "../config/db.local.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $price = $_POST["price"];

    if (!empty($name) && !empty($price)) {
        $sql = "INSERT INTO products (name, price) VALUES (:name, :price)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":price", $price);

        if ($stmt->execute()) {
            // Instead of immediate redirect, we show success then redirect via JS
            $message = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --accent: #6366f1;
            --neon-green: #4ade80;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: radial-gradient(circle at center, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 12px;
            padding: 12px;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 1);
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            color: white;
        }

        .input-group-text {
            background: rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--accent);
            border-radius: 12px 0 0 12px;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--accent), #a855f7);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-save:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .success-overlay {
            display: <?php echo ($message === 'success') ? 'flex' : 'none'; ?>;
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.9);
            border-radius: 24px;
            z-index: 100;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 position-relative">
            
            <div class="glass-card">
                <div class="success-overlay" id="successBox">
                    <i class="fas fa-check-circle mb-3" style="font-size: 60px; color: var(--neon-green);"></i>
                    <h3>Product Added!</h3>
                    <p class="text-muted">Syncing to inventory...</p>
                </div>

                <div class="mb-4 d-flex align-items-center">
                    <a href="index.php" class="text-muted me-3"><i class="fas fa-arrow-left"></i></a>
                    <h2 class="h4 mb-0">New Inventory Item</h2>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="small text-uppercase opacity-50 fw-bold mb-2">Item Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Premium Subscription" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-uppercase opacity-50 fw-bold mb-2">Unit Price (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-save w-100 text-white">
                        Deploy to Catalog <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    // If PHP reports success, wait 2 seconds then redirect
    <?php if ($message === 'success'): ?>
        setTimeout(() => {
            window.location.href = "index.php";
        }, 2000);
    <?php endif; ?>
</script>

</body>
</html>