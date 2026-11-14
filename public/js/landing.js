/**
 * BANTUIN - UI/UX Pro Max Landing Page Motion & Interactive Engine
 * Handles smooth scroll reveals, live stat counters, and interactive problem solver tabs.
 */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    // =========================================================================
    // 1. IntersectionObserver for Scroll Reveal Animations
    // =========================================================================
    const revealElements = document.querySelectorAll('.reveal-on-scroll, .reveal-fade');
    
    if ('IntersectionObserver' in window && revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            threshold: 0.15,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        // Fallback for older browsers
        revealElements.forEach(el => el.classList.add('is-revealed'));
    }

    // =========================================================================
    // 2. Animated Number Counters
    // =========================================================================
    const counterElements = document.querySelectorAll('.stat-counter-number[data-target]');
    
    function animateCounter(el) {
        const targetStr = el.getAttribute('data-target') || '0';
        const isDecimal = targetStr.includes('.');
        const target = parseFloat(targetStr.replace(/[^0-9.]/g, ''));
        const prefix = el.getAttribute('data-prefix') || '';
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = 2600; // ms: deliberate smooth presentation roll
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Ease out cubic
            const easeOutProgress = 1 - Math.pow(1 - progress, 3);
            const currentVal = target * easeOutProgress;

            if (isDecimal) {
                el.textContent = prefix + currentVal.toFixed(1) + suffix;
            } else {
                el.textContent = prefix + Math.floor(currentVal).toLocaleString('id-ID') + suffix;
            }

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                el.textContent = prefix + (isDecimal ? target.toFixed(1) : Math.floor(target).toLocaleString('id-ID')) + suffix;
            }
        }

        requestAnimationFrame(update);
    }

    if ('IntersectionObserver' in window && counterElements.length > 0) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        counterElements.forEach(counter => counterObserver.observe(counter));
    }

    // =========================================================================
    // 3. Interactive Problem Explorer Tabs
    // =========================================================================
    const explorerTabs = document.querySelectorAll('.explorer-tab-btn');
    const explorerPanels = document.querySelectorAll('.problem-tab-panel');

    if (explorerTabs.length > 0 && explorerPanels.length > 0) {
        explorerTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const targetSlug = this.getAttribute('data-target-tab');
                
                // Active button toggle
                explorerTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Panel toggle with smooth fade
                explorerPanels.forEach(panel => {
                    if (panel.getAttribute('id') === 'panel-' + targetSlug) {
                        panel.style.display = 'block';
                        panel.style.opacity = '0';
                        panel.style.transform = 'translateY(12px)';
                        setTimeout(() => {
                            panel.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            panel.style.opacity = '1';
                            panel.style.transform = 'translateY(0)';
                        }, 20);
                    } else {
                        panel.style.display = 'none';
                    }
                });
            });
        });
    }

    // =========================================================================
    // 4. Hero Live Simulator Micro-interaction
    // =========================================================================
    const simCases = document.querySelectorAll('.simulator-case-item');
    const simBadge = document.getElementById('hero-sim-status');
    const simDetails = document.getElementById('hero-sim-details');

    if (simCases.length > 0 && simBadge && simDetails) {
        simCases.forEach(item => {
            item.addEventListener('mouseenter', function () {
                simCases.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                
                const serviceName = this.getAttribute('data-service') || 'Layanan';
                const estTime = this.getAttribute('data-time') || '25 menit';
                const techName = this.getAttribute('data-tech') || 'Teknisi Siaga';

                simBadge.innerHTML = `<span class="pulse-dot me-1"></span> Teknisi Ditemukan (${techName})`;
                simDetails.textContent = `${serviceName} • Estimasi Tiba di Rumah: ~${estTime}`;
            });
        });
    }

    // =========================================================================
    // 5. Dynamic Apple Glossy Navbar & High-Precision Scrollspy
    // =========================================================================
    const landingSections = ['beranda', 'kategori-layanan', 'cara-kerja', 'keunggulan', 'bantuan'];
    let isClickScrolling = false;
    let clickScrollTimer = null;

    function setActiveNav(sectionId) {
        const navLinks = document.querySelectorAll('.landing-nav-item, #mainNavMenu .nav-link');
        if (!navLinks.length) return;
        navLinks.forEach(link => {
            const sec = link.getAttribute('data-nav-section') || (link.getAttribute('href') ? link.getAttribute('href').split('#')[1] : null);
            if (sec === sectionId) {
                if (!link.classList.contains('active')) {
                    link.classList.add('active');
                }
            } else {
                link.classList.remove('active');
            }
        });
    }

    // Smooth Scroll Helper with Navbar Height Compensation
    function scrollToSection(targetSectionId) {
        const targetElement = document.getElementById(targetSectionId);
        if (!targetElement) return;

        isClickScrolling = true;
        clearTimeout(clickScrollTimer);

        // Immediate active visual update with Apple pop animation
        setActiveNav(targetSectionId);

        const navHeight = document.querySelector('.main-navbar')?.offsetHeight || 75;
        let topPos = 0;
        if (targetSectionId !== 'beranda') {
            topPos = targetElement.getBoundingClientRect().top + window.pageYOffset - navHeight - 12;
        }

        window.scrollTo({
            top: topPos,
            behavior: 'smooth'
        });

        // Clean URL hash update without hard jump
        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', '#' + targetSectionId);
        }

        // Close mobile navbar collapse if open
        const navbarCollapse = document.getElementById('navbarMain');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            try {
                if (window.bootstrap && bootstrap.Collapse) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                } else {
                    navbarCollapse.classList.remove('show');
                }
            } catch (err) {}
        }

        // Re-enable scrollspy after smooth scroll settles
        clickScrollTimer = setTimeout(() => {
            isClickScrolling = false;
        }, 850);
    }

    // Intercept clicks on navbar links and landing page internal anchors
    document.querySelectorAll('.landing-nav-item, a[href*="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href) return;

            // Extract hash part
            const hashIndex = href.indexOf('#');
            if (hashIndex === -1) return;

            const targetId = href.substring(hashIndex + 1);
            if (!targetId) return;

            // Check if target is a section on this landing page
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                e.preventDefault();
                scrollToSection(targetId);
            }
        });
    });

    // Scrollspy to dynamically follow page position during manual scroll
    function handleScrollSpy() {
        if (isClickScrolling) return;

        const scrollY = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const bodyHeight = document.body.offsetHeight;

        // Top of page: Beranda
        if (scrollY < 160) {
            setActiveNav('beranda');
            return;
        }

        // Bottom of page: Bantuan (FAQ)
        if (scrollY + windowHeight >= bodyHeight - 70) {
            setActiveNav('bantuan');
            return;
        }

        const navHeight = document.querySelector('.main-navbar')?.offsetHeight || 75;
        const detectionPoint = scrollY + navHeight + (windowHeight * 0.28);

        // Find current section in view (check from bottom to top)
        for (let i = landingSections.length - 1; i >= 0; i--) {
            const secId = landingSections[i];
            const el = document.getElementById(secId);
            if (el) {
                if (detectionPoint >= el.offsetTop) {
                    setActiveNav(secId);
                    break;
                }
            }
        }
    }

    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                handleScrollSpy();
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    // Handle hash on initial page load (e.g. user opens localhost/lat1c/public/#kategori-layanan)
    if (window.location.hash) {
        const initialHash = window.location.hash.replace('#', '');
        if (landingSections.includes(initialHash)) {
            setActiveNav(initialHash);
            setTimeout(() => {
                scrollToSection(initialHash);
            }, 150);
        }
    }

    // =========================================================================
    // 6. Compact FAQ Hover & Touch Reveal Engine
    // =========================================================================
    const faqCards = document.querySelectorAll('.faq-hover-card');
    if (faqCards.length > 0) {
        faqCards.forEach(card => {
            // Desktop hover
            card.addEventListener('mouseenter', function () {
                this.classList.add('is-active');
            });
            card.addEventListener('mouseleave', function () {
                this.classList.remove('is-active');
            });

            // Touch / click toggle fallback
            card.addEventListener('click', function () {
                const alreadyActive = this.classList.contains('is-active');
                faqCards.forEach(c => c.classList.remove('is-active'));
                if (!alreadyActive) {
                    this.classList.add('is-active');
                }
            });
        });
    }

    // =========================================================================
    // 7. Hero Interactive 3D Mouse Parallax Physics
    // =========================================================================
    const heroSection = document.getElementById('beranda');
    const heroBg = document.querySelector('.fluent-hero-bg');
    const heroContainer = document.querySelector('.fluent-hero .container');

    if (heroSection && heroBg && heroContainer && window.matchMedia('(hover: hover)').matches) {
        let mouseX = 0, mouseY = 0;
        let targetX = 0, targetY = 0;
        let isHovering = false;

        heroSection.addEventListener('mouseenter', () => { isHovering = true; });
        heroSection.addEventListener('mouseleave', () => {
            isHovering = false;
            targetX = 0;
            targetY = 0;
        });

        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5; // -0.5 to 0.5
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            targetX = x;
            targetY = y;
        });

        function renderHeroParallax() {
            mouseX += (targetX - mouseX) * 0.08;
            mouseY += (targetY - mouseY) * 0.08;

            if (Math.abs(mouseX) > 0.0005 || Math.abs(mouseY) > 0.0005 || isHovering) {
                heroBg.style.transform = `translate(${mouseX * -18}px, ${mouseY * -14}px) scale(1.025)`;
                heroContainer.style.transform = `translate(${mouseX * 10}px, ${mouseY * 8}px)`;
            } else {
                heroBg.style.transform = '';
                heroContainer.style.transform = '';
            }

            requestAnimationFrame(renderHeroParallax);
        }
        renderHeroParallax();
    }

    // =========================================================================
    // 8. Interactive 3D Tilt for Service & Stepper Cards
    // =========================================================================
    const tiltCards = document.querySelectorAll('.ms-card, .fluent-step-card');
    if (tiltCards.length > 0 && window.matchMedia('(hover: hover)').matches) {
        tiltCards.forEach(card => {
            card.addEventListener('mousemove', function (e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = ((y - centerY) / centerY) * -5;
                const rotateY = ((x - centerX) / centerX) * 5;

                this.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-8px) scale3d(1.015, 1.015, 1.015)`;
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = '';
            });
        });
    }

    // =========================================================================
    // 9. Tactile Button Click Ripple Wave Effect
    // =========================================================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-fluent-primary, .btn-fluent-secondary, .btn-ms-primary');
        if (!btn) return;

        const rect = btn.getBoundingClientRect();
        const circle = document.createElement('span');
        const diameter = Math.max(rect.width, rect.height);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${e.clientX - rect.left - radius}px`;
        circle.style.top = `${e.clientY - rect.top - radius}px`;
        circle.className = 'fluent-ripple-wave';

        const existingRipple = btn.querySelector('.fluent-ripple-wave');
        if (existingRipple) {
            existingRipple.remove();
        }

        btn.appendChild(circle);
        setTimeout(() => {
            circle.remove();
        }, 850);
    });

    console.info('BANTUIN Landing Engine: Ready & Gacor Motion Activated.');
});

