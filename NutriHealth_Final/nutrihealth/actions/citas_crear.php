<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$fecha = $_POST['fecha'] ?? '';
$hora = normalize_time($_POST['hora'] ?? '');
$motivo = trim($_POST['motivo'] ?? '');
$returnTo = $_POST['return_to'] ?? 'dashboard.php';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !is_valid_slot($hora)) {
    flash('error', 'Selecciona una fecha y horario válido.');
    redirect($returnTo);
}

if ($fecha < date('Y-m-d')) {
    flash('error', 'No se pueden crear citas en fechas anteriores.');
    redirect($returnTo);
}

if ($user['rol'] === 'paciente') {
    $pacienteId = (int) $user['id'];
    $especialistaId = (int) ($_POST['especialista_id'] ?? 0);
    $tipo = $_POST['tipo_especialista'] ?? '';
} else {
    $pacienteId = (int) ($_POST['paciente_id'] ?? 0);
    $especialistaId = (int) $user['id'];
    $tipo = $user['rol'];
}

$patient = get_person($pacienteId);
$specialist = get_person($especialistaId);

if (!$patient || $patient['rol'] !== 'paciente') {
    flash('error', 'Paciente no válido.');
    redirect($returnTo);
}

if (!$specialist || !in_array($tipo, ['nutriologo', 'entrenador'], true) || $specialist['rol'] !== $tipo) {
    flash('error', 'Especialista no válido.');
    redirect($returnTo);
}

if (appointment_conflict($pacienteId, $especialistaId, $fecha, $hora)) {
    flash('error', 'Ese horario ya está ocupado para el paciente o el especialista. Cada cita dura una hora.');
    redirect($returnTo);
}

try {
    db()->beginTransaction();
    $stmt = db()->prepare('INSERT INTO citas (paciente_id, especialista_id, tipo_especialista, fecha, hora, motivo, estado)
                           VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$pacienteId, $especialistaId, $tipo, $fecha, $hora, $motivo ?: null, 'confirmada']);
    ensure_assignment($pacienteId, $especialistaId, $tipo);
    db()->commit();
    flash('success', 'Cita agendada correctamente.');
} catch (Throwable $e) {
    if (db()->inTransaction()) db()->rollBack();
    flash('error', 'No se pudo agendar la cita: ' . $e->getMessage());
}

redirect($returnTo);
