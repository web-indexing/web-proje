<?php
  include("findFunctions.php");
  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $URLS = $_POST["URLS"];
    $URL = $_POST["URL"];
    comparisonAll($URLS, $URL);
    // newline'a göre url'leri ayırdık.
    $_GLOBAL["url_array"] = explode("\n",$URLS);
    findChildUrls($_GLOBAL["url_array"]);
    
  }

  function comparisonAll($URLS, $URL){
    //OLAY BURADA GEÇECEK DE NASIL GEÇECEK ALTTAKİ FONKSİYONLAR DA DEĞİŞİR BÜYÜK İHTİMALLE
    //ALTTAKİ FONKSİYON ŞİMDİLİK LİNKLERİ GÖSTERİYOR FİLTRELEYİP
  }

  function findChildUrls($urls){
    foreach($urls as $url){
      $already_crawled = array();
      $crawling = array();
      $html = file_get_html(trim($url));
      $links = $html->find('a');
     
      foreach($links as $link){
        $l =  $link->href;
        
        $l = linkFilter($l, $url);
        if($l == null){
          continue;
        }

        // If the link isn't already in our crawl array add it, otherwise ignore it.
        if (!in_array($l, $already_crawled)) {
            $already_crawled[] = $l;
            $crawling[] = $l;
            echo "$l <br>";
            
        }
      }
    }
  }
  
  function linkFilter($l, $url){
    // Process all of the links we find. This is covered in part 2 and part 3 of the video series.
    if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
      return parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
    } else if (substr($l, 0, 2) == "//") {
      return parse_url($url)["scheme"].":".$l;
    } else if (substr($l, 0, 2) == "./") {
      return parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
    } else if (substr($l, 0, 1) == "#") {
      return parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
    } else if (substr($l, 0, 3) == "../") {
      return parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
    } else if (substr($l, 0, 11) == "javascript:") {
      return null;
    } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
      return parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
    }
  }

?>

<!DOCTYPE html>
<html>
  <?php include('templates/header.php'); ?>
  <?php include('templates/navbar.php'); ?>

  <div class="form_style">
    <h4> Site indexleme ve siralama.<br></h4>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
      <label for="URL"><h6> Karsilastirilicak olan web sitesi:</h6></label><br>
      <textarea name="URL" rows="1" cols="100"></textarea><br>
      <h6> Her url girişin sonunda enter tuşuna basarak bir alt satır geçilmelidir.</h6>
      <label for="URLS"><h6> Web sitesi kumesi:</h6></label><br>
      <textarea name="URLS" rows="20" cols="200"></textarea><br>
      <input type="submit" style="margin-top: 20px;">
    </form>
  </div>


  <?php include('templates/footer.php'); ?>

</html>