<?php
declare(strict_types=1);

/** Фирменный знак: глобус в зелёной капле-указателе и стрелка-вектор. */
function emblem_svg(string $uid = '1'): string
{
    $clip = 'sr-globe-' . $uid;
    return '<svg viewBox="0 0 100 100" role="img" aria-label="Знак «Адамко Сибирь»">'
        . '<path d="M5 60 C12 84 40 92 64 80 C70 77 74 72 76.5 67" fill="none" stroke="var(--brand)" stroke-width="9" stroke-linecap="round"/>'
        . '<path d="M85.4 49.1 L85.4 71.5 L67.6 62.5 Z" fill="var(--brand)"/>'
        . '<path d="M64.8 54.2 L22 72 L33.8 27.1 A23 23 0 1 1 64.8 54.2 Z" fill="var(--brand)"/>'
        . '<defs><clipPath id="' . $clip . '"><circle cx="32" cy="35" r="18.5"/></clipPath></defs>'
        . '<circle cx="32" cy="35" r="18.5" fill="#f2f5f7"/>'
        . '<g clip-path="url(#' . $clip . ')" fill="#7f8b95">'
        . '<path d="M32 29 q7 2 5 8 q-3 8 -8 11 q-3 -7 -1 -11 q1 -6 4 -8 z"/>'
        . '<path d="M21 21 q10 -3 15 3 q-5 4 -11 4 q-6 0 -4 -7 z"/>'
        . '<path d="M17 39 q5 2 4 7 q-6 -1 -6 -5 z"/>'
        . '<path d="M41 40 q4 1 4 5 q-4 1 -5 -3 z"/>'
        . '</g>'
        . '<g fill="none" stroke="#c6ced4" stroke-width="1.1">'
        . '<circle cx="32" cy="35" r="18.5"/>'
        . '<ellipse cx="32" cy="35" rx="8" ry="18.5"/>'
        . '<path d="M13 29h38M12 42h40" clip-path="url(#' . $clip . ')"/>'
        . '</g>'
        . '</svg>';
}

/** Контур шестерни: зубья строятся по кругу. */
function gear_path(float $cx, float $cy, float $rOut, float $rIn, int $teeth): string
{
    $d = '';
    $step = M_PI * 2 / $teeth;
    $pt = static fn(float $r, float $a): string =>
        number_format($cx + $r * cos($a), 2, '.', '') . ' ' . number_format($cy + $r * sin($a), 2, '.', '');
    for ($i = 0; $i < $teeth; $i++) {
        $a = $i * $step;
        $d .= ($i ? 'L' : 'M') . $pt($rIn, $a)
            . 'L' . $pt($rOut, $a + $step * 0.14)
            . 'L' . $pt($rOut, $a + $step * 0.36)
            . 'L' . $pt($rIn, $a + $step * 0.5);
    }
    return $d . 'Z';
}

function gear_markup(array $o): string
{
    $cx = (float) $o['cx'];
    $cy = (float) $o['cy'];
    $rOut = (float) $o['rOut'];
    $rIn = (float) $o['rIn'];
    $teeth = (int) ($o['teeth'] ?? 12);
    $stroke = $o['stroke'] ?? '#e35f1e';
    $fill = $o['fill'] ?? '#1b2026';
    $accent = $o['accent'] ?? '#f0a94a';
    $sw = (string) ($o['sw'] ?? 2);
    $spokes = (int) ($o['spokes'] ?? 5);

    $s = '<path d="' . gear_path($cx, $cy, $rOut, $rIn, $teeth) . '" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="' . $sw . '" stroke-linejoin="round"/>';
    $s .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . number_format($rIn * 0.62, 2, '.', '') . '" fill="none" stroke="' . $accent . '" stroke-width="' . $sw . '" opacity=".7"/>';
    for ($i = 0; $i < $spokes; $i++) {
        $a = $i * M_PI * 2 / $spokes;
        $s .= '<line x1="' . number_format($cx + $rIn * 0.26 * cos($a), 2, '.', '') . '" y1="' . number_format($cy + $rIn * 0.26 * sin($a), 2, '.', '')
            . '" x2="' . number_format($cx + $rIn * 0.60 * cos($a), 2, '.', '') . '" y2="' . number_format($cy + $rIn * 0.60 * sin($a), 2, '.', '')
            . '" stroke="' . $accent . '" stroke-width="' . $sw . '" stroke-linecap="round" opacity=".55"/>';
    }
    $s .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . number_format($rIn * 0.24, 2, '.', '') . '" fill="#0b0d0f" stroke="' . $stroke . '" stroke-width="' . $sw . '"/>';
    return $s;
}

