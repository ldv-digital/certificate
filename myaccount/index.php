<?php
# Initialize the session
session_start();

# If the user is not logged in, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
  header("Location: ../login");
  exit;
}

# Include connection
require_once "../config.php";

function updateProfile($link, $id, $username, $password = null)
{

  if (!empty($password)) {

    $stmt = mysqli_prepare($link, "UPDATE `users` SET `username`=?, `password`=? WHERE `id`=?");
    $param_password = password_hash($password, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ssi", $username, $param_password, $id);
  } else {

    $stmt = mysqli_prepare($link, "UPDATE `users` SET `username`=? WHERE `id`=?");
    mysqli_stmt_bind_param($stmt, "si", $username, $id);
  }

  if (mysqli_stmt_execute($stmt)) {
    return true;
  } else {
    return false;
  }
}

$id_user = $_SESSION["id"];
$myCertificates = $link->query("SELECT * FROM users WHERE id = '$id_user'");
$row = $myCertificates->fetch_assoc();
$userName = $row['username'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $userName = $_POST["username"];
  $password = isset($_POST["password"]) ? $_POST["password"] : null;
  $passwordCheck = isset($_POST["passwordCheck"]) ? $_POST["passwordCheck"] : null;

  if ($password && $password != $passwordCheck) {
    header("Location: ../myaccount?e_msg=A senha não corresponde.");
    exit;
  }

  if (updateProfile($link, $id_user, $userName, $password)) {
    header("Location: ../myaccount?s_msg=Atualização realizada com sucesso.");
    exit;
  }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User login system</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/main.css">
  <link rel="shortcut icon" href="../img/log.svg" type="image/x-icon">
  <!-- Adsense -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6667247105321030" crossorigin="anonymous"></script>
  <!-- Adsense -->
</head>

<body>

  <div class="sidebar">
    <div>
      <img src="../img/log.svg" alt="foto do usuário" />
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
    <div class="row justify-content-center">
      <div class="col-lg-5 text-center">

        <h4 class="my-4">Olá, <?= $userName; ?></h4>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
          <div class="mb-3">
            <label for="username" class="form-label">Nome de Usuário</label>
            <input type="text" class="form-control" name="username" id="username" value="<?= $userName; ?>">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" value="<?= $row['email']; ?>" disabled="disabled">
          </div>
          <div class="mb-2">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" name="password" id="password">
          </div>
          <div class="mb-2">
            <label for="password" class="form-label">Confirme a Senha</label>
            <input type="password" class="form-control" name="passwordCheck" id="password">
          </div>

          <div class="mb-3">
            <input type="submit" class="btn btn-primary form-control button" name="submit" value="Atualizar">
          </div>

        </form>
      </div>
    </div>
  </div>
</body>

</html>