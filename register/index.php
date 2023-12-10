<?php
# Include connection
require_once "../config.php";

# Define variables and initialize with empty values
$username_err = $email_err = $password_err = "";
$username = $email = $password = "";

# Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  # Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } else {
    $username = trim($_POST["username"]);
    if (!ctype_alnum(str_replace(array("@", "-", "_"), "", $username))) {
      $username_err = "Username can only contain letters, numbers and symbols like '@', '_', or '-'.";
    }
  }

  # Validate email 
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email address";
  } else {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email_err = "Please enter a valid email address.";
    } else {
      # Prepare a select statement
      $sql = "SELECT id FROM users WHERE email = ?";

      if ($stmt = mysqli_prepare($link, $sql)) {
        # Bind variables to the statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_email);

        # Set parameters
        $param_email = $email;

        # Execute the prepared statement 
        if (mysqli_stmt_execute($stmt)) {
          # Store result
          mysqli_stmt_store_result($stmt);

          # Check if email is already registered
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email is already registered.";
          }
        } else {
          echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
        }

        # Close statement
        mysqli_stmt_close($stmt);
      }
    }
  }

  # Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } else {
    $password = trim($_POST["password"]);
    if (strlen($password) < 8) {
      $password_err = "Password must contain at least 8 or more characters.";
    }
  }

  # Check input errors before inserting data into database
  if (empty($username_err) && empty($email_err) && empty($password_err)) {
    # Prepare an insert statement
    $sql = "INSERT INTO users(username, email, password) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
      # Bind varibales to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);

      # Set parameters
      $param_username = $username;
      $param_email = $email;
      $param_password = password_hash($password, PASSWORD_DEFAULT);

      # Execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {

        echo "<script>" . "window.location.href='../?s=1';" . "</script>";
        exit;
      } else {
        echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
      }

      # Close statement
      mysqli_stmt_close($stmt);
    }
  }

  # Close connection
  mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User login system</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="../css/home.css">
  <link rel="shortcut icon" href="../img/log.svg" type="image/x-icon">
  <script defer src="../js/script.js"></script>
  <script src="../js/fontawesome.js"></script>
  <!-- Adsense -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6667247105321030" crossorigin="anonymous"></script>
  <!-- Adsense -->
</head>

<body>
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="" method="post" novalidate class="sign-in-form">
          <h1>Cadastre-se</h1>
          <p>Por favor preencha este formulário para se cadastrar</p>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" name="username" id="username" value="<?= $username; ?>" placeholder="Nome de Usuário" />
          </div>
          <div class="input-field">
            <i class="fas fa-envelope"></i>
            <input type="text" name="email" id="email" value="<?= $email; ?>" placeholder="E-mail" />
            <small class="text-danger"><?= $email_err; ?></small>
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" value="<?= $password; ?>" placeholder="Senha" />
            <small class="text-danger"><?= $password_err; ?></small>
          </div>
          <p>
            <input type="checkbox" class="form-check-input" id="togglePassword">
            <label for="togglePassword" class="form-check-label">Ver senha</label>
          </p>
          <input type="submit" value="Login" class="btn solid" />
          <p class="social-text">Nossa plataforma é 100% gratuita e sem custos ocultos.</p>
          <div class="social-media">
            <p class="mb-0">Já tem uma conta? <a href="../">Fazer Login</a></p>
          </div>
        </form>
      </div>
    </div>
    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content description-certificate">
          <h2>Gere seu Link para o LinkedIn</h2>
          <p>Compartilhe seus certificados no LinkedIn em poucos passos simples:</p>
          <p>Crie sua conta em nossa plataforma.</li>
          <p>Carregue seu certificado.</p>
          <p>Gere um link exclusivo para o LinkedIn.</p>
          <p>Compartilhe seu sucesso profissional com o mundo!</p>
        </div>
        <img src="../img/log.svg" class="image" alt="">
      </div>

    </div>
  </div>

</body>

</html>