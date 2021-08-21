<?php
// var_dump($_FILES);
echo "<br>";
$inputName = "photo";
$frameNames = ["OptionTypeImage.png", "OptionTypeImage1.png"];
$framesDir = "images/frames/";

$padding = 5;



function print_files() {
    foreach($_FILES as $input_key => $filename) {
        echo "<b> INPUT NAME: " . $input_key . "</b><br>";
        foreach($filename as $key => $value) {
            echo $key . " : " . $value . "<br>";
        }
    }
}

function printbr($str) {
    echo "<br><br>";
    var_dump($str);
    echo "<br><br>";
}


// $frameImage = imagecreatefrompng($framesDir . "OptionTypeImage.png");

// $frameWidth = 0;
// do {
//     $frameWidth++;
// } while (imagecolorat($frameImage, 40, $frameWidth) < 2130706400);

// printbr($frameWidth);





if (move_uploaded_file($_FILES[$inputName]["tmp_name"], 'images/photos/'.$_FILES[$inputName]["name"])) {
    $photoName = $_FILES[$inputName]["name"];
    $photoPath = 'images/photos/'.$photoName;
    $photoType = mime_content_type($photoPath);
    // echo "Файл скопирован в " . $photoPath;
} else {
    echo "Файл не скопирован";
}


foreach ($frameNames as $frameName) {
    // Получение разрешения текущей рамки
    $frameDim = getimagesize($framesDir . $frameName);

    // Получение разрешения загруженной картинки
    $uploadedPhotoDim = getimagesize($photoPath);



    // Создаю пустую картинку с размерами рамки
    $blankImage = imagecreatetruecolor($frameDim[0], $frameDim[1]);

    // Создаю картинку на основе текущей рамки
    $frameImage = imagecreatefrompng($framesDir . $frameName);

    // Создаю картинку на основе загруженной фотки в зависимости от расширание
    switch ($photoType) {

        case "image/png":
            $uploadedPhoto = imagecreatefrompng($photoPath);
            break;

        case "image/jpeg":
            $uploadedPhoto = imagecreatefromjpeg($photoPath);
            break;

        default:
            
            break;

    }

    // Вычисляем ширину рамки (самой доски то есть)
    $frameWidth = 0;
    while (imagecolorat($frameImage, 40, $frameWidth) < 2130706400) {
        $frameWidth++;
    };
    $frameWidth -= 2;

    
    

    // Расчёт разрешения для уменьшенной картинки, чтобы она полностью умещалась в пустом поле рамки
    $innerDim = [$frameDim[0] - 2 * $frameWidth, $frameDim[1] - 2 * $frameWidth];

    if ($uploadedPhotoDim[0] / $innerDim[0] > $uploadedPhotoDim[1] / $innerDim[1]) {
        $uploadedPhotoCroppedDim = [round($uploadedPhotoDim[0] / $uploadedPhotoDim[1] * $innerDim[1]), $innerDim[1]];
    } else {
        $uploadedPhotoCroppedDim = [$innerDim[0], round($uploadedPhotoDim[1] / $uploadedPhotoDim[0] * $innerDim[0])];
    }



    
    // Добавление загруженной картинки (предварительно изменив её размер)
    imagecopyresized($blankImage, $uploadedPhoto, $frameWidth, $frameWidth, 0, 0, $uploadedPhotoCroppedDim[0], $uploadedPhotoCroppedDim[1], $uploadedPhotoDim[0], $uploadedPhotoDim[1]);
    
    // Добавление текущей рамки
    imagecopy($blankImage, $frameImage, 0, 0, 0, 0, $frameDim[0], $frameDim[1]);
    
    $resultName = 'images/results/' . substr($photoName, 0, -4) . '_' . substr($frameName, 0, -4) . '.jpg';
    
    // Сохранение сделанной картинки
    imagejpeg($blankImage, $resultName);

    imagedestroy($blankImage);
    imagedestroy($frameImage);
    imagedestroy($uploadedPhoto);
}




// $image = imagecreatefromjpeg($photoPath);
// $src_image = imagecreatefromjpeg($photoPath2);

// $image_dim = getimagesize($photoPath);
// $src_dim = getimagesize($photoPath2);

// imagecopy($image, $src_image, 50, 50, 0, 0, $src_dim[0], $src_dim[1]);

// imagejpeg($image, 'res.jpg');

// imagedestroy($image);
// imagedestroy($src_image);

// echo "<img src='res.jpg'>";

