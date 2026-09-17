<?php

namespace App\Core;

/**
 * База недоступна: неверные доступы, не запущен MySQL или магазин ещё не установлен.
 * Ловится в точке входа, чтобы показать человеку понятную страницу вместо трассировки.
 */
class DatabaseUnavailable extends \RuntimeException
{
}
