<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['entrenador']);
$user = current_user();
$patients = assigned_patients((int)$user['id'], 'entrenador');
$ids = array_column($patients, 'id');
$dietas = [];
if ($ids) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT dp.*, d.nombre AS dieta_nombre, d.descripcion, d.indicaciones, pac.nombre AS paciente_nombre, pac.apellido_paterno AS paciente_apellido, nut.nombre AS nutriologo_nombre
        FROM dietas_pacientes dp
        INNER JOIN dietas d ON d.id = dp.dieta_id
        INNER JOIN personas pac ON pac.id = dp.paciente_id
        INNER JOIN personas nut ON nut.id = d.nutriologo_id
        WHERE dp.paciente_id IN ($placeholders)
        ORDER BY dp.estado = 'activa' DESC, dp.fecha_inicio DESC");
    $stmt->execute($ids);
    $dietas = $stmt->fetchAll();
}
$pageTitle = 'Dietas de pacientes';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4"><div class="card card-soft p-4"><h1 class="h4 fw-bold mb-3">Dietas asignadas a mis pacientes</h1><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Paciente</th><th>Dieta</th><th>Nutriólogo</th><th>Estado</th><th>Periodo</th></tr></thead><tbody><?php if (!$dietas): ?><tr><td colspan="5" class="text-muted">No hay dietas para tus pacientes asignados.</td></tr><?php endif; ?><?php foreach ($dietas as $d): ?><tr><td><?= e($d['paciente_nombre'].' '.$d['paciente_apellido']) ?></td><td><strong><?= e($d['dieta_nombre']) ?></strong><div class="small text-muted"><strong>Descripción:</strong> <?= e($d['descripcion']) ?></div><div class="small text-muted"><strong>Indicaciones:</strong> <?= e($d['indicaciones'] ?? '') ?></div></td><td><?= e($d['nutriologo_nombre']) ?></td><td><span class="badge text-bg-<?= $d['estado']==='activa'?'success':'secondary' ?>"><?= e($d['estado']) ?></span></td><td><?= e($d['fecha_inicio']) ?> - <?= e($d['fecha_fin'] ?: 'sin fin') ?></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
