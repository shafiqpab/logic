<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create SubCon Date wise Production Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam REZA  
Creation date 	: 	02-08-2020
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
echo load_html_head_contents("SubCon Date wise Production Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
 
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["total_ord_quantity","total_cutt","total_sew","total_out_out","total_shortage"],
		col: [6,10,12,13,14],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
			
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id*cbo_type','Comapny Name*Report Type')==false)
		{
			return;
		}
		else
		{
			if(type==1){var action="action=report_generate";}
			else if(type==2){
				if (form_validation('txt_date_from*txt_date_to','Date Range*Date Range')==false)
				{
					return;
				}
				var action="action=report_generate_for_delivery";
				}
			var data=action+get_submitted_data_string('txt_order_no*cbo_company_id*cbo_buyer_id*cbo_location_id*cbo_floor_id*cbo_type*txt_date_from*txt_date_to',"../../");
			freeze_window(3);
			http.open("POST","requires/sub_con_date_wise_production_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			// document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			// var type=$('#cbo_type').val();
			
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		document.getElementById('scroll_body_summary').style.overflow="auto";
		document.getElementById('scroll_body_summary').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		
		document.getElementById('scroll_body_summary').style.overflowY="scroll";
		document.getElementById('scroll_body_summary').style.maxHeight="300px";
	}

	function openmypage_remark(po_break_down_id,item_id,country_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sub_con_date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_order(po_break_down_id,item_id,country_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sub_con_date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function prodPoppup(order_id,item_id,start_date,end_date,type,title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sub_con_date_wise_production_report_controller.php?order_id='+order_id+'&item_id='+item_id+'&type='+type+'&start_date='+start_date+'&end_date='+end_date+'&action='+action, title, 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
	}

	function rcvIssueQtyPopup(order_id,type,title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sub_con_date_wise_production_report_controller.php?order_id='+order_id+'&type='+type+'&action='+action, title, 'width=450px,height=100px,center=1,resize=0,scrolling=0','../');
	}
	

	function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
	{
		
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";	
		else
			var popupWidth = "width=750px,height=350px,";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sub_con_date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, 'Production Quantity', popupWidth+'center=1,resize=0,scrolling=0','../');
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

	function openmy_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var cbo_location_id=document.getElementById('cbo_location_id').value;
		var cbo_floor_id=document.getElementById('cbo_floor_id').value;

		var page_link="requires/sub_con_date_wise_production_report_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_location_id="+cbo_location_id;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_order_id').value=job[0];
			document.getElementById('txt_order_no').value=job[1];
			release_freezing();
		}
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <form id="orderwiseproductionreport_1">
    <div style="width:100%;" align="center">    
	<? echo load_freeze_divs ("../../",''); ?>
    <h3 style="width:970px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:970px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="130">Buyer Name</th>
                    <th width="120">Location</th>
                    <th width="120">Floor</th>
                    <th width="110">Type</th>
                    <th width="80">Order No</th>
                    <th width="" >Date Range</th><!--class="must_entry_caption"-->
                    <th width="70"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_con_date_wise_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sub_con_date_wise_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
								echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select Location--", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="floor_td">
							<? 
								echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor--", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
							<? 
								$arr = array(1=>"Show Order Wise",2=>"Show Order Location & Floor Wise",3=>"Show Order Country Wise",4=>"Show Order Country Location & Floor Wise");
								echo create_drop_down( "cbo_type", 110, $arr,"", 1, "-- Select --", 1, "",1,"" );
                            ?>
                        </td> 
                        <td>
                        	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Wr/Br Order" onDblClick="openmy_order();"/>
                        	<input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
							<? 
								/*$arr = array(1=>"ALL",2=>"Woven",3=>"Knit");
								echo create_drop_down( "txt_order_no", 80, $arr,"", 0, "-- Select --", $selected, "",0,"" );  */                            ?>
                        </td>   
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >
                            To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Production" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                        <td align="center">
							<input type="button" id="show_button" class="formbutton" style="width:70px" value="Delivery" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </tfoot>
            </table> 
            <br />
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
