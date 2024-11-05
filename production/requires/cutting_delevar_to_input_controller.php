<?
session_start();
include('../../includes/common.php');


$user_id = $_SESSION['logic_erp']["user_id"];
 
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************


if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select cutting_input,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val('".$result[csf("cutting_input")]."');\n";
		echo "$('#styleOrOrderWisw').val('".$result[csf("production_entry")]."');\n";
	}
	
	$sql_delevery = sql_select("select cut_panel_delevery from variable_settings_production where company_name=$data and variable_list=32 and status_active=1 and  is_deleted=0");
	if(count($sql_result)>0)
	{
		echo "$('#cbo_delivery_basis').val('".$sql_delevery[0][csf("cut_panel_delevery")]."');\n";
	}
	else
	{
		echo "$('#cbo_delivery_basis').val(1);\n";	
	}

	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=123","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
	
 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );		
	exit(); 
}

if ($action=="load_drop_down_knit_com")
{
	$exDataArr = explode("**",$data);	
	$knit_source=$exDataArr[0];
	$company=$exDataArr[1];
	//if($company=="" || $company==0) $company_cod = ""; else $company_cod = " and id=$company";
	if($knit_source==1)
	{
		echo create_drop_down( "cbo_cutting_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value+'**'+$('#cbo_knitting_source').val(), 'load_drop_down_cut_location', 'cutt_com_location_td' );" );
	}
	else if($knit_source==3)
	{
		echo create_drop_down( "cbo_cutting_company", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =22 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value+'**'+$('#cbo_knitting_source').val(), 'load_drop_down_cut_location', 'cutt_com_location_td' );",0 );
	}
	else
	{
		echo create_drop_down( "cbo_cutting_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
	}
	exit();	
}

if ($action=="load_drop_down_cut_location")
{
	$exDataArr = explode("**",$data);	
	$knit_source=$exDataArr[1];
	$company=$exDataArr[0];
	if($company=="" || $company==0) $company_cod = ""; else $company_cod = " and company_id=$company";
	if($knit_source==1)
		echo create_drop_down( "cbo_cut_com_location", 170, "select id,location_name from  lib_location where status_active=1 and is_deleted=0 $company_cod order by location_name","id,location_name", 1, "-- Select --", 0, "" );
	else if($knit_source==3)
		echo create_drop_down( "cbo_cut_com_location", 170, "select c.address_1,c.id from lib_supplier c where c.id='$company'","id,address_1", 1, "-- Select --", 0, "",0 );
	else
		echo create_drop_down( "cbo_cut_com_location", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
	exit();	
}

if ($action=="load_drop_down_sewing_com")
{
	$exDataArr = explode("**",$data);	
	$knit_source=$exDataArr[0];
	$company=$exDataArr[1];
	//if($company=="" || $company==0) $company_cod = ""; else $company_cod = " and id=$company";
	if($knit_source==1)
	{
		echo create_drop_down( "cbo_sewing_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value+'**'+$('#cbo_sewing_source').val(), 'load_drop_down_sew_location', 'sew_com_location_td' );" );
	}
	else if($knit_source==3)
	{
		echo create_drop_down( "cbo_sewing_company", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =22 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_delevar_to_input_controller', this.value+'**'+$('#cbo_sewing_source').val(), 'load_drop_down_sew_location', 'sew_com_location_td' );",0 );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
	}
	exit();	
}

if ($action=="load_drop_down_sew_location")
{
	$exDataArr = explode("**",$data);	
	$sewing_source=$exDataArr[1];
	$company=$exDataArr[0];
	if($company=="" || $company==0) $company_cod = ""; else $company_cod = " and company_id=$company";
	if($sewing_source==1)
		echo create_drop_down( "cbo_sew_com_location", 170, "select id,location_name from  lib_location where status_active=1 and is_deleted=0 $company_cod order by location_name","id,location_name", 1, "-- Select --", 0, "" );
	else if($sewing_source==3)
		echo create_drop_down( "cbo_sew_com_location", 170, "select c.address_1,c.id from lib_supplier c where c.id='$company'","id,address_1", 1, "-- Select --", 0, "",0 );
	else
		echo create_drop_down( "cbo_sew_com_location", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
	exit();	
}

if($action=="sys_surch_popup")
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
			<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>                	 
					<th width="150">Buyer Name</th>
					<th width="80">Job</th>
					<th width="80">Style</th>
					<th width="80">Order</th>
					<th width="80">Challan</th>
					<th width="260">Delivery Date Range</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</thead>
				<tbody>
					<tr class="general">
					<td><? echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 ); ?></td>
					<td><input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" /></td>
					<td><input type="text" style="width:80px" class="text_boxes"  name="txt_style_no" id="txt_style_no" /></td>
					<td><input type="text" style="width:80px" class="text_boxes"  name="txt_order_no" id="txt_order_no" /></td>
					<td><input type="text" style="width:80px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" /></td>

					<td>
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td> 
					<td>
						<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $delivery_basis_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_order_no').value, 'create_delivery_search_list', 'search_div_delivery', 'cutting_delevar_to_input_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:70px;" />
					</td>
				</tr>
				</tbody>
				<tr>
					<td align="center" colspan="7" valign="middle">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="hidden_delivery_id" >
					</td>
				</tr>
			</table>
			<div id="search_div_delivery"></div>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?

}


if($action=="create_delivery_search_list")
{
 	$ex_data = explode("_",$data);
	$txt_challan_no = $ex_data[0];
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];
	$delivery_basis = $ex_data[4];
	$buyer = $ex_data[5];
	$job = $ex_data[6];
	$style = $ex_data[7];
	$order = $ex_data[8];
 	$sql_cond="";
 	$po_cond="";

 	$job_and_order_sql="SELECT b.id,a.job_no_prefix_num,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.job_no=b.job_no_mst and a.company_name=$company group by b.id,a.job_no_prefix_num,a.style_ref_no,b.po_number  ";
 	foreach( sql_select($job_and_order_sql) as $key=>$vals)
 	{
 		$job_and_order_arr[$vals[csf("id")]]["job"]=$vals[csf("job_no_prefix_num")];
 		$job_and_order_arr[$vals[csf("id")]]["style"]=$vals[csf("style_ref_no")];
 		$job_and_order_arr[$vals[csf("id")]]["order"]=$vals[csf("po_number")];
 	}

 	if($job || $style || $order || $buyer)
 	{
 		$sql_cond_order="";

 		if($job)
 		{
 			$sql_cond_order.=" and a.job_no_prefix_num='$job' ";
 		}
 		if($buyer)
 		{
 			$sql_cond_order.=" and a.buyer_name='$buyer' ";
 		}
 		if($style)
 		{
 			$sql_cond_order.=" and a.style_ref_no like '%$style%' ";
 		}
 		if($order)
 		{
 			$sql_cond_order.=" and b.po_number like '%$order%' ";
 		}

 	    $order_sql="SELECT b.id from wo_po_details_master a,wo_po_break_down b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.job_no=b.job_no_mst $sql_cond_order ";
 		$all_po_array=array();
 		foreach( sql_select($order_sql) as $key=>$value)
		{
			$all_po_array[$value[csf("id")]]=$value[csf("id")];
		}
		$all_po_ids=implode(",", $all_po_array);
		$po_cond="";
		if($db_type==2 && count($all_po_array)>999)
		{
			$chnk_arr=array_chunk($all_po_array, 999);
			foreach($chnk_arr as $key=>$vals)
			{
				$ids=implode(",", $vals);
				if($po_cond=="")
				{
					$po_cond.=" and b.po_break_down_id in($ids)";
				}
				else 
				{
					$po_cond .=" or b.po_break_down_id in($ids)";
				}
			}
		}
		else
		{
			$po_cond=" and b.po_break_down_id in($all_po_ids)";
		}
 	}
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	if(trim($txt_challan_no)!="") $sql_cond .= " and a.challan_no='$txt_challan_no'";
	if(trim($trans_com)!=0) $sql_cond .= " and transport_supplier='$trans_com'";
	 
	if($db_type==2)
	{
		$sql = "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.challan_no, a.delivery_date,a.deliver_basis, listagg(CAST(b.buyer_id as VARCHAR(4000)),',') within group (order by b.buyer_id) as buyer_id ,b.po_break_down_id
		from  pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and
		a.deliver_basis=$delivery_basis $sql_cond $po_cond
		group by a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.challan_no, a.delivery_date,a.deliver_basis,b.po_break_down_id  order by a.id asc"; 
	}
	else if($db_type==0)
	{
		$sql = "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.challan_no, a.delivery_date,a.deliver_basis, group_concat(b.buyer_id) as buyer_id,b.po_break_down_id 
		from  pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0  and
		a.deliver_basis=$delivery_basis $sql_cond  $po_cond
		group by a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.challan_no, a.delivery_date,a.deliver_basis,b.po_break_down_id  order by a.id asc"; 
	}
	//echo $sql;die;
	$result = sql_select($sql);
	$exfact_qty_arr=return_library_array( "SELECT delivery_mst_id, sum(cut_delivery_qnty) as cut_delivery_qnty from  pro_cut_delivery_order_dtls where status_active=1 and delivery_mst_id>0 group by delivery_mst_id",'delivery_mst_id','cut_delivery_qnty');
 	$buyer_name_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active=1",'id','short_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	//$delivery_basis_arr=array(1=>"Order No",2=>"Cut No");
	$delivery_basis_arr=array(1=>"Order No",2=>"Cut Number",3=>"Bundle Number"); 
   ?>
     	<table cellspacing="0" width="860" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" ><p>SL</p></th>
                <th width="70"><p>Sys Challan No</p></th>
                <th width="150"><p>Buyer Name</p></th>
                 <th width="60" ><p>Challan</p></th>
                <th width="40" ><p>Job</p></th>
                <th width="100" ><p>Style</p></th>
                <th width="100" ><p>Order</p></th>               
                <th width="70" ><p>Delivery Date</p></th>
                <th width="100"><p>Delivery Basis</p></th>
                <th ><p>Delivery Qty</p></th>
            </thead>
     	</table>
     <div style="width:860px; max-height:220px;overflow-y:scroll;" >	 
        <table cellspacing="0" width="840" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {	
            	//$job_and_order_arr[$vals[csf("id")]]["job"]
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>);" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="70" align="center"><? echo $row[csf("sys_number_prefix_num")]; ?></td>
                    <td width="150" align="center" style="word-break:break-all">
					<?
					$buy_id_arr=array_unique(explode(",",$row[csf("buyer_id")]));
					$buyer_all="";
					foreach($buy_id_arr as $buy_id)
					{
						if($buyer_all!="") $buyer_all.=", ";
						$buyer_all .=$buyer_name_arr[$buy_id];
					}
					 echo $buyer_all; 
					?>&nbsp;</td>
                    <td width="60" align="center" style="word-break:break-all"><? echo $row[csf("challan_no")]; ?>&nbsp;</td>	
                    <td width="40" align="center"><? echo $job_and_order_arr[$row[csf("po_break_down_id")]]["job"]; ?>&nbsp;</td>	
                    <td width="100" align="center" style="word-break:break-all"><? echo $job_and_order_arr[$row[csf("po_break_down_id")]]["style"]; ?>&nbsp;</td>	
                    <td width="100" align="center" style="word-break:break-all"><? echo $job_and_order_arr[$row[csf("po_break_down_id")]]["order"]; ?>&nbsp;</td>	
                    <td width="70" align="center"><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</td>
                    <td width="100" align="center"><? echo $delivery_basis_arr[$row[csf("deliver_basis")]]; ?>&nbsp;</td>
                    <td align="center" style="padding-right:3px;"><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></td> 	
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
	$sql_mst=sql_select("select id, sys_number, company_id, location_id, challan_no, delivery_date,deliver_basis,knitting_source,knitting_company,cut_com_location,sewing_source,sewing_company,sewing_com_location  
	from  pro_cut_delivery_mst where id=$data");
	foreach($sql_mst as $row)
	{
		echo "$('#txt_system_no').val('".$row[csf('sys_number')]."');\n";
		echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf('company_id')].");\n";
		echo "$('#cbo_location_name').val(".$row[csf('location_id')].");\n";
		echo "$('#txt_challan_no').val('".$row[csf('challan_no')]."');\n";
		echo "$('#txt_ex_factory_date').val('".change_date_format($row[csf('delivery_date')])."');\n";
		echo "$('#cbo_delivery_basis').val(".$row[csf('deliver_basis')].");\n";
		echo "$('#cbo_knitting_source').val(".$row[csf('knitting_source')].");\n";
		echo "load_drop_down( 'requires/cutting_delevar_to_input_controller',".$row[csf('knitting_source')]."+'**'+".$row[csf('company_id')].", 'load_drop_down_knit_com', 'knitting_company_td' );\n";
		echo "$('#cbo_cutting_company').val(".$row[csf('knitting_company')].");\n";
		echo "load_drop_down( 'requires/cutting_delevar_to_input_controller',".$row[csf('knitting_company')]."+'**'+".$row[csf('knitting_source')].", 'load_drop_down_cut_location', 'cutt_com_location_td' );\n";
		echo "$('#cbo_cut_com_location').val(".$row[csf('cut_com_location')].");\n";
		echo "$('#cbo_sewing_source').val(".$row[csf('sewing_source')].");\n";
		echo "load_drop_down( 'requires/cutting_delevar_to_input_controller',".$row[csf('sewing_source')]."+'**'+".$row[csf('company_id')].", 'load_drop_down_sewing_com', 'sewing_company_td' );\n";
		echo "$('#cbo_sewing_company').val(".$row[csf('sewing_company')].");\n";
		echo "load_drop_down( 'requires/cutting_delevar_to_input_controller',".$row[csf('sewing_company')]."+'**'+".$row[csf('sewing_source')].", 'load_drop_down_sew_location', 'sew_com_location_td' );\n";
		echo "$('#cbo_sew_com_location').val(".$row[csf('sewing_com_location')].");\n";
		
		
		//echo "set_button_status(0, permission, 'fnc_cutDelivery',1,0);\n";
	}
}
 
if($action=="show_dtls_listview_mst")
{
?>	
	<div style="width:930px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="200" >Item Name</th>
                <th width="150" >Country</th>
                <th width="150" >Order No</th>
                <th width="100" >Delivery Date</th>
                <th width="100" >Delivery Qnty</th>                    
                <th align="center">Challan No</th>
            </thead>
    	</table> 
    </div>
	<div style="width:930px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="details_table">
		<? 
			$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
			$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
			$i=1;
		
			$total_production_qnty=0;
			$sql="select a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.cut_delivery_date,a.cut_delivery_qnty,a.location,b.challan_no from  pro_cut_delivery_order_dtls a,  pro_cut_delivery_mst b where a.delivery_mst_id=b.id and  a.delivery_mst_id=$data and a.status_active=1 and a.is_deleted=0 order by id";
			//echo $sql;
			$sqlResult =sql_select($sql);
 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";
				
				$total_production_qnty+=$selectResult[csf('cut_delivery_qnty')];	
 				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_cutDelivery_form_data','requires/cutting_delevar_to_input_controller');get_php_form_data('<? echo $selectResult[csf('po_break_down_id')];?>+**+<? echo $selectResult[csf('item_number_id')];?>+**+<? echo $selectResult[csf('country_id')];?>','populate_data_from_search_popup','requires/cutting_delevar_to_input_controller');" > 
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="200" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="150" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                     <td width="150" align="center"><p><? echo $order_num_arr[$selectResult[csf('po_break_down_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo change_date_format($selectResult[csf('cut_delivery_date')]); ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('cut_delivery_qnty')]; ?></p></td>
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
    <!--<script> setFilterGrid("details_table",-1); </script>-->
<?
	exit();
}
 

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	if($delivery_basis==1)
	{
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
				if(str==3) 
				{		
					document.getElementById('search_by_th_up').innerHTML="Job No";
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
								$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No");
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
									<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $delivery_basis; ?>, 'create_po_search_list_view', 'search_div', 'cutting_delevar_to_input_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
	else if($delivery_basis==2)
	{
		
	?>
	<script>
			function js_set_system_value(strCon ) 
			{
				
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th width="140">Company name</th>
						<th width="130">Cutting QC No</th>
						<th width="130">Cutting No</th>
						<th width="130">Job No.</th>
						<th width="250">Date Range</th>
						<th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
					</tr>
				</thead>
				<tbody>
					<tr>                    
							<td>
								<? 
							
									echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --",$company, "",1);
								?>
							</td>
							<td align="center">
								<input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:120px"  />
							</td>
							<td align="center" >
									<input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
									<input type="hidden" id="update_mst_id" name="update_mst_id" />
							</td>
							<td align="center">
								<input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
							</td>
						
							<td align="center" width="250">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $delivery_basis; ?>+'_'+document.getElementById('txt_cut_qc').value, 'create_po_search_list_view', 'search_div', 'cutting_delevar_to_input_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
							</td>
					</tr>
					<tr>                  
							<td align="center" height="40" valign="middle" colspan="6">
								<? echo load_month_buttons(1);  ?>
							</td>
					</tr>   
				</tbody>
			</tr>         
		</table> 
		<div align="center" valign="top" id="search_div"> </div>  
	</form>
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	}

	else if($delivery_basis==3)
	{
		
	?>
		<script>
			
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
			<table width="880" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
				<tr>
					<td align="center" width="100%">
						<table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
							<thead>                	 
								<th width="100">Cut Number</th>
								<th  width="80" align="center" id="">Job Number</th>
								<th  width="120" align="center" id="">Order Number</th>
								<th  width="80" align="center" id="">Bundle Number</th>
								<th width="200">Date Range</th>
								<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
							</thead>
							<tr>
								<td width="100">  
								<input type="text" style="width:100px" class="text_boxes"  name="txt_cut_no" id="txt_cut_no" />
								</td>
							
								<td width="80" align="center" >				
									<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_job_no" id="txt_job_no"  />			
								</td>
								<td width="120">  
								<input type="text" style="width:120px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
								</td>
								<td width="120">  
								<input type="text" style="width:100px" class="text_boxes"  name="txt_bundle_no" id="txt_bundle_no" />
								</td>
								<td align="center">
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td> 
								<td align="center">
									<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $delivery_basis; ?>+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_bundle_no').value, 'create_po_search_list_view', 'search_div', 'cutting_delevar_to_input_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
}

if($action=="create_po_search_list_view")
{ 
 	$ex_data = explode("_",$data);

	 $delivery_basis=$ex_data[6];
	 //echo $delivery_basis;
	 
	if($delivery_basis==1)
	{
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
				$sql_cond = " and a.job_no_prefix_num like '%".trim($txt_search_common)."%'";
		}
		if($txt_date_from!="" || $txt_date_to!="") 
		{
			if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
			if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
		}
		
		if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
		$sql = "SELECT b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut from wo_po_details_master a, wo_po_break_down b  where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.shiping_status!=3 and a.garments_nature=$garments_nature $sql_cond"; 
		
		$result = sql_select($sql);
		$poIDArr = array();
		foreach ($result as $val) 
		{
			$poIDArr[$val[csf('id')]] = $val[csf('id')];
		}
		$allPOId = implode(",", $poIDArr);

		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
		
		if($db_type==0)
		{
			$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPOId) group by po_break_down_id",'po_break_down_id','country');
		}
		else
		{
			$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPOId) group by po_break_down_id",'po_break_down_id','country');
		}
		
		$po_country_data_arr=array();
		$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPOId) group by po_break_down_id, item_number_id, country_id");
		
		foreach($poCountryData as $row)
		{
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
		}
		
		
		$total_ex_fac_data_arr=array();
		$total_ex_fac_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(cut_delivery_qnty) as cut_delivery_qnty from  pro_cut_delivery_order_dtls where status_active=1 and is_deleted=0 and po_break_down_id in($allPOId) group by po_break_down_id, item_number_id, country_id");
		foreach($total_ex_fac_arr as $row)
		{
			$total_ex_fac_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('cut_delivery_qnty')];
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
					<th width="80">Total Delivery Qty</th>
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
	}
	else if ($delivery_basis==2)
	{
		$company = $ex_data[0];	
		$cutting_no = $ex_data[1];
		$job_no = $ex_data[2];
		$from_date = $ex_data[3];
		$to_date = $ex_data[4];
		$cut_year= $ex_data[5];
		$system_no= $ex_data[7];

		if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
		if($db_type==2){ $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year";}
		if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}

		if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
		if(str_replace("'","",$company)==0) $conpany_cond_1=""; else $conpany_cond_1="AND C.COMPANY_ID=".str_replace("'","",$company)."";

		if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and b.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  ";

		if(str_replace("'","",$cutting_no)=="") $cut_cond_1=""; else $cut_cond_1="AND B.CUT_NO LIKE '%".str_replace("'","",$cutting_no)."%'";

		if(str_replace("'","",$job_no)=="") $job_cond =""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
		if(str_replace("'","",$job_no)=="") $job_cond_1 =""; else $job_cond_1="AND C.JOB_NO LIKE '%".str_replace("'","",$job_no)."%'";

		if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($system_no)." $year_cond";
		if(str_replace("'","",$system_no)=="") $system_cond_1=""; else $system_cond_1="AND A.CUT_QC_PREFIX_NO=".trim($system_no)." $year_cond";

		if( $from_date!="" && $to_date!="" )
		{
			if($db_type==0)
			{
				$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";

				$sql_cond_1= " AND C.CUTTING_QC_DATE  BETWEEN '".change_date_format($from_date,'YYYY-MM-DD')."' AND '".change_date_format($to_date,'YYYY-MM-DD')."'";
			}
			if($db_type==2)
			{
				$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
				$sql_cond_1= " AND C.CUTTING_QC_DATE  BETWEEN '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' AND '".change_date_format($to_date,'YYYY-MM-DD','-',1)."'";
			}
		}
		$sql_cut="SELECT B.CUT_NO
		FROM PRO_GARMENTS_PRODUCTION_MST A,PRO_GARMENTS_PRODUCTION_DTLS B,PRO_GMTS_CUTTING_QC_MST C
		WHERE A.ID=B.MST_ID 
		AND C.ID=A.DELIVERY_MST_ID
		$conpany_cond_1  $cut_cond_1 $job_cond_1 $system_cond_1 $sql_cond_1 $order_cond
		AND B.PRODUCTION_TYPE=9
		AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 
		AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1  
		AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ";
		//echo $sql_cut;
		$result_cut= sql_select($sql_cut);
		$rcv_cut_no_arr=array();
		foreach ($result_cut as $row) 
		{
			$rcv_cut_no_arr[$row["CUT_NO"]]=$row["CUT_NO"];
		}

		if (count($rcv_cut_no_arr)>0) {
			$rcv_cunt_no_cond=where_con_using_array($rcv_cut_no_arr,1,"a.cutting_no not");
		}

		$sql_order="SELECT a.id,a.cutting_no,a.cut_qc_prefix_no,a.cutting_qc_no, a.table_no, a.job_no, a.batch_id, a.cutting_qc_date, a.marker_length, 
		a.marker_width, a.fabric_width,c.job_no_prefix_num,b.cut_num_prefix_no,$year
		FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b,wo_po_details_master c
		where a.cutting_no=b.cutting_no and a.job_no=b.job_no and a.job_no=c.job_no   $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond $system_cond $rcv_cunt_no_cond order by id";

		//echo $sql_order."<br>";

		
			////echo $sql_cut;
		$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
		//$order_library=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"); and a.job_no=c.job_no
		
		$arr=array(3=>$table_no_arr);
		echo create_list_view("list_view", "Cutting QC No,Year,Cut No,Table No,Job No,Batch No,Marker Length,Markar Width,Fabric Width,Cutting QC Date","80,80,80,80,100,80,80,80,80,120","950","270",0, $sql_order , "js_set_system_value", "cutting_qc_no,cutting_no", "", 1, "0,0,0,table_no,0,0,0,0,0,0", $arr, "cut_qc_prefix_no,year,cut_num_prefix_no,table_no,job_no,batch_id,marker_length,marker_width,fabric_width,cutting_qc_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0,0,3") ;	
		
	}
	else if ($delivery_basis==3)
	{
		$txt_cut_no = $ex_data[0];
		$txt_job_no = $ex_data[1];
		$txt_date_from = $ex_data[2];
		$txt_date_to = $ex_data[3];
		$order_no = $ex_data[7];
		$bundle_no = $ex_data[8];
		$company = $ex_data[4];
		$garments_nature = $ex_data[5];
		
		$sql_cond="";
		
		if(trim($order_no)!="") $sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		if(trim($txt_cut_no)!="") $sql_cond.= " and a.cut_num_prefix_no=$txt_cut_no";
		if(trim($txt_job_no)!="") $sql_cond.= " and b.job_no like '%".trim($txt_job_no)."%'";
		

		
		$sql="SELECT  e.po_number,e.shipment_date,a.cutting_no,a.job_no,a.company_id,b.order_id,c.color_id,d.size_id,d.bundle_no
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,ppl_cut_lay_size c,wo_po_break_down e,ppl_cut_lay_bundle d
		where a.id=b.mst_id and a.id=c.mst_id and a.id=d.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and b.order_id=e.id and a.company_id=$company
		$sql_cond
		group by a.cutting_no,a.job_no,a.company_id,b.order_id,c.color_id,d.size_id,d.bundle_no,e.po_number,e.shipment_date ";
		//e.id,

		$result = sql_select($sql);
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$sql="SELECT B.BUNDLE_NO
		FROM PRO_GARMENTS_PRODUCTION_MST A,PRO_GARMENTS_PRODUCTION_DTLS B
		WHERE A.ID=B.MST_ID 
		AND B.PRODUCTION_TYPE=9
		AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 
		AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 
		AND B.CUT_NO='$data'";
		$sql_res=sql_select($sql);
			$recv_bundle_arr=array();
			foreach ($sql_res as $row) 
			{
			$recv_bundle_arr[$row["BUNDLE_NO"]]=$row["BUNDLE_NO"];
			}
			


		?>
		<div style="width:1030px;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="80">Company Name</th>
					<th width="100">Cut Number</th>
					<th width="100">Job No</th>
					<th width="100">Order No</th>
					<th width="100">Buyer</th>
					<th width="80">Bundle No</th>
					<th width="80">Shipment Date</th>
					<th width="">Bundle Qty</th>
				</thead>
			</table>
		</div>
		<div style="width:1030px; max-height:240px;overflow-y:scroll;" >	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
				<?
				$i=1;
				if ($recv_bundle_arr[$row["BUNDLE_NO"]] == "") 
				{
				foreach( $result as $row )
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
					$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" > 
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><?  echo $company_arr[$row[csf("company_id")]];?> </td>		
							<td width="100"><p><? echo $row[csf("cutting_no")]; ?></p></td>
							<td width="100"><p><? echo $row[csf("job_no")]; ?></p></td>	
							<td width="100"><p><? echo $row[csf("po_number")];?></p></td>
							<td width="100"><p><?  //echo $row[csf("order_id")];?></p></td>	
							<td width="80"><p><? echo $row[csf("bundle_no")]; ?>&nbsp;</p></td>
							<td width="80" align=""><? echo change_date_format($row[csf("shipment_date")]);?>&nbsp;</td>
							<td><? //echo $row[csf("shipment_date")];?></td> 	
						</tr>
					<? 
					$i++;
				}
				}
			?>
			</table>
		</div> 
		<?	
		
		
	}
exit();	
}
 
if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];

	//echo "select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name,a.shipment_date   from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id=$po_id";
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
		
		$cutting_qty = return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=".$po_id." and item_number_id='$item_id' and production_type=1 and country_id='$country_id' and status_active=1 and is_deleted=0","production_quantity");
 		if($cutting_qty=="")$cutting_qty=0;
		
		$total_produced = return_field_value("sum(cut_delivery_qnty) as cut_delivery_qnty","pro_cut_delivery_order_dtls","po_break_down_id=".$po_id." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0","cut_delivery_qnty");
		if($total_produced=="")$total_produced=0;
		
 		echo "$('#txt_finish_quantity').val('".$cutting_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
		$yet_to_produced = $cutting_qty-$total_produced;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
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
	$company_id = $dataArr[5];
	
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//#############################################################################################//
	
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=123 and company_name='$company_id'");
	$is_control = $control_and_preceding[0]['IS_CONTROL'];
	$preceding_page_id = $control_and_preceding[0]['PRECEDING_PAGE_ID'];

	$qty_source = 0;
	if($is_control==1)
	{
		if($preceding_page_id==117)
		{
			$qty_source = 1;
		}
	}

	// order wise - color level, color and size level
	$ex_fac_value=array();
	
	//$variableSettings=2;
	
	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN b.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty
					from wo_po_color_size_breakdown a 
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id";
					
			$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty from wo_po_color_size_breakdown a
                    left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and ex.is_deleted=0 and ex.status_active=1 group by a.item_number_id, a.color_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];
				$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("bundle_qnty")];
				
			}
		}
		else
		{
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN b.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty
					from wo_po_color_size_breakdown a 
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id";
					
			$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty from wo_po_color_size_breakdown a
                    left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and ex.is_deleted=0 and ex.status_active=1 group by a.item_number_id, a.color_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];
				$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("bundle_qnty")];
				
			}
		}
	}
	else if( $variableSettings==3 ) //color and size level
	{
			/*echo "select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id";*/
			$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}
			
			$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty
					from wo_po_color_size_breakdown a
                    left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and ex.is_deleted=0 and ex.status_active=1  group by a.item_number_id, a.color_number_id, a.size_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];
				$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("bundle_qnty")];
				

			}
					
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by item_number_id, color_number_id, size_order";
				
			/*$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";*/
			//echo $sql;
	}
	else // by default color and size level
	{
			
			
			$prodData = sql_select("SELECT a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}
			
			$sql_exfac=sql_select("SELECT a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty from wo_po_color_size_breakdown a
                    left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and ex.is_deleted=0 and ex.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];
				$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("bundle_qnty")];
				
			}
					
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by item_number_id,  color_number_id,size_order";
			
			/*$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";*/
				
	}
	
	//print_r($ex_fac_value);die;
	
	$colorResult = sql_select($sql);		
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			
			 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]]).'" onblur="fn_colorlevel_total('.($i+1).','. 1 . ')"></td><td><input type="text" name="txt_bundle" id="txtbundle_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" onblur="fn_colorlevel_total('.($i+1).','. 2 . ')"></td></tr>';				
			$totalQnty += $color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
			$totalBundle += $color[csf("production_qnty")]-$ex_fac_bundle[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
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
			$exfac_bundle=$ex_fac_bundle[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];
			
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).','. 1 .')"></td><td>Bundle</td><td><input type="text" name="txt_bundle" id="txtbundle_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).','. 2 .')"></td></tr>';				
		}
		$i++; 
	}
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th><th width="80">Bundle</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th><th><input type="text" id="total_bundle" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="confirm_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
	function js_set_value(str)
	{
		$('#hidden_ref').val(str);
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body bgcolor="#FFFFFF">
		<input type="hidden" id="hidden_ref" >
		<table width="190">
			<tr height="60">
				<td colspan="2" align="center" valign="middle" style="font-size:20px; font-weight:bold;">Qnty Excceded by <? echo ($placeholder_value-$filed_value); ?></td>
			</tr>
			<tr valign="bottom">
				<td width="100" align="right" valign="bottom"><input type="button" value="OK" id="btn_ok" onClick="js_set_value(1)" style="width:80px;" class="formbutton"></td>
				<td width="100" valign="bottom"><input type="button" value="Cancel" id="btn_cancel" onClick="js_set_value(2)" style="width:80px;" class="formbutton"></td>
			</tr>
		</table>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
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
			$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
			$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
			$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,cut_delivery_date,cut_delivery_qnty,location,lc_sc_no,invoice_no,challan_no from   pro_cut_delivery_order_dtls where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by id");
 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";
					
				$total_production_qnty+=$selectResult[csf('cut_delivery_qnty')];	
  		
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
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_cutDelivery_form_data','requires/cutting_delevar_to_input_controller');" > 
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="110" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td width="110" align="center"><p><? echo change_date_format($selectResult[csf('cut_delivery_date')]); ?></p></td>
                    <td width="110" align="center"><p><? echo $selectResult[csf('cut_delivery_qnty')]; ?></p></td>
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
	?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Item Name</th>
            <th width="80">Country</th>
            <th width="75">Shipment Date</th>
            <th>Order Qty.</th>                    
        </thead>
    </table>
	<div id="scroll_body" style="width:388px; max-height:450px; overflow-x:hidden;  overflow-y:scroll;">   
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_body_1">
		<?  
		$i=1;
		$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
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



if($action=="populate_cutDelivery_form_data")
{
	$ex_fac_value=array();
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	
	$sqlResult =sql_select("select id,garments_nature,po_break_down_id,item_number_id,country_id,location,cut_delivery_qnty,total_carton_qnty,remarks,entry_break_down_type  from  pro_cut_delivery_order_dtls where id='$data' and status_active=1 and is_deleted=0 order by id");
 	foreach($sqlResult as $result)
	{
		 
		echo "$('#txt_ex_quantity').attr('placeholder','".$result[csf('cut_delivery_qnty')]."');\n";
 		echo "$('#txt_ex_quantity').val('".$result[csf('cut_delivery_qnty')]."');\n";
		echo "$('#txt_total_carton_qnty').val('".$result[csf('total_carton_qnty')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
		echo "$('#sewing_production_variable').val('".$result[csf('entry_break_down_type')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_cutDelivery',1,1);\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level

		$variableSettings = $result[csf('entry_break_down_type')];
		
		
		//$variableSettings=2;
		
		if( $variableSettings!=1 ) // color size level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			//echo "select a.color_size_break_down_id,a.production_qnty,a.bundle_qnty,b.size_number_id, b.color_number_id from  pro_cut_delivery_color_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'";
			$sql_dtls = sql_select("select a.color_size_break_down_id,a.production_qnty,a.bundle_qnty,b.size_number_id, b.color_number_id from  pro_cut_delivery_color_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	
			
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$bundleQty[$index] = $row[csf('bundle_qnty')];
				
			}  
			
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty
							from wo_po_color_size_breakdown a 
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id";
							
					$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty from wo_po_color_size_breakdown a
							left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and ex.is_deleted=0 and ex.status_active=1 group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id");
					foreach($sql_exfac as $row_exfac)
					{
						$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];
						$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("bundle_qnty")];
						
					}
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty
							from wo_po_color_size_breakdown a 
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id order by a.item_number_id, a.color_number_id";
							
					$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty  from wo_po_color_size_breakdown a
							left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and ex.is_deleted=0 and ex.status_active=1 group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id");
					foreach($sql_exfac as $row_exfac)
					{
						$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];
						$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("bundle_qnty")];
						
					}
				}
				
			}
			else if( $variableSettings==3 ) //color and size level
			{
					
				$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id");
										
				foreach($prodData as $row)
				{				  
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
				}
				
				$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty 
						from wo_po_color_size_breakdown a
						left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id order by a.item_number_id, a.color_number_id, a.size_number_id");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];
					$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("bundle_qnty")];
					
				}
						
				$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty 
						from wo_po_color_size_breakdown a
						where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by item_number_id,color_number_id, size_order";
				/*$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order, a.size_order";*/
				
			}
			else // by default color and size level
			{
					$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id");
				foreach($prodData as $row)
				{				  
					$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
				}
				
				$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty,sum(ex.bundle_qnty) as bundle_qnty from wo_po_color_size_breakdown a
						left join pro_cut_delivery_color_dtls ex on ex.color_size_break_down_id=a.id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id order by a.item_number_id, a.color_number_id, a.size_number_id ");
				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];
					$ex_fac_bundle[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("bundle_qnty")];
					
				}
						
				$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by item_number_id,color_number_id, size_order";
				/*$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order, a.size_order";*/
				
			}
			//echo $sql;die;
 			$colorResult = sql_select($sql);
 			//print_r($colorResult);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					$amount = $amountArr[$color[csf("color_number_id")]];
					$bundle = $bundleQty[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).','. 1 .')"></td><td><input type="text" name="txtbundle" id="txtbundle_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" value="'.$bundle.'" onblur="fn_colorlevel_total('.($i+1).','. 2 .')"></td></tr>';				
					$totalQnty += $amount;
					$totalBundle += $bundle;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					$bundle = $bundleQty[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
					$exfac_qnty=$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).','. 1 .')" value="'.$amount.'" ></td><td>Bundle</td><td><input type="text" name="txt_bundle" id="txtbundle_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px"  onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).','. 2 .')" value="'.$bundle.'" ></td></tr>';				
					$colorWiseTotal += $amount;
					$colorWiseTotalBundle += $bundle;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th><th width="80">Bundle</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th><th><input type="text" id="total_bundle" value="'.$totalBundle.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		else
		{
			//echo "$('#txt_total_carton_qnty').attr('disabled',false);\n";

			echo "$('#txt_ex_quantity').removeAttr('readonly');\n";
			echo "$('#txt_total_carton_qnty').removeAttr('readonly');\n";
		}
	}
 	exit();		
}

