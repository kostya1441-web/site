const express = require("express");
const multer = require("multer");
const path = require("path");
const fs = require("fs");
const crypto = require("crypto");

const store = require("../lib/store");
const auth = require("../lib/auth");
const views = require("../lib/adminViews");

const router = express.Router();

const UPLOAD_DIR = path.join(__dirname, "..", "public", "uploads");
fs.mkdirSync(UPLOAD_DIR, { recursive: true });

const upload = multer({
  storage: multer.diskStorage({
    destination: UPLOAD_DIR,
    filename: (req, file, cb) => {
      const ext = path.extname(file.originalname).toLowerCase() || "";
      const base = path
        .basename(file.originalname, ext)
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .slice(0, 40);
      cb(null, `${Date.now()}-${crypto.randomBytes(3).toString("hex")}-${base}${ext}`);
    },
  }),
  limits: { fileSize: 12 * 1024 * 1024 },
  fileFilter: (req, file, cb) => {
    const ok = /^image\/(png|jpe?g|gif|webp|svg\+xml)$/.test(file.mimetype);
    cb(ok ? null : new Error("Only image uploads are allowed"), ok);
  },
});

// --------------------------------------------------------------- helpers --

function publicUploadPath(filename) {
  return `/uploads/${filename}`;
}

// Resolve an "image field" (hidden __current + file __upload) submitted
// either as a top-level body key or inside req.files, back to a single path.
function resolveImageField(body, files, fieldBase) {
  // Actual multipart fieldnames carry their full bracket path (e.g.
  // "blocks[0][image__upload]"), not the bare "image__upload" — match by
  // suffix. Callers already pre-filter `files` to the owning block/item,
  // so a loose suffix match can't cross-match a sibling field.
  const uploaded = (files || []).find((f) => f.fieldname.endsWith(`${fieldBase}__upload]`) || f.fieldname === `${fieldBase}__upload`);
  if (uploaded) return publicUploadPath(uploaded.filename);
  return getPath(body, `${fieldBase}__current`) || "";
}

function getPath(obj, dotted) {
  // dotted uses bracket notation already parsed by qs into nested objects;
  // this just walks "a[b][c]"-style keys that qs turned into obj.a.b.c
  const parts = dotted.replace(/\]/g, "").split("[");
  let cur = obj;
  for (const p of parts) {
    if (cur == null) return undefined;
    cur = cur[p];
  }
  return cur;
}

function truthy(v) {
  return v === "1" || v === "true" || v === true;
}

function biFromBody(obj, name) {
  return { en: (obj && obj[`${name}_en`]) || "", ru: (obj && obj[`${name}_ru`]) || "" };
}

