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

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User login system</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/main.css">
  <link rel="shortcut icon" href="./img/favicon-16x16.png" type="image/x-icon">
</head>

<body>

  <?php if ($certificate) : ?>
    <section class="features" style="text-align: center;">
      <div>
        Google AdSense
      </div>
      <div>
        <img src="<?= $certificate ?>" alt="Certificados" />
      </div>
      <div>
        Google AdSense
      </div>
    </section>
  <?php endif; ?>
  <?php if (!$certificate) : ?>
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


  <?php endif; ?>

</body>

</html>