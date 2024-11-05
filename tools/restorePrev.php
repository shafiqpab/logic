<!DOCTYPE html>

<html>
<head>
<title>JavaScript file upload</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="../js/ajaxupload.js" ></script>
<link href="../css/style_common.css" rel="stylesheet" type="text/css" />  
<script type="text/javascript" src="../js/jquery.js"></script>
<style>
#upload{
		 padding:5px;
		font-weight:bold; font-size:1.3em;
		font-family:Arial, Helvetica, sans-serif;
		text-align:center;
		background:#999999;
		color:#ffffff;
		border:1px solid #999999;
		width:350px;
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
	#files li{margin-top:15px;  }

	.success{ border:1px solid #CCCCCC;color:#660000; float:left }
	 
	.error{ background:#FFFFFF; border:1px solid #CCCCCC; }
</style>

<script type="text/javascript">

var del_file;
var master_tble_id;
	$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		new AjaxUpload(btnUpload, {
			 
			action: 'restore_db.php',
			name: 'uploadfile',
			 
			onSubmit: function(file, ext)
			{
			 
				 if (! (ext && /^(sql)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only SQL files are allowed');
					return false;
				}
				status.html('<img src="../images/loading.gif" />');
				 
			},
			onComplete: function(file, response){
				status.html('');
				status.text(response);
				 
			}
		});
	});
 


</script>
</head>
<body>
<center>
<div style="width:100%; text-align:center">
<fieldset style="width:400px">
<table>
    <tr>
        <td title="Click to Enlarge" align="center" height="175" width="400" valign="middle">
        	<div id="files" style="width:100%; border:1px solid; height:100%; background-color:" align="center">
        	<div id="files" style="float:left" align="center"></div>
            <br><br>
                Please Seelct a <strong>.sql</strong>  Extension Database Backup File and wait Till Upload and Restore Confirmation .<br><br>
            <!--  Select Database&nbsp;&nbsp;<select name="restore_db_name" id="restore_db_name" class="combo_boxes" style="width:200px">
                <?
				/*include('../includes/common.php');
				$result = mysql_list_dbs( $link );
				
				 
				  while( $row = mysql_fetch_object( $result ) ):
				 		echo '<option value="'.$row->Database.'">'.$row->Database.'</option>';
				  endwhile;*/
				
				?>
                
                </select> -->
        	</div>
        </td>
     </tr>
     <tr>
        <td align="center">
            <div id="status" align="center"></div>
            <div style="padding-top:5px"> <div id="upload" ><span>Select Database</span> </div></div>
          	<div style="width:100px; padding-top:5px" align="center">
                
          	</div>
        
        <!--<input type="file" style="visibility:hidden;" id="file_upload" name="file_upload" onchange="return upload();" /><br />
        <input type="button" class="formbutton" id="clickme" value="Upload Stuff!" /> -->
        
        
        </td>
    </tr>
</table>
</fieldset>

</div>
</center>
</body>
</html>