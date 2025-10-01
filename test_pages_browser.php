<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование страниц сайта</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .page-test {
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .page-link {
            display: inline-block;
            margin: 5px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .page-link:hover {
            background-color: #0056b3;
        }
        h1, h2 {
            color: #333;
        }
        .status {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 Тестирование страниц сайта</h1>
        
        <h2>📋 Основные страницы для проверки</h2>
        
        <div class="page-test">
            <h3>Главная страница</h3>
            <p><span class="status">Статус:</span> ✅ Исправлена (функция hex2rgb удалена)</p>
            <a href="index.php" class="page-link" target="_blank">Открыть index.php</a>
        </div>
        
        <div class="page-test">
            <h3>Портфолио</h3>
            <p><span class="status">Статус:</span> ✅ Исправлена (функция hex2rgb удалена)</p>
            <a href="portfolio.php" class="page-link" target="_blank">Открыть portfolio.php</a>
        </div>
        
        <div class="page-test">
            <h3>Профиль</h3>
            <p><span class="status">Статус:</span> ✅ Исправлена (функция hex2rgb удалена)</p>
            <a href="profile.php" class="page-link" target="_blank">Открыть profile.php</a>
        </div>
        
        <div class="page-test">
            <h3>Контакты</h3>
            <p><span class="status">Статус:</span> ✅ Исправлена (функция hex2rgb удалена)</p>
            <a href="contacts.php" class="page-link" target="_blank">Открыть contacts.php</a>
        </div>
        
        <h2>🔧 Что было исправлено</h2>
        <ul>
            <li>✅ Удалена функция <code>hex2rgb()</code> из файла <strong>portfolio.php</strong> (строки 70-84)</li>
            <li>✅ Удалена функция <code>hex2rgb()</code> из файла <strong>profile.php</strong> (строки 70-84)</li>
            <li>✅ Удалена функция <code>hex2rgb()</code> из файла <strong>contacts.php</strong> (строки 70-84)</li>
            <li>✅ Функция <code>hex2rgb()</code> осталась только в файле <strong>config.php</strong> (где и должна быть)</li>
        </ul>
        
        <h2>📝 Инструкция по проверке</h2>
        <ol>
            <li>Нажмите на каждую ссылку выше, чтобы открыть страницу в новой вкладке</li>
            <li>Убедитесь, что страница открывается без ошибки 500</li>
            <li>Проверьте, что меню и кнопки работают правильно</li>
            <li>Если все страницы открываются нормально - проблема решена!</li>
        </ol>
        
        <h2>🎯 Оригинальная проблема</h2>
        <p><strong>Было:</strong> Все опции меню (Портфолио, Профиль, Контакты) и кнопки возвращали ошибку 500 "Сайт zubkov.space пока не может обработать этот запрос".</p>
        
        <p><strong>Причина:</strong> Функция <code>hex2rgb()</code> была объявлена несколько раз в разных файлах, что вызывало фатальную ошибку PHP "Cannot redeclare hex2rgb()".</p>
        
        <p><strong>Решение:</strong> Удалены повторные объявления функции, оставлено только одно в файле config.php.</p>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #e7f3ff; border-radius: 5px;">
            <h3>🚀 Далее</h3>
            <p>После проверки всех страниц, вы можете удалить этот тестовый файл. Он нужен только для проверки исправления.</p>
        </div>
    </div>
</body>
</html>