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
    $similarityScore = findComparison($keywords1, $keywords2, $_GLOBAL["freqArray2"]);
  }
  //keyword bulma fonksiyonu geriye keyword dizisini döndürür.
  function findKeyword($wordFreqArray){
    //stopwordsleri stopwords.txt dosyasından alarak dizi oluşturuyoruz.
    $stopwords = array();
    $stopwordsFile = fopen("stop_words_english.txt","r");
    while(!feof($stopwordsFile)){

      $stopwords[] = trim(fgets($stopwordsFile));
    }
    fclose($stopwordsFile);
    
    //stopwordslerin bulunmadığı yeni bir array oluşturuyoruz.
    $filteredWordFreqArray = array();
    foreach($wordFreqArray as $key => $value){
      $inside = 1;
      foreach($stopwords as $stopword){
        
        if(strcmp($key,$stopword) == 0){
          $inside = 0;
          break;
        }
       
      }
      if($inside){
        $filteredWordFreqArray[$key] = $value;
      }
    
    }
    
    //ŞİMDİLİK EN YÜKSEK FREKANSA SAHİP 5 KELİMEYİ KEYWORD OLARAK SEÇİYORUZ
    arsort($filteredWordFreqArray);
    $keywords = array();
    $numberOfKeywords = 10;
    foreach($filteredWordFreqArray as $keyword => $frequency){
      if($numberOfKeywords > 0){
        $keywords[$keyword] = $frequency;
        $numberOfKeywords--;
      }else{
        break;
      }
    }



    return $keywords;
  }
  
  //iki site arasındaki benzerliği bulmamıza yarayan fonksiyon
  function findComparison($wordFreqArray1, $wordFreqArray2, $wordFreqArray3){
    $tmpArray = array();
    
    foreach($wordFreqArray1 as $key1 => $value1){
      foreach($wordFreqArray2 as $key2 => $value2){
        if($key1 == $key2){
          $tmpArray[$key1] = $value2;
          break;
        }
      }
    }

    $sumFreq = 0;
    foreach($wordFreqArray3 as $key => $value){
      $sumFreq += $value;
    }

    $multiply = 1;
    foreach($tmpArray as $key => $value){
      $multiply *= $value;
    }
    
    return $multiply / $sumFreq;
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
    //&& ($similarityScore != null)
    if(($keywords1 != null) && ($keywords2 != null) ){
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