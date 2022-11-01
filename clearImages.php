<?php

// Все картинки, загруженные пользователями, сохраняются на сервере, поэтому
// этот php нужен для удаления всех этих картинок.

// Для запуска этого файла в main.js есть функция removeAllImages()


$photosDirectories = glob("images/photos/*"); // Пути до загуженных файлов
$resultsDirectories = glob("images/results/*"); // Пути до папок с результатами

foreach ($photosDirectories as $photoDirectory) {
    if (file_exists($photoDirectory)) {
        unlink($photoDirectory); // Удаление загруженной фотографии
    }

    $currentResultDirectory = "images/results/" . pathinfo($photoDirectory)['filename'];
    
    $currentResultFiles = glob($currentResultDirectory . "/*");

    foreach ($currentResultFiles as $currentResultFile) {
        if (file_exists($currentResultFile)) {
            unlink($currentResultFile); // Удаление результирующего файла
        }
    }

    if (file_exists($currentResultDirectory)) {
        rmdir($currentResultDirectory); // Удаление текущей папки с результатами
    }
}