/** Три сцепленные шестерни на главном экране. */
function hero_gears_svg(): string
{
    return '<svg viewBox="0 0 400 400" id="sr-hero-gears" aria-hidden="true">'
        . '<circle cx="200" cy="200" r="182" fill="none" stroke="#333941" stroke-width="1"/>'
        . '<g class="gear" style="animation-duration:18s">' . gear_markup(['cx' => 200, 'cy' => 205, 'rOut' => 112, 'rIn' => 90, 'teeth' => 14, 'spokes' => 6]) . '</g>'
        . '<g class="gear rev" style="animation-duration:11s">' . gear_markup(['cx' => 315, 'cy' => 96, 'rOut' => 68, 'rIn' => 53, 'teeth' => 10, 'spokes' => 5, 'stroke' => '#f0a94a', 'accent' => '#e35f1e', 'fill' => '#181d23']) . '</g>'
        . '<g class="gear rev" style="animation-duration:14s">' . gear_markup(['cx' => 86, 'cy' => 322, 'rOut' => 54, 'rIn' => 42, 'teeth' => 9, 'spokes' => 4, 'stroke' => '#7d8892', 'accent' => '#e35f1e', 'fill' => '#151a1f']) . '</g>'
        . '</svg>';
}

/** Маленькая шестерня: кнопка админки, шапка панели, заглушка фото. */
function small_gear_svg(string $extraClass = ''): string
{
    $cls = trim('gear ' . $extraClass);
    return '<svg viewBox="0 0 64 64" aria-hidden="true"><g class="' . $cls . '">'
        . gear_markup(['cx' => 32, 'cy' => 32, 'rOut' => 28, 'rIn' => 21, 'teeth' => 10, 'spokes' => 4, 'sw' => 3, 'fill' => 'none', 'stroke' => 'currentColor', 'accent' => 'currentColor'])
        . '</g></svg>';
}

/** Стрелочный индикатор вместо отсутствующего фото. */
function gauge_svg(int $seed): string
{
    $angle = ($seed * 57) % 180 - 90;
    $x = number_format(21 + 12 * cos($angle * M_PI / 180), 2, '.', '');
    $y = number_format(21 + 12 * sin($angle * M_PI / 180), 2, '.', '');
    return '<svg width="42" height="42" viewBox="0 0 42 42" aria-hidden="true">'
        . '<circle cx="21" cy="21" r="18" fill="none" stroke="#3d434c" stroke-width="2"/>'
        . '<circle cx="21" cy="21" r="18" fill="none" stroke="#e35f1e" stroke-width="2" stroke-dasharray="70 200"/>'
        . '<line x1="21" y1="21" x2="' . $x . '" y2="' . $y . '" stroke="#f0a94a" stroke-width="2" stroke-linecap="round"/>'
        . '<circle cx="21" cy="21" r="2.5" fill="#e35f1e"/></svg>';
}

/** Иконки блока преимуществ. */
function feature_icon(int $index): string
{
    $icons = [
        '<svg viewBox="0 0 42 42" width="42" height="42"><rect x="6" y="6" width="30" height="30" fill="none" stroke="#e35f1e" stroke-width="2"/><rect x="14" y="14" width="14" height="14" fill="none" stroke="#f0a94a" stroke-width="2"/></svg>',
        '<svg viewBox="0 0 42 42" width="42" height="42"><path d="M21 5 L36 13 V27 L21 37 L6 27 V13 Z" fill="none" stroke="#e35f1e" stroke-width="2"/><path d="M14 21 L19 27 L29 15" fill="none" stroke="#f0a94a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        '<svg viewBox="0 0 42 42" width="42" height="42"><circle cx="21" cy="21" r="15" fill="none" stroke="#e35f1e" stroke-width="2"/><path d="M21 12v9l7 4" stroke="#f0a94a" stroke-width="2" stroke-linecap="round" fill="none"/></svg>',
    ];
    return $icons[$index % count($icons)];
}
