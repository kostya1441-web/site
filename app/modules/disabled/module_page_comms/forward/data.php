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

// Количество записей на странице.
define('PLAYERS_ON_PAGE', '80');

// Номер страницы.
$page_num = (int) intval ( get_section( 'num', '1' ) );

// Типы мутов: 0 - voice (mute), 1 - chat (gag), 2 - both (silence)
$comms_type = [
    0 => '<div class="color-red">' . $Translate->get_translate_phrase('_Forever') . '</div>',
    1 => '<div class="color-blue">' . $Translate->get_translate_phrase('_Uncomm') . '</div>',
    2 => '<strike>Истёк</strike>'
];

// CS2
$mod = $Db->db_data['IksAdmin'][0]['mod'];

// Подсчёт кол-ва страниц
$page_max = ceil($Db->queryNum('IksAdmin', $Db->db_data['IksAdmin'][0]['USER_ID'], $Db->db_data['IksAdmin'][0]['DB_num'], "SELECT COUNT(*) FROM `iks_comms`")[0] / PLAYERS_ON_PAGE);
$page_max = max(1, $page_max);

$page_num_min = ($page_num - 1) * PLAYERS_ON_PAGE;

( $page_num > $page_max || $page_num <= '0' ) && header('Location: ' . $General->arr_general['site']);

// Запрос на получение информации о мутах/гагах (IksAdmin)
$res = $Db->queryAll('IksAdmin', $Db->db_data['IksAdmin'][0]['USER_ID'], $Db->db_data['IksAdmin'][0]['DB_num'],
    "SELECT
        c.id, c.steam_id, c.name, c.ip,
        c.mute_type, c.duration, c.reason,
        c.created_at, c.end_at, c.deleted_at,
        c.unban_reason,
        IFNULL(a.name, 'Console') AS admin_name,
        a.steam_id AS admin_steam_id
    FROM `iks_comms` c
    LEFT JOIN `iks_admins` a ON c.admin_id = a.id
    ORDER BY c.created_at DESC
    LIMIT {$page_num_min}, " . PLAYERS_ON_PAGE);

// Задаём заголовок страницы.
$Modules->set_page_title( $General->arr_general['short_name'] . ' :: ' . $Translate->get_translate_phrase('_Comms') . ' :: ' . $Translate->get_translate_phrase('_Page') . ' ' . $page_num );

// Задаём описание страницы.
$Modules->set_page_description( $General->arr_general['short_name'] . ' :: ' . $Translate->get_translate_phrase('_Comms') . ' :: ' . $Translate->get_translate_phrase('_Page') . ' ' . $page_num );
