// Shared HTML rendering: layout chrome (nav/footer) + block renderers.
// Both the public site and the admin "preview" reuse these so edits show
// exactly as they will on the live page.

function esc(s) {
  if (s == null) return "";
  return String(s);
}

function bi(en, ru, tag = "span", cls = "") {
  const klass = cls ? ` class="${cls}"` : "";
  return (
    `<${tag}${klass} data-i18n-group="en">${esc(en)}</${tag}>` +
    `<${tag}${klass} data-i18n-group="ru">${esc(ru)}</${tag}>`
  );
}

function biBlock(enHtml, ruHtml, tag = "div", cls = "") {
  const klass = cls ? ` class="${cls}"` : "";
  return (
    `<${tag}${klass} data-i18n-group="en">${enHtml || ""}</${tag}>` +
    `<${tag}${klass} data-i18n-group="ru">${ruHtml || ""}</${tag}>`
  );
}

function ulList(items, cls = "spec-list") {
  const en = (items || []).map((i) => `<li>${esc(i.en)}</li>`).join("");
  const ru = (items || []).map((i) => `<li>${esc(i.ru)}</li>`).join("");
  return biBlock(`<ul class="${cls}">${en}</ul>`, `<ul class="${cls}">${ru}</ul>`, "div");
}

function renderNav(nav, assetPrefix) {
  return nav
    .map((item) => {
      const label = bi(item.en, item.ru);
      if (item.children && item.children.length) {
        const kids = item.children
          .map((c) => `<li><a href="${assetPrefix}${c.href.replace(/^\//, "")}">${bi(c.en, c.ru)}</a></li>`)
          .join("");
        return (
          `<li><a href="${assetPrefix}${item.href.replace(/^\//, "")}" class="nav-top">${label}</a>` +
          `<ul class="dropdown">${kids}</ul></li>`
        );
      }
      return `<li><a href="${assetPrefix}${item.href.replace(/^\//, "")}">${label}</a></li>`;
    })
    .join("");
}

function crumbsHtml(trail, assetPrefix) {
  const parts = trail.map(([en, ru, href]) =>
    href ? `<a href="${assetPrefix}${href.replace(/^\//, "")}">${bi(en, ru)}</a>` : bi(en, ru)
  );
  return `<div class="breadcrumb">${parts.join(" / ")}</div>`;
}

function ctaStrip(settings) {
  return `
<section class="tight">
  <div class="container">
    <div class="cta-strip">
      <div>
        <h3>${bi(settings.ctaTitle_en, settings.ctaTitle_ru)}</h3>
        ${biBlock(`<p>${esc(settings.ctaBody_en)}</p>`, `<p>${esc(settings.ctaBody_ru)}</p>`)}
      </div>
      <a class="btn" href="/contact/">${bi("Contact Us", "Связаться с нами")}</a>
    </div>
  </div>
</section>`;
}

// ---------------------------------------------------------------- blocks --

