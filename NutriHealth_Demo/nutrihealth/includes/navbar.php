<?php $user = current_user(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-success" href="<?= base_url('index.php') ?>">
            <span class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></span>
            NutriHealth
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php') ?>">Inicio</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard.php') ?>">Panel</a></li>
                    <?php if ($user['rol'] === 'paciente'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/paciente/citas.php') ?>">Citas</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/paciente/progreso.php') ?>">Progreso</a></li>
                    <?php elseif ($user['rol'] === 'nutriologo'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/nutriologo/pacientes.php') ?>">Pacientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/nutriologo/dietas.php') ?>">Dietas</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/entrenador/pacientes.php') ?>">Pacientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/entrenador/rutinas.php') ?>">Rutinas</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('pages/entrenador/dietas.php') ?>">Dietas</a></li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= e($user['nombre']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted"><?= role_label($user['rol']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('actions/logout.php') ?>">Cerrar sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login.php') ?>">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="btn btn-success rounded-pill px-4" href="<?= base_url('register.php') ?>">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
