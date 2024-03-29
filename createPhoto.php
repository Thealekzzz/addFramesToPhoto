<?php

// Этот файл создает картинку из фотографии, загруженной пользователем в 7 разных рамках.

echo "{step 0\n";

$inputName = "photo"; // Название инпута с фотографией, которую нужно обернуть в рамки
$frameNames = ["brazilian barnwood.png", "classic black.png", "classic brown.png", "metallic silver.png", "modern black.png", "modern brown.png", "modern white.png"];
$framesDir = "images/frames/"; // Путь где лежат папки с рамками
$framesSize = ["20x16", "24x18", "36x24", "40x30"]; // Варианты размеров рамок
$framesOrientation = ["vert", "horiz"]; // Варианты ориентаций рамок
$frameWidths = [
    [[25,26,30,32,17,17,17],[31,32,38,40,21,21,21]],
    [[21,22,25,27,14,14,13],[28,29,33,36,18,19,19]],
    [[15,15,16,18,9,9,9],[20,20,21,25,12,12,12]],
    [[13,13,14,17,8,8,8],[17,18,19,22,11,11,11]]
]; // Ширина каждой рамки. Это нужно чтобы уменьшить картинку, которая оборачивается в рамку и не считать заново количество пикселей, насколько ее нужно уменьшить



echo "step 1\n";

$uploadedFilename = "";  // Позже записываю название загруженного файла без расширения
$fontPath = __DIR__ . "/Montserrat-ExtraBold.ttf";

echo "step 2\n";

$factor = 2;  // Во сколько раз надо увеличить размер изображений с рамками
$gap = 20 * $factor;  // Расстояния между блоками с картинками и текстом
$nameMargin = 10 * $factor;  // Отступ от текста сверху
$fontSize = 14 * $factor;  // Размер текста в пикселях(?)

echo "step 3\n";

$count = 0;  // Счетчик количества записанный в финальный файл картинок для корректной итоговой записи

// Путь до папки с выбранными рамками
$currentFramesDir = $framesDir . $framesSize[$_POST["size"]] . "/" . $framesOrientation[$_POST["orientation"]] . "/";

// Получаю размер первой рамки, умножаю на factor и удаляю Temp
$frameDefaultDimTemp = getimagesize($currentFramesDir . $frameNames[0]);
$frameDefaultDim = [$frameDefaultDimTemp[0] * $factor, $frameDefaultDimTemp[1] * $factor];
unset($frameDefaultDimTemp);

echo "step 4\n";

// Сохраняю файл на сервер и сохраняю нужную инфу
if (move_uploaded_file($_FILES[$inputName]["tmp_name"], 'images/photos/'.$_FILES[$inputName]["name"])) {
    echo "step 5\n";

    $uploadedPhotoName = $_FILES[$inputName]["name"]; // Название фото с расширением
    $uploadedPhotoNameWoE = pathinfo($uploadedPhotoName)['filename']; // Название фото без расширения

    $uploadedPhotoPath = 'images/photos/'.$uploadedPhotoName; // Путь до фото
    $uploadedPhotoType = mime_content_type($uploadedPhotoPath); // Тип фото
    $uploadedPhotoDim = getimagesize($uploadedPhotoPath); // Разрешение фото
    
    $destinationDirectory = "images/results/" . $uploadedPhotoNameWoE . "/"; // Директория куда будут записаны все итоговые файлы

} else {
    // echo "Файл не скопирован";
    echo "перемещение файла не удалось";
    echo "{noDownload";
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
        echo "Ошибка: Формат файла не распознан (" . $uploadedPhotoType . ")";
        echo "{noDownload";
        return 0;

}

echo "step 6\n";


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

echo "step 7\n";


// Создаю итоговую картинку и заливаю её белым цветом
$destinationFile = imagecreatetruecolor($destinationPhotoDim[0], $destinationPhotoDim[1]);
imagefill($destinationFile, 0, 0, imagecolorallocate($destinationFile, 255, 255, 255));

// Создание переменной цвета для текста
$color = imagecolorallocate($destinationFile, 30, 30, 30);

// Создаю папку для итоговых файлов, если её еще нет
if (!file_exists($destinationDirectory)) {
    mkdir($destinationDirectory, 0777, true);
}

echo "step 8\n";


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

echo "step 9\n";

imagejpeg($destinationFile, $destinationDirectory . $uploadedPhotoNameWoE . "_FRAMED.jpg");


// Удаляю созданные из картинок объекты
imagedestroy($uploadedPhoto);
imagedestroy($destinationFile);
imagedestroy($framePhoto);
imagedestroy($framePhotoTemp);