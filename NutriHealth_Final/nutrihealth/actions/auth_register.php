<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

$rol = $_POST['rol'] ?? '';
$allowedRoles = ['paciente', 'nutriologo', 'entrenador'];
if (!in_array($rol, $allowedRoles, true)) {
    flash('error', 'Tipo de usuario no válido.');
    redirect('register.php');
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if ($nombre === '' || $email === '' || $usuario === '' || $password === '') {
    flash('error', 'Completa los campos obligatorios.');
    redirect('register.php?rol=' . $rol);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'El correo no tiene un formato válido.');
    redirect('register.php?rol=' . $rol);
}

if (strlen($password) < 6 || $password !== $passwordConfirm) {
    flash('error', 'La contraseña debe tener mínimo 6 caracteres y coincidir con la confirmación.');
    redirect('register.php?rol=' . $rol);
}

try {
    db()->beginTransaction();

    $stmt = db()->prepare('INSERT INTO personas
        (nombre, apellido_paterno, apellido_materno, fecha_nacimiento, sexo, telefono, email, direccion, usuario, password_hash, rol)
        VALUES (:nombre, :apellido_paterno, :apellido_materno, :fecha_nacimiento, :sexo, :telefono, :email, :direccion, :usuario, :password_hash, :rol)');

    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido_paterno' => trim($_POST['apellido_paterno'] ?? '') ?: null,
        ':apellido_materno' => trim($_POST['apellido_materno'] ?? '') ?: null,
        ':fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
        ':sexo' => $_POST['sexo'] ?: null,
        ':telefono' => trim($_POST['telefono'] ?? '') ?: null,
        ':email' => $email,
        ':direccion' => trim($_POST['direccion'] ?? '') ?: null,
        ':usuario' => $usuario,
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ':rol' => $rol,
    ]);

    $personId = (int) db()->lastInsertId();

    if ($rol === 'paciente') {
        $peso = $_POST['peso'] ?? null;
        $estatura = $_POST['estatura'] ?? null;
        $edad = $_POST['edad'] ?? null;

        if (!$peso || !$estatura || !$edad) {
            throw new Exception('El paciente debe capturar peso, estatura y edad.');
        }

        $health = db()->prepare('INSERT INTO datos_salud
            (paciente_id, peso, estatura, edad, enfermedades, alergias, medicamentos, objetivo_salud, nivel_actividad)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $health->execute([
            $personId,
            $peso,
            $estatura,
            $edad,
            trim($_POST['enfermedades'] ?? '') ?: null,
            trim($_POST['alergias'] ?? '') ?: null,
            trim($_POST['medicamentos'] ?? '') ?: null,
            trim($_POST['objetivo_salud'] ?? '') ?: null,
            $_POST['nivel_actividad'] ?? 'Moderado',
        ]);

        $history = db()->prepare('INSERT INTO historial_salud (paciente_id, peso, observaciones) VALUES (?, ?, ?)');
        $history->execute([$personId, $peso, 'Registro inicial del paciente.']);

        $current = db()->prepare('INSERT INTO datos_salud_actuales
            (paciente_id, peso, objetivo_salud, nivel_actividad) VALUES (?, ?, ?, ?)');
        $current->execute([
            $personId,
            $peso,
            trim($_POST['objetivo_salud'] ?? '') ?: null,
            $_POST['nivel_actividad'] ?? 'Moderado',
        ]);
    }

    db()->commit();

    $user = get_person($personId);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'nombre' => $user['nombre'],
        'email' => $user['email'],
        'usuario' => $user['usuario'],
        'rol' => $user['rol'],
    ];

    flash('success', 'Cuenta creada correctamente.');
    redirect('dashboard.php');
} catch (Throwable $e) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }

    if (str_contains($e->getMessage(), 'Duplicate')) {
        flash('error', 'El correo o usuario ya está registrado.');
    } else {
        flash('error', 'No se pudo crear la cuenta: ' . $e->getMessage());
    }
    redirect('register.php?rol=' . $rol);
}
