<?
/*-------------------------------------------- Comments -----------------------
Purpose			         : 	This Form Will Create Knit Garments Pre Cost Entry V3.
Functionality	         :
JS Functions	         :
Created by		         :	Kausar
Creation date 	         : 	12-06-2021
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :  
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];
$field_lavel_access = array();
if(count($_SESSION['logic_erp']['data_arr'][520])>0)
{
	$field_lavel_access = $_SESSION['logic_erp']['data_arr'][520];
}

$data_arr= json_encode($field_lavel_access); //print_r($data_arr);
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Pre Cost Entry V3","../../", 1, 1, $unicode,1,'');
$qcCons_from=return_field_value("excut_source","variable_order_tracking","excut_source=2 and variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");

?>

<script type="text/javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<?=$permission; ?>';
var user_level = '<?=$user_level; ?>';
var cmValidation='<?=$qcCons_from; ?>';
	//alert(cmValidation);
var israte_popup=2;
var mst_mandatory_field ="";
<?
//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][520] );
echo "var field_level_data= ". $data_arr . ";\n";

//For Mandatory
//echo $_SESSION['logic_erp']['mandatory_field'][520][5].'kausar'; //die;
/*if($_SESSION['logic_erp']['mandatory_field'][520][3]!="")
{
	echo "var mst_mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][520][3]) . "';\n";
	echo "var mst_mandatory_message = '". ($_SESSION['logic_erp']['mandatory_message'][520][3]) . "';\n";
}*/


