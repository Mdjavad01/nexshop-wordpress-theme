/* ═══════ NexShop JS — انیمیشن و تعامل ═══════ */
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

  // منوی موبایل (با aria-expanded برای دسترس‌پذیری)
  const burger = document.getElementById('nxBurger');
  const nav = document.getElementById('nxNav');
  burger && burger.addEventListener('click', () => {
    const open = nav ? nav.classList.toggle('is-open') : false;
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  // انیمیشن ورود بخش‌ها (IntersectionObserver)؛ اگر پشتیبانی نشود، همه‌چیز بلافاصله نمایش داده می‌شود
  const items = document.querySelectorAll('.nx-reveal');
  if (!('IntersectionObserver' in window)) {
    items.forEach((el) => el.classList.add('nx-on'));
    return;
  }
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
})();
