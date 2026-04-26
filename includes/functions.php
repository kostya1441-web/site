<?php

function start_session_if_needed(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function all_menu_items(array $menu): array
{
    $items = [];
    foreach ($menu as $categoryItems) {
        foreach ($categoryItems as $item) {
            $items[$item['id']] = $item;
        }
    }

    return $items;
}

function cart_total(array $cart, array $allItems): int
{
    $total = 0;
    foreach ($cart as $itemId => $qty) {
        if (isset($allItems[$itemId])) {
            $total += $allItems[$itemId]['price'] * $qty;
        }
    }

    return $total;
}

function russian_date(string $date): string
{
    $months = [
        '01' => 'января', '02' => 'февраля', '03' => 'марта', '04' => 'апреля',
        '05' => 'мая', '06' => 'июня', '07' => 'июля', '08' => 'августа',
        '09' => 'сентября', '10' => 'октября', '11' => 'ноября', '12' => 'декабря',
    ];

    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[date('m', $timestamp)] ?? date('m', $timestamp);
    $year = date('Y', $timestamp);

    return "$day $month $year";
}
