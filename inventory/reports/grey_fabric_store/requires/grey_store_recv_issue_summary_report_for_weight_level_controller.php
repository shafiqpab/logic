<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data and a.status_active=1 and a.is_deleted=0 and b.category_type=13 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond  $company_credential_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 0, "","");
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 0, "" );
	}
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	// echo $cbo_is_sales_type;
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:820px;margin-left:4px;">
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Is Sales</th>
							<th>Year</th>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							</th> 
						</thead>
						<tr class="general">
							<td align="center">
								<? echo create_drop_down( "cbo_is_sales", 80, $yes_no,"",0, "--Select--", $cbo_is_sales_type,"",1 );?>
							</td>
							<td align="center">
								<? echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
							</td>   
							<td align="center">	
								<?
								if ($cbo_is_sales_type==1) 
								{
									$search_by_arr=array(1=>"FSO No",2=>"Booking No",3=>"Style Ref.");
								}
								else
								{
									$search_by_arr=array(1=>"Order No",2=>"Booking No",3=>"Style Ref.",4=>"Job No.");
								}
								
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>                 
							<td align="center">				
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
							</td> 						
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_is_sales').value, 'create_order_no_search_list_view', 'search_div', 'grey_store_recv_issue_summary_report_for_weight_level_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>   
				</form>
			</fieldset>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$year =$data[3];
	$cbo_is_sales =$data[4];
	
	$search_field_cond='';
	if ($cbo_is_sales==1) 
	{
		if($search_string!="")
		{
			if($search_by==1) $search_field_cond=" and a.JOB_NO_PREFIX_NUM='$search_string' ";
			else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."'";
			else $search_field_cond=" and a.style_ref_no like '%".$search_string."'";
		}
	}
	else
	{
		if($search_string!="")
		{
			if($search_by==1) $search_field_cond=" and b.po_number='$search_string' ";
			else if($search_by==2) $search_field_cond=" and c.booking_no like '%".$search_string."'";
			else if($search_by==3) $search_field_cond=" and a.style_ref_no like '%".$search_string."'";
			else $search_field_cond=" and a.job_no like '".$search_string."%'";
		}
	}	

	if($db_type==0)
    {
        if($year!=0) $year_search_cond=" and year(a.insert_date)=$year"; else $year_search_cond="";
        $year_field="YEAR(a.insert_date) as year"; 
    }
    else if($db_type==2)
    {
        if($year!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year"; else $year_search_cond="";
        $year_field="to_char(a.insert_date,'YYYY') as year";
    }

    if ($cbo_is_sales==1) 
    {
    	$sql = "SELECT a.id as order_id, $year_field, a.job_no_prefix_num, a.job_no as po_number, a.within_group, a.sales_booking_no as booking_no, a.booking_date, a.customer_buyer as buyer_name, a.style_ref_no from fabric_sales_order_mst a
    	where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond 
    	order by a.id DESC"; 
    }
    else
    {
    	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field, c.booking_no, c.booking_type, b.id as order_id, b.po_number
	    from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c
	    where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_field_cond $year_search_cond
	    group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.booking_type, b.po_number, b.id order by a.job_no desc";
    }
	
    // echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90"><?echo ($cbo_is_sales==1) ? 'FSO NO' : 'Order No' ; ?> </th>
			<th width="60">Year</th>
			<th width="70">Buyer</th>               
			<th width="120">Booking No</th>
			<th width="110">Style Ref.</th>
			<?if ($cbo_is_sales==2) { echo '<th>Job No.</th>'; } ?>
		</thead>
	</table>
	<div style="width:700px; max-height:300px; overflow-y:scroll; float:left;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search" align="left">  
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$booking_data =$row[csf('order_id')]."**".$row[csf('po_number')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('po_number')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
					<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<?if ($cbo_is_sales==2) { echo "<td><p>".$row[csf('job_no')]."</p></td>"; } ?>
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

if($action=="generate_report_receive")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_knitting_company=str_replace("'","",$cbo_knitting_company);
	$cbo_is_sales=str_replace("'","",$cbo_is_sales);

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	$str_cond="";
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and e.po_breakdown_id =$txt_order_id";
	if($cbo_knitting_source>0) $str_cond .=" and a.knitting_source=$cbo_knitting_source";
	if($cbo_knitting_company>0) $str_cond .=" and a.knitting_company=$cbo_knitting_company";

	if($cbo_buyer_name>0)
	{
		$str_cond .= " and a.buyer_id = $cbo_buyer_name ";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		if($cbo_based_on == 1) 
		{
			$date_cond_rcv = " and a.receive_date between '$txt_date_from' and '$txt_date_to' ";
			$date_cond_trans = " and a.transfer_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_rcv="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond_rcv="";
			}else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_rcv="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond_rcv="";
			}
			$date_cond_trans=$date_cond_rcv;
		}
	}

	$user_array=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1",'id','supplier_name');
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$color_array=return_library_array( "select id,color_name from lib_color where status_active=1 $allColorCond", "id", "color_name");
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prod_id where userid=$user_id");
    oci_commit($con);
	// ========================= Receive query =============================
	if ($cbo_is_sales==1) // is sales
	{
		$receive_sql="SELECT a.id,a.receive_date, a.recv_number,a.booking_id as production_id, a.booking_no, a.receive_basis, a.location_id, a.knitting_source, a.knitting_company, a.challan_no, a.buyer_id, a.inserted_by, a.insert_date, a.booking_without_order,
		b.yarn_count, b.yarn_lot,b.brand_id, b.body_part_id, b.color_id, b.width, b.gsm, b.stitch_length, b.machine_dia, a.remarks , b.order_id, e.po_breakdown_id as po_id, c.cons_quantity, b.febric_description_id as deter_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c, ORDER_WISE_PRO_DETAILS e
		WHERE a.id=b.mst_id and b.trans_id = c.id and c.id=e.TRANS_ID and e.entry_form in(2) and e.IS_SALES=1 and e.trans_type=1 and e.status_active=1 and e.is_deleted=0 and a.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 and a.receive_basis in(2) and c.transaction_type=1 and b.trans_id<>0 $date_cond_rcv $str_cond order by a.recv_number asc";
	}
	else
	{
		$receive_sql="SELECT a.id,a.receive_date, a.recv_number,a.booking_id as production_id, a.booking_no, a.receive_basis, a.location_id, a.knitting_source, a.knitting_company, a.challan_no, a.buyer_id, a.inserted_by, a.insert_date, a.booking_without_order,
		b.yarn_count, b.yarn_lot,b.brand_id, b.body_part_id, b.color_id, b.width, b.gsm, b.stitch_length, b.machine_dia, a.remarks , b.order_id, e.po_breakdown_id as po_id, c.cons_quantity, b.febric_description_id as deter_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b , inv_transaction c left join ORDER_WISE_PRO_DETAILS e  on c.id=e.TRANS_ID and e.entry_form in(22) and e.trans_type=1 and e.status_active=1 and e.is_deleted=0
		WHERE a.id=b.mst_id and b.trans_id = c.id and a.entry_form in(22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category =13 and a.receive_basis in(9) and c.transaction_type=1 and b.trans_id<>0 $date_cond_rcv $str_cond order by a.recv_number asc";
	}	
	//and  a.recv_number in ('FAL-KNGFR-22-00098','FAL-KNGFR-22-00096')
	// echo $receive_sql;
	$receive_sql_result=sql_select($receive_sql);
	// ============== Receive data array ==================================
	$data_array = array();
	foreach ($receive_sql_result  as $val) 
	{
		$knitting_party = ($val[csf("knitting_source")]==1) ? $company_array[$val[csf("knitting_company")]] : $supplier_arr[$val[csf("knitting_company")]] ;

		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["production_id"].=$val[csf("production_id")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["knitting_source"]=$val[csf("knitting_source")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["party"]=$knitting_party;
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["challan_no"]=$val[csf("challan_no")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["buyer_id"]=$val[csf("buyer_id")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["po_id"].=$val[csf("po_id")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["yarn_count"].=$val[csf("yarn_count")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["yarn_lot"].=$val[csf("yarn_lot")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["brand_id"].=$val[csf("brand_id")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["body_part_id"].=$val[csf("body_part_id")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["color_id"].=$val[csf("color_id")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["dia"].=$val[csf("width")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["gsm"].=$val[csf("gsm")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["stitch_length"].=$val[csf("stitch_length")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["machine_dia"].=$val[csf("machine_dia")].',';
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["remarks"]=$val[csf("remarks")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["inserted_by"]=$val[csf("inserted_by")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["insert_date"]=$val[csf("insert_date")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["recv_qty"] +=  $val[csf("cons_quantity")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["recv_id"] =  $val[csf("id")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["booking_no"] =  $val[csf("booking_no")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["receive_basis"] =  $val[csf("receive_basis")];
		$recv_data_array[$val[csf("receive_date")]][$val[csf("recv_number")]][$val[csf("deter_id")]]["location_id"] =  $val[csf("location_id")];


		if ($cbo_is_sales==2) // without FSO
		{
			if( $production_id_check[$val[csf('production_id')]] == "" )
	        {
	            $production_id_check[$val[csf('production_id')]]=$val[csf('production_id')];
	            $production_id = $val[csf('production_id')];
	            // echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,$production_id)";
	            $r_id2=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$production_id)");
	        }
	    }

        if ($val[csf('booking_without_order')]==0) 
        {
        	if ($po_id_check[$val[csf('po_id')]] == "")
	        {
	            $po_id_check[$val[csf('po_id')]]=$val[csf('po_id')];
	            $po_id = $val[csf('po_id')];
	            // echo "insert into tmp_po_id (userid, po_id) values ($user_id,$po_id)";
	            $r_id2=execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)");
	        }
        }
        $po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
	}
	oci_commit($con);
	
	// =================== Order Information =============================
	if ($cbo_is_sales==1) // is sales
	{
		if (count($po_id_arr) > 0) 
		{
			$po_sql="SELECT b.id, b.job_no, b.sales_booking_no, b.style_ref_no, c.booking_no, d.booking_type, d.is_short, d.short_booking_type, e.job_no_mst, e.po_number, e.grouping 
			from tmp_po_id a, fabric_sales_order_mst b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e
			where a.po_id=b.id and b.booking_id=c.BOOKING_MST_ID and c.BOOKING_MST_ID=d.id and c.po_break_down_id=e.id and c.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id
			union all
			select b.id, b.job_no, b.sales_booking_no, b.style_ref_no, c.booking_no, c.booking_type, null as is_short, null as short_booking_type, null as job_no_mst, null as po_number, null as grouping
			from tmp_po_id a, fabric_sales_order_mst b, wo_non_ord_samp_booking_mst c
			where a.po_id=b.id and b.booking_id=c.ID and c.booking_type in(4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id";
			// echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
		    foreach ($po_sql_result as $val)
		    {
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            else
	            {
	            	$booking_type_arr[$val[csf("id")]]="Sample Without Order";
	            }
	            $po_name_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no_mst")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
		    }
		}
	}
	else
	{
		if (count($po_id_arr) > 0) 
		{
			$order_data_arr =  sql_select("SELECT b.id, b.po_number, c.job_no, c.style_ref_no, e.booking_no, e.booking_type, e.is_short, e.short_booking_type 
			from tmp_po_id a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d , wo_booking_mst e
			where a.po_id=b.id and b.job_id=c.id and b.id=d.po_break_down_id and d.booking_mst_id=e.id and c.company_name=$cbo_company_name and a.user_id=$user_id and e.booking_type in(1,4)");
		    foreach ($order_data_arr as $val)
		    {
	            $short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            $po_name_arr[$val[csf("id")]]=$val[csf("po_number")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
		    }
		}
		// =================== Order Information =============================

		// =================== Knitting Production Information ===============
	
		$production_sql_data=sql_select("SELECT b.id, b.receive_basis, b.booking_id, b.booking_no, b.booking_without_order
		from tmp_prod_id a, inv_receive_master b
		where a.prod_id=b.id and b.entry_form=2 and b.item_category=13 and a.userid=$user_id");
		foreach ($production_sql_data as $val)
		{
			if ($val[csf('receive_basis')]==2) 
			{
				$program_arr[$val[csf('id')]]=$val[csf('booking_id')];
			}
			if ($val[csf('receive_basis')]==1 && $val[csf('booking_without_order')]==1) 
			{
				$smn_booking_no_arr[$val[csf('id')]]=$val[csf('booking_no')];
			}
			$receive_basis_arr[$val[csf('id')]]=$val[csf('receive_basis')];
		}
	}
	// =================== Knitting Production Information ================

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prod_id where userid=$user_id");
    oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:3200px" id="main_body">
		<table width="3200" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary</td>
			</tr>
			<tr style="border:none;">
				<td colspan="20" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="3070" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Receive. Date</th>
					<th width="120">Receive. Ref.</th>
					<th width="110">Receive Challan</th>
					<th width="100">Job No</th>
					<th width="80">Buyer</th>
					<th width="120">Style No</th>
					<th width="120"><?echo ($cbo_is_sales==1) ? 'FSO NO' : 'Order No' ; ?></th>
					<th width="120">Fabric Booking</th>
					<th width="100">Booking Type</th>
					<th width="70">Program No</th>
					<th width="80">Knitting Source</th>
					<th width="100">Knitting Party</th>
					<th width="80">Production Basis</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="80">S. Length</th>
					<th width="50">MC.Dia</th>
					<th width="80">Receive Qty</th>
					<th width="80">User</th>
					<th width="80">Insert Date & Time</th>
					<th width="150">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:3090px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; $total_recv_qty=0;
					if(!empty($recv_data_array))
					{
						foreach($recv_data_array as $rcv_date => $rcv_data_arr)
						{
							foreach($rcv_data_arr as $rcv_number => $rcv_number_arr)
							{
								foreach($rcv_number_arr as $detar_id => $row)
								{
									$po_ids_arr = explode(",", $row["po_id"]);
									$job_no='';$style_ref_no='';$po_name='';$booking_no='';$booking_type='';
									foreach ($po_ids_arr as $poId) 
									{
										$job_no .= $job_no_arr[$poId].",";
										$style_ref_no .= $style_ref_no_arr[$poId].",";
										$po_name .= $po_name_arr[$poId].",";
										$booking_no .= $booking_no_arr[$poId].",";
										$booking_type .= $booking_type_arr[$poId].",";
									}
									$job_no =implode(",",array_unique(explode(",",chop($job_no,","))));
									$style_ref_no =implode(",",array_unique(explode(",",chop($style_ref_no,","))));
									$po_name =implode(",",array_unique(explode(",",chop($po_name,","))));
									$booking_no =implode(",",array_unique(explode(",",chop($booking_no,","))));
									$booking_type =implode(",",array_unique(explode(",",chop($booking_type,","))));

									$yarn_lot="";
									$yarn_lot_arr = explode(",", $row["yarn_lot"]);
									foreach ($yarn_lot_arr as $value) 
									{
										$yarn_lot .= $value.",";
									}
									$yarn_lot =implode(",",array_unique(explode(",",chop($yarn_lot,","))));

									$brand_name="";
									$brand_id_arr = explode(",", $row["brand_id"]);
									foreach ($brand_id_arr as $value) 
									{
										$brand_name .= $brand_arr[$value].",";
									}
									$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));

									$body_parts="";
									$body_part_id_arr = explode(",", $row["body_part_id"]);
									foreach ($body_part_id_arr as $value) 
									{
										$body_parts .= $body_part[$value].",";
									}
									$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));

									$dia =implode(",",array_unique(explode(",",chop($row["dia"],","))));
									$gsm =implode(",",array_unique(explode(",",chop($row["gsm"],","))));
									$stitch_length =implode(",",array_unique(explode(",",chop($row["stitch_length"],","))));
									$machine_dia =implode(",",array_unique(explode(",",chop($row["machine_dia"],","))));
									
									$program_no="";$production_recv_basis="";$smn_booking_no="";
									if ($cbo_is_sales==1) 
									{
										$program_no =$row["booking_no"];
										$production_recv_basis =$receive_basis[$row["receive_basis"]];
										$smn_booking_no =0;
									}
									else
									{
										$production_id_arr = explode(",", $row["production_id"]);
										foreach ($production_id_arr as $value) 
										{
											$program_no .= $program_arr[$value].",";
											$production_recv_basis .= $receive_basis[$receive_basis_arr[$value]].",";
											$smn_booking_no .= $smn_booking_no_arr[$value].",";
										}
										$program_no =implode(",",array_unique(explode(",",chop($program_no,","))));
										$production_recv_basis =implode(",",array_unique(explode(",",chop($production_recv_basis,","))));
										$smn_booking_no =implode(",",array_unique(explode(",",chop($smn_booking_no,","))));
									}
									//$report_title="Knit Grey Fabric Receive Report";
									//$str_data=$cbo_company_name.'*68470*'.$report_title.'*'.$row['txt_booking_no'].'*'.$row['cbo_receive_basis'].'*'.$row['cbo_location'];

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i;?></td>
										<td width="70"><? echo change_date_format($rcv_date);?></td>
                                        <td width="120"><? echo "<a href='##' onclick=\"generate_recv_print_report(" . $cbo_company_name . ",'" . $row["recv_id"] . "','" . $row["booking_no"] . "','" . $row["receive_basis"] . "','" . $row["location_id"] . "','" . $fReportId . "' )\">$rcv_number</a>"; ?></td>
										<td width="110"><? echo $row["challan_no"];?></td>
										<td width="100"><p><? echo $job_no;?></p></td>
										<td width="80"><p><? echo $buyer_arr[$row["buyer_id"]];?></p></td>
										<td width="120"><p><? echo $style_ref_no;?></p></td>
										<td width="120"><p><? echo $po_name;?></p></td>
										<td width="120" title="<?=$row["production_id"];?>"><p>
											<? if ($booking_no!='') 
											{
												echo $booking_no;
											}
											else
											{
												echo $smn_booking_no;
											}
											?></p>
										</td>
		                                <td width="100">
		                                	<? 
			                                if ($booking_type!="") 
			                                {
			                                	echo $booking_type;
			                                }
			                                else
		                                	{
		                                		echo "Sample Without Order";
		                                	}
			                                ?>
			                            </td>
		                                <td width="70"><p><? echo $program_no;?></p></td>
		                                <td width="80"><? echo $knitting_source[$row["knitting_source"]];?></td>
		                                <td width="100"><? echo $row["party"];?></td>
		                                <td width="80"><? echo $production_recv_basis;?></td>

		                                <td width="100" align="center"><p>
		                                	<? 
		                                	$count_name="";
		                                	foreach (explode(",", $row["yarn_count"]) as  $count) 
		                                	{
		                                		$count_name .= $count_array[$count].",";
		                                	}
		                                	echo chop($count_name,",");
		                                	?></p>
		                                </td>
		                                <td width="100"><p><? echo $yarn_lot;?></p></td>
		                                <td width="100"><p><? echo $brand_name;?></p></td>
		                                <td width="100"><p><? echo $body_parts;?></p></td>
		                                <td width="100"><p><? echo $constructtion_arr[$detar_id];?></p></td>
		                                <td width="140"><p><? echo $composition_arr[$detar_id];?></p></td>
		                                <td width="80">
	                                		<p><? 
	                                		$color_names="";
	                                		foreach (explode(",", $row["color_id"]) as $key => $color) {
	                                			$color_names .= $color_array[$color].",";
	                                		}
	                                		echo chop($color_names,",");
	                                		?>
	                                		</p>
		                                </td>
		                                <td width="50"><? echo $dia;?></td>
		                                <td width="50"><? echo $gsm;?></td>
		                                <td width="80"><p><? echo $stitch_length;?></p></td>
		                                <td width="50"><? echo $machine_dia;?></td>
		                                <td width="80" align="right"><? echo number_format($row["recv_qty"],2,'.',''); ?></td>
		                                <td width="80" align="center"><? echo $user_array[$row["inserted_by"]];?></td>
		                                <td width="80"><p><? echo date("d-M-Y",strtotime($row["insert_date"]))."&\n " .date("h:i",strtotime($row["insert_date"]));?></p></td>
		                                <td width="150"><p><? echo $row["remarks"];?></p></td>

									</tr>
									<?
									$total_recv_qty += $row["recv_qty"];
									$i++;
								}
							}
						}
					}
					else
					{
						echo "No Data Found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="100"></th>					
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="50">Total : </th>
					<th width="80" align="right"><? echo number_format($total_recv_qty,2,'.','');?></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="150"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}

if($action=="generate_report_issue")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$cbo_store_name = str_replace("'","",$cbo_store_name);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$txt_order = str_replace("'","",$txt_order);
	$txt_order_id = str_replace("'","",$txt_order_id);
	$cbo_based_on = str_replace("'","",$cbo_based_on);
	$cbo_knitting_source = str_replace("'","",$cbo_knitting_source);
	$cbo_knitting_company = str_replace("'","",$cbo_knitting_company);
	$cbo_is_sales = str_replace("'","",$cbo_is_sales);

	if($db_type==0)
	{
		$txt_date_from = change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to = change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from = change_date_format($txt_date_from,"","",1);
		$txt_date_to = change_date_format($txt_date_to,"","",1);
	}

	$str_cond="";
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and b.store_name=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and e.po_breakdown_id =$txt_order_id";
	if($cbo_knitting_source>0) $str_cond .=" and a.knit_dye_source=$cbo_knitting_source";
	if($cbo_knitting_company>0) $str_cond .=" and a.knit_dye_company=$cbo_knitting_company";


	if($cbo_buyer_name>0)
	{
		$str_cond .= " and a.buyer_id = $cbo_buyer_name ";
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_based_on == 1) 
		{
			$date_cond_iss = " and a.issue_date between '$txt_date_from' and '$txt_date_to' ";
			$date_cond_trans = " and a.transfer_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond_iss="";
			}
			else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond_iss="";
			}
			$date_cond_trans=$date_cond_iss;
		}
	}
	
	$user_array=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(21,24,25,26) and a.status_active=1",'id','supplier_name');
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$color_array=return_library_array( "select id,color_name from lib_color where status_active=1 $allColorCond", "id", "color_name");
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);
	// ========================= Issue query =============================
	if ($cbo_is_sales==1) // is sales
	{
		$issue_sql=sql_select("SELECT a.id, a.issue_date, a.issue_number, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.issue_basis, a.issue_purpose, a.buyer_id, a.booking_id, a.booking_no, a.remarks, a.inserted_by,a.insert_date, b.program_no, b.stitch_length, b.store_name as store_id, b.issue_qnty, e.quantity, b.body_part_id, b.color_id as color_id, e.po_breakdown_id as po_id, d.detarmination_id as deter_id, b.yarn_lot as yarn_lot, b.yarn_count, d.brand as brand_id, d.dia_width, d.gsm 
		from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details e, product_details_master d 
		where a.id = b.mst_id and b.prod_id = d.id and b.id = e.dtls_id and e.prod_id=d.id and e.entry_form=16 and e.trans_type=2 and e.IS_SALES=1 and e.status_active=1 and e.is_deleted=0  and a.entry_form = 16 and d.item_category_id=13 and a.issue_basis in(1,2,3) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $date_cond_iss $str_cond order by a.issue_number asc");
	}
	else
	{
		$issue_sql=sql_select("SELECT a.id, a.issue_date, a.issue_number, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.issue_basis, a.issue_purpose, a.buyer_id, a.booking_id, a.booking_no, a.remarks, a.inserted_by,a.insert_date, b.program_no, b.stitch_length, b.store_name as store_id, b.issue_qnty, e.quantity, b.body_part_id, b.color_id as color_id, e.po_breakdown_id as po_id, d.detarmination_id as deter_id, b.yarn_lot as yarn_lot, b.yarn_count, d.brand as brand_id, d.dia_width, d.gsm 
		from inv_issue_master a, inv_grey_fabric_issue_dtls b
		left join order_wise_pro_details e  on b.id = e.dtls_id and e.entry_form=16 and e.trans_type=2 and e.status_active=1 and e.is_deleted=0, product_details_master d
		where a.id = b.mst_id and b.prod_id = d.id and a.entry_form = 16 and d.item_category_id=13 and a.issue_basis in(1,2,3) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $date_cond_iss $str_cond order by a.issue_number asc");
	}
	// ============== Issue data array ==================================
	$issue_data_array = array();
	foreach ($issue_sql  as $val) 
	{
		if ($val[csf("program_no")]=="") 
		{
			$val[csf("program_no_or_booking_id")]=$val[csf("booking_id")];
		}
		else
		{
			$val[csf("program_no_or_booking_id")]=$val[csf("program_no")];
			
		}
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["program_no_or_booking_id"].=$val[csf("program_no_or_booking_id")].',';
		$dyeing_party = ($val[csf("knit_dye_source")]==1) ? $company_array[$val[csf("knit_dye_company")]] : $supplier_arr[$val[csf("knit_dye_company")]] ;

		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["program_no"].=$val[csf("program_no")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["knitting_source"]=$val[csf("knit_dye_source")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["party"]=$dyeing_party;
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["challan_no"]=$val[csf("challan_no")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["buyer_id"]=$val[csf("buyer_id")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["po_id"]=$val[csf("po_id")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["yarn_count"].=$val[csf("yarn_count")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["yarn_lot"].=$val[csf("yarn_lot")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["brand_id"].=$val[csf("brand_id")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["body_part_id"].=$val[csf("body_part_id")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["color_id"].=$val[csf("color_id")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["dia"].=$val[csf("dia_width")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["gsm"].=$val[csf("gsm")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["stitch_length"].=$val[csf("stitch_length")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["machine_dia"].=$val[csf("machine_dia")].',';
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["remarks"]=$val[csf("remarks")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["inserted_by"]=$val[csf("inserted_by")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["insert_date"]=$val[csf("insert_date")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["issue_basis"]=$val[csf("issue_basis")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["issue_purpose"]=$val[csf("issue_purpose")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["booking_no"]=$val[csf("booking_no")];
		$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["issue_id"]=$val[csf("id")];
		
		if ($val[csf("po_id")]!="") 
		{
			$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["issue_qnty"] +=  $val[csf("quantity")];
		}
		else // use for non order data
		{
			$issue_data_array[$val[csf("issue_date")]][$val[csf("issue_number")]][$val[csf("po_id")]][$val[csf("deter_id")]]["issue_qnty"] +=  $val[csf("issue_qnty")];
		}		

		if( $production_id_check[$val[csf('program_no_or_booking_id')]] == "" )
        {
            $program_no_or_booking_id_check[$val[csf('program_no_or_booking_id')]]=$val[csf('program_no_or_booking_id')];
            $program_no_or_booking_id = $val[csf('program_no_or_booking_id')];
            // echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,$program_no_or_booking_id)";
            $r_id2=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,$program_no_or_booking_id)");
        }

        if ($val[csf('po_id')]!="") 
        {
        	if ($po_id_check[$val[csf('po_id')]] == "")
	        {
	            $po_id_check[$val[csf('po_id')]]=$val[csf('po_id')];
	            $po_id = $val[csf('po_id')];
	            // echo "insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)";
	            $r_id2=execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)");
	        }
        }
        $po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
	}
	oci_commit($con);
	// echo '<pre>';print_r($issue_data_array);die;
	
	// =================== Order and booking Information =============================
	if ($cbo_is_sales==1) // is sales
	{
		if (count($po_id_arr) > 0) 
		{
			$po_sql="SELECT b.id, b.job_no, b.sales_booking_no, b.style_ref_no, c.booking_no, d.booking_type, d.is_short, d.short_booking_type, e.job_no_mst, e.po_number, e.grouping 
			from tmp_po_id a, fabric_sales_order_mst b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e
			where a.po_id=b.id and b.booking_id=c.BOOKING_MST_ID and c.BOOKING_MST_ID=d.id and c.po_break_down_id=e.id and c.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id
			union all
			select b.id, b.job_no, b.sales_booking_no, b.style_ref_no, c.booking_no, c.booking_type, null as is_short, null as short_booking_type, null as job_no_mst, null as po_number, null as grouping
			from tmp_po_id a, fabric_sales_order_mst b, wo_non_ord_samp_booking_mst c
			where a.po_id=b.id and b.booking_id=c.ID and c.booking_type in(4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id";
			// echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
		    foreach ($po_sql_result as $val)
		    {
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            else
	            {
	            	$booking_type_arr[$val[csf("id")]]="Sample Without Order";
	            }
	            $po_name_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no_mst")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
		    }
		}
	}
	else
	{
		if (count($po_id_arr) > 0) 
		{
			$order_data_arr =  sql_select("SELECT b.id, b.po_number, c.job_no, c.style_ref_no, e.booking_no, e.booking_type, e.is_short, e.short_booking_type 
			from tmp_po_id a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d , wo_booking_mst e
			where a.po_id=b.id and b.job_id=c.id and b.id=d.po_break_down_id and d.booking_mst_id=e.id and c.company_name=$cbo_company_name and a.user_id=$user_id and e.booking_type in(1,4)");
		    foreach ($order_data_arr as $val)
		    {
	            $job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
	            $short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            $po_name_arr[$val[csf("id")]]=$val[csf("po_number")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
		    }

			$prog_order_data_arr =  sql_select("SELECT b.dtls_id , e.booking_no, e.booking_type, e.is_short
			from tmp_prog_no a, PPL_PLANNING_ENTRY_PLAN_DTLS b, wo_booking_mst e
			where a.prog_no=b.dtls_id and e.BOOKING_NO=b.BOOKING_NO and a.userid=$user_id and e.booking_type in(1,4)
			union all
			SELECT b.dtls_id, e.booking_no, e.booking_type, e.is_short
			from tmp_prog_no a, PPL_PLANNING_ENTRY_PLAN_DTLS b, WO_NON_ORD_SAMP_BOOKING_MST e
			where a.prog_no=b.dtls_id and e.BOOKING_NO=b.BOOKING_NO and a.userid=$user_id and e.booking_type in(4)");
		    foreach ($prog_order_data_arr as $val)
		    {
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $prog_booking_type_arr[$val[csf("dtls_id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $prog_booking_type_arr[$val[csf("dtls_id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4)
	            {
	                $prog_booking_type_arr[$val[csf("dtls_id")]]="Sample With Order";
	            }
	            $prog_booking_no_arr[$val[csf("dtls_id")]]=$val[csf("booking_no")];
		    }
		}
	}
	// =================== Order and booking Information =============================

	// =================== Knitting Production Information ===========================
	$production_sql_data=sql_select("SELECT b.id, b.receive_basis, b.booking_id, b.booking_no, b.booking_without_order, c.brand_id, c.body_part_id, c.machine_dia
	from tmp_prog_no a, inv_receive_master b, pro_grey_prod_entry_dtls c
	where a.prog_no=b.booking_id and b.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and b.entry_form=2 and b.item_category=13 and b.receive_basis in(1,2) and a.userid=$user_id");
	foreach ($production_sql_data as $val)
	{
		if ($val[csf('receive_basis')]==2)
		{
			$receive_basis_arr[$val[csf('booking_id')]]=$val[csf('receive_basis')];
		}
		$production_brand_id_arr[$val[csf('booking_id')]]=$val[csf('brand_id')];
		$production_body_part_id_arr[$val[csf('booking_id')]]=$val[csf('body_part_id')];
		$production_machine_dia_arr[$val[csf('booking_id')]]=$val[csf('machine_dia')];
	}
	// =================== Knitting Production Information ===========================

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:3200px" id="main_body">
		<table width="3200" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary</td>
			</tr>
			<tr style="border:none;">
				<td colspan="20" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="3070" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Issue. Date</th>
					<th width="120">Issue. Ref.</th>
					<th width="110">Issue Challan</th>
					<th width="100">Job No</th>
					<th width="80">Buyer</th>
					<th width="120">Style No</th>
					<th width="120"><?echo ($cbo_is_sales==1) ? 'FSO NO' : 'Order No' ; ?></th>
					<th width="120">Fabric Booking</th>
					<th width="100">Booking Type</th>
					<th width="70">Program No</th>
					<th width="80">Dyeing Source</th>
					<th width="100">Dyeing Party</th>
					<th width="80">Production Basis</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="80">S. Length</th>
					<th width="50">MC.Dia</th>
					<th width="80">Issue Qty</th>
					<th width="80">User</th>
					<th width="80">Insert Date & Time</th>
					<th width="150">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:3090px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$print_report_format=return_field_value("format_id"," lib_report_template","template_name =$cbo_company_name  and module_id=6 and report_id=33 and is_deleted=0 and status_active=1");
				    $fReportId=explode(",",$print_report_format);
				    $fReportId=$fReportId[0];

					$i=1; $total_issue_qty=0;
					if(!empty($issue_data_array))
					{
						foreach($issue_data_array as $issue_date => $issue_data_arr)
						{
							foreach($issue_data_arr as $issue_number => $issue_number_arr)
							{
								foreach($issue_number_arr as $po_id => $po_id_arr)
								{
									foreach($po_id_arr as $detar_id => $row)
									{
										$job_no = $job_no_arr[$row["po_id"]];
										$style_ref_no = $style_ref_no_arr[$row["po_id"]];
										$po_name = $po_name_arr[$row["po_id"]];

										if ($row["issue_basis"]==3) // Knitting plan basis
										{
											$program_arr = explode(",", $row["program_no"]);
											$booking_no='';$booking_type='';
											foreach ($program_arr as $program) 
											{
												$booking_no .= $prog_booking_no_arr[$program].",";
												$booking_type .= $prog_booking_type_arr[$program].",";
											}
											$booking_no =implode(",",array_unique(explode(",",chop($booking_no,","))));
											$booking_type =implode(",",array_unique(explode(",",chop($booking_type,","))));
										}
										else
										{											
											$booking_no = $booking_no_arr[$row["po_id"]];
											$booking_type = $booking_type_arr[$row["po_id"]];
										}
										if ($cbo_is_sales==1) // is sales
										{
											$booking_no = $booking_no_arr[$row["po_id"]];
											$booking_type = $booking_type_arr[$row["po_id"]];
										}									

										$yarn_lot="";
										$yarn_lot_arr = explode(",", $row["yarn_lot"]);
										foreach ($yarn_lot_arr as $value) 
										{
											$yarn_lot .= $value.",";
										}
										$yarn_lot =implode(",",array_unique(explode(",",chop($yarn_lot,","))));

										

										$body_parts="";
										$body_part_id_arr = explode(",", $row["body_part_id"]);
										foreach ($body_part_id_arr as $value) 
										{
											$body_parts .= $body_part[$value].",";
										}
										$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));

										$dia =implode(",",array_unique(explode(",",chop($row["dia"],","))));
										$gsm =implode(",",array_unique(explode(",",chop($row["gsm"],","))));
										$stitch_length =implode(",",array_unique(explode(",",chop($row["stitch_length"],","))));
										// $machine_dia =implode(",",array_unique(explode(",",chop($row["machine_dia"],","))));
										
										/*$brand_name="";
										$brand_id_arr = explode(",", $row["brand_id"]);
										foreach ($brand_id_arr as $value) 
										{
											$brand_name .= $brand_arr[$value].",";
										}
										$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));*/

										$production_issue_basis="";$brand_name="";$body_parts="";$machine_dia="";
										$program_no_id_arr = explode(",", $row["program_no_or_booking_id"]);
										foreach ($program_no_id_arr as $value) 
										{
											$production_issue_basis .= $receive_basis[$receive_basis_arr[$value]].",";
											$brand_name .= $brand_arr[$production_brand_id_arr[$value]].",";
											$body_parts .= $body_part[$production_body_part_id_arr[$value]].",";
											$machine_dia .= $production_machine_dia_arr[$value].",";
										}
										$production_issue_basis =implode(",",array_unique(explode(",",chop($production_issue_basis,","))));
										$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));
										$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));
										$machine_dia =implode(",",array_unique(explode(",",chop($machine_dia,","))));
										$program_no =implode(",",array_unique(explode(",",chop($row["program_no"],","))));

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i;?></td>
											<td width="70"><? echo change_date_format($issue_date);?></td>
											<td width="120"><? echo "<a href='##' onclick=\"generate_print_report(" . $cbo_company_name . ",'" . $row["issue_id"] . "','" . $issue_number . "','" . $fReportId . "' )\">$issue_number</a>"; ?></td>
											<td width="110"><? echo $row["challan_no"];?></td>
											<td width="100"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_arr[$row["buyer_id"]];?></p></td>
											<td width="120"><p><? echo $style_ref_no;?></p></td>
											<td width="120" title="<?=$row["po_id"];?>"><p><? echo $po_name;?></p></td>
											<td width="120"><p>
												<? if ($row["issue_basis"]==1) // booking basis
												{
													echo $row["booking_no"];
												}
												else
												{
													echo $booking_no; // knitting plan basis
												}
												?></p>
											</td>
			                                <td width="100">
			                                	<? 
				                                if ($booking_type!="") 
				                                {
				                                	echo $booking_type;
				                                }
				                                else
			                                	{
			                                		echo "Sample Without Order";
			                                	}
				                                ?>
				                            </td>
			                                <td width="70"><p><? echo $program_no;?></p></td>
			                                <td width="80"><? echo $knitting_source[$row["knitting_source"]];?></td>
			                                <td width="100"><? echo $row["party"];?></td>
			                                <td width="80"><? 
			                                if ($production_issue_basis!="") 
			                                {
			                                 	echo $production_issue_basis;
			                                }
			                                else
			                                {
			                                	echo "Fabric Booking";
			                                }
			                                ?></td>

			                                <td width="100" align="center"><p>
			                                	<? 
			                                	$count_name="";
			                                	foreach (explode(",", $row["yarn_count"]) as  $count) 
			                                	{
			                                		$count_name .= $count_array[$count].",";
			                                	}
			                                	echo chop($count_name,",");
			                                	?></p>
			                                </td>
			                                <td width="100"><p><? echo $yarn_lot;?></p></td>
			                                <td width="100" title="<? echo $row["program_no_or_booking_id"]; ?>"><p><? echo $brand_name;?></p></td>
			                                <td width="100"><p><? echo $body_parts;?></p></td>
			                                <td width="100"><p><? echo $constructtion_arr[$detar_id];?></p></td>
			                                <td width="140"><p><? echo $composition_arr[$detar_id];?></p></td>
			                                <td width="80">
		                                		<p><? 
		                                		$color_names="";
		                                		foreach (explode(",", $row["color_id"]) as $key => $color) {
		                                			$color_names .= $color_array[$color].",";
		                                		}
		                                		echo chop($color_names,",");
		                                		?>
		                                		</p>
			                                </td>
			                                <td width="50"><? echo $dia;?></td>
			                                <td width="50"><? echo $gsm;?></td>
			                                <td width="80"><p><? echo $stitch_length;?></p></td>
			                                <td width="50"><? echo $machine_dia;?></td>
			                                <td width="80" align="right"><? echo number_format($row["issue_qnty"],2,'.',''); ?></td>
			                                <td width="80" align="center"><? echo $user_array[$row["inserted_by"]];?></td>
			                                <td width="80"><p><? echo date("d-M-Y",strtotime($row["insert_date"]))."&\n " .date("h:i",strtotime($row["insert_date"]));?></p></td>
			                                <td width="150"><p><? echo $row["remarks"];?></p></td>

										</tr>
										<?
										$total_issue_qty += $row["issue_qnty"];
										$i++;
									}
								}
							}
						}
					}
					else
					{
						echo "No Data Found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="100"></th>					
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="50">Total : </th>
					<th width="80" align="right"><? echo number_format($total_issue_qty,2,'.','');?></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="150"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}

if($action=="generate_report_transfer_in")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_is_sales=str_replace("'","",$cbo_is_sales);

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	$str_cond="";
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and a.to_order_id =$txt_order_id";


	if($cbo_buyer_name>0)
	{
		$str_cond .= " and a.buyer_id = $cbo_buyer_name ";
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_based_on == 1) 
		{
			$date_cond_iss = " and a.issue_date between '$txt_date_from' and '$txt_date_to' ";
			$date_cond_trans = " and a.transfer_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond_iss="";
			}
			else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond_iss="";
			}
			$date_cond_trans=$date_cond_iss;
		}
	}

	$user_array=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	// $booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$color_array=return_library_array( "select id,color_name from lib_color where status_active=1 $allColorCond", "id", "color_name");
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);

    if ($cbo_is_sales==1) // is sales
    {
    	$entry_form_cond=" and a.entry_form in(362)";
    }
    else
    {
    	$entry_form_cond=" and a.entry_form in(432,81,80,13)";
    }
	// ========================= Transfer In query =============================
	$trans_in_sql=sql_select("SELECT a.id, a.transfer_system_id as trans_in_number, a.transfer_date as trans_in_date, a.challan_no,a.transfer_criteria, a.to_order_id as po_id, a.remarks, a.inserted_by, a.insert_date, a.entry_form, b.to_trans_id, c.prod_id, c.transaction_type, e.detarmination_id as deter_id, b.yarn_lot, b.y_count, b.color_id , e.brand, e.dia_width, e.gsm, b.transfer_qnty, b.from_program as program_no, b.to_program, b.stitch_length
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master e 
	where a.id=b.mst_id and b.to_trans_id = c.id and a.id = c.mst_id and c.prod_id=e.id $entry_form_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and c.transaction_type=5 $date_cond_trans $str_cond order by a.transfer_system_id asc");
	// ============== Issue data array ==================================
	$trans_in_data_array = array();
	foreach ($trans_in_sql  as $val) 
	{
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["program_no"].=$val[csf("program_no")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["knitting_source"]=$val[csf("knit_dye_source")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["challan_no"]=$val[csf("challan_no")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["buyer_id"]=$val[csf("buyer_id")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["po_id"].=$val[csf("po_id")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["yarn_count"].=$val[csf("y_count")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["yarn_lot"].=$val[csf("yarn_lot")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["brand_id"].=$val[csf("brand")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["body_part_id"].=$val[csf("body_part_id")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["color_id"].=$val[csf("color_id")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["dia"].=$val[csf("dia_width")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["gsm"].=$val[csf("gsm")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["stitch_length"].=$val[csf("stitch_length")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["machine_dia"].=$val[csf("machine_dia")].',';
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["remarks"]=$val[csf("remarks")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["inserted_by"]=$val[csf("inserted_by")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["insert_date"]=$val[csf("insert_date")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["trans_in_basis"]=$val[csf("trans_in_basis")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["trans_in_purpose"]=$val[csf("trans_in_purpose")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["booking_no"]=$val[csf("booking_no")];
		$trans_in_data_array[$val[csf("trans_in_date")]][$val[csf("trans_in_number")]][$val[csf("deter_id")]]["trans_in_qnty"] +=  $val[csf("transfer_qnty")];

		if( $production_id_check[$val[csf('program_no')]] == "" )
        {
            $program_no_check[$val[csf('program_no')]]=$val[csf('program_no')];
            $program_no = $val[csf('program_no')];
            // echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,$program_no)";
            $r_id2=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,$program_no)");
        }

        if ($val[csf('entry_form')]==81 || $val[csf('entry_form')]==13 || $val[csf('entry_form')]==362)
        {
        	if ($po_id_check[$val[csf('po_id')]] == "")
	        {
	            $po_id_check[$val[csf('po_id')]]=$val[csf('po_id')];
	            $po_id = $val[csf('po_id')];
	            // echo "insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)";
	            $r_id2=execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)");
	        }
        }
        $po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];

        /*G:\wamp\www\platform-v3.5\inventory\grey_fabric\requires\grey_fabric_transfer_v2_controller.php
        G:\wamp\www\platform-v3.5\inventory\grey_fabric\requires\grey_fabric_sample_transfer_controller.php
        $action=='populate_data_from_order' and $action=="populate_data_from_sample"
        if ($val[csf("transfer_criteria")]==1 || $val[csf("transfer_criteria")]==2 || $val[csf("transfer_criteria")]==4 || $val[csf("transfer_criteria")]==6) // order id
        {
        	// out order // from_order_id
        }
        if ($val[csf("transfer_criteria")]==8 || $val[csf("transfer_criteria")]==7) // SMN booking ID
        {
        	// Sample out // from_order_id
        }
        if ($val[csf("transfer_criteria")]==1 || $val[csf("transfer_criteria")]==2 || $val[csf("transfer_criteria")]==4 || $val[csf("transfer_criteria")]==5)  // order id
        {
        	// in order // to_order_id
        }
        if ($val[csf("transfer_criteria")]==6 || $val[csf("transfer_criteria")]==8) // SMN booking ID 
        {
        	// Sample In // to_order_id
        }*/
	}
	oci_commit($con);
	// echo '<pre>';print_r($trans_in_data_array);die;
	
	// =================== Order Information =============================
	if ($cbo_is_sales==1) // is sales
	{
		if (count($po_id_arr) > 0) 
		{
			$po_sql="SELECT b.id, b.job_no, b.sales_booking_no, b.style_ref_no, b.customer_buyer, c.booking_no, d.booking_type, d.is_short, d.short_booking_type, e.job_no_mst, e.po_number, e.grouping 
			from tmp_po_id a, fabric_sales_order_mst b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e
			where a.po_id=b.id and b.booking_id=c.BOOKING_MST_ID and c.BOOKING_MST_ID=d.id and c.po_break_down_id=e.id and c.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id
			union all
			select b.id, b.job_no, b.sales_booking_no, b.style_ref_no, b.customer_buyer, c.booking_no, c.booking_type, null as is_short, null as short_booking_type, null as job_no_mst, null as po_number, null as grouping
			from tmp_po_id a, fabric_sales_order_mst b, wo_non_ord_samp_booking_mst c
			where a.po_id=b.id and b.booking_id=c.ID and c.booking_type in(4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id";
			// echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
		    foreach ($po_sql_result as $val)
		    {
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            else
	            {
	            	$booking_type_arr[$val[csf("id")]]="Sample Without Order";
	            }
	            $po_name_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no_mst")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
	            $buyer_name_arr[$val[csf("id")]]=$val[csf("customer_buyer")];
		    }
		}
	}
	else
	{
		if (count($po_id_arr) > 0) 
		{
			$order_data_arr =  sql_select("SELECT b.id, b.po_number, c.job_no, c.style_ref_no, c.buyer_name
			from tmp_po_id a, wo_po_break_down b, wo_po_details_master c
			where a.po_id=b.id and b.job_id=c.id and c.company_name=$cbo_company_name and a.user_id=$user_id");
		    foreach ($order_data_arr as $val)
		    {
	            $po_name_arr[$val[csf("id")]]=$val[csf("po_number")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $buyer_name_arr[$val[csf("id")]]=$val[csf("buyer_name")];
		    }

		    $booking_data_arr =  sql_select("SELECT b.id, b.po_number, c.job_no, c.style_ref_no, e.booking_no, e.booking_type, e.is_short, e.short_booking_type 
			from tmp_po_id a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d , wo_booking_mst e
			where a.po_id=b.id and b.job_id=c.id and b.id=d.po_break_down_id and d.booking_mst_id=e.id and c.company_name=$cbo_company_name and a.user_id=$user_id");
		    foreach ($booking_data_arr as $val)
		    {
	            $job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
	            $short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
		    }
		}
	}
	// =================== Order Information =============================

	// =================== Knitting Production Information ===============
	$production_sql_data=sql_select("SELECT b.id, b.receive_basis, b.booking_id, b.booking_no, b.booking_without_order, c.brand_id, c.body_part_id, c.machine_dia, c.color_id
	from tmp_prog_no a, inv_receive_master b, pro_grey_prod_entry_dtls c
	where a.prog_no=b.booking_id and b.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and b.entry_form=2 and b.item_category=13 and a.userid=$user_id");
	foreach ($production_sql_data as $val)
	{
		if ($val[csf('receive_basis')]==2)
		{
			$program_no_arr[$val[csf('booking_id')]]=$val[csf('booking_id')];			
		}
		else
		{			
			$booking_no_arr_arr[$val[csf('booking_id')]]=$val[csf('booking_no')];
		}
		$receive_basis_arr[$val[csf('booking_id')]]=$val[csf('receive_basis')];
		$knitting_source_arr[$val[csf('booking_id')]]=$val[csf('knitting_source')];

		$production_brand_id_arr[$val[csf('booking_id')]]=$val[csf('brand_id')];
		$production_body_part_id_arr[$val[csf('booking_id')]]=$val[csf('body_part_id')];
		$production_machine_dia_arr[$val[csf('booking_id')]]=$val[csf('machine_dia')];
		$production_color_id_arr[$val[csf('booking_id')]]=$val[csf('color_id')];
	}
	// =================== Knitting Production Information ================

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:3100px" id="main_body">
		<table width="3100" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary</td>
			</tr>
			<tr style="border:none;">
				<td colspan="20" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="2970" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Transfer. Date</th>
					<th width="120">Transfer. Ref.</th>
					<th width="110">Transfer Challan</th>
					<th width="100">Job No</th>
					<th width="80">Buyer</th>
					<th width="120">Style No</th>
					<th width="120"><?echo ($cbo_is_sales==1) ? 'FSO NO' : 'Order No' ; ?></th>
					<th width="120">Fabric Booking</th>
					<th width="100">Booking Type</th>
					<th width="70">Program No</th>
					<th width="80">Knitting Source</th>
					<th width="80">Production Basis</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="80">S. Length</th>
					<th width="50">MC.Dia</th>
					<th width="80">Transfer Qty</th>
					<th width="80">User</th>
					<th width="80">Insert Date & Time</th>
					<th width="150">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:2990px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="2970" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; $total_trans_in_qty=0;
					if(!empty($trans_in_data_array))
					{
						foreach($trans_in_data_array as $trans_in_date => $trans_in_data_arr)
						{
							foreach($trans_in_data_arr as $trans_in_number => $trans_in_number_arr)
							{
								foreach($trans_in_number_arr as $detar_id => $row)
								{
									$po_ids_arr = explode(",", $row["po_id"]);
									$job_no='';$style_ref_no='';$po_name='';$booking_no='';$booking_type='';$buyer_name='';
									foreach ($po_ids_arr as $poId) 
									{
										$job_no .= $job_no_arr[$poId].",";
										$style_ref_no .= $style_ref_no_arr[$poId].",";
										$po_name .= $po_name_arr[$poId].",";
										$buyer_name .= $buyer_arr[$buyer_name_arr[$poId]].",";
										$booking_no .= $booking_no_arr[$poId].",";
										$booking_type .= $booking_type_arr[$poId].",";
									}
									$job_no =implode(",",array_unique(explode(",",chop($job_no,","))));
									$style_ref_no =implode(",",array_unique(explode(",",chop($style_ref_no,","))));
									$po_name =implode(",",array_unique(explode(",",chop($po_name,","))));
									$buyer_name =implode(",",array_unique(explode(",",chop($buyer_name,","))));
									$booking_no =implode(",",array_unique(explode(",",chop($booking_no,","))));
									$booking_type =implode(",",array_unique(explode(",",chop($booking_type,","))));

									$yarn_lot="";
									$yarn_lot_arr = explode(",", $row["yarn_lot"]);
									foreach ($yarn_lot_arr as $value) 
									{
										$yarn_lot .= $value.",";
									}
									$yarn_lot =implode(",",array_unique(explode(",",chop($yarn_lot,","))));

									/*$brand_name="";
									$brand_id_arr = explode(",", $row["brand_id"]);
									foreach ($brand_id_arr as $value) 
									{
										$brand_name .= $brand_arr[$value].",";
									}
									$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));*/

									/*$body_parts="";
									$body_part_id_arr = explode(",", $row["body_part_id"]);
									foreach ($body_part_id_arr as $value) 
									{
										$body_parts .= $body_part[$value].",";
									}
									$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));*/

									$dia =implode(",",array_unique(explode(",",chop($row["dia"],","))));
									$gsm =implode(",",array_unique(explode(",",chop($row["gsm"],","))));
									$stitch_length =implode(",",array_unique(explode(",",chop($row["stitch_length"],","))));
									// $machine_dia =implode(",",array_unique(explode(",",chop($row["machine_dia"],","))));
									
									$production_trans_in_basis="";$program_no="";$smn_booking_no="";$knitting_source_name="";$brand_name="";$body_parts='';$machine_dia='';$color_names="";
									$program_no_id_arr = explode(",", $row["program_no"]);
									foreach ($program_no_id_arr as $value) 
									{
										$production_trans_in_basis .= $receive_basis[$receive_basis_arr[$value]].",";
										$program_no .= $program_no_arr[$value].",";
										$smn_booking_no .= $booking_no_arr_arr[$value].",";
										$knitting_source_name .= $knitting_source[$knitting_source_arr[$value]].",";

										$brand_name .= $brand_arr[$production_brand_id_arr[$value]].",";
										$body_parts .= $body_part[$production_body_part_id_arr[$value]].",";
										$machine_dia .= $production_machine_dia_arr[$value].",";
										$color_names .= $color_array[$production_color_id_arr[$value]].",";
									}
									$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));
									$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));
									$machine_dia =implode(",",array_unique(explode(",",chop($machine_dia,","))));
									$color_names =implode(",",array_unique(explode(",",chop($color_names,","))));

									$production_trans_in_basis =implode(",",array_unique(explode(",",chop($production_trans_in_basis,","))));
									$program_no =implode(",",array_unique(explode(",",chop($program_no,","))));
									$smn_booking_no =implode(",",array_unique(explode(",",chop($smn_booking_no,","))));
									$knitting_source_name =implode(",",array_unique(explode(",",chop($knitting_source_name,","))));

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i;?></td>
										<td width="70"><? echo change_date_format($trans_in_date);?></td>
										<td width="120"><? echo $trans_in_number;?></td>
										<td width="110"><? echo $row["challan_no"];?></td>
										<td width="100"><p><? echo $job_no;?></p></td>
										<td width="80"><p><? echo $buyer_name;?></p></td>
										<td width="120"><p><? echo $style_ref_no;?></p></td>
										<td width="120"><p><? echo $po_name;?></p></td>
										<td width="120"><p>
											<? if ($booking_no=='') 
											{
												echo $smn_booking_no;
											}
											else
											{
												echo $booking_no;
											}
											?></p>
										</td>
		                                <td width="100">
		                                	<? 
		                                	if ($booking_no!="") 
		                                	{
		                                		if ($booking_type!="" || $smn_booking_no!="") 
				                                {
				                                	echo $booking_type;
				                                }
				                                else
			                                	{
			                                		echo "Sample Without Order";
			                                	}
		                                	}			                                
			                                ?>
			                            </td>
		                                <td width="70"><p><? echo $program_no;?></p></td>
		                                <td width="80"><? echo $knitting_source_name;?></td>
		                                <td width="80"><? 
		                                if ($production_trans_in_basis!="") 
		                                {
		                                 	echo $production_trans_in_basis;
		                                }
		                                else
		                                {
		                                	echo "Fabric Booking";
		                                }
		                                ?></td>

		                                <td width="100" align="center"><p>
		                                	<? 
		                                	$count_name="";
		                                	foreach (explode(",", $row["yarn_count"]) as  $count) 
		                                	{
		                                		$count_name .= $count_array[$count].",";
		                                	}
		                                	echo chop($count_name,",");
		                                	?></p>
		                                </td>
		                                <td width="100"><p><? echo $yarn_lot;?></p></td>
		                                <td width="100"><p><? echo $brand_name;?></p></td>
		                                <td width="100"><p><? echo $body_parts;?></p></td>
		                                <td width="100"><p><? echo $constructtion_arr[$detar_id];?></p></td>
		                                <td width="140"><p><? echo $composition_arr[$detar_id];?></p></td>
		                                <td width="80"><p><? echo $color_names; ?></p></td>
		                                <td width="50"><? echo $dia;?></td>
		                                <td width="50"><? echo $gsm;?></td>
		                                <td width="80"><p><? echo $stitch_length;?></p></td>
		                                <td width="50"><? echo $machine_dia;?></td>
		                                <td width="80" align="right"><? echo number_format($row["trans_in_qnty"],2,'.',''); ?></td>
		                                <td width="80" align="center"><? echo $user_array[$row["inserted_by"]];?></td>
		                                <td width="80"><p><? echo date("d-M-Y",strtotime($row["insert_date"]))."&\n " .date("h:i",strtotime($row["insert_date"]));?></p></td>
		                                <td width="150"><p><? echo $row["remarks"];?></p></td>

									</tr>
									<?
									$total_trans_in_qty += $row["trans_in_qnty"];
									$i++;
								}
							}
						}
					}
					else
					{
						echo "No Data Found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="2970" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="100"></th>					
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="50">Total : </th>
					<th width="80" align="right"><? echo number_format($total_trans_in_qty,2,'.','');?></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="150"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}

if($action=="generate_report_transfer_out")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);


	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,"yyyy-mm-dd");
		$txt_date_to=change_date_format($txt_date_to,"yyyy-mm-dd");
	}
	else
	{
		$txt_date_from=change_date_format($txt_date_from,"","",1);
		$txt_date_to=change_date_format($txt_date_to,"","",1);
	}

	$str_cond="";
	if($cbo_company_name>0) $str_cond.=" and a.company_id=$cbo_company_name";
	if($cbo_store_name>0) $str_cond.=" and c.store_id=$cbo_store_name";
	if($txt_order_id) $str_cond .=" and a.from_order_id =$txt_order_id";


	if($cbo_buyer_name>0)
	{
		$str_cond .= " and a.buyer_id = $cbo_buyer_name ";
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_based_on == 1) 
		{
			$date_cond_iss = " and a.issue_date between '$txt_date_from' and '$txt_date_to' ";
			$date_cond_trans = " and a.transfer_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			if($db_type==0)
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond_iss="";
			}
			else
			{
				if($txt_date_from!="" && $txt_date_to!="") $date_cond_iss="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond_iss="";
			}
			$date_cond_trans=$date_cond_iss;
		}
	}

	$user_array=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$count_array=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$company_array=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	// $booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$color_array=return_library_array( "select id,color_name from lib_color where status_active=1 $allColorCond", "id", "color_name");
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');
	$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);

    if ($cbo_is_sales==1) // is sales
    {
    	$entry_form_cond=" and a.entry_form in(362)";
    }
    else
    {
    	$entry_form_cond=" and a.entry_form in(432,81,80,13)";
    }
	// ========================= Transfer Out query =====================
	$trans_out_sql=sql_select("SELECT a.id, a.transfer_system_id as trans_out_number, a.transfer_date as trans_out_date, a.challan_no,a.transfer_criteria, a.from_order_id as po_id, a.remarks, a.inserted_by, a.insert_date, a.entry_form, b.to_trans_id, c.prod_id, c.transaction_type, e.detarmination_id as deter_id, b.yarn_lot, b.y_count, b.color_id , e.brand, e.dia_width, e.gsm, b.transfer_qnty, b.from_program as program_no, b.to_program, b.stitch_length
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master e 
	where a.id=b.mst_id and b.trans_id = c.id and a.id = c.mst_id and c.prod_id=e.id $entry_form_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and c.transaction_type=6 $date_cond_trans $str_cond order by a.transfer_system_id asc");
	// ============== Issue data array ==================================
	$trans_in_data_array = array();
	foreach ($trans_out_sql  as $val) 
	{
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["program_no"].=$val[csf("program_no")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["knitting_source"]=$val[csf("knit_dye_source")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["challan_no"]=$val[csf("challan_no")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["buyer_id"]=$val[csf("buyer_id")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["po_id"].=$val[csf("po_id")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["yarn_count"].=$val[csf("y_count")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["yarn_lot"].=$val[csf("yarn_lot")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["brand_id"].=$val[csf("brand")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["body_part_id"].=$val[csf("body_part_id")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["color_id"].=$val[csf("color_id")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["dia"].=$val[csf("dia_width")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["gsm"].=$val[csf("gsm")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["stitch_length"].=$val[csf("stitch_length")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["machine_dia"].=$val[csf("machine_dia")].',';
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["remarks"]=$val[csf("remarks")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["inserted_by"]=$val[csf("inserted_by")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["insert_date"]=$val[csf("insert_date")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["trans_out_basis"]=$val[csf("trans_out_basis")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["trans_out_purpose"]=$val[csf("trans_out_purpose")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["booking_no"]=$val[csf("booking_no")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["trans_out_qnty"] +=  $val[csf("transfer_qnty")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["transfer_criteria"] =  $val[csf("transfer_criteria")];
		$trans_out_data_array[$val[csf("trans_out_date")]][$val[csf("trans_out_number")]][$val[csf("deter_id")]]["entry_form"] =  $val[csf("entry_form")];

		if( $production_id_check[$val[csf('program_no')]] == "" )
        {
            $program_no_check[$val[csf('program_no')]]=$val[csf('program_no')];
            $program_no = $val[csf('program_no')];
            // echo "insert into tmp_prog_no (userid, prog_no) values ($user_id,$program_no)";
            $r_id2=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_id,$program_no)");
        }

        if ($val[csf('entry_form')]==80 || $val[csf('entry_form')]==13 || $val[csf('entry_form')]==362)
        {
        	if ($po_id_check[$val[csf('po_id')]] == "")
	        {
	            $po_id_check[$val[csf('po_id')]]=$val[csf('po_id')];
	            $po_id = $val[csf('po_id')];
	            // echo "insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)";
	            $r_id2=execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,$po_id)");
	        }
        }
        $po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];

        /*G:\wamp\www\platform-v3.5\inventory\grey_fabric\requires\grey_fabric_transfer_v2_controller.php
        G:\wamp\www\platform-v3.5\inventory\grey_fabric\requires\grey_fabric_sample_transfer_controller.php
        $action=='populate_data_from_order' and $action=="populate_data_from_sample"
        if ($val[csf("transfer_criteria")]==1 || $val[csf("transfer_criteria")]==2 || $val[csf("transfer_criteria")]==4 || $val[csf("transfer_criteria")]==6) // order id
        {
        	// out order // from_order_id
        }
        if ($val[csf("transfer_criteria")]==8 || $val[csf("transfer_criteria")]==7) // SMN booking ID
        {
        	// Sample out // from_order_id
        }
        if ($val[csf("transfer_criteria")]==1 || $val[csf("transfer_criteria")]==2 || $val[csf("transfer_criteria")]==4 || $val[csf("transfer_criteria")]==5)  // order id
        {
        	// in order // to_order_id
        }
        if ($val[csf("transfer_criteria")]==6 || $val[csf("transfer_criteria")]==8) // SMN booking ID 
        {
        	// Sample In // to_order_id
        }*/
	}
	oci_commit($con);
	// echo '<pre>';print_r($trans_out_data_array);die;
	
	// =================== Order Information =============================
	if ($cbo_is_sales==1) // is sales
	{
		if (count($po_id_arr) > 0) 
		{
			$po_sql="SELECT b.id, b.job_no, b.sales_booking_no, b.style_ref_no, b.customer_buyer, c.booking_no, d.booking_type, d.is_short, d.short_booking_type, e.job_no_mst, e.po_number, e.grouping 
			from tmp_po_id a, fabric_sales_order_mst b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e
			where a.po_id=b.id and b.booking_id=c.BOOKING_MST_ID and c.BOOKING_MST_ID=d.id and c.po_break_down_id=e.id and c.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id
			union all
			select b.id, b.job_no, b.sales_booking_no, b.style_ref_no, b.customer_buyer, c.booking_no, c.booking_type, null as is_short, null as short_booking_type, null as job_no_mst, null as po_number, null as grouping
			from tmp_po_id a, fabric_sales_order_mst b, wo_non_ord_samp_booking_mst c
			where a.po_id=b.id and b.booking_id=c.ID and c.booking_type in(4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.user_id=$user_id";
			// echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
		    foreach ($po_sql_result as $val)
		    {
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            else
	            {
	            	$booking_type_arr[$val[csf("id")]]="Sample Without Order";
	            }
	            $po_name_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no_mst")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
	            $buyer_name_arr[$val[csf("id")]]=$val[csf("customer_buyer")];
		    }
		}
	}
	else
	{
		if (count($po_id_arr) > 0) 
		{
			$order_data_arr =  sql_select("SELECT b.id, b.po_number, c.job_no, c.style_ref_no, c.buyer_name
			from tmp_po_id a, wo_po_break_down b, wo_po_details_master c
			where a.po_id=b.id and b.job_id=c.id and c.company_name=$cbo_company_name and a.user_id=$user_id");
		    foreach ($order_data_arr as $val)
		    {
	            $po_name_arr[$val[csf("id")]]=$val[csf("po_number")];
	            $job_no_arr[$val[csf("id")]]=$val[csf("job_no")];
	            $style_ref_no_arr[$val[csf("id")]]=$val[csf("style_ref_no")];
	            $buyer_name_arr[$val[csf("id")]]=$val[csf("buyer_name")];
		    }

		    $booking_data_arr =  sql_select("SELECT b.id, b.po_number, c.job_no, c.style_ref_no, e.booking_no, e.booking_type, e.is_short, e.short_booking_type 
			from tmp_po_id a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d , wo_booking_mst e
			where a.po_id=b.id and b.job_id=c.id and b.id=d.po_break_down_id and d.booking_mst_id=e.id and c.company_name=$cbo_company_name and a.user_id=$user_id");
		    foreach ($booking_data_arr as $val)
		    {
	            $job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
	            $short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
	            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
	            {
	                $booking_type_arr[$val[csf("id")]]="Main";
	            }
	            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
	            {
	                $booking_type_arr[$val[csf("id")]]="Short";
	            }
	            else if($val[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$val[csf("id")]]="Sample With Order";
	            }
	            $booking_no_arr[$val[csf("id")]]=$val[csf("booking_no")];
		    }
		}
	}
	// =================== Order Information =============================

	// =================== Knitting Production Information ===============
	$production_sql_data=sql_select("SELECT b.id, b.receive_basis, b.booking_id, b.booking_no, b.booking_without_order, c.brand_id, c.body_part_id, c.machine_dia, c.color_id
	from tmp_prog_no a, inv_receive_master b, pro_grey_prod_entry_dtls c
	where a.prog_no=b.booking_id and b.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and b.entry_form=2 and b.item_category=13 and a.userid=$user_id");
	foreach ($production_sql_data as $val)
	{
		if ($val[csf('receive_basis')]==2)
		{
			$program_no_arr[$val[csf('booking_id')]]=$val[csf('booking_id')];			
		}
		else
		{			
			$booking_no_arr_arr[$val[csf('booking_id')]]=$val[csf('booking_no')];
			$smn_booking_type_arr[$val[csf('booking_id')]]='Sample Without Order';
		}
		$receive_basis_arr[$val[csf('booking_id')]]=$val[csf('receive_basis')];
		$knitting_source_arr[$val[csf('booking_id')]]=$val[csf('knitting_source')];		

		$production_brand_id_arr[$val[csf('booking_id')]]=$val[csf('brand_id')];
		$production_body_part_id_arr[$val[csf('booking_id')]]=$val[csf('body_part_id')];
		$production_machine_dia_arr[$val[csf('booking_id')]]=$val[csf('machine_dia')];
		$production_color_id_arr[$val[csf('booking_id')]]=$val[csf('color_id')];
	}
	// =================== Knitting Production Information ================

	$con = connect();
    $r_id1=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from tmp_prog_no where userid=$user_id");
    oci_commit($con);

	ob_start();
	?>
	<!-- <style type="text/css">
		.word_wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}
	</style> -->
	<div style="width:3100px" id="main_body">
		<table width="3100" id="" align="left">
			<tr class="form_caption" style="border:none;">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Store Wise Receive Issue Summary</td>
			</tr>
			<tr style="border:none;">
				<td colspan="20" align="center" style="border:none; font-size:14px;">
					Company Name : <? echo $company_array[str_replace("'","",$cbo_company_name)]; ?>
				</td>
			</tr>
		</table>
		<br />
		<table width="2970" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Transfer. Date</th>
					<th width="120">Transfer. Ref.</th>
					<th width="110">Transfer Challan</th>
					<th width="100">Job No</th>
					<th width="80">Buyer</th>
					<th width="120">Style No</th>
					<th width="120"><?echo ($cbo_is_sales==1) ? 'FSO NO' : 'Order No' ; ?></th>
					<th width="120">Fabric Booking</th>
					<th width="100">Booking Type</th>
					<th width="70">Program No</th>
					<th width="80">Knitting Source</th>
					<th width="80">Production Basis</th>
					<th width="100">Yarn Count</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Yarn Brand</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="140">Composition</th>
					<th width="80">Color</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="80">S. Length</th>
					<th width="50">MC.Dia</th>
					<th width="80">Transfer Qty</th>
					<th width="80">User</th>
					<th width="80">Insert Date & Time</th>
					<th width="150">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:2990px; overflow-y: scroll; max-height:250px; float: left;" id="scroll_body">
			<table width="2970" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
					<?
					$i=1; $total_trans_out_qty=0;
					if(!empty($trans_out_data_array))
					{
						foreach($trans_out_data_array as $trans_out_date => $trans_out_data_arr)
						{
							foreach($trans_out_data_arr as $trans_out_number => $trans_out_number_arr)
							{
								foreach($trans_out_number_arr as $detar_id => $row)
								{
									$po_ids_arr = explode(",", $row["po_id"]);
									$job_no='';$style_ref_no='';$po_name='';$booking_no='';$booking_type='';$buyer_name='';
									foreach ($po_ids_arr as $poId) 
									{
										$job_no .= $job_no_arr[$poId].",";
										$style_ref_no .= $style_ref_no_arr[$poId].",";
										$po_name .= $po_name_arr[$poId].",";
										$buyer_name .= $buyer_arr[$buyer_name_arr[$poId]].",";
										$booking_no .= $booking_no_arr[$poId].",";
										$booking_type .= $booking_type_arr[$poId].",";
									}
									$job_no =implode(",",array_unique(explode(",",chop($job_no,","))));
									$style_ref_no =implode(",",array_unique(explode(",",chop($style_ref_no,","))));
									$po_name =implode(",",array_unique(explode(",",chop($po_name,","))));
									$buyer_name =implode(",",array_unique(explode(",",chop($buyer_name,","))));
									$booking_no =implode(",",array_unique(explode(",",chop($booking_no,","))));
									$booking_type =implode(",",array_unique(explode(",",chop($booking_type,","))));

									$yarn_lot="";
									$yarn_lot_arr = explode(",", $row["yarn_lot"]);
									foreach ($yarn_lot_arr as $value) 
									{
										$yarn_lot .= $value.",";
									}
									$yarn_lot =implode(",",array_unique(explode(",",chop($yarn_lot,","))));

									/*$brand_name="";
									$brand_id_arr = explode(",", $row["brand_id"]);
									foreach ($brand_id_arr as $value) 
									{
										$brand_name .= $brand_arr[$value].",";
									}
									$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));

									$body_parts="";
									$body_part_id_arr = explode(",", $row["body_part_id"]);
									foreach ($body_part_id_arr as $value) 
									{
										$body_parts .= $body_part[$value].",";
									}
									$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));*/

									$dia =implode(",",array_unique(explode(",",chop($row["dia"],","))));
									$gsm =implode(",",array_unique(explode(",",chop($row["gsm"],","))));
									$stitch_length =implode(",",array_unique(explode(",",chop($row["stitch_length"],","))));
									// $machine_dia =implode(",",array_unique(explode(",",chop($row["machine_dia"],","))));
									
									$production_trans_out_basis="";$program_no="";$smn_booking_no="";$knitting_source_name="";$smn_booking_type='';$brand_name="";$body_parts='';$machine_dia='';$color_names="";
									$program_no_id_arr = explode(",", $row["program_no"]);
									foreach ($program_no_id_arr as $value) 
									{
										$production_trans_out_basis .= $receive_basis[$receive_basis_arr[$value]].",";
										$program_no .= $program_no_arr[$value].",";
										$knitting_source_name .= $knitting_source[$knitting_source_arr[$value]].",";
										$smn_booking_no .= $booking_no_arr_arr[$value].",";
										$smn_booking_type .= $smn_booking_type_arr[$value].",";

										$brand_name .= $brand_arr[$production_brand_id_arr[$value]].",";
										$body_parts .= $body_part[$production_body_part_id_arr[$value]].",";
										$machine_dia .= $production_machine_dia_arr[$value].",";
										$color_names .= $color_array[$production_color_id_arr[$value]].",";
									}
									$brand_name =implode(",",array_unique(explode(",",chop($brand_name,","))));
									$body_parts =implode(",",array_unique(explode(",",chop($body_parts,","))));
									$machine_dia =implode(",",array_unique(explode(",",chop($machine_dia,","))));
									$color_names =implode(",",array_unique(explode(",",chop($color_names,","))));

									$production_trans_out_basis =implode(",",array_unique(explode(",",chop($production_trans_out_basis,","))));
									$program_no =implode(",",array_unique(explode(",",chop($program_no,","))));
									$smn_booking_no =implode(",",array_unique(explode(",",chop($smn_booking_no,","))));
									$smn_booking_type =implode(",",array_unique(explode(",",chop($smn_booking_type,","))));
									//$smn_booking_noArr=array_unique(explode(",",chop($smn_booking_no,",")));
									//$smn_booking_noArr2=explode("-",chop($smn_booking_noArr[0],","));
									// echo $smn_booking_noArr2[1].'<br>';

									$knitting_source_name =implode(",",array_unique(explode(",",chop($knitting_source_name,","))));

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i;?></td>
										<td width="70"><? echo change_date_format($trans_out_date);?></td>
										<td width="120"><? echo $trans_out_number;?></td>
										<td width="110"><? echo $row["challan_no"];?></td>
										<td width="100"><p><? echo $job_no;?></p></td>
										<td width="80"><p><? echo $buyer_name;?></p></td>
										<td width="120"><p><? echo $style_ref_no;?></p></td>
										<td width="120"><p><? echo $po_name;?></p></td>
										<td width="120"><p>
											<? 
											if ($booking_no=='') 
											{
												echo $smn_booking_no;
											}
											else
											{
												echo $booking_no;
											}
											?></p>
										</td>
		                                <td width="100" title="<?=$row["transfer_criteria"];?>">
		                                	<?
		                                	if ($booking_no=='') 
											{
												echo $smn_booking_type;
											}
											else
											{
												echo $booking_type;
											}
											/*if ($row["transfer_criteria"]==8 || $row["transfer_criteria"]==7) 
											{
												echo $smn_booking_type;
											}
											else
											{
												echo $booking_type;
											}*/		                                
			                                ?>
			                            </td>
		                                <td width="70"><p><? echo $program_no;?></p></td>
		                                <td width="80"><? echo $knitting_source_name;?></td>
		                                <td width="80"><? 
		                                if ($production_trans_out_basis!="") 
		                                {
		                                 	echo $production_trans_out_basis;
		                                }
		                                else
		                                {
		                                	echo "Fabric Booking";
		                                }
		                                ?></td>

		                                <td width="100" align="center"><p>
		                                	<? 
		                                	$count_name="";
		                                	foreach (explode(",", $row["yarn_count"]) as  $count) 
		                                	{
		                                		$count_name .= $count_array[$count].",";
		                                	}
		                                	echo chop($count_name,",");
		                                	?></p>
		                                </td>
		                                <td width="100"><p><? echo $yarn_lot;?></p></td>
		                                <td width="100"><p><? echo $brand_name;?></p></td>
		                                <td width="100"><p><? echo $body_parts;?></p></td>
		                                <td width="100"><p><? echo $constructtion_arr[$detar_id];?></p></td>
		                                <td width="140"><p><? echo $composition_arr[$detar_id];?></p></td>
		                                <td width="80"><p><? echo $color_names; ?></p></td>
		                                <td width="50"><? echo $dia;?></td>
		                                <td width="50"><? echo $gsm;?></td>
		                                <td width="80"><p><? echo $stitch_length;?></p></td>
		                                <td width="50"><? echo $machine_dia;?></td>
		                                <td width="80" align="right"><? echo number_format($row["trans_out_qnty"],2,'.',''); ?></td>
		                                <td width="80" align="center"><? echo $user_array[$row["inserted_by"]];?></td>
		                                <td width="80"><p><? echo date("d-M-Y",strtotime($row["insert_date"]))."&\n " .date("h:i",strtotime($row["insert_date"]));?></p></td>
		                                <td width="150"><p><? echo $row["remarks"];?></p></td>

									</tr>
									<?
									$total_trans_out_qty += $row["trans_out_qnty"];
									$i++;
								}
							}
						}
					}
					else
					{
						echo "No Data Found";
					}
					?>
				</tbody>
			</table>
		</div>
		<table width="2970" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="30"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="100"></th>					
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="80"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="50">Total : </th>
					<th width="80" align="right"><? echo number_format($total_trans_out_qty,2,'.','');?></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="150"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	
	foreach (glob($user_id."*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$rptType";
	disconnect($con);
	exit();
}
?>
