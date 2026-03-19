/**
 * Logika Interaktif Portal SISE2026 BPS Kabupaten Jember
 * Navigation, animations, search, mobile menu
 */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Typewriter Effect
    const typewriterElement = document.getElementById('typewriter');
    if (typewriterElement) {
        const text = "Membangun Masa Depan Ekonomi Jember.";
        let index = 0;
        function typeWriter() {
            if (index < text.length) {
                typewriterElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeWriter, 70);
            }
        }
        setTimeout(typeWriter, 500);
    }

    // 2. 3D Parallax Card
    const heroCard = document.querySelector('.parallax-card');
    if (heroCard) {
        document.addEventListener('mousemove', (e) => {
            const { clientX, clientY } = e;
            const { innerWidth, innerHeight } = window;
            const rotateY = (innerWidth / 2 - clientX) / 50;
            const rotateX = (innerHeight / 2 - clientY) / 50;
            heroCard.style.transform = `rotateY(${rotateY}deg) rotateX(${-rotateX}deg) translateY(-10px)`;
        });
        document.addEventListener('mouseleave', () => {
            heroCard.style.transform = 'rotateY(0deg) rotateX(0deg) translateY(0px)';
        });
    }

    // 3. Scroll Reveal (Intersection Observer)
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // 4. Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenuContent = document.getElementById('mobile-menu-content');
    if (mobileMenuBtn && mobileMenuContent) {
        mobileMenuBtn.addEventListener('click', () => {
            const isOpen = !mobileMenuContent.classList.contains('hidden');
            if (isOpen) {
                mobileMenuContent.classList.add('hidden');
                mobileMenuBtn.querySelector('i').classList.replace('fa-times', 'fa-bars');
            } else {
                mobileMenuContent.classList.remove('hidden');
                mobileMenuBtn.querySelector('i').classList.replace('fa-bars', 'fa-times');
            }
        });
    }

    // 5. Mobile Accordion Submenus
    document.querySelectorAll('.mobile-acc-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const panel = btn.nextElementSibling;
            const icon = btn.querySelector('.fa-chevron-down');
            if (panel) {
                panel.classList.toggle('hidden');
                if (icon) icon.classList.toggle('rotate-180');
            }
        });
    });

    // 6. Client-side Search Filter (Universal)
    const searchInput = document.getElementById('search-lowongan');
    const cards = document.querySelectorAll('.lowongan-card');
    if (searchInput && cards.length) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            cards.forEach(card => {
                const title = card.querySelector('h3')?.textContent?.toLowerCase() || '';
                const location = card.querySelector('.location')?.textContent?.toLowerCase() || '';
                card.classList.toggle('hidden', !(title.includes(query) || location.includes(query)));
            });
        });
    }

    // 7. File Upload Zone Interactions
    document.querySelectorAll('[class*="border-dashed"]').forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        if (input) {
            zone.addEventListener('click', () => input.click());
            input.addEventListener('change', () => {
                if (input.files.length) {
                    const icon = zone.querySelector('i');
                    const label = zone.querySelector('p:first-of-type');
                    if (icon) icon.className = 'fas fa-check-circle text-2xl text-green-500 mb-2';
                    if (label) label.textContent = input.files[0].name;
                    zone.classList.add('border-green-300', 'bg-green-50');
                    zone.classList.remove('border-slate-200');
                }
            });
        }
    });

    // 8. Auto-dismiss flash messages
    const flashMsg = document.getElementById('flash-msg');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.opacity = '0';
            flashMsg.style.transform = 'translateX(100%)';
            setTimeout(() => flashMsg.remove(), 300);
        }, 4000);
    }

    console.log("SISE2026 Jember Portal: Initialized ✓");
});