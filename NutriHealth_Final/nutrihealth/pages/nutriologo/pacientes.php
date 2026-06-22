<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['nutriologo']);
$user = current_user();
$assigned = assigned_patients((int)$user['id'], 'nutriologo');
$allPatients = get_people_by_role('paciente');
$stmt = db()->prepare('SELECT * FROM dietas WHERE nutriologo_id = ? ORDER BY nombre');
$stmt->execute([$user['id']]);
$misDietas = $stmt->fetchAll();
$estados = all_statuses();
$pageTitle = 'Pacientes';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft p-4">
                <h1 class="h4 fw-bold mb-3">Asignar paciente</h1>
                <form method="post" action="<?= base_url('actions/paciente_asignar.php') ?>">
                    <input type="hidden" name="return_to" value="pages/nutriologo/pacientes.php">
                    <label class="form-label" for="buscar-asignar-paciente-nutriologo">Buscar paciente</label>
                    <input id="buscar-asignar-paciente-nutriologo" type="search" class="form-control mb-2" placeholder="Nombre o teléfono" data-filter-select="#asignar-paciente-nutriologo">
                    <select id="asignar-paciente-nutriologo" name="paciente_id" class="form-select mb-3" required>
                        <option value="">Selecciona...</option>
                        <?php foreach ($allPatients as $p): ?>
                            <option value="<?= (int)$p['id'] ?>" data-search="<?= e(full_name($p) . ' ' . ($p['telefono'] ?? '')) ?>"><?= e(full_name($p)) ?><?= !empty($p['telefono']) ? ' — ' . e($p['telefono']) : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-nh rounded-pill w-100">Asignar</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Mis pacientes</h2>
                <input type="search" class="form-control mb-3" placeholder="Buscar por nombre o número telefónico" data-filter-items="#pacientesNutriologo [data-search-item]" data-empty-target="#sin-resultados-nutriologo">
                <p id="sin-resultados-nutriologo" class="text-muted d-none">No se encontraron pacientes con ese criterio.</p>
                <?php if (!$assigned): ?><p class="text-muted">Aún no tienes pacientes asignados.</p><?php endif; ?>
                <div class="accordion" id="pacientesNutriologo">
                    <?php foreach ($assigned as $p): ?>
                        <?php
                        $dietasPaciente = diets_for_patient((int)$p['id']);
                        $rutinasPaciente = routines_for_patient((int)$p['id']);
                        $itemId = 'paciente-nutriologo-' . (int)$p['id'];
                        ?>
                        <div class="accordion-item mb-3 border rounded-4 overflow-hidden" data-search-item data-search="<?= e(full_name($p) . ' ' . ($p['telefono'] ?? '')) ?>">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $itemId ?>">
                                    <div>
                                        <strong><?= e(full_name($p)) ?></strong>
                                        <div class="small text-muted"><?= e($p['email']) ?> · <?= e($p['telefono']) ?></div>
                                    </div>
                                </button>
                            </h2>
                            <div id="<?= $itemId ?>" class="accordion-collapse collapse" data-bs-parent="#pacientesNutriologo">
                                <div class="accordion-body">
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= base_url('pages/nutriologo/progreso.php?paciente_id=' . (int)$p['id']) ?>">Ver historial y progreso</a>
                                    </div>

                                    <div class="border rounded-4 p-3 mb-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <h3 class="h6 fw-bold mb-0">Dietas asignadas</h3>
                                            <span class="badge text-bg-success">Editable por nutriólogo</span>
                                        </div>
                                        <?php if (!$dietasPaciente): ?><p class="text-muted small mb-2">Este paciente todavía no tiene dietas asignadas.</p><?php endif; ?>
                                        <?php foreach ($dietasPaciente as $dp): ?>
                                            <?php $puedeEditar = (int)$dp['nutriologo_id'] === (int)$user['id']; ?>
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body">
                                                    <?php if ($puedeEditar): ?>
                                                        <form class="row g-2 align-items-end" method="post" action="<?= base_url('actions/dieta_asignacion_actualizar.php') ?>">
                                                            <input type="hidden" name="return_to" value="pages/nutriologo/pacientes.php">
                                                            <input type="hidden" name="id" value="<?= (int)$dp['id'] ?>">
                                                            <div class="col-md-4">
                                                                <label class="form-label small">Dieta</label>
                                                                <select name="dieta_id" class="form-select form-select-sm" required>
                                                                    <?php foreach ($misDietas as $md): ?>
                                                                        <option value="<?= (int)$md['id'] ?>" <?= (int)$dp['dieta_id'] === (int)$md['id'] ? 'selected' : '' ?>><?= e($md['nombre']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2"><label class="form-label small">Inicio</label><input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= e($dp['fecha_inicio']) ?>"></div>
                                                            <div class="col-md-2"><label class="form-label small">Fin</label><input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= e($dp['fecha_fin']) ?>"></div>
                                                            <div class="col-md-2">
                                                                <label class="form-label small">Estado</label>
                                                                <select name="estado" class="form-select form-select-sm">
                                                                    <?php foreach ($estados as $estado): ?><option value="<?= e($estado) ?>" <?= $dp['estado'] === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option><?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2"><button class="btn btn-sm btn-outline-primary rounded-pill w-100">Guardar</button></div>
                                                        </form>
                                                        <a class="btn btn-sm btn-link px-0" target="_blank" rel="noopener" href="<?= base_url('pages/nutriologo/dieta_editar.php?id=' . (int)$dp['dieta_id']) ?>">Editar contenido de esta dieta</a>
                                                    <?php else: ?>
                                                        <strong><?= e($dp['dieta_nombre']) ?></strong>
                                                        <div class="small text-muted">Nutriólogo: <?= e(trim(($dp['nutriologo_nombre'] ?? '') . ' ' . ($dp['nutriologo_apellido'] ?? ''))) ?> · Estado: <?= e($dp['estado']) ?></div>
                                                        <p class="small mb-0"><?= nl2br(e($dp['dieta_descripcion'])) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <form class="row g-2 align-items-end mt-3" method="post" action="<?= base_url('actions/dieta_asignar.php') ?>">
                                            <input type="hidden" name="return_to" value="pages/nutriologo/pacientes.php">
                                            <input type="hidden" name="paciente_id" value="<?= (int)$p['id'] ?>">
                                            <div class="col-md-5">
                                                <label class="form-label small">Asignar nueva dieta</label>
                                                <select name="dieta_id" class="form-select form-select-sm" required>
                                                    <option value="">Selecciona...</option>
                                                    <?php foreach ($misDietas as $md): ?><option value="<?= (int)$md['id'] ?>"><?= e($md['nombre']) ?></option><?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3"><label class="form-label small">Inicio</label><input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>"></div>
                                            <div class="col-md-3"><label class="form-label small">Fin</label><input type="date" name="fecha_fin" class="form-control form-control-sm"></div>
                                            <div class="col-md-1"><button class="btn btn-sm btn-success rounded-pill">+</button></div>
                                        </form>
                                    </div>

                                    <div class="border rounded-4 p-3 bg-white">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <h3 class="h6 fw-bold mb-0">Rutina del paciente</h3>
                                            <span class="badge text-bg-secondary">Solo consulta</span>
                                        </div>
                                        <?php if (!$rutinasPaciente): ?><p class="text-muted small mb-0">Sin rutina asignada.</p><?php endif; ?>
                                        <?php foreach ($rutinasPaciente as $rp): ?>
                                            <div class="border-bottom py-2">
                                                <strong><?= e($rp['rutina_nombre']) ?></strong>
                                                <span class="badge text-bg-<?= $rp['estado'] === 'activa' ? 'success' : 'secondary' ?> ms-1"><?= e($rp['estado']) ?></span>
                                                <div class="small text-muted">Entrenador: <?= e(trim(($rp['entrenador_nombre'] ?? '') . ' ' . ($rp['entrenador_apellido'] ?? ''))) ?> · <?= e($rp['fecha_inicio']) ?> a <?= e($rp['fecha_fin'] ?: 'sin fin') ?></div>
                                                <div class="small"><?= nl2br(e($rp['rutina_descripcion'])) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
