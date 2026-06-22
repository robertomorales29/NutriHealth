<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Inicio';
require __DIR__ . '/includes/header.php';
?>
<section class="container py-5">
    <div class="hero-section shadow-sm p-4 p-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge hero-badge rounded-pill px-3 py-2 mb-3">Salud, nutrición y entrenamiento en un solo lugar</span>
                <h1 class="display-5 fw-bold mb-3">Administra NutriHealth de forma simple, visual e intuitiva.</h1>
                <p class="lead text-muted mb-4">Pacientes, nutriólogos y entrenadores pueden gestionar citas, seguimiento de salud, dietas y rutinas desde un panel centralizado.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= base_url('register.php') ?>" class="btn btn-nh btn-lg rounded-pill px-4">Crear cuenta</a>
                    <a href="<?= base_url('login.php') ?>" class="btn btn-outline-primary btn-lg rounded-pill px-4">Iniciar sesión</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner rounded-5">
                        <div class="carousel-item active">
                            <div class="news-slide d-flex flex-column justify-content-end">
                                <span class="badge bg-light text-success align-self-start mb-3">Nutrición</span>
                                <h3 class="fw-bold">Hábitos saludables para mejorar tu energía diaria</h3>
                                <p>Coloca aquí una imagen o liga de noticia sobre alimentación balanceada.</p>
                                <a class="btn btn-light rounded-pill align-self-start" target="_blank" href="https://www.who.int/news-room/fact-sheets/detail/healthy-diet">Leer noticia</a>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="news-slide slide-two d-flex flex-column justify-content-end">
                                <span class="badge bg-light text-primary align-self-start mb-3">Ejercicio</span>
                                <h3 class="fw-bold">La actividad física ayuda a prevenir enfermedades</h3>
                                <p>Actualiza este enlace con noticias de entrenamiento y salud.</p>
                                <a class="btn btn-light rounded-pill align-self-start" target="_blank" href="https://www.who.int/news-room/fact-sheets/detail/physical-activity">Leer noticia</a>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="news-slide slide-three d-flex flex-column justify-content-end">
                                <span class="badge bg-light text-success align-self-start mb-3">Seguimiento</span>
                                <h3 class="fw-bold">Medir avances facilita tomar mejores decisiones</h3>
                                <p>Usa esta sección para noticias sobre tecnología aplicada a la salud.</p>
                                <a class="btn btn-light rounded-pill align-self-start" target="_blank" href="https://www.paho.org/es/temas/promocion-salud">Leer noticia</a>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container pb-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Elige cómo quieres entrar a NutriHealth</h2>
        <p class="text-muted">El registro está separado por tipo de usuario para mostrar los campos correctos.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-soft role-card h-100 p-3">
                <div class="card-body">
                    <i class="bi bi-person-heart fs-1 text-success"></i>
                    <h4 class="fw-bold mt-3">Paciente</h4>
                    <p class="text-muted">Registra datos personales, datos de salud, agenda citas y consulta dietas o rutinas asignadas.</p>
                    <a href="<?= base_url('register.php?rol=paciente') ?>" class="btn btn-outline-success rounded-pill">Registrarme como paciente</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft role-card h-100 p-3">
                <div class="card-body">
                    <i class="bi bi-clipboard2-pulse fs-1 text-primary"></i>
                    <h4 class="fw-bold mt-3">Nutriólogo</h4>
                    <p class="text-muted">Consulta pacientes, registra avances, administra dietas y agenda citas de seguimiento.</p>
                    <a href="<?= base_url('register.php?rol=nutriologo') ?>" class="btn btn-outline-primary rounded-pill">Registrarme como nutriólogo</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft role-card h-100 p-3">
                <div class="card-body">
                    <i class="bi bi-bicycle fs-1 text-success"></i>
                    <h4 class="fw-bold mt-3">Entrenador</h4>
                    <p class="text-muted">Controla pacientes, revisa progreso, crea rutinas de ejercicio y gestiona citas deportivas.</p>
                    <a href="<?= base_url('register.php?rol=entrenador') ?>" class="btn btn-outline-success rounded-pill">Registrarme como entrenador</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
