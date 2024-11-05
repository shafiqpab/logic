<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sourcing Approval Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	27-02-2021
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
echo load_html_head_contents("Sourcing Approval Report", "../../", 1, 1,'',1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<?=$permission; ?>';
 
function fn_report_generated()
{
	freeze_window(3);
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		release_freezing();
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*hide_job_id*cbo_type*cbo_date_by*txt_date_from*txt_date_to*txt_ref_no',"../../")+'&report_title='+report_title;
	
	http.open("POST","requires/sourcing_approval_status_report_controller.php",true);
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
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		var tableFilters = { col_0: "none" }
		setFilterGrid("tbl_list_search",-1);
		show_msg('3');
		release_freezing();
 	}
}
function fn_report_generated1()
{
	freeze_window(3);
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		release_freezing();
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*hide_job_id*cbo_type*cbo_date_by*txt_date_from*txt_date_to*txt_ref_no',"../../")+'&report_title='+report_title;
	
	http.open("POST","requires/sourcing_approval_status_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse2;
}

function fn_report_generated_reponse2()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		var tableFilters = { col_0: "none" }
		setFilterGrid("tbl_list_search",-1);
		show_msg('3');
		release_freezing();
 	}
}

function history_budget_sheet(company_id,job_no,buyer_id,style_id,cost_date,type,entry_from,garments_nature,version)
{
	var zero_val='';
	freeze_window(3);
	var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
	if (r==true) zero_val="1"; else zero_val="0";
	 
	var rate_amt=2;
	var data="action="+type+"&zero_value="+zero_val+
			'&rate_amt='+rate_amt+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_id+"'"+
			'&txt_sourcing_date='+"'"+cost_date+"'"+
			'&version='+version+
			'&txt_job_no='+"'"+job_no+"'";
			
	if(entry_from==425 )
	{
		 if(garments_nature==3)
		{
			http.open("POST","../../order/sourcing/requires/pre_cost_entry_controller_v2.php",true);
		}		
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
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
		d.close();
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
	
	var page_link='requires/sourcing_approval_status_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&type='+type;
	if(type==1) var title='Booking No Search'; else var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hide_id=this.contentDoc.getElementById("hide_id").value;
		var hide_no=this.contentDoc.getElementById("hide_no").value;
		//alert(type+'='+hide_no);
		if(type==1)
		{
			$('#txt_booking_no').val(hide_no);
			$('#hide_booking_id').val(hide_id);	
		}
		else if(type==2)
		{
			$('#txt_job_no').val(hide_no);
			$('#hide_job_id').val(hide_id);	
		}
		else
		{
			$('#txt_ref_no').val(hide_no);
		}
	}
}

function openImgFile(job_no,action)
{
	var page_link='requires/sourcing_approval_status_report_controller.php?action='+action+'&job_no='+job_no;
	if(action=='img') var title='Image View'; else var title='File View';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
}

function openApproved_no(job_id,action)
{
	var page_link='requires/sourcing_approval_status_report_controller.php?action='+action+'&job_id='+job_id;
	var title='Approve Details';	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=350px,center=1,resize=1,scrolling=0','../');	
}

function generate_report(company_id,job_no,txt_po_breack_down_id,buyer_id,style_id,cost_date,type,entry_from,garments_nature)
{
	var zero_val='';
	freeze_window(3);
	var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
	if (r==true) zero_val="1"; else zero_val="0";
	 
	var rate_amt=2;
	var data="action="+type+"&zero_value="+zero_val+
			'&rate_amt='+rate_amt+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_id+"'"+
			'&txt_sourcing_date='+"'"+cost_date+"'"+
			'&txt_po_breack_down_id='+txt_po_breack_down_id+
			'&txt_job_no='+"'"+job_no+"'";
			
	if(entry_from==158 || entry_from==425 || entry_from==521)
	{
		if(garments_nature==2)
		{
			http.open("POST","../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		}
		else if(garments_nature==3)
		{
			http.open("POST","../../order/sourcing/requires/pre_cost_entry_controller_v2.php",true);
		}
		else if(garments_nature==100)
		{
			http.open("POST","../../order/sweater/requires/pre_cost_entry_controller_v2.php",true);
		}
	}
	else
	{
		http.open("POST","../../order/woven_order/requires/pre_cost_entry_controller.php",true);
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
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
		d.close();
		release_freezing();
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
<form id="approvalStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:920px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:920px;">
             <table class="rpt_table" width="910" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="130">Buyer Name</th>
					<th width="50">Year</th>
                    <th width="80">Date Type</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th width="90">Job No</th>
                    <th width="80">Internal Ref</th>
                    <th width="100">Type</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('approvalStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:150px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sourcing_approval_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
						<td><? $year_current=date("Y"); echo create_drop_down( "cbo_year", 90, create_year_array(),"", 1,"-All Year-",$year_current, "",0,"" ); ?></td>
                        <td>
                        	<?
								$search_by_date=array(1=>"Sourcing",2=>"Insert",3=>"Approved");
								echo create_drop_down( "cbo_date_by", 80, $search_by_date,"",0, "", "",'',0 );
							?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date" readonly/></td>					
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" placeholder="To Date" readonly /></td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage(2);" readonly>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                        <td><input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px" placeholder="Br./Wr." onDblClick="openmypage(3);" ></td>
                        <td>
                        	<?
								$search_by_arr=array(0=>"Pending",1=>"Partial Approved",2=>"Full Approved");
								echo create_drop_down( "cbo_type", 100, $search_by_arr,"",0, "", "",'',0 );
							?>
                        </td> 
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated();" />
						<input type="button" id="show_button2" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated1();" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" align="center"><?=load_month_buttons(1); ?></td>
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
