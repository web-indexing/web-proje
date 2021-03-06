<?php
  //html_dom_parser kütüphanesini ekledik.
  //LİNK = "https://simplehtmldom.sourceforge.io/"
include('simple_html_dom.php');
function findFreq($url){
    //html dosyasını aldık.
    $html = file_get_html($url);
    
    //tagsiz halini aldık
    $htmlPlainText = $html->plaintext;
    //filtrele
    $htmlPlainText = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $htmlPlainText);
    $htmlPlainText = preg_replace('/[0-9]+/', '', $htmlPlainText);
    $htmlPlainText = str_ireplace(array('nbsp','gt','lt','div','br','quot','class'),' ',$htmlPlainText);
    $htmlPlainText = strtolower($htmlPlainText);

    //kelimelerin frekansları hesaplanır.
    $wordArray = str_word_count($htmlPlainText,1);
    $wordFreqArray = array_count_values($wordArray);
    arsort($wordFreqArray);
    
    return $wordFreqArray;
}
  
  

  function printFreq($wordFreqArray){
    
    //bulunan kelimelerin frekanslarını ekrana yazdır. (uzunluğu 3'den küçükse yazdırma)
    if($wordFreqArray != null){
     //echo @$_GLOBAL["URL"];
     echo "
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
    $numberOfKeywords = 5;
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
  function findComparison($keywords_1, $keywords_2){
    $dot_product = 0;
    $keyword1_norm = 0;
    $keyword2_norm = 0;
    $control = true;
    foreach($keywords_1 as $key1 => $frequency1){
      $keyword1_norm += ($frequency1)**2;
      foreach($keywords_2 as $key2 => $frequency2){
        if($control){
          $keyword2_norm += ($frequency2)**2;
        }
        if($key1 == $key2){
          $dot_product += ($frequency1 * $frequency2); 
        }
      }
      if($control){
        $control = false;
      }
    }
    $keyword1_norm = ($keyword1_norm)**(1/2);
    $keyword2_norm = ($keyword2_norm)**(1/2);
    $cosine_similarity = $dot_product / ($keyword1_norm * $keyword2_norm );
    return $cosine_similarity;
    
  }
  
?>
