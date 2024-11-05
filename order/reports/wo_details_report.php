<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Non-Order Wise Booking Report.
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	30-07-2015
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
echo load_html_head_contents("Order Non-Order Wise Booking", "../../", 1, 1,$unicode,1,1);
?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

	var tableFilters =
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_wo_qnty","value_tot_wo_value","value_tot_precost_value","value_tot_deference","value_tot_deference_per","value_tot_receive_qnty","value_tot_receive_value","value_tot_rcv_balance"],
		col: [19,21,23,24,25,27,28,29],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	/*var tableFilters1 =
	{
		col_35: "none",
		col_5: "select",
		col_operation: {
		id: ["tot_fin_fab_qnty","tot_grey_fab_qnty"],
		col: [17,18],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}*/


	function openmypage_wo()
	{
		if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
		{
			return;
		}
		else
		{
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year_id").val()+"_"+$("#cbo_category_id").val()+"_"+$("#cbo_wo_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_details_report_controller.php?data='+data+'&action=wo_no_popup', 'Wo No Search', 'width=660px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_wo_id");
				var theemailval=this.contentDoc.getElementById("txt_wo_no");
				if ( theemailval.value!="" )
				{
					freeze_window(5);
					$("#hidd_wo_id").val(theemailid.value);
					$("#txt_wo_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}


	function fn_report_generated(operation)
	{

			if ($("#txt_wo_no").val()!='' ||  $("#txt_item_no").val()!='' ||  $("#txt_search_common").val()!='')
			{
				if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
				{
					return;
				}

			}
			else
			{
				if(form_validation('cbo_category_id*txt_date_from*txt_date_to','cbo_category_id*Date From*Date TO')==false)
				{
					return;
				}
			}


			var report_title=$( "div.form_caption" ).html();
			if(operation==0){
				var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_wo_type*cbo_category_id*cbo_buyer_id*cbo_year_id*txt_wo_no*hidd_wo_id*txt_date_from*txt_date_to*cbo_supplier*txt_date_category*txt_item_no*hidd_item_id*cbo_search_type*txt_search_common*txt_job_po_id*cbo_within_group_id',"../../")+'&report_title='+report_title;
			}
			else if(operation==2){
				var data="action=report_generate_3"+get_submitted_data_string('cbo_company_id*cbo_wo_type*cbo_category_id*cbo_buyer_id*cbo_year_id*txt_wo_no*hidd_wo_id*txt_date_from*txt_date_to*cbo_supplier*txt_date_category*txt_item_no*hidd_item_id*cbo_search_type*txt_search_common*txt_job_po_id*cbo_within_group_id',"../../")+'&report_title='+report_title;
			}
			else if(operation==3){
				var data="action=report_generate_4"+get_submitted_data_string('cbo_company_id*cbo_wo_type*cbo_category_id*cbo_buyer_id*cbo_year_id*txt_wo_no*hidd_wo_id*txt_date_from*txt_date_to*cbo_supplier*txt_date_category*txt_item_no*hidd_item_id*cbo_search_type*txt_search_common*txt_job_po_id*cbo_within_group_id',"../../")+'&report_title='+report_title;
			}
			else if(operation==4){
				var data="action=report_generate_5"+get_submitted_data_string('cbo_company_id*cbo_wo_type*cbo_category_id*cbo_buyer_id*cbo_year_id*txt_wo_no*hidd_wo_id*txt_date_from*txt_date_to*cbo_supplier*txt_date_category*txt_item_no*hidd_item_id*cbo_search_type*txt_search_common*txt_job_po_id*cbo_within_group_id',"../../")+'&report_title='+report_title;
			}
			else{
				var data="action=report_generate_2"+get_submitted_data_string('cbo_company_id*cbo_wo_type*cbo_category_id*cbo_buyer_id*cbo_year_id*txt_wo_no*hidd_wo_id*txt_date_from*txt_date_to*cbo_supplier*txt_date_category*txt_item_no*hidd_item_id*cbo_search_type*txt_search_common*txt_job_po_id*cbo_within_group_id',"../../")+'&report_title='+report_title;
			}
			
			freeze_window(3);
			http.open("POST","requires/wo_details_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;

	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			//var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		//show_msg('3');
			var cat_id=$("#cbo_category_id").val();
			//setFilterGrid("table_body",-1);
			//,tableFilters
			/*if(cat_id==2)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(cat_id==4)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}*/
			//release_freezing();
			var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["value_tot_wo_qnty","value_tot_wo_value","value_tot_precost_value","value_tot_deference","value_tot_deference_per","value_tot_receive_qnty","value_tot_receive_value","value_tot_rcv_balance","value_tot_issue_qnty","value_tot_issue_value","value_tot_issue_balance"],
					   col: [15,17,19,20,21,23,24,25,26,27,28],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						
				 }
			setFilterGrid("table_body",-1,tableFilters);
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}

	function openmypage_inhouse(book_id,po_id,item_name,item_description,mst_id,popup_type,type,item_color,action,brand_supp="")
	{
		var popup_width='870px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_details_report_controller.php?book_id='+book_id+'&po_id='+po_id+'&item_name='+item_name+'&item_description='+item_description+'&mst_id='+mst_id+'&popup_type='+popup_type+'&type='+type+'&color='+item_color+'&action='+action+'&brand_supp='+brand_supp, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}


	function booking_report_generate(company,booking_no,fabric_natu,type)
	{
		var report_title='Pro Forma Invoice V2';		
		print_report( company+'*'+booking_no+'*'+4, "print_f", "../../commercial/import_details/requires/pi_print_urmi")		
	}


	function openmypage_item()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_supplier").val()+"_"+$("#cbo_wo_type").val()+"_"+$("#cbo_category_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_details_report_controller.php?data='+data+'&action=item_popup', 'Item Group Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_item_id");
				var theemailval=this.contentDoc.getElementById("txt_item_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#hidd_item_id").val(theemailid.value);
					$("#txt_item_no").val(theemailval.value);
					release_freezing();
				}
			}

	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
	//	var cbo_year_id = $("#cbo_year").val();
		var cbo_search_type=document.getElementById('cbo_search_type').value;	
		var search_common=document.getElementById('txt_search_common').value;
		
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/wo_details_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_search_type='+cbo_search_type+'&search_common='+search_common;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_search_common').val(job_no);
			$('#txt_job_po_id').val(job_id);	 
		}
	}
	function change_search_type(str)
	{
		$('#txt_search_common').val('');
		$('#txt_job_po_id').val('');
		if(str==1)
		{
			document.getElementById('search_by_td_up').innerHTML="Job No";
			//$('#search_by_th_up').css('color','blue');
			$('#txt_search_common').attr('placeholder','Wirte/Browse');
			$('#txt_search_common').attr('onDblClick','openmypage_job()');
		}
		else if(str==2)
		{
			document.getElementById('search_by_td_up').innerHTML="Style Ref.";
			$('#txt_search_common').attr('placeholder','Wirte/Browse');
			$('#txt_search_common').attr('onDblClick','openmypage_job()');
		}
		else if(str==3)
		{
			document.getElementById('search_by_td_up').innerHTML="Internal Ref.";
			$('#txt_search_common').attr('placeholder','Wirte');
			
		


		
		}
		
	}
	function load_supplier(type_id)
	{
		if(type_id==1)
		{
			document.getElementById('th_supplier').innerHTML="Company.";
		}
		else
		{
			document.getElementById('th_supplier').innerHTML="Supplier.";
		}
		var cbo_company_id=$('#cbo_company_id').val();
		load_drop_down('requires/wo_details_report_controller', cbo_company_id+'_'+type_id, 'load_drop_down_supplier', 'supplier_td' );
	}
	function print_button_setting()
	{
		//$('#data_panel').html('');
		get_php_form_data($('#cbo_company_id').val(),'company_wise_report_button_setting','requires/wo_details_report_controller' ); 
	}
	function fn_on_change()
	{
		var cbo_company_name = $("#cbo_company_id").val();
		load_drop_down( 'requires/wo_details_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
		load_supplier($('#cbo_within_group_id').val());
		print_button_setting();
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <form name="wofbreport_1" id="wofbreport_1" autocomplete="off" >
    <h3 style="width:1570px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1570px;">
            <table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="150" class="must_entry_caption">Company Name </th>
                    <th width="130">Buyer</th>
                    <th width="100">Within Group</th>
                    <th width="130" id="th_supplier">Supplier</th>
                    <th width="110">WO Type</th>
                    <th width="110" class="must_entry_caption">Item Category</th>
                    <th width="110">Item Group</th>
                    <th width="60">WO Year</th>
                    <th width="75">WO No.</th>
                    <th>Search Type</th>
                    <th width="75" id="search_by_td_up">Job No.</th>
                    <th width="160">Date</th>
                    <th width="100">Date Category</th>
                    <th width="150"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('wofbreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                 <tbody>
                    <tr class="general">
                        <td>
						<?
							echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/wo_details_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/wo_details_report_controller');load_supplier($('#cbo_within_group_id').val());","","3");
						?>
						</td>
                        <td id="buyer_td">
							<?
								echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "--Select Buyer--", $selected, "",1,"" );
                            ?>
                        </td>
                         <td>
							<?
								//$within_group_arr=array(1=>"Short",2=>"Main",3=>"Sample With Order",4=>"Sample Without Order");
								echo create_drop_down( "cbo_within_group_id", 100, $yes_no,"", 1, "--All--", $selected, "load_supplier(this.value)",0,"","" );
                            ?>
                        </td>
                        <td id="supplier_td">
							<?
								echo create_drop_down( "cbo_supplier", 130, $blank_array,"", 1, "--Select Supplier--", $selected, "","","" );
                            ?>
                        </td>
                        <td>
							<?
								$wo_type=array(1=>"Short",2=>"Main",3=>"Sample With Order",4=>"Sample Without Order",5=>"Additional Booking");
								echo create_drop_down( "cbo_wo_type", 110, $wo_type,"", 1, "--All--", $selected, "",0,"","" );
                            ?>
                        </td>
                        <td>
						<?
						//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
							echo create_drop_down( "cbo_category_id", 110, $item_category,"", 1, "--Select Category--", 4, "",0,"4,25","" );
                        ?>
                        </td>

                         <td>
                            <input type="text" name="txt_item_no" id="txt_item_no" class="text_boxes" style="width:75px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_item_id')" onDblClick="openmypage_item();"  />
                            <input type="hidden" id="hidd_item_id" name="hidd_item_id" style="width:70px" />
                        </td>
                        <td>
							<?
								//$selected_year=date("Y");
								echo create_drop_down( "cbo_year_id", 60, $year,"", 1, "--ALL--", 0, "",0,"","" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_wo();" readonly />
                            <input type="hidden" id="hidd_wo_id" name="hidd_wo_id" style="width:50px" />
                        </td>
                       
                        <td>
                            <? 
								$search_by = array(1 => 'Job No', 2 => 'Style Ref', 3 => 'Internal Ref');
 								$dd = "change_search_type(this.value)";
                             
								echo create_drop_down("cbo_search_type", 70, $search_by, "", 0, "--Select--", "", $dd, 0);
								
                            ?>
                        </td>
                        <td id="search_by_td">
                           <input type="text" style="width:70px" class="text_boxes" name="txt_search_common" id="txt_search_common" onDblClick="openmypage_job()" placeholder="Write/Browse"/>
                            <input type="hidden" id="txt_job_po_id" name="txt_job_po_id"/>
						
                        </td>
                        
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td>
							<?
								$date_cat=array(1=>'Booking Date',2=>'Delivery Date');
								echo create_drop_down( "txt_date_category", 100, $date_cat,"", 0, "", 1, "",0,"","" );
                            ?>
                        </td>
                        <td align="center">
                            <input type="button" id="show_button1" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
							<input type="button" id="show_button2" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="12" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
						<td colspan="3" align="center">
							<input type="button" id="show_button3" class="formbutton" style="width:70px" value="Show 3" onClick="fn_report_generated(2)" />
							<input type="button" id="show_button4" class="formbutton" style="width:70px" value="Show 4" onClick="fn_report_generated(3)" />
							<input type="button" id="show_button5" class="formbutton" style="width:70px" value="Show 5" onClick="fn_report_generated(4)" />
						</td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form>
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_company_id','0','0','','0','fn_on_change()');
</script> 

<?
	$sql=sql_select("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name");
	$company_id='';
	$is_single_select=0;
	if(count($sql)==1){
		$company_id=$sql[0][csf('id')];
		$is_single_select=1;
		?>
		<script>
		set_multiselect('cbo_company_id','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
		get_php_form_data('<? echo $company_id?>','company_wise_report_button_setting','requires/wo_details_report_controller' ); 
		</script> 
		<?
	}
?>
</html>
