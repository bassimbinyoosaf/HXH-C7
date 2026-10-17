# HXH-C7 — Hunter × Hunter CTF Web Challenge

Hunter Association Internal Records & Operations Portal CTF challenge.

## Challenge Synopsis
- Title: HXH-C7: Hunter Association Internal Records Portal
- Category: Web Exploitation / Insecure Direct Object Reference (IDOR)
- Difficulty: Medium
- Flag: `SSP{WEB_IDOR_2A91FC7D}`
- Theme: Hunter × Hunter
- Port: 30007

## Quick Start

### Docker Compose (Recommended)
```bash
docker-compose up -d --build
```
Access: `http://localhost:30007`

Stop: `docker-compose down -v`

## Solution Summary
- Discover hidden paths via `robots.txt`
- Login to portal
- Exploit IDOR at `user_panel.php?id=<ID>` - access ID 13 (Gon Freecss) to get flag: `SSP{WEB_IDOR_2A91FC7D}`

## Walkthrough
See source files and challenge flow documented in repository.
