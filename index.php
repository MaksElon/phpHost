<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php include_once("styles.php"); ?>
</head>
<body>
    <?php
    include("navbar.php");
    include('connection_database.php');
    include_once "config.php";
    $sql = "SELECT u.email,u.id,u.image FROM tbl_user AS u";
    $stmt= $dbh->prepare($sql);
    $stmt->execute();

    ?>
    <!--<?php include_once("navbar.php"); ?>-->
    <!--<?php require("navbar.php"); ?>-->
    <!--<?php require_once("navbar.php"); ?>-->

    <table class="table table-striped">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Email</th>
            <th scope="col">Image</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while($row=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $path=IMG_PATH.$row['image'];
            ?>
            <tr class="al">
                <th scope="row"><?php echo $row['id']; ?> </th>
                <td> <?php echo $row['email']; ?> </td>
                <td><?php echo "<img class=\"imag\" src='$path'>" ?> </td>
            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>


    <?php require_once("scripts.php"); ?>
</body>
</html>