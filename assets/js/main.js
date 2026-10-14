/* ═══════ NexShop JS — انیمیشن، منوی موبایل و تعامل ═══════ */
(function () {
  'use strict';

  // سایه هدر هنگام اسکرول
  const header = document.getElementById('nxHeader');
  const toTop = document.getElementById('nxToTop');
  let ticking = false;
  const onScroll = () => {
    header && header.classList.toggle('is-scrolled', window.scrollY > 10);
    toTop && toTop.classList.toggle('is-visible', window.scrollY > 500);
    ticking = false;
  };
  window.addEventListener('scroll', () => {
    if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
  }, { passive: true });
  onScroll();

  // بازگشت به بالا
  toTop && toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ── منوی موبایل — دسترس‌پذیر ──
     باز/بسته با کلیک، بستن با ESC، بستن با کلیک روی لینک،
     بستن با کلیک بیرون از هدر، قفل فوکوس داخل هدر هنگام باز بودن */
  const burger = document.getElementById('nxBurger');
  const nav = document.getElementById('nxNav');

  const isMenuOpen = () => !!(nav && nav.classList.contains('is-open'));

  function setMenu(open) {
    if (!nav || !burger) return;
    nav.classList.toggle('is-open', open);
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  if (burger && nav) {
    burger.addEventListener('click', () => setMenu(!isMenuOpen()));

    // بستن با کلیک روی لینک‌های منو
    nav.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) setMenu(false);
    });

    // بستن با ESC و بازگرداندن فوکوس به دکمهٔ منو
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isMenuOpen()) {
        setMenu(false);
        burger.focus();
      }
    });

    // بستن با کلیک بیرون از هدر
    document.addEventListener('pointerdown', (e) => {
      if (isMenuOpen() && header && !header.contains(e.target)) setMenu(false);
    });

    // قفل فوکوس: اگر فوکوس از هدر خارج شد، به دکمهٔ منو برگردد
    document.addEventListener('focusin', (e) => {
      if (isMenuOpen() && header && !header.contains(e.target)) burger.focus();
    });
  }

  // انیمیشن ورود بخش‌ها (IntersectionObserver)؛ اگر پشتیبانی نشود، همه‌چیز بلافاصله نمایش داده می‌شود
  const items = document.querySelectorAll('.nx-reveal');
  if (!('IntersectionObserver' in window)) {
    items.forEach((el) => el.classList.add('nx-on'));
  } else {
    const io = new IntersectionObserver(
      (entries) => entries.forEach((e) => {
        if (e.isIntersecting) { e.target.classList.add('nx-on'); io.unobserve(e.target); }
      }),
      { threshold: 0.12 }
    );
    items.forEach((el) => io.observe(el));

    // بازنشانی انیمیشن کارت‌های ووکامرس بعد از AJAX (مثل فیلتر قیمت)
    if (window.jQuery) {
      jQuery(document.body).on('updated_wc_div', () => {
        document.querySelectorAll('.nx-reveal:not(.nx-on)').forEach((el) => io.observe(el));
      });
    }
  }
})();
