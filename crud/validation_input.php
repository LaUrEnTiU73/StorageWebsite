<?php
function validationInput($inputName, $inputPrice, $inputDescription, $inputData, $inputImage, $oldImage)
{
  $errors = [];

  $trimName = trim($inputName);
  if ($trimName == "")
    $errors[] = "Name is required.";
  else if (mb_strlen($trimName) < 3 || mb_strlen($trimName) > 50)
    $errors[] = "Name must be between 3-50 characters.";


  $numberPrice = str_replace(",", ".", $inputPrice);
  if ($numberPrice == "")
    $errors[] = "Price is required.";
  else if (!is_numeric($numberPrice))
    $errors[] = "Price must contain only numbers and maximum one point.";
  else if ((float) $numberPrice <= 0)
    $errors[] = "Price must be greaten than 0.";


  $trimDescription = trim($inputDescription);
  if ($trimDescription == "")
    $errors[] = "Description is required";
  else if (mb_strlen($trimDescription) < 3 || mb_strlen($trimDescription) > 2000)
    $errors[] = "Description must be between 3-2000 characters.";


  if ($inputData == "")
    $errors[] = "Data is required";


  $imageSize = $inputImage['size'];
  $imageType = $inputImage['type'];
  $imageName = $inputImage['name'];
  if ($imageName != '') {
    if ($imageType != "image/jpeg" && $imageType != "image/png")
      $errors[] = "Image extension must be JPEG or PNG.";
    if ($imageSize > 2097152)
      $errors[] = "Image size must not exceed 2MB ";
  } else if ($oldImage == "") {
    $errors[] = "Image is required";
  }
  return $errors;
}
?>