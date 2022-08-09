<?php
$envLocalDir = __DIR__ . '/.env.local';

function loadEnv($path)
{
    if (!is_readable($path)) {
        throw new \RuntimeException(sprintf('%s file is not readable', $path));
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {

        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

if (file_exists($envLocalDir)) {
    loadEnv($envLocalDir);
}

$name = $_POST['name']; // получаем имя клиента
$email = $_POST['email']; // получаем почту клиента
$message = $_POST['message']; // получаем сообщение клиента

// воодим между кавычек токен бота, который прислал @botfater
$token = getenv('TELEGRAM_BOT_TOKEN');
// вставляем номер чата, который можно найти на странице 
// api.telegram.org/botXXXXXXXXX/getUpdates — где XXX это токен бота
$chat_id = getenv('TELEGRAM_BOT_CHAT_ID');

// Собираем данные в один массив 
$arr = array(
    'Клиент: ' => $name,
    'Email: ' => $email,
    'Сообщение: ' => $message
);

// составляем сообщение из данных массива
$txt = '';
foreach($arr as $key => $value) {
    $txt .= $key."<b> ". urlencode($value)."</b> "."%0A";
}

// даем команду боту отправить сообщение с текстом
$sendToTelegram = fopen("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&parse_mode=html&text=$txt","r");

if ($sendToTelegram) {
    return true; // если прошло успешно, возвращаем ответ true
} else {
    return false; // если ошибка, возвращаем false
}
