<?php
  define('BOT_TOKEN', '262354959:AAGZbji0qOxQV-MwzzRqiWJYdPVzkqrbC4Y');
  define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
  
  $content = file_get_contents("php://input");
  $update = json_decode($content, true);

  if(!$update['message'])  {
    exit;
  } else {
    processMessage($update['message']);
  }
  
  /*
   * Gestisco la richiesta al bot
   */
  function processMessage($message) {
    // processo il contenuto del messaggio ricevuto da telegram
    $messageId = isset($message['message_id']) ? $message['message_id'] : "";
    $chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
    $firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
    $lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
    $username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
    $date = isset($message['date']) ? $message['date'] : "";
    $text = isset($message['text']) ? $message['text'] : "";
    if(isset($message['text'])) {
      // incoming text message
      $text = $message['text'];
      
      if(strpos($text, "/start") === 0) {
	      apiRequestJson("sendMessage", array('chat_id' => $chat_id, 'text' => "Hello", 'reply_markup' => array(
          'keyboard' => array(array("Hello", "Hi")),
          'one_time_keyboard' => true,
          'resize_keyboard' => true)));
      } elseif($text == "domanda 1") {
	      apiRequest("sendMessage", array('chat_id' => $chat_id, 'text' => "Risposta 1"));
      } elseif($text == "domanda 2") {
	      apiRequest("sendMessage", array('chat_id' => $chat_id, 'text' => "Risposta 2"));
      } elseif($message['forward_from']) {
        $forward_date = date(DATE_RFC2822, $message['forward_date']);
        apiRequest("sendMessage", array('chat_id' => $chat_id, 'text' => "Messaggio inoltrato da {$message['forward_from']['username']} il {$forward_date}"));
      } else {
        apiRequest("sendMessage", array('chat_id' => $chat_id, 'text' => "Comando non valido"));
      }
    }
  }
  
  /*
   * Gestisco le risposte al bot che richiedono parametri aggiuntivi oltre al testo
   */
  function apiRequestJson($method, $parameters) {
    if(!is_string($method)) {
      error_log("Method name must be a string\n");
      return false;
    }
    // se passo o meno qualche parametro
    if(!$parameters) {
      $parameters = array();
    } else if (!is_array($parameters)) {
      error_log("Parameters must be an array\n");
      return false;
    }

    $parameters["method"] = $method;

    $handle = curl_init(API_URL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    return exec_curl_request($handle);
  }
  /*
   * Gestisco le risposte al bot di solo testo
   */
  function apiRequest($method, $parameters) {
    if(!is_string($method)) {
      error_log("Method name must be a string\n");
      return false;
    }

    if(!$parameters) {
      $parameters = array();
    } else if (!is_array($parameters)) {
      error_log("Parameters must be an array\n");
      return false;
    }

    foreach ($parameters as $key => &$val) {
      // encoding to JSON array parameters, for example reply_markup
      if (!is_numeric($val) && !is_string($val)) {
        $val = json_encode($val);
      }
    }
    $url = API_URL.$method.'?'.http_build_query($parameters);

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);

    return exec_curl_request($handle);
  }
  /*
   * Gestisco la risposta effettiva che il bot manderà
   */
  function exec_curl_request($handle) {
    $response = curl_exec($handle);

    if($response === false) {
      $errno = curl_errno($handle);
      $error = curl_error($handle);
      error_log("Curl returned error $errno: $error\n");
      curl_close($handle);
      return false;
    }

    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);

    if($http_code >= 500) {
      // do not wat to DDOS server if something goes wrong
      sleep(10);
      return false;
    } else if($http_code != 200) {
      $response = json_decode($response, true);
      error_log("Request has failed with error {$response['error_code']}  : {$response['description']}\n");
      if($http_code == 401) {
        throw new Exception('Invalid access token provided');
      }
      return false;
    } else {
      $response = json_decode($response, true);
      if (isset($response['description'])) {
        error_log("Request was successfull: {$response['description']}\n");
      }
      $response = $response['result'];
    }

    return $response;
  }
