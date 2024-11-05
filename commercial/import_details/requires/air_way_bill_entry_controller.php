<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
//--------------------------- Start-------------------------------------//
if ($action=="load_buyer_dropdown")
{
	echo create_drop_down( "cbo_buyer_id",212,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.TAG_COMPANY=$data order by buy.buyer_name","id,buyer_name",1,'-Select',0,"",0);  
 	exit();
}

if ($action=="load_dealing_merchant_dropdown")
{
	echo create_drop_down( "cbo_dealing_merchant", 212, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}
if ($action=="load_currier_dropdown")
{
	$supplier_sql = "SELECT a.id,a.short_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where b.supplier_id=a.id and c.supplier_id=a.id and c.tag_company=$data and b.party_type=97 and a.status_active=1 and a.is_deleted=0";
	// echo $supplier_sql;die;
	echo create_drop_down( "cbo_currier_name", 212,$supplier_sql,"id,short_name", 1, "-- Select Currier --", $selected, "" );
	exit();	
}

if ($action=="sys_id_popup")
{
	echo load_html_head_contents("Style Name", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

    <script>

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		
		function js_set_value( name)
		{
			$('#txt_selected_name').val( name );
			parent.emailwindow.hide();
			
		}

	
    </script>

	</head>

	<body>
		<div align="center" style="width:1000px;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<fieldset style="width:1000px;margin-left:10px">
					<table style="margin-top:10px" width="990" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<th>Company</th>
							<th>Buyer</th>
							<th>Style Name</th>
							<th>Air Way Bill</th>
							<th>SYS Id</th>
							<th>Bill Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:70px;"></th>
							<input type="hidden" name="txt_selected_name" id="txt_selected_name" value="" />
						</thead>
						<tr class="general">
							<td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $company_id, "load_drop_down( 'air_way_bill_entry_controller',this.value, 'load_buyer_dropdown', 'buyer_td' );" );
								?>
							</td>
							<td align="center" id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id",212,array(),'',1,'-Select',1,"",0);
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px">
							</td>
							<td align="center">
								<input type="text" name="txt_air_way_bill" id="txt_air_way_bill" class="text_boxes" style="width:100px">
							</td>
							<td align="center">
								<input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:100px">
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_air_way_bill').value+'_'+document.getElementById('txt_sys_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sys_id_popup_list_view', 'search_div', 'air_way_bill_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down('air_way_bill_entry_controller',document.getElementById('cbo_company_id').value, 'load_buyer_dropdown', 'buyer_td' );
	</script>
	</html>
	<?
}

if($action=="create_sys_id_popup_list_view")
{
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	list($company_id,$buyer_id,$style_no,$air_way_bill,$sys_id,$date_from,$date_to)=explode('_',$data);
	
	if($sys_id!=''){
		$whereCon.=" and BILL_SYSTEM_ID like('%$sys_id')";
		}
	if($company_id!=0){
		$whereCon.=" and COMPANY_ID=$company_id";
		}
	if($buyer_id!=0){
		$whereCon.=" and BUYER_ID=$buyer_id";
		}
	if($style_no!=""){
		$whereCon.=" and style_no like('%$style_no')";
		}
	if($air_way_bill!=""){
		$whereCon.=" and air_way_bill like('%$air_way_bill')";
		}
	
	if($date_from!='' && $date_to!='')
	{
		if($db_type==0)
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
		$whereCon .= " and BILL_DATE between '".$date_from."' and '".$date_to."'";
	}

	$sql= "SELECT ID,BILL_PREFIX,BILL_PREFIX_NUMBER,BILL_SYSTEM_ID,COMPANY_ID,BUYER_ID,CURRIER_NAME,AIR_WAY_BILL,COUNTRY_ID,TEAM_LEADER,DEALING_MERCHANT,STYLE_STATUS,STYLE_NAME,STYLE_QTY,BILL_DATE,WEIGHT,CHARGE_USD,TOTAL_CHARGE_USD,CHARGE_BDT,RATE from air_way_bill_entry_mst where status_active = 1 AND is_deleted=0 $whereCon";
	  //echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="90">Buyer</th>
                <th width="80">Sys No</th>
                <th width="90">Style No</th>
                <th width="60">Rate</th>
                <th width="60">Bill Date</th>
            </thead>
		</table>
		<div style="width:840px; max-height:270px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search">
                <?
                $i=1; $pi_row_id="";
                $nameArray=sql_select( $sql );
                foreach ($nameArray as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					
            		?>
                    <tr height="40" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<?= $row["ID"]."_".$row["BILL_SYSTEM_ID"];?>')">
                        <td width="30" align="center"><?= $i; ?></td>
                        <td width="90"><p><?= $buyer_arr[$row['BUYER_ID']]; ?></p></td>
                        <td width="80"><?= $row['BILL_SYSTEM_ID']; ?></td>
                        <td width="90"><p><?= $row['STYLE_NAME']; ?></p></td>
                        <td width="60" align="center"><p><?= $row['RATE']; ?></p></td>
                        <td width="60"><p><?= $row['BILL_DATE']; ?></p></td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
            </table>
        </div>
        
	</div>
	<?
	exit();
}



if ($action=="style_popup")
{
	echo load_html_head_contents("Style Name", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

    <script>

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value(data)
		
		{
			
			var d=$('#cbo_po_id').val(data);
			parent.emailwindow.hide();
		}
    </script>

	</head>

	<body>
		<div align="center" style="width:850px;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<fieldset style="width:840px;margin-left:10px">
					<table style="margin-top:10px" width="840" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<th>Company</th>
							<th>Buyer</th>
							<th>Job No</th>
							<th>Order No</th>
							<th>Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:70px;"></th>
							<input type="hidden" name="cbo_po_id" id="cbo_po_id" value="" />
						</thead>
						<tr class="general">
							<td align="center">
								<?
									echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $company_id, "load_drop_down( 'air_way_bill_entry_controller',this.value, 'load_buyer_dropdown', 'buyer_td' );" );
								?>
							</td>
							<td align="center" id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id",120,array(),'',1,'-Select',1,"",0);
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px">
							</td>
							<td align="center">
								<input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:80px">
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $style_status; ?>, 'create_style_popup_list_view', 'search_div', 'air_way_bill_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');reset_hide_field(0);set_all();" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down('air_way_bill_entry_controller',document.getElementById('cbo_company_id').value, 'load_buyer_dropdown', 'buyer_td' );
	</script>
	</html>
	<?
}
if($action=="show_po_listview")
{
	$data = explode("**", $data);
	// echo "<pre>";
	// print_r($data);
	$company_id = $data[0];
	$PoId = $data[1];
	$job_no = $data[2];
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	?>
    
		<table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
			<thead>
				<th colspan="8" width="">&nbsp;</th>
				<th align="right" width="160" style="color:#F00">Remaining Amount :</th>
				<th id="tot_remain" width="110" style="color:#F00">0</th>
			</thead>
		</table>            
       <table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="100">Buyer Name</th>
            <th width="80">Order Status</th>
            <th width="100">PO Number</th>
            <th width="90">Job Number</th>
            <th width="100">Style Name</th>
            <th width="120">Item Name</th>
            <th width="80">Shipment Date</th>
            <th width="80">Order Quantity</th>
            <th>Amount(TK.)</th>
        </thead>
		</table>
		<div style="width:900px; overflow-y:scroll; max-height:250px;" id="search_div">
    	<table cellspacing="0" width="900" class="rpt_table" border="1" rules="all" id="table_body">
        <tbody> 
		<?
		 $select_field='';
		 $select_field='currier_pre_cost';
        $sql_cond="";
		// if($buyer_id>0)  $sql_cond.=" and a.buyer_name=$buyer_id";
		//  if($PoId!="")  $sql_cond.=" and b.ID=$PoId ";
		 if($style!="")  $sql_cond.=" and a.a.job_no='$style'";
		 if($job_no!="")  $sql_cond.=" and a.job_no='$job_no'";
		$sql="select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, b.id as po_id, b.po_number, b.po_quantity, b.is_confirmed, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by b.pub_shipment_date, a.id";
		 //echo $sql;
		$result=sql_select($sql);
		 $sql_prev_data="select id, po_id, amount from AIR_WAY_BILL_ENTRY_DTLS where company_id='$company_id' and job_no='$job_no' and status_active=1 and is_deleted=0";
		
		$sql_prev_data_result=sql_select($sql_prev_data);
		foreach($sql_prev_data_result as $row)
		{
			$amnt_arr[$row[csf("po_id")]]=$row[csf("amount")];
			$prev_id_arr[$row[csf("po_id")]]=$row[csf("id")];
		}
        $i=1; $tot_po_qty=0; $tot_amount=0;
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_qty=$row[csf("total_set_qnty")]*$row[csf("po_quantity")];
			$tot_po_qty+=$po_qty; 
			$amount=$amnt_arr[$row[csf("po_id")]];
			if($amount>0) $amount=$amount;
			else $amount=$amnt_errision_val;
			
			$prev_dtlsid=$prev_id_arr[$row[csf("po_id")]];
			$tot_amount+=$amount;
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                <td width="80"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
              	<td width="100"><p><? echo $row[csf("po_number")]; ?></p>
                    <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo $i; ?>" value="<? echo $row[csf("po_id")]; ?>">
                </td>
                <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                <td width="120">
                    <p>
						<? 
							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf("gmts_item_id")]);
							foreach($gmts_item_id as $item_id)
							{
								$gmts_item.=$garments_item[$item_id].",";
							}
							$gmts_item=substr($gmts_item,0,-1); 
                        	echo $gmts_item; 
                        ?>
                    </p>
                </td>
                <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                <td width="80" align="right"><? echo $po_qty; ?></td>
                <td align="center">
                    <input type="text" name="txt_total_charge_usd[]" id="txt_total_charge_usd_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $amount; ?>" onkeyup="calculate_balance(<? echo $i; ?>);" title="<? echo $po_qty; ?>">
					<input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id_<? echo $i; ?>" value="<? echo $prev_dtlsid; ?>" />
                    
                </td>
            </tr>
			<?	
				$i++;
			}
			?>
			</tbody>
			</table>
		</div>
		<table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
			<tfoot>	 
			<th colspan="8">Total</th>
			<th align="right" width="80" id="td_tot_po_qnty"><? echo $tot_po_qty; ?></th>
			<th align="center" width="90" style="padding-right:20px;"><input type="text" name="tot_amount" id="tot_amount" style="width:70px;" class="text_boxes_numeric" value="<? echo $tot_amount; ?>" readonly="readonly"></th>	
			</tfoot>	
		</table>
	<?
		 
		exit();
}


