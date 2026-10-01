#!/usr/bin/env python3
"""Generates the static, bilingual (EN/RU) Hazleton Pumps site.

Single source of truth for content lives in this file. Run:
    python3 tools/generate_site.py
from the repo root; it (re)writes every index.html under the site root.
Shared assets (css/js/images) are hand-maintained in assets/ and are not
touched by this script.
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

COMPANY_PHONE = "+27 (0) 12 666 8203"
COMPANY_EMAIL = "info@hazletonpumps.co.za"
YEAR = "2026"

# ---------------------------------------------------------------- helpers --

def bi(en, ru, tag="span", cls=""):
    """Inline bilingual fragment: both languages are emitted, CSS hides one."""
    klass = f' class="{cls}"' if cls else ""
    return (
        f'<{tag}{klass} data-i18n-group="en">{en}</{tag}>'
        f'<{tag}{klass} data-i18n-group="ru">{ru}</{tag}>'
    )


def bi_block(en_html, ru_html, tag="div", cls=""):
    """Block-level bilingual fragment for multi-element HTML (lists, paras)."""
    klass = f' class="{cls}"' if cls else ""
    return (
        f'<{tag}{klass} data-i18n-group="en">{en_html}</{tag}>'
        f'<{tag}{klass} data-i18n-group="ru">{ru_html}</{tag}>'
    )


def ul(items_en, items_ru, cls="spec-list"):
    li_en = "".join(f"<li>{t}</li>" for t in items_en)
    li_ru = "".join(f"<li>{t}</li>" for t in items_ru)
    return bi_block(f"<ul class='{cls}'>{li_en}</ul>", f"<ul class='{cls}'>{li_ru}</ul>", tag="div")


# ------------------------------------------------------------------- nav --

NAV = [
    {"en": "Home", "ru": "Главная", "href": "/"},
    {
        "en": "About Us", "ru": "О компании", "href": "/history/",
        "children": [
            {"en": "History", "ru": "История", "href": "/history/"},
            {"en": "Press Releases & Articles", "ru": "Пресс-релизы и статьи", "href": "/press-releases/"},
            {"en": "Awards", "ru": "Награды", "href": "/awards/"},
        ],
    },
    {
        "en": "Pump Systems", "ru": "Насосные системы", "href": "/pump-systems/",
        "children": [
            {"en": "Overview", "ru": "Обзор", "href": "/pump-systems/"},
            {"en": "Cyclone Solids Separation", "ru": "Циклонное отделение твёрдых частиц", "href": "/pump-systems/cyclone-solids-separation-submersible-pumping-system/"},
            {"en": "High Head Slurry Series", "ru": "Серия высоконапорных шламовых систем", "href": "/pump-systems/high-head-slurry-series-pumping-system/"},
        ],
    },
    {
        "en": "Hippo Range", "ru": "Линейка Hippo", "href": "/hippo-range/",
        "children": [
            {"en": "Overview", "ru": "Обзор", "href": "/hippo-range/"},
            {"en": "Submersibles", "ru": "Погружные насосы", "href": "/hippo-range/submersibles/"},
            {"en": "Verticals", "ru": "Вертикальные насосы", "href": "/hippo-range/verticals/"},
        ],
    },
    {
        "en": "Resources", "ru": "Ресурсы", "href": "/case-studies/",
        "children": [
            {"en": "Case Studies", "ru": "Примеры проектов", "href": "/case-studies/"},
            {"en": "Pump Curves", "ru": "Напорные характеристики", "href": "/pump-curves/"},
        ],
    },
    {"en": "Contact", "ru": "Контакты", "href": "/contact/"},
]


def render_nav():
    out = []
    for item in NAV:
        label = bi(item["en"], item["ru"])
        if "children" in item:
            kids = "".join(
                f'<li><a href="{c["href"]}">{bi(c["en"], c["ru"])}</a></li>' for c in item["children"]
            )
            out.append(
                f'<li><a href="{item["href"]}" class="nav-top">{label}</a>'
                f'<ul class="dropdown">{kids}</ul></li>'
            )
        else:
            out.append(f'<li><a href="{item["href"]}">{label}</a></li>')
    return "".join(out)


# ---------------------------------------------------------------- layout --

def layout(slug_title_en, slug_title_ru, body, breadcrumb=None, canonical="/"):
    crumb_html = ""
    if breadcrumb:
        parts = []
        for i, (en, ru, href) in enumerate(breadcrumb):
            if href:
                parts.append(f'<a href="{href}">{bi(en, ru)}</a>')
            else:
                parts.append(bi(en, ru))
        crumb_html = f'<div class="breadcrumb">{" / ".join(parts)}</div>'

    return f"""<!doctype html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{slug_title_en} | Hazleton Pumps</title>
<meta name="description" content="Hazleton Pumps — custom-engineered heavy-duty slurry pumps and pump systems, designed and manufactured in South Africa.">
<link rel="canonical" href="{canonical}">
<link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
<link rel="stylesheet" href="/assets/css/style.css">
<script>
(function(){{try{{var l=localStorage.getItem('hazleton-lang');if(l==='ru'){{document.documentElement.setAttribute('data-lang','ru');document.documentElement.setAttribute('lang','ru');}}}}catch(e){{}}}})();
</script>
</head>
<body>
<div class="topbar">
  <div class="container">
    <div class="contacts">
      <a href="tel:{COMPANY_PHONE.replace(' ', '')}">T: {COMPANY_PHONE}</a>
      <a href="mailto:{COMPANY_EMAIL}">E: {COMPANY_EMAIL}</a>
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
      <img src="/assets/img/logo.svg" alt="Hazleton Pumps logo">
      <span class="brand-name">Hazleton Pumps<small>{bi("Intl (Pty) Ltd", "Intl (Pty) Ltd · Южная Африка")}</small></span>
    </a>
    <nav class="main-nav"><ul>{render_nav()}</ul></nav>
    <div class="nav-actions">
      <a class="btn-quote" href="/contact/">{bi("Request a Quote", "Запросить КП")}</a>
      <button class="nav-toggle" aria-label="Menu">&#9776;</button>
    </div>
  </div>
</header>
{crumb_html and f'<div class="page-head-crumb" style="background:var(--navy)"><div class="container" style="padding-top:14px;padding-bottom:0">{crumb_html}</div></div>' or ""}
{body}
<footer class="site-footer">
  <div class="container">
    <div class="grid">
      <div>
        <div class="brand" style="color:#fff">
          <img src="/assets/img/logo.svg" alt="Hazleton Pumps" style="height:34px">
        </div>
        {bi_block(
            "<p class='tag'>Hazleton Pumps develops and manufactures heavy-duty slurry pumps and pump systems, custom built in South Africa and deployed worldwide.</p>",
            "<p class='tag'>Hazleton Pumps разрабатывает и производит тяжёлые шламовые насосы и насосные системы, изготавливаемые на заказ в Южной Африке и поставляемые по всему миру.</p>",
        )}
      </div>
      <div>
        <h4>{bi("Company", "Компания")}</h4>
        <ul>
          <li><a href="/history/">{bi("History", "История")}</a></li>
          <li><a href="/awards/">{bi("Awards", "Награды")}</a></li>
          <li><a href="/press-releases/">{bi("Press & Articles", "Пресса и статьи")}</a></li>
        </ul>
      </div>
      <div>
        <h4>{bi("Products", "Продукция")}</h4>
        <ul>
          <li><a href="/hippo-range/">{bi("Hippo Range", "Линейка Hippo")}</a></li>
          <li><a href="/pump-systems/">{bi("Pump Systems", "Насосные системы")}</a></li>
          <li><a href="/pump-curves/">{bi("Pump Curves", "Напорные характеристики")}</a></li>
          <li><a href="/case-studies/">{bi("Case Studies", "Примеры проектов")}</a></li>
        </ul>
      </div>
      <div>
        <h4>{bi("Contact", "Контакты")}</h4>
        <ul>
          <li>{bi("33 Van Tonder Street", "ул. Ван Тондер, 33")}</li>
          <li>{bi("Sunderland Ridge, Centurion, South Africa", "Сандерленд-Ридж, Сентурион, ЮАР")}</li>
          <li><a href="tel:{COMPANY_PHONE.replace(' ', '')}">{COMPANY_PHONE}</a></li>
          <li><a href="mailto:{COMPANY_EMAIL}">{COMPANY_EMAIL}</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>{bi(f"Copyright © {YEAR} Hazleton Pumps", f"© {YEAR} Hazleton Pumps. Все права защищены")}</span>
      <span>{bi("Original: hazletonpumps.co.za", "Оригинал сайта: hazletonpumps.co.za")}</span>
    </div>
  </div>
</footer>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
"""


def cta_strip():
    return f"""
<section class="tight">
  <div class="container">
    <div class="cta-strip">
      <div>
        <h3>{bi("Let us help you configure a solution!", "Поможем подобрать решение!")}</h3>
        {bi_block(
            "<p>Our pump solutions can be custom made to your pumping requirements. Don't see a solution to your current pumping problem?</p>",
            "<p>Наши насосные решения изготавливаются индивидуально под задачи заказчика. Не нашли готовое решение для своей задачи?</p>",
        )}
      </div>
      <a class="btn" href="/contact/">{bi("Contact Us", "Связаться с нами")}</a>
    </div>
  </div>
</section>
"""


_ROOT_LINK_RE = re.compile(r'(href|src)="(/[^"]*)"')


def _relativize(html, depth):
    """Rewrite root-relative URLs (/assets/..., /history/, /) into relative
    paths with explicit index.html targets, so the site also works opened
    directly from disk via file:// (no web server resolving directories)."""
    prefix = ("../" * depth) if depth else "./"

    def repl(m):
        attr, url = m.group(1), m.group(2)
        inner = url[1:]
        if inner == "":
            target = "index.html"
        elif inner.endswith("/"):
            target = inner + "index.html"
        else:
            target = inner
        return f'{attr}="{prefix}{target}"'

    return _ROOT_LINK_RE.sub(repl, html)


def write(path, html):
    depth = path.count("/")
    html = _relativize(html, depth)
    full = os.path.join(ROOT, path)
    os.makedirs(os.path.dirname(full), exist_ok=True)
    with open(full, "w", encoding="utf-8") as f:
        f.write(html)
    print("wrote", path)


# ===========================================================================
# HOME
# ===========================================================================