// Rebuilds a single block object from the parsed form body + uploaded files.
function blockFromForm(raw, files, idx) {
  const type = raw.type;
  const filesForBlock = (files || []).filter((f) => f.fieldname.startsWith(`blocks[${idx}]`));
  const image = (base) => resolveImageField(raw, filesForBlock, base);

  const itemsArray = (raw.items || []).map((it) => it || {});

  switch (type) {
    case "hero":
      return {
        type,
        eyebrow_en: raw.eyebrow_en,
        eyebrow_ru: raw.eyebrow_ru,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        body_en: raw.body_en,
        body_ru: raw.body_ru,
        image: image("image"),
        buttons: (raw.buttons || []).map((b) => ({ label_en: b.label_en, label_ru: b.label_ru, href: b.href })),
      };
    case "text":
      return {
        type,
        eyebrow_en: raw.eyebrow_en,
        eyebrow_ru: raw.eyebrow_ru,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        body_en: raw.body_en,
        body_ru: raw.body_ru,
        alt: truthy(raw.alt),
        center: truthy(raw.center),
      };
    case "textImage":
      return {
        type,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        body_en: raw.body_en,
        body_ru: raw.body_ru,
        image: image("image"),
        imageSide: raw.imageSide || "right",
        alt: truthy(raw.alt),
      };
    case "list":
      return {
        type,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        style: raw.style || "spec",
        items: itemsArray.map((it) => ({ en: it.en, ru: it.ru })),
      };
    case "cards": {
      const items = itemsArray.map((it, i) => {
        const itemFiles = filesForBlock.filter((f) => f.fieldname.startsWith(`blocks[${idx}][items][${i}]`));
        return {
          title_en: it.title_en,
          title_ru: it.title_ru,
          body_en: it.body_en,
          body_ru: it.body_ru,
          image: resolveImageField(it, itemFiles, "image"),
          link_href: it.link_href,
          link_en: it.link_en,
          link_ru: it.link_ru,
        };
      });
      return {
        type,
        eyebrow_en: raw.eyebrow_en,
        eyebrow_ru: raw.eyebrow_ru,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        variant: raw.variant || "plain",
        columns: Number(raw.columns) || 3,
        alt: truthy(raw.alt),
        items,
      };
    }
    case "panelGrid":
      return {
        type,
        items: itemsArray.map((it) => ({
          title_en: it.title_en,
          title_ru: it.title_ru,
          body_en: it.body_en,
          body_ru: it.body_ru,
          wide: truthy(it.wide),
        })),
      };
    case "advantages":
      return {
        type,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        items: itemsArray.map((it) => ({ label_en: it.label_en, label_ru: it.label_ru, body_en: it.body_en, body_ru: it.body_ru })),
      };
    case "image":
      return {
        type,
        image: image("image"),
        caption_en: raw.caption_en,
        caption_ru: raw.caption_ru,
        alt: truthy(raw.alt),
      };
    case "logos": {
      const items = itemsArray.map((it, i) => {
        const itemFiles = filesForBlock.filter((f) => f.fieldname.startsWith(`blocks[${idx}][items][${i}]`));
        return { image: resolveImageField(it, itemFiles, "image"), alt_en: it.alt_en };
      });
      return { type, title_en: raw.title_en, title_ru: raw.title_ru, items };
    }
    case "awards": {
      const items = itemsArray.map((it, i) => {
        const itemFiles = filesForBlock.filter((f) => f.fieldname.startsWith(`blocks[${idx}][items][${i}]`));
        return {
          title_en: it.title_en,
          title_ru: it.title_ru,
          body_en: it.body_en,
          body_ru: it.body_ru,
          image: resolveImageField(it, itemFiles, "image"),
        };
      });
      return { type, items };
    }
    case "pressClippings":
      return {
        type,
        title_en: raw.title_en,
        title_ru: raw.title_ru,
        items: itemsArray.map((it) => ({ title_en: it.title_en, title_ru: it.title_ru, source: it.source })),
      };
    case "caseStudyGrid":
    case "articleGrid":
      return { type, title_en: raw.title_en, title_ru: raw.title_ru, alt: truthy(raw.alt) };
    case "backLink":
      return { type, href: raw.href, label_en: raw.label_en, label_ru: raw.label_ru };
    case "contactBlock":
      return { type };
    case "curveGroups": {
      try {
        const parsed = JSON.parse(raw.__json);
        return { type, groups: parsed.groups || [] };
      } catch (e) {
        return { type, groups: [] };
      }
    }
    default:
      try {
        return JSON.parse(raw.__json);
      } catch (e) {
        return { type };
      }
  }
}

// ----------------------------------------------------------------- login --

router.get("/login", (req, res) => {
  if (req.session.loggedIn) return res.redirect("/admin/");
  res.send(views.loginPage());
});

router.post("/login", express.urlencoded({ extended: true }), (req, res) => {
  const { username, password } = req.body;
  if (auth.verifyLogin(username, password)) {
    req.session.loggedIn = true;
    req.session.username = username;
    return res.redirect("/admin/");
  }
  res.status(401).send(views.loginPage("Неверный логин или пароль."));
});

router.get("/logout", (req, res) => {
  req.session.destroy(() => res.redirect("/admin/login"));
});

router.use(auth.requireLogin);
router.use(express.urlencoded({ extended: true }));

// --------------------------------------------------------------- dashboard --

router.get("/", (req, res) => {
  res.send(views.dashboardPage(store.load()));
});

// ------------------------------------------------------------------ pages --

router.get("/pages/", (req, res) => {
  res.send(views.pagesListPage(store.load().pages));
});

