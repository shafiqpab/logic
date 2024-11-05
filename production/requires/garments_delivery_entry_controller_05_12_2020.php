<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$truck_type_arr=array(1=>"Own",2=>"Hired");
$transport_type_arr=array(1=>"Tailor",2=>"Container");
$truck_type_arr_json=json_encode($truck_type_arr);
$transport_type_arr_json=json_encode($transport_type_arr);

//************************************ Start **************************************************

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#Print1').hide();\n";
	echo "$('#print_remarks_rpt').hide();\n";
	echo "$('#print_remarks_rpt3').hide();\n";
	echo "$('#print_remarks_rpt_sonia').hide();\n";
	echo "$('#print_remarks_rpt4').hide();\n";
	echo "$('#print_remarks_rpt5').hide();\n";
	echo "$('#print_remarks_rpt6').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#Print1').show();\n";}
			if($id==121){echo "$('#print_remarks_rpt').show();\n";}
			if($id==122){echo "$('#print_remarks_rpt3').show();\n";}
			if($id==123){echo "$('#print_remarks_rpt_sonia').show();\n";}
			if($id==127){echo "$('#print_remarks_rpt4').show();\n";}
			if($id==169){echo "$('#print_remarks_rpt6').show();\n";}
			if($id==580){echo "$('#print_remarks_rpt5').show();\n";}
		}
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
	$variable_qty_source_poly=return_field_value("preceding_page_id","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=32","preceding_page_id");
	echo "document.getElementById('txt_qty_source').value='".$variable_qty_source_poly."';\n";

 	exit();
}

