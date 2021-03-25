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
    $comparisonValue = 1/$sumFreq;
    foreach($tmpArray as $key => $value){
      $comparisonValue *= $value;
    }
    
    return $comparisonValue;
  }

?>
