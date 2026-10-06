(function(){
  var ADMIN_PASSWORD = "adamko2026";
  var CKEY = "site-content";

  var DEFAULT = {
    brandName:"АДАМКО Сибирь", tagline:"новый вектор в горнодобывающем оборудовании",
    logo:"", logoShowText:true, logoHeight:44, logoPad:false,
    phone:"+7 (000) 000-00-00", email:"mail@adamko-sib.ru", city:"г. Новокузнецк, ул. Орджоникидзе, д. 35, офис 1209",
    badge:"Официальный партнёр JA Engineering Works (ЮАР)",
    h1a:"Горное и строительное", h1b:"оборудование",
    heroText:"ООО «Адамко Сибирь» — действующая компания в Новокузнецке, которая занимается оптовой торговлей горным и строительным оборудованием и техникой.",
    heroCta:"Связаться с нами",
    yearsNum:"С 2020 ГОДА",
    yearsText:"Компания зарегистрирована в Новокузнецке в 2020 году и специализируется на оптовых поставках машин и оборудования для добычи полезных ископаемых и строительства.",
    yearsCta:"О компании",
    catalogEyebrow:"Каталог оборудования",
    catalogTitle:"Горное и строительное оборудование",
    catalogAllLabel:"Все категории",
    catalogAllCta:"Открыть весь каталог",
    catalogPageLead:"Оборудование сгруппировано по категориям. Выберите категорию, чтобы посмотреть позиции и характеристики.",
    catalogCta:"Уточнить наличие",
    catalogMoreCta:"Подробнее",
    categories:[
      {id:"cat-1", code:"К-01", name:"Проходческое оборудование", desc:"Техника для проведения горных выработок и тоннелей: комбайны, комплексы и оборудование для крепления.", image:""},
      {id:"cat-2", code:"К-02", name:"Погрузочно-доставочные машины", desc:"Подземный транспорт и погрузочная техника для откатки горной массы.", image:""},
      {id:"cat-3", code:"К-03", name:"Буровое и перфораторное оборудование", desc:"Буровые установки, перфораторы и буровой инструмент под любые горно-геологические условия.", image:""},
      {id:"cat-4", code:"К-04", name:"Строительная техника", desc:"Землеройная и карьерная техника для строительных и вскрышных работ.", image:""},
      {id:"cat-5", code:"К-05", name:"Пневматическое и вспомогательное оборудование", desc:"Компрессоры, гидромолоты, вентиляция и освещение выработок.", image:""},
      {id:"cat-6", code:"К-06", name:"Запасные части и комплектующие", desc:"Узлы, агрегаты и расходные материалы для поставленной техники.", image:""}
    ],
    products:[
      {id:"p-101", catId:"cat-1", code:"П-101", name:"Проходческий комбайн избирательного действия", specs:"Сечение выработки: до 30 м²\nМощность привода: 132 кВт\nПрочность породы: до 100 МПа", text:"Комбайн для проведения горизонтальных и наклонных выработок по породам средней крепости. Поставляется с системой орошения и пылеподавления.", images:[]},
      {id:"p-102", catId:"cat-1", code:"П-102", name:"Тоннелепроходческий комплекс", specs:"Диаметр проходки: 3–6 м\nГидравлическая система подачи\nСистема пылеподавления", text:"Комплекс для проходки тоннелей и наклонных стволов. Конфигурация подбирается под проект заказчика.", images:[]},
      {id:"p-103", catId:"cat-1", code:"П-103", name:"Оборудование для крепления выработок", specs:"Анкероустановщики\nКрепеустановочные манипуляторы\nАнкерная и металлическая крепь", text:"Оборудование и материалы для возведения анкерной и рамной крепи.", images:[]},
      {id:"p-201", catId:"cat-2", code:"ПДМ-6", name:"Погрузочно-доставочная машина дизельная 6 т", specs:"Грузоподъёмность: 6 т\nОбъём ковша: 3 м³\nДвигатель: дизельный, с нейтрализатором", text:"ПДМ для подземных работ: погрузка и доставка горной массы в выработках среднего сечения.", images:[]},
      {id:"p-202", catId:"cat-2", code:"АС-20", name:"Подземный автосамосвал 20 т", specs:"Грузоподъёмность: 20 т\nПривод: полный, 4х4\nСистема пожаротушения", text:"Шарнирно-сочленённый самосвал для транспортировки руды и породы по подземным выработкам.", images:[]},
      {id:"p-203", catId:"cat-2", code:"ПНЛ-1", name:"Погрузочная машина с нагребающими лапами", specs:"Производительность: до 2 м³/мин\nПривод: пневматический\nДля выработок малого сечения", text:"Надёжная машина для погрузки горной массы в стеснённых условиях.", images:[]},
      {id:"p-301", catId:"cat-3", code:"БУ-32", name:"Буровая установка самоходная", specs:"Глубина бурения: до 32 м\nДиаметр шпура: 43–102 мм\nГидравлический перфоратор", text:"Самоходная установка для бурения шпуров и скважин при проходке и очистной выемке.", images:[]},
      {id:"p-302", catId:"cat-3", code:"ПП-60", name:"Перфоратор пневматический", specs:"Энергия удара: 60 Дж\nЧастота ударов: 2100 уд/мин\nДавление воздуха: 0,5 МПа", text:"Ручной перфоратор для бурения шпуров по крепким породам.", images:[]},
      {id:"p-303", catId:"cat-3", code:"БИ-01", name:"Буровой инструмент", specs:"Буровые штанги и коронки\nМуфты и переходники\nПод все распространённые типы перфораторов", text:"Расходный буровой инструмент со склада и под заказ.", images:[]},
      {id:"p-401", catId:"cat-4", code:"ЭК-21", name:"Экскаватор гусеничный", specs:"Эксплуатационная масса: 21 т\nОбъём ковша: 1,2 м³\nГлубина копания: 6,5 м", text:"Универсальный гусеничный экскаватор для вскрышных и строительных работ.", images:[]},
      {id:"p-402", catId:"cat-4", code:"БУЛ-18", name:"Бульдозер", specs:"Мощность: 180 л.с.\nОтвал: 3,4 м\nГидромеханическая трансмиссия", text:"Бульдозер для планировочных работ и формирования отвалов.", images:[]},
      {id:"p-403", catId:"cat-4", code:"КС-32", name:"Карьерный самосвал", specs:"Грузоподъёмность: 32 т\nОбъём кузова: 18 м³\nУсиленная рама и подвеска", text:"Самосвал для перевозки вскрыши и полезного ископаемого на карьерах.", images:[]},
      {id:"p-501", catId:"cat-5", code:"КВ-10", name:"Компрессор винтовой", specs:"Производительность: 5–12 м³/мин\nДавление: до 1,0 МПа\nДизельный или электрический привод", text:"Источник сжатого воздуха для пневмоинструмента и буровой техники.", images:[]},
      {id:"p-502", catId:"cat-5", code:"ГМ-30", name:"Гидравлический молот", specs:"Энергия удара: 3000 Дж\nДля носителей 18–26 т\nКомплект рабочего инструмента", text:"Навесной гидромолот для разрушения негабаритов и скальных пород.", images:[]},
      {id:"p-503", catId:"cat-5", code:"ВО-01", name:"Вентиляционные и осветительные системы", specs:"Вентиляторы местного проветривания\nШахтные светильники\nВоздуховоды и рукава", text:"Оборудование для проветривания и освещения подземных выработок.", images:[]},
      {id:"p-601", catId:"cat-6", code:"ЗЧ-01", name:"Узлы и агрегаты", specs:"Гидронасосы и гидромоторы\nРедукторы и мосты\nЭлементы ходовой части", text:"Оригинальные и аналоговые узлы для горной и строительной техники.", images:[]},
      {id:"p-602", catId:"cat-6", code:"ЗЧ-02", name:"Расходные материалы", specs:"Фильтры и РТИ\nМасла и смазки\nЗубья и коронки ковшей", text:"Расходники для регулярного обслуживания парка техники.", images:[]},
      {id:"p-603", catId:"cat-6", code:"ЗЧ-03", name:"Сервисные комплекты", specs:"Комплекты ТО\nРемкомплекты гидроцилиндров\nПоставка под заказ", text:"Готовые комплекты для планового и аварийного ремонта.", images:[]}
    ],
    features:[
      {title:"Оптовые поставки", text:"Работаем напрямую с производителями и поставляем горное и строительное оборудование оптовыми партиями."},
      {title:"Партнёрство с ЮАР", text:"Официальный партнёр JA Engineering Works — поставки проходческой техники, не уступающей зарубежным аналогам."},
      {title:"Сопровождение техники", text:"Помогаем с подбором техники под конкретные горно-геологические условия и её последующим обслуживанием."}
    ],
    aboutEyebrow:"О компании",
    aboutTitle:"ООО «Адамко Сибирь»",
    aboutText:"ООО «Адамко Сибирь» — действующая компания в Новокузнецке, которая занимается оптовой торговлей горным и строительным оборудованием. Компания зарегистрирована в 2020 году и работает с горнодобывающими и строительными предприятиями Кузбасса и других регионов.\n\nМы являемся партнёром южноафриканской компании JA Engineering Works и поставляем проходческую и погрузочно-доставочную технику, которая по своим характеристикам не уступает ставшим недоступными зарубежным аналогам. Специалисты компании имеют большой опыт работы в горно-шахтном машиностроении и помогают подобрать технику под конкретные задачи заказчика.",
    stats:[
      {value:"2020", label:"год основания компании"},
      {value:"ЮАР", label:"эксклюзивный партнёр по проходческой технике"},
      {value:"Кузбасс", label:"основной регион поставок и обслуживания"},
      {value:"B2B", label:"работаем с горнодобывающими и строительными предприятиями"}
    ],
    mapAddress:"г. Новокузнецк, ул. Орджоникидзе, д. 35, офис 1209",
    requisites:"ИНН 4217199759 · ОГРН 1204200012389",
    copyright:"© 2026 ООО «Адамко Сибирь». Все права защищены."
  };

  var CATALOG_PAGE = "catalog.html";
  var HOME_PAGE = "index.html";
  var content = null;
  var adminUnlocked = false;
  var activeCat = "all";
  var MAX_PHOTOS = 12;
  var LSKEY = "adamko-sibir-site-content";
  var hasCloud = false;
  try{ hasCloud = !!(window.storage && typeof window.storage.get === "function"); }catch(e){ hasCloud = false; }

  function $(id){ return document.getElementById(id); }
  function setText(id, value){ var el = $(id); if(el) el.textContent = value == null ? "" : value; }
  function on(id, ev, fn){ var el = $(id); if(el) el.addEventListener(ev, fn); }
  function isCatalogPage(){ return !!$("sr-catalog-grid"); }
  function catFromUrl(){
    var m = /[?&]cat=([^&#]+)/.exec(location.search || "");
    return m ? decodeURIComponent(m[1]) : "";
  }
  function syncUrl(){
    if(!isCatalogPage() || !window.history || !history.replaceState) return;
    try{
      history.replaceState(null, "", activeCat === "all" ? location.pathname : location.pathname + "?cat=" + encodeURIComponent(activeCat));
    }catch(e){}
  }
  function catalogHref(catId){
    return CATALOG_PAGE + (catId && catId !== "all" ? "?cat=" + encodeURIComponent(catId) : "");
  }
  function esc(s){ return String(s==null?"":s).replace(/[&<>"']/g, function(c){return {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c];}); }
  function lines(s){ return String(s==null?"":s).split("\n").map(function(x){return x.trim();}).filter(Boolean); }
  function plural(n, one, few, many){
    var a = Math.abs(n) % 100, b = a % 10;
    if(a > 10 && a < 20) return many;
    if(b > 1 && b < 5) return few;
    if(b === 1) return one;
    return many;
  }
  function uid(prefix){ return prefix + "-" + Date.now().toString(36) + Math.random().toString(36).slice(2,6); }
  function toast(msg){
    var t = $("sr-toast");
    if(!t) return;
    t.textContent = msg; t.classList.add("show");
    clearTimeout(t._h);
    t._h = setTimeout(function(){ t.classList.remove("show"); }, 2600);
  }

  /* ---------- фирменный знак ---------- */
  function emblemSvg(uid_){
    var clip = "sr-globe-" + (uid_ || "1");
    return '<svg viewBox="0 0 100 100" role="img" aria-label="Знак «Адамко Сибирь»">' +
      /* зелёный вектор-стрелка */
      '<path d="M5 60 C12 84 40 92 64 80 C70 77 74 72 76.5 67" fill="none" stroke="var(--brand)" stroke-width="9" stroke-linecap="round"/>' +
      '<path d="M85.4 49.1 L85.4 71.5 L67.6 62.5 Z" fill="var(--brand)"/>' +
      /* капля-указатель */
      '<path d="M64.8 54.2 L22 72 L33.8 27.1 A23 23 0 1 1 64.8 54.2 Z" fill="var(--brand)"/>' +
      /* глобус */
      '<defs><clipPath id="'+clip+'"><circle cx="32" cy="35" r="18.5"/></clipPath></defs>' +
      '<circle cx="32" cy="35" r="18.5" fill="#f2f5f7"/>' +
      '<g clip-path="url(#'+clip+')" fill="#7f8b95">' +
        '<path d="M32 29 q7 2 5 8 q-3 8 -8 11 q-3 -7 -1 -11 q1 -6 4 -8 z"/>' +
        '<path d="M21 21 q10 -3 15 3 q-5 4 -11 4 q-6 0 -4 -7 z"/>' +
        '<path d="M17 39 q5 2 4 7 q-6 -1 -6 -5 z"/>' +
        '<path d="M41 40 q4 1 4 5 q-4 1 -5 -3 z"/>' +
      '</g>' +
      '<g fill="none" stroke="#c6ced4" stroke-width="1.1">' +
        '<circle cx="32" cy="35" r="18.5"/>' +
        '<ellipse cx="32" cy="35" rx="8" ry="18.5"/>' +
        '<path d="M13 29h38M12 42h40" clip-path="url(#'+clip+')"/>' +
      '</g>' +
    '</svg>';
  }
  function renderBrand(){
    var mark = $("sr-mark");
    var foot = $("sr-foot-mark");
    var text = $("sr-brandtext");
    if(!mark || !foot || !text) return;
    if(content.logo){
      var hh = Math.max(24, Math.min(110, parseInt(content.logoHeight, 10) || 44));
      mark.style.setProperty("--logo-h", hh + "px");
      foot.style.setProperty("--logo-hf", Math.min(hh, 46) + "px");
      mark.className = "mark" + (content.logoPad ? " padded" : "");
      foot.parentNode.className = "foot-brandrow" + (content.logoPad ? " padded" : "");
      mark.innerHTML = '<img class="logo-img" src="'+esc(content.logo)+'" alt="'+esc(content.brandName)+'">';
      foot.innerHTML = '<img src="'+esc(content.logo)+'" alt="">';
      text.style.display = content.logoShowText ? "" : "none";
    } else {
      mark.className = "mark";
      foot.parentNode.className = "foot-brandrow";
      mark.innerHTML = emblemSvg("h");
      foot.innerHTML = emblemSvg("f");
      text.style.display = "";
    }
  }

  /* ---------- анимированные шестерёнки ---------- */
  function gearPathD(cx, cy, rOut, rIn, teeth){
    var d = "", step = Math.PI*2/teeth;
    function pt(r, a){ return (cx + r*Math.cos(a)).toFixed(2) + " " + (cy + r*Math.sin(a)).toFixed(2); }
    for(var i=0;i<teeth;i++){
      var a = i*step;
      d += (i ? "L" : "M") + pt(rIn, a) + "L" + pt(rOut, a + step*0.14) + "L" + pt(rOut, a + step*0.36) + "L" + pt(rIn, a + step*0.5);
    }
    return d + "Z";
  }
  function gearMarkup(o){
    var cx=o.cx, cy=o.cy, rOut=o.rOut, rIn=o.rIn, teeth=o.teeth||12;
    var stroke = o.stroke || "#e35f1e", fill = o.fill || "#1b2026", accent = o.accent || "#f0a94a";
    var s = '<path d="'+gearPathD(cx,cy,rOut,rIn,teeth)+'" fill="'+fill+'" stroke="'+stroke+'" stroke-width="'+(o.sw||2)+'" stroke-linejoin="round"/>';
    s += '<circle cx="'+cx+'" cy="'+cy+'" r="'+(rIn*0.62).toFixed(2)+'" fill="none" stroke="'+accent+'" stroke-width="'+(o.sw||2)+'" opacity=".7"/>';
    var spokes = o.spokes == null ? 5 : o.spokes;
    for(var i=0;i<spokes;i++){
      var a = i*Math.PI*2/spokes;
      s += '<line x1="'+(cx+rIn*0.26*Math.cos(a)).toFixed(2)+'" y1="'+(cy+rIn*0.26*Math.sin(a)).toFixed(2)+
           '" x2="'+(cx+rIn*0.60*Math.cos(a)).toFixed(2)+'" y2="'+(cy+rIn*0.60*Math.sin(a)).toFixed(2)+
           '" stroke="'+accent+'" stroke-width="'+(o.sw||2)+'" stroke-linecap="round" opacity=".55"/>';
    }
    s += '<circle cx="'+cx+'" cy="'+cy+'" r="'+(rIn*0.24).toFixed(2)+'" fill="#0b0d0f" stroke="'+stroke+'" stroke-width="'+(o.sw||2)+'"/>';
    return s;
  }
  function paintGears(){
    var hero = document.getElementById("sr-hero-gears");
    if(hero){
      hero.innerHTML =
        '<circle cx="200" cy="200" r="182" fill="none" stroke="#333941" stroke-width="1"/>' +
        '<g class="gear" style="animation-duration:18s">' + gearMarkup({cx:200,cy:205,rOut:112,rIn:90,teeth:14,spokes:6}) + '</g>' +
        '<g class="gear rev" style="animation-duration:11s">' + gearMarkup({cx:315,cy:96,rOut:68,rIn:53,teeth:10,spokes:5,stroke:"#f0a94a",accent:"#e35f1e",fill:"#181d23"}) + '</g>' +
        '<g class="gear rev" style="animation-duration:14s">' + gearMarkup({cx:86,cy:322,rOut:54,rIn:42,teeth:9,spokes:4,stroke:"#7d8892",accent:"#e35f1e",fill:"#151a1f"}) + '</g>';
    }
    var small = gearMarkup({cx:32,cy:32,rOut:28,rIn:21,teeth:10,spokes:4,sw:3,fill:"none",stroke:"currentColor",accent:"currentColor"});
    ["sr-fab-gear","sr-drawer-gear","sr-load-gear"].forEach(function(id){
      var el = document.getElementById(id);
      if(el) el.innerHTML = small;
    });
  }

  function gaugeSvg(seed){
    var angle = (seed*57)%180 - 90;
    return '<svg width="42" height="42" viewBox="0 0 42 42">' +
      '<circle cx="21" cy="21" r="18" fill="none" stroke="#3d434c" stroke-width="2"/>' +
      '<circle cx="21" cy="21" r="18" fill="none" stroke="#e35f1e" stroke-width="2" stroke-dasharray="70 200"/>' +
      '<line x1="21" y1="21" x2="' + (21+12*Math.cos(angle*Math.PI/180)).toFixed(2) + '" y2="' + (21+12*Math.sin(angle*Math.PI/180)).toFixed(2) + '" stroke="#f0a94a" stroke-width="2" stroke-linecap="round"/>' +
      '<circle cx="21" cy="21" r="2.5" fill="#e35f1e"/></svg>';
  }
  var FEATURE_ICONS = [
    '<svg viewBox="0 0 42 42" width="42" height="42"><rect x="6" y="6" width="30" height="30" fill="none" stroke="#e35f1e" stroke-width="2"/><rect x="14" y="14" width="14" height="14" fill="none" stroke="#f0a94a" stroke-width="2"/></svg>',
    '<svg viewBox="0 0 42 42" width="42" height="42"><path d="M21 5 L36 13 V27 L21 37 L6 27 V13 Z" fill="none" stroke="#e35f1e" stroke-width="2"/><path d="M14 21 L19 27 L29 15" fill="none" stroke="#f0a94a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    '<svg viewBox="0 0 42 42" width="42" height="42"><circle cx="21" cy="21" r="15" fill="none" stroke="#e35f1e" stroke-width="2"/><path d="M21 12v9l7 4" stroke="#f0a94a" stroke-width="2" stroke-linecap="round" fill="none"/></svg>'
  ];

  /* ---------- нормализация и миграция данных ---------- */
  function normalize(c){
    c = c || {};
    Object.keys(DEFAULT).forEach(function(k){
      if(c[k] == null) c[k] = JSON.parse(JSON.stringify(DEFAULT[k]));
    });
    // старый формат: catalog[{code,title,items,image}] -> категории + товары
    if(Array.isArray(c.catalog) && c.catalog.length && !c.__migrated){
      var cats = [], prods = [];
      c.catalog.forEach(function(old, i){
        var id = "cat-old-" + (i+1);
        cats.push({id:id, code:old.code||"", name:old.title||"Категория", desc:"", image:old.image||""});
        lines(old.items).forEach(function(line, j){
          prods.push({id:"p-old-"+(i+1)+"-"+(j+1), catId:id, code:"", name:line, specs:"", text:"", images:(j===0 && old.image) ? [old.image] : []});
        });
      });
      if(cats.length){ c.categories = cats; c.products = prods; }
      c.__migrated = true;
      delete c.catalog;
    }
    if(!c.__logoFix){ c.logoShowText = true; c.__logoFix = true; }
    if(!Array.isArray(c.categories) || !c.categories.length) c.categories = JSON.parse(JSON.stringify(DEFAULT.categories));
    if(!Array.isArray(c.products)) c.products = [];
    c.categories.forEach(function(cat, i){
      if(!cat.id) cat.id = uid("cat");
      if(cat.name == null) cat.name = "Категория " + (i+1);
      if(cat.code == null) cat.code = "";
      if(cat.desc == null) cat.desc = "";
      if(cat.image == null) cat.image = "";
    });
    c.products.forEach(function(p){
      if(!p.id) p.id = uid("p");
      if(p.catId == null) p.catId = "";
      if(p.name == null) p.name = "Позиция";
      if(p.code == null) p.code = "";
      if(p.specs == null) p.specs = "";
      if(p.text == null) p.text = "";
      if(!Array.isArray(p.images)) p.images = p.image ? [p.image] : [];
      p.images = p.images.filter(function(src){ return typeof src === "string" && src; });
      delete p.image;
    });
    return c;
  }
  function catById(id){
    for(var i=0;i<content.categories.length;i++) if(content.categories[i].id === id) return content.categories[i];
    return null;
  }
  function prodById(id){
    for(var i=0;i<content.products.length;i++) if(content.products[i].id === id) return content.products[i];
    return null;
  }
  function productsOf(catId){
    return content.products.filter(function(p){ return p.catId === catId; });
  }

  /* ---------- рендер сайта ---------- */
  function render(){
    renderBrand();
    setText("sr-brandname", content.brandName);
    setText("sr-tagline", content.tagline);
    setText("sr-phone", content.phone);
    setText("sr-email", content.email);
    setText("sr-city", content.city);
    var cp = $("sr-contact-phone");
    if(cp){ cp.textContent = content.phone; cp.href = "tel:" + String(content.phone||"").replace(/[^+\d]/g,""); }
    var ce = $("sr-contact-email");
    if(ce){ ce.textContent = content.email; ce.href = "mailto:" + content.email; }
    var side = $("sr-side-cta");
    if(side) side.href = "mailto:" + content.email;
    setText("sr-side-mini", content.brandName.replace(/^ООО\s*/,"").replace(/[«»"]/g,""));
    setText("sr-foot-phone", content.phone);
    setText("sr-foot-email", content.email);
    setText("sr-foot-city", content.city);
    setText("sr-foot-brand", content.brandName);
    setText("sr-foot-requisites", content.requisites);
    setText("sr-badge", content.badge);
    setText("sr-h1a", content.h1a);
    setText("sr-h1b", content.h1b);
    setText("sr-herotext", content.heroText);
    setText("sr-herocta", content.heroCta);
    setText("sr-yearsnum", content.yearsNum);
    setText("sr-yearstext", content.yearsText);
    setText("sr-yearscta", content.yearsCta);
    setText("sr-catalog-eyebrow", content.catalogEyebrow);
    setText("sr-catalog-title", content.catalogTitle);
    setText("sr-about-eyebrow", content.aboutEyebrow);
    setText("sr-about-title", content.aboutTitle);
    setText("sr-about-text", content.aboutText);
    setText("sr-map-address", content.mapAddress);
    setText("sr-copyright", content.copyright);
    setText("sr-catalog-allcta", content.catalogAllCta);
    setText("sr-catalog-lead", content.catalogPageLead);

    renderCatalog();
    renderFeatures();
    renderStats();

    var sv = $("sr-foot-services");
    if(sv){
      sv.innerHTML = "";
      content.categories.slice(0,6).forEach(function(cat){
        var li = document.createElement("li");
        li.innerHTML = '<a href="'+esc(catalogHref(cat.id))+'">'+esc(cat.name)+'</a>';
        sv.appendChild(li);
      });
      if(isCatalogPage()){
        Array.prototype.slice.call(sv.querySelectorAll("a")).forEach(function(a, i){
          a.onclick = function(e){
            e.preventDefault();
            setCat(content.categories[i].id);
            var sec = $("sr-catalog");
            if(sec) sec.scrollIntoView({behavior:"smooth"});
          };
        });
      }
    }

    if(adminUnlocked) renderAdminBody();
  }

  function renderFeatures(){
    var fg = $("sr-features-grid");
    if(!fg) return;
    fg.innerHTML = "";
    content.features.forEach(function(f, i){
      var el = document.createElement("div"); el.className = "f";
      el.innerHTML = '<div class="ficon">'+(FEATURE_ICONS[i%FEATURE_ICONS.length])+'</div><div><h3>'+esc(f.title)+'</h3><p>'+esc(f.text)+'</p></div>';
      fg.appendChild(el);
    });
    var fs = $("sr-features");
    if(fs) fs.style.display = content.features.length ? "" : "none";
  }

  function renderStats(){
    var sg = $("sr-stats-grid");
    if(!sg) return;
    sg.innerHTML = "";
    content.stats.forEach(function(s){
      var el = document.createElement("div"); el.className = "stat";
      el.innerHTML = '<div class="v">'+esc(s.value)+'</div><div class="l">'+esc(s.label)+'</div>';
      sg.appendChild(el);
    });
    sg.style.display = content.stats.length ? "" : "none";
  }

  function setCat(id){
    activeCat = id;
    renderCatalog();
    syncUrl();
  }

  function catCover(catId){
    var items = productsOf(catId);
    for(var i=0;i<items.length;i++){
      if(items[i].images && items[i].images.length) return items[i].images[0];
    }
    return "";
  }

  function renderCatPreview(){
    var grid = $("sr-cats-preview");
    if(!grid) return;
    grid.innerHTML = "";
    content.categories.forEach(function(cat, i){
      var a = document.createElement("a");
      a.className = "catcard";
      a.href = catalogHref(cat.id);
      var cover = catCover(cat.id);
      var photo = cover ?
        '<img src="'+esc(cover)+'" alt="'+esc(cat.name)+'" loading="lazy">' :
        '<div class="ph-empty">'+gaugeSvg(i+1)+'<span>Фото не добавлено</span></div>';
      a.innerHTML = '<div class="photo">'+photo+'</div>' +
        '<div class="body"><div class="top">' +
          (cat.code ? '<span class="code">'+esc(cat.code)+'</span>' : '') +
        '</div><h3>'+esc(cat.name)+'</h3>' +
        (cat.desc ? '<p>'+esc(cat.desc)+'</p>' : '') +
        '<div class="more"><span>Смотреть позиции</span><span class="cnt2">'+productsOf(cat.id).length+'</span></div></div>';
      grid.appendChild(a);
    });
    if(!content.categories.length){
      var e = document.createElement("div");
      e.className = "empty";
      e.textContent = "Категории пока не добавлены. Добавьте их в панели администратора.";
      grid.appendChild(e);
    }
  }

  function renderCatalog(){
    if(activeCat !== "all" && !catById(activeCat)) activeCat = "all";
    renderCatPreview();

    var cats = $("sr-catalog-cats");
    if(!cats) return;
    cats.innerHTML = "";
    var all = document.createElement("button");
    all.className = "chip" + (activeCat === "all" ? " active" : "");
    all.innerHTML = esc(content.catalogAllLabel) + '<span class="cnt">' + content.products.length + '</span>';
    all.onclick = function(){ setCat("all"); };
    cats.appendChild(all);
    content.categories.forEach(function(cat){
      var b = document.createElement("button");
      b.className = "chip" + (activeCat === cat.id ? " active" : "");
      b.innerHTML = esc(cat.name) + '<span class="cnt">' + productsOf(cat.id).length + '</span>';
      b.onclick = function(){ setCat(cat.id); };
      cats.appendChild(b);
    });

    var cur = activeCat === "all" ? null : catById(activeCat);
    setText("sr-catalog-catdesc", cur ? cur.desc : "");
    setText("sr-catalog-count", (activeCat === "all" ? content.products.length : productsOf(activeCat).length) + " " + plural(activeCat === "all" ? content.products.length : productsOf(activeCat).length, "позиция", "позиции", "позиций"));

    var list = activeCat === "all" ? content.products.slice() : productsOf(activeCat);
    var grid = $("sr-catalog-grid");
    grid.innerHTML = "";
    if(!list.length){
      var e = document.createElement("div"); e.className = "empty";
      e.textContent = "В этой категории пока нет позиций. Добавьте их в панели администратора.";
      grid.appendChild(e);
      return;
    }
    list.forEach(function(p, i){
      var cat = catById(p.catId);
      var el = document.createElement("article"); el.className = "item";
      var specs = lines(p.specs).slice(0,3).map(function(x){ return "<li>"+esc(x)+"</li>"; }).join("");
      var imgs = p.images || [];
      var photo = imgs.length ?
        '<img src="'+esc(imgs[0])+'" alt="'+esc(p.name)+'" loading="lazy">' +
          (imgs.length > 1 ? '<span class="ph-count">'+imgs.length+' фото</span>' : '') :
        '<div class="ph-empty">'+gaugeSvg(i+1)+'<span>Фото не добавлено</span></div>';
      el.innerHTML = '<div class="photo">'+photo+'</div>' +
        '<div class="body"><div class="top">' +
          (p.code ? '<span class="code">'+esc(p.code)+'</span>' : '') +
          (cat ? '<span class="cat-tag">'+esc(cat.name)+'</span>' : '') +
        '</div><h3>'+esc(p.name)+'</h3><ul>'+specs+'</ul>' +
        '<button class="btn-ghost" data-open-prod="'+esc(p.id)+'">'+esc(content.catalogMoreCta)+'</button></div>';
      grid.appendChild(el);
    });
    Array.prototype.slice.call(grid.querySelectorAll("[data-open-prod]")).forEach(function(b){
      b.onclick = function(){ openProduct(b.getAttribute("data-open-prod")); };
    });
  }

  var galProd = null, galIndex = 0;
  function paintGallery(){
    var photo = $("sr-prod-photo");
    var thumbs = $("sr-prod-thumbs");
    if(!photo || !thumbs) return;
    var imgs = galProd ? galProd.images : [];
    if(!imgs.length){
      photo.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;gap:10px;color:#66717a;"><svg viewBox="0 0 64 64" style="width:54px;height:54px;color:#e35f1e"><g class="gear">'+gearMarkup({cx:32,cy:32,rOut:28,rIn:21,teeth:10,spokes:4,sw:3,fill:"none",stroke:"currentColor",accent:"currentColor"})+'</g></svg><span>Фото не добавлено</span></div>';
      thumbs.innerHTML = "";
      return;
    }
    if(galIndex >= imgs.length) galIndex = 0;
    if(galIndex < 0) galIndex = imgs.length - 1;
    photo.innerHTML = '<img src="'+esc(imgs[galIndex])+'" alt="'+esc(galProd.name)+'">' +
      (imgs.length > 1 ?
        '<button class="sr-prod-nav prev" data-gnav="-1" aria-label="Предыдущее фото">‹</button>' +
        '<button class="sr-prod-nav next" data-gnav="1" aria-label="Следующее фото">›</button>' +
        '<span class="sr-prod-counter">'+(galIndex+1)+' / '+imgs.length+'</span>' : '');
    Array.prototype.slice.call(photo.querySelectorAll("[data-gnav]")).forEach(function(b){
      b.onclick = function(){ galIndex += +b.getAttribute("data-gnav"); paintGallery(); };
    });
    thumbs.innerHTML = imgs.length > 1 ? imgs.map(function(src, i){
      return '<button class="'+(i === galIndex ? "active" : "")+'" data-gthumb="'+i+'"><img src="'+esc(src)+'" alt=""></button>';
    }).join("") : "";
    Array.prototype.slice.call(thumbs.querySelectorAll("[data-gthumb]")).forEach(function(b){
      b.onclick = function(){ galIndex = +b.getAttribute("data-gthumb"); paintGallery(); };
    });
  }
  function openProduct(id){
    var p = prodById(id);
    if(!p) return;
    var cat = catById(p.catId);
    galProd = p; galIndex = 0;
    paintGallery();
    document.getElementById("sr-prod-code").textContent = p.code || "—";
    document.getElementById("sr-prod-cat").textContent = cat ? cat.name : "";
    document.getElementById("sr-prod-title").textContent = p.name;
    document.getElementById("sr-prod-specs").innerHTML = lines(p.specs).map(function(x){ return "<li>"+esc(x)+"</li>"; }).join("");
    document.getElementById("sr-prod-text").textContent = p.text || "";
    document.getElementById("sr-prod-cta").textContent = content.catalogCta;
    var bg = $("sr-prod-bg");
    if(bg) bg.classList.add("show");
  }
  function closeModal(id){ var el = $(id); if(el) el.classList.remove("show"); }
  on("sr-prod-close", "click", function(){ closeModal("sr-prod-bg"); });
  on("sr-prod-bg", "click", function(e){ if(e.target === this) this.classList.remove("show"); });
  on("sr-prod-cta", "click", function(){ closeModal("sr-prod-bg"); });
  document.addEventListener("keydown", function(e){
    var pb = $("sr-prod-bg");
    if(pb && pb.classList.contains("show") && galProd && galProd.images.length > 1){
      if(e.key === "ArrowLeft"){ galIndex--; paintGallery(); }
      if(e.key === "ArrowRight"){ galIndex++; paintGallery(); }
    }
    if(e.key === "Escape"){
      closeModal("sr-prod-bg");
      closeModal("sr-login-bg");
    }
  });

  /* ---------- загрузка / сохранение ---------- */
  function showPage(){
    var l = $("sr-loading"); if(l) l.style.display = "none";
    var pg = $("sr-page"); if(pg) pg.style.display = "block";
  }
  function loadAll(){
    paintGears();
    var urlCat = catFromUrl();
    if(urlCat) activeCat = urlCat;
    content = normalize(JSON.parse(JSON.stringify(DEFAULT)));
    showPage();
    render();
    fetchSaved().then(function(saved){
      if(saved){ content = normalize(saved); render(); }
      var urlCat2 = catFromUrl();
      if(urlCat2 && catById(urlCat2) && activeCat !== urlCat2){ activeCat = urlCat2; renderCatalog(); }
    });
  }
  function fetchSaved(){
    if(hasCloud){
      return window.storage.get(CKEY, true).then(function(res){
        if(!res || !res.value) return null;
        try{ return JSON.parse(res.value); }catch(e){ return null; }
      }).catch(function(){ return null; });
    }
    try{
      var raw = localStorage.getItem(LSKEY);
      return Promise.resolve(raw ? JSON.parse(raw) : null);
    }catch(e){ return Promise.resolve(null); }
  }
  function saveContent(){
    var json = JSON.stringify(content);
    if(hasCloud){ return window.storage.set(CKEY, json, true); }
    try{
      localStorage.setItem(LSKEY, json);
      return Promise.resolve(true);
    }catch(e){ return Promise.reject(e); }
  }

  /* ---------- вход в админку ---------- */
  function openAdminGate(){
    if(adminUnlocked){ openDrawer(); return; }
    var bg = $("sr-login-bg");
    if(!bg) return;
    bg.classList.add("show");
    $("sr-login-err").style.display = "none";
    $("sr-login-pass").value = "";
    $("sr-login-pass").focus();
  }
  on("sr-admin-fab", "click", openAdminGate);
  on("sr-admin-open", "click", openAdminGate);
  on("sr-login-close", "click", function(){ closeModal("sr-login-bg"); });
  on("sr-login-bg", "click", function(e){ if(e.target === this) this.classList.remove("show"); });
  on("sr-login-submit", "click", function(){
    var v = $("sr-login-pass").value;
    if(v === ADMIN_PASSWORD){
      adminUnlocked = true;
      closeModal("sr-login-bg");
      openDrawer();
    } else {
      $("sr-login-err").style.display = "block";
    }
  });
  on("sr-login-pass", "keydown", function(e){ if(e.key === "Enter") $("sr-login-submit").click(); });

  function openDrawer(){
    var d = $("sr-admin-drawer");
    if(!d) return;
    d.classList.add("show");
    renderAdminBody();
  }
  on("sr-admin-close", "click", function(){ closeModal("sr-admin-drawer"); });

  /* ---------- админка ---------- */
  function field(label, value, key, multiline){
    return '<div class="sr-field"><label>'+esc(label)+'</label>' +
      (multiline ? '<textarea data-key="'+key+'">'+esc(value)+'</textarea>' : '<input data-key="'+key+'" value="'+esc(value)+'">') +
      '</div>';
  }
  function selectField(label, value, key, options){
    var opts = options.map(function(o){
      return '<option value="'+esc(o.value)+'"'+(o.value === value ? ' selected' : '')+'>'+esc(o.label)+'</option>';
    }).join("");
    return '<div class="sr-field"><label>'+esc(label)+'</label><select data-key="'+key+'">'+opts+'</select></div>';
  }
  function checkField(label, checked, key){
    return '<div class="sr-field" style="display:flex;align-items:center;gap:9px;">' +
      '<input type="checkbox" data-check="'+key+'"'+(checked ? ' checked' : '')+' style="width:auto;">' +
      '<label style="margin:0;">'+esc(label)+'</label></div>';
  }
  function logoBlock(){
    var isFile = content.logo && content.logo.indexOf("data:") === 0;
    return '<div class="sr-logoprev" id="sr-logo-prev">' +
        (content.logo ? '<img src="'+esc(content.logo)+'" alt="">' : emblemSvg("a")) + '</div>' +
      '<div class="sr-photo-tools">' +
        '<label>📷 Загрузить логотип<input type="file" accept="image/*" id="sr-logo-file"></label>' +
        '<button type="button" id="sr-logo-clear">Вернуть стандартный знак</button>' +
      '</div>' +
      '<div class="sr-photo-name" id="sr-logo-name">' +
        (content.logo ? (isFile ? "Логотип загружен с ПК" : "Логотип задан по URL") : "Используется стандартный знак компании") + '</div>' +
      field("Ссылка на логотип (URL, необязательно)", isFile ? "" : content.logo, "logo") +
      field("Высота логотипа в шапке, px (24–110)", content.logoHeight, "logoHeight") +
      checkField("Показывать название рядом с логотипом", content.logoShowText, "logoShowText") +
      checkField("Светлая подложка (для логотипа на белом фоне)", content.logoPad, "logoPad");
  }
  function galleryItems(p){
    if(!p.images.length) return '<div class="sr-gal-empty">Фото не добавлены</div>';
    return p.images.map(function(src, i){
      return '<div class="sr-galitem'+(i === 0 ? ' main' : '')+'">' +
        '<img src="'+esc(src)+'" alt="">' +
        (i === 0 ? '<span class="mainbadge">Главное</span>' :
          '<button type="button" class="star" data-gmain="'+esc(p.id)+'|'+i+'" title="Сделать главным">★</button>') +
        '<button type="button" class="del" data-gdel="'+esc(p.id)+'|'+i+'" title="Удалить фото">✕</button>' +
        '</div>';
    }).join("");
  }
  function galleryBlock(p){
    return '<div class="sr-gal" data-gal="'+esc(p.id)+'">'+galleryItems(p)+'</div>' +
      '<div class="sr-photo-tools">' +
        '<label>📷 Добавить фото с ПК<input type="file" accept="image/*" multiple data-gfile="'+esc(p.id)+'"></label>' +
        '<button type="button" data-gclear="'+esc(p.id)+'">Удалить все фото</button>' +
      '</div>' +
      '<div class="sr-photo-name">Можно выбрать несколько файлов сразу. Первое фото — главное, оно показывается в каталоге.</div>' +
      '<div class="sr-field"><label>Добавить фото по ссылке</label><div class="sr-urlrow">' +
        '<input data-gurl="'+esc(p.id)+'" placeholder="https://...">' +
        '<button type="button" class="sr-addbtn" data-gadd="'+esc(p.id)+'">Добавить</button>' +
      '</div></div>';
  }
  function repaintGallery(id){
    var p = prodById(id);
    var box = document.querySelector('[data-gal="'+id+'"]');
    if(p && box) box.innerHTML = galleryItems(p);
    renderCatalog();
  }

  function renderAdminBody(){
    var body = document.getElementById("sr-admin-body");
    var h = "";
    if(!hasCloud){
      h += '<div style="background:#0f1418; border:1px solid #29333a; border-radius:10px; padding:12px 14px; font-size:12px; color:#8e99a3; margin-bottom:18px;">' +
        'Сайт открыт вне чата Claude, поэтому изменения сохраняются только в этом браузере (localStorage), а не для всех посетителей. ' +
        'Чтобы правки видели все, сайт нужно разместить на хостинге с собственным бэкендом.</div>';
    }
    h += '<h5>Шапка сайта</h5>';
    h += field("Название компании", content.brandName, "brandName");
    h += field("Слоган", content.tagline, "tagline");
    h += '<h5>Логотип</h5>';
    h += logoBlock();
    h += '<h5>Контакты в шапке</h5>';
    h += field("Телефон", content.phone, "phone");
    h += field("Email", content.email, "email");
    h += field("Город / адрес", content.city, "city");

    h += '<h5>Главный экран</h5>';
    h += field("Бейдж", content.badge, "badge");
    h += field("Заголовок, строка 1", content.h1a, "h1a");
    h += field("Заголовок, строка 2", content.h1b, "h1b");
    h += field("Описание", content.heroText, "heroText", true);
    h += field("Текст кнопки", content.heroCta, "heroCta");

    h += '<h5>Полоса под главным экраном</h5>';
    h += field("Короткая надпись", content.yearsNum, "yearsNum");
    h += field("Текст", content.yearsText, "yearsText", true);
    h += field("Текст кнопки", content.yearsCta, "yearsCta");

    h += '<h5>Каталог — заголовок и кнопки</h5>';
    h += field("Надпись сверху", content.catalogEyebrow, "catalogEyebrow");
    h += field("Заголовок", content.catalogTitle, "catalogTitle");
    h += field("Название вкладки «все»", content.catalogAllLabel, "catalogAllLabel");
    h += field("Кнопка «весь каталог» на главной", content.catalogAllCta, "catalogAllCta");
    h += field("Подзаголовок страницы каталога", content.catalogPageLead, "catalogPageLead", true);
    h += field("Кнопка на карточке", content.catalogMoreCta, "catalogMoreCta");
    h += field("Кнопка в карточке товара", content.catalogCta, "catalogCta");

    h += '<h5>Каталог — категории</h5>';
    h += '<div id="sr-adm-cats"></div><button class="sr-addbtn" id="sr-adm-cat-add">+ Добавить категорию</button>';

    h += '<h5>Каталог — товары по категориям</h5>';
    h += '<div id="sr-adm-prods"></div>';

    h += '<h5>Преимущества</h5>';
    h += '<div id="sr-adm-features"></div><button class="sr-addbtn" id="sr-adm-feat-add">+ Добавить преимущество</button>';

    h += '<h5>О компании</h5>';
    h += field("Надпись сверху", content.aboutEyebrow, "aboutEyebrow");
    h += field("Заголовок", content.aboutTitle, "aboutTitle");
    h += field("Текст", content.aboutText, "aboutText", true);

    h += '<h5>Факты о компании</h5>';
    h += '<div id="sr-adm-stats"></div><button class="sr-addbtn" id="sr-adm-stat-add">+ Добавить факт</button>';

    h += '<h5>Контакты и подвал</h5>';
    h += field("Адрес в блоке контактов", content.mapAddress, "mapAddress");
    h += field("Реквизиты", content.requisites, "requisites");
    h += field("Копирайт", content.copyright, "copyright");

    body.innerHTML = h;

    Array.prototype.slice.call(body.querySelectorAll("#sr-admin-body > .sr-field [data-key]")).forEach(function(el){
      el.addEventListener("input", function(){
        var k = el.getAttribute("data-key");
        content[k] = el.value;
        if(k === "logo"){
          var pv = document.getElementById("sr-logo-prev");
          if(pv) pv.innerHTML = el.value ? '<img src="'+esc(el.value)+'" alt="">' : emblemSvg("a");
          var nm = document.getElementById("sr-logo-name");
          if(nm) nm.textContent = el.value ? "Логотип задан по URL" : "Используется стандартный знак компании";
          renderBrand();
        }
        if(k === "logoHeight") renderBrand();
        if(k === "brandName" || k === "tagline") render();
      });
    });
    Array.prototype.slice.call(body.querySelectorAll("[data-check]")).forEach(function(el){
      el.addEventListener("change", function(){ content[el.getAttribute("data-check")] = el.checked; renderBrand(); });
    });
    bindLogoTools();

    renderCatsAdmin();
    renderProdsAdmin();
    renderFeaturesAdmin();
    renderStatsAdmin();

    document.getElementById("sr-adm-feat-add").onclick = function(){
      content.features.push({title:"Новое преимущество", text:"Короткое описание преимущества."});
      renderFeaturesAdmin(); render();
    };
    document.getElementById("sr-adm-stat-add").onclick = function(){
      content.stats.push({value:"Значение", label:"подпись факта"});
      renderStatsAdmin(); render();
    };
    document.getElementById("sr-adm-cat-add").onclick = function(){
      content.categories.push({id:uid("cat"), code:"К-0"+(content.categories.length+1), name:"Новая категория", desc:"", image:""});
      renderCatsAdmin(); renderProdsAdmin(); renderCatalog();
    };
  }

  function resizeImageForSite(file, maxSide, quality){
    var keepAlpha = /png|svg|webp/i.test(file.type);
    return new Promise(function(resolve,reject){
      if(/svg/i.test(file.type)){
        var r = new FileReader();
        r.onerror = reject;
        r.onload = function(){ resolve(r.result); };
        r.readAsDataURL(file);
        return;
      }
      var reader = new FileReader();
      reader.onerror = reject;
      reader.onload = function(){
        var img = new Image();
        img.onerror = reject;
        img.onload = function(){
          var ow = img.naturalWidth || img.width, oh = img.naturalHeight || img.height;
          var scale = Math.min(1, maxSide / Math.max(ow, oh));
          var w = Math.max(1, Math.round(ow*scale)), h = Math.max(1, Math.round(oh*scale));
          var canvas = document.createElement("canvas");
          canvas.width = w; canvas.height = h;
          var ctx = canvas.getContext("2d");
          if(!ctx) return reject(new Error("canvas"));
          ctx.drawImage(img,0,0,w,h);
          resolve(keepAlpha ? canvas.toDataURL("image/png") : canvas.toDataURL("image/jpeg", quality));
        };
        img.src = reader.result;
      };
      reader.readAsDataURL(file);
    });
  }

  function bindLogoTools(){
    var input = document.getElementById("sr-logo-file");
    if(input){
      input.addEventListener("change", function(){
        var file = input.files && input.files[0];
        if(!file) return;
        if(file.type.indexOf("image/") !== 0){ toast("Выберите файл изображения"); input.value = ""; return; }
        if(file.size > 15*1024*1024){ toast("Файл слишком большой. Максимум 15 МБ"); input.value = ""; return; }
        resizeImageForSite(file, 700, 0.92).then(function(dataUrl){
          content.logo = dataUrl;
          var pv = document.getElementById("sr-logo-prev");
          if(pv) pv.innerHTML = '<img src="'+esc(dataUrl)+'" alt="">';
          var urlInput = document.querySelector('#sr-admin-body [data-key="logo"]');
          if(urlInput) urlInput.value = "";
          var nm = document.getElementById("sr-logo-name");
          if(nm) nm.textContent = "Загружено: " + file.name;
          renderBrand();
        }).catch(function(){ toast("Не удалось обработать логотип"); });
      });
    }
    var clear = document.getElementById("sr-logo-clear");
    if(clear){
      clear.onclick = function(){
        content.logo = "";
        var pv = document.getElementById("sr-logo-prev");
        if(pv) pv.innerHTML = emblemSvg("a");
        var urlInput = document.querySelector('#sr-admin-body [data-key="logo"]');
        if(urlInput) urlInput.value = "";
        var nm = document.getElementById("sr-logo-name");
        if(nm) nm.textContent = "Используется стандартный знак компании";
        if(input) input.value = "";
        renderBrand();
      };
    }
  }

  function bindGalleryTools(wrap){
    if(wrap._galBound) return;
    wrap._galBound = true;
    wrap.addEventListener("click", function(e){
      var star = e.target.closest ? e.target.closest("[data-gmain]") : null;
      var del = e.target.closest ? e.target.closest("[data-gdel]") : null;
      var add = e.target.closest ? e.target.closest("[data-gadd]") : null;
      var clr = e.target.closest ? e.target.closest("[data-gclear]") : null;
      if(star){
        var sp = star.getAttribute("data-gmain").split("|"), prod = prodById(sp[0]);
        if(prod){ prod.images.unshift(prod.images.splice(+sp[1], 1)[0]); repaintGallery(sp[0]); }
      } else if(del){
        var dp = del.getAttribute("data-gdel").split("|"), prod2 = prodById(dp[0]);
        if(prod2){ prod2.images.splice(+dp[1], 1); repaintGallery(dp[0]); }
      } else if(add){
        var id = add.getAttribute("data-gadd"), prod3 = prodById(id);
        var input = wrap.querySelector('[data-gurl="'+id+'"]');
        var url = input ? input.value.trim() : "";
        if(!prod3 || !url) return;
        if(prod3.images.length >= MAX_PHOTOS){ toast("Максимум " + MAX_PHOTOS + " фото на позицию"); return; }
        prod3.images.push(url);
        input.value = "";
        repaintGallery(id);
      } else if(clr){
        var cid = clr.getAttribute("data-gclear"), prod4 = prodById(cid);
        if(!prod4 || !prod4.images.length) return;
        if(!confirm("Удалить все фото этой позиции?")) return;
        prod4.images = [];
        repaintGallery(cid);
      }
    });
    wrap.addEventListener("change", function(e){
      var input = e.target;
      if(!input.getAttribute || !input.getAttribute("data-gfile")) return;
      var id = input.getAttribute("data-gfile"), prod = prodById(id);
      var files = Array.prototype.slice.call(input.files || []);
      if(!prod || !files.length) return;
      var free = MAX_PHOTOS - prod.images.length;
      if(free <= 0){ toast("Максимум " + MAX_PHOTOS + " фото на позицию"); input.value = ""; return; }
      if(files.length > free){ toast("Добавим только " + free + " фото: лимит " + MAX_PHOTOS); files = files.slice(0, free); }
      var queue = Promise.resolve(), added = 0, skipped = 0;
      files.forEach(function(file){
        queue = queue.then(function(){
          if(file.type.indexOf("image/") !== 0 || file.size > 15*1024*1024){ skipped++; return; }
          return resizeImageForSite(file, 1400, 0.78).then(function(dataUrl){
            prod.images.push(dataUrl); added++;
          }).catch(function(){ skipped++; });
        });
      });
      queue.then(function(){
        input.value = "";
        repaintGallery(id);
        toast(added ? ("Добавлено фото: " + added + (skipped ? (", пропущено: " + skipped) : "")) : "Не удалось добавить фото");
      });
    });
  }

  function renderCatsAdmin(){
    var wrap = document.getElementById("sr-adm-cats");
    if(!wrap) return;
    wrap.innerHTML = content.categories.map(function(cat){
      return '<div class="sr-arritem">' +
        (content.categories.length > 1 ? '<button class="rm" data-rm-cat="'+esc(cat.id)+'" title="Удалить категорию">✕</button>' : '') +
        field("Название категории", cat.name, "cat__"+cat.id+"__name") +
        field("Код", cat.code, "cat__"+cat.id+"__code") +
        field("Описание категории", cat.desc, "cat__"+cat.id+"__desc", true) +
        '<div class="sr-photo-name">Позиций в категории: ' + productsOf(cat.id).length + '</div>' +
        '</div>';
    }).join("");
    Array.prototype.slice.call(wrap.querySelectorAll("[data-key]")).forEach(function(el){
      el.addEventListener("input", function(){
        var parts = el.getAttribute("data-key").split("__");
        var cat = catById(parts[1]);
        if(!cat) return;
        cat[parts[2]] = el.value;
        if(parts[2] === "name"){
          var head = document.querySelector('[data-cathead="'+cat.id+'"]');
          if(head) head.textContent = el.value;
          Array.prototype.slice.call(document.querySelectorAll('select[data-key$="__catId"] option[value="'+cat.id+'"]')).forEach(function(o){ o.textContent = el.value; });
        }
        renderCatalog();
      });
    });
    Array.prototype.slice.call(wrap.querySelectorAll("[data-rm-cat]")).forEach(function(b){
      b.onclick = function(){
        var id = b.getAttribute("data-rm-cat");
        var n = productsOf(id).length;
        if(!confirm(n ? ("Удалить категорию вместе с позициями (" + n + " шт.)?") : "Удалить категорию?")) return;
        content.categories = content.categories.filter(function(c){ return c.id !== id; });
        content.products = content.products.filter(function(p){ return p.catId !== id; });
        if(activeCat === id) activeCat = "all";
        renderCatsAdmin(); renderProdsAdmin(); renderCatalog();
      };
    });
  }

  function renderProdsAdmin(){
    var wrap = document.getElementById("sr-adm-prods");
    if(!wrap) return;
    var catOptions = content.categories.map(function(c){ return {value:c.id, label:c.name}; });
    var groups = content.categories.map(function(cat){
      var items = productsOf(cat.id);
      return '<div class="sr-catgroup">' +
        '<div class="sr-catgroup-head"><span data-cathead="'+esc(cat.id)+'">'+esc(cat.name)+'</span><span class="n">'+items.length+' поз.</span></div>' +
        items.map(function(p){
          return '<div class="sr-arritem">' +
            '<button class="rm" data-rm-prod="'+esc(p.id)+'" title="Удалить позицию">✕</button>' +
            galleryBlock(p) +
            field("Название позиции", p.name, "prod__"+p.id+"__name") +
            field("Артикул / код", p.code, "prod__"+p.id+"__code") +
            field("Характеристики (по одной в строке)", p.specs, "prod__"+p.id+"__specs", true) +
            field("Описание", p.text, "prod__"+p.id+"__text", true) +
            selectField("Категория", p.catId, "prod__"+p.id+"__catId", catOptions) +
            '</div>';
        }).join("") +
        '<button class="sr-addbtn" data-add-prod="'+esc(cat.id)+'">+ Добавить позицию в «'+esc(cat.name)+'»</button>' +
        '</div>';
    }).join("");
    var orphan = content.products.filter(function(p){ return !catById(p.catId); });
    if(orphan.length){
      groups += '<div class="sr-catgroup"><div class="sr-catgroup-head"><span>Без категории</span><span class="n">'+orphan.length+' поз.</span></div>' +
        orphan.map(function(p){
          return '<div class="sr-arritem">' +
            '<button class="rm" data-rm-prod="'+esc(p.id)+'">✕</button>' +
            field("Название позиции", p.name, "prod__"+p.id+"__name") +
            selectField("Категория", p.catId, "prod__"+p.id+"__catId", catOptions) +
            '</div>';
        }).join("") + '</div>';
    }
    wrap.innerHTML = groups;

    Array.prototype.slice.call(wrap.querySelectorAll("[data-key]")).forEach(function(el){
      var handler = function(){
        var parts = el.getAttribute("data-key").split("__");
        var p = prodById(parts[1]);
        if(!p) return;
        p[parts[2]] = el.value;
        if(parts[2] === "catId"){ renderProdsAdmin(); renderCatsAdmin(); }
        renderCatalog();
      };
      el.addEventListener("input", handler);
      if(el.tagName === "SELECT") el.addEventListener("change", handler);
    });
    bindGalleryTools(wrap);
    Array.prototype.slice.call(wrap.querySelectorAll("[data-rm-prod]")).forEach(function(b){
      b.onclick = function(){
        if(!confirm("Удалить позицию из каталога?")) return;
        var id = b.getAttribute("data-rm-prod");
        content.products = content.products.filter(function(p){ return p.id !== id; });
        renderProdsAdmin(); renderCatsAdmin(); renderCatalog();
      };
    });
    Array.prototype.slice.call(wrap.querySelectorAll("[data-add-prod]")).forEach(function(b){
      b.onclick = function(){
        var catId = b.getAttribute("data-add-prod");
        content.products.push({id:uid("p"), catId:catId, code:"", name:"Новая позиция", specs:"Характеристика 1\nХарактеристика 2", text:"", images:[]});
        renderProdsAdmin(); renderCatsAdmin(); renderCatalog();
      };
    });
  }

  function renderFeaturesAdmin(){
    var wrap = document.getElementById("sr-adm-features");
    if(!wrap) return;
    wrap.innerHTML = content.features.map(function(f, i){
      return '<div class="sr-arritem">' +
        (content.features.length > 1 ? '<button class="rm" data-rm-feat="'+i+'" title="Удалить преимущество">✕</button>' : '') +
        field("Заголовок", f.title, "feat__"+i+"__title") +
        field("Текст", f.text, "feat__"+i+"__text", true) + '</div>';
    }).join("");
    Array.prototype.slice.call(wrap.querySelectorAll("[data-key]")).forEach(function(el){
      el.addEventListener("input", function(){
        var parts = el.getAttribute("data-key").split("__");
        content.features[+parts[1]][parts[2]] = el.value;
        renderFeatures();
      });
    });
    Array.prototype.slice.call(wrap.querySelectorAll("[data-rm-feat]")).forEach(function(b){
      b.onclick = function(){
        if(!confirm("Удалить это преимущество?")) return;
        content.features.splice(+b.getAttribute("data-rm-feat"), 1);
        renderFeaturesAdmin(); renderFeatures();
      };
    });
  }

  function renderStatsAdmin(){
    var wrap = document.getElementById("sr-adm-stats");
    if(!wrap) return;
    wrap.innerHTML = content.stats.map(function(s, i){
      return '<div class="sr-arritem">' +
        (content.stats.length > 1 ? '<button class="rm" data-rm-stat="'+i+'" title="Удалить факт">✕</button>' : '') +
        field("Значение", s.value, "stat__"+i+"__value") +
        field("Подпись", s.label, "stat__"+i+"__label", true) + '</div>';
    }).join("");
    Array.prototype.slice.call(wrap.querySelectorAll("[data-key]")).forEach(function(el){
      el.addEventListener("input", function(){
        var parts = el.getAttribute("data-key").split("__");
        content.stats[+parts[1]][parts[2]] = el.value;
        renderStats();
      });
    });
    Array.prototype.slice.call(wrap.querySelectorAll("[data-rm-stat]")).forEach(function(b){
      b.onclick = function(){
        if(!confirm("Удалить этот факт?")) return;
        content.stats.splice(+b.getAttribute("data-rm-stat"), 1);
        renderStatsAdmin(); renderStats();
      };
    });
  }

  on("sr-admin-save", "click", function(){
    var save = $("sr-admin-save");
    save.textContent = "Сохранение…";
    saveContent().then(function(){
      save.textContent = "Сохранено ✓";
      render();
      toast(hasCloud ? "Изменения сохранены и видны всем посетителям" : "Изменения сохранены в этом браузере");
      setTimeout(function(){ save.textContent = "Сохранить изменения"; }, 1600);
    }).catch(function(err){
      save.textContent = "Сохранить изменения";
      var quota = err && (err.name === "QuotaExceededError" || /quota/i.test(String(err && err.message)));
      toast(quota ? "Не хватает места в браузере: удалите часть фото или уменьшите их размер" : "Не удалось сохранить. Попробуйте ещё раз");
    });
  });
  on("sr-admin-reset", "click", function(){
    if(!confirm("Вернуть исходное содержимое сайта? Ваши изменения будут потеряны.")) return;
    content = normalize(JSON.parse(JSON.stringify(DEFAULT)));
    activeCat = "all";
    saveContent().then(function(){ render(); renderAdminBody(); toast("Контент сброшен к исходному"); });
  });

  loadAll();
})();
