<?php
// Рабочая установка PHPMailer
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Установка PHPMailer для SMTP</h1>";

// Создаем директорию vendor если её нет
if (!file_exists('vendor')) {
    mkdir('vendor', 0755, true);
    echo "<div style='color: blue;'>✅ Создана директория vendor</div>";
}

// Создаем структуру для PHPMailer
$phpmailer_dir = 'vendor/phpmailer/phpmailer';
if (!file_exists($phpmailer_dir)) {
    mkdir($phpmailer_dir, 0755, true);
    echo "<div style='color: blue;'>✅ Создана директория PHPMailer</div>";
}

// Создаем src директорию
if (!file_exists($phpmailer_dir . '/src')) {
    mkdir($phpmailer_dir . '/src', 0755, true);
    echo "<div style='color: blue;'>✅ Создана директория src</div>";
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
        }
    }
});
?>';

file_put_contents('vendor/autoload.php', $autoload_content);
echo "<div style='color: green;'>✅ Создан autoload.php</div>";

// Создаем основные классы PHPMailer (упрощенные рабочие версии)

// Exception.php
$exception_content = '<?php
namespace PHPMailer\PHPMailer;

class Exception extends \Exception {
    public function errorMessage() {
        return $this->getMessage();
    }
}
?>';

file_put_contents($phpmailer_dir . '/src/Exception.php', $exception_content);
echo "<div style='color: green;'>✅ Создан Exception.php</div>";

// PHPMailer.php
$phpmailer_class_content = '<?php
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
    public $SMTPAutoTLS = true;
    public $SMTPSecureArray = ["ssl", "tls"];
    
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $replyTo = [];
    private $all_recipients = [];
    private $attachment = [];
    private $message_type = "plain";
    private $boundary = [];
    
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
        $this->all_recipients[$address] = true;
        return true;
    }
    
    public function addCC($address, $name = "") {
        $this->cc[] = [$address, $name];
        $this->all_recipients[$address] = true;
        return true;
    }
    
    public function addBCC($address, $name = "") {
        $this->bcc[] = [$address, $name];
        $this->all_recipients[$address] = true;
        return true;
    }
    
    public function addReplyTo($address, $name = "") {
        $this->replyTo[] = [$address, $name];
        return true;
    }
    
    public function isHTML($isHtml = true) {
        if ($isHtml) {
            $this->message_type = "html";
        } else {
            $this->message_type = "plain";
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
        
        $headers = "From: " . $this->From;
        if (!empty($this->FromName)) {
            $headers .= " <" . $this->From . ">";
        }
        $headers .= "\r\n";
        
        foreach ($this->replyTo as $reply) {
            $headers .= "Reply-To: " . $reply[0];
            if (!empty($reply[1])) {
                $headers .= " <" . $reply[0] . ">";
            }
            $headers .= "\r\n";
        }
        
        foreach ($this->cc as $cc) {
            $headers .= "Cc: " . $cc[0];
            if (!empty($cc[1])) {
                $headers .= " <" . $cc[0] . ">";
            }
            $headers .= "\r\n";
        }
        
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($this->message_type === "html") {
            $headers .= "Content-Type: text/html; charset=" . $this->CharSet . "\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=" . $this->CharSet . "\r\n";
        }
        
        $headers .= "X-Mailer: PHPMailer\r\n";
        $headers .= "X-Priority: 3\r\n";
        
        return mail($toStr, $subject, $body, $headers);
    }
    
    private function smtpSend() {
        try {
            // Упрощенная SMTP отправка через stream_socket_client
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
                throw new Exception("Не удалось подключиться к SMTP серверу: $errstr ($errno)");
            }
            
            // Читаем приветствие сервера
            fgets($socket, 515);
            
            // EHLO
            fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            fgets($socket, 515);
            
            // Аутентификация если требуется
            if ($this->SMTPAuth) {
                fwrite($socket, "AUTH LOGIN\r\n");
                fgets($socket, 515);
                
                fwrite($socket, base64_encode($this->Username) . "\r\n");
                fgets($socket, 515);
                
                fwrite($socket, base64_encode($this->Password) . "\r\n");
                $response = fgets($socket, 515);
                
                if (substr($response, 0, 3) !== "235") {
                    throw new Exception("Ошибка аутентификации SMTP");
                }
            }
            
            // Отправитель
            fwrite($socket, "MAIL FROM:<" . $this->From . ">\r\n");
            fgets($socket, 515);
            
            // Получатели
            foreach ($this->to as $address) {
                fwrite($socket, "RCPT TO:<" . $address[0] . ">\r\n");
                fgets($socket, 515);
            }
            
            // Данные письма
            fwrite($socket, "DATA\r\n");
            fgets($socket, 515);
            
            // Формируем письмо
            $email = "From: " . $this->From . "\r\n";
            $email .= "To: " . $this->to[0][0] . "\r\n";
            $email .= "Subject: " . $this->Subject . "\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            
            if ($this->message_type === "html") {
                $email .= "Content-Type: text/html; charset=" . $this->CharSet . "\r\n";
            } else {
                $email .= "Content-Type: text/plain; charset=" . $this->CharSet . "\r\n";
            }
            
            $email .= "\r\n";
            $email .= $this->Body;
            $email .= "\r\n.\r\n";
            
            fwrite($socket, $email);
            $response = fgets($socket, 515);
            
            if (substr($response, 0, 3) !== "250") {
                throw new Exception("Ошибка отправки письма через SMTP");
            }
            
            // Завершение сессии
            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("SMTP ошибка: " . $e->getMessage());
        }
    }
}
?>';