if($action=="load_drop_delivery_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
 			echo create_drop_down( "cbo_del_company", 172, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_del_company", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
 	else if($data==1)
 	{
  		echo create_drop_down( "cbo_del_company", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Delivery Company --", '', "load_drop_down( 'requires/garments_delivery_entry_controller', this.value, 'load_drop_down_del_location', 'del_location_td' );",0 );
 	}
 	else
 		echo create_drop_down( "cbo_del_company", 172, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
 	exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_del_location")
{
	echo create_drop_down( "cbo_delivery_location", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Delivery Location --", $selected, "load_drop_down( 'requires/garments_delivery_entry_controller', $data+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );" );
	exit();
}

if ($action=="load_drop_down_del_floor")
{
	$data=explode('**',$data);
	echo create_drop_down( "cbo_delivery_floor", 172, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_transport_com")
{
	echo create_drop_down( "cbo_transport_company", 172, "select a.id,a.supplier_name from  lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data'  and a.id in (select  supplier_id  from  lib_supplier_party_type where party_type in (35)) order by supplier_name","id,supplier_name", 1, "-- Select Transport --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_forwarder")
{
	echo create_drop_down( "cbo_forwarder", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select--", $selected,"forwarding_agent_disable_1(this.value);","0" );
	exit();
}
if ($action=="load_drop_down_forwarder2")
{
	echo create_drop_down( "cbo_forwarder_2", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='$data' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select--", $selected,"forwarding_agent_disable_2(this.value);","0" );
	exit();
}

if($action=="sys_surch_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

	?>
		<script>
		var company_id='<? echo $company_id;?>';

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
	        <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	             <thead>
	             	<th width="130">Company Name</th>
	             	<th width="120">Buyer Name</th>
	                <th width="160">Transport Com.</th>	                
	                <th width="100">Challan No</th>
	                <th width="100">Order No</th>
	                <th width="200">Ex-Factory Date Range</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	            </thead>
	            <tr class="general">
	            	<td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'garments_delivery_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
	                <td><? echo create_drop_down( "cbo_trans_com", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 ); ?></td>
	                <td><input type="text" style="width:90px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" /></td>
	                <td><input type="text" style="width:90px" class="text_boxes"  name="txt_po_no" id="txt_po_no" /></td>
	                <td>
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                </td>
	                <td>
	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_delivery_search_list', 'search_div_delivery', 'garments_delivery_entry_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
	                </td>
	            </tr>
	            <tr>
	                <td align="center" colspan="6" valign="middle">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_delivery_id" >
	                </td>
	            </tr>
	        </table>
	        <div id="search_div_delivery" style="margin-top:20px;"></div>
	    </form>
	</div>
	<script type="text/javascript">
		$("#cbo_company_name").val(company_id);
	</script>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
//$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
$order_sql=sql_select("SELECT id, po_number,sum(po_quantity) as po_quantity  from wo_po_break_down group by id, po_number");
foreach($order_sql as $val)
{
	$order_num_arr[$val[csf("id")]]=$val[csf("po_number")];
	$order_qnty_arr[$val[csf("id")]]+=$val[csf("po_quantity")];
}

if ($action=="load_drop_down_buyer")
{
    if($data != 0)
    {
		echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();  
    }  
    else{
        echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
        exit(); 
    }	 
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
	$buyer = $ex_data[6];
	//echo $trans_com;die;
	$sql_cond="";
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond.= "and b.ex_factory_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond.="and b.ex_factory_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!=0) $sql_cond .= " and a.company_id='$company'";
	if(trim($buyer)!=0) $sql_cond .= " and a.buyer_id='$buyer'";
	if(trim($txt_challan_no)!="") $sql_cond .= " and b.challan_no='$txt_challan_no'";
	if(trim($trans_com)!=0) $sql_cond .= " and a.transport_supplier='$trans_com'";
	if(trim($po_no)!="")
	{
		$po_no_id = return_field_value("id as po_id","wo_po_break_down","po_number='$po_no' and status_active=1","po_id");
		$po_cond="and b.po_break_down_id='$po_no_id'";
	}
	else $po_cond="";
	
	if($db_type==0)
	{
		$sql = "SELECT a.id, a.sys_number_prefix_num, year(a.insert_date) as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no,group_concat(b.po_break_down_id) as po_break_down_id,sum(b.ex_factory_qnty) as ex_factory_qnty
	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
	where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form!=85 $sql_cond $po_cond
	group by  a.id, a.sys_number_prefix_num, year(a.insert_date), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	else
	{
		$sql = "SELECT a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY') as delivery_year, a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no, listagg(CAST(b.po_break_down_id as VARCHAR(4000)),',') within group (order by b.po_break_down_id) as po_break_down_id
	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b
	where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form!=85 $sql_cond $po_cond
	group by  a.id, a.sys_number_prefix_num, to_char(a.insert_date,'YYYY'), a.sys_number, a.company_id, a.location_id, a.challan_no, a.buyer_id, a.transport_supplier, a.delivery_date, a.lock_no, a.driver_name, a.truck_no, a.dl_no  order by a.id desc";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	$all_po_arr=array();
	foreach($result as $v)
	{
		$all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
	}
	$ids= implode(",",$all_po_arr); 
	$all_po_cond="";
	if($db_type==2 && count($all_po_arr)>999)
	{
		$chnk=array_chunk($all_po_arr, 999);
		foreach($chnk as $v)
		{
			$po_ids=implode(",", $v);
			if($all_po_cond=="") $all_po_cond.="  id in ($po_ids) ";
			else $all_po_cond.=" or   id in ($po_ids) ";
		}
		 
	}
	else $all_po_cond=" id in($ids) ";

	$po_status_arr=return_library_array("SELECT id, shiping_status from wo_po_break_down where $all_po_cond","id","shiping_status");

	/*$exfact_sql=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		 sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("return_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]=$row[csf("carton_qnty")];
		}*/

	$exfact_qty_arr=return_library_array( "select delivery_mst_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where entry_form != 85 and status_active=1 and delivery_mst_id>0 group by delivery_mst_id",'delivery_mst_id','ex_factory_qnty');
 	$buyer_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$trans_com_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id","supplier_name");
   ?>
     	<table cellspacing="0" width="1130" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="50">Sys Num</th>
                <th width="50">Year</th>
                <th width="80">Company</th>
                <th width="70">Buyer Name</th>
                <th width="155">Transport Company</th>
                <th width="50">Challan No</th>
                <th width="70">Delivery Date</th>
                <th width="120">Driver Name</th>
                <th width="90">Truck No</th>
                <th width="90">Lock No</th>
                <th width="80">Ex-fact Qty</th>
                <th width="100">Ex-fact Status</th>
                <th>Order No</th>
            </thead>
     	</table>
     <div style="width:1130px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1112" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>);" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="50" align="center"><p><? echo $row[csf("sys_number_prefix_num")]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf("delivery_year")]; ?></p></td>
                    <td width="80" align="center"><p><? echo $company_arr[$row[csf("company_id")]]; ?></p></td>
                    <td width="70"><p><? echo $buyer_name_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="155" align="center"><p><? echo $trans_com_arr[$row[csf("transport_supplier")]];?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf("driver_name")]; ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $row[csf("truck_no")];?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $row[csf("lock_no")];?>&nbsp;</p></td>
                    <? if($db_type==1){?>
                    <td width="80" align="right"><p><?  echo number_format($row[csf("ex_factory_qnty")],0,"","");?></p></td>
                    <? }else{ ?>
                    <td width="80" align="right"><p><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></p></td>
                    <? } ?>

                    <td width="100">
                    	<p>
                    		<?  
                    		$po_id_arr=array_unique(explode(",",$row[csf("po_break_down_id")]));
                    		$arrs=$po_id_arr;
                    		$shiping_status_val="";
                    		foreach($arrs as $vals)
                    		{
                    			if($shiping_status_val=="")
                    			{
                    				$shiping_status_val .=$shipment_status[$po_status_arr[$vals]];
                    			}
                    			else
                    				$shiping_status_val .=','.$shipment_status[$po_status_arr[$vals]];
                    		}
                    		echo $shiping_status_val;
                    		?>&nbsp;
                    	</p>
                    </td>
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

if($action=="populate_muster_from_date")
{
	$sql_mst=sql_select("SELECT id, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no,dl_no,destination_place,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,mobile_no, do_no, gp_no,source
	from  pro_ex_factory_delivery_mst where id=$data and entry_form <> 85");

	/*	echo "select id, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no,destination_place,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,mobile_no, do_no, gp_no
		from  pro_ex_factory_delivery_mst where id=$data and entry_form!=85";*/

	foreach($sql_mst as $row)
	{
		$company_id=$row[csf('company_id')];
		echo "load_drop_down( 'requires/garments_delivery_entry_controller', '$company_id', 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/garments_delivery_entry_controller', '$company_id', 'load_drop_down_transport_com', 'transfer_com' );\n";
		echo "load_drop_down( 'requires/garments_delivery_entry_controller', '$company_id', 'load_drop_down_forwarder', 'forwarder_td' );\n";
		echo "$('#txt_system_no').val('".$row[csf('sys_number')]."');\n";
		echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf('company_id')].");\n";
		echo "$('#cbo_location_name').val(".$row[csf('location_id')].");\n";
		echo "$('#txt_challan_no').val('".$row[csf('challan_no')]."');\n";
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
		echo "$('#cbo_forwarder_2').val(".$row[csf('forwarder_2')].");\n";
		echo "$('#cbo_source').val(".$row[csf('source')].");\n";
		
		if($row[csf('source')]==1)
		{
			echo "load_drop_down( 'requires/garments_delivery_entry_controller', '1**$company_id', 'load_drop_delivery_company', 'dev_company_td' );\n";
		}
		else if($row[csf('source')]==3)
		{
			echo "load_drop_down( 'requires/garments_delivery_entry_controller', '3**$company_id', 'load_drop_delivery_company', 'dev_company_td' );\n";
		}
		echo "$('#cbo_del_company').val(".$row[csf('delivery_company_id')].");\n";

		echo "$('#txt_attention').val('".$row[csf('attention')]."');\n";
		echo "$('#txt_remarks').val('".$row[csf('remarks')]."');\n";

		echo "load_drop_down( 'requires/garments_delivery_entry_controller','".$row[csf("delivery_company_id")]."', 'load_drop_down_del_location', 'del_location_td') ;\n";

		echo "load_drop_down( 'requires/garments_delivery_entry_controller', '".$row[csf("delivery_company_id")]."'+'**'+ '".$row[csf("delivery_location_id")]."', 'load_drop_down_del_floor', 'del_floor_td' );\n";

		echo "$('#cbo_delivery_location').val(".$row[csf('delivery_location_id')].");\n";
		echo "$('#cbo_delivery_floor').val(".$row[csf('delivery_floor_id')].");\n";

		echo "$('#check_posted_in_accounce').val('');\n";

		$sql_is_posted_account=sql_select("select id from pro_ex_factory_mst where delivery_mst_id='".$row[csf('id')]."' and is_posted_account=1 and status_active=1 and is_deleted=0");
		if($sql_is_posted_account[0][csf('id')]!="")
		{
			echo "$('#check_posted_in_accounce').val(1);\n";
		}
		//echo "set_button_status(0, permission, 'fnc_exFactory_entry',1,0);\n";
	}
	exit();
}

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");


if($action=="show_dtls_listview_mst")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode);
	$country_short_array=return_library_array( "select id,short_name from lib_country", "id", "short_name"  );
	?>
	<div style="width:1380px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th style="word-break: break-all;word-wrap: break-word;" width="40">SL</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="120" >Item Name</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="110" >Country</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="130" >Country Short Name</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="130" >Style</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="120" >Order No</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="120" >Order Quantity</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="100" >Ex-Fact. Date</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="80" >Ex-Fact. Qnty</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="110" >Ex-Fact. Balance</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="120" >Invoice No</th>
                <th style="word-break: break-all;word-wrap: break-word;" width="120" >LC/SC No</th>
                <th width="80" style="word-break: break-all;word-wrap: break-word;" align="center">Challan No</th>
            </thead>
    	</table>
    </div>
	<div style="width:1380px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="details_table">
		<?
			$i=1;
			$total_production_qnty=0;
			$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where status_active=1");
			foreach($sqlEx as $row)
			{
				$invoice_data_arr[$row[csf("id")]]["id"]=$row[csf("id")];
				$invoice_data_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
				$invoice_data_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
				$invoice_data_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
			}
			//echo "select id,po_break_down_id,item_number_id,country_id,ex_factory_date,ex_factory_qnty,location,lc_sc_no,invoice_no,challan_no from  pro_ex_factory_mst where delivery_mst_id=$data and status_active=1 and is_deleted=0 order by id";
			$sqlResult =sql_select("select a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.ex_factory_date,a.ex_factory_qnty,a.location,a.lc_sc_no,a.invoice_no,b.challan_no from  pro_ex_factory_mst a,  pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.delivery_mst_id=$data and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 order by id");
			$po_id_arr = array();
			foreach($sqlResult as $row)
			{
				$po_id_arr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
			}
			$allPoId = implode(",", $po_id_arr);
			$style_sql = "SELECT a.style_ref_no,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($allPoId)";
			$style_sql_res = sql_select($style_sql);
			$style_ref_arr = array();
			foreach ($style_sql_res as $val) 
			{
				$style_ref_arr[$val[csf('id')]] = $val[csf('style_ref_no')];
			}

 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$total_production_qnty+=$selectResult[csf('ex_factory_qnty')];
				if($invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"]==1) //  lc
					$lc_sc = $lc_num_arr[$invoice_data_arr[$selectResult[csf("invoice_no")]]["lc_sc_id"]];
				else if($invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"]==2)
					$lc_sc = $sc_num_arr[$invoice_data_arr[$selectResult[csf("invoice_no")]]["lc_sc_id"]];

				$invoiceNo = $invoice_data_arr[$selectResult[csf("invoice_no")]]["invoice_no"];
				//$order_num_arr
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_exfactory_form_data','requires/garments_delivery_entry_controller');get_php_form_data('<? echo $selectResult[csf('po_break_down_id')];?>+**+<? echo $selectResult[csf('item_number_id')];?>+**+<? echo $selectResult[csf('country_id')];?>'+'**'+$('#hidden_preceding_process').val()+'**'+$('#txt_mst_id').val()+'**1'+'**'+$('#sewing_production_variable').val()+'**'+$('#variable_is_controll').val(),'populate_data_from_search_popup','requires/garments_delivery_entry_controller');" >
                    <td style="word-break: break-all;word-wrap: break-word;"  width="40" align="center"><? echo $i; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="120" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="110" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="130" align="center"><p><? echo $country_short_array[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="130" align="left"><p><? echo $style_ref_arr[$selectResult[csf('po_break_down_id')]]; ?>&nbsp;</p></td>
                     <td  style="word-break: break-all;word-wrap: break-word;" width="120" align="center"><p><? echo $order_num_arr[$selectResult[csf('po_break_down_id')]]; ?></p></td>
                     <td  style="word-break: break-all;word-wrap: break-word;" width="120" align="center"><p><? echo $order_qnty_arr[$selectResult[csf('po_break_down_id')]]; ?></p></td>
                    <td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><p><? echo change_date_format($selectResult[csf('ex_factory_date')]); ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="80" align="center"><p><? echo $selectResult[csf('ex_factory_qnty')]; ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="110" align="center"><p><? echo $order_qnty_arr[$selectResult[csf('po_break_down_id')]]-$selectResult[csf('ex_factory_qnty')]; ?></p></td>
                    <td  style="word-break: break-all;word-wrap: break-word;" width="120" align="center"><p><? echo $invoiceNo; ?>&nbsp;</p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="120" align="center"><p><? echo $lc_sc; ?>&nbsp;</p></td>
                    <td width="80" style="word-break: break-all;word-wrap: break-word;" align="center"><p><? echo $selectResult[csf('challan_no')]; ?>&nbsp;</p></td>
                </tr>
			<?
			$i++;
			}
			?>
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
		<!-- <script type="text/javascript">setFilterGrid("details_table",-1);</script> -->
	</div>
	<?
	exit();
}


if ($action=="lcsc_popup")
{
	extract($_REQUEST);
	$order_id=str_replace("'","",$order_id);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
	function js_set_value(str)
	{
		$("#lc_id_no").val(str);
		parent.emailwindow.hide();
		//parent.emailwindow.hide();
	}
	</script>

	<?
		if($db_type==0)
		{
	 		$sql = "SELECT a.id, a.invoice_no, a.invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, group_concat(b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=$order_id group by a.id order by a.invoice_no";
		}
		else
		{
			$sql = "SELECT a.id, a.invoice_no, max(a.invoice_date) as invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=$order_id group by a.id,a.invoice_no,a.buyer_id,a.lc_sc_id,a.benificiary_id,a.is_lc order by a.invoice_no";

		}
		//echo $sql;die;
		$result = sql_select($sql);
		$invoice_id_array = array();
		$lcsc_id_array = array();
		foreach ($result as $val) 
		{
			$invoice_id_array[$val[csf('id')]] = $val[csf('id')];
			$lcsc_id_array[$val[csf('lc_sc_id')]] = $val[csf('lc_sc_id')];
		}
		$invoiceId = implode(",", $invoice_id_array);
		$lcscId = implode(",", $lcsc_id_array);
		//===================== getting exfact qty ====================
		$sql_exfact = "SELECT c.invoice_no,c.lc_sc_no, d.production_qnty as ex_fact_qty from pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e where c.id=d.mst_id and e.id=c.delivery_mst_id and c.invoice_no in($invoiceId) and c.lc_sc_no in($lcscId) and c.po_break_down_id=$order_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
		// echo $sql_exfact;
		$sql_exfact_res = sql_select($sql_exfact);
		$exfact_qty_array = array();
		foreach ($sql_exfact_res as $val) 
		{
			$exfact_qty_array[$val['LC_SC_NO']][$val['INVOICE_NO']] += $val['EX_FACT_QTY'];
		}
		// print_r($exfact_qty_array);
	 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	 	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id=$order_id",'id','po_number');
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		if($db_type==0)
		{
			$po_num_arr=return_library_array("select id, group_concat(distinct(po_number)) as po_number from wo_po_break_down where status_active in(1,2,3) and is_deleted=0 ", "id", "po_number");
		}
		else
		{
			$po_num_arr=return_library_array("select id, listagg(CAST(po_number as VARCHAR(4000)),',') within group (order by po_number) as po_number from wo_po_break_down where status_active in(1,2,3) and is_deleted=0 group by id", "id", "po_number");
		}
	     //echo create_list_view("list_view","Invoice NO,Invoice Date,Buyer,LC/SC No,Order Qunty,Company","130,100,170,100,100,150","850","250",1,$sql,"js_set_value","invoice_no,lc_sc_no","",1,"0,0,buyer_id,0,0,benificiary_id",$printed_array,"invoice_no,invoice_date,buyer_id,lc_sc_no,order_quantity,benificiary_id","requires/garments_delivery_entry_controller","setFilterGrid('tbl_po_list',1)","0,0,0,0,0,1","","");

	   ?>
	  	<div style="width:980px; margin-top:10px">
	     	<table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
	            <thead>
	                <th width="30" >SL</th>
	                <th width="80" >Invoice No</th>
	                <th width="75" >Invoice Date</th>
	                <th width="120" >Buyer</th>
	                <th width="150" >LC/SC No</th>
	                <th width="120" >Order No</th>
	                <th width="70" >Order Qty</th>
	                <th width="70" >Invoice Qty</th>
	                <th width="70" >Att. Invoice Qty</th>
	                <th width="70" >Balance</th>
	                <th width="">Company Name</th>
	            </thead>
	     	</table>
	     </div>

	     <? if(count($result) == 0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Invoice does not exist ! Please make sure invoice for this po number <b>{ $po_arr[$order_id] }</b>.</div>";die(); }?>

	     <div style="width:980px; max-height:320px;overflow-y:scroll;" >

	        <table cellspacing="0" width="962" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
				<?
				$i=1;
	            foreach( $result as $row )
	            {
	                if ($i%2==0)  $bgcolor="#E9F3FF";
	                else $bgcolor="#FFFFFF";
					$po_number=$po_num_arr[$row[csf("po_id")]];


					if($row[csf("is_lc")]==1) //  lc
					{
						$lc_sc = $lc_num_arr[$row[csf('lc_sc_id')]];
					}
					else
					{
						$lc_sc = $sc_num_arr[$row[csf('lc_sc_id')]];
					}
						$attInvQty = $exfact_qty_array[$row[csf("lc_sc_id")]][$row[csf("id")]];
						$balanceQty = $row[csf("order_quantity")] - $attInvQty;
						if($balanceQty <= 0){$bgcolor="red";}

	 					?>
	                    <input type="hidden" id="lc_id_no" name="lc_id_no">
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')];?>,<? echo $row[csf('invoice_no')];?>,<? echo $row[csf('lc_sc_id')]; ?>,<? echo $lc_sc;?>');" >
							<td width="30" align="center"><? echo $i; ?></td>
                            <td width="80" align="left"><p><? echo $row[csf("invoice_no")]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf("invoice_date")]);?></td>
 							<td width="120"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
							<td width="150"><p><? echo $lc_sc; ?></p></td>
                            <td width="120"><p><? echo $po_number; ?></p></td>
							<td width="70" align="right"><? echo $row[csf("order_quantity")];?> </td>
							<td width="70" align="right"><? echo $row[csf("order_quantity")];?> </td>
							<td width="70" align="right"><? echo $attInvQty;?> </td>
							<td width="70" align="right"><? echo $balanceQty;?> </td>
 							<td width=""><p><?  echo $company_arr[$row[csf("benificiary_id")]];?></p></td>
						</tr>
						<?
						$i++;
	             }
	   		?>
				</table>
	            <script>setFilterGrid("tbl_invoice_list",-1);</script>
			</div>
		  <?
	exit();
}

if($action=="order_popup")
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
				else if(str==4)
				{
					document.getElementById('search_by_th_up').innerHTML="Internal Ref. No";
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
					
					document.getElementById('txt_search_common').value='<? echo $buyer_id;?>';
					<?
					if($buyer_id != 0)
					{
					?>
					document.getElementById('txt_search_common').disabled=true;
					<? } ?>
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
                 <thead>
                    <th width="130">Search By</th>
                    <th width="240" align="center" id="search_by_th_up">Enter Order Number</th>
                    <th width="200">Date Range</th>
                    <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                </thead>
                <tr class="general">
                    <td>
                    <?
                    $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Actual PO No",4=>"Internal Ref. No");
                    echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
                    ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
                    </td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td>
                    <td>
                        <input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $hidden_variable_cntl; ?>+'_'+<? echo $hidden_preceding_process; ?>+'_'+<? echo $buyer_id; ?>, 'create_po_search_list_view', 'search_div', 'garments_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="middle">
                        <? echo load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_mst_id">
                        <input type="hidden" id="hidden_grmtItem_id">
                        <input type="hidden" id="hidden_po_qnty">
                        <input type="hidden" id="hidden_country_id">
                    </td>
                </tr>
            </table>
            <div style="font-weight: bold;font-size: 14px;color: red;padding: 5px 0 0 0;text-align: center;width: 100%">N.B : Buyer mixed not allow</div>
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
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
 	$preceding_process = $ex_data[6];
 	$buyer_id = $ex_data[8];
  	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry


	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
		{
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		}
		else if(trim($txt_search_by)==1)
		{
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		}
		else if(trim($txt_search_by)==2)
		{
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
		}
		else if(trim($txt_search_by)==3)
		{
			$sql_cond = " and b.po_number_acc like '%".trim($txt_search_common)."%'";
		}
		else if(trim($txt_search_by)==4)
		{
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
		}
 	}
 	if(trim($txt_search_by)!=2 && $buyer_id !=0)
 	{
 		$sql_cond .= " and a.buyer_name=trim('$buyer_id')";
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	$qty_source_cond="";
	if($qty_source!=0)
	{
		$qty_source_cond="and b.id in(select po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and status_active=1 and is_deleted=0)";
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	
	$sql = "select b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_number_acc, b.po_quantity, b.plan_cut, b.grouping
			from wo_po_details_master a, wo_po_break_down_vw b 
			where
			a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $qty_source_cond order by b.shipment_date DESC";
	// echo $sql;
	/*if(trim($txt_search_by)==3 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut, b.grouping
		from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
		where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1) and  b.is_deleted=0 and c.status_active=1 and
			c.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $qty_source_cond ";
	}
	else
	{
		$sql = "SELECT b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut, b.grouping from wo_po_details_master a, wo_po_break_down b  where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1) and  b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $qty_source_cond";
	}*/
	//echo $sql;
	$result = sql_select($sql);
	$poId_arr = array();
	foreach ($result as $key => $val) 
	{
		$poId_arr[$val[csf('id')]] = $val[csf('id')];
	}

	if(count($poId_arr) >0)
	{
		$poIds=implode(",", $poId_arr);
        if(count($poId_arr)>999 && $db_type==2)
        {
         	$po_chunk=array_chunk($poId_arr, 999);
         	$po_cond= "";
         	foreach($po_chunk as $vals)
         	{
         		$imp_ids=implode(",", $vals);
         		if($po_cond=="") $po_cond.=" and (po_break_down_id in ($imp_ids) ";
         		else $po_cond.=" or po_break_down_id in ($imp_ids) ";
         	}
         	 $po_cond.=" )";
        }
        else $po_cond= " and po_break_down_id in($poIds) ";
	}
	else $po_cond = "";
	
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	/*if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_cond group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_cond group by po_break_down_id",'po_break_down_id','country');
	}*/

	$po_country_data_arr=array(); $po_country_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
	
	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
		if($po_country_arr[$row[csf("po_break_down_id")]]=="")
		{
			$po_country_arr[$row[csf("po_break_down_id")]].=$row[csf("country_id")];
		}
		else
		{
			$po_country_arr[$row[csf("po_break_down_id")]].=','.$row[csf("country_id")];
		}
	}


	$total_ex_fac_data_arr=array();
	$total_ex_fac_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum( case when entry_form<>85 then ex_factory_qnty else 0 end ) -sum(case when ex_factory_qnty>0 and entry_form=85 then ex_factory_qnty  else 0 end) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $po_cond group by po_break_down_id, item_number_id, country_id");
	foreach($total_ex_fac_arr as $row)
	{
		$total_ex_fac_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
	}
	?>
	<div style="width:1160px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="65">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="90">Acc.Order No</th>
                <th width="100">Buyer</th>
                <th width="100">Style</th>
                <th width="110">Item</th>
                <th width="70">Internal Ref. No</th>
                <th width="90">Country</th>
                <th width="80">Order Qty</th>
                <th width="70">Total Ex-factory Qty</th>
                <th width="70">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1160px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1142" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				// print_r($country);
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
								<td width="65" align="center"><? echo change_date_format($row[csf("shipment_date")]);?></td>
								<td width="100" style="word-break:break-all"><? echo $row[csf("po_number")]; ?></td>
                                <td width="90" style="word-break:break-all"><?=$row[csf("po_number_acc")]; ?></td>
								<td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
								<td width="100" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?></td>
								<td width="110" style="word-break:break-all"><? echo $garments_item[$grmts_item];?></td>
								<td width="70" style="word-break:break-all"><? echo $row[csf("grouping")];?></td>
								<td width="90" style="word-break:break-all" title="Country ID = <? echo $country_id;?>"><? echo $country_library[$country_id]; ?>&nbsp;</td>
								<td width="80" align="right"><? echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                                <td width="70" align="right"><?=$total_cut_qty=$total_ex_fac_data_arr[$row[csf('id')]][$grmts_item][$country_id]; ?>&nbsp;</td>
                                <td width="70" align="right"><? $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp;</td>
								<td style="word-break:break-all"><?=$company_arr[$row[csf("company_name")]];?> </td>
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
	$ex_mst_id = $dataArr[4];
	$source_type=$dataArr[5];
	$sewing_production_variable=$dataArr[6];
	$is_control=$dataArr[7];
	if($source_type==2)echo "$('#txt_mst_id').val('0');\n";
	$conds="";
	if($ex_mst_id) $conds.=" and a.id<>$ex_mst_id ";
	$qty_source=0;
	if($dataArr[3]==28) $qty_source=4; //Sewing Input
	else if($dataArr[3]==29) $qty_source=5;//Sewing Output
	else if($dataArr[3]==30) $qty_source=7;//Iron Output
	else if($dataArr[3]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[3]==32) $qty_source=7;//Iron Output
	else if($dataArr[3]==91) $qty_source=7;//Iron Output
	else if($dataArr[3]==103) $qty_source=11;//Poly Entry
	if($is_control !=1){$qty_source=0;}
	$res = sql_select("SELECT a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name,a.shipment_date   from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id=$po_id");
	 
	/*$ex_fac_poqty=return_field_value("sum(b.production_qnty)","pro_ex_factory_mst a,pro_ex_factory_dtls b "," a.id=b.mst_id and a.po_break_down_id='$po_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds");

	$ex_fac_countryqty=return_field_value("sum(b.production_qnty)","pro_ex_factory_mst a,pro_ex_factory_dtls b "," a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds");

	$hidden_countryqty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0");*/
	if($sewing_production_variable==2 || $sewing_production_variable==3)
	{
	$ex_fac_poqty=return_field_value("sum(b.production_qnty) as production_qnty","pro_ex_factory_mst a,pro_ex_factory_dtls b "," a.id=b.mst_id and a.po_break_down_id='$po_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds","production_qnty");
//echo "select sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where  a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds";
	$ex_fac_countryqty=return_field_value("sum(b.production_qnty) as production_qnty","pro_ex_factory_mst a,pro_ex_factory_dtls b "," a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds","production_qnty");

	$hidden_countryqty=return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0","order_quantity");
	}
	else
	{
		$ex_fac_poqty=return_field_value("sum(a.ex_factory_qnty) as production_qnty","pro_ex_factory_mst a "," a.po_break_down_id='$po_id'  and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 $conds","production_qnty");
//echo "select sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where  a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $conds";
	$ex_fac_countryqty=return_field_value("sum(a.ex_factory_qnty) as production_qnty","pro_ex_factory_mst a ","  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and country_id='$country_id' and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0  $conds","production_qnty");

	$hidden_countryqty=return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0","order_quantity");
	}

	//$challan_id = return_field_value("delivery_mst_id","pro_ex_factory_mst a "," a.id=$ex_mst_id  and a.status_active=1 and a.is_deleted=0","delivery_mst_id");
	$sqlLcScInfo = "SELECT a.ID,a.COMMISSION, a.COMMISSION_PERCENT,(c.EX_FACTORY_QNTY*b.CURRENT_INVOICE_RATE) as INVOICE_VALUE FROM com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b, pro_ex_factory_mst c WHERE a.id=b.mst_id and  a.id = c.invoice_no AND a.LC_SC_ID = c.LC_SC_no AND c.id = $ex_mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $sqlLcScInfo;
	$lcscRes = sql_select($sqlLcScInfo);
	$commission = ($lcscRes[0]['INVOICE_VALUE'] * $lcscRes[0]['COMMISSION_PERCENT'])/100;

	echo "$('#txt_commission').val('".$lcscRes[0]['COMMISSION_PERCENT']."');\n";
	echo "$('#txt_commission_amt').val('".$commission."');\n";
	echo "$('#txt_order_amt').val('".$lcscRes[0]['INVOICE_VALUE']."');\n";

	echo "$('#hidden_ex_fac_poqty').val('".$ex_fac_poqty."');\n";
	echo "$('#hidden_ex_fac_countryqty').val('".$ex_fac_countryqty."');\n";
	echo "$('#hidden_countryqty').val('".$hidden_countryqty."');\n";

 	foreach($res as $result)
	{
		echo "$('#txt_order_qty').val('".$result[csf('po_quantity')]."');\n";
		echo "$('#cbo_item_name').val(".$item_id.");\n";
		echo "$('#cbo_country_name').val(".$country_id.");\n";
		echo "$('#short_country_name').val(".$country_id.");\n";

		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_shipment_date').val('".change_date_format($result[csf('shipment_date')])."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		if($qty_source!=0)
   		{

   			echo "$('#source_msg').text('');\n";
   			if($qty_source==4) { echo "$('#source_msg').text('Sewing Input Qnty');\n"; }
   			else if($qty_source==5) { echo "$('#source_msg').text('Sewing Output Qnty');\n"; }
   			else if($qty_source==7) { echo "$('#source_msg').text('Iron Output Qnty');\n"; }
   			else if($qty_source==8) { echo "$('#source_msg').text('Packing And Finishing');\n"; }
   			else if($qty_source==11) { echo "$('#source_msg').text('Poly Entry Qnty');\n"; }
   			else{echo "$('#source_msg').text('Sewing Finish Qnty');\n";}

   			if($sewing_production_variable==1) // gross level
   			{
   				$finish_qty = sql_select("SELECT sum( a.production_quantity) as production_qnty from pro_garments_production_mst a where a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.production_type='$qty_source' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0");
   				// receive from fin gmts order to order transfer
   				$receive_qty = sql_select("SELECT sum( a.production_quantity) as production_qnty from pro_gmts_delivery_dtls a where a.to_po_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.production_type='10' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0");
   			}
   			else
   			{
   				$finish_qty = sql_select("SELECT sum( b.production_qnty) as production_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b where  a.id=b.mst_id and a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.production_type='$qty_source' and b.production_type='$qty_source' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
   				// receive from fin gmts order to order transfer
   				$receive_qty = sql_select("SELECT sum( b.production_qnty) as production_qnty from pro_garments_production_mst a ,pro_garments_production_dtls b where  a.id=b.mst_id and a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.production_type='10' and b.production_type='10' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=5 and b.trans_type=5");
   			}
		 	
		  	$finish_qty=$finish_qty[0][csf("production_qnty")]+$receive_qty[0][csf("production_qnty")];
 			if($finish_qty=="")$finish_qty=0;

			$total_produced = sql_select(" select sum(case when entry_form<>85 then ex_factory_qnty else 0 end )- sum(case when entry_form=85 then ex_factory_qnty else 0 end ) as ex_factory_qnty  from pro_ex_factory_mst where po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id'   and status_active=1 and is_deleted=0");

			$total_produced=$total_produced[0][csf("ex_factory_qnty")];
			//echo "reud $total_produced";
			if($total_produced=="")$total_produced=0;

	 		echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";
	 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
			$yet_to_produced = $finish_qty-$total_produced;
			echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
		}

		if($qty_source==0)
		{
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0");

			//$total_produced = return_field_value("sum(case when entry_form<>85 then ex_factory_qnty else 0 end )-sum(case when entry_form=85 then ex_factory_qnty else 0 end ) as ex_factory_qnty","pro_ex_factory_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and entry_form<>85 and country_id='$country_id'  and is_deleted=0");
			$total_produced = sql_select(" select sum(case when entry_form<>85 then ex_factory_qnty else 0 end )- sum(case when entry_form=85 then ex_factory_qnty else 0 end ) as ex_factory_qnty  from pro_ex_factory_mst where po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id'   and status_active=1 and is_deleted=0");

			$total_produced=$total_produced[0][csf("ex_factory_qnty")];
			echo "$('#txt_finish_quantity').val('".$plan_cut_qnty."');\n";
			echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
			// echo "change_shipping_status(0);\n";
		}

  	}
 	exit();
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$country_id = $dataArr[4];
	$qty_source=0;
	if($dataArr[5]==28) $qty_source=4; //Sewing Input
	else if($dataArr[5]==29) $qty_source=5;//Sewing Output
	else if($dataArr[5]==30) $qty_source=7;//Iron Output
	else if($dataArr[5]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[5]==32) $qty_source=7;//Iron Output
	else if($dataArr[5]==91) $qty_source=7;//Iron Output
	else if($dataArr[5]==103) $qty_source=11;//Poly Entry

	$is_control = $dataArr[6];
	if($is_control !=1){$qty_source=0;}

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//#############################################################################################//
	// order wise - color level, color and size level

	$ex_fac_value=array();

	//$variableSettings=2;
	if($qty_source!=0)
	{
		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{

				$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";

				$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,sum(case when entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a
	                    ,pro_ex_factory_mst m, pro_ex_factory_dtls ex where  ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
	                    and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}
			}
			else
			{
				$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id";

				$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,sum(case when entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a
	                    ,pro_ex_factory_mst m, pro_ex_factory_dtls ex where  ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
	                    and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}
			}
		}
		else if( $variableSettings==3 ) //color and size level
		{
				$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type='$qty_source' group by a.color_size_break_down_id
										union all 
										select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");

	 			foreach($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
				}

				$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,a.size_number_id,sum(case when m.entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when m.entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a,pro_ex_factory_mst m
	                    , pro_ex_factory_dtls ex where ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
	                    and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id, a.size_number_id");
	 			foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];


				}

				 $sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
						from wo_po_color_size_breakdown a
						where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";
		}
		else // by default color and size level
		{

				$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' group by a.color_size_break_down_id
										union all 
										select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
				foreach($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
				}

				$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,a.size_number_id,sum(case when m.entry_form<>85 then  ex.production_qnty else 0 end ) - sum(case when m.entry_form=85 then  ex.production_qnty else 0 end ) as ex_production_qnty from wo_po_color_size_breakdown a,pro_ex_factory_mst m
	                    , pro_ex_factory_dtls ex where ex.color_size_break_down_id=a.id and m.id=ex.mst_id and m.status_active=1 and m.is_deleted=0 and ex.status_active=1 and ex.is_deleted=0
	                    and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id, a.size_number_id");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}


				$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
						from wo_po_color_size_breakdown a
						where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";

		}
    }
    else // if preceding process =0 in variable setting then plan cut quantity will show
	{
		if( $variableSettings==2 ) // color level
		{

			/*$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(case when m.entry_form<>85 then b.production_qnty else 0 end ) - sum(case when m.entry_form=85 then b.production_qnty else 0 end ) as production_qnty
				from wo_po_color_size_breakdown a ,pro_ex_factory_mst m, pro_ex_factory_dtls b where a.po_break_down_id=m.po_break_down_id and m.id=b.mst_id and m.status_active=1 and m.is_deleted=0 and  a.id=b.color_size_break_down_id
				and  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";*/

			$dtlsData = sql_select("SELECT a.color_number_id, sum(case when m.entry_form<>85 then b.production_qnty else 0 end ) - sum(case when m.entry_form=85 then b.production_qnty else 0 end ) as production_qnty
				from wo_po_color_size_breakdown a ,pro_ex_factory_mst m, pro_ex_factory_dtls b where a.po_break_down_id=m.po_break_down_id and m.id=b.mst_id and m.status_active=1 and m.is_deleted=0 and  a.id=b.color_size_break_down_id
				and  a.po_break_down_id='$po_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.color_number_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_number_id')]] = $row[csf('production_qnty')];
			}

			$sql = "SELECT color_number_id, sum(plan_cut_qnty) as plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id order by color_number_id"; //color_number_id, id		


		}
		else if( $variableSettings==3 ) //color and size level
		{

			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(case when b.entry_form<>85 then a.production_qnty else 0 end)-sum(case when b.entry_form=85 then a.production_qnty else 0 end) as production_qnty
										from pro_ex_factory_dtls a,pro_ex_factory_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0   group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			}

			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order"; //color_number_id, id


		}
		else // by default color and size level
		{


			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(case when b.entry_form<>85 then a.production_qnty else 0 end)-sum(case when b.entry_form=85 then a.production_qnty else 0 end) as production_qnty
										from pro_ex_factory_dtls a,pro_ex_factory_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0   group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			}

			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";//color_number_id, id
		}
	}


	$colorResult = sql_select($sql);
 	$colorHTML="";
	$colorID='';
	$chkColor = array();
	$i=0;$totalQnty=0;
	if($qty_source!=0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{

				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
				$totalQnty += $color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];
				}
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

				$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
				$exfac_qnty=$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];

				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
			}
			$i++;
		}
	}

	if($qty_source==0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color_size_qnty_array[$color[csf("color_number_id")]]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
				$totalQnty += $color[csf("plan_cut_qnty")]-$color_size_qnty_array[$color[csf("color_number_id")]];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];
				}
						 $bundle_mst_data="";
						 $bundle_dtls_data="";
					 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];

 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
			}

			$i++;
		}
	}
	//echo $colorHTML;die;
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	?>
	<div style="width:930px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="110" align="center">Ex-Fact. Date</th>
                <th width="110" align="center">Ex-Fact. Qnty</th>
                <th width="120" align="center">Invoice No</th>
                <th width="120" align="center">LC/SC No</th>
                <th align="center">Challan No</th>
            </thead>
    	</table>
    </div>
	<div style="width:930px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
		<?
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,ex_factory_date,ex_factory_qnty,location,lc_sc_no,invoice_no,challan_no from  pro_ex_factory_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and entry_form<>85 and status_active=1 and is_deleted=0 order by id");
 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0)
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";

				$total_production_qnty+=$selectResult[csf('ex_factory_qnty')];

				$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='".$selectResult[csf('invoice_no')]."'");
				foreach($sqlEx as $val)
				{
					if($val[csf("is_lc")]==1) //  lc
						$lc_sc = $lc_num_arr[$val[csf('lc_sc_id')]];
					else
						$lc_sc = $sc_num_arr[$val[csf('lc_sc_id')]];

					$invoiceNo = $val[csf('invoice_no')];
				}
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_exfactory_form_data','requires/garments_delivery_entry_controller');" >
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="110" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td width="110" align="center"><p><? echo change_date_format($selectResult[csf('ex_factory_date')]); ?></p></td>
                    <td width="110" align="center"><p><? echo $selectResult[csf('ex_factory_qnty')]; ?></p></td>
                    <td width="120" align="center"><p><? echo $invoiceNo; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><? echo $lc_sc; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $selectResult[csf('challan_no')]; ?>&nbsp;</p></td>
                </tr>
			<?
			$i++;
			}
			?>
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>
<?
	exit();
}

