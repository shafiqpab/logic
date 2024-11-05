<?

session_start();
include('common.php');

extract($_REQUEST);


if( $action=="return_conversion_date" )
{
	echo set_conversion_rate( $cid, $cdate );
	die;
}


if($action=="search_by_drop_down")
{
	$nameArray=sql_select( $data );
	$drop_down.='<select	name="txt_search_common"	style="width:140px " class="combo_boxes" id="txt_search_common"	onchange="" >';
	foreach ($nameArray as $result)
	 {
		  $drop_down .='<option value="'.$result[0].'" ';
					if ($selected==$result[0]) { $drop_down .='selected'; } $drop_down .='>'.$result[1].'</option>\n';
	 }
	 echo $drop_down; 
	 die;
}

if($action=="search_by_drop_down_from_array")
{	
	$nameArray=$$data; 
	$drop_down.='<select	name="txt_search_common"	style="width:140px " class="combo_boxes" id="txt_search_common"	onchange="" >';
	foreach ($nameArray as $key=>$val)
	 {
		  $drop_down .='<option value="'.$key.'" ';
					if ($selected==$key) { $drop_down .='selected'; } $drop_down .='>'.$val.'</option>\n';
	 }
	 echo $drop_down; 
	 die;
}

if($action=="change_date_format")
{
	
	echo change_date_format($data, $new_format, $new_sep);
	die;
}
if($action=="add_new_drop_down")
{
	echo $data;
	die;
}

if($action=="add_ip_session")
{
	if (trim($ip_address)=="")
	{
		$ip_address= $_SERVER['REMOTE_ADDR'];
	}
	$_SESSION['logic_erp']["pc_local_ip"]="";
	$_SESSION['logic_erp']["pc_local_ip"]=$ip_address;
	die;
}


/*if($action=="save_post_session")
{
	$data=explode( "&",$data);
	foreach($data as $datas)
	{
		$fdata=explode( "=",$datas);
		$_SESSION['logic_erp'][$fdata[0]]="";
		$_SESSION['logic_erp'][$fdata[0]]=$fdata[1];
	}
	die;
}*/


if( $action=="file_uploader" )
{
	echo load_html_head_contents("File Uploader","../", '', '', '');
	extract($_REQUEST);

?>
    <script type="text/javascript" src="../js/ajaxupload.js" ></script>  
    
<script type="text/javascript" >
var del_file;
function check_ext(ext,type)
{
	if (type==1) // Image
	{
	 	if (! (ext && /^(jpg|png|jpeg|bmp)$/.test(ext)))  return false;
		else return true;
	}
	if (type==2) // pdf,txt,docs,xls
	{
	 	if (! (ext && /^(pdf|txt|doc|docx|xls|xlsx|msg)$/.test(ext)))  return false;
		else return true;
	}
}

	$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		 var master_tble_id='<? echo $mst_id; ?>';
		 var dets_tble_id='<? echo $det_id; ?>';
		 var form='<? echo $form; ?>';
		 var file_type='<? echo $file_type; ?>';
		 var is_multi='<? echo $is_multi; ?>';
 
		new AjaxUpload(btnUpload, {
			//alert(master_tble_id);
			
			action: 'common_functions_for_js.php?action=php_upload_file&master_tble_id='+master_tble_id+'&form='+form+'&dets_tble_id='+dets_tble_id+'&img_size=512&is_multi='+is_multi+'&file_type='+file_type,
			name: 'uploadfile',
			onSubmit: function(file, ext)
			{
				 
				if (check_ext(ext,file_type)==false)
				{
					if (file_type==1)
					{ 
						status.text('Only JPG, PNG or bmp files are allowed');
						return false;
					}
					if (file_type==2)
					{ 
						status.text('Only Text, Pdf or Docs files are allowed');
						return false;
					}
				}
				status.html('<img src="../images/loading.gif" />');
				 
			},
			onComplete: function(file, response){
				//On completion clear the status
				// alert(file_type);
				response=response.split("**");
				
				status.text('');
				 
				 
				var del_file="<a href='##' onclick=\"remove_this_image('"+trim(response[1])+"','"+trim(response[0])+ "','"+trim(response[2])+ "','"+trim(response[3])+"','"+trim(response[4])+"','"+trim(response[5])+"')\">"+response[6]+"-"+response[1]+"</a>";
				//Add uploaded file to list
				if(file_type==1)
				{
					$('<li></li>').appendTo('#files').html('<a href="##" onckick="openmypage("'+trim(response[0])+'","Image Info"); return false"> <img src="../'+response[0]+'" height="97px" width="89px" /></a><br />'+del_file).addClass('success');
				}
				else
				{
					$('<li></li>').appendTo('#files').html('<a href="common_functions_for_js.php?filename=../'+trim(response[0]) +'&action=download_file"> <img src="../file_upload/blank_file.png" height="97px" width="89px" /></a><br />'+del_file).addClass('success');
				}
			}
		});
	});
 
