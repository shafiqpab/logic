<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report for youth
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	07-July-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Code is poetry, I try to do that!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Cutting Inhand Report for Youth", "../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["gr_order_qty","gr_today_lay_qty","gr_total_lay_qty","gr_today_cut_qty","gr_total_cut_qty","gr_today_in_qty","gr_total_in_qty"],
			col: [12,13,14,16,17,19,20],
			operation: ["sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function open_order_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/cutting_and_input_status_report_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
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
	
	function open_ref_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/cutting_and_input_status_report_controller.php?action=ref_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
		var title="Search Internal Reference Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			var prodDescription=this.contentDoc.getElementById("txt_selected").value;  
			$("#txt_int_ref_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
	 
	function open_job_no()
	{
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/cutting_and_input_status_report_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
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

		var data="action=generate_report"+get_submitted_data_string('cbo_work_company_name*cbo_floor_name*txt_floor_group*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*cbo_shipping_status*hidden_job_id*txt_order_no*hidden_order_id*txt_int_ref_no*txt_production_date',"../../")+'&type='+type+'&report_title='+report_title;

		 
		freeze_window(3);
		http.open("POST","requires/cutting_and_input_status_report_controller.php",true);
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
	
	function openmypage_remarks_popup(wo_com,location,floor,po,item,country,color,job_no,action)
	{
		var data=po+'**'+item+'**'+country+'**'+color+'**'+job_no+'**'+wo_com+'**'+location+'**'+floor;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cutting_and_input_status_report_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=850px,height=450px,center=1,resize=0,scrolling=0','../');
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
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/cutting_and_input_status_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}	
	function openmypage_production_popup(po,item,color,type,day,action,title,popup_width,popup_height) 	 		 	 
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cutting_and_input_status_report_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_work_company_name').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/cutting_and_input_status_report_controller.php",true);
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
	      http.open("POST","requires/cutting_and_input_status_report_controller.php",true);
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
    <h3 style="width:1200px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1200px;">
            <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="130" >Working Company</th>
                        <th class="" width="130" >Location</th>
                        <th class="" width="130" >Floor</th>
                        <th width="100">Group</th>
                        <th width="130" >Buyer Name</th>
                        <th width="60">Job Year</th>
                        <th width="70">Job No</th>
                        <th width="80">Order No </th>
                        <th width="80">Int. Ref. </th>
                        <th width="130">Shipping Status</th>
                        <th width="70" class="must_entry_caption">Production Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center" id="td_company"> 
                        <?
                            echo create_drop_down( "cbo_work_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="location_td"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 130, $blank_array,"","", "-- Select location --", "", "" );
                            // echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="floor_td"> 
                        <?
                        	echo create_drop_down( "cbo_floor_name", 130, $blank_array,"","", "-- Select floor --", "", "" );
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
                        echo create_drop_down( "cbo_buyer_name", 130, "SELECT a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 group by  a.id,a.buyer_name  order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td>
                        <?
						
						echo create_drop_down( "cbo_year", 60, $year,"", 1, "Year--", 0, "",0 );
						?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:70px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:80px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                    <td>
                     <input type="text" id="txt_int_ref_no"  name="txt_int_ref_no"  style="width:80px" class="text_boxes" placeholder="Internal Ref."  />
                    </td>
                    <td>
                        <?
							echo create_drop_down( "cbo_shipping_status", 130, $shipment_status,"", 0, "-- Select Shipping Status", 0, "",0 );
						?>
                    </td>
                    <td><input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:50px"  placeholder="To Date" value="<? echo date("d-m-Y"); ?>"  ></td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                                            
                         
                     </td>
                    
                </tr>
                </tbody>
            </table>
      </fieldset>
    
 </form> 
 </div>  
    <div id="report_container" style="margin:10px 0;"></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script> 
	set_multiselect('cbo_work_company_name','0','0','','0'); 
	set_multiselect('cbo_location_name','0','0','','0'); 
	set_multiselect('cbo_floor_name','0','0','','0'); 
	set_multiselect('cbo_shipping_status','0','0','','0'); 

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_work_company_name,'0');getCompanyId();") ,3000)];
	// document.getElementById('cbo_year').value='<? echo date("Y");?>';
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
