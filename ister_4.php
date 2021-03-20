<?php
  include("findFunctions.php");
  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $URLS = $_POST["URLS"];
    $URL_array = explode("\n",$URLS);
    $number = 1;
    foreach($URL_array as $URL){
      echo "$number-) $URL <br>";
      $number++;
    }
   
  }
  
?>

<!DOCTYPE html>
<html>
  <?php include('templates/header.php'); ?>
  <?php include('templates/navbar.php'); ?>

  <div class="form_style">
    <h4> Site indexleme ve siralama.<br></h4>
    <h6> Her url girişin sonunda enter tuşuna basarak bir alt satır geçilmelidir.</h6>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <label for="URLS"><h6> URLS:</h6></label><br>
      <textarea name="URLS" rows="20" cols="200"></textarea><br>
      <input type="submit" style="margin-top: 20px;">
    </form>
  </div>


  <?php include('templates/footer.php'); ?>

</html>