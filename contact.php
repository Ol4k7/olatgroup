<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * contact.php - Secure SMTP Contact Form
 * Path: /olatgroup/contact.php
 */

// 1. Load Configurations and PHPMailer
require_once __DIR__ . '/config/config.php'; 
include 'includes/header.php'; 

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Initialize Variables
$statusMessage = '';
$statusClass = '';

// 3. CSRF Protection (Generate token if none exists)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 4. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- SECURITY CHECKS ---
    
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security validation failed. Please refresh the page.");
    }

    // Check Honeypot (Spam Bots)
    if (!empty($_POST['_honey'])) {
        exit("Spam detected.");
    }

    // --- DATA CLEANING & VALIDATION ---
    $name    = trim(htmlspecialchars($_POST['name']));
    $email   = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $message = trim(htmlspecialchars($_POST['message']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = "Invalid email format.";
        $statusClass = "error-msg";
    } 
    elseif (strlen($message) > 2000) {
        $statusMessage = "Message is too long. Please stay under 2000 characters.";
        $statusClass = "error-msg";
    }
    elseif (strlen($name) < 2 || strlen($name) > 100) {
        $statusMessage = "Please enter a valid name.";
        $statusClass = "error-msg";
    }
    else {
        // --- PROCESS EMAIL (PHPMailer) ---
        $mail = new PHPMailer(true);

        try {
            // Server Settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST; 
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER; 
            $mail->Password   = SMTP_PASS; // 16-char App Password from config.php
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_USER, 'Olat Group Web Inquiry');
            $mail->addAddress(SMTP_USER); // Sending to yourself
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Inquiry: ' . $name;
            
            $msgHTML = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #0066ff;'>Website Contact Inquiry</h2>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br($message) . "</p>
                </div>";

            $mail->Body = $msgHTML;

            $mail->send();
            $statusMessage = "Thank you! Your message has been sent successfully.";
            $statusClass = "success-msg";
            
            // Regenerate CSRF to prevent double submission
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $statusMessage = "Message could not be sent. Please try again later.";
            // $statusMessage .= $mail->ErrorInfo; // Uncomment for debugging in XAMPP
            $statusClass = "error-msg";
        }
    }
}
?>

<style>
  .success-msg { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px; text-align: center; }
  .error-msg { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb; margin-bottom: 20px; text-align: center; }
  .char-count { text-align: right; font-size: 0.85rem; color: #666; margin-top: -10px; margin-bottom: 10px; }
  #submitBtn:disabled { background: #ccc; cursor: not-allowed; opacity: 0.7; }
</style>

<section class="page-header">
    <h2>Contact Us</h2>
    <p>Let’s build something amazing together.</p>
</section>

<section class="contact-section">
    <div class="contact-card">
        <h3>Get in Touch</h3>
        <ul class="contact-list">
            <li><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:info@olatgrouplimited.co.uk">info@olatgrouplimited.co.uk</a></li>
            <li><i class="fas fa-phone"></i> <strong>Phone:</strong> <a href="tel:+447475598094">+44 74 7559 8094</a></li>
            <li><i class="fab fa-whatsapp"></i> <strong>WhatsApp:</strong> <a href="https://wa.me/447475598094" target="_blank">+44 74 7559 8094</a></li>
        </ul>
    </div>

    <form id="contactForm" class="contact-form" method="POST" action="contact.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="text" name="_honey" style="display:none">

        <?php if ($statusMessage): ?>
            <div class="<?php echo $statusClass; ?>">
                <i class="fas <?php echo ($statusClass == 'success-msg') ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i> 
                <?php echo $statusMessage; ?>
            </div>
        <?php endif; ?>

        <input type="text" name="name" id="name" placeholder="Your Name" required maxlength="100" />
        <input type="email" name="email" id="email" placeholder="Your Email" required />
        
        <textarea name="message" id="message" placeholder="Your Message" rows="5" maxlength="2000" required></textarea>
        <div id="charCount" class="char-count">0 / 2000</div>
        
        <button type="submit" id="submitBtn" class="btn gold">Send Message</button>
    </form>
</section>



<script>
    const form = document.getElementById('contactForm');
    const msgArea = document.getElementById('message');
    const charCounter = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');

    // Real-time Character Limiting
    msgArea.addEventListener('input', function() {
        const len = this.value.length;
        charCounter.textContent = `${len} / 2000`;
        charCounter.style.color = (len >= 2000) ? '#e11d48' : '#666';
    });

    // Prevent Mail Bombing / Multiple Submissions
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    });

    // Clean URL history
    if (window.location.search) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>

<?php include 'includes/footer.php'; ?>