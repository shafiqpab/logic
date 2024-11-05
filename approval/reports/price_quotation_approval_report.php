<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report.
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	20-01-2015
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
echo load_html_head_contents("Knitting Status Report", "../../", 1, 1, '', 1, 1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	 
	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_quotation_no*hide_quotation_id*cbo_type*cbo_date_by*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/price_quotation_approval_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		

	function fn_report_generated_reponse()
	{
	 	if(http.readyState == 4) 
		{
	  		var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1);
			show_msg('3');
			release_freezing();
	 	}		
	}

	function openmypage(type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		
		var page_link='requires/price_quotation_approval_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&type='+type;
		if(type==1) var title='Booking No Search'; else var title='Quotation No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=300px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_id=this.contentDoc.getElementById("hide_id").value;
			var hide_no=this.contentDoc.getElementById("hide_no").value;

			if(type==1)
			{
				$('#txt_booking_no').val(hide_no);
				$('#hide_booking_id').val(hide_id);	
			}
			else
			{
				$('#txt_quotation_no').val(hide_id);
				$('#hide_quotation_id').val(hide_id);
			}
		}
	}

	function openImg(quotation_no,action)
	{
		var page_link='requires/price_quotation_approval_report_controller.php?action='+action+'&quotation_no='+quotation_no;
		var title='Image View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');		
	}

	function generate_report(company_id,job_no,txt_po_breack_down_id,buyer_id,style_id,cost_date,type,report_cat)
	{		
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		
		var data="action="+type+"&zero_value="+zero_val+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_id+"'"+
				'&txt_costing_date='+"'"+cost_date+"'"+
				'&zero_value='+zero_val+
				'&txt_po_breack_down_id='+txt_po_breack_down_id+
				'&txt_quotation_no='+"'"+job_no+"'";

		if(report_cat==1)
		{
		http.open("POST","../../order/woven_order/requires/pre_cost_entry_controller.php",true);
		}
		if(report_cat==2) //Pre Cost v2
		{
		http.open("POST","../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;		
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#tbl_list_search tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	   '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		
		$("#tbl_list_search tr:first").show();
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="priceQuotationApprovalReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1010px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1010px;">
             <table class="rpt_table" width="1010" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Date Type</th>
                    <th>Quotaion ID</th>
                    <th>Date Range</th>                    
                    <th>Type</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('priceQuotationApprovalReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/price_quotation_approval_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down("cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "", 0,"");
                            ?>
                        </td>
                  
                        <td>
                        	<?
								$search_by_date=array(1=>"Quotation",2=>"Insert");
								echo create_drop_down("cbo_date_by", 100, $search_by_date, "", 0, "", "", "", 0);
							?>
                        </td>
                         <td>
                            <input type="text" name="txt_quotation_no" id="txt_quotation_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage(2);" readonly>
                            <input type="hidden" name="hide_quotation_id" id="hide_quotation_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly/>                    							
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:70px;" readonly />
                        </td>                        
                        <td>
                        	<?
								$search_by_arr=array(0=>"All",1=>"Pending",2=>"Partial Approved",3=>"Full Approved");
								echo create_drop_down("cbo_type", 100, $search_by_arr, "", 0, "", "", "", 0);
							?>
                        </td> 
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()"/>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    	</div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
