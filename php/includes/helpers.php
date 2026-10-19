<?php
function esc($s): string {
    return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
}

// Both languages are always emitted; CSS (data-i18n-group) hides one based
// on the <html data-lang> attribute set by assets/js/i18n.js.
function bi(?string $en, ?string $ru, string $tag = 'span', string $cls = ''): string {
    $klass = $cls ? " class=\"$cls\"" : '';
    return "<$tag$klass data-i18n-group=\"en\">" . esc($en) . "</$tag>"
         . "<$tag$klass data-i18n-group=\"ru\">" . esc($ru) . "</$tag>";
}

// Like bi(), but the caller supplies raw (already-safe) HTML per language
// instead of plain text that needs escaping — used for paragraphs/lists.
function bi_block(string $enHtml, string $ruHtml, string $tag = 'div', string $cls = ''): string {
    $klass = $cls ? " class=\"$cls\"" : '';
    return "<$tag$klass data-i18n-group=\"en\">$enHtml</$tag>"
         . "<$tag$klass data-i18n-group=\"ru\">$ruHtml</$tag>";
}

function ul_list(array $items, string $cls = 'spec-list'): string {
    $en = implode('', array_map(fn($i) => '<li>' . esc($i['en'] ?? '') . '</li>', $items));
    $ru = implode('', array_map(fn($i) => '<li>' . esc($i['ru'] ?? '') . '</li>', $items));
    return bi_block("<ul class=\"$cls\">$en</ul>", "<ul class=\"$cls\">$ru</ul>", 'div');
}

// Appends $suffix to the final segment of a (possibly bracketed) form
// field name, e.g. with_suffix("blocks[0][title]", "_en") ->
// "blocks[0][title_en]", with_suffix("title", "_en") -> "title_en".
// Appending after a closing "]" instead (e.g. "blocks[0][title]_en")
// looks equivalent but silently breaks: PHP's parse_str/$_POST parsing
// stops at the last "]" and drops everything after it, so two fields
// built that way collapse onto the same key instead of staying distinct.
function with_suffix(string $path, string $suffix): string {
    if (preg_match('/^(.*\[)([^\[\]]*)(\])$/', $path, $m)) {
        return $m[1] . $m[2] . $suffix . $m[3];
    }
    return $path . $suffix;
}

// Derives a flat, bracket-free key for a <input type="file"> from its
// logical (bracketed) field path, e.g. "blocks[0][items][2][image]" ->
// "upload__blocks_0_items_2_image". File inputs are kept flat rather
// than bracketed so PHP's nested-$_FILES hoisting (name/type/tmp_name
// split out *before* the array nesting) never has to be unpicked — the
// same deterministic key is derived again when parsing the submission,
// so it still round-trips to the right block/item.
function file_key_for(string $path): string {
    return 'upload__' . preg_replace('/[^a-zA-Z0-9]+/', '_', trim($path, '[]'));
}

function slugify(string $s): string {
    $s = mb_strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9\/]+/', '-', $s);
    $s = preg_replace('#/+#', '/', $s);
    return trim($s, '-/');
}

function redirect(string $url): never {
    header("Location: $url");
    exit;
}

function json_in($value, $default = []) {
    if ($value === null || $value === '') return $default;
    $decoded = json_decode($value, true);
    return $decoded === null ? $default : $decoded;
}
