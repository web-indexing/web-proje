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
    public $comparison_array;
    public $keyword_array0;
    public $score;
    function __construct($parentURL, $currentDepth,$keyword_array0){
      $this->already_crawled = array();
      $this->comparison_array = array();
      $this->keyword_array0 = $keyword_array0;
      $this->already_crawled[] = $parentURL;
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
      
      echo "($currentDepth) ";
      echo str_repeat("&nbsp&nbsp&nbsp",$currentDepth);
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
    function find_node($base_node, $node_url){
      if($base_node["parent"] != $node_url){
        foreach($base_node["child"] as $child){
          $returnValue = $this->find_node($child,$node_url);
          if($returnValue == null){
            continue;
          }else{
            return $returnValue;
          }
        }
      }else{
        return $base_node;
      }
    }
    function findComparison($base_node,$node_url, $global_keyword_array,$currentDepth){
      $node;
      if($base_node["parent"] != $node_url){
        $node = $this->find_node($base_node,$node_url);
        $this->score = 0.0;
      }else{
        $node = $base_node;
      }
      
      $parent = $node["parent"];
      $child_array = $node["child"];
      
      $score;
      foreach($this->keyword_array0 as $keyword0 => $value0){
        foreach($global_keyword_array[$parent] as $keyword1 => $value1){
          if($keyword0 == $keyword1){
            $score+=$value1;
          }
        }
      }
      $this->score += $score / (10)**$currentDepth;
      foreach($child_array as $child_node){
        $this->findComparison($child_node,$child_node["parent"],$global_keyword_array,$currentDepth+1);
      }
      
      return $this->score;
      
    }
    
  }
  
  function comparisonAll($URLS, $URL){
    //benzerliği bulunacak url'nin frekansı ve keywordleri bulunur.
    $wordFreqArray0 = findFreq(trim($URL));
    $keywordArray0 = findKeyword($wordFreqArray0);

    //bütün alt url'ler de dahil keywordlerin bulunduğu array
    $global_keyword_array = array();
    //url'nin ve alt bağlantıların keywordleri bulunur. arraye eklenir ve ekranda çizdirilir.
    //ağaç yapısı oluşturulur ve ekrana bastırılır.
    //url'nin ve alt bağlantıların benzerlik skorları hesaplanır ve ekrana bastırılır.
    foreach($URLS as $url){
      $urlTree = new UrlTree($url,0,$keywordArray0);
      $allURLS = $urlTree->already_crawled;
      $comparison_array = array();
      echo "<h6>$url sitesinin url agaci</h6> \n";
      UrlTree::printTree($urlTree->node,0);
      //alt url'lerin keywordlerinin bulunması ve ekrana yazdırılma kısmı.
      echo "<br>";
      echo "<h6>$url sitesinin ve alt url baglantilarinin keywordleri</h6> \n";
      foreach((array) $allURLS as $child_url){
        echo "$child_url \n";
        $wordFreqArray2 = findFreq($child_url);
        $keywordArray2 = findKeyword($wordFreqArray2);
        $global_keyword_array[$child_url] = $keywordArray2; 
        printFreq($keywordArray2);
        echo "<br>";
      }
      
      foreach((array) $allURLS as $child_url){
        $comparison_array[$child_url] = $urlTree->findComparison($urlTree->node,$child_url,$global_keyword_array,1);
      }
      
      arsort($comparison_array);
      echo "<h6>URL BENZERLIKLERI: </h6><br>";
      foreach($comparison_array as $child_url => $score){
        echo "$child_url => $score <br>";
      }
      
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
      comparisonAll($url_array, $_GLOBAL['URL']);

    }
    
    ?>
  </div>


  <?php include('templates/footer.php'); ?>

</html>