<?php
var_dump($_FILES);
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
    echo "Файл скопирован в " . $photoPath;
} else {
    echo "Файл не скопирован";
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], 'images/photos/'.$_FILES["file"]["name"])) {
    $photoPath2 = 'images/photos/'.$_FILES["file"]["name"];
    echo "Файл 2 скопирован в " . $photoPath2;
} else {
    echo "Файл 2 не скопирован";
}

$image = imagecreatefromjpeg($photoPath);

