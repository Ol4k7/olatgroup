<?php
  // --- OLAT GROUP CONTACT FORM LOGIC ---
  $msg = '';
  $msgClass = '';

  // Check if form was submitted
  if(filter_has_var(INPUT_POST, 'submit')){
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Check Required Fields
    if(!empty($email) && !empty($name) && !empty($message)){
      // Check Email Validity
      if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
        $msg = 'Please use a valid email address';
        $msgClass = 'alert-danger';
      } else {
        // CONFIGURATION
        $toEmail = 'tolulopetheophilus96@gmail.com'; // Your personal email
        $subject = 'New Message from Olat Group Website'; // Email Subject
        
        // Email Body Layout
        $body = "<h2>Contact Request</h2>
                 <p><strong>Name:</strong> $name</p>
                 <p><strong>Email:</strong> $email</p>
                 <p><strong>Message:</strong><br>$message</p>";

        // HEADERS
        $headers = "MIME-Version: 1.0" ."\r\n";
        $headers .="Content-Type:text/html;charset=UTF-8" . "\r\n";

        // CRITICAL: This MUST match your hosting domain to prevent Spam
        $headers .= "From: Olat Group <noreply@olatgrouplimited.co.uk>" . "\r\n"; 
        
        // Reply to the customer
        $headers .= "Reply-To: " . $email . "\r\n";

        // Attempt to send
        if(mail($toEmail, $subject, $body, $headers)){
          $msg = 'Message sent successfully!';
          $msgClass = 'alert-success';
          // Clear form after success
          $name = ''; $email = ''; $message = '';
          $_POST = array(); 
        } else {
          $msg = 'Message not sent (Server Error)';
          $msgClass = 'alert-danger';
        }
      }
    } else {
      $msg = 'Please fill in all fields';
      $msgClass = 'alert-danger';
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Get in touch with Olat Group for facility management or digital solutions. UK-based support via form, email, or phone.">
  <title>Contact Olat Group | Facility Management & Digital Services UK</title>
  <link rel="stylesheet" href="css/style.css?v=20251125" />
  <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDUiIGhlaWdodD0iNDUiIHZpZXdCb3g9IjAgMCA0NSAieG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIyMi41IiBjeT0iMjIuNSIgcj0iMjAiIGZpbGw9IiMwMDc4ZmYiLz48dGV4dCB4PSIyMi41IiB5PSIyNyIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE4IiBmaWxsPSJ3aGl0ZSI+TyI8L3RleHQ+PC9zdmc+" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="apple-touch-icon" sizes="180x180" href="/favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">
  <link rel="manifest" href="/favicon_io/site.webmanifest">
  <link rel="icon" href="/favicon_io/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/favicon_io/favicon.ico" type="image/x-icon">

  <style>
    .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; border: 1px solid #f5c6cb; }
  </style>
</head>
<body>

  <header>
    <a href="/" class="logo">
      <img src="weblogo.png" alt="Olat Group Logo">
      <h1>Olat Group <span>Limited</span></h1>
    </a>
    <button class="menu-toggle" id="menuToggle">&#9776;</button>
    <nav>
      <ul id="navList">
        <li><a href="/">Home</a></li>
        <li><a href="/static/about.html">About Us</a></li>
        <li><a href="/static/services.html">Services</a></li>
        <li><a href="/contact.php" class="active">Contact</a></li> </ul>
    </nav>
  </header>

  <section class="page-header">
    <h2>Contact Us</h2>
    <p>We’d love to hear from you, let’s build something amazing together.</p>
  </section>

  <section class="contact-section">
    <div class="contact-card">
      <h3>Get in Touch</h3>
      <ul class="contact-list">
        <li>
          <i class="fas fa-envelope"></i>
          <strong>Email:</strong>
          <a href="mailto:info@olatgrouplimited.co.uk">info@olatgrouplimited.co.uk</a>
        </li>
        <li>
          <i class="fas fa-phone"></i>
          <strong>Phone:</strong>
          <a href="tel:+447475598094">+44 74 7559 8094</a>
        </li>
        <li>
          <i class="fab fa-whatsapp"></i>
          <strong>WhatsApp:</strong>
          <a href="https://wa.me/447475598094" target="_blank">+44 74 7559 8094</a>
        </li>
        <li>
          <i class="fas fa-map-marker-alt"></i>
          <strong>Address:</strong>
          <span>United Kingdom</span>
        </li>
      </ul>
    </div>

    <form id="contactForm" class="contact-form" method="POST" action="">
      
      <?php if($msg != ''): ?>
        <div class="<?php echo $msgClass; ?>">
          <?php echo $msg; ?>
        </div>
      <?php endif; ?>

      <input type="text" name="name" id="name" placeholder="Your Name" required 
             value="<?php echo isset($name) ? $name : ''; ?>" />
      
      <input type="email" name="email" id="email" placeholder="Your Email" required 
             value="<?php echo isset($email) ? $email : ''; ?>" />
      
      <textarea name="message" id="message" placeholder="Your Message" rows="5" required><?php echo isset($message) ? $message : ''; ?></textarea>
      
      <button type="submit" name="submit" class="btn gold">Send Message</button>
    </form>
  </section>

  <footer>
    &copy; <span id="year"></span> Olat Group | All Rights Reserved
  </footer>

  <script src="js/script.js?v=20251119"></script>
</body>
</html>