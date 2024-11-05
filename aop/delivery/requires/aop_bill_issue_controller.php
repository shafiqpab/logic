<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php'); 

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	//$exchange_rate = set_conversion_rate($data[0], $conversion_date);
	echo $exchange_rate;
	exit();
}

//$batch_no_arr=return_library_array( "SELECT id, batch_no from pro_batch_create_mst where status_active =1 and is_deleted=0",'id','batch_no');

	/*$batch_no_sql="select a.id, a.batch_no,b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 group by a.id, a.batch_no,b.width_dia_type";
	$batch_no_result_arr =sql_select($batch_no_sql);
	$batch_no_arr=array();
	$width_dia_type_arr=array();
	foreach ($batch_no_result_arr as $row)
	{
		$batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
		$width_dia_type_arr[$row[csf('id')]]=$row[csf('width_dia_type')];
	}
	echo "<pre>";
	print_r($width_dia_type_arr);*/
	

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "SELECT id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}


if ($action=="load_drop_down_location_popup")
{
	$data=explode("_",$data);
	echo create_drop_down("cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}


if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//if($data[2]!=0 && $data[2]!='') $disabled='1'; else $disabled='0';
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
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
			$("#selected_job").val(id);
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
	$cbo_within_group=str_replace("'","",$data[3]);
	$year=str_replace("'","",$data[4]);
	$search_job=str_replace("'","",$data[5]);
	$search_order=trim(str_replace("'","",$data[6]));
	//echo $search_order ; die;
	$search_job_cond="";$search_order_cond="";
	if ($search_order!='') $search_order_cond=" and b.order_no like '%$search_order%'"; else $search_order_cond="";
	if ($search_job!='') $search_job_cond=" and a.subcon_job like '%$search_job%'"; else $search_job_cond="";
	if($company_name!=0) $company=" and a.company_id='$company_name'"; else { echo "Please Select Company First."; die; }
	if($location_name!=0) $location_id=" and a.location_id='$location_name'";
	if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'";
	if($db_type==0) 
	{
		$date_cond=" and YEAR(a.insert_date)=$year";
	}
	else if ($db_type==2)
	{
		$date_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year";
	}
	//and to_char(b.id)=d.order_id  and c.entry_form=291
	$sql= "SELECT a.id as job_id,c.id as production_id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no,a.delivery_date from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d, pro_batch_create_dtls f  where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=278 and d.batch_id=f.mst_id and d.id=f.po_id $search_job_cond $date_cond $company $location_id  $search_order_cond  $within_group_cond  group by a.id,c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no,a.delivery_date";

	echo  create_list_view("list_view", "Job No,Order No,Delivery Date,AOP Ref.","130,120,70","550","350",0,$sql, "js_set_value","subcon_job,production_id,job_id","",1,"0,0,0,0",$arr,"subcon_job,order_no,delivery_date,aop_reference", "",'','0,0,0,0') ;
	exit();		 
}  
if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center"><input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" /></td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="aop_bill_list_view")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	//echo $data;
	$data=explode('***',$data);
	
		//echo "<pre>";
		//print_r($data);
		$company_name=str_replace("'","",$data[0]);
		$location_name=str_replace("'","",$data[1]);
		$within_group=str_replace("'","",$data[2]);
		$party_name=str_replace("'","",$data[3]);
		$party_location=str_replace("'","",$data[4]);
		$bill_date=str_replace("'","",$data[5]);
		$date_from=str_replace("'","",$data[6]);
		$date_to=str_replace("'","",$data[7]);
		$job_no=str_replace("'","",$data[8]);
		$job_id=str_replace("'","",$data[9]);
		$production_id=str_replace("'","",$data[10]);
		$update_id=str_replace("'","",$data[11]);
		$update_id=str_replace("'","",$data[13]);
		$delv_id="'".implode("','",explode('!!!!',$data[13]))."'";
		//echo $delv_id; die;
		
		$batch_no_sql="select a.id, a.batch_no,b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 group by a.id, a.batch_no,b.width_dia_type";
		$batch_no_result_arr =sql_select($batch_no_sql);
		$batch_no_arr=array();
		$width_dia_type_arr=array();
		foreach ($batch_no_result_arr as $row)
		{
			$batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
			$width_dia_type_arr[$row[csf('id')]]=$fabric_typee[$row[csf('width_dia_type')]];
		}
		//echo "<pre>";
		//print_r($width_dia_type_arr);
		//-------------------------------------------------------------------------------------------------------------------------------------------------
		if($company_name!=0) $company=" and a.company_id='$company_name'"; else { echo "Please Select Company First."; die; }
		if($job_no!='')
		{
			if ($job_no!='') $search_job_cond=" and c.subcon_job='$job_no'"; else $search_job_cond="";
		}
		else
		{
			if($db_type==0)
			{ 
			if ($date_from!="" &&  $date_to!="") $date_cond= "and a.product_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
			$year_cond= "year(b.insert_date)as year";
			}
			else if ($db_type==2)
			{
			if ($date_from!="" &&  $date_to!="") $date_cond= "and a.product_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
			$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
			}
		}
		if($party_name!=0) $party_id_cond=" and a.party_id='$party_name'";
		if($location_name!=0) $location_id=" and a.location_id='$location_name'";
		if($party_location!=0) $party_location_id=" and a.delv_party_location='$party_location'";
		if($within_group!=0) $within_group_cond=" and a.within_group='$within_group'";
		
		$order_array=array();
		$order_sql="Select b.id, b.order_no, b.order_uom, b.process_id, b.cust_buyer, b.cust_style_ref, b.rate, b.amount, a.currency_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278";
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
 		//--------------------------------------------------------------------------------------------------------------------------------------------------
	
		?>
		</head>
		<body>
			<div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="917px" class="rpt_table">
					<thead>
                    	<th width="40">&nbsp;</th>
						<th width="30">SL</th>
						<th width="110">Challan No</th>
						<th width="60">Delivery Date</th>
						<th width="110">Order No</th>                    
						<th width="70">Buyer PO</th>                    
						<th width="180">Fabric Description</th>
						<th width="60">Delivery Qty</th>
						<th  width="60">Currency</th>
                        <th>Process</th>
					</thead>
			 </table>
        </div>
        <div style="width:920px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900px" class="rpt_table" id="tbl_list_search">
            <?
                $i=1;
				if(!$update_id)
				{
					 $sql="select a.id as del_mst_id,a.prefix_no_num,a.product_no,a.product_date,b.id as delev_dtls_id,b.batch_id,b.production_id,b.width_dia_type, b.order_id,b.process,b.fabric_description,b.product_qnty, b.no_of_roll,b.buyer_po_id,c.subcon_job,d.order_no,d.buyer_po_id as order_buyer_po_id,d.buyer_po_no, d.buyer_style_ref, d.order_id as work_order_id,d.booking_dtls_id,0 as type,d.rate,f.po_id from subcon_production_mst a, subcon_production_dtls b,subcon_ord_mst c,subcon_ord_dtls d, pro_batch_create_dtls f  where a.entry_form=307  and a.id=b.mst_id and c.id=d.mst_id and b.batch_id=f.mst_id and d.id=f.po_id and to_char( f.po_id )=b.order_id  and b.buyer_po_id=d.buyer_po_id and c.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.bill_status=0 $date_cond $company $party_id_cond $within_group_cond $search_job_cond";
					 //$location_id $party_location_id
				}
				else
				{
					//to_char(d.id)=b.order_id
					 $sql="(select a.id as mst_id,a.prefix_no_num,a.product_no,a.product_date,b.id as delev_dtls_id,b.batch_id,b.production_id,b.width_dia_type, b.order_id,b.process,b.fabric_description,b.product_qnty, b.no_of_roll,b.buyer_po_id,c.subcon_job,d.order_no,d.buyer_po_id as order_buyer_po_id,d.buyer_po_no, d.buyer_style_ref, d.order_id as work_order_id,d.booking_dtls_id, 0 as type,d.rate,f.po_id from subcon_production_mst a, subcon_production_dtls b,subcon_ord_mst c,subcon_ord_dtls d, pro_batch_create_dtls f where a.entry_form=307  and a.id=b.mst_id and c.id=d.mst_id and b.batch_id=f.mst_id and d.id=f.po_id and to_char( f.po_id )=b.order_id  and b.buyer_po_id=d.buyer_po_id and c.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.bill_status=0 $date_cond $company $party_id_cond $within_group_cond $search_job_cond)
					 union 
					 	(select a.id as mst_id,a.prefix_no_num,a.product_no,a.product_date,b.id as delev_dtls_id,b.batch_id,b.production_id,b.width_dia_type, b.order_id,b.process,b.fabric_description,b.product_qnty, b.no_of_roll,b.buyer_po_id,c.subcon_job,d.order_no,d.buyer_po_id as order_buyer_po_id,d.buyer_po_no, d.buyer_style_ref, d.order_id as work_order_id,d.booking_dtls_id, 1 as type,d.rate,f.po_id from subcon_production_mst a, subcon_production_dtls b,subcon_ord_mst c,subcon_ord_dtls d,subcon_inbound_bill_dtls e , pro_batch_create_dtls f where a.entry_form=307 and to_char(b.id)=e.delivery_id  and  e.delivery_id in ($delv_id) and a.id=b.mst_id and c.id=d.mst_id  and b.batch_id=f.mst_id and d.id=f.po_id and to_char( f.po_id )=b.order_id  and c.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.bill_status=1 $company $party_id_cond $within_group_cond) order by type DESC";
						// $location_id  $party_location_id
				//$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id, coller_cuff_measurement,currency_id from subcon_inbound_bill_dtls where mst_id=$upid and process_id=358 and status_active=1 and is_deleted=0 and process_id=358 order by challan_no ASC";
				}
				//echo $sql;
				$sql_result =sql_select($sql);
				foreach($sql_result as $row) // for update row
				{
					$process_id_val=$row[csf('process')];
					$process_id=explode(",", $process_id_val);
					$process_name="";
					foreach($process_id as $process_val)
					{
						if ($process_val=="") $process_name=$conversion_cost_head_array[$process_val]; else $process_name.=','.$conversion_cost_head_array[$process_val];
					}
					$process_name=implode(",",array_unique(explode(',',$process_name)));
					
					$all_value=$row[csf('delev_dtls_id')];
					
					$str_val=$row[csf('delev_dtls_id')].'_'.$row[csf('prefix_no_num')].'_'.change_date_format($row[csf('product_date')]).'_'.$row[csf('batch_id')].'_'.$row[csf('po_id')].'_'.$process_name.'_'.$row[csf('product_qnty')].'_'.$row[csf('order_no')].'_'.$row[csf('subcon_job')].'_'.$row[csf('order_buyer_po_id')].'_'.$row[csf('buyer_po_no')].'_'.$row[csf('buyer_style_ref')].'_'.$row[csf('work_order_id')].'_'.$row[csf('booking_dtls_id')].'_'.$row[csf('fabric_description')].'_'.$row[csf('no_of_roll')].'_'.$row[csf('process')].'_'.$batch_no_arr[$row[csf('batch_id')]].'_'.$width_dia_type_arr[$batch_no_arr[$row[csf('batch_id')]]].'_'.$row[csf('rate')].'_'.$order_array[$row[csf("po_id")]]['currency_id'];//$order_array[$row[csf("order_id")]]['currency_id'] 
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$checked_val=2; $ischeck="";
					
					
					if ($row[csf('type')]==0) { $row_color=$bgcolor; $checked_val=2; $ischeck="";}
					else {	$bgcolor="yellow"; $checked_val=1; $ischeck="checked";}
					?>
					<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('delev_dtls_id')]."***".$row[csf('product_no')]."***".$row[csf('product_date')]."***".$row[csf('batch_id')]."***".$row[csf('po_id')]."***".$row[csf('process')]."***".$row[csf('product_qnty')]."***".$row[csf('order_no')]; ?>');" >
						<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="110" style="word-break:break-all"><? echo $row[csf('product_no')]; ?></td>
						<td width="60"><? echo change_date_format($row[csf('product_date')]); ?></td>
						<td width="110" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
						<td width="70" style="word-break:break-all"><? echo $row[csf('buyer_po_no')]; ?></td>
						<td width="180" style="word-break:break-all"><? echo $row[csf('fabric_description')]; ?></td>
						<td width="60" align="right"><? echo $row[csf('product_qnty')]; ?>&nbsp;</td>
                        <td width="60" style="word-break:break-all"><? echo $currency[$order_array[$row[csf("po_id")]]['currency_id']]; ?>
						<td style="word-break:break-all"><? echo $process_name; ?>
						<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
						</td>
					</tr>
					<?php
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
	exit();
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	
	//print_r($ex_data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}

		function fnc_load_party_order_popup(company,party_name,within_group)
		{   	
			load_drop_down( 'aop_bill_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer', 'party_td' );
			if(party_name!=0 && party_name!=''){
				$('#cbo_party_name').attr('disabled',true);
			}
		}
		
		/*function fnc_load_party_popup(type,within_group)
		{
			//alert(within_group);
			var company = $('#cbo_company_id').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'aop_bill_issue_controller', company+'_'+within_group, 'load_drop_down_buyer', 'party_td' );
		}*/

		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_within_group').value;
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $ex_data[0];?>,<? echo $ex_data[1];?>,<? echo $ex_data[3];?>)" >
        <div align="center" style="width:100%;" >
            <form name="knittingbill_1"  id="knittingbill_1" autocomplete="off">
                <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                     <tr>
                         <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",'1' ); ?></th>
                    </tr>
                     <tr>
                        <th width="130">Company Name</th>
                        <th width="70">Within Group</th>
                        <th width="130">Party Name</th>
                        <th width="120">Location</th>
                        <th width="70">Bill ID</th>
                        <th width="170" colspan="2">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                     </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="issue_id">
								<?   
									echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"fnc_load_party_popup(1,".$ex_data[3].");load_drop_down( 'aop_bill_issue_controller', this.value, 'load_drop_down_location_popup', 'location_td');",1 );
                                ?>
                            </td>
                            <td>
                            	<?
		                    		echo create_drop_down( "cbo_within_group", 70, $yes_no,"", 0, "-- Select --",$ex_data[3], "" ,1);
		                    	?> 
                            </td>
                            <td width="130" id="party_td">
								<?
									echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", 0, "","","","","","",5 );
                                ?> 
                            </td>
                            <td id="location_td">
								<?
									echo create_drop_down( "cbo_location_name", 120, $blank_loc,"", 1, "--Select Location--", $selected,"","","","","","",3);
								?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:65px" placeholder="Write" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $ex_data[3]; ?>', 'bill_list_view', 'search_div', 'aop_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div" style="margin-top:10px;"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="bill_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name_cond=" and a.party_id='$data[1]'"; else $party_name_cond="";
	if ($data[7]!=0) $within_group_cond=" and c.within_group='$data[7]'"; else $within_group_cond="";
	$date_from=str_replace("'","",$data[2]);
	$date_to=str_replace("'","",$data[3]);
	$bill_no_prefx=str_replace("'","",$data[4]);
	$location_name=str_replace("'","",$data[5]);
	if($location_name!=0) $location_id=" and a.location_id='$location_name'";
	if($bill_no_prefx!=''){
		if ($bill_no_prefx!='') $search_bill_cond=" and a.prefix_no_num='$bill_no_prefx'"; else $search_bill_cond="";
	}

	//-------------------------------------------------------------------------------------------------------------------------------------------------
		
	if($db_type==0){ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
	}else if ($db_type==2){
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.bill_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
	}
	$within_group=$data[10];
	
	$sql= "select a.id,a.bill_no,a.prefix_no_num,a.location_id,a.bill_date,a.party_id,a.bill_for from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, pro_batch_create_mst c where a.id=b.mst_id and c.id=b.batch_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.process_id=358  $party_name_cond $company_name $location_id $date_cond $search_bill_cond $within_group_cond
	group by a.id,a.bill_no,a.prefix_no_num,a.location_id,a.bill_date,a.party_id,a.bill_for order by a.id DESC";
	
	//echo $sql; die;
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	if($within_group==1){
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else if($within_group==2){
		$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	}else{
		$party_arr='';
	}
	
	?> 
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Bill No</th>
                <th width="120">Location</th>
                <th width="80">Bill Date</th>
                <th width="120">Party</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_po_list">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")]; ?>);" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
						<td width="120"><? echo $location[$row[csf("location_id")]];  ?></td>
						<td width="80"><? echo change_date_format($row[csf("bill_date")]); ?></td>
						<td width="120"><? echo $party_arr[$row[csf("party_id")]];?> </td>	
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

