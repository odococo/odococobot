<?php

$botToken = "262354959:AAGZbji0qOxQV-MwzzRqiWJYdPVzkqrbC4Y";
$website = "https://api.telegram.org/bot".$botToken;

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);

//header("Content-Type: application/json");

if(strpos($text, "/start") === 0) {
	$response = "Ciao $firstname, benvenuto!";
} elseif($text == "domanda 1") {
	$response = "risposta 1";
} elseif($text == "domanda 2") {
	$response = "risposta 2";
} elseif($message['forward_from']) {
  $forward_date = date(DATE_RFC2822, $message['forward_date']);
  $response = "Messaggio inoltrato da {$message['forward_from']['username']} il {$forward_date}";
} else {
	$response = "Comando non valido!";
}

$parameters = array('chat_id' => $chatId, 'text' => $response);
$parameters['method'] = "sendMessage";
$parameters["reply_markup"] = '{ "keyboard": [["uno"], ["due"], ["tre"], ["quattro"]], "one_time_keyboard": false}';
$parameters['text'] .= print_r(json_encode($parameters), true);
file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=".$text);
?>
