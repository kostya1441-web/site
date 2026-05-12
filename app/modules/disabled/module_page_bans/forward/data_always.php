<?php // Проверка статуса бана игрока (IksAdmin)
if ( ! empty( $Modules->route ) && $Modules->route === 'profiles' ):
    if ( ! empty( $Db->db_data['IksAdmin'] ) ):
        $player_steam64 = $_SESSION['steamid'] ?? '';
        if ( ! empty( $player_steam64 ) ):
            $ban_check = $Db->query('IksAdmin', (int) $Db->db_data['IksAdmin'][0]['USER_ID'], (int) $Db->db_data['IksAdmin'][0]['DB_num'],
                "SELECT id, steam_id, end_at, duration, deleted_at
                 FROM `iks_bans`
                 WHERE steam_id = '{$Player->found[$Player->server_group]['steam_id']}'
                   AND deleted_at IS NULL
                   AND (duration = 0 OR end_at > UNIX_TIMESTAMP())
                 ORDER BY created_at DESC LIMIT 1");
            ! empty( $ban_check ) && $Player->get_profile_status()['priority'] < 3 && $Player->set_profile_status( $Translate->get_translate_phrase( '_Banned' ), '#ba0000', 3 );
        endif;
    endif;
endif;
