-- Миграция для уже установленных сайтов: добавляет архивацию выполненных заказов.
-- Примените один раз, если база данных была создана до появления этой функции.

ALTER TABLE `orders`
  ADD COLUMN `archived_at` DATETIME NULL DEFAULT NULL AFTER `status`;

-- Переносим в архив уже выполненные ранее заказы
UPDATE `orders` SET `archived_at` = `updated_at` WHERE `status` = 'completed' AND `archived_at` IS NULL;
