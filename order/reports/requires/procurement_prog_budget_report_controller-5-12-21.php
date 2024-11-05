<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );
	exit();
}

if($action=="wo_no_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
	$year_id=$ex_data[2];
	$category_id=$ex_data[3];
	$wo_type=$ex_data[4];
	?>
		<script>
			function js_set_value(wo_id,wo_no)
			{
				document.getElementById('txt_wo_no').value=wo_no;
				document.getElementById('txt_wo_id').value=wo_id;
				parent.emailwindow.hide();
			}
	    </script>
	</head>
	<body>
		<fieldset style="width:630px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="620" class="rpt_table">
	                <thead>
	                    <th>Buyer</th>
	                    <th>Search</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="" style="width:50px">
	                        <input type="hidden" name="txt_wo_id" id="txt_wo_id" value="" style="width:50px">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                        <?
								echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $year_id; ?>+'_'+<? echo $category_id; ?>+'_'+<? echo $wo_type; ?>, 'create_wo_search_list_view', 'search_div', 'procurement_prog_budget_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	            </table>
	            <div id="search_div" style="margin-top:10px"></div>
	        </form>
	    </fieldset>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
	if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
	if ($data[2]==0) $search_wo=""; else $search_wo=" and booking_no_prefix_num=$data[2]";

	if($db_type==0)
	{
		if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$data[3]";
	}
	elseif($db_type==2)
	{
		if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$data[3]";
	}

	if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and item_category=$data[4]";
	if ($data[5]==1 || $data[5]==2)  $wo_type_cond=" and booking_type in (1,2) and is_short='$data[5]'"; else $wo_type_cond="";
	if ($data[5]==3) $wo_type_cond_sam="  and booking_type=4"; else $wo_type_cond_sam="";

	if($db_type==0)
	{
		$year=" YEAR(insert_date) as year";
	}
	elseif($db_type==2)
	{
		$year=" TO_CHAR(insert_date,'YYYY') as year";
	}

	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	if($data[5]==0)
	{
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam
		union all
		SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond";
	}
	else if ($data[5]==1 || $data[5]==2 || $data[5]==3)
	{
		$sql= "select id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $wo_type_cond $wo_type_cond_sam";
	}
	else
	{
		$sql= "SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $year_id_cond $search_wo $category_id_cond";
	}
	//echo $sql;

	//$arr=array(3=>$buyerArr);
	//echo  create_list_view("list_view", "WO No,Year,WO Type,Buyer,WO Date", "70,70,130,140,170","630","320",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,0,buyer_id,0", $arr , "booking_no_prefix_num,year,style_ref_no,buyer_id,booking_date", "",'setFilterGrid("list_view",-1);','0,0,0,0,3','') ;

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="80">WO No </th>
                <th width="80">Year</th>
                <th width="130">WO Type</th>
                <th width="150">Buyer</th>
                <th width="100">WO Date</th>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:300px;" id="" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if ($selectResult[csf("type")]==0)
					{
						if ($selectResult[csf("booking_type")]==1 || $selectResult[csf("booking_type")]==2)
						{
							if ($selectResult[csf("is_short")]==1)
							{
								$wo_type="Short";
							}
							else
							{
								$wo_type="Main";
							}
						}
						elseif($selectResult[csf("booking_type")]==4)
						{
							$wo_type="Sample With Order";
						}
					}
					else
					{
						$wo_type="Sample Non Order";
					}
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('booking_no')]; ?>')">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="80" align="center"><p><? echo $selectResult[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
                        <td width="130"><p><? echo $wo_type; ?></p></td>
                        <td width="150"><p><? echo $buyerArr[$selectResult[csf('buyer_id')]]; ?></p></td>
                        <td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>
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

