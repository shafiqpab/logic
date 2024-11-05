<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report V3
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	04-Oct-2018
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
echo load_html_head_contents("Daily Cutting Inhand Report V3", "../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		
		col_operation: {
		id: ["grndTotID_gt_order_qnty","grndTotID_gt_lay_prev_qnty","grndTotID_gt_lay_qnty","grndTotID_gt_tot_lay_qnty","grndTotID_gt_cutting_prev_qnty","grndTotID_gt_cutting_qnty","grndTotID_gt_tot_cutting_qnty","grndTotID_gt_embroidery_rcv_qnty","grndTotID_gt_tot_embroidery_rcv_qnty","grndTotID_gt_sewing_in_prev_qnty","grndTotID_gt_sewing_in_qnty", "grndTotID_gt_tot_sewing_in_qnty","grndTotID_gt_sewing_out_prev_qnty","grndTotID_gt_sewing_out_qnty","grndTotID_gt_tot_sewing_out_qnty","grndTotID_gt_sewing_wip","grndTotID_gt_paking_finish_prev_qnty","grndTotID_gt_paking_finish_qnty","grndTotID_gt_tot_paking_finish_qnty","grndTotID_gt_carton_qnty","grndTotID_gt_finishing_wip", "grndTotID_gt_ex_fact_prev_qnty","grndTotID_gt_ex_fact_qnty","grndTotID_gt_tot_ex_fact_qnty","grndTotID_gt_ex_fact_fob","grndTotID_gt_ex_fact_wip","grndTotID_gt_ex_fact_wip_fob"],
		col: [10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function open_order_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/daily_cutting_inhand_report_urmi_v3_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			var prodDescription=this.contentDoc.getElementById("txt_selected").value;  
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
	
	 
	 
	function open_job_no()
	{
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/daily_cutting_inhand_report_urmi_v3_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
		}
	}
	 
	 

	function fn_generate_report(type)
	{
		if( form_validation('cbo_work_company_name*txt_production_date','Working Company Name*Production Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_work_company_name*cbo_floor_name*txt_floor_group*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*cbo_shipping_status*hidden_job_id*txt_order_no*hidden_order_id*txt_production_date',"../../")+'&type='+type+'&report_title='+report_title;

		 
		freeze_window(3);
		http.open("POST","requires/daily_cutting_inhand_report_urmi_v3_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{			 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}

function fn_report_generated_excel(type)
{
	if( form_validation('cbo_work_company_name*txt_production_date','Working Company Name*Production Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report_excel"+get_submitted_data_string('cbo_work_company_name*cbo_floor_name*txt_floor_group*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*cbo_shipping_status*hidden_job_id*txt_order_no*hidden_order_id*txt_production_date',"../../")+'&type='+type+'&report_title='+report_title;

		 
		freeze_window(3);
		http.open("POST","requires/daily_cutting_inhand_report_urmi_v3_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse_excel;
}	

function fn_report_generated_reponse_excel()
{
	if(http.readyState == 4)
	{
		// alert(reponse[2]);
		var reponse=trim(http.responseText).split("####");
		// alert(reponse.join('/'));		

		if(reponse[0]!='')
		{
			//alert("reponse");
			$('#download_link').removeAttr('href').attr('href','requires/'+reponse[1]);
			document.getElementById('download_link').click();
		}		
		
		show_msg('3');
		release_freezing();
	}
}
	
	function openmypage_remarks_popup(po,item,country,color,job_no,action)
	{
		var data=po+'**'+item+'**'+country+'**'+color+'**'+job_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_v3_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=850px,height=450px,center=1,resize=0,scrolling=0','../');
	}
	 

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	 
	 
	function openmypage(po_break_down_id,item_id,action,country_id)
	{
		 
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";
		
		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/daily_cutting_inhand_report_urmi_v3_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}	
	function openmypage_production_popup(po,item,color,type,day,action,title,popup_width,popup_height) 	 		 	 
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_v3_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_work_company_name').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/daily_cutting_inhand_report_urmi_v3_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_name','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)];
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{	 
	    var location_id = document.getElementById('cbo_location_name').value;

	    if(location_id !='') {
	      var data="action=load_drop_down_floor&choosenLocation="+location_id;
	      http.open("POST","requires/daily_cutting_inhand_report_urmi_v3_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_name','0','0','','0');
	          }			 
	      };
	    }         
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1406px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
	   <a id="download_link" style="text-decoration:none;"></a>
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1406px;">
            <table class="rpt_table" width="1406" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Working Company</th>
                        <th class="" width="120">Location</th>
                        <th class="" width="120">Floor</th>
                        <th width="100">Group</th>
                        <th width="120" >Buyer Name</th>
                        <th width="60">Job Year</th>
                        <th width="100">Job No</th>
                        <th width="100">Order No </th>
                        <th width="120">Shipping Status</th>
                        <th width="100" class="must_entry_caption">Production Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center" id="td_company"> 
                        <?
                            echo create_drop_down( "cbo_work_company_name", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="location_td"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 120, $blank_array,"","", "-- Select location --", "", "" );
                            // echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="floor_td"> 
                        <?
                        	echo create_drop_down( "cbo_floor_name",120, $blank_array,"","", "-- Select floor --", "", "" );
                            // echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
                        ?>
                    </td>
                    <td width="100" id="" align="center">
                    	<? 
                    		$sql_group="SELECT  group_name from lib_prod_floor where status_active=1 and  group_name is not null group by     group_name "; 
                    		echo create_drop_down( "txt_floor_group", 100, $sql_group,"group_name,group_name", 1, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>

                    <td align="center">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 120, "SELECT a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 group by  a.id,a.buyer_name  order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td>
                        <?
						
						echo create_drop_down( "cbo_year", 60, $year,"", 1, "Year--", 0, "",0 );
						?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:100px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                    <td>
                        <?
							echo create_drop_down( "cbo_shipping_status", 120, $shipment_status,"", 0, "-- Select Shipping Status", 0, "",0 );
						?>
                    </td>
                    <td><input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:70px"  placeholder="To Date" value="<? echo date("d-m-Y"); ?>"  ></td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                         <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show in Excel" onClick="fn_report_generated_excel(2)" />
                                            
                         
                     </td>
                    
                </tr>
                </tbody>
            </table>
      </fieldset>
    
 </form> 
 </div>  
    <div id="report_container" ></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script> 
	set_multiselect('cbo_work_company_name','0','0','','0'); 
	set_multiselect('cbo_location_name','0','0','','0'); 
	set_multiselect('cbo_floor_name','0','0','','0'); 
	set_multiselect('cbo_shipping_status','0','0','','0'); 

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_work_company_name,'0');getCompanyId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
