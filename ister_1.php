<?php


?>

<!DOCTYPE html>
<html>
  <?php include('templates/header.php'); ?>
  <?php include('templates/navbar.php'); ?>

  <div class="form_style">
  <form action="web_crawler.php" method="POST">
    <label for="URL">URL:</label><br>
    <input type="text" id="URL" name="URL"  required ><br>
    <input type="submit" style="margin-top: 20px;">
  </form>
  </div>
  
  <?php include('templates/footer.php'); ?>

</html>