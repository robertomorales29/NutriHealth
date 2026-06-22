<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['entrenador']);
$user = current_user();
$patients = assigned_patients((int)$user['id'], 'entrenador');
if (!$patients) $patients = get_people_by_role('paciente');
$citas = appointments_for_user((int)$user['id'], 'entrenador');
$estados = appointment_statuses();
$pageTitle = 'Citas entrenador';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-soft p-4">
                <h1 class="h4 fw-bold mb-3">Agendar cita deportiva</h1>
                <form method="post" action="<?= base_url('actions/citas_crear.php') ?>">
                    <input type="hidden" name="return_to" value="pages/entrenador/citas.php">
                    <input type="hidden" name="especialista_id" value="<?= (int)$user['id'] ?>" data-specialist-select>
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="paciente_id" class="form-select" data-patient-select required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($patients as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e(full_name($p)) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Fecha</label><input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" data-date-input required></div>
                    <div class="mb-3"><label class="form-label">Horarios disponibles</label><div data-slots-box><span class="text-muted small">Selecciona paciente y fecha.</span></div></div>
                    <div class="mb-3"><label class="form-label">Motivo</label><textarea name="motivo" class="form-control" rows="2"></textarea></div>
                    <button class="btn btn-nh rounded-pill w-100">Agendar</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-soft p-4">
                <h2 class="h4 fw-bold mb-3">Mi calendario</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Estado</th><th>Actualizar</th></tr></thead>
                        <tbody>
                        <?php if (!$citas): ?><tr><td colspan="5" class="text-muted">Sin citas.</td></tr><?php endif; ?>
                        <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td><?= e($cita['fecha']) ?></td>
                                <td><?= e(substr($cita['hora'],0,5)) ?></td>
                                <td>
                                    <strong><?= e($cita['paciente_nombre'].' '.$cita['paciente_apellido']) ?></strong>
                                    <div class="small text-muted"><?= e($cita['motivo']) ?></div>
                                </td>
                                <td><span class="badge text-bg-<?= badge_state($cita['estado']) ?>"><?= e($cita['estado']) ?></span></td>
                                <td>
                                    <form method="post" action="<?= base_url('actions/citas_estado.php') ?>" class="d-flex gap-2 align-items-center">
                                        <input type="hidden" name="id" value="<?= (int)$cita['id'] ?>">
                                        <input type="hidden" name="return_to" value="pages/entrenador/citas.php">
                                        <select name="estado" class="form-select form-select-sm">
                                            <?php foreach ($estados as $estado): ?>
                                                <option value="<?= e($estado) ?>" <?= $cita['estado'] === $estado ? 'selected' : '' ?>><?= e(ucfirst($estado)) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill">Guardar</button>
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
