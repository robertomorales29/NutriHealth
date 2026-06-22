<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$person = get_person((int)$user['id']);
$health = latest_health_data((int)$user['id']);
$currentHealth = latest_current_health_data((int)$user['id']);
$peso = $currentHealth['peso'] ?? $health['peso'] ?? null;
$imc = calculate_imc($peso ? (float)$peso : null, isset($health['estatura']) ? (float)$health['estatura'] : null);
$healthForMetrics = $health ?: [];
$healthForMetrics['peso'] = $peso;
$healthForMetrics['nivel_actividad'] = $currentHealth['nivel_actividad'] ?? $health['nivel_actividad'] ?? 'Moderado';
$calories = estimate_daily_calories($person, $healthForMetrics);

// Configuración visual dinámica de los cuatro indicadores circulares.
$clampPercentage = static fn(float $value): int => (int) round(max(0, min(100, $value)));
$activityLevel = $currentHealth['nivel_actividad'] ?? $health['nivel_actividad'] ?? null;
$height = isset($health['estatura']) && is_numeric($health['estatura']) ? (float) $health['estatura'] : null;

$imcProgress = $imc !== null ? $clampPercentage(($imc / 40) * 100) : 0;
$imcTone = match (true) {
    $imc === null => 'muted',
    $imc < 18.5 => 'info',
    $imc < 25 => 'success',
    $imc < 30 => 'warning',
    default => 'danger',
};

$healthyMinWeight = $height && $height > 0 ? round(18.5 * $height * $height, 1) : null;
$healthyMaxWeight = $height && $height > 0 ? round(24.9 * $height * $height, 1) : null;
$weightProgress = $peso && $healthyMaxWeight
    ? $clampPercentage(((float) $peso / $healthyMaxWeight) * 100)
    : ($peso ? $clampPercentage(((float) $peso / 150) * 100) : 0);
$weightTone = $imcTone;

// La escala de calorías es visual (0 a 3500 kcal/día), no una calificación médica.
$caloriesProgress = $calories ? $clampPercentage(($calories / 3500) * 100) : 0;
$caloriesTone = match (true) {
    $calories === null => 'muted',
    $calories < 1600 => 'info',
    $calories <= 2400 => 'success',
    $calories <= 3000 => 'warning',
    default => 'accent',
};

$activityProgress = match ($activityLevel) {
    'Bajo' => 33,
    'Moderado' => 66,
    'Alto' => 100,
    default => 0,
};
$activityTone = match ($activityLevel) {
    'Bajo' => 'info',
    'Moderado' => 'warning',
    'Alto' => 'success',
    default => 'muted',
};

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
                <div class="stat-circle stat-circle--<?= e($imcTone) ?>"
                     style="--circle-progress: <?= $imcProgress ?>%;"
                     role="progressbar" aria-label="IMC en escala visual" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $imcProgress ?>">
                    <div class="stat-content">
                        <div class="stat-number"><?= $imc !== null ? e((string)$imc) : '--' ?></div>
                        <div class="stat-label">IMC</div>
                        <div class="stat-scale"><?= $imcProgress ?>% escala</div>
                    </div>
                </div>
                <h6 class="fw-bold"><?= e(imc_status($imc)) ?></h6>
                <p class="text-muted small mb-0">El color cambia según la clasificación del IMC.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle stat-circle--<?= e($weightTone) ?>"
                     style="--circle-progress: <?= $weightProgress ?>%;"
                     role="progressbar" aria-label="Peso respecto a la referencia saludable" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $weightProgress ?>">
                    <div class="stat-content">
                        <div class="stat-number"><?= $peso ? e((string)$peso) : '--' ?></div>
                        <div class="stat-label">kg</div>
                        <div class="stat-scale"><?= $weightProgress ?>% referencia</div>
                    </div>
                </div>
                <h6 class="fw-bold">Peso actual</h6>
                <?php if ($healthyMinWeight !== null && $healthyMaxWeight !== null): ?>
                    <p class="text-muted small mb-0">Referencia por estatura: <?= e((string)$healthyMinWeight) ?>–<?= e((string)$healthyMaxWeight) ?> kg.</p>
                <?php else: ?>
                    <p class="text-muted small mb-0">Último registro disponible.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle stat-circle--<?= e($caloriesTone) ?>"
                     style="--circle-progress: <?= $caloriesProgress ?>%;"
                     role="progressbar" aria-label="Calorías estimadas en escala visual" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $caloriesProgress ?>">
                    <div class="stat-content">
                        <div class="stat-number"><?= $calories ? e((string)$calories) : '--' ?></div>
                        <div class="stat-label">kcal/día</div>
                        <div class="stat-scale"><?= $caloriesProgress ?>% escala</div>
                    </div>
                </div>
                <h6 class="fw-bold">Calorías estimadas</h6>
                <p class="text-muted small mb-0">Llenado calculado sobre una escala visual de 3500 kcal.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft h-100 text-center p-3">
                <div class="stat-circle stat-circle--<?= e($activityTone) ?>"
                     style="--circle-progress: <?= $activityProgress ?>%;"
                     role="progressbar" aria-label="Nivel de actividad" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $activityProgress ?>">
                    <div class="stat-content">
                        <div class="stat-number stat-number--text"><?= e($activityLevel ?? '--') ?></div>
                        <div class="stat-label">actividad</div>
                        <div class="stat-scale"><?= $activityProgress ?>% nivel</div>
                    </div>
                </div>
                <h6 class="fw-bold">Nivel deportivo</h6>
                <p class="text-muted small mb-0">Bajo 33%, moderado 66% y alto 100%.</p>
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
