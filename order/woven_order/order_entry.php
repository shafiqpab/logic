﻿<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	29-11-2015	
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
$user_id=$_SESSION['logic_erp']['user_id'];

$teamIdSql=sql_select("select a.id from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and a.project_type=1 and a.status_active =1 and a.is_deleted=0 and b.user_tag_id =$user_id group by a.id order by a.id");
$teamId=$teamIdSql[0][csf('id')];
if($teamId==''){
	$team_group_data=sql_select("select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name");
	if(count($team_group_data)==1){
		$teamId=$team_group_data[0][csf('id')];
	}
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');
$mst_mandatory_fields=array();
$mst_mandatory_msg=array();
$po_mandatory_fields=array();
$po_mandatory_msg=array();

if(count($_SESSION['logic_erp']['mandatory_field'][365])>0)
{
	foreach ($_SESSION['logic_erp']['mandatory_field'][365] as $key => $field) {
		if($key==8 || $key==9 || $key==16)
		{			
			$po_mandatory_fields[$key] = $field;
		}
		else{
			$mst_mandatory_fields[$key] = $field;
		}
	}

	foreach ($_SESSION['logic_erp']['mandatory_message'][365] as $key => $field) {
		if($key==8 || $key==9 || $key==16) 
		{			
			$po_mandatory_msg[$key] = $field;
		}
		else{
			$mst_mandatory_msg[$key] = $field;
		}
	}
}
	
	$sql_temp=sql_select("SELECT comapny_id, buyer_id, total as percentage, upper_limit_qty, lower_limit_qty FROM lib_excess_cut_slab WHERE comapny_id=3 and buyer_id=37 and status_active=1 and is_deleted=0 order by comapny_id, buyer_id, lower_limit_qty asc");
	//echo "SELECT comapny_id, buyer_id, total as percentage, upper_limit_qty, lower_limit_qty FROM lib_excess_cut_slab WHERE comapny_id=3 and buyer_id=37 and status_active=1 and is_deleted=0 order by comapny_id, buyer_id, lower_limit_qty asc";
	$i=0;
	foreach($sql_temp  as $row)
	{
		if( $exc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]=='') $i=0;
		$exc_perc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]['limit'][$i]=$row[csf("lower_limit_qty")]."__".$row[csf("upper_limit_qty")];
		$exc_perc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]['val'][$i]=$row[csf("percentage")];
		$exc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]=1;
		//echo $i."=";
		$i++;
	}
	//print_r($exc_perc);
	unset($sql_temp);
	$isBhTagInOrderEntryDisplay="";
	if($isBhTagInOrderEntry!=1) { $isBhTagInOrderEntryDisplay="display:none"; }//ISD-23-21013
