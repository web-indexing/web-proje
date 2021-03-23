<?php
  include("findFunctions.php");
  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $URLS = $_POST["URLS"];
    $URL = $_POST["URL"];
    $url_array = explode("\n",$URLS);
    comparisonAll($url_array, $URL);
    // newline'a göre url'leri ayırdık.
    //findChildUrls($url_array);
    
  }
  class UrlTree{
    public $node;
    public $already_crawled;
    function __construct($parentURL, $currentDepth){
      $this->already_crawled = array();
      $this->node = $this->recursive($parentURL,$currentDepth);
      
    }
    function recursive($parentURL, $currentDepth){
      $node_array = array();
      $node_array["parent"] = $parentURL;
      $node_array["child"] = array();
      if($currentDepth < 2){
        $child_url = array();
        $child_urls = $this->findChildUrl($node_array["parent"],$currentDepth);
        foreach($child_urls as $child_url){
          $node_array["child"][] = $this->recursive($child_url, $currentDepth+1);
        }
      }
      return $node_array;
    }

    function findChildUrl($url, $currentDepth){
      
      $crawling = array();
      $html = file_get_html(trim($url));
      $links = $html->find('a');
      
      foreach($links as $link){
        $l =  $link->href;
        
        $l = $this->linkFilter($l, $url);
        if($l == null){
          continue;
        }
        
        // If the link isn't already in our crawl array add it, otherwise ignore it.
        if (!in_array($l, $this->already_crawled)) {
            $this->already_crawled[] = $l;
            $crawling[] = $l;
            
        }
      }
      return $crawling; 
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

    
    static function printTree($node, $currentDepth){
      $parent = $node["parent"];
      $child_array = $node["child"];
      
      
      echo str_repeat("&nbsp&nbsp",$currentDepth);
      if(empty($child_array)){
        echo "- ";
      }else{
        echo "+ ";
      }

      echo "$parent <br>";

      foreach($child_array as $child){
        self::printTree($child, $currentDepth+1);
      }
      
    }
    
  }
 

  function comparisonAll($URLS, $URL){
    //OLAY BURADA GEÇECEK DE NASIL GEÇECEK ALTTAKİ FONKSİYONLAR DA DEĞİŞİR BÜYÜK İHTİMALLE
    //ALTTAKİ FONKSİYON ŞİMDİLİK LİNKLERİ GÖSTERİYOR FİLTRELEYİP
    
    //FREKANSLAR BULUNUR
    $wordFreqArray0 = findFreq($URL);
    //matris yapisi seklinde olacak. 0.index => 1.url'nin frekans dizisi ... şeklinde
    $wordFreqArray1 = array();
    foreach($URLS as $url){
      $wordFreqArray1[$url] = findFreq(trim($url));
    }
    
    //KEYWORDLER BULUNUR
    $keywordArray0 = findKeyword($wordFreqArray0);
    //matris yapisi şeklinde olacak. 0.index => 1.url'nin keywords dizisi ... şeklinde
    $keywordArray1 = array();
    foreach($wordFreqArray1 as $url => $wordFreqArray){
      $keywordArray1[$url] = findKeyword($wordFreqArray);
    }

    //URL ağacı çıkarılır.
    $urlTree = new UrlTree($URL,0);
    UrlTree::printTree($urlTree->node,0);
    
    /*
    $urlTrees = array();
    foreach($URLS as $url){
      $urlTrees[$url] = new UrlTree($url,0); 
    }
    
    foreach($URLS as $url){
      if($urlTrees[$url] != null){
        UrlTree::printTree($urlTrees[$url],0);
        echo "<br> <br> <br>";
      }
    }
    */

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