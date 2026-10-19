<?php
require_once __DIR__ . '/../includes/admin_views.php';
require_login();

$leads = db()->query('SELECT * FROM leads ORDER BY received_at DESC')->fetchAll();

$rows = '';
foreach ($leads as $l) {
    $style = $l['is_read'] ? '' : 'font-weight:700';
    $date = (new DateTime($l['received_at']))->format('d.m.Y H:i');
    $rows .= "<tr style=\"$style\"><td>" . esc($date) . '</td><td>' . esc($l['name']) . '</td>'
        . '<td><a href="mailto:' . esc($l['email']) . '">' . esc($l['email']) . '</a></td>'
        . '<td>' . esc($l['phone']) . '</td><td>' . esc($l['company']) . '</td>'
        . '<td style="max-width:320px">' . esc($l['message']) . '</td></tr>';
}

$body = '<div class="a-card"><table class="a-table">
  <thead><tr><th>Дата</th><th>Имя</th><th>Email</th><th>Телефон</th><th>Компания</th><th>Сообщение</th></tr></thead>
  <tbody>' . ($rows ?: '<tr><td colspan="6">Пока нет заявок.</td></tr>') . '</tbody>
</table></div>';

echo admin_shell('Заявки с сайта', '/admin/leads.php', $body);

db()->exec('UPDATE leads SET is_read = 1');