if($action=="show_country_listview")
{
	$country_short_library=return_library_array( "select id, short_name from  lib_country", "id", "short_name"  );
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Item Name</th>
            <th width="80">Country</th>
            <th width="80">Country Short Name</th>
            <th width="75">Shipment Date</th>
            <th>Order Qty.</th>
        </thead>
    </table>
	<div id="scroll_body" style="width:460px; max-height:450px; overflow-x:hidden;  overflow-y:auto;">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table" id="tbl_body_1">
		<?
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);">
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $country_short_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="75" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right"><?  echo $row[csf('order_qnty')]; ?></td>
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

if($action=="populate_exfactory_form_data")
{
	$ex_fac_value=array();
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where status_active=1");
	foreach($sqlEx as $row)
	{
		$invoice_data_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$invoice_data_arr[$row[csf("id")]]["invoice_no"]=$row[csf("invoice_no")];
		$invoice_data_arr[$row[csf("id")]]["is_lc"]=$row[csf("is_lc")];
		$invoice_data_arr[$row[csf("id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
	}

	$sqlResult =sql_select("SELECT id,garments_nature,po_break_down_id,item_number_id,country_id,location,ex_factory_date,ex_factory_qnty,total_carton_qnty,challan_no,invoice_no,lc_sc_no,carton_qnty,transport_com,remarks,shiping_status,entry_break_down_type,inspection_qty_validation,delivery_mst_id,is_posted_account,shiping_mode,foc_or_claim,inco_terms,actual_po,additional_info,additional_info_id  from pro_ex_factory_mst where id='$data' and status_active=1 and entry_form<>85 and is_deleted=0 order by id");

 	$actual_po=explode(",", $sqlResult[0][csf('actual_po')]);
 	$actual_po_no="";
 	foreach($actual_po as $val)
 	{
 		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
 	}
	$delivery_mst_id=$sqlResult[0][csf('delivery_mst_id')];
	$company_id=return_field_value( "company_id", "pro_ex_factory_delivery_mst"," status_active=1 and  is_deleted=0 and id='$delivery_mst_id'");
	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$company_id'");
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	// echo "select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$company_id'";
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";


	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry

	$is_control = $control_and_preceding[0][csf('is_control')];
	if($is_control !=1){$qty_source=0;}

 	foreach($sqlResult as $result)
	{

 		//echo "$('#cbo_location_name').val('".$result[csf('location')]."');\n";
		//echo "$('#txt_ex_factory_date').val('".change_date_format($result[csf('ex_factory_date')])."');\n";
		echo "$('#txt_ex_quantity').attr('placeholder','".$result[csf('ex_factory_qnty')]."');\n";
 		echo "$('#txt_ex_quantity').val('".$result[csf('ex_factory_qnty')]."');\n";
		echo "$('#txt_total_carton_qnty').val('".$result[csf('total_carton_qnty')]."');\n";
		//echo "$('#txt_challan_no').val('".$result[csf('challan_no')]."');\n";
		echo "$('#cbo_ins_qty_validation_type').val('".$result[csf('inspection_qty_validation')]."');\n";

		echo "$('#txt_invoice_no').val('');\n";
		echo "$('#txt_invoice_no').attr('placeholder','');\n";
 		echo "$('#txt_lc_sc_no').val('');\n";
		echo "$('#txt_lc_sc_no').attr('placeholder','');\n";



		//$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='".$result[csf('invoice_no')]."'");
		/*foreach($sqlEx as $val)
		{*/
		echo "$('#txt_invoice_no').val('".$invoice_data_arr[$result[csf('invoice_no')]]["invoice_no"]."');\n";
		echo "$('#txt_invoice_no').attr('placeholder','".$invoice_data_arr[$result[csf('invoice_no')]]["id"]."');\n";


		if($invoice_data_arr[$result[csf('invoice_no')]]["is_lc"]==1) //  lc
				$lc_sc =$lc_num_arr[$invoice_data_arr[$result[csf('invoice_no')]]["lc_sc_id"]];
			else
				$lc_sc =$sc_num_arr[$invoice_data_arr[$result[csf('invoice_no')]]["lc_sc_id"]];

		echo "$('#txt_lc_sc_no').val('".$lc_sc."');\n";
		echo "$('#txt_lc_sc_no').attr('placeholder','".$invoice_data_arr[$result[csf('invoice_no')]]["lc_sc_id"]."');\n";
		//}


 		echo "$('#txt_ctn_qnty').val('".$result[csf('carton_qnty')]."');\n";
		echo "$('#txt_transport_com').val('".$result[csf('transport_com')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#cbo_foc_claim').val('".$result[csf('foc_or_claim')]."');\n";
		echo "$('#shipping_mode').val('".$result[csf('shiping_mode')]."');\n";
		echo "$('#cbo_inco_term_id').val('".$result[csf('inco_terms')]."');\n";
		echo "$('#hidden_actual_po').val('".$result[csf('actual_po')]."');\n";
		echo "$('#txt_actual_po').val('".$actual_po_no."');\n";

		echo "$('#txt_add_info').val('".$result[csf('additional_info')]."');\n";
		echo "$('#hidden_add_info').val('".$result[csf('additional_info_id')]."');\n";

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_exFactory_entry',1,1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level


		$variableSettings = $result[csf('entry_break_down_type')];
		$is_posted_account = $result[csf('is_posted_account')];
		echo "$('#is_posted_account').val(".$is_posted_account.");\n";
		$disabled="";
		$msg="";
		if($is_posted_account==1)
		{
			$disabled="disabled";
			$msg="Already Posted In Accounting.";
			echo "disable_enable_fields( 'txt_order_no*cbo_ins_qty_validation_type*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty* txt_remark*shipping_status', 1 );\n";
		}
		else
		{
			echo "disable_enable_fields( 'txt_order_no*cbo_ins_qty_validation_type*txt_total_carton_qnty*txt_invoice_no*txt_lc_sc_no*txt_ctn_qnty*txt_remark*shipping_status', 0 );\n";
		}
		
		// echo "alert('ok');\n";
		//$variableSettings=2;
		if($qty_source!=0)
		{
			if( $variableSettings!=1 ) // gross level
			{
				$po_id = $result[csf('po_break_down_id')];
				$item_id = $result[csf('item_number_id')];
				$country_id = $result[csf('country_id')];

				$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_ex_factory_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
				foreach($sql_dtls as $row)
				{
					if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
				  	$amountArr[$index] = $row[csf('production_qnty')];
				}

				if( $variableSettings==2 ) // color level
				{
					if($db_type==0)
					{


						$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
								sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty
								from wo_po_color_size_breakdown a
								left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id";

						$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
								left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id");
						foreach($sql_exfac as $row_exfac)
						{
							$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

						}
					}
					else
					{
						$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
								sum(CASE WHEN b.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty
								from wo_po_color_size_breakdown a
								left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id";

						$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
								left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id");
						foreach($sql_exfac as $row_exfac)
						{
							$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

						}
					}

				}
				else if( $variableSettings==3 ) //color and size level
				{

						$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' group by a.color_size_break_down_id
											union all 
											select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
				foreach($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
				}

				$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
	                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
	                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id, a.size_number_id  order by a.item_number_id, a.color_number_id, a.size_number_id");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}

				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_number_id";*/
				$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
						from wo_po_color_size_breakdown a
						where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";

				}
				else // by default color and size level
				{


						$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type='$qty_source' group by a.color_size_break_down_id
											select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
				foreach($prodData as $row)
				{
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
				}

				$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
	                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
	                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id, a.size_number_id order by a.item_number_id, a.color_number_id, a.size_number_id");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}

				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_number_id";*/

				$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
						from wo_po_color_size_breakdown a
						where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) order by a.color_number_id, a.size_order";


				}
	 			$colorResult = sql_select($sql);
	 			//print_r($sql);die;
				$colorHTML="";
				$colorID='';
				$chkColor = array();
				$i=0;$totalQnty=0;$colorWiseTotal=0;
				foreach($colorResult as $color)
				{
					if( $variableSettings==2 ) // color level
					{
						$amount = $amountArr[$color[csf("color_number_id")]];
						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"'.$disabled.'></td></tr>';
						$totalQnty += $amount;
						$colorID .= $color[csf("color_number_id")].",";
					}
					else //color and size level
					{
						$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
						$amount = $amountArr[$index];
						if( !in_array( $color[csf("color_number_id")], $chkColor ) )
						{
							if( $i!=0 ) $colorHTML .= "</table></div>";
							$i=0;$colorWiseTotal=0;
							$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
							$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
							$chkColor[] = $color[csf("color_number_id")];
							$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
						}
	 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

						$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
						$exfac_qnty=$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];

						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'"'.$disabled.'></td></tr>';
						$colorWiseTotal += $amount;
					}
					$i++;
				}
				//echo $colorHTML;die;
				if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
				echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
				if( $variableSettings==3 )echo "$totalFn;\n";
				$colorList = substr($colorID,0,-1);
				echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			}//end if condtion $msg
		}
		if($qty_source==0)
			{
				if( $variableSettings!=1 ) // gross level
				{
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];


					$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, size_number_id, color_number_id from pro_ex_factory_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");

					foreach($sql_dtls as $row)
					{
						if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
					  	$amountArr[$index] = $row[csf('production_qnty')];
 					}

					if( $variableSettings==2 ) // color level
					{
						if($db_type==0)
						{

							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_ex_factory_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_ex_factory_dtls where is_deleted=0  ) as production_qnty from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 group by color_number_id";
						}
						else
						{
							$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
						from wo_po_color_size_breakdown a left join pro_ex_factory_dtls b on a.id=b.color_size_break_down_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";

						}
					}
					else if( $variableSettings==3 ) //color and size level
					{

							$dtlsData = sql_select("select a.color_size_break_down_id,
												sum( a.production_qnty) as production_qnty

												from pro_ex_factory_dtls a,pro_ex_factory_mst b where a.status_active=1 and a.mst_id=b.id  and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and b.entry_form<>85  group by a.color_size_break_down_id");


							foreach($dtlsData as $row)
							{
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							}

							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";


					}
					else // by default color and size level
					{


						$dtlsData = sql_select("select a.color_size_break_down_id,
												sum(a.production_qnty) as production_qnty					from pro_ex_factory_dtls a,pro_ex_factory_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id<>0 and b.entry_form<>85  group by a.color_size_break_down_id");

						foreach($dtlsData as $row)
						{
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
						}
						//print_r($color_size_qnty_array);

						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";

					}

					if($variableSettingsRej!=1)
					{
						$disable="";
					}
					else
					{
						$disable="disabled";
					}

		 			$colorResult = sql_select($sql);
		 			//print_r($sql_dtls);die;
					$colorHTML="";
					$colorID='';
					$chkColor = array();
					$i=0;$totalQnty=0;$colorWiseTotal=0;
					foreach($colorResult as $color)
					{

						if( $variableSettings==2 ) // color level
						{
							$amount = $amountArr[$color[csf("color_number_id")]];
							$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
							$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
							$totalQnty += $amount;
							$totalRejQnty += $rejectAmt;
							$colorID .= $color[csf("color_number_id")].",";
						}
						else //color and size level
						{
							$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];

							$amount = $amountArr[$index];
							//$amount = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];
							if( !in_array( $color[csf("color_number_id")], $chkColor ) )
							{
								if( $i!=0 ) $colorHTML .= "</table></div>";
								$i=0;
								$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].': <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
								$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
								$chkColor[] = $color[csf("color_number_id")];
								$totalFn .= "fn_total(".$color[csf("color_number_id")].");";

							}


							 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
		 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
							$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
 							$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];


							$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ></td></tr>';
							//$colorWiseTotal += $amount;
							 $bundle_dtls_data="";
							 $bundle_dtls_data="";
						}
						$i++;
					}
					//echo $colorHTML;die;
					if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$result[csf('production_quantity')].'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
					echo "$('#breakdown_td_id').html('".addslashes(trim($colorHTML))."');\n";
					if( $variableSettings==3 )echo "$totalFn;\n";
					$colorList = substr($colorID,0,-1);
					echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
				}
			}
		echo "$('#shipping_status').val('".$result[csf('shiping_status')]."');\n";
		echo "$('#posted_msg_td_id').text('".$msg."');\n";
		echo "$('#is_update_mood').val('1');\n";
		echo "set_field_level_access( ".$company_id.");\n";

	}

	exit();
}

