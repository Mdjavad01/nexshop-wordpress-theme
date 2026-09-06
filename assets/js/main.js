/* ═══════ NexShop JS — انیمیشن و تعامل ═══════ */
(function () {
  'use strict';

  // سایه هدر هنگام اسکرول
  const header = document.getElementById('nxHeader');
  const toTop = document.getElementById('nxToTop');
  const onScroll = () => {
    header && header.classList.toggle('is-scrolled', window.scrollY > 10);
    toTop && toTop.classList.toggle('is-visible', window.scrollY > 500);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // بازگشت به بالا
  toTop && toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  // منوی موبایل
  const burger = document.getElementById('nxBurger');
  const nav = document.getElementById('nxNav');
  burger && burger.addEventListener('click', () => nav && nav.classList.toggle('is-open'));

  // انیمیشن ورود بخش‌ها (IntersectionObserver)
  const io = new IntersectionObserver(
    (entries) => entries.forEach((e) => e.isIntersecting && e.target.classList.add('nx-on')),
    { threshold: 0.12 }
  );
  document.querySelectorAll('.nx-reveal').forEach((el) => io.observe(el));

  // بازنشانی انیمیشن کارت‌های ووکامرس بعد از AJAX (مثل فیلتر قیمت)
  if (window.jQuery) {
    jQuery(document.body).on('updated_wc_div', () => {
      document.querySelectorAll('.nx-reveal:not(.nx-on)').forEach((el) => io.observe(el));
    });
  }
})();
