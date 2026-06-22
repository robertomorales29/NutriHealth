<?php
require_once __DIR__ . '/../../includes/functions.php';
require_role(['nutriologo']);
$user = current_user();
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM dietas WHERE id = ? AND nutriologo_id = ?');
$stmt->execute([$id, $user['id']]);
$dieta = $stmt->fetch();
if (!$dieta) {
    flash('error', 'Dieta no encontrada o sin permisos.');
    redirect('pages/nutriologo/dietas.php');
}
$pageTitle = 'Editar dieta';
require __DIR__ . '/../../includes/header.php';
?>
<section class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-soft p-4">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Editar dieta</h1>
                        <p class="text-muted mb-0">Esta pantalla está separada para modificar la dieta sin perder la lista principal.</p>
                    </div>
                    <a class="btn btn-outline-secondary rounded-pill" href="<?= base_url('pages/nutriologo/dietas.php') ?>">Volver</a>
                </div>
                <form method="post" action="<?= base_url('actions/dieta_guardar.php') ?>">
                    <input type="hidden" name="id" value="<?= (int)$dieta['id'] ?>">
                    <input type="hidden" name="return_to" value="pages/nutriologo/dieta_editar.php?id=<?= (int)$dieta['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input name="nombre" class="form-control" value="<?= e($dieta['nombre']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="6" placeholder="Objetivo de la dieta, enfoque nutricional, tipo de alimentación...">
<?= e($dieta['descripcion'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Indicaciones</label>
                        <textarea name="indicaciones" class="form-control" rows="6" placeholder="Horarios, restricciones, preparación, recomendaciones para el paciente...">
<?= e($dieta['indicaciones'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-nh rounded-pill">Guardar cambios</button>
                    <a class="btn btn-outline-secondary rounded-pill" href="<?= base_url('pages/nutriologo/dietas.php') ?>">Cerrar edición</a>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
