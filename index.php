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

?>



<?php
# Inicializa a sessão
session_start();

# Verifica se o usuário já está logado; se sim, redireciona-o para a página de certificates
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE) {
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


    <link rel="stylesheet" type="text/css"
        href="./css/home.css">
   

</head>

<?php if ($certificate) : ?>
  <body>
      <section class="features" style="text-align: center;">
        <div>
          <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6667247105321030" crossorigin="anonymous"></script>
          <!-- Adsense -->
          <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-6667247105321030"
            data-ad-slot="8805526510"
            data-ad-format="auto"
            data-full-width-responsive="true">
          </ins>
          <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
          </script>
          <!-- Adsense -->
        </div>
        <div>
        <?php 
              
              $pos = strpos( $certificate, '.pdf' );
  
              if($pos):
            ?>
              <iframe class="iframe2" src="<?= $certificate ?>" frameborder="0"></iframe>  
            <?php 
              else:
            ?>
              <img src="<?= $certificate ?>" alt="Certificados" />
            <?php 
              endif; 
            ?>
        </div>
        <div>
          <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6667247105321030" crossorigin="anonymous"></script>
          <!-- Adsense2 -->
          <ins class="adsbygoogle"
          style="display:block"
          data-ad-client="ca-pub-6667247105321030"
          data-ad-slot="2004314442"
          data-ad-format="auto"
          data-full-width-responsive="true"></ins>
          <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
          <!-- Adsense2 -->
        </div>
      </section>
      </body>
    <?php endif; ?>



    <?php if (!$certificate) : ?>
<body dir="ltr" class="body">
    
    <div id="fullPage">
        <div id="brandingWrapper" class="float">
            <section class="features">
                <h2>Nossas Facilidades</h2>
                <ul>
                  <li>
                    <h3>Compartilhamento Simples</h3>
                    <p>Compartilhe seus certificados facilmente no LinkedIn.</p>
                  </li>
                  <li>
                    <h3>Acesso Gratuito</h3>
                    <p>Nossa plataforma é 100% gratuita e sem custos ocultos.</p>
                  </li>
                </ul>
              </section>
          
              <section class="generate-link">
                <h2>Gere seu Link para o LinkedIn</h2>
                <p>Compartilhe seus certificados no LinkedIn em poucos passos simples:</p>
                <ol>
                  <li>Crie sua conta em nossa plataforma.</li>
                  <li>Carregue seu certificado.</li>
                  <li>Gere um link exclusivo para o LinkedIn.</li>
                  <li>Compartilhe seu sucesso profissional com o mundo!</li>
                </ol>
              </section>
          
        </div>
        <div id="contentWrapper" class="float">
            <div id="content">
                <div id="header">
                    <img class='logoImage' id='companyLogo'
                        src='./img/new-logo.png'
                        alt='Logo site LDV' 
                        width="80"/>
                </div>
                <div id="workArea">

                    <div id="authArea" class="groupMargin">

                        <div id="loginArea">

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
                                <div class="mb-3">
                                  <input type="submit" class="btn btn-primary form-control button" name="submit" value="Entrar">
                                </div>
                                <p class="mb-0">Não tem uma conta? <a href="./register">Inscrever-se</a></p>
                              </form>

                            
                           
                        </div>

                    </div>

                </div>
                <div id="footerPlaceholder"></div>
            </div>
            <div id="footer">
                <div id="footerLinks" class="floatReverse">
                    <div><span id="copyright">&#169; 2016 Microsoft</span></div>
                </div>
            </div>
        </div>
    </div>
    


</body>
<?php endif; ?>

</html>