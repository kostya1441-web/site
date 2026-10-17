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

  /* ---------- карточка позиции во весь экран ---------- */
  var GAUGE = '<svg width="42" height="42" viewBox="0 0 42 42" aria-hidden="true">' +
    '<circle cx="21" cy="21" r="18" fill="none" stroke="#3d434c" stroke-width="2"/>' +
    '<circle cx="21" cy="21" r="18" fill="none" stroke="#64b519" stroke-width="2" stroke-dasharray="70 200"/>' +
    '<circle cx="21" cy="21" r="2.5" fill="#64b519"/></svg>';

  var current = null;
  var photoIndex = 0;
  var pushedState = false;

  function productBySlug(slug) {
    for (var i = 0; i < PRODUCTS.length; i++) {
      if (PRODUCTS[i].slug === slug) return PRODUCTS[i];
    }
    return null;
  }

  function productViewHtml(product) {
    var back = product.categorySlug
      ? (PAGE.catalogUrl || "catalog.php") + "?cat=" + encodeURIComponent(product.categorySlug)
      : (PAGE.catalogUrl || "catalog.php");
    var specs = (product.specs || []).map(function (row) {
      return '<div class="spec' + (row.k ? "" : " solo") + '">' +
        (row.k ? '<span class="k">' + esc(row.k) + "</span>" : "") +
        '<span class="v">' + esc(row.v) + "</span></div>";
    }).join("");

    return '<div class="prod-top">' +
        '<a class="prod-back" href="' + esc(back) + '">‹ ' + esc(product.category || "В каталог") + "</a>" +
        '<button type="button" class="prod-close" id="sr-prod-close" aria-label="Закрыть">×</button>' +
      "</div>" +
      '<div class="wrap prod-wrap">' +
        '<div class="prod-gallery">' +
          '<div class="prod-photo" id="sr-prod-photo"></div>' +
          '<div class="prod-thumbs" id="sr-prod-thumbs"></div>' +
        "</div>" +
        '<div class="prod-info">' +
          '<div class="top">' +
            (product.code ? '<span class="code">' + esc(product.code) + "</span>" : "") +
            (product.category ? '<span class="cat-tag">' + esc(product.category) + "</span>" : "") +
          "</div>" +
          '<h2 class="prod-title">' + esc(product.name) + "</h2>" +
          (specs
            ? '<div class="prod-block"><div class="prod-block-head">Характеристики</div>' +
              '<div class="prod-specs">' + specs + "</div></div>"
            : "") +
          (product.text
            ? '<div class="prod-block"><div class="prod-block-head">Описание</div>' +
              '<div class="prod-text">' + esc(product.text).replace(/\n/g, "<br>") + "</div></div>"
            : "") +
          '<div class="prod-cta">' +
            '<div class="prod-cta-text">' + esc(PAGE.catalogCta || "Уточнить наличие") + "</div>" +
            '<div class="prod-cta-row">' +
              '<a class="btn" href="tel:' + esc(PAGE.phoneHref || "") + '">' + esc(PAGE.phone || "Позвонить") + "</a>" +
              '<a class="btn-ghost" href="mailto:' + esc(PAGE.email || "") + '">Написать на почту</a>' +
            "</div>" +
          "</div>" +
        "</div>" +
      "</div>";
  }

  function paintGallery() {
    var photo = $("sr-prod-photo");
    var thumbs = $("sr-prod-thumbs");
    if (!photo || !thumbs || !current) return;

    var images = current.images || [];
    if (!images.length) {
      photo.innerHTML = '<div class="ph-empty">' + GAUGE + "<span>Фото не добавлено</span></div>";
      thumbs.innerHTML = "";
      return;
    }
    if (photoIndex >= images.length) photoIndex = 0;
    if (photoIndex < 0) photoIndex = images.length - 1;

    photo.innerHTML = '<img src="' + esc(images[photoIndex]) + '" alt="' + esc(current.name) + '">' +
      (images.length > 1
        ? '<button class="prod-nav prev" data-step="-1" aria-label="Предыдущее фото">‹</button>' +
          '<button class="prod-nav next" data-step="1" aria-label="Следующее фото">›</button>' +
          '<span class="prod-counter">' + (photoIndex + 1) + " / " + images.length + "</span>"
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

  function openProduct(slug, fromHistory) {
    var product = productBySlug(slug);
    if (!product) return false;

    current = product;
    photoIndex = 0;

    var view = $("sr-prod-view");
    if (view) view.innerHTML = productViewHtml(product);
    paintGallery();

    var closeBtn = $("sr-prod-close");
    if (closeBtn) closeBtn.onclick = function () { closeProduct(); };

    var bg = $("sr-prod-bg");
    if (bg) bg.classList.add("show");
    syncLock();
    window.scrollTo(0, 0);

    if (!fromHistory) rememberUrl(slug);
    return true;
  }

  function rememberUrl(slug) {
    if (!window.history || !history.pushState) return;
    try {
      var url = new URL(window.location.href);
      url.searchParams.set("prod", slug);
      history.pushState({ srProduct: slug }, "", url.toString());
      pushedState = true;
    } catch (e) { /* адрес не меняем */ }
  }

  function hideProduct() {
    var bg = $("sr-prod-bg");
    if (bg) bg.classList.remove("show");
    current = null;
    syncLock();
  }

  function closeProduct() {
    if (pushedState && window.history) {
      pushedState = false;
      history.back();
      return;
    }
    hideProduct();
    if (window.history && history.replaceState) {
      try {
        var url = new URL(window.location.href);
        url.searchParams.delete("prod");
        history.replaceState(null, "", url.toString());
      } catch (e) { /* адрес не меняем */ }
    }
  }

  window.addEventListener("popstate", function () {
    var slug = "";
    try {
      slug = new URL(window.location.href).searchParams.get("prod") || "";
    } catch (e) { slug = ""; }
    pushedState = false;
    if (slug && productBySlug(slug)) {
      openProduct(slug, true);
    } else {
      hideProduct();
    }
  });

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
    var open = bg && bg.classList.contains("show");
    if (open && current && (current.images || []).length > 1) {
      if (e.key === "ArrowLeft") { photoIndex--; paintGallery(); }
      if (e.key === "ArrowRight") { photoIndex++; paintGallery(); }
    }
    if (e.key === "Escape") {
      megaClose();
      if (open) closeProduct();
      closeOverlay("sr-login-bg");
    }
  });

  var serverRenderedClose = $("sr-prod-close");
  if (serverRenderedClose) serverRenderedClose.onclick = function () { closeProduct(); };

  bindMega();
  bindProductLinks();

  // Позиция открыта по прямой ссылке: страница уже отрисована сервером,
  // новую запись в истории добавлять не нужно.
  if (PAGE.openProduct) {
    openProduct(PAGE.openProduct, true);
  }

  /* Общие помощники для панели администратора. */
  window.SR = { toast: toast, syncLock: syncLock, esc: esc };
})();
