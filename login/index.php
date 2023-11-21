<?php
# Inicializa a sessão
session_start();

# Verifica se o usuário já está logado; se sim, redireciona-o para a página de certificates
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE) {
  echo "<script>" . "window.location.href='../certificates'" . "</script>";
  exit;
}

# Inclui a conexão com o banco de dados
require_once "../config.php";

# Define variáveis e inicializa com valores vazios
$user_login_err = $user_password_err = $login_err = "";
$user_login = $user_password = "";

# Processa os dados do formulário quando o formulário é enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["user_login"]))) {
    $user_login_err = "Por favor, insira seu nome de usuário ou um endereço de e-mail.";
  } else {
    $user_login = trim($_POST["user_login"]);
  }

  if (empty(trim($_POST["user_password"]))) {
    $user_password_err = "Por favor, insira sua senha.";
  } else {
    $user_password = trim($_POST["user_password"]);
  }

  # Valida as credenciais
  if (empty($user_login_err) && empty($user_password_err)) {
    # Prepara uma declaração SELECT
    $sql = "SELECT id, username, password FROM users WHERE username = ? OR email = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      # Associa as variáveis à declaração como parâmetros
      mysqli_stmt_bind_param($stmt, "ss", $param_user_login, $param_user_login);

      # Define os parâmetros
      $param_user_login = $user_login;

      # Executa a declaração
      if (mysqli_stmt_execute($stmt)) {
        # Armazena o resultado
        mysqli_stmt_store_result($stmt);

        # Verifica se o usuário existe; se sim, verifica a senha
        if (mysqli_stmt_num_rows($stmt) == 1) {
          # Associa os valores do resultado às variáveis
          mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);

          if (mysqli_stmt_fetch($stmt)) {
            # Verifica se a senha está correta
            if (password_verify($user_password, $hashed_password)) {

              # Armazena os dados nas variáveis de sessão
              $_SESSION["id"] = $id;
              $_SESSION["username"] = $username;
              $_SESSION["loggedin"] = TRUE;

              # Redireciona o usuário para a página de certificates
              echo "<script>" . "window.location.href='../certificates'" . "</script>";
              exit;
            } else {
              # Se a senha estiver incorreta, exibe uma mensagem de erro
              $login_err = "O e-mail ou senha que você digitou está incorreto.";
            }
          }
        } else {
          # Se o usuário não existir, exibe uma mensagem de erro
          $login_err = "Nome de usuário ou senha inválidos.";
        }
      } else {
        echo "<script>" . "alert('Ops! Algo deu errado. Por favor, tente novamente mais tarde.');" . "</script>";
        echo "<script>" . "window.location.href='../login'" . "</script>";
        exit;
      }

      # Fecha a declaração
      mysqli_stmt_close($stmt);
    }
  }

  # Fecha a conexão com o banco de dados
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
  <link rel="stylesheet" href="../css/main.css">
  <link rel="shortcut icon" href="../img/new-logo.png" type="image/x-icon">
  <script defer src="../js/script.js"></script>
  <!-- Adsense -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6667247105321030" crossorigin="anonymous"></script>
  <!-- Adsense -->
</head>

<body>
  <div class="container">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-lg-5">
        <?php
        if (!empty($login_err)) {
          echo "<div class='alert alert-danger'>" . $login_err . "</div>";
        }
        ?>
        <div class="form-wrap border rounded p-4">
          <h1>Entrar</h1>
          <p>Por favor faça o login para entrar</p>
          <!-- form starts here -->
          <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="user_login" class="form-label">Email</label>
              <input type="text" class="form-control" name="user_login" id="user_login" value="<?= $user_login; ?>">
              <small class="text-danger"><?= $user_login_err; ?></small>
            </div>
            <div class="mb-2">
              <label for="password" class="form-label">Senha</label>
              <input type="password" class="form-control" name="user_password" id="password">
              <small class="text-danger"><?= $user_password_err; ?></small>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="togglePassword">
              <label for="togglePassword" class="form-check-label">Ver senha</label>
            </div>
            <div class="mb-3">
              <input type="submit" class="btn btn-primary form-control button" name="submit" value="Entrar">
            </div>
            <p class="mb-0">Não tem uma conta? <a href="../register">Inscrever-se</a></p>
          </form>
          <!-- form ends here -->
        </div>
      </div>
    </div>
  </div>
</body>

</html>