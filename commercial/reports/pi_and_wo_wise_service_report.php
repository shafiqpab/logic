<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create PI and WO Wise Service Report.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	24-08-2021
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
echo load_html_head_contents("PI and WO Wise Service Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = { 
		col_80: "none", 
		col_operation: {
			id: ["value_total_req_qnty","value_total_wo_qnty","value_total_wo_amt","value_total_wo_balance","value_total_pi_qnty","value_total_pi_amt","value_total_lc_amt","value_total_pkg_qnty","value_total_pay_amt","value_total_mrr_qnty","value_total_mrr_amt","value_total_short_amt","value_total_pipe_line"],
			col: [12,19,21,23,30,32,39,51,58,59,60,61,62],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function generate_report(rep_type)
	{
		var supplier_id=$("#cbo_supplier").val();
		var date_type=$("#cbo_date_type").val();
		var txt_wo_po_no=$("#txt_wo_po_no").val();
		var txt_pi_no=$("#txt_pi_no").val();
		var txt_pi_id=$("#txt_pi_id").val();
		var item_category_id=$("#cbo_item_category_id").val();
		var cbo_location=$("#cbo_location").val();
		// var cbo_store_name=$("#cbo_store_name").val();
				
		if(txt_wo_po_no =="" && txt_pi_no =="")
		{
			if(form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Item Category*From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}

		var action='';
		if(rep_type==1){
			action="report_generate";
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to*cbo_supplier*txt_wo_po_no*txt_wo_id*cbo_date_type*txt_pi_no*txt_pi_id*cbo_location',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/pi_and_wo_wise_service_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);

			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';			
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
	}
	
	function change_date_caption(id)
	{
		if (id==1) { $("#dynamic_caption").html("Work Order Date"); }
		else if (id==2) { $("#dynamic_caption").html("PI Date"); }
	}
		
	function fn_req_wo(str)
	{
		var txt_wo_po_no=$('#txt_wo_po_no').val();
		var txt_pi_no=$('#txt_pi_no').val();
        if(str==2 && txt_wo_po_no)
		{
			$('#txt_wo_po_no').attr("disabled",false);
			$('#txt_pi_no').val("").attr("disabled",true);	
		}
		else  if(str==3 && txt_pi_no)
		{
			$('#txt_wo_po_no').val("").attr("disabled",true);
			$('#txt_pi_no').attr("disabled",false);
		}
		else
		{	
			$('#txt_wo_po_no').val("").attr("disabled",false);
			$('#txt_pi_no').val("").attr("disabled",false);
			$('#txt_date_from').val("").attr("disabled",false);
			$('#txt_date_to').val("").attr("disabled",false);
			$("#txt_date_from").addClass("datepicker");
		}
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false ){
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var item_category_id = $("#cbo_item_category_id").val();

		var page_link='requires/pi_and_wo_wise_service_report_controller.php?action=wo_no_popup&cbo_company_name='+cbo_company_name+'&cbo_supplier='+cbo_supplier+'&item_category_id='+item_category_id;
		var title='WO Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var wo_no=this.contentDoc.getElementById("hide_wo_no").value;
			var wo_id=this.contentDoc.getElementById("hide_wo_id").value;
			$('#txt_wo').val(wo_no);
			$('#txt_wo_po_no').val(wo_no);
			$('#txt_wo_id').val(wo_id);
			if(wo_id!='')
			{
				$('#txt_date_from').val("").attr("disabled",true);
				$('#txt_date_to').val("").attr("disabled",true);
			}
		}
	}

	function openmypage_pi()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false ){
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var item_category_id = $("#cbo_item_category_id").val();

		var page_link='requires/pi_and_wo_wise_service_report_controller.php?action=pi_no_popup&cbo_company_name='+cbo_company_name+'&cbo_supplier='+cbo_supplier+'&item_category_id='+item_category_id;
		var title='PI Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var pi_no=this.contentDoc.getElementById("hide_pi_no").value;
			var pi_id=this.contentDoc.getElementById("hide_pi_id").value;
			$('#txt_pi').val(pi_no);
			$('#txt_pi_no').val(pi_no);
			$('#txt_pi_id').val(pi_id);
			if(pi_id!='')
			{
				$('#txt_date_from').val("").attr("disabled",true);
				$('#txt_date_to').val("").attr("disabled",true);
			}
		}
	}

	function fnc_chk_category(id)
	{
		$('#txt_req_no').val("").attr("disabled",false);
		$('#txt_pi_no').val("").attr("disabled",false);
		$('#cbo_date_type').val(2).attr("disabled",false);
		change_date_caption(2);
	}

	function openmypage(wo_dtls_id)
	{
		var cbo_company_name=$("#cbo_company_name").val();
		page_link='requires/pi_and_wo_wise_service_report_controller.php?action=item_details'+'&wo_dtls_id='+wo_dtls_id+'&cbo_company_name='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Item Info', 'width=250px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			alert("Nayem");
		}
	}
</script>
</head>

<body onLoad="set_hotkey();">
<form id="PiAndWoServiceReport">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1210px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1210px;">
                <table class="rpt_table" width="1200" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="160" class="must_entry_caption">Company Name</th>
                            <th width="130">Location</th>
                            <th width="160" class="must_entry_caption">Item Category</th>
                            <!-- <th width="130">Store</th> -->
                            <th width="160">Supplier</th>
                            <th width="90">WO No</th>
                            <th width="90">PI No</th>
                            <th width="100">Data Type</th>
                            <th width="170" class="must_entry_caption" id="dynamic_caption">PI Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('PiAndWoServiceReport','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pi_and_wo_wise_service_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/pi_and_wo_wise_service_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                                ?>
                            </td>
                            <td align="center" id="location_td">
                            	<? 
                            		echo create_drop_down( "cbo_location", 130, $blank_array,"", 1,"-- Select --",0,"" );
                            	?>
                            </td>
                            <td align="center" id="category_td">
                            	<? 
                            		echo create_drop_down( "cbo_item_category_id", 160, $item_category,'', 1, '-- Select --',0,"fnc_chk_category(this.value);load_drop_down( 'requires/pi_and_wo_wise_service_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_supplier', 'supplier_td' );",0,'12,24,25,31,74,102,103,104'); // only service category show
                            	?>
                            </td>
                            <!-- <td id="store_td"> 
						  	<?
							   	echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td> -->
                            <td id="supplier_td"> 
						  	<?
							   	echo create_drop_down( "cbo_supplier", 160, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td>
                            <td align="center">
                            	<input type="text" name="txt_wo_po_no" id="txt_wo_po_no" value="" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_wo();"  onBlur="fn_req_wo(2);" readonly/>
                            	<input type="hidden" name="txt_wo" id="txt_wo">
                            	<input type="hidden" name="txt_wo_id" id="txt_wo_id">
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_pi_no" id="txt_pi_no" value="" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_pi();"  onBlur="fn_req_wo(3);" readonly/>
                            	<input type="hidden" name="txt_pi" id="txt_pi">
                            	<input type="hidden" name="txt_pi_id" id="txt_pi_id">
                            </td>
                            <td>
                            	<?
									$date_type_arr=array(1=>"Work Order",2=>"PI");
									echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"", 0, "--Select Date--", 2,"change_date_caption(this.value)" );
                            	?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date" readonly />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date" readonly />
                            </td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="11" align="center"><? echo load_month_buttons(1); ?>
                        </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
