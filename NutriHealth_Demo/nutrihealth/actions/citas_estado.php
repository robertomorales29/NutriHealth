<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['nutriologo', 'entrenador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$id = (int) ($_POST['id'] ?? 0);
$estado = $_POST['estado'] ?? '';
$returnTo = $_POST['return_to'] ?? 'dashboard.php';

if (!in_array($estado, appointment_statuses(), true)) {
    flash('error', 'Estado de cita no válido.');
    redirect($returnTo);
}

$stmt = db()->prepare('SELECT * FROM citas WHERE id = ? AND especialista_id = ? AND tipo_especialista = ?');
$stmt->execute([$id, $user['id'], $user['rol']]);
$cita = $stmt->fetch();
if (!$cita) {
    flash('error', 'No puedes modificar esa cita.');
    redirect($returnTo);
}

if (in_array($estado, ['pendiente', 'confirmada'], true) && appointment_conflict((int)$cita['paciente_id'], (int)$cita['especialista_id'], $cita['fecha'], $cita['hora'], $id)) {
    flash('error', 'No se puede activar esa cita porque el horario ya está ocupado.');
    redirect($returnTo);
}

$update = db()->prepare('UPDATE citas SET estado = ? WHERE id = ?');
$update->execute([$estado, $id]);
flash('success', 'Estado de la cita actualizado.');
redirect($returnTo);
