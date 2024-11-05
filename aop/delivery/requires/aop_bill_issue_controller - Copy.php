<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();		 
}

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_party_source').value+'_'+this.value,'packing_delivery_list_view','packing_info_list','requires/subcon_packing_bill_issue_controller','');","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}
if ($action=="load_drop_down_party_name_popup")
{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	exit();
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$party_name=$data[2];
	$challan_no = ($data[3] !="") ? $data[3] : "0";
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >

        	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead> 
                        <tr>                 
                            <th width="150">Company Name</th>
                            <th width="150">Location</th>
                            <th width="80">Job Year</th>
                            <th width="110">Job No</th>
                            <th width="110">Order No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>           
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <input type="hidden" id="selected_job">
                                <? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1); ?>
                            </td>
                            <td><? echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $data[1], "",1,"","","","",3 ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td><input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Search Job" /></td> 
                            <td><input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Search Order" /></td> 
                            <td>
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+<? echo $party_name; ?>+'_'+<? echo $challan_no; ?>+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value,'create_job_search_list_view','search_div','aop_bill_issue_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>        
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$company_name=str_replace("'","",$data[0]);
	$location_name=str_replace("'","",$data[1]);
	$party_name=str_replace("'","",$data[2]);
	$challan_no=str_replace("'","",$data[3]);
	$year=str_replace("'","",$data[4]);
	$search_job=str_replace("'","",$data[5]);
	$search_order=trim(str_replace("'","",$data[6]));

	/*if($search_job=="" && $search_order=="")
	{
		echo "<p style='text-align:center;'>Please Give Job or Order.</p>"; die;
	}
	else
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job%'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.po_number like '%$search_order%'"; else $search_order_cond="";
	}*/

	$man_challan_cond="";
	if($challan_no==0) $man_challan_cond=""; else $man_challan_cond="and a.challan_no='$challan_no'";

	if($db_type==0) 
	{
		$booking_without_order="IFNULL(a.booking_without_order,0)";
		$date_sql="YEAR(a.insert_date) as year";
		$date_cond=" and YEAR(a.insert_date)=$year";
	}
	else if ($db_type==2)
	{
		$booking_without_order="nvl(a.booking_without_order,0)";
		$date_sql="TO_CHAR(a.insert_date,'YYYY') as year";
		$date_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year";
	}
	//$sql= "select c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no, $aopcolor_id_str as color_id, $buyer_po_id_str as buyer_po_id,d.batch_id,d.order_id,d.production_id,d.product_qnty from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d where a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and a.entry_form=278 $company $party_id_cond $withinGroup $search_com_cond $aop_cond $po_idsCond $batch_idsCond $qc_cond $withinGroup and c.entry_form=294 group by c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no ,d.batch_id,d.order_id,d.production_id,d.product_qnty";

	//$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, a.within_group, $batch_cond,$order_cond , $qc_cond, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=307 and a.status_active=1 $company_name $party_cond $within_group $po_id_cond  $production_date_cond $product_id_cond group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no, a.within_group order by a.id DESC";

	$sql= "select c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d where a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and a.entry_form=278 $company $party_id_cond $withinGroup $search_com_cond $aop_cond $po_idsCond $batch_idsCond $qc_cond $withinGroup and c.entry_form=307 group by c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no";

	$po_sql="(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond)
	union all
	(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.receive_basis in (1,2) and a.item_category=13 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond) 
	union all
	(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.mst_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form =58 and d.entry_form=58 and c.entry_form=58 and a.receive_basis=10 and a.item_category=13 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond)
	union all
	(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond)
	order by po_breakdown_id ASC";
	//echo $po_sql;

	foreach (sql_select($po_sql) as $value) 
	{
		$po_arr[$value[csf("po_breakdown_id")]]=$value[csf("po_breakdown_id")];
	}

	if(count($po_arr)>999)
	{
		if($db_type==0)
		{
			$po_conds="and b.id in (".trim(implode(',', array_filter($po_arr)),',').")";
		}
		else if($db_type==2) 
		{
			$chunked_arr = array_chunk(array_filter($po_arr), 999);
			$po_conds=" and (";
			foreach ($chunked_arr as $po) 
			{
				$po_conds .="b.id in (".implode(',', $po).") or ";
			}
			$po_conds=chop($po_conds, " or ");
			$po_conds .=")";
		}
	}
	else
	{
		$po_conds="and b.id in (".trim(implode(',', array_filter($po_arr)),',').")";
	}

	/*$sql="SELECT a.id as job_id, a.job_no, a.job_no_prefix_num, $date_sql, a.company_name, a.location_name, a.buyer_name as party_id, b.id, b.job_no_mst, b.po_number as order_no, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and b.status_active!=0 $po_conds $search_job_cond $search_order_cond $date_cond order by a.id DESC";*/
	$sql= "SELECT c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no,a.delivery_date from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d where a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and a.entry_form=278 $company $party_id_cond $withinGroup $search_com_cond $aop_cond $po_idsCond $batch_idsCond $qc_cond $withinGroup and c.entry_form=307 group by c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no,a.delivery_date";

	echo  create_list_view("list_view", "Job No,Order No,Delivery Date,AOP Ref.","130,120,70","550","350",0,$sql, "js_set_value","subcon_job,id","",1,"0,0,0,0",$arr,"subcon_job,order_no,delivery_date,aop_reference", "",'','0,0,0,0') ;
	exit();		 
} 

