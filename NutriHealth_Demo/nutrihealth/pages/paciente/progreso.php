<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$patient = get_person((int)$user['id']);
$health = latest_health_data((int)$user['id']);
$stmt = db()->prepare('SELECT * FROM historial_salud WHERE paciente_id = ? ORDER BY fecha_registro DESC');
$stmt->execute([$user['id']]);
$historial = $stmt->fetchAll();
$pageTitle = 'Mi progreso';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft p-4">
                <h1 class="h4 fw-bold mb-3">Registrar avance</h1>
                <form method="post" action="<?= base_url('actions/historial_guardar.php') ?>">
                    <input type="hidden" name="return_to" value="pages/paciente/progreso.php">
                    <div class="mb-3"><label class="form-label">Peso kg *</label><input type="number" step="0.01" name="peso" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">% grasa</label><input type="number" step="0.01" name="porcentaje_grasa" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Masa muscular kg</label><input type="number" step="0.01" name="masa_muscular" class="form-control"></div>
                    <div class="row g-2">
                        <div class="col-6 mb-3"><label class="form-label">Cintura cm</label><input type="number" step="0.01" name="cintura" class="form-control"></div>
                        <div class="col-6 mb-3"><label class="form-label">Cadera cm</label><input type="number" step="0.01" name="cadera" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Observaciones</label><textarea name="observaciones" rows="3" class="form-control"></textarea></div>
                    <button class="btn btn-nh rounded-pill w-100">Guardar avance</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-soft p-4 mb-4">
                <h2 class="h4 fw-bold mb-3">Datos iniciales de salud</h2>
                <?php if ($health): ?>
                    <div class="row g-3">
                        <div class="col-md-3"><span class="text-muted small">Peso inicial</span><div class="fw-bold"><?= e($health['peso']) ?> kg</div></div>
                        <div class="col-md-3"><span class="text-muted small">Estatura</span><div class="fw-bold"><?= e($health['estatura']) ?> m</div></div>
                        <div class="col-md-3"><span class="text-muted small">Edad</span><div class="fw-bold"><?= e($health['edad']) ?></div></div>
                        <div class="col-md-3"><span class="text-muted small">Actividad</span><div class="fw-bold"><?= e($health['nivel_actividad']) ?></div></div>
                        <div class="col-12"><span class="text-muted small">Objetivo</span><div><?= e($health['objetivo_salud']) ?: 'Sin objetivo capturado' ?></div></div>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No hay datos iniciales.</p>
                <?php endif; ?>
            </div>
            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Historial de salud</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha</th><th>Peso</th><th>% grasa</th><th>Masa muscular</th><th>Observaciones</th></tr></thead>
                        <tbody>
                        <?php if (!$historial): ?><tr><td colspan="5" class="text-muted">Sin avances registrados.</td></tr><?php endif; ?>
                        <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><?= e($h['fecha_registro']) ?></td>
                                <td><?= e($h['peso']) ?> kg</td>
                                <td><?= e($h['porcentaje_grasa']) ?></td>
                                <td><?= e($h['masa_muscular']) ?></td>
                                <td><?= e($h['observaciones']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
