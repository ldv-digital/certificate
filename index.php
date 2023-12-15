<?php

# Include connection
require_once "./config.php";


$certificate = "";
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
  $uri = $_REQUEST['id'];
  $uri = base64_decode($uri);

  $myCertificates = $link->query("SELECT image FROM certificates WHERE id = '$uri'");
  mysqli_close($link);
  $row = $myCertificates->fetch_assoc();
  if (isset($row['image']) && !empty($row['image'])) {
    $certificate = "./uploads/" . $row['image'];
  }
}


# Inicializa a sessão
session_start();

# Verifica se o usuário já está logado; se sim, redireciona-o para a página de certificates
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE && empty($certificate)) {
  echo "<script>" . "window.location.href='./certificates'" . "</script>";
  exit;
}


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
              echo "<script>" . "window.location.href='./certificates'" . "</script>";
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
        echo "<script>" . "window.location.href='./login'" . "</script>";
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
<html lang="pt-BR">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta http-equiv="cache-control" content="no-cache,no-store" />
  <meta http-equiv="pragma" content="no-cache" />
  <meta http-equiv="expires" content="-1" />
  <meta name='mswebdialog-title' content='Connecting to Portal Alunos' />
  <title>Certificados</title>
  <link rel="stylesheet" type="text/css" href="./css/home.css">
  <script src="./js/fontawesome.js"></script>
</head>

<?php if ($certificate) : ?>

  <body>
    <section class="features" style="text-align: center;">
      <div>
        <?php

        $pos = strpos($certificate, '.pdf');

        if ($pos) :
        ?>
          <iframe class="iframe2" src="<?= $certificate ?>" frameborder="0"></iframe>
        <?php
        else :
        ?>
          <img style="width: 100%;" src="<?= $certificate ?>" alt="Certificados" />
        <?php
        endif;
        ?>
      </div>
    </section>
  </body>
<?php endif; ?>



<?php if (!$certificate) : ?>

  <body>
    <div class="container">
      <div class="forms-container">
        <div class="signin-signup">
          <form action="" method="post" novalidate class="sign-in-form">
            <h2 class="title">Entrar</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="user_login" id="user_login" value="<?= $user_login; ?>" placeholder="E-mail" />
              <small class="text-danger"><?= $user_login_err; ?></small>
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="user_password" id="password" placeholder="Senha" />
              <small class="text-danger"><?= $user_password_err; ?></small>
            </div>
            <input type="submit" value="Login" class="btn solid" />
            <p class="social-text">Nossa plataforma é 100% gratuita e sem custos ocultos.</p>
            <div class="social-media">
              <p class="mb-0">Não tem uma conta? <a href="./register">Inscrever-se</a></p>
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
          <img src="./img/log.svg" class="image" alt="">
        </div>

      </div>
    </div>

    <?php if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) : ?>
      <div class="toast">
        <div class="toast-content">
          <i class="fas fa-solid fa-check check"></i>

          <div class="message">
            <span class="text text-1">Sucesso</span>
            <span class="text text-2">Registro concluído com sucesso</span>
          </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>

        <div class="progress"></div>
      </div>

      <script>
        const toast = document.querySelector(".toast"),
          closeIcon = document.querySelector(".close"),
          progress = document.querySelector(".progress");

        let timer1, timer2;

        toast.classList.add("active");
        progress.classList.add("active");

        timer1 = setTimeout(() => {
          toast.classList.remove("active");
        }, 5000); //1s = 1000 milliseconds

        timer2 = setTimeout(() => {
          progress.classList.remove("active");
        }, 5300);


        closeIcon.addEventListener("click", () => {
          toast.classList.remove("active");

          setTimeout(() => {
            progress.classList.remove("active");
          }, 300);

          clearTimeout(timer1);
          clearTimeout(timer2);
        });
      </script>
    <?php endif; ?>

  </body>

<?php endif; ?>

</html>