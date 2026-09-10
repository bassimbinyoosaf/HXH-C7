-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS hunter_association;
USE hunter_association;

-- Create hunters table
CREATE TABLE IF NOT EXISTS hunters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    hunter_rank ENUM('Novice', 'Pro', 'Elite', 'Legendary') DEFAULT 'Novice',
    hunter_license VARCHAR(20) UNIQUE NOT NULL,
    assigned_missions TEXT,
    clearance_level INT DEFAULT 1,
    secret_flag VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create missions table
CREATE TABLE IF NOT EXISTS missions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    assigned_to INT,
    status ENUM('Pending', 'Active', 'Completed', 'Failed') DEFAULT 'Pending',
    classification ENUM('Public', 'Confidential', 'Top Secret', 'Hunter-only') DEFAULT 'Public',
    FOREIGN KEY (assigned_to) REFERENCES hunters(id) ON DELETE SET NULL
);

-- Create audit_log table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hunter_id INT,
    action VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hunter_id) REFERENCES hunters(id) ON DELETE CASCADE
);

-- Insert hunters with hashed passwords
INSERT INTO hunters (id, username, password_hash, email, hunter_rank, hunter_license, assigned_missions, clearance_level, secret_flag) VALUES
(1, 'netero_chairman', '$2y$12$OF4dbQmcKG/OXOHZgVM4t./zSQQWV5GramGPniURuhOsVneXFI99y', 'netero@hunter-assoc.org', 'Legendary', 'HUNTER-LIC-0001', 'Greed Island oversight', 5, NULL),
(2, 'ging_freecss', '$2y$12$DS2Sy3OOfUnbLe395iHI/OOAFPBKoyCcEwhzPULok7t96zpwePEKy', 'ging@hunter-assoc.org', 'Elite', 'HUNTER-LIC-0002', 'Dark Continent expedition', 4, NULL),
(3, 'pariston_hill', '$2y$12$x7OFWebOSXP7pxCayq9NmO5ZqHCC0WMXAwOdwwrpECI3E8jtGIxbG', 'pariston@hunter-assoc.org', 'Elite', 'HUNTER-LIC-0003', 'Vice-chairman duties', 4, NULL),
(4, 'biscuit_krueger', '$2y$12$fmDbd3.Jf.d9GtZKbNUVa.d7Hf.7ksGykgK/z.9KVxbT0UsOLt5/y', 'biscuit@hunter-assoc.org', 'Pro', 'HUNTER-LIC-0004', 'Training new Hunters', 3, NULL),
(5, 'killua_zoldyck', '$2y$12$kshb10htlkJ.YhwdLc4U5OlpMhQAfrpHW2Yw1ywRudfX.8GvlukRC', 'killua@hunter-assoc.org', 'Pro', 'HUNTER-LIC-0005', 'Gon rescue mission', 3, NULL),
(6, 'phantom_troupe_spy', '$2y$12$V.mrHeeT925oaLtKNZOgdeY4yylbKbu7DULQLXehWGSQJfMNbz156', 'spy@phantom-troupe.org', 'Novice', 'HUNTER-LIC-0006', 'Infiltration operations', 1, NULL),
(7, 'admin_system', '$2y$12$OF4dbQmcKG/OXOHZgVM4t./zSQQWV5GramGPniURuhOsVneXFI99y', 'admin@hunter-assoc.org', 'Elite', 'HUNTER-LIC-0007', 'System Administration', 5, 'SSP{PRANKED_YOU_LOL}'),
(8, 'admin_network', '$2y$12$DS2Sy3OOfUnbLe395iHI/OOAFPBKoyCcEwhzPULok7t96zpwePEKy', 'network@hunter-assoc.org', 'Pro', 'HUNTER-LIC-0008', 'Network Security', 4, 'SSP{KEEP_TRYING_HUNTER}'),
(9, 'admin_security', '$2y$12$x7OFWebOSXP7pxCayq9NmO5ZqHCC0WMXAwOdwwrpECI3E8jtGIxbG', 'security@hunter-assoc.org', 'Elite', 'HUNTER-LIC-0009', 'Security Operations', 5, 'SSP{NOT_HERE_LOL}'),
(10, 'admin_database', '$2y$12$fmDbd3.Jf.d9GtZKbNUVa.d7Hf.7ksGykgK/z.9KVxbT0UsOLt5/y', 'database@hunter-assoc.org', 'Pro', 'HUNTER-LIC-0010', 'Database Management', 4, 'SSP{NICE_TRY_404}'),
(11, 'admin_phantom', '$2y$12$kshb10htlkJ.YhwdLc4U5OlpMhQAfrpHW2Yw1ywRudfX.8GvlukRC', 'phantom@hunter-assoc.org', 'Novice', 'HUNTER-LIC-0011', 'Undercover Operations', 2, 'SSP{FAKE_FLAG_1337}'),
(12, 'admin_troupe', '$2y$12$V.mrHeeT925oaLtKNZOgdeY4yylbKbu7DULQLXehWGSQJfMNbz156', 'troupe@hunter-assoc.org', 'Novice', 'HUNTER-LIC-0012', 'Surveillance', 1, 'SSP{FAKE_FLAG_9999}'),
(13, 'gon_freecss', '$2y$12$OF4dbQmcKG/OXOHZgVM4t./zSQQWV5GramGPniURuhOsVneXFI99y', 'gon@hunter-assoc.org', 'Pro', 'HUNTER-LIC-0013', 'Greed Island investigation', 3, 'SSP{WEB_IDOR_2A91FC7D}'),
(14, 'kurapika_curse', '$2y$12$DS2Sy3OOfUnbLe395iHI/OOAFPBKoyCcEwhzPULok7t96zpwePEKy', 'kurapika@hunter-assoc.org', 'Elite', 'HUNTER-LIC-0014', 'Phantom Troupe tracking', 4, 'SSP{WARMER_BUT_NO}'),
(15, 'leorio_medic', '$2y$12$x7OFWebOSXP7pxCayq9NmO5ZqHCC0WMXAwOdwwrpECI3E8jtGIxbG', 'leorio@hunter-assoc.org', 'Pro', 'HUNTER-LIC-0015', 'Medical operations', 3, 'SSP{TRY_AGAIN_NOOB}'),
(16, 'hisoka_magic', '$2y$12$fmDbd3.Jf.d9GtZKbNUVa.d7Hf.7ksGykgK/z.9KVxbT0UsOLt5/y', 'hisoka@hunter-assoc.org', 'Elite', 'HUNTER-LIC-0016', 'Special intelligence', 4, NULL),
(17, 'chrollo_leader', '$2y$12$kshb10htlkJ.YhwdLc4U5OlpMhQAfrpHW2Yw1ywRudfX.8GvlukRC', 'chrollo@hunter-assoc.org', 'Legendary', 'HUNTER-LIC-0017', 'Phantom Troupe leadership', 5, NULL),
(18, 'meruem_king', '$2y$12$V.mrHeeT925oaLtKNZOgdeY4yylbKbu7DULQLXehWGSQJfMNbz156', 'meruem@hunter-assoc.org', 'Legendary', 'HUNTER-LIC-0018', 'Chimera Ant operations', 5, NULL);

