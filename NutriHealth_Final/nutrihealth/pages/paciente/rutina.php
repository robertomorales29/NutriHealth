<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$stmt = db()->prepare("SELECT rp.*, r.nombre, r.descripcion, p.nombre AS entrenador_nombre, p.apellido_paterno AS entrenador_apellido
    FROM rutinas_pacientes rp
    INNER JOIN rutinas r ON r.id = rp.rutina_id
    INNER JOIN personas p ON p.id = r.entrenador_id
    WHERE rp.paciente_id = ?
    ORDER BY rp.estado = 'activa' DESC, rp.fecha_inicio DESC");
$stmt->execute([$user['id']]);
$rutinas = $stmt->fetchAll();
$pageTitle = 'Mi rutina';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="card card-soft p-4">
        <h1 class="h4 fw-bold mb-3"><i class="bi bi-activity text-primary me-2"></i>Rutinas asignadas</h1>
        <?php if (!$rutinas): ?>
            <p class="text-muted mb-0">Aún no tienes una rutina asignada.</p>
        <?php else: ?>
            <div class="accordion" id="rutinasAccordion">
                <?php foreach ($rutinas as $index => $r): ?>
                    <?php
                    $ex = db()->prepare('SELECT * FROM rutina_ejercicios WHERE rutina_id = ? ORDER BY FIELD(dia_semana, "lunes","martes","miercoles","jueves","viernes","sabado","domingo"), id');
                    $ex->execute([$r['rutina_id']]);
                    $ejercicios = $ex->fetchAll();
                    ?>
                    <div class="accordion-item border-0 mb-3 rounded-4 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rutina<?= (int)$r['id'] ?>">
                                <strong><?= e($r['nombre']) ?></strong>&nbsp; <span class="badge text-bg-<?= $r['estado'] === 'activa' ? 'success' : 'secondary' ?> ms-2"><?= e($r['estado']) ?></span>
                            </button>
                        </h2>
                        <div id="rutina<?= (int)$r['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#rutinasAccordion">
                            <div class="accordion-body bg-light">
                                <p><?= nl2br(e($r['descripcion'])) ?></p>
                                <div class="small text-muted mb-3">Entrenador: <?= e($r['entrenador_nombre'] . ' ' . $r['entrenador_apellido']) ?> · Inicio <?= e($r['fecha_inicio']) ?></div>
                                <?php if (!$ejercicios): ?>
                                    <p class="text-muted">Esta rutina aún no tiene ejercicios capturados.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle">
                                            <thead><tr><th>Día</th><th>Ejercicio</th><th>Series</th><th>Reps</th><th>Duración</th><th>Descanso</th><th>Video</th></tr></thead>
                                            <tbody>
                                                <?php foreach ($ejercicios as $ejer): ?>
                                                    <tr>
                                                        <td><?= e($ejer['dia_semana']) ?></td>
                                                        <td><?= e($ejer['nombre_ejercicio']) ?><div class="small text-muted"><?= e($ejer['descripcion']) ?></div></td>
                                                        <td><?= $ejer['series'] !== null ? e((string)$ejer['series']) : '—' ?></td>
                                                        <td><?= $ejer['repeticiones'] !== null ? e((string)$ejer['repeticiones']) : '—' ?></td>
                                                        <td><?= $ejer['duracion_minutos'] !== null ? e((string)$ejer['duracion_minutos']) . ' min' : '—' ?></td>
                                                        <td><?= $ejer['descanso_segundos'] !== null ? e((string)$ejer['descanso_segundos']) . ' s' : '—' ?></td>
                                                        <td>
                                                            <?php $videoUrl = safe_external_url($ejer['link_video'] ?? null); ?>
                                                            <?php if ($videoUrl): ?>
                                                                <a class="btn btn-sm btn-outline-primary rounded-pill text-nowrap" target="_blank" rel="noopener noreferrer" href="<?= e($videoUrl) ?>"><i class="bi bi-play-circle me-1"></i>Ver video</a>
                                                            <?php else: ?>
                                                                <span class="text-muted">Sin video</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
