<?php

// Realizar la conexión a la base de datos
$servername = "localhost";
$username = "generous-library-moj";
$password = "i5X45G)M2A-o+p3Fch";
$database = "generous_library_moj_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos de la reserva desde la URL
    $id_empleado = $_GET['id_empleado'];
    $id_platillo = $_GET['id_platillo'];

    // Consultar el nombre del empleado
    $stmt_empleado = $conn->prepare("SELECT Nombre FROM empleados WHERE id = :id_empleado");
    $stmt_empleado->bindParam(':id_empleado', $id_empleado);
    $stmt_empleado->execute();
    $nombre_empleado = $stmt_empleado->fetch(PDO::FETCH_COLUMN);

    // Consultar el nombre del platillo
    $stmt_platillo = $conn->prepare("SELECT NombreMenu FROM menu WHERE id = :id_platillo");
    $stmt_platillo->bindParam(':id_platillo', $id_platillo);
    $stmt_platillo->execute();
    $nombre_platillo = $stmt_platillo->fetch(PDO::FETCH_COLUMN);

    // Consultar la fecha de reservación
    $stmt_reservacion = $conn->prepare("SELECT FechaReserva FROM transaccion WHERE IdEmpleado = :id_empleado AND IdMenu = :id_platillo");
    $stmt_reservacion->bindParam(':id_empleado', $id_empleado);
    $stmt_reservacion->bindParam(':id_platillo', $id_platillo);
    $stmt_reservacion->execute();
    $fecha_reservacion = $stmt_reservacion->fetch(PDO::FETCH_COLUMN);

    // Obtener el ID de la última transacción
    $stmt_last_transaction_id = $conn->prepare("SELECT id FROM transaccion ORDER BY id DESC LIMIT 1");
    $stmt_last_transaction_id->execute();
    $id_transaccion = $stmt_last_transaction_id->fetch(PDO::FETCH_COLUMN);

    // Consultar el número de serie del platillo
    $stmt_serial = $conn->prepare("SELECT NumSerie FROM alm_platillos WHERE IdMenu = :id_platillo AND IdEmpleado = :id_empleado");
    $stmt_serial->bindParam(':id_platillo', $id_platillo);
    $stmt_serial->bindParam(':id_empleado', $id_empleado);
    $stmt_serial->execute();
    $num_serie = $stmt_serial->fetch(PDO::FETCH_COLUMN);

    $query_last_reservation = "SELECT * FROM transaccion 
                                       WHERE IdEmpleado = ? 
                                       ORDER BY FechaReserva DESC 
                                       LIMIT 1";
            $stmt_last_reservation = $conn->prepare($query_last_reservation);
            $stmt_last_reservation->execute([$id_empleado]);
            $last_reservation = $stmt_last_reservation->fetch(PDO::FETCH_ASSOC);

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
    <title>Detalles de la Reservación</title>
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
        <h1>Detalles de la Reservación</h1>
        <p>Nombre Empleado: <?php echo $nombre_empleado; ?></p>
        <p>Empleado: <?php echo $id_empleado; ?></p>
        <p>Nombre del Platillo: <?php echo $nombre_platillo; ?></p>
        <p>Platillo: <?php echo $id_platillo; ?></p>
        <p>Código: <?php echo $num_serie; ?></p>
        <p>Fecha de Reservación: <?php echo $fecha_reservacion; ?></p>
        <span id='precioC'>$<?php echo $precio; ?></span>
    </div>
</body>
</html>
