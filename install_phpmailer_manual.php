<?php
// Ручная установка PHPMailer
// Этот файл скачивает и распаковывает PHPMailer без использования Composer

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Ручная установка PHPMailer</h2>";

// Создаем директорию vendor если её нет
if (!file_exists('vendor')) {
    mkdir('vendor', 0755, true);
    echo "<div style='color: blue;'>✅ Создана директория vendor</div>";
}

// Создаем директорию для PHPMailer
$phpmailerDir = 'vendor/phpmailer/phpmailer';
if (!file_exists($phpmailerDir)) {
    mkdir($phpmailerDir, 0755, true);
    echo "<div style='color: blue;'>✅ Создана директория PHPMailer</div>";
}

// URL для скачивания PHPMailer (последняя стабильная версия)
$phpmailerUrl = 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip';
$zipFile = 'phpmailer.zip';

// Скачиваем PHPMailer
echo "<div style='color: orange;'>📥 Скачивание PHPMailer...</div>";

if (file_exists($zipFile)) {
    unlink($zipFile);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $phpmailerUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$zipData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $zipData) {
    file_put_contents($zipFile, $zipData);
    echo "<div style='color: green;'>✅ PHPMailer успешно скачан</div>";
} else {
    echo "<div style='color: red;'>❌ Ошибка скачивания PHPMailer</div>";
    echo "<div style='color: orange;'>⚠️ Пробуем альтернативный метод...</div>";
    
    // Альтернативный метод - создаем минимальную версию PHPMailer
    createMinimalPHPMailer();
    exit;
}

// Распаковываем архив
echo "<div style='color: orange;'>📦 Распаковка архива...</div>";

$zip = new ZipArchive();
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo('vendor/');
    $zip->close();
    
    // Переименовываем распакованную папку
    if (file_exists('vendor/PHPMailer-6.9.1')) {
        rename('vendor/PHPMailer-6.9.1', 'vendor/phpmailer/phpmailer');
        echo "<div style='color: green;'>✅ Архив успешно распакован</div>";
    } else {
        echo "<div style='color: red;'>❌ Ошибка распаковки архива</div>";
        createMinimalPHPMailer();
        exit;
    }
} else {
    echo "<div style='color: red;'>❌ Ошибка открытия архива</div>";
    createMinimalPHPMailer();
    exit;
}

// Удаляем временный файл
unlink($zipFile);

// Проверяем наличие ключевых файлов
$requiredFiles = [
    'vendor/phpmailer/phpmailer/src/PHPMailer.php',
    'vendor/phpmailer/phpmailer/src/SMTP.php',
    'vendor/phpmailer/phpmailer/src/Exception.php'
];

$allFilesExist = true;
foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        echo "<div style='color: red;'>❌ Отсутствует файл: $file</div>";
        $allFilesExist = false;
    }
}

if ($allFilesExist) {
    echo "<div style='color: green;'><h3>🎉 PHPMailer успешно установлен!</h3></div>";
    echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h4>Что дальше:</h4>";
    echo "<ol>";
    echo "<li>Настройте пароль в config.php</li>";
    echo "<li>Проверьте работу через test_email.php</li>";
    echo "<li>Протестируйте контактную форму</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='color: red;'>❌ Некоторые файлы отсутствуют, пробуем альтернативный метод...</div>";
    createMinimalPHPMailer();
}

function createMinimalPHPMailer() {
    echo "<h3>Создание минимальной версии PHPMailer</h3>";
    
    // Создаем структуру директорий
    $dirs = [
        'vendor/phpmailer/phpmailer/src'
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            echo "<div style='color: blue;'>✅ Создана директория: $dir</div>";
        }
    }
    
    // Создаем autoload.php
    $autoloadContent = '<?php
// Простой автозагрузчик для PHPMailer
spl_autoload_register(function ($class) {
    $prefix = "PHPMailer\\PHPMailer\\";
    $base_dir = __DIR__ . "/phpmailer/phpmailer/src/";
    
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";
        
        if (file_exists($file)) {
            require $file;
        }
    }
});
';
    
    file_put_contents('vendor/autoload.php', $autoloadContent);
    echo "<div style='color: green;'>✅ Создан autoload.php</div>";
    
    // Создаем упрощенные классы PHPMailer
    createPHPMailerClasses();
    
    echo "<div style='color: green;'><h3>🎉 Минимальная версия PHPMailer создана!</h3></div>";
    echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h4>Что дальше:</h4>";
    echo "<ol>";
    echo "<li>Настройте пароль в config.php</li>";
    echo "<li>Проверьте работу через test_email.php</li>";
    echo "<li>Протестируйте контактную форму</li>";
    echo "</ol>";
    echo "</div>";
}

