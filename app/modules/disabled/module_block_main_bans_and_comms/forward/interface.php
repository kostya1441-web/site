<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_phrase('_List_recent_bans')?></h5>
            </div>
            <div class=table-responsive>
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th class="text-center tb-game"><?php echo $Translate->get_translate_phrase('_Game') ?></th>
                        <th class="text-center"><?php echo $Translate->get_translate_phrase('_Date') ?></th>
                        <?php if( $General->arr_general['avatars'] != 0 ):?>
                        <th class="text-right tb-avatar"></th>
                        <?php endif?>
                        <th class="text-left"><?php echo $Translate->get_translate_phrase('_Player') ?></th>
                        <?php if( $General->arr_general['avatars'] != 0 ):?>
                        <th class="text-right tb-avatar"></th>
                        <?php endif?>
                        <th class="text-left"><?php echo $Translate->get_translate_phrase('_Admin') ?></th>
                        <th class="text-center"><?php echo $Translate->get_translate_phrase('_Term') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 0, $c_b = sizeof( $res_bans ); $i < $c_b; $i++):
                        $p64 = (string) $res_bans[$i]['steam_id'];
                        $a64 = !empty($res_bans[$i]['admin_steam_id']) ? (string) $res_bans[$i]['admin_steam_id'] : '';
                        $General->get_js_relevance_avatar( $p64 );
                        !empty($a64) && $General->get_js_relevance_avatar( $a64 );
                    ?><tr>
                            <th class="text-center tb-game"><img <?php $i < '20' ? print 'src' : print 'data-src'?>="<?php echo $General->arr_general['site'] ?>storage/cache/img/mods/<?php echo $mod?>.png"></th>
                            <th class="text-center"><?php echo date('Y-m-d', $res_bans[$i]['created_at']) ?></th>
                            <?php if( $General->arr_general['avatars'] != 0 ) {?>
                                <th class="text-right tb-avatar pointer" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $p64?>/?search=1' "<?php } ?>><img class="rounded-circle" id="<?php echo $p64?>"<?php echo $i < '20' ? 'src' : 'data-src'?>="<?php echo $General->getAvatar($p64, 2)?>"></th>
                            <?php } ?>
                            <th class="text-left pointer" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $p64?>/?search=1' "<?php } ?>>
                                <a <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>href="<?php echo $General->arr_general['site'] ?>profiles/<?php echo $p64?>/?search=1"<?php } ?>><?php echo action_text_clear( action_text_trim($res_bans[$i]['name'], 13) )?></a>
                            </th>
                            <?php if( $General->arr_general['avatars'] != 0 ) {?>
                            <th class="text-right tb-avatar <?php !empty($a64) && print 'a-type'?>" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1' && !empty($a64)){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $a64?>/?search=1' "<?php } ?>><img class="rounded-circle" id="<?php echo $a64?>"<?php echo $i < '20' ? 'src' : 'data-src'?>="<?php echo !empty($a64) ? $General->getAvatar($a64, 2) : $General->arr_general['site'].'storage/cache/img/avatars_random/20.jpg'?>"></th><?php }?>
                            <th class="text-left <?php !empty($a64) && print 'pointer'?>" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1' && !empty($a64)): ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $a64?>/?search=1' "<?php endif; ?>>
                                <a <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1' && !empty($a64)): ?>href="<?php echo $General->arr_general['site'] ?>profiles/<?php echo $a64?>/?search=1"<?php endif; ?>><?php echo action_text_clear( action_text_trim($res_bans[$i]['user'], 13) )?></a>
                            </th>
                            <th class="text-center"><?php
                                if ( $res_bans[$i]['deleted_at'] !== null ) {
                                    echo $ban_type[1];
                                } elseif ( $res_bans[$i]['duration'] == 0 ) {
                                    echo $ban_type[0];
                                } elseif ( time() >= $res_bans[$i]['end_at'] ) {
                                    echo '<div class="color-green"><strike>' . $Modules->action_time_exchange( intval($res_bans[$i]['duration'] / 60) ) . '</strike></div>';
                                } else {
                                    echo $Modules->action_time_exchange( intval($res_bans[$i]['duration'] / 60) );
                                }?>
                            </th>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_phrase('_List_recent_mut_gags')?></h5>
            </div>
            <div class=table-responsive>
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th class="text-center"><?php echo $Translate->get_translate_phrase('_Type') ?></th>
                        <th class="text-center"><?php echo $Translate->get_translate_phrase('_Date') ?></th>
                        <?php if( $General->arr_general['avatars'] != 0 ) {?><th class="text-right tb-avatar"></th><?php }?>
                        <th class="text-left"><?php echo $Translate->get_translate_phrase('_Player') ?></th>
                        <?php if( $General->arr_general['avatars'] != 0 ) {?><th class="text-right tb-avatar"></th><?php }?>
                        <th class="text-left"><?php echo $Translate->get_translate_phrase('_Admin') ?></th>
                        <th class="text-center"><?php echo $Translate->get_translate_phrase('_Term') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 0, $c_c = sizeof( $res_comms ); $i < $c_c; $i++):
                        $p64 = (string) $res_comms[$i]['steam_id'];
                        $a64 = !empty($res_comms[$i]['admin_steam_id']) ? (string) $res_comms[$i]['admin_steam_id'] : '';
                        $General->get_js_relevance_avatar( $p64 );
                        !empty($a64) && $General->get_js_relevance_avatar( $a64 );
                    ?><tr>
                            <th class="text-center tb-type"><?php
                                if ($res_comms[$i]['mute_type'] == 1) {
                                    $General->get_icon('zmdi', 'comment-text', null);
                                } elseif ($res_comms[$i]['mute_type'] == 2) {
                                    $General->get_icon('zmdi', 'mic-off', null);
                                } else {
                                    $General->get_icon('zmdi', 'mic', null);
                                }
                            ?></th>
                            <th class="text-center"><?php echo date('Y-m-d', $res_comms[$i]['created_at']) ?></th>
                            <?php if( $General->arr_general['avatars'] != 0 ) {?>
                                <th class="text-right tb-avatar pointer" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $p64?>/?search=1' "<?php } ?>><img class="rounded-circle" id="<?php echo $p64?>"<?php $i < '20' ? print 'src' : print 'data-src'?>="<?php echo $General->getAvatar($p64, 2)?>"></th>
                            <?php } ?>
                            <th class="text-left pointer" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $p64?>/?search=1' "<?php } ?>>
                                <a <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>href="<?php echo $General->arr_general['site'] ?>profiles/<?php echo $p64?>/?search=1"<?php } ?>><?php echo action_text_clear( action_text_trim($res_comms[$i]['name'], 13) )?></a>
                            </th>
                            <?php if( $General->arr_general['avatars'] != 0 ) {?>
                            <th class="text-right tb-avatar <?php !empty($a64) && print 'a-type'?>" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1' && !empty($a64)){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $a64?>/?search=1' "<?php } ?>><img class="rounded-circle" id="<?php echo $a64?>"<?php $i < '20' ? print 'src' : print 'data-src'?>="<?php echo !empty($a64) ? $General->getAvatar($a64, 2) : $General->arr_general['site'].'storage/cache/img/avatars_random/20.jpg'?>"></th><?php }?>
                            <th class="text-left <?php !empty($a64) && print 'pointer'?>" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1' && !empty($a64)): ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $a64?>/?search=1' "<?php endif; ?>>
                                <a <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1' && !empty($a64)): ?>href="<?php echo $General->arr_general['site'] ?>profiles/<?php echo $a64?>/?search=1"<?php endif; ?>><?php echo action_text_clear( action_text_trim($res_comms[$i]['user'], 13) )?></a>
                            </th>
                            <th class="text-center"><?php
                                if ( $res_comms[$i]['deleted_at'] !== null ) {
                                    echo $comms_type[1];
                                } elseif ( $res_comms[$i]['duration'] == 0 ) {
                                    echo $comms_type[0];
                                } elseif ( time() >= $res_comms[$i]['end_at'] ) {
                                    echo '<div class="color-green"><strike>' . $Modules->action_time_exchange( intval($res_comms[$i]['duration'] / 60) ) . '</strike></div>';
                                } else {
                                    echo $Modules->action_time_exchange( intval($res_comms[$i]['duration'] / 60) );
                                }?>
                            </th>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
