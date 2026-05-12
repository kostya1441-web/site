<?php // Проверка статуса администратора игрока (IksAdmin)
if ( ! empty( $Modules->route ) && $Modules->route === 'profiles' ):
    if ( ! empty( $Db->db_data['IksAdmin'] ) ):
        $view_steam64 = $Player->found[$Player->server_group]['steam_id'] ?? '';
        if ( ! empty( $view_steam64 ) ):
            $admin_check = $Db->query('IksAdmin', (int) $Db->db_data['IksAdmin'][0]['USER_ID'], (int) $Db->db_data['IksAdmin'][0]['DB_num'],
                "SELECT a.steam_id, IFNULL(g.name, a.flags) AS role
                 FROM `iks_admins` a
                 LEFT JOIN `iks_groups` g ON a.group_id = g.id
                 WHERE a.steam_id = '{$view_steam64}'
                   AND a.deleted_at IS NULL
                   AND a.is_disabled = 0
                   AND (a.end_at IS NULL OR a.end_at > UNIX_TIMESTAMP())
                 LIMIT 1");
            ! empty( $admin_check ) && $Player->get_profile_status()['priority'] < 10 && $Player->set_profile_status( $admin_check['role'], '#ff6d0a', 10 );
        endif;
    endif;
endif;
