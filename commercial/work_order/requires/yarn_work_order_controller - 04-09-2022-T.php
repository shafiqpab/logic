<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$mrr_date_check="";
$select_insert_year="";
$date_ref="";
$group_concat="";
if($db_type==2 || $db_type==1)
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$select_insert_year="to_char";
	$date_ref=",'YYYY'";
	$group_concat="wm_concat";
	// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$select_insert_year="year";
	$date_ref="";
	$group_concat="group_concat";
}



//------------------------------------------------------------------------------------------------------

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=45 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
	foreach($printButton as $id){
		if($id==78)$buttonHtml.='<input id="Print1" class="formbutton_disabled printReport" type="button" style="width:80px" onclick="fnc_yarn_order_entry(4)" name="print" value="Print">';
		if($id==84)$buttonHtml.='<input type="button" style="width:80px;" id="id_print_to_button"  onClick="print_to_html_report(1)"   class="formbutton_disabled printReport" name="id_print_to_button" value="Print 2" />';
		if($id==85)$buttonHtml.='<input type="button" style="width:80px;" id="id_print_to_button2"  onClick="print_to_html_report(2)" class="formbutton_disabled printReport" name="id_print_to_button2" value="Print 3" />';
		if($id==193)$buttonHtml.='<input type="button" style="width:80px;" id="id_print_to_button4"  onClick="print_to_html_report(4)" class="formbutton_disabled printReport" name="id_print_to_button4" value="Print 4" />';
		if($id==129)$buttonHtml.='<input type="button" style="width:80px;" id="id_print_to_button5"  onClick="print_to_html_report(5)" class="formbutton_disabled printReport" name="id_print_to_button5" value="Print 5" />';
		if($id==72)$buttonHtml.='<input id="Print6" class="formbutton_disabled printReport" type="button" style="width:80px" onclick="print_to_html_report(3)" name="Print6" value="Print 6">';
        if($id==191)$buttonHtml.='<input id="Print7" class="formbutton_disabled printReport" type="button" style="width:80px" onclick="print_to_html_report(7)" name="Print7" value="Print 7">';
        if($id==227)$buttonHtml.='<input id="Print8" class="formbutton_disabled printReport" type="button" style="width:80px" onclick="print_to_html_report(8)" name="Print8" value="Print 8">';

	}
   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if($action =="item_rate_match_with_budget_variable_setting")
{
	//select id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=28
	$itemRateMatchWithBudget = return_field_value("pi_source_btb_lc", "variable_settings_commercial", "company_name=$data and variable_list=28 and status_active=1 and is_deleted=0");

	$yarnItemrateMatchWithBudget = ($itemRateMatchWithBudget=="")?2:$itemRateMatchWithBudget;
	echo "$('#item_rate_match_with_budget_variable').val('".$yarnItemrateMatchWithBudget."');\n";
	exit();
}



if ($action=="load_buyer_details")
{
	echo create_drop_down( "txt_search_common", 230, "select a.id, a.buyer_name from lib_buyer a,  lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data group by a.id,a.buyer_name order by  a.buyer_name","id,buyer_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(2) and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 order by a.supplier_name ","id,supplier_name", 1, "-- Select --", 0, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/yarn_work_order_controller');",0 );
	exit();
}

if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}

if ($action=="load_details_container") //Yarn
{
   	if($data==2) // independent
	{
		$i=1;
 	?>
	<div style="width:1330px;">
        <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all">
            <thead>
                <tr>
                    <th class="must_entry_caption">Color</th>
                    <th class="must_entry_caption">Count</th>
                    <th>Composition</th>
                    <th>%</th>
                    <th style="display:none">Comp 2</th>
                    <th style="display:none">%</th>
                    <th class="must_entry_caption">Yarn Type</th>
                    <th class="must_entry_caption">UOM</th>
                    <th class="must_entry_caption">Quantity</th>
                    <th class="must_entry_caption">Rate</th>
                    <th class="must_entry_caption">Value</th>
                    <th>Sample Delv. Start</th>
                    <th width="50">Bulk Delv. Start &nbsp;<input type="checkbox" id="bulk_delvi_start" name="bulk_delvi_start" onChange="check_all_report(this.id);"/></th>
					<th width="50">Bulk Delv. End &nbsp;<input type="checkbox" id="bulk_delvi_end" name="bulk_delvi_end" onChange="check_all_report(this.id);"/></th>
                    <th>No. of Lot &nbsp;<input type="checkbox" id="no_of_lot"  name="no_of_lot" onChange="check_all_report(this.id);"/></th>
                    <th>IR/Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="general" id="<? echo $i;?>">
                	<input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="<? echo $row[csf("id")]; ?>" />
                    <td width="100">
                        <input type="hidden" name="txt_po_<? echo $i;?>" id="txt_po_<? echo $i;?>" class="text_boxes" style="width:80px" value="" disabled readonly />
                        <input type="hidden" name="txt_po_brakdown_id_<? echo $i;?>" id="txt_po_brakdown_id_<? echo $i;?>" value="" disabled readonly />

                        <input type="text" name="txt_color_<? echo $i;?>" id="txt_color_<? echo $i;?>" class="text_boxes" onKeyPress="colorName(<? echo $i;?>)" onKeyUp="fn_copy_color(<? echo $i;?>)" style="width:80px"/>
                        <input type="hidden" id="hidden_colorID_<? echo $i;?>" value=""  />
                    </td>
                    <td width="100">
                        <?
                            echo create_drop_down( "cbocount_".$i, 100, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "Select", 0, "",0 );
                        ?>
                    </td>
                    <td width="200">
                        <?  echo create_drop_down( "cbocompone_".$i, 200, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition ); ?></td>
                    <td width="50"><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="100" /></td>
                    <td width="100" style="display:none"><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", 0, "control_composition($i,this.id,'percent_two')",0,"","","",$ommitComposition ); ?></td>
                    <td width="50" style="display:none"><input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value=""  /></td>
                    <td width="100">
                        <?
                            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "Select", 0, "",0,"","","",$ommitYarnType );
                        ?>
                    </td>
                    <td width="50">
                        <?
                            echo create_drop_down( "cbo_uom_".$i, 70, $unit_of_measurement,"", 1, "Select", 12, "",1 );
                        ?>
                    </td>
                    <td width="50">
                        <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;"/>
                    </td>
                    <td width="50">
                        <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
                    </td>
                    <td width="80">
                        <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly />
                    </td>
                    <td width="60">
                        <input type="text" name="txt_sample_date_<? echo $i;?>" id="txt_sample_date_<? echo $i;?>" class="datepicker" style="width:55px;" readonly />
                    </td>
                    <td width="60">
                        <input type="text" name="txt_inhouse_date_<? echo $i;?>" id="txt_inhouse_date_<? echo $i;?>" class="datepicker" style="width:55px;" readonly />
                    </td>
                    <td width="60">
                        <input type="text" name="txt_delivery_end_date_<? echo $i;?>" id="txt_delivery_end_date_<? echo $i;?>" class="datepicker" style="width:55px;" readonly />
                    </td>
                    <td width="50">
                        <input type="text" name="txt_number_of_lot_<? echo $i;?>" id="txt_number_of_lot_<? echo $i;?>" class="text_boxes_numeric " style="width:50px;" />
                    </td>
                    <td width="80">
                        <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:80px;" />
                    </td>
                    <td width="110">
                    	 <input type="button" name="txtview_<? echo $i;?>" id="txtview_<? echo $i;?>" class="formbuttonplasminus" value="View"  style="width:35px;" onClick="javascript:fn_view(<? echo $i;?>);" />
                         <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                         <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                    </td>
                </tr>
            </tbody>
        </table>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	<?
		exit();
	}
	else //blank container
	{
		echo "";
		exit();
	}
}

if ($action=="append_load_details_container")//Yarn details append table row
{
	$data_arr=explode('__',$data);
	$i=$data_arr[0];
	?>
    <tr class="general" id="<? echo $i;?>">
        <td width="100">
            <input type="hidden" name="txt_po_<? echo $i;?>" id="txt_po_<? echo $i;?>" class="text_boxes" style="width:80px" value="" disabled readonly />
            <input type="hidden" name="txt_po_brakdown_id_<? echo $i;?>" id="txt_po_brakdown_id_<? echo $i;?>" value="" disabled readonly />
            <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="<? echo $row[csf("id")]; ?>" />

            <input type="text" name="txt_color_<? echo $i;?>" id="txt_color_<? echo $i;?>" class="text_boxes" onKeyPress="colorName(<? echo $i;?>)" onKeyUp="fn_copy_color(<? echo $i;?>)" value="<? echo $data_arr[1]; ?>" style="width:80px"/>
            <input type="hidden" id="hidden_colorID_<? echo $i;?>" value=""  />
        </td>
        <td width="100">
            <?
                echo create_drop_down( "cbocount_".$i, 100, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "Select", 0, "",0 );
            ?>
        </td>
        <td width="200">
            <?  echo create_drop_down( "cbocompone_".$i, 200, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "",$disabled,"","","",$ommitComposition ); ?></td>
        <td width="50"><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $row[csf("percent_one")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>  /></td>
        <td width="100" style="display:none"><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_two_id")], "control_composition($i,this.id,'percent_two')",$disabled,"","","",$ommitComposition ); ?></td>
        <td width="50" style="display:none"><input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("percent_two")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>  /></td>
        <td width="100">
            <?
                echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "Select", 0, "",0,"","","",$ommitYarnType );
            ?>
        </td>
        <td width="50">
            <?
                echo create_drop_down( "cbo_uom_".$i, 70, $unit_of_measurement,"", 1, "Select", 12, "",1 );
            ?>
        </td>
        <td width="50">
            <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;"/>
        </td>
        <td width="50">
            <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric"  style="width:50px;" />
        </td>
        <td width="80">
            <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" readonly />
        </td>
        <td width="60">
            <input type="text" name="txt_sample_date_<? echo $i;?>" id="txt_sample_date_<? echo $i;?>" class="datepicker" style="width:55px;" readonly />
        </td>
        <td width="60">
            <input type="text" name="txt_inhouse_date_<? echo $i;?>" id="txt_inhouse_date_<? echo $i;?>" class="datepicker" style="width:55px;" readonly />
        </td>
        <td width="60">
            <input type="text" name="txt_delivery_end_date_<? echo $i;?>" id="txt_delivery_end_date_<? echo $i;?>" class="datepicker" style="width:55px;" readonly />
        </td>
        <td width="50">
            <input type="text" name="txt_number_of_lot_<? echo $i;?>" id="txt_number_of_lot_<? echo $i;?>" class="text_boxes_numeric " style="width:50px;" />
        </td>
        <td width="80">
            <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:80px;" />
        </td>
        <td width="110">
        	<input type="button" name="txtview_<? echo $i;?>" id="txtview_<? echo $i;?>" class="formbuttonplasminus" value="View"  style="width:35px;" onClick="javascript:fn_view(<? echo $i;?>);" />
             <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
             <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
        </td>
    </tr>
	<?
	exit();
}

// buyer order popoup here
if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$cbo_wo_basis=str_replace("'","",$cbo_wo_basis);

	?>
		<script>
			$(document).ready(function(e) {
	            $("#txt_search_common").focus();
	        });
			function search_populate(str)
			{
				//alert(str);
				str=str.split('_');
				if(str[0]==0)
				{
					document.getElementById('search_by_th_up').innerHTML="Order No";
					document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else if(str[0]==1)
				{
					document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
					document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else //if(str==2)
				{
					load_drop_down( 'yarn_work_order_controller', str[1], 'load_buyer_details', 'search_by_td' );
					document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				}
			}

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_job = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			//alert(tbl_row_count);return;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
				//var row_data = document.getElementById( 'search'+i ).getAttribute('is_approved_'+i);

			}
		}

		function toggle( x, origColor ){
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function js_set_value( str, str_variable )
		{
			if(str_variable==1)
			{
				alert("Please Ensure All Buyer Order Is Attached In LC/SC");
				return;
			}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				selected_job.push( $('#txt_individual_job' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_job.splice( i, 1 );
			}
			var id =''; var name =''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				job += ""+selected_job[i] + ",";
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			job 	= job.substr( 0, job.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_job').val( job );
		}

		function reset_hidden()
		{
			if($("#txt_selected").val()=="")
			{
				$("#txt_selected").val('');
				$("#txt_selected_id").val('');
				$("#txt_selected_job").val('');
	 		}
			else
			{
				var selectID = $('#txt_selected_id').val().split(",");
				var selectName = $('#txt_selected').val().split(",");
				var selectJob = $('#txt_selected_job').val().split(",");
				for(var i=0;i<selectID.length;i++)
				{
					selected_id.push( selectID[i] );
					selected_name.push( selectName[i] );
					selected_job.push( selectJob[i] );
				}
			}
		}
	    </script>
		</head>
		<body>
			<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" align="center">
		            <thead>
		                <th width="130">Search By</th>
		                <th width="180" align="center" id="search_by_th_up">Enter Order Number</th>
		                <th width="200">Date Range</th>
		                <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
		            </thead>
		            <tr class="general">
		                <td width="130">
		                    <?
		                        $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
		                        echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value+'_'+$company)",0 );
		                    ?>
		                </td>
		                <td width="180" align="center" id="search_by_td">
		                    <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
		                </td>
		                <td align="center">
		                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
		                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
		                </td>
		                <td align="center">
		                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $garments_nature; ?>'+'_'+'<? echo $txt_buyer_po; ?>', 'create_po_search_list_view', 'search_div', 'yarn_work_order_controller', 'setFilterGrid(\'table_body\',-1)');reset_hidden();set_all();" style="width:100px;" />
		                </td>
		            </tr>
		            <tr>
		                <td colspan="4" align="center" height="40" valign="middle">
		                    <? echo load_month_buttons(1);  ?>
		                </td>
		            </tr>
		        </table>
		        <div style="margin-top:5px" id="search_div"></div>
		        <table width="800" cellspacing="0" cellpadding="0" style="border:none" align="center">
		            <tr>
		                <td align="center" height="30" valign="bottom">
		                    <div style="width:100%">
		                        <div style="width:50%; float:left" align="left">
		                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		                        </div>
		                        <div style="width:50%; float:left" align="left">
		                            <input type="hidden" id="txt_selected_id" value="<? //echo $txt_buyer_po; ?>"  /> <!--po break down id here -->
		                            <input type="hidden" id="txt_selected" value="<? //echo $txt_buyer_po_no; ?>"  /> <!--po number here -->
		                            <input type="hidden" id="txt_selected_job" value="<? //echo $txt_job_selected; ?>"  /> <!--job number here -->
		                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		                        </div>
		                    </div>
		                </td>
		            </tr>
		        </table>
		    </form>
		    </div>
	    </body>
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if($action=="create_po_search_list_view")
{
 	extract($_REQUEST);
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
	$txt_buyer_po = $ex_data[6];
	//and a.garments_nature=$garments_nature
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	if($company>0) $com_cond=" and company_name=$company";
	$variable_sql=sql_select("select company_name, export_invoice_qty_source from variable_settings_commercial where status_active=1 and is_deleted=0 and variable_list=24 $com_cond");
	$variable_lc_sc=array();$variable_lc_sc_attach=0;
	foreach($variable_sql as $row)
	{
		$variable_lc_sc[$row[csf("company_name")]]=$row[csf("export_invoice_qty_source")];
		if($row[csf("export_invoice_qty_source")]==1) $variable_lc_sc_attach=$row[csf("export_invoice_qty_source")];
	}
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
 	}
	if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	if(trim($txt_buyer_po)!="") $buyer_po_arr = explode(",",$txt_buyer_po);

 	$sql = "select b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no_prefix_num, a.job_no, a.style_ref_no, $select_insert_year(a.insert_date $date_ref) as year, b.shipment_date, b.po_number, b.po_quantity
			from wo_po_details_master a, wo_po_break_down b
			where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 $sql_cond"; //and a.garments_nature=$garments_nature
	//echo $sql;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sql_result=sql_select($sql);
	if($variable_lc_sc_attach==1)
	{
		$all_job="";
		foreach($sql_result as $row)
		{
			if($job_check[$row[csf("job_no")]]=="")
			{
				$job_check[$row[csf("job_no")]]=$row[csf("job_no")];
				$all_job.="'".$row[csf("job_no")]."',";
			}
		}
		$all_job=chop($all_job,",");
		if($all_job!="")
		{
			$lc_sc_sql="select a.wo_po_break_down_id, b.job_no_mst from com_export_lc_order_info a, wo_po_break_down b  where a.wo_po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($all_job)
			union
			select a.wo_po_break_down_id, b.job_no_mst from com_sales_contract_order_info a, wo_po_break_down b  where a.wo_po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($all_job)
			group by a.wo_po_break_down_id, b.job_no_mst
			order by job_no_mst";
			//echo $lc_sc_sql;die;
			$lc_sc_result=sql_select($lc_sc_sql);
			$lc_sc_data=array();
			foreach($lc_sc_result as $row)
			{
				$lc_sc_data[$row[csf("job_no_mst")]][$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
			}

			$buyer_order_sql="select b.id as po_id, b.job_no_mst from  wo_po_break_down b where b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($all_job)";
			$buyer_order_result=sql_select($buyer_order_sql);
			$buyer_order_data=array();
			foreach($buyer_order_result as $row)
			{
				$buyer_order_data[$row[csf("job_no_mst")]][$row[csf("po_id")]]=$row[csf("po_id")];
			}
		}
	}
	?>
    <div style="width:920px;">
    <p style="text-align:left; font-size:16px; color:#FF0000;"><? if($variable_lc_sc_attach==1) echo "Red Color Indecates Buyer Order Is Not Attached In LC/SC"; ?></p>
     	<table cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="50">SL</th>
                <th width="70">Job No</th>
                <th width="80">Job Year</th>
                <th width="150">Order No</th>
                <th width="130">Buyer</th>
                <th width="150">Style</th>
                <th width="120">Order Qnty</th>
                <th>Shipment Date</th>
             </thead>
     	</table>
     </div>
     <div style="width:920px; max-height:230px;overflow-y:scroll;" >
        <table cellspacing="0" width="902" class="rpt_table" id="table_body" border="1" rules="all">
			<?
			$i=1; $po_row_id='';
            foreach( $result as $row )
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if( in_array($row[csf('id')],$buyer_po_arr))
				{
					if($pi_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
				}
				$variable_check=0;
				if($variable_lc_sc[$row[csf("company_name")]]==1)
				{
					if(count($lc_sc_data[$row[csf("job_no")]]) > 0 &&  count($lc_sc_data[$row[csf("job_no")]]) == count($buyer_order_data[$row[csf("job_no")]]))
					{
						$variable_check=2;
					}
					else
					{
						$variable_check=1;
						$bgcolor="#FF0000";
					}
				}
 				?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none;cursor:pointer;" onClick="js_set_value(<? echo $i;?>,<? echo $variable_check;?>)">
                    <td width="50" align="center">
                        <? echo $i; ?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                        <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf('po_number')]; ?>"/>
                        <input type="hidden" name="txt_individual_job" id="txt_individual_job<? echo $i ?>" value="<? echo $row[csf('job_no')]; ?>"/>
                    </td>
                    <td width="70" align="center"><? echo $row[csf("job_no_prefix_num")]; ?></td>
                    <td width="80" align="center"><? echo $row[csf("year")]; ?></td>
                    <td width="150" align="center"><? echo $row[csf("po_number")]; ?></td>
                    <td width="130"><? echo $buyer_arr[$row[csf("buyer_name")]];  ?></td>
                    <td width="150"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                    <td width="120" align="right"><? echo $row[csf("po_quantity")];?> </td>
                    <td align="center"><? echo change_date_format($row[csf("shipment_date")]);?></td>
                </tr>
				<?
				$i++;
             }
   			?>
            <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
		</table>
	</div>
	<?
	exit();
}

if($action=="requisition_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$cbo_wo_basis=str_replace("'","",$cbo_wo_basis);    

	?>
	<script>
	var selected_dtls_id = new Array;
	var selected_id = new Array;
	var selected_reqsition = new Array;

	/*function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		//alert(tbl_row_count);return;
		for( var i = 1; i <= tbl_row_count; i++ ) 
        {
			//js_set_value( i );
			var row_data = document.getElementById( 'search'+i ).getAttribute('is_approved_'+i);            
			var approval_data = row_data.split("**");

			var is_approved = approval_data[0];
			var allow_partial = approval_data[1];
			var aproval_necessity = approval_data[2];

			if(aproval_necessity==1 && allow_partial!=1)
			{
				if(is_approved==3) {
					alert("Requisition Number is Partial Approved. Please Approve it to select"); return;
				} else if(is_approved!=1) {
					alert("Requisition Number is not Approved. Please Approve it to select"); return;
				} else {
					js_set_value( i );
				}
			}

    		var mst_id =''; var dtls_id =''; var req_no ='';
    		for( var i = 0; i < selected_dtls_id.length; i++ ) 
            {
    			dtls_id += selected_dtls_id[i] + ',';
    			mst_id += selected_id[i] + ',';
    			req_no += selected_reqsition[i] + ',';
    		}
    		dtls_id	= dtls_id.substr( 0, dtls_id.length - 1 );
    		mst_id 	= mst_id.substr( 0, mst_id.length - 1 );
    		req_no 	= req_no.substr( 0, req_no.length - 1 );

    		$('#txt_dtls_id').val( dtls_id );
    		$('#txt_mst_id').val( mst_id );
    		$('#txt_req_no').val( req_no );
    	}
	}*/

    function check_all_data() {
        var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
        tbl_row_count = tbl_row_count - 1;
        for( var i = 1; i <= tbl_row_count; i++ ) {
            $('#search'+i).trigger('click');
        }
    }

	function toggle( x, origColor ){
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_req_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{
				js_set_value( old[i] )
			}
		}
	}

	function js_set_value( str, str_variable, aproval_necessity,is_approved, allow_partial )
	{

		if(str_variable==1)
		{
			alert("Please Ensure All Buyer Order Is Attached In LC/SC");
			return;
		}
		if(aproval_necessity==1 && allow_partial!=1)
		{
			if(is_approved==3)
			{
				alert("Requisition Number is Partial Approved. Please Approve it to select"); return;
			}
			else if(is_approved!=1)
			{
				alert("Requisition Number is not Approved. Please Approve it to select"); return;
			}
		}
		/*else{
			if(is_approved!=1)
			{
				alert("Work Order is not Approved. Please Approve it to select"); return;
			}
		}*/
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_dtls_id' + str).val(), selected_dtls_id ) == -1 ) {
			selected_dtls_id.push( $('#txt_dtls_id' + str).val() );
			selected_id.push( $('#txt_mst_id' + str).val() );
			selected_reqsition.push( $('#txt_req_no' + str).val() );
		}
		else {
			for( var i = 0; i < selected_dtls_id.length; i++ ) {
				if( selected_dtls_id[i] == $('#txt_dtls_id' + str).val() ) break;
			}
			selected_dtls_id.splice( i, 1 );
			selected_id.splice( i, 1 );
			selected_reqsition.splice( i, 1 );
		}
		var mst_id =''; var dtls_id =''; var req_no ='';
		for( var i = 0; i < selected_dtls_id.length; i++ ) {
			dtls_id += selected_dtls_id[i] + ',';
			mst_id += selected_id[i] + ',';
			req_no += selected_reqsition[i] + ',';
		}
		dtls_id	= dtls_id.substr( 0, dtls_id.length - 1 );
		mst_id 	= mst_id.substr( 0, mst_id.length - 1 );
		req_no 	= req_no.substr( 0, req_no.length - 1 );

		$('#txt_dtls_id').val( dtls_id );
		$('#txt_mst_id').val( mst_id );
		$('#txt_req_no').val( req_no );
        //alert(req_no);return;
		
	}

	/*function reset_hidden()
	{
		if($("#txt_selected").val()=="")
		{
			$("#txt_selected").val('');
			$("#txt_selected_id").val('');
			$("#txt_selected_job").val('');
 		}
		else
		{
			var selectID = $('#txt_selected_id').val().split(",");
			var selectName = $('#txt_selected').val().split(",");
			var selectJob = $('#txt_selected_job').val().split(",");
			for(var i=0;i<selectID.length;i++)
			{
				selected_id.push( selectID[i] );
				selected_name.push( selectName[i] );
				selected_job.push( selectJob[i] );
			}
		}
	}*/

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
 		<table width="850" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" align="center">
            <thead>
            	<th width="200">Company</th>
                <th width="200">Enter Requisition Number</th>
                <th width="135">Approval Type</th>
                <th width="200">Requisition Date Range</th>
                <th ><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
            </thead>
            <tr class="general">
            	<td>
                <?
					echo create_drop_down( "cbo_company_name", 195, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --",$company, "" );
				?>
                </td>
                <td >
                    <input type="text" style="width:190px" class="text_boxes"  name="txt_req_no" id="txt_req_no"  />
                </td>
                <td>
                    <?
                    if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
                    else $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
                    $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=14 and status_active=1 and is_deleted=0";   
                    $app_need_setup=sql_select($approval_status);
                    echo create_drop_down( "cbo_approval_type", 130, $yes_no, "", 1, "-- Select--", $app_need_setup[0][csf("approval_need")], "","","" );
                    ?>
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px"> To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                </td>
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value+'_'+'<? echo $garments_nature; ?>'+'_'+'<? echo $txt_req_dtls_id; ?>'+'_'+document.getElementById('cbo_approval_type').value, 'create_req_search_list_view', 'search_div', 'yarn_work_order_controller', 'setFilterGrid(\'table_body\',-1)');set_all();" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center" height="30" valign="middle">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
        </table>
        <div style="margin-top:5px" id="search_div"></div>
        <table width="750" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="hidden" id="txt_dtls_id" value="<? //echo $txt_buyer_po; ?>"  /> <!--req dtls id here -->
                            <input type="hidden" id="txt_mst_id" value="<? //echo $txt_buyer_po_no; ?>"  /> <!--req mst here -->
                            <input type="hidden" id="txt_req_no" value="<? //echo $txt_buyer_po_no; ?>"  /> <!--req number here -->
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}