?>	
<script>
	
	var permission='<?=$permission; ?>';
	var exc_perc =<?=json_encode($exc_perc); ?>;
	var user='<? echo $_SESSION['logic_erp']['user_id']; ?>';
	var isBhTagInOrderEntry='<?=$isBhTagInOrderEntry; ?>';
	var isBhTagInOrderEntryDisplay="";
	if(isBhTagInOrderEntry!=1) { var isBhTagInOrderEntryDisplay="display:none"; }//ISD-23-21013
	//alert( excess_percentage( 3, 37, 7000 ))
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------

		var field_level_data="";
	<?
		//print_r($_SESSION['logic_erp']['data_arr'][365]);
		if(isset($_SESSION['logic_erp']['data_arr'][365]))
		{
		  $data_arr=json_encode($_SESSION['logic_erp']['data_arr'][365] );
		  echo "var field_level_data= ". $data_arr . ";\n";
		}
		echo "var mst_mandatory_field = '". implode('*',$mst_mandatory_fields) . "';\n";
		echo "var mst_mandatory_message = '". implode('*',$mst_mandatory_msg) . "';\n";
		echo "var po_mandatory_field = '". implode('*',$po_mandatory_fields) . "';\n";
		echo "var po_mandatory_message = '". implode('*',$po_mandatory_msg) . "';\n";
	?>
	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size where status_active=1 and is_deleted=0 order by size_name ASC", "size_name" ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 order by color_name ASC", "color_name" ), 0, -1); ?>];

	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	
	function fnc_variable_settings_check(company_id)
	{
		$('#cbo_style_owner').val( company_id );
		$('#cbo_working_factory').val( 0 );
		var all_variable_settings=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/order_entry_controller');
		var ex_variable=all_variable_settings.split("_");
		var tna_integrated=ex_variable[0];
		var copy_quotation=ex_variable[1];
		//alert(copy_quotation);
		var publish_shipment_date=ex_variable[2];
		var po_update_period=ex_variable[3];
		var po_current_date=ex_variable[4];
		var season_mandatory=ex_variable[5];
		$('#po_current_date_maintain').val(po_current_date);
		var order_status=$('#cbo_order_status').val();
		var current_date='<? echo date('d-m-Y'); ?>';
		var excut_source=ex_variable[6];
		var cost_control_source=ex_variable[7];
		var set_smv_id=ex_variable[8];
		var color_from_lib=ex_variable[9];
		var act_po_id=ex_variable[10];
		var po_update_user_id=ex_variable[11];
		var po_control_booking=ex_variable[12];
		
		$('#act_po_id').val( act_po_id );
		$('#po_control_booking').val( po_control_booking );
		if(po_update_period==1)
		{
			if(po_current_date==1 && order_status==1)
			{
				$('#txt_po_received_date').attr('disabled','disabled');
				$('#txt_po_received_date').val(current_date);
			}
			else
			{
				$('#txt_po_received_date').val(current_date);	
				$('#txt_po_received_date').removeAttr('disabled','');
			}
		}
		else
		{
			if(po_current_date==1 && order_status==1)
			{
				$('#txt_po_received_date').attr('disabled','disabled');
				$('#txt_po_received_date').val(current_date);
			}
			else
			{
				$('#txt_po_received_date').val(current_date);	
				$('#txt_po_received_date').removeAttr('disabled','');
			}
		}
		
		if(copy_quotation==1)
		{
			if(set_smv_id==2 || set_smv_id==4 || set_smv_id==5 || set_smv_id==6)
			{
				
				$('#txt_style_ref').attr('readonly',true);
				$('#txt_style_ref').attr('placeholder','Browse');
				var page_link="'requires/order_entry_controller.php?action=quotation_id_popup','Quotation ID Selection Form'";// for Price Quotation
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else if(set_smv_id==7)
			{
				$('#txt_style_ref').attr('readonly',true);
				$('#txt_style_ref').attr('placeholder','Browse');
				var page_link="'requires/order_entry_controller.php?action=qc_id_popup','Quick Costing Style Selection'";// for Quick Costing
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else if(set_smv_id==3 || set_smv_id==8 || set_smv_id==9)
			{
				$('#txt_style_ref').attr('readonly',true);
				$('#txt_style_ref').attr('placeholder','Browse');
				var page_link="'requires/order_entry_controller.php?action=ws_id_popup','Work Study Style Selection'";// for Work Study
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else
			{
				$('#txt_style_ref').attr('readonly',false);
				$('#txt_style_ref').attr('placeholder','Write');
				$('#txt_style_ref').removeAttr('onDblClick','onDblClick');	
			}
		}
		else
		{
			$('#txt_style_ref').attr('readonly',false);
			$('#txt_style_ref').attr('placeholder','Write');
			$('#txt_style_ref').removeAttr('onDblClick','onDblClick');	
		}
		
		var bhstyle=$('#cbo_style_from').val(); //ISD-23-21013
		if(bhstyle==1)//ISD-23-21013
		{
			$('#txt_style_ref').attr('readonly',true);
			$('#txt_style_ref').attr('placeholder','Browse');
			var page_link="'requires/order_entry_controller.php?action=bh_style_popup','Buying House Style Selection'";// for Buying House
			$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
		}
		
		if(publish_shipment_date==1)
		{
			$('#txt_pub_shipment_date').attr('disabled',false);
			$('#txt_style_ref').val('');
		}
		else
		{
			$('#txt_pub_shipment_date').attr('disabled',true);
			$('#txt_style_ref').val('');
		}
		$('#hid_variable_pubshipdate').val(publish_shipment_date);
		
		if(season_mandatory==1) $('#is_season_must').val( season_mandatory );
		if(excut_source!=0) $('#hid_excessCut_source').val( excut_source );
		if(cost_control_source!=0) $('#hid_cost_source').val( cost_control_source );po_update_period_maintain
		if(set_smv_id!=0) $('#set_smv_id').val( set_smv_id ); else $('#set_smv_id').val( 0 );
		if(copy_quotation!=0) $('#hid_copy_quotion').val( copy_quotation ); else $('#hid_copy_quotion').val( 0 );
		if(po_update_period!=0) $('#po_update_period_maintain').val( po_update_period ); else $('#po_update_period_maintain').val( 0 );
		if(po_current_date!=0) $('#po_current_date_maintain').val( po_current_date ); else $('#po_current_date_maintain').val( 0 );
		if(po_update_user_id!=0) $('#txt_user_id').val( po_update_user_id ); else $('#txt_user_id').val( 0 );
			
		if(color_from_lib==1)
		{
			$('#hidd_color_from_lib').val( color_from_lib );
			$('#txtColorName_1').attr('readonly',true);
			$('#txtColorName_1').attr('placeholder','Browse');
			$('#txtColorName_1').removeAttr("onDblClick").attr("onDblClick","color_select_popup("+1+")");
		}
		else 
		{
			$('#hidd_color_from_lib').val( 2 );
			$('#txtColorName_1').attr('readonly',false);
			$('#txtColorName_1').attr('placeholder','Write');
			$('#txtColorName_1').removeAttr('onDblClick','onDblClick');	
		}
	}
	
	function color_select_popup(id)
	{
		var buyer_name=$('#cbo_buyer_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_entry_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("hiddcolor_name").value;
			if (color_name!="")
			{
				var excolor_name=color_name.split("__");//ISD-23-24355
				var colorseq=0; var inc=id;
				for(var b=1; b<=excolor_name.length; b++)
				{
					$('#txtColorName_'+inc).val(excolor_name[colorseq]);
					append_color_size_row(1);
					colorseq++; inc++;
				}
			}
		}
	}
	
	function color_select_popup1(id)//ISD-23-24355
	{
		var buyer_name=$('#cbo_buyer_name').val();
		//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
		//alert(texbox_id)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_entry_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#txtColorName_'+id).val(color_name.value);
				append_color_size_row(1);
			}
		}
	}
	
	function sub_dept_load(cbo_buyer_name,cbo_product_department)
	{
		if(cbo_buyer_name ==0 || cbo_product_department==0 )
		{
			return;
		}
		else
		{
			load_drop_down( 'requires/order_entry_controller',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' );
		}
	}
	
	function check_tna_templete(buyer_id)
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var tnare=return_global_ajax_value(buyer_id+"_"+cbo_company_name, 'check_tna_templete', '', 'requires/order_entry_controller');
		var data=tnare.split("_");
		var temp=data[0];
		var tna=data[1];
		var cut_off_used=data[3];
		if(tna==1 && temp==0)
		{
			alert("TNA Intregeted, But TNA templete not found for this Buyer; please set templete first.")
			$('#cbo_buyer_name').val(0);
		}
		$('#cut_off_used').val(cut_off_used);
	}
	
	function open_qoutation_popup(page_link,title)
	{
		if( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer Name')==false)
		{
			return;
		}
		else
		{
			var cbo_company_name=$('#cbo_company_name').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
			var txt_job_no=$('#txt_job_no').val();
			var set_smv_id=document.getElementById('set_smv_id').value;
			page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_job_no='+txt_job_no;
			var bhstyle=$('#cbo_style_from').val(); //ISD-23-21013
			if(bhstyle==1)
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1170px,height=420px,center=1,resize=0,scrolling=0','../')
			}
			else
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=420px,center=1,resize=0,scrolling=0','../')
			}
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					$('#tot_smv_qnty').val('');
					var cost_source=$('#hid_cost_source').val();
					
					if(bhstyle==1)//ISD-23-26871
					{
						get_php_form_data(theemail.value, "populate_data_from_bh_style_popup", "requires/order_entry_controller");
						show_list_view("0*0*"+bhstyle+"*"+$('#txt_quotation_id').val()+"*"+$('#cbo_company_name').val(),'order_listview','po_list_view','requires/order_entry_controller','setFilterGrid(\'tbl_po_list\',-1)');
					}
					else
					{
						if(set_smv_id==2){
							$("#cbo_order_uom").attr("disabled",true);
						}
						if(set_smv_id==3 || set_smv_id==8 || set_smv_id==9)
						{
							$('#txt_style_ref').val(theemail.value);
							$("#cbo_buyer_name").attr("disabled",true);
							$("#txt_style_ref").attr("disabled",true);
						}
						else
						{
							if(cost_source==2)
							{
								get_php_form_data( theemail.value+"_"+document.getElementById("cbo_company_name").value, "populate_data_from_search_popup_quotation", "requires/order_entry_controller" );
								$('#cbo_order_uom').attr('disabled',true);
							}
							else if (cost_source==1 || cost_source==5 || cost_source==8)
							{
								get_php_form_data( theemail.value, "populate_data_from_search_popup_qc", "requires/order_entry_controller" );
							}
							
							if(set_smv_id==2) load_drop_down('requires/order_entry_controller',document.getElementById("cbo_company_name").value, 'load_drop_down_location', 'location' );
							location_select();
						}
					}
					
					release_freezing();
				}
			}
		}
	}

	function open_set_popup(unit_id,texboxid) 
	{
		var	pcs_or_set="";
		var txt_job_no=document.getElementById('txt_job_no').value;
		var set_smv_id=document.getElementById('set_smv_id').value;
		var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		
		if(form_validation('cbo_buyer_name','Buyer')==false)
		{
			return;
		}
		if(txt_style_ref=="")
		{
			alert("Please Write/Browse Style Ref. Field Value.");
			return;
		}
		
		var precost = return_ajax_request_value(txt_job_no, 'check_precost', 'requires/order_entry_controller');
		var data=precost.split("_");
		if(set_smv_id==1 || set_smv_id==7)
		{	
			if(data[2]>0){
				alert("Pre Cost Approved, Any Change not allowed.");
				document.getElementById('cbo_order_uom').value=data[1];
				//return;
			}
			else if(data[0]>0 && texboxid=='cbo_order_uom'){
				alert("Pre Cost Found, UOM Change not allowed");
				document.getElementById('cbo_order_uom').value=data[1];
				return;
			}
			else if (data[0]>0 && texboxid=='set_button'){
				alert("Pre Cost Found, only Sew. and Cut. SMV Change allowed");
				//document.getElementById('cbo_order_uom').value=data[1];
			}
		}
		
		/*else if (data[0]>0 && texboxid=='set_button')
		{
			alert("Pre Cost Found, Any Change will be not allowed");
		}*/
		
		if(unit_id==58) pcs_or_set="Item Details For Set";
		else if(unit_id==57) pcs_or_set="Item Details For Pack";
		else pcs_or_set="Item Details For Pcs";
		
		var page_link="requires/order_entry_controller.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&tot_smv_qnty='+tot_smv_qnty+'&precostfound='+data[0]+'&precostapproved='+data[2]+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=1350px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
			load_drop_down( 'requires/order_entry_controller', item_id.value, 'load_drop_gmts_item', 'itm_td');
		}		
	}
	
	function open_terms_condition_popup(page_link,title)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
		if (txt_job_no=="")
		{
			alert("Save The Job No First")
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('txt_job_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
		}
	}
 
	function fnc_order_entry( operation )
	{
		freeze_window(operation);
		var delete_country=0;
		if(operation==2)
		{
			var rr=confirm("You are going to delete all PO & country data.\n Are you sure?");
			if(rr==true)
			{
				delete_country=1;
			}
			else
			{
				delete_country=0;
				release_freezing();
				return;
			}
		}
		
		var tot_smv_qnty=$('#tot_smv_qnty').val();
		if(tot_smv_qnty*1<=0){
			alert("Insert SMV");
			release_freezing();
			return;
		}

		// if(mst_mandatory_field !=''){
		// 	if (form_validation(mst_mandatory_field,mst_mandatory_message)==false){
		// 		release_freezing();
		// 		return;
		// 	}
		// }
		if(operation==4)
		{
			if (form_validation('txt_job_no*cbo_company_name','Job No*Company Name')==false)
			{
				release_freezing();
				return;
			}	
			print_report(document.getElementById('txt_job_no').value + '**'+document.getElementById('cbo_company_name').value+ '**'+document.getElementById('cbo_buyer_name').value+ '**' + '../../../' , "print_job_details", "requires/order_entry_controller");
			release_freezing();
			return;
		}else if(operation==5){

			if (form_validation('txt_job_no*cbo_company_name','Job No*Company Name')==false)
			{
				release_freezing();
				return;
			}	
			print_report(document.getElementById('txt_job_no').value + '**'+document.getElementById('cbo_company_name').value+ '**'+document.getElementById('cbo_buyer_name').value+ '**' + '../../../' , "print_B2", "requires/order_entry_controller");
			release_freezing();
			return;
		}
		
		if(operation==1)
		{
			var txt_job_no =$("#txt_job_no").val();
			var precost = return_ajax_request_value(txt_job_no, 'check_precost_approve', 'requires/order_entry_controller');
			if (precost==1)
			{
				alert("Pre Cost Approved, Any Change will be not allowed.");
				release_freezing();
				return;
			}
			
			/*var po_id="";
			var txt_job_no=document.getElementById('txt_job_no').value;
			var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/order_entry_controller')
			var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
			if(trim(booking_no_with_approvet_status_arr[0]) !="")
			{
				var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
				if(booking_no_with_approvet_status_arr[1] !="")
				{
					al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
				}
				al_magg+=" found,\nPlease Un-approved the booking first";
				alert(al_magg)
				release_freezing();
				return;
			}
			
			if(trim(booking_no_with_approvet_status_arr[1]) !="")
			{
				var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update color size break down, Pre-cost and booking against this Job ";
				var r=confirm(al_magg);
				if(r==false)
				{
					release_freezing();
					return;
				}
				else
				{
				}
			}*/
		}

		var buyer_name=get_dropdown_text('cbo_buyer_name');
		var buyer_id=$("#cbo_buyer_name").val();
		//if((buyer_name.search('TORAY-GU TRPU') && buyer_id==24) || (buyer_name.search('TORAY-N.MATSUYA') && buyer_id==28) || (buyer_name.search('TORAY-MUJI') && buyer_id==27))//24,28,27
		if((buyer_name=='TORAY-GU TRPU' && buyer_id==24) || (buyer_name=='TORAY-N.MATSUYA' && buyer_id==28) || (buyer_name=='TORAY-MUJI' && buyer_id==27))
		{
			alert("This buyer is not availabe for any Style Ref. in ERP. If need Please contract with MIS.");
			release_freezing();
			return;
		}
		
		if(mst_mandatory_field){
			if (form_validation(mst_mandatory_field,mst_mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}

			
		if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_product_department*txt_item_catgory*cbo_dealing_merchant*item_id*tot_smv_qnty','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Item Details*SMV')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			var is_season_must=$('#is_season_must').val()*1;
			if(is_season_must==1)
			{
				if($('#cbo_season_id').val()==0)
				{
					alert('Season not blank.');
					$('#cbo_season_id').focus();
					release_freezing();
					return;
				}
			}
			var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		
			var styledata='&txt_style_ref='+"'"+txt_style_ref+"'";
			
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_job_no*garments_nature*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_description*cbo_product_department*txt_product_code*cbo_sub_dept*cbo_currercy*cbo_agent*cbo_client*txt_repeat_no*cbo_region*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_packing*txt_remarks*cbo_ship_mode*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*tot_smv_qnty*txt_quotation_id*update_id*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_factory_merchant*cbo_qltyLabel*cbo_style_owner*chk_is_repeat*set_smv_id*hidd_job_id*cbo_fit_id*cbo_sustainability_standard*cbo_fab_material*cbo_order_nature*cbo_product_group*cbo_working_factory*cbo_working_location_id*cbo_style_from*txt_repeat_job_no',"../../")+styledata;
			//alert(data)
			
			http.open("POST","requires/order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse_mst;
		}
	}
	
	function fnc_on_submit_reponse_mst()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="SMV"){
				alert("Insert SMV in Item Pop-Up");
				release_freezing();
				return;
			}
			var alt_booking_msg="Delete Restricted, Booking Found, Booking No: "+reponse[0];
			if(reponse[0]==13)
			{
				alert(alt_booking_msg);
				release_freezing();
				return;
			}
			var alt_budget_msg="Delete Restricted, Budget Found.";
			if(reponse[0]==14)
			{
				alert(alt_budget_msg);
				release_freezing();
				return;
			}
			if(reponse[0]==15) 
			{
				release_freezing();
				setTimeout('fnc_order_entry('+ reponse[0]+')',8000); 
			}
			else if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_job_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('txt_repeat_no').value=reponse[3];
				$('#hidd_job_id').val(reponse[5]);
				if(parseInt(trim(reponse[0]))==0)
				{
					if(reponse[1]!='' && reponse[4]!=0)
					{
						fnc_qoutation_img_copy(reponse[1],reponse[4]);
					}
				}
				set_button_status(1, permission, 'fnc_order_entry',1);
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				refresh_page();
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
	}
	
	function refresh_page()
	{
		window.location.reload();
	}
	
	function fnc_qoutation_img_copy(job_no,quotation_id)
	{
		var company_name=document.getElementById('cbo_company_name').value;
		//alert(company_name+'_'+job_no+'_'+quotation_id);
		var job_image = return_ajax_request_value(job_no+'_'+quotation_id+'_'+company_name, 'quotation_image_copy_for_job', 'requires/order_entry_controller');
	}
	
	function openmypage_job(page_link,title)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;   
		}
		else
		{
			var cbo_company_name=$('#cbo_company_name').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
			var garments_nature=document.getElementById('garments_nature').value;
			page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=430px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job");
				if (theemail.value!="")
				{
					freeze_window(5);
					
					get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/order_entry_controller" );
					var current_date='<? echo date('d-m-Y'); ?>';
					set_field_level_access(document.getElementById("cbo_company_name").value);
					reset_form('orderentry_2','breakdown_div*breakdownratio_div*country_po_list_view*act_po_list_view','','','');
					load_drop_down( 'requires/order_entry_controller', document.getElementById('txt_job_no').value, 'load_drop_down_projected_po', 'projected_po_td' );
					load_drop_down( 'requires/order_entry_controller', document.getElementById('item_id').value, 'load_drop_gmts_item', 'itm_td');
					show_list_view(theemail.value+"*0*"+$('#cbo_style_from').val()+"*"+$('#txt_quotation_id').val()+"*"+$('#cbo_company_name').val(),'order_listview','po_list_view','requires/order_entry_controller','setFilterGrid(\'tbl_po_list\',-1)');
					po_recevied_date();
					$('#copy_asac').val(2);
					$('#copy_assc').val(2);
					$('#copy_acss').val(2);
					$('#copy_excut').val(2);
					
					$('#breakdown_div').html('');
					$('#breakdownratio_div').html('');
					$('#td_color tr:not(:first)').remove();
					$('#td_size tr:not(:first)').remove();
					$('#txtColorName_1').val('');
					$('#txtSizeName_1').val('');
					$('#txtColorId_1').val('');
					$('#txtSizeId_1').val('');
					$('#txt_is_update').val(0);
					$('#color_size_break_down_all_data').val('');
					$('#color_size_ratio_data').val('');
					$('#txt_avg_price').val( $('#txt_quotation_price').val() );
					set_button_status(1, permission, 'fnc_order_entry',1);
					set_button_status(0, permission, 'fnc_order_entry_details',2);
					release_freezing();
				}
			}
		}
	}
	// Master Form End -----------------------------------------------------------------------------
	function internal(ref)
	{
		var internal_ref = [];
		var int_ref=ref.split(",");
		for(var i=0; i<int_ref.length; i++)
		{
			//alert(int_ref[i].replace(/\"/g,''));
			internal_ref[i]= int_ref[i].replace(/\"/g,'');
		}
		//var str_color = [<? //echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
		$("#txt_grouping").autocomplete({
		source: internal_ref
		});
	}

	//Dtls Form-------------------------------------------------------------------------------------
	function fnc_order_entry_details( operation )
	{
		freeze_window(operation);
		var txt_job_no =$("#txt_job_no").val();
		var precost = 0;//return_ajax_request_value(txt_job_no, 'check_precost_approve', 'requires/order_entry_controller');
		if (precost==1)
		{
			alert("Pre Cost Approved, Any Change will be not allowed.");
			release_freezing();
			return;
		}
		// Mandatory Part
		if(po_mandatory_field !=''){
			if (form_validation(po_mandatory_field,po_mandatory_message)==false){
				release_freezing();
				return;
			}
		}

		if(form_validation('update_id*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_avg_price*cbo_deliveryCountry_id*cbo_breakdown_type*cbo_gmtsItem_id*txt_countryShip_date','Master Info*PO Number*PO Received Date*Pub Shipment Date*Avg. Rate Pcs/Set*Country*Break Down Type*Gmts Item*Country Ship Date')==false)
		{
			release_freezing();
			return;   
		}
		else
		{
			if($("#cbo_status").val()!=1 || operation==2)
			{
				var scLc_status = return_ajax_request_value(txt_job_no+"_"+$("#update_id_details").val(), 'sc_lc_status', 'requires/order_entry_controller')
				var ex_scLc_status=scLc_status.split("_");
				if(cbo_status==2) var status="InActive"; else if(cbo_status==3) var status="Cancelled"; else if(operation==2) var status="Delete";
				if(trim(ex_scLc_status[0])!="")
				{
					al_msgsc="";
					var al_msgsc="Sales Contract '"+trim(ex_scLc_status[0])+"' found and donot '"+status+"' Order no,\n If Need Please delete the Sales Contract first.";
					alert(al_msgsc);
					release_freezing();	
					return;
				}
				if(trim(ex_scLc_status[1])!="")
				{
					al_msglc="";
					var al_msglc="Export LC '"+trim(ex_scLc_status[1])+"' found and donot '"+status+"' Order no,\n If Need Please delete the Export LC first.";
					alert(al_msglc);
					release_freezing();	
					return;
				}
			}
			if($("#po_control_booking").val()==1 && operation==1){
				var booking_approval = return_ajax_request_value(txt_job_no+"_"+$("#update_id_details").val(), 'po_wise_booking_approval', 'requires/order_entry_controller');
				if (booking_approval==1)
				{
					alert("Booking Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
			
			var po_update_period=document.getElementById('po_update_period_maintain').value;
			var po_datediff=document.getElementById('txt_po_datedif_hour').value*1;
			var po_update_period=document.getElementById('po_update_period_maintain').value*1;
			var user_id=document.getElementById('txt_user_id').value;
			//alert(po_update_period+'='+po_datediff+'='+po_update_period+'='+user_id); release_freezing(); return;
			
			if(operation==1)
			{
				if(po_update_period!=0){
					var user_id_arr=user_id.split(',');
					if(jQuery.inArray( user , user_id_arr ) == -1)// It will use in Future
					{
						//var cbo_order_status = return_ajax_request_value(po_id, 'get_po_is_confirmed_status', 'requires/woven_order_entry_controller');
						//var txt_order_status=document.getElementById('txt_order_status').value*1;
						if(po_update_period<=po_datediff)//&& txt_order_status==1
						{
							//alert(user_id);
							alert("Update Period Exsits,You are not Allowed to Update.Please take approval of authority and mail to concern person for permission");
							release_freezing();
							return;
						}
					}
				}
			}
			
			
			var txt_quotation_id=(document.getElementById('txt_quotation_id').value)*1;
			var order_uom=$('#cbo_order_uom').val();
			if(order_uom==1){
				var txt_quotation_price=(document.getElementById('txt_quotation_price').value)*1;
			}
			else{
				var set_break_down=$('#set_breck_down').val();
				var item_arr=set_break_down.split('__');
				var txt_quotation_price=(document.getElementById('txt_quotation_price').value)*1/item_arr.length;
			}			
			var txt_avg_price=(document.getElementById('txt_poAmt').value)*1/(document.getElementById('txt_poQty').value)*1;
			var style_smv_source=$('#set_smv_id').val()*1;
			var hid_copy_quotion=$('#hid_copy_quotion').val()*1;
			if(hid_copy_quotion==1 && order_uom==1)
			{
				if( style_smv_source==2 || style_smv_source==7)
				{
					//alert(number_format(txt_quotation_price,6)+'='+number_format(txt_avg_price,6))
					if(number_format(txt_quotation_price,6) > number_format(txt_avg_price,6) ){
						alert("Unit price can not be Less than quoted price");
						release_freezing();	
						return;
					}
				}
			}
			
			var bhstyle=$('#cbo_style_from').val();
			//alert(bhstyle+'=='+number_format(txt_quotation_price,6)+'=='+number_format(txt_avg_price,6))
			if(bhstyle==1)
			{
				var txt_avg_price=(document.getElementById('txt_avg_price').value)*1;
				if(number_format(txt_quotation_price,6) < number_format(txt_avg_price,6) ){
					alert("Unit price can not be Greater than BH price");
					release_freezing();	
					return;
				}
				var bhpo=$('#hidd_bhpo_id').val();
				
				if(bhpo=="" || bhpo==0)
				{
					alert("PO is not found in Buying House");
					release_freezing();	
					return;
				}
			}
			
			if(operation==2)
			{
				var cutting_qty=return_global_ajax_value($("#update_id_details").val()+"_"+$("#cbo_deliveryCountry_id").val(), 'get_cutting_qty_country', '', 'requires/order_entry_controller');
				if(cutting_qty>0)
				{
					alert("Production found; So delete not allowed");
					release_freezing();
					return;
				}
			}
			
			var breakdown_type =$("#cbo_breakdown_type").val();
			if(breakdown_type==4)
			{
				if(form_validation('txt_breakdownGrouping*txt_pcsQty','Pack Type*Pcs Per Pack')==false)
				{
					release_freezing();
					return;   
				}
			}
			if(operation==0)
			{
				var buyer_name=get_dropdown_text('cbo_buyer_name');
				var buyer_id=$("#cbo_buyer_name").val();
				if((buyer_name=='TORAY-GU TRPU' && buyer_id==24) || (buyer_name=='TORAY-N.MATSUYA' && buyer_id==28) || (buyer_name=='TORAY-MUJI' && buyer_id==27))
				{
					alert("This buyer is not availabe for any Style Ref. in ERP. If need Please contract with MIS.");
					release_freezing();
					return;
				}
			}			
			if(breakdown_type==1)
			{
				var full_qty_validation=0;
				var po_id=$("#update_id_details").val();
				var set_breck_down=$("#set_breck_down").val();
				var country_id=$("#cbo_deliveryCountry_id").val();
				var pre_country_id=$("#hid_prev_country").val();
				var gmtsItem_id=$("#cbo_gmtsItem_id").val();
				
				var order_qty=($("#txt_docSheetQty").val()*1);
				var country_po_qty=($("#txt_poQty").val()*1);
				var set_qty=($("#tot_set_qnty").val()*1);
				var previous_ord_qty =0;
				if(po_id!="")
				{
					previous_ord_qty = return_ajax_request_value(po_id+'***'+country_id+'***'+gmtsItem_id+'***'+operation+'***'+pre_country_id, 'full_qty_check_for_validation', 'requires/order_entry_controller')
				}
				
				if(previous_ord_qty!=0)
				{
					var item_ratio_arr = new Array();
					var breck_down_set=set_breck_down.split('__');
					var q=0;
					for(var mn=1; mn<=breck_down_set.length; mn++)
					{
						var ex_set_data=breck_down_set[q].split('_');
						var ex_item_id=ex_set_data[0];
						var ex_item_ratio=ex_set_data[1];
						item_ratio_arr[ex_item_id]=ex_item_ratio;
						q++;
					}
					full_qty_validation=previous_ord_qty/set_qty;
				}
				var tot_country_qty=full_qty_validation+country_po_qty;
				//alert(previous_ord_qty)
				var bal_qty=order_qty-tot_country_qty;
				if(order_qty<tot_country_qty)
				{
					alert ("Order Qty Less Then Total Country Qty ["+ bal_qty +"]");
					release_freezing();
					return;
				}
			}
			//return;
			var delete_po=''; var delete_country='';
			if(operation==2)
			{
				var rr=confirm("You are going to delete country data.\n Are you sure?");
				if(rr==true)
				{
					 delete_country=1;
				}
				else
				{
					delete_country=0;
					release_freezing();	
					return;
				}
			}
			var color_table =$('#color_tbl tbody tr').length-1; 
			var size_table =$('#size_tbl tbody tr').length-1;
			if($('#breakdown_div').html()=='')
			{
				alert("Please Insert Color Size Qty.");
				release_freezing();
				return;
			}
			
			if(($('#txt_poQty').val()*1)==0)
			{
				alert("Please Insert Color Size Qty.");
				release_freezing();
				return;
			}
			
			var tna=check_tna_leadtime();
			var data=tna.split("_");
			var temp=data[0]*1; var tna=data[1]*1; var tna_process=data[3]*1;
			
			if(temp==0 && tna==1 && tna_process==1){
				alert("Order Lead Time not allowed less than available TNA Template's Lead Time. So,Prepare the Template first")
				release_freezing();
				return;
			}
			//release_freezing(); return;
			var sz=1; var data_color=""; var data_size=""; var data_breakdown="";
			for(var i=1; i<=size_table; i++)
			{
				var txtSizeName=encodeURIComponent(""+$('#txtSizeName_'+i).val()+"");
				data_size+="&txtSizeName_" + sz + "='" + txtSizeName+"'"+"&txtSizeId_" + sz + "='" + $('#txtSizeId_'+i).val()+"'";
				sz++;
			}
			//alert (data_size); 
			var z=1;
			if (breakdown_type==1)
			{
				for(var i=1; i<=color_table; i++)
				{
					var txtColorName=encodeURIComponent(""+$('#txtColorName_'+i).val()+"");
					//data_color+=get_submitted_data_string('txtColorName_'+i+'*txtColorId_'+i+'*txt_colorQty_'+i+'*txt_colorAmt_'+i,"../../",2);
					data_color+="&txtColorName_" + z + "='" + txtColorName+"'"+"&txtColorId_" + z + "='" + $('#txtColorId_'+i).val()+"'"+"&txt_colorQty_" + z + "='" + $('#txt_colorQty_'+i).val()+"'"+"&txt_colorAmt_" + z + "='" + $('#txt_colorAmt_'+i).val()+"'";
					var sq=1;
					for(var m=1; m<=size_table; m++)
					{
						if( ($("#txt_colorSizeQty_"+i+'_'+m).val()*1)!=0)
						{
							if (form_validation('txt_colorSizeRate_'+i+'_'+m,'Rate')==false)
							{
								release_freezing();
								return;   
							}
						}
						data_breakdown+="&txt_colorSizeId_"+z+"_"+sq+"='" + $('#txt_colorSizeId_'+i+'_'+m).val()+"'"+"&txt_colorSizeQty_"+z+"_"+sq+"='" + $('#txt_colorSizeQty_'+i+'_'+m).val()+"'"+"&txt_colorSizeRate_"+z+"_"+sq+"='" + $('#txt_colorSizeRate_'+i+'_'+m).val()+"'"+"&txt_colorSizeExCut_"+z+"_"+sq+"='" + $('#txt_colorSizeExCut_'+i+'_'+m).val()+"'"+"&txt_colorSizePLanCut_"+z+"_"+sq+"='" + $('#txt_colorSizePLanCut_'+i+'_'+m).val()+"'"+"&txt_colorSizeArticleNo_"+z+"_"+sq+"='" + $('#txt_colorSizeArticleNo_'+i+'_'+m).val()+"'"+"&txt_assortQty_"+z+"_"+sq+"='" + $('#txt_assortQty_'+i+'_'+m).val()+"'";
						sq++;
					}
					z++;
				}
			}
			else if (breakdown_type==4)
			{
				for(var i=1; i<=color_table; i++)
				{
					var txtColorName=encodeURIComponent(""+$('#txtColorName_'+i).val()+"");
					data_color+="&txtColorName_" + z + "='" + txtColorName+"'"+"&txtColorId_" + z + "='" + $('#txtColorId_'+i).val()+"'"+"&txt_colorQty_" + z + "='" + $('#txt_colorQty_'+i).val()+"'"+"&txt_colorAmt_" + z + "='" + $('#txt_colorAmt_'+i).val()+"'";
					var sq=1;
					for(var m=1; m<=size_table; m++)
					{
						if( ($("#txt_colorSizeQty_"+i+'_'+m).val()*1)!=0)
						{
							if (form_validation('txt_colorSizeRate_'+i+'_'+m,'Rate')==false)
							{
								release_freezing();
								return;   
							}
						}
						data_breakdown+="&txt_colorSizePackQty_"+z+"_"+sq+"='" + $('#txt_colorSizePackQty_'+i+'_'+m).val()+"'"+"&txt_colorSizePcsQty_"+z+"_"+sq+"='" + $('#txt_colorSizePcsQty_'+i+'_'+m).val()+"'"+"&txt_colorSizeId_"+z+"_"+sq+"='" + $('#txt_colorSizeId_'+i+'_'+m).val()+"'"+"&txt_colorSizeQty_"+z+"_"+sq+"='" + $('#txt_colorSizeQty_'+i+'_'+m).val()+"'"+"&txt_colorSizeRate_"+z+"_"+sq+"='" + $('#txt_colorSizeRate_'+i+'_'+m).val()+"'"+"&txt_colorSizeExCut_"+z+"_"+sq+"='" + $('#txt_colorSizeExCut_'+i+'_'+m).val()+"'"+"&txt_colorSizePLanCut_"+z+"_"+sq+"='" + $('#txt_colorSizePLanCut_'+i+'_'+m).val()+"'"+"&txt_colorSizeArticleNo_"+z+"_"+sq+"='" + $('#txt_colorSizeArticleNo_'+i+'_'+m).val()+"'"+"&txt_assortQty_"+z+"_"+sq+"='" + $('#txt_assortQty_'+i+'_'+m).val()+"'";
						sq++;
					}
					z++;
				}
			}
			else 
			{
				for(var i=1; i<=color_table; i++)
				{
					var txtColorName=encodeURIComponent(""+$('#txtColorName_'+i).val()+"");
					data_color+="&txtColorName_" + z + "='" + txtColorName+"'"+"&txtColorId_" + z + "='" + $('#txtColorId_'+i).val()+"'"+"&txt_colorQty_" + z + "='" + $('#txt_colorQty_'+i).val()+"'"+"&txt_colorAmt_" + z + "='" + $('#txt_colorAmt_'+i).val()+"'";
					var sq=1;
					for(var m=1; m<=size_table; m++)
					{
						if( ($("#txt_colorSizeQty_"+i+'_'+m).val()*1)!=0)
						{
							if (form_validation('txt_colorSizeRate_'+i+'_'+m,'Rate')==false)
							{
								release_freezing();
								return;   
							}
						}
						data_breakdown+="&txt_colorSizeId_"+z+"_"+sq+"='" + $('#txt_colorSizeId_'+i+'_'+m).val()+"'"+"&txt_colorSizeQty_"+z+"_"+sq+"='" + $('#txt_colorSizeQty_'+i+'_'+m).val()+"'"+"&txt_colorSizeRate_"+z+"_"+sq+"='" + $('#txt_colorSizeRate_'+i+'_'+m).val()+"'"+"&txt_colorSizeExCut_"+z+"_"+sq+"='" + $('#txt_colorSizeExCut_'+i+'_'+m).val()+"'"+"&txt_colorSizePLanCut_"+z+"_"+sq+"='" + $('#txt_colorSizePLanCut_'+i+'_'+m).val()+"'"+"&txt_colorSizeArticleNo_"+z+"_"+sq+"='" + $('#txt_colorSizeArticleNo_'+i+'_'+m).val()+"'"+"&txt_colorSizeRatioQty_"+z+"_"+sq+"='" + $('#txt_colorSizeRatioQty_'+i+'_'+m).val()+"'"+"&txt_colorSizeRatioRate_"+z+"_"+sq+"='" + $('#txt_colorSizeRatioRate_'+i+'_'+m).val()+"'"+"&txt_colorSizeRatioId_"+z+"_"+sq+"='" + $('#txt_colorSizeRatioId_'+i+'_'+m).val()+"'"+"&txt_assortQty_"+z+"_"+sq+"='" + $('#txt_assortQty_'+i+'_'+m).val()+"'";
						sq++;
					}
					z++;
				}
			}
			
			var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
			var txt_po_no=encodeURIComponent("'"+$('#txt_po_no').val()+"'");
			var po_nodata="&txt_po_no="+txt_po_no+"&txt_style_ref=" + txt_style_ref+"";
			//alert(data_color); release_freezing(); return;
			var data="action=save_update_delete_dtls&operation="+operation+"&color_table="+color_table+"&size_table="+size_table+"&delete_po="+delete_po+"&delete_country="+delete_country+get_submitted_data_string('cbo_breakdown_type*cbo_round_type*cbo_order_status*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_phd*txt_factory_rec_date*txt_avg_price*txt_upCharge*txt_docSheetQty*txt_noOf_carton*cbo_projected_po*cbo_tna_task*txt_grouping*txt_file_no*txt_file_year*cbo_packing_po_level*cbo_delay_for*update_id_details*color_size_break_down*update_id*txt_po_datedif_hour*txt_po_remarks*cbo_packing*copy_id*tot_set_qnty*cbo_status*set_breck_down*cbo_buyer_name*cbo_company_name*txt_sc_lc*hidd_job_id*cbo_gmtsItem_id*cbo_deliveryCountry_id*cbo_code_id*cbo_country_id*cbo_countryCode_id*txt_cutup_date*cbo_cutOff_id*txt_countryShip_date*txt_breakdownGrouping*txt_pcsQty*txt_quotation_id*hidd_bhpo_id*size_all_id',"../../")+data_color+data_size+data_breakdown+po_nodata;
			//alert (data); return;txt_amount
			
			http.open("POST","requires/order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponsedtls;
		}
	}
	 
	function fnc_on_submit_reponsedtls()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			release_freezing();
			// alert(http.responseText)	
			//return;
			if(trim(reponse[0])=='LeadTime')
			{
				alert("Bellow "+ trim(reponse[2]) +" Days Lead Time not allow. If required, please take approval of Marketing Director.");
				release_freezing();
				return;
			}
			if(trim(reponse[0]) ==24)
			{
				alert("Please Tag Image");
				release_freezing();	
				return; 
			}
			if(trim(reponse[0])==12)
			{
				alert("Org. Shipment Date Not Allowed");
				release_freezing();	
				return; 
			}
			if(trim(reponse[0])==13)
			{
				var al_msglc="";
				al_msglc="Booking No : '"+trim(reponse[1])+"' found and donot delete Order no,\n If Need Please delete the Booking first.";
				alert(al_msglc);
				release_freezing();	
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]==17)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==14)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==16)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			//alert(reponse[0]);
			show_msg(trim(reponse[0]));
			if(reponse[0]==15) 
			{
				release_freezing();	 
				setTimeout('fnc_order_entry_details('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2  || reponse[0]==20)
			{
				if(trim(reponse[0]) ==20)
				{
					alert(trim(reponse[1]));
					//return; 
				}
				var countryShip_date=$('#txt_countryShip_date').val();
				var gmtsItem_id=$('#cbo_gmtsItem_id').val();
				var country_id=$('#cbo_deliveryCountry_id').val();
				$('#update_id_details').val(reponse[1]);
				
				$('#txt_job_qty').val(reponse[2]);
				$('#txt_avgUnit_price').val(reponse[3]);
				$('#txt_total_price').val(reponse[4]);
				$('#txt_proj_qty').val(reponse[5]);
				$('#txt_proj_avgUnit_price').val(reponse[6]);
				$('#txt_proj_total_price').val(reponse[7]);
				$('#txt_orginProj_qty').val(reponse[8]);
				$('#txt_orginProj_total_price').val(reponse[9]);
				$('#txt_orginProj_total_amt').val(reponse[10]);
				$('#txt_avg_price').val(reponse[11]);
				/* var breakdown_type =$("#cbo_breakdown_type").val();
				if(breakdown_type==4)
				{
					$('#txt_docSheetQty').val(reponse[11]);
				} */
				$('#cbo_breakdown_type').attr('disabled','disabled');
				
				show_list_view( $('#update_id').val()+"*"+reponse[1]+"*"+$('#cbo_style_from').val()+"*"+$('#txt_quotation_id').val()+"*"+$('#cbo_company_name').val(),'order_listview','po_list_view','requires/order_entry_controller','setFilterGrid(\'tbl_po_list\',-1)');
				show_list_view(reponse[1],'show_po_active_listview','country_po_list_view','requires/order_entry_controller','');
				document.getElementById('copy_id').checked=false;
				$('#copy_id').val(2);
				$('#cbo_deliveryCountry_id').val('');
				
				$('#cbo_code_id').val('');
				$('#cbo_country_id').val('');
				$('#cbo_countryCode_id').val('');
				$('#txt_breakdownGrouping').val('');
				$('#txt_pcsQty').val('');
				
				if(reponse[0]==2)
				{
					fnc_resetPoDtls(0);
					$('#txt_po_no').val('');
					$('#update_id_details').val('');
					$('#cbo_gmtsItem_id').val('');
					$('#txt_cutup_date').val('');
					$('#cbo_cutOff_id').val('');
					$('#txt_countryShip_date').val('');
					$('#color_size_break_down_all_data').val('');
					$('#color_size_ratio_data').val('');
					if(reponse[1]==1)
					{
						reset_form('orderentry_2','','','','');
					}
				}
				if(reponse[0]==2)
				{
					reset_form('orderentry_2','','','','fnc_resetPoDtls()');
				}
				
				set_button_status(0, permission, 'fnc_order_entry_details',2);
			}
			release_freezing();
		}
	}

	function get_details_form_data(id,type,path)
	{
		reset_form('','','cbo_order_status*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_factory_rec_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task','','');
		get_php_form_data( id, type, path );
	}

	function set_tna_task()
	{
		//check_tna_leadtime();
		var txt_po_received_date=document.getElementById('txt_po_received_date').value;
		var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		//alert(txt_po_received_date+'=='+txt_pub_shipment_date)
		var datediff = date_compare(txt_po_received_date,txt_pub_shipment_date);//date_diff('d', txt_po_received_date, txt_pub_shipment_date);
		//alert(datediff);
		if(datediff==false)
		{
			alert("PO Received Date Is Greater Than Shipment Date.");
			$('#txt_pub_shipment_date').val("");
			return;
		}
		
		load_drop_down( 'requires/order_entry_controller', txt_po_received_date+"_"+txt_pub_shipment_date+"_"+cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td');
		$('#txt_cutup_date').val( txt_pub_shipment_date );
		$('#txt_countryShip_date').val( txt_pub_shipment_date );
		//alert(txt_factory_rec_date);
		if($('#txt_org_shipment_date').val()=="") $('#txt_org_shipment_date').val(txt_pub_shipment_date);
		if($('#txt_factory_rec_date').val()=="") $('#txt_factory_rec_date').val(txt_po_received_date);
		/*var pub_shipment_date=$('#txt_pub_shipment_date').val();
		var shipment_date=$('#txt_org_shipment_date').val();
		
		if(pub_shipment_date == '')
		{
			alert("Publish Shipment Date Can Not Be Null");
			$('#txt_org_shipment_date').val("");
			return;
		}
		if(date_compare(pub_shipment_date,shipment_date) === false){
			alert("Shipment date can not be less then Publish shipment date");
			$('#txt_org_shipment_date').val(' ');
		}*/
	}
	
	function check_tna_leadtime(){
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		var txt_po_received_date=document.getElementById('txt_po_received_date').value
		var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value
		var cbo_company_name=$('#cbo_company_name').val();
		var tna=return_global_ajax_value(buyer_id+"_"+cbo_company_name+'_'+txt_po_received_date+'_'+txt_pub_shipment_date, 'check_tna_leadtime', '', 'requires/order_entry_controller');
		return tna;
	}

	function pop_entry_actual_po()//ISD-21-02141
	{
		var po_id = $('#update_id_details').val();
		var txt_job_no = $('#txt_job_no').val();
		var gmts_item = $('#item_id').val();
		var po_quantity = $('#txt_po_quantity').val();
		var job_id = $('#hidd_job_id').val();
		var po_rcv_date = $('#txt_po_received_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var act_po_id = $('#act_po_id').val();
		if(po_id=="")
		{
			alert("Save The PO First");
			return;
		}
		var action="";
		if(act_po_id==1){
			action="actual_po_info_popup";
		}
		else{
			action="actual_po_info_popup_v1";
		}
		var page_link='requires/order_entry_controller.php?action='+action+'&po_id='+po_id+'&txt_job_no='+txt_job_no+'&gmts_item='+gmts_item+'&po_quantity='+po_quantity+'&job_id='+job_id+'&rcv_date='+po_rcv_date+'&cbo_company_name='+cbo_company_name+'&act_po_id='+act_po_id;
		var title='Actual Po Entry Info';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=550px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		}
	}
	//Dtls Form End -------------------------------------------------------------------------------------

	function po_recevied_date()
	{
		var po_current_date_maintain=$('#po_current_date_maintain').val();
		var order_status=$('#cbo_order_status').val();
		var current_date='<? echo date('d-m-Y'); ?>'
		if(po_current_date_maintain==1 && order_status==1)
		{
			$('#txt_po_received_date').attr('disabled','disabled');
			$('#txt_po_received_date').val(current_date);
		}
		else
		{
			$('#txt_po_received_date').val(current_date);	
			$('#txt_po_received_date').removeAttr('disabled','disabled');
		}
	}
	
	//var counter=1;
	function append_color_size_row(type)
	{
		if(type==1)
		{
			var color_from_lib=$('#hidd_color_from_lib').val();
			var counter =$('#color_tbl tbody tr').length; 
			if(counter>=1) counter++;
			else if (counter<1) counter=1;
			var z=1;
			for(var i=1;i<=counter;i++)
			{
				/*$("#txtColorName_"+i).autocomplete({
					source: str_color
				});*/
				
				$( "#txtColorName_"+i ).autocomplete({
					 source: function( request, response ) {
						  var matcher =  new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
						  response( $.grep( str_color, function( item ){
							  return matcher.test( item );
						  }) );
					  }
				});
				
				if($("#txtColorName_"+i).val()=="")
				{
					z++;
				}
			}
			//alert(z)
			if(z==1)
			{
				$('#color_tbl tbody').append(
					'<tr id="trColor_'+counter+'">'
					+ '<td id="td_'+counter+'" align="center" >'+counter+'</td><td align="center" ><input type="text" name="txtColorName[]" class="text_boxes" id="txtColorName_'+counter+'"  style="width:80px;" onKeyUp="append_color_size_row(1)" /><input type="hidden" name="txtColorId[]" class="text_boxes" id="txtColorId_'+counter+'"  style="width:50px;" /></td>'+ '</tr>'
				);
				
				if(color_from_lib==1)
				{
					$('#txtColorName_'+counter).attr('readonly',true);
					$('#txtColorName_'+counter).attr('placeholder','Browse');
					$('#txtColorName_'+counter).removeAttr("onDblClick").attr("onDblClick","color_select_popup("+counter+")");
					//if( e.which==39 )
					if( $('#txtColorName_'+counter).is('[readonly]') ){
						$('#txtColorName_'+counter).keyup( function(e){
							if( e.which == 46 ) { $("#txtColorName_"+counter).val(''); fnc_remove_row(type);} else { return false; }
						});
					}
				}
				else 
				{
					$('#txtColorName_'+counter).attr('readonly',false);
					$('#txtColorName_'+counter).attr('placeholder','Write');
					$('#txtColorName_'+counter).removeAttr('onDblClick','onDblClick');	
				}
			}
		}
		else if (type==2)
		{
			var counter =$('#size_tbl tbody tr').length; 
			
			if(counter>=1) counter++;
			else if (counter<1) counter=1;
			var x=1;
			for(var i=1; i<=counter-1; i++)
			{
				/*$("#txtSizeName_"+i).autocomplete({
					source:  str_size 
				});*/
				$( "#txtSizeName_"+i ).autocomplete({
					 source: function( request, response ) {
						  var matcher =  new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
						  response( $.grep( str_size, function( item ){
							  return matcher.test( item );
						  }) );
					  }
				});
				
				if($("#txtSizeName_"+i).val()=="")
				{
					x++;
				}
			}
			if(x==1)
			{
				$('#size_tbl tbody').append(
					'<tr id="trSize_'+counter+'">'
					+ '<td align="center"><input type="text" name="txtSizeName[]" class="text_boxes" id="txtSizeName_'+counter+'" style="width:80px;" onKeyUp="append_color_size_row(2)" /><input type="hidden" name="txtSizeId[]" class="text_boxes" id="txtSizeId_'+counter+'" style="width:50px;" /></td>'+ '</tr>'
				);
			}
		}
		fnc_remove_row(type);
	}
	
	function fnc_remove_row(type)
	{
		if (type==1)
		{
			var color_from_lib=$('#hidd_color_from_lib').val();
			var counter =$('#color_tbl tbody tr').length; 
			
			for(var i=1; i<=counter-1; i++)
			{
				if($("#txtColorName_"+i).val()=="")
				{
					var index=i-1;
					$("table#color_tbl tbody tr:eq("+index+")").remove();
				}
				var titel=$('#txtColorName_'+i).val();
				//alert (titel);
				$('#txtColorName_'+i).attr('title',titel);
			}
			var numRow = $('table#color_tbl tbody tr').length;
			for(var i=1; i<=counter-1; i++)
			{
				var index=i-1;
				$("#color_tbl  tbody tr:eq("+index+")").find("input").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					});
					//alert(index)
				});
				
				$("#color_tbl tbody tr:eq("+index+")").each(function(){
					$(this).find('td:first').html(i);
					if(color_from_lib==1) { $('#txtColorName_'+i).removeAttr("onDblClick").attr("onDblClick","color_select_popup("+i+")"); }
				});
			}
		}
		else if (type==2)
		{
			var counter =$('#size_tbl tbody tr').length; 
			
			var numRow = $('table#size_tbl tbody tr').length;
			
			for(var i=1; i<=counter-1; i++)
			{
				if($("#txtSizeName_"+i).val()=="")
				{
					var index=i-1;
					$("table#size_tbl tbody tr:eq("+index+")").remove()
				}
			}
			
			var numRow = $('table#size_tbl tbody tr').length;
			for(var i=1; i<=counter-1; i++)
			{
				var index=i-1;
				$("#size_tbl  tbody tr:eq("+index+")").find("input").each(function() {
					$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					}); 
				})
			}
		}
	}
	
	function fnc_create_qty_breakdown(is_update,type)
	{
		if(form_validation('txt_job_no*txt_avg_price','Master Part is not save*Rate')==false)
		{
			return;   
		}
		if(type==4)
		{
			if(form_validation('txt_pcsQty','Pcs Per Pack')==false)
			{
				return;   
			}
		}
		var copy_excut=$('#copy_excut').val();
		if(copy_excut==1){
			document.getElementById("copy_excut").checked = false;
			$('#copy_excut').val(2);
		}
		$('#txt_avg_price').attr('disabled',false);
		
		var html="";
		var size_table =$('#size_tbl tbody tr').length;
		var color_table =$('#color_tbl tbody tr').length; 
		
		var color_value = new Array();
		var j=1;
		for(var i=1;i<=color_table;i++)
		{
			var color_name=$("#txtColorName_"+i).val().toUpperCase();	
			var color_name=color_name.trim();		
			if(color_name!="")
			{
				if(jQuery.inArray(color_name, color_value) != -1) {
					$("#txtColorName_"+i).val('');
					alert("Duplicate Color Not Allowed");
					//append_color_size_row(1);
					
				} else {
				    color_value[j]=color_name;
					j++;
				}
			}
		}

		var size_value = new Array();
		var k=1;
		for(var i=1;i<=size_table;i++)
		{
			var size_name=$("#txtSizeName_"+i).val().toUpperCase();
			var size_name=size_name.trim();
			if(size_name!="")
			{
				if(jQuery.inArray(size_name, size_value) != -1) {
					$("#txtSizeName_"+i).val('');
					alert("Duplicate Size Not Allowed");
					//append_color_size_row(2);					
				} else {
					size_value[k]=size_name;
					k++;
				}
		   }
		}
		 
		//color_value.sort();
		var cbo_order_uom=$('#cbo_order_uom').val();
		var copyquoid=$('#hid_copy_quotion').val();
		var costsource=$('#hid_cost_source').val();
		var excut_source=$('#hid_excessCut_source').val()*1;
		var disable_fld=""; var excutdisable="";
		//release_freezing();
		if(excut_source==3) excutdisable="disabled"; else excutdisable="";
		
		//release_freezing(); return;
		if(type==1 || type==4)
		{
			var all_data=$("#color_size_break_down_all_data").val();
			//alert (all_data);
			var ex_all_data="";
			ex_all_data=all_data.split('___');
			var qty_readonly=""; var qty_static_val_send='';
			if (type==1)
			{
				var data_arr_id = new Array(); 
				var data_arr_qty = new Array(); 
				var data_arr_rate = new Array(); 
				var data_arr_excut = new Array(); 
				var data_arr_artno = new Array(); 
				var data_arr_plancut= new Array(); 
				var data_arr_assort= new Array(); 
				
				var k=0;
				for (var i = 1; i <= ex_all_data.length; i++)
				{
					var ex_data="";
					var ex_data=ex_all_data[k].split('**');
					//alert(ex_data[4]);
					var index_id=eval('"' + ex_data[1]+'_'+ex_data[2] + 'id'+'"');
					var index_qty=eval('"' + ex_data[1]+'_'+ex_data[2] + 'qty'+'"');
					var index_rate=eval('"' + ex_data[1]+'_'+ex_data[2] + 'rate'+'"');
					var index_excut=eval('"' + ex_data[1]+'_'+ex_data[2] + 'excut'+'"');
					var index_artno=eval('"' + ex_data[1]+'_'+ex_data[2] + 'artno'+'"');
					var index_plancut=eval('"' + ex_data[1]+'_'+ex_data[2] + 'plancut'+'"');
					var index_assort=eval('"' + ex_data[1]+'_'+ex_data[2] + 'assort'+'"');
					data_arr_id[index_id]=ex_data[0];
					data_arr_qty[index_qty]=ex_data[3];
					data_arr_rate[index_rate]=ex_data[4]*1;
					data_arr_excut[index_excut]=ex_data[5];
					data_arr_artno[index_artno]=ex_data[6];
					data_arr_plancut[index_plancut]=ex_data[7];
					data_arr_assort[index_assort]=ex_data[8];
					//console.log('1562-'+index_excut+'--'+ex_data[5]);
					k++;
				}
				qty_readonly="";
				qty_static_val_send=1;
			}
			else if (type==4)
			{
				var data_arr_pack = new Array(); 
				var data_arr_pcs = new Array();
				var data_arr_id = new Array(); 
				var data_arr_qty = new Array(); 
				var data_arr_rate = new Array(); 
				var data_arr_excut = new Array(); 
				var data_arr_artno = new Array(); 
				var data_arr_plancut= new Array(); 
				var data_arr_assort= new Array(); 
			
				var k=0;
				for (var i = 1; i <= ex_all_data.length; i++)
				{
					var ex_data="";
					var ex_data=ex_all_data[k].split('**');
					
					var index_pack=eval('"' + ex_data[1]+'_'+ex_data[2] + 'pack'+'"');
					var index_pcs=eval('"' + ex_data[1]+'_'+ex_data[2] + 'pcs'+'"');
	
					var index_id=eval('"' + ex_data[1]+'_'+ex_data[2] + 'id'+'"');
					var index_qty=eval('"' + ex_data[1]+'_'+ex_data[2] + 'qty'+'"');
					var index_rate=eval('"' + ex_data[1]+'_'+ex_data[2] + 'rate'+'"');
					var index_excut=eval('"' + ex_data[1]+'_'+ex_data[2] + 'excut'+'"');
					var index_artno=eval('"' + ex_data[1]+'_'+ex_data[2] + 'artno'+'"');
					var index_plancut=eval('"' + ex_data[1]+'_'+ex_data[2] + 'plancut'+'"');
					var index_assort=eval('"' + ex_data[1]+'_'+ex_data[2] + 'assort'+'"');
					
					data_arr_pack[index_pack]=ex_data[8];
					data_arr_pcs[index_pcs]=ex_data[9];

					data_arr_id[index_id]=ex_data[0];
					data_arr_qty[index_qty]=ex_data[3];
					data_arr_rate[index_rate]=ex_data[4];
					data_arr_excut[index_excut]=ex_data[5];
					data_arr_artno[index_artno]=ex_data[6];
					data_arr_plancut[index_plancut]=ex_data[7];
					data_arr_assort[index_assort]=ex_data[10];
					k++;
				}
				qty_readonly="readonly";
				qty_static_val_send=5;
			}
			freeze_window(5); 
			//alert (data_arr_packqty)
			$('#breakdown_div').html('');
			html += '<div style="width:750px;max-height:370px; overflow-y:scroll"><table cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all" align="left" id="breakdown_tbl"><thead><th width="90px">Color/Size</th><th>Particulars</th>';
			for (var i = 1; i < size_value.length; i++)
			{
				html += '<th>'+size_value[i]+'</th>'; 
			}
			
			html += '<th>Total Qty</th><th>Total Amount</th><th>Total Plan Cut Qty.</th></thead><tbody>';
			for (var i = 1; i < color_value.length; i++)
			{
				if (type==1)
				{
					html += '<tr id="'+color_value[i]+'"><td width="90px" rowspan="5"><strong>'+color_value[i]+'</strong></td><td width="70px">Qty.</td>';
				}
				else
				{
					html += '<tr id="'+color_value[i]+'"><td width="90px" rowspan="7"><strong>'+color_value[i]+'</strong></td><td width="70px">Pack Qty.</td>';
					
					for (var m = 1; m < size_value.length; m++){
						var index_packQty=eval('"' + color_value[i]+'_'+size_value[m] + 'pack'+'"');
						var packqty="";
						if(typeof(data_arr_pack[index_packQty])=="undefined") packqty=""; else packqty=data_arr_pack[index_packQty];
						html +='<td width="55px"><input name="txt_colorSizePackQty_[]" id="txt_colorSizePackQty_'+i+'_'+m+'" value="'+packqty+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,5); fnc_copy_qty_excut('+i+','+m+',this.value,5);" /></td>';
					}
					html += '<td width="60px" rowspan="7"><input type="text" name="txt_colorQty_[]" id="txt_colorQty_'+i+'" onDblClick="openmypage_ultimate_pop(this.value,'+i+','+"'"+color_value[i]+"'"+');" class="text_boxes_numeric" style="width:50px;" readonly /></td><td width="70px" rowspan="7"><input type="text" name="txt_colorAmt_[]" class="text_boxes_numeric" id="txt_colorAmt_'+i+'" style="width:50px;" readonly /></td><td width="70px" rowspan="5"><input type="text" name="txt_totPlanCutAmt_[]" class="text_boxes_numeric" id="txt_totPlanCutAmt_'+i+'" style="width:50px;" readonly /></td></tr><tr><td width="80px">Pcs Per Pack</td>';
					
					for (var m = 1; m < size_value.length; m++){
						var index_pcsQty=eval('"' + color_value[i]+'_'+size_value[m] + 'pcs'+'"');
						var pcsqty="";
						if(typeof(data_arr_pcs[index_pcsQty])=="undefined") pcsqty=""; else pcsqty=data_arr_pcs[index_pcsQty];
						html +='<td width="55px"><input name="txt_colorSizePcsQty_[]" id="txt_colorSizePcsQty_'+i+'_'+m+'" value="'+pcsqty+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,6); fnc_copy_qty_excut('+i+','+m+',this.value,6);" /></td>';
					}
					html += '<td>&nbsp</td></tr><tr><td width="80px">Qty.</td>';
				}
				
				for (var m = 1; m < size_value.length; m++){
					var index_qty=eval('"' + color_value[i]+'_'+size_value[m] + 'qty'+'"');
					var index_asst=eval('"' + color_value[i]+'_'+size_value[m] + 'assort'+'"');
					var qty=""; var asst="";
					if(typeof(data_arr_qty[index_qty])=="undefined") qty=""; else qty=data_arr_qty[index_qty];
					if(typeof(data_arr_assort[index_asst])=="undefined") asst=""; else asst=data_arr_assort[index_asst];
					html +='<td width="55px"><input name="txt_colorSizeQty_[]" id="txt_colorSizeQty_'+i+'_'+m+'" value="'+qty+'" saved_po_quantity="'+qty+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,'+qty_static_val_send+'); fnc_copy_qty_excut('+i+','+m+',this.value,'+qty_static_val_send+'); fnc_po_qty_validat_co_si( '+i+','+m+' ); " onDblClick="fnc_assortment_pop_up('+"'"+color_value[i]+"'"+','+"'"+size_value[m]+"'"+','+i+','+m+',this.value);" '+qty_readonly+' /><input name="txt_assortQty_[]" id="txt_assortQty_'+i+'_'+m+'" value="'+asst+'" class="text_boxes" style="width:50px;" type="hidden" /></td>';
				}
				
				if (type==1)
				{
					html += '<td width="60px" rowspan="5"><input type="text" name="txt_colorQty_[]" id="txt_colorQty_'+i+'" onDblClick="openmypage_ultimate_pop(this.value,'+i+','+"'"+color_value[i]+"'"+');" class="text_boxes_numeric" style="width:50px;" readonly /></td><td width="70px" rowspan="5"><input type="text" name="txt_colorAmt_[]" class="text_boxes_numeric" id="txt_colorAmt_'+i+'" style="width:50px;" readonly /></td><td>&nbsp</td></tr><tr><td width="80px">Rate</td>';
				}
				else
				{
					html += '<td>&nbsp</td></tr><tr><td width="80px">Rate</td>';
				}
				
				for (var m = 1; m < size_value.length; m++){
					var index_id=eval('"' + color_value[i]+'_'+size_value[m] + 'id'+'"');
					var index_rate=eval('"' + color_value[i]+'_'+size_value[m] + 'rate'+'"');
					//alert(data_arr_rate[index_rate]);
					var current_rate = document.getElementById('txt_avg_price').value;
					var id=''; var rate="";
					if(typeof(data_arr_id[index_id])=="undefined") id=''; else id=data_arr_id[index_id];
					//if(typeof(data_arr_rate[index_rate])=="undefined") rate=""; else rate=number_format(data_arr_rate[index_rate],4);
					if(typeof(data_arr_rate[index_rate])=="undefined") rate=number_format(current_rate,4); else rate=number_format(data_arr_rate[index_rate],4);
					html+='<td width="55px"><input type="hidden" name="txt_colorSizeId_[]" id="txt_colorSizeId_'+i+'_'+m+'" value="'+id+'" class="text_boxes_numeric" style="width:50px;" /><input type="text" name="txt_colorSizeRate_[]" id="txt_colorSizeRate_'+i+'_'+m+'" value="'+rate+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,2)" '+disable_fld+'/></td>';
				}
				html += '<td>&nbsp</td></tr><tr><td width="80px">Ex. Cut %</td>';
				
				for (var m = 1; m < size_value.length; m++){
					var index_excut=eval('"' + color_value[i]+'_'+size_value[m] + 'excut'+'"');
					var excut="";
					if(typeof(data_arr_excut[index_excut])=="undefined") excut=""; else excut=data_arr_excut[index_excut];//alert(excut);
					html+='<td width="55px"><input type="text" name="txt_colorSizeExCut_[]" id="txt_colorSizeExCut_'+i+'_'+m+'" value="'+excut+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,3); fnc_copy_qty_excut('+i+','+m+',this.value,2);" '+excutdisable+' /></td>';
				}
				html += '<td>&nbsp</td></tr><tr><td width="80px">Plan Cut Qty.</td>';
				
				for (var m = 1; m < size_value.length; m++){
					var index_plancut=eval('"' + color_value[i]+'_'+ size_value[m] + 'plancut'+'"');
					var plancut="";
					if(typeof(data_arr_plancut[index_plancut])=="undefined") plancut=""; else plancut=data_arr_plancut[index_plancut];
					html+='<td width="55px"><input type="text" name="txt_colorSizePlanCut_[]" id="txt_colorSizePLanCut_'+i+'_'+m+'" value="'+plancut+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,3); fnc_copy_qty_excut('+i+','+m+',this.value,3);" /></td>';
				}
				html += '<td width="70px"><input type="text" name="txt_totPlanCutAmt_[]" class="text_boxes_numeric" id="txt_totPlanCutAmt_'+i+'" style="width:50px;" readonly /></td></tr><tr><td width="80px">Article No</td>';
				
				for (var m = 1; m < size_value.length; m++){
					var index_artno=eval('"' + color_value[i]+'_'+size_value[m] + 'artno'+'"');
					var artno='';
					if(typeof(data_arr_artno[index_artno])=="undefined") artno=''; else artno=data_arr_artno[index_artno];
					html+='<td width="55px"><input type="text" name="txt_colorSizeArticleNo_[]" id="txt_colorSizeArticleNo_'+i+'_'+m+'" value="'+artno+'" class="text_boxes" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,4)"/></td>';
				}
				
				html += '<td>&nbsp</td></tr>';
			}

			html += '</tbody><tfoot><tr><td></td><td><strong>Total : </strong></td>';
			for (var m = 1; m < size_value.length; m++){
				html+='<td width="55px"><input type="text" name="txt_sizeQty_[]" id="txt_sizeQty_'+m+'" class="text_boxes_numeric" style="width:50px;" readonly /></td>';
			}
			
			html += '<td width="80px"><input type="text" name="txt_poQty" class="text_boxes_numeric" id="txt_poQty" style="width:50px;" readonly /></td><td width="80px"><input type="text" name="txt_poAmt" class="text_boxes_numeric" id="txt_poAmt" style="width:50px;" readonly /></td><td width="80px"><input type="text" name="txt_totplancut" class="text_boxes_numeric" id="txt_totplancut" style="width:50px;" readonly /></td></tr></tfoot></div></table>';
			
			$("#breakdown_div").append(html);
			//release_freezing();
			// alert(is_update+'A');
			if(is_update==0) 
			{
				set_button_status(0, permission, 'fnc_order_entry_details',2); 
				fnc_calAmountQty_ex();
				fnc_calculateRate(type,is_update); 
			}
			else if(is_update==1) 
			{
				set_button_status(1, permission, 'fnc_order_entry_details',2);
				fnc_calAmountQty_ex();
				fnc_calculateRate(type,is_update);
				
			}
			fnc_copy_qty_excut(0,0,0,2);
		}
		else if(type==2 || type==3)
		{
			var ratio_all_data=$("#color_size_ratio_data").val();
			var ex_ratio_all_data="";
			ex_ratio_all_data=ratio_all_data.split('___');
			
			var ratio_id_data_arr = new Array(); 
			var ratio_qty_data_arr = new Array(); 
			var ratio_data_rate_arr = new Array(); 
			
			var k=0;
			for (var i = 1; i <= ex_ratio_all_data.length; i++)
			{
				var ex_data_ratio="";
				var ex_data_ratio=ex_ratio_all_data[k].split('**');
				var index_ratio_id=eval('"' + ex_data_ratio[1]+'_'+ex_data_ratio[2] + 'id'+'"');
				var index_ratio_qty=eval('"' + ex_data_ratio[1]+'_'+ex_data_ratio[2] + 'qty'+'"');
				var index_ratio_rate=eval('"' + ex_data_ratio[1]+'_'+ex_data_ratio[2] + 'rate'+'"');
				
				ratio_id_data_arr[index_ratio_id]=ex_data_ratio[0];
				ratio_qty_data_arr[index_ratio_qty]=ex_data_ratio[3];
				ratio_data_rate_arr[index_ratio_rate]=ex_data_ratio[4];
				k++;
			}
			
			$('#breakdownratio_div').html('');
			$('#breakdown_div').html('');
			html += '<table cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all" align="left" id="breakdownRatio_tbl"><thead><th width="90px">Color/Size</th><th>Particulars</th>';
			for (var i = 1; i < size_value.length; i++)
			{
				html += '<th>'+size_value[i]+'</th>'; 
			}

			html += '<th>Total Qty</th><th>Total Amount</th><th>Total Plan Cut Qty.</th></thead><tbody>';
			for (var i = 1; i < color_value.length; i++)
			{
				html += '<tr id="'+color_value[i]+'"><td width="90px" rowspan="2"><strong>'+color_value[i]+'</strong></td><td width="70px">Qty.</td>';
				for (var m = 1; m < size_value.length; m++){
					var index_ratio_qty=eval('"' + color_value[i]+'_'+size_value[m] + 'qty'+'"');
					var qty_ratio="";
					if(typeof(ratio_qty_data_arr[index_ratio_qty])=="undefined") qty_ratio=""; else qty_ratio=ratio_qty_data_arr[index_ratio_qty];
					html +='<td width="55px"><input name="txt_colorSizeRatioQty_[]" id="txt_colorSizeRatioQty_'+i+'_'+m+'" value="'+qty_ratio+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_ratioQtyRate(this.value,1);" /></td>';
				}
				html += '<td width="60px" rowspan="2"><input type="text" name="txt_colorRatioQty_[]" id="txt_colorRatioQty_'+i+'" class="text_boxes_numeric" style="width:50px;" readonly /></td><td width="70px" rowspan="2"><input type="text" name="txt_colorRatioAmt_[]" class="text_boxes_numeric" id="txt_colorRatioAmt_'+i+'" style="width:50px;" readonly /></td></tr><tr><td width="80px">Rate</td>';
				
				for (var m = 1; m < size_value.length; m++){
					var index_ratio_id=eval('"' + color_value[i]+'_'+size_value[m] + 'id'+'"');
					var index_ratio_rate=eval('"' + color_value[i]+'_'+size_value[m] + 'rate'+'"');
					
					var id_ratio=''; var rate_ratio="";
					if(typeof(ratio_id_data_arr[index_ratio_id])=="undefined") id_ratio=''; else id_ratio=ratio_id_data_arr[index_ratio_id];
					if(typeof(ratio_data_rate_arr[index_ratio_rate])=="undefined") rate_ratio=""; else rate_ratio=ratio_data_rate_arr[index_ratio_rate];
					//alert (rate_ratio)
					html+='<td width="55px"><input type="hidden" name="txt_colorSizeRatioId_[]" id="txt_colorSizeRatioId_'+i+'_'+m+'" value="'+id_ratio+'" class="text_boxes_numeric" style="width:50px;" /><input type="text" name="txt_colorSizeRatioRate_[]" id="txt_colorSizeRatioRate_'+i+'_'+m+'" value="'+rate_ratio+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_ratioQtyRate(this.value,2);" '+disable_fld+' /></td>';
				}
				html += '</tr>';
			}

			html += '</tbody><tfoot><tr><td><input type="button" id="calculate" name="btn" value="Calculate" class="formbuttonplasminus" style="width:75px;" onClick="fnc_ratioBreakDown('+is_update+','+type+')" /></td><td><strong>Total : </strong></td>';
			for (var m = 1; m < size_value.length; m++){
				html+='<td width="55px"><input type="text" name="txt_sizeRatioQty_[]" id="txt_sizeRatioQty_'+m+'" class="text_boxes_numeric" style="width:50px;" readonly /></td>';
			}
			
			html += '<td width="80px"><input type="text" name="txt_poRatioQty" class="text_boxes_numeric" id="txt_poRatioQty" style="width:50px;" readonly /></td><td width="80px"><input type="text" name="txt_poRatioAmt" class="text_boxes_numeric" id="txt_poRatioAmt" style="width:50px;" readonly /></td><td>&nbsp;</td></tr></tfoot></table>';
			//freeze_window(5);
			$("#breakdownratio_div").append(html);
			if(is_update==1)
			{
				fnc_ratioQtyRate(0,0);
				fnc_ratioBreakDown(is_update,2);
				
			}
			fnc_calculateRate(type,is_update);
		}
		set_all_onclick();
		
		$('#cbo_breakdown_type').attr('disabled','disabled');
		
		release_freezing();
	}
	
	function fnc_ratioBreakDown( is_update, type )
	{
		var all_data=$("#color_size_break_down_all_data").val();
		var ex_all_data="";
		ex_all_data=all_data.split('___');
		var data_arr_id = new Array(); 
		var data_arr_qty = new Array(); 
		var data_arr_rate = new Array(); 
		var data_arr_excut = new Array(); 
		var data_arr_artno = new Array(); 
		var data_arr_plancut= new Array(); 
		var data_arr_assort= new Array(); 
		
		var k=0;
		for (var i = 1; i <= ex_all_data.length; i++)
		{
			var ex_data="";
			var ex_data=ex_all_data[k].split('**');
			var index_id=eval('"' + ex_data[1]+'_'+ex_data[2] + 'id'+'"');
			var index_qty=eval('"' + ex_data[1]+'_'+ex_data[2] + 'qty'+'"');
			var index_rate=eval('"' + ex_data[1]+'_'+ex_data[2] + 'rate'+'"');
			var index_excut=eval('"' + ex_data[1]+'_'+ex_data[2] + 'excut'+'"');
			var index_artno=eval('"' + ex_data[1]+'_'+ex_data[2] + 'artno'+'"');
			var index_plancut=eval('"' + ex_data[1]+'_'+ex_data[2] + 'plancut'+'"');
			var index_assort=eval('"' + ex_data[1]+'_'+ex_data[2] + 'assort'+'"');
			data_arr_id[index_id]=ex_data[0];
			data_arr_qty[index_qty]=ex_data[3];
			data_arr_rate[index_rate]=ex_data[4];
			data_arr_excut[index_excut]=ex_data[5];
			data_arr_artno[index_artno]=ex_data[6];
			data_arr_plancut[index_plancut]=ex_data[7];
			data_arr_assort[index_assort]=ex_data[8];
			//alert(data_arr_plancut[index_plancut]);
			k++;
		}
		
		var color_table =$('#color_tbl tbody tr').length; 
		var size_table =$('#size_tbl tbody tr').length;
		
 		var color_value = new Array();
		var j=1;
		for(var i=1;i<=color_table;i++)
		{
			if($("#txtColorName_"+i).val()!="")
			{
				color_value[j]=$("#txtColorName_"+i).val().toUpperCase();
				j++;
			}
		}
		
		var size_value = new Array();
		var k=1;
		for(var i=1;i<=size_table;i++)
		{
			if($("#txtSizeName_"+i).val()!="")
			{
				size_value[k]=$("#txtSizeName_"+i).val().toUpperCase();
				k++;
			}
		}	
		var cbo_order_uom=$('#cbo_order_uom').val();
		var disable_fld="";
		if(cbo_order_uom==58)
		{
			disable_fld="disabled";
		}
		else
		{
			disable_fld="";
		}
		var docSheetQty=$("#txt_docSheetQty").val()*1;
		var roundType=$("#cbo_round_type").val();
		var totCtnQty=$("#txt_poRatioQty").val()*1;
		var gmtsCtnQty=0; var isDecimel=0;
		if(type==3)
		{
			gmtsCtnQty=docSheetQty/totCtnQty*1;
			if(gmtsCtnQty % 1 === 0)
			{
				//isDecimel=1;
				$("#txt_noOf_carton").val( gmtsCtnQty );
			}
			else 
			{
				//isDecimel=2;
				var noOfCtnQty=0;
				if(roundType==1)
				{
					noOfCtnQty=Math.ceil(gmtsCtnQty);
					$("#txt_noOf_carton").val( noOfCtnQty );
				}
				else if(roundType==2)
				{
					noOfCtnQty=Math.floor(gmtsCtnQty);
					$("#txt_noOf_carton").val( noOfCtnQty );
				}
				else
				{
					$("#txt_noOf_carton").val( gmtsCtnQty );
				}
			}
		}
		var ctnQty=$("#txt_noOf_carton").val()*1;
		var ratioQty_arr=new Array; var ratioExCut_arr=new Array; var excutdisable="";
		var company_name=$('#cbo_company_name').val(); 
		var buyer_name=$('#cbo_buyer_name').val();
		var excut_source=$('#hid_excessCut_source').val()*1;
		if(excut_source==3) excutdisable="disabled"; else excutdisable="";
		
		var html="";
		if(type==2 || type==3)
		{
			$('#breakdown_div').html('');
			html += '<table cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all" align="left" id="breakdown_tbl"><thead><th width="90px">Color/Size</th><th>Particulars</th>';
			for (var i = 1; i <size_value.length; i++)
			{
				html += '<th>'+size_value[i]+'</th>'; 
			}

			html += '<th>Total Qty</th><th>Total Amount</th><th>Total Plan Cut Qty.</th></thead><tbody>';
			for (var i = 1; i < color_value.length; i++)
			{
				html += '<tr id="'+color_value[i]+'"><td width="90px" rowspan="5"><strong>'+color_value[i]+'</strong></td><td width="70px">Qty.</td>';
				for (var m = 1; m <size_value.length; m++){
					var index_qty=eval('"' + color_value[i]+'_'+size_value[m] + 'qty'+'"');
					var index_asst=eval('"' + color_value[i]+'_'+size_value[m] + 'assort'+'"');
					var qty=""; var asst="";
					var colorSizeCtnQty=0;
					colorSizeCtnQty=parseInt($('#txt_colorSizeRatioQty_'+i+'_'+m).val()*1);
					var colorSizeRatioQty=0;
					colorSizeRatioQty=(ctnQty*colorSizeCtnQty);
					ratioQty_arr[index_qty]=colorSizeRatioQty;
					if(typeof(data_arr_assort[index_asst])=="undefined") asst=""; else asst=data_arr_assort[index_asst];
					//alert (ratioQty_arr[index_qty])
					//if(typeof(data_arr_qty[index_qty])=="undefined") qty=""; else qty=data_arr_qty[index_qty];
					html +='<td width="55px"><input name="txt_colorSizeQty_[]" id="txt_colorSizeQty_'+i+'_'+m+'" value="'+ratioQty_arr[index_qty]+'" class="text_boxes_numeric" style="width:50px;" onChange="fnc_calAmountQty_ex(this.value,1); " onDblClick="fnc_assortment_pop_up('+"'"+color_value[i]+"'"+','+"'"+size_value[m]+"'"+','+i+','+m+',this.value);" readonly /><input name="txt_assortQty_[]" id="txt_assortQty_'+i+'_'+m+'" value="'+asst+'" class="text_boxes" style="width:50px;" type="hidden" /></td>';
					
					//var colorSizeQty=parseInt($('#txt_colorSizeQty_'+i+'_'+m).val()*1);
				}
				
				html += '<td width="60px" rowspan="5"><input type="text" name="txt_colorQty_[]" id="txt_colorQty_'+i+'" class="text_boxes_numeric" onDblClick="openmypage_ultimate_pop(this.value,'+i+','+"'"+color_value[i]+"'"+');" style="width:50px;" readonly /></td><td width="70px" rowspan="5"><input type="text" name="txt_colorAmt_[]" class="text_boxes_numeric" id="txt_colorAmt_'+i+'" style="width:50px;" readonly /></td><td>&nbsp</td></tr><tr><td width="80px">Rate</td>';
				
				for (var m = 1; m <size_value.length; m++){
					var colorSizeCtnRate=0;
					colorSizeCtnRate=$('#txt_colorSizeRatioRate_'+i+'_'+m).val()*1;
					var index_id=eval('"' + color_value[i]+'_'+size_value[m] + 'id'+'"');
					//var index_rate=eval('"' + color_value[i] + size_value[m] + 'rate'+'"');
					var id=''; var rate="";
					if(typeof(data_arr_id[index_id])=="undefined") id=''; else id=data_arr_id[index_id];
					//if(typeof(data_arr_rate[index_rate])=="undefined") rate=""; else rate=data_arr_rate[index_rate];
					html+='<td width="55px"><input type="hidden" name="txt_colorSizeId_[]" id="txt_colorSizeId_'+i+'_'+m+'" value="'+id+'" class="text_boxes_numeric" style="width:50px;" /><input type="text" name="txt_colorSizeRate_[]" id="txt_colorSizeRate_'+i+'_'+m+'" value="'+colorSizeCtnRate+'" class="text_boxes_numeric" style="width:50px;" onChange="fnc_calAmountQty_ex(this.value,2)" disabled /></td>';
				}
				html += '<td>&nbsp</td></tr><tr><td width="80px">Ex. Cut %</td>';
				
				for (var m = 1; m <size_value.length; m++){
					//if(is_update==1)
					var index_qty=eval('"' + color_value[i]+'_'+size_value[m] + 'qty'+'"');
					var index_excut=eval('"' + color_value[i]+'_'+size_value[m] + 'excut'+'"');
					
					var excut=0;
					if(typeof(data_arr_excut[index_excut])=="undefined") excut=""; else excut=data_arr_excut[index_excut];// ratioExCut_arr[index_excut] 
					html+='<td width="55px"><input type="text" name="txt_colorSizeExCut_[]" id="txt_colorSizeExCut_'+i+'_'+m+'" value="'+excut+'" class="text_boxes_numeric" style="width:50px;" onChange="fnc_calAmountQty_ex(this.value,3); fnc_copy_qty_excut('+i+','+m+',this.value,2);" '+excutdisable+' /></td>';
				}
				html += '<td>&nbsp</td></tr><tr><td width="80px">Plan Cut Qty.</td>';
				
				for (var m = 1; m < size_value.length; m++){
					var index_plancut=eval('"' + color_value[i]+'_'+size_value[m] + 'plancut'+'"');
					var index_excut=eval('"' + color_value[i]+'_'+size_value[m] + 'excut'+'"');
					var index_qty=eval('"' + color_value[i]+'_'+size_value[m] + 'qty'+'"');
					var plancut=""; var colorSizeExcut=0;
					
					var colorSizeCtnQty=0;
					colorSizeCtnQty=parseInt($('#txt_colorSizeRatioQty_'+i+'_'+m).val()*1);
					var colorSizeRatioQty=0;
					colorSizeRatioQty=(ctnQty*colorSizeCtnQty)*1;
					//alert(ratioExCut_arr[index_excut]+'__'+colorSizeCtnQty);
					//alert(data_arr_plancut[index_plancut])
					colorSizeExcut=((ratioExCut_arr[index_excut]*1)/100)*1; 
						//ratioExCut_arr[index_excut]
						
					plancut=( colorSizeRatioQty*colorSizeExcut)+colorSizeRatioQty;
					if(colorSizeExcut==0) plancut=colorSizeRatioQty;
					if(typeof(plancut)=="undefined" || typeof(plancut)=="NaN") plancut=""; else plancut=plancut;
					
					if(typeof(data_arr_plancut[index_plancut])=="undefined") plancut=""; else plancut=data_arr_plancut[index_plancut];
					html+='<td width="55px"><input type="text" name="txt_colorSizePlanCut_[]" id="txt_colorSizePLanCut_'+i+'_'+m+'" value="'+plancut+'"  class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,3); fnc_copy_qty_excut('+i+','+m+',this.value,3);" disabled /></td>';
					//alert(plancut)
				}
				html += '<td width="97px"><input type="text" name="txt_totPlanCutAmt_[]" class="text_boxes_numeric" id="txt_totPlanCutAmt_'+i+'" style="width:50px;" readonly /></td></tr><tr><td width="80px">Article No</td>';
				
				for (var m = 1; m <size_value.length; m++){
					var index_artno=eval('"' + color_value[i]+'_'+size_value[m] + 'artno'+'"');
					var artno='';
					if(typeof(data_arr_artno[index_artno])=="undefined") artno=''; else artno=data_arr_artno[index_artno];
					html+='<td width="55px"><input type="text" name="txt_colorSizeArticleNo_[]" id="txt_colorSizeArticleNo_'+i+'_'+m+'" value="'+artno+'" class="text_boxes" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,4)"/></td>';
					//fnc_copy_qty_excut(i, m, ratioQty_arr[index_qty], type);
				}
				
				html += '<td>&nbsp</td></tr>';
			}

			html += '</tbody><tfoot><tr><td></td><td><strong>Total : </strong></td>';
			for (var m = 1; m < size_value.length; m++){
				html+='<td width="55px"><input type="text" name="txt_sizeQty_[]" id="txt_sizeQty_'+m+'" class="text_boxes_numeric" style="width:50px;" readonly /></td>';
			}
			
			html += '<td width="80px"><input type="text" name="txt_poQty" class="text_boxes_numeric" id="txt_poQty" style="width:50px;" readonly /></td><td width="80px"><input type="text" name="txt_poAmt" class="text_boxes_numeric" id="txt_poAmt" style="width:50px;" readonly /></td><td width="97px"><input type="text" name="txt_totplancut" class="text_boxes_numeric" id="txt_totplancut" style="width:50px;" readonly /></td></tr></tfoot></table>';
			$("#breakdown_div").append(html);
			
			if(is_update==0) 
			{
				set_button_status(0, permission, 'fnc_order_entry_details',2); 
				fnc_calAmountQty_ex();
				//fnc_calculateRate(type);
				fnc_copy_qty_excut(0,0,0,2)
			}
			else if(is_update==1) 
			{
				set_button_status(1, permission, 'fnc_order_entry_details',2);
				fnc_calAmountQty_ex(0,0);
				fnc_copy_qty_excut(0,0,0,2)
				//fnc_calculateRate(type);
			}
		}
	}
	
	function fnc_ratioQtyRate(val,data_type)//Ratio Lavel
	{
		var breakdown_type =$('#cbo_breakdown_type').val();
		var color_table =$('#color_tbl tbody tr').length; 
		var size_table =$('#size_tbl tbody tr').length;
		var gmtsItem_id=$('#cbo_gmtsItem_id').val();
		var set_qnty=$('#tot_set_qnty').val()*1;
		var avg_price=$('#txt_avg_price').val()*1;
		var set_breck=$('#set_breck_down').val();
		set_breck=set_breck.split('__');
		var item_ratio_arr=new Array();
		for (var k = 0; k <set_breck.length; k++)
		{
			var ex_set_data=set_breck[k].split('_');
			item_ratio_arr[ex_set_data[0]]=ex_set_data[1]*1;
		}
		var set_price=(avg_price/set_qnty)*item_ratio_arr[gmtsItem_id];
		
		var colorQty=0; var colorAmt=0;
		for (var i = 1; i < color_table; i++)
		{
			var totColorQty=0;  var totColorRate=0; var totColorAmt=0;
			var j=1;
			for (var m = 1; m < size_table; m++)
			{
				//if(data_type==1) $('#txt_colorSizeRatioRate_'+i+'_'+m).val( set_price );
				totColorQty=totColorQty+$('#txt_colorSizeRatioQty_'+i+'_'+m).val()*1;
				totColorRate=totColorRate+($('#txt_colorSizeRatioRate_'+i+'_'+m).val()*1)*totColorQty;
				totColorAmt=totColorAmt+($('#txt_colorSizeRatioRate_'+i+'_'+m).val()*1)*($('#txt_colorSizeRatioQty_'+i+'_'+m).val()*1);
			}
			$('#txt_colorRatioQty_'+i).val(totColorQty);
			$('#txt_colorRatioAmt_'+i).val(totColorAmt);
			
			colorQty=colorQty+$('#txt_colorRatioQty_'+i).val()*1;
			colorAmt=colorAmt+$('#txt_colorRatioAmt_'+i).val()*1;
		}
		$('#txt_poRatioQty').val(colorQty);
		$('#txt_poRatioAmt').val(colorAmt);
		
		for (var m = 1; m < size_table; m++)
		{
			var totSizeQty=0;
			for (var i = 1; i < color_table; i++)
			{
				totSizeQty=totSizeQty+$('#txt_colorSizeRatioQty_'+i+'_'+m).val()*1;	
			}
			$('#txt_sizeRatioQty_'+m).val(totSizeQty);
		}
		
		var roundType=$("#cbo_round_type").val();
		var docSheetQty=$("#txt_docSheetQty").val()*1;
		var totCtnQty=$("#txt_poRatioQty").val()*1;
		var gmtsCtnQty=0;
		if(breakdown_type==3)
		{
			gmtsCtnQty=docSheetQty/totCtnQty*1;
			if(gmtsCtnQty % 1 === 0)
			{
				//isDecimel=1;
				$("#txt_noOf_carton").val( gmtsCtnQty );
			}
			else 
			{
				//isDecimel=2;
				var noOfCtnQty=0;
				if(roundType==1)
				{
					noOfCtnQty=Math.ceil(gmtsCtnQty);
					$("#txt_noOf_carton").val( noOfCtnQty );
				}
				else if(roundType==2)
				{
					noOfCtnQty=Math.floor(gmtsCtnQty);
					$("#txt_noOf_carton").val( noOfCtnQty );
				}
				else
				{
					$("#txt_noOf_carton").val( gmtsCtnQty );
				}
			}
		}
		var ctnQty=$("#txt_noOf_carton").val()*1;
		
		for (var x = 1; x < color_table; x++)
		{
			var z=1;
			for (var y = 1; y < size_table; y++)
			{
				var colorSizeCtnQty=0;
				colorSizeCtnQty=parseInt($('#txt_colorSizeRatioQty_'+x+'_'+y).val()*1);
				
				var colorSizeRatioQty=0;
				colorSizeRatioQty=(ctnQty*colorSizeCtnQty);
				
				$('#txt_colorSizeQty_'+x+'_'+y).val(colorSizeRatioQty);
				
				fnc_calAmountQty_ex(0,0);
			}
		}
	}
	
	function fnc_calculateRate(type,is_update)
	{
		//alert(is_update+'B');
		var gmtsItem_id=$('#cbo_gmtsItem_id').val();
		var set_qnty=$('#tot_set_qnty').val()*1;
		var avg_price=$('#txt_avg_price').val()*1;
		var set_breck=$('#set_breck_down').val();
		set_breck=set_breck.split('__');
		var item_ratio_arr=new Array();
		
		for (var k = 0; k <set_breck.length; k++)
		{
			var ex_set_data=set_breck[k].split('_');
			item_ratio_arr[ex_set_data[0]]=ex_set_data[1]*1;
		}
		var set_price=(avg_price/set_qnty)*item_ratio_arr[gmtsItem_id];
		
		var color_table =$('#color_tbl tbody tr').length; 
		var size_table =$('#size_tbl tbody tr').length;
		if(is_update==0)
		{
			for (var i = 1; i < color_table; i++)
			{
				for (var m = 1; m < size_table; m++)
				{
					if(type==1) $('#txt_colorSizeRate_'+i+'_'+m).val( number_format( set_price, 6) );
					else if(type==2 || type==3) $('#txt_colorSizeRatioRate_'+i+'_'+m).val( number_format( set_price, 6) );
					else if(type==4) 
					{
						var packQty=$('#txt_pcsQty').val()*1;
						var new_price=avg_price/packQty;
						$('#txt_colorSizeRate_'+i+'_'+m).val( number_format( new_price, 6) );
					}
				}
			}
		}
	}
	
	
	function fnc_calAmountQty_ex(data_val,data_type)
	{
		var type=$('#cbo_breakdown_type').val()*1;
		var color_table =$('#color_tbl tbody tr').length; 
		var size_table =$('#size_tbl tbody tr').length;
		var cbo_order_uom=$('#cbo_order_uom').val();

		var cbo_order_uom=$('#cbo_order_uom').val();
		var copyquoid=$('#hid_copy_quotion').val();
		var costsource=$('#hid_cost_source').val();
		var quotation_price=$('#txt_quotation_price').val();
		
		//alert (data_val+'_'+data_type)
		var colorQty=0; var colorAmt=0; var y=0; var tot_pcs_rate=0;var planCutAmt = 0; var avg_price_cond=0;var tot_plancolorAmt=0;
		for (var i = 1; i < color_table; i++)
		{
			var totColorQty=0; var totColorRate=0; var totColorAmt=0;var totPlanCutAmt=0;
			var j=1;  
			for (var m = 1; m < size_table; m++)
			{
				if(j==1)
				{
					//if($('#txt_colorSizeExCut_'+i+'_'+m).val()=="")
					//if($('#txt_colorSizeArticleNo_'+i+'_'+m).val()=="")	
					//if(data_type==4) $('#txt_colorSizeArticleNo_'+i+'_'+m).val( data_val ); issue id 13284
				}
				else
				{
					/*if(data_type==2) $('#txt_colorSizeRate_'+i+'_'+m).val( data_val );
					else if(data_type==3) $('#txt_colorSizeExCut_'+i+'_'+m).val( data_val );
					else if(data_type==4) $('#txt_colorSizeArticleNo_'+i+'_'+m).val( data_val );*/
				}
				var totPcsQty=""; var ex_cut="";
				if(type==1 || type==2  || type==3)
				{
					var txtSizeName=$('#txtSizeName_'+m).val();
					//alert(type);
					
					if(txtSizeName!="")
					{
					totColorQty=totColorQty+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
					totColorAmt=totColorAmt+($('#txt_colorSizeRate_'+i+'_'+m).val()*1)*($('#txt_colorSizeQty_'+i+'_'+m).val()*1);
					}
					if(($('#txt_colorSizePLanCut_'+i+'_'+m).val()*1)==0)
					{
						$('#txt_colorSizePLanCut_'+i+'_'+m).val( $('#txt_colorSizeQty_'+i+'_'+m).val()*1 );
					}
					else if(($('#txt_colorSizeExCut_'+i+'_'+m).val()*1)==0)
					{
						ex_cut=(($('#txt_colorSizeExCut_'+i+'_'+m).val()*1/100)*($('#txt_colorSizeQty_'+i+'_'+m).val()*1))+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
						ex_cut=Math.ceil(ex_cut);
						$('#txt_colorSizePLanCut_'+i+'_'+m).val( ex_cut );
					}
					else //if(($('#txt_colorSizeExCut_'+i+'_'+m).val()*1)!=0)
					{
						ex_cut=(($('#txt_colorSizeExCut_'+i+'_'+m).val()*1/100)*($('#txt_colorSizeQty_'+i+'_'+m).val()*1))+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
						ex_cut=Math.ceil(ex_cut);
						$('#txt_colorSizePLanCut_'+i+'_'+m).val( ex_cut );
					}
					totPlanCutAmt = totPlanCutAmt+$('#txt_colorSizePLanCut_'+i+'_'+m).val()*1;
				}
				else if(type==4)
				{
					totPcsQty=totPcsQty+($('#txt_colorSizePackQty_'+i+'_'+m).val()*1)*($('#txt_colorSizePcsQty_'+i+'_'+m).val()*1);
					//totColorRate=totColorRate+($('#txt_colorSizeRate_'+i+'_'+m).val()*1)*totPcsQty;
					totColorAmt=totColorAmt+($('#txt_colorSizeRate_'+i+'_'+m).val()*1)*totPcsQty;
					if(totPcsQty==0) totPcsQty="";
					$('#txt_colorSizeQty_'+i+'_'+m).val( totPcsQty );
					totColorQty=totColorQty+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
					totPlanCutAmt = totPlanCutAmt+$('#txt_colorSizePLanCut_'+i+'_'+m).val()*1;
				}
			}
			$('#txt_colorQty_'+i).val(totColorQty);
			$('#txt_colorAmt_'+i).val(totColorAmt);
			$('#txt_totPlanCutAmt_'+i).val(totPlanCutAmt);			
			colorQty=colorQty+$('#txt_colorQty_'+i).val()*1;
			colorAmt=colorAmt+$('#txt_colorAmt_'+i).val()*1;
			tot_plancolorAmt=tot_plancolorAmt+$('#txt_totPlanCutAmt_'+i).val()*1;
		}
		$('#txt_poQty').val(colorQty);
		$('#txt_poAmt').val(number_format(colorAmt,6,'.',''));
		$('#txt_totplancut').val(number_format(tot_plancolorAmt,6,'.',''));

		
		for (var m = 1; m < size_table; m++)
		{
			var totSizeQty=0;
			for (var i = 1; i < color_table; i++)
			{
				totSizeQty=totSizeQty+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;	
			}
			$('#txt_sizeQty_'+m).val(totSizeQty);
		}
	}
	
	function fnc_set_ship_date()
	{
		/*var txt_cutup_date=document.getElementById('txt_cutup_date').value;
		var country_id=document.getElementById('cbo_deliveryCountry_id').value;
		var cbo_cut_up=document.getElementById('cbo_cutOff_id').value;
		
		if(txt_cutup_date=="")
		{
			alert("Insert Cutup Date");
			$("#txt_countryShip_date").attr("disabled",false);
			return;
		}
		
		if(cbo_cut_up==0)
		{
			alert("Select Cutup");
			$("#txt_countryShip_date").attr("disabled",false);
			return;
		}
		var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'requires/order_entry_controller');
		document.getElementById('txt_countryShip_date').value=set_ship_date;
		$("#txt_countryShip_date").attr("disabled",true);
		
		*/

		var cut_off_used=$("#cut_off_used").val();
		if(cut_off_used==0 || cut_off_used==1)
		{
			var txt_cutup_date=document.getElementById('txt_cutup_date').value;
			var cbo_cut_up=document.getElementById('cbo_cutOff_id').value;
			
			var po_id=document.getElementById('update_id_details').value;
			var country_id=$("#cbo_deliveryCountry_id").val();
			var cutt_off=return_global_ajax_value(po_id+"_"+country_id, 'check_country', '', 'requires/order_entry_controller');
			
			var is_cutt=cutt_off.split("_");
			if(txt_cutup_date=="")
			{
				alert("Insert Cutup Date");
				 $("#txt_countryShip_date").attr("disabled",false);
				return;
			}
			
			if(is_cutt[1]!=0 || is_cutt[1]!=0)
			{
				if(cbo_cut_up==0)
				{
					alert("Select Cutup");
					$("#txt_countryShip_date").attr("disabled",false);
					return;
				}
				else
				{
					$("#txt_countryShip_date").attr("disabled",true);
				}
			}
			else
			{
				$("#txt_countryShip_date").attr("disabled",false);
				//return;
			}
			var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'requires/order_entry_controller');
			document.getElementById('txt_countryShip_date').value=set_ship_date;
		}
		else
		{
			$("#txt_countryShip_date").val( $("#txt_pub_shipment_date").val() );
		}
		
		
	}
	
	function fnc_noof_carton(type)
	{
		//$("#txt_noOf_carton").val('');
		if(type==1)
		{
			$("#txt_noOf_carton").attr("disabled",true);
			$("#cbo_round_type").attr("disabled",true);
			$("#txt_docSheetQty").attr("disabled",false);
			$("#txt_breakdownGrouping").attr("disabled",true);
			$("#shtQty_td").text("Order Qty");
			$("#shtQty_td").css('color','blue');
			$('#pack_type').css('color','black');
			$("#rate_td").text("Avg. Rate Pcs/Set");
			$('#rate_td').css('color','blue');
			$('#pcsQty_td').css('color','black');
			$("#txt_pcsQty").attr("disabled",true);
		}
		else if(type==2)
		{
			$("#txt_noOf_carton").attr("disabled",false);
			$("#cbo_round_type").attr("disabled",true);
			$("#txt_docSheetQty").attr("disabled",true);
			$("#txt_breakdownGrouping").attr("disabled",true);
			$("#shtQty_td").text("Po Sheet Qty");
			$("#shtQty_td").css('color','blue');
			$('#pack_type').css('color','black');
			$("#rate_td").text("Avg. Rate Pcs/Set");
			$('#rate_td').css('color','blue');
			$('#pcsQty_td').css('color','black');
			$("#txt_pcsQty").attr("disabled",true);
		}
		else if(type==3)
		{
			$("#txt_noOf_carton").attr("disabled",true);
			$("#cbo_round_type").attr("disabled",false);
			$("#txt_docSheetQty").attr("disabled",false);
			$("#txt_breakdownGrouping").attr("disabled",true);
			$("#shtQty_td").text("Po Sheet Qty");
			$("#shtQty_td").css('color','blue');
			$('#pack_type').css('color','black');
			$("#rate_td").text("Avg. Rate Pcs/Set");
			$('#rate_td').css('color','blue');
			$('#pcsQty_td').css('color','black');
			$("#txt_pcsQty").attr("disabled",true);
		}
		else if(type==4)
		{
			$("#txt_noOf_carton").attr("disabled",true);
			$("#cbo_round_type").attr("disabled",true);
			$("#txt_docSheetQty").attr("disabled",true);
			$("#txt_breakdownGrouping").attr("disabled",false);
			$("#shtQty_td").text("Avg. Rate Pcs");
			$("#shtQty_td").css('color','#444');
			$('#pack_type').css('color','blue');
			$("#rate_td").text("Avg. Rate Pack");
			$('#rate_td').css('color','blue');
			$('#pcsQty_td').css('color','blue');
			$("#txt_pcsQty").attr("disabled",false);
		}
	}
	
	function fnc_resetPoDtls(type)
	{
		$('#txt_avg_price').attr('disabled',false);
		$('#act_po_list_view').html('');
		$('#country_po_list_view').html('');
		$('#breakdown_div').html('');
		$('#breakdownratio_div').html('');
		$('#td_size').html('');
		$('#td_color').html('');
		$('#txt_is_update').val(0);
		$('#txt_noOf_carton').attr('disabled','true');
		$('#cbo_breakdown_type').removeAttr('disabled');
		$('#txt_countryShip_date').attr('disabled',false);
		$('#cbo_gmtsItem_id').removeAttr('disabled');
		$('#cbo_deliveryCountry_id').removeAttr('disabled');
		if($('#txt_job_no').val()!="")
		{
			$('#txt_avg_price').val( $('#txt_quotation_price').val() );
		}
		else
		{
			$('#txt_avg_price').val('');
		}
		
		var counter=1;
		$('#td_color').append(
					'<tr id="trColor_'+counter+'">'
					+ '<td align="center">'+counter+'</td><td align="center"><input type="text" name="txtColorName[]" class="text_boxes" id="txtColorName_'+counter+'"  style="width:80px;" onKeyUp="append_color_size_row(1)" /><input type="hidden" name="txtColorId[]" id="txtColorId_'+counter+'" value="0" class="text_boxes" style="width:50px"/></td>'+ '</tr>'
				);
				
		$('#td_size').append(
					'<tr id="trSize_'+counter+'">'
					+ '<td align="center"><input type="text" name="txtSizeName[]" class="text_boxes" id="txtSizeName_'+counter+'" style="width:80px;" onKeyUp="append_color_size_row(2)" /><input type="hidden" name="txtSizeId[]" id="txtSizeId_'+counter+'" value="0" class="text_boxes" style="width:50px"/></td>'+ '</tr>'
				);
		$('#update_id_details').val('');
		$('#txt_po_datedif_hour').val('');
		$('#cbo_currercy').val(2);
	}
	
	function copy_check(type)
	{
		if(type==1)
		{
			if(document.getElementById('copy_id').checked==true)
			{
				document.getElementById('copy_id').value=1;
				$('#txt_copypo_no').removeAttr('disabled','disabled');
				set_button_status(0, permission, 'fnc_order_entry_details',2);
				//alert(chk );
			}
			else if(document.getElementById('copy_id').checked==false)
			{
				document.getElementById('copy_id').value=2;
				$('#txt_copypo_no').val('');
				$('#txt_copypo_no').attr('disabled','disabled');
			}
		}
		else if(type==2)
		{
			if(document.getElementById('copy_asac').checked==true)
			{
				document.getElementById('copy_asac').value=1;
			}
			else if(document.getElementById('copy_asac').checked==false)
			{
				document.getElementById('copy_asac').value=2;
			}
		}
		else if(type==3)
		{
			if(document.getElementById('copy_assc').checked==true)
			{
				document.getElementById('copy_assc').value=1;
			}
			else if(document.getElementById('copy_assc').checked==false)
			{
				document.getElementById('copy_assc').value=2;
			}
		}
		else if(type==4)
		{
			if(document.getElementById('copy_acss').checked==true)
			{
				document.getElementById('copy_acss').value=1;
			}
			else if(document.getElementById('copy_acss').checked==false)
			{
				document.getElementById('copy_acss').value=2;
			}
		}
		else if(type==5)
		{
			if(document.getElementById('copy_excut').checked==true)
			{
				document.getElementById('copy_excut').value=1;
			}
			else if(document.getElementById('copy_excut').checked==false)
			{
				document.getElementById('copy_excut').value=2;
			}
		}
		else if(type==7)
		{
			if(document.getElementById('chk_is_repeat').checked==true)
			{
				document.getElementById('chk_is_repeat').value=1;
			}
			else if(document.getElementById('chk_is_repeat').checked==false)
			{
				document.getElementById('chk_is_repeat').value=2;
			}
		}
	}
	
	function excess_percentage( comp, buyer, qnty )
	{
		//var exc_perc=new Array();
		//alert (comp+'='+buyer+'='+qnty);
		// alert (typeof(exc_perc[comp])); 
		
		//if( exc_perc[comp][buyer]!="undefined" )
		if(typeof(exc_perc[comp])!= 'undefined')
		{
			if(typeof(exc_perc[comp][buyer])!= 'undefined')
			{
				var newp=exc_perc[comp][buyer]["limit"]; 
				//alert(newp);
				var newp= JSON.stringify(newp);
				var newstr=newp.split(",");
				for(var m=0; m< newstr.length; m++)
				{
					var limit=exc_perc[comp][buyer]["limit"][m].split("__");
					//alert(limit[1]+'='+limit[0]+'='+qnty);
					if((limit[1]*1)==0 && (qnty*1)>=(limit[0]*1))
					{
						return ( exc_perc[comp][buyer]["val"][m]*1);	
					}
					if( (qnty*1)>=(limit[0]*1) && (qnty*1)<=(limit[1]*1) )
					{
						//alert(limit[1]+'='+limit[0]+'='+qnty);
						return exc_perc[comp][buyer]["val"][m];
					}
					 //alert( newstr[m]+"=="+m)
				}
			}
		}
		return 0;
	}
	
	function fnc_copy_qty_excut(colRid,sizRid,val,type)
	{
		var color_table =$('#color_tbl tbody tr').length; 
		var size_table =$('#size_tbl tbody tr').length;
		//alert(color_table+'='+size_table)
		var company_name=$('#cbo_company_name').val(); 
		var buyer_name=$('#cbo_buyer_name').val(); 
		var excut_source=$('#hid_excessCut_source').val()*1;
		
		if(type==5 || type==6)
		{
			val=($('#txt_colorSizePackQty_'+colRid+'_'+sizRid).val()*1)*($('#txt_colorSizePcsQty_'+colRid+'_'+sizRid).val()*1);
		}
		
		var excut_fmLib =0;
		if(excut_source==2 && type==2)
		{
			return;
			//var excut_fmLib = excess_percentage(company_name,buyer_name,val);
			//if(excut_fmLib!=0) excut_fmLib =number_format(excut_fmLib*1,2 );
		}
		
		//alert(excut_fmLib)
		//alert (excut_fmLib+'='+val);
		var is_checked_asac=document.getElementById('copy_asac').value;
		var is_checked_assc=document.getElementById('copy_assc').value;
		var is_checked_acss=document.getElementById('copy_acss').value;
		var is_checked_excut=document.getElementById('copy_excut').value;
		if(type==1)
		{
			if(is_checked_asac==1)
			{
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						//if($('#txt_colorSizeQty_'+i+'_'+m).val()=="")
						//{
							$('#txt_colorSizeQty_'+i+'_'+m).val( val );
							if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val( excut_fmLib );
							var colorSizeExcut=0; var colorSizePlancut="";
							colorSizeExcut=(excut_fmLib*1)/100;
							colorSizePlancut=((val*colorSizeExcut)+val*1);
							
							var colorSizePlancutQty=Math.ceil(colorSizePlancut);
							//alert (colorSizePlancutQty)
							if(val==0 || colorSizeExcut==0) colorSizePlancutQty=val;
							$('#txt_colorSizePLanCut_'+i+'_'+m).val( colorSizePlancutQty );
							//alert (excut_fmLib)
						//}
					}
					sizRid=1;
				}
				fnc_calAmountQty_ex(0,0)
			}
			if(is_checked_assc==1)
			{
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						/*if($('#txt_colorSizeQty_'+colRid+'_'+m).val()=="")
						{*/
							$('#txt_colorSizeQty_'+colRid+'_'+m).val( val );
							if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+m).val( excut_fmLib );
							var colorSizeExcut=0; var colorSizePlancut="";
							colorSizeExcut=(excut_fmLib*1)/100;
							colorSizePlancut=((val*colorSizeExcut)+val*1);
							
							var colorSizePlancutQty=Math.ceil(colorSizePlancut);
							//alert (colorSizePlancutQty)
							if(val==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+colRid+'_'+m).val();
							$('#txt_colorSizePLanCut_'+colRid+'_'+m).val( colorSizePlancutQty );
						//}
					}
				}
				fnc_calAmountQty_ex(0,0)
			}
			if(is_checked_acss==1)
			{
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						/*if($('#txt_colorSizeQty_'+i+'_'+sizRid).val()=="")
						{*/
							$('#txt_colorSizeQty_'+i+'_'+sizRid).val( val );
							if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+sizRid).val( excut_fmLib );
							var colorSizeExcut=0; var colorSizePlancut="";
							colorSizeExcut=(excut_fmLib*1)/100;
							colorSizePlancut=((val*colorSizeExcut)+val*1);
							
							var colorSizePlancutQty=Math.ceil(colorSizePlancut);
							//alert (colorSizePlancutQty)
							if(val==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+i+'_'+sizRid).val();
							$('#txt_colorSizePLanCut_'+i+'_'+sizRid).val( colorSizePlancutQty );
						//}
					}
				}
				fnc_calAmountQty_ex(0,0)
			}			
			if(is_checked_asac==2 || is_checked_assc==2 || is_checked_acss==2)
			{
				if(excut_fmLib==0) excut_fmLib='';
				/*if($('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val()=="")
				{*/
					if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( excut_fmLib );
					
					var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut="";
					colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val()*1;
					colorSizeExcut=($('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val()*1)/100;
					colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
					//alert (colorSizePlancut)
					var colorSizePlancutQty=Math.ceil(colorSizePlancut);
					if(colorSizeQty==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
					$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val( colorSizePlancutQty );
					
				//}
			}
		}
		else if (type==2)
		{
			if(is_checked_excut==1 && is_checked_assc==2)
			{
				for (var i = 1; i < color_table; i++)
				{
					var colorTotPlanQty=0;
					for (var m = 1; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						/*if($('#txt_colorSizeQty_'+i+'_'+sizRid).val()=="")
						{*/
							if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val( excut_fmLib );
							else if(excut_source==1) $('#txt_colorSizeExCut_'+i+'_'+m).val( val );
						//}
						var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut="";
						colorSizeQty=$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
						colorSizeExcut=(val*1)/100;
						colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
						var colorSizePlancutQty=Math.ceil(colorSizePlancut);
						//if(colorSizeQty==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+i+'_'+m).val();
						$('#txt_colorSizePLanCut_'+i+'_'+m).val( colorSizePlancutQty );
						colorTotPlanQty=colorTotPlanQty+(colorSizePlancutQty*1);
					}
				}
			}
			if(is_checked_excut==1 && is_checked_assc==1)
			{
				for (var i = colRid; i < color_table; i++)
				{
					var colorTotPlanQty=0;
					for (var m = sizRid; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						/*if($('#txt_colorSizeQty_'+i+'_'+sizRid).val()=="")
						{*/
							if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+m).val( excut_fmLib );
							else if(excut_source==1) $('#txt_colorSizeExCut_'+colRid+'_'+m).val( val );
						//}
						var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut="";
						colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+m).val()*1;
						colorSizeExcut=(val*1)/100;
						colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
						var colorSizePlancutQty=Math.ceil(colorSizePlancut);
						$('#txt_colorSizePLanCut_'+colRid+'_'+m).val( colorSizePlancutQty );
						colorTotPlanQty=colorTotPlanQty+(colorSizePlancutQty*1);
					}
				}
			}
			else
			{
				if(colRid!=0 && sizRid!=0)
				{
					if(excut_fmLib==0) excut_fmLib='';
					
					//alert(excut_source+'='+excut_fmLib);
					if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( excut_fmLib );
					var exCut=$('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val();
					
					var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut=""; var exCut_new="";
					colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val()*1;
					colorSizeExcut=($('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val()*1)/100;
					colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
					//var exCut_new=((colorSizePlancut-colorSizeQty)*1/colorSizeQty)*100;
					var colorSizePlancutQty=Math.ceil(colorSizePlancut);
					//alert(colRid+'_'+sizRid)
					
					if(colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
					$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val( colorSizePlancutQty );
				}				
			}
			var tot_plancut=0;
			for (var i = 1; i < color_table; i++)
			{
				var colorTotPlanQty=0;
				for (var m = 1; m < size_table; m++)
				{
					 var size_name=$('#txtSizeName_'+m).val();
					 if(size_name!="")
					 {
						colorTotPlanQty=colorTotPlanQty+($('#txt_colorSizePLanCut_'+i+'_'+m).val()*1);
						tot_plancut=tot_plancut+($('#txt_colorSizePLanCut_'+i+'_'+m).val()*1);
					 }
				}
				//alert(tot_plancut);
				$('#txt_totPlanCutAmt_'+i).val(colorTotPlanQty);
			}
			$('#txt_totplancut').val(tot_plancut);
			
		}
		else if (type==3)
		{
			var colorSizeQty=0; var colorSizePlancut=0; var colorSizeExcut="";
			colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val()*1;
			//colorSizePlancut=($('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val()*1);/100
			colorSizeExcut=(((val*1)-colorSizeQty)/colorSizeQty)*100;
			//alert (colorSizeExcut)
			//var colorSizePlancutQty=Math.round(colorSizePlancut);
			//if(colorSizeQty==0 || colorSizePlancut==0) colorSizeExcut='';
			if(val<colorSizeQty) 
			{
				alert("Ex Cut % Negative Not Allowed."); // Because Your Plancut Qty less then Order Qty
				$('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val('');
				$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val('');
				return;
			}
			else $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( colorSizeExcut);
		}
		else if (type==5) //|| type==6
		{
			if(excut_fmLib==0) excut_fmLib='';
			var qty_qty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
			$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val(qty_qty);
			
			var pack_qty=$('#txt_colorSizePackQty_'+colRid+'_'+sizRid).val();
			if(is_checked_asac==1)
			{		
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if($('#txt_colorSizePackQty_'+i+'_'+m).val()=="")
						{
							$('#txt_colorSizePackQty_'+i+'_'+m).val( pack_qty );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val(excut_fmLib);
					}
					sizRid=1;
				}
			}
			if(is_checked_assc==1)
			{		
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if($('#txt_colorSizePackQty_'+colRid+'_'+m).val()=="")
						{
							$('#txt_colorSizePackQty_'+colRid+'_'+m).val( pack_qty );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+m).val(excut_fmLib);
					}
				}
			}
			if(is_checked_acss==1)
			{		
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if($('#txt_colorSizePackQty_'+i+'_'+sizRid).val()=="")
						{
							$('#txt_colorSizePackQty_'+i+'_'+sizRid).val( pack_qty );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+sizRid).val(excut_fmLib);
					}
				}
			}
			if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val(excut_fmLib);
			fnc_calAmountQty_ex(0,0)
		}
		else if (type==6)
		{
			if(excut_fmLib==0) excut_fmLib='';
			var qty_qty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
			$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val(qty_qty);
			
			var pcs_per_pack=$('#txt_colorSizePcsQty_'+colRid+'_'+sizRid).val();
			if(is_checked_asac==1)
			{		
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if($('#txt_colorSizePcsQty_'+i+'_'+m).val()=="")
						{
							$('#txt_colorSizePcsQty_'+i+'_'+m).val( pcs_per_pack );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val(excut_fmLib);
					}
					sizRid=1;
				}
			}
			if(is_checked_assc==1)
			{		
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if($('#txt_colorSizePcsQty_'+colRid+'_'+m).val()=="")
						{
							$('#txt_colorSizePcsQty_'+colRid+'_'+m).val( pcs_per_pack );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+m).val(excut_fmLib);
					}
				}
			}
			if(is_checked_acss==1)
			{		
				for (var i = colRid; i < color_table; i++)
				{
					for (var m = sizRid; m < size_table; m++)
					{
						if($('#txt_colorSizePcsQty_'+i+'_'+sizRid).val()=="")
						{
							$('#txt_colorSizePcsQty_'+i+'_'+sizRid).val( pcs_per_pack );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+sizRid).val(excut_fmLib);
					}
				}
			}
			if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( excut_fmLib );
			fnc_calAmountQty_ex(0,0)
		}
		//fnc_calAmountQty_ex(0,0);
		//fnc_ratioQtyRate(0,0)
	}
	
	function fnc_rateRatio_calculate()
	{
		var color_table =$('#color_tbl tbody tr').length; 
		var size_table =$('#size_tbl tbody tr').length;
		var gmtsItem_id=$('#cbo_gmtsItem_id').val();
		var set_qnty=$('#tot_set_qnty').val()*1;
		var avg_price=$('#txt_avg_price').val()*1;
		var set_breck=$('#set_breck_down').val();
		set_breck=set_breck.split('__');
		var item_ratio_arr=new Array();
		
		for (var k = 0; k <set_breck.length; k++)
		{
			var ex_set_data=set_breck[k].split('_');
			item_ratio_arr[ex_set_data[0]]=ex_set_data[1]*1;
		}
		var set_price=(avg_price/set_qnty)*item_ratio_arr[gmtsItem_id];
		for (var i = 1; i < color_table; i++)
		{
			for (var m = 1; m < size_table; m++)
			{
				$('#txt_colorSizeRatioRate_'+i+'_'+m).val( set_price );
			}
		}
	}
	
	function fnc_copy_po(operation,old_po_id,old_po_no)
	{
		var buyer_name=get_dropdown_text('cbo_buyer_name');
		var buyer_id=$("#cbo_buyer_name").val();
		//alert(operation+'_'+old_po_id+'_'+old_po_no);
		if((buyer_name=='TORAY-GU TRPU' && buyer_id==24) || (buyer_name=='TORAY-N.MATSUYA' && buyer_id==28) || (buyer_name=='TORAY-MUJI' && buyer_id==27))
		{
			alert("This buyer is not availabe for any Style Ref. in ERP. If need Please contract with MIS.");
			return;
		}
		var txt_job_no =$("#txt_job_no").val();
		var precost = return_ajax_request_value(txt_job_no, 'check_precost_approve', 'requires/order_entry_controller');
		if (precost==1)
		{
			alert("Pre Cost Approved, Any Change will be not allowed.");
			return;
		}
		
		if(old_po_id=="" && old_po_no!="")
		{
			alert("Please Need Po Save.");
			release_freezing();	
			return;
		}
		var r=confirm("You are Going to Copy a PO.\n Please, Press OK to Copy.\n Otherwise Press Cencel.");
		if(r==true)
		{
			if(form_validation('txt_copypo_no','Copy Po Number')==false)
			{
				return;   
			}
			var txt_copypo_no=encodeURIComponent("'"+$('#txt_copypo_no').val()+"'");
			var po_nodata='&txt_copypo_no='+"'"+txt_copypo_no+"'";
			var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('copy_id*hidd_job_id*update_id*set_breck_down*update_id_details',"../../")+po_nodata;
			//alert (operation); alert (old_po_no); return;
			freeze_window(operation);
			http.open("POST","requires/order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_copy_order_reponse;
		}
		else
		{
			$('#txt_copypo_no').val('');
			release_freezing();	
			return;
		}
	}
	
	function fnc_copy_order_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			release_freezing();	//return;
			if(trim(reponse[0]) ==24)
			{
				release_freezing();	
				return; 
			}
			if(trim(reponse[0]) ==12)
			{
				alert("Org. Shipment Date Not Allowed");
				release_freezing();	
				return; 
			}
			show_msg(reponse[0]);
			if(trim(reponse[0]) ==15)
			{ 
				release_freezing();
				var old_poid=$('#update_id_details').val();	
				var old_pono=$('#txt_po_no').val();	
				setTimeout('fnc_copy_po('+reponse[0]+','+old_poid+','+old_pono+')',8000); 
				//fnc_copy_po(reponse[1],old_po_id,old_po_no)
			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				$('#txt_copypo_no').attr('disabled','disabled');
				document.getElementById('copy_id').checked=false;
				$('#copy_id').val(2);
				$('#txt_copypo_no').val('');
				$('#txt_is_update').val(0);
				$('#update_id_details').val(reponse[1]);
				$('#txt_po_no').val(reponse[2]);
				
				
				$('#txt_job_qty').val(reponse[3]);
				$('#txt_avgUnit_price').val(reponse[4]);
				$('#txt_total_price').val(reponse[5]);
				$('#txt_proj_qty').val(reponse[6]);
				$('#txt_proj_avgUnit_price').val(reponse[7]);
				$('#txt_proj_total_price').val(reponse[8]);
				$('#txt_orginProj_qty').val(reponse[9]);
				$('#txt_orginProj_total_price').val(reponse[10]);
				$('#txt_orginProj_total_amt').val(reponse[11]);
				$('#txt_avg_price').val(reponse[11]);
				
				
				show_list_view( $('#update_id').val()+"*"+reponse[1]+"*"+$('#cbo_style_from').val()+"*"+$('#txt_quotation_id').val()+"*"+$('#cbo_company_name').val(),'order_listview','po_list_view','requires/order_entry_controller','setFilterGrid(\'tbl_po_list\',-1)');
				show_list_view(reponse[1],'show_po_active_listview','country_po_list_view','requires/order_entry_controller','');
				$('#breakdown_div').html('');
				$('#breakdownratio_div').html('');
				$('#td_color tr:not(:first)').remove();
				$('#td_size tr:not(:first)').remove();
				$('#txtColorName_1').val('');
				$('#txtSizeName_1').val('');
				$('#txtColorId_1').val('');
				$('#txtSizeId_1').val('');
				$('#color_size_break_down_all_data').val('');
				$('#color_size_ratio_data').val('');
				
				reset_form('','','cbo_deliveryCountry_id*cbo_code_id*cbo_country_id*cbo_countryCode_id*txt_cutup_date*cbo_cutOff_id','','');
				set_button_status(0, permission, 'fnc_order_entry_details',2);
			}
			release_freezing();
		}
	}
	
	function reorder_size_color()
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
		if(txt_job_no=="")
		{
			alert("Please Browse Job.");
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_entry_controller.php?action=reorder_size_color&txt_job_no='+txt_job_no, 'Color Size Ordering', 'width=700px,height=400px,center=1,resize=1,scrolling=0','../')	
		}
	}
	
	function openmypage_ultimate_pop(val,id,color)
	{
		//var color_name=color.innerText;
		//alert (color+'=='+id)
		if(form_validation('update_id_details','Po Save First.')==false)
		{
			return;   
		}
		else
		{
			if(val=='' || val==0)
			{
				alert('Qty Blank Not Allowed');
				return;   
			}
			else
			{
				var data=$('#txt_po_no').val()+'_'+$('#update_id_details').val()+'_'+val+'_'+color+'_'+id+'_'+$('#cbo_gmtsItem_id').val()+'_'+$('#cbo_deliveryCountry_id').val()+'_'+$('#cbo_code_id').val()+'_'+$('#cbo_country_id').val()+'_'+$('#cbo_countryCode_id').val()+'_'+$('#txt_countryShip_date').val();
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_entry_controller.php?data='+data+'&action=ultimate_dtls_popup','Utimate Dtls Pop-Up', 'width=780px,height=400px,center=1,resize=1,scrolling=0','../')
				
				release_freezing();
				emailwindow.onclose=function()
				{
				}
			}
		}
	}
	
	function fnc_buyer_style_po_check(value)
	{
		var company_id=$('#cbo_company_name').val();
		var buyer_id=$('#cbo_buyer_name').val();
		var style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		var po_id=$('#update_id_details').val();
		var buyer_style_po_check=return_ajax_request_value(company_id+'_'+buyer_id+'_'+style_ref+'_'+value+'_'+po_id, 'load_buyer_style_po_check', 'requires/order_entry_controller');
		var split_data=buyer_style_po_check.split('***');
		var show_val_column='';
		if(split_data[2]!="")
		{
			var r=confirm(split_data[1]);
			if (r==true)
			{
			}
			else
			{
				$('#txt_po_no').val('');
				$('#txt_po_no').focus();
				return;
			}
		}
	}
	
	function fnc_packtype(val)
	{
		var data_val='';
		data_val=val.toUpperCase();
		$("#txt_breakdownGrouping").val(data_val);
	}
	
	var row_color=new Array();
	var lastid='';
	function change_color_tr(v_id,e_color)
	{
		if(lastid!='') $('#tr_'+lastid).attr('bgcolor',row_color[lastid])
		 
		if( row_color[v_id]==undefined ) row_color[v_id]=$('#tr_'+v_id).attr('bgcolor');
		
		if( $('#tr_'+v_id).attr('bgcolor')=='#FF9900')
				$('#tr_'+v_id).attr('bgcolor',row_color[v_id])
			else
				$('#tr_'+v_id).attr('bgcolor','#FF9900')
		
		lastid=v_id;
		
		/*		
		var tot_row=$('#tbl_po_list tbody tr').length;
		//alert(tot_row);
		for(var i=1; i<tot_row-1;i++)
		{
			if(v_id==i)
			{
				document.getElementById("tr_"+v_id).bgColor="#33CC00";
			}
			else
			{
				if(i%2==0) Bcolor="#E9F3FF"; else Bcolor="#FFFFFF";
				document.getElementById("tr_"+i).bgColor=Bcolor;
			}
		}*/
	}
	
	function fnc_cut_off_select( country_id )
	{
		var cut_off_used= $('#cut_off_used').val();
		if(cut_off_used==0 || cut_off_used==1)
		{
			if(country_id!=0)
			{
				get_php_form_data(country_id, "load_cutOff_id_from_lib", "requires/order_entry_controller" );
			}
		}
	}
	
	function fnc_assortment_pop_up(color_n,size_n,col_in,siz_in,qty)
	{
		if(qty=='' || qty==0)
		{
			alert('Qty Blank Not Allowed.');
			return;   
		}
		else
		{
			var ass_sol_data=$('#txt_assortQty_'+col_in+'_'+siz_in).val();
			var data=color_n+'_'+size_n+'_'+qty+'_'+ass_sol_data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_entry_controller.php?data='+data+'&action=assortment_pop_up','Assortment Dtls Pop-Up', 'width=480px,height=200px,center=1,resize=1,scrolling=0','../');
			
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var assort_val=this.contentDoc.getElementById("txt_assort").value; //Access form field with id="emailfield"
				var solid_val=this.contentDoc.getElementById("txt_solid").value;
				var assort_solid='';
				assort_solid=assort_val+'!!'+solid_val;
				$('#txt_assortQty_'+col_in+'_'+siz_in).val(assort_solid);
			}
		}
	}
	
	function fnc_po_qty_validat_co_si( i, j )
	{
		var saved_po_quantity=$('#txt_colorSizeQty_'+i+'_'+j).attr('saved_po_quantity');
		//alert(saved_po_quantity)
	    var txt_po_quantity=$('#txt_colorSizeQty_'+i+'_'+j).val()*1;
		var hiddenid=$('#txt_colorSizeId_'+i+'_'+j).val(); 
		var txt_excess_cut=$('#txt_colorSizeExCut_'+i+'_'+j).val()*1;
		var po_id=$('#update_id_details').val();
		var country_id=$('#cbo_deliveryCountry_id').val();
		// if(hiddenid>0 && hiddenid !='')
		// {
		// 	//var cutting_qty=return_global_ajax_value(hiddenid, 'get_cutting_qty', '', 'size_color_breakdown_controller');
		// 	var cutting_qty=$('#txt_colorSizeQty_'+i+'_'+j).attr('production_quantity');
		// }
		if(po_id>0 && po_id !='')
		{
			var cutting_qty=return_global_ajax_value(po_id+'_'+country_id+'_'+hiddenid, 'get_cutting_qty_country', '', 'requires/order_entry_controller');
			//var cutting_qty=$('#txt_colorSizeQty_'+i+'_'+j).attr('production_quantity');
		}
		//alert(cutting_qty)
		console.log(cutting_qty);
		var excess_cut_per=(1+(txt_excess_cut/100));
		var allowed_qty=cutting_qty/excess_cut_per;
		allowed_qty=Math.ceil(allowed_qty);
		//alert(txt_po_quantity+'='+allowed_qty);
		if(txt_po_quantity<allowed_qty)
		{
			alert("Cutting Qty Found,You can update upto"+allowed_qty+" Qty");
			$('#txt_colorSizeQty_'+i+'_'+j).val(saved_po_quantity);
			return;
		}
	}
	
	/*function set_smv_check(company_id)
	{
		get_php_form_data(company_id,'set_smv_checking','requires/order_entry_controller' );
	}*/
	
	
	function set_ship_date()
	{
		var txt_cutup_date=document.getElementById('txt_cutup_date').value;
		var cbo_cut_up=document.getElementById('cbo_cutOff_id').value;
		
		var po_id=document.getElementById('update_id_details').value;
		var country_id=$("#cbo_deliveryCountry_id").val();
		var cutt_off=return_global_ajax_value(po_id+"_"+country_id, 'check_country', '', 'requires/order_entry_controller');
		
		var is_cutt=cutt_off.split("_");
		if(txt_cutup_date=="")
		{
			alert("Insert Cutup Date");
			 $("#txt_countryShip_date").attr("disabled",false);
			return;
		}
		
		if(is_cutt[1]!=0 || is_cutt[1]!=0)
		{
			if(cbo_cut_up==0)
			{
				alert("Select Cutup");
				$("#txt_countryShip_date").attr("disabled",false);
				return;
			}
			else
			{
				$("#txt_countryShip_date").attr("disabled",true);
			}
		}
		else
		{
			$("#txt_countryShip_date").attr("disabled",false);
			//return;
		}
		var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'requires/order_entry_controller');
		document.getElementById('txt_countryShip_date').value=set_ship_date;
    }
	
    function restrictToInteger(id) {
		var el = document.querySelector("#"+id);
		if(el)
		{
			 el.value = el.value.replace(/[^\d]/g, '');
		}
	}
	
	function fnc_get_company_config(company_id)
	{
		$('#cbo_style_owner').val( company_id );
		//load_drop_down( 'requires/order_entry_controller', company_id, 'load_drop_down_sew_location', 'sew_location' );

		get_php_form_data(company_id,'get_company_config','requires/order_entry_controller' );
		location_select();
		
		var celid=mst_mandatory_field.split("*")
		//alert( celid.length+"="+celid)
		var a=0;
		for (var i = 1; i <= celid.length; i++)
		{
			var td=$('#'+celid[a]).val();
			//alert(td+'='+celid[a])
			$('#'+celid[a]).closest('td').prev().css('color', 'blue');
			a++;
		}
		//po_update_period();
	}
	
	function fnc_get_buyer_config(buyer_id)
	{
		sub_dept_load(buyer_id,document.getElementById('cbo_product_department').value);
		check_tna_templete(buyer_id);
		get_php_form_data(buyer_id+'*'+1,'get_buyer_config','requires/order_entry_controller' );
		set_field_level_access(document.getElementById("cbo_company_name").value);
	}
	
	function fnc_generate_excel_sheet()
	{
		freeze_window(3);
		if(form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false)
		{
			release_freezing();
			return;   
		}
		else
		{
			var data="action=generate_excel_sheet"+get_submitted_data_string('cbo_company_name*hidd_job_id*txt_job_no', "../../");
			//	alert(data)
			http.open("POST","requires/order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_excel_sheet_reponse;	
		}
	}
	
	function fnc_generate_excel_sheet_reponse()
    {
        if (http.readyState == 4)
        {
            var response=trim(http.responseText).split("####");
			
			$('#button_container').append( '<input type="button" id="excel" name="excel" onClick="fnc_generate_excel_sheet();" class="formbutton" style="width:70px; display:none" value="Export Excel" /><a id="aa1" href="" style="text-decoration:none" download hidden>BB</a>&nbsp;&nbsp;&nbsp;' );
			
			if(response[1]>0)
			{
				$('#aa1').removeAttr('href').attr('href','requires/'+response[0]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
		release_freezing();
	}
	
	function set_pub_ship_date()
	{
		var company_id=$('#cbo_company_name').val();
		//var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/woven_order_entry_controller');
		var publish_shipment_date=$('#hid_variable_pubshipdate').val();
		if(publish_shipment_date==1){
			 $('#txt_pub_shipment_date').attr('disabled',false);
		}
		/*else{
			var txt_org_shipment_date=$('#txt_org_shipment_date').val()
			$('#txt_pub_shipment_date').val(txt_org_shipment_date);
		}*/
		
		var pub_shipment_date=$('#txt_pub_shipment_date').val();
		var shipment_date=$('#txt_org_shipment_date').val();
		
		if(pub_shipment_date == '')
		{
			alert("Publish Shipment Date Can Not Be Null");
			$('#txt_org_shipment_date').val("");
			return;
		}
		if(date_compare(pub_shipment_date,shipment_date) === false){
			alert("Shipment date can not be less then Publish shipment date");
			$('#txt_org_shipment_date').val("");
		}
	}
	function set_original_ship_date()
	{
		
		var pub_shipment_date=$('#txt_pub_shipment_date').val();
		var org_shipment_date=$('#txt_org_shipment_date').val();
		
		if(date_compare(pub_shipment_date,org_shipment_date) === false){
			alert("Publish shipment date can not be greater then Original shipment date");
			$('#txt_pub_shipment_date').val("");
		}
	}
	function get_sew_company_config(company_id)
	{
		load_drop_down( 'requires/order_entry_controller', company_id, 'load_drop_down_sew_location', 'sew_location');
	}
	
	function fnc_style_check()
	{
		var company_id=$('#cbo_company_name').val();
		$('#txt_style_ref').val('');
		$('#txt_quotation_id').val('');
		$('#txt_quotation_price').val('');
		
		var bhstyle=$('#cbo_style_from').val(); //ISD-23-21013
		if(bhstyle==1)//ISD-23-21013
		{
			$('#txt_style_ref').attr('readonly',true);
			$('#txt_style_ref').attr('placeholder','Browse');
			var page_link="'requires/order_entry_controller.php?action=bh_style_popup','Buying House Style Selection'";// for Buying House
			$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
		}
		else
		{
			fnc_variable_settings_check(company_id);	
		}
	}
	function set_phd_po_date(type){
		var pub_shipment_date=$('#txt_pub_shipment_date').val();
		if(type==1)
		{
			var shipment_date=$('#txt_phd').val();
			var field='txt_phd';
			var msg='Pack handover date';
		}		
		if(type==2)
		{
			var shipment_date=$('#txt_po_received_date').val();
			var field='txt_po_received_date';
			var msg='PO Received date';
		}

		if(pub_shipment_date != '')
		{
			if(date_compare(pub_shipment_date,shipment_date) === true){
				alert(msg+" can not be greater then or same as Publish shipment date");
				$('#'+field).val(' ');
			}
		}		
	}
	
	function repeat_openmypage(page_link,title)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_style_ref='+txt_style_ref;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");

			freeze_window(5);
		    //var repeat_job=this.contentDoc.getElementById("selected_job");
			document.getElementById('txt_repeat_job_no').value=theemail.value;
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey(); fnc_dealing();">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
    <?=load_freeze_divs ("../../",$permission);  ?>
    <table width="100%" cellpadding="0" cellspacing="2" align="center" >
        <tr>
            <td valign="top" width="950">
            <h3 style="width:950px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Garments Job Entry </h3> 
         	<div id="content_search_panel" style="width:950px">
            <fieldset style="width:950px;">
                <form name="orderentry_1" id="orderentry_1" autocomplete="off">
                <table width="950" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                    	<td width="85" class="must_entry_caption">Company</td>
                        <td width="140">
                        	<? echo create_drop_down( "cbo_company_name", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in (3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "fnc_get_company_config(this.value); fnc_variable_settings_check(this.value);"); 
							?> 
                            <input type="hidden" name="po_update_period_maintain" id="po_update_period_maintain"/>
                            <input type="hidden" name="po_current_date_maintain" id="po_current_date_maintain"/>
                            <input type="hidden" name="set_smv_id" id="set_smv_id" style="width:30px;"/>
                            <input type="hidden" name="hidd_color_from_lib" id="hidd_color_from_lib"/>
							<input type="hidden" name="act_po_id" id="act_po_id" value=""/>
                        </td>
                        <td width="120">Job No</td>              <!-- 11-00030  -->
                        <td width="140">
                        	<input style="width:120px;" type="text" title="Double Click to Search" onDblClick="openmypage_job('requires/order_entry_controller.php?action=job_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />
                            <input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
                            <input type="hidden" name="cut_off_used" id="cut_off_used"/>                           
                        </td>
                        <td width="90" class="must_entry_caption">Location</td>
                        <td width="140" id="location"><? echo create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                        <td width="80" class="must_entry_caption">Prod. Dept.</td>
                        <td><? echo create_drop_down( "cbo_product_department", 80, $product_dept, "", 1, "-Select-", $selected, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value)", "", "" ); ?>
                        	<input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Buyer</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                        <td class="must_entry_caption"><a href='##' onClick="fnc_generate_excel_sheet();">Style Ref.</a>
                            <select name="cbo_style_from" id="cbo_style_from" class="combo_boxes" style="width:50px;<?=$isBhTagInOrderEntryDisplay; ?>" onChange="fnc_style_check();">
                            	<option value="0">--</option>
                                <option value="1">BH</option>
                                <option value="2">Others</option>
                            </select>
                        </td>
                        <td>
                        	<input class="text_boxes" type="text" style="width:120px" placeholder="Browse/Write" name="txt_style_ref" id="txt_style_ref" onDblClick="open_qoutation_popup('requires/order_entry_controller.php?action=quotation_id_popup','Quotation ID Selection Form')" readonly/>	
                        </td>
                        <td>Style Description</td>
                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_style_description" id="txt_style_description" /></td>
                        <td>Sub. Dept </td>
                        <td id="sub_td"><? echo create_drop_down( "cbo_sub_dept", 130, $blank_array,"", 1, "-- Select Sub Dep --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                    	<td>Brand</td>
                        <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
                        <td>Season Year</td>
                        <td><?=create_drop_down("cbo_season_year",130,create_year_array(),"",1,"-Year-", $selected, "" ); ?></td>
                        <td>Season<input type="hidden" name="is_season_must" id="is_season_must" style="width:50px;" class="text_boxes" /></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?>
                        <td>Currency</td>
                        <td><? echo create_drop_down( "cbo_currercy", 130, $currency,'', 0, "",2, "" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Team Leader</td>   
                        <td id="leader_td"><? 
						echo create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $teamId, "load_drop_down( 'requires/order_entry_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );load_drop_down( 'requires/order_entry_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ); " );
                        ?></td>
                        <td class="must_entry_caption">Dealing Merchant</td>   
                        <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                        <td class="must_entry_caption">Factory Merchant</td>   
                        <td id="div_marchant_factory"><?=create_drop_down( "cbo_factory_merchant", 130, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                        <td>Region</td>
                        <td><?=create_drop_down( "cbo_region", 130, $region,'', 1, "-- Select Region --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Order Uom</td>
                        <td><?=create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "open_set_popup(this.value,this.id)","","1,58" ); ?>
                        	<input type="button" id="set_button" class="image_uploader" style="width:75px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value,this.id)" /> 
                        </td>
                        <td class="must_entry_caption">SMV</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="tot_smv_qnty" id="tot_smv_qnty" readonly/></td>
                        <td class="must_entry_caption">Product Category</td>
                        <td><?=create_drop_down( "txt_item_catgory", 130, $product_category,"", 1, "-- Select Product Category --", 1, "","","" ); ?></td>
                        <td>Agent </td>
                        <td id="agent_td"><?=create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                    	<td title="Working Company">W.Company</td>
                        <td><?=create_drop_down( "cbo_working_factory", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select W.Company-", $selected, "get_sew_company_config(this.value);"); ?></td>
                        <td title="Working Location">W.Location</td>
                        <td id="sew_location"><?=create_drop_down( "cbo_working_location_id", 130, $blank_array,"", 1, "-- Select Location --", $selected, ""); ?></td>
                        
                        <td>Style Owner</td>
                        <td><?=create_drop_down( "cbo_style_owner", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Style Owner--", $selected, ""); ?></td>
                        <td>Client</td>
                        <td id="party_type_td"><?=create_drop_down( "cbo_client", 130, $blank_array,"", 1, "-- Select Client --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td>Packing </td>
                        <td><?=create_drop_down( "cbo_packing", 130, $packing,"", 1, "--Select--", $selected, "","","" ); ?></td>
                        <td>Quality Level</td>
                        <td><?=create_drop_down( "cbo_qltyLabel", 130, $quality_label,"", 1, "--Quality Level--", $selected, "" ); ?></td>
                        <td>Fit</td>
                        <td><?=create_drop_down( "cbo_fit_id", 130, $fit_list_arr,"", 1, "--Fit List--", $selected, "" ); ?></td>
                        <td>Order Nature</td>
                    	<td><?=create_drop_down( "cbo_order_nature", 130, $fbooking_order_nature,"", 1, "-- Select--", 0, "","","12,13,14" ); ?></td>
                    </tr>
                    <tr>
                    	<td>Product Group</td>
                        <td><?=create_drop_down( "cbo_product_group", 130, $fbooking_order_nature,"", 1, "-Product Group-", $selected, "","","21,22,23,24" ); ?></td>
						<td>Sustainability Standard</td>
						<td><?=create_drop_down( "cbo_sustainability_standard", 130, $sustainability_standard,"", 1, "-- Select--", 0, "","","" ); ?></td>
                    	<td>Repeat No</td>
                        <td><input style="width:20px;" class="text_boxes_numeric" name="txt_repeat_no" id="txt_repeat_no" />&nbsp;<input style="width:65px;" class="text_boxes" name="txt_repeat_job_no" id="txt_repeat_job_no" onDblClick="repeat_openmypage( 'requires/order_entry_controller.php?action=repeat_job_popup', 'Repeat Job Form');" placeholder="Browse Job" /> &nbsp;<input type="checkbox" name="chk_is_repeat" id="chk_is_repeat" onClick="copy_check(7);" value="2" style="width:12px;" title="Is Repeat No? If Uncheck Then Yes." ></td>
                        <td>Ship Mode</td>
                        <td><?=create_drop_down( "cbo_ship_mode", 130,$shipment_mode, 1, "", $selected, "" ); ?></td>
                    </tr>
					<tr>
                    	<td> Fab. Material</td>
                        <td>
                            <? 
                                $fab_material=array(1=>"Organic",2=>"BCI");
                                echo create_drop_down( "cbo_fab_material", 130, $fab_material,"", 1, "-- Select--", 0, "","","" ); 
                            ?>
                        </td>
                    	<td>Remarks</td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:350px;"  name="txt_remarks" id="txt_remarks"/></td>
                        <td colspan="2"><input type="button" class="image_uploader" style="width:70px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 2 ,1)">
                        <input type="button" id="set_button" class="image_uploader" style="width:70px;" value="Internal Ref" onClick="open_terms_condition_popup('requires/order_entry_controller.php?action=terms_condition_popup','Terms Condition')" />
                        <input type="button" class="image_uploader" style="width:70px" value="IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1)">
                        
                    </tr>
                    <tr>
                    	<td colspan="7" id="budgetApp_td" style="font-size:18px; color:#F00" align="center"></td>
                        <td align="center" valign="middle">
                            <input type="hidden" id="update_id"> 
                            <input type="hidden" id="txt_quotation_id">
                            <input type="hidden" id="txt_quotation_price">
                            <input type="hidden" id="set_breck_down" />     
                            <input type="hidden" id="item_id" />
                            <input type="hidden" id="tot_set_qnty" /> 
                            <input type="hidden" id="hid_excessCut_source" />
                            <input type="hidden" id="hid_cost_source" />
                            <input type="hidden" id="hid_copy_quotion" />
                            <input type="hidden" id="hid_variable_pubshipdate" />
                            <input type="hidden" id="po_control_booking" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle" class="button_container">
                        	<?=load_submit_buttons( $permission, "fnc_order_entry", 0,1,"reset_form('orderentry_1*orderentry_2','po_list_view','','','fnc_resetPoDtls()','cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_season_id*cbo_region*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*cbo_packing*cbo_style_owner*hid_excessCut_source');",1); ?>
							<input type="button" id="Print_b2" value="Print B2" width="150" class="formbutton" onClick="fnc_order_entry(5);"/>
                            <input type="button" id="reorder" value="Color Size Sequence" width="120" class="image_uploader" onClick="reorder_size_color();"/>
                        </td>
                    </tr>
                    <tr><td colspan="8">
                        <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" rules="all" border="1">
                            <thead>
                                <th>Job Qty.</th>
                                <th>Avg Unit Price</th>
                                <th>Total Price</th>
                                <th>Projected Job Qty.</th>
                                <th>Projected Job Avg Unit Price</th>
                                <th>Projected Job Total Price</th>
                                <th>Original Projected Qty.</th>
                                <th>Original Avg Unit Price</th>
                                <th>Original Total Price</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input name="txt_job_qty" id="txt_job_qty" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_avgUnit_price" id="txt_avgUnit_price" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_total_price" id="txt_total_price" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_proj_qty" id="txt_proj_qty" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_proj_avgUnit_price" id="txt_proj_avgUnit_price" class="text_boxes_numeric" type="text" style="width:60px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_proj_total_price" id="txt_proj_total_price" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_orginProj_qty" id="txt_orginProj_qty" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_orginProj_total_price" id="txt_orginProj_total_price" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="Display" readonly/></td>
                                    <td><input name="txt_orginProj_total_amt" id="txt_orginProj_total_amt" class="text_boxes_numeric" type="text" style="width:70px;" placeholder="Display" readonly/></td>
                                </tr>
                            </tbody>
                        </table>
                    </td></tr>
                </table>
                </form>
            </fieldset>
            </div>
            </td>
            <td id="po_list_view" valign="top" align="left" width="470"></td> 
			<td id="act_po_list_view" valign="top" align="right" width="320"></td>           
        </tr>
    </table>
    <div align="left">
        <fieldset style="width:1240px;">
        <legend>PO Details Entry</legend>
            <form id="orderentry_2" autocomplete="off">
                <table cellpadding="0" cellspacing="2" border="0" >
                    <thead class="form_table_header">
                        <tr height="20">
                            <th class="must_entry_caption" colspan="2">Breakdown Type</th>
                            <th colspan="4"><?=create_drop_down( "cbo_breakdown_type", 250, $breakdown_type, "", 0,"--Select Breakdown Type--", '1',"fnc_noof_carton(this.value);", "","","","","4" ); ?></th>
                            <th colspan="2">Round Type</th>
                            <th><? $round_type=array(1=>"Round Up",2=>"Round Down"); echo create_drop_down( "cbo_round_type", 55, $round_type, "", 1,"-Select-", 0,"", 1 ); ?></th>
                            <th colspan="2"><strong>Copy PO</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled ></th>
                            <th colspan="3"><strong>Copy PO No</strong><input class="text_boxes" name="txt_copypo_no" id="txt_copypo_no" type="text" style="width:75px" onBlur="fnc_copy_po(0,document.getElementById('update_id_details').value,document.getElementById('txt_po_no').value);" disabled/></th>
                            <th colspan="4">&nbsp;</th>
                        </tr>
                        <tr height="20">
                            <th width="80">Order Status</th>
                            <th width="105" class="must_entry_caption">PO No</th>
                            <th width="60" class="must_entry_caption">PO Receive Date</th>
                            <th width="60" class="must_entry_caption">Pub. Ship. Date</th>
                            <th width="60">Org. Ship. Date</th>
                            <th width="60">Fac. Receive Date</th>
							<th width="60" title="Pack Handover Date/Planned Cut Date" id="txt_phd_id">PHD/PCD</th>
                            <th width="55" class="must_entry_caption" id="rate_td">Avg. Rate Pcs/Set</th>
                            <th width="40">Up Charge</th>
                            <th width="65" id="shtQty_td" class="must_entry_caption">Order Qty</th>
                            <th width="55">No Of Carton</th>
                            <th width="80">Projected Po</th>
                            <th width="80">TNA From /Upto</th>
                            <th width="70">Int. Ref/ Grouping</th>
                            <th width="70">File Year</th>
                            <th width="60">Common File No/CN</th>
                            <th width="80">Packing</th>
                            <th>Actual PO No.</th>
                        </tr> 
                    </thead>
                    <tr>
                        <td><?=create_drop_down( "cbo_order_status", 80, $order_status, 0, "", $selected,"", "po_recevied_date( this.value );" ); ?></td>
                        <td><input class="text_boxes" name="txt_po_no" id="txt_po_no" type="text" placeholder="Write" style="width:95px" onBlur="fnc_buyer_style_po_check(this.value);"/></td>
                        <td><input name="txt_po_received_date" id="txt_po_received_date" class="datepicker" type="text" value="<?=date('d-m-Y'); ?>" onChange="set_tna_task(); set_phd_po_date(2);" style="width:50px;" readonly/></td>
                        <td><input name="txt_pub_shipment_date" id="txt_pub_shipment_date" class="datepicker" type="text" onChange="set_tna_task();set_original_ship_date();" style="width:50px;" readonly/></td>
                        <td><input name="txt_org_shipment_date" id="txt_org_shipment_date" class="datepicker" type="text" onChange="set_pub_ship_date();" style="width:50px;" readonly/></td>
                        <td><input name="txt_factory_rec_date" id="txt_factory_rec_date" class="datepicker" type="text" value="" style="width:50px;" readonly/></td>
						<td><input name="txt_phd" id="txt_phd" class="datepicker" type="text"  style="width:50px;" onChange="set_phd_po_date(1);" readonly/></td>
                        <td><input name="txt_avg_price" id="txt_avg_price" class="text_boxes_numeric" type="text" style="width:45px;" onBlur="fnc_calculateRate(document.getElementById('cbo_breakdown_type').value,0);" /></td>
                        <td><input name="txt_upCharge" id="txt_upCharge" class="text_boxes_numeric" type="text" style="width:30px;" /></td>
                        <td><input name="txt_docSheetQty" id="txt_docSheetQty" class="text_boxes_numeric" type="text" style="width:55px;" /></td>
                        <td><input name="txt_noOf_carton" id="txt_noOf_carton" class="text_boxes_numeric" type="text" style="width:45px;" disabled /></td>
                        <td id="projected_po_td"><?=create_drop_down( "cbo_projected_po", 80,$blank_array, "", 1, "--Select--", "" ); ?></td>
                        <td id="tna_task_td"><?=create_drop_down( "cbo_tna_task", 80,$blank_array, "", 1, "--Select--", "" ); ?></td>
                        <td><input type="text" id="txt_grouping" name="txt_grouping" class="text_boxes" style="width:60px"></td>
                        <td id="file_year_td"><?=create_drop_down( "txt_file_year", 70, $blank_array,"", 1, "--Select--", "", "","","" ); ?></td>
                       <!--  <td><input type="text" id="txt_file_no" name="txt_file_no"  class="text_boxes_numeric" style="width:50px"></td> change for issue id ==17735 --> 
						<td><input type="text" id="txt_file_no" name="txt_file_no"  class="text_boxes" style="width:50px"></td>
                        <td><?=create_drop_down( "cbo_packing_po_level", 80, $packing,"", 1, "--Select--", "", "","","" ); ?></td>
                        <td><input type="button" id="reorder" value="Actual PO" style="width:60px" class="image_uploader" onClick="pop_entry_actual_po();"/></td>
                    </tr>
                    <tr>
                        <td><strong>Remarks</strong></td><td colspan="7"><input name="txt_po_remarks" id="txt_po_remarks" class="text_boxes" type="text" style="width:400px;"/></td>
                        <th><strong>Delay For</strong></th>
                        <td colspan="3"><?=create_drop_down( "cbo_delay_for", 155, $delay_for, 0, "", 1, "" ); ?></td>
                        <td align="right"><strong>Po Status</strong></td>
                        <td><?=create_drop_down( "cbo_status", 60, $row_status, 0, "", 1, "" ); ?></td>
                    	<td align="right"><strong>SC/LC:</strong></td><td><input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" style="width:60px"></td>
                    </tr>
                </table>
                <table width="1150">
                    <tr><td width="860">
                        <table rules="all" width="860" cellpadding="0" cellspacing="2" border="1" class="rpt_table">
                            <thead>
                                <th class="must_entry_caption">Garment Item</th>
                                <th class="must_entry_caption">Delivery Country</th>
                                <th>Code</th>
                                <th>Country</th>
                                <th>Country Code</th>
                                <th>Cut-off Date</th>
                                <th>Cut-Off</th>
                                <th class="must_entry_caption">Country Ship Date</th>
                                <th id="pack_type">Pack Type</th>
                                <th id="pcsQty_td">Pcs Per Pack</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="itm_td"><?=create_drop_down( "cbo_gmtsItem_id", 120, $garments_item, 0, 1, "--Select Item--", $selected_item,"fnc_calAmountQty_ex(0,1); fnc_calculateRate(document.getElementById('cbo_breakdown_type').value,0);",1);  ?></td>
                                    <td><?=create_drop_down( "cbo_deliveryCountry_id", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--Select Country--", "","load_drop_down( 'requires/order_entry_controller', this.value, 'load_dorp_down_code', 'code_td' ); fnc_cut_off_select(this.value); fnc_set_ship_date(); fnc_calculateRate(document.getElementById('cbo_breakdown_type').value,0);" ); ?><input type="hidden" id="hid_prev_country" /> </td>
                                    <td id="code_td"><?=create_drop_down( "cbo_code_id", 100,$blank_array, "", 1, "--Select Code--", "","" ); ?></td>
                                    <td><?=create_drop_down( "cbo_country_id", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--Country--", ""," load_drop_down( 'requires/order_entry_controller', this.value, 'load_dorp_down_countryCode', 'countryCode_td' );" ); ?></td>
                                    <td id="countryCode_td"><?=create_drop_down( "cbo_countryCode_id", 100,$blank_array, "", 1, "--Country Code--", "","" ); ?></td>
                                    <td><input name="txt_cutup_date" id="txt_cutup_date" class="datepicker" type="text" style="width:65px;" onChange="fnc_set_ship_date();"/></td>
                                    <td><?=create_drop_down( "cbo_cutOff_id", 100, $cut_up_array, 0, 1, "--Select--", "","fnc_set_ship_date();" );  ?></td>
                                    <td><input name="txt_countryShip_date" id="txt_countryShip_date" class="datepicker" type="text" style="width:65px;"/>
                                    	<input name="txt_is_update" id="txt_is_update" class="text_boxes" type="hidden" style="width:30px;" value="0"/></td>
                                    <td><input name="txt_breakdownGrouping" id="txt_breakdownGrouping" class="text_boxes" maxlength="5" placeholder="5 Char."type="text" style="width:40px;" onBlur="fnc_packtype(this.value);" disabled/></td>
                                    <td><input name="txt_pcsQty" id="txt_pcsQty" class="text_boxes_numeric" type="text" style="width:40px;" disabled/></td>
                                </tr>
                            </tbody>
                        </table>
                    </td><td>
                        <table rules="all" width="240" cellpadding="0" cellspacing="2" border="1" class="rpt_table">
                            <thead>
                                <th width="60" title="All Size All Color.">Copy Qty. ASAC</th>
                                <th width="60" title="All Size Same Color.">Copy Qty. ASSC</th>
                                <th width="60" title="All Color Same Size.">Copy Qty. ACSS</th>
                                <th width="60">Copy Ex.Cut %</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="60" align="center"><input type="checkbox" name="copy_asac" id="copy_asac" onClick="copy_check(2);" value="2" ></td>
                                    <td width="60" align="center"><input type="checkbox" name="copy_assc" id="copy_assc" onClick="copy_check(3);" value="2" ></td>
                                    <td width="60" align="center"><input type="checkbox" name="copy_acss" id="copy_acss" onClick="copy_check(4);" value="2" ></td>
                                    <td width="60" align="center"><input type="checkbox" name="copy_excut" id="copy_excut" onClick="copy_check(5);" value="2" ></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    </tr>
                </table>
                <table width="1050" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="110" valign="top">
                            <table rules="all" cellpadding="0" cellspacing="1" border="0" class="rpt_table" align="left" id="color_tbl">
                                <thead>
                                    <th>SL</th>
                                    <th>Color</th>
                                </thead>
                                <tbody id="td_color">
                                    <tr id="trColor_1">
                                        <td align="center">1</td>
                                        <td><input name="txtColorName[]" id="txtColorName_1" class="text_boxes" type="text" style="width:80px;" onKeyUp="append_color_size_row(1);"/><input name="txtColorId[]" id="txtColorId_1" class="text_boxes" type="hidden" style="width:50px;"/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="90" valign="top">
                            <table rules="all" cellpadding="0" cellspacing="1" border="0" class="rpt_table" align="left" id="size_tbl">
                                <thead>
                                    <th>Size</th>
                                </thead>
                                <tbody id="td_size">
                                    <tr id="trSize_1">
                                        <td><input name="txtSizeName[]" id="txtSizeName_1" class="text_boxes" type="text" style="width:80px;" onKeyUp="append_color_size_row(2);"/><input name="txtSizeId[]" id="txtSizeId_1" class="text_boxes" type="hidden" style="width:50px;"/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="1000" valign="top">
                        	<div id="breakdownratio_div"></div><br>
                            <div id="breakdown_div"> </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top"><input type="button" id="btn" name="btn" value="Quantity Breakdown" class="formbuttonplasminus" style="width:150px;" onClick=" fnc_create_qty_breakdown(document.getElementById('txt_is_update').value,document.getElementById('cbo_breakdown_type').value); " /></td>
                        <td height="50" valign="middle" align="center" class="button_container">
                            <input type="hidden" id="update_id_details"/>
                            <input type="hidden" id="hidd_bhpo_id"/>
                            <input type="hidden" id="color_size_break_down" value="" />
                            <input type="hidden" id="color_size_break_down_all_data" value="" /> 
                            <input type="hidden" id="size_all_id" value="" /> 
                            <input type="hidden" id="color_size_ratio_data" value="" /> 
                            <input type="hidden" id="txt_po_datedif_hour" />   
                            <input type="hidden" id="txt_user_id" />  
                            	<?=load_submit_buttons( $permission, "fnc_order_entry_details", 0,0 ,"reset_form('orderentry_2','','','','fnc_resetPoDtls()','txt_po_received_date');",2); ?>
                            <input type="hidden" id="reset_btn" class="formbutton" style="width:80px" value="Delete PO No" onClick="fnc_delete_po(2);" />
                        </td>
                    </tr>
                 </table>
            </form>
        </fieldset>
    </div>
    <div id="country_po_list_view"></div>
    <!--<div id="deleted_po_list_view"></div>-->
	</div>
    	<div id="button_container" align="center" style="padding: 10px;"></div>
	</body>
    
    <script>set_multiselect('cbo_delay_for','0','0','','');</script>        
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
	$('#cbo_buyer_name').val('0');
    function fnc_dealing()
	{
		load_drop_down( 'requires/order_entry_controller', '<? echo $teamId;?>', 'cbo_dealing_merchant', 'div_marchant' );
		load_drop_down( 'requires/order_entry_controller', '<? echo $teamId;?>', 'cbo_factory_merchant', 'div_marchant_factory' )
	}
    </script>
</html>