<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Olat Group | Innovating Spaces & Technology</title>
  <link rel="stylesheet" href="/static/css/style.css?v=1.0.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="apple-touch-icon" sizes="180x180" href="/favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">
  <link rel="manifest" href="/favicon_io/site.webmanifest">
  <link rel="icon" href="/favicon_io/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/favicon_io/favicon.ico" type="image/x-icon">

  <!-- Styles for the gallery section and modal -->
  <style>
    /* Gallery Section Styles */
    .gallery-section {
      padding: 4rem 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .gallery-section h2 {
      text-align: center;
      margin-bottom: 2rem;
    }

    .gallery-grid {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
      padding-bottom: 1rem;
    }

    .gallery-item {
      flex: 0 0 auto;
      width: 200px;
      scroll-snap-align:start;
      cursor: pointer;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .gallery-item:hover {
      transform: scale(1.05);
    }

    .gallery-item img {
      width: 100%;
      height: 50%;
      display: block;
      border-radius: 8px;
    }

    /* Modal for Tap to Expand */
    .modal {
      display: none;
      position: fixed;
      z-index: 10000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      max-width: 90%;
      max-height: 90%;
      border-radius: 8px;
    }

    .close-modal {
      position: absolute;
      top: 20px;
      right: 30px;
      color: white;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
    }

    .modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); justify-content: center; align-items: center; overflow: hidden; }
    .modal-content { max-width: 90%; max-height: 90%; border-radius: 8px; transition: transform 0.3s ease; cursor: grab; }
  </style>
</head>
<body>
  <header>
    <a href="/" class="logo">
      <img src="/static/weblogo.png" alt="Olat Group Logo">
      <h1>Olat Group <span>Limited</span></h1>
    </a>
    <button class="menu-toggle" id="menuToggle">&#9776;</button>
    <nav>
      <ul id="navList">
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="/static/about.html">About Us</a></li>
        <li><a href="/static/services.html">Services</a></li>
        <li><a href="/static/contact.html">Contact</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h2>We Build Your Digital Presence and Maintain Your Physical Space.</h2>
      <div class="hero-buttons">
        <a href="/static/services.html#digital" class="btn dark">OUR EXPERTISE</a>
      </div>
    </div>
    <div class="hero-image">
      <img src="/static/webimage.jpg" alt="Illustration">
    </div>
  </section>

  <section class="info-section">
    <div class="info-card">
      <h3>FACILITIES</h3>
      <p>Olat Group Limited is a leading provider of facility management and digital solutions. We ensure that businesses and spaces operate at their highest efficiency.</p>
    </div>
    <div class="info-card">
      <h3>DIGITAL SOLUTIONS</h3>
      <p>We offer a range of digital services, such as web design, graphic design, and data analytics to help businesses thrive in the modern era.</p>
    </div>
  </section>

  <!-- New Gallery Section for Tap-to-Expand Images -->
  <section class="gallery-section">
    <h2>Our Gallery</h2>

    <!-- Gallery Grid – PHP to list images from JSON data -->
    <div class="gallery-grid">
      <?php

        require_once __DIR__ . '/config.php';
        $data = json_decode(file_get_contents(DATA_FILE), true);
        $galleryItems = $data['gallery'] ?? [];
        usort($galleryItems, fn($a, $b) => strtotime($b['timestamp']) - strtotime($a['timestamp']));
        foreach ($galleryItems as $item) {
          if (isset($item['image'])) {
             echo '<div class="gallery-item" onclick="openModal(\'' . htmlspecialchars($item['image']) . '\')">';
            echo '<img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title'] ?? 'Gallery Image') . '">';
            echo '</div>';
          }
        }
      ?>
    </div>

    <!-- Modal for Tap to Expand -->
    <div id="imageModal" class="modal" onclick="closeModal()">
      <span class="close-modal" onclick="closeModal()">&times;</span>
      <img class="modal-content" id="modalImage">
    </div>
  </section>

  <footer>
    &copy; 2025 Olat Group | All Rights Reserved
  </footer>
  <script src="/static/js/script.js?v=1.0.0"></script>

  <!-- JavaScript for Tap to Expand Modal -->
  <script>
    function openModal(src) {
      document.getElementById('modalImage').src = src;
      document.getElementById('imageModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('imageModal').style.display = 'none';
    }
    const galleryImages = Array.from(document.querySelectorAll('.gallery-item img'));
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    let currentIndex = 0;

    // Open modal
    galleryImages.forEach((img, idx) => {
      img.addEventListener('click', () => {
        currentIndex = idx;
        showImage();
        modal.style.display = 'flex';
      });
    });

    function showImage() {
      modalImg.src = galleryImages[currentIndex].src;
    }

    function closeModal() {
      modal.style.display = 'none';
    }

    modal.addEventListener('click', closeModal);

    // Swipe support
    let startX = 0;
    let isDragging = false;

    modalImg.addEventListener('touchstart', e => {
      startX = e.touches[0].clientX;
    });

    modalImg.addEventListener('touchend', e => {
      const endX = e.changedTouches[0].clientX;
      handleSwipe(startX, endX);
    });

    modalImg.addEventListener('mousedown', e => {
      isDragging = true;
      startX = e.clientX;
      e.preventDefault();
    });

    modalImg.addEventListener('mouseup', e => {
      if (!isDragging) return;
      isDragging = false;
      handleSwipe(startX, e.clientX);
    });

    function handleSwipe(start, end) {
      if (end - start > 50) prevImage();
      else if (start - end > 50) nextImage();
    }

    function prevImage() {
      currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
      showImage();
    }

    function nextImage() {
      currentIndex = (currentIndex + 1) % galleryImages.length;
      showImage();
    }
  </script>
</body>
</html>