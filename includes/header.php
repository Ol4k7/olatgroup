<?php
// Start session for CSRF protection used in contact.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/config/config.php';

$current_page = basename($_SERVER['PHP_SELF']);

// 1. DYNAMIC SEO CONFIGURATION
$seo = [
    'index.php' => [
        'title' => 'Olat Group Limited | UK Facility Management & Digital Solutions',
        'desc'  => 'Premier UK partner for facility management and digital innovation. Professional cleaning, maintenance, web design, and branding services.'
    ],
    'about.php' => [
        'title' => 'About Olat Group | Our Mission & Core Values',
        'desc'  => 'Learn about Olat Group’s commitment to excellence in facility maintenance and digital strategy across the United Kingdom.'
    ],
    'services.php' => [
        'title' => 'Facility & Digital Services | Olat Group Limited',
        'desc'  => 'From deep cleaning and preventive maintenance to responsive web design and logo branding—explore our dual-sector expertise.'
    ],
    'contact.php' => [
        'title' => 'Contact Olat Group | Get a Professional Quote Today',
        'desc'  => 'Ready to innovate your space or brand? Contact Olat Group Limited for expert facility management or digital project inquiries.'
    ]
];

$page_title = $seo[$current_page]['title'] ?? 'Olat Group Limited';
$meta_desc  = $seo[$current_page]['desc'] ?? 'Innovating Spaces & Technology through Facility Management and Digital Solutions.';

// 2. AUTO-VERSIONING (Cache Busting)
function auto_version($file) {
    // This check was looking for /olatgroup, which is why it failed on live
    $web_path = $file; 
    $system_path = $_SERVER['DOCUMENT_ROOT'] . $web_path;

    if (file_exists($system_path)) {
        return $web_path . '?v=' . filemtime($system_path);
    }
    return $web_path;
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    
    <meta name="description" content="<?php echo $meta_desc; ?>" />
    <link rel="canonical" href="https://olatgrouplimited.co.uk/<?php echo ($current_page == 'index.php') ? '' : str_replace('.php', '', $current_page); ?>" />

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://olatgrouplimited.co.uk/<?php echo $current_page; ?>" />
    <meta property="og:title" content="<?php echo $page_title; ?>" />
    <meta property="og:description" content="<?php echo $meta_desc; ?>" />
    <meta property="og:image" content="https://olatgrouplimited.co.uk/static/webimage.jpg" />
    <meta property="og:site_name" content="Olat Group Limited" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="https://olatgrouplimited.co.uk/<?php echo $current_page; ?>" />
    <meta name="twitter:title" content="<?php echo $page_title; ?>" />
    <meta name="twitter:description" content="<?php echo $meta_desc; ?>" />
    <meta name="twitter:image" content="https://olatgrouplimited.co.uk/static/webimage.jpg" />

    <link rel="apple-touch-icon" sizes="180x180" href="/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/favicon_io/site.webmanifest">

    <link rel="stylesheet" href="<?php echo auto_version('/static/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<header>
    <a href="index.php" class="logo">
        <img src="<?php echo auto_version('/static/weblogo.png'); ?>" alt="Olat Group Logo">
        <h1>Olat Group <span>Limited</span></h1>
    </a>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">&#9776;</button>
    <nav>
        <ul id="navList">
            <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">About Us</a></li>
            <li><a href="services.php" class="<?= $current_page == 'services.php' ? 'active' : '' ?>">Services</a></li>
            <li><a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
        </ul>
    </nav>
</header>