<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	04-06-2014
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
echo load_html_head_contents("Dyeing Bill Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id*cbo_process_id*txt_date_from*txt_date_to','Comapny Name*Process*date_from*date_to')==false)//*txt_date_from*txt_date_to----*From Date*To Date
		{
			return;
		}
		else
		{
			if(type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_batch_no*txt_date_from*txt_date_to*cbo_body_part_search',"../../")+'&type='+type;
			}
			if(type==2)
			{
				//alert("Its Me");
			var data="action=report_generate_summary"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_batch_no*txt_date_from*txt_date_to*cbo_body_part_search',"../../")+'&type='+type;
			}
			if(type==3)
			{
				//alert("Its Me");
			var data="action=report_generate_summary_bill"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_batch_no*txt_date_from*txt_date_to*cbo_body_part_search',"../../")+'&type='+type;
			}
			if(type==4)
			{
				//alert("Its Me");
				if (form_validation('cbo_company_id*cbo_process_id*txt_batch_no*txt_date_from*txt_date_to','Comapny Name*Process*batch*date_from*date_to')==false)//*txt_date_from*txt_date_to----*From Date*To Date
				{
					return;
				}
			var data="action=report_generate_batch_wise"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_buyer_id*cbo_process_id*cbo_search_by*txt_bill_no*txt_batch_no*txt_date_from*txt_date_to*cbo_body_part_search',"../../")+'&type='+type;
			}
			freeze_window(3);
			http.open("POST","requires/dyeing_bill_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//var reponse=trim(http.responseText).split("**"); 
			var reponse=trim(http.responseText).split("####"); 
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1);
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
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="auto";
	}
	function show_progress_report_details(action,order_id,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_bill_report_controller.php?action='+action+'&order_id='+order_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
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

	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/dyeing_bill_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}
	

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1390px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1390px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
					<th width="135">Location</th>
                    <th width="125">Party </th>
                    <th width="100">Process</th>
                    <th width="80">Bill Type</th>
					<th width="110">Body Part Type</th>
					<th width="80">Bill No</th>
					<th width="80">Batch No</th>
                    <th width="250">Bill Date</th>
                    <th width="100"> <input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /> <input type="button" id="show_button" class="formbutton" style="width:80px" value="Batch Wise" onClick="fn_report_generated(4)" /> </th>
					<th width="100"> <input type="button" id="show_button" class="formbutton" style="width:80px" value="Bill Summary" onClick="fn_report_generated(3)" /> </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td  align="center"> 
                            <?
								echo create_drop_down( "cbo_company_id",135,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/dyeing_bill_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/dyeing_bill_report_controller', this.value, 'load_drop_down_location', 'location_td');"); 
                            ?>
                        </td>
						<td id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3); ?></td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 145, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <? 
								
                                echo create_drop_down( "cbo_process_id", 100, $production_process,"", 1, "-Select Process-", $selected, "","","" );
                            ?>
                        </td>
                        <td>
                            <? 
                                $search_by_arr = array(0 => "--All---", 1 => "Order", 2 => "Sample with order", 3 => "Sample without order");
                                echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "", " ",'',0 );//$bill_for
                             ?>
                        </td>
						 <td>
                            <? 
                                $cbo_body_part_search_array = array(0=>"---All---",1=>"Without Collar N Cuff",2=>"Collar N Cuff",3=>"Drawstring");
                                echo create_drop_down( "cbo_body_part_search", 110, $cbo_body_part_search_array,"",0, "", "0",'',0 );
                             ?>
                        </td>
                       <td>					   
                            <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes"  style="width:100px" placeholder="Write Bill ID" >                        
						</td>
						<td>					   
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes"  style="width:100px" placeholder="Write Batch No" >                        
						</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                        </td>
						
                        <td width="100" align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Bill" onClick="fn_report_generated(1)" />
                            
                        </td>
						<td width="100" ><input type="button" id="show_button" class="formbutton" style="width:80px" value="Date Summary" onClick="fn_report_generated(2)" /></td>
						 
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
