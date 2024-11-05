<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Garments Pre Cost Entry V2.
Functionality	         :
JS Functions	         :
Created by		         :	Monzu
Creation date 	         : 	18-10-2012
Updated by 		         :
Update date		         :
Comments		         :  This version  is oracle Compatible
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];
$field_lavel_access = array();
if(count($_SESSION['logic_erp']['data_arr'][158])>0)
{
	//$field_lavel_access = $_SESSION['logic_erp']['data_arr'][158];
	//$data_arr= json_encode($field_lavel_access); 
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Pre Cost Entry V2","../../", 1, 1, $unicode,1,'');
$qcCons_from=return_field_value("excut_source","variable_order_tracking","excut_source=2 and variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");
?>

<script type="text/javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<?=$permission; ?>';
	var user_level = '<?=$user_level; ?>';
	var cmValidation='<?=$qcCons_from; ?>';
	

var yarn_conv_actual_cost_dzn_calculation_change='<?=$yarn_conv_actual_cost_dzn_calculation_change; ?>';//ISD-23-03433
//alert(yarn_conv_actual_cost_dzn_calculation_change);
var mandatory_field=new Array();
var mandatory_message=new Array();
var field_level_data=new Array();
	//alert(cmValidation);
var israte_popup=2;
<?
//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][158] );
if(isset($_SESSION['logic_erp']['data_arr'][158]))
{
	$field_lavel_access = $_SESSION['logic_erp']['data_arr'][158];
	//echo count($_SESSION['logic_erp']['data_arr'][425]);
	$data_arr= json_encode($field_lavel_access);
	echo "var field_level_data= ". $data_arr . ";\n";
	//echo count($_SESSION['logic_erp']['data_arr'][425]);
}
if(isset($_SESSION['logic_erp']['mandatory_field'][158]))
{
	echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][158]) . "';\n";
	echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][158]) . "';\n";
}
//echo "var field_level_data= ". $data_arr . ";\n";

//For Mandatory
//echo $_SESSION['logic_erp']['mandatory_field'][158][5].'kausar'; //die;
/*if($_SESSION['logic_erp']['mandatory_field'][158][5]!="")
{
echo "var mst_mandatory_field = '". ($_SESSION['logic_erp']['mandatory_field'][158][5]) . "';\n";
echo "var mst_mandatory_message = '". ($_SESSION['logic_erp']['mandatory_message'][158][5]) . "';\n";
}*/
?>

