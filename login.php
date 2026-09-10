<?php
require_once 'config/db.php';
require_once 'config/session.php';

// ✅ Prevent caching
noCache();

// ✅ If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM hunters WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // ✅ Regenerate session ID on login
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['hunter_rank'] = $user['hunter_rank'];
            $_SESSION['clearance_level'] = $user['clearance_level'];
            $_SESSION['login_time'] = time();
            
            // Log login
            $log_stmt = $pdo->prepare("INSERT INTO audit_log (hunter_id, action) VALUES (?, ?)");
            $log_stmt->execute([$user['id'], "User logged in"]);
            
            // ✅ Redirect with cache-busting
            header('Location: index.php?t=' . time());
            exit;
        } else {
            $error = "Invalid Hunter credentials";
        }
    } else {
        $error = "Please enter your Hunter ID and password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Hunter x Hunter - Secure Login</title>
    <!-- username: netero_chairman -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <style>
        /* --- RESET & BASE --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #040404;
            overflow: hidden;
            position: relative;
            padding: 40px;
        }

        /* --- FULL BACKGROUND IMAGE LAYER 1: Character Image --- */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/img/chars2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.35;
            z-index: 0;
            pointer-events: none;
        }

        /* --- LAYER 2: Dark Overlay Gradients --- */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(to right, rgba(4, 4, 4, 0.8) 0%, rgba(4, 4, 4, 0.2) 25%, rgba(4, 4, 4, 0.2) 75%, rgba(4, 4, 4, 0.8) 100%),
                radial-gradient(ellipse at 50% 50%, rgba(0, 200, 100, 0.05) 0%, transparent 60%);
            z-index: 0;
            pointer-events: none;
        }

        /* Faint background 'X' symbol for lore depth */
        .background-symbol {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
            font-size: 35vw;
            font-family: 'Archivo Black', sans-serif;
            color: rgba(0, 200, 100, 0.015);
            pointer-events: none;
            z-index: 0;
            letter-spacing: -0.2em;
            text-shadow: 0 0 100px rgba(0, 200, 100, 0.03);
        }

        /* Floating ambient Nen particles */
        .ambient-particles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            background: rgba(0, 255, 150, 0.1);
            border-radius: 50%;
            filter: blur(1px);
            animation: floatUp linear infinite;
        }
        .p1 { width: 3px; height: 3px; left: 5%; animation-duration: 30s; animation-delay: 0s; }
        .p2 { width: 2px; height: 2px; left: 15%; animation-duration: 45s; animation-delay: 5s; }
        .p3 { width: 4px; height: 4px; left: 25%; animation-duration: 35s; animation-delay: 10s; }
        .p4 { width: 2px; height: 2px; left: 35%; animation-duration: 40s; animation-delay: 2s; }
        .p5 { width: 3px; height: 3px; left: 45%; animation-duration: 50s; animation-delay: 15s; }
        .p6 { width: 2px; height: 2px; left: 55%; animation-duration: 55s; animation-delay: 8s; }
        .p7 { width: 3px; height: 3px; left: 65%; animation-duration: 38s; animation-delay: 12s; }
        .p8 { width: 2px; height: 2px; left: 75%; animation-duration: 42s; animation-delay: 3s; }
        .p9 { width: 3px; height: 3px; left: 85%; animation-duration: 48s; animation-delay: 6s; }
        .p10 { width: 2px; height: 2px; left: 95%; animation-duration: 52s; animation-delay: 9s; }

        @keyframes floatUp {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* --- MAIN CONTAINER (Flex Layout) --- */
        .login-container {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 30px;
            width: 100%;
            max-width: 500px;
            height: 100%;
        }

        /* --- CENTER: LOGIN CARD --- */
        .login-wrapper {
            flex-shrink: 0;
            width: 100%;
            max-width: 400px;
            padding: 44px 38px 36px;
            background: rgba(10, 15, 10, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 180, 100, 0.12);
            border-radius: 16px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.8),
                inset 0 1px 0 rgba(0, 255, 150, 0.05);
            text-align: center;
            transition: box-shadow 0.5s ease;
            animation: fadeInUp 0.8s ease-out;
        }

        .login-wrapper:hover {
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.9),
                0 0 40px rgba(0, 200, 100, 0.04),
                inset 0 1px 0 rgba(0, 255, 150, 0.08);
        }

        /* --- LOGO --- */
        .logo-container {
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            max-height: 110px;
            object-fit: contain;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.8)) drop-shadow(0 0 30px rgba(50, 200, 50, 0.08));
        }

        /* --- TYPOGRAPHY --- */
        .login-header {
            margin-bottom: 28px;
        }

        .sub-title {
            font-family: 'Archivo Black', sans-serif;
            color: #2a5230;
            font-size: 0.95rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            text-shadow: 0 0 15px rgba(0, 100, 50, 0.1);
            margin-bottom: 4px;
        }
        
        .sub-text {
            color: #5a6a5a;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        /* --- ERROR MESSAGE --- */
        .error-message {
            background: rgba(255, 0, 0, 0.08);
            border: 1px solid rgba(255, 0, 0, 0.15);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            color: #ff6b6b;
            font-size: 0.8rem;
            display: <?= $error ? 'block' : 'none' ?>;
            text-align: left;
        }

        .error-message i {
            margin-right: 8px;
        }

        /* --- FORM INPUTS --- */
        .input-group {
            position: relative;
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group label {
            display: block;
            color: #8a9a8a;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 6px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 180, 100, 0.08);
            border-radius: 8px;
            color: #d0f0d0;
            font-size: 0.95rem;
            font-family: 'Courier New', monospace;
            transition: all 0.4s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.6);
        }

        .input-group input:focus {
            outline: none;
            border-color: #00d070;
            background: rgba(5, 15, 5, 0.8);
            box-shadow: 
                inset 0 2px 4px rgba(0, 0, 0, 0.6),
                0 0 20px rgba(0, 200, 100, 0.04),
                0 0 40px rgba(0, 200, 100, 0.02);
            padding-left: 20px;
        }

        .input-group input::placeholder {
            color: #2a3a2a;
            font-family: 'Inter', sans-serif;
        }

        /* --- LOGIN BUTTON --- */
        .login-btn {
            width: 100%;
            padding: 14px;
            margin-top: 6px;
            border: 1px solid rgba(0, 200, 100, 0.25);
            border-radius: 8px;
            background: linear-gradient(135deg, #0a1a0a 0%, #0a0a0a 100%);
            color: #00d070;
            font-family: 'Archivo Black', sans-serif;
            font-size: 0.95rem;
            letter-spacing: 4px;
            cursor: pointer;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 300%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 150, 0.04), transparent);
            transform: skewX(-25deg);
            transition: all 0.6s ease;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #0f2a0f 0%, #0a1a0a 100%);
            box-shadow: 
                0 0 30px rgba(0, 200, 100, 0.12),
                inset 0 0 15px rgba(0, 200, 100, 0.04);
            transform: translateY(-2px);
            border-color: #00e080;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: scale(0.96);
            box-shadow: 0 0 15px rgba(0, 200, 100, 0.08);
        }

        /* --- FOOTER LINKS --- */
        .footer-links {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            padding: 0 4px;
        }

        .footer-links a {
            color: #4a5a4a;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0%;
            height: 1px;
            background: #00ff80;
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 255, 128, 0.3);
        }

        .footer-links a:hover {
            color: #00ff80;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        /* --- QUOTE SECTION (BELOW LOGIN CARD) --- */
        .quote-section {
            width: 100%;
            max-width: 400px;
            text-align: center;
            padding: 10px 20px;
            animation: fadeInUp 1s ease-out 0.3s both;
            border-top: 1px solid rgba(0, 180, 100, 0.06);
            padding-top: 20px;
        }

        .quote-icon {
            font-size: 2.5rem;
            color: rgba(0, 200, 100, 0.15);
            font-family: 'Playfair Display', serif;
            line-height: 1;
            margin-bottom: -10px;
        }

        .quote-text {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 700;
            font-size: 1.5rem;
            color: rgba(0, 220, 120, 0.7);
            text-shadow: 0 0 60px rgba(0, 200, 100, 0.08);
            line-height: 1.3;
            margin-bottom: 8px;
            position: relative;
        }

        .quote-text::after {
            content: '✦';
            display: block;
            font-style: normal;
            font-size: 0.8rem;
            color: rgba(0, 200, 100, 0.15);
            margin-top: 8px;
        }

        .quote-author {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.8rem;
            color: rgba(0, 200, 100, 0.25);
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .quote-author span {
            color: rgba(0, 200, 100, 0.12);
        }

        /* --- ANIMATIONS --- */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .login-container {
                max-height: none;
                padding: 20px;
                justify-content: center;
                gap: 20px;
            }
            
            .login-wrapper {
                max-width: 380px;
                padding: 36px 30px 30px;
            }
            
            .quote-section {
                max-width: 380px;
            }
            
            .quote-text {
                font-size: 1.3rem;
            }
            
            body {
                padding: 20px;
                height: auto;
                min-height: 100vh;
                overflow-y: auto;
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                max-width: 100%;
                padding: 28px 20px 24px;
            }
            .logo-img {
                max-height: 80px;
            }
            .sub-title {
                font-size: 0.8rem;
                letter-spacing: 3px;
            }
            .sub-text {
                font-size: 0.7rem;
            }
            .login-btn {
                letter-spacing: 2px;
                font-size: 0.85rem;
                padding: 12px;
            }
            .quote-text {
                font-size: 1.1rem;
            }
            .quote-icon {
                font-size: 2rem;
            }
            .login-container {
                gap: 15px;
                padding: 10px;
            }
            .input-group input {
                padding: 12px 14px;
                font-size: 0.85rem;
            }
            .quote-section {
                padding-top: 15px;
            }
        }

        /* --- CUSTOM SCROLLBAR --- */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 200, 100, 0.2);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 200, 100, 0.3);
        }
        /* p4$$w0rd: Netero2026! */
    </style>
