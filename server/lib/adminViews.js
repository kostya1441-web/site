function esc(s) {
  if (s == null) return "";
  return String(s).replace(/"/g, "&quot;");
}
function escHtml(s) {
  if (s == null) return "";
  return String(s);
}

function shell({ title, active, body, flash }) {
  const navItems = [
    ["/admin/", "Дашборд"],
    ["/admin/pages/", "Страницы"],
    ["/admin/case-studies/", "Примеры проектов"],
    ["/admin/articles/", "Пресса и статьи"],
    ["/admin/settings", "Настройки и контакты"],
    ["/admin/leads", "Заявки"],
  ];
  const nav = navItems
    .map(
      ([href, label]) =>
        `<a href="${href}" class="${active === href ? "active" : ""}">${label}</a>`
    )
    .join("");
  return `<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${esc(title)} — Админка Hazleton Pumps</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin">
<div class="a-shell">
  <aside class="a-sidebar">
    <div class="brand"><img src="/assets/img/logo.svg" alt=""> Hazleton CMS</div>
    <nav>${nav}</nav>
    <div class="group-label">&nbsp;</div>
    <nav>
      <a href="/" target="_blank">↗ Открыть сайт</a>
      <a href="/admin/logout">Выйти</a>
    </nav>
  </aside>
  <main class="a-main">
    <div class="a-topbar"><h1>${esc(title)}</h1></div>
    <div class="a-content">
      ${flash ? `<div class="a-flash${flash.error ? " error" : ""}">${escHtml(flash.text)}</div>` : ""}
      ${body}
    </div>
  </main>
</div>
<script src="/assets/js/admin.js"></script>
</body>
</html>`;
}

function loginPage(error) {
  return `<!doctype html>
<html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Вход в админку — Hazleton Pumps</title>
<link rel="stylesheet" href="/assets/css/admin.css"></head>
<body class="admin">
<div class="login-wrap">
  <form class="login-card" method="post" action="/admin/login">
    <h1>Hazleton Pumps</h1>
    <p>Вход в панель администратора</p>
    ${error ? `<div class="a-flash error">${escHtml(error)}</div>` : ""}
    <div class="a-field"><label class="a-label">Логин</label><input type="text" name="username" required autofocus></div>
    <div class="a-field"><label class="a-label">Пароль</label><input type="password" name="password" required></div>
    <button class="a-btn accent" style="width:100%;justify-content:center" type="submit">Войти</button>
  </form>
</div>
</body></html>`;
}

// ---------------------------------------------------------- field helpers --

function textField(name, label, value, opts = {}) {
  return `<div class="a-field"><label class="a-label">${esc(label)}</label>
    <input type="${opts.type || "text"}" name="${esc(name)}" value="${esc(value)}" ${opts.required ? "required" : ""}></div>`;
}

function textareaField(name, label, value) {
  return `<div class="a-field"><label class="a-label">${esc(label)}</label>
    <textarea name="${esc(name)}">${escHtml(value || "")}</textarea></div>`;
}

// Appends `suffix` to the final path segment of a (possibly bracketed)
// field name, e.g. withSuffix("blocks[0][title]", "_en") ->
// "blocks[0][title_en]", withSuffix("title", "_en") -> "title_en".
// Appending after a closing "]" instead (e.g. "blocks[0][title]_en") looks
// equivalent but silently breaks: qs parses the bracket path up to the
// last "]" and drops everything after it, merging sibling en/ru fields
// into one key instead of two.
function withSuffix(path, suffix) {
  const m = /^(.*\[)([^[\]]*)(\])$/.exec(path);
  if (m) return `${m[1]}${m[2]}${suffix}${m[3]}`;
  return `${path}${suffix}`;
}

function biPair(nameBase, label, valueEn, valueRu, multiline = false) {
  const field = multiline ? textareaField : textField;
  return `<div class="a-grid">
    ${field(withSuffix(nameBase, "_en"), `${label} (EN)`, valueEn)}
    ${field(withSuffix(nameBase, "_ru"), `${label} (RU)`, valueRu)}
  </div>`;
}

// `prefix` is the bracket path of the block/item (e.g. "blocks[0]"), NOT
// including the field name itself — the __current/__upload suffix must be
// its own bracket segment ("blocks[0][image__current]"), never appended
// after a closing "]", or qs silently merges it into the sibling key.
function imageField(prefix, fieldKey, label, value) {
  const currentName = prefix ? `${prefix}[${fieldKey}__current]` : `${fieldKey}__current`;
  const uploadName = prefix ? `${prefix}[${fieldKey}__upload]` : `${fieldKey}__upload`;
  const previewId = "prev-" + `${prefix}-${fieldKey}`.replace(/[^a-zA-Z0-9]/g, "-");
  return `<div class="a-field">
    <label class="a-label">${esc(label)}</label>
    ${value ? `<img id="${previewId}" class="img-preview" src="${esc(value)}">` : `<img id="${previewId}" class="img-preview" style="display:none">`}
    <input type="hidden" name="${esc(currentName)}" value="${esc(value || "")}">
    <input type="file" name="${esc(uploadName)}" data-preview="${previewId}" accept="image/*">
    <p class="hint">Текущий файл: ${value ? esc(value) : "нет"}. Загрузите новый, чтобы заменить.</p>
  </div>`;
}

function checkbox(name, label, checked) {
  return `<label style="display:flex;gap:8px;align-items:center;font-size:0.85rem;margin-bottom:10px">
    <input type="checkbox" name="${esc(name)}" value="1" ${checked ? "checked" : ""}> ${esc(label)}
  </label>`;
}

function select(name, label, options, value) {
  const opts = options
    .map(([v, l]) => `<option value="${esc(v)}" ${v === value ? "selected" : ""}>${esc(l)}</option>`)
    .join("");
  return `<div class="a-field"><label class="a-label">${esc(label)}</label><select name="${esc(name)}">${opts}</select></div>`;
}

function repeater(id, label, rows, rowTemplateFn, emptyRowData) {
  const rowsHtml = rows.map((r, i) => rowTemplateFn(r, i)).join("");
  const emptyHtml = rowTemplateFn(emptyRowData, "__IDX__").replace(/__IDX__/g, "0");
  return `<div class="a-field">
    <label class="a-label">${esc(label)}</label>
    <div id="${id}">${rowsHtml}</div>
    <template id="${id}-template"><div class="repeater-row">${emptyHtml.replace(
      /<div class="repeater-row">|<\/div>$/g,
      ""
    )}<button type="button" class="a-btn small danger remove-row">Удалить</button></div></template>
    <button type="button" class="a-btn small ghost" data-add-row="${id}" style="margin-top:6px">+ Добавить строку</button>
  </div>`;
}

function itemRow(prefix, idx, item) {
  item = item || {};
  return `<div class="repeater-row">
    <div class="a-grid">
      ${textField(`${prefix}[${idx}][en]`, "Текст (EN)", item.en)}
      ${textField(`${prefix}[${idx}][ru]`, "Текст (RU)", item.ru)}
    </div>
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

function cardItemRow(prefix, idx, item) {
  item = item || {};
  const previewId = `prev-${prefix.replace(/[^a-zA-Z0-9]/g, "-")}-${idx}`;
  return `<div class="repeater-row">
    <div class="a-grid">
      ${textField(`${prefix}[${idx}][title_en]`, "Заголовок (EN)", item.title_en)}
      ${textField(`${prefix}[${idx}][title_ru]`, "Заголовок (RU)", item.title_ru)}
    </div>
    <div class="a-grid">
      ${textareaField(`${prefix}[${idx}][body_en]`, "Текст (EN)", item.body_en)}
      ${textareaField(`${prefix}[${idx}][body_ru]`, "Текст (RU)", item.body_ru)}
    </div>
    <div class="a-grid">
      <div>
        ${item.image ? `<img id="${previewId}" class="img-preview" src="${esc(item.image)}">` : `<img id="${previewId}" class="img-preview" style="display:none">`}
        <input type="hidden" name="${prefix}[${idx}][image__current]" value="${esc(item.image || "")}">
        <input type="file" name="${prefix}[${idx}][image__upload]" data-preview="${previewId}" accept="image/*">
      </div>
      <div>
        ${textField(`${prefix}[${idx}][link_href]`, "Ссылка (href)", item.link_href)}
        ${textField(`${prefix}[${idx}][link_en]`, "Текст ссылки (EN)", item.link_en)}
        ${textField(`${prefix}[${idx}][link_ru]`, "Текст ссылки (RU)", item.link_ru)}
      </div>
    </div>
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

function advantageRow(prefix, idx, item) {
  item = item || {};
  return `<div class="repeater-row">
    <div class="a-grid">
      ${textField(`${prefix}[${idx}][label_en]`, "Название (EN)", item.label_en)}
      ${textField(`${prefix}[${idx}][label_ru]`, "Название (RU)", item.label_ru)}
    </div>
    <div class="a-grid">
      ${textareaField(`${prefix}[${idx}][body_en]`, "Описание (EN)", item.body_en)}
      ${textareaField(`${prefix}[${idx}][body_ru]`, "Описание (RU)", item.body_ru)}
    </div>
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

function panelRow(prefix, idx, item) {
  item = item || {};
  return `<div class="repeater-row">
    <div class="a-grid">
      ${textField(`${prefix}[${idx}][title_en]`, "Заголовок (EN)", item.title_en)}
      ${textField(`${prefix}[${idx}][title_ru]`, "Заголовок (RU)", item.title_ru)}
    </div>
    <div class="a-grid">
      ${textareaField(`${prefix}[${idx}][body_en]`, "Текст (EN)", item.body_en)}
      ${textareaField(`${prefix}[${idx}][body_ru]`, "Текст (RU)", item.body_ru)}
    </div>
    ${checkbox(`${prefix}[${idx}][wide]`, "На всю ширину", item.wide)}
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

function logoItemRow(prefix, idx, item) {
  item = item || {};
  const previewId = `prev-${prefix.replace(/[^a-zA-Z0-9]/g, "-")}-${idx}`;
  return `<div class="repeater-row">
    ${item.image ? `<img id="${previewId}" class="img-preview" src="${esc(item.image)}">` : `<img id="${previewId}" class="img-preview" style="display:none">`}
    <input type="hidden" name="${prefix}[${idx}][image__current]" value="${esc(item.image || "")}">
    <input type="file" name="${prefix}[${idx}][image__upload]" data-preview="${previewId}" accept="image/*">
    ${textField(`${prefix}[${idx}][alt_en]`, "Подпись (alt)", item.alt_en)}
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

function clipRow(prefix, idx, item) {
  item = item || {};
  return `<div class="repeater-row">
    <div class="a-grid">
      ${textField(`${prefix}[${idx}][title_en]`, "Заголовок (EN)", item.title_en)}
      ${textField(`${prefix}[${idx}][title_ru]`, "Заголовок (RU)", item.title_ru)}
    </div>
    ${textField(`${prefix}[${idx}][source]`, "Источник", item.source)}
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

function buttonRow(prefix, idx, item) {
  item = item || {};
  return `<div class="repeater-row">
    <div class="a-grid">
      ${textField(`${prefix}[${idx}][label_en]`, "Текст кнопки (EN)", item.label_en)}
      ${textField(`${prefix}[${idx}][label_ru]`, "Текст кнопки (RU)", item.label_ru)}
    </div>
    ${textField(`${prefix}[${idx}][href]`, "Ссылка", item.href)}
    <button type="button" class="a-btn small danger remove-row">Удалить</button>
  </div>`;
}

const BLOCK_LABELS = {
  hero: "Шапка (Hero)",
  text: "Текстовый блок",
  textImage: "Текст + изображение",
  list: "Список",
  cards: "Карточки",
  panelGrid: "Панели (объекты/применение)",
  advantages: "Преимущества конфигурации",
  image: "Изображение",
  logos: "Логотипы партнёров",
  awards: "Награды",
  caseStudyGrid: "Сетка примеров проектов (авто)",
  articleGrid: "Список статей (авто)",
  pressClippings: "Упоминания в прессе",
  curveGroups: "Группы напорных характеристик",
  contactBlock: "Контакты + форма заявки",
  backLink: "Ссылка «Назад»",
};

function blockFields(block, idx) {
  const p = `blocks[${idx}]`;
  switch (block.type) {
    case "hero":
      return `
        ${biPair(`${p}[eyebrow]`, "Надпись над заголовком", block.eyebrow_en, block.eyebrow_ru)}
        ${biPair(`${p}[title]`, "Заголовок", block.title_en, block.title_ru)}
        ${biPair(`${p}[body]`, "Текст", block.body_en, block.body_ru, true)}
        ${imageField(p, "image", "Изображение", block.image)}
        ${repeater(`rep-${idx}-buttons`, "Кнопки", block.buttons || [], (it, i) => buttonRow(`${p}[buttons]`, i, it), {})}
      `;
    case "text":
      return `
        ${biPair(`${p}[eyebrow]`, "Надпись над заголовком", block.eyebrow_en, block.eyebrow_ru)}
        ${biPair(`${p}[title]`, "Заголовок (необязательно)", block.title_en, block.title_ru)}
        ${biPair(`${p}[body]`, "Текст (HTML разрешён)", block.body_en, block.body_ru, true)}
        ${checkbox(`${p}[alt]`, "Светлый фон", block.alt)}
        ${checkbox(`${p}[center]`, "По центру", block.center)}
      `;
    case "textImage":
      return `
        ${biPair(`${p}[title]`, "Заголовок (необязательно)", block.title_en, block.title_ru)}
        ${biPair(`${p}[body]`, "Текст (HTML разрешён)", block.body_en, block.body_ru, true)}
        ${imageField(p, "image", "Изображение", block.image)}
        ${select(`${p}[imageSide]`, "Сторона изображения", [["right", "Справа"], ["left", "Слева"]], block.imageSide || "right")}
        ${checkbox(`${p}[alt]`, "Светлый фон", block.alt)}
      `;
    case "list":
      return `
        ${biPair(`${p}[title]`, "Заголовок (необязательно)", block.title_en, block.title_ru)}
        ${select(`${p}[style]`, "Стиль", [["spec", "Галочки"], ["tag", "Плашки"]], block.style || "spec")}
        ${repeater(`rep-${idx}-items`, "Пункты списка", block.items || [], (it, i) => itemRow(`${p}[items]`, i, it), {})}
      `;
    case "cards":
      return `
        ${biPair(`${p}[eyebrow]`, "Надпись над заголовком", block.eyebrow_en, block.eyebrow_ru)}
        ${biPair(`${p}[title]`, "Заголовок (необязательно)", block.title_en, block.title_ru)}
        <div class="a-grid">
          ${select(`${p}[variant]`, "Вид карточек", [["plain", "Простые"], ["feature", "Преимущества (тёмные)"], ["product", "Товар (с фото и кнопкой)"]], block.variant || "plain")}
          ${select(`${p}[columns]`, "Колонок", [["2", "2"], ["3", "3"], ["4", "4"]], String(block.columns || 3))}
        </div>
        ${checkbox(`${p}[alt]`, "Светлый фон", block.alt)}
        ${repeater(`rep-${idx}-items`, "Карточки", block.items || [], (it, i) => cardItemRow(`${p}[items]`, i, it), {})}
      `;
    case "panelGrid":
      return repeater(`rep-${idx}-items`, "Панели", block.items || [], (it, i) => panelRow(`${p}[items]`, i, it), {});
    case "advantages":
      return `
        ${biPair(`${p}[title]`, "Заголовок списка преимуществ", block.title_en, block.title_ru)}
        ${repeater(`rep-${idx}-items`, "Преимущества", block.items || [], (it, i) => advantageRow(`${p}[items]`, i, it), {})}
      `;
    case "image":
      return `
        ${imageField(p, "image", "Изображение", block.image)}
        ${biPair(`${p}[caption]`, "Подпись (необязательно)", block.caption_en, block.caption_ru)}
        ${checkbox(`${p}[alt]`, "Светлый фон", block.alt)}
      `;
    case "logos":
      return `
        ${biPair(`${p}[title]`, "Заголовок (необязательно)", block.title_en, block.title_ru)}
        ${repeater(`rep-${idx}-items`, "Логотипы", block.items || [], (it, i) => logoItemRow(`${p}[items]`, i, it), {})}
      `;
    case "awards":
      return repeater(`rep-${idx}-items`, "Награды", block.items || [], (it, i) => cardItemRow(`${p}[items]`, i, it), {});
    case "pressClippings":
      return `
        ${biPair(`${p}[title]`, "Заголовок", block.title_en, block.title_ru)}
        ${repeater(`rep-${idx}-items`, "Упоминания", block.items || [], (it, i) => clipRow(`${p}[items]`, i, it), {})}
      `;
    case "caseStudyGrid":
    case "articleGrid":
      return `
        ${biPair(`${p}[title]`, "Заголовок (необязательно)", block.title_en, block.title_ru)}
        ${checkbox(`${p}[alt]`, "Светлый фон", block.alt)}
        <p class="hint">Карточки подтягиваются автоматически из раздела «${block.type === "caseStudyGrid" ? "Примеры проектов" : "Пресса и статьи"}».</p>
      `;
    case "backLink":
      return `
        ${textField(`${p}[href]`, "Ссылка", block.href)}
        ${textField(`${p}[label_en]`, "Текст (EN)", block.label_en)}
        ${textField(`${p}[label_ru]`, "Текст (RU)", block.label_ru)}
      `;
    case "contactBlock":
      return `<p class="hint">Этот блок автоматически использует телефон, email и адрес из раздела «Настройки и контакты», плюс форму заявки.</p>`;
    case "curveGroups":
      return curveGroupsFields(block, p);
    default:
      return `<p class="hint">Для этого типа блока нет формы — отредактируйте JSON напрямую.</p>
        ${textareaField(`${p}[__json]`, "Raw JSON", JSON.stringify(block, null, 2))}`;
  }
}

function curveGroupsFields(block, p) {
  const groups = block.groups || [];
  const rows = groups
    .map((g, gi) => {
      const items = g.items || [];
      const itemRows = items
        .map((it, ii) => {
          const previewId = `prev-${p.replace(/[^a-zA-Z0-9]/g, "-")}-${gi}-${ii}`;
          return `<div class="repeater-row">
          ${it.image ? `<img id="${previewId}" class="img-preview" src="${esc(it.image)}">` : `<img id="${previewId}" class="img-preview" style="display:none">`}
          <input type="hidden" name="${p}[groups][${gi}][items][${ii}][image__current]" value="${esc(it.image || "")}">
          <input type="file" name="${p}[groups][${gi}][items][${ii}][image__upload]" data-preview="${previewId}" accept="image/*">
          ${textField(`${p}[groups][${gi}][items][${ii}][title_en]`, "Название (EN)", it.title_en)}
          ${textField(`${p}[groups][${gi}][items][${ii}][title_ru]`, "Название (RU)", it.title_ru)}
        </div>`;
        })
        .join("");
      return `<div class="a-block">
        <div class="a-grid">
          ${textField(`${p}[groups][${gi}][title_en]`, "Группа — заголовок (EN)", g.title_en)}
          ${textField(`${p}[groups][${gi}][title_ru]`, "Группа — заголовок (RU)", g.title_ru)}
        </div>
        <label class="a-label">Кривые в этой группе</label>
        ${itemRows}
      </div>`;
    })
    .join("");
  return `<p class="hint">Группы и кривые этого раздела отредактируйте через Raw JSON — это самый предсказуемый способ для сложной вложенной структуры.</p>
    ${textareaField(`${p}[__json]`, "Raw JSON (groups)", JSON.stringify({ groups }, null, 2))}
    <div class="hint">Текущее содержимое (только просмотр):</div>
    ${rows}`;
}

function pageEditorPage(pathKey, page, flash) {
  const blocksHtml = (page.blocks || [])
    .map(
      (b, i) => `<div class="a-block">
      <div class="a-block-head">
        <span class="type">${esc(BLOCK_LABELS[b.type] || b.type)}</span>
        <label style="font-size:0.8rem"><input type="checkbox" name="blocks[${i}][__delete]" value="1"> Удалить блок</label>
      </div>
      <input type="hidden" name="blocks[${i}][type]" value="${esc(b.type)}">
      ${blockFields(b, i)}
    </div>`
    )
    .join("");

  const addTypeOptions = Object.entries(BLOCK_LABELS)
    .map(([v, l]) => `<option value="${v}">${l}</option>`)
    .join("");

  const body = `
  <form method="post" enctype="multipart/form-data">
    <div class="a-card">
      <h2>Заголовок страницы</h2>
      ${biPair("eyebrow", "Надпись над заголовком", page.eyebrow_en, page.eyebrow_ru)}
      ${biPair("title", "Заголовок страницы (H1)", page.title_en, page.title_ru)}
    </div>
    <div class="a-card">
      <h2>Блоки содержимого</h2>
      ${blocksHtml || '<p class="hint">Пока нет блоков.</p>'}
      <div class="add-type-row">
        <select name="__newBlockType">${addTypeOptions}</select>
        <button class="a-btn ghost small" type="submit" name="__action" value="addBlock">+ Добавить блок</button>
      </div>
    </div>
    <div class="a-row">
      <button class="a-btn accent" type="submit" name="__action" value="save">Сохранить изменения</button>
      <a class="a-btn ghost" href="/${pathKey}/" target="_blank">Открыть страницу на сайте</a>
    </div>
  </form>`;
  return shell({ title: `Страница: ${page.title_en || pathKey}`, active: "/admin/pages/", body, flash });
}

function pagesListPage(pages) {
  const rows = Object.entries(pages)
    .map(
      ([key, p]) => `<tr>
      <td>${esc(p.title_en || key)}</td>
      <td><code>/${esc(key)}/</code></td>
      <td><a class="a-btn small" href="/admin/pages/${encodeURIComponent(key)}">Редактировать</a></td>
    </tr>`
    )
    .join("");
  const body = `<div class="a-card">
    <table class="a-table"><thead><tr><th>Страница</th><th>URL</th><th></th></tr></thead><tbody>${rows}</tbody></table>
  </div>`;
  return shell({ title: "Страницы сайта", active: "/admin/pages/", body });
}

function dashboardPage(data) {
  const body = `
  <div class="a-grid">
    <div class="a-card">
      <h2>Сайт</h2>
      <p>${Object.keys(data.pages).length} страниц · ${data.caseStudies.length} примеров проектов · ${data.articles.length} статей</p>
      <a class="a-btn accent" href="/" target="_blank">Открыть сайт</a>
    </div>
    <div class="a-card">
      <h2>Новые заявки</h2>
      <p>${(data.leads || []).filter((l) => !l.read).length} непрочитанных из ${(data.leads || []).length} всего</p>
      <a class="a-btn" href="/admin/leads">Посмотреть заявки</a>
    </div>
  </div>
  <div class="a-card">
    <h2>Быстрые ссылки</h2>
    <div class="a-row">
      <a class="a-btn ghost" href="/admin/pages/">Редактировать страницы</a>
      <a class="a-btn ghost" href="/admin/case-studies/">Примеры проектов</a>
      <a class="a-btn ghost" href="/admin/articles/">Пресса и статьи</a>
      <a class="a-btn ghost" href="/admin/settings">Контакты и настройки</a>
    </div>
  </div>`;
  return shell({ title: "Дашборд", active: "/admin/", body });
}

function settingsPage(settings, adminUsername, flash) {
  const body = `<form method="post">
    <div class="a-card">
      <h2>Контакты</h2>
      ${textField("companyName", "Название компании", settings.companyName)}
      ${textField("phone", "Телефон", settings.phone)}
      ${textField("email", "Email", settings.email)}
      ${biPair("address", "Адрес", settings.address_en, settings.address_ru)}
    </div>
    <div class="a-card">
      <h2>Футер</h2>
      ${biPair("footerTag", "Текст под логотипом в футере", settings.footerTag_en, settings.footerTag_ru)}
      ${textField("year", "Год копирайта", settings.year)}
    </div>
    <div class="a-card">
      <h2>Блок «Поможем подобрать решение» (в конце каждой страницы)</h2>
      ${biPair("ctaTitle", "Заголовок", settings.ctaTitle_en, settings.ctaTitle_ru)}
      ${biPair("ctaBody", "Текст", settings.ctaBody_en, settings.ctaBody_ru, true)}
    </div>
    <div class="a-card">
      <h2>SEO</h2>
      ${textareaField("metaDescription", "Meta description", settings.metaDescription)}
    </div>
    <button class="a-btn accent" type="submit">Сохранить</button>
  </form>
  <div class="a-card">
    <h2>Логин и пароль администратора</h2>
    <form method="post" action="/admin/account">
      ${textField("username", "Логин", adminUsername)}
      ${textField("newPassword", "Новый пароль (оставьте пустым, чтобы не менять)", "", { type: "password" })}
      <button class="a-btn" type="submit">Обновить доступ</button>
    </form>
  </div>`;
  return shell({ title: "Настройки и контакты", active: "/admin/settings", body, flash });
}

function caseStudiesListPage(items) {
  const rows = items
    .map(
      (c) => `<tr>
      <td><img class="a-thumb" src="${esc(c.image)}"></td>
      <td>${esc(c.title_en)}</td>
      <td><code>${esc(c.slug)}</code></td>
      <td class="a-row">
        <a class="a-btn small" href="/admin/case-studies/${encodeURIComponent(c.slug)}">Редактировать</a>
        <form method="post" action="/admin/case-studies/${encodeURIComponent(c.slug)}/delete" onsubmit="return confirm('Удалить пример проекта?')">
          <button class="a-btn small danger" type="submit">Удалить</button>
        </form>
      </td>
    </tr>`
    )
    .join("");
  const body = `<div class="a-row" style="margin-bottom:16px"><a class="a-btn accent" href="/admin/case-studies/new">+ Новый пример проекта</a></div>
  <div class="a-card"><table class="a-table"><thead><tr><th>Фото</th><th>Название</th><th>Slug</th><th></th></tr></thead><tbody>${rows}</tbody></table></div>`;
  return shell({ title: "Примеры проектов", active: "/admin/case-studies/", body });
}

function caseStudyEditorPage(cs, isNew, flash) {
  const items = cs.solution || [];
  const body = `<form method="post" enctype="multipart/form-data">
    <div class="a-card">
      ${textField("slug", "Slug (часть URL, латиницей, без пробелов)", cs.slug, { required: true })}
      ${biPair("title", "Название", cs.title_en, cs.title_ru)}
      ${imageField("", "image", "Фото", cs.image)}
      ${biPair("challenge", "Задача (необязательно)", cs.challenge_en, cs.challenge_ru, true)}
      ${repeater("rep-solution", "Решение (пункты списка)", items, (it, i) => itemRow("solution", i, it), {})}
    </div>
    <button class="a-btn accent" type="submit">Сохранить</button>
  </form>`;
  return shell({ title: isNew ? "Новый пример проекта" : cs.title_en, active: "/admin/case-studies/", body, flash });
}

function articlesListPage(items) {
  const rows = items
    .map(
      (a) => `<tr>
      <td><img class="a-thumb" src="${esc(a.image)}"></td>
      <td>${esc(a.title_en)}</td>
      <td>${esc(a.date_en)}</td>
      <td class="a-row">
        <a class="a-btn small" href="/admin/articles/${encodeURIComponent(a.slug)}">Редактировать</a>
        <form method="post" action="/admin/articles/${encodeURIComponent(a.slug)}/delete" onsubmit="return confirm('Удалить статью?')">
          <button class="a-btn small danger" type="submit">Удалить</button>
        </form>
      </td>
    </tr>`
    )
    .join("");
  const body = `<div class="a-row" style="margin-bottom:16px"><a class="a-btn accent" href="/admin/articles/new">+ Новая статья</a></div>
  <div class="a-card"><table class="a-table"><thead><tr><th>Фото</th><th>Заголовок</th><th>Дата</th><th></th></tr></thead><tbody>${rows}</tbody></table></div>`;
  return shell({ title: "Пресса и статьи", active: "/admin/articles/", body });
}

function articleEditorPage(a, isNew, flash) {
  const body = `<form method="post" enctype="multipart/form-data">
    <div class="a-card">
      ${textField("slug", "Slug (часть URL)", a.slug, { required: true })}
      ${biPair("title", "Заголовок", a.title_en, a.title_ru)}
      ${biPair("date", "Дата (как текст)", a.date_en, a.date_ru)}
      ${imageField("", "image", "Фото", a.image)}
      ${biPair("teaser", "Короткий анонс (для списка)", a.teaser_en, a.teaser_ru, true)}
      ${biPair("body", "Текст статьи (HTML разрешён, можно использовать <h3>, <p>)", a.body_en, a.body_ru, true)}
    </div>
    <button class="a-btn accent" type="submit">Сохранить</button>
  </form>`;
  return shell({ title: isNew ? "Новая статья" : a.title_en, active: "/admin/articles/", body, flash });
}

function leadsPage(leads) {
  const rows = (leads || [])
    .map(
      (l) => `<tr style="${l.read ? "" : "font-weight:700"}">
      <td>${esc(new Date(l.receivedAt).toLocaleString("ru-RU"))}</td>
      <td>${esc(l.name)}</td>
      <td><a href="mailto:${esc(l.email)}">${esc(l.email)}</a></td>
      <td>${esc(l.phone || "")}</td>
      <td>${esc(l.company || "")}</td>
      <td style="max-width:320px">${esc(l.message)}</td>
    </tr>`
    )
    .join("");
  const body = `<div class="a-card"><table class="a-table">
    <thead><tr><th>Дата</th><th>Имя</th><th>Email</th><th>Телефон</th><th>Компания</th><th>Сообщение</th></tr></thead>
    <tbody>${rows || '<tr><td colspan="6">Пока нет заявок.</td></tr>'}</tbody>
  </table></div>`;
  return shell({ title: "Заявки с сайта", active: "/admin/leads", body });
}

module.exports = {
  shell,
  loginPage,
  dashboardPage,
  pagesListPage,
  pageEditorPage,
  settingsPage,
  caseStudiesListPage,
  caseStudyEditorPage,
  articlesListPage,
  articleEditorPage,
  leadsPage,
  BLOCK_LABELS,
};