router.get("/pages/:key(*)", (req, res) => {
  const data = store.load();
  const page = data.pages[req.params.key];
  if (!page) return res.status(404).send("Страница не найдена");
  res.send(views.pageEditorPage(req.params.key, page));
});

router.post("/pages/:key(*)", upload.any(), (req, res) => {
  const key = req.params.key;
  store.update((data) => {
    const page = data.pages[key];
    if (!page) return;
    page.eyebrow_en = req.body.eyebrow_en || "";
    page.eyebrow_ru = req.body.eyebrow_ru || "";
    page.title_en = req.body.title_en || page.title_en;
    page.title_ru = req.body.title_ru || page.title_ru;

    const rawBlocks = req.body.blocks || {};
    const indices = Object.keys(rawBlocks)
      .map(Number)
      .sort((a, b) => a - b);
    const rebuilt = [];
    for (const i of indices) {
      const raw = rawBlocks[i];
      if (truthy(raw.__delete)) continue;
      rebuilt.push(blockFromForm(raw, req.files, i));
    }
    page.blocks = rebuilt;

    if (req.body.__action === "addBlock" && req.body.__newBlockType) {
      page.blocks.push(defaultBlockFor(req.body.__newBlockType));
    }
  }).then(() => res.redirect(`/admin/pages/${encodeURIComponent(key)}`));
});

function defaultBlockFor(type) {
  const base = { type };
  if (type === "list" || type === "panelGrid" || type === "advantages" || type === "logos" || type === "awards" || type === "pressClippings") {
    base.items = [];
  }
  if (type === "cards") base.items = [];
  if (type === "hero") base.buttons = [];
  if (type === "curveGroups") base.groups = [];
  return base;
}

// ------------------------------------------------------------ case studies --

router.get("/case-studies/", (req, res) => {
  res.send(views.caseStudiesListPage(store.load().caseStudies));
});

router.get("/case-studies/new", (req, res) => {
  res.send(views.caseStudyEditorPage({ solution: [] }, true));
});

router.get("/case-studies/:slug", (req, res) => {
  const cs = store.load().caseStudies.find((c) => c.slug === req.params.slug);
  if (!cs) return res.status(404).send("Не найдено");
  res.send(views.caseStudyEditorPage(cs, false));
});

router.post("/case-studies/new", upload.any(), (req, res) => {
  const slug = (req.body.slug || "").trim().toLowerCase().replace(/[^a-z0-9-]+/g, "-");
  if (!slug) return res.status(400).send("Slug обязателен");
  store
    .update((data) => {
      if (data.caseStudies.some((c) => c.slug === slug)) throw new Error("slug-exists");
      data.caseStudies.push(buildCaseStudy(slug, req.body, req.files));
    })
    .then(() => res.redirect("/admin/case-studies/"))
    .catch(() => res.status(400).send("Такой slug уже существует. Вернитесь назад и выберите другой."));
});

router.post("/case-studies/:slug", upload.any(), (req, res) => {
  store
    .update((data) => {
      const idx = data.caseStudies.findIndex((c) => c.slug === req.params.slug);
      if (idx === -1) throw new Error("not-found");
      const existing = data.caseStudies[idx];
      const newSlug = (req.body.slug || existing.slug).trim().toLowerCase().replace(/[^a-z0-9-]+/g, "-");
      data.caseStudies[idx] = buildCaseStudy(newSlug, req.body, req.files, existing);
    })
    .then(() => res.redirect("/admin/case-studies/"))
    .catch(() => res.status(404).send("Не найдено"));
});

router.post("/case-studies/:slug/delete", (req, res) => {
  store
    .update((data) => {
      data.caseStudies = data.caseStudies.filter((c) => c.slug !== req.params.slug);
    })
    .then(() => res.redirect("/admin/case-studies/"));
});

function buildCaseStudy(slug, body, files, existing) {
  return {
    slug,
    title_en: body.title_en || "",
    title_ru: body.title_ru || "",
    image: resolveImageField(body, files, "image") || (existing && existing.image) || "",
    challenge_en: body.challenge_en || "",
    challenge_ru: body.challenge_ru || "",
    solution: (body.solution || []).map((it) => ({ en: it.en, ru: it.ru })),
  };
}

// ----------------------------------------------------------------- articles --