def page_home():
    body = f"""
<section class="hero">
  <div class="container">
    <div>
      <p class="eyebrow" style="color:#e8a768">{bi("Custom Technology · Incomparable Quality", "Технологии под заказ · Непревзойдённое качество")}</p>
      <h1>{bi("Getting to the bottom, faster!", "Быстрее добираемся до дна!")}</h1>
      {bi_block(
          "<p>Hazleton Pumps develops and manufactures heavy-duty pumps and pump systems, custom built and designed for our clients' specific needs and requirements. Proudly made in South Africa and deployed worldwide.</p>",
          "<p>Hazleton Pumps разрабатывает и производит тяжёлые насосы и насосные системы, создаваемые по индивидуальному проекту под конкретные задачи заказчика. С гордостью производится в Южной Африке и поставляется по всему миру.</p>",
      )}
      <div style="display:flex;gap:14px;flex-wrap:wrap">
        <a class="btn" href="/contact/">{bi("Request a Quote", "Запросить КП")}</a>
        <a class="btn ghost" href="/hippo-range/">{bi("Explore the Hippo Range", "Линейка Hippo")}</a>
      </div>
    </div>
    <div class="hero-art"><img src="/assets/img/hippo-pump.png" alt="Hippo heavy-duty slurry pump"></div>
  </div>
</section>

<section class="alt tight">
  <div class="container">
    {bi_block(
        "<p class='lede' style='margin:0 auto;text-align:center;max-width:70ch'>Hazleton Pumps can provide spare parts and services to all Hazleton Pumps products and systems. This includes pumps originally manufactured in North America and Canada.</p>",
        "<p class='lede' style='margin:0 auto;text-align:center;max-width:70ch'>Hazleton Pumps поставляет запасные части и выполняет сервисное обслуживание для всей продукции и систем Hazleton Pumps, включая насосы, изначально изготовленные в Северной Америке и Канаде.</p>",
    )}
  </div>
</section>

<section>
  <div class="container">
    <p class="eyebrow">{bi("Why Hazleton", "Почему Hazleton")}</p>
    <h2 class="section-title">{bi("Our competitive advantage", "Наши конкурентные преимущества")}</h2>
    <div class="grid cols-3" style="margin-top:28px">
      <div class="feature-card">
        <div class="num">01</div>
        <h3>{bi("Customised Technology", "Технологии под заказ")}</h3>
        {bi_block(
            "<p>Hazleton Pump systems are custom built and designed with pumps from the Hippo Slurry Pump Range, for each of our clients' specific needs.</p>",
            "<p>Насосные системы Hazleton собираются и проектируются с использованием насосов линейки Hippo под конкретные требования каждого клиента.</p>",
        )}
      </div>
      <div class="feature-card">
        <div class="num">02</div>
        <h3>{bi("Adaptive Materials", "Адаптируемые материалы")}</h3>
        {bi_block(
            "<p>Materials can be adjusted according to the type of application, from standard alloys to flameproof stainless steel.</p>",
            "<p>Материалы подбираются под тип применения — от стандартных сплавов до взрывозащищённой нержавеющей стали.</p>",
        )}
      </div>
      <div class="feature-card">
        <div class="num">03</div>
        <h3>{bi("Variable Capabilities", "Гибкие характеристики")}</h3>
        {bi_block(
            "<p>Motors can be selected depending on the voltage and pole speeds required for the application.</p>",
            "<p>Электродвигатели подбираются в зависимости от требуемого напряжения и частоты вращения.</p>",
        )}
      </div>
    </div>
  </div>
</section>

<section class="alt">
  <div class="container">
    <p class="eyebrow">{bi("What we build", "Что мы производим")}</p>
    <h2 class="section-title">{bi("Pump Systems", "Насосные системы")}</h2>
    <div class="grid cols-2" style="margin-top:28px">
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/solids-separation-system.svg" alt="Cyclone solid separation pump system"></div>
        <div class="body">
          <h3>{bi("Cyclone Solid Separation Pump System", "Циклонная система отделения твёрдых частиц")}</h3>
          {bi_block(
              "<p>Ideal for applications where fluids with solids need to be separated from the liquid. Different materials are used depending on whether the fluid contains hardened solids or abrasive liquids.</p>",
              "<p>Идеально подходит для задач, где твёрдые частицы необходимо отделить от жидкости. Материалы подбираются в зависимости от того, содержит ли среда затвердевшие частицы или абразивные жидкости.</p>",
          )}
          <a class="btn dark" href="/pump-systems/cyclone-solids-separation-submersible-pumping-system/">{bi("Find out more", "Подробнее")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/high-head-system.svg" alt="High head slurry series pumping system"></div>
        <div class="body">
          <h3>{bi("High Head Slurry Series Pumping System", "Серия высоконапорных шламовых систем")}</h3>
          {bi_block(
              "<p>For very deep pumping applications, able to pump heads of 250m per stage, through multiple stages. The system can be configured to the application depth, going as deep as required.</p>",
              "<p>Для задач с большой глубиной откачки: напор до 250 м на ступень, возможно несколько ступеней. Система конфигурируется под глубину объекта — настолько глубоко, насколько требуется.</p>",
          )}
          <a class="btn dark" href="/pump-systems/high-head-slurry-series-pumping-system/">{bi("Find out more", "Подробнее")}</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <p class="eyebrow">{bi("Flagship product", "Флагманский продукт")}</p>
    <h2 class="section-title">{bi("Hippo Range", "Линейка Hippo")}</h2>
    {bi_block(
        "<p class='lede'><strong>Your ally in pumping corrosive and acidic slurries.</strong> The award-winning Hippo Slurry Pump Range is the workhorse for the continuous, harsh demands of mining and mineral processing. Custom built, it provides pumping solutions that are robust, rugged, reliable and flexible — from high-volume dewatering to settled-out acidic and corrosive slurries. The range can be built to explosion-proof standard IEC 60079-1:2005 and is available in various formats and applications.</p>",
        "<p class='lede'><strong>Ваш надёжный партнёр в перекачке коррозионных и кислотных шламов.</strong> Удостоенная наград линейка шламовых насосов Hippo — это рабочая лошадка для непрерывных и тяжёлых условий горнодобывающей и перерабатывающей промышленности. Насосы изготавливаются по индивидуальному проекту и обеспечивают надёжные, прочные и гибкие решения — от высокообъёмного водоотлива до осевших кислотных и коррозионных шламов. Линейка может изготавливаться во взрывозащищённом исполнении по стандарту IEC 60079-1:2005 и доступна в различных конфигурациях.</p>",
    )}
    <div class="grid cols-2" style="margin-top:28px">
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/hippo-pump-top-suction.png" alt="Hippo submersible slurry pump"></div>
        <div class="body">
          <h3>{bi("Submersibles", "Погружные насосы")}</h3>
          {bi_block(
              "<p>All-metal heavy-duty submersible slurry pumps, capable of pumping abrasive and corrosive liquids with capacities up to 1500 l/s and heads up to 200 m.</p>",
              "<p>Цельнометаллические погружные шламовые насосы повышенной прочности для перекачки абразивных и коррозионных жидкостей, производительность до 1500 л/с, напор до 200 м.</p>",
          )}
          <a class="btn dark" href="/hippo-range/submersibles/">{bi("Configurations", "Конфигурации")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/hippo-vb-range.png" alt="Hippo vertical spindle pump range"></div>
        <div class="body">
          <h3>{bi("Verticals", "Вертикальные насосы")}</h3>
          {bi_block(
              "<p>The world's most versatile vertical spindle pump range — Bottom Suction, Bottom Discharge, Top Suction and Vortex configurations, heads up to 110 m and flow up to 1500 l/s.</p>",
              "<p>Самая универсальная в мире линейка вертикальных шпиндельных насосов — конфигурации с нижним всасыванием, нижним нагнетанием, верхним всасыванием и вихревая — напор до 110 м, расход до 1500 л/с.</p>",
          )}
          <a class="btn dark" href="/hippo-range/verticals/">{bi("Configurations", "Конфигурации")}</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="alt">
  <div class="container">
    <p class="eyebrow">{bi("Made in South Africa, relied on worldwide", "Производится в ЮАР, применяется по всему миру")}</p>
    <div class="two-col">
      {bi_block(
          "<p class='lede'>The Hippo Slurry Pump Range was designed and developed to meet the harsh, continuous conditions of the mining and mineral-processing industries of Southern Africa. Capable of running dry and operating across a wide range of temperatures, the Hippo Pump Range is the ideal solution from high-volume dewatering to harsh, abrasive applications — with a custom-built solution for every application. Our pumps and pump systems have proven equally effective in Canada, South America, Australia, the Middle East and Russia.</p>",
          "<p class='lede'>Линейка шламовых насосов Hippo была разработана для суровых и непрерывных условий горнодобывающей и перерабатывающей промышленности Южной Африки. Способные работать «на сухом ходу» и в широком диапазоне температур, насосы Hippo — идеальное решение как для высокообъёмного водоотлива, так и для тяжёлых абразивных задач, с индивидуальным решением под каждое применение. Наши насосы и насосные системы доказали свою эффективность и за пределами региона — в Канаде, Южной Америке, Австралии, на Ближнем Востоке и в России.</p>",
      )}
      <div class="img-frame"><img src="/assets/img/world-map.svg" alt="World map — countries where Hazleton Pumps operate"></div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <p class="eyebrow">{bi("Industries", "Отрасли")}</p>
    <h2 class="section-title">{bi("Our experience by industry", "Наш опыт по отраслям")}</h2>
    <div class="grid cols-3" style="margin-top:28px">
      {industry_cards()}
    </div>
  </div>
</section>

<section class="alt tight">
  <div class="container">
    <p class="eyebrow" style="text-align:center">{bi("Proudly associated", "Партнёрские ассоциации")}</p>
    <div class="assoc-row">
      <img src="/assets/img/assoc-sassda.webp" alt="SASSDA">
      <img src="/assets/img/assoc-flameproof.webp" alt="South African Flameproof Association">
      <img src="/assets/img/assoc-foundrymen.webp" alt="South African Institute of Foundrymen">
      <img src="/assets/img/assoc-sapsda.webp" alt="SAPSDA">
      <img src="/assets/img/assoc-sa-capital-equipment.webp" alt="SA Capital Equipment">
    </div>
  </div>
</section>

{cta_strip()}
"""
    return layout("Customise Technology | Incomparable Quality", "", body, canonical="/")


def industry_cards():
    industries = [
        ("MINING", "Горнодобывающая отрасль",
         "All sectors of mining and mineral extraction: coal, platinum, gold, iron and ore.",
         "Все сегменты горной добычи и переработки полезных ископаемых: уголь, платина, золото, железо и руда."),
        ("Pulp, Paper & Sugar", "Целлюлоза, бумага и сахар",
         "Aggressive and abrasive solids.", "Агрессивные и абразивные твёрдые частицы."),
        ("Chemical & Processing", "Химия и переработка",
         "Including oil sands, bitumen, petrochemical, fertiliser and explosive industries.",
         "Включая нефтяные пески, битум, нефтехимию, удобрения и взрывоопасные производства."),
        ("Nuclear", "Атомная промышленность",
         "Specifically designed for high temperatures and contamination.",
         "Специальные решения для высоких температур и загрязнённых сред."),
        ("Foundries & Steel Production", "Литейное и сталелитейное производство",
         "Cooling, water returns and concentrated abrasive solids.",
         "Охлаждение, оборотная вода и концентрированные абразивные твёрдые частицы."),
        ("Power Generation, Municipal Services & Waste Management", "Энергетика, ЖКХ и утилизация отходов",
         "Ash, water treatment and waste disposal.", "Зола, водоочистка и утилизация отходов."),
        ("Sand, Gravel, Ceramic & General Construction", "Песок, гравий, керамика и строительство",
         "Where high concentrations of abrasive solids are present.",
         "Там, где присутствуют высокие концентрации абразивных частиц."),
    ]
    out = []
    for en_t, ru_t, en_d, ru_d in industries:
        out.append(f"""<div class="card">
          <h3>{bi(en_t, ru_t)}</h3>
          {bi_block(f"<p>{en_d}</p>", f"<p>{ru_d}</p>")}
        </div>""")
    return "".join(out)


def page_head(eyebrow_en, eyebrow_ru, title_en, title_ru, crumbs):
    return f"""
<section class="page-head">
  <div class="container">
    {crumbs}
    <p class="eyebrow" style="color:#e8a768">{bi(eyebrow_en, eyebrow_ru)}</p>
    <h1>{bi(title_en, title_ru)}</h1>
  </div>
</section>
"""


def crumbs_html(trail):
    parts = []
    for en, ru, href in trail:
        if href:
            parts.append(f'<a href="{href}">{bi(en, ru)}</a>')
        else:
            parts.append(bi(en, ru))
    return f'<div class="breadcrumb">{" / ".join(parts)}</div>'


# ===========================================================================
# HISTORY
# ===========================================================================

def page_history():
    head = page_head(
        "About Us", "О компании", "Our History", "Наша история",
        crumbs_html([("Home", "Главная", "/"), ("History", "История", None)]),
    )
    body = head + f"""
<section>
  <div class="container">
    <div class="two-col">
      <div>
        <h2 class="section-title">{bi("A family-owned and managed pump manufacturer", "Семейный производитель насосов")}</h2>
        {bi_block(
            '''<p>Founded in 1979, Hazleton Pumps is a family-owned business located in Centurion, Gauteng, South Africa. It began by repairing submersible pumps and electric motors for mines and industry. The harsh mining conditions in South Africa caused a high failure rate in these repaired pumps, which led to the design, development and manufacture of specialised pumping solutions capable of pumping acidic liquids containing solids under a wide range of conditions.</p>
            <p>The main cause of submersible pump failure is the pump running dry and overheating. This was overcome by filling the motor housing with oil, which serves two functions: dissipating heat from the electrical winding, and lubricating the bearings and mechanical seals.</p>
            <p>The motor housing containing the rotor and stator is separated from the discharge pressure of the pumped liquid using a cantilever shaft design with a double-discharge volute — and by applying the latest design software, the Hippo Submersible Slurry Pump Range was born.</p>
            <p>Manufactured as standard from specialised materials such as 28% hard-chrome castings for abrasive applications and Duplex Stainless Steel alloys (CD4MCu) castings for acidic environments, and with quality as a core objective, all Hippo Submersible Slurry Pumps are manufactured to comply with ISO 9001:2015 and IEC 60079-1.</p>
            <p>A customer-centric approach is followed, building long-term, committed, sustainable relationships as specific, specialised pumps are designed, developed and manufactured in collaboration with customers to meet their individual requirements — with products that are reliable, low-maintenance, sustainable, efficient and offer a reasonable cost of ownership.</p>''',
            '''<p>Компания Hazleton Pumps основана в 1979 году как семейное предприятие в городе Сентурион, провинция Гаутенг, ЮАР. Изначально компания занималась ремонтом погружных насосов и электродвигателей для горнодобывающих предприятий. Из-за тяжёлых условий эксплуатации в южноафриканских шахтах отремонтированные насосы часто выходили из строя, что привело к разработке, проектированию и производству специализированных насосных решений, способных перекачивать кислотные жидкости с содержанием твёрдых частиц в самых разных условиях.</p>
            <p>Главная причина отказа погружных насосов — работа «на сухом ходу» и перегрев. Эта проблема была решена заполнением корпуса электродвигателя маслом, которое выполняет две функции: отводит тепло от электрической обмотки и смазывает подшипники и торцевые уплотнения.</p>
            <p>Корпус двигателя с ротором и статором отделён от давления нагнетания перекачиваемой жидкости за счёт консольной конструкции вала с двухспиральным корпусом (double-discharge volute) — а с применением новейшего программного обеспечения для проектирования появилась на свет линейка погружных шламовых насосов Hippo.</p>
            <p>Как правило, насосы изготавливаются из специальных материалов — например, из хромистого чугуна (28% хрома) для абразивных сред и дуплексной нержавеющей стали (CD4MCu) для кислотных сред. Качество — ключевой приоритет компании: все погружные шламовые насосы Hippo производятся в соответствии со стандартами ISO 9001:2015 и IEC 60079-1.</p>
            <p>Компания придерживается клиентоориентированного подхода, выстраивая долгосрочные и устойчивые отношения с заказчиками: специализированные насосы разрабатываются и производятся в сотрудничестве с клиентом, под его индивидуальные требования — надёжные, простые в обслуживании, экономичные и эффективные в эксплуатации.</p>''',
        )}
      </div>
      <div class="img-frame"><img src="/assets/img/mining-site.webp" alt="South African mining site"></div>
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout("History", "", body, canonical="/history/")


# ===========================================================================
# AWARDS
# ===========================================================================

def page_awards():
    head = page_head(
        "About Us", "О компании", "Awards", "Награды",
        crumbs_html([("Home", "Главная", "/"), ("Awards", "Награды", None)]),
    )
    body = head + f"""
<section>
  <div class="container">
    <div class="grid cols-2">
      <div class="award-card">
        <img src="/assets/img/award-sapba.png" alt="SA Premier Business Awards">
        <div>
          <h3>{bi("SA Premier Business Awards 2014/2015", "SA Premier Business Awards 2014/2015")}</h3>
          {bi_block(
              "<p>Awarded by the Department of Trade and Industry in the SMME category, 9 April 2015.</p>",
              "<p>Награда от Департамента торговли и промышленности ЮАР в категории малого и среднего бизнеса (SMME), 9 апреля 2015 года.</p>",
          )}
        </div>
      </div>
      <div class="award-card">
        <img src="/assets/img/award-safa.png" alt="Award of Excellence">
        <div>
          <h3>{bi("Award of Excellence", "Award of Excellence")}</h3>
          {bi_block(
              "<p>South African Flameproof Association — Most Innovative Product / Engineering Solution, runner-up, 25 May 2017.</p>",
              "<p>Южноафриканская ассоциация взрывозащищённого оборудования — номинация «Самое инновационное изделие / инженерное решение», второе место, 25 мая 2017 года.</p>",
          )}
        </div>
      </div>
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Awards", "", body, canonical="/awards/")


