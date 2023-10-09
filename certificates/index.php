<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
  echo "<script>" . "window.location.href='../login';" . "</script>";
  exit;
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
        <img
          src="../img/imgLogo.png"
          alt="foto do usuário"
        />
      </div>
      <a href="../certificates">
        <img class="icons" src="../img/iconHome.png"/>
        Certificados
      </a>

      <a href="../myaccount/">
        <img class="icons" src="../img/iconPerfil.png"/>
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
  </div>
</body>

</html>