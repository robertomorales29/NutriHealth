<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect('dashboard.php');
}
$pageTitle = 'Iniciar sesión';
require __DIR__ . '/includes/header.php';
?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card card-soft p-4">
                <div class="card-body">
                    <h1 class="h3 fw-bold mb-2">Iniciar sesión</h1>
                    <p class="text-muted mb-4">Entra con tu usuario o correo electrónico.</p>
                    <form method="post" action="<?= base_url('actions/auth_login.php') ?>" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Usuario o email</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-nh w-100 rounded-pill py-2" type="submit">Entrar</button>
                    </form>
                    <div class="text-center mt-4">
                        <span class="text-muted">¿No tienes cuenta?</span>
                        <a href="<?= base_url('register.php') ?>">Regístrate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
