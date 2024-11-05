<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Garments Price Quotation Entry Entry
Functionality	:
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	15-07-2021
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
//========== user credential start ========
$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id";
$company_id = $userCredential[0][csf('company_id')];
//$store_location_id = $userCredential[0][csf('store_location_id')];
//$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];
$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
	//echo $location_id.'DDD';
}
//-------------------------------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Info","../../", 1, 1, $unicode,'','');
?>
<script>

	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][314] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	var str_construction = [<? echo substr(return_library_autocomplete( "select construction from wo_pri_quo_fabric_cost_dtls group by construction ", "construction"), 0, -1); ?> ];
	var str_composition = [<? echo substr(return_library_autocomplete( "select composition from wo_pri_quo_fabric_cost_dtls group by composition", "composition"), 0, -1); ?>];
	var str_incoterm_place = [<? echo substr(return_library_autocomplete( "select incoterm_place from  wo_price_quotation group by incoterm_place", "incoterm_place"), 0, -1); ?>];
	var str_factory = [<? echo substr(return_library_autocomplete( "select factory from  wo_price_quotation group by factory", "factory"), 0, -1); ?>];
	// Common For All----------------------------------------------------
	function is_manual_approved(compony_id){
		var response=return_global_ajax_value( compony_id, 'is_manual_approved', '', 'requires/quotation_entry_controller_v2');
		if(trim(response)==1){
			$('#approve1').hide();
		}
		if(trim(response)==2 || trim(response)==0){
			$('#approve1').show();
		}
	}
	function set_auto_complete(type){
		if(type=='price_quation_mst'){
				$("#txt_incoterm_place").autocomplete({
					source: str_incoterm_place
				});
				$("#txt_factory").autocomplete({
					source:  str_factory
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
	}

	function show_sub_form(update_id, action, extra_str)
	{
		if(update_id==""){
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
				$(this).html('Quotation id is Empty').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
		}
		else
		{
			var fab_cost=$("#txt_fabric_pre_cost").val()*1;		var pre_fab_cost=$("#txt_fabric_pre_cost").attr('pre_fab_cost')*1;
			var trim_cost=$("#txt_trim_pre_cost").val()*1;		var pre_trim_cost=$("#txt_trim_pre_cost").attr('pre_trim_cost')*1;
			var embl_cost=$("#txt_embel_pre_cost").val()*1;		var pre_embl_cost=$("#txt_embel_pre_cost").attr('pre_embl_cost')*1;
			var wash_cost=$("#txt_wash_pre_cost").val()*1;		var pre_wash_cost=$("#txt_wash_pre_cost").attr('pre_wash_cost')*1;
			var comml_cost=$("#txt_comml_pre_cost").val()*1;	var pre_comml_cost=$("#txt_comml_pre_cost").attr('pre_comml_cost')*1;
			var comms_cost=$("#txt_commission_pre_cost").val()*1;var pre_comms_cost=$("#txt_commission_pre_cost").attr('pre_commis_cost')*1;

			if((fab_cost!=pre_fab_cost) && action!="show_fabric_cost_listview")
			{
				alert("Fabric Cost Change Found, Please Save or Update.");
				release_freezing();
				return;
			}
			if((trim_cost!=pre_trim_cost) && action!="show_trim_cost_listview")
			{
				alert("Trims Cost Change Found, Please Save or Update.");
				release_freezing();
				return;
			}
			if((embl_cost!=pre_embl_cost) && action!="show_embellishment_cost_listview")
			{
				alert("Embel. Cost Change Found, Please Save or Update.");
				release_freezing();
				return;
			}
			if((wash_cost!=pre_wash_cost) && action!="show_wash_cost_listview")
			{
				alert("Wash Cost Change Found, Please Save or Update.");
				release_freezing();
				return;
			}
			if((comml_cost!=pre_comml_cost) && action!="show_commission_cost_listview")
			{
				alert("Comml. Cost Change Found, Please Save or Update.");
				release_freezing();
				return;
			}
			if((comms_cost!=pre_comms_cost) && action!="show_commission_cost_listview")
			{
				alert("Commission Cost Change Found, Please Save or Update.");
				release_freezing();
				return;
			}

			if(action=="show_fabric_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('cbo_company_name').value,action,'cost_container','../woven_order/requires/quotation_entry_controller_v2','');
				sum_yarn_required();
				var approved_status=document.getElementById('cbo_approved_status').value;
				if(approved_status==3){
					approved_status=1;
				}
				if(approved_status==1){
					document.getElementById('save3').disabled=true;
					document.getElementById('update3').disabled=true;
					document.getElementById('Delete3').disabled=true;
					document.getElementById('save4').disabled=true;
					document.getElementById('update4').disabled=true;
					document.getElementById('Delete4').disabled=true;
					document.getElementById('save5').disabled=true;
					document.getElementById('update5').disabled=true;
					document.getElementById('Delete5').disabled=true;
				}
				else{
					document.getElementById('save3').disabled=false;
					document.getElementById('update3').disabled=false;
					document.getElementById('Delete3').disabled=false;
					document.getElementById('save4').disabled=false;
					document.getElementById('update4').disabled=false;
					document.getElementById('Delete4').disabled=false;
					document.getElementById('save5').disabled=false;
					document.getElementById('update5').disabled=false;
					document.getElementById('Delete5').disabled=false;
				}
				set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
				//yarn
				set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
				set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
				set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
				//yarn end
				// conversion
				set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
				set_auto_complete('tbl_fabric_cost');
				//calculate_cofirm_price_commision()
				//$('body').scrollTo('#cost_container');
			}
			if(action=="show_trim_cost_listview"){
				show_list_view(update_id+'*'+extra_str+'*'+document.getElementById('cbo_company_name').value,action,'cost_container','../woven_order/requires/quotation_entry_controller_v2','');
				set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
				set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
				set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
				//calculate_cofirm_price_commision()
			}
			if(action=="show_embellishment_cost_listview"){
				show_list_view(update_id,action,'cost_container','../woven_order/requires/quotation_entry_controller_v2','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
				//calculate_cofirm_price_commision()
			}
			if(action=="show_wash_cost_listview"){
				show_list_view(update_id+'_'+document.getElementById('cbo_company_name').value,action,'cost_container','../woven_order/requires/quotation_entry_controller_v2','');
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
				//calculate_cofirm_price_commision()
			}

			if(action=="show_commission_cost_listview"){
				show_list_view(update_id,action,'cost_container','../woven_order/requires/quotation_entry_controller_v2','');
				set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
				set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
				//calculate_cofirm_price_commision()
			}
			if(action=="show_comarcial_cost_listview"){
				show_list_view(update_id,action,'cost_container','../woven_order/requires/quotation_entry_controller_v2','');
				set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
				set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
				//calculate_cofirm_price_commision()
			}
			$(window).scrollTop($(document).height());
		}
		$('#hidd_is_dtls_open').val(2);
	}

	function set_sum_value(des_fil_id,field_id,table_id)
	{
		//alert(table_id)
		if(table_id=='tbl_fabric_cost'){
			var rowCount = $('#tbl_fabric_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_fabric_pre_cost').value=number_format_common((document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1), 1, 0, document.getElementById('cbo_currercy').value);
			calculate_main_total();
		}
		if(table_id=='tbl_yarn_cost'){
			var rowCount = $('#tbl_yarn_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(((document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1), 1, 0, document.getElementById('cbo_currercy').value);
			calculate_main_total();
		}
		if(table_id=='tbl_conversion_cost'){
			var rowCount = $('#tbl_conversion_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(((document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1), 1, 0, document.getElementById('cbo_currercy').value);
			calculate_main_total();
		}
		if(table_id=='tbl_trim_cost'){
			var rowCount = $('#tbl_trim_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_trim_pre_cost').value=document.getElementById('txttrimamount_sum').value;
			calculate_main_total()
		}
		if(table_id=='tbl_embellishment_cost'){
			var rowCount = $('#tbl_embellishment_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_embel_pre_cost').value=document.getElementById('txtamountemb_sum').value;
			calculate_main_total()
		}
		if(table_id=='tbl_wash_cost'){
			var rowCount = $('#tbl_wash_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_wash_pre_cost').value=document.getElementById('txtamountemb_sum').value;
			calculate_main_total()
		}
		if(table_id=='tbl_commission_cost'){
			var rowCount = $('#tbl_commission_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_commission_pre_cost').value=document.getElementById('txtamountcommission_sum').value;
			calculate_main_total()
			calculate_price_with_commision_dzn()
		}
		if(table_id=='tbl_comarcial_cost'){
			var rowCount = $('#tbl_comarcial_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			document.getElementById('txt_comml_pre_cost').value=document.getElementById('txtamountcomarcial_sum').value;
			calculate_main_total()
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id)
	{
		var update_id=document.getElementById('update_id').value;
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}

		if(table_id=='tbl_fabric_cost'){
			if(rowNo!=1){
				//alert(1);
				var permission_array=permission.split("_");
				var updateid=$('#updateid_'+rowNo).val();

				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_fabric_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_fabric_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_fabric_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }
							});
							$("#tbl_fabric_cost tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
							$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
							$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
							$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");

							$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
							$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
							$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
							$('#txtconsumption_'+i).removeAttr("onClick").attr("onClick","open_consumption_popup( 'requires/quotation_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
							$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
							$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
							$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						})
					}
					sum_yarn_required();
					set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
					if(updateid !="" && permission_array[2]==1){
						var data_component=fnc_quotation_entry_component();
						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_fabric_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
		}

		if(table_id=='tbl_yarn_cost'){
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#updateidyarncost_'+rowNo).val();
				//alert(updateid);
				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_yarn_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_yarn_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_yarn_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }
							});
							$('#increaseyarn_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_yarn_cost("+i+");");
							$('#decreaseyarn_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_yarn_cost');");
							$('#percentone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_one');");
							$('#cbocompone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_one');");
							$('#consratio_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio('consratio_"+i+"','consqnty_"+i+"','txtrateyarn_"+i+"','txtamountyarn_"+i+"','calculate_consumption')");
							$('#consqnty_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio('consratio_"+i+"','consqnty_"+i+"','txtrateyarn_"+i+"','txtamountyarn_"+i+"','calculate_ratio')");
							$('#txtrateyarn_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio('consratio_"+i+"','consqnty_"+i+"','txtrateyarn_"+i+"','txtamountyarn_"+i+"','calculate_amount')");
						})
					}

					set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
					set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
					set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
					if(updateid !="" && permission_array[2]==1){
						var data_component=fnc_quotation_entry_component();
						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_yarn_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
			//calculate_cofirm_price_commision()
		}

		if(table_id=='tbl_conversion_cost'){
			var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/quotation_entry_controller_v2');
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#updateidcoversion_'+rowNo).val();

				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_conversion_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_conversion_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_conversion_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }
							});
							$('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
							$('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
							$('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+");");
							$('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost');");
							$('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
							$('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
							if(conversion_from_chart==1)
							{
								$('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit_pop_up("+i+")");
							}
						})
					}
					set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
					set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
					set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
					if(updateid !="" && permission_array[2]==1){
						var data_component=fnc_quotation_entry_component();
						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_conversion_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
			//calculate_cofirm_price_commision()
		}

		if(table_id=='tbl_trim_cost'){
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#updateidtrim_'+rowNo).val();
				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_trim_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_trim_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_trim_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }
							});
							$('#tbl_trim_cost tr:eq('+i+') td:eq(1)').attr('id','tdsupplier_'+i);
							if($('#seq_'+i).val()=="")
							{
								$('#seq_'+i).val( i );
							}
							$('#cbogroup_'+i).removeAttr("onChange").attr("onChange","set_trim_cons_uom( this.value,"+i+" )");
							$('#txtdescription_'+i).removeAttr("onDblClick").attr("onDblClick","trims_description_popup( "+i+" )");
							$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
							$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost');");
							$('#txtconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
							$('#txttrimrate_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
							$('#txttrimrate_'+i).removeAttr("onDblClick").attr("onDblClick","trim_rate_popup( "+i+" )");
						})
					}
					set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
					set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
					set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );

					if(updateid !="" && permission_array[2]==1){
						//alert($('#txt_trim_pre_cost').val())
						var data_component=fnc_quotation_entry_component();

						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_trim_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
		//calculate_cofirm_price_commision()
		}

		if(table_id=='tbl_embellishment_cost'){
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#embupdateid_'+rowNo).val();
				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_embellishment_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_embellishment_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_embellishment_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
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
					set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
					calculate_main_total();
					if(updateid !="" && permission_array[2]==1){
						var data_component=fnc_quotation_entry_component();
						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_embellishment_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
			//calculate_cofirm_price_commision()
		}

		if(table_id=='tbl_wash_cost'){
			var conversion_from_chart=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'conversion_from_chart', '', 'requires/quotation_entry_controller_v2');
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#embupdateid_'+rowNo).val();

				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_wash_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_wash_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_wash_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }
							});
							$('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_wash_cost("+i+");");
							$('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost');");
							$('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
							$('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
							if(conversion_from_chart==1){
								$('#txtembrate_'+i).removeAttr("onClick").attr("onClick","set_wash_charge_unit_pop_up( "+i+" )");
							}
							$('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
						})
					}

					set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
					calculate_main_total();

					if(updateid !="" && permission_array[2]==1){
						var data_component=fnc_quotation_entry_component();
						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_wash_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
			//calculate_cofirm_price_commision()
		}
		if(table_id=='tbl_commission_cost'){
			var numRow = $('table#tbl_commission_cost tbody tr').length;
			if(numRow==rowNo && rowNo!=1){
				$('#tbl_commission_cost tbody tr:last').remove();
			}
			set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
			set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
			calculate_main_total();
			//calculate_cofirm_price_commision()
		}
		if(table_id=='tbl_comarcial_cost'){
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#comarcialupdateid_'+rowNo).val();

				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					$("table#tbl_comarcial_cost tbody tr:eq("+index+")").remove()
					var numRow = $('table#tbl_comarcial_cost tbody tr').length;
					for(i = rowNo;i <= numRow;i++){
						$("#tbl_comarcial_cost tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								'value': function(_, value) { return value }
							});
							$('#increasecomarcial_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_comarcial_cost("+i+");");
							$('#decreasecomarcial_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_comarcial_cost');");
							$('#txtcomarcialrate_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'rate' )");
							$('#txtcomarcialamount_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'amount' )");
						})
					}
					set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
					set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
					if(updateid !="" && permission_array[2]==1){
						var data_component=fnc_quotation_entry_component();
						var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_comarcial_cost', '', 'requires/quotation_entry_controller_v2');
					}
				}
				else
				{
					return;
				}
			}
		}
			//calculate_cofirm_price_commision()
	}

	function show_hide_content(row, id){
		$('#content_'+row).toggle('slow', function() {
		});
	}

	function enable_disable(value,fld_arry, i){
		return;
		var fld_arry=fld_arry.split('*');
		if(value==2){
			for(var j=0;j<fld_arry.length;j++){
				document.getElementById(fld_arry[j]+i).disabled=false;
			}
		}
		else{
			for(var j=0;j<fld_arry.length;j++){
				document.getElementById(fld_arry[j]+i).disabled=true;
				document.getElementById(fld_arry[j]+i).value="";
				set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			}
		}
	}
	// Common For All End ----------------------------------------------------

	// Start Fabric cost------------------------------------------------------
	function add_break_down_tr(i){
		var row_num=$('#tbl_fabric_cost tr').length-1;
		if (i==0){
			i=1;
			$("#txtconstruction_"+i).autocomplete({
				source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
				source:  str_composition
			});
			return;
		}
		if (row_num!=i){
			return false;
		}
		else{
			i++;
			$("#tbl_fabric_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_fabric_cost");
			$("#tbl_fabric_cost tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
			$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
			$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
			$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
			$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtconsumption_'+i).removeAttr("onClick").attr("onClick","open_consumption_popup( 'requires/quotation_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
			$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
			$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			var j=i-1;
			$('#cbogmtsitem_'+i).val($('#cbogmtsitem_'+j).val());
			$('#cbofabricnature_'+i).val($('#cbofabricnature_'+j).val());
			$('#cbocolortype_'+i).val($('#cbocolortype_'+j).val());
			$('#cbofabricsource_'+i).val($('#cbofabricsource_'+j).val());
			$('#cbostatus_'+i).val($('#cbostatus_'+j).val());
			$('#txtconsumption_'+i).val("");
			$('#txtrate_'+i).val("");
			$('#txtamount_'+i).val("");
			$('#consbreckdown_'+i).val("");
			$('#msmntbreackdown_'+i).val("");
			$('#updateid_'+i).val("");
			$('#processlossmethod_'+i).val("");
			$('#txtfinishconsumption_'+i).val("");
			$('#txtavgprocessloss_'+i).val("");
			$('#txtbodypart_'+i).val("");
			$('#txtgsmweight_'+i).val("");

			$("#txtconstruction_"+i).autocomplete({
				source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
				source: str_composition
			});

			set_all_onclick();
			sum_yarn_required()
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			//calculate_cofirm_price_commision()
		}
	}

	function open_fabric_decription_popup(i){
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/quotation_entry_controller_v2.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function(){
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			var cbo_certification_pop=this.contentDoc.getElementById("cbo_certification_pop");
			var cbo_fabric_code_pop=this.contentDoc.getElementById("cbo_fabric_code_pop");
			var cbo_yarn_color_id=this.contentDoc.getElementById("cbo_yarn_color_id");
			var cbo_yarn_code=this.contentDoc.getElementById("cbo_yarn_code");
			document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
			document.getElementById('fabricdescription_'+i).value=fab_desctiption.value;
			document.getElementById('fabricdescription_'+i).title=fab_desctiption.value;
			document.getElementById('cbofabricnature_'+i).value=fab_nature_id.value;
			document.getElementById('txtgsmweight_'+i).value=fab_gsm.value;
			document.getElementById('yarnbreackdown_'+i).value=yarn_desctiption.value;
			document.getElementById('txtconstruction_'+i).value=construction.value;
			document.getElementById('txtcomposition_'+i).value=composition.value;
			document.getElementById('cbocertification_'+i).value=cbo_certification_pop.value;
			document.getElementById('cbofabriccode_'+i).value=cbo_fabric_code_pop.value;
			document.getElementById('cboyarncolor_'+i).value=cbo_yarn_color_id.value;
			document.getElementById('txtyarncode_'+i).value=cbo_yarn_code.value;
		}
	}

	function open_consumption_popup(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc){
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var update_id=document.getElementById('update_id').value;

		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var hid_fab_cons_in_quotation_variable =document.getElementById('consumptionbasis_'+trorder).value;
		var body_part_id =document.getElementById(body_part_id).value;
		var txtgsmweight=document.getElementById(txtgsmweight_id).value;
		var cbofabricnature_id =document.getElementById(cbofabricnature_id).value;
		var cons_breck_downn=document.getElementById('consbreckdown_'+trorder).value;
		var msmnt_breack_downn=document.getElementById('msmntbreackdown_'+trorder).value;
		var marker_breack_down=document.getElementById('markerbreackdown_'+trorder).value;
		var calculated_conss=document.getElementById('txtconsumption_'+trorder).value;
		var garments_nature = document.getElementById('garments_nature').value;
		var updateid_fc = document.getElementById('updateid_'+trorder).value;
		var cbogmtsitem = document.getElementById('cbogmtsitem_'+trorder).value;
		document.getElementById('tr_ortder').value=trorder;
		if(body_part_id==0 ){
			alert("Select Body Part Id")
		}
		else if(cbofabricnature_id==0 ){
			alert("Select Fabric Nature Id");
		}
		else{
			var page_link=page_link+'&body_part_id='+body_part_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_company_id='+cbo_company_id+'&cbofabricnature_id='+cbofabricnature_id+'&cons_breck_downn='+cons_breck_downn+'&msmnt_breack_downn='+msmnt_breack_downn+'&calculated_conss='+calculated_conss+'&hid_fab_cons_in_quotation_variable='+hid_fab_cons_in_quotation_variable+'&txtgsmweight='+txtgsmweight+'&garments_nature='+garments_nature+'&marker_breack_down='+marker_breack_down+'&updateid_fc='+updateid_fc+'&cbogmtsitem='+cbogmtsitem+'&update_id='+update_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){
				var trorder= document.getElementById('tr_ortder').value;
				var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
				var msmnt_breack_down=this.contentDoc.getElementById("msmnt_breack_down");
				var marker_breack_down=this.contentDoc.getElementById("marker_breack_down");
				var calculated_cons=this.contentDoc.getElementById("calculated_cons");
				var finish_avg_cons=this.contentDoc.getElementById("avg_cons");
				var avg_process_loss=this.contentDoc.getElementById("calculated_procloss");
				var process_loss_method_id=this.contentDoc.getElementById("process_loss_method_id");

				var calculated_amount=this.contentDoc.getElementById("calculated_amount");
				var calculated_rate=this.contentDoc.getElementById("calculated_rate");


				document.getElementById('txtconsumption_'+trorder).value=calculated_cons.value;
				document.getElementById('txtfinishconsumption_'+trorder).value=finish_avg_cons.value;
				document.getElementById('txtavgprocessloss_'+trorder).value=avg_process_loss.value;
				document.getElementById('processlossmethod_'+trorder).value=process_loss_method_id.value;
				document.getElementById('consbreckdown_'+trorder).value=cons_breck_down.value;
				document.getElementById('msmntbreackdown_'+trorder).value=msmnt_breack_down.value;
				document.getElementById('markerbreackdown_'+trorder).value=marker_breack_down.value;

				document.getElementById('txtrate_'+trorder).value=calculated_rate.value;
				document.getElementById('txtamount_'+trorder).value=calculated_amount.value;
				//math_operation( 'txtamount_'+trorder, 'txtconsumption_'+trorder+'*'+'txtrate_'+trorder, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
				sum_yarn_required()
				set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')
				update_related_data(1)
				//calculate_cofirm_price_commision()
			}
		}
	}

	function change_caption( value, td_id ){
		if(value==2){
			document.getElementById(td_id).innerHTML="GSM";
		}
		else{
			document.getElementById(td_id).innerHTML="Yarn Weight";
		}
	}

	function sum_yarn_required(){
		var row_num=$('#tbl_fabric_cost tr').length-1;
		var yarn_for_knit=0;
		var yarn_for_woven=0;
		var total_knit_fabric_required=0;
		var total_woven_fabric_required=0;
		for (var i=1; i<=row_num; i++){
			var cbofabricnature=document.getElementById('cbofabricnature_'+i).value
			var cbofabricsource=document.getElementById('cbofabricsource_'+i).value
			if(cbofabricnature==2 && cbofabricsource==1){
				yarn_for_knit=yarn_for_knit+(document.getElementById('txtconsumption_'+i).value)*1
			}
			if(cbofabricnature==3 && cbofabricsource==1){
				yarn_for_woven=yarn_for_woven+(document.getElementById('txtgsmweight_'+i).value)*1
			}
			if(cbofabricnature==2){
				total_knit_fabric_required=total_knit_fabric_required+(document.getElementById('txtconsumption_'+i).value)*1
			}
			if(cbofabricnature==3){
				total_woven_fabric_required=total_woven_fabric_required+(document.getElementById('txtconsumption_'+i).value)*1
			}
		}
		document.getElementById('tot_yarn_needed').value=number_format_common(yarn_for_woven+yarn_for_knit,5,0);
		document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(yarn_for_woven+yarn_for_knit,5,0);
		document.getElementById('txtwoven_sum').value=number_format_common(total_woven_fabric_required,5,0);
		document.getElementById('txtknit_sum').value=number_format_common(total_knit_fabric_required,5,0);
	}

	function fnc_fabric_cost_dtls( operation )
	{
		var update_id=document.getElementById('update_id').value;
		if(operation==2)//operation==1 ||
		{
			var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
			var ex_job_pre_cost_data=is_job_pre_cost.split("***");
			if(ex_job_pre_cost_data[0]==1)
			{
				var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
				alert(job_pre_cost_msg);
				return
			}
		}
		freeze_window(operation);
		/*if(operation==2){
			alert("Delete Restricted")
			release_freezing();
			return;
		}*/
		var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
		var row_num=$('#tbl_fabric_cost tr').length-1;
		var data_all="";
		var data_component=fnc_quotation_entry_component();
		//alert(data_component); return;
		for (var i=1; i<=row_num; i++){
			if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*txtconstruction_'+i+'*txtcomposition_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i,'Gmts Item *Body Part*Fabric Nature*Color Type*Construction*Composition*Consunption*Fabric Source')==false){
				release_freezing();
				return;
			}

			if ( $('#cbofabricnature_'+i).val()=='3' && $('#cbofabricsource_'+i).val()=='1' &&  (form_validation('txtgsmweight_'+i,'Yarn Weight')==false || $('#txtgsmweight_'+i).val()=='0') ){
				document.getElementById('txtgsmweight_'+i).focus();
				document.getElementById('txtgsmweight_'+i).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
				$(this).html('Please Fill up Yarn Weight field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
				release_freezing();
				return;
			}

			if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtrate_'+i,'Rate')==false || $('#txtrate_'+i).val()=='0')){
				document.getElementById('txtrate_'+i).focus();
				document.getElementById('txtrate_'+i).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
					$(this).html('Please Fill up Rate field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
				release_freezing();
				return;
			}

			if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtamount_'+i,'Amount')==false || $('#txtamount_'+i).val()=='0') ){
				document.getElementById('txtamount_'+i).focus();
				document.getElementById('txtamount_'+i).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){
					$(this).html('Please Fill up Amount field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
				release_freezing();
				return;
			}
			data_all=data_all+get_submitted_data_string('cbo_company_name*cbo_costing_per*consumptionbasis_'+i+'*update_id*cbogmtsitem_'+i+'*txtbodypart_'+i+'*txtbodyparttype_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*oldlibyarncountdeterminationid_'+i+'*txtconstruction_'+i+'*txtcomposition_'+i+'*fabricdescription_'+i+'*txtgsmweight_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtfinishconsumption_'+i+'*txtavgprocessloss_'+i+'*cbostatus_'+i+'*consbreckdown_'+i+'*msmntbreackdown_'+i+'*yarnbreackdown_'+i+'*updateid_'+i+'*cbowidthdiatype_'+i+'*markerbreackdown_'+i+'*processlossmethod_'+i+'*cbofabriccode_'+i+'*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtamount_sum',"../../");
		}
		var data="action=save_update_delet_fabric_cost_dtls&operation="+operation+'&total_row='+row_num+data_all+"&data_component="+data_component;//
		//alert(data); return;
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_cost_dtls_reponse;
	}

	function fnc_fabric_cost_dtls_reponse(){
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==10)
		{
			release_freezing();
			return;
		}
		if(reponse[0]=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}
		if(reponse[0]==15){
			setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
		}

		if(trim(reponse[0])=='approved'){
			alert("This Costing is Approved");
			release_freezing();
			return;
		}

		if(http.readyState == 4){
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
			show_hide_content('fabric_cost', '')
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				update_related_data(reponse[0])
				fnc_quotation_entry_dtls1( 1 )
				release_freezing();
			}
		}
	}

	function update_related_data(operation){
		$('#accordion_h_yarn').click();
		var row_num=$('#tbl_yarn_cost tr').length-1;
		for(var i=1; i<=row_num; i++){
			
			var yarn_update=document.getElementById('updateidyarncost_'+i).value;
			//if(yarn_update=="")
			//{
			calculate_yarn_consumption_ratio('consratio_'+i,'consqnty_'+i,'txtrateyarn_'+i,'txtamountyarn_'+i,'calculate_consumption')
			//}
		}

		$('#accordion_h_conversion').click();
		var row_num=$('#tbl_conversion_cost tr').length-1;
		for(var i=1; i<=row_num; i++){
			set_conversion_qnty(i)
		}
	}

	// End Fabric cost------------------------------------------------------
	// Start Yarn Cost------------------------------------------------------
	function add_break_down_tr_yarn_cost( i ){
		var row_num=$('#tbl_yarn_cost tr').length-1;
		if (i==0){
			i=1;
			$("#txtconstruction_"+i).autocomplete({
			source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
			source:  str_composition
			});
			return;
		}
		if (row_num!=i){
			return false;
		}
		else{
			i++;
			$("#tbl_yarn_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_yarn_cost");
			$('#increaseyarn_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_yarn_cost("+i+");");
			$('#decreaseyarn_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_yarn_cost');");
			$('#percentone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_one');");
			$('#cbocompone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_one');");
			$('#consratio_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio('consratio_"+i+"','consqnty_"+i+"','txtrateyarn_"+i+"','txtamountyarn_"+i+"','calculate_consumption')");
			$('#consqnty_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio('consratio_"+i+"','consqnty_"+i+"','txtrateyarn_"+i+"','txtamountyarn_"+i+"','calculate_ratio')");
			$('#txtrateyarn_'+i).removeAttr("onChange").attr("onChange","calculate_yarn_consumption_ratio('consratio_"+i+"','consqnty_"+i+"','txtrateyarn_"+i+"','txtamountyarn_"+i+"','calculate_amount')");
			$('#updateidyarncost_'+i).val("");
			set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
			set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
			set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
			//calculate_cofirm_price_commision()
		}
	}

	function control_composition(id,td,type){
		return;
		/*var cbocompone=(document.getElementById('cbocompone_'+id).value);
		var cbocomptwo=(document.getElementById('cbocomptwo_'+id).value);
		var percentone=(document.getElementById('percentone_'+id).value)*1;
		var percenttwo=(document.getElementById('percenttwo_'+id).value)*1;
		var row_num=$('#tbl_yarn_cost tr').length-1;
		if(type=='percent_one' && percentone>100){
			alert("Greater Than 100 Not Allwed")
			document.getElementById('percentone_'+id).value="";
		}
		if(type=='percent_one' && percentone<=0){
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('percentone_'+id).value="";
			document.getElementById('percentone_'+id).disabled=true;
			document.getElementById('cbocompone_'+id).value=0;
			document.getElementById('cbocompone_'+id).disabled=true;
			document.getElementById('percenttwo_'+id).value=100;
			document.getElementById('percenttwo_'+id).disabled=false;
			document.getElementById('cbocomptwo_'+id).disabled=false;
		}
		if(type=='percent_one' && percentone==100){
			document.getElementById('percenttwo_'+id).value="";
			document.getElementById('cbocomptwo_'+id).value=0;
			document.getElementById('percenttwo_'+id).disabled=true;
			document.getElementById('cbocomptwo_'+id).disabled=true;
		}
		if(type=='percent_one' && percentone < 100 && percentone > 0 ){
			document.getElementById('percenttwo_'+id).value=100-percentone;
			document.getElementById('percenttwo_'+id).disabled=false;
			document.getElementById('cbocomptwo_'+id).disabled=false;
			//document.getElementById('cbocomptwo_'+id).value=0;
		}

		if(type=='comp_one' && cbocompone==cbocomptwo  ){
			alert("Same Composition Not Allowed");
			document.getElementById('cbocompone_'+id).value=0;
			//document.getElementById('percenttwo_'+id).value=100-percentone;
			//document.getElementById('cbocomptwo_'+id).value=0;
		}
		if(type=='percent_two' && percenttwo>100){
			alert("Greater Than 100 Not Allwed")
			document.getElementById('percenttwo_'+id).value="";
			//document.getElementById('cbocompone_'+id).value=0;
		}
		if(type=='percent_two' && percenttwo<=0){
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('percenttwo_'+id).value="";
			document.getElementById('percenttwo_'+id).disabled=true;
			document.getElementById('cbocomptwo_'+id).value=0;
			document.getElementById('cbocomptwo_'+id).disabled=true;
			document.getElementById('percentone_'+id).value=100;
			document.getElementById('percentone_'+id).disabled=false;
			document.getElementById('cbocompone_'+id).disabled=false;
		}
		if(type=='percent_two' && percenttwo==100){
			document.getElementById('percentone_'+id).value="";
			document.getElementById('cbocompone_'+id).value=0;
			document.getElementById('percentone_'+id).disabled=true;
			document.getElementById('cbocompone_'+id).disabled=true;
		}
		if(type=='percent_two' && percenttwo<100 && percenttwo>0){
			document.getElementById('percentone_'+id).value=100-percenttwo;
			document.getElementById('percentone_'+id).disabled=false;
			document.getElementById('cbocompone_'+id).disabled=false;
			//document.getElementById('cbocompone_'+id).value=0;
		}
		if(type=='comp_two' && cbocomptwo==cbocompone){
			alert("Same Composition Not Allowed");
			document.getElementById('cbocomptwo_'+id).value=0;
			//document.getElementById('percentone_'+id).value=100-percenttwo;
			//document.getElementById('cbocompone_'+id).value=0;
		}*/
	}

	function calculate_yarn_consumption_ratio(consratio_id,consqnty_id,txtrateyarn_id,txtamountyarn_id,type){
		var tot_yarn_needed=document.getElementById('tot_yarn_needed').value*1
		if(type=='calculate_consumption'){ 
			var consratio=document.getElementById(consratio_id).value*1
			var consqnty=(tot_yarn_needed*consratio)/100;
		//	alert(consqnty+'=='+consratio);
			//document.getElementById(consqnty_id).value=consqnty;// issue Id=6769
		}
		if(type=='calculate_ratio'){
			var consqnty=document.getElementById(consqnty_id).value*1
			var consratio=(consqnty/tot_yarn_needed)*100;
			document.getElementById(consratio_id).value=consratio;
		}
		if(type=='calculate_consumption' || type=='calculate_ratio' || type=='calculate_amount' ){
			document.getElementById(txtamountyarn_id).value=number_format_common((document.getElementById(consqnty_id).value*1)*(document.getElementById(txtrateyarn_id).value*1),1,0,document.getElementById('cbo_currercy').value);
		}
		set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
		set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
		//calculate_cofirm_price_commision()
	}

	function set_yarn_rate(i){
		var txt_costing_date=document.getElementById('txt_quotation_date').value;
		var cbocount=document.getElementById('cbocount_'+i).value;
		var cbocompone=document.getElementById('cbocompone_'+i).value;
		var percentone=document.getElementById('percentone_'+i).value;
		var cbotype=document.getElementById('cbotype_'+i).value;
		var supplier_id=document.getElementById('supplier_'+i).value;
		var yarn_rate = return_ajax_request_value(cbocount+"_"+cbocompone+"_"+percentone+"_"+cbotype+"_"+supplier_id+"_"+txt_costing_date, 'get_yarn_rate', 'requires/pre_cost_entry_controller');
		if(yarn_rate=="" || yarn_rate==0){
			alert('Yarn Rate not set');
			return;
		}
		document.getElementById('txtrateyarn_'+i).value=number_format_common(yarn_rate,1,0,document.getElementById('cbo_currercy').value);
		calculate_yarn_consumption_ratio(i,'calculate_amount');
		//calculate_cofirm_price_commision()
	}

	function fnc_fabric_yarn_cost_dtls( operation )
	{
		var update_id=document.getElementById('update_id').value;
		if(operation==2)//operation==1 ||
		{
			var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
			var ex_job_pre_cost_data=is_job_pre_cost.split("***");
			if(ex_job_pre_cost_data[0]==1)
			{
				var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
				alert(job_pre_cost_msg);
				return
			}
		}
		freeze_window(operation);
		if(operation==2){
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		if (form_validation('update_id','Company Name')==false){
			release_freezing();
			return;
		}
		var row_num=$('#tbl_yarn_cost tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++){
			if (form_validation('cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*consqnty_'+i,'Count*Comp 1*%*Cons Qty')==false){
				release_freezing();
				return;
			}

			data_all=data_all+get_submitted_data_string('update_id*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*cbotype_'+i+'*cboyarnfinish_'+i+'*cboyarnsnippingsystem_'+i+'*consratio_'+i+'*consqnty_'+i+'*supplier_'+i+'*txtrateyarn_'+i+'*txtamountyarn_'+i+'*cbostatusyarn_'+i+'*cboyarncolor_'+i+'*txtyarncode_'+i+'*cbocertification_'+i+'*updateidyarncost_'+i+'*txtconsumptionyarn_sum*txtamountyarn_sum',"../../");
		}
		var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_yarn_cost_dtls_reponse;
	}

	function fnc_fabric_yarn_cost_dtls_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(reponse[0]==15){
				setTimeout('fnc_fabric_yarn_cost_dtls('+ reponse[1]+')',8000);
			}
			else{
				if (reponse[0].length>2) reponse[0]=10;

				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
					var pre_fabcost=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_fabric_pre_cost").attr('pre_fab_cost',pre_fabcost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
				show_hide_content('yarn_cost', '')
				if(reponse[0]==0 || reponse[0]==1){
					update_related_data(reponse[0])
					fnc_quotation_entry_dtls1( 1 )
					release_freezing();
				}
			}
		}
	}
	// End Yarn Cost------------------------------------------------------

	// Start Conversion Cost------------------------------------------------------
	function add_break_down_tr_conversion_cost( i,conversion_from_chart ){
		var row_num=$('#tbl_conversion_cost tr').length-1;
		if (i==0){
			i=1;
			$("#txtconstruction_"+i).autocomplete({
				source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
				source:  str_composition
			});
			return;
		}
		if (row_num!=i){
			return false;
		}
		else{
			i++;
			$("#tbl_conversion_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_conversion_cost");
			$('#cbocosthead_'+i).removeAttr("onChange").attr("onChange","set_conversion_qnty("+i+")");
			$('#cbotypeconversion_'+i).removeAttr("onChange").attr("onChange","set_conversion_charge_unit("+i+","+conversion_from_chart+")");
			$('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_conversion_cost("+i+","+conversion_from_chart+");");
			$('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_conversion_cost');");
			$('#txtreqqnty_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			$('#txtchargeunit_'+i).removeAttr("onChange").attr("onChange","calculate_conversion_cost( "+i+" )");
			if(conversion_from_chart==1){
				$('#txtchargeunit_'+i).removeAttr("onClick").attr("onClick","set_conversion_charge_unit_pop_up("+i+")");
			}
			$('#updateidcoversion_'+i).val("");
			set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
			set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
			set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		}
	}

	function set_conversion_charge_unit(i,conversion_from_chart){
		set_conversion_qnty(i);
		if(conversion_from_chart==1){
			document.getElementById('txtchargeunit_'+i).readOnly=true
			set_conversion_charge_unit_pop_up(i);
		}
		else{
			document.getElementById('txtchargeunit_'+i).readOnly=false
		}
	}
	function set_conversion_charge_unit_bk(i,conversion_from_chart) // off Issue-34185
	{
		
		var cost_head= $('#cbotypeconversion_'+i).val();
		set_conversion_qnty(i);
		//alert(cost_head+'='+conversion_from_chart);
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
					if(cost_head==1)
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
		if(cbotypeconversion==35){
			return;
		}
		if(cbo_company_name==0){
			alert("Select Company");
			return;
		}
		if(cbotypeconversion==0){
			alert("Select Process");
			return;
		}
		if(txt_exchange_rate==0 || txt_exchange_rate==""){
			alert("Select Exchange Rate");
			return;
		}
		else{
			var page_link='requires/quotation_entry_controller_v2.php?action=conversion_chart_popup&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&coversionchargelibraryid='+coversionchargelibraryid;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Conversion Chart', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){
				var charge_id=this.contentDoc.getElementById("charge_id");
				var charge_value=this.contentDoc.getElementById("charge_value");
				document.getElementById('coversionchargelibraryid_'+i).value=charge_id.value;
				document.getElementById('txtchargeunit_'+i).value=number_format_common(charge_value.value/txt_exchange_rate,5,0,document.getElementById('cbo_currercy').value);
				math_operation( 'txtamountconversion_'+i, 'txtreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
				set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
				set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
			}
		}
	}

	function set_conversion_qnty(i)
	{
		var cbocosthead= document.getElementById('cbocosthead_'+i).value;
		//alert(cbocosthead)
		var cbotypeconversion=document.getElementById('cbotypeconversion_'+i).value;
		var updateidcoversion=document.getElementById('updateidcoversion_'+i).value;
		if(cbocosthead !=0 && updateidcoversion==''){
			var conversion_qnty=return_global_ajax_value(cbocosthead+"_"+cbotypeconversion, 'set_conversion_qnty', '', 'requires/quotation_entry_controller_v2');
			conversion_qnty=conversion_qnty.split("_");
		}
		if(cbocosthead !=0 && updateidcoversion >0){
			var txtconsumption=document.getElementsByName('txtconsumption_'+cbocosthead)[0].value*1;
			//alert(txtconsumption)
			var txtprocessloss=document.getElementById('txtprocessloss_'+i).value*1;
			var avg_cons=txtconsumption-(txtconsumption*txtprocessloss)/100;
			var conversion_qnty=avg_cons+"_"+txtprocessloss;
			conversion_qnty=conversion_qnty.split("_");
		}
		if(cbocosthead ==0){
			var conversion_qnty=document.getElementById('txtknit_sum').value*1;
			//var conversion_qnty[0]=conversion_qnty;
			var conversion_qnty=conversion_qnty+"_";
			conversion_qnty=conversion_qnty.split("_");
		}
		//conversion_qnty=conversion_qnty.split("_");
		//alert(cbocosthead)
		document.getElementById('txtreqqnty_'+i).value=conversion_qnty[0];

		if((conversion_qnty[1]=="" || conversion_qnty[1]==0) && cbotypeconversion>0){
			$('#txtprocessloss_'+i).css({ 'background': 'White' });
			$('#txtprocessloss_'+i).attr( 'title','Process Loss not Set In Library' );
			$('#txtprocessloss_'+i).attr( 'readonly',false );
			$('#txtprocessloss_'+i).val(0);
		}
		else{
			$('#txtprocessloss_'+i).css({ 'background': 'grey' });
			$('#txtprocessloss_'+i).attr( 'title','Process Loss Found In Library' );
			$('#txtprocessloss_'+i).attr( 'readonly',true );
			$('#txtprocessloss_'+i).val(conversion_qnty[1]);
		}
		math_operation( 'txtamountconversion_'+i, 'txtreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		//calculate_cofirm_price_commision()
	}
	function calculate_conversion_cost(i){
		math_operation( 'txtamountconversion_'+i, 'txtreqqnty_'+i+'*txtchargeunit_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
		set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
		//calculate_cofirm_price_commision()
	}

	function fnc_fabric_conversion_cost_dtls( operation )
	{
		var update_id=document.getElementById('update_id').value;
		if(operation==2)//operation==1 ||
		{
			var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
			var ex_job_pre_cost_data=is_job_pre_cost.split("***");
			if(ex_job_pre_cost_data[0]==1)
			{
				var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
				alert(job_pre_cost_msg);
				return
			}
		}
		freeze_window(operation);
		if(operation==2){
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		if (form_validation('update_id','Company Name')==false){
			release_freezing();
			return;
		}
		var row_num=$('#tbl_conversion_cost tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++){
			if (form_validation('cbocosthead_'+i+'*cbotypeconversion_'+i+'*txtreqqnty_'+i,'Fabric Description*Process*Req. Qnty')==false){
				release_freezing();
				return;
			}
			data_all=data_all+get_submitted_data_string('update_id*cbocosthead_'+i+'*cbotypeconversion_'+i+'*txtprocessloss_'+i+'*txtreqqnty_'+i+'*txtchargeunit_'+i+'*txtamountconversion_'+i+'*cbostatusconversion_'+i+'*updateidcoversion_'+i+'*coversionchargelibraryid_'+i+'*txtconreqnty_sum*txtconchargeunit_sum*txtconamount_sum',"../../",i);
		}
		var data="action=save_update_delet_fabric_conversion_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_conversion_cost_dtls_reponse;
	}

	function fnc_fabric_conversion_cost_dtls_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='bomFound'){
				alert("Conversion Cost Found in BOM for Quotation "+$('#txt_quotation_id').val()+",\n So Delete Restricted. Job No :"+trim(reponse[1])+".");
				release_freezing();
				return;
			}
			if(reponse[0]==15){
				setTimeout('fnc_fabric_conversion_cost_dtls('+ reponse[1]+')',8000);
			}
			else{
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					show_msg(trim(reponse[0]));
					var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
					var pre_fabcost=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_fabric_pre_cost").attr('pre_fab_cost',pre_fabcost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
				show_hide_content('conversion_cost', '')
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
					fnc_quotation_entry_dtls1( 1 )
					release_freezing();
				}
			}
		}
	}
	// End Conversion Cost--------------------------------------------------

	// Start Trim Cost------------------------------------------------------
	function add_break_down_tr_trim_cost( i ){
		var row_num=$('#tbl_trim_cost tr').length-1;
		if (i==0){
			i=1;
			$("#txtconstruction_"+i).autocomplete({
				source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
				source:  str_composition
			});
			return;
		}
		if (row_num!=i){
			return false;
		}
		else{
			i++;
			$("#tbl_trim_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i;},
					'name': function(_, name) {return name+1;},
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_trim_cost");
			$('#tbl_trim_cost tr:last td:eq(3)').attr('id','tdsupplier_'+i);
			$('#seq_'+i).val('');
			$('#cbogrouptext_'+i).removeAttr("ondblclick").attr("ondblclick","openpopup_itemgroup("+i+")");
			if($('#seq_'+i).val()=="")
			{
				$('#seq_'+i).val( i );
			}
			$('#cbogroup_'+i).removeAttr("onChange").attr("onChange","set_trim_cons_uom( this.value,"+i+" )");
			$('#txtdescription_'+i).val("");
			$('#cbonominasupplier_'+i).removeAttr("onChange").attr("onChange","set_trim_rate_amount( this.value,"+i+",'supplier_change' )");
			$('#txtdescription_'+i).removeAttr("onDblClick").attr("onDblClick","trims_description_popup( "+i+" )");
			$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
			$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost');");
			$('#txtconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
			$('#txttrimrate_'+i).removeAttr("onChange").attr("onChange","calculate_trim_cost( "+i+" )");
			$('#txttrimrate_'+i).removeAttr("onDblClick").attr("onDblClick","trim_rate_popup( "+i+" )");
			$('#updateidtrim_'+i).val("");
			$('#cbonominasupplier_'+i).val("");
			set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
			set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
			// calculate_cofirm_price_commision()
		}
	}

	function trims_description_popup(i)
	{
		var txtdescription=document.getElementById('txtdescription_'+i).value;
		var data=txtdescription
		var title = 'Description';
		var page_link = 'requires/quotation_entry_controller_v2.php?data='+data+'&action=trims_description_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=200px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var description=this.contentDoc.getElementById("description");
			$('#txtdescription_'+i).val(description.value);
		}
	}

	function calculate_trim_cost(i){
		math_operation( 'txttrimamount_'+i, 'txtconsdzngmts_'+i+'*txttrimrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
		set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
		set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
		set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
		//calculate_cofirm_price_commision()
	}

	function set_trim_cons_uom(trim_group_id,i){
		var cbo_cons_uom=return_global_ajax_value(trim_group_id, 'set_cons_uom', '', 'requires/quotation_entry_controller_v2');
		document.getElementById('cboconsuom_'+i).value = trim(cbo_cons_uom);
		var trim_rate_variable=document.getElementById('trim_rate_variable').value;
		var buyer=document.getElementById('cbo_buyer_name').value;
		set_trim_rate_amount( document.getElementById('cbonominasupplier_'+i).value,i,'item_change' )
		load_drop_down( 'requires/quotation_entry_controller_v2', trim_group_id+"_"+i+"_"+trim_rate_variable+"_"+buyer, 'load_drop_down_supplier_rate', 'tdsupplier_'+i );
	}

	function set_trim_rate_amount(supplier,i,type){
		var updateidtrim=document.getElementById('updateidtrim_'+i).value;
		if(updateidtrim==""){
			if(type=="item_change"){
			get_trim_rate_amount(supplier,i,type)
			}
			else{
				var txttrimrate=document.getElementById('txttrimrate_'+i).value;
				if(txttrimrate==0 || txttrimrate==""){
					get_trim_rate_amount(supplier,i,type)
				}
				else{
					var r=confirm("Rate Exist,\n It may have come from price quatation or Templete or Library\n If you want to change current rate\n Press OK \n Otherwise press Cencel");
					if(r==true){
						get_trim_rate_amount(supplier,i,type)
					}
					else{
						return;
					}
				}
			}
		}
		else{
			if(type=="item_change"){
				get_trim_rate_amount(supplier,i,type)
			}
			else{
				var r=confirm("Rate Exist,\n You are in update mode\n If you want to change current rate\n Press OK \n Otherwise press Cencel");
				if(r==true){
					get_trim_rate_amount(supplier,i,type)
				}
				else{
					return;
				}
			}
		}
	}

	function trim_rate_popup(i){
		var cbogroup=document.getElementById('cbogroup_'+i).value;
		var page_link="requires/quotation_entry_controller_v2.php?cbogroup="+trim(cbogroup)+"&action=trim_rate_popup_page";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Rate', 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var txt_selected_supllier=this.contentDoc.getElementById("txt_selected_supllier");
			var txt_selected_rate=this.contentDoc.getElementById("txt_selected_rate");
			document.getElementById('cbonominasupplier_'+i).value=txt_selected_supllier.value;
			document.getElementById('txttrimrate_'+i).value=txt_selected_rate.value;
			math_operation( 'txttrimamount_'+i, 'txtconsdzngmts_'+i+'*txttrimrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
			set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost' );
			set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost' );
			set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost' );
			//calculate_cofirm_price_commision()
		}
	}

	function get_trim_rate_amount(supplier,i,type){
		var cbogroup=document.getElementById('cbogroup_'+i).value;
		var txtconsdzngmts=(document.getElementById('txtconsdzngmts_'+i).value)*1;
		var rate=return_global_ajax_value(cbogroup+"_"+supplier, 'rate_from_library', '', 'requires/quotation_entry_controller_v2');
		rate=trim(rate);
		if(type=="item_change"){
			document.getElementById('txttrimrate_'+i).value=rate;
		}
		else{
			if(rate==0){
				var r=confirm("Rate not found in Library,\n Press OK to change current rate with 0\n Otherwise press Cencel");
				if(r==true){
					document.getElementById('txttrimrate_'+i).value=rate;
				}
				else{
					return;
				}
			}
			else{
				document.getElementById('txttrimrate_'+i).value=rate;
			}
		}
		calculate_trim_cost(i);
	}

function open_calculator(i){
	var cbogroup=document.getElementById('cbogroup_'+i).value;
	var cbo_costing_per=document.getElementById('cbo_costing_per').value;
	var calculator_parameter=return_global_ajax_value( cbogroup, 'calculator_parameter', '', 'requires/quotation_entry_controller_v2');
	if(trim(calculator_parameter)!=0){
		var page_link="requires/quotation_entry_controller_v2.php?calculator_parameter="+trim(calculator_parameter)+"&action=calculator_type&cbo_costing_per="+cbo_costing_per;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Calculator', 'width=350px,height=200px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function(){
			var txt_cons_for_cone=this.contentDoc.getElementById("txt_cons_for_cone");
			var txt_clacolator_param_value=this.contentDoc.getElementById("txt_clacolator_param_value");
			document.getElementById('txtconsdzngmts_'+i).value=txt_cons_for_cone.value;
			calculate_trim_cost(i);
		}
	}
}

function fnc_trim_cost_dtls( operation )
{
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Update Or Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	freeze_window(operation);
	/*if(operation==2){
		alert("Delete Restricted")
		release_freezing();
		return;
	}*/
	if (form_validation('update_id','Company Name')==false){
		release_freezing();
		return;
	}
	var row_num=$('#tbl_trim_cost tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('cbogroup_'+i+'*txtconsdzngmts_'+i,'Group*Cons/Dzn Gmts')==false){
			release_freezing();
			return;
		}

		data_all=data_all+get_submitted_data_string('update_id*cbogroup_'+i+'*txtdescription_'+i+'*cboconsuom_'+i+'*txtconsdzngmts_'+i+'*txttrimrate_'+i+'*txttrimamount_'+i+'*cboapbrequired_'+i+'*cbonominasupplier_'+i+'*cbotrimstatus_'+i+'*updateidtrim_'+i+'*seq_'+i+'*txtconsdzntrim_sum*txtratetrim_sum*txttrimamount_sum',"../../",i);
	}
	var data="action=save_update_delet_trim_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
	http.open("POST","requires/quotation_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trim_cost_dtls_reponse;
}

function fnc_trim_cost_dtls_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(trim(reponse[0])=='approved'){
			alert("This Costing is Approved");
			release_freezing();
			return;
		}

		if(reponse[0]==15){
			setTimeout('fnc_trim_cost_dtls('+ reponse[1]+')',8000);
		}
		else{
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				var txt_trim_pre_cost=(document.getElementById('txttrimamount_sum').value*1);
				var pre_trimcost=number_format_common(txt_trim_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
				$("#txt_trim_pre_cost").attr('pre_trim_cost',pre_trimcost);

				calculate_main_total();
			}
			show_sub_form(document.getElementById('update_id').value, 'show_trim_cost_listview');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				fnc_quotation_entry_dtls1( 1 )
				release_freezing();
			}
		}
	}
}
// End Trim Cost------------------------------------------------------
// Start Embellishment Cost------------------------------------------------------
function add_break_down_tr_embellishment_cost( i ){
	var row_num=$('#tbl_embellishment_cost tr').length-1;
	if (row_num!=i){
		return false;
	}
	else{
		i++;
		$("#tbl_embellishment_cost tr:last").clone().find("input,select,td").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { return name + i },
				'value': function(_, value) { return value }
			});
		}).end().appendTo("#tbl_embellishment_cost");
		$('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_embellishment_cost("+i+");");
		$('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost');");
		$('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
		$('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
		$('#cboembname_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
		$('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
		$('#embupdateid_'+i).val("");
		$('#cboembname_'+i).val(0);
		set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
		calculate_main_total();
		//calculate_cofirm_price_commision()

	}
}

function cbotype_loder( i ){
	var cboembname=document.getElementById('cboembname_'+i).value
	load_drop_down( 'requires/quotation_entry_controller_v2', cboembname+'_'+i, 'load_drop_down_embtype', 'embtypetd_'+i );
	check_duplicate(i)
}

function check_duplicate(id){
	var cboembname=(document.getElementById('cboembname_'+id).value);
	var cboembtype=(document.getElementById('cboembtype_'+id).value);
	var row_num=$('#tbl_embellishment_cost tr').length-1;
	for (var k=1;k<=row_num; k++){
		if(k==id){
			continue;
		}
		else{
			if(cboembname==document.getElementById('cboembname_'+k).value && cboembtype==document.getElementById('cboembtype_'+k).value){
				alert("Same Name, Same Type  Duplication Not Allowed.");
				document.getElementById('cboembtype_'+id).value=0;
			}
		}
	}
}
function calculate_emb_cost(i){
	math_operation( 'txtembamount_'+i, 'txtembconsdzngmts_'+i+'*txtembrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
	set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
	//calculate_cofirm_price_commision()
}

function fnc_embellishment_cost_dtls( operation )
{
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	freeze_window(operation);
	/*if(operation==2){
		alert("Delete Restricted")
		release_freezing();
		return;
	}*/
	if (form_validation('update_id','Company Name')==false){
		release_freezing();
		return;
	}
	var row_num=$('#tbl_embellishment_cost tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++){
		if (form_validation('cboembname_'+i+'*cboembtype_'+i+'*txtembconsdzngmts_'+i,'Name*Type*Cons')==false){
			release_freezing();
			return;
		}
		data_all=data_all+get_submitted_data_string('update_id*cboembname_'+i+'*cboembtype_'+i+'*txtembconsdzngmts_'+i+'*txtembrate_'+i+'*txtembamount_'+i+'*cboembstatus_'+i+'*embupdateid_'+i+'*txtamountemb_sum',"../../",i);
	}
	var data="action=save_update_delet_embellishment_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;

	http.open("POST","requires/quotation_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_embellishment_cost_dtls_reponse;
}

function fnc_embellishment_cost_dtls_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}

		if(reponse[0]==15){
			setTimeout('fnc_embellishment_cost_dtls('+ reponse[1]+')',8000);
		}
		else{
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				var txt_embel_pre_cost=(document.getElementById('txtamountemb_sum').value*1);
				var pre_emblcost=number_format_common(txt_embel_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
				$("#txt_embel_pre_cost").attr('pre_embl_cost',pre_emblcost);

				calculate_main_total();
			}
			show_sub_form(document.getElementById('update_id').value, 'show_embellishment_cost_listview');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				fnc_quotation_entry_dtls1( 1 )
				release_freezing();
			}
		}
	}
}
// End Embellishment Cost------------------------------------------------------

// Start Wash Cost------------------------------------------------------
function add_break_down_tr_wash_cost( i,conversion_from_chart ){
	var row_num=$('#tbl_wash_cost tr').length-1;
	if (row_num!=i){
		return false;
	}
	else{
		i++;
		$("#tbl_wash_cost tr:last").clone().find("input,select,td").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { return name + i },
				'value': function(_, value) { return value }
			});
		}).end().appendTo("#tbl_wash_cost");
		$('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_wash_cost("+i+");");
		$('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost');");
		$('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
		$('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
		if(conversion_from_chart==1){
			$('#txtembrate_'+i).removeAttr("onClick").attr("onClick","set_wash_charge_unit_pop_up( "+i+" )");
		}
		$('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
		$('#embupdateid_'+i).val("");
		$('#txtembrate_'+i).val("");
		$('#txtembamount_'+i).val("");
		$('#embratelibid_'+i).val("");
		$('#cboembtype_'+i).val(0);
		set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
		calculate_main_total();
		//calculate_cofirm_price_commision()
	}
}

function check_duplicate(id){
	var cboembname=(document.getElementById('cboembname_'+id).value);
	var cboembtype=(document.getElementById('cboembtype_'+id).value);
	var row_num=$('#tbl_wash_cost tr').length-1;
	for (var k=1;k<=row_num; k++){
		if(k==id){
			continue;
		}
		else{
			if(cboembname==document.getElementById('cboembname_'+k).value && cboembtype==document.getElementById('cboembtype_'+k).value){
				alert("Same Name, Same Type  Duplication Not Allowed.");
				document.getElementById('cboembtype_'+id).value=0;
			}
		}
	}
}

function set_wash_charge_unit_pop_up(i){
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
	var embratelibid=document.getElementById('embratelibid_'+i).value;
	if(cbo_company_name==0){
		alert("Select Company");
		return;
	}
	if(txt_exchange_rate==0 || txt_exchange_rate==""){
		alert("Select Exchange Rate");
		return;
	}
	else{
		var page_link='requires/quotation_entry_controller_v2.php?action=wash_chart_popup&cbo_company_name='+cbo_company_name+'&embratelibid='+embratelibid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Conversion Chart', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var charge_id=this.contentDoc.getElementById("charge_id");
			var charge_value=this.contentDoc.getElementById("charge_value");
			document.getElementById('embratelibid_'+i).value=charge_id.value;
			document.getElementById('txtembrate_'+i).value=number_format_common(charge_value.value/txt_exchange_rate,5,0,document.getElementById('cbo_currercy').value);
			calculate_wash_cost(i)
		}
	}
}

function calculate_wash_cost(i){
	math_operation( 'txtembamount_'+i, 'txtembconsdzngmts_'+i+'*txtembrate_'+i, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
	set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost' );
	//calculate_cofirm_price_commision()
}

function fnc_wash_cost_dtls( operation )
{
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	/*if(operation==2){
		alert("Delete Restricted")
		return;
	}*/
	if (form_validation('update_id','Company Name')==false){
		return;
	}
	var data_component=fnc_quotation_entry_component();
	var row_num=$('#tbl_wash_cost tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++){
		if (form_validation('cboembname_'+i+'*cboembtype_'+i+'*txtembconsdzngmts_'+i,'Name*Type*Cons')==false){
			release_freezing();
			return;
		}
		data_all=data_all+get_submitted_data_string('update_id*cboembname_'+i+'*cboembtype_'+i+'*txtembconsdzngmts_'+i+'*txtembrate_'+i+'*txtembamount_'+i+'*cboembstatus_'+i+'*embupdateid_'+i+'*embratelibid_'+i+'*txtamountemb_sum',"../../",i);
	}
	var data="action=save_update_delet_wash_cost_dtls&operation="+operation+'&total_row='+row_num+data_all+"&data_component="+data_component;
	freeze_window(operation);
	http.open("POST","requires/quotation_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_wash_cost_dtls_reponse;
}

function fnc_wash_cost_dtls_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}
		/*if(reponse[0]==2)
		{
			show_msg(reponse[0]);
			release_freezing();
			return;
		}*/
		if(reponse[0]==15){
			setTimeout('fnc_wash_cost_dtls('+ reponse[1]+')',8000);
		}
		else{
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				var txt_wash_pre_cost=(document.getElementById('txtamountemb_sum').value*1);
				var pre_washcost=number_format_common(txt_wash_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
				$("#txt_wash_pre_cost").attr('pre_wash_cost',pre_washcost);

				calculate_main_total();
			}
			show_sub_form(document.getElementById('update_id').value, 'show_wash_cost_listview');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				fnc_quotation_entry_dtls1( 1 )
				release_freezing();
			}
		}
	}
}
// End Wash Cost------------------------------------------------------
// Start Commision Cost------------------------------------------------------
function add_break_down_tr_commission_cost( i ){
	var row_num=$('#tbl_commission_cost tr').length-1;
	if (i==0){
		i=1;
		$("#txtconstruction_"+i).autocomplete({
			source: str_construction
		});
		$("#txtcomposition_"+i).autocomplete({
			source:  str_composition
		});
		return;
	}
	if (row_num!=i){
		return false;
	}
	else{
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

function calculate_commission_cost(i){
	var currency=(document.getElementById('cbo_currercy').value)*1;
	var commission_base=document.getElementById('cbocommissionbase_'+i).value;
	var cbo_costing_per=document.getElementById('cbo_costing_per').value;
	var txtcommissionrate=(document.getElementById('txtcommissionrate_'+i).value)*1;
	var amount=0;
	if(commission_base==1){
		var total_cost=(document.getElementById('txt_confirm_price_pre_cost_dzn').value)*1;
		if(total_cost==0 || total_cost==""){
			alert("Please Insert Price Before Comn/Dzn")
		}
		var txtcommissionrate_percent=txtcommissionrate/100;
		var amount=(total_cost/(1-txtcommissionrate_percent))-total_cost;
	}
	else if(commission_base==2){
		if(cbo_costing_per==1){
			var amount=txtcommissionrate*12*1;
		}
		if(cbo_costing_per==2){
			var amount=txtcommissionrate*1;
		}
		if(cbo_costing_per==3){
			var amount=txtcommissionrate*12*2;
		}
		if(cbo_costing_per==4){
			var amount=txtcommissionrate*12*3;
		}
		if(cbo_costing_per==5){
			var amount=txtcommissionrate*12*4;
		}
	}
	else if(commission_base==3){
		if(cbo_costing_per==1){
			var amount=txtcommissionrate*1*1;
		}
		if(cbo_costing_per==2){
			var amount=txtcommissionrate/12;
		}
		if(cbo_costing_per==3){
			var amount=txtcommissionrate*1*2;
		}
		if(cbo_costing_per==4){
			var amount=txtcommissionrate*1*3;
		}
		if(cbo_costing_per==5){
			var amount=txtcommissionrate*1*4;
		}
	}
	document.getElementById('txtcommissionamount_'+i).value=number_format_common(amount,1,0,cbo_currercy);
	set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
	set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
	//calculate_cofirm_price_commision()
}

function recalculate_commision_cost(){
	freeze_window(operation);
    var row_num=$('#tbl_commission_cost tr').length-1;
	for(var i=1; i<=row_num; i++){
		calculate_commission_cost(i)
	}
	fnc_commission_cost_dtls1( 1 )
}

function fnc_commission_cost_dtls( operation )
{
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	/*if(operation==2){
		alert("Delete Restricted")
		release_freezing();
		return;
	}*/
	if (form_validation('update_id','Company Name')==false){
		release_freezing();
		return;
	}
	var row_num=$('#tbl_commission_cost tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++){
		/*if (form_validation('cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionamount_'+i,'Particulars*Commn. Base*Amount')==false){
			release_freezing();
			return;
		}*/
		data_all=data_all+get_submitted_data_string('update_id*cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionrate_'+i+'*txtcommissionamount_'+i+'*cbocommissionstatus_'+i+'*commissionupdateid_'+i+'*txtratecommission_sum*txtamountcommission_sum',"../../",i);
	}
	var data="action=save_update_delet_commission_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
	http.open("POST","requires/quotation_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_commission_cost_dtls_reponse;
}

function fnc_commission_cost_dtls_reponse(){
	if(http.readyState == 4) {
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}
		if(reponse[0]==15){
			setTimeout('fnc_commission_cost_dtls('+ reponse[1]+')',8000);
		}
		else{
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				var txt_commission_pre_cost=(document.getElementById('txtamountcommission_sum').value*1);
				var pre_commiscost=number_format_common(txt_commission_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
				$("#txt_commission_pre_cost").attr('pre_commis_cost',pre_commiscost);

				calculate_main_total();
			}
			show_sub_form(document.getElementById('update_id').value, 'show_commission_cost_listview');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){
				fnc_quotation_entry_dtls1( 1 )
				release_freezing();
			}
		}
	}
}

function fnc_commission_cost_dtls1( operation )
{
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	if(operation==2){
		alert("Delete Restricted")
		return;
	}
	if (form_validation('update_id','Company Name')==false){
		return;
	}
	var row_num=$('#tbl_commission_cost tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++){
		if (form_validation('cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionamount_'+i,'Particulars*Commn. Base*Amount')==false){
			release_freezing();
			return;
		}
		data_all=data_all+get_submitted_data_string('update_id*cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionrate_'+i+'*txtcommissionamount_'+i+'*cbocommissionstatus_'+i+'*commissionupdateid_'+i+'*txtratecommission_sum*txtamountcommission_sum',"../../");
	}
	var data="action=save_update_delet_commission_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
	http.onreadystatechange = function(){
		if(http.readyState == 4 && http.status == 200){
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(reponse[0]==15){
				setTimeout('fnc_commission_cost_dtls('+ reponse[1]+')',8000);
			}
			else{
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					var txt_commission_pre_cost=(document.getElementById('txtamountcommission_sum').value*1);
					var pre_commiscost=number_format_common(txt_commission_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_commission_pre_cost").attr('pre_commis_cost',pre_commiscost);

					calculate_main_total();
				}
				show_sub_form(document.getElementById('update_id').value, 'show_commission_cost_listview');
				if(reponse[0]==0 || reponse[0]==1){
					fnc_quotation_entry_dtls1( 1 )
				}
			}
		}
	};
	http.open("POST","requires/quotation_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
}
// End Commision Cost------------------------------------------------------
// Start Comarcial Cost------------------------------------------------------

function add_break_down_tr_comarcial_cost( i ){
	var row_num=$('#tbl_comarcial_cost tr').length-1;
	if (row_num!=i){
		return false;
	}
	else{
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
		$('#txtcomarcialrate_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'rate' )");
		$('#txtcomarcialamount_'+i).removeAttr("onChange").attr("onChange","calculate_comarcial_cost( "+i+",'amount' )");
		$('#comarcialupdateid_'+i).val("");
		set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
		set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
		//calculate_cofirm_price_commision()
	}
}

function calculate_comarcial_cost(i,type){
	var txt_commercial_cost_method=document.getElementById('txt_commercial_cost_method').value*1;
	var update_id=document.getElementById('update_id').value;
	var currency=(document.getElementById('cbo_currercy').value)*1;
	var txtcomarcialrate=(document.getElementById('txtcomarcialrate_'+i).value)*1;
	var txtcomarcialamount=(document.getElementById('txtcomarcialamount_'+i).value)*1;

	var amount=0;
	if(txt_commercial_cost_method==1 || txt_commercial_cost_method==4 || txt_commercial_cost_method==5)
	{
		var sum_fab_yarn_trim=return_global_ajax_value(update_id+'_'+txt_commercial_cost_method, 'sum_fab_yarn_trim_emblish_value', '', 'requires/quotation_entry_controller_v2');
		var amount=number_format_common(sum_fab_yarn_trim, 1, 0, currency);
	}
	if(type=='rate'){
		var com_amount=amount*(txtcomarcialrate/100);
		document.getElementById('txtcomarcialamount_'+i).value=number_format_common(com_amount, 1, 0, currency);
	}
	if(type=='amount'){
		var com_rate=(txtcomarcialamount*100)/amount;
		//alert(amount);
		document.getElementById('txtcomarcialrate_'+i).value=number_format_common(com_rate, 1, 0, currency);
	}
	set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
	set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
}

function fnc_comarcial_cost_dtls( operation )
{
	freeze_window(operation);
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}

	/*if(operation==2){
		alert("Delete Restricted")
		release_freezing();
		return;
	}*/
	if (form_validation('update_id','Company Name')==false){
		release_freezing();
		return;
	}
	var row_num=$('#tbl_comarcial_cost tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++){
		if (form_validation('cboitem_'+i+'*txtcomarcialamount_'+i,'Item*Amount')==false){
			release_freezing();
			return;
		}
		data_all=data_all+get_submitted_data_string('update_id*cboitem_'+i+'*txtcomarcialrate_'+i+'*txtcomarcialamount_'+i+'*cbocomarcialstatus_'+i+'*comarcialupdateid_'+i+'*txtratecomarcial_sum*txtamountcomarcial_sum',"../../",i);
	}
	var data="action=save_update_delet_comarcial_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
	http.open("POST","requires/quotation_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_comarcial_cost_dtls_reponse;
}

function fnc_comarcial_cost_dtls_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}

		/*if(reponse[0]==2)
		{
			show_msg(reponse[0]);
			release_freezing();
			return;
		}*/
			//alert(reponse[0]);
		if(reponse[0]==15){
			setTimeout('fnc_comarcial_cost_dtls('+ reponse[1]+')',8000);
		}
		else{

			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{

				var txt_comml_pre_cost=(document.getElementById('txtamountcomarcial_sum').value*1);
				var pre_commlcost=number_format_common(txt_comml_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
				//alert(reponse[0]);
				$("#txt_comml_pre_cost").attr('pre_comml_cost',pre_commlcost);

				calculate_main_total();
			}

			show_sub_form(document.getElementById('update_id').value, 'show_comarcial_cost_listview');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2){

				fnc_quotation_entry_dtls1( 1 )
				release_freezing();
			}

		}
	}
}
// End Comarcial Cost------------------------------------------------------

