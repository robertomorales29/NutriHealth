<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'dashboard.php';
$id = (int) ($_POST['id'] ?? 0);
$pacienteId = (int) ($_POST['paciente_id'] ?? 0);

if ($id > 0) {
    $recordStmt = db()->prepare('SELECT * FROM historial_salud WHERE id = ?');
    $recordStmt->execute([$id]);
    $record = $recordStmt->fetch();
    if (!$record) {
        flash('error', 'El avance que intentas editar no existe.');
        redirect($returnTo);
    }
    $pacienteId = (int) $record['paciente_id'];
} elseif ($user['rol'] === 'paciente') {
    $pacienteId = (int) $user['id'];
}

$patient = get_person($pacienteId);
if (!$patient || $patient['rol'] !== 'paciente' || !can_manage_health_history($user, $pacienteId)) {
    flash('error', 'No tienes permiso para modificar el historial de este paciente.');
    redirect($returnTo);
}

$peso = clean_decimal_or_null($_POST['peso'] ?? null);
if ($peso === null || $peso <= 0) {
    flash('error', 'El peso es obligatorio y debe ser mayor que cero.');
    redirect($returnTo);
}

$fecha = trim($_POST['fecha_registro'] ?? '');
$fechaSql = null;
if ($fecha !== '') {
    $date = DateTime::createFromFormat('Y-m-d\TH:i', $fecha);
    if (!$date) {
        flash('error', 'La fecha del avance no es válida.');
        redirect($returnTo);
    }
    $fechaSql = $date->format('Y-m-d H:i:s');
}

$params = [
    $peso,
    clean_decimal_or_null($_POST['porcentaje_grasa'] ?? null),
    clean_decimal_or_null($_POST['masa_muscular'] ?? null),
    clean_decimal_or_null($_POST['cintura'] ?? null),
    clean_decimal_or_null($_POST['cadera'] ?? null),
    trim($_POST['observaciones'] ?? '') ?: null,
];

try {
    db()->beginTransaction();

    if ($id > 0) {
        if ($fechaSql !== null) {
            $stmt = db()->prepare('UPDATE historial_salud
                SET peso = ?, porcentaje_grasa = ?, masa_muscular = ?, cintura = ?, cadera = ?, observaciones = ?, fecha_registro = ?
                WHERE id = ? AND paciente_id = ?');
            $stmt->execute([...$params, $fechaSql, $id, $pacienteId]);
        } else {
            $stmt = db()->prepare('UPDATE historial_salud
                SET peso = ?, porcentaje_grasa = ?, masa_muscular = ?, cintura = ?, cadera = ?, observaciones = ?
                WHERE id = ? AND paciente_id = ?');
            $stmt->execute([...$params, $id, $pacienteId]);
        }
        $message = 'Avance actualizado correctamente.';
    } else {
        $stmt = db()->prepare('INSERT INTO historial_salud
            (paciente_id, peso, porcentaje_grasa, masa_muscular, cintura, cadera, observaciones)
            VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$pacienteId, ...$params]);
        $message = 'Avance registrado correctamente.';
    }

    sync_current_health_from_latest_history($pacienteId);
    db()->commit();
    flash('success', $message);
} catch (Throwable $e) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    flash('error', 'No se pudo guardar el avance.');
}

redirect($returnTo);