if($action=="create_req_search_list_view")
{
 	extract($_REQUEST);
	//echo $data;die;
	$ex_data = explode("_",$data);
	$txt_req_no = $ex_data[0];
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = trim(str_replace("'","",$ex_data[3]));
	$garments_nature = $ex_data[4];
	$txt_req_dtls_id=$ex_data[5];
    $approval_type=$ex_data[6];
    //echo $approval_type;
	$sql_cond='';
	if($company>0) $com_cond=" and company_name=$company";
	$variable_sql=sql_select("select company_name, export_invoice_qty_source from variable_settings_commercial where status_active=1 and is_deleted=0 and variable_list=24 $com_cond");
	$variable_lc_sc=array();$variable_lc_sc_attach=0;
	foreach($variable_sql as $row)
	{
		$variable_lc_sc[$row[csf("company_name")]]=$row[csf("export_invoice_qty_source")];
		if($row[csf("export_invoice_qty_source")]==1) $variable_lc_sc_attach=$row[csf("export_invoice_qty_source")];
	}
	//echo "<pre>";print_r($variable_lc_sc);die;
	//$variable_lc_sc_attach=return_field_value("export_invoice_qty_source","variable_settings_commercial","status_active=1 and is_deleted=0 and company_name=$company and variable_list=24","export_invoice_qty_source");

	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$buyer_short_name_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$com_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name");


	if($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',-1);
			$txt_date_to=change_date_format($txt_date_to,'','',-1);
		}
		$sql_cond=" and a.requisition_date between '$txt_date_from' and '$txt_date_to' ";
	}

	if ($txt_req_no != '') $sql_cond.=" and a.requ_prefix_num=$txt_req_no";	
	if ($txt_req_dtls_id == '') $txt_req_dtls_id=0; 

	$prev_req_wo=return_library_array("SELECT requisition_dtls_id, sum(supplier_order_quantity) as supplier_order_quantity from  wo_non_order_info_dtls where status_active=1 and requisition_dtls_id>0 and requisition_dtls_id not in($txt_req_dtls_id) group by requisition_dtls_id","requisition_dtls_id","supplier_order_quantity");
	$sql_wo_update=sql_select("select id as wo_dtls_id, requisition_dtls_id, supplier_order_quantity, amount from wo_non_order_info_dtls where requisition_dtls_id  not in($txt_req_dtls_id) and status_active=1 and is_deleted=0");
	
	foreach($sql_wo_update as $row)
	{
		//$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['requisition_dtls_id']=$row[csf("requisition_dtls_id")];
		//$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['wo_dtls_id']=$row[csf("wo_dtls_id")];
		$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['supplier_order_quantity']=$row[csf("supplier_order_quantity")];
		//$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['amount']=$row[csf("amount")];
	}	

	$aproval_necessity=0;
	if($db_type==0)
	{
		$necessity_sql=sql_select("select b.approval_need as approval_need, b.allow_partial, a.id as max_id from approval_setup_mst a, approval_setup_dtls b
		where a.id=b.mst_id and a.company_id = $company and b.page_id = 14 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		order by a.id desc limit 1");
	}
	else
	{
		$necessity_sql=sql_select("select b.approval_need as approval_need,b.allow_partial, a.id as max_id from approval_setup_mst a, approval_setup_dtls b
		where a.id=b.mst_id and a.company_id = $company and b.page_id = 14 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.setup_date=(select max(setup_date) as id from approval_setup_mst where company_id = $company and status_active=1)");
	}

	if ($necessity_sql)
	{	
		$aproval_necessity=$necessity_sql[0][csf("approval_need")];
		$allow_partial=$necessity_sql[0][csf("allow_partial")];
	}

    $approval_cond='';    
    if ($approval_type==1) $approval_cond=" and a.is_approved in(1,3)";
    if ($approval_type==2) $approval_cond=" and a.is_approved=0";

	if ($company>0) $sql_cond.=" and a.company_id=$company";
	$sql="SELECT a.id as mst_id, a.company_id, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, b.id as dtls_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.quantity as req_qnty,b.count_id,b.composition_id,b.yarn_type_id from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and a.item_category_id=1 $sql_cond $approval_cond order by a.requisition_date desc";
	//echo $sql;
	$sql_result=sql_select($sql);
	if($variable_lc_sc_attach==1)
	{
		$all_job="";
		foreach($sql_result as $row)
		{
			if($job_check[$row[csf("job_no")]]=="")
			{
				$job_check[$row[csf("job_no")]]=$row[csf("job_no")];
				$all_job.="'".$row[csf("job_no")]."',";
			}
		}
		$all_job=chop($all_job,",");
		if($all_job!="")
		{
			$lc_sc_sql="select a.wo_po_break_down_id, b.job_no_mst from com_export_lc_order_info a, wo_po_break_down b  where a.wo_po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($all_job)
			union
			select a.wo_po_break_down_id, b.job_no_mst from com_sales_contract_order_info a, wo_po_break_down b  where a.wo_po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($all_job)
			group by a.wo_po_break_down_id, b.job_no_mst
			order by job_no_mst";
			//echo $lc_sc_sql;die;
			$lc_sc_result=sql_select($lc_sc_sql);
			$lc_sc_data=array();
			foreach($lc_sc_result as $row)
			{
				$lc_sc_data[$row[csf("job_no_mst")]][$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
			}

			$buyer_order_sql="select b.id as po_id, b.job_no_mst from  wo_po_break_down b where b.status_active=1 and b.is_deleted=0 and b.job_no_mst in($all_job)";
			$buyer_order_result=sql_select($buyer_order_sql);
			$buyer_order_data=array();
			foreach($buyer_order_result as $row)
			{
				$buyer_order_data[$row[csf("job_no_mst")]][$row[csf("po_id")]]=$row[csf("po_id")];
			}
		}
	}

    $width=1280;
	?>
    <div style="width:<?= $width; ?>px;">
    <p style="text-align:left; font-size:16px; color:#FF0000;"><? if($variable_lc_sc_attach==1) echo "Red Color Indecates Buyer Order Is Not Attached In LC/SC"; ?></p>
     	<table cellspacing="0"  width="<?= $width-20; ?>" class="rpt_table" border="1" rules="all" align="left">
            <thead>
				<tr>
					<th colspan="16"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="80">Company</th>
					<th width="120">Req No</th>
					<th width="100">Job No</th>
					<th width="120">Style</th>
					<th width="70">Buyer</th>
					<th width="50">Color</th>
					<th width="40">Count</th>
					<th width="110">Composition</th>
					<th width="80">Yarn Type</th>
					<th width="70">Req Qnty</th>
					<th width="70">WO Qnty</th>
					<th width="70">Balance Qnty</th>
					<th width="70">Req Date</th>
					<th width="70">Delivery Date</th>
					<th>Approval Status</th>
				</tr>
             </thead>
     	</table>
     </div>
     <div style="width:<?= $width; ?>px; max-height:230px;overflow-y:scroll;" >
        <table cellspacing="0" width="<?= $width-20; ?>" class="rpt_table" id="table_body" border="1" rules="all">
			<?
			$i=1; $req_row_id='';
			$txt_req_dtls_id_arr=explode(",",$txt_req_dtls_id);
            foreach( $sql_result as $row )
            {
				if($row[csf("req_qnty")]>$prev_req_wo[$row[csf("dtls_id")]])
				{
					if( in_array($row[csf('dtls_id')],$txt_req_dtls_id_arr))
					{
						if($req_row_id=="") $req_row_id=$i; 
                        else $req_row_id.=",".$i;
					}

				    $wo_qty=$update_wo_data_arr[$row[csf("dtls_id")]]['supplier_order_quantity'];
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$variable_check=0;
					if($variable_lc_sc[$row[csf("company_id")]]==1)
					{
						if(count($lc_sc_data[$row[csf("job_no")]]) > 0 &&  count($lc_sc_data[$row[csf("job_no")]]) == count($buyer_order_data[$row[csf("job_no")]]))
						{
							$variable_check=2;
						}
						else
						{
							$variable_check=1;
							$bgcolor="#FF0000";
						}
					}

					if(($aproval_necessity==1 && $allow_partial ==2) || ($aproval_necessity==1 && $allow_partial ==0))
					{
						if($row[csf('is_approved')]!=1) $bgcolor="#ffa38f";
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none;cursor:pointer;" onClick="js_set_value(<? echo $i;?>,<? echo $variable_check;?>,<? echo $aproval_necessity;?>,<?= $row[csf('is_approved')];?>,<? echo $allow_partial;?>)">
						<td width="30" align="center">
							<? echo $i; //."=".count($lc_sc_data[$row[csf("job_no")]])."=".count($buyer_order_data[$row[csf("job_no")]])?>
							<input type="hidden" name="txt_mst_id[]" id="txt_mst_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>
							<input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
							<input type="hidden" name="txt_req_no[]" id="txt_req_no<? echo $i ?>" value="<? echo $row[csf('requ_no')]; ?>"/>
						</td>
                        <td width="80" align="center"><? echo $com_arr[$row[csf("company_id")]]; ?></td>
						<td width="120" align="center"><? echo $row[csf("requ_no")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("job_no")]; ?></td>
						<td width="120" align="center" style="word-break: break-all;"><? echo $row[csf("style_ref_no")]; ?></td>
						<td width="70"><? echo $buyer_short_name_arr[$row[csf("buyer_id")]];  ?></td>
						<td width="50"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>

						<td width="40"><p><? echo $yarn_count_arr[$row[csf("count_id")]]; ?></p></td>
						<td width="110"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
						<td width="80"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>

						<td width="70" align="right"><? echo number_format($row[csf("req_qnty")],0);?> </td>
                        <td width="70" align="right"><? echo number_format($wo_qty,0);?> </td>
                        <td width="70" align="right"><? echo number_format(($row[csf("req_qnty")]-$wo_qty),0);?> </td>
						<td width="70" align="center"><? echo change_date_format($row[csf("requisition_date")]);?> </td>
                        <td width="70" align="center"><? echo change_date_format($row[csf("delivery_date")]);?></td>
						<td align="center">
                            <?                            
                            if ($row[csf("is_approved")]==1) $approved_msg='Full Approved';
                            else if ($row[csf("is_approved")]==3) $approved_msg='Partial Approved';
                            else $approved_msg='Not Approved';
                            echo $approved_msg;
                            ?>                                
                        </td>
					</tr>
					<?
					$i++;
				}
             }
   			?>
            <input type="hidden" name="txt_req_row_id" id="txt_req_row_id" value="<? echo $req_row_id; ?>"/>
		</table>
	</div>
    <?
    exit();
}



if($action=="show_dtls_listview")
{
	extract($_REQUEST);
	$data_exp = explode("***",$data);
	$break_down_id = $data_exp[0];
	$job_numbers ="'".implode("','",array_unique(explode(",",$data_exp[1])))."'";

 	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst where job_no in(".$job_numbers.")", "job_no", "costing_per");

	$woQnty_arr=array();
	$wo_sql=sql_select("select po_breakdown_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, sum(supplier_order_quantity) as qnty from wo_non_order_info_dtls where po_breakdown_id in ($break_down_id) and status_active=1 and is_deleted=0 group by po_breakdown_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type");
	foreach($wo_sql as $resultRow)
	{
		$woQnty_arr[$resultRow[csf("po_breakdown_id")]][$resultRow[csf("yarn_count")]][$resultRow[csf("yarn_comp_type1st")]][$resultRow[csf("yarn_comp_percent1st")]][$resultRow[csf("yarn_comp_type2nd")]][$resultRow[csf("yarn_comp_percent2nd")]][$resultRow[csf("yarn_type")]] = $resultRow[csf("qnty")];
	}

	if($db_type==2 || $db_type==1 )
	{
	$sql="select a.id as po_id, a.plan_cut, a.po_number, b.job_no, b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.type_id, sum(b.cons_qnty) as qnty, b.rate, c.total_set_qnty as ratio from wo_po_break_down a, wo_pre_cost_fab_yarn_cost_dtls b, wo_po_details_master c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and b.job_no in (".$job_numbers.") and b.status_active=1 and b.is_deleted=0 group by a.id, b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.type_id, a.plan_cut,a.po_number, b.job_no, b.rate, c.total_set_qnty";
	}
	else if ($db_type==0)
	{
		$sql="select a.id as po_id, a.plan_cut, a.po_number, b.job_no, b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.type_id, sum(b.cons_qnty) as qnty, b.rate, c.total_set_qnty as ratio from wo_po_break_down a, wo_pre_cost_fab_yarn_cost_dtls b, wo_po_details_master c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and b.job_no in (".$job_numbers.") and b.status_active=1 and b.is_deleted=0 group by a.id, b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.type_id";
	}
	//echo $sql;
	$result=sql_select($sql);

	if( count($result)==0 ){ echo "No Data Found";die;}
	?>
    <div style="width:1320px;" align="left">
    <table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all">
        <thead>
            <tr>
                <th>PO</th>
                <th class="must_entry_caption">Color</th>
                <th>Count</th>
                <th>Composition</th>
                <th>%</th>
                <th style="display:none">Comp 2</th>
                <th style="display:none">%</th>
                <th>Yarn Type</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Value</th>
                <th>Sample Delv. Start</th>
                <th width="50">Bulk Delv. Start &nbsp;<input type="checkbox" id="bulk_delvi_start" name="bulk_delvi_start" onChange="check_all_report(this.id);"/></th>
				<th width="50">Bulk Delv. End &nbsp;<input type="checkbox" id="bulk_delvi_end" name="bulk_delvi_end" onChange="check_all_report(this.id);"/></th>
                <th>No. of Lot &nbsp;<input type="checkbox" id="no_of_lot"  name="no_of_lot" onChange="check_all_report(this.id);"/></th>
                <th>IR/Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
		<?
        $i=1;
        foreach($result as $row)
        {
            $dzn_qnty=0; $cons_qnty=0; $cons_balance_qnty=0;
            if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
            else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
            else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
            else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
            else $dzn_qnty=1;

            $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
            $plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
            $cons_qnty=$plan_cut_qnty*($row[csf('qnty')]/$dzn_qnty);

            $wo_qnty=$woQnty_arr[$row[csf("po_id")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("copm_two_id")]][$row[csf("percent_two")]][$row[csf("type_id")]];

            $cons_balance_qnty=$cons_qnty-$wo_qnty;
            $amount=$cons_balance_qnty*$row[csf('rate')];
        ?>
            <tr class="general" id="<? echo $i;?>">
                <td width="130">
                    <input type="text" name="txt_po_<? echo $i;?>" id="txt_po_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf('po_number')]; ?>" disabled readonly />
                    <input type="hidden" name="txt_po_brakdown_id_<? echo $i;?>" id="txt_po_brakdown_id_<? echo $i;?>" value="<? echo $row[csf('po_id')]; ?>" disabled readonly />
                    <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="" />
                </td>
                <td width="80">
                    <input type="text" name="txt_color_<? echo $i;?>" id="txt_color_<? echo $i;?>" class="text_boxes" onKeyPress="colorName(<? echo $i;?>)" onKeyUp="fn_copy_color(<? echo $i;?>)" style="width:80px" value="" />
                    <input type="hidden" id="hidden_colorID_<? echo $i;?>" value="" disabled  />
                </td>
                <td width="100">
                    <?
                        echo create_drop_down( "cbocount_".$i, 100, $yarn_count_arr,"", 1, "Select", $row[csf("count_id")], "",1 );
                    ?>
                </td>
                <td width="180">
                    <?  echo create_drop_down( "cbocompone_".$i, 180, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "",1,"","","",$ommitComposition ); ?></td>
                <td width="40"><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $row[csf("percent_one")];  ?>" disabled  /></td>
                <td width="100" style="display:none"><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_two_id")], "control_composition($i,this.id,'percent_two')",1,"","","",$ommitComposition ); ?></td>
                <td width="40" style="display:none"><input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("percent_two")];  ?>" disabled /></td>
                <td width="80">
                    <?
                        echo create_drop_down( "cbotype_".$i, 80, $yarn_type,"", 1, "Select", $row[csf("type_id")], "",1,"","","",$ommitYarnType );
                    ?>
                </td>
                <td width="50">
                    <?
                        echo create_drop_down( "cbo_uom_".$i, 50, $unit_of_measurement,"", 1, "Select", 12, "",1 );
                    ?>
                </td>
                <td width="50">
                    <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($cons_balance_qnty,2,".","");?>" />
                </td>
                <td width="50">
                    <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>,<? echo $row[csf('rate')];?>)"  class="text_boxes_numeric"  style="width:50px;" value="<? echo $row[csf('rate')];?>" />
                </td>
                <td width="80">
                    <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" value="<? echo number_format($amount,2,".",""); ?>" readonly />
                </td>
                <td width="55">
                    <input type="text" name="txt_sample_date_<? echo $i;?>" id="txt_sample_date_<? echo $i;?>" class="datepicker" style="width:50px;" readonly />
                </td>
                <td width="55">
                    <input type="text" name="txt_inhouse_date_<? echo $i;?>" id="txt_inhouse_date_<? echo $i;?>" class="datepicker" style="width:50px;" readonly />
                </td>
                <td width="55">
                    <input type="text" name="txt_delivery_end_date_<? echo $i;?>" id="txt_delivery_end_date_<? echo $i;?>" class="datepicker" style="width:50px;" readonly />
                </td>
                <td width="40">
                    <input type="text" name="txt_number_of_lot_<? echo $i;?>" id="txt_number_of_lot_<? echo $i;?>" class="text_boxes_numeric " style="width:40px;" />
                </td>
                <td width="70">
                    <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:70px;" />
                </td>
                <td >
                	<input type="button" name="txtview_<? echo $i;?>" id="txtview_<? echo $i;?>" class="formbuttonplasminus" value="View"  style="width:35px;" onClick="javascript:fn_view(<? echo $i;?>);" />
                     <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
            </tr>
		<?
        $i++;
        }
        ?>
    </table>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
	<?
	exit();
}

if($action=="show_req_dtls_listview")
{
	extract($_REQUEST);
	$data_exp = explode("***",$data);
	$req_dtls_id_all = $data_exp[0];
	$update_id=str_replace("'","",$data_exp[2]);
	$req_mst_id_all =implode(",",array_unique(explode(",",$data_exp[1])));
 	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
 	$buyer_short_name_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$update_wo_data_arr=array();
	//echo $update_id.'=='.$req_dtls_id_all.'=='.$req_mst_id_all;
	if($update_id!="")
	{
		$sql_wo_update=sql_select("select id as wo_dtls_id, requisition_dtls_id, supplier_order_quantity, amount from wo_non_order_info_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql_wo_update as $row)
		{
			$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['requisition_dtls_id']=$row[csf("requisition_dtls_id")];
			$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['wo_dtls_id']=$row[csf("wo_dtls_id")];
			$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['supplier_order_quantity']=$row[csf("supplier_order_quantity")];
			$update_wo_data_arr[$row[csf("requisition_dtls_id")]]['amount']=$row[csf("amount")];
		}
	}


	$sql_wo=sql_select("select requisition_dtls_id, sum(supplier_order_quantity) as supplier_order_quantity, sum(amount) as amount from  wo_non_order_info_dtls where status_active=1 and is_deleted=0 group by requisition_dtls_id");
	$prev_wo_qnty_arr=array();
	foreach($sql_wo as $row)
	{
		$prev_wo_qnty_arr[$row[csf("requisition_dtls_id")]]["supplier_order_quantity"]=$row[csf("supplier_order_quantity")];
		$prev_wo_qnty_arr[$row[csf("requisition_dtls_id")]]["amount"]=$row[csf("amount")];
	}

	$sql="SELECT a.id as mst_id, a.requ_prefix_num, a.requ_no, a.basis, b.id as dtls_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom, b.quantity as req_qnty, b.rate, b.yarn_inhouse_date, b.remarks, b.booking_no, b.is_short
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
	where a.id=b.mst_id and a.id in($req_mst_id_all) and b.id in ($req_dtls_id_all) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70";
	//echo $sql."<br>";
	$result=sql_select($sql);

	 //Precost rate ...............................................................................start;
	 $sql_precost = "select a.job_no,a.count_id,a.type_id,a.copm_one_id,a.rate from wo_pre_cost_fab_yarn_cost_dtls a,inv_purchase_requisition_dtls b where a.job_no=b.job_no  and b.id in ($req_dtls_id_all)";
	$result_precost=sql_select($sql_precost);
	foreach($result_precost as $row){
		$key=$row[csf('job_no')].$row[csf('count_id')].$row[csf('type_id')].$row[csf('copm_one_id')];
		$pre_cost_rate_arr[$key]=$row[csf('rate')];
	}
	//Precost rate ...............................................................................end;

	if( count($result)==0 ){ echo "No Data Found";die;}
	?>
    <div style="width:1350px;" align="left">
    <table cellspacing="0" width="1450" class="rpt_table" id="tbl_details" border="1" rules="all">
        <thead>
            <tr>
                <th width="40">Req. No</th>
                <th width="90">Job No</th>
                <th width="90">Booking No</th>
                <th width="50">Buyer</th>
                <th width="80">Style</th>
                <th width="70">Yarn Color</th>
                <th width="60">Count</th>
                <th width="100">Composition</th>
                <th width="25">%</th>
                <th width="70">Yarn Type</th>
                <th width="40">UOM</th>
                <th width="55">Req. Qnty</th>
                <th width="55">WO. Qnty</th>
                <th width="50">Rate</th>
                <th width="80">Value</th>
                <th width="50">Sample Delv. Start</th>
                <th width="50">Bulk Delv. Start &nbsp;<input type="checkbox" id="bulk_delvi_start" name="bulk_delvi_start" onChange="check_all_report(this.id);"/></th>
				<th width="50">Bulk Delv. End &nbsp;<input type="checkbox" id="bulk_delvi_end" name="bulk_delvi_end" onChange="check_all_report(this.id);"/></th>
                <th width="40">No. of Lot &nbsp;<input type="checkbox" id="no_of_lot"  name="no_of_lot" onChange="check_all_report(this.id);"/></th>
                <th width="70">IR/Remarks</th>
                <th>Act.</th>
            </tr>
        </thead>
        <tbody>
		<?
        $i=1;$dtls_found_arr=array();
        foreach($result as $row)
        {
            if($row[csf('yarn_inhouse_date')]!="" && $row[csf('yarn_inhouse_date')]!="0000-00-00") $inhouse_date=change_date_format($row[csf('yarn_inhouse_date')]); else $inhouse_date="&nbsp;";

			$req_qnty=(($row[csf("req_qnty")]+$update_wo_data_arr[$row[csf("dtls_id")]]['supplier_order_quantity'])-$prev_wo_qnty_arr[$row[csf("dtls_id")]]["supplier_order_quantity"]);

			if($update_wo_data_arr[$row[csf("dtls_id")]]['supplier_order_quantity']>0)
			{
				$wo_req_qnty=$update_wo_data_arr[$row[csf("dtls_id")]]['supplier_order_quantity'];
				$wo_req_amt=$update_wo_data_arr[$row[csf("dtls_id")]]['amount'];
			}
			else
			{
				$wo_req_qnty=$req_qnty;
				$wo_req_amt=$req_qnty*$row[csf('rate')];
			}
			//$dtls_found_arr[]=$temp_update_array[$row[csf("dtls_id")]];
			$key=$row[csf('job_no')].$row[csf('count_id')].$row[csf('yarn_type_id')].$row[csf('composition_id')];
			$bookingNo="";
			if($row[csf("basis")]==1){$bookingNo=$row[csf("booking_no")];}
        	?>
            <tr class="general" id="<? echo $i;?>" align="center">
                <td>
                    <input type="text" name="txt_req_pre_<? echo $i;?>" id="txt_req_pre_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $row[csf("requ_prefix_num")];?>" disabled readonly />
                    <input type="hidden" name="txt_req_<? echo $i;?>" id="txt_req_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $row[csf("requ_no")];?>" disabled readonly />
                    <input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes"  value="<? echo $row[csf("dtls_id")];?>" disabled readonly />
                    <input type="hidden" name="txt_po_<? echo $i;?>" id="txt_po_<? echo $i;?>" class="text_boxes"  value="" disabled readonly />
                    <input type="hidden" name="txt_po_brakdown_id_<? echo $i;?>" id="txt_po_brakdown_id_<? echo $i;?>" value="" disabled readonly />
                    <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="" />
                    <input type="hidden" name="txt_requ_rate_<? echo $i;?>" id="txt_requ_rate_<? echo $i;?>" value="<? echo $row[csf("rate")]?>" />
                    <input type="hidden" name="txt_requ_basis_<? echo $i;?>" id="txt_requ_basis_<? echo $i;?>" value="<? echo $row[csf("basis")]?>" />
                </td>
                <td>
                    <input type="text" name="txt_job_<? echo $i;?>" id="txt_job_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $row[csf("job_no")];?>" disabled readonly />
                    <input type="hidden" name="txt_job_id_<? echo $i;?>" id="txt_job_id_<? echo $i;?>" class="text_boxes" value="<? echo $row[csf("job_id")];?>" disabled readonly />
                </td>
                <td title="This Field Only For Requisition Based on Booking Basis">
                    <input type="text" name="txt_booking_<? echo $i;?>" id="txt_booking_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $bookingNo;?>" readonly />
                    <input type="hidden" id="isShort_<? echo $i; ?>" name="isShort_<? echo $i; ?>"  value="<? echo $row[csf("is_short")]?>">
                </td>
                <td >
                    <input type="text" name="txt_buyer_<? echo $i;?>" id="txt_buyer_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $buyer_short_name_arr[$row[csf("buyer_id")]];?>" readonly disabled/>
                    <input type="hidden" name="txt_buyer_id_<? echo $i;?>" id="txt_buyer_id_<? echo $i;?>" value="<? echo $row[csf("buyer_id")];?>" disabled  />
                </td>
                <td >
                     <input type="text" name="txt_style_<? echo $i;?>" id="txt_style_<? echo $i;?>" class="text_boxes" style="width:75px" value="<? echo $row[csf("style_ref_no")];?>" readonly disabled />
                </td>
                <td >
                    <input type="text" name="txt_color_<? echo $i;?>" id="txt_color_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $color_arr[$row[csf("color_id")]];?>" readonly disabled />
                    <input type="hidden" name="hidden_colorID_<? echo $i;?>" id="hidden_colorID_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>" disabled  />
                </td>
                <td >
                    <?
                        echo create_drop_down( "cbocount_".$i, 50, $yarn_count_arr,"", 1, "Select", $row[csf("count_id")], "",1 );
                    ?>
                </td>
                <td>
                    <?  echo create_drop_down( "cbocompone_".$i, 90, $composition,"", 1, "-- Select --", $row[csf("composition_id")], "",1,"","","",$ommitComposition ); ?></td>
                <td ><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:20px" value="<? echo $row[csf("com_percent")];  ?>" disabled  /></td>

                <td  style="display:none"><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_two_id")], "control_composition($i,this.id,'percent_two')",1,"","","",$ommitComposition ); ?></td>

                <td>
                <?
					echo create_drop_down( "cbotype_".$i, 70, $yarn_type,"", 1, "Select", $row[csf("yarn_type_id")], "",1,"","","",$ommitYarnType );
				?>
                </td>
                <td  style="display:none"><input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("percent_two")];  ?>" disabled /></td>
                <td>
                    <?
                        echo create_drop_down( "cbo_uom_".$i, 40, $unit_of_measurement,"", 1, "Select", 12, "",1 );
                    ?>
                </td>
                <td > <input type="text" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>"  class="text_boxes_numeric" style="width:55px;" value="<? echo number_format($req_qnty,2,".","");?>" readonly disabled /></td>
                <td>
                    <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:55px;" value="<? echo number_format($wo_req_qnty,2,".",""); ?>" />
                </td>
                <td >
                    <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>,<? echo $pre_cost_rate_arr[$key]*1;?>)"  class="text_boxes_numeric"  style="width:50px;" value="<? echo number_format($row[csf('rate')],4,".","");?>" />
                </td>
                <td>
                    <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:75px;" value="<? echo number_format($wo_req_amt,2,".",""); ?>" readonly />
                </td>
                <td>
                    <input type="text" name="txt_sample_date_<? echo $i;?>" id="txt_sample_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="" />
                </td>
                <td>
                    <input type="text" name="txt_inhouse_date_<? echo $i;?>" id="txt_inhouse_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $inhouse_date; ?>" onChange="CompareDate(<? echo $i;?>)" />
                </td>
                <td>
                    <input type="text" name="txt_delivery_end_date_<? echo $i;?>" id="txt_delivery_end_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="" onChange="CompareDate(<? echo $i;?>)" />
                </td>
                <td>
                    <input type="text" name="txt_number_of_lot_<? echo $i;?>" id="txt_number_of_lot_<? echo $i;?>" class="text_boxes_numeric " style="width:40px;" />
                </td>
                <td>
                    <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:65px;" value="<? echo $row[csf("remarks")];  ?>" />
                </td>
                <td>
                	<input type="button" name="txtview_<? echo $i;?>" id="txtview_<? echo $i;?>" class="formbuttonplasminus" value="View"  style="width:35px;" onClick="javascript:fn_view(<? echo $i;?>);" />
                     <input type="button" id="decreaserow_<? echo $i;?>" style="width:25px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>
            </tr>
		<?
        $i++;
        }

		/*$arr_diveation=array_diff($temp_update_array,$dtls_found_arr);
		if(!empty($arr_diveation))
		{
			$del_wo_dtls_id=implode(",",$arr_diveation);
		}*/
        ?>

        </tbody>
    </table>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </div>
	<?
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
	$terms_name = "";
	foreach( $terms_sql as $result )
	{
		//$terms_name.= '{value:"'.$result[csf('terms')].'",id:'.$result[csf('id')]."},";
		$terms_name.= '{value:"'.str_replace('"',"'",$result[csf('terms')]).'",id:'.$result[csf('id')]."},";
	}
	?>
	<script>

		function termsName(rowID)
		{
			$("#termsconditionID_"+rowID).val('');

			$(function() {
				var terms_name = [<? echo substr($terms_name, 0, -1); ?>];
				$("#termscondition_"+rowID).autocomplete({
					source: terms_name,
					select: function (event, ui) {
						$("#termscondition_"+rowID).val(ui.item.value); // display the selected text
						$("#termsconditionID_"+rowID).val(ui.item.id); // save selected id to hidden input
					}
				});
			});
		}

		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }
					});
				  }).end().appendTo("#tbl_termcondi_details");
				$("#tbl_termcondi_details tr:last td:first").html(i);
				$('#termscondition_'+i).removeAttr("onKeyPress").attr("onKeyPress","termsName("+i+");");
				$('#termscondition_'+i).removeAttr("onKeyUp").attr("onKeyUp","termsName("+i+");");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$('#termsconditionID_'+i).val("");
			}
		}

		function fn_deletebreak_down_tr(rowNo)
		{
			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_work_order_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"../../../");
			}
			var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//alert(data);
			//freeze_window(operation);
			http.open("POST","yarn_work_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_order_terms_condition_reponse;
		}

		function fnc_yarn_order_terms_condition_reponse()
		{
			if(http.readyState == 4)
			{
				//alert(http.responseText);
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					parent.emailwindow.hide();
				}
			}
		}

	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
    <? echo load_freeze_divs ("../../../",$permission,1); ?>
	<fieldset>
		   <form id="termscondi_1" autocomplete="off">
				<input type="hidden" id="txt_wo_number" name="txt_wo_number" value="<? echo str_replace("'","",$update_id) ?>"/>
				<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                    <thead>
                        <tr>
                            <th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $terms_and_conditionID = return_field_value("terms_and_condition","wo_non_order_info_mst","id = $update_id");
                    $flag=0;
                    if($terms_and_conditionID=="")
                        $condd = " and is_default=1";
                    else
                    {
                        $condd = " and id in ($terms_and_conditionID)";
                        $flag=1;
                    }
                    $data_array=sql_select("select id, terms from lib_terms_condition where page_id=100 $condd order by id");
                    if( count($data_array)>0 )
                    {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                            $i++;
                            ?>
                            <tr id="settr_1" align="center">
                                <td>
									<? echo $i;?>
                                </td>
                                <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" onKeyPress="termsName(<? echo $i;?>)" onKeyUp="termsName(<? echo $i;?>)" />
                                    <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" value="<? echo $row[csf('id')]; ?>"  readonly />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                </td>
                            </tr>
                            <?
                        }
                    }
                    ?>
                </tbody>
            </table>
            <table width="650" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" height="15" width="100%"> </td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="button_container">
						<?
                            echo load_submit_buttons( $permission, "fnc_work_order_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_delete_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	 //echo "10**";echo $total_row.'reza';die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");

		$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
		$terms_name = array();
		foreach( $terms_sql as $result )
		{
			$terms_name[$result[csf('terms')]] = $result[csf('id')];
		}

		$id=return_next_id( "id", "lib_terms_condition", 1 );
		$field_array = "id,terms,page_id"; $data_array = "";
		$idsArr = "";$j=0;
		for ($i=1;$i<=$total_row;$i++)
		{
			 $termscondition = "termscondition_".$i;
			 $termscondition = $$termscondition;
			 $termsconditionID = "termsconditionID_".$i;
			 $termsconditionID = $$termsconditionID;
			 if(str_replace("'","",$termsconditionID) == "")
			 {
				 $j++;
				 if ($j!=1){ $data_array .=",";}
				 $data_array .="(".$id.",".$termscondition.",100)";
				 $idsArr[]=$id;
				 $id=$id+1;
			 }
			 else
			 {
				 $idsArr[]=str_replace("'","",$termsconditionID);
			 }
		 }

	 //echo "insert into lib_terms_condition (".$field_array.") values ".$data_array."";die;
 		if($data_array!="")
		{
			$CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,0);
		}


		foreach($idsArr as $value)
		{
		   $value = str_replace("'","",$value);
		}

		$idsArr = implode(",", $idsArr);
		$rID=true;
		$rID = sql_update("wo_non_order_info_mst","terms_and_condition","'$idsArr'","id",str_replace("'","",$txt_wo_number),1);



		if($db_type==0)
		{
			if( $rID && $data_array!="" && $CondrID){
				mysql_query("COMMIT");
				echo "0**";
			}
			else if($rID && $data_array==""){
				mysql_query("COMMIT");
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		//oci_commit($con); oci_rollback($con);
		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $data_array!="" && $CondrID){
				oci_commit($con);
				echo "0**";
			}
			else if($rID && $data_array==""){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");

	//	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
		$terms_name = array();
		foreach( $terms_sql as $result )
		{
			$terms_name[$result[csf('terms')]] = $result[csf('id')];
		}

		$id=return_next_id( "id", "lib_terms_condition", 0 );
		$field_array = "id,terms"; $data_array = "";
		$idsArr = "";$j=0;
		for ($i=1;$i<=$total_row;$i++)
		{
			 $termscondition = "termscondition_".$i;
			 $termscondition = $$termscondition;
			 $termsconditionID = "termsconditionID_".$i;
			 $termsconditionID = $$termsconditionID;
			 if(str_replace("'","",$termsconditionID) == "")
			 {
				 $j++;
				 if ($j!=1){ $data_array .=",";}
				 $data_array .="(".$id.",".$termscondition.",100)";
				 $idsArr[]=$id;
				 $id=$id+1;
			 }
			 else
			 {
				 $idsArr[]=$termsconditionID;
			 }
		 }

 		if($data_array!="")
		{
			$CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,1);
		}

		foreach($idsArr as &$value)
		{
		   $value = str_replace("'","",$value);
		}
		$idsArr = implode(",", $idsArr);
		$rID = sql_update("wo_non_order_info_mst","terms_and_condition","'$idsArr'","wo_number",$txt_wo_number,1);
		//echo $rID;die;
		//oci_commit($con); oci_rollback($con);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if( $rID && $data_array!="" && $CondrID){
				oci_commit($con);
				echo "0**";
			}
			else if($rID && $data_array==""){
				oci_commit($con);
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $data_array!="" && $CondrID){
				mysql_query("COMMIT");
				echo "0**";
			}
			else if($rID && $data_array==""){
				mysql_query("COMMIT");
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$count_library=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1","id","yarn_count");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$ready_to_approved = str_replace("'", "", $cbo_ready_to_approved);
    $cbo_wo_type = str_replace("'", "", $cbo_wo_type);

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		// master table netry here---------------------------------------
		$id=return_next_id("id", "wo_non_order_info_mst", 1);
		//$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and item_category=1  and $year_cond=".date('Y',time())." order by id desc ", "wo_number_prefix", "wo_number_prefix_num" ));
		$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and entry_form = 144  and $year_cond=".date('Y',time())." order by id desc ", "wo_number_prefix", "wo_number_prefix_num" ));
		//echo "10**".$new_wo_number[0]."_".$new_wo_number[1]."_".$new_wo_number[2];die;
		$field_array_mst="id, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, wo_date, supplier_id, attention, buyer_name, style, wo_basis_id, entry_form, currency_id, delivery_date, source, pay_mode, do_no, remarks, wo_type, payterm_id, tenor, ship_mode_id, inserted_by, insert_date, inco_term,pi_issue_to, ready_to_approved, dealing_marchant";
		$data_array_mst="(".$id.",".$garments_nature.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",".$txt_buyer_po.",".$txt_wo_date.",".$cbo_supplier.",".$txt_attention.",".$txt_buyer_name.",".$txt_style.",".$cbo_wo_basis.",144,".$cbo_currency.",".$txt_delivery_date.",".$cbo_source.",".$cbo_pay_mode.",".$txt_do_no.",".$txt_remarks.",".$cbo_wo_type.",".$cbo_payterm_id.",".$txt_tenor.",".$cbo_ship_mode.",'".$user_id."','".$pc_date_time."',".$cbo_inco_term.",".$cbo_pi_issue_to.",".$ready_to_approved.",".$cbo_deal_merchant.")";

 		//$rID=sql_insert("wo_non_order_info_mst",$field_array,$data_array,1);

		// details table entry here --------------------------------------


		$total_row = str_replace("'","",$total_row);
		$wo_basis=str_replace("'","",$cbo_wo_basis);
		if($wo_basis==1) // when wo basis requisition
		{
			$field_array_dtls="id, mst_id, requisition_dtls_id, job_id, job_no, booking_no, buyer_id, style_no, requisition_no, po_breakdown_id, item_id,item_category_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, uom, req_quantity, supplier_order_quantity, rate, amount, yarn_inhouse_date,delivery_end_date,number_of_lot,remarks,inserted_by,insert_date,sample_start_date, is_short";
			$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
			$dtlsidform=return_next_id("id", "wo_non_order_info_dtls", 1);
			$data_array_dtls="";
			for($i=1;$i<=$total_row;$i++)
			{
				$txt_req = "txt_req_".$i;
				$txt_req_dtls_id = "txt_req_dtls_id_".$i;
				$txt_job = "txt_job_".$i;
				$txt_job_id = "txt_job_id_".$i;
				$txt_booking = "txt_booking_".$i;
				$txt_buyer_id = "txt_buyer_id_".$i;
				$txt_style = "txt_style_".$i;

				$po_breakdown_id = "txt_po_brakdown_id_".$i;
				$cbocount	 = "cbocount_".$i;
				$cbocompone	 = "cbocompone_".$i;
				$percentone	 = "percentone_".$i;
				$cbocomptwo	 = "cbocomptwo_".$i;
				$percenttwo	 = "percenttwo_".$i;
				$cbotype	 = "cbotype_".$i;

				$txt_color	 = "hidden_colorID_".$i;
				$cbo_uom	 = "cbo_uom_".$i;
				$txt_req_qnty  = "txt_req_qnty_".$i;
				$txt_quantity  = "txt_quantity_".$i;
				$txt_rate    = "txt_rate_".$i;
				$txt_amount  = "txt_amount_".$i;
				$txt_inhouse_date  = "txt_inhouse_date_".$i;
                $txt_delivery_end_date  = "txt_delivery_end_date_".$i;
				$txt_remarks  = "txt_remarks_".$i;
				$number_of_lot="txt_number_of_lot_".$i;
				$txt_sample_date  = "txt_sample_date_".$i;
				$isShort  = "isShort_".$i;


				if(str_replace("'","",$$txt_inhouse_date)!="" && str_replace("'","",$$txt_inhouse_date)!='0000-00-00')
				{
					if($db_type==0) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'','',-1);
				}
				else
				{
					$txt_inhouse_date='';
				}

                if(str_replace("'","",$$txt_delivery_end_date)!="" && str_replace("'","",$$txt_delivery_end_date)!='0000-00-00')
				{
					if($db_type==0) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'','',-1);
				}
				else
				{
					$txt_delivery_end_date='';
				}

				if($$txt_quantity!="" || $$txt_rate!="")
				{
					if($data_array_dtls!="") $data_array_dtls .=",";

					$data_array_dtls .="(".$dtlsid.",".$id.",".$$txt_req_dtls_id.",".$$txt_job_id.",".$$txt_job.",".$$txt_booking.",".$$txt_buyer_id.",".$$txt_style.",".$$txt_req.",".$$po_breakdown_id.",0,1,".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$txt_color.",".$$cbo_uom.",".$$txt_req_qnty.",".$$txt_quantity.",".$$txt_rate.",".$$txt_amount.",'".$txt_inhouse_date."','".$txt_delivery_end_date."',".$$number_of_lot.",".$$txt_remarks.",'".$user_id."','".$pc_date_time."',".$$txt_sample_date.",".$$isShort.")";
					$dtlsid=$dtlsid+1;
				}
			}
		}
		else // when wo basis buyer po or independent
		{
			$field_array_dtls="id, mst_id, po_breakdown_id, item_id,item_category_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, uom, supplier_order_quantity, rate, amount, yarn_inhouse_date,delivery_end_date, number_of_lot,remarks,inserted_by,insert_date,sample_start_date";
			$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
			$dtlsidform=return_next_id("id", "wo_non_order_info_dtls", 1);
			$data_array_dtls="";
			for($i=1;$i<=$total_row;$i++)
			{
				$po_breakdown_id = "txt_po_brakdown_id_".$i;
				$cbocount	 = "cbocount_".$i;
				$cbocompone	 = "cbocompone_".$i;
				$percentone	 = "percentone_".$i;
				$cbocomptwo	 = "cbocomptwo_".$i;
				$percenttwo	 = "percenttwo_".$i;
				$cbotype	 = "cbotype_".$i;

				$txt_color	 = "hidden_colorID_".$i;
				$txt_color	 = $$txt_color;
				if( str_replace("'","",$txt_color)=="" )
				{
					$txt_color_name = "txt_color_".$i;
					$txt_color = return_id( str_replace("'","",$$txt_color_name), $color_library, "lib_color", "id,color_name","144");
					$color_library[$txt_color]= strtoupper( str_replace("'","",$$txt_color_name) );
				}

				$cbo_uom	 = "cbo_uom_".$i;
				$txt_quantity  = "txt_quantity_".$i;
				$txt_rate    = "txt_rate_".$i;
				$txt_amount  = "txt_amount_".$i;
				$txt_inhouse_date  = "txt_inhouse_date_".$i;
                                $txt_delivery_end_date  = "txt_delivery_end_date_".$i;
				$txt_remarks  = "txt_remarks_".$i;
				$number_of_lot="txt_number_of_lot_".$i;
				$txt_sample_date  = "txt_sample_date_".$i;

				if(str_replace("'","",$$txt_inhouse_date)!="" && str_replace("'","",$$txt_inhouse_date)!='0000-00-00')
				{
					if($db_type==0) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'','',-1);
				}
				else
				{
					$txt_inhouse_date='';
				}

                                if(str_replace("'","",$$txt_delivery_end_date)!="" && str_replace("'","",$$txt_delivery_end_date)!='0000-00-00')
				{
					if($db_type==0) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'','',-1);
				}
				else
				{
					$txt_delivery_end_date='';
				}

				if($$txt_quantity!="" || $$txt_rate!="")
				{
					if($data_array_dtls!="") $data_array_dtls .=",";

					$data_array_dtls .="(".$dtlsid.",".$id.",".$$po_breakdown_id.",0,1,".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$txt_color.",".$$cbo_uom.",".$$txt_quantity.",".$$txt_rate.",".$$txt_amount.",'".$txt_inhouse_date."','".$txt_delivery_end_date."',".$$number_of_lot.",".$$txt_remarks.",'".$user_id."','".$pc_date_time."',".$$txt_sample_date.")";
					$dtlsid=$dtlsid+1;
				}
			}
		}


		//echo "5**insert into wo_non_order_info_dtls (".$field_array_dtls.") values".$data_array_dtls.""; //die;
		//echo "insert into wo_non_order_info_mst(".$field_array_mst.") values ".$data_array_mst." ";die;
		$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array_dtls,$data_array_dtls,1);

		//echo "5**insert into wo_non_order_info_dtls ($field_array_dtls) values".$data_array_dtls;die;

		//echo $dtlsrID;die;$cbo_wo_basis

		//echo "5**$rID ** $dtlsrID";die;
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
							//	echo 'fdfd';
				mysql_query("COMMIT");
				echo "0**".$new_wo_number[0]."**".$id."**".$dtlsidform."**".str_replace("'","",$cbo_wo_basis);
			}
			else
			{

				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{

			if($rID && $dtlsrID)
			{
											//	echo 'fdfd fdfd';
				oci_commit($con);
				echo "0**".$new_wo_number[0]."**".$id."**".$dtlsidform."**".str_replace("'","",$cbo_wo_basis);
			}
			else
			{

				oci_rollback($con);
				echo "10**";
			}
		}
		//release lock table
		check_table_status( 175,0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();

		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; die;}


		// master table netry here---------------------------------------
		//$mst_id = return_field_value("id","wo_non_order_info_mst","wo_number=$txt_wo_number");
		$mst_id=str_replace("'","",$update_id);

		$work_order_id=return_field_value("work_order_id","com_pi_item_details","work_order_id=$mst_id and item_category_id=1 and status_active=1 and is_deleted=0","work_order_id");

		if ($work_order_id != ""){
			echo "11**This Work Order Already Attach in PI Number.So Update Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
		}

		if($mst_id>0) $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$mst_id and status_active=1","pay_mode");
		if($mst_id>0 && $pay_mode==2)
		{
			/*$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b
			where a.id=b.pi_id and a.item_category_id=1 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$mst_id");
			if(count($pi_sql)>0)
			{
				echo "11**PI Number ".$pi_sql[0][csf("pi_number")]." Found . \n So Update/Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}*/
			$all_dtls_info_arr=array();
			for($i=1;$i<=$total_row;$i++)
			{
				$txt_quantity  = "txt_quantity_".$i;
				$hidden_colorID  = "hidden_colorID_".$i;
				$cbo_count  = "cbocount_".$i;
				$cbo_compone  = "cbocompone_".$i;
				$txt_percentone  = "percentone_".$i;
				$cbo_type  = "cbotype_".$i;
				$txt_rate  = "txt_rate_".$i;

				$wo_qnty = str_replace("'","",$$txt_quantity);
				$hidden_colorID = str_replace("'","",$$hidden_colorID);
				$cbo_count = str_replace("'","",$$cbo_count);
				$cbo_compone = str_replace("'","",$$cbo_compone);
				$txt_percentone = str_replace("'","",$$txt_percentone);
				$cbo_type = str_replace("'","",$$cbo_type);
				$txt_rate = str_replace("'","",$$txt_rate);
				
				$dtls_ID  = "txt_row_id_".$i;
				$dtlsID = str_replace("'","",$$dtls_ID);
				if($dtlsID)
				{
					$all_dtls_id_arr[$dtlsID]=$dtlsID;
					$all_dtls_qnty_arr[$dtlsID]=$wo_qnty;
					$all_dtls_info_arr[$dtlsID]['hidden_colorID']=$hidden_colorID;
					$all_dtls_info_arr[$dtlsID]['cbo_count']=$cbo_count;
					$all_dtls_info_arr[$dtlsID]['cbo_compone']=$cbo_compone;
					$all_dtls_info_arr[$dtlsID]['txt_percentone']=$txt_percentone;
					$all_dtls_info_arr[$dtlsID]['cbo_type']=$cbo_type;
					$all_dtls_info_arr[$dtlsID]['txt_rate']=$txt_rate;
				}
			}
			$pi_sql=sql_select("SELECT a.id as pi_id, a.pi_number, b.work_order_id, b.work_order_dtls_id, b.quantity as QUANTITY, b.rate as RATE, b.color_id as COLOR_ID, b.count_name as COUNT_NAME, b.yarn_composition_item1 as YARN_COMPOSITION_ITEM1, b.yarn_composition_percentage1 as YARN_COMPOSITION_PERCENTAGE1, b.yarn_type as YARN_TYPE
			from com_pi_master_details a, com_pi_item_details b
			where a.id=b.pi_id and a.item_category_id=1 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in(".implode(",",$all_dtls_id_arr).")");
			if(count($pi_sql)>0)
			{
				$pi_data=array();
				foreach($pi_sql as $row)
				{
					$pi_data[$row[csf("work_order_dtls_id")]]["quantity"]+=$row["QUANTITY"];
					$pi_data[$row[csf("work_order_dtls_id")]]["rate"]=$row["RATE"];
					$pi_data[$row[csf("work_order_dtls_id")]]["color_id"]=$row["COLOR_ID"];
					$pi_data[$row[csf("work_order_dtls_id")]]["count_name"]=$row["COUNT_NAME"];
					$pi_data[$row[csf("work_order_dtls_id")]]["yarn_composition_item1"]=$row["YARN_COMPOSITION_ITEM1"];
					$pi_data[$row[csf("work_order_dtls_id")]]["yarn_composition_percentage1"]=$row["YARN_COMPOSITION_PERCENTAGE1"];
					$pi_data[$row[csf("work_order_dtls_id")]]["yarn_type"]=$row["YARN_TYPE"];
				}
				foreach($pi_data as $wo_dtls_id=>$prod_pi_val)
				{
					$wo_qnty=$all_dtls_qnty_arr[$wo_dtls_id]*1;
					$wo_rate=$all_dtls_info_arr[$wo_dtls_id]['txt_rate']*1;
					$wo_color=$all_dtls_info_arr[$wo_dtls_id]['hidden_colorID'];
					$wo_count=$all_dtls_info_arr[$wo_dtls_id]['cbo_count'];
					$wo_compone=$all_dtls_info_arr[$wo_dtls_id]['cbo_compone'];
					$wo_percentone=$all_dtls_info_arr[$wo_dtls_id]['txt_percentone'];
					$wo_yarn_type=$all_dtls_info_arr[$wo_dtls_id]['cbo_type'];

					$pi_qnty=$prod_pi_val["quantity"]*1;
					$pi_rate=$prod_pi_val["rate"]*1;
					$pi_color=$prod_pi_val["color_id"];
					$pi_count=$prod_pi_val["count_name"];
					$pi_compone=$prod_pi_val["yarn_composition_item1"];
					$pi_percentone=$prod_pi_val["yarn_composition_percentage1"];
					$pi_yarn_type=$prod_pi_val["yarn_type"];

					if($wo_qnty < $pi_qnty)
					{
						echo "11**PI Number Found, WO Quantity Not Allow Less Then PI Quantity. \n WO Qnty = $wo_qnty, PI Qnty=$pi_qnty";check_table_status( 175,0);disconnect($con);die;
					}
					if($wo_rate!=$pi_rate)
					{
						echo "11**PI Number Found, WO Rate Not Allow Any Change. \n WO rate = $wo_rate, PI rate=$pi_rate";check_table_status( 175,0);disconnect($con);die;
					}
					if($wo_color!=$pi_color)
					{
						echo "11**PI Number Found, WO Color Not Allow Any Change. \n WO Color = $color_library[$wo_color], PI Color=$color_library[$pi_color]";check_table_status( 175,0);disconnect($con);die;
					}
					if($wo_count!=$pi_count)
					{
						echo "11**PI Number Found, WO Count Not Allow Any Change. \n WO Count = $count_library[$wo_count], PI Count=$count_library[$pi_count]";check_table_status( 175,0);disconnect($con);die;
					}
					if($wo_compone!=$pi_compone)
					{
						echo "11**PI Number Found, WO Composition Not Allow Any Change. \n WO Composition = $composition[$wo_compone], PI Composition=$composition[$pi_compone]";check_table_status( 175,0);disconnect($con);die;
					}
					if($wo_percentone!=$pi_percentone)
					{
						echo "11**PI Number Found, WO Parentage Not Allow Any Change. \n WO Parentage = $wo_percentone, PI Parentage=$pi_percentone";check_table_status( 175,0);disconnect($con);die;
					}
					if($wo_yarn_type!=$pi_yarn_type)
					{
						echo "11**PI Number Found, WO Yarn Type Not Allow Any Change. \n WO Yarn Type = $yarn_type[$wo_yarn_type], PI Yarn Type=$yarn_type[$pi_yarn_type]";check_table_status( 175,0);disconnect($con);die;
					}
				}
			}
		
		}

		if($mst_id>0 && $pay_mode!=2)
		{
			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
            from inv_receive_master a, inv_transaction b
            where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=1 and a.item_category=1 and b.item_category=1 and b.transaction_type=1 and a.receive_basis=2 and a.receive_purpose<>2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$mst_id");
            if(count($mrr_sql)>0)
            {
                echo "11**Receive Number ".$mrr_sql[0][csf("recv_number")]." Found .  \n So Update/Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
            }
		}


		if($mst_id!="")
		{
			$field_array_mst="buyer_po*wo_date*supplier_id*attention*buyer_name*style*wo_basis_id*entry_form*currency_id*delivery_date*source*pay_mode*do_no*remarks*wo_type*payterm_id*tenor*ship_mode_id*inco_term*pi_issue_to*updated_by*update_date*ready_to_approved*dealing_marchant";
			$data_array_mst="".$txt_buyer_po."*".$txt_wo_date."*".$cbo_supplier."*".$txt_attention."*".$txt_buyer_name."*".$txt_style."*".$cbo_wo_basis."*144*".$cbo_currency."*".$txt_delivery_date."*".$cbo_source."*".$cbo_pay_mode."*".$txt_do_no."*".$txt_remarks."*".$cbo_wo_type."*".$cbo_payterm_id."*".$txt_tenor."*".$cbo_ship_mode."*".$cbo_inco_term."*".$cbo_pi_issue_to."*'".$user_id."'*'".$pc_date_time."'*".$ready_to_approved."*".$cbo_deal_merchant."";
			//echo $field_array_mst."<br>".$data_array_mst;die;
			//$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
		}

 		// details table entry here --------------------------------------
		$total_row = str_replace("'","",$total_row);
		$txt_delete_row = str_replace("'","",$txt_delete_row);
		/*if($txt_delete_row!="")
		{
			$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",1);
			//$delete_details = sql_multirow_update("wo_non_order_info_dtls","status_active*is_deleted","0*1","id",$txt_delete_row,1);
			//echo "UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)";
		}*/
		//die;

		$wo_basis=str_replace("'","",$cbo_wo_basis);
		if($wo_basis==1)
		{
			$field_array_insert="id, mst_id, requisition_dtls_id, job_id, job_no, booking_no, buyer_id, style_no, requisition_no, po_breakdown_id,item_id,item_category_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, uom, req_quantity, supplier_order_quantity, rate, amount, yarn_inhouse_date, delivery_end_date, number_of_lot,remarks,inserted_by,insert_date,sample_start_date, is_short";
			//$field_array="supplier_order_quantity*rate*amount*updated_by*update_date*yarn_inhouse_date*delivery_end_date*number_of_lot*remarks";
			$field_array="requisition_dtls_id*job_id*job_no*booking_no*buyer_id*style_no*requisition_no*po_breakdown_id*item_id*item_category_id*yarn_count*yarn_comp_type1st*yarn_comp_percent1st*yarn_type*color_name*uom*req_quantity*supplier_order_quantity*rate*amount*updated_by*update_date*yarn_inhouse_date*delivery_end_date*number_of_lot*remarks*sample_start_date*is_short";

			$data_array=array();
			$update_ID=array();
			$data_array_insert="";
			$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
			$dtlsidform=return_next_id("id", "wo_non_order_info_dtls", 1);

			for($i=1;$i<=$total_row;$i++)
			{
				$txt_req = "txt_req_".$i;
				$txt_req_dtls_id = "txt_req_dtls_id_".$i;
				$txt_job = "txt_job_".$i;
				$txt_job_id = "txt_job_id_".$i;
				$txt_booking = "txt_booking_".$i;
				$txt_buyer_id = "txt_buyer_id_".$i;
				$txt_style = "txt_style_".$i;

				$po_breakdown_id = "txt_po_brakdown_id_".$i;
				$cbocount	 = "cbocount_".$i;
				$cbocompone	 = "cbocompone_".$i;
				$percentone	 = "percentone_".$i;
				$cbocomptwo	 = "cbocomptwo_".$i;
				$percenttwo	 = "percenttwo_".$i;
				$cbotype	 = "cbotype_".$i;

				$txt_color	 = "hidden_colorID_".$i;
				$cbo_uom	 = "cbo_uom_".$i;
				$txt_req_qnty  = "txt_req_qnty_".$i;
				$txt_quantity  = "txt_quantity_".$i;
				$txt_rate    = "txt_rate_".$i;
				$txt_amount  = "txt_amount_".$i;
				$dtls_ID  	 = "txt_row_id_".$i;
				$dtlsID = str_replace("'","",$$dtls_ID);
				$txt_inhouse_date  = "txt_inhouse_date_".$i;
				$txt_delivery_end_date  = "txt_delivery_end_date_".$i;
				$txt_remarks  = "txt_remarks_".$i;
				$number_of_lot="txt_number_of_lot_".$i;
				$txt_sample_date="txt_sample_date_".$i;
				$isShort="isShort_".$i;

				if(str_replace("'","",$$txt_inhouse_date)!="" && str_replace("'","",$$txt_inhouse_date)!='0000-00-00')
				{
					if($db_type==0) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'','',-1);
				}
				else
				{
					$txt_inhouse_date='';
				}
                     if(str_replace("'","",$$txt_delivery_end_date)!="" && str_replace("'","",$$txt_delivery_end_date)!='0000-00-00')
				{
					if($db_type==0) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'','',-1);
				}
				else
				{
					$txt_delivery_end_date='';
				}

				/*$po_breakdown_id = "txt_po_brakdown_id_".$i;
				$cbocount	 = "cbocount_".$i;
				$cbocompone	 = "cbocompone_".$i;
				$percentone	 = "percentone_".$i;
				$cbocomptwo	 = "cbocomptwo_".$i;
				$percenttwo	 = "percenttwo_".$i;
				$cbotype	 = "cbotype_".$i;
				$cbo_uom	 = "cbo_uom_".$i;
				$txt_quantity  = "txt_quantity_".$i;
				$txt_rate    = "txt_rate_".$i;
				$txt_amount  = "txt_amount_".$i;

				$dtls_ID  	 = "txt_row_id_".$i;
				$dtlsID = str_replace("'","",$$dtls_ID);*/

				if($$txt_quantity!="" || $$txt_rate!="") //check blank row
				{
					if($dtlsID>0) //update
					{
						$update_ID[]=$dtlsID;
						//$data_array[$dtlsID]=explode("*",("".$$txt_quantity."*".$$txt_rate."*".$$txt_amount."*'".$user_id."'*'".$pc_date_time."'*'".$txt_inhouse_date."'*'".$txt_delivery_end_date."'*".$$number_of_lot."*".$$txt_remarks.""));

						$data_array[$dtlsID]=explode("*",("".$$txt_req_dtls_id."*".$$txt_job_id."*".$$txt_job."*".$$txt_booking."*".$$txt_buyer_id."*".$$txt_style."*".$$txt_req."*".$$po_breakdown_id."*0*1*".$$cbocount."*".$$cbocompone."*".$$percentone."*".$$cbotype."*".$$txt_color."*".$$cbo_uom."*".$$txt_req_qnty."*".$$txt_quantity."*".$$txt_rate."*".$$txt_amount."*'".$user_id."'*'".$pc_date_time."'*'".$txt_inhouse_date."'*'".$txt_delivery_end_date."'*".$$number_of_lot."*".$$txt_remarks."*".$$txt_sample_date."*".$$isShort.""));
					}
					else // new insert
					{
						if($data_array_insert!="") $data_array_insert.=",";
						$data_array_insert.="(".$dtlsid.",".$mst_id.",".$$txt_req_dtls_id.",".$$txt_job_id.",".$$txt_job.",".$$txt_booking.",".$$txt_buyer_id.",".$$txt_style.",".$$txt_req.",".$$po_breakdown_id.",0,1,".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$txt_color.",".$$cbo_uom.",".$$txt_req_qnty.",".$$txt_quantity.",".$$txt_rate.",".$$txt_amount.",'".$txt_inhouse_date."','".$txt_delivery_end_date."',".$$number_of_lot.",".$$txt_remarks.",'".$user_id."','".$pc_date_time."',".$$txt_sample_date.",".$$isShort.")";
						$dtlsid=$dtlsid+1;
					}
				}//end if cond
			}
		}
		else
		{
			$field_array_insert="id, mst_id, po_breakdown_id,item_id,item_category_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, uom, supplier_order_quantity, rate, amount, yarn_inhouse_date, delivery_end_date, number_of_lot,remarks,inserted_by,insert_date,sample_start_date";
			$field_array="po_breakdown_id*item_id*item_category_id*yarn_count*yarn_comp_type1st*yarn_comp_percent1st*yarn_comp_type2nd*yarn_comp_percent2nd*yarn_type*color_name*uom*supplier_order_quantity*rate*amount*inserted_by*insert_date*yarn_inhouse_date*delivery_end_date*number_of_lot*remarks*sample_start_date";

			$data_array=array();
			$update_ID=array();
			$data_array_insert="";
			$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
			$dtlsidform=return_next_id("id", "wo_non_order_info_dtls", 1);

			for($i=1;$i<=$total_row;$i++)
			{
				$po_breakdown_id = "txt_po_brakdown_id_".$i;
				$cbocount	 = "cbocount_".$i;
				$cbocompone	 = "cbocompone_".$i;
				$percentone	 = "percentone_".$i;
				$cbocomptwo	 = "cbocomptwo_".$i;
				$percenttwo	 = "percenttwo_".$i;
				$cbotype	 = "cbotype_".$i;
				$cbo_uom	 = "cbo_uom_".$i;
				$txt_quantity  = "txt_quantity_".$i;
				$txt_rate    = "txt_rate_".$i;
				$txt_amount  = "txt_amount_".$i;

				$dtls_ID  	 = "txt_row_id_".$i;
				$dtlsID = str_replace("'","",$$dtls_ID);
				$txt_inhouse_date  = "txt_inhouse_date_".$i;
                $txt_delivery_end_date = "txt_delivery_end_date_".$i;
				$txt_remarks  = "txt_remarks_".$i;
				$number_of_lot="txt_number_of_lot_".$i;
				$txt_sample_date="txt_sample_date_".$i;

				if(str_replace("'","",$$txt_inhouse_date)!="" && str_replace("'","",$$txt_inhouse_date)!='0000-00-00')
				{
					if($db_type==0) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_inhouse_date=change_date_format(str_replace("'","",$$txt_inhouse_date),'','',-1);
				}
				else
				{
					$txt_inhouse_date='';
				}

                                if(str_replace("'","",$$txt_delivery_end_date)!="" && str_replace("'","",$$txt_delivery_end_date)!='0000-00-00')
				{
					if($db_type==0) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'yyyy-mm-dd');
					else if($db_type==2) $txt_delivery_end_date=change_date_format(str_replace("'","",$$txt_delivery_end_date),'','',-1);
				}
				else
				{
					$txt_delivery_end_date='';
				}


				if($$txt_quantity!="" || $$txt_rate!="") //check blank row
				{
					$txt_color	 = "hidden_colorID_".$i;
					$txt_color	 = $$txt_color;
					if( str_replace("'","",$txt_color)=="" )
					{
						$txt_color_name = "txt_color_".$i;
						$txt_color = return_id( str_replace("'","",$$txt_color_name), $color_library, "lib_color", "id,color_name","144");
						$color_library[$txt_color]= strtoupper( str_replace("'","",$$txt_color_name) );
					}

					if($dtlsID>0) //update
					{
						$update_ID[]=$dtlsID;
						$data_array[$dtlsID]=explode("*",("".$$po_breakdown_id."*0*1*".$$cbocount."*".$$cbocompone."*".$$percentone."*".$$cbocomptwo."*".$$percenttwo."*".$$cbotype."*".$txt_color."*".$$cbo_uom."*".$$txt_quantity."*".$$txt_rate."*".$$txt_amount."*'".$user_id."'*'".$pc_date_time."'*'".$txt_inhouse_date."'*'".$txt_delivery_end_date."'*".$$number_of_lot."*".$$txt_remarks."*".$$txt_sample_date.""));
					}
					else // new insert
					{
						if($data_array_insert!="") $data_array_insert.=",";
						$data_array_insert.="(".$dtlsid.",".$mst_id.",".$$po_breakdown_id.",0,1,".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$txt_color.",".$$cbo_uom.",".$$txt_quantity.",".$$txt_rate.",".$$txt_amount.",'".$txt_inhouse_date."','".$txt_delivery_end_date."',".$$number_of_lot.",".$$txt_remarks.",'".$user_id."','".$pc_date_time."',".$$txt_sample_date.")";
						$dtlsid=$dtlsid+1;
					}
				}//end if cond
			}
		}

		//print_r($data_array);die;
		//echo "insert into wo_non_order_info_dtls( ".$field_array_insert.") values ".$data_array_insert."";die;
		$rID=$delete_details=$dtlsrIDI=$dtlsrID=true;
		if($mst_id!="")
		{
			$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
			//echo "10***".$field_array_mst.'<br>'.$data_array_mst;die;
		}
		if($txt_delete_row!="")
		{
			$field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$delete_details=sql_multirow_update("wo_non_order_info_dtls",$field_array_dtls_del,$data_array_dtls_del,"id",$txt_delete_row,1);
			//$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",1);
		}
		if($data_array_insert!="")
		{
			$dtlsrIDI=sql_insert("wo_non_order_info_dtls",$field_array_insert,$data_array_insert,1);
		}
		if(count($update_ID)>0)
		{
			// bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
			$dtlsrID=execute_query(bulk_update_sql_statement("wo_non_order_info_dtls","id",$field_array,$data_array,$update_ID),1);
		}
		//echo $dtlsrID;die;
		//echo "10**".$rID."=".$delete_details."=".$dtlsrIDI."=".$dtlsrID;die;
		if($db_type==0)
		{
			if($rID && $dtlsrIDI && $dtlsrID && $delete_details)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_wo_number)."**".str_replace("'","",$update_id)."**".$dtlsidform."**".str_replace("'","",$cbo_wo_basis);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrIDI && $dtlsrID && $delete_details)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_wo_number)."**".str_replace("'","",$update_id)."**".$dtlsidform."**".str_replace("'","",$cbo_wo_basis);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//release lock table
		check_table_status( 175,0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();

		if($db_type==0)	{ mysql_query("BEGIN"); }

		$mst_id=str_replace("'","",$update_id);
		if($mst_id>0) $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$mst_id and status_active=1","pay_mode");
		if($mst_id>0 && $pay_mode==2)
		{

			$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b
			where a.id=b.pi_id and a.item_category_id=1 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$mst_id");
			if(count($pi_sql)>0)
			{
				echo "11**PI Number ".$pi_sql[0][csf("pi_number")]." Found . \n So Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}
		}

		if($mst_id>0 && $pay_mode!=2)
		{

			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate
			from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$mst_id");
			if(count($mrr_sql)>0)
			{
				echo "11**Receive Number ".$mrr_sql[0][csf("recv_number")]." Found .  \n So Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}
		}

		// master table delete here---------------------------------------
		//$mst_id = return_field_value("id","wo_non_order_info_mst","wo_number like $txt_wo_number");
		if($mst_id=="" || $mst_id==0){ echo "15**0"; disconnect($con);die;}
 		$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("wo_non_order_info_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
		if($db_type==0 )
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".$rID."_".$dtlsrID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="wo_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==1) // wo number
			{
				document.getElementById('search_by_th_up').innerHTML="Enter WO Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==2) // supplier
			{
				var supplier_name = '<option value="0">--- Select ---</option>';
				<?
				$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 order by supplier_name",'id','supplier_name');
				foreach($supplier_arr as $key=>$val)
				{
					echo "supplier_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Supplier Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:150px " class="combo_boxes" id="txt_search_common">'+ supplier_name +'</select>';
			}
		}

		function js_set_value(wo_number)
		{
			$("#hidden_wo_number").val(wo_number);
			parent.emailwindow.hide();
		}

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="100">Item Category</th>
                            <th width="130">Search By</th>
                            <th width="150" align="center" id="search_by_th_up">Enter Order Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="100">
                            <?
                                echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", $itemCategory, "",1);
                            ?>
                            </td>
                            <td width="130">
                            <?
                            $searchby_arr=array(1=>"WO Number",2=>"Supplier");
                            echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 0, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
                            ?>
                            </td>
                            <td width="150" align="center" id="search_by_td">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_search_list_view', 'search_div', 'yarn_work_order_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_wo_search_list_view")
{
 	extract($_REQUEST);
	$ex_data = explode("_",$data);
	$itemCategory = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
 	$garments_nature = $ex_data[6];
 	$wo_year = $ex_data[7];

	$sql_cond="";
	//if(trim($itemCategory)!="") $sql_cond .= " and item_category='$itemCategory'";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1)
			$sql_cond .= " and wo_number like '%".trim($txt_search_common)."'";
		else if(trim($txt_search_by)==2)
			$sql_cond .= " and supplier_id=trim('$txt_search_common')";
 	}
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==2)
		{
			$sql_cond .= " and wo_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}
		else
		{
			$sql_cond.= " and wo_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd","-")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd","-")."'";
		}
	}
	else
	{
		if($db_type==0){ $sql_cond .=" and year(insert_date)=".$wo_year.""; }
		else{ $sql_cond .=" and to_char(insert_date,'YYYY')=".$wo_year.""; }
	}
	if(trim($company)!="") $sql_cond .= " and company_name='$company'";

 	$sql = "SELECT id, wo_number_prefix_num, wo_number, company_name, buyer_po, wo_date,supplier_id,attention,wo_basis_id,item_category,currency_id,delivery_date,source,pay_mode, inserted_by, ready_to_approved, is_approved
	from wo_non_order_info_mst where status_active=1 and is_deleted=0 and entry_form=144 $sql_cond order by id desc"; //and garments_nature=$garments_nature
	//echo $sql;//die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Yes');

	$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$wo_basis,6=>$source,7=>$user_library,8=>$is_approved_arr,9=>$is_approved_arr);
	?>
	<table width="1090" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" align="center">
		<thead>
			<tr>
				<th with='100%'><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
		</thead>
	</table>
	<?
	echo  create_list_view("list_view", "Company, WO Number, WO Date, Pay Mode, Supplier, WO Basis, Source, Insert By, Ready To Approve,Approval Status", "150,80,80,80,150,100,60,120,100","1080","240",0, $sql, "js_set_value", "wo_number,id,wo_basis_id", "", 1, "company_name,0,0,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", $arr , "company_name,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id,source,inserted_by,ready_to_approved,is_approved", "","",'0,0,3,0,0,0,0,0,0,0,0');
 	exit();
}

