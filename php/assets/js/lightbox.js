(function () {
  function init() {
    var overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = '<button type="button" class="lightbox-close" aria-label="Close">&times;</button><img src="" alt="">';
    document.body.appendChild(overlay);
    var overlayImg = overlay.querySelector('img');

    function open(src, alt) {
      overlayImg.src = src;
      overlayImg.alt = alt || '';
      overlay.classList.add('is-open');
    }
    function close() {
      overlay.classList.remove('is-open');
      overlayImg.src = '';
    }

    overlay.addEventListener('click', close);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });

    var skip = 'header, footer, .topbar, .lightbox-overlay';
    document.querySelectorAll('main img, section img, .container img').forEach(function (img) {
      if (img.closest(skip)) return;
      img.classList.add('lightbox-zoomable');
      img.addEventListener('click', function (e) {
        e.stopPropagation();
        open(img.currentSrc || img.src, img.alt);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
