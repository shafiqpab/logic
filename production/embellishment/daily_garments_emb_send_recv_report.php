<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  .
Functionality	:	
JS Functions	:
Created by		:	Md. Reaz Uddin 
Creation date 	: 	25-10-2018
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
//die;
echo load_html_head_contents("Production Status Summary Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
			
	function fn_report_generated(param)
	{
		var txt_search_text=document.getElementById('txt_search_text').value;
		//var order_no=document.getElementById('txt_order_no').value;
		
		//var style_no=document.getElementById('txt_style_no').value;
		//alert(style_no);
		if(param==1)
		{
			if(form_validation('cbo_w_company_name*txt_search_text*txt_date_from*txt_date_to','Company*Search*From date Fill*To date Fill')==false)
			{
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			
			var data="action=report_generate"+get_submitted_data_string('cbo_w_company_name*cbo_company_name*cbo_location_id*cbo_production_type*cbo_buyer_name*txt_date_from*txt_date_to*cbo_search_by*txt_search_text',"../../")+'&report_title='+report_title;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/daily_garments_emb_send_recv_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		if(param==2)
		{
			if(txt_search_text=="")
			{	
				if(form_validation('cbo_w_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_w_company_name','Company')==false)
				{
					return;
				}
			}

			var report_title=$( "div.form_caption" ).html();
			
			var data="action=report_generate_date"+get_submitted_data_string('cbo_w_company_name*cbo_company_name*cbo_location_id*cbo_production_type*cbo_buyer_name*txt_date_from*txt_date_to*cbo_search_by*txt_search_text',"../../")+'&report_title='+report_title;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/daily_garments_emb_send_recv_report_controller.php",true);
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
			//alert(reponse[0]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_total_sew_out","value_total_spent_min","value_total_produced_min","value_total_cm_cost_earning","value_total_fob_earning","value_total_cm_cost","value_total_profit_loss","value_total_po_qty_pcs","value_total_po_value","value_total_shipment_value"],
					col: [7,8,9,12,13,14,15,16,17,18],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			
			
			//append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1);
			//alert(document.getElementById('graph_data').value);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}
	
	function search_by_text_change(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Style Ref";
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Order";
		}
		
	}

function openall_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var job_year = $("#cbo_job_year").val();
		var txt_search_text = $("#txt_search_text").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/daily_garments_emb_send_recv_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_search_text='+txt_search_text+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order_id").val(style_des);
			$("#txt_search_text").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}
	function fnc_chng_orderNo(orderNos)
	{
		$("#txt_order_id").val("");
		$("#txt_order_id_no").val(""); 
		$("#txt_search_text").val(""); 
	}	

</script>
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1100px;" id="content_search_panel">
            <table class="rpt_table" width="1100" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead> 
                	<tr>                   
                    <th class="must_entry_caption">Working Company</th>
					<th>Location</th>
					<th>Production Type</th>
                    <th>Search By</th>
					<th>Company</th>
                    <th id="search_by_th_up">Order No</th>					
					<th>Year</th>
                    <th>Buyer</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Production Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px;float:right" value="Reset" onClick="reset_form('costSheetReport_1','report_container*report_container2','','','')" />  </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td> 
                        <?
                        echo create_drop_down( "cbo_w_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_garments_emb_send_recv_report_controller',this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                        </td>
                        <td id="location_td">
                        <? 
                        echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", $selected, "",0,"" );
                        ?>
                        </td>
                        <td id="store_td">
                        <? 
					
                        echo create_drop_down( "cbo_production_type", 150, $production_type, 1, "-- Select One --", $selected,"5",0,0);
                        ?>
                        </td>
                        <td align="center">	
                        <?
                        $search_by_arr=array(1=>"Style Ref",2 =>"Order No");
                        echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", 2,"search_by_text_change(this.value);",0 );
                        ?>
                        </td>
						<td> 
                        <?
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_garments_emb_send_recv_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                        </td>
                        <td align="center">	
                        <input type="text" name="txt_search_text" id="txt_search_text"   class="text_boxes" placeholder="Browse or Write"   />
						 <!--<input type="text" name="txt_search_text" id="txt_search_text"  ondblclick="openall_order()" onKeyUp="fnc_chng_orderNo(this.value)"  class="text_boxes" placeholder="Browse or Write"   />-->
						<input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                        </td>
						
						<td align="center">
						<?
							$year_current=date("Y");
							echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",$year_current,'','');
						?>
                    </td>
                        <td id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                        ?>
                        </td>
                        <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                        </td>
                        <td>
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" >
                        </td>
                        <td>
                        <input type="button" id="show_button" class="formbutton" style="width:60px; float:right" value="Sent" onClick="fn_report_generated(1)" />
                        </td>
						<td>
                        <input type="button" id="show_button" class="formbutton" style="width:60px; float:right" value="Sent & Rcvd" onClick="fn_report_generated(2)" />
                        </td>
                        
                    </tr>
                </tbody>
                <tfoot>
                 	<tr align="center"  class="general">
                        <td colspan="9">
                        	<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
            </table> 
          </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//set_multiselect('cbo_buyer_name','0','0','','');
//set_multiselect('cbo_location_id','0','0','','');
</script>
</html>