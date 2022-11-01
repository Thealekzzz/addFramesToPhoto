<?php

// Этот php создан для удаления конкретной картинки, загруженной пользователем и всех сгенерированных на ее основе картинок
// Он запускается автоматически, когда пользователь уходит со страницы или обновляет ее.
// Но почему-то это иногда не срабатывает, фотки не удаляются и на сервере скапливается мусор.
// Для удаления всего этого мусора есть файл clearImages.php. Для его запуска в main.js есть функция removeAllImages()


// var_dump($_POST);
$frameNames = ["brazilian barnwood.png", "classic black.png", "classic brown.png", "metallic silver.png", "modern black.png", "modern brown.png", "modern white.png"];

// Путь до итоговой директории
$finalDirectory = "images/results/" . $_POST["filename"] . "/";
// Пути дял всех файлов, которые надо удалить
$uploadedPhotoPath = "images/photos/" . $_POST["filename"] . $_POST["fileextension"];
$finalPhotoPath = $finalDirectory . $_POST["filename"] . "_FRAMED.jpg";
// Путь до отдельных файлов без названия рамки!
$separatePhotoPath = $finalDirectory . $_POST["filename"];

// echo $uploadedPhotoPath . $finalDirectory . $finalPhotoPath;

if (file_exists($uploadedPhotoPath)) {
    unlink($uploadedPhotoPath); // Удаление файла
}
if (file_exists($finalPhotoPath)) {
    unlink($finalPhotoPath); // Удаление файла
}

foreach($frameNames as $frameName) {
    if (file_exists($separatePhotoPath . "_" . $frameName)) {
        unlink($separatePhotoPath . "_" . $frameName);
    }
}

// Удаляю директорию
if (file_exists($finalDirectory)) {
    rmdir($finalDirectory);
}
