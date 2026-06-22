<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$person = get_person((int)$user['id']);
$health = latest_health_data((int)$user['id']);
$history = latest_health_history((int)$user['id']);
$peso = $history['peso'] ?? $health['peso'] ?? null;
$imc = calculate_imc($peso ? (float)$peso : null, isset($health['estatura']) ? (float)$health['estatura'] : null);
$calories = estimate_daily_calories($person, $health);
$appointments = upcoming_appointments_for_user((int)$user['id'], 'paciente', 5);
$pageTitle = 'Panel del paciente';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="dashboard-hero p-4 p-lg-5 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="badge badge-role rounded-pill mb-2">Paciente</span>
                <h1 class="fw-bold mb-2">Hola, <?= e($user['nombre']) ?> 👋</h1>
                <p class="text-muted mb-0">Este es tu resumen deportivo y de salud. Desde aquí puedes revisar tu progreso, citas, dieta y rutina.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= base_url('pages/paciente/citas.php') ?>" class="btn btn-nh rounded-pill px-4"><i class="bi bi-calendar-plus me-2"></i>Agendar cita</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle"><div class="stat-content"><div class="stat-number"><?= $imc !== null ? e((string)$imc) : '--' ?></div><div class="stat-label">IMC</div></div></div>
                <h6 class="fw-bold"><?= e(imc_status($imc)) ?></h6>
                <p class="text-muted small mb-0">Índice de masa corporal.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle"><div class="stat-content"><div class="stat-number"><?= $peso ? e((string)$peso) : '--' ?></div><div class="stat-label">kg</div></div></div>
                <h6 class="fw-bold">Peso actual</h6>
                <p class="text-muted small mb-0">Último registro disponible.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle"><div class="stat-content"><div class="stat-number"><?= $calories ? e((string)$calories) : '--' ?></div><div class="stat-label">kcal/día</div></div></div>
                <h6 class="fw-bold">Calorías estimadas</h6>
                <p class="text-muted small mb-0">Estimación por edad, peso, estatura y actividad.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle"><div class="stat-content"><div class="stat-number"><?= e($health['nivel_actividad'] ?? '--') ?></div><div class="stat-label">actividad</div></div></div>
                <h6 class="fw-bold">Nivel deportivo</h6>
                <p class="text-muted small mb-0">Dato de salud inicial.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-calendar2-week text-primary me-2"></i>Próximas citas</h5>
                        <a href="<?= base_url('pages/paciente/citas.php') ?>" class="small">Ver agenda</a>
                    </div>
                    <?php if (!$appointments): ?>
                        <p class="text-muted mb-0">Aún no tienes citas próximas.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($appointments as $cita): ?>
                                <div class="list-group-item bg-transparent px-0 d-flex justify-content-between gap-3">
                                    <div>
                                        <strong><?= e($cita['especialista_nombre'] . ' ' . $cita['especialista_apellido']) ?></strong>
                                        <div class="text-muted small"><?= role_label($cita['especialista_rol']) ?> · <?= e($cita['motivo']) ?></div>
                                    </div>
                                    <div class="text-end small">
                                        <span class="fw-bold"><?= e($cita['fecha']) ?></span><br>
                                        <?= e(substr($cita['hora'], 0, 5)) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6"><a href="<?= base_url('pages/paciente/progreso.php') ?>" class="card card-soft text-decoration-none text-dark p-3 h-100"><i class="bi bi-graph-up-arrow fs-3 text-success"></i><strong class="mt-2">Progreso</strong><span class="text-muted small">Historial de salud</span></a></div>
                <div class="col-6"><a href="<?= base_url('pages/paciente/dieta.php') ?>" class="card card-soft text-decoration-none text-dark p-3 h-100"><i class="bi bi-egg-fried fs-3 text-primary"></i><strong class="mt-2">Mi dieta</strong><span class="text-muted small">Plan asignado</span></a></div>
                <div class="col-6"><a href="<?= base_url('pages/paciente/rutina.php') ?>" class="card card-soft text-decoration-none text-dark p-3 h-100"><i class="bi bi-activity fs-3 text-success"></i><strong class="mt-2">Mi rutina</strong><span class="text-muted small">Ejercicios</span></a></div>
                <div class="col-6"><a href="<?= base_url('pages/paciente/citas.php') ?>" class="card card-soft text-decoration-none text-dark p-3 h-100"><i class="bi bi-calendar-check fs-3 text-primary"></i><strong class="mt-2">Citas</strong><span class="text-muted small">Agenda</span></a></div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