if($action=="populate_data_from_search_popup")
{
	 $sql = "SELECT id, wo_number, company_name, buyer_po, wo_date, wo_type, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode,payterm_id, is_approved, do_no, remarks,tenor,pi_issue_to,ref_closing_status, ready_to_approved, inco_term,ship_mode_id,dealing_marchant
			from  wo_non_order_info_mst where id='$data' and is_deleted=0";
	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $resultRow)
	{
		echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
		//echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
		//echo "$('#cbo_company_name').attr('disabled',true);\n";
		echo "$('#update_id').val('".$resultRow[csf("id")]."');\n";
		//echo "$('#cbo_item_category').val('".$resultRow[csf("item_category")]."');\n";
		echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";
		echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
		echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."');\n";
		echo "$('#cbo_wo_basis').val('".$resultRow[csf("wo_basis_id")]."');\n";
		echo "$('#cbo_wo_basis').attr('disabled',true);\n";
		echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";
		echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
		echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
		echo "$('#txt_buyer_name').val('".$resultRow[csf("buyer_name")]."');\n";
		echo "$('#txt_style').val('".$resultRow[csf("style")]."');\n";
		echo "$('#txt_buyer_po').val('".$resultRow[csf("buyer_po")]."');\n";
		echo "$('#txt_do_no').val('".$resultRow[csf("do_no")]."');\n";
		echo "$('#txt_remarks').val('".$resultRow[csf("remarks")]."');\n";
		echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
        echo "$('#cbo_pi_issue_to').val('".$resultRow[csf("pi_issue_to")]."');\n";
        echo "$('#cbo_payterm_id').val('".$resultRow[csf("payterm_id")]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
        echo "$('#is_approved_id').val('".$resultRow[csf("is_approved")]."');\n";
		echo "$('#cbo_inco_term').val('".$resultRow[csf("inco_term")]."');\n";
		echo "$('#cbo_ship_mode').val('".$resultRow[csf("ship_mode_id")]."');\n";
        echo "$('#cbo_wo_type').val(".$resultRow[csf("wo_type")].");\n";
        echo "$('#cbo_deal_merchant').val(".$resultRow[csf("dealing_marchant")].");\n";

		if($resultRow[csf("ref_closing_status")]==1)
		{
			echo "$('#ref_closed_msg_id').html('Reference Closed');\n";
		}
		echo "$('#ref_closed_sts').val('".$resultRow[csf("ref_closing_status")]."');\n";

		if($resultRow[csf("wo_basis_id")]==3 && $resultRow[csf("buyer_po")]!="")
		{
			$sqlResult = sql_select("select job_no_mst, po_number from wo_po_break_down where id in (".$resultRow[csf("buyer_po")].")");
			$jobNumber=""; $poNumber=""; $i=0;
			foreach($sqlResult as $res)
			{
				if($i>0)
				{
					$poNumber .= ",";
					$jobNumber .= ",";
				}
				$poNumber .= $res[csf("po_number")];
				$jobNumber .= $res[csf("job_no_mst")];
				$i=1;
			}
		}
		if($resultRow[csf("wo_basis_id")]==1)
		{
			if($db_type==0)
			{
				$sql_req=sql_select("select a.mst_id as wo_mst_id, group_concat(a.requisition_no) as req_all, group_concat(a.requisition_dtls_id) as requisition_dtls_id, group_concat(b.mst_id) as req_mst_id from  wo_non_order_info_dtls a, inv_purchase_requisition_dtls b where a.requisition_dtls_id=b.id and a.mst_id='".$resultRow[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by  a.mst_id");


			}
			else if($db_type==2)
			{
				$sql_req=sql_select("select a.mst_id as wo_mst_id, LISTAGG(CAST(a.requisition_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.requisition_no) as req_all, LISTAGG(CAST(a.requisition_dtls_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.requisition_dtls_id) as requisition_dtls_id, LISTAGG(CAST(b.mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.mst_id)  as req_mst_id from  wo_non_order_info_dtls a, inv_purchase_requisition_dtls b where a.requisition_dtls_id=b.id and a.mst_id='".$resultRow[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.mst_id");

			}

            $req_all = implode(',',array_unique(explode(',',$sql_req[0][csf("req_all")])));
            
			echo "$('#txt_requisition').val('".$req_all."');\n";
			echo "$('#txt_req_id').val('".$sql_req[0][csf("req_mst_id")]."');\n";
			echo "$('#txt_req_dtls_id').val('".$sql_req[0][csf("requisition_dtls_id")]."');\n";
			echo "$('#txt_requisition').attr('disabled',false);\n";

		}

		echo "$('#txt_buyer_po_no').val('".$poNumber."');\n";
		echo "$('#txt_job_selected').val('".$jobNumber."');\n";

		//echo "fn_disable_enable('".$resultRow[csf("wo_basis_id")]."');\n";

		if($resultRow[csf("is_approved")]==1)
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($resultRow[csf("is_approved")]==3)
		{
			echo "$('#approved').text('Partial Approved');\n";
		}else{
			echo "$('#approved').text('');\n";
		}

		//echo "document.getElementById('is_approved').value = '".$is_approved."';\n";

		echo "item_rate_match_with_budget('".$resultRow[csf("company_name")]."');\n";
		echo "set_multiselect('txt_buyer_name','0','1','".$resultRow[csf('buyer_name')]."','0');\n";
	}
	exit();
}

if($action=="show_dtls_listview_update")
{
	$data=explode("****",$data);
	
	if($data[1]==1)
	{
		$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$buyer_short_name_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
		$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");

        $requ_rate_arr=return_library_array( "select id, rate from  inv_purchase_requisition_dtls", "id", "rate");

		$sql = "select b.id, a.wo_basis_id, b.requisition_dtls_id, b.requisition_no, b.job_id, b.job_no, b.buyer_id, b.style_no, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.req_quantity, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.yarn_inhouse_date, b.remarks,b.number_of_lot, b.delivery_end_date, b.sample_start_date, b.booking_no, b.is_short
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[0] and a.id=b.mst_id";
		$result=sql_select($sql);
		foreach($result as $row)
		{
			$all_req_dtls_id.=$row[csf("requisition_dtls_id")].",";
		}
		$all_req_dtls_id=implode(",",array_unique(explode(",",chop($all_req_dtls_id,","))));
		if($all_req_dtls_id!="")
		{
			$req_sql=sql_select("select b.id, b.rate, a.basis from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.id in($all_req_dtls_id)");
			$req_data=array();
			foreach($req_sql as $row)
			{
				$req_data[$row[csf("id")]]["rate"]=$row[csf("rate")];
				$req_data[$row[csf("id")]]["basis"]=$row[csf("basis")];
			}
		}

		//Precost rate ...............................................................................start;
		$sql_precost = "select a.job_no,a.count_id,a.type_id,a.copm_one_id,a.rate from wo_pre_cost_fab_yarn_cost_dtls a,wo_non_order_info_dtls b where a.job_no=b.job_no and b.mst_id =$data[0]";
		$result_precost=sql_select($sql_precost);
		foreach($result_precost as $row){
			$key=$row[csf('job_no')].$row[csf('count_id')].$row[csf('type_id')].$row[csf('copm_one_id')];
			$pre_cost_rate_arr[$key]=$row[csf('rate')];
		}

		/*$sql_precost = "select a.job_no,a.count_id,a.type_id,a.copm_one_id,a.rate from wo_pre_cost_fab_yarn_cost_dtls a,inv_purchase_requisition_dtls b where a.job_no=b.job_no  and b.id in ($req_dtls_id_all)";
		$result_precost=sql_select($sql_precost);
		foreach($result_precost as $row){
			$key=$row[csf('job_no')].$row[csf('count_id')].$row[csf('type_id')].$row[csf('copm_one_id')];
			$pre_cost_rate_arr[$key]=$row[csf('rate')];
		}*/
		//Precost rate ...............................................................................end;
		

		?>
		
		<div style="width:1340px;" align="left">
		<table cellspacing="0" width="1440" class="rpt_table" id="tbl_details" border="1" rules="all" >
			<thead>
				<tr>
					<th width="40">Req. No</th>
					<th width="100">Job No</th>
					<th width="100">Booking No</th>
					<th width="50">Buyer</th>
					<th width="80">Style</th>
					<th width="70">Yarn Color</th>
					<th width="55">Count</th>
					<th width="90">Composition</th>
					<th width="25">%</th>
					<th width="70">Yarn Type</th>
					<th width="40">UOM</th>
					<th width="55">Req. Qnty</th>
					<th width="55">WO. Qnty</th>
					<th width="50">Rate</th>
					<th width="80">Value</th>
					<th width="50">Sample Delv. Start</th>
                    <th width="50">Bulk Delv. Start &nbsp;<input type="checkbox" id="bulk_delvi_start" name="bulk_delvi_start" onChange="check_all_report(this.id);"/></th>
					<th width="50">Bulk Delv. End &nbsp;<input type="checkbox" id="bulk_delvi_end" name="bulk_delvi_end" onChange="check_all_report(this.id);"/></th>
                    <th width="40">No. of Lot &nbsp;<input type="checkbox" id="no_of_lot"  name="no_of_lot" onChange="check_all_report(this.id);"/></th>
                    <th width="70">IR/Remarks</th>
					<th>Action.</th>
				</tr>
			</thead>
			<tbody>
			<?
			$i=1;
			foreach($result as $row)
			{
                $requ_rate= "";
                if($row[csf("wo_basis_id")] == 1)
                {
                    $requ_rate = $req_data[$row[csf("requisition_dtls_id")]]["rate"];
					$requ_basis = $req_data[$row[csf("requisition_dtls_id")]]["basis"];
                }

				if($row[csf('yarn_inhouse_date')]!="" && $row[csf('yarn_inhouse_date')]!="0000-00-00") $inhouse_date=change_date_format($row[csf('yarn_inhouse_date')]); else $inhouse_date="&nbsp;";
                if($row[csf('delivery_end_date')]!="" && $row[csf('delivery_end_date')]!="0000-00-00") $delivery_end_date=change_date_format($row[csf('delivery_end_date')]); else $delivery_end_date="&nbsp;";
				if($row[csf('sample_start_date')]!="" && $row[csf('sample_start_date')]!="0000-00-00") $sample_start_date=change_date_format($row[csf('sample_start_date')]); else $sample_start_date="&nbsp;";
				$req_no_ref=explode('-',$row[csf("requisition_no")]);
				$req_no=$req_no_ref[3]*1;

				$key=$row[csf('job_no')].$row[csf('yarn_count')].$row[csf('yarn_type')].$row[csf('yarn_comp_type1st')];
				if($wo_pi_data[$row[csf("id")]]) $decrement_disable=" disabled "; else $decrement_disable="";
				// echo $decrement_disable."test<br>";
				?>
				<tr class="general" id="<? echo $i;?>">
                    <td>
                    <input type="hidden" name="txt_req_<? echo $i;?>" id="txt_req_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $row[csf("requisition_no")];?>" disabled readonly />
                    <input type="text" name="txt_req_<? echo $i;?>" id="txt_req_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $req_no;?>" disabled readonly />
                    <input type="hidden" name="txt_req_dtls_id_<? echo $i;?>" id="txt_req_dtls_id_<? echo $i;?>" class="text_boxes"  value="<? echo $row[csf("requisition_dtls_id")];?>" disabled readonly />
                    <input type="hidden" name="txt_po_<? echo $i;?>" id="txt_po_<? echo $i;?>" class="text_boxes"  value="" disabled readonly />
                    <input type="hidden" name="txt_po_brakdown_id_<? echo $i;?>" id="txt_po_brakdown_id_<? echo $i;?>" value="" disabled readonly />
                    <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="<? echo $row[csf("id")]; ?>" />
                    <input type="hidden" name="txt_requ_rate_<? echo $i;?>" id="txt_requ_rate_<? echo $i;?>" value="<? echo $requ_rate;?>" />
                    <input type="hidden" name="txt_requ_basis_<? echo $i;?>" id="txt_requ_basis_<? echo $i;?>" value="<? echo $requ_basis;?>" />
                    </td>
                    <td>
                    <input type="text" name="txt_job_<? echo $i;?>" id="txt_job_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("job_no")];?>" disabled readonly />
                    <input type="hidden" name="txt_job_id_<? echo $i;?>" id="txt_job_id_<? echo $i;?>" class="text_boxes" value="<? echo $row[csf("job_id")];?>" disabled readonly />
                    </td>
					<td title="This Field Only For Requisition Based on Booking Basis">
						<input type="text" name="txt_booking_<? echo $i;?>" id="txt_booking_<? echo $i;?>" class="text_boxes" style="width:90px"  value="<? echo $row[csf("booking_no")];?>" readonly />
                        <input type="hidden" id="isShort_<? echo $i; ?>" name="isShort_<? echo $i; ?>"  value="<? echo $row[csf("is_short")];?>">
					</td>
                    <td >
                    <input type="text" name="txt_buyer_<? echo $i;?>" id="txt_buyer_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $buyer_short_name_arr[$row[csf("buyer_id")]];?>" readonly disabled/>
                    <input type="hidden" name="txt_buyer_id_<? echo $i;?>" id="txt_buyer_id_<? echo $i;?>" value="<? echo $row[csf("buyer_id")];?>" disabled  />
                    </td>
                    <td >
                    <input type="text" name="txt_style_<? echo $i;?>" id="txt_style_<? echo $i;?>" class="text_boxes" style="width:75px" value="<? echo $row[csf("style_no")];?>" readonly disabled />
                    </td>
                    <td >
                    <input type="text" name="txt_color_<? echo $i;?>" id="txt_color_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $color_arr[$row[csf("color_name")]];?>" readonly disabled />
                    <input type="hidden" name="hidden_colorID_<? echo $i;?>" id="hidden_colorID_<? echo $i;?>" value="<? echo $row[csf("color_name")];?>" disabled  />
                    </td>
                    <td >
                    <?
                    echo create_drop_down( "cbocount_".$i, 50, $yarn_count_arr,"", 1, "Select", $row[csf("yarn_count")], "",1 );
                    ?>
                    </td>
                    <td title="<? $composition[$row[csf("yarn_comp_type1st")]];?>"><?  echo create_drop_down( "cbocompone_".$i, 90, $composition,"", 1, "-- Select --", $row[csf("yarn_comp_type1st")], "",1,"","","",$ommitComposition ); ?></td>
                    <td ><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:20px" value="<? echo $row[csf("yarn_comp_percent1st")];  ?>" disabled  /></td>

                    <td  style="display:none"><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_two_id")], "control_composition($i,this.id,'percent_two')",1,"","","",$ommitComposition ); ?></td>

                    <td>
                    <?
                    echo create_drop_down( "cbotype_".$i, 70, $yarn_type,"", 1, "Select", $row[csf("yarn_type")], "",1,"","","",$ommitYarnType );
                    ?>
                    </td>
                    <td  style="display:none"><input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("percent_two")];  ?>" disabled /></td>
                    <td>
                    <?
                    echo create_drop_down( "cbo_uom_".$i, 40, $unit_of_measurement,"", 1, "Select", $row[csf("uom")], "",1 );
                    ?>
                    </td>
                    <td > <input type="text" name="txt_req_qnty_<? echo $i;?>" id="txt_req_qnty_<? echo $i;?>"  class="text_boxes_numeric" style="width:55px;" value="<? echo number_format($row[csf("req_quantity")],2,".","");?>" readonly disabled /></td>
                    <td>
                    <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:55px;" value="<? echo number_format($row[csf("supplier_order_quantity")],2,".","");?>" />
                    </td>
                    <td >
                    <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>,<? echo $pre_cost_rate_arr[$key]*1;?>)"  class="text_boxes_numeric"  style="width:50px;" value="<? echo number_format($row[csf('rate')],4,".","");?>" />
                    </td>
                    <td>
                    <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:75px;" value="<? echo number_format($row[csf("amount")],2,".","");?>" readonly />
                    </td>
                    <td>
                    <input type="text" name="txt_sample_date_<? echo $i;?>" id="txt_sample_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $sample_start_date; ?>" />
                    </td>
                    <td>
                    <input type="text" name="txt_inhouse_date_<? echo $i;?>" id="txt_inhouse_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $inhouse_date; ?>" onChange="CompareDate(<? echo $i;?>)" />
                    </td>
                    <td>
                    <input type="text" name="txt_delivery_end_date_<? echo $i;?>" id="txt_delivery_end_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $delivery_end_date; ?>" onChange="CompareDate(<? echo $i;?>)" />
                    </td>
                    <td width="50">
                        <input type="text" name="txt_number_of_lot_<? echo $i;?>" id="txt_number_of_lot_<? echo $i;?>" class="text_boxes_numeric " style="width:50px;" value="<?  echo $row[csf("number_of_lot")]; ?>" />
                    </td>
                    <td>
                    <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:75px;" value="<?  echo $row[csf("remarks")]; ?>"/>
                    </td>
                    <td style="display: inline-block; width: 65px;">
                    <input type="button" name="txtview_<? echo $i;?>" id="txtview_<? echo $i;?>" class="formbuttonplasminus" value="View"  style="width:35px;" onClick="javascript:fn_view(<? echo $i;?>);" />
                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:25px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" <?= $decrement_disable;?> />
                    </td>
				</tr>
				<?
				$i++;
				$total_req_qnty+=$row[csf("req_quantity")];
				$total_wo_qnty+=$row[csf("supplier_order_quantity")];
				$total_wo_value+=$row[csf("amount")];
			}
			?>
			</tbody>
            <tfoot>
            	<th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total</th>
                <th colspan="2"><? echo number_format($total_req_qnty,2); ?></th>
                <th><? echo number_format($total_wo_qnty,2); ?></th>
                <th colspan="2"><? echo number_format($total_wo_value,2); ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
		</table>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</div>
        <?
	}
	else
	{
		$sql_pi="select ID, WORK_ORDER_DTLS_ID from COM_PI_ITEM_DETAILS where status_active=1 and is_deleted=0 and item_category_id=1 and WORK_ORDER_ID=$data[0]";
		$sql_pi_result=sql_select($sql_pi);
		//$wo_pi_data=array();
		foreach($sql_pi_result as $val)
		{
			$wo_pi_data[$val["WORK_ORDER_DTLS_ID"]]=$val["ID"];
		}
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$sql = "select b.id, a.wo_basis_id, b.po_breakdown_id, b.requisition_no, b.item_id,b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, b.req_quantity, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.yarn_inhouse_date,b.number_of_lot, b.remarks, c.po_number, b.delivery_end_date, b.sample_start_date
		from wo_non_order_info_mst a, wo_non_order_info_dtls b left join wo_po_break_down c on b.po_breakdown_id=c.id
		where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[0] and a.id=b.mst_id";

		$result = sql_select($sql);

		//Precost rate ...............................................................................start;
		$sql_precost = "select c.po_breakdown_id,a.count_id,a.type_id,a.copm_one_id,a.rate from wo_pre_cost_fab_yarn_cost_dtls a,wo_po_break_down b,wo_non_order_info_dtls c where a.job_no=b.job_no_mst and c.po_breakdown_id=b.id  and c.mst_id =$data[0]";
		$result_precost=sql_select($sql_precost);
		foreach($result_precost as $row){
			$key=$row[csf('po_breakdown_id')].$row[csf('count_id')].$row[csf('type_id')];//.$row[csf('copm_one_id')]
			$pre_cost_rate_arr[$key]=$row[csf('rate')];
		}
		//Precost rate ...............................................................................end;


		$totalQnty=$totalValue=0;
		$i=1;
		foreach($result as $row)
		{
			$totalQnty += $row[csf("supplier_order_quantity")];
			$totalValue += $row[csf("amount")];
			if($row[csf('yarn_inhouse_date')]!="" && $row[csf('yarn_inhouse_date')]!="0000-00-00") $inhouse_date=change_date_format($row[csf('yarn_inhouse_date')]); else $inhouse_date="&nbsp;";
            if($row[csf('delivery_end_date')]!="" && $row[csf('delivery_end_date')]!="0000-00-00") $delivery_end_date=change_date_format($row[csf('delivery_end_date')]); else $delivery_end_date="&nbsp;";
			if($row[csf('sample_start_date')]!="" && $row[csf('sample_start_date')]!="0000-00-00") $sample_start_date=change_date_format($row[csf('sample_start_date')]); else $sample_start_date="&nbsp;";
			$req_no_ref=explode('-',$row[csf("requisition_no")]);
			$req_no=$req_no_ref[3]*1;

			$key=$row[csf('po_breakdown_id')].$row[csf('yarn_count')].$row[csf('yarn_type')];//.$row[csf('yarn_comp_type1st')]
			if($wo_pi_data[$row[csf("id")]]) $decrement_disable=" disabled "; else $decrement_disable="";
			if($i==1)
			{
				?>
				<div style="width:1320px;">
					<table cellspacing="0" width="100%" class="rpt_table" id="tbl_details" border="1" rules="all" >
						<thead>
							<tr>
								<? if($row[csf("wo_basis_id")]==3 ){?>
								<th>PO Number</th>
								<? } ?>
								<th>Color</th>
								<th>Count</th>
								<th>Comp 1</th>
								<th>%</th>
								<th style="display:none">Comp 2</th>
								<th style="display:none">%</th>
								<th>Yarn Type</th>
								<th>UOM</th>
								<th>Quantity</th>
								<th>Rate</th>
								<th>Value</th>
                                <th>Sample Delv. Start</th>
                                <th width="50">Bulk Delv. Start &nbsp;<input type="checkbox" id="bulk_delvi_start" name="bulk_delvi_start" onChange="check_all_report(this.id);"/></th>
								<th width="50">Bulk Delv. End &nbsp;<input type="checkbox" id="bulk_delvi_end" name="bulk_delvi_end" onChange="check_all_report(this.id);"/></th>
                                <th>No. of Lot &nbsp;<input type="checkbox" id="no_of_lot"  name="no_of_lot" onChange="check_all_report(this.id);"/></th>
                                <th>IR/Remarks</th>
								<th>Action</th>
							</tr>
						</thead>
			 	<? 
			} 
			?>
                    <tr class="general" id="<? echo $i;?>">
                         <!-- This is for buyer po selected in WO Basis -->
                        <? if($row[csf("wo_basis_id")]==3)
                        {
                            echo "<td width=\"130\">";
                            $disble="disabled";
                            $disble_combo="1";
                        }
                        else
                        {
                            $disble="";
                            $disble_combo="0";
                        }
                        ?>
                            <input type="<? if($row[csf("wo_basis_id")]==3)echo 'text'; else echo 'hidden';?>" name="txt_po_<? echo $i;?>" id="txt_po_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf("po_number")]; ?>" disabled readonly />
                            <input type="hidden" name="txt_po_brakdown_id_<? echo $i;?>" id="txt_po_brakdown_id_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")]; ?>" disabled readonly />
                            <input type="hidden" name="txt_row_id_<? echo $i;?>" id="txt_row_id_<? echo $i;?>" value="<? echo $row[csf("id")]; ?>" disabled />
                        <? if($row[csf("wo_basis_id")]==3){
                        echo "</td>";
                        } ?>
                         <!-- This is for buyer po selected in WO Basis END -->
                         <td width="80">
                            <input type="text" name="txt_color_<? echo $i;?>" id="txt_color_<? echo $i;?>" class="text_boxes" onKeyPress="colorName(<? echo $i;?>)" onKeyUp="fn_copy_color(<? echo $i;?>)" style="width:80px" value="<? echo $color_arr[$row[csf("color_name")]]; ?>" />
                            <input type="hidden" id="hidden_colorID_<? echo $i;?>" value="<? echo $row[csf("color_name")]; ?>" disabled  />
                        </td>
                        <td width="90">
                            <?
                                echo create_drop_down( "cbocount_".$i, 90, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "Select", $row[csf("yarn_count")], "",$disble_combo );
                            ?>
                        </td>
                        <td width="170" title="<? echo $composition[$row[csf("yarn_comp_type1st")]];?>">
                            <?  echo create_drop_down( "cbocompone_".$i, 170, $composition,"", 1, "-- Select --", $row[csf("yarn_comp_type1st")], "",$disble_combo,"","","",$ommitComposition ); ?></td>
                        <td width="30"><input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $row[csf("yarn_comp_percent1st")];  ?>" <? echo $disble; ?>  /></td>
                        <td width="100" style="display:none"><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("yarn_comp_type2nd")], "control_composition($i,this.id,'percent_two')",$disble_combo,"",0,"",$ommitComposition ); ?></td>
                        <td width="40" style="display:none"><input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("yarn_comp_percent2nd")];  ?>"  <? echo $disble; ?> /></td>
                        <td width="100">
                            <?
                                echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "Select", $row[csf("yarn_type")], "",$disble_combo,"","","",$ommitYarnType );
                            ?>
                        </td>
                        <td width="50">
                            <?
                                echo create_drop_down( "cbo_uom_".$i, 70, $unit_of_measurement,"", 1, "Select", $row[csf("uom")], "",1 );
                            ?>
                        </td>
                        <td width="50">
                            <input type="text" name="txt_quantity_<? echo $i;?>" id="txt_quantity_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>)"  class="text_boxes_numeric" style="width:50px;" value="<? echo $row[csf("supplier_order_quantity")];?>" />
                        </td>
                        <td width="40">
                            <input type="text" name="txt_rate_<? echo $i;?>" id="txt_rate_<? echo $i;?>" onKeyUp="calculate_yarn_consumption_ratio(<? echo $i;?>,<? echo $pre_cost_rate_arr[$key]*1;?>)"  class="text_boxes_numeric"  style="width:40px;" value="<? echo $row[csf("rate")];?>"  />
                        </td>
                        <td width="80">
                            <input type="text" name="txt_amount_<? echo $i;?>" id="txt_amount_<? echo $i;?>" class="text_boxes_numeric" style="width:80px;" value="<? echo number_format($row[csf("amount")],3,".","");?>"  readonly  />
                        </td>
                        <td width="55">
                        <input type="text" name="txt_sample_date_<? echo $i;?>" id="txt_sample_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $sample_start_date; ?>" />
                        </td>
                        <td width="55">
                        <input type="text" name="txt_inhouse_date_<? echo $i;?>" id="txt_inhouse_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $inhouse_date; ?>" />
                        </td>
                        <td width="55">
                        <input type="text" name="txt_delivery_end_date_<? echo $i;?>" id="txt_delivery_end_date_<? echo $i;?>" class="datepicker" style="width:50px;" value="<?  echo $delivery_end_date; ?>" onChange="CompareDate(<? echo $i;?>)" />
                        </td>
                        <td width="40">
                            <input type="text" name="txt_number_of_lot_<? echo $i;?>" id="txt_number_of_lot_<? echo $i;?>" class="text_boxes_numeric " style="width:40px;" value="<?  echo $row[csf("number_of_lot")]; ?>" />
                        </td>
                        <td width="70">
                        <input type="text" name="txt_remarks_<? echo $i;?>" id="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:65px;" value="<?  echo $row[csf("remarks")]; ?>" />
                        </td>
                        <td width="110">
                            <input type="button" name="txtview_<? echo $i;?>" id="txtview_<? echo $i;?>" class="formbuttonplasminus" value="View"  style="width:35px;" onClick="javascript:fn_view(<? echo $i;?>);" />
                         <? if($row[csf("wo_basis_id")]!=3){?>
                             <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                         <? } ?>
                             <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" <?= $decrement_disable;?> />
                        </td>
                    </tr>
                    <?
					$i++;
			}
			?>
			<tfoot>
				<? if($row[csf("wo_basis_id")]==3 ) $col_span=7; else $col_span=6; ?>
				<tr>
					<th colspan="<? echo $col_span; ?>" align="right">Sum</th>
					<th align="right" id="tot_qnty"><? echo number_format($totalQnty,2); ?></th>
					<th></th>
					<th align="right" id="tot_value"><? echo number_format($totalValue,2); ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
		</div>
		<?
	}
	exit();
}

