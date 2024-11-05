<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Monthly Fabric Booking Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	17-07-2017
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Monthly Fabric Booking Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = {
						col_operation: {
						id: ["total_wo_fin_qty","total_wo_grey_qty"],
						col: [9,10],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
						}
					}
	function fn_report_generated()
	{
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_wo_no = $("#txt_wo_no").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		
		if(txt_style_ref!="" || txt_wo_no!="")
		{
			
		}
		else
		{
			if(form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*txt_style_ref*txt_style_ref_id*txt_wo_no*txt_wo_id*txt_date_from*txt_date_to*cbo_pay_mode*cbo_supplier_name*cbo_supplier_location*cbo_year_selection*txt_internal_ref',"../../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/monthly_fabric_booking_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var response=trim(http.responseText).split("**");
			//alert(reponse[2]);
		$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1);" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1);
			setFilterGrid("table_body",-1,tableFilters);
	 		show_msg('3');
		}
	}
	
	function new_window(type)
	{
		
		//document.getElementById('scroll_body1').style.overflow="auto";
		//document.getElementById('scroll_body1').style.maxHeight="none";
		//document.getElementById('scroll_body2').style.overflow="auto";
		//document.getElementById('scroll_body2').style.maxHeight="none";
		// alert(type);
		  
		 $('.scroll_div_inner').css('overflow','auto');
		 $('.scroll_div_inner').css('maxHeight','none');
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','480px');
		 /*
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		document.getElementById('scroll_body2').style.overflowY="scroll"; 
		document.getElementById('scroll_body2').style.maxHeight="480px";
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		*/
		$("#table_body tr:first").show();
	}
	
	
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		var page_link='requires/monthly_fabric_booking_report_controller.php?action=style_refarence_surch&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year_id;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	function openmypage_wo()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
	//	var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_wo_id = $("#txt_wo_id").val();
		var txt_wo_no = $("#txt_wo_no").val();
		var page_link='requires/monthly_fabric_booking_report_controller.php?action=work_order_popup&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_wo_id='+txt_wo_id+'&txt_wo_no='+txt_wo_no+'&txt_style_ref='+txt_style_ref; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_wo_no").val(style_des);
			$("#txt_wo_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}

	function fn_order_disable(type_id)
	{
		if(type_id==2)
		{
			$('#txt_wo_no').attr("disabled",true);
		}
		else
		{
			$('#txt_wo_no').attr("disabled",false);
		}
	}
	
	
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			<? echo load_freeze_divs ("../../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1260px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1260px;">
                <table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th width="100" class="">WO Type</th>
						<th class="" width="130">Company Name</th>
						<th  width="70" class="" align="right">Pay Mode</th>
						<th  width="100" class="" align="right">Supplier</th>
						<th  width="100" class="" align="right">Supplier Location</th>
                        <th width="130">Buyer Name</th>
						<th  width="120">WO No</th>
                        <th  width="120">Job No</th>
						<th  width="60">Int. Ref.</th>
                        <th width="170" class="must_entry_caption">Date Range</th>
                       
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">
					 	<td>
							<?
                            $search_style_arr=array(0=>"All",1=>"Sample",2=>"Short",3=>"Main");
                            echo create_drop_down( "cbo_search_type", 100, $search_style_arr,"", 0,"", 0, "",0,"" ); 
                        ?></td>                   
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_fabric_booking_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
						 
                          <td>
                               <?
							   		echo create_drop_down( "cbo_pay_mode", 70, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'requires/monthly_fabric_booking_report_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" );
							   ?> 
                          </td>
						    <td id="sup_td">
                               <?
							   		echo create_drop_down( "cbo_supplier_name", 100, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "load_drop_down( 'requires/monthly_fabric_booking_report_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td' )",0 );
							   ?> 
                            </td> 
							 <td id="sup_location_td">
                               <?
							   		echo create_drop_down( "cbo_supplier_location", 100, $blank_array,"", 1, "-- All Supplier Location --", $selected, "",0,"" );
							   ?> 
                            </td> 
								 
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                       
                      
                         <td align="center">
                            <input style="width:120px;" name="txt_wo_no" id="txt_wo_no" onDblClick="openmypage_wo()" class="text_boxes" placeholder="Write/Browse"  />   
                            <input type="hidden" name="txt_wo_id" id="txt_wo_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                        </td>
						<td align="center">
                            <input style="width:120px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style()" class="text_boxes" placeholder="Write/Browse" />
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                        </td>
						<td><input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>
                       
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
