<?php
require_once('../config/db.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $ProductId = $_POST['ProductId'];
  try {
    $sql = "DELETE FROM products WHERE Id=:ProductId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':ProductId' => $ProductId
    ]);
    header('Location: ../index.php?status=succesDeleted');
  } catch (PDOException $e) {
    error_log($e->getMessage());
    die("Error Delete Product");
  }
}
?>