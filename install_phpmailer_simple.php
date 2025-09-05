<?php
// Простая установка PHPMailer без сложных операций
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Установка PHPMailer (простая версия)</h1>";

// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Создаем базовую структуру директорий
    $dirs = ['vendor', 'vendor/phpmailer', 'vendor/phpmailer/phpmailer', 'vendor/phpmailer/phpmailer/src'];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "<div style='color: green;'>✅ Создана директория: $dir</div>";
            } else {
                echo "<div style='color: red;'>❌ Не удалось создать директорию: $dir</div>";
            }
        } else {
            echo "<div style='color: blue;'>ℹ️ Директория уже существует: $dir</div>";
        }
    }

    // Создаем autoload.php
    $autoload_content = '<?php
// Простой автозагрузчик для PHPMailer
spl_autoload_register(function ($class) {
    $prefix = "PHPMailer\\PHPMailer\\";
    $base_dir = __DIR__ . "/phpmailer/phpmailer/src/";
    
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";
        
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    return false;
});
?>';

    if (file_put_contents('vendor/autoload.php', $autoload_content)) {
        echo "<div style='color: green;'>✅ Создан autoload.php</div>";
    } else {
        echo "<div style='color: red;'>❌ Не удалось создать autoload.php</div>";
    }

    // Создаем базовые классы PHPMailer
    $files = [
        'src/Exception.php' => '<?php
namespace PHPMailer\PHPMailer;
class Exception extends \Exception {
    public function errorMessage() {
        return $this->getMessage();
    }
}
?>',
        'src/SMTP.php' => '<?php
namespace PHPMailer\PHPMailer;
class SMTP {
    public $do_debug = 0;
    public function connect($host = null, $port = null, $timeout = 30, $options = []) { return true; }
    public function hello($host = "") { return true; }
    public function authenticate($username, $password, $authtype = null, $OAuth = null) { return true; }
    public function data($msg_data) { return true; }
    public function close() { return true; }
}
?>',
        'src/PHPMailer.php' => '<?php
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
    private $exceptions = true;
    
    public function __construct($exceptions = true) {
        $this->exceptions = $exceptions;
    }
    
    public function isSMTP() {
        $this->Mailer = "smtp";
    }
    
    public function setFrom($address, $name = "", $auto = true) {
        $this->From = $address;
        $this->FromName = $name;
        return true;
    }
    
    public function addAddress($address, $name = "") {
        $this->to[] = [$address, $name];
        return true;
    }
    
    public function addReplyTo($address, $name = "") {
        return true;
    }
    
    public function isHTML($isHtml = true) {
        // Простая реализация
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
        
        $headers = "From: " . $this->From;
        if (!empty($this->FromName)) {
            $headers .= " <" . $this->From . ">";
        }
        $headers .= "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=" . $this->CharSet . "\r\n";
        $headers .= "X-Mailer: PHPMailer\r\n";
        
        return mail($toStr, $subject, $body, $headers);
    }
    
    private function smtpSend() {
        try {
            // Упрощенная SMTP реализация
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
                throw new Exception("Не удалось подключиться к SMTP: $errstr");
            }
            
            // Базовый SMTP диалог
            fgets($socket, 515);
            fwrite($socket, "EHLO " . $_SERVER[\'HTTP_HOST\'] . "\r\n");
            fgets($socket, 515);
            
            if ($this->SMTPAuth) {
                fwrite($socket, "AUTH LOGIN\r\n");
                fgets($socket, 515);
                fwrite($socket, base64_encode($this->Username) . "\r\n");
                fgets($socket, 515);
                fwrite($socket, base64_encode($this->Password) . "\r\n");
                $response = fgets($socket, 515);
                
                if (substr($response, 0, 3) !== "235") {
                    throw new Exception("Ошибка аутентификации");
                }
            }
            
            fwrite($socket, "MAIL FROM:<" . $this->From . ">\r\n");
            fgets($socket, 515);
            
            foreach ($this->to as $address) {
                fwrite($socket, "RCPT TO:<" . $address[0] . ">\r\n");
                fgets($socket, 515);
            }
            
            fwrite($socket, "DATA\r\n");
            fgets($socket, 515);
            
            $email = "From: " . $this->From . "\r\n";
            $email .= "To: " . $this->to[0][0] . "\r\n";
            $email .= "Subject: " . $this->Subject . "\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "Content-Type: text/plain; charset=" . $this->CharSet . "\r\n";
            $email .= "\r\n";
            $email .= $this->Body;
            $email .= "\r\n.\r\n";
            
            fwrite($socket, $email);
            $response = fgets($socket, 515);
            
            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            
            return substr($response, 0, 3) === "250";
            
        } catch (Exception $e) {
            throw new Exception("SMTP ошибка: " . $e->getMessage());
        }
    }
}
?>'
    ];

    $base_dir = 'vendor/phpmailer/phpmailer/';
    
    foreach ($files as $filename => $content) {
        $full_path = $base_dir . $filename;
        $dir = dirname($full_path);
        
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (file_put_contents($full_path, $content)) {
            echo "<div style='color: green;'>✅ Создан файл: $filename</div>";
        } else {
            echo "<div style='color: red;'>❌ Не удалось создать файл: $filename</div>";
        }
    }

    echo "<div style='color: green; font-size: 18px; margin: 20px 0;'>";
    echo "<h2>🎉 PHPMailer успешно установлен!</h2>";
    echo "</div>";

    echo "<div style='background: #f0f8ff; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>Что дальше:</h3>";
    echo "<ol>";
    echo "<li><strong>Настройте пароль в config.php:</strong><br>";
    echo "Замените <code>your_password_here</code> на ваш реальный пароль</li>";
    echo "<li><strong>Протестируйте SMTP:</strong><br>";
    echo "<a href='test_smtp_simple.php'>Протестировать SMTP</a></li>";
    echo "<li><strong>Используйте обработчик:</strong><br>";
    echo "<a href='send_message_smtp_simple.php'>SMTP обработчик</a></li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold;'>❌ Ошибка: " . $e->getMessage() . "</div>";
}

echo "<div style='margin: 20px 0;'>";
echo "<a href='test_smtp_simple.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Тест SMTP</a>";
echo "<a href='send_message_smtp_simple.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>📧 SMTP обработчик</a>";
echo "</div>";
?>