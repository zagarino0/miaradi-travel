<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/src/http.php';

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
api_require_role('super_admin');

if ($method === 'GET') {
    try {
        $rows = database_connection()->query(
            'SELECT id, option_type, option_value, option_label, sort_order, is_active
             FROM miaradi_filter_options ORDER BY option_type ASC, sort_order ASC, option_label ASC'
        )->fetchAll();
        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['sort_order'] = (int) $row['sort_order'];
            $row['is_active'] = (bool) $row['is_active'];
        }
        unset($row);
        api_json(['data' => $rows]);
    } catch (Throwable $exception) {
        error_log('[miaradi-api] Filter options read failed: ' . $exception->getMessage());
        api_json(['error' => ['code' => 'filter_options_unavailable', 'message' => 'Impossible de charger les options.']], 503);
    }
}

if (!in_array($method, ['POST', 'DELETE'], true)) {
    header('Allow: GET, POST, DELETE');
    api_json(['error' => ['code' => 'method_not_allowed', 'message' => 'HTTP method not allowed.']], 405);
}
api_require_csrf();

try {
    $pdo = database_connection();
    if ($method === 'DELETE') {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) api_json(['error' => ['code' => 'invalid_id', 'message' => 'Option invalide.']], 422);
        $lookup = $pdo->prepare('SELECT option_type, option_value, option_label FROM miaradi_filter_options WHERE id = :id');
        $lookup->execute(['id' => $id]);
        $option = $lookup->fetch(PDO::FETCH_ASSOC);
        if (!$option) api_json(['error' => ['code' => 'not_found', 'message' => 'Option introuvable.']], 404);

        $inUse = false;
        if ($option['option_type'] === 'region' || $option['option_type'] === 'theme') {
            $column = $option['option_type'] === 'region' ? 'region' : 'theme';
            $usage = $pdo->prepare("SELECT COUNT(*) FROM miaradi_circuits WHERE {$column} = :label OR {$column} = :value");
            $usage->execute(['label' => $option['option_label'], 'value' => $option['option_value']]);
            $inUse = (int) $usage->fetchColumn() > 0;
        } elseif ($option['option_type'] === 'duration') {
            if ($option['option_value'] === 'custom') {
                $usage = $pdo->query('SELECT COUNT(*) FROM miaradi_circuits WHERE duration_min IS NULL AND duration_max IS NULL');
                $inUse = (int) $usage->fetchColumn() > 0;
            } elseif (preg_match('/^(\\d{1,3})(?:-(\\d{1,3}))?$/', (string) $option['option_value'], $matches)) {
                $min = (int) $matches[1];
                $max = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : $min;
                $usage = $pdo->prepare('SELECT COUNT(*) FROM miaradi_circuits WHERE duration_min = :min AND duration_max = :max');
                $usage->execute(['min' => $min, 'max' => $max]);
                $inUse = (int) $usage->fetchColumn() > 0;
            }
        }
        if ($inUse) {
            api_json([
                'error' => [
                    'code' => 'option_in_use',
                    'message' => 'Cette option est utilisée par au moins un circuit. Modifiez ou réaffectez ces circuits avant de la supprimer.',
                ],
            ], 409);
        }

        $stmt = $pdo->prepare('DELETE FROM miaradi_filter_options WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if (!$stmt->rowCount()) api_json(['error' => ['code' => 'not_found', 'message' => 'Option introuvable.']], 404);
        api_json(['data' => ['deleted' => true]]);
    }

    $input = api_read_json();
    $type = (string) ($input['option_type'] ?? '');
    if (!in_array($type, ['region', 'theme', 'duration'], true)) {
        api_json(['error' => ['code' => 'invalid_type', 'message' => 'Choisissez une région, une thématique ou une durée.']], 422);
    }
    $label = api_clean_text($input['option_label'] ?? null, 120, true);
    $value = api_clean_text($input['option_value'] ?? null, 100, true);
    $order = $input['sort_order'] ?? 0;
    if (!$label || !$value || !is_int($order) || $order < -100000 || $order > 100000) {
        api_json(['error' => ['code' => 'validation_error', 'message' => 'Renseignez un libellé, une valeur et un ordre valides.']], 422);
    }
    if ($type !== 'duration' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value)) {
        api_json(['error' => ['code' => 'invalid_value', 'message' => 'La valeur technique doit être un identifiant minuscule avec des tirets, par exemple nord-ouest.']], 422);
    }
    if ($type === 'duration' && $value !== 'custom' && !preg_match('/^\d{1,3}(?:-\d{1,3})?$/', (string) $value)) {
        api_json(['error' => ['code' => 'invalid_duration', 'message' => 'La durée doit être un nombre de jours ou une plage, par exemple 3-5.']], 422);
    }
    if ($type === 'duration' && $value === 'custom') {
        // Option réservée aux circuits personnalisables sans durée fixe.
    } elseif ($type === 'duration' && str_contains((string) $value, '-')) {
        [$min, $max] = array_map('intval', explode('-', (string) $value, 2));
        if ($min < 1 || $max < $min || $max > 365) api_json(['error' => ['code' => 'invalid_duration', 'message' => 'La plage doit être comprise entre 1 et 365 jours.']], 422);
    } elseif ($type === 'duration' && $value !== 'custom' && ((int) $value < 1 || (int) $value > 365)) {
        api_json(['error' => ['code' => 'invalid_duration', 'message' => 'La durée doit être comprise entre 1 et 365 jours.']], 422);
    }
    $stmt = $pdo->prepare('INSERT INTO miaradi_filter_options (option_type, option_value, option_label, sort_order) VALUES (:type, :value, :label, :sort)');
    $stmt->execute(['type' => $type, 'value' => $value, 'label' => $label, 'sort' => $order]);
    api_json(['data' => ['id' => (int) $pdo->lastInsertId()]], 201);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') api_json(['error' => ['code' => 'duplicate_option', 'message' => 'Cette option existe déjà pour cette catégorie.']], 409);
    error_log('[miaradi-api] Filter option mutation failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'filter_option_save_failed', 'message' => 'Impossible d’enregistrer cette option.']], 503);
} catch (Throwable $exception) {
    error_log('[miaradi-api] Filter option mutation failed: ' . $exception->getMessage());
    api_json(['error' => ['code' => 'filter_option_save_failed', 'message' => 'Impossible d’enregistrer cette option.']], 503);
}
