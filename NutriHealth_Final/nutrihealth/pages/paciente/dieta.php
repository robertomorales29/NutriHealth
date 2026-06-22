<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$stmt = db()->prepare("SELECT dp.*, d.nombre, d.descripcion, d.indicaciones, p.nombre AS nutriologo_nombre, p.apellido_paterno AS nutriologo_apellido
    FROM dietas_pacientes dp
    INNER JOIN dietas d ON d.id = dp.dieta_id
    INNER JOIN personas p ON p.id = d.nutriologo_id
    WHERE dp.paciente_id = ?
    ORDER BY dp.estado = 'activa' DESC, dp.fecha_inicio DESC");
$stmt->execute([$user['id']]);
$dietas = $stmt->fetchAll();
$pageTitle = 'Mi dieta';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="card card-soft p-4">
        <h1 class="h4 fw-bold mb-3"><i class="bi bi-egg-fried text-success me-2"></i>Dietas asignadas</h1>
        <?php if (!$dietas): ?>
            <p class="text-muted mb-0">Aún no tienes una dieta asignada.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($dietas as $d): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 bg-light rounded-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-2">
                                    <h5 class="fw-bold"><?= e($d['nombre']) ?></h5>
                                    <span class="badge text-bg-<?= $d['estado'] === 'activa' ? 'success' : 'secondary' ?>"><?= e($d['estado']) ?></span>
                                </div>
                                <div class="mb-2"><div class="small text-uppercase text-muted fw-bold">Descripción</div><p class="text-muted mb-0"><?= nl2br(e($d['descripcion'])) ?></p></div>
                                <div class="mb-2"><div class="small text-uppercase text-muted fw-bold">Indicaciones</div><p class="text-muted mb-0"><?= nl2br(e($d['indicaciones'] ?? '')) ?></p></div>
                                <div class="small text-muted">Nutriólogo: <?= e($d['nutriologo_nombre'] . ' ' . $d['nutriologo_apellido']) ?></div>
                                <div class="small text-muted">Periodo: <?= e($d['fecha_inicio']) ?> a <?= e($d['fecha_fin'] ?: 'sin fecha fin') ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
