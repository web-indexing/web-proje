<?php
  include('findfunctions.php');
  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $_GLOBAL["freqArray"] = findFreq($_POST["URL"]);
  }

?>

<!DOCTYPE html>
<html>
  <?php include('templates/header.php'); ?>
  <?php include('templates/navbar.php'); ?>

  
  <div class="form_style">
    <h4> Sayfada Geçen Kelimelerin Frekanslarını hesaplama.<br> <br></h4>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <label for="URL">URL:</label><br>
      <textarea name="URL" rows="1" cols="50"></textarea><br>
      <input type="submit" style="margin-top: 20px;">
    </form>
    
    <?php
    if($_GLOBAL['freqArray'] != null){
      printFreq($_GLOBAL["freqArray"]);
    }
    
    ?>
    
  </div>

  
  <?php include('templates/footer.php'); ?>

</html>