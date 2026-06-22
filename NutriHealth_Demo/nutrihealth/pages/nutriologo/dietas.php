<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['nutriologo']);
$user = current_user();
$stmt = db()->prepare('SELECT * FROM dietas WHERE nutriologo_id = ? ORDER BY fecha_creacion DESC');
$stmt->execute([$user['id']]);
$dietas = $stmt->fetchAll();
$patients = assigned_patients((int)$user['id'], 'nutriologo');
if (!$patients) $patients = get_people_by_role('paciente');
$pageTitle = 'Dietas';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft p-4 mb-4">
                <h1 class="h4 fw-bold mb-3">Crear dieta</h1>
                <form method="post" action="<?= base_url('actions/dieta_guardar.php') ?>">
                    <input type="hidden" name="id" value="0">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4" placeholder="Objetivo de la dieta, enfoque nutricional, tipo de alimentación..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Indicaciones</label>
                        <textarea name="indicaciones" class="form-control" rows="4" placeholder="Horarios, restricciones, preparación, recomendaciones para el paciente..."></textarea>
                    </div>
                    <button class="btn btn-nh rounded-pill w-100">Crear dieta</button>
                </form>
            </div>
            <div class="card card-soft p-4">
                <h2 class="h5 fw-bold mb-3">Asignar dieta</h2>
                <form method="post" action="<?= base_url('actions/dieta_asignar.php') ?>">
                    <div class="mb-3">
                        <label class="form-label">Dieta</label>
                        <select name="dieta_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($dietas as $d): ?><option value="<?= (int)$d['id'] ?>"><?= e($d['nombre']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="paciente_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($patients as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e(full_name($p)) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3"><label class="form-label">Inicio</label><input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                        <div class="col-6 mb-3"><label class="form-label">Fin</label><input type="date" name="fecha_fin" class="form-control"></div>
                    </div>
                    <button class="btn btn-outline-success rounded-pill w-100">Asignar</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Mis dietas</h2>
                <?php if (!$dietas): ?><p class="text-muted">Aún no has creado dietas.</p><?php endif; ?>
                <div class="row g-3">
                    <?php foreach ($dietas as $d): ?>
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-light h-100">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <h5 class="fw-bold mb-0"><?= e($d['nombre']) ?></h5>
                                    <span class="badge text-bg-success">Dieta</span>
                                </div>
                                <div class="mb-3">
                                    <div class="small text-uppercase text-muted fw-bold">Descripción</div>
                                    <p class="mb-0"><?= nl2br(e($d['descripcion'] ?? 'Sin descripción')) ?></p>
                                </div>
                                <div class="mb-3">
                                    <div class="small text-uppercase text-muted fw-bold">Indicaciones</div>
                                    <p class="mb-0"><?= nl2br(e($d['indicaciones'] ?? 'Sin indicaciones')) ?></p>
                                </div>
                                <div class="small text-muted mb-3">Creada: <?= e($d['fecha_creacion']) ?></div>
                                <a class="btn btn-sm btn-outline-primary rounded-pill" target="_blank" rel="noopener" href="<?= base_url('pages/nutriologo/dieta_editar.php?id=' . (int)$d['id']) ?>">Editar en nueva pestaña</a>
                                <form class="d-inline" method="post" action="<?= base_url('actions/dieta_eliminar.php') ?>" onsubmit="return confirm('¿Eliminar dieta?')">
                                    <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
