<?php
function compressImage($width, $height, $path)
{
    include_once "config.php";
    list($w,$h)= getimagesize($path);
    $maxSize=0;
    if(($w>$h) and ($width>$height))
        $maxSize=$width;
    else
        $maxSize=$height;
    $width=$maxSize;
    $height=$maxSize;
    $ration_orig=$w/$h;
    if(1>$ration_orig)
    {
        $width=ceil($height*$ration_orig);
    }
    else
    {
        $height=ceil($width/$ration_orig);
    }

    $imgString=file_get_contents($path);
    $image=imagecreatefromstring($imgString);
    $tmp=imagecreatetruecolor($width,$height);
    imagecopyresampled($tmp,$image,
        0,0,
        0,0,
        $width, $height,
        $w,$h
    );
    $file_extension = strrchr($path, ".");

    switch($file_extension)
    {
        case 'jpeg' || 'jpg':
            imagejpeg($tmp,$path,30);
            break;
        case 'image/png':
            imagepng($tmp,$path,10);
            break;
        case 'image/gif':
            imagegif($tmp,$path);
            break;
        default:
            exit;
            break;
    }
    //return $path;
    imagedestroy($image);
    imagedestroy($tmp);
}