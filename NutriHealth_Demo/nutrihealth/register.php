<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect('dashboard.php');
}

$allowedRoles = ['paciente', 'nutriologo', 'entrenador'];
$rol = $_GET['rol'] ?? '';
$pageTitle = 'Registro';
require __DIR__ . '/includes/header.php';
?>
<section class="container py-5">
    <?php if (!in_array($rol, $allowedRoles, true)): ?>
        <div class="text-center mb-4">
            <h1 class="fw-bold">Selecciona tu tipo de registro</h1>
            <p class="text-muted">Cada actor tiene permisos diferentes dentro del sistema.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($allowedRoles as $item): ?>
                <div class="col-md-4">
                    <a href="<?= base_url('register.php?rol=' . $item) ?>" class="text-decoration-none text-dark">
                        <div class="card card-soft role-card h-100 p-4 text-center">
                            <i class="bi <?= $item === 'paciente' ? 'bi-person-heart text-success' : ($item === 'nutriologo' ? 'bi-clipboard2-pulse text-primary' : 'bi-bicycle text-success') ?> fs-1"></i>
                            <h3 class="h4 fw-bold mt-3"><?= role_label($item) ?></h3>
                            <p class="text-muted mb-0">Crear cuenta como <?= role_label($item) ?>.</p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-soft p-4">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                            <div>
                                <h1 class="h3 fw-bold mb-1">Registro de <?= role_label($rol) ?></h1>
                                <p class="text-muted mb-0">Completa los datos solicitados para crear tu cuenta.</p>
                            </div>
                            <a href="<?= base_url('register.php') ?>" class="btn btn-outline-secondary rounded-pill">Cambiar rol</a>
                        </div>
                        <form method="post" action="<?= base_url('actions/auth_register.php') ?>">
                            <input type="hidden" name="rol" value="<?= e($rol) ?>">
                            <h5 class="fw-bold border-bottom pb-2 mb-3">Datos personales</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Apellido paterno</label>
                                    <input type="text" name="apellido_paterno" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Apellido materno</label>
                                    <input type="text" name="apellido_materno" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha de nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sexo</label>
                                    <select name="sexo" class="form-select">
                                        <option value="">Selecciona...</option>
                                        <option>Masculino</option>
                                        <option>Femenino</option>
                                        <option>Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control">
                                </div>
                            </div>

                            <h5 class="fw-bold border-bottom pb-2 my-4">Datos de acceso</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Usuario *</label>
                                    <input type="text" name="usuario" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contraseña *</label>
                                    <input id="password" type="password" name="password" class="form-control" minlength="6" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Confirmar contraseña *</label>
                                    <input id="password_confirm" type="password" name="password_confirm" class="form-control" minlength="6" required>
                                </div>
                            </div>

                            <?php if ($rol === 'paciente'): ?>
                                <h5 class="fw-bold border-bottom pb-2 my-4">Datos iniciales de salud</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Peso kg *</label>
                                        <input type="number" step="0.01" name="peso" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estatura m *</label>
                                        <input type="number" step="0.01" name="estatura" class="form-control" placeholder="1.70" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Edad *</label>
                                        <input type="number" name="edad" class="form-control" min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nivel de actividad</label>
                                        <select name="nivel_actividad" class="form-select">
                                            <option>Bajo</option>
                                            <option selected>Moderado</option>
                                            <option>Alto</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Enfermedades</label>
                                        <textarea name="enfermedades" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Alergias</label>
                                        <textarea name="alergias" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Medicamentos</label>
                                        <textarea name="medicamentos" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Objetivo de salud</label>
                                        <input type="text" name="objetivo_salud" class="form-control" placeholder="Bajar grasa, ganar músculo, mejorar condición...">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="<?= base_url('login.php') ?>" class="btn btn-outline-secondary rounded-pill px-4">Ya tengo cuenta</a>
                                <button class="btn btn-nh rounded-pill px-4" type="submit">Crear cuenta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