# ===========================================================================
# PRESS RELEASES
# ===========================================================================

def page_press_releases():
    head = page_head(
        "About Us", "О компании", "Press Releases & Articles", "Пресс-релизы и статьи",
        crumbs_html([("Home", "Главная", "/"), ("Press Releases", "Пресс-релизы", None)]),
    )
    body = head + f"""
<section>
  <div class="container">
    <div class="grid cols-1" style="gap:20px">
      <a class="article-card" href="/press-releases/sa-hippo-to-exhibit-at-pdac-in-toronto/" style="text-decoration:none;color:inherit">
        <div class="thumb"><img src="/assets/img/press-sa-pump-toronto.jpg" alt="SA Hippo at PDAC Toronto"></div>
        <div>
          <div class="meta">{bi("Feb 2, 2017 · Press Release", "2 февраля 2017 г. · Пресс-релиз")}</div>
          <h3>{bi("SA Hippo to exhibit at PDAC in Toronto", "Южноафриканский Hippo на выставке PDAC в Торонто")}</h3>
          {bi_block(
              "<p>The PDAC International Convention, Trade Show &amp; Investors Exchange is the world's leading convention for people, companies and organisations connected with mineral exploration, drawing over 900 exhibitors and 22,000 attendees from 100+ countries…</p>",
              "<p>Международная конференция, выставка и инвестиционная биржа PDAC — ведущее в мире мероприятие для компаний и специалистов в области геологоразведки, собирающее более 900 экспонентов и 22 000 участников из более чем 100 стран…</p>",
          )}
          <span class="btn dark" style="margin-top:8px">{bi("Read more", "Читать далее")}</span>
        </div>
      </a>
      <a class="article-card" href="/press-releases/reducing-the-hidden-cost-of-electrical-submersible-pumps/" style="text-decoration:none;color:inherit">
        <div class="thumb"><img src="/assets/img/cd4mcu-steel.jpg" alt="Electrical submersible pump cost article"></div>
        <div>
          <div class="meta">{bi("Feb 25, 2015 · Press Release", "25 февраля 2015 г. · Пресс-релиз")}</div>
          <h3>{bi("Reducing the hidden cost of electrical submersible pumps", "Как снизить скрытые расходы на эксплуатацию погружных насосов")}</h3>
          {bi_block(
              "<p>Electrical submersible pumps are, once installed, usually out of sight and out of mind — with little attention paid to their real operating costs. We look at total cost of ownership rather than purchase price alone…</p>",
              "<p>После установки погружные электронасосы обычно скрыты из виду, и их реальная стоимость эксплуатации редко оказывается в центре внимания. В статье рассматривается совокупная стоимость владения, а не только цена покупки…</p>",
          )}
          <span class="btn dark" style="margin-top:8px">{bi("Read more", "Читать далее")}</span>
        </div>
      </a>
    </div>

    <h2 class="section-title" style="margin-top:48px">{bi("Press clippings", "Упоминания в прессе")}</h2>
    <ul class="press-clip-list">
      <li><span>{bi("Russia buys SA pump", "Россия покупает насос из ЮАР")}</span><span class="src">Engineering News</span></li>
      <li><span>{bi("Russian phosphate mine uses South African-made pumps", "Российское фосфатное предприятие использует насосы южноафриканского производства")}</span><span class="src">Engineering News</span></li>
      <li><span>{bi("Pump factory goes the extra mile — to Russia", "Насосный завод идёт дальше — до самой России")}</span><span class="src">Business Day</span></li>
    </ul>
  </div>
</section>
{cta_strip()}
"""
    return layout("Press Releases", "", body, canonical="/press-releases/")


