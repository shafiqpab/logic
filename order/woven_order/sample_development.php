<?
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  21-05-2014
Purpose			         :  This Form Will Create Sample Development Entry.						
Functionality	         :	
JS Functions	         :
Created by		         :	Shajjad 
Creation date 	         : 
Requirment Client        :  Fakir Apperels
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                
DB Script                : 
Updated by 		         : 	Kaiyum	  ,Monir Hossain
Update date		         : 	26-09-2016,11-10-2016  
QC Performed BY	         :		
QC Date			         :	
Comments		         : 	[ Kaiyum: update for 'buyer wise season auto select' ]
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Sample Development", "../../", 1, 1,$unicode,'','');

?>
<script>
  	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
  	var permission='<? echo $permission; ?>';
	
	var str_working_factory = [<? echo substr(return_library_autocomplete( "select working_factory from  sample_development_dtls group by working_factory", "working_factory"), 0, -1); ?>];
	
	var str_sample_color = [<? echo substr(return_library_autocomplete( "select color_name from  lib_color group by color_name", "color_name"), 0, -1); ?>];
	
	var str_fabrication = [<? echo substr(return_library_autocomplete( "select fabrication from  sample_development_dtls group by fabrication", "fabrication"), 0, -1); ?>];
	
	function option_disabled(mode)
	{
		if(mode=='save_mode')
		{
			$('#cbo_approval_status option:eq(2)').attr("disabled","disabled");
			$('#cbo_approval_status option:eq(4)').attr("disabled","disabled");
			$('#cbo_approval_status option:eq(1)').removeAttr("disabled","disabled");
			$('#cbo_approval_status option:eq(5)').removeAttr("disabled","disabled");
		}
		if(mode=='update_mode')
		{
			var cbo_approval_status=document.getElementById('cbo_approval_status').value
			if(cbo_approval_status==0)
			{
				$('#cbo_approval_status option:eq(1)').removeAttr("disabled","disabled");
				
			}
			else
			{
				$('#cbo_approval_status option:eq(1)').attr("disabled","disabled");
			}
			$('#cbo_approval_status option:eq(2)').removeAttr("disabled","disabled");
			$('#cbo_approval_status option:eq(4)').removeAttr("disabled","disabled");
			$('#cbo_approval_status option:eq(5)').attr("disabled","disabled");
		}
	}
  
  	function set_auto_complete()
	{
		$("#txt_working_factory").autocomplete({
		source: str_working_factory
		});
		
		$("#txt_sample_color").autocomplete({
		source: str_sample_color
		});
		
		$("#txt_fabrication").autocomplete({
		source: str_fabrication
		});
	}

	function fnc_sample_development_mst_info( operation )
	{
	   if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_name*cbo_product_department*cbo_item_name*cbo_team_leader*cbo_dealing_merchant','Company Name*Buyer Name*Style Name*Product Department*Garments Item*Team Leader*Dealing Merchant')==false)
		{
			return;
		}	
		else
		{
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_style_id*cbo_company_name*cbo_buyer_name*txt_style_name*cbo_product_department*txt_article_no*cbo_item_name*txt_item_catgory*cbo_region*cbo_agent*cbo_team_leader*cbo_dealing_merchant*txt_est_ship_date*txt_remarks*cbo_season_name*txt_quotation_id*txt_bhmerchant*txt_product_code',"../../");
			
			freeze_window(operation);
			http.open("POST","requires/sample_development_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_development_mst_info_reponse;
		}
	}
	
	function fnc_sample_development_mst_info_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			if(trim(reponse[0])==0 || trim(reponse[0])==1)
			{
			document.getElementById('txt_style_id').value  = reponse[1];
			set_button_status(1, permission, 'fnc_sample_development_mst_info',1);
			}
			if(trim(reponse[0])==2)
			{
				reset_form('sampledevelopment_1*sampledevelopment_2','sample_development_list_view','')
			}
			release_freezing();
		}
	}
	
	function fnc_sample_development_details_info( operation )
	{
		if(operation==4)
		 {
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id_dtl').val()+'*'+$('#txt_style_id').val(), "sample_development_print", "requires/sample_development_controller" );
			 return;
		 }
		else if(operation==0 || operation==1 || operation==2)
		{
			if (form_validation('cbo_sample_type*txt_sample_color','Sample Name*Sample Color')==false)
			{
				return;
			}
			else
			{
				var data="action=save_update_delete_dtl&operation="+operation+get_submitted_data_string('txt_style_id*cbo_sample_type*txt_sample_color*txt_working_factory*txt_sent_to_factory_date*txt_factory_dead_line_date*txt_receive_date_from_buyer*txt_receive_date_from_factory*txt_fabrication*cbo_fabric_sorce*txt_sent_to_buyer_date*txt_key_point*cbo_approval_status*txt_department*txt_status_date*txt_tf_receive_date*txt_buyer_meeting_date*txt_sample_charge*cbo_curency*txt_comments*txt_buyer_dead_line_date*txt_buyer_request_no*update_id_dtl',"../../");
				//alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/sample_development_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_sample_development_details_info_response;
			}
		}
	}
	
	function fnc_sample_development_details_info_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			if(trim(reponse[0])==0 || trim(reponse[0])==1)
			{
			document.getElementById('update_id_dtl').value  = reponse[1];
			reset_form('','','txt_sample_color*cbo_fabric_sorce');
			}
			if(trim(reponse[0])==2)
			{
			reset_form('sampledevelopment_2','','')
			}
			show_list_view(document.getElementById('txt_style_id').value, 'sample_development_details_info_list_view', 'sample_development_list_view', '../woven_order/requires/sample_development_controller', 'setFilterGrid(\'list_view1\',-1)');
			$('#update_id_dtl').val('');
			set_button_status(0, permission, 'fnc_sample_development_details_info',2,0);
			option_disabled('save_mode')
			release_freezing();		
		}
	}
	
	function openmypage()
	{
		var title = 'Style ID Search';	
		var page_link = 'requires/sample_development_controller.php?&action=style_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id
			
			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_search_popup", "requires/sample_development_controller" );
				show_list_view(mst_tbl_id, 'sample_development_details_info_list_view', 'sample_development_list_view', '../woven_order/requires/sample_development_controller', 'setFilterGrid(\'list_view1\',-1)');
				set_button_status(1, permission, 'fnc_sample_development_mst_info',1,0);
				release_freezing();
			}
		}
	}
	
	function openmypage_sizeinfo()
	{
		var txt_style_id = $('#txt_style_id').val();
		var update_id_dtl = $('#update_id_dtl').val();
		var txt_sample_color = $('#txt_sample_color').val();
		
		if (form_validation('txt_style_id*update_id_dtl','Style ID*Data is not save')==false)
		{
			if (txt_style_id=='')
			{
				alert("Please Select Style No.");
			}
			else if(update_id_dtl=='')
			{
				alert("Data is not save.Please save first.");
			}
			return;
		}
		else
		{
			get_php_form_data(update_id_dtl, "load_php_data_to_form_sample_development_details_info","requires/sample_development_controller" );
			var data=document.getElementById('hidden_size_id').value+"_"+document.getElementById('hidden_qnty').value+"_"+document.getElementById('hidden_tbl_size_id').value+"_"+document.getElementById('hidden_bhqnty').value+"_"+document.getElementById('hidden_remarks').value;
			//alert( data);
			var title = 'Size Info';	
			var page_link = 'requires/sample_development_controller.php?txt_style_id='+txt_style_id+'&update_id_dtl='+update_id_dtl+'&txt_sample_color='+txt_sample_color+'&data='+data+'&action=sizeinfo_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var receive_size=this.contentDoc.getElementById("hidden_size"); 
				var receive_qty=this.contentDoc.getElementById("hidden_qty");
				var receive_bhqty=this.contentDoc.getElementById("hidden_bhqty");  
				var receive_id=this.contentDoc.getElementById("hidden_id");
				$('#hidden_size_id').val(receive_size.value);
				$('#hidden_qnty').val(receive_qty.value);
				$('#hidden_bhqnty').val(receive_bhqty.value);
				$('#hidden_tbl_size_id').val(receive_id.value);
			}
		}
	}
