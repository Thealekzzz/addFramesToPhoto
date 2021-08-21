<?php
// var_dump($_FILES);
echo "<br>";
$inputName = "photo";



function print_files() {
    foreach($_FILES as $input_key => $filename) {
        echo "<b> INPUT NAME: " . $input_key . "</b><br>";
        foreach($filename as $key => $value) {
            echo $key . " : " . $value . "<br>";
        }
    }
}


if (move_uploaded_file($_FILES[$inputName]["tmp_name"], 'images/photos/'.$_FILES[$inputName]["name"])) {
    $photoPath = 'images/photos/'.$_FILES[$inputName]["name"];
    // echo "Файл скопирован в " . $photoPath;
} else {
    echo "Файл не скопирован";
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], 'images/photos/'.$_FILES["file"]["name"])) {
    $photoPath2 = 'images/photos/'.$_FILES["file"]["name"];
    // echo "Файл 2 скопирован в " . $photoPath2;
} else {
    echo "Файл 2 не скопирован";
}

// header('Content-Type: image/jpeg');

$image = imagecreatefromjpeg($photoPath);
$src_image = imagecreatefromjpeg($photoPath2);

$image_dim = getimagesize($photoPath);
$src_dim = getimagesize($photoPath2);

imagecopy($image, $src_image, 50, 50, 0, 0, $src_dim[0], $src_dim[1]);

imagejpeg($image, 'res.jpg');

imagedestroy($image);
imagedestroy($src_image);

echo "<img src='res.jpg'>";   