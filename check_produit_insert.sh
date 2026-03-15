#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

ref="TEST-$(date +%s)"

php -r '
require "connexion.php";
$ref = $argv[1];
$before = $bdd->prepare("SELECT COUNT(*) FROM Produits WHERE Ref = ?");
$before->execute([$ref]);
$b = (int)$before->fetchColumn();

$ins = $bdd->prepare("INSERT INTO Produits (Ref, Cat, Nom, Prix, Marque) VALUES (?, ?, ?, ?, ?)");
$ins->execute([$ref, "ordinateur", "Produit test", 99.99, "Script"]);

$after = $bdd->prepare("SELECT COUNT(*) FROM Produits WHERE Ref = ?");
$after->execute([$ref]);
$a = (int)$after->fetchColumn();

echo "REF=$ref" . PHP_EOL;
echo "Before=$b" . PHP_EOL;
echo "After=$a" . PHP_EOL;
echo $a === 1 ? "RESULT=OK" . PHP_EOL : "RESULT=FAILED" . PHP_EOL;
' "$ref"
