<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************



if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("ex_factory")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
		if($result[csf("ex_factory")]==1)
		{
			echo "$('#txt_ex_quantity').attr('readonly',false);\n";
		}

	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=32","is_control");
	if(!$variable_is_control)$variable_is_control=0;
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
	$variable_qty_source_poly=return_field_value("preceding_page_id","variable_settings_production","company_name=$data and variable_list=33","preceding_page_id");
	echo "document.getElementById('txt_qty_source').value='".$variable_qty_source_poly."';\n";

 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_transport_com")
{
	echo create_drop_down( "cbo_transport_company", 172, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data' $buyer_cond  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name","id,supplier_name", 1, "-- Select Transport --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_forwarder")
{
	echo create_drop_down( "cbo_forwarder", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select--", $selected,"","0" );

	//echo create_drop_down( "cbo_buyer_name", 172, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' $buyer_cond  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="delivery_system_popup") //System PopUp
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
		<script>
		function js_set_value(str)
		{
	 		$("#hidden_return_id").val(str);
	    	parent.emailwindow.hide();
	 	}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	             <thead>
	                <th width="160">Transport Com.</th>
	                <th width="150">Buyer Name</th>
	                <th width="100">Return No</th>
	                <th width="100">Order No</th>
	                <th width="200">Return Date</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	            </thead>
	            <tr align="center">
	                <td>
	                <?
	                echo create_drop_down( "cbo_trans_com", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 );
	                ?>
	                </td>
	                <td>
	                <?
						echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
					?>
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" />
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_po_no" id="txt_po_no" />
	                </td>
	                <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                </td>
	                <td align="center">
	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_return_search_list', 'search_div_delivery', 'garments_exfactory_return_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
	                </td>
	            </tr>
	            <tr>
	                <td align="center" height="40" colspan="6" valign="middle">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_return_id" >
	                </td>
	            </tr>
	        </table>
	        <div id="search_div_delivery" style="margin-top:20px;"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

if($action=="create_return_search_list")
{

 	$ex_data = explode("_",$data);
	$trans_com = $ex_data[0];
	$txt_challan_no = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$po_no = str_replace("'","",$ex_data[5]);
	$buyer_id = str_replace("'","",$ex_data[6]);
	//echo $trans_com;die;
	$sql_cond="";
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!=0) $sql_cond .= " and a.company_id='$company'";
	if(trim($buyer_id)!=0) $sql_cond .= " and a.buyer_id='$buyer_id'";

	if(trim($txt_challan_no)!="") $sql_cond .= " and a.sys_number_prefix_num='$txt_challan_no'";
	if(trim($trans_com)!=0) $sql_cond .= " and a.transport_supplier='$trans_com'";
	if(trim($po_no)!="")
	{
		if($db_type==2) $po_concat="listagg(CAST(id as VARCHAR(4000)),',') within group (order by id) as po_id";
		else if($db_type==0) $po_concat="group_concat(id) as po_id";

		$po_no_id = return_field_value("$po_concat","wo_po_break_down","po_number='$po_no'","po_id");
		 $po_cond="and b.po_break_down_id in($po_no_id)";
	}
	else
	{
		$po_cond="";
	}
	if($db_type==0)
	{
		$sql = "select a.id, a.sys_number_prefix_num, year(a.insert_date) as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no,group_concat(b.po_break_down_id) as po_break_down_id
	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
	where a.id=b.delivery_mst_id  and a.entry_form=85 and a.status_active=1 and a.is_deleted=0 $sql_cond $po_cond
	group by  a.id, a.sys_number_prefix_num, year(a.insert_date), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	else
	{
		 $sql = "select a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY') as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no, listagg(CAST(b.po_break_down_id as VARCHAR(4000)),',') within group (order by b.po_break_down_id) as po_break_down_id
	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
	where a.id=b.delivery_mst_id and a.entry_form=85 and a.status_active=1 and a.is_deleted=0 $sql_cond $po_cond
	group by  a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY'), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	$exfact_qty_arr=return_library_array( "select delivery_mst_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and delivery_mst_id>0 group by delivery_mst_id",'delivery_mst_id','ex_factory_qnty');
 	$buyer_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$trans_com_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id","supplier_name");
   ?>
     	<table cellspacing="0" width="1030" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="50" >Sys Num</th>
                <th width="50">Year</th>
                <th width="70" >Buyer Name</th>
                <th width="155" >Transport Company</th>
                <th width="50" >Challan No</th>
                <th width="70" >Delivery Date</th>
                <th width="120" >Driver Name</th>
                <th width="90" >Truck No</th>
                <th width="90">Lock No</th>
                <th width="80">Ex-fact Qty</th>
                <th >Order No</th>
            </thead>
     	</table>
     <div style="width:1030px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1012" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='".$row[csf('challan_no')]."' and entry_form!=85 ","buyer_id");
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>);" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="50" align="center"><p><? echo $row[csf("sys_number_prefix_num")]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf("delivery_year")]; ?></p></td>
                    <td width="70"><p><? echo $buyer_name_arr[$buyer_id]; ?>&nbsp;</p></td>
                    <td width="155" align="center"><p><? echo $trans_com_arr[$row[csf("transport_supplier")]];?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf("driver_name")]; ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $row[csf("truck_no")];?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $row[csf("lock_no")];?>&nbsp;</p></td>
                    <td width="80" align="right"><p><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></p></td>
                    <td><p>
					<?
					$po_id_arr=array_unique(explode(",",$row[csf("po_break_down_id")]));
					$all_po="";
					foreach($po_id_arr as $po_id)
					{
						if($all_po=="") $all_po=$order_num_arr[$po_id]; else $all_po.=", ".$order_num_arr[$po_id];
					}
					echo $all_po;
					?>&nbsp;</p></td>
                </tr>
				<?
				$i++;
             }
   		?>
			</table>
		</div>
	  <?
	exit();

}

if($action=="populate_master_from_date") //Master Part
{
	$sql_mst=sql_select("select id, sys_number, company_id, location_id, challan_no, transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no,destination_place,forwarder,mobile_no, do_no, gp_no
	from  pro_ex_factory_delivery_mst where id=$data and entry_form=85 and status_active=1 and is_deleted=0 ");
	foreach($sql_mst as $row)
	{
			$prev_mst_id=return_field_value("id","pro_ex_factory_delivery_mst","sys_number='".$row[csf('challan_no')]."' and entry_form!=85 ","id");
			$prev_delivery_date=return_field_value("delivery_date","pro_ex_factory_delivery_mst","sys_number='".$row[csf('challan_no')]."' and entry_form!=85 ","delivery_date");

		echo "$('#txt_return_no').val('".$row[csf('sys_number')]."');\n";
		echo "$('#txt_return_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf('company_id')].");\n";
		echo "$('#cbo_location_name').val(".$row[csf('location_id')].");\n";
		echo "$('#txt_challan_no').val('".$row[csf('challan_no')]."');\n";
		echo "$('#txt_prev_mst_id').val('".$prev_mst_id."');\n";
		echo "$('#txt_ex_factory_date').val('".change_date_format($prev_delivery_date)."');\n";
		echo "$('#cbo_transport_company').val(".$row[csf('transport_supplier')].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf('delivery_date')])."');\n";
		echo "$('#txt_truck_no').val('".$row[csf('truck_no')]."');\n";
		echo "$('#txt_lock_no').val('".$row[csf('lock_no')]."');\n";
		echo "$('#txt_driver_name').val('".$row[csf('driver_name')]."');\n";
		echo "$('#txt_dl_no').val('".$row[csf('dl_no')]."');\n";
		echo "$('#txt_mobile_no').val('".$row[csf('mobile_no')]."');\n";
		echo "$('#txt_do_no').val('".$row[csf('do_no')]."');\n";
		echo "$('#txt_gp_no').val('".$row[csf('gp_no')]."');\n";
		echo "$('#txt_destination').val('".$row[csf('destination_place')]."');\n";
		echo "$('#cbo_forwarder').val(".$row[csf('forwarder')].");\n";
		echo  "$('#cbo_transport_company').attr('disabled',true);\n";
		echo  "$('#cbo_forwarder').attr('disabled',true);\n";
		//echo "set_button_status(0, permission, 'fnc_exFactory_entry',1,0);\n";
		//OG-GDE-15-00006
	}
	exit();
}

