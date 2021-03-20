<?php
  include("findFunctions.php");

?>

<!DOCTYPE html>
<html>
  <?php include('templates/header.php'); ?>
  <?php include('templates/navbar.php'); ?>

  <div class="form_style">
    <h4> Site indexleme ve siralama.<br> <br></h4>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <label for="URL">URL:</label><br>
      <textarea name="URL" rows="1" cols="50"></textarea><br>
      <input type="submit" style="margin-top: 20px;">
    </form>
  </div>


  <?php include('templates/footer.php'); ?>

</html>