if ($action=="knitting_delivery_list_view")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	//echo $data;
	$data=explode('***',$data);
	
	$ex_bill_for=$data[4];
	$date_from=$data[5];
	$date_to=$data[6];
	$challan_no=$data[7];
	$sys_challan_no=$data[8];
	$update_id=$data[9];
	$str_data=$data[10];
	$job_id=$data[11];
	
	if($job_id)
	{
		$po_ids="";
		$po_sql="SELECT b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.id=$job_id and a.status_active!=0 and b.status_active!=0";
		foreach (sql_select($po_sql) as $value) 
		{
			$po_ids .= $value[csf("id")].",";
		}
		$po_ids=chop($po_ids, ",");
	}
	//echo $po_ids;

	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";
	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	//echo $data[3];
	//$delv_id=implode(',',explode('_',$data[8]));
	if($data[2]==2)
	{
		?>
		</head>
		<body>
			<div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="917px" class="rpt_table">
					<thead>
                    	<th width="40">&nbsp;</th>
						<th width="30">SL</th>
						<th width="70">Challan No</th>
						<th width="70">Delivery Date</th>
						<th width="110">Order No</th>                    
						<th width="180">Fabric Description</th>
						<th width="100">Delivery Qty</th>
						<th width="100">Delivery Pcs</th>
						<th width="100">Process</th>
						<th>Currency</th>
					</thead>
			 </table>
        </div>
        <div style="width:920px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900px" class="rpt_table" id="tbl_list_search">
            <?
				$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
				$item_body_part_id=return_library_array( "select id, body_part from lib_subcon_charge",'id','body_part');
				
				$delv_id=implode(',',explode('!!!!',$str_data));
				
				$order_array=array();
				$order_sql="Select b.id, b.order_no, b.order_uom, b.process_id, b.cust_buyer, b.cust_style_ref, b.rate, b.amount, a.currency_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach ($order_sql_result as $row)
				{
					$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
					$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
					$order_array[$row[csf("id")]]['cust_buyer']=$row[csf("cust_buyer")];
					$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
					$order_array[$row[csf("id")]]['rate']=$row[csf("rate")];
					$order_array[$row[csf("id")]]['amount']=$row[csf("amount")];
					$order_array[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
					$order_array[$row[csf("id")]]['process_id']=$row[csf("process_id")];
				}
				unset($order_sql_result);
				$rate_array=array();
				$rate_sql="select order_id, item_id, rate from subcon_ord_breakdown";
				$rate_sql_result =sql_select($rate_sql);
				foreach ($rate_sql_result as $row)
				{
					$rate_array[$row[csf("order_id")]][$row[csf("item_id")]]=$row[csf("rate")];
				}
				unset($rate_sql_result);
                $i=1;
				
				if($sys_challan_no!="") $sys_challan_cond=" and a.delivery_prefix_num in ($sys_challan_no)"; else $sys_challan_cond="";
				if(!$update_id)
				{
					$sql="select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, b.carton_roll as roll_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id='2' and a.status_active=1 and a.is_deleted=0 $sys_challan_cond"; 
				}
				else
				{
					$sql="(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, b.carton_roll as roll_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=2 and a.status_active=1 and b.bill_status=0 $sys_challan_cond)
					 union 
					 	(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, b.carton_roll as roll_qty, 1 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=2 and b.id in ( $delv_id ) and a.status_active=1 and b.bill_status=1) order by type DESC";
				}
				//echo $sql;
				$sql_result =sql_select($sql);
				
				foreach($sql_result as $row) // for update row
				{
					$process_id_val=$row[csf('process_id')]; $item_name="";
                    if($process_id_val==1 || $process_id_val==5) $item_name=$garments_item[$row[csf('item_id')]]; else $item_name=$item_id_arr[$row[csf('item_id')]];
					$all_value=$row[csf('id')];

					//checking coller & cuff subprocess is present or not
					if(in_array(3, explode(",", $order_array[$row[csf("order_id")]]['process_id']))==false) $subprocess_uom=0; else $subprocess_uom=1;

					$str_val=$row[csf('id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$order_array[$row[csf('order_id')]]['order_no'].'_'.$order_array[$row[csf('order_id')]]['cust_style_ref'].'_'.$order_array[$row[csf('order_id')]]['cust_buyer'].'_'.$row[csf('roll_qty')].'_0__'.$item_body_part_id[$row[csf('item_id')]].'_'.$body_part[$item_body_part_id[$row[csf('item_id')]]].'_'.$row[csf('item_id')].'_'.$item_name.'_'.$row[csf('delivery_qty')].'_0_0_'.$order_array[$row[csf('order_id')]]['order_uom'].'_'.$row[csf('delivery_pcs')].'_'.$subprocess_uom.'_'.$row[csf('collar_cuff')].'_1_'.$bodyPartTypeArr[$item_body_part_id[$row[csf('item_id')]]];
					//$order_array[$row[csf('order_id')]]['order_uom'].'_1_'.$subprocess_uom.'_'.$row[csf('collar_cuff')].'_'.$bodyPartTypeArr[$item_body_part_id[$row[csf('item_id')]]];
						
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$checked_val=2; $ischeck="";
					if ($row[csf('type')]==0) 
					{
						$row_color=$bgcolor; $checked_val=2; $ischeck="";
					}
					else 
					{
						$bgcolor="yellow"; $checked_val=1; $ischeck="checked";
					}
					?>
					<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."***".$order_array[$row[csf("order_id")]]['currency_id']; ?>');" >
						<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
						<td width="70"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td width="110" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="180" style="word-break:break-all"><? echo $item_name; ?></td>
						<td width="100" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td width="100" align="right"><? echo $row[csf('delivery_pcs')]; ?>&nbsp;</td>
						<td width="100" style="word-break:break-all"><? echo $production_process[$row[csf('process_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $currency[$order_array[$row[csf("order_id")]]['currency_id']]; ?>
						<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
						<input type="hidden" id="currid<? echo $row[csf('id')]; ?>" style="width:50px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>"></td>
					</tr>
					<?
					$i++;
				}
				?>
                </table>
         </div>
        <div>
            <table width="920">
                <tr style="border:none">
                    <td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/><b>Check all</b></td>
                    <td bgcolor="#FF80FF" align="center"><input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0);" /></td>
                </tr>
           </table>
      	</div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	}
	else if($data[2]==1)
	{
		if($ex_bill_for==3) $tbl_wight="820"; else $tbl_wight="1000";
		?>
		</head> 
		<body>
        <div id="list_view_body">
			<div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight; ?>px" class="rpt_table">
					<thead>
                    <?
					if($ex_bill_for==3)
					{
						?>
                    	<th width="30">&nbsp;</th>
						<th width="30">SL</th>
                        <th width="100">Buyer Name</th>
						<th width="60">Sys. Challan</th>
						<th width="70">Rec. Challan</th>
						<th width="70">Receive Date</th>
                        <th width="90">Body Part</th>
						<th width="160">Fabric Description</th>
                        <th width="60">Color Type</th>
						<th width="80">Receive Qty</th>
                        <th>Roll Qty</th>
                        <?
					}
					else
					{
						?>
                        <th width="30">&nbsp;</th>
						<th width="30">SL</th>
						<th width="60">Sys. Challan</th>
						<th width="70">Rec. Challan</th>
						<th width="60">Receive Date</th>
                        <th width="60">Job</th>
                        <th width="100">Style</th>                    
						<th width="100">Order No</th>
                        <th width="90">Body Part</th>
						<th width="160">Fabric Description</th>
                        <th width="60">Color Type</th>
						<th width="80">Receive Qty</th>
                        <th>Number of Roll</th>
                        <?
					}?>
					</thead>
			 </table>
        </div>
        <div style="width:<? echo $tbl_wight; ?>px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight-20; ?>px" class="rpt_table" id="tbl_list_search">
            <?
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				$product_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
				$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');
				//$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
				$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
				$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
				
				$bill_qty_array=array();$str_data="";
				$sql_bill="select mst_id,challan_no, order_id, febric_description_id, body_part_id, item_id, sum(packing_qnty) as roll_qty, sum(delivery_qty) as bill_qty from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by mst_id,challan_no, order_id, febric_description_id, body_part_id, item_id";
				$sql_bill_result =sql_select($sql_bill);
				
				foreach($sql_bill_result as $row)
				{
					$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty']=$row[csf('bill_qty')];
					$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['roll']=$row[csf('roll_qty')];
					if($row[csf('mst_id')]==$update_id)
					{
						 if($str_data=="") $str_data=$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')]; else $str_data.='!!!!'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
					}
				}
				unset($sql_bill_result);
				
				$color_type_array=array();
				$color_type_sql="select a.color_type_id, a.lib_yarn_count_deter_id, b.po_break_down_id from  wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and is_deleted=0 and a.company_id='$data[1]'";
				$color_type_sql_result =sql_select($color_type_sql);
				foreach($color_type_sql_result as $row)
				{
					$color_type_array[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]]['color_type']=$row[csf('color_type_id')];
				}
				unset($color_type_sql_result);
				
				$job_order_arr=array();
				$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0";
				$sql_job_result =sql_select($sql_job);
				foreach($sql_job_result as $row)
				{
					$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
					$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
				}
				unset($sql_job_result);
				
				$composition_arr=array();
				$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				$data_array=sql_select($sql_deter);
				if(count($data_array)>0)
				{
					foreach( $data_array as $row )
					{
						if(array_key_exists($row[csf('id')],$composition_arr))
						{
							$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
						else
						{
							$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
					}
				}
				$ex_str_data=explode("!!!!",$str_data);
				$str_arr=array();
				foreach($ex_str_data as $str)
				{
					$str_arr[]=$str;
				}
				
				if($db_type==0) 
				{
					$booking_without_order="IFNULL(a.booking_without_order,0)";
					$booking_without_order_roll="IFNULL(f.booking_without_order,0)";
				}
				else if ($db_type==2)
				{
					$booking_without_order="nvl(a.booking_without_order,0)";
					$booking_without_order_roll="nvl(f.booking_without_order,0)";
				}
				
				if($bill_for_id==0) $bill_for_id_cond=""; else $bill_for_id_cond="and d.booking_type='$bill_for_id'";
							
				$plan_booking_arr=array();
				$knit_booking="select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0";
				$knit_booking_result =sql_select($knit_booking);
				foreach($knit_booking_result as $row)
				{
					$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
				}
				unset($knit_booking_result);
				
				$roll_dlv_arr=array();
				$sql_dlv="select a.id, a.sys_number, b.barcode_num, c.receive_basis, c.booking_no, c.booking_id, c.buyer_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c where a.id=b.mst_id and b.grey_sys_id=c.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2 and b.status_active=1 and b.is_deleted=0";
				$sql_dlv_result =sql_select($sql_dlv);
				foreach($sql_dlv_result as $row)
				{
					/*$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['receive_basis']=$row[csf('receive_basis')];
					$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['booking_no']=$row[csf('booking_no')];
					$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['booking_id']=$row[csf('booking_id')];*/

					$roll_dlv_arr[$row[csf('sys_number')]]['receive_basis']=$row[csf('receive_basis')];
					$roll_dlv_arr[$row[csf('sys_number')]]['booking_no']=$row[csf('booking_no')];
					$roll_dlv_arr[$row[csf('sys_number')]]['booking_id']=$row[csf('booking_id')];
					$roll_dlv_arr[$row[csf('sys_number')]]['buyer_id']=$row[csf('buyer_id')];
				}
				unset($sql_dlv_result);
				
                $i=1;
				if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM";
				if($ex_bill_for!=3)
				{
					$man_challan_cond="";  $sys_challan_cond="";
					if($challan_no!="") $man_challan_cond="and a.challan_no='$challan_no'";
					if($sys_challan_no!="") $sys_challan_cond=" and a.recv_number_prefix_num in ($sys_challan_no)";

					$po_breakdown_id_conds="";
					if($job_id)
					{
						$po_breakdown_id_conds= " and c.po_breakdown_id in ($po_ids)";
						$date_cond="";
					}
					
					$sql="(select a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, d.receive_basis, d.booking_no, d.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d
						 where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0
						 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						 group by a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, d.receive_basis, d.booking_no, d.booking_id)
						 union all
						 (select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.receive_basis in (1,2) and a.item_category=13 and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id) 
						union all
						(select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, count(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(d.qnty) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, 0 booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d
						where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.mst_id 
						
						and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form =58 and d.entry_form=58 and c.entry_form=58 and a.receive_basis=10 and a.item_category=13 and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no)
						union all
						(select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id)
						
						order by recv_number_prefix_num ASC";
					//echo $sql;
					
					$sql_result=sql_select($sql);
				
					foreach($sql_result as $row) // for update row
					{
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						if(in_array($all_value,$str_arr))
						{
							$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
							if ($row[csf('entry_form')]==2)
							{
								if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
								if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
								if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
							}
							else if ($row[csf('entry_form')]==22)
							{
								if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
								if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
								if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; 
							}
							else if ($row[csf('entry_form')]==58)
							{
								$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id

								/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

								$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
								
								if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
								if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
								if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM";
							}
							
							$ex_booking="";
							if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
							
							$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0_____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]];
							if($independent==4)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
									<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
									<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
									<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
									<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></td>
									<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
									<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
									<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
									<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
									<td align="center"><? echo $row[csf('roll_qty')]; ?>
									
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
									<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
								</tr>
								<?php
								$i++;
							}
							else
							{
								if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
										<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
										<td width="30"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
										<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
										<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
										<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></td>
										<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
										<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
										<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
										<td align="center"><? echo $row[csf('roll_qty')]; ?>
										
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
										<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
									</tr>
									<?php
									$i++;
								}
							}
						}
					}
				
					foreach($sql_result as $row) // for new row
					{
						$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
						if ($row[csf('entry_form')]==2)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
						}
						else if ($row[csf('entry_form')]==22)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
							if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; 
						}
						else if ($row[csf('entry_form')]==58)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

							$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
							
							if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM";
						}
						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						//echo $row[csf('booking_no')];
						//if($ex_booking[1]!='Fb') echo $ex_booking[1];
						$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
						
						$avilable_qty=$row[csf('quantity')]-$bill_qty;
						$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]];
						if($independent==4)
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
									<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
									<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
									<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
									<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></td>
									<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
									<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
									<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
									<td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
									<td align="center"><? echo $row[csf('roll_qty')]; ?>
									
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
									<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
						else
						{
							if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
							{
								if($avilable_qty>0)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
										<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
										<td width="30"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
										<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
										<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
										<td width="100" style="word-break:break-all"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></td>
										<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
										<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
										<td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
										<td align="center"><? echo $row[csf('roll_qty')]; ?>
										
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
										<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
									</tr>
									<?php
									$i++;
								}
							}
						}
					}
				}
				else if($ex_bill_for==3)// sample without order
				{
					$sys_challan_cond="";
					if($sys_challan_no!="") $sys_challan_cond=" and a.recv_number_prefix_num in ($sys_challan_no)";
					$sql="(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, c.entry_form, c.receive_basis, c.booking_no, c.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c 
					where a.id=b.mst_id and c.id=a.booking_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and a.entry_form=22 and a.receive_basis=9 and a.item_category=13 and c.entry_form=2 and c.receive_basis in (0,1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.entry_form, c.receive_basis, c.booking_no, c.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$data[3]' and b.trans_id!=0 and a.knitting_company='$data[0]' and a.location_id='$data[1]' and a.entry_form=2 and a.item_category=13 and a.receive_basis in (0,1,2)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(f.qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details f
					where a.id=b.mst_id  and b.id=f.dtls_id and a.id=f.mst_id 
					and a.knitting_source=1 and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and b.trans_id!=0 and a.location_id='$data[1]' and a.entry_form =58 and a.receive_basis=10 and a.item_category=13
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and b.trans_id!=0 and a.location_id='$data[1]' and a.entry_form=22 and a.item_category=13 and a.receive_basis in (2,11)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					order by recv_number_prefix_num ASC";
					
					//echo $sql; die;
					$sql_result =sql_select($sql);
					foreach($sql_result as $row) // for update row
					{
						$row[csf('order_id')]=0;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						if(in_array($all_value,$str_arr))
						{
							$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
							if ($row[csf('entry_form')]==2)
							{
								if($row[csf('receive_basis')]==0) $independent=4;  //else $independent='';
								if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							}
							else if ($row[csf('entry_form')]==22)
							{
								if($row[csf('receive_basis')]==4) $independent=4; // else $independent='';
								if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							}
							
							else if ($row[csf('entry_form')]==58)
							{
								$rec_basis=0; $bookinNo=""; $bookingId=0;
	
								$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
								if($row[csf('buyer_id')]==0) $row[csf('buyer_id')]=$roll_dlv_arr[$row[csf('booking_no')]]['buyer_id'];
								
								if($rec_basis==0 ) $independent=4; 
								if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
							}
							
							$ex_booking="";
							if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
							
							$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
							
							$avilable_qty=$row[csf('quantity')]-$bill_qty;
							$avilable_roll=$row[csf('roll_qty')];//$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
							
							$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]];
							
							if($independent==4)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                                    <td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
                                    <td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
								</tr>
								<?php
								$i++;
							}
							else
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                                    <td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
                                    <td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
					}
					
					foreach($sql_result as $row) // for new row
					{
						$row[csf('order_id')]=0; $independent='';
						if ($row[csf('entry_form')]==2)
						{
							if($row[csf('receive_basis')]==0) $independent=4; //else $independent='';
						}
						else if ($row[csf('entry_form')]==22)
						{
							if($row[csf('receive_basis')]==4) $independent=4; // else $independent='';
						}
						else if ($row[csf('entry_form')]==58)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

							$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
							if($row[csf('buyer_id')]==0) $row[csf('buyer_id')]=$roll_dlv_arr[$row[csf('booking_no')]]['buyer_id'];
							
							if($rec_basis==0 ) $independent=4; //else $independent='';
							if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
						}
						$bill_for_sb="SMN";
						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						//echo $row[csf('booking_no')];
						//if($ex_booking[1]!='Fb') echo $ex_booking[1];
						$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
						
						$avilable_qty=$row[csf('quantity')]-$bill_qty;
						$avilable_roll=$row[csf('roll_qty')]-$roll_qty;//$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]];
						if($independent==4)
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" > 
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                                    <td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
                                    <td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
						else
						{
							if( strtolower($ex_booking[1])==strtolower($bill_for_sb) ) 
							{
								if($avilable_qty>0)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr id="tr_<?  echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" > 
										<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
										<td width="30" align="center"><? echo $i; ?></td>
										<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
										<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
										<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
										<td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
										<input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
									</tr>
									<?php
									$i++;
								}
							}
						}
					}
				}
				?>
            </table>
        </div>
        <div>
            <table width="1000">
                <tr style="border:none">
                	<td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/><b>Check all</b></td>
                    <td bgcolor="#FF80FF" align="center"><input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0);" /></td>
                </tr>
           </table>
      </div>
      </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?	
	}
	exit();
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="packingbill_1"  id="packingbill_1" autocomplete="off">
                <table  cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Party Name</th>
                        <th width="80">Bill ID</th>
                        <th width="170">Date Range</th>
                        <th>
                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" />
                        </th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> 
                                <input type="hidden" id="issue_id">  
                                <?   
									echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'subcon_packing_bill_issue_controller', this.value, 'load_drop_down_party_name_popup', 'party_td_pop' );",0 );
                                ?>
                            </td>
                            <td id="party_td_pop">
								<?
									echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
									
                                ?> 
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To 
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'packing_list_view', 'search_div', 'subcon_packing_bill_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
                            <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top" colspan="5" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="packing_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and party_id='$data[1]'"; else $party_name="";//{ echo "Please Select Party First."; die; }
	if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date="";
	if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$arr=array (2=>$location,4=>$party_arr,5=>$knitting_source,6=>$bill_for);
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, bill_no, prefix_no_num, $year_cond, location_id, bill_date, party_id, party_source, bill_for from subcon_inbound_bill_mst where status_active=1 and process_id=11 $company_name $party_name $return_date $bill_id_cond order by id DESC";
	
	echo  create_list_view("list_view", "Bill No,Year,Location,Bill Date,Party,Source,Bill For", "50,40,80,70,100,120,140","630","250",0, $sql , "js_set_value", "id", "", 1, "0,0,location_id,0,party_id,party_source,bill_for", $arr , "prefix_no_num,year,location_id,bill_date,party_id,party_source,bill_for", "subcon_packing_bill_issue_controller","",'0,0,0,3,0,0,0') ;
	exit();
}