// Start Master Form-----------------------------------------
function openmypage(page_link,title){
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company_id='+$("#cbo_company_name").val(), title, 'width=1100px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theemail=this.contentDoc.getElementById("selected_id")
		if (theemail.value!=""){
			freeze_window(5);
			$("#hidd_is_dtls_open").val(1);
			reset_form('quotationdtls_2','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2','')
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/quotation_entry_controller_v2" );
			var cpm=return_global_ajax_value($("#cbo_company_name").val()+"_"+$('#txt_quotation_date').val()+"_"+$('#cbo_location_id').val(), 'cost_per_minute', '', 'requires/quotation_entry_controller_v2');
			$("#cost_per_minute").val(cpm);
			summary();
			set_button_status(1, permission, 'fnc_quotation_entry',1);
			set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
			release_freezing();
		}
	}
}

function openmypage_inquery(){
	var page_link='requires/quotation_entry_controller_v2.php?action=inquery_id_popup';
	var title='Inquiry ID Selection Form' ;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theemail=this.contentDoc.getElementById("selected_id").value.split('_');
		if (theemail!=""){
			document.getElementById('txt_inquery_id').value=theemail[0];
			document.getElementById('cbo_company_name').value=theemail[1];
			load_drop_down( 'requires/quotation_entry_controller_v2',theemail[1], 'load_drop_down_buyer', 'buyer_td' );
			document.getElementById('cbo_buyer_name').value=theemail[2];
			document.getElementById('txt_style_ref').value=theemail[3];
			document.getElementById('txt_inquery_prifix').value=theemail[4];
			document.getElementById('cbo_season_name').value=theemail[5];
			get_php_form_data( theemail[0], "populate_sampledevelopment_data_taged_with_inquery", "requires/quotation_entry_controller_v2" );
			check_quatation()
		}
	}
}

