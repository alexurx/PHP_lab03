<?php
$dir = 'image/';
// Получаем список файлов в директории
$files = array_diff(scandir($dir), ['.', '..']);
// Если файлов нет, завершаем работу
if ($files === false) {
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .header {
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            background-color: white;
            border: 1px solid #000;
            display: inline-block;
            margin-bottom: 20px;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 600px;
            margin: auto;
        }
        .gallery img {
            width: 100%;
            aspect-ratio: 1 / 1; /* Делаем высоту равной ширине */
            border-radius: 10px;
            object-fit: cover; /* Заполняем контейнер */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="header">
    About Cats | News | Contacts
</div>

<h2>#cats</h2>
<p style="color: gray;">Explore a world of cats</p>

<div class="gallery">
    <?php
    // Выводим изображения
    foreach ($files as $file) {
        echo "<img src='{$dir}{$file}' alt='Cat' >";
    }
    ?>
</div>

<div class="footer">
    2025
</div>

</body>
</html>