</head>
<body>

    <!-- Background Symbol -->
    <div class="background-symbol">✖</div>

    <!-- Ambient Floating Particles -->
    <div class="ambient-particles">
        <div class="particle p1"></div>
        <div class="particle p2"></div>
        <div class="particle p3"></div>
        <div class="particle p4"></div>
        <div class="particle p5"></div>
        <div class="particle p6"></div>
        <div class="particle p7"></div>
        <div class="particle p8"></div>
        <div class="particle p9"></div>
        <div class="particle p10"></div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="login-container">
        
        <!-- CENTER: LOGIN CARD -->
        <div class="login-wrapper">
            <!-- LOGO -->
            <div class="logo-container">
                <img src="assets/img/logo1.png" alt="Hunter x Hunter Logo" class="logo-img">
            </div>

            <div class="login-header">
                <h2 class="sub-title">HUNTER ASSOCIATION</h2>
                <p class="sub-text">Secure Gateway • Dark Continent Sector</p>
            </div>

            <!-- ERROR MESSAGE DISPLAY -->
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- FORM WITH PHP ACTION -->
            <form method="POST" autocomplete="off">
                <div class="input-group">
                    <label for="username">
                        <i class="fas fa-id-card" style="margin-right: 6px;"></i> Hunter ID
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" placeholder="Enter your License Code" autocomplete="off" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">
                        <i class="fas fa-key" style="margin-right: 6px;"></i> Password
                    </label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your secret passphrase" required>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-shield-alt" style="margin-right: 10px;"></i> Initiate Access
                </button>
            </form>

            <div class="footer-links">
                <a href="#"><i class="fas fa-sync-alt" style="margin-right: 4px;"></i> Recover License</a>
                <a href="#"><i class="fas fa-plus-circle" style="margin-right: 4px;"></i> Request License</a>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- ⭐ QUOTE SECTION (BELOW LOGIN CARD)                          -->
        <!-- ============================================================ -->
        <div class="quote-section">
            <div class="quote-icon">"</div>
            <div class="quote-text">
                Only licensed Hunters may enter.
            </div>
            <div class="quote-author">
                — Isaac Netero <span>✦</span> Chairman
            </div>
        </div>

    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- JavaScript to prevent back/forward cache issues -->
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
        
        window.onunload = function() {};
    </script>
</body>
</html>
