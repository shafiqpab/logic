<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sample Status Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-12-2020
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
echo load_html_head_contents("Sample Status Report", "../../", 1, 1,$unicode,'1','');
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(operation)
	{
		freeze_window(3);
		var txt_req_no=document.getElementById('txt_req_no').value;
		var txt_style=document.getElementById('txt_style').value;
		
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var txt_req_no_id=document.getElementById('txt_req_no_id').value;
		
		var divData=msgData="";
		if(txt_date_from=="" && txt_date_to=="" && txt_req_no=="" && txt_style=="" && txt_req_no_id=="")
		{
			var divData="cbo_company_name*txt_date_from*txt_date_to";	
			var msgData="Company Name*From Date*To Date";	
		}
		else
		{
			var divData="cbo_company_name";	
			var msgData="Company Name";
		}
		
		//if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)//
		if(form_validation(divData,msgData)==false)
		{
			release_freezing();
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_brand*txt_req_no*txt_style*cbo_comp_status*cbo_date_type*txt_req_no_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			//alert(data); release_freezing(); return;
			http.open("POST","requires/sample_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+',1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(tot_rows*1>1)
			{
				 var tableFilters = 
				 {
					col_operation: {
						id: ["td_reqQty","value_reqQtyLbs"],
						col: [7,15],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
					}	
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			show_msg('3');
			release_freezing();
		}
	}

	function new_window(html_filter_print,type)
	{
		if(type==1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			if(html_filter_print*1>1) $("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="600px";
			
			if(html_filter_print*1>1) $("#table_body tr:first").show();
		}
	}
	
	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/sample_status_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}

	function openmypage_ir(type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/sample_status_report_controller.php?action=ir_popup&companyID='+companyID+'&type='+type;
		var title='IR PopUp';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=420px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var strid=this.contentDoc.getElementById("hidden_id").value;
			var strName=this.contentDoc.getElementById("hidden_name").value;

			if(type==1)
			{
				$('#txt_req_no_id').val(strid);
				$('#txt_ir_no').val(strName);
			}
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="sample_development_status_rpt">
    <div style="width:100%;" align="center">
        <?=load_freeze_divs ("../../",$permission); ?>
        <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:1050px;">
                <table class="rpt_table" width="1050" cellpadding="1" cellspacing="1" border="1" rules="all">
                   <thead>                    
                        <th width="140" class="must_entry_caption">Company</th>
                        <th width="130">Location</th>
                        <th width="120">Buyer</th>
                        <th width="70">Brand</th>
                        <th width="70">Req. No</th>
                        <th width="100">Style Ref.</th>
						<th width="70">IR/CN</th>
                        <th width="70">Comp Status</th>
                        <th width="70">Date Type</th>
                        <th width="130" colspan="2" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><?=create_drop_down("cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/sample_status_report_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/sample_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
                            <td id="location_td"><?=create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                            <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" ); ?></td>
                            <td id="brand_td"><?=create_drop_down("cbo_brand", 70, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                            <td><input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes_numeric" style="width:60px" ></td>
                            <td><input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:90px" ></td>
							<td><input type="text" name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:90px" placeholder="Browse/Write" onDblClick="openmypage_ir(1);" > <input type="hidden" name="txt_req_no_id" id="txt_req_no_id" onChange="$('#txt_req_no_id').val('');" ></td>
                            <td> 
								<?
                                    $comp_status=array(0=>"Select",1=>"Pending",2=>"Complete");
                                    echo create_drop_down( "cbo_comp_status", 70, $comp_status,"", 0, "-- Select --", 0, "" );
                                ?>
                            </td>
                            <td> 
								<?
                                    $dateType=array(1=>"Requisition",2=>"Delivery");
                                    echo create_drop_down( "cbo_date_type", 70, $dateType,"", 0, "-- Select --", 0, "" );
                                ?>
                            </td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0);" /></td>
                        </tr>
                        <tr>
                        	<td colspan="11" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
        	</fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>     
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