if ($action=="report_generate")
{
	extract($_REQUEST);

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidd_job_id=str_replace("'","",$hidd_job_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$hidd_search_id=str_replace("'","",$hidd_search_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$hidd_job_no=str_replace("'","",$hidd_job_no);

	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$dealing_merchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$trim_name_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");

	if($cbo_buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name='$cbo_buyer_id'";
	}

	if($db_type==0)
	{
		if ($cbo_job_year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$cbo_job_year_id";
	}
	elseif($db_type==2)
	{
		if ($cbo_job_year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$cbo_job_year_id";
	}

	if($db_type==0)
	{
		if( $txt_date_from=="" && $txt_date_to=="" ) $ship_date_cond=""; else $ship_date_cond=" and b.pub_shipment_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
	if($db_type==2)
	{
		if( $txt_date_from=="" && $txt_date_to=="" ) $ship_date_cond=""; else $ship_date_cond=" and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
	}

	if($txt_job_no=="") $job_cond=""; else $job_cond="and a.job_no_prefix_num='$txt_job_no'";
	if($db_type==0) $insert_year="year(a.insert_date)"; else if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY')";
	if(!$txt_style_ref) $style_cond=""; else $style_cond="and a.style_ref_no ='$txt_style_ref'";

	$hidd_job_no_cond="";
	if($hidd_job_no)
	{
		$hidd_job_no="'".implode("','", array_unique(explode(",", $hidd_job_no)))."'";
		$hidd_job_no_cond="and a.job_no in ($hidd_job_no)";
	}


	$goods_rcv_variable=return_field_value("export_invoice_qty_source as source","variable_settings_commercial","company_name=$cbo_company_id and variable_list=23 and status_active=1","source");

	if(	$goods_rcv_variable != 1) $goods_rcv_variable=2;
	//$goods_rcv_variable=2;
	//var_dump($goods_rcv_variable)//=2;
	$search_job="";
	if($cbo_search_type>0 && $txt_search_no!="")
	{
		/*if($cbo_search_type==2 || $cbo_search_type==3 || $cbo_search_type==4 || $cbo_search_type==12 || $cbo_search_type==25)
		{
			$sql="SELECT b.job_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=$cbo_search_type and a.booking_no='$txt_search_no'";
		}
		else if($cbo_search_type==24)
		{
			$sql= "select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no='$txt_search_no' and b.entry_form in (41,114,0) and a.entry_form in (41,114,0)";
		}
		else
		{
			if($cbo_search_type==1) $pi_cond=" and a.pi_number='$txt_search_no'"; else $pi_cond=" and a.id='$txt_search_no'";
			$sql="SELECT c.job_no
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.goods_rcv_status=2 $pi_cond";
		}*/
		if($cbo_search_type==24)
		{
			$sql= "select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no='$txt_search_no' and b.entry_form in (41,114,0) and a.entry_form in (41,114,0)";
		}
		//if($goods_rcv_variable==1) $goods_rcv_source=" and a.goods_rcv_status=1"; else $goods_rcv_source="  and a.goods_rcv_status=2";


		if($cbo_search_type==1) $pi_cond=" and a.pi_number='$txt_search_no'"; else if($cbo_search_type==8) $pi_cond=" and a.id='$txt_search_no'";
		if($cbo_search_type==1 || $cbo_search_type==8)
		{
			if($goods_rcv_variable==1)
			{
				$sql="SELECT c.job_no
				from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c
				where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id=24 and a.goods_rcv_status=2 $pi_cond
				union all
				SELECT c.job_no
				from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
				where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=2 $pi_cond
				union all
				SELECT c.job_no
				from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
				where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=1 $pi_cond
				UNION ALL
				SELECT c.job_no
				FROM com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c
				WHERE a.id = b.pi_id AND b.work_order_id = c.mst_id and b.work_order_dtls_id=c.id AND a.status_active = 1 AND a.is_deleted = 0
				AND b.status_active = 1 AND b.is_deleted = 0 AND a.pi_basis_id = 1 AND a.item_category_id =1 AND a.goods_rcv_status = 2 $pi_cond
				UNION ALL
				SELECT c.job_no
				FROM com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c
				WHERE a.id = b.pi_id AND b.work_order_id = c.mst_id and b.work_order_dtls_id=c.id AND a.status_active = 1 AND a.is_deleted = 0
				AND b.status_active = 1 AND b.is_deleted = 0 AND a.pi_basis_id = 1 AND a.item_category_id =1 AND a.goods_rcv_status = 1 $pi_cond ";

			}
			else
			{
				$sql="SELECT c.job_no
				from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c
				where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id=24 and a.goods_rcv_status=2 $pi_cond
				union all
				SELECT c.job_no
				from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
				where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=2 $pi_cond
				union all
				SELECT c.job_no
				from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
				where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=1 $pi_cond
				UNION ALL
				SELECT c.job_no
				FROM com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c
				WHERE a.id = b.pi_id AND b.work_order_id = c.mst_id and b.work_order_dtls_id=c.id AND a.status_active = 1 AND a.is_deleted = 0
				AND b.status_active = 1 AND b.is_deleted = 0 AND a.pi_basis_id = 1 AND a.item_category_id =1 AND a.goods_rcv_status = 2 $pi_cond
				UNION ALL
				SELECT d.job_no
				FROM com_pi_master_details a, com_pi_item_details b, inv_transaction c, wo_non_order_info_dtls d
				WHERE a.id=b.pi_id and b.work_order_dtls_id = c.id AND c.pi_wo_batch_no = d.mst_id and b.item_category_id=1 and c.item_category=1 and d.item_category_id=1 and d.job_id>0 and c.receive_basis=1 and c.transaction_type=1 AND a.status_active = 1 AND a.is_deleted = 0
				AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.pi_basis_id = 1 AND a.goods_rcv_status = 1 $pi_cond ";
			}

			//echo $sql;//die;
			$sql_res=sql_select($sql);
			foreach($sql_res as $row)
			{
				$search_job.="'".$row[csf("job_no")]."',";
			}
			$search_job=implode(",",array_unique(explode(",",chop($search_job,","))));
			if($search_job=="") { echo "No Data Found";die; }

		}

		//echo $sql;//die;
		$sql_res=sql_select($sql);
			foreach($sql_res as $row)
			{
				$search_job.="'".$row[csf("job_no")]."',";
			}
			$search_job=implode(",",array_unique(explode(",",chop($search_job,","))));
			if($search_job=="") { echo "No Data Found";die; }
	}
	//var_dump($search_job);
	/*$search_job_cond="";
	if($search_job!="")
	{
		$search_job_cond=" and a.job_no in($search_job)";
	}*/

	//$job_sql="select a.id, a.job_no, a.job_no_prefix_num, a.buyer_name, $insert_year as job_year, a.dealing_marchant, a.style_ref_no, a.job_quantity, a.avg_unit_price, a.total_price from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.company_name='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $year_id_cond $ship_date_cond group by a.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, a.dealing_marchant, a.style_ref_no, a.job_quantity, a.avg_unit_price, a.total_price order by a.id DESC";
	$job_sql="select a.id, a.job_no, a.job_no_prefix_num, a.buyer_name, $insert_year as job_year, a.dealing_marchant, a.style_ref_no, a.job_quantity, a.avg_unit_price, a.total_price, b.id as po_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $year_id_cond $job_cond $style_cond $ship_date_cond $hidd_job_no_cond order by a.id DESC";
	//echo $job_sql; //die;
	$job_sql_res=sql_select($job_sql);


	if(count($job_sql_res)<1) { echo "No Data Found";die; }
	$job_arr=array(); $po_job_arr=array(); $tot_rows=0; $jobNos=''; $poIds='';
	foreach($job_sql_res as $row)
	{
		$tot_rows++;
		$jobNos.="'".$row[csf("job_no")]."',";
		$poIds.=$row[csf("po_id")].",";

		$job_arr[$row[csf("job_no")]]["id"]=$row[csf("id")];
		$job_arr[$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
		$job_arr[$row[csf("job_no")]]["job_prefix_num"]=$row[csf("job_no_prefix_num")];
		$job_arr[$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
		$job_arr[$row[csf("job_no")]]["job_year"]=$row[csf("job_year")];
		$job_arr[$row[csf("job_no")]]["dealing_marchant"]=$row[csf("dealing_marchant")];
		$job_arr[$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$job_arr[$row[csf("job_no")]]["job_qty"]=$row[csf("job_quantity")];
		$job_arr[$row[csf("job_no")]]["avg_price"]=$row[csf("avg_unit_price")];
		$job_arr[$row[csf("job_no")]]["job_amount"]=$row[csf("total_price")];

		$po_job_arr[$row[csf("po_id")]]=$row[csf("job_no")];
	}

	unset($job_sql_res);

	$jobNos=implode(",",array_unique(explode(",",$jobNos)));
	$poIds=implode(",",array_unique(explode(",",$poIds)));
	//echo $poIds; //die;
	$jobNos=chop($jobNos,','); $poIds=chop($poIds,','); $jobNos_cond=""; $jobNos_conv_cond="";


	if($db_type==2 && $tot_rows>1000)
	{
		$jobNos_cond=" and (";
		$jobNos_conv_cond=" and (";
		$jobNosArr=array_chunk(explode(",",$jobNos),399);
		foreach($jobNosArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobNos_cond.=" job_no in($ids) or ";
			$jobNos_conv_cond.=" a.job_no in($ids) or ";
		}
		$jobNos_cond=chop($jobNos_cond,'or ');
		$jobNos_cond.=")";

		$jobNos_conv_cond=chop($jobNos_conv_cond,'or ');
		$jobNos_conv_cond.=")";
	}
	else
	{

		if($cbo_search_type==24 ){
			$jobNos_cond=" and job_no in ($jobNos)";
		}
		//echo "sumon __".$jobNos_cond;
		$jobNos_cond=" and job_no in ($jobNos)";
		$jobNos_conv_cond=" and a.job_no in ($jobNos)";
	}

	$poid_cond=""; $po_emb_cond="";

	if($db_type==2 && $tot_rows>1000)
	{
		$poid_cond=" and (";
		$po_emb_cond=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),399);
		foreach($poIdsArr as $idps)
		{
			$idps=implode(",",$idps);
			$poid_cond.=" b.po_breakdown_id in($idps) or ";
			$po_emb_cond.=" po_break_down_id in($ids) or ";
		}
		$poid_cond=chop($poid_cond,'or ');
		$poid_cond.=")";

		$po_emb_cond=chop($po_emb_cond,'or ');
		$po_emb_cond.=")";
	}
	else
	{
		$poid_cond=" and b.po_breakdown_id in ($poIds)";
		$po_emb_cond=" and po_break_down_id in ($poIds)";
	}
	//var_dump($cbo_buyer_id);
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if($cbo_buyer_id>0){
		$condition->buyer_name("=$cbo_buyer_id");
	}
	//$condition->buyer_name("=65");//die;

	if($txt_job_no!=''){
		$condition->job_no_prefix_num("in ($txt_job_no)");
	}
	if($jobNos!=""){
		$condition->job_no("in ($jobNos)");
	}


	if($txt_date_from!='' && $txt_date_to!=''){
		$condition->pub_shipment_date(" between '$txt_date_from' and '$txt_date_to'");
	}
	if($txt_style_ref!=''){
		$condition->style_ref_no("='$txt_style_ref'");
	}

	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery();//die;
	$yarn_data_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

	$yarn_arr=array();
	$sql_yarn="select job_no from wo_pre_cost_fab_yarn_cost_dtls where is_deleted=0 and status_active=1 $jobNos_cond group by job_no";
	//echo $sql_yarn;//die;
	$data_arr_yarn=sql_select($sql_yarn);
	foreach($data_arr_yarn as $yarn_row){
		$yarn_arr[$yarn_row[csf("job_no")]]['qty']+=$yarn_data_arr[$yarn_row[csf("job_no")]]['qty'];
		$yarn_arr[$yarn_row[csf("job_no")]]['amount']+=$yarn_data_arr[$yarn_row[csf("job_no")]]['amount'];
	}
	unset($data_arr_yarn);

	$fabric= new fabric($condition);
	$fabPur=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
	//echo $fabric->getQuery(); die;


	$fabPurArr=array();
	$sql = "select job_no, uom, fab_nature_id, rate from wo_pre_cost_fabric_cost_dtls where fabric_source=2 $jobNos_cond";
	//echo $sql;die;
	$data_fabPur=sql_select($sql);
	foreach($data_fabPur as $fabPur_row){
		$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('job_no')]];
		$Preamt=$Precons*$fabPur_row[csf('rate')];
		$fabPurArr[$fabPur_row[csf('job_no')]]['qty'][$fabPur_row[csf('uom')]]+=$Precons;
		$fabPurArr[$fabPur_row[csf('job_no')]]['amount'][$fabPur_row[csf('uom')]]+=$Preamt;
	}
	unset($data_fabPur);

	$knitData=array();
	$conversion= new conversion($condition);
	$knitQtyArr=$conversion->getQtyArray_by_jobAndProcess();
	//print_r($knitQtyArr); //die;
	$conversion= new conversion($condition);
	$knitAmtArr=$conversion->getAmountArray_by_jobAndProcess();

	//$sql = "select a.job_no, a.uom, b.cons_process from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description $jobNos_conv_cond";

	$sql = "select a.job_no, a.cons_process, b.uom from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id where 1=1 $jobNos_conv_cond order by  a.cons_process";
	//echo $sql;die;
	$data_knit=sql_select($sql);
	foreach($data_knit as $row_knit){
		$knitData[$row_knit[csf('job_no')]]['qty'][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]]=$knitQtyArr[$row_knit[csf('job_no')]][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]];
		$knitData[$row_knit[csf('job_no')]]['amount'][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]]=$knitAmtArr[$row_knit[csf('job_no')]][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]];

	}
	//echo $sql;die;
	unset($data_knit);

	// echo '<pre>';
	// print_r($knitData);
	// echo '</pre>';

	$embData=array(); $gmtsWashData=array();
	$emblishment= new emblishment($condition);
	$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
	$emblishment= new emblishment($condition);
	$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();


	//print_r($knitData['D n C-17-01621']['qty']);
	//echo $knitData['D n C-17-01621']['qty'][30][12].'=='; die;
	$wash= new wash($condition);
	$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
	$wash= new wash($condition);
	$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();


	//print_r($washQtyArr); die;
	$sql_emb = "select id, job_no, emb_name from wo_pre_cost_embe_cost_dtls where 1=1 $jobNos_cond";
	$data_emb=sql_select($sql_emb);
	foreach($data_emb as $remb){
		if($remb[csf('emb_name')]==3)
		{
			$gmtsWashData[$remb[csf('job_no')]]['qty']+=$washQtyArr[$remb[csf('job_no')]][$remb[csf('id')]];
			$gmtsWashData[$remb[csf('job_no')]]['amount']+=$washAmtArr[$remb[csf('job_no')]][$remb[csf('id')]];
		}
		else
		{
			$embData[$remb[csf('job_no')]]['qty']+=$embQtyArr[$remb[csf('job_no')]][$remb[csf('id')]];
			$embData[$remb[csf('job_no')]]['amount']+=$embAmtArr[$remb[csf('job_no')]][$remb[csf('id')]];
		}
	}
	unset($data_emb);


	//echo $gmtsWashData['D n C-17-01621']['qty'].'=='; die;
	$trimData=array();
	$trim= new trims($condition);
	$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

	$trim= new trims($condition);
	$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();



	$sql_trim= "select id, job_no, trim_group, cons_uom from wo_pre_cost_trim_cost_dtls where 1=1 $jobNos_cond";
	$data_trim=sql_select($sql_trim);
	foreach($data_trim as $row){
		$trimData[$row[csf('job_no')]]['qty'][$row[csf('id')]][$row[csf('trim_group')]]=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
		$trimData[$row[csf('job_no')]]['amount'][$row[csf('id')]][$row[csf('trim_group')]]=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		$trimData[$row[csf('job_no')]]['cons_uom'][$row[csf('id')]][$row[csf('trim_group')]]=$row[csf('cons_uom')];
	}
	unset($data_trim);
	//Based On Budget end

	$sql_non="select b.id, b.wo_number, a.po_breakdown_id, a.item_category_id, a.supplier_order_quantity, a.amount from wo_non_order_info_mst b, wo_non_order_info_dtls a where b.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobNos_cond ";
	//echo $sql_non;//die;
	$sql_non_res=sql_select($sql_non);
	$booking_job_arr=array(); $yarn_ord_arr=array(); $woNos=""; $wo_rows=0;
	foreach($sql_non_res as $nrow)
	{
		$wo_rows++;
		$woNos.="'".$nrow[csf("wo_number")]."',";
		$woNo_ids.="'".$nrow[csf("id")]."',";
		$job_no=$po_job_arr[$nrow[csf("po_breakdown_id")]];
		if($nrow[csf("item_category_id")]==1)
		{
			$yarn_ord_arr[$job_no]['qty']+=$nrow[csf("supplier_order_quantity")];
			$yarn_ord_arr[$job_no]['amt']+=$nrow[csf("amount")];
			$yarn_ord_arr[$job_no]['po'].=$nrow[csf("po_breakdown_id")].",";
		}

		$booking_job_arr[$nrow[csf("wo_number")]]=$job_no;
	}
	//var_dump($yarn_ord_arr[$job_no]);//die;

	// echo '<pre>';
	// print_r($yarn_ord_arr);
	// echo '</pre>';

	unset($sql_non_res);

	$sql_book="select 0 as booking_id, id, booking_no, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, booking_type, process, emblishment_name, uom, wo_qnty, grey_fab_qnty, amount, 1 as type
	from wo_booking_dtls where status_active=1 and is_deleted=0 and wo_qnty>0 $jobNos_cond
	union all
	select distinct a.id as booking_id, b.id, a.ydw_no as booking_no, b.job_no as job_no, 0 as po_break_down_id, 0 as pre_cost_fabric_cost_dtls_id, 3 as booking_type, d.cons_process as process, 0 as emblishment_name, c.uom as uom,  b.yarn_wo_qty as wo_qnty,  0 as grey_fab_qnty,  b.amount as amount, 2 as type
	from wo_yarn_dyeing_mst a,  wo_yarn_dyeing_dtls b,  wo_pre_cost_fabric_cost_dtls c, wo_pre_cost_fab_conv_cost_dtls d
	where a.id=b.mst_id and b.job_no=c.job_no and c.id=d.fabric_description  and a.company_id in($cbo_company_id) and a.item_category_id  in(1, 24)  and a.entry_form in(1,41) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.yarn_wo_qty >0 and d.cons_process=30";

	//echo $sql_book;//die;

	$sql_book_res=sql_select($sql_book);
	$fab_booking_arr=array(); $conv_cost_arr=array(); $emb_booking_arr=array(); $trim_booking_arr=array();
	//echo $job_no;//die;
	//$job_no='FAL-18-00274';
	//var_dump($yarn_ord_arr[$job_no]);//die;
	foreach($sql_book_res as $brow)
	{
		$wo_rows++;
		//if($cbo_search_type==24){
			$woNos.="'".$brow[csf("booking_no")]."',";
			$woNo_ids.="'".$brow[csf("booking_id")]."',";
		//}else{
			//$woNos.="'".$brow[csf("booking_no")]."',";
			//$woNo_ids.="'".$brow[csf("id")]."',";
		//}


		$job_no="";
		$job_no=$brow[csf("job_no")];
		// if($brow[csf("booking_type")]==1)
		// {
			$jobno=$po_job_arr[$brow[csf("po_break_down_id")]];
			$fab_booking_arr[$jobno]['qty']+=$brow[csf("grey_fab_qnty")];
			$fab_booking_arr[$jobno]['amt']+=$brow[csf("amount")];
			$fab_booking_arr[$jobno]['po'].=$brow[csf("po_break_down_id")].",";
		// }
		// else if($brow[csf("booking_type")]==2)
		// {
			$trim_booking_arr[$job_no][$brow[csf("pre_cost_fabric_cost_dtls_id")]]['qty']+=$brow[csf("wo_qnty")];
			$trim_booking_arr[$job_no][$brow[csf("pre_cost_fabric_cost_dtls_id")]]['amt']+=$brow[csf("amount")];
			$trim_booking_arr[$job_no][$brow[csf("pre_cost_fabric_cost_dtls_id")]]['po'].=$brow[csf("po_break_down_id")].",";
		//}
		// else if($brow[csf("booking_type")]==3)
		// {
			if($cbo_search_type==24)
			{
				if($dtls_id_check[$brow[csf("id")]]=="")
				{
					$dtls_id_check[$brow[csf("id")]]=$brow[csf("id")];
					$ydw_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['qty']+=$brow[csf("wo_qnty")];
					$ydw_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['amt']+=$brow[csf("amount")];
					$ydw_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['po'].=$brow[csf("po_break_down_id")].",";
				}	
			}else{
				if($dtls_id_check[$brow[csf("id")]]=="")
				{
					$dtls_id_check[$brow[csf("id")]]=$brow[csf("id")];
					$conv_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['qty']+=$brow[csf("wo_qnty")];
					$conv_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['amt']+=$brow[csf("amount")];
					$conv_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['po'].=$brow[csf("po_break_down_id")].",";
				}	
			}
		//}
		// else if($brow[csf("booking_type")]==6)
		// {
			$emb_booking_arr[$job_no][$brow[csf("emblishment_name")]]['qty']+=$brow[csf("wo_qnty")];
			$emb_booking_arr[$job_no][$brow[csf("emblishment_name")]]['amt']+=$brow[csf("amount")];
			$emb_booking_arr[$job_no]['po'].=$brow[csf("po_break_down_id")].",";
		//}
		$booking_job_arr[$brow[csf("booking_no")]]=$job_no;
		$booking_job_ids_arr[$brow[csf("booking_id")]]=$job_no;
	}

	unset($sql_book_res);
	
	// echo "<pre>";
	// print_r($woNo_ids); echo "</pre>";//die;

	$woNos=implode(",",array_unique(explode(",",$woNos)));
	$woNos=chop($woNos,','); $woNos_cond="";
	$woNo_ids=chop($woNo_ids,','); $woNo_ids_cond="";

	if($db_type==2 && $tot_rows>1000)
	{
		$woNos_cond=" and (";
		$woNosArr=array_chunk(explode(",",$woNos),399);
		foreach($woNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$woNos_cond.=" b.work_order_no in ($idws) or ";
		}
		$woNos_cond=chop($woNos_cond,'or ');
		$woNos_cond.=")";

		$woNo_ids_cond=" or (";
		$woNosArr=array_chunk(explode(",",$woNo_ids),399);
		foreach($woNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$woNo_ids_cond.=" b.work_order_id in ($idws) or ";
		}
		$woNo_ids_cond=chop($woNo_ids_cond,'or ');
		$woNo_ids_cond.=")";
	}
	else
	{
		$woNos_cond=" and (b.work_order_no in ($woNos)";
		$woNo_ids_cond=" or b.work_order_id in ($woNo_ids))";
	}

	if($cbo_search_type==24){
		$sql_pi="select a.id, a.item_category_id, b.work_order_no,b.work_order_id, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $woNos_cond $woNo_ids_cond and a.item_category_id = 24  and a.importer_id = $cbo_company_id group by a.id, a.item_category_id, b.work_order_no,  b.uom, b.service_type,  b.embell_name,  b.item_group,b.work_order_id";
	}else{
		$sql_pi="select a.id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $woNos_cond $woNo_ids_cond  and a.importer_id = $cbo_company_id group by a.id, a.item_category_id, b.work_order_no,  b.uom, b.service_type,  b.embell_name,  b.item_group";
	}

	//echo $sql_pi;//die;

	//echo "<pre>";var_dump($booking_job_arr);echo "</pre>";die;//*/
	$sql_pi_res=sql_select($sql_pi);
	$pi_arr=array(); $pi_fab_arr=array(); $pi_conv_arr=array(); $pi_emb_arr=array(); $pi_trim_arr=array(); $pi_job_arr=array(); $piids=""; $pi_rows=0;$jobn='';
	foreach($sql_pi_res as $prow)
	{
		$pi_rows++;
		$piids.=$prow[csf("id")].",";


		$jobn=$booking_job_arr[$prow[csf("work_order_no")]];
		//echo "<pre>";var_dump($jobn);echo "</pre>";//*/
		//echo $jobn.'='.$prow[csf("work_order_no")].'<br>';
		// if($prow[csf("item_category_id")]==1)
		// {
			$pi_arr[$jobn]['qty']+=$prow[csf("quantity")];
			$pi_arr[$jobn]['amt']+=$prow[csf("amount")];
			$pi_arr[$jobn]['wo_no'].=$prow[csf("work_order_no")].",";
		// }
		// else if($prow[csf("item_category_id")]==3)
		// {
			$pi_fab_arr[$jobn][$prow[csf("uom")]]['qty']+=$prow[csf("quantity")];
			$pi_fab_arr[$jobn][$prow[csf("uom")]]['amt']+=$prow[csf("amount")];
			$pi_fab_arr[$jobn][$prow[csf("uom")]]['wo_no'].=$prow[csf("work_order_no")].",";
		// }
		// else if($prow[csf("item_category_id")]==4)
		// {
			$pi_trim_arr[$jobn][$prow[csf("item_group")]]['qty']+=$prow[csf("quantity")];
			$pi_trim_arr[$jobn][$prow[csf("item_group")]]['amt']+=$prow[csf("amount")];
			$pi_trim_arr[$jobn][$prow[csf("item_group")]]['wo_no'].=$prow[csf("work_order_no")].",";
		// }
		// else if($prow[csf("item_category_id")]==12)
		// {
			$pi_conv_arr[$jobn][$prow[csf("service_type")]][$prow[csf("uom")]]['qty']+=$prow[csf("quantity")];
			$pi_conv_arr[$jobn][$prow[csf("service_type")]][$prow[csf("uom")]]['amt']+=$prow[csf("amount")];
			$pi_conv_arr[$jobn][$prow[csf("service_type")]][$prow[csf("uom")]]['wo_no'].=$prow[csf("work_order_no")].",";
		// }
		// else if($prow[csf("item_category_id")]==24)
		// {

			$pi_trim_arr[$jobn][$prow[csf("item_group")]]['qty']=$prow[csf("quantity")];
			$pi_trim_arr[$jobn][$prow[csf("item_group")]]['amt']=$prow[csf("amount")];
			$pi_trim_arr[$jobn][$prow[csf("item_group")]]['wo_no'].=$prow[csf("work_order_no")].",";

			$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['qty']=$prow[csf("quantity")];
			$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['amt']=$prow[csf("amount")];
			$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['wo_no'].=$prow[csf("work_order_no")].",";
			$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['wo_ids'].=$prow[csf("work_order_id")].",";
		// }
		// else if($prow[csf("item_category_id")]==25)
		// {
			$pi_emb_arr[$jobn][$prow[csf("embell_name")]]['qty']+=$prow[csf("quantity")];
			$pi_emb_arr[$jobn][$prow[csf("embell_name")]]['amt']+=$prow[csf("amount")];
			$pi_emb_arr[$jobn][$prow[csf("embell_name")]]['wo_no'].=$prow[csf("work_order_no")].",";
		// }
		$pi_job_arr[$prow[csf("id")]]=$jobn;
	}
	unset($sql_pi_res);
	/*echo "<pre>";var_dump($pi_conv_pi_arr);echo "</pre>";//*/
	$piids=implode(",",array_unique(explode(",",$piids)));
	$piids=chop($piids,','); $rec_pi_cond="";

	if($db_type==2 && $pi_rows>1000)
	{
		$rec_pi_cond=" and (";
		$piidsArr=array_chunk(explode(",",$piids),399);
		foreach($piidsArr as $idpis)
		{
			$idpis=implode(",",$idpis);
			$rec_pi_cond.=" a.booking_id in ($idpis) or ";
		}
		$rec_pi_cond=chop($rec_pi_cond,'or ');
		$rec_pi_cond.=")";
	}
	else
	{
		if($piids) $rec_pi_cond=" and a.booking_id in ($piids)";
	}

	$trim_id_arr=return_library_array( "select id, item_group_id from product_details_master where item_category_id=4", "id", "item_group_id");
	$mmr_woNos_cond=""; $mmr_woNo_ids_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$mmr_woNos_cond=" and ((";
		$woNosArr=array_chunk(explode(",",$woNos),399);
		foreach($woNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$mmr_woNos_cond.=" a.booking_no in ($idws) or ";
		}
		$mmr_woNos_cond=chop($mmr_woNos_cond,'or ');
		$mmr_woNos_cond.=")";

		$mmr_woNo_ids_cond=" or (";
		$woNo_ids_Arr=array_chunk(explode(",",$woNo_ids),399);
		foreach($woNo_ids_Arr as $idws)
		{
			$idws=implode(",",$idws);
			$mmr_woNos_cond.=" a.booking_id in ($idws) or ";
		}
		$mmr_woNo_ids_cond=chop($mmr_woNo_ids_cond,'or ');
		$mmr_woNo_ids_cond.="))";
	}
	else
	{
		$mmr_woNos_cond=" and (a.booking_no in ($woNos)";
		$mmr_woNo_ids_cond=" or a.booking_id in ($woNo_ids))";
	}
	//echo $woNo_ids;
	$sql_rec="select a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category in (1,5,6,7,22,23,24) and a.entry_form=1 and a.receive_purpose = 2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $mmr_woNos_cond $mmr_woNo_ids_cond"; //$rec_pi_cond
	//echo $sql_rec;//die;
	$sql_rec_res=sql_select($sql_rec);
	$rec_yarn_arr=array(); $rec_fab_arr=array(); $rec_trim_arr=array();

	foreach($sql_rec_res as $rrow)
	{
		$jobno='';
		// if($cbo_search_type==24){
			if($rrow[csf("receive_basis")]==1) $jobno=$pi_job_arr[$rrow[csf("booking_id")]];
			else if($rrow[csf("receive_basis")]==2) $jobno=$booking_job_ids_arr[$rrow[csf("booking_id")]];
			//echo $jobno.'===_';//die;
		// }else{
			if($rrow[csf("receive_basis")]==1) $jobno=$pi_job_arr[$rrow[csf("booking_id")]];
			else if($rrow[csf("receive_basis")]==2) $jobno=$booking_job_arr[$rrow[csf("booking_no")]];
		//}

		//echo $jobno.'=<br>';//die;
		// if($rrow[csf("item_category")]==1)
		// {
			$rec_yarn_arr[$jobno]['qty']+=$rrow[csf("order_qnty")];
			$rec_yarn_arr[$jobno]['amt']+=$rrow[csf("order_amount")];
			$rec_yarn_arr[$jobno]['booking_id'].=$rrow[csf("booking_id")].",";
		// }
		// else if($rrow[csf("item_category")]==3 || $rrow[csf("item_category")]==2)
		// {
			$rec_fab_arr[$jobno][$rrow[csf("order_uom")]]['qty']+=$rrow[csf("order_qnty")];
			$rec_fab_arr[$jobno][$rrow[csf("order_uom")]]['amt']+=$rrow[csf("order_amount")];
			$rec_fab_arr[$jobno][$rrow[csf("order_uom")]]['booking_id'].=$rrow[csf("booking_id")].",";
		// }
		// else if($rrow[csf("item_category")]==4)
		// {
			$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['qty']+=$rrow[csf("order_qnty")];
			$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['amt']+=$rrow[csf("order_amount")];
			$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['booking_id'].=$rrow[csf("booking_id")].",";
			$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['prod_id'].=$rrow[csf("prod_id")].",";
		// }
		// elseif($rrow[csf("item_category")]==24)
		// {
			$rec_yarn_arr[$jobno]['qty']+=$rrow[csf("order_qnty")];
			$rec_yarn_arr[$jobno]['amt']+=$rrow[csf("order_amount")];
			$rec_yarn_arr[$jobno]['booking_id'].=$rrow[csf("booking_id")].",";
		//}
	}

	unset($sql_rec_res);

	$sql_conv="select job_no, process_id, batch_issue_qty, amount from pro_grey_batch_dtls where status_active=1 and is_deleted=0 $jobNos_cond";
	// echo $sql_conv;
	$sql_conv_res=sql_select($sql_conv);
	$rec_conv_arr=array();
	foreach($sql_conv_res as $srow)
	{
		$rec_conv_arr[$srow[csf("job_no")]][$srow[csf("process_id")]]['qty']+=$srow[csf("batch_issue_qty")];
		$rec_conv_arr[$srow[csf("job_no")]][$srow[csf("process_id")]]['amt']+=$srow[csf("amount")];
	}
	unset($sql_conv_res);

	//----------------------------------------- For knitting producion-----------------------------

	$plan_job_arr=array();
	$book_job_arr=array();
	$all_po_ids=chop($poIds,",");
	$sql="select dtls_id, po_id from ppl_planning_entry_plan_dtls where po_id in ($all_po_ids) and status_active=1 and is_deleted=0";
	$all_plan_id="";
	foreach (sql_select($sql) as $val)
	{
		$all_plan_id.=$val[csf("dtls_id")].",";
		$plan_job_arr[$val[csf("dtls_id")]]=$po_job_arr[$val[csf("po_id")]];
	}
	$all_plan_id=chop($all_plan_id,",");
	if($all_plan_id=="") $all_plan_id=0;
	$sql_conv="select a.booking_id as plan_id, b.order_id, b.grey_receive_qnty as qntity from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in($all_plan_id)";
	//echo $sql_conv;die;
	$sql_conv_res=sql_select($sql_conv);

	foreach($sql_conv_res as $srow)
	{
		$rec_conv_arr[$job][1]['qty']+=$srow[csf("qntity")];
		$rec_conv_arr[$job][1]['booking_id'] .=$srow[csf("plan_id")].",";

		$job=$plan_job_arr[$srow[csf("plan_id")]];

	}
	unset($sql_conv_res);

	//-------------------------------------- Knit Gray fabric ---------------------------------------------

	$sql="select id as booking_id, booking_no, job_no from wo_booking_mst where status_active=1 and is_deleted=0";
	foreach (sql_select($sql) as $val)
	{
		$book_job_arr[$val[csf("booking_id")]]=$val[csf("job_no")];
	}

	$sql_conv="select a.booking_id, b.order_qnty as qntity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.receive_basis in (11) and a.entry_form=22 and a.knitting_source=3";
	//echo $sql_conv;
	$sql_conv_res=sql_select($sql_conv);
	foreach($sql_conv_res as $srow)
	{
		$rec_conv_arr[$job][1]['qty']+=$srow[csf("qntity")];
		$rec_conv_arr[$job][1]['booking_id'] .=$srow[csf("booking_id")].",";

		$job=$book_job_arr[$srow[csf("booking_id")]];

	}
	unset($sql_conv_res);

	//-------------------------------------------------------------------------------------------------------



	$sql_gmts="select po_break_down_id, embel_name, production_quantity from pro_garments_production_mst where production_type=3 and status_active=1 and is_deleted=0 $po_emb_cond";
	//echo $sql_gmts;die;
	$sql_gmts_res=sql_select($sql_gmts);
	$rec_emb_arr=array();
	foreach($sql_gmts_res as $srow)
	{
		$rec_emb_arr[$po_job_arr[$srow[csf("po_break_down_id")]]][$srow[csf("embel_name")]]['qty']+=$srow[csf("production_quantity")];
		$rec_emb_arr[$po_job_arr[$srow[csf("po_break_down_id")]]][$srow[csf("embel_name")]]['amt']+=0;//$srow[csf("amount")];
		$rec_emb_arr[$po_job_arr[$srow[csf("po_break_down_id")]]][$srow[csf("embel_name")]]['po_id'].=$srow[csf("po_break_down_id")].",";
	}
	unset($sql_gmts_res);

	$sql_trims_rcv = "select b.id as dtls_id,b.mst_id,b.trans_id, b.booking_id, b.item_group_id, b.rate, a.po_breakdown_id, a.order_amount, a.quantity
    					from order_wise_pro_details a, inv_trims_entry_dtls b
   						where a.dtls_id = b.id and b.trans_id = a.trans_id and b.prod_id = a.prod_id and a.entry_form = 24 and a.trans_type = 1 and a.po_breakdown_id in($all_po_ids) and b.status_active = 1 and a.status_active = 1 and b.rate > 0 and a.order_amount > 0";

    //echo $sql_trims_rcv;

    $sql_trims_rcv = sql_select($sql_trims_rcv);
    $trims_rcv_arr=array();
    foreach ($sql_trims_rcv as $row) {
    	if(isset($trims_rcv_arr[$row[csf('item_group_id')]])) {
    		$trims_rcv_arr[$row[csf('item_group_id')]]['order_amount'] += $row[csf('order_amount')];
		    $trims_rcv_arr[$row[csf('item_group_id')]]['quantity'] += $row[csf('quantity')];
    	} else {
    		$trims_rcv_arr[$row[csf('item_group_id')]]['po_id'] = $row[csf('po_breakdown_id')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['item_group_id'] = $row[csf('item_group_id')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['rate'] = $row[csf('rate')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['order_amount'] = $row[csf('order_amount')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['quantity'] = $row[csf('quantity')];
    	}    	
    }
    //unset($sql_trims_rcv);

    $trims_rcv_id_arr=array();
    foreach ($sql_trims_rcv as $row) {
    	$trims_rcv_id_arr[$row[csf('item_group_id')]]['mst_id'] .= $row[csf('mst_id')].',';
    	$trims_rcv_id_arr[$row[csf('item_group_id')]]['trans_id'] .= $row[csf('trans_id')].',';
	}   	
    
    unset($sql_trims_rcv);

	//print_r($pi_arr['D n C-17-01621']['1']); die;

	//print_r ($trimData['D n C-17-01621']['qty']).'=='; die;

	//echo $jobNos;
	$jobNumber = str_replace("'","",explode(',',$jobNos));

	foreach ($jobNumber as $value) {
		$costPerArr=$condition->getCostingPerArr();

		$costPerQty=$costPerArr[$value];
	}
	//var_dump($costPerQty);

	//$condition->init();
		//$jobNumber
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		//var_dump($costPerQty); //die;
	ob_start();

	?>

    <fieldset>
        <table width="1600"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="19">
                    <? echo $company_arr[$cbo_company_id]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="19"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold" colspan="19"> <? if( $txt_date_from!="" && $txt_date_to!="" ) echo "From ".change_date_format($txt_date_from)." To ".change_date_format($txt_date_to);?></td>
            </tr>
        </table>
        <table width="1600" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50" rowspan="2">SL</th>
                    <th colspan="8">Style Info</th>
                    <th colspan="4">Based On Budget</th>
                    <th colspan="6">Actual</th>
                </tr>
                <tr>
                    <th width="100">Buyer</th>
                    <th width="60">Job No</th>
                    <th width="60">Job Year</th>
                    <th width="110">Style Ref.</th>
                    <th width="100">Dealing Merchant</th>
                    <th width="80">Style Qty</th>
                    <th width="60">Unit Price</th>
                    <th width="90">Style FOB Value</th>

                    <th width="160">Particulars</th>
                    <th width="80">Total Req.Qty</th>
                    <th width="50">UOM</th>
                    <th width="90">Total Amount</th>

                    <th width="80">Total WO Qty</th>
                    <th width="90">Total WO Value</th>
                    <th width="80">Total PI Qty</th>
                    <th width="90">Total PI Value</th>
                    <th width="80">Total In-House Qty</th>
                    <th>Total In-House value</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:1617px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1600" rules="all" id="" >
            <?
			//print_r($job_arr); //"jahid";die;
            foreach($job_arr as $jobval)
			{
				$job_no=$jobval["job_no"];
				$trims_has +=count($trimData[$job_no]['qty']);
				//echo $yarn_ord_arr[$job_no]['po'];
			}

			$i=1;

			foreach($job_arr as $jobval)
			{
				if($trims_has>0)
				{
					$colspan_num=2;
				}
				else
				{
					$colspan_num="";
				}

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$job_no=$jobval["job_no"];
				$yarn_qty=0; $fabric_qty=0;
				$yarn_amount=0; $fabric_amount=0;
				$yarn_count=1; $fabric_count=0; $knit_count=0; $embl_count=1; $wash_count=1; $trim_count=0;
				$yarn_qty=$yarn_arr[$job_no]['qty'];
				$yarn_amount=$yarn_arr[$job_no]['amount'];

				$fabric_count=count($fabPurArr[$job_no]['qty']);
				//$knit_count=count($knitData[$job_no]['qty']);
				$trim_count=count($trimData[$job_no]['qty']);

				foreach ($knitData[$job_no]['qty'] as $process) {
					foreach ($process as $uom) {
						$knit_count++;
					}
				}

				$row_spn=$yarn_count+$fabric_count+$knit_count+$embl_count+$wash_count+$trim_count;
				$z=1;

				$yarn_wo_qty=0; $yarn_wo_amt=0; $pi_yarn_qty=0; $pi_yarn_amt=0; $rec_yarn_qty=0; $rec_yarn_amt=0;
				$yarn_wo_qty=$yarn_ord_arr[$job_no]['qty'];
				$yarn_wo_amt=$yarn_ord_arr[$job_no]['amt'];
				$all_po_id=implode(",", array_unique(explode(",", chop($yarn_ord_arr[$job_no]['po'],","))));
				//$woStatus = $cbo_search_type;

				$pi_yarn_qty=$pi_arr[$job_no]['qty'];
				$pi_yarn_amt=$pi_arr[$job_no]['amt'];
				$all_wo_no=implode(",", array_unique(explode(",", chop($pi_arr[$job_no]['wo_no'],","))));
				$all_pi_id=implode(",", array_unique(explode(",", chop($pi_arr[$job_no]['pi_id'],","))));

				if($cbo_search_type == 24){
					$rec_yarn_inhouse_qty=$rec_yarn_arr[$job_no]['qty'];
					$rec_yarn_inhouse_amt=$rec_yarn_arr[$job_no]['amt'];
				}else{
					$rec_yarn_qty=$rec_yarn_arr[$job_no]['qty'];
					$rec_yarn_amt=$rec_yarn_arr[$job_no]['amt'];
				}


				$all_booking_nos=implode(",", array_unique(explode(",", chop($rec_yarn_arr[$job_no]['booking_id'],","))));

				$data="wo_yarn_cost_dtls**".$job_no."**".$all_po_id."**".$cbo_search_type;

				$pi_data="pi_yarn_cost_dtls**".$job_no."**".$all_wo_no."**".$cbo_search_type."**".$all_pi_id;

				$inhouse_data="inhouse_yarn_cost_dtls**".$job_no."**".$all_booking_nos."**".$cbo_search_type;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<? if($z==1) { ?>
                	<td width="50" align="center" rowspan="<? echo $row_spn; ?>" title="<? echo $trim_count;  ?>"><? echo $i; ?></td>
                    <td width="100" rowspan="<? echo $row_spn; ?>"><? echo $buyer_arr[$jobval["buyer_name"]]; ?></td>
                    <td width="60" align="center" rowspan="<? echo $row_spn; ?>"><? echo $jobval["job_prefix_num"]; ?></td>
                    <td width="60" align="center" rowspan="<? echo $row_spn; ?>"><? echo $jobval["job_year"]; ?></td>
                    <td width="110" rowspan="<? echo $row_spn; ?>" style="word-break:break-all; word-wrap:break-word;"><p><? echo $jobval["style_ref_no"]; ?></p></td>
                    <td width="100" rowspan="<? echo $row_spn; ?>"><? echo $dealing_merchant_arr[$jobval["dealing_marchant"]]; ?></td>
                    <td width="80" align="right" rowspan="<? echo $row_spn; ?>"><? echo number_format($jobval["job_qty"],0); ?></td>
                    <td width="60" align="right" rowspan="<? echo $row_spn; ?>"><? echo number_format($jobval["avg_price"],4); ?></td>
                    <td width="90" align="right" rowspan="<? echo $row_spn; ?>"><? echo number_format($jobval["job_amount"],2); ?></td>
                    <? } ?>
                    <td width="160" colspan="<? echo $colspan_num; ?>"><? echo "Yarn Cost"; ?></td>
                    <td width="80" align="right"><? echo number_format($yarn_qty,2); ?></td>
                    <td width="50"><? echo "KG"; ?></td>
                    <td width="90" align="right"><? echo number_format($yarn_amount,2); ?></td>

                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                    		<? echo number_format($yarn_wo_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($yarn_wo_amt,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                    		<? echo number_format($pi_yarn_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($pi_yarn_amt,2); ?></td>

                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                    		<? echo number_format($rec_yarn_qty,2); ?>
                    	</a>
                    </td>
                    <td align="right"><? echo number_format($rec_yarn_amt,2); ?></td>
				</tr>
                <?
                foreach($fabPurArr[$job_no]['qty'] as $fuom=>$fabdata)
				{
					$fab_amount=0; $fab_booking_qty=0; $fab_booking_amt=0; $pi_fab_qty=0; $pi_fab_amt=0; $rec_fab_qty=0; $rec_fab_amt=0;
					$fab_amount=$fabPurArr[$job_no]['amount'][$fuom];
					$fab_booking_qty=$fab_booking_arr[$job_no]['qty'];
					$fab_booking_amt=$fab_booking_arr[$job_no]['amt'];
					$fab_po_id=implode(",", array_unique(explode(",", chop($fab_booking_arr[$job_no]['po'],","))));

					$pi_fab_qty=$pi_fab_arr[$job_no][$fuom]['qty'];
					$pi_fab_amt=$pi_fab_arr[$job_no][$fuom]['amt'];
					$all_wo_no=implode(",", array_unique(explode(",", chop($pi_fab_arr[$job_no][$fuom]['wo_no'],","))));

					$rec_fab_qty=$rec_fab_arr[$job_no][$fuom]['qty'];
					$rec_fab_amt=$rec_fab_arr[$job_no][$fuom]['amt'];
					$all_booking_nos=implode(",", array_unique(explode(",", chop($rec_fab_arr[$job_no][$fuom]['booking_id'],","))));

					$data="wo_purchase_fabric_dtls**".$job_no."**".$fab_po_id."**".$fuom;

					$pi_data="pi_purchase_fabric_dtls**".$job_no."**".$all_wo_no."**".$fuom;

					$inhouse_data="inhouse_fabric_purchase_dtls**".$job_no."**".$all_booking_nos."**".$fuom;
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="160" colspan="<? echo $colspan_num; ?>"><? echo "Fabric Purchase"; ?></td>
                        <td width="80" align="right"><? echo number_format($fabdata,2); ?></td>
                        <td width="50"><? echo $unit_of_measurement[$fuom]; ?></td>
                        <td width="90" align="right"><? echo number_format($fab_amount,2); ?></td>
                        <td width="80" align="right">
                        	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                        		<? echo number_format($fab_booking_qty,2); ?>
                        	</a>
                        </td>
                    	<td width="90" align="right"><? echo number_format($fab_booking_amt,2); ?></td>
                        <td width="80" align="right">
                        	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                        		<? echo number_format($pi_fab_qty,2); ?>
                        	</a>
                        </td>
                    	<td width="90" align="right"><? echo number_format($pi_fab_amt,2); ?></td>
                        <td width="80" align="right">
                        	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                        		<? echo number_format($rec_fab_qty,2); ?> </a></td>

                    	<td align="right"><? echo number_format($rec_fab_amt,2); ?></td>
                    </tr>
					<?
				}
				
                foreach($knitData[$job_no]['qty'] as $process=>$uomdata)
				{
					
					foreach($uomdata as $kuom=>$processqty)
					{
						$con_amount=0; $conv_booking_qty=0; $conv_booking_amt=0; $pi_conv_qty=0; $pi_conv_amt=0; $rec_conv_qty=0; $rec_conv_amt=0;
						$con_amount=$knitData[$job_no]['amount'][$process][$kuom];

						
						$conv_booking_qty=$conv_cost_arr[$job_no][$process][$kuom]['qty'];
						$conv_booking_amt=$conv_cost_arr[$job_no][$process][$kuom]['amt'];
						
						$ydw_cost_qty = $ydw_cost_arr[$job_no][$process][$kuom]['qty'];
						$ydw_cost_amt=$ydw_cost_arr[$job_no][$process][$kuom]['amt'];
						

						$all_po_id=implode(",", array_unique(explode(",", chop($conv_cost_arr[$job_no][$process][$kuom]['po'],","))));

						$pi_conv_qty=$pi_conv_arr[$job_no][$process][$kuom]['qty'];
						$pi_conv_amt=$pi_conv_arr[$job_no][$process][$kuom]['amt'];

						$pi_conv_pi_qty=$pi_conv_pi_arr[$job_no][$kuom]['qty'];
						$pi_conv_pi_amt=$pi_conv_pi_arr[$job_no][$kuom]['amt'];

						$all_wo_no=implode(",", array_unique(explode(",", chop($pi_conv_arr[$job_no][$process][$kuom]['wo_no'],","))));
						$all_wo_no_pi=implode(",", array_unique(explode(",", chop($pi_conv_pi_arr[$job_no][$kuom]['wo_no'],","))));
						$all_wo_no_ids_pi=implode(",", array_unique(explode(",", chop($pi_conv_pi_arr[$job_no][$kuom]['wo_ids'],","))));

						$rec_conv_qty=$rec_conv_arr[$job_no][$process]['qty'];
						$rec_conv_amt=$rec_conv_arr[$job_no][$process]['amt'];
						//echo "<pre>";print_r($rec_ydw_wo_qty);

						$inhouse_booking="0";
						if($process==1)
						{
							$inhouse_booking=$rec_conv_arr[$job_no][1]['booking_id'];
							$inhouse_booking= ($inhouse_booking) ? $inhouse_booking : "0";
						}else{
							$inhouse_booking=$rec_yarn_arr[$job_no]['booking_id'];
							$inhouse_booking= ($inhouse_booking) ? $inhouse_booking : "0";
						}

						$data="wo_knitting_dtls**".$job_no."**".$all_po_id."**".$cbo_search_type."**".$process."**".$kuom;

						$pi_data="pi_knitting_dtls**".$job_no."**".$all_wo_no_pi."**".$kuom."**".$process."**".$all_wo_no_ids_pi."**".$cbo_search_type;

						$inhouse_data="inhouse_knitting_dtls**".$job_no."**$inhouse_booking**".$kuom."**".$process."**".$cbo_search_type;

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="160" colspan="<? echo $colspan_num; ?>"><? echo $conversion_cost_head_array[$process]; ?></td>
                            <td width="80" align="right"><? echo number_format($processqty,2); ?></td>
                            <td width="50"><? echo $unit_of_measurement[$kuom]; ?></td>
                            <td width="90" align="right"><? echo number_format($con_amount,2); ?></td>
                            <td width="80" align="right" title="<? echo $conv_booking_qty;?>">
                            	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="#">
                            		<? 
										if($cbo_search_type == 24){
											echo number_format($ydw_cost_qty,2); 
										}else{
											echo number_format($conv_booking_qty,2);
										}
									?>
                                        
                            	</a>
                            </td>
                    		<td width="90" align="right"><? echo number_format($conv_booking_amt,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                            		<? echo number_format($pi_conv_pi_qty,2); ?>
                            	</a>
                            </td>
                    		<td width="90" align="right"><? echo number_format($pi_conv_pi_amt,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                            		<?
										if($cbo_search_type == 24){
											echo number_format($rec_yarn_inhouse_qty,2);
										}else{
											echo number_format($rec_conv_qty,2);
										}
									?>
                            	</a>
                            </td>
                    		<td align="right">
								<?
									if($cbo_search_type == 24){
										echo number_format($rec_yarn_inhouse_amt,2);
									}else{
										echo number_format($rec_conv_amt,2);
									}

								?>
							</td>
                        </tr>
						<?
					}
				}
				//echo $embData[$job_no]['qty'];
				$emb_qty=0; $emb_amount=0; $emb_booking_qty=0; $emb_booking_amt=0; $pi_emb_qty=0; $pi_emb_amt=0; $rec_emb_qty=0; $rec_emb_amt=0;
				$emb_qty=$embData[$job_no]['qty'];
				$emb_amount=$embData[$job_no]['amount'];
				$emb_booking_qty=$emb_booking_arr[$job_no][1]['qty'];
				$emb_booking_amt=$emb_booking_arr[$job_no][1]['amt'];

				$all_po_id=implode(",", array_unique(explode(",", chop($emb_booking_arr[$job_no]['po'],","))));

				$pi_emb_qty=$pi_emb_arr[$job_no][1]['qty'];
				$pi_emb_amt=$pi_emb_arr[$job_no][1]['amt'];
				$all_wo_no=implode(",", array_unique(explode(",", chop($pi_emb_arr[$job_no][1]['wo_no'],","))));

				$rec_emb_qty=$rec_emb_arr[$job_no][1]['qty'];
				$rec_emb_amt=$rec_emb_arr[$job_no][1]['amt'];
				$all_po_nos=implode(",", array_unique(explode(",", chop($rec_emb_arr[$job_no][1]['po_id'],","))));

				$data="wo_embellisment_dtls**".$job_no."**".$all_po_id;

				$pi_data="pi_embl_dtls**".$job_no."**".$all_wo_no."**".$cbo_search_type;

				$inhouse_data="inhouse_embl_dtls**".$job_no."**".$all_po_nos."**".$cbo_search_type;

				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="160" colspan="<? echo $colspan_num; ?>"><? echo "Embel. Cost"; ?></td>
                    <td width="80" align="right"><? echo number_format($emb_qty,2); ?></td>
                    <td width="50"><? echo "Pcs"; ?></td>
                    <td width="90" align="right"><? echo number_format($emb_amount,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                    		<? echo number_format($emb_booking_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($emb_booking_amt,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                    		<? echo number_format($pi_emb_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($pi_emb_amt,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                    		<? echo number_format($rec_emb_qty,2); ?>
                    	</a>
                    </td>
                    <td align="right"><? echo number_format($rec_emb_amt,2); ?></td>
                </tr>
				<?
				//echo $embData[$job_no]['qty'];
				$wash_qty=0; $wash_amount=0; $wash_booking_qty=0; $wash_booking_amt=0; $pi_wash_qty=0; $pi_wash_amt=0; $rec_wash_qty=0; $rec_wash_amt=0;
				$wash_qty=$gmtsWashData[$job_no]['qty'];
				$wash_amount=$gmtsWashData[$job_no]['amount'];
				$wash_booking_qty=$emb_booking_arr[$job_no][3]['qty'];
				$wash_booking_amt=$emb_booking_arr[$job_no][3]['amt'];

				$all_po_id=implode(",", array_unique(explode(",", chop($emb_booking_arr[$job_no]['po'],","))));

				$pi_wash_qty=$pi_emb_arr[$job_no][3]['qty'];
				$pi_wash_amt=$pi_emb_arr[$job_no][3]['amt'];
				$all_wo_no=implode(",", array_unique(explode(",", chop($pi_emb_arr[$job_no][3]['wo_no'],","))));

				$rec_wash_qty=$rec_emb_arr[$job_no][3]['qty'];
				$rec_wash_amt=$rec_emb_arr[$job_no][3]['amt'];
				$all_po_nos=implode(",", array_unique(explode(",", chop($rec_emb_arr[$job_no][3]['po_id'],","))));

				$data="wo_gmts_wash_dtls**".$job_no."**".$all_po_id;

				$pi_data="pi_gmt_wash_dtls**".$job_no."**".$all_wo_no;

				$inhouse_data="inhouse_gmts_wash_dtls**".$job_no."**".$all_po_nos;

				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="160" colspan="<? echo $colspan_num; ?>"><? echo "Gmts.Wash"; ?></td>
                    <td width="80" align="right"><? echo number_format($wash_qty,2); ?></td>
                    <td width="50"><? echo "Pcs"; ?></td>
                    <td width="90" align="right"><? echo number_format($wash_amount,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                    		<? echo number_format($wash_booking_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($wash_booking_amt,2); ?></td>

                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                    		<? echo number_format($pi_wash_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($pi_wash_amt,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                    		<? echo number_format($rec_wash_qty,2); ?>
                    	</a>
                    </td>
                    <td align="right"><? echo number_format($rec_wash_amt,2); ?></td>
                </tr>
				<?
				$x=1;
				//die;
				foreach($trimData[$job_no]['qty'] as $id=>$iddata)
				{
					foreach($iddata as $trim_group=>$trim_qty)
					{
						$trim_amount=0; $trim_uom=0; $trim_booking_qty=0; $trim_booking_amt=0; $pi_trim_qty=0; $pi_trim_amt=0; $rec_trim_qty=0; $rec_trim_amt=0;
						$trim_amount=$trimData[$job_no]['amount'][$id][$trim_group];
						$trim_uom=$trimData[$job_no]['cons_uom'][$id][$trim_group];
						$trim_booking_qty=$trim_booking_arr[$job_no][$id]['qty'];
						$trim_booking_amt=$trim_booking_arr[$job_no][$id]['amt'];

						$all_po_id=implode(",", array_unique(explode(",", chop($trim_booking_arr[$job_no][$id]['po'],","))));

						$pi_trim_qty=$pi_trim_arr[$job_no][$trim_group]['qty'];
						$pi_trim_amt=$pi_trim_arr[$job_no][$trim_group]['amt'];
						$all_wo_no=implode(",", array_unique(explode(",", chop($pi_trim_arr[$job_no][$trim_group]['wo_no'],","))));

						$rec_trim_qty=$trims_rcv_arr[$trim_group]['quantity'];
						//$rec_id=$trims_rcv_arr[$trim_group]['mst_id'];
						$trans_ids=$trims_rcv_id_arr[$trim_group]['trans_id'];
						//echo $trans_ids.'a';
						$rec_trim_amt=$trims_rcv_arr[$trim_group]['order_amount'];

						$all_book_nos=implode(",", array_unique(explode(",", chop($rec_trim_arr[$job_no][$trim_group]['booking_id'],","))));
						$all_prod_ids=implode(",", array_unique(explode(",", chop($rec_trim_arr[$job_no][$trim_group]['prod_id'],","))));
						$all_trans_ids=implode(",", array_unique(explode(",", chop($trans_ids,","))));
						//echo $all_trans_ids.'b';
						$data="wo_trims_dtls**".$job_no."**".$all_po_id."**".$trim_uom."**".$id;
						//'wo_trims_dtls**MF-20-00228**40848**1**35597'

						$pi_data="pi_trims_dtls**".$job_no."**".$all_wo_no."**".$trim_uom."**".$trim_group;
						// 'pi_trims_dtls**MF-20-00228**MF-TB-20-00092**1**154'

						//$inhouse_data="inhouse_trims_dtls**".$job_no."**".$all_wo_no."**".$trim_uom."**".$trim_group;
						$inhouse_data="inhouse_trims_dtls**".$all_trans_ids;
						//'inhouse_trims_dtls**MF-20-00228****1**154','850px'

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
							<? if($x==1) { ?>
                            <td width="20" align="center" rowspan="<? echo $trim_count; ?>"><div class='rotate'><? echo "Trims"; ?></div></td>
                            <? $x++; } ?>
                            <td width="140"><? echo $trim_name_arr[$trim_group]; ?></td>
                            <td width="80" align="right"><? echo number_format($trim_qty,2); ?></td>
                            <td width="50"><? echo $unit_of_measurement[$trim_uom]; ?></td>
                            <td width="90" align="right"><? echo number_format($trim_amount,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                            		<? echo number_format($trim_booking_qty,2); ?>
                            	</a>
                            </td>
                    		<td width="90" align="right"><? echo number_format($trim_booking_amt,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                            		<? echo number_format($pi_trim_qty,2); ?>
                            	</a>
                            </td>
                    		<td width="90" align="right"><? echo number_format($pi_trim_amt,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                            		<? echo number_format($rec_trim_qty,2); ?>
                            	</a>
                            </td>
                    		<td align="right"><? echo number_format($rec_trim_amt,2); ?></td>
                        </tr>
						<?
					}
				}
                $i++;
			}
			?>

            <?
			//=========================================
			die;
			$sql_result=sql_select($sql); $i=1;$item_group_sammary=array();
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($row[csf("type")]==1)
				{
					if ($row[csf("wo_type")]==2)
					{
						if ($row[csf("is_short")]==1)
						{
							$wo_type="Short";
							$wo_typw_id=1;
						}
						else
						{
							$wo_type="Main";
							$wo_typw_id=2;
						}
					}
					elseif($row[csf("wo_type")]==5)
					{
						$wo_type="Sample With Order";
						$wo_typw_id=3;
					}
				}
				else
				{
					$wo_type="Sample Without Order";
					$wo_typw_id=4;
				}
				$lead_time=datediff("d", $row[csf("wo_date")], $row[csf("delivery_date")]);
				//$job_number=implode(",",array_unique(explode(",",$row[csf("job_no")])));
				$rcv_qnty=$rcv_balance=$ontimeRcv=0;$rcv_mst_id=$po_id="";

				if($row[csf('type')]==1)
				{
					$po_id=$row[csf("po_id")];
					$rcv_qnty=$rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf("trim_group")]][$description_arr[$row[csf("dtls_id")]]]["rcv_qnty"];
					$rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
					$rcv_date_arr=array();
					$rcv_date_arr=array_unique(explode(",",chop($rcv_data_po[$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$description_arr[$row[csf("dtls_id")]]]["receive_date"],",")));
					foreach($rcv_date_arr as $rcv_date)
					{
						if($rcv_date!="" && $rcv_date!="0000-00-00")
						{
							if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]))
							{
								$ontimeRcv+=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$description_arr[$row[csf("dtls_id")]]]["rcv_qnty"];
								$rcv_mst_id.=$rcv_data_po_ontime[$rcv_date][$row[csf('book_mst_id')]][$row[csf("po_id")]][$row[csf('trim_group')]][$description_arr[$row[csf("dtls_id")]]]["mst_id"].",";
							}
						}
					}

				}
				else
				{
					$po_id=$row[csf("po_id")];
					$rcv_qnty=$rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf("trim_group")]][$description_arr[$row[csf("dtls_id")]]]["rcv_qnty"];
					$rcv_balance=$row[csf("wo_qnty")]-$rcv_qnty;
					$rcv_date_arr=array();
					$rcv_date_arr=array_unique(explode(",",chop($rcv_data_nonOrder[$row[csf('book_mst_id')]][$row[csf('trim_group')]][$description_arr[$row[csf("dtls_id")]]]["receive_date"],",")));
					foreach($rcv_date_arr as $rcv_date)
					{
						if($rcv_date!="" && $rcv_date!="0000-00-00")
						{
							if(strtotime($rcv_date)<=strtotime($row[csf("delivery_date")]))
							{
								$ontimeRcv+=$rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$description_arr[$row[csf("dtls_id")]]]["rcv_qnty"];
								$rcv_mst_id.=$rcv_data_nonOrder_on_time[$rcv_date][$row[csf('book_mst_id')]][$row[csf('trim_group')]][$description_arr[$row[csf("dtls_id")]]]["mst_id"].",";
							}
						}
					}
				}


				$rcv_mst_id=chop($rcv_mst_id,",");
				$otd=0;
				$otd=(($ontimeRcv/$row[csf("wo_qnty")])*100);


				if($row[csf("wo_qnty")]>0 && $row[csf("trim_group")]>0)
				{
					$item_group_sammary[$row[csf("trim_group")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$item_group_sammary[$row[csf("trim_group")]]["ontimeRcv"]+=$ontimeRcv;
					$item_group_sammary[$row[csf("trim_group")]]["rcv_qnty"]+=$rcv_qnty;
					$item_group_sammary[$row[csf("trim_group")]]["trim_group"]=$row[csf("trim_group")];

				}


				if($row[csf("supplier_id")]>0 && $row[csf("wo_qnty")]>0)
				{
					$supplier_wise_sammary[$row[csf("supplier_id")]]["wo_qnty"]+=$row[csf("wo_qnty")];
					$supplier_wise_sammary[$row[csf("supplier_id")]]["ontimeRcv"]+=$ontimeRcv;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["rcv_qnty"]+=$rcv_qnty;
					$supplier_wise_sammary[$row[csf("supplier_id")]]["supplier_id"]=$row[csf("supplier_id")];
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<td width="40" align="center"><? echo $i;?></td>
                    <td width="110"><p><? echo $row[csf("booking_no")]; ?></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? if($lead_time>0 && $lead_time<2) echo $lead_time." Day"; elseif($lead_time>1) echo $lead_time." Days"; else echo "0 Day"; ?>&nbsp;</p></td>
                    <td width="90"><p><?  echo $wo_type; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_year"]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $order_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $order_data_arr[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $trimsGroupArr[$row[csf("trim_group")]]; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $description_arr[$row[csf("dtls_id")]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2,'.',''); ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? $wo_value=$row[csf("wo_qnty")]*$row[csf("wo_rate")]; echo number_format($wo_value,2,'.',''); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]],2,'.',''); ?></p></td>
                    <td width="80" align="right"><p><? $precost_value=$row[csf("wo_qnty")]*$pre_cost_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]]; echo number_format($precost_value,2); ?></p></td>
                    <td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $po_id; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo $description_arr[$row[csf("dtls_id")]]; ?>','<? echo $rcv_mst_id; ?>','1','booking_inhouse_info');"><? echo number_format($ontimeRcv,2,'.','');?></a> </p></td>
                    <td width="80" align="right"><p><? echo number_format($otd,2);?></p></td>
                    <td width="80" align="right"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $row[csf("book_mst_id")]; ?>','<? echo $po_id; ?>','<? echo $row[csf("trim_group")]; ?>','<? echo $description_arr[$row[csf("dtls_id")]]; ?>','0','2','booking_inhouse_info');"><? echo number_format($rcv_qnty,2,'.','');?></a> </p></td>
                    <td width="80" align="right"><p><? echo number_format($rcv_balance,2,'.',''); ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?> &nbsp;</p></td>
				</tr>
				<?
				$tot_wo_qnty+=$row[csf("wo_qnty")];
				$tot_wo_value+=$wo_value;
				$tot_receive_qnty+=$rcv_qnty;
				$tot_rcv_balance+=$rcv_balance;
				$total_ontime_rcv+=$ontimeRcv;
				$i++;
			}
			?>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th id="value_tot_wo_qnty" align="right"><? echo number_format($tot_wo_qnty,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_wo_value" align="right"><? echo number_format($tot_wo_value,2) ;?></th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th id="value_tot_ontime_receive_qnty" align="right"><? echo number_format($total_ontime_rcv,2) ;?></th>
                    <th >&nbsp;</th>
                    <th id="value_tot_receive_qnty" align="right"><? echo number_format($tot_receive_qnty,2) ;?></th>
                    <th id="value_tot_rcv_balance" align="right"><? echo number_format($tot_rcv_balance,2) ;?></th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table cellspacing="0" cellpadding="0" border="0" width="1500" rules="all">
        	<tr>
            	<td valign="top">

                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="700" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="7" align="center" style="font-size:16px; font-weight:bold;">Item Group Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Item Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">On Time Rcv Qty</th>
                                <th width="100">Total Rcv Qty</th>
                                <th width="100">Rcv Balance</th>
                                <th>OTD %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $k=1;
							//print_r($item_group_sammary);die;
							foreach($item_group_sammary as $item_grp_id=>$val)
                            {
                               	$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$item_group_sammary[$item_grp_id]["otd"]=$otd;
							}

							foreach($item_group_sammary as $item_group=>$val)
							{
								$mid[$item_group]  = $val["otd"];
							}
							array_multisort($mid, SORT_DESC, $item_group_sammary);

                            foreach($item_group_sammary as $val)
                            {
                                if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                               //$otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $k; ?></td>
                                    <td>
									<?
									//echo $trimsGroupArr[$item_group];
									echo $trimsGroupArr[$val["trim_group"]];
									?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    <td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
                                    <td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
                                    <td align="right"><? echo number_format($rcv_bal,2); ?></td>
                                    <td align="right"><? echo number_format($val["otd"],2); ?></td>
                                </tr>
                                <?
                                $i++;$k++;
                                $sum_tot_wo_qnty+=$val["wo_qnty"];
                                $sum_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sum_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sum_tot_rcv_bal+=$val["rcv_bal"];
                            }
                            $tot_otd=(($sum_tot_ontime_rcv/$sum_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sum_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_ontime_rcv,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_rcv_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sum_tot_rcv_bal,2); ?></th>
                                <th align="right"><? echo number_format($tot_otd,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>

                </td>
                <td valign="top">&nbsp;</td>
                <td valign="top">
                	<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="700" rules="all">

                        <thead>
                        	<tr bgcolor="#CCCCCC">
                                <td colspan="7" align="center" style="font-size:16px; font-weight:bold;">Supplier Wise Summary</td>
                            </tr>
                            <tr>
                                <th width="50">SL</th>
                                <th width="150">Supplier Name</th>
                                <th width="100">WO Qty</th>
                                <th width="100">On Time Rcv Qty</th>
                                <th width="100">Total Rcv Qty</th>
                                <th width="100">Rcv Balance</th>
                                <th>OTD %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $m=1;
							foreach($supplier_wise_sammary as $supplier_id=>$val)
                            {
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
								$supplier_wise_sammary[$supplier_id]["otd"]=$otd;
							}

							foreach($supplier_wise_sammary as $supplier_id=>$val)
							{
								$sid[$supplier_id]  = $val["otd"];
							}
							array_multisort($sid, SORT_DESC, $supplier_wise_sammary);
                            foreach($supplier_wise_sammary as $val)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rcv_bal=$val["wo_qnty"]-$val["rcv_qnty"];
                                $otd=(($val["ontimeRcv"]/$val["wo_qnty"])*100);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td><? echo $m; ?></td>
                                    <td><? echo $supplierArr[$val["supplier_id"]]; ?></td>
                                    <td align="right"><? echo number_format($val["wo_qnty"],2); ?></td>
                                    <td align="right"><? echo number_format($val["ontimeRcv"],2); ?></td>
                                    <td align="right"><? echo number_format($val["rcv_qnty"],2); ?></td>
                                    <td align="right"><? echo number_format($rcv_bal,2); ?></td>
                                    <td align="right"><? echo number_format($val["otd"],2); ?></td>
                                </tr>
                                <?
                                $i++;$m++;
                                $sup_tot_wo_qnty+=$val["wo_qnty"];
                                $sup_tot_ontime_rcv+=$val["ontimeRcv"];
                                $sup_tot_rcv_qnty+=$val["rcv_qnty"];
                                $sup_tot_rcv_bal+=$val["rcv_bal"];
                            }
                            $sup_tot_otd=(($sup_tot_ontime_rcv/$sup_tot_wo_qnty)*100);
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Total:</th>
                                <th align="right"><? echo number_format($sup_tot_wo_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_ontime_rcv,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_rcv_qnty,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_rcv_bal,2); ?></th>
                                <th align="right"><? echo number_format($sup_tot_otd,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>



    </fieldset>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}


if($action=="booking_inhouse_info")
{

	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $popup_type;die;
	?>
	<!--<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:640px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="50">Sl</th>
                    <th width="70">Prod. ID</th>
                    <th width="120">Recv. ID</th>
                    <th width="100">Recv. Date</th>
                    <th width="170">Item Description.</th>
                    <th >Recv. Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;

					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";



					//echo $receive_qty_data=("select b.po_breakdown_id,c.id as prod_id,c.item_description,d.recv_number,d.receive_date, a.item_group_id,sum(b.quantity) as quantity from  inv_receive_master d,inv_trims_entry_dtls a ,order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=1 and a.item_group_id='$item_name' and b.po_breakdown_id=$po_id and b.entry_form=24 and a.prod_id=c.id and c.id=b.prod_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,c.item_description,d.recv_number,d.receive_date, a.item_group_id,c.id");


					$mst_cond="";
					if($popup_type==1)
					{
						if($mst_id!="") $mst_cond=" and a.id in($mst_id)"; else  $mst_cond=" and a.id in(0)";
					}

					if($po_id!="") $po_cond=" and d.po_breakdown_id";

					//echo $mst_cond."".$mst_id.jaid;

					/*$receive_qty_data=("select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and b.item_description='$item_description' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $mst_cond
					group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number, a.receive_date");*/


					if($po_id!="")
					{
						$receive_qty_data="select  a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, d.quantity as rcv_qnty, c.item_group_id, c.item_description
						from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c , order_wise_pro_details d
						where a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and c.trans_id=d.trans_id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and d.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id=$po_id and  b.pi_wo_batch_no in($book_id) and c.item_group_id='$item_name' and c.item_description='$item_description' and a.is_deleted=0";
					}
					else
					{
						$receive_qty_data="select  a.id as mst_id, a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, b.cons_quantity as rcv_qnty, c.item_group_id, c.item_description
						from inv_receive_master a, inv_transaction b, inv_trims_entry_dtls c
						where a.id=b.mst_id and b.id=c.trans_id and  b.transaction_type=1 and b.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.pi_wo_batch_no in($book_id) and c.item_group_id='$item_name' and c.item_description='$item_description' and a.is_deleted=0";
					}


					//echo $receive_qty_data;

					$dtlsArray=sql_select($receive_qty_data);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
                            <td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td ><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td ><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('rcv_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('rcv_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="4" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="pop_up_details")
{
	echo load_html_head_contents("Details","../../../", 1, 1, '','','');
	extract($_REQUEST);
	$expData=explode('**',$datas);
	$pop_up_type = $expData[0];
	$job_no = $expData[1];
	$mydata = $expData[2];
	$cbo_search_type = $expData[3];
	//echo $job_no."__".$mydata;//die;
	$mydata==''? $mydata=0 : $mydata=$expData[2];
	$uom=$expData[5];
	$process=$expData[4];

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	if($pop_up_type=="wo_purchase_fabric_dtls" || $pop_up_type=="wo_embellisment_dtls" || $pop_up_type=="wo_yarn_cost_dtls" || $pop_up_type=="wo_gmts_wash_dtls" || $pop_up_type=="wo_knitting_dtls" || $pop_up_type=="wo_trims_dtls" || $pop_up_type=="wo_yarn_cost_dtls_sweater")
	{
		$tot_qty=0;
		?>
    	<fieldset style="width:760px; margin: 0 auto;">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Wo No</th>
                        <th width="100">Wo Date</th>
                        <th width="100">Wo Qty</th>
                        <? if($pop_up_type=="wo_yarn_cost_dtls_sweater"){ ?>
                        	<th width="60">Rate</th>
                        	<th width="60">Value</th>
                        <? } ?>
                        <th width="100">Uom</th>
                        <th width="">Supplier</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:460px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $i=0;
                if($pop_up_type=="wo_purchase_fabric_dtls")
                {
                	$sql="SELECT a.booking_date as wo_date, a.supplier_id, b.booking_no as wo_number, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.booking_type, b.process, b.emblishment_name, b.uom, b.wo_qnty as quantity, b.grey_fab_qnty as quantity, b.amount from wo_booking_mst a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id in ($mydata) and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0";
                }
                else if($pop_up_type=="wo_embellisment_dtls")
                {
                	$sql="SELECT a.booking_date as wo_date, a.supplier_id, b.booking_no as wo_number, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.booking_type, b.process, b.emblishment_name, b.uom, b.wo_qnty as quantity, b.grey_fab_qnty, b.amount from wo_booking_mst a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id in ($mydata) and b.emblishment_name=1 and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0";
                }
                else if($pop_up_type=="wo_gmts_wash_dtls")
                {
                	$sql="SELECT a.booking_date as wo_date, a.supplier_id, b.booking_no as wo_number, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.booking_type, b.process, b.emblishment_name, b.uom, b.wo_qnty as quantity, b.grey_fab_qnty, b.amount from wo_booking_mst a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id in ($mydata) and b.emblishment_name=3 and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0";
                }
                else if($pop_up_type=="wo_yarn_cost_dtls")
                {
					if($mydata==0){

	                	$sql="SELECT a.wo_number, a.wo_date, a.supplier_id, b.po_breakdown_id, b.item_category_id, b.supplier_order_quantity as quantity, b.amount from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (b.po_breakdown_id is not null and b.po_breakdown_id>0) and a.wo_number='$job_no'";
					}else{

							$sql="SELECT a.wo_number, a.wo_date, a.supplier_id, b.po_breakdown_id, b.item_category_id, b.supplier_order_quantity as quantity, b.amount from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in ($mydata)";

					}
                }
                else if($pop_up_type=="wo_knitting_dtls")
                {
					if($cbo_search_type==24)
					{
						$sql = "select distinct  b.id as id, a.ydw_no as wo_number, b.job_no as job_no, 0 as po_break_down_id, 0 as pre_cost_fabric_cost_dtls_id, 3 as booking_type, d.cons_process as process, 0 as emblishment_name, c.uom as uom, b.yarn_wo_qty as quantity, b.yarn_wo_qty as grey_fab_qnty, b.amount as amount, 2 as type, a.booking_date,a.supplier_id
						from wo_yarn_dyeing_mst a,  wo_yarn_dyeing_dtls b,  wo_pre_cost_fabric_cost_dtls c, wo_pre_cost_fab_conv_cost_dtls d
						where a.id=b.mst_id and b.job_no=c.job_no and c.id=d.fabric_description and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and d.cons_process=30 and b.job_no ='$job_no'";
					}else{
						$sql="SELECT a.booking_date as wo_date, a.supplier_id, b.booking_no as wo_number, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.booking_type, b.process, b.emblishment_name, b.uom, b.wo_qnty as quantity, b.grey_fab_qnty, b.amount from wo_booking_mst a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id in ($mydata) and b.process='$process' and b.uom='$uom' and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0";
					}
                }
                else if($pop_up_type=="wo_trims_dtls")
                {
                	$sql="SELECT a.booking_date as wo_date, a.supplier_id, b.booking_no as wo_number, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.booking_type, b.process, b.emblishment_name, b.uom, b.wo_qnty as quantity, b.grey_fab_qnty, b.amount from wo_booking_mst a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id in ($mydata) and b.pre_cost_fabric_cost_dtls_id='$process' and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0";
                }
                else if($pop_up_type=="wo_yarn_cost_dtls_sweater")
                {
                	$sql="SELECT b.id, b.wo_number, b.wo_date, b.supplier_id, a.supplier_order_quantity as quantity, a.amount, a.job_no, a.rate, a.uom from wo_non_order_info_mst b, wo_non_order_info_dtls a where b.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$job_no' and a.item_category_id=1 order by b.wo_number ASC";
                }

                //echo $sql; die;
                $sql_arr= sql_select($sql);
                foreach( $sql_arr as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="120" align="center"><? echo $row[csf("wo_number")];?> </td>
                    <td width="100" align="center"><? echo change_date_format($row[csf("wo_date")]);?> </td>
                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
                    <? if($pop_up_type=="wo_yarn_cost_dtls_sweater"){ ?>
                    	<td width="60" align="center"><? echo $row[csf("rate")];?> </td>
                    	<td width="60" align="center"><? echo $row[csf("amount")];?> </td>
                    <? 
                    	$total_amt+=$row[csf("amount")];
                    	} 
                    ?>
                    <td align="center" width="100">
                    	<?
                    		if($pop_up_type=="wo_purchase_fabric_dtls" || $pop_up_type=="wo_knitting_dtls" || $pop_up_type=="wo_trims_dtls" || $pop_up_type=="wo_yarn_cost_dtls_sweater")
                    		{
                    			echo $unit_of_measurement[$row[csf("uom")]];
                    		}
                    		else if($pop_up_type=="wo_embellisment_dtls" || $pop_up_type=="wo_gmts_wash_dtls")
                    		{
                    			echo "Pcs";
                    		}
                    		else if($pop_up_type=="wo_yarn_cost_dtls")
                    		{
                    			echo "Kg";
                    		}
                    	?>
                    </td>
                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
                </tr>
                <?
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <? if($pop_up_type=="wo_yarn_cost_dtls_sweater"){ ?>
                    	<td></td>
                    	<td align="right"><? echo $total_amt;?> </td>
                    <? } ?>
                    <td align="right"><p>&nbsp; </p></td>
                    <td align="right"><p>&nbsp; </p></td>
                </tr>
            </table>
        </div>
		</fieldset>
 		</div>
		<?
	}
	else if($pop_up_type=="wo_lab_dtls_sweater")
	{ 
		$test_item_arr=return_library_array( "select id, test_item from lib_lab_test_rate_chart",'id','test_item');
	?>
		<fieldset style="width:760px; margin: 0 auto;">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Wo No</th>
                        <th width="100">Wo Date</th>
                        <th width="100">Test For</th>
                        <th width="100">Test Item</th>
                        <th width="50">Wo Qty</th>
                    	<th width="50">Rate</th>
                    	<th width="50">Value</th>
                        <th width="50">Uom</th>
                        <th width="">Supplier</th>
                    </tr>
                </thead>
                <?
                $lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );
                $i=0;
                $sql= "SELECT a.supplier_id, a.labtest_no as wo_number, a.wo_date, b.job_no, b.qty_breakdown, b.wo_value, b.id as dtls_id , b.test_for, b.test_item_id, b.test_item_value  from wo_labtest_mst a join wo_labtest_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' order by a.labtest_no";
                //echo $sql; die;
				$sql_arr= sql_select($sql);
				foreach ($sql_arr as $row) {
				    $qty_breakdown_arr=explode(",", $row[csf('qty_breakdown')]);
				    $quantity=0;
				    $jobno=$row[csf('job_no')];
				    $wo_no=$row[csf('wo_number')];
				    $test_item_value=explode(",",$row[csf('test_item_value')]);
				    $index=0;
					foreach ($qty_breakdown_arr as $qty_breakdown) {
						$brackdown_arr=explode("_", $qty_breakdown);
						$item_id= $brackdown_arr[0];
						$quantity =$brackdown_arr[1];
						$value =$brackdown_arr[2];

						$lab_test_wo_data[$wo_no][$item_id]['wo_no']=$row[csf('wo_number')];
						$lab_test_wo_data[$wo_no][$item_id]['wo_date']=$row[csf('wo_date')];
						$lab_test_wo_data[$wo_no][$item_id]['test_for']=$test_for[$row[csf("test_for")]];
						$lab_test_wo_data[$wo_no][$item_id]['test_item']=$lab_test_rate_library[$item_id];
						$lab_test_wo_data[$wo_no][$item_id]['qty'] +=$quantity;
						$lab_test_wo_data[$wo_no][$item_id]['rate'] =$test_item_value[$index];
						$lab_test_wo_data[$wo_no][$item_id]['value'] +=$value;
						$lab_test_wo_data[$wo_no][$item_id]['uom'] ="Pcs";
						$lab_test_wo_data[$wo_no][$item_id]['supplier'] =$supplier_arr[$row[csf("supplier_id")]];
						$index++;
					}
					
				}
                
                foreach( $lab_test_wo_data as $item_data_arr )
                {
                	foreach ($item_data_arr as $key => $item_data) {                	
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
		               	?>
		                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                    <td width="30" align="center"><? echo $i; ?></td>
		                    <td width="120" align="center"><? echo $item_data['wo_no'];?> </td>
		                    <td width="100" align="center"><? echo change_date_format($item_data['wo_date']);?> </td>
		                    <td width="100" align="center"><? echo $item_data['test_for'];?> </td>
		                    <td width="100" align="center"><? echo $item_data['test_item'];?> </td>
		                    <td width="50" align="right"><p><? echo number_format($item_data['qty'],2); ?></p></td>
		                    <td width="50" align="right"><? echo $item_data['rate'];?> </td>
		                    <td width="50" align="right"><? echo $item_data['value'];?> </td>
		                    <td align="center" width="50"><? echo $item_data['uom']; ?></td>
		                    <td width=""><? echo $item_data['supplier']; ?></td>
		                </tr>
		                <?
		                $tot_qty+=$item_data['qty'];
		                $total_amt+=$item_data['value'];
	                } 
            	}
                ?>
                <tr class="tbl_bottom">
                    <td colspan="5" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo $total_amt;?> </td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                </tr>
            </table>
        </div>
		</fieldset>
		<?
	}
	exit();
}

if($action=="pi_pop_up_details")
{
	echo load_html_head_contents("PI Details","../../../", 1, 1, '','','');
	extract($_REQUEST);
	$expData=explode('**',$datas);
	$pop_up_type = $expData[0];
	$job_no = $expData[1];
	$mydata = $expData[2];
	$mydata=='' ? $mydata=0 : $mydata=$expData[2];
	$uom=$expData[3];
	//$process=$expData[6];
	$pi_id = $expData[4];
	$wo_no_ids = $expData[5];
	$cbo_search_type = $expData[6];

	$mydata="'".implode("','", explode(",", $mydata))."'";

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');

	$tot_qty=0;
		?>
    	<fieldset style="width:920px; margin: 0 auto;">
		<?
	if($pop_up_type=="pi_yarn_cost_dtls")
    {
		?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="70">Color</th>
	                        <th width="50">Count</th>
	                        <th width="150">Composition</th>
	                        <th width="70">Yarn Type</th>
	                        <th width="50">Uom</th>
	                        <th width="100">PI Qty</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;
					//echo $mydata."__".$expData[4]."__";
	                $sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_type, sum(b.quantity) as quantity, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id in( 1,24) and (b.work_order_no in ($mydata) and a.id in($pi_id)) group by a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_type";
					//echo $sql;
	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="70" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="70" align="center"><? echo $color_arr[$row[csf("color_id")]];?> </td>
	                    <td width="50" align="center"><? echo $count_arr[$row[csf("count_name")]];?> </td>
	                    <td width="150" align="center">
	                    	<? echo $composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."%"; ?>
	                    </td>
	                    <td width="70" align="center"><? echo $yarn_type[$row[csf("yarn_type")]];?> </td>
	                    <td align="center" width="50"><? echo "Kg"; ?></td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="8" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
	}
	else if($pop_up_type=="pi_purchase_fabric_dtls")
    {
    	?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="80">Construction</th>
	                        <th width="170">Composition</th>
	                        <th width="70">Color</th>
	                        <th width="50">Uom</th>
	                        <th width="100">PI Qty</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;

	                $sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount, b.fabric_construction, b.fabric_composition, b.color_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=3 and b.work_order_no in ($mydata) group by a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.fabric_construction, b.fabric_composition, b.color_id";

	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="70" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="80" align="center"><? echo $row[csf("fabric_construction")]; ?> </td>
	                    <td width="170" align="center"><? echo $row[csf("fabric_composition")]; ?> </td>
	                    <td width="70" align="center"><? echo $color_arr[$row[csf("color_id")]];?> </td>
	                    <td align="center" width="50"><? echo $unit_of_measurement[$uom]; ?></td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="7" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
    }
    else if($pop_up_type=="pi_knitting_dtls")
    {
    	?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="150">Description</th>
	                        <th width="70">Gmts Color</th>
	                        <th width="70">Item Color</th>
	                        <th width="50">Uom</th>
	                        <th width="100">PI Qty</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;
					if($cbo_search_type ==24){
						$sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount, b.item_description, b.color_id, b.item_color from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=24 and b.uom=$uom and (b.work_order_no in ($mydata) or b.work_order_id in($wo_no_ids)) group by a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.item_description, b.color_id, b.item_color";
					}else{
						/*$sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount, b.item_description, b.color_id, b.item_color from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=12 and b.service_type='$process' and b.uom=$uom and (b.work_order_no in ($mydata) or b.work_order_id in($wo_no_ids)) group by a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.item_description, b.color_id, b.item_color";*/
						
						$sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount, b.item_description, b.color_id, b.item_color from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=12  and b.uom=$uom and (b.work_order_no in ($mydata) or b.work_order_id in($wo_no_ids)) group by a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.item_description, b.color_id, b.item_color";
						
					}

					//echo $sql;
	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="70" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="150" align="center" style="word-break: break-all;word-wrap: break-word;">
	                    	<? echo $row[csf("item_description")]; ?> </td>
	                    <td width="70" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?> </td>
	                    <td width="70" align="center"><? echo $color_arr[$row[csf("item_color")]];?> </td>
	                    <td align="center" width="50"><? echo $unit_of_measurement[$uom]; ?></td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="7" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
    }
    else if($pop_up_type=="pi_embl_dtls")
    {
    	?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="150">Gmts Item</th>
	                        <th width="70">Embl. Name</th>
	                        <th width="70">Embl. Type</th>
	                        <th width="70">Gmts Color</th>
	                        <th width="100">PI Qty</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;
	                $sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount, b.gmts_item_id, b.embell_name, b.embell_type, b.color_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=25 and b.embell_name=1 and b.work_order_no in ($mydata) group by a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.gmts_item_id, b.embell_name, b.embell_type, b.color_id";

	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="70" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="150" align="center" ><? echo $garments_item[$row[csf("gmts_item_id")]]; ?> </td>
	                    <td width="70" align="center"><? echo $emblishment_name_array[$row[csf("embell_name")]]; ?> </td>
	                    <td width="70" align="center"> <? echo $emblishment_print_type[$row[csf("embell_type")]]; ?> </td>
	                    <td width="70" align="center"><? echo $color_arr[$row[csf("color_id")]];?> </td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="7" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
    }
    else if($pop_up_type=="pi_gmt_wash_dtls")
    {
    	?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="50">Uom</th>
	                        <th width="100">PI Qty</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;
	                $sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.quantity, b.amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=25 and b.embell_name=3 and b.work_order_no in ($mydata)";

	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="70" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="50" align="center"><? echo "Pcs";?> </td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="4" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
    }
    else if($pop_up_type=="pi_trims_dtls")
    {
    	?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="100">PI Date</th>
	                        <th width="100">Item Group</th>
	                        <th width="100">Uom</th>
	                        <th width="100">PI Qty</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;
	               // $sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.quantity, b.amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=4 and b.item_group='$process' and b.work_order_no in ($mydata)";
					
					$sql="select a.id, a.pi_number, a.pi_date, a.supplier_id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, b.quantity, b.amount
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_group=$pi_id and b.work_order_no in ($mydata)";

					// echo $sql;

	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="100" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="100" align="center"><? echo $item_group_arr[$row[csf("item_group")]];?> </td>
	                    <td width="100" align="center"><? echo $unit_of_measurement[$uom]; ;?> </td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                }
	                ?>
	                <tr class="tbl_bottom">
	                    <td colspan="5" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
    }
    else if($pop_up_type=="pi_yarn_cost_dtls_sweater")
    {
		?>

		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="70">Color</th>
	                        <th width="50">Count</th>
	                        <th width="150">Composition</th>
	                        <th width="70">Yarn Type</th>
	                        <th width="50">Uom</th>
	                        <th width="100">PI Qty</th>
	                        <th width="60">Rate</th>
	                        <th width="60">Amount</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:440px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $i=0;
	                $sql="SELECT a.id, a.supplier_id, a.pi_number, a.pi_date, a.remarks, b.work_order_dtls_id, b.work_order_no, c.yarn_comp_type1st, c.color_name, c.yarn_count as count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.supplier_order_quantity as quantity, c.amount, c.rate, c.job_no, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1 from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.item_category_id=1 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.job_no='$job_no' order by c.id";

					//echo $sql;
	                $sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("pi_number")];?> </td>
	                    <td width="70" align="center"><? echo change_date_format($row[csf("pi_date")]);?> </td>
	                    <td width="70" align="center"><? echo $color_arr[$row[csf("color_id")]];?> </td>
	                    <td width="50" align="center"><? echo $count_arr[$row[csf("count_name")]];?> </td>
	                    <td width="150" align="center">
	                    	<? echo $composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."%"; ?>
	                    </td>
	                    <td width="70" align="center"><? echo $yarn_type[$row[csf("yarn_type")]];?> </td>
	                    <td align="center" width="50"><? echo "Kg"; ?></td>
	                    <td width="100" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                    <td width="60" align="center"><? echo $row[csf("rate")];?> </td>
	                    <td width="60" align="center"><? echo $row[csf("amount")];?> </td>
	                    <td width=""><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
	                </tr>
	                <?
	                $tot_qty+=$row[csf("quantity")];
	                $tot_amt+=$row[csf("amount")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="8" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"></td>
	                    <td align="right"><? echo number_format($tot_amt,2); ?></td>
	                    <td align="right"><p>&nbsp; </p></td>
	                </tr>
	            </table>
	        </div>
	    <?
	}
	else if($pop_up_type=="pi_lab_dtls")
	{
		?>
		<div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="150">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="70">Test For</th>
	                        <th width="50">Test Item</th>
	                        <th width="50">PI Qty</th>
	                        <th width="50">Rate</th>
	                        <th width="50">Amount</th>
	                        <th width="">Supplier</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>
	        <div style="width:100%; max-height:440px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );
	                $i=0;
	               $sql="SELECT a.id, a.supplier_id, a.pi_number,a.pi_date,b.test_for,b.test_item_id,a.item_category_id, b.work_order_no, b.work_order_dtls_id,  b.uom, b.amount as amount, c.qty_breakdown, c.test_item_value from com_pi_master_details a join  com_pi_item_details b on a.id=b.pi_id join wo_labtest_dtls c on c.id=b.work_order_dtls_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($mydata) and  c.job_no='$job_no' and c.status_active=1 and c.is_deleted=0";
					//echo $sql;
	                $sql_arr= sql_select($sql);
	                foreach ($sql_arr as $key => $row) {

	                	$qty_breakdown_arr=explode(",", $row[csf('qty_breakdown')]);
					    $quantity=0;
					    $pi_id=$row[csf('id')];
					    $test_item_value=explode(",",$row[csf('test_item_value')]);
					    $index=0;
						foreach ($qty_breakdown_arr as $qty_breakdown) {
							$brackdown_arr=explode("_", $qty_breakdown);
							$item_id= $brackdown_arr[0];
							$quantity =$brackdown_arr[1];
							$value =$brackdown_arr[2];
							$lab_test_pi_data[$pi_id][$item_id]['pi_no']=$row[csf('pi_number')];
							$lab_test_pi_data[$pi_id][$item_id]['pi_date']=$row[csf('pi_date')];
							$lab_test_pi_data[$pi_id][$item_id]['test_for']=$test_for[$row[csf("test_for")]];
							$lab_test_pi_data[$pi_id][$item_id]['test_item']=$lab_test_rate_library[$item_id];
							$lab_test_pi_data[$pi_id][$item_id]['qty'] +=$quantity;
							$lab_test_pi_data[$pi_id][$item_id]['rate'] =$test_item_value[$index];
							$lab_test_pi_data[$pi_id][$item_id]['value'] +=$value;
							$lab_test_pi_data[$pi_id][$item_id]['supplier'] =$supplier_arr[$row[csf("supplier_id")]];
							$index++;
						}
	                }
	                foreach( $lab_test_pi_data as $item_data_arr )
                {
                	foreach ($item_data_arr as $key => $item_data) {                	
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
		               	?>
		                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                    <td width="30" align="center"><? echo $i; ?></td>
		                    <td width="150" align="center"><? echo $item_data['pi_no'];?> </td>
		                    <td width="70" align="center"><? echo change_date_format($item_data['pi_date']);?> </td>
		                    <td width="70" align="center"><? echo $item_data['test_for'];?> </td>
		                    <td width="50" align="center"><? echo $item_data['test_item'];?> </td>
		                    <td width="50" align="right"><p><? echo number_format($item_data['qty'],2); ?></p></td>
		                    <td width="50" align="right"><? echo $item_data['rate'];?> </td>
		                    <td width="50" align="right"><? echo $item_data['value'];?> </td>
		                    <td width=""><? echo $item_data['supplier']; ?></td>
		                </tr>
		                <?
		                $tot_qty+=$item_data['qty'];
		                $total_amt+=$item_data['value'];
	                } 
            	}
                ?>
                <tr class="tbl_bottom">
                    <td colspan="5" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo $total_amt;?> </td>
                    <td align="right">&nbsp;</td>
                </tr>
            </table>
	        </div>
	    <?
	}
	    ?>
		</fieldset>
 		</div>
		<?
	exit();
}

if($action=="inhouse_pop_up_details")
{
	echo load_html_head_contents("Inhouse Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$expData=explode('**',$datas);
	$pop_up_type = $expData[0];
	$job_no = $expData[1];
	$mydata = $expData[2]; //booking_id
	//var_dump($expData);
	$mydata=='' ? $mydata=0 : $mydata=$expData[2];
	$uom=$expData[3];
	$process=$expData[4];
	$cbo_search_type=$expData[5];
	$process=='' ? $process=0 : $process=$expData[4];

	$mydata="'".implode("','", explode(",", $mydata))."'";

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	if($pop_up_type=="inhouse_yarn_cost_dtls" || $pop_up_type=="inhouse_fabric_purchase_dtls" || $pop_up_type=="inhouse_knitting_dtls" || $pop_up_type=="inhouse_embl_dtls" || $pop_up_type=="inhouse_gmts_wash_dtls" || $pop_up_type=="inhouse_trims_dtls")
	{
		$tot_qty=0;
		?>
    	<fieldset style="width:620px; margin: 0 auto;">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Prod. ID</th>
                        <th width="150">Recv. ID</th>
                        <th width="100">Challan No</th>
                        <th width="100">Recv. Date</th>
                        <th width="">Recv. Qty</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:320px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $i=0;
                if($pop_up_type=="inhouse_yarn_cost_dtls")
                {
					if($mydata=='' || $mydata ==0)
					{
						if($db_type != 2)
						{
						$sql="select a.challan_no,a.recv_number,a.receive_date, a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty as quantity, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category in(1,24) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id is not null and a.booking_id>0 and a.booking_id in($mydata)";
						}else{
							$sql="select a.challan_no,a.recv_number,a.receive_date, a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty as quantity, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category in(1,24) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id is not null and a.booking_id>0 and a.booking_id in($mydata)";
						}
					}
					else
					{
						$sql="select a.challan_no,a.recv_number,a.receive_date, a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty as quantity, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in ($mydata)";
					}

                }
                else if($pop_up_type=="inhouse_fabric_purchase_dtls")
                {
                	$sql="select a.challan_no,a.recv_number,a.receive_date, a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty as quantity, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category in (2,3) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in ($mydata) and b.order_uom=$uom";
                }
                else if($pop_up_type=="inhouse_knitting_dtls")
                {
                	$search_data=implode(",",explode(",", chop($expData[2],",")));

                	if($process==1) // for knitting only
                	{
						if($db_type==2)
						{
							$sql="select a.challan_no, a.recv_number, a.receive_date, b.prod_id, b.job_no, b.process_id, b.batch_issue_qty as quantity, b.amount from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.process_id=$process and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0
                		union all
                		select CAST(a.challan_no AS VARCHAR(30)) as challan_no, CAST(a.recv_number AS VARCHAR(30)) as recv_number, a.receive_date, b.prod_id, ' ' as job_no, 1 as process_id, b.grey_receive_qnty as quantity, 0 as amount from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in ($search_data)
                		union all
                		select CAST(a.challan_no AS VARCHAR(30)) as challan_no, CAST(a.recv_number AS VARCHAR(30)) as recv_number, a.receive_date, b.prod_id, ' ' as job_no, 1 as process_id, b.order_qnty as quantity, 0 as amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.receive_basis in (11) and a.entry_form=22 and a.knitting_source=3 and a.booking_id in ($search_data)";
						}else{
							$sql="select a.challan_no, a.recv_number, a.receive_date, b.prod_id, b.job_no, b.process_id, b.batch_issue_qty as quantity, b.amount from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.process_id=$process and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0
                		union all
                		select a.challan_no, a.recv_number, a.receive_date, b.prod_id, ' ' as job_no, 1 as process_id, b.grey_receive_qnty as quantity, 0 as amount from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in ($search_data)
                		union all
                		select a.challan_no, a.recv_number, a.receive_date, b.prod_id, ' ' as job_no, 1 as process_id, b.order_qnty as quantity, 0 as amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.receive_basis in (11) and a.entry_form=22 and a.knitting_source=3 and a.booking_id in ($search_data)";
						}

                	}
                	else
                	{
                		$sql="select a.challan_no, a.recv_number, a.receive_date, b.prod_id, b.job_no, b.process_id, b.batch_issue_qty as quantity, b.amount from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.process_id=$process and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0";
                	}
					if($cbo_search_type ==24){
						$sql="select a.recv_number, a.receive_basis, a.challan_no, a.receive_date, a.item_category, a.booking_id, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category in (1,5,6,7,22,23,24) and a.entry_form=1 and a.receive_purpose = 2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in($search_data)";
					}
                }
                else if($pop_up_type=="inhouse_embl_dtls")
                {
                	$sql="SELECT challan_no, po_break_down_id, embel_name,production_date as receive_date,  production_quantity as quantity from pro_garments_production_mst where production_type=3 and status_active=1 and is_deleted=0 and embel_name=1 and po_break_down_id in ($mydata)";
                }
                else if($pop_up_type=="inhouse_gmts_wash_dtls")
                {
                	$sql="SELECT challan_no, po_break_down_id, embel_name,production_date as receive_date,  production_quantity as quantity from pro_garments_production_mst where production_type=3 and status_active=1 and is_deleted=0 and embel_name=3 and po_break_down_id in ($mydata)";
                }
                else if($pop_up_type=="inhouse_trims_dtls")
                {
                	$sql="select a.challan_no,a.recv_number,a.receive_date, a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty as quantity, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category=4 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($job_no)";
                }

                //echo $sql;//die;
                $sql_arr= sql_select($sql);
                foreach( $sql_arr as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               	?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" align="center"><? echo $row[csf("prod_id")];?> </td>
	                    <td width="150" align="center"><? echo $row[csf("recv_number")];?> </td>
	                    <td width="100" align="center"><? echo $row[csf("challan_no")];?> </td>
	                    <td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]);?> </td>
	                    <td width="" align="right"><p><? echo number_format($row[csf("quantity")],2); ?></p></td>
	                </tr>
	                <?
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                	<td width="30"></td>
                    <td colspan="4" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div>
		</fieldset>
		<?
	}
	elseif ($pop_up_type="inhouse_yarn_cost_dtls_sweater") {

		$sql="SELECT a.recv_number, a.challan_no, a.receive_date, a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty, b.order_rate,  b.order_amount, b.job_no, c.product_name_details from inv_receive_master a, inv_transaction b, product_details_master c  where a.id=b.mst_id and b.origin_prod_id=c.id and a.receive_basis in (1) and a.item_category in (1) and a.entry_form=248 and a.receive_purpose = 43 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no'";
	 	?>
		<fieldset style="width:620px; margin: 0 auto;">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="450">Product Details</th>
                        <th width="60">UOM</th>
                        <th width="150">Recv. ID</th>
                        <th width="100">Challan No</th>
                        <th width="100">Recv. Date</th>
                        <th width="60">Recv. Qty</th>
                        <th width="60">Rate</th>
                        <th width="60">Recv. Amount</th>
                    </tr>
                </thead>
                <?
                $sql_arr= sql_select($sql);
                foreach( $sql_arr as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
	               	?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="450" align="center"><? echo $row[csf("product_name_details")];?> </td>
	                    <td width="60" align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]];?> </td>
	                    <td width="150" align="center"><? echo $row[csf("recv_number")];?> </td>
	                    <td width="100" align="center"><? echo $row[csf("challan_no")];?> </td>
	                    <td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]);?> </td>
	                    <td width="60" align="right"><p><? echo number_format($row[csf("order_qnty")],2); ?></p></td>
	                    <td width="60" align="right"><p><? echo number_format($row[csf("order_rate")],2); ?></p></td>
	                    <td width="60" align="right"><p><? echo number_format($row[csf("order_amount")],2); ?></p></td>
	                </tr>
	                <?
                $tot_qty+=$row[csf("order_qnty")];
                $tot_amt+=$row[csf("order_amount")];
                } ?>
                <tr class="tbl_bottom">
                	<td width="30"></td>
                    <td colspan="5" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td align="right"></td>
                    <td align="right"><p><? echo number_format($tot_amt,2); ?></p></td>
                </tr>
            </table>
        </div>
        </fieldset>
	<? }
	exit();
}

if($action=="wo_job_no_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
	$year_id=$ex_data[2];
	$category_id=$ex_data[3];
	//$wo_type=$ex_data[4];
	?>
	<script>
		function js_set_value(wo_id,wo_no,job_no)
		{
			document.getElementById('txt_wo_no').value=wo_no;
			document.getElementById('txt_wo_id').value=wo_id;
			document.getElementById('txt_job_no').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:620px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="620" class="rpt_table">
	                <thead>
	                    <th>Buyer</th>
	                    <th>
	                    	<?
	                    		if($category_id==1){ echo "Search PI No."; }
	                    		else if($category_id==8){ echo "Search PI System ID"; }
	                    		else { echo "Search"; }
	                    	?>
	                	</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="" style="width:50px">
	                        <input type="hidden" name="txt_wo_id" id="txt_wo_id" value="" style="width:50px">
	                        <input type="hidden" name="txt_job_no" id="txt_job_no" value="" style="width:50px">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                        <?
								echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $year_id; ?>+'_'+<? echo $category_id; ?>, 'create_wo_job_search_list_view', 'search_div', 'procurement_prog_budget_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>
	        </form>
	    </fieldset>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="create_wo_job_search_list_view")
{
	$data=explode('_',$data);//var_dump($data);
	$item_category=$data[4];
	if($item_category==2 || $item_category==3 || $item_category==4 || $item_category==12 || $item_category==25)
	{
		if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
		if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
		if ($data[2]==0) $search_wo=""; else $search_wo=" and booking_no_prefix_num=$data[2]";

		if($db_type==0)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$data[3]";
		}
		elseif($db_type==2)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$data[3]";
		}

		if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and item_category=$data[4]";

		if($db_type==0)
		{
			$year=" YEAR(insert_date) as year";
		}
		elseif($db_type==2)
		{
			$year=" TO_CHAR(insert_date,'YYYY') as year";
		}

		$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");

		if($item_category==25)
		{
			$booking_type_cond=" and booking_type=6";
		}
		else
		{
			$booking_type_cond="";
		}

		$sql="SELECT id,booking_no,job_no,$year,booking_no_prefix_num,booking_date,buyer_id,booking_type,is_short,0 as type from wo_booking_mst where status_active=1 and is_deleted=0 and job_no !=' ' $company_id $buyer_id $year_id_cond $search_wo $category_id_cond $booking_type_cond order by id Desc";
		//echo $sql;
		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">WO No </th>
					<th width="80">Year</th>
					<th width="130">WO Type</th>
					<th width="150">Buyer</th>
					<th width="100">WO Date</th>
				</thead>
			</table>
			<div style="width:618px; overflow-y:scroll; max-height:290px;" id="" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
				<?
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if ($selectResult[csf("booking_type")]==1 || $selectResult[csf("booking_type")]==2)
						{
							if ($selectResult[csf("is_short")]==1)
							{
								$wo_type="Short";
							}
							else
							{
								$wo_type="Main";
							}
						}
						elseif($selectResult[csf("booking_type")]==4)
						{
							$wo_type="Sample With Order";
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('booking_no')]; ?>','<? echo $selectResult[csf('job_no')]; ?>')">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo $selectResult[csf('booking_no_prefix_num')]; ?></p></td>
							<td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
							<td width="130"><p><? echo $wo_type; ?></p></td>
							<td width="150"><p><? echo $buyerArr[$selectResult[csf('buyer_id')]]; ?></p></td>
							<td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>
						</tr>
					<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>
		<?
	}
	else if($item_category==24)
	{
		if ($data[0]==0) $buyer_id=""; else $buyer_id=" and a.buyer_id=$data[0]";
		if ($data[1]==0) $company_id=""; else $company_id=" and a.company_id=$data[1]";
		if ($data[2]==0) $search_wo=""; else $search_wo=" and a.yarn_dyeing_prefix_num=$data[2]";

		if($db_type==0)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$data[3]";
		}
		elseif($db_type==2)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$data[3]";
		}

		if ($data[4]==0) $category_id_cond=""; else $category_id_cond=" and a.item_category_id=24";

		if($db_type==0) $year=" YEAR(a.insert_date) as year";
		elseif($db_type==2) $year=" TO_CHAR(a.insert_date,'YYYY') as year";

		$supplierArr = return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");

		if($item_category==24)
		{
			$sql= "select a.id, a.ydw_no, b.job_no, $year, a.yarn_dyeing_prefix_num, a.booking_date, a.supplier_id, a.entry_form from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id and b.job_no!=' ' $company_id $year_id_cond $search_wo $category_id_cond $wo_type_cond order by a.id Desc";
		}
		//echo $sql;
		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">WO No </th>
					<th width="80">Year</th>
					<th width="130">WO Type</th>
					<th width="150">Supplier</th>
					<th width="100">WO Date</th>
				</thead>
			</table>
			<div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
				<?
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($selectResult[csf("entry_form")]==41) $wo_type="Order";
						else if($selectResult[csf("entry_form")]==42) $wo_type="Non Order";
						else $wo_type="";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('ydw_no')]; ?>','<? echo $selectResult[csf('job_no')]; ?>')">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
							<td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
							<td width="130"><p><? echo $wo_type; ?></p></td>
							<td width="150"><p><? echo $supplierArr[$selectResult[csf('supplier_id')]]; ?></p></td>
							<td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>
						</tr>
					<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>
		<?
	}
	else if($item_category==1 || $item_category==8)
	{
		if ($data[0]==0) $buyer_id=""; else $buyer_id=" and a.supplier_id=$data[0]";
		if ($data[1]==0) $company_id=""; else $company_id=" and a.importer_id=$data[1]";

		if (!$data[2]){ echo "<p style='text-align:center;'>Please Insert Search Keyword</p>"; die;}

		if($item_category==1)
		{
			$search_wo=" and a.pi_number='$data[2]'";
		}
		else if($item_category==8)
		{
			$search_wo=" and a.id=$data[2]";
		}

		if($db_type==0)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$data[3]";
		}
		elseif($db_type==2)
		{
			if ($data[3]==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$data[3]";
		}

		if($db_type==0)
		{
			$year=" YEAR(a.insert_date) as year";
		}
		elseif($db_type==2)
		{
			$year=" TO_CHAR(a.insert_date,'YYYY') as year";
		}

		$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");

		/*$sql="SELECT a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, $year, d.job_no as job_no from com_pi_master_details a, com_pi_item_details b, wo_booking_mst c, wo_booking_dtls d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.pi_id and b.work_order_no=c.booking_no and c.booking_no=d.booking_no and c.item_category in (2,3,4,12,25) and c.status_active=1 and c.is_deleted=0  $company_id $buyer_id $year_id_cond $search_wo
			union
			SELECT a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, $year, d.job_no as job_no from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.pi_id and b.work_order_no=c.ydw_no and c.id=d.mst_id and c.item_category_id=24 and c.status_active=1 and c.is_deleted=0  $company_id $buyer_id $year_id_cond $search_wo
			group by a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.insert_date, job_no";*/

		$pi_sql="SELECT a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, $year, b.work_order_no from com_pi_master_details a, com_pi_item_details b where  a.id=b.pi_id $company_id $buyer_id $year_id_cond $search_wo and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.insert_date, b.work_order_no";
		foreach (sql_select($pi_sql) as $val)
		{
			$pi_data[$val[csf("work_order_no")]]["id"]				=$val[csf("id")];
			$pi_data[$val[csf("work_order_no")]]["pi_number"]		=$val[csf("pi_number")];
			$pi_data[$val[csf("work_order_no")]]["supplier_id"]		=$val[csf("supplier_id")];
			$pi_data[$val[csf("work_order_no")]]["importer_id"]		=$val[csf("importer_id")];
			$pi_data[$val[csf("work_order_no")]]["pi_date"]			=$val[csf("pi_date")];
			$pi_data[$val[csf("work_order_no")]]["work_order_no"]	=$val[csf("work_order_no")];
			$pi_data[$val[csf("work_order_no")]]["year"]			=$val[csf("year")];
			$work_order[$val[csf("work_order_no")]]=$val[csf("work_order_no")];
		}

		$wo_number="'".implode("','", array_filter($work_order))."'";

		$job_sql="(SELECT c.booking_no as booking_no, d.job_no as job_no from wo_booking_mst c, wo_booking_dtls d where c.booking_no in ($wo_number) and c.booking_no=d.booking_no and c.item_category in (2,3,4,12,25) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.booking_no, d.job_no)
			union
			(SELECT c.ydw_no as booking_no, d.job_no as job_no from wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d where c.ydw_no in ($wo_number) and c.id=d.mst_id and c.item_category_id=24 and c.status_active=1 and c.is_deleted=0 group by c.ydw_no, d.job_no)";

		//echo $sql; die;
		?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
				<thead>
					<th width="30">SL</th>
					<th width="80">PI System ID</th>
					<th width="110">PI Number</th>
					<th width="100">PI Date</th>
					<th width="80">Year</th>
					<th width="100">Buyer</th>
					<th width="120">Job No </th>
				</thead>
			</table>
			<div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
				<?
					$i=1;
					$nameArray=sql_select( $job_sql );
					foreach ($nameArray as $selectResult)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$id 			=	$pi_data[$selectResult[csf("booking_no")]]["id"];
						$pi_number 		=	$pi_data[$selectResult[csf("booking_no")]]["pi_number"];
						$supplier_id	=	$pi_data[$selectResult[csf("booking_no")]]["supplier_id"];
						$importer_id	=	$pi_data[$selectResult[csf("booking_no")]]["importer_id"];
						$pi_date 		=	$pi_data[$selectResult[csf("booking_no")]]["pi_date"];
						$work_ord_no	=	$pi_data[$selectResult[csf("booking_no")]]["work_order_no"];
						$year 			=	$pi_data[$selectResult[csf("booking_no")]]["year"];

						$jobs 		=$selectResult[csf('job_no')];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $pi_number; ?>','<? echo $jobs; ?>')">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo $id; ?></p></td>
							<td width="110" align="center"><? echo $pi_number; ?></td>
							<td width="100"><? echo change_date_format($pi_date); ?></td>
							<td width="80"><p><? echo $year; ?></p></td>
							<td width="100"><p><? echo $buyerArr[$supplier_id]; ?></p></td>
							<td  width="120" align="center"><? echo $jobs; ?></td>
						</tr>
					<?
						$i++;
					}
				?>
				</table>
			</div>
		</div>
		<?
	}
	exit();
}

if ($action=="report_generate1")
{
	extract($_REQUEST);

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidd_job_id=str_replace("'","",$hidd_job_id);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$hidd_search_id=str_replace("'","",$hidd_search_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$hidd_job_no=str_replace("'","",$hidd_job_no);

	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$dealing_merchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$trim_name_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");

	if($cbo_buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name='$cbo_buyer_id'";
	}

	if($db_type==0)
	{
		if ($cbo_job_year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(a.insert_date)=$cbo_job_year_id";
	}
	elseif($db_type==2)
	{
		if ($cbo_job_year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(a.insert_date,'YYYY')=$cbo_job_year_id";
	}

	if($db_type==0)
	{
		if( $txt_date_from=="" && $txt_date_to=="" ) $ship_date_cond=""; else $ship_date_cond=" and b.pub_shipment_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
	if($db_type==2)
	{
		if( $txt_date_from=="" && $txt_date_to=="" ) $ship_date_cond=""; else $ship_date_cond=" and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
	}

	if($txt_job_no=="") $job_cond=""; else $job_cond="and a.job_no_prefix_num='$txt_job_no'";
	if($db_type==0) $insert_year="year(a.insert_date)"; else if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY')";
	if(!$txt_style_ref) $style_cond=""; else $style_cond="and a.style_ref_no ='$txt_style_ref'";

	$hidd_job_no_cond="";
	if($hidd_job_no)
	{
		$hidd_job_no="'".implode("','", array_unique(explode(",", $hidd_job_no)))."'";
		$hidd_job_no_cond="and a.job_no in ($hidd_job_no)";
	}


	$goods_rcv_variable=return_field_value("export_invoice_qty_source as source","variable_settings_commercial","company_name=$cbo_company_id and variable_list=23 and status_active=1","source");

	if(	$goods_rcv_variable != 1) $goods_rcv_variable=2;
	//$goods_rcv_variable=2;
	//var_dump($goods_rcv_variable)//=2;
	$search_job="";
	if($cbo_search_type>0 && $txt_search_no!="")
	{
		if($cbo_search_type==24)
		{
			$sql= "select b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.ydw_no='$txt_search_no' and b.entry_form in (41,114,0) and a.entry_form in (41,114,0)";
		}

		if($cbo_search_type==1) $pi_cond=" and a.pi_number='$txt_search_no'"; else if($cbo_search_type==8) $pi_cond=" and a.id='$txt_search_no'";
		if($cbo_search_type==1 || $cbo_search_type==8)
		{
			if($goods_rcv_variable==1)
			{
				$sql="SELECT c.job_no from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id=24 and a.goods_rcv_status=2 $pi_cond union all SELECT c.job_no from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=2 $pi_cond union all SELECT c.job_no from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=1 $pi_cond UNION ALL SELECT c.job_no FROM com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c WHERE a.id = b.pi_id AND b.work_order_id = c.mst_id and b.work_order_dtls_id=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.pi_basis_id = 1 AND a.item_category_id =1 AND a.goods_rcv_status=2 $pi_cond UNION ALL SELECT c.job_no FROM com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c WHERE a.id = b.pi_id AND b.work_order_id = c.mst_id and b.work_order_dtls_id=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.pi_basis_id = 1 AND a.item_category_id=1 AND a.goods_rcv_status=1 $pi_cond ";
			}
			else
			{
				$sql="SELECT c.job_no from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id=24 and a.goods_rcv_status=2 $pi_cond union all SELECT c.job_no from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=2 $pi_cond union all SELECT c.job_no from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_basis_id=1 and a.item_category_id not in(24,1) and a.goods_rcv_status=1 $pi_cond UNION ALL SELECT c.job_no FROM com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c WHERE a.id = b.pi_id AND b.work_order_id = c.mst_id and b.work_order_dtls_id=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.pi_basis_id = 1 AND a.item_category_id =1 AND a.goods_rcv_status = 2 $pi_cond UNION ALL SELECT d.job_no FROM com_pi_master_details a, com_pi_item_details b, inv_transaction c, wo_non_order_info_dtls d WHERE a.id=b.pi_id and b.work_order_dtls_id = c.id AND c.pi_wo_batch_no = d.mst_id and b.item_category_id=1 and c.item_category=1 and d.item_category_id=1 and d.job_id>0 and c.receive_basis=1 and c.transaction_type=1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.pi_basis_id = 1 AND a.goods_rcv_status = 1 $pi_cond ";
			}

			//echo $sql;//die;
			$sql_res=sql_select($sql);
			foreach($sql_res as $row)
			{
				$search_job.="'".$row[csf("job_no")]."',";
			}
			$search_job=implode(",",array_unique(explode(",",chop($search_job,","))));
			if($search_job=="") { echo "No Data Found";die; }

		}

		//echo $sql;//die;
		$sql_res=sql_select($sql);
			foreach($sql_res as $row)
			{
				$search_job.="'".$row[csf("job_no")]."',";
			}
			$search_job=implode(",",array_unique(explode(",",chop($search_job,","))));
			if($search_job=="") { echo "No Data Found";die; }
	}
	$job_sql="SELECT a.id, a.job_no, a.job_no_prefix_num, a.buyer_name, $insert_year as job_year, a.dealing_marchant, a.style_ref_no, a.job_quantity, a.avg_unit_price, a.total_price, b.id as po_id, c.costing_date, c.costing_per from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and a.garments_nature=100 and  a.company_name='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $year_id_cond $job_cond $style_cond $ship_date_cond $hidd_job_no_cond order by a.id DESC";
	//echo $job_sql; //die;
	$job_sql_res=sql_select($job_sql);


	if(count($job_sql_res)<1) { echo "No Data Found";die; }
	$job_arr=array(); $po_job_arr=array(); $tot_rows=0; $jobNos=''; $poIds='';
	foreach($job_sql_res as $row)
	{
		$tot_rows++;
		$jobNos.="'".$row[csf("job_no")]."',";
		$poIds.=$row[csf("po_id")].",";

		$job_arr[$row[csf("job_no")]]["id"]=$row[csf("id")];
		$job_arr[$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
		$job_arr[$row[csf("job_no")]]["job_prefix_num"]=$row[csf("job_no_prefix_num")];
		$job_arr[$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
		$job_arr[$row[csf("job_no")]]["job_year"]=$row[csf("job_year")];
		$job_arr[$row[csf("job_no")]]["dealing_marchant"]=$row[csf("dealing_marchant")];
		$job_arr[$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$job_arr[$row[csf("job_no")]]["job_qty"]=$row[csf("job_quantity")];
		$job_arr[$row[csf("job_no")]]["avg_price"]=$row[csf("avg_unit_price")];
		$job_arr[$row[csf("job_no")]]["job_amount"]=$row[csf("total_price")];
		$job_arr[$row[csf("job_no")]]["costing_date"]=$row[csf("costing_date")];
		$job_arr[$row[csf("job_no")]]["costing_per"]=$row[csf("costing_per")];

		$po_job_arr[$row[csf("po_id")]]=$row[csf("job_no")];
	}	

	unset($job_sql_res);

	$jobNos=implode(",",array_unique(explode(",",$jobNos)));
	$poIds=implode(",",array_unique(explode(",",$poIds)));
	//echo $poIds; //die;
	$jobNos=chop($jobNos,','); $poIds=chop($poIds,','); $jobNos_cond=""; $jobNos_conv_cond=""; $jobNos_cond2="";


	if($db_type==2 && $tot_rows>1000)
	{
		$jobNos_cond=" and (";
		$jobNos_cond2=" and (";
		$jobNos_conv_cond=" and (";
		$jobNos_pi_cond=" and (";
		$jobNosArr=array_chunk(explode(",",$jobNos),399);
		foreach($jobNosArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobNos_cond.=" job_no in($ids) or ";
			$jobNos_cond2.=" b.job_no in($ids) or ";
			$jobNos_conv_cond.=" a.job_no in($ids) or ";
			$jobNos_pi_cond.=" c.job_no in($ids) or ";
		}
		$jobNos_cond=chop($jobNos_cond,'or ');
		$jobNos_cond.=")";

		$jobNos_cond2=chop($jobNos_cond,'or ');
		$jobNos_cond2.=")";

		$jobNos_conv_cond=chop($jobNos_conv_cond,'or ');
		$jobNos_conv_cond.=")";

		$jobNos_pi_cond=chop($jobNos_conv_cond,'or ');
		$jobNos_pi_cond.=")";
	}
	else
	{

		if($cbo_search_type==24 ){
			$jobNos_cond=" and job_no in ($jobNos)";
		}
		//echo "sumon __".$jobNos_cond;
		$jobNos_cond=" and job_no in ($jobNos)";
		$jobNos_cond2=" and b.job_no in ($jobNos)";
		$jobNos_conv_cond=" and a.job_no in ($jobNos)";
		$jobNos_pi_cond=" and c.job_no in ($jobNos)";
	}

	$poid_cond=""; $po_emb_cond="";

	if($db_type==2 && $tot_rows>1000)
	{
		$poid_cond=" and (";
		$po_emb_cond=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),399);
		foreach($poIdsArr as $idps)
		{
			$idps=implode(",",$idps);
			$poid_cond.=" b.po_breakdown_id in($idps) or ";
			$po_emb_cond.=" po_break_down_id in($ids) or ";
		}
		$poid_cond=chop($poid_cond,'or ');
		$poid_cond.=")";

		$po_emb_cond=chop($po_emb_cond,'or ');
		$po_emb_cond.=")";
	}
	else
	{
		$poid_cond=" and b.po_breakdown_id in ($poIds)";
		$po_emb_cond=" and po_break_down_id in ($poIds)";
	}
	//var_dump($cbo_buyer_id);
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if($cbo_buyer_id>0){
		$condition->buyer_name("=$cbo_buyer_id");
	}
	//$condition->buyer_name("=65");//die;

	if($txt_job_no!=''){
		$condition->job_no_prefix_num("in ($txt_job_no)");
	}
	if($jobNos!=""){
		$condition->job_no("in ($jobNos)");
	}


	if($txt_date_from!='' && $txt_date_to!=''){
		$condition->pub_shipment_date(" between '$txt_date_from' and '$txt_date_to'");
	}
	if($txt_style_ref!=''){
		$condition->style_ref_no("='$txt_style_ref'");
	}

	$condition->init();
	$yarn= new yarn($condition);
	$yarn_data_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

	$sql_po="SELECT a.job_no,  c.item_number_id, c.color_number_id, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $jobNos_conv_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id ASC";
	$sql_po_data=sql_select($sql_po); $gmts_item_color_qty_arr=array();
	foreach($sql_po_data as $row)
	{
		$gmts_item_color_qty_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
	}

	$fabPurArr=array();
	$sql = "SELECT job_no, uom, fab_nature_id, rate from wo_pre_cost_fabric_cost_dtls where fabric_source=2 $jobNos_cond";
	//echo $sql;die;
	$data_fabPur=sql_select($sql);
	foreach($data_fabPur as $fabPur_row){
		$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('job_no')]];
		$Preamt=$Precons*$fabPur_row[csf('rate')];
		$fabPurArr[$fabPur_row[csf('job_no')]]['qty'][$fabPur_row[csf('uom')]]+=$Precons;
		$fabPurArr[$fabPur_row[csf('job_no')]]['amount'][$fabPur_row[csf('uom')]]+=$Preamt;
	}
	unset($data_fabPur);
	$costing_per_arr= array(1=>12,2=>1,3=>24,4=>36,4=>48);
	$yarn_arr=array();
	$sql_yarn="SELECT a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement, d.costing_per from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c, wo_pre_cost_mst d where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and a.job_no=d.job_no $jobNos_conv_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	//echo $sql_yarn; die;
	$data_arr_yarn=sql_select($sql_yarn);
	foreach($data_arr_yarn as $yarn_row)
	{
		$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0; $amount_req=0;
		$poQty=$gmts_item_color_qty_arr[$yarn_row[csf("job_no")]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('color_number_id')]];
		$yarn_req_kg=($yarn_row[csf('measurement')]/$costing_per_arr[$yarn_row[csf("costing_per")]])*$poQty;
		$yarn_req_lbs=$yarn_req_kg*2.20462;
		$amount_req=$yarn_req_lbs*$yarn_row[csf('rate')];
		
		$yarn_arr[$yarn_row[csf("job_no")]]['qty']+=$yarn_req_lbs;
		$yarn_arr[$yarn_row[csf("job_no")]]['amount']+=$amount_req;
	}
	unset($data_arr_yarn);

	$fabric= new fabric($condition);
	$fabPur=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();

	$knitData=array();
	$conversion= new conversion($condition);
	$knitQtyArr=$conversion->getQtyArray_by_jobAndProcess();
	//print_r($knitQtyArr); //die;
	$conversion= new conversion($condition);
	$knitAmtArr=$conversion->getAmountArray_by_jobAndProcess();

	//$sql = "select a.job_no, a.uom, b.cons_process from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description $jobNos_conv_cond";

	$sql = "select a.job_no, a.cons_process, b.uom from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id where 1=1 $jobNos_conv_cond order by  a.cons_process";
	//echo $sql;die;
	$data_knit=sql_select($sql);
	foreach($data_knit as $row_knit){
		$knitData[$row_knit[csf('job_no')]]['qty'][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]]=$knitQtyArr[$row_knit[csf('job_no')]][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]];
		$knitData[$row_knit[csf('job_no')]]['amount'][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]]=$knitAmtArr[$row_knit[csf('job_no')]][$row_knit[csf('cons_process')]][$row_knit[csf('uom')]];

	}
	//echo $sql;die;
	unset($data_knit);

	$others= new other($condition);
	$othersAmtArr=$others->getAmountArray_by_job();


	//echo $gmtsWashData['D n C-17-01621']['qty'].'=='; die;
	$trimData=array();
	$trim= new trims($condition);
	$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

	$trim= new trims($condition);
	$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();



	$sql_trim= "select id, job_no, trim_group, cons_uom from wo_pre_cost_trim_cost_dtls where 1=1 $jobNos_cond";
	$data_trim=sql_select($sql_trim);
	foreach($data_trim as $row){
		$trimData[$row[csf('job_no')]]['qty'][$row[csf('id')]][$row[csf('trim_group')]]=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
		$trimData[$row[csf('job_no')]]['amount'][$row[csf('id')]][$row[csf('trim_group')]]=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		$trimData[$row[csf('job_no')]]['cons_uom'][$row[csf('id')]][$row[csf('trim_group')]]=$row[csf('cons_uom')];
	}
	unset($data_trim);
	//Based On Budget end

	$sql_non="select b.id, b.wo_number, a.po_breakdown_id, a.item_category_id, a.supplier_order_quantity, a.amount, a.job_no, a.job_id from wo_non_order_info_mst b, wo_non_order_info_dtls a where b.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobNos_conv_cond ";
	//echo $sql_non; die;
	$sql_non_res=sql_select($sql_non);
	$booking_job_arr=array(); $yarn_ord_arr=array(); $woNos=""; $wo_rows=0;
	foreach($sql_non_res as $nrow)
	{
		$wo_rows++;
		$woNos.="'".$nrow[csf("wo_number")]."',";
		$woNo_ids.="'".$nrow[csf("id")]."',";
		$job_no=$nrow[csf("job_no")];
		if($nrow[csf("item_category_id")]==1)
		{
			$yarn_ord_arr[$job_no]['qty']+=$nrow[csf("supplier_order_quantity")];
			$yarn_ord_arr[$job_no]['amt']+=$nrow[csf("amount")];
			$yarn_ord_arr[$job_no]['job_id'].=$nrow[csf("job_id")].",";
		}

		$booking_job_arr[$nrow[csf("wo_number")]]=$job_no;
	}
	/*echo '<pre>';
	print_r($yarn_ord_arr); die;*/

	unset($sql_non_res);

	$sql_book="SELECT 0 as booking_id, id, booking_no, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, booking_type, process, emblishment_name, uom, wo_qnty, grey_fab_qnty, amount, 1 as type from wo_booking_dtls where status_active=1 and is_deleted=0 and wo_qnty>0 $jobNos_cond union all select distinct a.id as booking_id, b.id, a.ydw_no as booking_no, b.job_no as job_no, 0 as po_break_down_id, 0 as pre_cost_fabric_cost_dtls_id, 3 as booking_type, d.cons_process as process, 0 as emblishment_name, c.uom as uom,  b.yarn_wo_qty as wo_qnty,  0 as grey_fab_qnty,  b.amount as amount, 2 as type from wo_yarn_dyeing_mst a,  wo_yarn_dyeing_dtls b,  wo_pre_cost_fabric_cost_dtls c, wo_pre_cost_fab_conv_cost_dtls d where a.id=b.mst_id and b.job_no=c.job_no and c.id=d.fabric_description  and a.company_id in($cbo_company_id) and a.item_category_id  in(1, 24)  and a.entry_form in(1,41) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.yarn_wo_qty >0 and d.cons_process=30";
	//echo $sql_book; die;

	$sql_book_res=sql_select($sql_book);
	$fab_booking_arr=array(); $conv_cost_arr=array(); $emb_booking_arr=array(); $trim_booking_arr=array();
	//echo $job_no;//die;
	//$job_no='FAL-18-00274';
	//var_dump($yarn_ord_arr[$job_no]);//die;
	foreach($sql_book_res as $brow)
	{
		$wo_rows++;
		$woNos.="'".$brow[csf("booking_no")]."',";
		$woNo_ids.="'".$brow[csf("booking_id")]."',";
		$job_no="";
		$job_no=$brow[csf("job_no")];
		$jobno=$po_job_arr[$brow[csf("po_break_down_id")]];
		$fab_booking_arr[$jobno]['qty']+=$brow[csf("grey_fab_qnty")];
		$fab_booking_arr[$jobno]['amt']+=$brow[csf("amount")];
		$fab_booking_arr[$jobno]['po'].=$brow[csf("po_break_down_id")].",";
		$trim_booking_arr[$job_no][$brow[csf("pre_cost_fabric_cost_dtls_id")]]['qty']+=$brow[csf("wo_qnty")];
		$trim_booking_arr[$job_no][$brow[csf("pre_cost_fabric_cost_dtls_id")]]['amt']+=$brow[csf("amount")];
		$trim_booking_arr[$job_no][$brow[csf("pre_cost_fabric_cost_dtls_id")]]['po'].=$brow[csf("po_break_down_id")].",";
		if($cbo_search_type==24)
		{
			if($dtls_id_check[$brow[csf("id")]]=="")
			{
				$dtls_id_check[$brow[csf("id")]]=$brow[csf("id")];
				$ydw_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['qty']+=$brow[csf("wo_qnty")];
				$ydw_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['amt']+=$brow[csf("amount")];
				$ydw_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['po'].=$brow[csf("po_break_down_id")].",";
			}	
		}else{
			if($dtls_id_check[$brow[csf("id")]]=="")
			{
				$dtls_id_check[$brow[csf("id")]]=$brow[csf("id")];
				$conv_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['qty']+=$brow[csf("wo_qnty")];
				$conv_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['amt']+=$brow[csf("amount")];
				$conv_cost_arr[$job_no][$brow[csf("process")]][$brow[csf("uom")]]['po'].=$brow[csf("po_break_down_id")].",";
			}	
		}
		$emb_booking_arr[$job_no][$brow[csf("emblishment_name")]]['qty']+=$brow[csf("wo_qnty")];
		$emb_booking_arr[$job_no][$brow[csf("emblishment_name")]]['amt']+=$brow[csf("amount")];
		$emb_booking_arr[$job_no]['po'].=$brow[csf("po_break_down_id")].",";
		$booking_job_arr[$brow[csf("booking_no")]]=$job_no;
		$booking_job_ids_arr[$brow[csf("booking_id")]]=$job_no;
	}

	unset($sql_book_res);

	$lab_test_wo_data=sql_select("SELECT a.labtest_no, b.job_no, b.qty_breakdown, b.wo_value, b.id as dtls_id, b.test_item_id from wo_labtest_mst a join wo_labtest_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobNos_cond2 ");
	//echo "SELECT a.labtest_no, b.job_no, b.qty_breakdown, b.wo_value, b.id as dtls_id, b.test_item_id from wo_labtest_mst a join wo_labtest_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobNos_cond2 "; die;
	$lab_test_wo_arr=array();
	foreach ($lab_test_wo_data as $row) {
		$qty_breakdown_arr=explode(",", $row[csf('qty_breakdown')]);
		foreach ($qty_breakdown_arr as $qty_breakdown) {
			$qty=explode("_", $qty_breakdown);
			$lab_test_wo_arr[$row[csf('job_no')]]['qty']+=$qty[1];
			$labtestqty[$row[csf('dtls_id')]]+=$qty[1];
		}
		$test_item_arr= explode(",", $row[csf('test_item_id')]);
		foreach ($test_item_arr as $data) {
			$lab_test_item_arr[$row[csf('job_no')]][$data]=$data;
		}
		$lab_test_wo_arr[$row[csf('job_no')]]['amt']+=$row[csf('wo_value')];
		$labTestWoNos[$row[csf('labtest_no')]]=$row[csf('labtest_no')];
		$labTestdtlsId[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
		$tabBookingArr[$row[csf('labtest_no')]]=$row[csf('job_no')];
	}

	if($db_type==2 && count($labTestWoNos)>1000)
	{
		$lwoNos_cond=" and (";
		$lwoNosArr=array_chunk($labTestWoNos,399);
		foreach($lwoNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$lwoNos_cond.=" b.work_order_no in ($idws) or ";
		}
		$lwoNos_cond=chop($lwoNos_cond,'or ');
		$lwoNos_cond.=")";
	}
	else
	{
		$labwoNo="'" . implode ( "', '", $labTestWoNos ) . "'";
		$lwoNos_cond=" and b.work_order_no in ($labwoNo)";
	}

	if($db_type==2 && count($labTestdtlsId)>1000)
	{
		$lpoNos_cond=" and (";
		$lpoNosArr=array_chunk($labTestdtlsId,399);
		foreach($lpoNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$lpoNos_cond.=" b.work_order_dtls_id in ($idws) or ";
		}
		$lpoNos_cond=chop($lpoNos_cond,'or ');
		$lpoNos_cond.=")";
	}
	else
	{
		$labpoId=implode(",", $labTestdtlsId);
		$lpoNos_cond=" and b.work_order_dtls_id in ($labpoId)";
	}

	$lab_sql_pi=sql_select("SELECT a.id, a.item_category_id, b.work_order_no, b.work_order_dtls_id,  b.uom,sum(b.quantity) as quantity, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lwoNos_cond $lpoNos_cond  and a.importer_id = $cbo_company_id group by a.id, a.item_category_id, b.work_order_no, b.uom, b.work_order_dtls_id");
	foreach ($lab_sql_pi as $row) {
		$jobNo=$tabBookingArr[$row[csf('work_order_no')]];
		$labtestPiArr[$jobNo]['qty'] += $labtestqty[$row[csf('work_order_dtls_id')]];
		$labtestPiArr[$jobNo]['amt'] += $row[csf('amount')];
		$labtestPiArr[$jobNo]['pi_id'][$row[csf('id')]] = $row[csf('id')];

	}

	$woNos=implode(",",array_unique(explode(",",$woNos)));
	$woNos=chop($woNos,','); $woNos_cond="";
	$woNo_ids=chop($woNo_ids,','); $woNo_ids_cond="";

	if($db_type==2 && $tot_rows>1000)
	{
		$woNos_cond=" and (";
		$woNosArr=array_chunk(explode(",",$woNos),399);
		foreach($woNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$woNos_cond.=" b.work_order_no in ($idws) or ";
		}
		$woNos_cond=chop($woNos_cond,'or ');
		$woNos_cond.=")";

		$woNo_ids_cond=" or (";
		$woNosArr=array_chunk(explode(",",$woNo_ids),399);
		foreach($woNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$woNo_ids_cond.=" b.work_order_id in ($idws) or ";
		}
		$woNo_ids_cond=chop($woNo_ids_cond,'or ');
		$woNo_ids_cond.=")";
	}
	else
	{
		$woNos_cond=" and (b.work_order_no in ($woNos)";
		$woNo_ids_cond=" or b.work_order_id in ($woNo_ids))";
	}

	$sql_pi_yarn=sql_select("SELECT a.id, a.supplier_id, a.pi_number, a.pi_date, a.remarks, b.work_order_dtls_id, b.work_order_no, c.yarn_comp_type1st, c.color_name, c.yarn_count as count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type as type_id, c.supplier_order_quantity as quantity, c.amount, c.rate, c.job_no from com_pi_master_details a, com_pi_item_details b,wo_non_order_info_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.item_category_id=1 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $jobNos_pi_cond order by c.id");
	$pi_arr=array(); 
	foreach ($sql_pi_yarn as $row) {
		$jobn=$row[csf("job_no")];
		$pi_arr[$jobn]['qty']+=$row[csf("quantity")];
		$pi_arr[$jobn]['amt']+=$row[csf("amount")];
		$pi_arr[$jobn]['wo_no'].=$row[csf("work_order_no")].",";
	}
	if($cbo_search_type==24){
		$sql_pi="SELECT a.id, a.item_category_id, b.work_order_no,b.work_order_id, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $woNos_cond $woNo_ids_cond and a.item_category_id = 24  and a.importer_id = $cbo_company_id group by a.id, a.item_category_id, b.work_order_no,  b.uom, b.service_type,  b.embell_name,  b.item_group,b.work_order_id";
	}else{
		$sql_pi="SELECT a.id, a.item_category_id, b.work_order_no, b.uom, b.service_type, b.embell_name, b.item_group, sum(b.quantity) as quantity, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $woNos_cond $woNo_ids_cond  and a.importer_id = $cbo_company_id group by a.id, a.item_category_id, b.work_order_no,  b.uom, b.service_type,  b.embell_name,  b.item_group";
	}
	//echo $sql_pi;die;

	$sql_pi_res=sql_select($sql_pi);
	$pi_fab_arr=array(); $pi_conv_arr=array(); $pi_emb_arr=array(); $pi_trim_arr=array(); $pi_job_arr=array(); $piids=""; $pi_rows=0;$jobn='';
	foreach($sql_pi_res as $prow)
	{
		$pi_rows++;
		$piids.=$prow[csf("id")].",";
		$jobn=$booking_job_arr[$prow[csf("work_order_no")]];		
		$pi_fab_arr[$jobn][$prow[csf("uom")]]['qty']+=$prow[csf("quantity")];
		$pi_fab_arr[$jobn][$prow[csf("uom")]]['amt']+=$prow[csf("amount")];
		$pi_fab_arr[$jobn][$prow[csf("uom")]]['wo_no'].=$prow[csf("work_order_no")].",";
		$pi_trim_arr[$jobn][$prow[csf("item_group")]]['qty']+=$prow[csf("quantity")];
		$pi_trim_arr[$jobn][$prow[csf("item_group")]]['amt']+=$prow[csf("amount")];
		$pi_trim_arr[$jobn][$prow[csf("item_group")]]['wo_no'].=$prow[csf("work_order_no")].",";
		$pi_conv_arr[$jobn][$prow[csf("service_type")]][$prow[csf("uom")]]['qty']+=$prow[csf("quantity")];
		$pi_conv_arr[$jobn][$prow[csf("service_type")]][$prow[csf("uom")]]['amt']+=$prow[csf("amount")];
		$pi_conv_arr[$jobn][$prow[csf("service_type")]][$prow[csf("uom")]]['wo_no'].=$prow[csf("work_order_no")].",";
		$pi_trim_arr[$jobn][$prow[csf("item_group")]]['qty']=$prow[csf("quantity")];
		$pi_trim_arr[$jobn][$prow[csf("item_group")]]['amt']=$prow[csf("amount")];
		$pi_trim_arr[$jobn][$prow[csf("item_group")]]['wo_no'].=$prow[csf("work_order_no")].",";
		$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['qty']=$prow[csf("quantity")];
		$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['amt']=$prow[csf("amount")];
		$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['wo_no'].=$prow[csf("work_order_no")].",";
		$pi_conv_pi_arr[$jobn][$prow[csf("uom")]]['wo_ids'].=$prow[csf("work_order_id")].",";
		$pi_job_arr[$prow[csf("id")]]=$jobn;
	}
	unset($sql_pi_res);
	/*echo "<pre>";var_dump($pi_conv_pi_arr);echo "</pre>";//*/
	$piids=implode(",",array_unique(explode(",",$piids)));
	$piids=chop($piids,','); $rec_pi_cond="";

	if($db_type==2 && $pi_rows>1000)
	{
		$rec_pi_cond=" and (";
		$piidsArr=array_chunk(explode(",",$piids),399);
		foreach($piidsArr as $idpis)
		{
			$idpis=implode(",",$idpis);
			$rec_pi_cond.=" a.booking_id in ($idpis) or ";
		}
		$rec_pi_cond=chop($rec_pi_cond,'or ');
		$rec_pi_cond.=")";
	}
	else
	{
		if($piids) $rec_pi_cond=" and a.booking_id in ($piids)";
	}

	$trim_id_arr=return_library_array( "select id, item_group_id from product_details_master where item_category_id=4", "id", "item_group_id");
	$mmr_woNos_cond=""; $mmr_woNo_ids_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$mmr_woNos_cond=" and ((";
		$woNosArr=array_chunk(explode(",",$woNos),399);
		foreach($woNosArr as $idws)
		{
			$idws=implode(",",$idws);
			$mmr_woNos_cond.=" a.booking_no in ($idws) or ";
		}
		$mmr_woNos_cond=chop($mmr_woNos_cond,'or ');
		$mmr_woNos_cond.=")";

		$mmr_woNo_ids_cond=" or (";
		$woNo_ids_Arr=array_chunk(explode(",",$woNo_ids),399);
		foreach($woNo_ids_Arr as $idws)
		{
			$idws=implode(",",$idws);
			$mmr_woNos_cond.=" a.booking_id in ($idws) or ";
		}
		$mmr_woNo_ids_cond=chop($mmr_woNo_ids_cond,'or ');
		$mmr_woNo_ids_cond.="))";
	}
	else
	{
		$mmr_woNos_cond=" and (a.booking_no in ($woNos)";
		$mmr_woNo_ids_cond=" or a.booking_id in ($woNo_ids))";
	}
	//echo $woNo_ids;
	$rec_yarn_arr=array(); 
	$sql_rec_yarn=sql_select("SELECT a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty, b.order_amount, b.job_no from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1) and a.item_category in (1) and a.entry_form=248 and a.receive_purpose = 43 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobNos_cond2");
	foreach ($sql_rec_yarn as $row) {
		$$jobno=$row[csf("job_no")];
		$rec_yarn_arr[$jobno]['qty']+=$row[csf("order_qnty")];
		$rec_yarn_arr[$jobno]['amt']+=$row[csf("order_amount")];
		$rec_yarn_arr[$jobno]['booking_id'].=$row[csf("booking_id")].",";
	}


	$sql_rec="select a.receive_basis, a.item_category, a.booking_id, a.booking_no, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_amount, b.order_uom, b.order_qnty, b.order_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,2) and a.item_category in (1,5,6,7,22,23,24) and a.entry_form=1 and a.receive_purpose = 2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $mmr_woNos_cond $mmr_woNo_ids_cond";
	//echo $sql_rec; die;
	$sql_rec_res=sql_select($sql_rec);
	$rec_fab_arr=array(); $rec_trim_arr=array();

	foreach($sql_rec_res as $rrow)
	{
		$jobno='';
		if($rrow[csf("receive_basis")]==1) $jobno=$pi_job_arr[$rrow[csf("booking_id")]];
		else if($rrow[csf("receive_basis")]==2) $jobno=$booking_job_ids_arr[$rrow[csf("booking_id")]];
		if($rrow[csf("receive_basis")]==1) $jobno=$pi_job_arr[$rrow[csf("booking_id")]];
		else if($rrow[csf("receive_basis")]==2) $jobno=$booking_job_arr[$rrow[csf("booking_no")]];		
		$rec_fab_arr[$jobno][$rrow[csf("order_uom")]]['qty']+=$rrow[csf("order_qnty")];
		$rec_fab_arr[$jobno][$rrow[csf("order_uom")]]['amt']+=$rrow[csf("order_amount")];
		$rec_fab_arr[$jobno][$rrow[csf("order_uom")]]['booking_id'].=$rrow[csf("booking_id")].",";
		$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['qty']+=$rrow[csf("order_qnty")];
		$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['amt']+=$rrow[csf("order_amount")];
		$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['booking_id'].=$rrow[csf("booking_id")].",";
		$rec_trim_arr[$jobno][$trim_id_arr[$rrow[csf("prod_id")]]]['prod_id'].=$rrow[csf("prod_id")].",";
		$rec_yarn_arr[$jobno]['qty']+=$rrow[csf("order_qnty")];
		$rec_yarn_arr[$jobno]['amt']+=$rrow[csf("order_amount")];
		$rec_yarn_arr[$jobno]['booking_id'].=$rrow[csf("booking_id")].",";
	}
	unset($sql_rec_res);
	$sql_conv="select job_no, process_id, batch_issue_qty, amount from pro_grey_batch_dtls where status_active=1 and is_deleted=0 $jobNos_cond";
	$sql_conv_res=sql_select($sql_conv);
	$rec_conv_arr=array();
	foreach($sql_conv_res as $srow)
	{
		$rec_conv_arr[$srow[csf("job_no")]][$srow[csf("process_id")]]['qty']+=$srow[csf("batch_issue_qty")];
		$rec_conv_arr[$srow[csf("job_no")]][$srow[csf("process_id")]]['amt']+=$srow[csf("amount")];
	}
	unset($sql_conv_res);

	//----------------------------------------- For knitting producion-----------------------------

	$plan_job_arr=array();
	$book_job_arr=array();
	$all_po_ids=chop($poIds,",");
	$sql="select dtls_id, po_id from ppl_planning_entry_plan_dtls where po_id in ($all_po_ids) and status_active=1 and is_deleted=0";
	$all_plan_id="";
	foreach (sql_select($sql) as $val)
	{
		$all_plan_id.=$val[csf("dtls_id")].",";
		$plan_job_arr[$val[csf("dtls_id")]]=$po_job_arr[$val[csf("po_id")]];
	}
	$all_plan_id=chop($all_plan_id,",");
	if($all_plan_id=="") $all_plan_id=0;
	$sql_conv="select a.booking_id as plan_id, b.order_id, b.grey_receive_qnty as qntity from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id in($all_plan_id)";
	//echo $sql_conv;die;
	$sql_conv_res=sql_select($sql_conv);

	foreach($sql_conv_res as $srow)
	{
		$rec_conv_arr[$job][1]['qty']+=$srow[csf("qntity")];
		$rec_conv_arr[$job][1]['booking_id'] .=$srow[csf("plan_id")].",";

		$job=$plan_job_arr[$srow[csf("plan_id")]];

	}
	unset($sql_conv_res);

	//-------------------------------------- Knit Gray fabric ---------------------------------------------

	$sql="select id as booking_id, booking_no, job_no from wo_booking_mst where status_active=1 and is_deleted=0";
	foreach (sql_select($sql) as $val)
	{
		$book_job_arr[$val[csf("booking_id")]]=$val[csf("job_no")];
	}

	$sql_conv="select a.booking_id, b.order_qnty as qntity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.receive_basis in (11) and a.entry_form=22 and a.knitting_source=3";
	//echo $sql_conv;
	$sql_conv_res=sql_select($sql_conv);
	foreach($sql_conv_res as $srow)
	{
		$rec_conv_arr[$job][1]['qty']+=$srow[csf("qntity")];
		$rec_conv_arr[$job][1]['booking_id'] .=$srow[csf("booking_id")].",";

		$job=$book_job_arr[$srow[csf("booking_id")]];

	}
	unset($sql_conv_res);

	//-------------------------------------------------------------------------------------------------------

	$sql_trims_rcv = "select b.id as dtls_id,b.mst_id,b.trans_id, b.booking_id, b.item_group_id, b.rate, a.po_breakdown_id, a.order_amount, a.quantity
    					from order_wise_pro_details a, inv_trims_entry_dtls b
   						where a.dtls_id = b.id and b.trans_id = a.trans_id and b.prod_id = a.prod_id and a.entry_form = 24 and a.trans_type = 1 and a.po_breakdown_id in($all_po_ids) and b.status_active = 1 and a.status_active = 1 and b.rate > 0 and a.order_amount > 0";

    //echo $sql_trims_rcv;

    $sql_trims_rcv = sql_select($sql_trims_rcv);
    $trims_rcv_arr=array();
    foreach ($sql_trims_rcv as $row) {
    	if(isset($trims_rcv_arr[$row[csf('item_group_id')]])) {
    		$trims_rcv_arr[$row[csf('item_group_id')]]['order_amount'] += $row[csf('order_amount')];
		    $trims_rcv_arr[$row[csf('item_group_id')]]['quantity'] += $row[csf('quantity')];
    	} else {
    		$trims_rcv_arr[$row[csf('item_group_id')]]['po_id'] = $row[csf('po_breakdown_id')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['item_group_id'] = $row[csf('item_group_id')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['rate'] = $row[csf('rate')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['order_amount'] = $row[csf('order_amount')];
	    	$trims_rcv_arr[$row[csf('item_group_id')]]['quantity'] = $row[csf('quantity')];
    	}    	
    }
    //unset($sql_trims_rcv);

    $trims_rcv_id_arr=array();
    foreach ($sql_trims_rcv as $row) {
    	$trims_rcv_id_arr[$row[csf('item_group_id')]]['mst_id'] .= $row[csf('mst_id')].',';
    	$trims_rcv_id_arr[$row[csf('item_group_id')]]['trans_id'] .= $row[csf('trans_id')].',';
	}   	
    
    unset($sql_trims_rcv);
	$jobNumber = str_replace("'","",explode(',',$jobNos));

	foreach ($jobNumber as $value) {
		$costPerArr=$condition->getCostingPerArr();

		$costPerQty=$costPerArr[$value];
	}
	if($costPerQty>1){
		$costPerUom=($costPerQty/12)." Dzn";
	}else{
		$costPerUom=($costPerQty/1)." Pcs";
	}
	ob_start();

	?>

    <fieldset>
        <table width="1600"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none; font-size:18px;" colspan="19">
                    <? echo $company_arr[$cbo_company_id]; ?>
                </td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="19"> <? echo $report_title ;?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold" colspan="19"> <? if( $txt_date_from!="" && $txt_date_to!="" ) echo "From ".change_date_format($txt_date_from)." To ".change_date_format($txt_date_to);?></td>
            </tr>
        </table>
        <table width="1600" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50" rowspan="2">SL</th>
                    <th colspan="8">Style Info</th>
                    <th colspan="4">Based On Budget</th>
                    <th colspan="7">Actual</th>
                </tr>
                <tr>
                    <th width="100">Buyer</th>
                    <th width="60">Job No</th>
                    <th width="60">Job Year</th>
                    <th width="110">Style Ref.</th>
                    <th width="100">Dealing Merchant</th>
                    <th width="80">Style Qty</th>
                    <th width="60">Unit Price</th>
                    <th width="90">Style FOB Value</th>

                    <th width="160">Particulars</th>
                    <th width="80">Total Req.Qty</th>
                    <th width="50">UOM</th>
                    <th width="90">Total Amount</th>

                    <th width="80">Total WO Qty</th>
                    <th width="90">Total WO Value</th>
                    <th width="80">Total PI Qty</th>
                    <th width="90">Total PI Value</th>
                    <th width="90" title="Budgeted Total Amount - Total PI Value">Less/Extra Value</th>
                    <th width="80">Total In-House Qty</th>
                    <th>Total In-House value</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:1617px" id="scroll_body" >
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1600" rules="all" id="" >
            <?
            foreach($job_arr as $jobval)
			{
				$job_no=$jobval["job_no"];
				$trims_has +=count($trimData[$job_no]['qty']);
			}

			$i=1;

			foreach($job_arr as $jobval)
			{
				if($trims_has>0)
				{
					$colspan_num=2;
				}
				else
				{
					$colspan_num="";
				}

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$less_bgcolor="green";
				$job_no=$jobval["job_no"];
				$yarn_qty=0; $fabric_qty=0;
				$yarn_amount=0; $fabric_amount=0;
				$yarn_count=1; $fabric_count=0; $knit_count=0; $embl_count=0; $wash_count=0; $trim_count=0;
				$yarn_qty=$yarn_arr[$job_no]['qty'];
				$yarn_amount=$yarn_arr[$job_no]['amount'];

				$fabric_count=count($fabPurArr[$job_no]['qty']);
				//$knit_count=count($knitData[$job_no]['qty']);
				$trim_count=count($trimData[$job_no]['qty']);

				foreach ($knitData[$job_no]['qty'] as $process) {
					foreach ($process as $uom) {
						$knit_count++;
					}
				}

				$row_spn=$trim_count+2;
				$z=1;

				$yarn_wo_qty=0; $yarn_wo_amt=0; $pi_yarn_qty=0; $pi_yarn_amt=0; $rec_yarn_qty=0; $rec_yarn_amt=0;
				$yarn_wo_qty=$yarn_ord_arr[$job_no]['qty'];
				$yarn_wo_amt=$yarn_ord_arr[$job_no]['amt'];
				$all_job_id=implode(",", array_unique(explode(",", chop($yarn_ord_arr[$job_no]['job_id'],","))));
				//$woStatus = $cbo_search_type;

				$pi_yarn_qty=$pi_arr[$job_no]['qty'];
				$pi_yarn_amt=$pi_arr[$job_no]['amt'];
				$all_wo_no=implode(",", array_unique(explode(",", chop($pi_arr[$job_no]['wo_no'],","))));
				$all_pi_id=implode(",", array_unique(explode(",", chop($pi_arr[$job_no]['pi_id'],","))));

				if($cbo_search_type == 24){
					$rec_yarn_inhouse_qty=$rec_yarn_arr[$job_no]['qty'];
					$rec_yarn_inhouse_amt=$rec_yarn_arr[$job_no]['amt'];
				}else{
					$rec_yarn_qty=$rec_yarn_arr[$job_no]['qty'];
					$rec_yarn_amt=$rec_yarn_arr[$job_no]['amt'];
				}
				$total_amount+=$yarn_amount;
				$total_wo_amount+=$yarn_wo_amt;
				$total_pi_amount +=$pi_yarn_amt;
				$total_rcv_amount +=$rec_yarn_amt;
				$less_extra_value= $yarn_amount-$pi_yarn_amt;
				$total_less_value+=$less_extra_value;
				if($less_extra_value < 0 )
				{
					$less_bgcolor="red";
				}
				$all_booking_nos=implode(",", array_unique(explode(",", chop($rec_yarn_arr[$job_no]['booking_id'],","))));

				$data="wo_yarn_cost_dtls_sweater**".$job_no."**".$all_job_id."**".$cbo_search_type;

				$pi_data="pi_yarn_cost_dtls_sweater**".$job_no."**".$all_wo_no."**".$cbo_search_type."**".$all_pi_id;

				$inhouse_data="inhouse_yarn_cost_dtls_sweater**".$job_no."**".$all_booking_nos."**".$cbo_search_type;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                	<? if($z==1) { ?>
                	<td width="50" align="center" rowspan="<? echo $row_spn; ?>" title="<? echo $trim_count;  ?>"><? echo $i; ?></td>
                    <td width="100" rowspan="<? echo $row_spn; ?>"><? echo $buyer_arr[$jobval["buyer_name"]]; ?></td>
                    <td width="60" align="center" rowspan="<? echo $row_spn; ?>"><a href='#report_details' onclick="generate_report('<? echo $cbo_company_id; ?>','<? echo $job_no; ?>','<? echo $jobval['buyer_name']; ?>','<? echo $jobval['style_ref_no']; ?>','<? echo change_date_format($jobval['costing_date']); ?>','','<? echo $jobval['costing_per']; ?>','preCostRpt2');"><? echo $jobval["job_prefix_num"]; ?></a></td>
                    <td width="60" align="center" rowspan="<? echo $row_spn; ?>"><? echo $jobval["job_year"]; ?></td>
                    <td width="110" rowspan="<? echo $row_spn; ?>" style="word-break:break-all; word-wrap:break-word;"><p><? echo $jobval["style_ref_no"]; ?></p></td>
                    <td width="100" rowspan="<? echo $row_spn; ?>"><? echo $dealing_merchant_arr[$jobval["dealing_marchant"]]; ?></td>
                    <td width="80" align="right" rowspan="<? echo $row_spn; ?>"><? echo number_format($jobval["job_qty"],0); ?></td>
                    <td width="60" align="right" rowspan="<? echo $row_spn; ?>"><? echo number_format($jobval["avg_price"],4); ?></td>
                    <td width="90" align="right" rowspan="<? echo $row_spn; ?>"><? echo number_format($jobval["job_amount"],2); ?></td>
                    <? } ?>
                    <td width="160" colspan="<? echo $colspan_num; ?>"><? echo "Yarn Cost"; ?></td>
                    <td width="80" align="right"><? echo number_format($yarn_qty,2); ?></td>
                    <td width="50"><? echo "Lbs"; ?></td>
                    <td width="90" align="right"><? echo number_format($yarn_amount,2); ?></td>

                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                    		<? echo number_format($yarn_wo_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($yarn_wo_amt,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','950px');" href="##">
                    		<? echo number_format($pi_yarn_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($pi_yarn_amt,2); ?></td>
                    <td width="90" align="right" bgcolor="<? echo $less_bgcolor ?>"><? echo number_format($less_extra_value,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                    		<? echo number_format($rec_yarn_qty,2); ?>
                    	</a>
                    </td>
                    <td align="right"><? echo number_format($rec_yarn_amt,2); ?></td>
				</tr>
                <?
				//Lab Test
				$less_bgcolor="green";
				$lab_qty=0; $lab_amount=0; $lab_booking_qty=0; $lab_booking_amt=0; $pi_lab_qty=0; $pi_lab_amt=0; 
				$less_extra_value=0;
				$lab_qty=$jobval["job_qty"];
				$lab_amount=$othersAmtArr[$job_no]['lab_test'];
				$lab_booking_qty=$lab_test_wo_arr[$job_no]['qty'];
				$lab_booking_amt=$lab_test_wo_arr[$job_no]['amt'];

				$pi_lab_qty=$labtestPiArr[$job_no]['qty'];
				$pi_lab_amt=$labtestPiArr[$job_no]['amt'];

				$total_amount+=$lab_amount;
				$total_wo_amount+=$lab_booking_amt;
				$total_pi_amount +=$pi_lab_amt;
				$total_rcv_amount +=0;
				$less_extra_value=$lab_amount-$pi_lab_amt;
				$total_less_value+=$less_extra_value;
				if($less_extra_value < 0 )
				{
					$less_bgcolor="red";
				}

				$all_pi_id=implode(",", $labtestPiArr[$job_no]['pi_id']);

				$rec_wash_qty=$rec_emb_arr[$job_no][3]['qty'];
				$rec_wash_amt=$rec_emb_arr[$job_no][3]['amt'];
				$all_po_nos=implode(",", array_unique(explode(",", chop($rec_emb_arr[$job_no][3]['po_id'],","))));

				$all_lab_test_item = implode(",", $lab_test_item_arr[$job_no]);
				$data="wo_lab_dtls_sweater**".$job_no."**".$all_lab_test_item;

				$pi_data="pi_lab_dtls**".$job_no."**".$all_pi_id;

				$inhouse_data="inhouse_gmts_wash_dtls**".$job_no."**".$all_po_nos;

				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="160" colspan="<? echo $colspan_num; ?>"><? echo "Lab Test"; ?></td>
                    <td width="80" align="right"><? echo number_format($lab_qty,2); ?></td>
                    <td width="50"><? echo "Pcs"; ?></td>
                    <td width="90" align="right"><? echo number_format($lab_amount,2); ?></td>
                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                    		<? echo number_format($lab_booking_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($lab_booking_amt,2); ?></td>

                    <td width="80" align="right">
                    	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                    		<? echo number_format($pi_lab_qty,2); ?>
                    	</a>
                    </td>
                    <td width="90" align="right"><? echo number_format($pi_lab_amt,2); ?></td>
                    <td width="90" align="right" bgcolor="<? echo $less_bgcolor ?>"><? echo number_format($less_extra_value,2); ?></td>
                    <td width="80" align="right">0</td>
                    <td align="right">0</td>
                </tr>
				<?
				$x=1;
				//die;
				$total_less_bgcolor="green";
				foreach($trimData[$job_no]['qty'] as $id=>$iddata)
				{
					foreach($iddata as $trim_group=>$trim_qty)
					{
						$trim_amount=0; $trim_uom=0; $trim_booking_qty=0; $trim_booking_amt=0; $pi_trim_qty=0; $pi_trim_amt=0; $rec_trim_qty=0; $rec_trim_amt=0; $less_extra_value=0;
						$less_bgcolor="green";
						$trim_amount=$trimData[$job_no]['amount'][$id][$trim_group];
						$trim_uom=$trimData[$job_no]['cons_uom'][$id][$trim_group];
						$trim_booking_qty=$trim_booking_arr[$job_no][$id]['qty'];
						$trim_booking_amt=$trim_booking_arr[$job_no][$id]['amt'];

						$all_po_id=implode(",", array_unique(explode(",", chop($trim_booking_arr[$job_no][$id]['po'],","))));

						$pi_trim_qty=$pi_trim_arr[$job_no][$trim_group]['qty'];
						$pi_trim_amt=$pi_trim_arr[$job_no][$trim_group]['amt'];
						$all_wo_no=implode(",", array_unique(explode(",", chop($pi_trim_arr[$job_no][$trim_group]['wo_no'],","))));

						$rec_trim_qty=$trims_rcv_arr[$trim_group]['quantity'];
						//$rec_id=$trims_rcv_arr[$trim_group]['mst_id'];
						$trans_ids=$trims_rcv_id_arr[$trim_group]['trans_id'];
						//echo $trans_ids.'a';
						$rec_trim_amt=$trims_rcv_arr[$trim_group]['order_amount'];

						$all_book_nos=implode(",", array_unique(explode(",", chop($rec_trim_arr[$job_no][$trim_group]['booking_id'],","))));
						$all_prod_ids=implode(",", array_unique(explode(",", chop($rec_trim_arr[$job_no][$trim_group]['prod_id'],","))));
						$all_trans_ids=implode(",", array_unique(explode(",", chop($trans_ids,","))));
						//echo $all_trans_ids.'b';
						$data="wo_trims_dtls**".$job_no."**".$all_po_id."**".$trim_uom."**".$id;
						//'wo_trims_dtls**MF-20-00228**40848**1**35597'

						$pi_data="pi_trims_dtls**".$job_no."**".$all_wo_no."**".$trim_uom."**".$trim_group;
						// 'pi_trims_dtls**MF-20-00228**MF-TB-20-00092**1**154'

						//$inhouse_data="inhouse_trims_dtls**".$job_no."**".$all_wo_no."**".$trim_uom."**".$trim_group;
						$inhouse_data="inhouse_trims_dtls**".$all_trans_ids;
						//'inhouse_trims_dtls**MF-20-00228****1**154','850px'
						$total_amount+=$trim_amount;
						$total_wo_amount+=$trim_booking_amt;
						$total_pi_amount +=$pi_trim_amt;
						$total_rcv_amount +=$rec_trim_amt;
						$less_extra_value=$trim_amount-$pi_trim_amt;
						$total_less_value+=$less_extra_value;
						if($less_extra_value < 0 )
						{
							$less_bgcolor="red";
						}

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
							<? if($x==1) { ?>
                            <td width="20" align="center" rowspan="<? echo $trim_count; ?>"><div class='rotate'><? echo "Trims"; ?></div></td>
                            <? $x++; } ?>
                            <td width="140"><? echo $trim_name_arr[$trim_group]; ?></td>
                            <td width="80" align="right"><? echo number_format($trim_qty,2); ?></td>
                            <td width="50"><? echo $unit_of_measurement[$trim_uom]; ?></td>
                            <td width="90" align="right"><? echo number_format($trim_amount,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('pop_up_details','<? echo $data; ?>','850px');" href="##">
                            		<? echo number_format($trim_booking_qty,2); ?>
                            	</a>
                            </td>
                    		<td width="90" align="right"><? echo number_format($trim_booking_amt,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('pi_pop_up_details','<? echo $pi_data; ?>','850px');" href="##">
                            		<? echo number_format($pi_trim_qty,2); ?>
                            	</a>
                            </td>
                    		<td width="90" align="right"><? echo number_format($pi_trim_amt,2); ?></td>
                    		<td width="90" align="right" bgcolor="<? echo $less_bgcolor ?>"><? echo number_format($less_extra_value,2); ?></td>
                            <td width="80" align="right">
                            	<a onClick="show_popup_report_details('inhouse_pop_up_details','<? echo $inhouse_data; ?>','850px');" href="##">
                            		<? echo number_format($rec_trim_qty,2); ?>
                            	</a>
                            </td>
                    		<td align="right"><? echo number_format($rec_trim_amt,2); ?></td>
                        </tr>
						<?
					}
				}
                $i++;
			}
			if($total_less_value < 0 )
			{
				$total_less_bgcolor="red";
			}
			?>
			<tfoot>
				<tr>
					<td colspan="12"></td>
					<td><strong>Total</strong></td>
					<td align="right"><strong><? echo number_format($total_amount,2) ?></strong></td>
					<td></td>
					<td align="right"><strong><? echo number_format($total_wo_amount,2) ?></strong></td>					
					<td></td>
					<td align="right"><strong><? echo number_format($total_pi_amount,2) ?></strong></td>	
					<td align="right" bgcolor="<? echo $total_less_bgcolor ?>"><strong><? echo number_format($total_less_value,2); ?></strong></td>
					<td></td>
					<td align="right"><strong><? echo number_format($total_rcv_amount,2) ?></strong></td>
				</tr>
				<tr>
					<td colspan="16"></td>
					<td colspan="2" align="right"><strong>Total Save/Loss</strong></td>
					<td align="right" bgcolor="<? echo $total_less_bgcolor ?>"><strong><? echo number_format($total_less_value,2) ?></strong></td>
					<td></td>
					<td></td>
				</tr>
			</tfoot>
			</table>
		</div>
    </fieldset>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

?>