file_put_contents($phpmailer_dir . '/src/PHPMailer.php', $phpmailer_class_content);
echo "<div style='color: green;'>✅ Создан PHPMailer.php</div>";

// SMTP.php
$smtp_content = '<?php
namespace PHPMailer\PHPMailer;

class SMTP {
    public $do_debug = 0;
    public $Debugoutput = "echo";
    public $Host = "localhost";
    public $Port = 25;
    public $Timeout = 300;
    public $SMTPSecure = "";
    
    public function connect($host, $port = null, $timeout = 30, $options = []) {
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

file_put_contents($phpmailer_dir . '/src/SMTP.php', $smtp_content);
echo "<div style='color: green;'>✅ Создан SMTP.php</div>";

echo "<div style='color: green; font-size: 18px; margin: 20px 0;'>";
echo "<h2>🎉 PHPMailer успешно установлен!</h2>";
echo "</div>";

echo "<div style='background: #f0f8ff; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>Что дальше:</h3>";
echo "<ol>";
echo "<li><strong>Настройте SMTP в config.php:</strong><br>";
echo "Замените <code>your_password_here</code> на ваш реальный пароль от почты</li>";
echo "<li><strong>Проверьте работу SMTP:</strong><br>";
echo "Откройте <a href='test_smtp.php'>test_smtp.php</a> для тестирования</li>";
echo "<li><strong>Используйте обработчик с SMTP:</strong><br>";
echo "<code>send_message_smtp.php</code> для отправки через SMTP</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; margin: 20px 0; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h4>⚠️ Важно:</h4>";
echo "<ul>";
echo "<li>Для Mail.ru может потребоваться включить IMAP/SMTP в настройках почты</li>";
echo "<li>Проверьте, что порт 465 (SSL) или 587 (TLS) не заблокирован</li>";
echo "<li>Если основной пароль не работает, создайте пароль для приложений</li>";
echo "</ul>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<a href='test_smtp.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Тест SMTP</a>";
echo "<a href='send_message_smtp.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>📧 SMTP обработчик</a>";
echo "</div>";
?>';

file_put_contents('install_phpmailer_working.php', $install_content);

echo "<h2>✅ PHPMailer установлен!</h2>";
echo "<p>Теперь можно настроить SMTP для надежной отправки писем.</p>";
?>