// pro_cut_delivery_order_dtls
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	// $country_id_arr=return_library_array("SELECT id, country_id from wo_po_color_size_breakdown", "id", "country_id");
	// $item_id_arr=return_library_array("SELECT id, item_number_id from wo_po_color_size_breakdown", "id", "item_number_id");

	//echo $operation;

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here 
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		if(str_replace("'","",$sewing_production_variable)==0 || str_replace("'","",$sewing_production_variable)=="")
		{
			$sewing_production_variable=3;
		}
		
		if(str_replace("'","",$txt_system_id)=="")
		{
			$delivery_mst_id=return_next_id("id", "pro_cut_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CDI', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from pro_cut_delivery_mst where company_id=$cbo_company_name $mrr_cond order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
			
			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location_id, challan_no, delivery_date, deliver_basis, knitting_source, knitting_company, cut_com_location,sewing_source,sewing_company,sewing_com_location, inserted_by, insert_date";
			$data_array_delivery="(".$delivery_mst_id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_name.",".$cbo_location_name.",".$new_sys_number[2].",".$txt_ex_factory_date.",".$cbo_delivery_basis.",".$cbo_knitting_source.",".$cbo_cutting_company.",".$cbo_cut_com_location.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_sew_com_location.",".$user_id.",'".$pc_date_time."')";
			$mrr_no=$new_sys_number[0];
			$mrr_no_challan=$new_sys_number[2];
			
		}
		else
		{
			$delivery_mst_id=str_replace("'","",$txt_system_id);
			$mrr_no=str_replace("'","",$txt_system_no);
			$mrr_no_challan=str_replace("'","",$txt_challan_no);
			
			$field_array_delivery="company_id*location_id*challan_no*delivery_date*deliver_basis*knitting_source*knitting_company*cut_com_location*sewing_source*sewing_company*sewing_com_location*updated_by*update_date";
			$data_array_delivery="".$cbo_company_name."*".$cbo_location_name."*".$txt_challan_no."*".$txt_ex_factory_date."*".$cbo_delivery_basis."*".$cbo_knitting_source."*".$cbo_cutting_company."*".$cbo_cut_com_location."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_sew_com_location."*".$user_id."*'".$pc_date_time."'";
			
		}
		
		$id=return_next_id("id", "pro_cut_delivery_order_dtls", 1);
		
		if(str_replace("'","",$cbo_delivery_basis)==1)
		{
			$field_array1="id, delivery_mst_id, garments_nature, buyer_id, po_break_down_id, item_number_id, country_id, location, cut_delivery_date, cut_delivery_qnty, total_carton_qnty, challan_no, remarks,  entry_break_down_type, inserted_by, insert_date,production_type";
			$data_array1="(".$id.",".$delivery_mst_id.",".$garments_nature.",".$cbo_buyer_name.",".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.",".$cbo_location_name.",".$txt_ex_factory_date.",".$txt_ex_quantity.",".$txt_total_carton_qnty.",".$mrr_no_challan.",".$txt_remark.",".$sewing_production_variable.",".$user_id.",'".$pc_date_time."','9')";
			
			// pro_cut_delivery_color_dtls table entry here ----------------------------------///
			$field_array="id,mst_id,color_size_break_down_id,production_qnty,bundle_qnty,production_type";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 and status_active=1 order by id");
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue); 
				$dtls_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					
					if($j==0)$data_array = "(".$dtls_id.",".$id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$colorSizeNumberIDArr[2]."',9)";
					else $data_array .= ",(".$dtls_id.",".$id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$colorSizeNumberIDArr[2]."',9)";
					$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowEx = explode("***",$colorIDvalue); 
				$dtls_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$colorSizebundle = $colorAndSizeAndValue_arr[3];
					$index = $sizeID.$colorID;
					if($j==0)$data_array = "(".$dtls_id.",".$id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$colorSizebundle."',9)";
					else $data_array .= ",(".$dtls_id.",".$id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$colorSizebundle."',9)";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			$DeliveryrID=$dtlsrID=$rID=true;
			if(str_replace("'","",$txt_system_id)=="")
			{
				$DeliveryrID=sql_insert("pro_cut_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$DeliveryrID=sql_update("pro_cut_delivery_mst",$field_array_delivery,$data_array_delivery,"id",str_replace("'","",$txt_system_id),1);
			}
			
			$rID=sql_insert("pro_cut_delivery_order_dtls",$field_array1,$data_array1,1);
	
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{
				$dtlsrID=sql_insert("pro_cut_delivery_color_dtls",$field_array,$data_array,1);
			} 
			 
		}
		else if(str_replace("'","",$cbo_delivery_basis)==2)
		{
			$field_array1="id, delivery_mst_id, garments_nature, buyer_id, po_break_down_id, item_number_id, country_id, location, cut_delivery_date,
			cut_delivery_qnty, total_carton_qnty, challan_no, remarks,  entry_break_down_type,cutting_no, inserted_by, insert_date";
			$DeliveryrID=$dtlsrID=$rID=true;
			$field_array="id,mst_id,color_size_break_down_id,production_qnty,bundle_qnty,bundle_no,production_type";
			$dtls_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
			$po_id_arr=array();
			$po_details_arr=array();
			
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$pobreakDownId="pobreakDownId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qcpass="qcpass_".$j;
				$bundleNo="bundleNo_".$j;
				if($j>1)
				{
					if(!in_array($$pobreakDownId,$po_id_arr))
					{
					$id++;	
					}
				}
				
				if($data_array!="") $data_array.= ",";
				$data_array.= "(".$dtls_id.",".$id.",'".$$colorSizeId."','".$$qcpass."',1,'".$$bundleNo."',9)";
				$po_details_arr[$$pobreakDownId]['po_no']=$$pobreakDownId;
				$po_details_arr[$$pobreakDownId]['color_size_id']=$$colorSizeId;
				$po_details_arr[$$pobreakDownId]['qty']+=$$qcpass;
				if($po_details_arr[$$pobreakDownId]['dtls_id']==''){
					$po_details_arr[$$pobreakDownId]['dtls_id']=$id;
				}
				if(str_replace("'","",$update_dtls_id)!="") $update_dtls_id.=",";
				$update_dtls_id.=$id;
				$po_details_arr[$$pobreakDownId]['carton_qty']+=1;
				$po_id_arr[]=$$pobreakDownId;
				$dtls_id++;	
			}
			foreach($po_details_arr as $p_id=>$p_value)
			{
				if($data_array1!="") $data_array1.= ",";	
				$data_array1.="(".$p_value['dtls_id'].",".$delivery_mst_id.",".$garments_nature.",".$cbo_buyer_name.",".$p_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_location_name.",".$txt_ex_factory_date.",".$p_value['qty'].",".$p_value['carton_qty'].",".$mrr_no_challan.",".$txt_remark.",3,".$txt_order_no.",".$user_id.",'".$pc_date_time."')";	
			}

			$DeliveryrID=sql_insert("pro_cut_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			$rID=sql_insert("pro_cut_delivery_order_dtls",$field_array1,$data_array1,1);
			$dtlsrID=sql_insert("pro_cut_delivery_color_dtls",$field_array,$data_array,1);
			$hidden_po_break_down_id=implode(",",array_unique(explode(",",$update_dtls_id)));
		}
		//echo "10**insert into pro_cut_delivery_order_dtls ($field_array1) values $data_array1";die;

		// ========

		//echo "10**". $DeliveryrID."**".$rID ."**". $dtlsrID  ."**".print_r($po_details_arr);die;


		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
		 
			if($DeliveryrID && $rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($DeliveryrID && $rID && $dtlsrID)
			{
				oci_commit($con); 
				echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
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
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//echo $cbo_delivery_basis;die;
		$delivery_mst_id=str_replace("'","",$txt_system_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		
		$field_array_delivery="company_id*location_id*challan_no*delivery_date*deliver_basis*knitting_source*knitting_company*cut_com_location*sewing_source*sewing_company*sewing_com_location*updated_by*update_date";
		$data_array_delivery="".$cbo_company_name."*".$cbo_location_name."*".$txt_challan_no."*".$txt_ex_factory_date."*".$cbo_delivery_basis."*".$cbo_knitting_source."*".$cbo_cutting_company."*".$cbo_cut_com_location."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_sew_com_location."*".$user_id."*'".$pc_date_time."'";
		
		//  pro_cut_delivery_order_dtls table data entry here
		if(str_replace("'","",$cbo_delivery_basis)==1)
		{
				
			$field_array1="garments_nature*buyer_id*location*cut_delivery_date*cut_delivery_qnty*total_carton_qnty*challan_no*remarks*entry_break_down_type*updated_by*update_date";
			$data_array1="".$garments_nature."*".$cbo_buyer_name."*".$cbo_location_name."*".$txt_ex_factory_date."*".$txt_ex_quantity."*".$txt_total_carton_qnty."*".$txt_challan_no."*".$txt_remark."*".$sewing_production_variable."*".$user_id."*'".$pc_date_time."'";
			
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
			{
				// pro_cut_delivery_color_dtls table entry here ----------------------------------///
				//echo "delete from pro_cut_delivery_color_dtls where mst_id=$txt_mst_id";die;
				$dtlsrDelete = execute_query("delete from pro_cut_delivery_color_dtls where mst_id=$txt_mst_id",1);
				$field_array="id,mst_id,color_size_break_down_id,production_qnty,bundle_qnty,production_type";
				
				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{	
				
					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 and status_active=1 order by id" );
					$colSizeID_arr=array(); 
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}	
					// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
					$rowEx = explode("**",$colorIDvalue); 
					$dtls_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode("*",$val);
						
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$colorSizeNumberIDArr[2]."',9)";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$colorSizeNumberIDArr[2]."',9)";
						$dtls_id=$dtls_id+1;							
						$j++;								
					}
				}
				if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{	
					
					$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 order by size_number_id,color_number_id" );
					$colSizeID_arr=array(); 
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].'*'.$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}	
					
					//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
					$rowEx = explode("***",$colorIDvalue); 
					$dtls_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];				
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$colorSizebundle = $colorAndSizeAndValue_arr[3];
						$index = $sizeID.'*'.$colorID;
						
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$colorSizebundle."',9)";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$colorSizebundle."',9)";
						$dtls_id=$dtls_id+1;
						$j++;
					}
				}
				
				
			}//end cond
			// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_delivery.") VALUES ".$data_array_delivery.""; die;
			$deliveryrID=sql_update("pro_cut_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$delivery_mst_id."",1);
			$rID=sql_update("pro_cut_delivery_order_dtls",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{
				$dtlsrID=sql_insert("pro_cut_delivery_color_dtls",$field_array,$data_array,1);
			} 
		}
		
		if(str_replace("'","",$cbo_delivery_basis)==2)
		{
			$field_array1="id, delivery_mst_id, garments_nature, buyer_id, po_break_down_id, item_number_id, country_id,location,cut_delivery_date, cut_delivery_qnty, total_carton_qnty, challan_no, remarks,  entry_break_down_type,cutting_no, inserted_by, insert_date";
			
			$DeliveryrID=$dtlsrID=$rID=true;
			$field_array="id,mst_id,color_size_break_down_id,production_qnty,bundle_qnty,bundle_no,production_type";
			$dtls_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
			$id=return_next_id("id", "pro_cut_delivery_order_dtls", 1);
			$po_id_arr=array();
			$po_details_arr=array();
			$update_dtls_id="";
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$pobreakDownId="pobreakDownId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qcpass="qcpass_".$j;
				$bundleNo="bundleNo_".$j;
				if($j>1)
				{
					if(!in_array($$pobreakDownId,$po_id_arr))
					{
					$id++;	
					}
				}
				
				if($data_array!="") $data_array.= ",";
				$data_array.= "(".$dtls_id.",".$id.",'".$$colorSizeId."','".$$qcpass."',1,'".$$bundleNo."',9)";
				$po_details_arr[$$pobreakDownId]['po_no']=$$pobreakDownId;
				$po_details_arr[$$pobreakDownId]['color_size_id']=$$colorSizeId;
				$po_details_arr[$$pobreakDownId]['qty']+=$$qcpass;
				$po_details_arr[$$pobreakDownId]['dtls_id']=$id;
				if(str_replace("'","",$update_dtls_id)!="") $update_dtls_id.=",";
				$update_dtls_id.=$id;
				$po_details_arr[$$pobreakDownId]['carton_qty']+=1;
				$po_id_arr[]=$$pobreakDownId;
				$dtls_id++;	
			}
			foreach($po_details_arr as $p_id=>$p_value)
			{
				if($data_array1!="") $data_array1.= ",";	
				$data_array1.="(".$p_value['dtls_id'].",".$delivery_mst_id.",".$garments_nature.",".$cbo_buyer_name.",".$p_id.", ".$cbo_item_name.",".$cbo_country_name.",".$cbo_location_name.",".$txt_ex_factory_date.",".$p_value['qty'].",".$p_value['carton_qty'].",".$mrr_no_challan.",".$txt_remark.",3,".$txt_order_no.",".$user_id.",'".$pc_date_time."')";	
				
			}
			$txt_mst_id=str_replace("'","",$txt_mst_id);
			
			$deliveryrID=sql_update("pro_cut_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$delivery_mst_id."",1);
			$sql_dtls_delete=execute_query("delete from pro_cut_delivery_order_dtls where delivery_mst_id in ($delivery_mst_id)",1);
			$sql_color_delete=execute_query("delete from pro_cut_delivery_color_dtls where mst_id in ($txt_mst_id)",1);
			$rID=sql_insert("pro_cut_delivery_order_dtls",$field_array1,$data_array1,1);
			$dtlsrID=sql_insert("pro_cut_delivery_color_dtls",$field_array,$data_array,1);
			$update_dtls_id=implode(",",array_unique(explode(",",$update_dtls_id)));
			
		}
		//echo $deliveryrID."**".$sql_dtls_delete."**".$sql_color_delete."**".$rID."**".$dtlsrID;die;
		if($db_type==0)
		{
			if(str_replace("'","",$cbo_delivery_basis)==1)
			{	
				
				if(str_replace("'","",$sewing_production_variable)!=1)
				{
					if($deliveryrID && $rID && $dtlsrID && $dtlsrDelete)
					{
						mysql_query("COMMIT");  
						echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".str_replace("'","",$hidden_po_break_down_id);
					}
				}
				else
				{
					if($deliveryrID && $rID)
					{
						mysql_query("COMMIT");  
						echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".str_replace("'","",$hidden_po_break_down_id);
					}
				}
			}
			else
			{
				if($deliveryrID && $rID && $dtlsrID && $sql_dtls_delete && $sql_color_delete)
					{
						mysql_query("COMMIT");  
						echo "1**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
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
		 if(str_replace("'","",$cbo_delivery_basis)==1)
			{
				if(str_replace("'","",$sewing_production_variable)!=1)
				{
					if($deliveryrID && $rID && $dtlsrID && $dtlsrDelete)
					{
						oci_commit($con); 
						echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
					}
					else
					{
						oci_rollback($con);
						echo "10**".str_replace("'","",$hidden_po_break_down_id);
					}
				}
				else
				{
					if($rID && $deliveryrID)
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
			else
			{
					
				if($deliveryrID && $rID && $dtlsrID && $sql_dtls_delete && $sql_color_delete)
				{
					oci_commit($con); 
					echo "1**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no."**".$mrr_no_challan."**".str_replace("'","",$cbo_delivery_basis);
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

		//echo "Dtls_id".$txt_mst_id."Test<br>".$txt_system_id."<br>";
  		
		// $rID = sql_delete(" pro_cut_delivery_order_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);
		// $dtlsrID = sql_delete(" pro_cut_delivery_color_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id',$txt_mst_id,1);

		$field_array_up="status_active*is_deleted*updated_by*update_date";
		$data_array_up="0*1*'".$user_id."'*'".$pc_date_time."'";

		// $rID1=sql_update("pro_cut_delivery_mst",$field_array_up,$data_array_up,"id",$txt_system_id,0); 	
		$rID=sql_update("pro_cut_delivery_order_dtls",$field_array_up,$data_array_up,"id",$txt_mst_id,0); 	
		$dtlsrID=sql_update("pro_cut_delivery_color_dtls",$field_array_up,$data_array_up,"mst_id",$txt_mst_id,0); 	
		// $dtlsrID=execute_query("UPDATE pro_cut_delivery_color_dtls set status_active=0,is_deleted=1 where mst_id=$txt_mst_id");
 		
 		if($db_type==0)
		{
			if($rID && $dtlsrID )
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
			if($rID && $dtlsrID)
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
//******************************************************************* ForCutting Scan*******************************************************


if($action=="cutqc_level")
{
	list($short_name)=explode('-',$data);
	// echo "<pre>";
	// print_r($short_name);
	// echo "</pre>";
	$convertToInt= convertToInt('B.BUNDLE_NO',array($short_name,'-'),'bundle');
		
	?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Order No</th>
            <th width="80">Bundle No</th>
            <th width="60">Qc Pass Qty.</th>
            <th width="">Check All &nbsp;<input id="all_check" name="all_check" type="checkbox" onClick="check_all('all_check')" checked/></th>                      
        </thead>
    </table>
	<div id="scroll_body" style="width:388px; max-height:350px; overflow-x:hidden;  overflow-y:scroll;">   
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="tbl_body_1">
    <tbody id="bundle_table_body">
		<?  
		$i=0;
		$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
		
		$sql_bundle=sql_select("SELECT b.order_id,b.color_id,b.size_id,b.bundle_no,b.qc_pass_qty,b.color_size_id,$convertToInt from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.cutting_no='$data' and b.status_active=1 order by bundle");

		$sql="SELECT B.BUNDLE_NO
		FROM PRO_GARMENTS_PRODUCTION_MST A,PRO_GARMENTS_PRODUCTION_DTLS B
		WHERE A.ID=B.MST_ID 
		AND B.PRODUCTION_TYPE=9
		AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 
		AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 
		AND B.CUT_NO='$data'";

		//echo $sql;

		$sql_res=sql_select($sql);
		$recv_bundle_arr=array();
		foreach ($sql_res as $row) 
		{
			$recv_bundle_arr[$row["BUNDLE_NO"]]=$row["BUNDLE_NO"];
		}
		
		$total_bundle_qty=0;
		foreach($sql_bundle as $row)
		{
			$i++;
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if ($recv_bundle_arr[$row["BUNDLE_NO"]] == "") 
			{ ?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" > 
					<td width="30"><? echo $i; ?></td>
					<td width="110" style="word-break:break-all;" align="center"><p><? echo $order_num_arr[$row[csf("order_id")]]; ?></p></td>
					<td width="80" align="center"><p><? echo $row[csf("bundle_no")]; ?>&nbsp;</p></td>
				
					<td align="right" width="60"><?  echo $row[csf("qc_pass_qty")]; ?>
					
					</td>
					<td align="right" width=""><input type="checkbox" id="bundle_check_<? echo $i; ?>" name="bundle_check[]" onChange="calculate_bundle_qty()" checked/>	
					
					
					<input type="hidden" id="bundleNo_<? echo $i; ?>" name="bundleNo[]" value="<?  echo $row[csf("bundle_no")]; ?>"/>
					<input type="hidden" id="qcpassQty_<? echo $i; ?>" name="qcpassQty[]" value="<?  echo $row[csf("qc_pass_qty")]; ?>"/>
					<input type="hidden" id="pobreakDownId_<? echo $i; ?>" name="pobreakDownId[]" value="<?  echo $row[csf("order_id")]; ?>"/>

					<input type="hidden" id="colorSizeId_<? echo $i; ?>" name="colorSizeId[]" value="<?  echo $row[csf('color_size_id')]; ?>"/>
					</td>
				</tr>
				<?	
				$total_bundle_qty+=$row[csf("qc_pass_qty")];
			} 
		}
		?>
        
        </tbody>
	</table>
    
    </div>
    <br/>
     <input type="hidden" id="txt_bundel_total" name="txt_bundel_total"   value="<? echo $total_bundle_qty;  ?>"/>
      <input type="hidden" id="txt_bundel_number" name="txt_bundel_number"   value="<? echo $i;  ?>"/>
   
	<?
	exit();
}


if($action=="cutqc_level_update")
{
	$dataArr= explode("_",$data);
	?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Order No</th>
            <th width="80">Bundle No hh</th>
            <th width="60">Qc Pass Qty.</th>
            <th width="">Check All &nbsp;<input id="all_check" name="all_check" type="checkbox" onClick="check_all('all_check')" checked/></th>                      
        </thead>
    </table>
	<div id="scroll_body" style="width:388px; max-height:350px; overflow-x:hidden;  overflow-y:scroll;">   
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="tbl_body_1">
    <tbody id="bundle_table_body">
		<?
		$inserted_bundle=array();
		$sql_update_bundle=sql_select("select a.bundle_no from pro_cut_delivery_color_dtls a,pro_cut_delivery_order_dtls b 
		 where b.delivery_mst_id=$dataArr[0] and b.id=a.mst_id and a.status_active=1 and a.is_deleted=0");
		foreach($sql_update_bundle as $val)
		{
			$inserted_bundle[$val[csf('bundle_no')]]=$val[csf('bundle_no')];
		}

		$i=0;
		$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
		$sql_bundle=sql_select("Select b.order_id,b.color_id,b.size_id,b.bundle_no,b.qc_pass_qty,b.color_size_id from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b
		where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.cutting_no='$dataArr[1]' and b.status_active=1");
		$total_bundle_qty=0;
		foreach($sql_bundle as $row)
		{
			$ceck_value="";
			if(in_array($row[csf('bundle_no')],$inserted_bundle)) { $ceck_value="checked"; }
			$i++;
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" > 
				<td width="30"><? echo $i; ?></td>
				<td width="110" style="word-break:break-all;" align="center"><p><? echo $order_num_arr[$row[csf('order_id')]]; ?></p></td>
				<td width="80" align="center"><p><? echo $row[csf('bundle_no')]; ?>&nbsp;</p></td>
			
				<td align="right" width="60"><?  echo $row[csf('qc_pass_qty')]; ?>
                  
                </td>
                <td align="right" width=""><input type="checkbox" id="bundle_check_<? echo $i; ?>" name="bundle_check[]" onChange="calculate_bundle_qty()"
                  <? echo $ceck_value;  ?>/>	
                <input type="hidden" id="bundleNo_<? echo $i; ?>" name="bundleNo[]" value="<?  echo $row[csf('bundle_no')]; ?>"/>
                <input type="hidden" id="qcpassQty_<? echo $i; ?>" name="qcpassQty[]" value="<?  echo $row[csf('qc_pass_qty')]; ?>"/>
                <input type="hidden" id="pobreakDownId_<? echo $i; ?>" name="pobreakDownId[]" value="<?  echo $row[csf('order_id')]; ?>"/>
                <input type="hidden" id="colorSizeId_<? echo $i; ?>" name="colorSizeId[]" value="<?  echo $row[csf('color_size_id')]; ?>"/>
                </td>
			</tr>
		<?	
		$total_bundle_qty+=$row[csf('qc_pass_qty')];
			
		}
		?>
        
        </tbody>
	</table>
    
    </div>
    <br/>
     <input type="hidden" id="txt_bundel_total" name="txt_bundel_total"   value="<? echo $total_bundle_qty;  ?>"/>
      <input type="hidden" id="txt_bundel_number" name="txt_bundel_number"   value="<? echo $i;  ?>"/>
   
	<?
	exit();
}
if($action=="populate_data_for_cutno_popup")
{
	
	$res = sql_select("select sum(a.po_quantity) as po_quantity,sum(a.plan_cut) as plan_cut,min(a.shipment_date) as shipment_date,b.company_name, b.buyer_name, b.style_ref_no, b.job_no   from wo_po_break_down a, wo_po_details_master b,pro_gmts_cutting_qc_mst c where a.job_no_mst=b.job_no  and c.job_no=b.job_no and c.cutting_no='$data' group by b.company_name, b.buyer_name, b.style_ref_no, b.job_no"); 
 	foreach($res as $result)
	{
		echo "$('#txt_order_qty').val('".$result[csf('po_quantity')]."');\n";
		//echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n"; 		
		//echo "$('#txt_shipment_date').val('".change_date_format($result[csf('shipment_date')])."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		$cutting_qty = return_field_value("sum(a.production_quantity) as production_quantity","pro_garments_production_mst a, wo_po_break_down b"," b.job_no_mst='".$result[csf('job_no')]."' and a.po_break_down_id=b.id 	 and a.production_type=1  and a.status_active=1 and a.is_deleted=0","production_quantity");
 		if($cutting_qty=="") $cutting_qty=0;
		$total_produced = return_field_value("sum(cut_delivery_qnty) as cut_delivery_qnty","pro_cut_delivery_order_dtls a,wo_po_break_down b"," a.po_break_down_id=b.id and b.job_no_mst='".$result[csf('job_no')]."' and a.status_active=1 and a.is_deleted=0","cut_delivery_qnty");
		//if($total_produced=="")$total_produced=0;
		
 		echo "$('#txt_finish_quantity').val('".$cutting_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
		$yet_to_produced = $cutting_qty-$total_produced;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
  	}
 	exit();	
}

 
if($action=="populate_data_from_cutting_update")
{

	$res = sql_select("select sum(a.po_quantity) po_quantity,sum(a.plan_cut) as plan_cut,b.company_name, b.buyer_name, b.style_ref_no, b.job_no,min(a.shipment_date)  as shipment_date from wo_po_break_down a, wo_po_details_master b,pro_cut_delivery_order_dtls d where d.po_break_down_id 	=a.id and a.job_no_mst=b.job_no and d.delivery_mst_id=$data group by b.company_name, b.buyer_name, b.style_ref_no, b.job_no"); 
	
 	foreach($res as $result)
	{
		echo "$('#txt_order_qty').val('".$result[csf('po_quantity')]."');\n";
		//echo "$('#cbo_item_name').val(".$item_id.");\n";
		//echo "$('#cbo_country_name').val(".$country_id.");\n";
		
		//echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		//echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n"; 		
		echo "$('#txt_shipment_date').val('".change_date_format($result[csf('shipment_date')])."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		
		$cutting_qty = return_field_value("sum(a.production_quantity) as production_quantity","pro_garments_production_mst a,wo_po_break_down b","a.po_break_down_id=b.id and b.job_no_mst='".$result[csf('job_no')]."' and a.production_type=1  and a.status_active=1 and a.is_deleted=0","production_quantity");
 		if($cutting_qty=="")$cutting_qty=0;
		
		$total_produced = return_field_value("sum(a.cut_delivery_qnty) as cut_delivery_qnty","pro_cut_delivery_order_dtls a,wo_po_break_down b","a.po_break_down_id=b.id and  b.job_no_mst='".$result[csf('job_no')]."' and a.status_active=1 and a.is_deleted=0","cut_delivery_qnty");
		if($total_produced=="")$total_produced=0;
		
 		echo "$('#txt_finish_quantity').val('".$cutting_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
		$yet_to_produced = $cutting_qty-$total_produced;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
  	}
 	exit();	
}



if($action=="populate_cutDelivery_details")
{
	$ex_fac_value=array();
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
 	if($db_type==0)
	{
	
	$sqlResult =sql_select("select group_concat(id) as mst_id,sum(cut_delivery_qnty) as cut_delivery_qnty,sum(total_carton_qnty) as total_carton_qnty,cutting_no,remarks,delivery_mst_id  from  pro_cut_delivery_order_dtls where delivery_mst_id=$data and status_active=1 and is_deleted=0  group by cutting_no,remarks,delivery_mst_id");
	}
	if($db_type==2)
	{
	$sqlResult =sql_select("select listagg((id),',') within group (order by id) as mst_id, sum(cut_delivery_qnty) as cut_delivery_qnty,sum(total_carton_qnty) as total_carton_qnty,cutting_no,remarks,delivery_mst_id  from  pro_cut_delivery_order_dtls where delivery_mst_id=$data and status_active=1 and is_deleted=0  group by cutting_no,remarks,delivery_mst_id");
	}
	
 	foreach($sqlResult as $result)
	{
		 
		echo "$('#txt_ex_quantity').attr('placeholder','".$result[csf('cut_delivery_qnty')]."');\n";
 		echo "$('#txt_ex_quantity').val('".$result[csf('cut_delivery_qnty')]."');\n";
		echo "$('#txt_total_carton_qnty').val('".$result[csf('total_carton_qnty')]."');\n";
		//echo "$('#txt_order_no').attr('placeholder','".$result[csf('cutting_no')]."');\n";
		echo "$('#txt_order_no').val('".$result[csf('cutting_no')]."');\n";
		echo "$('#txt_cutting_update').val('".$result[csf('cutting_no')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_mst_id').val('".$result[csf('mst_id')]."');\n";
		echo "$('#sewing_production_variable').val(3);\n";
	}
	
	
}


//*****************************************Cutting Scan Finish****************************************

if($action=="cut_delivery_print")
{    
	extract($_REQUEST);
	$data=explode('*',$data);
	$show_buyer_name=$data[5];
	
	echo load_html_head_contents("Cutting Delivery Info","../", 1, 1, $unicode,'','');
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$style_library=return_library_array( "select d.id, m.style_ref_no from wo_po_break_down d, wo_po_details_master m where m.job_no=d.job_no_mst", "id", "style_ref_no");
	$job_no_library=return_library_array( "select d.id, d.job_no_mst from wo_po_break_down d, wo_po_details_master m where m.job_no=d.job_no_mst", "id", "job_no_mst");
	if($db_type==0)
	{
		$delivery_mst_sql=sql_select("select id,challan_no,sys_number_prefix_num,knitting_source,knitting_company,delivery_date,company_id,DATE_FORMAT(insert_date, '%y') as insert_year, cut_com_location from pro_cut_delivery_mst where id=$data[1]");
	}
	else if($db_type==2)
	{
		$delivery_mst_sql=sql_select("select id,challan_no,sys_number_prefix_num,knitting_source,knitting_company,delivery_date,company_id,TO_CHAR(insert_date,'YY') as insert_year, cut_com_location,sewing_source,sewing_company,sewing_com_location from pro_cut_delivery_mst where id=$data[1]");
	}
	foreach($delivery_mst_sql as $row)
	{
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$knitting_source=$row[csf("knitting_source")];
		$knitting_company=$row[csf("knitting_company")];
		$delivery_date=$row[csf("delivery_date")];
		$com_id=$row[csf("company_id")];
		$insert_year=$row[csf("insert_year")];
		$cut_com_location=$row[csf("cut_com_location")];
		$sewing_source=$row[csf("sewing_source")];
		$sewing_company=$row[csf("sewing_company")];
		$sewing_com_location=$row[csf("sewing_com_location")];
	}
	
	$com_id=str_pad($com_id,2,'0',STR_PAD_LEFT);
	$cln_no=str_pad($challan_no,8,'0',STR_PAD_LEFT);
	$insert_year=str_pad($insert_year,2,'0',STR_PAD_LEFT);
	$barcode_creat_id=$com_id.$cln_no.$insert_year;

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div style="width:1010px;">
	    <table width="1000" cellspacing="0" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="3" align="center" width="70"><img src="../<? echo $image_location; ?>" height="50" width="60"></td>
	            <td align="center" width="600"  style="font-size:22px; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td id="barcode_img_id" align="right" style="font-size:22px" rowspan="3"></td>
	        </tr>
	        <tr>
	        	<td align="center" style="font-size:20px;"> Delivery Challan</td>
	        </tr>

	        <tr>
	        	<td align="center" style="font-size:16px;"><strong>Cutting Section</strong></td>
	        </tr>
	    </table>
		<table width="1110" cellspacing="0" style="margin-bottom:20px;">
	    	<tr>
	    		<td colspan="4" width="555"><h2 style="text-align:left;">Cutting Company : 
	    			<?
					if($knitting_source==1) echo $company_library[$knitting_company];  else echo $supplier_library[$knitting_company];
					
					?>
				</td>
				<td colspan="4" width="555"><h2 style="text-align:left;">
	    		 Cutting Company Location : 
	    				<?
					if($knitting_source==1)
					{
						$location=return_field_value("location_name","lib_location","id=".$cut_com_location,"location_name");
					}
					else
					{
						$location=return_field_value("address_1","lib_supplier","id=".$cut_com_location,"address_1");
					}
					echo $location;
				?>
	    			</h2>
	    		</td>
	    	</tr>
	        <tr >
	        	<td style="font-size:14px;font-weight:bold" width="30" valign="top" align="right">To : &nbsp;</td> 
	            <td style="font-size:14px;font-weight:bold" width="220" valign="top">
				<?
					if($sewing_source==1) echo $company_library[$sewing_company];  else echo $supplier_library[$sewing_company];
					
				?>
	            </td>
	            <td style="font-size:14px;font-weight:bold" width="100" valign="top" align="right">Location :</td> 
	            <td style="font-size:14px;font-weight:bold" width="220" valign="top"> &nbsp;
				<?
					if($sewing_source==1)
					{
						$location=return_field_value("location_name","lib_location","id=".$sewing_com_location,"location_name");
					}
					else
					{
						$location=return_field_value("address_1","lib_supplier","id=".$sewing_com_location,"address_1");
					}
					echo $location;
				?>
	            </td>
	            <td style="font-size:14px;font-weight:bold" width="100" valign="top" align="right">Challan No : &nbsp;</td>
	            <td style="font-size:14px;font-weight:bold" width="120" valign="top"><? echo $challan_no; ?> </td>
	            <td style="font-size:14px;font-weight:bold" width="100" valign="top" align="right">Date : &nbsp;</td>
	            <td style="font-size:14px;font-weight:bold" valign="top"><? echo change_date_format($delivery_date); ?> </td>
	        </tr>
	    </table>
	        <?
			$plan_cutqty_arr=array();
			$sql_qty=sql_select("select color_size_break_down_id,production_qnty from pro_cut_delivery_color_dtls where status_active=1 and is_deleted=0");
			foreach($sql_qty as $inf)
			{
				$plan_cutqty_arr[$inf[csf('color_size_break_down_id')]]=$inf[csf('production_qnty')];
			}

			$booking_no_arr=array();

			$sql_booking_no=sql_select("select a.booking_no, a.job_no from wo_booking_mst a where status_active=1 and is_deleted=0");
			foreach($sql_booking_no as $inf)
			{
				$booking_no_arr[$inf[csf('job_no')]] .=$inf[csf('booking_no')].', ';
			}

			$sql="select a.id as mst_id,a.challan_no,b.id as dtls_order_id,b.buyer_id, b.po_break_down_id, b.item_number_id,b.cutting_no, b.country_id, b.remarks, c.id as dtls_color_id, c.color_size_break_down_id, c.production_qnty, c.bundle_qnty,c.bundle_no from pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b,  pro_cut_delivery_color_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$data[1] order by a.id,b.id,c.id";
			//echo $sql;
			$result=sql_select($sql);

			$details_arr = array();

			foreach($result as $row)
	        {
	        	$details_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];

	        }
				
			?> 
	    <table cellspacing="0" width="1100"  border="1" rules="all" style="margin-top:-15px;" class="rpt_table">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
				<?
				if($show_buyer_name==1)
				{
				?>
				<th width="70">Buyer</th>
			   <?
				}
				?>
	            
	            <th width="100">Job No</th>
	            <th width="100">Style Ref</th>
	            <th width="120">Booking No</th>
	            <th width="100">Order</th>
	            <th width="100">Item</th>
	            <th width="80">Color</th>
	            <th width="100">Cut No</th>
	            <th width="100">Bundle No</th>
	            <th width="100">Country</th>
	            <th width="50">Size</th>
	            <th width="80">Qty(Pcs)</th>
	            <th width="80">Over Delivery</th>
	            <th width="50">No of bundle</th>
	            <th width="180"> Remarks </th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=array();
			$tot_over_qty=0;
	        foreach($details_arr as $row1)
	        {

	        	$sql1="select a.id as mst_id,a.challan_no,b.id as dtls_order_id,b.buyer_id, b.po_break_down_id, b.item_number_id,b.cutting_no, b.country_id, b.remarks, c.id as dtls_color_id, c.color_size_break_down_id, c.production_qnty, c.bundle_qnty,c.bundle_no from pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b,  pro_cut_delivery_color_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$data[1] and b.po_break_down_id=$row1 order by a.id,b.id,c.id";
			//echo $sql;
			$result1=sql_select($sql1);

			$total_row = count($result1);

			$k = 1;

			foreach($result1 as $row)
	        {
	            if ($i%2==0)  
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	            $color_count=count($cid);

	            $color_size_break_down_id = $row[csf("color_size_break_down_id")];

	            $sql_color_size=sql_select("select id, color_number_id, size_number_id,plan_cut_qnty from  wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and id=$color_size_break_down_id");
				foreach($sql_color_size as $row1)
				{
					$color_size_arr[$row1[csf("id")]]['color_number_id']=$row1[csf("color_number_id")];
					$color_size_arr[$row1[csf("id")]]['size_number_id']=$row1[csf("size_number_id")];
					$color_size_arr[$row1[csf("id")]]['plan_cut_qnty']=$row1[csf("plan_cut_qnty")];
				}
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><p><? echo $i;  ?></p></td>
					<?
					if($show_buyer_name==1)
					  {
					  ?>
					 <td><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<?
					  }
					 ?>
	                <?php
	                	if($k==1){
	                ?>
	                <td rowspan="<?php echo $total_row;?>" align="center" ><p><? echo $job_no_library[$row[csf("po_break_down_id")]]; ?></p></td>
	            	<?php }?>
	                <td><p><? echo $style_library[$row[csf("po_break_down_id")]]; ?>&nbsp;</p></td>
	                <?php
	                	if($k==1){
	                ?>
	                <td rowspan="<?php echo $total_row;?>" align="center"><p><? echo $booking_no_arr[$job_no_library[$row[csf("po_break_down_id")]]]; ?></p></td>
	                <?php }?>
	                <td><p><? echo $order_library[$row[csf("po_break_down_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $garments_item[$row[csf("item_number_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $color_library[$color_size_arr[$row[csf("color_size_break_down_id")]]['color_number_id']]; ?></p></td>
					
	                <td align="center"><p><? if($data[4]!=1) { echo $row[csf("cutting_no")];} ?></p></td>
	                <td align="center"><p><? echo $row[csf("bundle_no")] ?></p></td>
	                <td><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $size_library[$color_size_arr[$row[csf("color_size_break_down_id")]]['size_number_id']]; ?>&nbsp;</p></td>
	                <td align="right" style="padding-right:3px;"><p><? echo number_format($row[csf("production_qnty")],0,"",""); $tot_cut_qnty +=$row[csf("production_qnty")]; ?></p></td>
	                <td align="right" style="padding-right:3px;"><p>
					<? 
					 $over_qty=$plan_cutqty_arr[$row[csf("color_size_break_down_id")]]-$color_size_arr[$row[csf("color_size_break_down_id")]]['plan_cut_qnty'];
					 if($over_qty>0) { echo number_format($over_qty,0,"",""); $tot_over_qty +=$over_qty;}
					 ?></p></td>
	                 <td align="right" style="padding-right:3px;"><p><? echo number_format($row[csf("bundle_qnty")],0,"",""); $tot_bundle +=$row[csf("bundle_qnty")]; ?></p></td>
	                 <td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	            $k++;
		        }
		    }
		        ?>
	        </tbody>
	        <?
				if($show_buyer_name==1)
				{
				?>
				<tr>
	            <td colspan="12" align="right"><strong>Grand Total :</strong></td>
	            <td align="right"><? echo number_format($tot_cut_qnty,0,"",""); ?></td>
	            <td align="right"><? echo number_format($tot_over_qty,0,"",""); ?></td>
	            <td align="right"><? echo number_format($tot_bundle,0,"",""); ?></td>
	            <td align="right">&nbsp;</td>
	        </tr>    
				
			   <?
				}
				else{
				?>
				 <tr>
	            <td colspan="11" align="right"><strong>Grand Total :</strong></td>
	            <td align="right"><? echo number_format($tot_cut_qnty,0,"",""); ?></td>
	            <td align="right"><? echo number_format($tot_over_qty,0,"",""); ?></td>
	            <td align="right"><? echo number_format($tot_bundle,0,"",""); ?></td>
	            <td align="right">&nbsp;</td>
	        </tr>                           
                <?

				}
				?>
			
	       
	    </table>
		</div>
			 <?
	            echo signature_table(54, $data[0], "1100px",'',0);
	         ?>
		</div>
	     <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	     <script>

		function generateBarcode( valuess )
		{
			   
				var btype = 'code39';
				var renderer ='bmp';
				 
				var settings = {
				  output:renderer,
				  bgColor: '#FFFFFF',
				  color: '#000000',
				  barWidth: 1,
				  barHeight: 30,
				  moduleSize:5,
				  posX: 10,
				  posY: 20,
				  addQuietZone: 1
				};
				 valuess = {code:valuess, rect: false};
				
				$("#barcode_img_id").show().barcode(valuess, btype, settings);
			  
		} 
	  
		generateBarcode('<? echo $barcode_creat_id; ?>');
		 
		 
		 </script>
	            
	<?
	exit();	
}

if($action=="cut_delivery_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Cutting Delivery Info","../", 1, 1, $unicode,'','');
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$internal_ref=return_library_array( "select id, grouping from wo_po_break_down", "id", "grouping"  );
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$style_library=return_library_array( "select d.id, m.style_ref_no from wo_po_break_down d, wo_po_details_master m where m.job_no=d.job_no_mst", "id", "style_ref_no");
	if($db_type==0)
	{
		$delivery_mst_sql=sql_select("select id,challan_no,sys_number_prefix_num,knitting_source,knitting_company,delivery_date,company_id,DATE_FORMAT(insert_date, '%y') as insert_year, cut_com_location,sewing_source,sewing_company,sewing_com_location from pro_cut_delivery_mst where id=$data[1]");
	}
	else if($db_type==2)
	{
		$delivery_mst_sql=sql_select("select id,challan_no,sys_number_prefix_num,knitting_source,knitting_company,delivery_date,company_id,TO_CHAR(insert_date,'YY') as insert_year, cut_com_location,sewing_source,sewing_company,sewing_com_location from pro_cut_delivery_mst where id=$data[1]");
	}
	foreach($delivery_mst_sql as $row)
	{
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$knitting_source=$row[csf("knitting_source")];
		$knitting_company=$row[csf("knitting_company")];
		$delivery_date=$row[csf("delivery_date")];
		$com_id=$row[csf("company_id")];
		$insert_year=$row[csf("insert_year")];
		$cut_com_location=$row[csf("cut_com_location")];
		$sewing_source=$row[csf("sewing_source")];
		$sewing_company=$row[csf("sewing_company")];
		$sewing_com_location=$row[csf("sewing_com_location")];
	}
	
	$com_id=str_pad($com_id,2,'0',STR_PAD_LEFT);
	$cln_no=str_pad($challan_no,8,'0',STR_PAD_LEFT);
	$insert_year=str_pad($insert_year,2,'0',STR_PAD_LEFT);
	$barcode_creat_id=$com_id.$cln_no.$insert_year;

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div style="width:1010px;">
	    <table width="1010" cellspacing="0" style="margin-bottom:5px;">
	        <tr>
	            <td rowspan="3" align="left" width="201"><img src="../<? echo $image_location; ?>" height="50" width="60"></td>
	            <td align="center" width="608"  style="font-size:22px; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td id="barcode_img_id" width="201" align="right" style="font-size:22px" rowspan="3"></td>
	        </tr>
	        <tr>
	        	<td align="center" style="font-size:20px;"> Delivery Challan</td>
	        </tr>
	        <tr>
	        	<td align="center" style="font-size:16px;"><strong>Cutting Section</strong></td>
	        </tr>
	    </table>
	    <table width="1110" cellspacing="0" style="margin-bottom:20px;">
	    	<tr>
	    		<td colspan="8" width="1110"><h2 style="text-align:center;">Cutting Comoany : 
	    			<?
					if($knitting_source==1) echo $company_library[$knitting_company];  else echo $supplier_library[$knitting_company];
					
					?>,
	    		 Cutting Compoany Location : 
	    				<?
					if($knitting_source==1)
					{
						$location=return_field_value("location_name","lib_location","id=".$cut_com_location,"location_name");
					}
					else
					{
						$location=return_field_value("address_1","lib_supplier","id=".$cut_com_location,"address_1");
					}
					echo $location;
				?>
	    			</h2>
	    		</td>
	    	</tr>
	        <tr >
	        	<td style="font-size:14px;font-weight:bold" width="30" valign="top" align="right">To : &nbsp;</td> 
	            <td style="font-size:14px;font-weight:bold" width="220" valign="top">
				<?
					if($sewing_source==1) echo $company_library[$sewing_company];  else echo $supplier_library[$sewing_company];
					
				?>
	            </td>
	            <td style="font-size:14px;font-weight:bold" width="100" valign="top" align="right">Location :</td> 
	            <td style="font-size:14px;font-weight:bold" width="220" valign="top"> &nbsp;
				<?
					if($sewing_source==1)
					{
						$location=return_field_value("location_name","lib_location","id=".$sewing_com_location,"location_name");
					}
					else
					{
						$location=return_field_value("address_1","lib_supplier","id=".$sewing_com_location,"address_1");
					}
					echo $location;
				?>
	            </td>
	            <td style="font-size:14px;font-weight:bold" width="100" valign="top" align="right">Challan No : &nbsp;</td>
	            <td style="font-size:14px;font-weight:bold" width="120" valign="top"><? echo $challan_no; ?> </td>
	            <td style="font-size:14px;font-weight:bold" width="100" valign="top" align="right">Date : &nbsp;</td>
	            <td style="font-size:14px;font-weight:bold" valign="top"><? echo change_date_format($delivery_date); ?> </td>
	        </tr>
	    </table>
	   
	        <?
			$plan_cutqty_arr=array();
			$sql_qty=sql_select("select color_size_break_down_id,production_qnty from pro_cut_delivery_color_dtls where status_active=1 and is_deleted=0");
			foreach($sql_qty as $inf)
			{
				$plan_cutqty_arr[$inf[csf('color_size_break_down_id')]]=$inf[csf('production_qnty')];
			}
			
			$sql_color_size=sql_select("select id, color_number_id, size_number_id,plan_cut_qnty from  wo_po_color_size_breakdown where status_active=1");
			foreach($sql_color_size as $row)
			{
				$color_size_arr[$row[csf("id")]]['color_number_id']=$row[csf("color_number_id")];
				$color_size_arr[$row[csf("id")]]['size_number_id']=$row[csf("size_number_id")];
				$color_size_arr[$row[csf("id")]]['plan_cut_qnty']=$row[csf("plan_cut_qnty")];
			}
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			$sql="select a.id as mst_id,a.challan_no,b.id as dtls_order_id,b.buyer_id, b.po_break_down_id, b.item_number_id,b.cutting_no, b.country_id, b.remarks, c.id as dtls_color_id, c.color_size_break_down_id, c.production_qnty, c.bundle_qnty,c.bundle_no from pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b,  pro_cut_delivery_color_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$data[1] order by a.id,b.id,c.id";
			// echo $sql;
			$result=sql_select($sql);
				
			?> 
	    <table cellspacing="0" width="1110"  border="1" rules="all" class="rpt_table">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="70">Buyer</th>
	            <th width="100">Style Ref</th>
	            <th width="100">Order</th>
	            <th width="100">Internal Ref.</th>
	            <th width="100">Item</th>
	            <th width="100">Color</th>
	            <th width="100">Country</th>
	            <th width="80">Size</th>
	            <th width="80">Qty(Pcs)</th>
	            <th width="80">Over Delivery</th>
	            <th width="50">No of bundle</th>
	            <th> Remarks </th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=array();
			$tot_over_qty=0;
	        foreach($result as $row)
	        {
	            if ($i%2==0)  
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	            $color_count=count($cid);
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><p><? echo $i;  ?></p></td>
	                <td><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $style_library[$row[csf("po_break_down_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $order_library[$row[csf("po_break_down_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $internal_ref[$row[csf("po_break_down_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $garments_item[$row[csf("item_number_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $color_library[$color_size_arr[$row[csf("color_size_break_down_id")]]['color_number_id']]; ?>&nbsp;</p></td>
	                <td><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td><p><? echo $size_library[$color_size_arr[$row[csf("color_size_break_down_id")]]['size_number_id']]; ?>&nbsp;</p></td>
	                <td align="right" style="padding-right:3px;"><p><? echo number_format($row[csf("production_qnty")],0,"",""); $tot_cut_qnty +=$row[csf("production_qnty")]; ?></p></td>
	                <td align="right" style="padding-right:3px;"><p>
					<? 
					 $over_qty=$plan_cutqty_arr[$row[csf("color_size_break_down_id")]]-$color_size_arr[$row[csf("color_size_break_down_id")]]['plan_cut_qnty'];
					 if($over_qty>0) { echo number_format($over_qty,0,"",""); $tot_over_qty +=$over_qty;}
					 ?></p></td>
	                 <td align="right" style="padding-right:3px;"><p><? echo number_format($row[csf("bundle_qnty")],0,"",""); $tot_bundle +=$row[csf("bundle_qnty")]; ?></p></td>
	                 <td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        </tbody>
	        
	        <tr>
	            <td colspan="9" align="right"><strong>Grand Total :</strong></td>
	            <td align="right"><? echo number_format($tot_cut_qnty,0,"",""); ?></td>
	            <td align="right"><? echo number_format($tot_over_qty,0,"",""); ?></td>
	            <td align="right"><? echo number_format($tot_bundle,0,"",""); ?></td>
	            <td align="right">&nbsp;</td>
	        </tr>                           
	    </table>
		</div>
			 <?
	            // echo signature_table(54, $data[0], "900px");
			 	echo signature_table(123, $data[0], "900px",$template_id);
	         ?>
		</div>
	     <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	     <script>

		function generateBarcode( valuess )
		{
			   
				var btype = 'code39';
				var renderer ='bmp';
				 
				var settings = {
				  output:renderer,
				  bgColor: '#FFFFFF',
				  color: '#000000',
				  barWidth: 1,
				  barHeight: 30,
				  moduleSize:5,
				  posX: 10,
				  posY: 20,
				  addQuietZone: 1
				};
				 valuess = {code:valuess, rect: false};
				
				$("#barcode_img_id").show().barcode(valuess, btype, settings);
			  
		} 
	  
		generateBarcode('<? echo $barcode_creat_id; ?>');
		 
		 
		 </script>
	            
	<?
	exit();	
}

// Print 2
if($action=="cutting_delivery_to_input_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_name_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	
	$order_array=array();
	$order_sql="select a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);
	
	 $sql="select a.id as mst_id,b.id as dtls_id,a.delivery_date,a.deliver_basis,a.location_id,a.knitting_source,a.knitting_company,a.challan_no,b.id as dtls_order_id,b.buyer_id, b.po_break_down_id, b.item_number_id,b.cutting_no, b.country_id, b.remarks from pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1] order by a.id,b.id";
	
		//echo $sql;
	//echo $sql;
	$dataArray=sql_select($sql);
	$delivery_basis_arr=array(1=>"Order No",2=>"Cut Number",3=>"Bundle Number");
	$cutting_no=$dataArray[0][csf('cutting_no')]; 
	$deliver_basis=$dataArray[0][csf('deliver_basis')];
	if($deliver_basis==2)
	{
	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px"> 
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;'; 
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;'; 
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;'; 
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;'; 
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;'; 
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;'; 
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[2];  ?>/Gate Pass</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?> 
        	<td width="100"><p><strong>Buyer :</strong></p></td>  <td> <? echo  $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']];?></td>
            <td width="125"><strong>Issue Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            <td width="125"><strong>Issue To :</strong></td><td width="175px"><?  if($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; else if($dataArray[0][csf('knitting_source')]==3) echo $supplier_library[$dataArray[0][csf('knitting_company')]]; ?></td>
        </tr>
        <tr>
        <td> <strong>Order No :</strong></td> <td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td> 
         <td><strong>Style :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
         <td><strong>Unit :</strong></td><td><? echo $location_name_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
           <td><strong>Order Qty :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>Line :</strong></td><td><? //echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Job no :</strong></td><td><? echo  $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
        	<td><strong>Emb Type :</strong></td><td><? //echo $delivery_basis_arr[$dataArray[0][csf('deliver_basis')]];//if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; //echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>User ID :</strong></td><td><? //echo $garments_item[$dataArray[0][csf('deliver_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Embel. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
        </tr>
    </table>
         <br>
        <?
			$mst_id=$dataArray[0][csf('dtls_id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql_cutting="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_cut_delivery_color_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql_cutting;
			$result=sql_select($sql_cutting);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}
		$sql_color="SELECT sum(a.production_qnty) as production_qnty,sum(a.bundle_qnty) as bundle_qnty,sum(a.reject_qty) as reject_qty,a.bundle_no as bundle_no, b.country_id, b.color_number_id from pro_cut_delivery_color_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id,b.country_id,a.bundle_no ";
			
			//echo $sql; and a.production_date='$production_date'
			$result_data=sql_select($sql_color);
			$color_array=array ();$color_country_array=array ();
			foreach ( $result_data as $row )
			{
				$tot_bundle=count($row[csf('bundle_no')]);
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$color_country_array[$row[csf('color_number_id')]]['country']=$row[csf('country_id')];
				$color_country_array[$row[csf('color_number_id')]]['bundle_qnty']=$row[csf('bundle_qnty')];
				$color_country_array[$row[csf('color_number_id')]]['reject_qty']=$row[csf('reject_qty')];
				$color_country_array[$row[csf('color_number_id')]]['bundle_no']+=$tot_bundle;//$row[csf('bundle_no')];
			}
			  $sql_cut="select b.color_id,sum(b.reject_qty) as reject_qty from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b where a.id=b.mst_id  and b.order_id=$po_break_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_id";
			$result_rej=sql_select($sql_cut);
			$cutting_reject_array=array();
			foreach ( $result_rej as $row )
			{
				$cutting_reject_array[$row[csf('color_id')]]=$row[csf('reject_qty')];
			}
			if($db_type==2) $grp_concat="listagg((batch_id),',') within group (order by batch_id) as batch_id";else  $grp_concat="group_concat(batch_id) as batch_id";
			//echo "select $grp_concat ,company_id from ppl_cut_lay_mst where  entry_form=76";
			$cut_batch=sql_select("select cutting_no,batch_id  from ppl_cut_lay_mst where  entry_form=76");
			$cutting_batch_no_arr=array();
			 foreach($cut_batch as $cut_val)
			 {
				 $cutting_batch_no_arr[$cut_val[csf('cutting_no')]]=$cut_val[csf('batch_id')];
			 } 
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_name_arr=return_library_array("select id,country_name from  lib_country ","id","country_name");
			$batch_no_arr=return_library_array("select id,batch_no from   pro_batch_create_mst ","id","batch_no");
			
			$table_width=750+(count($size_array)*50);
		?> 
	<div style="width:100%;">
    <table align="left" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;">
        <thead bgcolor="#dddddd" align="center">
        <tr>
            <th width="30" rowspan="2">SL</th>
            <th width="80" rowspan="2" align="center">Color</th>
            <th width="80" rowspan="2" align="center">Country</th>
            <th width="80" rowspan="2" align="center">Batch No</th>
            <th width="80" rowspan="2" align="center">Cutting no</th>
            <th align="center" width="50" colspan="<? echo count($size_array); ?>">Size</th>
				
            <th width="80" rowspan="2" align="center">Total Issue Qty.</th>
            <th width="80" rowspan="2" align="center">Reject Qty</th>
            <th width="80" rowspan="2" align="center">No of Bundle</th>
            <th width="80" rowspan="2" align="center">Remark</th>
        </tr>
        <tr>
        	<?
                 $i=0;
				foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="50" rowspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
        </tr>
        </thead>
            <tbody>
            <?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;$color_check_arr=array();$k=1;
            $tot_qnty=array();$grand_total_size_qty=array();
            foreach($color_array as $cid)
            {
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";
            $color_count=count($cid);
            $batch_data=explode(",",$cutting_batch_no_arr[$cutting_no]);
            $batch_no='';
            foreach($batch_data as $b_id)
            {
            if($batch_no=='') $batch_no=$batch_no_arr[$b_id];else $batch_no.=','.$batch_no_arr[$b_id];
            }
            $color_check_id=$cid;
            if (!in_array($color_check_id,$color_check_arr) )
            {
            if($k!=1)
            {
            ?>
            <tr>
                <td colspan="5" align="right"><strong>Sub. Total : </strong></td>
                <?
                 foreach ($size_array as $sizval)
                {
                ?>
                <td align="right"><strong><? echo number_format($tot_qnty_size[$sizval],2); ?></strong></td>
                <?
                }
                ?>
                 <td align="right"><strong><? 
                 $tot_issue=0; $tot_reject=0;
                  foreach($color_array as $cid)
                  {
                     $tot_issue+=$tot_qnty[$cid];//echo number_format($tot_qnty[$cid],2);
                     $tot_reject=$cutting_reject_array[$cid];  
                  }
                 echo  number_format($tot_issue,2);// number_format($tot_qnty[$cid],2); ?></strong></td>
                 <td align="right"><strong><? echo number_format($tot_reject,2); ?></strong></td>
                 <td align="right"><strong><? //echo $dataArray[0][csf('remarks')]; ?></strong></td>
                 <td align="right"><strong><? //echo $dataArray[0][csf('remarks')]; ?></strong></td>
            </tr>
            <?
            }
            ?>
            <?
            unset($tot_qnty_size);
            unset($tot_qnty);
           //unset($cutting_reject_array);
            $color_check_arr[]=$color_check_id; 
            $k++;    
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i;  ?></td>
                <td><? echo $colorarr[$cid]; ?></td>
                <td><? echo $country_name_arr[$color_country_array[$cid]['country']]; ?></td>
                <td><? echo  $batch_no; ?></td>
                <td><? echo $cutting_no; ?></td>
                <?
                foreach ($size_array as $sizval)
                {
                $size_count=count($sizval);
                ?>
                <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                <?
                $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
                $tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                $grand_total_size_qty[$sizval]+=$qun_array[$cid][$sizval];
                }
               //	echo $cid;
                ?>
                <td align="right"><? echo number_format($tot_qnty[$cid],2); ?></td>
                <td align="right"><? echo number_format($cutting_reject_array[$cid],2); ?></td>
                <td align="right"><? echo  $color_country_array[$cid]['bundle_no']; ?></td>
                <td align="right"><? echo  $dataArray[0][csf('remarks')]; ?></td>
            </tr>
            <?
            $production_quantity+=$tot_qnty[$cid];
            //$prod_qty_size+=$qun_array[$sizval];
            $bundle_qty+=$color_country_array[$cid]['bundle_qnty'];
            $reject_qty+=$cutting_reject_array[$cid];
            //$grand_total_size_qty[$cid]+=$g_tot_qnty_size[$cid];
            //unset($tot_qnty_size[$sizval]);
            $i++;
            }
            ?>
            </tbody>
             <tr>
                <td colspan="5" align="right"><strong>Sub. Total : </strong></td>
                <?
                 foreach ($size_array as $sizval)
                    {
                ?>
                <td align="right"><strong><? echo number_format($tot_qnty_size[$sizval],2); ?></strong></td>
                <?
                    }
                    //echo $cid;
                ?>
                 <td align="right"><strong><? 
                 $tot_issue=0; $tot_reject=0;
                  foreach($color_array as $cid)
                  {
                     $tot_issue+=$tot_qnty[$cid];//echo number_format($tot_qnty[$cid],2);  
                      $tot_reject=$cutting_reject_array[$cid]; //$tot_qnty[$cid];
                  }
                  echo $tot_issue;
                 ?></strong></td>
                 <td align="right"><strong><? echo number_format($tot_reject,2); ?></strong></td>
                 <td align="right"><strong><? //echo number_format($tot_qnty_size[$sizval],2); ?></strong></td>
                 <td align="right"><strong><? //echo number_format($tot_qnty_size[$sizval],2); ?></strong></td>
            </tr>
        	<tr>
                <td colspan="5" align="right"><strong>Grand Total :</strong></td>
                <?
                    foreach ($size_array as $sizval)
                    {
                        //echo $sizval;
                        ?>
                        <td align="right"><strong><?php echo number_format($grand_total_size_qty[$sizval],2);//$tot_qnty_size[$sizval]; ?></strong></td>
                        <?
                    }
                ?>
                <td align="right"><strong><?php echo number_format($production_quantity,2); ?></strong></td>
                <td align="right"><strong><?php echo number_format($reject_qty,2); ?></strong></td> 
                <td align="right"><?php //echo $production_quantity; ?></td>
                <td align="right"><?php //echo $reject_qty; ?></td>
                 
        	</tr>                           
    </table>
    &nbsp;<br>
    <table align="right" cellspacing="0" width="900" style="display:none" >
        <tr>
            <td width="80"><strong>Remarks : </strong></td>
            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(54, $data[0], "900px");
         ?>
	</div>
	</div>
<?
	}
exit();	
}

?>