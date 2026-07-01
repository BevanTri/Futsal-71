document.addEventListener('DOMContentLoaded', function () {
  // Alert auto-dismiss with fade
  document.querySelectorAll('.alert-futsal').forEach(function (el) {
    if (!el.classList.contains('alert-permanent')) {
      setTimeout(function () {
        el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(function () { el.remove(); }, 300);
      }, 5000);
    }
  });

  // Confirm delete
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // Active nav link
  var currentPath = window.location.pathname.replace(/\/+$/, '');
  document.querySelectorAll('.navbar-futsal .nav-link').forEach(function (link) {
    var linkPath = link.getAttribute('href').replace(/\/+$/, '');
    if (currentPath === linkPath || currentPath + '/' === linkPath) {
      link.classList.add('active');
    }
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
});

function formatRupiah(a) { return 'Rp ' + a.toLocaleString('id-ID'); }
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }); }
function copyToClipboard(t) { navigator.clipboard.writeText(t).catch(function (e) { console.error(e); }); }
