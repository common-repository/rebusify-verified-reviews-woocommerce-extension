<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//https://stackoverflow.com/questions/35311051/php-upload-file-from-one-server-to-another-server
//https://stackoverflow.com/questions/13785433/php-upload-file-to-another-server-without-curl

error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors', 'On');

function do_post_request($url, $postdata, $files = null) 
{ 
    $data = ""; 
    $boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10); 

    //Collect Postdata 
    foreach($postdata as $key => $val) 
    { 
        $data .= "--$boundary\n"; 
        $data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n"; 
    } 

    $data .= "--$boundary\n"; 

    //Collect Filedata 
    foreach($files as $key => $file) 
    { 
        $fileContents = file_get_contents($file['tmp_name']); 

        $data .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$file['name']}\"\n"; 
        $data .= "Content-Type: image/jpeg\n"; 
        $data .= "Content-Transfer-Encoding: binary\n\n"; 
        $data .= $fileContents."\n"; 
        $data .= "--$boundary--\n"; 
    } 

    $params = array('http' => array( 
           'method' => 'POST', 
           'header' => 'Content-Type: multipart/form-data; boundary='.$boundary, 
           'content' => $data 
        )); 

   $ctx = stream_context_create($params); 
   $fp = fopen($url, 'rb', false, $ctx); 

   if (!$fp) { 
      throw new Exception("Problem with $url, $php_errormsg"); 
   } 

   $response = @stream_get_contents($fp); 
   if ($response === false) { 
      throw new Exception("Problem reading data from $url, $php_errormsg"); 
   } 
   return $response; 
} 

//set data (in this example from post) 

if(isset($_REQUEST['btn_submit']))
{
//sample data 
$postdata = array( 
    'name' => $_POST['name'], 
    'age' => $_POST['age']
  
); 

//sample image 
$files['image'] = $_FILES['image']; 

$res=do_post_request("https://reviews.trustalyze.com/api/rbs/rv_upload_video.php", $postdata, $files); 
echo $res;
}

?>
<form method="POST" action="" enctype="multipart/form-data">
    <input type="file" name="image" />
    <input type="hidden" name="name" value="xyz" />
    <input type="hidden" name="age" value="45" />
           
    <input type="submit" value="Submit" name="btn_submit"/>
</form>