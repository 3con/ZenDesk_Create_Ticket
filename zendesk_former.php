<?php
define("ZDAPIKEY", "<your api key>");
define("ZDUSER", "<your user email>");
define("ZDURL", "https://<your zendesk domain>/api/v2");


function curlWrap($url, $json)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
	curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
	curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$output = curl_exec($ch);
	curl_close($ch);
	$decoded = json_decode($output);
	return $decoded;
}


foreach($_POST as $key => $value){
	if(preg_match('/^z_/i',$key)){
		$arr[strip_tags($key)] = strip_tags($value);
	}
}


//if (isset($_GET['z_file'])){
	//Get the uploaded file information
	$name_of_uploaded_file =
	basename($_FILES["z_file"]["name"]);
	
	// Replaces spaces in file name with _ 
	$name_of_uploaded_file= str_replace(' ','_',$name_of_uploaded_file);
	//print_r($name_of_uploaded_file);
 
	//get the file extension of the file
	$type_of_uploaded_file =
	 substr($name_of_uploaded_file,
	  strrpos($name_of_uploaded_file, '.') + 1);
	//print_r($type_of_uploaded_file);
 
	$size_of_uploaded_file =
	 $_FILES["z_file"]["size"]/1024;//size in KBs
	//print_r($size_of_uploaded_file);
	
	
        $temp_dir = "<path to local directory>/tmp/";
        
	$mf_dir = $temp_dir.$_FILES["z_file"]["name"];
	//print_r($mf_dir);
	
	
	$mf = move_uploaded_file($_FILES["z_file"]["tmp_name"],$temp_dir.$_FILES["z_file"]["name"]);
	$mf_dir = $temp_dir.$_FILES["z_file"]["name"];
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
	curl_setopt($ch, CURLOPT_URL, ZDURL."/uploads.json?filename=".$name_of_uploaded_file);
	curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/$type_of_uploaded_file'));
	curl_setopt($ch, CURLOPT_POST, true);
	$file = fopen($mf_dir,'r');
	//print_r($file);
	$size = filesize($mf_dir);
	$fildata = fread($file,$size);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fildata);
	curl_setopt($ch, CURLOPT_INFILE, $file);
	curl_setopt($ch, CURLOPT_INFILESIZE, $size);
	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$output1 = curl_exec($ch);
	curl_close($ch);
	$decoded1 = json_decode($output1, true);


	//print_r($decoded);
	$toke = $decoded1['upload']['token'];
	//print_r($toke);
	

//}





$create = json_encode(array(
			    'ticket' => array(
					'subject' => $arr["z_subject"],
					'comment' => array(
							"body" => $arr["z_description"],
							"uploads" => ["$toke"]),
					'requester' => array(
							"name" => $arr["z_name"],
							"email" => $arr["z_requester"])
					)
			    )
		      );

		      
// print_r($create);

$return = curlWrap("/tickets.json", $create);

//print_r($return);

header("Location: <page to redirect to done>");

exit;
?>

