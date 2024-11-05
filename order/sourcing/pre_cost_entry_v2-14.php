<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V2
Converted by             :   
Converted Date           :   
Purpose			         : 	This page Will Create Garments Pre Cost Entry.
Functionality	         :
JS Functions	         :
Created by		         :	Aziz
Creation date 	         : 	12-9-2020
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
Comments		         :  This version  is oracle Compatible and copy from knit gmts
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];
//echo $user_level;
//echo '<pre>';
//print_r($_SESSION['logic_erp']['data_arr'][425]); die;
//echo count($_SESSION['logic_erp']['data_arr'][425]);
/*if(count($_SESSION['logic_erp']['data_arr'][425])>0)
{
	$field_lavel_access = $_SESSION['logic_erp']['data_arr'][425];
	//echo count($_SESSION['logic_erp']['data_arr'][425]);
	$data_arr= json_encode($field_lavel_access);
	echo "var field_level_data= ". $data_arr . ";\n";
	//echo count($_SESSION['logic_erp']['data_arr'][425]);
}*/
//echo count($_SESSION['logic_erp']['data_arr'][425]);
	


echo load_html_head_contents("Sample Info","../../", 1, 1, $unicode,1,'');
?>

<script>
	var permission = '<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var user_level = '<? echo $user_level; ?>';

<? 
if(count($_SESSION['logic_erp']['data_arr'][425])>0)
{
	$field_lavel_access = $_SESSION['logic_erp']['data_arr'][425];
	//echo count($_SESSION['logic_erp']['data_arr'][425]);
	$data_arr= json_encode($field_lavel_access);
	echo "var field_level_data= ". $data_arr . ";\n";
	//echo count($_SESSION['logic_erp']['data_arr'][425]);
}
?>
function toggle( x, origColor ) {
	var newColor = 'pink';
	if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
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
			//document.getElementById('txtconsdzngmts_1').value=exdata[4];
			//document.getElementById('txttrimrate_1').value=exdata[5];
			//document.getElementById('txttrimamount_1').value=exdata[6];
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
			document.getElementById('txtconsdzngmts_1').value='';
			document.getElementById('txttrimrate_1').value='';
			document.getElementById('txttrimamount_1').value='';
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
			//document.getElementById('txtconsdzngmts_'+row_count).value=exdata[4];
			//document.getElementById('txttrimrate_'+row_count).value=exdata[5];
			//document.getElementById('txttrimamount_'+row_count).value=exdata[6];
			document.getElementById('cboapbrequired_'+row_count).value=exdata[7];
		}
		a++;

	}
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
			//alert(select_template_data);
			if(select_template_data != '')
			{
				load_template_data(select_template_data);
			}
        }
    }
}
function calculate_lab_test(i)
{
  	set_sum_value( 'txtratelabtest_sum', 'txtrate_', 'tbl_lab_test_cost' );
}
function add_break_down_tr_lab_test( i )
{
	var row_num=$('#tbl_lab_test_cost tr').length-1;
	if(row_num=='3'){
		alert("You can't take more than three brand names.")
		return false;
	}
	else if (row_num!=i){
		return false;
	}
	else{
		i++;
		$("#tbl_lab_test_cost tr:last").clone().find("input,select").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i;},
				'name': function(_, name) {return name+1;},
				'value': function(_, value) { return value }
			});
		}).end().appendTo("#tbl_lab_test_cost");

		$('#cbobrandname_'+i).val('');
		$('#txtrate_'+i).val('');
 		$('#updateidlabtest_'+i).val('');
 		$('#cbobrandname_'+i).removeAttr("onChange").attr("onChange","check_duplicate_brand("+i+",this.id);");
 		$('#txtrate_'+i).removeAttr("onChange").attr("onChange","calculate_lab_test( "+i+" )");
 		$('#increaselabtest_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_lab_test("+i+");");
		$('#decreaselabtest_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_lab_test_cost');");


		set_sum_value( 'txtratelabtest_sum', 'txtrate_', 'tbl_lab_test_cost' );

	}
}
function check_duplicate_brand(id,td)
{
	var item_id=(document.getElementById('cbobrandname_'+id).value);
	var row_num=$('#tbl_lab_test_cost tr').length-1;
	for (var k=1;k<=row_num; k++)
	{
		if(k==id)
		{
			continue;
		}
		else
		{
			if(item_id==document.getElementById('cbobrandname_'+k).value)
			{
				alert("Brand Name Duplication Not Allowed.");
				document.getElementById(td).value="0";
				document.getElementById("txtrate_"+id).value=" ";
				document.getElementById(td).focus();
			}
		}
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

function fnc_lab_test_dtls( operation )
{
	var update_id=document.getElementById('update_id').value;
 	if(operation==1 || operation==2)
	{
		var is_job_pre_cost=return_global_ajax_value(update_id, 'validate_is_job_pre_cost', '', 'requires/pre_cost_entry_controller_v2');
		var ex_job_pre_cost_data=is_job_pre_cost.split("***");
		if(ex_job_pre_cost_data[0]==1)
		{
			var job_pre_cost_msg="Job Or Budget Found, Delete Restricted.\n Job No : "+ex_job_pre_cost_data[1];
			alert(job_pre_cost_msg);
			return
		}
	}
	freeze_window(operation);
	if(operation==2)
	{
		alert("Delete Restricted")
		release_freezing();
		return;
	}
	if (form_validation('update_id','Company Name')==false)
	{
		release_freezing();
		return;
	}
	var row_num=$('#tbl_lab_test_cost  tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		if (form_validation('cbobrandname_'+i+'*txtrate_'+i,'Brand Name*Rate')==false)
		{
			release_freezing();
			return;
		}


		data_all=data_all+get_submitted_data_string('cbobrandname_'+i+'*txtrate_'+i+'*cbolabteststatus_'+i+'*updateidlabtest_'+i,"../../");
	}
	 var txtratelabtest_sum=$("#txtratelabtest_sum").val();
	 var lab_test_percent= $("#txt_lab_test_po_price").val();
	 var hidd_job_id= $("#hidd_job_id").val();
	var data="action=save_update_delete_lab_test&operation="+operation+'&total_row='+row_num+'&txtratelabtest_sum='+txtratelabtest_sum+'&lab_test_percent='+lab_test_percent+'&update_id='+update_id+'&hidd_job_id='+hidd_job_id+data_all;
 	http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_lab_test_dtls_reponse;
}

function fnc_lab_test_dtls_reponse(){
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText).split('**');
 		if(trim(reponse[0])=='approved')
		{
			alert("This Costing is Approved");
			release_freezing();
			return;
		}
		if(reponse[0]==15)
		{
			setTimeout('fnc_lab_test_dtls('+ reponse[1]+')',4000);
		}
		else
		{
			if (reponse[0].length>2) reponse[0]=10;
			var update_id=$("#update_id").val();
			//alert(update_id);release_freezing();
  			show_sub_form(update_id,'show_lab_test_listview','');
   			//tot_tbl_trims_cost();
 			if(reponse[0]==0 || reponse[0]==1)
			{

  				fnc_quotation_entry_dtls1( 1 );
  				release_freezing();


			}

		}
	}
}

