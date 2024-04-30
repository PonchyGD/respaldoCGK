<?php
session_start(); // Iniciar sesión para mantener el estado del usuario

// Verificar si el empleado no está autenticado
if (!isset($_SESSION['empleado_id'])) {
    // Si el empleado no está autenticado, redirigirlo a la página de inicio de sesión
    header("Location: ../login.php");
    exit();
}

$nombre_empleado = $_SESSION['empleado_nombre'];
date_default_timezone_set('America/Mexico_City');
$fecha_actual_php = date("Y-m-d");
$bloquear_pagina = true;
$es_fin_de_semana = (date('N') >= 6);
$servername = "localhost";
$username = "generous-library-moj";
$password = "i5X45G)M2A-o+p3Fch";
$database = "generous_library_moj_db";
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Modificar la consulta para excluir el platillo más caro que ya ha sido reservado por el empleado
    $query_check_reservation = "SELECT * FROM transaccion 
                                WHERE IdEmpleado = ? AND FechaReserva = ?";
    $stmt_check_reservation = $conn->prepare($query_check_reservation);
    $stmt_check_reservation->execute([$_SESSION['empleado_id'], $fecha_actual_php]);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comedor</title>
    <link rel="stylesheet" href="2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #imagenPrincipal {
            max-width: 100%;
            height: auto;
        }

        #container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .platillo {
            width: 200px;
            margin: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .platillo img {
            width: 100%;
            height: auto;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .platillo h2 {
            font-size: 18px;
            margin: 10px 0;
            text-align: center;
        }

        .platillo p {
            font-size: 14px;
            margin: 0 10px 10px;
        }

        .tab-container {
            text-align: center; /* Para centrar los elementos .tab */
        }

        .tab-wrapper {
            display: flex;
            justify-content: center; /* Centrar las tabs */
        }

        .tab {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #007bff; /* Cambio de color de fondo */
            color: #fff; /* Cambio de color de texto */
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s; /* Animación al pasar el mouse */
        }

        .tab:hover {
            background-color: #0056b3; /* Color de fondo al pasar el mouse */
        }

        .tabcontent {
            display: none;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #007bff; /* Color del borde */
            background-color: #f1f1f1; /* Color de fondo */
        }

        #mensaje-reserva {
            background-color: #ffe6e6;
            border: 1px solid #ff9999;
            color: #ff3333;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            max-width: 400px;
            margin: 20px auto;
        }

        #mensaje-reserva p {
            margin: 5px 0;
        }

        #ultima-reserva {
            background-color: #e6f7ff;
            border: 1px solid #99ccff;
            color: #007bff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            max-width: 400px;
            margin: 20px auto;
        }

        #ultima-reserva p {
            margin: 5px 0;
        }

        #menu {
            text-align: center;
        }

        #nombre-empleado {
            text-align: center;
            font-size: 20px;
            margin: 20px 0;
        }

        #btn {
            font-family: Arial, Helvetica, sans-serif;
        }
        .roboto-black {
            font-family: "Roboto", sans-serif;
            font-weight: 900;
            font-style: normal;
        }

        #precioC {
            color: red;
        }
    </style>
</head>
<body>

<img id="imagenPrincipal" src="../logo.jpg" alt="Imagen Principal">

<?php
// Verificar si hubo una reservación y hasta el día siguiente
if (!$es_fin_de_semana) {
    $reserva_realizada = false; // Variable para indicar si se realizó una reserva
    // Realizar la consulta para verificar si se realizó una reserva para el día actual
    $query_check_reservation_today = "SELECT * FROM transaccion 
                                      WHERE IdEmpleado = ? AND FechaReserva = ?";
    $stmt_check_reservation_today = $conn->prepare($query_check_reservation_today);
    $stmt_check_reservation_today->execute([$_SESSION['empleado_id'], $fecha_actual_php]);
    
    // Verificar si se encontró alguna reserva para el día actual
    if ($stmt_check_reservation_today->rowCount() > 0) {
        $reserva_realizada = true; // Se encontró una reserva
    }
}

