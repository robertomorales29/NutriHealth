<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['entrenador']);
$user = current_user();
$patients = assigned_patients((int)$user['id'], 'entrenador');
$appointments = upcoming_appointments_for_user((int)$user['id'], 'entrenador', 6);
$stmt = db()->prepare('SELECT COUNT(*) FROM rutinas WHERE entrenador_id = ?');
$stmt->execute([$user['id']]);
$routinesCount = (int)$stmt->fetchColumn();
$pageTitle = 'Panel del entrenador';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="dashboard-hero p-4 p-lg-5 mb-4">
        <span class="badge badge-role rounded-pill mb-2">Entrenador</span>
        <h1 class="fw-bold mb-2">Panel de <?= e($user['nombre']) ?></h1>
        <p class="text-muted mb-0">Controla pacientes, agenda citas, revisa progreso y crea rutinas deportivas.</p>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="card card-soft p-4 h-100"><i class="bi bi-people fs-2 text-success"></i><h3 class="fw-bold mt-2"><?= count($patients) ?></h3><span class="text-muted">Pacientes asignados</span></div></div>
        <div class="col-md-4"><div class="card card-soft p-4 h-100"><i class="bi bi-calendar-check fs-2 text-primary"></i><h3 class="fw-bold mt-2"><?= count($appointments) ?></h3><span class="text-muted">Citas próximas</span></div></div>
        <div class="col-md-4"><div class="card card-soft p-4 h-100"><i class="bi bi-activity fs-2 text-success"></i><h3 class="fw-bold mt-2"><?= $routinesCount ?></h3><span class="text-muted">Rutinas creadas</span></div></div>
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card card-soft p-4 h-100">
                <div class="d-flex justify-content-between mb-3"><h2 class="h5 fw-bold">Calendario cercano</h2><a href="<?= base_url('pages/entrenador/citas.php') ?>">Ver citas</a></div>
                <?php if (!$appointments): ?><p class="text-muted">No tienes citas próximas.</p><?php endif; ?>
                <?php foreach ($appointments as $cita): ?>
                    <div class="border-bottom py-2 d-flex justify-content-between">
                        <div><strong><?= e($cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']) ?></strong><div class="small text-muted"><?= e($cita['motivo']) ?></div></div>
                        <div class="text-end small"><strong><?= e($cita['fecha']) ?></strong><br><?= e(substr($cita['hora'], 0, 5)) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6"><a class="card card-soft p-3 h-100 text-decoration-none text-dark" href="<?= base_url('pages/entrenador/pacientes.php') ?>"><i class="bi bi-people fs-3 text-success"></i><strong>Pacientes</strong><span class="small text-muted">Control</span></a></div>
                <div class="col-6"><a class="card card-soft p-3 h-100 text-decoration-none text-dark" href="<?= base_url('pages/entrenador/rutinas.php') ?>"><i class="bi bi-activity fs-3 text-primary"></i><strong>Rutinas</strong><span class="small text-muted">Crear y asignar</span></a></div>
                <div class="col-6"><a class="card card-soft p-3 h-100 text-decoration-none text-dark" href="<?= base_url('pages/entrenador/citas.php') ?>"><i class="bi bi-calendar-plus fs-3 text-success"></i><strong>Citas</strong><span class="small text-muted">Agenda</span></a></div>
                <div class="col-6"><a class="card card-soft p-3 h-100 text-decoration-none text-dark" href="<?= base_url('pages/entrenador/progreso.php') ?>"><i class="bi bi-graph-up fs-3 text-primary"></i><strong>Progreso</strong><span class="small text-muted">Historial</span></a></div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
