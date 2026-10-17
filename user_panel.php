<?php
require_once 'config/db.php';
require_once 'config/session.php';

// ✅ Prevent caching
noCache();

// ✅ Require login
requireLogin();

// 🔥 REAL VULNERABILITY: ID parameter in URL - visible IDOR!
// Example: http://localhost:3001/user_panel.php?id=13
$id_param = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get all hunters for dropdown - ONLY show decoy users
$stmt = $pdo->query("SELECT id, username FROM hunters WHERE 
    username IN ('admin_system', 'admin_network', 'admin_phantom', 'admin_troupe')
    ORDER BY username");
$hunters = $stmt->fetchAll();

// Get selected hunter based on ID parameter
$selected_hunter = null;

if ($id_param > 0) {
    $stmt = $pdo->prepare("SELECT * FROM hunters WHERE id = ?");
    $stmt->execute([$id_param]);
    $selected_hunter = $stmt->fetch();
}

function getRankColor($rank) {
    switch($rank) {
        case 'Legendary': return 'rank-legendary';
        case 'Elite': return 'rank-elite';
        case 'Pro': return 'rank-pro';
        default: return 'rank-novice';
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
    <title>User Records</title>
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
        .panel-container {
            position: relative;
            z-index: 2;
            padding: 20px 25px 30px;
            max-width: 800px;
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
        .panel-body { padding: 30px 35px 35px; }
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
        .form-select-glass {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 180, 100, 0.08);
            border-radius: 10px;
            color: #d0f0d0;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .form-select-glass:focus {
            background: rgba(0, 0, 0, 0.6);
            border-color: #00d070;
            box-shadow: 0 0 20px rgba(0, 200, 100, 0.05);
            color: #fff;
        }
        .form-select-glass option {
            background: #1a1a2e;
            color: #d0f0d0;
        }
        .flag-container {
            background: rgba(46, 204, 113, 0.05);
            border: 2px solid rgba(46, 204, 113, 0.15);
            border-radius: 12px;
            padding: 16px 20px;
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }
        .flag-container:hover {
            border-color: rgba(46, 204, 113, 0.3);
            box-shadow: 0 0 30px rgba(46, 204, 113, 0.05);
        }
        .flag-container .flag-label {
            color: #2ecc71;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .flag-container .flag-value {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #fff;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 16px;
            border-radius: 8px;
            margin-top: 6px;
            border: 1px solid rgba(46, 204, 113, 0.1);
            word-break: break-all;
        }
        .fake-flag {
            background: rgba(231, 76, 60, 0.05);
            border: 2px solid rgba(231, 76, 60, 0.15);
            border-radius: 12px;
            padding: 16px 20px;
            animation: fadeIn 0.5s ease-out;
        }
        .fake-flag .flag-label {
            color: #e74c3c;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .fake-flag .flag-value {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #e74c3c;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 16px;
            border-radius: 8px;
            margin-top: 6px;
            border: 1px solid rgba(231, 76, 60, 0.1);
            word-break: break-all;
            text-decoration: line-through;
            opacity: 0.7;
        }
        .troll-flag {
            background: rgba(241, 196, 15, 0.05);
            border: 2px solid rgba(241, 196, 15, 0.15);
            border-radius: 12px;
            padding: 16px 20px;
            animation: fadeIn 0.5s ease-out;
        }
        .troll-flag .flag-label {
            color: #f1c40f;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .troll-flag .flag-value {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #f1c40f;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 16px;
            border-radius: 8px;
            margin-top: 6px;
            border: 1px solid rgba(241, 196, 15, 0.1);
            word-break: break-all;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0, 200, 100, 0.15); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0, 200, 100, 0.25); }
        @media (max-width: 768px) {
            .panel-container { padding: 12px 15px 20px; }
            .panel-body { padding: 20px; }
        }
        @media (max-width: 480px) {
            .panel-container { padding: 8px 10px 15px; }
            .panel-body { padding: 16px; }
            .flag-container .flag-value { font-size: 0.85rem; padding: 6px 12px; }
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

    <div class="panel-container">
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

            <div class="panel-body">
                <div class="section-title">
                    <i class="bi bi-people-fill me-2"></i> User Records
                    <span class="badge" style="background: rgba(0, 200, 100, 0.08); color: rgba(0, 200, 100, 0.3); border: 1px solid rgba(0, 200, 100, 0.06); font-size: 0.5rem; letter-spacing: 2px; margin-left: 10px;">
                        <i class="bi bi-shield-check me-1"></i> RESTRICTED
                    </span>
                </div>

                <?php if ($id_param > 0 && isset($selected_hunter)): ?>
                    <div style="background: rgba(0, 200, 100, 0.03); border: 1px solid rgba(0, 200, 100, 0.06); border-radius: 10px; padding: 8px 16px; margin-bottom: 16px; text-align: center;">
                        <small style="color: #4a5a4a;">
                            <i class="bi bi-link-45deg me-1"></i> 
                            Viewing: <strong style="color: #00d070;"><?= htmlspecialchars($selected_hunter['username']) ?></strong> 
                            (ID: <?= $id_param ?>)
                        </small>
                    </div>
                <?php endif; ?>

                <form method="GET" class="mb-4" action="">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label" style="color: #6a7a6a; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">
                                <i class="bi bi-person"></i> Select User
                            </label>
                            <select name="id" class="form-select form-select-glass" required>
                                <option value="">— Choose a User —</option>
                                <?php foreach ($hunters as $hunter): ?>
                                    <option value="<?= $hunter['id'] ?>" <?= ($id_param == $hunter['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hunter['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-hunter w-100">
                                <i class="bi bi-search me-2"></i> View Records
                            </button>
                        </div>
                    </div>
                </form>

                <?php if ($id_param > 0 && isset($selected_hunter)): ?>
                    <div style="border-top: 1px solid rgba(0, 180, 100, 0.08); padding-top: 20px;">
                        <div style="background: rgba(10, 10, 15, 0.3); border-radius: 10px; padding: 16px; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="font-size: 2.5rem; color: #00d070;">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #fff; font-size: 1.1rem;">
                                        <?= htmlspecialchars($selected_hunter['username']) ?>
                                    </div>
                                    <div style="color: #6a7a6a; font-size: 0.8rem;">
                                        ID: <?= $selected_hunter['id'] ?> • Rank: <?= $selected_hunter['hunter_rank'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($id_param > 0 && isset($selected_hunter)): ?>
                    <?php 
                    $flag_value = isset($selected_hunter['secret_flag']) ? $selected_hunter['secret_flag'] : '';

                    // Target Hunter: Gon Freecss (ID 13) - Fetch flag from flag.php vault
                    if ((int)$selected_hunter['id'] === 13 || $selected_hunter['username'] === 'gon_freecss') {
                        if (!defined('INTERNAL_FLAG_ACCESS')) {
                            define('INTERNAL_FLAG_ACCESS', true);
                        }
                        require_once __DIR__ . '/flag.php';
                        if (function_exists('get_classified_flag')) {
                            $flag_value = get_classified_flag();
                        }
                    }

                    $is_fake = ($flag_value === 'SSP{FAKE_FLAG_1337}' || $flag_value === 'SSP{FAKE_FLAG_9999}');
                    $is_troll = ($flag_value === 'SSP{PRANKED_YOU_LOL}' || $flag_value === 'SSP{KEEP_TRYING_HUNTER}');
                    $has_flag = !empty($flag_value) && !$is_fake && !$is_troll;
                    ?>
                    
                    <?php if ($has_flag): ?>
                        <div class="flag-container">
                            <div class="d-flex align-items-start">
                                <div class="me-3" style="font-size: 2rem; color: #2ecc71;">
                                    <i class="bi bi-flag-fill"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="flag-label">
                                        <i class="bi bi-shield-check me-1"></i> 
                                        🏴 CLASSIFIED INTELLIGENCE DISCOVERED
                                    </div>
                                    <div class="flag-value">
                                        <?= htmlspecialchars($flag_value) ?>
                                    </div>
                                    <small style="color: #4a5a4a; font-size: 0.6rem; display: block; margin-top: 4px;">
                                        <i class="bi bi-info-circle me-1"></i> 
                                        Retrieved from <?= htmlspecialchars($selected_hunter['username']) ?>'s records
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($is_troll): ?>
                        <div class="troll-flag">
                            <div class="d-flex align-items-start">
                                <div class="me-3" style="font-size: 2rem; color: #f1c40f;">
                                    <i class="bi bi-emoji-laughing-fill"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="flag-label">
                                        <i class="bi bi-emoji-smile me-1"></i> 
                                        😂 PRANKED! 
                                    </div>
                                    <div class="flag-value">
                                        <?= htmlspecialchars($flag_value) ?>
                                    </div>
                                    <small style="color: #5a5a3a; font-size: 0.6rem; display: block; margin-top: 4px;">
                                        <i class="bi bi-emoji-wink me-1"></i> 
                                        Nice try, Hunter! Keep searching...
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($is_fake): ?>
                        <div class="fake-flag">
                            <div class="d-flex align-items-start">
                                <div class="me-3" style="font-size: 2rem; color: #e74c3c;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="flag-label">
                                        <i class="bi bi-shield-slash me-1"></i> 
                                        DECOY INTELLIGENCE DETECTED
                                    </div>
                                    <div class="flag-value">
                                        <?= htmlspecialchars($flag_value) ?>
                                    </div>
                                    <small style="color: #4a5a4a; font-size: 0.6rem; display: block; margin-top: 4px;">
                                        <i class="bi bi-info-circle me-1"></i> 
                                        This appears to be a decoy - not the real flag
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center" style="background: rgba(10, 10, 15, 0.3); border-radius: 10px; padding: 16px; border: 1px dashed rgba(42, 42, 74, 0.2);">
                            <i class="bi bi-lock" style="font-size: 2rem; color: #3a4a3a; display: block; margin-bottom: 8px;"></i>
                            <small style="color: #3a4a3a;">
                                No classified intelligence available for this user
                            </small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($id_param == 0): ?>
                        <div class="text-center" style="background: rgba(10, 10, 15, 0.3); border-radius: 10px; padding: 20px; border: 1px dashed rgba(42, 42, 74, 0.2);">
                            <i class="bi bi-shield-slash" style="font-size: 2rem; color: #3a4a3a; display: block; margin-bottom: 8px;"></i>
                            <small style="color: #3a4a3a;">
                                Select a user from the dropdown above to view their records
                            </small>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

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
