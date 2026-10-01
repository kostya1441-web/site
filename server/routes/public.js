const express = require("express");
const store = require("../lib/store");
const {
  layout,
  pageHead,
  renderBlock,
  ctaStrip,
  bi,
  biBlock,
  ulList,
} = require("../lib/render");

const router = express.Router();

function findNavTrail(nav, urlPath) {
  for (const item of nav) {
    if (item.href === urlPath) return [[item.en, item.ru, null]];
    if (item.children) {
      for (const c of item.children) {
        if (c.href === urlPath) return [[item.en, item.ru, item.href], [c.en, c.ru, null]];
      }
    }
  }
  return null;
}

function renderPage(data, pagePath, overrideTitleRaw) {
  const page = data.pages[pagePath];
  if (!page) return null;
  const ctx = { settings: data.settings, caseStudies: data.caseStudies, articles: data.articles };
  let body = "";
  if (page.showHead !== false) {
    const trail = page.crumbs && page.crumbs.length
      ? page.crumbs.map((c) => [c.en, c.ru, c.href || null])
      : [["Home", "Главная", "/"], [page.title_en, page.title_ru, null]];
    body += pageHead(page.eyebrow_en || "", page.eyebrow_ru || "", page.title_en, page.title_ru, trail, "/");
  }
  body += (page.blocks || []).map((b) => renderBlock(b, ctx)).join("\n");
  if (page.cta !== false) body += ctaStrip(data.settings);
  return layout({
    settings: data.settings,
    nav: data.nav,
    titleEn: page.seoTitle_en || page.title_en,
    bodyHtml: body,
  });
}

router.get("/", (req, res) => {
  const data = store.load();
  res.send(renderPage(data, ""));
});

router.get("/case-studies/", (req, res) => {
  const data = store.load();
  res.send(renderPage(data, "case-studies"));
});

router.get("/case-studies/:slug/", (req, res) => {
  const data = store.load();
  const cs = data.caseStudies.find((c) => c.slug === req.params.slug);
  if (!cs) return res.status(404).send("Not found");
  const challengeBlock = cs.challenge_en
    ? `<div class="panel"><h3>${bi("Challenge", "Задача")}</h3>${biBlock(`<p>${cs.challenge_en}</p>`, `<p>${cs.challenge_ru}</p>`)}</div>`
    : "";
  const body =
    pageHead("Case Study", "Пример проекта", cs.title_en, cs.title_ru, [
      ["Home", "Главная", "/"],
      ["Case Studies", "Примеры проектов", "/case-studies/"],
      [cs.title_en, cs.title_ru, null],
    ], "/") +
    `<section><div class="container" style="max-width:900px">
      <a class="back-link" href="/case-studies/">${bi("← Back to case studies", "← Назад к примерам проектов")}</a>
      <div class="img-frame" style="margin-bottom:26px"><img src="${cs.image}" alt=""></div>
      ${challengeBlock}
      <div class="panel"><h3>${bi("Solution", "Решение")}</h3>${ulList(cs.solution)}</div>
      <a class="btn" href="/contact/">${bi("Contact us", "Связаться с нами")}</a>
    </div></section>`;
  const data2 = store.load();
  res.send(layout({ settings: data2.settings, nav: data2.nav, titleEn: cs.title_en, bodyHtml: body }));
});

router.get("/press-releases/", (req, res) => {
  const data = store.load();
  res.send(renderPage(data, "press-releases"));
});

router.get("/press-releases/:slug/", (req, res) => {
  const data = store.load();
  const a = data.articles.find((x) => x.slug === req.params.slug);
  if (!a) return res.status(404).send("Not found");
  const body =
    pageHead("Press Release", "Пресс-релиз", a.title_en, a.title_ru, [
      ["Home", "Главная", "/"],
      ["Press Releases", "Пресс-релизы", "/press-releases/"],
      [a.title_en, a.title_ru, null],
    ], "/") +
    `<section><div class="container" style="max-width:860px">
      <a class="back-link" href="/press-releases/">${bi("← Back to press releases", "← Назад к пресс-релизам")}</a>
      ${a.image ? `<div class="img-frame" style="margin-bottom:28px"><img src="${a.image}" alt=""></div>` : ""}
      <p class="meta" style="color:var(--steel);font-weight:700">${bi(a.date_en, a.date_ru)}</p>
      ${biBlock(a.body_en, a.body_ru)}
    </div></section>` +
    ctaStrip(data.settings);
  res.send(layout({ settings: data.settings, nav: data.nav, titleEn: a.title_en, bodyHtml: body }));
});

router.post("/contact/submit", express.urlencoded({ extended: true }), (req, res) => {
  const { name, email, phone, company, message } = req.body;
  store.update((data) => {
    data.leads = data.leads || [];
    data.leads.unshift({
      id: Date.now().toString(36),
      name,
      email,
      phone,
      company,
      message,
      receivedAt: new Date().toISOString(),
      read: false,
    });
  });
  res.redirect("/contact/?sent=1");
});

// Generic catch-all for every other content page, matching any depth
// (history/, hippo-range/verticals/vertical-top-suction/, etc).
router.get(/^\/(.+)\/$/, (req, res, next) => {
  const slug = req.params[0];
  const data = store.load();
  const html = renderPage(data, slug);
  if (!html) return next();
  res.send(html);
});

module.exports = router;
