<?php
use App\Models\Message;
/** @var array $messages */
/** @var array $filters */
?>
<div class="admin-head">
    <div>
        <h1>Обращения с сайта</h1>
        <p>Вопросы из формы «Задать вопрос» на странице контактов<?= $newCount ? ', новых: ' . (int) $newCount : '' ?></p>
    </div>
</div>

<form class="filters" method="get" action="<?= u('/admin/messages') ?>">
    <input type="search" name="q" value="<?= e($filters['search']) ?>" placeholder="Имя, телефон или текст">
    <select name="status">
        <option value="">Все обращения</option>
        <?php foreach (Message::STATUSES as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn--primary" type="submit">Фильтр</button>
    <a class="btn btn--ghost" href="<?= u('/admin/messages') ?>">Сброс</a>
</form>

<?php if ($messages): ?>
    <div class="messages">
        <?php foreach ($messages as $item): ?>
            <article class="message <?= $item['status'] === 'new' ? 'message--new' : '' ?>">
                <header class="message__head">
                    <div>
                        <strong class="message__name"><?= e($item['name']) ?></strong>
                        <a class="message__phone" href="<?= e(phone_link($item['phone'])) ?>"><?= e($item['phone']) ?></a>
                        <?php if ($item['email']): ?>
                            <a class="message__phone" href="mailto:<?= e($item['email']) ?>"><?= e($item['email']) ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="message__meta">
                        <span class="chip <?= $item['status'] === 'new' ? 'chip--new' : 'chip--completed' ?>">
                            <?= e(Message::STATUSES[$item['status']] ?? $item['status']) ?>
                        </span>
                        <small class="muted"><?= date_ru($item['created_at']) ?></small>
                    </div>
                </header>

                <p class="message__text"><?= nl2br(e($item['message'])) ?></p>

                <?php if (!$item['mail_sent']): ?>
                    <p class="message__warn">Письмо на почту не ушло — сервер не смог его отправить. Обращение сохранено только здесь.</p>
                <?php endif; ?>

                <details class="message__note">
                    <summary>Заметка менеджера<?= $item['admin_note'] ? ' — заполнена' : '' ?></summary>
                    <form method="post" action="<?= u('/admin/messages/' . (int) $item['id'] . '/note') ?>" class="stack">
                        <?= csrf_field() ?>
                        <textarea name="admin_note" rows="3" placeholder="Что ответили клиенту"><?= e($item['admin_note']) ?></textarea>
                        <button class="btn btn--ghost btn--sm" type="submit">Сохранить заметку</button>
                    </form>
                </details>

                <footer class="message__actions">
                    <form method="post" action="<?= u('/admin/messages/' . (int) $item['id'] . '/status') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="<?= $item['status'] === 'new' ? 'processed' : 'new' ?>">
                        <button class="btn btn--ghost btn--sm" type="submit">
                            <?= $item['status'] === 'new' ? 'Отметить обработанным' : 'Вернуть в новые' ?>
                        </button>
                    </form>
                    <form method="post" action="<?= u('/admin/messages/' . (int) $item['id'] . '/delete') ?>"
                          onsubmit="return confirm('Удалить обращение от <?= e($item['name']) ?>?')">
                        <?= csrf_field() ?>
                        <button class="btn btn--danger btn--sm" type="submit">Удалить</button>
                    </form>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
    <?php \App\Core\View::partial('partials/pagination', ['pagination' => $pagination]); ?>
<?php else: ?>
    <div class="panel empty-box">
        <p>Обращений пока нет.</p>
        <p class="muted small">Сюда попадают вопросы из формы на странице «Контакты».</p>
    </div>
<?php endif; ?>
