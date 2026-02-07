<?php 
// 1. Include the brain - handles the database and the relative pathing logic
include 'includes/header.php'; 
?>

<style>
  .gallery-section { padding: 40px 20px; text-align: center; }
  .gallery-grid { 
    display: flex; 
    overflow-x: auto; 
    gap: 15px; 
    padding: 20px 0; 
    scroll-snap-type: x mandatory; 
    scrollbar-width: none; /* Hide scrollbar for Firefox */
  }
  .gallery-grid::-webkit-scrollbar { display: none; } /* Hide scrollbar for Chrome/Safari */

  .gallery-item { 
    flex: 0 0 auto; 
    width: 250px; 
    scroll-snap-align: center; 
    cursor: pointer; 
    transition: transform 0.3s ease; 
  }
  .gallery-item:hover { transform: scale(1.05); }
  .gallery-item img { width: 100%; height: 180px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

  /* THE MODAL (FIXED POSITIONING) */
  .modal { 
    display: none; 
    position: fixed; 
    z-index: 9999; 
    left: 0; top: 0; 
    width: 100%; height: 100%; 
    background: rgba(0, 0, 0, 0.95); 
    justify-content: center; 
    align-items: center; 
    flex-direction: column;
  }
  .modal-inner { position: relative; text-align: center; max-width: 90%; }
  .modal-content { max-width: 100%; max-height: 80vh; border-radius: 8px; }
  .modal-title { color: #fff; margin-top: 15px; font-size: 1.2rem; font-family: 'Sora', sans-serif; }
  .close-modal { 
    position: absolute; 
    top: -50px; right: 0; 
    color: #fff; font-size: 40px; 
    cursor: pointer; 
  }
</style>

<section class="hero">
  <div class="hero-content">
    <h2>We Build Your Digital Presence and Maintain Your Physical Space.</h2>
    <div class="hero-buttons">
      <a href="services.php#digital" class="btn dark">OUR EXPERTISE</a>
    </div>
  </div>
  <div class="hero-image">
    <img src="<?php echo auto_version('/static/webimage.jpg'); ?>" alt="Facility Management and Digital Solutions">
  </div>
</section>

<section class="info-section">
  <div class="info-card">
    <h3>FACILITY MANAGEMENT</h3>
    <p><strong>STREAMLINE YOUR OPERATIONS</strong></p>
    <p>We ensure your business premises are safe, clean, and fully functional.</p>
  </div>
  <div class="info-card">
    <h3>DIGITAL SOLUTIONS</h3>
    <p><strong>AMPLIFY YOUR BRAND</strong></p>
    <p>From captivating web design to insightful data analytics, we build the modern digital tools.</p>
  </div>
</section>

<section class="gallery-section">
  <h2>Our Gallery</h2>

  <div class="gallery-grid">
    <?php
      try {
          // Fetch items marked as 'gallery' in your SQL database
          $stmt = $pdo->prepare("SELECT * FROM projects WHERE service_type = 'gallery' ORDER BY created_at DESC");
          $stmt->execute();
          $galleryItems = $stmt->fetchAll();

          if (count($galleryItems) > 0) {
              foreach ($galleryItems as $item) {
                  // CORRECT XAMPP PATH: /olatgroup + /public/projects/filename.jpg
                  $imagePath = '/olatgroup' . $item['image_path'];
                  
                  echo '<div class="gallery-item" data-title="' . htmlspecialchars($item['title']) . '">';
                  echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($item['title']) . '">';
                  echo '</div>';
              }
          } else {
              echo '<p style="color:#888;">No projects in gallery yet.</p>';
          }
      } catch (PDOException $e) {
          echo '<p>Error loading gallery.</p>';
      }
    ?>
  </div>

  <div id="imageModal" class="modal">
     <div class="modal-inner">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="modalImage">
        <p id="modalTitle" class="modal-title"></p>
     </div>
  </div>
</section>

<script>
  // SELECT ALL GALLERY ITEMS
  const galleryItems = Array.from(document.querySelectorAll('.gallery-item'));
  const modal = document.getElementById('imageModal');
  const modalImg = document.getElementById('modalImage');
  const modalTitle = document.getElementById('modalTitle');
  const closeBtn = document.querySelector('.close-modal');
  let currentIndex = 0;

  // OPEN MODAL
  function openModal(src, title, index) {
    currentIndex = index;
    modalImg.src = src;
    modalTitle.textContent = title;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Stop page scroll
  }

  galleryItems.forEach((item, idx) => {
    const img = item.querySelector('img');
    const title = item.getAttribute('data-title');
    img.addEventListener('click', () => { 
      openModal(img.src, title, idx); 
    });
  });

  // CLOSE MODAL
  function closeModal() { 
    modal.style.display = 'none'; 
    document.body.style.overflow = 'auto'; 
  }

  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  // NAVIGATION & SWIPE
  function updateModal() {
    const item = galleryItems[currentIndex];
    const img = item.querySelector('img');
    modalImg.src = img.src;
    modalTitle.textContent = item.getAttribute('data-title');
  }

  function prevImage() {
    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
    updateModal();
  }
  function nextImage() {
    currentIndex = (currentIndex + 1) % galleryItems.length;
    updateModal();
  }

  // KEYBOARD SUPPORT
  document.addEventListener('keydown', (e) => {
    if (modal.style.display === 'flex') {
      if (e.key === 'ArrowLeft') prevImage();
      if (e.key === 'ArrowRight') nextImage();
      if (e.key === 'Escape') closeModal();
    }
  });
</script>

<?php 
include 'includes/footer.php'; 
?>