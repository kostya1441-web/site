<?php
/**
 * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
 *
 * @link https://steamcommunity.com/profiles/76561198038416053
 * @link https://github.com/M0st1ce
 *
 * @license GNU General Public License Version 3
 */

empty( $Db->db_data['IksAdmin'] ) && get_iframe( '012','Не найден мод - IksAdmin  :: /storage/cache/sessions/db.php' );

// Количество банов на странице.
define('PLAYERS_ON_PAGE', '80');

// Номер страницы.
$page_num = (int) intval ( get_section( 'num', '1' ) );

// Типы банов.
$ban_type = [
    0 => '<div class="color-red">' . $Translate->get_translate_phrase('_Forever') . '</div>',
    1 => '<div class="color-blue">' . $Translate->get_translate_phrase('_Unban') . '</div>',
    2 => '<strike>Истёк</strike>'
];

// CS2
$mod = $Db->db_data['IksAdmin'][0]['mod'];

// Подсчёт кол-ва страниц
$page_max = ceil($Db->queryNum('IksAdmin', $Db->db_data['IksAdmin'][0]['USER_ID'], $Db->db_data['IksAdmin'][0]['DB_num'], "SELECT COUNT(*) FROM `iks_bans`")[0] / PLAYERS_ON_PAGE);
$page_max = max(1, $page_max);

$page_num_min = ($page_num - 1) * PLAYERS_ON_PAGE;

( $page_num > $page_max || $page_num <= '0' ) && header('Location: ' . $General->arr_general['site']);

// Запрос на получение информации о банах (IksAdmin)
$res = $Db->queryAll('IksAdmin', $Db->db_data['IksAdmin'][0]['USER_ID'], $Db->db_data['IksAdmin'][0]['DB_num'],
    "SELECT
        b.id, b.steam_id, b.name, b.ip,
        b.duration, b.reason, b.ban_type,
        b.created_at, b.end_at, b.deleted_at,
        b.unban_reason,
        IFNULL(a.name, 'Console') AS admin_name,
        a.steam_id AS admin_steam_id
    FROM `iks_bans` b
    LEFT JOIN `iks_admins` a ON b.admin_id = a.id
    ORDER BY b.created_at DESC
    LIMIT {$page_num_min}, " . PLAYERS_ON_PAGE);

// Задаём заголовок страницы.
$Modules->set_page_title( $General->arr_general['short_name'] . ' :: ' . $Translate->get_translate_phrase('_Bans') . ' :: ' . $Translate->get_translate_phrase('_Page') . ' ' . $page_num );

// Задаём описание страницы.
$Modules->set_page_description( $General->arr_general['short_name'] . ' :: ' . $Translate->get_translate_phrase('_Bans') . ' :: ' . $Translate->get_translate_phrase('_Page') . ' ' . $page_num );
