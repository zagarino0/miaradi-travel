<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['error' => ['code' => 'method_not_allowed', 'message' => 'Only GET requests are supported.']]);
    exit;
}

function filter_normalize_text(mixed $value): string
{
    $text = trim((string) ($value ?? ''));
    if (function_exists('iconv')) {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($ascii !== false) $text = $ascii;
    }
    return strtolower($text);
}

function filter_region_value(mixed $value): string
{
    $region = filter_normalize_text($value);
    if (preg_match('/ouest|west|menabe|boeny|melaky|betsiboka/', $region)) return 'west';
    if (preg_match('/nord|north|nosy|sava|diana|analanjirofo|sainte.?marie/', $region)) return 'north';
    if (preg_match('/est|east|alaotra|mangoro|atsinanana/', $region)) return 'east';
    if (preg_match('/sud|south|androy|anosy|ihorombe|atsimo/', $region)) return 'south';
    if (preg_match('/haute|highland|analamanga|vakinankaratra|amoron|betsileo/', $region)) return 'highlands';
    return trim(preg_replace('/[^a-z0-9]+/', '-', $region), '-') ?: 'all';
}

function filter_theme_value(mixed $value): string
{
    $theme = filter_normalize_text($value);
    if (preg_match('/coast|island|lagon|cote|ile/', $theme)) return 'coast';
    if (preg_match('/culture|rencontre|heritage/', $theme)) return 'culture';
    return trim(preg_replace('/[^a-z0-9]+/', '-', $theme), '-') ?: 'nature';
}

function filter_duration_value(?int $min, ?int $max): string
{
    return ($min !== null && $min > 0 && $max !== null && $max > 0)
        ? $min . '-' . $max
        : 'custom';
}

try {
    $pdo = database_connection();
    $options = ['region' => [], 'theme' => [], 'duration' => []];

    // Les options administrées restent prioritaires pour conserver leurs libellés.
    $configured = $pdo->query(
        'SELECT option_type, option_value, option_label
         FROM miaradi_filter_options
         WHERE is_active = 1
         ORDER BY option_type ASC, sort_order ASC, option_label ASC'
    )->fetchAll(PDO::FETCH_ASSOC);

    $addOption = static function (string $type, string $value, string $label) use (&$options): void {
        if (!isset($options[$type]) || $value === '' || $label === '') return;
        foreach ($options[$type] as $existing) {
            if ($existing['value'] === $value) return;
        }
        $options[$type][] = ['value' => $value, 'label' => $label];
    };

    foreach ($configured as $row) {
        $type = (string) $row['option_type'];
        $rawValue = (string) $row['option_value'];
        $label = trim((string) $row['option_label']);
        if ($type === 'region') $value = filter_region_value($label !== '' ? $label : $rawValue);
        elseif ($type === 'theme') $value = filter_theme_value($label !== '' ? $label : $rawValue);
        elseif ($type === 'duration') $value = $rawValue;
        else continue;
        $addOption($type, $value, $label);
    }

    // Complète automatiquement les filtres à partir des circuits réellement publiés.
    $circuits = $pdo->query(
        'SELECT region, theme, duration_min, duration_max
         FROM miaradi_circuits
         WHERE is_published = 1
         ORDER BY sort_order ASC, id ASC'
    )->fetchAll(PDO::FETCH_ASSOC);

    foreach ($circuits as $circuit) {
        $regionLabel = trim((string) ($circuit['region'] ?? ''));
        if ($regionLabel !== '') {
            $addOption('region', filter_region_value($regionLabel), $regionLabel);
        }

        $themeLabel = trim((string) ($circuit['theme'] ?? ''));
        if ($themeLabel !== '') {
            $addOption('theme', filter_theme_value($themeLabel), $themeLabel);
        }

        $min = $circuit['duration_min'] === null ? null : (int) $circuit['duration_min'];
        $max = $circuit['duration_max'] === null ? null : (int) $circuit['duration_max'];
        $durationValue = filter_duration_value($min, $max);
        $durationLabel = $durationValue === 'custom'
            ? 'Sur mesure'
            : ($min === $max ? $min . ' jours' : $min . '–' . $max . ' jours');
        $addOption('duration', $durationValue, $durationLabel);
    }

    echo json_encode(['data' => $options], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Public filter options failed: ' . $exception->getMessage());
    http_response_code(503);
    echo json_encode(['error' => ['code' => 'filter_options_unavailable', 'message' => 'Filter options unavailable.']]);
}
