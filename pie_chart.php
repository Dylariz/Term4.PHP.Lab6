<?php
// Если был отправлен POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, какая кнопка была нажата
    if (isset($_POST['build'])) {
        // Генерируем случайные данные для диаграммы
        $data = generateRandomData();
    } elseif (isset($_POST['read'])) {
        // Читаем данные из файла
        $data = readDataFromFile('data.txt');
    }
    // Создаем и сохраняем изображение
    createAndSaveImage($data);
}

// Функция для генерации случайных данных
function generateRandomData() {
    // Генерируем случайное количество секторов
    $sectors = rand(3, 9);
    $data = [];
    $totalWeight = 0;
    for ($i = 0; $i < $sectors; $i++) {
        // Генерируем случайный вес и цвет для каждого сектора
        $weight = rand(1, 10);
        $color = [rand(20, 220), rand(20, 220), rand(20, 220)];
        $data[] = ['weight' => $weight, 'color' => $color];
        $totalWeight += $weight;
    }
    return $data;
}

// Функция для чтения данных из файла
function readDataFromFile($filename) {
    $data = [];
    $lines = file($filename);
    foreach ($lines as $line) {
        // Разбиваем строку на части и сохраняем в массив
        list($weight, $r, $g, $b) = explode(':', trim($line));
        $data[] = ['weight' => $weight, 'color' => [$r, $g, $b]];
    }
    return $data;
}

// Функция для создания и сохранения изображения
function createAndSaveImage($data) {
    // Устанавливаем размеры диаграммы и изображения
    $width = 500;
    $height = 500;
    $offsetY = 100;
    $offsetX = 100; 
    $image_width = $width + $offsetY * 2;
    $image_height = $height + $offsetX * 2;
    $font = 4;

    // Создаем изображение
    $image = imagecreatetruecolor($image_width, $image_height);
    // Заполняем фон серым цветом и добавляем черную рамку
    $light_gray = imagecolorallocate($image, 245, 245, 245);
    $black = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, $image_width - 1, $image_height - 1, $light_gray);
    imagerectangle($image, 0, 0, $image_width - 1, $image_height - 1, $black);

    // Считаем общий вес всех секторов
    $totalWeight = array_sum(array_column($data, 'weight'));
    $start = 0;
    foreach ($data as $sector) {
        // Рассчитываем угол конца сектора
        $end = $start + ($sector['weight'] / $totalWeight) * 360;
        // Создаем цвет для сектора
        $color = imagecolorallocate($image, ...$sector['color']);
        // Рисуем сектор
        imagefilledarc($image, intval($width / 2 + $offsetX), intval($height / 2 + $offsetY), intval($width), intval($height), floor($start), floor($end), $color, IMG_ARC_PIE);
        // Добавляем черную границу сектора
        imagearc($image, intval($width / 2 + $offsetX), intval($height / 2 + $offsetY), intval($width), intval($height), floor($start), floor($end), $black);

        // Добавляем значение в сектор
        $angle = deg2rad(($start + $end) / 2);
        $textX = intval(($width / 2 + $offsetX) + cos($angle) * ($width / 1.9));
        $textY = intval(($height / 2 + $offsetY) + sin($angle) * ($height / 1.9));
        $text = strval($sector['weight']);
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        imagestring($image, $font, intval($textX - $textWidth / 2), intval($textY - $textHeight / 2), $text, $black);

        $start = $end;
    }

    // Сохраняем изображение
    imagepng($image, 'pie_chart.png');
    // Освобождаем память
    imagedestroy($image);
}

?>

<!-- Форма для кнопок -->
<form method="post">
    <input type="submit" name="build" value="Построить">
    <input type="submit" name="read" value="Считать">
</form>

<!-- Выводим изображение -->
<img src="pie_chart.png" alt="Pie Chart">
