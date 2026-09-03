<?php
require_once('../config/db.php');
require_once('validation_input.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ProductName = $_POST['ProductName'];
    $ProductPrice = $_POST['ProductPrice'];
    $ProductDescription = $_POST['ProductDescription'];
    $ProductAvaibility = $_POST['ProductAvaibility'];
    $ProductImage = $_FILES['ProductImage']['name'];
    $OldProductImage = $_POST['OldProductImage'];


    if (isset($_POST['ProductStock']))
        $ProductStock = 1;
    else
        $ProductStock = 0;

    $errors = validationInput(
        $ProductName,
        $ProductPrice,
        $ProductDescription,
        $ProductAvaibility,
        $_FILES['ProductImage'],
        $OldProductImage
    );

    if (!empty($errors)) {
        header("Location: ../index.php?status=errorAdd");
    } else {
        $ProductImageUnique = "img" . time() . '_' . $ProductImage;

        $destination = "../assets/uploads/$ProductImageUnique";
        $tmpName = $_FILES['ProductImage']['tmp_name'];

        move_uploaded_file($tmpName, $destination);

        try {
            $sql = "INSERT INTO products (Image,Name,Price,Description,Avaibility,InStock) 
                VALUES (:ProductImageUnique, :ProductName, :ProductPrice, :ProductDescription, :ProductAvaibility, :ProductStock)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ProductImageUnique' => $ProductImageUnique,
                ':ProductName' => $ProductName,
                ':ProductPrice' => $ProductPrice,
                ':ProductDescription' => $ProductDescription,
                ':ProductAvaibility' => $ProductAvaibility,
                ':ProductStock' => $ProductStock
            ]);
            header("Location: ../index.php?status=succesAdd");
            exit();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Error Add Product");
        }
    }
} else {
    header("Location: ../index.php");
    exit();

}
?>