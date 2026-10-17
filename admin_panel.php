<?php
require_once 'config/db.php';
require_once 'config/session.php';

// ✅ Prevent caching
noCache();

// ✅ Require login
requireLogin();

// 🔥 THIS IS A PRANK PAGE - JUST TROLLS THE PLAYER
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>⚠️ Access Denied</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #040404;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/img/char2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(to right, rgba(4, 4, 4, 0.9) 0%, rgba(4, 4, 4, 0.4) 25%, rgba(4, 4, 4, 0.4) 75%, rgba(4, 4, 4, 0.9) 100%),
                radial-gradient(ellipse at 50% 50%, rgba(0, 200, 100, 0.03) 0%, transparent 60%);
            z-index: 0;
            pointer-events: none;
        }
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
            background: rgba(0, 255, 150, 0.08);
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
        .prank-container {
            position: relative;
            z-index: 2;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .prank-card {
            background: rgba(10, 15, 10, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 2px solid rgba(231, 76, 60, 0.3);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 60px rgba(231, 76, 60, 0.05);
            padding: 40px;
            animation: pulseBorder 2s ease-in-out infinite;
        }
        @keyframes pulseBorder {
            0%, 100% { border-color: rgba(231, 76, 60, 0.3); }
            50% { border-color: rgba(231, 76, 60, 0.6); }
        }
        .prank-icon {
            font-size: 5rem;
            color: #e74c3c;
            margin-bottom: 20px;
            display: block;
            animation: shake 0.5s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        .prank-title {
            font-family: 'Archivo Black', sans-serif;
            font-size: 2rem;
            color: #e74c3c;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 16px;
            text-shadow: 0 0 40px rgba(231, 76, 60, 0.2);
        }
        .prank-text {
            color: #8a9a8a;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .prank-text .highlight {
            color: #e74c3c;
            font-weight: 700;
        }
        .btn-prank {
            background: transparent;
            color: #e74c3c;
            border: 2px solid #e74c3c;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
        }
        .btn-prank:hover {
            background: #e74c3c;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(231, 76, 60, 0.3);
        }
        .hidden-clue {
            margin-top: 30px;
            opacity: 0.1;
            font-size: 0.5rem;
            color: #1a2a1a;
            user-select: none;
        }
        @media (max-width: 480px) {
            .prank-container { padding: 20px; }
            .prank-card { padding: 24px; }
            .prank-title { font-size: 1.5rem; }
            .prank-icon { font-size: 3.5rem; }
        }
    </style>
</head>
<body>
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

    <div class="prank-container">
        <div class="prank-card">
            <i class="bi bi-exclamation-triangle-fill prank-icon"></i>
            <h1 class="prank-title">ACCESS DENIED</h1>
            <p class="prank-text">
                This panel has been <span class="highlight">compromised</span> by the Phantom Troupe.
                <br><br>
                All administrative records have been <span class="highlight">encrypted</span> and 
                moved to a secure location.
                <br><br>
                <span style="color: #5a6a5a; font-size: 0.9rem;">
                    <i class="bi bi-emoji-frown"></i> Nice try, Hunter.
                </span>
            </p>
            <a href="index.php" class="btn-prank">
                <i class="bi bi-arrow-left me-2"></i> Return to Dashboard
            </a>
            <div class="hidden-clue">
                // The real panel is elsewhere... find it if you can.
            </div>
        </div>
    </div>
</body>
</html>
