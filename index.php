<?php
$botToken = "bot" . "******";

$content = file_get_contents("php://input");
$update = json_decode($content, true);
if (!$update) {
  exit;
}
$usekeyboard = 0;
$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['from']['first_name']) ? $message['from']['first_name'] : "";
$lastname = isset($message['from']['last_name']) ? $message['from']['last_name'] : "";
if ($lastname == "") {
  $completename = $firstname;
} else {
  $completename = "$firstname $lastname";
}
$username = isset($message['from']['username']) ? $message['from']['username'] : $completename;
$userId = isset($message['from']['id']) ? $message['from']['id'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";
$text = trim($text);
$notstrtoloweredtext = $text;
$text = strtolower($text);
header("Content-Type: application/json");
$response = '';
$servernamedb = "ftp.bottt1.altervista.org";
$usernamedb = "bottt1";
$passworddb = "******";
$dbnamedb = "my_bottt1";
$portdb = 21;
$conn = mysqli_init();

function mcd($a, $b) {
  if ($a < $b) {
    $a += $b;
    $b = $a - $b;
    $a -= $b;
  }

  if ($b == 0 || $a == $b) return $a;
  return mcd($b, $a % $b);
}

if (!$conn) {
  $response = $response . "mysqli_init failed";
}
if (!mysqli_real_connect($conn, $servernamedb, $usernamedb, $passworddb, $dbnamedb, $portdb)) {
  $response = $response . "Connect Error: " . mysqli_connect_error();
}
if (strpos($text, "/") === 0) {
  $exploded = explode(" ", $text);
  $command = $exploded[0];
  $sql = "SELECT * FROM commands1 WHERE command = '$command'";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO commands1 (command, times) VALUES ('$command', 1)";
    $result1 = $conn->query($sql1);
  } else {
    $sql1 = "UPDATE commands1 SET times = times + 1 WHERE command = '$command'";
    $result1 = $conn->query($sql1);
  }
  $sql = "SELECT * FROM commands2 WHERE command = '$command' AND chatId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO commands2 (chatId, command, times) VALUES ($chatId, '$command', 1)";
    $result1 = $conn->query($sql1);
  } else {
    $sql1 = "UPDATE commands2 SET times = times + 1 WHERE command = '$command' AND chatId = $chatId";
    $result1 = $conn->query($sql1);
  }
}
if ($chatId > 0) {
  $sql = "SELECT * FROM isusing WHERE chatId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO isusing (chatId, command) VALUES ($chatId, 0)";
    $result1 = $conn->query($sql1);
  }
  while ($row = $result->fetch_assoc()) {
    $command = $row["command"];
  }
  if ($command == 1) {
    $rand = rand(1, 5);
    if ($text == 1 || $text == 2 || $text == 3 || $text == 4 || $text == 5 || $text == "/stop") {
      if ($text == $rand) {
        $time = time();
        $response = "Hai perso!";
        $sql = "UPDATE lottery SET points = 64 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE lottery SET time = $time WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE lottery SET status = 1 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE isusing SET command = 0 WHERE chatId = $chatId";
        $result = $conn->query($sql);
      } else if ($text == "/stop") {
        $time = time();
        $sql = "UPDATE lottery SET totalpoints = totalpoints + points WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "SELECT * FROM lottery WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $rowsnumber = $result->num_rows;
        while ($row = $result->fetch_assoc()) {
          $totalpoints = $row["totalpoints"];
        }
        $sql = "UPDATE lottery SET points = 64 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE lottery SET time = $time WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE lottery SET status = 1 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE isusing SET command = 0 WHERE chatId = $chatId";
        $result = $conn->query($sql);
        if ($rowsnumber == 1) {
          $response = "Ti sei fermato. Hai complessivamente $totalpoints Punti Lotteria.";
        } else {
          $response = "Ti sei fermato. Hai guadagnato punti in $rowsnumber gruppi.";
        }
      } else {
        $sql = "UPDATE lottery SET points = floor(points * 1.25) WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "SELECT * FROM lottery WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          $points = $row["points"];
        }
        $response = "Complimenti! Hai aumentato il tuo punteggio del 25% e il tuo nuovo punteggio è $points. Se vuoi fermarti, invia /stop";
      }
    } else {
      $response = "Opzione non valida";
    }
  }
  if ($command == 2) {
    $rand = rand(1, 200);
    if ($text == "/spam") {
      if ($rand == 1) {
        $sql = "UPDATE spammers SET totalpoints = totalpoints + 250 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE spammers SET status = 1 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
        $sql = "UPDATE isusing SET command = 0 WHERE chatId = $chatId";
        $result = $conn->query($sql);
        $sql = "SELECT * FROM spammers WHERE chatId = $chatId";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          $totalpoints = $row["totalpoints"];
        }
        $response = "Hai vinto 250 punti! Hai complessivamente $totalpoints punti. Avvia di nuovo il comando nel gruppo per giocare ancora.";
      } else if ($rand != 1) {
        $response = "Hai perso 1 punto";
        $sql = "UPDATE spammers SET totalpoints = totalpoints - 1 WHERE chatId = $chatId AND status = 0";
        $result = $conn->query($sql);
      }
    } else {
      $response = "Opzione non valida";
    }
  }
}
if ($chatId < 0) {
  $sql = "SELECT * FROM automatic WHERE type = 1";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $groupId1 = $row["groupId"];
    $time = $row["time"];
    $time = $time + 36000;
    $currenttime1 = time() + 36000;
    $days1 = floor($currenttime1 / 86400) - floor($time / 86400);
    if ($days1 != 0) {
      if ($groupId1 > 0) {
        $response = "Il comando /classificalotteria funziona solo nei gruppi";
      } else {
        $response = "";
        $sql2 = "SELECT * FROM lottery WHERE chatIdDest = $groupId1 ORDER BY totalpoints DESC LIMIT 10";
        $result2 = $conn->query($sql2);
        if ($result2->num_rows == 0) {
          $response = "Classifica vuota";
        } else {
          while ($row2 = $result2->fetch_assoc()) {
            $userId1 = $row2["chatId"];
            $sql1 = "SELECT * FROM score WHERE userId = $userId1";
            $result1 = $conn->query($sql1);
            while ($row1 = $result1->fetch_assoc()) {
              $username1 = $row1["username"];
            }
            $response = $response . $username1 . ": " . $row2["totalpoints"] . "\n";
          }
        }
      }
      $encoded = urlencode("La Lotteria del TheScrasse Bot è aperta! La Lotteria apre ogni giorno alle ore 16.\nTop 10:\n$response");
      $response = "";
      $inline_keyboard = array(
        'inline_keyboard' => array(
          array(
            array(
              'text' => 'Gioca',
              'url' => "t.me/thescrasse_bot?start=lotteria$groupId1"
            )
          ),
          array(
            array(
              'text' => 'Gioca in tutti i gruppi',
              'url' => "t.me/thescrasse_bot?start=lotteriatutti$groupId1"
            )
          )
        )
      );
      $inline_keyboard = json_encode($inline_keyboard);
      $encodedk = urlencode($inline_keyboard);
      $fgc = file_get_contents("https://api.telegram.org/$botToken/sendMessage?chat_id=$groupId1&text=$encoded&reply_markup=$encodedk");
    }
  }
  $currenttime = time();
  $sql = "UPDATE automatic SET time = $currenttime WHERE type = 1";
  $result = $conn->query($sql);
  $sql = "UPDATE counter SET counter = counter + 1";
  $result = $conn->query($sql);
  $sql = "SELECT * FROM spam WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO spam (groupId) VALUES ($chatId)";
    $result1 = $conn->query($sql1);
  }
  $sql = "SELECT * FROM automatic WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO automatic (type, groupId, time) VALUES (1, $chatId, 0)";
    $result1 = $conn->query($sql1);
  }
  $sql = "SELECT * FROM groups WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO groups (groupId, counter, score, scorerem, coins, scoreremS, coinsS, time) VALUES ($chatId, 0, 0, 0, 0, 0, 0, 0)";
    $result1 = $conn->query($sql1);
  }
  $sql = "UPDATE groups SET counter = counter + 1 WHERE groupId = $chatId";
  $result = $conn->query($sql);
  $sql = "UPDATE bombs SET messages = messages - 1 WHERE groupId = $chatId";
  $result = $conn->query($sql);
  $sql = "SELECT * FROM bombs WHERE groupId = $chatId AND messages = 0";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $chatId = $row["groupId"];
    $luckyId = $row["userId"];
    $lucky = $row["username"];
    $weight = $row["weight"];
    if ($weight == 1) {
      $weighttext = "1 punto, che è andato a";
    } else {
      $weighttext = "$weight punti, che sono andati a";
    }
    $sql1 = "UPDATE score SET score = score + $weight WHERE userId = $luckyId AND groupId = $chatId";
    $result1 = $conn->query($sql1);
    $sql2 = "UPDATE score SET score = score - $weight WHERE userId = $userId AND groupId = $chatId";
    $result2 = $conn->query($sql2);
    $encoded = urlencode("$username è esploso! Ha perso $weighttext $lucky");
    $fgc = file_get_contents("https://api.telegram.org/$botToken/sendMessage?chat_id=$chatId&text=$encoded");
  }
  $sql = "SELECT * FROM score WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "SELECT DISTINCT groupId FROM score";
    $result1 = $conn->query($sql1);
    while ($row = $result1->fetch_assoc()) {
      $groupId = $row["groupId"];
      $sql2 = "SELECT * FROM score WHERE groupId = $groupId ORDER BY RAND() LIMIT 1";
      $result2 = $conn->query($sql2);
      while ($row = $result2->fetch_assoc()) {
        $userId1 = $row["userId"];
        $username1 = $row["username"];
        $sql3 = "UPDATE score SET score = score + 1 WHERE groupId = $groupId AND userId = $userId1";
        $result3 = $conn->query($sql3);
        //    $sql5 = "SELECT * FROM spam WHERE groupId = $groupId AND spam = 0";
        //    $result5 = $conn->query($sql5);
        //    if($result5->num_rows == 0) {
        $json1 = file_get_contents("https://api.telegram.org/$botToken/getChat?chat_id=$groupId");
        $json2 = json_decode($json1, true);
        $groupname = isset($json2['result']['title']) ? $json2['result']['title'] : "";
        $encoded = urlencode("Il bot è stato aggiunto ad un nuovo gruppo! È stato sorteggiato un punto fra i membri del gruppo $groupname. Il punto è andato a te.");
        $fgc = file_get_contents("https://api.telegram.org/$botToken/sendMessage?chat_id=$userId1&text=$encoded");
        //    }
      }
    }
    $sql4 = "INSERT INTO score (groupId, userId, username, score, bombs) VALUES ($chatId, $userId, '$username', 0, 0)";
    $result4 = $conn->query($sql4);
  } else {
    $sql = "SELECT * FROM score WHERE groupId = $chatId AND userId = $userId";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $sql4 = "INSERT INTO score (groupId, userId, username, score, bombs) VALUES ($chatId, $userId, '$username', 0, 0)";
      $result4 = $conn->query($sql4);
    }
  }
  $sql = "SELECT * FROM groups WHERE groupId = $chatId";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $counter = $row["counter"];
  }
  if ($counter % 100 == 0) {
    $score = 1;
    $powers = 100;
    while ($counter % ($powers * 10) == 0) {
      $score = $score * 4;
      $powers = $powers * 10;
    }
    if ($score == 1) {
      $encoded = urlencode("Complimenti! Hai scritto il messaggio numero $counter. Hai ottenuto $score punto.");
      $fgc = file_get_contents("https://api.telegram.org/$botToken/sendMessage?chat_id=$chatId&text=$encoded");
    } else {
      $encoded = urlencode("Complimenti! Hai scritto il messaggio numero $counter. Hai ottenuto $score punti.");
      $fgc = file_get_contents("https://api.telegram.org/$botToken/sendMessage?chat_id=$chatId&text=$encoded");
    }
    $numberp = $counter / 100;
    $numberp4 = $numberp - 4;
    $time = time();
    $sql = "INSERT INTO points (groupId, number, time) VALUES ($chatId, $numberp, $time)";
    $result = $conn->query($sql);
    $sql = "SELECT * FROM points WHERE groupId = $chatId AND number = $numberp4";
    $result = $conn->query($sql);
    if ($result->num_rows != 0) {
      while ($row = $result->fetch_assoc()) {
        $oldtime = $row["time"];
      }
      $difference = $time - $oldtime;
      $sql = "UPDATE groups SET time = $difference WHERE groupId = $chatId";
      $result = $conn->query($sql);
    }
    $sql = "SELECT * FROM score WHERE userId = $userId AND groupId = $chatId";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $sql1 = "INSERT INTO score (groupId, userId, username, score, bombs) VALUES ($chatId, $userId, '$username', $score, $score)";
      $result1 = $conn->query($sql1);
    } else {
      $sql1 = "UPDATE score SET score = score + $score WHERE userId = $userId AND groupId = $chatId";
      $result1 = $conn->query($sql1);
      $sql1 = "UPDATE score SET bombs = bombs + $score WHERE userId = $userId AND groupId = $chatId";
      $result1 = $conn->query($sql1);
    }
    $sql = "UPDATE groups SET score = score + $score WHERE groupId = $chatId";
    $result = $conn->query($sql);
    $sql = "UPDATE groups SET scorerem = scorerem + $score WHERE groupId = $chatId";
    $result = $conn->query($sql);
    $sql = "SELECT * FROM groups WHERE groupId = $chatId";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $scorerem = $row["scorerem"];
    }
    $coins = floor($scorerem / 3);
    $coins3 = $coins * 3;
    if ($coins > 0) {
      $sql = "UPDATE groups SET scorerem = scorerem - $coins3 WHERE groupId = $chatId";
      $result = $conn->query($sql);
      $sql = "UPDATE groups SET coins = coins + $coins WHERE groupId = $chatId";
      $result = $conn->query($sql);
    }
    $sql = "UPDATE groups SET scoreremS = scoreremS + $score WHERE groupId = $chatId";
    $result = $conn->query($sql);
    $sql = "SELECT * FROM groups WHERE groupId = $chatId";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $scoreremS = $row["scoreremS"];
    }
    $coinsS = floor($scoreremS / 50);
    $coinsS50 = $coinsS * 50;
    if ($coinsS > 0) {
      $sql = "UPDATE groups SET scoreremS = scoreremS - $coinsS50 WHERE groupId = $chatId";
      $result = $conn->query($sql);
      $sql = "UPDATE groups SET coinsS = coinsS + $coinsS WHERE groupId = $chatId";
      $result = $conn->query($sql);
    }
  }
}
if (strpos($text, "/classifica") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /classifica funziona solo nei gruppi";
  } else {
    $sql = "SELECT * FROM score WHERE groupId = $chatId ORDER BY score DESC LIMIT 10";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $response = "Classifica vuota";
    } else {
      while ($row = $result->fetch_assoc()) {
        $response = $response . $row["username"] . ": " . $row["score"] . "\n";
      }
    }
  }
}
if (strpos($text, "/classificacomandi") === 0) {
  $response = "Classifica totale\n";
  $sql = "SELECT * FROM commands1 ORDER BY times DESC LIMIT 10";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $response = "Classifica vuota";
  } else {
    while ($row = $result->fetch_assoc()) {
      $response = $response . $row["command"] . ": " . $row["times"] . "\n";
    }
  }
  $response = $response . "\nClassifica del gruppo\n";
  $sql = "SELECT * FROM commands2 WHERE chatId = $chatId ORDER BY times DESC LIMIT 10";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $response = "Classifica vuota";
  } else {
    while ($row = $result->fetch_assoc()) {
      $response = $response . $row["command"] . ": " . $row["times"] . "\n";
    }
  }
}
if (strpos($text, "/classificaultimi") === 0) {
  $response = "";
  if ($chatId > 0) {
    $response = "Il comando /classificaultimi funziona solo nei gruppi";
  } else {
    $sql = "SELECT * FROM score WHERE groupId = $chatId ORDER BY score ASC LIMIT 10";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $response = "Classifica vuota";
    } else {
      while ($row = $result->fetch_assoc()) {
        $response = $response . $row["username"] . ": " . $row["score"] . "\n";
      }
    }
  }
}
if (strpos($text, "/punti") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /punti funziona solo nei gruppi";
  } else {
    $sql = "SELECT * FROM score WHERE groupId = $chatId AND userId = $userId";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $response = "Hai 0 punti";
    } else {
      while ($row = $result->fetch_assoc()) {
        if ($row["score"] == 1 || $row["score"] == -1) {
          $response = "Hai " . $row["score"] . " punto";
        } else {
          $response = "Hai " . $row["score"] . " punti";
        }
      }
    }
  }
}
if (strpos($text, "/messaggio") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /messaggio funziona solo nei gruppi";
  } else {
    $sql = "SELECT * FROM groups WHERE groupId = $chatId";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $time = $row["time"];
    }
    if ($time == 0) {
      $response = "Questo è il messaggio numero " . $counter . ". Il tempo stimato per il prossimo punto non è disponibile";
    } else {
      $rem = 100 - ($counter % 100);
      $timeresp = floor($time * $rem / 400);
      $s = $timeresp % 60;
      if ($s == 1) {
        $s = "1 secondo";
      } else {
        $s = "$s secondi";
      }
      $m = floor(($timeresp % 3600) / 60);
      if ($m == 1) {
        $m = "1 minuto";
      } else {
        $m = "$m minuti";
      }
      $h = floor(($timeresp % 86400) / 3600);
      if ($h == 1) {
        $h = "1 ora";
      } else {
        $h = "$h ore";
      }
      $d = floor($timeresp / 86400);
      if ($d == 1) {
        $d = "1 giorno";
      } else {
        $d = "$d giorni";
      }
      if ($d != 0) {
        $response = "Questo è il messaggio numero " . $counter . ". Tempo stimato per il prossimo punto: $d, $h, $m, $s";
      } else if ($h != 0) {
        $response = "Questo è il messaggio numero " . $counter . ". Tempo stimato per il prossimo punto: $h, $m, $s";
      } else if ($m != 0) {
        $response = "Questo è il messaggio numero " . $counter . ". Tempo stimato per il prossimo punto: $m, $s";
      } else {
        $response = "Questo è il messaggio numero " . $counter . ". Tempo stimato per il prossimo punto: $s";
      }
    }
  }
}
if (strpos($text, "/info") === 0) {
  $sql = "SELECT * FROM counter";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $countertot = $row["counter"];
  }
  $sql = "SELECT * FROM groups";
  $result = $conn->query($sql);
  $groupsn = $result->num_rows;
  $response = "Il bot conta messaggi in " . $groupsn . " gruppi" . "\n" . "Messaggi inviati complessivamente: " . $countertot;
}
if (strpos($text, "/gettoni") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /gettoni funziona solo nei gruppi";
  } else {
    $sql = "SELECT * FROM groups WHERE groupId = $chatId";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $coins = $row["coins"];
    }
    if ($coins == 1) {
      $response = "Questo gruppo ha " . $coins . " gettone. Usalo per avviare il comando /misentofortunato";
    } else if ($coins == 0) {
      $r300 = 300 - ($counter % 300);
      $r1000 = 1000 - ($counter % 1000);
      $rem = min($r300, $r1000);
      if ($rem == 1) {
        $response = "Questo gruppo ha " . $coins . " gettoni. Manca " . $rem . " messaggio al prossimo gettone";
      } else {
        $response = "Questo gruppo ha " . $coins . " gettoni. Mancano " . $rem . " messaggi al prossimo gettone";
      }
    } else {
      $response = "Questo gruppo ha " . $coins . " gettoni. Usali per avviare il comando /misentofortunato";
    }
  }
}
if (strpos($text, "/supergettoni") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /supergettoni funziona solo nei gruppi";
  } else {
    $sql = "SELECT * FROM groups WHERE groupId = $chatId";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $coinsS = $row["coinsS"];
    }
    if ($coinsS == 1) {
      $response = "Questo gruppo ha " . $coinsS . " Supergettone. Usalo per avviare il comando /misentofortunatooo";
    } else if ($coinsS == 0) {
      $response = "Questo gruppo ha " . $coinsS . " Supergettoni. Mancano millemila messaggi al prossimo Supergettone";
    } else {
      $response = "Questo gruppo ha " . $coinsS . " Supergettoni. Usali per avviare il comando /misentofortunatooo";
    }
  }
}
if (strpos($text, "/spamon") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /spamon funziona solo nei gruppi";
  } else {
    $sql = "UPDATE spam SET spam = 1 WHERE groupId = $chatId";
    $result = $conn->query($sql);
    $response = "Sono stati attivati tutti i messaggi";
  }
}
if (strpos($text, "/spamoff") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /spamoff funziona solo nei gruppi";
  } else {
    $sql = "UPDATE spam SET spam = 0 WHERE groupId = $chatId";
    $result = $conn->query($sql);
    $response = "Sono stati attivati solo i messaggi principali";
  }
}
if (strpos($text, "/misentofortunato") === 0 && strpos($text, "/misentofortunatooo") == false) {
  $sql = "SELECT * FROM groups WHERE groupId = $chatId";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $coins = $row["coins"];
  }
  if ($coins > 0) {
    $min = min($coins, 30);
    for ($i = 1; $i <= $min; $i++) {
      $sql = "SELECT * FROM score WHERE groupId = $chatId ORDER BY RAND() LIMIT 1";
      $result = $conn->query($sql);
      while ($row = $result->fetch_assoc()) {
        $lucky = $row["userId"];
        $luckyu = $row["username"];
        $sql1 = "UPDATE score SET score = score + 1 WHERE userId = $lucky AND groupId = $chatId";
        $result1 = $conn->query($sql1);
      }
      $sql = "SELECT * FROM score WHERE groupId = $chatId ORDER BY RAND() LIMIT 1";
      $result = $conn->query($sql);
      while ($row = $result->fetch_assoc()) {
        $unlucky = $row["userId"];
        $unluckyu = $row["username"];
        $sql1 = "UPDATE score SET score = score - 1 WHERE userId = $unlucky AND groupId = $chatId";
        $result1 = $conn->query($sql1);
      }
      $sql = "UPDATE groups SET coins = coins - 1 WHERE groupId = $chatId";
      $result = $conn->query($sql);
      $response = $response . "\n$luckyu ha rubato un punto a $unluckyu";
    }
  } else {
    $response = "Questo gruppo non ha gettoni";
  }
}
if (strpos($text, "/misentofortunatooo") === 0) {
  $sql = "SELECT * FROM groups WHERE groupId = $chatId";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $coinsS = $row["coinsS"];
  }
  if ($coinsS > 0) {
    $sql = "SELECT * FROM score WHERE groupId = $chatId ORDER BY RAND() LIMIT 12";
    $result = $conn->query($sql);
    $a = 0;
    $usernames = array();
    $userIds = array();
    if ($result->num_rows == 12) {
      while ($row = $result->fetch_assoc()) {
        $usernames[$a] = $row["username"];
        $userId2 = $row["userId"];
        if ($a == 0) {
          $sql1 = "UPDATE score SET score = score + 5 WHERE userId = $userId2";
          $result1 = $conn->query($sql1);
        } else if ($a > 0 && $a < 6) {
          $sql1 = "UPDATE score SET score = score + 1 WHERE userId = $userId2";
          $result1 = $conn->query($sql1);
        } else if ($a > 5 && $a < 11) {
          $sql1 = "UPDATE score SET score = score - 1 WHERE userId = $userId2";
          $result1 = $conn->query($sql1);
        } else if ($a == 11) {
          $sql1 = "UPDATE score SET score = score - 5 WHERE userId = $userId2";
          $result1 = $conn->query($sql1);
        }
        $a++;
      }
      $sql = "UPDATE groups SET coinsS = coinsS - 1 WHERE groupId = $chatId";
      $result = $conn->query($sql);
      $response = "Ops... a causa del Supergettone i punti si sono dispersi dappertutto. Ecco le conseguenze:\n" . "$usernames[0] ha guadagnato 5 punti\n" . "$usernames[1], $usernames[2], $usernames[3], $usernames[4], $usernames[5] hanno guadagnato 1 punto\n" . "$usernames[6], $usernames[7], $usernames[8], $usernames[9], $usernames[10] hanno perso 1 punto\n" . "$usernames[11] ha perso 5 punti\n" . "Fra 50 punti un nuovo Supergettone getterà il caos sul gruppo!";
    } else {
      $response = "Il gruppo deve essere composto da almeno 12 persone";
    }
  } else {
    $response = "Questo gruppo non ha Supergettoni";
  }
}
if (strpos($text, "/bomba") === 0) {
  $sql = "SELECT * FROM score WHERE userId = $userId AND groupId = $chatId";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $bombs = $row["bombs"];
  }
  $nbombs = floor($bombs / 2);
  if ($nbombs == 1) {
    $nbombs = "1 bomba";
  } else {
    $nbombs = "$nbombs bombe";
  }
  if ($bombs > 1) {
    $response = "Hai $bombs punti Bomba.\nUsa il comando /bomb1 per innescare una sola bomba.\nUsa il comando /bomb2 per innescare $nbombs.";
  } else {
    $response = "Ti servono almeno 2 punti Bomba per innescare una bomba. Ora ne hai $bombs";
  }
}
if (strpos($text, "/bomb1") === 0) {
  $sql = "SELECT * FROM score WHERE userId = $userId AND groupId = $chatId";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $bombs = $row["bombs"];
  }
  if ($bombs > 1) {
    $messages = rand(101, 300);
    $sql = "INSERT INTO bombs (groupId, userId, username, messages) VALUES ($chatId, $userId, '$username', $messages)";
    $result = $conn->query($sql);
    $sql = "UPDATE score SET bombs = bombs - 2 WHERE userId = $userId AND groupId = $chatId";
    $result = $conn->query($sql);
    $response = "Bomba innescata";
  } else {
    $response = "Ti servono almeno 2 punti Bomba per innescare una bomba. Ora ne hai $bombs";
  }
}
if (strpos($text, "/bomb2") === 0) {
  $sql = "SELECT * FROM score WHERE userId = $userId AND groupId = $chatId";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $bombs = $row["bombs"];
  }
  if ($bombs > 1) {
    $nbombs = floor($bombs / 2);
    $remainder = $bombs % 2;
    $maxmessage = $nbombs * 10 + 10;
    $messages = rand(11, $maxmessage);
    $sql = "INSERT INTO bombs (groupId, userId, username, messages, weight) VALUES ($chatId, $userId, '$username', $messages, $nbombs)";
    $result = $conn->query($sql);
    $sql = "UPDATE score SET bombs = $remainder WHERE userId = $userId AND groupId = $chatId";
    $result = $conn->query($sql);
    $response = "Bombe innescate";
  } else {
    $response = "Ti servono almeno 2 gettoni per innescare una bomba. Ora ne hai $bombs";
  }
}
if (strpos($text, "/calcolatricecheesplode") === 0) {
  $number = substr($text, 24);
  $base = base_convert($number, 10, 3);
  $base = (string) $base;
  if (strpos($base, "2") !== false) {
    $fgc = file_get_contents("https://api.telegram.org/$botToken/sendAnimation?chat_id=$chatId&animation=https://i.giphy.com/oe33xf3B50fsc.gif");
  } else {
    $response = 'Calcolatrice sana e salva';
  }
}
if (strpos($text, "/flash") === 0) {
  $number = base_convert($text, 10, 10);
  $number = (string) $number;
  if (strlen($number) != 3) {
    $response = "Il numero non ha 3 cifre";
  } else if (strpos($number, '8') !== false || strpos($number, '9') !== false || strpos($number, '0') !== false) {
    $response = "Le cifre non sono comprese fra 1 e 7";
  } else {
    $a7 = $number[0];
    $b7 = $number[1];
    $c7 = $number[2];
    $abc = intval($a7 . $b7 . $c7);
    $bca = intval($b7 . $c7 . $a7);
    $cab = intval($c7 . $a7 . $b7);
    if ($abc % 7 == 0) {
      $response = "$abc è divisibile per 7";
    } else if ($bca % 7 == 0) {
      $response = "$bca è divisibile per 7";
    } else if ($cab % 7 == 0) {
      $response = "$cab è divisibile per 7";
    } else {
      $response = "Un supereroe in meno!";
    }
  }
}
if (strpos($text, "/brawler") === 0) {
  $brawlers = array(
    "Bombardino",
    "Bo",
    "Brock",
    "Bull",
    "Carl",
    "Colt",
    "Corvo",
    "Barryl",
    "Dynamike",
    "El Primo",
    "Frank",
    "Jessie",
    "Mortis",
    "Nita",
    "Pam",
    "Penny",
    "Piper",
    "Pocho",
    "Rosa",
    "Stecca",
    "Shelly",
    "Spike",
    "Tara",
    "Leon",
    "Eugenio"
  );
  $random1 = array_rand($brawlers, 2);
  $random2 = array(
    $brawlers[$random1[0]],
    $brawlers[$random1[1]]
  );
  shuffle($random2);
  $response = "Se giochi con $random2[0] vinci sicuramente! Se invece giochi con $random2[1] perdi...";
}
if (strpos($text, "/scomponi") === 0) {
  $a = 0;
  $b = 0;
  $arr = array();
  $factors = "";
  $number1 = base_convert($text, 10, 10);
  if ($number1 > pow(10, 16)) {
    $response = "Il numero è troppo grande, non mi va di scomporlo!";
  } else {
    $number2 = $number1;
    $sqrt = sqrt($number1);
    while ($b == 0) {
      $b = 1;
      for ($i = 2; $i <= $sqrt; $i++) {
        if ($b == 1) {
          if ($number2 % $i == 0) {
            $a = $a + 1;
            $b = 0;
            $arr[$i]++;
            $number2 = $number2 / $i;
            $sqrt = sqrt($number2);
          }
        }
      }
    }
    $arr[$number2]++;
    end($arr);
    for ($j = 2; $j <= key($arr); $j++) {
      if ($j == key($arr)) {
        if ($arr[$j] == 1) {
          $factors = "$factors$j";
        } else if ($arr[$j] != 0) {
          $factors = "$factors$j^$arr[$j]";
        }
      } else {
        if ($arr[$j] == 1) {
          $factors = "$factors" . "$j * ";
        } else if ($arr[$j] != 0) {
          $factors = "$factors" . "$j^$arr[$j] * ";
        }
      }
    }
    if ($number1 == 0 || $number1 == 1) {
      $response = "Non sono un idiota, lo so che $number1 non è primo!";
    } else if ($a == 0) {
      $response = "Che brutto il numero $number1, è primo...";
    } else if ($a > 4) {
      $response = "$number1 = $factors. Finalmente un numero con tanti fattori primi!";
    } else {
      $response = "$number1 = $factors";
    }
  }
}
if (strpos($text, "/phi") === 0) {
  $a = 0;
  $b = 0;
  $arr = array();
  $factors = "";
  $number1 = base_convert($text, 10, 10);
  if ($number1 > pow(10, 16)) {
    $response = "Il numero è troppo grande, non mi va di scomporlo!";
  } else {
    $number2 = $number1;
    $number3 = $number1;
    $sqrt = sqrt($number1);
    while ($b == 0) {
      $b = 1;
      for ($i = 2; $i <= $sqrt; $i++) {
        if ($b == 1) {
          if ($number2 % $i == 0) {
            $a = $a + 1;
            $b = 0;
            $arr[$i]++;
            $number2 = $number2 / $i;
            $sqrt = sqrt($number2);
          }
        }
      }
    }
    $arr[$number2]++;
    end($arr);
    for ($j = 2; $j <= key($arr); $j++) {
      if ($arr[$j] != 0) {
        $number3 = $number3 / $j * ($j - 1);
      }
    }
    $response = $number3;
  }
}
if (strpos($text, "/mischiacolori") === 0) {
  $text = str_replace("/mischiacolori@thescrasse_bot ", "", $text);
  $text = str_replace("/mischiacolori ", "", $text);
  $colours = explode(" ", $text);
  if (strlen($colours[0]) < 3 || strlen($colours[1]) < 3) {
    $response = "Colori troppo corti";
  } else {
    $rand0 = rand(2, strlen($colours[0]));
    $rand1 = rand(2, strlen($colours[1])) * (-1);
    $colour0 = substr($colours[0], 0, $rand0);
    $colour1 = substr($colours[1], $rand1);
    $response = $colour0 . $colour1;
  }
}
if (strpos($text, "/comandigarematematica") === 0) {
  $response = "Strumenti\n\n" . "/scomponi\n" . "Formato: /scomponi [numero intero]\n" . "Risposta: il numero scomposto in fattori primi\n\n" . "Altri comandi\n\n" . "/calcolatricecheesplode\n" . "Formato: /calcolatricecheesplode [numero intero]\n" . "Gara: Gara di Febbraio 2019, quesito 6\n" . "Risposta: una GIF di un'esplosione se, per ottenere il numero, bisogna schiacciare +1 due volte consecutive nella calcolatrice\n\n" . "/flash\n" . "Formato: /flash [numero a 3 cifre]\n" . "Gara: V Allenamento Online 2019, quesito 19\n" . "Risposta: l'eventuale permutazione divisibile per 7, oppure \"Un supereroe in meno\"\n\n" . "/mischiacolori\n" . "Formato: /mischiacolori [testo] [testo]\n" . "Gara: Gara a squadre locale, quesito 16\n" . "Risposta: una parola formata dalle prime lettere della prima parola e dalle ultime lettere della seconda parola\n\n";
}
if (strpos($text, "/lotteria") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /lotteria funziona solo nei gruppi";
  } else {
    $response = "Benvenuto alla Lotteria del TheScrasse Bot!";
    $inline_keyboard = array(
      'inline_keyboard' => array(
        array(
          array(
            'text' => 'Gioca',
            'url' => "t.me/thescrasse_bot?start=lotteria$chatId"
          )
        ),
        array(
          array(
            'text' => 'Gioca in tutti i gruppi',
            'url' => "t.me/thescrasse_bot?start=lotteriatutti$chatId"
          )
        ),
      )
    );
    $usekeyboard = 1;
  }
}
if (strpos($text, "/glispammoni") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /glispammoni funziona solo nei gruppi";
  } else {
    $response = "Benvenuto al gioco \"Gli Spammoni\" del TheScrasse Bot!";
    $inline_keyboard = array(
      'inline_keyboard' => array(
        array(
          array(
            'text' => 'Gioca',
            'url' => "t.me/thescrasse_bot?start=glispammoni$chatId"
          )
        )
      )
    );
    $usekeyboard = 1;
  }
}
if (strpos($text, "/start lotteria") === 0) {
  $chatIdDest = substr($text, 15);
  $chatIdDest1 = substr($chatIdDest, 0, 5);
  $chatIdDest2 = substr($chatIdDest, 5);
  $chatIdDest2 = $chatIdDest2 + 0;
  if ($chatIdDest1 == "tutti") {
    $sql = "SELECT * FROM lottery WHERE chatId = $chatId ORDER BY time DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $status = $row["status"];
      $time = $row["time"];
      if ($status == 0) {
        $b++;
      }
    }
    $time = $time + 36000;
    $currenttime1 = time() + 36000;
    $limit = floor($currenttime1 / 86400) * 86400 - 36000;
    if ($b > 0) {
      $response = "Stai già giocando alla lotteria in un gruppo";
    } else {
      $sql = "SELECT * FROM lottery WHERE chatId = $chatId AND chatIdDest = $chatIdDest2";
      $result = $conn->query($sql);
      if ($result->num_rows == 0) {
        $sql = "INSERT INTO lottery (chatId, chatIdDest, status, points, totalpoints, time) VALUES ($chatId, $chatIdDest2, 0, 64, 0, 0)";
        $result = $conn->query($sql);
        $sql = "UPDATE isusing SET command = 1 WHERE chatId = $chatId";
        $result = $conn->query($sql);
        $sql = "UPDATE lottery SET status = 0 WHERE chatId = $chatId AND time < $limit";
        $result = $conn->query($sql);
        $response = "Benvenuto alla Lotteria del TheScrasse Bot. Hai 64 Punti Lotteria. Scegli un numero da 1 a 5 per aumentare il tuo punteggio. Se vuoi fermarti, invia /stop";
      } else {
        $currenttime2 = time() + 7200;
        $days1 = floor($currenttime1 / 86400) - floor($time / 86400);
        if ($days1 == 0) {
          $sql = "UPDATE isusing SET command = 0 WHERE chatId = $chatId";
          $result = $conn->query($sql);
          $days2 = floor($currenttime1 / 86400) - floor($currenttime2 / 86400);
          if ($days2 == 0) {
            $response = "Per giocare alla Lotteria torna alle ore 16 di oggi";
          } else {
            $response = "Per giocare alla Lotteria torna alle ore 16 di domani";
          }
        } else {
          $sql = "UPDATE lottery SET status = 0 WHERE chatId = $chatId AND time < $limit";
          $result = $conn->query($sql);
          $sql = "UPDATE isusing SET command = 1 WHERE chatId = $chatId";
          $result = $conn->query($sql);
          $response = "Benvenuto alla Lotteria del TheScrasse Bot. Hai 64 Punti Lotteria. Scegli un numero da 1 a 5 per aumentare il tuo punteggio. Se vuoi fermarti, invia /stop";
        }
      }
    }
  } else if (is_numeric($chatIdDest) == TRUE) {
    $sql = "SELECT * FROM groups WHERE groupId = $chatIdDest";
    $result = $conn->query($sql);
    if ($result->num_rows != 0) {
      $sql = "UPDATE isusing SET command = 1 WHERE chatId = $chatId";
      $result = $conn->query($sql);
      $sql = "SELECT * FROM lottery WHERE chatId = $chatId AND chatIdDest = $chatIdDest";
      $result = $conn->query($sql);
      if ($result->num_rows == 0) {
        $sql = "INSERT INTO lottery (chatId, chatIdDest, status, points, totalpoints, time) VALUES ($chatId, $chatIdDest, 0, 64, 0, 0)";
        $result = $conn->query($sql);
        $response = "Benvenuto alla Lotteria del TheScrasse Bot. Hai 64 Punti Lotteria. Scegli un numero da 1 a 5 per aumentare il tuo punteggio. Se vuoi fermarti, invia /stop";
      } else {
        while ($row = $result->fetch_assoc()) {
          $status = $row["status"];
          $time = $row["time"];
        }
        if ($status == 1) {
          $time = $time + 36000;
          $currenttime1 = time() + 36000;
          $currenttime2 = time() + 7200;
          $days1 = floor($currenttime1 / 86400) - floor($time / 86400);
          if ($days1 == 0) {
            $sql = "UPDATE isusing SET command = 0 WHERE chatId = $chatId";
            $result = $conn->query($sql);
            $days2 = floor($currenttime1 / 86400) - floor($currenttime2 / 86400);
            if ($days2 == 0) {
              $response = "Per giocare alla Lotteria torna alle ore 16 di oggi";
            } else {
              $response = "Per giocare alla Lotteria torna alle ore 16 di domani";
            }
          } else {
            $sql = "SELECT * FROM lottery WHERE chatId = $chatId AND status = 0";
            $result = $conn->query($sql);
            if ($result->num_rows != 0) {
              $response = "Stai già giocando alla lotteria in un altro gruppo";
            } else {
              $sql = "UPDATE lottery SET status = 0 WHERE chatId = $chatId AND chatIdDest = $chatIdDest";
              $result = $conn->query($sql);
              $response = "Benvenuto alla Lotteria del TheScrasse Bot. Hai 64 Punti Lotteria. Scegli un numero da 1 a 5 per aumentare il tuo punteggio. Se vuoi fermarti, invia /stop";
            }
          }
        } else {
          $response = "Non puoi usare questo comando";
        }
      }
    } else {
      $response = "Il codice del gruppo non è valido";
    }
  } else {
    $response = "Il codice del gruppo non è valido";
  }
}
if (strpos($text, "/start glispammoni") === 0) {
  $chatIdDest = substr($text, 18);
  if (is_numeric($chatIdDest) == TRUE) {
    $sql = "SELECT * FROM groups WHERE groupId = $chatIdDest";
    $result = $conn->query($sql);
    if ($result->num_rows != 0) {
  $sql = "UPDATE isusing SET command = 2 WHERE chatId = $chatId";
  $result = $conn->query($sql);
  $sql = "SELECT * FROM spammers WHERE chatId = $chatId AND chatIdDest = $chatIdDest";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql = "INSERT INTO spammers (chatId, chatIdDest, status, points, totalpoints, time) VALUES ($chatId, $chatIdDest, 0, 0, 0, 0)";
    $result = $conn->query($sql);
    $response = "Benvenuto a \"Gli Spammoni\" del TheScrasse Bot. Se vuoi tentare la fortuna, invia /spam a ripetizione!";
  } else {
    while ($row = $result->fetch_assoc()) {
      $status = $row["status"];
    }
    if ($status == 1) {
      $sql = "UPDATE spammers SET status = 0 WHERE chatId = $chatId AND chatIdDest = $chatIdDest";
      $result = $conn->query($sql);
      $response = "Benvenuto a \"Gli Spammoni\" del TheScrasse Bot. Se vuoi tentare la fortuna, invia /spam a ripetizione!";
    } else {
      $response = "Non puoi usare questo comando";
    }
}
} else {
  $response = "Il codice del gruppo non è valido";
}
} else {
  $response = "Il codice del gruppo non è valido";
}
}
if (strpos($text, "/classificalotteria") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /classificalotteria funziona solo nei gruppi";
  } else {
    $response = "";
    $sql = "SELECT * FROM lottery WHERE chatIdDest = $chatId ORDER BY totalpoints DESC LIMIT 10";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $response = "Classifica vuota";
    } else {
      while ($row = $result->fetch_assoc()) {
        $userId1 = $row["chatId"];
        $sql1 = "SELECT * FROM score WHERE userId = $userId1";
        $result1 = $conn->query($sql1);
        while ($row1 = $result1->fetch_assoc()) {
          $username1 = $row1["username"];
        }
        $response = $response . $username1 . ": " . $row["totalpoints"] . "\n";
      }
    }
  }
}
if (strpos($text, "/classificaspammoni") === 0) {
  if ($chatId > 0) {
    $response = "Il comando /classificaspammoni funziona solo nei gruppi";
  } else {
    $response = "";
    $sql = "SELECT * FROM spammers WHERE chatIdDest = $chatId ORDER BY totalpoints DESC LIMIT 10";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
      $response = "Classifica vuota";
    } else {
      while ($row = $result->fetch_assoc()) {
        $userId1 = $row["chatId"];
        $sql1 = "SELECT * FROM score WHERE userId = $userId1";
        $result1 = $conn->query($sql1);
        while ($row1 = $result1->fetch_assoc()) {
          $username1 = $row1["username"];
        }
        $response = $response . $username1 . ": " . $row["totalpoints"] . "\n";
      }
    }
  }
}
if (strpos($text, "/impostagas") === 0) {
  $text = str_replace("/impostagas@thescrasse_bot ", "", $text);
  $text = str_replace("/impostagas ", "", $text);
  $number = base_convert($text, 10, 10);
  $sql = "SELECT * FROM milizia WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $sql1 = "INSERT INTO milizia (groupId, leaderboard) VALUES ($chatId, $number)";
    $result1 = $conn->query($sql1);
    $response = "Hai impostato la gara numero $number";
  } else {
    $sql1 = "UPDATE milizia SET leaderboard = $number WHERE groupId = $chatId";
    $result1 = $conn->query($sql1);
    $response = "Hai impostato la gara numero $number";
  }
}
if (strpos($text, "/posizionegas") === 0) {
  $sql = "SELECT * FROM milizia WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $response = "Non hai impostato una gara. Impostala con il comando /impostagas";
  } else {
    while ($row = $result->fetch_assoc()) {
      $leadid = $row["leaderboard"];
    }
    $text = str_replace("/posizionegas@TheScrasse_Bot ", "", $notstrtoloweredtext);
    $text = str_replace("/posizionegas ", "", $text);
    $page = file_get_contents("https://stefanomilizia.altervista.org/GaS/classifica.php?idgara=$leadid");
    $pos = strpos($page, $text);
    $length = strlen($text);
    $check1 = substr($page, $pos - 1, 1);
    $check2 = substr($page, $pos + $length, 1);
    if (!($check1 == ">" && $check2 == "<")) {
      $response = "La squadra non esiste. Simboli trovati:\n$check1\n$check2";
    } else {
      $substr1 = substr($page, $pos - 35, 10);
      $substr1 = base_convert($substr1, 10, 10);
      $substr2 = substr($page, $pos + $length + 24, 15);
      $substr2 = base_convert($substr2, 10, 10);
      $response = "La squadra \"$text\" si trova in posizione $substr1 e ha $substr2 punti";
    }
  }
}
if (strpos($text, "/classificagas") === 0) {
  $response = "";
  $sql = "SELECT * FROM milizia WHERE groupId = $chatId";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    $response = "Non hai impostato una gara. Impostala con il comando /impostagas";
  } else {
    while ($row = $result->fetch_assoc()) {
      $leadid = $row["leaderboard"];
    }
    $text = "<td class=\"squadra\">";
    $text1 = "<div class=\"titolo\">";
    $text2 = "<div class=\"rimanenti\">";
    $leadsite = "https://stefanomilizia.altervista.org/GaS/classifica.php?idgara=$leadid";
    $filesite = "https://stefanomilizia.altervista.org/GaS/testo.php?idgara=$leadid";
    $page = file_get_contents($leadsite);
    if (strpos($page, $text1) === false) {
      $response = "La gara non esiste. Imposta un'altra gara con il comando /impostagas";
    } else {
      if (strpos($page, $text2) === false) {
        $text2 = "<div id=\"timeMessage\" class=\"rimanenti\">";
      }
      $exploded = explode($text, $page);
      $exploded1 = explode($text1, $page);
      $exploded2 = explode($text2, $page);
      $title = explode("</div>", $exploded1[1]);
      $info = explode("</div>", $exploded2[1]);
      $response = $title[0] . "\n\n" . $info[0] . "\n\n";
      $i = 1;
      while ($exploded[$i] != "") {
        $team = explode("</td>", $exploded[$i]);
        $response = $response . $i . " - " . $team[0] . " - " . substr($team[1], 19) . "\n";
        $i++;
      }
      $response = $response . "\nClassifica completa: $leadsite\nTesto: $filesite";
    }
  }
}
if (strpos($text, "/countdown") === 0) {
  $counter = 1;
  $timeresp = 1555765200 - time();
  $s = $timeresp % 60;
  if ($s == 1) {
    $s = "1 secondo";
  } else {
    $s = "$s secondi";
  }
  $m = floor(($timeresp % 3600) / 60);
  if ($m == 1) {
    $m = "1 minuto";
  } else {
    $m = "$m minuti";
  }
  $h = floor(($timeresp % 86400) / 3600);
  if ($h == 1) {
    $h = "1 ora";
  } else {
    $h = "$h ore";
  }
  $d = floor($timeresp / 86400);
  if ($d == 1) {
    $d = "1 giorno";
  } else {
    $d = "$d giorni";
  }
  if ($timeresp < 0) {
    $response = "La One Hundred è già iniziata!";
  } else {
    if ($d != 0) {
      $response = "Tempo rimanente per la One Hundred: $d, $h, $m, $s";
    } else if ($h != 0) {
      $response = "Tempo rimanente per la One Hundred: $h, $m, $s";
    } else if ($m != 0) {
      $response = "Tempo rimanente per la One Hundred: $m, $s";
    } else {
      $response = "Tempo rimanente per la One Hundred: $s";
    }
  }
}
if (strpos($text, "/mcd") === 0) {
  $text = str_replace("/mcd@thescrasse_bot ", "", $text);
  $text = str_replace("/mcd ", "", $text);
  $args = explode(" ", $text);
  if (count(args) != 2) {
    $response = 'Inserisci due numeri!';
  } else {
    $response = mcd((int)$args[0], (int)$args[1]);
  }
}
if (strpos($text, "/mcm") === 0) {
  $text = str_replace("/mcm@thescrasse_bot ", "", $text);
  $text = str_replace("/mcm ", "", $text);
  $args = explode(" ", $text);
  if (count(args) != 2) {
    $response = 'Inserisci due numeri!';
  } else {
    $a = (int)$args[0];
    $b = (int)$args[1];
    $response = $a * $b / mcd($a, $b);
  }
}
if ($response != '' && $usekeyboard == 1) {
  $parameters = array(
    'chat_id' => $chatId,
    "text" => $response,
    "reply_markup" => $inline_keyboard
  );
}
if ($response != '' && $usekeyboard == 0) {
  $parameters = array(
    'chat_id' => $chatId,
    "text" => $response
  );
}
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
?>
