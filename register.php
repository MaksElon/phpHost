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


    if ($_FILES["image"]["size"] > 500000) {
        $defaultError="Файл завеликий";
    }
    else{
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
                    $file_extension = strrchr($_FILES['image']['name'], ".");
                    $imageEncrypt=uniqid().$file_extension;
                    $target = $_SERVER['DOCUMENT_ROOT'].'/'.IMG_PATH.$imageEncrypt;

                    $img = $_POST['outputHidden'];
                    list(, $img) = explode(';', $img);
                    list(, $img)      = explode(',', $img);
                    $img = base64_decode($img);
                    $arr=getimagesizefromstring($img);
                    if($arr[0]>300&&$arr[1]>300) {
                        file_put_contents($target, $img);

                        compressImage(130, 130, $target);

                        $sqlPost = "INSERT INTO `tbl_user` (`email`, `password`, `image`) VALUES (?, ?, ?);";
                        $stmtPost = $dbh->prepare($sqlPost);
                        $stmtPost->execute([$email, $password, $imageEncrypt]);
                        header("Location:  index.php");
                        exit();
                    }else{
                        $defaultError="Фото замале";
                    }

                }else {
                    $defaultError='Користувач з такою поштою вже існує';
                }
            }
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
                    <input type="file" name="image" class="custom-file-input" id="customFile">
                    <label class="custom-file-label"  for="customFile">Вибрати файл</label>
                </div>
                <div>
                    <input type="hidden" id="hide" name="outputHidden"/>
                    <img id="output" src="images/default.jpg" class="offset-3" style="border-radius:0%; height: 200px !important;width: 300px !important;"/>
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
<?php include_once("cropper.php"); ?>
<?php require_once("scripts.php"); ?>
<script src="node_modules/cropperjs/dist/cropper.min.js"></script>
</body>
</html>