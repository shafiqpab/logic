<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Batch and Rack wise Finish fabric Stock Sales Report

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
echo load_html_head_contents("Batch and Rack wise Finish fabric Stock Sales Report","../../../", 1, 1, $unicode,1,1);

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var company_name=document.getElementById('cbo_company_id').value;
		var store_name=document.getElementById('cbo_store_name').value;
		var buyer_id=document.getElementById('cbo_buyer_id').value;
		var cbo_search_by=document.getElementById('cbo_search_by').value;
		var search_string=document.getElementById('txt_search_string').value;
		var get_upto=document.getElementById('cbo_get_upto').value;
		var txt_qnty=document.getElementById('txt_qnty').value;		
		var from_date=document.getElementById('txt_date_from').value;
		var to_date=document.getElementById('txt_date_to').value;

		if(company_name==0)
		{			
			alert("Please Select Company");
			return;			
		}
		if ((from_date=='' || to_date=='') && store_name==0 && buyer_id==0 && (search_string=='' || cbo_search_by==0))
		{
			if( form_validation('txt_date_from*txt_date_to*cbo_store_name*cbo_buyer_id*txt_search_string','Form Date*To Date*Store Name*Buyer*Job No')==false )
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_store_name*cbo_buyer_id*cbo_buyer_client_id*cbo_year*cbo_search_by*txt_search_string*txt_search_str_id*cbo_get_upto*txt_days*cbo_get_upto_qnty*txt_qnty*txt_date_from*txt_date_to*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id',"../../../")+'&report_title='+report_title+'&cbo_report_type='+type;
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
		}
	}

	function new_window() 
    {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "250px";

        $("tr th:first-child").show();
        $("tr td:first-child").show();
    }

    function generate_report_exel_only(type)
	{
		var company_name=document.getElementById('cbo_company_id').value;
		var store_name=document.getElementById('cbo_store_name').value;
		var buyer_id=document.getElementById('cbo_buyer_id').value;
		var cbo_search_by=document.getElementById('cbo_search_by').value;
		var search_string=document.getElementById('txt_search_string').value;
		var get_upto=document.getElementById('cbo_get_upto').value;
		var txt_qnty=document.getElementById('txt_qnty').value;		
		var from_date=document.getElementById('txt_date_from').value;
		var to_date=document.getElementById('txt_date_to').value;

		if(company_name==0)
		{			
			alert("Please Select Company");
			return;			
		}
		if ((from_date=='' || to_date=='') && store_name==0 && buyer_id==0 && (search_string=='' || cbo_search_by==0))
		{
			if( form_validation('txt_date_from*txt_date_to*cbo_store_name*cbo_buyer_id*txt_search_string','Form Date*To Date*Store Name*Buyer*Job No')==false )
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_exel_only"+get_submitted_data_string('cbo_company_id*cbo_store_name*cbo_buyer_id*cbo_buyer_client_id*cbo_year*cbo_search_by*txt_search_string*txt_search_str_id*cbo_get_upto*txt_days*cbo_get_upto_qnty*txt_qnty*txt_date_from*txt_date_to*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id',"../../../")+'&report_title='+report_title+'&cbo_report_type='+type;
		// alert(data);return;

		freeze_window(3);
		http.open("POST","requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse_exel_only;
	}

	function generate_report_reponse_exel_only()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split("####");

			if(reponse!='')
			{
				$('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company_id = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var search_by = $("#cbo_search_by").val();
		var txt_search_string = $("#txt_search_string").val();
		var page_link='requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller.php?action=job_no_popup&company_id='+company_id+'&buyer_name='+buyer_name+'&search_by='+search_by+'&txt_search_string='+txt_search_string+'&cbo_year_id='+cbo_year_id;
		if (search_by==1) {var title='Job No Search';}
		else if(search_by==2){
			var title='Booking No Search';
		}
		else if(search_by==3){
			var title='Batch No Search';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_search_string').val(job_no);
			$('#txt_search_str_id').val(job_id);
		}
	}

	function getCompanyId() 
	{
	    var company_id = $("#cbo_company_id").val();
	    load_drop_down( 'requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller',company_id, 'load_drop_down_store', 'store_td' );
	    set_multiselect('cbo_store_name','0','0','','0');
	    setTimeout[($("#store_td a").attr("onclick", "disappear_list(cbo_store_name,'0');loadFloor();"), 3000)];
	    
	    load_drop_down( 'requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );

	    set_multiselect('cbo_buyer_id','0','0','','0');
	    setTimeout[($("#buyer_td a").attr("onclick", "disappear_list(cbo_buyer_id,'0');"), 3000)];

	    load_drop_down( 'requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller',company_id, 'load_drop_down_buyer_client', 'buyer_clent_td' );
	}

	function loadFloor() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        load_drop_down('requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller', company_id+'_'+store_id, 'load_drop_down_floors', 'floor_td');
        set_multiselect('cbo_floor_id', '0', '0', '', '0');
        setTimeout[($("#floor_td a").attr("onclick", "disappear_list(cbo_floor_id,'0');loadRoom();"), 3000)]; 
    }

    function loadRoom() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        load_drop_down('requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller', company_id+'_'+store_id+'_'+floor_id, 'load_drop_down_rooms', 'room_td');
        set_multiselect('cbo_room_id', '0', '0', '', '0');
        setTimeout[($("#room_td a").attr("onclick", "disappear_list(cbo_room_id,'0');loadRack();"), 3000)]; 
    }

    function loadRack() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        var room_id = document.getElementById('cbo_room_id').value;
        load_drop_down('requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id, 'load_drop_down_racks', 'rack_td');
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
        load_drop_down('requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id+'_'+rack_id, 'load_drop_down_shelfs', 'shelf_td');
        set_multiselect('cbo_shelf_id', '0', '0', '', '0');
        setTimeout[($("#shelf_td a").attr("onclick", "disappear_list(cbo_shelf_id,'0');"), 3000)];
    }

    function change_caption(type)
	{
		if(type==1)
		{
			$('#td_search').html('Job/Style No');
			
			$('#txt_search_string').val('')
			$('#txt_search_str_id').val('')
		}
		else if(type==2)
		{
			$('#td_search').html('Booking No');
			
			$('#txt_search_string').val('')
			$('#txt_search_str_id').val('')
		}
		else if(type==3)
		{
			$('#td_search').html('Batch No');
			
			$('#txt_search_string').val('')
			$('#txt_search_str_id').val('')
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1760px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel- </h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1760px;">
                <table class="rpt_table" width="1760" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="120">Buyer</th>
                            <th width="120">Buyer Client</th>

                            <th width="60">Job Year</th>
                            <th width="60">Search By</th>
                            <th width="75" id="td_search">Job No</th>

							<th>Get Upto</th>
							<th>Days</th>
							<th>Get Upto</th>
							<th>Qty.</th>

							<th width="120">Store Name</th>
							<th width="120">Floor</th>
                            <th width="120">Room</th>
                            <th width="120">Rack</th>
                            <th width="120">Shelf</th>
							<th class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center">
                        <td id="company_td">
                            <?
                               echo create_drop_down( "cbo_company_id", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller',this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/batch_and_rack_wise_finish_fabric_stock_sales_report_controller',this.value, 'load_drop_down_buyer_client', 'buyer_clent_td' );" );
                            ?>
                        </td>
                        
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                           ?>
                        </td>
                        <td id="buyer_clent_td">
                            <?
                                echo create_drop_down( "cbo_buyer_client_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                           ?>
                        </td>
                        <td>
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
							<?
								$search_type_arr = array(1=>"Job/Style No",2=>"Booking No",3=>"Batch No");
								echo create_drop_down( "cbo_search_by", 90, $search_type_arr,"", 1, "--Select Type--", 1, "change_caption(this.value);",0 );
							?>
						</td>
                        <td>
                            <input type="text" id="txt_search_string" name="txt_search_string" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_search_str_id" name="txt_search_str_id" class="text_boxes" style="width:60px" />
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
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date('d-m-Y'); ?>" class="datepicker" style="width:55px;" />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date('d-m-Y'); ?>" class="datepicker" style="width:55px;"/>
						</td>
                        <td>
                            <input type="button" name="search" id="search" value="Digital Bin Card" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                            <input type="text" name="search" id="search" value="Excel Only" onClick="generate_report_exel_only(2)" style="width:60px" class="formbutton" />
							<input type="hidden" name="search" id="search3" value="E" style="width:10px" class="formbutton" />
            				<a href="" id="aa1"></a>
                        </td>
                    </tr>
                	</tbody>
                	<tfoot>
						<tr>
							<td colspan="16" align="center"><? echo load_month_buttons(1);  ?></td>
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
	set_multiselect('cbo_company_id*cbo_buyer_id*cbo_store_name*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id','0*0*0*0*0*0*0','0*0*0*0*0*0*0','','0*0*0*0*0*0*0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
