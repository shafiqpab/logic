<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report.
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	26-04-2014
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);
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
	    var page_link='requires/daily_cutting_inhand_report_urmi_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_order_no").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	 }
	 
	 
	 
	function open_job_no()
	{
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/daily_cutting_inhand_report_urmi_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	 }
	 
	 

	function fn_generate_report(type)
	{
		if( form_validation('cbo_work_company_name*txt_production_date','Working Company Name*Production Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_work_company_name*cbo_floor_name*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*txt_production_date',"../../")+'&type='+type+'&report_title='+report_title;

		// var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*txt_job_no*hidden_job_id*txt_production_date',"../../");

		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_cutting_inhand_report_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
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
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function openmypage_cutting_sewing_total(po,item,cutting,color,type,action)
	{
		var data=po+'**'+item+'**'+cutting+'**'+type+'**'+color;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=650px,height=450px,center=1,resize=0,scrolling=0','../');
	}
	 function openmypage_fab_issue(batch_id,action)
	{

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?action='+action+'&batch_id='+batch_id, 'Remarks View', 'width=680px,height=250px,center=1,resize=0,scrolling=0','../');
}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type,color_id,today_total)
	{
		
	var popup_width=width;
	var popup_height=height;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id+'&today_total='+today_total, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 	
	
  function openmypage_swing(company_id,order_id,order_number,insert_date,type,action,width,height,resource,color_id,today_total)
	{
	var popup_width=width;
	var popup_height=height;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&resource='+resource+'&color_id='+color_id+'&today_total='+today_total, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 
	
	function today_fab_recv_po(company_id,order_id,color_id,prod_date,type)
	{
		var popup_width=560;
		var popup_height=380;
		//alert(type);
		if(type==1) 
		{ 
			var action="today_fabric_recv_qty";
			var title="Today Fabric  Recv Details";
		}
		else  
		{ 
			action="total_fabric_recv_qty";
			var title="Total Fabric  Recv Details";
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?company_id='+company_id+'&action='+action+'&order_id='+order_id+'&color_id='+color_id+'&prod_date='+prod_date, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 
	//function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
	function openmypage(po_break_down_id,item_id,action,country_id)
	{
		/*alert(po_break_down_id+'*'+item_id+'*'+action+'*'+country_id);
		return;*/
		//alert(po_break_down_id);
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
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/daily_cutting_inhand_report_urmi_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}	 	 		 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1510px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1510px;">
            <table class="rpt_table" width="1510" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="210" >Working Company</th>
                        <th class="" width="150" >Location</th>
                        <th class="" width="150" >Floor</th>
                        <th width="130" >Buyer Name</th>
                        <th width="60">Job Year</th>
                        <th width="100">Job No</th>
                        <th width="100">Order No </th>
                        <!--<th width="100">Shipping Status</th>-->
                        <th width="100" class="must_entry_caption">Production Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_work_company_name", 200, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                        ?>
                    </td>

                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
                        ?>
                    </td>

                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
                        ?>
                    </td>
                    <td align="center">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 130, "SELECT a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 group by  a.id,a.buyer_name  order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                   <?php /*?> <td>
                        <?
							echo create_drop_down( "cbo_shipping_status", 100, $shipment_status,"", 0, "-- Select Shipping Status", 0, "",0 );
						?>
                    </td><?php */?>
                    <td><input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:70px"  placeholder="To Date" value="<? echo date("d-m-Y"); ?>"  ></td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_generate_report(2)" />
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 3" onClick="fn_generate_report(3)" />
                         <input type="button" id="show_button" class="formbutton" style="width:84px" value="Cutting Status" onClick="fn_generate_report(4)" />
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
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
