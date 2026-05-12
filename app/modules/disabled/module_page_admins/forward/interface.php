<?php
    /**
     * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
     *
     * @link https://steamcommunity.com/profiles/76561198038416053
     * @link https://github.com/M0st1ce
     *
     * @license GNU General Public License Version 3
     */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><?php echo $Translate->get_translate_phrase('_Admins_sb')?></h5>
            </div>
            <table class="table table-hover">
                <thead>
                <tr class="pointer">
                    <?php if( $General->arr_general['avatars'] != 0 ) {?><th class="text-right tb-avatar"></th><?php }?>
                    <th class="text-left"><?php echo $Translate->get_translate_phrase('_Admin') ?></th>
                    <th class="text-center">Группа</th>
                    <th class="text-center">Флаги</th>
                    <th class="text-center">Активен до</th>
                    <th class="text-center">Банов</th>
                    <th class="text-center">Мутов / Гагов</th>
                </tr>
                </thead>
                <tbody>
                <?php for ( $i = 0, $sz = sizeof( $res ); $i < $sz; $i++ ):
                    // steam_id в iks_admins — SteamID64 (VARCHAR 17 символов)
                    $admin_steam64 = (string) $res[$i]['steam_id'];
                    $General->get_js_relevance_avatar( $admin_steam64 );
                ?>
                    <tr class="pointer" onclick="location.href = '<?php echo sprintf('%sprofiles/%s/0/?search=1', $General->arr_general['site'], $admin_steam64)?>';">
                    <?php if( $General->arr_general['avatars'] != 0 ) {?>
                        <th class="text-right tb-avatar"><img class="rounded-circle" id="<?php echo $admin_steam64?>"<?php echo $i < '20' ? 'src' : 'data-src'?>="<?php echo $General->getAvatar($admin_steam64, 2)?>"></th>
                    <?php } ?>
                    <th class="text-left" <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>onclick="location.href = '<?php echo $General->arr_general['site'] ?>profiles/<?php echo $admin_steam64?>/0' "<?php } ?>>
                        <a <?php if ($Modules->array_modules['module_page_profiles']['setting']['status'] == '1'){ ?>href="<?php echo $General->arr_general['site'] ?>profiles/<?php echo $admin_steam64?>/0"<?php } ?>><?php echo action_text_clear( action_text_trim($res[$i]['name'], 13) )?></a>
                    </th>
                    <th class="text-center"><?php echo htmlspecialchars($res[$i]['group_name'])?></th>
                    <th class="text-center"><code><?php echo htmlspecialchars($res[$i]['flags'] ?? '')?></code></th>
                    <th class="text-center"><?php
                        if (empty($res[$i]['end_at'])) {
                            echo '<div class="color-red">Навсегда</div>';
                        } elseif ($res[$i]['end_at'] <= time()) {
                            echo '<div class="color-green"><strike>' . date('Y-m-d', $res[$i]['end_at']) . '</strike></div>';
                        } else {
                            echo date('Y-m-d', $res[$i]['end_at']);
                        }
                    ?></th>
                    <th class="text-center"><?php echo $res[$i]['bans_count']?></th>
                    <th class="text-center"><?php echo $res[$i]['comms_count']?></th>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
