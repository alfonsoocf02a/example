<?php
// Iniciar la conexión a la base de datos
require_once 'db_config.php';

// Crear una nueva conexión a la base de datos utilizando la clase mysqli
$conn = new mysqli($host, $username, $password, $database);

// Verificar la conexión: La propiedad connect_error se establece en un mensaje de error si la conexión falla
if ($conn->connect_error) {
    // Si la conexión falla, se imprime un mensaje de error y se termina la ejecución del script
    die("Conexión fallida: " . $conn->connect_error);
}

// Para mostrar caracteres especiales
$conn->set_charset("utf8");

// Verificar si el botón de consultar ha sido pulsado (si se ha enviado una solicitud POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Consulta SQL para obtener
    if (isset($_POST['consulta1'])) {
        // Consulta 1: Recuento de vehículos por marca
        $sql = "SELECT  
                    fv.brand AS 'Marca', 
                    COUNT(fv.brand) AS 'Vehiculos'
                FROM
                    FleetVehicle fv
                JOIN Brand ON Brand.BRAName = fv.brand
                GROUP BY fv.brand
                ORDER BY COUNT(fv.brand) DESC";
    } elseif (isset($_POST['consulta2'])) {
        // Consulta 2: Facturación por cliente y mes
        $sql = "SELECT
                    c.companyName AS 'Cliente',
                    YEAR(cl.date) AS 'Año',
                    MONTH(cl.date) AS 'Mes',
                    ROUND(SUM(cl.total), 2) AS 'Facturado'
                FROM 
                    Client AS c
                JOIN 
                    ClientLiquidation AS cl ON c.id = cl.client_id
                WHERE
                    c.type_id IN (2,4)
                GROUP BY YEAR(cl.date), MONTH(cl.date), c.id";
    }

    // Ejecutar la consulta SQL y almacenar el resultado en la variable $query
    $query = $conn->query($sql);

    // Array para almacenar los resultados de la consulta a la base de datos
    $results = [];

    // Verificar si la consulta se realizó correctamente
    if ($query) {
        // Si la consulta se realizó correctamente, se recorre el resultado fila por fila y se almacena en el array $results
        while($row = $query->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        // Si la consulta falla, se imprime un mensaje de error
        echo "Error en la consulta: " . $conn->error;
    }
}

// Cerrar la conexión a la base de datos para liberar recursos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas SQL</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container m-4">
    <div class="d-flex align-items-center">
        <h2 class="mr-4">Consultas SQL</h2>
        <form method="POST">
            <button type="submit" name="consulta1" class="btn btn-primary">Marcas y Vehiculos</button>
            <button type="submit" name="consulta2" class="btn btn-primary">Fact. Cliente/Mes</button>
        </form>
    </div>
    <table class="table table-striped mt-3">
       <thead>
            <tr>
                <!-- Cambiar los títulos de las columnas según la consulta realizada -->
                <?php if (!empty($results)): ?>
                    <?php foreach ($results[0] as $column => $value): ?>
                        <th><?php echo $column; ?></th>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
