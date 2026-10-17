/* Витрина: меню каталога, карточка позиции с галереей. */
(function () {
  "use strict";

  var PAGE = {};
  try {
    PAGE = JSON.parse(document.getElementById("sr-page-data").textContent) || {};
  } catch (e) {
    PAGE = {};
  }
  var PRODUCTS = PAGE.products || [];

  function $(id) { return document.getElementById(id); }
  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  /* ---------- уведомления и блокировка прокрутки ---------- */
  function toast(message) {
    var el = $("sr-toast");
    if (!el) return;
    el.textContent = message;
    el.classList.add("show");
    clearTimeout(el._hide);
    el._hide = setTimeout(function () { el.classList.remove("show"); }, 2800);
  }

  function syncLock() {
    var open = ["sr-prod-bg", "sr-login-bg", "sr-admin-drawer"].some(function (id) {
      var el = $(id);
      return el && el.classList.contains("show");
    });
    document.body.classList.toggle("sr-lock", open);
  }

  function closeOverlay(id) {
    var el = $(id);
    if (el) el.classList.remove("show");
    syncLock();
  }

  /* ---------- карточка позиции ---------- */
  var current = null;
  var photoIndex = 0;

  function productBySlug(slug) {
    for (var i = 0; i < PRODUCTS.length; i++) {
      if (PRODUCTS[i].slug === slug) return PRODUCTS[i];
    }
    return null;
  }

  function paintGallery() {
    var photo = $("sr-prod-photo");
    var thumbs = $("sr-prod-thumbs");
    if (!photo || !thumbs || !current) return;

    var images = current.images || [];
    if (!images.length) {
      photo.innerHTML = '<div class="ph-empty"><span>Фото не добавлено</span></div>';
      thumbs.innerHTML = "";
      return;
    }
    if (photoIndex >= images.length) photoIndex = 0;
    if (photoIndex < 0) photoIndex = images.length - 1;

    photo.innerHTML = '<img src="' + esc(images[photoIndex]) + '" alt="' + esc(current.name) + '">' +
      (images.length > 1
        ? '<button class="sr-prod-nav prev" data-step="-1" aria-label="Предыдущее фото">‹</button>' +
          '<button class="sr-prod-nav next" data-step="1" aria-label="Следующее фото">›</button>' +
          '<span class="sr-prod-counter">' + (photoIndex + 1) + " / " + images.length + "</span>"
        : "");
    photo.querySelectorAll("[data-step]").forEach(function (b) {
      b.onclick = function () { photoIndex += +b.getAttribute("data-step"); paintGallery(); };
    });

    thumbs.innerHTML = images.length > 1
      ? images.map(function (src, i) {
          return '<button class="' + (i === photoIndex ? "active" : "") + '" data-thumb="' + i + '">' +
            '<img src="' + esc(src) + '" alt=""></button>';
        }).join("")
      : "";
    thumbs.querySelectorAll("[data-thumb]").forEach(function (b) {
      b.onclick = function () { photoIndex = +b.getAttribute("data-thumb"); paintGallery(); };
    });
  }

  function openProduct(slug) {
    var product = productBySlug(slug);
    if (!product) return false;

    current = product;
    photoIndex = 0;
    $("sr-prod-code").textContent = product.code || "—";
    $("sr-prod-cat").textContent = product.category || "";
    $("sr-prod-title").textContent = product.name;
    $("sr-prod-specs").innerHTML = (product.specs || []).map(function (x) {
      return "<li>" + esc(x) + "</li>";
    }).join("");
    $("sr-prod-text").textContent = product.text || "";
    paintGallery();

    var bg = $("sr-prod-bg");
    if (bg) bg.classList.add("show");
    syncLock();

    if (window.history && history.replaceState) {
      try {
        var url = new URL(window.location.href);
        url.searchParams.set("prod", slug);
        history.replaceState(null, "", url.toString());
      } catch (e) { /* адрес не меняем */ }
    }
    return true;
  }

  function closeProduct() {
    closeOverlay("sr-prod-bg");
    if (window.history && history.replaceState) {
      try {
        var url = new URL(window.location.href);
        url.searchParams.delete("prod");
        history.replaceState(null, "", url.toString());
      } catch (e) { /* адрес не меняем */ }
    }
  }

  function bindProductLinks() {
    document.querySelectorAll("[data-prod-slug]").forEach(function (link) {
      link.addEventListener("click", function (e) {
        var slug = link.getAttribute("data-prod-slug");
        if (!productBySlug(slug)) return; // позиции нет на этой странице — обычный переход
        e.preventDefault();
        megaClose();
        openProduct(slug);
      });
    });
  }

  /* ---------- выпадающее меню каталога ---------- */
  var lastPointerTouch = false;
  document.addEventListener("pointerdown", function (e) {
    if (e && e.pointerType) lastPointerTouch = e.pointerType !== "mouse";
  }, true);

  function hoverMode() {
    if (lastPointerTouch) return false;
    try {
      return !!(window.matchMedia && window.matchMedia("(hover: hover) and (pointer: fine)").matches);
    } catch (e) {
      return true;
    }
  }

  function megaOpen() {
    var mega = $("sr-mega"), btn = $("sr-mega-btn");
    if (!mega) return;
    mega.classList.add("show");
    document.body.classList.add("sr-mega-open");
    if (btn) btn.setAttribute("aria-expanded", "true");
  }

  function megaClose() {
    var mega = $("sr-mega"), btn = $("sr-mega-btn");
    if (!mega) return;
    mega.classList.remove("show");
    document.body.classList.remove("sr-mega-open");
    if (btn) btn.setAttribute("aria-expanded", "false");
  }

  function selectMegaCategory(id) {
    document.querySelectorAll("[data-mega-cat]").forEach(function (el) {
      el.classList.toggle("active", el.getAttribute("data-mega-cat") === id);
    });
    document.querySelectorAll("[data-mega-group]").forEach(function (el) {
      el.classList.toggle("active", el.getAttribute("data-mega-group") === id);
    });
  }

  function bindMega() {
    var btn = $("sr-mega-btn"), mega = $("sr-mega"), header = $("sr-header");
    if (!btn || !mega || !header) return;

    btn.addEventListener("click", function () {
      if (!hoverMode()) {
        if (mega.classList.contains("show")) megaClose(); else megaOpen();
        return;
      }
      window.location.href = "catalog.php";
    });
    btn.addEventListener("mouseenter", function () { if (hoverMode()) megaOpen(); });
    btn.addEventListener("focus", function () { if (hoverMode()) megaOpen(); });
    header.addEventListener("mouseleave", function () { if (hoverMode()) megaClose(); });

    document.querySelectorAll("[data-mega-cat]").forEach(function (link) {
      var id = link.getAttribute("data-mega-cat");
      link.addEventListener("mouseenter", function () { if (hoverMode()) selectMegaCategory(id); });
      link.addEventListener("focus", function () { selectMegaCategory(id); });
      link.addEventListener("click", function (e) {
        if (hoverMode()) return; // обычный переход в категорию
        e.preventDefault();
        selectMegaCategory(id);
      });
    });

    document.addEventListener("click", function (e) {
      if (!mega.classList.contains("show")) return;
      if (mega.contains(e.target) || btn.contains(e.target)) return;
      megaClose();
    });
  }

  /* ---------- общие обработчики ---------- */
  document.addEventListener("keydown", function (e) {
    var bg = $("sr-prod-bg");
    if (e.key === "ArrowLeft" && bg && bg.classList.contains("show") && current && (current.images || []).length > 1) {
      photoIndex--; paintGallery();
    }
    if (e.key === "ArrowRight" && bg && bg.classList.contains("show") && current && (current.images || []).length > 1) {
      photoIndex++; paintGallery();
    }
    if (e.key === "Escape") {
      megaClose();
      if (bg && bg.classList.contains("show")) closeProduct();
      closeOverlay("sr-login-bg");
    }
  });

  var closeBtn = $("sr-prod-close");
  if (closeBtn) closeBtn.addEventListener("click", closeProduct);

  var prodBg = $("sr-prod-bg");
  if (prodBg) {
    prodBg.addEventListener("click", function (e) { if (e.target === this) closeProduct(); });
  }

  var prodCta = $("sr-prod-cta");
  if (prodCta) prodCta.addEventListener("click", function () { closeOverlay("sr-prod-bg"); });

  bindMega();
  bindProductLinks();

  if (PAGE.openProduct) {
    openProduct(PAGE.openProduct);
  }

  /* Общие помощники для панели администратора. */
  window.SR = { toast: toast, syncLock: syncLock, esc: esc };
})();
