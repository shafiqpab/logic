<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create sample requisition wise without order Report.
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	20-03-2023
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

//---------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Progress Report", "../../", 1, 1,$unicode,'1','1');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';

	var tableFilters =
	{
		col_operation: {
		id: ["value_dtls_tot_gery_req","value_dtls_tot_yarn_issue","value_dtls_tot_yarn_balance","value_dtls_tot_gery_knit_product","value_dtls_tot_gray_bal","value_dtls_tot_gery_delivery","value_dtls_tot_gery_in_knit_product","value_dtls_tot_grey_knit_receive_prod","value_dtls_tot_grey_knit_receive_purchase","value_dtls_tot_net_transfer","value_dtls_tot_gray_available_all","value_dtls_tot_gray_balance","value_dtls_tot_gray_issue","value_dtls_tot_batch_qty","value_dtls_tot_dying_qty","value_dtls_tot_dying_balance","value_dtls_tot_fin_req_qty","value_dtls_tot_fin_prod_qnty","value_tot_fin_balance","value_dtls_tot_fin_delivery_qty","value_dtls_tot_fabric_in_prod_floor","value_dtls_tot_finish_prod_rece_store","value_finish_parchase_rece_store","value_dtls_tot_fabric_store_available","value_dtls_tot_fin_balance","value_dtls_tot_cutting_qty","value_dtls_tot_yet_to_issue","value_dtls_tot_left_over"],
		col: [14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_requisition()
	{
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
		var title = 'Requisition ID Search';
		var page_link = 'requires/sample_requisition_wise_finish_fabric_status_controller.php?&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=700px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_requ_no=this.contentDoc.getElementById("hide_requ_no").value;
			var hide_requ_id=this.contentDoc.getElementById("hide_requ_id").value;

			$('#txt_req_no').val(hide_requ_no);
			$('#hide_req_id').val(hide_requ_id);
		}
 	}
	
	function fn_report_generated(excel_type)
	{
		if(form_validation('cbo_company_id','Company Name')==false)	
		{
			return;
		}

		if($('#chk_stock_only').is(':checked'))
		{
			var chk_stock_only=1;
		}
		else
		{
			var chk_stock_only=0;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_name*cbo_fab_nature*cbo_store_name*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id*cbo_year*search_type*txt_search_value*cbo_value_with*txt_date_from*txt_date_to',"../../")+'&excel_type='+excel_type;
		
		freeze_window(3);
		http.open("POST","requires/sample_requisition_wise_finish_fabric_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse; 
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####"); 
			$('#report_container2').html(reponse[0]);
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
		$('#table_body tr:first').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}

	function open_fabric_description_popup()
	{
		var page_link='requires/sample_requisition_wise_finish_fabric_status_controller.php?action=fabric_description_popup&fabric_nature=2';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=800px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			document.getElementById('txt_fabric_id').value=fab_des_id.value;
			document.getElementById('txt_fabric_desc').value=fab_desctiption.value;
			document.getElementById('txt_gsm').value=fab_gsm.value;
		}
	}

	function openmypage(action, requ_no, deter_id, color, gsm)
	{
		page_link='requires/sample_requisition_wise_finish_fabric_status_controller.php?requ_no='+requ_no +'&action='+ action +'&gsm=' + gsm +'&color=' + color +'&deter_id=' + deter_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail Veiw', 'width=1140px, height=450px, center=1, resize=0, scrolling=0','../');
	}

	//openmypage('receive_popup','<? echo  $requ_no;?>',<? echo $deter_id;?>,<? echo $color_no;?>,<? echo $gsm_weight;?>)

	function search_populate(str)
    {
        if (str == 1)
        {
            document.getElementById('search_text_td').innerHTML = "Enter Style no";
        } 
		else if (str == 2)
        {
            document.getElementById('search_text_td').innerHTML = "Enter Requisition no";
        }
    }

	function fnc_change_value(value) 
	{
		var chk_stock_only=$("#chk_stock_only").is(":checked");
		if(chk_stock_only)
		{
			$("#cbo_value_with").val("2");
			$("#cbo_value_with").prop("disabled",true);
		}
		else{
			$("#cbo_value_with").val("1");
			$("#cbo_value_with").prop("disabled",false);
		}
	}

	function getCompanyId() 
	{
	    var company_id = $("#cbo_company_id").val();
	    load_drop_down( 'requires/sample_requisition_wise_finish_fabric_status_controller',company_id, 'load_drop_down_store', 'store_td' );
	    set_multiselect('cbo_store_name','0','0','','0');
	    setTimeout[($("#store_td a").attr("onclick", "disappear_list(cbo_store_name,'0');loadFloor();"), 3000)];
	    
	    load_drop_down( 'requires/sample_requisition_wise_finish_fabric_status_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer_name','0','0','','0');
	}

	function loadFloor() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        load_drop_down('requires/sample_requisition_wise_finish_fabric_status_controller', company_id+'_'+store_id, 'load_drop_down_floors', 'floor_td');
        set_multiselect('cbo_floor_id', '0', '0', '', '0');
        setTimeout[($("#floor_td a").attr("onclick", "disappear_list(cbo_floor_id,'0');loadRoom();"), 3000)]; 
    }

    function loadRoom() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        load_drop_down('requires/sample_requisition_wise_finish_fabric_status_controller', company_id+'_'+store_id+'_'+floor_id, 'load_drop_down_rooms', 'room_td');
        set_multiselect('cbo_room_id', '0', '0', '', '0');
        setTimeout[($("#room_td a").attr("onclick", "disappear_list(cbo_room_id,'0');loadRack();"), 3000)]; 
    }

    function loadRack() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_id').value;
        var room_id = document.getElementById('cbo_room_id').value;
        load_drop_down('requires/sample_requisition_wise_finish_fabric_status_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id, 'load_drop_down_racks', 'rack_td');
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
        load_drop_down('requires/sample_requisition_wise_finish_fabric_status_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id+'_'+rack_id, 'load_drop_down_shelfs', 'shelf_td');
        set_multiselect('cbo_shelf_id', '0', '0', '', '0');
        setTimeout[($("#shelf_td a").attr("onclick", "disappear_list(cbo_shelf_id,'0');"), 3000)];
        
    }

</script>
</head>
<body onLoad="set_hotkey();">
<form id="SampleProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <fieldset style="width:1030px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="1030px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                    <tr>
						<th width="130" class="must_entry_caption">Company Name</th>
						<th width="110">Buyer Name</th>
                        <th width="100">Fabric Nature</th>
                        <th width="100">Store Name</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th width="60">Year</th>
						<th width="60">Stock only</th>
                        <th width="100">Search by</th>
                        <th width="100" id="search_text_td">Enter Style no</th>
                        
						<th width="50">Value range</th>
						<th class="must_entry_caption" width="140" colspan="2">Requisition Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>
                 </thead>
                <tbody>
                    <tr class="general">
                        <td id="company_td">
							<? 
							$com_sql =sql_select("select id,company_name,business_nature from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
							$all_company_arr=array();
							foreach ($com_sql as $row) 
							{
								$buss_com = explode(",",$row[csf("business_nature")]);
								
								foreach ($buss_com as $val) 
								{
									if($val==2 || $val==3)
									{
										$all_company_arr[$row[csf("id")]] =$row[csf("company_name")];
									}
								}
								
							} 
							echo create_drop_down( "cbo_company_id", 100, $all_company_arr,"", 1,"-- Select Company --", 0, "",0,"" ); 

							//echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_progress_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); 
							?>
						</td>
                        <td id="buyer_td">
							<? echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?>
						</td>
                        <td>
							<? 
							$fabric_natures = array(2=> "Knit Finish Fabrics", 3=>"Woven Fabrics");
							echo create_drop_down( "cbo_fab_nature", 100, $fabric_natures,"", 1,"-- All --", 2, "",0,"" ); 
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
						<td>
							<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
						</td>
						<td>
							<input type="checkbox"  name="chk_stock_only" id="chk_stock_only" style="width:50px;" onClick="fnc_change_value(this.value)">
						</td>
                        <td>
						<? 
							$search_type_arr = array(1 => "Style", 2 => "Requisition no");
							echo create_drop_down("search_type", 100, $search_type_arr, "", 0, "-Select-", 0, "search_populate(this.value)", 0, "", "", "", "", "");
						?>
						</td>
						<td>
                            <input type="text" id="txt_search_value" name="txt_search_value" class="text_boxes" style="width:70px" />                            
                        </td>
						<td>
						<? 
							 $valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
							 echo create_drop_down( "cbo_value_with", 100, $valueWithArr,"",0,"",1,"","","");
						?>
						</td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="16"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script>
	set_multiselect('cbo_company_id*cbo_buyer_name*cbo_store_name*cbo_floor_id*cbo_room_id*cbo_rack_id*cbo_shelf_id','0*0*0*0*0*0*0','0*0*0*0*0*0*0','','0*0*0*0*0*0*0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>