function remove_this_image(id,location,master_tble_id,dets_tble_id,form,file_type)
{
	 var conf=confirm("Do you really Want to Delete the Image");
	 
	 if (conf==1)
	 {
		 document.getElementById('files').innerHTML= $.ajax({
			  url: 'common_functions_for_js.php?action=delete_uploaded_file&img_id='+id+'&location='+location+'&master_tble_id='+master_tble_id+'&dets_tble_id='+dets_tble_id+'&form='+form+'&file_type='+file_type ,
			  async: false
			}).responseText
		/*ajax.requestFile = 'common_functions_for_js.php?action=delete_uploaded_file&img_id='+id+'&location='+location ;	 
		ajax.onCompletion = status_msg;
		ajax.runAJAX();*/
	}
}
 
</script>
    <style>
#upload{
		 padding:5px;
		font-weight:bold; font-size:1.3em;
		font-family:Arial, Helvetica, sans-serif;
		text-align:center;
		background:#999999;
		color:#ffffff;
		border:1px solid #999999;
		width:190px;
		cursor:pointer !important;
		-moz-border-radius:5px; -webkit-border-radius:5px;
	}
	 
	.darkbg{
		background:#ddd !important;
	}
	#status{
		font-family:Arial; padding:5px;
	}
	#files{ list-style:none;  }
	#files li{margin-top:7px; margin-left:7px; float:left  }

	.success{ border:1px solid #CCCCCC;color:#660000; float:left }
	 
	.error{ background:#FFFFFF; border:1px solid #CCCCCC; }
</style>
</head>
<body>
    <div style="width:620px">
   
    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        	<tr>
            	<td width="100%" height="250" align="center"> <div id="files"  style="width:100%; border:1px solid;  height:100%; background-color:" align="center"> 
         
                <?
					if ($det_id=="") $det=""; else $det=" and details_tble_id='$det_id'";
				 
					$nameArray=sql_select( "select id,image_location,master_tble_id,details_tble_id,form_name,file_type,real_file_name from common_photo_library where master_tble_id='$mst_id' and form_name='$form' and file_type=$file_type $det" );
					if (count($nameArray)>0) 
					{
						foreach ($nameArray as $inf)
						{
							$ext =strtolower( get_file_ext($inf[csf("image_location")]));
							if($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="bmp")
							{
								$lin=$inf[csf("image_location")];
					?>			<li><a href="##" > <img  src="<? echo '../'. $inf[csf("image_location")]; ?>" height="97" width="89" /></a><br />
								<? if($show_button==1) {
									?><a href='##'  onClick="remove_this_image('<? echo $inf[csf("id")]; ?>','<? echo $lin; ?>','<? echo $inf[csf("master_tble_id")]; ?>','<? echo $inf[csf("details_tble_id")]; ?>','<? echo $inf[csf("form_name")]; ?>','<? echo $inf[csf("file_type")]; ?>')"><? echo $inf[csf("real_file_name")]; ?></a>  
									<? } ?> 	 	
								</li>
                       
                <? 
							}
							else
							{
								$lin=$inf[csf("image_location")];
								
					?>			<li><a href="common_functions_for_js.php?filename=<? echo "../".$lin ?>&action=download_file" style="text-transform:none"> <img  src="<? echo '../file_upload/blank_file.png'; ?>" height="97" width="89" /></a><br />
								<? if($show_button==1) {
									?><a href='##' onClick="remove_this_image('<? echo $inf[csf("id")]; ?>','<? echo $lin; ?>','<? echo $inf[csf("master_tble_id")]; ?>','<? echo $inf[csf("details_tble_id")]; ?>','<? echo $inf[csf("form_name")]; ?>','<? echo $inf[csf("file_type")]; ?>')"><? echo $inf[csf("real_file_name")]; ?></a>  
									<? } ?> 	 	
								</li>
					   
				<? 
							}
						}
					}
					else
					{ ?>
                    	<div id="files" style="float:left" align="center"></div>
                <?
					}
					?>
                        </div> 
                </td>
            </tr>
            
            <tr>
            	<td width="100%" align="center">
                <div id="status" align="center">
                	</div>
						<? if($show_button==1) {
							
								?><div style="padding-top:5px"> <div id="upload" ><span>Select <? if($file_type==1){echo "Image";}if($file_type==2){echo "File";}?></span> </div><? } ?> 	 
                    </div>
					<div style="width:100px; padding-top:5px" align="center">
					</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?

}


if ($action=="php_upload_file")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$image =$_FILES["uploadfile"]["name"];
	$uploadedfile = $_FILES['uploadfile']['tmp_name'];
	$extension =strtolower( get_file_ext($_FILES["uploadfile"]["name"]));
	
 	if($extension=="jpg" || $extension=="jpeg" || $extension=="png" || $extension=="bmp")
	{
		if ( $image ) 
		{
			
			if($extension=="jpg" || $extension=="jpeg" )
			{
				$uploadedfile = $_FILES['uploadfile']['tmp_name'];
				$src = imagecreatefromjpeg($uploadedfile);
			}
			else if($extension=="png")
			{
				$uploadedfile = $_FILES['uploadfile']['tmp_name'];
				$src = imagecreatefrompng($uploadedfile);
			}
			else 
			{
				$src = imagecreatefromgif($uploadedfile);
			}
			list($width,$height)=getimagesize($uploadedfile);
			$newwidth=300;
			if ($width<$newwidth)
			{
				$newwidth=$width;
			}
			$newheight=($height/$width)*$newwidth;
			$tmp=imagecreatetruecolor($newwidth,$newheight);
			
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height); 
			
			$uploaddir = '../file_upload/';
			$tmp_name = $uploaddir . basename($_FILES['uploadfile']['name']);
			 
			 if ($is_multi!=1)
			 {
				 if ($details_tble_id=="") $details=""; else $details="and details_tble_id='$details_tble_id'";
				 $dd=execute_query( "delete from common_photo_library where master_tble_id='$master_tble_id' and form_name='$form' $details", $commit );
				 $id=return_next_id( "id", "common_photo_library", 1 ) ;
				 if ($dets_tble_id!="") $fname=$form."_".$master_tble_id."_".$dets_tble_id.".".$extension;
				 else $fname=$form."_".$master_tble_id.".".$extension;
			 }
			 else
			 {
				 $id=return_next_id( "id", "common_photo_library", 1 ) ;
				 if ($dets_tble_id!="") $fname=$form."_".$master_tble_id."_".$dets_tble_id."_".$id.".".$extension;
				 else $fname=$form."_".$master_tble_id."_".$id.".".$extension;
			 }
			
			
			$file="$uploaddir$fname";
			
			$file_save="file_upload/"."$fname";
			 imagejpeg($tmp,$file,100);
			 imagedestroy($src);
			 imagedestroy($tmp);
			/*if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) 
			{*/
					
				$field_array="id,master_tble_id,details_tble_id,form_name,image_location,file_type,real_file_name";
				$data_array="('".$id."','".$master_tble_id."','".$dets_tble_id."','".$form."','".$file_save."','".$file_type."','".basename($_FILES['uploadfile']['name'])."')";
				$rID=sql_insert("common_photo_library",$field_array,$data_array,1); 
				if($db_type==0)
				{
					if($rID ){
						mysql_query("COMMIT");  
						echo $file_save."**".$id."**".$master_tble_id."**".$dets_tble_id."**".$form."**".$file_type."**".basename($_FILES['uploadfile']['name']);
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$id;
					}
				}
					 
				disconnect($con);
				
				if($db_type==2 || $db_type==1 )
				{
				echo $file_save."**".$id."**".$master_tble_id."**".$dets_tble_id."**".$form."**".$file_type."**".basename($_FILES['uploadfile']['name']);
				}
			/*}
			else
				echo  "not".$extension; die;
			*/
		}
	}
	else  // Not Image File
	{
		$uploaddir = '../file_upload/';
		$tmp_name = $uploaddir . basename($_FILES['uploadfile']['name']);
		
		 if ($is_multi!=1)
		 {
			 if ($details_tble_id=="") $details=""; else $details="and details_tble_id='$details_tble_id'";
			 $dd=execute_query( "delete from common_photo_library where master_tble_id='$master_tble_id' and form_name='$form' $details", $commit );
			 $id=return_next_id( "id", "common_photo_library", 1 ) ;
			 if ($dets_tble_id!="") $fname=$form."_".$master_tble_id."_".$dets_tble_id.".".$extension;
			 else $fname=$form."_".$master_tble_id.".".$extension;
		 }
		 else
		 {
			 $id=return_next_id( "id", "common_photo_library", 1 ) ;
			 if ($dets_tble_id!="") $fname=$form."_".$master_tble_id."_".$dets_tble_id."_".$id.".".$extension;
			 else $fname=$form."_".$master_tble_id."_".$id.".".$extension;
		 }
		
		
		$file="$uploaddir$fname";
		
		$file_save="file_upload/"."$fname";
		 
		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) 
		{
			$field_array="id,master_tble_id,details_tble_id,form_name,image_location,file_type,real_file_name";
			$data_array="('".$id."','".$master_tble_id."','".$dets_tble_id."','".$form."','".$file_save."','".$file_type."','".basename($_FILES['uploadfile']['name'])."')";
			$rID=sql_insert("common_photo_library",$field_array,$data_array,1); 
			if($db_type==0)
			{
				
				if($rID ){
					mysql_query("COMMIT");  
					echo $file_save."**".$id."**".$master_tble_id."**".$dets_tble_id."**".$form."**".$file_type."**".basename($_FILES['uploadfile']['name']);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
				 
			disconnect($con);
			
			if($db_type==2 || $db_type==1 )
			{
			echo $file_save."**".$id."**".$master_tble_id."**".$dets_tble_id."**".$form."**".$file_type."**".basename($_FILES['uploadfile']['name']);
			}
		}
		 
		
	}
	
}

