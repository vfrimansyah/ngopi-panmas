<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '');
$roles = $pdo->query("SELECT * FROM settings_menu_roles WHERE menu_id IN (2, 70, 71, 72)")->fetchAll(PDO::FETCH_ASSOC);
print_r($roles);
