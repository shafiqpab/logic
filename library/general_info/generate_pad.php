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
		if (form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{
			var fd = new FormData(); 
			fd.append('header_img', $('#upload_header_file')[0].files[0]); 
			fd.append('body_img', $('#upload_body_file')[0].files[0]); 
			fd.append('footer_img', $('#upload_footer_file')[0].files[0]); 
			fd.append('cbo_company_id', $('#cbo_company_id').val()); 
			fd.append('update_id', $('#update_id').val());
			fd.append('operation', operation);     

			$.ajax({ 
				url: 'requires/generate_pad_controller.php?action=save_update_delete', 
				type: 'post', 
				fd: $(this).serialize(),
				data: fd, 
				contentType: false, 
				processData: false,
				success: function(result)
				{ 
					var reponse=result.split('**');
					show_msg(reponse[0]);
					show_list_view(reponse[1],'search_list_view','search_list_view','../general_info/requires/generate_pad_controller','setFilterGrid("list_view",-1)');
					reset_form('gen_pad_1','','');
					set_button_status(0, permission, 'fnc_pad_info',1);
					release_freezing();
				}	
			}); 		
		}
	}


let file_upload=(e)=>{
	if(e.target.id=='upload_body_file'){
		$("."+e.target.id).css({'background-image':"url(" + URL.createObjectURL(e.target.files[0]) + ")",'background-repeat': 'no-repeat','height': '29px','width': '44px','background-position': 'center 40%'});
	}
	else{
		$("."+e.target.id).attr("src",URL.createObjectURL(e.target.files[0]));
	}
}

</script>

</head>
<body onLoad="set_hotkey()">
<div>
	<div style="overflow:hidden; float:left;width:410px;">	
		<? echo load_freeze_divs ("../../", $permission);  ?>
			<fieldset width="400">
				<legend>Generate Padd Info</legend>
				<form name="gen_pad_1" id="gen_pad_1">	
					<table cellpadding="0" cellspacing="2" align="center" >
						<tr>
							<td width="100" class="must_entry_caption">Company Name</td>
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "" );
								?>
							</td>
						</tr>
						<tr>
							<td>Header <span style="font-size: 10px; color:blue;">[Size:793x150]px</span></td>
							<td> 
								<input type="file"  id="upload_header_file" name="upload_header_file" style="width:150px;" onChange="file_upload(event)"/> 
							</td>
						</tr>
						<tr>
							<td>Body Water Mark</td>
							<td> <input type="file" id="upload_body_file" name="upload_body_file" style="width:150px;" onChange="file_upload(event)"/> </td>
						</tr>
						<tr>
							<td>Footer <span style="font-size: 10px; color:blue;">[Size:793x90]px</span></td>
							<td> 
								<input type="file" id="upload_footer_file" name="upload_footer_file" style="width:150px;" onChange="file_upload(event)"/> 
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center" class="button_container">
								<input type="hidden" name="update_id" id="update_id" >
								<? 
									echo load_submit_buttons( $permission, "fnc_pad_info",0,1 ,"reset_form('gen_pad_1','','',1)");
								?>
							</td>				
						</tr>
						<tr>
							<td height="16" colspan="4"></td>
						</tr>
					</table>
				</form>	
			</fieldset>
				
			<fieldset style="width:400px; margin-top:10px; position:absolute;">
				<legend>List View</legend>
				<div style="width:400px; margin-top:10px" id="search_list_view" align="left"></div>
		</fieldset>
		</div>
		
		<div style="width:40%; float:left; margin:30px 10px 0 10px;">
			<table>
				<tr>
					<td><img src="../../images/pad/header.png" alt="header Image"  class="upload_header_file"></td>
				</tr>
				<tr>
					<td class="upload_body_file" style="text-align: justify;">
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Eos accusantium iste eum laboriosam pariatur distinctio, illo culpa facere molestiae possimus rerum sed. Non rem, reprehenderit sunt est voluptas reiciendis error?
						Rem obcaecati reiciendis error illo nulla consequuntur, libero quos assumenda ab at beatae dolores et expedita velit laborum nostrum eos facere unde quod impedit eligendi atque quasi itaque quam. Autem.
						Odio eaque error et, ea vitae nemo dolorum ad optio explicabo reprehenderit ipsam! Sed, laudantium animi odit doloremque dolorum quibusdam dolor suscipit magnam nostrum asperiores mollitia perferendis facilis eos fugiat?
						Ducimus quis quos, iusto reprehenderit omnis et amet corporis tempora eos reiciendis dolor ipsum suscipit expedita repellendus vel laborum facere numquam obcaecati modi incidunt at ex! Similique quam sint officiis!
						Quisquam incidunt cupiditate possimus earum ullam eveniet enim nesciunt rerum nam? Ab tempore, deleniti numquam sit ipsa temporibus error earum? Tempora et incidunt cupiditate recusandae. Dolorum dolores deleniti hic nisi.
						Ipsam, dolores. Iste, quam, sed deserunt veniam, illum at nam non ea cumque optio eos vitae. Eligendi accusantium, molestias amet voluptatibus, ipsa odio eum maxime ea ipsum consequuntur iure doloribus<br><br>

						Lorem ipsum dolor sit amet consectetur adipisicing elit. Eos accusantium iste eum laboriosam pariatur distinctio, illo culpa facere molestiae possimus rerum sed. Non rem, reprehenderit sunt est voluptas reiciendis error?
						Rem obcaecati reiciendis error illo nulla consequuntur, libero quos assumenda ab at beatae dolores et expedita velit laborum nostrum eos facere unde quod impedit eligendi atque quasi itaque quam. Autem.
						Odio eaque error et, ea vitae nemo dolorum ad optio explicabo reprehenderit ipsam! Sed, laudantium animi odit doloremque dolorum quibusdam dolor suscipit magnam nostrum asperiores mollitia perferendis facilis eos fugiat?
						Ducimus quis quos, iusto reprehenderit omnis et amet corporis tempora eos reiciendis dolor ipsum suscipit expedita repellendus vel laborum facere numquam obcaecati modi incidunt at ex! Similique quam sint officiis!
						Quisquam incidunt cupiditate possimus earum ullam eveniet enim nesciunt rerum nam? Ab tempore, deleniti numquam sit ipsa temporibus error earum? Tempora et incidunt cupiditate recusandae. Dolorum dolores deleniti hic nisi.
						Ipsam, dolores. Iste, quam, sed deserunt veniam, illum at nam non ea cumque optio eos vitae. Eligendi accusantium, molestias amet voluptatibus, ipsa odio eum maxime ea ipsum consequuntur iure doloribus


					</td>
				</tr>
				<tr>
					<td><img src="../../images/pad/footer.png" alt="footer Image" class="upload_footer_file"></td>
				</tr>
			</table>


		</div>
	</div>


</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
	show_list_view(1,'search_list_view','search_list_view','../general_info/requires/generate_pad_controller','setFilterGrid("list_view",-1)');

</script>
</html>
