<?php

// var_dump(phpinfo());
// phpinfo();
// return 0;

var_dump("step 0");

$inputName = "photo";
$frameNames = ["brazilian barnwood.png", "classic black.png", "classic brown.png", "metallic silver.png", "modern black.png", "modern brown.png", "modern white.png"];
$framesDir = "images/frames/";
$framesSize = ["20x16", "24x18", "36x24", "40x30"];
$framesOrientation = ["vert", "horiz"];
$frameWidths = [
    [[27,26,30,32,17,17,17],[33,32,38,40,21,21,21]],
    [[23,22,25,27,14,14,13],[30,29,33,36,18,19,19]],
    [[15,15,16,18,9,9,9],[20,20,21,25,12,12,12]],
    [[14,13,14,17,8,8,8],[18,18,19,22,11,11,11]]
]; // Ширины каждой рамки

$tf = fopen("data.txt", "at");
$textToWrite = $_FILES[$inputName]["name"] . " : " . $_POST["OS"] . "\n";
fwrite($tf, $textToWrite);
fclose($tf);

var_dump("step 1");

$uploadedFilename = "";  // Позже записываю название загруженного файла без расширения
$fontPath = __DIR__ . "/Montserrat-ExtraBold.ttf";

var_dump("step 2");

$factor = 2;  // Во сколько раз надо увеличить размер изображений с рамками
$gap = 20 * $factor;  // Расстояния между блоками с картинками и текстом
$nameMargin = 10 * $factor;  // Отступ от текста сверху
$fontSize = 14 * $factor;  // Размер текста в пикселях(?)

var_dump("step 3");

$count = 0;  // Счетчик количества записанный в финальный файл картинок для корректной итоговой записи

// Путь до папки с выбранными рамками
$currentFramesDir = $framesDir . $framesSize[$_POST["size"]] . "/" . $framesOrientation[$_POST["orientation"]] . "/";

// Получаю размер первой рамки, умножаю на factor и удаляю Temp
$frameDefaultDimTemp = getimagesize($currentFramesDir . $frameNames[0]);
$frameDefaultDim = [$frameDefaultDimTemp[0] * $factor, $frameDefaultDimTemp[1] * $factor];
unset($frameDefaultDimTemp);

var_dump("step 4");

// Сохраняю файл на сервер и сохраняю нужную инфу
if (move_uploaded_file($_FILES[$inputName]["tmp_name"], 'images/photos/'.$_FILES[$inputName]["name"])) {
    var_dump("step 5");

    $uploadedPhotoName = $_FILES[$inputName]["name"]; // Название фото с расширением
    $uploadedPhotoNameWoE = pathinfo($uploadedPhotoName)['filename']; // Название фото без расширения

    $uploadedPhotoPath = 'images/photos/'.$uploadedPhotoName; // Путь до фото
    $uploadedPhotoType = mime_content_type($uploadedPhotoPath); // Тип фото
    $uploadedPhotoDim = getimagesize($uploadedPhotoPath); // Разрешение фото
    
    $destinationDirectory = "images/results/" . $uploadedPhotoNameWoE . "/"; // Директория куда будут записаны все итоговые файлы

} else {
    // echo "Файл не скопирован";
    var_dump("перемещение файла не удалось");
    return 0;
}

// Создаю картинку на основе загруженной фотки в зависимости от расширения
switch ($uploadedPhotoType) {

    case "image/png":
        $uploadedPhoto = imagecreatefrompng($uploadedPhotoPath);
        break;

    case "image/jpeg":
        $uploadedPhoto = imagecreatefromjpeg($uploadedPhotoPath);
        break;

    case "image/webp":
        $uploadedPhoto = imagecreatefromwebp($uploadedPhotoPath);
        break;

    case "image/wbmp":
        $uploadedPhoto = imagecreatefromwbmp($uploadedPhotoPath);
        break;

    default:
        var_dump("Ошибка: Формат файла не распознан");
        return 0;

}

var_dump("step 6");


