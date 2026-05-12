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

// Типы банов.
$ban_type = [
    0 => '<div class="color-red">' . $Translate->get_translate_phrase('_Forever') . '</div>',
    1 => '<div class="color-blue">' . $Translate->get_translate_phrase('_Unban') . '</div>',
    2 => '<strike>Истёк</strike>'
];

// Типы мутов.
$comms_type = [
    0 => '<div class="color-red">' . $Translate->get_translate_phrase('_Forever') . '</div>',
    1 => '<div class="color-blue">' . $Translate->get_translate_phrase('_Uncomm') . '</div>',
    2 => '<strike>Истёк</strike>'
];

// CS2
$mod = $Db->db_data['IksAdmin'][0]['mod'];

// Последние 10 банов (IksAdmin)
$res_bans = $Db->queryAll('IksAdmin', $Db->db_data['IksAdmin'][0]['USER_ID'], $Db->db_data['IksAdmin'][0]['DB_num'],
    "SELECT
        b.steam_id, b.name, b.duration, b.reason,
        b.created_at, b.end_at, b.deleted_at,
        IFNULL(a.name, 'Console') AS user,
        a.steam_id AS admin_steam_id
    FROM `iks_bans` b
    LEFT JOIN `iks_admins` a ON b.admin_id = a.id
    ORDER BY b.created_at DESC LIMIT 10");

// Последние 10 мутов/гагов (IksAdmin)
$res_comms = $Db->queryAll('IksAdmin', $Db->db_data['IksAdmin'][0]['USER_ID'], $Db->db_data['IksAdmin'][0]['DB_num'],
    "SELECT
        c.steam_id, c.name, c.mute_type, c.duration, c.reason,
        c.created_at, c.end_at, c.deleted_at,
        IFNULL(a.name, 'Console') AS user,
        a.steam_id AS admin_steam_id
    FROM `iks_comms` c
    LEFT JOIN `iks_admins` a ON c.admin_id = a.id
    ORDER BY c.created_at DESC LIMIT 10");