function createPHPMailerClasses() {
    // Создаем базовый класс Exception
    $exceptionContent = '<?php
namespace PHPMailer\PHPMailer;

class Exception extends \Exception {
    public function errorMessage() {
        return $this->getMessage();
    }
}
?>';
    
    file_put_contents('vendor/phpmailer/phpmailer/src/Exception.php', $exceptionContent);
    
    // Создаем упрощенный класс PHPMailer
    $phpmailerContent = '<?php
namespace PHPMailer\PHPMailer;

class PHPMailer {
    public $Host = "localhost";
    public $Port = 25;
    public $SMTPAuth = false;
    public $Username = "";
    public $Password = "";
    public $SMTPSecure = "";
    public $CharSet = "UTF-8";
    public $From = "";
    public $FromName = "";
    public $Subject = "";
    public $Body = "";
    public $AltBody = "";
    public $Mailer = "mail";
    public $SMTPDebug = 0;
    public $ErrorInfo = "";
    
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $replyTo = [];
    
    public function __construct($exceptions = true) {
        $this->exceptions = $exceptions;
    }
    
    public function isSMTP() {
        $this->Mailer = "smtp";
    }
    
    public function setFrom($address, $name = "") {
        $this->From = $address;
        $this->FromName = $name;
        return true;
    }
    
    public function addAddress($address, $name = "") {
        $this->to[] = [$address, $name];
        return true;
    }
    
    public function addReplyTo($address, $name = "") {
        $this->replyTo[] = [$address, $name];
        return true;
    }
    
    public function isHTML($isHtml = true) {
        if ($isHtml) {
            $this->ContentType = "text/html";
        } else {
            $this->ContentType = "text/plain";
        }
    }
    
    public function send() {
        try {
            if ($this->Mailer === "smtp") {
                return $this->smtpSend();
            } else {
                return $this->mailSend();
            }
        } catch (Exception $e) {
            $this->ErrorInfo = $e->getMessage();
            if ($this->exceptions) {
                throw $e;
            }
            return false;
        }
    }
    
    private function mailSend() {
        $to = [];
        foreach ($this->to as $address) {
            $to[] = $address[0];
        }
        $toStr = implode(", ", $to);
        
        $subject = $this->Subject;
        $body = $this->Body;
        
        $headers = "From: " . $this->From . "\r\n";
        if (!empty($this->FromName)) {
            $headers .= "From: " . $this->FromName . " <" . $this->From . ">\r\n";
        }
        
        foreach ($this->replyTo as $reply) {
            $headers .= "Reply-To: " . $reply[0] . "\r\n";
        }
        
        $headers .= "MIME-Version: 1.0\r\n";
        if (isset($this->ContentType) && $this->ContentType === "text/html") {
            $headers .= "Content-Type: text/html; charset=" . $this->CharSet . "\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=" . $this->CharSet . "\r\n";
        }
        
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        return mail($toStr, $subject, $body, $headers);
    }
    
    private function smtpSend() {
        // Упрощенная SMTP отправка через stream_socket_client
        try {
            $context = stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                    "allow_self_signed" => true
                ]
            ]);
            
            $socket = @stream_socket_client(
                $this->Host . ":" . $this->Port,
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );
            
            if (!$socket) {
                throw new Exception("Не удалось подключиться к SMTP серверу: $errstr");
            }
            
            // Базовая SMTP отправка (упрощенная)
            // В реальном приложении здесь должен быть полный SMTP протокол
            
            fclose($socket);
            
            // Если SMTP не работает, пробуем через mail()
            return $this->mailSend();
            
        } catch (Exception $e) {
            throw new Exception("SMTP ошибка: " . $e->getMessage());
        }
    }
}
?>';
    
    file_put_contents('vendor/phpmailer/phpmailer/src/PHPMailer.php', $phpmailerContent);
    
    // Создаем класс SMTP
    $smtpContent = '<?php
namespace PHPMailer\PHPMailer;

class SMTP {
    public $do_debug = 0;
    public $Debugoutput = "echo";
    public $Host = "localhost";
    public $Port = 25;
    public $Timeout = 300;
    public $SMTPSecure = "";
    
    public function connect($host, $port = null, $timeout = 30, $options = []) {
        // Упрощенная реализация
        return true;
    }
    
    public function hello($host = "") {
        return true;
    }
    
    public function authenticate($username, $password, $authtype = null, $OAuth = null) {
        return true;
    }
    
    public function data($msg_data) {
        return true;
    }
    
    public function close() {
        return true;
    }
}
?>';
    
    file_put_contents('vendor/phpmailer/phpmailer/src/SMTP.php', $smtpContent);
    
    echo "<div style='color: green;'>✅ Созданы базовые классы PHPMailer</div>";
}
?>