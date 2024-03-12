<?php
session_start();

// Verificar si hay tareas en la sesión; si no, inicializar con un arreglo vacío
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Quitamos posibles espacios vacíos en la cadena
    $newTask = isset($_POST['newTask']) ? trim($_POST['newTask']) : '';

    // Manejar adición de tarea
    if(isset($_POST['type']) && $_POST['type'] == 'add' && !empty($newTask)) {
        $_SESSION['tasks'][] = $newTask;
    }

    // Manejar eliminación de una tarea específica
    if(isset($_POST['type']) && $_POST['type'] == 'delete' && isset($_POST['taskToDelete'])) {
        $taskToDelete = $_POST['taskToDelete'];
        $_SESSION['tasks'] = array_filter($_SESSION['tasks'], function($task) use ($taskToDelete) {
            return $task !== $taskToDelete;
        });
    }

    // Si se quiere limpiar la lista de tareas
    if (isset($_POST['clearTasks'])) {
        $_SESSION['tasks'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container m-4">
    <h2>Lista de Tareas</h2>
    <div class="row my-4">
        <div class="col-md-6">
            <!-- Formulario para añadir una nueva tarea -->
            <form action="" method="POST">
                <input type="hidden" name="type" value="add">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Tarea" name="newTask" id="newTask">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Añadir Tarea</button>
                    </div>
                </div>
            </form>

            <!-- Lista de tareas con botón de eliminar individual, estilo similar al formulario de añadir tarea -->
            <?php foreach ($_SESSION['tasks'] as $task): ?>
            <form action="" method="POST" >
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="<?php echo htmlspecialchars($task); ?>" readonly>
                    <div class="input-group-append">
                        <input type="hidden" name="type" value="delete">
                        <input type="hidden" name="taskToDelete" value="<?php echo htmlspecialchars($task); ?>">
                        <button type="submit" class="btn btn-warning">Borrar</button>
                    </div>
                </div>
            </form>
            <?php endforeach; ?>

            <!-- Botón para limpiar la lista de tareas -->
            <form action="" method="POST">
                <button name="clearTasks" value="true" class="btn btn-danger mt-3" type="submit">Borrar Todas</button>
            </form>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>
