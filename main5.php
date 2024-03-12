<?php
require_once 'db_config.php';

function connectToDatabase($host, $username, $password, $database) {
    try {
        $conn = new mysqli($host, $username, $password, $database);
        if ($conn->connect_error) {
            throw new Exception("Conexión fallida: " . $conn->connect_error);
        }
        $conn->set_charset("utf8");
        return $conn;
    } catch (Exception $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }
}

function executeQuery($conn, $sql, $params = [], $types = null) {
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $conn->error);
        }
        if ($params && $types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    } catch (Exception $e) {
        die("Error al ejecutar consulta: " . $e->getMessage());
    }
}

function getTableHeaders($results) {
    $headers = '';
    if (!empty($results)) {
        foreach ($results[0] as $column => $value) {
            $headers .= "<th>$column</th>";
        }
    }
    return $headers;
}

function getQueryResults($conn, $facturacionMinima) {
    $results = [];
    if (isset($_POST['consulta1'])) {
        $sql = "SELECT  
            fv.brand AS 'Marca', 
            COUNT(fv.brand) AS 'Vehiculos'
        FROM
            FleetVehicle fv
        JOIN Brand ON Brand.BRAName = fv.brand
        GROUP BY fv.brand
        ORDER BY COUNT(fv.brand) DESC";
        $result = executeQuery($conn, $sql);
    } elseif (isset($_POST['consulta2'])) {
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
        $result = executeQuery($conn, $sql, [$facturacionMinima], 'd');
    }

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
    return $results;
}

$conn = connectToDatabase($host, $username, $password, $database);
$facturacionMinima = $_POST['facturacionMinima'] ?? 0;
$results = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $results = getQueryResults($conn, $facturacionMinima);
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
            <form method="POST" class="mr-4">
                <button type="submit" name="consulta1" class="btn btn-primary">Marcas & Vehiculos</button>
            </form>
            <form method="POST" class="form-inline">
                <label for="facturacionMinima" class="mr-2"><b>Cantidad Mínima Facturación:</b></label>
                <div class="input-group">
                    <input type="number" class="form-control" name="facturacionMinima" id="facturacionMinima" 
                        value="<?php echo $facturacionMinima; ?>">
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
