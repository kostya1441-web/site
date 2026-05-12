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

// Запрос на получение списка администраторов (IksAdmin)
$res = $Db->queryAll('IksAdmin', 0, 0,
    "SELECT
        a.id, a.steam_id, a.name, a.flags, a.immunity, a.end_at,
        IFNULL(g.name, 'Без группы') AS group_name,
        (SELECT COUNT(*) FROM `iks_bans` WHERE admin_id = a.id AND deleted_at IS NULL) AS bans_count,
        (SELECT COUNT(*) FROM `iks_comms` WHERE admin_id = a.id AND deleted_at IS NULL) AS comms_count
    FROM `iks_admins` a
    LEFT JOIN `iks_groups` g ON a.group_id = g.id
    WHERE a.deleted_at IS NULL AND a.is_disabled = 0
    ORDER BY a.created_at DESC");

$Modules->set_page_title( $General->arr_general['short_name'] . ' :: ' . $Translate->get_translate_phrase('_Admins_sb') );

$Modules->set_page_description( $General->arr_general['short_name'] . ' :: ' . $Translate->get_translate_phrase('_Admins_sb') );
