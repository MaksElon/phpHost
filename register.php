<?php
$passwordError="";
$emailError="";
$defaultError="";
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    include('connection_database.php');
    include_once "config.php";
    include_once "compress.php";

    $email = $_POST["email"];
    $password = $_POST['password'];

    $_SERVER["email"]=$email;
    $_SERVER["password"]=$password;

    $image = $_FILES['image']['name'];
    $file_extension = strrchr($image, ".");
    $imageEncrypt=uniqid().$file_extension;
    $target = IMG_PATH.$imageEncrypt;
    if ($_FILES["image"]["size"] > 500000) {
        $defaultError="Файл завеликий";
    }
    else{
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            compressImage(130,130,$target);
            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number    = preg_match('@[0-9]@', $password);
            $emailRegex = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$email);

            if(!$emailRegex)
            {
                $emailError='Нереальна електронна пошта';
            }
            else{
                if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
                    $passwordError='Слабий пароль';
                }else{
                    $sqlGet = "SELECT u.id FROM `tbl_user` AS u WHERE u.email=? LIMIT 1";
                    $stmtGet= $dbh->prepare($sqlGet);
                    $stmtGet->execute([$email]);
                    $rows = $stmtGet->fetchAll(PDO::FETCH_ASSOC);

                    if(!$rows)
                    {
                        
                        $sqlPost = "INSERT INTO `tbl_user` (`email`, `password`, `image`) VALUES (?, ?, ?);";
                        $stmtPost= $dbh->prepare($sqlPost);
                        $stmtPost->execute([$email, $password,$imageEncrypt]);
                        echo '<script>window.location.href = "index.php";</script>';
                        exit();
                    }else {
                        $defaultError='Користувач з такою поштою вже існує';
                    }
                }
            }
        }else{
            $defaultError="Помилка в додаванні файлу";
        }
    }
}
?>
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
<?php require("navbar.php"); ?>

<div class="container">
    <div class="row">
        <h1 class="col text-center">Реєстрація</h1>
    </div>
    <div class="row pb-4">
        <div class="offset-3 col-6 align-content-center">
        <form action="register.php" method="post" enctype="multipart/form-data">
            <label class="form-text text-danger"><?php echo $defaultError?></label>
            <div class="form-group">
                <label for="exampleInputEmail1">Електронна пошта</label>
                <input value='<?php echo isset($_SERVER['email']) ? $_SERVER['email'] : ''; ?>' name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                <small id="emailHelp" class="form-text text-danger"><?php echo $emailError?></small>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Пароль</label>
                <input value='<?php echo isset($_SERVER['password']) ? $_SERVER['password'] : ''; ?>'  name="password" type="password" class="form-control" id="exampleInputPassword1" aria-describedby="passHelp">
                <small id="passHelp" class="form-text text-danger"><?php echo $passwordError?></small>
            </div>
            <div class="custom-file mb-3">
                <input type="file" onchange="loadFile(event)" name="image" class="custom-file-input" id="customFile">
                <label class="custom-file-label"  for="customFile">Вибрати файл</label>
            </div>
            <div>
                <img id="output" src="images/default.jpg" class="offset-3" style="border-radius:0%; height: 200px !important;width: 300px !important;"/>
                <script>
                    loadFile = function(event) {
                        let output = document.getElementById('output');
                        console.log(event.target.files[0]);
                        output.src = URL.createObjectURL(event.target.files[0]);
                        output.onload = function() {
                            URL.revokeObjectURL(output.src)
                        }
                    };
                </script>
            </div>
            <div class="form-group form-check offset-3 mt-3">
                <input required type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Згоден із правилами користування</label>
            </div>
            <button type="submit" class="btn mb-3 btn-primary offset-5">Надіслати</button>
        </form>
        </div>
    </div>
</div>

<?php require_once("scripts.php"); ?>
</body>
</html>