// Quitar "!" para desactivar
if (!$es_fin_de_semana) {
?>
    <h1 id="menu" class="roboto-black">Bienvenid@, <?php echo $nombre_empleado; ?>.</p>
    <h2 id="menu">Elija su comida del día de hoy: <?php echo date("d-m-Y") ?>.</h2>
    <a id="btn" href="cerrar.php">Cerrar Sesion</a>
    <div class="tab-container">
        <div class="tab-wrapper">
        <button class="tab" onclick="openTab(event, 'combo')">
            <span class='roboto-black'>Menú:</span> <br> <i class="fa-solid fa-glass-water"></i> <i class='bx bxs-bowl-hot'></i> <i class="fas fa-ice-cream"></i> 
        </button>
        <button class="tab" onclick="openTab(event, 'agua')">
        <span class='roboto-black'>Menú:</span> <br> <i class="fa-solid fa-glass-water"></i>
        </button>
        </div>
    </div>
<?php
}
?>

<?php
        // Obtener la fecha actual de PHP
        $fecha_actual_php = date("Y-m-d");
    
        $es_fin_de_semana = (date('N') >= 6);

        // Poner "!" para desactivar
        if ($es_fin_de_semana) {
            echo "<div id='mensaje-reserva'>";
            echo "<p>No es posible realizar reservas en este momento.</p>";
            echo "</div>";
            echo "<a id='btn' href='cerrar.php'>Cerrar Sesion</a>";
            exit();
        }
?>

<div id="agua" class="tabcontent">
    <div id="container">
        
        <?php
        // Conexión a la base de datos MySQL utilizando PDO
        $servername = "localhost";
        $username = "generous-library-moj";
        $password = "i5X45G)M2A-o+p3Fch";
        $database = "generous_library_moj_db";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	   // Modificar la consulta para excluir el platillo más caro que ya ha sido reservado por el empleado
        $query_check_reservation = "SELECT * FROM transaccion 
                                    WHERE IdEmpleado = ? AND FechaReserva = ?";
        $stmt_check_reservation = $conn->prepare($query_check_reservation);
        $stmt_check_reservation->execute([$_SESSION['empleado_id'], $fecha_actual_php]);

        // Verificar si el empleado ya ha realizado una reserva para algún menú en la misma fecha
        if ($stmt_check_reservation->rowCount() > 0) {
            echo "<div id='mensaje-reserva'>";
            echo "<p>Ya has realizado una reserva para hoy. No puedes reservar más platillos.</p>";

            // Mostrar los datos de la última reserva realizada por el empleado
            $query_last_reservation = "SELECT * FROM transaccion 
                                       WHERE IdEmpleado = ? 
                                       ORDER BY FechaReserva DESC 
                                       LIMIT 1";
            $stmt_last_reservation = $conn->prepare($query_last_reservation);
            $stmt_last_reservation->execute([$_SESSION['empleado_id']]);
            $last_reservation = $stmt_last_reservation->fetch(PDO::FETCH_ASSOC);

            if ($last_reservation) {
                if ($last_reservation) {
                    // Obtener el precio del platillo reservado
                    $query_precio = "SELECT Precio FROM menu WHERE id = ?";
                    $stmt_precio = $conn->prepare($query_precio);
                    $stmt_precio->execute([$last_reservation['IdMenu']]);
                    $row_precio = $stmt_precio->fetch(PDO::FETCH_ASSOC);
                    $precio = $row_precio['Precio'];
                    
                
                    echo "<div id='ultima-reserva'>";
                    echo "<p>Su reserva de hoy:</p>";
                    echo "<p>Colaborador: " . $nombre_empleado . ".</p>";
                    echo "<p>Código del menú: <span id='precioC' class='roboto-black'>" . $last_reservation['NumSerie'] . "<span></p>";
                    echo "<p>" . $last_reservation['NombrePlatillo'] . ".</p>";
                    echo "<p class='roboto-black'> <span id='precioC'>$" . $precio . "<span></p>"; // Mostrar el precio
                    echo "<img id='qr-code' src='{$last_reservation['CodigoQR']}' alt='Código QR'>";
                    echo "</div>";
                }
                
            }

            echo "</div>";
        } else {
            // Verificar si se reservó un platillo especial
            $especial_reservado = false;
            $query_check_especial = "SELECT COUNT(*) AS total FROM transaccion 
                                    WHERE IdEmpleado = ? AND Reservado = 1 
                                    AND IdMenu IN (SELECT id FROM menu WHERE especial = 1)";
            $stmt_check_especial = $conn->prepare($query_check_especial);
            $stmt_check_especial->execute([$_SESSION['empleado_id']]);
            $row_especial = $stmt_check_especial->fetch(PDO::FETCH_ASSOC);
            if ($row_especial['total'] > 0) {
                $especial_reservado = true;
            }

            // Verificar si se reservó un platillo especial_2
            $especial_2_reservado = false;
            $query_check_especial_2 = "SELECT COUNT(*) AS total FROM transaccion 
                                    WHERE IdEmpleado = ? AND Reservado = 1 
                                    AND IdMenu IN (SELECT id FROM menu WHERE especial_2 = 1)";
            $stmt_check_especial_2 = $conn->prepare($query_check_especial_2);
            $stmt_check_especial_2->execute([$_SESSION['empleado_id']]);
            $row_especial_2 = $stmt_check_especial_2->fetch(PDO::FETCH_ASSOC);
            if ($row_especial_2['total'] > 0) {
                $especial_2_reservado = true;
            }

            $query = "SELECT * FROM menu 
                    WHERE id NOT IN (
                        SELECT IdMenu FROM transaccion 
                        WHERE IdEmpleado = ? AND Reservado = 1
                    )
                    AND agua = 1";

            // Verificar si se reservó un platillo especial
            if ($especial_reservado) {
                // Si se reservó un platillo especial, excluir todos los platillos especiales
                $query .= " AND especial = 0";
            } 

            // Verificar si se reservó un platillo especial_2
            if ($especial_2_reservado) {
                // Si se reservó un platillo especial_2, excluir todos los platillos especiales_2
                $query .= " AND especial_2 = 0";
            }

            $stmt = $conn->prepare($query);
            $stmt->execute([$_SESSION['empleado_id']]);


            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='platillo'>";
                echo "<a href='pagina_platillo.php?id={$row['id']}'>";
                echo "<img src='{$row['Imagen']}' alt='{$row['NombreMenu']}'>";
                echo "</a>";
                echo "<h2>{$row['NombreMenu']}</h2>";
                echo "<p>{$row['Descripcion']}</p>";
                echo "</div>";
            }
 	  }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $conn = null;
        ?>
    </div>