function renderBlock(block, ctx) {
  switch (block.type) {
    case "hero":
      return `
<section class="hero">
  <div class="container">
    <div>
      ${block.eyebrow_en ? `<p class="eyebrow" style="color:#e8a768">${bi(block.eyebrow_en, block.eyebrow_ru)}</p>` : ""}
      <h1>${bi(block.title_en, block.title_ru)}</h1>
      ${biBlock(`<p>${block.body_en || ""}</p>`, `<p>${block.body_ru || ""}</p>`)}
      <div style="display:flex;gap:14px;flex-wrap:wrap">
        ${(block.buttons || [])
          .map(
            (b, i) =>
              `<a class="btn ${i === 0 ? "" : "ghost"}" href="${b.href}">${bi(b.label_en, b.label_ru)}</a>`
          )
          .join("")}
      </div>
    </div>
    ${block.image ? `<div class="hero-art"><img src="${block.image}" alt=""></div>` : ""}
  </div>
</section>`;

    case "text":
      return `
<section class="${block.alt ? "alt" : ""} ${block.tight ? "tight" : ""}">
  <div class="container" style="${block.maxWidth ? `max-width:${block.maxWidth}` : ""}">
    ${block.eyebrow_en ? `<p class="eyebrow">${bi(block.eyebrow_en, block.eyebrow_ru)}</p>` : ""}
    ${block.title_en ? `<h2 class="section-title">${bi(block.title_en, block.title_ru)}</h2>` : ""}
    ${biBlock(
      `<div class="lede" style="${block.center ? "margin:0 auto;text-align:center" : ""}">${block.body_en || ""}</div>`,
      `<div class="lede" style="${block.center ? "margin:0 auto;text-align:center" : ""}">${block.body_ru || ""}</div>`
    )}
  </div>
</section>`;

    case "textImage":
      return `
<section class="${block.alt ? "alt" : ""}">
  <div class="container">
    <div class="two-col">
      ${
        block.imageSide === "left"
          ? `<div class="img-frame"><img src="${block.image}" alt=""></div>${biBlock(
              `<div class='lede'>${block.body_en || ""}</div>`,
              `<div class='lede'>${block.body_ru || ""}</div>`
            )}`
          : `${
              block.title_en
                ? `<div><h2 class="section-title">${bi(block.title_en, block.title_ru)}</h2>${biBlock(
                    `<div>${block.body_en || ""}</div>`,
                    `<div>${block.body_ru || ""}</div>`
                  )}</div>`
                : biBlock(`<div class='lede'>${block.body_en || ""}</div>`, `<div class='lede'>${block.body_ru || ""}</div>`)
            }<div class="img-frame"><img src="${block.image}" alt=""></div>`
      }
    </div>
  </div>
</section>`;

    case "list":
      return `
<section>
  <div class="container">
    ${block.title_en ? `<h2 class="section-title">${bi(block.title_en, block.title_ru)}</h2>` : ""}
    ${ulList(block.items, block.style === "tag" ? "tag-list" : "spec-list")}
  </div>
</section>`;

    case "cards": {
      const cols = block.columns || 3;
      const cards = (block.items || [])
        .map((it) => {
          if (block.variant === "feature") {
            return `<div class="feature-card">
              ${it.num ? `<div class="num">${esc(it.num)}</div>` : ""}
              <h3>${bi(it.title_en, it.title_ru)}</h3>
              ${biBlock(`<p>${it.body_en || ""}</p>`, `<p>${it.body_ru || ""}</p>`)}
            </div>`;
          }
          if (block.variant === "product") {
            return `<div class="product-card">
              ${it.image ? `<div class="thumb"><img src="${it.image}" alt=""></div>` : ""}
              <div class="body">
                <h3>${bi(it.title_en, it.title_ru)}</h3>
                ${biBlock(`<p>${it.body_en || ""}</p>`, `<p>${it.body_ru || ""}</p>`)}
                ${it.link_href ? `<a class="btn dark" href="${it.link_href}">${bi(it.link_en || "Find out more", it.link_ru || "Подробнее")}</a>` : ""}
              </div>
            </div>`;
          }
          // plain card
          return `<div class="card">
            <h3>${bi(it.title_en, it.title_ru)}</h3>
            ${biBlock(`<p>${it.body_en || ""}</p>`, `<p>${it.body_ru || ""}</p>`)}
          </div>`;
        })
        .join("");
      return `
<section class="${block.alt ? "alt" : ""}">
  <div class="container">
    ${block.eyebrow_en ? `<p class="eyebrow">${bi(block.eyebrow_en, block.eyebrow_ru)}</p>` : ""}
    ${block.title_en ? `<h2 class="section-title">${bi(block.title_en, block.title_ru)}</h2>` : ""}
    <div class="grid cols-${cols}" style="margin-top:28px">${cards}</div>
  </div>
</section>`;
    }

    case "panelGrid": {
      const panels = (block.items || [])
        .map(
          (it) => `<div class="panel" style="${it.wide ? "grid-column:1/-1" : ""}">
          <h3>${bi(it.title_en, it.title_ru)}</h3>
          ${biBlock(`<div>${it.body_en || ""}</div>`, `<div>${it.body_ru || ""}</div>`)}
        </div>`
        )
        .join("");
      return `
<section>
  <div class="container" style="max-width:${block.maxWidth || "1020px"}">
    <div class="grid cols-2" style="margin-top:12px">${panels}</div>
  </div>
</section>`;
    }

    case "advantages": {
      const items = block.items || [];
      const liEn = items.map((i) => `<li><strong>${esc(i.label_en)}:</strong> ${esc(i.body_en)}</li>`).join("");
      const liRu = items.map((i) => `<li><strong>${esc(i.label_ru)}:</strong> ${esc(i.body_ru)}</li>`).join("");
      return `
<section>
  <div class="container" style="max-width:1020px">
    <div class="panel">
      <h3>${bi("The Hippo Twin-Volute Discharge Design", "Двухспиральная конструкция нагнетания Hippo")}</h3>
      <p>${bi("Balanced hydraulic forces totally eliminate shaft deflection during partial loading.", "Сбалансированные гидравлические силы полностью исключают прогиб вала при частичной загрузке.")}</p>
      <h4>${bi(block.title_en, block.title_ru)}</h4>
      ${biBlock(`<ul class="spec-list">${liEn}</ul>`, `<ul class="spec-list">${liRu}</ul>`, "div")}
    </div>
  </div>
</section>`;
    }

    case "image":
      return `
<section class="${block.alt ? "alt" : ""} tight">
  <div class="container">
    <figure class="img-frame">
      <img src="${block.image}" alt="">
      ${block.caption_en ? `<figcaption>${bi(block.caption_en, block.caption_ru)}</figcaption>` : ""}
    </figure>
  </div>
</section>`;

    case "logos":
      return `
<section class="alt tight">
  <div class="container">
    ${block.title_en ? `<p class="eyebrow" style="text-align:center">${bi(block.title_en, block.title_ru)}</p>` : ""}
    <div class="assoc-row">
      ${(block.items || []).map((it) => `<img src="${it.image}" alt="${esc(it.alt_en || "")}">`).join("")}
    </div>
  </div>
</section>`;

    case "awards":
      return `
<section>
  <div class="container">
    <div class="grid cols-2">
      ${(block.items || [])
        .map(
          (it) => `<div class="award-card">
        <img src="${it.image}" alt="">
        <div>
          <h3>${bi(it.title_en, it.title_ru)}</h3>
          ${biBlock(`<p>${it.body_en || ""}</p>`, `<p>${it.body_ru || ""}</p>`)}
        </div>
      </div>`
        )
        .join("")}
    </div>
  </div>
</section>`;

    case "caseStudyGrid": {
      const cs = ctx.caseStudies || [];
      const cards = cs
        .map(
          (c) => `<a class="case-card" href="/case-studies/${c.slug}/">
        <div class="thumb"><img src="${c.image}" alt=""></div>
        <div class="body">
          <h3>${bi(c.title_en, c.title_ru)}</h3>
          <span>${bi("Learn more →", "Подробнее →")}</span>
        </div>
      </a>`
        )
        .join("");
      return `
<section class="${block.alt ? "alt" : ""}">
  <div class="container">
    ${block.title_en ? `<h2 class="section-title">${bi(block.title_en, block.title_ru)}</h2>` : ""}
    <div class="grid cols-3" style="margin-top:22px">${cards}</div>
  </div>
</section>`;
    }

    case "articleGrid": {
      const articles = ctx.articles || [];
      const cards = articles
        .map(
          (a) => `<a class="article-card" href="/press-releases/${a.slug}/" style="text-decoration:none;color:inherit">
        <div class="thumb"><img src="${a.image}" alt=""></div>
        <div>
          <div class="meta">${bi(a.date_en, a.date_ru)}</div>
          <h3>${bi(a.title_en, a.title_ru)}</h3>
          ${biBlock(`<p>${a.teaser_en || ""}</p>`, `<p>${a.teaser_ru || ""}</p>`)}
          <span class="btn dark" style="margin-top:8px">${bi("Read more", "Читать далее")}</span>
        </div>
      </a>`
        )
        .join("");
      return `
<section>
  <div class="container">
    <div class="grid cols-1" style="gap:20px">${cards}</div>
  </div>
</section>`;
    }

    case "pressClippings": {
      const items = block.items || [];
      return `
<section>
  <div class="container">
    <h2 class="section-title">${bi(block.title_en, block.title_ru)}</h2>
    <ul class="press-clip-list">
      ${items
        .map((i) => `<li><span>${bi(i.title_en, i.title_ru)}</span><span class="src">${esc(i.source)}</span></li>`)
        .join("")}
    </ul>
  </div>
</section>`;
    }

    case "curveGroups": {
      const groups = block.groups || [];
      return `
<section>
  <div class="container">
    ${groups
      .map(
        (g) => `
      <h2 class="section-title" style="margin-top:40px">${bi(g.title_en, g.title_ru)}</h2>
      <div class="grid cols-3">
        ${(g.items || [])
          .map(
            (it) => `<div class="card" style="padding:0;overflow:hidden">
          <div class="img-frame" style="border:none;border-radius:0"><img src="${it.image}" alt="" loading="lazy"></div>
          <div style="padding:16px 18px"><h3 style="font-size:0.95rem;margin:0">${bi(it.title_en, it.title_ru)}</h3></div>
        </div>`
          )
          .join("")}
      </div>`
      )
      .join("")}
  </div>
</section>`;
    }

    case "contactBlock": {
      const s = ctx.settings;
      return `
<section>
  <div class="container">
    <div class="two-col">
      <div>
        <h2 class="section-title">${bi("Contact us", "Свяжитесь с нами")}</h2>
        <div class="contact-block">
          <div class="item"><span class="ic">&#9679;</span><div>
            <strong>${esc(s.companyName)}</strong><br>
            ${bi(s.address_en, s.address_ru)}
          </div></div>
          <div class="item"><span class="ic">&#9742;</span><div><a href="tel:${esc(s.phone).replace(/[^\d+]/g, "")}">${esc(s.phone)}</a></div></div>
          <div class="item"><span class="ic">&#9993;</span><div><a href="mailto:${esc(s.email)}">${esc(s.email)}</a></div></div>
        </div>
      </div>
      <div class="panel">
        <h3>${bi("Request a quote", "Запросить коммерческое предложение")}</h3>
        ${ctx.formNotice || ""}
        <form class="quote-form" method="post" action="/contact/submit">
          <div><label>${bi("Name and Surname", "Имя и фамилия")}</label><input type="text" name="name" required></div>
          <div><label>${bi("Email Address", "Электронная почта")}</label><input type="email" name="email" required></div>
          <div><label>${bi("Contact Number", "Контактный телефон")}</label><input type="tel" name="phone"></div>
          <div><label>${bi("Company Name", "Название компании")}</label><input type="text" name="company"></div>
          <div><label>${bi("Message", "Сообщение")}</label><textarea name="message" required></textarea></div>
          <button type="submit" class="btn">${bi("Submit", "Отправить")}</button>
        </form>
      </div>
    </div>
  </div>
</section>`;
    }

    case "backLink":
      return `<div class="container" style="padding-top:40px;max-width:${block.maxWidth || "900px"}"><a class="back-link" href="${block.href}">${bi("← " + block.label_en, "← " + block.label_ru)}</a></div>`;

    default:
      return "";
  }
}