// Если фото горизонтальное, делаю константы для итогового фото
if ($_POST["orientation"]) {
    // Записываю размер итоговой картинки (Первая - горизонтальная (4 на 2), вторая - вертикальная (2 на 4))
    $destinationPhotoDim = [$frameDefaultDim[0] * 2 + 3 * $gap, ($frameDefaultDim[1] + $nameMargin + $fontSize) * round(7 / 2) + (round(7 / 2) + 1) * $gap];
    $orient = 2; // переменная для корректной записи картинок в итоговое фото

} else {
    // Записываю размер итоговой картинки (Первая - горизонтальная (4 на 2), вторая - вертикальная (2 на 4))
    $destinationPhotoDim = [$frameDefaultDim[0] * round(7 / 2) + (round(7 / 2) + 1) * $gap, $frameDefaultDim[1] * 2 + 3 * $gap + 2 * $nameMargin + 2 * $fontSize];
    $orient = 4; // переменная для корректной записи картинок в итоговое фото

}

var_dump("step 7");


// Создаю итоговую картинку и заливаю её белым цветом
$destinationFile = imagecreatetruecolor($destinationPhotoDim[0], $destinationPhotoDim[1]);
imagefill($destinationFile, 0, 0, imagecolorallocate($destinationFile, 255, 255, 255));

// Создание переменной цвета для текста
$color = imagecolorallocate($destinationFile, 30, 30, 30);

// Создаю папку для итоговых файлов, если её еще нет
if (!file_exists($destinationDirectory)) {
    mkdir($destinationDirectory, 0777, true);
}

var_dump("step 8");


// Создаю объект для фото рамок в цикле
$framePhoto = imagecreatetruecolor($frameDefaultDim[0], $frameDefaultDim[1]);

foreach ($frameNames as $frameNumber => $frameName) {
    $currentFramePhotoPath = $currentFramesDir . $frameName; // Путь к текущей рамке
    $framePhotoDim = getimagesize($currentFramePhotoPath);

    // Фото остается своего размера, но при добавлении его на destinationFile меняю на frameDefaultDim
    $framePhotoTemp = imagecreatefrompng($currentFramePhotoPath);

    // Вычисляем ширину рамки (самой доски то есть)
    // $frameWidth = 5;
    // while (imagecolorat($framePhotoTemp, 40, $frameWidth) < 2130706400) {
    //     $frameWidth++;
    //     if ($frameWidth > 40) {
    //         $frameWidth = 7;
    //         break;
    //     }
    // }
    // $frameWidth -= 1; // На всякий случай

    $frameWidth = $frameWidths[$_POST["size"]][$_POST["orientation"]][$frameNumber];
    $frameWidth *= $factor; // Чтобы пропорционально увеличивать отступы


    // Считаю размеры внутренней части рамки
    $innerDim = [$frameDefaultDim[0] - 2 * $frameWidth, $frameDefaultDim[1] - 2 * $frameWidth];
    
    // Считаю разрешение картинки чтобы она полностью умещалась внутри рамки
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


    
    $text = strtoupper(pathinfo($frameName)['filename']); // Получаю название рамки без расширения


    // Добавление загруженной картинки (с изменением её размера)
    imagecopyresized($framePhoto, $uploadedPhoto, $offsetCroppedDim[0], $offsetCroppedDim[1], 0, 0, $uploadedPhotoCroppedDim[0], $uploadedPhotoCroppedDim[1], $uploadedPhotoDim[0], $uploadedPhotoDim[1]);

    // Добавление текущей рамки (с изменением её размера)
    imagecopyresized($framePhoto, $framePhotoTemp, 0, 0, 0, 0, $frameDefaultDim[0], $frameDefaultDim[1], $framePhotoDim[0], $framePhotoDim[1]);

    // Добавление текущей картинки на финальное фото
    imagecopy($destinationFile, $framePhoto, $gap + ($gap + $frameDefaultDim[0]) * ($count % $orient), $gap + ($gap + $frameDefaultDim[1] + $fontSize + $nameMargin) * floor($count / $orient), 0, 0, $frameDefaultDim[0], $frameDefaultDim[1]);

    // Добавление текста
    imagettftext($destinationFile, $fontSize, 0, $gap + ($gap + $frameDefaultDim[0]) * ($count % $orient), ($gap + $frameDefaultDim[1] + $fontSize + $nameMargin) * (1 + floor($count / $orient)), $color, $fontPath, $text);
    $count++;


    imagejpeg($framePhoto, $destinationDirectory . $uploadedPhotoNameWoE . "_" . $frameName);
}

var_dump("step 9");

imagejpeg($destinationFile, $destinationDirectory . $uploadedPhotoNameWoE . "_FRAMED.jpg");


// Удаляю созданные из картинок объекты
imagedestroy($uploadedPhoto);
imagedestroy($destinationFile);
imagedestroy($framePhoto);
imagedestroy($framePhotoTemp);