if ($action=="delete_uploaded_file")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	//echo "delete from common_photo_library where id='$img_id'"; die;
	$rID=execute_query( "delete from common_photo_library where id='$img_id'", $commit );
	unlink('../'.$location);
	if($db_type==0)
	{
		if($rID ){
			mysql_query("COMMIT");  
			//echo $file_save."**".$id;
		}
		else{
			mysql_query("ROLLBACK"); 
			//echo "10**".$id;
		}
	}
		if ($dets_tble_id=="") $det=""; else $det=" and details_tble_id='$dets_tble_id'";
		$new_img=""; 
		$nameArray=sql_select( "select id,image_location,master_tble_id,details_tble_id,form_name,file_type,real_file_name from common_photo_library where master_tble_id='$master_tble_id' and form_name='$form' and file_type='$file_type' $det" );
		if (count($nameArray)>0) 
		{
			foreach ($nameArray as $inf)
			{
				$ext =strtolower( get_file_ext($inf[csf("image_location")]));
				if($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="bmp")
				{
				
				$new_img .="<li><a href='##' > <img  src='../".$inf[csf("image_location")]."' height='97' width='89' /></a><br />
				<a href='##' onclick=\"remove_this_image('".$inf[csf("id")]."','".$inf[csf("image_location")]."','".$inf[csf("master_tble_id")]."','".$inf[csf("details_tble_id")]."','".$inf[csf("form_name")]."','".$inf[csf("file_type")]."')\">".$inf[csf("real_file_name")]."</a>
				</li>";
				}
				else
				{
				$new_img .="<li><a href='common_functions_for_js.php?filename=../$lin&action=download_file' style='text-transform:none' > <img  src='../file_upload/blank_file.png' height='97' width='89' /></a><br />
				<a href='##' onclick=\"remove_this_image('".$inf[csf("id")]."','".$inf[csf("image_location")]."','".$inf[csf("master_tble_id")]."','".$inf[csf("details_tble_id")]."','".$inf[csf("form_name")]."','".$inf[csf("file_type")]."')\">".$inf[csf("real_file_name")]."</a>
				</li>";	
				}
			}
		}
		else
		{  
			$new_img .='<div id="files" style="float:left" align="center"></div>';
	 
		}
		
	disconnect($con);
	echo $new_img;
	if($db_type==2 || $db_type==1 )
	{
		//echo $file_save."**".$id;
	}
}
	 
