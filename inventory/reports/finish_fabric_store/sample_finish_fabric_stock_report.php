<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	18-08-2018
Updated by 		: 		
Update date		:  	   
QC Performed BY	:		
QC Date			:	
Comments		: 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

	require_once('../../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Sample Finish Fabric Closing Stock Report","../../../", 1, 1, $unicode,1,1); 
	?>	
	<script>
		var permission='<? echo $permission; ?>';
		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
		
		var tableFilters = 
		{
			//col_16: "none",
			col_operation: {
				id: ["value_req_qnty","value_recv_qnty","value_iss_ret_qnty","value_trans_id_qnty","value_tot_recv_qnty","value_recv_roll","value_issue_qnty","value_recv_ret_qnty","value_trans_out_qnty","value_grand_issue_qnty","value_issue_roll","value_stock_qnty","value_stock_roll","value_recv_balance","value_issue_balance"],
				col: [11,12,13,14,15,16,17,18,19,20,21,22,23,27,28],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}	
		var tableFilters2 = 
		{
			//col_16: "none",
			col_operation: {
				id: ["value_req_qnty","value_recv_qnty","value_iss_ret_qnty","value_trans_id_qnty","value_tot_recv_qnty","value_recv_roll","value_issue_qnty","value_recv_ret_qnty","value_trans_out_qnty","value_grand_issue_qnty","value_issue_roll","value_stock_qnty","value_stock_roll","value_recv_balance","value_issue_balance"],
				col: [12,13,14,15,16,17,18,19,20,21,22,23,27,28,29],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}	
		function openmypage_item_account()
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_acc').value+"_"+document.getElementById('txt_product_id_des').value+"_"+document.getElementById('txt_item_account_no').value;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sample_finish_fabric_stock_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=710px,height=420px,center=1,resize=0','../../')

			emailwindow.onclose=function()
			{
				var item_account_id=this.contentDoc.getElementById("txt_selected_id").value;
				var item_account_val=this.contentDoc.getElementById("txt_selected").value;
				var item_account_no=this.contentDoc.getElementById("txt_selected_no").value;
				document.getElementById("txt_product_id_des").value=item_account_id;
				document.getElementById("txt_item_acc").value=item_account_val;
				document.getElementById("txt_item_account_no").value=item_account_no;
			}
		}

		function openmypage_booking()
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			var companyID = $("#cbo_company_name").val();
			//var buyer_name = $("#cbo_buyer_id").val();
			var cbo_year_id = $("#cbo_year").val();
			var cbo_booking_type = $("#cbo_booking_type").val();

			var page_link='requires/sample_finish_fabric_stock_report_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id+'&booking_type='+cbo_booking_type;
			var title='Booking No Search';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var booking_no=this.contentDoc.getElementById("hide_job_no").value;
				//alert(booking_no);
				$('#txt_booking_no').val(booking_no);
				//$('#txt_job_id').val(job_id);	 
			}
		}
		
		function generate_report(operation)
		{
			var booking_no= $("#txt_booking_no").val().trim();
			var txt_date_from= $("#txt_date_from").val().trim();
			var txt_date_to= $("#txt_date_to").val().trim();
			var validation_str = "";var validation_msg = "";
			if(txt_date_from == "" || txt_date_to=="")
			{
				validation_str = "*txt_booking_no";
				validation_msg = "*Date From*Date To";
			}
			else if(booking_no == "")
			{
				validation_str = "*txt_date_from*txt_date_to";
				validation_msg = "*Booking No";
			}
			
			if( form_validation('cbo_company_name*cbo_item_category_id'+validation_str,'Company Name*Item Category'+validation_msg)==false )
			{
				return;
			}
			
			var report_title=$( "div.form_caption" ).html(); 
			var cbo_company_name = $("#cbo_company_name").val();
			var cbo_item_category_id = $("#cbo_item_category_id").val();
			var txt_product_id_des = $("#txt_product_id_des").val();
			var txt_product_id = $("#txt_product_id").val();
			var cbo_year = $("#cbo_year").val();
			var cbo_booking_type = $("#cbo_booking_type").val();
			var txt_booking_no = $("#txt_booking_no").val();
			var from_date = $("#txt_date_from").val();
			var to_date = $("#txt_date_to").val();
			var cbo_value_with = $("#cbo_value_with").val();
			var cbo_uom = $("#cbo_uom").val();
			var cbo_year = $("#cbo_year").val();
			var cbo_store_id = $("#cbo_store_id").val();
			if(operation==1)
			{
				var action = "generate_report";
			}
			else
			{
				var action = "generate_report_show2";
			}
			var dataString = "&cbo_company_name="+cbo_company_name+"&txt_booking_no="+txt_booking_no+"&cbo_item_category_id="+cbo_item_category_id+"&txt_product_id_des="+txt_product_id_des+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&cbo_value_with="+cbo_value_with+"&report_type="+operation+"&cbo_uom="+cbo_uom+"&cbo_year="+cbo_year+"&booking_type="+cbo_booking_type+"&cbo_store_id="+cbo_store_id;
			var data="action="+action+dataString;
			freeze_window(operation);
			http.open("POST","requires/sample_finish_fabric_stock_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse;  
		}
		
		function generate_report_reponse()
		{	
			if(http.readyState == 4) 
			{	 
				var reponse=trim(http.responseText).split("**");
				$("#report_container2").html(reponse[0]);  
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters2);
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
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
			$('#scroll_body tr:first').show();
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

	function openpage_fabric_booking(approve_category_book)
	{ 
		var companyID = $("#cbo_company_name").val();
		var type = $("#cbo_booking_type").val();
		var app_cat_book = approve_category_book.split("_");
		var is_approve = app_cat_book[0];
		var item_category_id = app_cat_book[1];
		var booking_no = app_cat_book[2];
		var job_no = app_cat_book[3];
		var po_ids = app_cat_book[4];
		var fabric_source = app_cat_book[5];
		var report_title ="";
		if(type==2){
			var data="action=show_fabric_booking_report6"+'&txt_booking_no='+booking_no+'&cbo_company_name='+companyID+'&id_approved_id='+is_approve+'&cbo_fabric_natu='+item_category_id+'&report_title='+report_title+'&img_source='+'../../../';

			http.open("POST","../../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		}
		else
		{
			report_title = "Sample Fabric Booking -With order";
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			}
			var data="action=show_fabric_booking_report"+'&txt_booking_no='+"'"+booking_no+"'"+'&cbo_company_name='+companyID+'&id_approved_id='+is_approve+'&cbo_fabric_natu='+item_category_id+'&cbo_fabric_source='+fabric_source+'&txt_order_no_id='+po_ids+'&txt_job_no='+"'"+job_no+"'"+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate;

			http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);

		}

		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;

	}

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{	
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}

	function openmypage(po_id,prod_id,job,style,body_part,fabric_desc,gsm,width,store_id,color,action,from_date="",batch_id="")
	{
		var companyID = $("#cbo_company_name").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_finish_fabric_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prod_id='+prod_id+'&job='+job+'&style='+style+'&body_part='+body_part+'&fabric_desc='+fabric_desc+'&gsm='+gsm+'&width='+width+'&store_id='+store_id+'&color='+color+'&from_date='+from_date+'&action='+action+'&batch_id='+batch_id, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	

	</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
		<form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
			<h3 style="width:1410px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
			<div id="content_search_panel" style="width:1410px" >      
				<fieldset>  
					<table class="rpt_table" width="1410" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th width="130" class="must_entry_caption">Company</th>
							<th width="120">Fabric Nature</th>
							<th width="90">Item Description</th>
							<th width="90">Product Id</th>
							<th width="70">Year</th>
							<th width="100">Booking Type</th>
							<th width="100">Booking No</th>
							<th width="120">UOM</th>
							<th width="120" class="must_entry_caption">Store</th>
							<th width="120">Value</th>
							<th  class="must_entry_caption">Booking Date</th>
							<th width="150"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
						</thead>
						<tbody>
							<tr>
								<td>
									<? 
									echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected,"load_drop_down( 'requires/sample_finish_fabric_stock_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
									?>                            
								</td>
								<td>
									<?php 
									echo create_drop_down( "cbo_item_category_id", 120,$item_category,"", 0, "-- All --", 1, "","","2","","","");
									//,3
									?> 
								</td>

								<td>
									<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
									<input type="hidden" name="txt_product_id_des" id="txt_product_id_des" style="width:90px;"/> <input type="hidden" name="txt_item_account_no" id="txt_item_account_no" style="width:90px;"/>
								</td>
								<td>
									<input type="text" name="txt_product_id" id="txt_product_id" style="width:80px;" class="text_boxes" placeholder="Write"/>  
								</td>
								<td>
									<?
									echo create_drop_down( "cbo_year", 70, create_year_array(),"", 0,"-- All --", date("Y",time()), "",0,"" );
									?>  
								</td>
								<td>
		                            <?   
		                                $valueWithArr=array(1=>'Sample With Order',2=>'Sample Without Order');
		                                echo create_drop_down( "cbo_booking_type", 100, $valueWithArr,"",0,"",2,"","","");
		                            ?>
		                        </td>
								<td width="100">
									<input style="width:90px;" name="txt_booking_no" id="txt_booking_no" class="text_boxes" onDblClick="openmypage_booking()" placeholder="Browse/Write"  />
									<input type="hidden" name="txt_booking_id" id="txt_booking_id" style="width:90px;"/> <input type="hidden" name="txt_item_account_no" id="txt_item_account_no" style="width:90px;"/>
								</td>
								<td width="120">
									<? 
									echo create_drop_down( "cbo_uom", 120, $unit_of_measurement ,"", 0, "", "", "", "","1,12,23,27" );
									?>
								</td>
								<td id="store_td">
		                            <?
		                            	echo create_drop_down( "cbo_store_id", 120, $blank_array,"", 1, "--Select Store--", 0, "",0 );
		                            ?>
		                        </td>
								<td width="120">
									<?   
									$valueWithArr=array(0=>'Qnty With 0',1=>'Qnty Without 0');
									echo create_drop_down( "cbo_value_with", 102, $valueWithArr,"",0,"",0,"","","");
									?>
								</td>
								<td>
									<input type="text" name="txt_date_from" id="txt_date_from" value=" <? echo date("01-m-Y", strtotime('-1 month'));?> " class="datepicker" style="width:55px;"  />                    							
									To
									<input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y");?>" class="datepicker" style="width:55px;"  />                        
								</td>
								<td>
									<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
									<input type="button" name="search2" id="search2" value="Show 2" onClick="generate_report(2)" style="width:70px" class="formbutton" />
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="11" align="center">
									<? echo load_month_buttons(1);  ?>
								</td>
							</tr>
						</tfoot>
					</table> 
				</fieldset> 
			</div>
			<br /> 
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div> 
		</form>    
	</div>
	<div style="display:none" id="data_panel"></div>
</body>  
<script>
	set_multiselect('cbo_uom','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