function open_set_popup(unit_id){
	var txt_quotation_id=document.getElementById('update_id').value;
	var set_breck_down=document.getElementById('set_breck_down').value;
	var tot_set_qnty=document.getElementById('tot_set_qnty').value;
	var txt_inquery_id=document.getElementById('txt_inquery_id').value;
	var set_smv_id=document.getElementById('set_smv_id').value;
	var txt_style_ref=document.getElementById('txt_style_ref').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var item_id=document.getElementById('item_id').value;

	var page_link="requires/quotation_entry_controller_v2.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&txt_inquery_id='+txt_inquery_id+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&item_id='+item_id;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Item Details", 'width=860px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var set_breck_down=this.contentDoc.getElementById("set_breck_down")
		var item_id=this.contentDoc.getElementById("item_id")
		var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty")
		var tot_smv_qnty=this.contentDoc.getElementById('tot_smv_qnty');
		document.getElementById('set_breck_down').value=set_breck_down.value;
		document.getElementById('item_id').value=item_id.value;
		document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
		document.getElementById('txt_sew_smv').value=tot_smv_qnty.value;
		calculate_cm_cost_with_method();
		fnc_calculate_dep_oper_interest_income();
	}
}
function set_exchange_rate(currency){
	if(currency==1){
		document.getElementById('txt_exchange_rate').value=1;
	}
	else{
		document.getElementById('txt_exchange_rate').value=80;
	}
}

