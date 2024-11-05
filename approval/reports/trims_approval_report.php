<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Trims Approval Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	26-09-2016
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
echo load_html_head_contents("Trims Approval Report", "../../", 1, 1,'',1,1);

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
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*hide_job_id*cbo_type*cbo_date_by*txt_date_from*txt_date_to*txt_booking_no*hide_booking_id*cbo_booking_type',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/trims_approval_report_controller.php",true);
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
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate_2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*hide_job_id*cbo_type*cbo_date_by*txt_date_from*txt_date_to*txt_booking_no*hide_booking_id*cbo_booking_type',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/trims_approval_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse1;
}
	

function fn_report_generated_reponse1()
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

function openmypage(type)
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var buyerID = $("#cbo_buyer_name").val();
	
	var page_link='requires/trims_approval_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&type='+type;
	if(type==1) var title='Booking No Search'; else var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
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
			$('#txt_job_no').val(hide_no);
			$('#hide_job_id').val(hide_id);	
		}
	}
}

function openImgFile(job_no,action)
{
	var page_link='requires/trims_approval_report_controller.php?action='+action+'&job_no='+job_no;
	if(action=='img') var title='Image View'; else var title='File View';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	
}



function generate_report(company_id,job_no,buyer_id,style_id,cost_date,type)
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
				'&txt_job_no='+"'"+job_no+"'";

	
		http.open("POST","../../order/woven_order/requires/pre_cost_entry_controller.php",true);
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

function generate_trim_booking_report(txt_booking_no,report_type,cbo_company_name,cbo_isshort,id_approved_id,entry_form,type,i)
{
	
	var show_comment='';
	var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
	if (r==true)
	{
		show_comment="1";
	}
	else
	{
		show_comment="0";
	}
	//alert(show_comment);return; trims_booking_multi_job_controller

	//var show_comment='1';
	var data="action="+type+
				'&txt_booking_no='+"'"+txt_booking_no+"'"+
				'&cbo_company_name='+"'"+cbo_company_name+"'"+
				'&report_title='+"Multiple Job Wise Trims Booking"+
				'&show_comment='+"'"+show_comment+"'"+
				'&cbo_isshort='+"'"+cbo_isshort+"'"+
				'&id_approved_id='+"'"+id_approved_id+"'"+
				'&entry_form='+"'"+entry_form+"'"+
				'&report_type='+"'"+report_type+"'"+
				
					'&path=../../';
					
	http.open("POST","../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
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
	   }
		
	}
}


</script>
</head>
<body onLoad="set_hotkey();">
<form id="approvalStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1110px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:1110px;">
             <table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Booking No</th>
                    <th>Booking Type</th>
                    <th>Date Type</th>
                    <th>Date Range</th>
                    <th>Job No</th>
                    <th>Type</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('approvalStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_approval_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:60px" placeholder="Write/Browse" onDblClick="openmypage(1);" >
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" readonly>
                        </td>
                         <td>
                           <?
								$booking_type=array(1=>"All",2=>"Main",3=>"Short",4=>"Sample",5=>"Additional");
								echo create_drop_down( "cbo_booking_type", 70, $booking_type,"",0, "", "",'',0 );
							?>
                        </td>
                  
                        <td>
                        	<?
								$search_by_date=array(1=>"Booking",2=>"Insert",3=>"Approved");
								echo create_drop_down( "cbo_date_by", 100, $search_by_date,"",0, "", "",'',0 );
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" placeholder="From Date" readonly/>                    							
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:50px;" readonly />
                        </td>
                         <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write" >
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"Full Pending",2=>"Full Approved",3=>"Partial Approved");
								echo create_drop_down( "cbo_type", 100, $search_by_arr,"",1, "--All--", "",'',0 );
							
							?>
                        </td> 
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
							<input type="button" id="show_button2" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated1();" /></td>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
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
