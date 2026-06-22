<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['paciente']);
$user = current_user();
$nutriologos = get_people_by_role('nutriologo');
$entrenadores = get_people_by_role('entrenador');
$citas = appointments_for_user((int)$user['id'], 'paciente');
$pageTitle = 'Mis citas';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-soft p-4">
                <h1 class="h4 fw-bold mb-3"><i class="bi bi-calendar-plus text-success me-2"></i>Agendar cita</h1>
                <p class="text-muted small">Cada cita dura una hora. El sistema bloquea horarios ocupados del paciente y del especialista.</p>
                <form method="post" action="<?= base_url('actions/citas_crear.php') ?>">
                    <input type="hidden" name="return_to" value="pages/paciente/citas.php">
                    <input type="hidden" name="tipo_especialista" data-type-input>
                    <div class="mb-3">
                        <label class="form-label">Tipo de especialista</label>
                        <select class="form-select" data-role-select required>
                            <option value="nutriologo">Nutriólogo</option>
                            <option value="entrenador">Entrenador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialista disponible</label>
                        <select name="especialista_id" class="form-select" data-specialist-select required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($nutriologos as $n): ?>
                                <option value="<?= (int)$n['id'] ?>" data-role="nutriologo"><?= e(full_name($n)) ?></option>
                            <?php endforeach; ?>
                            <?php foreach ($entrenadores as $e): ?>
                                <option value="<?= (int)$e['id'] ?>" data-role="entrenador"><?= e(full_name($e)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" data-date-input required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horarios disponibles</label>
                        <div data-slots-box class="d-flex flex-wrap gap-1">
                            <div class="text-muted small">Selecciona especialista y fecha para ver horarios disponibles.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea name="motivo" class="form-control" rows="2" placeholder="Consulta de seguimiento, evaluación física, ajuste de dieta..."></textarea>
                    </div>
                    <button class="btn btn-nh rounded-pill w-100" type="submit">Confirmar cita</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-soft p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 fw-bold mb-0"><i class="bi bi-calendar2-week text-primary me-2"></i>Calendario de citas</h2>
                    <span class="badge text-bg-light">Agenda personal</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Fecha</th><th>Hora</th><th>Especialista</th><th>Estado</th><th></th></tr></thead>
                        <tbody>
                        <?php if (!$citas): ?>
                            <tr><td colspan="5" class="text-muted">No tienes citas registradas.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td><?= e($cita['fecha']) ?></td>
                                <td><?= e(substr($cita['hora'], 0, 5)) ?></td>
                                <td>
                                    <strong><?= e($cita['especialista_nombre'] . ' ' . $cita['especialista_apellido']) ?></strong>
                                    <div class="small text-muted"><?= role_label($cita['especialista_rol']) ?></div>
                                </td>
                                <td><span class="badge text-bg-<?= badge_state($cita['estado']) ?>"><?= e($cita['estado']) ?></span></td>
                                <td class="text-end">
                                    <?php if (!in_array($cita['estado'], ['cancelada','finalizada'], true)): ?>
                                        <form method="post" action="<?= base_url('actions/citas_cancelar.php') ?>" onsubmit="return confirm('¿Cancelar esta cita?')">
                                            <input type="hidden" name="id" value="<?= (int)$cita['id'] ?>">
                                            <input type="hidden" name="return_to" value="pages/paciente/citas.php">
                                            <button class="btn btn-sm btn-outline-danger rounded-pill">Cancelar</button>
                                        </form>
                                    <?php endif; ?>
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
