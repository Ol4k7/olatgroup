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
        <li><a href="/contact.php" class="active">Contact</a></li> 
      </ul>
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

    <form id="contactForm" class="contact-form" action="https://formsubmit.co/info@olatgrouplimited.co.uk" method="POST">
      
      <input type="hidden" name="_next" value="https://olatgrouplimited.co.uk/static/contact.php?sent=true">

      <input type="hidden" name="_subject" value="New Inquiry - Olat Group Website">

      <input type="text" name="_honey" style="display:none">

      <input type="hidden" name="_captcha" value="false">

      <div id="successMessage" style="display:none; background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb;">
        <i class="fas fa-check-circle"></i> Message sent successfully! We will contact you soon.
      </div>

      <input type="text" name="name" id="name" placeholder="Your Name" required />
      
      <input type="email" name="email" id="email" placeholder="Your Email" required />
      
      <textarea name="message" id="message" placeholder="Your Message" rows="5" required></textarea>
      
      <button type="submit" class="btn gold">Send Message</button>
    </form>
    </section>

  <footer>
    &copy; <span id="year"></span> Olat Group | All Rights Reserved
  </footer>

  <script src="js/script.js?v=20251209"></script>

  <script>
  
  if (window.location.search.includes('sent=true')) {
    document.getElementById('successMessage').style.display = 'block';
    document.getElementById('contactForm').scrollIntoView({ behavior: 'smooth' });
    window.history.replaceState({}, document.title, window.location.pathname);
  }
</script>
</body>
</html>