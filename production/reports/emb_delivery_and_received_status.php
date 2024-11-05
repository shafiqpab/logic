<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Embellishment Report.
Functionality	:	
JS Functions	:
Created by		:   Kamrul Hasan
Creation date 	: 	22-08-2022
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
echo load_html_head_contents("Embellishment Delivery and Received Status", "../../", 1, 1,$unicode,'1','');

?>	

<script>
	var tableFilters = {}	
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		var po_no = document.getElementById('txt_po_no').value;
		if(po_no !="")
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
			
		var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_source*cbo_party_name*cbo_search_by*txt_po_no*hidden_po_id*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/emb_delivery_and_received_status_controller.php",true);
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
	  
	
	function new_window1()
    {
        // document.getElementById('scroll_body').style.overflow="auto";
        // document.getElementById('scroll_body').style.maxHeight="none"; 
        // $(".flt").css('display','none');
           
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        // document.getElementById('scroll_body').style.overflowY="auto"; 
        // document.getElementById('scroll_body').style.maxHeight="400px";
        // $(".flt").css('display','block');
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
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('embell_approval_div').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
		}
	}

	

	function openmypage_order()
	{ 
		var company_name=$("#cbo_company_name").val();
		var search_by=$("#cbo_search_by").val();
		//alert(search_by);
		
	  
		if(form_validation('cbo_company_name','Company Name')==false)
		{			
				return;
		}
		
	   	var garments_nature=document.getElementById('garments_nature').value;
		var page_link='requires/emb_delivery_and_received_status_controller.php?action=order_popup&company_name='+company_name+'&garments_nature='+garments_nature+'&search_by='+search_by;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var id=this.contentDoc.getElementById("selected_id").value;  
			var po_no=this.contentDoc.getElementById("selected_name").value; 
			var style_no=this.contentDoc.getElementById("txt_selected_style_no").value; 
			//console.log(style_no); 
 			$("#hidden_po_id").val(id);
			$("#txt_po_no").val(po_no);
			$("#txt_po_no").val(style_no);
			$("#txt_job_no").val(job_no);
			
			  
		}
	}
 function dynamic_ttl_change(type)
 {
	//alert(type);
	if(type==1)
	{
		$("#search_by_text").text(' Select PO Number');

	}else if(type==2)
	{
		$("#search_by_text").text(' Select Job Number');
	}else if(type==3)
	{
		$("#search_by_text").text(' Select Style Number');
	}
 }
</script>

</head>

<body onLoad="set_hotkey();">
<form id="order_wise_embell_approval_rpt">
<input type="hidden" id="hidden_job_id" name="hidden_job_id" value="0">
 
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:950px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:950px;">
                <table class="rpt_table" width="950" cellpadding="1" cellspacing="2">
                   <thead>                    
                        <th width="130" class="must_entry_caption">Company Name</th>
                       
                        <th width="130" class="must_entry_caption">Source</th>
                        <th width="130">Party</th>
						<th width="130">Search By</th>
                    
                         <th width="130" id="search_by_text">Select</th>
                        <th width="150" class="must_entry_caption"> Production Date Range</th>
                        <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:40px" value="Reset" /></th>
                     </thead>
                    <tbody>
                    <tr class="general">
						
                        <td id="company_td"> 
                            <?
                               echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                       
                        <td>
                            <?
								echo create_drop_down("cbo_source",130,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/emb_delivery_and_received_status_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_party','party_td');",0,'1,3');
							?>
                          </td>
                          <td id="party_td">
                            <? 
                                echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"" );
                            ?>	
                          
                          </td>
						  <td>
						 
						   <?
						
						    $search_by_arr=array(1=>"Po No",2=>"Job No", 3=>"Style No");
						   ?>
                            <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", 1,"dynamic_ttl_change(this.value);" );
                            ?>
                            
                        </td>
                       
                           <td>
						     
                        	<input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" placeholder="Browse/Write"  ondblclick="openmypage_order();">		
							<input type="hidden" id="hidden_po_id">			

                    	</td>
                        <td>
                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" >
                    	</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:40px" value="Show" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table cellpadding="1" cellspacing="2">
                    <tr>
                        <td>
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
