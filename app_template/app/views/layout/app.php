<!DOCTYPE html>
<html lang="pl" class="no-js">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="noodp" name="robots">
    <meta content="noydir" name="robots">
    <title>Twoja nowa aplikacja</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php # include 'app/views/shared/_stylesheet.php'; ?>
    <?php # include 'app/views/shared/_javascript.php'; ?>
  </head>
  <body data-env="<?= Config::get('env') ?>">
    <?php
      include 'app/views/shared/_header.php';

      if($path) {
        include $path;
      } else {
        include 'app/views/shared/404.php';
      }

      include 'app/views/shared/_footer.php';
    ?>
  </body>
</html>