//pro_ex_factory_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","id=$hidden_po_break_down_id and status_active=1 and is_deleted=0");
	if($is_projected_po == 2)
	{
		echo "35**Projected PO is not allow to delivery.";die();
	}

	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=32");
    if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
     
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }  //table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if(str_replace("'","",$txt_system_id)=="")
		{
			$delivery_mst_id=return_next_id("id", "pro_ex_factory_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GDE', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from pro_ex_factory_delivery_mst where company_id=$cbo_company_name and entry_form!=85 $mrr_cond order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));

			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location_id, challan_no, buyer_id, transport_supplier, delivery_date, lock_no, driver_name, truck_no, dl_no, mobile_no, do_no, gp_no, destination_place, forwarder,forwarder_2,source,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks, inserted_by, insert_date";
			$data_array_delivery="(".$delivery_mst_id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_name.",".$cbo_location_name.",".$new_sys_number[2].",".$cbo_buyer_name.",".$cbo_transport_company.",".$txt_ex_factory_date.",".$txt_lock_no.",".$txt_driver_name.",".$txt_truck_no.",".$txt_dl_no.",".$txt_mobile_no.",".$txt_do_no.",".$txt_gp_no.",".$txt_destination.",".$cbo_forwarder.",".$cbo_forwarder_2.",".$cbo_source.",".$cbo_del_company.",".$cbo_delivery_location.",".$cbo_delivery_floor.",".$txt_attention.",".$txt_remarks.",".$user_id.",'".$pc_date_time."')";
			$mrr_no=$new_sys_number[0];
			$mrr_no_challan=$new_sys_number[2];

		}
		else
		{
			$delivery_mst_id=str_replace("'","",$txt_system_id);
			$mrr_no=str_replace("'","",$txt_system_no);
			$mrr_no_challan=str_replace("'","",$txt_challan_no);

			$field_array_delivery="company_id*location_id*buyer_id*transport_supplier*delivery_date*lock_no*driver_name*truck_no*dl_no*mobile_no*do_no*gp_no*destination_place*forwarder*forwarder_2*source*delivery_company_id*delivery_location_id*delivery_floor_id*attention*remarks*updated_by*update_date";
			$data_array_delivery="".$cbo_company_name."*".$cbo_location_name."*".$cbo_buyer_name."*".$cbo_transport_company."*".$txt_ex_factory_date."*".$txt_lock_no."*".$txt_driver_name."*".$txt_truck_no."*".$txt_dl_no."*".$txt_mobile_no."*".$txt_do_no."*".$txt_gp_no."*".$txt_destination."*".$cbo_forwarder."*".$cbo_forwarder_2."*".$cbo_source."*".$cbo_del_company."*".$cbo_delivery_location."*".$cbo_delivery_floor."*".$txt_attention."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";


		}

		$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
		$country_exfactory_qty=$country_exfactory_qty+str_replace("'","",$txt_ex_quantity);

		//if($country_exfactory_qty>=$country_order_qty) $country_order_status=3; else $country_order_status=str_replace("'","",$shipping_status);
		$country_order_status=str_replace("'","",$shipping_status);
		$cbo_inco_term_id = str_replace("'","",$cbo_inco_term_id);
		//----------Compare buyer inspection qty and ex-factory qty for validation----------------

		/*if($is_control==1 && $user_level!=2)
		{
			$cbo_ins_qty_validation_type=str_replace("'","",$cbo_ins_qty_validation_type);
			if($cbo_ins_qty_validation_type==2)
			{
			$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and inspection_status=1 and status_active=1 and is_deleted=0");
				if($country_insfection_qty < $country_exfactory_qty)
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}

			}
			else
			{
			$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and inspection_status=1 and status_active=1 and is_deleted=0");

			$order_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and status_active=1 and is_deleted=0");

				if($order_insfection_qty < $order_exfactory_qty+str_replace("'","",$txt_ex_quantity))
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}
			}

		}*/
		//--------------------------------------------------------------Compare end;

		$id=return_next_id("id", "pro_ex_factory_mst", 1);

  		$field_array1="id, delivery_mst_id, garments_nature, po_break_down_id,actual_po,additional_info,additional_info_id, item_number_id, country_id, location, ex_factory_date, ex_factory_qnty, total_carton_qnty, challan_no, invoice_no, lc_sc_no, carton_qnty, transport_com, remarks, shiping_status, entry_break_down_type,inspection_qty_validation,shiping_mode,foc_or_claim, inco_terms, inserted_by, insert_date";
		$data_array1="(".$id.",".$delivery_mst_id.",".$garments_nature.",".$hidden_po_break_down_id.",".$hidden_actual_po.",".$txt_add_info.",".$hidden_add_info.", ".$cbo_item_name.",".$cbo_country_name.",".$cbo_location_name.",".$txt_ex_factory_date.",".$txt_ex_quantity.",".$txt_total_carton_qnty.",".$mrr_no_challan.",'".$invoice_id."','".$lcsc_id."',".$txt_ctn_qnty.",".$txt_transport_com.",".$txt_remark.",".$shipping_status.",".$sewing_production_variable.",".$cbo_ins_qty_validation_type.",".$shipping_mode.",".$cbo_foc_claim.",".$cbo_inco_term_id.",".$user_id.",'".$pc_date_time."')";


		//echo "INSERT INTO pro_ex_factory_delivery_mst (".$field_array1.") VALUES ".$data_array1;die;

 		//$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);

		//echo "10**update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name";die;

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3)",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active in(1,2,3) and is_deleted=0");
		if($country_wise_status>0) $order_status=2; else $order_status=3;
 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);


		// pro_ex_factory_dtls table entry here ----------------------------------///


		$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id
								union all 
								select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
		/*echo "select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id
								union all 
								select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id";die;*/
		foreach($prodData as $row)
		{
			$color_size_data[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
		}

		$sql_exfac=sql_select("SELECT a.color_size_break_down_id, sum(a.production_qnty) as ex_production_qnty
							from pro_ex_factory_dtls a, pro_ex_factory_mst b
							where b.id=a.mst_id and a.status_active=1 and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name
							group by a.color_size_break_down_id");
		foreach($sql_exfac as $row_exfac)
		{
			$ex_fac_data[$row_exfac[csf("color_size_break_down_id")]]=$row_exfac[csf("ex_production_qnty")];

		}


		$field_array="id,mst_id,color_size_break_down_id,production_qnty";

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id<>0  and status_active in(1,2,3)  and is_deleted=0  order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue);
 			$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				if($is_control==1 && $user_level!=2)
				{
					$garments_delivery_data=0;
					if($colorSizeNumberIDArr[1]>0)
					{
						$garments_delivery_data=$color_size_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]-$ex_fac_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]];
						if(($colorSizeNumberIDArr[1]*1)>($garments_delivery_data*1))
						{
							echo "35**Delivery Quantity Not Over Finish Qnty";
							check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}

				if($j==0)$data_array = "(".$dtls_id.",".$id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				$dtls_id=$dtls_id+1;
 				$j++;
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
 			$rowEx = explode("***",$colorIDvalue);
			$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				if($is_control==1 && $user_level!=2)
				{
					$garments_delivery_data=0;
					if($colorSizeValue>0)
					{
						$garments_delivery_data=$color_size_data[$colSizeID_arr[$index]]-$ex_fac_data[$colSizeID_arr[$index]];
						if(($colorSizeValue*1)>($garments_delivery_data*1))
						{
							echo "35**Delivery Qnty Quantity Not Over Finish Qnty";
							check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}

				if($j==0)$data_array = "(".$dtls_id.",".$id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array .= ",(".$dtls_id.",".$id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$dtls_id=$dtls_id+1;
 				$j++;
			}
		}
		//Ref Closing if not close by Ref close page
		$shipping_status_id=str_replace("'","",$shipping_status);
		$cbo_ref_type=163;$unclose_id=1;
		$txt_ref_cls_date=str_replace("'","",$txt_ex_factory_date);
		//$ref_close_max_id=return_field_value("max(id) as max_id","inv_reference_closing","inv_pur_req_mst_id=$hidden_po_break_down_id and reference_type=163 and status_active in(1) and is_deleted=0","max_id");
		if($shipping_status_id==3)
		{
			$ref_id=return_next_id( "id", "inv_reference_closing", 1 ) ;//closing_status
			$field_array_ref_close="id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";
			$data_array_ref_close="(".$ref_id.",".$cbo_company_name.",'".$txt_ref_cls_date."',".$cbo_ref_type.",".$unclose_id.",".$hidden_po_break_down_id.",".$txt_order_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$DeliveryrID=sql_insert("inv_reference_closing",$field_array_ref_close,$data_array_ref_close,1);
			//echo "10**INSERT INTO inv_reference_closing (".$field_array_ref_close.") VALUES ".$data_array_ref_close;die;

		}



		$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);
		$DeliveryrID=true;
		//echo "insert into pro_ex_factory_delivery_mst ($field_array_delivery) values $data_array_delivery";die;
		if(str_replace("'","",$txt_system_id)=="")
		{
			$DeliveryrID=sql_insert("pro_ex_factory_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$DeliveryrID=sql_update("pro_ex_factory_delivery_mst",$field_array_delivery,$data_array_delivery,"id",str_replace("'","",$txt_system_id),1);
		}
		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		}

		$invoiceID=true;
		if($invoice_id!="")  
		{			
			$field_array_invoice="ex_factory_date*shipping_mode*total_carton_qnty";
			$data_array_invoice="".$txt_ex_factory_date."*".$shipping_mode."*".$txt_total_carton_qnty;
 			//$invoiceID=sql_update("com_export_invoice_ship_mst","ex_factory_date",$txt_ex_factory_date,"id",$invoice_id,1);
			$invoiceID=sql_update("com_export_invoice_ship_mst",$field_array_invoice,$data_array_invoice,"id",$invoice_id,1);
		}
		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**$rID  && $DeliveryrID && $sts_ex_mst && $sts_ex && $sts_country";

		if($db_type==0)
		{

			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $DeliveryrID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID  && $DeliveryrID && $sts_ex_mst && $sts_ex && $sts_country)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID  && $DeliveryrID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID  && $DeliveryrID && $sts_ex_mst && $sts_ex && $sts_country)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$delivery_mst_id=str_replace("'","",$txt_system_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$cbo_inco_term_id=str_replace("'","",$cbo_inco_term_id);


		$is_gate_passed = return_field_value("sys_number","inv_gate_pass_mst","challan_no='$mrr_no' and basis=12 and status_active=1 and is_deleted=0");
		if($is_gate_passed != "")
		{
			echo "36**Gate Pass Found($is_gate_passed).Update Restricted!";disconnect($con); die();
		}

		/*$buyer_id_chack=return_field_value("buyer_id","pro_ex_factory_delivery_mst","id=$delivery_mst_id","buyer_id");
		if($buyer_id_chack!=$cbo_buyer_name)
		{
			echo "50";die;
		}*/

		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}



		$field_array1="garments_nature*location*ex_factory_date*actual_po*additional_info*additional_info_id*ex_factory_qnty*total_carton_qnty*challan_no*invoice_no*lc_sc_no*carton_qnty*transport_com*remarks*shiping_status*entry_break_down_type*inspection_qty_validation*shiping_mode*foc_or_claim*inco_terms*updated_by*update_date";
		$data_array1="".$garments_nature."*".$cbo_location_name."*".$txt_ex_factory_date."*".$hidden_actual_po."*".$txt_add_info."*".$hidden_add_info."*".$txt_ex_quantity."*".$txt_total_carton_qnty."*".$txt_challan_no."*'".$invoice_id."'*'".$lcsc_id."'*".$txt_ctn_qnty."*".$txt_transport_com."*".$txt_remark."*".$shipping_status."*".$sewing_production_variable."*".$cbo_ins_qty_validation_type."*".$shipping_mode."*".$cbo_foc_claim."*".$cbo_inco_term_id."*".$user_id."*'".$pc_date_time."'";

		$field_array_delivery="company_id*location_id*buyer_id*transport_supplier*delivery_date*lock_no*driver_name*truck_no*dl_no*mobile_no*do_no*gp_no*destination_place*forwarder*forwarder_2*source*delivery_company_id*delivery_location_id*delivery_floor_id*attention*remarks*updated_by*update_date";
		$data_array_delivery="".$cbo_company_name."*".$cbo_location_name."*".$cbo_buyer_name."*".$cbo_transport_company."*".$txt_ex_factory_date."*".$txt_lock_no."*".$txt_driver_name."*".$txt_truck_no."*".$txt_dl_no."*".$txt_mobile_no."*".$txt_do_no."*".$txt_gp_no."*".$txt_destination."*".$cbo_forwarder."*".$cbo_forwarder_2."*".$cbo_source."*".$cbo_del_company."*".$cbo_delivery_location."*".$cbo_delivery_floor."*".$txt_attention."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";

 		//$rID=sql_update("pro_ex_factory_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $country_order_qty."**".$data_array;die;

		// pro_ex_factory_mst table data entry here
		$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id<>$txt_mst_id");
		$country_exfactory_qty=$country_exfactory_qty+str_replace("'","",$txt_ex_quantity);

		//if($country_exfactory_qty>=$country_order_qty) $country_order_status=3; else $country_order_status=str_replace("'","",$shipping_status);
		$country_order_status=str_replace("'","",$shipping_status);


		//----------Compare buyer inspection qty and ex-factory qty for validation----------------
		/*if($is_control==1 && $user_level!=2)
		{

			$cbo_ins_qty_validation_type=str_replace("'","",$cbo_ins_qty_validation_type);
			if($cbo_ins_qty_validation_type==2)
			{
			$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and inspection_status=1 and status_active=1 and is_deleted=0");
				if($country_insfection_qty < $country_exfactory_qty)
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}

			}
			else
			{
			$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and inspection_status=1 and status_active=1 and is_deleted=0");

			$order_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

				if($order_insfection_qty < $order_exfactory_qty+str_replace("'","",$txt_ex_quantity))
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}
			}

		}*/
		//--------------------------------------------------------------Compare end;





		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3)",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active in(1,2,3) and is_deleted=0");
		if($country_wise_status>0) $order_status=2; else $order_status=3;
 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);



		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			// pro_ex_factory_dtls table entry here ----------------------------------///

			$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id
								union all 
								select a.color_size_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type=10 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{
				$color_size_data[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}

			$sql_exfac=sql_select("SELECT a.color_size_break_down_id, sum(a.production_qnty) as ex_production_qnty
								from pro_ex_factory_dtls a, pro_ex_factory_mst b
								where a.status_active=1 and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name 
								and b.id=a.mst_id and b.id !=$txt_mst_id
								group by a.color_size_break_down_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_data[$row_exfac[csf("color_size_break_down_id")]]=$row_exfac[csf("ex_production_qnty")];

			}



			$field_array="id, mst_id,color_size_break_down_id,production_qnty";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id<>0  and status_active in(1,2,3) and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue);
				$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					if($is_control==1 && $user_level!=2)
					{
						$garments_delivery_data=0;
						if($colorSizeNumberIDArr[1]>0)
						{
							$garments_delivery_data=$color_size_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]-$ex_fac_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]];
							if(($colorSizeNumberIDArr[1]*1)>($garments_delivery_data*1))
							{
								echo "35**Delivery Qnty Quantity Not Over Finish Qnty";
								check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = explode("***",$colorIDvalue);
				$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;

					if($is_control==1 && $user_level!=2)
					{
						$garments_delivery_data=0;
						if($colorSizeValue>0)
						{
							$garments_delivery_data=$color_size_data[$colSizeID_arr[$index]]-$ex_fac_data[$colSizeID_arr[$index]];
							if(($colorSizeValue*1)>($garments_delivery_data*1))
							{
								echo "35**Delivery Quantity Not Over Finish Qnty";
								check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}


					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			//$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		}//end cond
		//=======Ref Closing if not close by Ref close page=====
		$shipping_status_id=str_replace("'","",$shipping_status);
		$cbo_ref_type=163;$unclose_id=1;
		$txt_ref_cls_date=str_replace("'","",$txt_ex_factory_date);
		$ref_close_max_id=return_field_value("max(id) as max_id","inv_reference_closing","inv_pur_req_mst_id=$hidden_po_break_down_id and reference_type=163 and status_active in(1) and is_deleted=0","max_id");
		if($shipping_status_id==3)
		{
			$ref_id=return_next_id( "id", "inv_reference_closing", 1 ) ;//closing_status
			$field_array_ref_close="id,company_id,closing_date,reference_type,closing_status,inv_pur_req_mst_id,mrr_system_no,inserted_by,insert_date";
			$data_array_ref_close="(".$ref_id.",".$cbo_company_name.",'".$txt_ref_cls_date."',".$cbo_ref_type.",".$unclose_id.",".$hidden_po_break_down_id.",".$txt_order_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$DeliveryrID=sql_insert("inv_reference_closing",$field_array_ref_close,$data_array_ref_close,1);
			//echo "10**INSERT INTO inv_reference_closing (".$field_array_ref_close.") VALUES ".$data_array_ref_close;die;
		}
		else
		{
			$shipping_status_id=str_replace("'","",$shipping_status);//updated_by*update_date
			$cbo_ref_type=163;$unclose_id=0;
			$txt_ref_cls_date=str_replace("'","",$txt_ex_factory_date);
			$DeliveryrID = execute_query("update inv_reference_closing set closing_status=$unclose_id,closing_date='".$txt_ref_cls_date."',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where inv_pur_req_mst_id=$hidden_po_break_down_id and id=$ref_close_max_id",1);
			//echo "10**update inv_reference_closing set closing_status=$unclose_id,closing_date='".$txt_ref_cls_date."',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where inv_pur_req_mst_id=$hidden_po_break_down_id and id=$ref_close_max_id";die;
		}

		//echo "10**INSERT INTO inv_reference_closing (".$field_array_ref_close.") VALUES ".$data_array_ref_close;die;
		

		$dtlsrDelete = execute_query("delete from pro_ex_factory_dtls where mst_id=$txt_mst_id",1);

		$rID=$deliveryrID=$dtlsrID=$invoiceID=true;
		$rID=sql_update("pro_ex_factory_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		$deliveryrID=sql_update("pro_ex_factory_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$delivery_mst_id."",1);
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		}
		if($invoice_id!="")
		{
			$field_array_invoice="ex_factory_date*shipping_mode*total_carton_qnty";
			$data_array_invoice="".$txt_ex_factory_date."*".$shipping_mode."*".$txt_total_carton_qnty;
			$invoiceID=sql_update("com_export_invoice_ship_mst",$field_array_invoice,$data_array_invoice,"id",$invoice_id,1);
 			//$invoiceID=sql_update("com_export_invoice_ship_mst","ex_factory_date",$txt_ex_factory_date,"id",$invoice_id,1);
		}

		//echo "10**".$data_array_invoice; die;

		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $deliveryrID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $dtlsrDelete)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID && $deliveryrID && $sts_country && $sts_ex && $sts_ex_mst)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $deliveryrID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $dtlsrDelete)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID && $deliveryrID && $sts_country && $sts_ex && $sts_ex_mst)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
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

		$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

		if($country_exfactory_qty>=$country_order_qty) $country_order_status=3;
		else if($country_exfactory_qty>0 && $country_exfactory_qty < $country_order_qty) $country_order_status=2;
		else $country_order_status=1;

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if($country_wise_status>0 && $country_exfactory_qty>0) $order_status=2;
		else if($country_wise_status>0 && $country_exfactory_qty<=0) $order_status=1;
		else $order_status=3;

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