function calculate_lead_time(){
	var txt_est_ship_date= document.getElementById('txt_est_ship_date').value;
	var txt_op_date= document.getElementById('txt_op_date').value;
	var lead_time = return_ajax_request_value(txt_est_ship_date+"_"+txt_op_date, 'lead_time_calculate', 'requires/quotation_entry_controller_v2')
	document.getElementById('txt_lead_time').value=trim(lead_time)
}

function fnc_quotation_entry( operation )
{
	if(($("#hidd_is_dtls_open").val())==2)
	{
		alert("PLease Update Summary Part or Browse Quotation ID or Reload Page. Then you can update Master Part.");
		release_freezing();
		return;
	}
	var cm_editable=$("#cm_cost_editable").val()*1;
	//alert(cm_editable);
	var update_id=document.getElementById('update_id').value;
	var txt_confirm_date=document.getElementById('txt_confirm_date_pre_cost').value;
	var ready_to_approved=document.getElementById('cbo_ready_to_approved').value;
	if(operation==0 || operation==1)
	{
		if(cm_editable==1) //Yes
		{
			if(ready_to_approved == 1)
			{
				if (form_validation('txt_cm_pre_cost','CM Cost')==false){
				release_freezing();
				return;
				}
			}
		}
	}
	if(operation==1 && ready_to_approved==1)
	{
		if(txt_confirm_date=="")
		{
			alert('Confirm date is empty.');
			$('#txt_confirm_date_pre_cost').focus();
			release_freezing();
			return;
		}
	}

	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	var zero_val="0";
	if(operation==2)
	{
		var r=confirm("You are Going to Delete Quotation ID.\n Please, Press OK to Delete.\n Otherwise Press Cencel.");
		//alert(r); return;
		if(r==true) zero_val="1";
		else
		{
			zero_val="0"
			return;
		}
	}
	if(operation==0 || operation==1)
	{
		if(ready_to_approved==1)
		{
			var detail_part=return_global_ajax_value(update_id, 'detail_part_save_check', '', 'requires/quotation_entry_controller_v2');
			var response=trim(detail_part).split('**');
			//alert(response[0]);
			var fab_qty=response[1]*1;
			var yarn_qty=response[2]*1;
			var trim_qty=response[3]*1;
			var embl_qty=response[4]*1;
			var wash_qty=response[5]*1;
			var commer_qty=response[6]*1;
			//alert(fab_qty+'='+commer_qty);
			if(fab_qty==0) var fab_msg="Fabric"+"\n";else fab_msg="";
			if(yarn_qty==0) var yarn_msg="Yarn"+"\n";else yarn_msg="";
			if(trim_qty==0) var trim_msg="Trim"+"\n";else trim_msg="";
			if(embl_qty==0) var embl_msg="Emblishment"+"\n";else embl_msg="";
			if(wash_qty==0) var wash_msg="Wash"+"\n";else wash_msg="";
			if(commer_qty==0) var commer_msg="Commercial"+"\n";else commer_msg="";
			//var entry_mst=fab_msg+','+yarn_msg+','+trim_msg+','+embl_msg+','+wash_msg+','+commer_msg;
			if(response[0]=='DetailPartNotEntryFound')
			{
				//alert(response[0]);
				//var r=confirm('Without of following Heads, is Quotation valid?\n'+fab_msg+'\n'+ yarn_msg+'\n'+ trim_msg+'\n'+  embl_msg+'\n'+ wash_msg+'\n'+ commer_msg+'\n Press  \'OK\' to Allow Entry\nPress  \'Cancel\'  to  Not Allow Entry');
					var r=confirm('Without of following Heads, is Quotation valid?\n'+fab_msg+ yarn_msg+ trim_msg+  embl_msg+ wash_msg+ commer_msg+'\n Press  \'OK\' to Allow Entry\nPress  \'Cancel\'  to  Not Allow Entry');
				if (r==false) {
					//release_freezing();
					return;
				}

				//release_freezing();
				//return;
			}
		}
	}

	freeze_window(operation);
	if($("#txt_inquery_id").attr('placeholder')==1){
		if (form_validation('txt_inquery_prifix','Inquiry ID')==false){
			release_freezing();
			return;
		}
	}
	var data_component=fnc_quotation_entry_component();
	//alert(data_component); return;

	if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*cbo_currercy*txt_exchange_rate*cbo_costing_per*cbo_order_uom*item_id*txt_quotation_date','Company Name*Buyer Name*Style Ref*Currency*Exchange Rate*Costing Per*UOM*Item*Quot Date')==false){
		release_freezing();
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+"&zero_value="+zero_val+get_submitted_data_string('txt_quotation_id*cbo_company_name*cbo_location_id*cbo_buyer_name*txt_style_ref*txt_style_ref_id*txt_revised_no*cbo_pord_dept*txt_product_code*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*update_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_season_name*cbo_dealing_merchant*txt_op_date*txt_inquery_id*txt_m_list_no*txt_bh_marchant*cbo_ready_to_approved*set_smv_id*txt_mkt_no*txt_style_id*txt_division*cbo_buyer_brand_id*cbo_department_id*cbo_level_type_id*cbo_design_type*txt_style_short_name',"../../")+"&data_component="+data_component;

		//alert(data); release_freezing(); return;
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_quotation_entry_reponse;
	}
}

function fnc_quotation_entry_reponse(){
	if(http.readyState == 4) {
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=='approved')
		{
			alert("This quotation is approved");
			release_freezing();
			return;
		}
		if(reponse[0]=='approvedPre')
		{
			alert("Budget is approved againts this quotation");
			release_freezing();
			return;
		}
		if(reponse[0]=='DetailPartApp')
		{
		//var zero_val='';
			var r=confirm("Detail part not save found! Press  \"OK\"  to Not Allowed Entry\nPress  \"Cancel\"  to  Allowed Entry");
			if (r==true) {
				release_freezing();
				return;
			}
			release_freezing();
		}
		//release_freezing();
		//return;
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);

		if(reponse[3]==1){
			document.getElementById('cbo_approved_status').value = '2';
			document.getElementById('approve1').value = 'Approved';
			document.getElementById('cbo_company_name').disabled=false;
			document.getElementById('cbo_buyer_name').disabled=false;
			document.getElementById('txt_style_ref').disabled=false;
			document.getElementById('txt_revised_no').disabled=false;
			document.getElementById('cbo_pord_dept').disabled=false;
			document.getElementById('txt_style_desc').disabled=false;
			document.getElementById('cbo_currercy').disabled=false;
			document.getElementById('cbo_agent').disabled=false;
			document.getElementById('txt_offer_qnty').disabled=false;
			document.getElementById('cbo_region').disabled=false;
			document.getElementById('cbo_color_range').disabled=false;
			document.getElementById('cbo_inco_term').disabled=false;
			document.getElementById('txt_incoterm_place').disabled=false;
			document.getElementById('txt_machine_line').disabled=false;
			document.getElementById('txt_prod_line_hr').disabled=false;
			document.getElementById('cbo_costing_per').disabled=false;
			document.getElementById('txt_quotation_date').disabled=false;
			document.getElementById('txt_est_ship_date').disabled=false;
			document.getElementById('txt_factory').disabled=false;
			document.getElementById('txt_remarks').disabled=false;
			document.getElementById('cbo_order_uom').disabled=false;
			document.getElementById('image_button').disabled=false;
			document.getElementById('set_button').disabled=false;
			//document.getElementById('save1').disabled=false;
			//document.getElementById('update1').disabled=false;
			//document.getElementById('Delete1').disabled=false;
			//===================
			document.getElementById('txt_lab_test_pre_cost').disabled=false;
			document.getElementById('txt_inspection_pre_cost').disabled=false;
			document.getElementById('txt_cm_pre_cost').disabled=false;
			document.getElementById('txt_freight_pre_cost').disabled=false;
			document.getElementById('txt_common_oh_pre_cost').disabled=false;
			document.getElementById('txt_1st_quoted_price_pre_cost').disabled=false;
			document.getElementById('txt_first_quoted_price_date').disabled=false;
			document.getElementById('txt_revised_price_pre_cost').disabled=false;
			document.getElementById('txt_revised_price_date').disabled=false;
			document.getElementById('txt_confirm_price_pre_cost').disabled=false;
			document.getElementById('txt_confirm_date_pre_cost').disabled=false;
			//document.getElementById('save2').disabled=false;
			//document.getElementById('update2').disabled=false;
			//document.getElementById('Delete2').disabled=false;
		}
		if(reponse[3]==2){
			location.reload();
			document.getElementById('cbo_approved_status').value = '1';
			document.getElementById('approve1').value = 'Un-Approved';
			document.getElementById('cbo_company_name').disabled=true;
			document.getElementById('cbo_buyer_name').disabled=true;
			document.getElementById('txt_style_ref').disabled=true;
			document.getElementById('txt_revised_no').disabled=true;
			document.getElementById('cbo_pord_dept').disabled=true;
			document.getElementById('txt_style_desc').disabled=true;
			document.getElementById('cbo_currercy').disabled=true;
			document.getElementById('cbo_agent').disabled=true;
			document.getElementById('txt_offer_qnty').disabled=true;
			document.getElementById('cbo_region').disabled=true;
			document.getElementById('cbo_color_range').disabled=true;
			document.getElementById('cbo_inco_term').disabled=true;
			document.getElementById('txt_incoterm_place').disabled=true;
			document.getElementById('txt_machine_line').disabled=true;
			document.getElementById('txt_prod_line_hr').disabled=true;
			document.getElementById('cbo_costing_per').disabled=true;
			document.getElementById('txt_quotation_date').disabled=true;
			document.getElementById('txt_est_ship_date').disabled=true;
			document.getElementById('txt_factory').disabled=true;
			document.getElementById('txt_remarks').disabled=true;
			document.getElementById('cbo_order_uom').disabled=true;
			document.getElementById('image_button').disabled=true;
			document.getElementById('set_button').disabled=true;
			//document.getElementById('save1').disabled=true;
			//document.getElementById('update1').disabled=true;
			//document.getElementById('Delete1').disabled=true;
			//===================
			document.getElementById('txt_lab_test_pre_cost').disabled=true;
			document.getElementById('txt_inspection_pre_cost').disabled=true;
			document.getElementById('txt_cm_pre_cost').disabled=true;
			document.getElementById('txt_freight_pre_cost').disabled=true;
			document.getElementById('txt_common_oh_pre_cost').disabled=true;
			document.getElementById('txt_1st_quoted_price_pre_cost').disabled=true;
			document.getElementById('txt_first_quoted_price_date').disabled=true;
			document.getElementById('txt_revised_price_pre_cost').disabled=true;
			document.getElementById('txt_revised_price_date').disabled=true;
			document.getElementById('txt_confirm_price_pre_cost').disabled=true;
			document.getElementById('txt_confirm_date_pre_cost').disabled=true;
			//document.getElementById('save2').disabled=true;
			//document.getElementById('update2').disabled=true;
			//document.getElementById('Delete2').disabled=true;
         }

		if(reponse[0]==0 || reponse[0]==1){
			//fnc_quotation_entry_dtls1( reponse[0] )
			document.getElementById('update_id').value  = reponse[1];
			document.getElementById('txt_quotation_id').value  = reponse[1];
			document.getElementById('update_id_dtls').value  = reponse[2];

			set_button_status(1, permission, 'fnc_quotation_entry',1);
			set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
			summary();
			show_list_view(reponse[1],'show_fabric_cost_listview','cost_container','../woven_order/requires/quotation_entry_controller_v2','');
			release_freezing();
		}
		else if (reponse[0]==10)
		{
			release_freezing();
		}

		if(reponse[0]==2){
			reset_form('quotationmst_1','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2','')
			reset_form('quotationdtls_2','','txt_expected_1*txt_expected_2*txt_expected_3*txt_expected_4*txt_expected_5*txt_expected_6*txt_confirmed_1*txt_confirmed_2*txt_confirmed_3*txt_confirmed_4*txt_confirmed_5*txt_confirmed_6*txt_deviation_1*txt_deviation_2*txt_deviation_3*txt_deviation_4*txt_deviation_5*txt_deviation_6','','')
			summary();
			set_button_status(0, permission, 'fnc_quotation_entry',1);
			release_freezing();
		}
		if(reponse[0]==3 && reponse[3]==2){
			release_freezing();
			document.getElementById('app_sms').innerHTML = 'This Quotation Is Approved'

		}
		if(reponse[0]==3 && reponse[3]==1){
			release_freezing();
			document.getElementById('app_sms').innerHTML = ''
		}

		/*if(document.getElementById('cbo_ready_to_approved').value==1){
			if(confirm("Mail Send! Are you sure?")){
				var returnValue=return_global_ajax_value(document.getElementById('txt_quotation_id').value, 'mail_action', '', '../../auto_mail/price_quotation_mail_notification');
				alert(returnValue);
			}
		}*/


	}
}

