<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['nutriologo']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/nutriologo/pacientes.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'pages/nutriologo/pacientes.php';
$id = (int) ($_POST['id'] ?? 0);
$dietaId = (int) ($_POST['dieta_id'] ?? 0);
$estado = $_POST['estado'] ?? 'activa';
$fechaInicio = $_POST['fecha_inicio'] ?: null;
$fechaFin = $_POST['fecha_fin'] ?: null;

if (!in_array($estado, all_statuses(), true)) {
    flash('error', 'Estado de dieta no válido.');
    redirect($returnTo);
}

$assignment = db()->prepare('SELECT dp.* FROM dietas_pacientes dp INNER JOIN dietas d ON d.id = dp.dieta_id WHERE dp.id = ? AND d.nutriologo_id = ?');
$assignment->execute([$id, $user['id']]);
$current = $assignment->fetch();
if (!$current) {
    flash('error', 'Asignación de dieta no válida.');
    redirect($returnTo);
}

$validDiet = db()->prepare('SELECT id FROM dietas WHERE id = ? AND nutriologo_id = ?');
$validDiet->execute([$dietaId, $user['id']]);
if (!$validDiet->fetch()) {
    flash('error', 'La dieta seleccionada no es válida.');
    redirect($returnTo);
}

try {
    db()->beginTransaction();
    if ($estado === 'activa') {
        $finish = db()->prepare("UPDATE dietas_pacientes SET estado = 'finalizada' WHERE paciente_id = ? AND estado = 'activa' AND id <> ?");
        $finish->execute([(int)$current['paciente_id'], $id]);
    }
    $update = db()->prepare('UPDATE dietas_pacientes SET dieta_id = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE id = ?');
    $update->execute([$dietaId, $fechaInicio, $fechaFin, $estado, $id]);
    db()->commit();
    flash('success', 'Dieta asignada actualizada correctamente.');
} catch (Throwable $e) {
    if (db()->inTransaction()) db()->rollBack();
    flash('error', 'No se pudo actualizar la dieta asignada: ' . $e->getMessage());
}

redirect($returnTo);
