/* Панель администратора: читает и сохраняет контент через api.php. */
(function () {
  "use strict";

  var PAGE = {};
  try {
    PAGE = JSON.parse(document.getElementById("sr-page-data").textContent) || {};
  } catch (e) {
    PAGE = {};
  }

  var state = null;
  var csrf = "";
  var unlocked = !!PAGE.isAdmin;
  var dirty = false;

  function $(id) { return document.getElementById(id); }
  function esc(s) { return window.SR ? window.SR.esc(s) : String(s == null ? "" : s); }
  function toast(m) { if (window.SR) window.SR.toast(m); }
  function lock() { if (window.SR) window.SR.syncLock(); }

  var SETTING_FIELDS = [
    ["Шапка сайта", [
      ["brandName", "Название компании"],
      ["tagline", "Слоган"],
      ["phone", "Телефон"],
      ["email", "Email"],
      ["city", "Город / адрес"]
    ]],
    ["Главный экран", [
      ["badge", "Бейдж"],
      ["h1a", "Заголовок, строка 1"],
      ["h1b", "Заголовок, строка 2"],
      ["heroText", "Описание", true],
      ["heroCta", "Текст кнопки"]
    ]],
    ["Полоса под главным экраном", [
      ["yearsNum", "Короткая надпись"],
      ["yearsText", "Текст", true],
      ["yearsCta", "Текст кнопки"]
    ]],
    ["Каталог — заголовки и кнопки", [
      ["catalogEyebrow", "Надпись сверху"],
      ["catalogTitle", "Заголовок"],
      ["catalogAllLabel", "Название вкладки «все»"],
      ["catalogAllCta", "Кнопка «весь каталог» на главной"],
      ["catalogPageLead", "Подзаголовок страницы каталога", true],
      ["megaTitle", "Заголовок выпадающего меню"],
      ["catalogMoreCta", "Кнопка на карточке"],
      ["catalogCta", "Кнопка в карточке позиции"]
    ]],
    ["О компании", [
      ["aboutEyebrow", "Надпись сверху"],
      ["aboutTitle", "Заголовок"],
      ["aboutText", "Текст", true]
    ]],
    ["Контакты и подвал", [
      ["mapAddress", "Адрес в блоке контактов"],
      ["requisites", "Реквизиты"],
      ["copyright", "Копирайт"]
    ]]
  ];

  /* ---------- запросы к серверу ---------- */
  function api(action, options) {
    options = options || {};
    var init = { method: options.method || "GET", credentials: "same-origin", headers: {} };
    if (csrf) init.headers["X-CSRF-Token"] = csrf;
    if (options.json) {
      init.headers["Content-Type"] = "application/json";
      init.body = JSON.stringify(options.json);
    }
    if (options.body) init.body = options.body;
    return fetch("api.php?action=" + action, init).then(function (response) {
      return response.json().catch(function () {
        throw new Error("Сервер вернул не JSON. Проверьте, что сайт открыт по http, а не как файл");
      }).then(function (data) {
        if (!response.ok || !data.ok) throw new Error(data.error || "Ошибка запроса");
        return data;
      });
    });
  }

  function loadState() {
    return api("state").then(function (data) {
      csrf = data.csrf;
      state = data.content;
      state.categories.forEach(function (c) { c.id = +c.id; });
      state.products.forEach(function (p) {
        p.id = +p.id;
        p.category_id = +p.category_id;
        p.images = p.images || [];
      });
      dirty = false;
      return state;
    });
  }

  /* ---------- вход ---------- */
  function openAdmin() {
    if (unlocked) {
      (state ? Promise.resolve(state) : loadState())
        .then(openDrawer)
        .catch(function (err) { toast(err.message); });
      return;
    }
    var bg = $("sr-login-bg");
    if (!bg) return;
    bg.classList.add("show");
    lock();
    $("sr-login-err").style.display = "none";
    $("sr-login-pass").value = "";
    $("sr-login-pass").focus();
  }

  function submitLogin() {
    var pass = $("sr-login-pass").value;
    api("login", { method: "POST", json: { password: pass } }).then(function (data) {
      csrf = data.csrf;
      unlocked = true;
      $("sr-login-bg").classList.remove("show");
      return loadState().then(openDrawer);
    }).catch(function (err) {
      var box = $("sr-login-err");
      box.textContent = err.message;
      box.style.display = "block";
    });
  }

  function openDrawer() {
    var drawer = $("sr-admin-drawer");
    if (!drawer) return;
    drawer.classList.add("show");
    lock();
    renderBody();
  }

  function closeDrawer() {
    var drawer = $("sr-admin-drawer");
    if (drawer) drawer.classList.remove("show");
    lock();
  }

  /* ---------- поля формы ---------- */
  function field(label, value, key, multiline) {
    return '<div class="sr-field"><label>' + esc(label) + "</label>" +
      (multiline
        ? '<textarea data-key="' + key + '">' + esc(value) + "</textarea>"
        : '<input data-key="' + key + '" value="' + esc(value) + '">') +
      "</div>";
  }

  function checkField(label, checked, key) {
    return '<div class="sr-field" style="display:flex;align-items:center;gap:9px;">' +
      '<input type="checkbox" data-check="' + key + '"' + (checked ? " checked" : "") + ' style="width:auto;">' +
      '<label style="margin:0;">' + esc(label) + "</label></div>";
  }

  function selectField(label, value, key, options) {
    var opts = options.map(function (o) {
      return '<option value="' + esc(o.value) + '"' + (String(o.value) === String(value) ? " selected" : "") + ">" +
        esc(o.label) + "</option>";
    }).join("");
    return '<div class="sr-field"><label>' + esc(label) + '</label><select data-key="' + key + '">' + opts + "</select></div>";
  }

  function galleryItems(product) {
    if (!product.images.length) return '<div class="sr-gal-empty">Фото не добавлены</div>';
    return product.images.map(function (src, i) {
      return '<div class="sr-galitem' + (i === 0 ? " main" : "") + '">' +
        '<img src="' + esc(src) + '" alt="">' +
        (i === 0
          ? '<span class="mainbadge">Главное</span>'
          : '<button type="button" class="star" data-gmain="' + i + '" title="Сделать главным">★</button>') +
        '<button type="button" class="del" data-gdel="' + i + '" title="Удалить фото">✕</button>' +
        "</div>";
    }).join("");
  }

  function galleryBlock(product, index) {
    return '<div class="sr-gal" data-gal="' + index + '">' + galleryItems(product) + "</div>" +
      '<div class="sr-photo-tools">' +
        '<label>📷 Добавить фото с ПК<input type="file" accept="image/*" multiple data-gfile="' + index + '"></label>' +
        '<button type="button" data-gclear="' + index + '">Удалить все фото</button>' +
      "</div>" +
      '<div class="sr-photo-name">Файлы загружаются на сервер в папку uploads. Первое фото — главное, оно видно в каталоге.</div>' +
      '<div class="sr-field"><label>Добавить фото по ссылке</label><div class="sr-urlrow">' +
        '<input data-gurl="' + index + '" placeholder="https://...">' +
        '<button type="button" class="sr-addbtn" data-gadd="' + index + '">Добавить</button>' +
      "</div></div>";
  }

  /* ---------- сборка панели ---------- */
  function renderBody() {
    var body = $("sr-admin-body");
    if (!body || !state) return;
    var s = state.settings;

    var html = '<div class="sr-publish">' +
      '<div class="sr-publish-status ok">● Контент хранится в базе данных</div>' +
      "<p>Кнопка «Сохранить изменения» записывает всё в базу, и правки сразу видят все посетители. " +
      "Фотографии загружаются на сервер в папку <b>uploads</b>.</p></div>";

    SETTING_FIELDS.forEach(function (section) {
      html += "<h5>" + esc(section[0]) + "</h5>";
      section[1].forEach(function (f) {
        html += field(f[1], s[f[0]] || "", "set__" + f[0], !!f[2]);
      });
      if (section[0] === "Шапка сайта") {
        html += "<h5>Логотип</h5>" + logoBlock();
      }
    });

    html += "<h5>Каталог — категории</h5><div id=\"sr-adm-cats\"></div>" +
      '<button class="sr-addbtn" id="sr-adm-cat-add">+ Добавить категорию</button>';
    html += "<h5>Каталог — позиции по категориям</h5><div id=\"sr-adm-prods\"></div>";
    html += "<h5>Преимущества</h5><div id=\"sr-adm-features\"></div>" +
      '<button class="sr-addbtn" id="sr-adm-feat-add">+ Добавить преимущество</button>';
    html += "<h5>Факты о компании</h5><div id=\"sr-adm-stats\"></div>" +
      '<button class="sr-addbtn" id="sr-adm-stat-add">+ Добавить факт</button>';

    body.innerHTML = html;

    body.querySelectorAll('[data-key^="set__"]').forEach(function (el) {
      el.addEventListener("input", function () {
        state.settings[el.getAttribute("data-key").slice(5)] = el.value;
        dirty = true;
      });
    });
    body.querySelectorAll("[data-check]").forEach(function (el) {
      el.addEventListener("change", function () {
        state.settings[el.getAttribute("data-check")] = el.checked ? "1" : "0";
        dirty = true;
      });
    });

    bindLogoTools();
    renderCats();
    renderProds();
    renderSimple("features", ["title", "text"], ["Заголовок", "Текст"]);
    renderSimple("stats", ["value", "label"], ["Значение", "Подпись"]);

    $("sr-adm-cat-add").onclick = function () {
      state.categories.push({ id: 0, slug: "", code: "", name: "Новая категория", description: "" });
      dirty = true;
      renderCats();
      renderProds();
    };
    $("sr-adm-feat-add").onclick = function () {
      state.features.push({ id: 0, title: "Новое преимущество", text: "Короткое описание." });
      dirty = true;
      renderSimple("features", ["title", "text"], ["Заголовок", "Текст"]);
    };
    $("sr-adm-stat-add").onclick = function () {
      state.stats.push({ id: 0, value: "Значение", label: "подпись факта" });
      dirty = true;
      renderSimple("stats", ["value", "label"], ["Значение", "Подпись"]);
    };
  }

  function logoBlock() {
    var s = state.settings;
    var logo = s.logo || "";
    return '<div class="sr-logoprev" id="sr-logo-prev">' +
        (logo ? '<img src="' + esc(logo) + '" alt="">' : "<span>Используется стандартный знак</span>") + "</div>" +
      '<div class="sr-photo-tools">' +
        '<label>📷 Загрузить логотип<input type="file" accept="image/*" id="sr-logo-file"></label>' +
        '<button type="button" id="sr-logo-clear">Вернуть стандартный знак</button>' +
      "</div>" +
      field("Ссылка на логотип (URL, необязательно)", logo, "set__logo") +
      field("Высота логотипа в шапке, px (24–110)", s.logoHeight || "44", "set__logoHeight") +
      checkField("Показывать название рядом с логотипом", s.logoShowText === "1", "logoShowText") +
      checkField("Светлая подложка (для логотипа на белом фоне)", s.logoPad === "1", "logoPad");
  }

  function bindLogoTools() {
    var input = $("sr-logo-file");
    if (input) {
      input.addEventListener("change", function () {
        var file = input.files && input.files[0];
        input.value = "";
        if (!file) return;
        uploadFile(file).then(function (url) {
          state.settings.logo = url;
          dirty = true;
          var prev = $("sr-logo-prev");
          if (prev) prev.innerHTML = '<img src="' + esc(url) + '" alt="">';
          var urlField = document.querySelector('[data-key="set__logo"]');
          if (urlField) urlField.value = url;
          toast("Логотип загружен");
        }).catch(function (err) { toast(err.message); });
      });
    }
    var clear = $("sr-logo-clear");
    if (clear) {
      clear.onclick = function () {
        state.settings.logo = "";
        dirty = true;
        var prev = $("sr-logo-prev");
        if (prev) prev.innerHTML = "<span>Используется стандартный знак</span>";
        var urlField = document.querySelector('[data-key="set__logo"]');
        if (urlField) urlField.value = "";
      };
    }
  }

  function renderCats() {
    var wrap = $("sr-adm-cats");
    if (!wrap) return;
    wrap.innerHTML = state.categories.map(function (category, i) {
      var count = state.products.filter(function (p) { return sameCategory(p, category, i); }).length;
      return '<div class="sr-arritem">' +
        (state.categories.length > 1 ? '<button class="rm" data-rm-cat="' + i + '" title="Удалить категорию">✕</button>' : "") +
        field("Название категории", category.name, "cat__" + i + "__name") +
        field("Код", category.code, "cat__" + i + "__code") +
        field("Описание категории", category.description, "cat__" + i + "__description", true) +
        '<div class="sr-photo-name">Позиций в категории: ' + count + "</div>" +
        "</div>";
    }).join("");

    wrap.querySelectorAll("[data-key]").forEach(function (el) {
      el.addEventListener("input", function () {
        var parts = el.getAttribute("data-key").split("__");
        state.categories[+parts[1]][parts[2]] = el.value;
        dirty = true;
        if (parts[2] === "name") renderProds();
      });
    });
    wrap.querySelectorAll("[data-rm-cat]").forEach(function (b) {
      b.onclick = function () {
        var i = +b.getAttribute("data-rm-cat");
        var category = state.categories[i];
        var items = state.products.filter(function (p) { return sameCategory(p, category, i); });
        if (!confirm(items.length
          ? "Удалить категорию вместе с позициями (" + items.length + " шт.)?"
          : "Удалить категорию?")) return;
        state.products = state.products.filter(function (p) { return !sameCategory(p, category, i); });
        state.categories.splice(i, 1);
        dirty = true;
        renderCats();
        renderProds();
      };
    });
  }

  /* Позиция принадлежит категории по id, а у новых категорий — по порядковому ключу. */
  function sameCategory(product, category, index) {
    if (category.id) return product.category_id === category.id;
    return product.category_key === "new" + index;
  }

  function renderProds() {
    var wrap = $("sr-adm-prods");
    if (!wrap) return;
    var options = state.categories.map(function (c, i) {
      return { value: c.id ? String(c.id) : "new" + i, label: c.name };
    });

    wrap.innerHTML = state.categories.map(function (category, ci) {
      var items = [];
      state.products.forEach(function (product, pi) {
        if (sameCategory(product, category, ci)) items.push({ product: product, index: pi });
      });
      return '<div class="sr-catgroup">' +
        '<div class="sr-catgroup-head"><span>' + esc(category.name) + "</span>" +
        '<span class="n">' + items.length + " поз.</span></div>" +
        items.map(function (row) {
          var p = row.product, i = row.index;
          return '<div class="sr-arritem">' +
            '<button class="rm" data-rm-prod="' + i + '" title="Удалить позицию">✕</button>' +
            galleryBlock(p, i) +
            field("Название позиции", p.name, "prod__" + i + "__name") +
            field("Артикул / код", p.code, "prod__" + i + "__code") +
            field("Характеристики (по одной в строке)", p.specs, "prod__" + i + "__specs", true) +
            field("Описание", p.description, "prod__" + i + "__description", true) +
            selectField("Категория", p.category_id ? String(p.category_id) : (p.category_key || ""), "prod__" + i + "__category", options) +
            "</div>";
        }).join("") +
        '<button class="sr-addbtn" data-add-prod="' + ci + '">+ Добавить позицию в «' + esc(category.name) + "»</button>" +
        "</div>";
    }).join("");

    wrap.querySelectorAll("[data-key]").forEach(function (el) {
      var handler = function () {
        var parts = el.getAttribute("data-key").split("__");
        var product = state.products[+parts[1]];
        if (!product) return;
        dirty = true;
        if (parts[2] === "category") {
          var value = el.value;
          if (value.indexOf("new") === 0) {
            product.category_id = 0;
            product.category_key = value;
          } else {
            product.category_id = +value;
            delete product.category_key;
          }
          renderProds();
          renderCats();
          return;
        }
        product[parts[2]] = el.value;
      };
      el.addEventListener("input", handler);
      if (el.tagName === "SELECT") el.addEventListener("change", handler);
    });

    wrap.querySelectorAll("[data-rm-prod]").forEach(function (b) {
      b.onclick = function () {
        if (!confirm("Удалить позицию из каталога?")) return;
        state.products.splice(+b.getAttribute("data-rm-prod"), 1);
        dirty = true;
        renderProds();
        renderCats();
      };
    });
    wrap.querySelectorAll("[data-add-prod]").forEach(function (b) {
      b.onclick = function () {
        var ci = +b.getAttribute("data-add-prod");
        var category = state.categories[ci];
        state.products.push({
          id: 0,
          category_id: category.id || 0,
          category_key: category.id ? undefined : "new" + ci,
          slug: "",
          code: "",
          name: "Новая позиция",
          specs: "Характеристика 1\nХарактеристика 2",
          description: "",
          images: []
        });
        dirty = true;
        renderProds();
        renderCats();
      };
    });

    bindGallery(wrap);
  }

  function bindGallery(wrap) {
    wrap.querySelectorAll("[data-gfile]").forEach(function (input) {
      input.addEventListener("change", function () {
        var product = state.products[+input.getAttribute("data-gfile")];
        var files = Array.prototype.slice.call(input.files || []);
        input.value = "";
        if (!product || !files.length) return;
        var queue = Promise.resolve();
        var added = 0;
        files.forEach(function (file) {
          queue = queue.then(function () {
            return uploadFile(file).then(function (url) {
              product.images.push(url);
              added++;
            }).catch(function (err) { toast(err.message); });
          });
        });
        queue.then(function () {
          dirty = true;
          renderProds();
          if (added) toast("Загружено фото: " + added);
        });
      });
    });

    wrap.querySelectorAll("[data-gadd]").forEach(function (b) {
      b.onclick = function () {
        var i = +b.getAttribute("data-gadd");
        var input = wrap.querySelector('[data-gurl="' + i + '"]');
        var url = input ? input.value.trim() : "";
        if (!url) return;
        state.products[i].images.push(url);
        dirty = true;
        renderProds();
      };
    });
    wrap.querySelectorAll("[data-gclear]").forEach(function (b) {
      b.onclick = function () {
        var product = state.products[+b.getAttribute("data-gclear")];
        if (!product.images.length) return;
        if (!confirm("Удалить все фото этой позиции?")) return;
        product.images = [];
        dirty = true;
        renderProds();
      };
    });
    wrap.querySelectorAll("[data-gal]").forEach(function (box) {
      var product = state.products[+box.getAttribute("data-gal")];
      box.querySelectorAll("[data-gmain]").forEach(function (b) {
        b.onclick = function () {
          var i = +b.getAttribute("data-gmain");
          product.images.unshift(product.images.splice(i, 1)[0]);
          dirty = true;
          renderProds();
        };
      });
      box.querySelectorAll("[data-gdel]").forEach(function (b) {
        b.onclick = function () {
          product.images.splice(+b.getAttribute("data-gdel"), 1);
          dirty = true;
          renderProds();
        };
      });
    });
  }

  function renderSimple(name, columns, labels) {
    var wrap = $("sr-adm-" + (name === "features" ? "features" : "stats"));
    if (!wrap) return;
    var rows = state[name];
    wrap.innerHTML = rows.map(function (row, i) {
      return '<div class="sr-arritem">' +
        (rows.length > 1 ? '<button class="rm" data-rm-row="' + i + '" title="Удалить">✕</button>' : "") +
        field(labels[0], row[columns[0]], name + "__" + i + "__" + columns[0]) +
        field(labels[1], row[columns[1]], name + "__" + i + "__" + columns[1], true) +
        "</div>";
    }).join("");

    wrap.querySelectorAll("[data-key]").forEach(function (el) {
      el.addEventListener("input", function () {
        var parts = el.getAttribute("data-key").split("__");
        state[name][+parts[1]][parts[2]] = el.value;
        dirty = true;
      });
    });
    wrap.querySelectorAll("[data-rm-row]").forEach(function (b) {
      b.onclick = function () {
        if (!confirm("Удалить этот блок?")) return;
        state[name].splice(+b.getAttribute("data-rm-row"), 1);
        dirty = true;
        renderSimple(name, columns, labels);
      };
    });
  }

  /* ---------- загрузка файлов и сохранение ---------- */
  function uploadFile(file) {
    if (file.type.indexOf("image/") !== 0) {
      return Promise.reject(new Error("Выберите файл изображения"));
    }
    var form = new FormData();
    form.append("file", file);
    return api("upload", { method: "POST", body: form }).then(function (data) { return data.url; });
  }

  function save() {
    var button = $("sr-admin-save");
    button.disabled = true;
    button.textContent = "Сохранение…";
    api("save", { method: "POST", json: state }).then(function () {
      dirty = false;
      button.textContent = "Сохранено ✓";
      toast("Изменения сохранены, страница обновится");
      setTimeout(function () { window.location.reload(); }, 600);
    }).catch(function (err) {
      button.disabled = false;
      button.textContent = "Сохранить изменения";
      toast(err.message);
    });
  }

  /* ---------- привязка кнопок ---------- */
  var fab = $("sr-admin-fab");
  if (fab) fab.addEventListener("click", openAdmin);
  var footerLink = $("sr-admin-open");
  if (footerLink) footerLink.addEventListener("click", openAdmin);

  var loginSubmit = $("sr-login-submit");
  if (loginSubmit) loginSubmit.addEventListener("click", submitLogin);
  var loginPass = $("sr-login-pass");
  if (loginPass) {
    loginPass.addEventListener("keydown", function (e) { if (e.key === "Enter") submitLogin(); });
  }
  var loginClose = $("sr-login-close");
  if (loginClose) {
    loginClose.addEventListener("click", function () {
      $("sr-login-bg").classList.remove("show");
      lock();
    });
  }
  var loginBg = $("sr-login-bg");
  if (loginBg) {
    loginBg.addEventListener("click", function (e) {
      if (e.target === this) { this.classList.remove("show"); lock(); }
    });
  }

  var closeBtn = $("sr-admin-close");
  if (closeBtn) closeBtn.addEventListener("click", closeDrawer);
  var saveBtn = $("sr-admin-save");
  if (saveBtn) saveBtn.addEventListener("click", save);
  var logoutBtn = $("sr-admin-logout");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function () {
      api("logout", { method: "POST" }).then(function () {
        dirty = false;
        window.location.reload();
      });
    });
  }

  window.addEventListener("beforeunload", function (e) {
    if (!dirty) return;
    e.preventDefault();
    e.returnValue = "";
  });
})();
