<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report.
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	: 	11-08-2013
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
echo load_html_head_contents("Knitting Status Report", "../../", 1, 1,'',1,1);
/*$dataArray=sql_select("select group_concat(id) as id, group_concat(booking_no) as booking_no from wo_non_ord_samp_booking_mst where booking_date < '2014-01-29' and item_category=2");
$booking_id=$dataArray[0][csf('id')];
$booking_no="'".implode("','",explode(",",$dataArray[0][csf('booking_no')]))."'";
$con = connect();
mysql_query("BEGIN");

$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",0,"id",$booking_id,0);
$delete=execute_query("delete from approval_history where mst_id in($booking_id) and entry_form=9",0);
$deleteMstHs=execute_query("delete from wo_nonord_samboo_msthtry where booking_id in($booking_id)",0);
$deletedtlsHs=execute_query("delete from wo_nonor_sambo_dtl_hstry where booking_no in($booking_no)",0);
$deletedtlsyarHs=execute_query("delete from wo_nonord_samyar_dtlhstry where booking_no in($booking_no)",0);

if($rID && $delete && $deleteMstHs && $deletedtlsHs && $deletedtlsyarHs)
{
	mysql_query("COMMIT");  
	echo $booking_id."Complete";
}
else echo "Not Complete";
disconnect($con);*/
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
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*hide_booking_id*cbo_sample_type*cbo_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/sample_booking_approval_status_report_controller.php",true);
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

function openmypage()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var buyerID = $("#cbo_buyer_name").val();
	
	var page_link='requires/sample_booking_approval_status_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID;
	title='Sample Booking No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hide_id=this.contentDoc.getElementById("hide_id").value;
		var hide_no=this.contentDoc.getElementById("hide_no").value;

		$('#txt_booking_no').val(hide_no);
		$('#hide_booking_id').val(hide_id);
		
	}
}

function generate_worder_report(booking_no,company_id,approved,type_id,entry_page)
{
	//var data="action=show_fabric_booking_report"+
		var data="action="+type_id+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&id_approved_id='+"'"+approved+"'";
				
	http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
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

function assending_func(){
		var type=$('#cbo_type').val();
			if(type==2){
				$('#cbo_ascending_by').val("2",false)
			}else{
				$('#cbo_ascending_by').prop("disabled",false)
			}
		}



</script>


</head>
 
<body onLoad="set_hotkey();">

<form id="approvalStatusReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",''); ?>
         
         <h3 style="width:1050px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:1050px;">
         <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Sample Type</th>
                    <th>Booking No</th>
                    <th>Insert Date Range</th>
                    <th>Type</th>
					<th>Ascending By .</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('approvalStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_booking_approval_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="sample_td">
                            <? $sampl_type_apprv_stat=array(2=>"Fabric",4=>"Trims");
                                echo create_drop_down( "cbo_sample_type", 100, $sampl_type_apprv_stat,"", 1, "-- Select --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:120px" placeholder="Browse" onDblClick="openmypage();" readonly>
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px;" placeholder="From Date" readonly/>                    							
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:75px;" readonly />
                        </td>
                        <td onchange="assending_func()">
                        	<?
								$search_by_arr=array(1=>"Pending",2=>"Full Approved");
								echo create_drop_down( "cbo_type", 100, $search_by_arr,"",0, "", "",'',0 );
							?>
                        </td> 
						<td>
                        	<?
								$ascending_by=array(1=>"Booking Date",2=>"Final Approved");
								echo create_drop_down( "cbo_ascending_by", 100, $ascending_by,"",0, "", "",'',0 );
							?>
                        </td> 
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
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