?>
var str_construction=[<?=substr(return_library_autocomplete("select construction from wo_pri_quo_fabric_cost_dtls group by construction","construction"), 0, -1); ?>];
var str_composition=[<?=substr(return_library_autocomplete("select composition from wo_pri_quo_fabric_cost_dtls group by composition","composition"), 0, -1); ?>];
var str_incoterm_place=[<?=substr(return_library_autocomplete("select incoterm_place from  wo_price_quotation group by incoterm_place","incoterm_place" ), 0, -1); ?>];
var str_trimdescription=[<?=substr(return_library_autocomplete("select description from  wo_pre_cost_trim_cost_dtls group by description","description"), 0, -1); ?>];

	function trims_description_autocomplete(trim_group,i){
		var description=return_global_ajax_value(trim_group, 'trims_description', '', 'requires/pre_cost_entry_controller_v3');
		//alert("description");
		description=description.split(",");

		var str_trimdescription_g = new Array();
		for( j=0; j<=description.length; j++ ){
			//alert(description[i])
			str_trimdescription_g.push(description[j]);
		}
		$("#txtdescription_"+i).autocomplete({
			source: str_trimdescription_g
		});
	}

	function set_auto_complete(type){
		if(type=='pre_cost_mst'){
			$("#txt_incoterm_place").autocomplete({
				source: str_incoterm_place
			});
		}
		if(type=='tbl_fabric_cost'){
			var row_num=$('#tbl_fabric_cost tr').length-1;
			for (var i=1; i<=row_num; i++){
				$("#txtconstruction_"+i).autocomplete({
					source: str_construction
				});
				$("#txtcomposition_"+i).autocomplete({
					source:  str_composition
				});
			}
		}
		if(type=='tbl_trim_cost'){
			var row_num=$('#tbl_trim_cost tr').length-1;
			for (var i=1; i<=row_num; i++){
				$("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
				});
			}
		}
	}
	
	function fnc_mandatorytdcolor()
	{
		//return;
		var celid=mst_mandatory_field.split("*")
		//alert( celid.length+"="+mst_mandatory_field+"="+celid)
		var a=0;
		for (var i = 1; i <= celid.length; i++)
		{
			var td=$('#'+celid[a]).val();
			//alert(td+'='+celid[a])
			$('#'+celid[a]).closest('td').prev().css('color', 'blue');
			a++;
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id,tr)
	{
		var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
		if(r==false)
		{
			return;
		}
		else
		{
		}


		if(table_id=='tbl_fabric_cost'){
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#updateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();

				if(updateid !="" && permission_array[2]==1){

					var is_booking=return_global_ajax_value(updateid+"__1__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v3');
					var ex_booking_data=is_booking.split("***");
					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else if(ex_booking_data[0]==1)
					{
						var booking_msg="Booking Found, Delete Restricted.\n Booking No : "+ex_booking_data[1];
						alert(booking_msg);
						return
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_fabric_cost', '', 'requires/pre_cost_entry_controller_v3');
					}
				}
				//var index=rowNo-1
				//$("table#tbl_fabric_cost tbody tr:eq("+index+")").remove()
				//var numRow = $('table#tbl_fabric_cost tbody tr').length;
				
				var index = $(tr).closest("tr").index();
				//alert(index)
				$("table#tbl_fabric_cost tbody tr:eq("+index+")").remove();
				var numRow = $('table#tbl_fabric_cost tbody tr').length;
				
				for(i = rowNo;i <= numRow;i++){
					$("#tbl_fabric_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'value': function(_, value) { return value }
						});
						$("#tbl_fabric_cost tr:eq("+i+")").removeAttr('id').attr('id','fabriccosttbltr_'+i);
						if($('#seq_'+i).val()!= "")
						{
							$('#seq_'+i).val( i );
						}
						$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
						$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost',this);");
						$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
						$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");

						$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
						$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
						$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
						$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v3.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
						$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
						$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");

					})
				}
			}
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required();

		}
		else if(table_id=='tbl_yarn_cost')
		{
			alert("Not Allowed to delete row")
			return;
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#updateidyarncost_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
					var booking=return_global_ajax_value(updateid, 'delete_row_yarn_cost', '', 'requires/pre_cost_entry_controller_v3');
				}
				var index=rowNo-1
				$("table#tbl_yarn_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_yarn_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_yarn_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }
							});

						  $('#increaseyarn_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_yarn_cost("+i+",this);");
						  $('#decreaseyarn_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_yarn_cost',this);");
						  $('#percentone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_one');");
						  $('#cbocompone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_one');");
						  if($('#txtyarnseq_'+i).val()!= "")
							{
								$('#txtyarnseq_'+i).val( i );
							}
						  //$('#percenttwo_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_two');");
						  //$('#cbocomptwo_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_two');");
						  //$('#consratio_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio("+i+",'calculate_consumption')");
						  $('#consqnty_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio("+i+",'calculate_amount')");
						  $('#txtrateyarn_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio("+i+",'calculate_amount')");
					})
				}
			}
			set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
			set_sum_value( 'txtavgconsumptionyarn_sum', 'avgconsqnty_', 'tbl_yarn_cost' );
			set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
			calculate_yarn_consumption_ratio(rowNo,'calculate_ratio');
		}
		else if(table_id=='tbl_conversion_cost')
		{
			var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/pre_cost_entry_controller_v3');
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#updateidcoversion_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();

				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__3__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v3');
					var ex_booking_data=is_booking.split("***");

					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else if(ex_booking_data[0]==1)
					{
						var booking_msg="Booking Found, Delete Restricted.\n Booking No : "+ex_booking_data[1];
						alert(booking_msg);
						return
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_conversion_cost', '', 'requires/pre_cost_entry_controller_v3');
					}
				}
				var index=rowNo-1
				$("table#tbl_conversion_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_conversion_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_conversion_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});

					   $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
					   $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","add_yarn_conversion_cost("+i+",this)");
					   $('#txtprocessloss_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
					   $('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
					   $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+");");
					   $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost',this);");
					   $('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
					   $('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
					   $('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
					})
				}
			}
			set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
			set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
			set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
			set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		}
		else if(table_id =='tbl_trim_cost')
		{
			var numRow = $('table#tbl_trim_cost tbody tr').length;
			console.log(numRow);
			/*if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_trim_cost tbody tr:last').remove();
			}*/
			if(numRow!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#updateidtrim_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__2__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v3');
					var ex_booking_data=is_booking.split("***");
					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else if(ex_booking_data[0]==1)
					{
						var booking_msg="Booking Found, Delete Restricted.\n Booking No : "+ex_booking_data[1];
						alert(booking_msg);
						return
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_trim_cost', '', 'requires/pre_cost_entry_controller_v3');
						if(booking==11){
							alert("Booking Found, Delete Not Allowed");
							return;
						}
					}
				}
				var index=rowNo-1
				$("table#tbl_trim_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_trim_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_trim_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
						$('#tbl_trim_cost tr:eq('+i+') td:eq(4)').attr('id','tdsupplier_'+i);
						if($('#seq_'+i).val()=="" || $('#seq_'+i).val()!="")
						{
							$('#seq_'+i).val( i );
						}
						$('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+" )");
						$('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup('"+i+"_1' )");
						$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
						$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost',this);");
						$('#txtconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
						$('#txtdescription_'+i).removeAttr("onDblClick").attr("onDblClick","trims_description_popup( "+i+" )");
						//$('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","open_calculator( "+i+" )");
						$('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick"," set_session_large_post_data_trim('requires/pre_cost_entry_controller_v3.php?action=consumption_popup_trim', 'Consumtion Entry Form',"+i+")");
						$('#txttrimrate_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
						$('#txttrimrate_'+i).removeAttr("onDblClick").attr("onDblClick","trim_rate_popup( "+i+" )");
					})
				}
			}
			set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
			set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
		}
		else if(table_id=='tbl_embellishment_cost')
		{
			/*var numRow = $('table#tbl_embellishment_cost tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_embellishment_cost tbody tr:last').remove();
			}*/
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#embupdateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__6__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v3');
					var ex_booking_data=is_booking.split("***");

					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else if(ex_booking_data[0]==1)
					{
						var booking_msg="Booking Found, Delete Restricted.\n Booking No : "+ex_booking_data[1];
						alert(booking_msg);
						return
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_embellishment_cost', '', 'requires/pre_cost_entry_controller_v3');
					}
				}
				var index=rowNo-1
				$("table#tbl_embellishment_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_embellishment_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_embellishment_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }
							});

						  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_embellishment_cost("+i+");");
						  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost',this);");
						  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
						  $('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
						  $('#cboembname_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
						  $('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
					})
				}
			}
			set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			//calculate_main_total()
		}

		else if(table_id=='tbl_wash_cost')
		{
			/*var numRow = $('table#tbl_wash_cost tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_wash_cost tbody tr:last').remove();
			}*/
			var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/pre_cost_entry_controller_v3');
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#embupdateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__6__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v3');
					var ex_booking_data=is_booking.split("***");
					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else if(ex_booking_data[0]==1)
					{
						var booking_msg="Booking Found, Delete Restricted.\n Booking No : "+ex_booking_data[1];
						alert(booking_msg);
						return
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_wash_cost', '', 'requires/pre_cost_entry_controller_v3');
					}
				}
				var index=rowNo-1
				$("table#tbl_wash_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_wash_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_wash_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }
							});
						  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_wash_cost("+i+");");
						  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost',this);");
						  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
						  $('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
						  if(conversion_from_chart==1)
						  {
							  $('#txtembrate_'+i).removeAttr("onClick").attr("onClick","set_wash_charge_unit_pop_up( "+i+" )");
						  }
						  $('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate_wash( "+i+" )");
					})
				}
			}
			set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
			calculate_main_total()
		}
		else if(table_id=='tbl_commission_cost')
		{
			var numRow = $('table#tbl_commission_cost tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_commission_cost tbody tr:last').remove();
			}
			/*else
			{
																																																																																																																																																																																																																																																																																																																																										reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
			} */
			set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
			set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
			calculate_main_total();

		}
		else if(table_id=='tbl_comarcial_cost')
		{
			/*var numRow = $('table#tbl_comarcial_cost tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_comarcial_cost tbody tr:last').remove();
			}*/
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__0__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v3');
					var ex_booking_data=is_booking.split("***");

					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller_v3');
					}
				}
				var index=rowNo-1
				$("table#tbl_comarcial_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_comarcial_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_comarcial_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }
							});
						  $('#increasecomarcial_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_comarcial_cost("+i+");");
						  $('#decreasecomarcial_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_comarcial_cost',this);");
						  $('#txtcomarcialrate_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'cal_amount' )");
						  $('#txtcomarcialamount_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'cal_rate')");
					})
				}
			}
			set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
			set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
		}
	}

	function show_sub_form(update_id, action, extra_str)
	{
		var check_is_master_part_saved=return_global_ajax_value(update_id, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v3');
		if(trim(check_is_master_part_saved)=="")
		{
			alert ("Save Master Part")	;
			return;
		}

		if(update_id=="")
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
			$(this).html('Quotation id is Empty').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
		}
		else
		{
			var fab_cost=$("#txt_fabric_pre_cost").val()*1;		var pre_fab_cost=$("#txt_fabric_pre_cost").attr('pre_fab_cost')*1;
			var trim_cost=$("#txt_trim_pre_cost").val()*1;		var pre_trim_cost=$("#txt_trim_pre_cost").attr('pre_trim_cost')*1;
			var embl_cost=$("#txt_embel_pre_cost").val()*1;		var pre_embl_cost=$("#txt_embel_pre_cost").attr('pre_emb_cost')*1;
			var wash_cost=$("#txt_wash_pre_cost").val()*1;		var pre_wash_cost=$("#txt_wash_pre_cost").attr('pre_wash_cost')*1;
			var comml_cost=$("#txt_comml_pre_cost").val()*1;	var pre_comml_cost=$("#txt_comml_pre_cost").attr('pre_comml_cost')*1;
			var comms_cost=$("#txt_commission_pre_cost").val()*1; var pre_comms_cost=$("#txt_commission_pre_cost").attr('pre_commis_cost')*1;

			if(fab_cost!=pre_fab_cost)
			{
				alert("Fabric Cost Change Found, Please Save or Update.");
				return;
			}
			if(trim_cost!=pre_trim_cost)
			{
				alert("Trims Cost Change Found, Please Save or Update");
				release_freezing();
				return;
			}
			if(embl_cost!=pre_embl_cost)
			{
				alert("Embel. Cost Change Found, Please Save or Update.");
				return;
			}
			if(wash_cost!=pre_wash_cost)
			{
				alert("Wash Cost Change Found, Please Save or Update.");
				return;
			}
			/*if(comml_cost!=pre_comml_cost)
			{
				alert("Comml. Cost Change Found, Please Save or Update.");
				return;
			}*/
			if(comms_cost!=pre_comms_cost)
			{
				alert("Commission Cost Change Found, Please Save or Update.");
				return;
			}
			//calculate_main_total();
			if(action=="show_fabric_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v3','');
				set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
				//yarn
				//set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
				set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
				set_sum_value( 'txtavgconsumptionyarn_sum', 'avgconsqnty_', 'tbl_yarn_cost' );
				set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
				//yarn end
				// conversion
				set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );

				set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
				// conversion end
				set_auto_complete('tbl_fabric_cost');
				var tot_yarn_needed=document.getElementById('tot_yarn_needed').value;
				var txtavgconsumptionyarn_sum=document.getElementById('txtavgconsumptionyarn_sum').value;
				if((tot_yarn_needed*1)<(txtavgconsumptionyarn_sum*1))
				{
					document.getElementById('txtavgconsumptionyarn_sum').style.backgroundColor='#F00';
				}
			}

			if(action=="show_trim_cost_listview")
			{
				show_list_view(update_id+'*'+extra_str+'*'+document.getElementById('txt_quotation_id').value+'*'+document.getElementById('copy_quatation_id').value+'*'+document.getElementById('cbo_company_name').value+'*'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v3','');
				set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
				set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
				set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
				var str_trimdescription = [ <? //echo substr(return_library_autocomplete("select description from  wo_pre_cost_trim_cost_dtls group by description", "description" ), 0, -1); ?> ];

				set_auto_complete('tbl_trim_cost');
				/*var numRow = $('table#tbl_trim_cost tbody tr').length;
				for(i = 1;i <= numRow;i++)
				{
				set_multiselect('cbogroup_'+i,'1','0',document.getElementById("cbogroup_"+i).value,'0');
				}*/
			}

			if(action=="show_embellishment_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v3','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			}

			if(action=="show_wash_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v3','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
			}

			if(action=="show_commission_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v3','');
				set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
				set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
			}

			if(action=="show_comarcial_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_final_price_dzn_pre_cost').value+'_'+document.getElementById('txt_commission_pre_cost').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v3','');
				set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
				set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
			}
			//partial pre cost copy -----------------new development kaiyum-------------------------------------------
			if(action=="partial_pre_cost_copy_action")
			{
				var page_link='requires/pre_cost_entry_controller_v3.php?action=partial_pre_cost_copy_popup';
				var title='Partial Pre Cost Copy';
				var hidd_job_id=$("#hidd_job_id").val();
				//alert(job_no);
				var txt_job_no=document.getElementById('txt_job_no').value;
				page_link=page_link + "&txt_job_no="+txt_job_no+ "&hidd_job_id="+hidd_job_id+"&cons_variable="+extra_str;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=100px,center=1,resize=1,scrolling=0','../')
				emailwindow.onclose=function()
				{
				}
			}
		//partial pre cost copy -----------------END-------------------------------------------
		}
		$('#hidd_is_dtls_open').val(2);
	}

	function color_select_popup(buyer_name,texbox_id)
	{
		//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
		//alert(texbox_id)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/pre_cost_entry_controller_v3.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+texbox_id).val(color_name.value);
			}
		}
	}

	function show_hide_content(row, id){
		$('#content_'+row).toggle('slow', function() {
			 //get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/size_color_breakdown_controller' );
		});
	}

	function set_sum_value(des_fil_id,field_id,table_id)
	{
		if(table_id=='tbl_fabric_cost')
		{
			var rowCount = $('#tbl_fabric_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
			//sum_yarn_required()
			calculate_main_total();
		}
		if(table_id=='tbl_yarn_cost')
		{
			var rowCount = $('#tbl_yarn_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)

			calculate_main_total();
		}
		if(table_id=='tbl_conversion_cost')
		{
			var rowCount = $('#tbl_conversion_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			//document.getElementById('txt_fabric_pre_cost').value=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
			calculate_main_total();
		}
		if(table_id=='tbl_trim_cost')
		{
			var rowCount = $('#tbl_trim_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_trim_pre_cost').value=document.getElementById('txttrimamount_sum').value;
			calculate_main_total()
		}
		if(table_id=='tbl_embellishment_cost')
		{
			var rowCount = $('#tbl_embellishment_cost tr').length-1;
			var embAmtTot=0
			for(var i=1; i<=rowCount; i++)
			{
				if( $('#cboembstatus_'+i).val()==1 )
				{
					embAmtTot+=$('#txtembamount_'+i).val()*1;
				}
			}
			$('#txtamountemb_sum').val( number_format_common( embAmtTot, 1, 0,document.getElementById('cbo_currercy').value) );
			
			//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			//math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_embel_pre_cost').value=document.getElementById('txtamountemb_sum').value;
			calculate_main_total();
		}
		if(table_id=='tbl_wash_cost')
		{
			var rowCount = $('#tbl_wash_cost tr').length-1;
			//alert(rowCount)
			var washAmtTot=0
			for(var i=1; i<=rowCount; i++)
			{
				if( $('#cboembstatus_'+i).val()==1 )
				{
					washAmtTot+=$('#txtembamount_'+i).val()*1;
				}
			}
			$('#txtamountemb_sum').val( number_format_common( washAmtTot, 1, 0,document.getElementById('cbo_currercy').value) );
			//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			//math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_wash_pre_cost').value=document.getElementById('txtamountemb_sum').value;
			calculate_main_total();
		}
		if(table_id=='tbl_commission_cost')
		{
			var rowCount = $('#tbl_commission_cost tr').length-1;
			if(des_fil_id=='txtamountcommission_sum')
			{
				var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			}
			if(des_fil_id=='txtratecommission_sum')
			{
				var ddd={ dec_type:3, comma:0}
			}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_commission_pre_cost').value=document.getElementById('txtamountcommission_sum').value;
			calculate_main_total();
		}
		if(table_id=='tbl_comarcial_cost')
		{
			var rowCount = $('#tbl_comarcial_cost tr').length-1;
			if(des_fil_id=='txtamountcomarcial_sum')
			{
				var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			}
			if(des_fil_id=='txtratecomarcial_sum')
			{
				var ddd={ dec_type:3, comma:0}
			}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_comml_pre_cost').value=document.getElementById('txtamountcomarcial_sum').value;
			calculate_main_total()
		}
	}

	function enable_disable(value,fld_arry,i)
	{
		var fld_arry=fld_arry.split('*');
		if(value==2)
		{
			var rate_amount = return_ajax_request_value(document.getElementById('updateid_'+i).value, 'rate_amount', 'requires/pre_cost_entry_controller_v3')
			rate_amount_arr=rate_amount.split('_');
			for(var j=0;j<fld_arry.length;j++)
			{
				document.getElementById(fld_arry[j]+i).disabled=false;
				if(rate_amount_arr[j] != undefined){
					document.getElementById(fld_arry[j]+i).value=rate_amount_arr[j];
				}
			}
		}
		else
		{
			for(var j=0;j<fld_arry.length;j++)
			{
				document.getElementById(fld_arry[j]+i).disabled=true;
				document.getElementById(fld_arry[j]+i).value='';
			}
		}
	}
//Common for All end ----------------------------
//Fabric Cost-------------------------------------
	function add_break_down_tr(i,tr)
	{
		var row_num=$('#tbl_fabric_cost tr').length-1;
		if (i==0)
		{
			i=1;
			$("#txtconstruction_"+i).autocomplete({
				source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
				source:  str_composition
			});
			return;
		}
		/*if (row_num!=i)
		{
			return false;
		}
		else
		{*/
			var j=i;
			
			var index = $(tr).closest("tr").index();
			//alert(index)
			var i=row_num;
			i++;

			/*$("#tbl_fabric_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_fabric_cost");*/
			
			
			var tr=$("#tbl_fabric_cost tbody tr:eq("+index+")");
			//alert(tr)
			var cl=$("#tbl_fabric_cost tbody tr:eq("+index+")").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
					});
				}).end();
			tr.after(cl);
			
			
			$("#tbl_fabric_cost tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
			if($('#seq_'+i).val()!= "")
			{
				$('#seq_'+i).val( i );
			}
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost',this);");
			$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
			$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");

			$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
			$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
			$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
			$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v3.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
			$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
			$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#totalqty_'+i).removeAttr("onClick").attr("onClick","loadTotal( "+i+",'fabric' )");
			var j=i-1;
			$('#cbogmtsitem_'+i).val($('#cbogmtsitem_'+j).val());
			$('#txtbodyparttype_'+i).val($('#txtbodyparttype_'+j).val());
			$('#cbofabricnature_'+i).val($('#cbofabricnature_'+j).val());
			$('#cbocolortype_'+i).val($('#cbocolortype_'+j).val());
			$('#cbofabricsource_'+i).val($('#cbofabricsource_'+j).val());
			$('#cbosourceid_'+i).val($('#cbosourceid_'+j).val());
			$('#cbostatus_'+i).val(1);
			$('#uom_'+i).val($('#uom_'+j).val());
			$('#txtconsumption_'+i).val("");
			$('#txtrate_'+i).val("");
			$('#txtamount_'+i).val("");
			$('#consbreckdown_'+i).val("");
			$('#msmntbreackdown_'+i).val("");
			$('#colorbreackdown_'+i).val("");
			$('#updateid_'+i).val("");
			$('#processlossmethod_'+i).val("");
			$('#txtfinishconsumption_'+i).val("");
			$('#txtavgprocessloss_'+i).val("");
			//$('#txtbodypart_'+i).val("");
			$('#txtgsmweight_'+i).val("");
			$('#markerbreackdown_'+i).val("");
			$('#cbowidthdiatype_'+i).val("");
			$('#prifabcostdtlsid_'+i).val("");
			$('#precostapproved_'+i).val(0);

			$('#cbogmtsitem_'+i).removeAttr('disabled');
			$('#txtbodypart_'+i).removeAttr('disabled');
			$('#txtbodyparttext_'+i).removeAttr('disabled');
			$('#cbofabricnature_'+i).removeAttr('disabled');
			$('#cbocolortype_'+i).removeAttr('disabled');
			$('#fabricdescription_'+i).removeAttr('disabled');
			$('#cbofabricsource_'+i).removeAttr('disabled');
			$('#cbowidthdiatype_'+i).removeAttr('disabled');
			$('#txtgsmweight_'+i).removeAttr('disabled');
			$('#cbocolorsizesensitive_'+i).removeAttr('disabled');
			$('#txtcolor_'+i).removeAttr('disabled');
			$('#uom_'+i).removeAttr('disabled');
			$('#cbonominasupplier_'+i).removeAttr('disabled');
			$('#consumptionbasis_'+i).removeAttr('disabled');
			$('#txtconsumption_'+i).removeAttr('disabled');
			$('#txtrate_'+i).removeAttr('disabled');
			$('#txtamount_'+i).removeAttr('disabled');
			$('#cbostatus_'+i).removeAttr('disabled');
			$('#decrease_'+i).removeAttr('disabled');

			control_color_field(i);
			set_all_onclick();
			sum_yarn_required();
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			$("#libyarncountdeterminationid_"+i).autocomplete({
				source: str_construction
			});
			$("#fabricdescription_"+i).autocomplete({
				source:  str_composition
			});
		//}
	}

	function open_body_part_popup(i){
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v3.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=460px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var id=this.contentDoc.getElementById("gid");
			var name=this.contentDoc.getElementById("gname");
			var type=this.contentDoc.getElementById("gtype");
			document.getElementById('txtbodyparttext_'+i).value=name.value;
			document.getElementById('txtbodypart_'+i).value=id.value;
			document.getElementById('txtbodyparttype_'+i).value=type.value;
			//sum_yarn_required()
		}
	}

	function open_fabric_decription_popup(i)
	{
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v3.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=960px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
			document.getElementById('fabricdescription_'+i).value=fab_desctiption.value;
			document.getElementById('fabricdescription_'+i).title=fab_desctiption.value;
			document.getElementById('cbofabricnature_'+i).value=fab_nature_id.value;
			document.getElementById('txtgsmweight_'+i).value=fab_gsm.value;
			document.getElementById('yarnbreackdown_'+i).value=yarn_desctiption.value;
			document.getElementById('construction_'+i).value=construction.value;
			document.getElementById('composition_'+i).value=composition.value;
			//sum_yarn_required()
		}
	}

	function set_session_large_post_data(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
	{
		var cons_breck_downn=document.getElementById('consbreckdown_'+trorder).value;
		var msmnt_breack_downn=document.getElementById('msmntbreackdown_'+trorder).value;
		var marker_breack_down=document.getElementById('markerbreackdown_'+trorder).value;
		//set_session_large_post_data( '&cons_breck_downn='+cons_breck_downn+'&msmnt_breack_downn='+msmnt_breack_downn+'&marker_breack_down='+marker_breack_down,"../../", "save_post_session" ) ;
		var data="action=save_post_session&cons_breck_downn="+cons_breck_downn+'&msmnt_breack_downn='+msmnt_breack_downn+'&marker_breack_down='+marker_breack_down;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4)
			{
				if(http.responseText==1)
				{
					open_consumption_popup(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
				}
			}
		}
	}

	function open_consumption_popup(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var cbo_approved_status=document.getElementById('cbo_approved_status').value;
		var hid_fab_cons_in_quotation_variable =document.getElementById('consumptionbasis_'+trorder).value;
		var body_part_id =document.getElementById(body_part_id).value;
		var txtgsmweight=document.getElementById(txtgsmweight_id).value;
		var cbofabricnature_id =document.getElementById(cbofabricnature_id).value;
		var cons_breck_downn=document.getElementById('consbreckdown_'+trorder).value;
		var msmnt_breack_downn=document.getElementById('msmntbreackdown_'+trorder).value;
		var marker_breack_down=document.getElementById('markerbreackdown_'+trorder).value;
		var calculated_conss=document.getElementById('txtconsumption_'+trorder).value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var cbogmtsitem = document.getElementById('cbogmtsitem_'+trorder).value;
		var garments_nature = document.getElementById('garments_nature').value;
		var pri_fab_cost_dtls_id=document.getElementById('prifabcostdtlsid_'+trorder).value;
		var pre_cost_fabric_cost_dtls_id=document.getElementById('updateid_'+trorder).value;
		var precostapproved=document.getElementById('precostapproved_'+trorder).value;
		var cbofabricsource=document.getElementById('cbofabricsource_'+trorder).value;
		var uom=document.getElementById('uom_'+trorder).value;
		var consumptionbasis=document.getElementById('consumptionbasis_'+trorder).value;
		var budgeton=$('#budgeton_'+trorder).val();

		document.getElementById('tr_ortder').value=trorder;
		if(cbogmtsitem==0 )
		{
			alert("Select Gmts Item");
			return;
		}
		if(body_part_id==0 )
		{
			alert("Select Body Part");
			return;
		}
		if(cbofabricnature_id==0 )
		{
			alert("Select Fabric Nature");
			return;
		}
		if(hid_fab_cons_in_quotation_variable=='' || hid_fab_cons_in_quotation_variable<=0 )
		{
			alert("You have to set Variable for this Company");
			return;
		}

		if(cbofabricnature_id==2 && (txtgsmweight==0 || txtgsmweight=='') && consumptionbasis==2)
		{
			alert("Fill up Gsm");
			document.getElementById(txtgsmweight_id).focus();
			return;
		}

		if(cbofabricnature_id==2 && (txtgsmweight==0 || txtgsmweight==''))
		{
			if(body_part_id !=2){
				if(body_part_id !=3){
					alert("Fill up Gsm");
					document.getElementById(txtgsmweight_id).focus();
					return;
				}
			}
		}
		//else
		//{
			var page_link=page_link+'&body_part_id='+body_part_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_company_id='+cbo_company_id+'&cbofabricnature_id='+cbofabricnature_id+'&calculated_conss='+calculated_conss+'&hid_fab_cons_in_quotation_variable='+hid_fab_cons_in_quotation_variable+'&txtgsmweight='+txtgsmweight+'&txt_job_no='+txt_job_no+'&cbogmtsitem='+cbogmtsitem+'&garments_nature='+garments_nature+'&cbo_approved_status='+cbo_approved_status+'&pri_fab_cost_dtls_id='+pri_fab_cost_dtls_id+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved+'&cbofabricsource='+cbofabricsource+'&uom='+uom+'&budgeton='+budgeton;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1260px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var trorder= document.getElementById('tr_ortder').value;
				var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
				var msmnt_breack_down=this.contentDoc.getElementById("msmnt_breack_down");
				var marker_breack_down=this.contentDoc.getElementById("marker_breack_down");
				var calculated_cons=this.contentDoc.getElementById("calculated_cons");
				var finish_avg_cons=this.contentDoc.getElementById("avg_cons");
				var avg_process_loss=this.contentDoc.getElementById("calculated_procloss");
				var process_loss_method_id=this.contentDoc.getElementById("process_loss_method_id");
				//var tot_plancut_qty=this.contentDoc.getElementById("job_plancut_qty");
				var tot_plancut_qty=this.contentDoc.getElementById("tot_plancut_qty");
				var calculated_plancutqty=this.contentDoc.getElementById("calculated_plancutqty");

				var calculated_rate=this.contentDoc.getElementById("calculated_rate");
				var calculated_amount=this.contentDoc.getElementById("calculated_amount");

				$('#txtconsumption_'+trorder).val(calculated_cons.value);
				$('#txtfinishconsumption_'+trorder).val(finish_avg_cons.value);
				$('#txtavgprocessloss_'+trorder).val(avg_process_loss.value);
				$('#processlossmethod_'+trorder).val(process_loss_method_id.value);
				$('#consbreckdown_'+trorder).val(cons_breck_down.value);
				$('#msmntbreackdown_'+trorder).val(msmnt_breack_down.value);
				$('#markerbreackdown_'+trorder).val(marker_breack_down.value);
				$('#isclickedconsinput_'+trorder).val(1);
				$('#plancutqty_'+trorder).val(calculated_plancutqty.value);
				$('#jobplancutqty_'+trorder).val(tot_plancut_qty.value);
				$('#isconspopupupdate_'+trorder).val(1);
				$('#txtrate_'+trorder).val(calculated_rate.value);
				$('#txtamount_'+trorder).val(calculated_amount.value);

				math_operation( 'txtamount_'+trorder, 'txtconsumption_'+trorder+'*'+'txtrate_'+trorder, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );

				set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
				sum_yarn_required()
				var row_num=$('#tbl_fabric_cost tr').length-1;
				if(trorder==row_num)
				{
					document.getElementById('check_input').value=0;
					document.getElementById('is_click_cons_box').value=0;
				}
			}
		//}
	}

	function change_caption( value, td_id )
	{
		if(value==2) document.getElementById(td_id).innerHTML="GSM"; else document.getElementById(td_id).innerHTML="Yarn Weight";
	}

	function control_color_field(i)
	{
		var cbocolorsizesensitive = document.getElementById('cbocolorsizesensitive_'+i).value;

		if(cbocolorsizesensitive==3)
		{
			$('#txtcolor_'+i).removeAttr('disabled').attr("onClick","open_color_popup("+i+");");
			$('#txtcolor_'+i).attr('readonly','readonly');
		}
		else
		{
			$('#txtcolor_'+i).removeAttr('onClick');
			$('#txtcolor_'+i).attr('disabled','disabled');
		}
	}

	function open_color_popup(i)
	{
	    var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var cbogmtsitem = document.getElementById('cbogmtsitem_'+i).value;
		var color_breck_down=document.getElementById('colorbreackdown_'+i).value;
		var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
		var pre_cost_fabric_cost_dtls_id=document.getElementById('updateid_'+i).value;
	    var precostapproved=document.getElementById('precostapproved_'+i).value;
		if(cbocolorsizesensitive==3)
		{
			var page_link="requires/pre_cost_entry_controller_v3.php?txt_job_no="+trim(txt_job_no)+"&action=open_color_list_view&color_breck_down="+color_breck_down+"&cbogmtsitem="+cbogmtsitem+"&cbo_company_id="+cbo_company_id+"&cbo_buyer_name="+cbo_buyer_name+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=400px,height=480px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var color_breck_down=this.contentDoc.getElementById("color_breck_down") //Access form field with id="emailfield"
				document.getElementById('colorbreackdown_'+i).value=color_breck_down.value;
			}
		}
	}

	function fnc_fabric_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}

		if(operation==1)
		{
			var txt_job_no=document.getElementById('txt_job_no').value;
			get_php_form_data(txt_job_no, 'check_data_mismass', "requires/pre_cost_entry_controller_v3" );
			var check_input=document.getElementById('check_input').value*1;
			var is_click_cons_box=document.getElementById('is_click_cons_box').value*1;
			if(is_click_cons_box==1 && check_input==1)
			{
				alert("Change found in color size Brackdown,Please Click in Avg. Grey Cons Input Box and just close the popup and click update button")
				release_freezing();
				return;
			}
		}

		/*if(operation==1)
		{
			var po_id="";
			//alert(po_id)
			var txt_job_no=document.getElementById('txt_job_no').value;
			var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/pre_cost_entry_controller_v3')
			//alert(booking_no_with_approvet_status)
			var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
			if(trim(booking_no_with_approvet_status_arr[0]) !="")
			{
				var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
				if(trim(booking_no_with_approvet_status_arr[1]) !="")
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
				var al_magg="Un-Approved Main Fabric Booking No ("+booking_no_with_approvet_status_arr[1]+") Found \n If you update this Pre-Cost \n You have click on 'Apply Last Update Button' in main Fabric booking page";
				//alert(al_magg)
				var r=confirm(al_magg);
				if(r==false)
				{
					release_freezing();
					return;
				}
				else
				{
					//continue;
				}
			}
		}*/
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(2);
			//alert(qc_validate);
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;

				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				//alert(pri_fabric_pre_cost+"==="+txt_fabric_pre_cost);
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation');
					release_freezing();
					return;
				}
			}
		}

	    var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
	    var row_num=$('#tbl_fabric_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			var txtconsumption=document.getElementById('txtconsumption_'+i).value;
			if(txtconsumption*1<=0)
			{
				 document.getElementById('txtconsumption_'+i).focus();
				 document.getElementById('txtconsumption_'+i).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$(this).html('Please Fill up Consumption field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				 });
				 release_freezing();
				 return;
			}
			if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*txtbodyparttype_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*cbowidthdiatype_'+i+'*txtgsmweight_'+i+'*uom_'+i,'Gmts Item *Body Part*Body Part Type*Fabric Nature*Color Type*Construction*Composition*Consunption*Fabric Source*Width /Dia Type*GSM*UOM')==false)
			{
				release_freezing();
				return;
			}

			if ( $('#cbofabricnature_'+i).val()=='3' && $('#cbofabricsource_'+i).val()=='1' &&  (form_validation('txtgsmweight_'+i,'Yarn Weight')==false || $('#txtgsmweight_'+i).val()=='0') )
			{
				document.getElementById('txtgsmweight_'+i).focus();
				document.getElementById('txtgsmweight_'+i).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{
					$(this).html('Please Fill up Yarn Weight field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
				release_freezing();
				return;
			}

			if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtrate_'+i,'Rate')==false || $('#txtrate_'+i).val()=='0') )
			{
				document.getElementById('txtrate_'+i).focus();
				document.getElementById('txtrate_'+i).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{
					$(this).html('Please Fill up Rate field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
				release_freezing();
				return;
			}

			if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtamount_'+i,'Amount')==false || $('#txtamount_'+i).val()=='0') )
			{
				 document.getElementById('txtamount_'+i).focus();
				 document.getElementById('txtamount_'+i).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$(this).html('Please Fill up Amount field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				 });
				 release_freezing();
				 return;
			}
			if ($('#cbocolorsizesensitive_'+i).val()=='0' && $('#txtcolor_'+i).val()=='')
			{
				 document.getElementById('txtcolor_'+i).focus();
				 document.getElementById('txtcolor_'+i).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$(this).html('Please Fill up Color field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				 });
				 release_freezing();
				 return;
			}
			if($('#consbreckdown_'+i).val()=='')
			{
				$('#txtconsumption_'+i).click();
				release_freezing();
				return;
			}
			//alert($('#colorbreackdown_'+i).val());
			//alert($('#cbocolorsizesensitive_'+i).val());
			if($('#colorbreackdown_'+i).val()=='' && $('#cbocolorsizesensitive_'+i).val()==3)
			{
				alert("Please set Contrast Color");
				$('#txtcolor_'+i).click();
				release_freezing();
				return;
			}

			if($('#isclickedconsinput_'+i).val()==2)
			{
				//alert("Please Click in Avg. Grey Cons Input Box and just close the popup and click update button")
				document.getElementById('txtconsumption_'+i).focus();
				document.getElementById('txtconsumption_'+i).style.backgroundImage=bgcolor;
				alert(" Change found in color size Brackdown,Please Click in Avg. Grey Cons Input Box and just close the popup and click update button")
				release_freezing();
				return;
			}
			data_all+="&consumptionbasis_" + z + "='" + $('#consumptionbasis_'+i).val()+"'"+"&cbogmtsitem_" + z + "='" + $('#cbogmtsitem_'+i).val()+"'"+"&txtbodypart_" + z + "='" + $('#txtbodypart_'+i).val()+"'"+"&cbofabricnature_" + z + "='" + $('#cbofabricnature_'+i).val()+"'"+"&cbocolortype_" + z + "='" + $('#cbocolortype_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'"+"&construction_" + z + "='" + $('#construction_'+i).val()+"'"+"&composition_" + z + "='" + $('#composition_'+i).val()+"'"+"&fabricdescription_" + z + "='" + $('#fabricdescription_'+i).val()+"'"+"&txtgsmweight_" + z + "='" + $('#txtgsmweight_'+i).val()+"'"+"&cbocolorsizesensitive_" + z + "='" + $('#cbocolorsizesensitive_'+i).val()+"'"+"&txtcolor_" + z + "='" + $('#txtcolor_'+i).val()+"'"+"&txtconsumption_" + z + "='" + $('#txtconsumption_'+i).val()+"'"+"&cbofabricsource_" + z + "='" + $('#cbofabricsource_'+i).val()+"'"+"&cbonominasupplier_" + z + "='" + $('#cbonominasupplier_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtfinishconsumption_" + z + "='" + $('#txtfinishconsumption_'+i).val()+"'"+"&txtavgprocessloss_" + z + "='" + $('#txtavgprocessloss_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&msmntbreackdown_" + z + "='" + $('#msmntbreackdown_'+i).val()+"'"+"&updateid_" + z + "='" + $('#updateid_'+i).val()+"'"+"&processlossmethod_" + z + "='" + $('#processlossmethod_'+i).val()+"'"+"&colorbreackdown_" + z + "='" + $('#colorbreackdown_'+i).val()+"'"+"&yarnbreackdown_" + z + "='" + $('#yarnbreackdown_'+i).val()+"'"+"&markerbreackdown_" + z + "='" + $('#markerbreackdown_'+i).val()+"'"+"&cbowidthdiatype_" + z + "='" + $('#cbowidthdiatype_'+i).val()+"'"+"&avgtxtconsumption_" + z + "='" + $('#avgtxtconsumption_'+i).val()+"'"+"&avgtxtgsmweight_" + z + "='" + $('#avgtxtgsmweight_'+i).val()+"'"+"&plancutqty_" + z + "='" + $('#plancutqty_'+i).val()+"'"+"&jobplancutqty_" + z + "='" + $('#jobplancutqty_'+i).val()+"'"+"&isclickedconsinput_" + z + "='" + $('#isclickedconsinput_'+i).val()+"'"+"&oldlibyarncountdeterminationid_" + z + "='" + $('#oldlibyarncountdeterminationid_'+i).val()+"'"+"&isconspopupupdate_" + z + "='" + $('#isconspopupupdate_'+i).val()+"'"+"&uom_" + z + "='" + $('#uom_'+i).val()+"'"+"&txtbodyparttype_" + z + "='" + $('#txtbodyparttype_'+i).val()+"'"+"&cbosourceid_" + z + "='" + $('#cbosourceid_'+i).val()+"'"+"&budgeton_" + z + "='" + $('#budgeton_'+i).val()+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_fabric_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_costing_per*txt_cost_control_source*update_id*hidd_job_id*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtwoven_fin_sum*txtknit_fin_sum*txtamount_sum*avg*txtwoven_sum_production*txtknit_sum_production*txtwoven_fin_sum_production*txtknit_fin_sum_production*txtwoven_sum_purchase*txtknit_sum_purchase*txtwoven_fin_sum_purchase*txtknit_fin_sum_purchase*txtwoven_amount_sum_purchase*txtkint_amount_sum_purchase*txt_quotation_id*copy_quatation_id',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_cost_dtls_reponse;
	}

	function fnc_fabric_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(trim(reponse[0])==6)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			if(trim(reponse[0])==15)
			{
				 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value*1;
				if (trim(reponse[0]).length>2) reponse[0]=10;
				show_msg(trim(reponse[0]));
				if(trim(reponse[0])==10)
				{
					release_freezing();
					return;
				}

				if(trim(reponse[0])==0 || trim(reponse[0])==1)
				{
					var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
					var pre_fabcost=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_fabric_pre_cost").attr('pre_fab_cost',pre_fabcost);

					calculate_main_total();
				}

				show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
				show_hide_content('fabric_cost', '')

				if(reponse[0]==1)
				{
					var txt_job_no=document.getElementById('txt_job_no').value;
					get_php_form_data(txt_job_no, 'check_data_mismass', "requires/pre_cost_entry_controller_v3" );
					document.getElementById('is_click_cons_box').value=1;

				}
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					 var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					 var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					 document.getElementById('txt_comml_pre_cost').value=reponse[3];
					 document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
					 set_currier_cost_method_variable(company_name);
					 calculate_main_total();
					fnc_quotation_entry_dtls1(1);
					
					release_freezing();
				}
			}
		}
	}

	function open_stripe_color_popup()
	{
		var row_num=$('#tbl_fabric_cost tr').length-1;
		for (var i=1; i<=row_num; i++)
		{
			if($('#updateid_'+i).val()=='')
			{
			  alert("Save or Update Fabric Cost")
			  release_freezing();
			  return;
			}
		}

		var txt_job_no=document.getElementById('txt_job_no').value;
		var hidd_job_id=document.getElementById('hidd_job_id').value;
		var index_page=$('#index_page', window.parent.document).val();
		var page_link="stripe_color_measurement_urmi.php?permission="+permission+'&txt_job_no='+txt_job_no+'&hidd_job_id='+hidd_job_id+'&index_page='+index_page;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=1200px,height=500px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}

	function open_fabric_color_image_popup() //Fabric Color Add Image
	{
	    var row_num=$('#tbl_fabric_cost tr').length-1;
		for (var i=1; i<=row_num; i++)
		{
			if($('#updateid_'+i).val()=='')
			{
			  alert("Save or Update Fabric Cost")
			  return;
			}
		}

		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value
		var index_page=$('#index_page', window.parent.document).val();
		var page_link='requires/pre_cost_entry_controller_v3.php?action=fabric_add_image_popup&txt_job_no='+txt_job_no+'&cbo_company_name='+cbo_company_name;
		//var page_link="requires/pre_cost_entry_controller_v3.php?permission="+permission+'&txt_job_no='+txt_job_no+'&index_page='+index_page;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Add Image For Fabric Color", 'width=800px,height=500px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}

	function sum_yarn_required()
	{
		var row_num=$('#tbl_fabric_cost tr').length-1;

		var yarn= new Array();
		var same= new Array();

		var knit_fab= new Array();
		var same_knit= new Array();

		var woven_fab= new Array();
		var same_woven= new Array();

		var knit_fin_fab= new Array();
		var same_knit_fin= new Array();

		var woven_fin_fab= new Array();
		var same_woven_fin= new Array();


		var knit_fab_prod= new Array();
		var same_knit_prod= new Array();

		var woven_fab_prod= new Array();
		var same_woven_prod= new Array();

		var knit_fin_fab_prod= new Array();
		var same_knit_fin_prod= new Array();

		var woven_fin_fab_prod= new Array();
		var same_woven_fin_prod= new Array();

		var knit_fab_purc= new Array();
		var same_knit_purc= new Array();

		var woven_fab_purc= new Array();
		var same_woven_purc= new Array();

		var knit_fin_fab_purc= new Array();
		var same_knit_fin_purc= new Array();

		var woven_fin_fab_purc= new Array();
		var same_woven_fin_purc= new Array();

		var knit_fab_purc_amt= new Array();
		var same_knit_purc_amt= new Array();

		var woven_fab_purc_amt= new Array();
		var same_woven_purc_amt= new Array();


		for (var i=1; i<=row_num; i++)
		{
			//alert('mmmm')
			var cbofabricsource=document.getElementById('cbofabricsource_'+i).value;
			var cbogmtsitem=document.getElementById('cbogmtsitem_'+i).value;
			var txtbodypart=document.getElementById('txtbodypart_'+i).value;
			var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
			var arrindex=cbogmtsitem+'_'+txtbodypart+'_'+cbofabricnature;
			if(cbofabricnature==2 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same[arrindex]=1;
				}
			}
			if(cbofabricnature==3 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=1;
				}
			}

			if(cbofabricnature==2)
			{
				if(array_key_exists(arrindex, knit_fab))
				{
					knit_fab[arrindex]=knit_fab[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit[arrindex]=same_knit[arrindex]+1;
					knit_fin_fab[arrindex]=knit_fin_fab[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin[arrindex]=same_knit_fin[arrindex]+1
				}
				else
				{
					knit_fab[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit[arrindex]=1;

					knit_fin_fab[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin[arrindex]=1;
				}
			}

			if(cbofabricnature==3)
			{
				if(array_key_exists(arrindex, woven_fab))
				{
					woven_fab[arrindex]=woven_fab[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven[arrindex]=same_woven[arrindex]+1;

					woven_fin_fab[arrindex]=woven_fin_fab[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin[arrindex]=same_woven_fin[arrindex]+1
				}
				else
				{
					woven_fab[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven[arrindex]=1;

					woven_fin_fab[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin[arrindex]=1;
				}
			}
			if(cbofabricnature==2 && cbofabricsource==1)
			{
				//rows[i]=arrindex
				//rows_val[i]=(document.getElementById('txtconsumption_'+i).value)*1;
				if(array_key_exists(arrindex, knit_fab_prod))
				{
					knit_fab_prod[arrindex]=knit_fab_prod[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_prod[arrindex]=same_knit_prod[arrindex]+1

					knit_fin_fab_prod[arrindex]=knit_fin_fab_prod[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_prod[arrindex]=same_knit_fin_prod[arrindex]+1
				}
				else
				{
					knit_fab_prod[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_prod[arrindex]=1;

					knit_fin_fab_prod[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_prod[arrindex]=1;
				}
			}

			if(cbofabricnature==3 && cbofabricsource==1)
			{
				//rows[i]=arrindex
				//rows_val[i]=(document.getElementById('txtgsmweight_'+i).value)*1;
				if(array_key_exists(arrindex, woven_fab_prod))
				{
					woven_fab_prod[arrindex]=woven_fab_prod[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_prod[arrindex]=same_woven_prod[arrindex]+1


					woven_fin_fab_prod[arrindex]=woven_fin_fab_prod[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_prod[arrindex]=same_woven_fin_prod[arrindex]+1
				}
				else
				{
					woven_fab_prod[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_prod[arrindex]=1;

					woven_fin_fab_prod[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_prod[arrindex]=1;
				}
			}

			if(cbofabricnature==2 && cbofabricsource==2)
			{
				//rows[i]=arrindex
				//rows_val[i]=(document.getElementById('txtconsumption_'+i).value)*1;
				if(array_key_exists(arrindex, knit_fab_purc))
				{
					knit_fab_purc[arrindex]=knit_fab_purc[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_purc[arrindex]=same_knit_purc[arrindex]+1

					knit_fin_fab_purc[arrindex]=knit_fin_fab_purc[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_purc[arrindex]=same_knit_fin_purc[arrindex]+1


					knit_fab_purc_amt[arrindex]=knit_fab_purc_amt[arrindex]+(document.getElementById('txtamount_'+i).value)*1;
					same_knit_purc_amt[arrindex]=same_knit_purc_amt[arrindex]+1
				}
				else
				{
					knit_fab_purc[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_purc[arrindex]=1;

					knit_fin_fab_purc[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_purc[arrindex]=1;

					knit_fab_purc_amt[arrindex]=(document.getElementById('txtamount_'+i).value)*1;
					same_knit_purc_amt[arrindex]=1;
				}
			}

			if(cbofabricnature==3 && cbofabricsource==2)
			{
				//rows[i]=arrindex
				//rows_val[i]=(document.getElementById('txtgsmweight_'+i).value)*1;
				if(array_key_exists(arrindex, woven_fab_purc))
				{
					woven_fab_purc[arrindex]=woven_fab_purc[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_purc[arrindex]=same_woven_purc[arrindex]+1


					woven_fin_fab_purc[arrindex]=woven_fin_fab_purc[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_purc[arrindex]=same_woven_fin_purc[arrindex]+1


					woven_fab_purc_amt[arrindex]=woven_fab_purc_amt[arrindex]+(document.getElementById('txtamount_'+i).value)*1;
					same_woven_purc_amt[arrindex]=same_woven_purc_amt[arrindex]+1
				}
				else
				{
					woven_fab_purc[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_purc[arrindex]=1;

					woven_fin_fab_purc[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_purc[arrindex]=1;

					woven_fab_purc_amt[arrindex]=(document.getElementById('txtamount_'+i).value)*1;
					same_woven_purc_amt[arrindex]=1;
				}
			}
		}

		document.getElementById('tot_yarn_needed').value=number_format_common(array_sum (yarn), 5, 0);
		document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(array_sum (yarn), 5, 0);

		document.getElementById('txtwoven_sum').value=number_format_common(array_sum (woven_fab), 5, 0);
		document.getElementById('txtknit_sum').value=number_format_common(array_sum (knit_fab), 5, 0);

		document.getElementById('txtwoven_fin_sum').value=number_format_common(array_sum (woven_fin_fab), 5, 0);
		document.getElementById('txtknit_fin_sum').value=number_format_common(array_sum (knit_fin_fab), 5, 0);

		document.getElementById('txtwoven_sum_production').value=number_format_common(array_sum (woven_fab_prod), 5, 0);
		document.getElementById('txtknit_sum_production').value=number_format_common(array_sum (knit_fab_prod), 5, 0);

		document.getElementById('txtwoven_fin_sum_production').value=number_format_common(array_sum (woven_fin_fab_prod), 5, 0);
		document.getElementById('txtknit_fin_sum_production').value=number_format_common(array_sum (knit_fin_fab_prod), 5, 0);


		document.getElementById('txtwoven_sum_purchase').value=number_format_common(array_sum (woven_fab_purc), 5, 0);
		document.getElementById('txtknit_sum_purchase').value=number_format_common(array_sum (knit_fab_purc), 5, 0);

		document.getElementById('txtwoven_fin_sum_purchase').value=number_format_common(array_sum (woven_fin_fab_purc), 5, 0);
		document.getElementById('txtknit_fin_sum_purchase').value=number_format_common(array_sum (knit_fin_fab_purc), 5, 0);

		document.getElementById('txtwoven_amount_sum_purchase').value=number_format_common(array_sum (woven_fab_purc_amt), 5, 0);
		document.getElementById('txtkint_amount_sum_purchase').value=number_format_common(array_sum (knit_fab_purc_amt), 5, 0);
		//document.getElementById('txtamount_sum').value=number_format_common(array_sum (woven_fab_purc_amt)+array_sum (knit_fab_purc_amt), 5, 0);
		//sum_yarn_required_avg(document.getElementById('avg').value);
		//sum_yarn_required_avg("Make AVG");
	}

	function sum_yarn_required_avg(value)
	{
		return;
		var row_num=$('#tbl_fabric_cost tr').length-1;
		var yarn=0;
		var avg_yarn=0;

		var knit_fab=0;
		var avg_knit_fab=0;
		var knit_fin_fab=0;
		var avg_knit_fin_fab=0;

		var knit_fab_prod=0;
		var avg_knit_fab_prod=0;
		var knit_fin_fab_prod=0;
		var avg_knit_fin_fab_prod=0;

		var knit_fab_pur=0;
		var avg_knit_fab_pur=0;
		var knit_fin_fab_pur=0;
		var avg_knit_fin_fab_pur=0;

		var woven_fab=0;
		var avg_woven_fab=0;
		var woven_fin_fab=0;
		var avg_woven_fin_fab=0;

		var woven_fab_prod=0;
		var avg_woven_fab_prod=0;
		var woven_fin_fab_prod=0;
		var avg_woven_fin_fab_prod=0;

		var woven_fab_pur=0;
		var avg_woven_fab_pur=0;
		var woven_fin_fab_pur=0;
		var avg_woven_fin_fab_pur=0;

		var knit_fab_pur_amt=0;
		var avg_knit_fab_pur_amt=0;
		var woven_fab_pur_amt=0;
		var avg_woven_fab_pur_amt=0;

		for (var i=1; i<=row_num; i++)
		{
			var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
			var cbofabricsource=document.getElementById('cbofabricsource_'+i).value;
			var txtamount=document.getElementById('txtamount_'+i).value*1
			var totplancutqty=document.getElementById('jobplancutqty_'+i).value*1;

			var plancutqty=document.getElementById('plancutqty_'+i).value*1;
			var plancutqty_percent= (plancutqty/totplancutqty)*100;

			var txtconsumption=document.getElementById('txtconsumption_'+i).value*1
			var txtconsumption_percent=number_format_common((txtconsumption*plancutqty_percent)/100,5,0)*1;
			document.getElementById('avgtxtconsumption_'+i).value=txtconsumption_percent;

			$('#txtconsumption_'+i).attr("avglValue", txtconsumption_percent);
			$('#txtconsumption_'+i).attr("title", txtconsumption_percent);

			var txtfinishconsumption=document.getElementById('txtfinishconsumption_'+i).value*1
			var txtfinishconsumption_percent=number_format_common((txtfinishconsumption*plancutqty_percent)/100,5,0)*1;

			var txtamount=document.getElementById('txtamount_'+i).value*1
			var txtamount_percent=number_format_common((txtamount*plancutqty_percent)/100,5,0)*1;

			$('#txtamount_'+i).attr("avglValue", txtamount_percent);
			$('#txtamount_'+i).attr("title", txtamount_percent);

			var txtgsmweight=document.getElementById('txtgsmweight_'+i).value*1
			var txtgsmweight_percent=number_format_common((txtgsmweight*plancutqty_percent)/100,5,1)*1;
			document.getElementById('avgtxtgsmweight_'+i).value=txtgsmweight_percent;
			$('#avgtxtgsmweight_'+i).attr("avglValue", txtgsmweight_percent);
			$('#avgtxtgsmweight_'+i).attr("title", txtgsmweight_percent);

			if(cbofabricnature==2 && cbofabricsource==1)
			{
				yarn+=txtconsumption;
				avg_yarn+=txtconsumption_percent;

				knit_fab_prod+=txtconsumption;
				avg_knit_fab_prod+=txtconsumption_percent;

				knit_fin_fab_prod+=txtfinishconsumption;
				avg_knit_fin_fab_prod+=txtfinishconsumption_percent;
			}
			if(cbofabricnature==3 && cbofabricsource==1)
			{
				yarn+=txtgsmweight;
				avg_yarn+=txtgsmweight_percent;

				woven_fab_prod+=txtconsumption;
				avg_woven_fab_prod+=txtconsumption_percent;

				woven_fin_fab_prod+=txtfinishconsumption;
				avg_woven_fin_fab_prod+=txtfinishconsumption_percent;
			}

			if(cbofabricnature==2 && cbofabricsource==2)
			{
				knit_fab_pur+=txtconsumption;
				avg_knit_fab_pur+=txtconsumption_percent;

				knit_fin_fab_pur+=txtfinishconsumption;
				avg_knit_fin_fab_pur+=txtfinishconsumption_percent;

				knit_fab_pur_amt+=txtamount;
				avg_knit_fab_pur_amt+=txtamount_percent;
			}
			if(cbofabricnature==3 && cbofabricsource==2)
			{
				woven_fab_pur+=txtconsumption;
				avg_woven_fab_pur+=txtconsumption_percent;

				woven_fin_fab_pur+=txtfinishconsumption;
				avg_woven_fin_fab_pur+=txtfinishconsumption_percent;

				woven_fab_pur_amt+=txtamount;
				avg_woven_fab_pur_amt+=txtamount_percent;
			}

			if(cbofabricnature==2)
			{
				knit_fab+=txtconsumption;
				avg_knit_fab+=txtconsumption_percent;

				knit_fin_fab+=txtfinishconsumption;
				avg_knit_fin_fab+=txtfinishconsumption_percent;
			}
			if(cbofabricnature==3)
			{
				woven_fab+=txtconsumption;
				avg_woven_fab+=txtconsumption_percent;

				woven_fin_fab+=txtfinishconsumption;
				avg_woven_fin_fab+=txtfinishconsumption_percent;
			}
		}
		if(value=="Make AVG")
		{
		document.getElementById('avg').value="Make UAVG";
		document.getElementById('tot_yarn_needed').value=number_format_common(avg_yarn, 5, 0);
		document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(avg_yarn, 5, 0);

		document.getElementById('txtknit_sum').value=number_format_common(avg_knit_fab, 5, 0);
		document.getElementById('txtknit_fin_sum').value=number_format_common(avg_knit_fin_fab, 5, 0);

		document.getElementById('txtwoven_sum').value=number_format_common(avg_woven_fab, 5, 0);
		document.getElementById('txtwoven_fin_sum').value=number_format_common(avg_woven_fin_fab, 5, 0);

		document.getElementById('txtknit_sum_production').value=number_format_common(avg_knit_fab_prod, 5, 0);
		document.getElementById('txtknit_fin_sum_production').value=number_format_common(avg_knit_fin_fab_prod, 5, 0);

		document.getElementById('txtwoven_sum_production').value=number_format_common(avg_woven_fab_prod, 5, 0);
		document.getElementById('txtwoven_fin_sum_production').value=number_format_common(avg_woven_fin_fab_prod, 5, 0);

		document.getElementById('txtknit_sum_purchase').value=number_format_common(avg_knit_fab_pur, 5, 0);
		document.getElementById('txtknit_fin_sum_purchase').value=number_format_common(avg_knit_fin_fab_pur, 5, 0);

		document.getElementById('txtwoven_sum_purchase').value=number_format_common(avg_woven_fab_pur, 5, 0);
		document.getElementById('txtwoven_fin_sum_purchase').value=number_format_common(avg_woven_fin_fab_pur, 5, 0);

		document.getElementById('txtkint_amount_sum_purchase').value=number_format_common(avg_knit_fab_pur_amt, 5, 0);
		document.getElementById('txtwoven_amount_sum_purchase').value=number_format_common(avg_woven_fab_pur_amt, 5, 0);

		document.getElementById('txtamount_sum').value=number_format_common(avg_knit_fab_pur_amt+avg_woven_fab_pur_amt, 5, 0);

		var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
		document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
		}

		if(value=="Make UAVG")
		{
		document.getElementById('avg').value="Make AVG";
		document.getElementById('tot_yarn_needed').value=number_format_common(yarn, 5, 0);
		document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(yarn, 5, 0);

		document.getElementById('txtknit_sum').value=number_format_common(knit_fab, 5, 0);
		document.getElementById('txtknit_fin_sum').value=number_format_common(knit_fin_fab, 5, 0);

		document.getElementById('txtwoven_sum').value=number_format_common(woven_fab, 5, 0);
		document.getElementById('txtwoven_fin_sum').value=number_format_common(woven_fin_fab, 5, 0);

		document.getElementById('txtknit_sum_production').value=number_format_common(knit_fab_prod, 5, 0);
		document.getElementById('txtknit_fin_sum_production').value=number_format_common(knit_fin_fab_prod, 5, 0);

		document.getElementById('txtwoven_sum_production').value=number_format_common(woven_fab_prod, 5, 0);
		document.getElementById('txtwoven_fin_sum_production').value=number_format_common(woven_fin_fab_prod, 5, 0);

		document.getElementById('txtknit_sum_purchase').value=number_format_common(knit_fab_pur, 5, 0);
		document.getElementById('txtknit_fin_sum_purchase').value=number_format_common(knit_fin_fab_pur, 5, 0);

		document.getElementById('txtwoven_sum_purchase').value=number_format_common(woven_fab_pur, 5, 0);
		document.getElementById('txtwoven_fin_sum_purchase').value=number_format_common(woven_fin_fab_pur, 5, 0);

		document.getElementById('txtkint_amount_sum_purchase').value=number_format_common(knit_fab_pur_amt, 5, 0);
		document.getElementById('txtwoven_amount_sum_purchase').value=number_format_common(woven_fab_pur_amt, 5, 0);

		document.getElementById('txtamount_sum').value=number_format_common(knit_fab_pur_amt+woven_fab_pur_amt, 5, 0);

		var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
		document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
		}

		make_avg_for_same_row();
		return;
	}

	function make_avg_for_same_row()
	{
		return;
		var row_num=$('#tbl_fabric_cost tr').length-1;
		var rows=new Array();
		var rows_val=new Array();

		var yarn= new Array();
		var same= new Array();

		var knit_fab= new Array();
		var same_knit= new Array();

		var woven_fab= new Array();
		var same_woven= new Array();

		var knit_fin_fab= new Array();
		var same_knit_fin= new Array();

		var woven_fin_fab= new Array();
		var same_woven_fin= new Array();

		var knit_fab_prod= new Array();
		var same_knit_prod= new Array();

		var woven_fab_prod= new Array();
		var same_woven_prod= new Array();

		var knit_fin_fab_prod= new Array();
		var same_knit_fin_prod= new Array();

		var woven_fin_fab_prod= new Array();
		var same_woven_fin_prod= new Array();


		var knit_fab_purc= new Array();
		var same_knit_purc= new Array();

		var woven_fab_purc= new Array();
		var same_woven_purc= new Array();

		var knit_fin_fab_purc= new Array();
		var same_knit_fin_purc= new Array();

		var woven_fin_fab_purc= new Array();
		var same_woven_fin_purc= new Array();


		var knit_fab_purc_amt= new Array();
		var same_knit_purc_amt= new Array();

		var woven_fab_purc_amt= new Array();
		var same_woven_purc_amt= new Array();

		for (var i=1; i<=row_num; i++)
		{
			var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
			var cbofabricsource=document.getElementById('cbofabricsource_'+i).value;
			var cbogmtsitem=document.getElementById('cbogmtsitem_'+i).value;
			var txtbodypart=document.getElementById('txtbodypart_'+i).value;

			var arrindex=cbogmtsitem+'_'+txtbodypart+'_'+cbofabricnature;
			rows[i]=arrindex;

			if(cbofabricnature==2 && cbofabricsource==1)
			{
				//rows[i]=arrindex
				rows_val[i]=(document.getElementById('txtconsumption_'+i).value)*1;
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same[arrindex]=1;
				}
			}

			if(cbofabricnature==3 && cbofabricsource==1)
			{
				//rows[i]=arrindex
				rows_val[i]=(document.getElementById('txtgsmweight_'+i).value)*1;
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=1;
				}
			}

			if(cbofabricnature==2)
			{
				if(array_key_exists(arrindex, knit_fab))
				{
					knit_fab[arrindex]=knit_fab[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit[arrindex]=same_knit[arrindex]+1;

					knit_fin_fab[arrindex]=knit_fin_fab[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin[arrindex]=same_knit_fin[arrindex]+1
				}
				else
				{
					knit_fab[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit[arrindex]=1;

					knit_fin_fab[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin[arrindex]=1;
				}
			}
			if(cbofabricnature==3)
			{
				if(array_key_exists(arrindex, woven_fab))
				{
					woven_fab[arrindex]=woven_fab[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven[arrindex]=same_woven[arrindex]+1;

					woven_fin_fab[arrindex]=woven_fin_fab[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin[arrindex]=same_woven_fin[arrindex]+1
				}
				else
				{
					woven_fab[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven[arrindex]=1;

					woven_fin_fab[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin[arrindex]=1;
				}
			}

			if(cbofabricnature==2 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, knit_fab_prod))
				{
					knit_fab_prod[arrindex]=knit_fab_prod[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_prod[arrindex]=same_knit_prod[arrindex]+1

					knit_fin_fab_prod[arrindex]=knit_fin_fab_prod[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_prod[arrindex]=same_knit_fin_prod[arrindex]+1
				}
				else
				{
					knit_fab_prod[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_prod[arrindex]=1;

					knit_fin_fab_prod[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_prod[arrindex]=1;
				}
			}

			if(cbofabricnature==3 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, woven_fab_prod))
				{
					woven_fab_prod[arrindex]=woven_fab_prod[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_prod[arrindex]=same_woven_prod[arrindex]+1


					woven_fin_fab_prod[arrindex]=woven_fin_fab_prod[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_prod[arrindex]=same_woven_fin_prod[arrindex]+1
				}
				else
				{
					woven_fab_prod[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_prod[arrindex]=1;

					woven_fin_fab_prod[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_prod[arrindex]=1;
				}
			}

			if(cbofabricnature==2 && cbofabricsource==2)
			{
				if(array_key_exists(arrindex, knit_fab_purc))
				{
					knit_fab_purc[arrindex]=knit_fab_purc[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_purc[arrindex]=same_knit_purc[arrindex]+1

					knit_fin_fab_purc[arrindex]=knit_fin_fab_purc[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_purc[arrindex]=same_knit_fin_purc[arrindex]+1


					knit_fab_purc_amt[arrindex]=knit_fab_purc_amt[arrindex]+(document.getElementById('txtamount_'+i).value)*1;
					same_knit_purc_amt[arrindex]=same_knit_purc_amt[arrindex]+1
				}
				else
				{
					knit_fab_purc[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_purc[arrindex]=1;

					knit_fin_fab_purc[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_purc[arrindex]=1;

					knit_fab_purc_amt[arrindex]=(document.getElementById('txtamount_'+i).value)*1;
					same_knit_purc_amt[arrindex]=1;
				}
			}

			if(cbofabricnature==3 && cbofabricsource==2)
			{
				if(array_key_exists(arrindex, woven_fab_purc))
				{
					woven_fab_purc[arrindex]=woven_fab_purc[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_purc[arrindex]=same_woven_purc[arrindex]+1


					woven_fin_fab_purc[arrindex]=woven_fin_fab_purc[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_purc[arrindex]=same_woven_fin_purc[arrindex]+1


					woven_fab_purc_amt[arrindex]=woven_fab_purc_amt[arrindex]+(document.getElementById('txtamount_'+i).value)*1;
					same_woven_purc_amt[arrindex]=same_woven_purc_amt[arrindex]+1
				}
				else
				{
					woven_fab_purc[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_purc[arrindex]=1;

					woven_fin_fab_purc[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_purc[arrindex]=1;

					woven_fab_purc_amt[arrindex]=(document.getElementById('txtamount_'+i).value)*1;
					same_woven_purc_amt[arrindex]=1;
				}
			}
		}//for loop end

		var value="Make AVG";
		if(value=="Make AVG")
		{

			document.getElementById('avg').value="Make UAVG";
			var avg = new Array()
			for(key in yarn)
			{
				avg[key]=yarn[key]/same[key];
			}

			var avg_knit = new Array()
			for(key in knit_fab)
			{
				avg_knit[key]=knit_fab[key]/same_knit[key];
			}
			var avg_woven = new Array()
			for(key in woven_fab)
			{
				avg_woven[key]=woven_fab[key]/same_woven[key];
			}


			var avg_knit_fin = new Array()
			for(key in knit_fin_fab)
			{
				avg_knit_fin[key]=knit_fin_fab[key]/same_knit_fin[key];
			}
			var avg_woven_fin = new Array()
			for(key in woven_fin_fab)
			{
				avg_woven_fin[key]=woven_fin_fab[key]/same_woven_fin[key];
			}

			//=======================
			var avg_knit_prod = new Array()
			for(key in knit_fab_prod)
			{
				avg_knit_prod[key]=knit_fab_prod[key]/same_knit_prod[key];
			}
			var avg_woven_prod = new Array()
			for(key in woven_fab_prod)
			{
				avg_woven_prod[key]=woven_fab_prod[key]/same_woven_prod[key];
			}


			var avg_knit_fin_prod = new Array()
			for(key in knit_fin_fab_prod)
			{
				avg_knit_fin_prod[key]=knit_fin_fab_prod[key]/same_knit_fin_prod[key];
			}
			var avg_woven_fin_prod = new Array()
			for(key in woven_fin_fab_prod)
			{
				avg_woven_fin_prod[key]=woven_fin_fab_prod[key]/same_woven_fin_prod[key];
			}

			var avg_knit_purc = new Array()
			for(key in knit_fab_purc)
			{
				avg_knit_purc[key]=knit_fab_purc[key]/same_knit_purc[key];
			}
			var avg_woven_purc = new Array()
			for(key in woven_fab_purc)
			{
				avg_woven_purc[key]=woven_fab_purc[key]/same_woven_purc[key];
			}

			var avg_knit_fin_purc = new Array()
			for(key in knit_fin_fab_purc)
			{
				avg_knit_fin_purc[key]=knit_fin_fab_purc[key]/same_knit_fin_purc[key];
			}
			var avg_woven_fin_purc = new Array()
			for(key in woven_fin_fab_purc)
			{
				avg_woven_fin_purc[key]=woven_fin_fab_purc[key]/same_woven_fin_purc[key];
			}

			var avg_knit_purc_amt = new Array()
			for(key in knit_fab_purc_amt)
			{
				avg_knit_purc_amt[key]=knit_fab_purc_amt[key]/same_knit_purc_amt[key];
			}
			var avg_woven_purc_amt = new Array()
			for(key in woven_fab_purc_amt)
			{
				avg_woven_purc_amt[key]=woven_fab_purc_amt[key]/same_woven_purc_amt[key];
			}
			//===================

			//alert(Object.keys(same));
			document.getElementById('tot_yarn_needed').value=number_format_common(array_sum (avg), 5, 0);
			document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(array_sum (avg), 5, 0);

			document.getElementById('txtwoven_sum').value=number_format_common(array_sum (avg_woven), 5, 0);
			document.getElementById('txtknit_sum').value=number_format_common(array_sum (avg_knit), 5, 0);

			document.getElementById('txtwoven_fin_sum').value=number_format_common(array_sum (avg_woven_fin), 5, 0);
			document.getElementById('txtknit_fin_sum').value=number_format_common(array_sum (avg_knit_fin), 5, 0);


			document.getElementById('txtwoven_sum_production').value=number_format_common(array_sum (avg_woven_prod), 5, 0);
			document.getElementById('txtknit_sum_production').value=number_format_common(array_sum (avg_knit_prod), 5, 0);

			document.getElementById('txtwoven_fin_sum_production').value=number_format_common(array_sum (avg_woven_fin_prod), 5, 0);
			document.getElementById('txtknit_fin_sum_production').value=number_format_common(array_sum (avg_knit_fin_prod), 5, 0);


			document.getElementById('txtwoven_sum_purchase').value=number_format_common(array_sum (avg_woven_purc), 5, 0);
			document.getElementById('txtknit_sum_purchase').value=number_format_common(array_sum (avg_knit_purc), 5, 0);

			document.getElementById('txtwoven_fin_sum_purchase').value=number_format_common(array_sum (avg_woven_fin_purc), 5, 0);
			document.getElementById('txtknit_fin_sum_purchase').value=number_format_common(array_sum (avg_knit_fin_purc), 5, 0);

			document.getElementById('txtwoven_amount_sum_purchase').value=number_format_common(array_sum (avg_woven_purc_amt), 5, 0);
			document.getElementById('txtkint_amount_sum_purchase').value=number_format_common(array_sum (avg_knit_purc_amt), 5, 0);
			if(array_sum (avg_woven_purc_amt)>0 && array_sum (avg_knit_purc_amt)>0)
			{
			document.getElementById('txtamount_sum').value=number_format_common((array_sum (avg_woven_purc_amt)+array_sum (avg_knit_purc_amt))/2, 5, 0);
			}
			else
			{
			document.getElementById('txtamount_sum').value=number_format_common(array_sum (avg_woven_purc_amt)+array_sum (avg_knit_purc_amt), 5, 0);
			}

			var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)

			for(key in rows)
			{
				var cbofabricnature2=document.getElementById('cbofabricnature_'+key).value;
				var key_avg=(yarn[rows[key]]/same[rows[key]]);
				var avg=(key_avg/yarn[rows[key]])*rows_val[key];
				avg=number_format_common(avg,5,0)
				if(cbofabricnature2==2)
				{
					document.getElementById('avgtxtconsumption_'+key).value=avg;
					$('#txtconsumption_'+key).attr("avglValue", avg);
					$('#txtconsumption_'+key).attr("title", avg);
				}
				if(cbofabricnature2==3)
				{
					document.getElementById('avgtxtgsmweight_'+key).value=avg;
					$('#txtgsmweight_'+key).attr("avglValue", avg);
					$('#txtgsmweight_'+key).attr("title", avg);
				}
			}// end for loop
		}// end if(value=="Make AVG")
	}

	function update_related_data(operation)
	{
		$('#accordion_h_yarn').click();
		 var row_num=$('#tbl_yarn_cost tr').length-1;
		for(var i=1; i<=row_num; i++)
		{
		calculate_yarn_consumption_ratio(i,'calculate_consumption')
		}
		//fnc_fabric_yarn_cost_dtls( operation );
	}
//Fabric Cost end -------------------------------------
// Yarn Cost-------------------------------------------
	function add_break_down_tr_yarn_cost( i,tr )
	{
		alert("Not Allowed to insert row")
		return;
		var row_num=$('#tbl_yarn_cost tr').length-1;
		if (i==0)
		{
			i=1;
			 $("#txtconstruction_"+i).autocomplete({
				 source: str_construction
			  });
			   $("#txtcomposition_"+i).autocomplete({
				 source:  str_composition
			  });
			  return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_yarn_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			  }).end().appendTo("#tbl_yarn_cost");

			  $('#increaseyarn_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_yarn_cost("+i+",this);");
			  $('#decreaseyarn_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_yarn_cost',this);");
			  $('#percentone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_one');");
			  $('#cbocompone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_one');");
			  //$('#percenttwo_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_two');");
			  //$('#cbocomptwo_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_two');");
			  //$('#consratio_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio("+i+",'calculate_consumption')");
			  $('#consqnty_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio("+i+",'calculate_amount')");
			  $('#txtrateyarn_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio("+i+",'calculate_amount')");
			  $('#updateidyarncost_'+i).val("");
			 // set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
			  set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
			  set_sum_value( 'txtavgconsumptionyarn_sum', 'avgconsqnty_', 'tbl_yarn_cost' );
			  set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
			 //calculate_yarn_consumption_ratio(i,'calculate_consumption')
			 //calculate_yarn_consumption_ratio(i,'calculate_ratio');
			 //calculate_yarn_consumption_ratio(i,'calculate_amount')
		}
	}

	function control_composition(id,td,type)
	{
		return;

		var cbocompone=(document.getElementById('cbocompone_'+id).value);
		//var cbocomptwo=(document.getElementById('cbocomptwo_'+id).value);
		var percentone=(document.getElementById('percentone_'+id).value)*1;
		//var percenttwo=(document.getElementById('percenttwo_'+id).value)*1;
		var row_num=$('#tbl_yarn_cost tr').length-1;

		if(type=='percent_one' && percentone>100)
		{
			alert("Greater Than 100 Not Allwed");
			document.getElementById('percentone_'+id).value="";
		}

		if(type=='percent_one' && percentone<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('percentone_'+id).value="";
			document.getElementById('percentone_'+id).disabled=true;
			document.getElementById('cbocompone_'+id).value=0;
			document.getElementById('cbocompone_'+id).disabled=true;
			//document.getElementById('percenttwo_'+id).value=100;
			//document.getElementById('percenttwo_'+id).disabled=false;
			//document.getElementById('cbocomptwo_'+id).disabled=false;

		}
		if(type=='percent_one' && percentone==100)
		{
			//document.getElementById('percenttwo_'+id).value="";
			//document.getElementById('cbocomptwo_'+id).value=0;
			//document.getElementById('percenttwo_'+id).disabled=true;
			//document.getElementById('cbocomptwo_'+id).disabled=true;
		}

		if(type=='percent_one' && percentone < 100 && percentone > 0 )
		{
			//document.getElementById('percenttwo_'+id).value=100-percentone;
			//document.getElementById('percenttwo_'+id).disabled=false;
			//document.getElementById('cbocomptwo_'+id).disabled=false;
			//document.getElementById('cbocomptwo_'+id).value=0;
		}

		if(type=='comp_one' && cbocompone==cbocomptwo  )
		{
			alert("Same Composition Not Allowed");
			document.getElementById('cbocompone_'+id).value=0;
			//document.getElementById('percenttwo_'+id).value=100-percentone;
			//document.getElementById('cbocomptwo_'+id).value=0;
		}

		if(type=='percent_two' && percenttwo>100)
		{
			alert("Greater Than 100 Not Allwed")
			document.getElementById('percenttwo_'+id).value="";
			//document.getElementById('cbocompone_'+id).value=0;
		}
		if(type=='percent_two' && percenttwo<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			//document.getElementById('percenttwo_'+id).value="";
			//document.getElementById('percenttwo_'+id).disabled=true;
			//document.getElementById('cbocomptwo_'+id).value=0;
			//document.getElementById('cbocomptwo_'+id).disabled=true;
			document.getElementById('percentone_'+id).value=100;
			document.getElementById('percentone_'+id).disabled=false;
			document.getElementById('cbocompone_'+id).disabled=false;
		}
		if(type=='percent_two' && percenttwo==100)
		{
			document.getElementById('percentone_'+id).value="";
			document.getElementById('cbocompone_'+id).value=0;
			document.getElementById('percentone_'+id).disabled=true;
			document.getElementById('cbocompone_'+id).disabled=true;
		}

		if(type=='percent_two' && percenttwo<100 && percenttwo>0)
		{
			document.getElementById('percentone_'+id).value=100-percenttwo;
			document.getElementById('percentone_'+id).disabled=false;
			document.getElementById('cbocompone_'+id).disabled=false;

			//document.getElementById('cbocompone_'+id).value=0;
		}

		if(type=='comp_two' && cbocomptwo==cbocompone)
		{
			alert("Same Composition Not Allowed");
			document.getElementById('cbocomptwo_'+id).value=0;
			//document.getElementById('percentone_'+id).value=100-percenttwo;
			//document.getElementById('cbocompone_'+id).value=0;
		}
	}

	function calculate_yarn_consumption_ratio(i,type)
	{
		var cbocount=document.getElementById('cbocount_'+i).value;
		var cbocompone=document.getElementById('cbocompone_'+i).value;
		var percentone=document.getElementById('percentone_'+i).value;
		//var cbocomptwo=document.getElementById('cbocomptwo_'+i).value;
		//var percenttwo=document.getElementById('percenttwo_'+i).value;
		var cbotype=document.getElementById('cbotype_'+i).value;
		var txtrateyarn=document.getElementById('txtrateyarn_'+i).value

		var rowCount = $('#tbl_yarn_cost tr').length-1;

		for (var k=i; k<=rowCount; k++)
		{
			var cbocountk=document.getElementById('cbocount_'+k).value;
			var cbocomponek=document.getElementById('cbocompone_'+k).value;
			var percentonek=document.getElementById('percentone_'+k).value;
			//var cbocomptwok=document.getElementById('cbocomptwo_'+k).value;
			//var percenttwok=document.getElementById('percenttwo_'+k).value;
			var cbotypek=document.getElementById('cbotype_'+k).value;
			if(cbocount==cbocountk && cbocompone==cbocomponek && percentone==percentonek && cbotype==cbotypek)
			{
				document.getElementById('txtrateyarn_'+k).value=txtrateyarn;
				document.getElementById('txtamountyarn_'+k).value=number_format_common((document.getElementById('consqnty_'+k).value*1)*(txtrateyarn*1),1,0,document.getElementById('cbo_currercy').value);
			}
			else
			{
				document.getElementById('txtamountyarn_'+i).value=number_format_common((document.getElementById('consqnty_'+i).value*1)*(document.getElementById('txtrateyarn_'+i).value*1),1,0,document.getElementById('cbo_currercy').value);
			}
		}
		//set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
		set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtavgconsumptionyarn_sum', 'avgconsqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
	}

	function set_yarn_rate(i)
	{
		//alert(i)
		var txt_costing_date=document.getElementById('txt_costing_date').value;
		var cbocount=document.getElementById('cbocount_'+i).value;
		var cbocompone=document.getElementById('cbocompone_'+i).value;
		var percentone=document.getElementById('percentone_'+i).value;
		var cbotype=document.getElementById('cbotype_'+i).value;
		var supplier_id=document.getElementById('supplier_'+i).value;
		var yarn_rate = return_ajax_request_value(cbocount+"_"+cbocompone+"_"+percentone+"_"+cbotype+"_"+supplier_id+"_"+txt_costing_date, 'get_yarn_rate', 'requires/pre_cost_entry_controller_v3');
		if(yarn_rate=="" || yarn_rate==0)
		{
			alert('Yarn Rate not set');
			return;
		}
		document.getElementById('txtrateyarn_'+i).value=number_format_common(yarn_rate,1,0,document.getElementById('cbo_currercy').value);
		calculate_yarn_consumption_ratio(i,'calculate_amount');
	}

	function fnc_fabric_yarn_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}

		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}

		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(2);
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				//alert(pri_fabric_pre_cost+"==="+txt_fabric_pre_cost);
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation');
					release_freezing();
					return;
				}
			}
		}

	    var row_num=$('#tbl_yarn_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbocompone_'+i+'*percentone_'+i+'*consqnty_'+i+'*cbocount_'+i+'*cbotype_'+i+'*txtrateyarn_'+i,'Company Name*Comp 1*Percent*Cons Qnty*Count*Type*Rate')==false)
			{
				release_freezing();
				return;
			}
			data_all+="&cbocount_" + z + "='" + $('#cbocount_'+i).val()+"'"+"&cbocompone_" + z + "='" + $('#cbocompone_'+i).val()+"'"+"&percentone_" + z + "='" + $('#percentone_'+i).val()+"'"+"&color_" + z + "='" + $('#color_'+i).val()+"'"+"&cbotype_" + z + "='" + $('#cbotype_'+i).val()+"'"+"&consqnty_" + z + "='" + $('#consqnty_'+i).val()+"'"+"&txtrateyarn_" + z + "='" + $('#txtrateyarn_'+i).val()+"'"+"&avgconsqnty_" + z + "='" + $('#avgconsqnty_'+i).val()+"'"+"&txtamountyarn_" + z + "='" + $('#txtamountyarn_'+i).val()+"'"+"&supplier_" + z + "='" + $('#supplier_'+i).val()+"'"+"&updateidyarncost_" + z + "='" + $('#updateidyarncost_'+i).val()+"'"+"&cboyarnfinish_" + z + "='" + $('#cboyarnfinish_'+i).val()+"'"+"&cboyarnsnippingsystem_" + z + "='" + $('#cboyarnsnippingsystem_'+i).val()+"'"+"&cbocertification_" + z + "='" + $('#cbocertification_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*txt_cost_control_source*update_id*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsumptionyarn_sum*txtamountyarn_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_yarn_cost_dtls_reponse;
	}

	function fnc_fabric_yarn_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='PartialBooking')
			{
				alert("This Costing is Partial Booking Approved Found="+reponse[1]);
				release_freezing();
				return;
			}
			//PartialBooking
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_fabric_yarn_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value;
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);

				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
					var pre_fabcost=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_fabric_pre_cost").attr('pre_fab_cost',pre_fabcost);

					calculate_main_total();
				}

				show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
				show_hide_content('yarn_cost', '')
				//release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[3])
					var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					document.getElementById('txt_comml_pre_cost').value=reponse[3];
					document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
					set_currier_cost_method_variable(company_name);
					calculate_main_total();
					
					fnc_quotation_entry_dtls1(1);
					release_freezing();
				}
			}
		}
	}
// Yarn Cost End -------------------------------------------
//Conversion Cost-------------------------------------------
	function add_yarn_conversion_cost(row_id)
	{
		var current_row=row_id;
		var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/pre_cost_entry_controller_v3');
		var yarn_count_deter_id=$('#cbocosthead_'+row_id).val();
		var yarn_process_loss_data=return_global_ajax_value(yarn_count_deter_id, 'process_loss_from_yarn', '', 'requires/pre_cost_entry_controller_v3');
		var yarn_loss_data_arr = yarn_process_loss_data.split("__");		
		if(yarn_loss_data_arr.length>0)
		{
			//fn_deletebreak_down_tr(row_id,'tbl_conversion_cost');
			for (var i = 1; i < yarn_loss_data_arr.length; i++) {				
				add_break_down_tr_conversion_cost(row_id,conversion_from_chart);
				if(i-1==0)
				{
					var process_loss_arr = yarn_loss_data_arr[0].split("_");
					$('#cbotypeconversion_'+row_id).val(process_loss_arr[0]);
					$('#txtprocessloss_'+row_id).val(process_loss_arr[1]);
					$('#txtchargeunit_'+row_id).val(process_loss_arr[2]);
					$('#cbotypeconversion_'+row_id).change();
				}
				row_id++;
				var process_loss_arr = yarn_loss_data_arr[i].split("_");			
				$('#cbocosthead_'+row_id).val(yarn_count_deter_id);
				$('#cbotypeconversion_'+row_id).val(process_loss_arr[0]);
				$('#txtprocessloss_'+row_id).val(process_loss_arr[1]);
				$('#txtchargeunit_'+row_id).val(process_loss_arr[2]);
				$('#cbotypeconversion_'+row_id).change();
			}
		}
	}
	
	function add_break_down_tr_conversion_cost( i,conversion_from_chart )
	{
		var row_num=$('#tbl_conversion_cost tr').length-1;
		if (i==0)
		{
			i=1;
			 $("#txtconstruction_"+i).autocomplete({
				 source: str_construction
			  });
			   $("#txtcomposition_"+i).autocomplete({
				 source:  str_composition
			  });
			  return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_conversion_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			  }).end().appendTo("#tbl_conversion_cost");
			  $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
			  $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","add_yarn_conversion_cost("+i+")");
			  $('#txtprocessloss_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
			  $('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
			  $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+");");
			  $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost',this);");
			  $('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			  $('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			  $('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
			  $('#totalqty_'+i).removeAttr("onClick").attr("onClick","loadTotal( "+i+",'Conv' )");
			  $('#updateidcoversion_'+i).val("");
			  $('#cbocosthead_'+i).val("");
			  $('#cbotypeconversion_'+i).val("");
			  $('#txtprocessloss_'+i).val("");
			  $('#txtreqqnty_'+i).val("");//document.getElementById('txtknit_sum').value
			  $('#txtavgreqqnty_'+i).val("");//document.getElementById('txtknit_sum_production').value
			  $('#txtchargeunit_'+i).val("");
			  $('#txtamountconversion_'+i).val("");
			  $('#colorbreakdown_'+i).val("");
			  $('#coversionchargelibraryid_'+i).val("");
			  set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
			  set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
			  set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
			  set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		}
	}

	function set_conversion_charge_unit(i,conversion_from_chart)
	{
		var cost_head= $('#cbotypeconversion_'+i).val();
		set_conversion_qnty(i);
		if(cost_head==30 || cost_head==31)
		{
			document.getElementById('txtchargeunit_'+i).readOnly=true
			charge_unit_color_popup(i,conversion_from_chart);
			$('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
		}
		else
		{
			if(conversion_from_chart==1)
			{
				if(israte_popup==2)
				{
					if(cost_head==35)
					{
						document.getElementById('txtchargeunit_'+i).readOnly=false
						document.getElementById('txtchargeunit_'+i).focus();
					}
					else
					{
						document.getElementById('txtchargeunit_'+i).readOnly=true
					}
					set_conversion_charge_unit_pop_up(i)
				}
				else
				{
					if(cost_head==1)//cost_head==35 ||
					{
						if(cost_head==35)
						{
							document.getElementById('txtchargeunit_'+i).readOnly=false
							document.getElementById('txtchargeunit_'+i).focus();
						}
						else
						{
							document.getElementById('txtchargeunit_'+i).readOnly=true
						}
						set_conversion_charge_unit_pop_up(i)
					}else{
						document.getElementById('txtchargeunit_'+i).readOnly=false
					}
				}
				$('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
			}
			else
			{
				document.getElementById('txtchargeunit_'+i).readOnly=false
				$('#txtchargeunit_'+i).removeAttr("onClick");
			}
		}
	}

	function set_conversion_charge_unit_pop_up(i)
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value
		var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
		var coversionchargelibraryid=document.getElementById('coversionchargelibraryid_'+i).value
		if(cbo_company_name==0)
		{
			alert("Select Company");
			return;
		}
		if(cbotypeconversion==0)
		{
			alert("Select Process");
			return;
		}
		if(txt_exchange_rate==0 || txt_exchange_rate=="")
		{
			alert("Select Exchange Rate");
			return;
		}
		else
		{
			var page_link='requires/pre_cost_entry_controller_v3.php?action=conversion_chart_popup&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&coversionchargelibraryid='+coversionchargelibraryid;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Conversion Chart', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var charge_id=this.contentDoc.getElementById("charge_id");
				var charge_value=this.contentDoc.getElementById("charge_value");

				document.getElementById('coversionchargelibraryid_'+i).value=charge_id.value;
				document.getElementById('txtchargeunit_'+i).value=number_format_common(charge_value.value/txt_exchange_rate,5,0,document.getElementById('cbo_currercy').value);
				//math_operation( 'txtamountconversion_'+i, 'txtreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
				math_operation( 'txtamountconversion_'+i, 'txtavgreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
				set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
			}
		}
	}

	function set_conversion_qnty(i)
	{
	  var cbocosthead= document.getElementById('cbocosthead_'+i).value;
	  var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
	  var txtprocessloss=document.getElementById('txtprocessloss_'+i).value*1;
	  var updateidcoversion=$('#updateidcoversion_'+i).val();
	  if(cbotypeconversion==30 || cbotypeconversion==31){
		  return;
	  }
	  if(cbocosthead !=0){
		  var conversion_qnty=trim(return_global_ajax_value(cbocosthead+"_"+cbotypeconversion+"_"+txtprocessloss+"_"+updateidcoversion, 'set_conversion_qnty', '', 'requires/pre_cost_entry_controller_v3'));
	  }
	  if(cbocosthead ==0){
		  var conversion_qnty=document.getElementById('txtknit_sum').value+"_"+document.getElementById('txtknit_sum_production').value+"_"+txtprocessloss+"_"+updateidcoversion;
	  }
	  conversion_qnty=conversion_qnty.split("_");
	  document.getElementById('txtreqqnty_'+i).value=trim(conversion_qnty[0]);
	  document.getElementById('txtavgreqqnty_'+i).value=trim(conversion_qnty[1]);

	  if((conversion_qnty[2]=="" || conversion_qnty[2]==0) && cbotypeconversion>0){
			$('#txtprocessloss_'+i).css({ 'background': 'white' });
			$('#txtprocessloss_'+i).attr( 'title','Process Loss not Set In Library' );
			$('#txtprocessloss_'+i).attr( 'readonly',false );
			$('#txtprocessloss_'+i).val(0);
		}
		else{
			$('#txtprocessloss_'+i).val(conversion_qnty[2])
			if(conversion_qnty[3]>0){
				$('#txtprocessloss_'+i).attr( 'title','Process Loss Found In Library' );
				$('#txtprocessloss_'+i).attr( 'readonly',true );
				$('#txtprocessloss_'+i).css({ 'background': 'grey' });
			}else{
				$('#txtprocessloss_'+i).attr( 'readonly',false );
			}
		}
	  calculate_conversion_cost(i);
	}

	function calculate_conversion_cost(i){
		math_operation( 'txtamountconversion_'+i, 'txtavgreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
		set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
	}

	function charge_unit_color_popup(i,conversion_from_chart)
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value
		var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
		var cbo_currercy= document.getElementById('cbo_currercy').value;
		var cbocosthead=document.getElementById('cbocosthead_'+i).value;
		var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
		var job_no=document.getElementById('txt_job_no').value;
		var colorbreakdown=document.getElementById('colorbreakdown_'+i).value;
		var coversionchargelibraryid=document.getElementById('coversionchargelibraryid_'+i).value;
		var page_link="requires/pre_cost_entry_controller_v3.php?action=conversion_color_popup&cbocosthead="+cbocosthead+'&job_no='+job_no+'&colorbreakdown='+colorbreakdown+'&conversion_from_chart='+conversion_from_chart+'&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_currercy='+cbo_currercy+'&coversionchargelibraryid='+coversionchargelibraryid;
		if(cbotypeconversion==25 || cbotypeconversion==30 || cbotypeconversion==31){
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Color Pop Up', 'width=1060px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function(){
			    var theform=this.contentDoc.forms[0];
				var color_breck_down=this.contentDoc.getElementById("color_breck_down");
				var chargelibid_breck_down=this.contentDoc.getElementById("chargelibid_breck_down");
				var avg_total_value=this.contentDoc.getElementById("avg_total_value");
				var avg_total_cons=this.contentDoc.getElementById("avg_total_cons");
				if (color_breck_down.value!=""){
					document.getElementById('colorbreakdown_'+i).value=color_breck_down.value;
					document.getElementById('txtchargeunit_'+i).value=avg_total_value.value;
					var txtprocessloss=document.getElementById('txtprocessloss_'+i).value*1;
					var cons=avg_total_cons.value;
					//var avg_req=cons-(cons*txtprocessloss)/100;//comments by kausar
					var avg_req=cons;//-(cons*txtprocessloss)/100;
	                var avg_cons=cons-(cons*txtprocessloss)/100;
					document.getElementById('txtreqqnty_'+i).value=number_format_common(avg_req, 5, 0);
					document.getElementById('txtavgreqqnty_'+i).value=number_format_common(avg_cons, 5, 0);
					document.getElementById('coversionchargelibraryid_'+i).value=chargelibid_breck_down.value;
					calculate_conversion_cost(i);
				}
			}
		}
	}

	function fnc_fabric_conversion_cost_dtls( operation )
	{
	   freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(2);
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				//alert(pri_fabric_pre_cost+"==="+txt_fabric_pre_cost);
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation');
					release_freezing();
					return;
				}
			}
		}

	    var row_num=$('#tbl_conversion_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbocosthead_'+i,'Company Name*Fabric Description')==false)
			{
				release_freezing();
				return;
			}
			
			data_all+="&cbocosthead_" + z + "='" + $('#cbocosthead_'+i).val()+"'"+"&cbotypeconversion_" + z + "='" + $('#cbotypeconversion_'+i).val()+"'"+"&txtprocessloss_" + z + "='" + $('#txtprocessloss_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtavgreqqnty_" + z + "='" + $('#txtavgreqqnty_'+i).val()+"'"+"&txtchargeunit_" + z + "='" + $('#txtchargeunit_'+i).val()+"'"+"&txtamountconversion_" + z + "='" + $('#txtamountconversion_'+i).val()+"'"+"&cbostatusconversion_" + z + "='" + $('#cbostatusconversion_'+i).val()+"'"+"&updateidcoversion_" + z + "='" + $('#updateidcoversion_'+i).val()+"'"+"&colorbreakdown_" + z + "='" + $('#colorbreakdown_'+i).val()+"'"+"&coversionchargelibraryid_" + z + "='" + $('#coversionchargelibraryid_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_fabric_conversion_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*txt_cost_control_source*cbo_company_name*txt_quotation_id*copy_quatation_id*txtconreqnty_sum*txtconchargeunit_sum*txtconamount_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_conversion_cost_dtls_reponse;
	}

	function fnc_fabric_conversion_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_fabric_conversion_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value*1;
				if (reponse[0].length>2) reponse[0]=10;

				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
					var pre_fabcost=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_fabric_pre_cost").attr('pre_fab_cost',pre_fabcost);

					
					show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
					show_hide_content('conversion_cost', '')
					set_currier_cost_method_variable(company_name);
					calculate_main_total();
					if(reponse[0]==0 || reponse[0]==1)
					{
						fnc_quotation_entry_dtls1(1)
						release_freezing();
					}
				}
				else
				{
					release_freezing();
				}

				
			}
		}
	}

//Conversion Cost End -------------------------------------------
// Trim Cost-----------------------------------------------------
	function add_break_down_tr_trim_cost( i )
	{
		var row_num=$('#tbl_trim_cost tr').length-1;
		if (i==0)
		{
			i=1;
			/* $("#txtconstruction_"+i).autocomplete({
				 source: str_construction
			  });
			   $("#txtcomposition_"+i).autocomplete({
				 source:  str_composition
			  });*/
			   $("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
				});
			  return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_trim_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			  }).end().appendTo("#tbl_trim_cost");

			 // $('#tbl_trim_cost tr:last td:eq(4)').attr('id','tdsupplier_'+i);

			  $('#tbl_trim_cost tr:last td:eq(6)').attr('id','tdsupplier_'+i);

			  $('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+" )");
			  $('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup('"+i+"_1' )");
			  $('#txtnominasupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_trimsupplier("+i+")");
			  //$('#cbonominasupplier_'+i).removeAttr("onChange").attr("onChange","set_trim_rate_amount( this.value,"+i+",'supplier_change' )");
			  $('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
			  $('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost',this);");
			  $('#txtconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
			  $('#txtdescription_'+i).removeAttr("onDblClick").attr("onDblClick","trims_description_popup( "+i+" )");
			  //$('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","open_calculator( "+i+" )");
			  $('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick"," set_session_large_post_data_trim('requires/pre_cost_entry_controller_v3.php?action=consumption_popup_trim', 'Consumtion Entry Form',"+i+")");
			  $('#txttrimrate_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
			  $('#txttrimrate_'+i).removeAttr("onDblClick").attr("onDblClick","trim_rate_popup( "+i+" )");
			  $('#totalqty_'+i).removeAttr("onClick").attr("onClick","loadTotal( "+i+",'trim' )");
			  $('#seq_'+i).val('');
			  if($('#seq_'+i).val()=='')
			  {
				$('#seq_'+i).val( i );
			  }
			  $('#cbogrouptext_'+i).removeAttr('disabled','disabled');
			  $('#txtdescription_'+i).removeAttr('disabled','disabled');
			  $('#countrytext_'+i).removeAttr('disabled','disabled');
			  $('#txtsupref_'+i).removeAttr('disabled','disabled');
			  $('#txtremark_'+i).removeAttr('disabled','disabled');
			  $('#cbosourceid_'+i).removeAttr('disabled','disabled');
			  $('#txtnominasupplier_'+i).removeAttr('disabled','disabled');
			  $('#cbonominasupplier_'+i).removeAttr('disabled','disabled');
			  $('#txttrimrate_'+i).removeAttr('disabled','disabled');
			  $('#decreasetrim_'+i).removeAttr('disabled','disabled');

			  $('#updateidtrim_'+i).val("");
			 // $('#cbogrouptext_'+i).val("");
			 // $('#cbogroup_'+i).val("");
			 // $('#countrytext_'+i).val("");
			 // $('#country_'+i).val("");

			 // $('#txtdescription_'+i).val("");
			 // $('#txtsupref_'+i).val("");
			//  $('#cboconsuom_'+i).val(0);
			 // $('#cbosourceid_'+i).val(0);
			//  $('#consbreckdown_'+i).val("");
			  $('#txtconsdzngmts_'+i).val("");
			  $('#txttrimrate_'+i).val("");
			  $('#txttrimamount_'+i).val("");
			  $('#txtnominasupplier_'+i).val("");
			  $('#cbonominasupplier_'+i).val("");
			  $('#calculatorstring_'+i).val("");
			  $('#totalqty_'+i).val("");
			  $('#totalamount_'+i).val("");
			  $("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
			  });
			  set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			  set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
			  set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
			 // set_multiselect('cbogroup_'+i,'0','0','','');
		}
	}

	function loadTotal(i,type)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;

		var res=return_global_ajax_value(txt_job_no+'__'+type, 'load_total_qtyAmount', '', 'requires/pre_cost_entry_controller_v3');
		if(type=='trim')
		{
			var updateidtrim=document.getElementById('updateidtrim_'+i).value;
			var resObj=JSON.parse(res);
			var row_num=$('#tbl_trim_cost tr').length-1;
			var tot_trim_amt='';var total_trim_qty='';
			for (var j=1; j<=row_num; j++)
			{
				var updateidtrim=document.getElementById('updateidtrim_'+j).value;
				//alert(resObj.qty[updateidtrim]);
				if(updateidtrim == ""){
					//alert("Save the row first");
					continue;
				}
				if(resObj.qty[updateidtrim] !=undefined){
					document.getElementById('totalqty_'+j).value=resObj.qty[updateidtrim];
					document.getElementById('totalamount_'+j).value=resObj.amt[updateidtrim];
					var tot_trim_amt=(tot_trim_amt*1)+resObj.amt[updateidtrim]*1;
					var total_trim_qty=(total_trim_qty*1)+resObj.qty[updateidtrim]*1;
				}
			}
			//alert(tot_trim_amt);
			//document.getElementById('totalqty_sum').value=total_trim_qty;
			document.getElementById('totalamount_sum').value=tot_trim_amt;

		}
		if(type=='fabric')
		{
			var updateidfab=document.getElementById('updateid_'+i).value;
			var resObj=JSON.parse(res);
			var row_num=$('#tbl_fabric_cost tr').length-1;
			for (var j=1; j<=row_num; j++)
			{
				var updateidfab=document.getElementById('updateid_'+j).value;
				//alert(resObj.qty[updateidtrim]);
				if(updateidfab == ""){
					//alert("Save the row first");
					continue;
				}
				if(resObj.qty[updateidfab] !=undefined){
					document.getElementById('totalqty_'+j).value=resObj.qty[updateidfab];
					document.getElementById('totalamount_'+j).value=resObj.amt[updateidfab];
				}
			}
		}
		if(type=='Yarn')
		{
			var updateidfab=document.getElementById('updateidyarncost_'+i).value;
			var resObj=JSON.parse(res);
			var row_num=$('#tbl_yarn_cost tr').length-1;
			//alert(row_num)
			var tot_yarn_amt='';var total_yarn_qty='';
			for (var j=1; j<=row_num; j++)
			{
				var updateidyarn=document.getElementById('updateidyarncost_'+j).value;
				//alert(resObj.qty[updateidyarn]);
				if(updateidyarn == ""){
					//alert("Save the row first");
					continue;
				}
				if(resObj.qty[updateidyarn]!=undefined){
					//alert(resObj.qty[updateidyarn]+"=="+resObj.amt[updateidyarn]+"=="+updateidyarn)
					var yQty=resObj.qty[updateidyarn];
					
					document.getElementById('totalyqty_'+j).value=yQty;
					document.getElementById('totalyamount_'+j).value=resObj.amt[updateidyarn];
					
					var tot_yarn_amt=(tot_yarn_amt*1)+resObj.amt[updateidyarn]*1;
					//var total_yarn_qty=(total_yarn_qty*1)+resObj.qty[updateidyarn]*1;
				}
			}
			$('#totalyarnamount').val(tot_yarn_amt);
		}
		if(type=='Conv')
		{
			var updateidconv=document.getElementById('updateidcoversion_'+i).value;
			var resObj=JSON.parse(res);
			var row_num=$('#tbl_conversion_cost tr').length-1;
			// alert(row_num)
			var tot_conv_amt=''; var total_conv_qty='';
			for (var j=1; j<=row_num; j++)
			{
				var updateidconv=document.getElementById('updateidcoversion_'+j).value;
				//alert(resObj.qty[updateidconv]);
				if(updateidconv==""){
					//alert("Save the row first");
					continue;
				}
				//alert(resObj.qty[updateidconv]);
				if(resObj.qty[updateidconv]!=undefined){
					document.getElementById('totalcqty_'+j).value=resObj.qty[updateidconv];
					document.getElementById('totalcamount_'+j).value=resObj.amt[updateidconv];
					var tot_conv_amt=(tot_conv_amt*1)+resObj.amt[updateidconv]*1;
					//var total_conv_qty=(total_conv_qty*1)+resObj.qty[updateidconv]*1;
				}
			}
			$('#txtconareqamt_sum').val(tot_conv_amt);
		}
	}

	function open_country_popup(i)
	{
		var id=i.split("_");
		var i=id[0];
		var type=id[1];
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_country=document.getElementById('country_'+i).value
		var txt_country_name=document.getElementById('countrytext_'+i).value
		var page_link='requires/pre_cost_entry_controller_v3.php?action=open_country_popup';
		var title='Country';
		page_link=page_link+'&txt_job_no='+txt_job_no+'&txt_country='+trim(txt_country)+'&txt_country_name='+trim(txt_country_name);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=350px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			var theemailname=this.contentDoc.getElementById("txt_selected_name");
			document.getElementById('country_'+i).value=trim(theemail.value);
			document.getElementById('countrytext_'+i).value=trim(theemailname.value);
			if(type==2) check_duplicate(i);
		}
	}

	function set_session_large_post_data_trim(page_link,title,trorder)
	{
		var cons_breck_downn_rtim=document.getElementById('consbreckdown_'+trorder).value;
		//var cons_breck_downn_emb=''
		var data="action=save_post_session_trim&cons_breck_downn_trim="+cons_breck_downn_rtim;
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4)
			{
				if(http.responseText==1)
				{
					open_consumption_popup_trim(page_link,title,trorder);
				}
			}
		}
	}

	function open_consumption_popup_trim(page_link,title,trorder)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var cbo_approved_status=document.getElementById('cbo_approved_status').value;
		var cons_breck_downn=document.getElementById('consbreckdown_'+trorder).value;
		var txt_job_no = document.getElementById('txt_job_no').value;
	    var cbogroup=document.getElementById('cbogroup_'+trorder).value;
		var cboconsuom=document.getElementById('cboconsuom_'+trorder).value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var country=document.getElementById('country_'+trorder).value;
		var txtconsdzngmts=document.getElementById('txtconsdzngmts_'+trorder).value;
		var txttrimrate=document.getElementById('txttrimrate_'+trorder).value;
		var item_id=document.getElementById('item_id').value;
		var calculatorstring=document.getElementById('calculatorstring_'+trorder).value;
		var cbogrouptext=document.getElementById('cbogrouptext_'+trorder).value;
		var updateidtrim=document.getElementById('updateidtrim_'+trorder).value;
		var qcdata=$('#txtconsdzngmts_'+trorder).attr('qcdata');
		

	    //var calculator_parameter=return_global_ajax_value(cbogroup, 'calculator_parameter', '', 'requires/pre_cost_entry_controller_v3');
		var page_link=page_link+'&txt_job_no='+txt_job_no+'&cbo_costing_per='+cbo_costing_per+'&cons_breck_downn='+cons_breck_downn+'&cbo_approved_status='+cbo_approved_status+'&cbogroup='+cbogroup+'&cboconsuom='+cboconsuom+'&tot_set_qnty='+tot_set_qnty+'&country='+country+'&txttrimrate='+txttrimrate+'&item_id='+item_id+'&calculatorstring='+calculatorstring+"&cbogrouptext="+cbogrouptext+"&updateidtrim="+updateidtrim+"&txtconsdzngmts="+txtconsdzngmts+"&qcdata="+qcdata;
		//alert(page_link)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title+"  "+cbogrouptext, 'width=1270px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_dzn = ''; var dzn_set_ratio = '';
			var txt_costing_per = document.getElementById('cbo_costing_per').value;
			var txt_order_uom = document.getElementById('cbo_order_uom').value;
			var txt_set_qty = document.getElementById('tot_set_qnty').value;
			if(txt_costing_per==1) cons_dzn=12;
			if(txt_costing_per==2) cons_dzn=1;
			if(txt_costing_per==3) cons_dzn=24;
			if(txt_costing_per==4) cons_dzn=36;
			if(txt_costing_per==5) cons_dzn=48;
			if(txt_order_uom == 58){
				 dzn_set_ratio = cons_dzn * txt_set_qty;
			}
			if(txt_order_uom == 1){
				 dzn_set_ratio = cons_dzn;
			}
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var calculator_string=this.contentDoc.getElementById("calculator_string");
			
			var avg_totcons=this.contentDoc.getElementById("avg_totcons");
			var avg_amount=this.contentDoc.getElementById("avg_amountcons");
			var avg_rate=this.contentDoc.getElementById("avg_ratecons");

			//var set_avg_totcons = (avg_totcons.value/dzn_set_ratio)*dzn_country_percent;
			//var set_avg_totcons = (avg_totcons.value);
			document.getElementById('txtconsdzngmts_'+trorder).value=avg_totcons.value;//number_format_common(avg_totcons, 5, 0);
			document.getElementById('txttrimrate_'+trorder).value=avg_rate.value;
			document.getElementById('txttrimamount_'+trorder).value=avg_amount.value;

			//alert(cons_breck_down.value)
			document.getElementById('consbreckdown_'+trorder).value=cons_breck_down.value;
			document.getElementById('calculatorstring_'+trorder).value=calculator_string.value;
			var index=trorder-1;
			var tr=$("#tbl_trim_cost tbody tr:eq("+index+")");
			tr.css('background-color', 'green');
			$('#txtconsdzngmts_'+trorder).css('background-color', 'green');
			math_operation( 'txttrimamount_'+trorder, 'txtconsdzngmts_'+trorder+'*txttrimrate_'+trorder, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
			set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
	        set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
		}
	}

	function trim_rate_popup(i)
	{
	    var cbogroup=document.getElementById('cbogroup_'+i).value;

		var page_link="requires/pre_cost_entry_controller_v3.php?cbogroup="+trim(cbogroup)+"&action=trim_rate_popup_page";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Rate', 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var txt_selected_supllier=this.contentDoc.getElementById("txt_selected_supllier");
			var txt_selected_rate=this.contentDoc.getElementById("txt_selected_rate");
			var txt_supllierName=this.contentDoc.getElementById("txt_supllierName");
			document.getElementById('txtnominasupplier_'+i).value=txt_supllierName.value;
			document.getElementById('cbonominasupplier_'+i).value=txt_selected_supllier.value;
			document.getElementById('txttrimrate_'+i).value=txt_selected_rate.value;
			math_operation( 'txttrimamount_'+i, 'txtconsdzngmts_'+i+'*txttrimrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
			set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
	        set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
		}
	}

	function open_calculator(i)
	{
		var cbogroup=document.getElementById('cbogroup_'+i).value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var calculator_parameter=return_global_ajax_value(cbogroup, 'calculator_parameter', '', 'requires/pre_cost_entry_controller_v3');
		if(trim(calculator_parameter)!=0)
		{
			var page_link="requires/pre_cost_entry_controller_v3.php?calculator_parameter="+trim(calculator_parameter)+"&action=calculator_type&cbo_costing_per="+cbo_costing_per;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Calculator', 'width=350px,height=200px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var txt_cons_for_cone=this.contentDoc.getElementById("txt_cons_for_cone");
				var txt_clacolator_param_value=this.contentDoc.getElementById("txt_clacolator_param_value");
				document.getElementById('txtconsdzngmts_'+i).value=txt_cons_for_cone.value;
				calculate_trim_cost(i);
			}
		}
	}

	function openpopup_itemgroup(i)
	{
		var updateidtrim=document.getElementById('updateidtrim_'+i).value
		if(updateidtrim*1>0){
			var booking=return_global_ajax_value(updateidtrim, 'check_trims_booking', '', 'requires/pre_cost_entry_controller_v3');
			if(booking==11)
			{
				alert("Booking Found, Change Not Allowed");
				return;
			}
		}

		var page_link="requires/pre_cost_entry_controller_v3.php?action=openpopup_itemgroup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Group Select', 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//var id=this.contentDoc.getElementById("gid");
			var itemdata=this.contentDoc.getElementById("itemdata").value;
			//alert(itemdata);
			var row_count=$('#tbl_trim_cost tr').length-1;
			var itemdata=itemdata.split(",");
			var a=0; var n=0;
			for(var b=1; b<=itemdata.length; b++)
			{
				//alert(itemdata[a]);
				var exdata="";
				var exdata=itemdata[a].split("***");
				if(a==0)
				{
					document.getElementById('cbogroup_'+i).value=exdata[0];
					document.getElementById('cbogrouptext_'+i).value=exdata[1];
					document.getElementById('cboconsuom_'+i).value=exdata[2];
					$('#cbogrouptext_'+i).removeAttr("title").attr( 'title',exdata[1] );
					set_trim_cons_uom(exdata[0],i);
				}
				else
				{
					add_break_down_tr_trim_cost(row_count);
					n++;
					row_count++;
					document.getElementById('cbogroup_'+row_count).value=exdata[0];
					document.getElementById('cbogrouptext_'+row_count).value=exdata[1];
					document.getElementById('cboconsuom_'+row_count).value=exdata[2];
					$('#cbogrouptext_'+row_count).removeAttr("title").attr( 'title',exdata[1] );
					set_trim_cons_uom(exdata[0],row_count);
				}
				a++;
			}
			//document.getElementById('cbogroup_'+i).value=id.value;
			//document.getElementById('cbogrouptext_'+i).value=name.value;
			//$('#cbogrouptext_'+i).removeAttr("title").attr( 'title',name.value );
			//set_trim_cons_uom(id.value,i)
			//trims_description_autocomplete(id.value,i)
		}
	}

	function calculate_trim_cost(i)
	{
		math_operation( 'txttrimamount_'+i, 'txtconsdzngmts_'+i+'*txttrimrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
		set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
		set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
	}

	function set_trim_cons_uom(trim_group_id,i)
	{
		var trim_rate_variable=document.getElementById('trim_rate_variable').value;
		var buyer=document.getElementById('cbo_buyer_name').value;
		set_trim_rate_amount( document.getElementById('cbonominasupplier_'+i).value,i,'item_change' );
	}

	function fncopenpopup_trimsupplier(inc)
	{
		var updateidtrim=document.getElementById('updateidtrim_'+inc).value
		if(updateidtrim*1>0){
			var booking=return_global_ajax_value(updateidtrim, 'check_trims_booking', '', 'requires/pre_cost_entry_controller_v3');
			if(booking==11)
			{
				alert("Booking Found, Change Not Allowed");
				return;
			}
		}

		var cbogroup=$('#cbogroup_'+inc).val();
		var trim_rate_variable=$('#trim_rate_variable').val();
		var buyer=$('#cbo_buyer_name').val();
		var nominasupplier=$('#cbonominasupplier_'+inc).val();

		var page_link="requires/pre_cost_entry_controller_v3.php?trim_rate_variable="+trim(trim_rate_variable)+"&action=openpopup_trimsupplier&cbogroup="+cbogroup+"&buyer="+buyer+"&nominasupplier="+nominasupplier;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Nominated Supplier PopUp', 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var suppdata=this.contentDoc.getElementById("suppdata").value;
			//alert(itemdata);
			var suppdataarr=suppdata.split(",");
			var a=0;  var suppid=""; var suppname="";
			for(var b=1; b<=suppdataarr.length; b++)
			{
				var exdata="";
				var exdata=suppdataarr[a].split("***");

				if(suppid=="") suppid=exdata[0]; else suppid+=','+exdata[0];
				if(suppname=="") suppname=exdata[1]; else suppname+=','+exdata[1];
				a++;
			}

			set_trim_rate_amount(suppid,inc,'supplier_change');

			$('#cbonominasupplier_'+inc).val(suppid);
			$('#txtnominasupplier_'+inc).val(suppname);
		}
	}

	function set_trim_rate_amount(supplier,i,type)
	{
		var updateidtrim=document.getElementById('updateidtrim_'+i).value;
		if(updateidtrim=="")
		{
			if(type=="item_change")
			{
				get_trim_rate_amount(supplier,i,type);
			}
			else
			{
				var txttrimrate=document.getElementById('txttrimrate_'+i).value;
				if(txttrimrate==0 || txttrimrate=="")
				{
					get_trim_rate_amount(supplier,i,type);
				}
				else
				{
					var r=confirm("Rate Exist,\n It may have come from price Quotation or Templete or Library\n If you want to change current rate\n Press OK \n Otherwise press Cencel");
					if(r==true)
					{
						get_trim_rate_amount(supplier,i,type);
					}
					else return;
				}
			}
		}
		else
		{
			if(type=="item_change")
			{
				get_trim_rate_amount(supplier,i,type);
			}
			else
			{
				var r=confirm("Rate Exist,\n You are in update mode\n If you want to change current rate\n Press OK \n Otherwise press Cencel");
				if(r==true)
				{
					get_trim_rate_amount(supplier,i,type);
				}
				else return;
			}
		}
	}

	function get_trim_rate_amount(supplier,i,type)
	{
		var cbogroup=document.getElementById('cbogroup_'+i).value;
		var txtconsdzngmts=(document.getElementById('txtconsdzngmts_'+i).value)*1;
		var rate=return_global_ajax_value(cbogroup+"_"+supplier, 'rate_from_library', '', 'requires/pre_cost_entry_controller_v3');
		rate=trim(rate);
		if(type=="item_change")
		{
			document.getElementById('txttrimrate_'+i).value=rate;
		}
		else
		{
			if(rate==0)
			{
				var r=confirm("Rate not found in Library,\n Press OK to change current rate with 0\n Otherwise press Cencel");
				if(r==true)
				{
					document.getElementById('txttrimrate_'+i).value=rate;
				}
				else return;
			}
			else
			{
				document.getElementById('txttrimrate_'+i).value=rate;
			}
		}
		calculate_trim_cost(i);
	}

	function trims_description_popup(i)
	{
		var txtdescription=document.getElementById('txtdescription_'+i).value;
		var data=txtdescription
		var title = 'Description';
		var page_link = 'requires/pre_cost_entry_controller_v3.php?data='+data+'&action=trims_description_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=200px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var description=this.contentDoc.getElementById("description");
			$('#txtdescription_'+i).val(description.value);
		}
	}

	function fnc_trim_cost_dtls( operation )
	{
	    freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);
		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(3);
			//alert(qc_validate);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_trim_pre_cost=$('#txt_trim_pre_cost').attr('pri_trim_pre_cost')*1;
				var txt_trim_pre_cost=$('#txt_trim_pre_cost').val()*1;
				if(txt_trim_pre_cost>pri_trim_pre_cost)
				{
					alert('Trims cost is greater than Quotation');
					release_freezing();
					return;
				}
			}
		}

		//var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: ';
		//var reg=/[^a-zA-Z0-9\.\-\,\%\@\!\\\/\<\>\?\+\[\]\{\}\;\:]+/;
		 //var test = new RegExp("[a-zA-Z0-9!@#$%^*_|]");
		var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
	    var row_num=$('#tbl_trim_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbogroup_'+i,'Company Name*Group')==false)//+'*txtconsdzngmts_'+i*Cons/Unit Gmts
			{
				release_freezing();
				return;
			}

			for (var k=1;k<=row_num; k++)
			{
				if(k==i)
				{
					continue;
				}
				else
				{
					if(document.getElementById('cbogrouptext_'+i).value==document.getElementById('cbogrouptext_'+k).value && document.getElementById('cbogroup_'+i).value==document.getElementById('cbogroup_'+k).value && document.getElementById('txtdescription_'+i).value==document.getElementById('txtdescription_'+k).value  && document.getElementById('txtsupref_'+i).value==document.getElementById('txtsupref_'+k).value  && document.getElementById('cbonominasupplier_'+i).value==document.getElementById('cbonominasupplier_'+k).value)
					{
						alert("Same Group, Same Description, Same Brand/Sup Ref And Same Nominated Suppiler Duplication Not Allowed.");
						release_freezing();
						return;
					}
				}
			}
			
			data_all+="&cbogroup_" + z + "='" + $('#cbogroup_'+i).val()+"'"+"&txtdescription_" + z + "='" + $('#txtdescription_'+i).val()+"'"+"&txtsupref_" + z + "='" + $('#txtsupref_'+i).val()+"'"+"&cboconsuom_" + z + "='" + $('#cboconsuom_'+i).val()+"'"+"&txtconsdzngmts_" + z + "='" + $('#txtconsdzngmts_'+i).val()+"'"+"&txttrimrate_" + z + "='" + $('#txttrimrate_'+i).val()+"'"+"&txttrimamount_" + z + "='" + $('#txttrimamount_'+i).val()+"'"+"&cboapbrequired_" + z + "='" + $('#cboapbrequired_'+i).val()+"'"+"&cbonominasupplier_" + z + "='" + $('#cbonominasupplier_'+i).val()+"'"+"&updateidtrim_" + z + "='" + $('#updateidtrim_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&calculatorstring_" + z + "='" + $('#calculatorstring_'+i).val()+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'"+"&cbotrimstatus_" + z + "='" + $('#cbotrimstatus_'+i).val()+"'"+"&cbosourceid_" + z + "='" + $('#cbosourceid_'+i).val()+"'"+"&cboitemprint_" + z + "='" + $('#cboitemprint_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_trim_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsdzntrim_sum*txtratetrim_sum*txttrimamount_sum*cbo_costing_per',"../../")+data_all;

		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trim_cost_dtls_reponse;
	}

	function fnc_trim_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==6)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			if(reponse[0]==15)
			{
				 setTimeout('fnc_trim_cost_dtls('+ reponse[1]+')',8000);
			}
			if(reponse[0]==10)
			{
				show_msg(reponse[0]);
				release_freezing();
				return;
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==10)
				{
					show_msg(reponse[0]);
					release_freezing();
					return;
				}
				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_trim_pre_cost=$('#txttrimamount_sum').val()*1;
					var pre_trimcost=number_format_common(txt_trim_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_trim_pre_cost").attr('pre_trim_cost',pre_trimcost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value);

				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[3])
					var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					document.getElementById('txt_comml_pre_cost').value=reponse[3];
					document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
					calculate_main_total();
					fnc_quotation_entry_dtls1(1);
					release_freezing();
				}
			}
		}
	}
// Trim Cost End -----------------------------------------------------
// Embellisment Cost-------------------------------------------------
	function add_break_down_tr_embellishment_cost( i )
	{
		var row_num=$('#tbl_embellishment_cost tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			$("#tbl_embellishment_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_embellishment_cost");
			$("#tbl_embellishment_cost tr:last").removeAttr('id').attr('id','embellishment_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(0)").removeAttr('id').attr('id','cboembnametd_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(1)").removeAttr('id').attr('id','embtypetd_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(4)").removeAttr('id').attr('id','embsuppliertd_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(5)").removeAttr('id').attr('id','txtembconsdzngmtstd_'+i);

			$("#tbl_embellishment_cost tr:last").find("td:eq(6)").removeAttr('id').attr('id','txtembratetd_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(7)").removeAttr('id').attr('id','txtembamounttd_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(8)").removeAttr('id').attr('id','cboembstatustd_'+i);
			$("#tbl_embellishment_cost tr:last").find("td:eq(9)").removeAttr('id').attr('id','buttontd_'+i);

			 // $('#txtembamount_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr_embellishment_cost("+i+");");
			  //$('#embtypetd_'+i).removeAttr("id").attr("id","'embtypetd_'+i");
			  //$('#embellishment_' + i).find("td:eq(0)").removeAttr('id').attr('id','cboembnametd_'+i);
			  //$('#embellishment_' + i).find("td:eq(1)").removeAttr('id').attr('id','embtypetd_'+i);

			  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_embellishment_cost("+i+");");
			  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost',this);");
			  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
			  $('#txtembconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data_emb( 'requires/pre_cost_entry_controller_v3.php?action=consumption_popup_emb', 'Consumtion Entry Form',"+i+")");
			  $('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");

			  $('#txtembsupplier_'+i).removeAttr("onfocus").attr("onfocus","auto_completesupplier( $('#cbo_company_name').val(), '"+i+"' )");
			  $('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup('"+i+"_2' )");

			  $('#cboembname_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
			  $('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
			  $('#cboembbodypart_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
			 // $('#countrytext_'+i).removeAttr("onBlur").attr("onBlur","check_duplicate( "+i+" )");
			  $('#txtembsupplier_'+i).removeAttr("onBlur").attr("onBlur","check_duplicate( "+i+" )");

			  $('#cboembtype_'+i).val(0);
			  $('#cboembbodypart_'+i).val(0);
			  $('#countrytext_'+i).val('');
			  $('#country_'+i).val('');
			  $('#txtembsupplier_'+i).val('');
			  $('#hidembsupplier_'+i).val('');
			  $('#cboembsupplierid_'+i).val(0);

			  $('#txtembconsdzngmts_'+i).val("");
			  $('#txtembrate_'+i).val("");
			  $('#txtembamount_'+i).val("");
			  $('#embupdateid_'+i).val("");
			  $('#cboembname_'+i).val($('#cboembname_'+row_num).val());
			  $('#empbudgeton_'+i).val($('#empbudgeton_'+row_num).val());

			  $('#cboembname_'+i).removeAttr('disabled');
			  $('#cboembtype_'+i).removeAttr('disabled');
			  $('#cboembbodypart_'+i).removeAttr('disabled');
			  $('#countrytext_'+i).removeAttr('disabled');
			  $('#txtembsupplier_'+i).removeAttr('disabled');


			  //set_sum_value( 'txtconsdznemb_sum', 'txtembconsdzngmts_', 'tbl_embellishment_cost' );
			  //set_sum_value( 'txtrateemb_sum', 'txtembrate_', 'tbl_embellishment_cost' );
			  set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			  calculate_main_total();
		}
	}

	function cbotype_loder( i )
	{
		var cboembname=document.getElementById('cboembname_'+i).value
		var company_id=document.getElementById('cbo_company_name').value
		load_drop_down( 'requires/pre_cost_entry_controller_v3', cboembname+'_'+i+'_'+company_id, 'load_drop_down_embtype', 'embtypetd_'+i );
		get_php_form_data(cboembname+'_'+i+'_'+company_id,'load_drop_down_embtype_budgeton','requires/pre_cost_entry_controller_v3' );

		check_duplicate(i);
	}

	function check_duplicate(id)
	{
		var cboembname=(document.getElementById('cboembname_'+id).value);
		var cboembtype=(document.getElementById('cboembtype_'+id).value);
		var embbodypart=(document.getElementById('cboembbodypart_'+id).value);
		var countrytext=(document.getElementById('countrytext_'+id).value);
		var txtembsupplier=(document.getElementById('txtembsupplier_'+id).value);
		var row_num=$('#tbl_embellishment_cost tr').length-1;

		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(cboembname==document.getElementById('cboembname_'+k).value && cboembtype==document.getElementById('cboembtype_'+k).value && embbodypart==document.getElementById('cboembbodypart_'+k).value  && countrytext==document.getElementById('countrytext_'+k).value  && txtembsupplier==document.getElementById('txtembsupplier_'+k).value)
				{
					alert("Same Name, Same Type, Same Body Part, Same Country, Same Suppiler, Duplication Not Allowed.");
					document.getElementById('cboembtype_'+id).value=0;
					document.getElementById('cboembbodypart_'+id).value=0;
					//document.getElementById(td).focus();
				}
			}
		}
	}

	//set_session_large_post_data_emb('requires/pre_cost_entry_controller_v3.php?action=consumption_popup_emb', 'Consumtion Entry Form','1')

	function set_session_large_post_data_emb(page_link,title,trorder)
	{
		var cons_breck_downn_emb=document.getElementById('consbreckdownemb_'+trorder).value;
		//var cons_breck_downn_emb=''
		var data="action=save_post_session_emb&cons_breck_downn_emb="+cons_breck_downn_emb;
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4)
			{
				if(http.responseText==1)
				{
				open_consumption_popup_emb(page_link,title,trorder)
				}
			}
		}
	}

	function open_consumption_popup_emb(page_link,title,trorder)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var cbo_approved_status=document.getElementById('cbo_approved_status').value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var garments_nature = document.getElementById('garments_nature').value;
		var tot_set_qnty = document.getElementById('tot_set_qnty').value;
		var precostapproved=document.getElementById('cbo_approved_status').value;
		var consbreckdownemb=document.getElementById('consbreckdownemb_'+trorder).value;
		var embupdateid=document.getElementById('embupdateid_'+trorder).value;
		var item_id= document.getElementById('item_id').value;
		var empbudgeton= document.getElementById('empbudgeton_'+trorder).value;
		var country=document.getElementById('country_'+trorder).value;

		/*if(cbogmtsitem==0 )
		{
			alert("Select Gmts Item");
			return;
		}*/

		var page_link=page_link+'&cbo_company_id='+cbo_company_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_approved_status='+cbo_approved_status+'&txt_job_no='+txt_job_no+'&garments_nature='+garments_nature+'&embupdateid='+embupdateid+'&precostapproved='+precostapproved+'&tot_set_qnty='+tot_set_qnty+'&item_id='+item_id+'&empbudgeton='+empbudgeton+'&country='+country;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			//var trorder= document.getElementById('tr_ortder').value;
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var calculated_cons=this.contentDoc.getElementById("calculated_cons");
			//var tot_plancut_qty=this.contentDoc.getElementById("job_plancut_qty");
			//var calculated_plancutqty=this.contentDoc.getElementById("calculated_plancutqty");

			var calculated_rate=this.contentDoc.getElementById("calculated_rate");
			var calculated_amount=this.contentDoc.getElementById("calculated_amount");

			document.getElementById('txtembconsdzngmts_'+trorder).value=calculated_cons.value;
			document.getElementById('consbreckdownemb_'+trorder).value=cons_breck_down.value;
			document.getElementById('txtembrate_'+trorder).value=calculated_rate.value;
			document.getElementById('txtembamount_'+trorder).value=calculated_amount.value;
			var index=trorder-1;
			var tr=$("#tbl_embellishment_cost tbody tr:eq("+index+")");
			tr.css('background-color', 'green');
			$('#txtembconsdzngmts_'+trorder).css('background-color', 'green');

			set_sum_value( 'txtamountemb_sum', 'txtembamount_','tbl_embellishment_cost');

			/*var row_num=$('#tbl_fabric_cost tr').length-1;
			if(trorder==row_num)
			{
				document.getElementById('check_input').value=0;
				document.getElementById('is_click_cons_box').value=0;
			}*/
		}
	}

	function calculate_emb_cost(i)
	{
		math_operation('txtembamount_'+i,'txtembconsdzngmts_'+i+'*txtembrate_'+i,'*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value});
		set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
	}

	function fnc_embellishment_cost_dtls( operation )
	{
		<?php
		$js_array = json_encode($emblishment_name_array);
		echo "var javascript_type_array = ". $js_array . ";\n";
		?>

	    freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(4);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_embel_pre_cost=$('#txt_embel_pre_cost').attr('pri_embel_pre_cost')*1;
				var txt_embel_pre_cost=$('#txt_embel_pre_cost').val()*1;
				if(txt_embel_pre_cost>pri_embel_pre_cost)
				{
					release_freezing();
					alert('Emblishment cost is greater than Quotation');
					return
				}
			}
		}
		if (form_validation('update_id','Company Name')==false)
		{
			release_freezing();
			return;
		}
	    var row_num=$('#tbl_embellishment_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{			
			for (var k=1;k<=row_num; k++)
			{
				if(k==i)
				{
					continue;
				}
				else
				{
					if(document.getElementById('cboembname_'+i).value==document.getElementById('cboembname_'+k).value && document.getElementById('cboembtype_'+i).value==document.getElementById('cboembtype_'+k).value && document.getElementById('cboembbodypart_'+i).value==document.getElementById('cboembbodypart_'+k).value  && document.getElementById('countrytext_'+i).value==document.getElementById('countrytext_'+k).value  && document.getElementById('txtembsupplier_'+i).value==document.getElementById('txtembsupplier_'+k).value)
					{
						alert("Same Name, Same Type, Same Body Part, Same Country, Same Suppiler, Duplication Not Allowed.");
						release_freezing();
						return;
					}
				}
			}
			//alert("i am here"); release_freezing(); return;
			var cboembname=document.getElementById('cboembname_'+i).value;
			var cboembtype=document.getElementById('cboembtype_'+i).value;
			var txtembconsdzngmts=document.getElementById('txtembconsdzngmts_'+i).value*1;
			
			if(cboembname >0 && txtembconsdzngmts >0 && cboembtype==0){
				alert("Select "+javascript_type_array[cboembname]+ " Type");
				release_freezing();
				return;
			}
			
			data_all+="&cboembname_" + z + "='" + $('#cboembname_'+i).val()+"'"+"&cboembtype_" + z + "='" + $('#cboembtype_'+i).val()+"'"+"&cboembbodypart_" + z + "='" + $('#cboembbodypart_'+i).val()+"'"+"&cboembsupplierid_" + z + "='" + $('#cboembsupplierid_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&txtembconsdzngmts_" + z + "='" + $('#txtembconsdzngmts_'+i).val()+"'"+"&txtembrate_" + z + "='" + $('#txtembrate_'+i).val()+"'"+"&txtembamount_" + z + "='" + $('#txtembamount_'+i).val()+"'"+"&cboembstatus_" + z + "='" + $('#cboembstatus_'+i).val()+"'"+"&embupdateid_" + z + "='" + $('#embupdateid_'+i).val()+"'"+"&consbreckdownemb_" + z + "='" + $('#consbreckdownemb_'+i).val()+"'"+"&empbudgeton_" + z + "='" + $('#empbudgeton_'+i).val()+"'";
			z++;
		}
		//alert(data_all); release_freezing(); return;
		var data="action=save_update_delet_embellishment_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_cost_control_source*txtamountemb_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_embellishment_cost_dtls_reponse;
	}

	function fnc_embellishment_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==10)
			{
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_embellishment_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				//show_msg(reponse[0]);
				//alert(reponse[0]);
				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_embel_pre_cost=$('#txtamountemb_sum').val()*1;
					var pre_emblcost=number_format_common(txt_embel_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_embel_pre_cost").attr('pre_emb_cost',pre_emblcost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_embellishment_cost_listview');
				//document.getElementById('check_open_close_fabric_cost').checked=true;
				//show_content_form('fabric_cost');
				//show_hide_content('embellishment_cost', '')

				//set_button_status(0, permission, 'fnc_embellishment_cost_dtls',7);
				//set_button_status(is_update, permission, submit_func,btn_id)
				//release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					fnc_quotation_entry_dtls1(1)
					release_freezing();
				}
			}
		}
	}
	// Embellisment Cost End -------------------------------------------------

	// Start Wash Cost------------------------------------------------------
	function add_break_down_tr_wash_cost( i,conversion_from_chart )
	{
		var row_num=$('#tbl_wash_cost tr').length-1;
		//alert(row_num)
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_wash_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			  }).end().appendTo("#tbl_wash_cost");

			 // $('#txtembamount_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr_embellishment_cost("+i+");");
			  //$('#embtypetd_'+i).removeAttr("id").attr("id","'fabriccosttbltr_'+i");
			  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_wash_cost("+i+");");
			  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost',this);");
			  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
			  $('#txtembconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data_wash('requires/pre_cost_entry_controller_v3.php?action=consumption_popup_wash', 'Consumtion Entry Form',"+i+")");

			  $('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
			  if(conversion_from_chart==1)
			  {
				  $('#txtembrate_'+i).removeAttr("onClick").attr("onClick","set_wash_charge_unit_pop_up( "+i+" )");
			  }
			  $('#cboembname_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
			  $('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate_wash( "+i+" )");
			  $('#embupdateid_'+i).val("");
			  $('#txtembconsdzngmts_'+i).val("");
			  $('#txtembrate_'+i).val("");
			  $('#txtembamount_'+i).val("");
			  $('#embratelibid_'+i).val("");
			  $('#cboembtype_'+i).val(0);
			  $('#consbreckdownwash_'+i).val("");
			  //set_sum_value( 'txtconsdznemb_sum', 'txtembconsdzngmts_', 'tbl_embellishment_cost' );
			  //set_sum_value( 'txtrateemb_sum', 'txtembrate_', 'tbl_embellishment_cost' );
			  set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
			  calculate_main_total();
		}
	}

	function check_duplicate_wash(id)
	{
		var cboembname=(document.getElementById('cboembname_'+id).value);
		var cboembtype=(document.getElementById('cboembtype_'+id).value);
		var row_num=$('#tbl_wash_cost tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(cboembname==document.getElementById('cboembname_'+k).value && cboembtype==document.getElementById('cboembtype_'+k).value)
				{
					alert("Same Name, Same Type  Duplication Not Allowed.");
					document.getElementById('cboembtype_'+id).value=0;
					//document.getElementById(td).focus();
				}
			}
		}
	}

	function set_session_large_post_data_wash(page_link,title,trorder)
	{
		var cons_breck_downn_wash=document.getElementById('consbreckdownwash_'+trorder).value;
		//var cons_breck_downn_emb=''
		var data="action=save_post_session_wash&cons_breck_downn_wash="+cons_breck_downn_wash;
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
			if(http.readyState == 4)
			{
				if(http.responseText==1)
				{
					open_consumption_popup_wash(page_link,title,trorder)
				}
			}
		}
	}

	function open_consumption_popup_wash(page_link,title,trorder)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var txt_exchange_rate=document.getElementById('txt_exchange_rate').value;
		var cbo_currercy=document.getElementById('cbo_currercy').value;
		var cbo_approved_status=document.getElementById('cbo_approved_status').value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var garments_nature = document.getElementById('garments_nature').value;
		var tot_set_qnty = document.getElementById('tot_set_qnty').value;
		var precostapproved=document.getElementById('cbo_approved_status').value;
		var consbreckdownwash=document.getElementById('consbreckdownwash_'+trorder).value;
		var embupdateid=document.getElementById('embupdateid_'+trorder).value;
		var item_id= document.getElementById('item_id').value;
		var empbudgeton= document.getElementById('empbudgeton_'+trorder).value;
		var country=document.getElementById('country_'+trorder).value;

		var page_link=page_link+'&cbo_company_id='+cbo_company_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_approved_status='+cbo_approved_status+'&txt_job_no='+txt_job_no+'&garments_nature='+garments_nature+'&embupdateid='+embupdateid+'&precostapproved='+precostapproved+'&tot_set_qnty='+tot_set_qnty+'&consbreckdownwash='+consbreckdownwash+'&item_id='+item_id+'&empbudgeton='+empbudgeton+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_currercy='+cbo_currercy+'&country='+country;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var calculated_cons=this.contentDoc.getElementById("calculated_cons");

			var calculated_rate=this.contentDoc.getElementById("calculated_rate");
			var calculated_amount=this.contentDoc.getElementById("calculated_amount");

			document.getElementById('txtembconsdzngmts_'+trorder).value=calculated_cons.value;
			document.getElementById('consbreckdownwash_'+trorder).value=cons_breck_down.value;
			document.getElementById('txtembrate_'+trorder).value=calculated_rate.value;
			document.getElementById('txtembamount_'+trorder).value=calculated_amount.value;
			var index=trorder-1;
			var tr=$("#tbl_embellishment_cost tbody tr:eq("+index+")");
			tr.css('background-color', 'green');
			$('#txtembconsdzngmts_'+trorder).css('background-color', 'green');
			set_sum_value( 'txtamountemb_sum', 'txtembamount_','tbl_wash_cost');
		}
	}

	function calculate_wash_cost(i)
	{
		math_operation( 'txtembamount_'+i, 'txtembconsdzngmts_'+i+'*txtembrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
	}

	function fnc_wash_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(5);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_wash_pre_cost=$('#txt_wash_pre_cost').attr('pri_wash_pre_cost')*1;
				var txt_wash_pre_cost=$('#txt_wash_pre_cost').val()*1;
				if(txt_wash_pre_cost>pri_wash_pre_cost)
				{
					alert('Wash cost is greater than Quotation');
					release_freezing();
					return
				}
			}
		}

		var row_num=$('#tbl_wash_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
			}
			//var cboembname=document.getElementById('cboembname_'+i).value;
			var cboembtype=document.getElementById('cboembtype_'+i).value;
			//var txtembconsdzngmts=document.getElementById('txtembconsdzngmts_'+i).value*1;

			if(cboembtype==0){
				alert("Select Type");
				release_freezing();
				return;
			}

			data_all+="&cboembname_" + z + "='" + $('#cboembname_'+i).val()+"'"+"&cboembtype_" + z + "='" + $('#cboembtype_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&txtembconsdzngmts_" + z + "='" + $('#txtembconsdzngmts_'+i).val()+"'"+"&txtembrate_" + z + "='" + $('#txtembrate_'+i).val()+"'"+"&txtembamount_" + z + "='" + $('#txtembamount_'+i).val()+"'"+"&cboembstatus_" + z + "='" + $('#cboembstatus_'+i).val()+"'"+"&embupdateid_" + z + "='" + $('#embupdateid_'+i).val()+"'"+"&embratelibid_" + z + "='" + $('#embratelibid_'+i).val()+"'"+"&consbreckdownwash_" + z + "='" + $('#consbreckdownwash_'+i).val()+"'"+"&empbudgeton_" + z + "='" + $('#empbudgeton_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_wash_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_cost_control_source*txtamountemb_sum',"../../")+data_all;
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_wash_cost_dtls_reponse;
	}

	function fnc_wash_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_wash_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_wash_pre_cost=$('#txtamountemb_sum').val()*1;
					var pre_washcost=number_format_common(txt_wash_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_wash_pre_cost").attr('pre_wash_cost',pre_washcost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_wash_cost_listview');
				if(reponse[0]==0 || reponse[0]==1)
				{
					fnc_quotation_entry_dtls1( 1 )
					release_freezing();
				}
			}
		}
	}
	// End Wash Cost------------------------------------------------------
	// Commision Cost---------------------------------------------------------
	function add_break_down_tr_commission_cost( i )
	{
		var row_num=$('#tbl_commission_cost tr').length-1;
		if (i==0)
		{
			i=1;
			 $("#txtconstruction_"+i).autocomplete({
				 source: str_construction
			  });
			   $("#txtcomposition_"+i).autocomplete({
				 source:  str_composition
			  });
			  return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_commission_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			  }).end().appendTo("#tbl_commission_cost");
			  $('#increasecommission_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_commission_cost("+i+");");
			  $('#decreasecommission_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_commission_cost',this);");
			  $('#txtcommissionrate_'+i).removeAttr("onChange").attr("onChange","calculate_commission_cost( "+i+" )");
			  $('#cbocommissionbase_'+i).removeAttr("onChange").attr("onChange","calculate_commission_cost( "+i+" )");
			  $('#commissionupdateid_'+i).val("");
			  set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
			  set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
		}
	}

	function calculate_commission_cost(i)
	{
		var commission_base=document.getElementById('cbocommissionbase_'+i).value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var txtcommissionrate=(document.getElementById('txtcommissionrate_'+i).value)*1;
		var amount=0;
		if(commission_base==1)
		{
			//var total_cost=(document.getElementById('txt_total_pre_cost').value);
			var total_cost=(document.getElementById('txt_final_price_dzn_pre_cost').value);

			//var txtcommissionrate_percent=txtcommissionrate/100;
			//alert(txtcommissionrate_percent);
			// var amount=(total_cost/(1-txtcommissionrate_percent))-total_cost;
			var amount=(txtcommissionrate*total_cost)/100;
		}
		if(commission_base==2)
		{
			if(cbo_costing_per==1) var amount=txtcommissionrate*12*1;
			if(cbo_costing_per==2) var amount=txtcommissionrate*1;
			if(cbo_costing_per==3) var amount=txtcommissionrate*12*2;
			if(cbo_costing_per==4) var amount=txtcommissionrate*12*3;
			if(cbo_costing_per==5) var amount=txtcommissionrate*12*4;
		}
		if(commission_base==3)
		{
			if(cbo_costing_per==1) var amount=txtcommissionrate*1*1;
			if(cbo_costing_per==2) var amount=txtcommissionrate/12;
			if(cbo_costing_per==3) var amount=txtcommissionrate*1*2;
			if(cbo_costing_per==4) var amount=txtcommissionrate*1*3;
			if(cbo_costing_per==5) var amount=txtcommissionrate*1*4;
		}
		document.getElementById('txtcommissionamount_'+i).value=number_format_common(amount,1,0,document.getElementById('cbo_currercy').value);

		//math_operation( 'txtembamount_'+i, 'txtembconsdzngmts_'+i+'*txtembrate_'+i, '*','' );
		set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
		set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
		//calculate_depreciation_amortization();
		//calculate_oparating_expanseses();
		//calculate_deffd_lc();
		//calculate_interest_cost();
		//calculate_income_tax();
		//set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
	}

	function fnc_commission_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(6);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_commission_pre_cost=$('#txt_commission_pre_cost').attr('pri_commission_pre_cost')*1;
				var txt_commission_pre_cost=$('#txt_commission_pre_cost').val()*1;
				if(txt_commission_pre_cost>pri_commission_pre_cost)
				{
				alert('Commision cost is greater than Quotation');
				release_freezing();
				return
				}
			}
		}

		var row_num=$('#tbl_commission_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
			}
			data_all+="&cboparticulars_" + z + "='" + $('#cboparticulars_'+i).val()+"'"+"&cbocommissionbase_" + z + "='" + $('#cbocommissionbase_'+i).val()+"'"+"&txtcommissionrate_" + z + "='" + $('#txtcommissionrate_'+i).val()+"'"+"&txtcommissionamount_" + z + "='" + $('#txtcommissionamount_'+i).val()+"'"+"&cbocommissionstatus_" + z + "='" + $('#cbocommissionstatus_'+i).val()+"'"+"&commissionupdateid_" + z + "='" + $('#commissionupdateid_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_commission_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*copy_quatation_id*txt_quotation_id*txt_cost_control_source*txtratecommission_sum*txtamountcommission_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_commission_cost_dtls_reponse;
	}

	function fnc_commission_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}

			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_commission_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value*1;
				if (reponse[0].length>2) reponse[0]=10;

				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_commission_pre_cost=$('#txtamountcommission_sum').val()*1;
					var pre_commiscost=number_format_common(txt_commission_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_commission_pre_cost").attr('pre_commis_cost',pre_commiscost);

					calculate_main_total();
				}
				//show_msg(reponse[0]);
				//alert(reponse[0]);
				show_sub_form(document.getElementById('update_id').value, 'show_commission_cost_listview');
				//document.getElementById('check_open_close_fabric_cost').checked=true;
				//show_content_form('fabric_cost');
				//show_hide_content('commission_cost', '')

				//set_button_status(0, permission, 'fnc_embellishment_cost_dtls',7);
				//set_button_status(is_update, permission, submit_func,btn_id)
				//release_freezing();
				//alert(reponse[0])
				if(reponse[0]==0 || reponse[0]==1)
				{

					var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					//alert(reponse[3])
					document.getElementById('txt_comml_pre_cost').value=reponse[3];
					document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
					
					set_currier_cost_method_variable(company_name);
					calculate_main_total();
					fnc_quotation_entry_dtls1(1)
					release_freezing();
				}
			}
		}
	}
	// Commision Cost End ---------------------------------------------------------
// Commarcial Cost-------------------------------------------------------------
	function add_break_down_tr_comarcial_cost( i )
	{
		var row_num=$('#tbl_comarcial_cost tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_comarcial_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_comarcial_cost");
			$('#increasecomarcial_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_comarcial_cost("+i+");");
			$('#decreasecomarcial_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_comarcial_cost',this);");
			$('#txtcomarcialrate_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'cal_amount' )");
			$('#txtcomarcialamount_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'cal_rate')");
			$('#comarcialupdateid_'+i).val("");
			set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
			set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
		}
	}

	function calculate_comarcial_cost(i,type)
	{
		var txt_commercial_cost_method=document.getElementById('txt_commercial_cost_method').value;
		var update_id=document.getElementById('update_id').value;
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var txtcomarcialrate=(document.getElementById('txtcomarcialrate_'+i).value)*1;
		var txtcomarcialamount=document.getElementById('txtcomarcialamount_'+i).value
		var amount=0;
		//var total_cost=(document.getElementById('txt_final_price_dzn_pre_cost').value);
		if(txt_commercial_cost_method==1 || txt_commercial_cost_method==4 || txt_commercial_cost_method==5 || txt_commercial_cost_method==6 || txt_commercial_cost_method==7)
		{
			var sum_comarcial_cost=return_global_ajax_value(update_id+'_'+txt_commercial_cost_method, 'sum_comarcial_cost_value', '', 'requires/pre_cost_entry_controller_v3');
			var amount=number_format_common(sum_comarcial_cost, 1, 0, currency);
		}

		if(txt_commercial_cost_method==2)
		{
			var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value;
			var amount=number_format_common(txt_final_price_dzn_pre_cost, 1, 0, currency);
		}
		if(txt_commercial_cost_method==3)
		{
			var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value*1;
			var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
			var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
			var amount=number_format_common(fob_value, 1, 0, currency);
		}
		//alert(amount);

		if(type=='cal_amount')
		{
			var com_amount=amount*(txtcomarcialrate/100);
			document.getElementById('txtcomarcialamount_'+i).value=number_format_common(com_amount, 1, 0, currency);
		}
		if(type=='cal_rate')
		{
			var com_rate=(txtcomarcialamount*100)/amount;
			document.getElementById('txtcomarcialrate_'+i).value=number_format_common(com_rate, 1, 0, currency);
		}
		set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
		set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
		calculate_main_total();
	}

	function fnc_comarcial_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		
		var row_num=$('#tbl_comarcial_cost tr').length-1;
		for(var i=1; i<=row_num; i++){
			calculate_comarcial_cost(i,'cal_amount');
		}
		
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(7);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_comml_pre_cost=$('#txt_comml_pre_cost').attr('pri_comml_pre_cost')*1;
				var txt_comml_pre_cost=$('#txt_comml_pre_cost').val()*1;
				if(txt_comml_pre_cost>pri_comml_pre_cost)
				{
					alert('Commercial cost is greater than Quotation');
					release_freezing();
					return
				}
			}
		}
		
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
			}
			
			data_all+="&cboitem_" + z + "='" + $('#cboitem_'+i).val()+"'"+"&txtcomarcialrate_" + z + "='" + $('#txtcomarcialrate_'+i).val()+"'"+"&txtcomarcialamount_" + z + "='" + $('#txtcomarcialamount_'+i).val()+"'"+"&cbocomarcialstatus_" + z + "='" + $('#cbocomarcialstatus_'+i).val()+"'"+"&comarcialupdateid_" + z + "='" + $('#comarcialupdateid_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_comarcial_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*copy_quatation_id*txt_quotation_id*txt_cost_control_source*txtratecomarcial_sum*txtamountcomarcial_sum',"../../")+data_all;

		http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_comarcial_cost_dtls_reponse;
	}

	function fnc_comarcial_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}

			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_comarcial_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_comml_pre_cost=$('#txtamountcomarcial_sum').val()*1;
					var pre_commlcost=number_format_common(txt_comml_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_comml_pre_cost").attr('pre_comml_cost',pre_commlcost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_comarcial_cost_listview');

				if(reponse[0]==0 || reponse[0]==1)
				{
					fnc_quotation_entry_dtls1(1)
					release_freezing();
				}
			}
		}
	}

	function fnc_quotation_entry_dtls1( operation )
	{
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		/*var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}*/
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(1);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var pri_trim_pre_cost=$('#txt_trim_pre_cost').attr('pri_trim_pre_cost')*1;
				var pri_embel_pre_cost=$('#txt_embel_pre_cost').attr('pri_embel_pre_cost')*1;
				var pri_wash_pre_cost=$('#txt_wash_pre_cost').attr('pri_wash_pre_cost')*1;
				var pri_comml_pre_cost=$('#txt_comml_pre_cost').attr('pri_comml_pre_cost')*1;
				var pri_commission_pre_cost=$('#txt_commission_pre_cost').attr('pri_commission_pre_cost')*1;

				var pri_lab_test_pre_cost=$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost')*1;
				var pri_inspection_pre_cost=$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost')*1;
				var pri_cm_pre_cost=$('#txt_cm_pre_cost').attr('pri_cm_pre_cost')*1;
				var pri_freight_pre_cost=$('#txt_freight_pre_cost').attr('pri_freight_pre_cost')*1;
				var pri_currier_pre_cost=$('#txt_currier_pre_cost').attr('pri_currier_pre_cost')*1;
				var pri_certificate_pre_cost=$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost')*1;
				var pri_common_oh_pre_cost=$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost')*1;
				var pri_depr_amor_pre_cost=$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost')*1;
				var pri_total_pre_cost=$('#txt_total_pre_cost').attr('pri_total_pre_cost')*1;

				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				var txt_trim_pre_cost=$('#txt_trim_pre_cost').val()*1;
				var txt_embel_pre_cost=$('#txt_embel_pre_cost').val()*1;
				var txt_wash_pre_cost=$('#txt_wash_pre_cost').val()*1;
				var txt_comml_pre_cost=$('#txt_comml_pre_cost').val()*1;
				var txt_commission_pre_cost=$('#txt_commission_pre_cost').val()*1;

				var txt_lab_test_pre_cost=$('#txt_lab_test_pre_cost').val()*1;
				var txt_inspection_pre_cost=$('#txt_inspection_pre_cost').val()*1;
				var txt_cm_pre_cost=$('#txt_cm_pre_cost').val()*1;
				var txt_freight_pre_cost=$('#txt_freight_pre_cost').val()*1;
				var txt_currier_pre_cost=$('#txt_currier_pre_cost').val()*1;
				var txt_certificate_pre_cost=$('#txt_certificate_pre_cost').val()*1;
				var txt_common_oh_pre_cost=$('#txt_common_oh_pre_cost').val()*1;
				var txt_depr_amor_pre_cost=$('#txt_depr_amor_pre_cost').val()*1;
				var txt_total_pre_cost=$('#txt_total_pre_cost').val()*1;

				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					alert('Fabric cost ('+txt_fabric_pre_cost+') is greater than Quotation ('+pri_fabric_pre_cost+')');
					return;
				}
				if(txt_trim_pre_cost>pri_trim_pre_cost)
				{
					alert('Trims cost ('+txt_trim_pre_cost+') is greater than Quotation ('+pri_trim_pre_cost+')');
					return;
				}
				if(txt_embel_pre_cost>pri_embel_pre_cost)
				{
					alert('Emblishment cost ('+txt_embel_pre_cost+') is greater than Quotation ('+pri_embel_pre_cost+')');
					return;
				}
				if(txt_wash_pre_cost>pri_wash_pre_cost)
				{
					alert('Wash cost ('+txt_wash_pre_cost+') is greater than Quotation ('+pri_wash_pre_cost+')');
					return;
				}
				if(txt_comml_pre_cost>pri_comml_pre_cost)
				{
					alert('Comarcial cost ('+txt_comml_pre_cost+') is greater than Quotation ('+pri_comml_pre_cost+')');
					return;
				}
				if(txt_commission_pre_cost>pri_commission_pre_cost)
				{
					alert('Commission cost ('+txt_commission_pre_cost+') is greater than Quotation ('+pri_commission_pre_cost+')');
					return;
				}
				if(txt_lab_test_pre_cost>pri_lab_test_pre_cost)
				{
					alert("Labtest Cost ("+txt_lab_test_pre_cost+") is greater than Quotation ("+pri_lab_test_pre_cost+")");
					return;
				}
				if(txt_inspection_pre_cost>pri_inspection_pre_cost)
				{
					alert("Inspection Cost ("+txt_inspection_pre_cost+") is greater than Quotation ("+pri_inspection_pre_cost+")");
					return;
				}
				if(txt_cm_pre_cost>pri_cm_pre_cost)
				{
					alert("CM Cost ("+txt_cm_pre_cost+") is greater than Quotation ("+pri_cm_pre_cost+")");
					return;
				}
				if(txt_freight_pre_cost>pri_freight_pre_cost)
				{
					alert("Freight Cost ("+txt_freight_pre_cost+") is greater than Quotation ("+pri_freight_pre_cost+")");
					return;
				}
				if(txt_currier_pre_cost>pri_currier_pre_cost)
				{
					alert("Currier Cost ("+txt_currier_pre_cost+") is greater than Quotation ("+pri_currier_pre_cost+")");
					return;
				}
				if(txt_certificate_pre_cost>pri_certificate_pre_cost)
				{
					alert("Certificate Cost ("+txt_certificate_pre_cost+") is greater than Quotation ("+pri_certificate_pre_cost+")");
					return;
				}
				if(txt_common_oh_pre_cost>pri_common_oh_pre_cost)
				{
					alert("Operating Expenses Cost ("+txt_common_oh_pre_cost+") is greater than Quotation ("+pri_common_oh_pre_cost+")");
					return;
				}
				if(txt_depr_amor_pre_cost>pri_depr_amor_pre_cost)
				{
					alert("Depreciation & Amortization Cost ("+txt_depr_amor_pre_cost+") is greater than Quotation ("+pri_depr_amor_pre_cost+")");
					return;
				}
				var pri_total_with_commi_cost=number_format(pri_total_pre_cost+pri_commission_pre_cost,4,'.','');
				if(txt_total_pre_cost>pri_total_with_commi_cost)
				{
					if(number_format(txt_total_pre_cost,4,'.','')>pri_total_with_commi_cost)
					{
						alert("Total Cost ("+txt_total_pre_cost+") is greater than Quotation ("+pri_total_with_commi_cost+")");
						return;
					}
					
				}
			}
		}

		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*txt_cost_control_source*cbo_costing_per*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_total_pre_cost*txt_total_po_price*txt_final_price_dzn_pre_cost*txt_final_price_dzn_po_price*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_total_pre_cost_psc_set*txt_total_pre_cost_psc_set_po_price*txt_final_price_pcs_pre_cost*txt_final_price_pcs_po_price*txt_margin_pcs_pre_cost*txt_margin_pcs_po_price*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_deffdlc_pre_cost*txt_deffdlc_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_incometax_pre_cost*txt_incometax_po_price*txt_design_pre_cost*txt_design_po_price*txt_studio_pre_cost*txt_studio_po_price',"../../");
			//alert(data); return;
			http.onreadystatechange = function() {
				if( http.readyState == 4 && http.status == 200 ) {
					var reponse=trim(http.responseText).split('**');
					//alert(reponse) release_freezing(); return;
					if(trim(reponse[0])=='quataNotApp')
					{
						alert("Quotation is not Approved. Please Approved the Quotation");
						release_freezing();
						return;
					}
					if(trim(reponse[0])=='qcNotApp')
					{
						alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
						release_freezing();
						return;
					}
					if(trim(reponse[0])==10)
					{
						release_freezing();
						return;
					}
					if (trim(reponse[0]).length>2) reponse[0]=10;
					show_msg(trim(reponse[0]));
					$('#hidd_is_dtls_open').val(1);
					document.getElementById('update_id_dtls').value  = reponse[2];
					set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				}
			}
			http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		}
	}