if($action=="ex_factory_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	echo load_html_head_contents("Garments Delivery Info","../".$data[5], 1, 1, $unicode,'','');
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$location_library_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
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
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,delivery_company_id,delivery_location_id,delivery_floor_id from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
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
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	?>
	<div style="width:910px; margin-top:5px;">
	    <table width="900" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $data[5].$image_location; ?>" height="60" width="200"></td>
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
	            <td colspan="5" style="font-size:x-large; padding-left:252px;"><strong>Delivery Challan<?// echo $data[3]; ?></strong></td>
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
	        <tr>
	        	<td style="font-size:16px;width:150px;"><strong>Delivery Company :</strong></td>
	            <td style="font-size:16px;"><? echo $company_library[$delivery_company]; ?> </td>
	            <td style="font-size:16px;width:160px;"><strong>Delivery Location :</strong></td>
	            <td style="font-size:16px;"><? echo $location_library_arr[$delivery_location]; ?> </td>
	            <td style="font-size:16px;width:150px;"><strong>Delivery Floor :</strong></td>
	            <td style="font-size:16px;"><? echo $floor_library_arr[$delivery_floor]; ?> </td>

	        </tr>
	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				$sql="SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty,shiping_mode, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po  from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
			}
			else if($db_type==0)
			{
				$sql="SELECT po_break_down_id, group_concat(invoice_no) as invoice_no,shiping_mode, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks ,group_concat(actual_po) as actual_po from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
			}
			//echo $sql;die;
			$result=sql_select($sql);
			$table_width=850;
			$col_span=6;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="120">Style Ref.</th>
	            <th width="120" >Order No</th>
	            <th width="100" >Buyer</th>
	            <th width="200" >Invoice No</th>
	            <th width="50">Ship Mode</th>
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
	                <td style="font-size:12px;"><p>
	                <?
	                 $actual_po=$row[csf("actual_po")];
	                if($actual_po)
	                {
	                	$actual_po_no="";
	                	$actual_po=explode(",", $actual_po);
	                	foreach($actual_po as $val)
	                	{

	                		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
	                	}
	                	echo $actual_po_no;
	                }
	                else  echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
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
	                  <td style="font-size:12px;" align="center"><p><? echo $shipment_mode[$row[csf("shiping_mode")]];?> </p></td>
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
	            <td align="right" style="font-size:12px;"><strong><? echo number_format($tot_carton_qnty,0,"",""); ?></strong></td>
	            <td align="right" style="font-size:12px;"><strong><? echo number_format($tot_qnty,0,"",""); ?></strong></td>
	        </tr>
	    </table>
	    <h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
	    <script type="text/javascript" src="../<? echo $data[5];?>js/jquery.js"></script>
	    <script type="text/javascript" src="../<? echo $data[5];?>js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
			 <?
	            echo signature_table(63, $data[0], $table_width."px");
	         ?>
		</div>
	<?
	exit();
}

