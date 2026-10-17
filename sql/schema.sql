-- Структура и стартовый контент сайта ООО «Адамко Сибирь».
-- Импортируется в phpMyAdmin: выберите базу → вкладка «Импорт» → этот файл.

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `features`;
DROP TABLE IF EXISTS `stats`;
DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `skey`   VARCHAR(64) NOT NULL COMMENT 'ключ настройки',
  `svalue` TEXT NOT NULL COMMENT 'значение',
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Тексты и настройки сайта';

CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`        VARCHAR(80)  NOT NULL COMMENT 'адрес: catalog.php?cat=slug',
  `code`        VARCHAR(40)  NOT NULL DEFAULT '',
  `name`        VARCHAR(190) NOT NULL,
  `description` TEXT         NOT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_category_slug` (`slug`),
  KEY `idx_category_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Категории каталога';

CREATE TABLE `products` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `slug`        VARCHAR(80)  NOT NULL COMMENT 'адрес: catalog.php?cat=..&prod=slug',
  `code`        VARCHAR(40)  NOT NULL DEFAULT '' COMMENT 'артикул',
  `name`        VARCHAR(190) NOT NULL,
  `specs`       TEXT         NOT NULL COMMENT 'характеристики, по одной в строке',
  `description` TEXT         NOT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_product_slug` (`slug`),
  KEY `idx_product_category` (`category_id`, `sort_order`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Позиции каталога';

CREATE TABLE `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(500) NOT NULL COMMENT 'путь вида uploads/файл.jpg или внешняя ссылка',
  `sort_order` INT          NOT NULL DEFAULT 0 COMMENT 'первое фото — главное',
  PRIMARY KEY (`id`),
  KEY `idx_image_product` (`product_id`, `sort_order`),
  CONSTRAINT `fk_image_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Фотографии позиций';

CREATE TABLE `features` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(190) NOT NULL,
  `text`       TEXT         NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Блок преимуществ';

CREATE TABLE `stats` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `value`      VARCHAR(190) NOT NULL,
  `label`      TEXT         NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Факты о компании';

INSERT INTO `settings` (`skey`, `svalue`) VALUES
('brandName', 'АДАМКО Сибирь'),
('tagline', 'новый вектор в горнодобывающем оборудовании'),
('logo', ''),
('logoShowText', '1'),
('logoHeight', '44'),
('logoPad', '0'),
('phone', '+7 (000) 000-00-00'),
('email', 'mail@adamko-sib.ru'),
('city', 'г. Новокузнецк, ул. Орджоникидзе, д. 35, офис 1209'),
('badge', 'Официальный партнёр JA Engineering Works (ЮАР)'),
('h1a', 'Горное и строительное'),
('h1b', 'оборудование'),
('heroText', 'ООО «Адамко Сибирь» — действующая компания в Новокузнецке, которая занимается оптовой торговлей горным и строительным оборудованием и техникой.'),
('heroCta', 'Связаться с нами'),
('yearsNum', 'С 2020 ГОДА'),
('yearsText', 'Компания зарегистрирована в Новокузнецке в 2020 году и специализируется на оптовых поставках машин и оборудования для добычи полезных ископаемых и строительства.'),
('yearsCta', 'О компании'),
('catalogEyebrow', 'Каталог оборудования'),
('catalogTitle', 'Горное и строительное оборудование'),
('catalogAllLabel', 'Все категории'),
('catalogAllCta', 'Открыть весь каталог'),
('catalogPageLead', 'Оборудование сгруппировано по категориям. Выберите категорию, чтобы посмотреть позиции и характеристики.'),
('megaTitle', 'Каталог'),
('catalogCta', 'Уточнить наличие'),
('catalogMoreCta', 'Подробнее'),
('aboutEyebrow', 'О компании'),
('aboutTitle', 'ООО «Адамко Сибирь»'),
('aboutText', 'ООО «Адамко Сибирь» — действующая компания в Новокузнецке, которая занимается оптовой торговлей горным и строительным оборудованием. Компания зарегистрирована в 2020 году и работает с горнодобывающими и строительными предприятиями Кузбасса и других регионов.\n\nМы являемся партнёром южноафриканской компании JA Engineering Works и поставляем проходческую и погрузочно-доставочную технику, которая по своим характеристикам не уступает ставшим недоступными зарубежным аналогам. Специалисты компании имеют большой опыт работы в горно-шахтном машиностроении и помогают подобрать технику под конкретные задачи заказчика.'),
('mapAddress', 'г. Новокузнецк, ул. Орджоникидзе, д. 35, офис 1209'),
('requisites', 'ИНН 4217199759 · ОГРН 1204200012389'),
('copyright', '© 2026 ООО «Адамко Сибирь». Все права защищены.');

INSERT INTO `categories` (`id`, `slug`, `code`, `name`, `description`, `sort_order`) VALUES
(1, 'prohodcheskoe-oborudovanie', 'К-01', 'Проходческое оборудование', 'Техника для проведения горных выработок и тоннелей: комбайны, комплексы и оборудование для крепления.', 1),
(2, 'pogruzochno-dostavochnye-mashiny', 'К-02', 'Погрузочно-доставочные машины', 'Подземный транспорт и погрузочная техника для откатки горной массы.', 2),
(3, 'burovoe-i-perforatornoe-oborudovanie', 'К-03', 'Буровое и перфораторное оборудование', 'Буровые установки, перфораторы и буровой инструмент под любые горно-геологические условия.', 3),
(4, 'stroitelnaya-tehnika', 'К-04', 'Строительная техника', 'Землеройная и карьерная техника для строительных и вскрышных работ.', 4),
(5, 'pnevmaticheskoe-i-vspomogatelnoe-oborudovanie', 'К-05', 'Пневматическое и вспомогательное оборудование', 'Компрессоры, гидромолоты, вентиляция и освещение выработок.', 5),
(6, 'zapasnye-chasti-i-komplektuyuschie', 'К-06', 'Запасные части и комплектующие', 'Узлы, агрегаты и расходные материалы для поставленной техники.', 6);

INSERT INTO `products` (`id`, `category_id`, `slug`, `code`, `name`, `specs`, `description`, `sort_order`) VALUES
(1, 1, 'prohodcheskiy-kombayn-izbiratelnogo-deystviya', 'П-101', 'Проходческий комбайн избирательного действия', 'Сечение выработки: до 30 м²\nМощность привода: 132 кВт\nПрочность породы: до 100 МПа', 'Комбайн для проведения горизонтальных и наклонных выработок по породам средней крепости. Поставляется с системой орошения и пылеподавления.', 1),
(2, 1, 'tonneleprohodcheskiy-kompleks', 'П-102', 'Тоннелепроходческий комплекс', 'Диаметр проходки: 3–6 м\nГидравлическая система подачи\nСистема пылеподавления', 'Комплекс для проходки тоннелей и наклонных стволов. Конфигурация подбирается под проект заказчика.', 2),
(3, 1, 'oborudovanie-dlya-krepleniya-vyrabotok', 'П-103', 'Оборудование для крепления выработок', 'Анкероустановщики\nКрепеустановочные манипуляторы\nАнкерная и металлическая крепь', 'Оборудование и материалы для возведения анкерной и рамной крепи.', 3),
(4, 2, 'pogruzochno-dostavochnaya-mashina-dizelnaya-6-t', 'ПДМ-6', 'Погрузочно-доставочная машина дизельная 6 т', 'Грузоподъёмность: 6 т\nОбъём ковша: 3 м³\nДвигатель: дизельный, с нейтрализатором', 'ПДМ для подземных работ: погрузка и доставка горной массы в выработках среднего сечения.', 4),
(5, 2, 'podzemnyy-avtosamosval-20-t', 'АС-20', 'Подземный автосамосвал 20 т', 'Грузоподъёмность: 20 т\nПривод: полный, 4х4\nСистема пожаротушения', 'Шарнирно-сочленённый самосвал для транспортировки руды и породы по подземным выработкам.', 5),
(6, 2, 'pogruzochnaya-mashina-s-nagrebayuschimi-lapami', 'ПНЛ-1', 'Погрузочная машина с нагребающими лапами', 'Производительность: до 2 м³/мин\nПривод: пневматический\nДля выработок малого сечения', 'Надёжная машина для погрузки горной массы в стеснённых условиях.', 6),
(7, 3, 'burovaya-ustanovka-samohodnaya', 'БУ-32', 'Буровая установка самоходная', 'Глубина бурения: до 32 м\nДиаметр шпура: 43–102 мм\nГидравлический перфоратор', 'Самоходная установка для бурения шпуров и скважин при проходке и очистной выемке.', 7),
(8, 3, 'perforator-pnevmaticheskiy', 'ПП-60', 'Перфоратор пневматический', 'Энергия удара: 60 Дж\nЧастота ударов: 2100 уд/мин\nДавление воздуха: 0,5 МПа', 'Ручной перфоратор для бурения шпуров по крепким породам.', 8),
(9, 3, 'burovoy-instrument', 'БИ-01', 'Буровой инструмент', 'Буровые штанги и коронки\nМуфты и переходники\nПод все распространённые типы перфораторов', 'Расходный буровой инструмент со склада и под заказ.', 9),
(10, 4, 'ekskavator-gusenichnyy', 'ЭК-21', 'Экскаватор гусеничный', 'Эксплуатационная масса: 21 т\nОбъём ковша: 1,2 м³\nГлубина копания: 6,5 м', 'Универсальный гусеничный экскаватор для вскрышных и строительных работ.', 10),
(11, 4, 'buldozer', 'БУЛ-18', 'Бульдозер', 'Мощность: 180 л.с.\nОтвал: 3,4 м\nГидромеханическая трансмиссия', 'Бульдозер для планировочных работ и формирования отвалов.', 11),
(12, 4, 'karernyy-samosval', 'КС-32', 'Карьерный самосвал', 'Грузоподъёмность: 32 т\nОбъём кузова: 18 м³\nУсиленная рама и подвеска', 'Самосвал для перевозки вскрыши и полезного ископаемого на карьерах.', 12),
(13, 5, 'kompressor-vintovoy', 'КВ-10', 'Компрессор винтовой', 'Производительность: 5–12 м³/мин\nДавление: до 1,0 МПа\nДизельный или электрический привод', 'Источник сжатого воздуха для пневмоинструмента и буровой техники.', 13),
(14, 5, 'gidravlicheskiy-molot', 'ГМ-30', 'Гидравлический молот', 'Энергия удара: 3000 Дж\nДля носителей 18–26 т\nКомплект рабочего инструмента', 'Навесной гидромолот для разрушения негабаритов и скальных пород.', 14),
(15, 5, 'ventilyacionnye-i-osvetitelnye-sistemy', 'ВО-01', 'Вентиляционные и осветительные системы', 'Вентиляторы местного проветривания\nШахтные светильники\nВоздуховоды и рукава', 'Оборудование для проветривания и освещения подземных выработок.', 15),
(16, 6, 'uzly-i-agregaty', 'ЗЧ-01', 'Узлы и агрегаты', 'Гидронасосы и гидромоторы\nРедукторы и мосты\nЭлементы ходовой части', 'Оригинальные и аналоговые узлы для горной и строительной техники.', 16),
(17, 6, 'rashodnye-materialy', 'ЗЧ-02', 'Расходные материалы', 'Фильтры и РТИ\nМасла и смазки\nЗубья и коронки ковшей', 'Расходники для регулярного обслуживания парка техники.', 17),
(18, 6, 'servisnye-komplekty', 'ЗЧ-03', 'Сервисные комплекты', 'Комплекты ТО\nРемкомплекты гидроцилиндров\nПоставка под заказ', 'Готовые комплекты для планового и аварийного ремонта.', 18);

INSERT INTO `features` (`id`, `title`, `text`, `sort_order`) VALUES
(1, 'Оптовые поставки', 'Работаем напрямую с производителями и поставляем горное и строительное оборудование оптовыми партиями.', 1),
(2, 'Партнёрство с ЮАР', 'Официальный партнёр JA Engineering Works — поставки проходческой техники, не уступающей зарубежным аналогам.', 2),
(3, 'Сопровождение техники', 'Помогаем с подбором техники под конкретные горно-геологические условия и её последующим обслуживанием.', 3);

INSERT INTO `stats` (`id`, `value`, `label`, `sort_order`) VALUES
(1, '2020', 'год основания компании', 1),
(2, 'ЮАР', 'эксклюзивный партнёр по проходческой технике', 2),
(3, 'Кузбасс', 'основной регион поставок и обслуживания', 3),
(4, 'B2B', 'работаем с горнодобывающими и строительными предприятиями', 4);

SET FOREIGN_KEY_CHECKS = 1;
