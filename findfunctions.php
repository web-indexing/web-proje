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
     echo $_GLOBAL["URL"];
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
      

?>
