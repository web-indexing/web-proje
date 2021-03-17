<?php
  
  //Getting URL from form.
  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $_GLOBAL["URL"] = test_input($_POST["URL"]);
  }

  //Security function.
  function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
      
      //html_dom_parser kütüphanesini ekledik.
      //LİNK = "https://simplehtmldom.sourceforge.io/" 
      include('simple_html_dom.php');

      //html dosyasını aldık.
      $html = file_get_html($_GLOBAL["URL"]);

      //tagsiz halini aldık
      $htmlPlainText = $html->plaintext;

      //filtrele
      $htmlPlainText = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $htmlPlainText);
      $htmlPlainText = preg_replace('/[0-9]+/', '', $htmlPlainText);
      $htmlPlainText = str_ireplace(array('nbsp','gt','lt','div','br'),' ',$htmlPlainText);
      $htmlPlainText = strtolower($htmlPlainText);

      //kelimelerin frekansları hesaplanır.
      $wordArray = str_word_count($htmlPlainText,1);
      $wordFreqArray = array_count_values($wordArray);
      arsort($wordFreqArray);
      
      //bulunan kelimelerin frekanslarını ekrana yazdır. (uzunluğu 3'den küçükse yazdırma)
      if($wordFreqArray != null){
        echo $_GLOBAL["URL"];
        echo "<br><br>
        <table>
        <tr>
          <th>Kelime</th>
          <th>Frekans</th> 
        </tr>";
      }
      
      foreach($wordFreqArray as $key => $value){
        if(strlen($key) >= 3){
          echo "<tr>
                <td>$key</td>
                <td>$value</td>
              </tr>";
        }
        
      }
      echo "</table>";
    ?>
    
  </div>

  
  <?php include('templates/footer.php'); ?>

</html>