if ($action=="load_php_data_to_form_issue")
{
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, party_id, party_source, bill_for, is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_packing_bill_issue_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_party_source').value				= '".$row[csf("party_source")]."';\n"; 
		echo "load_drop_down( 'requires/subcon_packing_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('hidden_integ_unlock').value			= '".$row[csf("post_integration_unlock")]."';\n";
		
		if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==0)
		{
			echo "$('#accounting_integration_div').text('All Ready Posted in Accounting.');\n"; 
		}
		else if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==1)
		{
			echo "$('#accounting_integration_div').text('Deleting not allowed since posted in Accounts.Only Data changing is allowed.');\n"; 
		}
		else 
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	}
	exit();
}

if ($action=="packing_delivery_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$data=explode('_',$data);
	if($data[0]==2)
	{
		?>
		</head>
		<body>
			<div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="817px" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="80">Challan No</th>
						<th width="80">Delivery Date</th>
						<th width="150">Order No</th>                    
						<th width="200">Item Description</th>
						<th width="100">Delivery Qty</th>
						<th width="100">Process</th>
						<th width="" >Currency</th>
					</thead>
			 </table>
        </div>
        <div style="width:820px;max-height:180px; overflow-y:scroll" id="packing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800px" class="rpt_table" id="list_view_issue">
            <?
				$order_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');
                $i=1;
				if(!$data[2])
				{
					$sql="select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[1]' and b.process_id='11' and a.status_active=1 and a.is_deleted=0"; 
				}
				else
				{
					$sql="(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.party_id='$data[1]' and b.process_id='11' and a.status_active=1 and b.bill_status='0')
					 union 
					 	(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.party_id='$data[1]' and b.process_id='11' and b.id in ( $data[3] ) and a.status_active=1)";
				}
				$sql_result =sql_select($sql);
                foreach($sql_result as $row)
				{
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr id="tr_<?  echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$currency_arr[$row[csf('order_id')]]; ?>');" > 
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                        <td width="150" align="center"><? echo $order_arr[$row[csf('order_id')]]; ?></td>
                        <?
                        $process_id_val=$row[csf('process_id')];
						if($process_id_val==1 || $process_id_val==5 || $process_id_val==10 || $process_id_val==11)
						{
							$item_id_arr=$garments_item;
						}
						else
						{
							$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
						}
						?>
                        <td width="200" align="center"><? echo $item_id_arr[$row[csf('item_id')]]; ?></td>
                        <td width="100" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
                        <td width="100" align="center"><? echo $production_process[$row[csf('process_id')]]; ?></td>
                        <td width="" align="center"><? echo $currency[$currency_arr[$row[csf('order_id')]]]; ?>
                        <input type="hidden" id="currid<? echo $row[csf('id')]; ?>" value="<? echo $currency_arr[$row[csf('order_id')]]; ?>"></td>
                    </tr>
                    <?php
                    $i++;
                }
				?>
            </table>
            </div>
            <table>
                <tr style="border:none">
                    <td align="center" colspan="8" >
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close()" />
                    </td>
                </tr>
           </table>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	}
	exit();
}

