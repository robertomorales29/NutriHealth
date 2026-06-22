<?php
require_once __DIR__ . '/../config/database.php';

function normalize_path_for_url(string $path): string
{
    $path = str_replace('\\', '/', $path);
    $parts = [];

    foreach (explode('/', $path) as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        if ($part === '..') {
            array_pop($parts);
            continue;
        }
        $parts[] = $part;
    }

    return implode('/', $parts);
}

function relative_url_from_current_script(string $targetPath): string
{
    $root = normalize_path_for_url(APP_ROOT);
    $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? APP_ROOT . DIRECTORY_SEPARATOR . 'index.php';
    $scriptDir = normalize_path_for_url(dirname($scriptFile));
    $target = normalize_path_for_url(APP_ROOT . ($targetPath !== '' ? DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $targetPath) : ''));

    $fromParts = $scriptDir === '' ? [] : explode('/', $scriptDir);
    $toParts = $target === '' ? [] : explode('/', $target);

    $i = 0;
    $max = min(count($fromParts), count($toParts));
    while ($i < $max && $fromParts[$i] === $toParts[$i]) {
        $i++;
    }

    $up = array_fill(0, count($fromParts) - $i, '..');
    $down = array_slice($toParts, $i);
    $relativeParts = array_merge($up, $down);

    return $relativeParts ? implode('/', $relativeParts) : '.';
}

function base_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    preg_match('/^([^?#]*)([?#].*)?$/', $path, $matches);
    $cleanPath = $matches[1] ?? '';
    $suffix = $matches[2] ?? '';

    $relative = relative_url_from_current_script($cleanPath);

    if ($cleanPath === '') {
        return $relative === '.' ? './' : rtrim($relative, '/') . '/';
    }

    return $relative . $suffix;
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (isset($_SESSION['flash'][$key])) {
        $stored = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $stored;
    }

    return null;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Primero inicia sesión para continuar.');
        redirect('login.php');
    }
}

function require_role(array $roles): void
{
    require_login();
    $user = current_user();
    if (!in_array($user['rol'], $roles, true)) {
        flash('error', 'No tienes permisos para acceder a esa sección.');
        redirect('dashboard.php');
    }
}

function full_name(array $person): string
{
    return trim(($person['nombre'] ?? '') . ' ' . ($person['apellido_paterno'] ?? '') . ' ' . ($person['apellido_materno'] ?? ''));
}

function role_label(string $rol): string
{
    return match ($rol) {
        'paciente' => 'Paciente',
        'nutriologo' => 'Nutriólogo',
        'entrenador' => 'Entrenador',
        default => ucfirst($rol),
    };
}

function get_person(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM personas WHERE id = ?');
    $stmt->execute([$id]);
    $person = $stmt->fetch();
    return $person ?: null;
}

function get_people_by_role(string $role): array
{
    $stmt = db()->prepare('SELECT * FROM personas WHERE rol = ? AND activo = 1 ORDER BY nombre, apellido_paterno');
    $stmt->execute([$role]);
    return $stmt->fetchAll();
}

