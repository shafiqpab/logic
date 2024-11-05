<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Buyer-Order Wise Knitting Status Report
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad Shahriar 
Creation date 	: 	21-08-2013
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
echo load_html_head_contents("Buyer-Order Wise Knitting Status Report", "../../", 1, 1,'','1','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption").html();
		if(type==1){
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*hide_buyer_id*cbo_year*txt_job_no*txt_order_no*hide_order_id*cbo_type*txt_date_from*txt_date_to*cbo_search_by*txt_search_common',"../../")+'&report_title='+report_title;
		}
		else if(type==2)
		{
			var data="action=order_wise_knitting_tna_report"+get_submitted_data_string('cbo_company_name*hide_buyer_id*cbo_year*txt_job_no*txt_order_no*hide_order_id*cbo_type*txt_date_from*txt_date_to*cbo_search_by*txt_search_common',"../../")+'&report_title='+report_title;
		}
		freeze_window(3);
		http.open("POST","requires/buyer_order_wise_knitting_status_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
			show_msg('3');
			release_freezing();
		}
	}

	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/buyer_order_wise_knitting_status_report_controller.php?action=order_no_search_popup&companyID='+companyID;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function openmypage_buyer()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/buyer_order_wise_knitting_status_report_controller.php?action=buyer_name_search_popup&companyID='+companyID;
		var title='Buyer Info Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var buyer_name=this.contentDoc.getElementById("hide_buyer_name").value;
			var buyer_id=this.contentDoc.getElementById("hide_buyer_id").value;
			
			$('#txt_buyer_name').val(buyer_name);
			$('#hide_buyer_id').val(buyer_id);	 
		}
	}
	
	function openmypage_greyAvailable(po_id,action,data)
	{
		if(action=="grey_available") 
		{
			popup_width='500px';
			var title='Grey Available Info';
		}
		else if(action=="total_receiv_breakdown") 
		{
			popup_width='500px';
			var title='Total Receive Dtls';
		}
		else
		{
			popup_width='750px';
			var title='Grey and Yarn Info';
		}
		
		var page_link='requires/buyer_order_wise_knitting_status_report_controller.php?action='+action+'&po_id='+po_id+'&data='+data;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=230px,center=1,resize=1,scrolling=0','../');
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function caption_change( val )
	{
		if(val==1)
		{
			$('#date_td').html('Plan Date');
		}
		else
		{
			$('#date_td').html('TNA Date');
		}
	}

	
</script>
</head>

<body onLoad="set_hotkey();">
<form id="knitttingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1070px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
			<fieldset style="width:1050px;">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Search By</th>
                        <th id="search_by_td_up">Enter File No</th>
                        <th>Job Year</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th>Type</th>
                        <th colspan="2" id="date_td">TNA Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitttingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_buyer_name" id="txt_buyer_name" class="text_boxes" style="width:120px" placeholder="Browse" onDblClick="openmypage_buyer();" readonly>
                                <input type="hidden" name="hide_buyer_id" id="hide_buyer_id" readonly>
                            </td>
                             <td align="center">	
                    	<?
                       		$po_search_by_arr=array(1=>"File No",2=>"Ref. No",3=>"Style Ref");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $po_search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                       	 </td>  
                          <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                       	 </td> 	
                        
                            <td>
								<?
                                    echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" placeholder="Write" /></td>
                            <td>
                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                            </td>
                            <td>
                                <?
                                    $search_by_arr=array(1=>"Plan Date Wise",2=>"TNA Date Wise");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "2",'caption_change(this.value);',0 );
                                ?>
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                            </td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" /></td>               	
                        </tr>
                        <tr>
                            <td colspan="10" align="center">
                                <? echo load_month_buttons(1); ?>
                             </td>
                             <td>
                                <input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show 2" onClick="fn_report_generated(2)" />
                            </td>
                        </tr>
                    </tbody>        
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form> 
</body>
<script>
	//set_multiselect('cbo_buyer_name','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>