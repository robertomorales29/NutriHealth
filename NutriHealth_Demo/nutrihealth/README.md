# NutriHealth - Versión Demo
Esta carpeta corresponde a una versión demo. No incluye las últimas mejoras incorporadas en la versión final.


# NutriHealth - Proyecto PHP + XAMPP + MySQL
Proyecto base funcional para administrar un sitio de salud con tres actores:
- Paciente
- Nutriólogo
- Entrenador

Usa la base de datos del archivo `sql/tablas.sql`, Bootstrap 5 por CDN, PHP con PDO y MySQL/phpMyAdmin.

## Instalación en XAMPP
1. Copiar la carpeta `nutrihealth` dentro de:
```txt
C:\xampp\htdocs\
```

La ruta final debe quedar:
```txt
C:\xampp\htdocs\nutrihealth\
```

2. Abrir XAMPP y activa:
- Apache
- MySQL

3. Entrar a phpMyAdmin:
```txt
http://localhost/phpmyadmin
```

4. Importa el archivo:
```txt
nutrihealth/sql/tablas.sql
```

Ese script crea la base de datos `sitio_salud` y todas las tablas del proyecto.

5. Revisar la conexión en:
```txt
config/database.php
```

6. Abrrir el sitio:
```txt
http://localhost/nutrihealth/
```

## Funcionalidades implementadas

### Inicio
- Página inicial con diseño Bootstrap.
- Slider de noticias con links externos.
- Botones para iniciar sesión y registrarse.
- Registro separado por Paciente, Nutriólogo y Entrenador.

### Registro e inicio de sesión
- Registro en tabla `personas`.
- Password con `password_hash`.
- Login con usuario o email.
- Redirección automática al panel correspondiente según el rol.

### Paciente
- Registro completo en `personas` y `datos_salud`.
- Creación automática del primer registro en `historial_salud`.
- Dashboard con círculos de salud: IMC, peso, calorías estimadas y nivel de actividad.
- Agenda de citas con nutriólogos o entrenadores.
- Ver horarios disponibles por especialista y fecha.
- Ver y cancelar citas.
- Ver dieta asignada.
- Ver rutina asignada.
- Registrar avances en historial de salud.

### Nutriólogo
- Registro en `personas`.
- Dashboard de pacientes, citas y dietas.
- Asignar pacientes.
- Consultar historial de salud de pacientes.
- Registrar nuevos avances de pacientes.
- Crear, modificar y eliminar dietas.
- Asignar dietas a pacientes.
- Agendar y cancelar citas con pacientes.

### Entrenador
- Registro en `personas`.
- Dashboard deportivo.
- Asignar pacientes.
- Consultar historial de salud y progreso.
- Registrar avances físicos.
- Crear, modificar y eliminar rutinas.
- Agregar ejercicios a rutinas.
- Asignar rutinas a pacientes.
- Consultar dietas asignadas a sus pacientes.
- Agendar y cancelar citas con pacientes.


## Estructura principal

```txt
nutrihealth/
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── config/
│   ├── app.php
│   └── database.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   ├── navbar.php
│   ├── flash.php
│   └── footer.php
├── actions/
├── api/
├── pages/
│   ├── paciente/
│   ├── nutriologo/
│   └── entrenador/
├── assets/
│   ├── css/
│   └── js/
├── sql/
│   └── tablas.sql
└── uploads/
```