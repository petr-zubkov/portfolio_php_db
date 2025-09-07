<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$database = new Database();
$db = $database->getConnection();

// Получаем текущие данные
$query = "SELECT * FROM about WHERE id = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$about = $stmt->fetch(PDO::FETCH_ASSOC);

// Устанавливаем значения по умолчанию
if (!$about) {
    $about = [
        'title' => '',
        'description' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование "Обо мне"</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Редактирование раздела "Обо мне"</h1>
        
        <!-- Сообщения об ошибках/успехе -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Данные успешно сохранены!</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                switch ($_GET['error']) {
                    case 'empty_fields': echo 'Поля не могут быть пустыми!'; break;
                    case 'save_failed': echo 'Ошибка сохранения данных!'; break;
                    default: echo 'Произошла ошибка!';
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Форма редактирования -->
        <form action="../handlers/about.php" method="POST">
            <div class="form-group">
                <label for="title">Заголовок</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?= htmlspecialchars($about['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?= 
                    htmlspecialchars($about['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</body>
</html>