# HXH-C7 — Hunter × Hunter CTF Web Challenge

An anime-themed web exploitation CTF challenge featuring the **Hunter Association Internal Records & Operations Portal**.

---

## 🎯 Challenge Synopsis
- **Title**: HXH-C7: Hunter Association Internal Records Portal
- **Category**: Web Exploitation / Insecure Direct Object Reference (IDOR) & Reconnaissance
- **Difficulty**: Medium
- **Flag**: `SSP{WEB_IDOR_2A91FC7D}`
- **Theme**: Hunter × Hunter Anime (Emerald Nen Glow, Dark Anime Cyber-Terminal, Chairman Netero & Gon Freecss)
- **Port**: `3001` (Web) / `3307` (MariaDB)

---

## 🚀 Quick Start & Deployment

### Docker Compose (Recommended)
Launch the multi-container environment with Apache PHP 8.2 and MariaDB 10.6:

```bash
docker-compose up -d --build
```

Access the challenge at:
👉 **`http://localhost:3001`** (or `http://<SERVER_IP>:3001`)

To stop the containers:
```bash
docker-compose down -v
```

---

## 🎮 Challenge Workflow & Stages

### 1. Reconnaissance & Discovery
* **Robots.txt Analysis**: Inspecting `/robots.txt` reveals hidden disallow paths such as `/admin_panel.php`, `/user_panel.php`, `/credentials.php`, `/db_dump.php`, and `/hidden_dir.php`.
* **Credential Enumeration**: Exploring leaks or source comments (`login.php` / `creds.txt`) allows authentication into the Hunter Portal.

### 2. Portal Access & Decoy Traps
* Logging in grants access to the **Hunter Records Management Hub**.
* Interacting with standard administrative profiles (`admin_system`, `admin_network`, `admin_phantom`, `admin_troupe`) yields amusing decoy flags (e.g., `SSP{PRANKED_YOU_LOL}`, `SSP{KEEP_TRYING_HUNTER}`).

### 3. IDOR Exploitation (`user_panel.php?id=<ID>`)
* The dossier viewing endpoint `/user_panel.php?id=1` uses raw integer identifiers without enforcing horizontal authorization checks:
  ```php
  $id_param = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  $stmt = $pdo->prepare("SELECT * FROM hunters WHERE id = ?");
  $stmt->execute([$id_param]);
  ```
* Fuzzing candidate Hunter IDs from `1` to `18` allows reading confidential dossiers of all registered Hunters.

### 4. Flag Capture
* Navigating to **`http://localhost:3001/user_panel.php?id=13`** loads the profile of **Gon Freecss** (`HUNTER-LIC-0013`).
* His confidential classification record exposes the true flag:
  `SSP{WEB_IDOR_2A91FC7D}`

---

## 📁 Directory Structure

```
HXH-C7/
├── assets/
│   ├── css/
│   │   └── style.css            # Dark cyber-anime Hunter UI styles
│   └── img/
│       ├── chars.png            # Character artwork
│       ├── chars2.png           # Background artwork
│       ├── chars3.jpeg          # Nen aura graphic
│       └── logo1.png            # Hunter Association insignia
├── config/
│   ├── db.php                   # PDO Database connection handler
│   └── session.php              # Session lifecycle & auth middleware
├── docker/
│   └── db-init/
│       └── init.sql             # MariaDB schema, hunter records & secret flags
├── Dockerfile                   # PHP 8.2 Apache container configuration
├── docker-compose.yml           # Web + MariaDB multi-container orchestration
├── index.php                    # Portal dashboard entrypoint
├── login.php                    # Hunter authentication terminal
├── logout.php                   # Session termination
├── user_panel.php               # Target IDOR dossier inspector (?id=13)
├── admin_panel.php              # Admin records interface
├── profile.php                  # Hunter candidate profile view
├── robots.txt                   # Search crawler directives & path hints
├── README.md                    # Challenge documentation
└── .env                         # Environment variables
```

---

## 🛡️ Remediation
To fix the IDOR vulnerability in production systems:
1. **Enforce Authorization Checks**: Ensure `$_SESSION['user_id'] === $id_param` or verify that the authenticated user possesses administrative clearance before returning records.
2. **Use Indirect Object References**: Use cryptographically secure session-bound tokens or UUIDs instead of sequential numerical IDs.

---

## Walkthrough
The challenge flow is documented in the files present in this repository (see `WALKTHROUGH` files and source code).
