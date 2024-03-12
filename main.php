<?php
session_start();

// Verificar si hay tareas en la sesión; si no, inicializar con un arreglo vacío
// isset verifica si la variable está definida
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Verificar si el formulario de nueva tarea ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newTask'])) {

    //Quitamos posibles espacios vacios en la cadena
    $newTask = trim($_POST['newTask']);

    if($_POST['type'] == 'add'){

        if (!empty($newTask)) {
            // Añadir la nueva tarea al arreglo de tareas en la sesión
            $_SESSION['tasks'][] = $newTask;
        }
    }

    echo $_POST['type'];

    if($_POST['type'] == 'delete'){

        foreach($_SESSION['tasks'] as $index => $task){
            if($task == $newTask) {
                unset($_SESSION['tasks'][$index]);
            }
        }
    }    

}

// Verificar si el formulario de nueva tarea ha sido enviado
/*if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newTask'])) {

    //Quitamos posibles espacios vacios en la cadena
    $newTask = trim($_POST['newTask']);

    if (!empty($newTask)) {

        foreach($_SESSION['tasks'] as $index => $task){

            if($task == $newTask) {
                unset($_SESSION['tasks'][$index]);
            }
        }
    }
}*/

// Si se quiere limpiar la lista de tareas
if (isset($_POST['clearTasks'])) {
    $_SESSION['tasks'] = [];
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
            <form action="main.php" method="POST" name = "add">
                <div class="input-group mb-3">
                    <input name="type" value = "add" hidden></input>
                    <input type="text" class="form-control" placeholder="Tarea" name="newTask" id="newTask">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" name="add">Añadir Tarea</button>
                    </div>
                </div>
            </form>
            <!-- Lista de tareas -->
            <ul id="taskList" class="list-group">
                <?php foreach ($_SESSION['tasks'] as $task): ?>
                    <!-- htmlspecialchars($task) convierte caracteres especiales a entidades HTML. 
                    Es una práctica de seguridad importante para prevenir ataques de inyección de código -->
                    <li class="list-group-item"><?php echo htmlspecialchars($task); ?></li>
                <?php endforeach; ?>
            </ul>
            <!-- Botón para limpiar la lista de tareas -->
            <form action="main.php" method="POST" name = "delete">
                <input name="type" value = "delete" hidden></input>
                <button name="clearTasks" value="true" class="btn btn-danger mt-3" type="submit">Borrar Todas</button>
                <button name="clearSelected" value="true" class="btn btn-warning mt-3" type="submit">Borrar Seleccionada</button>
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
