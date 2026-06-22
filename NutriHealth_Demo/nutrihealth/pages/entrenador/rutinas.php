<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['entrenador']);
$user = current_user();
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM rutinas WHERE id = ? AND entrenador_id = ?');
    $stmt->execute([(int)$_GET['edit'], $user['id']]);
    $edit = $stmt->fetch() ?: null;
}
$stmt = db()->prepare('SELECT * FROM rutinas WHERE entrenador_id = ? ORDER BY fecha_creacion DESC');
$stmt->execute([$user['id']]);
$rutinas = $stmt->fetchAll();
$patients = assigned_patients((int)$user['id'], 'entrenador');
if (!$patients) $patients = get_people_by_role('paciente');
$days = week_days();
$pageTitle = 'Rutinas';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft p-4 mb-4">
                <h1 class="h4 fw-bold mb-3"><?= $edit ? 'Modificar rutina' : 'Crear rutina' ?></h1>
                <form method="post" action="<?= base_url('actions/rutina_guardar.php') ?>">
                    <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input name="nombre" class="form-control" value="<?= e($edit['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción general</label>
                        <textarea name="descripcion" class="form-control" rows="5" placeholder="Objetivo, intensidad, calentamiento, indicaciones generales...">
<?= e($edit['descripcion'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-nh rounded-pill w-100"><?= $edit ? 'Actualizar rutina' : 'Crear rutina' ?></button>
                    <?php if ($edit): ?>
                        <a class="btn btn-outline-secondary rounded-pill w-100 mt-2" href="<?= base_url('pages/entrenador/rutinas.php') ?>">Cancelar edición</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card card-soft p-4">
                <h2 class="h5 fw-bold mb-3">Asignar rutina a paciente</h2>
                <form method="post" action="<?= base_url('actions/rutina_asignar.php') ?>">
                    <div class="mb-3">
                        <label class="form-label">Rutina</label>
                        <select name="rutina_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($rutinas as $r): ?>
                                <option value="<?= (int)$r['id'] ?>"><?= e($r['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="paciente_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($patients as $p): ?>
                                <option value="<?= (int)$p['id'] ?>"><?= e(full_name($p)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-outline-success rounded-pill w-100">Asignar</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Mis rutinas</h2>
                <?php if (!$rutinas): ?><p class="text-muted">Aún no has creado rutinas.</p><?php endif; ?>
                <?php foreach ($rutinas as $r): ?>
                    <?php $ejercicios = routine_exercises((int)$r['id']); ?>
                    <div class="border rounded-4 p-3 mb-4 bg-light" id="rutina-<?= (int)$r['id'] ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1"><?= e($r['nombre']) ?></h5>
                                <p class="text-muted mb-0"><?= nl2br(e($r['descripcion'])) ?></p>
                            </div>
                            <div class="text-nowrap">
                                <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= base_url('pages/entrenador/rutinas.php?edit=' . (int)$r['id']) ?>">Editar rutina</a>
                                <form class="d-inline" method="post" action="<?= base_url('actions/rutina_eliminar.php') ?>" onsubmit="return confirm('¿Eliminar rutina? También se eliminarán sus ejercicios si la base lo permite.')">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                </form>
                            </div>
                        </div>

                        <h6 class="fw-bold text-success">Ejercicios de la rutina</h6>
                        <?php if (!$ejercicios): ?>
                            <div class="alert alert-light border small mb-3">Sin ejercicios capturados.</div>
                        <?php endif; ?>
                        <div class="accordion mb-3" id="accordion-rutina-<?= (int)$r['id'] ?>">
                            <?php foreach ($ejercicios as $ej): ?>
                                <?php $collapseId = 'ejercicio-' . (int)$ej['id']; ?>
                                <div class="accordion-item border-0 mb-2 rounded-3 overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>">
                                            <span class="fw-semibold me-2"><?= e(ucfirst($ej['dia_semana'])) ?>:</span>
                                            <?= e($ej['nombre_ejercicio']) ?>
                                            <span class="badge text-bg-light ms-2"><?= e((string)$ej['series']) ?> series</span>
                                            <span class="badge text-bg-light ms-1"><?= e((string)$ej['repeticiones']) ?> reps</span>
                                            <span class="badge text-bg-light ms-1"><?= e((string)$ej['duracion_minutos']) ?> min</span>
                                        </button>
                                    </h2>
                                    <div id="<?= $collapseId ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-rutina-<?= (int)$r['id'] ?>">
                                        <div class="accordion-body bg-white">
                                            <form class="row g-2" method="post" action="<?= base_url('actions/rutina_ejercicio_guardar.php') ?>">
                                                <input type="hidden" name="return_to" value="pages/entrenador/rutinas.php#rutina-<?= (int)$r['id'] ?>">
                                                <input type="hidden" name="id" value="<?= (int)$ej['id'] ?>">
                                                <input type="hidden" name="rutina_id" value="<?= (int)$r['id'] ?>">
                                                <div class="col-md-3">
                                                    <label class="form-label small">Día</label>
                                                    <select name="dia_semana" class="form-select form-select-sm">
                                                        <?php foreach ($days as $day): ?>
                                                            <option value="<?= e($day) ?>" <?= $ej['dia_semana'] === $day ? 'selected' : '' ?>><?= e(ucfirst($day)) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-9">
                                                    <label class="form-label small">Ejercicio</label>
                                                    <input name="nombre_ejercicio" class="form-control form-control-sm" value="<?= e($ej['nombre_ejercicio']) ?>" required>
                                                </div>
                                                <div class="col-md-3"><label class="form-label small">Series</label><input type="number" min="0" name="series" class="form-control form-control-sm" value="<?= e((string)$ej['series']) ?>"></div>
                                                <div class="col-md-3"><label class="form-label small">Repeticiones</label><input type="number" min="0" name="repeticiones" class="form-control form-control-sm" value="<?= e((string)$ej['repeticiones']) ?>"></div>
                                                <div class="col-md-3"><label class="form-label small">Minutos</label><input type="number" min="0" name="duracion_minutos" class="form-control form-control-sm" value="<?= e((string)$ej['duracion_minutos']) ?>"></div>
                                                <div class="col-md-3"><label class="form-label small">Descanso seg.</label><input type="number" min="0" name="descanso_segundos" class="form-control form-control-sm" value="<?= e((string)$ej['descanso_segundos']) ?>"></div>
                                                <div class="col-md-12"><label class="form-label small">Descripción</label><textarea name="descripcion" class="form-control form-control-sm" rows="2"><?= e($ej['descripcion']) ?></textarea></div>
                                                <div class="col-md-12"><label class="form-label small">Link de video</label><input type="url" name="link_video" class="form-control form-control-sm" value="<?= e($ej['link_video']) ?>"></div>
                                                <div class="col-md-8"><button class="btn btn-sm btn-nh rounded-pill">Guardar cambios del ejercicio</button></div>
                                            </form>
                                            <form class="mt-2" method="post" action="<?= base_url('actions/rutina_ejercicio_eliminar.php') ?>" onsubmit="return confirm('¿Eliminar este ejercicio?')">
                                                <input type="hidden" name="return_to" value="pages/entrenador/rutinas.php#rutina-<?= (int)$r['id'] ?>">
                                                <input type="hidden" name="id" value="<?= (int)$ej['id'] ?>">
                                                <button class="btn btn-sm btn-outline-danger rounded-pill">Eliminar ejercicio</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="border-top pt-3">
                            <h6 class="fw-bold mb-2">Agregar ejercicio</h6>
                            <form class="row g-2" method="post" action="<?= base_url('actions/rutina_ejercicio_guardar.php') ?>">
                                <input type="hidden" name="return_to" value="pages/entrenador/rutinas.php#rutina-<?= (int)$r['id'] ?>">
                                <input type="hidden" name="rutina_id" value="<?= (int)$r['id'] ?>">
                                <div class="col-md-3"><select name="dia_semana" class="form-select form-select-sm"><?php foreach ($days as $day): ?><option value="<?= e($day) ?>"><?= e(ucfirst($day)) ?></option><?php endforeach; ?></select></div>
                                <div class="col-md-5"><input name="nombre_ejercicio" class="form-control form-control-sm" placeholder="Ejercicio" required></div>
                                <div class="col-md-2"><input type="number" min="0" name="series" class="form-control form-control-sm" placeholder="Series"></div>
                                <div class="col-md-2"><input type="number" min="0" name="repeticiones" class="form-control form-control-sm" placeholder="Reps"></div>
                                <div class="col-md-3"><input type="number" min="0" name="duracion_minutos" class="form-control form-control-sm" placeholder="Minutos"></div>
                                <div class="col-md-3"><input type="number" min="0" name="descanso_segundos" class="form-control form-control-sm" placeholder="Descanso seg."></div>
                                <div class="col-md-6"><input type="url" name="link_video" class="form-control form-control-sm" placeholder="Link de video opcional"></div>
                                <div class="col-md-12"><input name="descripcion" class="form-control form-control-sm" placeholder="Descripción breve del ejercicio"></div>
                                <div class="col-md-12"><button class="btn btn-sm btn-outline-success rounded-pill">Agregar ejercicio</button></div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