</div>

<div id="combo" class="tabcontent">
    <div id="container">
        
        <?php
        // Conexión a la base de datos MySQL utilizando PDO
        $servername = "localhost";
        $username = "generous-library-moj";
        $password = "i5X45G)M2A-o+p3Fch";
        $database = "generous_library_moj_db";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Modificar la consulta para excluir el platillo más caro que ya ha sido reservado por el empleado
        $query_check_reservation = "SELECT * FROM transaccion 
                                    WHERE IdEmpleado = ? AND FechaReserva = ?";
        $stmt_check_reservation = $conn->prepare($query_check_reservation);
        $stmt_check_reservation->execute([$_SESSION['empleado_id'], $fecha_actual_php]);

        // Verificar si el empleado ya ha realizado una reserva para algún menú en la misma fecha
        if ($stmt_check_reservation->rowCount() > 0) {
            echo "<div id='mensaje-reserva'>";
            echo "<p>Ya has realizado una reserva para hoy. No puedes reservar más platillos.</p>";

            // Mostrar los datos de la última reserva realizada por el empleado
            $query_last_reservation = "SELECT * FROM transaccion 
                                       WHERE IdEmpleado = ? 
                                       ORDER BY FechaReserva DESC 
                                       LIMIT 1";
            $stmt_last_reservation = $conn->prepare($query_last_reservation);
            $stmt_last_reservation->execute([$_SESSION['empleado_id']]);
            $last_reservation = $stmt_last_reservation->fetch(PDO::FETCH_ASSOC);

            if ($last_reservation) {
                // Obtener el precio del platillo reservado
                $query_precio = "SELECT Precio FROM menu WHERE id = ?";
                $stmt_precio = $conn->prepare($query_precio);
                $stmt_precio->execute([$last_reservation['IdMenu']]);
                $row_precio = $stmt_precio->fetch(PDO::FETCH_ASSOC);
                $precio = $row_precio['Precio'];
            
                echo "<div id='ultima-reserva'>";
                echo "<p>Su reserva de hoy:</p>";
                echo "<p>Colaborador: " . $nombre_empleado . ".</p>";
                echo "<p>Código del menú: <span id='precioC' class='roboto-black'>" . $last_reservation['NumSerie'] . "<span></p>";
                echo "<p>" . $last_reservation['NombrePlatillo'] . ".</p>";
                echo "<p class='roboto-black'> <span id='precioC'>$" . $precio . "<span></p>"; // Mostrar el precio
                echo "<img id='qr-code' src='{$last_reservation['CodigoQR']}' alt='Código QR'>";
                echo "</div>";
            }

            echo "</div>";
        } else {
            $especial_reservado = false;
            $query_check_especial = "SELECT COUNT(*) AS total FROM transaccion 
                                    WHERE IdEmpleado = ? AND Reservado = 1 
                                    AND IdMenu IN (SELECT id FROM menu WHERE especial = 1)";
            $stmt_check_especial = $conn->prepare($query_check_especial);
            $stmt_check_especial->execute([$_SESSION['empleado_id']]);
            $row_especial = $stmt_check_especial->fetch(PDO::FETCH_ASSOC);
            if ($row_especial['total'] > 0) {
                $especial_reservado = true;
            }

            // Verificar si se reservó un platillo especial_2
            $especial_2_reservado = false;
            $query_check_especial_2 = "SELECT COUNT(*) AS total FROM transaccion 
                                    WHERE IdEmpleado = ? AND Reservado = 1 
                                    AND IdMenu IN (SELECT id FROM menu WHERE especial_2 = 1)";
            $stmt_check_especial_2 = $conn->prepare($query_check_especial_2);
            $stmt_check_especial_2->execute([$_SESSION['empleado_id']]);
            $row_especial_2 = $stmt_check_especial_2->fetch(PDO::FETCH_ASSOC);
            if ($row_especial_2['total'] > 0) {
                $especial_2_reservado = true;
            }

            $query = "SELECT * FROM menu 
                    WHERE id NOT IN (
                        SELECT IdMenu FROM transaccion 
                        WHERE IdEmpleado = ? AND Reservado = 1
                    )
                    AND combo = 1";

            // Verificar si se reservó un platillo especial
            if ($especial_reservado) {
                // Si se reservó un platillo especial, excluir todos los platillos especiales
                $query .= " AND especial = 0";
            } 

            // Verificar si se reservó un platillo especial_2
            if ($especial_2_reservado) {
                // Si se reservó un platillo especial_2, excluir todos los platillos especiales_2
                $query .= " AND especial_2 = 0";
            }

            $query .= " OR tacos = 1";

            // if (!$especial_3_reservado) {
            //     $query .= " AND tacos = 1";
            // }

            $stmt = $conn->prepare($query);
            $stmt->execute([$_SESSION['empleado_id']]);
  
            // Generar la estructura HTML para cada platillo
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='platillo'>";
                echo "<a href='pagina_platillo.php?id={$row['id']}'>";
                echo "<img src='{$row['Imagen']}' alt='{$row['NombreMenu']}'>";
                echo "</a>";
                echo "<h2>{$row['NombreMenu']}</h2>";
                echo "<p>{$row['Descripcion']}</p>";
                echo "</div>";
            }
	  }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $conn = null;
        ?>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        // Ocultar todos los elementos con la clase "tabcontent"
        var tabcontent = document.getElementsByClassName("tabcontent");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Remover la clase "active" de todos los elementos con la clase "tab"
        var tablinks = document.getElementsByClassName("tab");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Mostrar el contenido de la pestaña seleccionada y agregar la clase "active" a la pestaña
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Mostrar la pestaña por defecto al cargar la página
    document.getElementsByClassName("tab")[0].click();
</script>
<p id='menu'>© 2024, by: <a id='menu' href="mailto:elponchygd@gmail.com">Elías Flores (Ponchy)</a></p>
</body>
</html>