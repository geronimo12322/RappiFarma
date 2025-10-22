<?php
include "linkDB.php";
$db = new database();
$conn = $db->conexion();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Log In</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <form action="./index.php" method="post">
                    <div class="input-group mb-3 mt-3">
                        <span class="input-group-text">@</span>
                        <input name="email" type="text" class="form-control" placeholder="Email">
                    </div>
                    <div class="mb-3">
                        <input name="pass" type="password" class="form-control" placeholder="Password">
                    </div>
                    <button class="btn btn-outline-danger" type="submit">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>