if($action=="previous_dtls_id")
{
	// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
	if($db_type==0)
	{
		$previous_dtls_id=return_field_value("$group_concat(id)","wo_non_order_info_dtls","mst_id='".trim($data)."' and status_active=1 and is_deleted=0");
	}
	else
	{
		$previous_dtls_id=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","wo_non_order_info_dtls","mst_id='".trim($data)."' and status_active=1 and is_deleted=0","id");
	}
	echo $previous_dtls_id;
	exit();
}



if ($action=="print_to_html_report")
{
	    extract($_REQUEST);
		$data=explode('*',$data);
        if ($data[6]=='YarnPurchaseOrderPrintButton'){
            echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');
        } else {
            echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
        }
		
		//print_r ($data);
		/*if($db_type==0)
		{
			$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, $group_concat(b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks  from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= '$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
		}
		// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
		else
		{
			$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, LISTAGG(CAST (b.po_breakdown_id as varchar(4000) ), ',')  WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= $data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, a.buyer_name, a.style, a.do_no, a.remarks";
		}*/

		$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
		//if(($data[4]==1 || $data[4]==0) && $user_level_library[$user_id]==2)
		if(($data[4]==1 && $user_level_library[$user_id]==2) || ($data[4]==0))
		{

			$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
			$location=return_field_value("city","lib_company","id=$data[0]" );
			$address=return_field_value("address","lib_location","id=$data[0]");
			$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );


			$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
			$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
			$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
			$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
			$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
		 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

			//echo "SELECT distinct a.id, a.wo_number_prefix_num,b.requisition_no,d.booking_no, a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id, a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id, a.remarks,a.do_no, a.insert_date  FROM  wo_non_order_info_mst a,wo_non_order_info_dtls b,inv_purchase_requisition_mst c,inv_purchase_requisition_dtls d WHERE a.id=b.mst_id and c.id=d.mst_id and c.requ_no=b.requisition_no and a.id = $data[1] and b.mst_id=$data[1]";
			$sql_data = sql_select("SELECT distinct a.id, a.wo_number_prefix_num,b.requisition_no,d.booking_no, a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id, a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id, a.remarks,a.do_no, a.insert_date  FROM  wo_non_order_info_mst a,wo_non_order_info_dtls b,inv_purchase_requisition_mst c,inv_purchase_requisition_dtls d WHERE a.id=b.mst_id and c.id=d.mst_id and c.requ_no=b.requisition_no and a.id = $data[1] and b.mst_id=$data[1]");
            //$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks,do_no, insert_date  FROM  wo_non_order_info_mst WHERE id = $data[1]");

			$requisition_no='';
			$booking_no='';
			foreach($sql_data as $row)
			{
				$work_order_no=$row[csf("wo_number")];
				$item_category_id=$row[csf("item_category")];
				$supplier_id=$row[csf("supplier_id")];
				$work_order_date=$row[csf("wo_date")];
				$currency_id=$row[csf("currency_id")];
				$buyer_name=$row[csf("buyer_name")];
				$style=$row[csf("style")];
				$wo_basis_id=$row[csf("wo_basis_id")];
				$pay_mode_id=$row[csf("pay_mode")];
				$pay_term_id=$row[csf("payterm_id")];
				$source=$row[csf("source")];
				$delivery_date=$row[csf("delivery_date")];
				$attention=$row[csf("attention")];

				$delivery_place=$row[csf("delivery_place")];
				$do_no=$row[csf("do_no")];
				$remarks=$row[csf("remarks")];
				$insert_date=$row[csf("insert_date")];
				$requisition_no.=$row[csf("requisition_no")].',';
		        if($row[csf("booking_no")]=='0')
				{
				    continue;
				}
				else
				{
				    $booking_no.=$row[csf("booking_no")].',';
				}


			}
			//$pay_mode=array(1=>"Credit",2=>"Import",3=>"In House",4=>"Cash",5=>"Within Group");
			if($pay_mode_id=='1')
			{
			$pay_mode_str='Credit';
			}
			else if($pay_mode_id=='2')
			{
			$pay_mode_str='Import';
			}
			  else if($pay_mode_id=='3')
			{
			$pay_mode_str='In House';
			}
			  else if($pay_mode_id=='4')
			{
			$pay_mode_str='Cash';
			}
			  else if($pay_mode_id=='5')
			{
			$pay_mode_str='Within Group';
			}
			//$source=array(1=>"Abroad",2=>"EPZ",3=>"Non-EPZ");

			  if($source=='1')
			  {
			  $source_str='Abroad';
			  }else if($source=='2')
			  {
			  $source_str='EPZ';
			  }else if($source=='3')
			  {
			  $source_str='Non-EPZ';
			  }

			//$pay_mode=return_field_value("pa","lib_company","id=$data[0]" );
			$sql_job=sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($sql_job as $row)
			{
				$buyer_job_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
				$buyer_job_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
				$buyer_job_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$buyer_job_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
				$buyer_job_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			}


			$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = '$supplier_id'");

		/*print_r("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");*/
		   foreach($sql_supplier as $supplier_data)
			{//contact_no
				$row_mst[csf('supplier_id')];

				if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
				if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
				if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
				if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
				if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
				if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
				if($supplier_data[csf('supplier_name')]!='')$supplier_name = $supplier_data[csf('supplier_name')].','.' ';else $supplier_name='';
				if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
				//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
				$country = $supplier_data['country_id'];
				$supplier_name=$supplier_name;
				$supplier_address = $address_1;
				$supplier_country =$country;
				$supplier_phone =$contact_no;
				$supplier_email = $email;
			}
			$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
			$varcode_booking_no=$work_order_no;
			?>
			<div style="width:930px;">
		    <table width="900" cellspacing="0" align="center">
		        <tr>
		        	<td rowspan="3" width="70">
                        <?
                        if ($data[6]=='YarnPurchaseOrderPrintButton')
                        {   
                            ?>
                            <img src="../../<? echo $image_location; ?>" height="70" width="200">
                            <?
                        } 
                        else 
                        { 
                            ?>
                            <img src="../../../<? echo $image_location; ?>" height="70" width="200">
                            <?    
                        }
                        ?>    
                    </td>
		            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
		            <td rowspan="3" id="barcode_img_id"> </td>
		        </tr>
		        <tr class="form_caption">
		        	<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>
		        </tr>
		        <tr>
		            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
		        </tr>
                <tr>
                    <?
                        if($db_type==0)
                        {
                            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
                        }
                        else
                        {
                            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
                        }
                        //echo $approval_status;
                        $approval_status=sql_select($approval_status);
                        $draft = '';
                        if($approval_status[0][csf('approval_need')] == 1)
                        {
                            if($data[5] != 1){
                                $draft = 'Draft';
                            }
                        }

                    ?>
                    <td colspan="2" align="center" style="font-size:x-large"><strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;<font style="color:#F00"><? if($data[5]==1){ echo "(Approved)";}else{echo $draft;}; ?> </font></strong></td>
                </tr>
		    </table>
		    <table width="900" cellspacing="0" align="center">
		         <tr>
		            <td width="300" align="left"><strong>To</strong>,&nbsp;<? echo $attention ?></td>
		            <td width="150"><strong>WO Number:</strong></td>
		            <td width="150" align="left"><? echo $work_order_no; ?></td>
		                        <td><strong>Pay Mode:</strong></td>
		            <td align="left"><? echo $pay_mode_str; ?></td>

		        </tr>
		        <tr>
		           <td rowspan="4"><? echo $supplier_name_library[$supplier_id]; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Mobile :".$supplier_phone; echo "<br>"; echo "Mail :".$supplier_email; ?></td>
		            <td width="150" align="left" ><strong>WO Date :</strong></td>
		            <td width="150" align="left"><? echo change_date_format($work_order_date); ?></td>

					<td><strong>Currency:</strong></td>
		            <td align="left"><? echo $currency[$currency_id]; ?></td>

		        </tr>
		        <tr>
		            <td ><strong>Delivery Date :</strong></td>
		            <td><? echo change_date_format($delivery_date); ?></td>


		            <td align="left"><strong>Source</strong></td>
		            <td align="left" ><? echo $source_str; ?></td>
		        </tr>
		        <tr>
		            <td align="left" ><strong>Print Date:</strong></td>
		            <td> <? $pc_day_time=explode(" ",$pc_date_time); echo change_date_format($pc_day_time[0]); echo " ".$pc_day_time[1]." ".$pc_day_time[2]; ?></td>
		                        <td align="left" ><strong>WO Basis:</strong></td>
		            <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>
		        </tr>
		        <tr>
		            <td ><strong>Pay Term :</strong></td>
		            <td><? echo $pay_term[$pay_term_id]; ?></td>


		            <td align="left"><strong>Tenor</strong></td>
		            <td align="left" ><? echo $source; ?></td>
		        </tr>
		        <tr>
		<td></td>
		            <td ><strong>Req.NO:</strong></td>
		            <td colspan="3"><? echo $requisition_no; ?></td>



		        </tr>
		        <tr>
		      <td></td>
		            <td ><strong>Fab.Booking No :</strong></td>
		            <td colspan="3"><? echo $booking_no;  ?></td>



		        </tr>
		        <tr>
		          <td>Dear Sir,</td>
		        </tr>
		        <tr>
		          <td colspan="3">
					Pleased to inform You that Your price offer has been accepted with the following terms .
				 </td>
		        </tr>
		    </table>
		         <br>
		         <?
				 	if($wo_basis_id==3)
					{
						$buy_job_sty="Buyer Job Style";
					}
					else
					{
						$buy_job_sty="Buyer Style";
					}
				 ?>
		    <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="70">Color</th>
		            <th width="60">Count</th>
		            <th width="250">Item Description</th>
		            <th width="50" >UOM</th>
		            <th width="70">Quantity </th>
		            <th width="60">Rate</th>
		            <th width="60">Amount</th>
		        </thead>
		        <tbody>
		<?
			$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

			$i=1; $buy_job_sty_val="";$carrency_id="";
			$mst_id=$dataArray[0][csf('id')];

			//$sql_dtls="Select a.id, a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";


			$sql_dtls="Select a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
			$sql_result = sql_select($sql_dtls);
			foreach($sql_result as $row)
			{
					$feb_des='';
					if($row[csf("yarn_comp_type2nd")]==0)
					{
						$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %, '.$yarn_type[$row[csf("yarn_type")]];
					}
					else if( $row[csf("yarn_comp_type2nd")]!=0)
					{
						$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %,'.$composition[$row[csf("yarn_comp_type2nd")]].' '.$row[csf("yarn_comp_percent2nd")].' %, '.$yarn_type[$row[csf("yarn_type")]];
					}

					$key=$row[csf("po_breakdown_id")].$row[csf("color_name")].$row[csf("yarn_count")].$feb_des.$row[csf("uom")];
					$dataArr[$key]=array(
						po_breakdown_id=>$row[csf("po_breakdown_id")],
						color_name=>$row[csf("color_name")],
						yarn_count=>$row[csf("yarn_count")],
						uom=>$row[csf("uom")],
						feb_des=>$feb_des
					);
					$qtyArr[$key]+=$row[csf("supplier_order_quantity")];
					$amuArr[$key]+=$row[csf("amount")];

		$carrency_id=$row[csf('currency_id')];
		if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}

			}

			//var_dump($dataArr);

			//var_dump($amuArr);
			//	var_dump($qtyArr);

			//echo $sql_dtls;
			$sql_result = sql_select($sql_dtls);
			foreach($dataArr as $key=>$row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($wo_basis_id==2)
					{
						$buyer_name_val="";
						$buyer_id=explode(',',$buyer_name);
						foreach($buyer_id as $val)
						{
							if($buyer_name_val=="") $buyer_name_val=$buyer_arr[$val]; else $buyer_name_val.=', '.$buyer_arr[$val];
						}

						$buy_job_sty_val=$buyer_name_val."<br>".$style;
					}
					else if($wo_basis_id==3)
					{
						if($row["po_breakdown_id"]!="" && $row["po_breakdown_id"]!=0)
						{
							$buyer_name_val=$buyer_arr[$buyer_job_arr[$row["po_breakdown_id"]]["buyer_name"]]."<br>".$buyer_job_arr[$row["po_breakdown_id"]]["job_no"]."<br>".$buyer_job_arr[$row["po_breakdown_id"]]["style_ref_no"]."<br>";
						}
						$buy_job_sty_val=$buyer_name_val;
					}



				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
		                <td align="center"><? echo $i; ?></td>
		                <?

						?>

		                <td align="center"><p><? echo $color_arr[$row["color_name"]]; ?></p></td>
		                <td align="center"><p><? echo $count_arr[$row["yarn_count"]]; ?></p></td>
		                <td align="center"><p><? echo $row[feb_des]; ?></p></td>
		                <td align="center"><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
		                <td align="right"><p><? echo number_format($qtyArr[$key],2); ?></p></td>
		                <td align="right"><p><? echo number_format($amuArr[$key]/$qtyArr[$key],4,".",""); ?></p></td>
		                <td align="right"><p><? echo number_format($amuArr[$key],2,".","");?></p></td>

					</tr>
					<? $i++; } ?>
		        </tbody>
		        <tfoot>
		                <th colspan="5" align="right">Total </th>
		                <th align="right"><? echo number_format(array_sum($qtyArr),0); ?></th>
		                <th>&nbsp;</th>
		                <th align="right"><? echo $word_amount=number_format(array_sum($amuArr),2,".",""); ?></th>
		                <tr>
		                <th colspan="8" align="left">In words: <span style="font-weight:normal !important;"><? echo number_to_words($word_amount,$currency[$carrency_id],$paysa_sent); ?></span></th>

		                </tr>
		        </tfoot>
		    </table>

		    <br>
		    		<? echo get_spacial_instruction($work_order_no,"900px",144);?>

		            <div style="margin:20px 0px 0px 20px;">
		            Your scheduled delivery with quality and co-operation will be highly appreciated. <br>
		            Thank You
					</div>


		     <?
		        echo signature_table(42, $data[0], "900px", "", "", $user_id);
		     ?>
			</div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		    <script>
		    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
		    </script>
			<?
		    exit();
			}
		else{}
	}