function pageHead(eyebrow_en, eyebrow_ru, title_en, title_ru, crumbTrail, assetPrefix) {
  return `
<section class="page-head">
  <div class="container">
    ${crumbsHtml(crumbTrail, assetPrefix)}
    <p class="eyebrow" style="color:#e8a768">${bi(eyebrow_en, eyebrow_ru)}</p>
    <h1>${bi(title_en, title_ru)}</h1>
  </div>
</section>`;
}

function layout({ settings, nav, titleEn, bodyHtml, assetPrefix = "/", path: urlPath = "/" }) {
  const phoneHref = esc(settings.phone).replace(/[^\d+]/g, "");
  return `<!doctype html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${esc(titleEn)} | ${esc(settings.companyName)}</title>
<meta name="description" content="${esc(settings.metaDescription)}">
<link rel="icon" href="${esc(settings.logo || "/assets/img/logo.svg")}">
<link rel="stylesheet" href="${assetPrefix}assets/css/style.css">
<script>
(function(){try{var l=localStorage.getItem('hazleton-lang');if(l==='ru'){document.documentElement.setAttribute('data-lang','ru');document.documentElement.setAttribute('lang','ru');}}catch(e){}})();
</script>
</head>
<body>
<div class="topbar">
  <div class="container">
    <div class="contacts">
      <a href="tel:${phoneHref}">T: ${esc(settings.phone)}</a>
      <a href="mailto:${esc(settings.email)}">E: ${esc(settings.email)}</a>
    </div>
    <div class="lang-switch">
      <button type="button" data-lang-btn="en">EN</button>
      <button type="button" data-lang-btn="ru">RU</button>
    </div>
  </div>
</div>
<header class="site-header">
  <div class="container nav-row">
    <a href="/" class="brand">
      <img src="${esc(settings.logo || "/assets/img/logo.svg")}" alt="${esc(settings.companyName)} logo">
      <span class="brand-name">${esc(settings.companyName)}<small>${bi("Intl (Pty) Ltd", "Intl (Pty) Ltd · Южная Африка")}</small></span>
    </a>
    <nav class="main-nav"><ul>${renderNav(nav, assetPrefix)}</ul></nav>
    <div class="nav-actions">
      <a class="btn-quote" href="/contact/">${bi("Request a Quote", "Запросить КП")}</a>
      <button class="nav-toggle" aria-label="Menu">&#9776;</button>
    </div>
  </div>
</header>
${bodyHtml}
<footer class="site-footer">
  <div class="container">
    <div class="grid">
      <div>
        <div class="brand" style="color:#fff"><img src="${esc(settings.logo || "/assets/img/logo.svg")}" alt="" style="height:34px"></div>
        ${biBlock(`<p class='tag'>${esc(settings.footerTag_en)}</p>`, `<p class='tag'>${esc(settings.footerTag_ru)}</p>`)}
      </div>
      <div>
        <h4>${bi("Company", "Компания")}</h4>
        <ul>
          <li><a href="/history/">${bi("History", "История")}</a></li>
          <li><a href="/awards/">${bi("Awards", "Награды")}</a></li>
          <li><a href="/press-releases/">${bi("Press & Articles", "Пресса и статьи")}</a></li>
        </ul>
      </div>
      <div>
        <h4>${bi("Products", "Продукция")}</h4>
        <ul>
          <li><a href="/hippo-range/">${bi("Hippo Range", "Линейка Hippo")}</a></li>
          <li><a href="/pump-systems/">${bi("Pump Systems", "Насосные системы")}</a></li>
          <li><a href="/pump-curves/">${bi("Pump Curves", "Напорные характеристики")}</a></li>
          <li><a href="/case-studies/">${bi("Case Studies", "Примеры проектов")}</a></li>
        </ul>
      </div>
      <div>
        <h4>${bi("Contact", "Контакты")}</h4>
        <ul>
          <li>${bi(settings.address_en, settings.address_ru)}</li>
          <li><a href="tel:${phoneHref}">${esc(settings.phone)}</a></li>
          <li><a href="mailto:${esc(settings.email)}">${esc(settings.email)}</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>${bi(`Copyright © ${settings.year} ${settings.companyName}`, `© ${settings.year} ${settings.companyName}. Все права защищены`)}</span>
      <span>${bi("Powered by your own editable site", "Работает на вашем редактируемом сайте")}</span>
    </div>
  </div>
</footer>
<script src="${assetPrefix}assets/js/i18n.js"></script>
</body>
</html>`;
}

module.exports = {
  esc,
  bi,
  biBlock,
  ulList,
  renderNav,
  crumbsHtml,
  ctaStrip,
  renderBlock,
  pageHead,
  layout,
};
