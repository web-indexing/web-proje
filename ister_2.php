<?php
  include('findfunctions.php');
  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $_GLOBAL["freqArray1"] = findFreq($_POST["URL1"]);
    $_GLOBAL["freqArray2"] = findFreq($_POST["URL2"]);
    
  }
  
  function findKeyword($wordFreqArray){
    $stopwords = array("is","not","that","there","are","can","you","with","of","those","after","all","one");
    $keywords;
    //stopwordslerin bulunmadığı yeni bir array oluşturuyoruz.
    
    foreach($wordFreqArray as $key => $value){
      $inside = 1;
      for($i = 0; $i<count($stopwords); $i++){
        
        if($key == $stopwords[$i]){
          $inside = 0;
        }
       
      }
      if($inside){
        $keywords[$key] = $value;;
      }
    
    }
    
    //BURADA FREKANSA GORE VS KEYWORDLERİ BULUP DONDUREBİLİRİZ. şimdilik yukardaki kadar.

    return $keywords;
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
    $keywords1 = findKeyword($_GLOBAL["freqArray1"]);
    $keywords2 = findKeyword($_GLOBAL["freqArray2"]);
    if(($keywords1 != null) && ($keywords2 != null)){
      printFreq($keywords1);
      printFreq($keywords2);
    }
    
    ?>

  </div>


  <?php include('templates/footer.php'); ?>

</html>