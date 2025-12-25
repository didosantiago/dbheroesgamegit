<?php
session_start();
require_once 'config.php';
require_once 'classes/Core.php';
require_once 'classes/Treino.php';

$core = new Core();
$treino = new Treino();
$user = $core->getUser();
$id = $user->guerreiro;

echo "<h2>REGENERATION TEST</h2>";
echo "<pre>";

// Get BEFORE values
$sql = "SELECT * FROM usuarios_personagens WHERE id = $id";
$stmt = DB::prepare($sql);
$stmt->execute();
$before = $stmt->fetch();

echo "=== BEFORE RECOVERY ===\n";
echo "HP: " . $before->hp . "\n";
echo "KI Usado: " . $before->ki_usado . " (Available: " . ($before->mana - $before->ki_usado) . ")\n";
echo "Energia Usada: " . $before->energia_usada . " (Available: " . ($before->energia - $before->energia_usada) . ")\n";
echo "time_hp: " . $before->time_hp . " | Now: " . time() . " | Diff: " . (time() - $before->time_hp) . " sec\n";
echo "time_ki: " . $before->time_ki . " | Now: " . time() . " | Diff: " . (time() - $before->time_ki) . " sec\n";
echo "time_stamina: " . $before->time_stamina . " | Now: " . time() . " | Diff: " . (time() - $before->time_stamina) . " sec\n\n";

// Call recovery functions
echo "=== CALLING RECOVERY FUNCTIONS ===\n";
$treino->recoveryEnergia($id, $user->vip);
echo "✓ recoveryEnergia() called\n";

$treino->recoveryKI($id, $user->vip);
echo "✓ recoveryKI() called\n";

$treino->recoveryHP($id, $user->vip);
echo "✓ recoveryHP() called\n\n";

// Get AFTER values
$stmt->execute();
$after = $stmt->fetch();

echo "=== AFTER RECOVERY ===\n";
echo "HP: " . $after->hp . " (" . ($after->hp != $before->hp ? "CHANGED +" . ($after->hp - $before->hp) : "NO CHANGE") . ")\n";
echo "KI Usado: " . $after->ki_usado . " (" . ($after->ki_usado != $before->ki_usado ? "CHANGED -" . ($before->ki_usado - $after->ki_usado) : "NO CHANGE") . ")\n";
echo "Energia Usada: " . $after->energia_usada . " (" . ($after->energia_usada != $before->energia_usada ? "CHANGED -" . ($before->energia_usada - $after->energia_usada) : "NO CHANGE") . ")\n";
echo "time_hp: " . $after->time_hp . " (" . ($after->time_hp != $before->time_hp ? "UPDATED" : "NOT UPDATED") . ")\n";
echo "time_ki: " . $after->time_ki . " (" . ($after->time_ki != $before->time_ki ? "UPDATED" : "NOT UPDATED") . ")\n";
echo "time_stamina: " . $after->time_stamina . " (" . ($after->time_stamina != $before->time_stamina ? "UPDATED" : "NOT UPDATED") . ")\n";

echo "</pre>";
?>