def page_article_pdac():
    head = page_head(
        "Press Release", "Пресс-релиз", "SA Hippo to exhibit at PDAC in Toronto", "Южноафриканский Hippo на выставке PDAC в Торонто",
        crumbs_html([("Home", "Главная", "/"), ("Press Releases", "Пресс-релизы", "/press-releases/"), ("PDAC Toronto", "PDAC Торонто", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:860px">
    <a class="back-link" href="/press-releases/">{bi("← Back to press releases", "← Назад к пресс-релизам")}</a>
    <div class="img-frame" style="margin-bottom:28px"><img src="/assets/img/press-sa-pump-toronto.jpg" alt="SA Hippo at PDAC Toronto"></div>
    <p class="meta" style="color:var(--steel);font-weight:700">{bi("2 February 2017", "2 февраля 2017 г.")}</p>
    {bi_block(
        '''<p>The PDAC International Convention, Trade Show &amp; Investors Exchange is the world's leading convention for people, companies and organisations in, or connected with, mineral exploration. Over 900 exhibitors and 22,000 attendees from more than 100 countries attend, alongside technical sessions, short courses and networking events. Held annually in Toronto, Canada since 1932, it is today the event of choice for the world's mineral industry.</p>
        <p>PDAC is regarded as a crucial event for the international mining industry, bringing together the main decision-makers for projects across the Americas and Africa, who are always sourcing reliable products for use in their international mining operations — which is why Hazleton Pumps chose to participate again in PDAC 2017.</p>
        <p>Hazleton Pumps, the South African family-owned and managed company, first exhibited at PDAC in 2016, showcasing the Hippo Submersible Slurry Pump range and its capability to pump liquids containing solids.</p>
        <p>To demonstrate these capabilities clearly, the company developed a working model of a solid-separation pumping system — one of the most important mining applications, in which solids are separated from the liquid they are suspended in. A small Hippo submersible slurry pump feeds the cyclone, the solids are separated out, and are then returned to the pump and remixed for the process to repeat — ideal for exhibition demonstrations.</p>
        <p>Since the 2016 PDAC, Hazleton Pumps has received and completed orders from Canada worth more than R8.5 million, and continued participation at this event is essential to sustaining growth in this market. The demonstration unit has also been used at CIM in Vancouver, where the South African Consul General and trade representatives visited the Hazleton Pumps stand.</p>''',
        '''<p>Международная конференция, выставка и инвестиционная биржа PDAC — ведущее в мире мероприятие для людей, компаний и организаций, связанных с геологоразведкой. Мероприятие собирает более 900 экспонентов и 22 000 участников из более чем 100 стран; в программе — технические сессии, курсы и сетевые мероприятия. Проводимая ежегодно в Торонто (Канада) с 1932 года, сегодня это главное событие для мировой горнодобывающей отрасли.</p>
        <p>PDAC считается важнейшим событием для международной горнодобывающей отрасли, собирающим ключевых лиц, принимающих решения по проектам в Северной и Южной Америке и Африке — и именно поэтому компания Hazleton Pumps вновь приняла участие в PDAC 2017.</p>
        <p>Hazleton Pumps — южноафриканская семейная компания — впервые представила свою продукцию на PDAC в 2016 году, продемонстрировав линейку погружных шламовых насосов Hippo и их способность перекачивать жидкости с твёрдыми включениями.</p>
        <p>Чтобы наглядно показать эти возможности, компания разработала рабочую модель системы отделения твёрдых частиц — одного из важнейших процессов в горной добыче, при котором твёрдые частицы отделяются от перекачиваемой жидкости. Небольшой погружной насос Hippo подаёт смесь в циклон, где происходит разделение, после чего твёрдые частицы возвращаются обратно в насос для повторного цикла — удобное решение для демонстрации на выставках.</p>
        <p>С момента участия в PDAC 2016 года компания Hazleton Pumps получила и выполнила заказы из Канады на сумму более 8,5 млн южноафриканских рандов, и дальнейшее участие в этом мероприятии остаётся важным условием роста на этом рынке. Демонстрационная установка также использовалась на выставке CIM в Ванкувере, где стенд Hazleton Pumps посетили генеральный консул ЮАР и торговые представители.</p>''',
    )}
  </div>
</section>
{cta_strip()}
"""
    return layout("SA Hippo to exhibit at PDAC in Toronto", "", body, canonical="/press-releases/sa-hippo-to-exhibit-at-pdac-in-toronto/")


def page_article_hidden_cost():
    head = page_head(
        "Press Release", "Пресс-релиз", "Reducing the hidden cost of electrical submersible pumps", "Как снизить скрытые расходы на эксплуатацию погружных насосов",
        crumbs_html([("Home", "Главная", "/"), ("Press Releases", "Пресс-релизы", "/press-releases/"), ("Hidden cost", "Скрытые расходы", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:860px">
    <a class="back-link" href="/press-releases/">{bi("← Back to press releases", "← Назад к пресс-релизам")}</a>
    <p class="meta" style="color:var(--steel);font-weight:700">{bi("25 February 2015", "25 февраля 2015 г.")}</p>
    {bi_block(
        '''<p>Electrical submersible pumps, once installed, are usually fully submerged and out of sight — so their real running costs tend to get little attention. When buying a submersible pump, or any capital equipment, the key measure is total cost of ownership: power usage, maintenance, and the cost of downtime, not just the purchase price. Many factors affect a submersible pump's service life, and getting them wrong leads to costs that could easily have been avoided.</p>
        <h3>Evaluating the system</h3>
        <p>Before a pump is selected, the liquid to be pumped must be characterised: what solids are present, their size, the liquid's specific gravity and, if not neutral, its pH and chemical make-up. This determines the pump's materials of construction. The required flow rate must be calculated accurately, since pump capacity drives cost directly, and the static lift, pipe length, fittings and resulting friction losses must all be established to size the discharge pressure correctly. Undersized pipework wastes energy and accelerates wear.</p>
        <p>Once flow rate and total discharge pressure are known, a pump can be selected against its performance curve, which plots head against flow with power and efficiency on the secondary axis. The curve's "Best Efficiency Point" (BEP) is where the pump should operate — it delivers the most output for the least power, and minimises wear when solids are present. A discharge pressure gauge can confirm the pump is running at its BEP.</p>
        <h3>Materials and failures</h3>
        <p>Where solids or aggressive chemicals are present, materials of construction need careful selection — Duplex Stainless Steels such as CD4MCu are commonly used for acidic slurries. The leading cause of submersible pump failure is overheated electrical windings, usually from running dry; filling the motor housing with oil keeps windings and seals cool even when the pump runs dry. A quality trailing cable with screened conductors is equally important, as cable damage is a frequent failure point.</p>
        <p>Every installation needs a purpose-designed electrical control panel with earth-leakage protection, installed by a qualified electrician with the correct certification.</p>
        <h3>Conclusion</h3>
        <p>Properly specified, electrical submersible pumps are an economically sound, portable alternative to horizontal or vertical spindle pumps for almost any tank or sump application.</p>''',
        '''<p>После установки погружные электронасосы обычно полностью скрыты под жидкостью и находятся вне поля зрения, поэтому их реальным эксплуатационным расходам редко уделяется должное внимание. При выборе погружного насоса или любого другого основного оборудования ключевым показателем должна быть совокупная стоимость владения — расходы на электроэнергию, обслуживание и простои, а не только цена покупки. На срок службы погружного насоса влияет множество факторов, и ошибки в их оценке приводят к расходам, которых легко можно было избежать.</p>
        <h3>Оценка насосной системы</h3>
        <p>Прежде чем выбрать насос, необходимо определить характеристики перекачиваемой жидкости: наличие и размер твёрдых частиц, удельный вес жидкости, а если среда не нейтральна — её pH и химический состав. Эти данные определяют материал исполнения насоса. Требуемый расход нужно рассчитать точно, так как производительность насоса напрямую влияет на его стоимость; также необходимо учесть высоту подъёма, длину трубопровода, арматуру и вызванные ими потери на трение, чтобы правильно определить давление нагнетания. Заниженный диаметр труб приводит к лишнему расходу энергии и ускоренному износу.</p>
        <p>Зная расход и полное давление нагнетания, насос подбирают по его напорной характеристике, где напор отображается в зависимости от расхода, а мощность и КПД — на вспомогательной оси. «Точка максимального КПД» (Best Efficiency Point, BEP) на кривой — это режим, в котором должен работать насос: максимальная отдача при минимальном потреблении энергии и минимальный износ при наличии твёрдых частиц. Манометр на напорной линии позволяет подтвердить, что насос работает именно в этой точке.</p>
        <h3>Материалы и причины отказов</h3>
        <p>При наличии твёрдых частиц или агрессивных химических сред материал изготовления насоса требует тщательного подбора — для кислотных шламов часто применяется дуплексная нержавеющая сталь, например CD4MCu. Основная причина отказа погружных насосов — перегрев электрической обмотки, как правило, из-за работы «на сухом ходу»; заполнение корпуса двигателя маслом сохраняет обмотку и уплотнения в холодном состоянии даже при работе без жидкости. Не менее важен качественный кабель с экранированными жилами, так как повреждение кабеля — частая причина отказа.</p>
        <p>Для каждой установки требуется специально спроектированный электрощит защиты с устройством защитного отключения, монтаж которого должен выполнять квалифицированный электрик с соответствующим допуском.</p>
        <h3>Вывод</h3>
        <p>При правильном подборе погружные электронасосы являются экономически выгодной и мобильной альтернативой горизонтальным или вертикальным шпиндельным насосам практически для любой ёмкости или приямка.</p>''',
    )}
  </div>
</section>
{cta_strip()}
"""
    return layout("Reducing the hidden cost of electrical submersible pumps", "", body, canonical="/press-releases/reducing-the-hidden-cost-of-electrical-submersible-pumps/")


# ===========================================================================
# PUMP SYSTEMS
# ===========================================================================

def page_pump_systems():
    head = page_head(
        "Products", "Продукция", "Pump Systems", "Насосные системы",
        crumbs_html([("Home", "Главная", "/"), ("Pump Systems", "Насосные системы", None)]),
    )
    body = head + f"""
<section>
  <div class="container">
    <div class="grid cols-2">
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/solids-separation-system.svg" alt="Cyclone solid separation pump system"></div>
        <div class="body">
          <h3>{bi("Cyclone Solid Separation Pump System", "Циклонная система отделения твёрдых частиц")}</h3>
          {bi_block(
              "<p>Ideal for applications where fluids with solids need to be separated from the liquid. Different materials are used depending on whether the fluid contains hardened solids or abrasive liquids.</p>",
              "<p>Идеально подходит для задач, где твёрдые частицы необходимо отделить от жидкости. Материалы подбираются в зависимости от того, содержит ли среда затвердевшие частицы или абразивные жидкости.</p>",
          )}
          <a class="btn dark" href="/pump-systems/cyclone-solids-separation-submersible-pumping-system/">{bi("Find out more", "Подробнее")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/high-head-system.svg" alt="High head slurry series pumping system"></div>
        <div class="body">
          <h3>{bi("High Head Slurry Series Pumping System", "Серия высоконапорных шламовых систем")}</h3>
          {bi_block(
              "<p>For very deep pumping applications, able to pump heads of 250 m per stage through multiple stages. The system is configured to the application depth, going as deep as required.</p>",
              "<p>Для задач с большой глубиной откачки: напор до 250 м на ступень, возможно несколько ступеней. Система конфигурируется под глубину объекта — настолько глубоко, насколько требуется.</p>",
          )}
          <a class="btn dark" href="/pump-systems/high-head-slurry-series-pumping-system/">{bi("Find out more", "Подробнее")}</a>
        </div>
      </div>
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Pump Systems", "", body, canonical="/pump-systems/")


def system_detail(obj_en, obj_ru, op_en, op_ru, app_en, app_ru, duty_en, duty_ru, mat_en, mat_ru):
    return f"""
<div class="grid cols-2" style="margin-top:12px">
  <div class="panel">
    <h3>{bi("Objective", "Задача")}</h3>
    {bi_block(f"<p>{obj_en}</p>", f"<p>{obj_ru}</p>")}
  </div>
  <div class="panel">
    <h3>{bi("Operation", "Принцип работы")}</h3>
    {bi_block(f"<p>{op_en}</p>", f"<p>{op_ru}</p>")}
  </div>
  <div class="panel">
    <h3>{bi("Application", "Применение")}</h3>
    {bi_block(f"<p>{app_en}</p>", f"<p>{app_ru}</p>")}
  </div>
  <div class="panel">
    <h3>{bi("Duty", "Рабочие параметры")}</h3>
    {bi_block(f"<p>{duty_en}</p>", f"<p>{duty_ru}</p>")}
  </div>
  <div class="panel" style="grid-column:1/-1">
    <h3>{bi("Materials of construction", "Материалы изготовления")}</h3>
    {bi_block(f"<p>{mat_en}</p>", f"<p>{mat_ru}</p>")}
  </div>
</div>
"""


def page_cyclone_system():
    head = page_head(
        "Pump Systems", "Насосные системы", "Cyclone Solid Separation Pump System", "Циклонная система отделения твёрдых частиц",
        crumbs_html([("Home", "Главная", "/"), ("Pump Systems", "Насосные системы", "/pump-systems/"), ("Cyclone Solids Separation", "Циклонное отделение", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:980px">
    {bi_block(
        "<p class='lede'>The Cyclone Solid Separation Pump System is ideal for applications where solids need to be separated from a fluid. Different materials are used depending on whether the pumped fluid contains hardened solids or abrasive liquids.</p>",
        "<p class='lede'>Циклонная система отделения твёрдых частиц идеально подходит для задач, в которых твёрдые частицы необходимо отделить от жидкости. Материалы подбираются в зависимости от того, содержит ли перекачиваемая среда затвердевшие частицы или абразивные жидкости.</p>",
    )}
    {system_detail(
        "To separate the solids contained in the fluid from the liquid.",
        "Отделить твёрдые частицы, содержащиеся в жидкости, от самой жидкости.",
        "The fluid containing solids is pumped using a Hippo Submersible Pump through a cyclone, where the solids are separated from the liquid.",
        "Жидкость с твёрдыми частицами перекачивается погружным насосом Hippo через циклон, в котором происходит отделение твёрдых частиц от жидкости.",
        "Suitable for any application where solids must be removed from a liquid.",
        "Подходит для любых задач, где твёрдые частицы необходимо удалить из жидкости.",
        "The head and volume to be pumped determine the submersible pump size, as well as the flow rate through the cyclone.",
        "Напор и объём перекачки определяют типоразмер погружного насоса, а также пропускную способность циклона.",
        "High-chrome castings are used so the system can pump liquids containing hardened solids; for corrosive liquids, duplex stainless steel is the standard casting material.",
        "Для перекачки жидкостей с затвердевшими частицами применяется хромистый чугун; для коррозионных жидкостей стандартным материалом является дуплексная нержавеющая сталь.",
    )}
    <div class="img-frame" style="margin-top:24px"><img src="/assets/img/solids-separation-diagram.png" alt="Diagram of the solid separation system"></div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Cyclone Solid Separation Pump System", "", body, canonical="/pump-systems/cyclone-solids-separation-submersible-pumping-system/")


def page_high_head_system():
    head = page_head(
        "Pump Systems", "Насосные системы", "High Head Slurry Series Pumping System", "Серия высоконапорных шламовых систем",
        crumbs_html([("Home", "Главная", "/"), ("Pump Systems", "Насосные системы", "/pump-systems/"), ("High Head Slurry Series", "Высоконапорная серия", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:980px">
    {bi_block(
        "<p class='lede'>For very deep pumping applications, the High Head Slurry Series Pumping System is a viable solution able to pump heads of 250 m per stage, through multiple stages. The system is configured to the application depth, going as deep as required.</p>",
        "<p class='lede'>Для задач с большой глубиной откачки серия высоконапорных шламовых систем обеспечивает напор до 250 м на ступень, с возможностью установки нескольких ступеней. Система конфигурируется под глубину объекта — настолько глубоко, насколько требуется.</p>",
    )}
    {system_detail(
        "To create an effectively unlimited pumping head, enabling heads of up to 250 m per stage.",
        "Обеспечить практически неограниченный напор — до 250 м на ступень.",
        "An unlimited head is achieved by using the Hippo High Head Slurry Submersible Pump and feeding the discharge of the first pump into the inlet of the second, and so on. With continuous “run-dry” capability, the risk of overheating when running dry is eliminated.",
        "Неограниченный напор достигается за счёт использования высоконапорного погружного насоса Hippo: выход первого насоса подаётся на вход второго, и так далее. Благодаря способности непрерывной работы «на сухом ходу» риск перегрева при работе без жидкости исключён.",
        "Built to explosion-proof standard.",
        "Исполнение по взрывозащищённому стандарту.",
        "Heads of up to 250 m per stage can be achieved.",
        "Достижимый напор — до 250 м на ступень.",
        "Duplex Stainless Steel such as CD4MCu is used, giving the capability to pump acidic liquids containing solids.",
        "Применяется дуплексная нержавеющая сталь, например CD4MCu, что позволяет перекачивать кислотные жидкости с содержанием твёрдых частиц.",
    )}
  </div>
</section>
{cta_strip()}
"""
    return layout("High Head Slurry Series Pumping System", "", body, canonical="/pump-systems/high-head-slurry-series-pumping-system/")


# ===========================================================================
# HIPPO RANGE
# ===========================================================================

def page_hippo_range():
    head = page_head(
        "Flagship product", "Флагманский продукт", "Hippo Range", "Линейка Hippo",
        crumbs_html([("Home", "Главная", "/"), ("Hippo Range", "Линейка Hippo", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:980px">
    {bi_block(
        "<p class='lede'><strong>Your ally in pumping corrosive and acidic slurries.</strong> The award-winning Hippo Slurry Pump Range is the perfect workhorse for the continuous, harsh demands of mining and mineral processing. Custom built, it delivers pumping solutions that are robust, rugged, reliable and flexible. Capacities are extremely diverse, assisting applications from high-volume dewatering to settled-out acidic and corrosive slurries. The range can be built to explosion-proof standard IEC 60079-1:2005 and is available in various formats and applications.</p>",
        "<p class='lede'><strong>Ваш надёжный партнёр в перекачке коррозионных и кислотных шламов.</strong> Удостоенная наград линейка шламовых насосов Hippo — рабочая лошадка для непрерывных и тяжёлых условий горнодобывающей и перерабатывающей промышленности. Насосы изготавливаются по индивидуальному проекту и обеспечивают надёжные, прочные и гибкие решения. Диапазон производительности чрезвычайно широк — от высокообъёмного водоотлива до осевших кислотных и коррозионных шламов. Линейка может изготавливаться во взрывозащищённом исполнении по стандарту IEC 60079-1:2005 и доступна в различных конфигурациях.</p>",
    )}
  </div>
</section>
<section class="alt">
  <div class="container">
    <div class="grid cols-2">
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/hippo-pump-top-suction.png" alt="Hippo heavy duty slurry submersible pump"></div>
        <div class="body">
          <h3>{bi("Hippo Heavy Duty Slurry Submersible Pumps", "Погружные шламовые насосы Hippo повышенной прочности")}</h3>
          {bi_block(
              "<p>Designed and developed to pump corrosive and abrasive slurries — named after the hippopotamus, a robust and strong inhabitant of Africa's waterways. The range includes both an all-metal heavy-duty submersible and a vertical-spindle cantilever pump range.</p>",
              "<p>Разработаны для перекачки коррозионных и абразивных шламов — название линейки отсылает к бегемоту, крепкому и сильному обитателю африканских водоёмов. Линейка включает как цельнометаллические погружные насосы повышенной прочности, так и вертикальные шпиндельные консольные насосы.</p>",
          )}
          {ul(
              ["Capable of pumping abrasive and corrosive liquids", "Capacities up to 1500 l/s", "Heads up to 200 m", "Power installed from 3 kW up to 1000 kW", "Voltage supply up to 6.6 kV at both 50 &amp; 60 Hz", "Liquids up to 120 °C", "Can run dry continuously"],
              ["Перекачка абразивных и коррозионных жидкостей", "Производительность до 1500 л/с", "Напор до 200 м", "Мощность от 3 кВт до 1000 кВт", "Напряжение питания до 6,6 кВ при 50 и 60 Гц", "Жидкости с температурой до 120 °C", "Непрерывная работа «на сухом ходу»"],
          )}
          <a class="btn dark" href="/hippo-range/submersibles/">{bi("Configurations", "Конфигурации")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/hippo-vb-range.png" alt="Hippo vertical spindle pump range"></div>
        <div class="body">
          <h3>{bi("Hippo Cantilever Vertical Spindle Heavy Duty Slurry & De-watering Pumps", "Консольные вертикальные шпиндельные насосы Hippo для шлама и водоотлива")}</h3>
          {bi_block(
              "<p>The world's most versatile vertical-spindle pump range: an all-metal design with Bottom Suction, Bottom Discharge, Top Suction and Vortex configurations. Proven reliability lets it handle high-density abrasive and corrosive slurries, running dry at heads up to 110 m and flows up to 1500 l/s.</p>",
              "<p>Самая универсальная в мире линейка вертикальных шпиндельных насосов: цельнометаллическая конструкция в конфигурациях с нижним всасыванием, нижним нагнетанием, верхним всасыванием и вихревая. Проверенная надёжность позволяет работать с плотными абразивными и коррозионными шламами, выдерживая работу «на сухом ходу» при напоре до 110 м и расходе до 1500 л/с.</p>",
          )}
          {ul(
              ["Capable of pumping abrasive and corrosive liquids", "Capacities up to 1500 l/s and heads up to 110 m", "Flameproof certification to IEC 60079-1", "Power installed from 3 kW up to 575 kW", "Voltage supply up to 6.6 kV at both 50 &amp; 60 Hz", "Liquids up to 300 °C", "Can run dry continuously", "Shaft lengths designed to customer requirements", "Direct-coupled (preferred) or V-belt driven", "No submerged seals or bearings on the standard pump"],
              ["Перекачка абразивных и коррозионных жидкостей", "Производительность до 1500 л/с, напор до 110 м", "Взрывозащищённое исполнение по IEC 60079-1", "Мощность от 3 кВт до 575 кВт", "Напряжение питания до 6,6 кВ при 50 и 60 Гц", "Жидкости с температурой до 300 °C", "Непрерывная работа «на сухом ходу»", "Длина вала — по требованиям заказчика", "Прямая муфта (предпочтительно) или клиноремённый привод", "Отсутствие погружных уплотнений и подшипников в стандартном исполнении"],
          )}
          <a class="btn dark" href="/hippo-range/verticals/">{bi("Configurations", "Конфигурации")}</a>
        </div>
      </div>
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Hippo Range", "", body, canonical="/hippo-range/")


IEC_NOTE_EN = "To pump in explosive environments, all Hippo Submersible Pumps comply with IEC (SANS) 60079-0:2005 and IEC (SANS) 60079-1:2004."
IEC_NOTE_RU = "Для работы во взрывоопасных средах все погружные насосы Hippo соответствуют стандартам IEC (SANS) 60079-0:2005 и IEC (SANS) 60079-1:2004."


def hippo_variant_page(
    code, title_en, title_ru, back_label_en, back_label_ru, back_href,
    intro_en, intro_ru, specs_en, specs_ru, advantages, iec_extra,
    apps_intro_en, apps_intro_ru, apps_en, apps_ru, image, crumb_group_en, crumb_group_ru, crumb_group_href,
    canonical,
):
    adv_items_en = "".join(f"<li><strong>{a[0]}:</strong> {a[1]}</li>" for a in advantages)
    adv_items_ru = "".join(f"<li><strong>{a[2]}:</strong> {a[3]}</li>" for a in advantages)
    head = page_head(
        "Hippo Range", "Линейка Hippo", title_en, title_ru,
        crumbs_html([("Home", "Главная", "/"), ("Hippo Range", "Линейка Hippo", "/hippo-range/"), (crumb_group_en, crumb_group_ru, crumb_group_href), (title_en, title_ru, None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:1020px">
    <a class="back-link" href="{back_href}">{bi(f"← {back_label_en}", f"← {back_label_ru}")}</a>
    <div class="two-col">
      <div>
        {bi_block(f"<p class='lede'>{intro_en}</p>", f"<p class='lede'>{intro_ru}</p>")}
        {ul(specs_en, specs_ru)}
      </div>
      <div class="img-frame"><img src="/assets/img/{image}" alt="{title_en}"></div>
    </div>

    <div class="panel" style="margin-top:36px">
      <h3>{bi("The Hippo Twin-Volute Discharge Design", "Двухспиральная конструкция нагнетания Hippo")}</h3>
      {bi_block("<p>Balanced hydraulic forces totally eliminate shaft deflection during partial loading.</p>", "<p>Сбалансированные гидравлические силы полностью исключают прогиб вала при частичной загрузке.</p>")}
      <h4>{bi(f"Advantages of the {code} configuration", f"Преимущества конфигурации {code}")}</h4>
      {bi_block(f"<ul class='spec-list'>{adv_items_en}</ul>", f"<ul class='spec-list'>{adv_items_ru}</ul>", tag="div")}
    </div>

    <div class="panel">
      <h3>{bi("IEC (SANS) standards", "Стандарты IEC (SANS)")}</h3>
      {bi_block(f"<p>{IEC_NOTE_EN}</p>", f"<p>{IEC_NOTE_RU}</p>")}
      {iec_extra or ""}
    </div>

    <div class="panel">
      <h3>{bi("Applications", "Применение")}</h3>
      {bi_block(f"<p>{apps_intro_en}</p>", f"<p>{apps_intro_ru}</p>")}
      {ul(apps_en, apps_ru, cls="tag-list")}
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout(title_en, "", body, canonical=canonical)


def page_submersibles_index():
    head = page_head(
        "Hippo Range", "Линейка Hippo", "Submersibles", "Погружные насосы",
        crumbs_html([("Home", "Главная", "/"), ("Hippo Range", "Линейка Hippo", "/hippo-range/"), ("Submersibles", "Погружные насосы", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:980px">
    {bi_block(
        "<p class='lede'><strong>Hippo Heavy Duty Slurry Submersible Pumps.</strong> Designed and developed to pump corrosive and abrasive slurries. The range consists of both an all-metal heavy-duty submersible and a vertical-spindle cantilever pump range.</p>",
        "<p class='lede'><strong>Погружные шламовые насосы Hippo повышенной прочности.</strong> Разработаны для перекачки коррозионных и абразивных шламов. Линейка включает как цельнометаллические погружные насосы повышенной прочности, так и вертикальные шпиндельные консольные насосы.</p>",
    )}
  </div>
</section>
<section class="alt">
  <div class="container">
    <div class="grid cols-2">
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/sb-design-features.jpg" alt="Submersible Bottom Suction pump"></div>
        <div class="body">
          <h3>{bi("Submersible Bottom Suction (SB)", "Погружной насос с нижним всасыванием (SB)")}</h3>
          {bi_block(
              "<p>Used where solid particles have already settled out and need to be agitated before being pumped. All-metal construction with high-chrome hydraulics as standard; available in duplex stainless steel for acidic environments.</p>",
              "<p>Применяется там, где твёрдые частицы уже осели и требуют взмучивания перед откачкой. Цельнометаллическая конструкция со стандартной гидравликой из хромистого чугуна; доступно исполнение из дуплексной нержавеющей стали для кислотных сред.</p>",
          )}
          <a class="btn dark" href="/hippo-range/submersibles/submersible-bottom-suction/">{bi("SB Series", "Серия SB")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/st-design-features.jpg" alt="Submersible Top Suction pump"></div>
        <div class="body">
          <h3>{bi("Submersible Top Suction (ST)", "Погружной насос с верхним всасыванием (ST)")}</h3>
          {bi_block(
              "<p>Used where high discharge pressures exist and for pumping liquids with entrapped air (froth pumping) — pumping excess liquid away while solids remain in the sump for mechanical removal.</p>",
              "<p>Применяется при высоком давлении нагнетания и при перекачке жидкостей с вовлечённым воздухом (пенные среды) — откачивает избыток жидкости, оставляя твёрдые частицы в приямке для механического удаления.</p>",
          )}
          <a class="btn dark" href="/hippo-range/submersibles/submersible-top-suction/">{bi("ST Series", "Серия ST")}</a>
        </div>
      </div>
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Submersibles", "", body, canonical="/hippo-range/submersibles/")


def page_sb():
    advantages = [
        ("Twin Volute Discharge Design", "radial forces are balanced out in the volute, minimising bearing load and increasing bearing life while enabling more efficient pumping.",
         "Двухспиральная конструкция нагнетания", "радиальные силы уравновешиваются в корпусе, что снижает нагрузку на подшипники, увеличивает их ресурс и повышает эффективность перекачки."),
        ("Agitator", "the Hippo heavy-duty slurry pump range can be fitted with an agitator.",
         "Мешалка (агитатор)", "насосы Hippo повышенной прочности могут комплектоваться мешалкой."),
        ("“Run Dry”", "the stator housing is filled with oil, which dissipates heat away from the winding, enabling continuous dry running under full, partial or non-submerged conditions.",
         "«Сухой ход»", "корпус статора заполнен маслом, которое отводит тепло от обмотки, обеспечивая непрерывную работу на сухом ходу при полном, частичном или отсутствующем погружении."),
        ("Cantilever Shaft", "the Twin Volute design uses a cantilever shaft, separating the wet end from the motor end without support bearings.",
         "Консольный вал", "двухспиральная конструкция использует консольный вал, отделяющий проточную часть от двигателя без опорных подшипников."),
        ("Mechanical Seals", "dissociated from the wet end as a result of the cantilever shaft, subject only to submergence pressure — extending seal life.",
         "Торцевые уплотнения", "благодаря консольному валу отделены от проточной части и работают только под давлением погружения, что увеличивает их срок службы."),
        ("Moisture Detection", "a built-in moisture detector cuts power to the pump if the mechanical seals fail, preventing winding damage.",
         "Контроль влажности", "встроенный датчик влажности отключает питание насоса при отказе торцевых уплотнений, предотвращая повреждение обмотки."),
        ("Hydraulic-End Designs", "a specific hydraulic end is available for every application — high head or high volume, large or irregular solids, abrasive or corrosive liquids.",
         "Исполнения гидравлической части", "под каждую задачу доступна своя гидравлическая часть — для высокого напора или большого объёма, крупных или неправильной формы частиц, абразивных или коррозионных сред."),
    ]
    iec_extra = bi_block(
        "<p>Type SBO — Open Vane Type Impeller with Agitator. Type SBC — Closed Vane Type Impeller with Agitator Extension &amp; Spray-Bar.</p>",
        "<p>Тип SBO — открытое рабочее колесо с мешалкой. Тип SBC — закрытое рабочее колесо с удлинённой мешалкой и распылительной штангой.</p>",
    )
    body = hippo_variant_page(
        code="SB",
        title_en="Submersible Bottom Suction Pump", title_ru="Погружной насос с нижним всасыванием",
        back_label_en="Submersibles", back_label_ru="Погружные насосы", back_href="/hippo-range/submersibles/",
        intro_en="The Hippo Submersible Bottom Suction Pump Range is used where solid particles have already settled out and need to be agitated before being pumped. The all-metal pump, with high-chrome hydraulics as standard, offers durability and high efficiency, and can be manufactured in duplex stainless steel alloys (e.g. CD4MCu) for acidic environments. A wide range of accessories is available to suit client requirements.",
        intro_ru="Погружной насос Hippo с нижним всасыванием применяется там, где твёрдые частицы в жидкости уже осели и требуют взмучивания перед откачкой. Цельнометаллическая конструкция со стандартной гидравликой из хромистого чугуна обеспечивает долговечность и высокую эффективность; доступно исполнение из дуплексной нержавеющей стали (например, CD4MCu) для кислотных сред. Насос может комплектоваться широким набором опций под требования заказчика.",
        specs_en=["Capable of pumping abrasive and corrosive liquids", "Capacities up to 1500 l/s and heads up to 150 m", "Flameproof certification to IEC 60079-1", "Power installed from 3 kW up to 1000 kW", "Voltage supply up to 6.6 kV at both 50 &amp; 60 Hz", "Liquids up to 120 °C", "Can run dry"],
        specs_ru=["Перекачка абразивных и коррозионных жидкостей", "Производительность до 1500 л/с, напор до 150 м", "Взрывозащищённое исполнение по IEC 60079-1", "Мощность от 3 кВт до 1000 кВт", "Напряжение питания до 6,6 кВ при 50 и 60 Гц", "Жидкости с температурой до 120 °C", "Работа «на сухом ходу»"],
        advantages=advantages, iec_extra=iec_extra,
        apps_intro_en="The Hippo SB range excels in complex slurry and de-watering applications. Its compact design and high-pressure capability suit pits, pontoons, or temporary and fixed installations.",
        apps_intro_ru="Насосы серии Hippo SB отлично подходят для сложных задач перекачки шлама и водоотлива. Компактная конструкция и способность работать при высоком давлении позволяют использовать их в карьерах, на понтонах, а также во временных и стационарных установках.",
        apps_en=["Mineral process plants", "Mineral excavations", "Coal slurries", "Oil sands", "Fly-ash sumps and ponds", "Slurry transfer pumping", "Dredging", "Lime slurry removal", "Emergency dump pond", "Phosphoric acid plants", "Clay slurries"],
        apps_ru=["Перерабатывающие предприятия", "Горные выработки", "Угольные шламы", "Нефтяные пески", "Золоотвалы и пруды-накопители", "Перекачка шлама", "Дноуглубительные работы", "Удаление известкового шлама", "Аварийные пруды-накопители", "Производство фосфорной кислоты", "Глинистые шламы"],
        image="sb-design-features.jpg",
        crumb_group_en="Submersibles", crumb_group_ru="Погружные насосы", crumb_group_href="/hippo-range/submersibles/",
        canonical="/hippo-range/submersibles/submersible-bottom-suction/",
    )
    return body


def page_st():
    advantages = [
        ("Twin Volute Discharge Design", "radial forces are balanced out in the volute, minimising bearing load and increasing bearing life while enabling more efficient pumping.",
         "Двухспиральная конструкция нагнетания", "радиальные силы уравновешиваются в корпусе, что снижает нагрузку на подшипники, увеличивает их ресурс и повышает эффективность перекачки."),
        ("Lifting Bracket", "allows the pump to be lowered while in operation.",
         "Подъёмная скоба", "позволяет опускать насос во время работы."),
        ("“Run Dry”", "the stator housing is filled with oil, which dissipates heat away from the winding, enabling continuous dry running under full, partial or non-submerged conditions.",
         "«Сухой ход»", "корпус статора заполнен маслом, которое отводит тепло от обмотки, обеспечивая непрерывную работу на сухом ходу при полном, частичном или отсутствующем погружении."),
        ("Angular Contact Bearings", "mounted back-to-back to restrict axial movement during surging.",
         "Радиально-упорные подшипники", "установлены по схеме «спина к спине», что ограничивает осевое смещение при гидроударах."),
        ("Oil-Filled Motor Housing", "lubricates bearings and seals and dissipates heat away from the winding.",
         "Маслонаполненный корпус двигателя", "смазывает подшипники и уплотнения и отводит тепло от обмотки."),
        ("Double Mechanical Seals", "with moisture detector, operating at submergence pressure.",
         "Двойные торцевые уплотнения", "с датчиком влажности, работают под давлением погружения."),
        ("Moisture Detection", "a built-in moisture detector cuts power to the pump if the mechanical seals fail, preventing winding damage.",
         "Контроль влажности", "встроенный датчик влажности отключает питание насоса при отказе торцевых уплотнений, предотвращая повреждение обмотки."),
        ("Stator Motor", "designed to operate at any supply voltage and vacuum-impregnated.",
         "Статор двигателя", "рассчитан на работу при любом напряжении питания, выполнен с вакуумной пропиткой."),
        ("Cantilever Shaft", "separates the motor from the pump end, so the mechanical seal operates at submergence pressure rather than discharge pressure.",
         "Консольный вал", "отделяет двигатель от насосной части, благодаря чему торцевое уплотнение работает под давлением погружения, а не нагнетания."),
    ]
    body = hippo_variant_page(
        code="ST",
        title_en="Submersible Top Suction Pump", title_ru="Погружной насос с верхним всасыванием",
        back_label_en="Submersibles", back_label_ru="Погружные насосы", back_href="/hippo-range/submersibles/",
        intro_en="The Hippo Submersible Top Suction Slurry Pump Range is used where high discharge pressures exist and for pumping liquids with entrapped air (froth pumping). The all-metal pump, with high-chrome hydraulics as standard, offers durability and high efficiency, and can be manufactured in duplex stainless steel alloys (e.g. CD4MCu) for acidic environments. The Top Suction design pumps excess liquid away while the maximum amount of solids remains in the sump to be removed mechanically.",
        intro_ru="Погружной насос Hippo с верхним всасыванием применяется при высоком давлении нагнетания и для перекачки жидкостей с вовлечённым воздухом (пенные среды). Цельнометаллическая конструкция со стандартной гидравликой из хромистого чугуна обеспечивает долговечность и высокую эффективность; доступно исполнение из дуплексной нержавеющей стали (например, CD4MCu) для кислотных сред. Конструкция с верхним всасыванием откачивает избыток жидкости, оставляя максимум твёрдых частиц в приямке для последующего механического удаления.",
        specs_en=["Capable of pumping abrasive and corrosive liquids", "Capacities up to 1500 l/s and heads up to 200 m", "Flameproof to IEC 60079-1", "Power installed from 3 kW up to 1000 kW", "Voltage supply up to 6.6 kV at both 50 &amp; 60 Hz", "Liquids up to 120 °C", "Suitable for froth pumping", "Can run dry"],
        specs_ru=["Перекачка абразивных и коррозионных жидкостей", "Производительность до 1500 л/с, напор до 200 м", "Взрывозащищённое исполнение по IEC 60079-1", "Мощность от 3 кВт до 1000 кВт", "Напряжение питания до 6,6 кВ при 50 и 60 Гц", "Жидкости с температурой до 120 °C", "Подходит для пенных сред", "Работа «на сухом ходу»"],
        advantages=advantages, iec_extra="",
        apps_intro_en="The Hippo ST range excels in de-watering and drainage applications, and for pumping liquids with entrapped air (froth pumping). Its compact, high-pressure design suits sumps, open pits and pontoons, and units can be installed in series for very high heads such as acid mine-water drainage.",
        apps_intro_ru="Насосы серии Hippo ST отлично подходят для водоотлива, дренажа и перекачки жидкостей с вовлечённым воздухом (пенные среды). Компактная конструкция, рассчитанная на высокое давление, подходит для приямков, открытых карьеров и понтонов; возможна последовательная установка насосов для получения очень высокого напора, например при откачке кислотных шахтных вод.",
        apps_en=["General hard dewatering &amp; drainage", "Slag pits", "Tailings ponds", "Slurry froth pumping", "Mineral processing", "Phosphoric acid plants", "Mine dewatering"],
        apps_ru=["Общий жёсткий водоотлив и дренаж", "Шлаковые приямки", "Хвостохранилища", "Перекачка пенных шламов", "Переработка полезных ископаемых", "Производство фосфорной кислоты", "Шахтный водоотлив"],
        image="st-design-features.jpg",
        crumb_group_en="Submersibles", crumb_group_ru="Погружные насосы", crumb_group_href="/hippo-range/submersibles/",
        canonical="/hippo-range/submersibles/submersible-top-suction/",
    )
    return body


def page_verticals_index():
    head = page_head(
        "Hippo Range", "Линейка Hippo", "Verticals", "Вертикальные насосы",
        crumbs_html([("Home", "Главная", "/"), ("Hippo Range", "Линейка Hippo", "/hippo-range/"), ("Verticals", "Вертикальные насосы", None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:980px">
    {bi_block(
        "<p class='lede'><strong>Hippo Cantilever Vertical Spindle Heavy Duty Slurry &amp; De-watering Pumps.</strong> The world's most versatile pump range — an all-metal vertical-spindle design with Bottom Suction, Bottom Discharge, Top Suction and Vortex configurations. Proven reliability and efficiency let the Hippo range handle high-density abrasive and corrosive slurries, running dry at heads up to 110 m and flows up to 1500 l/s.</p>",
        "<p class='lede'><strong>Консольные вертикальные шпиндельные насосы Hippo для шлама и водоотлива.</strong> Самая универсальная в мире линейка насосов — цельнометаллическая вертикальная шпиндельная конструкция в конфигурациях с нижним всасыванием, нижним нагнетанием, верхним всасыванием и вихревая. Проверенная надёжность и эффективность позволяют линейке Hippo работать с плотными абразивными и коррозионными шламами, выдерживая работу «на сухом ходу» при напоре до 110 м и расходе до 1500 л/с.</p>",
    )}
  </div>
</section>
<section class="alt">
  <div class="container">
    <div class="grid cols-2">
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/vb-design-features.jpg" alt="Vertical Bottom Suction pump"></div>
        <div class="body">
          <h3>{bi("Vertical Bottom Suction Pump (VB)", "Вертикальный насос с нижним всасыванием (VB)")}</h3>
          {bi_block("<p>Used where solids have already settled out and need to be agitated before being pumped.</p>", "<p>Применяется там, где твёрдые частицы уже осели и требуют взмучивания перед откачкой.</p>")}
          <a class="btn dark" href="/hippo-range/verticals/vertical-bottom-suction/">{bi("Configuration", "Конфигурация")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/vb-design-features.jpg" alt="Vertical Bottom Discharge pump"></div>
        <div class="body">
          <h3>{bi("Vertical Bottom Discharge (VBD)", "Вертикальный насос с нижним нагнетанием (VBD)")}</h3>
          {bi_block("<p>Used where solids have already settled out and need to be agitated before being pumped, with discharge at the base of the pump.</p>", "<p>Применяется там, где твёрдые частицы уже осели и требуют взмучивания перед откачкой; нагнетание выполняется в нижней части насоса.</p>")}
          <a class="btn dark" href="/hippo-range/verticals/vertical-bottom-discharge/">{bi("Configuration", "Конфигурация")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/vbo-impeller.jpg" alt="Vertical Spindle Top Suction pump"></div>
        <div class="body">
          <h3>{bi("Vertical Spindle Top Suction Pump (VT)", "Вертикальный шпиндельный насос с верхним всасыванием (VT)")}</h3>
          {bi_block("<p>Used where high discharge pressures exist and for pumping liquids with entrapped air (froth pumping).</p>", "<p>Применяется при высоком давлении нагнетания и для перекачки жидкостей с вовлечённым воздухом (пенные среды).</p>")}
          <a class="btn dark" href="/hippo-range/verticals/vertical-top-suction/">{bi("Configuration", "Конфигурация")}</a>
        </div>
      </div>
      <div class="product-card">
        <div class="thumb"><img src="/assets/img/twin-volute-design-verticals.jpg" alt="Vertical Spindle Vortex pump"></div>
        <div class="body">
          <h3>{bi("Vertical Spindle Vortex Pump (VV)", "Вертикальный шпиндельный вихревой насос (VV)")}</h3>
          {bi_block("<p>Used where solids have already settled out and need to be agitated before being pumped, using a vortex hydraulic design.</p>", "<p>Применяется там, где твёрдые частицы уже осели и требуют взмучивания перед откачкой, с вихревой гидравлической конструкцией.</p>")}
          <a class="btn dark" href="/hippo-range/verticals/vertical-spindle-vortex-pump/">{bi("Configuration", "Конфигурация")}</a>
        </div>
      </div>
    </div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Verticals", "", body, canonical="/hippo-range/verticals/")


VERTICAL_SPECS_EN = ["Capable of pumping abrasive and corrosive liquids", "Capacities up to 1500 l/s and heads up to 110 m", "Flame/explosion-proof certification to IEC 60079-1", "Power installed from 3 kW up to 575 kW", "Voltage supply up to 6.6 kV at both 50 &amp; 60 Hz", "Liquids up to 300 °C", "Can run dry continuously", "Shaft lengths designed to customer requirements", "Accommodates standard flange-mounted motors", "Direct-coupled (preferred) or V-belt driven", "No submerged seals or bearings on the standard pump"]
VERTICAL_SPECS_RU = ["Перекачка абразивных и коррозионных жидкостей", "Производительность до 1500 л/с, напор до 110 м", "Взрывозащищённое исполнение по IEC 60079-1", "Мощность от 3 кВт до 575 кВт", "Напряжение питания до 6,6 кВ при 50 и 60 Гц", "Жидкости с температурой до 300 °C", "Непрерывная работа «на сухом ходу»", "Длина вала — по требованиям заказчика", "Совместим со стандартными фланцевыми электродвигателями", "Прямая муфта (предпочтительно) или клиноремённый привод", "Отсутствие погружных уплотнений и подшипников в стандартном исполнении"]

VERTICAL_ADVANTAGES = [
    ("Twin Volute Discharge Design", "radial forces are balanced out in the volute, minimising bearing load and increasing bearing life while enabling more efficient pumping.",
     "Двухспиральная конструкция нагнетания", "радиальные силы уравновешиваются в корпусе, что снижает нагрузку на подшипники, увеличивает их ресурс и повышает эффективность перекачки."),
    ("Agitator", "the Hippo heavy-duty slurry pump range can be fitted with an agitator.",
     "Мешалка (агитатор)", "насосы Hippo повышенной прочности могут комплектоваться мешалкой."),
    ("“Run Dry”", "the stator housing is filled with oil, which dissipates heat away from the winding, enabling continuous dry running under full, partial or non-submerged conditions.",
     "«Сухой ход»", "корпус статора заполнен маслом, которое отводит тепло от обмотки, обеспечивая непрерывную работу на сухом ходу при полном, частичном или отсутствующем погружении."),
    ("Cantilever Shaft", "the Twin Volute design uses a cantilever shaft, separating the wet end from the motor end without support bearings.",
     "Консольный вал", "двухспиральная конструкция использует консольный вал, отделяющий проточную часть от двигателя без опорных подшипников."),
    ("Mechanical Seals", "dissociated from the wet end as a result of the cantilever shaft, subject only to submergence pressure — extending seal life.",
     "Торцевые уплотнения", "благодаря консольному валу отделены от проточной части и работают только под давлением погружения, что увеличивает их срок службы."),
    ("Moisture Detection", "a built-in moisture detector cuts power to the pump if the mechanical seals fail, preventing winding damage.",
     "Контроль влажности", "встроенный датчик влажности отключает питание насоса при отказе торцевых уплотнений, предотвращая повреждение обмотки."),
    ("Hydraulic-End Designs", "a specific hydraulic end is available for every application — high head or high volume, large or irregular solids, abrasive or corrosive liquids.",
     "Исполнения гидравлической части", "под каждую задачу доступна своя гидравлическая часть — для высокого напора или большого объёма, крупных или неправильной формы частиц, абразивных или коррозионных сред."),
]

VERTICAL_APPS_EN = ["Mineral process plants", "Mineral excavations", "Coal mine tunnels", "Slurry pumping", "Flotation cell transfer", "Magnetite recovery", "Slurry storage sumps", "Slurry transport", "Tailings sumps", "Thickener feed pumps"]
VERTICAL_APPS_RU = ["Перерабатывающие предприятия", "Горные выработки", "Угольные тоннели", "Перекачка шлама", "Передача на флотационные камеры", "Извлечение магнетита", "Приямки хранения шлама", "Транспортировка шлама", "Хвостовые приямки", "Подача на сгустители"]


def page_vb():
    return hippo_variant_page(
        code="VB",
        title_en="Vertical Bottom Suction Pump", title_ru="Вертикальный насос с нижним всасыванием",
        back_label_en="Verticals", back_label_ru="Вертикальные насосы", back_href="/hippo-range/verticals/",
        intro_en="The Hippo Vertical Spindle Bottom Suction Pump Range is used where solid particles have already settled out and need to be agitated before being pumped. The all-metal pump, with a high-chrome hydraulic end as standard, offers durability and high efficiency, and can be manufactured in duplex stainless steel alloys (e.g. CD4MCu) for acidic environments.",
        intro_ru="Вертикальный шпиндельный насос Hippo с нижним всасыванием применяется там, где твёрдые частицы в жидкости уже осели и требуют взмучивания перед откачкой. Цельнометаллическая конструкция со стандартной гидравлической частью из хромистого чугуна обеспечивает долговечность и высокую эффективность; доступно исполнение из дуплексной нержавеющей стали (например, CD4MCu) для кислотных сред.",
        specs_en=VERTICAL_SPECS_EN, specs_ru=VERTICAL_SPECS_RU,
        advantages=VERTICAL_ADVANTAGES, iec_extra="",
        apps_intro_en="The Hippo VB range excels in complex slurry and de-watering applications. Its compact design and high-pressure capability suit pits, pontoons, or temporary and fixed installations.",
        apps_intro_ru="Насосы серии Hippo VB отлично подходят для сложных задач перекачки шлама и водоотлива. Компактная конструкция и способность работать при высоком давлении позволяют использовать их в карьерах, на понтонах, а также во временных и стационарных установках.",
        apps_en=VERTICAL_APPS_EN, apps_ru=VERTICAL_APPS_RU,
        image="vb-design-features.jpg",
        crumb_group_en="Verticals", crumb_group_ru="Вертикальные насосы", crumb_group_href="/hippo-range/verticals/",
        canonical="/hippo-range/verticals/vertical-bottom-suction/",
    )


def page_vbd():
    return hippo_variant_page(
        code="VBD",
        title_en="Vertical Bottom Discharge Pump", title_ru="Вертикальный насос с нижним нагнетанием",
        back_label_en="Verticals", back_label_ru="Вертикальные насосы", back_href="/hippo-range/verticals/",
        intro_en="The Hippo Vertical Bottom Discharge Pump Range is used where solid particles have already settled out and need to be agitated before being pumped. The all-metal pump, with a high-chrome hydraulic end as standard, offers durability and high efficiency, and can be manufactured in duplex stainless steel alloys (e.g. CD4MCu) for acidic environments.",
        intro_ru="Вертикальный насос Hippo с нижним нагнетанием применяется там, где твёрдые частицы в жидкости уже осели и требуют взмучивания перед откачкой. Цельнометаллическая конструкция со стандартной гидравлической частью из хромистого чугуна обеспечивает долговечность и высокую эффективность; доступно исполнение из дуплексной нержавеющей стали (например, CD4MCu) для кислотных сред.",
        specs_en=VERTICAL_SPECS_EN, specs_ru=VERTICAL_SPECS_RU,
        advantages=VERTICAL_ADVANTAGES, iec_extra="",
        apps_intro_en="The Hippo VBD range is used in complex slurry and de-watering applications. Its compact design and high-pressure capability suit pits, pontoons, or temporary and fixed installations.",
        apps_intro_ru="Насосы серии Hippo VBD применяются в сложных задачах перекачки шлама и водоотлива. Компактная конструкция и способность работать при высоком давлении позволяют использовать их в карьерах, на понтонах, а также во временных и стационарных установках.",
        apps_en=VERTICAL_APPS_EN, apps_ru=VERTICAL_APPS_RU,
        image="vb-design-features.jpg",
        crumb_group_en="Verticals", crumb_group_ru="Вертикальные насосы", crumb_group_href="/hippo-range/verticals/",
        canonical="/hippo-range/verticals/vertical-bottom-discharge/",
    )


def page_vt():
    body = hippo_variant_page(
        code="VT",
        title_en="Vertical Spindle Top Suction Pump", title_ru="Вертикальный шпиндельный насос с верхним всасыванием",
        back_label_en="Verticals", back_label_ru="Вертикальные насосы", back_href="/hippo-range/verticals/",
        intro_en="The Hippo Vertical Spindle Top Suction Slurry Pump Range is used where high discharge pressures exist and for pumping liquids with entrapped air (froth pumping). The all-metal pump, with high-chrome hydraulics as standard, offers durability and high efficiency, and can be manufactured in duplex stainless steel alloys (e.g. CD4MCu) for acidic environments.",
        intro_ru="Вертикальный шпиндельный насос Hippo с верхним всасыванием применяется при высоком давлении нагнетания и для перекачки жидкостей с вовлечённым воздухом (пенные среды). Цельнометаллическая конструкция со стандартной гидравликой из хромистого чугуна обеспечивает долговечность и высокую эффективность; доступно исполнение из дуплексной нержавеющей стали (например, CD4MCu) для кислотных сред.",
        specs_en=VERTICAL_SPECS_EN, specs_ru=VERTICAL_SPECS_RU,
        advantages=VERTICAL_ADVANTAGES, iec_extra=bi_block(
            "<p>Type VBO — Open Vane Type Impeller with Agitator.</p>", "<p>Тип VBO — открытое рабочее колесо с мешалкой.</p>"),
        apps_intro_en="The Hippo VT range excels in de-watering and drainage applications, and for pumping liquids with entrapped air (froth pumping). Units can be installed in series to accommodate very high heads, such as acid mine-water drainage.",
        apps_intro_ru="Насосы серии Hippo VT отлично подходят для водоотлива, дренажа и перекачки жидкостей с вовлечённым воздухом (пенные среды). Возможна последовательная установка насосов для получения очень высокого напора, например при откачке кислотных шахтных вод.",
        apps_en=["General hard dewatering &amp; drainage", "Slag pits", "Tailings ponds", "Slurry froth pumping", "Mineral processing", "Phosphoric acid plants", "Mine dewatering"],
        apps_ru=["Общий жёсткий водоотлив и дренаж", "Шлаковые приямки", "Хвостохранилища", "Перекачка пенных шламов", "Переработка полезных ископаемых", "Производство фосфорной кислоты", "Шахтный водоотлив"],
        image="vbo-impeller.jpg",
        crumb_group_en="Verticals", crumb_group_ru="Вертикальные насосы", crumb_group_href="/hippo-range/verticals/",
        canonical="/hippo-range/verticals/vertical-top-suction/",
    )
    return body


def page_vv():
    return hippo_variant_page(
        code="VV",
        title_en="Vertical Spindle Vortex Pump", title_ru="Вертикальный шпиндельный вихревой насос",
        back_label_en="Verticals", back_label_ru="Вертикальные насосы", back_href="/hippo-range/verticals/",
        intro_en="The Hippo Vertical Spindle Vortex Pump Range is used where solid particles have already settled out and need to be agitated before being pumped. The all-metal pump, with a high-chrome hydraulic end as standard, offers durability and high efficiency, and can be manufactured in duplex stainless steel alloys (e.g. CD4MCu) for acidic environments.",
        intro_ru="Вертикальный шпиндельный вихревой насос Hippo применяется там, где твёрдые частицы в жидкости уже осели и требуют взмучивания перед откачкой. Цельнометаллическая конструкция со стандартной гидравлической частью из хромистого чугуна обеспечивает долговечность и высокую эффективность; доступно исполнение из дуплексной нержавеющей стали (например, CD4MCu) для кислотных сред.",
        specs_en=VERTICAL_SPECS_EN, specs_ru=VERTICAL_SPECS_RU,
        advantages=VERTICAL_ADVANTAGES, iec_extra="",
        apps_intro_en="The Hippo VV range performs excellently in complex slurry and de-watering applications. Its compact design and high-pressure capability suit pits, pontoons, or temporary and fixed installations.",
        apps_intro_ru="Насосы серии Hippo VV отлично зарекомендовали себя в сложных задачах перекачки шлама и водоотлива. Компактная конструкция и способность работать при высоком давлении позволяют использовать их в карьерах, на понтонах, а также во временных и стационарных установках.",
        apps_en=VERTICAL_APPS_EN, apps_ru=VERTICAL_APPS_RU,
        image="twin-volute-design-verticals.jpg",
        crumb_group_en="Verticals", crumb_group_ru="Вертикальные насосы", crumb_group_href="/hippo-range/verticals/",
        canonical="/hippo-range/verticals/vertical-spindle-vortex-pump/",
    )


# ===========================================================================
# CASE STUDIES
# ===========================================================================

CASE_STUDIES = [
    dict(slug="foskor-richards-bay-south-africa", img="case-new-denmark.jpg",
         title_en="Foskor, Richards Bay — South Africa", title_ru="Фоскор, Ричардс-Бей — ЮАР",
         challenge_en="Foskor, a plant producing sulphuric acid (H₂SO₄), phosphoric acid (H₃PO₄) and granular fertiliser (MAP/DAP), needed a durable solution for pumping highly corrosive slurries.",
         challenge_ru="Завод Foskor, производящий серную кислоту (H₂SO₄), фосфорную кислоту (H₃PO₄) и гранулированные удобрения (MAP/DAP), нуждался в надёжном решении для перекачки высококоррозионных шламов.",
         solution_items_en=["Hazleton Pumps International designed and manufactured fit-for-purpose vertical spindle slurry pumps for the project.", "Pumps manufactured from CD4MCu duplex stainless steel for resistance to pH below 4."],
         solution_items_ru=["Hazleton Pumps International спроектировала и изготовила вертикальные шпиндельные шламовые насосы, подобранные под задачу проекта.", "Насосы изготовлены из дуплексной нержавеющей стали CD4MCu для устойчивости к средам с pH ниже 4."]),
    dict(slug="apatite-phosphate-mine-russia", img="case-apatite-russia.webp",
         title_en="Apatite Phosphate Mine — Russia", title_ru="Апатитовое месторождение — Россия",
         challenge_en="", challenge_ru="",
         solution_items_en=["The project called for vertical spindle pumps.", "Motors selected according to voltage and optimal pole speeds.", "Materials adapted to suit the application.", "Best efficiency through custom design.", "Reasonable cost of ownership.", "Minimum downtime: reliability and durability.", "Can run dry.", "Double discharge volute provides balanced radial loads on bearings and seals."],
         solution_items_ru=["Проект требовал применения вертикальных шпиндельных насосов.", "Электродвигатели подобраны по напряжению и оптимальной частоте вращения.", "Материалы адаптированы под условия применения.", "Максимальная эффективность за счёт индивидуального проектирования.", "Экономически обоснованная стоимость владения.", "Минимальные простои: надёжность и долговечность.", "Возможность работы «на сухом ходу».", "Двухспиральный корпус обеспечивает сбалансированные радиальные нагрузки на подшипники и уплотнения."]),
    dict(slug="rossing-uranium-namibia", img="case-rossing-namibia.webp",
         title_en="Rössing Uranium — Namibia", title_ru="Урановый рудник Рёссинг — Намибия",
         challenge_en="The project required heavy-duty vertical spindle slurry pumps for pontoon installation, capable of handling corrosive slurry with a pH below 4, and ambient temperatures of up to 43 °C in Namibia's desert climate.",
         challenge_ru="Проект требовал тяжёлых вертикальных шпиндельных шламовых насосов для установки на понтонах, способных работать с коррозионным шламом с pH ниже 4 при температуре окружающей среды до 43 °C в условиях пустынного климата Намибии.",
         solution_items_en=["Pumps cast and manufactured from CD4MCu duplex stainless steel to withstand corrosion.", "185 kW and 250 kW motors sized for the slurry's specific gravity.", "Reasonable cost of ownership and long-term value.", "Minimum downtime: reliability and durability.", "Design allows the pumps to run dry.", "Double discharge volute design provides balanced radial loads, increasing bearing life."],
         solution_items_ru=["Насосы отлиты и изготовлены из дуплексной нержавеющей стали CD4MCu для устойчивости к коррозии.", "Электродвигатели 185 кВт и 250 кВт подобраны под удельный вес шлама.", "Экономически обоснованная стоимость владения и долгосрочная ценность.", "Минимальные простои: надёжность и долговечность.", "Конструкция позволяет насосам работать «на сухом ходу».", "Двухспиральная конструкция обеспечивает сбалансированные радиальные нагрузки, увеличивая ресурс подшипников."]),
    dict(slug="new-denmark-colliery-south-africa", img="case-new-denmark.jpg",
         title_en="New Denmark Colliery — South Africa", title_ru="Угольная шахта Нью-Денмарк — ЮАР",
         challenge_en="", challenge_ru="",
         solution_items_en=["Certified flameproof submersible slurry pumps with flameproof electrical control panels for added protection.", "Designed for underground coal mines with flammable dust and combustible methane present.", "Manufactured from cast iron and hard chrome.", "Complies with IEC 60079 flameproof specifications.", "Can be designed for any electrical supply voltage, including high voltage, at 50 or 60 Hz.", "Trolley or skid available for ease of movement underground.", "Safety controls protect the pumps from overload, heat or excessive vibration."],
         solution_items_ru=["Сертифицированные взрывозащищённые погружные шламовые насосы с взрывозащищёнными электрощитами управления для дополнительной защиты.", "Разработаны для подземных угольных шахт с наличием горючей пыли и метана.", "Изготовлены из чугуна и хромистого чугуна.", "Соответствуют взрывозащищённым требованиям IEC 60079.", "Могут изготавливаться под любое напряжение питания, включая высокое, при 50 или 60 Гц.", "Доступна тележка или салазки для удобства перемещения под землёй.", "Системы защиты оберегают насосы от перегрузки, перегрева и избыточной вибрации."]),
    dict(slug="new-vaal-colliery-south-africa", img="case-new-vaal.jpg",
         title_en="New Vaal Colliery — South Africa", title_ru="Угольная шахта Нью-Ваал — ЮАР",
         challenge_en="Hazleton Pumps International was approached for a slurry pumping solution at the New Vaal Colliery project in South Africa, where a permanent installation was required for coal slurry.",
         challenge_ru="К Hazleton Pumps International обратились за решением для перекачки шлама на проекте угольной шахты Нью-Ваал в ЮАР, где требовалась стационарная установка для угольного шлама.",
         solution_items_en=["Heavy-duty vertical spindle slurry pumps were supplied complete with motors.", "A total of 68 Hippo vertical spindle slurry pumps (75L and 100L models) were designed and manufactured for New Vaal Colliery."],
         solution_items_ru=["Поставлены тяжёлые вертикальные шпиндельные шламовые насосы в комплекте с электродвигателями.", "Всего для шахты Нью-Ваал спроектировано и изготовлено 68 вертикальных шпиндельных насосов Hippo (модели 75L и 100L)."]),
    dict(slug="amandelbult-platinum-mine-south-africa", img="case-amandelbult.jpg",
         title_en="Amandelbult Platinum Mine — South Africa", title_ru="Платиновый рудник Амандельбюлт — ЮАР",
         challenge_en="Amandelbult mine, in the Thabazimbi area of South Africa's North-West Province, produces platinum-group metals valued for jewellery (for platinum's purity and resistance to tarnishing) and for catalytic converters.",
         challenge_ru="Рудник Амандельбюлт расположен в районе Табазимби северо-западной провинции ЮАР и добывает металлы платиновой группы, востребованные в ювелирном деле (благодаря чистоте и устойчивости платины к потускнению) и в производстве каталитических нейтрализаторов.",
         solution_items_en=["Pumps designed to handle heavy-duty, highly abrasive slurries.", "Fixed installation on frames within a sump at the plant."],
         solution_items_ru=["Насосы рассчитаны на перекачку тяжёлых высокоабразивных шламов.", "Стационарная установка на рамах в приямке предприятия."]),
    dict(slug="tautona-gold-mine-south-africa", img="case-tautona.jpg",
         title_en="TauTona Gold Mine — South Africa", title_ru="Золотой рудник ТауТона — ЮАР",
         challenge_en="This mining operation near Carletonville, South Africa, is the deepest in the world — reaching 3,900 m.",
         challenge_ru="Этот рудник близ Карлтонвилля в ЮАР — самый глубокий в мире: глубина разработки достигает 3900 м.",
         solution_items_en=["Heavy-duty submersible slurry pumps manufactured with a 28% hard-chrome wet end for durability.", "Designed for underground gold-mine conditions with abrasive material.", "Pumps can be designed for any electrical supply voltage, including high voltage, at 50 or 60 Hz."],
         solution_items_ru=["Погружные шламовые насосы повышенной прочности с проточной частью из хромистого чугуна (28% хрома) для долговечности.", "Разработаны для условий подземного золотодобывающего рудника с абразивными материалами.", "Насосы могут изготавливаться под любое напряжение питания, включая высокое, при 50 или 60 Гц."]),
    dict(slug="cullinan-diamond-mine-south-africa", img="case-cullinan.jpg",
         title_en="Cullinan Diamond Mine — South Africa", title_ru="Алмазный рудник Куллинан — ЮАР",
         challenge_en="", challenge_ru="",
         solution_items_en=["Hazleton Pumps International designed and manufactured three 350VBDC, 600 kW Hippo Bottom Suction vertical spindle pumps for Petra Diamonds' Cullinan Diamond Mine.", "The fixed installation pumps water from the dam to the process plant, where dust and dirt are washed from the diamond-bearing gravel."],
         solution_items_ru=["Hazleton Pumps International спроектировала и изготовила три вертикальных шпиндельных насоса Hippo с нижним всасыванием модели 350VBDC мощностью 600 кВт для алмазного рудника Куллинан компании Petra Diamonds.", "Стационарная установка подаёт воду из дамбы на перерабатывающий завод, где от алмазосодержащего гравия отмываются пыль и грязь."]),
    dict(slug="kenmare-titanium-sands-mozambique", img="case-kenmare.jpg",
         title_en="Kenmare Titanium Sands — Mozambique", title_ru="Титановые пески Кенмаре — Мозамбик",
         challenge_en="The Kenmare project in Mozambique needed a solution for pumping sand slurry containing titanium; extreme abrasion levels in the slurry challenged equipment durability.",
         challenge_ru="Проект Kenmare в Мозамбике требовал решения для перекачки песчаного шлама с содержанием титана; экстремальная абразивность шлама предъявляла высокие требования к долговечности оборудования.",
         solution_items_en=["Heavy-duty submersible slurry pumps were needed for pontoon installation, with chain blocks and electrical control panels.", "A total of 18 Hippo submersible slurry pumps (75M and 200DM models) were designed and manufactured for Kenmare."],
         solution_items_ru=["Для установки на понтонах потребовались погружные шламовые насосы повышенной прочности с цепными талями и электрощитами управления.", "Для компании Kenmare спроектировано и изготовлено 18 погружных шламовых насосов Hippo (модели 75M и 200DM)."]),
]


def page_case_studies_index():
    head = page_head(
        "Resources", "Ресурсы", "Case Studies", "Примеры проектов",
        crumbs_html([("Home", "Главная", "/"), ("Case Studies", "Примеры проектов", None)]),
    )
    cards = "".join(f"""<a class="case-card" href="/case-studies/{c['slug']}/">
      <div class="thumb"><img src="/assets/img/{c['img']}" alt="{c['title_en']}"></div>
      <div class="body">
        <h3>{bi(c['title_en'], c['title_ru'])}</h3>
        <span>{bi("Learn more →", "Подробнее →")}</span>
      </div>
    </a>""" for c in CASE_STUDIES)
    body = head + f"""
<section>
  <div class="container">
    <div class="two-col" style="margin-bottom:40px">
      <div>
        <h2 class="section-title">{bi("The offering", "Наше предложение")}</h2>
        {bi_block(
            "<p class='lede'>Hazleton Pumps, based in Centurion, South Africa, designs and manufactures the Hippo range of custom-engineered, heavy-duty slurry pumping solutions — cast and assembled in South Africa to the customer's requirements at duty point.</p>",
            "<p class='lede'>Компания Hazleton Pumps из города Сентурион (ЮАР) проектирует и производит линейку шламовых насосных решений Hippo повышенной прочности, изготавливаемых по индивидуальному проекту — отливка и сборка выполняются в Южной Африке под требования заказчика в рабочей точке.</p>",
        )}
        {ul(
            ["Motors from 5.5 kW to 850 kW", "Engineered to displace corrosive and abrasive slurry in heavy-duty mining and industrial applications", "Capacities up to 1500 litres per second", "Liquids up to 95 °C", "Heads up to 250 m", "24-month guarantee"],
            ["Электродвигатели от 5,5 кВт до 850 кВт", "Рассчитаны на перекачку коррозионных и абразивных шламов в тяжёлых горнодобывающих и промышленных задачах", "Производительность до 1500 литров в секунду", "Жидкости с температурой до 95 °C", "Напор до 250 м", "Гарантия 24 месяца"],
        )}
      </div>
      <div>
        <h2 class="section-title">{bi("Custom design & manufacture", "Индивидуальное проектирование и производство")}</h2>
        {bi_block(
            "<p class='lede'>Project requirements are assessed case by case, and slurry pumping solutions are designed accordingly.</p>",
            "<p class='lede'>Требования каждого проекта оцениваются индивидуально, и шламовые насосные решения проектируются соответствующим образом.</p>",
        )}
        <p style="font-weight:700;margin-bottom:6px">{bi("Materials of manufacture include:", "Используемые материалы:")}</p>
        {ul(["Hard chrome", "CD4MCu duplex stainless steel", "Sanichrome S28", "Hastelloy"], ["Хромистый чугун", "Дуплексная нержавеющая сталь CD4MCu", "Sanichrome S28", "Hastelloy"], cls="tag-list")}
        <div class="img-frame" style="margin-top:16px"><img src="/assets/img/cd4mcu-steel.jpg" alt="CD4MCu duplex stainless steel casting"></div>
      </div>
    </div>
    <h2 class="section-title">{bi("Projects worldwide", "Проекты по всему миру")}</h2>
    <div class="grid cols-3" style="margin-top:22px">{cards}</div>
  </div>
</section>
{cta_strip()}
"""
    return layout("Case Studies", "", body, canonical="/case-studies/")


def page_case_study(c):
    challenge_block = ""
    if c["challenge_en"]:
        challenge_block = f"""<div class="panel">
          <h3>{bi("Challenge", "Задача")}</h3>
          {bi_block(f"<p>{c['challenge_en']}</p>", f"<p>{c['challenge_ru']}</p>")}
        </div>"""
    sol_en = "".join(f"<li>{s}</li>" for s in c["solution_items_en"])
    sol_ru = "".join(f"<li>{s}</li>" for s in c["solution_items_ru"])
    head = page_head(
        "Case Study", "Пример проекта", c["title_en"], c["title_ru"],
        crumbs_html([("Home", "Главная", "/"), ("Case Studies", "Примеры проектов", "/case-studies/"), (c["title_en"], c["title_ru"], None)]),
    )
    body = head + f"""
<section>
  <div class="container" style="max-width:900px">
    <a class="back-link" href="/case-studies/">{bi("← Back to case studies", "← Назад к примерам проектов")}</a>
    <div class="img-frame" style="margin-bottom:26px"><img src="/assets/img/{c['img']}" alt="{c['title_en']}"></div>
    {challenge_block}
    <div class="panel">
      <h3>{bi("Solution", "Решение")}</h3>
      {bi_block(f"<ul class='spec-list'>{sol_en}</ul>", f"<ul class='spec-list'>{sol_ru}</ul>", tag="div")}
    </div>
    <a class="btn" href="/contact/">{bi("Contact us", "Связаться с нами")}</a>
  </div>
</section>
"""
    return layout(c["title_en"], "", body, canonical=f"/case-studies/{c['slug']}/")


# ===========================================================================
# PUMP CURVES
# ===========================================================================

PUMP_CURVE_GROUPS = [
    ("50 Hz Curves — Low Voltage", "Характеристики 50 Гц — низкое напряжение", [
        ("Hippo 2-Pole 50 Hz Curve — Low Voltage", "Hippo, 2 полюса, 50 Гц — низкое напряжение", "Hippo-2-Pole-Speed-50-Hz-Curve-Low-Voltage.jpg"),
        ("Hippo 4-Pole 50 Hz Curve — Low Voltage", "Hippo, 4 полюса, 50 Гц — низкое напряжение", "Hippo-4-Pole-Speed-50-Hz-Curve-Low-Voltage.jpg"),
        ("Hippo 6-Pole 50 Hz Curve — Low Voltage", "Hippo, 6 полюсов, 50 Гц — низкое напряжение", "Hippo-6-Pole-Speed-50-Hz-Curve-Low-Voltage.jpg"),
    ]),
    ("50 Hz Curves — Medium/High Voltage", "Характеристики 50 Гц — среднее/высокое напряжение", [
        ("Hippo 2-Pole 50 Hz Curve — Medium/High Voltage", "Hippo, 2 полюса, 50 Гц — среднее/высокое напряжение", "Hippo-2-Pole-Speed-50-Hz-Curve-MediumHigh-Voltage.jpg"),
        ("Hippo 4-Pole 50 Hz Curve — Medium/High Voltage", "Hippo, 4 полюса, 50 Гц — среднее/высокое напряжение", "Hippo-4-Pole-Speed-50-Hz-Curve-MediumHigh-Voltage.jpg"),
        ("Hippo 6-Pole 50 Hz Curve — Medium/High Voltage", "Hippo, 6 полюсов, 50 Гц — среднее/высокое напряжение", "Hippo-6-Pole-Speed-50-Hz-Curve-MediumHigh-Voltage.jpg"),
    ]),
    ("60 Hz Curves — Low Voltage", "Характеристики 60 Гц — низкое напряжение", [
        ("Hippo 2-Pole 60 Hz Curve — Low Voltage", "Hippo, 2 полюса, 60 Гц — низкое напряжение", "Hippo-2-Pole-Speed-60-Hz-Curve-Low-Votage.jpg"),
        ("Hippo 4-Pole 60 Hz Curve — Low Voltage", "Hippo, 4 полюса, 60 Гц — низкое напряжение", "Hippo-4-Pole-Speed-60-Hz-Curve-Low-Votage.jpg"),
        ("Hippo 6-Pole 60 Hz Curve — Low Voltage", "Hippo, 6 полюсов, 60 Гц — низкое напряжение", "Hippo-6-Pole-Speed-60-Hz-Curve-Low-Votage.jpg"),
    ]),
    ("60 Hz Curves — Medium/High Voltage", "Характеристики 60 Гц — среднее/высокое напряжение", [
        ("Hippo 2-Pole 60 Hz Curve — Medium/High Voltage", "Hippo, 2 полюса, 60 Гц — среднее/высокое напряжение", "Hippo-2-Pole-Speed-60-Hz-Curve-MediumHigh-Voltage.jpg"),
        ("Hippo 4-Pole 60 Hz Curve — Medium/High Voltage", "Hippo, 4 полюса, 60 Гц — среднее/высокое напряжение", "Hippo-4-Pole-Speed-60-Hz-Curve-MediumHigh-Voltage.jpg"),
        ("Hippo 6-Pole 60 Hz Curve — Medium/High Voltage", "Hippo, 6 полюсов, 60 Гц — среднее/высокое напряжение", "Hippo-6-Pole-Speed-60-Hz-Curve-MediumHigh-Voltage.jpg"),
    ]),
]


def page_pump_curves():
    head = page_head(
        "Resources", "Ресурсы", "Pump Performance Curves", "Напорные характеристики насосов",
        crumbs_html([("Home", "Главная", "/"), ("Pump Curves", "Напорные характеристики", None)]),
    )
    groups_html = ""
    for g_en, g_ru, items in PUMP_CURVE_GROUPS:
        cards = "".join(f"""<div class="card" style="padding:0;overflow:hidden">
          <div class="img-frame" style="border:none;border-radius:0"><img src="/assets/img/{img}" alt="{t_en}" loading="lazy"></div>
          <div style="padding:16px 18px"><h3 style="font-size:0.95rem;margin:0">{bi(t_en, t_ru)}</h3></div>
        </div>""" for t_en, t_ru, img in items)
        groups_html += f"""
        <h2 class="section-title" style="margin-top:40px">{bi(g_en, g_ru)}</h2>
        <div class="grid cols-3">{cards}</div>
        """
    body = head + f"""
<section>
  <div class="container">
    {bi_block(
        "<p class='lede'>Performance curves for the Hippo pump range, across pole speeds, frequencies and voltage classes. Contact us for curves matching your specific duty point.</p>",
        "<p class='lede'>Напорные характеристики насосов линейки Hippo для различных частот вращения, частот сети и классов напряжения. Свяжитесь с нами, чтобы подобрать кривую под вашу рабочую точку.</p>",
    )}
    {groups_html}
  </div>
</section>
{cta_strip()}
"""
    return layout("Pump Performance Curves", "", body, canonical="/pump-curves/")


# ===========================================================================
# CONTACT
# ===========================================================================

def page_contact():
    head = page_head(
        "Get in touch", "Связаться с нами", "Contact Us", "Контакты",
        crumbs_html([("Home", "Главная", "/"), ("Contact", "Контакты", None)]),
    )
    body = head + f"""
<section>
  <div class="container">
    <div class="two-col">
      <div>
        <h2 class="section-title">{bi("Contact us", "Свяжитесь с нами")}</h2>
        <div class="contact-block">
          <div class="item"><span class="ic">&#9679;</span><div>
            <strong>Hazleton Pumps Intl (Pty) Ltd</strong><br>
            {bi("33 Van Tonder Street<br>Sunderland Ridge, Centurion, South Africa", "ул. Ван Тондер, 33<br>Сандерленд-Ридж, Сентурион, ЮАР")}
          </div></div>
          <div class="item"><span class="ic">&#9742;</span><div><a href="tel:{COMPANY_PHONE.replace(' ', '')}">{COMPANY_PHONE}</a></div></div>
          <div class="item"><span class="ic">&#9993;</span><div><a href="mailto:{COMPANY_EMAIL}">{COMPANY_EMAIL}</a></div></div>
        </div>
        <div class="img-frame" style="margin-top:28px"><img src="/assets/img/world-map.svg" alt="Hazleton Pumps — global reach"></div>
      </div>
      <div class="panel">
        <h3>{bi("Request a quote", "Запросить коммерческое предложение")}</h3>
        <form class="quote-form" onsubmit="return false;">
          <div>
            <label>{bi("Name and Surname", "Имя и фамилия")}</label>
            <input type="text" name="name" required>
          </div>
          <div>
            <label>{bi("Email Address", "Электронная почта")}</label>
            <input type="email" name="email" required>
          </div>
          <div>
            <label>{bi("Contact Number", "Контактный телефон")}</label>
            <input type="tel" name="phone">
          </div>
          <div>
            <label>{bi("Company Name", "Название компании")}</label>
            <input type="text" name="company">
          </div>
          <div>
            <label>{bi("Message", "Сообщение")}</label>
            <textarea name="message" required></textarea>
          </div>
          <button type="submit" class="btn">{bi("Submit", "Отправить")}</button>
        </form>
      </div>
    </div>
  </div>
</section>
"""
    return layout("Contact", "", body, canonical="/contact/")


# ===========================================================================
# DRIVER
# ===========================================================================

def main():
    write("index.html", page_home())
    write("history/index.html", page_history())
    write("awards/index.html", page_awards())
    write("press-releases/index.html", page_press_releases())
    write("press-releases/sa-hippo-to-exhibit-at-pdac-in-toronto/index.html", page_article_pdac())
    write("press-releases/reducing-the-hidden-cost-of-electrical-submersible-pumps/index.html", page_article_hidden_cost())

    write("pump-systems/index.html", page_pump_systems())
    write("pump-systems/cyclone-solids-separation-submersible-pumping-system/index.html", page_cyclone_system())
    write("pump-systems/high-head-slurry-series-pumping-system/index.html", page_high_head_system())

    write("hippo-range/index.html", page_hippo_range())
    write("hippo-range/submersibles/index.html", page_submersibles_index())
    write("hippo-range/submersibles/submersible-bottom-suction/index.html", page_sb())
    write("hippo-range/submersibles/submersible-top-suction/index.html", page_st())
    write("hippo-range/verticals/index.html", page_verticals_index())
    write("hippo-range/verticals/vertical-bottom-suction/index.html", page_vb())
    write("hippo-range/verticals/vertical-bottom-discharge/index.html", page_vbd())
    write("hippo-range/verticals/vertical-top-suction/index.html", page_vt())
    write("hippo-range/verticals/vertical-spindle-vortex-pump/index.html", page_vv())

    write("case-studies/index.html", page_case_studies_index())
    for c in CASE_STUDIES:
        write(f"case-studies/{c['slug']}/index.html", page_case_study(c))

    write("pump-curves/index.html", page_pump_curves())
    write("contact/index.html", page_contact())

    print(f"\nDone — {6 + 3 + 9 + 1 + len(CASE_STUDIES) + 2} pages generated.")


if __name__ == "__main__":
    main()
