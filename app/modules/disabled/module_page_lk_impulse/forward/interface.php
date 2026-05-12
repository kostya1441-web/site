<?php
/**
 * @author SAPSAN 隼 #3604 (modified for cs2-vip integration)
 *
 * @license GNU General Public License Version 3
 */
if( IN_LR != true ) { header('Location: ' . $General->arr_general['site']); exit;}
$Gateways = $LK->LkGetGatewaysOn();
if(isset( $_SESSION['user_admin'] )):?>
<aside class="sidebar-right unshow">
    <section class="sidebar">
        <div class="user-sidebar-right-block">
            <div class="info">
                <div class="details">
                    <div class="admin_type"><?php echo $Translate->translate( 'module_page_adminpanel','_Chief_admin')?></div>
                    <div class="admin_rights"><?php echo $Translate->translate( 'module_page_adminpanel','_All_access_rights')?></div>
                </div>
            </div>
        </div>
        <div class="card menu">
            <ul class="nav">
                <li <?php get_section('section','') == 'vip_packages' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url(2),'section','vip_packages')?>';">
                    <a>VIP Пакеты</a>
                </li>
                <li <?php get_section('section','') == 'users' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url(2),'section','users')?>';">
                    <a><?php echo $Translate->translate('module_page_lk_impulse','_UsersList')?></a>
                </li>
                <li <?php get_section('section','') == 'gateways' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url(2),'section','gateways')?>';">
                    <a><?php echo $Translate->translate('module_page_lk_impulse','_SettingsGateways')?></a>
                </li>
                <li <?php get_section('section','') == 'payments' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url(2),'section','payments')?>';">
                    <a><?php echo $Translate->translate('module_page_lk_impulse','_PaymentsList')?></a>
                </li>
                <li <?php get_section('section','') == 'promocodes' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url(2),'section','promocodes')?>';">
                    <a><?php echo $Translate->translate('module_page_lk_impulse','_Promo')?></a>
                </li>
                <li <?php get_section('section','') == 'logs' && print 'class="table-active"'?> onclick="location.href = '<?php echo set_url_section(get_url(2),'section','logs')?>';">
                    <a><?php echo $Translate->translate('module_page_lk_impulse','_Logs')?></a>
                </li>
            </ul>
        </div>
    </section>
</aside>
<?php endif;

// Секции доступны только авторизованным (для admin-секций) либо залогиненным (для user-секций)
$allowed_sections = ['gateways','payments','promocodes','logs','users','search','vip_packages'];
if(!empty($_GET['section']) && in_array($_GET['section'], $allowed_sections) && (isset($_SESSION['steamid32']) || isset($_SESSION['user_admin']))):?>
    <div class="row">
        <?php require MODULES . 'module_page_lk_impulse/includes/'.basename($_GET['section']).'.php'; ?>
    </div>
<?php else:?>