// Commarcial Cost End -------------------------------------------------------------

	//Master form---------------------------------------------------------------------------
	function openmypage(page_link,title)
	{
		hide_left_menu("Button1");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1230px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			if(title=="Job/Order Selection Form")
			{
				var action="populate_data_from_job_table";
			}
			//alert(action);
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
			//alert(theform);
			if (theemail.value!="")
			{
				freeze_window(5);
				$("#hidd_is_dtls_open").val(1);
				reset_form('precosting_1*quotationdtls_2', 'cost_container','','cbo_currercy,2*cbo_approved_status,2*is_click_cons_box,1*txt_incoterm_place,Chittagong','','txt_costing_date*txt_exchange_rate*cbo_costing_per');//cbo_costing_per,1*
				//reset_form('quotationdtls_2','','');
				get_php_form_data( theemail.value, action, "requires/pre_cost_entry_controller_v3" );
				get_php_form_data( theemail.value, 'check_data_mismass', "requires/pre_cost_entry_controller_v3");
				
				$('#cbo_company_name').attr('disabled','true');
				$('#cbo_buyer_name').attr('disabled','true');
				$('#cbo_agent').attr('disabled','true');
				$('#cbo_region').attr('disabled','true');
				$('#cbo_order_uom').attr('disabled','true');
				//company_id
				//set_button_status(1, permission, 'fnc_quotation_entry',1);
				//set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				var cm_cost_method = $('#cm_cost_predefined_method_id').attr('lib_variable_data');
				var txt_precost_id=document.getElementById("txt_precost_id").value;
				var cm_cost_method_data = cm_cost_method.split("___");
				if(cm_cost_method_data[2]==1)
				{
					$('#sew_td').css('color','blue');
				}
				if(txt_precost_id=='')
				{
					set_field_level_access(document.getElementById("cbo_company_name").value); 
				}
				
				check_exchange_rate();
				release_freezing();
			}
		}
	}
	function mo_sheet_popup(page_link,title)
	{
		var job_id= $('#hidd_job_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&job_id='+job_id, title, 'width=1230px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var fabric_cons_breakdown=this.contentDoc.getElementById("fabric_cons_breakdown");
			var trim_cons_breakdown=this.contentDoc.getElementById("trims_cons_breakdown");
			//var action="mo_sheet4_report";
			var data="action=mo_sheet4_report&job_id="+job_id+
				'&fabric_cons_breakdown='+"'"+fabric_cons_breakdown.value+"'"+
				'&trims_cons_breakdown='+"'"+trim_cons_breakdown.value+"'"+
				'&path=../../../';
				console.log(data);
				http.open("POST","requires/pre_cost_entry_controller_v3.php",true);

				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function()
				{
					var file_data=http.responseText.split("****");
					if(http.readyState == 4)
					{
						/* var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close(); */
						$('#mo_sheet_4').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
						document.getElementById('mo_sheet_4').click();
						release_freezing();
				   	}
				}
		}
	}

	function open_set_popup(unit_id)
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var tot_smv_qnty=document.getElementById('txt_sew_smv').value;

		var page_link="requires/pre_cost_entry_controller_v3.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&tot_smv_qnty='+tot_smv_qnty;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=460px,height=250px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			//var set_breck_down=this.contentDoc.getElementById("set_breck_down") //Access form field with id="emailfield"
			//var item_id=this.contentDoc.getElementById("item_id") //Access form field with id="emailfield"
			//var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty") //Access form field with id="emailfield"
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			//document.getElementById('set_breck_down').value=set_breck_down.value;
			//document.getElementById('item_id').value=item_id.value;
			document.getElementById('txt_sew_smv').value=tot_smv_qnty.value;
			//get_php_form_data( cbo_company_name+"**"+txt_job_no+"**"+tot_smv_qnty.value, 'get_efficiency_percent', "requires/pre_cost_entry_controller" );
			calculate_cm_cost_with_method();
		}
	}

	function fnc_precosting_entry( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(1);
			//alert(qc_validate);
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var pri_trim_pre_cost=$('#txt_trim_pre_cost').attr('pri_trim_pre_cost')*1;
				var pri_embel_pre_cost=$('#txt_embel_pre_cost').attr('pri_embel_pre_cost')*1;
				var pri_wash_pre_cost=$('#txt_wash_pre_cost').attr('pri_wash_pre_cost')*1;
				var pri_comml_pre_cost=$('#txt_comml_pre_cost').attr('pri_comml_pre_cost')*1;
				var pri_commission_pre_cost=$('#txt_commission_pre_cost').attr('pri_commission_pre_cost')*1;

				var pri_lab_test_pre_cost=$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost')*1;
				var pri_inspection_pre_cost=$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost')*1;
				var pri_cm_pre_cost=$('#txt_cm_pre_cost').attr('pri_cm_pre_cost')*1;
				var pri_freight_pre_cost=$('#txt_freight_pre_cost').attr('pri_freight_pre_cost')*1;
				var pri_currier_pre_cost=$('#txt_currier_pre_cost').attr('pri_currier_pre_cost')*1;
				var pri_certificate_pre_cost=$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost')*1;
				var pri_common_oh_pre_cost=$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost')*1;
				var pri_depr_amor_pre_cost=$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost')*1;
				var pri_total_pre_cost=$('#txt_total_pre_cost').attr('pri_total_pre_cost')*1;

				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				var txt_trim_pre_cost=$('#txt_trim_pre_cost').val()*1;
				var txt_embel_pre_cost=$('#txt_embel_pre_cost').val()*1;
				var txt_wash_pre_cost=$('#txt_wash_pre_cost').val()*1;
				var txt_comml_pre_cost=$('#txt_comml_pre_cost').val()*1;
				var txt_commission_pre_cost=$('#txt_commission_pre_cost').val()*1;

				var txt_lab_test_pre_cost=$('#txt_lab_test_pre_cost').val()*1;
				var txt_inspection_pre_cost=$('#txt_inspection_pre_cost').val()*1;
				var txt_cm_pre_cost=$('#txt_cm_pre_cost').val()*1;
				var txt_freight_pre_cost=$('#txt_freight_pre_cost').val()*1;
				var txt_currier_pre_cost=$('#txt_currier_pre_cost').val()*1;
				var txt_certificate_pre_cost=$('#txt_certificate_pre_cost').val()*1;
				var txt_common_oh_pre_cost=$('#txt_common_oh_pre_cost').val()*1;
				var txt_depr_amor_pre_cost=$('#txt_depr_amor_pre_cost').val()*1;
				var txt_total_pre_cost=$('#txt_total_pre_cost').val()*1;
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					release_freezing();
					alert('Fabric cost ('+txt_fabric_pre_cost+') is greater than Quotation ('+pri_fabric_pre_cost+')');
					return;
				}
				if(txt_trim_pre_cost>pri_trim_pre_cost)
				{
					release_freezing();
					alert('Trims cost ('+txt_trim_pre_cost+') is greater than Quotation ('+pri_trim_pre_cost+')');
					return;
				}
				if(txt_embel_pre_cost>pri_embel_pre_cost)
				{
					release_freezing();
					alert('Emblishment Cost ('+txt_embel_pre_cost+') is greater than Quotation ('+pri_embel_pre_cost+')');
					return;
				}
				if(txt_wash_pre_cost>pri_wash_pre_cost)
				{
					release_freezing();
					alert('Wash cost ('+txt_wash_pre_cost+') is greater than Quotation ('+pri_wash_pre_cost+')');
					return;
				}
				if(txt_comml_pre_cost>pri_comml_pre_cost)
				{
					release_freezing();
					alert('Comarcial cost ('+txt_comml_pre_cost+') is greater than Quotation ('+pri_comml_pre_cost+')');
					return;
				}
				if(txt_commission_pre_cost>pri_commission_pre_cost)
				{
					release_freezing();
					alert('Commission cost ('+txt_commission_pre_cost+') is greater than Quotation ('+pri_commission_pre_cost+')');
					return;
				}
				if(txt_lab_test_pre_cost>pri_lab_test_pre_cost)
				{
					release_freezing();
					alert("Labtest Cost ("+txt_lab_test_pre_cost+") is greater than Quotation ("+pri_lab_test_pre_cost+")");
					return;
				}
				if(txt_inspection_pre_cost>pri_inspection_pre_cost)
				{
					release_freezing();
					alert("Inspection Cost ("+txt_inspection_pre_cost+") is greater than Quotation ("+pri_inspection_pre_cost+")");
					return;
				}
				if(txt_cm_pre_cost>pri_cm_pre_cost)
				{
					release_freezing();
					alert("CM Cost ("+txt_cm_pre_cost+") is greater than Quotation ("+pri_cm_pre_cost+")");
					return;
				}
				if(txt_freight_pre_cost>pri_freight_pre_cost)
				{
					release_freezing();
					alert("Freight Cost ("+txt_freight_pre_cost+") is greater than Quotation ("+pri_freight_pre_cost+")");
					return;
				}
				if(txt_currier_pre_cost>pri_currier_pre_cost)
				{
					release_freezing();
					alert("Currier Cost ("+txt_currier_pre_cost+") is greater than Quotation ("+pri_currier_pre_cost+")");
					return;
				}
				if(txt_certificate_pre_cost>pri_certificate_pre_cost)
				{
					release_freezing();
					alert("Certificate Cost ("+txt_certificate_pre_cost+") is greater than Quotation ("+pri_certificate_pre_cost+")");
					return;
				}
				if(txt_common_oh_pre_cost>pri_common_oh_pre_cost)
				{
					release_freezing();
					alert("Operating Expenses Cost ("+txt_common_oh_pre_cost+") is greater than Quotation ("+pri_common_oh_pre_cost+")");
					return;
				}
				if(txt_depr_amor_pre_cost>pri_depr_amor_pre_cost)
				{
					release_freezing();
					alert("Depreciation & Amortization Cost ("+txt_depr_amor_pre_cost+") is greater than Quotation ("+pri_depr_amor_pre_cost+")");
					return;
				}
				var pri_total_with_commi_cost=number_format(pri_total_pre_cost+pri_commission_pre_cost,4,'.','');

				//if(txt_total_pre_cost>(pri_total_pre_cost+pri_commission_pre_cost))
				if(txt_total_pre_cost>pri_total_with_commi_cost)
				{
					if(number_format(txt_total_pre_cost,4,'.','')>pri_total_with_commi_cost)
					{
						release_freezing();
						alert("Total Cost ("+txt_total_pre_cost+") is greater than Quotation ("+pri_total_with_commi_cost+")")
						return;
					}
					
				}
			}
		}

		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;
		//alert(user_level); release_freezing(); return;
		if(($('#cbo_ready_to_approved').val()*1)==1 && buyer_profit_per>0)
		{
			var margin_pcs_bom_per=$('#txt_margin_pcs_bom_per').val()*1;
			if(buyer_profit_per>margin_pcs_bom_per)
			{
				alert("Buyer Min Budgeted Profit % is greater-than BOM Margin/Pcs %");
				if(user_level!='2')
				{
					release_freezing();
					return;
				}
			}
		}
		if(($('#cbo_ready_to_approved').val()*1)==1)
		{
			fnc_margin_as_bom();
		}

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!='2')
				{
					release_freezing();
					return;
				}
			}
		}

		if(operation==1)
		{
			if(($('#txt_sew_efficiency_source').val()*1)!=1)
			{
				get_php_form_data( $("#cbo_company_name").val()+"**"+$("#txt_job_no").val()+"**"+$("#txt_sew_smv").val(), 'get_efficiency_percent', "requires/pre_cost_entry_controller_v3" );
				calculate_cm_cost_with_method();
			}
		}
		//alert(document.getElementById('cm_cost_predefined_method_id').value);
		if(document.getElementById('cm_cost_predefined_method_id').value>0)
		{
			if (form_validation('txt_sew_efficiency_per','Efficiency %')==false)
			{
				release_freezing();
				return;
				
			}
		}

		if(document.getElementById('cm_cost_predefined_method_id').value==1)
		{
			var sew_efficiency_per=document.getElementById('txt_sew_efficiency_per').value;
			if(sew_efficiency_per=="")
			{
				alert("Insert Sew Efficiency %");
				release_freezing();
				return;
			}
		}
		
		var cm_cost_method = $('#cm_cost_predefined_method_id').attr('lib_variable_data');
		var cm_cost_method_data = cm_cost_method.split("___");
		
		if(cm_cost_method_data[2]==1)
		{
			var sew_efficiency_per=document.getElementById('txt_sew_efficiency_per').value*1;
			if(sew_efficiency_per=="" || sew_efficiency_per==0)
			{
				alert("Insert Sew Efficiency %");
				release_freezing();
				return;
			}
		}
		
		if (form_validation('txt_job_no*txt_costing_date*cbo_inco_term*txt_incoterm_place*cbo_costing_per','Job No*Costing Date*Incoterm*Incoterm Palce*Costing Per')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('garments_nature*txt_job_no*hidd_job_id*txt_cost_control_source*txt_costing_date*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_remarks*update_id*update_id*copy_quatation_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_ready_to_approved*txt_budget_minute*cbo_company_name*txt_quotation_id*txt_sew_efficiency_source*txt_cost_control_source*pre_cost_id*txt_fabric_pre_cost*txt_refusing',"../../");

			http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_precosting_entry_reponse;
		}
	}

	function fnc_precosting_entry_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='file')
			{
				alert("Please upload file");
				release_freezing();
				return;
			}
			if(reponse[0]=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}

			if(reponse[0]=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}

			if(reponse[0]=='quickcostingtag')
			{
				alert("Please tag this BOM with Quick Costing. Than apply for approval.");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(reponse[0]=='FabApp')
			{
				alert("Fabric Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}
			if(reponse[0]=='YarApp')
			{
				alert("Yarn Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='ConApp')
			{
				alert("Conversion Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='TriApp')
			{
				alert("Trims Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='EmbApp')
			{
				alert("Emblishment Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}
			if(reponse[0]=='WasApp')
			{
				alert("Wash Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='ComnApp')
			{
				alert("Commision Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='CommApp')
			{
				alert("Commercial Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='LabApp')
			{
				alert("Lab test Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='InspApp')
			{
				alert("Inspection Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='CmApp')
			{
				alert("CM Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}
			if(reponse[0]=='FreApp')
			{
				alert("Freight Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='CurrApp')
			{
				alert("Currier Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='CertApp')
			{
				alert("Certificate Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='CoohApp')
			{
				alert("Opert. Exp. Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}
			if(reponse[0]=='DeprApp')
			{
				alert("Depc. & Amort. Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}
			if(reponse[0]=='InterApp')
			{
				alert("Interest Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if(reponse[0]=='IncomApp')
			{
				alert("Income Tax Cost ("+reponse[3]+") Missmatch with the Quotation ("+reponse[2]+")");
				release_freezing();
				return;
			}

			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			document.getElementById('update_id').value  = reponse[2];
			$("#hidd_is_dtls_open").val(1);
			if(reponse[3]==1)
			{
				document.getElementById('cbo_approved_status').value = '2';
				document.getElementById('approve1').value = 'Approved';
				document.getElementById('txt_costing_date').disabled=false;
				document.getElementById('cbo_inco_term').disabled=false;
				document.getElementById('txt_incoterm_place').disabled=false;
				document.getElementById('txt_machine_line').disabled=false;
				document.getElementById('txt_prod_line_hr').disabled=false;
				document.getElementById('cbo_costing_per').disabled=false;
				document.getElementById('txt_remarks').disabled=false;
				document.getElementById('save1').disabled=false;
				document.getElementById('update1').disabled=false;
				document.getElementById('Delete1').disabled=false;
				//===================
				document.getElementById('txt_lab_test_pre_cost').disabled=false;
				document.getElementById('txt_inspection_pre_cost').disabled=false;
				document.getElementById('txt_cm_pre_cost').disabled=false;
				document.getElementById('txt_freight_pre_cost').disabled=false;
				document.getElementById('txt_common_oh_pre_cost').disabled=false;
				document.getElementById('save2').disabled=false;
				document.getElementById('update2').disabled=false;
				document.getElementById('Delete2').disabled=false;
			}
			if(reponse[3]==2)
			{
				document.getElementById('cbo_approved_status').value = '1';
				document.getElementById('approve1').value = 'Un-Approved';
				document.getElementById('txt_costing_date').disabled=true;
				document.getElementById('cbo_inco_term').disabled=true;
				document.getElementById('txt_incoterm_place').disabled=true;
				document.getElementById('txt_machine_line').disabled=true;
				document.getElementById('txt_prod_line_hr').disabled=true;
				document.getElementById('cbo_costing_per').disabled=true;
				document.getElementById('txt_remarks').disabled=true;
				document.getElementById('save1').disabled=true;
				document.getElementById('update1').disabled=true;
				document.getElementById('Delete1').disabled=true;

				document.getElementById('txt_lab_test_pre_cost').disabled=true;
				document.getElementById('txt_inspection_pre_cost').disabled=true;

				var cm_cost_editable= $("#cm_cost_editable").val();
				if(cm_cost_editable==1)
				{
					document.getElementById('txt_cm_pre_cost').disabled=false;
				}
				else
				{
					document.getElementById('txt_cm_pre_cost').disabled=true;
				}
				document.getElementById('txt_freight_pre_cost').disabled=true;
				document.getElementById('txt_common_oh_pre_cost').disabled=true;
				document.getElementById('save2').disabled=true;
				document.getElementById('update2').disabled=true;
				document.getElementById('Delete2').disabled=true;
			 }
			//show_list_view(reponse[1],'company_list_view','company_list_view','../cost_center/requires/company_details_controller','setFilterGrid("list_view",-1)');
			//reset_form('companydetailsform_1','','');
			set_button_status(1, permission, 'fnc_precosting_entry',1);
			//release_freezing();
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2 || reponse[0]==3){
				fnc_quotation_entry_dtls1( reponse[0] )
				reset_form('','cost_container','')
				release_freezing();
			}
			if(reponse[0]==3 && reponse[3]==2){
				document.getElementById('app_sms').innerHTML = 'This Job Is Approved'
			}
			if(reponse[0]==3 && reponse[3]==1){
				document.getElementById('app_sms').innerHTML = ''
			}
			release_freezing();
		}
	}

	function cm_cost_predefined_method(cm_cost_data)
	{
		//alert(cm_cost_data)
		var ex_cm_data=cm_cost_data.split('___');
		var cm_cost_method=ex_cm_data[0];
		var cm_cost_editable=ex_cm_data[1];
		//var cm_cost_method=return_global_ajax_value(company_id, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v3');
		//alert(ex_cm_data);
		if(cm_cost_method ==0)
		{
			if($("#txt_quotation_id").val()!="")
			{
				if(cm_cost_editable==1)
				{
					$("#txt_cm_pre_cost").attr("disabled",false);
				}
				else
				{
					$("#txt_cm_pre_cost").attr("disabled",true);
				}
			}
			else
			{
				document.getElementById('txt_cm_pre_cost').disabled=false;
			}
			$("#txt_sew_smv").attr("disabled",true);
			//$("#txt_sew_efficiency_per").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
		}
		else if(cm_cost_method ==1)
		{
			//document.getElementById('txt_cm_pre_cost').disabled=true;
			$("#txt_cm_pre_cost").attr("disabled",true);
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
		}
		else if(cm_cost_method ==2)
		{
			document.getElementById('txt_cm_pre_cost').disabled=true;
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",false);
			$("#txt_cut_efficiency_per").attr("disabled",false);
		}
		else if(cm_cost_method ==3)
		{
			document.getElementById('txt_cm_pre_cost').disabled=true;
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",true);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
		}
		else if(cm_cost_method ==4)
		{
			document.getElementById('txt_cm_pre_cost').disabled=true;
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
		}

		if(cm_cost_editable==1)
		{
			$("#txt_cm_pre_cost").attr("disabled",false);
		}
		else
		{
			$("#txt_cm_pre_cost").attr("disabled",true);
		}
		
		if(ex_cm_data[2]==1)
		{
			$('#sew_td').css('color','blue');
		}

		document.getElementById('cm_cost_predefined_method_id').value=cm_cost_method;
		document.getElementById('cm_cost_editable').value=cm_cost_editable;
	}

	//Master Form End-----------------------------------------------------

	//Dtls Form 1--------------------------------------------------------
	function calculate_cm_cost_with_method()
	{
		//1. CM Cost = SMV*CPM+ (SMV*CPM)* Efficiency Wastage%
		//2. CM Cost= CU(SMV*CPM/ Efficiency %)+ SF(SMV*CPM/ Efficiency %)
		//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
		var cm_cost=0;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		//var cm_cost_predefined_method_id=(document.getElementById('cm_cost_predefined_method_id').value)*1;
		var txt_sew_smv=parseFloat(document.getElementById('txt_sew_smv').value);
		var txt_cut_smv=parseFloat(document.getElementById('txt_cut_smv').value);
		var txt_sew_efficiency_per=parseFloat(document.getElementById('txt_sew_efficiency_per').value);
		var txt_cut_efficiency_per=parseFloat(document.getElementById('txt_cut_efficiency_per').value);
		//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);

		var cbo_currercy= document.getElementById('cbo_currercy').value;
		var txt_exchange_rate= document.getElementById('txt_exchange_rate').value*1;
		var txt_machine_line= document.getElementById('txt_machine_line').value;
		var txt_prod_line_hr= document.getElementById('txt_prod_line_hr').value;
		var cbo_costing_per= document.getElementById('cbo_costing_per').value;
		var txt_costing_date= document.getElementById('txt_costing_date').value;
		var txt_job_no= document.getElementById('txt_job_no').value;
		if(txt_costing_date=="")
		{
			alert("Costing Date");
			return;
		}
		var cbo_costing_per_value=0
		if(cbo_costing_per==1) cbo_costing_per_value=12;
		if(cbo_costing_per==2) cbo_costing_per_value=1;
		if(cbo_costing_per==3) cbo_costing_per_value=24;
		if(cbo_costing_per==4) cbo_costing_per_value=36;
		if(cbo_costing_per==5) cbo_costing_per_value=48;

		//var cpm=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v3');
		var cpm=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date+'_'+txt_job_no, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v3');
		var cm_cost_method = $('#cm_cost_predefined_method_id').attr('lib_variable_data');//return_global_ajax_value(cbo_company_name, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v3');//$('#cm_cost_predefined_method_id').attr('lib_variable_data');
		var cm_cost_method_data = cm_cost_method.split("___");
		var cm_cost_predefined_method_id = cm_cost_method_data[0];
		var data=cpm.split("_");
		//alert(cm_cost_method);
		if(cm_cost_predefined_method_id==1)
		{
			if(data[3]==0 || data[3]=="" )
			{
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			var txt_efficiency_wastage=100-txt_sew_efficiency_per;
			document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
			var cm_cost=(txt_sew_smv*data[3]*cbo_costing_per_value)+((txt_sew_smv*data[3]*cbo_costing_per_value)*(txt_efficiency_wastage/100));
			//alert(txt_exchange_rate)
			cm_cost=cm_cost/txt_exchange_rate;
			//alert(cm_cost+'='+txt_sew_smv+'*'+data[3]+'*'+cbo_costing_per_value+'+'+txt_sew_smv+'*'+data[3]+'*'+cbo_costing_per_value+'*'+txt_efficiency_wastage+'/'+100)
		}
		if(cm_cost_predefined_method_id==2)
		{
			if(data[3]==0 ||data[3]=="" )
			{
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			//alert(data[3])
			var cut_per=txt_cut_efficiency_per/100;
			var sew_per=txt_sew_efficiency_per/100;
			var cu=(txt_cut_smv*trim(data[3])*cbo_costing_per_value)/cut_per
			if(isNaN(cu))
			{
				cu=0;
			}

			var su=(txt_sew_smv*trim(data[3])*cbo_costing_per_value)/sew_per
			//alert("("+txt_sew_smv+"*"+data[3]+"*"+cbo_costing_per_value+")/"+sew_per)
			if(isNaN(su))
			{
				su=0;
			}
			var cm_cost=(cu+su)/txt_exchange_rate;
		}
		
		if(cm_cost_predefined_method_id>0 || cm_cost_method_data[2]==1)
		{
			$('#sew_td').css('color','blue');
			if (form_validation('txt_sew_efficiency_per','Efficiency %')==false)
			{
				return;
			}
		}
		else{
			//alert(cm_cost_predefined_method_id);
			$('#sew_td').css('color','black');
		}
		
		

		if(cm_cost_predefined_method_id==3)
		{
			//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
			//alert("EX"+txt_exchange_rate);
			if(cbo_currercy==0 || cbo_currercy=="")
			{
				alert("Insert Currency");
				return;
			}
			if(txt_exchange_rate==0 || txt_exchange_rate=="")
			{
				alert("Insert Exchange Rate");
				return;
			}
			if(txt_machine_line==0 || txt_machine_line=="")
			{
				alert("Insert Machine/Line");
				return;
			}
			if(txt_prod_line_hr==0 || txt_prod_line_hr=="")
			{
				alert("Insert Prod/Line/Hr");
				return;
			}
			if(cbo_costing_per==0 || cbo_costing_per=="")
			{
				alert("Insert Costing Per");
				return;
			}
			//alert(cm_cost_predefined_method_id)
			if(data[0]==0)
			{
				alert("Insert Monthly CM Expense in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			if(data[1]==0)
			{
				alert("Insert No. of Factory Machine  in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			if(data[2]==0)
			{
				alert("Insert Working Hour in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			var per_day_cost=data[0]/26;
			var per_machine_cost=per_day_cost/data[1];
			var per_line_cost=per_machine_cost*txt_machine_line;
			var total_production_per_line=txt_prod_line_hr*data[2];
			var per_product_cost=per_line_cost/total_production_per_line;

			if(cbo_costing_per==1) var cm_cost=(per_product_cost*12)/txt_exchange_rate;
			if(cbo_costing_per==2) var cm_cost=(per_product_cost*1)/txt_exchange_rate;
			if(cbo_costing_per==3) var cm_cost=(per_product_cost*24)/txt_exchange_rate;
			if(cbo_costing_per==4) var cm_cost=(per_product_cost*36)/txt_exchange_rate;
			if(cbo_costing_per==5) var cm_cost=(per_product_cost*48)/txt_exchange_rate;
			//var su=(txt_sew_smv*cpm)/txt_sew_efficiency_per
			//var cm_cost=per_product_cost*;
			//number_format_common(final_cost_psc, 1, 0, currency);
		}
		if(cm_cost_predefined_method_id==4)
		{
			if(data[3]==0 ||data[3]=="" )
			{
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			var sew_per=txt_sew_efficiency_per/100;
			var su=((trim(data[3])/sew_per)*txt_sew_smv*cbo_costing_per_value);
			cm_cost=su/txt_exchange_rate;
		}
		if(cm_cost!=Infinity && cm_cost_predefined_method_id>0)
		{
			document.getElementById('txt_cm_pre_cost').value=number_format_common(cm_cost,1,0,cbo_currercy)	;
			calculate_main_total();
		}
	}

	function calculate_main_total()
	{
		var currency=$("#cbo_currercy").val()*1;
		var dblTot_fa=($("#txt_fabric_pre_cost").val()*1)+($("#txt_trim_pre_cost").val()*1)+($("#txt_embel_pre_cost").val()*1)+($("#txt_wash_pre_cost").val()*1)+($("#txt_comml_pre_cost").val()*1)+($("#txt_commission_pre_cost").val()*1)+($("#txt_lab_test_pre_cost").val()*1)+($("#txt_inspection_pre_cost").val()*1)+($("#txt_cm_pre_cost").val()*1)+($("#txt_freight_pre_cost").val()*1)+($("#txt_currier_pre_cost").val()*1)+($("#txt_certificate_pre_cost").val()*1)+($("#txt_common_oh_pre_cost").val()*1)+($("#txt_depr_amor_pre_cost").val()*1)+($("#txt_incometax_pre_cost").val()*1)+($("#txt_interest_pre_cost").val()*1)+($("#txt_deffdlc_pre_cost").val()*1)+($("#txt_design_pre_cost").val()*1)+($("#txt_studio_pre_cost").val()*1);

		$("#txt_total_pre_cost").val( number_format_common(dblTot_fa, 1, 0, currency) );

		//alert(dblTot_fa);
		calculate_total_cost_psc_set();
		clculate_margin_dzn();
		calculate_margin_pcs_set();
		calculate_percent_on_po_price();
	}

	function calculate_total_cost_psc_set()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var txt_total_pre_cost=document.getElementById('txt_total_pre_cost').value;
		var tatal_cost_pcs=0;
		if(cbo_costing_per==1) tatal_cost_pcs=txt_total_pre_cost/12;
		if(cbo_costing_per==2) tatal_cost_pcs=txt_total_pre_cost/1;
		if(cbo_costing_per==3) tatal_cost_pcs=txt_total_pre_cost/(2*12);
		if(cbo_costing_per==4) tatal_cost_pcs=txt_total_pre_cost/(3*12);
		if(cbo_costing_per==5) tatal_cost_pcs=txt_total_pre_cost/(4*12);
		document.getElementById('txt_total_pre_cost_psc_set').value=number_format_common(tatal_cost_pcs,2,0,currency);
	}

	function clculate_margin_dzn()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var txt_final_price_dzn_pre_cost=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
		var txt_total_pre_cost=(document.getElementById('txt_total_pre_cost').value)*1;
		var txt_margin_dzn_pre_cost=txt_final_price_dzn_pre_cost-txt_total_pre_cost;
		document.getElementById('txt_margin_dzn_pre_cost').value=number_format_common(txt_margin_dzn_pre_cost, 1, 0, currency);
		if(txt_margin_dzn_pre_cost<0) document.getElementById('txt_margin_dzn_pre_cost').style.backgroundColor='#F00';
		else document.getElementById('txt_margin_dzn_pre_cost').style.backgroundColor='';
	}

	function calculate_margin_pcs_set()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var txt_margin_dzn_pre_cost=(document.getElementById('txt_margin_dzn_pre_cost').value)*1
		var margin_psc_set=0;
		if(cbo_costing_per==1) margin_psc_set=txt_margin_dzn_pre_cost/12;
		if(cbo_costing_per==2) margin_psc_set=txt_margin_dzn_pre_cost/1;
		if(cbo_costing_per==3) margin_psc_set=txt_margin_dzn_pre_cost/(2*12);
		if(cbo_costing_per==4) margin_psc_set=txt_margin_dzn_pre_cost/(3*12);
		if(cbo_costing_per==5) margin_psc_set=txt_margin_dzn_pre_cost/(4*12);
		document.getElementById('txt_margin_pcs_pre_cost').value=number_format_common(margin_psc_set, 1, 0, currency);
		if(margin_psc_set<0) document.getElementById('txt_margin_pcs_pre_cost').style.backgroundColor='#F00';
		else document.getElementById('txt_margin_pcs_pre_cost').style.backgroundColor='';
	}

	function calculate_percent_on_po_price()
	{
		var txt_confirm_price_pre_cost_dzn=$("#txt_final_price_dzn_pre_cost").val()*1;
		var txt_final_price_pcs_pre_cost=$("#txt_final_price_pcs_pre_cost").val()*1;

		var txt_fabric_po_price=(($("#txt_fabric_pre_cost").val()*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_trim_po_price=(((document.getElementById('txt_trim_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_embel_po_price=(((document.getElementById('txt_embel_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_wash_po_price=(((document.getElementById('txt_wash_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;

		var txt_comml_po_price=(((document.getElementById('txt_comml_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_commission_po_price=(((document.getElementById('txt_commission_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_lab_test_po_price=(((document.getElementById('txt_lab_test_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_inspection_po_price=(((document.getElementById('txt_inspection_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;

		var txt_cm_po_price=(((document.getElementById('txt_cm_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_freight_po_price=(((document.getElementById('txt_freight_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_currier_po_price=(((document.getElementById('txt_currier_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_certificate_po_price=(((document.getElementById('txt_certificate_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;


		var txt_common_oh_po_price=(((document.getElementById('txt_common_oh_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_depr_amor_po_price=(((document.getElementById('txt_depr_amor_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;

		var txt_interest_po_price=(((document.getElementById('txt_interest_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_incometax_po_price=(((document.getElementById('txt_incometax_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;

		var txt_deffdlc_po_price=(((document.getElementById('txt_deffdlc_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_design_pre_cost=(((document.getElementById('txt_design_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_studio_pre_cost=(((document.getElementById('txt_studio_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;

		var txt_total_po_price=(((document.getElementById('txt_total_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		var txt_final_price_dzn_po_price=(txt_confirm_price_pre_cost_dzn/txt_confirm_price_pre_cost_dzn)*100;
		var txt_margin_dzn_po_price=(((document.getElementById('txt_margin_dzn_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;

		var txt_final_price_pcs_po_price=(((document.getElementById('txt_final_price_pcs_pre_cost').value)*1)/txt_final_price_pcs_pre_cost)*100;
		var txt_total_pre_cost_psc_set_po_price=(((document.getElementById('txt_total_pre_cost_psc_set').value)*1)/txt_final_price_pcs_pre_cost)*100;
		var txt_margin_pcs_po_price=(($("#txt_margin_pcs_pre_cost").val()*1)/txt_final_price_pcs_pre_cost)*100;

		document.getElementById('txt_fabric_po_price').value=number_format_common(txt_fabric_po_price, 7, 0);
		document.getElementById('txt_trim_po_price').value=number_format_common(txt_trim_po_price, 7, 0);
		document.getElementById('txt_embel_po_price').value=number_format_common(txt_embel_po_price, 7, 0);
		document.getElementById('txt_wash_po_price').value=number_format_common(txt_wash_po_price, 7, 0);

		document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
		document.getElementById('txt_commission_po_price').value=number_format_common(txt_commission_po_price, 7, 0);
		document.getElementById('txt_lab_test_po_price').value=number_format_common(txt_lab_test_po_price, 7, 0);
		document.getElementById('txt_inspection_po_price').value=number_format_common(txt_inspection_po_price, 7, 0);

		document.getElementById('txt_cm_po_price').value=number_format_common(txt_cm_po_price, 7, 0);
		document.getElementById('txt_freight_po_price').value=number_format_common(txt_freight_po_price, 7, 0);
		document.getElementById('txt_currier_po_price').value=number_format_common(txt_currier_po_price, 7, 0);
		document.getElementById('txt_certificate_po_price').value=number_format_common(txt_certificate_po_price, 7, 0);

		document.getElementById('txt_common_oh_po_price').value=number_format_common(txt_common_oh_po_price, 7, 0);
		document.getElementById('txt_depr_amor_po_price').value=number_format_common(txt_depr_amor_po_price, 7, 0);
		document.getElementById('txt_interest_po_price').value=number_format_common(txt_interest_po_price, 7, 0);
		document.getElementById('txt_incometax_po_price').value=number_format_common(txt_incometax_po_price, 7, 0);

		document.getElementById('txt_deffdlc_po_price').value=number_format_common(txt_deffdlc_po_price, 7, 0);
		document.getElementById('txt_design_po_price').value=number_format_common(txt_design_pre_cost, 7, 0);
		document.getElementById('txt_studio_po_price').value=number_format_common(txt_studio_pre_cost, 7, 0);


		document.getElementById('txt_total_po_price').value=number_format_common(txt_total_po_price, 7, 0);
		document.getElementById('txt_final_price_dzn_po_price').value=number_format_common(txt_final_price_dzn_po_price, 7, 0);
		document.getElementById('txt_margin_dzn_po_price').value=number_format_common(txt_margin_dzn_po_price, 7, 0);

		document.getElementById('txt_total_pre_cost_psc_set_po_price').value=number_format_common(txt_total_pre_cost_psc_set_po_price, 7, 0);
		document.getElementById('txt_final_price_pcs_po_price').value=number_format_common(txt_final_price_pcs_po_price, 7, 0);
		document.getElementById('txt_margin_pcs_po_price').value=number_format_common(txt_margin_pcs_po_price, 7, 0);
	}

	function calculate_confirm_price_dzn(type)
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var txt_final_price_pcs_pre_cost=(document.getElementById('txt_final_price_pcs_pre_cost').value)*1;
		if(cbo_costing_per==1) document.getElementById('txt_final_price_dzn_pre_cost').value=number_format_common((txt_final_price_pcs_pre_cost*12), 1, 0,currency);
		if(cbo_costing_per==2) document.getElementById('txt_final_price_dzn_pre_cost').value=number_format_common((txt_final_price_pcs_pre_cost*1), 1, 0,currency);
		if(cbo_costing_per==3) document.getElementById('txt_final_price_dzn_pre_cost').value=number_format_common((txt_final_price_pcs_pre_cost*12*2), 1, 0,currency);
		if(cbo_costing_per==4) document.getElementById('txt_final_price_dzn_pre_cost').value=number_format_common((txt_final_price_pcs_pre_cost*12*3), 1, 0,currency);
		if(cbo_costing_per==5) document.getElementById('txt_final_price_dzn_pre_cost').value=number_format_common((txt_final_price_pcs_pre_cost*12*4), 1, 0,currency);

		clculate_margin_dzn();
		calculate_margin_pcs_set();
		calculate_percent_on_po_price();
		if(type!=1)
		{
			calculate_depreciation_amortization();
			calculate_oparating_expanseses();
			calculate_deffd_lc();
			calculate_interest_cost();
			calculate_income_tax();
		}
	}

	function calculate_depreciation_amortization()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var txt_costing_date= document.getElementById('txt_costing_date').value;

		var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value*1;
		var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var depreciation_amortization_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v3');
		var depreciation_amortization_per=document.getElementById('cost_per_minute').value;
		var data=depreciation_amortization_per.split('_');
		var data_value=data[4];
		if(data_value=="") data_value=0;
		// alert(fob_value);
		depreciation_amortization_value=(fob_value*data_value)/100;
		if(number_format_common(depreciation_amortization_value,1,0,currency)>0)
		{
			document.getElementById('txt_depr_amor_pre_cost').readOnly=true
		}
		else
		{
			document.getElementById('txt_depr_amor_pre_cost').readOnly=false
		}
		document.getElementById('txt_depr_amor_pre_cost').value=number_format_common(depreciation_amortization_value,1,0,currency)
		//alert(fob_value);
		calculate_main_total();
	}

	function calculate_oparating_expanseses()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var txt_costing_date=document.getElementById('txt_costing_date').value;

		var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value*1;
		var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var oparating_expanses_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v3');
		var oparating_expanses_per=document.getElementById('cost_per_minute').value;
		var data=oparating_expanses_per.split('_');
		var data_value=data[5];
		//alert(data_value)
		if(data_value=="") data_value=0;
		// alert(fob_value);
		oparating_expanses_value=(fob_value*data_value)/100;
		if(number_format_common(oparating_expanses_value,1,0,currency)>0)
		{
			document.getElementById('txt_common_oh_pre_cost').readOnly=true
		}
		else
		{
			document.getElementById('txt_common_oh_pre_cost').readOnly=false
		}
		document.getElementById('txt_common_oh_pre_cost').value=number_format_common(oparating_expanses_value,1,0,currency)
		calculate_main_total()
	}

	function check_booking(){
		var response=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_booking_found', 'requires/pre_cost_entry_controller_v3');
		response=response.split("_");
	}

	function change_cost_per(costing_per)
	{
		var is_used_costing_per=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_used_costing_per', 'requires/pre_cost_entry_controller_v3');
		var data=is_used_costing_per.split("_");
		if(data[0]>0)
		{
			//alert("Costing Found, So Costing Per Change will delete all details part costing");
			var r=confirm("Costing Found, So Costing Per Change will delete all details part costing");
			if(r==false)
			{
				document.getElementById('cbo_costing_per').value=data[1];
				change_caption_cost_dtls( data[1], 'change_caption_dzn' );
				return;
			}
			else
			{
				var response=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_booking_found', 'requires/pre_cost_entry_controller_v3');
				response=response.split("_");
				if(response[0]>0){
					alert(response[1]+" Found, So delete costing not allowed");
					document.getElementById('cbo_costing_per').value=data[1];
					change_caption_cost_dtls( data[1], 'change_caption_dzn' );
					return;
				}
				else{
					var r=confirm("Are You Sure to delete all details part costing\n. After Delete It will not be possible to get data back");
					if(r==false)
					{
						document.getElementById('cbo_costing_per').value=data[1];
						change_caption_cost_dtls( data[1], 'change_caption_dzn' );
						return;
					}
					else{
						/*var response_d=0//return_ajax_request_value(document.getElementById('txt_job_no').value, 'delete_costing', 'requires/pre_cost_entry_controller_v3');
						if(response_d==0){*/
						var r=confirm("Costing has been Deleted,\n If You do not want to delete yet please press Cancel\n Or press ok to delete Permanently");
						if(r==false){
							//var response_a=return_ajax_request_value(document.getElementById('txt_job_no').value, 'active_costing', 'requires/pre_cost_entry_controller_v3');
							alert("Costing has got Backed");
							document.getElementById('cbo_costing_per').value=data[1];
							change_caption_cost_dtls( data[1], 'change_caption_dzn' );
							return;
						}
						else{
							var response_dp=return_ajax_request_value(document.getElementById('txt_job_no').value, 'delete_costing_permanently', 'requires/pre_cost_entry_controller_v3');
							get_php_form_data( document.getElementById('txt_job_no').value, 'populate_data_from_job_table', "requires/pre_cost_entry_controller_v3" );
							get_php_form_data( document.getElementById('txt_job_no').value, 'check_data_mismass', "requires/pre_cost_entry_controller_v3" );
							alert("Costing has been Deleted Permanently");
							change_caption_cost_dtls( costing_per, 'change_caption_dzn' );
						}
					}
				}
			}
		}
		else{
			change_caption_cost_dtls( costing_per, 'change_caption_dzn' );
		}
	}

	function change_caption_cost_dtls( value, type )
	{
		if(type=="change_caption_dzn")
		{
			if(value==1)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 1 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/1 Dzn";
			}
			if(value==2)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 1 Pcs";
				document.getElementById('margin_dzn').innerHTML="Margin/ 1 Pcs";
			}
			if(value==3)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 2 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 2 Dzn";
			}
			if(value==4)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 3 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 3 Dzn";
			}
			if(value==5)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 4 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 4 Dzn";
			}
			calculate_confirm_price_dzn();
		}
		if(type=="change_caption_pcs")
		{
			if(value==1)
			{
				document.getElementById('price_pcs_td').innerHTML="Price/Pcs  ";
				document.getElementById('margin_pcs_td').innerHTML="Margin/pcs ";
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/Pcs ";
				document.getElementById('margin_bom_td').innerHTML="BOM Margin/Pcs";
			}
			if(value==58)
			{
				document.getElementById('price_pcs_td').innerHTML="Price/Set ";
				document.getElementById('margin_pcs_td').innerHTML="Margin/Set";
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/Set ";
				document.getElementById('margin_bom_td').innerHTML="BOM Margin/Set";
			}
		}
	}

	function fnc_quotation_entry_dtls( operation )
	{
		freeze_window(operation);

		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;
		var cm_pre_cost= $('#txt_cm_pre_cost').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}
		var check_is_master_part_saved=return_global_ajax_value(document.getElementById('update_id').value, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v3');
		if(trim(check_is_master_part_saved)=="")
		{
			release_freezing();
			alert ("Save Master Part")	;
			return;
		}
		var mendatory_field =''; var mendatory_message ='';
		var cm_cost_method=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v3');
		var cm_method = cm_cost_method.split("___");
		var cm_cost_compulsory = cm_method[2];
		//alert(cm_cost_compulsory);
		if(cm_cost_compulsory == 1)
		{
			mendatory_field = "*txt_cm_pre_cost";
			mendatory_message = "*CM Cost";
			if(cm_pre_cost=="" || cm_pre_cost==0)
			{
				release_freezing();
				alert('Insert CM Cost');
				return;

			}
		}

		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==6)
		{
			var qc_validate=fnc_budgete_cost_validate(1);
			//alert(qc_validate);
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var pri_trim_pre_cost=$('#txt_trim_pre_cost').attr('pri_trim_pre_cost')*1;
				var pri_embel_pre_cost=$('#txt_embel_pre_cost').attr('pri_embel_pre_cost')*1;
				var pri_wash_pre_cost=$('#txt_wash_pre_cost').attr('pri_wash_pre_cost')*1;
				var pri_comml_pre_cost=$('#txt_comml_pre_cost').attr('pri_comml_pre_cost')*1;
				var pri_commission_pre_cost=$('#txt_commission_pre_cost').attr('pri_commission_pre_cost')*1;

				var pri_lab_test_pre_cost=$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost')*1;
				var pri_inspection_pre_cost=$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost')*1;
				var pri_cm_pre_cost=$('#txt_cm_pre_cost').attr('pri_cm_pre_cost')*1;
				var pri_freight_pre_cost=$('#txt_freight_pre_cost').attr('pri_freight_pre_cost')*1;
				var pri_currier_pre_cost=$('#txt_currier_pre_cost').attr('pri_currier_pre_cost')*1;
				var pri_certificate_pre_cost=$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost')*1;
				var pri_common_oh_pre_cost=$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost')*1;
				var pri_depr_amor_pre_cost=$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost')*1;
				var pri_total_pre_cost=$('#txt_total_pre_cost').attr('pri_total_pre_cost')*1;

				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				var txt_trim_pre_cost=$('#txt_trim_pre_cost').val()*1;
				var txt_embel_pre_cost=$('#txt_embel_pre_cost').val()*1;
				var txt_wash_pre_cost=$('#txt_wash_pre_cost').val()*1;
				var txt_comml_pre_cost=$('#txt_comml_pre_cost').val()*1;
				var txt_commission_pre_cost=$('#txt_commission_pre_cost').val()*1;

				var txt_lab_test_pre_cost=$('#txt_lab_test_pre_cost').val()*1;
				var txt_inspection_pre_cost=$('#txt_inspection_pre_cost').val()*1;
				var txt_cm_pre_cost=$('#txt_cm_pre_cost').val()*1;
				var txt_freight_pre_cost=$('#txt_freight_pre_cost').val()*1;
				var txt_currier_pre_cost=$('#txt_currier_pre_cost').val()*1;
				var txt_certificate_pre_cost=$('#txt_certificate_pre_cost').val()*1;
				var txt_common_oh_pre_cost=$('#txt_common_oh_pre_cost').val()*1;
				var txt_depr_amor_pre_cost=$('#txt_depr_amor_pre_cost').val()*1;
				var txt_total_pre_cost=$('#txt_total_pre_cost').val()*1;
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					release_freezing();
					alert('Fabric cost ('+txt_fabric_pre_cost+') is greater than Quotation ('+pri_fabric_pre_cost+')');
					return;
				}
				if(txt_trim_pre_cost>pri_trim_pre_cost)
				{
					release_freezing();
					alert('Trims cost ('+txt_trim_pre_cost+') is greater than Quotation ('+pri_trim_pre_cost+')');
					return;
				}
				if(txt_embel_pre_cost>pri_embel_pre_cost)
				{
					release_freezing();
					alert('Emblishment Cost ('+txt_embel_pre_cost+') is greater than Quotation ('+pri_embel_pre_cost+')');
					return;
				}
				if(txt_wash_pre_cost>pri_wash_pre_cost)
				{
					release_freezing();
					alert('Wash cost ('+txt_wash_pre_cost+') is greater than Quotation ('+pri_wash_pre_cost+')');
					return;
				}
				if(txt_comml_pre_cost>pri_comml_pre_cost)
				{
					release_freezing();
					alert('Comarcial cost ('+txt_comml_pre_cost+') is greater than Quotation ('+pri_comml_pre_cost+')');
					return;
				}
				if(txt_commission_pre_cost>pri_commission_pre_cost)
				{
					release_freezing();
					alert('Commission cost ('+txt_commission_pre_cost+') is greater than Quotation ('+pri_commission_pre_cost+')');
					return;
				}
				if(txt_lab_test_pre_cost>pri_lab_test_pre_cost)
				{
					release_freezing();
					alert("Labtest Cost ("+txt_lab_test_pre_cost+") is greater than Quotation ("+pri_lab_test_pre_cost+")");
					return;
				}
				if(txt_inspection_pre_cost>pri_inspection_pre_cost)
				{
					release_freezing();
					alert("Inspection Cost ("+txt_inspection_pre_cost+") is greater than Quotation ("+pri_inspection_pre_cost+")");
					return;
				}
				if(txt_cm_pre_cost>pri_cm_pre_cost)
				{
					release_freezing();
					alert("CM Cost ("+txt_cm_pre_cost+") is greater than Quotation ("+pri_cm_pre_cost+")");
					return;
				}
				if(txt_freight_pre_cost>pri_freight_pre_cost)
				{
					release_freezing();
					alert("Freight Cost ("+txt_freight_pre_cost+") is greater than Quotation ("+pri_freight_pre_cost+")");
					return;
				}
				if(txt_currier_pre_cost>pri_currier_pre_cost)
				{
					release_freezing();
					alert("Currier Cost ("+txt_currier_pre_cost+") is greater than Quotation ("+pri_currier_pre_cost+")");
					return;
				}
				if(txt_certificate_pre_cost>pri_certificate_pre_cost)
				{
					release_freezing();
					alert("Certificate Cost ("+txt_certificate_pre_cost+") is greater than Quotation ("+pri_certificate_pre_cost+")");
					return;
				}
				if(txt_common_oh_pre_cost>pri_common_oh_pre_cost)
				{
					release_freezing();
					alert("Operating Expenses Cost ("+txt_common_oh_pre_cost+") is greater than Quotation ("+pri_common_oh_pre_cost+")");
					return;
				}
				if(txt_depr_amor_pre_cost>pri_depr_amor_pre_cost)
				{
					release_freezing();
					alert("Depreciation & Amortization Cost ("+txt_depr_amor_pre_cost+") is greater than Quotation ("+pri_depr_amor_pre_cost+")");
					return;
				}
				var pri_total_with_commi_cost=number_format(pri_total_pre_cost+pri_commission_pre_cost,4,'.','');
				//if(txt_total_pre_cost>(pri_total_pre_cost+pri_commission_pre_cost))
				if(txt_total_pre_cost>pri_total_with_commi_cost)
				{
					release_freezing();
					alert("Total Cost ("+txt_total_pre_cost+") is greater than Quotation ("+pri_total_with_commi_cost+")");
					return;
				}
			}
		}

		if (form_validation('cbo_company_name'+mendatory_field,'Company Name'+mendatory_message)==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*cbo_costing_per*txt_cost_control_source*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_total_pre_cost*txt_total_po_price*txt_final_price_dzn_pre_cost*txt_final_price_dzn_po_price*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_total_pre_cost_psc_set*txt_total_pre_cost_psc_set_po_price*txt_final_price_pcs_pre_cost*txt_final_price_pcs_po_price*txt_margin_pcs_pre_cost*txt_margin_pcs_po_price*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_deffdlc_pre_cost*txt_deffdlc_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_incometax_pre_cost*txt_incometax_po_price*txt_design_pre_cost*txt_design_po_price*txt_studio_pre_cost*txt_studio_po_price',"../../");

			http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_quotation_entry_dtls_reponse;
		}
	}

	function fnc_quotation_entry_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation.");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='qcNotApp')
			{
				alert("Short Quotation V3 is not Approved. Please Approved the Short Quotation V3");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved'){
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}

			if(trim(reponse[0])==10)
			{
				release_freezing();
				return;
			}
			if (trim(reponse[0]).length>2) reponse[0]=10;
			if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
				show_msg(reponse[0]);
				$('#hidd_is_dtls_open').val(1);
				document.getElementById('update_id_dtls').value  = trim(reponse[0]);
				set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				release_freezing();
			}
		}
	}
	//Dtls Form 1 End --------------------------------------------------------
	//report code here-------------------------------------------------------
	//created by Bilas-------------------------------------------------------

	function generate_report(type)
	{
		if (form_validation('txt_job_no','Please Select The Job Number.')==false)
		{
			return;
		}
		else
		{
			freeze_window(3);
			fnc_margin_as_bom();
			if(type=="summary" || type=="budget3_details" || type=="budget_4")
			{
				if(type=='summary')
				{
					var rpt_type=3;var comments_head=0;
				}
				else if(type=='budget3_details')
				{
					var rpt_type=4;var comments_head=1;
				}
				else if(type=='budget_4')
				{
					var rpt_type=7; comments_head=1;
				}

				var report_title="Budget/Cost Sheet";
				//	var comments_head=0;
				var cbo_company_name=$('#cbo_company_name').val();
				var cbo_buyer_name=$('#cbo_buyer_name').val();
				var txt_style_ref=$('#txt_style_ref').val();
				var txt_style_ref_id=$('#hidd_job_id').val();
				var txt_quotation_id=$('#txt_quotation_id').val();
				var sign=0;
				//var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_style_ref_id*txt_order*txt_order_id*txt_season*txt_season_id*txt_file_no*txt_quotation_id*txt_hidden_quot_id',"../../../")+'&comments_head='+comments_head+'&report_title='+report_title;
				var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
				var data="action=report_generate&reporttype="+rpt_type+
				'&cbo_company_name='+"'"+cbo_company_name+"'"+
				'&cbo_buyer_name='+"'"+cbo_buyer_name+"'"+
				'&txt_style_ref='+"'"+txt_style_ref+"'"+
				'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
				'&txt_order='+"'"+txt_order+"'"+
				'&txt_order_id='+"'"+txt_order_id+"'"+
				'&txt_season='+"'"+txt_season+"'"+

				'&txt_season_id='+"'"+txt_season_id+"'"+
				'&txt_file_no='+"'"+txt_file_no+"'"+
				'&txt_quotation_id='+"'"+txt_quotation_id+"'"+
				'&txt_hidden_quot_id='+"'"+txt_quotation_id+"'"+
				'&comments_head='+"'"+comments_head+"'"+
				'&sign='+"'"+sign+"'"+
				'&report_title='+"'"+report_title+"'"+
				'&path=../../../';

				http.open("POST","../../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);

				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function()
				{
					if(http.readyState == 4)
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
						release_freezing();
				   }
				}
			}
			else
			{
				var rate_amt=2; var zero_val='';
				if(type!='mo_sheet' && type != 'budgetsheet' && type != 'materialSheet' && type != 'materialSheet2' && type!='mo_sheet_3')
				{
					var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
				}

				if(type=='materialSheet' ||  type == 'materialSheet2')
				{
					var r=confirm("Press \"OK\" to show Qty  Excluding Allowance.\nPress \"Cancel\" to show Qty Including Allowance.");
				}

				var excess_per_val="";

				if(type=='mo_sheet')
				{
					excess_per_val = prompt("Please enter your Excess %", "0");
					if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
				}

				if(type == 'budgetsheet')
				{
					var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
				}

				if(type=='mo_sheet_3')
				{
					excess_per_val = prompt("Please enter your Excess %", "0");
					if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
				}

				if (r==true) zero_val="1"; else zero_val="0";
				//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
				var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*cbo_costing_per*print_option_id',"../../");

				freeze_window(3);
				http.open("POST","requires/pre_cost_entry_controller_v3.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_generate_report_reponse;
			}
		}
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			show_msg('3');
			release_freezing();
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currercy').val();
		var costing_date = $('#txt_costing_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+costing_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/pre_cost_entry_controller_v3');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function fnc_budgete_cost_validate(type)
	{
		var company_id=$('#cbo_company_name').val();
		var fab_cost_pre=$('#txt_fabric_pre_cost').val()*1;
		var trim_cost_pre=$('#txt_trim_pre_cost').val()*1;
		var embel_cost_pre=$('#txt_embel_pre_cost').val()*1;
		var wash_cost_pre=$('#txt_wash_pre_cost').val()*1;
		var commercial_cost_pre=$('#txt_comml_pre_cost').val()*1;
		var lab_cost_pre=$('#txt_lab_test_pre_cost').val()*1;
		var inspection_cost_pre=$('#txt_inspection_pre_cost').val()*1;
		var cm_cost_pre=$('#txt_cm_pre_cost').val()*1;
		var comml_pre_cost=$('#txt_comml_pre_cost').val()*1;
		var freight_cost_pre=$('#txt_freight_pre_cost').val()*1;
		var currier_cost_pre=$('#txt_currier_pre_cost').val()*1;
		var certificate_cost_pre=$('#txt_certificate_pre_cost').val()*1;
		var common_oh_cost_pre=$('#txt_common_oh_pre_cost').val()*1;
		var commission_cost_pre=$('#txt_commission_pre_cost').val()*1;
		var depr_amor_cost_pre=$('#txt_depr_amor_pre_cost').val()*1;
		var total_cost_pre=$('#txt_total_pre_cost').val()*1;

		

		var fab_cons_kg=0; var fab_cons_mtr=0; var fab_cons_yds=0; var fab_amount=0; var sp_oparation_amount=0; var acc_amount=0; var fright_amount=0; var lab_amount=0; var misce_amount=0; var other_amount=0; var comm_amount=0; var fob_amount=0; var cm_amount=0; var commercial_cost=0; var rmg_ratio=0; var is_approved=0; var wash_amount=0;
		var costing_id=$('#txt_quotation_id').val()*1;
		var cost_control_source_data=return_global_ajax_value( company_id, 'cost_control_source', '', 'requires/pre_cost_entry_controller_v3');
		if(trim(cost_control_source_data)==1 || trim(cost_control_source_data)==6)
		{
			if(costing_id!=0)
			{
				var str_data=return_global_ajax_value( costing_id, 'budgete_cost_validate', '', 'requires/pre_cost_entry_controller_v3');
				var spdata=str_data.split("##");
				var fab_cons_kg=trim(spdata[0]); var fab_cons_mtr=trim(spdata[1]); var fab_cons_yds=trim(spdata[2]); var fab_amount=trim(spdata[3]); var sp_oparation_amount=trim(spdata[4]); var acc_amount=trim(spdata[5]); var fright_amount=trim(spdata[6]); var lab_amount=trim(spdata[7]); var misce_amount=trim(spdata[8]); var other_amount=trim(spdata[9]); var comm_amount=trim(spdata[10]); var fob_amount=trim(spdata[11]); var cm_amount=trim(spdata[12]); var commercial_cost=trim(spdata[13]); var rmg_ratio=trim(spdata[14]); var is_approved=trim(spdata[15]); var wash_amount=trim(spdata[16]);
				//alert(fab_amount);
				var msg=""; var msg_type=0;
				if(is_approved==1)
				{
					if(type==2)
					{
						if(fab_cost_pre>fab_amount)
						{
							msg_type=1;
							var fab_cost_dif=fab_cost_pre-fab_amount;
							msg+="\nBOM Limit. Fabric Cost:="+ fab_amount;
							msg+="\nFabric Cost Over form Qc:="+  number_format(fab_cost_dif,4,'.','' );
						}

						var row_num=$('#tbl_fabric_cost tr').length-1;
						var cons_kg_pre=0; var cons_mtr_pre=0;  var cons_yds_pre=0;
						for (var i=1; i<=row_num; i++)
						{
							if(($('#uom_'+i).val()*1)==12)
							{
								cons_kg_pre+=$('#txtconsumption_'+i).val()*1;
							}
							if(($('#uom_'+i).val()*1)==23)
							{
								cons_mtr_pre+=$('#txtconsumption_'+i).val()*1;
							}
							if(($('#uom_'+i).val()*1)==27)
							{
								cons_yds_pre+=$('#txtconsumption_'+i).val()*1;
							}
						}
						
						cons_kg_pre=number_format(cons_kg_pre,4,'.','' );
						cons_mtr_pre=number_format(cons_mtr_pre,4,'.','' );
						cons_yds_pre=number_format(cons_yds_pre,4,'.','' );

						if(cons_kg_pre>fab_cons_kg)
						{
							msg_type=1;
							var cons_kg_dif=cons_kg_pre-fab_cons_kg;
							msg+="\nBOM Limit. Fabric Cons KG:="+ fab_cons_kg;
							msg+="\nFabric Cons KG Over form Qc:="+  number_format(cons_kg_dif,4,'.','' );
						}
						if(cons_mtr_pre>fab_cons_mtr)
						{
							msg_type=1;
							var cons_mtr_dif=cons_mtr_pre-fab_cons_mtr;
							msg+="\nBOM Limit. Fabric Cons Mtr:="+ fab_cons_mtr;
							msg+="\nFabric Cons Mtr Over form Qc:="+ number_format(cons_mtr_dif,4,'.','' );
						}

						if(cons_yds_pre>fab_cons_yds)
						{
							msg_type=1;
							var cons_yds_dif=cons_yds_pre-fab_cons_yds;
							msg+="\nBOM Limit. Fabric Cons YDS:="+ fab_cons_yds;
							msg+="\nFabric Cons YDS Over form Qc:="+ number_format(cons_yds_dif,4,'.','' );
						}
					}
					if(type==4)
					{
						if(embel_cost_pre>sp_oparation_amount)
						{
							msg_type=1;
							var sp_oparation_dif=embel_cost_pre-sp_oparation_amount;
							msg+="\nBOM Limit. Embel. Cost Cost:="+ sp_oparation_amount;
							msg+="\nEmbel. Cost Over form Qc:="+ number_format(sp_oparation_dif,4,'.','');
						}
					}
					if(type==5)
					{
						if(wash_cost_pre>wash_amount)
						{
							msg_type=1;
							var wash_amount_dif=wash_cost_pre-wash_amount;
							msg+="\nBOM Limit. Gmts.Wash Cost:="+ wash_amount;
							msg+="\nGmts.Wash Cost Over form Qc:="+ number_format(wash_amount_dif,4,'.','');
						}
					}
					if(type==3)
					{
						if(trim_cost_pre>acc_amount)
						{
							msg_type=1;
							var trim_cost_dif=trim_cost_pre-acc_amount;
							msg+="\nBOM Limit. Trim Cost:="+ acc_amount;
							msg+="\nTrim Cost Over form Qc:="+ number_format(trim_cost_dif,4,'.','' );
						}
					}
					if(type==1)
					{
						if(lab_cost_pre>lab_amount)
						{
							msg_type=1;
							var lab_cost_dif=lab_cost_pre-lab_amount;
							msg+="\nBOM Limit. Lab Cost:="+ lab_amount;
							msg+="\nLab Cost Over form Qc:="+ number_format(lab_cost_dif,4,'.','' );
						}
					}
					if(type==1)
					{
						//alert(lab_cost_pre+'='+lab_amount+'____'+msg);return;
						if(freight_cost_pre>fright_amount)
						{
							msg_type=1;
							var freight_cost_dif=freight_cost_pre-fright_amount;
							msg+="\nBOM Limit. Freight Cost:="+ fright_amount;
							msg+="\nFreight Cost Over form Qc:="+ number_format(freight_cost_dif,4,'.','' );

						}
					}
					if(type==6)
					{
						if(commission_cost_pre>comm_amount)
						{
							msg_type=1;
							var commission_cost_dif=commission_cost_pre-comm_amount;
							msg+="\nBOM Limit. Commission Cost:="+ comm_amount;
							msg+="\nCommission Cost Over form Qc:="+ number_format(commission_cost_dif,4,'.','' );
						}
					}
					
					if(type==7)
					{
						if(comml_pre_cost>commercial_cost)
						{
							msg_type=1;
							var commercial_cost_dif=comml_pre_cost-commercial_cost;
							msg+="\n BOM Limit. Commercial Cost:="+ commercial_cost;
							msg+="\n Commercial Cost Over form Qc:="+ number_format(commercial_cost_dif,4,'.','' );
						}
					}

					if(type==1 && cmValidation==2)
					{
						if(cm_cost_pre>cm_amount)
						{
							msg_type=1;
							var cm_cost_dif=cm_cost_pre-cm_amount;
							msg+="\nBOM Limit. CM Cost:="+ cm_amount;
							msg+="\nCM Cost Over form Qc:="+ number_format(cm_cost_dif,4,'.','' );
						}
					}
					if(msg_type!=1)
					{
						msg_type=0;
						msg=0;
					}
					return msg+'__'+msg_type;
				}
				else
				{
					var msg_type=0;
					var msg=0+'__'+msg_type;
					return msg;
				}
			}
			else
			{
				var msg_type=0;
				var msg=0+'__'+msg_type;
				return msg;
			}
		}
		else
		{
			var msg_type=0;
			var msg=0+'__'+msg_type;
			return msg;
		}
	}

	function print_report_button_setting(report_ids)
	{
		$("#report_btn_1").hide();
		$("#report_btn_2").hide();
		$("#report_btn_3").hide();
		$("#report_btn_4").hide();
		$("#report_btn_5").hide();
		$("#report_btn_6").hide();
		$("#report_btn_7").hide();
		$("#report_btn_8").hide();
		$("#report_btn_9").hide();
		$("#report_btn_10").hide();
		$("#report_btn_11").hide();
		$("#report_btn_12").hide();
		$("#report_btn_13").hide();
		$("#report_btn_14").hide();
		$("#report_btn_15").hide();
		$("#report_btn_16").hide();
		$("#report_btn_17").hide();
		$("#report_btn_18").hide();
		$("#report_btn_19").hide();
		$("#report_btn_20").hide();
		$("#report_btn_21").hide();
		$("#report_btn_22").hide();
		$("#report_btn_23").hide();
		$("#report_btn_24").hide();
		$("#report_btn_25").hide();
		$("#report_btn_26").hide();
		$("#report_btn_27").hide();
		$("#report_btn_28").hide();
		$("#report_btn_30").hide();
		$("#report_btn_31").hide();
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==50) $("#report_btn_1").show();
			if(report_id[k]==51) $("#report_btn_2").show();
			if(report_id[k]==52) $("#report_btn_3").show();
			if(report_id[k]==63) $("#report_btn_4").show();
			if(report_id[k]==156) $("#report_btn_5").show();
			if(report_id[k]==157) $("#report_btn_6").show();
			if(report_id[k]==158) $("#report_btn_7").show();
			if(report_id[k]==159)$("#report_btn_8").show();
			if(report_id[k]==170)$("#report_btn_9").show();
			if(report_id[k]==171)$("#report_btn_10").show();
			if(report_id[k]==142)$("#report_btn_11").show();
			if(report_id[k]==192)$("#report_btn_12").show();
			if(report_id[k]==197)$("#report_btn_13").show();
			if(report_id[k]==211)$("#report_btn_14").show();
			if(report_id[k]==221)$("#report_btn_15").show();
			if(report_id[k]==173)$("#report_btn_16").show();
			if(report_id[k]==238)$("#report_btn_17").show();
			if(report_id[k]==215)$("#report_btn_18").show();
			if(report_id[k]==270)$("#report_btn_19").show();
			if(report_id[k]==581)$("#report_btn_20").show();
			if(report_id[k]==730)$("#report_btn_21").show();
			if(report_id[k]==759)$("#report_btn_22").show();
			if(report_id[k]==351)$("#report_btn_23").show();
			if(report_id[k]==268) $("#report_btn_24").show();
			if(report_id[k]==381)$("#report_btn_25").show();
			if(report_id[k]==405)$("#report_btn_26").show();
			if(report_id[k]==765)$("#report_btn_27").show();
			if(report_id[k]==403)$("#report_btn_28").show();
			if(report_id[k]==769)$("#report_btn_30").show();
			if(report_id[k]==882)$("#report_btn_31").show();
		}
	}

	function ResetForm(){
		reset_form('precosting_1*quotationdtls_2','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2*is_click_cons_box,1*txt_incoterm_place,Chittagong*cbo_ready_to_approved,2*txt_costing_date,<? echo date("d-m-Y"); ?>','')
		var company_id=document.getElementById('cbo_company_name').value;
		$('#cbo_costing_per').removeAttr('disabled');
		check_exchange_rate();
	}

	function calculate_deffd_lc()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var txt_costing_date= document.getElementById('txt_costing_date').value;

		var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value*1;
		var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var depreciation_amortization_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller');
		var txt_deffd_lc_cost_percent=document.getElementById('txt_deffd_lc_cost_percent').value;
		//var data=incometax_per.split('_');
		var data_value=txt_deffd_lc_cost_percent;
		if(data_value=="") data_value=0;

		deffd_lc_value=(fob_value*data_value)/100;
		if(number_format_common(deffd_lc_value,1,0,currency)>0)
		{
			document.getElementById('txt_deffdlc_pre_cost').readOnly=true ;
		}
		else
		{
			document.getElementById('txt_deffdlc_pre_cost').readOnly=false;
		}
		document.getElementById('txt_deffdlc_pre_cost').value=number_format_common(deffd_lc_value,1,0,currency)
		//alert(fob_value);
		calculate_main_total();
	}

	function calculate_interest_cost()
	{
		var currency=$("#cbo_currercy").val()*1;
		var cbo_company_name=$("#cbo_company_name").val()*1;
		var txt_costing_date=$("#txt_costing_date").val();

		var txt_final_price_dzn_pre_cost=$("#txt_final_price_dzn_pre_cost").val()*1;
		var txt_commission_pre_cost=$("#txt_commission_pre_cost").val()*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var depreciation_amortization_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller');
		var interest_per=$("#cost_per_minute").val();
		var data=interest_per.split('_');
		var data_value=data[6];
		if(data_value=="") data_value=0;
		//alert(data_value);
		interest_value=(fob_value*data_value)/100;
		if(number_format_common(interest_value,1,0,currency)>0)
		{
			document.getElementById('txt_interest_pre_cost').readOnly=true ;
		}
		else
		{
			document.getElementById('txt_interest_pre_cost').readOnly=false;
		}
		$("#txt_interest_pre_cost").val( number_format_common(interest_value,1,0,currency) );
		//alert(fob_value);
		calculate_main_total();
	}

	function calculate_income_tax()
	{
		var currency=$("#cbo_currercy").val()*1;
		var cbo_company_name=$("#cbo_company_name").val()*1;
		var txt_costing_date= $("#txt_costing_date").val();

		var txt_final_price_dzn_pre_cost=$("#txt_final_price_dzn_pre_cost").val()*1;
		var txt_commission_pre_cost=$("#txt_commission_pre_cost").val()*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var depreciation_amortization_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller');
		var incometax_per=$("#cost_per_minute").val();
		var data=incometax_per.split('_');
		var data_value=data[7];
		if(data_value=="") data_value=0;

		incometax_value=(fob_value*data_value)/100;
		if(number_format_common(incometax_value,1,0,currency)>0)
		{
			document.getElementById('txt_incometax_pre_cost').readOnly=true;
		}
		else
		{
			document.getElementById('txt_incometax_pre_cost').readOnly=false ;
		}
		$("#txt_incometax_pre_cost").val( number_format_common(incometax_value,1,0,currency) );
		//alert(fob_value);
		calculate_main_total();
	}

	function set_efficiency_percent(data){
		var data=JSON.parse(data)
		if(data.efficiency_source==2 || data.efficiency_source==3){
			$('#txt_sew_efficiency_per').attr('disabled','true');
		}else{
			$('#txt_sew_efficiency_per').removeAttr('disabled');
		}

		if( ($('#txt_quotation_id').val()*1)==0)
		{
			if(data.efficiency_percent==0 || data.efficiency_percent== ""){
				document.getElementById('txt_cm_pre_cost').value=0
				document.getElementById('txt_cm_po_price').value=0
			}
		}
		document.getElementById('txt_sew_efficiency_per').value = data.efficiency_percent;
		document.getElementById('txt_sew_efficiency_source').value = data.efficiency_source;
		calculate_cm_cost_with_method();
	}

	function set_currier_cost_method_variable(company)
	{
		var job_no= document.getElementById('txt_job_no').value;
		var fabric_pre_cost= document.getElementById('txt_fabric_pre_cost').value;
		var final_price_dzn_pre_cost= document.getElementById('txt_final_price_dzn_pre_cost').value;
		var commission_pre_cost= document.getElementById('txt_commission_pre_cost').value;
		var currency=$("#cbo_currercy").val()*1;

		var response_data=return_global_ajax_value(job_no+'_'+company+'_'+fabric_pre_cost+'_'+final_price_dzn_pre_cost+'_'+commission_pre_cost, 'curreir_cost_method', '', 'requires/pre_cost_entry_controller_v3');
		var response=response_data.split('**');
		var currier_cost_method= response[0]*1;
		var currier_amount=0;
		currier_amount= response[1]*1;
		var editable= response[2]*1;
		var based_on= response[3]*1;
		var fixamount= response[4]*1;
		if(based_on==2 && fixamount>0)
		{
			var costing_perid=$("#cbo_costing_per").val()*1;
			var styleqty=$("#txt_offer_qnty").val()*1;
			
			if(costing_perid==1) var costing_per_amount=12*1;
			if(costing_perid==2) var costing_per_amount=1;
			if(costing_perid==3) var costing_per_amount=12*2;
			if(costing_perid==4) var costing_per_amount=12*3;
			if(costing_perid==5) var costing_per_amount=12*4;
			//alert(costing_perid+'_'+costing_per_amount+'_'+fixamount+'_'+styleqty)
			
			currier_amount=(fixamount/styleqty)*costing_per_amount;
		}
		if(currier_amount>0)
		$("#txt_currier_pre_cost").val(number_format_common(currier_amount,1,0,currency));
		//alert(currier_amount);
		if(editable==1)
		{
			document.getElementById('txt_currier_pre_cost').readOnly=false
		}
		else
		{
			document.getElementById('txt_currier_pre_cost').readOnly=true
		}
		calculate_main_total();
	}

	function auto_completesupplier(company_id,rid) // Auto Complite Party/Transport Com
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		if($("#txtembsupplier_"+rid).val()=='') $("#cboembsupplierid_"+rid).val(0);

		var supplier = return_global_ajax_value( company_id, 'supplier_name', '', 'requires/pre_cost_entry_controller_v3');
		supplierInfo = eval(supplier);
		$("#txtembsupplier_"+rid).autocomplete({
			source: supplierInfo,
			search: function( event, ui ) {
				$("#cboembsupplierid_"+rid).val(0);
				$("#hidembsupplier_"+rid).val("");
			},
			select: function (e, ui) {
				$(this).val(ui.item.label);
				$("#hidembsupplier_"+rid).val(ui.item.label);
				$("#cboembsupplierid_"+rid).val(ui.item.id);
			}
		});

		$(".supplier_name").live("blur",function(){
			  if($(this).siblings(".hidsupplier_"+rid).val() == ""){
				  $(this).val("");
				  $("#cboembsupplierid_"+rid).val(0);
			 }
		});
		//check_duplicate(rid);
	}

	function openmypage_unapprove_request()
	{
		if (form_validation('txt_job_no','Job No')==false)
		{
			return;
		}

		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		var data=txt_job_no+"_"+txt_un_appv_request;
		var title = 'Un Approval Request';
		var page_link = 'requires/pre_cost_entry_controller_v3.php?data='+data+'&action=unapp_request_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function fnc_margin_as_bom()
	{
		var job_no= document.getElementById('txt_job_no').value;
		var response_data=return_global_ajax_value(job_no, 'margin_pcs_as_bom', '', 'requires/pre_cost_entry_controller_v3');

		var exdata=response_data.split('***');

		$('#txt_margin_pcs_bom_cost').val(exdata[1]);
		$('#txt_margin_pcs_bom_per').val(exdata[2]);
	}

	function openmypage_template_name(title)
	{
		var update_id=$("#update_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var check_is_master_part_saved=return_global_ajax_value(update_id, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v3');
		if(trim(check_is_master_part_saved)=="")
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
			$(this).html('Please Save Master Part').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
			return;
		}
		/*var page_link='requires/pre_cost_entry_controller_v3.php?action=trims_cost_template_name_popup&company=' + document.getElementById('cbo_company_name').value + '&update_id=' + document.getElementById('update_id').value;*/
		var row_count=$('#tbl_trim_cost tr').length;
		if(row_count == 0){
			$('#txt_trim_pre_cost').focus();
		}
		var page_link='requires/pre_cost_entry_controller_v3.php?action=trims_cost_template_name_popup&company=' + document.getElementById('cbo_company_name').value + '&buyer_name=' + document.getElementById('cbo_buyer_name').value+ '&update_id=' + document.getElementById('update_id').value;
		if ( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer')==false )
		{
			return;
		}
		else
		{

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=400px,center=1,resize=0,scrolling=0', '')
			emailwindow.onclose = function()
			{
				var theform = this.contentDoc.forms[0];
				var select_template_data = this.contentDoc.getElementById('select_template_data').value;
				//alert(select_template_data);
				if(select_template_data != '')
				{
					load_template_data(select_template_data);
				}
			}
		}
	}

	function load_template_data(data)
	{
		var row_count=$('#tbl_trim_cost tr').length-1;
		var itemdata=data.split(",");
		var a=0; var n=0;
		for(var b=1; b<=itemdata.length; b++)
		{
			var exdata="";
			var exdata=itemdata[a].split("***");
			if(row_count == 1 && document.getElementById('cboconsuom_1').value == 0)
			{
				document.getElementById('cbogroup_1').value=exdata[2];
				document.getElementById('cbogrouptext_1').value=exdata[0];
				document.getElementById('txtdescription_1').value=exdata[10];
				document.getElementById('txtsupref_1').value=exdata[9];
				document.getElementById('cbonominasupplier_1').value=exdata[8];
				document.getElementById('cboconsuom_1').value=exdata[3];
				document.getElementById('txtconsdzngmts_1').value=exdata[4];
				document.getElementById('txttrimrate_1').value=exdata[5];
				document.getElementById('txttrimamount_1').value=exdata[6];
				document.getElementById('cboapbrequired_1').value=exdata[7];
			}
			else if(row_count == 1 && document.getElementById('cbogroup_1').value == 42)
			{
				document.getElementById('cbogroup_1').value=exdata[2];
				document.getElementById('cbogrouptext_1').value=exdata[0];
				document.getElementById('txtdescription_1').value=exdata[10];
				document.getElementById('txtsupref_1').value=exdata[9];
				document.getElementById('cbonominasupplier_1').value=exdata[8];
				document.getElementById('cboconsuom_1').value=exdata[3];
				document.getElementById('txtconsdzngmts_1').value=exdata[4];
				document.getElementById('txttrimrate_1').value=exdata[5];
				document.getElementById('txttrimamount_1').value=exdata[6];
				document.getElementById('cboapbrequired_1').value=exdata[7];
			}
			else
			{
				add_break_down_tr_trim_cost(row_count);
				n++;
				row_count++;
				document.getElementById('cbogroup_'+row_count).value=exdata[2];
				document.getElementById('cbogrouptext_'+row_count).value=exdata[0];
				document.getElementById('txtdescription_'+row_count).value=exdata[10];
				document.getElementById('txtsupref_'+row_count).value=exdata[9];
				document.getElementById('cbonominasupplier_'+row_count).value=exdata[8];
				document.getElementById('cboconsuom_'+row_count).value=exdata[3];
				document.getElementById('txtconsdzngmts_'+row_count).value=exdata[4];
				document.getElementById('txttrimrate_'+row_count).value=exdata[5];
				document.getElementById('txttrimamount_'+row_count).value=exdata[6];
				document.getElementById('cboapbrequired_'+row_count).value=exdata[7];
			}
			a++;
		}
	}

	function report_part(type)
	{
		if(type=='preCostRpt5')
		{
			var print_option = $("#print_option").val();
			var print_option_id = $("#print_option_id").val();
			var print_option_no = $("#print_option_no").val();

			var page_link='requires/pre_cost_entry_controller_v3.php?action=report_part_select_view&print_option='+print_option+'&print_option_id='+print_option_id+'&print_option_no='+print_option_no;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Pre-Costing Print Option", 'width=460px,height=270px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var option_des=this.contentDoc.getElementById("txt_selected").value;
				var option_id=this.contentDoc.getElementById("txt_selected_id").value;
				var serial_no=this.contentDoc.getElementById("txt_selected_no").value;
				$("#print_option").val(option_des);
				$("#print_option_id").val(option_id);
				$("#print_option_no").val(serial_no);
				generate_report(type);
			}
		}
	}

	function fnc_tna_process( operation )
	{
		if($('#txt_job_no').val()==''){alert("Please choose a job to TNA Process");return;}

		var data="action=tna_process&cbo_company="+$('#cbo_company_name').val()+"&txt_job_no="+$('#txt_job_no').val()+"&is_manual_process=1";
		freeze_window(operation);
		// alert(data)
		http.open("POST","../../tna/knit/requires/tna_process_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_process_reponse;
	}

	function fnc_tna_process_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if( trim( reponse[2])!="" && trim( reponse[2])!=undefined )
			{
				$('#messagebox_main').html("Process Failed for following PO Number-"+reponse[2]);
			}
			else
			{
				$('#messagebox_main', window.parent.document).fadeTo(10,1,function(){
					$(this).html('Process is completed successfully.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
			}
		}
	}

	function fnc_po_pop()
	{
		if (form_validation('txt_job_no','Job No')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var hidd_job_id=$("#hidd_job_id").val();
			var page_link='requires/pre_cost_entry_controller_v3.php?action=order_no_popup&hidd_job_id='+hidd_job_id;
			var title='Order Search';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var po_id=this.contentDoc.getElementById("txt_selected_id").value;
				var po_no=this.contentDoc.getElementById("txt_selected").value;
				if (po_id!="")
				{
					$("#txt_po_no").val(po_no);
					$("#txt_po_breack_down_id").val(po_id);
				}
			}
		}
	}

	function fncChangeButton(value)
	{
		if(value==1) $("#btn_appSubmission_withoutanyChange").show();
		else $("#btn_appSubmission_withoutanyChange").hide();
	}

	function fnc_appSubmission_withoutanyChange()
	{
		freeze_window(1);
		var update_id=$("#txt_job_no").val();
		var check_is_master_part_saved=return_global_ajax_value(update_id, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(check_is_master_part_saved)=="")
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
				$(this).html('Please Save Master Part').removeClass('messagebox').addClass('messagebox_error').fadeOut(7000);
			});
			release_freezing();
			return;
		}
		else
		{
			var cbo_ready_to_approved=$("#cbo_ready_to_approved").val();
			/*if(cbo_ready_to_approved==1)
			{*/
				fnc_margin_as_bom();
				var submission_withoutanyChange=return_global_ajax_value(update_id+'**'+$("#hidd_job_id").val()+'**'+cbo_ready_to_approved+'**'+$("#pre_cost_id").val()+'**'+$("#txt_fabric_pre_cost").val()+'**'+$("#cbo_company_name").val(), 'appSubmission_withoutanyChange', '', 'requires/pre_cost_entry_controller_v2');
				var response=submission_withoutanyChange.split('**');
				
				if(trim(response[0])=='commercial')
				{
					alert("Commercial Cost Compulsory. Please Input Commercial Cost.");
					release_freezing();
					return;
				}
				if(trim(response[0])=='cmcost')
				{
					alert("CM Cost Compulsory. Please Input CM Cost.");
					release_freezing();
					return;
				}
				if(trim(response[0])=='file')
				{
					alert("Please upload file.");
					release_freezing();
					return;
				}
				if(trim(response[0])=='approved'){
					alert("This Costing is Approved");
					release_freezing();
					return;
				}
				if(trim(response[0])==1)
				{
					if(cbo_ready_to_approved==1)
					{
						alert("Ready To Approved Yes is Updated Successfully.");
					}
					else
					{
						alert("Ready To Approved No is Updated Successfully.");
					}
					release_freezing();
					return;
				}
				else
				{
					alert("Ready To Approved Yes is not Updated Successfully.");
					release_freezing();
					return;
				}
			/*}
			else
			{
				alert("Applicable for Ready To Approved Yes");
				return;
			}*/
		}
	}



</script>
</head>
<body onLoad="set_hotkey(); set_auto_complete('pre_cost_mst');" >
    <div style="width:100%;" align="center" >
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:1070px;">
        <legend>Pre-Costing</legend>
        <form name="precosting_1" id="precosting_1" autocomplete="off" enctype="multipart/form-data">
            <div style="width:1070px;">
            <table  width="1070" cellspacing="2" cellpadding=""  border="0">
                <tr>
                    <td width="120" class="must_entry_caption">Job No</td>
                    <td width="130"><input style="width:110px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/pre_cost_entry_controller_v3.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />
                    	<input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
						<input type="hidden" id="txt_precost_id" name="txt_precost_id" class="text_boxes" style="width:110px;" disabled />
                    </td>
                    <td width="120">Company</td>
                    <td width="130"><?=create_drop_down("cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "","1"); ?></td>
                    <td width="120">Quotation ID</td>
                    <td width="130"><input type="text" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:110px;" readonly /></td>
                    <td width="120">Style Ref</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly/></td>
                </tr>
                <tr>
                    <td>Style Desc.</td>
                    <td colspan="3"><input class="text_boxes" type="text" style="width:360px;" name="txt_style_desc" id="txt_style_desc" maxlength="100" title="Maximum 100 Character" readonly/></td>
                    <td>Buyer</td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td>Prod. Dept.</td>
                    <td><? echo create_drop_down( "cbo_pord_dept", 80, $product_dept,"", 1, "-- Select --",0, "",1,"" ); ?>
                        <input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code"  disabled />
                    </td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><? echo create_drop_down( "cbo_currercy", 50, $currency,"", 0, "", 2, "" ,1,""); ?>
                        ER. <input class="text_boxes_numeric" type="text" style="width:30px;" name="txt_exchange_rate" id="txt_exchange_rate" readonly/>
                    </td>
                    <td>Agent</td>
                    <td id="agent_td"><? echo create_drop_down( "cbo_agent", 120, $blank_array,"", 1, "-- Select Agent --", $selected, "",1,"" ); ?></td>
                    <td>Job Qty.</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_offer_qnty" id="txt_offer_qnty"/></td>
                    <td>Order UOM </td>
                    <td><? echo create_drop_down("cbo_order_uom",60, $unit_of_measurement, "",0, "",1, "change_caption_cost_dtls(this.value, 'change_caption_pcs' )",1,"1,57,58" ); ?>
                        <input type="button" id="set_button" class="image_uploader" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                        <input type="hidden" id="set_breck_down" />
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="tot_set_qnty" />
                        <input type="hidden" id="print_option" name="print_option" />
                        <input type="hidden" id="print_option_no" name="print_option_no" />
                        <input type="hidden" id="print_option_id" name="print_option_id" />
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Costing Date</td>
                    <td><input class="datepicker" type="text" style="width:110px;" name="txt_costing_date" id="txt_costing_date" onChange="calculate_confirm_price_dzn();check_exchange_rate();" value="<? echo date('d-m-Y'); ?>"/></td>
                    <td class="must_entry_caption">Incoterm</td>
                    <td><? echo create_drop_down( "cbo_inco_term", 120, $incoterm,"", 0, "",1,"" );?></td>
                    <td class="must_entry_caption">Incoterm Place</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_incoterm_place" id="txt_incoterm_place" maxlength="100" title="Maximum 100 Character" value="Chittagong"/></td>
                    <td>Machine/Line</td><!--class="must_entry_caption"-->
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_machine_line" id="txt_machine_line" maxlength="100" title="Maximum 100 Character"/></td>
                </tr>
                <tr>
                    <td>Prod/Line/Hr</td><!--class="must_entry_caption"-->
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_prod_line_hr" id="txt_prod_line_hr" maxlength="100" title="Maximum 100 Character"/></td>
                    <td class="must_entry_caption">Costing Per</td>
                    <td><? echo create_drop_down( "cbo_costing_per", 70, $costing_per, "",0, "0", 2, "change_cost_per(this.value);","","" ); ?><span id="idCostingUom" style="float:right; width:35px; font-weight: bold; color:black; padding-right:20px"></span></td>
                    <td>Region</td>
                    <td><? echo create_drop_down( "cbo_region", 120, $region,"", 1, "-- Select Region --",0,"",1,"" ); ?></td>
                    <td>Approved</td>
                    <td><? echo create_drop_down( "cbo_approved_status", 120, $yes_no,"", 0, "", 2, "",1,"" ); ?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Sew. SMV</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_sew_smv" id="txt_sew_smv" onChange="calculate_cm_cost_with_method()" disabled /></td>
                    <td class="" id="sew_td">Sew Efficiency %</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_sew_efficiency_per" id="txt_sew_efficiency_per" onChange="calculate_cm_cost_with_method()" />
                    	<input class="text_boxes_numeric" type="hidden" style="width:80px;" name="txt_sew_efficiency_source" id="txt_sew_efficiency_source"/>
                    </td>
                    <td>Cut. SMV</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_cut_smv" id="txt_cut_smv" onChange="calculate_cm_cost_with_method()" /></td>
                    <td>Cut Efficiency %</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_cut_efficiency_per" id="txt_cut_efficiency_per" onChange="calculate_cm_cost_with_method()"  />
                    </td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3">
                    <input class="text_boxes" type="text" style="width:365px;" name="txt_remarks" id="txt_remarks" maxlength="500" title="Maximum 500 Character" placeholder="Remarks"/></td>
                    <td>Budget Minute</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_budget_minute" id="txt_budget_minute" /></td>
                    <td>Images</td>
                    <td><input type="button" class="image_uploader" style="width:120px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'pre_cost_v2', 0 ,1,2)" /></td>
                </tr>
                <tr>
                    <td>File no</td>
                    <td><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:110px" ></td>
                    <td>Internal Ref</td>
                    <td><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:110px" ></td>


                    <td>Copy From</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_copy_form" id="txt_copy_form" disabled/><input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_efficiency_wastage" id="txt_efficiency_wastage" onChange="calculate_cm_cost_with_method()" readonly /></td>
                    <td><input type="button" class="image_uploader" style="width:100px" value="Copy With Cons." onClick="show_sub_form( document.getElementById('update_id').value, 'partial_pre_cost_copy_action', '1');" /></td>
                    <td><input type="button" class="image_uploader" style="width:120px" value="Copy Without Cons." onClick="show_sub_form( document.getElementById('update_id').value, 'partial_pre_cost_copy_action', '2');" />
                    </td>
                </tr>
                <tr>
                	<td>Ready To Approved</td>
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 120, $yes_no,"", 1, "-- Select--", 2, "fncChangeButton(this.value);","","" ); ?></td>
                    <td colspan="2"><input type="button" name="btn_appSubmission_withoutanyChange" id="btn_appSubmission_withoutanyChange" class="formbuttonplasminus" style="width:130px;" onClick="fnc_appSubmission_withoutanyChange();" value="Submit For Approval"></td>
                    <td>File</td>
                    <td><input id="cbo_add_file" type="button" class="image_uploader" style="width:122px; text-align: center;" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('pre_cost_id').value,'', 'pre_cost_v2', 2 ,1,'','',1)"></td>

                    
                    <td>Un-approve Request</td>
                    <td>
                        <Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click" ID="txt_un_appv_request" style="width:110px;" onClick="openmypage_unapprove_request();">
                    </td>
                </tr>
                <tr>
                     <td>Refusing Cause</td>
                      <td colspan="5"><input class="text_boxes" type="text" style="width:615px;" name="txt_refusing" id="txt_refusing" maxlength="500" title="Maximum 500 Character" placeholder="Refusing Cause"/></td>
                </tr>
                
                <tr>
                    <td align="center" height="10" colspan="8" valign="top" id="check_sms" style="font-size:18px; color:#F00">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="8" valign="top" style="font-size:18px; color:#F00">&nbsp;<span id="app_sms"></span><span id="txt_total_pre_cost_view"></span></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" class="button_container" colspan="8">
                        <input type="hidden" id="update_id" value="" />
                        <input type="hidden" id="copy_quatation_id" value="" />
                        <input type="hidden" id="budget_exceeds_quot_id" value="" />
                        <input type="hidden" id="txt_cost_control_source" value="" />
                        <input type="hidden" id="pre_cost_id" value="" />
                        <input type="hidden" id="cm_cost_predefined_method_id" lib_variable_data="" value="" width="50" />
                        <input type="hidden" id="cm_cost_editable" value="" width="50" />
                        <input type="hidden" id="check_input" name="check_input" value="" width="50" />
                        <input type="hidden" id="is_click_cons_box" name="is_click_cons_box" value="1" width="50" />
                        <input type="hidden" id="cost_per_minute" name="cost_per_minute" value="" width="50" />
                        <input type="hidden" id="txt_deffd_lc_cost_percent" name="txt_deffd_lc_cost_percent" value="" width="50" />
                        <input type="hidden" id="hidd_is_dtls_open" value="1" />
                        <input type="hidden" id="hidd_trim_rate" value="" />
                        <? echo load_submit_buttons( $permission, "fnc_precosting_entry", 0,0 ,"ResetForm()",1,1) ; ?>
                        <input type="button" name="process" class="formbutton" style="width:150px;" onClick="fnc_tna_process(7)" value="TNA process manual">
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </fieldset>
    <div style="height:2px;"><br></div>
    <div style="width:1280px;">
        <table  width="100%" border="0">
            <tr valign="top">
                <td width="16%">
                <fieldset>
                    <form id="quotationdtls_2" autocomplete="off">
                    <table width="100%" cellspacing="2" cellpadding="0" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="94" align="center">Cost Components</th>
                                <th width="70" align="center">Budgeted Cost</th>
                                <th width="40" align="center"> % To Q.price </th>
                            </tr>
                        </thead>
                        <tr>
                            <td>Fabric Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_fabric_pre_cost" id="txt_fabric_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_fabric_cost_listview','');" onChange="calculate_main_total();" pre_fab_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_fabric_po_price" id="txt_fabric_po_price" style="width:32px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Trims Cost &nbsp <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;" onClick="openmypage_template_name('Template Search')"
                        >...</span></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_trim_pre_cost" id="txt_trim_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value);" onChange="calculate_main_total();" pre_trim_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_trim_po_price" id="txt_trim_po_price" style="width:32px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Embel. Cost</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_embel_pre_cost" id="txt_embel_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_embellishment_cost_listview','');" onChange="calculate_main_total();" pre_emb_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_embel_po_price" id="txt_embel_po_price" style="width:32px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Gmts.Wash</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_wash_pre_cost" id="txt_wash_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_wash_cost_listview','');" onChange="calculate_main_total();" pre_wash_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_wash_po_price" id="txt_wash_po_price" style="width:32px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Comml. Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_comml_pre_cost" id="txt_comml_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_comarcial_cost_listview','');" onChange="calculate_main_total();" pre_comml_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_comml_po_price" id="txt_comml_po_price" style="width:32px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Lab Test</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_lab_test_pre_cost" id="txt_lab_test_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_lab_test_po_price" id="txt_lab_test_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Inspection </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_inspection_pre_cost" id="txt_inspection_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_inspection_po_price" id="txt_inspection_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Freight</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_freight_pre_cost" id="txt_freight_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_freight_po_price" id="txt_freight_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Courier Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_currier_pre_cost" id="txt_currier_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_currier_po_price" id="txt_currier_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Certificate Cost </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_certificate_pre_cost" id="txt_certificate_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_certificate_po_price" id="txt_certificate_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td title="Deferred LC / Document Charges">Deffd. LC/DC</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_deffdlc_pre_cost" id="txt_deffdlc_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_deffdlc_po_price" id="txt_deffdlc_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Design Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_design_pre_cost" id="txt_design_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_design_po_price" id="txt_design_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Studio Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_studio_pre_cost" id="txt_studio_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_studio_po_price" id="txt_studio_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Opert. Exp.</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_common_oh_pre_cost" id="txt_common_oh_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_common_oh_po_price" id="txt_common_oh_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>CM Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_cm_pre_cost" id="txt_cm_pre_cost" style="width:60px;" onChange="calculate_main_total();" /></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_cm_po_price" id="txt_cm_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Interest</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_interest_pre_cost" id="txt_interest_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_interest_po_price" id="txt_interest_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Income Tax</td>
                            <td>
                            <input class="text_boxes_numeric" type="text" name="txt_incometax_pre_cost" id="txt_incometax_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_incometax_po_price" id="txt_incometax_po_price" style="width:32px;" onChange="calculate_main_total();" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Depc. & Amort.</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_depr_amor_pre_cost" id="txt_depr_amor_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_depr_amor_po_price" id="txt_depr_amor_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Commission</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_commission_pre_cost" id="txt_commission_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_commission_cost_listview','');" onChange="calculate_main_total();" pre_commis_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_commission_po_price" id="txt_commission_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr bgcolor="#CCFF99">
                            <td>Total Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost" id="txt_total_pre_cost" style="width:60px;" readonly =""/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_total_po_price" id="txt_total_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="final_price_td_dzn">Price/Dzn</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_dzn_pre_cost" id="txt_final_price_dzn_pre_cost" style="width:60px;" readonly /></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_dzn_po_price" id="txt_final_price_dzn_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="margin_dzn">Margin/Dzn</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_dzn_pre_cost" id="txt_margin_dzn_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" buyer_profit_per="" name="txt_margin_dzn_po_price" id="txt_margin_dzn_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="price_pcs_td">Price/Pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_pcs_pre_cost" id="txt_final_price_pcs_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_pcs_po_price" id="txt_final_price_pcs_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr bgcolor="#CCFF99">
                            <td id="final_cost_td_pcs_set">Final Cost/Pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost_psc_set" id="txt_total_pre_cost_psc_set" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost_psc_set_po_price" id="txt_total_pre_cost_psc_set_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="margin_pcs_td">Margin/pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_pre_cost" id="txt_margin_pcs_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_po_price" id="txt_margin_pcs_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="margin_bom_td">BOM Margin/Pcs </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_bom_cost" id="txt_margin_pcs_bom_cost" style="width:60px;" onClick="fnc_margin_as_bom();" placeholder="Click To Load" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_bom_per" id="txt_margin_pcs_bom_per" min_profit_per="" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3" valign="middle" class="button_container"><input type="hidden" id="update_id_dtls" name="update_id_dtls" readonly/>
                            <? echo load_submit_buttons( $permission, "fnc_quotation_entry_dtls", 0,0 ,"reset_form('quotationdtls_2','','')",2) ; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">Select PO:<span id="po_td">

							<input class="text_boxes" type="text" name="txt_po_no" id="txt_po_no" style="width:180px;" onDblClick="fnc_po_pop();" placeholder="Browse" readonly/>
                            <input class="text_boxes" type="hidden" name="txt_po_breack_down_id" id="txt_po_breack_down_id" style="width:80px;"/>
							<? //echo create_drop_down( "txt_po_breack_down_id", 190,$blank_array, "", 1, "SL#Po No#Po Qty(Pcs)#Ship Date#Po Status", "", ""); ?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <input type="button" id="report_btn_1" class="formbutton" value="Cost Rpt" onClick="generate_report('preCostRpt');" style="display:none;" />
                                <input type="button" id="report_btn_2" class="formbutton" value="Cost Rpt2" onClick="generate_report('preCostRpt2');" style="display:none;" />
                                <input type="button" id="report_btn_3" class="formbutton" value="BOM Rpt" onClick="generate_report('bomRpt');" style="display:none;" />
                                <input type="button" id="report_btn_4" class="formbutton" value="BOM Rpt 2" onClick="generate_report('bomRpt2');" style="display:none;" />
                                <input type="button" id="report_btn_5" class="formbutton" value="Acce. Dtls" onClick="generate_report('accessories_details');" style="display:none;" />
                                <input type="button" id="report_btn_6" class="formbutton" value="Acce. Dtls 2" onClick="generate_report('accessories_details2');" style="display:none;"  />
                                <input type="button" id="report_btn_7" class="formbutton" value="Cost Woven" onClick="generate_report('preCostRptWoven');" style="display:none;"  />
                                <input type="button" id="report_btn_8" class="formbutton" value="Bom Woven" onClick="generate_report('bomRptWoven');" style="display:none;" />
                                <input type="button" id="report_btn_9" class="formbutton" value="Cost Rpt3" onClick="generate_report('preCostRpt3');" style="display:none;" />
                                <input type="button" id="report_btn_10" class="formbutton" value="Cost Rpt4" onClick="generate_report('preCostRpt4');" style="display:none;" />
                                <input type="button" id="report_btn_11" class="formbutton" value="Rpt Bpkw" onClick="generate_report('preCostRptBpkW');"  style="display:none;"/>
                                <input type="button" id="report_btn_12" class="formbutton" value="BOM Dtls" onClick="generate_report('checkListRpt');" style="display:none;" />
								<input type="button" id="report_btn_13" class="formbutton" value="BOM Rpt 3" onClick="generate_report('bomRpt3');" style="display:none;" />
								<input type="button" id="report_btn_14" class="formbutton" value="MO Sheet" onClick="generate_report('mo_sheet');" style="display:none;" />
								<input type="button" id="report_btn_15" class="formbutton" value="Fab. Pre-Cost" onClick="generate_report('fabric_cost_detail');" style="display:none;" />
                                <input type="button" id="report_btn_16" class="formbutton" value="Cost Rpt5" onClick="report_part('preCostRpt5');" style="display:none;" />
                                <input type="button" id="report_btn_17" class="formbutton" value="Summary" onClick="generate_report('summary');" style="display:none;" />
								<input type="button" id="report_btn_18" class="formbutton" value="Budget3 Details" onClick="generate_report('budget3_details');" style="display:none;" />
                                <input type="button" id="report_btn_19" class="formbutton" value="Cost Rpt6" onClick="generate_report('preCostRpt6');" style="display:none;" />
                                <input type="button" id="report_btn_20" class="formbutton" value="Cost Sheet" onClick="generate_report('costsheet');" style="display:none;" />
                                <input type="button" id="report_btn_21" class="formbutton" value="Budget Sheet" onClick="generate_report('budgetsheet');" style="display:none;" />
                                <input type="button" id="report_btn_22" class="formbutton" value="Materials Dtls" onClick="generate_report('materialSheet');" style="display:none;" />
                                <input type="button" id="report_btn_23" class="formbutton" value="BOM Rpt 4" onClick="generate_report('bomRpt4');" style="display:none;" />
                                <input type="button" id="report_btn_24" class="formbutton" value="Budget 4" onClick="generate_report('budget_4');" style="display:none;" />
                                <input type="button" id="report_btn_25" class="formbutton" value="MO Sheet 2" onClick="generate_report('mo_sheet_2');"  style="display:none;"/>
								<input type="button" id="report_btn_26" class="formbutton" value="Materials Dtls 2" onClick="generate_report('materialSheet2');" style="display:none;" />
								<input type="button" id="report_btn_27" class="formbutton" value="BOM Rpt 5" onClick="generate_report('bomRpt5');" style="display:none;"  />
								<input type="button" id="report_btn_28" class="formbutton" value="MO Sheet 3" onClick="generate_report('mo_sheet_3');" style="display:none;"  />
								<input type="button" id="report_btn_29" class="formbutton" value="MO Sheet 4" onClick="mo_sheet_popup('requires/pre_cost_entry_controller_v3.php?action=mo_sheet4_popup','MO SHEET 4');"/>
								<a id="mo_sheet_4" href="" style="text-decoration:none" download="" hidden="">MO4</a>
								<input type="button" id="report_btn_30" class="formbutton" value="Cost Rpt7" onClick="generate_report('preCostRpt7');" style="display:none;" />
								<input type="button" id="report_btn_31" class="formbutton" value="BOM Rpt4 V2" onClick="generate_report('bomRpt4_v2');" style="display:none;" />
                            </td>
                        </tr>
                    </table>
                    </form>
                </fieldset>
                </td>
                <td width="84%" valign="top" id="cost_container"></td>
            </tr>
        </table>
    </div>
    <div style="display:none;" id="data_panel"></div>
    </div>
</body>
<script>
calculate_confirm_price_dzn();
check_exchange_rate();
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
