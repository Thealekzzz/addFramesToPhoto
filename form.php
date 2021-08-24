<?php

$inputName = "photo";
$frameNames = ["brazilian barnwood.png", "classic black.png", "classic brown.png", "metallic silver.png", "modern black.png", "modern brown.png", "modern white.png"];
$framesDir = "images/frames/";
$framesSize = ["20x16", "24x18", "36x24", "40x30"];
$framesOrientation = ["vert", "horiz"];

$uploadedFilename = "";  // Позже записываю название загруженного файла без расширения

$gap = 20;  // Расстояния между блоками с картинками и текстом
$nameMargin = 10;  // Отступ от текста сверху
$fontSize = 40;  // Размер текста в пикселях(?)

$count = 0;  // Счетчик количества записанный в финальный файл картинок для корректной итоговой записи


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

    $uploadedFilename = pathinfo($photoName)['filename']; // Имя файла без расширения
    $currentDirectory = "images/results/" . $uploadedFilename . "/"; // Директория куда будут записаны все итоговые файлы
} else {
    // echo "Файл не скопирован";
}


// Получение разрешения загруженной картинки
$uploadedPhotoDim = getimagesize($photoPath);

// Записываем, вертикальная ли фотка
$isVertical = $uploadedPhotoDim[0] < $uploadedPhotoDim[1];

// Записываем размер, к которому будем приводить все картинки
$frameDefaultDim = getimagesize($framesDir . $framesSize[$_POST["size"]] . "/" . $framesOrientation[$_POST["orientation"]] . "/" . $frameNames[0]);

// if ($isVertical) {
//     $temp = $frameDefaultDim[0];
//     $frameDefaultDim[0] = $frameDefaultDim[1];
//     $frameDefaultDim[1] = $temp;
// }

// Записываю размер итоговой картинки
$finalFileDim = [$frameDefaultDim[0] * round(7 / 2) + (round(7 / 2) + 1) * $gap, $frameDefaultDim[1] * 2 + 3 * $gap + 2 * $nameMargin + 2 * $fontSize];

// Создаю итоговую картинку
$finalFile = imagecreatetruecolor($finalFileDim[0], $finalFileDim[1]);
imagefill($finalFile, 0, 0, imagecolorallocate($finalFile, 255, 255, 255));

// var_dump($framesDir . $framesSize[$_POST["size"]] . "/" . $framesOrientation[$_POST["orientation"]] . "/" . $frameNames[0]);
// var_dump($_POST);

if (!file_exists($currentDirectory)) {
    mkdir($currentDirectory, 0777, true);

}

foreach ($frameNames as $frameName) {
    // Путь к файлу в соответствующих папках размера и ориентации
    $currentFramePath = $framesDir . $framesSize[$_POST["size"]] . "/" . $framesOrientation[$_POST["orientation"]] . "/" . $frameName;

    // echo $currentFramePath;

    // Получение разрешения текущей рамки
    $frameDim = getimagesize($currentFramePath);
    

    // Создаю картинку на основе текущей рамки, с учётом ориентации загруженной картинки
    // if ($isVertical) {
    //     // Создаю картинку на основе текущей рамки
    //     $frameImageTemp = imagecreatefrompng($currentFramePath);

    //     $frameImageRaw = imagecreatetruecolor($frameDim[1], $frameDim[0]);
    //     $frameImageRaw = imagerotate($frameImageTemp, 90, 0);

    //     // imagealphablending($frameImageTemp, false);
    //     // imagesavealpha($frameImageTemp, true);

    //     // $frameImageRaw = imagerotate($frameImageTemp, 90, imageColorAllocateAlpha($frameImageTemp, 0, 0, 0, 127));
    //     // imagealphablending($frameImageRaw, false);
    //     // imagesavealpha($frameImageRaw, true);

    //     $temp = $frameDim[0];
    //     $frameDim[0] = $frameDim[1];
    //     $frameDim[1] = $temp;
    // } else {
    $frameImage = imagecreatefrompng($currentFramePath);
    // }

    // $frameImage = imagecreatetruecolor($frameDefaultDim[0], $frameDefaultDim[1]);  // Создаем фото с заполнением его черным, поэтому не будет видно фото
    // imagecopyresized($frameImage, $frameImageRaw, 0, 0, 0, 0, $frameDefaultDim[0], $frameDefaultDim[1], $frameDim[0], $frameDim[1]);

    // for($i = 1; $i < 40; $i++) {
    //     echo imagecolorat($frameImage, 40, $i) . " " . $i . "   ";
    // }
    


    // Создаю пустую картинку с размерами стандартной рамки
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
    $frameWidth = 5;
    while (imagecolorat($frameImage, 40, $frameWidth) < 2130706400) {
        $frameWidth++;
        if ($frameWidth > 30) {
            $frameWidth = 7;
            break;
        }
    }
    $frameWidth -= 2;



    // echo $frameWidth . "   ";

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

    // Кропаю полученную картинку до стандартного размера и помещение её на финальное фото
    imagecopyresized($finalFile, $blankImage, $gap + ($gap + $frameDefaultDim[0]) * ($count % 4), $gap + ($gap + $frameDefaultDim[1] + $fontSize + $nameMargin) * floor($count / 4), 0, 0, $frameDefaultDim[0], $frameDefaultDim[1], $frameDim[0], $frameDim[1]);
    $count++;

    
    // Сохранение сделанной картинки
    imagejpeg($blankImage, $currentDirectory . $uploadedFilename . "_" . $frameName);



    imagedestroy($blankImage);
    // imagedestroy($frameImageTemp);
    // imagedestroy($frameImageRaw);
    imagedestroy($frameImage);
    imagedestroy($uploadedPhoto);
}

$resultName = $currentDirectory . $uploadedFilename . '_' . "FINAL" . '.jpg';
imagejpeg($finalFile, $resultName);

// echo $uploadedFilename; // Отправляем только название файла

imagedestroy($finalFile);
