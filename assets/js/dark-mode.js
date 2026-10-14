/* ═══════ NexShop Dark/Light Mode ═══════
   حالت اولیه توسط اسکریپت بوتِ inline در <head> ست شده است (بدون فلش).
   این فایل فقط سوییچ دستی و همگام‌سازی با سیستم را مدیریت می‌کند. */
(function () {
  'use strict';

  var KEY = 'nexshop_scheme';
  var root = document.documentElement;
  var btn = document.getElementById('nxThemeToggle');

  function current() {
    return root.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
  }

  function apply(theme) {
    root.setAttribute('data-theme', theme);
    if (btn) {
      btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      var label = btn.getAttribute('data-label-' + (theme === 'dark' ? 'light' : 'dark'));
      if (label) btn.setAttribute('aria-label', label);
    }
  }

  if (btn) {
    apply(current());
    btn.addEventListener('click', function () {
      var next = current() === 'dark' ? 'light' : 'dark';
      apply(next);
      try { localStorage.setItem(KEY, next); } catch (e) { /* حالت خصوصی مرورگر */ }
      // کلاس موقت برای انتقال نرم رنگ‌ها (تعریف‌شده در style.css)
      root.classList.add('nx-theme-switched');
      setTimeout(function () { root.classList.remove('nx-theme-switched'); }, 400);
      // رویداد عمومی برای ویجت‌های المنتور / اسکریپت‌های سفارشی
      window.dispatchEvent(new CustomEvent('nexshop:scheme-changed', { detail: { theme: next } }));
    });
  }

  // اگر کاربر انتخاب دستی نداشته باشد، تغییر تم سیستم به‌صورت زنده اعمال می‌شود
  var stored = null;
  try { stored = localStorage.getItem(KEY); } catch (e) {}
  var mq = window.matchMedia ? window.matchMedia('(prefers-color-scheme: light)') : null;
  if (mq && mq.addEventListener && !stored) {
    mq.addEventListener('change', function (e) { apply(e.matches ? 'light' : 'dark'); });
  }
})();
