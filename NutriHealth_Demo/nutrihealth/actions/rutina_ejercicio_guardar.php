<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['entrenador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/entrenador/rutinas.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'pages/entrenador/rutinas.php';
$id = (int) ($_POST['id'] ?? 0);
$rutinaId = (int) ($_POST['rutina_id'] ?? 0);
$dia = $_POST['dia_semana'] ?? 'lunes';
$nombre = trim($_POST['nombre_ejercicio'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$link = trim($_POST['link_video'] ?? '');

if (!in_array($dia, week_days(), true)) {
    $dia = 'lunes';
}

if ($nombre === '') {
    flash('error', 'El ejercicio necesita nombre.');
    redirect($returnTo);
}

$stmt = db()->prepare('SELECT id FROM rutinas WHERE id = ? AND entrenador_id = ?');
$stmt->execute([$rutinaId, $user['id']]);
if (!$stmt->fetch()) {
    flash('error', 'Rutina no válida.');
    redirect($returnTo);
}

$params = [
    $dia,
    $nombre,
    $descripcion ?: null,
    clean_int_or_null($_POST['series'] ?? null),
    clean_int_or_null($_POST['repeticiones'] ?? null),
    clean_int_or_null($_POST['duracion_minutos'] ?? null),
    clean_int_or_null($_POST['descanso_segundos'] ?? null),
    $link ?: null,
];

if ($id > 0) {
    $exists = db()->prepare('SELECT re.id FROM rutina_ejercicios re INNER JOIN rutinas r ON r.id = re.rutina_id WHERE re.id = ? AND re.rutina_id = ? AND r.entrenador_id = ?');
    $exists->execute([$id, $rutinaId, $user['id']]);
    if (!$exists->fetch()) {
        flash('error', 'Ejercicio no válido para modificar.');
        redirect($returnTo);
    }

    $update = db()->prepare('UPDATE rutina_ejercicios
        SET dia_semana = ?, nombre_ejercicio = ?, descripcion = ?, series = ?, repeticiones = ?, duracion_minutos = ?, descanso_segundos = ?, link_video = ?
        WHERE id = ?');
    $update->execute([...$params, $id]);
    flash('success', 'Ejercicio actualizado correctamente.');
} else {
    $insert = db()->prepare('INSERT INTO rutina_ejercicios
        (rutina_id, dia_semana, nombre_ejercicio, descripcion, series, repeticiones, duracion_minutos, descanso_segundos, link_video)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $insert->execute([$rutinaId, ...$params]);
    flash('success', 'Ejercicio agregado a la rutina.');
}

redirect($returnTo);