if($action=="show_po_listview_master")
{
	$sys_id = explode("*", $data);
	// echo "<pre>";
	// print_r($data);
	// echo "*".$sys_id[0];die;
	$system_id=$sys_id[0];
	$company_id=$sys_id[1];
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	?>
		<table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
			<thead>
				<th colspan="8" width="">&nbsp;</th>
				<th align="right" width="160" style="color:#F00">Remaining Amount :</th>
				<th id="tot_remain" width="110" style="color:#F00">0</th>
			</thead>
		</table>            
       <table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="100">Buyer Name</th>
            <th width="80">Order Status</th>
            <th width="100">PO Number</th>
            <th width="90">Job Number</th>
            <th width="100">Style Name</th>
            <th width="120">Item Name</th>
            <th width="80">Shipment Date</th>
            <th width="80">Order Quantity</th>
            <th>Amount(TK.)</th>
        </thead>
		</table>
		<div style="width:900px; overflow-y:scroll; max-height:250px;" id="search_div">
    	<table cellspacing="0" width="900" class="rpt_table" border="1" rules="all" id="table_body">
        <tbody> 
		<?
		 $select_field='';
		 $select_field='currier_pre_cost';
        $sql_cond="";
		$sql="select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, b.id as po_id, b.po_number, b.po_quantity, b.is_confirmed, b.pub_shipment_date as shipment_date,c.amount,c.id as details_id from wo_po_details_master a, wo_po_break_down b,AIR_WAY_BILL_ENTRY_DTLS c,AIR_WAY_BILL_ENTRY_MST d where a.job_no=b.job_no_mst and b.id=c.po_id and d.id=c.mst_id and a.company_name='$company_id' and d.BILL_SYSTEM_ID='$system_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.STATUS_ACTIVE=1 order by b.pub_shipment_date, a.id";
		 //echo $sql;
		$result=sql_select($sql);
		 $sql_prev_data= " select a.id, a.po_id, amount from AIR_WAY_BILL_ENTRY_DTLS a,AIR_WAY_BILL_ENTRY_MST b where a.mst_id=b.id and a.company_id='$company_id' and b.BILL_SYSTEM_ID='$system_id' and a.status_active=1 and a.is_deleted=0 ";
		
		$sql_prev_data_result=sql_select($sql_prev_data);
		foreach($sql_prev_data_result as $row)
		{
			$amnt_arr[$row[csf("po_id")]]=$row[csf("amount")];
			$prev_id_arr[$row[csf("po_id")]]=$row[csf("id")];
		}
        $i=1; $tot_po_qty=0; $tot_amount=0;
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_qty=$row[csf("total_set_qnty")]*$row[csf("po_quantity")];
			$tot_po_qty+=$po_qty; 
			$amount=$amnt_arr[$row[csf("po_id")]];
			if($amount>0) $amount=$amount;
			else $amount=$amnt_errision_val;
			
			$prev_dtlsid=$prev_id_arr[$row[csf("po_id")]];
			$tot_amount+=$amount;
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                <td width="80"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
              	<td width="100"><p><? echo $row[csf("po_number")]; ?></p>
                    <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo $i; ?>" value="<? echo $row[csf("po_id")]; ?>">
				
                </td>
                <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                <td width="120">
                    <p>
						<? 
							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf("gmts_item_id")]);
							foreach($gmts_item_id as $item_id)
							{
								$gmts_item.=$garments_item[$item_id].",";
							}
							$gmts_item=substr($gmts_item,0,-1); 
                        	echo $gmts_item; 
                        ?>
                    </p>
                </td>
                <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                <td width="80" align="right"><? echo $po_qty; ?></td>
                <td align="center">
                    <input type="text" name="txt_total_charge_usd[]" id="txt_total_charge_usd_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $amount; ?>" onkeyup="calculate_balance(<? echo $i; ?>);" title="<? echo $po_qty; ?>">
					<input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id_<? echo $i; ?>" value="<? echo $prev_dtlsid; ?>" />
                    
                </td>
            </tr>
			<?	
				$i++;
			}
			?>
			</tbody>
			</table>
		</div>
		<table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
			<tfoot>	 
			<th colspan="8">Total</th>
			<th align="right" width="80" id="td_tot_po_qnty"><? echo $tot_po_qty; ?></th>
			<th align="center" width="90" style="padding-right:20px;"><input type="text" name="tot_amount" id="tot_amount" style="width:70px;" class="text_boxes_numeric" value="<? echo $tot_amount; ?>" readonly="readonly"></th>	
			</tfoot>	
		</table>
	<?
		 
		exit();
}


