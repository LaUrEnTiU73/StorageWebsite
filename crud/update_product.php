<?php
require_once('../config/db.php');
require_once('validation_input.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ProductId = $_POST['ProductId'];
    $ProductName = $_POST['ProductName'];
    $ProductPrice = $_POST['ProductPrice'];
    $ProductDescription = $_POST['ProductDescription'];
    $ProductAvaibility = $_POST['ProductAvaibility'];
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
        header("Location: ../index.php?status=errorEdit");

    } else {
        if (
            isset($_FILES['ProductImage']) &&
            $_FILES['ProductImage']['error'] === UPLOAD_ERR_OK
        ) {

            $newImageName = $_FILES['ProductImage']['name'];
            $tmpName = $_FILES['ProductImage']['tmp_name'];
            $ProductImageUnique = "img" . time() . '_' . $newImageName;
            $destination = "../assets/uploads/$ProductImageUnique";
            move_uploaded_file($tmpName, $destination);

            try {
                $sql = "UPDATE products SET 
                    Image=:ProductImageUnique,
                    Name=:ProductName,
                    Price=:ProductPrice,
                    Description=:ProductDescription,
                    Avaibility=:ProductAvaibility,
                    InStock=:ProductStock 
                    WHERE Id=:ProductId";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':ProductImageUnique' => $ProductImageUnique,
                    ':ProductName' => $ProductName,
                    ':ProductPrice' => $ProductPrice,
                    ':ProductDescription' => $ProductDescription,
                    ':ProductAvaibility' => $ProductAvaibility,
                    ':ProductStock' => $ProductStock,
                    ':ProductId' => $ProductId
                ]);
                header("Location: ../index.php?status=succesUpdate");
                exit();
            } catch (PDOException $e) {
                error_log($e->getMessage());
                die("Error Edit Product");
            }
        } else {
            try {
                $sql = "UPDATE products SET 
                    Name=:ProductName,
                    Price=:ProductPrice,
                    Description=:ProductDescription,
                    Avaibility=:ProductAvaibility,
                    InStock=:ProductStock 
                    WHERE Id=:ProductId";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':ProductName' => $ProductName,
                    ':ProductPrice' => $ProductPrice,
                    ':ProductDescription' => $ProductDescription,
                    ':ProductAvaibility' => $ProductAvaibility,
                    ':ProductStock' => $ProductStock,
                    ':ProductId' => $ProductId
                ]);
                header("Location: ../index.php?status=succesUpdate");
                exit();
            } catch (PDOException $e) {
                error_log($e->getMessage());
                die("Error Edit Product");
            }
        }

    }
} else {
    header("Location: ../index.php");
    exit();

}
?>