-- Insert missions
INSERT INTO missions (id, title, description, assigned_to, status, classification) VALUES
(1, 'Greed Island Server Security', 'Review and patch vulnerabilities in GI servers', 1, 'Active', 'Top Secret'),
(2, 'Phantom Troupe Tracing', 'Track digital footprints of PT members', 2, 'Active', 'Confidential'),
(3, 'Dark Continent Data Analysis', 'Analyze recovered data from DC expeditions', 3, 'Pending', 'Hunter-only'),
(4, 'License Renewal Audit', 'Verify all Hunter license renewals', 4, 'Active', 'Confidential'),
(5, 'Gon Recovery Mission', 'Coordinate search for missing Hunter', 5, 'Active', 'Top Secret'),
(6, 'Internal Security Threat', 'Investigate potential insider threat', 6, 'Pending', 'Top Secret'),
(7, 'System Integrity Check', 'Verify all system logs for unauthorized access', 7, 'Active', 'Top Secret'),
(8, 'Network Penetration Test', 'Simulate attack on internal network', 8, 'Pending', 'Confidential'),
(9, 'Security Audit', 'Full security audit of all systems', 9, 'Active', 'Top Secret'),
(10, 'Database Encryption', 'Implement encryption for sensitive data', 10, 'Pending', 'Confidential'),
(11, 'Phantom Troupe Infiltration', 'Gather intelligence on PT activities', 11, 'Active', 'Top Secret'),
(12, 'Dark Continent Expedition Prep', 'Prepare for next expedition', 12, 'Pending', 'Hunter-only'),
(13, 'Greed Island Server Analysis', 'Analyze corrupted server data', 13, 'Active', 'Confidential'),
(14, 'Kurta Clan Investigation', 'Investigate massacre evidence', 14, 'Active', 'Top Secret'),
(15, 'Medical Emergency Response', 'Coordinate medical teams', 15, 'Pending', 'Public'),
(16, 'Magical Beast Research', 'Study magical creatures', 16, 'Active', 'Hunter-only'),
(17, 'Phantom Troupe Leader Tracking', 'Track Chrollo\'s movements', 17, 'Active', 'Top Secret'),
(18, 'Chimera Ant Threat Assessment', 'Assess current threat level', 18, 'Pending', 'Top Secret');
