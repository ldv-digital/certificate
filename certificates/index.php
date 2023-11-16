<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
  header("Location: ../login");
  exit;
}

$idUser = 0;
if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
  $idUser = $_SESSION["id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['remove-file']) && !empty($_POST['remove-file'])) {
    $idCertificate = (int)$_POST['remove-file'];
    $stmt = mysqli_prepare($link, "DELETE FROM certificates WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $idCertificate);
    if (mysqli_stmt_execute($stmt)) {
      mysqli_stmt_close($stmt);
      header("Location: ../certificates?s_msg=Imagem removida com sucesso!!");
      exit;
    } else {
      header("Location: ../certificates?e_msg=Erro ao remover a imagem.");
      exit;
    }
  } else {
    $target_dir = "../uploads/";
    $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
    $timeNow = time();
    $imageName = "{$idUser}-{$timeNow}.{$imageFileType}";
    $target_file = $target_dir . $imageName;
    $uploadOk = 1;

    // Validate file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
      header("Location: ../certificates?e_msg=Tamanho do arquivo muito grande.");
      exit;
    }

    // Allow specific file formats
    $allowedFormats = array("jpg", "jpeg", "png", "pdf", "webp");
    if (!in_array($imageFileType, $allowedFormats)) {
      header("Location: ../certificates?e_msg=Somente arquivos JPG, JPEG, PNG, WEBP e PDF são permitidos.");
      exit;
    }

    if (file_exists($target_file)) {
      header("Location: ../certificates?e_msg=O arquivo já existe.");
      exit;
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      $stmt = mysqli_prepare($link, "INSERT INTO certificates (id_user, image) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "is", $idUser, $imageName);
      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../certificates?s_msg=Sucesso ao cadastrar");
        exit;
      } else {
        header("Location: ../certificates?e_msg=Erro ao cadastrar");
        exit;
      }
    } else {
      header("Location: ../certificates?e_msg=Erro ao fazer o upload do arquivo");
      exit;
    }
  }
}

$myCertificates = [];

if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
  $idUser = $_SESSION["id"];
  $myCertificates = $link->query("SELECT * FROM certificates WHERE id_user = $idUser");
  mysqli_close($link);
}

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
      <img src="../img/new-logo.png" alt="foto do usuário" />
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
    <?php if (isset($_REQUEST['s_msg']) && !empty($_REQUEST['s_msg'])) : ?>
      <div class="alert alert-success my-5">
        <?= $_REQUEST['s_msg'] ?>
      </div>
    <?php endif; ?>
    <?php if (isset($_REQUEST['e_msg']) && !empty($_REQUEST['e_msg'])) : ?>
      <div class="alert alert-error my-5">
        <?= $_REQUEST['e_msg'] ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      Selecione seu Certificado:
      <input class="inputEnv" type="file" name="fileToUpload" id="fileToUpload">
      <input class="inputEnv2" type="submit" value="Enviar Certificado" name="submit">
    </form>





    <table class="table-certificate">
      <tr>
        <th>id</th>
        <th>name</th>
        <th></th>
        <th></th>
      </tr>

      <?php foreach ($myCertificates as $item) : ?>
        <tr>
          <td><?= $item['id'] ?></td>
          <td><img src="../uploads/<?= $item['image'] ?>"></td>
          <td> <a class="btn-grid" href="/?id=<?= base64_encode($item['id']) ?>" target="_blank">Visualizar</a> </td>
          <td>
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="remove-file" value="<?= $item['id'] ?>">
              <input class="btn-grid-del" type="submit" value="remover" name="submit">
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>


  </div>

</body>

</html>