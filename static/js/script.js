document.addEventListener("DOMContentLoaded", () => {
  console.log("Olat Group website loaded successfully.");

  // === Responsive Menu Toggle ===
  const menuToggle = document.getElementById("menuToggle");
  const navList = document.getElementById("navList");

  if (menuToggle && navList) {
    menuToggle.addEventListener("click", () => {
      navList.classList.toggle("show");
      menuToggle.classList.toggle("open");
    });

    // Close menu on link click (mobile)
    document.querySelectorAll("nav ul li a").forEach(link => {
      link.addEventListener("click", () => {
        navList.classList.remove("show");
        menuToggle.classList.remove("open");
      });
    });
  }

  // === Header Shrink on Scroll ===
  window.addEventListener("scroll", () => {
    const header = document.querySelector("header");
    header.classList.toggle("shrink", window.scrollY > 50);
  });

  // === Scroll Reveal Animation ===
  const observer = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.15 }
  );

  document.querySelectorAll(".info-card, .contact-card, .contact-form, .hero-content")
    .forEach(el => observer.observe(el));

  // === Contact Form Handler ===
  // const contactForm = document.getElementById("contactForm");
  // if (contactForm) {
  //   contactForm.addEventListener("submit", e => {
  //     e.preventDefault();

  //     const name = document.getElementById("name").value.trim();
  //     const email = document.getElementById("email").value.trim();
  //     const message = document.getElementById("message").value.trim();

  //     if (!name || !email || !message) {
  //       alert("Please fill in all fields before sending.");
  //       return;
  //     }

  //     const recipient = "info@olatgrouplimited.co.uk";
  //     const subject = encodeURIComponent(`New Message from ${name}`);
  //     const body = encodeURIComponent(`Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}`);
  //     const mailtoLink = `mailto:${recipient}?subject=${subject}&body=${body}`;

  //     window.open(mailtoLink, "_blank");
  //     alert("Opening your email app…");
  //   });
  // }

  // === FLIP CARD LOGIC (Only Active Feature) ===
  document.querySelectorAll('.flip-card').forEach(card => {
    const backBtn = card.querySelector('.back-flip');

    card.addEventListener('click', () => {
      if (!card.classList.contains('flipped')) {
        card.classList.add('flipped');
      }
    });

    backBtn?.addEventListener('click', e => {
      e.stopPropagation();
      card.classList.remove('flipped');

      void card.offsetWidth;

      card.querySelector('.flip-inner').style.transform = '';
    });
  });

  // === Dynamic Year ===
  const yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();
});