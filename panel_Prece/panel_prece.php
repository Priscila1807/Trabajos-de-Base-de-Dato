<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../loginInicio.php");
    exit();
}

include '../config/conexion.php';

$id_usuario = $_SESSION['id_usuario'];


$stmt = $conn->prepare("
    SELECT u.nombre, u.apellido, r.nombre AS rol_nombre 
    FROM usuario u 
    JOIN rol r ON u.id_rol = r.id_rol 
    WHERE u.id_usuario = ?
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($datos_usuario = $resultado->fetch_assoc()) {
    $nombre_usuario = $datos_usuario['nombre'];
    $apellido_usuario = $datos_usuario['apellido'];
    $rol_usuario = $datos_usuario['rol_nombre'];
} else {
    $nombre_usuario = "Silvana";
    $apellido_usuario = "Castro";
    $rol_usuario = "Preceptora";
}
$stmt->close();


function obtenerIniciales($nombre) {
    return mb_strtoupper(mb_substr($nombre, 0, 1));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEN - Panel de Preces</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --marron-oscuro: #4A3525; 
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
            flex-direction: column;
            color: #333;
        }

    
        .top-navbar {
            background-color: var(--marron-oscuro);
            color: var(--blanco);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            width: 100%;
        }

        .nav-brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-brand-sub {
            font-size: 0.75rem;
            color: #d1c7bd;
            font-weight: 400;
            margin-top: 2px;
        }

        .nav-badges {
            display: flex;
            gap: 10px;
        }

        .badge-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Contenedor Principal Expandido */
        .main-layout {
            max-width: 95%; /* Ocupa hasta el 95% de la pantalla */
            width: 100%;
            margin: 25px auto;
            padding: 0 15px;
            display: flex;
            gap: 25px;
            flex-grow: 1;
        }

        
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            background-color: var(--blanco);
            border-radius: 20px;
            padding: 25px 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            height: fit-content;
            border: 1px solid rgba(255,255,255,0.8);
        }

        .user-profile-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gris-borde);
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            background-color: var(--dorado);
            color: var(--blanco);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(229, 168, 59, 0.3);
        }

        .user-details h4 {
            font-size: 0.95rem;
            color: var(--marron-oscuro);
            font-weight: 700;
        }

        .user-details span {
            font-size: 0.75rem;
            color: var(--gris-texto);
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 30px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gris-texto);
            transition: all 0.2s ease;
        }

        .nav-item.active {
            background-color: var(--dorado);
            color: var(--blanco);
            box-shadow: 0 4px 12px rgba(229, 168, 59, 0.3);
        }

        .nav-item:hover:not(.active) {
            background-color: var(--gris-input);
            color: var(--marron-oscuro);
        }

        .sidebar-footer-info {
            font-size: 0.72rem;
            color: #94a3b8;
            line-height: 1.4;
            padding-top: 10px;
            border-top: 1px solid var(--gris-borde);
        }

        .btn-logout-sidebar {
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #ffeeec;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 11px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-logout-sidebar:hover {
            background-color: #fee2e2;
        }

    
        .content-area {
            flex-grow: 1;
            background-color: var(--blanco);
            border-radius: 20px;
            padding: 35px 40px; /* Mayor espacio interior */
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border: 1px solid rgba(255,255,255,0.8);
            display: flex;
            flex-direction: column;
            min-height: 80vh; /* Altura más amplia para cubrir la pantalla */
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gris-borde);
            margin-bottom: 25px;
        }

        .content-header h3 {
            font-size: 1.45rem;
            color: var(--marron-oscuro);
            font-weight: 700;
        }

        .content-header p {
            font-size: 0.85rem;
            color: var(--gris-texto);
            margin-top: 2px;
        }

        .sync-badge {
            background-color: var(--gris-input);
            border: 1px solid var(--gris-borde);
            color: var(--gris-texto);
            font-size: 0.78rem;
            padding: 6px 14px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

    
        .welcome-banner {
            background: linear-gradient(135deg, #fcfbfa 0%, #f7f4f0 100%);
            border: 1px solid #f0e9e1;
            border-left: 6px solid var(--dorado);
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 30px;
        }

        .welcome-banner h4 {
            color: var(--marron-oscuro);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .welcome-banner p {
            color: var(--gris-texto);
            font-size: 0.92rem;
            line-height: 1.6;
        }

    
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }

        .module-card {
            background-color: var(--gris-input);
            border: 1px solid var(--gris-borde);
            border-radius: 16px;
            padding: 24px;
            transition: transform 0.25s, box-shadow 0.25s;
        }

        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            background-color: var(--blanco);
            border-color: rgba(229, 168, 59, 0.4);
        }

        .module-card h5 {
            color: var(--marron-oscuro);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .module-card h5 i {
            color: var(--dorado);
            font-size: 1.1rem;
        }

        .module-card p {
            color: var(--gris-texto);
            font-size: 0.82rem;
            line-height: 1.5;
        }

       
        
        @media (max-width: 1024px) {
            .main-layout {
                max-width: 98%;
                gap: 18px;
            }
            .sidebar {
                width: 240px;
            }
            .content-area {
                padding: 25px 30px;
            }
        }

    
        @media (max-width: 820px) {
            .top-navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .main-layout {
                flex-direction: column;
                max-width: 100%;
                margin: 15px auto;
            }
            .sidebar {
                width: 100%;
            }
            .content-area {
                padding: 20px;
                min-height: auto;
            }
            .modules-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="top-navbar">
        <div>
            <div class="nav-brand-title">CEN • Panel de Preces</div>
            <div class="nav-brand-sub">Portafolio de seguimiento — <?php echo htmlspecialchars($nombre_usuario); ?></div>
        </div>
        <div class="nav-badges">
            <span class="badge-pill"><i class="fa-solid fa-user-shield"></i> <?php echo htmlspecialchars($rol_usuario); ?></span>
            <span class="badge-pill"><i class="fa-solid fa-school"></i> Acceso interno</span>
        </div>
    </header>

    <!-- Contenedor general expandido -->
    <div class="main-layout">
        <aside class="sidebar">
            <div class="user-profile-box">
                <div class="user-avatar">
                    <?php echo obtenerIniciales($nombre_usuario); ?>
                </div>
                <div class="user-details">
                    <h4><?php echo htmlspecialchars($nombre_usuario . ' ' . $apellido_usuario); ?></h4>
                    <span><?php echo htmlspecialchars($rol_usuario); ?> • CEN</span>
                </div>
            </div>

            <nav class="nav-menu">
                <a href="panel_prece.php" class="nav-item active">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-file-lines"></i> Formulario
                </a>
            </nav>

            <div class="sidebar-footer-info">
                Último acceso hoy, 
                <script>
                    const d = new Date();
                    document.write(d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
                </script><br>
                Centro Educativo Nonogasta
            </div>

            <a href="../logout.php" class="btn-logout-sidebar">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar Sesión
            </a>
        </aside>
        <main class="content-area">
            <div class="content-header">
                <div>
                    <h3>Inicio</h3>
                    <p>Resumen general de tu portafolio.</p>
                </div>
                <div class="sync-badge">
                    <i class="fa-solid fa-cloud-arrow-up" style="color: var(--dorado);"></i> Datos sincronizados
                </div>
            </div>

            <!-- Banner de Bienvenida -->
            <div class="welcome-banner">
                <h4>Bienvenida, <?php echo htmlspecialchars($nombre_usuario); ?> 👋</h4>
                <p>Este espacio reúne tus recursos y formularios de seguimiento de estudiantes. Podés ir completando la información a lo largo del ciclo lectivo.</p>
            </div>

            <div class="modules-grid">
                
                <div class="module-card">
                    <h5><i class="fa-solid fa-pen-to-square"></i> Registro de novedades</h5>
                    <p>Anotá observaciones importantes del curso, asistencia, citas con familias, etc.</p>
                </div>

                <div class="module-card">
                    <h5><i class="fa-solid fa-users"></i> Seguimiento grupal</h5>
                    <p>Acceso rápido al formulario para completar información clave de cada división.</p>
                </div>

                <div class="module-card">
                    <h5><i class="fa-solid fa-shield-halved"></i> Entregas digitales</h5>
                    <p>Los registros quedan guardados de forma segura para el equipo directivo.</p>
                </div>

            </div>
        </main>

    </div>

</body>
</html>