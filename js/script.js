// ============================================
// BrightPath Academy - Main JavaScript
// ============================================

(function () {
  "use strict";

  // Navbar Scroll Effect
  const navbar = document.querySelector(".navbar");
  if (navbar) {
    const handleScroll = () => {
      if (window.scrollY > 60) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    };
    window.addEventListener("scroll", handleScroll, { passive: true });
    handleScroll();
  }

  // Mobile Navigation
  const hamburger = document.getElementById("hamburger");
  const mobileMenu = document.getElementById("mobileMenu");

  if (hamburger && mobileMenu) {
    hamburger.addEventListener("click", () => {
      hamburger.classList.toggle("active");
      mobileMenu.classList.toggle("open");
      document.body.style.overflow = mobileMenu.classList.contains("open")
        ? "hidden"
        : "";
    });

    // Close on outside click
    document.addEventListener("click", (e) => {
      if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
        hamburger.classList.remove("active");
        mobileMenu.classList.remove("open");
        document.body.style.overflow = "";
      }
    });

    // Close on link click
    mobileMenu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        hamburger.classList.remove("active");
        mobileMenu.classList.remove("open");
        document.body.style.overflow = "";
      });
    });
  }

  // Active Nav Link
  const currentPath = window.location.pathname.split("/").pop() || "index.php";
  document.querySelectorAll(".nav-link[href]").forEach((link) => {
    const href = link.getAttribute("href").split("/").pop();
    if (href === currentPath || (currentPath === "" && href === "index.php")) {
      link.classList.add("active");
    }
  });

  // Counter Animation
  const animateCounter = (el, target, duration = 2000) => {
    let start = 0;
    const step = Math.ceil(target / (duration / 16));
    const timer = setInterval(() => {
      start += step;
      if (start >= target) {
        start = target;
        clearInterval(timer);
      }
      el.textContent = start.toLocaleString("en-IN");
    }, 16);
  };

  // Intersection Observer for counters
  const counters = document.querySelectorAll("[data-counter]");
  if (counters.length) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.counter);
            animateCounter(el, target);
            observer.unobserve(el);
          }
        });
      },
      { threshold: 0.5 }
    );
    counters.forEach((c) => observer.observe(c));
  }

  // Animate on Scroll
  const animateEls = document.querySelectorAll(".animate-on-scroll");
  if (animateEls.length) {
    const animObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("animated");
            animObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    animateEls.forEach((el) => animObserver.observe(el));
  }

  // Testimonials Slider
  const setupTestimonialSlider = () => {
    const slider = document.querySelector(".testimonials-slider");
    const track  = document.querySelector(".testimonials-track");
    if (!slider || !track) return;

    let current = 0;
    const cards = track.querySelectorAll(".testimonial-card");
    const total = cards.length;
    if (!total) return;

    const dots    = document.querySelectorAll(".slider-dot");
    const prevBtn = document.querySelector(".slider-btn.prev");
    const nextBtn = document.querySelector(".slider-btn.next");

    const getVisible = () => {
      if (window.innerWidth < 768)  return 1;
      if (window.innerWidth < 1100) return 2;
      return 3;
    };

    const goTo = (index) => {
      const visible  = getVisible();
      const gap      = visible === 1 ? 0 : 24;
      const maxIndex = Math.max(0, total - visible);
      current        = Math.max(0, Math.min(index, maxIndex));

      const cardWidth = Math.floor((slider.offsetWidth - gap * (visible - 1)) / visible);
      cards.forEach(c => { c.style.width = cardWidth + 'px'; c.style.minWidth = cardWidth + 'px'; });
      track.style.gap       = gap + 'px';
      track.style.transform = `translateX(-${current * (cardWidth + gap)}px)`;

      dots.forEach((d, i) => {
        d.classList.toggle("active", i === current);
        d.style.display = (i <= maxIndex) ? '' : 'none';
      });
    };

    goTo(0);

    if (prevBtn) prevBtn.addEventListener("click", () => goTo(current - 1));
    if (nextBtn) nextBtn.addEventListener("click", () => goTo(current + 1));
    dots.forEach((d, i) => d.addEventListener("click", () => goTo(i)));

    let autoSlide = setInterval(() => {
      const visible = getVisible();
      goTo(current + 1 > total - visible ? 0 : current + 1);
    }, 4500);
    track.addEventListener("mouseenter", () => clearInterval(autoSlide));
    track.addEventListener("mouseleave", () => {
      autoSlide = setInterval(() => {
        const visible = getVisible();
        goTo(current + 1 > total - visible ? 0 : current + 1);
      }, 4500);
    });

    let resizeTimer;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => goTo(0), 150);
    });
  };

  // Defer until page layout is fully computed
  if (document.readyState === 'complete') {
    setupTestimonialSlider();
  } else {
    window.addEventListener('load', setupTestimonialSlider);
  }

  // Back to Top
  const backToTop = document.querySelector(".back-to-top");
  if (backToTop) {
    window.addEventListener("scroll", () => {
      backToTop.classList.toggle("visible", window.scrollY > 400);
    }, { passive: true });
    backToTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  }

  // Progress bars animate
  const progressFills = document.querySelectorAll(".progress-fill[data-width]");
  if (progressFills.length) {
    const pObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.width = entry.target.dataset.width + "%";
          pObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    progressFills.forEach((p) => { p.style.width = "0%"; pObserver.observe(p); });
  }

  // Password Toggle
  document.querySelectorAll(".toggle-password").forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = document.querySelector(btn.dataset.target);
      if (!input) return;
      const isPass = input.type === "password";
      input.type = isPass ? "text" : "password";
      btn.textContent = isPass ? "🙈" : "👁";
    });
  });

  // Form Validation
  const setupFormValidation = (formId) => {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener("submit", (e) => {
      let valid = true;
      form.querySelectorAll("[required]").forEach((input) => {
        const errorEl = form.querySelector(`[data-error="${input.name}"]`);
        if (!input.value.trim()) {
          valid = false;
          input.style.borderColor = "var(--danger)";
          if (errorEl) errorEl.textContent = "This field is required";
        } else {
          input.style.borderColor = "";
          if (errorEl) errorEl.textContent = "";
        }

        if (input.type === "email" && input.value) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(input.value)) {
            valid = false;
            input.style.borderColor = "var(--danger)";
            if (errorEl) errorEl.textContent = "Enter a valid email";
          }
        }

        if (input.name === "phone" && input.value) {
          if (!/^[0-9]{10}$/.test(input.value.replace(/\s/g, ""))) {
            valid = false;
            input.style.borderColor = "var(--danger)";
            if (errorEl) errorEl.textContent = "Enter a valid 10-digit phone number";
          }
        }
      });

      if (!valid) {
        e.preventDefault();
        form.querySelector("[required][style*='danger']")?.scrollIntoView({ behavior: "smooth", block: "center" });
      } else {
        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="loader-ring" style="width:20px;height:20px;border-width:3px;display:inline-block;"></span> Processing...';
        }
      }
    });

    // Real-time validation
    form.querySelectorAll(".form-control").forEach((input) => {
      input.addEventListener("blur", () => {
        if (input.required && !input.value.trim()) {
          input.style.borderColor = "var(--danger)";
        } else {
          input.style.borderColor = "";
        }
      });
    });
  };

  setupFormValidation("contactForm");
  setupFormValidation("enrollmentForm");
  setupFormValidation("loginForm");
  setupFormValidation("registerForm");

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener("click", (e) => {
      const target = document.querySelector(a.getAttribute("href"));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });

  // Alert auto-dismiss
  document.querySelectorAll(".alert[data-dismiss]").forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      alert.style.transform = "translateY(-10px)";
      setTimeout(() => alert.remove(), 300);
    }, parseInt(alert.dataset.dismiss) || 4000);
  });

  // Course filter
  const filterBtns = document.querySelectorAll(".filter-btn");
  const courseCards = document.querySelectorAll(".course-card[data-category]");

  if (filterBtns.length && courseCards.length) {
    filterBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        filterBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        const filter = btn.dataset.filter;

        courseCards.forEach((card) => {
          const match = filter === "all" || card.dataset.category.toLowerCase().includes(filter.toLowerCase());
          card.style.display = match ? "" : "none";
          if (match) {
            card.style.animation = "fadeInUp 0.4s ease forwards";
          }
        });
      });
    });
  }

  // Copy to clipboard
  document.querySelectorAll(".copy-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const text = btn.dataset.copy;
      if (text) {
        navigator.clipboard.writeText(text).then(() => {
          btn.textContent = "✓ Copied!";
          setTimeout(() => (btn.textContent = "📋 Copy"), 2000);
        });
      }
    });
  });

  // Table search
  const tableSearch = document.getElementById("tableSearch");
  if (tableSearch) {
    const table = document.getElementById(tableSearch.dataset.table);
    if (table) {
      tableSearch.addEventListener("input", () => {
        const q = tableSearch.value.toLowerCase().trim();
        table.querySelectorAll("tbody tr").forEach((row) => {
          row.style.display = row.textContent.toLowerCase().includes(q) ? "" : "none";
        });
      });
    }
  }

  // Confirmed delete  
  document.querySelectorAll("[data-confirm]").forEach((el) => {
    el.addEventListener("click", (e) => {
      if (!confirm(el.dataset.confirm || "Are you sure?")) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  });

  // Image preview on upload
  document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
    input.addEventListener("change", () => {
      const preview = document.getElementById(input.dataset.preview);
      if (preview && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => (preview.src = e.target.result);
        reader.readAsDataURL(input.files[0]);
      }
    });
  });

  // Notification badge
  const notifBadge = document.querySelector(".notif-badge");
  if (notifBadge) {
    const count = parseInt(notifBadge.textContent);
    if (count > 0) {
      notifBadge.style.display = "flex";
    }
  }

  console.log("🎓 BrightPath Academy - Powered by Excellence");
})();
