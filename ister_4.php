<?php
  include("findFunctions.php");
  if($_SERVER["REQUEST_METHOD"] == "POST"){  
    $_GLOBAL["URLS"] = $_POST["URLS"];
    $_GLOBAL["URL"] = $_POST["URL"];
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
  function merge_keyword_arrays($global_keyword_array, $keyword_array){
    foreach($global_keyword_array as $keyword1 => $frequency1){
      foreach($keyword_array as $keyword2 => $frequency2){
        if($keyword1 == $keyword2){
          $new_value = $frequency1 + $frequency2;
          $global_keyword_array[$keyword1] = $new_value;
        }
        else{
          $global_keyword_array[$keyword2] = $frequency2;
        }
      }
    }
    return $global_keyword_array;
  }

  function comparisonAll($URLS, $URL){
    //OLAY BURADA GEÇECEK DE NASIL GEÇECEK ALTTAKİ FONKSİYONLAR DA DEĞİŞİR BÜYÜK İHTİMALLE
    //ALTTAKİ FONKSİYON ŞİMDİLİK LİNKLERİ GÖSTERİYOR FİLTRELEYİP
    
    //benzerliği bulunacak url'nin frekansı ve keywordleri bulunur.
    $wordFreqArray0 = findFreq($URL);
    $keywordArray0 = findKeyword($wordFreqArray0);
    
    //web sitesi kümesindeki her sitenin alt siteleriyle birlikte ağaç yapısı çıkarılır ve ekrana yazdırılır.
    //web sitesi kümesindeki her sitenin ve alt sitelerinin keywordleri gösterilir.
    $global_keyword_array = array();
    foreach($URLS as $url){
      echo str_repeat("*",200);
      $wordFreqArray1 = findFreq($url);
      $global_keyword_array[$url] = findKeyword($wordFreqArray1);
      $urlTree = new UrlTree($url,0);
      echo "<h6>$url sitesinin url agaci</h6> \n";
      UrlTree::printTree($urlTree->node,0);
      $allURLS = $urlTree->already_crawled;
      echo "<br>";
      echo "<h6>$url sitesinin ve alt url baglantilarinin keywordleri</h6> \n";
      echo "$url \n";
      printFreq($global_keyword_array[$url]);
      echo "\n";
      foreach((array) $allURLS as $child_url){
        echo "$child_url \n";
        $wordFreqArray2 = findFreq($child_url);
        $keywordArray2 = findKeyword($wordFreqArray2);
        $global_keyword_array[$url] = merge_keyword_arrays($global_keyword_array[$url],$keywordArray2);
        printFreq($keywordArray2);
        echo "<br>";
      }
      $global_keyword_array[$url] = findKeyword($global_keyword_array[$url]);
      arsort($global_keyword_array[$url]);

    }
  

    echo "<p>Benzerlik siralamasi.<p> \n";
    $global_comparison_array = array();
    foreach($global_keyword_array as $url => $keyword_array){
      $global_comparison_array[$url] = findComparison($keyword_array, $keywordArray0,$wordFreqArray0);
    }
    arsort($global_comparison_array);
    foreach($global_comparison_array as $url => $comparison){
      echo "$url ------> $comparison \n";
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
    <?php

    if(($_GLOBAL['URLS'] != null) && ($_GLOBAL['URL'] != null)){
      $url_array = explode("\n",$_GLOBAL["URLS"]);
      comparisonAll($url_array, $URL);

    }
    
    ?>
  </div>


  <?php include('templates/footer.php'); ?>

</html>