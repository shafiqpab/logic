<?


/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Yarn count.
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	5-01-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Count Information", "../../", 1, 1,$unicode,'','');

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

		var permission='<? echo $permission; ?>';	
		 
function fnc_pad_info( operation )
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	else
	{
		
	 
		var fd = new FormData(); 
		var header_img = $('#upload_header_file')[0].files[0]; 
		var body_img = $('#upload_body_file')[0].files[0]; 
		var footer_img = $('#upload_footer_file')[0].files[0]; 
		var cbo_company_name = $('#cbo_company_name').val(); 
		var txt_header_size = $('#txt_header_size').val(); 
		var txt_footer_size = $('#txt_footer_size').val(); 
		fd.append('header_img', header_img); 
		fd.append('body_img', body_img); 
		fd.append('footer_img', footer_img); 
		fd.append('cbo_company_name', cbo_company_name); 
		fd.append('txt_header_size', txt_header_size); 
		fd.append('txt_footer_size', txt_footer_size);  

		$.ajax({ 
			url: 'requires/generate_pad_controller.php?action=save_update_delete', 
			type: 'post', 
			fd: $(this).serialize(),
			data: fd, 
			contentType: false, 
			processData: false,
			success: function(http){ 
				
				if(http.readyState == 4) 
	      {
			release_freezing(); return;
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==15) 
		  { 
			 setTimeout('fnc_pad_info( 0 )',8000); 
		  }
		else
		{
			if (reponse[0].length>2) reponse[0]=10;
			
			show_msg(reponse[0]);
			show_list_view(reponse[1],'search_list_view','search_list_view','../general_info/requires/generate_pad_controller','setFilterGrid("list_view",-1)');
			//reset_form('yarncountinfo_1','','');
			set_button_status(0, permission, 'fnc_pad_info',1);
			release_freezing();
		}
	  }
			}, 
		}); 
 	
		
		
		
		
		
		
		
		
	}
}



function header_upload(header_img_name){
const [file] = upload_header_file.files
  if (file) {
    txt_header_image_show.src = URL.createObjectURL(file)
  }
}


function body_upload(img_name){
const [file] = upload_body_file.files
  if (file) {
    txt_body_image_show.src = URL.createObjectURL(file)
	$(".body1").css({"background-image":"url("+txt_body_image_show.src+")"});
  }
}



function footer_upload(footer_img_name){
const [file] = upload_footer_file.files
  if (file) {
    txt_footer_image_show.src = URL.createObjectURL(file)
  }
}

    function setHight(newHeight,classTag){
        $("."+classTag).height(newHeight);
    }


$(document).ready(function(){
    $(".set-height-btn1").click(function(){
        var newHeight1 = $(".input-height1").val();
        $(".box1").height(newHeight1);
    });
});



	</script>

</head>

<body onLoad="set_hotkey()">
	<div style="width:1000px;">
		<? echo load_freeze_divs ("../../", $permission);  ?>
		<table width="600" cellpadding="0" cellspacing="2" align="left">
			<tr>
				<td width="80%" align="center" valign="top"> 
				<fieldset width="580">
					<legend>Generate Padd Info</legend>
					<form name="yarncountinfo_1" id="yarncountinfo_1">	
						<table cellpadding="0" cellspacing="2" width="100%">
							<tr>
								<td width="90" class="must_entry_caption" style="padding: 3px 0px;">Company Name</td>
								<td colspan="3">
									<?
									echo create_drop_down( "cbo_company_name", 450, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "" );
									?>
								<input type="hidden" name="update_id" id="update_id" >
								</td>
							</tr>
							<tr>
								<td  align="left">Header </td>
								<td > <input type="file"  id="upload_header_file" name="upload_header_file" class="image_uploader" required style="width:150px;margin-left: -27px;" onChange="header_upload(this.value)"/> </td>

								<td  style="padding: 3px 0px;">Header Size</td>
								<td>
								<input type="text" class="input-height text_boxes" class="text_boxes"name="txt_header_size" id="txt_header_size" required onKeyUp="setHight(this.value,'box')" style="border-radius:25px;width:150px; margin-left: -17px;" >
								</td>
							</tr>
							<tr>
								<td  valign="top">Body </td>
								<td> <input type="file" id="upload_body_file" name="upload_body_file" class="image_uploader" required style="width:150px;margin-left: -27px" onChange="body_upload(this.value)"/> </td>
							</tr>
							<tr>
								<td  align="left">Footer</td>
								<td> <input type="file" id="upload_footer_file" name="upload_footer_file" class="image_uploader" required style="width:150px;margin-left: -27px" onChange="footer_upload(this.value)"/> </td>

								<td style="padding: 3px 0px;" align="left">Footer Size</td>
								<td >
									<input type="text" class="input-height1 text_boxes"  required style="border-radius:25px;width:150px;margin-left: -17px;"name="txt_footer_size" id="txt_footer_size" onKeyUp="setHight(this.value,'box1')" id="txt_footer_size" >
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" class="button_container">
									<? 
										echo load_submit_buttons( $permission, "fnc_pad_info",0,1 ,"reset_form('yarncountinfo_1','','',1)");
									?>
								</td>				
							</tr>
							<tr>
								<td height="16" colspan="4"></td>
							</tr>
						</table>
					</form>	
				</fieldset>
				<fieldset style="width:585px; margin-top:10px; position:absolute;">
					<legend>List View</legend>
					<div style="width:585px; margin-top:10px" id="search_list_view" align="left">
						<?
							$companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
							$arr=array (0=>$companyarr);
							echo  create_list_view ( "list_view", "Company Name,Header Size,Footer Size,Header Image,Body Image,Footer Image", "100,80,80,80,100,","570","220",0, "select id,company_id,header_size,footer_size,header_location,body_location,footer_location from template_pad  where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,", $arr , "company_id,header_size,footer_size,header_location,body_location,footer_location", "../general_info/requires/generate_pad_controller", 'setFilterGrid("list_view",-1);' );
						
						?>
						
					</div>
				</fieldset>
		</table>
		
		<div style="width:350px;overflow: hidden; padding-top:0px; margin-top:0px; position:relative;" align="right" >
			<table style="border: 1px solid black;width:350px;height:450px">
				<style>
				.body1
				{
				
					background-repeat:no-repeat;
					width:100%;
					height:200px;
					/* display: block;

					/* margin-left: auto;
				margin-right: auto;
					background-size: cover; */
					
					
				}
				</style> 
				<tr>
					<td>
						<img src="" class="box" alt="header Image"  id="txt_header_image_show" style="margin-top: -108px;"> 
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" src="" alt="body Image" class="body1"  id="txt_body_image_show" align="center">
					</td >
				</tr>
				<tr>
					<td>
						<img src=""class="box1" alt="footer Image" id="txt_footer_image_show" style="margin-bottom: -217px; width:350px;height:250px"> 
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
 


$( "thead tr th" ).dblclick(function() {
  $( "#tbl_scroll_body" ).animate({scrollTop:0}, 'slow');
});
 
</script>

</html>