if($action=="ex_factory_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	echo load_html_head_contents("Garments Delivery Info","../".$data[5], 1, 1, $unicode,'','');
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$location_library_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
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
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,delivery_company_id,delivery_location_id,delivery_floor_id from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
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
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	?>
	<div style="width:910px; margin-top:5px;">
	    <table width="900" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $data[5].$image_location; ?>" height="60" width="200"></td>
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
	            <td colspan="5" style="font-size:x-large; padding-left:252px;"><strong>Delivery Challan<?// echo $data[3]; ?></strong></td>
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
	        <tr>
	        	<td style="font-size:16px;width:150px;"><strong>Delivery Company :</strong></td>
	            <td style="font-size:16px;"><? echo $company_library[$delivery_company]; ?> </td>
	            <td style="font-size:16px;width:160px;"><strong>Delivery Location :</strong></td>
	            <td style="font-size:16px;"><? echo $location_library_arr[$delivery_location]; ?> </td>
	            <td style="font-size:16px;width:150px;"><strong>Delivery Floor :</strong></td>
	            <td style="font-size:16px;"><? echo $floor_library_arr[$delivery_floor]; ?> </td>

	        </tr>
	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				$sql="SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty,shiping_mode, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po  from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
			}
			else if($db_type==0)
			{
				$sql="SELECT po_break_down_id, group_concat(invoice_no) as invoice_no,shiping_mode, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks ,group_concat(actual_po) as actual_po from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id,shiping_mode";
			}
			//echo $sql;die;
			$result=sql_select($sql);
			$table_width=970;
			$col_span=7;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="120">Style Ref.</th>
	            <th width="120" >Order No</th>
	            <th width="120" >Act. PO No</th>
	            <th width="100" >Buyer</th>
	            <th width="200" >Invoice No</th>
	            <th width="50">Ship Mode</th>
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
	                <td style="font-size:12px;"><p>
	                <?	                
	                echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
	                <?
	                $actual_po=$row[csf("actual_po")];
	                if($actual_po)
	                {
	                	$actual_po_no="";
	                	$actual_po=explode(",", $actual_po);
	                	foreach($actual_po as $val)
	                	{

	                		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
	                	}
	                	echo $actual_po_no;
	                } ?>&nbsp;</p></td>

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
	                  <td style="font-size:12px;" align="center"><p><? echo $shipment_mode[$row[csf("shiping_mode")]];?> </p></td>
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
	            <td align="right" style="font-size:12px;"><strong><? echo number_format($tot_carton_qnty,0,"",""); ?></strong></td>
	            <td align="right" style="font-size:12px;"><strong><? echo number_format($tot_qnty,0,"",""); ?></strong></td>
	        </tr>
	    </table>
	    <h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
	    <script type="text/javascript" src="../<? echo $data[5];?>js/jquery.js"></script>
	    <script type="text/javascript" src="../<? echo $data[5];?>js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
			 <?
	            echo signature_table(63, $data[0], $table_width."px");
	         ?>
		</div>
	<?
	exit();
}

if($action=="ex_factory_print_new")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	if($data[5]=="" or $data[5]==0)
	{
		$data[5]=$data[0];
	}
	$id_ref=str_replace("'","",$data[4]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$country_short_library=return_library_array( "select id, short_name from  lib_country", "id", "short_name"  );
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
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
		$forwarder_2=$row[csf("forwarder_2")];
		$system_num=$row[csf("sys_number")];
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];

	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:800px; margin-top:10px;">
	    <table width="800" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="60" width="200"></td>
	            <td colspan="4" align="center"  style="font-size:xx-large;"><strong><? echo $company_library[$data[5]]; ?></strong></td>
	            <td rowspan="2" id="barcode_img_id"></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="4" align="center" style="font-size:12px;">
					<?

						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[5]");
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
					if($forwarder>0)
					{
						$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
		  				foreach($supplier_sql as $row)
		  				{

		  				$address_1=$row[csf("address_1")];
		  				$address_2=$row[csf("address_2")];
		  				$address_3=$row[csf("address_3")];
		  				$address_4=$row[csf("address_4")];
		  				$contact_no=$row[csf("contact_no")];
		  				}
					}else
					{
						$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
						foreach($supplier_sql as $row)
						{

						$address_1=$row[csf("address_1")];
						$address_2=$row[csf("address_2")];
						$address_3=$row[csf("address_3")];
						$address_4=$row[csf("address_4")];
						$contact_no=$row[csf("contact_no")];
						}
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
	         <tr style="font-size:15px;">
	        	<td width="100" valign="top" ><strong>Challan No :</strong></td>
	            <td width="200" valign="top" ><? echo $challan_no; ?></td>
	            <td width="100" valign="top" ><strong>Driver Name :</strong></td>
	            <td width="120" valign="top" ><? echo $driver_name; ?> </td>
	            <td width="80" valign="top" ><strong>Date:</strong></td>
	            <td valign="top" ><? echo change_date_format($data[2]); ?> </td>
	        </tr>
	        <tr style="font-size:15px;">
	        	<td valign="top" ><strong><? if( $forwarder>0) { echo 'C&F Name:';} else {echo 'Forwarding Agent';}?></strong></td>
	            <td valign="top" ><? if( $forwarder>0){echo $supplier_library[$forwarder];} else { echo $supplier_library[$forwarder_2];}  ?></td>
	            <td valign="top" ><strong>Mobile Num :</strong></td>
	            <td valign="top" ><? echo $mobile_no; ?> </td>
	            <td valign="top" ><strong>Do No:</strong></td>
	            <td valign="top" ><? echo $do_no; ?> </td>
	        </tr>
			<tr style="font-size:15px;">
	            <td valign="top" ><strong>Address:</strong></td>
	            <td valign="top" ><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
	            <td ><strong>DL No:</strong></td>
	            <td ><? echo $dl_no; ?> </td>
	            <td ><strong>GP No:</strong></td>
	            <td ><? echo $gp_no; ?> </td>
	        </tr>
	        <tr style="font-size:15px;">
	            <td valign="top" ><strong>Trns. Comp:</strong></td>
	            <td valign="top" ><? echo $supplier_library[$supplier_name]; ?> </td>
	            <td ><strong>Truck No:</strong></td>
	            <td ><? echo $truck_no; ?> </td>
	            <td ><strong>Lock No:</strong></td>
	            <td ><? echo $lock_no; ?> </td>
	        </tr>
	         <tr style="font-size:15px;">
	            <td ><strong>Delivery Company:</strong></td>
	            <td ><? echo $company_library[$delivery_company]; ?> </td>
	            <td ><strong>Delivery Location:</strong></td>
	            <td ><? echo $location_library[$delivery_location]; ?> </td>
	             <td ><strong>Delivery Floor:</strong></td>
	            <td ><? echo $floor_library[$delivery_floor]; ?> </td>

	        </tr>
	        <tr style="font-size: 15px">
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        	<td><strong>Final Destination</strong>:</td>
	            <td><? echo $destination_place;?></td>
	        </tr>

	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id

			if($db_type==2)
			{
				$sql="SELECT c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
				order by a.style_ref_no";
			}
			else if($db_type==0)
			{
				$sql="SELECT c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks,group_concat(actual_po) as actual_po ,c.shiping_mode
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode
				order by a.style_ref_no";
			}
			//echo $sql;
			$result=sql_select($sql);
			$table_width=850;
			$col_span=10;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="20">SL</th>
	            <th width="60" >Buyer</th>
	            <th width="100" >Style Ref.</th>
	            <th width="100" >Order No</th>
	            <th width="60" >Country</th>
	            <th width="60" >Country Short Name</th>
	            <th width="130" >Item Name</th>
	            <th width="150" >Invoice No</th>
	            <th width="50">Ship Mode</th>
	            <th width="50">FOC/Claim</th>
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
	                <td style="font-size:12px;"><p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><?
	                 $actual_po=$row[csf("actual_po")];
	                if($actual_po)
	                {
	                	$actual_po_no="";
	                	$actual_po=explode(",", $actual_po);
	                	foreach($actual_po as $val)
	                	{

	                		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
	                	}
	                	echo $actual_po_no;
	                }
	                else  echo $row[csf("po_number")]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
	                <?
	                 $garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
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
	                <td align="center" style="font-size:12px;"><p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p></td>
	                <td align="center" style="font-size:12px;"><p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td style="font-size:12px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        </tbody>
	        <tr bgcolor="#CCCCCC">
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
	           
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;">&nbsp;</td>
	        </tr>

	    </table>
	    <h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
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

if($action=="ex_factory_print_new2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	$show_hide_delv_info = str_replace("'","",$data[5]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$vat_library=return_library_array( "select id, vat_number from lib_company", "id", "vat_number"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$country_short_library=return_library_array( "select id, short_name from  lib_country", "id", "short_name"  );
	//$destination_library=return_library_array( "select id, destination_place from pro_ex_factory_delivery_mst","id","destination_place"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$challan_no_full=$row[csf("sys_number")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$mobile_no=$row[csf("mobile_no")];
		$do_no=$row[csf("do_no")];
		$gp_no=$row[csf("gp_no")];
		$forwarder=$row[csf("forwarder")];
		$forwarder_2=$row[csf("forwarder_2")];
		$system_num=$row[csf("sys_number")];
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];
		$attention=$row[csf("attention")];
		$remarks=$row[csf("remarks")];

	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:900px; margin-top:10px; margin-left:55px;">

	    <br>

			<?php
			$table_width=950;
			$col_span=11;

					if($forwarder>0)
					{
						$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
		  				foreach($supplier_sql as $row)
		  				{

		  				$address_1=$row[csf("address_1")];
		  				$address_2=$row[csf("address_2")];
		  				$address_3=$row[csf("address_3")];
		  				$address_4=$row[csf("address_4")];
		  				$contact_no=$row[csf("contact_no")];
		  				}
					}else
					{
						$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
						foreach($supplier_sql as $row)
						{

						$address_1=$row[csf("address_1")];
						$address_2=$row[csf("address_2")];
						$address_3=$row[csf("address_3")];
						$address_4=$row[csf("address_4")];
						$contact_no=$row[csf("contact_no")];
						}
					}
	            ?>

		<!--<div style="width:<? //echo $table_width;?>px;">-->
	    	<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
		        <tr style="background-color:#fff;border-color:#fff;">
		            <td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
		            <td valign="top"  align="center"  style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


	                <div style="text-align:center;">
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
		                <span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
		                <span style="font-size:15px;"><strong>Delivery Challan</strong></span>
		            </div>


	                </td>
		            <td style="border:none; float:right;">
	                <span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
	                 <span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
	                <span style="float:left;"   id="barcode_img_id"></span>

	                </td>
	       		</tr>

	         </table>

	        <div style="width:950; margin-left:-50px;">
		         <table border="1" cellpadding="1" cellspacing="1" style="width:950px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table" >
			        <tr>
			        	<td width="160" style="font-size:14px;"><? if( $forwarder>0) { echo 'C&F Name:';} else {echo 'Forwarding Agent';}?></td>
			            <td width="160" style="font-size:14px;"><strong><? if( $forwarder>0){echo $supplier_library[$forwarder];} else { echo $supplier_library[$forwarder_2];}  ?></strong></td>
		                <td width="160" style="font-size:14px;">Trns. Comp:</td>
			            <td width="160" style="font-size:14px;"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>
		              	<td width="160" style="font-size:14px;">Do No:</td>
			            <td width="160" style="font-size:14px;"><strong><? echo $do_no; ?></strong></td>
			        </tr>
		            <tr>
		            	<td style="font-size:14px;">Address:</td>
			            <td style="font-size:14px;"><strong><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?></strong></td>
		                <td style="font-size:14px;">Driver Name :</td>
			            <td style="font-size:14px;"><strong><? echo $driver_name; ?></strong></td>
		                <td style="font-size:14px;">GP No:</td>
			            <td style="font-size:14px;"><strong><? echo $gp_no; ?></strong></td>
			        </tr>
		            <tr>
		           		<td style="font-size:14px;">Attention:</td>
			            <td style="font-size:14px;"><strong><? echo $attention; ?></strong></td>
		                <td style="font-size:14px;">Mobile No :</td>
			            <td style="font-size:14px;"><strong><? echo $mobile_no; ?></strong></td>
		                <td style="font-size:14px;">Lock No:</td>
			            <td style="font-size:14px;"><strong><? echo $lock_no; ?></strong></td>
		            </tr>
		             <tr>
		                <td style="font-size:14px;">DL No:</td>
			           	<td style="font-size:14px;"><strong><? echo $dl_no; ?></strong></td>
		                <td style="font-size:14px;">Final Destination:</td>
			            <td style="font-size:14px;"><strong><? echo $destination_place;?></strong></td>
			            <td style="font-size:14px;">Truck No:</td>
			            <td style="font-size:14px;"><strong><? echo $truck_no; ?></strong></td>
		            </tr>
		            <? if($show_hide_delv_info){?>
			        <tr>
		                <td style="font-size:14px;">Delivery Floor:</td>
			            <td style="font-size:14px;"><strong><? echo $floor_library[$delivery_floor]; ?></strong></td>
			            <td style="font-size:14px;">Delivery Company:</td>
			            <td style="font-size:14px;"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
			            <td style="font-size:14px;">Delivery Location:</td>
			            <td style="font-size:14px;"><strong><? echo $location_library[$delivery_location]; ?></strong></td>
		            </tr>
		            <? }?>
		            <tr>
		                <td style="font-size:14px;">Vat No.:</td> 
			            <td style="font-size:14px;"><strong><? echo $vat_library[$data[0]]; ?></strong></td>
		                <td style="font-size:14px;">Remarks:</td>
			            <td colspan="3" style="font-size:14px;"><strong><? echo $remarks; ?></strong></td>
		            </tr>
		       </table>
	      	</div>
		   <table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center" style="border:none;">
	            <tr >
	            <th style="font-size:12px;" width="20">SL</th>
	            <th style="font-size:12px;" width="60" >Buyer</th>
	            <th style="font-size:12px;" width="100" >Style Ref.</th>
	            <th style="font-size:12px;" width="100" >Order No</th>
	            <th style="font-size:12px;" width="60" >Country</th>
	            <th style="font-size:12px;" width="60" >Country Short Name</th>
	            <th style="font-size:12px;" width="130" >Item Name</th>
	            <th style="font-size:12px;" width="150" >Invoice No</th>
	            <th style="font-size:12px;" width="150" >LC SC No</th>
	            <th style="font-size:12px;" width="50">Ship Mode</th>
	            <th style="font-size:12px;" width="50">FOC/Claim</th>
	            <th style="font-size:12px;" width="50">Delivery Qnty</th>
	            <th style="font-size:12px;" width="50">NO Of Carton</th>
	            <th style="font-size:12px;" >Remarks</th>
	         </tr>
	        </thead>
	        <tbody>
			<?
			$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
			$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
			if($db_type==2)
			{
				$sql="SELECT c.foc_or_claim, c.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no
				order by a.style_ref_no";
			}
			else if($db_type==0)
			{
				$sql="SELECT c.foc_or_claim,c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode,c.lc_sc_no
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.foc_or_claim, c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode,c.lc_sc_no
				order by a.style_ref_no";
			}
			//echo $sql;
			$result=sql_select($sql);
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
	                <td style="font-size:12px;"><p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><?
	                 $actual_po=$row[csf("actual_po")];
	                if($actual_po)
	                {
	                	$actual_po_no="";
	                	$actual_po=explode(",", $actual_po);
	                	foreach($actual_po as $val)
	                	{

	                		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
	                	}
	                	echo $actual_po_no;
	                }
	                else echo $row[csf("po_number")]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
	                <?
	                 $garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
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
	                <td align="left" style="font-size:12px;"><p><? echo $lc_num_arr[$row[csf("lc_sc_no")]].$sc_num_arr[$row[csf("lc_sc_no")]]; ?></p></td>
	                <td align="left" style="font-size:12px;"><p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p></td>
	                <td align="left" style="font-size:12px;"><p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td style="font-size:12px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        <tr bgcolor="#CCCCCC">
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
	          
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;">&nbsp;</td>
	        </tr>
	        <tr style="border:none;">
	        	<td colspan="13"  style=" border:none;border-color:#FFFFFF;">
	            	 <h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
	            </td>
	        </tr>
	        </tbody>
	        </table>
	        <?
			            echo signature_table(63, $data[0], $table_width."px");
			         ?>
	        <!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
			            echo signature_table(63, $data[0], $table_width."px");
			         ?>
		         	</td>
	         	</tr>
	        </tfoot> -->

	    

		<!--</div>-->

	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
	<?
	exit();
}

