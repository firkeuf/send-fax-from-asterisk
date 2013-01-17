<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FAX for Asterisk</title>

<link href="css/css.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="container">
  <div class="content">
    <h1>FAX for Asterisk</h1>
    <p> 
<?php 


// SET CONFIGURATIONS HERE ------------------------------ 
//$asterisk_spool_folder = '/var/spool/asterisk/tmp/';//"/var/spool/asterisk/tmp/"; 
//$uploadpath='/var/spool/asterisk/tmp/';
$tmp_dir = '/var/spool/asterisk/tmp/';


// END CONFIGURATIONS 


// HELPERS -------------------
   function unique_name($path, $suffix) 
   { 
      $file = $path."/".mt_rand().$suffix; 
      return $file; 
   } 
   // error list 
   $ERROR_CONVERTING_DOCUMENT = 1; 
   $ERROR_CREATING_CALL_FILE = 2; 
   $ERROR_UPLOADING_FILE = 3;
   $ERROR_NO_ERROR = 0;
// END HELPERS --------------



// generate a new name for the PDF. 
$input_file_noext = unique_name($tmp_dir, ""); 
$input_file = $input_file_noext;
$rand_digit_file = rand(100, 199);

do {
    $output_file = $tmp_dir.$rand_digit_file.'.tif';
}
while (file_exists($output_file));

?>
<!--  initiating document conversion. HTML comment hack to supress all error messages from appearing on the screen 


<?
$error = $ERROR_NO_ERROR;  // no error at beginning

$script_local_path = $_REAL_BASE_DIR = realpath(dirname(__FILE__));

$input_file_orig_name = basename($_FILES['faxFile']['name']); 
$ext = substr($input_file_orig_name, strrpos($input_file_orig_name, '.') + 1);

  
// Chech the file type
if ($ext == "pdf")  {
	if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file)) {
		$input_file_type = "pdf";
                
	}else{
		$error = $ERROR_UPLOADING_FILE;
	}
}

if ($ext == "jpg")  {
	if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file)) {
		$input_file_type = "jpg";
                
	}else{
		$error = $ERROR_UPLOADING_FILE;
	}
}

if ($ext == "tif")  {
	if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file)) {
		$input_file_type = "tif";
	}else{
		$error = $ERROR_UPLOADING_FILE;
	}
}

if ($ext == "tiff")  {
	if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file)) {
		$input_file_type = "tiff";
	}else{
		$error = $ERROR_UPLOADING_FILE;
	}
}
// we should now have a PDF file which we will convert to tif 

if($error == $ERROR_NO_ERROR && $input_file_type == "pdf") {

	// convert the attached PDF to .tif using ghostsccript ... 
	$gs_command = "/usr/local/bin/convert -define quantum:polarity=min-is-white -rotate '90>' -density 204x196 -resize 1728x -compress Group4 -type bilevel -monochrome ${input_file} ${output_file}" ;
	$gs_command_output = system($gs_command, $retval);
        unlink($input_file);
        chmod($output_file, 0660);
		
	if ($retval != 0) {
		$message = "There was an error converting your PDF file to TIF. Try uploading the file again or with an older version of PDF"; 
		$error = $ERROR_CONVERTING_DOCUMENT; 
		// die();
	}
	else  {
            $message = "Чтобы отправить факс: наберите номер, дождитесь ответа факса, переключите звонок на номер <br>".
                    "<span class='red-big-text'> 88 ${rand_digit_file}";
        } 
}

if($error == $ERROR_NO_ERROR && $input_file_type == "jpg" || $input_file_type == "tiff" || $input_file_type == "tif") {
       	// convert the attached JPEG to .tif using ImageMagik ... 
	$gs_command = "/usr/local/bin/convert -define quantum:polarity=min-is-white -rotate '90>' -density 204x196 -resize 1728x -compress Group4 -type bilevel -monochrome ${input_file} ${output_file}";
	system($gs_command, $retval);
        unlink($input_file);
        chmod($output_file, 0660);
        
       
		
	if ($retval != 0) {
		$message = "There was an error converting your JPEG file to TIF. Try uploading the file again"; 
		$error = $ERROR_CONVERTING_DOCUMENT; 
		// die();
	}
	else  {
            $message = "Чтобы отправить факс: наберите номер, дождитесь ответа факса, переключите звонок на номер <br>".
                    "<span class='red-big-text'> 88 ${rand_digit_file}";
        } 
}



?>
END HTML HACK to supress errors appearing on screen. 
-->


<? 



if ($error == $ERROR_NO_ERROR) {
	echo $message."<br>";
        echo "<span class='back-link'><a href='/sendfax/index.html'> <-- Назад.</a></span>";
        
        	
} if ($error == $ERROR_CONVERTING_DOCUMENT) {
	echo "<span class='error'><a href='/sendfax/index.html'>Возникли проблемы с конвертацией Вашего документа </a>". 
	     " попробуйте загрузить в другом формате. </span> <br /><br />". 
		  $doc_convert_output;  	
} if ($error == $ERROR_UPLOADING_FILE) {
        echo "<span class='error'><a href='/sendfax/index.html'>Ваш документ не был загружен попробуйте еще раз.</a> </span> <br /><br />". 
		  $doc_convert_output ;  
    
}

?>


</p>
  <!-- end .content --></div>
  <!-- end .container --></div>
       <p class="footer-copyrights"> Uvita (<a href="http://uvita.com.ua">http://uvita.com.ua</a>). </p>

</body>
</html>
