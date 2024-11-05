<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Color List
					
Functionality	:	
				

JS Functions	:

Created by		:	Ashraful 
Creation date 	: 	17-02-2014
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
echo load_html_head_contents("Color Information", "../../", 1, 1,$unicode,1,'');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

	var tableFilters = 
		{
			col_3: "select",
			col_4: "select"
		}





function fn_active_inactive()
{
	if($('#txt_terms_condition').val()){
		$('#txt_terms_condition_more').attr('disabled', true);
		$('#cbo_status').val(1);
	}
	else if($('#txt_terms_condition_more').val()){
		$('#txt_terms_condition').attr('disabled', true);
		$('#cbo_status').val(0);
	}
	else
	{
		$('#txt_terms_condition').attr('disabled', false);
		$('#txt_terms_condition_more').attr('disabled', false);
	}
}


function fnc_terms_condition( operation )
{
	
	if($('#txt_terms_condition').val()){
		var validationField='txt_terms_condition*txt_tag_page';
	}
	else if($('#txt_terms_condition_more').val()){
		var validationField='txt_terms_condition_more*txt_tag_page';
	}
	else{
		var validationField='txt_terms_condition*txt_tag_page';	
	}
	
	// if(operation==2){alert('Delete not allowed.');return;}
	
	if (form_validation(validationField,'Terms & Condition*Tag Page Name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_terms_condition*txt_terms_condition_more*txt_terms_condition_hdn*cbo_status*txt_tag_page*txt_tag_page_id*update_id*txt_terms_prefix',"../../");
		freeze_window(operation);
		http.open("POST","requires/terms_condition_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_terms_condition_reponse;
	}
}

function fnc_terms_condition_reponse()
{
	if(http.readyState == 4) 
	{  
		var reponse=trim(http.responseText).split('**');
		//alert(reponse[0])
		show_msg(reponse[0]);
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			show_list_view('','color_list_view','color_list_view','../merchandising_details/requires/terms_condition_entry_controller','setFilterGrid("list_view",-1,tableFilters)');
			reset_form('colorinfo_1','','');
			set_button_status(0, permission, 'fnc_terms_condition',1);
			release_freezing();
		}
		else if(reponse[0]==15)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
	}
}
function openmypage_tag_page()
	{
		var txt_tag_page_id = $('#txt_tag_page_id').val();
		var title = 'Tag Buyer Selection Form';	
		var page_link = 'requires/terms_condition_entry_controller.php?txt_tag_page_id='+txt_tag_page_id+'&action=page_name_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var buyer_id=this.contentDoc.getElementById("hidden_page_id").value;	 //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("hidden_buyer_name").value;
			$('#txt_tag_page_id').val(buyer_id);
			$('#txt_tag_page').val(buyer_name);
		}
	}


</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:60%; margin:0 auto;">	
    
   <fieldset>
    <fieldset style="width:500px;">
		<legend>Color Info</legend>
		<form name="colorinfo_1" id="colorinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="500px">
			 	<tr>
					<td width="100">Terms Prefix</td>
					<td colspan="3">
                       <textarea name="txt_terms_prefix" id="txt_terms_prefix" class="text_boxes" style="width:92%"></textarea> 
					</td>
              </tr>
			 	<tr>
					<td class="must_entry_caption">Terms & Condition</td>
					<td colspan="3">
                       <textarea name="txt_terms_condition" id="txt_terms_condition" class="text_boxes" style="width:92%" onKeyUp="fn_active_inactive();"></textarea> 
					   <input type="hidden" name="txt_terms_condition_hdn" id="txt_terms_condition_hdn">
					</td>
              </tr>
			 	<tr>
					<td class="must_entry_caption">Add More T & C</td>
					<td colspan="3">
                        <textarea name="txt_terms_condition_more" id="txt_terms_condition_more" class="text_boxes" style="width:92%" onKeyUp="fn_active_inactive();"></textarea>
					</td>
              </tr>
              <tr>
					<td class="must_entry_caption">Tag Page</td>
					<td colspan="3">
                        <input type="text" name="txt_tag_page" id="txt_tag_page" class="text_boxes" style="width:92%;" placeholder="Click To Search"  onClick="openmypage_tag_page();" readonly />
                            <input type="hidden" name="txt_tag_page_id" id="txt_tag_page_id" value="" />
					</td>
                    <td width="50" ></td>
					<td >
						 <input type="hidden" id="cbo_status" value="">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_terms_condition", 0,0 ,"reset_form('colorinfo_1','','');fn_active_inactive();",1);
				        ?> 
                        <input type="hidden" name="update_id" id="update_id" >
					</td>				
				</tr>
                <tr>
                    <td colspan="4">
                      <b>Note:</b> Bold Tag =&lt;b&gt; -------- &lt;/b&gt; ; Under Line Tag: &lt;u&gt; -------- &lt;/u&gt;
                    </td>
                </tr>
		   </table>
			</form> 
		</fieldset>	
        
            <div id="color_list_view">
				<script>
					show_list_view('','color_list_view','color_list_view','../merchandising_details/requires/terms_condition_entry_controller','setFilterGrid("list_view",-1,tableFilters)');
				</script>
            </div>
     </fieldset>
        
        
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