if($action=="confirm_msg_box")
{
	 echo load_html_head_contents("File Uploader","../", '', '', '');
	 $btn_msg[0][0]="ok";
	 $btn_msg[0][1]="Cancel";
	 
	 $btn_msg[1][0]="Yes";
	 $btn_msg[1][1]="No";
	 
	 ?>
     	<input type="hidden" id="txt_action">
        
        <div>
        	<table width="100%" cellspacing="0" border="0">
            	<tr>
                	<td width="100%" height="80" align="center"><strong><? echo $msg; ?></strong></td>
                </tr>
                <tr>
                	<td width="100%" height="80" align="center">
                    	<input style="width:100px;" class="frombutton" type="button" onClick="document.getElementById('txt_action').value=1; parent.emailwindow.hide();" value="<? echo $btn_msg[$btn_type][0]; ?>">
                    	&nbsp;&nbsp;&nbsp;<input style="width:100px;" class="frombutton" onClick="document.getElementById('txt_action').value=1; parent.emailwindow.hide();" type="button" value="<? echo $btn_msg[$btn_type][1]; ?>">
                    </td>
                </tr>
            </table>
        </div>
     <?
}

if ($action=="create_menu_session")
{
	$data=explode("_",$data);
	$_SESSION["module_id"]=$data[1];
	$_SESSION['menu_id']=$data[0];
}

