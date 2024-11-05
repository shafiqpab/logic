<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This form will create Sweater Garments Pre-Cost V2
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	29-12-2015
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];
//echo $user_level;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Pre-Cost Entry V2 ","../../", 1, 1, $unicode,1,'');
?>

<script type="text/javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	var user_level = '<? echo $user_level; ?>';
	var israte_popup=2;
	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][521]);
	echo "var field_level_data= ". $data_arr . ";\n";
	?>
	var str_construction =[<?=substr(return_library_autocomplete("select construction from wo_pri_quo_fabric_cost_dtls group by construction","construction"), 0, -1); ?>];
	var str_composition = [<?=substr(return_library_autocomplete("select composition from wo_pri_quo_fabric_cost_dtls group by composition","composition"), 0, -1); ?>];
	var str_incoterm_place = [<?=substr(return_library_autocomplete("select incoterm_place from  wo_price_quotation group by incoterm_place","incoterm_place"), 0, -1); ?>];
	var str_trimdescription = [<?=substr(return_library_autocomplete("select description from  wo_pre_cost_trim_cost_dtls group by description","description"), 0, -1); ?>];

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
		if(type=='tbl_trim_cost'){
			var row_num=$('#tbl_trim_cost tr').length-1;
			for (var i=1; i<=row_num; i++){
				$("#txtdescription_"+i).autocomplete({
					source: str_trimdescription
				});
			}
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id)
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
				var index=rowNo-1
				$("table#tbl_fabric_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_fabric_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++){
					$("#tbl_fabric_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'value': function(_, value) { return value }
						});
						
						if($('#seq_'+i).val()!= "")
						{
							$('#seq_'+i).val( i );
						}
						
						$("#tbl_fabric_cost tr:eq("+i+")").removeAttr('id').attr('id','fabriccosttbltr_'+i);
						$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
						$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
						$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
						$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
						$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
						$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
						$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
						$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
						$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
						$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
					})
				}
			}
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required()
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
					var booking=return_global_ajax_value(updateid, 'delete_row_yarn_cost', '', 'requires/pre_cost_entry_controller_v2');
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
					   $('#txtprocessloss_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
					   $('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
					   $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+");");
					   $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost');");
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
						$('#cbointernaltext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_internalitem("+i+" )");
						$('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+" )");
						$('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup('"+i+"_1' )");
						$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
						$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost');");
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
						  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost');");
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
						  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost');");
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
						  $('#decreasecomarcial_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_comarcial_cost');");
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
			/*var comml_cost=$("#txt_comml_pre_cost").val()*1;      var pre_comml_cost=$("#txt_comml_pre_cost").attr('pre_comml_cost')*1;
            if(comml_cost!=pre_comml_cost)
            {
                alert("Comml. Cost Change Found, Please Save or Update.");
                return;
            }*/

			if(action=="show_fabric_cost_listview")
			{
				var yarn_controller=0;
				show_list_view(update_id+'_'+$("#txt_quotation_id").val()+'_'+$("#copy_quatation_id").val()+'_'+$("#cbo_company_name").val()+'_'+$("#cbo_buyer_name").val()+'_'+yarn_controller+'_'+$("#txt_cost_control_source").val(),action,'cost_container','requires/pre_cost_entry_controller_v2','');
				
				
				// set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
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
					//document.getElementById('txtavgconsumptionyarn_sum').style.backgroundColor='#F00';
				}
			}

			if(action=="show_trim_cost_listview")
			{
				show_list_view(update_id+'*'+extra_str+'*'+$("#txt_quotation_id").val()+'*'+$("#copy_quatation_id").val()+'*'+$("#cbo_company_name").val()+'*'+$("#cbo_buyer_name").val()+'*'+$("#txt_cost_control_source").val(),action,'cost_container','requires/pre_cost_entry_controller_v2','');
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
				show_list_view(update_id+'_'+$("#txt_quotation_id").val()+'_'+$("#copy_quatation_id").val()+'_'+$("#cbo_company_name").val()+'_'+$("#cbo_buyer_name").val()+'_'+$("#txt_cost_control_source").val(),action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			}

			if(action=="show_wash_cost_listview")
			{
				show_list_view(update_id+'_'+$("#txt_quotation_id").val()+'_'+$("#copy_quatation_id").val()+'_'+$("#cbo_company_name").val()+'_'+$("#cbo_buyer_name").val()+'_'+$("#txt_cost_control_source").val(),action,'cost_container','requires/pre_cost_entry_controller_v2','');
				
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
			}

			if(action=="show_commission_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+$("#txt_cost_control_source").val(),action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
				set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
			}

			if(action=="show_comarcial_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_final_price_dzn_pre_cost').value+'_'+document.getElementById('txt_commission_pre_cost').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
				set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
			}
			if(action=="show_cm_cost_listview"){
	  	        show_list_view(update_id,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecm_sum', 'txtcmrate_', 'tbl_cm_cost' );
	  	    }
			//partial pre cost copy -----------------new development kaiyum-------------------------------------------
			if(action=="partial_pre_cost_copy_action")
			{
				var page_link='requires/pre_cost_entry_controller_v2.php?action=partial_pre_cost_copy_popup';
				var title='Partial Pre Cost Copy';
				var job_no=$("#txt_job_no").val();
				//alert(job_no);
				var txt_job_no=document.getElementById('txt_job_no').value;
				page_link=page_link + "&txt_job_no="+txt_job_no;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=100px,center=1,resize=1,scrolling=0','../')
				emailwindow.onclose=function()
				{
				}
			}
		//partial pre cost copy -----------------END-------------------------------------------
		}
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
		$('#content_'+row).toggle('slow', function() {
			 //get_php_form_data( id, 'set_php_form_data', 'requires/size_color_breakdown_controller' );
		});
	}

	function set_sum_value(des_fil_id,field_id,table_id){
		if(table_id=='tbl_fabric_cost')
		{
			var rowCount = $('#tbl_fabric_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
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
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_embel_pre_cost').value=document.getElementById('txtamountemb_sum').value;
			calculate_main_total()
		}
		if(table_id=='tbl_wash_cost')
		{
			var rowCount = $('#tbl_wash_cost tr').length-1;
			//alert(rowCount)
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_wash_pre_cost').value=document.getElementById('txtamountemb_sum').value;
			calculate_main_total()
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
		if(table_id=='tbl_cm_cost')
		{
			var rowCount = $('#tbl_cm_cost tr').length-1;
			//alert(rowCount)
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_cm_pre_cost').value=document.getElementById('txtratecm_sum').value;
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
//Common for All end ----------------------------
//Fabric Cost-------------------------------------
	function add_break_down_tr(i)
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
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			$("#tbl_fabric_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_fabric_cost");
			$("#tbl_fabric_cost tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
			if($('#seq_'+i).val()!= "")
			{
				$('#seq_'+i).val( i );
			}
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
			$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
			$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
			$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
			$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
			$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
			$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
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
			$('#cbostatus_'+i).val($('#cbostatus_'+j).val());
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

			set_all_onclick();
			sum_yarn_required();
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			$("#libyarncountdeterminationid_"+i).autocomplete({
				source: str_construction
			});
			$("#fabricdescription_"+i).autocomplete({
				source:  str_composition
			});
		}
	}

	function open_body_part_popup(i){
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v2.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=450px,height=450px,center=1,resize=1,scrolling=0','../');
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
		var page_link='requires/pre_cost_entry_controller_v2.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
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
		}
	}

	function set_session_large_post_data(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
	{
		var operation=0;
		var updateid =document.getElementById(updateid_fc).value;

		if(updateid) operation=1; else operation=0;
		if(updateid) open_consumption_popup(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
		else fnc_fabric_cost_dtls_per_row(operation,trorder)

		/*var cons_breck_downn=document.getElementById('consbreckdown_'+trorder).value;
		var msmnt_breack_downn=document.getElementById('msmntbreackdown_'+trorder).value;
		var marker_breack_down=document.getElementById('markerbreackdown_'+trorder).value;
		var data="action=save_post_session&cons_breck_downn="+cons_breck_downn+'&msmnt_breack_downn='+msmnt_breack_downn+'&marker_breack_down='+marker_breack_down;
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
		}*/
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
		var body_part_id_type =document.getElementById('txtbodyparttype_'+trorder).value;
		var budgeton=document.getElementById('budgeton_'+trorder).value;
	
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
	
		if( hid_fab_cons_in_quotation_variable=='' || hid_fab_cons_in_quotation_variable <= 0 )
		{
			alert("You have to set Variable for this Company");
			return;
		}
		if(hid_fab_cons_in_quotation_variable==1) var tblwidth=1060; else if(hid_fab_cons_in_quotation_variable==2) var tblwidth=1260; else var tblwidth=1060;
		
		var page_link=page_link+'&body_part_id='+body_part_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_company_id='+cbo_company_id+'&cbofabricnature_id='+cbofabricnature_id+'&calculated_conss='+calculated_conss+'&hid_fab_cons_in_quotation_variable='+hid_fab_cons_in_quotation_variable+'&txtgsmweight='+txtgsmweight+'&txt_job_no='+txt_job_no+'&cbogmtsitem='+cbogmtsitem+'&garments_nature='+garments_nature+'&cbo_approved_status='+cbo_approved_status+'&pri_fab_cost_dtls_id='+pri_fab_cost_dtls_id+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved+'&cbofabricsource='+cbofabricsource+'&uom='+uom+'&body_part_id_type='+body_part_id_type+'&msmnt_breack_downn='+msmnt_breack_downn+'&budgeton='+budgeton;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+tblwidth+'px,height=480px,center=1,resize=1,scrolling=0','../')
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
			var tot_plancut_qty=this.contentDoc.getElementById("tot_plancut_qty");
			var calculated_plancutqty=this.contentDoc.getElementById("calculated_plancutqty");
			var calculated_rate=this.contentDoc.getElementById("calculated_rate");
			var calculated_amount=this.contentDoc.getElementById("calculated_amount");
	
			document.getElementById('txtconsumption_'+trorder).value=calculated_cons.value;
			document.getElementById('txtfinishconsumption_'+trorder).value=finish_avg_cons.value;
			document.getElementById('txtavgprocessloss_'+trorder).value=avg_process_loss.value;
			document.getElementById('processlossmethod_'+trorder).value=process_loss_method_id.value;
			document.getElementById('consbreckdown_'+trorder).value=cons_breck_down.value;
			document.getElementById('msmntbreackdown_'+trorder).value=msmnt_breack_down.value;
			document.getElementById('markerbreackdown_'+trorder).value=marker_breack_down.value;
			document.getElementById('isclickedconsinput_'+trorder).value=1;
			document.getElementById('plancutqty_'+trorder).value=calculated_plancutqty.value;
			document.getElementById('jobplancutqty_'+trorder).value=tot_plancut_qty.value;
			document.getElementById('isconspopupupdate_'+trorder).value=1;
			document.getElementById('txtrate_'+trorder).value=calculated_rate.value;
			document.getElementById('txtamount_'+trorder).value=calculated_amount.value;
			math_operation( 'txtamount_'+trorder, 'txtconsumption_'+trorder+'*'+'txtrate_'+trorder, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required();
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
		if(value==2) document.getElementById(td_id).innerHTML="GSM";
		else document.getElementById(td_id).innerHTML="Yarn Weight";
	}
	
	function control_color_field(i)
	{
		var cbocolorsizesensitive = document.getElementById('cbocolorsizesensitive_'+i).value;
		if(cbocolorsizesensitive==0)
		{
			$('#txtcolor_'+i).removeAttr('disabled');
			$('#txtcolor_'+i).removeAttr('onClick');
		}
		if(cbocolorsizesensitive==1) $('#txtcolor_'+i).attr('disabled','true')
		if(cbocolorsizesensitive==2) $('#txtcolor_'+i).attr('disabled','true')
		if(cbocolorsizesensitive==3) $('#txtcolor_'+i).removeAttr('disabled').attr("onClick","open_color_popup("+i+");");
		if(cbocolorsizesensitive==4) $('#txtcolor_'+i).attr('disabled','true')
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
			var page_link="requires/pre_cost_entry_controller_v2.php?txt_job_no="+trim(txt_job_no)+"&action=open_color_list_view&color_breck_down="+color_breck_down+"&cbogmtsitem="+cbogmtsitem+"&cbo_company_id="+cbo_company_id+"&cbo_buyer_name="+cbo_buyer_name+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=400px,height=480px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var color_breck_down=this.contentDoc.getElementById("color_breck_down") //Access form field with id="emailfield"
				document.getElementById('colorbreackdown_'+i).value=color_breck_down.value;
			}
		}
	}

	function fnc_fabric_cost_dtls_per_row(operation,i )
	{
		freeze_window(operation);

		var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;
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

		var data_all=""; var z=1;
		var row_num=$('#tbl_fabric_cost tr').length-1;
		
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*txtbodyparttype_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*cbofabricsource_'+i+'*uom_'+i,'Gmts Item *Body Part*Body Part Type*Yarn Nature*Color Type*Construction*Composition*Yarn Source*UOM')==false)
			{
				release_freezing();
				return;
			}
			
			data_all+="&consumptionbasis_" + z + "='" + $('#consumptionbasis_'+i).val()+"'"+"&cbogmtsitem_" + z + "='" + $('#cbogmtsitem_'+i).val()+"'"+"&txtbodypart_" + z + "='" + $('#txtbodypart_'+i).val()+"'"+"&cbofabricnature_" + z + "='" + $('#cbofabricnature_'+i).val()+"'"+"&cbocolortype_" + z + "='" + $('#cbocolortype_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'"+"&construction_" + z + "='" + $('#construction_'+i).val()+"'"+"&composition_" + z + "='" + $('#composition_'+i).val()+"'"+"&fabricdescription_" + z + "='" + $('#fabricdescription_'+i).val()+"'"+"&txtgsmweight_" + z + "='" + $('#txtgsmweight_'+i).val()+"'"+"&cbocolorsizesensitive_" + z + "='" + $('#cbocolorsizesensitive_'+i).val()+"'"+"&txtcolor_" + z + "='" + $('#txtcolor_'+i).val()+"'"+"&txtconsumption_" + z + "='" + $('#txtconsumption_'+i).val()+"'"+"&cbofabricsource_" + z + "='" + $('#cbofabricsource_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtfinishconsumption_" + z + "='" + $('#txtfinishconsumption_'+i).val()+"'"+"&txtavgprocessloss_" + z + "='" + $('#txtavgprocessloss_'+i).val()+"'"+"&cbostatus_" + z + "='" + $('#cbostatus_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&msmntbreackdown_" + z + "='" + $('#msmntbreackdown_'+i).val()+"'"+"&updateid_" + z + "='" + $('#updateid_'+i).val()+"'"+"&processlossmethod_" + z + "='" + $('#processlossmethod_'+i).val()+"'"+"&colorbreackdown_" + z + "='" + $('#colorbreackdown_'+i).val()+"'"+"&yarnbreackdown_" + z + "='" + $('#yarnbreackdown_'+i).val()+"'"+"&markerbreackdown_" + z + "='" + $('#markerbreackdown_'+i).val()+"'"+"&cbowidthdiatype_" + z + "='" + $('#cbowidthdiatype_'+i).val()+"'"+"&avgtxtconsumption_" + z + "='" + $('#avgtxtconsumption_'+i).val()+"'"+"&avgtxtgsmweight_" + z + "='" + $('#avgtxtgsmweight_'+i).val()+"'"+"&plancutqty_"+z+ "='"+$('#plancutqty_'+i).val()+"'"+"&jobplancutqty_"+z+"='"+ $('#jobplancutqty_'+i).val()+"'"+"&isclickedconsinput_" + z + "='" + $('#isclickedconsinput_'+i).val()+"'"+"&oldlibyarncountdeterminationid_" + z + "='" + $('#oldlibyarncountdeterminationid_'+i).val()+"'"+"&isconspopupupdate_" + z + "='" + $('#isconspopupupdate_'+i).val()+"'"+"&uom_" + z + "='" + $('#uom_'+i).val()+"'"+"&txtbodyparttype_" + z + "='" + $('#txtbodyparttype_'+i).val()+"'"+"&budgeton_" + z + "='" + $('#budgeton_'+i).val()+"'"+"&prifabcostdtlsid_" + z + "='" + $('#prifabcostdtlsid_'+i).val()
			
			+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'";
			z++;
		}
		
		var data="action=save_update_delet_fabric_cost_dtls_per_row&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_costing_per*txt_cost_control_source*update_id*hidd_job_id*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtwoven_fin_sum*txtknit_fin_sum*txtamount_sum*avg*txtwoven_sum_production*txtknit_sum_production*txtwoven_fin_sum_production*txtknit_fin_sum_production*txtwoven_sum_purchase*txtknit_sum_purchase*txtwoven_fin_sum_purchase*txtknit_fin_sum_purchase*txtwoven_amount_sum_purchase*txtkint_amount_sum_purchase*txt_quotation_id*copy_quatation_id',"../../")+data_all;
		
		//alert(row_num); release_freezing(); return;
		//var data="action=save_update_delet_fabric_cost_dtls_per_row&operation="+operation+'&total_row='+i+data_all;
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function (){
			fnc_fabric_cost_dtls_per_row_reponse(row_num)
		};
	}

	function fnc_fabric_cost_dtls_per_row_reponse(i)
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
	
			if (reponse[0].length>2)
			{
				 reponse[0]=10;
			}
			show_msg(reponse[0]);
			show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
			show_hide_content('fabric_cost', '')
			release_freezing();
	
			if(reponse[0]==0)
			{
				 open_consumption_popup('requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumption Entry Form','txtbodypart_'+i, 'cbofabricnature_'+i, 'txtgsmweight_'+i,i,'updateid_'+i)
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
				alert("Change found in color size Brackdown,Please Click in Avg. Grey Cons Input Box and just close the popup and click update button.")
				release_freezing();
				return;
			}
		}
		
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==7)
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
		//alert(row_num)
		for (var i=1; i<=row_num; i++)
		{
			var txtconsumption=document.getElementById('txtconsumption_'+i).value*1;
			if(operation==1)
			{
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
				
				if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtrate_'+i,'Rate')==false || $('#txtrate_'+i).val()=='0') )
				{
					document.getElementById('txtrate_'+i).focus();
					document.getElementById('txtrate_'+i).style.backgroundImage=bgcolor;
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
						$(this).html('Please Fill up Rate field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
					});
					release_freezing();
					return;
				}
	
				if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtamount_'+i,'Amount')==false || $('#txtamount_'+i).val()=='0') )
				{
					document.getElementById('txtamount_'+i).focus();
					document.getElementById('txtamount_'+i).style.backgroundImage=bgcolor;
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
						$(this).html('Please Fill up Amount field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
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
			}
			
			if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*cbofabricsource_'+i+'*uom_'+i,'Gmts Item *Body Part*Fabric Nature*Color Type*Construction*Composition*Fabric Source*UOM')==false)
			{
				release_freezing();
				return;
			}

			if ($('#cbocolorsizesensitive_'+i).val()=='0' && $('#txtcolor_'+i).val()=='')
			{
				 document.getElementById('txtcolor_'+i).focus();
				 document.getElementById('txtcolor_'+i).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
					$(this).html('Please Fill up Color field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				 });
				 release_freezing();
				 return;
			}
			
			if($('#colorbreackdown_'+i).val()=='' && $('#cbocolorsizesensitive_'+i).val()==3)
			{
				alert("Please set Contrast Color");
				$('#txtcolor_'+i).click();
				release_freezing();
				return;
			}

			if($('#isclickedconsinput_'+i).val()==2)
			{
				document.getElementById('txtconsumption_'+i).focus();
				document.getElementById('txtconsumption_'+i).style.backgroundImage=bgcolor;
				alert("Change found in color size Brackdown,Please Click in Avg. Grey Cons Input Box and just close the popup and click update button.")
				release_freezing();
				return;
			}
			//alert(data_all)
		
			data_all+="&consumptionbasis_" + z + "='" + $('#consumptionbasis_'+i).val()+"'"+"&cbogmtsitem_" + z + "='" + $('#cbogmtsitem_'+i).val()+"'"+"&txtbodypart_" + z + "='" + $('#txtbodypart_'+i).val()+"'"+"&cbofabricnature_" + z + "='" + $('#cbofabricnature_'+i).val()+"'"+"&cbocolortype_" + z + "='" + $('#cbocolortype_'+i).val()+"'"+"&libyarncountdeterminationid_" + z + "='" + $('#libyarncountdeterminationid_'+i).val()+"'"+"&construction_" + z + "='" + $('#construction_'+i).val()+"'"+"&composition_" + z + "='" + $('#composition_'+i).val()+"'"+"&fabricdescription_" + z + "='" + $('#fabricdescription_'+i).val()+"'"+"&txtgsmweight_" + z + "='" + $('#txtgsmweight_'+i).val()+"'"+"&cbocolorsizesensitive_" + z + "='" + $('#cbocolorsizesensitive_'+i).val()+"'"+"&txtcolor_" + z + "='" + $('#txtcolor_'+i).val()+"'"+"&txtconsumption_" + z + "='" + $('#txtconsumption_'+i).val()+"'"+"&cbofabricsource_" + z + "='" + $('#cbofabricsource_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtfinishconsumption_" + z + "='" + $('#txtfinishconsumption_'+i).val()+"'"+"&txtavgprocessloss_" + z + "='" + $('#txtavgprocessloss_'+i).val()+"'"+"&cbostatus_" + z + "='" + $('#cbostatus_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&msmntbreackdown_" + z + "='" + $('#msmntbreackdown_'+i).val()+"'"+"&updateid_" + z + "='" + $('#updateid_'+i).val()+"'"+"&processlossmethod_" + z + "='" + $('#processlossmethod_'+i).val()+"'"+"&colorbreackdown_" + z + "='" + $('#colorbreackdown_'+i).val()+"'"+"&yarnbreackdown_" + z + "='" + $('#yarnbreackdown_'+i).val()+"'"+"&markerbreackdown_" + z + "='" + $('#markerbreackdown_'+i).val()+"'"+"&cbowidthdiatype_" + z + "='" + $('#cbowidthdiatype_'+i).val()+"'"+"&avgtxtconsumption_" + z + "='" + $('#avgtxtconsumption_'+i).val()+"'"+"&avgtxtgsmweight_" + z + "='" + $('#avgtxtgsmweight_'+i).val()+"'"+"&plancutqty_" + z + "='" + $('#plancutqty_'+i).val()+"'"+"&jobplancutqty_" + z + "='" + $('#jobplancutqty_'+i).val()+"'"+"&isclickedconsinput_" + z + "='" + $('#isclickedconsinput_'+i).val()+"'"+"&oldlibyarncountdeterminationid_" + z + "='" + $('#oldlibyarncountdeterminationid_'+i).val()+"'"+"&isconspopupupdate_" + z + "='" + $('#isconspopupupdate_'+i).val()+"'"+"&uom_" + z + "='" + $('#uom_'+i).val()+"'"+"&txtbodyparttype_" + z + "='" + $('#txtbodyparttype_'+i).val()+"'"+"&budgeton_" + z + "='" + $('#budgeton_'+i).val()+"'"+"&prifabcostdtlsid_" + z + "='" + $('#prifabcostdtlsid_'+i).val()+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'";
			z++;
		}
		
		var data="action=save_update_delet_fabric_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_costing_per*txt_cost_control_source*update_id*hidd_job_id*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtwoven_fin_sum*txtknit_fin_sum*txtamount_sum*avg*txtwoven_sum_production*txtknit_sum_production*txtwoven_fin_sum_production*txtknit_fin_sum_production*txtwoven_sum_purchase*txtknit_sum_purchase*txtwoven_fin_sum_purchase*txtknit_fin_sum_purchase*txtwoven_amount_sum_purchase*txtkint_amount_sum_purchase*txt_quotation_id*copy_quatation_id',"../../")+data_all;
		
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
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]==6)
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
			if(reponse[0]==15)
			{
				 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value*1;
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);
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
					 calculate_main_total()
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
			  return;
			}
		}
	
		var txt_job_no=document.getElementById('txt_job_no').value
		var index_page=$('#index_page', window.parent.document).val();
		var page_link="stripe_color_measurement_urmi.php?permission="+permission+'&txt_job_no='+txt_job_no+'&index_page='+index_page;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=1200px,height=500px,center=1,resize=1,scrolling=0','../')
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
			if(cbofabricnature==100 && cbofabricsource==1)
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
		var cbotype=document.getElementById('cbotype_'+i).value;
		var txtrateyarn=document.getElementById('txtrateyarn_'+i).value*1;
		var cbo_currercy=$('#cbo_currercy').val();

		var consumptionyarn_sum=$('#txtconsumptionyarn_sum').val()*1;

		var rowCount = $('#tbl_yarn_cost tr').length-1;

		for (var k=i; k<=rowCount; k++)
		{
			var cbocountk=$('#cbocount_'+i).val();
			var cbocomponek=$('#cbocompone_'+i).val();
			var percentonek=$('#percentone_'+i).val();
			//var cbocomptwok=document.getElementById('cbocomptwo_'+k).value;
			//var percenttwok=document.getElementById('percenttwo_'+k).value;
			var cbotypek=$('#cbotype_'+i).val(); var inc=0;
			if(cbocount==cbocountk && cbocompone==cbocomponek && percentone==percentonek && cbotype==cbotypek)
			{
				//$('#txtrateyarn_'+k).val( txtrateyarn );
				inc=k;
			}
			else
			{
				txtrateyarn=$('#txtrateyarn_'+i).val()*1;
				inc=i;
			}
			var consqnty=0; var avg_yarn_cons=0; var consdznlbs=0; var rate_dzn=0; var rate=0; var yarnamount=0;
			//consqnty=$('#consqnty_'+inc).val()*1;
			//avg_yarn_cons= ( consqnty / consumptionyarn_sum ) * ( consumptionyarn_sum / rowCount );
			//$('#avgconsqnty_'+inc).val( number_format(avg_yarn_cons,4,'.','' ) );

			consdznlbs=$('#txtconsdznlbs_'+inc).val()*1;

			yarnamount=consdznlbs*txtrateyarn;

			$('#txtamountyarn_'+inc).val( number_format_common( yarnamount,1,0,cbo_currercy) );
		}

		set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtavgconsumptionyarn_sum', 'avgconsqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtconsumptionyarnlbs_sum', 'txtconsdznlbs_', 'tbl_yarn_cost' );
		//set_sum_value( 'txtratecal_sum', 'txtrateyarn_', 'tbl_yarn_cost' );
		set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
	}

	function set_yarn_rate(i)
	{
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
		if(cost_control_source==7)
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
					release_freezing();
					alert('Fabric cost is greater than Quotation');
					return;
				}
			}
		}

	    var row_num=$('#tbl_yarn_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbocompone_'+i+'*percentone_'+i+'*consqnty_'+i+'*cbocount_'+i+'*cbotype_'+i,'Company Name*Comp 1*Percent*Cons Qnty*Count*Type')==false)
			{
				release_freezing();
				return;
			}
			//data_all=data_all+get_submitted_data_string('cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*color_'+i+'*cbotype_'+i+'*consqnty_'+i+'*txtrateyarn_'+i+'*avgconsqnty_'+i+'*txtconsdznlbs_'+i+'*txtamountyarn_'+i+'*supplier_'+i+'*updateidyarncost_'+i,"../../");
			
			data_all+="&cbocount_" + z + "='" + $('#cbocount_'+i).val()+"'"+"&cbocompone_" + z + "='" + $('#cbocompone_'+i).val()+"'"+"&percentone_" + z + "='" + $('#percentone_'+i).val()+"'"+"&color_" + z + "='" + $('#color_'+i).val()+"'"+"&cbotype_" + z + "='" + $('#cbotype_'+i).val()+"'"+"&consqnty_" + z + "='" + $('#consqnty_'+i).val()+"'"+"&txtrateyarn_" + z + "='" + $('#txtrateyarn_'+i).val()+"'"+"&avgconsqnty_" + z + "='" + $('#avgconsqnty_'+i).val()+"'"+"&txtconsdznlbs_" + z + "='" + $('#txtconsdznlbs_'+i).val()+"'"+"&txtamountyarn_" + z + "='" + $('#txtamountyarn_'+i).val()+"'"+"&supplier_" + z + "='" + $('#supplier_'+i).val()+"'"+"&supplier_source_" + z + "='" + $('#supplier_source_'+i).val()+"'"+"&updateidyarncost_" + z + "='" + $('#updateidyarncost_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsumptionyarn_sum*txtamountyarn_sum',"../../")+data_all;
		//var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
			  $('#txtprocessloss_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
			  $('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
			  $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+");");
			  $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost');");
			  $('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			  $('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			  $('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit( "+i+","+conversion_from_chart+")");
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
		set_conversion_qnty(i);
		var cost_head= $('#cbotypeconversion_'+i).val();
		//var conversion_process_loss=return_global_ajax_value(cost_head, 'set_conversion_process_loss', '', 'requires/pre_cost_entry_controller_v2');
		var conversion_qnty=return_global_ajax_value(cost_head, 'set_conversion_charge', '', 'requires/pre_cost_entry_controller_v2');
	
		//document.getElementById(txtchargeunit_id+i).value = conversion_qnty
		//math_operation( 'txtamountconversion_'+i, 'txtreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		//set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
		//set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
		//set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		if(cost_head==30 || cost_head==31)
		{
			document.getElementById('txtchargeunit_'+i).readOnly=true
			charge_unit_color_popup(i,conversion_qnty,conversion_from_chart)
		}
		else
		{
			if(conversion_from_chart==1)
			{
				//document.getElementById('txtchargeunit_'+i).disabled=true
				//if(cost_head==1)//cost_head==35 ||
				//{
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
				/*}else{
				document.getElementById('txtchargeunit_'+i).readOnly=false
				}*/
			}
			else
			{
				document.getElementById('txtchargeunit_'+i).readOnly=false
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
		var page_link='requires/pre_cost_entry_controller_v2.php?action=conversion_chart_popup&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&coversionchargelibraryid='+coversionchargelibraryid;
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
	function set_conversion_qnty(i){
	  var cbocosthead= document.getElementById('cbocosthead_'+i).value;
	  var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
	  var txtprocessloss=document.getElementById('txtprocessloss_'+i).value;
	  var updateidcoversion=$('#updateidcoversion_'+i).val();
	  if(cbotypeconversion==30 || cbotypeconversion==31){
		  return;
	  }
	  if(cbocosthead !=0){
		  var conversion_qnty=trim(return_global_ajax_value(cbocosthead+"_"+cbotypeconversion+"_"+txtprocessloss+"_"+updateidcoversion, 'set_conversion_qnty', '', 'requires/pre_cost_entry_controller_v2'));
	  }
	  if(cbocosthead ==0){
		  var conversion_qnty=document.getElementById('txtknit_sum').value+"_"+document.getElementById('txtknit_sum_production').value+"_"+txtprocessloss+"_"+updateidcoversion
	  }
	  conversion_qnty=conversion_qnty.split("_");
	  document.getElementById('txtreqqnty_'+i).value=trim(conversion_qnty[0]);
	  document.getElementById('txtavgreqqnty_'+i).value=trim(conversion_qnty[1]);
	
	  if((conversion_qnty[2]=="" || conversion_qnty[2]==0) && cbotypeconversion>0){
			$('#txtprocessloss_'+i).css({ 'background': 'white' });
			$('#txtprocessloss_'+i).attr( 'title','Process Loss not Set In Library' );
			$('#txtprocessloss_'+i).attr( 'readonly',false );
			$('#txtprocessloss_'+i).val(0)
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
	 /* math_operation( 'txtamountconversion_'+i, 'txtavgreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
	  set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
	  set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
	  set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
	  set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );*/
	  calculate_conversion_cost(i);
	}
	
	function calculate_conversion_cost(i){
		math_operation( 'txtamountconversion_'+i, 'txtavgreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
		set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
	}
	
	function charge_unit_color_popup(i,conversion_qnty,conversion_from_chart){
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value
			var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
			var cbo_currercy= document.getElementById('cbo_currercy').value;
			var cbocosthead=document.getElementById('cbocosthead_'+i).value;
			var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
			var job_no=document.getElementById('txt_job_no').value;
			var colorbreakdown=document.getElementById('colorbreakdown_'+i).value;
			var page_link="requires/pre_cost_entry_controller_v2.php?action=conversion_color_popup&cbocosthead="+cbocosthead+'&job_no='+job_no+'&conversion_qnty='+conversion_qnty+'&colorbreakdown='+colorbreakdown+'&conversion_from_chart='+conversion_from_chart+'&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_currercy='+cbo_currercy;
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
						var avg_cons=cons-(cons*txtprocessloss)/100;
						document.getElementById('txtreqqnty_'+i).value=number_format_common(avg_cons, 5, 0);
						document.getElementById('txtavgreqqnty_'+i).value=number_format_common(avg_cons, 5, 0);
						document.getElementById('coversionchargelibraryid_'+i).value=chargelibid_breck_down.value;
						calculate_conversion_cost(i)
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
		if(cost_control_source==7)
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
		var data_all="";  var z=1;
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('update_id*cbocosthead_'+i,'Company Name*Fabric Description')==false)
			{
				release_freezing();
				return;
			}
			//data_all=data_all+get_submitted_data_string('cbocosthead_'+i+'*cbotypeconversion_'+i+'*txtprocessloss_'+i+'*txtreqqnty_'+i+'*txtavgreqqnty_'+i+'*txtchargeunit_'+i+'*txtamountconversion_'+i+'*cbostatusconversion_'+i+'*updateidcoversion_'+i+'*colorbreakdown_'+i+'*coversionchargelibraryid_'+i,"../../");
			
			data_all+="&cbocosthead_" + z + "='" + $('#cbocosthead_'+i).val()+"'"+"&cbotypeconversion_" + z + "='" + $('#cbotypeconversion_'+i).val()+"'"+"&txtprocessloss_" + z + "='" + $('#txtprocessloss_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtavgreqqnty_" + z + "='" + $('#txtavgreqqnty_'+i).val()+"'"+"&txtchargeunit_" + z + "='" + $('#txtchargeunit_'+i).val()+"'"+"&txtamountconversion_" + z + "='" + $('#txtamountconversion_'+i).val()+"'"+"&cbostatusconversion_" + z + "='" + $('#cbostatusconversion_'+i).val()+"'"+"&updateidcoversion_" + z + "='" + $('#updateidcoversion_'+i).val()+"'"+"&colorbreakdown_" + z + "='" + $('#colorbreakdown_'+i).val()+"'"+"&coversionchargelibraryid_" + z + "='" + $('#coversionchargelibraryid_'+i).val()+"'";
			z++;
		}
		
		var data="action=save_update_delet_fabric_conversion_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*txt_cost_control_source*cbo_company_name*txt_quotation_id*copy_quatation_id*txtconreqnty_sum*txtconchargeunit_sum*txtconamount_sum',"../../")+data_all;
		
		//var data="action=save_update_delet_fabric_conversion_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
	
			  $('#cbointernaltext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_internalitem("+i+" )");
			  $('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+" )");
			  $('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup('"+i+"_1' )");
			  $('#cbonominasupplier_'+i).removeAttr("onChange").attr("onChange","set_trim_rate_amount( this.value,"+i+",'supplier_change' )");
			  $('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
			  $('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost');");
			  $('#txtconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
			  $('#txtdescription_'+i).removeAttr("onDblClick").attr("onDblClick","trims_description_popup( "+i+" )");
			
			  //$('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","open_calculator( "+i+" )");
			  $('#txtconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick"," set_session_large_post_data_trim('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_trim', 'Consumtion Entry Form',"+i+")");
			  $('#btnimg_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../', document.getElementById('updateTempleteTrimTd_"+i+"').value,'', 'knit_order_entry', 0 ,1)");
	
			 
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
			  $('#cbonominasupplier_'+i).removeAttr('disabled','disabled');
			  $('#txttrimrate_'+i).removeAttr('disabled','disabled');
			  $('#decreasetrim_'+i).removeAttr('disabled','disabled');
			  
			  $('#updateidtrim_'+i).val("");
			  $('#updateTempleteTrimTd_'+i).val("");
			  $('#cbogrouptext_'+i).val("");
			  $('#cbogroup_'+i).val("");
			  $('#countrytext_'+i).val("");
			  $('#country_'+i).val("");
			 
			  $('#txtdescription_'+i).val("");
			  $('#txtsupref_'+i).val("");
			  $('#cboconsuom_'+i).val(0);
			  $('#consbreckdown_'+i).val("");
			  $('#txtconsdzngmts_'+i).val("");
			  $('#txttrimrate_'+i).val("");
			  $('#txttrimamount_'+i).val("");
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
	
		var res=return_global_ajax_value(txt_job_no+'__'+type, 'load_total_qtyAmount', '', 'requires/pre_cost_entry_controller_v2');
		if(type=='trim')
		{
			var updateidtrim=document.getElementById('updateidtrim_'+i).value;
			var resObj=JSON.parse(res);
			var row_num=$('#tbl_trim_cost tr').length-1;
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
				}
			}
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=350px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			var theemailname=this.contentDoc.getElementById("txt_selected_name");
			//if (trim(theemail.value)!="")
			//{
				//freeze_window(5);
				document.getElementById('country_'+i).value=trim(theemail.value);
				document.getElementById('countrytext_'+i).value=trim(theemailname.value);
				//release_freezing();
			//}
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
					open_consumption_popup_trim(page_link,title,trorder)
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
		var txttrimrate=document.getElementById('txttrimrate_'+trorder).value;
		var item_id=document.getElementById('item_id').value;
		var calculatorstring=document.getElementById('calculatorstring_'+trorder).value;
		var cbogrouptext=document.getElementById('cbogrouptext_'+trorder).value;
		var updateidtrim=document.getElementById('updateidtrim_'+trorder).value;
		var txtconsdzngmts = document.getElementById('txtconsdzngmts_'+trorder).value;
		if(($('#txtconsdzngmts_'+trorder).attr('tmp_cons')*1)!=0)
		{
			var txtconsdzngmts=$('#txtconsdzngmts_'+trorder).attr('tmp_cons')*1;
		}
		var txtexper=document.getElementById('txtexper_'+trorder).value;
		var calculator_parameter=return_global_ajax_value(cbogroup, 'calculator_parameter', '', 'requires/pre_cost_entry_controller_v2');
		var page_link=page_link+'&txt_job_no='+txt_job_no+'&cbo_costing_per='+cbo_costing_per+'&calculator_parameter='+trim(calculator_parameter)+'&cons_breck_downn='+cons_breck_downn+'&cbo_approved_status='+cbo_approved_status+'&cbogroup='+cbogroup+'&cboconsuom='+cboconsuom+'&tot_set_qnty='+tot_set_qnty+'&country='+country+'&txttrimrate='+txttrimrate+'&item_id='+item_id+'&calculatorstring='+calculatorstring+"&cbogrouptext="+cbogrouptext+"&updateidtrim="+updateidtrim+"&txtconsdzngmts="+txtconsdzngmts+"&txtexper="+txtexper;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title+"  "+cbogrouptext, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var calculator_string=this.contentDoc.getElementById("calculator_string");
			
			var avg_tot_cons=this.contentDoc.getElementById("avg_cons");
			//document.getElementById('txtconsdzngmts_'+trorder).value=avg_cons.value;
			var avg_ex_per=this.contentDoc.getElementById("avg_exper");
			var avg_totcons=this.contentDoc.getElementById("avg_totcons");
			var avg_amount=this.contentDoc.getElementById("avg_amountcons");
			var avg_rate=this.contentDoc.getElementById("avg_ratecons");
			document.getElementById('excessper_'+trorder).value=avg_ex_per.value;//avg popup
			document.getElementById('totalcons_'+trorder).value=avg_tot_cons.value;
			document.getElementById('txtconsdzngmts_'+trorder).value=avg_totcons.value;
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
	
		var page_link="requires/pre_cost_entry_controller_v2.php?cbogroup="+trim(cbogroup)+"&action=trim_rate_popup_page";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Rate', 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var txt_selected_supllier=this.contentDoc.getElementById("txt_selected_supllier");
			var txt_selected_rate=this.contentDoc.getElementById("txt_selected_rate");
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
		}
	}
	
	function openpopup_internalitem(i){
		var updateidtrim=document.getElementById('updateidtrim_'+i).value
		var job_id=document.getElementById('hidd_job_id').value
		if(updateidtrim==''){
			alert("Please Save Trims Item First.");
			return;
		}
		var page_link="requires/pre_cost_entry_controller_v2.php?action=openpopup_internalitem&trimid="+updateidtrim+"&jobid="+job_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Select', 'width=550px,height=200px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
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
		//var cbo_cons_uom=return_global_ajax_value(trim_group_id, 'set_cons_uom', '', 'requires/pre_cost_entry_controller_v2');
		//document.getElementById('cboconsuom_'+i).value = trim(cbo_cons_uom);
		var trim_rate_variable=document.getElementById('trim_rate_variable').value;
		var buyer=document.getElementById('cbo_buyer_name').value;
		set_trim_rate_amount( document.getElementById('cbonominasupplier_'+i).value,i,'item_change' );
		load_drop_down( 'requires/pre_cost_entry_controller_v2', trim_group_id+"_"+i+"_"+trim_rate_variable+"_"+buyer, 'load_drop_down_supplier_rate', 'tdsupplier_'+i );
		//alert();
	}
	
	function set_trim_rate_amount(supplier,i,type)
	{
		var updateidtrim=document.getElementById('updateidtrim_'+i).value;
		if(updateidtrim=="")
		{
			if(type=="item_change")
			{
				get_trim_rate_amount(supplier,i,type)
			}
			else
			{
				var txttrimrate=document.getElementById('txttrimrate_'+i).value;
				if(txttrimrate==0 || txttrimrate=="")
				{
				get_trim_rate_amount(supplier,i,type)
				}
				else
				{
					var r=confirm("Rate Exist,\n It may have come from price Quotation or Templete or Library\n If you want to change current rate\n Press OK \n Otherwise press Cencel");
					if(r==true)
					{
						get_trim_rate_amount(supplier,i,type)
					}
					else
					{
						return;
					}
				}
			}
		}
		else
		{
			if(type=="item_change")
			{
				get_trim_rate_amount(supplier,i,type)
			}
			else
			{
				var r=confirm("Rate Exist,\n You are in update mode\n If you want to change current rate\n Press OK \n Otherwise press Cencel");
				if(r==true)
				{
					get_trim_rate_amount(supplier,i,type)
				}
				else
				{
					return;
				}
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
				else
				{
				return;
				}
			}
			else
			{
				document.getElementById('txttrimrate_'+i).value=rate;
			}
		}
		calculate_trim_cost(i)
	}

	function trims_description_popup(i)
	{
		var txtdescription=document.getElementById('txtdescription_'+i).value;
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
		if(cost_control_source==7)
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
				release_freezing();
				alert('Trims cost is greater than Quotation');
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
			if (form_validation('update_id*cbogroup_'+i+'*txtconsdzngmts_'+i,'Company Name*Group*Cons/Unit Gmts')==false)
			{
				release_freezing();
				return;
			}//txtremark_

			/*var txtdescription=$('#txtdescription_'+i).val();
			var newstr = txtdescription.replace(reg, '');
			alert(newstr);
			var txtsupref=$('#txtsupref_'+i).val();
			var txtremark=$('#txtremark_'+i).val();
			//alert(txtdescription.match(reg))
			if(txtdescription.match(reg)){
				alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
				release_freezing();
				return;
			}
			if(txtsupref.match(reg)){
				alert("Your Brand/Sup Ref Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
				release_freezing();
				return;
			}
			if(txtremark.match(reg)){
				alert("Your Remarks Ref Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
				release_freezing();
				return;
			} */
			
			data_all+="&cbogroup_" + z + "='" + $('#cbogroup_'+i).val()+"'"+"&txtdescription_" + z + "='" + $('#txtdescription_'+i).val()+"'"+"&txtsupref_" + z + "='" + $('#txtsupref_'+i).val()+"'"+"&cboconsuom_" + z + "='" + $('#cboconsuom_'+i).val()+"'"+"&txtconsdzngmts_" + z + "='" + $('#txtconsdzngmts_'+i).val()+"'"+"&txttrimrate_" + z + "='" + $('#txttrimrate_'+i).val()+"'"+"&txttrimamount_" + z + "='" + $('#txttrimamount_'+i).val()+"'"+"&cboapbrequired_" + z + "='" + $('#cboapbrequired_'+i).val()+"'"+"&cbonominasupplier_" + z + "='" + $('#cbonominasupplier_'+i).val()+"'"+"&cbotrimstatus_" + z + "='" + $('#cbotrimstatus_'+i).val()+"'"+"&updateidtrim_" + z + "='" + $('#updateidtrim_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&calculatorstring_" + z + "='" + $('#calculatorstring_'+i).val()+"'"+"&updateTempleteTrimTd_" + z + "='" + $('#updateTempleteTrimTd_'+i).val()+"'"+"&seq_" + z + "='" + $('#seq_'+i).val()+"'"+"&cboitemprint_" + z + "='" + $('#cboitemprint_'+i).val()+"'"+"&excessper_" + z + "='" + $('#excessper_'+i).val()+"'"+"&totalcons_" + z + "='" + $('#totalcons_'+i).val()+"'";
			
			z++;
		}
		var data="action=save_update_delet_trim_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsdzntrim_sum*txtratetrim_sum*txttrimamount_sum',"../../")+data_all;
		
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
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				show_sub_form(document.getElementById('update_id').value, 'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value);
	
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[3])
					var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					document.getElementById('txt_comml_pre_cost').value=reponse[3];
					document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
					calculate_main_total()
					fnc_quotation_entry_dtls1(1)
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
			  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost');");
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
		load_drop_down( 'requires/pre_cost_entry_controller_v2', cboembname+'_'+i+'_'+company_id, 'load_drop_down_embtype', 'embtypetd_'+i );
		get_php_form_data(cboembname+'_'+i+'_'+company_id,'load_drop_down_embtype_budgeton','requires/pre_cost_entry_controller_v2' );

		check_duplicate(i)
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
		math_operation( 'txtembamount_'+i, 'txtembconsdzngmts_'+i+'*txtembrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
	}

	function fnc_embellishment_cost_dtls( operation )
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
		if(cost_control_source==7)
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

	    var row_num=$('#tbl_embellishment_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id','Company Name')==false)
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
					if(document.getElementById('cboembname_'+i).value==document.getElementById('cboembname_'+k).value && document.getElementById('cboembtype_'+i).value==document.getElementById('cboembtype_'+k).value && document.getElementById('cboembbodypart_'+i).value==document.getElementById('cboembbodypart_'+k).value  && document.getElementById('countrytext_'+i).value==document.getElementById('countrytext_'+k).value  && document.getElementById('txtembsupplier_'+i).value==document.getElementById('txtembsupplier_'+k).value)
					{
						alert("Same Name, Same Type, Same Body Part, Same Country, Same Suppiler, Duplication Not Allowed.");
						release_freezing();
						return;
						//document.getElementById('cboembtype_'+id).value=0;
						//document.getElementById('cboembbodypart_'+id).value=0;
						//document.getElementById(td).focus();
					}
				}
			}

			var cboembname=document.getElementById('cboembname_'+i).value;
			var cboembtype=document.getElementById('cboembtype_'+i).value;
			var txtembconsdzngmts=document.getElementById('txtembconsdzngmts_'+i).value*1;

			if(cboembname ==1 && txtembconsdzngmts >0 && cboembtype==0){
				alert("Select Print Type");
				release_freezing();
				return;

			}
			/*var consbreckdownemb=document.getElementById('consbreckdownemb_'+i).value;
			if(consbreckdownemb==""){
				alert("Please add Breakdown");
				$('#txtembconsdzngmts_'+i).css('background-color', 'red');
				release_freezing();
				return;
			}*/

			//data_all=data_all+get_submitted_data_string('cboembname_'+i+'*cboembtype_'+i+'*cboembbodypart_'+i+'*country_'+i+'*cboembsupplierid_'+i+'*txtembconsdzngmts_'+i+'*txtembrate_'+i+'*txtembamount_'+i+'*cboembstatus_'+i+'*embupdateid_'+i+'*consbreckdownemb_'+i+'*empbudgeton_'+i,"../../");
			
			data_all+="&cboembname_" + z + "='" + $('#cboembname_'+i).val()+"'"+"&cboembtype_" + z + "='" + $('#cboembtype_'+i).val()+"'"+"&cboembbodypart_" + z + "='" + $('#cboembbodypart_'+i).val()+"'"+"&cboembsupplierid_" + z + "='" + $('#cboembsupplierid_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&txtembconsdzngmts_" + z + "='" + $('#txtembconsdzngmts_'+i).val()+"'"+"&txtembrate_" + z + "='" + $('#txtembrate_'+i).val()+"'"+"&txtembamount_" + z + "='" + $('#txtembamount_'+i).val()+"'"+"&cboembstatus_" + z + "='" + $('#cboembstatus_'+i).val()+"'"+"&embupdateid_" + z + "='" + $('#embupdateid_'+i).val()+"'"+"&consbreckdownemb_" + z + "='" + $('#consbreckdownemb_'+i).val()+"'"+"&empbudgeton_" + z + "='" + $('#empbudgeton_'+i).val()+"'";
			z++;
		}
		//alert(data_all); return;
		var data="action=save_update_delet_embellishment_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_cost_control_source*txtamountemb_sum',"../../")+data_all;
		//var data="action=save_update_delet_embellishment_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
				show_sub_form(document.getElementById('update_id').value, 'show_embellishment_cost_listview');
				//document.getElementById('check_open_close_fabric_cost').checked=true;
				//show_content_form('fabric_cost');
				//show_hide_content('embellishment_cost', '')

				//set_button_status(0, permission, 'fnc_embellishment_cost_dtls',7);
				//set_button_status(is_update, permission, submit_func,btn_id)
				//release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					fnc_quotation_entry_dtls1(1);
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
			 $("#tbl_wash_cost tr:last").clone().find("input,select,td").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
	
			  }).end().appendTo("#tbl_wash_cost");
	
			 // $('#txtembamount_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr_embellishment_cost("+i+");");
			  //$('#embtypetd_'+i).removeAttr("id").attr("id","'fabriccosttbltr_'+i");
			  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_wash_cost("+i+");");
			  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost');");
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

	function calculate_cm_cost_dtls(i)
	{
		set_sum_value( 'txtratecm_sum', 'txtcmrate_', 'tbl_cm_cost' );
	}

	function fnc_cm_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}

		var row_num=$('#tbl_cm_cost tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			/*if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
			}*/

			data_all=data_all+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txtparticular_'+i+'*txtcmrate_'+i+'*txtcmdtlsid_'+i+'*txtratecm_sum',"../../");
		}
		var data="action=save_update_delete_cm_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cm_cost_dtls_reponse;
	}
	
	function fnc_cm_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(reponse[0]==10)
			{
				show_msg(reponse[0]);
				release_freezing();
				// setTimeout('fnc_cm_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				show_sub_form(document.getElementById('update_id').value, 'show_cm_cost_listview');
				if(reponse[0]==0 || reponse[0]==1)
				{
						fnc_quotation_entry_dtls1( 1 )
						release_freezing();
				}
			}
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
		if(cost_control_source==7)
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

			//data_all=data_all+get_submitted_data_string('cboembname_'+i+'*cboembtype_'+i+'*country_'+i+'*txtembconsdzngmts_'+i+'*txtembrate_'+i+'*txtembamount_'+i+'*cboembstatus_'+i+'*embupdateid_'+i+'*embratelibid_'+i+'*consbreckdownwash_'+i+'*empbudgeton_'+i,"../../");
			data_all+="&cboembname_" + z + "='" + $('#cboembname_'+i).val()+"'"+"&cboembtype_" + z + "='" + $('#cboembtype_'+i).val()+"'"+"&country_" + z + "='" + $('#country_'+i).val()+"'"+"&txtembconsdzngmts_" + z + "='" + $('#txtembconsdzngmts_'+i).val()+"'"+"&txtembrate_" + z + "='" + $('#txtembrate_'+i).val()+"'"+"&txtembamount_" + z + "='" + $('#txtembamount_'+i).val()+"'"+"&cboembstatus_" + z + "='" + $('#cboembstatus_'+i).val()+"'"+"&embupdateid_" + z + "='" + $('#embupdateid_'+i).val()+"'"+"&embratelibid_" + z + "='" + $('#embratelibid_'+i).val()+"'"+"&consbreckdownwash_" + z + "='" + $('#consbreckdownwash_'+i).val()+"'"+"&empbudgeton_" + z + "='" + $('#empbudgeton_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_wash_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_cost_control_source*txtamountemb_sum',"../../")+data_all;
		//var data="action=save_update_delet_wash_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
		
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
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
			  $('#decreasecommission_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_commission_cost');");
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
		if(cost_control_source==7)
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
	
			//data_all=data_all+get_submitted_data_string('cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionrate_'+i+'*txtcommissionamount_'+i+'*cbocommissionstatus_'+i+'*commissionupdateid_'+i,"../../");
			
			data_all+="&cboparticulars_" + z + "='" + $('#cboparticulars_'+i).val()+"'"+"&cbocommissionbase_" + z + "='" + $('#cbocommissionbase_'+i).val()+"'"+"&txtcommissionrate_" + z + "='" + $('#txtcommissionrate_'+i).val()+"'"+"&txtcommissionamount_" + z + "='" + $('#txtcommissionamount_'+i).val()+"'"+"&cbocommissionstatus_" + z + "='" + $('#cbocommissionstatus_'+i).val()+"'"+"&commissionupdateid_" + z + "='" + $('#commissionupdateid_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delet_commission_cost_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*copy_quatation_id*txt_quotation_id*txt_cost_control_source*txtratecommission_sum*txtamountcommission_sum',"../../")+data_all;
		
		//var data="action=save_update_delet_commission_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
			$('#decreasecomarcial_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_comarcial_cost');");
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
			var sum_fab_yarn_trim=return_global_ajax_value(update_id+'_'+txt_commercial_cost_method, 'sum_comarcial_cost_value', '', 'requires/pre_cost_entry_controller_v2');
			var amount=number_format_common(sum_fab_yarn_trim, 1, 0, currency);
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
			//var amount=(txtcomarcialrate*total_cost)/100;
			//document.getElementById('txtcomarcialamount_'+i).value=number_format_common(amount, 1, 0,document.getElementById('cbo_currercy').value);
			var com_amount=amount*(txtcomarcialrate/100);
			document.getElementById('txtcomarcialamount_'+i).value=number_format_common(com_amount, 1, 0, currency);
		}
		if(type=='cal_rate')
		{
			//var amount=(txtcomarcialamount/total_cost)*100;
			//document.getElementById('txtcomarcialrate_'+i).value=number_format_common(amount, 3, 0);
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
		if(cost_control_source==7)
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
				alert('Comarcial cost is greater than Quotation');
				release_freezing();
				return
				}
			}
		}

		var data_all="";  var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
			}
			
			data_all+="&cboitem_"+z+"='"+$('#cboitem_'+i).val()+"'"+"&txtcomarcialrate_"+z+"='"+$('#txtcomarcialrate_'+i).val()+"'"+"&txtcomarcialamount_"+z+"='"+$('#txtcomarcialamount_'+i).val()+"'"+"&cbocomarcialstatus_"+z+"='" + $('#cbocomarcialstatus_'+i).val()+"'"+"&comarcialupdateid_" + z + "='" + $('#comarcialupdateid_'+i).val()+"'";
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
			if(trim(reponse[0])==10){
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
					var txt_comm_pre_cost=$('#txtamountcomarcial_sum').val()*1;
					var pre_commcost=number_format_common(txt_comm_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_comml_pre_cost").attr('pre_comml_cost',pre_commcost);
					fnc_quotation_entry_dtls1(1)
					release_freezing();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_comarcial_cost_listview');
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
		if(cost_control_source==7)
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
				if(txt_total_pre_cost>(pri_total_pre_cost+pri_commission_pre_cost))
				{
					alert("Total Cost ("+txt_total_pre_cost+") is greater than Quotation ("+pri_total_pre_cost+pri_commission_pre_cost+")");
					return;
				}
			}
		}

		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*cbo_costing_per*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_total_pre_cost*txt_total_po_price*txt_final_price_dzn_pre_cost*txt_final_price_dzn_po_price*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_total_pre_cost_psc_set*txt_total_pre_cost_psc_set_po_price*txt_final_price_pcs_pre_cost*txt_final_price_pcs_po_price*txt_margin_pcs_pre_cost*txt_margin_pcs_po_price*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_deffdlc_pre_cost*txt_deffdlc_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_incometax_pre_cost*txt_incometax_po_price*txt_design_pre_cost*txt_design_po_price*txt_studio_pre_cost*txt_studio_po_price',"../../");
			//alert(data); return;
			http.onreadystatechange = function() {
				if( http.readyState == 4 && http.status == 200 ) {
					var reponse=trim(http.responseText).split('**');
					//alert(reponse[0]);
					if(reponse[0]=='quataNotApp')
					{
						alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
					if(reponse[0]==11)
					{
						alert(reponse[1]);
						release_freezing();
						return;
					}
					if (reponse[0].length>2) reponse[0]=10;
					show_msg(reponse[0]);
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
		//alert("monzu");
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
				reset_form('precosting_1', 'cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2*is_click_cons_box,1*txt_incoterm_place,Chittagong','','txt_costing_date*txt_exchange_rate')
				get_php_form_data( theemail.value, action, "requires/pre_cost_entry_controller_v2" );
				var txt_precost_id=document.getElementById("txt_precost_id").value;
				get_php_form_data( theemail.value, 'check_data_mismass', "requires/pre_cost_entry_controller_v2");
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=560px,height=250px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			//var set_breck_down=this.contentDoc.getElementById("set_breck_down") //Access form field with id="emailfield"
			//var item_id=this.contentDoc.getElementById("item_id") //Access form field with id="emailfield"
			//var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty") //Access form field with id="emailfield"
			//var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			//document.getElementById('set_breck_down').value=set_breck_down.value;
			//document.getElementById('item_id').value=item_id.value;
			//document.getElementById('txt_sew_smv').value=tot_smv_qnty.value;
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
		if(cost_control_source==1 || cost_control_source==7)
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


		if(operation==1)
		{
			if(($('#txt_sew_efficiency_source').val()*1)!=1)
			{
				get_php_form_data( $("#cbo_company_name").val()+"**"+$("#txt_job_no").val()+"**"+$("#txt_sew_smv").val(), 'get_efficiency_percent', "requires/pre_cost_entry_controller_v2" );
				calculate_cm_cost_with_method();
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
		if (form_validation('txt_job_no*txt_costing_date*cbo_inco_term*txt_incoterm_place*cbo_costing_per','Job No*Costing Date*Incoterm*Incoterm Palce*Costing Per')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			//eval(get_submitted_variables('garments_nature*txt_job_no*txt_costing_date*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_remarks*update_id*copy_quatation_id*cbo_approved_status'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('garments_nature*txt_job_no*hidd_job_id*txt_costing_date*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_remarks*update_id*update_id*copy_quatation_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_ready_to_approved*txt_budget_minute*cbo_company_name*txt_quotation_id*txt_sew_efficiency_source',"../../");


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
			if(reponse[0]=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
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
				document.getElementById('txt_cm_pre_cost').disabled=true;
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

	function cm_cost_predefined_method(cm_cost_method)
	{
		//alert(company_id)
		//var cm_cost_method=return_global_ajax_value(company_id, 'cm_cost_predefined_method', '', 'requires/pre_cost_entry_controller_v2');
		//alert(cm_cost_method);
		if(cm_cost_method ==0)
		{
			if($("#txt_quotation_id").val()!="" && $("#txt_quotation_id").val()!=0)
			{
				$("#txt_cm_pre_cost").attr("disabled",true);
			}
			else
			{
				document.getElementById('txt_cm_pre_cost').disabled=false;
			}
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",true);
			//$("#txt_sew_efficiency_per").attr("disabled",false);
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
		document.getElementById('cm_cost_predefined_method_id').value=cm_cost_method;
	}
	
	//Master Form End-----------------------------------------------------
	
	// Dtls Form 1--------------------------------------------------------
	function calculate_cm_cost_with_method()
	{
		var cm_cost=0;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var cm_cost_predefined_method_id=(document.getElementById('cm_cost_predefined_method_id').value)*1;
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

		var cpm=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date+'_'+txt_job_no, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');

		var data=cpm.split("_");
		//alert(cm_cost_predefined_method_id)
		if(cm_cost_predefined_method_id==1)
		{
			if(data[3]==0 || data[3]=="" )
			{
				alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
				return;
			}
			var txt_efficiency_wastage=100-txt_sew_efficiency_per;
			document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
			var cm_cost=(txt_sew_smv*data[3]*cbo_costing_per_value)+((txt_sew_smv*data[3]*cbo_costing_per_value)*(txt_efficiency_wastage/100));
			//alert(txt_exchange_rate)
			cm_cost=cm_cost/txt_exchange_rate;
			//alert(cm_cost)
		}
		if(cm_cost_predefined_method_id==2)
		{
			if(data[3]==0 ||data[3]=="" )
			{
				alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
				return;
			}
			//alert(data[3])
			var cut_per=txt_cut_efficiency_per/100;
			var sew_per=txt_sew_efficiency_per/100;
			var cu=(txt_cut_smv*trim(data[3])*cbo_costing_per_value)/cut_per
			if(isNaN(cu))
			{
				cu=0
			}

			var su=(txt_sew_smv*trim(data[3])*cbo_costing_per_value)/sew_per
			//alert("("+txt_sew_smv+"*"+data[3]+"*"+cbo_costing_per_value+")/"+sew_per)
			if(isNaN(su))
			{
				su=0
			}
			var cm_cost=(cu+su)/txt_exchange_rate;
		}

		if(cm_cost_predefined_method_id==3)
		{
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
				alert("Insert Monthly CM Expense in Library>Merchandising Detailes>Financial Parameter Setup");
				return;
			}
			if(data[1]==0)
			{
				alert("Insert No. of Factory Machine  in Library>Merchandising Detailes>Financial Parameter Setup");
				return;
			}
			if(data[2]==0)
			{
				alert("Insert Working Hour in Library>Merchandising Detailes>Financial Parameter Setup");
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
				alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
				return;
			}
			var sew_per=txt_sew_efficiency_per/100;
			var su=((trim(data[3])/sew_per)*txt_sew_smv*cbo_costing_per_value);
			cm_cost=su/txt_exchange_rate;
		}
		if(cm_cost!=Infinity && cm_cost_predefined_method_id>0)
		{
			document.getElementById('txt_cm_pre_cost').value=number_format_common(cm_cost,1,0,cbo_currercy)	;
			calculate_main_total()
		}
	}

	function calculate_main_total()
	{
		var currency=$("#cbo_currercy").val()*1;
		var dblTot_fa=($("#txt_fabric_pre_cost").val()*1)+($("#txt_trim_pre_cost").val()*1)+($("#txt_embel_pre_cost").val()*1)+($("#txt_wash_pre_cost").val()*1)+($("#txt_comml_pre_cost").val()*1)+($("#txt_commission_pre_cost").val()*1)+($("#txt_lab_test_pre_cost").val()*1)+($("#txt_inspection_pre_cost").val()*1)+($("#txt_cm_pre_cost").val()*1)+($("#txt_freight_pre_cost").val()*1)+($("#txt_currier_pre_cost").val()*1)+($("#txt_certificate_pre_cost").val()*1)+($("#txt_common_oh_pre_cost").val()*1)+($("#txt_depr_amor_pre_cost").val()*1)+($("#txt_incometax_pre_cost").val()*1)+($("#txt_interest_pre_cost").val()*1)+($("#txt_deffdlc_pre_cost").val()*1)+($("#txt_design_pre_cost").val()*1)+($("#txt_studio_pre_cost").val()*1);

		$("#txt_total_pre_cost").val( number_format_common(dblTot_fa, 1, 0, currency) );

		//alert(dblTot_fa);
		calculate_total_cost_psc_set()
		clculate_margin_dzn();
		calculate_margin_pcs_set();
		calculate_percent_on_po_price()
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

	function calculate_confirm_price_dzn()
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
		calculate_margin_pcs_set()
		calculate_percent_on_po_price();

		calculate_depreciation_amortization();
		calculate_oparating_expanseses();
		calculate_deffd_lc();
		calculate_interest_cost();
  		calculate_income_tax();
	}

	function calculate_depreciation_amortization()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var txt_costing_date= document.getElementById('txt_costing_date').value;

		var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value*1;
		var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var depreciation_amortization_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
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
		calculate_main_total()
	}

	function calculate_oparating_expanseses()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var txt_costing_date=document.getElementById('txt_costing_date').value;

		var txt_final_price_dzn_pre_cost=document.getElementById('txt_final_price_dzn_pre_cost').value*1;
		var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		//var oparating_expanses_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
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
		var response=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_booking_found', 'requires/pre_cost_entry_controller_v2');
		response=response.split("_");
	}

	function change_cost_per(costing_per){
		var is_used_costing_per=return_ajax_request_value(document.getElementById('txt_job_no').value, 'is_used_costing_per', 'requires/pre_cost_entry_controller_v2');
		var data=is_used_costing_per.split("_");
		if(data[0]>0){
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
				calculate_confirm_price_dzn()
			}
			if(value==2)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 1 Pcs";
				document.getElementById('margin_dzn').innerHTML="Margin/ 1 Pcs";
				calculate_confirm_price_dzn()
			}
			if(value==3)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 2 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 2 Dzn";
				calculate_confirm_price_dzn()
			}
			if(value==4)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 3 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 3 Dzn";
				calculate_confirm_price_dzn()
			}
			if(value==5)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 4 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 4 Dzn";
				calculate_confirm_price_dzn()
			}
		}
		if(type=="change_caption_pcs")
		{
			if(value==1)
			{
				document.getElementById('price_pcs_td').innerHTML="Price/Pcs  ";
				document.getElementById('margin_pcs_td').innerHTML="Margin/pcs ";
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/Pcs ";
			}
			if(value==58)
			{
				document.getElementById('price_pcs_td').innerHTML="Price/Set ";
				document.getElementById('margin_pcs_td').innerHTML="Margin/Set";
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/Set ";
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
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		var commission_cost=$("#txt_commission_pre_cost").val()*1;      var pre_commission_cost=$("#txt_commission_pre_cost").attr('pre_commis_cost')*1;
		
		var comml_cost=$("#txt_comml_pre_cost").val()*1;      var pre_comml_cost=$("#txt_comml_pre_cost").attr('pre_comml_cost')*1;
		var wash_cost=$("#txt_wash_pre_cost").val()*1;		var pre_wash_cost=$("#txt_wash_pre_cost").attr('pre_wash_cost')*1;
		var embl_cost=$("#txt_embel_pre_cost").val()*1;		var pre_embl_cost=$("#txt_embel_pre_cost").attr('pre_emb_cost')*1;
		var trim_cost=$("#txt_trim_pre_cost").val()*1;		var pre_trim_cost=$("#txt_trim_pre_cost").attr('pre_trim_cost')*1;
		var fab_cost=$("#txt_fabric_pre_cost").val()*1;		var pre_fab_cost=$("#txt_fabric_pre_cost").attr('pre_fab_cost')*1;
		
		if(fab_cost!=pre_fab_cost)
		{
			//console.log(fab_cost+'!='+pre_fab_cost);
			alert("Fabric Cost Change Found, Please Save or Update.");
			release_freezing();
			return;
		}
		if(trim_cost!=pre_trim_cost)
		{
			//console.log(trim_cost+'!='+pre_trim_cost);
			alert("Trims Cost Change Found, Please Save or Update");
			release_freezing();
			return;
		}
		
		if(commission_cost!=pre_commission_cost)
		{
			release_freezing();
		    alert("Commission Cost Change Found, Please Save or Update.");
		    return;
		}
		if(comml_cost!=pre_comml_cost)
		{
			release_freezing();
		    alert("Comml. Cost Change Found, Please Save or Update.");
		    return;
		}
		if(embl_cost!=pre_embl_cost)
		{
			//console.log(embl_cost+'!='+pre_embl_cost);
			alert("Embel. Cost Change Found, Please Save or Update.");
			release_freezing();
			return;
		}
		if(wash_cost!=pre_wash_cost)
		{
			release_freezing();
		    alert("Wash Cost Change Found, Please Save or Update.");
		    return;
		}
		
		if(cost_control_source==7)
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
				if(txt_total_pre_cost>(pri_total_pre_cost+pri_commission_pre_cost))
				{
					release_freezing();
					alert("Total Cost ("+txt_total_pre_cost+") is greater than Quotation ("+pri_total_pre_cost+pri_commission_pre_cost+")");
					return;
				}
			}
		}

		if (form_validation('cbo_company_name','Company Name')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*cbo_costing_per*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_total_pre_cost*txt_total_po_price*txt_final_price_dzn_pre_cost*txt_final_price_dzn_po_price*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_total_pre_cost_psc_set*txt_total_pre_cost_psc_set_po_price*txt_final_price_pcs_pre_cost*txt_final_price_pcs_po_price*txt_margin_pcs_pre_cost*txt_margin_pcs_po_price*cbo_company_name*txt_quotation_id*copy_quatation_id*txt_deffdlc_pre_cost*txt_deffdlc_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_incometax_pre_cost*txt_incometax_po_price*txt_design_pre_cost*txt_design_po_price*txt_studio_pre_cost*txt_studio_po_price',"../../");

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
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
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
			if(reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				show_msg(reponse[0]);
				document.getElementById('update_id_dtls').value  = reponse[2];
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
			var rate_amt=2; var zero_val='';
			if(type!='mo_sheet')
			{
				var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			}

			if (r==true) zero_val="1"; else zero_val="0";
			//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
			var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*cbo_costing_per',"../../");
			freeze_window(3);
			if(type == 'preCostRpt4'){
				http.open("POST","requires/pre_cost_entry_report_controller_v2.php",true);
			}
			else{
				http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_reponse;
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
		var response=return_global_ajax_value( cbo_currercy+"**"+costing_date, 'check_conversion_rate', '', 'requires/pre_cost_entry_controller_v2');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function is_manula_approved(compony_id){
		var response=return_global_ajax_value( compony_id, 'is_manula_approved', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(response)==1){
			$('#approve1').hide();
		}
		if(trim(response)==2 || trim(response)==0){
			$('#approve1').show();
		}
	}
	//kaiyum
	
	//Master form---------------------------------------------------------------------------
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

		//var sp_oparation_pre=embel_cost_pre+wash_cost_pre;

		var fab_cons_kg=0; var fab_cons_mtr=0; var fab_cons_yds=0; var fab_amount=0; var sp_oparation_amount=0; var wash_amount=0; var acc_amount=0; var fright_amount=0; var lab_amount=0; var misce_amount=0; var other_amount=0; var comm_amount=0; var fob_amount=0; var cm_amount=0; var commercial_cost=0; var rmg_ratio=0; var is_approved=0;
		var costing_id=$('#txt_quotation_id').val()*1;
		var cost_control_source_data=return_global_ajax_value( company_id, 'cost_control_source', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(cost_control_source_data)==1 || trim(cost_control_source_data)==7)
		{
			if(costing_id!=0)
			{
				var str_data=return_global_ajax_value( costing_id, 'budgete_cost_validate', '', 'requires/pre_cost_entry_controller_v2');
				var short_quatation_val=return_global_ajax_value( company_id, 'short_quatation_validate', '', 'requires/pre_cost_entry_controller_v2');
				var spdata=str_data.split("##");
				var fab_cons_kg=trim(spdata[0]); var fab_cons_mtr=trim(spdata[1]); var fab_cons_yds=trim(spdata[2]); var fab_amount=trim(spdata[3]); var sp_oparation_amount=trim(spdata[4]); var wash_amount=trim(spdata[5]); var acc_amount=trim(spdata[6]); var fright_amount=trim(spdata[7]); var lab_amount=trim(spdata[8]); var misce_amount=trim(spdata[9]); var other_amount=trim(spdata[10]); var comm_amount=trim(spdata[11]); var fob_amount=trim(spdata[12]); var cm_amount=trim(spdata[13]); var commercial_cost=trim(spdata[14]); var rmg_ratio=trim(spdata[15]); var is_approved=1;//trim(spdata[15]);
				//alert(fab_cost_pre+'_'+fab_amount);
				var msg=""; var msg_type=0;
				if(is_approved==1)
				{
					if(short_quatation_val==1){
						if(type==2)
						{
							if(fab_cost_pre>fab_amount)
							{
								msg_type=1;
								var fab_cost_dif=fab_cost_pre-fab_amount;
								msg+="\nBOM Limit. Yarn Cost:="+ fab_amount;
								msg+="\nYarn Cost Over form Qc:="+  number_format(fab_cost_dif,4,'.','' );
							}

							/*var row_num=$('#tbl_fabric_cost tr').length-1;
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
							}*/
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
						if(type==4)
						{
							if(embel_cost_pre>sp_oparation_amount)
							{
								msg_type=1;
								var sp_oparation_dif=embel_cost_pre-sp_oparation_amount;
								msg+="\nBOM Limit. Embel. Cost Cost:="+ sp_oparation_amount;
								msg+="\nEmbel. Cost Cost Over form Qc:="+ number_format(sp_oparation_dif,4,'.','');
							}
						}
						if(type==5)
						{
							if(wash_cost_pre>wash_amount)
							{
								msg_type=1;
								var wash_dif=wash_cost_pre-wash_amount;
								msg+="\nBOM Limit. Gmts.Wash Cost:="+ wash_amount;
								msg+="\nGmts.Wash Cost Over form Qc:="+ number_format(wash_dif,4,'.','');
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

						if(type==1)
						{
							if(cm_cost_pre>cm_amount)
							{
								msg_type=1;
								var cm_cost_dif=cm_cost_pre-cm_amount;
								msg+="\nBOM Limit. CM Cost:="+ cm_amount;
								msg+="\nCM Cost Over form Qc:="+ number_format(cm_cost_dif,4,'.','' );
							}
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
		
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==50) $("#report_btn_1").show();
			if(report_id[k]==51) $("#report_btn_2").show();
			if(report_id[k]==63) $("#report_btn_3").show();
			if(report_id[k]==170) $("#report_btn_4").show();
			if(report_id[k]==171) $("#report_btn_5").show();
			if(report_id[k]==197) $("#report_btn_6").show();
			if(report_id[k]==211) $("#report_btn_7").show();
		}
	}

	function ResetForm(){
		reset_form('precosting_1*quotationdtls_2','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2*is_click_cons_box,1*txt_incoterm_place,Chittagong*cbo_ready_to_approved,2*txt_costing_date,<? echo date("d-m-Y"); ?>','')
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

	function set_currier_cost_method_variable(company){

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
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		var data=txt_job_no+"_"+txt_un_appv_request;
		var title = 'Un Approval Request';
		var page_link = 'requires/pre_cost_entry_controller_v2.php?data='+data+'&action=unapp_request_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function fnc_copy_rate(value,i)
	{
		var colorName=$("#color_"+i).val();
		var count_id=$("#cbocount_"+i).val();
		var compo_id=$("#cbocompone_"+i).val();
		var rowCount = $('#tbl_yarn_cost tbody tr').length;
		var rate_copy=$('input[name="rate_copy"]:checked').val()*1;

		for(var j=i; j<=rowCount; j++)
		{
			if(rate_copy==1)
			{
				$("#txtrateyarn_"+j).val( value );
			}
			else if(rate_copy==2)
			{
				if( colorName==$("#color_"+j).val() ) $("#txtrateyarn_"+j).val( value );
			}
			else if(rate_copy==3)
			{
				if( count_id==$("#cbocount_"+j).val() ) $("#txtrateyarn_"+j).val( value );
			}
			else if(rate_copy==4)
			{
				if( compo_id==$("#cbocompone_"+j).val() )$("#txtrateyarn_"+j).val( value );
			}
			calculate_yarn_consumption_ratio( j, 'calculate_amount');
		}
	}
	
	//Trims Template 
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
		/*var page_link='requires/pre_cost_entry_controller_v2.php?action=trims_cost_template_name_popup&company=' + document.getElementById('cbo_company_name').value + '&update_id=' + document.getElementById('update_id').value;*/
		var row_count=$('#tbl_trim_cost tr').length;
		if(row_count == 0){
			$('#txt_trim_pre_cost').click();
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
				// alert(select_template_data);
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
				document.getElementById('txtconsdzngmts_1').value=exdata[13];
				$('#txtconsdzngmts_1').attr('tmp_cons',exdata[4]);
				document.getElementById('txttrimrate_1').value=exdata[5];
				document.getElementById('txttrimamount_1').value=exdata[6];
				document.getElementById('cboapbrequired_1').value=exdata[7];
				document.getElementById('updateTempleteTrimTd_1').value=exdata[11];
				document.getElementById('txtexper_1').value=exdata[12];
	
			}
			else if(row_count == 1 && document.getElementById('cbogroup_1').value == 42)
			{
				document.getElementById('cbogroup_1').value=exdata[2];
				document.getElementById('cbogrouptext_1').value=exdata[0];
				document.getElementById('txtdescription_1').value=exdata[10];
				document.getElementById('txtsupref_1').value=exdata[9];
				document.getElementById('cbonominasupplier_1').value=exdata[8];
				document.getElementById('cboconsuom_1').value=exdata[3];
				document.getElementById('txtconsdzngmts_1').value='';
				document.getElementById('txttrimrate_1').value='';
				document.getElementById('txttrimamount_1').value='';
				document.getElementById('cboapbrequired_1').value=exdata[7];
				document.getElementById('updateTempleteTrimTd_1').value=exdata[11];
				document.getElementById('txtexper_1').value=exdata[12];
	
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
				document.getElementById('txtconsdzngmts_'+row_count).value=exdata[13];
				$('#txtconsdzngmts_'+row_count).attr('tmp_cons',exdata[4]);
				document.getElementById('txtconsdzngmts_'+row_count).value=exdata[13];
				document.getElementById('txttrimrate_'+row_count).value=exdata[5];
				document.getElementById('txttrimamount_'+row_count).value=exdata[6];
				document.getElementById('cboapbrequired_'+row_count).value=exdata[7];
				document.getElementById('updateTempleteTrimTd_'+row_count).value=exdata[11];
				document.getElementById('txtexper_'+row_count).value=exdata[12];
			}
			
			a++;
	
		}
	}
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete('pre_cost_mst')" >
    <div style="width:100%;" align="center">
    <?=load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:1200px;">
        <legend>Pre-Costing</legend>
        <form name="precosting_1" id="precosting_1" autocomplete="off" enctype="multipart/form-data">
            <div style="width:1200px;">
            <table  width="1200" cellspacing="2" cellpadding=""  border="0">
                <tr>
                    <td width="110" class="must_entry_caption">Job No</td>
                    <td width="130"><input  style="width:110px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/pre_cost_entry_controller_v2.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />
                    	<input type="hidden" name="hidd_job_id" id="hidd_job_id"/>
                    	<input type="hidden" id="txt_precost_id" name="txt_precost_id"/>
                    </td>
                    <td width="90">Company</td>
                    <td width="130"><? echo create_drop_down("cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pre_cost_entry_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td' );  load_drop_down( 'requires/pre_cost_entry_controller_v2', this.value, 'load_drop_down_agent', 'agent_td' );is_manula_approved(this.value);set_currier_cost_method_variable(this.value);"); ?></td>
                    <td width="110">Quot. ID <input type="text" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:50px;" readonly placeholder="Quot. ID" /></td>
                    <td width="130"><input type="text" id="txt_quotation_style" name="txt_quotation_style" class="text_boxes" style="width:110px;" readonly placeholder="Quot. Style" /></td>
                    <td width="90">Style Ref</td>
                    <td width="130"><input class="text_boxes" type="text" style="width:110px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Max 75 Character" readonly/></td>
                    <td width="110">Buyer</td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                </tr>
                <tr>
                    <td>Style Desc.</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_style_desc" id="txt_style_desc" maxlength="100" title="Maximum 100 Character" readonly/></td>
                    <td>Job Qty.</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_offer_qnty" id="txt_offer_qnty"/></td>
                    <td>Prod. Dept.</td>
                    <td><? echo create_drop_down( "cbo_pord_dept", 80, $product_dept,"", 1, "-- Select --",0, "",1,"" ); ?>
                        <input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code"  disabled />
                    </td>
                    <td>Currency</td>
                    <td><? echo create_drop_down( "cbo_currercy", 50, $currency,"", 0, "", 2, "" ,1,""); ?>
                        ER. <input class="text_boxes_numeric" type="text" style="width:30px;" name="txt_exchange_rate" id="txt_exchange_rate" readonly/>
                    </td>
                    <td>Order UOM </td>
                    <td><? echo create_drop_down("cbo_order_uom",60, $unit_of_measurement, "",0, "",1, "change_caption_cost_dtls(this.value, 'change_caption_pcs' )",1,"1,58" ); ?>
                        <input type="button" id="set_button" class="image_uploader" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                        <input type="hidden" id="set_breck_down" />
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="tot_set_qnty" />
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Costing Date</td>
                    <td><input class="datepicker" type="text" style="width:110px;" name="txt_costing_date" id="txt_costing_date" onChange="calculate_confirm_price_dzn(); check_exchange_rate();" value="<?=date('d-m-Y'); ?>"/></td>
                    <td class="must_entry_caption">Costing Per</td>
                    <td><? echo create_drop_down( "cbo_costing_per", 120, $costing_per, "",0, "0", 1, "change_cost_per(this.value)","","" ); ?></td>
                    <td class="must_entry_caption">Incoterm</td>
                    <td><? echo create_drop_down( "cbo_inco_term", 120, $incoterm,"", 0, "",1,"" );?></td>
                    <td class="must_entry_caption">Incoterm Place</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_incoterm_place" id="txt_incoterm_place" maxlength="100" title="Maximum 100 Character" value="Chittagong"/></td>
                    <td>Agent</td>
                    <td id="agent_td"><? echo create_drop_down( "cbo_agent", 120, $blank_array,"", 1, "-- Select Agent --", $selected, "",1,"" ); ?></td>
                </tr>
                <tr>
                    <td>Region</td>
                    <td><? echo create_drop_down( "cbo_region", 120, $region,"", 1, "-- Select Region --",0,"",1,"" ); ?></td>
                    
                    <td class="must_entry_caption">SMV</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_sew_smv" id="txt_sew_smv" onChange="calculate_cm_cost_with_method()" disabled /></td>
                    <td class="must_entry_caption">Efficiency %</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_sew_efficiency_per" id="txt_sew_efficiency_per" onChange="calculate_cm_cost_with_method()" />
                    	<input class="text_boxes_numeric" type="hidden" style="width:80px;" name="txt_sew_efficiency_source" id="txt_sew_efficiency_source"/>
                    </td>
                    <td>File no</td>
                    <td><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:110px" ></td>
                    <td>Internal Ref</td>
                    <td><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:110px" ></td>
                </tr>
               <tr>
               		<td>Copy From</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_copy_form" id="txt_copy_form" disabled/></td>
                    <td>Budget Minute</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_budget_minute" id="txt_budget_minute" /></td>
                   	<td>Gauge</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_gauge " id="txt_gauge" disabled/></td>
               		<td>Approved</td>
                    <td><? echo create_drop_down( "cbo_approved_status", 120, $yes_no,"", 0, "", 2, "",1,"" ); ?></td>
                    <td>Un-approve request</td>
                    <td>
                        <Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click" ID="txt_un_appv_request" style="width:110px;" onClick="openmypage_unapprove_request();">
                    </td>
                </tr>
                <tr>
                    <td>Ready To Approved</td>
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 120, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    <td>Remarks</td>
                    <td colspan="5">
                    <input class="text_boxes" type="text" style="width:575px;" name="txt_remarks" id="txt_remarks" maxlength="500" title="Maximum 500 Character" placeholder="Remarks"/></td>
                    <td><input type="button" class="image_uploader" style="width:110px" value="Copy Without Cons." onClick="show_sub_form(document.getElementById('update_id').value,'partial_pre_cost_copy_action','');" />
                    <td><input type="button" class="image_uploader" style="width:120px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1,2)" /></td>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="10" valign="top" id="check_sms" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="10" valign="top" id="app_sms" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" class="button_container" colspan="10">
                        <input type="hidden" id="update_id" value="" />
                        <input type="hidden" id="copy_quatation_id" value="" />
                        <input type="hidden" id="budget_exceeds_quot_id" value="" />
                        <input type="hidden" id="txt_cost_control_source" value="" />
                        <input type="hidden" id="pre_cost_id" value="" />
                        <input type="hidden" id="cm_cost_predefined_method_id" value="" width="50" />
                        <input type="hidden" id="check_input" name="check_input" value="" width="50" />
                        <input type="hidden" id="is_click_cons_box" name="is_click_cons_box" value="1" width="50" />
                        <input type="hidden" id="cost_per_minute" name="cost_per_minute" value="" width="50" />
                        <input type="hidden" id="txt_deffd_lc_cost_percent" name="txt_deffd_lc_cost_percent" value="" width="50" />

                        <input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_cut_smv" id="txt_cut_smv" onChange="calculate_cm_cost_with_method()" />
                    <input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_cut_efficiency_per" id="txt_cut_efficiency_per" onChange="calculate_cm_cost_with_method()"  />
                     <input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_prod_line_hr" id="txt_prod_line_hr" maxlength="100" title="Maximum 100 Character"/>
                    <input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_machine_line" id="txt_machine_line" maxlength="100" title="Maximum 100 Character"/>
                    <input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_efficiency_wastage" id="txt_efficiency_wastage" onChange="calculate_cm_cost_with_method()" readonly />
                        <? //$dd="disable_enable_fields( 'txt_costing_date*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*copy_quatation_id*txt_remarks*save1*update1*Delete1*txt_lab_test_pre_cost*txt_inspection_pre_cost*txt_cm_pre_cost*txt_freight_pre_cost*txt_common_oh_pre_cost*txt_1st_quoted_price_pre_cost*txt_first_quoted_price_date*txt_revised_price_pre_cost*txt_revised_price_date*txt_confirm_price_pre_cost*txt_confirm_date_pre_cost*save2*update2*Delete2', 0 )";
                        echo load_submit_buttons( $permission, "fnc_precosting_entry", 0,0 ,"ResetForm()",1,1) ; ?>
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
                            <td>Yarn Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_fabric_pre_cost" id="txt_fabric_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_fabric_cost_listview','');" onChange="calculate_main_total()" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_fabric_po_price" id="txt_fabric_po_price" style="width:32px;" onChange="calculate_main_total()" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Trims Cost &nbsp <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;" onClick="openmypage_template_name('Template Search')"
                        >...</span></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_trim_pre_cost" id="txt_trim_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value);" onChange="calculate_main_total()" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_trim_po_price" id="txt_trim_po_price" style="width:32px;" onChange="calculate_main_total()" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Embel. Cost</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_embel_pre_cost" id="txt_embel_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_embellishment_cost_listview','');" onChange="calculate_main_total()"  readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_embel_po_price" id="txt_embel_po_price" style="width:32px;" onChange="calculate_main_total()" disabled="" /></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Gmts.Wash</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_wash_pre_cost" id="txt_wash_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_wash_cost_listview','');" onChange="calculate_main_total()" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_wash_po_price" id="txt_wash_po_price" style="width:32px;" onChange="calculate_main_total()" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Comml. Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_comml_pre_cost" id="txt_comml_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_comarcial_cost_listview','');" onChange="calculate_main_total()" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_comml_po_price" id="txt_comml_po_price" style="width:32px;" onChange="calculate_main_total()" disabled="" /></td>
                        </tr>
                        <tr>
                            <td>Lab Test</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_lab_test_pre_cost" id="txt_lab_test_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_lab_test_po_price" id="txt_lab_test_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Inspection </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_inspection_pre_cost" id="txt_inspection_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_inspection_po_price" id="txt_inspection_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Freight</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_freight_pre_cost" id="txt_freight_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_freight_po_price" id="txt_freight_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Courier Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_currier_pre_cost" id="txt_currier_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_currier_po_price" id="txt_currier_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Certificate Cost </td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_certificate_pre_cost" id="txt_certificate_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_certificate_po_price" id="txt_certificate_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td title="Deferred LC / Document Charges">Deffd. LC/DC</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_deffdlc_pre_cost" id="txt_deffdlc_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_deffdlc_po_price" id="txt_deffdlc_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Design Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_design_pre_cost" id="txt_design_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_design_po_price" id="txt_design_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Studio/Others</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_studio_pre_cost" id="txt_studio_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_studio_po_price" id="txt_studio_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Opert. Exp.</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_common_oh_pre_cost" id="txt_common_oh_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_common_oh_po_price" id="txt_common_oh_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>CM Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_cm_pre_cost" id="txt_cm_pre_cost" style="width:60px;" onChange="calculate_main_total()" onClick=" show_sub_form(document.getElementById('update_id').value,'show_cm_cost_listview','');" /></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_cm_po_price" id="txt_cm_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td bgcolor="#CCFF99">Interest</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_interest_pre_cost" id="txt_interest_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_interest_po_price" id="txt_interest_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Income Tax</td>
                            <td>
                            <input class="text_boxes_numeric" type="text" name="txt_incometax_pre_cost" id="txt_incometax_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_incometax_po_price" id="txt_incometax_po_price" style="width:32px;" onChange="calculate_main_total()" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Depc. & Amort.</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_depr_amor_pre_cost" id="txt_depr_amor_pre_cost" style="width:60px;" onChange="calculate_main_total()"/></td>
                            <td><input class="text_boxes_numeric" type="text"  name="txt_depr_amor_po_price" id="txt_depr_amor_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <td>Commission</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_commission_pre_cost" id="txt_commission_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_commission_cost_listview','');" onChange="calculate_main_total()" readonly/></td>
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
                        <tr>
                            <td id="final_cost_td_pcs_set">Final Cost/Pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost_psc_set" id="txt_total_pre_cost_psc_set" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost_psc_set_po_price" id="txt_total_pre_cost_psc_set_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <!--<tr>
                        <td>
                        1st Quoted Price
                        </td>
                        <td align="center">
                        <input class="text_boxes_numeric" type="text" name="txt_1st_quoted_price_pre_cost" id="txt_1st_quoted_price_pre_cost" style="width:80px;"/>
                        </td>
                        <td align="center">
                        <input class="datepicker" type="text" name="txt_first_quoted_price_date" id="txt_first_quoted_price_date" style="width:80px;"/>
                        </td>
                        </tr>
                        <tr>
                        <td>
                        Revised Price
                        </td>
                        <td align="center">
                        <input class="text_boxes_numeric" type="text" name="txt_revised_price_pre_cost" id="txt_revised_price_pre_cost" style="width:80px;"/>
                        </td>
                        <td align="center">
                        <input class="datepicker" type="text" name="txt_revised_price_date" id="txt_revised_price_date" style="width:80px;"/>

                        </td>
                        </tr>
                        <tr>
                        <td id="confirm_price_td_set_pcs">
                        Confirm Price/Pcs
                        </td>
                        <td align="center">
                        <input class="text_boxes_numeric" type="text" name="txt_confirm_price_pre_cost" id="txt_confirm_price_pre_cost" style="width:80px;" onChange="calculate_confirm_price_dzn()"/>
                        </td>
                        <td align="center">
                        <input class="text_boxes_numeric" type="text" name="txt_confirm_price_set_pcs_rate" id="txt_confirm_price_set_pcs_rate" style="width:80px;" />
                        </td>
                        </tr>
                        <tr>
                        <td id="confirm_price_td_dzn">
                        Confirm Price/Dzn
                        </td>
                        <td align="center">
                        <input class="text_boxes_numeric" type="text" name="txt_confirm_price_pre_cost_dzn" id="txt_confirm_price_pre_cost_dzn" style="width:80px;" readonly/>
                        </td>
                        <td align="center">
                        <input class="text_boxes_numeric" type="text" name="txt_confirm_price_po_price_dzn" id="txt_confirm_price_po_price_dzn" style="width:80px;" readonly />
                        </td>
                        </tr>-->

                        <tr>
                            <td id="margin_pcs_td">Margin/pcs</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_pre_cost" id="txt_margin_pcs_pre_cost" style="width:60px;" readonly/></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_margin_pcs_po_price" id="txt_margin_pcs_po_price" style="width:32px;" disabled=""/></td>
                        </tr>
                        <tr>
                            <!--<td>
                            Confirm Date
                            </td>
                            <td align="center">
                            <input class="datepicker" type="text" name="txt_confirm_date_pre_cost" id="txt_confirm_date_pre_cost" style="width:80px;"/>
                            </td>-->
                            <td><input type="hidden" id="update_id_dtls" name="update_id_dtls" readonly/></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3" valign="middle" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_quotation_entry_dtls", 0,0 ,"reset_form('quotationdtls_2','','')",2) ; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">Select PO: <span id="po_td"><? echo create_drop_down( "txt_po_breack_down_id", 190,$blank_array, "", 1, "-- Select PO --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <input type="button" id="report_btn_1" class="formbutton" value="Cost Rpt" onClick="generate_report('preCostRpt');" style="display:none;" />
                                <input type="button" id="report_btn_2" class="formbutton" value="Cost Rpt2" onClick="generate_report('preCostRpt2');" style="display:none;"/>
                                <input type="button" id="report_btn_3" class="formbutton" value="BOM Rpt 2" onClick="generate_report('bomRpt2');" style="display:none;" />
                                <input type="button" id="report_btn_4" class="formbutton" value="Cost Rpt3" onClick="generate_report('preCostRpt3');" style="display:none;" />
                                <input type="button" id="report_btn_5" class="formbutton" value="Cost Rpt4" onClick="generate_report('preCostRpt4');" style="display:none;" />
								<input type="button" id="report_btn_6" class="formbutton" value="BOM Rpt 3" onClick="generate_report('bomRpt3');" style="display:none;" />
								<input type="button" id="report_btn_7" class="formbutton" value="MO Sheet" onClick="generate_report('mo_sheet');" style="display:none;"/>
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