function latest_health_data(int $pacienteId): ?array
{
    $stmt = db()->prepare('SELECT * FROM datos_salud WHERE paciente_id = ? ORDER BY fecha_registro DESC, id DESC LIMIT 1');
    $stmt->execute([$pacienteId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function latest_health_history(int $pacienteId): ?array
{
    $stmt = db()->prepare('SELECT * FROM historial_salud WHERE paciente_id = ? ORDER BY fecha_registro DESC, id DESC LIMIT 1');
    $stmt->execute([$pacienteId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function calculate_imc(?float $peso, ?float $estatura): ?float
{
    if (!$peso || !$estatura || $estatura <= 0) {
        return null;
    }
    return round($peso / ($estatura * $estatura), 1);
}

function imc_status(?float $imc): string
{
    if ($imc === null) return 'Sin datos';
    if ($imc < 18.5) return 'Bajo peso';
    if ($imc < 25) return 'Saludable';
    if ($imc < 30) return 'Sobrepeso';
    return 'Obesidad';
}

function estimate_daily_calories(?array $person, ?array $health): ?int
{
    if (!$person || !$health || empty($health['peso']) || empty($health['estatura']) || empty($health['edad'])) {
        return null;
    }

    $weight = (float) $health['peso'];
    $heightCm = (float) $health['estatura'] * 100;
    $age = (int) $health['edad'];
    $sexo = $person['sexo'] ?? 'Otro';
    $activity = $health['nivel_actividad'] ?? 'Moderado';

    $bmr = 10 * $weight + 6.25 * $heightCm - 5 * $age;
    $bmr += $sexo === 'Femenino' ? -161 : 5;

    $factor = match ($activity) {
        'Bajo' => 1.2,
        'Alto' => 1.725,
        default => 1.55,
    };

    return (int) round($bmr * $factor);
}

function latest_current_health_data(int $pacienteId): ?array
{
    $stmt = db()->prepare('SELECT * FROM datos_salud_actuales WHERE paciente_id = ? LIMIT 1');
    $stmt->execute([$pacienteId]);
    $row = $stmt->fetch();

    if ($row) {
        return $row;
    }

    sync_current_health_from_latest_history($pacienteId);
    $stmt->execute([$pacienteId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function sync_current_health_from_latest_history(int $pacienteId): void
{
    $initial = latest_health_data($pacienteId);
    if (!$initial) {
        return;
    }

    $latest = latest_health_history($pacienteId);
    $stmt = db()->prepare('SELECT objetivo_salud, nivel_actividad FROM datos_salud_actuales WHERE paciente_id = ? LIMIT 1');
    $stmt->execute([$pacienteId]);
    $existing = $stmt->fetch() ?: [];

    $objetivo = array_key_exists('objetivo_salud', $existing)
        ? $existing['objetivo_salud']
        : ($initial['objetivo_salud'] ?? null);
    $actividad = $existing['nivel_actividad'] ?? $initial['nivel_actividad'] ?? 'Moderado';

    $upsert = db()->prepare('INSERT INTO datos_salud_actuales
        (paciente_id, peso, porcentaje_grasa, masa_muscular, cintura, cadera, objetivo_salud, nivel_actividad)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            peso = VALUES(peso),
            porcentaje_grasa = VALUES(porcentaje_grasa),
            masa_muscular = VALUES(masa_muscular),
            cintura = VALUES(cintura),
            cadera = VALUES(cadera),
            objetivo_salud = VALUES(objetivo_salud),
            nivel_actividad = VALUES(nivel_actividad)');
    $upsert->execute([
        $pacienteId,
        $latest['peso'] ?? $initial['peso'] ?? null,
        $latest['porcentaje_grasa'] ?? null,
        $latest['masa_muscular'] ?? null,
        $latest['cintura'] ?? null,
        $latest['cadera'] ?? null,
        $objetivo,
        $actividad,
    ]);
}

function specialist_has_patient(int $especialistaId, string $rol, int $pacienteId): bool
{
    if ($rol === 'nutriologo') {
        $stmt = db()->prepare('SELECT COUNT(*) FROM pacientes_nutriologos WHERE paciente_id = ? AND nutriologo_id = ? AND activo = 1');
    } elseif ($rol === 'entrenador') {
        $stmt = db()->prepare('SELECT COUNT(*) FROM pacientes_entrenadores WHERE paciente_id = ? AND entrenador_id = ? AND activo = 1');
    } else {
        return false;
    }

    $stmt->execute([$pacienteId, $especialistaId]);
    return (int) $stmt->fetchColumn() > 0;
}

function can_manage_health_history(array $user, int $pacienteId): bool
{
    if ($user['rol'] === 'paciente') {
        return (int) $user['id'] === $pacienteId;
    }

    if (in_array($user['rol'], ['nutriologo', 'entrenador'], true)) {
        return specialist_has_patient((int) $user['id'], $user['rol'], $pacienteId);
    }

    return false;
}

function clean_decimal_or_null(mixed $value): ?float
{
    if ($value === null || $value === '') {
        return null;
    }

    if (!is_numeric($value)) {
        return null;
    }

    return max(0, round((float) $value, 2));
}

function safe_external_url(?string $url): ?string
{
    $url = trim((string) $url);
    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) ? $url : null;
}

function appointment_slots(): array
{
    return ['08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00'];
}

function normalize_time(string $hora): string
{
    if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
        return $hora . ':00';
    }
    return $hora;
}

function is_valid_slot(string $hora): bool
{
    return in_array(normalize_time($hora), appointment_slots(), true);
}

function appointment_conflict(int $pacienteId, int $especialistaId, string $fecha, string $hora, ?int $excludeId = null): bool
{
    $hora = normalize_time($hora);
    $sql = "SELECT COUNT(*) FROM citas
            WHERE fecha = :fecha
              AND hora = :hora
              AND estado NOT IN ('cancelada', 'finalizada')
              AND (paciente_id = :paciente_id OR especialista_id = :especialista_id)";
    $params = [
        ':fecha' => $fecha,
        ':hora' => $hora,
        ':paciente_id' => $pacienteId,
        ':especialista_id' => $especialistaId,
    ];

    if ($excludeId !== null) {
        $sql .= ' AND id <> :exclude_id';
        $params[':exclude_id'] = $excludeId;
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}

function available_slots(int $especialistaId, string $fecha, ?int $pacienteId = null): array
{
    $available = [];
    foreach (appointment_slots() as $slot) {
        $sql = "SELECT COUNT(*) FROM citas
                WHERE fecha = ?
                  AND hora = ?
                  AND estado NOT IN ('cancelada', 'finalizada')
                  AND especialista_id = ?";
        $params = [$fecha, $slot, $especialistaId];

        if ($pacienteId !== null) {
            $sql = "SELECT COUNT(*) FROM citas
                    WHERE fecha = ?
                      AND hora = ?
                      AND estado NOT IN ('cancelada', 'finalizada')
                      AND (especialista_id = ? OR paciente_id = ?)";
            $params = [$fecha, $slot, $especialistaId, $pacienteId];
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ((int) $stmt->fetchColumn() === 0) {
            $available[] = $slot;
        }
    }
    return $available;
}

function ensure_assignment(int $pacienteId, int $especialistaId, string $tipo): void
{
    if ($tipo === 'nutriologo') {
        $table = 'pacientes_nutriologos';
        $field = 'nutriologo_id';
    } else {
        $table = 'pacientes_entrenadores';
        $field = 'entrenador_id';
    }

    $stmt = db()->prepare("SELECT id FROM {$table} WHERE paciente_id = ? AND {$field} = ? LIMIT 1");
    $stmt->execute([$pacienteId, $especialistaId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $update = db()->prepare("UPDATE {$table} SET activo = 1 WHERE id = ?");
        $update->execute([$existing['id']]);
        return;
    }

    $insert = db()->prepare("INSERT INTO {$table} (paciente_id, {$field}, activo) VALUES (?, ?, 1)");
    $insert->execute([$pacienteId, $especialistaId]);
}

function assigned_patients(int $especialistaId, string $tipo): array
{
    if ($tipo === 'nutriologo') {
        $sql = "SELECT p.* FROM personas p
                INNER JOIN pacientes_nutriologos pn ON pn.paciente_id = p.id
                WHERE pn.nutriologo_id = ? AND pn.activo = 1 AND p.activo = 1
                ORDER BY p.nombre";
    } else {
        $sql = "SELECT p.* FROM personas p
                INNER JOIN pacientes_entrenadores pe ON pe.paciente_id = p.id
                WHERE pe.entrenador_id = ? AND pe.activo = 1 AND p.activo = 1
                ORDER BY p.nombre";
    }
    $stmt = db()->prepare($sql);
    $stmt->execute([$especialistaId]);
    return $stmt->fetchAll();
}

function upcoming_appointments_for_user(int $userId, string $role, int $limit = 6): array
{
    if ($role === 'paciente') {
        $condition = 'c.paciente_id = ?';
    } else {
        $condition = 'c.especialista_id = ?';
    }

    $sql = "SELECT c.*, pac.nombre AS paciente_nombre, pac.apellido_paterno AS paciente_apellido,
                   esp.nombre AS especialista_nombre, esp.apellido_paterno AS especialista_apellido, esp.rol AS especialista_rol
            FROM citas c
            INNER JOIN personas pac ON pac.id = c.paciente_id
            INNER JOIN personas esp ON esp.id = c.especialista_id
            WHERE {$condition}
              AND c.estado NOT IN ('cancelada', 'finalizada')
              AND CONCAT(c.fecha, ' ', c.hora) >= NOW()
            ORDER BY c.fecha ASC, c.hora ASC
            LIMIT {$limit}";
    $stmt = db()->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function appointments_for_user(int $userId, string $role): array
{
    $condition = $role === 'paciente' ? 'c.paciente_id = ?' : 'c.especialista_id = ?';
    $sql = "SELECT c.*, pac.nombre AS paciente_nombre, pac.apellido_paterno AS paciente_apellido,
                   esp.nombre AS especialista_nombre, esp.apellido_paterno AS especialista_apellido, esp.rol AS especialista_rol
            FROM citas c
            INNER JOIN personas pac ON pac.id = c.paciente_id
            INNER JOIN personas esp ON esp.id = c.especialista_id
            WHERE {$condition}
            ORDER BY c.fecha DESC, c.hora DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}


function diets_for_patient(int $pacienteId): array
{
    $sql = "SELECT dp.*, d.nombre AS dieta_nombre, d.descripcion AS dieta_descripcion,
                   d.indicaciones AS dieta_indicaciones, d.nutriologo_id,
                   n.nombre AS nutriologo_nombre, n.apellido_paterno AS nutriologo_apellido
            FROM dietas_pacientes dp
            INNER JOIN dietas d ON d.id = dp.dieta_id
            LEFT JOIN personas n ON n.id = d.nutriologo_id
            WHERE dp.paciente_id = ?
            ORDER BY FIELD(dp.estado, 'activa','finalizada','cancelada'), dp.fecha_inicio DESC, dp.id DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute([$pacienteId]);
    return $stmt->fetchAll();
}

function routines_for_patient(int $pacienteId): array
{
    $sql = "SELECT rp.*, r.nombre AS rutina_nombre, r.descripcion AS rutina_descripcion, r.entrenador_id,
                   e.nombre AS entrenador_nombre, e.apellido_paterno AS entrenador_apellido
            FROM rutinas_pacientes rp
            INNER JOIN rutinas r ON r.id = rp.rutina_id
            LEFT JOIN personas e ON e.id = r.entrenador_id
            WHERE rp.paciente_id = ?
            ORDER BY FIELD(rp.estado, 'activa','finalizada','cancelada'), rp.fecha_inicio DESC, rp.id DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute([$pacienteId]);
    return $stmt->fetchAll();
}

function routine_exercises(int $rutinaId): array
{
    $stmt = db()->prepare('SELECT * FROM rutina_ejercicios WHERE rutina_id = ? ORDER BY FIELD(dia_semana, "lunes","martes","miercoles","jueves","viernes","sabado","domingo"), id');
    $stmt->execute([$rutinaId]);
    return $stmt->fetchAll();
}

function all_statuses(): array
{
    return ['activa', 'finalizada', 'cancelada'];
}

function appointment_statuses(): array
{
    return ['pendiente', 'confirmada', 'cancelada', 'finalizada'];
}

function week_days(): array
{
    return ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
}

function clean_int_or_null(mixed $value): ?int
{
    if ($value === null || $value === '') {
        return null;
    }
    return max(0, (int) $value);
}

function badge_state(string $estado): string
{
    return match ($estado) {
        'confirmada' => 'success',
        'cancelada' => 'danger',
        'finalizada' => 'secondary',
        default => 'warning',
    };
}
