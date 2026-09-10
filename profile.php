<?php
require_once 'config/db.php';
require_once 'config/session.php';

// ✅ Prevent caching
noCache();

// ✅ Require login
requireLogin();

// Only show the logged-in user's profile (NO URL PARAMETERS)
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM hunters WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

if (!$current_user) {
    die("User not found");
}

function getRankColor($rank) {
    switch($rank) {
        case 'Legendary': return 'rank-legendary';
        case 'Elite': return 'rank-elite';
        case 'Pro': return 'rank-pro';
        default: return 'rank-novice';
    }
}

function getRankIcon($rank) {
    switch($rank) {
        case 'Legendary': return 'bi bi-crown-fill';
        case 'Elite': return 'bi bi-star-fill';
        case 'Pro': return 'bi bi-shield-fill';
        default: return 'bi bi-person-fill';
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
    <title>My Profile</title>
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
        .profile-container {
            position: relative;
            z-index: 2;
            padding: 20px 25px 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .seamless-card {
            background: rgba(10, 15, 10, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 180, 100, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .seamless-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.5), 0 0 30px rgba(0, 200, 100, 0.03);
            border-color: rgba(0, 200, 100, 0.15);
        }
        .navbar-custom {
            background: rgba(0, 200, 100, 0.03) !important;
            border-bottom: 1px solid rgba(0, 180, 100, 0.08) !important;
            padding: 10px 20px !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .navbar-custom .navbar-brand {
            font-family: 'Archivo Black', sans-serif;
            font-weight: 900;
            font-size: 1.1rem;
            color: rgba(0, 200, 100, 0.4) !important;
            letter-spacing: 3px;
            text-transform: uppercase;
            opacity: 0.5;
        }
        .navbar-custom .navbar-brand i {
            color: rgba(0, 200, 100, 0.3);
            font-size: 1.2rem;
        }
        .btn-hunter-outline {
            background: transparent;
            color: #00d070;
            border: 1px solid rgba(0, 200, 100, 0.3);
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }
        .btn-hunter-outline:hover {
            background: rgba(0, 200, 100, 0.1);
            color: #00ff80;
            border-color: #00d070;
            transform: translateY(-1px);
            box-shadow: 0 0 20px rgba(0, 200, 100, 0.1);
        }
        .rank-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .rank-legendary {
            background: linear-gradient(135deg, #f1c40f, #f39c12);
            color: #1a1a2e;
            border-color: #f1c40f;
            box-shadow: 0 0 30px rgba(241, 196, 15, 0.2);
            animation: pulseGold 2s infinite;
        }
        .rank-elite {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            border-color: #3498db;
            box-shadow: 0 0 20px rgba(52, 152, 219, 0.15);
        }
        .rank-pro {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
            border-color: #2ecc71;
        }
        .rank-novice {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: #fff;
            border-color: #95a5a6;
        }
        @keyframes pulseGold {
            0%, 100% { box-shadow: 0 0 30px rgba(241, 196, 15, 0.2); }
            50% { box-shadow: 0 0 50px rgba(241, 196, 15, 0.4); }
        }
        .profile-body { padding: 30px 35px 35px; }
        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 3px solid #00d070;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #00d070;
            background: linear-gradient(135deg, rgba(0, 200, 100, 0.1), rgba(10, 10, 15, 0.9));
            margin: 0 auto 16px;
            box-shadow: 0 0 40px rgba(0, 200, 100, 0.1);
            transition: all 0.3s ease;
        }
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 0 60px rgba(0, 200, 100, 0.2);
        }
        .detail-item {
            background: rgba(10, 10, 15, 0.4);
            border: 1px solid rgba(0, 180, 100, 0.05);
            border-radius: 10px;
            padding: 14px 18px;
            transition: all 0.3s ease;
        }
        .detail-item:hover {
            border-color: rgba(0, 200, 100, 0.15);
            background: rgba(10, 10, 15, 0.6);
        }
        .detail-item .detail-label {
            color: #6a7a6a;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }
        .detail-item .detail-value {
            color: #d0d0d0;
            font-size: 1rem;
            font-weight: 600;
        }
        .detail-item .detail-value i { color: #00d070; margin-right: 6px; }
        .license-plate {
            background: linear-gradient(135deg, #00d070, #00a050);
            color: #0a0a0f;
            padding: 6px 18px;
            border-radius: 6px;
            font-family: 'Archivo Black', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 2px;
            display: inline-block;
            box-shadow: 0 0 30px rgba(0, 200, 100, 0.1);
        }
        .section-title {
            font-family: 'Archivo Black', sans-serif;
            font-size: 1.1rem;
            color: rgba(0, 200, 100, 0.3);
            text-align: center;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 180, 100, 0.05);
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0, 200, 100, 0.15); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0, 200, 100, 0.25); }
        @media (max-width: 992px) {
            .profile-container { padding: 12px 15px 20px; }
            .profile-avatar { width: 110px; height: 110px; font-size: 3rem; }
            .profile-body { padding: 20px 20px 25px; }
        }
        @media (max-width: 768px) {
            .navbar-custom .navbar-brand { font-size: 0.9rem; }
            .profile-avatar { width: 90px; height: 90px; font-size: 2.5rem; }
            .profile-body { padding: 16px 16px 20px; }
        }
        @media (max-width: 480px) {
            .profile-container { padding: 8px 10px 15px; }
            .profile-avatar { width: 70px; height: 70px; font-size: 2rem; }
            .detail-item { padding: 10px 12px; }
            .detail-item .detail-value { font-size: 0.85rem; }
            .profile-body { padding: 12px 12px 16px; }
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

    <div class="profile-container">
        <div class="seamless-card">
            <nav class="navbar navbar-custom navbar-expand-lg">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php">
                        <i class="bi bi-shield-fill-check me-2"></i>
                        <span style="opacity: 0.4;">HUNTER</span>
                    </a>
                    <div class="navbar-nav ms-auto align-items-center">
                        <a href="index.php" class="btn btn-hunter-outline btn-sm">
                            <i class="bi bi-arrow-left"></i> Dashboard
                        </a>
                    </div>
                </div>
            </nav>

            <div class="profile-body">
                <div class="section-title">
                    <i class="bi bi-person-badge me-2"></i> My Profile
                    <span class="badge" style="background: rgba(0, 200, 100, 0.08); color: rgba(0, 200, 100, 0.3); border: 1px solid rgba(0, 200, 100, 0.06); font-size: 0.5rem; letter-spacing: 2px; margin-left: 10px;">
                        <i class="bi bi-shield-check me-1"></i> VERIFIED
                    </span>
                </div>

                <div class="text-center mb-4">
                    <div class="profile-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 style="font-weight: 700; color: #fff; font-size: 1.8rem; margin-bottom: 4px;">
                        <?= htmlspecialchars($current_user['username']) ?>
                    </h3>
                    <span class="rank-badge <?= getRankColor($current_user['hunter_rank']) ?>">
                        <i class="<?= getRankIcon($current_user['hunter_rank']) ?> me-1"></i>
                        <?= $current_user['hunter_rank'] ?>
                    </span>
                    <div class="mt-2">
                        <span class="license-plate">
                            <i class="bi bi-award-fill me-1"></i>
                            <?= htmlspecialchars($current_user['hunter_license']) ?>
                        </span>
                    </div>
                </div>

                <hr style="border-color: rgba(0, 180, 100, 0.08);">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-envelope"></i> Email</span>
                            <span class="detail-value">
                                <i class="bi bi-envelope-fill"></i>
                                <?= htmlspecialchars($current_user['email']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-shield-lock"></i> Clearance</span>
                            <span class="detail-value">
                                <i class="bi bi-shield-fill-check"></i>
                                Level <?= $current_user['clearance_level'] ?>
                                <?php if ($current_user['clearance_level'] >= 4): ?>
                                    <span class="badge" style="background: rgba(0, 200, 100, 0.1); color: #00d070; border: 1px solid rgba(0, 200, 100, 0.15); font-size: 0.5rem; margin-left: 6px;">
                                        <i class="bi bi-star-fill me-1"></i> ELITE
                                    </span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-mission"></i> Assigned Missions</span>
                            <span class="detail-value">
                                <i class="bi bi-clipboard-fill"></i>
                                <?= htmlspecialchars($current_user['assigned_missions'] ?? 'No missions assigned') ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="detail-item">
                            <span class="detail-label"><i class="bi bi-calendar"></i> Joined</span>
                            <span class="detail-value">
                                <i class="bi bi-calendar-event"></i>
                                <?= date('F d, Y \a\t g:i A', strtotime($current_user['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-center" style="color: #2a3a2a; font-size: 0.7rem;">
                    <i class="bi bi-hash"></i> Hunter ID: <?= $current_user['id'] ?>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center" style="color: #2a3a2a; font-size: 0.7rem; border-top: 1px solid rgba(42, 42, 74, 0.15); padding-top: 20px;">
            <i class="bi bi-shield-fill" style="color: #00d070;"></i>
            Hunter Association · Secure Network · v3.7.2
            <span style="color: #2a3a2a; margin: 0 10px;">|</span>
            <span style="color: #2a3a2a;"><?= date('Y') ?> · All Rights Reserved</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
