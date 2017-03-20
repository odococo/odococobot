<?php echo "Silence is golden";
$botToken = "262354959:AAGZbji0qOxQV-MwzzRqiWJYdPVzkqrbC4Y";
  $website = "https://api.telegram.org/bot".$botToken;
  //$content = file_get_contents("php://input");
  $content = file_get_contents($website);
  print_r($content);
  echo "ok";
