<?php
session_start();
require_once "../config/db.local.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        // We select everything (*) to ensure we get the 'role' column
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            // THIS LINE IS KEY: It stops the "Undefined index" error
            $_SESSION["user_role"] = $user["role"]; 
            
            header("Location: ../dashboard/dashboard.php");
            exit;
        } else {
            $message = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --bg: #0f172a;
        }

        body {
            background-color: var(--bg);
            color: white;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background-image: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }

        /* The Watchful Eye Animation */
        .eye-container {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            margin: 0 auto 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid var(--primary);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
            transition: all 0.5s ease;
            overflow: hidden;
        }

        .pupil {
            width: 35px;
            height: 35px;
            background: #000;
            border-radius: 50%;
            position: absolute;
            transition: transform 0.1s ease-out;
        }

        /* Privacy Mode (Password focus) */
        body.privacy-mode .eye-container {
            transform: scaleY(0.1);
            background: #475569;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(15px);
            border-radius: 28px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px;
            border-radius: 12px;
        }

        .form-control:focus {
            background: #0f172a;
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        /* Fix placeholder visibility */
        .form-control::placeholder {
            color: rgba(148, 163, 184, 0.6);
            opacity: 1;
        }

        /* Fix label visibility */
        .form-label {
            color: #94a3b8 !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
        }

        /* Floating Background Particles */
        .particle {
            position: absolute;
            background: rgba(99, 102, 241, 0.2);
            border-radius: 50%;
            z-index: -1;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="particle" style="width: 80px; height: 80px; left: 10%; bottom: -10%;"></div>
<div class="particle" style="width: 40px; height: 40px; left: 80%; bottom: -5%; animation-delay: 5s;"></div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="eye-container" id="eye">
                <div class="pupil" id="pupil"></div>
            </div>

            <div class="glass-card">
                <h3 class="text-center mb-1">Welcome Back</h3>
                <p class="text-center text-muted small mb-4">Secure Terminal Access</p>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger py-2 small text-center"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold form-label mb-1">OFFICIAL EMAIL</label>
                        <input type="email" name="email" class="form-control" id="emailField" placeholder="name@company.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold form-label mb-1">ENCRYPTED PASSWORD</label>
                        <input type="password" name="password" class="form-control" id="passField" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-login w-100 text-white">
                        Login <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="../register/register.php" class="text-decoration-none small text-primary">Request Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const eye = document.getElementById('eye');
    const pupil = document.getElementById('pupil');
    const passField = document.getElementById('passField');
    const body = document.body;

    // Mouse Tracking Eye Logic
    document.addEventListener('mousemove', (e) => {
        if (!body.classList.contains('privacy-mode')) {
            const rect = eye.getBoundingClientRect();
            const eyeX = rect.left + rect.width / 2;
            const eyeY = rect.top + rect.height / 2;
            
            const angle = Math.atan2(e.clientY - eyeY, e.clientX - eyeX);
            const distance = Math.min(rect.width / 4, Math.hypot(e.clientX - eyeX, e.clientY - eyeY) / 10);
            
            const pupilX = Math.cos(angle) * distance;
            const pupilY = Math.sin(angle) * distance;
            
            pupil.style.transform = `translate(${pupilX}px, ${pupilY}px)`;
        }
    });

    // Toggle "Privacy Mode" when typing password
    passField.addEventListener('focus', () => body.classList.add('privacy-mode'));
    passField.addEventListener('blur', () => body.classList.remove('privacy-mode'));
</script>

</body>
</html>