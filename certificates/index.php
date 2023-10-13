<?php
# Include connection
require_once "../config.php";

# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
  echo "<script>" . "window.location.href='../login';" . "</script>";
  exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $target_dir = "../uploads/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));



  $timeNOw = time();
  $mageName = "image-{$timeNOw}.{$imageFileType}";

  $target_file = $target_dir . $mageName;





  // Check if file already exists
  if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if (
    $imageFileType != "jpg"
    && $imageFileType != "png"
    && $imageFileType != "jpeg"
    && $imageFileType != "pdf"
    && $imageFileType != "webp"
  ) {
    echo "Sorry, only JPG, JPEG, PNG, WEBP & PDF files are allowed.";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
  } else {

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      echo "The file " . $mageName . " has been uploaded.";




      //success upload
      if ((isset($_SESSION["id"]) && !empty($_SESSION["id"])) && !empty($mageName)) {
        $idUser = $_SESSION["id"];

        # Prepare an insert statement
        $sql = "INSERT INTO `certificates`(`id_user`, `image`) VALUES ('$idUser','$mageName')";

        if ($stmt = mysqli_prepare($link, $sql)) {
          # Execute the prepared statement
          if (mysqli_stmt_execute($stmt)) {
            echo "sucesso ao cadastrar no banco";
          } else {
            echo "erro ao cadastrar no banco";
          }

          # Close statement
          mysqli_stmt_close($stmt);
        }

        # Close connection
        mysqli_close($link);
      }
      //success upload


    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  }
}


$myCertificates = [];
if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
  $idUser = $_SESSION["id"];
  $myCertificates = $link->query("SELECT * FROM certificates WHERE id_user = $idUser");
}

//listar certificados



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/main.css">
  <link rel="shortcut icon" href="../img/favicon-16x16.png" type="image/x-icon">
</head>

<body>

  <div class="sidebar">
    <div>
      <img src="../img/imgLogo.png" alt="foto do usuário" />
    </div>
    <a href="../certificates">
      <img class="icons" src="../img/iconHome.png" />
      Certificados
    </a>

    <a href="../myaccount/">
      <img class="icons" src="../img/iconPerfil.png" />
      Perfil
    </a>


    <a href="../logout" class="btnClosed">
      <img class="icons" src="../img/iconSair.png" />
      Sair
    </a>
  </div>
  </div>
  <div class="content">
    <div class="alert alert-success my-5">
      Welcome ! You are now signed in to your account...
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-5 text-center">
        <img src="../img/blank-avatar.jpg" class="img-fluid rounded" alt="User avatar" width="180">
        <h4 class="my-4">Olá, <?= htmlspecialchars($_SESSION["username"]); ?></h4>
        <h4 class="my-4">Id, <?= htmlspecialchars($_SESSION["id"]); ?></h4>
      </div>
    </div>



    <form method="post" enctype="multipart/form-data">
      Select image to upload:
      <input type="file" name="fileToUpload" id="fileToUpload">
      <input type="submit" value="Upload Image" name="submit">
    </form>





    <table>
      <tr>
        <th>id</th>
        <th>name</th>
        <th>action</th>
      </tr>

      <?php foreach ($myCertificates as $item) : ?>
        <tr>
          <td><?= $item['id'] ?></td>
          <td><img src="../uploads/<?= $item['image'] ?>"><?= $item['image'] ?></td>
          <td>remover</td>
        </tr>
      <?php endforeach; ?>
    </table>


  </div>

</body>

</html>