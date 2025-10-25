document.addEventListener("DOMContentLoaded", () => {
  console.log("💠 Olat Group website loaded successfully.");

  // === Responsive Menu Toggle ===
  const menuToggle = document.getElementById("menuToggle");
  const navList = document.getElementById("navList");

  if (menuToggle && navList) {
    menuToggle.addEventListener("click", () => {
      navList.classList.toggle("show");
      menuToggle.classList.toggle("open");
    });

    // Close menu when link is clicked (mobile UX)
    document.querySelectorAll("nav ul li a").forEach((link) => {
      link.addEventListener("click", () => {
        navList.classList.remove("show");
        menuToggle.classList.remove("open");
      });
    });
  }

  // === Header Shrink on Scroll ===
  window.addEventListener("scroll", () => {
    const header = document.querySelector("header");
    if (window.scrollY > 50) {
      header.classList.add("shrink");
    } else {
      header.classList.remove("shrink");
    }
  });

  // === Scroll Reveal Animation ===
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.15 }
  );

  document.querySelectorAll(".info-card, .contact-card, .contact-form, .hero-content")
    .forEach((el) => observer.observe(el));

  // === Contact Form Handler ===
  const contactForm = document.getElementById("contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const message = document.getElementById("message").value.trim();

      if (!name || !email || !message) {
        alert("⚠️ Please fill in all fields before sending.");
        return;
      }

      const recipient = "tolulopetheophilus96@gmail.com";
      const subject = encodeURIComponent(`New Message from ${name}`);
      const body = encodeURIComponent(
        `Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}`
      );

      const mailtoLink = `mailto:${recipient}?subject=${subject}&body=${body}`;

      window.open(mailtoLink, "_blank");
      alert("✅ Opening your email app…");
    });
  }
});
