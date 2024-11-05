<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Embellishment Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq 
Creation date 	: 	10-02-2019
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
echo load_html_head_contents("Order Wise Embellishment Report", "../../", 1, 1,$unicode,'1','');

?>	

<script>
	var tableFilters = {}	
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		var job_no = document.getElementById('txt_job_no').value;
		if(job_no !="")
		{
			if(form_validation('cbo_company_name*cbo_source','Company Name*Source')==false)
			{			
				return;
			}
		}
		else
		{			
			if(form_validation('cbo_company_name*cbo_source*txt_date_from*txt_date_to','Company Name*Source*Date from*Date To')==false)
			{			
				return;
			}
		}
			
		var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_emb_type*cbo_source*cbo_party_name*cbo_buyer_name*txt_job_no*txt_date_from*txt_date_to*txt_inter_ref',"../../");
		freeze_window(3);
		http.open("POST","requires/order_wise_emb_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
		
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window1()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
					
				
			setFilterGrid("table_body",-1,tableFilters);		
			show_msg('3');
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function generate_worder_report(action,txt_booking_no,txt_job_no,cbo_company_name,cbo_buyer_name,txt_booking_date,txt_delivery_date,cbo_currency,cbo_supplier_name,hidden_supplier_id,cbo_pay_mode,txt_exchange_rate,cbo_source,txt_season)
	{  
		var cbo_booking_natu='0'; var calculation_basis='1';var cbo_is_short='1';var cbo_template_id='1'; var cbo_level='2'; var show_comment=1;
		
		// var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		// if (r==true) zero_val="1"; else zero_val="0";hidden_supplier_id
		//var path='../';
		var data="action="+action+"&txt_booking_no="+txt_booking_no+"&txt_job_no="+txt_job_no+"&cbo_company_name='"+cbo_company_name+"'&cbo_buyer_name="+cbo_buyer_name+"&txt_booking_date="+txt_booking_date+"&txt_delivery_date='"+txt_delivery_date+"'&cbo_currency="+cbo_currency+"&cbo_supplier_name='"+cbo_supplier_name+"&hidden_supplier_id='"+hidden_supplier_id+"'&cbo_pay_mode="+cbo_pay_mode+"&txt_exchange_rate="+txt_exchange_rate+"&cbo_source="+cbo_source+"&cbo_booking_natu="+cbo_booking_natu+"&calculation_basis="+calculation_basis+"&cbo_is_short="+cbo_is_short+"&cbo_template_id="+cbo_template_id+"&txt_season="+txt_season+"&cbo_level="+cbo_level+"&show_comment="+show_comment;
		http.open("POST","../../order/woven_order/requires/print_booking_urmi_controller.php",true);
		
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
	  
	
	function new_window1()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none"; 
        $(".flt").css('display','none');
           
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        document.getElementById('scroll_body').style.overflowY="auto"; 
        document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css('display','block');
      }

	function new_window(html_filter_print,type)
	{
		if(type==1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			("#data_panel2").hide();
			
			if(html_filter_print*1>1) $("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="400px";
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
			
			$("#data_panel2").show();
			
			if(html_filter_print*1>1) $("#table_body tr:first").show();
		}
		else if(type==2)
		{
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('embell_approval_div').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
		}
	}

	function open_emb_popup(param) //po,country,item,color,cutting,source,date from, date to , production type
	{	
		
		var page_link='requires/order_wise_emb_report_controller.php?action=open_emb_popup&data='+param;
		var title="Emblishment Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			
		}
	}

	function open_emb_balance_popup(param) //po,country,item,color,cutting,source,date from, date to , production type
	{	
		var page_link='requires/order_wise_emb_report_controller.php?action=open_emb_balance_popup&data='+param;
		var title="Emblishment Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			
		}
	}

	function openmypage_job()
	{ 
		var company_name=$("#cbo_company_name").val();
		var page_link='requires/order_wise_emb_report_controller.php?action=job_popup&company_name='+company_name;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=795px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("selected_id").value;  
			var job_no=this.contentDoc.getElementById("selected_name").value; 
 			$("#hidden_job_id").val(job_id);
			$("#txt_job_no").val(job_no);
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="order_wise_embell_approval_rpt">
<input type="hidden" id="hidden_job_id" name="hidden_job_id" value="0">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1030px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" cellpadding="1" cellspacing="2">
                   <thead>                    
                        <th width="130" class="must_entry_caption">Company Name</th>
                        <th width="130">Emb. Type</th>
                        <th width="130" class="must_entry_caption">Source</th>
                        <th width="130">Party</th>
                        <th width="130">Buyer</th>
                        <th width="100"> Job No</th>
                        <th width="100"> Internal Ref.</th>
                        <th width="130" class="must_entry_caption" colspan="2">Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td id="company_td"> 
                            <?
                               echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td id="emb_td">
							 <? 
                                echo create_drop_down( "cbo_emb_type", 130, $emblishment_name_array,"", 1, "-Select Emb Type - ", $selected, "" );
                             ?>	
                        </td>
                        <td>
                            <?
								echo create_drop_down("cbo_source",130,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/order_wise_emb_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_party','party_td');",0,'1,3');
							?>
                          </td>
                          <td id="party_td">
                            <? 
                                echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"" );
                            ?>	
                          
                          </td>
                        <td id="buyer_td">
                             <? 
                             echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); 
                            ?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Browse/Write" style="width:88px" onDblClick="openmypage_job();"></td>
                        <td><input type="text" name="txt_inter_ref" id="txt_inter_ref" class="text_boxes" placeholder="Write" style="width:88px" ></td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" ></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(2);" />
                        </td>
                    </tr>
                    </tbody>
                    
                   	<tr>
                        <td colspan="10" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table>
                
        	</fieldset>
        </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px 0"></div>
    <div id="report_container2"></div>
 </form>
 <script>
	// set_multiselect('cbo_company_name','0','0','0','0');	
	// set_multiselect('cbo_location_name','0','0','','0');
	
	// setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)]; 
	// setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_company_name,'0');getLocationId();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</body>
</html>
