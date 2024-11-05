<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Woven Garments Order Entry
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	29-11-2015
Updated by 		:	zakaria joy
Update date		:	01-03-2018
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
$data_level_secured=$_SESSION['logic_erp']["data_level_secured"];
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');
$mst_mandatory_fields=array();
$mst_mandatory_msg=array();
$po_mandatory_fields=array();
$po_mandatory_msg=array();

if(count($_SESSION['logic_erp']['mandatory_field'][351])>0)
{
	foreach ($_SESSION['logic_erp']['mandatory_field'][351] as $key => $field) {
		if($key==1 || $key==2 || $key==3 || $key==4)
		{			
			$po_mandatory_fields[$key] = $field;
		}
		else{
			$mst_mandatory_fields[$key] = $field;
		}
	}

	foreach ($_SESSION['logic_erp']['mandatory_message'][351] as $key => $field) {
		if($key==1 || $key==2 || $key==3 || $key==4)
		{			
			$po_mandatory_msg[$key] = $field;
		}
		else{
			$mst_mandatory_msg[$key] = $field;
		}
	}
}
?>
<script>

	$( document ).ready(function() {
		<?
			if(!empty($_SESSION['logic_erp']['mandatory_field'][351][1]))
			{
				echo "document.getElementById('txt_grouping_id').style.color = 'blue';\n";
			}
			if(!empty($_SESSION['logic_erp']['mandatory_field'][351][2]))
			{
				echo "document.getElementById('file_year_id').style.color = 'blue';\n";
			}
			if(!empty($_SESSION['logic_erp']['mandatory_field'][351][3]))
			{
				echo "document.getElementById('file_no_id').style.color = 'blue';\n";
			}
			if(!empty($_SESSION['logic_erp']['mandatory_field'][351][10]))
			{
				echo "document.getElementById('style_desc_id').style.color = 'blue';\n";
			}
			if(!empty($_SESSION['logic_erp']['mandatory_field'][351][12]))
			{
				echo "document.getElementById('seasioncation_td').style.color = 'blue';\n";
			}
		?>
	});

	function lib_mandatory_check(company_id){
		var all_lib_settings=return_ajax_request_value(company_id, 'load_lib_mandatory_settings', 'requires/order_entry_controller');
		var ex_variable=all_lib_settings.split("_");
		if(ex_variable[0]==1)
		{
			document.getElementById('image_button_front').style.color = 'blue';
			<?
			$field="image_button_front";
			$mst_mandatory_fields[11] = $field;
				?>
		}else{
			document.getElementById('image_button_front').style.color = '';
		}
		if(ex_variable[1]==1)
		{
			//kausar
		}else{
			document.getElementById('seasioncation_td').style.color = '';
		}
	}

	var permission='<? echo $permission; ?>';
	var isBhTagInOrderEntry='<?=$isBhTagInOrderEntry; ?>';
	var isBhTagInOrderEntryDisplay="";
	if(isBhTagInOrderEntry!=1) { var isBhTagInOrderEntryDisplay="display:none"; }//ISD-23-26871
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var field_level_data="";
	<?
		if(isset($_SESSION['logic_erp']['data_arr'][351]))
		{

		  $data_arr=json_encode($_SESSION['logic_erp']['data_arr'][351] );
		  echo "var field_level_data= ". $data_arr . ";\n";
		}
		echo "var mst_mandatory_field = '". implode('*',$mst_mandatory_fields) . "';\n";
		echo "var mst_mandatory_message = '". implode('*',$mst_mandatory_msg) . "';\n";
		echo "var po_mandatory_field = '". implode('*',$po_mandatory_fields) . "';\n";
		echo "var po_mandatory_message = '". implode('*',$po_mandatory_msg) . "';\n";
	?>
	// Master Form-----------------------------------------------------------------------------
	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size order by size_name ASC", "size_name" ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color order by color_name ASC", "color_name" ), 0, -1); ?>];
	
	function fnc_mandatorytdcolor()
	{
		var celid=mst_mandatory_field.split("*")
		//alert( celid.length+"="+mandatory_field+"="+celid)
		var a=0;
		for (var i = 1; i <= celid.length; i++)
		{
			var td=$('#'+celid[a]).val();
			//alert(td+'='+celid[a])
			$('#'+celid[a]).closest('td').prev().css('color', 'blue');
			a++;
		}
		
		var celid=po_mandatory_field.split("*")
		//alert( celid.length+"="+po_mandatory_field+"="+mst_mandatory_field)
		var a=0;
		for (var i = 1; i <= celid.length; i++)
		{
			var td=$('#'+celid[a]).val();
			//alert(td+'='+celid[a])
			$('#'+celid[a]).closest('td').prev().css('color', 'blue');
			a++;
		}
	}
	
	function change_country_code(id)
	{
		$('#cbo_countryCode_id').val(id);
		/*var color_table =$('#color_tbl tbody tr').length;
		for(var i=1;i<=color_table;i++)
		{
			if($("#txtColorName_"+i).val()!="")
			{
				$("#txtColorName_"+i).attr("disabled",true);
			}
		}*/
	}
	
	function change_country(id)
	{
		$('#cbo_deliveryCountry_id').val(id);		
		$('#cbo_deliveryCountry_id').change();
	}
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
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
		var sew_company_location=ex_variable[10];
		var editable=ex_variable[11];
		var styleeditable=ex_variable[12];
		var act_po_id=ex_variable[13]
		$('#act_po_id').val( act_po_id );

		if(sew_company_location!=0) $('#sewing_company_validate_id').val( sew_company_location ); else $('#sewing_company_validate_id').val( 0 );
		//alert(set_smv_id);
		if(set_smv_id==4 || set_smv_id==3) //WS+PQ+OE+PC
		{
			$('#item_smv_check').val(set_smv_id);
			$('#smv_1').attr('disabled','disabled');
		}
		else
		{
			$('#smv_1').removeAttr('disabled','');
		}
		var color_from_lib=ex_variable[9];

		if(po_update_period==1)
		{
			if(po_current_date==1 && order_status==1)
			{
				$('#txt_po_received_date').attr('disabled','disabled');
				$('#txt_po_received_date').val(current_date);
			}
			else
			{
				$('#txt_po_received_date').val('');
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
				$('#txt_po_received_date').val('');
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
				if(styleeditable==1)
				{
					$('#txt_style_ref').attr('readonly',false);
					$('#txt_style_ref').attr('placeholder','Write/Browse');
				}
				else
				{
					$('#txt_style_ref').attr('readonly',true);
					$('#txt_style_ref').attr('placeholder','Browse');
				}
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
			
			if(set_smv_id==4) 
			{
				$("#seasioncation_td").css('color','blue');
				//$('#cbo_season_id').attr('disabled',true);
			}
			else 
			{
				$("#seasioncation_td").css('color','black');
				//$('#cbo_season_id').attr('disabled',false);
			}
			
			/*$('#txt_style_ref').attr('readonly',true);
			$('#txt_style_ref').attr('placeholder','Browse');
			var page_link="'requires/order_entry_controller.php?action=quotation_id_popup','Quotation ID Selection Form'";
			$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");*/
		}
		else
		{
			$('#txt_style_ref').attr('readonly',false);
			$('#txt_style_ref').attr('placeholder','Write');
			$('#txt_style_ref').removeAttr('onDblClick','onDblClick');
			if(set_smv_id==1 && styleeditable==1)
			{
				$('#txt_style_ref').removeAttr("onDblClick").attr("onBlur","fnc_duplicte_style_check("+company_id+");");
			}
		}
		
		var bhstyle=$('#cbo_style_from').val(); //ISD-23-26871
		if(bhstyle==1)//ISD-23-26871
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

		if(season_mandatory==1) $('#is_season_must').val( season_mandatory );
		if(excut_source!=0) $('#hid_excessCut_source').val( excut_source );
		if(editable!=0) $('#hid_excessCut_editable').val( editable );
		if(cost_control_source!=0) $('#hid_cost_source').val( cost_control_source );
		if(set_smv_id!=0) $('#set_smv_id').val( set_smv_id ); else $('#set_smv_id').val( 0 );
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
		set_field_level_access(company_id);
	}
	
	function fnc_duplicte_style_check(companyid)//ISD-22-23393
	{
		var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		if(trim(txt_style_ref)!="")
		{
			var duplicate_style_check=return_ajax_request_value(companyid+'_*_'+txt_style_ref+'_*_'+$('#txt_job_no').val(), 'load_duplicate_style', 'requires/order_entry_controller');
			var exstyle_check=duplicate_style_check.split("_");
			var flag=exstyle_check[0];
			var jobno=exstyle_check[1];
			if(flag==1)
			{
				if( confirm("Style Duplicate Found. Job No: "+jobno) )
					void(0);
				else
				{
					$('#txt_style_ref').val('');
				}
			}
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
				var excolor_name=color_name.split("__");//ISD-23-22211
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
		var marketing_team_id=data[2];
		var cut_off_used=data[3];
		if(tna==1 && temp==0)
		{
			alert("TNA Intregeted, But TNA templete not found for this Buyer; please set templete first.")
			$('#cbo_buyer_name').val(0);
		}
		$('#cut_off_used').val(cut_off_used);

		//===============crm====26060===as per common team decision===============21-11-2021================
		// if(marketing_team_id>0){  
		// 	$('#cbo_team_leader').val(marketing_team_id);
		// 	$('#cbo_team_leader').change();
		// }
		
	}

	function open_qoutation_popup(page_link,title)
	{
		if( form_validation('cbo_company_name','Company Name')==false)
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
			
			var bhstyle=$('#cbo_style_from').val(); //ISD-23-26871
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
				var set_item_down=this.contentDoc.getElementById("set_breck_down");
				var unit_id=this.contentDoc.getElementById("unit_id");
				var cost_source=$('#hid_cost_source').val();
				//alert(cost_source+'_'+set_smv_id);
				//var hid_cost_source=$('#hid_cost_source').val()*1;
				if (theemail.value!="")
				{
					freeze_window(5);
					$('#tot_smv_qnty').val('');
					if(bhstyle==1)//ISD-23-26871
					{
						get_php_form_data(theemail.value, "populate_data_from_bh_style_popup", "requires/order_entry_controller");
						var set_breck_down=$('#set_breck_down').val();
						var item_smv_check=$('#item_smv_check').val();
						var datas=set_breck_down+'***'+item_smv_check;
						var list_view_tr = return_global_ajax_value( datas+'**'+bhstyle, 'load_php_dtls_form', '', 'requires/order_entry_controller');
						show_list_view("0*0*"+bhstyle+"*"+$('#txt_quotation_id').val()+"*"+$('#cbo_company_name').val(),'order_listview','po_list_view','requires/order_entry_controller','setFilterGrid(\'tbl_po_list\',-1)');
					}
					else
					{
						if(set_smv_id==3 || set_smv_id==8 || set_smv_id==9)
						{
							$('#txt_style_ref').val(theemail.value);
						}
						else
						{
							if(cost_source==2)
							{	
								get_php_form_data( theemail.value+"_"+document.getElementById("cbo_company_name").value, "populate_data_from_search_popup_quotation", "requires/order_entry_controller" );
								if(set_smv_id==4) 
								{
									$("#seasioncation_td").css('color','blue');
									$('#cbo_season_id').attr('disabled',true);
								}
								var item_smv_check=$('#item_smv_check').val();
								var datas=set_item_down.value+'***'+item_smv_check;
								var list_view_tr = return_global_ajax_value( datas, 'load_php_dtls_form', '', 'requires/order_entry_controller');
							}
							else if (cost_source==1 || cost_source==4)//QC
							{
								get_php_form_data( theemail.value, "populate_data_from_search_popup_qc", "requires/order_entry_controller" );//else 
								$("#seasioncation_td").css('color','black');
								var set_breck_down=$('#set_breck_down').val();
								var item_smv_check=$('#item_smv_check').val();
								var datas=set_breck_down+'***'+item_smv_check;
								var list_view_tr = return_global_ajax_value( datas, 'load_php_dtls_form', '', 'requires/order_entry_controller');
								
								//$('#cbo_season_id').attr('disabled',false);
							}
							if(set_smv_id==2) $('#cbo_order_uom').attr('disabled',true);
							
							var location_id=document.getElementById("cbo_location_name").value;
							//get_php_form_data( theemail.value, "populate_data_from_search_popup_qc", "requires/order_entry_controller" );
							if(location_id==0)
							{
								load_drop_down('requires/order_entry_controller',document.getElementById("cbo_company_name").value, 'load_drop_down_location', 'location' );
							}
							location_select();
						}
					}
					release_freezing();
				}
				 
				if(trim(list_view_tr)!='' && trim(set_smv_id)!=3 )
				{
					$("#tbl_set_details tbody tr").remove();
					$("#tbl_set_details tbody").append(list_view_tr);
					set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
					set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
					set_sum_value_set( 'tot_smv_qnty_total', 'smvset_' );
					set_all_onclick();
					document.getElementById("quotation_id").value=theemail.value;
					document.getElementById("unit_id").value=unit_id.value;
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

		if(unit_id==58)
		{
			pcs_or_set="Item Details For Set";
		}
		else if(unit_id==57)
		{
			pcs_or_set="Item Details For Pack";
		}
		else
		{
			pcs_or_set="Item Details For Pcs";
		}

		var page_link="requires/order_entry_controller.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&tot_smv_qnty='+tot_smv_qnty+'&precostfound='+data[0]+'&precostapproved='+data[2]+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=1150px,height=300px,center=1,resize=1,scrolling=0','../')
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
	//smv

		var tot_smv_qnty=$('#tot_smv_qnty').val();
		if(tot_smv_qnty*1<=0){
			alert("Insert SMV");
			release_freezing();
			return;
		}
		if(mst_mandatory_field !=''){
			if (form_validation(mst_mandatory_field,mst_mandatory_message)==false){
				release_freezing();
				return;
			}
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
		}
		var buyer_name=get_dropdown_text('cbo_buyer_name');
		var buyer_id=$("#cbo_buyer_name").val();
		if((buyer_name=='TORAY-GU TRPU' && buyer_id==24) || (buyer_name=='TORAY-N.MATSUYA' && buyer_id==28) || (buyer_name=='TORAY-MUJI' && buyer_id==27))
		{
			alert("This buyer is not availabe for any Style Ref. in ERP. If need Please contract with MIS.");
			release_freezing();
			return;
		}

		var sewing_company_validate_id=document.getElementById('sewing_company_validate_id').value*1;
		if(sewing_company_validate_id==1)
		{
			if (form_validation('cbo_working_factory*cbo_working_location_id','Working Company*Working Location')==false){
				release_freezing();
				return;
			}
		}

		if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_product_department*txt_item_catgory*cbo_dealing_merchant*tot_smv_qnty','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*SMV')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var set_smv_id=$('#item_smv_check').val();
			var is_season_must=$('#is_season_must').val()*1;
			if(is_season_must==1 || set_smv_id==4)
			{
				if($('#cbo_season_id').val()==0)
				{
					alert('Season not blank.');
					$('#cbo_season_id').focus();
					release_freezing();
					return;
				}
			}
			js_set_value_set();
			
			var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
			var styledata='&txt_style_ref='+"'"+txt_style_ref+"'";
			
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_job_no*garments_nature*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_description*cbo_product_department*txt_product_code*cbo_sub_dept*cbo_currercy*cbo_agent*cbo_client*txt_repeat_no*cbo_region*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*txt_remarks*cbo_ship_mode*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*tot_smv_qnty*txt_quotation_id*update_id*cbo_season_id*cbo_factory_merchant*cbo_qltyLabel*cbo_fit_id*cbo_style_owner*chk_is_repeat*set_smv_id*cbo_ready_for_budget*txt_req_no*cbo_season_year*cbo_brand_id*txt_bodywashColor*hidd_job_id*cbo_working_location_id*cbo_working_factory*hidd_inquery_id*cbo_order_criteria*cbo_style_from',"../../")+styledata;
			//alert(data);
			
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
			/*if(reponse[0]=='quataNotApp')
			{
				alert("Price quotation is not fully approved. Please approve the price quotation.");
				release_freezing();
				return;
			}*/
			if(reponse[0]=="SMV"){
				alert("Insert SMV in item part")
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
			//var alt_budget_msg="Delete Restricted, Budget Found";
			if(reponse[0]==14)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='QcNotApp')
			{
				alert("Quick Costing Woven is not Approved. Please Approved the Quick Costing Woven");
				release_freezing();
				return;
			}
			
			if(reponse[0]==15)
			{
				release_freezing();
				setTimeout('fnc_order_entry('+ reponse[0]+')',8000);
			}
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_job_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('txt_repeat_no').value=reponse[3];
				$('#hidd_job_id').val(reponse[5]);
				load_drop_down( 'requires/order_entry_controller',reponse[4], 'load_drop_gmts_item', 'itm_td');
				var quotation_id=$('#txt_quotation_id').val()*1;
				if(parseInt(trim(reponse[0]))==0)
				{
					if(reponse[1]!='' && quotation_id!=0)
					{
						fnc_qoutation_img_copy(reponse[1],quotation_id);
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

	function fnc_qoutation_img_copy(job_no,quotation_id)
	{
		var company_name=document.getElementById('cbo_company_name').value;
		//alert(company_name+'_'+job_no+'_'+quotation_id);
		var job_image = return_ajax_request_value(job_no+'_'+quotation_id+'_'+company_name, 'quotation_image_copy_for_job', 'requires/order_entry_controller');
	}

	function openmypage_job(page_link,title){
		/*if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else{*/
			hide_left_menu("Button1");
			var cbo_company_name=$('#cbo_company_name').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
			var garments_nature=document.getElementById('garments_nature').value;
			page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=430px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];

				var theemail=this.contentDoc.getElementById("selected_job");
				var set_breck_down=this.contentDoc.getElementById("set_breck_down");
				var quotation_id=this.contentDoc.getElementById("quotation_id");
				var unit_id=this.contentDoc.getElementById("unit_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					var company_id=return_global_ajax_value(theemail.value, 'get_company_name', '', 'requires/order_entry_controller');
					//set_field_level_access(company_id);
					fnc_variable_settings_check(company_id);
					get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/order_entry_controller" );
					
					var current_date='<? echo date('d-m-Y'); ?>';
					reset_form('orderentry_2','breakdown_div*breakdownratio_div*country_po_list_view','','','');
					load_drop_down( 'requires/order_entry_controller', document.getElementById('txt_job_no').value, 'load_drop_down_projected_po', 'projected_po_td' );
					load_drop_down( 'requires/order_entry_controller', document.getElementById('item_id').value, 'load_drop_gmts_item', 'itm_td');
					show_list_view(theemail.value+"*0*"+$('#cbo_style_from').val()+"*"+$('#txt_quotation_id').val()+"*"+$('#cbo_company_name').val(),'order_listview','po_list_view','requires/order_entry_controller','setFilterGrid(\'tbl_po_list\',-1)');
					po_recevied_date();
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
					var item_smv_check=$('#item_smv_check').val();
					
					var datas=set_breck_down.value+'***'+item_smv_check+'***'+unit_id.value;
					var list_view_tr = return_global_ajax_value( datas, 'load_php_dtls_form', '', 'requires/order_entry_controller');
					if(list_view_tr != ''){
					$("#tbl_set_details tbody tr").remove();
					$("#tbl_set_details tbody").append(list_view_tr);
					set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
					set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
					set_sum_value_set( 'tot_smv_qnty_total', 'smvset_' );
					set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
					set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
					document.getElementById("quotation_id").value=quotation_id.value;						
					}
					document.getElementById("unit_id").value=unit_id.value;
					disable_smv_set();fnc_product_feild_disabled();
					set_field_level_access( $('#cbo_company_name').val() );
					set_button_status(1, permission, 'fnc_order_entry',1);
					set_button_status(0, permission, 'fnc_order_entry_details',2);

					release_freezing();
				}
			}
		//}
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

	function fnc_product_feild_disabled(){
		//var item_id=(document.getElementById('cboitem_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			$('#cboitem_'+k).attr('disabled','disabled');
			
		}
	}

	//Dtls Form-------------------------------------------------------------------------------------
	function fnc_order_entry_details( operation )
	{
		freeze_window(operation);
		var txt_job_no =$("#txt_job_no").val();
		var company_id =$("#cbo_company_name").val();
		var precost = return_ajax_request_value(txt_job_no, 'check_precost_approve', 'requires/order_entry_controller');
		var po_variable = return_ajax_request_value(company_id, 'check_po_entry_control', 'requires/order_entry_controller');
		if(operation==2){
			if (precost==1 || precost==3)
			{
				alert("Pre Cost Approved, Any Change will be not allowed.");
				release_freezing();
				return;
			}
		}
		else if(operation==1){
			if(po_variable ==1){
				if (precost==1 || precost==3)
				{
					alert("Pre Cost Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
		}
		else if(operation==0){
			if(po_variable ==3 || po_variable ==2){
				if (precost==1 || precost==3)
				{
					alert("Pre Cost Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
		}
		else{
			if(po_variable ==2){
				if (precost==1 || precost==3)
				{
					alert("Pre Cost Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
		}		
		
		if(po_mandatory_field !=''){
			if (form_validation(po_mandatory_field,po_mandatory_message)==false){
				release_freezing();
				return;
			}
		}

		if(form_validation('update_id*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_shipment_date*txt_avg_price*cbo_deliveryCountry_id*cbo_breakdown_type*cbo_gmtsItem_id*txt_countryShip_date','Master Info*PO Number*PO Received Date*Pub Shipment Date*Shipment Date*Avg. Rate Pcs/Set*Country*Break Down Type*Gmts Item*Country Ship Date')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			if(operation==1)
			{
				if($("#cbo_status").val()!=1)
				{
					var scLc_status = return_ajax_request_value(txt_job_no+"_"+$("#update_id_details").val(), 'sc_lc_status', 'requires/order_entry_controller')
					var ex_scLc_status=scLc_status.split("_");
					if(trim(ex_scLc_status[0])!="")
					{
						al_msgsc="";
						var al_msgsc="Sales Contract '"+trim(ex_scLc_status[0])+"' found and donot delete Order no,\n If Need Please delete the Sales Contract first.";
						alert(al_msgsc);
						release_freezing();
						return;
					}
					if(trim(ex_scLc_status[1])!="")
					{
						al_msglc="";
						var al_msglc="Export LC '"+trim(ex_scLc_status[1])+"' found and donot delete Order no,\n If Need Please delete the Export LC first.";
						alert(al_msglc);
						release_freezing();
						return;
					}
				}

			}
			else if( operation==2)
			{
				var scLc_status = return_ajax_request_value(txt_job_no+"_"+$("#update_id_details").val(), 'sc_lc_status', 'requires/order_entry_controller')
				var ex_scLc_status=scLc_status.split("_");
				//alert (scLc_status);
				if(trim(ex_scLc_status[0])!="")
				{
					al_msgsc="";
					var al_msgsc="Sales Contract '"+trim(ex_scLc_status[0])+"' found and donot delete Order no,\n If Need Please delete the Sales Contract first.";
					alert(al_msgsc);
					release_freezing();
					return;
				}
				if(trim(ex_scLc_status[1])!="")
				{
					al_msglc="";
					var al_msglc="Export LC '"+trim(ex_scLc_status[1])+"' found and donot delete Order no,\n If Need Please delete the Export LC first.";
					alert(al_msglc);
					release_freezing();
					return;
				}
			}

			if(operation==1 || operation==2)
			{
				var booking_status = return_ajax_request_value(txt_job_no+"_"+$("#update_id_details").val(), 'check_booking_withpo', 'requires/order_entry_controller');
				var reponse=booking_status.split('**');
				var alt_booking_msg="Update or Delete Restricted, Booking Found, Booking No: "+reponse[1];
				if(reponse[0]==13)
				{
					alert(alt_booking_msg);
					release_freezing();
					return;
				}

			}
			var txt_quotation_id=(document.getElementById('txt_quotation_id').value)*1;
			var txt_quotation_price=(document.getElementById('txt_quotation_price').value)*1;
			var txt_avg_price=(document.getElementById('txt_avg_price').value)*1;
			var hid_cost_source=$('#hid_cost_source').val()*1;
			var hid_copy_quotion=$('#hid_copy_quotion').val()*1;
			console.log(hid_cost_source+'--'+hid_copy_quotion+'--'+txt_quotation_id+'--'+txt_avg_price+'--'+txt_quotation_price);
			if( hid_cost_source==2 || hid_cost_source==4)
			{
				if(txt_quotation_id>0 && txt_quotation_id !=""){
					if(txt_avg_price < txt_quotation_price){
						alert("Unit price can not be less than quoted price");
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
				var cutting_qty=return_global_ajax_value($("#update_id_details").val()+"_"+$("#cbo_deliveryCountry_id").val(), 'get_cutting_qty', '', 'requires/order_entry_controller');
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
				var check_pre_cost = return_ajax_request_value(txt_job_no, 'check_pre_cost', 'requires/order_entry_controller');
				console.log(check_pre_cost);
				if(check_pre_cost!="No")
				{
					var rr=confirm("Pre-costing Id="+check_pre_cost+" Found against this job.\n Are you sure?");
					if(rr==true)
					{
						
					}
					else
					{
						release_freezing();
						return;
					}
				}
				
				/*var r=confirm("You are going to delete buyer order.\n Are you sure?");
				if(r==true)
				{
					delete_po=1;
				}
				else
				{
					*/var rr=confirm("You are going to delete country data.\n Are you sure?");
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
				//}
			}
			var txt_file_year='';
			var txt_file_year_check=document.getElementById("txt_file_year");
			if(txt_file_year_check)
			{
				txt_file_year=txt_file_year_check.value;
			}
			var color_table =$('#color_tbl tbody tr').length-1;
			var size_table =$('#size_tbl tbody tr').length-1;
			
			
			//var data_country=get_submitted_data_string('cbo_gmtsItem_id*cbo_deliveryCountry_id*cbo_code_id*cbo_country_id*cbo_countryCode_id*txt_cutup_date*cbo_cutOff_id*txt_countryShip_date*txt_breakdownGrouping*txt_pcsQty',"../../",2);
			
			var sz=1; var data_size=""; var data_color=""; var data_breakdown="";
			for(var i=1; i<=size_table; i++)
			{
				var txtSizeName=encodeURIComponent(""+$('#txtSizeName_'+i).val()+"");
				data_size+="&txtSizeName_" + sz + "='"+ txtSizeName+"'"+"&txtSizeId_" + sz + "='" + $('#txtSizeId_'+i).val()+"'";
				sz++;
			}
			//alert (data_size); return;
			var z=1;
			if (breakdown_type==1)
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
			//alert(data_color);
			//alert(data_breakdown);  return;
			if(z==1)
			{
				alert("Color Size Level Qty. Not found.");
				release_freezing();
				return;
			}
			var po_nodata="";
			var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
			var txt_po_no=encodeURIComponent("'"+$('#txt_po_no').val()+"'");
			var po_nodata="&txt_po_no="+txt_po_no+"&txt_style_ref=" + txt_style_ref+"";
			
			var data="action=save_update_delete_dtls&operation="+operation+"&color_table="+color_table+"&size_table="+size_table+"&delete_po="+delete_po+"&txt_file_year="+txt_file_year+"&delete_country="+delete_country+get_submitted_data_string('cbo_breakdown_type*cbo_round_type*cbo_order_status*txt_po_received_date*txt_pub_shipment_date*txt_avg_price*txt_upCharge*txt_docSheetQty*txt_noOf_carton*cbo_projected_po*txt_grouping*txt_file_no*cbo_packing_po_level*cbo_delay_for*update_id_details*color_size_break_down*update_id*txt_po_datedif_hour*txt_po_remarks*copy_id*tot_set_qnty*cbo_status*set_breck_down*cbo_buyer_name*cbo_company_name*txt_shipment_date*txt_phd*hidd_job_id*txt_poAmt*txt_poQty*txt_factory_rec_date*txt_sc_lc*cbo_gmtsItem_id*cbo_deliveryCountry_id*cbo_code_id*cbo_country_id*cbo_countryCode_id*txt_cutup_date*cbo_cutOff_id*txt_countryShip_date*txt_breakdownGrouping*txt_pcsQty*cbo_buyer_name*hidd_bhpo_id*size_all_id',"../../")+po_nodata+data_color+data_size+data_breakdown;
			//alert (data); return;
			
			http.open("POST","requires/order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponsedtls;
		}
	}
	function refresh_page()
	{
		window.location.reload();
	}

	function fnc_on_submit_reponsedtls()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			release_freezing();
			// alert(http.responseText)
			//return;
			if(trim(reponse[0]) ==10)
			{
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
			if(trim(reponse[0])==14) //Issue Id-3249 for Team 
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(trim(reponse[0])==16) //Issue ISD-23-13775 for Continental  
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			//alert(reponse[0]+'='+reponse[1]);
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
				
				if(trim(reponse[0]) ==0)
				{
					//$('#cbo_breakdown_type').attr('disabled','disabled');
					fnc_product_feild_disabled();
					//return;
				}

				var countryShip_date=$('#txt_countryShip_date').val();
				var gmtsItem_id=$('#cbo_gmtsItem_id').val();
				var country_id=$('#cbo_deliveryCountry_id').val();
				$('#update_id_details').val(reponse[1]);
				$('#txt_job_qty').val(reponse[2]);
				$('#txt_avg_price').val(reponse[11]);
				$('#txt_avgUnit_price').val(reponse[3]);
				$('#txt_total_price').val(reponse[4]);
				$('#txt_proj_qty').val(reponse[5]);
				$('#txt_proj_avgUnit_price').val(reponse[6]);
				$('#txt_proj_total_price').val(reponse[7]);
				$('#txt_orginProj_qty').val(reponse[8]);
				$('#txt_orginProj_total_price').val(reponse[9]);
				$('#txt_orginProj_total_amt').val(reponse[10]);
				var breakdown_type =$("#cbo_breakdown_type").val();
				/* if(breakdown_type!=4)
				{
					$('#txt_docSheetQty').val(reponse[12]);
				} */
				$('#cbo_breakdown_type').attr('disabled','disabled');

				$('#cbo_order_uom').attr('disabled','disabled');


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
		reset_form('','','cbo_order_status*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task','','');
		get_php_form_data( id, type, path );
	}

	function set_tna_task()
	{
		var txt_po_received_date=document.getElementById('txt_po_received_date').value
		var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var company_id=$('#cbo_company_name').val()
		var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_controller');
		if(publish_shipment_date !=1)
		{
			if(txt_po_received_date !='')
			{
				var datediff = date_compare(txt_pub_shipment_date,txt_po_received_date);
				if(datediff==true)
				{
					alert("Shipment date can not be less than or same as PO Received date.");
					$('#txt_pub_shipment_date').val("");
					$('#txt_shipment_date').val("");
					$('#txt_countryShip_date').val("");
					return;
				}
			}			
		}
		else{
			if(txt_po_received_date !='')
			{
				/*if(txt_pub_shipment_date == '')
				{
					alert("Publish Shipment Date Can Not Be Null");
					$('#txt_shipment_date').val("");
					return;
				}*/
				var datediff = date_compare(txt_pub_shipment_date,txt_po_received_date);
				if(datediff==true)
				{
					alert("Shipment date can not be less than or same as PO Received date.");
					$('#txt_shipment_date').val("");
					return;
				}
			}
		}
		//load_drop_down( 'requires/order_entry_controller', txt_po_received_date+"_"+txt_pub_shipment_date+"_"+cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td');
		$('#txt_cutup_date').val( txt_pub_shipment_date );
		$('#txt_countryShip_date').val(txt_pub_shipment_date);
	}

	function set_publish_shipment_date()
	{
		var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value;
		var txt_shipment_date=document.getElementById('txt_shipment_date').value;
		var txt_po_received_date=document.getElementById('txt_po_received_date').value;
		if(txt_pub_shipment_date == ''){	
			if(txt_po_received_date!=''){
				if(date_compare(txt_pub_shipment_date,txt_po_received_date) === true){
					alert("Publish Shipment date can not be less than or same as PO Received date.");
					$('#txt_pub_shipment_date').val(' ');
					return;
				}
			}			
			$('#txt_pub_shipment_date').val(txt_shipment_date);
			$('#txt_countryShip_date').val(txt_shipment_date);
		}
		else{
			if(txt_po_received_date!='' && txt_shipment_date ==''){
				if(date_compare(txt_pub_shipment_date,txt_po_received_date) === true){
					alert("Country/Publish Shipment date can not be less than or same as PO Received date.");
					$('#txt_countryShip_date').val(' ');
					$('#txt_pub_shipment_date').val(' ');
					return;
				}
			}
			if(txt_po_received_date!='' && txt_shipment_date !=''){
				if(date_compare(txt_pub_shipment_date,txt_po_received_date) === true){					
						alert("Publish Shipment date can not be less than or same as PO Received date");
						$('#txt_countryShip_date').val(' ');
						$('#txt_pub_shipment_date').val(' ');
						return;
				}
				else{
					//txt_pub_shipment_date>txt_shipment_date
					if(date_compare(txt_shipment_date,txt_pub_shipment_date) === true && (txt_shipment_date!=txt_pub_shipment_date))
					{
						alert("Publish Shipment date can not be greater than Shipment date");
						$('#txt_countryShip_date').val(' ');
						$('#txt_pub_shipment_date').val(' ');
						return;	
					}
				}
			}
			$('#txt_countryShip_date').val(txt_pub_shipment_date);
		}

	}
	function set_pub_ship_date()
	{
		var company_id=$('#cbo_company_name').val()
		var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_controller');
		if(publish_shipment_date==1){
			 $('#txt_pub_shipment_date').attr('disabled',false);
		}
		else{
			var txt_shipment_date=$('#txt_shipment_date').val()
			$('#txt_pub_shipment_date').val(txt_shipment_date);
			$('#txt_countryShip_date').val(txt_shipment_date);
		}
	}

	function pop_entry_actual_po()
	{
		var po_id = $('#update_id_details').val();
		var txt_job_no = $('#txt_job_no').val();
		var gmts_item = $('#item_id').val();
		var po_quantity = $('#txt_docSheetQty').val();
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
		var page_link='requires/order_entry_controller.php?action='+action+'&po_id='+po_id+'&txt_job_no='+txt_job_no+'&gmts_item='+gmts_item+'&po_quantity='+po_quantity+'&job_id='+job_id+'&rcv_date='+po_rcv_date+'&cbo_company_name='+cbo_company_name;
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
			$('#txt_po_received_date').val('');
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

			if(z==1)
			{
				$('#color_tbl tbody').append(
					'<tr id="trColor_'+counter+'">'
					+ '<td id="td_'+counter+'" align="center" >'+counter+'</td><td align="center"><input type="text" name="txtColorName[]" class="text_boxes" id="txtColorName_'+counter+'" style="width:80px;" onKeyUp="append_color_size_row(1)"/><input type="hidden" name="txtColorId[]" class="text_boxes" id="txtColorId_'+counter+'" style="width:50px;" /></td>'+ '</tr>'
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
				if($("#txtColorName_"+i).val()==""){
					var index=i-1;
					$("table#color_tbl tbody tr:eq("+index+")").remove();
				}
				if($("#txtColorName_"+i).val() != ""){
					var titel=$('#txtColorName_'+i).val();
					$('#txtColorName_'+i).attr('title',titel);
				}


			}
			var numRow = $('table#color_tbl tbody tr').length;
			for(var i=1; i<=counter-1; i++)
			{
				var index=i-1;
				$("#color_tbl  tbody tr:eq("+index+")").find("input").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					});
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

				if($("#txtSizeName_"+i).val()==""){
					$("#txtSizeName_"+i).focus();
				}
			}
		}
	}

	function fnc_create_qty_breakdown(is_update,type)
	{
		if(form_validation('txt_avg_price','Rate')==false)
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
		var excut_edit="";
		var editable=$('#hid_excessCut_editable').val()*1;
		var excutSource=$('#hid_excessCut_source').val()*1;
		if(editable==2)
		{
			excut_edit='disabled';
		}
		if(excutSource==3)
		{
			excut_edit='disabled';
		}
		$('#txt_avg_price').attr('disabled',false);
		var html="";
		var size_table =$('#size_tbl tbody tr').length;
		var color_table =$('#color_tbl tbody tr').length;
		//alert(size_count)

		var color_value = new Array();
		var j=1;
		for(var i=1;i<=color_table;i++)
		{
			var color_name=trim($("#txtColorName_"+i).val().toUpperCase());			
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
			var size_name=trim($("#txtSizeName_"+i).val().toUpperCase());
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
		//size_value.sort();
		
		//color_value.sort();
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
		if(type==1 || type==4)
		{
			var all_data=$("#color_size_break_down_all_data").val();
			console.log ('all data: '+all_data);
			var ex_all_data="";
			ex_all_data=all_data.split('___');
			var qty_readonly=""; var qty_static_val_send='';
			//if(all_data != ''){
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
						
						//alert(index_qty);

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
			//}

			freeze_window(5);
			//alert (data_arr_packqty)
			$('#breakdown_div').html('');
			html += '<table cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all" align="left" id="breakdown_tbl"><thead><th width="90px">Color/Size</th><th>Particulars</th>';
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
					html += '</tr><tr><td width="80px">Qty.</td>';
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
				var current_rate = document.getElementById('txt_avg_price').value;
				for (var m = 1; m < size_value.length; m++){
					var index_id=eval('"' + color_value[i]+'_'+size_value[m] + 'id'+'"');
					var index_rate=eval('"' + color_value[i]+'_'+size_value[m] + 'rate'+'"');
					var id=''; var rate="";
					if(typeof(data_arr_id[index_id])=="undefined") id=''; else id=data_arr_id[index_id];
					if(typeof(data_arr_rate[index_rate])=="undefined") rate=number_format(current_rate,4); else rate=number_format(data_arr_rate[index_rate],4);
					html+='<td width="55px"><input type="hidden" name="txt_colorSizeId_[]" id="txt_colorSizeId_'+i+'_'+m+'" value="'+id+'" class="text_boxes_numeric" style="width:50px;" /><input type="text" name="txt_colorSizeRate_[]" id="txt_colorSizeRate_'+i+'_'+m+'" value="'+rate+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,2); fnc_copy_rate('+i+','+m+',this.value,'+qty_static_val_send+');" '+disable_fld+'/></td>';
				}
				html += '<td>&nbsp</td></tr><tr><td width="80px">Ex. Cut %</td>';

				for (var m = 1; m < size_value.length; m++){
					var index_excut=eval('"' + color_value[i]+'_'+size_value[m] + 'excut'+'"');
					var excut="";
					if(typeof(data_arr_excut[index_excut])=="undefined") excut=""; else excut=data_arr_excut[index_excut];
					html+='<td width="55px"><input type="text" name="txt_colorSizeExCut_[]" id="txt_colorSizeExCut_'+i+'_'+m+'" value="'+excut+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,3); fnc_copy_qty_excut('+i+','+m+',this.value,2);" '+excut_edit+'  /></td>';
				}
				html += '<td>&nbsp</td></tr><tr><td width="80px">Plan Cut Qty.</td>';

				for (var m = 1; m < size_value.length; m++){
					var index_plancut=eval('"' + color_value[i]+'_'+size_value[m] + 'plancut'+'"');
					var plancut="";
					if(typeof(data_arr_plancut[index_plancut])=="undefined") plancut=""; else plancut=data_arr_plancut[index_plancut];
					html+='<td width="55px"><input type="text" name="txt_colorSizePlanCut_[]" id="txt_colorSizePLanCut_'+i+'_'+m+'" value="'+plancut+'" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_calAmountQty_ex(this.value,3); fnc_copy_qty_excut('+i+','+m+',this.value,3);" /></td>';
				}
				html += '<td width="70px"><input type="text" name="txt_totPlanCutAmt_[]" class="text_boxes_numeric" id="txt_totPlanCutAmt_'+i+'" style="width:50px;" readonly /></td></tr><tr><td width="80px">Article No</td>';

				for (var m = 1; m < size_value.length; m++){
					var index_artno=eval('"' + color_value[i]+'_'+size_value[m] + 'artno'+'"');
					var artno='';
					if(typeof(data_arr_artno[index_artno])=="undefined") artno=''; else artno=data_arr_artno[index_artno];
					html+='<td width="55px"><input type="text" name="txt_colorSizeArticleNo_[]" id="txt_colorSizeArticleNo_'+i+'_'+m+'" value="'+artno+'" class="text_boxes" style="width:50px;" /></td>';
				}

				html += '</tr>';
			}

			html += '</tbody><tfoot><tr><td></td><td><strong>Total : </strong></td>';
			for (var m = 1; m < size_value.length; m++){
				html+='<td width="55px"><input type="text" name="txt_sizeQty_[]" id="txt_sizeQty_'+m+'" class="text_boxes_numeric" style="width:50px;" readonly /></td>';
			}

			html += '<td width="80px"><input type="text" name="txt_poQty" class="text_boxes_numeric" id="txt_poQty" style="width:50px;" readonly /></td><td width="80px"><input type="text" name="txt_poAmt" class="text_boxes_numeric" id="txt_poAmt" style="width:50px;" readonly /></td><td width="80px"><input type="text" name="txt_totplancut" class="text_boxes_numeric" id="txt_totplancut" style="width:50px;" readonly /></td></tr></tfoot></table>';

			$("#breakdown_div").append(html);
			//release_freezing();
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
		}
		else if(type==2 || type==3)
		{
			var ratio_all_data=$("#color_size_ratio_data").val();
			var ex_ratio_all_data="";
			console.log(ratio_all_data);
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
				html += '<td width="60px" rowspan="2"><input type="text" name="txt_colorRatioQty_[]" id="txt_colorRatioQty_'+i+'" class="text_boxes_numeric" style="width:50px;" readonly /></td><td width="70px" rowspan="2"><input type="text" name="txt_colorRatioAmt_[]" class="text_boxes_numeric" id="txt_colorRatioAmt_'+i+'" style="width:50px;" readonly /></td><td rowspan="2"></td></tr><tr><td width="80px">Rate</td>';

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

		/*var color_value = new Array();
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
		}*/
		var color_value = new Array();
		var j=1;
		for(var i=1;i<=color_table;i++)
		{
			var color_name=trim($("#txtColorName_"+i).val().toUpperCase());			
			if(color_name!="")
			{
				if(jQuery.inArray(color_name, color_value) != -1) {
					$("#txtColorName_"+i).val('');
					alert("Duplicate Color Not Allowed");
					append_color_size_row(1);
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
			var size_name=trim($("#txtSizeName_"+i).val().toUpperCase());
			if(size_name!="")
			{
				if(jQuery.inArray(size_name, size_value) != -1) {
					$("#txtSizeName_"+i).val('');
					alert("Duplicate Size Not Allowed");
					append_color_size_row(2);
				} else {
				    size_value[k]=size_name;
					k++;
				}
			}
		}
		//size_value.sort();
		

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
		console.log(size_value.length+'--'+color_value.length);
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
					html +='<td width="55px"><input name="txt_colorSizeQty_[]" id="txt_colorSizeQty_'+i+'_'+m+'" value="'+ratioQty_arr[index_qty]+'" class="text_boxes_numeric" style="width:50px;" onChange="fnc_calAmountQty_ex(this.value,1); " onDblClick="fnc_assortment_pop_up('+"'"+color_value[i]+"'"+','+"'"+size_value[m]+"'"+','+i+','+m+',this.value);" readonly /><input name="txt_assortQty_[]" id="txt_assortQty_'+i+'_'+m+'" value="'+asst+'" class="text_boxes" style="width:50px;" type="hidden" /></td>';

					//var colorSizeQty=parseInt($('#txt_colorSizeQty_'+i+'_'+m).val()*1);
				}

				html += '<td width="60px" rowspan="5"><input type="text" name="txt_colorQty_[]" id="txt_colorQty_'+i+'" class="text_boxes_numeric" onDblClick="openmypage_ultimate_pop(this.value,'+i+','+"'"+color_value[i]+"'"+');" style="width:50px;" readonly /></td><td width="70px" rowspan="5"><input type="text" name="txt_colorAmt_[]" class="text_boxes_numeric" id="txt_colorAmt_'+i+'" style="width:50px;" readonly /></td><td>&nbsp;</td></tr><tr><td width="80px">Rate</td>';

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
					//excut=excess_percentage(company_name,buyer_name,ratioQty_arr[index_qty]);//excess_percentage(1,73, ratioQty_arr[i+""+m] );
					//ratioExCut_arr[index_excut]=excut;
					//alert(data_arr_excut[index_excut])
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
					html+='<td width="55px"><input type="text" name="txt_colorSizeArticleNo_[]" id="txt_colorSizeArticleNo_'+i+'_'+m+'" value="'+artno+'" class="text_boxes" style="width:50px;" /></td>';
					//fnc_copy_qty_excut(i, m, ratioQty_arr[index_qty], type);
					// onBlur="fnc_calAmountQty_ex(this.value,4)" MD09-05-2022
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
			}
			else if(is_update==1)
			{
				set_button_status(1, permission, 'fnc_order_entry_details',2);
				fnc_calAmountQty_ex(0,0);
				//fnc_calculateRate(type);
			}
		}
	}

	function fnc_ratioQtyRate(val,data_type)
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
		//alert(is_update);
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
					if(type==1){
						 $('#txt_colorSizeRate_'+i+'_'+m).val( number_format( set_price, 4) );
						 fnc_calAmountQty_ex(number_format( set_price, 4),2);

					}
					else if(type==2 || type==3)
					{
						 $('#txt_colorSizeRatioRate_'+i+'_'+m).val( number_format( set_price, 4) );	
					}
					else if(type==4)
					{
						var packQty=$('#txt_pcsQty').val()*1;
						var new_price=avg_price/packQty;
						$('#txt_colorSizeRate_'+i+'_'+m).val( number_format( new_price, 4) );
						fnc_calAmountQty_ex(number_format( new_price, 4),2);
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

		//alert (data_val+'_'+data_type)
		//console.log(data_val+'_'+data_type);

		var size_value = new Array();
		var k=1;
		for(var i=1;i<=size_table;i++)
		{
			var size_name=trim($("#txtSizeName_"+i).val().toUpperCase());
			if(size_name!="")
			{
				if(jQuery.inArray(size_name, size_value) != -1) {
					/*$("#txtSizeName_"+i).val('');
					append_color_size_row(2);*/					
				} else {
				    size_value[k]=size_name;
					k++;
				}
			}
		}
		//size_value.sort();
		var color_value = new Array();
		var j=1;
		for(var i=1;i<=color_table;i++)
		{
			var color_name=trim($("#txtColorName_"+i).val().toUpperCase());			
			if(color_name!="")
			{
				if(jQuery.inArray(color_name, color_value) != -1) {
					/*$("#txtColorName_"+i).val('');
					append_color_size_row(2);*/				
				} else {
				    color_value[j]=color_name;
					j++;
				}
			}
		}

		var colorQty=0; var colorAmt=0; var y=0; var tot_pcs_rate=0; var planCutAmt = 0;
		for (var i = 1; i < color_value.length; i++)
		{
			var totColorQty=0; var totColorRate=0; var totColorAmt=0; var totPlanCutAmt=0;
			var j=1;
			for (var m = 1; m < size_value.length; m++)
			{
				if(j==1)
				{
					//if($('#txt_colorSizeExCut_'+i+'_'+m).val()=="")
					if($('#txt_colorSizeArticleNo_'+i+'_'+m).val()=="")
						if(data_type==4) $('#txt_colorSizeArticleNo_'+i+'_'+m).val( data_val );
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
					totColorQty=totColorQty+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
					totColorAmt=totColorAmt+($('#txt_colorSizeRate_'+i+'_'+m).val()*1)*($('#txt_colorSizeQty_'+i+'_'+m).val()*1);

					if(($('#txt_colorSizePLanCut_'+i+'_'+m).val()*1)==0)
					{
						$('#txt_colorSizePLanCut_'+i+'_'+m).val( $('#txt_colorSizeQty_'+i+'_'+m).val()*1 );
					}
					if(($('#txt_colorSizeExCut_'+i+'_'+m).val()*1)==0)
					{

						$('#txt_colorSizePLanCut_'+i+'_'+m).val( $('#txt_colorSizeQty_'+i+'_'+m).val()*1 );
					}
					else
					{

						ex_cut=(($('#txt_colorSizeExCut_'+i+'_'+m).val()*1/100)*($('#txt_colorSizeQty_'+i+'_'+m).val()*1))+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
						ex_cut=Math.ceil(ex_cut);
						$('#txt_colorSizePLanCut_'+i+'_'+m).val(ex_cut);

					}
					totPlanCutAmt = totPlanCutAmt+$('#txt_colorSizePLanCut_'+i+'_'+m).val()*1;

				}
				else if(type==4)
				{
					totPcsQty=totPcsQty+($('#txt_colorSizePackQty_'+i+'_'+m).val()*1)*($('#txt_colorSizePcsQty_'+i+'_'+m).val()*1);
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
			planCutAmt=planCutAmt+$('#txt_totPlanCutAmt_'+i).val()*1;
		}
		$('#txt_poQty').val(colorQty);
		$('#txt_poAmt').val(colorAmt);
		$('#txt_totplancut').val(planCutAmt);

		for (var m = 1; m < size_value.length; m++)
		{
			var totSizeQty=0;
			for (var i = 1; i < color_value.length; i++)
			{
				totSizeQty=totSizeQty+$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
			}
			$('#txt_sizeQty_'+m).val(totSizeQty);
		}
	}

	function fnc_set_ship_date()
	{
		var cut_off_used=$("#cut_off_used").val();
		if(cut_off_used==0 || cut_off_used==1)
		{
			var txt_cutup_date=document.getElementById('txt_cutup_date').value;
			var cbo_cut_up=document.getElementById('cbo_cutOff_id').value;

			var po_id=document.getElementById('update_id_details').value;
			var country_id=$("#cbo_deliveryCountry_id").val();
			var cutt_off=return_global_ajax_value(po_id+"_"+country_id, 'check_country', '', 'requires/order_entry_controller');

			var is_cutt=cutt_off.split("_");
			if(is_cutt[1]!=0 || is_cutt[1]!="")
			{
				if(cbo_cut_up==0)
				{
					alert("Select Cutup");
					$("#txt_countryShip_date").attr("disabled",false);
					$("#txt_countryShip_date").val( $("#txt_pub_shipment_date").val() );
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
				
			}
			
			if(cbo_cut_up==0 || cbo_cut_up=='')
			{
				$("#txt_countryShip_date").val( $("#txt_pub_shipment_date").val() );
			}
			else{
				var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'requires/order_entry_controller');
				document.getElementById('txt_countryShip_date').value=set_ship_date;
			}
			if(txt_cutup_date=="") $("#txt_cutup_date").val( $("#txt_shipment_date").val() );
			if($("#txt_cutup_date").val()=="")
			{
				alert("Insert Cutup Date");
				$("#txt_countryShip_date").attr("disabled",false);
				return;
			}
		}
		else{
			$("#txt_countryShip_date").val( $("#txt_pub_shipment_date").val() );
		}
		
	}

    function fnc_noof_carton(type)
    {

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
		var color_from_lib=$('#hidd_color_from_lib').val();
		var company_id=$('#cbo_company_name').val();
		let field_type = typeof field_level_data;
		if(company_id!=0 && field_type === 'object' && field_level_data[company_id] !=undefined)
		{
			if(Object.keys(field_level_data[company_id]).length>0){
				$.each( field_level_data[company_id], function( key, value ) {
					if(key=='cbo_breakdown_type')
					{
						if( value['is_disable']==1) {
							first_sts.push({
								f_titles: key,
								f_vals:  $('#'+key).attr('disabled')
							});
							$('#'+key).attr('disabled',true);
						}
						else
						{
							first_sts.push({
								f_titles: key,
								f_vals:  $('#'+key).attr('disabled')
							});
							$('#'+key).attr('disabled',false);
						}				
						if(value['defalt_value']==null || value['defalt_value']=="undefined"){value['defalt_value']='';}
						if(value['defalt_value']!='')
						{  
							
							if(value['defalt_value']=="undefined") return;
							first_values.push({
								f_title: key,
								f_val:  $('#'+key).val()
							});
							
							 if(value['defalt_value']!=null)
							 { 
								$('#'+key).attr('value',value['defalt_value']);
								$('#'+key).change();
							 }

							//$('#'+key).attr('value',4);
							//$('#'+key).attr('value',value['defalt_value']);
						}
					}				
				});
			}
		}
		else
		{
			$('#cbo_breakdown_type').removeAttr('disabled');
		}
		$('#txt_avg_price').attr('disabled',false);
		$('#country_po_list_view').html('');
		$('#breakdown_div').html('');
		$('#breakdownratio_div').html('');
		$('#td_size').html('');
		$('#td_color').html('');
		$('#txt_is_update').val(0);
		$('#copy_asac').val(2);
		$('#copy_assc').val(2);
		$('#copy_acss').val(2);
		$('#copy_excut').val(2);
		$('#txt_noOf_carton').attr('disabled','true');		
		$('#txt_countryShip_date').attr('disabled',false);
		$('#cbo_deliveryCountry_id').attr('disabled',false);
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
		else if(type==8)
		{
			if(document.getElementById('copy_article').checked==true)
			{
				document.getElementById('copy_article').value=1;
			}
			else if(document.getElementById('copy_article').checked==false)
			{
				document.getElementById('copy_article').value=2;
			}
		}
		else if(type==9)
		{
			if(document.getElementById('copy_assc_rate').checked==true)
			{
				document.getElementById('copy_assc_rate').value=1;
			}
			else if(document.getElementById('copy_assc_rate').checked==false)
			{
				document.getElementById('copy_assc_rate').value=2;
			}
		}
	}
	<?
	$sql_temp=sql_select("SELECT percentage,upper_limit_qty,comapny_id,buyer_id,lower_limit_qty FROM lib_excess_cut_slab WHERE  status_active=1 and is_deleted=0 order by comapny_id,buyer_id,lower_limit_qty asc");
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
	unset($sql_temp);
	?>
	var exc_perc =<? echo json_encode($exc_perc); ?>;
	function excess_percentage( comp, buyer, qnty )
	{
		//var exc_perc=new Array();
		//alert (comp+'='+buyer+'='+qnty); return;

		//if( exc_perc[comp][buyer]!="undefined" )
		if(typeof(exc_perc[comp])!= 'undefined')
		{
			if(typeof(exc_perc[comp][buyer])!= 'undefined')
			{
				var newp=exc_perc[comp][buyer]["limit"];
				var newp= JSON.stringify(newp);
				var newstr=newp.split(",");
				for(var m=0; m< newstr.length; m++)
				{
					var limit=exc_perc[comp][buyer]["limit"][m].split("__");
					if((limit[1]*1)==0 && (qnty*1)>=(limit[0]*1))
					{
						return ( exc_perc[comp][buyer]["val"][m]*1);
					}
					if( (qnty*1)>=(limit[0]*1) && (qnty*1)<=(limit[1]*1) )
					{
						return exc_perc[comp][buyer]["val"][m];
					}
					// alert( newstr[m]+"=="+m)
				}
			}
		}
		return 0;
	}
	function fnc_copy_rate(colRid,sizRid,val,type)
	{
		var color_table =$('#color_tbl tbody tr').length;
		var size_table =$('#size_tbl tbody tr').length;

		var is_checked_assc_rate=document.getElementById('copy_assc_rate').value;

		if(type==1)
		{
			if(is_checked_assc_rate==1)
			{
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
					{
						$('#txt_colorSizeRate_'+colRid+'_'+m).val( val );
					}
				}
				fnc_calAmountQty_ex(val,2)
			}
		}
	}


	function fnc_copy_qty_excut(colRid,sizRid,val,type)
	{
		var color_table =$('#color_tbl tbody tr').length;
		var size_table =$('#size_tbl tbody tr').length;
		//alert(color_table+'='+size_table)
		var company_name=$('#cbo_company_name').val();
		var buyer_name=$('#cbo_buyer_name').val();
		var excut_source=$('#hid_excessCut_source').val()*1;
		var editable=$('#hid_excessCut_editable').val()*1;
			//alert(type+'=='+excut_source);
		if(type==5 || type==6)
		{
			val=($('#txt_colorSizePackQty_'+colRid+'_'+sizRid).val()*1)*($('#txt_colorSizePcsQty_'+colRid+'_'+sizRid).val()*1);
		}

		var excut_fmLib =0;
		if(excut_source==2)
		{
			var excut_fmLib = excess_percentage(company_name,buyer_name,val);
			if(excut_fmLib!=0) excut_fmLib =number_format(excut_fmLib*1,2 );
		}

		//alert(excut_fmLib)
		
		var is_checked_asac=document.getElementById('copy_asac').value;
		var is_checked_assc=document.getElementById('copy_assc').value;
		var is_checked_acss=document.getElementById('copy_acss').value;
		var is_checked_excut=document.getElementById('copy_excut').value;
		var copy_assc_rate=document.getElementById('copy_assc_rate').value;
		
		//var is_checked_article=document.getElementById('copy_article').value;
		if(type==1)
		{
			if(is_checked_asac==1)
			{
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
				}
				fnc_calAmountQty_ex(0,0)
			}
			if(is_checked_assc==1)
			{
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
				if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( excut_fmLib );

				var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut="";
				colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val()*1;
				colorSizeExcut=($('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val()*1)/100;
				colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
				//alert (colorSizePlancut)
				var colorSizePlancutQty=Math.ceil(colorSizePlancut);
				if(colorSizeQty==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
				$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val( colorSizePlancutQty );
			}			
		}
		else if (type==2)
		{
			if(is_checked_excut==1)
			{
				var planCutAmt = 0;
				for (var i = 1; i < color_table; i++)
				{
					var totPlanCutAmt=0;
					for (var m = 1; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						
						$('#txt_colorSizeExCut_'+i+'_'+m).val( val );
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val( excut_fmLib );
						else if(excut_source==1) $('#txt_colorSizeExCut_'+i+'_'+m).val( val );
						else $('#txt_colorSizeExCut_'+i+'_'+m).val( val );
						
						var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut="";
						colorSizeQty=$('#txt_colorSizeQty_'+i+'_'+m).val()*1;
						colorSizeExcut=(val*1)/100;
						colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
						var colorSizePlancutQty=Math.ceil(colorSizePlancut);
						if(colorSizeQty==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+i+'_'+m).val();
						$('#txt_colorSizePLanCut_'+i+'_'+m).val( colorSizePlancutQty );
						totPlanCutAmt = totPlanCutAmt+$('#txt_colorSizePLanCut_'+i+'_'+m).val()*1;
					}
					$('#txt_totPlanCutAmt_'+i).val(totPlanCutAmt);
					planCutAmt=planCutAmt+$('#txt_totPlanCutAmt_'+i).val()*1;
				}
				$('#txt_totplancut').val(planCutAmt);

			}
			else
			{
				var planCutAmt = 0;
				for (var i = 1; i < color_table; i++)
				{
					var totPlanCutAmt=0;
					for (var m = 1; m < size_table; m++)
					{
						if(excut_fmLib==0) excut_fmLib='';
						/*if($('#txt_colorSizeQty_'+i+'_'+sizRid).val()=="")
						{*/
							if(excut_source==2) $('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( excut_fmLib );
							var exCut=$('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val()*1;
						//}
						var colorSizeQty=0; var colorSizeExcut=0; var colorSizePlancut=""; var exCut_new="";
						colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val()*1;
						colorSizeExcut=($('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val()*1)/100;
						colorSizePlancut=(colorSizeQty*colorSizeExcut)+colorSizeQty;
						var exCut_new=((colorSizePlancut-colorSizeQty)*1/colorSizeQty)*100;
					//	alert(exCut_new+'='+colorSizePlancut+'='+colorSizeQty);
						var colorSizePlancutQty=Math.ceil(colorSizePlancut);
						if(colorSizeQty==0 || colorSizeExcut==0) colorSizePlancutQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
						$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val( colorSizePlancutQty );
						$('#txt_colorSizeExCut_'+colRid+'_'+sizRid).val( number_format(exCut_new,2,'.','') );
						totPlanCutAmt = totPlanCutAmt+$('#txt_colorSizePLanCut_'+i+'_'+m).val()*1;
					}
					$('#txt_totPlanCutAmt_'+i).val(totPlanCutAmt);
					planCutAmt=planCutAmt+$('#txt_totPlanCutAmt_'+i).val()*1;
				}
				$('#txt_totplancut').val(planCutAmt);
			}
		}
		else if (type==3)
		{
			var colorSizeQty=0; var colorSizePlancut=0; var colorSizeExcut="";
			colorSizeQty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val()*1;
			//colorSizePlancut=($('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val()*1);/100
			if(colorSizeQty>0){
				colorSizeExcut=(((val*1)-colorSizeQty)/colorSizeQty)*100;
			}
			
			//alert (colorSizeExcut+'='+val+'='+colorSizeQty)
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
		}else if (type==4)
		{
			var colorSizeQty=0; var colorSizePlancut=0; var colorSizeExcut="";
			// article=$('#txt_colorSizeArticleNo_'+colRid+'_'+sizRid).val()*1;
			//colorSizePlancut=($('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val()*1);/100
			
			if(is_checked_article==1)
			{
			
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
					{
						var article=$('#txt_colorSizeArticleNo_'+i+'_'+m).val( val );
					}
				}
			}
		}
		else if (type==5) //|| type==6
		{
			if(excut_fmLib==0) excut_fmLib='';
			var qty_qty=$('#txt_colorSizeQty_'+colRid+'_'+sizRid).val();
			$('#txt_colorSizePLanCut_'+colRid+'_'+sizRid).val(qty_qty);

			var pack_qty=$('#txt_colorSizePackQty_'+colRid+'_'+sizRid).val();
			if(is_checked_asac==1)
			{
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
					{
						if($('#txt_colorSizePackQty_'+i+'_'+m).val()=="")
						{
							$('#txt_colorSizePackQty_'+i+'_'+m).val( pack_qty );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val(excut_fmLib);
					}
				}
			}
			if(is_checked_assc==1)
			{
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
					{
						if($('#txt_colorSizePcsQty_'+i+'_'+m).val()=="")
						{
							$('#txt_colorSizePcsQty_'+i+'_'+m).val( pcs_per_pack );
						}
						if(excut_source==2) $('#txt_colorSizeExCut_'+i+'_'+m).val(excut_fmLib);
					}
				}
			}
			if(is_checked_assc==1)
			{
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
				for (var i = 1; i < color_table; i++)
				{
					for (var m = 1; m < size_table; m++)
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
		var company_id =$("#cbo_company_name").val();
		var precost = return_ajax_request_value(txt_job_no, 'check_precost_approve', 'requires/order_entry_controller');
		var po_variable = return_ajax_request_value(company_id, 'check_po_entry_control', 'requires/order_entry_controller');
		if(operation==2){
			if (precost==1 || precost==3)
			{
				alert("Pre Cost Approved, Any Change will be not allowed.");
				release_freezing();
				return;
			}
		}
		else if(operation==1){
			if(po_variable ==1){
				if (precost==1 || precost==3)
				{
					alert("Pre Cost Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
		}
		else if(operation==0){
			if(po_variable ==3 || po_variable ==2){
				if (precost==1 || precost==3)
				{
					alert("Pre Cost Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
		}
		else{
			if(po_variable ==2){
				if (precost==1 || precost==3)
				{
					alert("Pre Cost Approved, Any Change will be not allowed.");
					release_freezing();
					return;
				}
			}
		}

		if(old_po_id=="" && old_po_no!="")
		{
			alert("Please Need Po Save.");
			release_freezing();
			return;
		}
		var company_id=$('#cbo_company_name').val();
		//var buyer_id=$('#cbo_buyer_name').val();
		var style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		var po_id=$('#update_id_details').val();
		var value=$('#txt_copypo_no').val();
		if(value !='')
		{
			var buyer_style_po_check=return_ajax_request_value(company_id+'_'+buyer_id+'_'+style_ref+'_'+value+'_'+po_id+'_1', 'load_buyer_style_po_check', 'requires/order_entry_controller');
			var split_data=buyer_style_po_check.split('***');
			var show_val_column='';
			if(split_data[2]!="")
			{
				var r=confirm(split_data[1]);
				
			}					
			else
			{
				var r=confirm("You are Going to Copy a PO.\n Please, Press OK to Copy.\n Otherwise Press Cancel.");
			}
		}
		if (r==true)
		{
			if(form_validation('txt_copypo_no','Copy Po Number')==false)
			{
				return;
			}
			var txt_copypo_no=encodeURIComponent("'"+$('#txt_copypo_no').val()+"'");
			var po_nodata='&txt_copypo_no='+"'"+txt_copypo_no+"'";
			var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('copy_id*hidd_job_id*update_id*set_breck_down*update_id_details*cbo_buyer_name',"../../")+po_nodata;
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
			if(reponse[0]==14)//ISD-23-10902
			{
				alert(reponse[1]);
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

				//alert(reponse[3]);
				$('#txt_job_qty').val(reponse[3]);
				$('#txt_avg_price').val(reponse[12]);
				$('#txt_avgUnit_price').val(reponse[4]);
				$('#txt_total_price').val(reponse[5]);
				$('#txt_proj_qty').val(reponse[6]);
				$('#txt_proj_avgUnit_price').val(reponse[7]);
				$('#txt_proj_total_price').val(reponse[8]);
				$('#txt_orginProj_qty').val(reponse[9]);
				$('#txt_orginProj_total_price').val(reponse[10]);
				$('#txt_orginProj_total_amt').val(reponse[11]);

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
		var buyer_style_po_check=return_ajax_request_value(company_id+'_'+buyer_id+'_'+style_ref+'_'+value+'_'+po_id+'_0', 'load_buyer_style_po_check', 'requires/order_entry_controller');
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
	}
	var rowcolor=new Array();
	var last_id='';
	function change_color_country_po_tr(v_id,e_color)
	{
		if(last_id!='') $('#country_tr_'+last_id).attr('bgcolor',rowcolor[last_id])

			if( rowcolor[v_id]==undefined ) rowcolor[v_id]=$('#country_tr_'+v_id).attr('bgcolor');

		if( $('#country_tr_'+v_id).attr('bgcolor')=='#FF9900')
			$('#country_tr_'+v_id).attr('bgcolor',rowcolor[v_id])
		else
			$('#country_tr_'+v_id).attr('bgcolor','#FF9900')

		last_id=v_id;
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

		if(txt_po_quantity<allowed_qty)
		{
			alert("Cutting Qty Found,You can update upto "+allowed_qty+" Qty");
			$('#txt_colorSizeQty_'+i+'_'+j).val(saved_po_quantity);
			fnc_calAmountQty_ex(saved_po_quantity,1);
			fnc_copy_qty_excut(i,j,saved_po_quantity,1);
			return;
		}
	}

	function shipment_date_diff(){
		var company_id=$('#cbo_company_name').val()
		var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_controller');
		var pub_shipment_date=$('#txt_pub_shipment_date').val();
		var shipment_date=$('#txt_shipment_date').val();
		if(publish_shipment_date ==1)
		{
			if(pub_shipment_date == '')
			{
				alert("Publish Shipment Date Can Not Be Null");
				$('#txt_shipment_date').val("");
				return;
			}
			if(date_compare(pub_shipment_date,shipment_date) === false){
				alert("Shipment date can not be less then Publish shipment date");
				$('#txt_shipment_date').val(' ');
			}
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
	
	function restrictToInteger(id) {
		var el = document.querySelector("#"+id);
		if(el)
		{
			 el.value = el.value.replace(/[^\d]/g, '');
		}
	}
	
	function get_sew_company_config(company_id)
	{
		load_drop_down( 'requires/order_entry_controller', company_id, 'load_drop_down_sew_location', 'sew_location' );
	}
	
	function style_wise_front_back_img_show(){
		$("#image_button_front").hide();
		$("#image_button_back").hide();

		$("#image_button_front1").show();
		$("#image_button_back1").show();
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
	
	function fnc_get_company_config(company_id)
	{
		$('#cbo_style_owner').val( company_id );
		//load_drop_down( 'requires/order_entry_controller', company_id, 'load_drop_down_sew_location', 'sew_location' );

		get_php_form_data(company_id,'get_company_config','requires/order_entry_controller' );
		location_select();
		set_field_level_access(company_id);
		
		var celid=mst_mandatory_field.split("*")
		//alert( celid.length+"="+mandatory_field+"="+celid)
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
		set_field_level_access( $('#cbo_company_name').val() );
	}
	
	function req_openmypage(page_link,title)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_style_ref='+txt_style_ref;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");

			freeze_window(5);
			document.getElementById('txt_req_no').value=theemail.value;
			release_freezing();
		}
	}

</script>
<script type="text/javascript">
/*$(document).ready(function() {
 $('#txt_file_no').bind('copy paste cut',function(e) { 
 e.preventDefault(); //disable cut,copy,paste
 alert('cut,copy & paste options are disabled !!');
 });
});*/
</script>
</head>
<body onLoad="set_hotkey(); fnc_mandatorytdcolor();">
	<div style="width:100%;" align="center">
		<?=load_freeze_divs ("../../",$permission); ?>
        <table width="100%" cellpadding="0" cellspacing="2" align="center" >
			<tr>
				<td valign="top" width="950">
                <h3 style="width:950px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')">-Garments Job Entry [WVN]</h3>
                    <div id="content_search_panel" style="width:950px">
                        <fieldset style="width:950px;">
                            <form name="orderentry_1" id="orderentry_1" autocomplete="off">
                            <table width="950" cellspacing="2" cellpaddizzng="0" border="0">
                                <tr>
                                    <td colspan="4" align="right"><span  style="color:blue; font-size:16px; font-weight:bold ">Job No</span></td>
                                    <td colspan="4">
                                        <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_job('requires/order_entry_controller.php?action=job_popup','Job/Order Selection Form');" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly />
                                        <input type="hidden" name="hidd_job_id" id="hidd_job_id"/>
                                        <input type="hidden" name="po_update_period_maintain" id="po_update_period_maintain" />
                                        <input type="hidden" name="po_current_date_maintain" id="po_current_date_maintain"/>
                                        <input type="hidden" name="set_smv_id" id="set_smv_id"/>
                                        <input type="hidden" name="hidd_color_from_lib" id="hidd_color_from_lib"/>
                                        <input type="hidden" name="sewing_company_validate_id" id="sewing_company_validate_id" />
                                        <input type="hidden" name="hidd_inquery_id" id="hidd_inquery_id"/>
                                        <input type="hidden" name="cut_off_used" id="cut_off_used"/>
                                        <input type="hidden" name="act_po_id" id="act_po_id"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="90" class="must_entry_caption">Company</td>
                                    <td width="150"><?=create_drop_down( "cbo_company_name", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "fnc_get_company_config(this.value); fnc_variable_settings_check(this.value); lib_mandatory_check(this.value);"); //set_smv_check(this.value); ?>
                                    </td>
                                    <td width="90" class="must_entry_caption">Location</td>
                                    <td width="150" id="location"><?=create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "Select", $selected, "" ); ?></td>
                                    <td width="90" class="must_entry_caption">Buyer</td>
                                    <td width="150" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "Select Buyer", $selected, "" ); ?></td>
                                    <td width="120" class="must_entry_caption">Style Ref.
                                    	<select name="cbo_style_from" id="cbo_style_from" class="combo_boxes" style="width:45px;<?=$isBhTagInOrderEntryDisplay; ?>" onChange="fnc_style_check();">
                                            <option value="0">--</option>
                                            <option value="1">BH</option>
                                            <option value="2">Others</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="text_boxes" type="text" style="width:70px" placeholder="Browse/Write" name="txt_style_ref" id="txt_style_ref" onDblClick="open_qoutation_popup('requires/order_entry_controller.php?action=quotation_id_popup','Quotation ID Selection Form')" title="" readonly/>
                                        <input class="text_boxes" id="quotation_id" type="text" style="width:30px" placeholder="Q. ID" disabled/>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="style_desc_id">Style Desc.</td>
                                    <td><input class="text_boxes" type="text" style="width:120px;" name="txt_style_description" id="txt_style_description" placeholder="Style Description" /></td>
                                    <td id="season_year_id">Season Year</td>
                                    <td><?=create_drop_down( "cbo_season_year", 130, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                                    <td id="seasioncation_td">Season<input type="hidden" name="is_season_must" id="is_season_must" style="width:50px;" class="text_boxes" /></td>
                                    <td id="season_td"><?=create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "--Season--",$selected, "" ); ?></td>
                                    <td id="brandtd">Brand</td>
                                    <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Prod. Dept.</td>
                                    <td><?=create_drop_down( "cbo_product_department", 80, $product_dept, "", 1, "Select", $selected, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value)", "", "" ); ?>
                                        <input class="text_boxes" type="text" style="width:35px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" />
                                    </td>
                                    <td>Sub. Dept.</td>
                                    <td id="sub_td"><?=create_drop_down( "cbo_sub_dept", 130, $blank_array,"", 1, "Select Sub Dep", $selected, "" ); ?></td>
                                    <td>Currency</td>
                                    <td><?=create_drop_down( "cbo_currercy", 130, $currency,'', 0, "",2, "" ); ?></td>
                                    <td class="must_entry_caption">Product Cate.</td>
                                    <td><?=create_drop_down( "txt_item_catgory", 130, $product_category,"", 1, "Select Product Category", 1, "","","" ); ?></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Team Leader</td>
                                    <?
								$user_level=return_field_value("user_level","user_passwd"," id ='$user_id'");
								$teamIdSql=sql_select("select a.id from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and a.project_type=2 and a.status_active =1 and a.is_deleted=0 and b.user_tag_id =$user_id group by a.id order by a.id");
								 //issue 4734
								foreach( $teamIdSql as $row)
								{
									$teamIdSqlArr[$row[csf('id')]]=$row[csf('id')];
								}
						 		if($user_level==2) //Admin
								{
								 $teamId_cond="";
								}
								else {
									$teamId=implode(',',$teamIdSqlArr);
								if($teamId!='') $teamId_cond="and id in($teamId)";else $teamId_cond="";
								}
									?>
                                    <td id="leader_td"><?=create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 $teamId_cond order by team_leader_name  ","id,team_leader_name", 1, "Select Team", $selected, "load_drop_down( 'requires/order_entry_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );load_drop_down( 'requires/order_entry_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ) "); ?></td>
                                    <td class="must_entry_caption">Dealing Merchant</td>
                                    <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "Select Team Member", $selected, "" ); ?></td>
                                    <td>Factory Merchant</td>
                                    <td id="div_marchant_factory"><?=create_drop_down( "cbo_factory_merchant", 130, $blank_array,"", 1, "Select Team Member", $selected, "" ); ?></td>
                                    <td>Agent</td>
                                    <td id="agent_td"><?=create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "Select Agent", $selected, "" ); ?></td>
                                </tr>
                                <tr>
                                    <td>Client</td>
                                    <td id="party_type_td"><? echo create_drop_down( "cbo_client", 130, $blank_array,"", 1, "Select Client", $selected, "" ); ?></td>
                                    <td>Style Owner</td>
                                    <td><?=create_drop_down( "cbo_style_owner", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Style Owner", $selected, ""); ?></td>
                                    <td>Region</td>
                                    <td><?=create_drop_down( "cbo_region", 130, $region,'', 1, "Select Region", $selected, "" ); ?></td>
                                    <td>Repeat No</td>
                                    <td><input style="width:80px;" class="text_boxes_numeric" name="txt_repeat_no" id="txt_repeat_no" />&nbsp;&nbsp;<input type="checkbox" name="chk_is_repeat" id="chk_is_repeat" onClick="copy_check(7);" value="2" style="width:12px;" title="Is Repeat No? If Uncheck Then Yes." ></td>
                                </tr>
                                <tr>
                                    <td>Quality Level</td>
                                    <td><?=create_drop_down( "cbo_qltyLabel", 130, $quality_label,"", 1, "Quality Level", $selected, "" ); ?></td>
									<td>Fit</td>
                                    <td><?=create_drop_down( "cbo_fit_id", 130, $fit_list_arr,"", 1, "--Fit List--", $selected, "" ); ?></td>  
                                    <td>Order Criteria</td>
                                    <td><?=create_drop_down( "cbo_order_criteria", 130,$order_criteria,"", 1, "-Select-", 1, "" ); ?></td>                       
                                    <td>Ship Mode</td>
                                    <td><?=create_drop_down( "cbo_ship_mode", 130,$shipment_mode, 1, "", $selected, "" ); ?></td>
                                </tr>
                                <tr>
                                    <td>B/Wash Color</td>
                                    <td><input class="text_boxes" type="text" style="width:120px;" name="txt_bodywashColor" id="txt_bodywashColor"/></td>
                                    <td class="must_entry_caption">Order Uom</td>
                                    <td><?=create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "get_unit_id(this.value)","","1,58" ); ?>
                                        <input class="text_boxes" type="text" style="width:60px;" name="tot_smv_qnty" id="tot_smv_qnty" placeholder="SMV" disabled/>
                                    </td>                                                           
                                    <td width="110">Working Company</td>
									<td><?=create_drop_down( "cbo_working_factory", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Working Company", $selected, "get_sew_company_config(this.value);"); ?></td>
									<td align="" width="110">W.Location </td>
									<td id="sew_location"><?=create_drop_down( "cbo_working_location_id", 130, $blank_array,"", 1, "-- Select Location --", $selected, ""); ?></td>
                                </tr>
                                <tr>
                                	<td>Remarks</td>
                                    <td colspan="3"><input class="text_boxes" type="text" style="width:355px;" name="txt_remarks" id="txt_remarks"/></td>
									<td>Ready for Budget</td>
                                    <td><? asort($yes_no); echo create_drop_down( "cbo_ready_for_budget", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
									<td width="110">Sample Req</td>
									<td>
									<input style="width:110px;" class="text_boxes" name="txt_req_no" id="txt_req_no" onDblClick="req_openmypage( 'requires/order_entry_controller.php?action=req_popup', 'Sample Req Form');" placeholder="Browse" readonly/> 
									</td>
                                </tr>
                                <tr>
                                    <td colspan="8" align="center">
                                        <?
                                        $sql_smv="select upper(style_ref) as style_ref, gmts_item_id, total_smv from ppl_gsd_entry_mst where status_active=1 and is_deleted=0";
                                        $sql_result=sql_select($sql_smv); $set_smv_arr=array();
                                        foreach($sql_result as $row)
                                        {
                                            $set_smv_arr[$row[csf('style_ref')]][$row[csf('gmts_item_id')]]+=$row[csf('total_smv')];
                                        }
                                        
                                        $other_cost_approved=return_field_value("current_approval_status","co_com_pre_costing_approval","job_no='$txt_job_no' and entry_form=15 and cost_component_id=12");
                                        
                                        $disabled=0; $disab=""; $disabl="";
                                        if($precostapproved==0 )
                                        {
                                            if($other_cost_approved==1)
                                            {
                                                echo '<p style="color:#FF0000;">Pre Cost Others Cost Approved, Any Change not allowed.</P>';
                                                $disab="disabled";
                                                $disabled=1;
                                            }
                                            else if($precostfound >0 ){
                                                echo "Pre Cost Found, only Sew. and Cut. SMV Change allowed";
                                                $disabled=1;
                                                $disab="disabled";
                                            }
                                            else $disabled=0;
                                        }
                                        else if($precostapproved==1 )
                                        {
                                            echo '<p style="color:#FF0000;">Pre Cost Approved, Any Change not allowed.</P>';
                                            $disabl="disabled"; $disab="disabled"; $disabled=1;
                                        }
                                        else $disabl="";
                                        
                                        if($set_smv_id==2 || $set_smv_id==3 || $set_smv_id==8) $readonly="disabled"; //Work Study 1 Bulletin 2
                                        else $readonly="";
                                        ?>
                                        <form id="setdetails_1" autocomplete="off">
                                            <input type="hidden" id="set_breck_down" />
                                            <input type="hidden" id="item_id"/>
                                            <input type="hidden" id="unit_id" value="1" />
                                            <table width="840" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                                                <thead>
                                                    <tr>
                                                        <th width="100">Product</th><th width="20">Set Ratio</th><th width="20">Sew SMV/ Pcs</th><th width="20">Cut SMV/ Pcs</th><th width="20">Fin SMV/ Pcs</th><th width="60">Complexity</th><th width="90">Print</th><th width="80">Embro</th><th width="80">Wash</th><th width="80">SP. Works</th><th width="80">Gmts Dyeing</th><th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?
                                                $smv_arr=array();
                                                $sql_d=sql_select("Select gmts_item_id, set_item_ratio, smv_pcs, smv_set, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id from wo_po_details_mas_set_details where job_no='$txt_job_no' order by id");
                                                foreach($sql_d as $sql_r)
                                                {
                                                    if($sql_r[csf('gmts_item_id')]=="") $sql_r[csf('gmts_item_id')]=0;
                                                    if($sql_r[csf('set_item_ratio')]=="") $sql_r[csf('set_item_ratio')]=0;
                                                    if($sql_r[csf('smv_pcs')]==""){
                                                        $sql_r[csf('smv_pcs')]=0;
                                                        $sql_r[csf('smv_set')]=0;
                                                    }
                                                    if($sql_r[csf('complexity')]=="") $sql_r[csf('complexity')]=0;
                                                    if($sql_r[csf('embelishment')]=="") $sql_r[csf('embelishment')]=0;
                                                    if($sql_r[csf('cutsmv_pcs')]==""){
                                                        $sql_r[csf('cutsmv_pcs')]=0;
                                                        $sql_r[csf('cutsmv_set')]=0;
                                                    }
                                                    if($sql_r[csf('finsmv_pcs')]==""){
                                                        $sql_r[csf('finsmv_pcs')]=0;
                                                        $sql_r[csf('finsmv_set')]=0;
                                                    }
                                                    if($sql_r[csf('printseq')]=="") $sql_r[csf('printseq')]=0;
                                                    if($sql_r[csf('embro')]=="") $sql_r[csf('embro')]=0;
                                                    if($sql_r[csf('embroseq')]=="") $sql_r[csf('embroseq')]=0;
                                                    
                                                    if($sql_r[csf('wash')]=="") $sql_r[csf('wash')]=0;
                                                    if($sql_r[csf('washseq')]=="") $sql_r[csf('washseq')]=0;
                                                    
                                                    if($sql_r[csf('spworks')]=="") $sql_r[csf('spworks')]=0;
                                                    if($sql_r[csf('spworksseq')]=="") $sql_r[csf('spworksseq')]=0;
                                                    
                                                    if($sql_r[csf('gmtsdying')]=="") $sql_r[csf('gmtsdying')]=0;
                                                    if($sql_r[csf('gmtsdyingseq')]=="") $sql_r[csf('gmtsdyingseq')]=0;
                                                    if($sql_r[csf('quot_id')]=="") $sql_r[csf('quot_id')]=0;
                                                
                                                    $smv_arr[]=implode("_",$sql_r);
                                                }
                                                $smv_srt=rtrim(implode("__",$smv_arr),"__");
                                                if(count($sql_d)){
                                                    $set_breck_down=$smv_srt;
                                                }
                                                //echo $set_breck_down;
                                                $data_array=explode("__",$set_breck_down);
                                                if($data_array[0]=="")
                                                {
                                                    $data_array=array();
                                                }
                                                
                                                if( count($data_array)>0)
                                                {
                                                    $i=0;
                                                    foreach( $data_array as $row )
                                                    {
                                                        $i++;
                                                        $data=explode('_',$row);
                                                        $tot_cutsmv_qnty+=$data[6];
                                                        $tot_finsmv_qnty+=$data[8];
                                                        ?>
                                                        <tr id="settr_<?=$i; ?>" align="center">
                                                            <td><? echo create_drop_down("cboitem_".$i, 150, get_garments_item_array(3), "",1,"Select Item", $data[0], "check_duplicate(".$i.",this.id ); check_smv_set(".$i."); check_smv_set_popup(".$i.");",$disabled,'' ); ?></td>
                                                            <td><input type="text" id="txtsetitemratio_<? echo $i;?>" name="txtsetitemratio_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)" value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else {echo "";}?> <? //echo $disab; ?> /></td>
                                                            <td><input type="text" id="smv_<? echo $i;?>" name="smv_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2]; ?>" <? echo $disabl." "; echo $readonly; ?>  />
                                                            <input type="hidden" id="smvset_<? echo $i;?>" name="smvset_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[3]; ?>" readonly/></td>
                                                            <td><input type="text" id="cutsmv_<? echo $i;?>" name="cutsmv_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(<? echo $i;?>)" value="<? echo $data[6]; ?>" <? echo $disabl." "; echo $readonly; ?> />
                                                            <input type="hidden" id="cutsmvset_<? echo $i;?>" name="cutsmvset_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[7] ?>" readonly/></td>
                                                            <td><input type="text" id="finsmv_<? echo $i;?>" name="finsmv_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" onChange="calculate_set_finsmv(<? echo $i;?>)" value="<? echo $data[8] ?>" <? echo $disab." "; echo $readonly; ?> />
                                                            <input type="hidden" id="finsmvset_<? echo $i;?>" name="finsmvset_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[9] ?>" readonly/></td>
                                                            <td><? echo create_drop_down("complexity_".$i, 60, $complexity_level, "",1,"Select", $data[4], "",$disabled,'' ); ?></td>
                                                            <td><? echo create_drop_down("emblish_".$i, 40, $yes_no, "",1,"Select", $data[5], "",$disabled,'' ); ?>
                                                            <input type="text" id="printseq_<? echo $i;?>" name="printseq_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[10] ?>" <? echo $disab." "; echo $readonly; ?> />
                                                            </td>
                                                            <td><? echo create_drop_down("embro_".$i, 45, $yes_no, "",1,"Select", $data[11], "",$disabled,'' ); ?>
                                                            <input type="text" id="embroseq_<? echo $i;?>" name="embroseq_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[12] ?>" <? echo $disab." "; echo $readonly; ?>/>
                                                            </td>
                                                            <td><? echo create_drop_down("wash_".$i, 45, $yes_no, "",1,"Select", $data[13], "",$disabled,'' ); ?>
                                                            <input type="text" id="washseq_<? echo $i;?>" name="washseq_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[14] ?>" <? echo $disab." "; echo $readonly; ?>/> </td>
                                                            <td><? echo create_drop_down("spworks_".$i, 45, $yes_no, "",1,"Select", $data[15], "",$disabled,'' ); ?>
                                                            <input type="text" id="spworksseq_<? echo $i;?>" name="spworksseq_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[16] ?>" <? echo $disab." "; echo $readonly; ?>/></td>
                                                            <td><? echo create_drop_down("gmtsdying_".$i, 45, $yes_no, "",1,"Select", $data[17], "",$disabled,'' ); ?>
                                                            <input type="text" id="gmtsdyingseq_<? echo $i;?>" name="gmtsdyingseq_<? echo $i;?>" style="width:20px" class="text_boxes_numeric" value="<? echo $data[18] ?>" <? echo $disab." "; echo $readonly; ?>/>
                                                            </td>
                                                            <td>
                                                            <input type="hidden" id="hidquotid_<? echo $i;?>" name="hidquotid_<? echo $i;?>" class="text_boxes_numeric" value="<? echo $data[19]; ?>" readonly/>
                                                            <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                                            <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                                            </td>
                                                        </tr>
                                                        <?
                                                    }
                                                }
                                                else
                                                {
                                                    ?>
                                                    <tr id="settr_1" align="center">
                                                        <td><?=create_drop_down( "cboitem_1", 120, get_garments_item_array(3), "",1,"Select", 0, "check_duplicate(1,this.id ); check_smv_set(1); check_smv_set_popup(1);",'','' ); ?></td>
                                                        <td><input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="1" disabled /></td>
                                                        
														<td><input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)" value="0" <? echo $readonly ?>  />
                                                        <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" value="0"/>
                                                        </td>

                                                        <td><input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(1)" value="0"  />
                                                        <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  />
                                                        </td>
                                                        <td><input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(1)" value="0"  />
                                                        <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  />
                                                        </td>
                                                        <td><? echo create_drop_down( "complexity_1", 60, $complexity_level, "",1,"Select", 0, "",'','' ); ?></td>
                                                        <td><? echo create_drop_down( "emblish_1", 45, $yes_no, "",1,"Select", 0, "",'','' ); ?>
                                                        <input type="text" id="printseq_1" name="printseq_1" style="width:20px" class="text_boxes_numeric" value="<? //echo $data[9] ?>" />
                                                        </td>
                                                        <td><? echo create_drop_down( "embro_1", 45, $yes_no, "",1,"Select", $data[5], "",$disabled,'' ); ?>
                                                        <input type="text" id="embroseq_1" name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="<? //echo $data[9] ?>" />
                                                        </td>
                                                        <td><? echo create_drop_down( "wash_1", 45, $yes_no, "",1,"Select", $data[5], "",$disabled,'' ); ?>
                                                        <input type="text" id="washseq_1" name="washseq_1" style="width:20px" class="text_boxes_numeric" value="<? //echo $data[9] ?>" />
                                                        </td>
                                                        <td><? echo create_drop_down( "spworks_1", 45, $yes_no, "",1,"Select", $data[5], "",$disabled,'' ); ?>
                                                        <input type="text" id="spworksseq_1" name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="<? //echo $data[9] ?>" />
                                                        </td>
                                                        <td><? echo create_drop_down( "gmtsdying_1", 45, $yes_no, "",1,"Select", $data[5], "",$disabled,'' ); ?>
                                                        <input type="text" id="gmtsdyingseq_1" name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="<? //echo $data[9] ?>" />
                                                        </td>
                                                        <td>
                                                        <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                                                        <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                                        <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <table width="840" cellspacing="0" class="rpt_table" border="0" rules="all">
                                                <tfoot>
                                                    <tr>
                                                        <th width="120">Total</th>
                                                        <th width="40"><input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 0;} ?>" disabled />
                                                        </th>
                                                        <th width="40"><input type="text" id="tot_smv_qnty_total" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" disabled />
                                                        </th>
                                                        <th width="40"><input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_cutsmv_qnty !=''){ echo $tot_cutsmv_qnty;} else{ echo 0;} ?>" disabled />
                                                        </th>
                                                        <th width="40"><input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_finsmv_qnty !=''){ echo $tot_finsmv_qnty;} else{ echo 0;} ?>" disabled />
                                                        </th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
										<input type="button" class="image_uploader" id="image_button_front" style="width:90px" value="IMAGE FRONT" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1)">

									    <input  type="button" id="image_button_front1" class="image_uploader" style="display:none;width:95px" value="ADD IMAGE FRONT" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'knit_order_entry_front', 0 ,1)" />
									</td>
                                    <td>
										<input type="button" class="image_uploader" id="image_button_back" style="width:85px" value="IMAGE BACK" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry_back', 0 ,1)">
										<input  type="button" id="image_button_back1" class="image_uploader" style="display:none;width:95px" value="ADD IMAGE BACK" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'knit_order_entry_back', 0 ,1)" />
								</td>
                                    <td><input type="button" class="image_uploader" style="width:90px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 2 ,1)"></td>
                                    <td><input type="button" id="set_button" class="image_uploader" style="width:85px;" value="Internal Ref" onClick="open_terms_condition_popup('requires/order_entry_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
                                    <td>&nbsp;</td>
                                    <td align="center" valign="middle">
                                        <input type="hidden" id="update_id">
										<input type="hidden" id="style_update_id">
                                        <input type="hidden" id="txt_quotation_id">
                                        <input type="hidden" id="txt_quotation_price">
                                        <input type="hidden" id="set_breck_down" />
                                        <input type="hidden" id="item_id" />
                                        <input type="hidden" id="item_smv_check" />
                                        <input type="hidden" id="tot_set_qnty" />
                                        <input type="hidden" id="hid_excessCut_source" />
                                        <input type="hidden" id="hid_excessCut_editable" />
                                        <input type="hidden" id="hid_cost_source" />
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="8" id="budgetApp_td" style="font-size:18px; color:#F00" align="center"></td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="8" valign="middle" class="button_container"><?=load_submit_buttons( $permission, "fnc_order_entry", 0,0,"refresh_page()",1); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
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
                                                    <td><input name="txt_job_qty" id="txt_job_qty" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled" /></td>
                                                    <td><input name="txt_avgUnit_price" id="txt_avgUnit_price" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_total_price" id="txt_total_price" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_proj_qty" id="txt_proj_qty" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_proj_avgUnit_price" id="txt_proj_avgUnit_price" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_proj_total_price" id="txt_proj_total_price" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_orginProj_qty" id="txt_orginProj_qty" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_orginProj_total_price" id="txt_orginProj_total_price" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                    <td><input name="txt_orginProj_total_amt" id="txt_orginProj_total_amt" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="Display" disabled="disabled"/></td>
                                                </tr>
                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            </form>
                        </fieldset>
                    </div>
                </td>
                <td valign="top" align="left">
                    <div id="po_list_view" width="400" style="padding-left: 10px; overflow: hidden;"></div>
                    <div id="country_po_list_view" width="400" style="padding-left: 10px; margin-top: 6px "></div>
                </td>
            </tr>
        </table>
	<div align="left">
	<fieldset style="width:1050px;">
		<legend>PO Details Entry</legend>
		<form id="orderentry_2" autocomplete="off">
			<table cellpadding="0" cellspacing="2" border="0" rules="all" >
				<thead class="form_table_header">
					<tr>
						<th colspan="2" class="must_entry_caption">Breakdown Type</th>
						<th colspan="4"><? echo create_drop_down( "cbo_breakdown_type", 200, $breakdown_type, "", 0,"Select Breakdown Type", '1',"fnc_noof_carton(this.value);", "" ,"","","","4"); ?></th>
						<th>Round Type</th>
						<th><? $round_type=array(1=>"Round Up",2=>"Round Down"); echo create_drop_down( "cbo_round_type", 55, $round_type, "", 1,"-Select-", 0,"", 1 ); ?></th>
						<th colspan="2"><strong>Copy PO</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled ></th>
						<th colspan="3"><strong>Copy PO No</strong><input class="text_boxes" name="txt_copypo_no" id="txt_copypo_no" type="text" style="width:75px" onBlur="fnc_copy_po(0,document.getElementById('update_id_details').value,document.getElementById('txt_po_no').value);" disabled/></th>
						<th colspan="3"><input type="button" id="reorder" value="Color Size Sequence" width="120" class="image_uploader" onClick="reorder_size_color();"/></th>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<th width="80">Order Status</th>
						<th width="110" class="must_entry_caption">PO No</th>
						<th width="60" class="must_entry_caption">PO Received Date</th>
						<th width="60" class="must_entry_caption">Pub. Shipment Date</th>
						<th width="60" class="must_entry_caption">Shipment Date</th>
						<th width="60">Fac. Receive Date</th>
						<th width="60" title="Pack Handover Date/Planned Cut Date" id="txt_phd_id">PHD/PCD</th>
						<th width="60" class="must_entry_caption" id="rate_td">Avg. Rate Pcs/Set</th>
						<th width="40">Up Charge</th>
						<th width="65" id="shtQty_td" class="must_entry_caption">Order Qty.</th>
						<th width="55">No Of Carton</th>
						<th width="80">Projected Po</th>
						<th width="65" id="txt_grouping_id">Internal Ref/ Grouping</th>
						<th width="55" id="file_year_id">File Year</th>
						<th width="65" id="file_no_id">Comm File No</th>
						<th width="70">Packing</th>
						<th>Actual PO No.</th>
					</tr>
				</thead>
				<tr>
					<td><?= create_drop_down( "cbo_order_status", 80, $order_status, 0, "", $selected,"", "po_recevied_date( this.value );" ); ?></td>
					<td><input class="text_boxes" name="txt_po_no" id="txt_po_no" type="text" placeholder="Write" style="width:100px" onBlur="fnc_buyer_style_po_check(this.value);"/></td>
					<td><input name="txt_po_received_date" id="txt_po_received_date" class="datepicker" type="text" onChange="set_phd_po_date(2);" style="width:50px;" readonly/></td>
					<td><input name="txt_pub_shipment_date" id="txt_pub_shipment_date" class="datepicker" type="text"  style="width:50px;" onChange="set_publish_shipment_date();" readonly/></td>
					<td><input name="txt_shipment_date" id="txt_shipment_date" class="datepicker" type="text"  style="width:50px;" onChange="set_pub_ship_date(); shipment_date_diff(); set_tna_task();" readonly/></td>
					<td><input  name="txt_factory_rec_date" id="txt_factory_rec_date" class="datepicker" type="text" style="width:50px;" readonly/></td>
					<td><input name="txt_phd" id="txt_phd" class="datepicker" type="text"  style="width:50px;" onChange="set_phd_po_date(1);" readonly/></td>
					<td><input name="txt_avg_price" id="txt_avg_price" class="text_boxes_numeric" type="text" style="width:50px;" onBlur="fnc_calculateRate(document.getElementById('cbo_breakdown_type').value,0);" /></td>
					<td><input name="txt_upCharge" id="txt_upCharge" class="text_boxes_numeric" type="text" style="width:30px;" /></td>
					<td><input name="txt_docSheetQty" id="txt_docSheetQty" class="text_boxes_numeric" type="text" style="width:55px;" /></td>
					<td><input name="txt_noOf_carton" id="txt_noOf_carton" class="text_boxes_numeric" type="text" style="width:45px;" disabled /></td>
					<td id="projected_po_td"><? echo create_drop_down( "cbo_projected_po", 80,$blank_array, "", 1, "Select", "" ); ?></td>
					<td ><input type="text" id="txt_grouping" name="txt_grouping" class="text_boxes" style="width:55px"></td>
					<td id="file_year_td"><?=create_drop_down( "txt_file_year", 55, $blank_array,"", 1, "-Select-", "", "","","" ); ?></td>
					<td><input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes"  style="width:55px"></td>
					<td><? echo create_drop_down( "cbo_packing_po_level", 70, $packing,"", 1, "Select", "", "","","" ); ?></td>
					<td><input type="button" id="reorder" value="Actual PO" width="90" class="image_uploader" onClick="pop_entry_actual_po();"/></td>
				</tr>
				<tr>
					<td><strong>Remarks</strong></td>
                    <td colspan="6"><input name="txt_po_remarks" id="txt_po_remarks" class="text_boxes" type="text" style="width:420px;"/></td>
					<th><strong>Delay For</strong></th>
					<td colspan="3"><?=create_drop_down( "cbo_delay_for", 145, $delay_for, 0, "", 1, "" ); ?></td>
					<td><strong>Po Status</strong></td>
					<td><?=create_drop_down( "cbo_status", 65, $row_status, 0, "", 1, "" ); ?></td>
					<td align="right"><strong>SC/LC:</strong></td>
                    <td><input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" style="width:55px"></td>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
				</tr>
			</table>
			<table width="600">
				<tr><td width="400">
					<table rules="all" width="400" cellpadding="0" cellspacing="2" border="1" class="rpt_table">
						<thead>
							<th width="100" class="must_entry_caption">Gmts. Product</th>
							<th width="80" class="must_entry_caption">Delivery Country</th>
							<th width="80">Country Code</th>
							<th width="100">Code</th>
							<!-- <th>Country</th>
							<th>Country Code</th> -->
							<th width="70">Cut-off Date</th>
							<th width="70">Cut-Off</th>
							<th width="70" class="must_entry_caption">Country Ship Date</th>
							<!-- <th id="pack_type">Pack Type</th>
							<th id="pcsQty_td">Pcs Per Pack</th> -->
						</thead>
						<tbody>
							<tr>
								<td id="itm_td">
									<?=create_drop_down( "cbo_gmtsItem_id", 120, $garments_item, 0, 1, "Select Item", $selected_item,"fnc_calAmountQty_ex(0,1); fnc_calculateRate(document.getElementById('cbo_breakdown_type').value,0);",1);  ?>
								</td>
								<td>
									<?=create_drop_down( "cbo_deliveryCountry_id", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "Select Country", "","load_drop_down( 'requires/order_entry_controller', this.value, 'load_dorp_down_code', 'code_td' ); fnc_cut_off_select(this.value); fnc_set_ship_date(); fnc_calculateRate(document.getElementById('cbo_breakdown_type').value,0);change_country_code(this.value)" ); ?>
									<input type="hidden" id="hid_prev_country" />
								</td>
								<td>
									<?=create_drop_down( "cbo_countryCode_id", 100,"select id, short_name from lib_country where status_active=1 and is_deleted=0 and short_name is not null  order by country_name", "id,short_name", 1, " Country Code", "","change_country(this.value)" ); ?>
								</td>
								<td id="code_td">
									<?=create_drop_down( "cbo_code_id", 100,$blank_array, "", 1, "Select Code", "","" ); ?>
								</td>
								<td style="display: none;">
									<?=create_drop_down( "cbo_country_id", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "Country", ""," load_drop_down( 'requires/order_entry_controller', this.value, 'load_dorp_down_countryCode', 'countryCode_td' );" ); ?></td>
								<td id="countryCode_td" style="display: none;"><?=create_drop_down( "cbo_countryCode_id", 100,$blank_array, "", 1, "Country Code", "","" ); ?>=</td>
								<td><input name="txt_cutup_date" id="txt_cutup_date" class="datepicker" type="text" style="width:65px;" onChange="fnc_set_ship_date();"/></td>
								<td><?=create_drop_down( "cbo_cutOff_id", 100, $cut_up_array, 0, 1, "Select", "","fnc_set_ship_date();" );  ?></td>
								<td><input name="txt_countryShip_date" id="txt_countryShip_date" class="datepicker" type="text" style="width:65px;"/>
									<input name="txt_is_update" id="txt_is_update" class="text_boxes" type="hidden" style="width:30px;" value="0"/></td>
									<input name="txt_breakdownGrouping" id="txt_breakdownGrouping" class="text_boxes" maxlength="5" placeholder="5 Char." type="hidden" style="width:40px;" onBlur="fnc_packtype(this.value);" disabled/>
									<input name="txt_pcsQty" id="txt_pcsQty" class="text_boxes_numeric" type="hidden" style="width:40px;" disabled/>
								</tr>
							</tbody>
						</table>
					</td><td>
						<table rules="all" width="300" cellpadding="0" cellspacing="2" border="1" class="rpt_table">
							<thead>
								<th width="60" title="All Size All Color.">Copy Qty. ASAC</th>
								<th width="60" title="All Size Same Color.">Copy Qty. ASSC</th>
								<th width="60" title="All Color Same Size.">Copy Qty. ACSS</th>
								<th width="60">Copy Ex.Cut %</th>								
								<th width="60" title="All Size Same Color.">Copy Rate ASSC</th>
								<!-- <th width="60">Copy  Article No</th> -->
							</thead>
							<tbody>
								<tr>
									<td width="60" align="center"><input type="checkbox" name="copy_asac" id="copy_asac" onClick="copy_check(2)" value="2" ></td>
									<td width="60" align="center"><input type="checkbox" name="copy_assc" id="copy_assc" onClick="copy_check(3)" value="2" ></td>
									<td width="60" align="center"><input type="checkbox" name="copy_acss" id="copy_acss" onClick="copy_check(4)" value="2" ></td>
									<td width="60" align="center"><input type="checkbox" name="copy_excut" id="copy_excut" onClick="copy_check(5)" value="2" ></td>
									<!-- <td width="60" align="center"><input type="checkbox" name="copy_article" id="copy_article" onClick="copy_check(8)" value="2" ></td> -->									
									<td width="60" align="center"><input type="checkbox" name="copy_assc_rate" id="copy_assc_rate" onClick="copy_check(9)" value="2" ></td>
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
						<div id="breakdown_div"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top"><input type="button" id="btn" name="btn" value="Quantity Breakdown" class="formbuttonplasminus" style="width:150px;" onClick="fnc_create_qty_breakdown(document.getElementById('txt_is_update').value,document.getElementById('cbo_breakdown_type').value);" /></td>
					<td height="50" valign="middle" align="center" class="button_container">
						<input type="hidden" id="update_id_details"/>
                        <input type="hidden" id="hidd_bhpo_id"/>
						<input type="hidden" id="color_size_break_down" value="" />
						<input type="hidden" id="color_size_break_down_all_data" value="" />
						<input type="hidden" id="size_all_id" value="" /> 
						<input type="hidden" id="color_size_ratio_data" value="" />
						<input type="hidden" id="txt_po_datedif_hour" />
						<input type="hidden" id="txt_user_id" />
						
						<? echo load_submit_buttons( $permission, "fnc_order_entry_details", 0,0 ,"reset_form('orderentry_2','','','','fnc_resetPoDtls()','txt_po_received_date*txt_file_year*txt_file_no*cbo_gmtsItem_id*cbo_deliveryCountry_id');",2); ?>
						<input type="hidden" id="reset_btn" class="formbutton" style="width:80px" value="Delete PO No" onClick="fnc_delete_po(2);" />
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
</div>

<!--<div id="deleted_po_list_view"></div>-->
</div>
<script>
	function get_unit_id(value){
		document.getElementById("unit_id").value = value;
		if(value == 1){
			document.getElementById("txtsetitemratio_1").value = 1;
			document.getElementById("txtsetitemratio_1").disabled = true;
		}
		if(value == 58){
			document.getElementById("txtsetitemratio_1").value = 1;
			document.getElementById("txtsetitemratio_1").disabled = false;

		}
	}
	function add_break_down_set_tr( i )
	{
		var unit_id= document.getElementById('unit_id').value;
		if(unit_id==1)
		{
			alert('Only One Item');
			return false;
		}
		var row_num=$('#tbl_set_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}

		var setsmv=document.getElementById('set_smv_id').value;
		//alert(setsmv);
		if(setsmv==3)
		{
			if(form_validation('smv_'+i,'Sew SMV')==false)
			{
				$('#smv_'+i).focus();
				return;
			}
		}

		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		else
		{
			i++;
			$("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_set_details");
			$('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			$('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);check_smv_set("+i+");check_smv_set_popup("+i+");");
			//$('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);check_smv_set_popup("+i+")");
			$('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			$('#cutsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_cutsmv("+i+")");
			$('#finsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_finsmv("+i+")");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			$('#cboitem_'+i).val('');
			set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
			set_sum_value_set( 'tot_smv_qnty_total', 'smvset_' );
			set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
			set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
		}
	}

	function fn_delete_down_tr(rowNo,table_id)
	{

		if(table_id=='tbl_set_details')
		{
			var numRow = $('table#tbl_set_details tbody tr').length;
			if($('#unit_id').val()==58 && numRow<=2)
			{
				alert("Please select Minimum 2 Item for SET UOM");
				return;
			}
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_set_details tbody tr:last').remove();
			}
			set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
			set_sum_value_set( 'tot_smv_qnty_total', 'smvset_' );
			set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
			set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
		}
	}

	function check_duplicate(id,td)
	{
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref','Company*Buyer*Style Ref.')==false)
		{
			$('#cboitem_'+id).val(0);
			return;
		}
		
		if( ($('#set_smv_id').val()*1 )==3) 
		{
			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_style_ref').attr('disabled','disabled');
		}
		var item_id=(document.getElementById('cboitem_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('cboitem_'+k).value)
				{
					alert("Same Gmts Item Duplication Not Allowed.");
					document.getElementById(td).value="0";
					document.getElementById(td).focus();
				}
			}
		}
	}

	function check_smv_set(id)
	{
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref','Company*Buyer*Style Ref.')==false)
		{
			$('#cboitem_'+id).val(0);
			return;
		}
		
		if( ($('#set_smv_id').val()*1 )==3) 
		{
			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_style_ref').attr('disabled','disabled');
		}
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		//alert(item_id);
		var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		var set_smv_id=document.getElementById('set_smv_id').value;
		var item_id=$('#cboitem_'+id).val();
		//alert(td);
		//get_php_form_data(company_id,'set_smv_work_study','requires/woven_order_entry_controller' );
		var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'requires/order_entry_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			if(set_smv_id==1)
			{
				$('#smv_'+id).val(response[1]);
				$('#tot_smv_qnty').val(response[1]);
				/*for (var k=1;k<=row_num; k++)
				 {
				 $('#smv_'+k).val(response[1]);
				}*/
			}
		}
	}

	function disable_smv_set()
	{
		var row_num=$('#tbl_set_details tr').length-1;
		var txt_style_ref=encodeURIComponent("'"+$('#txt_style_ref').val()+"'");
		var set_smv_id=document.getElementById('set_smv_id').value;
		console.log(set_smv_id);
		for(var id =1;id<=row_num;id++)
		{
			var smv=(document.getElementById('smv_'+id).value);
			var item_id=$('#cboitem_'+id).val();
			//alert(td);
			//get_php_form_data(company_id,'set_smv_work_study','requires/woven_order_entry_controller' );
			var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'disable_smv_work_study', '', 'requires/order_entry_controller');
			var response=response.split("_");
			if(response[0]==1)
			{
				if(set_smv_id==3 || set_smv_id==8)
				{
				     $("#smv_"+id).attr('readonly','readonly');
				     $("#cutsmv_"+id).attr('readonly','readonly');
				     $("#finsmv_"+id).attr('readonly','readonly');
				}
			}
		}
	}

	function check_smv_set_popup(id)
	{
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref','Company*Buyer*Style Ref.')==false)
		{
			$('#cboitem_'+id).val(0);
			return;
		}
		
		if( ($('#set_smv_id').val()*1 )==3) 
		{
			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_style_ref').attr('disabled','disabled');
		} 
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;

		var txt_style_ref=encodeURIComponent(""+$('#txt_style_ref').val()+"");
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var set_smv_id=document.getElementById('set_smv_id').value;
		var item_id=$('#cboitem_'+id).val();
		//alert(cbo_company_name);
		if(set_smv_id==3 || set_smv_id==8)
		{
			var page_link="requires/order_entry_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
		}
		else
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
			var smv_data=selected_smv_data.split("_");
			var row_id=smv_data[3];

			$("#smv_"+row_id).val(smv_data[0]);
			$("#smv_"+row_id).attr('readonly','readonly');
			$("#cutsmv_"+row_id).val(smv_data[1]);
			$("#cutsmv_"+row_id).attr('readonly','readonly');
			$("#finsmv_"+row_id).val(smv_data[2]);
			$("#finsmv_"+row_id).attr('readonly','readonly');
			$("#hidquotid_"+row_id).val(smv_data[4]);

			calculate_set_smv(row_id);
		}
	}

	function calculate_set_smv(i)
	{
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('smv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('smvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		set_sum_value_set( 'tot_smv_qnty_total', 'smvset_' );

		calculate_set_cutsmv(i);
		calculate_set_finsmv(i);
	}

	function calculate_set_cutsmv(i)
	{
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('cutsmv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('cutsmvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
	}

	function calculate_set_finsmv(i)
	{
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('finsmv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('finsmvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
	}

	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		if(des_fil_id=="tot_set_qnty")
		{
			math_operation( des_fil_id, field_id, '+', rowCount );
		}
		else if(des_fil_id=="tot_smv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		else if(des_fil_id=="tot_smv_qnty_total")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		if(des_fil_id=="tot_cutsmv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		if(des_fil_id=="tot_finsmv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
	}

	function js_set_value_set()
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		var set_breck_down="";
		var item_id=""
		var unit_id
		for(var i=1; i<=rowCount; i++)
		{
			if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
			{
				return;
			}
			var smv =document.getElementById('smv_'+i).value;
			if(smv==0)
			{
				alert("Smv 0 not accepted");
				return;
			}
			if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0)
			if($('#cboitem_'+i).val()=='') $('#cboitem_'+i).val(0)
			if($('#cutsmv_'+i).val()=='') $('#cutsmv_'+i).val(0)
			if($('#cutsmvset_'+i).val()=='') $('#cutsmvset_'+i).val(0)
			if($('#finsmv_'+i).val()=='') $('#finsmv_'+i).val(0)
			if($('#finsmvset_'+i).val()=='') $('#finsmvset_'+i).val(0)
			if($('#printseq_'+i).val()=='') $('#printseq_'+i).val(1)
			if($('#embroseq_'+i).val()=='') $('#embroseq_'+i).val(2)
			if($('#washseq_'+i).val()=='') $('#washseq_'+i).val(3)
			if($('#spworksseq_'+i).val()=='') $('#spworksseq_'+i).val(4)
			if($('#gmtsdyingseq_'+i).val()=='') $('#gmtsdyingseq_'+i).val(5)
			if(set_breck_down=="")
				{
					set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val()+'_'+$('#hidquotid_'+i).val();
					item_id+=$('#cboitem_'+i).val();
				}
				else
				{
					set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val()+'_'+$('#hidquotid_'+i).val();

					item_id+=","+$('#cboitem_'+i).val();
				}
			}

				if($('#unit_id').val()==58 && rowCount<=1)
				{
					alert("Please select Minimum 2 Item for SET UOM");
					return;
				}
				document.getElementById('set_breck_down').value=set_breck_down;
				document.getElementById('item_id').value=item_id;

				//parent.emailwindow.hide();
			}

			function open_emblishment_pop_up(i)
			{
				var page_link="order_entry_controller.php?action=open_emblishment_list";
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=620px,height=300px,center=1,resize=1,scrolling=0','../')
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
				}
			}
	</script>
	<script>
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		set_sum_value_set( 'tot_smv_qnty_total', 'smvset_' );
		set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
		set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
	</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
</body>

<script>set_multiselect('cbo_delay_for','0','0','','');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_buyer_name').val('0');</script>
</html>