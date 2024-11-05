<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Color Wise RMG Production Status Report.
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	22-12-2020
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
echo load_html_head_contents("Color Wise RMG Production Status Report", "../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{} 
	
 	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
	    var page_link='requires/color_wise_rmg_production_status_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no;
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var buyer=$("#cbo_buyer_name").val();
	    var page_link='requires/color_wise_rmg_production_status_controller.php?action=job_popup&buyer='+buyer;
		var title="Search Style Popup";
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
		var job_no = $("#txt_job_no").val();
		var order_no = $("#txt_order_no").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_work_company_name = $("#cbo_work_company_name").val();
		
		if( form_validation('txt_production_date','Production Date')==false )
		{
			return;
		}
		// if( form_validation('cbo_company_name','Company Name')==false && form_validation('cbo_work_company_name','Working Company Name')==false )
		// {
		// 	return;
		// }
		if (cbo_work_company_name == '0' && cbo_company_name == '0'){
           alert("Please Select Company Name or Working Company Name First");return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_work_company_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*cbo_process*txt_production_date*cbo_shipment_status',"../../")+'&type='+type+'&report_title='+report_title;

		freeze_window(3);
		http.open("POST","requires/color_wise_rmg_production_status_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(2)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		if(type==1)
		{
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('summary_part').innerHTML+'</body</html>');
		}
		else
		{
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		}
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}
		
	function open_report_popup(arg,type,action)
	{
		if(type=='1' || type=='1_')
		{
			var title = "Sewing Output Popup";
		}
		else
		{
			var title = "Embellishment Send Receive Pop-up";
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/color_wise_rmg_production_status_controller.php?data='+arg+'&type='+type+'&action='+action, title, 'width=450px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1210px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1210px;">
            <table class="rpt_table" width="1210" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption" width="120" >Company</th>
                        <th class="" width="120" >Working Company</th>
                        <!-- <th class="" width="120" >Location</th> -->
                        <th width="120" >Buyer</th>
                        <th width="80">Style</th>
                        <th width="80">Order</th>
                        <th width="120">Process</th>
                        <th width="120">Shipment Status</th>
                        <th width="80" class="must_entry_caption">Production Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 120, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/color_wise_rmg_production_status_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/color_wise_rmg_production_status_controller',this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>
                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_work_company_name", 120, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/color_wise_rmg_production_status_controller',this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>

                    <!-- <td align="center" id="location_td"> 
                        <?
                            //echo create_drop_down( "cbo_location_name", 120, $blank_array,"",1, "-- Select --", "", "" );
                        ?>
                    </td> -->
                    <td align="center" id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select --", 0, "",0 );
                        ?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:80px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:80px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                    <td>
                        <?
                        $process_arr = array(1 => "Cutting", 4 => "Sewing Input", 5 => "Sewing Out", 2 => "Wash", 8 => "Finishing",  9 => "Shipment");
							echo create_drop_down( "cbo_process", 120, $process_arr,"", 0, "-- Select --", 0, "",0 );
						?>
                    </td>
                    <td>
                        <?
							$shipment_status_arr=array(2=>"Full/Partial Pending",3=>"Full Delivery/Closed");
							echo create_drop_down( "cbo_shipment_status", 120, $shipment_status_arr,"", 0, "-- Select --", 1, "",0,'','','','0,1' );
						?>
                    </td>
                    <td><input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:80px"  placeholder="To Date" value="<? echo date("d-m-Y"); ?>"  ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_generate_report(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show2" onClick="fn_generate_report(2)" />
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Short" onClick="fn_generate_report(3)" />
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Wash Status" onClick="fn_generate_report(4)" />
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Cutting Status" onClick="fn_generate_report(5)" />
                    </td>
                    
                </tr>
                </tbody>
				
            </table>
      </fieldset>
    
 </form> 
 </div>  
    <div style="padding: 5px;" id="report_container" ></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script> 
	// set_multiselect('cbo_work_company_name','0','0','','0'); 
	set_multiselect('cbo_process','0','0','','0'); 
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
