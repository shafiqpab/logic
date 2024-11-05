<?
/*-------------------------------------------- Comments
Purpose			: 	This Form Will Create Rack Wise Statement Report V2
				
Functionality	:	
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	23-07-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:	Passion to write neat and clean code!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Rack Wise Statement Report V2","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	 {
		 col_operation: {
			 id: ["value_yarn_req_qnty","value_total_rcv_qty","value_total_issue_rate","value_total_trans_in_qty","value_total_rec_rate","value_total_issue","value_total_rcv_ret_qty","value_total_trns_out","value_total_iss_qty","value_total_clos_qty","value_total_rate_dlr","value_total_rate_tk","value_total_amnt"],
			 col: [22,23,24,25,26,27,28,29,30,31,32,33,34],
			 operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		 }
	 }

	function generate_report(rpt_type)
	{
		
		if( form_validation('txt_date_from*txt_date_to','from date *to date')==false )
		{
			return;
		}
  
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_shipment_status*txt_job_no*txt_job_id*txt_style_ref_id*txt_style_ref*txt_style_ref_no*txt_order_no*txt_order_id*cbo_location_id*txt_item_group*cbo_item_group*txt_order*txt_order_id_no*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_suppler_name*txt_date_from*txt_date_to*cbo_value_range_by',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/rack_wise_statement_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[2]); return;
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// setFilterGrid("table_body",-1);
			setFilterGrid("table_body",-1,tableFilters);

			if(reponse[2]==2)
			{
				// if(reponse[0]!='')
				// {
					$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
					document.getElementById('aa1').click();
				//}
			}
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$("#table_body tr:first").show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		// var cbo_year_id = $("#cbo_year").val();
		// var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/rack_wise_statement_report_v2_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	function openmypage_style()
        {
            if( form_validation('cbo_company_id','Company Name')==false )
            {
                return;
            }
            var company = $("#cbo_company_id").val();
            var buyer = $("#cbo_buyer_id").val();
            var txt_style_ref_no = $("#txt_style_ref_no").val();
            var txt_style_ref_id = $("#txt_style_ref_id").val();
            var txt_style_ref = $("#txt_style_ref").val();
            var cbo_year = $("#cbo_year_selection").val();
            var page_link='requires/rack_wise_statement_report_v2_controller.php?action=style_reference_search&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
            var title="Search Style Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var style_id=this.contentDoc.getElementById("txt_selected_id").value;
                var style_des=this.contentDoc.getElementById("txt_selected").value;
                var style_no=this.contentDoc.getElementById("txt_selected_no").value;

                $("#txt_style_ref").val(style_des);
                $("#txt_style_ref_id").val(style_id);
                $("#txt_style_ref_no").val(style_no);
            }
        }
	

	function clr_hidden(ref) 
	{
		if(ref == 1)
		{
			$("#txt_job_id").val("");
		}
		else
		{
			$("#txt_order_id").val("");
		}
	}

	function exportToExcel()
	{
		$(".fltrow").hide();
		var tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
	    var data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}
		
		var ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}
		
	    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	    document.getElementById("dlink").traget = "_blank";
		document.getElementById("dlink").download = '<?=$_SESSION['logic_erp']['user_id']."_".time();?>' + '.xls';
	    document.getElementById("dlink").click();
		$(".fltrow").show();
		// alert('ok');
	}

    function openmypage_group()
	{
		var cbo_year_selection = $("#cbo_year_selection").val()
		// alert(cbo_year_selection);return;
		var cbo_item_group = $("#cbo_item_group").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/rack_wise_statement_report_v2_controller.php?action=item_group_popup&cbo_year_selection='+cbo_year_selection+'&cbo_item_group='+cbo_item_group, 'Item Group Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_item_group").val(selected_name);
			$("#cbo_item_group").val(selected_id);
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var buyer = $("#cbo_buyer_id").val();

		var page_link='requires/rack_wise_statement_report_v2_controller.php?action=order_search&company='+company+'&buyer_name='+buyer;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id);
			$("#txt_order_id_no").val(style_des_no);
		}
	}

	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1880px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1880px;">
                <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th>                                
                            <th>Location</th>                                
                            <th>Buyer</th>
                          	<th>Item group</th>
                          	<th>Job No</th>
                            <th>Style No</th>
                            <th>In Ref</th>
                            <th>Store</th>
                            <th>Floor</th>
                            <th>Room</th>
							<th>Rack</th>
							<th>Self</th>
							<th>Bin</th>
							<th>Supplier Name</th>
							<th>Order No</th>
                            <th>Shipment Status</th>
							<th>Value Range</th>
                            <th>Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/rack_wise_statement_report_v2_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/rack_wise_statement_report_v2_controller',this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/rack_wise_statement_report_v2_controller', this.value, 'load_drop_down_store','store_td');load_drop_down( 'requires/rack_wise_statement_report_v2_controller', this.value, 'load_drop_down_supplier', 'supplier_td');" );
                            ?>                            
                        </td>
                        <td id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location_id", 80, $blank_array,"", 1, "--Select Location--", 0, "",0 );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 80, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                             <input type="text" id="txt_item_group" name="txt_item_group" class="text_boxes" style="width:70px" value="" onDblClick="openmypage_group();" placeholder="Browse" readonly />
                            <input type="hidden" id="cbo_item_group" name="cbo_item_group" />
                        </td>
						<td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse/Write" onchange="clr_hidden(1);" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
						<td align="center">
                            <input style="width:80px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()"  class="text_boxes" placeholder="Browse" />
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>
                            <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                        </td>  

						<td width="110">
                           <input style="width:100px;" name="txt_order" id="txt_order" onDblClick="openmypage_order()" class="text_boxes" placeholder="Browse or Write" />
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                        </td>

						<td  id="store_td">
							<?
							echo create_drop_down( "cbo_store_name", 70, $blank_array,"",1, "--Select store--", 1, "" );
							?>
                        </td>

						<td id="floor_td">
							<? echo create_drop_down( "cbo_floor", 80,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>

						<td id="room_td">
							<? echo create_drop_down( "cbo_room", 80,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>

						<td id="rack_td">
							<? echo create_drop_down( "txt_rack", 80,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>

						<td id="shelf_td">
							<? echo create_drop_down( "txt_shelf", 80,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>

						<td id="bin_td">
							<? echo create_drop_down( "cbo_bin", 80,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>

						<td id="supplier_td">
                            <?
                            echo create_drop_down( "cbo_suppler_name", 100, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                            ?>
                        </td>
						<td>
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width: 100px;"  placeholder="Write">
						</td>

						<td> 
                            <?
								echo create_drop_down( "cbo_shipment_status", 100,$shipment_status,"", 1,"-- All --", $selected, "",0,"" );
                            ?>
                        </td>
						<td>
                            <?
								$value_range_by=array(1=>'Value with 0',2=>'Value without 0');
                                echo create_drop_down( "cbo_value_range_by", 90, $value_range_by,"", 1, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" readonly/>
                            To			
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" readonly/>				
                        </td>
                                
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                    <tr> 
                    	<td colspan="18" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
						<td><input type="button" name="search" id="search" value="Show Excel" onClick="generate_report(2)" style="width:100px" class="formbutton" />
						<a id="aa1" href="" style="text-decoration:none" download hidden>BB</a>
					</td>
                    </tr>
                </table> 
            </fieldset>  
        </div>
        <div id="report_container" align="center" style="margin:5px 0;"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