<?
//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][425] );
//echo "var field_level_data= ". $data_arr . ";\n";
?>
var str_construction = [ <? echo substr(return_library_autocomplete("select construction from wo_pri_quo_fabric_cost_dtls group by construction ", "construction" ), 0, -1); ?> ];
var str_composition = [ <? echo substr(return_library_autocomplete("select composition from wo_pri_quo_fabric_cost_dtls group by composition", "composition" ), 0, -1); ?> ];
var str_incoterm_place = [ <? echo substr(return_library_autocomplete("select incoterm_place from  wo_price_quotation group by incoterm_place", "incoterm_place" ), 0, -1); ?> ];
var str_trimdescription = [ <? echo substr(return_library_autocomplete("select description from  wo_pre_cost_trim_cost_dtls group by description", "description" ), 0, -1); ?> ];

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

				if(updateid !="" && permission_array[2]==1){

					var is_booking=return_global_ajax_value(updateid+"__1", 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");
					if(ex_booking_data[0]==1)
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
		else if(table_id=='tbl_lab_test_cost')
		{
 			var permission_array=permission.split("_");
			var updateid=$('#updateidlabtest_'+rowNo).val();
			var r=confirm("Are you sure?")
			if(r==true)
			{
				var index=rowNo-1
				$("table#tbl_lab_test_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_lab_test_cost tbody tr').length;
				for(i = rowNo;i <= numRow;i++){
					$("#tbl_lab_test_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'value': function(_, value) { return value }
						});
						$('#tbl_lab_test_cost tr:eq('+i+') td:eq(4)').attr('id','tdsupplier_'+i);

						$('#increaselabtest_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_lab_test("+i+");");
						$('#decreaselabtest_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_lab_test_cost');");
 						$('#txtrate_'+i).removeAttr("onChange").attr("onChange","calculate_lab_test( "+i+" )");
 					})
				}
				set_sum_value( 'txtratelabtest_sum', 'txtrate_', 'tbl_lab_test_cost' );

				if(updateid !="" && permission_array[2]==1)
				{
 					var data_component=fnc_quotation_entry_component();

					var booking=return_global_ajax_value(updateid+'***'+data_component, 'delete_row_trim_cost', '', 'requires/quotation_entry_controller');
				}
			}
			else
			{
				return;
			}

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

						  $('#increaseyarn_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_yarn_cost("+i+");");
						  $('#decreaseyarn_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_yarn_cost');");
						  $('#percentone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'percent_one');");
						  $('#cbocompone_'+i).removeAttr("onChange").attr("onChange","control_composition("+i+",this.id,'comp_one');");
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
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__3", 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");
					if(ex_booking_data[0]==1)
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
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__2", 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");
					if(ex_booking_data[0]==1)
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
						$('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+" )");
						$('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup("+i+" )");
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
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__6", 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");
					if(ex_booking_data[0]==1)
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
				if(updateid !="" && permission_array[2]==1)
				{
					var is_booking=return_global_ajax_value(updateid+"__6", 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");
					if(ex_booking_data[0]==1)
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
			{reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
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
				if(updateid !="" && permission_array[2]==1)
				{
					var booking=return_global_ajax_value(updateid, 'delete_row_comarcial_cost', '', 'requires/pre_cost_entry_controller_v2');
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

	function show_sub_form(update_id, action)
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

			var trim_cost=$("#txt_trim_pre_cost").val()*1;
			var pre_trim_cost=$("#txt_trim_pre_cost").attr('pre_trim_cost')*1;
            if(trim_cost!=pre_trim_cost)
            {
                alert("Trims Cost Change Found, Please Save or Update.");
                return;
            }
			
			if(action=="show_fabric_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				
				//set_sum_value( 'txtconreqnty_sum', 'txtreqqnty_', 'tbl_conversion_cost' );
				//set_sum_value( 'txtavgconreqnty_sum', 'txtavgreqqnty_', 'tbl_conversion_cost' );

				//set_sum_value( 'txtconchargeunit_sum', 'txtchargeunit_', 'tbl_conversion_cost' );
				//set_sum_value( 'txtconamount_sum', 'txtamountconversion_', 'tbl_conversion_cost' );
				// conversion end
				set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
				set_auto_complete('tbl_fabric_cost');
			}

			if(action=="show_trim_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				
				set_sum_value( 'txtconsdzntrim_sum', 'txtconsdzngmts_', 'tbl_trim_cost');
				set_sum_value( 'txtratetrim_sum', 'txttrimrate_', 'tbl_trim_cost');
				set_sum_value( 'txttrimamount_sum', 'txttrimamount_', 'tbl_trim_cost');
				var str_trimdescription = [ <? //echo substr(return_library_autocomplete("select description from  wo_pre_cost_trim_cost_dtls group by description", "description" ), 0, -1); ?> ];
				set_auto_complete('tbl_trim_cost');
			}

			if(action=="show_embellishment_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				
				set_sum_value( 'txtamountemb_sum', 'txtembamount_', 'tbl_embellishment_cost' );
			}
			if(action=="show_wash_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value('txtamountemb_sum', 'txtembamount_', 'tbl_wash_cost');
			}
			
			/*if(action=="show_lab_test_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratelabtest_sum', 'txtrate_', 'tbl_lab_test_cost' );
	  	    }

			

			if(action=="show_commission_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecommission_sum', 'txtcommissionrate_', 'tbl_commission_cost' );
				set_sum_value( 'txtamountcommission_sum', 'txtcommissionamount_', 'tbl_commission_cost' );
			}

			if(action=="show_comarcial_cost_listview")
			{
				show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_final_price_dzn_pre_cost').value+'_'+document.getElementById('txt_commission_pre_cost').value,action,'cost_container','requires/pre_cost_entry_controller_v2','');
				set_sum_value( 'txtratecomarcial_sum', 'txtcomarcialrate_', 'tbl_comarcial_cost' );
				set_sum_value( 'txtamountcomarcial_sum', 'txtcomarcialamount_', 'tbl_comarcial_cost' );
			}			

			//partial pre cost copy -----------------new development kaiyum-------------------------------------------
			if(action=="partial_pre_cost_copy_action")
			{
				var page_link='requires/pre_cost_entry_controller_v2.php?action=partial_pre_cost_copy_popup';
				var title='Partial Pre Cost Copy';
				var job_no=$("#txt_job_no").val();
				var hidd_job_id=$("#hidd_job_id").val();
				//alert(job_no);
				var txt_job_no=document.getElementById('txt_job_no').value;
				page_link=page_link +"&txt_job_no="+txt_job_no+"&hidd_job_id="+hidd_job_id+"&cons_variable="+extra_str;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=100px,center=1,resize=1,scrolling=0','../')
				emailwindow.onclose=function()
				{
				}
			}*/ //partial pre cost copy -----------------END-------------------------------------------
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
			 //get_php_form_data( id, 'set_php_form_data', '../woven_gmts/requires/size_color_breakdown_controller' );
		});
	}

	function set_sum_value(des_fil_id,field_id,table_id)
	{
		
		if(table_id=='tbl_fabric_cost')
		{
			//alert(field_id); 
			var rowCount = $('#tbl_fabric_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			//var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1;
			//alert(txt_fabric_pre_cost);
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
			sum_yarn_required();
			calculate_main_total();
		}
		/*if(table_id=='tbl_conversion_cost')
		{
			var rowCount = $('#tbl_conversion_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			//document.getElementById('txt_fabric_pre_cost').value=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
			calculate_main_total();
		}
		if(table_id=='tbl_lab_test_cost')
		{
	 		var rowCount = $('#tbl_lab_test_cost  tr').length-1;

	 		var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			document.getElementById('txt_lab_test_pre_cost').value=document.getElementById('txtratelabtest_sum').value;
			calculate_main_total();

		}*/
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
		/*if(table_id=='tbl_commission_cost')
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
		}*/
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
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
			$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
			$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
			$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
			$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
			$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
			$('#txtnominasupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_fabricsupplier("+i+")");
			$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
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
			var sensitivity=$('#cbocolorsizesensitive_'+i).val();
			$('#updateid_'+i).val("");
			$('#processlossmethod_'+i).val("");
			$('#txtfinishconsumption_'+i).val("");
			$('#txtavgprocessloss_'+i).val("");
			/*$('#txtbodypart_'+i).val("");*/
			$('#txtgsmweight_'+i).val("");
			$('#txtgsmweighttype_'+i).val("");
			$('#markerbreackdown_'+i).val("");
			//$('#cbowidthdiatype_'+i).val("");
			$('#prifabcostdtlsid_'+i).val("");
			$('#txtnominasupplier_'+i).val("");
			$('#cbonominasupplier_'+i).val("");
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
			//alert(sensitivity);
			if(sensitivity==3)
			{
				$('#txtcolor_'+i).removeAttr('disabled');
			}
			else
			{
				$('#txtcolor_'+i).attr('disabled',true);
			}
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
			/*$("#libyarncountdeterminationid_"+i).autocomplete({
				source: str_construction
			});
			$("#fabricdescription_"+i).autocomplete({
				source:  str_composition
			});*/
		}
	}

	function open_body_part_popup(i){
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v2.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			var weight_type=this.contentDoc.getElementById("weight_type");
			document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
			document.getElementById('fabricdescription_'+i).value=fab_desctiption.value;
			document.getElementById('fabricdescription_'+i).title=fab_desctiption.value;
			document.getElementById('cbofabricnature_'+i).value=fab_nature_id.value;
			document.getElementById('txtgsmweight_'+i).value=fab_gsm.value;
			document.getElementById('yarnbreackdown_'+i).value=yarn_desctiption.value;
			document.getElementById('construction_'+i).value=construction.value;
			document.getElementById('composition_'+i).value=composition.value;
			if(fab_nature_id.value ==3)
			{
				document.getElementById('txtgsmweighttype_'+i).value=weight_type.value;
			}
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
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
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
		var fabrication_id=document.getElementById('libyarncountdeterminationid_'+trorder).value;
	
	
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
		//var dd= set_session_large_post_data( '&cons_breck_downn='+cons_breck_downn+'&msmnt_breack_downn='+msmnt_breack_downn+'&marker_breack_down='+marker_breack_down,"../../", "save_post_session" ) ;
		var page_link=page_link+'&body_part_id='+body_part_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_company_id='+cbo_company_id+'&cbofabricnature_id='+cbofabricnature_id+'&calculated_conss='+calculated_conss+'&hid_fab_cons_in_quotation_variable='+hid_fab_cons_in_quotation_variable+'&txtgsmweight='+txtgsmweight+'&txt_job_no='+txt_job_no+'&cbogmtsitem='+cbogmtsitem+'&garments_nature='+garments_nature+'&cbo_approved_status='+cbo_approved_status+'&pri_fab_cost_dtls_id='+pri_fab_cost_dtls_id+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved+'&cbofabricsource='+cbofabricsource+'&uom='+uom+'&fid='+fabrication_id+'&cost_control_source='+cost_control_source;
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
//alert(1);

			math_operation( 'txtamount_'+trorder, 'txtconsumption_'+trorder+'*'+'txtrate_'+trorder, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
//alert(2);
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			//sum_yarn_required()
			
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
	if(value==2)
	{
		document.getElementById(td_id).innerHTML="GSM";
	}
	else
	{
		document.getElementById(td_id).innerHTML="Yarn Weight";
	}
}

function control_color_field(i)
{
	var cbocolorsizesensitive = document.getElementById('cbocolorsizesensitive_'+i).value;
	$('#txtcolor_'+i).val('');
	if(cbocolorsizesensitive==0)
	{
		$('#txtcolor_'+i).removeAttr('disabled');
		$('#txtcolor_'+i).removeAttr('onClick');
	}
	if(cbocolorsizesensitive==1)
	{
		$('#txtcolor_'+i).attr('disabled','true')
	}
	if(cbocolorsizesensitive==2)
	{
		$('#txtcolor_'+i).attr('disabled','true')
	}
	if(cbocolorsizesensitive==3)
	{
		$('#txtcolor_'+i).removeAttr('disabled').attr("onClick","open_color_popup("+i+");");
	}
	if(cbocolorsizesensitive==4)
	{
		$('#txtcolor_'+i).attr('disabled','true')
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
	//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
	//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

	//alert(buyer_profit_per+'-'+margin_dzn_percent);

	/*if(buyer_profit_per!=0)
	{
		if(buyer_profit_per>margin_dzn_percent)
		{
			alert("Buyer Profit % is greater-than Margin Dzn %");
			if(user_level!=2)
			{
				//release_freezing();
				//return;
			}
		}
	}*/

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

	var copy_quatation_id=document.getElementById('copy_quatation_id').value;
	var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
	var cost_control_source=document.getElementById('txt_cost_control_source').value;
	if(cost_control_source==4)//Quick Costing [WVN]
	{
		var qc_validate=fnc_budgete_cost_validate();
		//alert(qc_validate);
		var qc_validation=qc_validate.split("__");
		if(qc_validation[1]==1)
		{
			alert(qc_validation[0]);
			release_freezing();
			return;
		}
	}
	else if(cost_control_source==2)//Price Quotation
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
	var data_all="";
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
		if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*txtbodyparttype_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*txtgsmweight_'+i+'*uom_'+i,'Gmts Item *Body Part*Body Part Type*Fabric Nature*Color Type*Construction*Composition*Consunption*Fabric Source*Fabric Weight*UOM')==false)
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
		  $('#txtconsumption_'+i).dblclick();
		  //$('#txtconsumption_'+i).click();
		  release_freezing();
		  return;
		}
		//console.log(1514);
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
		//alert(consbreckdown);
		//var txttconsumption_avglValue=$('#txtconsumption_'+i).attr("avglValue"));
		data_all=data_all+get_submitted_data_string('cbo_company_name*cbo_costing_per*consumptionbasis_'+i+'*update_id*hidd_job_id*cbogmtsitem_'+i+'*txtbodypart_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*construction_'+i+'*composition_'+i+'*fabricdescription_'+i+'*txtgsmweight_'+i+'*txtgsmweighttype_'+i+'*cbocolorsizesensitive_'+i+'*txtcolor_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*cbonominasupplier_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtfinishconsumption_'+i+'*txtavgprocessloss_'+i+'*cbostatus_'+i+'*consbreckdown_'+i+'*msmntbreackdown_'+i+'*updateid_'+i+'*processlossmethod_'+i+'*colorbreackdown_'+i+'*yarnbreackdown_'+i+'*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtwoven_fin_sum*txtknit_fin_sum*txtamount_sum*markerbreackdown_'+i+'*cbowidthdiatype_'+i+'*avg*avgtxtconsumption_'+i+'*avgtxtgsmweight_'+i+'*plancutqty_'+i+'*jobplancutqty_'+i+'*isclickedconsinput_'+i+'*oldlibyarncountdeterminationid_'+i+'*isconspopupupdate_'+i+'*uom_'+i+'*txtbodyparttype_'+i+'*txtwoven_sum_production*txtknit_sum_production*txtwoven_fin_sum_production*txtknit_fin_sum_production*txtwoven_sum_purchase*txtknit_sum_purchase*txtwoven_fin_sum_purchase*txtknit_fin_sum_purchase*txtwoven_amount_sum_purchase*txtkint_amount_sum_purchase*txt_quotation_id*copy_quatation_id',"../../");
	}
	//alert(data_all);
	var data="action=save_update_delet_fabric_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
	//freeze_window(operation);
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
	    if(reponse[0]==10)
		{
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='approved'){
			alert("This Costing is Approved");
			release_freezing();
			return;
		}
		if(reponse[0]=='quataNotApp')
		{
			alert("Quotation is not  Approved. Please Approved the Quotation");
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
			 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
		}
		else
		{
			var company_name=document.getElementById('cbo_company_name').value*1;
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
			//show_hide_content('fabric_cost', '')

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
				 //document.getElementById('txt_comml_pre_cost').value=reponse[3];
				 //document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
				 calculate_main_total()
				fnc_quotation_entry_dtls1(1);
				set_currier_cost_method_variable(company_name);
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

		//document.getElementById('tot_yarn_needed').value=number_format_common(array_sum (yarn), 5, 0);
		//document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(array_sum (yarn), 5, 0);

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


function make_avg_for_same_row(){
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


function sum_yarn_required_avg_old(value)
{
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

		}//for loop end

		//alert(Object.keys(rows));
		//alert(rows[2]);
		if(value=="Make UAVG")
		{
			//alert(value)
		document.getElementById('avg').value="Make AVG";
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

		document.getElementById('txtamount_sum').value=number_format_common(array_sum (woven_fab_purc_amt)+array_sum (knit_fab_purc_amt), 5, 0);

		var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
		document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)

			for(key in rows)
			{
				var cbofabricnature1=document.getElementById('cbofabricnature_'+key).value;
				if(cbofabricnature1==2)
			    {
				document.getElementById('avgtxtconsumption_'+key).value=rows_val[key];
				$('#txtconsumption_'+key).attr("avglValue", rows_val[key]);
				$('#txtconsumption_'+key).attr("title", rows_val[key]);
				}
				if(cbofabricnature1==3)
			    {
				document.getElementById('avgtxtgsmweight_'+key).value=rows_val[key];
				$('#txtgsmweight_'+key).attr("avglValue", rows_val[key]);
				$('#txtgsmweight_'+key).attr("title", rows_val[key]);
				}
			}
		}

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
		calculate_main_total();
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
	//	var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

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
		if(cost_control_source==4)//Quick Costing [WVN]
		{
			var qc_validate=fnc_budgete_cost_validate();
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)//Price Quotation
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
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('update_id*cbocosthead_'+i,'Company Name*Fabric Description')==false)
			{
				release_freezing();
				return;
			}
			//eval(get_submitted_variables('update_id*cbocosthead_'+i+'*cbotypeconversion_'+i+'*txtreqqnty_'+i+'*txtchargeunit_'+i+'*txtamountconversion_'+i+'*cbostatusconversion_'+i+'*updateidcoversion_'+i+'*colorbreakdown_'+i+'*txtconreqnty_sum*txtconchargeunit_sum*txtconamount_sum'));
			data_all=data_all+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*cbocosthead_'+i+'*cbotypeconversion_'+i+'*txtprocessloss_'+i+'*txtreqqnty_'+i+'*txtavgreqqnty_'+i+'*txtchargeunit_'+i+'*txtamountconversion_'+i+'*cbostatusconversion_'+i+'*updateidcoversion_'+i+'*colorbreakdown_'+i+'*coversionchargelibraryid_'+i+'*txtconreqnty_sum*txtconchargeunit_sum*txtconamount_sum',"../../");
		}
		var data="action=save_update_delet_fabric_conversion_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
		if(reponse[0]=='quataNotApp')
		{
			alert("Quotation is not  Approved. Please Approved the Quotation");
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

		  $('#cbogrouptext_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+" )");
		  $('#cbogrouptext_'+i).removeAttr("placeholder").attr("placeholder","Browse");

		  $('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup("+i+" )");
		  $('#txtnominasupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_trimsupplier("+i+")");
		  //$('#cbonominasupplier_'+i).removeAttr("onChange").attr("onChange","set_trim_rate_amount( this.value,"+i+",'supplier_change' )");
		  $('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
		  $('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_trim_cost');");
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
			$('#cbonominasupplier_'+i).removeAttr('disabled','disabled');
			$('#txtconsdzngmts_'+i).removeAttr('disabled','disabled');
			//$('#cboconsuom_'+i).removeAttr('disabled','disabled');
			//$('#txttrimrate_'+i).removeAttr('disabled','disabled');

		  $('#updateidtrim_'+i).val("");
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
				set_sum_value( 'totalqty_sum', 'totalqty_', 'tbl_trim_cost' );
				set_sum_value( 'totalamount_sum', 'totalamount_', 'tbl_trim_cost' );
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
	}
}

function set_session_large_post_data_trim(page_link,title,trorder)
{
		var cbogrouptext=document.getElementById('cbogrouptext_'+trorder).value;
		if(cbogrouptext == "")
		{
			alert("Item group cannot be null");
			return;
		}
		//consbreckdown_
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
		var txtdescription=document.getElementById('txtdescription_'+trorder).value;
		var supplier=document.getElementById('cbonominasupplier_'+trorder).value;
		var excess_per = document.getElementById('excessper_'+trorder).value;
		var ex_cons = document.getElementById('excons_'+trorder).value;
		var total_cons = document.getElementById('totalcons_'+trorder).value;
		var txtconsdzngmts = document.getElementById('txtconsdzngmts_'+trorder).value;
	    var calculator_parameter=return_global_ajax_value(cbogroup, 'calculator_parameter', '', 'requires/pre_cost_entry_controller_v2');
		var page_link=page_link+'&txt_job_no='+txt_job_no+'&cbo_costing_per='+cbo_costing_per+'&calculator_parameter='+trim(calculator_parameter)+'&cons_breck_downn='+cons_breck_downn+'&cbo_approved_status='+cbo_approved_status+'&cbogroup='+cbogroup+'&cboconsuom='+cboconsuom+'&tot_set_qnty='+tot_set_qnty+'&country='+country+'&txttrimrate='+txttrimrate+'&item_id='+item_id+'&calculatorstring='+calculatorstring+"&cbogrouptext="+cbogrouptext+"&updateidtrim="+updateidtrim+"&txtdescription="+txtdescription+"&supplier="+supplier+"&excessper="+excess_per+"&excons="+ex_cons+"&totalcons="+total_cons+"&txtconsdzngmts="+txtconsdzngmts;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title+" "+cbogrouptext, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var calculator_string=this.contentDoc.getElementById("calculator_string");
			var carton_description=this.contentDoc.getElementById("carton_description");


			//var avg_cons=this.contentDoc.getElementById("avg_cons");
			//document.getElementById('txtconsdzngmts_'+trorder).value=avg_cons.value;
			var avg_totcons=this.contentDoc.getElementById("avg_totcons");
			var avg_amount=this.contentDoc.getElementById("avg_amountcons");
			var avg_rate=this.contentDoc.getElementById("avg_ratecons");
			var cbonominasupplier=this.contentDoc.getElementById("cbonominasupplier");
			var txtdescription=this.contentDoc.getElementById("txtdescription");
			var cboconsuom=this.contentDoc.getElementById("cboconsuom");
			document.getElementById('txtconsdzngmts_'+trorder).value=avg_totcons.value;
			document.getElementById('txttrimrate_'+trorder).value=avg_rate.value;
			document.getElementById('txttrimamount_'+trorder).value=avg_amount.value;

			//alert(cbonominasupplier.value);
			document.getElementById('consbreckdown_'+trorder).value=cons_breck_down.value;
			document.getElementById('calculatorstring_'+trorder).value=calculator_string.value;
			if(cbonominasupplier.value != ''){
				document.getElementById('cbonominasupplier_'+trorder).value=cbonominasupplier.value;
			}
			if(txtdescription.value != ''){
				document.getElementById('txtdescription_'+trorder).value=txtdescription.value;
			}
			if(trim(calculator_parameter) ==2 && carton_description.value != '')
			{
				document.getElementById('txtdescription_'+trorder).value=carton_description.value;
			}
			if(cboconsuom.value != ''){
				document.getElementById('cboconsuom_'+trorder).value=cboconsuom.value;
			}
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
		//document.getElementById('cbogroup_'+i).value=id.value;
		//document.getElementById('cbogrouptext_'+i).value=name.value;
		//$('#cbogrouptext_'+i).removeAttr("title").attr( 'title',name.value );
		//set_trim_cons_uom(id.value,i)
		//trims_description_autocomplete(id.value,i)
	}
}

function openpopup_itemgroup1(i)
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
		var id=this.contentDoc.getElementById("gid");
		var name=this.contentDoc.getElementById("itemdata");
		document.getElementById('cbogroup_'+i).value=id.value;
		document.getElementById('cbogrouptext_'+i).value=name.value;
		$('#cbogrouptext_'+i).removeAttr("title").attr( 'title',name.value );
		set_trim_cons_uom(id.value,i)
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
	//var cbo_cons_uom=return_global_ajax_value(trim_group_id, 'set_cons_uom', '', 'requires/pre_cost_entry_controller_v2');
  	//document.getElementById('cboconsuom_'+i).value = trim(cbo_cons_uom);
	var trim_rate_variable=document.getElementById('trim_rate_variable').value;
	var buyer=document.getElementById('cbo_buyer_name').value;

	set_trim_rate_amount( document.getElementById('cbonominasupplier_'+i).value,i,'item_change' );

	//load_drop_down( 'requires/pre_cost_entry_controller_v2', trim_group_id+"_"+i+"_"+trim_rate_variable+"_"+buyer, 'load_drop_down_supplier_rate', 'tdsupplier_'+i );
}

function fncopenpopup_trimsupplier(inc)
{
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
alert(nominasupplier);
	var page_link="requires/pre_cost_entry_controller_v2.php?trim_rate_variable="+trim(trim_rate_variable)+"&action=openpopup_trimsupplier&cbogroup="+cbogroup+"&buyer="+buyer+"&nominasupplier="+nominasupplier;
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

		//set_trim_rate_amount(suppid,inc,'supplier_change');
		if(nominasupplier=='')
		{
			$('#cbonominasupplier_'+inc).val(suppid);
			$('#txtnominasupplier_'+inc).val(suppname);
		}

	}
}
function fncopenpopup_emblsupplier(inc)
{
	var updateidfabric=document.getElementById('embupdateid_'+inc).value
	if(updateidfabric*1>0){
		var booking=return_global_ajax_value(updateidfabric, 'check_emb_booking', '', 'requires/pre_cost_entry_controller_v2');
		if(booking==11)
		{
			alert("Booking Found, Change Not Allowed");
			return;
		}
	}

	//var cbogroup=$('#cbogroup_'+inc).val();
	//var trim_rate_variable=$('#trim_rate_variable').val();
	var buyer=$('#cbo_buyer_name').val();
	var nominasupplier=$('#cboembsupplierid_'+inc).val();

	//var page_link="requires/pre_cost_entry_controller_v2.php?action=openpopup_trimsupplier&cbogroup="+cbogroup+"&buyer="+buyer+"&nominasupplier="+nominasupplier;
	var page_link="requires/pre_cost_entry_controller_v2.php?action=openpopup_emblsupplier&&buyer="+buyer+"&nominasupplier="+nominasupplier;
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

		//set_trim_rate_amount(suppid,inc,'supplier_change');

		$('#cboembsupplierid_'+inc).val(suppid);
		$('#txtembsupplier_'+inc).val(suppname);
	}
}

function fncopenpopup_fabricsupplier(inc)
{
	var updateidfabric=document.getElementById('updateid_'+inc).value
	if(updateidfabric*1>0){
		var booking=return_global_ajax_value(updateidfabric, 'check_fabric_booking', '', 'requires/pre_cost_entry_controller_v2');
		if(booking==11)
		{
			alert("Booking Found, Change Not Allowed");
			return;
		}
	}

	//var cbogroup=$('#cbogroup_'+inc).val();
	//var trim_rate_variable=$('#trim_rate_variable').val();
	var buyer=$('#cbo_buyer_name').val();
	var nominasupplier=$('#cbonominasupplier_'+inc).val();

	//var page_link="requires/pre_cost_entry_controller_v2.php?action=openpopup_trimsupplier&cbogroup="+cbogroup+"&buyer="+buyer+"&nominasupplier="+nominasupplier;
	var page_link="requires/pre_cost_entry_controller_v2.php?action=openpopup_fabricsupplier&&buyer="+buyer+"&nominasupplier="+nominasupplier;
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

		//set_trim_rate_amount(suppid,inc,'supplier_change');

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
		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		/*if(buyer_profit_per!=0)
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
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==4)//Quick Costing [WVN]
		{
			var qc_validate=fnc_budgete_cost_validate();
			//alert(qc_validate);
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)//Price Quotation
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
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('update_id*cbogroup_'+i+'*txtconsdzngmts_'+i,'Company Name*Group*Cons/Unit Gmts')==false)
			{
				release_freezing();
				return;
			}//txtremark_


			data_all=data_all+get_submitted_data_string('cbo_company_name*update_id*hidd_job_id*txt_quotation_id*copy_quatation_id*cbogroup_'+i+'*txtdescription_'+i+'*txtsupref_'+i+'*cboconsuom_'+i+'*txtconsdzngmts_'+i+'*txttrimrate_'+i+'*txttrimamount_'+i+'*cboapbrequired_'+i+'*cbonominasupplier_'+i+'*cbotrimstatus_'+i+'*updateidtrim_'+i+'*consbreckdown_'+i+'*txtremark_'+i+'*country_'+i+'*calculatorstring_'+i+'*txtconsdzntrim_sum*txtratetrim_sum*txttrimamount_sum*seq_'+i+'*excessper_'+i+'*excons_'+i+'*totalcons_'+i+'*cbomaterialsource_'+i,"../../",i);
		}
		var data="action=save_update_delet_trim_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;

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
		if(reponse[0]=='quataNotApp')
		{
			alert("Quotation is not  Approved. Please Approved the Quotation");
			release_freezing();
			return;
		}
		if(reponse[0]==6)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==10){
			show_msg(reponse[0]);
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
			if(reponse[0]==0 || reponse[0]==1) 
				{
					var txt_trim_pre_cost=$('#txttrimamount_sum').val()*1;
					var pre_trimcost=number_format_common(txt_trim_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					$("#txt_trim_pre_cost").attr('pre_trim_cost',pre_trimcost);
					//var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					//var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					//calculate_main_total();					
					fnc_quotation_entry_dtls1(1)
				}
			show_sub_form(document.getElementById('update_id').value, 'show_trim_cost_listview');

			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==10)
			{
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

		 // $('#txtembamount_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr_embellishment_cost("+i+");");
		  //$('#embtypetd_'+i).removeAttr("id").attr("id","'fabriccosttbltr_'+i");
		  $('#increaseemb_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_embellishment_cost("+i+");");
		  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_embellishment_cost');");
		  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");
		  $('#txtembconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data_emb('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_emb', 'Consumtion Entry Form',"+i+")");
		  $('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_emb_cost( "+i+" )");

		  $('#txtembsupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_emblsupplier("+i+")");
		  $('#countrytext_'+i).removeAttr("onDblClick").attr("onDblClick","open_country_popup( "+i+" )");

		  $('#cboembname_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
		  $('#cboembtype_'+i).removeAttr("onChange").attr("onChange","check_duplicate( "+i+" )");
		  $('#txtembconsdzngmts_'+i).val("");
		  $('#txtembrate_'+i).val("");
		  $('#txtembamount_'+i).val("");
		  $('#embupdateid_'+i).val("");
		  $('#cboembname_'+i).val($('#cboembname_'+row_num).val());
		  $('#empbudgeton_'+i).val($('#empbudgeton_'+row_num).val());
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
		//load_drop_down( 'requires/pre_cost_entry_controller_v2', cboembname+'_'+i, 'load_drop_down_embtype', 'embtypetd_'+i );
		get_php_form_data(cboembname+'_'+i+'_'+company_id,'load_drop_down_embtype','requires/pre_cost_entry_controller_v2' );

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

	//set_session_large_post_data_emb('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_emb', 'Consumtion Entry Form','1')

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
		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		/*if(buyer_profit_per!=0)
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
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==4)//Quick Costing [WVN]
		{
			var qc_validate=fnc_budgete_cost_validate();
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)//Price Quotation
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
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
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

			data_all=data_all+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*cboembname_'+i+'*cboembtype_'+i+'*cboembbodypart_'+i+'*country_'+i+'*cboembsupplierid_'+i+'*txtembconsdzngmts_'+i+'*txtembrate_'+i+'*txtembamount_'+i+'*cboembstatus_'+i+'*embupdateid_'+i+'*consbreckdownemb_'+i+'*empbudgeton_'+i+'*txtamountemb_sum',"../../");
		}
		//alert(data_all); return;
		var data="action=save_update_delet_embellishment_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
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
	console.log("zakaria");
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
		  $('#decreaseemb_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_wash_cost');");
		  $('#txtembconsdzngmts_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
		  $('#txtembconsdzngmts_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data_wash('requires/pre_cost_entry_controller_v2.php?action=consumption_popup_wash', 'Consumtion Entry Form',"+i+")");
		 $('#txtembsupplier_'+i).removeAttr("onDblClick").attr("onDblClick","fncopenpopup_emblsupplier("+i+")");
		  $('#txtembrate_'+i).removeAttr("onChange").attr("onChange","calculate_wash_cost( "+i+" )");
		  if(conversion_from_chart==1)
		  {
			  $('#txtembrate_'+i).removeAttr("onClick").attr("onClick","set_wash_charge_unit_pop_up( "+i+" )");
		  }
		  //$('#cboembname_'+i).removeAttr("onChange").attr("onChange","cbotype_loder( "+i+" )");
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
		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		/*if(buyer_profit_per!=0)
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
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==4)//Quick Costing [WVN]
		{
			var qc_validate=fnc_budgete_cost_validate();
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)//Price Quotation
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
		var data_all="";
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

			data_all=data_all+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*txt_quotation_id*copy_quatation_id*cboembname_'+i+'*cboembtype_'+i+'*country_'+i+'*txtembconsdzngmts_'+i+'*txtembrate_'+i+'*txtembamount_'+i+'*cboembsupplierid_'+i+'*cboembstatus_'+i+'*embupdateid_'+i+'*embratelibid_'+i+'*consbreckdownwash_'+i+'*empbudgeton_'+i+'*txtamountemb_sum',"../../");
		}
		var data="action=save_update_delet_wash_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
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
				if(reponse[0]==10){
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
		calculate_depreciation_amortization();
		calculate_oparating_expanseses();
		calculate_deffd_lc();
		calculate_interest_cost();
		calculate_income_tax();
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
	//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
	//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

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
	if(cost_control_source==4)//Quick Costing [WVN]
	{
		var qc_validate=fnc_budgete_cost_validate();
		var qc_validate=qc_validate.split("__");
		if(qc_validate[1]==1)
		{
			alert(qc_validate[0]);
			release_freezing();
			return;
		}
	}
	else if(cost_control_source==2)//Price Quotation
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
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{

		if (form_validation('update_id','Company Name')==false)
		{
			release_freezing();
			return;
		}

		//eval(get_submitted_variables('cbo_company_name*update_id*cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionrate_'+i+'*txtcommissionamount_'+i+'*cbocommissionstatus_'+i+'*commissionupdateid_'+i+'*txtratecommission_sum*txtamountcommission_sum'));
		data_all=data_all+get_submitted_data_string('cbo_company_name*update_id*hidd_job_id*copy_quatation_id*txt_quotation_id*cboparticulars_'+i+'*cbocommissionbase_'+i+'*txtcommissionrate_'+i+'*txtcommissionamount_'+i+'*cbocommissionstatus_'+i+'*commissionupdateid_'+i+'*txtratecommission_sum*txtamountcommission_sum',"../../");
	}
	var data="action=save_update_delet_commission_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
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
		if(reponse[0]=='quataNotApp')
		{
			alert("Quotation is not  Approved. Please Approved the Quotation");
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
				calculate_main_total();
				set_currier_cost_method_variable(company_name);
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
			$('#synchronize_'+i).removeAttr("onClick").attr("onClick","calculate_comarcial_cost("+i+",'cal_amount');");
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
		if(txt_commercial_cost_method==1)
		{
			var sum_fab_yarn_trim=return_global_ajax_value(update_id, 'sum_fab_yarn_trim_value', '', 'requires/pre_cost_entry_controller_v2');
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
		if(txt_commercial_cost_method == 5 || txt_commercial_cost_method == 6){
			var amount=return_global_ajax_value(update_id+'_'+txt_commercial_cost_method, 'commercial_cost_method_value', '', 'requires/pre_cost_entry_controller_v2');
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
		var row_num=$('#tbl_comarcial_cost tr').length-1;
		for(var i=1; i<=row_num; i++){
			calculate_comarcial_cost(i,'cal_amount');
		}
		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
	//	var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

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
		if(cost_control_source==4)//Quick Costing [WVN]
		{
			var qc_validate=fnc_budgete_cost_validate();
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)//Price Quotation
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


		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('update_id','Company Name')==false)
			{
				release_freezing();
				return;
			}
			data_all=data_all+get_submitted_data_string('update_id*hidd_job_id*cbo_company_name*copy_quatation_id*txt_quotation_id*cboitem_'+i+'*txtcomarcialrate_'+i+'*txtcomarcialamount_'+i+'*cbocomarcialstatus_'+i+'*comarcialupdateid_'+i+'*txtratecomarcial_sum*txtamountcomarcial_sum',"../../");
		}
		var data="action=save_update_delet_comarcial_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;

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
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
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
				//show_msg(reponse[0]);
				//alert(reponse[0]);
				show_sub_form(document.getElementById('update_id').value, 'show_comarcial_cost_listview');
				//document.getElementById('check_open_close_fabric_cost').checked=true;
				//show_content_form('fabric_cost');
				//show_hide_content('comarcial_cost', '')
				//set_button_status(0, permission, 'fnc_embellishment_cost_dtls',7);
				//set_button_status(is_update, permission, submit_func,btn_id)

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
		if(cost_control_source==4)//Quick Costing [WVN]
		{
			var qc_validate=fnc_budgete_cost_validate();
			var qc_validate=qc_validate.split("__");
			if(qc_validate[1]==1)
			{
				alert(qc_validate[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)//Price Quotation
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var pri_trim_pre_cost=$('#txt_trim_pre_cost').attr('pri_trim_pre_cost')*1;
				var pri_embel_pre_cost=$('#txt_embel_pre_cost').attr('pri_embel_pre_cost')*1;
				var pri_wash_pre_cost=$('#txt_wash_pre_cost').attr('pri_wash_pre_cost')*1;
				 

				 
				//var pri_total_pre_cost=$('#txt_total_pre_cost').attr('pri_total_pre_cost')*1;

				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				var txt_trim_pre_cost=$('#txt_trim_pre_cost').val()*1;
				var txt_embel_pre_cost=$('#txt_embel_pre_cost').val()*1;
				var txt_wash_pre_cost=$('#txt_wash_pre_cost').val()*1;
				 
				//var txt_total_pre_cost=$('#txt_total_pre_cost').val()*1;

				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation');
					return;
				}
				if(txt_trim_pre_cost>pri_trim_pre_cost)
				{
					alert('Trims cost is greater than Quotation');
					return;
				}
				if(txt_embel_pre_cost>pri_embel_pre_cost)
				{
					alert('Emblishment cost is greater than Quotation');
					return;
				}
				if(txt_wash_pre_cost>pri_wash_pre_cost)
				{
					alert('Wash cost is greater than Quotation');
					return;
				}
				


				var price_tot_cost=number_format((pri_total_pre_cost*1)+(pri_commission_pre_cost*1),4,'.','' );
				//alert(txt_total_pre_cost+'_'+pri_total_pre_cost+'_'+pri_commission_pre_cost+'_'+price_tot_cost);
				if((txt_total_pre_cost*1)>price_tot_cost)
				{
					//alert("Total Cost"+txt_total_pre_cost+ " is greater than Quotation"+price_tot_cost);
					//return;
				}
			}
		}

		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*cbo_costing_per*cbo_order_uom*update_id_dtls*txt_fabric_pre_cost*txt_trim_pre_cost*txt_embel_pre_cost*txt_wash_pre_cost',"../../");
			//alert(data); return;
			http.onreadystatechange = function() {
				if( http.readyState == 4 && http.status == 200 ) {
					var reponse=trim(http.responseText).split('**');
					if(reponse[0]=='quataNotApp')
					{
						alert("Quotation is not  Approved. Please Approved the Quotation");
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
		//hide_left_menu("Button1");
		var company_id=document.getElementById("cbo_company_name").value;
		page_link+='&company_id='+company_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1390px,height=450px,center=1,resize=1,scrolling=0','../')
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
				reset_form('precosting_1', 'cost_container','','cbo_currercy,2*cbo_approved_status,2*is_click_cons_box,1','','txt_sourcing_date*txt_exchange_rate*cbo_costing_per');//cbo_costing_per,1*
				
				
				get_php_form_data( theemail.value, action, "requires/pre_cost_entry_controller_v2" );
				get_php_form_data( theemail.value, 'check_data_mismass', "requires/pre_cost_entry_controller_v2" );
				//txt_sourcing_date
				var txt_sourcing_date=$("#txt_sourcing_date").val();
				var sourcing_inserted_by=$("#sourcing_inserted_by").val();
			//	alert(permission);
				if(txt_sourcing_date!="")
				{
					 set_button_status(1, permission, 'fnc_precosting_entry',1);
				}
				else
				{
					 set_button_status(0, permission, 'fnc_precosting_entry',1);
				}
				$('#txt_sourcing_date').removeAttr('disabled','disabled');
				/*if(sourcing_inserted_by!="") //Dtls part
				{
					 set_button_status(1, permission, 'fnc_quotation_entry_dtls',1);
				}
				else
				{
					 set_button_status(0, permission, 'fnc_quotation_entry_dtls',1);
				}*/
				
				//set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
				
				
				//setTimeout(function(){ $('#cbo_company_name').change();}, 3000);
				
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
		
		if(operation==2 || operation==3)
		{
			alert("Delete/Approve Restricted")
			release_freezing();
			return;
		}
		if(operation==1)
		{
			var trim_cost=$("#txt_trim_pre_cost").val()*1;    
			 var pre_trim_cost=$("#txt_trim_pre_cost").attr('pre_trim_cost')*1;
            if(trim_cost!=pre_trim_cost)
            {
                alert("Trims Cost Change Found, Please Save or Update.");
                release_freezing();
                return;
            }
		}
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

		/*if(operation==1)
		{
			if(($('#txt_sew_efficiency_source').val()*1)!=1)
			{
				get_php_form_data( $("#cbo_company_name").val()+"**"+$("#txt_job_no").val()+"**"+$("#txt_sew_smv").val(), 'get_efficiency_percent', "requires/pre_cost_entry_controller_v2" );
				calculate_cm_cost_with_method();
			}
		}*/

		/*if(document.getElementById('cm_cost_predefined_method_id').value==1)
		{
			var sew_efficiency_per=document.getElementById('txt_sew_efficiency_per').value;
			if(sew_efficiency_per=="")
			{
				alert("Insert Sew Efficiency %");
				release_freezing();
				return;
			}
		}*/
		//alert('DDD');return;
		if (form_validation('txt_job_no*txt_sourcing_date*cbo_costing_per','Job No*Sourcing Costing Date*Costing Per')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_job_no*hidd_job_id*txt_sourcing_date*cbo_costing_per*txt_remarks*update_id*copy_quatation_id*cbo_approved_status*txt_exchange_rate*txt_sew_smv*cbo_ready_to_approved*cbo_company_name*txt_quotation_id',"../../");

				//alert(data); 
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
			/*if(reponse[0]=='YarApp')
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
			}*/

			

			/*if(reponse[0]=='ComnApp')
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
			}*/


			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			document.getElementById('update_id').value  = reponse[2];
			if(reponse[3]==1)
			{
				document.getElementById('cbo_approved_status').value = '2';
				document.getElementById('approve1').value = 'Approved';
				document.getElementById('txt_sourcing_date').disabled=false;
			 
				document.getElementById('cbo_costing_per').disabled=false;
				document.getElementById('txt_remarks').disabled=false;
				document.getElementById('save1').disabled=false;
				document.getElementById('update1').disabled=false;
				document.getElementById('Delete1').disabled=false;
				//===================
			
				document.getElementById('save2').disabled=false;
				document.getElementById('update2').disabled=false;
				document.getElementById('Delete2').disabled=false;
			}
			if(reponse[3]==2)
			{
				document.getElementById('cbo_approved_status').value = '1';
				document.getElementById('approve1').value = 'Un-Approved';
				document.getElementById('txt_sourcing_date').disabled=true;
				 
				document.getElementById('cbo_costing_per').disabled=true;
				document.getElementById('txt_remarks').disabled=true;
				document.getElementById('save1').disabled=true;
				document.getElementById('update1').disabled=true;
				document.getElementById('Delete1').disabled=true;

				
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

	if(cm_cost_editable==1)
	{
		document.getElementById('txt_cm_pre_cost').disabled=false;
	}

	document.getElementById('cm_cost_predefined_method_id').value=cm_cost_method;
	document.getElementById('cm_cost_editable').value=cm_cost_editable;
}

//Master Form End-----------------------------------------------------

// Dtls Form 1--------------------------------------------------------
	function calculate_cm_cost_with_method()
	{
		//1. CM Cost = SMV*CPM+ (SMV*CPM)* Efficiency Wastage%
		//2. CM Cost= CU(SMV*CPM/ Efficiency %)+ SF(SMV*CPM/ Efficiency %)
		//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
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

		//var cpm=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
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
		//calculate_total_cost_psc_set()
	//	clculate_margin_dzn();
		//calculate_margin_pcs_set();
		//calculate_percent_on_po_price()
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
		calculate_margin_pcs_set()
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
		//var depreciation_amortization_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
		var depreciation_amortization_per=document.getElementById('cost_per_minute').value;
		var data=depreciation_amortization_per.split('_');
		var data_value=data[4];
		if(data_value=="") data_value=0;
		// alert(fob_value);
		var depreciation_amortization_value=(fob_value*data_value)/100;
		//if(depreciation_amortization_value==0) depreciation_amortization_value=$('#txt_depr_amor_pre_cost').val()*1;
		if(number_format_common(depreciation_amortization_value,1,0,currency)>0)
		{
			document.getElementById('txt_depr_amor_pre_cost').readOnly=true;
		}
		else
		{
			document.getElementById('txt_depr_amor_pre_cost').readOnly=false;
		}
		document.getElementById('txt_depr_amor_pre_cost').value=number_format_common(depreciation_amortization_value,1,0,currency);
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
		//var oparating_expanses_per=return_global_ajax_value(cbo_company_name+'_'+txt_costing_date, 'cost_per_minute', '', 'requires/pre_cost_entry_controller_v2');
		var oparating_expanses_per=document.getElementById('cost_per_minute').value;
		var data=oparating_expanses_per.split('_');
		var data_value=data[5];
		//alert(data_value)
		if(data_value=="") data_value=0;

		var oparating_expanses_value=(fob_value*data_value)/100;
		//if(oparating_expanses_value==0) oparating_expanses_value=$('#txt_common_oh_pre_cost').val()*1;
		if(number_format_common(oparating_expanses_value,1,0,currency)>0)
		{
			document.getElementById('txt_common_oh_pre_cost').readOnly=true;
		}
		else
		{
			document.getElementById('txt_common_oh_pre_cost').readOnly=false;
		}
		document.getElementById('txt_common_oh_pre_cost').value=number_format_common(oparating_expanses_value,1,0,currency);
		calculate_main_total();
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
				calculate_confirm_price_dzn();
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
				calculate_confirm_price_dzn();
			}
			if(value==4)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 3 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 3 Dzn";
				calculate_confirm_price_dzn();
			}
			if(value==5)
			{
				document.getElementById('final_price_td_dzn').innerHTML="Price/ 4 Dzn";
				document.getElementById('margin_dzn').innerHTML="Margin/ 4 Dzn";
				calculate_confirm_price_dzn();
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
		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		/*if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					//release_freezing();
					//return;
				}
			}
		}*/
		/*var check_is_master_part_saved=return_global_ajax_value(document.getElementById('update_id').value, 'check_is_master_part_saved', '', 'requires/pre_cost_entry_controller_v2');
		if(trim(check_is_master_part_saved)=="")
		{
			release_freezing();
			alert ("Save Master Part")	;
			return;
		}*/
		//alert(1);
		
		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		//alert(cost_control_source);
		if(cost_control_source==1)
		{

		}
		else if(cost_control_source==4)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var pri_trim_pre_cost=$('#txt_trim_pre_cost').attr('pri_trim_pre_cost')*1;
				var pri_embel_pre_cost=$('#txt_embel_pre_cost').attr('pri_embel_pre_cost')*1;
				var pri_wash_pre_cost=$('#txt_wash_pre_cost').attr('pri_wash_pre_cost')*1;
				var pri_comml_pre_cost=$('#txt_comml_pre_cost').attr('pri_comml_pre_cost')*1;
			//	var pri_commission_pre_cost=$('#txt_commission_pre_cost').attr('pri_commission_pre_cost')*1;

				 

				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				var txt_trim_pre_cost=$('#txt_trim_pre_cost').val()*1;
				var txt_embel_pre_cost=$('#txt_embel_pre_cost').val()*1;
				var txt_wash_pre_cost=$('#txt_wash_pre_cost').val()*1;
				
				
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					release_freezing();
					alert('Fabric cost is greater than Quotation');
					return;
				}
				if(txt_trim_pre_cost>pri_trim_pre_cost)
				{
					release_freezing();
					alert('Trims cost is greater than Quotation');
					return;
				}
				if(txt_embel_pre_cost>pri_embel_pre_cost)
				{
					release_freezing();
					alert('Emblishment cost is greater than Quotation');
					return;
				}
				if(txt_wash_pre_cost>pri_wash_pre_cost)
				{
					release_freezing();
					alert('Wash cost is greater than Quotation');
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
			var data="action=save_update_delete_quotation_entry_dtls&operation="+operation+get_submitted_data_string('update_id*hidd_job_id*cbo_costing_per*update_id_dtls*txt_fabric_pre_cost*txt_trim_pre_cost*txt_embel_pre_cost*txt_wash_pre_cost',"../../");

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
				alert("Quotation is not  Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
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
			var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			if (r==true) zero_val="1"; else zero_val="0";
			freeze_window();
			//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
			var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*cbo_costing_per',"../../");
			http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_reponse;
		}
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
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

	function fnc_budgete_cost_validate() //Quick Costing [WVN]
	{
		var company_id=$('#cbo_company_name').val();
		var fab_cost_pre=$('#txt_fabric_pre_cost').val()*1;
		var trim_cost_pre=$('#txt_trim_pre_cost').val()*1;
		var embel_cost_pre=$('#txt_embel_pre_cost').val()*1;
		var wash_cost_pre=$('#txt_wash_pre_cost').val()*1;
	 

		var fab_cons_kg=0; var fab_cons_yds=0; var fab_amount=0; var sp_oparation_amount=0; var acc_amount=0; var fright_amount=0; var lab_amount=0; var misce_amount=0; var other_amount=0; var comm_amount=0; var fob_amount=0; var cm_amount=0; var rmg_ratio=0;
		var costing_id=$('#txt_quotation_id').val()*1;
		var cost_control_source_data=return_global_ajax_value( company_id, 'cost_control_source', '', 'requires/pre_cost_entry_controller_v2');
		if(cost_control_source_data==1)
		{
			if(costing_id!=0)
			{
				var str_data=return_global_ajax_value( costing_id+'_'+$('#txt_job_no').val(), 'budgete_cost_validate', '', 'requires/pre_cost_entry_controller_v2');
				var spdata=str_data.split("##");
				var fab_cons_kg=spdata[0]; var fab_cons_yds=spdata[1]; var fab_amount=spdata[2]; var sp_oparation_amount=spdata[3]; var acc_amount=spdata[4]; var fright_amount=spdata[5]; var lab_amount=spdata[6]; var misce_amount=spdata[7]; var other_amount=spdata[8]; var comm_amount=spdata[9]; var fob_amount=spdata[10]; var cm_amount=spdata[11]; var rmg_ratio=spdata[12];
				var msg=""; var msg_type=0;
				if(fab_cost_pre>fab_amount)
				{
					msg_type=1;
					msg="BOM Limit. Fabric Cost := "+fab_amount+'__'+msg_type;
				}
				else if(embel_cost_pre>sp_oparation_amount)
				{
					msg_type=1;
					msg="BOM Limit. Gmts.Wash Cost := "+sp_oparation_amount+'__'+msg_type;
				}
				else if(trim_cost_pre>acc_amount)
				{
					msg_type=1;
					msg="BOM Limit. Trim Cost := "+acc_amount+'__'+msg_type;
				}
				
				else
				{
					msg_type=0;
					msg=" "+'__'+msg_type;
				}
				return msg;
			}
			else
			{
				var msg_type=0;
				var msg=" "+'__'+msg_type;
				return msg;
			}
		}
		else
		{
			var msg_type=0;
			var msg=" "+'__'+msg_type;
			return msg;
		}
	}

	function print_report_button_setting(report_ids)
	{
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
			if(report_id[k]==211)$("#report_btn_14").show();
			if(report_id[k]==307)$("#report_btn_15").show();
		}
	}

	function ResetForm(){
		document.getElementById('app_sms').innerHTML ='';
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
		var interest_value=(fob_value*data_value)/100;
		//if(interest_value==0) interest_value=$('#txt_interest_pre_cost').val()*1;
		if(number_format_common(interest_value,1,0,currency)>0)
		{
			document.getElementById('txt_interest_pre_cost').readOnly=true;
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
		//if(incometax_value==0) incometax_value=$('#txt_incometax_pre_cost').val()*1;
		if(number_format_common(incometax_value,1,0,currency)>0)
		{
			document.getElementById('txt_incometax_pre_cost').readOnly=true;
		}
		else
		{
			document.getElementById('txt_incometax_pre_cost').readOnly=false;
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
		var currier_amount= response[1]*1;
		var editable= response[2]*1;
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

		var supplier = return_global_ajax_value( company_id, 'supplier_name', '', 'requires/pre_cost_entry_controller_v2');
		supplierInfo = eval(supplier);
		$("#txtembsupplier_"+rid).autocomplete({
		 source: supplierInfo,
		 search: function( event, ui ) {
			$("#cboembsupplierid_"+rid).val("");
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
			 }
		});
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

</script>
</head>
<body onLoad="set_hotkey(); set_auto_complete('pre_cost_mst');" >
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:1070px;">
        <legend>Pre-Costing</legend>
        <form name="precosting_1" id="precosting_1" autocomplete="off">
            <div style="width:1070px;">
            <table  width="1070" cellspacing="2" cellpadding=""  border="0">
            	<tr>
            		<td width="120" class="must_entry_caption" colspan="4" align="right">Job No</td>
                    <td width="130" colspan="4"><input  style="width:130px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/pre_cost_entry_controller_v2.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Browse Job No." name="txt_job_no" id="txt_job_no" readonly />
                    	<input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
                    </td>
            	</tr>
                <tr>
                    <td width="120">Precost ID</td>
                    <td width="130"><input type="text" id="txt_precost_id" name="txt_precost_id" class="text_boxes" style="width:110px;" disabled /></td>
                    <td width="120">Company</td>
                    <td width="130"><? echo create_drop_down("cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pre_cost_entry_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td' );  load_drop_down( 'requires/pre_cost_entry_controller_v2', this.value, 'load_drop_down_agent', 'agent_td' );is_manula_approved(this.value);set_currier_cost_method_variable(this.value)",1); ?></td>
                    <td width="120">Quotation ID</td>
                    <td width="130"><input type="text" id="txt_quotation_id" name="txt_quotation_id" class="text_boxes" style="width:110px;" readonly disabled /></td>
                    <td width="120">Style Ref</td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly disabled/></td>
                </tr>
                <tr>
                    <td>Style Desc.</td>
                    <td colspan="3"><input class="text_boxes" type="text" style="width:360px;" name="txt_style_desc" id="txt_style_desc" maxlength="100" title="Maximum 100 Character" readonly disabled/></td>
                    <td>Buyer</td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td>Prod. Dept.</td>
                    <td><? echo create_drop_down( "cbo_pord_dept", 120, $product_dept,"", 1, "-- Select --",0, "",1,"" ); ?>
                       <!-- <input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code"  disabled />-->
                    </td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td><? echo create_drop_down( "cbo_currercy", 50, $currency,"", 0, "", 2, "" ,1,""); ?>
                        ER. <input class="text_boxes_numeric" type="text" style="width:30px;" name="txt_exchange_rate" id="txt_exchange_rate" readonly disabled/>
                    </td>
                   
                    <td>Job Qty.</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:110px;" name="txt_offer_qnty" id="txt_offer_qnty" disabled />
                    <input class="text_boxes_numeric" type="hidden" style="width:110px;" name="txt_sew_smv" id="txt_sew_smv"  disabled />
                    </td>
                    <td>Order UOM </td>
                    <td><? echo create_drop_down("cbo_order_uom",120, $unit_of_measurement, "",0, "",1, "change_caption_cost_dtls(this.value, 'change_caption_pcs' )",1,"1,58" ); ?>
                       
                    </td>
                     <td>&nbsp; </td>
                    <td>
                    	 <input type="button" id="set_button"  style="width:120px" class="image_uploader" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                        <input type="hidden" id="set_breck_down" />
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="tot_set_qnty" />
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Sourcing Date</td>
                    <td><input class="datepicker" type="text" style="width:110px;" name="txt_sourcing_date" id="txt_sourcing_date" value="<? echo date('d-m-Y'); ?>"/></td>
                    <td class="must_entry_caption">Costing Per</td>
                    <td><? echo create_drop_down( "cbo_costing_per", 120, $costing_per, "",0, "0", 1, "",1,"" ); //change_cost_per(this.value) ?>
                    	
                    </td>
                     <td>Approved</td>
                    <td><? echo create_drop_down( "cbo_approved_status", 120, $yes_no,"", 0, "", 2, "",1,"" ); ?></td>
                     <td>Images</td>
                    <td>
                    <input type="button" class="image_uploader" style="width:120px" value="CLICK TO VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1,2)" />
                    </td>
                   
                </tr>
               
                <tr>
                    <td>Ready To Approved</td>
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 120, $yes_no,"", 1, "-- Select--", 2, "",1,"" ); ?></td>
                     <td>Remarks</td>
                    <td colspan="5">
                    <input class="text_boxes" type="text" style="width:615px;" name="txt_remarks" id="txt_remarks" maxlength="500" title="Maximum 500 Character" placeholder="Remarks" disabled readonly/></td>
                   
                </tr>
                
                
                <tr>
                    <td align="center" height="10" colspan="8" valign="top" id="check_sms" style="font-size:18px; color:#F00">   </td>
                </tr>
                <tr>
                    <td align="center" height="10" colspan="8" valign="top" id="app_sms" style="font-size:18px; color:#F00">   </td>
                </tr>
                <tr>
                    <td align="center" valign="middle" class="button_container" colspan="8">
                        <input type="hidden" id="update_id" value="" />
                        <input type="hidden" id="copy_quatation_id" value="" />
                        <input type="hidden" id="budget_exceeds_quot_id" value="" />
                        <input type="hidden" id="txt_cost_control_source" value="" />
                        <input type="hidden" id="pre_cost_id" value="" />
                        <input type="hidden" id="cm_cost_predefined_method_id" value="" width="50" />
                        <input type="hidden" id="cm_cost_editable" value="" width="50" />
                        <input type="hidden" id="check_input" name="check_input" value="" width="50" />
                        <input type="hidden" id="is_click_cons_box" name="is_click_cons_box" value="1" width="50" />
                        <input type="hidden" id="cost_per_minute" name="cost_per_minute" value="" width="50" />
                        <input type="hidden" id="txt_deffd_lc_cost_percent" name="txt_deffd_lc_cost_percent" value="" width="50" />
                        <? echo load_submit_buttons( $permission, "fnc_precosting_entry", 0,0 ,"ResetForm()",1,1) ; ?>
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
                    <table width="100%" cellspacing="3" cellpadding="0" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="94" align="center">Cost Components</th>
                                <th width="70" align="center">Budgeted Cost</th>
                                
                            </tr>
                        </thead>
                        <tr>
                            <td>Fabric Cost</td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_fabric_pre_cost" id="txt_fabric_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_fabric_cost_listview','');" placeholder="Click" onChange="calculate_main_total()" readonly/></td>
                            
                        </tr>
                        <tr>
                            <td>Trims Cost &nbsp <span id="load_temp" style="float:right; width:10px; font-weight: bold;background-color: white;color:black; border: 1px white solid; cursor: pointer;"></span></td>
                            <td><input class="text_boxes_numeric" type="text" name="txt_trim_pre_cost" id="txt_trim_pre_cost" style="width:60px;" onClick=" show_sub_form(document.getElementById('update_id').value,'show_trim_cost_listview');" placeholder="Click" onChange="calculate_main_total()" readonly/></td>
                            
                        </tr>
                        <tr>
                            <td>Embel. Cost</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_embel_pre_cost" id="txt_embel_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_embellishment_cost_listview','');" onChange="calculate_main_total()"  readonly placeholder="Click"/></td>
                            
                        </tr>
                        <tr>
                            <td>Gmts.Wash</td>
                            <td><input style="width:60px;" class="text_boxes_numeric" type="text" name="txt_wash_pre_cost" id="txt_wash_pre_cost" onClick=" show_sub_form(document.getElementById('update_id').value,'show_wash_cost_listview','');" onChange="calculate_main_total()" readonly placeholder="Click"/></td>
                            
                        </tr>
                        
                        <tr>
                            <!--<td>
                            Confirm Date
                            </td>
                            <td align="center">
                            <input class="datepicker" type="text" name="txt_confirm_date_pre_cost" id="txt_confirm_date_pre_cost" style="width:80px;"/>
                            </td>-->
                            <td><input type="hidden" id="update_id_dtls" name="update_id_dtls" readonly/>
                            <input type="hidden" id="sourcing_inserted_by" name="sourcing_inserted_by" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3" valign="middle" class="button_container">
                            <? echo load_submit_buttons( $permission, "fnc_quotation_entry_dtls", 0,0 ,"reset_form('quotationdtls_2','','')",2) ; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">Select PO: <span id="po_td"><? echo create_drop_down( "txt_po_breack_down_id", 190,$blank_array, "", 1, "-- Select PO --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                            	<!--<input type="button" id="report_btn_2" class="formbutton" value="Cost Rpt2" onClick="generate_report('preCostRpt2')" style="display:none;" />
                                <input type="button" id="report_btn_7" class="formbutton" value="Cost Woven" onClick="generate_report('preCostRptWoven')" style="display:none;"  />
                                <input type="button" id="report_btn_8" class="formbutton" value="Bom Woven" onClick="generate_report('bomRptWoven')" style="display:none;" />
                                <input type="button" id="report_btn_12" class="formbutton" value="BOM Dtls" onClick="generate_report('checkListRpt')" style="display:none;" />
                                <input type="button" id="report_btn_15" class="formbutton" value="Basic Cost" onClick="generate_report('basic_cost');" style="display:none;" />-->                                
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
//calculate_confirm_price_dzn();
//check_exchange_rate();
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