<?php // ---- Активный VIP текущего игрока ----
if(!empty($player_vip)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="badge"><i class="zmdi zmdi-shield-check zmdi-hc-fw"></i> Ваш VIP</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Сервер (sid)</th>
                            <th>Группа</th>
                            <th>Истекает</th>
                            <th>Статус</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($player_vip as $v): ?>
                        <tr>
                            <td><?php echo (int)$v['sid']?></td>
                            <td><code><?php echo htmlspecialchars($v['group'])?></code></td>
                            <td><?php echo $v['expires'] == 0 ? '<span class="color-green">Навсегда</span>' : date('d.m.Y H:i', $v['expires'])?></td>
                            <td><?php
                                if($v['expires'] == 0) echo '<span class="color-green">Активен</span>';
                                elseif(time() < $v['expires']) echo '<span class="color-green">Активен</span>';
                                else echo '<span class="color-red">Истёк</span>';
                            ?></td>
                        </tr>
                        <?php endforeach?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif?>

<div class="row">
    <?php if(!empty($vip_packages)): ?>
    <?php foreach($vip_packages as $pkg): ?>
    <div class="col-md-4" style="margin-bottom:20px;">
        <div class="card" style="height:100%;">
            <div class="card-header">
                <h5 class="badge"><i class="zmdi zmdi-shield zmdi-hc-fw"></i> <?php echo htmlspecialchars($pkg['name'])?></h5>
            </div>
            <div class="card-container" style="padding:15px; flex:1;">
                <?php if(!empty($pkg['description'])): ?>
                <p style="margin-bottom:10px; opacity:0.8;"><?php echo htmlspecialchars($pkg['description'])?></p>
                <?php endif?>
                <div style="margin-bottom:8px;"><i class="zmdi zmdi-time zmdi-hc-fw"></i> <b><?php echo (int)$pkg['duration_days']?></b> дней</div>
                <div style="margin-bottom:8px;"><i class="zmdi zmdi-label zmdi-hc-fw"></i> Группа: <code><?php echo htmlspecialchars($pkg['vip_group'])?></code></div>
                <div style="margin-bottom:8px;"><i class="zmdi zmdi-tv zmdi-hc-fw"></i> Сервер ID: <?php echo (int)$pkg['sid']?></div>
                <div style="font-size:1.4em; font-weight:bold; margin-top:10px; color: var(--span-color);"><?php echo number_format($pkg['price'],2,'.','')?> ₽</div>
            </div>
            <div class="card-bottom" style="padding:10px; text-align:center;">
                <button class="btn" onclick="openVipBuy(<?php echo (int)$pkg['id']?>, '<?php echo htmlspecialchars($pkg['name'],ENT_QUOTES)?>', <?php echo number_format($pkg['price'],2,'.','')?>)">
                    Купить
                </button>
            </div>
        </div>
    </div>
    <?php endforeach?>
    <?php else:?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-container" style="padding:20px; text-align:center; opacity:0.6;">
                VIP-пакеты пока не настроены. <?php if(isset($_SESSION['user_admin'])): ?>
                <a href="<?php echo set_url_section(get_url(2),'section','vip_packages')?>">Добавьте пакеты в панели управления.</a>
                <?php endif?>
            </div>
        </div>
    </div>
    <?php endif?>
</div>

<?php // ---- Модальное окно оплаты ---- ?>
<div id="vip-buy-modal" class="modal-window" style="display:none;">
    <div class="card" style="max-width:480px; margin:auto;">
        <div class="card-header">
            <h5 class="badge" id="vip-modal-title">Купить VIP</h5>
            <a href="#" title="Закрыть" class="modal-close badge" onclick="document.getElementById('vip-buy-modal').style.display='none'; return false;"><i class="zmdi zmdi-close zmdi-hc-fw"></i></a>
        </div>
        <form id="vip-pay-form" enctype="multipart/form-data" method="post">
            <input type="hidden" name="vip_package_id" id="vip_package_id">
            <div style="padding:15px;">
                <?php if(!empty($Gateways)):
                    if(count($Gateways) > 1 || $Gateways[0]['id'] == 5):?>
                    <div class="input-form text-center">
                        <div style="margin-bottom:10px;" class="input_text text-left"><?php echo $Translate->translate('module_page_lk_impulse','_ChangeGateway')?></div>
                        <?php $PCYM2 = 0; foreach($Gateways as $info):?>
                            <input type="radio" name="gatewayPay" value="<?php echo mb_strtolower($info['name_kassa'])?>" id="VGateway<?=$info['id']?>" class="gateways">
                            <label for="VGateway<?=$info['id']?>" style="background: url('<?php echo $General->arr_general['site'] . MODULES ?>module_page_lk_impulse/assets/gateways/<?php echo mb_strtolower($info['name_kassa'])?>.svg') no-repeat;background-position: center;" class="gateways-label"></label>
                            <?php if(mb_strtolower($info['name_kassa']) == 'yandexmoney' && !$PCYM2):?>
                            <input type="radio" name="gatewayPay" value="yandexmoneycard" id="VGateway99" class="gateways">
                            <label for="VGateway99" style="background: url('<?php echo $General->arr_general['site'] . MODULES ?>module_page_lk_impulse/assets/gateways/yandexmoneycard.svg') no-repeat;background-position: center;" class="gateways-label"></label>
                            <?php $PCYM2=1; endif; endforeach?>
                    </div>
                    <?php else:?>
                    <input type="hidden" name="gatewayPay" value="<?php echo mb_strtolower($Gateways[0]['name_kassa'])?>">
                    <?php endif;
                endif;?>

                <?php if(isset($_SESSION['steamid32'])):?>
                    <input type="hidden" name="steam" value="<?php echo $_SESSION['steamid32']?>">
                <?php else:?>
                    <div class="input-form">
                        <div class="input_text">STEAM ID:</div>
                        <input name="steam" placeholder="STEAM_1:1:390... / 7656119803... / https://steamcommunity.com/profiles/...">
                    </div>
                <?php endif?>

                <div style="margin-top:15px; font-size:1.2em;">
                    Сумма: <b id="vip-modal-price"></b> ₽
                </div>
            </div>
            <div class="card-bottom" style="padding:10px; text-align:center;">
                <input class="btn" type="submit" value="Оплатить">
            </div>
        </form>
        <div style="display:none;" id="vip-result"></div>
    </div>
</div>

<script>
function openVipBuy(pkgId, pkgName, pkgPrice) {
    document.getElementById('vip_package_id').value = pkgId;
    document.getElementById('vip-modal-title').textContent = 'Купить: ' + pkgName;
    document.getElementById('vip-modal-price').textContent = pkgPrice;
    document.getElementById('vip-buy-modal').style.display = 'flex';
    document.getElementById('vip-buy-modal').style.alignItems = 'center';
    document.getElementById('vip-buy-modal').style.justifyContent = 'center';
    document.getElementById('vip-buy-modal').style.position = 'fixed';
    document.getElementById('vip-buy-modal').style.top = '0';
    document.getElementById('vip-buy-modal').style.left = '0';
    document.getElementById('vip-buy-modal').style.width = '100%';
    document.getElementById('vip-buy-modal').style.height = '100%';
    document.getElementById('vip-buy-modal').style.background = 'rgba(0,0,0,0.6)';
    document.getElementById('vip-buy-modal').style.zIndex = '9999';
}
document.getElementById('vip-pay-form').addEventListener('submit', function(e){
    e.preventDefault();
    var form = document.getElementById('vip-pay-form');
    var data = new FormData(form);
    fetch(window.location.href, { method: 'POST', body: data })
        .then(r => r.json())
        .then(function(json) {
            if (json.location) {
                window.location.href = json.location;
            } else if (json.text) {
                document.getElementById('vip-result').style.display = 'block';
                document.getElementById('vip-result').innerHTML = '<div class="' + json.status + '">' + json.text + '</div>';
            } else if (json) {
                document.getElementById('vip-result').style.display = 'block';
                document.getElementById('vip-result').innerHTML = json;
            }
        });
});
</script>

<?php endif?>

<style type="text/css">
    .input-form { margin-bottom: 25px; }
    #vip-buy-modal .card { width: 100%; }
</style>
