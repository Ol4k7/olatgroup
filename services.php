<?php 
// Include the optimized header (contains $pdo and auto_version)
include 'includes/header.php'; 
?>
  <style>
    .p{
        font-size: 1.1rem;
        color: #ffffff;
        line-height: 1.8;}
  </style>

  <section class="page-header">
    <h2>Our Services</h2>
    <p>Tap any card to explore our expertise</p>
  </section>

  <section class="flip-container">
    <div class="flip-card" data-service="facilities">
      <div class="flip-inner">
        <div class="flip-front"><h2>Facility Management</h2></div>
        <div class="flip-back">
          <h2>Facility Management</h2> 
          <p>We deliver <strong>world-class facility management</strong> that keeps your spaces clean, safe, and efficient.</p>
          <ul>
            <li>Daily & deep cleaning</li>
            <li>Preventive maintenance</li>
            <li>Safety inspections</li>
            <li>24/7 emergency response</li>
          </ul>
          <div class="flip-actions">
            <button class="back-flip">Back</button>
            <button class="explore-btn" data-target="facilities">Explore</button>
          </div>
        </div>
      </div>
    </div>

    <div class="flip-card" data-service="digital">
      <div class="flip-inner">
        <div class="flip-front"><h2>Digital Solutions</h2></div>
        <div class="flip-back">
          <h2>Digital Solutions</h2>
          <p>We build <strong>digital experiences</strong> that turn ideas into impact, from websites to branding.</p>
          <ul>
            <li>Fast, responsive web design</li>
            <li>Logo & brand identity</li>
            <li>Social media graphics</li>
            <li>UI/UX & digital strategy</li>
          </ul>
          <div class="flip-actions">
            <button class="back-flip">Back</button>
            <button class="explore-btn" data-target="digital">Explore</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div id="portfolioOverlay" class="portfolio-overlay">
    <div class="portfolio-container">
      <button id="closePortfolio" class="close-btn"><i class="fas fa-times"></i></button>
      <div id="portfolioContent"></div>
    </div>
  </div>

  <script>
    /**
     * 1. CORE DATA FETCHING
     * Points directly to your one and only projects.php in the admin folder.
     */
    async function loadPortfolio(service) {
      const content = document.getElementById("portfolioContent");
      content.innerHTML = `<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading ${service} projects...</div>`;

      try {
        // Fetching from the admin folder
        const res = await fetch(`admin/projects.php?service=${service}`);
        if (!res.ok) throw new Error("Failed to connect to projects.php");
        
        const projects = await res.json();

        if (service === "facilities") {
          renderFacilities(projects);
        } else {
          renderDigital(projects);
        }
      } catch (err) {
        content.innerHTML = `<p class="error">Failed to load projects. Ensure admin/projects.php is accessible.</p>`;
        console.error(err);
      }
    }

    /**
     * 2. RENDERING LOGIC
     */
    function renderFacilities(projects) {
      const html = `
        <h2 style="color: #ffffff;" class="portfolio-title">Facilities Projects</h2>
        <div class="project-grid">
          ${projects.length === 0 ? '<p style="color: #ffffff;">No facility projects uploaded yet.</p>' : ''}
          ${projects.map(p => `
            <div class="project-card">
              <img src="${p.image}" alt="${p.title}" onerror="this.src='https://via.placeholder.com/400x250?text=Olat+Group'">
              <div class="project-info">
                <h4>${p.title}</h4>
                <p>${p.description}</p>
              </div>
            </div>
          `).join('')}
        </div>
      `;
      document.getElementById("portfolioContent").innerHTML = html;
    }

    function renderDigital(projects) {
      const web = projects.filter(p => p.type === "web");
      const graphics = projects.filter(p => p.type === "graphics");
      
      const tabs = `
        <div class="portfolio-tabs">
          <button class="tab-btn active" data-tab="web">Web Design</button>
          <button class="tab-btn" data-tab="graphics">Graphics Design</button>
        </div>
        <div id="tabContent"></div>
      `;
      document.getElementById("portfolioContent").innerHTML = tabs;
      document.getElementById("tabContent").innerHTML = renderWeb(web);

      document.querySelectorAll(".tab-btn").forEach(btn => {
        btn.addEventListener("click", () => {
          document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          const tab = btn.dataset.tab;
          document.getElementById("tabContent").innerHTML = tab === "web" ? renderWeb(web) : renderGraphics(graphics);
        });
      });
    }

    function renderWeb(projects) {
      return `
        <div class="project-grid">
          ${projects.length === 0 ? '<p style="color: #ffffff;">No web design projects found.</p>' : ''}
          ${projects.map(p => `
            <a href="${p.url}" target="_blank" class="project-card web">
              <img src="${p.image}" alt="${p.title}">
              <div class="overlay"><i class="fas fa-external-link-alt"></i> View Live Site</div>
              <div class="project-info"><h4>${p.title}</h4></div>
            </a>
          `).join('')}
        </div>
      `;
    }

    function renderGraphics(projects) {
      const categories = {};
      projects.forEach(p => {
        const cat = p.category || "General Branding";
        if (!categories[cat]) categories[cat] = [];
        categories[cat].push(p);
      });

      if (Object.keys(categories).length === 0) return '<p style="color: #ffffff;">No graphics design projects found.</p>';
      
      return Object.entries(categories).map(([cat, items]) => `
        <div class="graphics-category">
          <h3>${cat}</h3>
          <div class="gallery-grid">
            ${items.map(p => `
              <img src="${p.image}" alt="${p.title}" onclick="window.open('${p.image}', '_blank')">
            `).join('')}
          </div>
        </div>
      `).join('');
    }

    /**
     * 3. INTERACTION LISTENERS
     */
    document.querySelectorAll(".explore-btn").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation(); // Prevents flip-card from toggling when clicking button
        const service = btn.dataset.target;
        document.getElementById("portfolioOverlay").classList.add("active");
        loadPortfolio(service);
      });
    });

    document.getElementById("closePortfolio").addEventListener("click", () => {
      document.getElementById("portfolioOverlay").classList.remove("active");
    });

    // Flip card toggle for mobile/tap users
    document.querySelectorAll('.flip-card').forEach(card => {
      card.addEventListener('click', () => {
        card.classList.toggle('flipped');
      });
    });

    document.querySelectorAll('.back-flip').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        btn.closest('.flip-card').classList.remove('flipped');
      });
    });
  </script>

<?php 
include 'includes/footer.php'; 
?>