if ($action=="load_php_dtls_form")  //new issue 
{
	$data = explode("_",$data);
	$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
	$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id);   $delete_id=implode(",",$delete_id);
	
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$color_id=return_library_array( "select id,color_id from lib_subcon_charge",'id','color_id');
	$febricdesc_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	
	$order_array=array();
	$order_sql="Select b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref, b.main_process_id, b.process_id, b.rate, b.amount, a.currency_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result =sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$order_array[$row[csf("id")]]['cust_buyer']=$row[csf("cust_buyer")];
		$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
		$order_array[$row[csf("id")]]['rate']=$row[csf("rate")];
		$order_array[$row[csf("id")]]['amount']=$row[csf("amount")];
		$order_array[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
		$order_array[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$order_array[$row[csf("id")]]['process_id']=$row[csf("process_id")];
	}
	//var_dump($order_array);die;
	
	if( $data[2]!="" )
	{
		$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id=11"; 
	}
	else
	{
		if($bill_id!="" && $del_id!="")
			$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=11)
			 union
			 (select 0, b.id as delivery_id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, 0, 0, null, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and b.process_id=11)";
		else if($bill_id!="" && $del_id=="")
			$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=11";
		else  if($bill_id=="" && $del_id!="")
			$sql="select 0, b.id as delivery_id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, 0, 0, 0, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id=11"; 
	}
	$sql_result =sql_select($sql);	
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		if( $data[2]!="" )
		{
			if($data[1]=="") $data[1]=$row[csf("delivery_id")]; else $data[1].=",".$row[csf("delivery_id")];
		}
		?>
        <tr align="center" id="dtls_form_delete">				
            <td>
				<? if ($k==$num_rowss) { ?>
                <input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
                <input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
                <? } ?>
                <input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:80px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>" />
                <input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
                <input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" value="<? echo $row[csf("delivery_id")]; ?>">
                <input type="text" name="txt_deleverydate_<? echo $k; ?>" id="txt_deleverydate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled />									
            </td>
            <td>
                <input type="text" name="txt_challenno_<? echo $k; ?>" id="txt_challenno_<? echo $k; ?>"  class="text_boxes" style="width:55px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
            </td>
            <td>
                <input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:50px" > 
                <input type="text" name="txt_orderno_<? echo $k; ?>" id="txt_orderno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $order_array[$row[csf("order_id")]]['order_no']; ?>" readonly />										
            </td>
            <td>
                <input type="text" name="txt_stylename_<? echo $k; ?>" id="txt_stylename_<? echo $k; ?>"  class="text_boxes" style="width:80px;" value="<? echo $order_array[$row[csf("order_id")]]['cust_style_ref']; ?>"  />
            </td>
            <td>
                <input type="text" name="txt_buyername_<? echo $k; ?>" id="txt_buyername_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $order_array[$row[csf("order_id")]]['cust_buyer']; ?>"  />								
            </td>
            <td>			
                <input name="txt_numberroll_<? echo $k; ?>" id="txt_numberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:40px" value="<? echo $row[csf("carton_roll")]; ?>" readonly />							
            </td> 
            <td>
                <input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
                <input type="text" name="text_febricdesc_<? echo $k; ?>" id="text_febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:105px" value="<? echo $garments_item[$row[csf("item_id")]]; ?>" readonly/>
            </td>
            <td>
                <input type="hidden" name="color_process_<? echo $k; ?>" id="color_process_<? echo $k; ?>" value="<? echo $order_array[$row[csf("order_id")]]['main_process_id']; ?>">
                <input type="text" name="txt_color_process_<? echo $k; ?>" id="txt_color_process_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $color_arr[$color_id[$row[csf("item_id")]]].''.$production_process[$order_array[$row[csf("order_id")]]['main_process_id']]; ?>" readonly/>
            </td>
            <td>
				<?
					$process=explode(',',$order_array[$row[csf("order_id")]]['process_id']);
					$add_process='';
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=",".$conversion_cost_head_array[$inf];
					}
                ?>
                <input type="hidden" name="add_process_<? echo $k; ?>" id="add_process_<? echo $k; ?>" value="<? echo $order_array[$row[csf("order_id")]]['process_id']; ?>">
                <input type="text" name="txt_add_process_<? echo $k; ?>" id="txt_add_process_<? echo $k; ?>"  class="text_boxes" style="width:115px" value="<? echo $add_process; ?>" readonly/>
            </td>
            <td>
                <input type="text" name="txt_deliveryqnty_<? echo $k; ?>" id="txt_deliveryqnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("delivery_qty")]; ?>" readonly />
            </td>
            <td>
                <input type="text" name="txt_rate_<? echo $k; ?>" id="txt_rate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px" value="<? echo $order_array[$row[csf("order_id")]]['rate']; ?>" />
            </td>
            <td>
				<?
					$total_amount=$row[csf("delivery_qty")]*$order_array[$row[csf("order_id")]]['rate'];
                ?>
                <input type="text" name="txt_amount_<? echo $k; ?>" id="txt_amount_<? echo $k; ?>" style="width:60px"  class="text_boxes_numeric"  value="<? echo  $total_amount; ?>" readonly />
            </td>
            <td>
            <? 
            //$order_array[$row[csf("order_id")]]['currency_id']
            echo create_drop_down( "cbo_curanci_$k", 60, $currency,"", 1, "-Select Currency-",$row[csf("currency_id")],"",0,"" );
            ?>
            </td>
            <td>
                <input type="text" name="txt_remarks_<? echo $k; ?>" id="txt_remarks_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $row[csf("remarks")]; ?>" />
            </td>
        </tr>
	<?	
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$process_id=11; 
	if ($operation==0)   // Insert Here========================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0"; 
			die;			
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'PNF', date("Y",time()), 5, "select prefix_no,prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=$process_id  $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, party_id, party_source, bill_for, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_name.",".$cbo_party_source.",".$cbo_bill_for.",".$process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array;die;
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,0);
			$return_no=$new_bill_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="bill_no*company_id*location_id*bill_date*party_id*party_source*bill_for*updated_by*update_date";
			$data_array="".$txt_bill_no."*".$cbo_company_id."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, packing_qnty, delivery_qty, rate, amount, remarks, process_id, currency_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*packing_qnty*delivery_qty*rate*amount*remarks*currency_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="txt_deleverydate_".$i;
			$challen_no="txt_challenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$style_name="txt_stylename_".$i;
			$buyer_name="txt_buyername_".$i;
			$number_roll="txt_numberroll_".$i;
			$quantity="txt_deliveryqnty_".$i;
			$rate="txt_rate_".$i;
			$amount="txt_amount_".$i;
			$remarks="txt_remarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="cbo_curanci_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1.=",";
				$data_array1.="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$number_roll.",".$$quantity.",".$$rate.",".$$amount.",".$$remarks.",'".str_replace("'",'',$process_id)."',".$$curanci.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1")); 
			}
			else
			{
			$id_arr[]=str_replace("'",'',$$updateid_dtls);
			$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
			$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
		}
			//order table insert====================================================================================================
		if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
		{
			$order_id_arr[]=str_replace("'",'',$$orderid);
			$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
		}
		else
		{
			$order_id_arr[]=str_replace("'",'',$$orderid);
			$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
		}
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));

		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1; die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
		}
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID)
			{
				mysql_query("ROLLBACK");  
				echo "5**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 && $rID2 && $rID4)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID)
			{
				oci_rollback($con);
				echo "5**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		
		$nameArray= sql_select("select is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			exit();
		}
		
		$field_array="bill_no*company_id*location_id*bill_date*party_id*party_source*bill_for*updated_by*update_date";
		$data_array="".$txt_bill_no."*".$cbo_company_id."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,1);
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, packing_qnty, delivery_qty, rate, amount, remarks, process_id, currency_id,inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*packing_qnty*delivery_qty*rate*amount*remarks*currency_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="txt_deleverydate_".$i;
			$challen_no="txt_challenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$buyer_name="txt_buyername_".$i;
			$number_roll="txt_numberroll_".$i;
			$number_roll="txt_numberroll_".$i;
			$quantity="txt_deliveryqnty_".$i;
			$rate="txt_rate_".$i;
			$amount="txt_amount_".$i;
			$remarks="txt_remarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="cbo_curanci_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$number_roll.",".$$quantity.",".$$rate.",".$$amount.",".$$remarks.",'".$process_id."',".$$curanci.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1")); 
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
		//order table insert====================================================================================================
			if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
			}
			else
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
			}
			//order table insert====================================================================================================
		}
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
		$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
		}
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery );
		$rID3=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery ));
				
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID && $rID1 && $rID2 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID && $rID1 && $rID2 && $rID4)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=str_replace("'",'',$update_id);
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$field_array_delivery="bill_status";
		
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery );
		$rID4=execute_query(bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery ));
		
		if($db_type==0)
		{
			if($rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="packing_bill_print") 
{
    extract($_REQUEST);
	//echo $data;
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:990px;">
         <table width="990" cellspacing="0" align="right" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <?
                        $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?> 
                            Block No: <? echo $result[csf('block_no')];?> 
                            City No: <? echo $result[csf('city')];?> 
                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                            Province No: <?php echo $result[csf('province')];?> 
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                            Email Address: <? echo $result[csf('email')];?> 
                            Website No: <? echo $result[csf('website')]; ?> <br>
                            <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                        }
                    ?> 
                </td>
            </tr>           
        	<tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
            </tr>
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
             <tr>
             <?
			 	/*if($dataArray[0][csf('party_source')]==3)
				{*/
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	//$address="";
						if($result!="") {$address=$result[csf('address_1')];}
					}
				//}
			 ?>
             
                <td><strong>Party Name : </strong></td><td colspan="5"> <? echo $party_library[$dataArray[0][csf('party_id')]].'  <strong style="margin-left:30px;"> Address : </strong> '.$address; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="990"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="60" align="center">Challan No</th>
                <th width="65" align="center">D. Date</th>
                <th width="70"align="center">Order</th> 
                <th width="70" align="center">Buyer</th>
                <th width="70" align="center">Style</th>
                              
                <th width="120"  align="center">Item Des.</th>
                <th width="30" align="center">Roll</th>
                <th width="60" align="center">D. Qty</th>
                <th width="30" align="center">UOM</th>
                <th width="60" align="center">Currency</th>
                <th width="30" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="" align="center">Remarks</th>
            </thead>
		 <?
		 	$order_array=array();
			$order_sql="select id, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
			//var_dump($order_array);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, packing_qnty, delivery_qty, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['cust_style_ref']; ?></p></td>
                   <!-- <td><p><? //echo $yarn_desc_arr[$row[csf('item_id')]]; ?></p></td>-->
                    <td><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[$order_array[$row[csf('order_id')]]['order_uom']]; ?></p></td>
                    <td><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="7"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
                
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="14" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <table width="930" align="left" > 
            <tr>

                <td colspan=14 align=left>&bull; Receiver should be aware of the quantity &amp; specification of the Product(s) at the time of taking delivery.</td>
            </tr>
            <tr>
                <td colspan=14 align=left>&bull; No claim will be entertained after delivery of goods.</td>

            </tr>
            <tr>
                <td colspan=14 align=left>&bull; Delivery Challan have been attached.</td>
            </tr>
            <tr>
                <td colspan=14 align=left>&bull; Payment should be made within seven days from the bill date.</td>
            </tr> 
        </table>
        <br>
		 <?
            echo signature_table(158, $data[0], "990px");
         ?>
   </div>
   </div>
<?
}
?>