if($action=="ex_factory_print_new3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	$show_hide_delv_info = str_replace("'","",$data[5]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$vat_library=return_library_array( "select id, vat_number from lib_company", "id", "vat_number"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$country_short_library=return_library_array( "select id, short_name from  lib_country", "id", "short_name"  );
	$lib_color=return_library_array( "select id, color_name from lib_color","id","color_name"  );

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$challan_no_full=$row[csf("sys_number")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$mobile_no=$row[csf("mobile_no")];
		$do_no=$row[csf("do_no")];
		$gp_no=$row[csf("gp_no")];
		$forwarder=$row[csf("forwarder")];
		$forwarder_2=$row[csf("forwarder_2")];
		$system_num=$row[csf("sys_number")];
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];
		$attention=$row[csf("attention")];
		$remarks=$row[csf("remarks")];

	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:1100px; margin-top:10px;">

	    <br>

			<?php
			$table_width=1080;
			$col_span=12;

					if($forwarder>0)
					{
						$supplier_sql=sql_select("SELECT id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
		  				foreach($supplier_sql as $row)
		  				{

		  				$address_1=$row[csf("address_1")];
		  				$address_2=$row[csf("address_2")];
		  				$address_3=$row[csf("address_3")];
		  				$address_4=$row[csf("address_4")];
		  				$contact_no=$row[csf("contact_no")];
		  				}
					}else
					{
						$supplier_sql=sql_select("SELECT id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
						foreach($supplier_sql as $row)
						{

						$address_1=$row[csf("address_1")];
						$address_2=$row[csf("address_2")];
						$address_3=$row[csf("address_3")];
						$address_4=$row[csf("address_4")];
						$contact_no=$row[csf("contact_no")];
						}
					}
	            ?>

		<!--<div style="width:<? //echo $table_width;?>px;">-->
	    	<table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
		        <tr style="background-color:#fff;border-color:#fff;">
		            <td valign="top" style="border:none;" align="left" width="200"><img src="../<? echo $image_location; ?>" height="60"></td>
		            <td valign="top"  align="center"  style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


	                <div style="text-align:center;">
						<?

							$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
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
		                <span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
		                <span style="font-size:15px;"><strong>Delivery Challan</strong></span>
		            </div>


	                </td>
		            <td style="border:none; float:right;">
	                <span style="float:right;"><strong>Challan No : <? echo $challan_no_full; ?> &nbsp;&nbsp;</strong></span><br>
	                 <span style="float:right;"><strong>Challan Date : <? echo change_date_format($data[2]);  ?>&nbsp;&nbsp;</strong></span><br>
	                <span style="float:left;"   id="barcode_img_id"></span>

	                </td>
	       		</tr>

	         </table>

	        <div style="width:1080px; margin:0 auto;">
		         <table border="1" cellpadding="1" cellspacing="1" style="width:1080px; margin-top: 10px; font-size: 18px" rules="all" class="rpt_table" >
			        <tr>
			        	<td width="160" style="font-size:14px;"><? if( $forwarder>0) { echo 'C&F Name:';} else {echo 'Forwarding Agent';}?></td>
			            <td width="160" style="font-size:14px;"><strong><? if( $forwarder>0){echo $supplier_library[$forwarder];} else { echo $supplier_library[$forwarder_2];}  ?></strong></td>
		                <td width="160" style="font-size:14px;">Trns. Comp:</td>
			            <td width="160" style="font-size:14px;"><strong><? echo $supplier_library[$supplier_name]; ?></strong></td>
		              	<td width="160" style="font-size:14px;">Do No:</td>
			            <td width="160" style="font-size:14px;"><strong><? echo $do_no; ?></strong></td>
			        </tr>
		            <tr>
		            	<td style="font-size:14px;">Address:</td>
			            <td style="font-size:14px;"><strong><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?></strong></td>
		                <td style="font-size:14px;">Driver Name :</td>
			            <td style="font-size:14px;"><strong><? echo $driver_name; ?></strong></td>
		                <td style="font-size:14px;">GP No:</td>
			            <td style="font-size:14px;"><strong><? echo $gp_no; ?></strong></td>
			        </tr>
		            <tr>
		           		<td style="font-size:14px;">Attention:</td>
			            <td style="font-size:14px;"><strong><? echo $attention; ?></strong></td>
		                <td style="font-size:14px;">Mobile No :</td>
			            <td style="font-size:14px;"><strong><? echo $mobile_no; ?></strong></td>
		                <td style="font-size:14px;">Lock No:</td>
			            <td style="font-size:14px;"><strong><? echo $lock_no; ?></strong></td>
		            </tr>
		             <tr>
		                <td style="font-size:14px;">DL No:</td>
			           	<td style="font-size:14px;"><strong><? echo $dl_no; ?></strong></td>
		                <td style="font-size:14px;">Final Destination:</td>
			            <td style="font-size:14px;"><strong><? echo $destination_place;?></strong></td>
			            <td style="font-size:14px;">Truck No:</td>
			            <td style="font-size:14px;"><strong><? echo $truck_no; ?></strong></td>
		            </tr>
		            <? if($show_hide_delv_info){?>
			        <tr>
		                <td style="font-size:14px;">Delivery Floor:</td>
			            <td style="font-size:14px;"><strong><? echo $floor_library[$delivery_floor]; ?></strong></td>
			            <td style="font-size:14px;">Delivery Company:</td>
			            <td style="font-size:14px;"><strong><? echo $company_library[$delivery_company]; ?> </strong></td>
			            <td style="font-size:14px;">Delivery Location:</td>
			            <td style="font-size:14px;"><strong><? echo $location_library[$delivery_location]; ?></strong></td>
		            </tr>
		            <? }?>
		            <tr>
		                <td style="font-size:14px;">Vat No.:</td> 
			            <td style="font-size:14px;"><strong><? echo $vat_library[$data[0]]; ?></strong></td>
		                <td style="font-size:14px;">Remarks:</td>
			            <td colspan="3" style="font-size:14px;"><strong><? echo $remarks; ?></strong></td>
		            </tr>
		       </table>
	      	</div>
		   <table style="margin-top:10px;" align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center" style="border:none;">
	            <tr >
	            <th style="font-size:12px;" width="20">SL</th>
	            <th style="font-size:12px;" width="60" >Buyer</th>
	            <th style="font-size:12px;" width="100" >Style Ref.</th>
	            <th style="font-size:12px;" width="100" >Order No</th>
	            <th style="font-size:12px;" width="60" >Country</th>
	            <th style="font-size:12px;" width="60" >Country Short Name</th>
	            <th style="font-size:12px;" width="130" >Item Name</th>
	            <th style="font-size:12px;" width="130" >Color Name</th>
	            <th style="font-size:12px;" width="150" >Invoice No</th>
	            <th style="font-size:12px;" width="150" >LC SC No</th>
	            <th style="font-size:12px;" width="50">Ship Mode</th>
	            <th style="font-size:12px;" width="50">FOC/Claim</th>
	            <th style="font-size:12px;" width="50">Delivery Qnty</th>
	            <th style="font-size:12px;" width="50">NO Of Carton</th>
	            <th style="font-size:12px;" >Remarks</th>
	         </tr>
	        </thead>
	        <tbody>
			<?
			$lc_num_arr = return_library_array("SELECT id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
			$sc_num_arr = return_library_array("SELECT id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
			
			$sql="SELECT d.foc_or_claim, d.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, d.country_id, d.invoice_no as invoice_no, d.total_carton_qnty as total_carton_qnty, sum(e.production_qnty) as total_qnty, d.remarks , actual_po as actual_po,d.shiping_mode,d.lc_sc_no,c.color_number_id
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, pro_ex_factory_mst d, pro_ex_factory_dtls e
				where a.id=b.job_id and b.id=d.po_break_down_id and d.delivery_mst_id=$data[1] and a.status_active=1 and b.status_active=1 and e.status_active=1 and d.status_active=1 and d.is_deleted=0 and b.id=c.po_break_down_id and a.id=c.job_id and c.id=e.color_size_break_down_id and d.id=e.mst_id
				group by d.foc_or_claim, d.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, d.country_id, d.invoice_no, d.total_carton_qnty, d.remarks , actual_po,d.shiping_mode,d.lc_sc_no,c.color_number_id
				order by a.style_ref_no";
			
			// echo $sql;
			$result=sql_select($sql);
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
	                <td style="font-size:12px;"><p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><?
	                 $actual_po=$row[csf("actual_po")];
	                if($actual_po)
	                {
	                	$actual_po_no="";
	                	$actual_po=explode(",", $actual_po);
	                	foreach($actual_po as $val)
	                	{

	                		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
	                	}
	                	echo $actual_po_no;
	                }
	                else echo $row[csf("po_number")]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_short_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
	                <?
	                 $garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
	                 $garments_item_all="";
	                 foreach($garments_item_arr as $item_id)
	                 {
	                     $garments_item_all .=$garments_item[$item_id].",";
	                 }
	                 $garments_item_all=substr($garments_item_all,0,-1);
	                 echo $garments_item_all;
	                ?>
	                 &nbsp;</p></td>
	                  <td style="font-size:12px;"><p><? echo $lib_color[$row[csf("color_number_id")]]; ?></p></td>
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
	                <td align="left" style="font-size:12px;"><p><? echo $lc_num_arr[$row[csf("lc_sc_no")]].$sc_num_arr[$row[csf("lc_sc_no")]]; ?></p></td>
	                <td align="left" style="font-size:12px;"><p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p></td>
	                <td align="left" style="font-size:12px;"><p><? echo $foc_claim_arr[$row[csf("foc_or_claim")]]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td style="font-size:12px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        <tr bgcolor="#CCCCCC">
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
	          
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;">&nbsp;</td>
	        </tr>
	        <tr style="border:none;">
	        	<td colspan="13"  style=" border:none;border-color:#FFFFFF;">
	            	 <h3 align="center">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
	            </td>
	        </tr>
	        </tbody>
	        </table>
	        <?
			            echo signature_table(63, $data[0], $table_width."px");
			         ?>
	        <!-- <tfoot>
	        	<tr>
	        		<td colspan="12"  style=" border-color:#FFFFFF;">
			         <?
			            echo signature_table(63, $data[0], $table_width."px");
			         ?>
		         	</td>
	         	</tr>
	        </tfoot> -->

	    

		<!--</div>-->

	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
	<?
	exit();
}

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

if($action=="deleted_col_size")
{

 	$ex_data = explode("*",$data);
	$company = $ex_data[0];
	$sys_id = $ex_data[1];
	$mst_id = $ex_data[2];
	$dates = $ex_data[3];
	$po_id = $ex_data[4];
	if($po_id)
	{
		$sql_cond.="and  a.po_break_down_id in($po_id)";
	}
	//$sql_cond.=" and  a.delivery_mst_id in($sys_id)";

	/*if($dates!="")
	{
		if($db_type==0){$sql_cond .= " and a.ex_factory_date = '".change_date_format($dates,'yyyy-mm-dd')."' ";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.ex_factory_date = '".date("j-M-Y",strtotime($dates))."'";}
	}*/

    $sql="SELECT a.challan_no, c.po_number,d.color_number_id,d.size_number_id,sum(b.production_qnty) as qnty from pro_ex_factory_mst a ,pro_ex_factory_dtls b,wo_po_break_down c,wo_po_color_size_breakdown d where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and c.id=d.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and( c.status_active<>1 or d.status_active<>1) $sql_cond group by  a.challan_no,c.po_number,d.color_number_id,d.size_number_id";
	$result = sql_select($sql);
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');

   ?>
     	<table cellspacing="0" width="600" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Order No.</th>
                <th width="100" >Challan No.</th>
                <th width="100">Color</th>
                <th width="50" >Size</th>
                <th width="80">Ex-fact Qty</th>

            </thead>
            <tbody>
	            <?
				$i=1;
				$total_qty=0;
	            foreach( $result as $row )
	            {
	                if ($i%2==0)  $bgcolor="#E9F3FF";
	                else $bgcolor="#FFFFFF";
					?>
	                <tr bgcolor="<? echo $bgcolor; ?>" >
	                    <td width="30" align="center"><? echo $i++; ?></td>
	                    <td width="100" align="center"><p><? echo $row[csf("po_number")]; ?></p></td>
	                    <td width="100" align="center"><p><? echo $row[csf("challan_no")]; ?></p></td>
	                    <td width="100" align="center"><p><? echo $color_arr[$row[csf("color_number_id")]]; ?></p></td>
	                    <td width="50" align="center"><p><? echo $size_arr[$row[csf("size_number_id")]]; ?>&nbsp;</p></td>
	                    <td width="80"  align="right"><p><?  echo $qty=number_format($row[csf("qnty")],0,"","");?></p></td>

	                </tr>

					<?
					$total_qty+=$qty;

	             }
	   			?>
	   			<tr bgcolor="#E4E4E4">
	   				<td colspan="5" align="right">Total</td>
	   				<td align="right"><? echo $total_qty; ?></td>

	   			</tr>

            </tbody>
     	</table>
    <?
exit();

}


if ($action == "actual_po_action")
{
	extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    //echo "string $act_po_id";
    $act_po_id_ar=explode(",", $act_po_id);

    ?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
                    js_set_value(i);
                }
            }
        }
        var selected_id = new Array();
        var selected_name = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual' + str).val());
                selected_name.push($('#txt_individual_name' + str).val());

            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual' + str).val()) break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = '';
            var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#hidden_actual_po_id').val(id);
            $('#hidden_actual_po_no').val(name);
        }

        function fnc_close() {
            document.getElementById('hidden_actual_po_id_return').value=document.getElementById('hidden_actual_po_id').value;
            document.getElementById('hidden_actual_po_no_return').value=document.getElementById('hidden_actual_po_no').value;
            parent.emailwindow.hide();
        }



    </script>
    </head>
    <body>


	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="150">Po Number</th>
			<th width="130">Po Qnty.</th>

		</thead>
	</table>
	<div style="width:340px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			$sql="SELECT id, acc_po_no,acc_po_qty from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($po_id) ";

			$result = sql_select($sql);
			$js_set_string="";
			foreach ($result as $row)
			{
				if(in_array($row[csf('id')], $act_po_id_ar))
				{
					if($js_set_string=="")$js_set_string=$i; else $js_set_string.=','.$i;

				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="40"><? echo $i; ?>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
						<input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('acc_po_no')]; ?>"/>
					</td>

					<td width="130"><p><? echo $row[csf('acc_po_no')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('acc_po_qty')]; ?></p></td>

				</tr>
				<?
				$i++;

			}


			?>

		</table>
		<input type="hidden" name="hidden_actual_po_id" id="hidden_actual_po_id">
		<input type="hidden" name="hidden_actual_po_id_return" id="hidden_actual_po_id_return">
		<input type="hidden" name="hidden_actual_po_no" id="hidden_actual_po_no">
		<input type="hidden" name="hidden_actual_po_no_return" id="hidden_actual_po_no_return">
	</div>
	<table width="320">
		<tr>
			<td align="center" >
				<span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		var js_set_string='<? echo $js_set_string;?>';
		js_set_arr=js_set_string.split(",");

		var i;
		for(i=0;i<js_set_arr.length;i++)
		{
			js_set_value(js_set_arr[i]);
		}
	</script>
	<?
	exit();
}

