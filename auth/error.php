<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de inventario</title>
    <link rel="stylesheet" href="2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        #error {
            background-color: #e6f7ff;
            border: 1px solid #99ccff;
            color: #007bff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            max-width: 400px;
            margin: 450px auto;
        }

        #error p {
            margin: 5px 0;
        }
        
        #boton {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            
        }

        #boton:hover {
            background-color: #0056b3;
        }
        .texto {
            font-family: "Roboto", sans-serif;
            font-weight: 900;
            font-style: normal;
        }
    </style>
</head>
<body>
    <div id="error">
        <p class="texto">No hay códigos disponibles para este menú.</p>
        <a href="index.php" id="boton" class="texto"'>Regresar</a>
    </div>
</body>
</html>