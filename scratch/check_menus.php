<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '');
$cols = $pdo->query("DESCRIBE settings_menus")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
