<?php

$conceptText = '«Квартирник» — это кафе-бар в Новокузнецке с атмосферой домашних вечеринок: мягкий свет, кирпичный лофт, живая музыка и камерные встречи.';

$events = [
    [
        'id' => 1,
        'date' => '2026-05-02',
        'title' => 'Акустический вечер: Группа «Тепло»',
        'artist' => 'Группа «Тепло»',
        'description' => 'Ламповая программа из авторских песен и каверов в формате квартирника.',
    ],
    [
        'id' => 2,
        'date' => '2026-05-09',
        'title' => 'Джаз и винил',
        'artist' => 'DJ Vinyl + Live Sax',
        'description' => 'Мягкий джазовый сет, виниловые пластинки и вечерняя барная карта.',
    ],
    [
        'id' => 3,
        'date' => '2026-05-16',
        'title' => 'Поэтический open mic',
        'artist' => 'Гости и резиденты',
        'description' => 'Камерный вечер стихов, музыки и импровизации.',
    ],
];

$menu = [
    'Еда' => [
        ['id' => 101, 'name' => 'Брускетта с томатами', 'description' => 'Хрустящий багет, томаты, базилик, оливковое масло.', 'price' => 390, 'image' => 'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?auto=format&fit=crop&w=800&q=80'],
        ['id' => 102, 'name' => 'Паста с грибами', 'description' => 'Сливочный соус, шампиньоны, пармезан.', 'price' => 560, 'image' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=800&q=80'],
        ['id' => 103, 'name' => 'Тёплый салат с курицей', 'description' => 'Микс салатов, соус мед-горчица, вяленые томаты.', 'price' => 520, 'image' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?auto=format&fit=crop&w=800&q=80'],
    ],
    'Напитки' => [
        ['id' => 201, 'name' => 'Латте «Вечерний»', 'description' => 'Кофе, молочная пенка, корица.', 'price' => 280, 'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=800&q=80'],
        ['id' => 202, 'name' => 'Облепиховый чай', 'description' => 'Яркий чай с мёдом и апельсином.', 'price' => 320, 'image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=800&q=80'],
    ],
    'Бар' => [
        ['id' => 301, 'name' => 'Коктейль «Кирпичный Лофт»', 'description' => 'Бурбон, вишня, биттер, дымный аромат.', 'price' => 490, 'image' => 'https://images.unsplash.com/photo-1470337458703-46ad1756a187?auto=format&fit=crop&w=800&q=80'],
        ['id' => 302, 'name' => 'Авторский глинтвейн', 'description' => 'Красное вино, специи, цитрус.', 'price' => 430, 'image' => 'https://images.unsplash.com/photo-1514361892635-eae31ec02d1a?auto=format&fit=crop&w=800&q=80'],
    ],
];