//Challan PopUp
if($action=="delivery_challan_sys_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
		<script>
		function js_set_value(str)
		{
	 		$("#hidden_delivery_id").val(str);
	    	parent.emailwindow.hide();
	 	}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	             <thead>
	                <th width="160">Transport Com.</th>
	                <th width="150">Buyer Name</th>
	                <th width="100">Challan No</th>
	                <th width="100">Order No</th>
	                <th width="200">Ex-Factory Date Range</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	            </thead>
	            <tr align="center">
	                <td>
	                <?
	                echo create_drop_down( "cbo_trans_com", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 );
	                ?>
	                </td>
	                <td>
	                <?
						echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and b.tag_company=$company and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
					?>
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_challan_no"   id="txt_challan_no" />
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_po_no" id="txt_po_no" />
	                </td>
	                <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                </td>
	                <td align="center">
	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_delivery_search_list', 'search_div_delivery', 'garments_exfactory_return_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
	                </td>
	            </tr>
	            <tr>
	                <td align="center" height="40" colspan="6" valign="middle">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_delivery_id" >
	                </td>
	            </tr>
	        </table>
	        <div id="search_div_delivery" style="margin-top:20px;"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_delivery_search_list")
{

 	$ex_data = explode("_",$data);
	$trans_com = $ex_data[0];
	$txt_challan_no = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$po_no = str_replace("'","",$ex_data[5]);
	$buyer_id = str_replace("'","",$ex_data[6]);

	//echo $trans_com;die;
	$sql_cond="";
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($buyer_id)!=0) $sql_cond .= " and a.buyer_id='$buyer_id'";
	if(trim($txt_challan_no)!="") $sql_cond .= " and a.challan_no='$txt_challan_no'";
	if(trim($trans_com)!=0) $sql_cond .= " and a.transport_supplier='$trans_com'";
	if(trim($po_no)!="")
	{
		if($db_type==2) $po_concat="listagg(CAST(id as VARCHAR(4000)),',') within group (order by id) as po_id";
		else if($db_type==0) $po_concat="group_concat(id) as po_id";

		$po_no_id = return_field_value("$po_concat","wo_po_break_down","po_number='$po_no'","po_id");
		 $po_cond="and b.po_break_down_id in($po_no_id)";
	}
	else
	{
		$po_cond="";
	}
	if($db_type==0)
	{
		$sql = "select a.id, a.sys_number_prefix_num, year(a.insert_date) as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no,group_concat(b.po_break_down_id) as po_break_down_id
	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
	where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond $po_cond
	group by  a.id, a.sys_number_prefix_num, year(a.insert_date), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	else
	{
		  $sql = "select a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY') as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no, listagg(CAST(b.po_break_down_id as VARCHAR(4000)),',') within group (order by b.po_break_down_id) as po_break_down_id
	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
	where a.id=b.delivery_mst_id and a.entry_form!=85 and a.status_active=1 and a.is_deleted=0 $sql_cond $po_cond
	group by  a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY'), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	$exfact_qty_arr=return_library_array( "select delivery_mst_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and delivery_mst_id>0 group by delivery_mst_id",'delivery_mst_id','ex_factory_qnty');
 	$buyer_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$trans_com_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id","supplier_name");
   ?>
     	<table cellspacing="0" width="1030" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="50" >Sys Num</th>
                <th width="50">Year</th>
                <th width="70" >Buyer Name</th>
                <th width="155" >Transport Company</th>
                <th width="50" >Challan No</th>
                <th width="70" >Delivery Date</th>
                <th width="120" >Driver Name</th>
                <th width="90" >Truck No</th>
                <th width="90">Lock No</th>
                <th width="80">Ex-fact Qty</th>
                <th >Order No</th>
            </thead>
     	</table>
     <div style="width:1030px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1012" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>);" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="50" align="center"><p><? echo $row[csf("sys_number_prefix_num")]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf("delivery_year")]; ?></p></td>
                    <td width="70"><p><? echo $buyer_name_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="155" align="center"><p><? echo $trans_com_arr[$row[csf("transport_supplier")]];?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf("driver_name")]; ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $row[csf("truck_no")];?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $row[csf("lock_no")];?>&nbsp;</p></td>
                    <td width="80" align="right"><p><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></p></td>
                    <td><p>
					<?
					$po_id_arr=array_unique(explode(",",$row[csf("po_break_down_id")]));
					$all_po="";
					foreach($po_id_arr as $po_id)
					{
						if($all_po=="") $all_po=$order_num_arr[$po_id]; else $all_po.=", ".$order_num_arr[$po_id];
					}
					echo $all_po;
					?>&nbsp;</p></td>
                </tr>
				<?
				$i++;
             }
   		?>
			</table>
		</div>
	  <?
	exit();

}