if ($action == "print_to_html_report2") {
    extract($_REQUEST);
    $data = explode('*', $data);

    if ($data[6]=='YarnPurchaseOrderPrintButton'){
        echo load_html_head_contents($data[2], "../../../", 1, 1, $unicode, '', '');
    } else {
        echo load_html_head_contents($data[2], "../../", 1, 1, $unicode, '', '');
    }

    

    
    //print_r ($data);
	$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	//if(($data[4]==1 || $data[4]==0) && $user_level_library[$user_id]==2)
	$data[4]=0;
	if(($data[4]==1 && $user_level_library[$user_id]==2) || ($data[4]==0))
	{
	    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	    $location = return_field_value("city", "lib_company", "id=$data[0]");
	    $address = return_field_value("address", "lib_location", "id=$data[0]");
	    $lib_country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");

	    $item_name_arr = return_library_array("select id,item_name from lib_item_group", "id", "item_name");
	    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier', 'id', 'supplier_name');
	    $lib_terms_condition = return_library_array("select id, terms from lib_terms_condition", 'id', 'terms');
	    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	    if($db_type == 0){ //CONVERT (int, '10') //, wo_po_break_down c //and b.po_breakdown_id = c.id 
	        $sql = "select a.id as wo_id,  a.wo_number, a.buyer_po, a.wo_basis_id, cast(b.requisition_no as nvarchar2(20)) as req_nos, cast('' as nvarchar2(25)) as booking_no, a.supplier_id as supplier_id, a.wo_date as wo_date, a.currency_id as currency_id, a.buyer_name as buyer_id, a.style, a.pay_mode, a.source, a.delivery_date,a.tenor, a.attention, a.remarks, a.inco_term, a.payterm_id as payterm_id
			from wo_non_order_info_mst a,wo_non_order_info_dtls b 
			where a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0 and a.company_name = $data[0]
			union all 
			select 0 as wo_id,  null as wo_number, cast(s.id as nvarchar2(200)) as buyer_po, p.basis as wo_basis_id, p.requ_no as req_nos, q.booking_no as booking_no, cast(p.supplier_id as nvarchar2(50)) as supplier_id, cast('' as date) as wo_date, cast('' as number) as currency_id, cast(q.buyer_id as nvarchar2(20)) as buyer_id, q.style_ref_no as style, p.pay_mode, p.source, p.delivery_date, 0 as tenor, p.attention, cast(p.remarks as nvarchar2(2000)) as remarks, 0 as inco_term, 0 as payterm_id
			from inv_purchase_requisition_mst p, inv_purchase_requisition_dtls q, wo_non_order_info_dtls r, wo_po_break_down s
			where p.id=q.mst_id and q.id = r.requisition_dtls_id and r.job_no = s.job_no_mst and p.company_id = $data[0] and r.mst_id=$data[1]";
	    }else{ //, wo_po_break_down c  //and b.po_breakdown_id = c.id
	        $sql = "select a.id as wo_id,  a.wo_number, a.buyer_po, a.wo_basis_id, cast(b.requisition_no as nvarchar2(20)) as req_nos, cast('' as nvarchar2(25)) as booking_no, a.supplier_id as supplier_id, a.wo_date as wo_date, a.currency_id as currency_id, a.buyer_name as buyer_id, a.style, a.pay_mode, a.source, a.delivery_date,a.tenor, a.attention, a.remarks, a.inco_term, a.payterm_id as payterm_id
			from wo_non_order_info_mst a,wo_non_order_info_dtls b
			where a.id=b.mst_id  and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0 and a.company_name = $data[0]
			union all 
			select 0 as wo_id,  null as wo_number, cast(s.id as nvarchar2(200)) as buyer_po, p.basis as wo_basis_id, p.requ_no as req_nos, q.booking_no as booking_no, cast(p.supplier_id as nvarchar2(50)) as supplier_id, cast('' as date) as wo_date, cast('' as number) as currency_id, cast(q.buyer_id as nvarchar2(20)) as buyer_id, q.style_ref_no as style, p.pay_mode, p.source, p.delivery_date, 0 as tenor, p.attention, cast(p.remarks as nvarchar2(2000)) as remarks, 0 as inco_term, 0 as payterm_id
			from inv_purchase_requisition_mst p, inv_purchase_requisition_dtls q, wo_non_order_info_dtls r, wo_po_break_down s
			where p.id=q.mst_id and q.id = r.requisition_dtls_id and r.job_no = s.job_no_mst and p.company_id = $data[0] and r.mst_id=$data[1]";
		}
		//echo $sql;
	    $sql_data = sql_select($sql);

	    $req_nos = '';
	    $booking_no = '';$buyer_po="";
	    foreach ($sql_data as $row) {
			//if($row[csf("wo_basis_id")] == 1){
				$work_order_no_arr[$row[csf("wo_id")]] = $row[csf("wo_number")];
			//}else{
				//$work_order_no = $row[csf("wo_number")];
			//}
	        $item_category_id = $row[csf("item_category")];
	        $supplier_id_arr[$row[csf("wo_id")]] = $row[csf("supplier_id")];
	        $work_order_date_arr[$row[csf("wo_id")]] = $row[csf("wo_date")];
	        $currency_id_arr[$row[csf("wo_id")]] = $row[csf("currency_id")];
	        $buyer_name = $row[csf("buyer_name")];
	        $style = $row[csf("style")];
	        $wo_basis_id = $row[csf("wo_basis_id")];
	        $pay_mode_id_arr[$row[csf("wo_id")]] = $row[csf("pay_mode")];
	        $pay_term_id_arr[$row[csf("wo_id")]] = $row[csf("payterm_id")];
	        $source_arr[$row[csf("wo_id")]] = $row[csf("source")];
	        $delivery_date = $row[csf("delivery_date")];
	        $attention_arr[$row[csf("wo_id")]] = $row[csf("attention")];

	        $delivery_place = $row[csf("delivery_place")];
	        $do_no = $row[csf("do_no")];
	        $remarks = $row[csf("remarks")];
	        $insert_date = $row[csf("insert_date")];
	        $inco_term_arr[$row[csf("wo_id")]] = $row[csf("inco_term")];
	        $pi_issue_to = $row[csf("pi_issue_to")];
	        $req_nos .= $row[csf("req_nos")].",";	 
	       // $buyer_po .= $row[csf("buyer_po")].",";	
		  	if($row[csf("buyer_po")]!="")
			{	        
	       	 	$buyer_po .= $row[csf("buyer_po")].",";	
			}        
	        $req_dtls_ids = $row[csf("req_dtls_ids")];
	        $tenor_arr[$row[csf("wo_id")]] = $row[csf("tenor")];
	        $booking_no[$row[csf("buyer_po")]] = $row[csf("booking_no")];
	        //
		}
		$req_nos = chop($req_nos,",");
		$buyer_po = chop($buyer_po,",");
		//print_r($work_order_no_arr[$data[1]]);//die(" with work_order_no");
	        if($req_nos != "" || $buyer_po != "")
			{

				$req_nos= implode(",",array_unique(explode(",",$req_nos)));
				$buyer_po= implode(",",array_unique(explode(",", $buyer_po)));
				
				array_unique(explode(",",$req_nos));
				$req_noswithcomma = "'" . implode ( "', '", array_unique(explode(",",$req_nos)))  . "'";
				if($req_nos =="")
				{
					$req_no_cond = "";
				}else{
					$req_no_cond = " and c.requisition_no in ($req_noswithcomma)";
				}

				if($buyer_po =="")
				{
					$buyer_po_cond = "";
				}else{
					$buyer_po_cond = " and c.po_breakdown_id in ($buyer_po)";
				}
				
				if( $req_noswithcomma != "" || $buyer_po != "")
				{					

					$req_sql = "select a.booking_no, d.id as po_id 
					from inv_purchase_requisition_dtls a ,inv_purchase_requisition_mst b,  wo_non_order_info_dtls c,  wo_po_break_down d
					where a.mst_id = b.id and b.requ_no = c.requisition_no and c.job_no = d.job_no_mst and  b.basis=1 $req_no_cond 
					union all
					select cast('' as nvarchar2(20)) as booking_no, c.po_breakdown_id as po_id 
					from inv_purchase_requisition_dtls a ,inv_purchase_requisition_mst b,  wo_non_order_info_dtls c,  wo_po_break_down d
					where a.mst_id = b.id and b.requ_no = c.requisition_no and c.po_breakdown_id = d.id  and b.basis=3 $buyer_po_cond  and b.company_id = $data[0]"; //and b.company_id = $data[0]

					//echo $req_sql;
					$booking_data= sql_select($req_sql);
	                                $book= "";$po_booking_data_array=array();
					foreach($booking_data as $row){

						if($row[csf("booking_no")])
						{
							  $book .=$row[csf("booking_no")].',';
							  $booking_number[$row[csf("booking_no")]] = $row[csf("booking_no")];
							  $bookingTagPo[$row[csf("po_id")]]=$row[csf("po_id")];
						}
						//if($po_id_arr[$row[csf("po_id")]] != $row[csf("po_id")])
						//{
							$po_id_arr[$row[csf("po_id")]] = $row[csf("po_id")];
							$po_booking_data_array[$row[csf("po_id")]] = $row[csf("booking_no")];
						//}

					}

				}

			}
			//$booki = explode($book);
	        //$booking_no = implode(",",array_unique(explode(",",chop($book,","))));
			//print_r($po_booking_data_array);//die;
	       //echo $req_nos;
			//$tna_data_array[array_unique(explode(",",chop($book,",")))][$work_order_no];
			//print_r(array_unique(explode(",",chop($book,","))));
	        if( $booking_no == ""){
	            $booking_no = "";
	        }


	    //$pay_mode=array(1=>"Credit",2=>"Import",3=>"In House",4=>"Cash",5=>"Within Group");
	    if ($pay_mode_id_arr[$data[1]] == '1') {
	        $pay_mode_str = 'Credit';
	    } else if ($pay_mode_id_arr[$data[1]] == '2') {
	        $pay_mode_str = 'Import';
	    } else if ($pay_mode_id_arr[$data[1]] == '3') {
	        $pay_mode_str = 'In House';
	    } else if ($pay_mode_id_arr[$data[1]] == '4') {
	        $pay_mode_str = 'Cash';
	    } else if ($pay_mode_id_arr[$data[1]] == '5') {
	        $pay_mode_str = 'Within Group';
	    }
	    //$source=array(1=>"Abroad",2=>"EPZ",3=>"Non-EPZ");

	    if ($source_arr[$data[1]] == '1') {
	        $source_str = 'Abroad';
	    } else if ($source_arr[$data[1]] == '2') {
	        $source_str = 'EPZ';
	    } else if ($source_arr[$data[1]] == '3') {
	        $source_str = 'Non-EPZ';
	    }

	    //$pay_mode = return_field_value("pa", "lib_company", "id=$data[0]");
	    $sql_job = sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	    foreach ($sql_job as $row) {
	        $buyer_job_arr[$row[csf("po_id")]]["po_id"] = $row[csf("po_id")];
	        $buyer_job_arr[$row[csf("po_id")]]["po_number"] = $row[csf("po_number")];
	        $buyer_job_arr[$row[csf("po_id")]]["buyer_name"] = $row[csf("buyer_name")];
	        $buyer_job_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
	        $buyer_job_arr[$row[csf("po_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
	    }


	    $sql_supplier = sql_select("select id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 from  lib_supplier where id=".$supplier_id_arr[$data[1]]."");

	    foreach ($sql_supplier as $supplier_data) {//contact_no
	        $row_mst[csf('supplier_id')];

	        if ($supplier_data[csf('address_1')] != '')
	            $address_1 = $supplier_data[csf('address_1')] . ',' . ' ';
	        else
	            $address_1 = '';
	        if ($supplier_data[csf('address_2')] != '')
	            $address_2 = $supplier_data[csf('address_2')] . ',' . ' ';
	        else
	            $address_2 = '';
	        if ($supplier_data[csf('address_3')] != '')
	            $address_3 = $supplier_data[csf('address_3')] . ',' . ' ';
	        else
	            $address_3 = '';
	        if ($supplier_data[csf('address_4')] != '')
	            $address_4 = $supplier_data[csf('address_4')] . ',' . ' ';
	        else
	            $address_4 = '';
	        if ($supplier_data[csf('contact_no')] != '')
	            $contact_no = $supplier_data[csf('contact_no')] . ',' . ' ';
	        else
	            $contact_no = '';
	        if ($supplier_data[csf('web_site')] != '')
	            $web_site = $supplier_data[csf('web_site')] . ',' . ' ';
	        else
	            $web_site = '';
	        if ($supplier_data[csf('supplier_name')] != '')
	            $supplier_name = $supplier_data[csf('supplier_name')] . ',' . ' ';
	        else
	            $supplier_name = '';
	        if ($supplier_data[csf('email')] != '')
	            $email = $supplier_data[csf('email')] . ',' . ' ';
	        else
	            $email = '';
	        $country = $supplier_data['country_id'];
	        $supplier_name = $supplier_name;
	        $supplier_address = $address_1;
	        $supplier_country = $country;
	        $supplier_phone = $contact_no;
	        $supplier_email = $email;
	    }
	    $image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	    $varcode_booking_no = $work_order_no_arr[$data[1]];
	    ?>
			<style type="text/css">
				table > tbody > tr > td{
					word-wrap: break-word;
					word-break: break-all;
				}
			</style>
			<div style="width:960px;">
			<table width="950" cellspacing="0" align="center">
				<tr>
					<td rowspan="3" width="70">
                        <?
                        if ($data[6]=='YarnPurchaseOrderPrintButton')
                        {   
                            ?>
                             <img src="../../<? echo $image_location; ?>" height="70" width="200">
                            <?
                        } 
                        else 
                        { 
                            ?>
                             <img src="../../../<? echo $image_location; ?>" height="70" width="200">
                            <?    
                        }
                        ?>                       
                    </td>
					<td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
					<td rowspan="3" id="barcode_img_id"> </td>
				</tr>
				<tr class="form_caption">
					<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
				</tr>
			</table><br>
			<table width="950" cellspacing="0" align="center">

					<tr>
					<td align="left"><strong>To</strong>,&nbsp;<? echo $attention_arr[$data[1]] ?></td>
					<td width="115"><strong>PO Number</strong></td>
					<td width="180" align="left">: <? echo $work_order_no_arr[$data[1]]; ?></td>
					<td width="80"><strong>Incoterm</strong></td>
					<td width="150" align="left">: <? echo $incoterm[$inco_term_arr[$data[1]]]; ?></td>
					<!-- <td><strong>Pay Mode:</strong></td>
					<td align="left"><? //echo $pay_mode_str; ?></td>-->
				</tr>
				<tr>
					<td rowspan="4">
						<?
						echo $supplier_name_library[$supplier_id_arr[$data[1]]];
						echo "<br>";
						echo $supplier_address;
						echo $lib_country_arr[$country];
						echo "<br>";
						echo "<b>Mobile &nbsp;&nbsp;&nbsp;:</b>" . $supplier_phone;
						echo "<br>";
						echo "<b>Mail &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>" . $supplier_email;
						?>
					</td>
					<td align="left" ><strong>PO Date </strong></td>
					<td align="left">: <? echo change_date_format($work_order_date_arr[$data[1]]); ?></td>

					<td><strong>Currency</strong></td>
					<td align="left">: <? echo $currency[$currency_id_arr[$data[1]]]; ?></td>

				</tr>
				<tr>
					<td><strong>Pay Mode</strong></td>
					<td align="left">: <? echo $pay_mode_str; ?></td>
					<td >
						<strong>Buyer</strong>
						<!--<strong>Delivery Date </strong>-->
					</td>
					<td align="left">: <?
					if ($wo_basis_id==2) {

						//$buyer=explode(',', $buyer_name);
						$buyer=explode(',', $buyer_name);
						foreach ($buyer as $buyers) {
							$buyers_all[$buyers]=$buyer_arr[$buyers];
						}
						echo implode(',',$buyers_all);
					}
					else if($wo_basis_id==3){
						$sql_dtls_buyer = sql_select("select a.buyer_po from  wo_non_order_info_mst a, wo_non_order_info_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] and a.id=b.mst_id");
						$poID= $sql_dtls_buyer [0][csf("buyer_po")];
							$sql_po_buyer = sql_select("select a.id, b.buyer_name from  wo_po_break_down a, wo_po_details_master b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id= $poID and a.job_no_mst=b.job_no");
							$buyers_all="";
							foreach ($sql_po_buyer  as $row) {
								//$buyers_all.=$buyer_arr[$row[csf("buyer_name")]].',';
								$buyers_all[$row[csf("buyer_name")]]=$buyer_arr[$row[csf("buyer_name")]];
							}
								echo implode(',',$buyers_all);
					}
					else{

						$sql_dtls_buyer = sql_select("select b.id, b.buyer_id from  wo_non_order_info_mst a, wo_non_order_info_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] and a.id=b.mst_id");
						$buyers_all="";
						foreach ($sql_dtls_buyer  as $row) {
							//$buyers_all.=$buyer_arr[$row[csf("buyer_id")]].',';
							$buyers_all[$row[csf("buyer_id")]]=$buyer_arr[$row[csf("buyer_id")]];
						}
						echo implode(',',$buyers_all);
					}
					//echo change_date_format($delivery_date);
					?></td>
				</tr>
				<tr>
					<td align="left" ><strong>Print Date</strong></td>
					<td>
						<p> <?
						$pc_day_time = explode(" ", $pc_date_time);
						echo ": ".change_date_format($pc_day_time[0]);
						echo " " . $pc_day_time[1] . " " . $pc_day_time[2];
						?>
						</p>
					</td>
					<td align="left" ><strong>WO Basis</strong></td>
					<td align="left" >: <? echo $wo_basis[$wo_basis_id]; ?></td>
				</tr>
				<tr>
					<td ><strong>Pay Term </strong></td>
					<td>: <? echo $pay_term[$pay_term_id_arr[$data[1]]]; ?></td>


					<td align="left"><strong>Tenor</strong></td>
					<td align="left" >: <? echo $tenor_arr[$data[1]]; ?></td>
				</tr>
				<tr>
					<td align="left"><strong>Source &nbsp;:</strong> <? echo $source_str; ?></td>
				</tr>
				<tr>
					<td ><strong>Req.NO</strong></td>
					<td colspan="4">: <? echo $req_nos; ?></td>
				</tr>
				<tr>
					<td><strong>PI Issue To</strong>: <? echo $company_library[$pi_issue_to]; ?></td>
					
				</tr>
				<!-- <tr>
					<td ><strong>Fab.Booking No </strong></td>
					<td colspan="4">: <? //echo $booking_no; ?></td>
				</tr> -->


			</table>
					<br>
			<?
			if ($wo_basis_id == 3) {
				$buy_job_sty = "Buyer Job Style";
			} else {
				$buy_job_sty = "Buyer Style";
			}
			?>
			<table align="center" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
	                        <thead bgcolor="#dddddd" align="center">
	                            <th width="20">SL</th>
	                            <th width="70">Color</th>
	                            <th width="40">Count</th>
	                            <th>Item Description</th>
	                            <th width="40" >UOM</th>
	                            <th width="60">Quantity </th>
	                            <th width="40">Rate</th>
	                            <th width="80">Amount</th>
                                <th width="60">Sample Delv. Start</th>
	                            <th width="60">Bulk Delv. Start</th>
	                            <th width="60">Bulk Delv. End</th>
	                            <th width="50">Per day Delv Qty</th>
	                            <th width="50">No. of Lot</th>
	                            <th width="80">IR/Remarks</th>
	                        </thead>
	                        <tbody>
	    <?
	    $store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

	    $i = 1;
	    $buy_job_sty_val = "";
	    $carrency_id = "";
	    $mst_id = $dataArray[0][csf('id')];

	    //$sql_dtls="Select a.id, a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";


	    $sql_dtls = "Select a.po_breakdown_id, a.job_no, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id,a.delivery_end_date,a.yarn_inhouse_date,a.remarks,a.number_of_lot, a.sample_start_date from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	    //echo $sql_dtls;
	    $sql_result = sql_select($sql_dtls);
	    foreach ($sql_result as $row) 
		{
	        $feb_des = '';
	        if ($row[csf("yarn_comp_type2nd")] == 0) {
	            $feb_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
	        } else if ($row[csf("yarn_comp_type2nd")] != 0) {
	            $feb_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %,' . $composition[$row[csf("yarn_comp_type2nd")]] . ' ' . $row[csf("yarn_comp_percent2nd")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
	        }
			$job_nos[$row[csf("job_no")]] = $row[csf("job_no")];
	        $key = $row[csf("po_breakdown_id")] . $row[csf("color_name")] . $row[csf("yarn_count")] . $feb_des . $row[csf("uom")];
	        $dataArr[$key] = array(
	            po_breakdown_id => $row[csf("po_breakdown_id")],
	            color_name => $row[csf("color_name")],
	            yarn_count => $row[csf("yarn_count")],
	            uom => $row[csf("uom")],
	            sample_start_date => $row[csf("sample_start_date")],
				delivery_end_date => $row[csf("delivery_end_date")],
	            yarn_inhouse_date => $row[csf("yarn_inhouse_date")],
	            remarks => $row[csf("remarks")],
				number_of_lot => $row[csf("number_of_lot")],
	            feb_des => $feb_des
	        );
	        $qtyArr[$key] += $row[csf("supplier_order_quantity")];
	        $amuArr[$key] += $row[csf("amount")];

	        $carrency_id = $row[csf('currency_id')];
	        if ($carrency_id == 1) {
	            $paysa_sent = "Paisa";
	        } else if ($carrency_id == 2) {
	            $paysa_sent = "CENTS";
	        }
	    }

	    $sql_result = sql_select($sql_dtls);
	    foreach ($dataArr as $key => $row) {
	        if ($i % 2 == 0)
	            $bgcolor = "#E9F3FF";
	        else
	            $bgcolor = "#FFFFFF";
	        if ($wo_basis_id == 2) {
	            $buyer_name_val = "";
	            $buyer_id = explode(',', $buyer_name);
	            foreach ($buyer_id as $val) {
	                if ($buyer_name_val == "")
	                    $buyer_name_val = $buyer_arr[$val];
	                else
	                    $buyer_name_val .= ', ' . $buyer_arr[$val];
	            }

	            $buy_job_sty_val = $buyer_name_val . "<br>" . $style;
	        }
	        else if ($wo_basis_id == 3) {
	            if ($row["po_breakdown_id"] != "" && $row["po_breakdown_id"] != 0) {
	                $buyer_name_val = $buyer_arr[$buyer_job_arr[$row["po_breakdown_id"]]["buyer_name"]] . "<br>" . $buyer_job_arr[$row["po_breakdown_id"]]["job_no"] . "<br>" . $buyer_job_arr[$row["po_breakdown_id"]]["style_ref_no"] . "<br>";
	            }
	            $buy_job_sty_val = $buyer_name_val;
	        }

				$startTimeStamp = strtotime($row[yarn_inhouse_date]);
				$endTimeStamp = strtotime($row[delivery_end_date]);
				$timeDiff = abs($endTimeStamp - $startTimeStamp);
				$numberDays = $timeDiff/86400;  // 86400 seconds in one day
				$numberDays = intval($numberDays)+1;
				$tot_number_of_lot+=$row['number_of_lot'];
				

	        ?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $color_arr[$row["color_name"]]; ?></p></td>
					<td align="center"><p><? echo $count_arr[$row["yarn_count"]]; ?></p></td>
					<td align="center"><p><? echo $row[feb_des]; ?></p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
					<td align="right"><p><? echo number_format($qtyArr[$key]); ?></p></td>
					<td align="right"><p><? echo number_format($amuArr[$key] / $qtyArr[$key],2); ?></p></td>
					<td align="right"><p><? echo number_format($amuArr[$key], 2); ?></p></td>
					<td align="right"><p><? echo change_date_format($row[sample_start_date]); ?></p></td>
					<td align="right"><p><? echo change_date_format($row[yarn_inhouse_date]); ?></p></td>
					<td align="right"><p><? echo change_date_format($row[delivery_end_date]); ?></p></td>
					<td align="right">
						<p><?
						$parDayTot+=($qtyArr[$key]/$numberDays);
						echo number_format($qtyArr[$key]/$numberDays);
						?></p>
					</td>
					<td align="right"><? echo $row['number_of_lot']; ?></td>
					<td><p><? echo $row['remarks']; ?></p></td>
				</tr>
	<?
	$i++;
	    }
	    ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5" align="right">Total </th>
					<th align="right"><? echo number_format(array_sum($qtyArr)); ?></th>
					<th>&nbsp;</th>
					<th align="right"><? echo $word_amount = number_format(array_sum($amuArr), 2); ?></th>
					<th colspan="3"></th>
					<th align="right"><? echo number_format($parDayTot); ?></th>
					<th align="right"><? echo $tot_number_of_lot; ?></th>
					<th></th>
				</tr>
					<tr>
					<th colspan="14" align="left">
						In words: <span style="font-weight:normal !important;">
						<?
						$amount_without_comma = number_format(array_sum($amuArr),2,'.','');
						$inWordReturn =number_to_words($amount_without_comma, $currency[$carrency_id], $paysa_sent);
						$inWordArray = explode($currency[$carrency_id],$inWordReturn);
						echo $currency[$carrency_id]." ".$inWordArray[0]." ".$inWordArray[1];
						?>
						</span></th>
					</tr>
			</tfoot>
		</table>

		<br>



		<?
		$work_order_no=$work_order_no_arr[$data[1]];
		echo get_spacial_instruction($work_order_no,"900px",144);

		$user_sql = "select a.id,a.user_full_name,b.custom_designation from user_passwd a,lib_designation b where a.valid=1 and b.id=a.designation";
		$user_data_array=sql_select($user_sql);
		foreach($user_data_array as $row){
			$user_arr[$row[csf(id)]]=$row[csf(user_full_name)].'<br><span style="font-size:12px;">('.$row[csf(custom_designation)].')</span>';
		}


        if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;die;
        $approval_status=sql_select($approval_status);
		$sql="select updated_by,inserted_by,company_name,is_approved  FROM wo_non_order_info_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_name')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}

	   //$last_authority = return_field_value("user_id", "electronic_approval_setup", " page_id=412 and entry_form=2 and company_id=$company_name order by sequence_no desc");
	//echo $approval_status[0][csf('approval_need')]."eeee" ;die;
		$sql="select approved_by  FROM approval_history where mst_id=$data[1] and entry_form=2 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf(approved_by)];
		}

        if($approval_status[0][csf('approval_need')] == 1){
            if($is_approved==1){ echo '<style > body{ background-image: url("../../../img/approved.gif"); } </style>';}
            else{ echo '<style > body{ background-image: url("../../../img/draft.gif"); } </style>';}
        }
		
	?>

		<br>
		<br>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="text-align:center;" rules="all" width="900">
			<thead>
				<tr>
					<th colspan="8" align="center">TNA Information</th>
				</tr>
				<tr>
					<th width="130" align="center" rowspan="2">Fabric Booking No</th>
					<th width="80" align="center" rowspan="2">FB Date</th>
					<th width="100" align="center" rowspan="2">FB Last Update Date</th>
					<th align="center" rowspan="2">Order No</th>
					<th align="center" colspan="2">Yarn Allocation</th>
					<th align="center" colspan="2">Knitting</th>
				</tr>
				<tr>					
					<th width="80">Plan Start</th>
					<th width="80">Plan End</th>
					<th width="80">Plan Start</th>
					<th width="80">Plan End</th>
				</tr>
			</thead>
			<tbody>
				<?
				$jobs_nombers = implode("','",$job_nos);
				$sql_tna_info = "select a.id, a.task_number, a.task_start_date, a.task_finish_date, a.actual_start_date, a.actual_finish_date, a.process_date, a.po_number_id 
				from tna_process_mst a	where a.po_number_id in(".implode(",", $po_id_arr).") and a.task_number in(48,60) and  a.is_deleted=0 and a.status_active=1 and a.TASK_TYPE=1";
				//echo $sql_tna_info;
				$tna_info_result = sql_select($sql_tna_info);
				foreach ($tna_info_result as  $value) {
					$tna_info_arr[$value[csf("po_number_id")]][$value[csf("task_number")]]["task_start_date"] = $value[csf("task_start_date")];
					$tna_info_arr[$value[csf("po_number_id")]][$value[csf("task_number")]]["task_finish_date"] = $value[csf("task_finish_date")];
				}

				//echo implode(",", $po_id_arr);
				$sql_booking_po = "select b.id, b.po_number, c.booking_no ,d.BOOKING_DATE,d.UPDATE_DATE
				from  wo_po_break_down b, wo_booking_dtls c,WO_BOOKING_MST d
				where c.booking_no=d.booking_no and b.id = c.po_break_down_id and c.booking_no in('".implode("','",$booking_number)."') and b.id in(".implode(",", $bookingTagPo).") and
				 b.is_deleted=0 and c.booking_type = 1 and c.is_deleted = 0 order by c.booking_no desc";
				//echo $sql_booking_po;
				$booking_po_result = sql_select($sql_booking_po);

				foreach ($booking_po_result as $order_data) {
					//$po_data_array[$order_data[csf("booking_no")]][$order_data[csf("id")]] = $order_data[csf("po_number")];
					$po_data_array[$order_data[csf("booking_no")]][$order_data[csf("id")]] = $order_data[csf("po_number")];
					$booking_date_array[$order_data[csf("booking_no")]] = $order_data[BOOKING_DATE];
				}
				 
				
				
				if($db_type==0)
				{
					$update_date_colum=",max(date(c.UPDATE_DATE)) as UPDATE_DATE";
				}
				else
				{
					$update_date_colum=",max(to_char(c.UPDATE_DATE,'DD-MM-YYYY')) as UPDATE_DATE";
				}				
				
				$sql_booking_po2 = "select c.booking_no $update_date_colum
				from  wo_booking_dtls c
				where c.booking_no in('".implode("','",$booking_number)."') and c.booking_type = 1 and c.is_deleted = 0 group by c.booking_no";
				//echo $sql_booking_po;
				$booking_po_result2 = sql_select($sql_booking_po2);

				foreach ($booking_po_result2 as $rows) {
					$booking_update_date_array[$rows[csf("booking_no")]] = $rows[UPDATE_DATE];
				}
				
				
				
				foreach ($po_data_array as $booking_no => $booking_data) {
					$k=1;
					foreach ($booking_data as $po_id => $po_number) {
						?>
						<tr>
							<? if($k ==1){ ?>
							<td rowspan="<? echo count($booking_data) ?>"><?= $booking_no;?></td>
							<td rowspan="<? echo count($booking_data) ?>"><?= change_date_format($booking_date_array[$booking_no]);?></td>
							<td rowspan="<? echo count($booking_data) ?>"><?=  $booking_update_date_array[$booking_no];?></td>
							<? } ?>
							<td align="left"><?= $po_number;?></td>
							<td><?= change_date_format($tna_info_arr[$po_id][48]["task_start_date"]);?></td>
							<td><?= change_date_format($tna_info_arr[$po_id][48]["task_finish_date"]);?></td>
							<td><?= change_date_format($tna_info_arr[$po_id][60]["task_start_date"]);?></td>
							<td><?= change_date_format($tna_info_arr[$po_id][60]["task_finish_date"]);?></td>
						</tr>
						<?
						$k++;
					}
				}
				?>
			</tbody>
		</table>
		<br><br><br><br>
	    <table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="text-align:center;" rules="all" width="880">
			<tr>
	        	<td style="border:none; line-height:13px;" align="left"><? echo $user_arr[$PreparedBy]; ?></td>
	            <td style="border:none; line-height:13px;" align="right"><? echo $user_arr[$last_approved_by]; ?></td>
	        </tr>
			<tr style="alignment-baseline:baseline;">
	        	<td width="" style="text-decoration:overline; border:none" align="left">Prepared By</td>
	            <td width="" style="text-decoration:overline; border:none" align="right">Authorized By</td>
	        </tr>
	    </table>
	    <?
	    		//echo signature_table(42, $data[0], "900px");
	    ?>
	     </div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
	        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	        <script>
	        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	        </script>
	    <?
	    exit();
		}
	else{}
}

if ($action == "print_to_html_report3") {
    extract($_REQUEST);
    $data = explode('*', $data);
    
    if ($data[6]=='YarnPurchaseOrderPrintButton'){
        echo load_html_head_contents($data[2], "../../../", 1, 1, $unicode, '', '');
    } else {
        echo load_html_head_contents($data[2], "../../", 1, 1, $unicode, '', '');
    }
    //print_r ($data);
	$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	//if(($data[4]==1 || $data[4]==0) && $user_level_library[$user_id]==2)
	if(($data[4]==1 && $user_level_library[$user_id]==2) || ($data[4]==0))
	{
	    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	    $location = return_field_value("city", "lib_company", "id=$data[0]");
	    $address = return_field_value("address", "lib_location", "id=$data[0]");
	    $lib_country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");

	    $item_name_arr = return_library_array("select id,item_name from lib_item_group", "id", "item_name");
	    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier', 'id', 'supplier_name');
	    $lib_terms_condition = return_library_array("select id, terms from lib_terms_condition", 'id', 'terms');
	    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	    if($db_type == 0){
	        $sql = "SELECT  a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.wo_type, a.currency_id,a.tenor,
	        group_concat(b.requisition_no) as req_nos,
	        group_concat(b.requisition_dtls_id) as req_dtls_ids,
	        a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term ,a.buyer_name
	        FROM wo_non_order_info_mst a,wo_non_order_info_dtls b
	        WHERE a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0
	        group by a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,
	        a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.wo_type, a.currency_id,a.tenor,a.buyer_name ";
	    }else{
	        $sql = "SELECT  a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.wo_type, a.currency_id,a.tenor,
	        LISTAGG (cast(b.requisition_no as varchar(4000)),',') within group ( order by b.requisition_no ) as req_nos,
	        LISTAGG (cast(b.requisition_dtls_id as varchar(4000)),',') within group ( order by b.requisition_dtls_id ) as req_dtls_ids,
	        a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,a.buyer_name
	        FROM wo_non_order_info_mst a,wo_non_order_info_dtls b
	        WHERE a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0
	        group by a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,
	        a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.wo_type, a.currency_id,a.tenor,a.buyer_name  ";
	    }
	    $sql_data = sql_select($sql);

	    $req_nos = '';
	    $booking_no = '';
	    foreach ($sql_data as $row) {
	        $work_order_no = $row[csf("wo_number")];
	        $item_category_id = $row[csf("item_category")];
	        $supplier_id = $row[csf("supplier_id")];
	        $work_order_date = $row[csf("wo_date")];
	        $currency_id = $row[csf("currency_id")];
	        $buyer_name = $row[csf("buyer_name")];
	        $style = $row[csf("style")];
	        $wo_basis_id = $row[csf("wo_basis_id")];
	        $pay_mode_id = $row[csf("pay_mode")];
	        $pay_term_id = $row[csf("payterm_id")];
	        $source = $row[csf("source")];
	        $delivery_date = $row[csf("delivery_date")];
	        $attention = $row[csf("attention")];

	        $delivery_place = $row[csf("delivery_place")];
	        $do_no = $row[csf("do_no")];
	        $remarks = $row[csf("remarks")];
	        $insert_date = $row[csf("insert_date")];
	        $inco_term = $row[csf("inco_term")];
	        $pi_issue_to = $row[csf("pi_issue_to")];
	        $req_nos = $row[csf("req_nos")];
	        $req_dtls_ids = $row[csf("req_dtls_ids")];
	        $tenor = $row[csf("tenor")];
	        $buyer_name = $row[csf("buyer_name")];
            $wo_type = $row[csf("wo_type")];
	    }
	        if($req_nos != "")
			{

				$req_nos= implode(",",array_unique(explode(",",$req_nos)));

				array_unique(explode(",",$req_nos));

				$req_noswithcomma = "'" . implode ( "', '", array_unique(explode(",",$req_nos)))  . "'";
				if( $req_noswithcomma != "")
				{
					 $req_sql = "select a.booking_no
					from inv_purchase_requisition_dtls a ,inv_purchase_requisition_mst b,  wo_non_order_info_dtls c
					where a.mst_id = b.id and b.requ_no = c.requisition_no and
					c.requisition_no in ($req_noswithcomma) group by a.booking_no";
					$booking_data= sql_select($req_sql);
	                                $book= "";
					foreach($booking_data as $row){

						if($row[csf("booking_no")])
						{
							  $book .=$row[csf("booking_no")].',';
						}

					}

				}

	        }

	        $booking_no = implode(",",array_unique(explode(",",chop($book,","))));
	       //echo $booking_no;

	        if( $booking_no == ""){
	            $booking_no = "";
	        }


	    //$pay_mode=array(1=>"Credit",2=>"Import",3=>"In House",4=>"Cash",5=>"Within Group");
	    if ($pay_mode_id == '1') {
	        $pay_mode_str = 'Credit';
	    } else if ($pay_mode_id == '2') {
	        $pay_mode_str = 'Import';
	    } else if ($pay_mode_id == '3') {
	        $pay_mode_str = 'In House';
	    } else if ($pay_mode_id == '4') {
	        $pay_mode_str = 'Cash';
	    } else if ($pay_mode_id == '5') {
	        $pay_mode_str = 'Within Group';
	    }
	    //$source=array(1=>"Abroad",2=>"EPZ",3=>"Non-EPZ");

	    if ($source == '1') {
	        $source_str = 'Abroad';
	    } else if ($source == '2') {
	        $source_str = 'EPZ';
	    } else if ($source == '3') {
	        $source_str = 'Non-EPZ';
	    }

	    //$pay_mode = return_field_value("pa", "lib_company", "id=$data[0]");
	    $sql_job = sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	    foreach ($sql_job as $row) {
	        $buyer_job_arr[$row[csf("po_id")]]["po_id"] = $row[csf("po_id")];
	        $buyer_job_arr[$row[csf("po_id")]]["po_number"] = $row[csf("po_number")];
	        $buyer_job_arr[$row[csf("po_id")]]["buyer_name"] = $row[csf("buyer_name")];
	        $buyer_job_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
	        $buyer_job_arr[$row[csf("po_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
	    }


	    $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

	    foreach ($sql_supplier as $supplier_data) {//contact_no
	        $row_mst[csf('supplier_id')];

	        if ($supplier_data[csf('address_1')] != '')
	            $address_1 = $supplier_data[csf('address_1')] . ',' . ' ';
	        else
	            $address_1 = '';
	        if ($supplier_data[csf('address_2')] != '')
	            $address_2 = $supplier_data[csf('address_2')] . ',' . ' ';
	        else
	            $address_2 = '';
	        if ($supplier_data[csf('address_3')] != '')
	            $address_3 = $supplier_data[csf('address_3')] . ',' . ' ';
	        else
	            $address_3 = '';
	        if ($supplier_data[csf('address_4')] != '')
	            $address_4 = $supplier_data[csf('address_4')] . ',' . ' ';
	        else
	            $address_4 = '';
	        if ($supplier_data[csf('contact_no')] != '')
	            $contact_no = $supplier_data[csf('contact_no')] . ',' . ' ';
	        else
	            $contact_no = '';
	        if ($supplier_data[csf('web_site')] != '')
	            $web_site = $supplier_data[csf('web_site')] . ',' . ' ';
	        else
	            $web_site = '';
	        if ($supplier_data[csf('supplier_name')] != '')
	            $supplier_name = $supplier_data[csf('supplier_name')] . ',' . ' ';
	        else
	            $supplier_name = '';
	        if ($supplier_data[csf('email')] != '')
	            $email = $supplier_data[csf('email')] . ',' . ' ';
	        else
	            $email = '';
	        $country = $supplier_data['country_id'];
	        $supplier_name = $supplier_name;
	        $supplier_address = $address_1;
	        $supplier_country = $country;
	        $supplier_phone = $contact_no;
	        $supplier_email = $email;
	    }
	    $image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	    $varcode_booking_no = $work_order_no;
	    ?>


	                    <div style="width:960px;">
	                    <table width="950" cellspacing="0" align="center">
	                        <tr>
	                        	<td rowspan="3" width="70">
                                    <?
                                    if ($data[6]=='YarnPurchaseOrderPrintButton')
                                    {   
                                        ?>
                                        <img src="../../<? echo $image_location; ?>" height="70" width="200">
                                        <?
                                    } 
                                    else 
                                    { 
                                        ?>
                                        <img src="../../../<? echo $image_location; ?>" height="70" width="200">
                                        <?    
                                    }
                                    ?>                                    
                                </td>
	                            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	                            <td rowspan="3" id="barcode_img_id"> </td>
	                        </tr>
	                        <tr class="form_caption">
	                        	<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>
	                        </tr>
	                        <tr>
	                            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
	                        </tr>
	                    </table><br>
	                    <table width="950" cellspacing="0" align="center">

	                         <tr>
	                            <td align="left"><strong>To</strong>,&nbsp;<? echo $attention ?></td>
	                            <td width="115"><strong>PO Number</strong></td>
	                            <td width="180" align="left">: <? echo $work_order_no; ?></td>
	                            <td width="80"><strong>Incoterm</strong></td>
	                            <td width="150" align="left">: <? echo $incoterm[$inco_term]; ?></td>
	<!--                            <td><strong>Pay Mode:</strong></td>
	                            <td align="left"><? echo $pay_mode_str; ?></td>-->
	                        </tr>
	                        <tr>
	                           <td rowspan="4">
	                               <?
	                                echo $supplier_name_library[$supplier_id];
	                                echo "<br>";
	                                echo $supplier_address;
	                                echo $lib_country_arr[$country];
	                                echo "<br>";
	                                echo "<b>Mobile &nbsp;&nbsp;&nbsp;:</b>" . $supplier_phone;
	                                echo "<br>";
	                                echo "<b>Mail &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>" . $supplier_email;
	                                ?>
	                           </td>
	                            <td align="left" ><strong>PO Date </strong></td>
	                            <td align="left">: <? echo change_date_format($work_order_date); ?></td>

	                            <td><strong>Currency</strong></td>
	                            <td align="left">: <? echo $currency[$currency_id]; ?></td>

	                        </tr>
	                        <tr>
	                            <td><strong>Pay Mode</strong></td>
	                            <td align="left">: <? echo $pay_mode_str; ?></td>
	                            <td >
	                            	<!-- <strong>Buyer</strong> -->
	                            	<!--<strong>Delivery Date </strong>-->
	                            </td>
	                            <td align="left"> <?
									/*if ($wo_basis_id==2) {

										$buyer=explode(',', $buyer_name);
										foreach ($buyer as $buyers) {
											$buyers_all.=$buyer_arr[$buyers].',';
										}
										echo chop($buyers_all,',');
									}
									else if($wo_basis_id==3){
										$sql_dtls_buyer = sql_select("select a.buyer_po from  wo_non_order_info_mst a, wo_non_order_info_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] and a.id=b.mst_id");
										$poID= $sql_dtls_buyer [0][csf("buyer_po")];
										$sql_po_buyer = sql_select("select a.id, b.buyer_name from  wo_po_break_down a, wo_po_details_master b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id= $poID and a.job_no_mst=b.job_no");
										$buyers_all="";
										foreach ($sql_po_buyer  as $row) {
										$buyers_all.=$buyer_arr[$row[csf("buyer_name")]].',';
										}
										echo chop($buyers_all,',');
									}
									else{

										$sql_dtls_buyer = sql_select("select b.id, b.buyer_id from  wo_non_order_info_mst a, wo_non_order_info_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] and a.id=b.mst_id");
										$buyers_all="";
										foreach ($sql_dtls_buyer  as $row) {
										$buyers_all.=$buyer_arr[$row[csf("buyer_id")]].',';
										}
										echo chop($buyers_all,',');
								}
	                            //echo change_date_format($delivery_date);*/
	                            ?></td>
	                        </tr>
	                        <tr>
	                            <td align="left" ><strong>Print Date</strong></td>
	                            <td>
	                                <p> <?
	                                $pc_day_time = explode(" ", $pc_date_time);
	                                echo ": ".change_date_format($pc_day_time[0]);
	                                echo " " . $pc_day_time[1] . " " . $pc_day_time[2];
	                                ?>
	                                </p>
	                            </td>
	                            <td align="left" ><strong>WO Basis</strong></td>
	                            <td align="left" >: <? echo $wo_basis[$wo_basis_id]; ?></td>
	                        </tr>
	                        <tr>
	                            <td ><strong>Pay Term </strong></td>
	                            <td>: <? echo $pay_term[$pay_term_id]; ?></td>


	                            <td align="left"><strong>Tenor</strong></td>
	                            <td align="left" >: <? echo $tenor; ?></td>
	                        </tr>
	                        <tr>
	                            <td align="left"><strong>Source &nbsp;&nbsp;&nbsp;:</strong> <? echo $source_str; ?></td>
	                            <td ><strong>Req.NO</strong></td>
	                            <td colspan="3">: <? echo $req_nos; ?></td>
	                        </tr>
	                        <tr>
	                            <td><strong>PI Issue To</strong>: <? echo $company_library[$pi_issue_to]; ?></td>
	                            <td ><strong>Fab.Booking No </strong></td>
	                            <td>: <? echo $booking_no; ?></td>
                                <td><strong>Wo Type</strong></td>
                                <td align="left">: <? echo $wo_type_array[$wo_type]; ?></td>
	                        </tr>


	                    </table>
	                         <br>
						<?
                        if ($wo_basis_id == 3) {
                            $buy_job_sty = "Buyer Job Style";
                        } else {
                            $buy_job_sty = "Buyer Style";
                        }
                        ?>
	                    <table align="center" cellspacing="0" width="1090"  border="1" rules="all" class="rpt_table" >
	                        <thead bgcolor="#dddddd" align="center">
	                            <th width="20">SL</th>
	                            <th width="70">Buyer</th>
	                            <th width="70">Style Ref.</th>
	                            <th width="70">Color</th>
	                            <th width="40">Count</th>
	                            <th>Item Description</th>
	                            <th width="40" >UOM</th>
	                            <th width="60">Quantity </th>
	                            <th width="40">Rate</th>
	                            <th width="80">Amount</th>
                                <th width="60">Sample Delv. Start</th>
	                            <th width="60">Bulk Delv. Start</th>
	                            <th width="60">Bulk Delv. End</th>
	                            <!-- <th width="50">Per day Delv Qty</th> -->
	                            <th width="50">No. of Lot</th>
	                            <th width="80">IR/Remarks</th>
	                        </thead>
	                        <tbody>
	    <?
	    $store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

	    $i = 1;
	    $buy_job_sty_val = "";
	    $carrency_id = "";
	    $mst_id = $dataArray[0][csf('id')];

	    //$sql_dtls="Select a.id, a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";


	    $sql_dtls = "Select a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id,a.delivery_end_date,a.yarn_inhouse_date,a.remarks,a.number_of_lot, a.sample_start_date, a.buyer_id, a.style_no  from wo_non_order_info_dtls a, wo_non_order_info_mst b where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	    //echo $sql_dtls;die();
	    $sql_result = sql_select($sql_dtls);
	    foreach ($sql_result as $row) 
		{
	        $feb_des = '';
	        if ($row[csf("yarn_comp_type2nd")] == 0) {
	            $feb_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
	        } else if ($row[csf("yarn_comp_type2nd")] != 0) {
	            $feb_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %,' . $composition[$row[csf("yarn_comp_type2nd")]] . ' ' . $row[csf("yarn_comp_percent2nd")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
	        }

	        $key = $row[csf("po_breakdown_id")] . $row[csf("color_name")] . $row[csf("yarn_count")] . $feb_des . $row[csf("uom")];
	        $dataArr[$key] = array(
	            po_breakdown_id => $row[csf("po_breakdown_id")],
	            color_name => $row[csf("color_name")],
	            buyer => $row[csf("buyer_id")],
	            style_ref => $row[csf("style_no")],
	            yarn_count => $row[csf("yarn_count")],
	            uom => $row[csf("uom")],
	            sample_start_date => $row[csf("sample_start_date")],
				delivery_end_date => $row[csf("delivery_end_date")],
	            yarn_inhouse_date => $row[csf("yarn_inhouse_date")],
	            remarks => $row[csf("remarks")],
				number_of_lot => $row[csf("number_of_lot")],
	            feb_des => $feb_des
	        );
	        $qtyArr[$key] += $row[csf("supplier_order_quantity")];
	        $amuArr[$key] += $row[csf("amount")];

	        $carrency_id = $row[csf('currency_id')];
	        if ($carrency_id == 1) {
	            $paysa_sent = "Paisa";
	        } else if ($carrency_id == 2) {
	            $paysa_sent = "CENTS";
	        }
	    }

	    $sql_result = sql_select($sql_dtls);
	    foreach ($dataArr as $key => $row) {
	        if ($i % 2 == 0)
	            $bgcolor = "#E9F3FF";
	        else
	            $bgcolor = "#FFFFFF";
	        if ($wo_basis_id == 2) {
	            $buyer_name_val = "";
	            $buyer_id = explode(',', $buyer_name);
	            foreach ($buyer_id as $val) {
	                if ($buyer_name_val == "")
	                    $buyer_name_val = $buyer_arr[$val];
	                else
	                    $buyer_name_val .= ', ' . $buyer_arr[$val];
	            }

	            $buy_job_sty_val = $buyer_name_val . "<br>" . $style;
	        }
	        else if ($wo_basis_id == 3) {
	            if ($row["po_breakdown_id"] != "" && $row["po_breakdown_id"] != 0) {
	                $buyer_name_val = $buyer_arr[$buyer_job_arr[$row["po_breakdown_id"]]["buyer_name"]] . "<br>" . $buyer_job_arr[$row["po_breakdown_id"]]["job_no"] . "<br>" . $buyer_job_arr[$row["po_breakdown_id"]]["style_ref_no"] . "<br>";
	            }
	            $buy_job_sty_val = $buyer_name_val;
	        }

				$startTimeStamp = strtotime($row[yarn_inhouse_date]);
				$endTimeStamp = strtotime($row[delivery_end_date]);
				$timeDiff = abs($endTimeStamp - $startTimeStamp);
				$numberDays = $timeDiff/86400;  // 86400 seconds in one day
				$numberDays = intval($numberDays)+1;
				$tot_number_of_lot+=$row['number_of_lot'];

	        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>">
	                            <td align="center"><? echo $i; ?></td>
	                            <td align="center"><p><? echo $buyer_arr[$row["buyer"]]; ?></p></td>
	                            <td align="center"><p><? echo $row["style_ref"]; ?></p></td>
	                            <td align="center"><p><? echo $color_arr[$row["color_name"]]; ?></p></td>
	                            <td align="center"><p><? echo $count_arr[$row["yarn_count"]]; ?></p></td>
	                            <td align="center"><p><? echo $row[feb_des]; ?></p></td>
	                            <td align="center"><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
	                            <td align="right"><p><? echo number_format($qtyArr[$key], 2); ?></p></td>
	                            <td align="right"><p><? echo number_format($amuArr[$key] / $qtyArr[$key],2); ?></p></td>
	                            <td align="right"><p><? echo number_format($amuArr[$key], 2); ?></p></td>
	                            <td align="right"><p><? echo change_date_format($row[sample_start_date]); ?></p></td>
                                <td align="right"><p><? echo change_date_format($row[yarn_inhouse_date]); ?></p></td>
	                            <td align="right"><p><? echo change_date_format($row[delivery_end_date]); ?></p></td>
	                            <!-- <td align="right">
	                                <p><?
	                                $parDayTot+=($qtyArr[$key]/$numberDays);
									echo number_format($qtyArr[$key]/$numberDays);
	                                ?></p>
	                            </td> -->
	                            <td align="right"><? echo $row['number_of_lot']; ?></td>
	                            <td><p><? echo $row['remarks']; ?></p></td>
	                        </tr>
	<?
	$i++;
	    }
	    ?>
	                        </tbody>
	                        <tfoot>
                            	<tr>
                                	<th colspan="7" align="right">Total </th>
	                                <th align="right"><? echo number_format(array_sum($qtyArr), 2); ?></th>
	                                <th>&nbsp;</th>
	                                <th align="right"><? echo $word_amount = number_format(array_sum($amuArr), 2); ?></th>
	                                <th colspan="3"></th>
	                                <!-- <th align="right"><? echo number_format($parDayTot); ?></th> -->
	                                <th align="right"><? echo $tot_number_of_lot; ?></th>
	                                <th></th>
                                </tr>
	                             <tr>
	                                <th colspan="14" align="left">
	                                    In words: <span style="font-weight:normal !important;">
	                                    <?
	                                    $amount_without_comma = number_format(array_sum($amuArr),2,'.','');
	                                    $inWordReturn =number_to_words($amount_without_comma, $currency[$carrency_id], $paysa_sent);
	                                    $inWordArray = explode($currency[$carrency_id],$inWordReturn);
	                                    echo $currency[$carrency_id]." ".$inWordArray[0]." ".$inWordArray[1];
	                                    ?>
	                                    </span></th>
	                              </tr>
	                        </tfoot>
	                    </table>

	                    <br>



		<?
		echo get_spacial_instruction($work_order_no,"1090px",144);

		$user_sql = "select a.id,a.user_full_name,b.custom_designation from user_passwd a,lib_designation b where a.valid=1 and b.id=a.designation";
		$user_data_array=sql_select($user_sql);
		foreach($user_data_array as $row){
			$user_arr[$row[csf(id)]]=$row[csf(user_full_name)].'<br><span style="font-size:12px;">('.$row[csf(custom_designation)].')</span>';
		}


        if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;die;
        $approval_status=sql_select($approval_status);
		$sql="select updated_by,inserted_by,company_name,is_approved  FROM wo_non_order_info_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_name')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}

	   //$last_authority = return_field_value("user_id", "electronic_approval_setup", " page_id=412 and entry_form=2 and company_id=$company_name order by sequence_no desc");
	//echo $approval_status[0][csf('approval_need')]."eeee" ;die;
		$sql="select approved_by  FROM approval_history where mst_id=$data[1] and entry_form=2 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf(approved_by)];
		}

        if($approval_status[0][csf('approval_need')] == 1){
            if($is_approved==1){ echo '<style > body{ background-image: url("../../../img/approved.gif"); } </style>';}
            else{ echo '<style > body{ background-image: url("../../../img/draft.gif"); } </style>';}
        }

	?>


	    <br><br><br><br>
	    <table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="text-align:center;" rules="all" width="1000" align="center">
			<tr>
	        	<td style="border:none; line-height:13px;" align="left"><? echo $user_arr[$PreparedBy]; ?></td>
	            <td style="border:none; line-height:13px;" align="right"><? echo $user_arr[$last_approved_by]; ?></td>
	        </tr>
			<tr style="alignment-baseline:baseline;">
	        	<td width="" style="text-decoration:overline; border:none" align="left">Prepared By</td>
	            <td width="" style="text-decoration:overline; border:none" align="right">Authorized By</td>
	        </tr>
	    </table>


	    <?
	    		//echo signature_table(42, $data[0], "900px");
	    ?>
	     </div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
	        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	        <script>
	        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	        </script>
	    <?
	    exit();
		}
	else{}
}


if ($action == "print_to_html_report4") {
    extract($_REQUEST);
    $data = explode('*', $data);

    if ($data[6]=='YarnPurchaseOrderPrintButton'){
        echo load_html_head_contents($data[2], "../../../", 1, 1, $unicode, '', '');
    } else {
        echo load_html_head_contents($data[2], "../../", 1, 1, $unicode, '', '');
    }
    
    //print_r ($data);
	$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	//if(($data[4]==1 || $data[4]==0) && $user_level_library[$user_id]==2)
	if(($data[4]==1 && $user_level_library[$user_id]==2) || ($data[4]==0))
	{
	    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	    $location = return_field_value("city", "lib_company", "id=$data[0]");
	    $address = return_field_value("address", "lib_location", "id=$data[0]");
	    $lib_country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");

	    $item_name_arr = return_library_array("select id,item_name from lib_item_group", "id", "item_name");
	    $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier', 'id', 'supplier_name');
	    $lib_terms_condition = return_library_array("select id, terms from lib_terms_condition", 'id', 'terms');
	    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	    if($db_type == 0){
	        $sql = "SELECT  a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id,a.tenor,
	        group_concat(b.requisition_no) as req_nos,
	        group_concat(b.requisition_dtls_id) as req_dtls_ids,
	        a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term ,a.buyer_name
	        FROM wo_non_order_info_mst a,wo_non_order_info_dtls b
	        WHERE a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0
	        group by a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,
	        a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id,a.tenor,a.buyer_name ";
	    }else{
	        $sql = "SELECT  a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id,a.tenor,
	        LISTAGG (cast(b.requisition_no as varchar(4000)),',') within group ( order by b.requisition_no ) as req_nos,
	        LISTAGG (cast(b.requisition_dtls_id as varchar(4000)),',') within group ( order by b.requisition_dtls_id ) as req_dtls_ids,
	        a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,a.buyer_name
	        FROM wo_non_order_info_mst a,wo_non_order_info_dtls b
	        WHERE a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0
	        group by a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id,
	        a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,
	        a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id,a.tenor,a.buyer_name  ";
	    }
	    $sql_data = sql_select($sql);

	    $req_nos = '';
	    $booking_no = '';
	    foreach ($sql_data as $row) {
	        $work_order_no = $row[csf("wo_number")];
	        $item_category_id = $row[csf("item_category")];
	        $supplier_id = $row[csf("supplier_id")];
	        $work_order_date = $row[csf("wo_date")];
	        $currency_id = $row[csf("currency_id")];
	        $buyer_name = $row[csf("buyer_name")];
	        $style = $row[csf("style")];
	        $wo_basis_id = $row[csf("wo_basis_id")];
	        $pay_mode_id = $row[csf("pay_mode")];
	        $pay_term_id = $row[csf("payterm_id")];
	        $source = $row[csf("source")];
	        $delivery_date = $row[csf("delivery_date")];
	        $attention = $row[csf("attention")];

	        $delivery_place = $row[csf("delivery_place")];
	        $do_no = $row[csf("do_no")];
	        $remarks = $row[csf("remarks")];
	        $insert_date = $row[csf("insert_date")];
	        $inco_term = $row[csf("inco_term")];
	        $pi_issue_to = $row[csf("pi_issue_to")];
	        $req_nos = $row[csf("req_nos")];
	        $req_dtls_ids = $row[csf("req_dtls_ids")];
	        $tenor = $row[csf("tenor")];
	        $buyer_name = $row[csf("buyer_name")];
	        //
	    }
	        if($req_nos != "")
			{

				$req_nos= implode(",",array_unique(explode(",",$req_nos)));

				array_unique(explode(",",$req_nos));

				$req_noswithcomma = "'" . implode ( "', '", array_unique(explode(",",$req_nos)))  . "'";
				if( $req_noswithcomma != "")
				{
					 $req_sql = "select a.booking_no
					from inv_purchase_requisition_dtls a ,inv_purchase_requisition_mst b,  wo_non_order_info_dtls c
					where a.mst_id = b.id and b.requ_no = c.requisition_no and
					c.requisition_no in ($req_noswithcomma) group by a.booking_no";
					$booking_data= sql_select($req_sql);
	                                $book= "";
					foreach($booking_data as $row){

						if($row[csf("booking_no")])
						{
							  $book .=$row[csf("booking_no")].',';
						}

					}

				}

	        }

	        $booking_no = implode(",",array_unique(explode(",",chop($book,","))));
	       //echo $booking_no;

	        if( $booking_no == ""){
	            $booking_no = "";
	        }
	    if ($pay_mode_id == '1') {
	        $pay_mode_str = 'Credit';
	    } else if ($pay_mode_id == '2') {
	        $pay_mode_str = 'Import';
	    } else if ($pay_mode_id == '3') {
	        $pay_mode_str = 'In House';
	    } else if ($pay_mode_id == '4') {
	        $pay_mode_str = 'Cash';
	    } else if ($pay_mode_id == '5') {
	        $pay_mode_str = 'Within Group';
	    }
	    //$source=array(1=>"Abroad",2=>"EPZ",3=>"Non-EPZ");

	    if ($source == '1') {
	        $source_str = 'Abroad';
	    } else if ($source == '2') {
	        $source_str = 'EPZ';
	    } else if ($source == '3') {
	        $source_str = 'Non-EPZ';
	    }

	    //$pay_mode = return_field_value("pa", "lib_company", "id=$data[0]");
	    $sql_job = sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	    foreach ($sql_job as $row) {
	        $buyer_job_arr[$row[csf("po_id")]]["po_id"] = $row[csf("po_id")];
	        $buyer_job_arr[$row[csf("po_id")]]["po_number"] = $row[csf("po_number")];
	        $buyer_job_arr[$row[csf("po_id")]]["buyer_name"] = $row[csf("buyer_name")];
	        $buyer_job_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
	        $buyer_job_arr[$row[csf("po_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
	    }


	    $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

	    foreach ($sql_supplier as $supplier_data) {//contact_no
	        $row_mst[csf('supplier_id')];

	        if ($supplier_data[csf('address_1')] != '')
	            $address_1 = $supplier_data[csf('address_1')] . ',' . ' ';
	        else
	            $address_1 = '';
	        if ($supplier_data[csf('address_2')] != '')
	            $address_2 = $supplier_data[csf('address_2')] . ',' . ' ';
	        else
	            $address_2 = '';
	        if ($supplier_data[csf('address_3')] != '')
	            $address_3 = $supplier_data[csf('address_3')] . ',' . ' ';
	        else
	            $address_3 = '';
	        if ($supplier_data[csf('address_4')] != '')
	            $address_4 = $supplier_data[csf('address_4')] . ',' . ' ';
	        else
	            $address_4 = '';
	        if ($supplier_data[csf('contact_no')] != '')
	            $contact_no = $supplier_data[csf('contact_no')] . ',' . ' ';
	        else
	            $contact_no = '';
	        if ($supplier_data[csf('web_site')] != '')
	            $web_site = $supplier_data[csf('web_site')] . ',' . ' ';
	        else
	            $web_site = '';
	        if ($supplier_data[csf('supplier_name')] != '')
	            $supplier_name = $supplier_data[csf('supplier_name')] . ',' . ' ';
	        else
	            $supplier_name = '';
	        if ($supplier_data[csf('email')] != '')
	            $email = $supplier_data[csf('email')] . ',' . ' ';
	        else
	            $email = '';
	        $country = $supplier_data['country_id'];
	        $supplier_name = $supplier_name;
	        $supplier_address = $address_1;
	        $supplier_country = $country;
	        $supplier_phone = $contact_no;
	        $supplier_email = $email;
	    }
	    $image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
		$varcode_booking_no = $work_order_no;
		
		$company_address = sql_select("select id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data[0]."");
		foreach($company_address as $row){
			$company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
			$company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
			$company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
			$company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
			$company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
			$company_add[$row[csf('id')]]['city'] = $row[csf('city')];
			$company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
			$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
			$company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
			$company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
			$company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
		}
	    ?>


	                    <div style="width:960px;">
	                    <table width="950" cellspacing="0" align="center" border="1">
	                        <tr>
	                        	<td rowspan="3" width="70">
                                    <?
                                    if ($data[6]=='YarnPurchaseOrderPrintButton')
                                    {   
                                        ?>
                                       <img src="../../<? echo $image_location; ?>" height="70" width="200">
                                        <?
                                    } 
                                    else 
                                    { 
                                        ?>
                                        <img src="../../../<? echo $image_location; ?>" height="70" width="200">
                                        <?    
                                    }
                                    ?>                                    
                                </td>
	                            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	                            <td rowspan="3" id="barcode_img_id"> </td>
	                        </tr>
	                        <tr class="form_caption">
	                        	<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?>
								<? //echo $company_add[$data[0]]['plot_no'].",".$company_add[$data[0]]['level_no'].", ".$company_add[$data[0]]['road_no'].", ".$company_add[$data[0]]['city'].", ".$country_array[$company_add[$data[0]]['country_id']]; ?></td>
	                        </tr>
	                        <tr>
	                            <td colspan="2" align="center" style="font-size:x-large"><strong>PURCHASE ORDER</strong></td>
	                        </tr>
	                    </table><br>
	                    <table width="950" cellspacing="0" align="center" border="1">
	                         <tr>
	                            <td align="left"><strong>To</strong>,&nbsp;<? echo $attention ?></td>
	                            <td width="115"><strong>WO Number</strong></td>
	                            <td width="180" align="left">: <? echo $work_order_no; ?></td>
	                            <td width="80"><strong>Incoterm</strong></td>
	                            <td width="150" align="left">: <? echo $incoterm[$inco_term]; ?></td>
	<!--                            <td><strong>Pay Mode:</strong></td>
	                            <td align="left"><? echo $pay_mode_str; ?></td>-->
	                        </tr>
	                        <tr>
	                           <td rowspan="7">
	                               <?
	                                echo $supplier_name_library[$supplier_id];
	                                echo "<br>";
	                                echo $supplier_address;
	                                echo $lib_country_arr[$country];
	                                echo "<br>";
	                                echo "<b>Mobile &nbsp;&nbsp;&nbsp;:</b>" . $supplier_phone;
	                                echo "<br>";
	                                echo "<b>Mail &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>" . $supplier_email;
	                                ?>
	                           </td>
	                            <td align="left" ><strong>WO Date </strong></td>
	                            <td align="left">: <? echo change_date_format($work_order_date); ?></td>

	                            <td><strong>Currency</strong></td>
	                            <td align="left">: <? echo $currency[$currency_id]; ?></td>

	                        </tr>
	                        <tr>
	                            <td><strong>Pay Mode</strong></td>
	                            <td align="left">: <? echo $pay_mode_str; ?></td>
	                            <td >
	                            	<strong>Buyer</strong>
	                            	<!--<strong>Delivery Date </strong>-->
	                            </td>
	                            <td align="left">: <?
                                if ($wo_basis_id==2) {

                                    $buyer=explode(',', $buyer_name);
                                    foreach ($buyer as $buyers) {
                                        $buyers_all.=$buyer_arr[$buyers].',';
                                    }
                                    echo chop($buyers_all,',');
                                }
                                else if($wo_basis_id==3){
                                    $sql_dtls_buyer = sql_select("select a.buyer_po from  wo_non_order_info_mst a, wo_non_order_info_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] and a.id=b.mst_id");
                                    $poID= $sql_dtls_buyer [0][csf("buyer_po")];
                                     $sql_po_buyer = sql_select("select a.id, b.buyer_name from  wo_po_break_down a, wo_po_details_master b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id= $poID and a.job_no_mst=b.job_no");
                                      $buyers_all="";
                                      foreach ($sql_po_buyer  as $row) {
                                       $buyers_all.=$buyer_arr[$row[csf("buyer_name")]].',';
                                    }
                                     echo chop($buyers_all,',');
                                }
                                else{

                                    $sql_dtls_buyer = sql_select("select b.id, b.buyer_id from  wo_non_order_info_mst a, wo_non_order_info_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] and a.id=b.mst_id");
                                    $buyers_all="";
                                    foreach ($sql_dtls_buyer  as $row) {
                                       $buyers_all.=$buyer_arr[$row[csf("buyer_id")]].',';
                                    }
                                    echo chop($buyers_all,',');
                               }
	                            //echo change_date_format($delivery_date);
	                            ?></td>
	                        </tr>
	                        <tr>
	                            <td align="left" ><strong>Print Date</strong></td>
	                            <td>
	                                <p> <?
	                                $pc_day_time = explode(" ", $pc_date_time);
	                                echo ": ".change_date_format($pc_day_time[0]);
	                                echo " " . $pc_day_time[1] . " " . $pc_day_time[2];
	                                ?>
	                                </p>
	                            </td>
	                            <td align="left" ><strong>WO Basis</strong></td>
	                            <td align="left" >: <? echo $wo_basis[$wo_basis_id]; ?></td>
	                        </tr>
	                        <tr>
	                            <td ><strong>Pay Term </strong></td>
	                            <td>: <? echo $pay_term[$pay_term_id]; ?></td>


	                            <td align="left"><strong>Tenor</strong></td>
	                            <td align="left" >: <? echo $tenor; ?></td>
	                        </tr>
	                        <tr>
	                            <td align="left"><strong>Source &nbsp;&nbsp;&nbsp;:</strong> <? echo $source_str; ?></td>
	                            <td ><strong>Req.NO</strong></td>
	                            <td colspan="3">: <? echo $req_nos; ?></td>
	                        </tr>
	                        <tr>
	                            <td><strong>PI Issue To</strong>: <? echo $company_library[$pi_issue_to]; ?></td>
	                            <td ><strong>Fab.Booking No </strong></td>
	                            <td colspan="3">: <? echo $booking_no; ?></td>
	                        </tr>

	                    </table>
	                         <br>
	    <?
	    if ($wo_basis_id == 3) {
	        $buy_job_sty = "Buyer Job Style";
	    } else {
	        $buy_job_sty = "Buyer Style";
	    }
	    ?>
	                    <table align="center" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
	                        <thead bgcolor="#dddddd" align="center">
	                            <th width="30">SL</th>
	                            <th width="70">Color</th>
	                            <th width="40">Count</th>
	                            <th>Item Description</th>
	                            <th width="40" >UOM</th>
	                            <th width="70">Quantity </th>
	                            <th width="40">Rate</th>
	                            <th width="60">Amount</th>
	                            <th width="60">Delivery Start Date</th>
	                            <th width="60">Delivery End Date</th>
	                            <th width="60">Per day Delv Qty</th>
	                            <th width="50">No. of Lot</th>
	                            <th>IR/Remarks</th>
	                        </thead>
	                        <tbody>
	    <?
	    $store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

	    $i = 1;
	    $buy_job_sty_val = "";
	    $carrency_id = "";
	    $mst_id = $dataArray[0][csf('id')];


	    $sql_dtls = "Select a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id,a.delivery_end_date,a.yarn_inhouse_date,a.remarks,a.number_of_lot from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	    //echo $sql_dtls;
	    $sql_result = sql_select($sql_dtls);
	    foreach ($sql_result as $row) {
	        $feb_des = '';
	        if ($row[csf("yarn_comp_type2nd")] == 0) {
	            $feb_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
	        } else if ($row[csf("yarn_comp_type2nd")] != 0) {
	            $feb_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %,' . $composition[$row[csf("yarn_comp_type2nd")]] . ' ' . $row[csf("yarn_comp_percent2nd")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
	        }

	        $key = $row[csf("po_breakdown_id")] . $row[csf("color_name")] . $row[csf("yarn_count")] . $feb_des . $row[csf("uom")];
	        $dataArr[$key] = array(
	            po_breakdown_id => $row[csf("po_breakdown_id")],
	            color_name => $row[csf("color_name")],
	            yarn_count => $row[csf("yarn_count")],
	            uom => $row[csf("uom")],
	            delivery_end_date => $row[csf("delivery_end_date")],
	            yarn_inhouse_date => $row[csf("yarn_inhouse_date")],
	            remarks => $row[csf("remarks")],
				number_of_lot => $row[csf("number_of_lot")],
	            feb_des => $feb_des
	        );
	        $qtyArr[$key] += $row[csf("supplier_order_quantity")];
	        $amuArr[$key] += $row[csf("amount")];

	        $carrency_id = $row[csf('currency_id')];
	        if ($carrency_id == 1) {
	            $paysa_sent = "Paisa";
	        } else if ($carrency_id == 2) {
	            $paysa_sent = "CENTS";
	        }
	    }

	    $sql_result = sql_select($sql_dtls);
	    foreach ($dataArr as $key => $row) {
	        if ($i % 2 == 0)
	            $bgcolor = "#E9F3FF";
	        else
	            $bgcolor = "#FFFFFF";
	        if ($wo_basis_id == 2) {
	            $buyer_name_val = "";
	            $buyer_id = explode(',', $buyer_name);
	            foreach ($buyer_id as $val) {
	                if ($buyer_name_val == "")
	                    $buyer_name_val = $buyer_arr[$val];
	                else
	                    $buyer_name_val .= ', ' . $buyer_arr[$val];
	            }

	            $buy_job_sty_val = $buyer_name_val . "<br>" . $style;
	        }
	        else if ($wo_basis_id == 3) {
	            if ($row["po_breakdown_id"] != "" && $row["po_breakdown_id"] != 0) {
	                $buyer_name_val = $buyer_arr[$buyer_job_arr[$row["po_breakdown_id"]]["buyer_name"]] . "<br>" . $buyer_job_arr[$row["po_breakdown_id"]]["job_no"] . "<br>" . $buyer_job_arr[$row["po_breakdown_id"]]["style_ref_no"] . "<br>";
	            }
	            $buy_job_sty_val = $buyer_name_val;
	        }

				$startTimeStamp = strtotime($row[yarn_inhouse_date]);
				$endTimeStamp = strtotime($row[delivery_end_date]);
				$timeDiff = abs($endTimeStamp - $startTimeStamp);
				$numberDays = $timeDiff/86400;  // 86400 seconds in one day
				$numberDays = intval($numberDays)+1;
				$tot_number_of_lot+=$row['number_of_lot'];

	        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>">
	                            <td align="center"><? echo $i; ?></td>
	                            <td align="center"><p><? echo $color_arr[$row["color_name"]]; ?></p></td>
	                            <td align="center"><p><? echo $count_arr[$row["yarn_count"]]; ?></p></td>
	                            <td align="center"><p><? echo $row[feb_des]; ?></p></td>
	                            <td align="center"><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
	                            <td align="right"><p><? echo number_format($qtyArr[$key]); ?></p></td>
	                            <td align="right"><p><? echo number_format($amuArr[$key] / $qtyArr[$key],2); ?></p></td>
	                            <td align="right"><p><? echo number_format($amuArr[$key], 2); ?></p></td>
	                            <td align="right"><p><? echo change_date_format($row[yarn_inhouse_date]); ?></p></td>
	                            <td align="right"><p><? echo change_date_format($row[delivery_end_date]); ?></p></td>
	                            <td align="right">
	                                <p><?
	                                $parDayTot+=($qtyArr[$key]/$numberDays);
									echo number_format($qtyArr[$key]/$numberDays);
	                                ?></p>
	                            </td>
	                            <td align="right"><? echo $row['number_of_lot']; ?></td>
	                            <td><p><? echo $row['remarks']; ?></p></td>
	                        </tr>
	<?
	$i++;
	    }
	    ?>
	                        </tbody>
	                        <tfoot>
	                                <th colspan="5" align="right">Total </th>
	                                <th align="right"><? echo number_format(array_sum($qtyArr)); ?></th>
	                                <th>&nbsp;</th>
	                                <th align="right"><? echo $word_amount = number_format(array_sum($amuArr), 2); ?></th>
	                                <th colspan="2"></th>
	                                <th align="right"><? echo number_format($parDayTot); ?></th>
	                                <th align="right"><? echo $tot_number_of_lot; ?></th>
	                                <th></th>
	                             <tr>
	                                <th colspan="13" align="left">
	                                    In words: <span style="font-weight:normal !important;">
	                                    <?
	                                    $amount_without_comma = number_format(array_sum($amuArr),2,'.','');
	                                    $inWordReturn =number_to_words($amount_without_comma, $currency[$carrency_id], $paysa_sent);
	                                    $inWordArray = explode($currency[$carrency_id],$inWordReturn);
	                                    echo $currency[$carrency_id]." ".$inWordArray[0]." ".$inWordArray[1];
	                                    ?>
	                                    </span></th>
	                              </tr>
	                        </tfoot>
	                    </table>

	                    <br>



		<?
		echo get_spacial_instruction($work_order_no,"900px",144);

		$user_sql = "select a.id,a.user_full_name,b.custom_designation from user_passwd a,lib_designation b where a.valid=1 and b.id=a.designation";
		$user_data_array=sql_select($user_sql);
		foreach($user_data_array as $row){
			$user_arr[$row[csf(id)]]=$row[csf(user_full_name)].'<br><span style="font-size:12px;">('.$row[csf(custom_designation)].')</span>';
		}



		$sql="select updated_by,inserted_by,company_name,is_approved  FROM wo_non_order_info_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_name')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}

        if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
        }
        $approval_status=sql_select($approval_status);

		$sql="select approved_by  FROM approval_history where mst_id=$data[1] and entry_form=2 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf(approved_by)];
		}

        if($approval_status[0][csf('approval_need')] == 1)
        {
            if($is_approved==1){ echo '<style > body{ background-image: url("../../../img/approved.gif"); } </style>';}
            else{ echo '<style > body{ background-image: url("../../../img/draft.gif"); } </style>';}
        }

	?>


	    <br><br><br><br>
	    <table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="text-align:center;" rules="all" width="880">
			<tr>
	        	<td style="border:none; line-height:13px;" align="left"><? echo $user_arr[$PreparedBy]; ?></td>
	            <td style="border:none; line-height:13px;" align="right"><? echo $user_arr[$last_approved_by]; ?></td>
	        </tr>
			<tr style="alignment-baseline:baseline;">
	        	<td width="" style="text-decoration:overline; border:none" align="left">Prepared By</td>
	            <td width="" style="text-decoration:overline; border:none" align="right">Authorized By</td>
	        </tr>
	    </table>


	    <?
	    		//echo signature_table(42, $data[0], "900px");
	    ?>
	     </div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
	        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	        <script>
	        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	        </script>
	    <?
	    exit();
		}
	else{}
}
if ($action=="yarn_work_order_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    // print_r($rate_amount);die;
	if($data[6] == 6){
		echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');
    } else if ($data[6] == 'YarnPurchaseOrderPrintButton'){
        echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');
	} else {
		echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	}

	//print_r ($data);
	/*if($db_type==0)
	{
		$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, $group_concat(b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks  from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= '$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
	}
	// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
	else
	{
		$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, LISTAGG(CAST (b.po_breakdown_id as varchar(4000) ), ',')  WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= $data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, a.buyer_name, a.style, a.do_no, a.remarks";
	}*/

	$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	$bin_no=return_field_value("bin_no as bin_no","lib_company","id=$data[0]",'bin_no');
	//if(($data[3]==1 || $data[3]==0) && $user_level_library[$user_id]==2)
	if(($data[3]==1 && $user_level_library[$user_id]==2) || ($data[3]==0))
	{

		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$location=return_field_value("city","lib_company","id=$data[0]" );
		$address=return_field_value("address","lib_location","id=$data[0]");
		$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
		$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
		$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
		$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');


		$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks,do_no, insert_date  FROM  wo_non_order_info_mst WHERE id = $data[1]");
		foreach($sql_data as $row)
		{
			$work_order_no=$row[csf("wo_number")];
			$item_category_id=$row[csf("item_category")];
			$supplier_id=$row[csf("supplier_id")];
			$work_order_date=$row[csf("wo_date")];
			$currency_id=$row[csf("currency_id")];
			$buyer_name=$row[csf("buyer_name")];
			$style=$row[csf("style")];
			$wo_basis_id=$row[csf("wo_basis_id")];
			$pay_mode_id=$row[csf("pay_mode")];
			$source=$row[csf("source")];
			$delivery_date=$row[csf("delivery_date")];
			$attention=$row[csf("attention")];
			$requisition_no=$row[csf("requisition_no")];
			$delivery_place=$row[csf("delivery_place")];
			$do_no=$row[csf("do_no")];
			$remarks=$row[csf("remarks")];
			$insert_date=$row[csf("insert_date")];
		}

		$sql_job=sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql_job as $row)
		{
			$buyer_job_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$buyer_job_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$buyer_job_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$buyer_job_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$buyer_job_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		}


		$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

        foreach($sql_supplier as $supplier_data)
		{//contact_no
			$row_mst[csf('supplier_id')];

			if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
			if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
			if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
			if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
			if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
			if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
			if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
			//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
			$country = $supplier_data['country_id'];

			$supplier_address = $address_1;
			$supplier_country =$country;
			$supplier_phone =$contact_no;
			$supplier_email = $email;
		}
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//echo "select image_location from common_photo_library where  file_type=1 and form_name='company_details' and master_tble_id='$data[0]'";
		$varcode_booking_no=$work_order_no;

            ?>
            <div style="width:930px;">
            <table width="900" cellspacing="0" align="center">
                <tr>
                    <td rowspan="3" width="70">
                    <? if($data[6] == 6){ ?>
                        <img src="../../../<? echo $image_location; ?>" height="70" width="200">
                    <? }else if ($data[6] =='YarnPurchaseOrderPrintButton') { ?>
                        <img src="../../../<? echo $image_location; ?>" height="70" width="200">
                    <? }else{ ?>
                        <img src="../../<? echo $image_location; ?>" height="70" width="200">
                    <? }
                    ?></td>
                    <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
                    <td rowspan="3" id="barcode_img_id"></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="2" align="center" style="font-size:14px">
					<? 
					 $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$data[0]'");

					 foreach ($nameArray as $result) {
					   ?>
					   <? echo $result[csf('plot_no')]; ?>
					   <? echo $result[csf('level_no')] ?>
					   <? echo $result[csf('road_no')]; ?>
					   <? echo $result[csf('block_no')]; ?>
					   <? echo $result[csf('city')]; ?>
					   <? echo $result[csf('zip_code')]; ?>
					   <? echo $result[csf('province')]; ?>
					   <? echo $country_arr[$result[csf('country_id')]]; ?>
					   <? echo $result[csf('email')]; ?>
					   <? echo $result[csf('website')];
					 }
					//echo $location; ?>
				</td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2] ; ?></strong></td>
                </tr>

                <tr>
                    <?
                    if($db_type==0)
                    {
                        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
                    }
                    else
                    {
                        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
                    }
                    $approval_status=sql_select($approval_status);
                    $draft = '';
                    if($approval_status[0][csf('approval_need')] == 1)
                    {
                        if($data[5] != 1){
                            $draft = 'Draft';
                        }
                    }

                    ?>
                    <td colspan="2" align="center" style="font-size:x-large"><strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;<font style="color:#F00"><? if($data[5]==1){ echo "(Approved)";}else{echo $draft;}; ?> </font></strong></td>
                </tr>
            </table>
            <table width="900" cellspacing="0" align="center">
               <tr>
                    <td width="300" align="left" style="font-size:16px;"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
                    <td width="150" style="font-size:16px;"><strong>WO Number:</strong></td>
                    <td width="150" align="left" style="font-size:16px;"><b><? echo $work_order_no; ?></b></td>
                    <td><strong>Currency:</strong></td>
                    <td align="left"><? echo $currency[$currency_id];
                            //echo $pay_mode[$data[4]];
                    ?></td>
                </tr>
                <tr>
                    <td rowspan="4" style="font-size:16px;"><? echo "<strong>".$supplier_name_library[$supplier_id]."</strong>"; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; echo "<br>"; echo "Remarks :".$remarks; ?></td>
                    <td width="150" align="left" ><strong>WO Date :</strong></td>
                    <td width="150" align="left"><? echo change_date_format($work_order_date); ?></td>
                    <td align="left" ><strong>WO Basis:</strong></td>
                    <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>
                </tr>

                <tr>
                    <td ><strong>Delivery Date :</strong></td>
                    <td><? echo change_date_format($delivery_date); ?></td>
                    <td align="left"><strong>D/O No.</strong></td>
                    <td align="left" ><? echo $do_no; ?></td>
                </tr>
                <tr>
                    <td ><strong>Pay Mode :</strong></td>
                    <td><? echo $pay_mode[$pay_mode_id]; ?></td>
                     <td ><strong><? if($bin_no) echo 'BIN'; ?>  :</strong></td>
               		 <td><? if($bin_no) echo $bin_no; ?></td>
                </tr>

                <tr>
                    <td align="right" colspan="5" >Print Date: <? $pc_day_time=explode(" ",$pc_date_time); echo change_date_format($pc_day_time[0]); echo " ".$pc_day_time[1]." ".$pc_day_time[2]; ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="5" >Insert Date: <? $insert_day_time=explode(" ",$insert_date); echo change_date_format($insert_day_time[0]); echo " ".$insert_day_time[1]." ".$insert_day_time[2]; ?></td>
                </tr>
            </table>
            <br>
            <?
            if($wo_basis_id==3)
            {
                $buy_job_sty="Buyer Job Style";
            }
            else if($wo_basis_id==2)
            {
                $buy_job_sty="Buyer Style";
            }
            else if($wo_basis_id==1)
            {
                $buy_job_sty="Buyer Job Style";
            }
            ?>
            <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <? if($wo_basis_id!=1){ ?><th width="90">PO No</th><? } ?>
                    <th width="140"><? echo $buy_job_sty; ?></th>
                    <th width="70">Color</th>
                    <th width="60">Count</th>
                    <th width="250">Item Description</th>
                    <th width="50" >UOM</th>
                    <th width="70">Quantity </th>
                    <th width="60">Rate</th>
                    <th >Amount</th>
                </thead>
                <tbody>
                    <?
                    $store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

                    $i=1; $buy_job_sty_val="";
                    $mst_id=$dataArray[0][csf('id')];

                    $sql_dtls="Select a.id, a.job_no,a.buyer_id,a.style_no, a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
                     //echo $sql_dtls;
                    $sql_result = sql_select($sql_dtls);
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        $order_quantity+=$row[csf('supplier_order_quantity')];
                        $amount += $row[csf('amount')];
                        if($wo_basis_id==2)
                        {
                            $buyer_name_val="";
                            $buyer_id=explode(',',$buyer_name);
                            foreach($buyer_id as $val)
                            {
                                if($buyer_name_val=="") $buyer_name_val=$buyer_arr[$val]; else $buyer_name_val.=', '.$buyer_arr[$val];
                            }

                            $buy_job_sty_val=$buyer_name_val."<br>".$style;
                        }
                        else if($wo_basis_id==3)
                        {
                            if($row[csf("po_breakdown_id")]!="" && $row[csf("po_breakdown_id")]!=0)
                            {
                                $buyer_name_val=$buyer_arr[$buyer_job_arr[$row[csf("po_breakdown_id")]]["buyer_name"]]."<br>".$buyer_job_arr[$row[csf("po_breakdown_id")]]["job_no"]."<br>".$buyer_job_arr[$row[csf("po_breakdown_id")]]["style_ref_no"]."<br>";
                            }
                            $buy_job_sty_val=$buyer_name_val;
                        }
                        else if($wo_basis_id==1)
                        {
                            if($row[csf("job_no")]!="")
                            {
                                $buyer_name_val=$buyer_arr[$row[csf("buyer_id")]]."<br>".$row[csf("job_no")]."<br>".$row[csf("style_no")]."<br>";
                            }
                            $buy_job_sty_val=$buyer_name_val;
                        }



                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td align="center"><? echo $i; ?></td>
                            <?
                            $feb_des='';
                            if($row[csf("yarn_comp_type2nd")]==0)
                            {
                                $feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %, '.$yarn_type[$row[csf("yarn_type")]];
                            }
                            else if( $row[csf("yarn_comp_type2nd")]!=0)
                            {
                                $feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %,'.$composition[$row[csf("yarn_comp_type2nd")]].' '.$row[csf("yarn_comp_percent2nd")].' %, '.$yarn_type[$row[csf("yarn_type")]];
                            }

                            ?>
                            <? if($wo_basis_id!=1){ ?><td><p><? echo $buyer_job_arr[$row[csf("po_breakdown_id")]]["po_number"]; ?>&nbsp;</p></td><? }?>
                            <td><p><? echo $buy_job_sty_val; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
                            <td align="center"><p><? echo $count_arr[$row[csf("yarn_count")]]; ?></p></td>
                            <td align="center"><p><? echo $feb_des; ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf("supplier_order_quantity")],2); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf("amount")],2,".",""); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}  ?></p></td>
                        </tr>
                        <? $i++; 
                    } ?>
                </tbody>
                <tfoot>
                    <th colspan="<? echo ($wo_basis_id==1)?6:7;?>" align="right">Total </th>
                    <th align="right"><? echo number_format($order_quantity,0); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo $word_amount=number_format($amount,2,".",""); ?></th>
                </tfoot>
            </table>
            <table width="900" align="center">
                <tr>
                <td colspan="11">&nbsp;  </td>
                </tr>
                <tr>
                <td colspan="11"> Amount in words:<? echo number_to_words($word_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
                </tr>
                <tr>
                <td colspan="11">&nbsp;   </td>
                </tr>
                <tr>
                <td colspan="11">&nbsp;   </td>
                </tr>
            </table>
			<br>
			<h2>Summery</h2>
			<br>
			<table  width="900" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
				<thead>
					<tr>
                		<th width="3%">Sl</th>
                		<th width="25%">Composition</th>
                		<th width="27%">Color</th>
                		<th width="15%">Yarn type</th>
                		<th width="15%">Count</th>
                		<th>Yarn Qty</th>
            		</tr>
        		</thead>
        	<tbody>
			
        <?php
			$i = 1;
			$sql_summary= "Select a.yarn_count, a.yarn_comp_type1st, a.color_name,a.yarn_type, sum(a.supplier_order_quantity) as yarn_group_total from  wo_non_order_info_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.yarn_count, a.yarn_comp_type1st,a.color_name,a.yarn_type";
			//echo $sql_dtls;//die;
			$sql_result_summary = sql_select($sql_summary);
			$tot_qnty = 0;
			foreach ($sql_result_summary as $row) {
		?>
			<tr>
        <td align="center"><? echo $i++; ?></td>
        <td align="center" ><p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?></p></td>
        <td align="center" ><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
        <td align="center" ><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
        <td align="center"><p><? echo $count_arr[$row[csf("yarn_count")]]; ?>&nbsp;</p></td>
        <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>
			</tr>
			<?php
				}
			?>

        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,0); ?></th>
            </tr>
        </tfoot>
    </table>





            <?

            $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=2 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
            $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=2 AND  mst_id ='$data[1]' order by  approved_no,approved_date");
            $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
            $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
            $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

            ?>
            <? if(count($approved_sql)>0)
            {
                $sl=1;
                ?>
                <div style="margin-top:15px">
                    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                        <label><b>Yarn Work Order Approval Status </b></label>
                        <thead>
                            <tr style="font-weight:bold">
                                <th width="20">SL</th>
                                <th width="250">Name</th>
                                <th width="200">Designation</th>
                                <th width="100">Approval Date</th>
                            </tr>
                        </thead>
                        <? foreach ($approved_sql as $key => $value)
                        {
                            ?>
                            <tr>
                                <td width="20"><? echo $sl; ?></td>
                                <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                                <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                                <td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

                                echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                            </tr>
                            <?
                            $sl++;
                        }
                        ?>
                    </table>
                </div>
                <?
            }
            else{
                echo "<div style='text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;'>".$draft."</div>";
            }
            ?>
            <? if(count($approved_his_sql) > 0)
            {
                $sl=1;
                ?>
                <div style="margin-top:15px">
                    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                        <label><b>Yarn Work Order Approval / Un-Approval History </b></label>
                        <thead>
                            <tr style="font-weight:bold">
                                <th width="20">SL</th>
                                <th width="150">Approved / Un-Approved</th>
                                <th width="150">Designation</th>
                                <th width="50">Approval Status</th>
                                <th width="150">Reason for Un-Approval</th>
                                <th width="150">Date</th>
                            </tr>
                        </thead>
                        <? foreach ($approved_his_sql as $key => $value)
                        {
                            if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                                <td width="20"><? echo $sl; ?></td>
                                <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                                <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                                <td width="50">Yes</td>
                                <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                                <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

                                echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                            </tr>

                            <?
                            $sl++;
                            $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                            $un_approved_date=$un_approved_date[0];
                            if($db_type==0) //Mysql
                            {
                                if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                            }
                            else
                            {
                                if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                            }

                            if($un_approved_date!="")
                            {
                            ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                                <td width="20"><? echo $sl; ?></td>
                                <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                                <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                                <td width="50">No</td>
                                <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                                <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

                                echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                            </tr>

                                <?
                                $sl++;

                            }

                        }
                        ?>
                    </table>
                </div>
                <?
            }
            ?>

            <!-- //approved status end-->
            <br/>
            <br>

            <?

                echo get_spacial_instruction($work_order_no,"900px",144);
                echo signature_table(42, $data[0], "900px");
            ?>
            </div>
		?>
		

		<? if($data[6] == 6){ ?>
			<script type="text/javascript" src="../../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<? }else{ ?>
			<script type="text/javascript" src="../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
			<? } ?>
	    <script>
	    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	    </script>
		<?
	    exit();
	}
	else{}
}