if($action=="create_style_popup_list_view")
{
	list($company_id,$buyer_id,$job_no,$po_no,$date_from,$date_to,$style_status)=explode('_',$data);
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

	
	if($company_id!=0){
		$whereCon.=" and a.COMPANY_NAME=$company_id";
		$whereCon2.=" and a.COMPANY_ID=$company_id";
		}
	if($buyer_id!=0){
		$whereCon.=" and a.BUYER_NAME=$buyer_id";
		$whereCon2.=" and a.BUYER_NAME=$buyer_id";
		}
	if($job_no!=""){
		$whereCon.=" and a.JOB_NO like('%$job_no')";
		
		}
	if($po_no!=""){
		$whereCon.=" and b.PO_NUMBER like('%$po_no')";
		
		}
	
	if($date_from!='' && $date_to!='')
	{
		if($db_type==0)
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
		$whereCon .= " and b.PUB_SHIPMENT_DATE between '".$date_from."' and '".$date_to."'";
		$whereCon2 .= " and a.QUOT_DATE between '".$date_from."' and '".$date_to."'";
	}

	if($style_status==1)
	{
		$sql= "SELECT a.BUYER_NAME, a.COMPANY_ID as COMPANY_NAME,  a.STYLE_REF_NO as STYLE_REF_NO, sum(b.SUBMISSION_QTY) as PO_QNTY_PCS 
		from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_DTLS b where a.id=b.SAMPLE_MST_ID and a.STATUS_ACTIVE = 1 and a.IS_DELETED=0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED=0 $whereCon2
		group by a.BUYER_NAME, a.COMPANY_ID,  a.STYLE_REF_NO";
	}
	else
	{
		$sql= "SELECT a.JOB_NO, a.BUYER_NAME, a.COMPANY_NAME, a.STYLE_REF_NO, sum(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_PCS, b.id as PO_ID 
		from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id = b.job_id and a.STATUS_ACTIVE = 1 and a.IS_DELETED=0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED=0 $whereCon group by a.JOB_NO, a.BUYER_NAME, a.COMPANY_NAME, a.STYLE_REF_NO, b.ID order by b.id desc";
	}
	  //echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="540" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">Company</th>
                <th width="110">Buyer</th>
                <th width="110">Job No</th>
                <th>Style</th>
            </thead>
		</table>
		<div style="width:540px; max-height:270px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="540" class="rpt_table" id="tbl_list_search">
                <?
                $i=1; $pi_row_id="";
                $nameArray=sql_select( $sql );
                foreach ($nameArray as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
            		?>
                    <tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<?=$row["PO_ID"]."_".$row["JOB_NO"]."_".$row["STYLE_REF_NO"];?>')">
                        <td width="30" align="center"><?= $i; ?></td>
                        <td width="110"><p><?= $company_library[$row[COMPANY_NAME]]; ?></p></td>
                        <td width="110"><?= $buyer_arr[$row[BUYER_NAME]]; ?></td>
                        <td width="110"><p><?= $row[JOB_NO]; ?></p></td>
                        <td><p><?= $row[STYLE_REF_NO]; ?></p></td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
            </table>
        </div>
        
	</div>
	<?
	exit();
}


if ($action=="print_report")
{
echo "Print Design not found";	
exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_currier_name=str_replace("'","",$cbo_currier_name);
	$txt_air_way_bill=str_replace("'","",$txt_air_way_bill);
	$cbo_country_id=str_replace("'","",$cbo_country_id);
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_dealing_merchant=str_replace("'","",$cbo_dealing_merchant);
	$cbo_style_status=str_replace("'","",$cbo_style_status);
	$txt_style_name=str_replace("'","",$txt_style_name);
	$txt_style_qty=str_replace("'","",$txt_style_qty);
	$txt_bill_date=str_replace("'","",$txt_bill_date);
	$txt_weight=str_replace("'","",$txt_weight);
	$txt_charge_usd=str_replace("'","",$txt_charge_usd);
	$txt_dfs_charge_usd=str_replace("'","",$txt_dfs_charge_usd);
	$txt_total_charge_usd=str_replace("'","",$txt_total_charge_usd);
	$txt_charge_bdt=str_replace("'","",$txt_charge_bdt);
	$txt_ex_rate=str_replace("'","",$txt_ex_rate);
	$cbo_approve_status=str_replace("'","",$cbo_approve_status);
	$update_id=str_replace("'","",$update_id);
	$txt_system_id=str_replace("'","",$txt_system_id);

	 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if ($db_type==2) 
		{
			$txt_bill_date = date("d-M-Y", strtotime($txt_bill_date));
		}
		else{
			$txt_bill_date = date("Y-m-d", strtotime($txt_bill_date));
		}	


		$id=return_next_id("id", "air_way_bill_entry_mst", 1);

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";

		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', "AWB", date("Y",time()), 5, "select bill_prefix,bill_prefix_number from air_way_bill_entry_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "bill_prefix", "bill_prefix_number" ));

 		$field_array="id,bill_prefix,bill_prefix_number,bill_system_id,company_id,buyer_id,currier_name,air_way_bill,country_id,team_leader,dealing_merchant,style_status,style_name,style_qty,bill_date,weight,charge_usd,dfs_charge_usd,total_charge_usd,charge_bdt,rate,ready_to_approve,inserted_by,insert_date,status_active,is_deleted";

		$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_id.",".$cbo_buyer_id.",".$cbo_currier_name.",'".$txt_air_way_bill."',".$cbo_country_id.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_style_status.",'".$txt_style_name."',".$txt_style_qty.",'".$txt_bill_date."',".$txt_weight.",'".$txt_charge_usd."','".$txt_dfs_charge_usd."','".$txt_total_charge_usd."','".$txt_charge_bdt."','".$txt_ex_rate."','".$cbo_approve_status."','".$user_id."','".$pc_date_time."',"."1".","."0".")";

		$field_array_dtls="id,mst_id,company_id,po_id, job_no, amount, inserted_by, insert_date, is_deleted, status_active";
		$id_dtls=return_next_id("id", "AIR_WAY_BILL_ENTRY_DTLS",1);
		$data_array_dtls='';
		for($j=1;$j<=$tot_row;$j++)  
			{ 	
				$txt_amount="txt_amount".$j;
				//echo $txt_amount;
				$po_id="po_id".$j;
				$jobNo="jobNo".$j;
				if ($data_array_dtls!='') {$data_array_dtls .=",";}
				$data_array_dtls .="(".$id_dtls.",".$id.",".$cbo_company_id.",".$$po_id.",'".$$jobNo."',".$$txt_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_dtls++;
			}
		
		//echo "insert into AIR_WAY_BILL_ENTRY_DTLS (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("air_way_bill_entry_mst",$field_array,$data_array,1);
		$rID1=sql_insert("AIR_WAY_BILL_ENTRY_DTLS",$field_array_dtls,$data_array_dtls,1);
		// echo	 $rID."<br>";
		// echo "10**".$rID."##".$rID1;disconnect($con); die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'", '', $new_system_id[0])."**".$id."**".$id_dtls;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**"."0"."**".$new_system_id[0];
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $rID1)
			{
				oci_commit($con);  
				echo "0**".$new_system_id[0]."**".$id."**".$id_dtls;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_system_id[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();

 		$field_array_update="buyer_id*currier_name*air_way_bill*country_id*team_leader*dealing_merchant*style_status*style_name*style_qty*bill_date*weight*charge_usd*dfs_charge_usd*total_charge_usd*charge_bdt*rate*ready_to_approve*updated_by*update_date";   

		$data_array_update="".$cbo_buyer_id."*".$cbo_currier_name."*'".$txt_air_way_bill."'*".$cbo_country_id."*'".$cbo_team_leader."'*".$cbo_dealing_merchant."*".$cbo_style_status."*'".$txt_style_name."'*".$txt_style_qty."*'".$txt_bill_date."'*'".$txt_weight."'*'".$txt_charge_usd."'*'".$txt_dfs_charge_usd."'*'".$txt_total_charge_usd."'*'".$txt_charge_bdt."'*'".$txt_ex_rate."'*'".$cbo_approve_status."'*".$user_id."*'".$pc_date_time."'";
		
		//  echo  "10**".$update_id;disconnect($con);die;
		//$rID=sql_update("air_way_bill_entry_mst",$field_array_update,$data_array_update,"id","".$update_id."",1);

		$field_array_dtls="id,mst_id,company_id,po_id,job_no,amount,inserted_by, insert_date,status_active,is_deleted";
		$id_dtls=return_next_id("id", "AIR_WAY_BILL_ENTRY_DTLS",1);
		$data_array_dtls='';
		for($j=1;$j<=$tot_row;$j++)  
			{ 	
				$txt_amount="txt_amount".$j;
				//echo $txt_amount;
				$po_id="po_id".$j;
				$jobNo="jobNo".$j;
				if ($data_array_dtls!='') {$data_array_dtls .=",";}
				$data_array_dtls .="(".$id_dtls.",".$update_id.",".$cbo_company_id.",".$$po_id.",'".$$jobNo."',".$$txt_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls++;
			}
			// $rID2=execute_query("delete from AIR_WAY_BILL_ENTRY_DTLS where mst_id =".$update_id."",0);
			// $rID1=sql_insert("AIR_WAY_BILL_ENTRY_DTLS",$field_array_dtls,$data_array_dtls,1);
			//  echo"10**"."INSERT INTO AIR_WAY_BILL_ENTRY_DTLS (".$field_array_dtls.") VALUES ".$data_array_dtls; disconnect($con);die;
			// echo "10**".$rID."##".$rID1;disconnect($con); die;
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("air_way_bill_entry_mst",$field_array_update,$data_array_update,"id","".$update_id."",0);
			$rID1=sql_delete("AIR_WAY_BILL_ENTRY_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
			$rID2=sql_insert("AIR_WAY_BILL_ENTRY_DTLS",$field_array_dtls,$data_array_dtls,0);	
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 &&$rID2 )
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'", '', $id)."**".str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2) // Delete Here
	{
		// echo "20**"."Delete Restricted";die;

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$delete=execute_query( "delete from air_way_bill_entry_mst where id = $update_id",0);
		$delete_details=execute_query( "delete from AIR_WAY_BILL_ENTRY_DTLS where mst_id = $update_id",0);
		// $rID1=sql_delete("AIR_WAY_BILL_ENTRY_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
		echo  "**".$delete_details;

		if($db_type==0)
		{
			if($delete && $delete_details)
			{
				mysql_query("COMMIT");
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "7**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($delete && $delete_details)
			{
				oci_commit($con);
			if($rID && $rID1  )
				echo "2**".$rID."**". $rID1 ;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$rID."**". $rID1 ;
			}
		}

		disconnect($con);
	}
}


if ($action=="get_form_data")
{
		$sql= "SELECT ID,BILL_PREFIX,BILL_PREFIX_NUMBER,BILL_SYSTEM_ID,COMPANY_ID,BUYER_ID,CURRIER_NAME,AIR_WAY_BILL,COUNTRY_ID,TEAM_LEADER,DEALING_MERCHANT,STYLE_STATUS,STYLE_NAME,STYLE_QTY,BILL_DATE,WEIGHT,CHARGE_USD,TOTAL_CHARGE_USD,CHARGE_BDT,RATE,DFS_CHARGE_USD,READY_TO_APPROVE, IS_POSTED_ACCOUNT from air_way_bill_entry_mst where status_active = 1 AND is_deleted=0 and id='$data'";

	 //echo $sql;die;
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		
		
		echo "load_drop_down( 'requires/air_way_bill_entry_controller',".$row[COMPANY_ID].", 'load_buyer_dropdown', 'buyer_td' );\n";
		echo "load_drop_down( 'requires/air_way_bill_entry_controller', ".$row[TEAM_LEADER].", 'load_dealing_merchant_dropdown', 'marchant_td' );\n";
		
		echo "document.getElementById('update_id').value = '".$row[ID]."';\n";  
		echo "document.getElementById('txt_system_id').value = '".$row[BILL_SYSTEM_ID]."';\n";  
		echo "document.getElementById('cbo_company_id').value = '".$row[COMPANY_ID]."';\n";  
		echo "document.getElementById('cbo_buyer_id').value = '".$row[BUYER_ID]."';\n";  
		echo "document.getElementById('cbo_currier_name').value = '".$row[CURRIER_NAME]."';\n";  
		echo "document.getElementById('txt_air_way_bill').value = '".$row[AIR_WAY_BILL]."';\n";  
		echo "document.getElementById('cbo_country_id').value = '".$row[COUNTRY_ID]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[TEAM_LEADER]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[DEALING_MERCHANT]."';\n";  
		echo "document.getElementById('cbo_style_status').value = '".$row[STYLE_STATUS]."';\n";  
		echo "document.getElementById('txt_style_name').value = '".$row[STYLE_NAME]."';\n";  
		echo "document.getElementById('txt_style_qty').value = '".$row[STYLE_QTY]."';\n";  
		echo "document.getElementById('txt_weight').value = '".$row[WEIGHT]."';\n";  
		echo "document.getElementById('txt_charge_usd').value = '".$row[CHARGE_USD]."';\n"; 
		
		echo "document.getElementById('txt_dfs_charge_usd').value = '".$row[DFS_CHARGE_USD]."';\n"; 
		 
		echo "document.getElementById('txt_total_charge_usd').value = '".$row[TOTAL_CHARGE_USD]."';\n";  
		echo "document.getElementById('txt_charge_bdt').value = '".$row[CHARGE_BDT]."';\n";  
		echo "document.getElementById('txt_ex_rate').value = '".$row[RATE]."';\n"; 
		echo "document.getElementById('cbo_approve_status').value = '".$row[READY_TO_APPROVE]."';\n"; 
		echo "document.getElementById('txt_bill_date').value = '".change_date_format($row[BILL_DATE])."';\n"; 
		echo "document.getElementById('hidden_posted_in_account').value = '".$row["IS_POSTED_ACCOUNT"]."';\n";
		if($row["IS_POSTED_ACCOUNT"]==1)
		{
			echo "$('#is_posted_accounts').text('Already Posted In Accounts');\n";
		}
		else
		{
			echo "$('#is_posted_accounts').text('');\n";
		}
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_buyer_id').attr('disabled','true')".";\n";
		echo "$('#cbo_currier_name').attr('disabled','true')".";\n";
		echo "$('#cbo_team_leader').attr('disabled','true')".";\n";
		echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
		
		
		
	}
	
	exit();
}

?>


 