<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

try {
    require_login();
    $especialistaId = (int) ($_GET['especialista_id'] ?? 0);
    $fecha = $_GET['fecha'] ?? '';
    $pacienteId = isset($_GET['paciente_id']) && $_GET['paciente_id'] !== '' ? (int) $_GET['paciente_id'] : null;

    if ($especialistaId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['ok' => false, 'message' => 'Parámetros incompletos.']);
        exit;
    }

    if ($fecha < date('Y-m-d')) {
        echo json_encode(['ok' => true, 'slots' => [], 'message' => 'No se pueden agendar fechas anteriores.']);
        exit;
    }

    $specialist = get_person($especialistaId);
    if (!$specialist || !in_array($specialist['rol'], ['nutriologo', 'entrenador'], true)) {
        echo json_encode(['ok' => false, 'message' => 'Especialista no válido.']);
        exit;
    }

    if ($pacienteId !== null) {
        $patient = get_person($pacienteId);
        if (!$patient || $patient['rol'] !== 'paciente') {
            echo json_encode(['ok' => false, 'message' => 'Paciente no válido.']);
            exit;
        }
    } elseif (current_user()['rol'] === 'paciente') {
        $pacienteId = current_user()['id'];
    }

    echo json_encode(['ok' => true, 'slots' => available_slots($especialistaId, $fecha, $pacienteId)]);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => 'Error al consultar disponibilidad.']);
}
