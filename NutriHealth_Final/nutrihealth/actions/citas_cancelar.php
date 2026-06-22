<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$id = (int) ($_POST['id'] ?? 0);
$returnTo = $_POST['return_to'] ?? 'dashboard.php';

$stmt = db()->prepare("SELECT * FROM citas WHERE id = ? AND estado NOT IN ('cancelada', 'finalizada') LIMIT 1");
$stmt->execute([$id]);
$cita = $stmt->fetch();

if (!$cita || ((int)$cita['paciente_id'] !== (int)$user['id'] && (int)$cita['especialista_id'] !== (int)$user['id'])) {
    flash('error', 'No puedes cancelar esta cita.');
    redirect($returnTo);
}

$update = db()->prepare("UPDATE citas SET estado = 'cancelada', observaciones = CONCAT(COALESCE(observaciones, ''), '\nCancelada por usuario el ', NOW()) WHERE id = ?");
$update->execute([$id]);
flash('success', 'Cita cancelada correctamente.');
redirect($returnTo);
