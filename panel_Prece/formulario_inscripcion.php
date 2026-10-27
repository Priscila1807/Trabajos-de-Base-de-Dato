<?php
session_start();
include '../config/conexion.php'; 
$mensaje_exito = "";
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar datos del alumno
    $alumno_nombre = trim($_POST['alumno_nombre'] ?? '');
    $alumno_dni = trim($_POST['alumno_dni'] ?? '');
    $alumno_cuil = trim($_POST['alumno_cuil'] ?? '');
    $alumno_edad = trim($_POST['alumno_edad'] ?? null);
    $alumno_fnac = trim($_POST['alumno_fnac'] ?? '');
    $alumno_lnac = trim($_POST['alumno_lnac'] ?? '');
    $alumno_escuela = trim($_POST['alumno_escuela'] ?? '');
    $alumno_obs = trim($_POST['alumno_obs'] ?? '');

    // Madre
    $madre_nombre = trim($_POST['madre_nombre'] ?? '');
    $madre_dni = trim($_POST['madre_dni'] ?? '');
    $madre_cuil = trim($_POST['madre_cuil'] ?? '');
    $madre_fnac = trim($_POST['madre_fnac'] ?? null);
    $madre_lnac = trim($_POST['madre_lnac'] ?? '');
    $madre_asig = trim($_POST['madre_asig'] ?? '');
    $madre_dom = trim($_POST['madre_dom'] ?? '');
    $madre_tel = trim($_POST['madre_tel'] ?? '');
    $madre_est = trim($_POST['madre_est'] ?? '');
    $madre_email = trim($_POST['madre_email'] ?? '');

    // Padre
    $padre_nombre = trim($_POST['padre_nombre'] ?? '');
    $padre_dni = trim($_POST['padre_dni'] ?? '');
    $padre_cuil = trim($_POST['padre_cuil'] ?? '');
    $padre_fnac = trim($_POST['padre_fnac'] ?? null);
    $padre_lnac = trim($_POST['padre_lnac'] ?? '');
    $padre_asig = trim($_POST['padre_asig'] ?? '');
    $padre_dom = trim($_POST['padre_dom'] ?? '');
    $padre_tel = trim($_POST['padre_tel'] ?? '');
    $padre_est = trim($_POST['padre_est'] ?? '');
    $padre_email = trim($_POST['padre_email'] ?? '');

    // Acta y Autorización
    $tutor_acta = trim($_POST['tutor_acta'] ?? '');
    $tutor_dni_acta = trim($_POST['tutor_dni_acta'] ?? '');
    $correo_acta = trim($_POST['correo_acta'] ?? '');
    $aut_imagen = trim($_POST['aut_imagen'] ?? 'NO');

    $directorio_subida = "../uploads/";
    if (!is_dir($directorio_subida)) {
        mkdir($directorio_subida, 0777, true);
    }

    $ruta_foto = "";

    if (!empty($_POST['imagen_webcam'])) {
        $dataImg = $_POST['imagen_webcam'];
        $dataImg = str_replace('data:image/png;base64,', '', $dataImg);
        $dataImg = str_replace(' ', '+', $dataImg);
        $fileData = base64_decode($dataImg);
        
        $nombre_archivo = time() . "_webcam.png";
        $ruta_foto = $directorio_subida . $nombre_archivo;
        file_put_contents($ruta_foto, $fileData);
    } 
    elseif (isset($_FILES['alumno_foto']) && $_FILES['alumno_foto']['error'] == 0) {
        $nombre_archivo = time() . "_" . basename($_FILES['alumno_foto']['name']);
        $ruta_foto = $directorio_subida . $nombre_archivo;
        move_uploaded_file($_FILES['alumno_foto']['tmp_name'], $ruta_foto);
    }

    if (!empty($alumno_nombre) && !empty($alumno_dni) && !empty($madre_nombre) && !empty($madre_dni)) {
        $stmt = $conn->prepare("INSERT INTO inscripciones (
            alumno_apellido_nombre, alumno_dni, alumno_cuil, alumno_edad, alumno_fecha_nacimiento, alumno_lugar_nacimiento, alumno_escuela_origen, alumno_foto, alumno_observaciones,
            madre_apellido_nombre, madre_dni, madre_cuil, madre_fecha_nacimiento, madre_lugar_nacimiento, madre_percebe_asignacion, madre_domicilio, madre_telefono, madre_estudios, madre_email,
            padre_apellido_nombre, padre_dni, padre_cuil, padre_fecha_nacimiento, padre_lugar_nacimiento, padre_percebe_asignacion, padre_domicilio, padre_telefono, padre_estudios, padre_email,
            tutor_nombre_acta, tutor_dni_acta, correo_contacto_acta, autorizacion_imagen
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssisssssssssssssssssssssssssssss",
            $alumno_nombre, $alumno_dni, $alumno_cuil, $alumno_edad, $alumno_fnac, $alumno_lnac, $alumno_escuela, $ruta_foto, $alumno_obs,
            $madre_nombre, $madre_dni, $madre_cuil, $madre_fnac, $madre_lnac, $madre_asig, $madre_dom, $madre_tel, $madre_est, $madre_email,
            $padre_nombre, $padre_dni, $padre_cuil, $padre_fnac, $padre_lnac, $padre_asig, $padre_dom, $padre_tel, $padre_est, $padre_email,
            $tutor_acta, $tutor_dni_acta, $correo_acta, $aut_imagen
        );

        if ($stmt->execute()) {
            $mensaje_exito = "¡Ficha de inscripción generada y guardada con éxito!";
        } else {
            $mensaje_error = "Error al guardar en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensaje_error = "Por favor, completá todos los campos obligatorios marcados con asterisco (*).";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inscripción - CEN</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --marron-oscuro: #4A3525;
            --marron-cabecera: #3D2B1E;
            --marron-fondo-pagina: #EAE3D9;
            --dorado: #E5A83B;
            --blanco: #ffffff;
        }
        
        * { box-sizing: border-box; font-family: 'Outfit', sans-serif; margin: 0; padding: 0; }
        
        body { 
            background-color: var(--marron-fondo-pagina); 
            padding: 20px; 
            color: #333; 
        }
        
        .container { 
            max-width: 850px; 
            background: var(--blanco); 
            margin: 0 auto; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            overflow: hidden; 
        }
        
        .header { 
            background-color: var(--marron-oscuro); 
            color: white; 
            padding: 25px 20px; 
            text-align: center; 
        }
        
        .header h1 { font-size: 1.5rem; color: var(--dorado); margin-bottom: 5px; }
        .header p { font-size: 0.85rem; opacity: 0.8; }
        
        .form-body { padding: 30px; }
        
        .alert { 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-weight: 500; 
            text-align: center; 
            font-size: 0.88rem;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #4ade80; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        
        fieldset { 
            border: 1px solid #e2e8f0; 
            border-radius: 12px; 
            padding: 20px; 
            margin-bottom: 25px; 
        }
        
        legend { 
            font-weight: 700; 
            color: var(--marron-oscuro); 
            padding: 0 10px; 
            font-size: 1.1rem; 
        }
        
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 5px; color: #475569; }
        .input-group input, .input-group select, .input-group textarea {
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid #cbd5e1; 
            border-radius: 8px; 
            font-size: 0.88rem; 
            outline: none; 
            background: #f8fafc;
        }
        .input-group input:focus, .input-group select:focus, .input-group textarea:focus { 
            border-color: var(--dorado); 
            background: white; 
        }
        
        /* Cámara Web */
        .webcam-container { 
            margin-top: 10px; 
            background: #1e1e1e; 
            padding: 10px; 
            border-radius: 10px; 
            text-align: center; 
            display: none; 
        }
        #video { width: 100%; max-width: 280px; border-radius: 8px; }
        .btn-cam { 
            background: #334155; 
            color: white; 
            padding: 8px 12px; 
            border-radius: 6px; 
            font-size: 0.8rem; 
            cursor: pointer; 
            border: none; 
            margin-top: 5px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            gap: 5px; 
        }
        .btn-cam:hover { background: #475569; }
        #canvas { display: none; }

        .actions { 
            display: flex; 
            justify-content: flex-end; 
            gap: 10px; 
            margin-top: 20px; 
        }
        
        .btn { 
            padding: 12px 20px; 
            border-radius: 8px; 
            font-weight: 700; 
            cursor: pointer; 
            border: none; 
            font-size: 0.9rem; 
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-clear { background: #e2e8f0; color: #334155; }
        .btn-submit { background: var(--dorado); color: white; box-shadow: 0 4px 12px rgba(229,168,59,0.3); }
        .btn-print { background: var(--marron-oscuro); color: white; }

        /* ================= MEDIA QUERIES (RESPONSIVE EN TODO) ================= */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .form-body { padding: 18px 15px; }
            fieldset { padding: 15px 12px; }
            legend { font-size: 1rem; }
            
            .grid-2, .grid-3 {
                grid-template-columns: 1fr !important;
                gap: 10px;
            }
            
            .header h1 { font-size: 1.25rem; }
            .header p { font-size: 0.8rem; }
            
            .actions {
                flex-direction: column;
                gap: 10px;
            }
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .input-group label { font-size: 0.8rem; }
            .input-group input, .input-group select, .input-group textarea { font-size: 0.85rem; padding: 8px 10px; }
        }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; width: 100%; max-width: 100%; border-radius: 0; }
            .actions, .btn, .btn-cam, .webcam-container { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Centro Educativo Nonogasta</h1>
        <p>Formulario de Inscripción del Estudiante — Ciclo Lectivo</p>
    </div>

    <div class="form-body">
        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $mensaje_exito; ?></div>
        <?php endif; ?>
        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <!-- DATOS DEL ALUMNO -->
            <fieldset>
                <legend>Datos del Alumno/a</legend>
                <div class="grid-2">
                    <div class="input-group">
                        <label>Apellido y nombres *</label>
                        <input type="text" name="alumno_nombre" required>
                    </div>
                    <div class="input-group">
                        <label>D.N.I. *</label>
                        <input type="text" name="alumno_dni" required>
                    </div>
                </div>
                <div class="grid-3">
                    <div class="input-group">
                        <label>CUIL</label>
                        <input type="text" name="alumno_cuil" placeholder="##-########-#">
                    </div>
                    <div class="input-group">
                        <label>Edad</label>
                        <input type="number" name="alumno_edad">
                    </div>
                    <div class="input-group">
                        <label>Fecha de nacimiento *</label>
                        <input type="date" name="alumno_fnac" required>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="input-group">
                        <label>Lugar de nacimiento</label>
                        <input type="text" name="alumno_lnac">
                    </div>
                    <div class="input-group">
                        <label>Escuela de origen</label>
                        <input type="text" name="alumno_escuela">
                    </div>
                </div>
                
                <!-- FOTO Y CÁMARA WEB -->
                <div class="grid-2">
                    <div class="input-group">
                        <label>Foto del alumno/a</label>
                        <input type="file" name="alumno_foto" accept="image/*">
                        <button type="button" class="btn-cam" onclick="abrirCamara()"><i class="fa-solid fa-camera"></i> Tomar foto</button>
                        
                        <div class="webcam-container" id="webcamContainer">
                            <video id="video" autoplay playsinline></video>
                            <canvas id="canvas"></canvas>
                            <div>
                                <button type="button" class="btn-cam" style="background: #16a34a;" onclick="capturarFoto()"><i class="fa-solid fa-check"></i> Capturar</button>
                                <button type="button" class="btn-cam" style="background: #dc2626;" onclick="cerrarCamara()"><i class="fa-solid fa-xmark"></i> Cerrar</button>
                            </div>
                        </div>
                        <input type="hidden" name="imagen_webcam" id="imagen_webcam">
                        <div id="previewTexto" style="font-size: 0.75rem; color: #16a34a; margin-top: 4px; font-weight: 600;"></div>
                    </div>
                    <div class="input-group">
                        <label>Observaciones de salud / necesidades</label>
                        <textarea name="alumno_obs" rows="3"></textarea>
                    </div>
                </div>
            </fieldset>

            <!-- DATOS DE LA MADRE / TUTORA -->
            <fieldset>
                <legend>Datos de la Madre / Tutora</legend>
                <div class="grid-2">
                    <div class="input-group">
                        <label>Apellido y nombres *</label>
                        <input type="text" name="madre_nombre" required>
                    </div>
                    <div class="input-group">
                        <label>D.N.I. *</label>
                        <input type="text" name="madre_dni" required>
                    </div>
                </div>
                <div class="grid-3">
                    <div class="input-group">
                        <label>CUIL</label>
                        <input type="text" name="madre_cuil">
                    </div>
                    <div class="input-group">
                        <label>Fecha de nacimiento</label>
                        <input type="date" name="madre_fnac">
                    </div>
                    <div class="input-group">
                        <label>Lugar de nacimiento</label>
                        <input type="text" name="madre_lnac">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="input-group">
                        <label>Percebe asignación</label>
                        <select name="madre_asig">
                            <option value="">Seleccionar...</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Domicilio *</label>
                        <input type="text" name="madre_dom" required>
                    </div>
                    <div class="input-group">
                        <label>Teléfono *</label>
                        <input type="text" name="madre_tel" required>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="input-group">
                        <label>Estudios cursados</label>
                        <select name="madre_est">
                            <option value="">Seleccionar...</option>
                            <option value="Primario">Primario</option>
                            <option value="Secundario">Secundario</option>
                            <option value="Terciario">Terciario</option>
                            <option value="Universitario">Universitario</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="madre_email">
                    </div>
                </div>
            </fieldset>

            <!-- DATOS DEL PADRE / TUTOR -->
            <fieldset>
                <legend>Datos del Padre / Tutor</legend>
                <div class="grid-2">
                    <div class="input-group">
                        <label>Apellido y nombres</label>
                        <input type="text" name="padre_nombre">
                    </div>
                    <div class="input-group">
                        <label>D.N.I.</label>
                        <input type="text" name="padre_dni">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="input-group">
                        <label>CUIL</label>
                        <input type="text" name="padre_cuil">
                    </div>
                    <div class="input-group">
                        <label>Fecha de nacimiento</label>
                        <input type="date" name="padre_fnac">
                    </div>
                    <div class="input-group">
                        <label>Lugar de nacimiento</label>
                        <input type="text" name="padre_lnac">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="input-group">
                        <label>Percebe asignación</label>
                        <select name="padre_asig">
                            <option value="">Seleccionar...</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Domicilio</label>
                        <input type="text" name="padre_dom">
                    </div>
                    <div class="input-group">
                        <label>Teléfono</label>
                        <input type="text" name="padre_tel">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="input-group">
                        <label>Estudios cursados</label>
                        <select name="padre_est">
                            <option value="">Seleccionar...</option>
                            <option value="Primario">Primario</option>
                            <option value="Secundario">Secundario</option>
                            <option value="Terciario">Terciario</option>
                            <option value="Universitario">Universitario</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="padre_email">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Acta de Aceptación y Autorización</legend>
                <p style="font-size: 0.85rem; line-height: 1.5; margin-bottom: 15px; color: #475569;">
                    Mediante la presente el que suscribe, conociendo los términos del <b>RÉGIMEN DE CONVIVENCIA ESCOLAR</b>, ACEPTA en todos sus términos lo establecido y se compromete a colaborar para su estricto cumplimiento. Asimismo, AUTORIZA / NO AUTORIZA el uso de imágenes del estudiante para publicaciones educativas.
                </p>
                <div class="grid-3">
                    <div class="input-group">
                        <label>Nombre y Apellido del Tutor/a *</label>
                        <input type="text" name="tutor_acta" required>
                    </div>
                    <div class="input-group">
                        <label>D.N.I. del Tutor/a *</label>
                        <input type="text" name="tutor_dni_acta" required>
                    </div>
                    <div class="input-group">
                        <label>Correo de contacto</label>
                        <input type="email" name="correo_acta">
                    </div>
                </div>
                <div class="input-group" style="margin-top: 15px;">
                    <label>Autorización de uso de imagen *</label>
                    <select name="aut_imagen" required>
                        <option value="">Seleccionar...</option>
                        <option value="AUTORIZO">AUTORIZO</option>
                        <option value="NO AUTORIZO">NO AUTORIZO</option>
                    </select>
                </div>
            </fieldset>

            <div class="actions">
                <a href="panel_prece.php" class="btn btn-clear" style="text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                <button type="reset" class="btn btn-clear">Limpiar</button>
                <button type="button" onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> Imprimir Ficha</button>
                <button type="submit" class="btn btn-submit"><i class="fa-solid fa-floppy-disk"></i> Guardar en Base de Datos</button>
            </div>

        </form>
    </div>
</div>

<script>
    let mediaStream = null;

    function abrirCamara() {
        const container = document.getElementById('webcamContainer');
        const video = document.getElementById('video');
        container.style.display = 'block';

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(stream => {
                mediaStream = stream;
                video.srcObject = stream;
            })
            .catch(err => {
                alert("No se pudo acceder a la cámara: " + err);
            });
    }

    function capturarFoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const imagenWebcamInput = document.getElementById('imagen_webcam');
        const previewTexto = document.getElementById('previewTexto');

        canvas.width = video.videoWidth || 320;
        canvas.height = video.videoHeight || 240;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataURL = canvas.toDataURL('image/png');
        imagenWebcamInput.value = dataURL;

        previewTexto.innerText = "¡Foto capturada con éxito desde la cámara!";
        cerrarCamara();
    }

    function cerrarCamara() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
        document.getElementById('webcamContainer').style.display = 'none';
    }
</script>

</body>
</html>