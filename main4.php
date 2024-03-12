<?php
require_once 'db_config.php';

// Crear una nueva conexión a la base de datos utilizando la clase mysqli
$conn = new mysqli($host, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Función para obtener los encabezados de las tablas
function getTableHeaders($results) {
    $headers = '';
    if (!empty($results)) {
        foreach ($results[0] as $column => $value) {
            $headers .= "<th>$column</th>";
        }
    }
    return $headers;
}

$results = [];
$facturacionMinima = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['facturacionMinima'])) {
        $facturacionMinima = $_POST['facturacionMinima'];
    }

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
                GROUP BY YEAR(cl.date), MONTH(cl.date), c.id
                HAVING facturado > $facturacionMinima";
    }


    // Ejecuta la consulta solo si $sql está definido
    if (isset($sql)) {
        $query = $conn->query($sql);

        if ($query) {
            while ($row = $query->fetch_assoc()) {
                $results[] = $row;
            }
        } else {
            echo "Error en la consulta: " . $conn->error;
        }
    }

}


$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container m-4">
    <div class="d-flex align-items-center">
        <h2 class="mr-4">Consultas SQL</h2>
        <div class="d-flex align-items-center">
            <!-- Envoltorio del botón y formulario para mantener alineación y uniformidad -->
            <div class="form-inline mr-4">
                <form method="POST">
                    <button type="submit" name="consulta1" class="btn btn-primary mb-2">Marcas y Vehiculos</button>
                </form>
            </div>
            <form method="POST" class="form-inline">
                <!-- Grupo de entrada para la facturación mínima -->
                <label for="facturacionMinima" class="mr-2"><b>Cantidad Mínima Facturación:</b></label>
                <div class="input-group mb-2">
                    <input type="number" class="form-control" placeholder="Facturación mínima" 
                        name="facturacionMinima" id="facturacionMinima"
                        value="<?php echo isset($_POST['facturacionMinima']) ? $_POST['facturacionMinima'] : '0'; ?>">
                    <div class="input-group-append">
                        <button type="submit" name="consulta2" class="btn btn-primary">Fact. Cliente/Mes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php if (!empty($results)): ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr><?php echo getTableHeaders($results); ?></tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <?php foreach ($row as $key => $value): ?>
                    <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>