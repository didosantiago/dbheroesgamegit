<?php
session_start();
require_once './init.php';

// Your character ID (change this to your actual character ID)
$test_character_id = 1;

// MANUALLY insert a test reward
$sql = "INSERT INTO personagens_new_valores (idPersonagem, gold, exp, visualizado) 
        VALUES (?, 100, 50, 0)";
$stmt = DB::prepare($sql);
$stmt->execute([$test_character_id]);

echo "Test reward inserted! Now go to your portal page - popup should appear!";
?>
