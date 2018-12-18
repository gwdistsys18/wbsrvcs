<?php

function startTimer() {
    $stimer = explode( ' ', microtime() );
    $stimer = $stimer[1] + $stimer[0];
    return $stimer;
}

function endTimer($stimer)
{
    $etimer = explode( ' ', microtime() );
    $etimer = $etimer[1] + $etimer[0];
    return $etimer - $stimer;
}

// send a request to host as: $url?$qs
function makeReq($url,$qs) {
    $url = "http://$url/wbsrvcs/wbsrvcs.php";
    $uri = "$url?$qs";
    $cobj=curl_init($uri);
    curl_setopt($cobj,CURLOPT_RETURNTRANSFER,1);
    $xml=curl_exec($cobj);
    curl_close($cobj);
    return $xml;
}

// do a computation bound loop
function loop($cnt) {
    $tmp = 22;
    $base=5000;
    /* Can configure the value of $base to control work required. */
    //$cnt=$cnt*$cnt;
    for ($i =1; $i < $cnt*$base; $i++)
    {
        $tmp += $i;
        $tmp = $tmp/2;
    }
    $tmp = $tmp*2;
}

// convert a multidimensional array to url save and encoded string
// usage: string Array2String( array Array )

function Array2String($Array) {
  $Return='';
  $NullValue="^^^";
  foreach ($Array as $Key => $Value) {
   if(is_array($Value))
     $ReturnValue='^^array^'.Array2String($Value);
   else
     $ReturnValue=(strlen($Value)>0)?$Value:$NullValue;
   $Return.=urlencode($Key) . '|' . urlencode($ReturnValue).'||';
  }
  return urlencode(substr($Return,0,-2));
}

function String2Array($String) {
  if(!$String)
    return array();
  $Return=array();
  $String=urldecode($String);
  $TempArray=explode('||',$String);
  $NullValue=urlencode("^^^");
  foreach ($TempArray as $TempValue) {
   list($Key,$Value)=explode('|',$TempValue);
   $DecodedKey=urldecode($Key);
   if($Value!=$NullValue) {
     $ReturnValue=urldecode($Value);
     if(substr($ReturnValue,0,8)=='^^array^')
       $ReturnValue=String2Array(substr($ReturnValue,8));
     $Return[$DecodedKey]=$ReturnValue;
     }
   else
     $Return[$DecodedKey]=NULL;
  }
  return $Return;
}


?>
