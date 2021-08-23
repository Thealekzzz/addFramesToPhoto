<?php

$inputName = "photo";
$frameNames = ["OptionTypeImage.png", "OptionTypeImage (1).png", "OptionTypeImage (2).png", "OptionTypeImage (3).png", "OptionTypeImage (4).png", "OptionTypeImage (5).png", "OptionTypeImage (6).png"];
$framesDir = "images/frames/";

$gap = 40;  // Расстояния между блоками с картинками и текстом
$nameMargin = 10;  // Отступ от текста сверху
$fontSize = 40;  // Размер текста в пикселях(?)


function print_files() {
    foreach($_FILES as $input_key => $filename) {
        echo "<b> INPUT NAME: " . $input_key . "</b><br>";
        foreach($filename as $key => $value) {
            echo $key . " : " . $value . "<br>";
        }
    }
}

function printbr($str) {
    var_dump("Собственный вывод: " . $str);
}


// $frameImage = imagecreatefrompng($framesDir . "OptionTypeImage.png");


if (move_uploaded_file($_FILES[$inputName]["tmp_name"], 'images/photos/'.$_FILES[$inputName]["name"])) {
    $photoName = $_FILES[$inputName]["name"];
    $photoPath = 'images/photos/'.$photoName;
    $photoType = mime_content_type($photoPath);
    // echo "Файл скопирован в " . $photoPath;
} else {
    // echo "Файл не скопирован";
}


foreach ($frameNames as $frameName) {
    // Получение разрешения текущей рамки - это и будет размер итоговой картинки
    $frameDim = getimagesize($framesDir . $frameName);

    // Получение разрешения загруженной картинки
    $uploadedPhotoDim = getimagesize($photoPath);

    // Записываем, вертикальная ли фотка
    $uploadedPhotoDim[0] < $uploadedPhotoDim[1] ? $isVertical = true : $isVertical = false;
    

    // Создаю картинку на основе текущей рамки, с учётом ориентации загруженной картинки
    if ($isVertical) {
        // Создаю картинку на основе текущей рамки
        $frameImageTemp = imagecreatefrompng($framesDir . $frameName);

        $frameImage = imagecreatetruecolor($frameDim[1], $frameDim[0]);
        $frameImage = imagerotate($frameImageTemp, 90, 0);

        $temp = $frameDim[0];
        $frameDim[0] = $frameDim[1];
        $frameDim[1] = $temp;
    } else {
        $frameImage = imagecreatefrompng($framesDir . $frameName);
    }

    // Создаю пустую картинку с размерами рамки
    $blankImage = imagecreatetruecolor($frameDim[0], $frameDim[1]);

    // Создаю картинку на основе загруженной фотки в зависимости от расширения
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
    // $frameWidth -= 2; // Отнимаю погрешность 2 пикселя
    

    // Расчёт разрешения для уменьшенной картинки, чтобы она полностью умещалась в пустом поле рамки
    $innerDim = [$frameDim[0] - 2 * $frameWidth, $frameDim[1] - 2 * $frameWidth];

    if ($uploadedPhotoDim[0] / $innerDim[0] > $uploadedPhotoDim[1] / $innerDim[1]) {
        $uploadedPhotoCroppedDim = [round($uploadedPhotoDim[0] / $uploadedPhotoDim[1] * $innerDim[1]), $innerDim[1]];

        // Сдвиг по х и у для уменьшенной картинки, чтобы она была по центру
        $offsetCroppedDim = [$frameWidth - ($uploadedPhotoCroppedDim[0] - $innerDim[0]) / 2, $frameWidth];
        // $offsetCroppedDim = [$frameWidth, ($uploadedPhotoCroppedDim[1] - $innerDim[1]) / 2];

    } else {
        $uploadedPhotoCroppedDim = [$innerDim[0], round($uploadedPhotoDim[1] / $uploadedPhotoDim[0] * $innerDim[0])];

        // Сдвиг по х и у для уменьшенной картинки, чтобы она была по центру
        $offsetCroppedDim = [$frameWidth, $frameWidth - ($uploadedPhotoCroppedDim[1] - $innerDim[1]) / 2];
        // $offsetCroppedDim = [($uploadedPhotoCroppedDim[0] - $innerDim[0]) / 2, $frameWidth];

    }

    
    // Добавление загруженной картинки (предварительно изменив её размер)
    imagecopyresized($blankImage, $uploadedPhoto, $offsetCroppedDim[0], $offsetCroppedDim[1], 0, 0, $uploadedPhotoCroppedDim[0], $uploadedPhotoCroppedDim[1], $uploadedPhotoDim[0], $uploadedPhotoDim[1]);
    
    // Добавление текущей рамки
    imagecopy($blankImage, $frameImage, 0, 0, 0, 0, $frameDim[0], $frameDim[1]);
    
    $resultName = 'images/results/' . substr($photoName, 0, -4) . '_' . substr($frameName, 0, -4) . '.jpg';
    
    // Сохранение сделанной картинки
    imagejpeg($blankImage, $resultName);

    imagedestroy($blankImage);
    imagedestroy($frameImage);
    imagedestroy($uploadedPhoto);
}

echo $photoPath;