if ($action=="yarn_work_order_print5")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    // print_r($data);die;
	if($data[6] == 6){
		echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');
	}else{
		echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	}

	//print_r ($data);
	/*if($db_type==0)
	{
		$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, $group_concat(b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks  from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= '$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
	}
	// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
	else
	{
		$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, LISTAGG(CAST (b.po_breakdown_id as varchar(4000) ), ',')  WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= $data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, a.buyer_name, a.style, a.do_no, a.remarks";
	}*/

	$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	//if(($data[3]==1 || $data[3]==0) && $user_level_library[$user_id]==2)
	if(($data[3]==1 && $user_level_library[$user_id]==2) || ($data[3]==0))
	{
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$location=return_field_value("city","lib_company","id=$data[0]" );
		$address=return_field_value("address","lib_location","id=$data[0]");
		$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
		$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
		$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
		$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');


		$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks,do_no, insert_date  FROM  wo_non_order_info_mst WHERE id = $data[1]");
		foreach($sql_data as $row)
		{
			$work_order_no=$row[csf("wo_number")];
			$item_category_id=$row[csf("item_category")];
			$supplier_id=$row[csf("supplier_id")];
			$work_order_date=$row[csf("wo_date")];
			$currency_id=$row[csf("currency_id")];
			$buyer_name=$row[csf("buyer_name")];
			$style=$row[csf("style")];
			$wo_basis_id=$row[csf("wo_basis_id")];
			$pay_mode_id=$row[csf("pay_mode")];
			$source=$row[csf("source")];
			$delivery_date=$row[csf("delivery_date")];
			$attention=$row[csf("attention")];
			$requisition_no=$row[csf("requisition_no")];
			$delivery_place=$row[csf("delivery_place")];
			$do_no=$row[csf("do_no")];
			$remarks=$row[csf("remarks")];
			$insert_date=$row[csf("insert_date")];
		}


		$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

	   	foreach($sql_supplier as $supplier_data)
		{//contact_no
			$row_mst[csf('supplier_id')];

			if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
			if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
			if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
			if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
			if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
			if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
			if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
			//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
			$country = $supplier_data['country_id'];

			$supplier_address = $address_1;
			$supplier_country =$country;
			$supplier_phone =$contact_no;
			$supplier_email = $email;
		}
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//echo "select image_location from common_photo_library where  file_type=1 and form_name='company_details' and master_tble_id='$data[0]'";
		$varcode_booking_no=$work_order_no;
		?>
		<div style="width:970px;">
	    <table width="950" cellspacing="0" align="center">
	        <tr>

	        	<td rowspan="3" width="70">
				<? if($data[6] == 6){ ?>
					<img src="../../../<? echo $image_location; ?>" height="70" width="200">
				<? }else{ ?>
					<img src="../../<? echo $image_location; ?>" height="70" width="200">
				<? }
				?></td>
	            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="3" id="barcode_img_id"></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>
	        </tr>
	        <tr>
	            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2] ; ?></strong></td>
	        </tr>

            <tr>
                <?
                    if($db_type==0)
                    {
                        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
                    }
                    else
                    {
                        $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=15 and status_active=1 and is_deleted=0";
                    }
                    $approval_status=sql_select($approval_status);
                    $draft = '';
                    if($approval_status[0][csf('approval_need')] == 1)
                    {
                        if($data[5] != 1){
                            $draft = 'Draft';
                        }
                    }

                ?>
                <td colspan="2" align="center" style="font-size:x-large"><strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;<font style="color:#F00"><? if($data[5]==1){ echo "(Approved)";}else{echo $draft;}; ?> </font></strong></td>
            </tr>
	    </table>
	    <table width="950" cellspacing="0" align="center">
	         <tr>
	            <td width="300" align="left" style="font-size:16px;"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
	            <td width="150" style="font-size:16px;"><strong>WO Number:</strong></td>
	            <td width="150" align="left" style="font-size:16px;"><b><? echo $work_order_no; ?></b></td>
	            <td width="150" ><strong>Currency:</strong></td>
	            <td align="left"><? echo $currency[$currency_id];?></td>
	        </tr>
	        <tr>
	            <td rowspan="4" style="font-size:16px;"><? echo "<strong>".$supplier_name_library[$supplier_id]."</strong>"; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; echo "<br>"; echo "Remarks :".$remarks; ?></td>
	            <td align="left" ><strong>WO Date :</strong></td>
	            <td align="left"><? echo change_date_format($work_order_date); ?></td>
	            <td align="left" ><strong>WO Basis:</strong></td>
	            <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>
	        </tr>

	        <tr>
	            <td ><strong>Delivery Date :</strong></td>
	            <td><? echo change_date_format($delivery_date); ?></td>
	            <td align="left"><strong>D/O No.</strong></td>
	            <td align="left" ><? echo $do_no; ?></td>
	        </tr>
            <tr>
                <td ><strong>Pay Mode :</strong></td>
                <td><? echo $pay_mode[$pay_mode_id]; ?></td>
            </tr>

	        <tr>
	            <td align="right" colspan="5" >Print Date: <? $pc_day_time=explode(" ",$pc_date_time); echo change_date_format($pc_day_time[0]); echo " ".$pc_day_time[1]." ".$pc_day_time[2]; ?></td>
	        </tr>
	        <tr>
	            <td align="right" colspan="5" >Insert Date: <? $insert_day_time=explode(" ",$insert_date); echo change_date_format($insert_day_time[0]); echo " ".$insert_day_time[1]." ".$insert_day_time[2]; ?></td>
	        </tr>
	    </table>
	         <br>
	         <?
			 	if($wo_basis_id==3)
				{
					$buy_job_sty="Buyer Job Style";
				}
				else if($wo_basis_id==2)
				{
					$buy_job_sty="Buyer Style";
				}
				else if($wo_basis_id==1)
				{
					$buy_job_sty="Buyer Job Style";
				}
			 ?>
	    <table align="center" cellspacing="0" width="1050"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <? if($wo_basis_id!=1){ ?><th width="80">PO No</th><? } ?>
	            <th width="130"><? echo $buy_job_sty; ?></th>
	            <th width="70">Color</th>
	            <th width="50">Count</th>
	            <th width="170">Item Description</th>
	            <th width="50" >UOM</th>
	            <th width="80">Quantity </th>
	            <th width="60">Rate</th>
	            <th width="100">Amount</th>
                <th width="80">LC/SC</th>
                <th>remarks</th>
	        </thead>
	        <tbody>
	<?
		$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

		$i=1; $buy_job_sty_val="";
		$mst_id=$dataArray[0][csf('id')];

		$sql_dtls="Select a.id, a.job_no, a.buyer_id,a.style_no, a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id, a.remarks 
		from wo_non_order_info_dtls a, wo_non_order_info_mst b  
		where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
		//  echo $sql_dtls;
		$sql_result = sql_select($sql_dtls);
		$all_jobs="";
		foreach($sql_result as $row)
		{
			if($job_check[$row[csf('job_no')]]=="" && $row[csf('job_no')]!="")
			{
				$job_check[$row[csf('job_no')]]=$row[csf('job_no')];
				$all_jobs.="'".$row[csf('job_no')]."',";
			}
		}
		
		$all_jobs=chop($all_jobs,",");
		if($all_jobs!="")
		{
			$sql_job=sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number, b.sc_lc 
			from wo_po_details_master a, wo_po_break_down b 
			where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in($all_jobs)");
			foreach($sql_job as $row)
			{
				$buyer_job_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
				$buyer_job_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
				$buyer_job_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$buyer_job_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
				$buyer_job_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				if($job_sc_lc_check[$row[csf("job_no")]][$row[csf("sc_lc")]]=="")
				{
					$job_sc_lc_check[$row[csf("job_no")]][$row[csf("sc_lc")]]=$row[csf("sc_lc")];
					$job_sc_lc[$row[csf("job_no")]].=$row[csf("sc_lc")].",";
				}
				
			}
		}
		
		
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$order_quantity+=$row[csf('supplier_order_quantity')];
				$amount += $row[csf('amount')];
				if($wo_basis_id==2)
				{
					$buyer_name_val="";
					$buyer_id=explode(',',$buyer_name);
					foreach($buyer_id as $val)
					{
						if($buyer_name_val=="") $buyer_name_val=$buyer_arr[$val]; else $buyer_name_val.=', '.$buyer_arr[$val];
					}

					$buy_job_sty_val=$buyer_name_val."<br>".$style;
				}
				else if($wo_basis_id==3)
				{
					if($row[csf("po_breakdown_id")]!="" && $row[csf("po_breakdown_id")]!=0)
					{
						$buyer_name_val=$buyer_arr[$buyer_job_arr[$row[csf("po_breakdown_id")]]["buyer_name"]]."<br>".$buyer_job_arr[$row[csf("po_breakdown_id")]]["job_no"]."<br>".$buyer_job_arr[$row[csf("po_breakdown_id")]]["style_ref_no"]."<br>";
					}
					$buy_job_sty_val=$buyer_name_val;
				}
				else if($wo_basis_id==1)
				{
					if($row[csf("job_no")]!="")
					{
						$buyer_name_val=$buyer_arr[$row[csf("buyer_id")]]."<br>".$row[csf("job_no")]."<br>".$row[csf("style_no")]."<br>";
					}
					$buy_job_sty_val=$buyer_name_val;
				}



			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><? echo $i; ?></td>
	                <?
					$feb_des='';
					if($row[csf("yarn_comp_type2nd")]==0)
					{
						$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %, '.$yarn_type[$row[csf("yarn_type")]];
					}
					else if( $row[csf("yarn_comp_type2nd")]!=0)
					{
						$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %,'.$composition[$row[csf("yarn_comp_type2nd")]].' '.$row[csf("yarn_comp_percent2nd")].' %, '.$yarn_type[$row[csf("yarn_type")]];
					}

					?>
	                <? if($wo_basis_id!=1){ ?><td><p><? echo $buyer_job_arr[$row[csf("po_breakdown_id")]]["po_number"]; ?>&nbsp;</p></td><? }?>
	                <td><p><? echo $buy_job_sty_val; ?>&nbsp;</p></td>
	                <td align="center"><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
	                <td align="center"><p><? echo $count_arr[$row[csf("yarn_count")]]; ?></p></td>
	                <td align="center"><p><? echo $feb_des; ?></p></td>
	                <td align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
	                <td align="right"><p><? echo number_format($row[csf("supplier_order_quantity")],2); ?></p></td>
	                <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
	                <td align="right"><p><? echo number_format($row[csf("amount")],2,".",""); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}  ?></p></td>
                    <td style="padding-left:3px; word-break:break-all;"><p><? echo chop($job_sc_lc[$row[csf("job_no")]],","); ?></p></td>
                    <td style="padding-left:2px; word-break:break-all;"><p><? echo $row[csf("remarks")]; ?></p></td>
				</tr>
				<? $i++; } ?>

	        </tbody>
	        <tfoot>
	                <th colspan="<? echo ($wo_basis_id==1)?6:7;?>" align="right">Total </th>
	                <th align="right"><? echo number_format($order_quantity,0); ?></th>
	                <th>&nbsp;</th>
	                <th align="right"><? echo $word_amount=number_format($amount,2,".",""); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
	        </tfoot>
	    </table>
	    <table width="950" align="center">
	        <tr>
	        <td colspan="11">&nbsp;</td>
	        </tr>
	        <tr>
	        <td colspan="11">Amount in words:<? echo number_to_words($word_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
	        </tr>
	        <tr>
	        <td colspan="11">&nbsp;</td>
	        </tr>
	        <tr>
	        <td colspan="11">&nbsp;</td>
	        </tr>
	    </table>
         <?
		 $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=2 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
		$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=2 AND  mst_id ='$data[1]' order by  approved_no,approved_date");
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
        
		if(count($approved_sql)>0)
		{
			$sl=1;
			?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>Yarn Work Order Approval Status </b></label>
					<thead>
						<tr style="font-weight:bold">
							<th width="20">SL</th>
							<th width="250">Name</th>
							<th width="200">Designation</th>
							<th width="100">Approval Date</th>
						</tr>
					</thead>
					<? foreach ($approved_sql as $key => $value)
					{
						?>
						<tr>
							<td width="20"><? echo $sl; ?></td>
							<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
							<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
							<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
	
							echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
						</tr>
						<?
						$sl++;
					}
					?>
				</table>
			</div>
			<?
		}
		else{
			echo "<div style='text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;'>".$draft."</div>";
		}
		if(count($approved_his_sql) > 0)
    	{
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Yarn Work Order Approval / Un-Approval History </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>

                    <?
                    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">No</td>
                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>

						<?
						$sl++;

					}

                }
                ?>
            </table>
        </div>
        <?
    }
    	?>

        <!-- //approved status end-->
        <br/>
	    <br>

	     <?

			echo get_spacial_instruction($work_order_no,"900px",144);
			echo signature_table(42, $data[0], "900px");
	     ?>
		</div>
		<? if($data[6] == 6){ ?>
			<script type="text/javascript" src="../../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<? }else{ ?>
			<script type="text/javascript" src="../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
			<? } ?>
	    <script>
	    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	    </script>
		<?
	    exit();
	}
	else{}
}

