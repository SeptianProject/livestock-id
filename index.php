<?php

declare(strict_types=1);

?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LivestockID - Platform Manajemen Peternakan Modern</title>
    <meta name="description" content="Platform manajemen peternakan modern berbasis data. Pantau kesehatan dan produksi ternak secara realtime dengan LivestockID." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php include __DIR__ . '/sections/navbar.html'; ?>
    <?php include __DIR__ . '/sections/hero.html'; ?>
    <?php include __DIR__ . '/sections/partners.html'; ?>
    <?php include __DIR__ . '/sections/features.html'; ?>
    <?php include __DIR__ . '/sections/dashboard.html'; ?>
    <?php include __DIR__ . '/sections/stats.html'; ?>
    <?php include __DIR__ . '/sections/benefits.html'; ?>
    <?php include __DIR__ . '/sections/testimonials.html'; ?>
    <?php include __DIR__ . '/sections/faq.html'; ?>
    <?php include __DIR__ . '/sections/cta.html'; ?>
    <?php include __DIR__ . '/sections/footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function initNavbar() {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;
            window.addEventListener("scroll", () => {
                navbar.classList.toggle("ls-navbar--scrolled", window.scrollY > 60);
            });
        }

        function initActiveNav() {
            const sections = document.querySelectorAll("section[id]");
            const navLinks = document.querySelectorAll(".ls-nav-link");
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            navLinks.forEach((l) => l.classList.remove("active"));
                            const active = document.querySelector(`.ls-nav-link[href="#${entry.target.id}"]`);
                            if (active) active.classList.add("active");
                        }
                    });
                }, {
                    threshold: 0.4
                }
            );
            sections.forEach((s) => observer.observe(s));
        }

        function initFeatureAccordion() {
            document.querySelectorAll(".ls-acc-item").forEach((item) => {
                item.querySelector(".ls-acc-header").addEventListener("click", () => {
                    const isActive = item.classList.contains("active");
                    document.querySelectorAll(".ls-acc-item").forEach((i) => i.classList.remove("active"));
                    if (!isActive) {
                        item.classList.add("active");
                        const img = item.dataset.img;
                        const target = document.getElementById(item.dataset.target);
                        if (img && target) {
                            target.style.opacity = "0";
                            setTimeout(() => {
                                target.src = img;
                                target.style.opacity = "1";
                            }, 200);
                        }
                    }
                });
            });
        }

        function initDashboardTabs() {
            document.querySelectorAll(".ls-dash-tab").forEach((btn) => {
                btn.addEventListener("click", () => {
                    document.querySelectorAll(".ls-dash-tab").forEach((b) => b.classList.remove("active"));
                    document.querySelectorAll(".ls-tab-panel").forEach((p) => p.classList.remove("active"));
                    btn.classList.add("active");
                    document.getElementById(btn.dataset.tab).classList.add("active");
                });
            });
        }

        function initStatsCounter() {
            function animateCounter(el) {
                const target = parseInt(el.dataset.target, 10);
                const duration = 1800;
                const step = Math.ceil(target / (duration / 16));
                let current = 0;
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current.toLocaleString("id-ID");
                    if (current >= target) clearInterval(timer);
                }, 16);
            }

            const statsObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.querySelectorAll(".ls-stat-number[data-target]").forEach(animateCounter);
                            statsObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.5
                }
            );

            const statsSection = document.querySelector(".ls-stats");
            if (statsSection) statsObserver.observe(statsSection);
        }

        function initSmoothScroll() {
            document.querySelectorAll('a[href^="#"]').forEach((a) => {
                a.addEventListener("click", (e) => {
                    const id = a.getAttribute("href").slice(1);
                    const target = document.getElementById(id);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: "smooth",
                            block: "start"
                        });
                    }
                });
            });
        }

        initNavbar();
        initActiveNav();
        initFeatureAccordion();
        initDashboardTabs();
        initStatsCounter();
        initSmoothScroll();
    </script>
</body>

</html>