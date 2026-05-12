<?php
if(IN_LR != true || !isset($_SESSION['user_admin'])) { header('Location: ' . $General->arr_general['site']); exit; }
$all_packages = $LK->LkGetVipPackages();
?>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5 class="badge"><i class="zmdi zmdi-shield zmdi-hc-fw"></i> VIP Пакеты</h5>
        </div>
        <div class="card-container">
            <p style="opacity:0.7; margin-bottom:15px;">
                Пакеты отображаются на странице доната. После покупки VIP автоматически выдаётся в таблицу
                <code>vip_users</code> (плагин <b>cs2-vip by Pisex</b>).
                Убедитесь, что соединение <b>Vips</b> настроено в Панели управления → Базы данных.
            </p>

            <div class="table-responsive">
                <table class="table table-hover mb-0" id="vip-pkg-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Название</th>
                        <th>Группа</th>
                        <th>Дней</th>
                        <th>Цена (₽)</th>
                        <th>Server ID (sid)</th>
                        <th>Описание</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($all_packages)): ?>
                    <tr><td colspan="9" class="text-center" style="opacity:0.6;">Нет пакетов. Добавьте первый.</td></tr>
                    <?php endif?>
                    <?php foreach($all_packages as $pkg): ?>
                    <tr id="vip-pkg-row-<?php echo $pkg['id']?>">
                        <td><?php echo $pkg['id']?></td>
                        <td><?php echo htmlspecialchars($pkg['name'])?></td>
                        <td><code><?php echo htmlspecialchars($pkg['vip_group'])?></code></td>
                        <td><?php echo $pkg['duration_days']?></td>
                        <td><?php echo number_format($pkg['price'],2,'.','')?></td>
                        <td><?php echo $pkg['sid']?></td>
                        <td style="max-width:200px; white-space:normal;"><?php echo htmlspecialchars($pkg['description'])?></td>
                        <td><?php echo $pkg['status'] ? '<span class="color-green">Активен</span>' : '<span style="opacity:0.5;">Выкл</span>'?></td>
                        <td>
                            <button class="btn" style="margin:2px; padding:4px 10px;" onclick="editVipPkg(<?php echo htmlspecialchars(json_encode($pkg), ENT_QUOTES)?>)">
                                <i class="zmdi zmdi-edit"></i>
                            </button>
                            <button class="btn" style="margin:2px; padding:4px 10px; background:var(--danger-color,#d9534f);" onclick="deleteVipPkg(<?php echo $pkg['id']?>, '<?php echo htmlspecialchars($pkg['name'],ENT_QUOTES)?>')">
                                <i class="zmdi zmdi-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach?>
                    </tbody>
                </table>
            </div>

            <hr>
            <h6 id="vip-form-title" style="margin-bottom:15px; font-weight:bold;">Добавить пакет</h6>
            <form id="vip-pkg-form" enctype="multipart/form-data" method="post" style="max-width:600px;">
                <input type="hidden" name="pkg_id" id="pkg_id">
                <div class="input-form">
                    <div class="input_text">Название <small>(отображается игрокам)</small></div>
                    <input type="text" name="pkg_name" id="pkg_name" placeholder="VIP Bronze — 30 дней" required>
                </div>
                <div class="input-form">
                    <div class="input_text">Группа VIP <small>(совпадает с groups.ini в плагине)</small></div>
                    <input type="text" name="pkg_group" id="pkg_group" placeholder="vip" required>
                </div>
                <div class="input-form" style="display:flex; gap:20px;">
                    <div style="flex:1;">
                        <div class="input_text">Количество дней</div>
                        <input type="number" name="pkg_days" id="pkg_days" value="30" min="1" required>
                    </div>
                    <div style="flex:1;">
                        <div class="input_text">Цена (₽)</div>
                        <input type="number" step="0.01" name="pkg_price" id="pkg_price" value="199" min="1" required>
                    </div>
                    <div style="flex:1;">
                        <div class="input_text">Server ID (sid)</div>
                        <input type="number" name="pkg_sid" id="pkg_sid" value="1" min="0" required>
                    </div>
                </div>
                <div class="input-form">
                    <div class="input_text">Описание <small>(опционально)</small></div>
                    <input type="text" name="pkg_desc" id="pkg_desc" placeholder="Доступ к VIP командам, скин, и т.д.">
                </div>
                <div class="input-form">
                    <label>
                        <input type="checkbox" name="pkg_status" id="pkg_status" checked> Активен (виден игрокам)
                    </label>
                </div>
                <div class="btn_form">
                    <button class="btn" type="submit" id="vip-pkg-submit">Добавить</button>
                    <button class="btn" type="button" id="vip-pkg-cancel" style="display:none; margin-left:10px; opacity:0.7;" onclick="resetVipForm()">Отмена</button>
                </div>
            </form>
            <div id="vip-pkg-result" style="margin-top:10px;"></div>
        </div>
    </div>
</div>

<script>
function editVipPkg(pkg) {
    document.getElementById('pkg_id').value      = pkg.id;
    document.getElementById('pkg_name').value    = pkg.name;
    document.getElementById('pkg_group').value   = pkg.vip_group;
    document.getElementById('pkg_days').value    = pkg.duration_days;
    document.getElementById('pkg_price').value   = pkg.price;
    document.getElementById('pkg_sid').value     = pkg.sid;
    document.getElementById('pkg_desc').value    = pkg.description;
    document.getElementById('pkg_status').checked = pkg.status == 1;
    document.getElementById('vip-form-title').textContent = 'Редактировать пакет #' + pkg.id;
    document.getElementById('vip-pkg-submit').textContent = 'Сохранить';
    document.getElementById('vip-pkg-cancel').style.display = 'inline-block';
    document.getElementById('vip-pkg-form').scrollIntoView({behavior:'smooth'});
}
function resetVipForm() {
    document.getElementById('pkg_id').value = '';
    document.getElementById('vip-pkg-form').reset();
    document.getElementById('vip-form-title').textContent = 'Добавить пакет';
    document.getElementById('vip-pkg-submit').textContent = 'Добавить';
    document.getElementById('vip-pkg-cancel').style.display = 'none';
}
function deleteVipPkg(id, name) {
    if (!confirm('Удалить пакет "' + name + '"?')) return;
    var fd = new FormData();
    fd.append('pkg_delete', id);
    fetch(window.location.href, {method:'POST', body: fd})
        .then(r => r.json())
        .then(function(json){
            document.getElementById('vip-pkg-result').innerHTML = '<div class="' + json.status + '">' + json.text + '</div>';
            if (json.status === 'success') {
                var row = document.getElementById('vip-pkg-row-' + id);
                if (row) row.remove();
            }
        });
}
document.getElementById('vip-pkg-form').addEventListener('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);
    fetch(window.location.href, {method:'POST', body: fd})
        .then(r => r.json())
        .then(function(json){
            document.getElementById('vip-pkg-result').innerHTML = '<div class="' + json.status + '">' + json.text + '</div>';
            if (json.status === 'success') {
                setTimeout(function(){ location.reload(); }, 800);
            }
        });
});
</script>
