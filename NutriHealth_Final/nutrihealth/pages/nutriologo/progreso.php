<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['nutriologo']);
$user = current_user();
$patients = assigned_patients((int)$user['id'], 'nutriologo');
$allowedIds = array_map(static fn(array $p): int => (int) $p['id'], $patients);
$requestedId = (int)($_GET['paciente_id'] ?? 0);
$pacienteId = in_array($requestedId, $allowedIds, true) ? $requestedId : ($allowedIds[0] ?? 0);
$patient = $pacienteId ? get_person($pacienteId) : null;
$health = $patient ? latest_health_data($pacienteId) : null;
$currentHealth = $patient ? latest_current_health_data($pacienteId) : null;
$historial = [];
if ($patient) {
    $stmt = db()->prepare('SELECT * FROM historial_salud WHERE paciente_id = ? ORDER BY fecha_registro DESC, id DESC');
    $stmt->execute([$pacienteId]);
    $historial = $stmt->fetchAll();
}
$pageTitle = 'Progreso del paciente';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft p-4 mb-4">
                <h1 class="h4 fw-bold mb-3">Seleccionar paciente</h1>
                <?php if (!$patients): ?>
                    <p class="text-muted mb-0">Primero asigna un paciente desde el apartado de pacientes.</p>
                <?php else: ?>
                    <form method="get">
                        <label class="form-label" for="buscar-paciente-progreso-nutriologo">Buscar por nombre</label>
                        <input id="buscar-paciente-progreso-nutriologo" type="search" class="form-control mb-2" placeholder="Escribe el nombre" data-filter-select="#paciente-progreso-nutriologo">
                        <select id="paciente-progreso-nutriologo" name="paciente_id" class="form-select mb-3">
                            <?php foreach ($patients as $p): ?><option value="<?= (int)$p['id'] ?>" data-search="<?= e(full_name($p)) ?>" <?= $pacienteId === (int)$p['id'] ? 'selected' : '' ?>><?= e(full_name($p)) ?></option><?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-primary rounded-pill w-100">Consultar</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php if ($patient): ?>
            <div class="card card-soft p-4">
                <h2 class="h5 fw-bold mb-3">Registrar avance</h2>
                <form method="post" action="<?= base_url('actions/historial_guardar.php') ?>">
                    <input type="hidden" name="return_to" value="pages/nutriologo/progreso.php?paciente_id=<?= (int)$pacienteId ?>">
                    <input type="hidden" name="paciente_id" value="<?= (int)$pacienteId ?>">
                    <div class="mb-3"><label class="form-label">Peso kg *</label><input type="number" min="0.01" step="0.01" name="peso" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">% grasa</label><input type="number" min="0" step="0.01" name="porcentaje_grasa" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Masa muscular kg</label><input type="number" min="0" step="0.01" name="masa_muscular" class="form-control"></div>
                    <div class="row g-2">
                        <div class="col-6 mb-3"><label class="form-label">Cintura cm</label><input type="number" min="0" step="0.01" name="cintura" class="form-control"></div>
                        <div class="col-6 mb-3"><label class="form-label">Cadera cm</label><input type="number" min="0" step="0.01" name="cadera" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="3"></textarea></div>
                    <button class="btn btn-nh rounded-pill w-100">Guardar avance</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-8">
            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Historial <?= $patient ? 'de ' . e(full_name($patient)) : '' ?></h2>
                <?php if ($health || $currentHealth): ?>
                    <div class="alert alert-info">
                        <strong>Objetivo actual:</strong> <?= e($currentHealth['objetivo_salud'] ?? $health['objetivo_salud'] ?? 'Sin objetivo') ?> ·
                        <strong>Actividad actual:</strong> <?= e($currentHealth['nivel_actividad'] ?? $health['nivel_actividad'] ?? 'Sin dato') ?> ·
                        <strong>Alergias:</strong> <?= e($health['alergias'] ?? 'Sin dato') ?>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha</th><th>Peso</th><th>% grasa</th><th>Masa muscular</th><th>Cintura</th><th>Cadera</th><th>Observaciones</th><th>Acciones</th></tr></thead>
                        <tbody>
                        <?php if (!$historial): ?><tr><td colspan="8" class="text-muted">Sin historial.</td></tr><?php endif; ?>
                        <?php foreach ($historial as $h): ?>
                            <tr>
                                <td class="text-nowrap"><?= e($h['fecha_registro']) ?></td>
                                <td><?= e((string)$h['peso']) ?></td>
                                <td><?= e((string)$h['porcentaje_grasa']) ?></td>
                                <td><?= e((string)$h['masa_muscular']) ?></td>
                                <td><?= e((string)$h['cintura']) ?></td>
                                <td><?= e((string)$h['cadera']) ?></td>
                                <td><?= e($h['observaciones']) ?></td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editarAvance<?= (int)$h['id'] ?>">Editar</button>
                                    <form class="d-inline" method="post" action="<?= base_url('actions/historial_eliminar.php') ?>" onsubmit="return confirm('¿Eliminar este avance? Esta acción no se puede deshacer.')">
                                        <input type="hidden" name="return_to" value="pages/nutriologo/progreso.php?paciente_id=<?= (int)$pacienteId ?>">
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

<?php foreach ($historial as $h): ?>
<div class="modal fade" id="editarAvance<?= (int)$h['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" action="<?= base_url('actions/historial_guardar.php') ?>">
                <div class="modal-header"><h2 class="modal-title h5">Editar avance</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="return_to" value="pages/nutriologo/progreso.php?paciente_id=<?= (int)$pacienteId ?>">
                    <input type="hidden" name="paciente_id" value="<?= (int)$pacienteId ?>">
                    <input type="hidden" name="id" value="<?= (int)$h['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Fecha y hora</label><input type="datetime-local" name="fecha_registro" class="form-control" value="<?= e(date('Y-m-d\TH:i', strtotime($h['fecha_registro']))) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Peso kg *</label><input type="number" min="0.01" step="0.01" name="peso" class="form-control" value="<?= e((string)$h['peso']) ?>" required></div>
                        <div class="col-md-4"><label class="form-label">% grasa</label><input type="number" min="0" step="0.01" name="porcentaje_grasa" class="form-control" value="<?= e((string)$h['porcentaje_grasa']) ?>"></div>
                        <div class="col-md-4"><label class="form-label">Masa muscular kg</label><input type="number" min="0" step="0.01" name="masa_muscular" class="form-control" value="<?= e((string)$h['masa_muscular']) ?>"></div>
                        <div class="col-md-4"><label class="form-label">Cintura cm</label><input type="number" min="0" step="0.01" name="cintura" class="form-control" value="<?= e((string)$h['cintura']) ?>"></div>
                        <div class="col-md-4"><label class="form-label">Cadera cm</label><input type="number" min="0" step="0.01" name="cadera" class="form-control" value="<?= e((string)$h['cadera']) ?>"></div>
                        <div class="col-12"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="3"><?= e($h['observaciones']) ?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-nh rounded-pill">Guardar cambios</button></div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