if ($action == "add_info_action")
{
	extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    $datas=explode("___",$hidden_add_info);
    $disabled_cond="";
    if($shipping_mode!=7) $disabled_cond=" disabled='true' ";

    ?>
    <script>
    	var truck_type_arr='<? echo $truck_type_arr_json;?>';
 		var truck_type_arr=JSON.parse(truck_type_arr);
 		var transport_type_arr='<? echo $transport_type_arr_json;?>';
 		var transport_type_arr=JSON.parse(transport_type_arr);


        function fnc_close()
        {
        	var truck_type=$("#cbo_truck_type").val()*1;
        	var truck_type_txt="";
        	if(truck_type)truck_type_txt=truck_type_arr[truck_type];
         	var transport_type=$("#cbo_transport_type").val()*1;
         	var transport_type_txt="";
         	if(transport_type)
         	  transport_type_txt=transport_type_arr[transport_type];
         	var vehicle_size=$("#txt_vehicle_size").val();
        	var chassis_no=$("#txt_chassis_no").val();
        	var currier=$("#txt_currier_name").val();
        	var cbm=$("#txt_cbm_no").val();
        	var data=truck_type+"___"+transport_type+"___"+vehicle_size+"___"+chassis_no+"___"+currier+"___"+cbm;
        	var data_val=truck_type_txt+" "+transport_type_txt+" "+vehicle_size+" "+chassis_no+" "+currier+" "+cbm;
        	//alert(data_val);
        	document.getElementById('all_field_data').value=data;
        	document.getElementById('all_field_data_value').value=data_val;
        	parent.emailwindow.hide();
        }



    </script>
    </head>
    <body>
    	<form>
    	<input type="hidden" name="all_field_data" id="all_field_data">
    	<input type="hidden" name="all_field_data_value" id="all_field_data_value">


    		<table cellspacing="0" cellpadding="0" cellpadding="0" border="0" rules="all" width="450" class="">
    			<tr>
    				<td colspan="5" height="10"></td>
    			</tr>
    			<tr>
    				<td width="100"><strong>Truck Type</strong></td>
    				<td> <? echo create_drop_down( "cbo_truck_type", 122, $truck_type_arr,"", 1, "-- Select Truck Type --", $datas[0], "" );?></td>


    				<td width="110">&nbsp;&nbsp;<strong>Transport Type</strong></td>
    				<td> <? echo create_drop_down( "cbo_transport_type", 122, $transport_type_arr,"", 1, "-- Select Transport Type --", $datas[1], "" );?></td>



    			</tr>
    			<tr>
    				<td colspan="5" height="3"></td>
    			</tr>

    			<tr>
    				<td width="100"><strong>Vehicle Size</strong></td>
    				<td><input style="width: 112px;" type="text" class="text_boxes" name="txt_vehicle_size" id="txt_vehicle_size" value="<? echo $datas[2];?>"></td>


    				<td width="110">&nbsp;&nbsp;<strong>Chassis Number</strong></td>
    				<td  > <input style="width: 112px;" type="text" class="text_boxes" name="txt_chassis_no" id="txt_chassis_no" value="<? echo $datas[3];?>"></td>



    			</tr>
    			<tr>
    				<td colspan="5" height="3"></td>
    			</tr>

    			<tr>
    				<td width="100"><strong>Courier Name</strong></td>
    				<td  ><input  style="width: 112px;" type="text" class="text_boxes" name="txt_currier_name" id="txt_currier_name" <?echo $disabled_cond;?> value="<? echo $datas[4];?>"></td>
    				<td width="110">&nbsp;&nbsp;<strong>CBM Of Goods</strong></td>
    				<td  > <input style="width: 112px;" type="text" class="text_boxes" name="txt_cbm_no" id="txt_cbm_no" value="<? echo $datas[5];?>"></td>


    			</tr>
    			<tr>
    				<td colspan="5" height="11"></td>
    			</tr>
    			<tr>
    				<td colspan="5" align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px;" /> </td>
    			</tr>


    		</table>
    	</form>
    </body>
    </html>

	<?
	exit();
}

if($action=="ExFactoryPrintSonia")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
 	$actual_po_library=return_library_array( "SELECT id, acc_po_no from wo_po_acc_po_info",'id','acc_po_no');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$country_short_library=return_library_array( "select id, short_name from  lib_country", "id", "short_name"  );
	$additional_sql="SELECT  additional_info_id from PRO_EX_FACTORY_MST where delivery_mst_id='$data[1]' order by id asc ";
	$additional_arr=array();
	$kk=0;
	$add_data="";
	foreach(sql_select($additional_sql) as $vals)
	{
		if($kk==0)
		{
			if($vals[csf("additional_info_id")])
			{
				$add_data.=$vals[csf("additional_info_id")];
				$kk++;
			}
		}
	}
	//echo "string $add_data";
	$add_data=explode("___", $add_data);
	$truck_type=$truck_type_arr[$add_data[0]];
	$trans_type=$transport_type_arr[$add_data[1]];
	$sizes=$add_data[2];
	$chassis_no=$add_data[3];
	$courier_name=$add_data[4];
	$cbm=$add_data[5];

	$delivery_mst_sql=sql_select("SELECT id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,sys_number,mobile_no,do_no,gp_no,forwarder,forwarder_2,delivery_company_id,delivery_location_id,delivery_floor_id,attention,remarks,delivery_date from pro_ex_factory_delivery_mst where id=$data[1] and entry_form!=85");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$delivery_date=change_date_format($row[csf("delivery_date")]);
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$challan_no_full=$row[csf("sys_number")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$mobile_no=$row[csf("mobile_no")];
		$do_no=$row[csf("do_no")];
		$gp_no=$row[csf("gp_no")];
		$forwarder=$row[csf("forwarder")];
		$forwarder_2=$row[csf("forwarder_2")];
		$system_num=$row[csf("sys_number")];
		$delivery_company=$row[csf("delivery_company_id")];
		$delivery_location=$row[csf("delivery_location_id")];
		$delivery_floor=$row[csf("delivery_floor_id")];
		$attention=$row[csf("attention")];
		$remarks=$row[csf("remarks")];

	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:900px; margin-top:10px; margin-left:55px;">

	    <br>

			<?php
			$table_width=950;
			$col_span=6;

					if($forwarder>0)
					{
						$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
		  				foreach($supplier_sql as $row)
		  				{

		  				$address_1=$row[csf("address_1")];
		  				$address_2=$row[csf("address_2")];
		  				$address_3=$row[csf("address_3")];
		  				$address_4=$row[csf("address_4")];
		  				$contact_no=$row[csf("contact_no")];
		  				}
					}else
					{
						$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder_2");
						foreach($supplier_sql as $row)
						{

						$address_1=$row[csf("address_1")];
						$address_2=$row[csf("address_2")];
						$address_3=$row[csf("address_3")];
						$address_4=$row[csf("address_4")];
						$contact_no=$row[csf("contact_no")];
						}
					}
	            ?>


	            <table style="margin-top:-0px;border:none;" align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	            	<tr style="background-color:#fff;border-color:#fff;">
	            		<td valign="top" style="border:none;" align="left"><img src="../<? echo $image_location; ?>" height="60"></td>
	            		<td valign="top"  align="center"  style="border:none;"><span style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></span><br>


	            			<div style="text-align:center;">
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
	            				<span style="font-size:13px;"><strong>100% Export Oriented Garments</strong></span><br>
	            				<span style="font-size:15px;"><strong>Delivery Challan</strong></span>
	            			</div>


	            		</td>
	            		<td style="border:none; float:right;">

	            			<span style="float:left;"   id="barcode_img_id"></span>

	            		</td>
	            	</tr>

	            </table>


	            <div style="width:950; margin-left:-50px;">
	            	<table  style="border: none;" cellpadding="0" cellspacing="0" width="950"  border="0" rules="" class="" >
	            		<tr>
	            			<td colspan="6" height="10"></td>

	            		</tr>
	            		<tr>
	            			<td width="120"><strong>Challan No:</strong></td>
	            			<td width="150" style="font-size:12px;"><?php echo $challan_no;?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Driver Name:</strong></td>
	            			<td width="150" style="font-size:12px;"><?php echo $driver_name;?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>Date:</strong></td>
	            			<td width="150" style="font-size:12px;"><?php echo $delivery_date;?></td>
	            		</tr>

	            		<tr>
	            			<td width="120"><strong>C&F Name:</strong></td>
	            			<td width="150"><?php echo $supplier_library[$forwarder];?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Mobile Num:</strong></td>
	            			<td width="150"><?php echo $mobile_no;?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>Do No:</strong></td>
	            			<td width="150"><?php echo $do_no;?></td>
	            		</tr>


	            		<tr>
	            			<td width="120"><strong>C&F Address:</strong></td>
	            			<td width="150"><?php echo $address_1;?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>DL No:</strong></td>
	            			<td width="150"><?php echo $dl_no;?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>GP No:</strong></td>
	            			<td width="150"><?php echo $gp_no;?></td>
	            		</tr>

	            		<tr>
	            			<td width="120"><strong>Trns. Comp:</strong></td>
	            			<td width="150"><?php echo $supplier_library[$supplier_name];?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Truck No:</strong></td>
	            			<td width="150"><?php echo $truck_no;?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>Lock No:</strong></td>
	            			<td width="150"><?php echo $lock_no;?></td>
	            		</tr>

	            		<tr>
	            			<td width="120"><strong>Trns. Type:</strong></td>
	            			<td width="150"><?php echo $truck_type;?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Courier Company:</strong></td>
	            			<td width="150"><?php echo $courier_name;?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>Trns. Type / Size:</strong></td>
	            			<td width="150"><?php echo $trans_type." / ".$sizes;?></td>
	            		</tr>

	            		<tr>
	            			<td width="120"><strong>Delivery Com:</strong></td>
	            			<td width="150"><?php echo $company_library[$delivery_company];?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Delivery Location:</strong></td>
	            			<td width="150"><?php echo $location_library[$delivery_location];?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>Delivery Floor:</strong></td>
	            			<td width="150"><?php echo $floor_library[$delivery_floor];?></td>
	            		</tr>

	            		<tr>
	            			<td width="120"><strong>Final Destination:</strong></td>
	            			<td width="150"><?php echo $destination_place;?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Chassis No:</strong></td>
	            			<td width="150"><?php echo $chassis_no;?></td>


	            			<td width="120">&nbsp;&nbsp;<strong>CBM Of Goods:</strong></td>
	            			<td width="150"><?php echo $cbm;?></td>
	            		</tr>

	            		<tr>
	            			<td width="120"><strong>Attention:</strong></td>
	            			<td width="150"><?php echo $attention;?></td>

	            			<td width="120">&nbsp;&nbsp;<strong>Remarks:</strong></td>
	            			<td colspan="3"><?php echo $remarks;?></td>
	            		</tr>
	            		<tr>
	            			<td colspan="6" height="10"></td>

	            		</tr>


	            	</table>




	            </div>
		   <table style="margin-top:-0px;" align="right" cellspacing="0" border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center" style="border:none;">
	            <tr >
		            <!-- <th style="font-size:14px;" width="20">SL</th> -->
		            <th style="font-size:14px;" width="70">Buyer</th>
		            <th style="font-size:14px;" width="100">Style Ref.</th>
		            <th style="font-size:14px;" width="100">Order No</th>
		            <th style="font-size:14px;" width="60">Country</th>

		            <th style="font-size:14px;" width="130" >Item Name</th>
		            <th style="font-size:14px;" width="150" >Invoice No</th>
		            <th style="font-size:14px;" width="50">Ship Mode</th>
		            <th style="font-size:14px;" width="50">Delivery Qnty</th>
		            <th style="font-size:14px;" width="50">NO Of Carton</th>
		            <th style="font-size:14px;" width="100">Shipping Status</th>
		            <th style="font-size:14px;">Remarks</th>
	         </tr>
	        </thead>
	        <tbody>
			<?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				  $sql="SELECT c.id,a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, listagg(CAST(c.invoice_no as VARCHAR(4000)),',') within group (order by c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty, sum(c.ex_factory_qnty) as total_qnty, listagg(CAST(c.remarks as VARCHAR(4000)),',') within group (order by c.remarks) as remarks , listagg(CAST(actual_po as VARCHAR(4000)),',') within group (order by actual_po) as actual_po,c.shiping_mode, c.shiping_status
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by  c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id, c.shiping_mode, c.shiping_status
				order by a.style_ref_no";
			}
			else if($db_type==0)
			{
				$sql="SELECT c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id as po_break_down_id, b.po_number, c.country_id, group_concat(c.invoice_no) as invoice_no, sum(c.total_carton_qnty) as total_carton_qnty , sum(c.ex_factory_qnty) as total_qnty,group_concat(c.remarks) as remarks ,group_concat(actual_po) as actual_po,c.shiping_mode, c.shiping_status
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=$data[1] and c.status_active=1 and c.is_deleted=0
				group by c.id, a.buyer_name, a.gmts_item_id, a.style_ref_no, b.id, b.po_number, c.country_id,c.shiping_mode, c.shiping_status
				order by a.style_ref_no";
			}
			//echo $sql;
			$result=sql_select($sql);
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
	                <!-- <td style="font-size:14px;"><? // echo $i;  ?></td> -->
	                <td style="font-size:14px;"><p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
	                <td style="font-size:14px;"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
	                <td style="font-size:14px;"><p><?
	                 $actual_po=$row[csf("actual_po")];
	                if($actual_po)
	                {
	                	$actual_po_no="";
	                	$actual_po=explode(",", $actual_po);
	                	foreach($actual_po as $val)
	                	{

	                		if($actual_po_no=="")$actual_po_no=$actual_po_library[$val]; else $actual_po_no.=','.$actual_po_library[$val];
	                	}
	                	echo $actual_po_no;
	                }
	                else echo $row[csf("po_number")]; ?>&nbsp;</p></td>
	                <td style="font-size:14px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>

	                <td style="font-size:14px;"><p>
	                <?
	                 $garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
	                 $garments_item_all="";
	                 foreach($garments_item_arr as $item_id)
	                 {
	                     $garments_item_all .=$garments_item[$item_id].",";
	                 }
	                 $garments_item_all=substr($garments_item_all,0,-1);
	                 echo $garments_item_all;
	                ?>
	                 &nbsp;</p></td>
	                <td style="font-size:14px;"><p>
					<?
					 $invoice_id="";
					 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
					 foreach($invoice_id_arr as $inv_id)
					 {
						 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];

					 }
					 echo $invoice_id;
					?>&nbsp;</p></td>
	                <td align="right" style="font-size:14px;"><p><? echo $shipment_mode[$row[csf("shiping_mode")]]; ?></p></td>
	                <td align="right" style="font-size:14px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	                <td align="right" style="font-size:14px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td align="right" style="font-size:14px;"><p><? echo $shipment_status[$row[csf("shiping_status")]]; ?></p></td>
	                <td style="font-size:14px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        <tr>
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
	            <td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
	            <td align="right" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:14px;font-weight: bold;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
	            <td align="right" style="font-size:14px;font-weight: bold;">&nbsp;</td>
	        </tr>
	        <tr style="border:1px solid #FFFFFF;">
	        	<td colspan="11"  style=" border:1px solid #FFFFFF;">
	            	 <h3 align="left">In Words : &nbsp;<? echo number_to_words($tot_qnty,"Pcs");?></h3>
	            </td>
	        </tr>
	        </tbody>


	    </table>

	    <table  cellpadding="0"  cellspacing="0" width="<? echo $table_width;?>"  border="0" rules="all" class="" >
	     	<tr>
	     		<td colspan="12"  style=" border-color:#FFFFFF;">
	     			<?
	     			echo signature_table(63, $data[0], $table_width."px");
	     			?>
	     		</td>
	     	</tr>
	     </table>

		<!--</div>-->

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