if ($action=="generate_report_file")

 	if ($htm_doc!="")
	{
		if ($type=="") $type=1;
		
		$name=time();
		if ($type==1) $filename=$_SESSION['logic_erp']['user_name']."_".$name.".xls";
		else if ($type==2) $filename=$_SESSION['logic_erp']['user_name']."_".$name.".doc";
		else if ($type==3) $filename=$_SESSION['logic_erp']['user_name']."_".$name.".txt";
		else if ($type==4)
		{
			$filename=$_SESSION['logic_erp']['user_name']."_".$name.".pdf";
		}
		$filename=$_SESSION['logic_erp']['user_name']."_".$name.".xls";
		if (strlen($path)==3) $im_url=""; else if (strlen($path)==6) $im_url="../"; else if (strlen($path)==9) $im_url="../../"; else if (strlen($path)==12) $im_url="../../../"; 
		$filename= '../ext_resource/tmp_report/'.$filename;
		 
		$create_new_doc = fopen($filename, 'w');	
		if(fwrite($create_new_doc,$htm_doc))
			echo str_replace("../","",$filename);
		else
			echo 0;
		 
	}
	
if($action=="download_file")
{
	extract($_REQUEST);
	set_time_limit(0);
	$file_path=$_REQUEST['filename'];
	download_start($file_path, ''.$_REQUEST['filename'].'', 'text/plain');
}

function download_start($file, $name, $mime_type='')
{
	if(file_exists($file))
	{
		echo "file found";
	}
	else
	{
    	die('File not found');
	}

	//Check the file exist or not
	if(!is_readable($file)) die('File not found or inaccessible!');
	$size = filesize($file);
	$name = rawurldecode($name);
	/* MIME type check*/
	$known_mime_types=array(
	  "pdf" => "application/pdf",
	  "txt" => "text/plain",
	  "html" => "text/html",
	  "htm" => "text/html",
	  "exe" => "application/octet-stream",
	  "zip" => "application/zip",
	  "doc" => "application/msword",
	  "xls" => "application/vnd.ms-excel",
	  "ppt" => "application/vnd.ms-powerpoint",
	  "gif" => "image/gif",
	  "png" => "image/png",
	  "jpeg"=> "image/jpg",
	  "jpg" =>  "image/jpg",
	  "php" => "text/plain"
	);
	
	if($mime_type=='')
	{
		$file_extension = strtolower(substr(strrchr($file,"."),1));
		if(array_key_exists($file_extension, $known_mime_types))
		{
		$mime_type=$known_mime_types[$file_extension];
	    } 
		else 
		{
			$mime_type="application/force-download";
		}
    }
	//turn off output buffering to decrease cpu usage
	@ob_end_clean(); 
	// required for IE Only
	if(ini_get('zlib.output_compression'))
	ini_set('zlib.output_compression', 'Off'); 
	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header("Content-Transfer-Encoding: binary");
	header('Accept-Ranges: bytes'); 
	/*non-cacheable */
	header("Cache-control: private");
	header('Pragma: private');
	header("Expires: Tue, 15 May 1984 12:00:00 GMT");
	
	// multipart-download and download resuming support
	if(isset($_SERVER['HTTP_RANGE']))
	{
		list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
		list($range) = explode(",",$range,2);
		list($range, $range_end) = explode("-", $range);
		$range=intval($range);
		if(!$range_end) {
		 $range_end=$size-1;
		} else {
		 $range_end=intval($range_end);
		}
		$new_length = $range_end-$range+1;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range-$range_end/$size");
	} else {
		$new_length=$size;
		header("Content-Length: ".$size);
	}
	
	/* Will output the file itself */
	$chunksize = 1*(1024*1024); //you may want to change this
	$bytes_send = 0;
	if ($file = fopen($file, 'r')){
	if(isset($_SERVER['HTTP_RANGE']))
	fseek($file, $range);
	
	while(!feof($file) && (!connection_aborted()) && ($bytes_send<$new_length))
	{
		$buffer = fread($file, $chunksize);
		print($buffer); //echo($buffer); // can also possible
		flush();
		$bytes_send += strlen($buffer);
	}
	fclose($file);
	} else
	//If no permissiion
	die('Error - can not open file.');
	//die
	die();
}
?>

