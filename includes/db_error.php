<?php
/** Понятное сообщение, если база недоступна или ещё не импортирована. */
http_response_code(503);
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Сайт временно недоступен</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{margin:0;background:#0b0d0f;color:#f4f6f7;font:15px/1.6 Arial,sans-serif;display:flex;min-height:100vh;align-items:center;justify-content:center;padding:24px}
.box{max-width:620px;border:1px solid #273038;border-radius:16px;background:#111519;padding:28px}
h1{margin:0 0 14px;font-size:22px}
code{background:#0b0e10;padding:2px 6px;border-radius:5px;color:#a8e05f}
ol{padding-left:20px;color:#aab3ba}
li{margin-bottom:8px}
</style>
</head>
<body>
<div class="box">
  <h1>Нет связи с базой данных</h1>
  <p>Сайт не смог прочитать контент. Обычно помогает одно из двух:</p>
  <ol>
    <li>В файле <code>config.php</code> указать имя базы, пользователя и пароль, выданные хостингом.</li>
    <li>В phpMyAdmin создать базу и импортировать в неё файл <code>sql/schema.sql</code>.</li>
  </ol>
  <p style="color:#6f7a83;font-size:13px;margin-bottom:0">Подробная инструкция — в файле README.md рядом с сайтом.</p>
</div>
</body>
</html>
