<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


if($action=="populate_details_data")
{
	$data=explode("**", $data);
	$mst_id=$data[0];
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	//$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	

	//$sql="SELECT b.id,b.mst_id,b.booking_no,b.fabric_sales_order_no,b.program_no,b.program_date,b.fabric_desc,b.machine_dia,b.machine_gg,b.stitch_length,b.color_range,b.wo_qty,b.rate,b.amount,b.remark_text,b.buyer_id,b.style_ref_no,b.program_qnty,b.within_group,a.wo_no from knitting_work_order_mst a, knitting_work_order_dtls b where a.id=b.mst_id and b.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id not in (select wo_dtls_id from wo_bill_dtls where status_active=1 and is_deleted=0)";
	$sql="SELECT a.id as knit_id, b.id,b.mst_id,b.booking_no,b.fabric_sales_order_no,b.program_no,b.program_date,b.fabric_desc,b.machine_dia,b.machine_gg,
	b.stitch_length,b.color_range,b.wo_qty,b.rate,b.amount,b.remark_text,b.buyer_id,b.style_ref_no,b.program_qnty,b.within_group,a.wo_no ,b.fab_pcs_qnty 
	from knitting_work_order_mst a, knitting_work_order_dtls b
	 where a.id=b.mst_id and b.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$result=sql_select($sql);
	foreach ($result as $row) 
	{
	$prog_no_arr[$row[csf('program_no')]]=$row[csf('program_no')];	
	}
	$prog_noCond=implode(",",$prog_no_arr);
	  $sql_recv=sql_select("select b.trans_id, b.prod_id, a.booking_id,b.body_part_id, b.febric_description_id, b.gsm, b.order_id, b.grey_receive_qnty,c.item_description as item_desc
	   from pro_grey_prod_entry_dtls b,inv_receive_master a,product_details_master c 
	where a.id=b.mst_id and c.id=b.prod_id and a.entry_form=2 and a.receive_basis=2 and a.knitting_source=3 AND c.ITEM_CATEGORY_ID = 13
	 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in($prog_noCond)");
	 
	 foreach ($sql_recv as $row) {
		 $Recv_qty_arr[$row[csf('booking_id')]][$row[csf('item_desc')]]+=$row[csf('grey_receive_qnty')];
	 }

	$cu_qty_sql=sql_select("SELECT b.wo_id, b.bill_qty,b.wo_dtls_id from wo_bill_mst a join wo_bill_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.wo_id in ($mst_id)");
	foreach ($cu_qty_sql as $data) {
		$cu_qty_arr[$data[csf('wo_dtls_id')]]+=$data[csf('bill_qty')];
	}
	$data="";
	foreach ($result as $row) {
		if($data!="") $data.="**";
		$buyer='';
		if($row[csf('within_group')]==1)
		{
			$buyer=$comp[$row[csf('buyer_id')]];
		}else{
			$buyer=$buyer_arr[$row[csf('buyer_id')]];
		}
		$Recv_qty=$Recv_qty_arr[$row[csf('program_no')]][$row[csf('fabric_desc')]];
		if($Recv_qty=='') $Recv_qty=0;
		//echo $row[csf('program_no')].'=A'.$row[csf('fabric_desc')];
		$balance_qty=$row[csf('wo_qty')]-$cu_qty_arr[$row[csf('id')]];
		$data.=$row[csf('buyer_id')]."__".$buyer."__".$row[csf('style_ref_no')]."__".$row[csf('booking_no')]."__".$row[csf('fabric_sales_order_no')]."__".$row[csf('wo_no')]."__".$row[csf('fabric_desc')]."__".$row[csf('program_no')]."__".$row[csf('program_date')]."__".change_date_format($row[csf('program_date')])."__".$row[csf('machine_dia')]."__".$row[csf('machine_gg')]."__".$row[csf('stitch_length')]."__".$row[csf('color_range')]."__".$color_range[$row[csf('color_range')]]."__".$balance_qty."__".$row[csf('program_qnty')]."__".$row[csf('within_group')]."__".$row[csf('id')]."__".$row[csf('mst_id')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('fab_pcs_qnty')]."__".$Recv_qty;
	}
	echo $data;
	exit();
	
	
}
if($action=="populate_details_data_save")
{
	$data=explode("**", $data);
	$wo_bill_id=$data[0];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	$sql="select id,mst_id,wo_dtls_id,booking_no,fso_no,wo_id,wo_qty,bill_qty,rate,amount,entry_form from wo_bill_dtls where mst_id=$wo_bill_id and status_active=1 and entry_form=421 ";
	//echo $sql;die;
	$result=sql_select($sql);
	$data="";
	$knitting_work_order_dtls_ids='';
	foreach ($result as $row) {
		if(!empty($row[csf('wo_dtls_id')]))
		{
			$knitting_work_order_dtls_ids.=$row[csf('wo_dtls_id')].",";
		}
	}
	$knitting_work_order_dtls_ids=chop($knitting_work_order_dtls_ids,",");
	//echo $knitting_work_order_dtls_ids;die;

	$sql_knd="SELECT b.id,b.mst_id,b.booking_no,b.fabric_sales_order_no,b.program_no,b.program_date,b.fabric_desc,b.machine_dia,b.machine_gg,b.stitch_length,b.color_range,b.wo_qty,b.rate,b.amount,b.remark_text,b.buyer_id,b.style_ref_no,b.program_qnty,b.within_group,a.wo_no,b.fab_pcs_qnty from knitting_work_order_mst a, knitting_work_order_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id  in ($knitting_work_order_dtls_ids)";
	//echo $sql_knd;
	$wo_order_details=sql_select($sql_knd);
	$wo_details_data=array();
	foreach ($wo_order_details as $row) {
		$wo_details_data[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		//echo $wo_details_data[$row[csf('id')]]['buyer_id'];
		$wo_details_data[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		//echo $wo_details_data[$row[csf('id')]]['style_ref_no'];
		$wo_details_data[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		$wo_details_data[$row[csf('id')]]['fabric_sales_order_no']=$row[csf('fabric_sales_order_no')];
		$wo_details_data[$row[csf('id')]]['wo_no']=$row[csf('wo_no')];
		$wo_details_data[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$wo_details_data[$row[csf('id')]]['program_no']=$row[csf('program_no')];
		$wo_details_data[$row[csf('id')]]['program_date']=$row[csf('program_date')];
		$wo_details_data[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
		$wo_details_data[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
		$wo_details_data[$row[csf('id')]]['stitch_length']=$row[csf('stitch_length')];
		$wo_details_data[$row[csf('id')]]['color_range']=$row[csf('color_range')];
		$wo_details_data[$row[csf('id')]]['wo_qty']=$row[csf('wo_qty')];
		$wo_details_data[$row[csf('id')]]['program_qnty']=$row[csf('program_qnty')];
		$wo_details_data[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$wo_details_data[$row[csf('id')]]['id']=$row[csf('id')];
		$wo_details_data[$row[csf('id')]]['mst_id']=$row[csf('mst_id')];
		$wo_details_data[$row[csf('id')]]['rate']=$row[csf('rate')];
		$wo_details_data[$row[csf('id')]]['amount']=$row[csf('amount')];
		$wo_details_data[$row[csf('id')]]['fab_pcs_qnty']=$row[csf('fab_pcs_qnty')];
		$wo_id_arr[$row[csf('mst_id')]] = $row[csf('mst_id')];
		$prog_arr[$row[csf('program_no')]] = $row[csf('program_no')];
	}
	//echo "<pre>";
	//print_r($wo_details_data);
	//echo "</pre>";
	$mst_id_str=implode(", ", $wo_id_arr);
	$cu_qty_sql=sql_select("SELECT b.wo_id, b.bill_qty from wo_bill_mst a join wo_bill_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id<>$wo_bill_id and b.wo_id in ($mst_id_str)");
	foreach ($cu_qty_sql as $data) {
		$cu_qty_arr[$data[csf('wo_id')]]+=$data[csf('bill_qty')];
	}
	$prog_noCond=implode(",",$prog_arr);
	  $sql_recv=sql_select("select b.trans_id, b.prod_id, a.booking_id,b.body_part_id, b.febric_description_id, b.gsm, b.order_id, b.grey_receive_qnty,c.item_description as item_desc
	   from pro_grey_prod_entry_dtls b,inv_receive_master a ,product_details_master c
	where a.id=b.mst_id and c.id=b.prod_id and a.entry_form=2 and a.receive_basis=2 and a.knitting_source=3
	 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in($prog_noCond)");
	  
	  
	 foreach ($sql_recv as $row) {
		 $Recv_qty_arr[$row[csf('booking_id')]][$row[csf('item_desc')]]+=$row[csf('grey_receive_qnty')];
	 }

	$data="";
	foreach ($result as $row) {
		if($data!="") $data.="**";
		$buyer='';
		$Recv_qty=$Recv_qty_arr[$wo_details_data[$row[csf('wo_dtls_id')]]['program_no']][$wo_details_data[$row[csf('wo_dtls_id')]]['fabric_desc']];
		if($Recv_qty=='') $Recv_qty=0;
		// if($wo_details_data[$row[csf('wo_dtls_id')]]['within_group']==1)
		// {
		// 	$buyer=$comp[$wo_details_data[$row[csf('wo_dtls_id')]]['buyer_id']];
		// }else{
		// 	$buyer=$buyer_arr[$wo_details_data[$row[csf('wo_dtls_id')]]['buyer_id']];
		// }
		//$recvQty=999;
		
		$buyer=$buyer_arr[$wo_details_data[$row[csf('wo_dtls_id')]]['buyer_id']];
		$balance_qty=$wo_details_data[$row[csf('wo_dtls_id')]]['wo_qty']-$cu_qty_arr[$row[csf('wo_id')]];
		$data.=$wo_details_data[$row[csf('wo_dtls_id')]]['buyer_id']."__".$buyer."__".$wo_details_data[$row[csf('wo_dtls_id')]]['style_ref_no']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['booking_no']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['fabric_sales_order_no']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['wo_no']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['fabric_desc']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['program_no']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['program_date']."__".change_date_format($wo_details_data[$row[csf('wo_dtls_id')]]['program_date'])."__".$wo_details_data[$row[csf('wo_dtls_id')]]['machine_dia']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['machine_gg']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['stitch_length']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['color_range']."__".$color_range[$wo_details_data[$row[csf('wo_dtls_id')]]['color_range']]."__".$wo_details_data[$row[csf('wo_dtls_id')]]['wo_qty']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['program_qnty']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['within_group']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['id']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['mst_id']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['rate']."__".$wo_details_data[$row[csf('wo_dtls_id')]]['amount'].'&&&&'.$row[csf('id')].'__'.$row[csf('wo_qty')].'__'.$row[csf('bill_qty')].'__'.$row[csf('rate')].'__'.$row[csf('amount')].'__'.$balance_qty."__".$wo_details_data[$row[csf('wo_dtls_id')]]['fab_pcs_qnty']."__".$Recv_qty;
	}
	echo $data;
	exit();
	
	
}

if($action=="work_order_popup")
{
	echo load_html_head_contents("Knitting Bill WO","../../../", 1, 1, $unicode); 
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(work_order_no,is_posted_in_account)
		{
			document.getElementById('selected_work_order').value=work_order_no;
			$("#hidden_posted_in_account").val(is_posted_in_account);
			parent.emailwindow.hide();
		}
    </script>
    </script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<input type="hidden" id="hidden_posted_in_account" value=""/>
				<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th>Company Name</th>
			                <th>Supplier</th>
			                <th>Search By</th>
			                <th id="search_by_td_up" width="170">Please Enter WO No</th>
			                <th colspan="2">Bill Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> <input type="hidden" id="selected_work_order">
			                    <?
			                    if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "",$on);
			                    ?>
			                </td>
			               
			                <td>
			                	
			                	<?

			                		if($supplier_id!="" && $supplier_id!=0){
										$on=1;
									}else{
										$on=0;
									}
		                            echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type =  '20' or party_type like '20,%' or party_type like '%,20' or party_type like'%,20,%' order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $supplier_id, "",$on );
		                        ?> 
			                </td>
			                 <th align="center">
								<?
								$search_by_arr = array(1=>"WO No",2 => "FSO No", 3 => "Fabric Booking No",4=>"Program No");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							 
			               <input type="hidden" name="po_breakdown_id" id="po_breakdown_id" value="<? echo $po_breakdown_id;?>">
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'wo_data_search_list_view', 'search_div', 'knitting_bill_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
			            </tr>
			            <tr>
			                <th align="center" valign="middle" colspan="8"><? echo load_month_buttons(1); ?> </th>
			            </tr>
			        </tbody>
			    </table>
    			<div id="search_div"> </div>
    		</form>
   		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();

}
if ($action=="wo_data_search_list_view")
{
	$data=explode('_',$data);

	
	if ($data[3] != "") {
		$wo_po_cond=" and d.po_breakdown_id in ($data[6])";
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	$supplier_condition="";
	if($data[1]!="" && $data[1]!=0){
		$supplier_condition=" and a.supplier_id ='$data[1]' ";
	}
	
	$search_string=$data[3];
	$search_field_cond="";
	if ($data[3] != "") {
		if($data[2]==1)
		{
			$search_field_cond = " and LOWER(a.prefix_no_num) like LOWER('%" . $search_string . "%')";	
		}
		else if ($data[2] == 2) {
			$search_field_cond = " and LOWER(b.fso_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[2] == 3)
		{
			$search_field_cond = " and LOWER(b.booking_no) like LOWER('%" . $search_string . "%')";
		}
		else{
			$search_field_cond = " and LOWER(b.program_no) like LOWER('%" . $search_string . "%')";
		}
	}
	

	$bill_date="";
	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $bill_date  = "and a.bill_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $bill_date ="";
	}

	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $bill_date  = "and a.bill_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $bill_date ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	$fabric_sale = sql_select("select sales_booking_no,job_no,style_ref_no,booking_id,buyer_id from fabric_sales_order_mst where id in ($data[6])");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	
	
    $sql= "select a.id, a.prefix_no, a.prefix_no_num, a.bill_no, a.company_id, a.bill_date, a.pay_mode, a.supplier_id, a.wo_no, a.wo_id,
a.manual_bill_no, a.remarks, a.tot_wo_qty, a.tot_bill_qty, a.tot_bill_amt, a.upchage, a.discount,a.is_posted_account
     from wo_bill_mst a, wo_bill_dtls b
     where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and a.entry_form=421 and b.entry_form=421   $bill_date $supplier_condition  $company $search_field_cond
     group by a.id, a.prefix_no, a.prefix_no_num, a.bill_no, a.company_id, a.bill_date, a.pay_mode, a.supplier_id, a.wo_no, a.wo_id,
a.manual_bill_no, a.remarks, a.tot_wo_qty, a.tot_bill_qty, a.tot_bill_amt, a.upchage, a.discount,a.is_posted_account
     order by a.prefix_no_num
		";
	//echo $sql;die;
	$result = sql_select($sql);
	
	?>
	
	<table class="rpt_table"  rules="all" width="780" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="140">Company name</th>
                <th width="120">Source</th>
                <th width="140">Knitting company</th>
                <th width="70">Bill Date</th>
                <th width="110">Bill No</th>
                <th>Bill Qty.</th>
            </tr>
        </thead>
    </table>
	<table id="list_view" class="rpt_table"  rules="all" width="780" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			
			$total=0;

			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$supplier=$supllier_arr[$row[csf('supplier_id')]];
				
				$data=$row[csf('id')]."__".$row[csf('bill_no')]."__".$row[csf('company_id')]."__".change_date_format($row[csf('bill_date')])."__".$row[csf('pay_mode')]."__".$row[csf('supplier_id')]."__".$row[csf('wo_no')]."__".$row[csf('wo_id')]."__".$row[csf('manual_bill_no')]."__".$row[csf('remarks')]."__".$row[csf('tot_wo_qty')]."__".$row[csf('tot_bill_qty')]."__".$row[csf('tot_bill_amt')]."__".$row[csf('upchage')]."__".$row[csf('discount')];

				$total+=$row[csf('tot_bill_qty')];

				$is_posted_account=$row[csf('is_posted_account')];
				
				
            ?>
            <tr onClick="js_set_value('<? echo $data; ?>','<? echo $is_posted_account; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td width="35"><? echo $i; ?>
                	
                </td>
                
                
                <td width="140" ><p><? echo $comp[$row[csf("company_id")]];  ?></p>
                	
                </td>
                <td width="120" style="word-break:break-all"><? echo $knitting_source[3]; ?></td>
                <td width="140" ><?php echo $supplier; ?></td>
                <td width="70" ><p><? echo change_date_format($row[csf('bill_date')]); ?></td>
                <td  width="110" ><p><? echo $row[csf('bill_no')]; ?></p></td>
                <td > <p><? echo number_format($row[csf('tot_bill_qty')],2); ?></p></td>
            </tr>
            <?
			}
			?>
        </tbody>
        <tfoot>
        	<tr>
        		<td colspan="6"></td>
        		<td><?php if(count($row)){ echo number_format($total,2);} ?></td>
        	</tr>
        </tfoot>
    </table>
    </div>
    
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if ($action=="issue_no_pop")
{
  	echo load_html_head_contents("Knitting Bill","../../../", 1, 1, $unicode);
  	//print_r($_REQUEST);die;
  	extract($_REQUEST);
	?>
	
    <script>

		var selected_id = new Array;
		
		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			//tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			var str_data=$('#hidden_data_id_' + str).val();
			var str_all=str_data.split("__");

			if ( document.getElementById('hidd_pay_mode').value!="" && document.getElementById('hidd_pay_mode').value!=str_all[1] )
			{
				alert('Pay Mode Mixing Not Allowed')
				return;
			}
			//toggle( tr_id, '#FFFFCC');
			document.getElementById('hidd_pay_mode').value=str_all[1];
			
			if ( document.getElementById('hidd_supplier').value!="" && document.getElementById('hidd_supplier').value!=str_all[2] )
			{
				alert('Supplier Mixing Not Allowed')
				return;
			}
			//toggle( tr_id, '#FFFFCC');
			document.getElementById('hidd_supplier').value=str_all[2];
			
			if ( document.getElementById('hidd_currency').value!="" && document.getElementById('hidd_currency').value!=str_all[3] )
			{
				alert('Currency Mixing Not Allowed')
				return;
			}
			
			document.getElementById('hidd_currency').value=str_all[3];

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray($('#hidden_data_id_' + str).val(), selected_id) == -1) {

				

				selected_id.push($('#hidden_data_id_' + str).val());
				

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#hidden_data_id_' + str).val()) break;
				}
				selected_id.splice(i, 1);
				
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				if(id!='') id+='***';
				id += selected_id[i];
			}

			
			

			$('#selected_work_order').val(id);
			
		}

	</script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th>Company Name</th>
			                <th>Supplier</th>
			                <th>Search By</th>
			                <th id="search_by_td_up" width="170">Please Enter FSO No</th>
			                <th colspan="2">Wo Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> <input type="hidden" id="selected_work_order">
			                	<input type="hidden" id="hidd_wo_id">
                            	<input type="hidden" id="hidd_wo_no">
                                <input type="hidden" id="hidd_pay_mode">
                                <input type="hidden" id="hidd_supplier">
                                <input type="hidden" id="hidd_currency">
			                    <?
			                    if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "",$on);
			                    ?>
			                </td>
			               
			                <td>
			                	
			                	<?

			                		if($supplier_id!="" && $supplier_id!=0){
										$on=1;
									}else{
										$on=0;
									}
		                            echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type =  '20' or party_type like '20,%' or party_type like '%,20' or party_type like'%,20,%' order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $supplier_id, "",$on );
		                        ?> 
			                </td>
			                 <th align="center">
								<?
								$search_by_arr = array(1=>"WO No",2 => "FSO No", 3 => "Fabric Booking No",4=>"Style Ref",5=>"Program No");
								$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							 
			               <input type="hidden" name="po_breakdown_id" id="po_breakdown_id" value="<? echo $po_breakdown_id;?>">
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'data_search_list_view', 'search_div', 'knitting_bill_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
			            </tr>
			            <tr>
			                <th align="center" valign="middle" colspan="8"><? echo load_month_buttons(1); ?> </th>
			            </tr>
			        </tbody>
			    </table>
    			<div id="search_div"> </div>
    		</form>
   		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
	exit();
}


if ($action=="data_search_list_view")
{
	$data=explode('_',$data);

	
	if ($data[3] != "") {
		$wo_po_cond=" and d.po_breakdown_id in ($data[6])";
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	$supplier_condition="";
	if($data[1]!="" && $data[1]!=0){
		$supplier_condition=" and a.supplier_id ='$data[1]' ";
	}
	
	$search_string=$data[3];
	$search_field_cond="";
	if ($data[3] != "") {
		if($data[2]==1)
		{
			$search_field_cond = " and LOWER(a.wo_number_prefix_num) like LOWER('%" . $search_string . "%')";	
		}
		else if ($data[2] == 2) {
			$search_field_cond = " and LOWER(b.fabric_sales_order_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[2] == 3)
		{
			$search_field_cond = " and LOWER(b.booking_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[2] == 4)
		{
			$search_field_cond = " and LOWER(b.style_ref_no) like LOWER('%" . $search_string . "%')";
		
		}
		else{
			$search_field_cond = " and LOWER(b.program_no) like LOWER('%" . $search_string . "%')";
		}
	}
	
	
	$booking_date="";
	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}

	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	$fabric_sale = sql_select("select sales_booking_no,job_no,style_ref_no,booking_id,buyer_id from fabric_sales_order_mst where id in ($data[6])");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	

    //$sql= "SELECT a.id,a.company_id,a.currency_id,a.supplier_id,a.wo_no,a.booking_date,sum(b.wo_qty) as wo_qty,a.pay_mode from knitting_work_order_mst a, knitting_work_order_dtls b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and b.id not in(select wo_dtls_id from wo_bill_dtls where status_active=1 and is_deleted=0 and entry_form=421) $booking_date $supplier_condition  $company $search_field_cond group by a.id,a.company_id,a.supplier_id,a.wo_no,a.booking_date,a.pay_mode,a.currency_id order by a.id,a.wo_no";

    $sql= "SELECT a.id,a.company_id,a.currency_id,a.supplier_id,a.wo_no,a.booking_date,sum(b.wo_qty) as wo_qty,a.pay_mode from knitting_work_order_mst a, knitting_work_order_dtls b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 $booking_date $supplier_condition  $company $search_field_cond group by a.id,a.company_id,a.supplier_id,a.wo_no,a.booking_date,a.pay_mode,a.currency_id order by a.id,a.wo_no ";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$mst_id_arr[$row[csf('id')]]=$row[csf('id')];
	}
	$mst_id_str=implode(", ", $mst_id_arr);
	$cu_qty_sql=sql_select("SELECT b.wo_id, b.bill_qty from wo_bill_mst a join wo_bill_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.wo_id in in ($mst_id_str)");
	foreach ($cu_qty_sql as $data) {
		$cu_qty_arr[$row[csf('wo_id')]]+=$row[csf('bill_qty')];
	}
	
	?>
	<script type="text/javascript">
    	
    	$(document).ready(function(e) {
			
			set_all();
		});
    </script>
	<table class="rpt_table"  rules="all" width="780" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="140">Company Name</th>
                <th width="120">Source</th>
                <th width="140">Knitting Company</th>
                <th width="70">WO Date</th>
                <th width="110">WO No</th>
                <th>WO Qty.</th>
            </tr>
        </thead>
    </table>
	<table id="list_view" class="rpt_table"  rules="all" width="780" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			
			$total=0;

			foreach($result as $row )
			{
				$cut_qty=$cu_qty_arr[$row[csf('id')]];
				$req_qty=$row[csf('wo_qty')];
				if($req_qty>$cut_qty)
				{
					$i++;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$supplier=$supllier_arr[$row[csf('supplier_id')]];
					$data=$row[csf('id')]."__".$row[csf('pay_mode')]."__".$row[csf('supplier_id')]."__".$row[csf('currency_id')];
					$total+=$row[csf('wo_qty')];				
					
		            ?>
		            <tr onClick="js_set_value('<? echo $i; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
		                <td width="35"><? echo $i; ?>
		                	<input type="hidden" name="hidden_data" id="hidden_data_id_<?php echo $i ?>"
							value="<? echo $data; ?>"/>
		                </td>                
		                <td width="140" ><p><? echo $comp[$row[csf("company_id")]];  ?></p></td>
		                <td width="120" style="word-break:break-all"><? echo $knitting_source[3]; ?></td>
		                <td width="140" ><?php echo $supplier; ?></td>
		                <td width="70" ><p><? echo change_date_format($row[csf('booking_date')]); ?></td>
		                <td  width="110" ><p><? echo $row[csf('wo_no')]; ?></p></td>
		                <td > <p><? echo number_format($row[csf('wo_qty')],2); ?></p></td>
		            </tr>
		            <?
	        	}
			}
			?>
        </tbody>
        <tfoot>
        	<tr>
        		<td colspan="6"></td>
        		<td><?php if(count($row)){ echo number_format($total,2);} ?></td>
        	</tr>
        </tfoot>
    </table>
    <table width="800" cellspacing="0" cellpadding="0" style="border:none" align="center" id="ds">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
    </div>
    
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	$update_id=str_replace("'", "", $update_id);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$txt_booking_date=str_replace("'", "", $txt_booking_date);
	$cbo_pay_mode=str_replace("'", "", $cbo_pay_mode);
	$cbo_supplier_name=str_replace("'", "", $cbo_supplier_name);
	$txt_manual_bill=str_replace("'", "", $txt_manual_bill);
	$txt_remark=str_replace("'", "", $txt_remark);
	$totalWoQnty=str_replace("'", "", $totalWoQnty);
	$totalBillQnty=str_replace("'", "", $totalBillQnty);
	$totalamount=str_replace("'", "", $totalamount);
	$upcharge=str_replace("'", "", $upcharge);
	$discount=str_replace("'", "", $discount);
	$grand_total=str_replace("'", "", $grand_total);
	$total_amount_update=str_replace("'", "", $total_amount_update);
	$total_bill_qnty_update=str_replace("'", "", $total_bill_qnty_update);
	$total_wo_qnty_update=str_replace("'", "", $total_wo_qnty_update);

	/**
	|--------------------------------------------------------------------------
	| is_posted_account checking
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation==1 || $operation==2) 
	{
		$wo_data=sql_select("select IS_POSTED_ACCOUNT from wo_bill_mst where id=$update_id");
		$is_posted_account=$wo_data[0]["IS_POSTED_ACCOUNT"]*1;
		
		if($is_posted_account>0)
		{
			echo "20**Update Restricted. Data already posted in Accounting.";
			oci_rollback($con);
			disconnect($con);
			die;
		}
	}


	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}

		
			

		
		$id = return_next_id_by_sequence("wo_bill_mst_seq", "wo_bill_mst", $con);
		$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'KWB', date("Y",time()), 5, "select id, prefix_no, prefix_no_num from wo_bill_mst where company_id=$cbo_company_name and entry_form=421 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num"));


		
		if($db_type==0)
			{
				if ($txt_booking_date!="") $txt_booking_date  = change_date_format($txt_booking_date, "yyyy-mm-dd", "-");
			}

			if($db_type==2)
			{
				if ($txt_booking_date!="") $txt_booking_date  = change_date_format($txt_booking_date, "yyyy-mm-dd", "-",1);
			}
			
			$field_array = "id,prefix_no,prefix_no_num,bill_no,company_id,bill_date,pay_mode,supplier_id,manual_bill_no, remarks, entry_form, tot_wo_qty, tot_bill_qty,tot_bill_amt,upchage,discount,inserted_by, insert_date";
		
		$data_array = "(" . $id . ",'" . $new_wo_number[1] . "','" . $new_wo_number[2] . "','" . $new_wo_number[0] . "','" . $cbo_company_name . "','" . $txt_booking_date . "','" . $cbo_pay_mode . "','" . $cbo_supplier_name . "','"  . $txt_manual_bill . "','" . $txt_remark . "', 421,'".$totalWoQnty."','".$totalBillQnty."','".$totalamount."','".$upcharge."','".$discount."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		
		$data_array_dtls="";
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			
			$detailsId="detailsId_".$j;
			$wodtlsId="wodtlsId_".$j;
			$buyerid="buyerid_".$j;
			$fabricsaleorderno="fabricsaleorderno_".$j;
			$bookingno="bookingno_".$j;
			$woid="woid_".$j;
			$woqty="woqty_".$j;
			$billqty="billqty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$checkid="checkid_".$j;
			if($$billqty>$$woqty)
			{
				echo "billover**";
				exit();
			}
			
			$dtls_id = return_next_id_by_sequence("wo_bill_dtls_seq", "wo_bill_dtls", $con);
		
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$wodtlsId . "','" . $$bookingno . "','" . $$fabricsaleorderno . "','" . $$woid . "','" . $$woqty. "','" . $$billqty .  "','".$$rate. "','" . $$amount .  "',421,'". $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
			
		}
		$dtls_field_array = "id,mst_id,wo_dtls_id,booking_no,fso_no,wo_id,wo_qty,bill_qty,rate,amount,entry_form,inserted_by,insert_date,is_deleted";

			//echo "10**"."<pre>insert into wo_bill_mst (".$field_array.") values ".$data_array."</pre>";
			//echo "<pre>insert into wo_bill_dtls(".$dtls_field_array.") values ".$data_array_dtls."<pre>";
			//die;

			//echo "10*insert into wo_bill_mst($field_array)values".$data_array;
			//echo "10*insert into wo_bill_dtls($dtls_field_array)values".$data_array_dtls;
			//die;

		    $rID=sql_insert("wo_bill_mst",$field_array,$data_array,0);
		    $rID1=sql_insert("wo_bill_dtls",$dtls_field_array,$data_array_dtls,0);
		    
		   
			

		if($db_type==0)
		{
			if($rID  && $rID1){
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_wo_number[0]."**".str_replace("'","",$upcharge)."**".str_replace("'","",$discount);;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id."**".$rID."**".$rID1;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $rID1){
				oci_commit($con);
				echo "0**".$id."**".$new_wo_number[0]."**".str_replace("'","",$upcharge)."**".str_replace("'","",$discount);;
			}
			else{
				oci_rollback($con);
				echo "10**".$id."**".$rID."**".$rID1;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		 $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array = "bill_date*pay_mode*supplier_id*manual_bill_no*remarks*tot_wo_qty*tot_bill_qty*tot_bill_amt*upchage*discount*updated_by*update_date";

		if($db_type==0)
			{
				if ($txt_booking_date!="") $txt_booking_date  = change_date_format($txt_booking_date, "yyyy-mm-dd", "-");
			}

			if($db_type==2)
			{
				if ($txt_booking_date!="") $txt_booking_date  = change_date_format($txt_booking_date, "yyyy-mm-dd", "-",1);
			}
		
		
		//echo "10**".$data_array;die;
		
		$bill_qty=0;
		$dataArrDtls=""; //$dataArrDtlsUp="";
		
		
		$dtlsArrField = "id, mst_id, buyer_id, wo_dtls_id, wo_id, bill_qty, rate, amount, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$dtlsArrFieldUp = "buyer_id*wo_dtls_id*bill_qty*rate*amount*updated_by*update_date*status_active*is_deleted";
		$rID1=true;$dataArrDtlsUp=array();
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			$detailsId="detailsId_".$j;
			$wodtlsId="wodtlsId_".$j;
			$buyerid="buyerid_".$j;
			$fabricsaleorderno="fabricsaleorderno_".$j;
			$bookingno="bookingno_".$j;
			$woid="woid_".$j;
			$woqty="woqty_".$j;
			$billqty="billqty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$checkid="checkid_".$j;
			$buyer_id=str_replace("'","",$$buyerid);
			$check_id=str_replace("'","",$$checkid);
			if($check_id==2)
			{
				if(str_replace("'","",$$detailsId)!="")
				{
					$id_arr[]=str_replace("'",'',$$detailsId);

					
					
					$dataArrDtlsUp[str_replace("'",'',$$detailsId)] =explode("*",("'".$buyer_id."'*'".$$wodtlsId."'*'".$$billqty."'*'".$$rate."'*'".$$amount."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));
					//print_r($dataArrDtlsUp).'=T';
				}
				
			}
			else
			{
				$wo_qty+=$$billqty;
				
				if(str_replace("'","",$$detailsId)!="")
				{
					$id_arr[]=str_replace("'",'',$$detailsId);
					

					$dataArrDtlsUp[str_replace("'",'',$$detailsId)] =explode("*",("'".$buyer_id."'*'".$$wodtlsId."'*'".$$billqty."'*'".$$rate."'*'".$$amount."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
					//print_r($dataArrDtlsUp).'=X';
				}
				else
				{
					$dtls_id = return_next_id_by_sequence("wo_bill_dtls_seq", "wo_bill_dtls", $con);
					
					if ($dataArrDtls != "") $dataArrDtls .= ",";
					$dataArrDtls .= "(" . $dtls_id . "," . $update_id . ",'" . $buyer_id . "','" . $wodtlsId . "','" . $$woid . "','" . $$billqty. "','" . $$rate. "','" . $$amount."',421,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
				}
			}
		}

		$data_array="'" . $txt_booking_date . "'*'" . $cbo_pay_mode . "'*'" . $cbo_supplier_name . "'*'" . $txt_manual_bill . "'*'" . $txt_remark."'*'".$total_wo_qnty_update."'*'".$total_bill_qnty_update."'*'".$total_amount_update."'*'".$upcharge."'*'".$discount . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		
		$flag=1;
		
		$rID=sql_update("wo_bill_mst",$field_array,$data_array,"id","".$update_id."",0);
		//echo "10**12**".sql_update("wo_bill_mst",$field_array,$data_array,"id",$update_id,0);die;
		if($rID==1) $flag=1; else $flag=0;
		// echo "10**J";die;
		// echo "10**".bulk_update_sql_statement("wo_bill_dtls", "id",$dtlsArrFieldUp, $dataArrDtlsUp, $id_arr ); die;
		
		if($dataArrDtlsUp!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("wo_bill_dtls", "id",$dtlsArrFieldUp,$dataArrDtlsUp,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}

		//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;;
		//echo "insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;die;
		if($dataArrDtls!=""){
			$rID3=sql_insert("wo_bill_dtls",$dtlsArrField,$dataArrDtls,0);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		// echo "10**".$rID.'--'.$rID1.'--'.$rID3.'--'.$flag; die;
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_bill_wo)."**".str_replace("'","",$upcharge)."**".str_replace("'","",$discount);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_bill_wo)."**".str_replace("'","",$upcharge)."**".str_replace("'","",$discount);
			}
			else{
				oci_rollback($con);
				echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$flag=1;
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_bill_mst",$field_array,$data_array,"id","".$update_id."",0);
		if($rID==1) $flag=1; else $flag=0;

		$sql=sql_select("select * from wo_bill_dtls where mst_id=$update_id and status_active=1");
		$rID2=1;
		if(count($sql)){
			$deleted_field_array="updated_by*update_date*status_active*is_deleted";
			$deleted_data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID2=sql_delete("wo_bill_dtls",$deleted_field_array,$deleted_data_array,"mst_id","".$update_id."",0);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($db_type==0)
		{
			if($rID && $rID2){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id)."**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id)."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "print_knitting_bill") 
{
	echo load_html_head_contents("Knitting W/O Bill", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$address="";
	$carrency_id=1;
?>
	<div style="margin-left:20px">
		<table width="100%" cellpadding="0" cellspacing="0" >
			<tr>
				<td width="180" align="right"> 
            	<img  src='../../<? echo $image_location; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><?=$company_library[$data[1]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px">
                        	<?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id='$data[1]' and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result)
								{
									?>
									Plot No: <? echo $result[csf('plot_no')]; ?>
									Level No: <? echo $result[csf('level_no')] ?>
									Road No: <? echo $result[csf('road_no')]; ?>
									Block No: <? echo $result[csf('block_no')]; ?>
									City Name: <? echo $result[csf('city')]; ?>
									Zip Code: <? echo $result[csf('zip_code')]; ?>
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];

									$address="Plot No: ".$result[csf('plot_no')]." Level No: ".$result[csf('level_no')]." Road No: ".$result[csf('road_no')]." Block No: ".$result[csf('block_no')]." City Name: ".$result[csf('city')]." Zip Code: ".$result[csf('zip_code')]." Province No: ".$result[csf('province')]." Country: ".$country_arr[$result[csf('country_id')]]." Email Address: ".$result[csf('email')]." Website No: ".$result[csf('website')];
								}
								?>
                        </td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><?=$data[2]; ?></strong></td>
                    </tr>
                </table>
            </td>
		</table>
		<?
		$sql_mst="select a.id, a.bill_no, a.prefix_no_num, a.bill_date, a.pay_mode, a.supplier_id, a.wo_no, a.wo_id, a.manual_bill_no, a.tot_wo_qty, a.tot_bill_qty, a.tot_bill_amt, a.upchage, a.discount, a.remarks from wo_bill_mst a where a.id='$data[0]' and a.entry_form=421 and a.is_deleted=0 and a.status_active=1";
		$dataArray=sql_select($sql_mst);


		$updataArr=array();
				$sqlDtls="select id, wo_dtls_id, bill_qty, rate, amount, remarks from wo_bill_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0";
				//echo $sqlDtls;die;
				$sqlDtlsRes=sql_select($sqlDtls);
				$wo_dtls_ids='';
				
				foreach($sqlDtlsRes as $row)
				{
					$updataArr[$row[csf('wo_dtls_id')]]=$row[csf('id')].'__'.$row[csf('bill_qty')].'__'.$row[csf('rate')].'__'.$row[csf('amount')].'__'.$row[csf('remarks')];
					$wo_dtls_ids.=$row[csf('wo_dtls_id')].",";
				}
				// unset($sqlDtlsRes); 
				// echo "<pre>";
				// print_r($updataArr);
				// echo "</pre>";
				
				$wo_dtls_ids=chop($wo_dtls_ids,",");
				$sql="SELECT a.id, b.buyer_id,b.within_group, b.style_ref_no, b.booking_no, b.fabric_sales_order_no, a.wo_no, a.currency_id, a.exchange_rate, a.pay_mode, b.id as dtls_id, b.fabric_desc,  b.color_range, b.machine_dia, b.machine_gg,b.stitch_length, b.wo_qty, b.rate, b.amount, b.remark_text,b.fab_pcs_qnty
				from knitting_work_order_mst a, knitting_work_order_dtls b 
				where a.id=b.mst_id and b.id in ($wo_dtls_ids) and a.status_active=1 and b.status_active=1 order by a.id ASC";
				//echo $sql;die;
				$result=sql_select($sql); 

				$currency_ids=array();
				$exchange_rates=array();
				foreach ($result as $row) {
					$currency_ids[]=$row[csf('currency_id')];
					$exchange_rates[]=$row[csf('exchange_rate')];
				}

				$exchange_rates=array_unique($exchange_rates);
				$currency_ids=array_unique($currency_ids);
		
		//echo $sql_mst;die;
		$womst_id=$dataArray[0][csf('wo_id')];
		//echo $womst_id;die;
		$address="";
		?>
		<table width="930" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><?=$dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"><?=change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Manual Bill No :</strong></td> <td width="175"><?=$dataArray[0][csf('manual_bill_no')]; ?></td>
            </tr>
            <tr>
				<?
                    if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5)
                    {
						$party_name=$company_library[$dataArray[0][csf('supplier_id')]];
                    }else{
                    	 $party_name=$supplier_arr[$dataArray[0][csf('supplier_id')]];
                    }
                   
                    $party_add=$dataArray[0][csf('supplier_id')];
                    $nameArray=sql_select( "select address_1 from lib_supplier where id=$party_add"); 
                   // echo "select address_1 from lib_supplier where id=$party_add";
                     
                    foreach ($nameArray as $row)
                    { 
                       
                       $address=$row[csf('address_1')];
                       break;
                    }
                   
                    
                ?>
                <td><strong>Supplier Name : </strong></td><td ><?=$party_name; ?></td>
                <td><strong>Currency : </strong></td><td><? if(count($currency_ids)==1)
                	{
                		echo $currency[$currency_ids[0]];
                		$carrency_id=$currency_ids[0];
                	} ?></td>
                <td><strong>Conversion Rate: </strong></td><td><? 

                	if(count($exchange_rates)==1)
                	{
                		echo $exchange_rates[0];
                	}
                 ?></td>
            </tr>
            <tr>
                
                <td ><strong>Address : </strong></td><td colspan="5"><?=$address; ?></td>
            </tr>
             <tr>
                
                <td ><strong>Remarks : </strong></td><td colspan="5"><?=$dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <table style="margin-top:10px;" width="1300" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="115">Buyer</th>
                <th width="90">Style Ref.</th>
                <th width="100">F.Booking No</th>
                <th width="100">FSO No</th>
                <th width="100">WO No</th>
                <th width="150">Fabric Description</th>
                <th width="100">M/C Dia x Gauge</th>
                <th width="80">S.L</th>
                <th width="70">Color Range</th>
                <th width="100">Fabric Qty(Pcs.)</th>
                <th width="70">WO Qty.</th>
                <th width="70">Bill Qty.</th>
                <th width="60">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody>
			<?
				$i=1;
				foreach($result as $row)
				{
					$dtls_id=$updataArr[$row[csf('dtls_id')]];
					//echo "<pre>".$row[csf('dtls_id')]."</pre>";
					if($dtls_id!="")
					{
						$ex_dtls_data=explode("__",$dtls_id);
						
						$billQty=$rate=$amount=$remarks="";
						
						$billQty=$ex_dtls_data[1];
						$rate=$ex_dtls_data[2];
						$amount=$ex_dtls_data[3];
						$remarks=$ex_dtls_data[4];
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$buyer='';
						
						// if($row[csf('within_group')]==1)
						// {
						// 	$buyer=$company_library[$row[csf('buyer_id')]];
						// }else{
						// 	$buyer=$buyer_arr[$row[csf('buyer_id')]];
						// }
						$buyer=$buyer_arr[$row[csf('buyer_id')]];
				
						$cons_comp=$row[csf('fabric_desc')];
					
					?>
                        <tr bgcolor="<?=$bgcolor; ?>" > 
                            <td align="center"><?=$i; ?></td>
                            <td style="word-break:break-all"><? echo $buyer; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('booking_no')]; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('fabric_sales_order_no')]; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('wo_no')]; ?></td>
                            
                            <td style="word-break:break-all"><?=$cons_comp; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('machine_dia')]." * ".$row[csf('machine_gg')]; ?></td>
                            
                            <td align="center"><?=$row[csf('stitch_length')]; ?></td>
                            <td style="word-break:break-all"><?=$color_range[$row[csf('color_range')]]; ?></td>
                            
                            <td align="right"><?=number_format($row[csf('fab_pcs_qnty')],2); ?></td>
                            <td align="right"><?=number_format($row[csf('wo_qty')],2); ?></td>
                            <td align="right"><?=number_format($billQty,2); ?></td>
                            <td align="right"><?=number_format($rate,2); ?></td>
                            <td align="right"><?=number_format($amount,2); ?></td>
                        </tr>
                        <?
						$i++;
						$totWoQty+=$row[csf('wo_qty')];
						$totBillQty+=$billQty;
						$totBillAmt+=$amount;
					}
				}
				?>
             </tbody>
             <tfoot>
				<tr > 
					<td align="right" colspan="11"><strong>Total:</strong></td>
					<td align="right"><?=number_format($totWoQty,2); ?>&nbsp;</td>
					<td align="right"><?=number_format($totBillQty,2); ?>&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right"><?=number_format($totBillAmt,2); ?></td>
				</tr>
                <tr > 
					<td align="right" colspan="14"><strong>Upcharge:</strong></td>
					<td align="right"><?=$dataArray[0][csf('upchage')]; ?></td>
				</tr>
                <tr > 
					<td align="right" colspan="14"><strong>Discount:</strong></td>
					<td align="right"><?=$dataArray[0][csf('discount')]; ?></td>
				</tr>
                <tr > 
					<td align="right" colspan="14"><strong>Grand Total:</strong></td>
                    <? $grandTot=($totBillAmt+$dataArray[0][csf('upchage')])-$dataArray[0][csf('discount')]; 
					
					$carrency_id=1;
					if($carrency_id==1){ $paysa_sent="Paisa"; } else if($carrency_id==2){ $paysa_sent="CENTS"; }
					$format_total_amount=number_format($grandTot,2,'.','');
					
					?>
					<td align="right"><?=number_format($grandTot,2); ?></td>
				</tr>
                
			   <tr>
				   <td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
			   </tr>
            </tfoot>
        </table>
		<? echo signature_table(199, $data[1], "1400px"); ?>
    </div>
    <?
    exit();
}



?>