var str_construction = [ <? echo substr(return_library_autocomplete("select construction from wo_pri_quo_fabric_cost_dtls group by construction ", "construction" ), 0, -1); ?> ];
var str_composition = [ <? echo substr(return_library_autocomplete("select composition from wo_pri_quo_fabric_cost_dtls group by composition", "composition" ), 0, -1); ?> ];
var str_incoterm_place = [ <? echo substr(return_library_autocomplete("select incoterm_place from wo_price_quotation group by incoterm_place", "incoterm_place" ), 0, -1); ?> ];
//var str_trimdescription = [ <? //echo substr(return_library_autocomplete("select description from wo_pre_cost_trim_cost_dtls group by description", "description" ), 0, -1); ?> ];

	function trims_description_autocomplete(trim_group,i){
		var description=return_global_ajax_value(trim_group, 'trims_description', '', 'requires/pre_cost_entry_controller_v2');
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
		/*if(type=='tbl_trim_cost'){
			var row_num=$('#tbl_trim_cost tr').length-1;
			for (var i=1; i<=row_num; i++){
				$("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
				});
			}
		}*/
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
			var fabricRow = $('table#tbl_fabric_cost tbody tr').length;
			if(fabricRow!=1){
				var permission_array=permission.split("_");
				var updateid=$('#updateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();

				if(updateid !="" && permission_array[2]==1){

					var is_booking=return_global_ajax_value(updateid+"__1__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
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
						var booking=return_global_ajax_value(updateid, 'delete_row_fabric_cost', '', 'requires/pre_cost_entry_controller_v2');
					}
				}
				/*var index=rowNo-1
				$("table#tbl_fabric_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_fabric_cost tbody tr').length;*/
				
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
						$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
						$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
						$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","fnc_source_for( this.value, "+i+")")
						$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");

					})
				}
			}
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required();

		}
		else if(table_id=='tbl_conversion_cost')
		{
			var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/pre_cost_entry_controller_v2');
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#updateidcoversion_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();

				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__3__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
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
						var booking=return_global_ajax_value(updateid, 'delete_row_conversion_cost', '', 'requires/pre_cost_entry_controller_v2');
					}
				}
				//var index=rowNo-1
				var index = $(tr).closest("tr").index();
				$("table#tbl_conversion_cost tbody tr:eq("+index+")").remove();
				var numRow = $('table#tbl_conversion_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_conversion_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
						
					   $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+",this.value,1)");
					   $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","add_yarn_conversion_cost("+i+")");
					   $('#txtprocessloss_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+",this.value,2)");
					   $('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","("+i+","+conversion_from_chart+")");
					   $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+",this);");
					   $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost',this);");
					   $('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
					   $('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
					   $('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","( "+i+","+conversion_from_chart+")");
					})
					//alert(i)
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
					var is_booking=return_global_ajax_value(updateid+"__2__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
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
						var booking=return_global_ajax_value(updateid, 'delete_row_trim_cost', '', 'requires/pre_cost_entry_controller_v2');
						if(booking==11){
							alert("Booking Found, Delete Not Allowed");
							return;
						}
					}
				}
				var index=rowNo-1
				$("table#tbl_trim_cost tbody tr:eq("+index+")").remove();
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
						$('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick"," set_session_large_post_data_trim('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_trim', 'Consumtion Entry Form',"+i+")");
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
					var is_booking=return_global_ajax_value(updateid+"__6__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
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
						var booking=return_global_ajax_value(updateid, 'delete_row_embellishment_cost', '', 'requires/pre_cost_entry_controller_v2');
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
			calculate_main_total()
		}

		else if(table_id=='tbl_wash_cost')
		{
			/*var numRow = $('table#tbl_wash_cost tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_wash_cost tbody tr:last').remove();
			}*/
			var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/pre_cost_entry_controller_v2');
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#embupdateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__6__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
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
						var booking=return_global_ajax_value(updateid, 'delete_row_wash_cost', '', 'requires/pre_cost_entry_controller_v2');
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
					var is_booking=return_global_ajax_value(updateid+"__0__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");

					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller_v2');
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
		var check_is_master_part_saved=return_global_ajax_value(update_id, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v2');
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
				release_freezing();
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
				release_freezing();
				return;
			}
			if(wash_cost!=pre_wash_cost)
			{
				alert("Wash Cost Change Found, Please Save or Update.");
				release_freezing();
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
				release_freezing();
				return;
			}
			//calculate_main_total();
			if(action=="show_fabric_cost_listview")
			{
				var yarn_controller=0;
				show_list_view(update_id+'**'+document.getElementById('txt_quotation_id').value+'**'+document.getElementById('copy_quatation_id').value+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_cost_control_source').value+'**'+yarn_controller+'**'+permission,action,'cost_container','requires/pre_cost_entry_controller_v2','');
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
				show_list_view(update_id+'*'+extra_str+'*'+document.getElementById('txt_quotation_id').value+'*'+document.getElementById('copy_quatation_id').value+'*'+document.getElementById('cbo_company_name').value+'*'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
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
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			}

			if(action=="show_wash_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
			}

			if(action=="show_commission_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
				set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
			}

			if(action=="show_comarcial_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_final_price_dzn_pre_cost').value+'_'+document.getElementById('txt_commission_pre_cost').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
				set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
			}
			//partial pre cost copy -----------------new development kaiyum-------------------------------------------
			if(action=="partial_pre_cost_copy_action")
			{
				var page_link='requires/pre_cost_entry_controller_v2.php?action=partial_pre_cost_copy_popup';
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/pre_cost_entry_controller_v2.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
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
		//alert(row);
		$('#content_'+row).toggle('slow', function() {
			 //get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/size_color_breakdown_controller' );
			 
		});
		if(yarn_conv_actual_cost_dzn_calculation_change==1)
		 {
			 if(row=="yarn_cost") loadTotal(1,'Yarn');
			 if(row=="conversion_cost") loadTotal(1,'Conv');
		 }
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
			var rate_amount = return_ajax_request_value(document.getElementById('updateid_'+i).value, 'rate_amount', 'requires/pre_cost_entry_controller_v2')
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
	function fnc_source_for(val,i)
	{
		if(val==1)
		{
			$('#cbosourceid_'+i).attr('disabled','disabled');
		}
		else
		{
			$('#cbosourceid_'+i).removeAttr('disabled','disabled');
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
			$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
			$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
			$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","fnc_source_for( this.value, "+i+")");
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
			//$('#cbosourceid_'+i).removeAttr('disabled');
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
		var page_link='requires/pre_cost_entry_controller_v2.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
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
		/*if (form_validation('txtbodypart_'+i+'*cbocolortype_'+i+'*cbofabricsource_'+i+'*cbofabricnature_'+i,'Body Part*Color Type*Fabric Source*Fabric Nature')==false)
		{
			release_freezing();
			return;
		}*/
		var cbo_company_name=$('#cbo_company_name').val();
		var txtbodypart=$('#txtbodypart_'+i).val();
		var cbocolortype=$('#cbocolortype_'+i).val();
		var cbofabricsource=$('#cbofabricsource_'+i).val();
		var txt_job_no=$('#txt_job_no').val();
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v2.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid+'&cbo_company_name='+cbo_company_name+'&txtbodypart='+txtbodypart+'&cbocolortype='+cbocolortype+'&cbofabricsource='+cbofabricsource+'&txt_job_no='+txt_job_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../');
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
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
		var priQutFabId_costControlSource=document.getElementById('prifabcostdtlsid_'+trorder).value+'_'+document.getElementById('txt_cost_control_source').value;
		var pre_cost_fabric_cost_dtls_id=document.getElementById('updateid_'+trorder).value;
		var precostapproved=document.getElementById('precostapproved_'+trorder).value;
		var cbofabricsource=document.getElementById('cbofabricsource_'+trorder).value;
		var uom=document.getElementById('uom_'+trorder).value;
		var consumptionbasis=document.getElementById('consumptionbasis_'+trorder).value;
		var budgeton=document.getElementById('budgeton_'+trorder).value;
		
		var last_app_id= $('#fabricdescription_'+trorder).attr('last_app_id');
		//alert(last_app_id);
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
		var page_link=page_link+'&body_part_id='+body_part_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_company_id='+cbo_company_id+'&cbofabricnature_id='+cbofabricnature_id+'&calculated_conss='+calculated_conss+'&hid_fab_cons_in_quotation_variable='+hid_fab_cons_in_quotation_variable+'&txtgsmweight='+txtgsmweight+'&txt_job_no='+txt_job_no+'&cbogmtsitem='+cbogmtsitem+'&garments_nature='+garments_nature+'&cbo_approved_status='+cbo_approved_status+'&priQutFabId_costControlSource='+priQutFabId_costControlSource+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved+'&cbofabricsource='+cbofabricsource+'&uom='+uom+'&budgeton='+budgeton;
		//alert(page_link)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=455px,center=1,resize=1,scrolling=0','../');
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
			if(last_app_id==2) //Color size change synchronize check
			{
				$('#lastappidchk_'+trorder).val(1);
			}
			var index=trorder-1;
			var tr=$("#tbl_fabric_cost tbody tr:eq("+index+")");
			tr.css('background-color', 'green');
			$('#txtconsumption_'+trorder).css('background-color', 'green');
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required()
			var row_num=$('#tbl_fabric_cost tr').length-1;
			if(trorder==row_num)
			{
				document.getElementById('check_input').value=0;
				document.getElementById('is_click_cons_box').value=0;
			}
		}
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
		var color_breck_down=encodeURIComponent("'"+$('#colorbreackdown_'+i).val()+"'");
		//var color_breck_down=document.getElementById('colorbreackdown_'+i).value;
		var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
		var pre_cost_fabric_cost_dtls_id=document.getElementById('updateid_'+i).value;
	    var precostapproved=document.getElementById('precostapproved_'+i).value;
		if(cbocolorsizesensitive==3)
		{
			var page_link="requires/pre_cost_entry_controller_v2.php?txt_job_no="+trim(txt_job_no)+"&action=open_color_list_view&color_breck_down="+color_breck_down+"&cbogmtsitem="+cbogmtsitem+"&cbo_company_id="+cbo_company_id+"&cbo_buyer_name="+cbo_buyer_name+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved;
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
		var var_mandatory_id= $('#var_mandatory_id').val();
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
			get_php_form_data(txt_job_no, 'check_data_mismass', "requires/pre_cost_entry_controller_v2" );
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
			var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/pre_cost_entry_controller_v2')
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(2);
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
				if(number_format(txt_fabric_pre_cost,6)>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation');
					release_freezing();
					return;
				}
			}
		}

	    var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
	    var row_num=$('#tbl_fabric_cost tr').length-1;
		var data_all=""; var z=1;var data_all2="";
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
			if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*txtbodyparttype_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*txtgsmweight_'+i+'*uom_'+i,'Gmts Item *Body Part*Body Part Type*Fabric Nature*Color Type*Construction*Composition*Consunption*Fabric Source*GSM*UOM')==false)
			{
				release_freezing();
				return;
			}
			var dia_widthtype=$('#cbowidthdiatype_'+i).val();
			if(var_mandatory_id==1 && dia_widthtype==0) // Variable-> Budget v2 mandatory
			{
				alert('Please select Width/Dia Type');
			 	document.getElementById('cbowidthdiatype_'+i).focus();
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
			//var colorbreackdown_gg=$('#colorbreackdown_'+i).val();
			//var colorbreackdown_g=colorbreackdown_gg.replace("+","[plus]");
			var colorbreackdown=encodeURIComponent("'"+$('#colorbreackdown_'+i).val()+"'");
			var fabricdescription=encodeURIComponent("'"+$('#fabricdescription_'+i).val()+"'");
			var composition=encodeURIComponent("'"+$('#composition_'+i).val()+"'");
		 
			//+"'"+"&colorbreackdown_" + z + "='" + $('#colorbreackdown_'+i).val() 
			data_all+="&consumptionbasis_" + z + "='" + $('#consumptionbasis_'+i).val()+"'"+"&cbogmtsitem_" + z + "='" + $('#cbogmtsitem_'+i).val()+"'"+"&txtbodypart_" + z + "='" + $('#txtbodypart_'+i).val()+"'"+"&cbofabricnature_" + z + "='" + $('#cbofabricnature_'+i).val()+"'"+"&cbocolortype_" + z + "='" + $('#cbocolortype_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'"+"&construction_" + z + "='" + $('#construction_'+i).val()+"'"+"&composition_" + z + "='" + $('#composition_'+i).val()+"'"+"&fabricdescription_" + z + "='" + $('#fabricdescription_'+i).val()+"'"+"&txtgsmweight_" + z + "='" + $('#txtgsmweight_'+i).val()+"'"+"&cbocolorsizesensitive_" + z + "='" + $('#cbocolorsizesensitive_'+i).val()+"'"+"&txtcolor_" + z + "='" + $('#txtcolor_'+i).val()+"'"+"&txtconsumption_" + z + "='" + $('#txtconsumption_'+i).val()+"'"+"&cbofabricsource_" + z + "='" + $('#cbofabricsource_'+i).val()+"'"+"&cbonominasupplier_" + z + "='" + $('#cbonominasupplier_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtfinishconsumption_" + z + "='" + $('#txtfinishconsumption_'+i).val()+"'"+"&txtavgprocessloss_" + z + "='" + $('#txtavgprocessloss_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&msmntbreackdown_" + z + "='" + $('#msmntbreackdown_'+i).val()+"'"+"&updateid_" + z + "='" + $('#updateid_'+i).val()+"'"+"&processlossmethod_" + z + "='" + $('#processlossmethod_'+i).val()+"'"+"&yarnbreackdown_" + z + "='" + $('#yarnbreackdown_'+i).val()+"'"+"&markerbreackdown_" + z + "='" + $('#markerbreackdown_'+i).val()+"'"+"&cbowidthdiatype_" + z + "='" + $('#cbowidthdiatype_'+i).val()+"'"+"&avgtxtconsumption_" + z + "='" + $('#avgtxtconsumption_'+i).val()+"'"+"&avgtxtgsmweight_" + z + "='" + $('#avgtxtgsmweight_'+i).val()+"'"+"&plancutqty_" + z + "='" + $('#plancutqty_'+i).val()+"'"+"&jobplancutqty_" + z + "='" + $('#jobplancutqty_'+i).val()+"'"+"&isclickedconsinput_" + z + "='" + $('#isclickedconsinput_'+i).val()+"'"+"&oldlibyarncountdeterminationid_" + z + "='" + $('#oldlibyarncountdeterminationid_'+i).val()+"'"+"&isconspopupupdate_" + z + "='" + $('#isconspopupupdate_'+i).val()+"'"+"&uom_" + z + "='" + $('#uom_'+i).val()+"'"+"&txtbodyparttype_" + z + "='" + $('#txtbodyparttype_'+i).val()+"'"+"&cbosourceid_" + z + "='" + $('#cbosourceid_'+i).val()+"'"+"&lastappidchk_" + z + "='" + $('#lastappidchk_'+i).val()+"'"+"&prifabcostdtlsid_" + z + "='" + $('#prifabcostdtlsid_'+i).val()+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'"+"&hiddencolorsizesensitive_" + z + "='" + $('#hiddencolorsizesensitive_'+i).val()+"'"+"&budgeton_" + z + "='" + $('#budgeton_'+i).val()+"'";
			
			data_all2+="&colorbreackdown_" + z + "=" +colorbreackdown+"&fabricdescription_" + z + "=" + fabricdescription+"&composition_" + z + "=" + composition+"";
			
			z++;
		}
		
		var data="action=save_update_delet_fabric_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_costing_per*update_id*hidd_job_id*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtwoven_fin_sum*txtknit_fin_sum*txtamount_sum*avg*txtwoven_sum_production*txtknit_sum_production*txtwoven_fin_sum_production*txtknit_fin_sum_production*txtwoven_sum_purchase*txtknit_sum_purchase*txtwoven_fin_sum_purchase*txtknit_fin_sum_purchase*txtwoven_amount_sum_purchase*txtkint_amount_sum_purchase*txt_quotation_id*copy_quatation_id*txt_cost_control_source',"../../")+data_all+data_all2;
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}

			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]==17) //Fabric Determintaion when zero/Blank Found
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			if(reponse[0]==6)
			{
				alert(reponse[1]);
				var index=reponse[2]-1;
				var tr=$("#tbl_fabric_cost tbody tr:eq("+index+")");
				tr.css('background-color', 'RED');
				$('#txtconsumption_'+reponse[2]).css('background-color', 'RED');
				release_freezing();
				return;
			}

			if(reponse[0]==15)
			{
				 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value*1;
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);
				if(reponse[0]==10)
				{
					release_freezing();
					return;
				}

				if(reponse[0]==0 || reponse[0]==1)
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
					get_php_form_data(txt_job_no, 'check_data_mismass', "requires/pre_cost_entry_controller_v2" );
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Stripe Color Details PopUp", 'width=1200px,height=500px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	function open_fabric_price_popup(){
		var txt_job_no=document.getElementById('txt_job_no').value;
		var hidd_job_id=document.getElementById('hidd_job_id').value;
		if(txt_job_no=='' && hidd_job_id=='')
		{
			alert("Please Save Pre Cost")
			release_freezing();
			return;
		}
		var index_page=$('#index_page', window.parent.document).val();
		var page_link="requires/pre_cost_entry_controller_v2.php?action=fabric_price_popup&permission="+permission+'&txt_job_no='+txt_job_no+'&hidd_job_id='+hidd_job_id+'&index_page='+index_page;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Fabric Price Details", 'width=800px,height=500px,center=1,resize=1,scrolling=0','../')
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
		var page_link='requires/pre_cost_entry_controller_v2.php?action=fabric_add_image_popup&txt_job_no='+txt_job_no+'&cbo_company_name='+cbo_company_name;
		//var page_link="requires/pre_cost_entry_controller_v2.php?permission="+permission+'&txt_job_no='+txt_job_no+'&index_page='+index_page;
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
	function control_composition(id,td,type)
	{
		var r=confirm("Do you want change Composition ?");
		var old_value=document.getElementById('componeid_'+id).value;
		var new_value=document.getElementById('cbocompone_'+id).value;
		if(r==false){
			document.getElementById('cbocompone_'+id).value=old_value;
		}
		else
		{
			document.getElementById('componeid_'+id).value=new_value;
			return;
		}
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
		var txt_costing_per = document.getElementById('cbo_costing_per').value;
		var cons_dzn=0;
		if(txt_costing_per==1) cons_dzn=12;
		if(txt_costing_per==2) cons_dzn=1;
		if(txt_costing_per==3) cons_dzn=24;
		if(txt_costing_per==4) cons_dzn=36;
		if(txt_costing_per==5) cons_dzn=48;
		
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
			if(yarn_conv_actual_cost_dzn_calculation_change==1)//ISD-23-03433
			{
				var cbotypek=document.getElementById('cbotype_'+k).value;
				var orderqty=$('#totalyqty_'+k).attr('poqty')*1;
				var planqty=$('#totalyqty_'+k).attr('planqty')*1;
				if(cbocount==cbocountk && cbocompone==cbocomponek && percentone==percentonek && cbotype==cbotypek)
				{
					document.getElementById('txtrateyarn_'+k).value=txtrateyarn;
					var totyarnreq=$('#totalyqty_'+k).val()*1;
					var totalyarnamt=(totyarnreq*1)*(txtrateyarn*1);
					var yarnamtdzn=(totalyarnamt*1)/(orderqty*1);
					document.getElementById('txtamountyarn_'+k).value=number_format_common((yarnamtdzn*cons_dzn),1,0,document.getElementById('cbo_currercy').value);
				}
				else
				{
					var totyarnreq=$('#totalyqty_'+k).val()*1;
					var txtrateyarn=$('#txtrateyarn_'+k).val()*1;
					var totalyarnamt=(totyarnreq*1)*(txtrateyarn*1);
					var yarnamtdzn=(totalyarnamt*1)/(orderqty*1);
					document.getElementById('txtamountyarn_'+k).value=number_format_common((yarnamtdzn*cons_dzn),1,0,document.getElementById('cbo_currercy').value);
				}
			}
			else
			{
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
		var yarn_rate = return_ajax_request_value(cbocount+"_"+cbocompone+"_"+percentone+"_"+cbotype+"_"+supplier_id+"_"+txt_costing_date, 'get_yarn_rate', 'requires/pre_cost_entry_controller_v2');
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
			alert("Delete Restricted.")
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
				alert("Buyer Profit % is greater-than Margin Dzn %.");
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
		if(cost_control_source==1 || cost_control_source==5  || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(2);
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
				if(number_format(txt_fabric_pre_cost,6)>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation.');
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
		var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsumptionyarn_sum*txtamountyarn_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
				alert("This Costing is Approved.");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved.");
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}
			
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]=='purReq')
			{
				alert("Purchase Requisition Found. Req. No:"+reponse[1]);
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
				release_freezing();
			}
		}
	}
// Yarn Cost End -------------------------------------------
//Conversion Cost-------------------------------------------
	function add_yarn_conversion_cost(row_id)
	{
		var current_row=row_id;
		$('#covseq_'+row_id).val(0);
		$('#cbotypeconversion_'+row_id).val(0);
		var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/pre_cost_entry_controller_v2');
		var yarn_count_deter_id=$('#cbocosthead_'+row_id).val();
		var yarn_process_loss_data=return_global_ajax_value(yarn_count_deter_id, 'process_loss_from_yarn', '', 'requires/pre_cost_entry_controller_v2');
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
	
	function add_break_down_tr_conversion_cost( i,conversion_from_chart,tr )
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
			var tr=$("#tbl_conversion_cost tbody tr:eq("+index+")");
			
			 /*$("#tbl_conversion_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			  }).end().appendTo("#tbl_conversion_cost");*/
			  
			  
			  var cl=$("#tbl_conversion_cost tbody tr:eq("+index+")").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
					});
				}).end();
			tr.after(cl);
			  
			  if($('#covseq_'+i).val()!= "")
				{
					$('#covseq_'+i).val( 0 );
				}
				$('#colorbreakdownaop_'+i).val("");
			  $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+",this.value,1)");
			  $('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","add_yarn_conversion_cost("+i+")");
			  $('#txtprocessloss_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+",this.value,2)");
			  $('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
			  $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+",this);");
			  $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost',this);");
			  $('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			  $('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			  $('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","( "+i+","+conversion_from_chart+")");
			  $('#totalqty_'+i).removeAttr("onClick").attr("onClick","loadTotal( "+i+",'Conv' )");
			  $('#updateidcoversion_'+i).val("");
			  $('#cbocosthead_'+i).val("");
			  $('#cbotypeconversion_'+i).val("");
			  $('#txtprocessloss_'+i).val("");
			  $('#txtreqqnty_'+i).val("");//document.getElementById('txtknit_sum').value
			  $('#txtavgreqqnty_'+i).val("");//document.getElementById('txtknit_sum_production').value
			  $('#txtchargeunit_'+i).val("");
			  $('#txtamountconversion_'+i).val("");
			  $('#totalcqty_'+i).val("");
			  $('#totalcamount_'+i).val("");
			  $('#colorbreakdown_'+i).val(""); 
			//  alert(i);
			 // $('#colorbreakdownaop_'+i).val("");
			  $('#coversionchargelibraryid_'+i).val("");
			  
			  $('#convfabtd_'+i).attr( 'title','Process Loss not Set In Library' );
			  
			  set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
			  set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
			  set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
			  set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		//}
	}

	function set_conversion_charge_unit(i,conversion_from_chart) //conversion_aop_color_popup
	{
		var cost_head= $('#cbotypeconversion_'+i).val();
		var var_conv_aop_chart_from= $('#var_conv_aop_chart_id').val();
		//alert(cost_head+'='+conversion_from_chart);
		if(cost_head==31 || cost_head==1 || cost_head==265 || cost_head==35) // Issue Id=11140 For Lariz As per Nasir//265 & 35 added by issue id ISD-23-26371
		{
			conversion_from_chart=conversion_from_chart;
		}
		else conversion_from_chart=2;
		set_conversion_qnty(i,2,3);
		if(cost_head==30 || cost_head==31)
		{
			document.getElementById('txtchargeunit_'+i).readOnly=true;
			charge_unit_color_popup(i,conversion_from_chart);
			$('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
		}
		else
		{
			if(conversion_from_chart==1)
			{
				if(israte_popup==2)
				{
					if(cost_head==35 || cost_head==153 || cost_head==134 || cost_head==483)
					{
						document.getElementById('txtchargeunit_'+i).readOnly=false;
						document.getElementById('txtchargeunit_'+i).focus();
					}
					else
					{
						document.getElementById('txtchargeunit_'+i).readOnly=true;
					}
					set_conversion_charge_unit_pop_up(i)
				}
				else
				{
					if(cost_head==1 || cost_head==265 || cost_head==35)//265 & 35 added by issue id ISD-23-26371
					{
						if(cost_head==35)
						{
							document.getElementById('txtchargeunit_'+i).readOnly=false;
							document.getElementById('txtchargeunit_'+i).focus();
						}
						else
						{
							document.getElementById('txtchargeunit_'+i).readOnly=true;
						}
						set_conversion_charge_unit_pop_up(i)
					}else{
						document.getElementById('txtchargeunit_'+i).readOnly=false;
					}
				}
				$('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
			}
			else
			{
				if(cost_head==35 && var_conv_aop_chart_from==1) //AOP Chart From Lib
				{
					//alert(conversion_from_chart+'='+var_conv_aop_chart_from);
					 set_aop_conversion_charge_unit(i,var_conv_aop_chart_from);
					 $('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_aop_conversion_charge_unit( "+i+","+var_conv_aop_chart_from+")");
				}
				else{
					document.getElementById('txtchargeunit_'+i).readOnly=false
					$('#txtchargeunit_'+i).removeAttr("onClick");
					document.getElementById('colorbreakdownaop_'+i).value='';
				}
			}
			document.getElementById('colorbreakdown_'+i).value='';
		}
	}
	
	function set_aop_conversion_charge_unit(i,conversion_from_chart)
	{
		//alert(conversion_from_chart);
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value
		var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
		var cbo_currercy= document.getElementById('cbo_currercy').value;
		var cbocosthead=document.getElementById('cbocosthead_'+i).value;
		var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
		var job_no=document.getElementById('txt_job_no').value;
		var colorbreakdown=document.getElementById('colorbreakdownaop_'+i).value;
	 	var coversionchargelibraryid=document.getElementById('coversionchargelibraryid_'+i).value;
		var current_covseq= document.getElementById('covseq_'+i).value;
		var page_link="requires/pre_cost_entry_controller_v2.php?action=conversion_aop_color_popup&cbocosthead="+cbocosthead+'&job_no='+job_no+'&colorbreakdown='+colorbreakdown+'&conversion_from_chart='+conversion_from_chart+'&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_currercy='+cbo_currercy+'&coversionchargelibraryid='+coversionchargelibraryid;
		if(cbotypeconversion==35){
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'AOP Color Pop Up', 'width=1460px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function(){
			    var theform=this.contentDoc.forms[0];
				var color_breck_down=this.contentDoc.getElementById("color_breck_down");
				//var chargelibid_breck_down=this.contentDoc.getElementById("chargelibid_breck_down");
				var avg_total_value=this.contentDoc.getElementById("avg_total_value");
				var avg_total_cons=this.contentDoc.getElementById("avg_total_cons");
				if (color_breck_down.value!=""){
					document.getElementById('colorbreakdownaop_'+i).value=color_breck_down.value;
					document.getElementById('txtchargeunit_'+i).value=avg_total_value.value;
					var txtprocessloss=document.getElementById('txtprocessloss_'+i).value*1;
					var cons=avg_total_cons.value;
					var reqqty=0; var avgreqqnty=0;
					for(var k=1; k<i; k++){
						var covseq= document.getElementById('covseq_'+k).value;
						if(current_covseq==covseq){
							reqqty=document.getElementById('txtavgreqqnty_'+k).value;
							console.log(reqqty);
							//avgreqqnty=reqqty-(reqqty*txtprocessloss)/100
						}
					}
					if(reqqty>0){
						var avg_req=reqqty;//-(cons*txtprocessloss)/100;
						var avg_cons=reqqty-(reqqty*txtprocessloss)/100;
					}
					else{
						//var avg_req=cons-(cons*txtprocessloss)/100;//comments by kausar
						var avg_req=cons;//-(cons*txtprocessloss)/100;
						var avg_cons=cons-(cons*txtprocessloss)/100;
					}
					
					document.getElementById('txtreqqnty_'+i).value=number_format_common(avg_req, 5, 0);
					document.getElementById('txtavgreqqnty_'+i).value=number_format_common(avg_cons, 5, 0);
				//	document.getElementById('coversionchargelibraryid_'+i).value=chargelibid_breck_down.value;
					calculate_conversion_cost(i);
				}
			}
		}
	}
	 
	function set_conversion_charge_unit_pop_up(i) 
	{
		//alert(i);
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
			var page_link='requires/pre_cost_entry_controller_v2.php?action=conversion_chart_popup&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&coversionchargelibraryid='+coversionchargelibraryid;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Conversion Chart', 'width=1460px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var charge_id=this.contentDoc.getElementById("charge_id");
				var charge_value=this.contentDoc.getElementById("charge_value");

				document.getElementById('coversionchargelibraryid_'+i).value=charge_id.value;
				
				document.getElementById('txtchargeunit_'+i).value=number_format_common(charge_value.value/txt_exchange_rate,5,0,document.getElementById('cbo_currercy').value);
				
				if(yarn_conv_actual_cost_dzn_calculation_change==1)//ISD-23-03433
				{
					var txt_costing_per = document.getElementById('cbo_costing_per').value;
					var cons_dzn=0;
					if(txt_costing_per==1) cons_dzn=12;
					if(txt_costing_per==2) cons_dzn=1;
					if(txt_costing_per==3) cons_dzn=24;
					if(txt_costing_per==4) cons_dzn=36;
					if(txt_costing_per==5) cons_dzn=48;
					
					var orderqty=$('#totalcqty_'+i).attr('poqty')*1;
					var planqty=$('#totalcqty_'+i).attr('planqty')*1;
						
					var totconvreq=$('#totalcqty_'+i).val()*1;
					var txtrateconv=$('#txtchargeunit_'+i).val()*1;
					var totalconvamt=(totconvreq*1)*(txtrateconv*1);
					var convamtdzn=(totalconvamt*1)/(orderqty*1);
					document.getElementById('txtamountconversion_'+i).value=number_format_common((convamtdzn*cons_dzn),1,0,document.getElementById('cbo_currercy').value);
				}
				else
				{
					math_operation('txtamountconversion_'+i,'txtavgreqqnty_'+i+'*txtchargeunit_'+i,'*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
				}
				
				//math_operation( 'txtamountconversion_'+i, 'txtavgreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
				set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
			}
		}
	}

	function set_conversion_qnty(i,fabid,type)
	{
		//alert(i+'--'+value+'--'+type);
		var row_num=$('#tbl_conversion_cost tr').length-1;
	  var cbocosthead= document.getElementById('cbocosthead_'+i).value;
	  var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
	  var txtprocessloss=document.getElementById('txtprocessloss_'+i).value*1;
	  var updateidcoversion=$('#updateidcoversion_'+i).val();
	  $('#applastidchk_'+i).val(1);
	  if(cbotypeconversion==30 || cbotypeconversion==31){
		 // return; ISD-22-12944
	  }
	  if(cbocosthead !=0){
		  var conversion_qnty=trim(return_global_ajax_value(cbocosthead+"_"+cbotypeconversion+"_"+txtprocessloss+"_"+updateidcoversion, 'set_conversion_qnty', '', 'requires/pre_cost_entry_controller_v2'));
	  }
	  if(cbocosthead ==0){
		  var conversion_qnty=document.getElementById('txtknit_sum').value+"_"+document.getElementById('txtknit_sum_production').value+"_"+txtprocessloss+"_"+updateidcoversion;
	  }
	  conversion_qnty=conversion_qnty.split("_");
	  //Added by zakaria joy; pls discuss before delete
	  var req_qty=0; var avg_reqqnty=0;
	  var cbo_costhead=document.getElementById('cbocosthead_'+i).value;
	  var reqqty=0; var avgreqqnty=0;
	  for(var k=1; k<i; k++){		
		var running_costhead= document.getElementById('cbocosthead_'+k).value;		
		if(cbo_costhead==running_costhead){
			reqqty=document.getElementById('txtavgreqqnty_'+k).value;
			avgreqqnty=reqqty-(reqqty*txtprocessloss)/100
		}
	  }
	  if(reqqty>0 && avgreqqnty>0){
		req_qty=reqqty;
		avg_reqqnty=avgreqqnty;
	  }
	  else{
		req_qty=conversion_qnty[0];
		avg_reqqnty=conversion_qnty[1];
	  }
	  document.getElementById('txtreqqnty_'+i).value=trim(req_qty);
	  document.getElementById('txtavgreqqnty_'+i).value=trim(avg_reqqnty);
	  document.getElementById('covseq_'+i).value=trim(conversion_qnty[4]);

	  var pre_req_qty=0; var pre_avgreqqnty=0; var current_txtprocessloss=0; var current_avgreqqnty=0;
	  if(row_num!=i){
		var next_row=i+1;
		if(type==1){
			var current_costhead=fabid;
		}
		else{
			var current_costhead= document.getElementById('cbocosthead_'+i).value;
		}
		
		var current_reqqty= document.getElementById('txtavgreqqnty_'+i).value;
		for(var z=i+1; z<=row_num; z++){
			var costhead= document.getElementById('cbocosthead_'+z).value;
			console.log(z+'--'+current_costhead+'--'+costhead);
			if(current_costhead==costhead){
				current_txtprocessloss=document.getElementById('txtprocessloss_'+z).value*1;
				if(pre_avgreqqnty>0){
					pre_req_qty=pre_avgreqqnty;
				}
				else{
					pre_req_qty =current_reqqty;
				}				
				pre_avgreqqnty=pre_req_qty-(pre_req_qty*current_txtprocessloss)/100;				
				document.getElementById('txtreqqnty_'+z).value=pre_req_qty;
	  			document.getElementById('txtavgreqqnty_'+z).value=pre_avgreqqnty;
				pre_req_qty=pre_avgreqqnty;
			}
		}
	  }

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
				$('#txtprocessloss_'+i).attr( 'readonly',false );
				$('#txtprocessloss_'+i).css({ 'background': 'grey' });
			}else{
				$('#txtprocessloss_'+i).attr( 'readonly',false );
			}
		}
	  calculate_conversion_cost(i);
	}

	function calculate_conversion_cost(i){
		$('#applastidchk_'+i).val(1);
		
		if(yarn_conv_actual_cost_dzn_calculation_change==1)//ISD-23-03433
		{
			var txt_costing_per = document.getElementById('cbo_costing_per').value;
			var cons_dzn=0;
			if(txt_costing_per==1) cons_dzn=12;
			if(txt_costing_per==2) cons_dzn=1;
			if(txt_costing_per==3) cons_dzn=24;
			if(txt_costing_per==4) cons_dzn=36;
			if(txt_costing_per==5) cons_dzn=48;
			
			var orderqty=$('#totalcqty_'+i).attr('poqty')*1;
			var planqty=$('#totalcqty_'+i).attr('planqty')*1;
				
			var totconvreq=$('#totalcqty_'+i).val()*1;
			var txtrateconv=$('#txtchargeunit_'+i).val()*1;
			var totalconvamt=(totconvreq*1)*(txtrateconv*1);
			var convamtdzn=(totalconvamt*1)/(orderqty*1);
			document.getElementById('txtamountconversion_'+i).value=number_format_common((convamtdzn*cons_dzn),1,0,document.getElementById('cbo_currercy').value);
		}
		else
		{
			math_operation( 'txtamountconversion_'+i, 'txtavgreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		}
		
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
		var current_covseq= document.getElementById('covseq_'+i).value;
		var page_link="requires/pre_cost_entry_controller_v2.php?action=conversion_color_popup&cbocosthead="+cbocosthead+'&job_no='+job_no+'&colorbreakdown='+colorbreakdown+'&conversion_from_chart='+conversion_from_chart+'&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_currercy='+cbo_currercy+'&coversionchargelibraryid='+coversionchargelibraryid;
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
					var reqqty=0; var avgreqqnty=0;
					for(var k=1; k<i; k++){
						var covseq= document.getElementById('covseq_'+k).value;
						if(current_covseq==covseq){
							reqqty=document.getElementById('txtavgreqqnty_'+k).value;
							console.log(reqqty);
							//avgreqqnty=reqqty-(reqqty*txtprocessloss)/100
						}
					}
					if(reqqty>0){
						var avg_req=reqqty;//-(cons*txtprocessloss)/100;
						var avg_cons=reqqty-(reqqty*txtprocessloss)/100;
					}
					else{
						//var avg_req=cons-(cons*txtprocessloss)/100;//comments by kausar
						var avg_req=cons;//-(cons*txtprocessloss)/100;
						var avg_cons=cons-(cons*txtprocessloss)/100;
					}
					
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(2);
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
				if(number_format(txt_fabric_pre_cost,6)>pri_fabric_pre_cost)
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
			if (form_validation('update_id*cbocosthead_'+i+'*cbotypeconversion_'+i+'*txtchargeunit_'+i,'Company Name*Fabric Description*Process*Charge')==false)
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
					if($('#cbocosthead_'+i).val()==$('#cbocosthead_'+k).val() && $('#cbotypeconversion_'+i).val()==$('#cbotypeconversion_'+k).val())
					{
						alert("Same Fabric and Same Process Duplication Not Allowed.");//ISD-22-28884
						release_freezing();
						return;
					}
				}
			}
			//colorbreakdownaop_5
			data_all+="&cbocosthead_" + z + "='" + $('#cbocosthead_'+i).val()+"'"+"&cbotypeconversion_" + z + "='" + $('#cbotypeconversion_'+i).val()+"'"+"&txtprocessloss_" + z + "='" + $('#txtprocessloss_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtavgreqqnty_" + z + "='" + $('#txtavgreqqnty_'+i).val()+"'"+"&txtchargeunit_" + z + "='" + $('#txtchargeunit_'+i).val()+"'"+"&txtamountconversion_" + z + "='" + $('#txtamountconversion_'+i).val()+"'"+"&cbostatusconversion_" + z + "='" + $('#cbostatusconversion_'+i).val()+"'"+"&updateidcoversion_" + z + "='" + $('#updateidcoversion_'+i).val()+"'"+"&colorbreakdown_" + z + "='" + $('#colorbreakdown_'+i).val()+"'"+"&colorbreakdownaop_" + z + "='" + $('#colorbreakdownaop_'+i).val()+"'"+"&coversionchargelibraryid_" + z + "='" + $('#coversionchargelibraryid_'+i).val()+"'"+"&lastappidchk_" + z + "='" + $('#applastidchk_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_fabric_conversion_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*txt_cost_control_source*cbo_company_name*txt_quotation_id*copy_quatation_id*txtconreqnty_sum*txtconchargeunit_sum*txtconamount_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
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

					
				}

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
			   /*$("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
				});*/
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
			  $('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick"," set_session_large_post_data_trim('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_trim', 'Consumtion Entry Form',"+i+")");
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
			  $('#cbogrouptext_'+i).val("");
			  $('#cbogroup_'+i).val("");
			  $('#countrytext_'+i).val("");
			  $('#country_'+i).val("");

			  $('#txtdescription_'+i).val("");
			  $('#txtsupref_'+i).val("");
			  $('#cboconsuom_'+i).val(0);
			  $('#cbosourceid_'+i).val(0);
			  $('#consbreckdown_'+i).val("");
			  $('#txtconsdzngmts_'+i).val("");
			  $('#txttrimrate_'+i).val("");
			  $('#txttrimamount_'+i).val("");
			  $('#txtnominasupplier_'+i).val("");
			  $('#cbonominasupplier_'+i).val("");
			  $('#calculatorstring_'+i).val("");
			  $('#totalqty_'+i).val("");
			  $('#totalamount_'+i).val("");
			 /* $("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
			  });*/
			  set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			  set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
			  set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
			 // set_multiselect('cbogroup_'+i,'0','0','','');
		}
	}

	function loadTotal(i,type)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;

		var res=return_global_ajax_value(txt_job_no+'__'+type, 'load_total_qtyAmount', '', 'requires/pre_cost_entry_controller_v2');
		var resObj=JSON.parse(res);
		if(type=='trim')
		{
			var updateidtrim=document.getElementById('updateidtrim_'+i).value;
			//var resObj=JSON.parse(res);
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
			//var resObj=JSON.parse(res);
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
					if(yarn_conv_actual_cost_dzn_calculation_change==1)
					{
						$('#totalyqty_'+j).attr('poqty',resObj.poqty[updateidyarn]);
						$('#totalyqty_'+j).attr('planqty',resObj.planqty[updateidyarn]);
					}
					//alert(resObj.poqty[updateidyarn]+'--'+resObj.planqty[updateidyarn])
					var tot_yarn_amt=(tot_yarn_amt*1)+resObj.amt[updateidyarn]*1;
					var total_yarn_qty=(total_yarn_qty*1)+resObj.qty[updateidyarn]*1;
				}
			}
			$('#totalyarnamount').val(tot_yarn_amt);
			$('#totalyarnqty').val(total_yarn_qty);
		}
		if(type=='Conv')
		{
			var updateidconv=document.getElementById('updateidcoversion_'+i).value;
			var row_num=$('#tbl_conversion_cost tr').length-1;
			//alert(row_num)
			var tot_conv_amt=''; var total_conv_qty='';
			for (var j=1; j<=row_num; j++)
			{
				var updateidconv=document.getElementById('updateidcoversion_'+j).value;
				//alert(resObj.qty[updateidconv]);
				if(updateidconv==""){
					//alert("Save the row first");
					continue;
				}
				if(resObj.qty[updateidconv]!=undefined){
					document.getElementById('totalcqty_'+j).value=resObj.qty[updateidconv];
					document.getElementById('totalcamount_'+j).value=resObj.amt[updateidconv];
					
					if(yarn_conv_actual_cost_dzn_calculation_change==1)
					{
						$('#totalcqty_'+j).attr('poqty',resObj.poqty[updateidconv]);
						$('#totalcqty_'+j).attr('planqty',resObj.planqty[updateidconv]);
					}
					
					var tot_conv_amt=(tot_conv_amt*1)+resObj.amt[updateidconv]*1;
					var total_conv_qty=(total_conv_qty*1)+resObj.qty[updateidconv]*1;
				}
			}
			$('#txtconareqamt_sum').val(tot_conv_amt);
			$('#txtconareqqty_sum').val(total_conv_qty);
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
		var page_link='requires/pre_cost_entry_controller_v2.php?action=open_country_popup';
		var title='Country';
		page_link=page_link+'&txt_job_no='+txt_job_no+'&txt_country='+trim(txt_country)+'&txt_country_name='+trim(txt_country_name);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=350px,center=1,resize=0,scrolling=0','../')
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
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
		var txtexper=document.getElementById('txtexper_'+trorder).value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var country=document.getElementById('country_'+trorder).value;
		var txttrimrate=document.getElementById('txttrimrate_'+trorder).value;
		var item_id=document.getElementById('item_id').value;
		var calculatorstring=document.getElementById('calculatorstring_'+trorder).value;
		var cbogrouptext=document.getElementById('cbogrouptext_'+trorder).value;
		var updateidtrim=document.getElementById('updateidtrim_'+trorder).value;
		var last_app_id=document.getElementById('txtlastpdateid_'+trorder).value;
		var txtconsdzngmts = document.getElementById('txtconsdzngmts_'+trorder).value;
		var txtdescription=document.getElementById('txtdescription_'+trorder).value;
		var excessper=document.getElementById('excessper_'+trorder).value;
		var totalcons=document.getElementById('totalcons_'+trorder).value;
		//alert(txtexper)
	    //var calculator_parameter=return_global_ajax_value(cbogroup, 'calculator_parameter', '', 'requires/pre_cost_entry_controller_v2');
		var page_link=page_link+'&txt_job_no='+txt_job_no+'&cbo_costing_per='+cbo_costing_per+'&cons_breck_downn='+cons_breck_downn+'&cbo_approved_status='+cbo_approved_status+'&cbogroup='+cbogroup+'&cboconsuom='+cboconsuom+'&tot_set_qnty='+tot_set_qnty+'&country='+country+'&txttrimrate='+txttrimrate+'&item_id='+item_id+'&calculatorstring='+calculatorstring+"&cbogrouptext="+cbogrouptext+"&updateidtrim="+updateidtrim+"&txtconsdzngmts="+txtconsdzngmts+"&txtdescription="+txtdescription+"&txtexper="+txtexper+"&excessper="+excessper+"&totalcons="+totalcons;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title+"  "+cbogrouptext, 'width=1280px,height=460px,center=1,resize=1,scrolling=0','../')
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
			var avg_ex_per=this.contentDoc.getElementById("avg_exper");
			var avg_tot_cons=this.contentDoc.getElementById("avg_cons");
			
			document.getElementById('txtconsdzngmts_'+trorder).value=avg_totcons.value;//number_format_common(avg_totcons, 5, 0);
			document.getElementById('txttrimrate_'+trorder).value=avg_rate.value;
			document.getElementById('txttrimamount_'+trorder).value=number_format_common(avg_amount.value, 6, 0);
			if(last_app_id==2) //Color size change synchronize check
			{
				$('#lastappidchk_'+trorder).val(1);
			}
			document.getElementById('excessper_'+trorder).value=avg_ex_per.value;//avg popup
			document.getElementById('totalcons_'+trorder).value=avg_tot_cons.value;
			document.getElementById('consbreckdown_'+trorder).value=cons_breck_down.value;
			document.getElementById('calculatorstring_'+trorder).value=calculator_string.value;
			var index=trorder-1;
			var tr=$("#tbl_trim_cost tbody tr:eq("+index+")");
			tr.css('background-color', 'green');
			$('#txtconsdzngmts_'+trorder).css('background-color', 'green');
			math_operation( 'txttrimamount_'+trorder, 'txtconsdzngmts_'+trorder+'*txttrimrate_'+trorder, '*','',{dec_type:6,comma:0,currency:document.getElementById('cbo_currercy').value} );
			set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
	        set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
		}
	}

	function trim_rate_popup(i)
	{
	    var cbogroup=document.getElementById('cbogroup_'+i).value;

		var page_link="requires/pre_cost_entry_controller_v2.php?cbogroup="+trim(cbogroup)+"&action=trim_rate_popup_page";
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
		var calculator_parameter=return_global_ajax_value(cbogroup, 'calculator_parameter', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(calculator_parameter)!=0)
		{
			var page_link="requires/pre_cost_entry_controller_v2.php?calculator_parameter="+trim(calculator_parameter)+"&action=calculator_type&cbo_costing_per="+cbo_costing_per;
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
			var booking=return_global_ajax_value(updateidtrim, 'check_trims_booking', '', 'requires/pre_cost_entry_controller_v2');
			if(booking==11)
			{
				alert("Booking Found, Change Not Allowed");
				return;
			}
		}

		var page_link="requires/pre_cost_entry_controller_v2.php?action=openpopup_itemgroup";
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
		var supplier_id_st='';
		var updateidtrim=document.getElementById('updateidtrim_'+inc).value
		if(updateidtrim*1>0){
			var booking=return_global_ajax_value(updateidtrim, 'check_trims_booking', '', 'requires/pre_cost_entry_controller_v2');
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

		var page_link="requires/pre_cost_entry_controller_v2.php?trim_rate_variable="+trim(trim_rate_variable)+"&action=openpopup_trimsupplier&cbogroup="+cbogroup+"&buyer="+buyer+"&nominasupplier="+nominasupplier;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Nominated Supplier PopUp', 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var suppdata=this.contentDoc.getElementById("suppdata").value;
			var csuppdata=this.contentDoc.getElementById("comsuppdata").value;
			//alert(suppdata+'--'+csuppdata);
			var a=0;  var suppid=""; var suppname=""; var ratesuppid="";
			if(suppdata !=''){
				var suppdataarr=suppdata.split(",");
				if(suppdataarr.length>0){
					for(var b=1; b<=suppdataarr.length; b++)
					{
						var exdata="";
						var exdata=suppdataarr[a].split("***");

						if(suppid=="") suppid=exdata[0]; else suppid+=','+exdata[0];
						if(ratesuppid=="") ratesuppid=exdata[0]; else ratesuppid+=','+exdata[0];
						if(suppname=="") suppname=exdata[1]; else suppname+=','+exdata[1];
						a++;
					}
				}
			}	
			var csuppid="";		
			if(csuppdata!=''){
				var suppcdataarr=csuppdata.split(",");
				var c=0;   
				if(suppcdataarr.length>0){
					for(var b=1; b<=suppcdataarr.length; b++)
					{
						var exdata="";
						var exdata=suppcdataarr[c].split("***");

						if(csuppid=="") csuppid=exdata[0]; else csuppid+=','+exdata[0];
						if(ratesuppid=="") ratesuppid=exdata[0]; else ratesuppid+=','+exdata[0];
						if(suppname=="") suppname=exdata[1]; else suppname+=','+exdata[1];
						c++;
					}
				}
			}
			
			set_trim_rate_amount(ratesuppid,inc,'supplier_change');
			supplier_id_st= suppid+'_'+csuppid;
			$('#cbonominasupplier_'+inc).val(supplier_id_st);
			$('#txtnominasupplier_'+inc).val(suppname);
			var ex_supplieridArr=suppid.split(",");
			var ex_supplierid_len=ex_supplieridArr.length;
			//alert(ex_supplierid_len+'=='+suppid);
			if(ex_supplierid_len==1)
			{
				fnc_load_supplier_source(suppid,inc)	
			}
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
		var rate=return_global_ajax_value(cbogroup+"_"+supplier, 'rate_from_library', '', 'requires/pre_cost_entry_controller_v2');
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
		//var txtdescription=document.getElementById('txtdescription_'+i).value;
		var txtdescription=encodeURIComponent(""+$('#txtdescription_'+i).val()+"");
		var data=txtdescription
		var title = 'Description';
		var page_link = 'requires/pre_cost_entry_controller_v2.php?data='+data+'&action=trims_description_popup';

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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(3);
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

		var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
	    var row_num=$('#tbl_trim_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbogroup_'+i,'Company Name*Group')==false)
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
			//hidSizeRateCalStr_
			data_all+="&cbogroup_" + z + "='" + $('#cbogroup_'+i).val()+"'"+"&txtdescription_" + z + "='" + $('#txtdescription_'+i).val()+"'"+"&txtsupref_" + z + "='" + $('#txtsupref_'+i).val()+"'"+"&cboconsuom_" + z + "='" + $('#cboconsuom_'+i).val()+"'"+"&txtconsdzngmts_" + z + "='" + $('#txtconsdzngmts_'+i).val()+"'"+"&txttrimrate_" + z + "='" + $('#txttrimrate_'+i).val()+"'"+"&txttrimamount_" + z + "='" + $('#txttrimamount_'+i).val()+"'"+"&cboapbrequired_" + z + "='" + $('#cboapbrequired_'+i).val()+"'"+"&cbonominasupplier_" + z + "='" + $('#cbonominasupplier_'+i).val()+"'"+"&updateidtrim_" + z + "='" + $('#updateidtrim_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&calculatorstring_" + z + "='" + $('#calculatorstring_'+i).val()+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'"+"&cbotrimstatus_" + z + "='" + $('#cbotrimstatus_'+i).val()+"'"+"&cbosourceid_" + z + "='" + $('#cbosourceid_'+i).val()+"'"+"&lastappidchk_" + z + "='" + $('#lastappidchk_'+i).val()+"'"+"&cboitemprint_" + z + "='" + $('#cboitemprint_'+i).val()+"'"+"&txtexper_" + z + "='" + $('#txtexper_'+i).val()+"'"+"&cbomaterialsource_" + z + "='" + $('#cbomaterialsource_'+i).val()+"'"+"&excessper_" + z + "='" + $('#excessper_'+i).val()+"'"+"&totalcons_" + z + "='" + $('#totalcons_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_trim_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*cbo_costing_per*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsdzntrim_sum*txtratetrim_sum*txttrimamount_sum',"../../")+data_all;

		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}
			
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]==6)
			{
				alert(reponse[1]);
				var index=reponse[2]-1;
				var tr=$("#tbl_trim_cost tbody tr:eq("+index+")");
				tr.css('background-color', 'RED');
				$('#txtconsdzngmts_'+reponse[2]).css('background-color', 'RED');
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

			  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_embellishment_cost("+i+");");
			  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost',this);");
			  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
			  $('#txtembconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data_emb( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup_emb', 'Consumtion Entry Form',"+i+")");
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
			  set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			  calculate_main_total();
		}
	}

	function cbotype_loder( i )
	{
		var cboembname=document.getElementById('cboembname_'+i).value
		var company_id=document.getElementById('cbo_company_name').value
		load_drop_down( 'requires/pre_cost_entry_controller_v2', cboembname+'_'+i+'_'+company_id, 'load_drop_down_embtype', 'embtypetd_'+i );
		get_php_form_data(cboembname+'_'+i+'_'+company_id,'load_drop_down_embtype_budgeton','requires/pre_cost_entry_controller_v2' );

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

	function set_session_large_post_data_emb(page_link,title,trorder)
	{
		var cons_breck_downn_emb=document.getElementById('consbreckdownemb_'+trorder).value;
		//var cons_breck_downn_emb=''
		var data="action=save_post_session_emb&cons_breck_downn_emb="+cons_breck_downn_emb;
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
		
		var last_app_id=document.getElementById('txtlastpdateid_'+trorder).value;

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
			
			if(last_app_id==2) //Color size change synchronize check
			{
				$('#lastappidchk_'+trorder).val(1);
			}
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
		var job_id=$('#hidd_job_id').val();
		var issue_chk=0;
		if(operation==1){
			issue_chk=return_global_ajax_value(job_id, 'emb_print_issue_check', '', 'requires/pre_cost_entry_controller_v2');			
		}
		if(issue_chk>0){
			if (confirm('Isssue found do you want change Yes or No')) {

			} 
			else {
				release_freezing();
				return;
			}
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(4);
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
			
			data_all+="&cboembname_" + z + "='" + $('#cboembname_'+i).val()+"'"+"&cboembtype_" + z + "='" + $('#cboembtype_'+i).val()+"'"+"&cboembbodypart_" + z + "='" + $('#cboembbodypart_'+i).val()+"'"+"&cboembsupplierid_" + z + "='" + $('#cboembsupplierid_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&txtembconsdzngmts_" + z + "='" + $('#txtembconsdzngmts_'+i).val()+"'"+"&txtembrate_" + z + "='" + $('#txtembrate_'+i).val()+"'"+"&txtembamount_" + z + "='" + $('#txtembamount_'+i).val()+"'"+"&cboembstatus_" + z + "='" + $('#cboembstatus_'+i).val()+"'"+"&embupdateid_" + z + "='" + $('#embupdateid_'+i).val()+"'"+"&consbreckdownemb_" + z + "='" + $('#consbreckdownemb_'+i).val()+"'"+"&empbudgeton_" + z + "='" + $('#empbudgeton_'+i).val()+"'"+"&lastappidchk_" + z + "='" + $('#lastappidchk_'+i).val()+"'";
			z++;
		}
		//alert(data_all); release_freezing(); return;
		var data="action=save_update_delet_embellishment_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_cost_control_source*txtamountemb_sum',"../../")+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
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
			  $('#txtembconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data_wash('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_wash', 'Consumtion Entry Form',"+i+")");

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
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
		var last_app_id=document.getElementById('txtlastpdateid_'+trorder).value;

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
			if(last_app_id==2) //Color size change synchronize check
			{
				$('#lastappidchk_'+trorder).val(1);
			}
			
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(5);
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
			//var txtembconsdzngmts=document.getElementById('txtembconsdzngmts_'+i).value*1; txtamountemb_sum

			if(cboembtype==0){
				alert("Select Type");
				release_freezing();
				return;
			}
			
			data_all+="&cboembname_" + z + "='" + $('#cboembname_'+i).val()+"'"+"&cboembtype_" + z + "='" + $('#cboembtype_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&txtembconsdzngmts_" + z + "='" + $('#txtembconsdzngmts_'+i).val()+"'"+"&txtembrate_" + z + "='" + $('#txtembrate_'+i).val()+"'"+"&txtembamount_" + z + "='" + $('#txtembamount_'+i).val()+"'"+"&cboembstatus_" + z + "='" + $('#cboembstatus_'+i).val()+"'"+"&embupdateid_" + z + "='" + $('#embupdateid_'+i).val()+"'"+"&embratelibid_" + z + "='" + $('#embratelibid_'+i).val()+"'"+"&consbreckdownwash_" + z + "='" + $('#consbreckdownwash_'+i).val()+"'"+"&empbudgeton_" + z + "='" + $('#empbudgeton_'+i).val()+"'"+"&lastappidchk_" + z + "='" + $('#lastappidchk_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_wash_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_cost_control_source*txtamountemb_sum',"../../")+data_all;
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
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

		set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
		set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(6);
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
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}

			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
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
					
					fnc_quotation_entry_dtls1(1);
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
	//calculate_incentives_cost
	function calculate_incentives_cost(i,type)
	{
		var txt_commercial_cost_method=1;
		var update_id=document.getElementById('update_id').value;
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var txt_incentives_pre_rate=(document.getElementById('txt_incentives_pre_rate').value)*1;
		var incentives_pre_cost=document.getElementById('txt_incentives_pre_cost').value
		var amount=0;
		//var total_cost=(document.getElementById('txt_final_price_dzn_pre_cost').value);
		if(update_id!=0)
		{
			var sum_incentives_cost=return_global_ajax_value(update_id+'_'+txt_commercial_cost_method, 'sum_incentives_cost_value', '', 'requires/pre_cost_entry_controller_v2');
			var amount=number_format_common(sum_incentives_cost, 1, 0, currency);
		}

		if(type=='cal_amount')
		{
			var com_amount=amount*(txt_incentives_pre_rate/100);
			document.getElementById('txt_incentives_pre_cost').value=number_format_common(com_amount, 1, 0, currency);
		}
		if(type=='cal_rate')
		{
			var com_rate=(incentives_pre_cost*100)/amount;
			document.getElementById('txt_incentives_pre_rate').value=number_format_common(com_rate, 1, 0, currency);
		}
		//set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
		//set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
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
			var sum_comarcial_cost=return_global_ajax_value(update_id+'_'+txt_commercial_cost_method, 'sum_comarcial_cost_value', '', 'requires/pre_cost_entry_controller_v2');
			var amount=number_format_common(sum_comarcial_cost, 1, 0, currency);
		}

		/*if(txt_commercial_cost_method==1)
		{
			var sum_fab_yarn_trim=return_global_ajax_value(update_id, 'sum_comarcial_cost_value', '', 'requires/pre_cost_entry_controller_v2');
			var amount=number_format_common(sum_fab_yarn_trim, 1, 0, currency);
		}*/
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
		set_sum_value('txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost');
		set_sum_value('txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost');
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(7);
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

		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}

			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
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
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(1);
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

				if(number_format(txt_fabric_pre_cost,6)>pri_fabric_pre_cost)
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
					alert('Commercial cost ('+txt_comml_pre_cost+') is greater than Quotation ('+pri_comml_pre_cost+')');
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
			
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*txt_cost_control_source*cbo_costing_per*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txtcmcost*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txtoptexpper*txt_common_oh_pre_cost*txt_common_oh_po_price*txtdepramortper*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_total_pre_cost*txt_total_po_price*txt_final_price_dzn_pre_cost*txt_final_price_dzn_po_price*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_total_pre_cost_psc_set*txt_total_pre_cost_psc_set_po_price*txt_final_price_pcs_pre_cost*txt_final_price_pcs_po_price*txt_margin_pcs_pre_cost*txt_margin_pcs_po_price*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_deffdlc_pre_cost*txt_deffdlc_po_price*txtinterestper*txt_interest_pre_cost*txt_interest_po_price*txtincometexper*txt_incometax_pre_cost*txt_incometax_po_price*txt_incentives_pre_cost*txt_incentives_pre_cost_per*txt_incentives_pre_rate*txt_design_pre_cost*txt_design_po_price*txt_studio_pre_cost*txt_studio_po_price',"../../");
			 //alert(data); return;
			http.onreadystatechange = function() {
				if( http.readyState == 4 && http.status == 200 ) {
					var reponse=trim(http.responseText).split('**');
					
					if(trim(reponse[0])=='readyapproved')
					{
						alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
						release_freezing();
						return;
					}
					
					if(reponse[0]=='quataNotApp')
					{
						alert("Quotation is not Approved. Please Approved the Quotation");
						release_freezing();
						return;
					}
					if(reponse[0]=='negative')
					{
						alert(reponse[1]);
						release_freezing();
						return;
					}
					
					if(reponse[0]==10)
					{
						release_freezing();
						return;
					}
					if (reponse[0].length>2) reponse[0]=10;
					show_msg(reponse[0]);
					$('#hidd_is_dtls_open').val(1);
					document.getElementById('update_id_dtls').value  = reponse[2];
					set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				}
			}
			http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
		}
	}
// Commarcial Cost End -------------------------------------------------------------

//Master form---------------------------------------------------------------------------
	function openmypage(page_link,title)
	{
		hide_left_menu("Button1");
		//alert(page_link);
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

				// dont remove this 
				set_button_status(0, permission, 'fnc_precosting_entry',1);


				get_php_form_data( theemail.value, action, "requires/pre_cost_entry_controller_v2" );
				var txt_precost_id=document.getElementById("pre_cost_id").value;
				get_php_form_data( theemail.value, 'check_data_mismass', "requires/pre_cost_entry_controller_v2");
				
				$('#cbo_company_name').attr('disabled','true');
				$('#cbo_buyer_name').attr('disabled','true');
				$('#cbo_agent').attr('disabled','true');
				$('#cbo_region').attr('disabled','true');
				$('#cbo_order_uom').attr('disabled','true');
				check_exchange_rate();
				
				if(txt_precost_id=='')
				{
				set_field_level_access(document.getElementById("cbo_company_name").value); 
				}
				release_freezing();
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

		var page_link="requires/pre_cost_entry_controller_v2.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&tot_smv_qnty='+tot_smv_qnty;
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

		if(mandatory_field!="") 
		{
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==5 || cost_control_source==8)
		{
			var qc_validate=fnc_budget_cost_validate(1);
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
				
				var pri_incentives_pre_cost=$('#txt_incentives_pre_cost').attr('pri_incentives_pre_cost')*1; //
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
				if(number_format(txt_fabric_pre_cost,6)>pri_fabric_pre_cost)
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
		if(($('#cbo_ready_to_approved').val()*1)==1)
		{
			fnc_margin_as_bom();
			if(buyer_profit_per>0)
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
			if(($('#txt_margin_pcs_bom_cost').val()*1)==0)
			{
				alert("First calculate BOM Margin by clicking on BOM Margin/Pcs and Master saving then submit for approval.");
				release_freezing();
				return;
			}
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
				get_php_form_data( $("#cbo_company_name").val()+"**"+$("#txt_job_no").val()+"**"+$("#txt_sew_smv").val(), 'get_efficiency_percent', "requires/pre_cost_entry_controller_v2" );
				calculate_cm_cost_with_method();
			}


		}


		if(operation==1 && ($('#cbo_ready_to_approved').val()*1)==1){

			get_php_form_data($("#cbo_company_name").val()+"**"+$("#txt_job_no").val(),'check_variable_setting','requires/pre_cost_entry_controller_v2')
			var hidd_variableCheck_id=$('#hidd_variableCheck_id').val();
			var reponse=trim(hidd_variableCheck_id).split('_');	// alert(reponse)
			var notice="";
			
			if(reponse[0]==1){
				 notice="\n Fabric Cost:";
			}
			if(reponse[1]==2){
				 notice =notice+"\n Trims Cost:";
			}
			if(reponse[2]==3){
				 notice =notice+"\n Emblishment Cost:";
			}
			if(reponse[3]==4){
				 notice =notice+"\n Wash Cost:";
			}
			if(reponse[4]==5){
				 notice =notice+"\n Commercial Cost:";
			}
			if(reponse[5]==6){
				 notice =notice+"\n Comission Cost:";
			}
			if(notice!==""){
			 r=confirm(" Without Cost Head Save Not Found:"+notice+" \n  Press OK \n Otherwise press Cencel");
			 if(r==false){ release_freezing(); return;}
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
		if(db_type==0)//For Mysql
		{
			var sew_efficiency_per=document.getElementById('txt_sew_efficiency_per').value;
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
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('garments_nature*txt_job_no*hidd_job_id*txt_costing_date*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_remarks*update_id*update_id*copy_quatation_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_ready_to_approved*txt_budget_minute*cbo_company_name*txt_quotation_id*txt_sew_efficiency_source*txt_cost_control_source*pre_cost_id*txt_fabric_pre_cost*txt_refusing',"../../");

			http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			//alert(reponse[0]);

			
			if(reponse[0]=='synchronize')
			{
				alert("Please Synchronize Cost");
				release_freezing();
				return;
			}
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
			
			if(reponse[0]=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
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
			//alert(reponse[3])
			if(trim(reponse[0])==3 && trim(reponse[3])==1)
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
			if(trim(reponse[0])==3 && trim(reponse[3])==2)
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
			if(trim(reponse[0])==0 || trim(reponse[0])==1){
			set_button_status(1, permission, 'fnc_precosting_entry',1);
			}
			// release_freezing();
			if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
				//alert(1);
				fnc_quotation_entry_dtls1( reponse[0] )
				reset_form('','cost_container','')
				release_freezing();
				//alert(2);
			}
			//alert(trim(reponse[0])+'='+trim(reponse[3]));
			if(trim(reponse[0])==3 && trim(reponse[3])==2){
				document.getElementById('app_sms').innerHTML = 'This Job Is Approved';
				release_freezing();
			}
			if(trim(reponse[0])==3 && trim(reponse[3])==1){
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
		//var cm_cost_method=return_global_ajax_value(company_id, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v2');
		//alert(cm_cost_method);
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

		//var cpm=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
		var cpm=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date+'_'+txt_job_no, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
		var cm_cost_method = $('#cm_cost_predefined_method_id').attr('lib_variable_data');//return_global_ajax_value(cbo_company_name, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v2');//$('#cm_cost_predefined_method_id').attr('lib_variable_data');
		var cm_cost_method_data = cm_cost_method.split("___");
		var cm_cost_predefined_method_id = cm_cost_method_data[0];
		var data=cpm.split("_");
		//alert(cm_cost_method);
		if(cm_cost_predefined_method_id==1)
		{
			if(data[3]==0 || data[3]=="" )
			{
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup For CM Calculation");
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
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup For CM Calculation");
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
		
		if(cm_cost_predefined_method_id>0)
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
			document.getElementById('txt_cm_pre_cost').value=number_format_common(cm_cost,1,0,cbo_currercy);
			document.getElementById('txtcmcost').value=number_format_common(cm_cost,1,0,cbo_currercy);
			calculate_main_total();
		}
	}

	function calculate_main_total()
	{
		var currency=$("#cbo_currercy").val()*1;
		var dblTot_fa=($("#txt_fabric_pre_cost").val()*1)+($("#txt_trim_pre_cost").val()*1)+($("#txt_embel_pre_cost").val()*1)+($("#txt_wash_pre_cost").val()*1)+($("#txt_comml_pre_cost").val()*1)+($("#txt_commission_pre_cost").val()*1)+($("#txt_lab_test_pre_cost").val()*1)+($("#txt_inspection_pre_cost").val()*1)+($("#txt_cm_pre_cost").val()*1)+($("#txt_freight_pre_cost").val()*1)+($("#txt_currier_pre_cost").val()*1)+($("#txt_certificate_pre_cost").val()*1)+($("#txt_common_oh_pre_cost").val()*1)+($("#txt_depr_amor_pre_cost").val()*1)+($("#txt_incometax_pre_cost").val()*1)+($("#txt_interest_pre_cost").val()*1)+($("#txt_deffdlc_pre_cost").val()*1)+($("#txt_design_pre_cost").val()*1)+($("#txt_studio_pre_cost").val()*1)+($("#txt_incentives_pre_cost").val()*1);

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
		
		var txt_incentives_pre_cost_per=(((document.getElementById('txt_incentives_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100;
		

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
		document.getElementById('txt_incentives_pre_cost_per').value=number_format_common(txt_incentives_pre_cost_per, 7, 0);
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
		//alert(type)
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
		calculate_interest_incometex_oparating_depreciation_cost();//Add by Kausar 
		if(type!=1)
		{
			//alert(type)
			calculate_deffd_lc();
		}
	}

	function check_booking(){
		var response=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_booking_found', 'requires/pre_cost_entry_controller_v2');
		response=response.split("_");
	}

	function change_cost_per(costing_per)
	{
		var is_status_update=0;
		const el = document.querySelector('#save1');
		if (el.classList.contains("formbutton_disabled")) {
			is_status_update=1;
		}
		var is_used_costing_per=return_ajax_request_value(document.getElementById('txt_job_no').value+'**'+costing_per+'**'+is_status_update, 'is_used_costing_per', 'requires/pre_cost_entry_controller_v2');
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
				var response=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_booking_found', 'requires/pre_cost_entry_controller_v2');
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
						/*var response_d=0//return_ajax_request_value(document.getElementById('txt_job_no').value, 'delete_costing', 'requires/pre_cost_entry_controller_v2');
						if(response_d==0){*/
						var r=confirm("Costing has been Deleted,\n If You do not want to delete yet please press Cancel\n Or press ok to delete Permanently");
						if(r==false){
							//var response_a=return_ajax_request_value(document.getElementById('txt_job_no').value, 'active_costing', 'requires/pre_cost_entry_controller_v2');
							alert("Costing has got Backed");
							document.getElementById('cbo_costing_per').value=data[1];
							change_caption_cost_dtls( data[1], 'change_caption_dzn' );
							return;
						}
						else{
							var response_dp=return_ajax_request_value(document.getElementById('txt_job_no').value, 'delete_costing_permanently', 'requires/pre_cost_entry_controller_v2');
							get_php_form_data( document.getElementById('txt_job_no').value, 'populate_data_from_job_table', "requires/pre_cost_entry_controller_v2" );
							get_php_form_data( document.getElementById('txt_job_no').value, 'check_data_mismass', "requires/pre_cost_entry_controller_v2" );
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
			calculate_confirm_price_dzn(1);// 1 pass by kausar for issue id ISD-23-24128
		}
		if(type=="change_caption_pcs")
		{
			if(value==1)
			{
				document.getElementById('price_pcs_td').innerHTML="Price/Pcs  ";
				document.getElementById('margin_pcs_td').innerHTML="G. Margin/Pcs ";
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/Pcs ";
				document.getElementById('margin_bom_td').innerHTML="Net Margin/Pcs";
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
		var check_is_master_part_saved=return_global_ajax_value(document.getElementById('update_id').value, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(check_is_master_part_saved)=="")
		{
			release_freezing();
			alert ("Save Master Part")	;
			return;
		}
		var mendatory_field =''; var mendatory_message ='';
		var cm_cost_method=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v2');
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
		if(cost_control_source==1 || cost_control_source==5)
		{
			var qc_validate=fnc_budget_cost_validate(1);
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
				if(number_format(txt_fabric_pre_cost,6)>pri_fabric_pre_cost)
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
					alert('Commercial cost ('+txt_comml_pre_cost+') is greater than Quotation ('+pri_comml_pre_cost+')');
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
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*cbo_costing_per*txt_cost_control_source*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txtcmcost*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_total_pre_cost*txt_total_po_price*txt_final_price_dzn_pre_cost*txt_final_price_dzn_po_price*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_total_pre_cost_psc_set*txt_total_pre_cost_psc_set_po_price*txt_final_price_pcs_pre_cost*txt_final_price_pcs_po_price*txt_margin_pcs_pre_cost*txt_margin_pcs_po_price*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_deffdlc_pre_cost*txt_deffdlc_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_incometax_pre_cost*txt_incometax_po_price*txt_design_pre_cost*txt_design_po_price*txt_studio_pre_cost*txt_studio_po_price*txt_incentives_pre_cost*txt_incentives_pre_cost_per*txt_incentives_pre_rate*txtinterestper*txtincometexper',"../../");
			http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
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
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation.");
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
			if(reponse[0]=='negative')
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}

			if(reponse[0]==10)
			{
				release_freezing();
				return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				show_msg(reponse[0]);
				$('#hidd_is_dtls_open').val(1);
				document.getElementById('update_id_dtls').value  = reponse[2];
				set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				release_freezing();
			}
		}
	}
	//Dtls Form 1 End --------------------------------------------------------
	//report code here-------------------------------------------------------
	//created by Bilas-------------------------------------------------------

	function generate_report(type,mail_data,mail_status)
	{
		// bomRpt2 preCostRpt2

		// alert(type);return;

		if (form_validation('txt_job_no','Please Select The Job Number.')==false)
		{
			return;
		}
		else
		{
			fnc_margin_as_bom();
			if(type=="summary" || type=="budget3_details" || type=="budget_4" || type=="budget5")			
			{
				freeze_window(3);
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
				}else if(type=='budget5')
				{
					var rpt_type=8; comments_head=1;
				}

				var report_title="Budget/Cost Sheet";
				//	var comments_head=0;
				var cbo_company_name=$('#cbo_company_name').val();
				var cbo_buyer_name=$('#cbo_buyer_name').val();
				var txt_style_ref=$('#txt_style_ref').val();
				var txt_style_ref_id=$('#hidd_job_id').val();
				var txt_quotation_id=$('#txt_quotation_id').val();
				var sign=0;
				var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
				var data="action=report_generate&reporttype="+rpt_type+"&mail_data="+mail_data+
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
				  
				var rate_amt=2; 
				var zero_val='';
				if(mail_status!==1){

					if(type!='mo_sheet' && type != 'budgetsheet' && type != 'budgetsheet2' && type != 'budgetsheet4'  && type != 'budgetsheet2v3' && type != 'mo_sheet_1' && type != 'mo_sheet_3' && type != 'budgetsheet3'  && type != 'preCostRpt10' && type != 'preCostRpt12' && type !='accessories_details3')
					{
						var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
					}
					var excess_per_val="";
					
					if(type == 'preCostRpt10')
					{
						var r=confirm("Press  \"OK\" to Show Conversion Cost Details, \nPress  \"Cancel\"  to Show Conversion Cost Details");
					}
					if(type=='mo_sheet')
					{
						excess_per_val = prompt("Please enter your Excess %", "0");
						if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
					}
					if(type == 'budgetsheet')
					{
						var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
					}
					if(type == 'budgetsheet2' || type == 'budgetsheet4' || type == 'budgetsheet2v3')
					{
						var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
					}
					if(type == 'mo_sheet_1')
					{
						var supplier_check=confirm("Press  \"OK\" to  open with non-nominated supplier, \nPress  \"Cancel\"  to open with All Supplier");
						var r;
					}
					if(type=='mo_sheet_3')
					{
						excess_per_val = prompt("Please enter your Excess %", "0");
						if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
					}
				}
				var show_yarn=0;

				if(type=='preCostRpt9'){
					type='preCostRpt2';
					show_yarn=1;
				}

				if (r==true) zero_val="1"; else zero_val="0";
				if (supplier_check==true) supplier_check="1"; else supplier_check="0";

				if(type == 'preCostRpt7' ||type == 'preCostRpt8' || type == 'trims_check_list' || type == 'budgetsheet2'  || type == 'budgetsheet4' || type == 'budgetsheet2v3'|| type == 'budgetsheet3' || type == 'ocsReport' || type == 'preCostRpt10' || type == 'preCostRpt11' || type == 'preCostRpt12' || type =='accessories_details3' || type =='preCostRpt13' || type =='fabricBom' ){
					var data="action="+type+"&mail_data="+mail_data+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&supplier_check="+supplier_check+"&show_yarn="+show_yarn+"&path=../../"+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*txt_color_id*cbo_costing_per*print_option_id',"../../");
					http.open("POST","requires/pre_cost_entry_report_controller_v2.php",true);
				}
				else{ 
					var data="action="+type+"&mail_data="+mail_data+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&supplier_check="+supplier_check+"&show_yarn="+show_yarn+"&path=../../"+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*txt_color_id*cbo_costing_per*print_option_id',"../../");
					http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
				}
				// freeze_window(3);
				
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
			
			var reponse=trim(http.responseText).split('****');
			var file_data=http.responseText.split("****");
			
			if(reponse[0]!=="mail_sent"){
				if(file_data[2]==100)
			{
			$('#data_panel').html(file_data[1]);
			$('#print_report4').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
				//$('#print_report4')[0].click();
			document.getElementById('print_report4').click();
			}
			else
			{
				$('#pdf_file_name').html(file_data[1]);
				$('#data_panel').html(file_data[0]);
			}

				$('#data_panel').html( http.responseText );
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
				d.close();
				show_msg('3');
				// release_freezing();

			}

		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currercy').val();
		var costing_date = $('#txt_costing_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+costing_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/pre_cost_entry_controller_v2');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	/*function is_manula_approved(compony_id){
		var response=return_global_ajax_value( compony_id, 'is_manula_approved', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(response)==1){
			$('#approve1').hide();
		}
		if(trim(response)==2 || trim(response)==0){
			$('#approve1').show();
		}
	}*/

	function fnc_budget_cost_validate(type)
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
		var hidd_job_id=$('#hidd_job_id').val()*1;

		
		var cost_control_source_data=return_global_ajax_value( company_id, 'cost_control_source', '', 'requires/pre_cost_entry_controller_v2');
		
		var fabdata=""; var isfabsave=0; var row_num=0;
		if(type==2)
		{
			if(trim(cost_control_source_data)==5 || trim(cost_control_source_data)==8)
			{
				var row_num=$('#tbl_fabric_cost tr').length-1;
				var z=1;
				for (var i=1; i<=row_num; i++)
				{
					if(($('#updateid_'+i).val()*1)>0) isfabsave=1;
					
					fabdata+=$('#libyarncountdeterminationid_'+i).val()+ "=" + $('#oldlibyarncountdeterminationid_'+i).val()+ "=" + $('#txtconsumption_'+i).val() + "=" + $('#txtrate_'+i).val() + "=" + $('#txtamount_'+i).val()+ "=" + $('#txtavgprocessloss_'+i).val()+ "=" + $('#updateid_'+i).val()+ "=" + $('#yarnbreackdown_'+i).val()+ "=" + $('#avgtxtconsumption_'+i).val()+ "=" + $('#avgtxtgsmweight_'+i).val()+ "=" + $('#prifabcostdtlsid_'+i).val()+ "=" + $('#cbofabricnature_'+i).val()+ "@@";
					z++;
				}
			}
		}
		//alert(fabdata)

		var fab_cons_kg=0; var fab_cons_mtr=0; var fab_cons_yds=0; var fab_amount=0; var sp_oparation_amount=0; var acc_amount=0; var fright_amount=0; var lab_amount=0; var operating_exp=0; var other_amount=0; var comm_amount=0; var fob_amount=0; var cm_amount=0; var commercial_cost=0; var wash_amount=0; var inspction_amount=0; var rmg_ratio=0; var is_approved=0; var entry_formno=0; var bomfabcostruntime=0;
		var costing_id=$('#txt_quotation_id').val()*1;
		
		var sp_oparation_pre=embel_cost_pre+wash_cost_pre;
		
		if(trim(cost_control_source_data)==1 || trim(cost_control_source_data)==5 || trim(cost_control_source_data)==8)
		{
			if(costing_id!=0)
			{
				if(type==2) 
				{
					if(trim(cost_control_source_data)==5 || trim(cost_control_source_data)==8) 
					{
						var alldata=costing_id+'**'+isfabsave+'**'+cost_control_source_data+'**'+type+'**'+hidd_job_id+'**'+fabdata;
					}
				}
				else var alldata=costing_id;
				
				//alert(alldata)
				
				var str_data=return_global_ajax_value( alldata, 'budget_cost_validate', '', 'requires/pre_cost_entry_controller_v2');
				//var msg=0+'__'+0; release_freezing(); return;
				var spdata=str_data.split("##");
				var fab_cons_kg=trim(spdata[0]); var fab_cons_mtr=trim(spdata[1]); var fab_cons_yds=trim(spdata[2]); var fab_amount=trim(spdata[3]); var sp_oparation_amount=trim(spdata[4]); var acc_amount=trim(spdata[5]); var fright_amount=trim(spdata[6]); var lab_amount=trim(spdata[7]); var operating_exp=trim(spdata[8]); var other_amount=trim(spdata[9]); var comm_amount=trim(spdata[10]); var fob_amount=trim(spdata[11]); var cm_amount=trim(spdata[12]); var commercial_cost=trim(spdata[13]); var rmg_ratio=trim(spdata[14]); var is_approved=trim(spdata[15]); var entry_formno=trim(spdata[16]); var bomfabcostruntime=trim(spdata[17]); var wash_amount=spdata[18]; var inspction_amount=spdata[19];
				//alert(fab_amount);
				if(bomfabcostruntime!=0 && bomfabcostruntime>fab_cost_pre) fab_cost_pre=bomfabcostruntime;
				//else if(bomfabcostruntime) fab_cost_pre=bomfabcostruntime;
				
				if(entry_formno==458 || entry_formno==634) { var sp_oparation_pre=embel_cost_pre; var wash_cost_pre=wash_cost_pre; }
				else { var sp_oparation_pre=embel_cost_pre+wash_cost_pre; }
				
				if(entry_formno==634) is_approved=1;
				
				var msg=""; var msg_type=0;
				if(is_approved==1)
				{
					if(type==2)
					{
						if((fab_cost_pre*1)>(fab_amount*1))
						{
							msg_type=1;
							var fab_cost_dif=fab_cost_pre-fab_amount;
							msg+="\nBOM Limit. Fabric Cost:="+ fab_amount;
							msg+="\nFabric Cost Over form Qc:="+  number_format(fab_cost_dif,4,'.','' );
						}
						if(entry_formno!=458)
						{
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
	
							if(number_format(cons_kg_pre,4,'.','')>fab_cons_kg*1)
							{
								msg_type=1;
								var cons_kg_dif=cons_kg_pre-fab_cons_kg;
								msg+="\nBOM Limit. Fabric Cons KG:="+ fab_cons_kg;
								msg+="\nFabric Cons KG Over form Qc:="+  number_format(cons_kg_dif,2,'.','' );
							}
							if(number_format(cons_mtr_pre,4,'.','')>fab_cons_mtr*1)
							{
								msg_type=1;
								var cons_mtr_dif=cons_mtr_pre-fab_cons_mtr;
								msg+="\nBOM Limit. Fabric Cons Mtr:="+ fab_cons_mtr;
								msg+="\nFabric Cons Mtr Over form Qc:="+ number_format(cons_mtr_dif,2,'.','' );
							}
	
							if(number_format(cons_yds_pre,4,'.','')>fab_cons_yds*1)
							{
								msg_type=1;
								var cons_yds_dif=cons_yds_pre-fab_cons_yds;
								msg+="\nBOM Limit. Fabric Cons YDS:="+ fab_cons_yds;
								msg+="\nFabric Cons YDS Over form Qc:="+ number_format(cons_yds_dif,2,'.','' );
							}
						}
					}
					if(entry_formno==458 || entry_formno==634)
					{
						if(type==4)
						{
							if(sp_oparation_pre>sp_oparation_amount)
							{
								msg_type=1;
								var sp_oparation_dif=sp_oparation_pre-sp_oparation_amount;
								msg+="\nBOM Limit. Embel. Cost Cost:="+ sp_oparation_amount;
								msg+="\nEmbel. Cost Cost Over form Qc:="+ number_format(sp_oparation_dif,2,'.','');
							}
						}
						if(type==5)
						{
							if(wash_cost_pre>wash_amount)
							{
								msg_type=1;
								var washamt_dif=wash_cost_pre-wash_amount;
								msg+="\nBOM Limit. Gmts.Wash Cost:="+ wash_amount;
								msg+="\nGmts.Wash Cost Over form Qc:="+ number_format(washamt_dif,2,'.','');
							}
						}
					}
					else
					{
						if(type==4 || type==5)
						{
							if(sp_oparation_pre>sp_oparation_amount)
							{
								msg_type=1;
								var sp_oparation_dif=sp_oparation_pre-sp_oparation_amount;
								msg+="\nBOM Limit. Embel. Cost & Gmts.Wash Cost:="+ sp_oparation_amount;
								msg+="\nEmbel. Cost & Gmts.Wash Cost Over form Qc:="+ number_format(sp_oparation_dif,2,'.','');
							}
						}
					}
					if(type==3)
					{
						if(trim_cost_pre>acc_amount)
						{
							msg_type=1;
							var trim_cost_dif=trim_cost_pre-acc_amount;
							msg+="\nBOM Limit. Trim Cost:="+ acc_amount;
							msg+="\nTrim Cost Over form Qc:="+ number_format(trim_cost_dif,2,'.','' );
						}
					}
					/*if(type==1)
					{
						if(lab_cost_pre>lab_amount)
						{
							msg_type=1;
							var lab_cost_dif=lab_cost_pre-lab_amount;
							msg+="\nBOM Limit. Lab Cost:="+ lab_amount;
							msg+="\nLab Cost Over form Qc:="+ number_format(lab_cost_dif,2,'.','' );
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
							msg+="\nFreight Cost Over form Qc:="+ number_format(freight_cost_dif,2,'.','' );

						}
					}
					if(type==1)
					{
						if(inspection_cost_pre>inspction_amount)
						{
							msg_type=1;
							var inspction_cost_dif=freight_cost_pre-inspction_amount;
							msg+="\nBOM Limit. Inspection Cost:="+ inspction_amount;
							msg+="\nInspection Cost Over form Qc:="+ number_format(inspction_cost_dif,2,'.','' );

						}
					}
					if(type==1)
					{
						if(common_oh_cost_pre>operating_exp)
						{
							msg_type=1;
							var operating_exp_dif=common_oh_cost_pre-operating_exp;
							msg+="\nBOM Limit. Opert. Exp. Cost:="+ operating_exp;
							msg+="\nOpert. Exp. Cost Over form Qc:="+ number_format(operating_exp_dif,2,'.','' );

						}
					}*/
					/*if(type==6)
					{
						if(commission_cost_pre>comm_amount)
						{
							msg_type=1;
							var commission_cost_dif=commission_cost_pre-comm_amount;
							msg+="\nBOM Limit. Commission Cost:="+ comm_amount;
							msg+="\nCommission Cost Over form Qc:="+ number_format(commission_cost_dif,2,'.','' );
						}
					}
					
					if(type==7)
					{
						if(comml_pre_cost>commercial_cost)
						{
							msg_type=1;
							var commercial_cost_dif=comml_pre_cost-commercial_cost;
							msg+="\n BOM Limit. Commercial Cost:="+ commercial_cost;
							msg+="\n Commercial Cost Over form Qc:="+ number_format(commercial_cost_dif,2,'.','' );
						}
					}*/

					/*if(type==1 && cmValidation==2)
					{
						if(cm_cost_pre>cm_amount)
						{
							msg_type=1;
							var cm_cost_dif=cm_cost_pre-cm_amount;
							msg+="\nBOM Limit. CM Cost:="+ cm_amount;
							msg+="\nCM Cost Over form Qc:="+ number_format(cm_cost_dif,2,'.','' );
						}
					}*/
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
		$("#report_btn_23").hide();
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
		$("#show_button_29").hide();		
		$("#show_button_30").hide();
		$("#report_btn_31").hide();		
		$("#report_btn_32").hide();		
		$("#report_btn_33").hide();	
		$("#report_btn_34").hide();
		$("#report_btn_35").hide();	
		$("#report_btn_36").hide();	
		$("#report_btn_37").hide();
		$("#report_btn_38").hide();	
		$("#report_btn_39").hide();	
		$("#report_btn_40").hide();
		$("#report_btn_41").hide();
		$("#report_btn_42").hide();
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
			if(report_id[k]==730) $("#report_btn_21").show();
			if(report_id[k]==351) $("#report_btn_22").show();
			if(report_id[k]==381) $("#report_btn_23").show();
			if(report_id[k]==268) $("#report_btn_24").show();
			if(report_id[k]==403) $("#report_btn_25").show();
			if(report_id[k]==769) $("#report_btn_26").show();
			if(report_id[k]==445) $("#report_btn_27").show();
			if(report_id[k]==460) $("#report_btn_28").show();
			if(report_id[k]==129)$("#show_button_29").show();
			if(report_id[k]==235)$("#show_button_30").show();
			if(report_id[k]==25) $("#report_btn_31").show();
			if(report_id[k]==120) $("#report_btn_32").show();
			if(report_id[k]==494) $("#report_btn_33").show();
			if(report_id[k]==498) $("#report_btn_34").show();
			if(report_id[k]==800) $("#report_btn_35").show();
			if(report_id[k]==427) $("#report_btn_36").show();
			if(report_id[k]==341) $("#report_btn_37").show();
			if(report_id[k]==342) $("#report_btn_38").show();
			if(report_id[k]==486) $("#report_btn_39").show();
			if(report_id[k]==874) $("#report_btn_40").show();
			if(report_id[k]==881) $("#report_btn_41").show();
			if(report_id[k]==509) $("#report_btn_42").show();
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
	
	function calculate_interest_incometex_oparating_depreciation_cost()
	{
		var currency=$("#cbo_currercy").val()*1;
		var cbo_company_name=$("#cbo_company_name").val()*1;
		var txt_costing_date=$("#txt_costing_date").val();

		var txt_final_price_dzn_pre_cost=$("#txt_final_price_dzn_pre_cost").val()*1;
		var txt_commission_pre_cost=$("#txt_commission_pre_cost").val()*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		var dtls_data=$("#cost_per_minute").val();
		var data=dtls_data.split('_');
		if($("#cbo_approved_status").val()==1) return;
		
		//Interest
		var interestdata=data[6];
		if(interestdata=="") interestdata=0;
		//alert(data_value);
		if(interestdata>0)
		{
			var interest_value=(fob_value*interestdata)/100;
		
			//if(interest_value==0) interest_value=$('#txt_interest_pre_cost').val()*1;
			if(interestdata>0)//ISD-22-07073
				$('#txt_interest_pre_cost').attr('disabled','true');
			else
				$('#txt_interest_pre_cost').removeAttr('disabled');
			
			$('#txtinterestper').val(interestdata);
			$('#txtinterestper').attr("disabled",true);
		
			$("#txt_interest_pre_cost").val( number_format_common(interest_value,1,0,currency) );
		}
		else
		{
			$('#txtinterestper').attr("disabled",false);
			
			var interest_value=(fob_value*($('#txtinterestper').val()*1))/100;
		
			//if(interest_value==0) interest_value=$('#txt_interest_pre_cost').val()*1;
			if(interestdata>0)//ISD-22-07073
				$('#txt_interest_pre_cost').attr('disabled','true');
			else
				$('#txt_interest_pre_cost').removeAttr('disabled');
		
			$("#txt_interest_pre_cost").val( number_format_common(interest_value,1,0,currency) );
		}
		//alert(fob_value);
		
		//Income Tex
		var incometexvalue=data[7];
		if(incometexvalue=="") incometexvalue=0;
		if(incometexvalue>0)
		{
			incometax_value=(fob_value*incometexvalue)/100;
			//if(incometax_value==0) incometax_value=$('#txt_incometax_pre_cost').val()*1;
			if(incometexvalue>0)//ISD-22-07073
				$('#txt_incometax_pre_cost').attr('disabled','true');
			else
				$('#txt_incometax_pre_cost').removeAttr('disabled');
				
			$('#txtincometexper').val(incometexvalue);
			$('#txtincometexper').attr("disabled",true);
			
			$("#txt_incometax_pre_cost").val( number_format_common(incometax_value,1,0,currency) );
		}
		else
		{
			$('#txtincometexper').attr("disabled",false);
			
			incometax_value=(fob_value*($('#txtincometexper').val()*1))/100;
			//if(incometax_value==0) incometax_value=$('#txt_incometax_pre_cost').val()*1;
			if(incometexvalue>0)//ISD-22-07073
				$('#txt_incometax_pre_cost').attr('disabled','true');
			else
				$('#txt_incometax_pre_cost').removeAttr('disabled');
			
			$("#txt_incometax_pre_cost").val( number_format_common(incometax_value,1,0,currency) );
		}
		
		//oparating_expanseses
		var operatingExpVal=data[5];
		cost_per_minute
		//alert(data_value)
		if(operatingExpVal=="") operatingExpVal=0;
		if(operatingExpVal>0)
		{
			var oparating_expanses_value=(fob_value*operatingExpVal)/100;
			//if(oparating_expanses_value==0) oparating_expanses_value=$('#txt_common_oh_pre_cost').val()*1;
			if(operatingExpVal>0)//ISD-22-07073
				$('#txt_common_oh_pre_cost').attr('disabled','true');
			else
				$('#txt_common_oh_pre_cost').removeAttr('disabled');
				
			$('#txtoptexpper').val(operatingExpVal);
			$('#txtoptexpper').attr("disabled",true);
			
			document.getElementById('txt_common_oh_pre_cost').value=number_format_common(oparating_expanses_value,1,0,currency);
		}
		else
		{
			$('#txtoptexpper').attr("disabled",false);
			
			var oparating_expanses_value=(fob_value*($('#txtoptexpper').val()*1))/100;
			//if(oparating_expanses_value==0) oparating_expanses_value=$('#txt_common_oh_pre_cost').val()*1;
			if(operatingExpVal>0)//ISD-22-07073
				$('#txt_common_oh_pre_cost').attr('disabled','true');
			else
				$('#txt_common_oh_pre_cost').removeAttr('disabled');
			
			if(operatingExpVal>0) document.getElementById('txt_common_oh_pre_cost').value=number_format_common(oparating_expanses_value,1,0,currency);
		}
		
		//depreciation_amortization
		var depr_amor_value=data[4];
		if(depr_amor_value=="") depr_amor_value=0;
		// alert(fob_value);
		if(depr_amor_value>0)
		{
			var depreciation_amortization_value=(fob_value*depr_amor_value)/100;
			//if(depreciation_amortization_value==0) depreciation_amortization_value=$('#txt_depr_amor_pre_cost').val()*1;
			if(depr_amor_value>0)//ISD-22-07073
				$('#txt_depr_amor_pre_cost').attr('disabled','true');
			else
				$('#txt_depr_amor_pre_cost').removeAttr('disabled');
			
			$('#txtdepramortper').val(depr_amor_value);
			$('#txtdepramortper').attr("disabled",true);
			
			document.getElementById('txt_depr_amor_pre_cost').value=number_format_common(depreciation_amortization_value,1,0,currency);
		}
		else
		{
			$('#txtdepramortper').attr("disabled",false);
			
			var depreciation_amortization_value=(fob_value*($('#txtdepramortper').val()*1))/100;
			//if(depreciation_amortization_value==0) depreciation_amortization_value=$('#txt_depr_amor_pre_cost').val()*1;
			if(depr_amor_value>0)//ISD-22-07073
				$('#txt_depr_amor_pre_cost').attr('disabled','true');
			else
				$('#txt_depr_amor_pre_cost').removeAttr('disabled');
			
			document.getElementById('txt_depr_amor_pre_cost').value=number_format_common(depreciation_amortization_value,1,0,currency);
		}
		
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

		var response_data=return_global_ajax_value(job_no+'_'+company+'_'+fabric_pre_cost+'_'+final_price_dzn_pre_cost+'_'+commission_pre_cost, 'curreir_cost_method', '', 'requires/pre_cost_entry_controller_v2');
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
			document.getElementById('txt_currier_pre_cost').readOnly=false;
		}
		else
		{
			document.getElementById('txt_currier_pre_cost').readOnly=true;
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

		var supplier = return_global_ajax_value( company_id, 'supplier_name', '', 'requires/pre_cost_entry_controller_v2');
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
		var pre_cost_id=document.getElementById('pre_cost_id').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		var data=txt_job_no+"_"+txt_un_appv_request;
		var title = 'Un Approval Request';
		var page_link = 'requires/pre_cost_entry_controller_v2.php?data='+data+'&action=unapp_request_popup&pre_cost_id='+pre_cost_id;
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
		var response_data=return_global_ajax_value(job_no, 'margin_pcs_as_bom', '', 'requires/pre_cost_entry_controller_v2');

		var exdata=response_data.split('***');

		$('#txt_margin_pcs_bom_cost').val(exdata[1]);
		$('#txt_margin_pcs_bom_per').val(exdata[2]);
	}

	function openmypage_template_name(title)
	{
		var update_id=$("#update_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var check_is_master_part_saved=return_global_ajax_value(update_id, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(check_is_master_part_saved)=="")
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
			$(this).html('Please Save Master Part').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
			return;
		}
		var row_count=$('#tbl_trim_cost tr').length;
		if(row_count == 0){
			$('#txt_trim_pre_cost').focus();
		}
		var page_link='requires/pre_cost_entry_controller_v2.php?action=trims_cost_template_name_popup&company=' + document.getElementById('cbo_company_name').value + '&buyer_name=' + document.getElementById('cbo_buyer_name').value+ '&update_id=' + document.getElementById('update_id').value;
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
		var itemdata=data.split("#");
		var a=0; var n=0;
		for(var b=1; b<=itemdata.length; b++)
		{
			var exdata="";
			var exdata=itemdata[a].split("***");
			if(exdata[8]==0) exdata[8]="";
			if(row_count == 1 && document.getElementById('cboconsuom_1').value == 0)
			{
				document.getElementById('cbogroup_1').value=exdata[2];
				document.getElementById('cbogrouptext_1').value=exdata[0];
				document.getElementById('txtdescription_1').value=exdata[10];
				document.getElementById('txtexper_1').value=exdata[11];
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
				document.getElementById('txtexper_1').value=exdata[11];
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
				//alert(exdata[0]+'='+row_count+'='+exdata[2]);
				document.getElementById('cbogroup_'+row_count).value=exdata[2];
				document.getElementById('cbogrouptext_'+row_count).value=exdata[0];
				document.getElementById('txtdescription_'+row_count).value=exdata[10];
				document.getElementById('txtexper_'+row_count).value=exdata[11];
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

			var page_link='requires/pre_cost_entry_controller_v2.php?action=report_part_select_view&print_option='+print_option+'&print_option_id='+print_option_id+'&print_option_no='+print_option_no;

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

	function report_part2(type)
	{
		if(type=='masterWO')
		{
			var print_option = $("#print_option").val();
			var print_option_id = $("#print_option_id").val();
			var print_option_no = $("#print_option_no").val();

			var page_link='requires/pre_cost_entry_report_controller_v2.php?action=report_part2_select_view&print_option='+print_option+'&print_option_id='+print_option_id+'&print_option_no='+print_option_no;

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

		
		var popup_check=confirm("Press  \"OK\" to  open Po Level, \nPress  \"CANCEL\"  to open Color Level");
		if(popup_check==true){
			var popup_checkid=1;
		}
		else{
			var popup_checkid=0;
		}
		if (form_validation('txt_job_no','Job No')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var hidd_job_id=$("#hidd_job_id").val();
			var page_link='requires/pre_cost_entry_controller_v2.php?action=order_no_popup&hidd_job_id='+hidd_job_id+'&popup_checkid='+popup_checkid;
			var title='Order Search';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=400px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var po_id=this.contentDoc.getElementById("txt_selected_id").value;
				var po_no=this.contentDoc.getElementById("txt_selected").value;
				
				var color_no=this.contentDoc.getElementById("txt_selected_color").value;
				$("#txt_color_id").val('');
				//var po_idArr=po_id.split(",");
				////var po_ids=po_idArr[0];
				//var color_no=po_idArr[1];
					//alert(po_id+'='+color_no);
			
				if (po_id!="")
				{
					$("#txt_po_no").val(po_no);
					$("#txt_po_breack_down_id").val(po_id);
					if(popup_checkid==0)
					{
					$("#txt_color_id").val(color_no);
					}
				}
			}
		}
	}

	function fncChangeButton(value)
	{
		if(value==1) $("#btn_appSubmission_withoutanyChange").val("Submit For Approval");
		else $("#btn_appSubmission_withoutanyChange").val("UN-Submit For Editing");
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

				if(trim(response[0])=='synchronize')
				{
					alert("Please Synchronize Cost");
					release_freezing();
					return;
				}
				
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

	function openmypage_refusing_cause()
	{
		if (form_validation('txt_job_no','Job Number')==false)
		{
			return;
		}

		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_refusing_cause=document.getElementById('txt_refusing').value;
		var update_id=document.getElementById('update_id').value;
		//alert(update_id);

		var data=txt_job_no+"_"+txt_refusing_cause;

		var title = 'Refusing Cause';
		var page_link = 'requires/pre_cost_entry_controller_v2.php?data='+data+'&action=refusing_cause_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var refusing_cause=this.contentDoc.getElementById("hidden_appv_cause");

			$('#txt_refusing').val(refusing_cause.value);
		}
	}
	
	
	function call_print_button_for_mail(mail,mail_body,type){		
	
	var company=$('#cbo_company_name').val();
	var mail_item=89;
	var data=return_global_ajax_value( company+'_'+mail_item, 'mail_template', '', '../../auto_mail/setting/mail_controller');
	if(data==1){
		generate_report('preCostRpt',mail+'**1',1);
	}else if(data==2){			
		generate_report('preCostRpt2',mail+'**1',1);
	}else if(data==3){			
		generate_report('bomRpt',mail+'**1',1);
	}else if(data==4){			
		generate_report('bomRpt2',mail+'**1',1);
	}else if(data==5){			
		generate_report('accessories_details',mail+'**1',1);
	}else if(data==6){			
		generate_report('accessories_details2',mail+'**1',1);
	}else if(data==7){			
		generate_report('preCostRptWoven',mail+'**1',1);
	}else if(data==8){			
		generate_report('bomRptWoven',mail+'**1',1);
	}else if(data==9){			
		generate_report('preCostRpt3',mail+'**1',1);
	}else if(data==10){			
		generate_report('preCostRpt4',mail+'**1',1);
	}else if(data==11){			
		generate_report('preCostRptBpkW',mail+'**1',1);
	}else if(data==12){			
		generate_report('checkListRpt',mail+'**1',1);
	}else if(data==13){			
		generate_report('bomRpt3',mail+'**1',1);
	}else if(data==14){			
		generate_report('mo_sheet',mail+'**1',1);
	}else if(data==15){			
		generate_report('fabric_cost_detail',mail+'**1',1);
	}else if(data==16){			
		report_part('preCostRpt5',mail+'**1',1);
	}else if(data==17){			
		generate_report('summary',mail+'**1',1);
	}else if(data==18){			
		generate_report('budget3_details',mail+'**1',1);
	}else if(data==19){			
		generate_report('preCostRpt6',mail+'**1',1);
	}else if(data==20){			
		generate_report('costsheet',mail+'**1',1);
	}else if(data==21){			
		generate_report('budgetsheet',mail+'**1',1);
	}else if(data==22){			
		generate_report('bomRpt4',mail+'**1',1);
	}else if(data==23){			
		generate_report('mo_sheet_1',mail+'**1',1);
	}else if(data==24){			
		generate_report('budget_4',mail+'**1',1);
	}else if(data==25){			
		generate_report('mo_sheet_3',mail+'**1',1);
	}else if(data==26){	
		generate_report('preCostRpt7',mail+'**1',1);
	}else if(data==27){			
		generate_report('budgetsheet2',mail+'**1',1);
	}else if(data==28){			
		generate_report('fabricBom',mail+'**1',1);
	}else if(data==29){			
		generate_report('masterWO',mail+'**1',1);
	}
}
function fnc_load_supplier_source(supp_id,row_id)
{
		var check_supplier=return_global_ajax_value(supp_id, 'check_supplier_country', '', 'requires/pre_cost_entry_controller_v2');
		//alert(check_supplier+'='+supp_id+'='+row_id);
		if(check_supplier==21)
		{
		$('#cbosourceid_'+row_id).val(2);
		}
		else $('#cbosourceid_'+row_id).val(1);
}
function fnc_cm_negitive()
{
	var calculativecm=$('#txtcmcost').val()*1;
	var actualcm=$('#txt_cm_pre_cost').val()*1;
	if(calculativecm>0 && calculativecm>actualcm)
	{
		alert("Actual CM is less than calculated CM. If you agree with the change then an auto mail will send to the top management.")
	}
}
</script>
</head>
<body onLoad="set_hotkey(); set_auto_complete('pre_cost_mst')" >
    <div style="width:100%;" align="center" >
    <?=load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:1250px;">
        <legend>Pre-Costing</legend>
        <form name="precosting_1" id="precosting_1" autocomplete="off" enctype="multipart/form-data">
            <div style="width:1250px;">
            <table  width="1250" cellspacing="2" cellpadding=""  border="0">
                <tr>
                    <td width="120" class="must_entry_caption">Job No</td>
                    <td width="130"><input  style="width:110px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/pre_cost_entry_controller_v2.php?action=order_popup','Job/Order Selection Form');" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />
                    	<input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
						<input type="hidden" name="hidd_variableCheck_id" id="hidd_variableCheck_id" style="width:30px;" value="0" class="text_boxes" />
                    </td>
                    <td width="120">Company</td>
                    <td width="130"><?=create_drop_down("cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "","1"); ?></td>
                    <td width="120">Quotation ID</td>
                    <td width="130"><input type="text" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:110px;" readonly /></td>
                    <td width="120">Style Ref</td>
                    <td width="130"><input class="text_boxes" type="text" style="width:110px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly/></td>
                    <td width="120">Style Desc.</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_style_desc" id="txt_style_desc" maxlength="100" title="Maximum 100 Character" readonly/></td>
                </tr>
                <tr>
                    <td>Buyer</td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td>Prod. Dept.</td>
                    <td><?=create_drop_down( "cbo_pord_dept", 80, $product_dept,"", 1, "-- Select --",0, "",1,"" ); ?>
                        <input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code"  disabled />
                    </td>
                    <td>Agent</td>
                    <td id="agent_td"><?=create_drop_down( "cbo_agent", 120, $blank_array,"", 1, "-- Select Agent --", $selected, "",1,"" ); ?></td>
                    <td>Job Qty.</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_offer_qnty" id="txt_offer_qnty"/></td>
                    <td>Order UOM </td>
                    <td><?=create_drop_down("cbo_order_uom",60, $unit_of_measurement, "",0, "",1, "change_caption_cost_dtls(this.value, 'change_caption_pcs' )",1,"1,58" ); ?>
                        <input type="button" id="set_button" class="image_uploader" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                        <input type="hidden" id="set_breck_down" />
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="tot_set_qnty" />
                        <input type="hidden" id="print_option" name="print_option" />
                        <input type="hidden" id="print_option_no" name="print_option_no" />
                        <input type="hidden" id="print_option_id" name="print_option_id" />
                        <input type="hidden" name="var_mandatory_id" id="var_mandatory_id" style="width:30px;" class="text_boxes" />
						<input type="hidden" name="var_conv_aop_chart_id" id="var_conv_aop_chart_id" style="width:30px;" class="text_boxes" />
                    </td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currercy", 50, $currency,"", 0, "", 2, "" ,1,""); ?>
                        ER. <input class="text_boxes_numeric" type="text" style="width:30px;" name="txt_exchange_rate" id="txt_exchange_rate" readonly/>
                    </td>
                    <td class="must_entry_caption">Costing Date</td>
                    <td><input class="datepicker" type="text" style="width:110px;" name="txt_costing_date" id="txt_costing_date" onChange="calculate_confirm_price_dzn(0); check_exchange_rate();" value="<?=date('d-m-Y'); ?>"/></td>
                    <td class="must_entry_caption">Costing Per</td>
                    <td><?=create_drop_down( "cbo_costing_per", 120, $costing_per, "",0, "0", 1, "change_cost_per(this.value)","","" ); //change_cost_per(this.value) ?></td>
                    <td class="must_entry_caption">Incoterm</td>
                    <td><?=create_drop_down( "cbo_inco_term", 120, $incoterm,"", 0, "",1,"" );?></td>
                    <td class="must_entry_caption">Incoterm Place</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_incoterm_place" id="txt_incoterm_place" maxlength="100" title="Maximum 100 Character" value="Chittagong"/></td>
                    
                </tr>
                <tr>
                	<td class="must_entry_caption">Sew. SMV</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_sew_smv" id="txt_sew_smv" onChange="calculate_cm_cost_with_method()" disabled /></td>
                    <td class="" id="sew_td">Sew Efficiency %</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_sew_efficiency_per" id="txt_sew_efficiency_per" onChange="calculate_cm_cost_with_method();" />
                    	<input class="text_boxes_numeric" type="hidden" style="width:80px;" name="txt_sew_efficiency_source" id="txt_sew_efficiency_source"/>
                    </td>
                    <td>Cut. SMV</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_cut_smv" id="txt_cut_smv" onChange="calculate_cm_cost_with_method()" /></td>
                    <td>Cut Efficiency %</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_cut_efficiency_per" id="txt_cut_efficiency_per" onChange="calculate_cm_cost_with_method()"  />
                    </td>
                    <td>Prod/Line/Hr</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_prod_line_hr" id="txt_prod_line_hr" maxlength="100" title="Maximum 100 Character"/></td>
                </tr>
                <tr>
                    <td>Machine/Line</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_machine_line" id="txt_machine_line" maxlength="100" title="Maximum 100 Character"/></td>
                    <td>Region</td>
                    <td><?=create_drop_down( "cbo_region", 120, $region,"", 1, "-- Select Region --",0,"",1,"" ); ?></td>
                    <td>Budget Minute</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_budget_minute" id="txt_budget_minute" /></td>
                    <td>File no</td>
                    <td><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:110px" ></td>
                    <td>Internal Ref</td>
                    <td><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:110px" ></td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3">
                    <input class="text_boxes" type="text" style="width:365px;" name="txt_remarks" id="txt_remarks" maxlength="500" title="Maximum 500 Character" placeholder="Remarks"/></td>
                    <td>Approved</td>
                    <td><?=create_drop_down( "cbo_approved_status", 120, $yes_no,"", 0, "", 2, "",1,"" ); ?></td> 
                    <td>Copy From</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_copy_form" id="txt_copy_form" disabled/><input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_efficiency_wastage" id="txt_efficiency_wastage" onChange="calculate_cm_cost_with_method();" readonly /></td>
                    <td>Un-approve Request</td>
                    <td>
                        <Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click" ID="txt_un_appv_request" style="width:110px;" onClick="openmypage_unapprove_request();">
                    </td>
                </tr>
                <tr>
                	<td>Ready To Approved</td>
                    <td><?=create_drop_down( "cbo_ready_to_approved", 120, $yes_no,"", 1, "-- Select--", 2, "fncChangeButton(this.value);","","" ); ?></td>
                    <td colspan="2"><input type="button" name="btn_appSubmission_withoutanyChange" id="btn_appSubmission_withoutanyChange" class="formbuttonplasminus" style="width:130px;" onClick="fnc_appSubmission_withoutanyChange();" value="Submit For Approval"></td>
                    <td><input type="button" class="image_uploader" style="width:80px" value="Copy With Cons." onClick="show_sub_form( document.getElementById('update_id').value, 'partial_pre_cost_copy_action', '1');" /></td>
                    <td><input type="button" class="image_uploader" style="width:120px" value="Copy Without Cons." onClick="show_sub_form( document.getElementById('update_id').value, 'partial_pre_cost_copy_action', '2');" />
                    </td>
                    <td><input id="cbo_add_file" type="button" class="image_uploader" style="width:100px; text-align: center;" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('pre_cost_id').value,'', 'pre_cost_v2', 2 ,1)"></td>
                    <td><input type="button" class="image_uploader" style="width:100px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1)" /></td>
                    <td>Deny/Refusing Cause</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_refusing" id="txt_refusing" maxlength="500" title="Maximum 500 Character" readonly placeholder="Double Click for Browse" onClick="openmypage_refusing_cause();"/></td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="10" valign="top" id="check_sms" style="font-size:18px; color:#F00">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="10" valign="top" id="check_sms2" style="font-size:18px; color:#F00">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="10" valign="top" style="font-size:18px; color:#F00">&nbsp;<span id="app_sms"></span><span id="txt_total_pre_cost_view"></span></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" class="button_container" colspan="10">
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
                        
                        <input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
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
                <td width="17%">
                <fieldset>
                    <form id="quotationdtls_2" autocomplete="off">
                    <table width="100%" cellspacing="2" cellpadding="0" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="107" align="center">Cost Components</th>
                                <th width="70" align="center">Budgeted Cost</th>
                                <th align="center"> % To Q.price </th>
                            </tr>
                        </thead>
                        <tr>
                            <td>Fabric</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_fabric_pre_cost" id="txt_fabric_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_fabric_cost_listview','');" onChange="calculate_main_total();" pre_fab_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_fabric_po_price" id="txt_fabric_po_price" style="width:28px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Trims &nbsp <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;" onClick="openmypage_template_name('Template Search')">...</span></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_trim_pre_cost" id="txt_trim_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value);" onChange="calculate_main_total();" pre_trim_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_trim_po_price" id="txt_trim_po_price" style="width:28px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Embel.</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_embel_pre_cost" id="txt_embel_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_embellishment_cost_listview','');" onChange="calculate_main_total();" pre_emb_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_embel_po_price" id="txt_embel_po_price" style="width:28px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Gmts. Wash</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_wash_pre_cost" id="txt_wash_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_wash_cost_listview','');" onChange="calculate_main_total();" pre_wash_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_wash_po_price" id="txt_wash_po_price" style="width:28px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Comml.</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_comml_pre_cost" id="txt_comml_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_comarcial_cost_listview','');" onChange="calculate_main_total();" pre_comml_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_comml_po_price" id="txt_comml_po_price" style="width:28px;" onChange="calculate_main_total();" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Lab Test</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_lab_test_pre_cost" id="txt_lab_test_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_lab_test_po_price" id="txt_lab_test_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Inspection </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_inspection_pre_cost" id="txt_inspection_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_inspection_po_price" id="txt_inspection_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Freight</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_freight_pre_cost" id="txt_freight_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_freight_po_price" id="txt_freight_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Courier</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_currier_pre_cost" id="txt_currier_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_currier_po_price" id="txt_currier_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Certificate</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_certificate_pre_cost" id="txt_certificate_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_certificate_po_price" id="txt_certificate_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td title="Deferred LC / Document Charges">Deffd. LC/DC</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_deffdlc_pre_cost" id="txt_deffdlc_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_deffdlc_po_price" id="txt_deffdlc_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Design</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_design_pre_cost" id="txt_design_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_design_po_price" id="txt_design_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Studio</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_studio_pre_cost" id="txt_studio_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_studio_po_price" id="txt_studio_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Opert. Exp.&nbsp;<input name="txtoptexpper" id="txtoptexpper" class="text_boxes_numeric" style="width:20px;" title="Calculative Operating Expenses %" placeholder="%" disabled onBlur="calculate_interest_incometex_oparating_depreciation_cost();" /></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_common_oh_pre_cost" id="txt_common_oh_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_common_oh_po_price" id="txt_common_oh_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>CM&nbsp;<input name="txtcmcost" id="txtcmcost" class="text_boxes_numeric" style="width:30px;" title="Calculative CM %" placeholder="%" disabled readonly /></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_cm_pre_cost" id="txt_cm_pre_cost" style="width:60px;" onChange="calculate_main_total();" onBlur="fnc_cm_negitive();" /></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_cm_po_price" id="txt_cm_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Interest&nbsp;<input name="txtinterestper" id="txtinterestper" class="text_boxes_numeric" style="width:30px;" title="Calculative Interest %" placeholder="%" disabled onBlur="calculate_interest_incometex_oparating_depreciation_cost();" /></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_interest_pre_cost" id="txt_interest_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_interest_po_price" id="txt_interest_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Income Tax&nbsp;<input name="txtincometexper" id="txtincometexper" class="text_boxes_numeric" style="width:20px;" title="Calculative Income Tax %" placeholder="%" disabled onBlur="calculate_interest_incometex_oparating_depreciation_cost();" /></td>
                            <td>
                            <input class="text_boxes_numeric" type="text" name="txt_incometax_pre_cost" id="txt_incometax_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_incometax_po_price" id="txt_incometax_po_price" style="width:28px;" onChange="calculate_main_total();" disabled=""/></td>
                        </tr>
                         <tr>
                            <td title="Incentives Missing Cost" style="word-break:break-all">Incentives Missing&nbsp;<input class="text_boxes_numeric" type="text" name="txt_incentives_pre_rate" id="txt_incentives_pre_rate" style="width:30px;" onChange="calculate_incentives_cost( 1,'cal_amount');calculate_main_total();" placeholder="Rate"/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_incentives_pre_cost" id="txt_incentives_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_incentives_pre_cost_per" id="txt_incentives_pre_cost_per" style="width:28px;" onChange="calculate_incentives_cost( 1,'cal_rate');calculate_main_total();" disabled=""/></td>
                        </tr>
                        
                        <tr>
                            <td title="Depreciation, Depletion, and Amortization [DD&A]">DD&A &nbsp;<input name="txtdepramortper" id="txtdepramortper" class="text_boxes_numeric" style="width:20px;" title="Calculative Depreciation, Depletion, and Amortization [DD&A] %" placeholder="%" disabled onBlur="calculate_interest_incometex_oparating_depreciation_cost();" /></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_depr_amor_pre_cost" id="txt_depr_amor_pre_cost" style="width:60px;" onChange="calculate_main_total();"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_depr_amor_po_price" id="txt_depr_amor_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Commission</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_commission_pre_cost" id="txt_commission_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_commission_cost_listview','');" onChange="calculate_main_total();" pre_commis_cost="" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_commission_po_price" id="txt_commission_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr bgcolor="#CCFF99">
                            <td>Total Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost" id="txt_total_pre_cost" style="width:60px;" readonly =""/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_total_po_price" id="txt_total_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="final_price_td_dzn">Price/Dzn</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_dzn_pre_cost" id="txt_final_price_dzn_pre_cost" style="width:60px;" readonly /></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_dzn_po_price" id="txt_final_price_dzn_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="margin_dzn">Margin/Dzn</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_dzn_pre_cost" id="txt_margin_dzn_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" buyer_profit_per="" name="txt_margin_dzn_po_price" id="txt_margin_dzn_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="price_pcs_td">Price/Pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_pcs_pre_cost" id="txt_final_price_pcs_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_final_price_pcs_po_price" id="txt_final_price_pcs_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr bgcolor="#CCFF99">
                            <td id="final_cost_td_pcs_set">Final Cost/Pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost_psc_set" id="txt_total_pre_cost_psc_set" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost_psc_set_po_price" id="txt_total_pre_cost_psc_set_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="margin_pcs_td">G. Margin/Pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_pre_cost" id="txt_margin_pcs_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_po_price" id="txt_margin_pcs_po_price" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td id="margin_bom_td">Net Margin/Pcs </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_bom_cost" id="txt_margin_pcs_bom_cost" style="width:60px;" onClick="fnc_margin_as_bom();" placeholder="Click To Load" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_bom_per" id="txt_margin_pcs_bom_per" min_profit_per="" style="width:28px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3" valign="middle" class="button_container"><input type="hidden" id="update_id_dtls" name="update_id_dtls" readonly/>
                            <? echo load_submit_buttons( $permission, "fnc_quotation_entry_dtls", 0,0 ,"reset_form('quotationdtls_2','','')",2) ; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">Select PO:<span id="po_td">

							<input class="text_boxes" type="text" name="txt_po_no" id="txt_po_no" style="width:180px;" onDblClick="fnc_po_pop();" placeholder="Browse" readonly/>
                           
							<input class="text_boxes" type="hidden" name="txt_po_breack_down_id" id="txt_po_breack_down_id" style="width:80px;"/>
							<input class="text_boxes" type="hidden" name="txt_color_id" id="txt_color_id" style="width:80px;"/>
							<? //echo create_drop_down( "txt_po_breack_down_id", 190,$blank_array, "", 1, "SL#Po No#Po Qty(Pcs)#Ship Date#Po Status", "", ""); ?></span></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <input type="button" id="report_btn_1" class="formbutton" value="Cost Rpt" onClick="generate_report('preCostRpt');" style="display:none;" />
                                <input type="button" id="report_btn_2" class="formbutton" value="Cost Rpt2" onClick="generate_report('preCostRpt2');" style="display:none;" />
                                <input type="button" id="report_btn_3" class="formbutton" value="BOM Rpt" onClick="generate_report('bomRpt');" style="display:none;" />
                                <input type="button" id="report_btn_4" class="formbutton" value="BOM Rpt 2" onClick="generate_report('bomRpt2');" style="display:none;" />
                                <input type="button" id="report_btn_5" class="formbutton" value="Acce. Dtls" onClick="generate_report('accessories_details');" style="display:none;" />&nbsp;<a   id="print_report4" href="" style="text-decoration:none" download hidden>BB</a>
                                <input type="button" id="report_btn_6" class="formbutton" value="Acce. Dtls 2" onClick="generate_report('accessories_details2');" style="display:none;"  />
                                <input type="button" id="report_btn_7" class="formbutton" value="Cost Woven" onClick="generate_report('preCostRptWoven');" style="display:none;"  />
                                <input type="button" id="report_btn_8" class="formbutton" value="Bom Woven" onClick="generate_report('bomRptWoven');" style="display:none;" />
                                <input type="button" id="report_btn_9" class="formbutton" value="Cost Rpt3" onClick="generate_report('preCostRpt3');" style="display:none;" />
                                <input type="button" id="report_btn_10" class="formbutton" value="Cost Rpt4" onClick="generate_report('preCostRpt4');" style="display:none;" />
                                <input type="button" id="report_btn_11" class="formbutton" value="Rpt Bpkw" onClick="generate_report('preCostRptBpkW');"  style="display:none;"/>
                                <input type="button" id="report_btn_12" class="formbutton" value="BOM Dtls" onClick="generate_report('checkListRpt');" style="display:none;" />
								<input type="button" id="report_btn_13" class="formbutton" value="BOM Rpt 3" onClick="generate_report('bomRpt3');" style="display:none;" />
								<input type="button" id="report_btn_14" class="formbutton" value="MO Sheet" onClick="generate_report('mo_sheet');" style="display:none;" />
								<input type="button" id="report_btn_15" class="formbutton" value="Fab. PreCost" onClick="generate_report('fabric_cost_detail');" style="display:none;" />
                                <input type="button" id="report_btn_16" class="formbutton" value="Cost Rpt5" onClick="report_part('preCostRpt5');" style="display:none;" />
                                <input type="button" id="report_btn_17" class="formbutton" value="Summary" onClick="generate_report('summary');" style="display:none;" />
								<input type="button" id="report_btn_18" class="formbutton" value="Budget3 Dtls" onClick="generate_report('budget3_details');" style="display:none;" />
                                <input type="button" id="report_btn_19" class="formbutton" value="Cost Rpt6" onClick="generate_report('preCostRpt6');" style="display:none;" />
                                <input type="button" id="report_btn_20" class="formbutton" value="Cost sheet" onClick="generate_report('costsheet');" style="display:none;" />
                                <input type="button" id="report_btn_21" class="formbutton" value="Budget sheet" onClick="generate_report('budgetsheet');" style="display:none;" />
								<input type="button" id="report_btn_22" class="formbutton" value="BOM Rpt 4" onClick="generate_report('bomRpt4');" style="display:none;"  />
								<input type="button" id="report_btn_23" class="formbutton" value="MO Sheet 1" onClick="generate_report('mo_sheet_1');" style="display:none;"  />
                                <input type="button" id="report_btn_24" class="formbutton" value="Budget 4" onClick="generate_report('budget_4');" style="display:none;" />
								<input type="button" id="report_btn_25" class="formbutton" value="MO Sheet 3" onClick="generate_report('mo_sheet_3');" style="display:none;"  />
							
								<input type="button" id="report_btn_26" class="formbutton" value="Cost Rpt7" onClick="generate_report('preCostRpt7');" style="display:none;" />
								<input type="button" id="report_btn_27" class="formbutton" value="Cost Rpt8" onClick="generate_report('preCostRpt8');" style="display:none;" />
								<input type="button" id="report_btn_28" class="formbutton" value="Trims Check List" onClick="generate_report('trims_check_list');" style="display:none;" />
								<input type="button" id="show_button_29" class="formbutton" value="Budget 5" onClick="generate_report('budget5');"  style="display:none;"/>
								<input type="button" id="show_button_30" class="formbutton" value="Cost Rpt9" onClick="generate_report('preCostRpt9');"  style="display:none;" />
								<input type="button" id="report_btn_31" class="formbutton" value="Budget Sheet 2" onClick="generate_report('budgetsheet2');" style="display:none;" />
								<input type="button" id="report_btn_32" class="formbutton" value="Budget Sheet 3" onClick="generate_report('budgetsheet3');" style="display:none;" />
								<input type="button" id="report_btn_33" class="formbutton" value="OCS Report" onClick="generate_report('ocsReport');" style="display:none;" />
								<input type="button" id="report_btn_34" class="formbutton" value="Cost Rpt10" onClick="generate_report('preCostRpt10');"   style="display:none;"/>
                                <input type="button" id="report_btn_35" class="formbutton" value="Cost Rpt11" onClick="generate_report('preCostRpt11');"   style="display:none;"/>
								<input type="button" id="report_btn_36" class="formbutton" value="Cost Rpt12" onClick="generate_report('preCostRpt12');"   style="display:none;"/>

								<input type="button" id="report_btn_37" class="formbutton" value="Budget Sheet 2 v2" onClick="generate_report('budgetsheet4');"   style="display:none;"/>
								<input type="button" id="report_btn_38" class="formbutton" value="Budget Sheet 2 v3" onClick="generate_report('budgetsheet2v3');"   style="display:none;"/>
								<input type="button" id="report_btn_39" class="formbutton" value="Acce. Dtls 3" onClick="generate_report('accessories_details3');" style="display:none;"  />
                                <input type="button" id="report_btn_40" class="formbutton" value="Cost Rpt13" onClick="generate_report('preCostRpt13');" style="display:none;"  />
								<input type="button" id="report_btn_41" class="formbutton" value="Fabric Bom" onClick="generate_report('fabricBom');" style="display:none;"  />
								<input type="button" id="report_btn_42" class="formbutton" value="Master WO" onClick="report_part2('masterWO');"   style="display:none;"/>
                                <!--
                                	ID:report_btn_26,report_btn_27,report_btn_28,show_button_29,show_button_30,report_btn_31,report_btn_32,report_btn_33,report_btn_34,report_btn_35,report_btn_36=Rpt Page
                                	Name:Cost Rpt7,Cost Rpt8,Trims Check List,Budget 5,Cost Rpt9,Budget Sheet 2,Budget Sheet 3,OCS Report,Cost Rpt10,Cost Rpt11,Cost Rpt12=Rpt Page
                                -->
                            </td>
                        </tr>
                    </table>
                    </form>
                </fieldset>
                </td>
                <td width="83%" valign="top" id="cost_container"></td>
            </tr>
        </table>
    </div>
    <div style="display:none;" id="data_panel"></div>
    </div>
</body>
<script>
calculate_confirm_price_dzn(0);
check_exchange_rate();
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
