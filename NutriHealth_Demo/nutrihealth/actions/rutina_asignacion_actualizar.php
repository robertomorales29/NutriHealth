<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['entrenador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/entrenador/pacientes.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'pages/entrenador/pacientes.php';
$id = (int) ($_POST['id'] ?? 0);
$rutinaId = (int) ($_POST['rutina_id'] ?? 0);
$estado = $_POST['estado'] ?? 'activa';
$fechaInicio = $_POST['fecha_inicio'] ?: null;
$fechaFin = $_POST['fecha_fin'] ?: null;

if (!in_array($estado, all_statuses(), true)) {
    flash('error', 'Estado de rutina no válido.');
    redirect($returnTo);
}

$assignment = db()->prepare('SELECT rp.* FROM rutinas_pacientes rp INNER JOIN rutinas r ON r.id = rp.rutina_id WHERE rp.id = ? AND r.entrenador_id = ?');
$assignment->execute([$id, $user['id']]);
$current = $assignment->fetch();
if (!$current) {
    flash('error', 'Asignación de rutina no válida.');
    redirect($returnTo);
}

$validRoutine = db()->prepare('SELECT id FROM rutinas WHERE id = ? AND entrenador_id = ?');
$validRoutine->execute([$rutinaId, $user['id']]);
if (!$validRoutine->fetch()) {
    flash('error', 'La rutina seleccionada no es válida.');
    redirect($returnTo);
}

try {
    db()->beginTransaction();
    if ($estado === 'activa') {
        $finish = db()->prepare("UPDATE rutinas_pacientes SET estado = 'finalizada' WHERE paciente_id = ? AND estado = 'activa' AND id <> ?");
        $finish->execute([(int)$current['paciente_id'], $id]);
    }
    $update = db()->prepare('UPDATE rutinas_pacientes SET rutina_id = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE id = ?');
    $update->execute([$rutinaId, $fechaInicio, $fechaFin, $estado, $id]);
    db()->commit();
    flash('success', 'Rutina asignada actualizada correctamente.');
} catch (Throwable $e) {
    if (db()->inTransaction()) db()->rollBack();
    flash('error', 'No se pudo actualizar la rutina asignada: ' . $e->getMessage());
}

redirect($returnTo);
