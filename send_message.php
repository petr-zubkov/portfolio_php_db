<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Простая валидация
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['error'] = 'Пожалуйста, заполните все поля';
        header('Location: index.php#contact');
        exit;
    }
    
    // В реальном проекте здесь должна быть отправка email
    // Для демонстрации просто сохраним сообщение в сессию
    
    $_SESSION['success'] = 'Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.';
    header('Location: index.php#contact');
    exit;
} else {
    header('Location: index.php');
    exit;
}
?>