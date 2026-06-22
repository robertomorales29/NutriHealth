<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['entrenador']);

$returnTo = $_POST['return_to'] ?? 'pages/entrenador/rutinas.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/entrenador/rutinas.php');
}

$user = current_user();
$rutinaId = (int) ($_POST['rutina_id'] ?? 0);
$pacienteId = (int) ($_POST['paciente_id'] ?? 0);
$fechaInicio = $_POST['fecha_inicio'] ?: date('Y-m-d');
$fechaFin = $_POST['fecha_fin'] ?: null;

$stmt = db()->prepare('SELECT id FROM rutinas WHERE id = ? AND entrenador_id = ?');
$stmt->execute([$rutinaId, $user['id']]);
if (!$stmt->fetch()) {
    flash('error', 'Rutina no válida.');
    redirect($returnTo);
}

$patient = get_person($pacienteId);
if (!$patient || $patient['rol'] !== 'paciente') {
    flash('error', 'Paciente no válido.');
    redirect($returnTo);
}

$cancel = db()->prepare("UPDATE rutinas_pacientes SET estado = 'finalizada' WHERE paciente_id = ? AND estado = 'activa'");
$cancel->execute([$pacienteId]);

$insert = db()->prepare('INSERT INTO rutinas_pacientes (paciente_id, rutina_id, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)');
$insert->execute([$pacienteId, $rutinaId, $fechaInicio, $fechaFin, 'activa']);
ensure_assignment($pacienteId, (int) $user['id'], 'entrenador');
flash('success', 'Rutina asignada correctamente.');
redirect($returnTo);
