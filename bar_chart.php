<?php
// Установка размеров диаграммы и картинки
$width = 800;
$height = 500;
$offsetY = 100;
$offsetX = 100; 
$image_width = $width + $offsetY * 2;
$image_height = $height + $offsetX * 2;

$font = 4; // Размер шрифта для надписей
$maxHeight = 100; // Максимальная высота столбца в относительных единицах
$px_ratio = $height / ($maxHeight * 2); // Пикселей на одну единицу высоты столбца

// Генерация случайного количества столбцов
$columns = rand(5, 12);
$barWidth = $width / ($columns * 2);

// Создание изображения
$image = imagecreatetruecolor($image_width, $image_height);

// Заполнение фона серым цветом и добавление чёрной рамки
$light_gray = imagecolorallocate($image, 245, 245, 245);
$black = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, $image_width - 1, $image_height - 1, $light_gray);
imagerectangle($image, 0, 0, $image_width - 1, $image_height - 1, $black);

// Рисование оси X и Y
imageline($image, $offsetX, $offsetY, $offsetX, $height + $offsetY, $black); // Ось Y
imageline($image, $offsetX, $offsetY + $height / 2, $offsetX + $width, $offsetY + $height / 2, $black); // Ось X

// Добавление надписей к оси Y
for ($i = -$maxHeight; $i <= $maxHeight; $i += 20) {
    $y = $height / 2 + $offsetY - $i * $px_ratio;
    $textWidth = imagefontwidth($font) * strlen("". $i);
    $textHeight = imagefontwidth($font);
    imagestring($image, $font, $offsetX - $textWidth - 3, $y - $textHeight, $i, $black);
}

// Генерация столбцов
$barData = [];
for ($i = 0; $i < $columns; $i++) {
    // Генерация случайной высоты и цвета для каждого столбца
    $barHeight = rand(-$maxHeight, $maxHeight); // Высота может быть отрицательной
    $color = imagecolorallocate($image, rand(20, 220), rand(20, 220), rand(20, 220));
    $barData[] = ['height' => $barHeight, 'px_height' => $barHeight * $px_ratio, 'color' => $color];
}

// Рисование столбцов
foreach ($barData as $i => $data) {
    $x1 = intval($i * $barWidth * 2 + $offsetX + $barWidth / 2);
    $y1 = intval($height / 2 + $offsetY);
    $x2 = intval($i * $barWidth * 2 + $offsetX + $barWidth * 1.5);
    $y2 = intval($height / 2 + $offsetY - $data['px_height']);
    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $data['color']);

    // Добавление значения над столбцом
    $textWidth = imagefontwidth($font) * strlen($data['height']);
    $textHeight = imagefontheight($font);
    $xText = intval($x1 + $barWidth / 2 - $textWidth / 2);
    $yText = $data['height'] >= 0 ? $y2 - $textHeight : $y2;
    imagestring($image, $font, $xText, $yText, $data['height'], $black);

    // Добавление обводки столбца
    imagerectangle($image, $x1, $y1, $x2, $y2, $black);
}

// Сохранение изображения
imagepng($image, 'bar_chart.png');

// Освобождение памяти
imagedestroy($image);

// Вывод изображения
echo '<img src="bar_chart.png" alt="Bar Chart">';
?>