function copy_quatation(operation){
	freeze_window(operation);
	if (form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref*cbo_currercy*txt_exchange_rate*cbo_costing_per*cbo_order_uom*item_id*txt_quotation_date','Company Name*Buyer Name*Style Ref*Currency*Exchange Rate*Costing Per*UOM*Item*Quot Date')==false){
		release_freezing();
		return;
	}
	else{
		var data="action=copy_quatation&operation="+operation+get_submitted_data_string('txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_style_ref_id*txt_revised_no*cbo_pord_dept*txt_product_code*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*update_id*cbo_approved_status*cm_cost_predefined_method_id*txt_exchange_rate*txt_sew_smv*txt_cut_smv*txt_sew_efficiency_per*txt_cut_efficiency_per*txt_efficiency_wastage*cbo_season_name*cbo_dealing_merchant',"../../");
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = copy_quatation_reponse;
	}
}

	function copy_quatation_reponse()
	{
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			document.getElementById('update_id').value  = reponse[2];
			document.getElementById('txt_quotation_id').value  = reponse[2];
			document.getElementById('update_id_dtls').value  = reponse[3];
			document.getElementById('txt_quotation_date').value  = reponse[4];
			if(reponse[3]==1){
				document.getElementById('cbo_approved_status').value = '2';
				document.getElementById('approve1').value = 'Approved';
				document.getElementById('cbo_company_name').disabled=false;
				document.getElementById('cbo_buyer_name').disabled=false;
				document.getElementById('txt_style_ref').disabled=false;
				document.getElementById('txt_revised_no').disabled=false;
				document.getElementById('cbo_pord_dept').disabled=false;
				document.getElementById('txt_style_desc').disabled=false;
				document.getElementById('cbo_currercy').disabled=false;
				document.getElementById('cbo_agent').disabled=false;
				document.getElementById('txt_offer_qnty').disabled=false;
				document.getElementById('cbo_region').disabled=false;
				document.getElementById('cbo_color_range').disabled=false;
				document.getElementById('cbo_inco_term').disabled=false;
				document.getElementById('txt_incoterm_place').disabled=false;
				document.getElementById('txt_machine_line').disabled=false;
				document.getElementById('txt_prod_line_hr').disabled=false;
				document.getElementById('cbo_costing_per').disabled=false;
				document.getElementById('txt_quotation_date').disabled=false;
				document.getElementById('txt_est_ship_date').disabled=false;
				document.getElementById('txt_factory').disabled=false;
				document.getElementById('txt_remarks').disabled=false;
				document.getElementById('cbo_order_uom').disabled=false;
				document.getElementById('image_button').disabled=false;
				document.getElementById('set_button').disabled=false;
				document.getElementById('save1').disabled=false;
				document.getElementById('update1').disabled=false;
				document.getElementById('Delete1').disabled=false;
				//===================
				document.getElementById('txt_lab_test_pre_cost').disabled=false;
				document.getElementById('txt_inspection_pre_cost').disabled=false;
				document.getElementById('txt_cm_pre_cost').disabled=false;
				document.getElementById('txt_freight_pre_cost').disabled=false;
				document.getElementById('txt_common_oh_pre_cost').disabled=false;
				document.getElementById('txt_1st_quoted_price_pre_cost').disabled=false;
				document.getElementById('txt_first_quoted_price_date').disabled=false;
				document.getElementById('txt_revised_price_pre_cost').disabled=false;
				document.getElementById('txt_revised_price_date').disabled=false;
				document.getElementById('txt_confirm_price_pre_cost').disabled=false;
				document.getElementById('txt_confirm_date_pre_cost').disabled=false;
				document.getElementById('save2').disabled=false;
				document.getElementById('update2').disabled=false;
				document.getElementById('Delete2').disabled=false;
			}
			if(reponse[3]==2){
				document.getElementById('cbo_approved_status').value = '1';
				document.getElementById('approve1').value = 'Un-Approved';
				document.getElementById('cbo_company_name').disabled=true;
				document.getElementById('cbo_buyer_name').disabled=true;
				document.getElementById('txt_style_ref').disabled=true;
				document.getElementById('txt_revised_no').disabled=true;
				document.getElementById('cbo_pord_dept').disabled=true;
				document.getElementById('txt_style_desc').disabled=true;
				document.getElementById('cbo_currercy').disabled=true;
				document.getElementById('cbo_agent').disabled=true;
				document.getElementById('txt_offer_qnty').disabled=true;
				document.getElementById('cbo_region').disabled=true;
				document.getElementById('cbo_color_range').disabled=true;
				document.getElementById('cbo_inco_term').disabled=true;
				document.getElementById('txt_incoterm_place').disabled=true;
				document.getElementById('txt_machine_line').disabled=true;
				document.getElementById('txt_prod_line_hr').disabled=true;
				document.getElementById('cbo_costing_per').disabled=true;
				document.getElementById('txt_quotation_date').disabled=true;
				document.getElementById('txt_est_ship_date').disabled=true;
				document.getElementById('txt_factory').disabled=true;
				document.getElementById('txt_remarks').disabled=true;
				document.getElementById('cbo_order_uom').disabled=true;
				document.getElementById('image_button').disabled=true;
				document.getElementById('set_button').disabled=true;
				document.getElementById('save1').disabled=true;
				document.getElementById('update1').disabled=true;
				document.getElementById('Delete1').disabled=true;
				//===================
				document.getElementById('txt_lab_test_pre_cost').disabled=true;
				document.getElementById('txt_inspection_pre_cost').disabled=true;
				document.getElementById('txt_cm_pre_cost').disabled=true;
				document.getElementById('txt_freight_pre_cost').disabled=true;
				document.getElementById('txt_common_oh_pre_cost').disabled=true;
				document.getElementById('txt_1st_quoted_price_pre_cost').disabled=true;
				document.getElementById('txt_first_quoted_price_date').disabled=true;
				document.getElementById('txt_revised_price_pre_cost').disabled=true;
				document.getElementById('txt_revised_price_date').disabled=true;
				document.getElementById('txt_confirm_price_pre_cost').disabled=true;
				document.getElementById('txt_confirm_date_pre_cost').disabled=true;
				document.getElementById('save2').disabled=true;
				document.getElementById('update2').disabled=true;
				document.getElementById('Delete2').disabled=true;
			}
			set_button_status(1, permission, 'fnc_quotation_entry',1);
			show_list_view(reponse[2],'show_fabric_cost_listview','cost_container','../woven_order/requires/quotation_entry_controller_v2','');
			release_freezing();
		}
	}

	function cm_cost_predefined_method(company_id,cm_cost_method) 
	{
		
		if(cm_cost_method ==0){
			$("#txt_cm_pre_cost").attr("disabled",false);
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",true);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
		}
		else if(cm_cost_method ==1){
			$("#txt_sew_smv").attr("disabled",false);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
			$("#txt_cm_pre_cost").attr("disabled",true);
		}
		else if(cm_cost_method ==2){
			$("#txt_sew_smv").attr("disabled",false);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",false);
			$("#txt_cut_efficiency_per").attr("disabled",false);
			$("#txt_cm_pre_cost").attr("disabled",true);
		}
		else if(cm_cost_method ==3){
			$("#txt_sew_smv").attr("disabled",true);
			$("#txt_sew_efficiency_per").attr("disabled",true);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);
			$("#txt_cm_pre_cost").attr("disabled",true);
		}
		else if(cm_cost_method ==4)
		{
			document.getElementById('txt_cm_pre_cost').disabled=true;
			$("#txt_sew_smv").attr("disabled",false);
			$("#txt_sew_efficiency_per").attr("disabled",false);
			$("#txt_cut_smv").attr("disabled",true);
			$("#txt_cut_efficiency_per").attr("disabled",true);

		}
		document.getElementById('cm_cost_predefined_method_id').value=cm_cost_method;
	}

	function fnc_smv_integration(company_id,set_smv_id)
	{
		$('#set_smv_id').val( set_smv_id );
	}
	function style_from_library(data){
		if(data == 1){
			$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_style_ref_popup()");
			$('#txt_style_ref').prop('readonly', true);
			$('#txt_style_ref').val("");
			$('#txt_style_ref_id').val("");
			$('#txt_style_ref').attr('placeholder','Browse');
		}
		if(data == 2){
			$('#txt_style_ref').removeAttr("onDblClick");
			$('#txt_style_ref').prop('readonly', false);
			$('#txt_style_ref').val("");
			$('#txt_style_ref_id').val("");
			$('#txt_style_ref').attr('placeholder','Write');
		}
	}
	function open_style_ref_popup()
	{
		if(form_validation('cbo_buyer_name','Buyer Name')==false)
		{
			return;
		}
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_style_ref_id=document.getElementById('txt_style_ref_id').value;
	 	var page_link='requires/quotation_entry_controller_v2.php?action=style_ref_popup&txt_style_ref='+txt_style_ref+'&txt_style_ref_id='+txt_style_ref_id+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Style Description', 'width=1090px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var txt_style=this.contentDoc.getElementById("txt_style_ref");
			var id_style=this.contentDoc.getElementById("txt_style_ref_id");
			var all_data=this.contentDoc.getElementById("txt_all_data").value;
			$('#txt_style_ref').val(txt_style.value);
			$('#txt_style_ref_id').val(id_style.value);
			var data=all_data.split('***');
			$('#cbo_order_uom').val(data[2]);
			$('#cbo_pord_dept').val(data[3]);
			$('#item_id').val(data[4]);
			$('#cbo_buyer_brand_id').val(data[5]);
			$('#txt_division').val(data[6]);
			$('#cbo_department_id').val(data[7]);
			$('#cbo_level_type_id').val(data[8]);
			$('#cbo_design_type').val(data[9]);
			$('#txt_style_short_name').val(data[10]);
			$('#txt_style_id').val(data[11]);
		
			//txt_style_id*txt_division*cbo_buyer_brand_id*cbo_department_id*cbo_level_type_id*cbo_design_type*txt_style_short_name
			console.log(all_data);
			check_quatation();

		}
	}
	//// End Master Form-----------------------------------------
	// Start Dtls Form ------------------------------------------

	function  change_caption_cost_dtls( value, type )
	{
		if(type=="change_caption_dzn"){
			if(value==1){
				document.getElementById('confirm_price_td_dzn').innerHTML="Price Before Commn/ 1 Dzn";
				document.getElementById('prod_cost_td_dzn').innerHTML="Prd. Cost /1 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/1 Dzn";
				document.getElementById('commission_dzn').innerHTML="Commn/1 Dzn";
				document.getElementById('price_with_comm_dzn_td').innerHTML="Price with Commn/ 1 Dzn";
			}
			if(value==2){
				document.getElementById('confirm_price_td_dzn').innerHTML="Price Before Commn/ 1 Pcs";
				document.getElementById('prod_cost_td_dzn').innerHTML="Prd. Cost / 1 Pcs";
				document.getElementById('margin_dzn').innerHTML="Margin/ 1 Pcs";
				document.getElementById('commission_dzn').innerHTML="Commn/1 pcs";
				document.getElementById('price_with_comm_dzn_td').innerHTML="Price with Commn/ 1 Pcs";
			}
			if(value==3){
				document.getElementById('confirm_price_td_dzn').innerHTML="Price Before Commn/ 2 Dzn";
				document.getElementById('prod_cost_td_dzn').innerHTML="Prd. Cost /2 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 2 Dzn";
				document.getElementById('commission_dzn').innerHTML="Commn/2 Dzn";
				document.getElementById('price_with_comm_dzn_td').innerHTML="Price with Commn/2 Dzn";
			}
			if(value==4){
				document.getElementById('confirm_price_td_dzn').innerHTML="Price Before Commn/ 3 Dzn";
				document.getElementById('prod_cost_td_dzn').innerHTML="Prd. Cost / 3 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 3 Dzn";
				document.getElementById('commission_dzn').innerHTML="Commn/ 3 Dzn";
				document.getElementById('price_with_comm_dzn_td').innerHTML="Price with Commn/ 3 Dzn";
			}
			if(value==5){
				document.getElementById('confirm_price_td_dzn').innerHTML="Price Before Commn/ 4 Dzn";
				document.getElementById('prod_cost_td_dzn').innerHTML="Prd. Cost / 4 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 4 Dzn";
				document.getElementById('commission_dzn').innerHTML="Commn/4 Dzn";
				document.getElementById('price_with_comm_dzn_td').innerHTML="Price with Commn / 4 Dzn";
			}
		}
		if(type=="change_caption_pcs"){
			if(value==1){
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/ Pcs ";
				document.getElementById('confirm_price_td_set_pcs').innerHTML="Price Before Comn/ Pcs";
				document.getElementById('asking_profit_td_pcs').innerHTML="Asking Profit/Pcs";
				document.getElementById('asking_quoted_price_psc_set').innerHTML="Asking Quoted Price/ Pcs";
				document.getElementById('first_quoted_price_psc_set').innerHTML="1st Quoted Price/ Pcs";
				document.getElementById('revised_quoted_price_psc_set').innerHTML="Revised Price/ Pcs";
				document.getElementById('price_with_comm_pcs_td').innerHTML="Price with Commn/Pcs";
			}
			if(value==58){
				document.getElementById('final_cost_td_pcs_set').innerHTML="Final Cost/ Set ";
				document.getElementById('confirm_price_td_set_pcs').innerHTML="Price Before Comn/ Set";
				document.getElementById('asking_profit_td_pcs').innerHTML="Asking Profit/ Set";
				document.getElementById('asking_quoted_price_psc_set').innerHTML="Asking Quoted Price/ Set";
				document.getElementById('first_quoted_price_psc_set').innerHTML="1st Quoted Price/ Pcs";
				document.getElementById('revised_quoted_price_psc_set').innerHTML="Revised Price/ Set";
				document.getElementById('price_with_comm_pcs_td').innerHTML="Price with Commn/ Set";
			}
		}
		fnc_calculate_dep_oper_interest_income();
	}

	function calculate_cm_cost_with_method()
	{
		var cm_cost=0;
		var cbo_company_name=(document.getElementById('cbo_company_name').value)*1;
		var cbo_location_id=(document.getElementById('cbo_location_id').value)*1;
		var cm_cost_predefined_method_id=document.getElementById('cm_cost_predefined_method_id').value;
		//alert(cm_cost_predefined_method_id)
		var txt_sew_smv=document.getElementById('txt_sew_smv').value*1;
		var txt_cut_smv=document.getElementById('txt_cut_smv').value*1;
		var txt_sew_efficiency_per=document.getElementById('txt_sew_efficiency_per').value*1;
		var txt_cut_efficiency_per=document.getElementById('txt_cut_efficiency_per').value*1;
		var cbo_currercy= document.getElementById('cbo_currercy').value;
		var txt_exchange_rate= document.getElementById('txt_exchange_rate').value*1;
		var txt_machine_line= document.getElementById('txt_machine_line').value;
		var txt_prod_line_hr= document.getElementById('txt_prod_line_hr').value;
		var cbo_costing_per= document.getElementById('cbo_costing_per').value;
		var txt_quotation_date= document.getElementById('txt_quotation_date').value;

		var cbo_costing_per_value=0;
		if(cbo_costing_per==1) cbo_costing_per_value=12;
		if(cbo_costing_per==2) cbo_costing_per_value=1;
		if(cbo_costing_per==3) cbo_costing_per_value=24;
		if(cbo_costing_per==4) cbo_costing_per_value=36;
		if(cbo_costing_per==5) cbo_costing_per_value=48;

		var cpm=return_global_ajax_value(cbo_company_name+"_"+txt_quotation_date+"_"+cbo_location_id, 'cost_per_minute', '', 'requires/quotation_entry_controller_v2');
		$("#cost_per_minute").val(cpm);
		var data=cpm.split("_");
		if(cm_cost_predefined_method_id==1){
			if(data[3]==0 || data[3]=="" ){
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			var txt_efficiency_wastage=100-txt_sew_efficiency_per;
			document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
			var cm_cost=(txt_sew_smv*data[3]*cbo_costing_per_value)+((txt_sew_smv*data[3]*cbo_costing_per_value)*(txt_efficiency_wastage/100));
			cm_cost=cm_cost/txt_exchange_rate;
		}
		else if(cm_cost_predefined_method_id==2){
			if(data[3]==0 ||data[3]=="" ){
				alert("Insert Cost Per Minute in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			var cut_per=txt_cut_efficiency_per/100;
			var sew_per=txt_sew_efficiency_per/100;
			
			var cu=(txt_cut_smv*data[3]*cbo_costing_per_value)/cut_per;
			var cu=number_format_common(cu,1,0,cbo_currercy);
			var su=(txt_sew_smv*data[3]*cbo_costing_per_value)/sew_per;
			var su=number_format_common(su,1,0,cbo_currercy);
			var cm_cost=((cu*1)+(su*1))/(txt_exchange_rate*1);
			//alert(cu+'--'+cm_cost+'--'+data[3]+'--'+cbo_costing_per_value+'--'+cut_per)
		}
		else if(cm_cost_predefined_method_id==3){
			if(cbo_currercy==0 || cbo_currercy==""){
				alert("Insert Currency");
				return;
			}
			if(txt_exchange_rate==0 || txt_exchange_rate==""){
				alert("Insert Exchange Rate");
				return;
			}
			if(txt_machine_line==0 || txt_machine_line==""){
				alert("Insert Machine/Line");
				return;
			}
			if(txt_prod_line_hr==0 || txt_prod_line_hr==""){
				alert("Insert Prod/Line/Hr");
				return;
			}
			if(cbo_costing_per==0 || cbo_costing_per==""){
				alert("Insert Costing Per");
				return;
			}
			if(data[0]==0){
				alert("Insert Monthly CM Expense in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			if(data[1]==0){
				alert("Insert No. of Factory Machine  in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			if(data[2]==0){
				alert("Insert Working Hour in Library>Merchandising Details>Financial Parameter Setup");
				return;
			}
			var per_day_cost=data[0]/26;
			var per_machine_cost=per_day_cost/data[1];
			var per_line_cost=per_machine_cost*txt_machine_line;
			var total_production_per_line=txt_prod_line_hr*data[2];
			var per_product_cost=per_line_cost/total_production_per_line;
			if(cbo_costing_per==1){ var cm_cost=(per_product_cost*12)/txt_exchange_rate; }
			if(cbo_costing_per==2){ var cm_cost=(per_product_cost*1)/txt_exchange_rate; }
			if(cbo_costing_per==3){ var cm_cost=(per_product_cost*24)/txt_exchange_rate; }
			if(cbo_costing_per==4){ var cm_cost=(per_product_cost*36)/txt_exchange_rate; }
			if(cbo_costing_per==5){ var cm_cost=(per_product_cost*48)/txt_exchange_rate; }
		}
		else if(cm_cost_predefined_method_id==4)
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
		//alert(cm_cost_predefined_method_id+'--'+cm_cost+'--'+cbo_currercy)
		if(cm_cost_predefined_method_id!=0)
		{
			if(cm_cost!=Infinity){
				document.getElementById('txt_cm_pre_cost').value=number_format_common(cm_cost,1,0,cbo_currercy);
				calculate_main_total();
			}
		}
		fnc_calculate_dep_oper_interest_income();
	}

	function fnc_calculate_dep_oper_interest_income()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;

		var txt_final_price_dzn_pre_cost=document.getElementById('txt_with_commission_pre_cost_dzn').value*1;
		var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
		var fob_value=txt_final_price_dzn_pre_cost-txt_commission_pre_cost;
		var depreciation_amortization_per=document.getElementById('cost_per_minute').value;
		var data=depreciation_amortization_per.split('_');
		var data_value_depreciation=data[4];
		var data_value_oparating=data[5];
		var data_value_interest=data[6];
		var data_value_income=data[7];
		if(data_value_depreciation=="") data_value_depreciation=0;
		if(data_value_oparating=="") data_value_oparating=0;
		if(data_value_interest=="") data_value_interest=0;
		if(data_value_income=="") data_value_income=0;

		var depreciation_amortization_value=(fob_value*data_value_depreciation)/100;
		var oparating_value=(fob_value*data_value_oparating)/100;
		var interest_value=(fob_value*data_value_interest)/100;
		var income_value=(fob_value*data_value_income)/100;
		//alert(depreciation_amortization_value+'_'+oparating_value+'_'+interest_value+'_'+income_value);

		if(number_format_common(depreciation_amortization_value,1,0,currency)>0) document.getElementById('txt_depr_amor_pre_cost').readOnly=true;
		else document.getElementById('txt_depr_amor_pre_cost').readOnly=false;

		if(number_format_common(oparating_value,1,0,currency)>0) document.getElementById('txt_common_oh_pre_cost').readOnly=true;
		else document.getElementById('txt_common_oh_pre_cost').readOnly=false;

		if(number_format_common(interest_value,1,0,currency)>0) document.getElementById('txt_interest_pre_cost').readOnly=true;
		else document.getElementById('txt_interest_pre_cost').readOnly=false;

		if(number_format_common(income_value,1,0,currency)>0) document.getElementById('txt_income_tax_pre_cost').readOnly=true;
		else document.getElementById('txt_income_tax_pre_cost').readOnly=false;

		document.getElementById('txt_depr_amor_pre_cost').value=number_format_common(depreciation_amortization_value,1,0,currency);
		document.getElementById('txt_common_oh_pre_cost').value=number_format_common(oparating_value,1,0,currency);
		$("#txt_interest_pre_cost").val( number_format_common(interest_value,1,0,currency) );
		$("#txt_income_tax_pre_cost").val( number_format_common(income_value,1,0,currency) );

		//alert(fob_value);
		calculate_main_total();
	}

	function calculate_main_total()
	{
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var dblTot_fa=(document.getElementById('txt_fabric_pre_cost').value*1)+(document.getElementById('txt_trim_pre_cost').value*1)+(document.getElementById('txt_embel_pre_cost').value*1)+(document.getElementById('txt_wash_pre_cost').value*1)+(document.getElementById('txt_comml_pre_cost').value*1)+(document.getElementById('txt_lab_test_pre_cost').value*1)+(document.getElementById('txt_inspection_pre_cost').value*1)+(document.getElementById('txt_cm_pre_cost').value*1)+(document.getElementById('txt_freight_pre_cost').value*1)+(document.getElementById('txt_currier_pre_cost').value*1)+(document.getElementById('txt_certificate_pre_cost').value*1)+(document.getElementById('txt_common_oh_pre_cost').value*1)+(document.getElementById('txt_depr_amor_pre_cost').value*1)+(document.getElementById('txt_interest_pre_cost').value*1)+(document.getElementById('txt_income_tax_pre_cost').value*1)+(document.getElementById('txt_design_pre_cost').value*1)+(document.getElementById('txt_studio_pre_cost').value*1);
		
		document.getElementById('txt_total_pre_cost').value=number_format_common(dblTot_fa, 1, 0, currency);
		document.getElementById('txt_cost_dzn').value=number_format_common(dblTot_fa, 1, 0, currency);
		calculate_final_cost_pcs();
		calculate_asking_quoted_price();
		clculate_margin_dzn();
		calculate_percent_on_po_price();
	}

	function calculate_final_cost_pcs()
	{
		var update_id=document.getElementById('update_id').value;
		var company_id=document.getElementById('cbo_company_name').value;
		var currency=(document.getElementById('cbo_currercy').value)*1;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var txt_total_pre_cost=(document.getElementById('txt_total_pre_cost').value)*1
		var txt_quotation_date=document.getElementById('txt_quotation_date').value
		var final_cost_psc=0;
		if(cbo_costing_per==1) final_cost_psc=txt_total_pre_cost/12;
		else if(cbo_costing_per==2) final_cost_psc=txt_total_pre_cost/1;
		else if(cbo_costing_per==3) final_cost_psc=txt_total_pre_cost/(2*12);
		else if(cbo_costing_per==4) final_cost_psc=txt_total_pre_cost/(3*12);
		else if(cbo_costing_per==5) final_cost_psc=txt_total_pre_cost/(4*12);

		document.getElementById('txt_final_cost_pcs_po_price').value=number_format_common(final_cost_psc, 1, 0, currency);
		if(cbo_order_uom==58||cbo_order_uom==57){
			var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1
			document.getElementById('txt_final_cost_set_pcs_rate').value=number_format_common((final_cost_psc/tot_set_qnty), 1, 0, currency);
		}
		if(cbo_order_uom==1){
			document.getElementById('txt_final_cost_set_pcs_rate').value="";
		}
		var asking_profit_percent=0;
		var txt_final_cost_dzn_po_price=document.getElementById('txt_final_cost_dzn_po_price').value;
		if(txt_final_cost_dzn_po_price==0){
			asking_profit_percent=return_global_ajax_value(company_id+"_"+txt_quotation_date, 'asking_profit_percent', '', 'requires/quotation_entry_controller_v2');
			document.getElementById('txt_final_cost_dzn_po_price').value=asking_profit_percent;
		}
		else{
			asking_profit_percent=txt_final_cost_dzn_po_price;
		}
		var margin_method=1-(asking_profit_percent/100);
		var asking_profit=(number_format_common(final_cost_psc, 1, 0, currency)/margin_method)-number_format_common(final_cost_psc, 1, 0, currency);
		document.getElementById('txt_final_cost_dzn_pre_cost').value=number_format_common(asking_profit, 1, 0, currency);
		document.getElementById('asking_profit_td_pcs').innerHTML="Asking Profit ("+asking_profit_percent+"%)";
	}

function calculate_asking_quoted_price(){
	var currency=(document.getElementById('cbo_currercy').value)*1;
	var txt_final_cost_pcs_po_price=(document.getElementById('txt_final_cost_pcs_po_price').value)*1;
	var txt_final_cost_dzn_pre_cost=(document.getElementById('txt_final_cost_dzn_pre_cost').value)*1;
	document.getElementById('txt_asking_quoted_price').value=number_format_common((txt_final_cost_pcs_po_price+txt_final_cost_dzn_pre_cost),1,0,currency)
	if(document.getElementById('update_mode').value==0){
		document.getElementById('txt_1st_quoted_price_pre_cost').value=number_format_common((txt_final_cost_pcs_po_price+txt_final_cost_dzn_pre_cost),1,0,currency);
		document.getElementById('txt_1st_quoted_po_price').value=document.getElementById('txt_final_cost_dzn_po_price').value*1
	}
	calculate_first_quoted_price_pcs('value')
}

function calculate_first_quoted_price_pcs(type){
	var permission = '<? echo $permission; ?>';
	var txt_1st_quoted_po_price=(document.getElementById('txt_1st_quoted_po_price').value)*1
	var asking_profit_percent = (document.getElementById('txt_final_cost_dzn_po_price').value)*1;
	permission=permission.split('_');
	var currency=(document.getElementById('cbo_currercy').value)*1;
	var final_cost_psc=(document.getElementById('txt_final_cost_pcs_po_price').value)*1
	if(type=='percent'){
		var margin_method=1-(txt_1st_quoted_po_price/100);
		var txt_1st_quoted_price_pre_cost=(final_cost_psc/margin_method);
		document.getElementById('txt_1st_quoted_price_pre_cost').value=number_format_common(txt_1st_quoted_price_pre_cost,1,0,currency);
	}
	if(type=='value'){
		var txt_1st_quoted_price_pre_cost=(document.getElementById('txt_1st_quoted_price_pre_cost').value)*1
		var percent=((txt_1st_quoted_price_pre_cost-final_cost_psc)/txt_1st_quoted_price_pre_cost)*100;
		document.getElementById('txt_1st_quoted_po_price').value=number_format_common(percent,1,0,currency);
		$('#txt_1st_quoted_po_price').removeAttr("title").attr("title",number_format_common(percent,1,0,currency));
	}
	calculate_confirm_price_dzn();
}

function calculate_confirm_price_dzn()
{
	var update_id=document.getElementById('update_id').value;
	var currency=(document.getElementById('cbo_currercy').value)*1;
	var cbo_order_uom=document.getElementById('cbo_order_uom').value;
	var cbo_costing_per=document.getElementById('cbo_costing_per').value;
	var txt_confirm_price_pre_cost=(document.getElementById('txt_confirm_price_pre_cost').value)*1;
	if(txt_confirm_price_pre_cost=="" && txt_confirm_price_pre_cost==0){
		var txt_revised_price_pre_cost=(document.getElementById('txt_revised_price_pre_cost').value)*1;
		if(txt_revised_price_pre_cost!="" && txt_revised_price_pre_cost!=0) txt_confirm_price_pre_cost=txt_revised_price_pre_cost;
		else var txt_confirm_price_pre_cost=(document.getElementById('txt_1st_quoted_price_pre_cost').value)*1;
	}
	if(cbo_costing_per==1) var txt_confirm_price_pre_cost_dzn=number_format_common((txt_confirm_price_pre_cost*12), 1, 0, currency);
	if(cbo_costing_per==2) var txt_confirm_price_pre_cost_dzn=number_format_common((txt_confirm_price_pre_cost*1), 1, 0, currency);
	if(cbo_costing_per==3) var txt_confirm_price_pre_cost_dzn=number_format_common((txt_confirm_price_pre_cost*12*2), 1, 0, currency);
	if(cbo_costing_per==4) var txt_confirm_price_pre_cost_dzn=number_format_common((txt_confirm_price_pre_cost*12*3), 1, 0, currency);
	if(cbo_costing_per==5) var txt_confirm_price_pre_cost_dzn=number_format_common((txt_confirm_price_pre_cost*12*4), 1, 0, currency);

	document.getElementById('txt_confirm_price_pre_cost_dzn').value=txt_confirm_price_pre_cost_dzn;
	if(cbo_order_uom==58 || cbo_order_uom==57){
		var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1
		document.getElementById('txt_confirm_price_set_pcs_rate').value=number_format_common((txt_confirm_price_pre_cost/tot_set_qnty), 1, 0, currency);
	}
	if(cbo_order_uom==1){
		document.getElementById('txt_confirm_price_set_pcs_rate').value="";
	}
	clculate_margin_dzn();
	calculate_price_with_commision_dzn()
	calculate_percent_on_po_price();
	//fnc_calculate_dep_oper_interest_income();
}

function calculate_cofirm_price_commision(){
	 var cbo_costing_per=document.getElementById('cbo_costing_per').value;
	 var update_id=document.getElementById('update_id').value;
	 var currency=(document.getElementById('cbo_currercy').value)*1;
	 var txt_confirm_price_pre_cost_dzn=document.getElementById('txt_confirm_price_pre_cost_dzn').value;
	 var cofirm_price_commision=return_global_ajax_value(update_id+"_"+cbo_costing_per+"_"+txt_confirm_price_pre_cost_dzn, 'cofirm_price_commision', '', 'requires/quotation_entry_controller_v2');
	document.getElementById('txt_commission_pre_cost').value=number_format_common(cofirm_price_commision,1,0,currency);
}

function clculate_margin_dzn(){
	var currency=document.getElementById('cbo_currercy').value
	var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_confirm_price_pre_cost_dzn').value)*1;
	var txt_total_pre_cost=(document.getElementById('txt_total_pre_cost').value)*1;
	document.getElementById('txt_margin_dzn_pre_cost').value=number_format_common((txt_confirm_price_pre_cost_dzn-txt_total_pre_cost), 1, 0, currency);
}

function calculate_percent_on_po_price(){
	var cbo_costing_per_value=0;
	var cbo_costing_per=document.getElementById('cbo_costing_per').value;
	if(cbo_costing_per==1) cbo_costing_per_value=1*12;
	if(cbo_costing_per==2) cbo_costing_per_value=1*1;
	if(cbo_costing_per==3) cbo_costing_per_value=2*12;
	if(cbo_costing_per==4) cbo_costing_per_value=3*12;
	if(cbo_costing_per==5) cbo_costing_per_value=4*12;

	var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_confirm_price_pre_cost_dzn').value)*1;
	document.getElementById('txt_fabric_po_price').value=number_format_common(((((document.getElementById('txt_fabric_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_trim_po_price').value=number_format_common(((((document.getElementById('txt_trim_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_embel_po_price').value=number_format_common(((((document.getElementById('txt_embel_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);

	document.getElementById('txt_wash_po_price').value=number_format_common(((((document.getElementById('txt_wash_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_comml_po_price').value=number_format_common(((((document.getElementById('txt_comml_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_lab_test_po_price').value=number_format_common(((((document.getElementById('txt_lab_test_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_inspection_po_price').value=number_format_common(((((document.getElementById('txt_inspection_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_cm_po_price').value=number_format_common(((((document.getElementById('txt_cm_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_freight_po_price').value=number_format_common(((((document.getElementById('txt_freight_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);

	document.getElementById('txt_currier_po_price').value=number_format_common(((((document.getElementById('txt_currier_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);

	document.getElementById('txt_certificate_po_price').value=number_format_common(((((document.getElementById('txt_certificate_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_design_po_price').value=number_format_common(((((document.getElementById('txt_design_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_studio_po_price').value=number_format_common(((((document.getElementById('txt_studio_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);

	document.getElementById('txt_common_oh_po_price').value=number_format_common(((((document.getElementById('txt_common_oh_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);

	document.getElementById('txt_depr_amor_po_price').value=number_format_common(((((document.getElementById('txt_depr_amor_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_interest_po_price').value=number_format_common(((((document.getElementById('txt_interest_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_income_tax_po_price').value=number_format_common(((((document.getElementById('txt_income_tax_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);

	document.getElementById('txt_total_po_price').value=number_format_common(((((document.getElementById('txt_total_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_cost_dzn_po_price').value=number_format_common(((((document.getElementById('txt_total_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_commission_po_price').value=number_format_common(((((document.getElementById('txt_commission_pre_cost').value)*1)/(txt_confirm_price_pre_cost_dzn+(document.getElementById('txt_commission_pre_cost').value)*1))*100), 7, 0);
	document.getElementById('txt_asking_quoted_po_price').value=number_format_common(((((document.getElementById('txt_asking_quoted_price').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_confirm_price_po_price_dzn').value=number_format_common(((((document.getElementById('txt_confirm_price_pre_cost_dzn').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
	document.getElementById('txt_margin_dzn_po_price').value=number_format_common(((((document.getElementById('txt_margin_dzn_pre_cost').value)*1)/txt_confirm_price_pre_cost_dzn)*100), 7, 0);
}

function calculate_price_with_commision_dzn(){
	var cbo_costing_per=document.getElementById('cbo_costing_per').value;
	var currency=document.getElementById('cbo_currercy').value
	var txt_confirm_price_pre_cost_dzn= document.getElementById('txt_confirm_price_pre_cost_dzn').value*1;
	var txt_commission_pre_cost= document.getElementById('txt_commission_pre_cost').value*1;
	var price_with_commision_dzn=number_format_common((txt_confirm_price_pre_cost_dzn+txt_commission_pre_cost),1,0,currency)
	document.getElementById('txt_with_commission_pre_cost_dzn').value=price_with_commision_dzn;
	var price_with_commision_pcs=0;
	if(cbo_costing_per==1) price_with_commision_pcs=number_format_common((price_with_commision_dzn/12), 1, 0, currency);
	if(cbo_costing_per==2) price_with_commision_pcs=number_format_common((price_with_commision_dzn/1), 1, 0, currency);
	if(cbo_costing_per==3) price_with_commision_pcs=number_format_common((price_with_commision_dzn/24), 1, 0, currency);
	if(cbo_costing_per==4) price_with_commision_pcs=number_format_common((price_with_commision_dzn/36), 1, 0, currency);
	if(cbo_costing_per==5) price_with_commision_pcs=number_format_common((price_with_commision_dzn/48), 1, 0, currency);

	document.getElementById('txt_with_commission_pre_cost_pcs').value=price_with_commision_pcs;
}

function fnc_quotation_entry_dtls( operation )
{
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	if(operation==2){
		alert("Delete Restricted")
		return;
	}
	if (form_validation('cbo_company_name','Company Name')==false){
		return;
	}

	var quotation_id=$('#txt_quotation_id').val();
	if(quotation_id=="")
	{
		alert('Style Ref is Not Save. Please Save Master Part 1st.');
		return;
	}

	var currency=document.getElementById('cbo_currercy').value
	var update_id=document.getElementById('update_id').value;
	var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
	var cofirm_price_commision=return_global_ajax_value(update_id, 'txt_commission_pre_cost', '', 'requires/quotation_entry_controller_v2');
	var cofirm_price_commision_4disit=number_format_common(cofirm_price_commision,1,0,currency)*1;
	if(cofirm_price_commision_4disit !=txt_commission_pre_cost){
		$('#txt_commission_pre_cost').focus();
		recalculate_commision_cost()
	}
	var txt_confirm_price_pre_cost=document.getElementById('txt_confirm_price_pre_cost').value*1
	var txt_confirm_date_pre_cost=document.getElementById('txt_confirm_date_pre_cost').value
	if (txt_confirm_price_pre_cost >0 && form_validation('txt_confirm_date_pre_cost','Confirm Date')==false){
		alert(1)
		return;
	}
	if (txt_confirm_date_pre_cost !="" && form_validation('txt_confirm_price_pre_cost','Price Before Comn')==false){
		alert(2)
		return;
	}
	if(txt_confirm_price_pre_cost =="" && txt_confirm_date_pre_cost!=""){
		alert("Insert Confirm Price");
		return;
	}
	else{
		var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('cbo_company_name*txt_quotation_date*update_id*cbo_costing_per*cbo_order_uom*tot_set_qnty*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_income_tax_pre_cost*txt_income_tax_po_price*txt_total_pre_cost*txt_total_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_final_cost_dzn_pre_cost*txt_final_cost_dzn_po_price*txt_final_cost_pcs_po_price*txt_final_cost_set_pcs_rate*txt_1st_quoted_price_pre_cost*txt_1st_quoted_po_price*txt_first_quoted_price_date*txt_revised_price_pre_cost*txt_revised_price_date*txt_confirm_price_pre_cost*txt_confirm_price_set_pcs_rate*txt_confirm_price_pre_cost_dzn*txt_confirm_price_po_price_dzn*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_confirm_date_pre_cost*txt_asking_quoted_price*txt_asking_quoted_po_price*txt_with_commission_pre_cost_dzn*txt_with_commission_po_price_dzn*txt_with_commission_pre_cost_pcs*txt_with_commission_po_price_pcs*txt_terget_qty*txt_studio_pre_cost*txt_design_pre_cost*txt_studio_po_price*txt_design_po_price',"../../");
		freeze_window(operation);
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_quotation_entry_dtls_reponse;
	}
}

function fnc_quotation_entry_dtls_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		if(reponse[0]==0 || reponse[0]==1)
		{
			$('#hidd_is_dtls_open').val(1);
			document.getElementById('update_id_dtls').value  = reponse[2];
			set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
		}
		summary();
		release_freezing();

	}
}

function fnc_quotation_entry_component()
{
	var data_coponent="";

	var data_coponent=$('#update_id_dtls').val()+"_"+$('#txt_fabric_pre_cost').val()+"_"+$('#txt_fabric_po_price').val()+"_"+$('#txt_trim_pre_cost').val()+"_"+$('#txt_trim_po_price').val()+"_"+$('#txt_embel_pre_cost').val()+"_"+$('#txt_embel_po_price').val()+"_"+$('#txt_wash_pre_cost').val()+"_"+$('#txt_wash_po_price').val()+"_"+$('#txt_comml_pre_cost').val()+"_"+$('#txt_comml_po_price').val()+"_"+$('#txt_lab_test_pre_cost').val()+"_"+$('#txt_lab_test_po_price').val()+"_"+$('#txt_inspection_pre_cost').val()+"_"+$('#txt_inspection_po_price').val()+"_"+$('#txt_cm_pre_cost').val()+"_"+$('#txt_cm_po_price').val()+"_"+$('#txt_freight_pre_cost').val()+"_"+$('#txt_freight_po_price').val()+"_"+$('#txt_currier_pre_cost').val()+"_"+$('#txt_currier_po_price').val()+"_"+$('#txt_certificate_pre_cost').val()+"_"+$('#txt_certificate_po_price').val()+"_"+$('#txt_common_oh_pre_cost').val()+"_"+$('#txt_common_oh_po_price').val()+"_"+$('#txt_depr_amor_pre_cost').val()+"_"+$('#txt_depr_amor_po_price').val()+"_"+$('#txt_interest_pre_cost').val()+"_"+$('#txt_interest_po_price').val()+"_"+$('#txt_income_tax_pre_cost').val()+"_"+$('#txt_income_tax_po_price').val()+"_"+$('#txt_total_pre_cost').val()+"_"+$('#txt_total_po_price').val()+"_"+$('#txt_commission_pre_cost').val()+"_"+$('#txt_commission_po_price').val()+"_"+$('#txt_final_cost_dzn_pre_cost').val()+"_"+$('#txt_final_cost_dzn_po_price').val()+"_"+$('#txt_final_cost_pcs_po_price').val()+"_"+$('#txt_final_cost_set_pcs_rate').val()+"_"+$('#txt_1st_quoted_price_pre_cost').val()+"_"+$('#txt_1st_quoted_po_price').val()+"_"+$('#txt_first_quoted_price_date').val()+"_"+$('#txt_revised_price_pre_cost').val()+"_"+$('#txt_revised_price_date').val()+"_"+$('#txt_confirm_price_pre_cost').val()+"_"+$('#txt_confirm_price_set_pcs_rate').val()+"_"+$('#txt_confirm_price_pre_cost_dzn').val()+"_"+$('#txt_confirm_price_po_price_dzn').val()+"_"+$('#txt_margin_dzn_pre_cost').val()+"_"+$('#txt_margin_dzn_po_price').val()+"_"+$('#txt_confirm_date_pre_cost').val()+"_"+$('#txt_asking_quoted_price').val()+"_"+$('#txt_asking_quoted_po_price').val()+"_"+$('#txt_with_commission_pre_cost_dzn').val()+"_"+$('#txt_with_commission_po_price_dzn').val()+"_"+$('#txt_with_commission_pre_cost_pcs').val()+"_"+$('#txt_with_commission_po_price_pcs').val()+"_"+$('#txt_terget_qty').val()+"_"+$('#txt_studio_pre_cost').val()+"_"+$('#txt_design_pre_cost').val()+"_"+$('#txt_studio_po_price').val()+"_"+$('#txt_design_po_price').val()+"_"+$('#update_id').val()+"_"+$('#cbo_costing_per').val()+"_"+$('#cbo_order_uom').val()+"_"+$('#tot_set_qnty').val()+"_"+$('#txt_quotation_date').val()+"_"+$('#cbo_company_name').val();

	 return data_coponent;
}


function fnc_quotation_entry_dtls1( operation )
{
	if (form_validation('cbo_company_name','Company Name')==false){
		return;
	}
	var update_id=document.getElementById('update_id').value;
	if(operation==2)//operation==1 ||
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/quotation_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	var currency=document.getElementById('cbo_currercy').value
	var update_id=document.getElementById('update_id').value;
	var txt_commission_pre_cost=document.getElementById('txt_commission_pre_cost').value*1;
	var cofirm_price_commision=return_global_ajax_value(update_id, 'txt_commission_pre_cost', '', 'requires/quotation_entry_controller_v2');
	var cofirm_price_commision_4disit=number_format_common(cofirm_price_commision,1,0,currency)*1;
	if(cofirm_price_commision_4disit != txt_commission_pre_cost){
		$('#txt_commission_pre_cost').focus()
		recalculate_commision_cost()
	}
	var txt_confirm_price_pre_cost=document.getElementById('txt_confirm_price_pre_cost').value*1
	var txt_confirm_date_pre_cost=document.getElementById('txt_confirm_date_pre_cost').value;
	if (txt_confirm_price_pre_cost >0 && form_validation('txt_confirm_date_pre_cost','Confirm Date')==false){
		return;
	}
	if (txt_confirm_date_pre_cost !="" && form_validation('txt_confirm_price_pre_cost','Price Before Comn')==false){
		return;
	}
	if(txt_confirm_price_pre_cost =="" && txt_confirm_date_pre_cost!=""){
		alert("Insert Confirm Price");
		return;
	}
	else{
		var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*cbo_currercy*cbo_costing_per*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_fabric_po_price*txt_trim_pre_cost*txt_trim_po_price*txt_embel_pre_cost*txt_embel_po_price*txt_wash_pre_cost*txt_wash_po_price*txt_comml_pre_cost*txt_comml_po_price*txt_lab_test_pre_cost*txt_lab_test_po_price*txt_inspection_pre_cost*txt_inspection_po_price*txt_cm_pre_cost*txt_cm_po_price*txt_freight_pre_cost*txt_freight_po_price*txt_currier_pre_cost*txt_currier_po_price*txt_certificate_pre_cost*txt_certificate_po_price*txt_common_oh_pre_cost*txt_common_oh_po_price*txt_depr_amor_pre_cost*txt_depr_amor_po_price*txt_interest_pre_cost*txt_interest_po_price*txt_income_tax_pre_cost*txt_income_tax_po_price*txt_total_pre_cost*txt_total_po_price*txt_commission_pre_cost*txt_commission_po_price*txt_final_cost_dzn_pre_cost*txt_final_cost_dzn_po_price*txt_final_cost_pcs_po_price*txt_final_cost_set_pcs_rate*txt_1st_quoted_price_pre_cost*txt_1st_quoted_po_price*txt_first_quoted_price_date*txt_revised_price_pre_cost*txt_revised_price_date*txt_confirm_price_pre_cost*txt_confirm_price_set_pcs_rate*txt_confirm_price_pre_cost_dzn*txt_confirm_price_po_price_dzn*txt_margin_dzn_pre_cost*txt_margin_dzn_po_price*txt_confirm_date_pre_cost*txt_asking_quoted_price*txt_asking_quoted_po_price*txt_with_commission_pre_cost_dzn*txt_with_commission_po_price_dzn*txt_with_commission_pre_cost_pcs*txt_with_commission_po_price_pcs*txt_terget_qty*txt_studio_pre_cost*txt_design_pre_cost*txt_studio_po_price*txt_design_po_price',"../../");
		http.onreadystatechange = function(){
			if(http.readyState == 4 && http.status == 200) {
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);
				$('#hidd_is_dtls_open').val(1);
				document.getElementById('update_id_dtls').value  = reponse[2];
				set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				summary()
			}
		}
		http.open("POST","requires/quotation_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
	}
}
// End Dtls Form ------------------------------------------
//created by Bilas-------------------------------------------------------

function summary(){
	var update_id=document.getElementById('update_id').value;
	var cbo_costing_per=document.getElementById('cbo_costing_per').value*1;
	var currency=document.getElementById('cbo_currercy').value*1;
	var txt_asking_quoted_price = document.getElementById('txt_asking_quoted_price').value*1;
	var txt_with_commission_pre_cost_pcs = document.getElementById('txt_with_commission_pre_cost_pcs').value*1;
	var txt_final_cost_pcs_po_price = document.getElementById('txt_final_cost_pcs_po_price').value*1;
	var txt_commission_pre_cost = document.getElementById('txt_commission_pre_cost').value*1;
	var txt_commission_po_price = document.getElementById('txt_commission_po_price').value/100;
	var txt_offer_qnty = document.getElementById('txt_offer_qnty').value*1;
	if(cbo_costing_per==1) cbo_costing_per_value=12;
	if(cbo_costing_per==2) cbo_costing_per_value=1;
	if(cbo_costing_per==3) cbo_costing_per_value=24;
	if(cbo_costing_per==4) cbo_costing_per_value=36;
	if(cbo_costing_per==5) cbo_costing_per_value=48;

	var txt_asking_quoted_price_commision=return_global_ajax_value(update_id+"_"+cbo_costing_per+"_"+txt_asking_quoted_price, 'cofirm_price_commision', '', 'requires/quotation_entry_controller_v2');
	var txt_asking_quoted_price_commision_4=number_format_common(txt_asking_quoted_price_commision,1,0,currency)*1;
	//var txt_expected_1=number_format_common((txt_asking_quoted_price+txt_asking_quoted_price_commision_4),1,0,currency);
	//alert(txt_commission_pre_cost+'=='+txt_asking_quoted_price);
	var txt_expected_1=number_format_common(((txt_commission_pre_cost/12)+txt_asking_quoted_price),1,0,currency);
	document.getElementById('txt_expected_1').value=txt_expected_1;
	document.getElementById('txt_confirmed_1').value=txt_with_commission_pre_cost_pcs;
	document.getElementById('txt_expected_2').value=txt_final_cost_pcs_po_price;
	document.getElementById('txt_confirmed_2').value=txt_final_cost_pcs_po_price;
	var txt_confirmed_3=number_format_common((txt_commission_pre_cost/cbo_costing_per_value),1,0,currency);
	document.getElementById('txt_confirmed_3').value=number_format_common((txt_commission_pre_cost/cbo_costing_per_value),1,0,currency);
	document.getElementById('txt_deviation_2').value=txt_final_cost_pcs_po_price-txt_final_cost_pcs_po_price;
	var confirm_4=txt_with_commission_pre_cost_pcs-txt_final_cost_pcs_po_price-(number_format_common((txt_commission_pre_cost/cbo_costing_per_value),1,0,currency)*1)
	document.getElementById('txt_confirmed_4').value=number_format_common((confirm_4),1,0,currency);
	var txt_deviation_1=txt_with_commission_pre_cost_pcs*1-(number_format_common((txt_asking_quoted_price+txt_asking_quoted_price_commision_4),1,0,currency)*1);
	document.getElementById('txt_deviation_1').value=number_format_common(txt_deviation_1,1,0,currency);
	var txt_expected_3=(number_format_common((txt_asking_quoted_price+txt_asking_quoted_price_commision_4),1,0,currency)*1)*txt_commission_po_price;
	document.getElementById('txt_expected_3').value=number_format_common(txt_expected_3,1,0,currency);
	var txt_expected_4=number_format_common((txt_expected_1*1-txt_final_cost_pcs_po_price*1-txt_expected_3*1),1,0,currency);
	document.getElementById('txt_expected_4').value=txt_expected_4;
	document.getElementById('txt_deviation_3').value=number_format_common((txt_expected_3*1-txt_confirmed_3*1),1,0,currency);
	document.getElementById('txt_deviation_4').value=number_format_common((confirm_4*1-txt_expected_4*1),1,0,currency);
	document.getElementById('txt_expected_5').value=number_format_common(txt_expected_4*cbo_costing_per_value,1,0,currency);
	document.getElementById('txt_confirmed_5').value=number_format_common((confirm_4*cbo_costing_per_value),1,0,currency);
	document.getElementById('txt_deviation_5').value=number_format_common((number_format_common((confirm_4*cbo_costing_per_value),1,0,currency)*1-number_format_common(txt_expected_4*cbo_costing_per_value,1,0,currency)*1),1,0,currency);
	document.getElementById('txt_expected_6').value=number_format_common(txt_expected_4*txt_offer_qnty,1,0,currency);
	document.getElementById('txt_confirmed_6').value=number_format_common((confirm_4*txt_offer_qnty),1,0,currency);
	document.getElementById('txt_deviation_6').value=number_format_common((number_format_common((confirm_4*txt_offer_qnty),1,0,currency)*1-number_format_common( txt_expected_4*txt_offer_qnty,1,0,currency)*1),1,0,currency);
}

function generate_report(type)
{
	if (form_validation('txt_quotation_id','Please Browse The Quotation ID.')==false){
		return;
	}
	else
	{
		if(type=='summary2' || type=='lc_cost_details' )
		{
			if(type=='summary2')
			{
				var rpt_type=5; var comments_head=0;
			}
			else if(type=='lc_cost_details')
			{
				var rpt_type=6; var comments_head=1;
			}
			var report_title="Budget/Cost Sheet";

			var cbo_company_name=$('#cbo_company_name').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
			var txt_style_ref=$('#txt_style_ref').val();
			var txt_style_ref_id=$('#hidd_job_id').val();
			var txt_quotation_id=$('#txt_quotation_id').val();
			var sign=0;
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
		//alert(data);
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
			var zero_val=0;
			if(type=="preCostRpt" || type=="preCostRpt2")
			{
				var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
				if (r==true) zero_val="1"; else zero_val="0";
			}

			var data="action=generate_report&type="+type+"&zero_value="+zero_val+"&"+get_submitted_data_string('txt_quotation_id*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_quotation_date',"../../")+'&path=../../';
			//alert(data);
			http.open("POST","requires/quotation_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_reponse;
		}
	}
}

function fnc_generate_report_reponse(){
	if(http.readyState == 4){
		$('#data_panel').html( http.responseText );
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		/*d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');*/

		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function check_exchange_rate(){
	var cbo_currercy=$('#cbo_currercy').val();
	var quotation_date = $('#txt_quotation_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+quotation_date+"**"+cbo_company_name, 'check_exchange_rate', '', 'requires/quotation_entry_controller_v2');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function get_company_config(company_id){
	get_php_form_data(company_id,'get_company_config','requires/quotation_entry_controller_v2' );
	var cpm=return_global_ajax_value(company_id+"_"+$('#txt_quotation_date').val()+"_"+$('#cbo_location_id').val(), 'cost_per_minute', '', 'requires/quotation_entry_controller_v2');
	$("#cost_per_minute").val(cpm);
}

function get_buyer_config(buyer_id)
{
	load_drop_down( 'requires/quotation_entry_controller_v2', buyer_id+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');
	load_drop_down( 'requires/quotation_entry_controller_v2',buyer_id, 'load_drop_down_buyer_brand', 'brand_td' );
	load_drop_down( 'requires/quotation_entry_controller_v2',buyer_id, 'load_drop_down_buyer_division', 'division_td' );
}

function print_report_button_setting(report_ids)
{
	$("#report_btn").hide();
	$("#report_btn_2").hide();
	$("#report_btn_3").hide();
	$("#report_btn_4").hide();
	$("#report_btn_7").hide();
	$("#report_btn_8").hide();
	$("#report_btn_9").hide();

	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		if(report_id[k]==90) $("#report_btn").show();
		if(report_id[k]==91) $("#report_btn_2").show();
		if(report_id[k]==92) $("#report_btn_3").show();
		if(report_id[k]==219) $("#report_btn_4").show();
		if(report_id[k]==239) $("#report_btn_7").show();
		if(report_id[k]==217) $("#report_btn_8").show();
		if(report_id[k]==275) $("#report_btn_9").show();
	}
}

function openpopup_itemgroup(i)
{
	var page_link="requires/quotation_entry_controller_v2.php?action=openpopup_itemgroup";
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
				//add_break_down_tr_trim_cost(
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

	function openpopup_itemgroup1(i)
	{
		var page_link="requires/quotation_entry_controller_v2.php?action=openpopup_itemgroup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Group Select', 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var id=this.contentDoc.getElementById("gid");
			var name=this.contentDoc.getElementById("gname");
			document.getElementById('cbogroup_'+i).value=id.value;
			document.getElementById('cbogrouptext_'+i).value=name.value;
			$('#cbogrouptext_'+i).removeAttr("title").attr( 'title',name.value );
			set_trim_cons_uom(id.value,i)
			//trims_description_autocomplete(id.value,i)
		}
	}

function check_quatation(){
	var txt_style_ref=$('#txt_style_ref').val();
	var txt_quotation_id=$('#txt_quotation_id').val();
	var response=return_global_ajax_value( txt_style_ref+"**"+txt_quotation_id, 'check_style_ref', '', 'requires/quotation_entry_controller_v2');
	response=trim(response).split('**');
	if(response[0]==1){
		var r=confirm("Following quotation id found against '"+txt_style_ref+"' style ref.\n"+response[1]+". \n If you want to continue press Ok, otherwise press Cancel");
		if(r==false)
		{
			$('#txt_style_ref').val('');
			return;
		}
		else
		{
			//continue;
		}
	}
}

	function open_body_part_popup(i)
	{
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/quotation_entry_controller_v2.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=460px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var id=this.contentDoc.getElementById("gid");
			var name=this.contentDoc.getElementById("gname");
			var type=this.contentDoc.getElementById("gtype");
			document.getElementById('txtbodyparttext_'+i).value=name.value;
			document.getElementById('txtbodypart_'+i).value=id.value;
			document.getElementById('txtbodyparttype_'+i).value=type.value;
		}
	}

	function openmypage_unapprove_request()
	{
		if (form_validation('txt_quotation_id','Quotation ID')==false)
		{
			return;
		}

		var txt_quotation_id=document.getElementById('txt_quotation_id').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		var data=txt_quotation_id+"_"+txt_un_appv_request;
		var title = 'Un Approval Request';
		var page_link = 'requires/quotation_entry_controller_v2.php?data='+data+'&action=unapp_request_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function open_terms_condition_popup(page_link,title)
	{
		var txt_booking_no=document.getElementById('txt_quotation_id').value;
		if (txt_booking_no=="")
		{
			alert("Save The Quotation First");
			return;
		}
		else
		{
			page_link=page_link+get_submitted_data_string('txt_quotation_id','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey();set_auto_complete('price_quation_mst')" >
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
	<div id="content_master_form">
        <table width="1250px" cellpadding="0" cellspacing="2" align="center">
            <tr>
                <td width="77%" align="left" valign="top">  <!--   Form Left Container -->
                    <fieldset style="width:962px;">
                        <legend>Price Quotation</legend>
                        <form name="quotationmst_1" id="quotationmst_1" autocomplete="off">
                            <div style="width:960px;">
                                <table width="100%" cellspacing="2" cellpadding=""  border="0">
                                    <tr>
                                        <td align="right" class="must_entry_caption" colspan="4">Quotation ID</td>
                                        <td colspan="4">
                                        	<input type="text" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:130px;" readonly placeholder="Browse Quotation" onDblClick="openmypage('requires/quotation_entry_controller_v2.php?action=quotation_id_popup','Quotation ID Selection Form')"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100">Inquiry ID</td>
                                        <td width="145">
                                            <input type="text" id="txt_inquery_prifix" name="txt_inquery_prifix" class="text_boxes" style="width:120px;" readonly placeholder="Browse Inquiry" onDblClick="openmypage_inquery()"/>
                                            <input type="hidden" id="txt_inquery_id" name="txt_inquery_id" class="text_boxes" style="width:120px;" placeholder="" />
                                            <input type="hidden" name="set_smv_id" id="set_smv_id" style="width:30px;" class="text_boxes" />
                                        </td>
                                        <td width="100" class="must_entry_caption">Company</td>
                                        <td width="145"><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_company_config(this.value);is_manual_approved(this.value);check_exchange_rate()",0); ?>
                                        </td>
                                        <td width="100">Location</td>
                                        <td width="145" id="location_td"><? echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-Select Location-", $selected, "" ); ?></td>
                                        <td width="100" class="must_entry_caption">Buyer</td>
                                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                                    </tr>
                                    <tr>
                                    	<td class="must_entry_caption">Style Ref</td>
                                        <td>
                                        	<input class="text_boxes" type="text" style="width:120px;" name="txt_style_ref" id="txt_style_ref" maxlength="120" title="Maximum 120 Character" onBlur="check_quatation();" />
                                        	<input type="hidden" id="txt_style_ref_id" name="txt_style_ref_id" value="">
                                        </td>
                                        <td>Style ID</td>
                                        <td><input type="text" name="txt_style_id" id="txt_style_id" class="text_boxes" style="width:120px" maxlength="50" title="" readonly /></td>

                                         <td>Style Desc.</td>
                                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_style_desc" id="txt_style_desc" maxlength="100" title="Maximum 100 Character"/></td>
                                       
						
                                        <td >Buyer Brand</td>
										<td id="brand_td">
											<?php 

												echo create_drop_down( "cbo_buyer_brand_id", 130,  $blank_array,"", "1", "Select Buyer Brand", 0, "",1 );
											 ?>
										</td>

										
                                    </tr>
                                    <tr>
                    	                    <td  >Fashion/Order Type </td>
                    						<td >
                    							<?
                    							

                    								echo create_drop_down( "cbo_level_type_id", 130, "select level_type,id from lib_complexity_level where status_active=1 ORDER BY level_type ASC","id,level_type", "1", "Select", 0, "" ,1);
                    							?>
                    						</td>
                    						
                    						<td  >Design Type</td>
                    						<td >
                    							<?php 
                    								 $design_type=array(1=>"Custom Design",2=>"In House");
                    								echo create_drop_down( "cbo_design_type", 130, $design_type,"", "", "", 1, "",1 );
                    							 ?>
                    						</td>
                    	                   <td  >Style Nick Name</td>
                    						<td>
                    							<input type="text" name="txt_style_short_name" id="txt_style_short_name" class="text_boxes" style="width:120px" maxlength="20" title="Maximum 20 Character" readonly />
                    					   </td>
                    					   <td>Un-App. Request</td>
                    					   <td><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click" ID="txt_un_appv_request" style="width:120px;" onClick="openmypage_unapprove_request();" disabled></td>
                                    </tr>
                                    <tr>
                                    	<td  >Division </td>
										<td  id="division_td">
											
											<?php echo create_drop_down( "txt_division", 130, $blank_array,"", "1", "Select Division", 0, "" ,1); ?>
										</td>
										<td  >Department</td>
										<td id="department_td">
											<?php 

												echo create_drop_down( "cbo_department_id", 130, "select id, department_name from lib_department_name where  status_active=1 and is_deleted=0 order by department_name","id,department_name", "1", "Select Department", 0, "",1 );
											 ?>
										</td>
                                    	<td>Revised No</td>
                                    	<td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_revised_no" id="txt_revised_no"/></td>
                                    	<td>Prod. Dept.</td>
                                    	<td><? echo create_drop_down( "cbo_pord_dept", 70, $product_dept,"", 1, "-Select-",0, "" ); ?>
                                    		<input class="text_boxes" type="text" style="width:43px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" /></td>
                                    </tr>
                                    <tr>
                                    	<td>M. List No</td>
                                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_m_list_no" id="txt_m_list_no" /></td>
                                        <td class="must_entry_caption">Currency</td>
                                        <td><? echo create_drop_down( "cbo_currercy",50, $currency,"", 0, "", 2, "check_exchange_rate();" ,"",""); ?>
                                        	ER. <input class="text_boxes_numeric" type="text" style="width:40px;"   name="txt_exchange_rate" id="txt_exchange_rate" onChange="calculate_cm_cost_with_method();" readonly/>
                                        </td>
                                        <td>Agent</td>
                                        <td id="agent_td"><? echo create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                                        <td>Offer Qty.</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_offer_qnty" id="txt_offer_qnty"/></td>
                                    </tr>
                                    <tr>
                                    	<td>Region</td>
                                        <td><? echo create_drop_down( "cbo_region", 130, $region, 1, "-- Select Region --", 0, "" ); ?></td>
                                        <td>Color Range</td>
                                        <td><? echo create_drop_down( "cbo_color_range", 130, $color_range,"", 1, "-- Select--", 0, "" ); ?></td>
                                        <td>Incoterm</td>
                                        <td><? echo create_drop_down( "cbo_inco_term", 130, $incoterm,"", 0, "",1,"" ); ?></td>
                                        <td>Incoterm Place</td>
                                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_incoterm_place" id="txt_incoterm_place" maxlength="100" title="Maximum 100 Character"/></td>
                                    </tr>
                                    <tr>
                                    	<td>Machine/Line</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_machine_line" id="txt_machine_line" /></td>
                                        <td>Prod/Line/Hr</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_prod_line_hr" id="txt_prod_line_hr" /></td>
                                        <td class="must_entry_caption">Costing Per</td>
                                        <td><? echo create_drop_down( "cbo_costing_per", 130, $costing_per, "",1, "-- Select--", 1, "change_caption_cost_dtls( this.value, 'change_caption_dzn' )","","" ); ?></td>
                                        <td class="must_entry_caption">Quot. Date</td>
                                        <td><input class="datepicker" type="text" style="width:120px;" name="txt_quotation_date" id="txt_quotation_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y",time()); ?>" /></td>
                                    </tr>
                                    <tr>
                                    	<td>OP Date</td>
                                        <td><input class="datepicker" type="text" style="width:120px;" name="txt_op_date" id="txt_op_date" onChange="calculate_lead_time()"/></td>
                                        <td>Est. Ship Date</td>
                                        <td>
                                            <input class="datepicker" type="text" style="width:65px;" name="txt_est_ship_date" id="txt_est_ship_date" onChange="calculate_lead_time();"/>
                                            <input class="text_boxes" type="text" style="width:35px;" name="txt_lead_time" id="txt_lead_time" placeholder="Lead Time" readonly/>
                                        </td>
                                        <td class="must_entry_caption">Order UOM</td>
                                        <td><? echo create_drop_down( "cbo_order_uom",55, $unit_of_measurement, "",0, "", 1, "change_caption_cost_dtls( this.value, 'change_caption_pcs' )","","1,58" ); ?>
                                            <input type="button" id="set_button" class="image_uploader" style="width:70px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value);" />
                                            <input type="hidden" id="set_breck_down" />
                                            <input type="hidden" id="item_id" />
                                            <input type="hidden" id="tot_set_qnty" />
                                        </td>
                                        <td>Factory</td>
                                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_factory" id="txt_factory" maxlength="100" title="Maximum 100 Character"/></td>
                                    </tr>
                                    <tr>
                                        <td>Sew. SMV</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_sew_smv" id="txt_sew_smv" onChange="calculate_cm_cost_with_method();" readonly /></td>
                                        <td>Sew Effi. %</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_sew_efficiency_per" id="txt_sew_efficiency_per" onChange="calculate_cm_cost_with_method();"  value="0"/></td>
                                        <td>Cut. SMV</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_cut_smv" id="txt_cut_smv" onChange="calculate_cm_cost_with_method();" /></td>
                                        <td>Cut Efficiency %</td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_cut_efficiency_per" id="txt_cut_efficiency_per" onChange="calculate_cm_cost_with_method();"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Season</td>
                                        <td id="season_td"><? echo create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                                        <td>Dealing Merchant</td>
                                        <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member--", $selected, "" ); ?></td>
                                        <td>BH Merchant</td>
                                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_bh_marchant" id="txt_bh_marchant" maxlength="500"/></td>
                                        <td>Mkt. No.</td>
                                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_mkt_no" id="txt_mkt_no"/><input class="text_boxes_numeric" type="hidden" name="txt_efficiency_wastage" id="txt_efficiency_wastage" onChange="calculate_cm_cost_with_method();" readonly /></td>
                                        
                                    </tr>
                                    <tr>
                                        <td>Ready To Approve</td>
                                        <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                                        <td>Approved</td>
                                        <td><? echo create_drop_down( "cbo_approved_status", 130, $yes_no,"", 0, "", 2, "",1,"" ); ?></td>
                                        <td>Remarks</td>
                                        <td colspan="3"><input class="text_boxes" type="text" style="width:365px;" name="txt_remarks" id="txt_remarks" maxlength="500"/></td>
                                    </tr>
                                    <tr>
                                    	<td>&nbsp;</td>
                                        <td><input type="button" id="image_button" class="image_uploader" style="width:130px" value="ADD IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_entry', 0 ,1)" />
                                        </td>
                                        <td>&nbsp;</td>
                                        <td><input type="button" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'quotation_entry', 2 ,1)"></td>
                                        <td>&nbsp;</td>
                                        <td><? include("../../terms_condition/terms_condition.php"); terms_condition(314,'txt_quotation_id','../../'); ?></td>
                                        
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                    	<td align="center" height="10" colspan="8" valign="top" id="app_sms" style="font-size:18px; color:#F00">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="right" valign="top" class="button_container" colspan="5">
                                            <input type="hidden" id="cm_cost_predefined_method_id" value="" />
                                            <input type="hidden" id="update_id" value="" />
                                            <input type="hidden" id="hidd_is_dtls_open" value="1" />
                                             <input type="hidden" id="cm_cost_editable" value="" width="50" />
                                            <input type="hidden" id="cost_per_minute" name="cost_per_minute" value="" width="50" />
                                            <? $dd="disable_enable_fields( 'cbo_company_name*cbo_buyer_name*txt_style_ref*txt_revised_no*cbo_pord_dept*txt_style_desc*cbo_currercy*cbo_agent*txt_offer_qnty*cbo_region*cbo_color_range*cbo_inco_term*txt_incoterm_place*txt_machine_line*txt_prod_line_hr*cbo_costing_per*txt_quotation_date*txt_est_ship_date*txt_factory*txt_remarks*garments_nature*cbo_order_uom*set_button*image_button*save1*update1*Delete1*txt_lab_test_pre_cost*txt_inspection_pre_cost*txt_cm_pre_cost*txt_freight_pre_cost*txt_common_oh_pre_cost*txt_1st_quoted_price_pre_cost*txt_first_quoted_price_date*txt_revised_price_pre_cost*txt_revised_price_date*txt_confirm_price_pre_cost*txt_confirm_date_pre_cost*save2*update2*Delete2', 0 )";
                                            echo load_submit_buttons( $permission, "fnc_quotation_entry", 0,0 ,"reset_form('quotationmst_1*quotationdtls_2','cost_container','','cbo_currercy,2*cbo_costing_per,1*cbo_approved_status,2',$dd)",1,1) ; ?>
                                        </td>
                                        <td align="left" valign="top" class="button_container" colspan="5"><input type="button" id="copy_btn" class="formbutton" value="Copy" onClick="copy_quatation(5);" /></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </fieldset>
                </td>
                <td width=".5%" valign="top">&nbsp;</td>
                <td width="22.5%" valign="top">
                    <fieldset>
                        <legend>Summary</legend>
                        <table width="100%" cellspacing="2" cellpadding="0" class="rpt_table" rules="all">
                            <thead>
                                <th width="130">Particulars</th>
                                <th width="50">Asking</th>
                                <th width="50">Confirmed</th>
                                <th>Deviation</th>
                            </thead>
                            <tr>
                                <td>Price With Commn/Pcs </td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_expected_1" id="txt_expected_1" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_confirmed_1" id="txt_confirmed_1" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_deviation_1" id="txt_deviation_1" style="width:40px;" readonly/></td>
                            </tr>
                            <tr>
                                <td>Prod. Cost/Pcs</td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_expected_2" id="txt_expected_2" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_confirmed_2" id="txt_confirmed_2" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_deviation_2" id="txt_deviation_2" style="width:40px;" readonly/></td>
                            </tr>
                            <tr>
                                <td>Commn/Pcs</td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_expected_3" id="txt_expected_3" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_confirmed_3" id="txt_confirmed_3" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_deviation_3" id="txt_deviation_3" style="width:40px;" readonly/></td>
                            </tr>
                            <tr>
                                <td>Margin/Pcs</td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_expected_4" id="txt_expected_4" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_confirmed_4" id="txt_confirmed_4" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_deviation_4" id="txt_deviation_4" style="width:40px;" readonly/></td>
                            </tr>
                            <tr>
                                <td>Margin/Dzn</td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_expected_5" id="txt_expected_5" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_confirmed_5" id="txt_confirmed_5" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_deviation_5" id="txt_deviation_5" style="width:40px;" readonly/></td>
                            </tr>
                            <tr>
                                <td>Margin for Offer Qty</td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_expected_6" id="txt_expected_6" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_confirmed_6" id="txt_confirmed_6" style="width:40px;" readonly/></td>
                                <td><input class="text_boxes_numeric" type="text" name="txt_deviation_6" id="txt_deviation_6" style="width:40px;" readonly/></td>
                            </tr>
                        </table>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
	<!--********************************Master Form End****************************************-->
	<div style="height:10px;"></div>
    <fieldset>
        <form id="quotationdtls_2" autocomplete="off">
            <table width="1750px" cellspacing="2" cellpadding="0" class="rpt_table" rules="all">
                <tr>
                    <thead>
                        <th width="100">Cost Components</th>
                        <th width="50">Fabric Cost</th>
                        <th width="50">Trims Cost&nbsp;<input type="checkbox" id="is_tmplete" name="is_tmplete" onClick="show_sub_form(document.getElementById('update_id').value,'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value+'*'+0);"/></th>
                        <th width="50">Embel. Cost</th>
                        <th width="50">Gmts. Wash</th>
                        <th width="50">Comml. Cost</th>
                        <th width="50">Lab Test</th>
                        <th width="50">Inspection</th>
                        <th width="50">CM Cost</th>
                        <th width="50">Freight</th>
                        <th width="50">Courier Cost</th>
                        <th width="50">Certif. Cost</th>
                        <th width="50">Design Cost</th>
                        <th width="50">Studio Cost</th>
                        <th width="50">Operating Expenses</th>
                        <th width="50">Deprec. & Amort.</th>
                        <th width="50">Interest</th>
                        <th width="50">Income Tax</th>
                        <th width="50">Total Cost</th>
                        <th width="50" id="final_cost_td_pcs_set">Final Cost /Pcs</th>
                        <th width="50" id="asking_profit_td_pcs">Asking Profit /Pcs</th>
                        <th width="50" id="asking_quoted_price_psc_set">Asking Quoted Price /Pcs</th>
                        <th width="100" id="first_quoted_price_psc_set">1st Quoted Price /Pcs</th>
                        <th width="50" id="revised_quoted_price_psc_set">Revised Price /Pcs</th>
                        <th width="50" id="confirm_price_td_set_pcs">Price Before Comn/Pcs</th>
                        <th width="50" id="confirm_price_td_dzn">Price Before Comn /Dzn</th>
                        <th width="50" id="prod_cost_td_dzn">Prd. Cost /Dzn</th>
                        <th width="50" id="margin_dzn">Margin /Dzn</th>
                        <th width="50" id="commission_dzn">Commi./ Dzn</th>
                        <th width="50" id="price_with_comm_dzn_td">Price with Commn /Dzn</th>
                        <th width="50" id="price_with_comm_pcs_td">Price with Commn /Pcs</th>
                        <th width="50">Target price</th>
                        <th>Confirm Date</th>
                    </thead>
                </tr>
                <tr bgcolor="#CCFFFF">
                    <td><strong>Mkt. Costing</strong></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_fabric_pre_cost" id="txt_fabric_pre_cost" style="width:40px;" onFocus=" show_sub_form(document.getElementById('update_id').value,'show_fabric_cost_listview','');" onChange="calculate_main_total();" pre_fab_cost="" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_trim_pre_cost" id="txt_trim_pre_cost" style="width:40px;" onFocus=" show_sub_form(document.getElementById('update_id').value,'show_trim_cost_listview',document.getElementById('cbo_buyer_name').value+'*'+1);" onChange="calculate_main_total();" pre_trim_cost="" readonly/></td>
                    <td><input style="width:40px;" class="text_boxes_numeric" type="text" name="txt_embel_pre_cost" id="txt_embel_pre_cost" onFocus=" show_sub_form(document.getElementById('update_id').value,'show_embellishment_cost_listview','');" onChange="calculate_main_total();" pre_embl_cost="" readonly/></td>
                    <td><input style="width:40px;" class="text_boxes_numeric" type="text" name="txt_wash_pre_cost" id="txt_wash_pre_cost" onFocus=" show_sub_form(document.getElementById('update_id').value,'show_wash_cost_listview','');" onChange="calculate_main_total();" pre_wash_cost="" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_comml_pre_cost" id="txt_comml_pre_cost" style="width:40px;" onFocus=" show_sub_form(document.getElementById('update_id').value,'show_comarcial_cost_listview','');" onChange="calculate_main_total();" pre_comml_cost="" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_lab_test_pre_cost" id="txt_lab_test_pre_cost" style="width:40px;" onChange="calculate_main_total();"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_inspection_pre_cost" id="txt_inspection_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_cm_pre_cost" id="txt_cm_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_freight_pre_cost" id="txt_freight_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_currier_pre_cost" id="txt_currier_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_certificate_pre_cost" id="txt_certificate_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_design_pre_cost" id="txt_design_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_studio_pre_cost" id="txt_studio_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_common_oh_pre_cost" id="txt_common_oh_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_depr_amor_pre_cost" id="txt_depr_amor_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_interest_pre_cost" id="txt_interest_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_income_tax_pre_cost" id="txt_income_tax_pre_cost" style="width:40px;" onChange="calculate_main_total()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_total_pre_cost" id="txt_total_pre_cost" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_final_cost_pcs_po_price" id="txt_final_cost_pcs_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_final_cost_dzn_pre_cost" id="txt_final_cost_dzn_pre_cost" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_asking_quoted_price" id="txt_asking_quoted_price" style="width:40px;" readonly/></td>
                    <td>
                        <input class="text_boxes_numeric" type="text" name="txt_1st_quoted_price_pre_cost" id="txt_1st_quoted_price_pre_cost" onChange="calculate_first_quoted_price_pcs('value')" style="width:17px;" />
                        <input class="text_boxes_numeric" type="text" name="txt_1st_quoted_po_price" id="txt_1st_quoted_po_price" onChange="calculate_first_quoted_price_pcs('percent')" style="width:17px;"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_revised_price_pre_cost" id="txt_revised_price_pre_cost" onChange="calculate_confirm_price_dzn()" style="width:40px;"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_confirm_price_pre_cost" id="txt_confirm_price_pre_cost" style="width:40px;" onChange="calculate_confirm_price_dzn()"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_confirm_price_pre_cost_dzn" id="txt_confirm_price_pre_cost_dzn" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_cost_dzn" id="txt_cost_dzn" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_margin_dzn_pre_cost" id="txt_margin_dzn_pre_cost" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_commission_pre_cost" id="txt_commission_pre_cost" style="width:40px;" onFocus=" show_sub_form(document.getElementById('update_id').value,'show_commission_cost_listview','');" pre_commis_cost="" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_with_commission_pre_cost_dzn" id="txt_with_commission_pre_cost_dzn" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_with_commission_pre_cost_pcs" id="txt_with_commission_pre_cost_pcs" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_terget_qty" id="txt_terget_qty" style="width:40px;"/></td>
                    <td><input class="datepicker" type="text" name="txt_confirm_date_pre_cost" id="txt_confirm_date_pre_cost" style="width:40px;font-size:9px" placeholder="Date"/></td>
                </tr>
                <tr>
                    <td><strong>% To Q.Price</strong></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_fabric_po_price" id="txt_fabric_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_trim_po_price" id="txt_trim_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_embel_po_price" id="txt_embel_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_wash_po_price" id="txt_wash_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_comml_po_price" id="txt_comml_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_lab_test_po_price" id="txt_lab_test_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_inspection_po_price" id="txt_inspection_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_cm_po_price" id="txt_cm_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_freight_po_price" id="txt_freight_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_currier_po_price" id="txt_currier_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_certificate_po_price" id="txt_certificate_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_design_po_price" id="txt_design_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_studio_po_price" id="txt_studio_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_common_oh_po_price" id="txt_common_oh_po_price" style="width:40px;" onChange="calculate_main_total()" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_depr_amor_po_price" id="txt_depr_amor_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_interest_po_price" id="txt_interest_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_income_tax_po_price" id="txt_income_tax_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_total_po_price" id="txt_total_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_final_cost_set_pcs_rate" id="txt_final_cost_set_pcs_rate" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_final_cost_dzn_po_price" id="txt_final_cost_dzn_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text"  name="txt_asking_quoted_po_price" id="txt_asking_quoted_po_price" style="width:40px;" readonly/></td>
                    <td><input class="datepicker" type="text" name="txt_first_quoted_price_date" id="txt_first_quoted_price_date" placeholder="Date" style="width:50px;font-size:8px" /></td>
                    <td><input class="datepicker" type="text" name="txt_revised_price_date" id="txt_revised_price_date" placeholder="Date" style="width:40px;font-size:8px"/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_confirm_price_set_pcs_rate" id="txt_confirm_price_set_pcs_rate" style="width:40px;" readonly /></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_confirm_price_po_price_dzn" id="txt_confirm_price_po_price_dzn" style="width:40px;" readonly /></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_cost_dzn_po_price" id="txt_cost_dzn_po_price" style="width:40px;" readonly /></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_margin_dzn_po_price" id="txt_margin_dzn_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_commission_po_price" id="txt_commission_po_price" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_with_commission_po_price_dzn" id="txt_with_commission_po_price_dzn" style="width:40px;" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" name="txt_with_commission_po_price_pcs" id="txt_with_commission_po_price_pcs" style="width:40px;" readonly/></td>
                    <td>
                        <input type="hidden" id="update_id_dtls" name="update_id_dtls" style="width:18px"/>
                        <input type="hidden" id="update_mode" name="update_mode" style="width:18px" value="0"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="33" valign="top">
						<? echo load_submit_buttons( $permission, "fnc_quotation_entry_dtls", 0,0 ,"reset_form('quotationdtls_2','cost_container','')",2) ; ?>
                        <input type="button" id="report_btn" class="formbutton" value="Quot. Rpt" onClick="generate_report('preCostRpt');" style="display:none;" />
                        <input type="button" id="report_btn_2" class="formbutton" value="Quot. Rpt2" onClick="generate_report('preCostRpt2');" style="display:none;" />
                        <input type="button" id="report_btn_3" class="formbutton" value="Quot. Rpt3" onClick="generate_report('preCostRpt3');" style="display:none;" />
                        <input type="button" id="report_btn_4" class="formbutton" value="Quot. Summary" onClick="generate_report('preCostRpt4');" style="display:none;" />
                        <input type="button" id="report_btn_5" class="formbutton" value="Cost. Sheet Rpt" onClick="generate_report('costingSheetRpt');" style="display:none;" />
                        <input type="button" id="report_btn_6" class="formbutton" value="Buyer Sub. Summary" onClick="generate_report('buyerSubmitSummery');" style="display:inline;" />
                        <input type="button" id="report_btn_7" class="formbutton" value="Summary 2" onClick="generate_report('summary2');" style="display:none;" />
                        <input type="button" id="report_btn_8" class="formbutton" value="LC Cost Dtls" onClick="generate_report('lc_cost_details');" style="display:none;" />
                        <input type="button" id="report_btn_9" class="formbutton" value="Quot. Rpt5" onClick="generate_report('preCostRpt5');" style="display:none;" />


                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <div style="width:100%;">
        <table width="100%" border="0">
            <tr valign="top">
                <td width="100%" valign="top">
                    <div style="width:100%" id="cost_container"></div>
                    <div style="width:100%" id="cost_container_commission"></div>
                </td>
            </tr>
        </table>
    </div>
    <div style="display:none" id="data_panel"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>eval(check_exchange_rate());</script>
</html>