function color_from_library(company_id)
{
	var color_from_library=return_global_ajax_value(company_id, 'color_from_library', '', 'requires/sample_development_controller');
	if(color_from_library==1)
	{
		$('#txt_sample_color').attr('readonly',true);
		$('#txt_sample_color').attr('placeholder','Click');
		$('#txt_sample_color').attr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value);");
		
	}
	else
	{
		$('#txt_sample_color').attr('readonly',false);
		$('#txt_sample_color').removeAttr('placeholder','Click');
		$('#txt_sample_color').removeAttr('onClick',"color_select_popup(document.getElementById('cbo_buyer_name').value);");
	}
}
function color_select_popup(buyer_name)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_development_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		if (color_name.value!="")
		{
			$('#txt_sample_color').val(color_name.value);
		}
	}
}
function quotation_inquery_popup()
	{
		//reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');
	
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();		
		var cbo_buyer_name = $("#cbo_buyer_name").val();	
		var page_link='requires/sample_development_controller.php?action=quotation_inquery_id&company='+company+'&cbo_buyer_name='+cbo_buyer_name; 
		var title="Search  Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_"); 
			get_php_form_data(mrrNumber[0], "populate_data_from_data", "requires/sample_development_controller");
		}
	}
	
	function fnc_sample_developemt(type)
	{
		if( form_validation('txt_style_id*cbo_company_name','Style ID*Company Name')==false )
		{
			return;
		}
		 
		 print_report( $('#cbo_company_name').val()+'*'+$('#update_id_dtl').val()+'*'+$('#txt_style_id').val()+$('#update_id').val(), "sample_development_request_print", "requires/sample_development_controller" );
			 return;
		
	}
	
	function season_auto_load()
	{
		load_drop_down( 'requires/sample_development_controller',  document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td' );
	}
</script>
</head>
<body onLoad="set_hotkey(); set_auto_complete(); season_auto_load();">
	<div align="center" style="width:100%;">
		<?=load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:950px;">
        	<legend>Order Details</legend>
            <form name="sampledevelopment_1" id="sampledevelopment_1" autocomplete="off">
                <table align="center" width="960">
                    <tr>
                        <td width="90">Style ID</td>                          
                        <td width="140"><Input name="txt_style_id" class="text_boxes" ID="txt_style_id" style="width:120px" placeholder="Double Click to Search" onDblClick="openmypage()" maxlength="50" title="Maximum 50 Character" readonly></td>
                        <td width="90" class="must_entry_caption">Company Name</td>                          
                        <td width="140">
                            <?=create_drop_down( "cbo_company_name", 130, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_development_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_development_controller', this.value, 'load_drop_down_agent', 'agent_td' );color_from_library( this.value )" ); ?>
                        </td>
                        <td width="90" class="must_entry_caption">Buyer Name</td>                          
                        <td id="buyer_td" width="140"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>  
                        <td class="must_entry_caption">Style Name</td>                          
                        <td>
                            <Input name="txt_style_name" class="text_boxes" ID="txt_style_name" onDblClick="quotation_inquery_popup();" placeholder="Double Click to Quotation" style="width:120px" maxlength="50" title="Maximum 50 Character">
                             <input type="hidden" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:30px;">
                        </td> 
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Garments Item</td>                          
                        <td><?=create_drop_down( "cbo_item_name", 130, get_garments_item_array(2), "",1," -- Select Item --", $selected, "",'','' ); ?></td> 
                        <td class="must_entry_caption">Product Dept.</td>                          
                        <td>
							<?=create_drop_down( "cbo_product_department", 80, $product_dept, "", 1, "-- Select prod. Dept--", $selected, "", "", "" ); ?>
                             <input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" />
                        </td> 
                        <td class="must_entry_caption">Team Leader</td>   
                        <td><?=create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sample_development_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' ) " ); ?></td>
                        <td class="must_entry_caption">Dealing Merchant</td>   
                        <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td>Article Number</td>                          
                        <td><input name="txt_article_no" class="text_boxes" ID="txt_article_no" style="width:120px" maxlength="50" title="Maximum 50 Character"></td> 
                        <td>Product Category</td>                          
                        <td><?=create_drop_down( "txt_item_catgory", 130, $product_category,"", 1, "-- Select Product Category --", $selected, "","","" ); ?></td>
                        <td>Region</td>                          
                        <td><?=create_drop_down( "cbo_region", 130, $region, 1, "-- Select Region --", $selected, "" ); ?></td> 
                        <td>Agent Name</td>                          
                        <td id="agent_td"><?=create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td>BH Merchant</td>
                        <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_bhmerchant" id="txt_bhmerchant"/></td>
                        <td>Est. Ship Date</td>                          
                        <td><input name="txt_est_ship_date" id="txt_est_ship_date" class="datepicker" type="text" value="" style="width:120px; text-align:left" /></td>
                        <td>Season</td>
                        <td id="season_td"><?=create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td> 
                        <td>Images</td>
                        <td><input type="button" class="image_uploader" style="width:130px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_style_id').value,'', 'sample_development', 0 ,1)"></td>
                    </tr>
                    <tr>
                   		<td>Remarks/Desc.</td>                          
                        <td colspan="5"><Input name="txt_remarks" class="text_boxes" ID="txt_remarks" style="width:590px" maxlength="50" title="Maximum 50 Character"></td>
                        <td >&nbsp;&nbsp;File</td>
                        <td align="left"> <input type="button" id="image_button" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader( '../../', document.getElementById('txt_style_id').value,'', 'sample_development', 2 ,1)" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" height="15">
                            <input type="hidden" name="update_id" id="update_id" value="">
                            <input type="hidden" name="id_lib_mkt_team_member_info" id="id_lib_mkt_team_member_info" value="">
                        </td>		 
                    </tr>
                    <tr>
                        <td colspan="8"8 valign="bottom" align="center" class="button_container">
                            <? 
                                echo load_submit_buttons( $permission, "fnc_sample_development_mst_info", 0,0,"reset_form('sampledevelopment_1*sampledevelopment_2','sample_development_list_view','')",1);
                            ?>
                        </td>		 
                    </tr>
                </table>
            </form>
            </fieldset>
            <br>      
            <fieldset style="width:950px;">
                <legend>New Sample Development Entry</legend>
                <form name="sampledevelopment_2" id="sampledevelopment_2" autocomplete="off" >
                    <table align="center" width="950">
                        <tr>
                            <td width="90" class="must_entry_caption">Sample Name</td>                          
                            <td width="140" id="sampletd"><?=create_drop_down( "cbo_sample_type", 130, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '1', "--Select--", '', "",'','' ); ?></td>
                            <td width="90" class="must_entry_caption">Sample Color</td>                          
                            <td width="140">
                                <Input name="txt_sample_color" class="text_boxes" ID="txt_sample_color" style="width:64px" maxlength="50" title="Maximum 50 Character">
                                <input type="button" class="image_uploader" style="width:45px" value="Size Dtls" onClick="openmypage_sizeinfo();">
                            </td>
                            <td width="90">Rcv. From Buyer</td>                          
                            <td width="140"><input name="txt_receive_date_from_buyer" id="txt_receive_date_from_buyer" class="datepicker" type="text" style="width:120px;" /></td>
                            <td width="90">TF Receive Date</td>                          
                            <td><input name="txt_tf_receive_date" id="txt_tf_receive_date" class="datepicker" type="text" value="" style="width:120px; text-align:left" /></td>
                        </tr>
                        <tr>
                            <td>Buyer Dead Line</td>                          
                            <td><input name="txt_buyer_dead_line_date" id="txt_buyer_dead_line_date" class="datepicker" type="text" style="width:120px; text-align:left" /></td>
                            <td>Working Factory</td>                          
                            <td><Input name="txt_working_factory" class="text_boxes" ID="txt_working_factory" style="width:120px" maxlength="50" title="Maximum 50 Character"></td>
                            <td>Sent To Smpl Dept.</td>                          
                            <td><input name="txt_sent_to_factory_date" id="txt_sent_to_factory_date" class="datepicker" type="text" style="width:120px; text-align:left" /></td>
                            <td>Dead Line</td>                          
                            <td><input name="txt_factory_dead_line_date" id="txt_factory_dead_line_date" class="datepicker" type="text" style="width:120px; text-align:left" /></td>
                        </tr>
                        <tr>
                            <td>Rcv.From Smp Dept.</td>                          
                            <td><input name="txt_receive_date_from_factory" id="txt_receive_date_from_factory" class="datepicker" type="text" style="width:120px;" /></td>
                            <td>Sent to Buyer</td>                          
                            <td><input name="txt_sent_to_buyer_date" id="txt_sent_to_buyer_date" class="datepicker" type="text"  style="width:120px;" /></td>
                            <td>Fabrication</td>                          
                            <td><Input name="txt_fabrication" class="text_boxes" ID="txt_fabrication" style="width:120px" maxlength="100" title="Maximum 100 Character"></td>
                            <td>Fabric Source</td>                          
                            <td><?=create_drop_down( "cbo_fabric_sorce", 130, $fabric_source, "", 1, "-- Select Fabric Source--", $selected, "", "", "" ); ?></td>
                        </tr>
                        <tr>
                            <td>Key Point/Val Drive</td>                          
                            <td><Input name="txt_key_point" class="text_boxes" ID="txt_key_point" style="width:120px" maxlength="50" title="Maximum 50 Character"></td>
                            <td>Department</td>                          
                            <td><Input name="txt_department" class="text_boxes" ID="txt_department" style="width:120px" maxlength="50" title="Maximum 50 Character"></td>
                            <td>Approval Status</td>
                            <td><?=create_drop_down( "cbo_approval_status", 130, $approval_status,"", 1, "-----   -----", "", "","",'' ); ?></td>
                            <td>Status Date</td>                          
                            <td><input name="txt_status_date" id="txt_status_date" class="datepicker" type="text" value="" style="width:120px; text-align:left"  /></td>
                        </tr>
                        <tr>
                            <td>Buyer Meeting</td>                          
                            <td><input name="txt_buyer_meeting_date" id="txt_buyer_meeting_date" class="datepicker" type="text" style="width:120px; text-align:left" /></td>
                            <td>Sample Charge</td>                          
                            <td>
                                <input name="txt_sample_charge" id="txt_sample_charge" class="text_boxes_numeric" type="text" value="" style="width:40px;"  />
                                <?=create_drop_down( "cbo_curency", 78, $currency,"", 1, "--Select-- ", "", "","",'' ); ?>
                            </td>
                            <td>Buyer Req. No</td>                          
                            <td><input name="txt_buyer_request_no" id="txt_buyer_request_no" class="text_boxes" type="text" title="Maximum 100 Character" maxlength="100" style="width:120px;" /></td>
                            <td>Comments</td>                          
                            <td><Input name="txt_comments" class="text_boxes" ID="txt_comments" style="width:120px" maxlength=""></td>	
                        </tr>
                        <tr>
                            <td colspan="8" valign="bottom" align="center" class="button_container">
                                <?=load_submit_buttons( $permission, "fnc_sample_development_details_info", 0,1,"reset_form('sampledevelopment_2','','')",2); ?>
                                <input type="hidden" name="update_id_dtl" id="update_id_dtl" value="">
                                <input type="hidden" name="hidden_size_id" id="hidden_size_id" value="">
                                <input type="hidden" name="hidden_qnty" id="hidden_qnty" value="">
                                <input type="hidden" name="hidden_bhqnty" id="hidden_bhqnty" value="">
                                <input type="hidden" name="hidden_remarks" id="hidden_remarks" value="">
                                <input type="hidden" name="hidden_tbl_size_id" id="hidden_tbl_size_id" value="">
                                <input type="button" value="Print 2" class="formbutton" onClick="fnc_sample_developemt(2)" style="width:80px;">					
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" id="sample_development_list_view" align="center"></td>
                        </tr>
                    </table>
                </form>
            </fieldset>
	</div>
</body>
<script>
option_disabled('save_mode')
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			