if($action=="populate_challan_master_from_date")
{
	$sql_mst=sql_select("select id, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no,destination_place,forwarder,mobile_no, do_no, gp_no
	from  pro_ex_factory_delivery_mst where id=$data and entry_form!=85 ");
	foreach($sql_mst as $row)
	{
		echo "$('#txt_challan_no').val('".$row[csf('sys_number')]."');\n";
		echo "$('#txt_prev_mst_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf('company_id')].");\n";
		echo "$('#cbo_location_name').val(".$row[csf('location_id')].");\n";
		//echo "$('#txt_challan_no').val('".$row[csf('challan_no')]."');\n";
		echo "$('#cbo_transport_company').val(".$row[csf('transport_supplier')].");\n";
		echo "$('#txt_ex_factory_date').val('".change_date_format($row[csf('delivery_date')])."');\n";
		echo "$('#txt_truck_no').val('".$row[csf('truck_no')]."');\n";
		echo "$('#txt_lock_no').val('".$row[csf('lock_no')]."');\n";
		echo "$('#txt_driver_name').val('".$row[csf('driver_name')]."');\n";
		echo "$('#txt_dl_no').val('".$row[csf('dl_no')]."');\n";
		echo "$('#txt_mobile_no').val('".$row[csf('mobile_no')]."');\n";
		echo "$('#txt_do_no').val('".$row[csf('do_no')]."');\n";
		echo "$('#txt_gp_no').val('".$row[csf('gp_no')]."');\n";
		echo "$('#txt_destination').val('".$row[csf('destination_place')]."');\n";
		echo "$('#cbo_forwarder').val(".$row[csf('forwarder')].");\n";
		echo  "$('#cbo_transport_company').attr('disabled',true);\n";
		echo  "$('#cbo_forwarder').attr('disabled',true);\n";
		//echo "set_button_status(0, permission, 'fnc_exFactory_entry',1,0);\n";
	}
	exit();
}

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$job_library=return_library_array( "select id,job_no_mst from wo_po_break_down", "id", "job_no_mst"  );
$color_library=return_library_array( "select id,color_name from  lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

if($action=="show_dtls_listview_mst")
{
	$data=explode("_",$data);
	$variable_settings_production = $data[2];
	if($db_type==2) $grp_concat2="LISTAGG(CAST(id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY id) as mst_id";
	else if($db_type==0) $grp_concat2="group_concat(id) as mst_id";
	$mst_id=return_field_value("$grp_concat2","pro_ex_factory_delivery_mst","sys_number='$data[1]' and entry_form=85 ","mst_id");
	if($mst_id=="") $mst_ids=" and a.id in(0)";else $mst_ids=" and a.id in($mst_id)";

	if($variable_settings_production !=1) // color size level
	{
		$sql_data= "SELECT b.id as dtls_mst_id,c.id as dtls_id,b.po_break_down_id as po_id,b.item_number_id,c.color_size_break_down_id as color_mst_id ,b.country_id,d.size_number_id,d.color_number_id,sum(case when   a.entry_form=85 and a.challan_no='$data[1]' then c.production_qnty else 0 end ) as prod_qty,sum(case when   a.entry_form<>85 and a.sys_number='$data[1]' then c.production_qnty else 0 end ) as tot_delv_qty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b ,pro_ex_factory_dtls c,wo_po_color_size_breakdown d
		where a.id=b.delivery_mst_id  and b.id=c.mst_id and d.id=c.color_size_break_down_id and b.po_break_down_id=d.po_break_down_id and a.status_active=1    and a.is_deleted=0 and( a.challan_no='$data[1]' or   a.sys_number='$data[1]')
		group by b.po_break_down_id,b.item_number_id,c.color_size_break_down_id,b.country_id,b.id,c.id,d.size_number_id,d.color_number_id order by b.po_break_down_id";
		$result=sql_select($sql_data);
		$po_color_arr=array();
		foreach($result as $row)
		{
			$po_color_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty']+=$row[csf('prod_qty')];
			$po_color_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['tot_delv_qty']+=$row[csf('tot_delv_qty')];

		}


		$sql_gmt= "SELECT a.id as mst_id,b.id as dtls_mst_id,c.id as dtls_id,b.po_break_down_id as po_id,b.item_number_id,c.color_size_break_down_id as color_mst_id ,b.country_id,d.size_number_id,d.color_number_id,sum(case when b.entry_form<>85 then  c.production_qnty else 0 end ) as prod_qty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b ,pro_ex_factory_dtls c,wo_po_color_size_breakdown d
		where a.id=b.delivery_mst_id  and b.id=c.mst_id and d.id=c.color_size_break_down_id and b.po_break_down_id=d.po_break_down_id and a.status_active=1 and a.id=$data[0] and a.is_deleted=0
		group by  a.id,b.po_break_down_id,b.item_number_id,c.color_size_break_down_id,b.country_id,b.id,c.id,d.size_number_id,d.color_number_id order by b.po_break_down_id";
		$sql_result=sql_select($sql_gmt);
		$k=1;
		foreach($sql_result as $row)
		{
			$color_name=$color_library[$row[csf('color_number_id')]];
			$color_size=$size_library[$row[csf('size_number_id')]];
			 // echo  $previous_prod_qty2;
			  $buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='$data[1]' and entry_form!=85 ","buyer_id");

			 $previous_prod_qty=$po_color_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty'];
			 $return_qty=$row[csf('prod_qty')]-($previous_prod_qty);
			 $return_qty2=$row[csf('prod_qty')];
			 $hidden_tot_qty=$row[csf('prod_qty')];
			 $tot_qntys=$po_color_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['tot_delv_qty']-($previous_prod_qty);

			// echo $row[csf('prod_qty')].'='.$previous_prod_qty.'='.$previous_prod_qty2;
		?>
			<tr class="" id="tr_<? echo $k; ?>">
	            <td> <? echo $k; ?></td>
	            <td><? echo create_drop_down( "cbobuyer_".$k, 110,"select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id,buyer_name",1, "-- Select --",$buyer_id , "1",1,1 ); ?>
	            </td>
	            <td>
	            <input type="text" name="txtjob[]" id="txtjob_<? echo $k; ?>" class="text_boxes" style="width:110px;" value="<? echo $job_library[$row[csf('po_id')]];?>" disabled >
	            </td>
	            <td>
	            <input type="text" name="txtorder[]" id="txtorder_<? echo $k; ?>" class="text_boxes"   value="<? echo $order_num_arr[$row[csf('po_id')]];?>" style="width:90px;" disabled />
	            </td>
	            <td> <?
	                   echo create_drop_down( "cbo_item_name_".$k, 90, $garments_item,"", 1, "-- Select Item --", $row[csf('item_number_id')], "",1,0 );	 			?>
	             </td>
	            <td>
	           	 <?
	                   echo create_drop_down( "cbo_country_name_".$k, 90, "select id,country_name from lib_country where status_active=1 and is_deleted=0","id,country_name", 1, "-- Select Country --", $row[csf('country_id')], "",1,0 );	 		?>
	            </td>
	            <td>
	            <input type="text" name="txtcolor[]" id="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_name;?>"    style="width:85px" readonly placeholder="Display" disabled />
	            </td>
	            <td>
	            <input type="text" name="txtsize[]" id="txtsize_<? echo $k; ?>" class="text_boxes" style="width:75px" value="<? echo $color_size;?>" readonly disabled />

	            </td>
	            <td>
	            <input type="text" name="txtexfactoryqty[]" id="txtexfactoryqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $return_qty2;?>" readonly disabled />



	            </td>
	            <td>
	            <input type="text" name="txtreturnqty[]" id="txtreturnqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px"  onBlur="return_prod_qty_row(this.id);"  value="" placeholder="<? echo $tot_qntys;?>">
	            	<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $k; ?>"  value="<? echo $row[csf('dtls_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="colormstid[]" id="colormstid_<? echo $k; ?>"  value="<? echo $row[csf('color_mst_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="dtlsmstid[]" id="dtlsmstid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  value="<? echo $row[csf('dtls_mst_id')];?>">
	                <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  >
	            </td>


			</tr>
		<?
		$k++;
		}
		exit();
	}
	else // gross level
	{
		$sql_data= "SELECT b.id as dtls_mst_id,b.po_break_down_id as po_id,b.item_number_id,b.country_id,sum(case when a.entry_form=85 and a.challan_no='$data[1]' then b.ex_factory_qnty else 0 end ) as prod_qty,sum(case when a.entry_form<>85 and a.sys_number='$data[1]' then b.ex_factory_qnty else 0 end ) as tot_delv_qty from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and( a.challan_no='$data[1]' or a.sys_number='$data[1]') group by b.po_break_down_id,b.item_number_id,b.country_id,b.id order by b.po_break_down_id"; 
		$result=sql_select($sql_data);
		$po_color_arr=array();
		foreach($result as $row)
		{
			$po_color_arr[$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty']+=$row[csf('prod_qty')];
			$po_color_arr[$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['tot_delv_qty']+=$row[csf('tot_delv_qty')];

		}


		$sql_gmt= "SELECT a.id as mst_id,b.id as dtls_mst_id,b.po_break_down_id as po_id,b.item_number_id,b.country_id,sum(case when b.entry_form<>85 then  b.ex_factory_qnty else 0 end ) as prod_qty from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.id=$data[0] and a.is_deleted=0 group by  a.id,b.po_break_down_id,b.item_number_id,b.country_id,b.id order by b.po_break_down_id"; 
		$sql_result=sql_select($sql_gmt);
		$k=1;
		foreach($sql_result as $row)
		{			
			 $buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='$data[1]' and entry_form!=85 ","buyer_id");

			 $previous_prod_qty=$po_color_arr[$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty'];
			 $return_qty=$row[csf('prod_qty')]-($previous_prod_qty);
			 $return_qty2=$row[csf('prod_qty')];
			 $hidden_tot_qty=$row[csf('prod_qty')];
			 $tot_qntys=$po_color_arr[$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['tot_delv_qty']-($previous_prod_qty);

			// echo $row[csf('prod_qty')].'='.$previous_prod_qty.'='.$previous_prod_qty2;
		?>
			<tr class="" id="tr_<? echo $k; ?>">
	            <td> <? echo $k; ?></td>
	            <td><? echo create_drop_down( "cbobuyer_".$k, 110,"select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id,buyer_name",1, "-- Select --",$buyer_id , "1",1,1 ); ?>
	            </td>
	            <td>
	            <input type="text" name="txtjob[]" id="txtjob_<? echo $k; ?>" class="text_boxes" style="width:110px;" value="<? echo $job_library[$row[csf('po_id')]];?>" disabled >
	            </td>
	            <td>
	            <input type="text" name="txtorder[]" id="txtorder_<? echo $k; ?>" class="text_boxes"   value="<? echo $order_num_arr[$row[csf('po_id')]];?>" style="width:90px;" disabled />
	            </td>
	            <td> <?
	                   echo create_drop_down( "cbo_item_name_".$k, 90, $garments_item,"", 1, "-- Select Item --", $row[csf('item_number_id')], "",1,0 );	 			?>
	             </td>
	            <td>
	           	 <?
	                   echo create_drop_down( "cbo_country_name_".$k, 90, "select id,country_name from lib_country where status_active=1 and is_deleted=0","id,country_name", 1, "-- Select Country --", $row[csf('country_id')], "",1,0 );	 		?>
	            </td>
	            <td>
	            <input type="text" name="txtcolor[]" id="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_name;?>"    style="width:85px" readonly disabled />
	            </td>
	            <td>
	            <input type="text" name="txtsize[]" id="txtsize_<? echo $k; ?>" class="text_boxes" style="width:75px" value="<? echo $color_size;?>" readonly disabled />

	            </td>
	            <td>
	            <input type="text" name="txtexfactoryqty[]" id="txtexfactoryqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $return_qty2;?>" readonly disabled />



	            </td>
	            <td>
	            <input type="text" name="txtreturnqty[]" id="txtreturnqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px"  onBlur="return_prod_qty_row(this.id);"  value="" placeholder="<? echo $tot_qntys;?>">
	            	<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $k; ?>"  value="<? echo $row[csf('dtls_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="colormstid[]" id="colormstid_<? echo $k; ?>"  value="<? echo $row[csf('color_mst_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="dtlsmstid[]" id="dtlsmstid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  value="<? echo $row[csf('dtls_mst_id')];?>">
	                <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  >
	            </td>


			</tr>
		<?
		$k++;
		}
		exit();
	}
}

if($action=="show_dtls_listview_mst2")
{

	$data=explode("_",$data);
	$variable_settings_production = $data[2];

	if($variable_settings_production !=1) // for color and size level
	{
		$sql_data= "SELECT b.id as dtls_mst_id,c.id as dtls_id,b.po_break_down_id as po_id,b.item_number_id,c.color_size_break_down_id as color_mst_id ,b.country_id,d.size_number_id,d.color_number_id,sum(c.production_qnty) as prod_qty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b ,pro_ex_factory_dtls c,wo_po_color_size_breakdown d
		where a.id=b.delivery_mst_id  and b.id=c.mst_id and d.id=c.color_size_break_down_id and b.po_break_down_id=d.po_break_down_id and a.status_active=1 and a.sys_number='$data[1]' and a.entry_form!=85  and a.is_deleted=0
		group by  b.po_break_down_id,b.item_number_id,c.color_size_break_down_id,b.country_id,b.id,c.id,d.size_number_id,d.color_number_id order by b.po_break_down_id";
		$result=sql_select($sql_data);
		$po_color_arr=array();
		foreach($result as $row)
		{
			$po_color_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty']=$row[csf('prod_qty')];

		}
		if($db_type==2) $grp_concat2="LISTAGG(CAST(a.id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as mst_id";
		else if($db_type==0) $grp_concat2="group_concat(a.id) as mst_id";
		 $sql_data2= "SELECT a.id as mst_id,b.id as dtls_mst_id,c.id as dtls_id,b.po_break_down_id as po_id,b.item_number_id,c.color_size_break_down_id as color_mst_id ,b.country_id,d.size_number_id,d.color_number_id,sum(c.production_qnty) as prod_qty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b ,pro_ex_factory_dtls c,wo_po_color_size_breakdown d
		where a.id=b.delivery_mst_id  and b.id=c.mst_id and d.id=c.color_size_break_down_id and b.po_break_down_id=d.po_break_down_id and a.status_active=1 and a.challan_no='$data[1]'  and a.entry_form=85  and a.is_deleted=0
		group by  a.id,b.po_break_down_id,b.item_number_id,c.color_size_break_down_id,b.country_id,b.id,c.id,d.size_number_id,d.color_number_id order by b.po_break_down_id";
		$result2=sql_select($sql_data2);
		$po_color_arr2=array();$po_color_arr3=array();
		foreach($result2 as $row)
		{
			$po_color_arr2[$row[csf('mst_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty']=$row[csf('prod_qty')];
		}
		$sql_data3= "SELECT $grp_concat2,b.po_break_down_id as po_id ,b.country_id
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and  a.status_active=1 and a.challan_no='$data[1]'  and a.entry_form=85  and a.is_deleted=0
		group by  b.po_break_down_id,b.country_id";
		$result3=sql_select($sql_data3);
		foreach($result3 as $row)
		{
			$po_color_arr3[$row[csf('po_id')]][$row[csf('country_id')]]['mst_id']=$row[csf('mst_id')];
		}
		//print_r($po_color_arr3);
		//echo $sys_number_id;
		 $sql_gmt= "SELECT a.id as mst_id,b.id as dtls_mst_id,c.id as dtls_id,b.po_break_down_id as po_id,b.item_number_id,c.color_size_break_down_id as color_mst_id ,b.country_id,d.size_number_id,d.color_number_id,sum(c.production_qnty) as prod_qty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b ,pro_ex_factory_dtls c,wo_po_color_size_breakdown d
		where a.id=b.delivery_mst_id  and b.id=c.mst_id and d.id=c.color_size_break_down_id and b.po_break_down_id=d.po_break_down_id and a.status_active=1 and a.id=$data[0] and a.is_deleted=0
		group by a.id,  b.po_break_down_id,b.item_number_id,c.color_size_break_down_id,b.country_id,b.id,c.id,d.size_number_id,d.color_number_id order by b.po_break_down_id";
		$sql_result=sql_select($sql_gmt);
		$k=1;
		foreach($sql_result as $row)
		{
			$color_name=$color_library[$row[csf('color_number_id')]];
			$color_size=$size_library[$row[csf('size_number_id')]];
			 $previous_prod_qty=$po_color_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty'];

			  $mst_id=array_unique(explode(",",$po_color_arr3[$row[csf('po_id')]][$row[csf('country_id')]]['mst_id']));
			  //print_r($mst_id);
			  $previous_prod_qty2=0;
			  foreach($mst_id as $id)
			  {
				  if($id!=$row[csf('mst_id')])
				  {
			  $previous_prod_qty2=$po_color_arr2[$id][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty'];
				  }
			  }
			 // echo  $previous_prod_qty2;

			 $buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='$data[1]' and entry_form!=85 ","buyer_id");
			 $return_qty=$previous_prod_qty-($row[csf('prod_qty')]+$previous_prod_qty2);
			 $return_qty2=$previous_prod_qty;
			// echo $previous_prod_qty.'='.$previous_prod_qty2.'++'.$row[csf('prod_qty')];
		?>
			<tr class="" id="tr_<? echo $k; ?>">
	            <td> <? echo $k; ?></td>
	            <td><? echo create_drop_down( "cbobuyer_".$k, 110,"select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id,buyer_name",1, "-- Select --",$buyer_id , "1",1,1 ); ?>
	            </td>
	            <td>
	            <input type="text" name="txtjob[]" id="txtjob_<? echo $k; ?>" class="text_boxes" style="width:110px;" value="<? echo $job_library[$row[csf('po_id')]];?>" disabled >
	            </td>
	            <td>
	            <input type="text" name="txtorder[]" id="txtorder_<? echo $k; ?>" class="text_boxes"   value="<? echo $order_num_arr[$row[csf('po_id')]];?>" style="width:90px;" disabled />
	            </td>
	            <td> <?
	                   echo create_drop_down( "cbo_item_name_".$k, 90, $garments_item,"", 1, "-- Select Item --", $row[csf('item_number_id')], "",1,0 );	 			?>
	             </td>
	            <td>
	           	 <?
	                   echo create_drop_down( "cbo_country_name_".$k, 90, "select id,country_name from lib_country where status_active=1 and is_deleted=0","id,country_name", 1, "-- Select Country --", $row[csf('country_id')], "",1,0 );	 		?>
	            </td>
	            <td>
	            <input type="text" name="txtcolor[]" id="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_name;?>"    style="width:85px" readonly placeholder="Display" disabled />
	            </td>
	            <td>
	            <input type="text" name="txtsize[]" id="txtsize_<? echo $k; ?>" class="text_boxes" style="width:75px" value="<? echo $color_size;?>" readonly disabled />

	            </td>
	            <td>
	            <input type="text" name="txtexfactoryqty[]" id="txtexfactoryqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $return_qty2;?>" readonly disabled />

	            </td>
	            <td>
	            <input type="text" placeholder="<? echo $return_qty+$row[csf('prod_qty')];?>" name="txtreturnqty[]" id="txtreturnqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px" onBlur="return_prod_qty_row(this.id);"  value="<? echo $row[csf('prod_qty')];?>">
	            	<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $k; ?>"  value="<? echo $row[csf('dtls_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="colormstid[]" id="colormstid_<? echo $k; ?>"  value="<? echo $row[csf('color_mst_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="dtlsmstid[]" id="dtlsmstid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  value="<? echo $row[csf('dtls_mst_id')];?>">
	                <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  >
	            </td>


			</tr>
		<?
		$k++;
		}
		exit();
	}
	else // for gross level 
	{
		$sql_data= "SELECT b.id as dtls_mst_id,b.po_break_down_id as po_id,b.item_number_id,b.country_id,sum(b.ex_factory_qnty) as prod_qty from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.sys_number='$data[1]' and a.entry_form!=85  and a.is_deleted=0 group by  b.po_break_down_id,b.item_number_id,b.country_id,b.id order by b.po_break_down_id"; 
		$result=sql_select($sql_data);
		$po_color_arr=array();
		foreach($result as $row)
		{
			$po_color_arr[$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty']=$row[csf('prod_qty')];

		}
		if($db_type==2) $grp_concat2="LISTAGG(CAST(a.id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as mst_id";
		else if($db_type==0) $grp_concat2="group_concat(a.id) as mst_id";

		$sql_data2= "SELECT a.id as mst_id,b.id as dtls_mst_id,b.po_break_down_id as po_id,b.item_number_id,b.country_id,sum(b.ex_factory_qnty) as prod_qty from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.challan_no='$data[1]'  and a.entry_form=85  and a.is_deleted=0 group by  a.id,b.po_break_down_id,b.item_number_id,b.country_id,b.id order by b.po_break_down_id"; 
		$result2=sql_select($sql_data2);
		$po_color_arr2=array();$po_color_arr3=array();
		foreach($result2 as $row)
		{
			$po_color_arr2[$row[csf('mst_id')]][$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty']=$row[csf('prod_qty')];
		}
		$sql_data3= "SELECT $grp_concat2,b.po_break_down_id as po_id ,b.country_id
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and  a.status_active=1 and a.challan_no='$data[1]'  and a.entry_form=85  and a.is_deleted=0
		group by  b.po_break_down_id,b.country_id";
		$result3=sql_select($sql_data3);
		foreach($result3 as $row)
		{
			$po_color_arr3[$row[csf('po_id')]][$row[csf('country_id')]]['mst_id']=$row[csf('mst_id')];
		}
		//print_r($po_color_arr3);
		//echo $sys_number_id;
		 $sql_gmt= "SELECT a.id as mst_id,b.id as dtls_mst_id,b.po_break_down_id as po_id,b.item_number_id,b.country_id,sum(b.ex_factory_qnty) as prod_qty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
		where a.id=b.delivery_mst_id  and a.status_active=1 and a.id=$data[0] and a.is_deleted=0
		group by a.id,  b.po_break_down_id,b.item_number_id,b.country_id,b.id order by b.po_break_down_id";
		$sql_result=sql_select($sql_gmt);
		$k=1;
		foreach($sql_result as $row)
		{
			$color_name=$color_library[$row[csf('color_number_id')]];
			$color_size=$size_library[$row[csf('size_number_id')]];
			 $previous_prod_qty=$po_color_arr[$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty'];

			  $mst_id=array_unique(explode(",",$po_color_arr3[$row[csf('po_id')]][$row[csf('country_id')]]['mst_id']));
			  //print_r($mst_id);
			  $previous_prod_qty2=0;
			  foreach($mst_id as $id)
			  {
				  if($id!=$row[csf('mst_id')])
				  {
			  $previous_prod_qty2=$po_color_arr2[$id][$row[csf('item_number_id')]][$row[csf('po_id')]][$row[csf('country_id')]]['qty'];
				  }
			  }
			 // echo  $previous_prod_qty2;

			 $buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='$data[1]' and entry_form!=85 ","buyer_id");
			 $return_qty=$previous_prod_qty-($row[csf('prod_qty')]+$previous_prod_qty2);
			 $return_qty2=$previous_prod_qty;
			// echo $previous_prod_qty.'='.$previous_prod_qty2.'++'.$row[csf('prod_qty')];
		?>
			<tr class="" id="tr_<? echo $k; ?>">
	            <td> <? echo $k; ?></td>
	            <td><? echo create_drop_down( "cbobuyer_".$k, 110,"select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id,buyer_name",1, "-- Select --",$buyer_id , "1",1,1 ); ?>
	            </td>
	            <td>
	            <input type="text" name="txtjob[]" id="txtjob_<? echo $k; ?>" class="text_boxes" style="width:110px;" value="<? echo $job_library[$row[csf('po_id')]];?>" disabled >
	            </td>
	            <td>
	            <input type="text" name="txtorder[]" id="txtorder_<? echo $k; ?>" class="text_boxes"   value="<? echo $order_num_arr[$row[csf('po_id')]];?>" style="width:90px;" disabled />
	            </td>
	            <td> <?
	                   echo create_drop_down( "cbo_item_name_".$k, 90, $garments_item,"", 1, "-- Select Item --", $row[csf('item_number_id')], "",1,0 );	 			?>
	             </td>
	            <td>
	           	 <?
	                   echo create_drop_down( "cbo_country_name_".$k, 90, "select id,country_name from lib_country where status_active=1 and is_deleted=0","id,country_name", 1, "-- Select Country --", $row[csf('country_id')], "",1,0 );	 		?>
	            </td>
	            <td>
	            <input type="text" name="txtcolor[]" id="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_name;?>"    style="width:85px" readonly disabled />
	            </td>
	            <td>
	            <input type="text" name="txtsize[]" id="txtsize_<? echo $k; ?>" class="text_boxes" style="width:75px" value="<? echo $color_size;?>" readonly disabled />

	            </td>
	            <td>
	            <input type="text" name="txtexfactoryqty[]" id="txtexfactoryqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $return_qty2;?>" readonly disabled />

	            </td>
	            <td>
	            <input type="text" placeholder="<? echo $return_qty+$row[csf('prod_qty')];?>" name="txtreturnqty[]" id="txtreturnqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:80px" onBlur="return_prod_qty_row(this.id);"  value="<? echo $row[csf('prod_qty')];?>">
	            	<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $k; ?>"  value="<? echo $row[csf('dtls_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="colormstid[]" id="colormstid_<? echo $k; ?>"  value="<? echo $row[csf('color_mst_id')];?>" class="text_boxes" style="width:80px" readonly />
	                <input type="hidden" name="dtlsmstid[]" id="dtlsmstid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  value="<? echo $row[csf('dtls_mst_id')];?>">
	                <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_<? echo $k; ?>" class="text_boxes"  style="width:50px" readonly  >
	            </td>


			</tr>
		<?
		$k++;
		}
		exit();
	}
}


if($action=="challan_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });

		function search_populate(str)
		{
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?

					$buyer_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name",'id','buyer_name');


				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}
		}
	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
  		parent.emailwindow.hide();
 	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
	    		<tr>
	        		<td align="center" width="100%">
	                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	                   		 <thead>
	                        	<th width="130">Search By</th>
	                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
	                        	<th width="200">Date Range</th>
	                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	                    	</thead>
	        				<tr>
	                    		<td width="130">
								<?
								$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Actual PO No");
								echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
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
	                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'garments_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                            </td>
	        				</tr>
	             		</table>
	          		</td>
	        	</tr>
	        	<tr>
	            	<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_mst_id">
	                    <input type="hidden" id="hidden_grmtItem_id">
	                    <input type="hidden" id="hidden_po_qnty">
	                    <input type="hidden" id="hidden_country_id">
	          		</td>
	            </tr>
	    	</table>
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
		else if(trim($txt_search_by)==3)
			$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}


	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	if(trim($txt_search_by)==3 && trim($txt_search_common)!="")
	{
		$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut
		from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
		where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and
			c.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond";
	}
	else
	{
		$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut from wo_po_details_master a, wo_po_break_down b  where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond";
	}

	//echo $sql;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');



	/*if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}*/


	$po_country_sql=sql_select("SELECT po_break_down_id, country_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
	foreach ($po_country_sql as $key => $value)
	{
		if($po_country_arr[$value[csf("po_break_down_id")]]=="")
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=$value[csf("country_id")];

		}
		else
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=','.$value[csf("country_id")];
		}


	}



	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}


	$total_ex_fac_data_arr=array();
	$total_ex_fac_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
	foreach($total_ex_fac_arr as $row)
	{
		$total_ex_fac_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
	}
	?>
	<div style="width:1030px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Ex-factory Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1030px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));

				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" >
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf("shipment_date")]);?></td>
								<td width="100"><p><? echo $row[csf("po_number")]; ?></p></td>
								<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
								<td width="120"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="140"><p><?  echo $garments_item[$grmts_item];?></p></td>
								<td width="100"><p><? echo $country_library[$country_id]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                                <td width="80" align="right">
								<?
								echo $total_cut_qty=$total_ex_fac_data_arr[$row[csf('id')]][$grmts_item][$country_id];
                                 ?> &nbsp;
                               </td>
                               <td width="80" align="right">
                                <?
                                 $balance=$po_qnty-$total_cut_qty;
                                 echo $balance;
                                 ?>&nbsp;
                               </td>
								<td><?  echo $company_arr[$row[csf("company_name")]];?> </td>
							</tr>
						<?
						$i++;
					}
				}
            }
   		?>
        </table>
    </div>
	<?
exit();
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];

	$res = sql_select("select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name,a.shipment_date   from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id=$po_id");

 	foreach($res as $result)
	{
		echo "$('#txt_order_qty').val('".$result[csf('po_quantity')]."');\n";
		echo "$('#cbo_item_name').val(".$item_id.");\n";
		echo "$('#cbo_country_name').val(".$country_id.");\n";


		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_shipment_date').val('".change_date_format($result[csf('shipment_date')])."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";

		$finish_qty = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and production_type=8 and country_id='$country_id' and status_active=1 and is_deleted=0");
 		if($finish_qty=="")$finish_qty=0;

		$total_produced = return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0");
		if($total_produced=="")$total_produced=0;

 		echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
		$yet_to_produced = $finish_qty-$total_produced;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
  	}
 	exit();
}


// Ex factory Return
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$sewing_production_variable = str_replace("'","",$sewing_production_variable);

  		$buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number=$txt_challan_no and entry_form!=85 ","buyer_id");
		if(str_replace("'","",$txt_return_id)=="")
		{

			$return_mst_id=return_next_id("id", "pro_ex_factory_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GDER', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from pro_ex_factory_delivery_mst where company_id=$cbo_company_name and   entry_form=85 $mrr_cond order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));

			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id,buyer_id, location_id, challan_no,transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no, mobile_no, do_no, gp_no, destination_place, forwarder,entry_form,inserted_by,insert_date";
			$data_array_delivery="(".$return_mst_id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$buyer_id.",".$cbo_location_name.",".$txt_challan_no.",".$cbo_transport_company.",".$txt_return_date.",".$txt_lock_no.",".$txt_driver_name.",".$txt_truck_no.",".$txt_dl_no.",".$txt_mobile_no.",".$txt_do_no.",".$txt_gp_no.",".$txt_destination.",".$cbo_forwarder.",85,".$user_id.",'".$pc_date_time."')";
			//$mrr_no=$new_sys_number[0];
			$mrr_no=$new_sys_number[0];

		}
		else
		{
			$return_mst_id=str_replace("'","",$txt_return_id);
			$mrr_no=str_replace("'","",$txt_return_no);
			//$mrr_no_challan=str_replace("'","",$txt_challan_no);

			$field_array_delivery="company_id*buyer_id*location_id*buyer_id*transport_supplier*delivery_date*lock_no*driver_name*truck_no*dl_no*mobile_no*do_no*gp_no*destination_place*forwarder*updated_by*update_date";
			$data_array_delivery="".$cbo_company_name."*".$buyer_id."*".$cbo_location_name."*".$cbo_transport_company."*".$txt_return_date."*".$txt_lock_no."*".$txt_driver_name."*".$txt_truck_no."*".$txt_dl_no."*".$txt_mobile_no."*".$txt_do_no."*".$txt_gp_no."*".$txt_destination."*".$cbo_forwarder."*".$user_id."*'".$pc_date_time."'";
		}
		 $sql=sql_select("select b.id,b.ex_factory_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where  a.id=b.delivery_mst_id and b.delivery_mst_id=$txt_prev_mst_id");
		$presentStock_qty_arr=array();
		//$product_name_details="";
		foreach($sql as $row)
		{
			$presentStock_qty_arr[$row[csf('id')]]=$row[csf("ex_factory_qnty")];

		}
		//print_r($presentStock_qty_arr);die;
		//$dltreturnid=explode(",",$dtls_return_id);

		//print_r($mst_return_qty_arr);die;
		$field_array_dtls="id,delivery_mst_id,garments_nature,po_break_down_id,item_number_id,country_id,location,ex_factory_date,ex_factory_qnty,total_carton_qnty,entry_form,challan_no,invoice_no,lc_sc_no,carton_qnty,transport_com,remarks,shiping_status,entry_break_down_type,inspection_qty_validation,status_active,is_deleted,inserted_by,insert_date";
		$mst_dtls_arr=("SELECT id,delivery_mst_id,garments_nature,po_break_down_id,item_number_id,country_id,location,ex_factory_date,ex_factory_qnty,total_carton_qnty,entry_form,challan_no,invoice_no,lc_sc_no,carton_qnty,transport_com,remarks,shiping_status,entry_break_down_type,inspection_qty_validation,status_active,is_deleted from pro_ex_factory_mst where  delivery_mst_id=$txt_prev_mst_id  order by id" );
		$resutl=sql_select($mst_dtls_arr);
		$mst_dtls_id=return_next_id("id", "pro_ex_factory_mst", 1);

		$data_array_dtls="";$j=0;//$dtls_return_id=="";
		$return_actual_ret_qty='';$delivery_qty=0;
		foreach($resutl  as $row)
		{
			$mst_id_arr[$row[csf('id')]]=$mst_dtls_id;

			if($j==0) $data_array_dtls= "(".$mst_dtls_id.",".$return_mst_id.",'".$row[csf('garments_nature')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_number_id')]."','".$row[csf('country_id')]."','".$row[csf('location')]."',".$txt_return_date.",'".$mst_return_qty_arr[$row[csf('id')]]."','".$row[csf('total_carton_qnty')]."',85,".$txt_challan_no.",'".$row[csf('invoice_no')]."','".$row[csf('lc_sc_no')]."','".$row[csf('carton_qnty')]."','".$row[csf('transport_com')]."','".$row[csf('remarks')]."','".$row[csf('shiping_status')]."','".$row[csf('entry_break_down_type')]."','".$row[csf('inspection_qty_validation')]."','".$row[csf('status_active')]."','".$row[csf('is_deleted')]."',".$user_id.",'".$pc_date_time."')";
			else
			 $data_array_dtls.=",(".$mst_dtls_id.",".$return_mst_id.",'".$row[csf('garments_nature')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_number_id')]."','".$row[csf('country_id')]."','".$row[csf('location')]."',".$txt_return_date.",'".$mst_return_qty_arr[$row[csf('id')]]."','".$row[csf('total_carton_qnty')]."',85,".$txt_challan_no.",'".$row[csf('invoice_no')]."','".$row[csf('lc_sc_no')]."','".$row[csf('carton_qnty')]."','".$row[csf('transport_com')]."','".$row[csf('remarks')]."','".$row[csf('shiping_status')]."','".$row[csf('entry_break_down_type')]."','".$row[csf('inspection_qty_validation')]."','".$row[csf('status_active')]."','".$row[csf('is_deleted')]."',".$user_id.",'".$pc_date_time."')";

			 $mst_dtls_id=$mst_dtls_id+1;
 				$j++;

		}
		///echo "INSERT INTO pro_ex_factory_mst (".$field_array_dtls.") VALUES ".$data_array_dtls;die;
		// pro_ex_factory_dtls table entry here ----------------------------------///

		$mst_return_qty_arr=array();
		for($i=1; $i<=$row_num; $i++)
		{
			$txtreturnqty="txtreturnqty_".$i;
			$update_mst_id="dtlsmstid_".$i;
			$mst_dtld_update_id=str_replace("'","",$$update_mst_id);
			$mst_return_qty_arr[$mst_id_arr[$mst_dtld_update_id]]+=str_replace("'","",$$txtreturnqty);
		}


		$field_array_dtls_up="ex_factory_qnty";
		$field_array_dtls2="id,mst_id,color_size_break_down_id,production_qnty";
		$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
		//$dltreturnid=explode(",",$dtls_return_id);
		$add_comma=0;$return_ids=="";
		for($i=1; $i<=$row_num; $i++)
		{
			$wo_po_color_mst_id="colormstid_".$i;
			$txtreturnqty="txtreturnqty_".$i;
			$txtexfactoryqty="txtexfactoryqty_".$i;
			$txtsize="txtsize_".$i;
			$txtcolor="txtcolor_".$i;
			//$cbo_country_name="cbo_country_name_".$i;
			$update_details_id="updatedtlsid_".$i;
			$update_mst_id="dtlsmstid_".$i;
			$mst_dtld_update_id=str_replace("'","",$$update_mst_id);
			//echo $mst_id_arr[$mst_dtld_update_id];
			//echo $delivery_qty=$presentStock_qty_arr[$mst_id_arr[$mst_dtld_update_id]];
			 $return_actual_ret_qty=$delivery_qty-$$txtreturnqty;
			//echo "0**".$$update_mst_id; die;
			if($return_ids=="") $return_ids=$dtls_id; else $return_ids.=",".$dtls_id;
				if ($add_comma!=0) $data_array_dtls2 .=",";
				$data_array_dtls2.="(".$dtls_id.",".$mst_id_arr[$mst_dtld_update_id].",".$$wo_po_color_mst_id.",".$$txtreturnqty.")";

				$dtls_id=$dtls_id+1;
				$add_comma++;


		}
		foreach($mst_return_qty_arr as $update_key=>$val_qty)
		{
			$updateID_array[]=str_replace("'",'',$update_key);
			$data_array_dtls_up[str_replace("'",'',$update_key)]=explode("*",("".$val_qty.""));
		}
		// echo "0**INSERT INTO pro_ex_factory_dtls (".$field_array_dtls2.") VALUES ".$data_array_dtls2;die;
		$rID=sql_insert("pro_ex_factory_mst",$field_array_dtls,$data_array_dtls,1);
		$DeliveryrID=true;
		if(str_replace("'","",$txt_return_id)=="")
		{
			$DeliveryrID=sql_insert("pro_ex_factory_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$DeliveryrID=sql_update("pro_ex_factory_delivery_mst",$field_array_delivery,$data_array_delivery,"id",str_replace("'","",$txt_return_id),1);
		}
		$dtlsrID=true;
		$dtlsrID=execute_query(bulk_update_sql_statement("pro_ex_factory_mst","id",$field_array_dtls_up,$data_array_dtls_up,$updateID_array),1);

		$rID_dtls=true;
		if($sewing_production_variable !=1) // when color size level 
		{
			$rID_dtls=sql_insert("pro_ex_factory_dtls",$field_array_dtls2,$data_array_dtls2,1);
		}
		//echo "10**".$rID."**".$DeliveryrID."**".$rID_dtls;die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
				if($rID && $DeliveryrID  && $rID_dtls && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$return_mst_id)."**".$mrr_no."**".str_replace("'","",$txt_prev_mst_id)."**".str_replace("'","",$return_ids)."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$return_mst_id);
				}
		}

		if($db_type==2 || $db_type==1 )
		{
				if($rID && $DeliveryrID && $rID_dtls && $dtlsrID)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$return_mst_id)."**".$mrr_no."**".str_replace("'","",$txt_prev_mst_id)."**".str_replace("'","",$return_ids)."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$return_mst_id);
				}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }


		$delivery_mst_id=str_replace("'","",$txt_return_id);
		$mrr_no=str_replace("'","",$txt_return_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		//$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$sewing_production_variable = str_replace("'","",$sewing_production_variable);

		$field_array_delivery="company_id*location_id*transport_supplier*delivery_date*lock_no*driver_name*truck_no*dl_no*mobile_no*do_no*gp_no*destination_place*forwarder*updated_by*update_date";
			$data_array_delivery="".$cbo_company_name."*".$cbo_location_name."*".$cbo_transport_company."*".$txt_return_date."*".$txt_lock_no."*".$txt_driver_name."*".$txt_truck_no."*".$txt_dl_no."*".$txt_mobile_no."*".$txt_do_no."*".$txt_gp_no."*".$txt_destination."*".$cbo_forwarder."*".$user_id."*'".$pc_date_time."'";

		$sql=sql_select("select b.id,b.ex_factory_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where  a.id=b.delivery_mst_id and b.delivery_mst_id=$delivery_mst_id and a.entry_form=85");
		$presentStock_qty_arr=array();
		//$product_name_details="";
		foreach($sql as $row)
		{
			$presentStock_qty_arr[$row[csf('id')]]=$row[csf("ex_factory_qnty")];

		}
		$field_array_dtls_up2="ex_factory_qnty";
		$mst_return_qty_arr=array();
		for($i=1; $i<=$row_num; $i++)
		{
			$txtreturnqty="txtreturnqty_".$i;
			$update_mst_id="dtlsmstid_".$i;

			$mst_return_qty_arr[str_replace("'","",$$update_mst_id)]+=str_replace("'","",$$txtreturnqty);
		}

		foreach($mst_return_qty_arr as $update_key=>$val_qty)
		{
			$ret_val_qty=$val_qty;//+$presentStock_qty_arr[str_replace("'","",$update_key)];
			$updateID_array_up[]=str_replace("'",'',$update_key);
			$data_array_dtls_upate[str_replace("'",'',$update_key)]=explode("*",("".$ret_val_qty.""));
		}

		//$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);
		$field_array_dtls_up="mst_id*color_size_break_down_id*production_qnty";
			$return_ids="";
			for($i=1; $i<=$row_num; $i++)
			{
				$wo_po_color_mst_id="colormstid_".$i;
				$txtreturnqty="txtreturnqty_".$i;
				//$txtexfactoryqty="txtexfactoryqty_".$i;
				$txtsize="txtsize_".$i;
				$txtcolor="txtcolor_".$i;
				$update_details_id="updatedtlsid_".$i;
				$update_mst_id="dtlsmstid_".$i;
				$mst_dtld_update_id=str_replace("'","",$$update_mst_id);
				if($return_ids=="") $return_ids=str_replace("'",'',$$update_details_id); else $return_ids.=",".str_replace("'",'',$$update_details_id);

				$updateID_array[]=str_replace("'",'',$$update_details_id);
				$data_array_dtls_up[str_replace("'",'',$$update_details_id)]=explode("*",("".$$update_mst_id."*".$$wo_po_color_mst_id."*".$$txtreturnqty.""));

			}
			$deliveryrID=true;
			$deliveryrID=sql_update("pro_ex_factory_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$txt_return_id."",1);
			$dtlsuprID=true;
			$dtlsuprID=execute_query(bulk_update_sql_statement("pro_ex_factory_mst","id",$field_array_dtls_up2,$data_array_dtls_upate,$updateID_array_up),1);

			$dtlsrID=true;
			if($sewing_production_variable !=1)
			{
				$dtlsrID=execute_query(bulk_update_sql_statement("pro_ex_factory_dtls","id",$field_array_dtls_up,$data_array_dtls_up,$updateID_array),1);
			}
			

		if($db_type==0)
		{
				if($deliveryrID && $dtlsrID && $dtlsuprID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".str_replace("'","",$txt_prev_mst_id)."**".str_replace("'","",$return_ids)."**".str_replace("'","",$txt_challan_no);;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$delivery_mst_id);
				}
		}
		if($db_type==2 || $db_type==1 )
		{

				if($deliveryrID && $dtlsrID && $dtlsuprID)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".str_replace("'","",$txt_prev_mst_id)."**".str_replace("'","",$return_ids)."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$delivery_mst_id);
				}



		}
		disconnect($con);
		die;
	}

	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$delivery_mst_id=str_replace("'","",$txt_system_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);


 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);
		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		$rID = sql_delete("pro_ex_factory_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_ex_factory_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="garments_exfactory_print")
{
	//$start = microtime(true);
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data); die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$order_sql=sql_select("select a.id, a.po_number, b.buyer_name, b.gmts_item_id, b.style_ref_no from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach($order_sql as $row)
	{
		$order_job_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$order_job_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$order_job_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
	}

	//echo "select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num from pro_ex_factory_delivery_mst where id=$data[1]");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$system_num=$row[csf("sys_number")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	?>
	<div style="width:810px; margin-top:5px;">
	    <table width="800" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="50" width="100"></td>
	            <td colspan="4" align="center"  style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="2" id="barcode_img_id"></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="4" align="center" style="font-size:14px;" valign="top">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						$company_address="";
						foreach ($nameArray as $result)
						{
						?>
							<? if($result[csf('plot_no')]!="") $company_address.= $result[csf('plot_no')].", "; ?>
							<? if($result[csf('level_no')]!="") $company_address.= $result[csf('level_no')].", ";?>
							<? if($result[csf('road_no')]!="") $company_address.= $result[csf('road_no')].", "; ?>
							<? if($result[csf('block_no')]!="") $company_address.= $result[csf('block_no')].", ";?>
							<? if($result[csf('city')]!="") $company_address.= $result[csf('city')].", ";?>
							<? if($result[csf('zip_code')]!="") $company_address.= $result[csf('zip_code')].", "; ?>
							<? if($result[csf('province')]!="") $company_address.= $result[csf('province')];?>
							<? if($result[csf('country_id')]!=0) $company_address.= $country_arr[$result[csf('country_id')]].", "; ?><br>
							<? if($result[csf('email')]!="") $company_address.= $result[csf('email')].", ";?>
							<? if($result[csf('website')]!="") $company_address.= $result[csf('website')];
						}
						$company_address=chop($company_address," , ");
						echo $company_address;
	                ?>
	            </td>
	        </tr>
	        	<?
					$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
					foreach($supplier_sql as $row)
					{

					$address_1=$row[csf("address_1")];
					$address_2=$row[csf("address_2")];
					$address_3=$row[csf("address_3")];
					$address_4=$row[csf("address_4")];
					$contact_no=$row[csf("contact_no")];
					}
					//echo $supplier_sql;die;

	            ?>
	        <tr>
	            <td colspan="5" style="font-size:x-large; padding-left:252px;"><strong><? echo $data[6]; ?></strong></td>
	            <td style="font-size:16px;">Date : <? echo change_date_format($data[2]); ?></td>
	        </tr>
	        <tr >
	        	<td width="100" valign="top" style="font-size:16px;"><strong>Name:</strong></td>
	            <td width="200" valign="top" style="font-size:16px;"><? echo $supplier_library[$supplier_name]; ?></td>
	            <td width="100" valign="top" style="font-size:16px;"><strong>Challan No :</strong></td>
	            <td width="120" valign="top" style="font-size:16px;"><? echo $challan_no; ?> </td>
	            <td width="80" valign="top" style="font-size:16px;"><strong>DL/NO:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $dl_no; ?> </td>
	        </tr>

	        <tr>
	            <td valign="top" style="font-size:16px;"><strong>Address:</strong></td>
	            <td colspan="3" valign="top" style="font-size:16px;"><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
	            <td style="font-size:16px;"><strong>Truck No:</strong></td>
	            <td style="font-size:16px;"><? echo $truck_no; ?> </td>
	        </tr>
	        <tr >
	            <td style="font-size:16px;"><strong>Destination :</strong></td>
	            <td style="font-size:16px;"><? echo $destination_place; ?> </td>
	            <td  valign="top" style="font-size:16px;"><strong >Driver Name :</strong></td>
	            <td  valign="top" style="font-size:16px;"><? echo $driver_name; ?> </td>
	            <td style="font-size:16px;"><strong >Lock No :</strong></td>
	            <td style="font-size:16px;"><? echo $lock_no; ?> </td>
	        </tr>
	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				$sql="SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id";
			}
			else if($db_type==0)
			{
				$sql="SELECT po_break_down_id, group_concat(invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id";
			}
			//echo $sql;die;
			$result=sql_select($sql);
			$table_width=800;
			$col_span=5;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="120">Style Ref.</th>
	            <th width="120" >Order No</th>
	            <th width="100" >Buyer</th>
	            <th width="200" >Invoice No</th>
	            <th width="50">NO Of Carton</th>
	            <th>Quantity</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($result as $row)
	        {
	            if ($i%2==0)
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	            $color_count=count($cid);
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td style="font-size:12px;"><? echo $i;  ?></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
					<?
					 $invoice_id="";
					 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
					 foreach($invoice_id_arr as $inv_id)
					 {
						 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];

					 }
					 echo $invoice_id;
					?>&nbsp;</p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        </tbody>

	        <tr>
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:12px;"><strong>Grand Total :</strong></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	        </tr>
	    </table>
	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
		 <?
            echo signature_table(63, $data[0], $table_width."px");
         ?>
	</div>
<?
//echo "Start Time: " . date("Y-m-d H:i:s.u",$start) . "<br/>";
//echo "End Time: " . date("Y-m-d H:i:s.u", microtime(true)) . "<br/>";
//$duration = microtime(true) - $start;
//echo "Printing Time: =" . date("s.u", $duration) ;

exit();
}

if($action=="ex_factory_print_new")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$order_sql=sql_select("select a.id, a.po_number, b.buyer_name, b.gmts_item_id, b.style_ref_no from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach($order_sql as $row)
	{
		$order_job_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$order_job_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$order_job_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
	}

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,mobile_no,do_no,gp_no,forwarder from pro_ex_factory_delivery_mst where id=$data[1]");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$mobile_no=$row[csf("mobile_no")];
		$do_no=$row[csf("do_no")];
		$gp_no=$row[csf("gp_no")];
		$forwarder=$row[csf("forwarder")];
		$system_num=$row[csf("sys_number")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:800px; margin-top:10px;">
	    <table width="800" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="55" width="65"></td>
	            <td colspan="4" align="center"  style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="2" id="barcode_img_id"></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="4" align="center" style="font-size:12px;">
					<?

						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						$company_address="";
						foreach ($nameArray as $result)
						{

							 if($result[csf('plot_no')]!="") $company_address.=$result[csf('plot_no')].", ";
							 if($result[csf('level_no')]!="") $company_address.= $result[csf('level_no')].", ";
							 if($result[csf('road_no')]!="") $company_address.= $result[csf('road_no')].", ";
							 if($result[csf('block_no')]!="") $company_address.= $result[csf('block_no')].", ";
							 if($result[csf('city')]!="") $company_address.= $result[csf('city')]."<br>";
							 if($result[csf('zip_code')]!="") $company_address.= $result[csf('zip_code')].", ";
							 if($result[csf('province')]!="") $company_address.= $result[csf('province')].", ";
							 if($result[csf('country_id')]!=0 && $result[csf('country_id')]!=""){ if($country_library[$result[csf('country_id')]]!="") $company_address.= $country_library[$result[csf('country_id')]].", ";}
							 if($result[csf('email')]!="") $company_address.= $result[csf('email')].", ";
							 if($result[csf('website')]!="") $company_address.= $result[csf('website')];
						}
						$company_address=chop($company_address," , ");
						echo $company_address;
	                ?> <br>
	                <span style="font-size:16px;">100% Export Oriented</span><br>
	                <span style="font-size:22px;">Delivery Challan</span>
	            </td>
	        </tr>
	        	<?
				  	//echo "select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder";
					$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
					foreach($supplier_sql as $row)
					{

					$address_1=$row[csf("address_1")];
					$address_2=$row[csf("address_2")];
					$address_3=$row[csf("address_3")];
					$address_4=$row[csf("address_4")];
					$contact_no=$row[csf("contact_no")];
					}
					//echo $supplier_sql;die;

	            ?>
	         <tr>
	         	<td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>

	         </tr>
	         <tr >
	        	<td width="100" valign="top" style="font-size:16px;"><strong>Challan No :</strong></td>
	            <td width="200" valign="top" style="font-size:16px;"><? echo $challan_no; ?></td>
	            <td width="100" valign="top" style="font-size:16px;"><strong>Driver Name :</strong></td>
	            <td width="120" valign="top" style="font-size:16px;"><? echo $driver_name; ?> </td>
	            <td width="80" valign="top" style="font-size:16px;"><strong>Date:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo change_date_format($data[2]); ?> </td>
	        </tr>
	        <tr >
	        	<td valign="top" style="font-size:16px;"><strong>C&F Name:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $supplier_library[$forwarder]; ?></td>
	            <td valign="top" style="font-size:16px;"><strong>Mobile Num :</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $mobile_no; ?> </td>
	            <td valign="top" style="font-size:16px;"><strong>DO NO:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $do_no; ?> </td>
	        </tr>
			<tr>
	            <td valign="top" style="font-size:16px;"><strong>Address:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
	            <td style="font-size:16px;"><strong>DL No:</strong></td>
	            <td style="font-size:16px;"><? echo $dl_no; ?> </td>
	            <td style="font-size:16px;"><strong>GP No:</strong></td>
	            <td style="font-size:16px;"><? echo $gp_no; ?> </td>
	        </tr>
	        <tr>
	            <td valign="top" style="font-size:16px;"><strong>Trns. Comp:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $supplier_library[$supplier_name]; ?> </td>
	            <td style="font-size:16px;"><strong>Truck No:</strong></td>
	            <td style="font-size:16px;"><? echo $truck_no; ?> </td>
	             <td style="font-size:16px;"><strong>Lock No:</strong></td>
	            <td style="font-size:16px;"><? echo $lock_no; ?> </td>
	        </tr>

	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				$sql="SELECT po_break_down_id, country_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id, country_id";
			}
			else if($db_type==0)
			{
				$sql="SELECT po_break_down_id, country_id, group_concat(invoice_no) as invoice_no, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id, country_id";
			}
			//echo $sql;die;
			$result=sql_select($sql);
			$table_width=800;
			$col_span=7;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="20">SL</th>
	            <th width="60" >Buyer</th>
	            <th width="100" >Style Ref.</th>
	            <th width="100" >Order No</th>
	            <th width="60" >Country</th>
	            <th width="130" >Item Name</th>
	            <th width="150" >Invoice No</th>
	            <th width="50">Delivery Qnty</th>
	            <th width="50">NO Of Carton</th>
	            <th >Remarks</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($result as $row)
	        {
	            if ($i%2==0)
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	            $color_count=count($cid);
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td style="font-size:12px;"><? echo $i;  ?></td>
	                <td style="font-size:12px;"><p><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
	                <?
	                 $garments_item_arr=explode(",",$order_job_arr[$row[csf("po_break_down_id")]]['gmts_item_id']);
	                 $garments_item_all="";
	                 foreach($garments_item_arr as $item_id)
	                 {
	                     $garments_item_all .=$garments_item[$item_id].",";
	                 }
	                 $garments_item_all=substr($garments_item_all,0,-1);
	                 echo $garments_item_all;
	                ?>
	                 &nbsp;</p></td>
	                <td style="font-size:12px;"><p>
					<?
					 $invoice_id="";
					 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
					 foreach($invoice_id_arr as $inv_id)
					 {
						 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];

					 }
					 echo $invoice_id;
					?>&nbsp;</p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td style="font-size:12px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        </tbody>

	        <tr>
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;">&nbsp;</td>
	        </tr>
	    </table>
		</div>
			 <?
	            echo signature_table(63, $data[0], $table_width."px");
	         ?>
	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
	<?
	exit();
}

?>
