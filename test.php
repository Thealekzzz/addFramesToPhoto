<?php

// unlink("images/results/IMG5522_OptionTypeImage (2).jpg"); // Удаление файла
// echo "Файл удалён";


$frameImageRaw = imagecreatefrompng("images/frames/40x30/horiz/brazilian barnwood.png");

$frameImage = imagecreate(358, 274);  // Создаем фото с заполнением его черным, поэтому не будет видно фото
$frameDim = getimagesize("images/frames/40x30/horiz/brazilian barnwood.png");
imagecopyresized($frameImage, $frameImageRaw, 0, 0, 0, 0, 358, 274, $frameDim[0], $frameDim[1]);

for($i = 1; $i < 40; $i++) {
    echo imagecolorat($frameImage, 40, $i) . " " . $i . "   ";
}


