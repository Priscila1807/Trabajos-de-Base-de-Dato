<?php

include 'config/conexion.php';

$mensaje_error = "";
$mensaje_exito = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $id_rol   = 2; // Rol por defecto (Preceptora)

    if (!empty($nombre) && !empty($apellido) && !empty($email) && !empty($password)) {
        // Verificar si el correo ya existe en la base de datos
        $checkEmail = $conn->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $mensaje_error = "El correo electrónico ya está registrado.";
        } else {
            // Insertar usuario con la contraseña directa
            $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido, email, password_hash, id_rol, activo) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssi", $nombre, $apellido, $email, $password, $id_rol);

            if ($stmt->execute()) {
                $mensaje_exito = "¡Cuenta creada con éxito! Ya podés iniciar sesión a la izquierda.";
            } else {
                $mensaje_error = "Error al registrar el usuario en la base de datos.";
            }
            $stmt->close();
        }
        $checkEmail->close();
    } else {
        $mensaje_error = "Por favor, completá todos los campos de registro.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("
            SELECT u.id_usuario, u.nombre, u.apellido, u.password_hash, u.activo, r.nombre AS rol_nombre 
            FROM usuario u 
            JOIN rol r ON u.id_rol = r.id_rol 
            WHERE u.email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (!$user['activo']) {
                $mensaje_error = "Esta cuenta se encuentra desactivada.";
            } else if ($password === $user['password_hash']) {
                session_start();
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nombre_completo'] = $user['nombre'] . ' ' . $user['apellido'];
                $_SESSION['rol'] = $user['rol_nombre'];

                // Redirigir al panel de preceptoría
                header("Location: panel_Prece/panel_prece.php");
                exit();
            } else {
                $mensaje_error = "Contraseña incorrecta.";
            }
        } else {
            $mensaje_error = "No existe una cuenta registrada con este correo.";
        }
        $stmt->close();
    } else {
        $mensaje_error = "Por favor, ingresá tu correo y contraseña para entrar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso y Registro - CEN</title>
    <!-- Fuente Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Iconos FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --marron-oscuro: #4A3525; 
            --marron-cabecera: #3D2B1E;
            --marron-fondo-pagina: #EAE3D9; 
            --puntos-fondo: #D5CCC0; 
            --dorado: #E5A83B;  
            --dorado-hover: #D4962A;
            --blanco: #ffffff;
            --gris-texto: #71717a;
            --gris-borde: #e2e8f0;
            --gris-input: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--marron-fondo-pagina);
            background-image: radial-gradient(var(--puntos-fondo) 2px, transparent 2px);
            background-size: 20px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .main-wrapper {
            width: 100%;
            max-width: 950px;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 15px;
            text-align: center;
        }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #4ade80; }

        .main-container {
            background-color: var(--blanco);
            width: 100%;
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .login-section, .register-section {
            width: 50%;
            background-color: var(--blanco);
            display: flex;
            flex-direction: column;
        }

        .login-section {
            border-right: 1px solid #eee;
        }

        .card-header {
            background-color: var(--marron-oscuro);
            height: 160px;
            padding: 20px 15px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 0 0 50% 50% / 0 0 20px 20px;
        }

        .profile-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid var(--dorado);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background-color: #2c1e17;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-photo .placeholder-icon {
            font-size: 2rem;
            color: var(--dorado);
        }

        .header-title {
            color: var(--blanco);
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .header-subtitle {
            color: var(--dorado);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .card-body {
            padding: 25px 35px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .section-title {
            text-align: center;
            margin-bottom: 18px;
        }

        .section-title h2 {
            font-size: 1.3rem;
            color: var(--marron-oscuro);
            font-weight: 700;
            margin-bottom: 3px;
        }

        .section-title p {
            color: var(--gris-texto);
            font-size: 0.82rem;
        }

        .custom-form {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .input-group {
            position: relative;
            margin-bottom: 12px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 38px 12px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.88rem;
            color: #333;
            background-color: var(--gris-input);
            outline: none;
            transition: all 0.25s ease;
            font-weight: 500;
        }

        .input-group input:focus {
            border-color: var(--dorado);
            background-color: var(--blanco);
            box-shadow: 0 0 0 3px rgba(229, 168, 59, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 4px;
        }

        .forgot-container {
            min-height: 20px;
            text-align: right;
            margin-bottom: 10px;
        }

        .forgot-link {
            font-size: 0.78rem;
            color: var(--gris-texto);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            color: var(--dorado-hover);
            text-decoration: underline;
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background-color: var(--dorado);
            color: var(--blanco);
            border: none;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(229, 168, 59, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: auto;
        }

        .btn-primary:hover {
            background-color: var(--dorado-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(229, 168, 59, 0.45);
        }

        @media (max-width: 850px) {
            .main-container {
                flex-direction: column;
                max-width: 450px;
            }
            .login-section, .register-section {
                width: 100%;
                border-right: none;
            }
            .login-section {
                border-bottom: 2px solid #eee;
            }
        }
    </style>
</head>
<body>

    <div class="main-wrapper">
        <!-- Alertas dinámicas -->
        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <div class="main-container">
            
            <div class="login-section">
                <div class="card-header">
                    <div class="profile-photo">
                        <img src="imagen/images.png" alt="Logo CEN" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fa-solid fa-graduation-cap placeholder-icon" style="display: none;"></i>
                    </div>
                    <h1 class="header-title">Centro Educativo Nonogasta</h1>
                    <p class="header-subtitle">Módulo de Inscripción</p>
                </div>

                <div class="card-body">
                    <div class="section-title">
                        <h2>Iniciar sesión</h2>
                        <p>Usá tu cuenta institucional para continuar.</p>
                    </div>

                    <form action="loginInicio.php" method="POST" class="custom-form">
                        <input type="hidden" name="action" value="login">

                        <div class="input-group">
                            <input type="email" id="email" name="email" placeholder="Gmail institucional" required>
                            <i class="fa-regular fa-envelope input-icon"></i>
                        </div>

                        <div class="input-group">
                            <input type="password" id="password" name="password" placeholder="Contraseña" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePassword('password', 'eye-icon-login')">
                                <i class="fa-regular fa-eye" id="eye-icon-login"></i>
                            </button>
                        </div>

                        <div class="forgot-container">
                            <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                        </div>

                        <button type="submit" class="btn-primary">
                            Entrar <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="register-section">
                <div class="card-header">
                    <h1 class="header-title" style="font-size: 1.4rem;">Crear Cuenta</h1>
                    <p class="header-subtitle">Nuevo Usuario</p>
                </div>

                <div class="card-body">
                    <div class="section-title">
                        <h2>Registrate</h2>
                        <p>Completá tus datos para solicitar acceso.</p>
                    </div>

                    <form action="loginInicio.php" method="POST" class="custom-form">
                        <input type="hidden" name="action" value="register">

                        <div class="input-group">
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
                            <i class="fa-regular fa-user input-icon"></i>
                        </div>

                        <div class="input-group">
                            <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>
                            <i class="fa-regular fa-id-card input-icon"></i>
                        </div>

                        <div class="input-group">
                            <input type="email" id="email_reg" name="email" placeholder="Gmail institucional" required>
                            <i class="fa-regular fa-envelope input-icon"></i>
                        </div>

                        <div class="input-group">
                            <input type="password" id="password_reg" name="password" placeholder="Crear contraseña" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                            <button type="button" class="toggle-password" onclick="togglePassword('password_reg', 'eye-icon-reg')">
                                <i class="fa-regular fa-eye" id="eye-icon-reg"></i>
                            </button>
                        </div>

                        <div class="forgot-container"></div>

                        <button type="submit" class="btn-primary">
                            Registrarme <i class="fa-solid fa-user-plus"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>