if ($action == "print_to_html_report7") 
{
    extract($_REQUEST);
    $data = explode('*', $data); 
    $company_id= $data[0]; 
    $mst_id= $data[1]; 
    $report_title= $data[2]; 
    $supplier_id= $data[4];
    //print_r ($data);
    $user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $supplier_library = return_library_array('select id,supplier_name FROM lib_supplier', 'id', 'supplier_name');
    $country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");    
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
    $color_name_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');
	$user_designation=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_designation_name=return_library_array("SELECT id,system_designation from lib_designation", "id", "system_designation");
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");
    $dealing_marchant_arr = return_library_array("SELECT id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
   
    $sql_company=sql_select( "select id, company_name, company_short_name, contact_no, plot_no, level_no, road_no, block_no, city, country_id, zip_code, email, website from lib_company where id=$company_id and status_active=1 and is_deleted=0");


    $address=$email=$contact_no="";
    foreach($sql_company as $row)
    {
        if ($row[csf('plot_no')] !='') $address .= $row[csf('plot_no')].','.' ';
        if ($row[csf('level_no')] !='') $address .= $row[csf('level_no')].','.' ';
        if ($row[csf('road_no')] !='') $address .= $row[csf('road_no')].','.' ';
        if ($row[csf('block_no')] !='') $address .= $row[csf('block_no')].','.' ';            
        if ($row[csf('zip_code')] !='') $address .= $row[csf('zip_code')];
        if ($row[csf('city')] !='') $address .= $row[csf('city')].','.' ';
        if ($row[csf('country_id')] !='') $address .= $country_library[$row[csf('country_id')]];
        if ($row[csf('email')] !='') $email = "Email:&nbsp;".$row[csf('email')];
        if ($row[csf('contact_no')] !='') $contact_no = "TEL:&nbsp;".$row[csf('contact_no')];
        if ($row[csf('website')] !='') $website = "Website:&nbsp;".$row[csf('website')];
    }


    $sql_supplier = sql_select("SELECT id, supplier_name, contact_no, country_id, web_site, email, address_1 from  lib_supplier where id=$supplier_id");
    $supplier_address=$supplier_email=$supplier_contact_no="";
    foreach ($sql_supplier as $row) 
    {
        if ($row[csf('address_1')] !='') $supplier_address .= $row[csf('address_1')].','.' ';
        if ($row[csf('country_id')] !='') $supplier_address .= $country_library[$row[csf('country_id')]];
        if ($row[csf('email')] !='') $supplier_email = "Email:&nbsp;".$row[csf('email')];
        if ($row[csf('contact_no')] !='') $supplier_contact_no = "TEL#&nbsp;".$row[csf('contact_no')];
    } 


   $sql="SELECT a.id as ID, a.wo_number as WO_NUMBER, a.wo_basis_id as WO_BASIS_ID, a.payterm_id as PAYTERM_ID, a.tenor as TENOR, a.wo_date as WO_DATE, a.supplier_id as SUPPLIER_ID, a.delivery_date as DELIVERY_DATE, a.attention as CONTACT_PERSON, a.remarks as REMARKS, a.is_approved as IS_APPROVED, a.terms_and_condition as TERMS_AND_CONDITION, a.source as SOURCE, a.pay_mode as PAY_MODE, a.buyer_name as BUYER_NAME, a.inco_term as INCO_TERM, a.currency_id as CURRENCY_ID, a.ship_mode_id as SHIP_MODE_ID, b.yarn_count as YARN_COUNT, b.yarn_comp_type1st as YARN_COMP_TYPE1ST, b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, b.yarn_type as YARN_TYPE, b.supplier_order_quantity as WO_QTY, b.uom as UOM, b.rate as RATE, b.amount as AMOUNT, b.number_of_lot as LOT, b.buyer_id as BUYER_ID, b.remarks as REMARKS_DTLS, b.color_name as COLOR_NAME,a.dealing_marchant as DEALING_MARCHANT, a.inserted_by as INSERTED_BY from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$company_id and a.id=$mst_id and a.entry_form=144 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
   $sql_res=sql_select($sql);
   $check_wo_number=array();
    $buyer_names = "";
   $check_buyer_id=array();
   foreach ($sql_res as $val) 
   {
        if ($check_wo_number[$val['WO_NUMBER']] == "")
        {
            $check_wo_number[$val['WO_NUMBER']]=$val['WO_NUMBER'];
            $wo_number=$val['WO_NUMBER'];
            $tenor=$val['TENOR'];
            $payTerm=$pay_term[$val['PAYTERM_ID']];
            $wo_date=change_date_format($val['WO_DATE']);
            $delivery_date=change_date_format($val['DELIVERY_DATE']);
            $contact_person=$val['CONTACT_PERSON'];
            $payMode=$pay_mode[$val['PAY_MODE']];
            $sourceData=$source[$val['SOURCE']];
            $inco_term=$incoterm[$val['INCO_TERM']];
            $woBasis=$wo_basis[$val['WO_BASIS_ID']];
            $attention=$val['CONTACT_PERSON'];
            $currencyData=$currency[$val['CURRENCY_ID']];
            $shipmentMode=$shipment_mode[$val['SHIP_MODE_ID']];
            $dealing_marchant=$val['DEALING_MARCHANT'];
            $remarks=$val['REMARKS'];
            $insert_user=$val['INSERTED_BY'];
			$buyer_id_arr=explode(',',$val['BUYER_NAME']);
			foreach($buyer_id_arr as $row)
			{
				$buyer_names.=$buyer_arr[$row].', ';
			}
			$approved_id=$val['IS_APPROVED'];

			if($approved_id==1){ $approved_status="Approved";}
			else if($approved_id==3){ $approved_status=$is_approved[$approved_id];}

        }
   }

    ?>
    <style type="text/css">
        table,tbody,tr,td{
            word-wrap: break-word;
            word-break: break-all;
        }
    </style>

    
    <div style="width:1000px;">
        <table width="1200" cellspacing="0" align="center">
            <tr>
                <td rowspan="4" width="70">
                    <img src="../../../<? echo $image_location; ?>" height="70" width="200">                       
                </td>
                <td colspan="10" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="2" align="center" style="font-size:16px"><strong><? echo $address; ?><strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="2" align="center" style="font-size:16px"><strong><? echo $contact_no.', '.$email.', '.$website; ?><strong></td>
            </tr>
        </table><br>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <tr>
                <td align="center" colspan="14" style="font-size:20px"><strong><? echo $report_title.':&nbsp;'.$wo_number; ?></strong></td>
            </tr>
            <tr>
                <td align="left" colspan="7" style="font-size:18px"><strong>Beneficiary:</strong></td>
                <td align="left" colspan="7" style="font-size:18px"><strong>Consignee:</strong></td>
            </tr>
            <tr>
                <td align="left" colspan="7" style="font-size:16px"><strong><? echo $supplier_library[$supplier_id].'<br>'.$supplier_address.'<br>'.$supplier_contact_no.'<br>'.$supplier_email; ?></strong></td>
                <td align="left" colspan="7" style="font-size:16px"><strong><? echo $company_library[$company_id].'<br>'.$address.'<br>'.$contact_no.'<br>'.$email; ?></strong></td>                
            </tr>
            <tr>
                <td width="80" style="font-size:16px"><strong>Issue Date</strong></td>
                <td width="80" style="font-size:16px"><? echo $wo_date; ?></td>
                <td width="100" style="font-size:16px"><strong>Delivery Date</strong></td>
                <td width="80" style="font-size:16px"><? echo $delivery_date; ?></td>
                <td width="75" style="font-size:16px"><strong>Pay Mode</strong></td>
                <td width="85" style="font-size:16px"><? echo $payMode; ?></td>
                <td width="80" style="font-size:16px"><strong>Source</strong></td>
                <td width="80" style="font-size:16px"><? echo $sourceData; ?></td>
                <td width="70" style="font-size:16px"><strong>Pay Term</strong></td>
                <td width="80" style="font-size:16px"><? echo $payTerm; ?></td>
                <td width="60" style="font-size:16px"><strong>Tenor</strong></td>
                <td width="60" style="font-size:16px"><?if($tenor){ echo $tenor.' Days'; }?></td>
                <td width="65" style="font-size:16px"><strong>Buyer</strong></td>
                <td style="font-size:16px; word-break:break-word;"><? echo trim($buyer_names, ", "); ?></td>
            </tr>            
            <tr>
                <td style="font-size:16px"><strong>Incoterm</strong></td>
                <td style="font-size:16px"><? echo $inco_term; ?></td>
                <td style="font-size:16px"><strong>Currency</strong></td>
                <td style="font-size:16px"><? echo $currencyData; ?></td>
                <td style="font-size:16px"><strong>Attention</strong></td>
                <td style="font-size:16px"><? echo $attention; ?></td>
                <td style="font-size:16px"><strong>Ship Mode</strong></td>
                <td style="font-size:16px"><? echo $shipmentMode; ?></td>
				<td style="font-size:16px"><strong>Dealing Merchant</strong></td>
                <td  colspan="3" style="font-size:16px"><? echo $dealing_marchant_arr[$dealing_marchant]; ?></td>
                <td style="font-size:16px"><strong>Remarks</strong></td>
                <td style="font-size:16px"><? echo $remarks; ?></td>
            </tr>       
        </table>
		<br>
		<div width="1200px;" align="center"><strong style="font-size:18px;color:red;"><?echo $approved_status;?> </strong></div>
        <br>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">                
                <th width="40">SL</th>
                <th width="120">Color</th>
                <th width="250">Item Description</th>
                <th width="90">Yarn Count</th>
				<th width="80">UOM</th>
                <th width="80">Quantity</th>
                <th width="80">Unit Price</th>
                <th width="80">Amount</th>
                <!-- <th width="70">No of Lot</th> -->
                <th>Remarks</th>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_wo_qty=$tot_amount=0;
                foreach ($sql_res as $row) 
                {
                    $item_des=$composition[$row['YARN_COMP_TYPE1ST']].' '.$row['YARN_COMP_PERCENT1ST'].' %, '.' '.$yarn_type[$row['YARN_TYPE']];
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td ><? echo $color_name_arr[$row['COLOR_NAME']]; ?></td>
                        <td style="word-break:break-word;"><? echo $item_des; ?></td>
                        <td align="center"><? echo $count_arr[$row['YARN_COUNT']]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
                        <td align="right"><? echo number_format($row['WO_QTY'],2); ?></td>
                        <td align="right"><? echo number_format($row['RATE'],4); ?></td>
                        <td align="right"><? echo number_format($row['AMOUNT'],2); ?></td>
                        <!-- <td width="70" align="right"><? echo $row['LOT']; ?>&nbsp;</td> -->
                        <td><? echo $row['REMARKS_DTLS']; ?></td>
                    </tr>
                    <?
                    $tot_wo_qty+=$row['WO_QTY'];
                    $tot_amount+=$row['AMOUNT'];
                    $i++;
                }
                ?>    
            </tbody>                
            <tfoot>
                <tr bgcolor="#dddddd">
                    <td colspan="5" align="right"><strong>Total</strong></td>
                    <td width="100" align="right"><strong><? echo number_format($tot_wo_qty,2); ?></strong></td>
                    <td >&nbsp;</td>
                    <td width="100" align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
                    <td >&nbsp;</td>
                </tr>
				<tr bgcolor="#dddddd">
                    <td colspan="9" style="font-size:18px;"><strong>Amount in words:&nbsp;</strong><?echo number_to_words($tot_amount,$currencyData);?></td>
                </tr>
            </tfoot> 
        </table>        
        <br>

        <br>
        <?
			echo get_spacial_instruction($wo_number,"1200px",144);
			$approved_sql=sql_select("SELECT mst_id, approved_by as APPROVED_BY ,approved_date as APPROVED_DATE from approval_history where entry_form=2 AND  mst_id =$mst_id order by approved_by");

			if(count($approved_sql)>0)
			{
				?>	<br/>
					<table cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
						<thead>
							<tr>
								<th colspan="4">WO Approval Status </th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="200">Name</th>
								<th width="120">Designation</th>
								<th>Approval Date</th>
							</tr>
						</thead>
						<tbody>
							<?
								$i=1;
								foreach($approved_sql as $row)
								{
									?>
										<tr>
											<td><?=$i;?></td>
											<td><?echo $user_arr[$row["APPROVED_BY"]];?></td>
											<td><?echo $user_designation_name[$user_designation[$row["APPROVED_BY"]]];?></td>
											<td><?echo change_date_format($row["APPROVED_DATE"]);?></td>
										</tr>
									<?
									$i++;
								}
							?>
						</tbody>
					</table>
				<?
			}
        ?>
        <br>
        <?
        echo signature_table(42, $company_id, "1200px", "", "70", $user_arr[$insert_user]);
        ?>  
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <?
    exit();
}


if ($action=="yarn_work_order_print8")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    // print_r($data);die;
	echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
    $user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	// $location=return_field_value("city","lib_company","id=$data[0]" );
	// $address=return_field_value("address","lib_location","id=$data[0]");
	// $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	// $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	// $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');

	$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, wo_date, currency_id, supplier_id, wo_basis_id, delivery_date, source, pay_mode, payterm_id, tenor, do_no FROM  wo_non_order_info_mst WHERE id = $data[1]");
	foreach($sql_data as $row)
	{
		$work_order_no=$row[csf("wo_number")];
		$supplier_id=$row[csf("supplier_id")];
		$work_order_date=$row[csf("wo_date")];
		$currency_id=$row[csf("currency_id")];
		$wo_basis_id=$row[csf("wo_basis_id")];
		$pay_mode_id=$row[csf("pay_mode")];
		$payterm_id=$row[csf("payterm_id")];
		$source_id=$row[csf("source")];
		$delivery_date=$row[csf("delivery_date")];
		$requisition_no=$row[csf("requisition_no")];
		$do_no=$row[csf("do_no")];
		$tenor=$row[csf("tenor")];
	}
	if($db_type==0)
	{
		$woDate=change_date_format($work_order_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$woDate=change_date_format($work_order_date,'','',-1);
	}
	// $exchange_sql="SELECT conversion_rate,marketing_rate,con_date from currency_conversion_rate where currency=$currency_id and company_id=$data[0] and con_date<='$woDate' status_active=1 and is_deleted=0";
	// echo $exchange_sql;
	$exchange_rate=return_field_value("conversion_rate","currency_conversion_rate"," currency=$currency_id and company_id=$data[0] and con_date<='$woDate' and status_active=1 and is_deleted=0 order by id desc","conversion_rate");

	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,web_site,email FROM  lib_supplier WHERE id = $supplier_id");
	foreach($sql_supplier as $supplier_data)
	{
		if($supplier_data[csf('contact_no')]!='')$supplier_phone = $supplier_data[csf('contact_no')];else $supplier_phone='';
		if($supplier_data[csf('email')]!='')$supplier_email = $supplier_data[csf('email')];else $supplier_email='';
	}

	$sql_dtls="SELECT a.id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, a.REQUISITION_NO, b.BOOKING_NO
	from wo_non_order_info_dtls a, inv_purchase_requisition_dtls b
	where a.requisition_dtls_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	// echo $sql_dtls;
	$sql_result = sql_select($sql_dtls);
	$requisition_no=$booking_no='';
	foreach($sql_result as $row)
	{
		$requisition_no.=$row['REQUISITION_NO'].',';
		$booking_no.=$row['BOOKING_NO'].',';
	}
	$requisition_no=implode(", ",array_unique(explode(",",chop($requisition_no,','))));
	$booking_no=implode(", ",array_unique(explode(",",chop($booking_no,','))));

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select image_location from common_photo_library where file_type=1 and form_name='company_details' and master_tble_id='$data[0]'";
	$varcode_booking_no=$work_order_no;
	?>
	<div style="width:970px;">
	<table width="900" cellspacing="0" align="center">
		<tr>
			<td rowspan="3" width="70">
				<img src="../../../<? echo $image_location; ?>" height="70" width="200">
			</td>
			<td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			<td rowspan="3" id="barcode_img_id"></td>
		</tr>
		<!-- <tr class="form_caption">
			<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>
		</tr> -->
		<tr>
			<td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2] ; ?></strong></td>
		</tr>
	</table>
	<br>
	<br>
	<table width="900" cellspacing="0" align="center">
		<tr>
			<td width="100" >To</td>
			<td width="200" ><strong><? echo $company_library[$data[0]]; ?></strong></td>
			<td width="150" >WO Number :</td>
			<td width="150" ><? echo $work_order_no; ?></td>
			<td width="150" >Pay Mode :</td>
			<td ><? echo $pay_mode[$pay_mode_id];?></td>
		</tr>
		<tr>
			<td ></td>
			<td ></td>
			<td >WO Date:</td>
			<td ><? echo change_date_format($work_order_date); ?></td>
			<td >Currency :</td>
			<td ><? echo $currency[$currency_id];?></td>
		</tr>
		<tr>
			<td ></td>
			<td ></td>
			<td ></td>
			<td ></td>
			<td >Source :</td>
			<td ><? echo $source[$source_id];?></td>
		</tr>
		<tr>
			<td >Cell :</td>
			<td ><?echo $supplier_phone;?></td>
			<td >Delivery Date :</td>
			<td ><?echo change_date_format($delivery_date);?></td>
			<td >D/O No. :</td>
			<td ><? echo $do_no;?></td>
		</tr>
		<tr>
			<td >Email :</td>
			<td ><?echo $supplier_email;?></td>
			<td >Print Date :</td>
			<td ><? $pc_day_time=explode(" ",$pc_date_time); echo change_date_format($pc_day_time[0]); echo " ".$pc_day_time[1]." ".$pc_day_time[2]; ?></td>
			<td >WO Basis :</td>
			<td ><? echo $wo_basis[$wo_basis_id];?></td>
		</tr>
		<tr>
			<td ></td>
			<td ></td>
			<td >Pay Term:</td>
			<td ><? echo $pay_term[$payterm_id]; ?></td>
			<td >Tenor :</td>
			<td ><? echo $tenor?></td>
		</tr>
		<tr>
			<td >Req.NO :</td>
			<td colspan="5"><?echo $requisition_no;?></td>
		</tr>
		<tr>
			<td >Fab.Booking No :</td>
			<td colspan="5"><?echo $booking_no;?></td>
		</tr>
	</table>
	<br>
	<table width="900" cellspacing="0" align="center">
		<tr>
			<td>Dear Sir,</td>
		</tr>
		<tr>
			<td>Pleased to inform You that Your price offer has been accepted with the following terms .</td>
		</tr>
	</table>
	<br>
	<table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<th width="30">SL</th>
			<th width="100">Color</th>
			<th width="100">Count</th>
			<th width="200">Item Description</th>
			<th width="80" >UOM</th>
			<th width="80">Quantity </th>
			<th width="80">Rate</th>
			<th width="100">Amount USD</th>
			<th >Amount BDT</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($sql_result as $row)
			{
				$order_quantity+=$row[csf('supplier_order_quantity')];
				$amount += $row[csf('amount')];
				$word_amount_bd += $row[csf('amount')]*$exchange_rate;
				$feb_des='';
				if($row[csf("yarn_comp_type2nd")]==0)
				{
					$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %, '.$yarn_type[$row[csf("yarn_type")]];
				}
				else if( $row[csf("yarn_comp_type2nd")]!=0)
				{
					$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %,'.$composition[$row[csf("yarn_comp_type2nd")]].' '.$row[csf("yarn_comp_percent2nd")].' %, '.$yarn_type[$row[csf("yarn_type")]];
				}
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
					<td align="center"><p><? echo $count_arr[$row[csf("yarn_count")]]; ?></p></td>
					<td align="center"><p><? echo $feb_des; ?></p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
					<td align="right"><p><? echo number_format($row[csf("supplier_order_quantity")],2); ?></p></td>
					<td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
					<td align="right"><p><? echo number_format($row[csf("amount")],2,".","");  ?></p></td>
					<td align="right"><p><? echo number_format($row[csf("amount")]*$exchange_rate,2,".",""); ?></p></td>
				</tr>
				<? $i++; 
			} ?>

		</tbody>
		<tfoot>
			<th colspan="5" align="right">Total </th>
			<th align="right"><? echo number_format($order_quantity,0); ?></th>
			<th>&nbsp;</th>
			<th align="right"><? echo $word_amount=number_format($amount,2,".",""); ?></th>
			<th align="right"><? echo number_format($word_amount_bd,2,".",""); ?></th>
		</tfoot>
	</table>
	<br>
	<table width="950" cellspacing="0" align="center">
		<tr>
			<td>In words [USD]: <?
				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";} 
			 	echo number_to_words($word_amount,$currency[$currency_id],$paysa_sent); ?>
			 </td>
		</tr>
		<tr>
			<td>In words [BDT]: <? echo number_to_words($word_amount_bd,"TK","Paisa"); ?></td>
		</tr>
	</table>
	<br/>
	<br>
	<?
	echo get_spacial_instruction($work_order_no,"900px",144);
	// echo signature_table(42, $data[0], "900px");
    
	?>
	<br>
	<table width="900" cellspacing="0" align="center">
		<tr>
			<td>Your scheduled delivery with quality and co-operation will be highly appreciated.</td>
		</tr>
		<tr>
			<td>Thank You</td>
		</tr>
	</table>
	<br>
    <?
   
    echo signature_table(42, $data[0], "900px", "", "70", $user_arr[$user_id]);
    ?>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action=="stock_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	/*?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==1) // wo number
			{
				document.getElementById('search_by_th_up').innerHTML="Enter WO Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==2) // supplier
			{
				var supplier_name = '<option value="0">--- Select ---</option>';
				<?
				$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 order by supplier_name",'id','supplier_name');
				foreach($supplier_arr as $key=>$val)
				{
					echo "supplier_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Supplier Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:150px " class="combo_boxes" id="txt_search_common">'+ supplier_name +'</select>';
			}
		}

		function js_set_value(wo_number)
		{
			$("#hidden_wo_number").val(wo_number);
			parent.emailwindow.hide();
		}

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="100">Item Category</th>
                            <th width="130">Search By</th>
                            <th width="150" align="center" id="search_by_th_up">Enter Order Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="100">
                            <?
                                echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", $itemCategory, "",1);
                            ?>
                            </td>
                            <td width="130">
                            <?
                            $searchby_arr=array(1=>"WO Number",2=>"Supplier");
                            echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 0, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
                            ?>
                            </td>
                            <td width="150" align="center" id="search_by_td">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_wo_search_list_view', 'search_div', 'yarn_work_order_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?*/


		$search_cond = "";

		if($yarn_type_id>0) $search_cond .= " and a.yarn_type in ($yarn_type_id)";
		if($yarn_count>0) $search_cond .= " and a.yarn_count_id in($yarn_count)";
		if($yarn_comp>0) $search_cond .= " and a.yarn_comp_type1st in($yarn_comp)";
		//if($cbo_supplier==0) $search_cond .=""; else $search_cond .= "  and a.supplier_id in($cbo_supplier)";
		//if($txt_composition=="") $search_cond .= ""; else $search_cond .= " and a.product_name_details like '%".trim($txt_composition)."%'";

		if($cbo_company_name==0)
		{
			$company_cond="";
		}
		else
		{
			$company_cond= " and a.company_id=$cbo_company_name";
		}

		$issue_qnty_arr=sql_select("select a.prod_id, b.recv_trans_id, b.issue_qnty from  inv_transaction a,  inv_mrr_wise_issue_details b where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category=1");
		$mrr_issue_qnty_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$mrr_issue_qnty_arr[$row[csf("recv_trans_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")];
		}

		$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
		where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
		$mrr_rate_arr=array();
		foreach($mrr_rate_sql as $row)
		{
			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
		}

		//$trans_out_sql="select ";

		if($db_type==0)
		{
			$exchange_rate=return_field_value("conversion_rate","currency_conversion_rate","currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
			$sql="select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, c.recv_number, c.receive_date, 1 as type
			from product_details_master a, inv_transaction b, inv_receive_master c
			where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(1,9) and b.transaction_type in(1,4) $company_cond $search_cond
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, c.recv_number, c.receive_date
			union all
			select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, c.transfer_system_id as recv_number, c.transfer_date as receive_date, 2 as type
			from product_details_master a, inv_transaction b, inv_item_transfer_mst c
			where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) and c.transfer_criteria in(1,2) $company_cond $search_cond
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, c.transfer_system_id, c.transfer_date
			order by yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, id, recv_number, receive_date";
		}
		else
		{
			$exchange_rate=return_field_value("conversion_rate","(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)","ROWNUM = 1");
			$sql="select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, c.recv_number, c.receive_date, 1 as type
			from product_details_master a, inv_transaction b, inv_receive_master c
			where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(1,9) and b.transaction_type in(1,4) $company_cond $search_cond
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, c.recv_number, c.receive_date
			union all
			select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, c.transfer_system_id as recv_number, c.transfer_date as receive_date, 2 as type
			from product_details_master a, inv_transaction b, inv_item_transfer_mst c
			where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) and c.transfer_criteria in(1,2) $company_cond $search_cond
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, c.transfer_system_id, c.transfer_date
			order by yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, id, recv_number, receive_date";
		}

		//echo $sql;//die;echo count($result);
		$result = sql_select($sql);

		ob_start();
		?>
		<div>
            <table width="1340" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="15" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $companyArr[$cbo_company_name]; ?>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2" width="40">SL</th>
                        <th rowspan="2" width="120">Company Name</th>
                        <th colspan="7">Description</th>
                        <th rowspan="2" width="100">Stock In Hand</th>
                        <th rowspan="2" width="90">Avg. Rate (USD)</th>
                        <th rowspan="2" width="100">Stock Value (USD)</th>
                        <th rowspan="2" width="100">MRR No.</th>
                        <th rowspan="2" width="70">Receive Date</th>
                        <th rowspan="2">Age (Days)</th>
                    </tr>
                    <tr>
                        <th width="50">Prod.ID</th>
                        <th width="60">Count</th>
                        <th width="150">Composition</th>
                        <th width="100">Yarn Type</th>
                        <th width="80">Color</th>
                        <th width="80">Lot</th>
                        <th width="100">Supplier</th>
                    </tr>
                </thead>
			</table>
			<div style="width:1340px; overflow-y:scroll; max-height:350px" id="scroll_body" >
				<table width="1320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
                    $tot_stock_value=0;$i=1;
                    foreach($result as $row)
                    {
                        $ageOfDays = datediff("d",$row[csf("receive_date")],date("Y-m-d"));

                        $compositionDetails = $composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]."%\n";
                        if($row[csf("yarn_comp_type2nd")]!=0) $compositionDetails.=$composition[$row[csf("yarn_comp_type2nd")]]." ".$row[csf("yarn_comp_percent2nd")]."%";


						$totalRcv=$row[csf("cons_quantity")];
						$totalIssue=0;
						$trans_id_arr=array_unique(explode(",",$row[csf("trans_id")]));
						foreach($trans_id_arr as $tr_id)
						{
							$totalIssue+=$mrr_issue_qnty_arr[$tr_id][$row[csf("id")]];
						}

                        $stockInHand=$totalRcv-$totalIssue;

                        //subtotal and group-----------------------
                        $check_string=$row[csf("yarn_count_id")].$compositionDetails.$row[csf("yarn_type")];

                         if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if(number_format($stockInHand,2)>0.00)
						{
							if( !in_array($check_string,$checkArr) )
							{
								$checkArr[$i]=$check_string;
								if($i>1)
								{
								?>
									<tr bgcolor="#CCCCCC" style="font-weight:bold">
										<td colspan="9" align="right">Sub Total</td>
										<td width="100" align="right"><? echo number_format($total_stock_in_hand,2); ?></td>
										<td width="90" align="right">&nbsp;</td>
										<!--<td width="110" align="right"><?echo number_format($sub_stock_value,2); ?></td>-->
										<td width="100" align="right"><? echo number_format($sub_stock_value_usd,2); ?></td>
										<td width="100" align="right">&nbsp;</td>
										<td width="70" align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
								<?

									$total_stock_in_hand=0;
									$sub_stock_value=0;
									$sub_stock_value_usd=0;
								}
							}

							$avg_rate=$mrr_rate_arr[$row[csf("id")]];
							$stock_value=$stockInHand*$avg_rate;
							$stock_value_usd=$stock_value/$exchange_rate;
							$avg_rate_usd=$stock_value_usd/$stockInHand;
							$avg_rate_usd=abs($avg_rate_usd);
							//$stock_value_usd=($stockInHand*$row[csf("avg_rate_per_unit")])/$exchange_rate;

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="120"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
								<td width="50" align="center"><? echo $row[csf("id")]; ?></td>
								<td width="60" align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
								<td width="100" title="<? echo "transaction Id=".$row[csf("trans_id")]."Receive Qnty=".$totalRcv."Issue Qnty=".$totalIssue; ?>"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="right" title="<? echo $row[csf("cons_amount")]; ?>" ><? echo number_format($stockInHand,2); ?></td>
								<td width="90" align="right" title="<? echo $avg_rate."==".$stock_value_usd."==".$row[csf("cons_quantity")]."==".$exchange_rate; ?>"><? echo number_format($avg_rate_usd,4); ?></td>
								<td width="100" align="right"><? echo number_format($stock_value_usd,2); ?></td>
								<td width="100" align="center"><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
								<td align="center"><? echo $ageOfDays; ?></td>

							</tr>
							<?
							$i++;
						}
						$total_stock_in_hand+=$stockInHand;
						$sub_stock_value+=$stock_value;
						$sub_stock_value_usd+=$stock_value_usd;

						$grand_total_stock_in_hand+=$stockInHand;
						$tot_stock_value+=$stock_value;
						$tot_stock_value_usd+=$stock_value_usd;
                    }
                    ?>
                    <tr bgcolor="#CCCCCC" style="font-weight:bold">
                        <td colspan="9" align="right">Sub Total</td>
                        <td width="100" align="right"><? echo number_format($total_stock_in_hand,2); ?></td>
                        <td width="90" align="right">&nbsp;</td>
                        <!--<td width="110" align="right"><echo number_format($sub_stock_value,2); ?></td>-->
                        <td width="100" align="right"><? echo number_format($sub_stock_value_usd,2); ?></td>
                        <td width="100" align="right">&nbsp;</td>
                        <td width="70" align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                </table>
            </div>
            <table width="1320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
                <tr class="tbl_bottom">
                    <td width="40"></td>
                    <td width="120"></td>
                    <td width="50"></td>
                    <td width="60"></td>
                    <td width="150"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100" align="right">Grand Total</td>
                    <td width="100" align="right" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand,2); ?></td>
                    <td width="90" align="right">&nbsp;</td>
                    <!--<td width="110" align="right"><?echo number_format($tot_stock_value,2); ?></td>-->
                    <td width="100" align="right"><? echo number_format($tot_stock_value_usd,2); ?></td>
                    <td width="100" align="right">&nbsp;</td>
                    <td width="70" align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                </tr>
            </table>
		</div>
	<?
	exit();
}

?>
