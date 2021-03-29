<?php
  include('findfunctions.php');
  $keywords1;
  $keywords2;
  $similarityScore;

  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $_GLOBAL["freqArray1"] = findFreq($_POST["URL1"]);
    $_GLOBAL["freqArray2"] = findFreq($_POST["URL2"]);
    $keywords1 = findKeyword($_GLOBAL["freqArray1"]);
    $keywords2 = findKeyword($_GLOBAL["freqArray2"]);
    $similarityScore = findComparison($keywords1, $keywords2);
  }
  
  
  
?>

<!DOCTYPE html>
<html>
  <?php include('templates/header.php'); ?>
  <?php include('templates/navbar.php'); ?>

  <div class="form_style">
    <h4> Sayfadaki keywordleri ve sitelerin benzerliklerini bulma.<br> <br></h4>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <label for="URL">Birinci URL:</label><br>
      <textarea name="URL1" rows="1" cols="50"></textarea><br>
      <label for="URL">İkinci URL:</label><br>
      <textarea name="URL2" rows="1" cols="50"></textarea><br>
      <input type="submit" style="margin-top: 20px;">
    </form>

    <?php
    //
    if(($keywords1 != null) && ($keywords2 != null) && ($similarityScore != null)){
      echo "<h1> Keywords </h1>";
      printFreq($keywords1);
      echo "<h1> Keywords </h1>";
      printFreq($keywords2);
      echo "<br> <h6> Benzerlik:</h6> $similarityScore";
    }
    
    ?>

  </div>


  <?php include('templates/footer.php'); ?>

</html>