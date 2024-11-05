<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:
Creation date 	:
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
echo load_html_head_contents("Order Wise Color Finich Fabric Stock Report","../../../", 1, 1, $unicode,1,1);

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation: {
		id: ["value_opening_stock","value_rcv_qnty","value_inside_iss_return","value_out_iss_return","value_trans_in","value_total_rcv","value_total_cutting_inside","value_total_cutting_outside","value_total_other_issue","value_total_rcv_return","value_total_transfer_out","value_total_issue","value_issue_amount","value_stock_qnty","value_stock_amount","value_stock_amount_tk"],
		col: [28,29,30,31,32,33,36,37,38,39,40,41,43,44,47,48],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters_2 =
	{
		col_operation: {
		id: ["value_opening_stock","value_rcv_qnty","value_inside_iss_return","value_out_iss_return","value_trans_in","value_total_rcv","value_total_cutting_inside","value_total_cutting_outside","value_total_other_issue","value_total_rcv_return","value_total_transfer_out","value_total_issue","value_issue_amount","value_stock_qnty","value_stock_amount","value_stock_amount_tk"],
		//col: [32,34,36,38,40,42,46,48,50,52,54,56,59,60,63],

		col: [33,35,37,39,41,43,47,49,51,53,55,57,60,61,65,66],
		
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report(type)
	{
		var company_name=document.getElementById('cbo_company_id').value;
		var store_name=document.getElementById('cbo_store_name').value;
		var buyer_id=document.getElementById('cbo_buyer_id').value;
		var job_no=document.getElementById('txt_job_no').value;
		var txt_internal_ref=document.getElementById('txt_internal_ref').value;
		var book_no=document.getElementById('txt_book_no').value;
		var pi_no=document.getElementById('txt_pi_no').value;		
		var pay_mode=document.getElementById('cbo_pay_mode').value;
		var supplier_id=document.getElementById('cbo_supplier_id').value;
		var value_with=document.getElementById('cbo_value_with').value;
		var get_upto=document.getElementById('cbo_get_upto').value;
		var txt_qnty=document.getElementById('txt_qnty').value;		
		var from_date=document.getElementById('txt_date_from').value;
		var to_date=document.getElementById('txt_date_to').value;
		var txt_batch_no=document.getElementById('txt_batch_no').value;
		var cbo_booking_type=document.getElementById('cbo_booking_type').value;
		//alert(txt_date_from+txt_date_to);return;

		if(company_name==0)
		{			
			alert("Please Select Company");
			return;			
		}
		else if ((from_date=='' || to_date=='') && store_name==0 && buyer_id==0 && job_no=='' && txt_internal_ref=='' && book_no=='' && pi_no=='' && pay_mode==0 && supplier_id==0) 
		{
			if( form_validation('txt_date_from*txt_date_to*cbo_store_name*cbo_buyer_id*txt_job_no*txt_internal_ref*txt_book_no*txt_pi_no*cbo_pay_mode*cbo_supplier_id','Form Date*To Date*Store Name*Buyer*Job No*Booking No*PI No*Pay Mode*Supplier')==false )
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_store_name*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*txt_job_no*txt_job_id*txt_internal_ref*txt_pi_no*hdn_pi_id*cbo_pay_mode*cbo_supplier_id*cbo_value_with*cbo_get_upto*txt_days*cbo_get_upto_qnty*txt_qnty*txt_date_from*txt_date_to*txt_batch_no*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id*cbo_box_id*cbo_booking_type',"../../../")+'&report_title='+report_title+'&cbo_report_type='+type;

		freeze_window(3);
		http.open("POST","requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php",true);
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
			if(typeof(reponse[1]) != 'undefined') {
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(reponse[2] == 1){
					setFilterGrid("table_body",-1,tableFilters);
				}
				else
				{
					setFilterGrid("table_body",-1,tableFilters_2);
				}
				show_msg('3');
			}

			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";

		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#table_body tr:first').show();
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="380px";
		$('#table_body2 tr:first').show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

    function openmypage_pinumber()
    {
        var companyID = $('#cbo_company_id').val();

        if (form_validation('cbo_company_id','Company')==false)
        {
            return;
        }

        var page_link='requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php?action=pinumber_popup&companyID='+companyID;
        var title='PI Number Info';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var pi_id=this.contentDoc.getElementById("pi_id").value;
            var pi_no=this.contentDoc.getElementById("pi_no").value;

            $('#hdn_pi_id').val(pi_id);
            $('#txt_pi_no').val(pi_no);
        }
    }

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_booking=this.contentDoc.getElementById("selected_booking").value;
			var selected_booking_entry_type=this.contentDoc.getElementById("selected_booking_entry_type").value;

			document.getElementById("txt_book_no").value=selected_booking;
			document.getElementById("cbo_booking_type").value=selected_booking_entry_type;
		}
	}

	function openmypage(po_id,body_part_id,color,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php?companyID='+companyID+'&po_id='+po_id+'&body_part_id='+body_part_id+'&color='+color+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function open_po_number(po_id)
	{
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php?po_id='+po_id+'&action=open_po_number', 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_qnty(booking_no,prod_ref,action,from_date,to_date,po_ids)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller.php?companyID='+companyID+'&booking_no='+booking_no+'&prod_ref='+prod_ref+'&action='+action+'&from_date='+from_date+'&to_date='+to_date+'&po_ids='+po_ids, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function getCompanyId() 
	{
	    var company_id = $("#cbo_company_id").val();
	    load_drop_down( 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller',company_id, 'load_drop_down_store', 'store_td' );
	    set_multiselect('cbo_store_name','0','0','','0');
	    setTimeout[($("#store_td a").attr("onclick", "disappear_list(cbo_store_name,'0');loadFloor();"), 3000)];
	    
	    load_drop_down( 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );
	}

	function loadFloor() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        load_drop_down('requires/room_rack_wise_finish_fabric_stock_report_gmts_controller', company_id+'_'+store_id, 'load_drop_down_floors', 'floor_td');
        set_multiselect('cbo_floor_id', '0', '0', '', '0');
        setTimeout[($("#floor_td a").attr("onclick", "disappear_list(cbo_floor_id,'0');loadRoom();"), 3000)]; 
    }

    function loadRoom() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        load_drop_down('requires/room_rack_wise_finish_fabric_stock_report_gmts_controller', company_id+'_'+store_id+'_'+floor_id, 'load_drop_down_rooms', 'room_td');
        set_multiselect('cbo_room_id', '0', '0', '', '0');
        setTimeout[($("#room_td a").attr("onclick", "disappear_list(cbo_room_id,'0');loadRack();"), 3000)]; 
    }

    function loadRack() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        var room_id = document.getElementById('cbo_room_id').value;
        load_drop_down('requires/room_rack_wise_finish_fabric_stock_report_gmts_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id, 'load_drop_down_racks', 'rack_td');
        set_multiselect('cbo_rack_id', '0', '0', '', '0');
        setTimeout[($("#rack_td a").attr("onclick", "disappear_list(cbo_rack_id,'0');loadShelf();"), 3000)]; 
    }

    function loadShelf() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        var room_id = document.getElementById('cbo_room_id').value;
        var rack_id = document.getElementById('cbo_rack_id').value;
        load_drop_down('requires/room_rack_wise_finish_fabric_stock_report_gmts_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id+'_'+rack_id, 'load_drop_down_shelfs', 'shelf_td');
        set_multiselect('cbo_shelf_id', '0', '0', '', '0');
        setTimeout[($("#shelf_td a").attr("onclick", "disappear_list(cbo_shelf_id,'0');loadBin();"), 3000)];
        loadBin();
    }

    function loadBin() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        var room_id = document.getElementById('cbo_room_id').value;
        var rack_id = document.getElementById('cbo_rack_id').value;
        var shelf_id = document.getElementById('cbo_shelf_id').value;
        load_drop_down('requires/room_rack_wise_finish_fabric_stock_report_gmts_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id+'_'+rack_id+'_'+shelf_id, 'load_drop_down_boxs', 'box_td');
        set_multiselect('cbo_box_id','0','0','','0'); 
    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:2260px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel- </h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:2360px;">
                <table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="120">Store Name</th>
                            <th width="120">Floor</th>
                            <th width="120">Room</th>
                            <th width="120">Rack</th>
                            <th width="120">Shelf</th>
                            <th width="120">Box</th>
                            <th width="120">Buyer</th>

                            <th width="60">Job Year</th>
                            <th width="75">Job</th>
							<th width="80">Internal Ref</th>
                            <th width="75">F.Booking No.</th>
                            <th width="100">Booking Type</th>
                            <th width="75">Batch No</th>

                            <th width="75">PI</th>
                            <th width="100">Pay Mode</th>
                            <th width="100">Supplier</th>
                            <th width="100">Value</th>

							<th>Get Upto</th>
							<th>Days</th>
							<th>Get Upto</th>
							<th>Qty.</th>
							<th class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center">
                        <td id="company_td">
                            <?
                               echo create_drop_down( "cbo_company_id", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller',this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="floor_td">
                            <?
                                echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="room_td">
                            <?
                                echo create_drop_down( "cbo_room_id", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="rack_td">
                            <?
                                echo create_drop_down( "cbo_rack_id", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="shelf_td">
                            <?
                                echo create_drop_down( "cbo_shelf_id", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="box_td">
                            <?
                                echo create_drop_down( "cbo_box_id", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                           ?>
                        </td>
                        <td>
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
						<td>
                            <input type="text" id="txt_internal_ref" name="txt_internal_ref" class="text_boxes" style="width:70px" />                            
                        </td>
                        <td>
                            <input type="text" id="txt_book_no" name="txt_book_no" class="text_boxes" style="width:70px" onDblClick="openmypage_booking();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_book_id" name="txt_book_id" class="text_boxes" style="width:60px" />
                        </td>
						<td>
							<?
								$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
								echo create_drop_down( "cbo_booking_type", 90, $booking_type_arr,"", 1, "--Select Type--", "", "",0 );
							?>
						</td>
                        <td>
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:70px"  placeholder="Write" />
                            <input type="hidden" id="txt_batch_id" name="txt_batch_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:70px" placeholder="Write Or Browse" onDblClick="openmypage_pinumber()"  />
                            <input type="hidden" id="hdn_pi_id" readonly />
                        </td>

	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_pay_mode", 100, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'requires/room_rack_wise_finish_fabric_stock_report_gmts_controller', this.value + '_' + document.getElementById('cbo_company_id').value, 'load_drop_down_supplier', 'supplier_td' )","","1,2,3,5" );
	                    ?> 
	                    </td>
                        <td id="supplier_td">
                            <?
                                echo create_drop_down( "cbo_supplier_id", 100, $blank_array,"", 1, "--Select Supplier--", 0, "",0 );
                           ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_value_with", 100, array(1=>'Value With 0',2=>'Value Without 0'),"", 0, "", 2, "",0 );
                           ?>
                        </td>

                        <td>
							<?
							$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
							echo create_drop_down( "cbo_get_upto", 60, $get_upto,"", 1, "- All -", 0, "",0 );
							?>
						</td>
						<td>
							<input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_get_upto_qnty", 60, $get_upto,"", 1, "- All -", 0, "",0 );
							?>
						</td>
						<td>
							<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date('d-m-Y'); ?>" class="datepicker" style="width:55px;" />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date('d-m-Y'); ?>" class="datepicker" style="width:55px;"/>
						</td>


                        <td>
                            <input type="hidden" name="search" id="search" value="Show 2" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                	</tbody>
                	<tfoot>
						<tr>
							<td colspan="15" align="center"><? echo load_month_buttons(1);  ?></td>
						</tr>
                	</tfoot>
                </table>
            </fieldset>
        </div>
    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script>
	set_multiselect('cbo_company_id*cbo_store_name*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id*cbo_box_id','0*0*0*0*0*0*0','0*0*0*0*0*0*0','','0*0*0*0*0*0*0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
