<?php
// config.php
define('ADMIN_PASSWORD_HASH','$2y$12$lwXkBdjUFAGM6n.m6fl1be/swivvMzYcoOxp5/5ixewGmKtCQ/uJ2'); // ← Generate below
define('DATA_FILE', __DIR__ . '/data/projects.json');
define('UPLOAD_DIR', __DIR__ . '/public/projects');
define('ALLOWED_EXT', ['png', 'jpg', 'jpeg', 'gif', 'webp']);

// Create dirs
@mkdir(UPLOAD_DIR, 0755, true);
@mkdir(dirname(DATA_FILE), 0755, true);

// Init JSON
if (!file_exists(DATA_FILE)) {
    file_put_contents(DATA_FILE, json_encode(["facilities" => [], "digital" => []], JSON_PRETTY_PRINT));
}
?>