if ($action=="load_php_data_to_form_issue")
{
	$ex_data=explode('_',$data);
	
	$sql="SELECT min(delivery_date) as min_date, max(delivery_date) as max_date FROM subcon_inbound_bill_dtls WHERE mst_id='$ex_data[0]' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id,party_location_id, bill_date, party_id, party_source, attention, bill_for, is_posted_account,post_integration_unlock,currency,exchange_rate  from subcon_inbound_bill_mst where id=$ex_data[0]");
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "fnc_load_party(1,".$ex_data[1].");\n";	
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "fnc_load_party(2,".$ex_data[1].");\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_party_location').value			= '".$row[csf("party_location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('txt_del_date_from').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_del_date_to').value 				= '".change_date_format($maxdate)."';\n";  
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('is_posted_account').value            = '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('cbo_currency_id').value            	= '".$row[csf("currency")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value            = '".$row[csf("exchange_rate")]."';\n";
		//echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_name*cbo_bill_for',1);\n";
		//echo "fnc_disable_mst_field(document.getElementById('cbo_party_name').value);\n";
	}	
	exit();
}
if ($action=="load_dtls_data") 
{
	$ex_data=explode("!^!",$data);
	$upid=$ex_data[0];
	//var_dump($order_array);
	
	$batch_no_sql="select a.id, a.batch_no,b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 group by a.id, a.batch_no,b.width_dia_type";
		$batch_no_result_arr =sql_select($batch_no_sql);
		$batch_no_arr=array();
		$width_dia_type_arr=array();
		foreach ($batch_no_result_arr as $row)
		{
			$batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
			$width_dia_type_arr[$row[csf('id')]]=$fabric_typee[$row[csf('width_dia_type')]];
		}
	
	
	$sql="select id as upd_id,delivery_id,delivery_date,challan_no,packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, coller_cuff_measurement,currency_id from subcon_inbound_bill_dtls where mst_id=$upid and process_id=358 and status_active=1 and is_deleted=0 and process_id=358 order by challan_no ASC";
	
	$sql_result_arr =sql_select($sql);
	
	$delivery_id_arr=array();
	foreach ($sql_result_arr as $row)
	{
		$delivery_id_arr[$row[csf('delivery_id')]]=$row[csf('delivery_id')];
		
	}
		$job_order_arr=array();
		
		$sql_job="select a.id as mst_id,a.prefix_no_num,a.product_no,a.product_date,b.id as delev_dtls_id,b.batch_id,b.production_id,b.width_dia_type, b.order_id,b.process,b.fabric_description,b.product_qnty, b.no_of_roll,b.buyer_po_id,c.subcon_job,d.order_no,d.buyer_po_id as order_buyer_po_id,d.buyer_po_no, d.buyer_style_ref, d.order_id as work_order_id,d.booking_dtls_id from subcon_production_mst a, subcon_production_dtls b,subcon_ord_mst c,subcon_ord_dtls d ,pro_batch_create_dtls f where a.entry_form=307  and a.id=b.mst_id and c.id=d.mst_id and b.batch_id=f.mst_id and d.id=f.po_id and c.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.id in(".implode(',', $delivery_id_arr).") ";  
		 // and to_char(d.id)=b.order_id
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			
			$process_id_val=$row[csf('process')];
			$process_id=explode(",", $process_id_val);  //$fabric_typee
			$process_name="";
			foreach($process_id as $process_val)
			{
				if ($process_val=="") $process_name=$conversion_cost_head_array[$process_val]; else $process_name.=','.$conversion_cost_head_array[$process_val];
			}
			$process_name=implode(",",array_unique(explode(',',$process_name)));
			
			
			$job_order_arr[$row[csf('delev_dtls_id')]]['batch_no']=$batch_no_arr[$row[csf('batch_id')]];
			$job_order_arr[$row[csf('delev_dtls_id')]]['prefix_no_num']=$row[csf('prefix_no_num')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['product_date']=change_date_format($row[csf('product_date')]);
			$job_order_arr[$row[csf('delev_dtls_id')]]['batch_id']=$row[csf('batch_id')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['process_name']=$process_name;
			$job_order_arr[$row[csf('delev_dtls_id')]]['order_no']=$row[csf('order_no')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['subcon_job']=$row[csf('subcon_job')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['order_buyer_po_id']=$row[csf('order_buyer_po_id')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['work_order_id']=$row[csf('work_order_id')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['booking_dtls_id']=$row[csf('booking_dtls_id')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['fabric_description']=$row[csf('fabric_description')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['no_of_roll']=$row[csf('no_of_roll')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['process']=$row[csf('process')];
			$job_order_arr[$row[csf('delev_dtls_id')]]['order_id']=$row[csf('order_id')];
			
		}
		unset($sql_job_result);
	/*echo "<pre>";
	print_r($job_order_arr); 
	
	die;
	*/
	 $str_val="";
	foreach ($sql_result_arr as $row) // delivery_id $job_order_arr[$row[csf('delivery_id')]]['work_order_id']
	{
		if($str_val=="") $str_val=$row[csf('delivery_id')].'_'.$row[csf('challan_no')].'_'.$job_order_arr[$row[csf('delivery_id')]]['product_date'].'_'.$job_order_arr[$row[csf('delivery_id')]]['batch_id'].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('delivery_id')]]['process_name'].'_'.$row[csf('delivery_qty')].'_'.$job_order_arr[$row[csf('delivery_id')]]['order_no'].'_'.$job_order_arr[$row[csf('delivery_id')]]['subcon_job'].'_'.$job_order_arr[$row[csf('delivery_id')]]['order_buyer_po_id'].'_'.$job_order_arr[$row[csf('delivery_id')]]['buyer_po_no'].'_'.$job_order_arr[$row[csf('delivery_id')]]['buyer_style_ref'].'_'.$job_order_arr[$row[csf('delivery_id')]]['work_order_id'].'_'.$job_order_arr[$row[csf('delivery_id')]]['booking_dtls_id'].'_'.$job_order_arr[$row[csf('delivery_id')]]['fabric_description'].'_'.$row[csf('carton_roll')].'_'.$job_order_arr[$row[csf('delivery_id')]]['process'].'_'.$job_order_arr[$row[csf('delivery_id')]]['batch_no'].'_'.$row[csf('upd_id')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('remarks')].'_'.$width_dia_type_arr[$job_order_arr[$row[csf('delivery_id')]]['batch_id']].'_'.$row[csf('currency_id')];
		else $str_val.="###".$row[csf('delivery_id')].'_'.$row[csf('challan_no')].'_'.$job_order_arr[$row[csf('delivery_id')]]['product_date'].'_'.$job_order_arr[$row[csf('delivery_id')]]['batch_id'].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('delivery_id')]]['process_name'].'_'.$row[csf('delivery_qty')].'_'.$job_order_arr[$row[csf('delivery_id')]]['order_no'].'_'.$job_order_arr[$row[csf('delivery_id')]]['subcon_job'].'_'.$job_order_arr[$row[csf('delivery_id')]]['order_buyer_po_id'].'_'.$job_order_arr[$row[csf('delivery_id')]]['buyer_po_no'].'_'.$job_order_arr[$row[csf('delivery_id')]]['buyer_style_ref'].'_'.$job_order_arr[$row[csf('delivery_id')]]['work_order_id'].'_'.$job_order_arr[$row[csf('delivery_id')]]['booking_dtls_id'].'_'.$job_order_arr[$row[csf('delivery_id')]]['fabric_description'].'_'.$row[csf('carton_roll')].'_'.$job_order_arr[$row[csf('delivery_id')]]['process'].'_'.$job_order_arr[$row[csf('delivery_id')]]['batch_no'].'_'.$row[csf('upd_id')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('remarks')].'_'.$width_dia_type_arr[$job_order_arr[$row[csf('delivery_id')]]['batch_id']].'_'.$row[csf('currency_id')]; 
					
	}
	echo $str_val;
	exit();
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="358"; 
	
	
	
	if ($operation==0)   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0"; 
			disconnect($con); die;			
		}
		if($db_type==0)$year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'AOPI', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_name and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		//echo "10**".print_r($new_bill_no); disconnect($con); die;
			
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id,party_source, location_id,party_location_id, bill_date, party_id, process_id,currency,exchange_rate, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_name.",".$cbo_within_group.",".$cbo_location_name.",".$cbo_party_location.",".$txt_bill_date.",".$cbo_party_name.",'".$bill_process_id."',".$cbo_currency_id.",".$txt_exchange_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="company_id*location_id*party_location_id*bill_date*party_id*party_source*currency*exchange_rate*updated_by*update_date";
			$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_party_location."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_within_group."*".$cbo_currency_id."*".$txt_exchange_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		//----------------------------------------------------------------------
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no,order_id,packing_qnty, delivery_qty, rate, amount,domestic_amount, remarks, currency_id, process_id,batch_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*packing_qnty*delivery_qty*rate*amount*domestic_amount*remarks*currency_id*batch_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$process_id=358;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			
			
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$orderid="ordernoid_".$i;
			$orderno="orderno_".$i;
			$txtJobNo="txtJobNo_".$i;
			$txtBuyPO="txtBuyPO_".$i;
			$txtBuyStyle="txtBuyStyle_".$i;
			$txtBatchNo="txtBatchNo_".$i;
			$textFebricDesc="textFebricDesc_".$i;
			$textProcess="textProcess_".$i;
			$txtDia="txtDia_".$i;
			$number_roll="txtNoRoll_".$i;
			$quantity="deliveryqnty_".$i;
			$rate="txtrate_".$i;
			$amount="amount_".$i;
			$remarks="remarksvalue_".$i;
			$curanci="cboCurrency_".$i;
			$remarksname="remarks_".$i;
			$txtBatchid="txtBatchid_".$i;
			$updateid_dtls="updateiddtls_".$i;
			
			$domistic_amount=str_replace("'",'',$$amount)*str_replace("'",'',$txt_exchange_rate);
			
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if($$amount!="")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$number_roll.",".$$quantity.",".$$rate.",".$$amount.",".$domistic_amount.",".$$remarks.",".$$curanci.",'".$process_id."',".$$txtBatchid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id1=$id1+1;
					$add_comma++;
				}
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$domistic_amount."*".$$remarks."*".$$curanci."*".$$txtBatchid."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
			
		}
		$flag=1;
		if(str_replace("'",'',$update_id)=="")
		{
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die; 
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;disconnect($con); die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID2=execute_query(bulk_update_sql_statement( "subcon_production_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		//if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID.'='.$rID1.'='.$flag; disconnect($con); die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
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
			disconnect($con); die;
		}
		
		
		$field_array="company_id*location_id*party_location_id*bill_date*party_id*party_source*currency*exchange_rate*updated_by*update_date";
		$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_party_location."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_within_group."*".$cbo_currency_id."*".$txt_exchange_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		//$field_array="bill_date*updated_by*update_date";
		//$data_array="".$txt_bill_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//$dtls_update_id_array=array();
		
		$sql_dtls="Select id,delivery_id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row){
			$dtls_update_id_array[]=$row[csf('id')];
			$dtls_update_delivery_id_array[$row[csf('id')]]=$row[csf('delivery_id')];
		}
		 
		$return_no=str_replace("'",'',$txt_bill_no);
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
	
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id,packing_qnty, delivery_qty, rate, amount,domestic_amount, remarks, currency_id, process_id,batch_id,inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*packing_qnty*delivery_qty*rate*amount*domestic_amount*remarks*currency_id*batch_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$process_id=358;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$orderid="ordernoid_".$i;
			$orderno="orderno_".$i;
			$txtJobNo="txtJobNo_".$i;
			$txtBuyPO="txtBuyPO_".$i;
			$txtBuyStyle="txtBuyStyle_".$i;
			$txtBatchNo="txtBatchNo_".$i;
			$textFebricDesc="textFebricDesc_".$i;
			$textProcess="textProcess_".$i;
			$txtDia="txtDia_".$i;
			$number_roll="txtNoRoll_".$i;
			$quantity="deliveryqnty_".$i;
			$rate="txtrate_".$i;
			$amount="amount_".$i;
			$remarks="remarksvalue_".$i;
			$curanci="cboCurrency_".$i;
			$remarksname="remarks_".$i;
			$txtBatchid="txtBatchid_".$i;
			$updateid_dtls="updateiddtls_".$i;
			
			//echo $up_id=str_replace("'",'',$$updateid_dtls);
			
			$domistic_amount=str_replace("'",'',$$amount)*str_replace("'",'',$txt_exchange_rate);
			//echo "10**".$domistic_amount; disconnect($con); die;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$number_roll.",".$$quantity.",".$$rate.",".$$amount.",".$domistic_amount.",".$$remarks.",".$$curanci.",'".$process_id."',".$$txtBatchid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$domistic_amount."*".$$remarks."*".$$curanci."*".$$txtBatchid."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
				
			}
			
		}
		
		
		//echo $distance_delete_id; disconnect($con); die;
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ); disconnect($con); die;
		
		$flag=1;
		if($update_id!="")
		{
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			
			$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID7=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID7==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		
		//$id_arr
		
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			
			$new_delete_id=implode(",",array_diff($dtls_update_id_array,$id_arr));
			$all_delt_id=explode(",",$new_delete_id);
			//echo "10**"."mahbub<pre>";
			//print_r($all_delt_id);
			$data_array_status_up=array();
			//$id_delete_arr = array();
			foreach($all_delt_id as $val)
			{
				if($val != '')
				{
					$id_delete_arr[]=$val;
					$data_array_status_up[$val] =explode("*",("0*1"));
					$all_delver_id[]=$dtls_update_delivery_id_array[$val];
				}
			}
			
			if(!empty($id_delete_arr))
			{
				$field_array_status_up="status_active*is_deleted";
				$rID3=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_status_up,$data_array_status_up,$id_delete_arr ));
				if($rID3==1) $flag=1; else $flag=0;
			}
			
			
			if(!empty($id_arr_delivery))
			{
				$rID2=execute_query(bulk_update_sql_statement( "subcon_production_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
			
			$data_array_bill_status_up=array();
			foreach($all_delver_id as $val)
			{
				if($val != '')
				{
					$id_delver_bill_arr[]=$val;
					$data_array_bill_status_up[$val] =explode("*",("0"));
				}
			}
			
			if(!empty($id_delver_bill_arr))
			{
				
				//echo "10**".bulk_update_sql_statement("subcon_production_dtls", "id",$field_array_delivery,$data_array_bill_status_up,$id_delver_bill_arr ); disconnect($con); die;
				$rID6=execute_query(bulk_update_sql_statement("subcon_production_dtls", "id",$field_array_delivery,$data_array_bill_status_up,$id_delver_bill_arr ));
				if($rID6==1 && $flag==1) $flag=1; else $flag=0;
			}
		
	//echo "10**".$rID.'-'.$rID1.'-'.$rID2.'-'.$rID3.'-'.$rID6.'-'.$rID7.'-'.$flag; disconnect($con); die;
				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
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
	
}
if($action=="aop_bill_issue_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$party_adress_arr = return_library_array("select id, address_1 from lib_buyer", "id", "address_1");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	
	$sql_mst="select id, prefix_no_num, bill_no, location_id, bill_date, party_id, party_source, process_id, party_location_id, remarks,exchange_rate,currency from subcon_inbound_bill_mst where process_id=358 and status_active=1 and is_deleted=0 and id='$data[1]'";
	$dataArray = sql_select($sql_mst);
	
	$party_name=""; $party_address=""; $party_address="";
	if( $data[3]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
	}
	else if($data[3]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
	}
	
	?>
    <div style="width:1200px;">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="100" align="right"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="1200" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
         <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Bill No:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
            	<td width="130"><strong>Party Name: </strong></td>
                <td width="175"><? echo $party_name; ?></td>
                <td width="130"><strong>Party Location:</strong></td>
                <td><? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
            </tr> 
            <tr>
            	<td><strong>Bill Date: </strong></td>
                <td><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
            	<td><strong>Currency: </strong></td>
                <td><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
                <td><strong>Exchange Rate: </strong></td>
                <td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
            </tr>
            <? if($data[3]==2){ ?>
	            <tr>
	            	<td><strong>Party Address: </strong></td>
	                <td colspan="5"><? echo $party_adress_arr[$dataArray[0][csf('party_id')]];//party_address; ?></td>
	            </tr>
        	<? }?>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="100">Internal Ref. No.</th>
                    <th width="60">AOP Ref.</th>
                    <th width="60">Delivery Date</th>
                    <th width="130">Delivery Challan</th>
                    <th width="90">Cust. Style</th>
                    <th width="80">Cust. Buyer</th>
                    <th width="150">Process Name</th>
                    <th width="150">Const. Compo.</th>
                    <th width="80">Color</th>
                    <th width="60">Bill Qty</th>
                    <th width="50">Rate</th>
                    <th width="60">Amount</th>
                    <th width="70" style="display:none">Currency</th>
                    <th>Remarks</th>
                </thead>
				<?
				
				$buyer_po_arr=array();
				$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
				$po_sql_res=sql_select($po_sql);
				foreach ($po_sql_res as $row)
				{
					$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
					$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
				}
				unset($po_sql_res);
				
			
				$delivery_qty_sql=sql_select("SELECT b.id,b.process,a.product_no,a.product_date from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.entry_form=307  and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
				$process_arr=array();
				foreach($delivery_qty_sql as $row)
				{
					$process_arr[$row[csf('id')]]['process'] 		= $row[csf('process')];
					$process_arr[$row[csf('id')]]['product_no'] 	= $row[csf('product_no')];
					$process_arr[$row[csf('id')]]['product_date'] 	= change_date_format($row[csf('product_date')]);
				}
				//echo "<pre>";
				//print_r($process_arr);
				
				$mst_id = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];

				$sql= "select  a.id, a.subcon_job,a.aop_reference,d.currency_id, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer,b.aop_color_id,b.construction, b.composition,d.delivery_qty, d.rate, d.amount, d.remarks, d.delivery_id, d.delivery_date,d.batch_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_inbound_bill_dtls d where a.subcon_job=b.job_no_mst and b.id=d.order_id and a.entry_form=278 and d.process_id=358 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id='$data[0]' and d.mst_id='$data[1]' order by a.id ASC";
				//echo $sql; die;
				$sql_res=sql_select($sql);
				
 				$i=1; $grand_tot_qty=0; $k=1;

				foreach ($sql_res as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if( $dataArray[0][csf('party_source')]==1)
					{
						$buyer_buyer=$buyer_library[$row[csf('buyer_buyer')]];
						
					}elseif( $dataArray[0][csf('party_source')]==2)
					{
						$buyer_buyer=$row[csf('buyer_buyer')];
					}
					$internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
					
					/*$process=explode(",",$process_arr[$row[csf('delivery_id')]]['process']);
					echo "<pre>";
					print_r($process);
					
					$product_no=$process_arr[$row[csf('delivery_id')]]['product_no'];
					$process_arr='';
					foreach($process as $val)
					{
						if($process_arr=='') $process_arr=$conversion_cost_head_array[$val]; else $process_arr.=", ".$conversion_cost_head_array[$val];
						
					}*/
					$product_no_val=$process_arr[$row[csf('delivery_id')]]['product_no'];
					$product_date_val=$process_arr[$row[csf('delivery_id')]]['product_date'];
					$product_id=explode(",", $product_no_val);
					$product_date_val=explode(",", $product_date_val);
					$product_no=""; $product_name=""; $product_date="";
					foreach($product_id as $product_val){
						if ($product_val=="") $product_name=$product_val; else $product_name.=$product_val;
					}
					foreach($product_date_val as $pDate){
						if ($pDate=="") $product_date=$pDate; else $product_date.=$pDate;
					}
					$product_no=implode(",",array_unique(explode(',',$product_name)));
					$product_date=implode(",",array_unique(explode(',',$product_date)));
					
					
					$process_id_val=$process_arr[$row[csf('delivery_id')]]['process'];
					$process_id=explode(",", $process_id_val);
					$process_name="";
					foreach($process_id as $process_val)
					{
						if ($process_val=="") $process_name=$conversion_cost_head_array[$process_val]; else $process_name.=$conversion_cost_head_array[$process_val];
					}
					$process_name=implode(",",array_unique(explode(',',$process_name)));
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td style="word-break:break-all"><? echo $internalRef; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf("aop_reference")];//change_date_format($row[csf("delivery_date")]); ?></td>
                        <td style="word-break:break-all"><? echo $product_date; ?></td>
                        <td style="word-break:break-all"><? echo $product_no; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf('buyer_style_ref')]; ?></td>
                        <td style="word-break:break-all"><? echo $buyer_buyer; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $process_name; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $row[csf('construction')]." ".$row[csf('composition')]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $color_arr[$row[csf('aop_color_id')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('delivery_qty')], 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('rate')], 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('amount')], 2, '.', ''); ?>&nbsp;</td>
                         <td style="word-break:break-all;display:none" align="center"><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                    </tr>
					<?
					$i++;
					$sub_total_qty+=$row[csf('delivery_qty')];
					$grand_tot_qty+=$row[csf('delivery_qty')];
					
					$sub_total_amt+=$row[csf('amount')];
					$grand_tot_amt+=$row[csf('amount')];
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="10" align="right"><b>Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
                    <td>&nbsp;</td>
                </tr>
				<tr>
            	<td colspan="160" align="left"><b>In Word: <? 
					//echo var_dump($sub_total_amt);
					echo number_to_words(number_format($sub_total_amt,2),$uom_unit,$uom_gm); ?></b></td>
        		</tr>
            </table>
            
            <br>
			<? echo signature_table(190, $com_id, "1130px"); ?>
        </div>
    </div>
	<?
	exit();
}
?>