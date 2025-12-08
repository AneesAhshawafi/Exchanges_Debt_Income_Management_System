<?php
// $arr=[1,2,3,4,5,6];
// echo "<pre>";
// print_r(array_chunk($arr,2));
// echo "</pre>";


// echo "<br>";
// $str="aaaa,bbbbb,ccccc,dddddd";
// echo "explode"."<br>";
// $result=explode(",",$str,4);
// $result=explode(",",$str,3);
// print_r($result);
// echo "<br>";
// echo "implode"."<br>";
// $array1=array("wwww","anees","org");
// echo (implode("/",$array1)."<br>");

// function square($array){
//     return $array%2==0;
// }
// $array=[1,2,3,4,5,6,6];
// print_r(array_filter($array,"square"));
// echo"<br>";
// print_r(array_unique($array));
// $result=range(0,100,10);
// // $result[1]=0;
// echo "<pre>";
// print_r($result);
// echo "</pre>";
// $result=range(0,100);

// echo "<pre>";
// print_r($result);
// echo "</pre>";

// $result_sum=array_sum($result);
// echo $result_sum."<br>";
// $arr=["a"=>1,"b"=>4,"c"=>7];
// print_r(array_keys($arr));
// echo"<br>";
// print_r(array_values($arr));
// echo"<br>";
// print_r(array_reverse($arr));

// echo"<br>";
// $result=range(0,100,20);;
// print_r($result);
// echo"<br>";

// print_r(array_rand($result,3));
// echo"<br>";
// $arr=["a"=>[1,2,3,4],"b"=>["f"=>4,5,6,7],"c"=>["f"=>9,0,8,7]];

// echo "<pre>";
// print_r($arr);
// echo "</pre>";

// echo "<pre>";
// print_r(array_column($arr,"f"));
// echo "</pre>";

// echo "<pre>";
// print_r(array_column($arr,1,2));
// echo "</pre>";

// $arr=[1,2,3,4];
// list($a,$b,$c,$d)=$array;
// echo $d;

// echo "<br>";

// $arr=array_fill(0,5,range(3,5));
// echo "<pre>";
// print_r($arr);
// echo "</pre>";

// $arr=[1,2,3,4,5,6];
// $arr2=[5,6,8,9,10,11];
// echo "<pre>";
// print_r(array_slice($arr,1,3));
// echo "</pre>";
// echo "<pre>";
// print_r(array_slice($arr,1,3,$arr2));
// echo "</pre>";
// echo count($GLOBALS);
// print_r($GLOBALS);


// $arr=Array("a"=>1,"b"=>2,"c"=>3,"d"=>4,"e"=>5,"f"=>6);
// echo "main";
// echo "<br>";
// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// echo "sort";
// echo "<br>";
// sort($arr);

// echo "<pre>";
// print_r($arr);
// echo "</pre>";

// echo "rsort";
// echo "<br>";
// rsort($arr);

// echo "<pre>";
// print_r($arr);
// echo "</pre>";

// echo "asort";
// echo "<br>";
// asort($arr);

// echo "<pre>";
// print_r($arr);
// echo "</pre>";

// echo "ksort";
// echo "<br>";
// ksort($arr);

// echo "<pre>";
// print_r($arr);
// echo "</pre>";

// echo  $_SERVER['HTTP_USER_AGENT']. "<br>";
echo  $_SERVER["SERVER_NAME"]  . "<br>";
echo  $_SERVER["SERVER_SOFTWARE"]  . "<br>";
echo  $_SERVER["REMOTE_ADDR"]  . "<br>";
echo  $_SERVER["HTTP_REFERER"]  . "<br>";
echo  $_SERVER["DOCUMENT_ROOT"] .$_SERVER["SCRIPT_NAME"]  . "<br>";
