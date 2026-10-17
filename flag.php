<?php
// Prevent direct web / browser access
$is_direct_access = (
    basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'flag.php' ||
    basename($_SERVER['PHP_SELF'] ?? '') === 'flag.php' ||
    (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'flag.php') !== false) ||
    !defined('INTERNAL_FLAG_ACCESS')
);

if ($is_direct_access) {
    http_response_code(403);
    header('HTTP/1.1 403 Forbidden');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Hunter Intelligence Vault</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #040404;
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, rgba(231, 76, 60, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .error-card {
            background: rgba(12, 12, 18, 0.9);
            border: 1px solid rgba(231, 76, 60, 0.3);
            border-radius: 16px;
            padding: 40px;
            max-width: 520px;
            text-align: center;
            box-shadow: 0 0 50px rgba(231, 76, 60, 0.15);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }
        .error-code {
            font-family: 'Archivo Black', sans-serif;
            font-size: 4rem;
            color: #e74c3c;
            line-height: 1;
            margin-bottom: 12px;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(231, 76, 60, 0.4);
        }
        .error-title {
            font-weight: 700;
            color: #fff;
            font-size: 1.25rem;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .error-desc {
            color: #8a9a8a;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .code-pill {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(231, 76, 60, 0.12);
            color: #ff6b6b;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid rgba(231, 76, 60, 0.2);
            font-size: 0.85rem;
        }
        .status-badge {
            display: inline-block;
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .footer-note {
            font-size: 0.7rem;
            color: #4a5a4a;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 16px;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="status-badge">
            <i class="bi bi-shield-lock-fill me-1"></i> Access Restricted
        </div>
        <div class="error-code">403</div>
        <div class="error-title">Classified Intelligence Vault</div>
        <p class="error-desc">
            Direct web access to <span class="code-pill">flag.php</span> is strictly forbidden by the Hunter Association Executive Committee.
            Dossier assets must be retrieved through authorized internal personnel channels.
        </p>
        <div class="footer-note">
            Hunter Association Security Core · Incident Ref #403-IDOR · Clearance Level 5 Required
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// Function to safely provide the flag when included internally
function get_classified_flag() {
    return 'SSP{WEB_IDOR_2A91FC7D}';
}
