<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$health = latest_health_data((int) $user['id']);
$currentHealth = latest_current_health_data((int) $user['id']);
$stmt = db()->prepare('SELECT * FROM historial_salud WHERE paciente_id = ? ORDER BY fecha_registro DESC, id DESC');
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
                    <div class="mb-3"><label class="form-label">Peso kg *</label><input type="number" min="0.01" step="0.01" name="peso" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">% grasa</label><input type="number" min="0" step="0.01" name="porcentaje_grasa" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Masa muscular kg</label><input type="number" min="0" step="0.01" name="masa_muscular" class="form-control"></div>
                    <div class="row g-2">
                        <div class="col-6 mb-3"><label class="form-label">Cintura cm</label><input type="number" min="0" step="0.01" name="cintura" class="form-control"></div>
                        <div class="col-6 mb-3"><label class="form-label">Cadera cm</label><input type="number" min="0" step="0.01" name="cadera" class="form-control"></div>
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
                        <div class="col-md-3"><span class="text-muted small">Peso inicial</span><div class="fw-bold"><?= e((string)$health['peso']) ?> kg</div></div>
                        <div class="col-md-3"><span class="text-muted small">Estatura</span><div class="fw-bold"><?= e((string)$health['estatura']) ?> m</div></div>
                        <div class="col-md-3"><span class="text-muted small">Edad</span><div class="fw-bold"><?= e((string)$health['edad']) ?></div></div>
                        <div class="col-md-3"><span class="text-muted small">Actividad inicial</span><div class="fw-bold"><?= e($health['nivel_actividad']) ?></div></div>
                        <div class="col-12"><span class="text-muted small">Objetivo inicial</span><div><?= e($health['objetivo_salud']) ?: 'Sin objetivo capturado' ?></div></div>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No hay datos iniciales.</p>
                <?php endif; ?>
            </div>

            <div class="card card-soft p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="h4 fw-bold mb-0">Datos actuales del paciente</h2>
                    <?php if ($currentHealth): ?><span class="small text-muted">Actualizados: <?= e($currentHealth['fecha_actualizacion']) ?></span><?php endif; ?>
                </div>
                <p class="text-muted small">Estos valores se actualizan con tu último avance y también puedes corregirlos manualmente.</p>
                <form method="post" action="<?= base_url('actions/datos_salud_actuales_guardar.php') ?>">
                    <input type="hidden" name="return_to" value="pages/paciente/progreso.php">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Peso actual kg *</label><input type="number" min="0.01" step="0.01" name="peso" class="form-control" value="<?= e((string)($currentHealth['peso'] ?? '')) ?>" required></div>
                        <div class="col-md-4"><label class="form-label">% grasa</label><input type="number" min="0" step="0.01" name="porcentaje_grasa" class="form-control" value="<?= e((string)($currentHealth['porcentaje_grasa'] ?? '')) ?>"></div>
                        <div class="col-md-4"><label class="form-label">Masa muscular kg</label><input type="number" min="0" step="0.01" name="masa_muscular" class="form-control" value="<?= e((string)($currentHealth['masa_muscular'] ?? '')) ?>"></div>
                        <div class="col-md-4"><label class="form-label">Cintura cm</label><input type="number" min="0" step="0.01" name="cintura" class="form-control" value="<?= e((string)($currentHealth['cintura'] ?? '')) ?>"></div>
                        <div class="col-md-4"><label class="form-label">Cadera cm</label><input type="number" min="0" step="0.01" name="cadera" class="form-control" value="<?= e((string)($currentHealth['cadera'] ?? '')) ?>"></div>
                        <div class="col-md-4">
                            <label class="form-label">Nivel de actividad</label>
                            <select name="nivel_actividad" class="form-select">
                                <?php foreach (['Bajo', 'Moderado', 'Alto'] as $nivel): ?>
                                    <option value="<?= e($nivel) ?>" <?= ($currentHealth['nivel_actividad'] ?? 'Moderado') === $nivel ? 'selected' : '' ?>><?= e($nivel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Objetivo de salud actual</label><input type="text" maxlength="150" name="objetivo_salud" class="form-control" value="<?= e($currentHealth['objetivo_salud'] ?? '') ?>"></div>
                        <div class="col-12"><button class="btn btn-outline-primary rounded-pill">Actualizar datos actuales</button></div>
                    </div>
                </form>
            </div>

            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Historial de salud</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha</th><th>Peso</th><th>% grasa</th><th>Masa muscular</th><th>Cintura</th><th>Cadera</th><th>Observaciones</th><th>Acciones</th></tr></thead>
                        <tbody>
                        <?php if (!$historial): ?><tr><td colspan="8" class="text-muted">Sin avances registrados.</td></tr><?php endif; ?>
                        <?php foreach ($historial as $h): ?>
                            <tr>
                                <td class="text-nowrap"><?= e($h['fecha_registro']) ?></td>
                                <td><?= e((string)$h['peso']) ?> kg</td>
                                <td><?= e((string)$h['porcentaje_grasa']) ?></td>
                                <td><?= e((string)$h['masa_muscular']) ?></td>
                                <td><?= e((string)$h['cintura']) ?><?= $h['cintura'] !== null ? ' cm' : '' ?></td>
                                <td><?= e((string)$h['cadera']) ?><?= $h['cadera'] !== null ? ' cm' : '' ?></td>
                                <td><?= e($h['observaciones']) ?></td>
                                <td>
                                    <form method="post" action="<?= base_url('actions/historial_eliminar.php') ?>" onsubmit="return confirm('¿Eliminar este avance? Esta acción no se puede deshacer.')">
                                        <input type="hidden" name="return_to" value="pages/paciente/progreso.php">
                                        <input type="hidden" name="id" value="<?= (int)$h['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                    </form>
                                </td>
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
