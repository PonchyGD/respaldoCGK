<?php
session_start(); // Iniciar sesión para mantener el estado del usuario

// Verificar si el empleado está autenticado
if (!isset($_SESSION['empleado_id'])) {
    // Si el empleado no está autenticado, redirigirlo a la página de inicio de sesión
    header("Location: ../login.php");
    exit();
}

// Obtener el nombre, ID del empleado y la fecha actual
$nombre_empleado = $_SESSION['empleado_nombre'];
$id_empleado = $_SESSION['empleado_id'];
date_default_timezone_set('America/Mexico_City');
$fecha_reservacion = date("Y-m-d");

// Realizar la conexión a la base de datos
$servername = "localhost";
$username = "generous-library-moj";
$password = "i5X45G)M2A-o+p3Fch";
$database = "generous_library_moj_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt_last_reservation = $conn->prepare("SELECT IdMenu FROM transaccion WHERE IdEmpleado = :id_empleado ORDER BY FechaReserva DESC LIMIT 1");
    $stmt_last_reservation->bindParam(':id_empleado', $id_empleado);
    $stmt_last_reservation->execute();
    $id_platillo = $stmt_last_reservation->fetch(PDO::FETCH_COLUMN);

    $query_last_reservation = "SELECT * FROM transaccion 
                                       WHERE IdEmpleado = ? 
                                       ORDER BY FechaReserva DESC 
                                       LIMIT 1";
            $stmt_last_reservation = $conn->prepare($query_last_reservation);
            $stmt_last_reservation->execute([$_SESSION['empleado_id']]);
            $last_reservation = $stmt_last_reservation->fetch(PDO::FETCH_ASSOC);

    
    // Consultar el nombre del platillo
    $stmt_platillo = $conn->prepare("SELECT NombreMenu FROM menu WHERE id = :id_platillo");
    $stmt_platillo->bindParam(':id_platillo', $id_platillo);
    $stmt_platillo->execute();
    $nombre_platillo = $stmt_platillo->fetch(PDO::FETCH_COLUMN);

    $stmt_serial = $conn->prepare("SELECT NumSerie FROM alm_platillos WHERE IdMenu = :id_platillo AND IdEmpleado = :id_empleado AND FechaReserva = :FechaReserva");
    $stmt_serial->bindParam(':id_platillo', $id_platillo);
    $stmt_serial->bindParam(':id_empleado', $id_empleado);
    $stmt_serial->bindParam(':FechaReserva', $fecha_reservacion);
    $stmt_serial->execute();
    $num_serie = $stmt_serial->fetch(PDO::FETCH_COLUMN);

    // Ruta de la carpeta donde se guardarán los códigos QR
    $carpeta_qr = "qrcodes/";

    // Generar el texto del código QR con la información del empleado, platillo y fecha de reservación
    $url_detalles_reservacion = "https://juftalo.nyc.dom.my.id/auth/detalles_reservacion.php?id_empleado=$id_empleado&id_platillo=$id_platillo";
    $texto_qr = $url_detalles_reservacion;

    // Generar un nombre aleatorio para el archivo del código QR
    $archivo_qr = $carpeta_qr . "codigo_qr_" . uniqid() . ".png";

    // Generar el código QR
    require_once "../phpqrcode/qrlib.php";
    QRcode::png($texto_qr, $archivo_qr);

    // Insertar la ruta del código QR en la base de datos
    $query_update_qr_path = "UPDATE transaccion SET CodigoQR = :codigo_qr WHERE IdEmpleado = :id_empleado AND FechaReserva = :fecha_reserva ORDER BY FechaReserva DESC LIMIT 1";
    $stmt_update_qr_path = $conn->prepare($query_update_qr_path);
    $stmt_update_qr_path->bindParam(':codigo_qr', $archivo_qr);
    $stmt_update_qr_path->bindParam(':id_empleado', $id_empleado);
    $stmt_update_qr_path->bindParam(':fecha_reserva', $fecha_reservacion);
    $stmt_update_qr_path->execute();

    // Obtener el ID de la última transacción
    $stmt_last_transaction_id = $conn->prepare("SELECT id FROM transaccion ORDER BY id DESC LIMIT 1");
    $stmt_last_transaction_id->execute();
    $id_transaccion = $stmt_last_transaction_id->fetch(PDO::FETCH_COLUMN);

    $query_precio = "SELECT Precio FROM menu WHERE id = ?";
                    $stmt_precio = $conn->prepare($query_precio);
                    $stmt_precio->execute([$last_reservation['IdMenu']]);
                    $row_precio = $stmt_precio->fetch(PDO::FETCH_ASSOC);
                    $precio = $row_precio['Precio'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éxito de Reserva</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
        img {
            margin-top: 20px;
            max-width: 100%;
        }
        #precioC {
            color: red;
            font-family: "Roboto", sans-serif;
            font-weight: 900;
            font-style: normal;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Reserva Exitosa!</h1>
        <p>¡Hola, <?php echo $nombre_empleado; ?>!. Su reserva de hoy (<?php echo $fecha_reservacion; ?>): <?php echo $nombre_platillo; ?> (Código: <?php echo $num_serie; ?>). <br> <span id='precioC'>$<?php echo $precio; ?></span> </p>
        <img src="<?php echo $archivo_qr; ?>" alt="Código QR">
    </div>
    <a href="index.php">Regresar al Menú.</a>
</body>
</html>