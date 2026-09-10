<?php
require_once 'config/db.php';
require_once 'config/session.php';

// ✅ Prevent caching
noCache();

// ✅ Require login
requireLogin();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current user info
$stmt = $pdo->prepare("SELECT * FROM hunters WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

// Get mission count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM missions WHERE assigned_to = ?");
$stmt->execute([$user_id]);
$mission_count = $stmt->fetchColumn();

// Get total hunters count
$stmt = $pdo->query("SELECT COUNT(*) FROM hunters");
$total_hunters = $stmt->fetchColumn();

// Get active missions count
$stmt = $pdo->query("SELECT COUNT(*) FROM missions WHERE status = 'Active'");
$active_missions = $stmt->fetchColumn();

// Get top secret missions
$stmt = $pdo->query("SELECT COUNT(*) FROM missions WHERE classification = 'Top Secret'");
$top_secret = $stmt->fetchColumn();

// Get rank color
function getRankColor($rank) {
    switch($rank) {
        case 'Legendary': return 'rank-legendary';
        case 'Elite': return 'rank-elite';
        case 'Pro': return 'rank-pro';
        default: return 'rank-novice';
    }
}

// Get status badge
function getStatusBadge($status) {
    switch($status) {
        case 'Active': return 'badge-status active';
        case 'Pending': return 'badge-status pending';
        case 'Completed': return 'badge-status completed';
        default: return 'badge-status failed';
    }
}

// Get classification badge
function getClassificationBadge($class) {
    switch($class) {
        case 'Top Secret': return 'badge-classification top-secret';
        case 'Confidential': return 'badge-classification confidential';
        case 'Hunter-only': return 'badge-classification hunter-only';
        default: return 'badge-classification public';
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
    <title>Hunter Association - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* ============================================================ */
        /* HUNTER × HUNTER DASHBOARD THEME                              */
        /* ============================================================ */
        
        /* --- RESET & BASE --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #040404;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: #e0e0e0;
        }

        /* --- FULL BACKGROUND IMAGE --- */
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

        /* --- DARK OVERLAY GRADIENTS --- */
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

        /* --- FLOATING NEN PARTICLES --- */
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

        /* --- MAIN CONTAINER --- */
        .dashboard-container {
            position: relative;
            z-index: 2;
            padding: 15px 25px 30px;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* --- NAVBAR (Clean - No Brand Text) --- */
        .navbar-custom {
            background: linear-gradient(180deg, rgba(10, 10, 15, 0.95) 0%, rgba(18, 18, 30, 0.9) 100%) !important;
            border-bottom: 2px solid rgba(0, 200, 100, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            padding: 10px 0;
            backdrop-filter: blur(20px);
            border-radius: 12px 12px 0 0;
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

        /* --- HERO BANNER --- */
        .hero-banner {
            position: relative;
            border-radius: 0 0 16px 16px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 180, 100, 0.1);
            border-top: none;
        }

        .hero-banner img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }

        .hero-banner .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(4, 4, 4, 0.6) 0%, rgba(4, 4, 4, 0.1) 40%, rgba(4, 4, 4, 0.1) 60%, rgba(4, 4, 4, 0.6) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .hero-banner .hero-title {
            font-family: 'Archivo Black', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: #00d070;
            text-shadow: 0 0 60px rgba(0, 200, 100, 0.3), 0 4px 20px rgba(0, 0, 0, 0.8);
            letter-spacing: 8px;
            text-transform: uppercase;
            animation: glowPulse 3s ease-in-out infinite;
        }

        .hero-banner .hero-title .accent {
            color: #fff;
            text-shadow: 0 0 40px rgba(255, 255, 255, 0.1);
        }

        .hero-banner .hero-subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: rgba(0, 200, 100, 0.5);
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-top: 6px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
        }

        .hero-banner .hero-subtitle span {
            color: rgba(255, 255, 255, 0.3);
        }

        .hero-banner .hero-license {
            position: absolute;
            bottom: 15px;
            right: 25px;
            background: rgba(0, 200, 100, 0.08);
            border: 1px solid rgba(0, 200, 100, 0.15);
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.7rem;
            color: rgba(0, 200, 100, 0.4);
            letter-spacing: 2px;
            backdrop-filter: blur(10px);
        }

        .hero-banner .hero-license strong {
            color: #00d070;
        }

        .hero-banner .hero-badge {
            position: absolute;
            top: 15px;
            right: 25px;
            background: rgba(0, 200, 100, 0.06);
            border: 1px solid rgba(0, 200, 100, 0.08);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.55rem;
            color: rgba(0, 200, 100, 0.3);
            letter-spacing: 3px;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
        }

        @keyframes glowPulse {
            0%, 100% { text-shadow: 0 0 60px rgba(0, 200, 100, 0.3), 0 4px 20px rgba(0, 0, 0, 0.8); }
            50% { text-shadow: 0 0 80px rgba(0, 200, 100, 0.5), 0 4px 20px rgba(0, 0, 0, 0.8); }
        }

        /* --- RANK BADGE --- */
        .rank-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid;
            transition: all 0.3s ease;
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

        /* --- BUTTONS --- */
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

        .btn-hunter {
            background: linear-gradient(135deg, #00d070, #00a050);
            color: #0a0a0f;
            font-weight: 700;
            border: none;
            padding: 10px 28px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        .btn-hunter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 200, 100, 0.3);
            color: #0a0a0f;
        }

        /* --- SIDEBAR --- */
        .sidebar-card {
            background: rgba(10, 15, 10, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 180, 100, 0.08);
            border-radius: 16px;
            padding: 24px 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .sidebar-card .nav-link {
            color: #8a9a8a;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .sidebar-card .nav-link:hover {
            background: rgba(0, 200, 100, 0.05);
            color: #00d070;
            transform: translateX(4px);
        }

        .sidebar-card .nav-link.active {
            background: rgba(0, 200, 100, 0.08);
            color: #00d070;
            border-left: 3px solid #00d070;
        }

        .sidebar-card .nav-link i {
            margin-right: 12px;
            width: 20px;
        }

        /* --- CARDS (Glassmorphism) --- */
        .card-glass {
            background: rgba(10, 15, 10, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 180, 100, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }

        .card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.5), 0 0 30px rgba(0, 200, 100, 0.03);
            border-color: rgba(0, 200, 100, 0.15);
        }

        .card-glass .card-header {
            background: rgba(0, 200, 100, 0.03);
            border-bottom: 1px solid rgba(0, 180, 100, 0.08);
            padding: 16px 24px;
            font-weight: 700;
            color: #00d070;
            font-size: 1rem;
            letter-spacing: 1px;
        }

        .card-glass .card-body {
            padding: 24px;
        }

        /* --- STAT CARDS --- */
        .stat-card {
            background: rgba(10, 15, 10, 0.5);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 180, 100, 0.06);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(0, 200, 100, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 20px rgba(0, 200, 100, 0.02);
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            color: #00d070;
            margin-bottom: 8px;
            display: block;
        }

        .stat-card .stat-number {
            font-family: 'Archivo Black', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: #fff;
            text-shadow: 0 0 30px rgba(0, 200, 100, 0.05);
        }

        .stat-card .stat-label {
            color: #8a9a8a;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .stat-card .stat-sub {
            font-size: 0.7rem;
            color: #5a6a5a;
            margin-top: 2px;
        }

        /* --- TABLES --- */
        .table-hunter {
            background: transparent;
            color: #d0d0d0;
        }

        .table-hunter thead {
            background: rgba(0, 200, 100, 0.03);
        }

        .table-hunter thead th {
            color: #00d070;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.7rem;
            padding: 14px 12px;
            border-bottom: 2px solid rgba(0, 200, 100, 0.1);
        }

        .table-hunter tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(42, 42, 74, 0.15);
        }

        .table-hunter tbody tr:hover {
            background: rgba(0, 200, 100, 0.03);
        }

        .table-hunter tbody td {
            padding: 12px 12px;
            vertical-align: middle;
            color: #c0c0c0;
        }

        /* --- BADGES --- */
        .badge-status {
            padding: 4px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-status.active {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .badge-status.pending {
            background: rgba(241, 196, 15, 0.15);
            color: #f1c40f;
            border: 1px solid rgba(241, 196, 15, 0.2);
        }

        .badge-status.completed {
            background: rgba(52, 152, 219, 0.15);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .badge-status.failed {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        .badge-classification {
            padding: 4px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.65rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-classification.top-secret {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.2);
            animation: pulseRed 2s infinite;
        }

        .badge-classification.confidential {
            background: rgba(241, 196, 15, 0.15);
            color: #f1c40f;
            border: 1px solid rgba(241, 196, 15, 0.2);
        }

        .badge-classification.hunter-only {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .badge-classification.public {
            background: rgba(149, 165, 166, 0.15);
            color: #95a5a6;
            border: 1px solid rgba(149, 165, 166, 0.2);
        }

        @keyframes pulseRed {
            0%, 100% { box-shadow: 0 0 10px rgba(231, 76, 60, 0.1); }
            50% { box-shadow: 0 0 25px rgba(231, 76, 60, 0.2); }
        }

        /* --- SCROLLBAR --- */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 200, 100, 0.15);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 200, 100, 0.25);
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .dashboard-container {
                padding: 12px 15px 20px;
            }
            .stat-card .stat-number {
                font-size: 1.8rem;
            }
            .hero-banner .hero-title {
                font-size: 2.5rem;
                letter-spacing: 5px;
            }
            .hero-banner img {
                height: 180px;
            }
            .hero-banner .hero-subtitle {
                font-size: 0.8rem;
                letter-spacing: 4px;
            }
        }

        @media (max-width: 768px) {
            .navbar-custom .navbar-brand {
                font-size: 0.9rem;
            }
            .sidebar-card {
                margin-bottom: 20px;
            }
            .hero-banner .hero-title {
                font-size: 1.8rem;
                letter-spacing: 4px;
            }
            .hero-banner .hero-subtitle {
                font-size: 0.6rem;
                letter-spacing: 3px;
            }
            .hero-banner img {
                height: 140px;
            }
            .hero-banner .hero-license {
                display: none;
            }
            .hero-banner .hero-badge {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                padding: 8px 10px 15px;
            }
            .stat-card {
                padding: 16px;
            }
            .stat-card .stat-number {
                font-size: 1.5rem;
            }
            .card-glass .card-body {
                padding: 16px;
            }
            .hero-banner .hero-title {
                font-size: 1.3rem;
                letter-spacing: 3px;
            }
            .hero-banner img {
                height: 100px;
            }
            .hero-banner .hero-subtitle {
                font-size: 0.5rem;
                letter-spacing: 2px;
            }
        }
    </style>
</head>
<body>

    <!-- ===== FLOATING NEN PARTICLES ===== -->
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

    <!-- ===== DASHBOARD CONTAINER ===== -->
    <div class="dashboard-container">

        <!-- ===== NAVBAR ===== -->
        <nav class="navbar navbar-custom navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <i class="bi bi-shield-fill-check me-2"></i>
                    <span style="opacity: 0.4;">HUNTER</span>
                </a>
                
                <div class="navbar-nav ms-auto align-items-center">
                    <div class="d-flex align-items-center me-3">
                        <span class="rank-badge <?= getRankColor($current_user['hunter_rank']) ?> me-2">
                            <i class="bi bi-award-fill me-1"></i>
                            <?= $current_user['hunter_rank'] ?>
                        </span>
                        <span class="text-light" style="font-weight: 600; font-size: 0.9rem;">
                            <i class="bi bi-person-fill me-1" style="color: #00d070;"></i>
                            <?= htmlspecialchars($current_user['username']) ?>
                        </span>
                    </div>
                    <a href="logout.php" class="btn btn-hunter-outline btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Exit
                    </a>
                </div>
            </div>
        </nav>

        <!-- ===== HERO BANNER ===== -->
        <div class="hero-banner">
            <img src="assets/img/chars3.jpeg" alt="Hunter Association">
            <div class="hero-overlay">
                <div class="hero-title">
                    HUNTER <span class="accent">✧</span> ASSOCIATION
                </div>
                <div class="hero-subtitle">
                    Dark Continent Sector <span>✦</span> Secure Operations
                </div>
            </div>
            <div class="hero-badge">
                <i class="bi bi-shield-fill-check me-1"></i> Elite Access
            </div>
            <div class="hero-license">
                <i class="bi bi-award-fill me-1" style="color: #00d070;"></i>
                License: <strong><?= htmlspecialchars($current_user['hunter_license']) ?></strong>
            </div>
        </div>

        <div class="row">
            <!-- ===== SIDEBAR ===== -->
            <div class="col-md-3 col-lg-2 mb-4">
                <div class="sidebar-card">
                    <h6 class="text-warning mb-3" style="font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-size: 0.7rem; color: #00d070;">
                        <i class="bi bi-compass"></i> Navigation
                    </h6>
                    <hr style="border-color: rgba(0, 200, 100, 0.08);">
                    
                    <div class="nav flex-column">
                        <a href="index.php" class="nav-link active">
                            <i class="bi bi-house-door-fill"></i> Dashboard
                        </a>
                        <a href="profile.php" class="nav-link">
                            <i class="bi bi-person-fill"></i> My Profile
                        </a>
                        <a href="#" class="nav-link" onclick="alert('🚧 Under Construction - Phantom Troupe intercepted this page!')">
                            <i class="bi bi-clipboard-fill"></i> Missions
                            <span class="badge bg-danger float-end" style="font-size: 0.5rem; background: rgba(231, 76, 60, 0.3) !important; color: #e74c3c !important;">NEW</span>
                        </a>
                    </div>
                    
                    <hr style="border-color: rgba(0, 200, 100, 0.08);">
                    
                    <div class="mt-2" style="font-size: 0.7rem; color: #5a6a5a;">
                        <i class="bi bi-info-circle"></i>
                        <span style="color: #6a7a6a;">Clearance: Level <?= $current_user['clearance_level'] ?></span>
                        <br>
                        <span style="color: #6a7a6a;">License: <?= htmlspecialchars($current_user['hunter_license']) ?></span>
                    </div>
                </div>
            </div>

            <!-- ===== MAIN CONTENT ===== -->
            <div class="col-md-9 col-lg-10">
                
                <!-- ===== WELCOME BANNER ===== -->
                <div class="card-glass mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 style="font-weight: 700; color: #fff; font-size: 1.8rem;">
                                    <i class="bi bi-hand-thumbs-up-fill" style="color: #00d070;"></i>
                                    Welcome back, <span style="color: #00d070;"><?= htmlspecialchars($current_user['username']) ?></span>
                                </h2>
                                <p style="color: #7a8a7a; margin-top: 8px;">
                                    <i class="bi bi-flag-fill" style="color: #00d070;"></i>
                                    Your Hunter License is active. <strong style="color: #00d070;"><?= $mission_count ?></strong> missions assigned.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div style="background: rgba(0, 200, 100, 0.05); border: 1px solid rgba(0, 200, 100, 0.1); border-radius: 10px; padding: 10px 16px; display: inline-block;">
                                    <small style="color: #5a6a5a; display: block; font-size: 0.6rem; letter-spacing: 2px; text-transform: uppercase;">Status</small>
                                    <span style="color: #00d070; font-weight: 700;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== STAT CARDS ===== -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <span class="stat-icon"><i class="bi bi-people-fill"></i></span>
                            <div class="stat-number"><?= $total_hunters ?></div>
                            <div class="stat-label">Total Hunters</div>
                            <div class="stat-sub">Active Members</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <span class="stat-icon"><i class="bi bi-mission"></i></span>
                            <div class="stat-number"><?= $active_missions ?></div>
                            <div class="stat-label">Active Missions</div>
                            <div class="stat-sub">In Progress</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <span class="stat-icon"><i class="bi bi-shield-lock-fill"></i></span>
                            <div class="stat-number"><?= $top_secret ?></div>
                            <div class="stat-label">Top Secret</div>
                            <div class="stat-sub">Classification</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <span class="stat-icon"><i class="bi bi-stars"></i></span>
                            <div class="stat-number" style="font-size: 1.5rem; padding-top: 8px;">
                                <span class="rank-badge <?= getRankColor($current_user['hunter_rank']) ?>" style="font-size: 0.9rem;">
                                    <?= $current_user['hunter_rank'] ?>
                                </span>
                            </div>
                            <div class="stat-label" style="margin-top: 4px;">Your Rank</div>
                            <div class="stat-sub">Clearance Level <?= $current_user['clearance_level'] ?></div>
                        </div>
                    </div>
                </div>

                <!-- ===== MISSIONS TABLE ===== -->
                <div class="card-glass">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-table me-2"></i> Confidential Mission Reports
                            </span>
                            <span>
                                <span class="badge" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.15); font-size: 0.65rem;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> CLASSIFIED
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hunter">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-hash"></i> ID</th>
                                        <th><i class="bi bi-file-text"></i> Mission Title</th>
                                        <th><i class="bi bi-person"></i> Assigned Hunter</th>
                                        <th><i class="bi bi-clock"></i> Status</th>
                                        <th><i class="bi bi-shield"></i> Classification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 🔥 VULNERABILITY: No access control, shows all missions
                                    $stmt = $pdo->query("SELECT m.*, h.username FROM missions m LEFT JOIN hunters h ON m.assigned_to = h.id ORDER BY m.id DESC");
                                    $missions = $stmt->fetchAll();
                                    foreach ($missions as $mission):
                                    ?>
                                    <tr>
                                        <td><strong style="color: #00d070;">#<?= $mission['id'] ?></strong></td>
                                        <td>
                                            <?php 
                                            $icon = $mission['classification'] === 'Top Secret' ? 'bi-shield-lock-fill' : ($mission['classification'] === 'Confidential' ? 'bi-file-earmark-lock' : 'bi-file-text');
                                            ?>
                                            <i class="bi <?= $icon ?>" style="color: #00d070; margin-right: 6px;"></i>
                                            <?= htmlspecialchars($mission['title']) ?>
                                        </td>
                                        <td>
                                            <?php if ($mission['username']): ?>
                                                <i class="bi bi-person-circle" style="color: #6a7a6a;"></i>
                                                <?= htmlspecialchars($mission['username']) ?>
                                            <?php else: ?>
                                                <span style="color: #4a5a4a;">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-status <?= getStatusBadge($mission['status']) ?>">
                                                <?= $mission['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-classification <?= getClassificationBadge($mission['classification']) ?>">
                                                <?php if ($mission['classification'] === 'Top Secret'): ?>
                                                    <i class="bi bi-shield-lock-fill"></i>
                                                <?php elseif ($mission['classification'] === 'Confidential'): ?>
                                                    <i class="bi bi-lock-fill"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-eye"></i>
                                                <?php endif; ?>
                                                <?= $mission['classification'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3" style="background: rgba(231, 76, 60, 0.03); border: 1px solid rgba(231, 76, 60, 0.06); border-radius: 10px; padding: 10px 16px;">
                            <small style="color: #6a7a6a;">
                                <i class="bi bi-shield-slash me-1" style="color: #e74c3c;"></i>
                                <strong style="color: #e74c3c;">Security Notice:</strong> 
                                All mission data is visible to all authenticated Hunters. 
                                <span style="color: #4a5a4a;">(Authorization check bypassed)</span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- ===== FOOTER ===== -->
                <div class="mt-4 text-center" style="color: #2a3a2a; font-size: 0.7rem; border-top: 1px solid rgba(42, 42, 74, 0.15); padding-top: 20px;">
                    <i class="bi bi-shield-fill" style="color: #00d070;"></i>
                    Hunter Association · Secure Network · v3.7.2
                    <span style="color: #2a3a2a; margin: 0 10px;">|</span>
                    <span style="color: #2a3a2a;"><?= date('Y') ?> · All Rights Reserved</span>
                    <br>
                    <span style="color: #1a2a1a;">
                        <i class="bi bi-lock-fill me-1"></i> 
                        <span style="color: #2a3a2a;">This system is monitored by the Hunter Association Security Division</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