router.get("/articles/", (req, res) => {
  res.send(views.articlesListPage(store.load().articles));
});

router.get("/articles/new", (req, res) => {
  res.send(views.articleEditorPage({}, true));
});

router.get("/articles/:slug", (req, res) => {
  const a = store.load().articles.find((x) => x.slug === req.params.slug);
  if (!a) return res.status(404).send("Не найдено");
  res.send(views.articleEditorPage(a, false));
});

router.post("/articles/new", upload.any(), (req, res) => {
  const slug = (req.body.slug || "").trim().toLowerCase().replace(/[^a-z0-9-]+/g, "-");
  if (!slug) return res.status(400).send("Slug обязателен");
  store
    .update((data) => {
      if (data.articles.some((a) => a.slug === slug)) throw new Error("slug-exists");
      data.articles.push(buildArticle(slug, req.body, req.files));
    })
    .then(() => res.redirect("/admin/articles/"))
    .catch(() => res.status(400).send("Такой slug уже существует."));
});

router.post("/articles/:slug", upload.any(), (req, res) => {
  store
    .update((data) => {
      const idx = data.articles.findIndex((a) => a.slug === req.params.slug);
      if (idx === -1) throw new Error("not-found");
      const existing = data.articles[idx];
      const newSlug = (req.body.slug || existing.slug).trim().toLowerCase().replace(/[^a-z0-9-]+/g, "-");
      data.articles[idx] = buildArticle(newSlug, req.body, req.files, existing);
    })
    .then(() => res.redirect("/admin/articles/"))
    .catch(() => res.status(404).send("Не найдено"));
});

router.post("/articles/:slug/delete", (req, res) => {
  store.update((data) => {
    data.articles = data.articles.filter((a) => a.slug !== req.params.slug);
  }).then(() => res.redirect("/admin/articles/"));
});

function buildArticle(slug, body, files, existing) {
  return {
    slug,
    title_en: body.title_en || "",
    title_ru: body.title_ru || "",
    date_en: body.date_en || "",
    date_ru: body.date_ru || "",
    image: resolveImageField(body, files, "image") || (existing && existing.image) || "",
    teaser_en: body.teaser_en || "",
    teaser_ru: body.teaser_ru || "",
    body_en: body.body_en || "",
    body_ru: body.body_ru || "",
  };
}

// ----------------------------------------------------------------- settings --

router.get("/settings", (req, res) => {
  res.send(views.settingsPage(store.load().settings, auth.readAdmin().username));
});

router.post("/account", (req, res) => {
  const { username, newPassword } = req.body;
  const current = auth.readAdmin();
  if (newPassword && newPassword.length < 8) {
    return res.send(
      views.settingsPage(store.load().settings, current.username, {
        error: true,
        text: "Пароль должен быть не короче 8 символов. Логин/пароль не изменены.",
      })
    );
  }
  auth.setPassword(username || current.username, newPassword || null);
  req.session.username = username || current.username;
  res.send(
    views.settingsPage(store.load().settings, auth.readAdmin().username, {
      text: "Логин и пароль обновлены.",
    })
  );
});

router.post("/settings", (req, res) => {
  store
    .update((data) => {
      Object.assign(data.settings, {
        companyName: req.body.companyName,
        phone: req.body.phone,
        email: req.body.email,
        address_en: req.body.address_en,
        address_ru: req.body.address_ru,
        footerTag_en: req.body.footerTag_en,
        footerTag_ru: req.body.footerTag_ru,
        year: req.body.year,
        ctaTitle_en: req.body.ctaTitle_en,
        ctaTitle_ru: req.body.ctaTitle_ru,
        ctaBody_en: req.body.ctaBody_en,
        ctaBody_ru: req.body.ctaBody_ru,
        metaDescription: req.body.metaDescription,
      });
    })
    .then(() => res.send(views.settingsPage(store.load().settings, auth.readAdmin().username, { text: "Настройки сохранены." })));
});

// -------------------------------------------------------------------- leads --

router.get("/leads", (req, res) => {
  const data = store.load();
  res.send(views.leadsPage(data.leads));
  store.update((d) => {
    (d.leads || []).forEach((l) => (l.read = true));
  });
});

module.exports = router;
