<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['entrenador']);
$user = current_user();
$patients = assigned_patients((int)$user['id'], 'entrenador');
if (!$patients) $patients = get_people_by_role('paciente');
$pacienteId = (int)($_GET['paciente_id'] ?? ($patients[0]['id'] ?? 0));
$patient = $pacienteId ? get_person($pacienteId) : null;
$health = $patient ? latest_health_data($pacienteId) : null;
$historial = [];
if ($patient) { $stmt = db()->prepare('SELECT * FROM historial_salud WHERE paciente_id = ? ORDER BY fecha_registro DESC'); $stmt->execute([$pacienteId]); $historial = $stmt->fetchAll(); }
$pageTitle = 'Progreso deportivo';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft p-4 mb-4"><h1 class="h4 fw-bold mb-3">Seleccionar paciente</h1><form method="get"><select name="paciente_id" class="form-select mb-3" onchange="this.form.submit()"><?php foreach ($patients as $p): ?><option value="<?= (int)$p['id'] ?>" <?= $pacienteId === (int)$p['id'] ? 'selected' : '' ?>><?= e(full_name($p)) ?></option><?php endforeach; ?></select><button class="btn btn-outline-primary rounded-pill w-100">Consultar</button></form></div>
            <?php if ($patient): ?><div class="card card-soft p-4"><h2 class="h5 fw-bold mb-3">Registrar avance físico</h2><form method="post" action="<?= base_url('actions/historial_guardar.php') ?>"><input type="hidden" name="return_to" value="pages/entrenador/progreso.php?paciente_id=<?= (int)$pacienteId ?>"><input type="hidden" name="paciente_id" value="<?= (int)$pacienteId ?>"><div class="mb-3"><label class="form-label">Peso kg *</label><input type="number" step="0.01" name="peso" class="form-control" required></div><div class="mb-3"><label class="form-label">% grasa</label><input type="number" step="0.01" name="porcentaje_grasa" class="form-control"></div><div class="mb-3"><label class="form-label">Masa muscular</label><input type="number" step="0.01" name="masa_muscular" class="form-control"></div><div class="mb-3"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="3"></textarea></div><button class="btn btn-nh rounded-pill w-100">Guardar avance</button></form></div><?php endif; ?>
        </div>
        <div class="col-lg-8"><div class="card card-soft p-4"><h2 class="h4 fw-bold mb-3">Historial <?= $patient ? 'de ' . e(full_name($patient)) : '' ?></h2><?php if ($health): ?><div class="alert alert-info">Objetivo: <?= e($health['objetivo_salud']) ?> · Actividad: <?= e($health['nivel_actividad']) ?> · Enfermedades: <?= e($health['enfermedades']) ?></div><?php endif; ?><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Fecha</th><th>Peso</th><th>% grasa</th><th>Masa muscular</th><th>Observaciones</th></tr></thead><tbody><?php if (!$historial): ?><tr><td colspan="5" class="text-muted">Sin historial.</td></tr><?php endif; ?><?php foreach ($historial as $h): ?><tr><td><?= e($h['fecha_registro']) ?></td><td><?= e($h['peso']) ?></td><td><?= e($h['porcentaje_grasa']) ?></td><td><?= e($h['masa_muscular']) ?></td><td><?= e($h['observaciones']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
