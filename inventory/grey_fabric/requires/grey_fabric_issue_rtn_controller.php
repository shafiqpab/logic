<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id' and item_category_id=13 and variable_list=3 and status_active=1","fabric_roll_level");

//--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";die;
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	//load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller*13', 'store','store_td', $('#cbo_company_id').val(), this.value);
	exit();
}

// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
if ($action=="varible_inventory")
{
	$store_maintain=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	if($store_maintain=="" || $store_maintain==2) $store_maintain=0; else $store_maintain=$store_maintain;
	echo "document.getElementById('store_update_upto').value 		= '".$store_maintain."';\n";
}
// ==============End Floor Room Rack Shelf Bin upto variable Settings==============

/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=13 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select --",0,"",0);
	exit();
}*/
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/grey_fabric_issue_rtn_controller",$data);
}
if ($action=="load_drop_down_knit_com")
{
	$exDataArr = explode("**",$data);
	$knit_source=$exDataArr[0];
	$company=$exDataArr[1];
	if($exDataArr[2]=="") $exDataArr[2]=0;
	$knitting_company=$exDataArr[2];
	if($company=="" || $company==0) $company_cod = ""; else $company_cod = " and id=$company";
	if($knit_source==1)
	{
		echo create_drop_down( "cbo_knitting_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 $company_cod order by company_name","id,company_name", 0, "-- Select --", $company, "" );
	}
	else if($knit_source==3)
	{
		echo create_drop_down( "cbo_knitting_company", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", $knitting_company, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
	}
	exit();
}

if ($action=="fabbook_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>

	function fn_check()
	{
		/*if(form_validation('cbo_buyer_name','Buyer Name')==false )
			return;
		else*/
			show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $issue_purpose; ?>+'_'+<? echo $cbo_basis; ?>, 'create_fabbook_search_list_view', 'search_div', 'grey_fabric_issue_rtn_controller', 'setFilterGrid(\'list_view\',-1)');
	}

	function js_set_value(booking_dtls)
	{
 		//$("#hidden_booking_id").val(booking_id);
		$("#hidden_booking_number").val(booking_dtls);
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th>Buyer Name</th>
                    <th>Search By</th>
                    <th align="center" id="search_by_td_up">Enter <? if($cbo_basis==3) echo "Program"; else "Booking"; ?> No </th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?
						   echo create_drop_down( "cbo_buyer_name", 160, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
							if($cbo_basis==1)
							{
                            	$search_by = array(1=>'Booking No', 2=>'Buyer Order', 3=>'Job No', 4=>'Issue No');
							}
							else
							{
								$search_by = array(1=>'Booking No', 2=>'Buyer Order', 3=>'Job No', 4=>'Issue No', 5=>'Program No');
							}
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../')";
							echo create_drop_down( "cbo_search_by", 130, $search_by, "", 0, "-- --", "5", $dd, 0);
                        ?>
                    </td>
                    <td width="180" align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check()" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                    <input type="hidden" id="hidden_booking_id" value="" />
                    <input type="hidden" id="hidden_booking_number" value="" />
                    <!-- END -->
                </td>
            </tr>
            </tbody>
         </tr>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_fabbook_search_list_view")
{
 	$ex_data = explode("_",$data);
	$buyer = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
	$booking_type = $ex_data[6];
	$cbo_basis=$ex_data[7];
 	if($cbo_basis==3)
	{
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		if($buyer==0) $buyer="%%";

		if(trim($txt_search_common)!="")
		{
			if($txt_search_by==1)
			{
				$search_field_cond=" and a.booking_no like '%$txt_search_common%'";
			}
			else if($txt_search_by==2 || $txt_search_by==3)
			{
				if($txt_search_by==2) $search_field='po_number'; else $search_field='job_no_mst';

				if($db_type==0)
				{
					$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","$search_field like '%$txt_search_common%' and status_active=1 and is_deleted=0","po_id");
				}
				else
				{
					$po_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","$search_field like '%$txt_search_common%' and status_active=1 and is_deleted=0","po_id");
				}

				$search_field_cond=" and c.po_id in(".$po_id.")";
			}
			else if($txt_search_by==4)
			{
				$search_field_cond=" and e.issue_number like '%$txt_search_common%'";
			}
			else if($txt_search_by==5)
			{
				$search_field_cond=" and b.id like '$txt_search_common'";
			}
		}
		else
		{
			$search_field_cond="";
		}

		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$date_cond= " and b.program_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond= " and b.program_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}
		else $date_cond="";

		$con = connect();	
		$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = $user_id and ENTRY_FORM = 183");
		oci_commit($con);

		$sql = "SELECT a.id, a.booking_no, a.is_sales, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.id as knit_id, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date,e.issue_number_prefix_num
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c,  inv_transaction d, inv_issue_master e
		where a.id=b.mst_id and b.id=c.dtls_id and b.id=d.requisition_no and d.mst_id=e.id and e.entry_form=16 and e.issue_basis=3 and a.company_id=$company and a.buyer_id like '$buyer' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $search_field_cond
		group by b.id, a.id, a.booking_no, a.is_sales, a.buyer_id, a.body_part_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, b.color_range, b.knitting_source, b.knitting_party, b.stitch_length, b.program_date,e.issue_number_prefix_num";
		//echo $sql;
		$result = sql_select($sql);
		foreach ($result as $key => $row) 
		{
			$program_array[$row[csf('knit_id')]]=$row[csf('knit_id')];
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 183, 1,$program_array, $empty_arr); //program no

		$rqsn_array=array();
		if($db_type==0)
		{
			$reqsn_dataArray=sql_select("SELECT a.knit_id, a.requisition_no, group_concat(distinct(yarn_count_id)) as yarn_count_id, group_concat(distinct(lot)) as lot from ppl_yarn_requisition_entry a, product_details_master b 
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 group by a.knit_id, a.requisition_no");
		}
		else
		{
			$reqsn_dataArray=sql_select("SELECT a.knit_id, a.requisition_no, LISTAGG(b.yarn_count_id, ',') WITHIN GROUP (ORDER BY b.yarn_count_id) as yarn_count_id, LISTAGG(CAST(b.lot AS VARCHAR2(4000))) WITHIN GROUP (ORDER BY b.id) as lot 
			from GBL_TEMP_ENGINE g, ppl_yarn_requisition_entry a, product_details_master b 
			where g.ref_val=a.knit_id and g.user_id=$user_id and g.entry_form=183 and g.ref_from=1 and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 group by a.knit_id, a.requisition_no");
		}
		foreach($reqsn_dataArray as $row)
		{
			$rqsn_array[$row[csf('knit_id')]]['rqsn_no'].=$row[csf('requisition_no')];
			$rqsn_array[$row[csf('knit_id')]]['count'].=implode(",",array_unique(explode(",",$row[csf('yarn_count_id')])));
			$rqsn_array[$row[csf('knit_id')]]['lot'].=implode(",",array_unique(explode(",",$row[csf('lot')])));
		}

		$po_array=array();
		$po_sql="SELECT a.dtls_id, a.po_id, b.is_sales, c.id, c.job_no as job_no_mst, null as po_number, c.style_ref_no, c.customer_buyer as buyer_id
		from GBL_TEMP_ENGINE g, ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_mst b, fabric_sales_order_mst c
		where g.ref_val=a.dtls_id and g.user_id=$user_id and g.entry_form=183 and g.ref_from=1 and b.id=a.mst_id and a.po_id=c.id and b.is_sales=1
		union all
		select a.dtls_id, a.po_id, b.is_sales, c.id, c.job_no_mst, c.po_number, d.style_ref_no, d.buyer_name as buyer_id
		from GBL_TEMP_ENGINE g, ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_mst b, wo_po_break_down c, wo_po_details_master d 
		where g.ref_val=a.dtls_id and g.user_id=$user_id and g.entry_form=183 and g.ref_from=1 and a.mst_id=b.id and a.po_id=c.id and c.job_id=d.id and b.is_sales=0";
		// echo $po_sql;
		$po_sql_result = sql_select($po_sql);
		foreach($po_sql_result as $row)
		{
			$plan_details_array[$row[csf('dtls_id')]].=$row[csf('po_id')].',';
			if ($row[csf('is_sales')]==1) 
			{
				$po_array[$row[csf('dtls_id')]]['no'].=$row[csf('job_no_mst')].',';
				$po_array[$row[csf('dtls_id')]]['buyer_id']=$row[csf('buyer_id')];
			}
			else
			{
				$po_array[$row[csf('dtls_id')]]['no'].=$row[csf('po_number')].',';
				$po_array[$row[csf('dtls_id')]]['buyer_id']=$row[csf('buyer_id')];
			}
			
			$po_array[$row[csf('dtls_id')]]['job_no']=$row[csf('job_no_mst')];
			$po_array[$row[csf('dtls_id')]]['style']=$row[csf('style_ref_no')];
		}
		// echo "<pre>";print_r($plan_details_array);

		/*$po_array=array();
		$po_sql=sql_select("SELECT a.id, a.job_no_mst, a.po_number, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		}*/

		
		/*if($db_type==0)
		{
			$plan_details_array=return_library_array( "SELECT dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where company_id=$company group by dtls_id", "dtls_id", "po_id"  );
		}
		else
		{
			$plan_details_array=return_library_array( "SELECT dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company group by dtls_id", "dtls_id", "po_id"  );
		}*/

		$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = $user_id and ENTRY_FORM = 183");
		oci_commit($con);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table">
			<thead>
				<th width="30">SL</th>
                <th width="50">Issue No</th>
                <th width="50">Plan Id</th>
                <th width="50">Prog. Id</th>
                <th width="70">Prog. Date</th>
                <th width="50">Reqsn. No</th>
				<th width="110">Booking No</th>
				<th width="70">Buyer</th>
				<th width="100">PO No</th>
				<th width="90">Job No</th>
                <th width="130">Fabric Desc</th>
				<th width="55">Gsm</th>
				<th width="55">Dia</th>
				<th>Color Range</th>
			</thead>
		</table>
		<div style="width:1040px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1022" class="rpt_table" id="list_view">
            <?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$reqn_no=$rqsn_array[$row[csf('knit_id')]]['rqsn_no'];

					$po_id=array_unique(explode(",",chop($plan_details_array[$row[csf('knit_id')]],",")));
					/*$po_no=''; $job_no=''; $style_ref_no='';

					foreach($po_id as $val)
					{
						if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=",".$po_array[$val]['no'];
						if($job_no=='') $job_no=$po_array[$val]['job_no'];
						if($style_ref_no=='') $style_ref_no=$po_array[$val]['style'];
					}*/

					$po_no_id=implode(",",array_unique(explode(",",chop($plan_details_array[$row[csf('knit_id')]],","))));
					$po_no=implode(",",array_unique(explode(",",chop($po_array[$row[csf('knit_id')]]['no'],","))));
					$job_no=$po_array[$row[csf('knit_id')]]['job_no'];
					$style_ref_no=$po_array[$row[csf('knit_id')]]['style'];

					if ($row[csf('is_sales')]==1) 
					{
						$row[csf('buyer_id')]=$po_array[$row[csf('knit_id')]]['buyer_id'];
					}
					

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('knit_id')]; ?>_<? echo $row[csf('knit_id')]; ?>_<? echo $row[csf('buyer_id')]; ?>_<? echo $style_ref_no; ?>_<? echo $po_no; ?>_<? echo $po_no_id; ?>');">
						<td width="30"><? echo $i; ?></td>
						<td width="50" align="center"><? echo $row[csf('issue_number_prefix_num')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('id')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('knit_id')]; ?></td>
                        <td width="70" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
						<td width="50" align="center"><? echo $reqn_no; ?>&nbsp;</td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="100" title="<?=$po_no_id;?>"><p><? echo $po_no; ?></p></td>
                        <td width="90"><p><? echo $job_no; ?></p></td>
						<td width="130"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
						<td width="55"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
						<td width="55"><p><? echo $row[csf('dia')]; ?></p></td>
						<td><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
            </table>
        </div>
		<?
	}
	else
	{
		$sql_cond="";
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==1) // for Booking No
			{
				$sql_cond .= " and a.booking_no LIKE '%$txt_search_common%'";
			}
			else if(trim($txt_search_by)==2) // for buyer order
			{
				$sql_cond .= " and b.po_number LIKE '%$txt_search_common%'";	// wo_po_break_down
			}
			else if(trim($txt_search_by)==3) // for job no
			{
				$sql_cond .= " and a.job_no LIKE '%$txt_search_common%'";
			}
			else if(trim($txt_search_by)==4) // for issue no
			{
				$sql_cond .= " and c.issue_number LIKE '%$txt_search_common%'";
			}
		}

		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}

		if( trim($buyer)!=0 ) $sql_cond .= " and a.buyer_id='$buyer'";
		if( trim($company)!=0 ) $sql_cond .= " and a.company_id='$company'";
		if( trim($booking_type)==1 ) $sql_cond .= " and a.booking_type!=4";
		else if( trim($booking_type)==4 ) $sql_cond .= " and a.booking_type=4";

		if(trim($booking_type)==8 )
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst, c.issue_number_prefix_num
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b,inv_issue_master c
				where
					a.booking_no=b.booking_no and
					a.id=c.booking_id and
					c.entry_form=16 and
					c.issue_basis=1 and
					a.status_active=1 and
					a.is_deleted=0 and
					b.status_active=1 and
					b.is_deleted=0
					$sql_cond
					group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no, c.issue_number_prefix_num";
		}
		else
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst , c.issue_number_prefix_num
			from wo_booking_mst a, wo_po_break_down b ,inv_issue_master c, wo_booking_dtls d
			where
				a.booking_no = d.booking_no and
				d.job_no=b.job_no_mst and
				a.id=c.booking_id and
				c.entry_form=16 and
				c.issue_basis=1 and
				a.status_active=1 and
				a.is_deleted=0 and
				b.status_active=1 and
				b.is_deleted=0
				$sql_cond
				group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no, c.issue_number_prefix_num";
		}
		//item_category=2 knit fabrics
		//echo $sql;
		$result = sql_select($sql);

		// for checking Reference Closing

		$sql_2 = "SELECT DISTINCT INV_PUR_REQ_MST_ID, CLOSING_STATUS FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 AND insert_date IN (  SELECT MAX (insert_date) FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 GROUP BY INV_PUR_REQ_MST_ID)";
		// echo $sql_2;

		$result_2 = sql_select($sql_2);

		foreach($result as $key=> $row)
		{
			foreach($result_2 as $val)
			{
				if(($row['ID'] == $val['INV_PUR_REQ_MST_ID']) && ($val['CLOSING_STATUS']==1)) {
					// echo $row['ID']."  ";
					unset($result[$key]);
				}
			}
		}

		$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		?>
		<div align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="60">Issue No</th>
                <th width="105">Booking No</th>
				<th width="90">Book. Date</th>
				<th width="100">Buyer</th>
				<th width="90">Item Cat.</th>
				<th width="90">Job No</th>
				<th width="90">Order Qnty</th>
				<th width="80">Ship. Date</th>
				<th >Order No</th>
			</thead>
		</table>

		<div style="width:1040px; max-height:240px; overflow-y:scroll" id="list_container_batch" >
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" id="list_view">
			<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$po_qnty_in_pcs=0; $po_no=''; $po_no_id=''; $min_shipment_date='';
					if( trim($booking_type)!=8 )
					{
						$po_sql = "select a.style_ref_no, b.po_number,b.id, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in (".$row[csf('po_break_down_id')].")";
						$nameArray=sql_select($po_sql);
						$style_ref_no="";
						foreach ($nameArray as $po_row)
						{
							if($po_no=="") $po_no=$po_row[csf('po_number')]; else $po_no.=",".$po_row[csf('po_number')];
							if($po_no_id=="") $po_no_id=$po_row[csf('id')]; else $po_no_id.=",".$po_row[csf('id')];

							if($min_shipment_date=='')
							{
								$min_shipment_date=$po_row[csf('pub_shipment_date')];
							}
							else
							{
								if($po_row[csf('pub_shipment_date')]<$min_shipment_date) $min_shipment_date=$po_row[csf('pub_shipment_date')]; else $min_shipment_date=$min_shipment_date;
							}

							$po_qnty_in_pcs+=$po_row[csf('po_qnty_in_pcs')];
							$style_ref_no = $po_row[csf('style_ref_no')];
						}
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('booking_no')]; ?>_<? echo $row[csf('buyer_id')]; ?>_<? echo $style_ref_no; ?>_<? echo $po_no; ?>_<? echo $po_no_id; ?>');">
						<td width="30"><? echo $i; ?></td>
						<td width="60" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="90" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="90"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
						<td width="90"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="90" align="right"><? echo $po_qnty_in_pcs; ?></td>
						<td width="80" align="center"><? echo change_date_format($min_shipment_date); ?></td>
						<td><p>
						<?
						$po_no=implode(",",array_unique(explode(",",$po_no)));
						  echo $po_no;
						?>
                        </p></td>
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

if($action=="return_po_popup")
{
	echo load_html_head_contents("Issue Return Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_issue_id=str_replace("'","",$txt_issue_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$update_id=str_replace("'","",$update_id);
	$txt_return_qnty=str_replace("'","",$txt_return_qnty);

	$store_id=str_replace("'","",$store_id);
	$floor_id=str_replace("'","",$floor_id);
	$room_id=str_replace("'","",$room_id);
	$rack_id=str_replace("'","",$rack_id);
	$self_id=str_replace("'","",$self_id);

	if($update_id>0 && $txt_return_qnty>0)
	{
		$order_sql=sql_select("select po_breakdown_id,quantity from order_wise_pro_details where trans_id=$update_id and trans_type=4 and entry_form=51 and status_active=1");
		foreach($order_sql as $row)
		{
			$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]=$row[csf("quantity")];
		}
	}
	//echo $variable_setting_production;die;

	if($variable_setting_production==1)
	{
		$table_width=600;
		$txt_break_roll=explode("_",$txt_break_roll);
		foreach($txt_break_roll as $val)
		{
			$txt_break_roll_data=explode("**",$val);
			$po_id=$txt_break_roll_data[0];
			$roll_no=$txt_break_roll_data[1];
			$qty=$txt_break_roll_data[2];

			// $order_wise_qnty_arr[$po_id][$roll_no]=$qty;
			$order_wise_qnty_arr[$po_id]=$qty;
		}
		$disabled="disabled='disabled'";
	}
	else
	{
		$table_width=500;
		$txt_break_qnty=explode("_",$txt_break_qnty);
		foreach($txt_break_qnty as $val)
		{
			$txt_break_qnty_data=explode("**",$val);
			$po_id=$txt_break_qnty_data[0];
			$qty=$txt_break_qnty_data[1];

			$order_wise_qnty_arr[$po_id]=$qty;
		}
		$disabled='';
	}
	?>
	<script>
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var total_balance_quantity=$('#total_balance_quantity').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;

				if(txt_prop_grey_qnty>total_balance_quantity)
				{
					alert("Return Qnty not available");
					$('#txt_prop_grey_qnty').val("");
					return;
				}
				var len=totalFinish=0;
				$("#pop_table tbody").find('tr').each(function()
				{
					len=len+1;
					var row_balance = $("#issueqnty_"+len).attr("placeholder")*1;
					var perc=(row_balance/total_balance_quantity)*100;
					var return_qnty=(perc*txt_prop_grey_qnty)/100;
					return_qnty = return_qnty.toFixed(2);
					$("#issueqnty_"+len).val(return_qnty);
				});
			}
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$("#pop_table tbody").find('tr').each(function()
				{
					$(this).find('input[name="issueqnty[]"]').val('');
				});
			}
		}
		function js_set_value()
		{
			var table_legth=$('#pop_table tbody tr').length;
			var break_qnty=break_roll=break_id="";
			var tot_qnty=0;
			for(var i=1; i<=table_legth; i++)
			{
				//if(i!=1) break_qnty+="_";
				tot_qnty +=($("#issueqnty_"+i).val()*1);
				if(break_qnty!="") break_qnty +="_";
				break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
				if(break_roll!="") break_roll +="_";
				break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
				if(break_id!="") break_id +=",";
				break_id+=($("#poId_"+i).val()*1);
			}
			$("#tot_qnty").val(tot_qnty);
			$("#break_qnty").val(break_qnty);
			$("#break_roll").val(break_roll);
			$("#break_order_id").val(break_id);
			$('#distribution_method').val( $('#cbo_distribiution_method').val());
			parent.emailwindow.hide();
		}

		function fn_calculate(id)
		{
			var recv_qnty=($("#recevqnty_"+id).val()*1);
			var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
			var issue_qnty=($("#issueqnty_"+id).val()*1);
			var hiddenissue_qnty=($("#hiddenissueqnty_"+id).val()*1);
			if(((cumu_qnty*1)+(issue_qnty*1))>((recv_qnty*1)+(hiddenissue_qnty*1)))
			{
				alert("Return Quantity Can not be Greater Than Receive Quantity.");
				$("#issueqnty_"+id).val(0);
			}
		}
	</script>
	</head>
	<body>
	    <div align="center" style="width:100%;" >
	    	<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
					<thead>
						<th>Total Return Qnty</th>
						<th>Distribution Method</th>
					</thead>
					<tr class="general">
						<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_return_qnty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>/></td>

						<td>
							<?
							$distribiution_method=array(1=>"Proportionately",2=>"Manually");
							echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",$disable_drop_down );

							?>
						</td>
					</tr>
				</table>
			</div>
			<br>
		    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" id="pop_table">
	                <thead>
	                    <tr>
	                        <th width="140">Order No</th>
	                        <th width="120">Issue Quantity</th>
	                        <th width="120">Cumulative Return</th>
	                        <?
							if($variable_setting_production==1)
							{
								?>
								<th>Roll</th>
								<?
							}
							?>
	                        <th width="120">Return Quantity</th>
	                    </tr>
	                </thead>
	                <tbody>
		                <?
						$po_no_arr = return_library_array("select id,po_number from wo_po_break_down","id","po_number");

						if($floor_id =="" && $db_type==2)
						{
							$floor_cond_a = " and a.floor_id is null";
							$floor_cond_b = " and b.floor_id is null";
						}
						else
						{
							$floor_cond_a = " and a.floor_id='$floor_id'";
							$floor_cond_b = " and b.floor_id='$floor_id'";
						}
						if($room_id =="" && $db_type==2)
						{
							$room_cond_a = " and a.room is null";
							$room_cond_b = " and b.room is null";
						}
						else
						{
							$room_cond_a = " and a.room='$room_id'";
							$room_cond_b = " and b.room='$room_id'";
						}
						if($rack_id =="" && $db_type==2)
						{
							$rack_cond_a = " and a.rack is null";
							$rack_cond_b = " and b.rack is null";
						}
						else
						{
							$rack_cond_a = " and a.rack='$rack_id'";
							$rack_cond_b = " and b.rack='$rack_id'";
						}
						if($self_id =="" && $db_type==2)
						{
							$self_cond_a = " and a.self is null";
							$self_cond_b = " and b.self is null";
						}
						else
						{
							$self_cond_a = " and a.self='$self_id'";
							$self_cond_b = " and b.self='$self_id'";
						}

						$sql=sql_select("SELECT a.prod_id, a.po_breakdown_id, sum(a.quantity) as receive_qnty, b.mst_id from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_issue_id' and b.prod_id='$txt_prod_id' and a.entry_form in(16) and b.transaction_type in(2) and b.store_id='$store_id' $floor_cond_b $room_cond_b $rack_cond_b $self_cond_b group by b.mst_id, a.prod_id, a.po_breakdown_id");
						$i=1;
						foreach($sql as $row)
						{
							$cumilitive_issue=return_field_value("sum(c.quantity) as cumu_qnty","inv_transaction a, inv_receive_master b,  order_wise_pro_details c","a.mst_id=b.id and a.id=c.trans_id and c.status_active=1 and b.issue_id='$txt_issue_id' and c.prod_id='".$row[csf('prod_id')]."' and a.store_id='".$store_id."' $floor_cond_a $room_cond_a $rack_cond_a $self_cond_a and c.po_breakdown_id='".$row[csf('po_breakdown_id')]."'","cumu_qnty");
							$return_balance=$row[csf("receive_qnty")]-$cumilitive_issue;
							?>
		                	<tr>
		                    	<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
		                        <input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
		                        </td>
		                        <td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo number_format($row[csf("receive_qnty")],2);  ?>" readonly disabled ></td>
		                        <td align="center"><input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue_<? echo $i; ?>" value="<? echo number_format($cumilitive_issue,2); ?>" class="text_boxes_numeric" style="width:110px" readonly disabled ></td>
		                        <?
								if($variable_setting_production==1)
								{
									?>
									<td align="center"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
									<?
								}
								else
								{
									?>
									<td align="center" style="display:none;"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
									<?
								}
								?>
		                        <td align="center">
		                        <input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" placeholder="<?=$return_balance+$order_wise_qnty_arr[$row[csf("po_breakdown_id")]];?>">
		                        <input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>">
		                        </td>

		                    </tr>
		                    <?
							$i++;
							$balance_quantity += $return_balance+$order_wise_qnty_arr[$row[csf("po_breakdown_id")]];
						}
						?>
	                </tbody>
		        </table>
		        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
		            <tr>
		                <td align="center">
			                <input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
			                <input type="hidden" id="tot_qnty" name="tot_qnty" >
			                <input type="hidden" id="break_qnty" name="break_qnty" >
			                <input type="hidden" id="break_roll" name="break_roll" >
			                <input type="hidden" id="break_order_id" name="break_order_id" >
			                <input type="hidden" id="total_balance_quantity" name="total_balance_quantity" value="<? echo $balance_quantity;?>">
			                <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
		                </td>
		            </tr>
		        </table>
		    </form>
	    </div>
	</body>
	<?

}

if($action=="itemdesc_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
	<table width="830" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
            <input type="hidden" id="hidden_recv_number" name="hidden_recv_number" >
                <tr>
                    <th width="50">SL</th>
                    <th width="100" >Product Id</th>
                    <th width="120">Issue Number</th>
                    <th width="80">Issue Date</th>
                    <th width="240">Production Description</th>
                    <th width="100">Lot</th>
                    <th>Quantity</th>
                </tr>
            </thead>
    </table>
    <div style="width:830px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
	<table width="812" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="prod_list_view">
            <tbody>
            <?
			//$issue_lot_arr=return_library_array("select mst_id, max(yarn_lot) as yarn_lot from  inv_grey_fabric_issue_dtls where status_active=1 and is_deleted=0 group by mst_id","mst_id","yarn_lot");
			if($basis==3)
			{
				$sql_prod=sql_select("select a.company_id, a.id as issue_id, a.issue_number, a.issue_date, sum(b.cons_quantity) as cons_quantity, c.id as prod_id, c.product_name_details, d.yarn_lot, d.yarn_count, b.store_id, b.floor_id, b.room, b.rack, b.self, d.stitch_length
				from inv_issue_master a, inv_transaction b, product_details_master c, inv_grey_fabric_issue_dtls d
				where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=16 and a.company_id=$company and b.requisition_no=$booking_id and a.issue_basis=$basis
				group by a.company_id, a.id, a.issue_number, a.issue_date, c.id, c.product_name_details, d.yarn_lot, d.yarn_count, b.store_id, b.floor_id, b.room, b.rack, b.self, d.stitch_length");

			}
			else
			{
				$sql_prod=sql_select("select a.company_id, a.id as issue_id, a.issue_number, a.issue_date, sum(b.cons_quantity) as cons_quantity, c.id as prod_id, c.product_name_details, d.yarn_lot, d.yarn_count, b.store_id, b.floor_id, b.room, b.rack, b.self, d.stitch_length
				from inv_issue_master a, inv_transaction b, product_details_master c, inv_grey_fabric_issue_dtls d
				where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=16 and a.company_id=$company and a.booking_id=$booking_id and a.issue_basis=$basis
				group by a.company_id, a.id, a.issue_number, a.issue_date, c.id, c.product_name_details, d.yarn_lot, d.yarn_count, b.store_id, b.floor_id, b.room, b.rack, b.self, d.stitch_length");
			}
			$i=1;
			foreach($sql_prod as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("issue_id")]."_".$row[csf("prod_id")]."_".$row[csf("yarn_lot")]."_".$row[csf("yarn_count")]."_".$row[csf("rack")]."_".$row[csf("self")]."_".urlencode($row[csf("stitch_length")])."_".$row[csf("company_id")]."_".$row[csf("store_id")]."_".$row[csf("floor_id")]."_".$row[csf("room")]; ?>');" style="cursor:pointer;">
                    <td width="50"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $row[csf("prod_id")]; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><? echo $row[csf("issue_number")]; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!='000-00-00') echo change_date_format($row[csf("issue_date")]); ?>&nbsp;</p></td>
                    <td width="240"><p><? echo $row[csf("product_name_details")]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row[csf("yarn_lot")]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf("cons_quantity")],2); ?></p></td>
            	</tr>
                <?
				$i++;
			}
			?>
            </tbody>
    </table>
    </div>
   </div>
   <script>setFilterGrid('prod_list_view',-1);</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



/*if($action=="create_item_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$booking_no = $ex_data[5];
	$basis = $ex_data[6];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for lot no
 			$sql_cond .= " and c.lot LIKE '%$txt_search_common%'";
 		else if(trim($txt_search_by)==2) // for issue no
 			$sql_cond .= " and a.issue_number LIKE '%$txt_search_common%'";
		else if(trim($txt_search_by)==3) // for chllan no
 			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		else if(trim($txt_search_by)==4) // for Item Description
 			$sql_cond .= " and c.product_name_details LIKE '%$txt_search_common%'";
 	}

	if($booking_no!="") $sql_cond .= " and a.issue_basis=$basis and a.booking_no='$booking_no'";

	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql="select a.id,a.issue_purpose,a.issue_number_prefix_num,a.issue_number,$year_field c.product_name_details,c.lot,a.challan_no,c.current_stock,c.id as prod_id
			from inv_issue_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and c.status_active=1 and b.item_category=13 and b.transaction_type=2 and a.entry_form=16 $sql_cond group by a.id, a.issue_purpose, a.issue_number_prefix_num, a.issue_number, a.challan_no, a.insert_date, c.id, c.product_name_details, c.lot, c.current_stock order by a.id desc";
	//echo $sql;// and c.current_stock>0
	$arr=array(2=>$yarn_issue_purpose);
 	echo create_list_view("list_view", "Issue No, Year, Issue Purpose, Item Name Details, Lot No, Challan No","70,55,125,250,100","820","260",0, $sql , "js_set_value", "prod_id,id", "", 1, "0,0,issue_purpose,0,0,0", $arr, "issue_number_prefix_num,year,issue_purpose,product_name_details,lot,challan_no", "","",'0,0,0,0,0,0') ;
	exit();
}*/


if($action=="populate_data_from_data")
{
	$ex_data = explode("_",$data);

	$issueID = trim($ex_data[0]);
	$prodID = trim($ex_data[1]);
	$yarn_lot = trim($ex_data[2]);
	$yarn_count = trim($ex_data[3]);
	$rack = trim($ex_data[4]);
	$self = trim($ex_data[5]);
	$stitch_length = trim($ex_data[6]);
	$company_id = trim($ex_data[7]);
	$store_id = trim($ex_data[8]);
	$floor_id = trim($ex_data[9]);
	$room = trim($ex_data[10]);

	$sql_condition="";
	if($db_type==0)
	{
		if($yarn_lot!="")
			$sql_condition=" and d.yarn_lot='$yarn_lot'";
		else
			$sql_condition=" and d.yarn_lot=' '";
	}
	else
	{
		if($yarn_lot!="")
			$sql_condition=" and d.yarn_lot='$yarn_lot'";
		else
			$sql_condition=" and d.yarn_lot is null";
	}

	if($yarn_count!="") $sql_condition.=" and d.yarn_count='$yarn_count'";

	if($stitch_length!="") $sql_condition.=" and d.stitch_length='$stitch_length'";
	//$sql_condition .= " and b.store_id='$store_id' and b.floor_id= '$floor_id' and b.room='$room' and b.rack='$rack' and b.self='$self'";

	if($store_id!="") $store_cond=" and b.store_id='$store_id'";
	if($floor_id!="") $floor_cond=" and b.floor_id= '$floor_id'";
	if($room!="") $room_cond=" and b.room='$room'";
	if($rack!="") $rack_cond=" and b.rack='$rack'";
	if($self!="") $self_cond=" and b.self='$self'";

	$sql = "SELECT a.id, a.issue_purpose, a.issue_number_prefix_num, a.challan_no, c.id as prod_id,c.product_name_details,c.unit_of_measure,sum(b.cons_quantity) as cons_quantity, max(b.cons_rate) as cons_rate, sum(b.cons_amount) as cons_amount, d.yarn_lot, d.yarn_count, b.store_id, b.floor_id, b.room, b.rack, b.self, d.stitch_length
	from inv_issue_master a, inv_transaction b, product_details_master c, inv_grey_fabric_issue_dtls d
	where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id  and a.id=$issueID and c.id=$prodID and b.item_category=13 and b.transaction_type=2  $sql_condition $store_cond $floor_cond $room_cond $rack_cond $self_cond
	group by a.id, a.issue_purpose, a.issue_number_prefix_num, a.challan_no, c.id, c.product_name_details,c.unit_of_measure, d.yarn_lot, d.yarn_count, b.store_id, b.floor_id, b.room, b.rack, b.self, d.stitch_length";

	$res = sql_select($sql);
	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	 	where b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id";
	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
 	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row)
 	{
 		$company  = $room_rack_shelf_row[csf("company_id")];
 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
 		$room_id  = $room_rack_shelf_row[csf("room_id")];
 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
 			$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
 			$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
 		}
 	}
 	unset($lib_room_rack_shelf_arr);

 	$store_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$company_id' and b.category_type=13 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id","store_name");
	//echo $sql; //die;//and a.challan_no='$challan_no'
	foreach($res as $row)
	{
		//$product_description=$row[csf("product_name_details")];

	 	$floor_name = $lib_floor_arr[$row[csf("floor_id")]];
 		$room_name = $lib_room_arr[$row[csf("floor_id")]][$row[csf("room")]];
 		$rack_name = $lib_rack_arr[$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]];
 		$self_name = $lib_shelf_arr[$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]];

		echo "$('#txt_item_description').val('".$row[csf("product_name_details")],$row[csf("gsm")],$row[csf("dia_width")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_yarn_lot').val('".$row[csf("yarn_lot")]."');\n";
		echo "$('#yarn_count').val('".$row[csf("yarn_count")]."');\n";
		echo "$('#stitch_length').val('".$row[csf("stitch_length")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("unit_of_measure")].");\n";
		echo "$('#txt_issue_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_issue_qnty').val('".$row[csf("cons_quantity")]."');\n";

		$cons_rate = number_format($row[csf("cons_rate")],2,'.','');
		echo "$('#txt_rate').val('".$cons_rate."');\n";
		echo "$('#txt_hdn_consRate').val('".$row[csf("cons_rate")]."');\n";

		$totalReturned = return_field_value("sum(cons_quantity)","inv_transaction","issue_id='".$row[csf("id")]."' and prod_id='".$row[csf("prod_id")]."' and batch_lot='".$row[csf("yarn_lot")]."' and yarn_count='".$row[csf("yarn_count")]."' and stitch_length='".$row[csf("stitch_length")]."' and store_id='".$store_id."' and floor_id='".$row[csf("floor_id")]."' and room='".$row[csf("room")]."' and rack='".$row[csf("rack")]."' and self='".$row[csf("self")]."' and item_category=13 and transaction_type=4 and status_active=1 and is_deleted=0");
		if($totalReturned=="") $totalReturned=0;
		echo "$('#txt_total_return').val('".$totalReturned."');\n";
		echo "$('#txt_total_return_display').val('".$totalReturned."');\n";
		$netUsed = $row[csf("cons_quantity")]-$totalReturned;
		echo "$('#txt_net_used').val('".$netUsed."');\n";

		$cons_amount = number_format($row[csf("cons_amount")],2,'.','');
		echo "$('#txt_amount').val('".$cons_amount."');\n";


		echo "$('#txt_rack_issue').val('".$row[csf("rack")]."');\n";
		echo "$('#txt_self_issue').val('".$row[csf("self")]."');\n";

		echo "$('#cbo_store_name').val('".$store_id."');\n";
		echo "$('#cbo_store_name_show').val('".$store_arr[$store_id]."');\n";

		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "$('#cbo_floor_show').val('".$floor_name."');\n";

		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "$('#cbo_room_show').val('".$room_name."');\n";

		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "$('#txt_rack_show').val('".$rack_name."');\n";

		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "$('#txt_shelf_show').val('".$self_name."');\n";

		echo "$('#cbo_store_name_show').attr('disabled','true')".";\n";
		echo "$('#cbo_floor_show').attr('disabled','true')".";\n";
		echo "$('#cbo_room_show').attr('disabled','true')".";\n";
		echo "$('#txt_rack_show').attr('disabled','true')".";\n";
		echo "$('#txt_shelf_show').attr('disabled','true')".";\n";
   	}
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_issue_purpose)==3 || str_replace("'","",$cbo_issue_purpose)==8) $book_without_order=0; else $book_without_order=1;


        //------------------Check Receive Date with last Issue Date-------------------
        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id = $txt_prod_id and transaction_type in (2,3,6)", "max_date");
        if($max_issue_date !="")
        {
            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
            $receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));

            if ($receive_date < $max_issue_date)
            {
                echo "20**Return Date Can not Be Less Than Last Issue Date Of This Lot";
                die;
            }
        }
        //-----------------------------------------------------------------------------

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//---------------Check Duplicate product in Same return number ------------------------//
		//*txt_rack_issue*txt_self_issue*yarn_count*stitch_length*txt_yarn_lot
		$dup_cond="";
		if(str_replace("'","",$txt_yarn_lot)!="") $dup_cond=" and c.yarn_lot=$txt_yarn_lot";
		if(str_replace("'","",$yarn_count)!="") $dup_cond.=" and c.yarn_count=$yarn_count";
		if(str_replace("'","",$stitch_length)!="") $dup_cond.=" and c.stitch_length=$stitch_length";
		if(str_replace("'","",$txt_rack_issue)!="") $dup_cond.=" and c.rack=$txt_rack_issue";
		if(str_replace("'","",$txt_self_issue)>0) $dup_cond.=" and c.self=$txt_self_issue";

		$duplicate=is_duplicate_field("b.id","inv_receive_master a, inv_transaction b, inv_grey_fabric_issue_dtls c","a.id=b.mst_id and b.id=c.trans_id and a.id=$issue_mst_id and b.prod_id=$txt_prod_id and b.transaction_type=4 $dup_cond");
		if($duplicate==1)
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			//check_table_status( $_SESSION['menu_id'], 0 );
			disconnect($con);
			die;
		}
		//------------------------------Check Duplicate END---------------------------------------//



 		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$issue_mst_id);
			//issue master table UPDATE here START----------------------//
 			$field_array_mst="receive_basis*receive_purpose*booking_id*booking_no*booking_without_order*receive_date*location_id*knitting_source*knitting_company*issue_id*challan_no*updated_by*update_date";
			$data_array_mst=$cbo_basis."*".$cbo_issue_purpose."*".$txt_booking_id."*".$txt_booking_no."*".$book_without_order."*".$txt_return_date."*".$cbo_location."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$txt_issue_id."*".$txt_return_challan_no."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
		}
		else
		{
			//issue master table entry here START---------------------------------------//
			//$id=return_next_id("id", "inv_receive_master", 1);

			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			//$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KGIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form=51 and $year_cond=".date('Y',time())." order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'KGIR',51,date("Y",time()),13 ));

 			$field_array_mst="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_basis, receive_purpose, booking_id, booking_no, booking_without_order, receive_date, location_id, knitting_source, knitting_company, issue_id, challan_no, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',51,13,".$cbo_company_id.",".$cbo_basis.",".$cbo_issue_purpose.",".$txt_booking_id.",".$txt_booking_no.",".$book_without_order.",".$txt_return_date.",".$cbo_location.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_issue_id.",".$txt_return_challan_no.",'".$user_id."','".$pc_date_time."')";
			//echo "20**".$field_array_mst."<br>".$data_array_mst;die;
		}

		//echo str_replace("'","",$txt_return_qnty).'*'.str_replace("'","",$txt_hdn_consRate).'<br>';
		$currentAmount = (str_replace("'","",$txt_return_qnty)*str_replace("'","",$txt_hdn_consRate));
		$currentAmount = number_format($currentAmount,2,".","");

		//transaction table insert here START--------------------------------//cbo_uom yarn_count*txt_yarn_lot
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,company_id, booking_without_order,prod_id,item_category,transaction_type,transaction_date,order_uom,order_qnty,cons_uom,cons_quantity,cons_rate,cons_amount,store_id,floor_id,room,rack,self,remarks,issue_id,issue_challan_no,batch_lot,yarn_count,stitch_length,inserted_by,insert_date";
 		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$book_without_order.",".$txt_prod_id.",13,4,".$txt_return_date.",".$cbo_uom.",".$txt_return_qnty.",".$cbo_uom.",".$txt_return_qnty.",".$txt_hdn_consRate.",".$currentAmount.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_remarks.",".$txt_issue_id.",".$txt_issue_challan_no.",".$txt_yarn_lot.",".$yarn_count.",".$stitch_length.",'".$user_id."','".$pc_date_time."')";
		//echo "5**insert into inv_transaction($field_array_trans) values".$data_array_trans;die;

		//adjust product master table START-------------------------------------//
		//echo "5**select product_name_details,last_purchased_qnty,current_stock from product_details_master where id=$txt_prod_id";die;
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock, stock_value from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$product_name_details 	=$result[csf("product_name_details")];
		}
		$nowStock 					= $presentStock+str_replace("'","",$txt_return_qnty);
		$nowStockValue 				= $presentStockValue+str_replace("'","",$currentAmount);
		if($nowStock >0)
		{
			//echo $nowStockValue.'/'.$nowStock.'<br>';
			$nowStockRate = $nowStockValue/$nowStock;
		}else{
			$nowStockRate =0;
			$nowStockValue=0;
		}

		//echo "10**string";die;
		//$rtn_qnty=str_replace("'","",$txt_return_qnty);
		//$nowStock 		= $presentStock+$rtn_qnty;
		$field_array_prod="last_purchased_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*".$nowStockValue."*".$nowStockRate."*".$user_id."*'".$pc_date_time."'";


		//order_wise_pro_detail table insert here
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,inserted_by,insert_date";


		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$transactionID.",4,51,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",'".$user_id."','".$pc_date_time."')";
					//$proportion_id++;
				}
			}

			if($variable_setting_production==1)
			{
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,qnty,inserted_by,insert_date";

				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);

					if($order_roll_arr[1]>0)
					{
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$order_roll_arr[0].",51,".$order_roll_arr[1].",".$order_roll_arr[2].",'".$user_id."','".$pc_date_time."')";
						//$roll_id++;
					}
				}
			}
		}

		$rID=$transID=$prodUpdate=$propoId=$rollId=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_receive_master",$field_array_mst,$data_array_mst,1);
		}
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_proportion!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			if($variable_setting_production==1)
			{
				$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			}
		}

		//echo "5**".$rID ."**". $transID ."**".  $prodUpdate ."**".  $propoId ."**".  $rollId;die;

		if($db_type==0)
		{
			if( $rID && $transID && $prodUpdate && $propoId && $rollId )
			{
				mysql_query("COMMIT");
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_return_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $transID && $prodUpdate && $propoId && $rollId)
			{
				oci_commit($con);
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_return_number[0];
			}
		}
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//table lock here
		$issue_mst_id= str_replace("'","",$issue_mst_id);
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";
			//check_table_status( $_SESSION['menu_id'], 0 );
			disconnect($con);
			die;
		}

		$txt_return_qnty=str_replace("'","",$txt_return_qnty);

		$currentAmount = (str_replace("'","",$txt_return_qnty)*str_replace("'","",$txt_hdn_consRate));
		$currentAmount = number_format($currentAmount,2,".","");

		//*************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock, a.stock_value, b.cons_amount, b.cons_quantity from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=13 and b.item_category=13 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_amnt  = $result[csf("cons_amount")];
		}

		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);

		$present_prod_sql = sql_select("select current_stock, stock_value from product_details_master where id=$txt_prod_id and item_category_id=13");
		$curr_stock_qnty= $present_prod_sql[0][csf("current_stock")];
		$curr_stock_value= $present_prod_sql[0][csf("stock_value")];

 		//echo $receive_purpose;die;
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		// echo "10**".$before_prod_id.'=='.$txt_prod_id;die;
		$update_array_prod= "last_purchased_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			//echo "10**".$curr_stock_qnty.'-'.$before_issue_qnty.'+'.$txt_return_qnty.'='.$curr_stock_value.'-'.$before_issue_amnt.'+'.str_replace("'","",$currentAmount);die;

			$adj_stock_qnty = (($curr_stock_qnty-$before_issue_qnty)+$txt_return_qnty); // CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_value =  (($curr_stock_value-$before_issue_amnt)+str_replace("'","",$currentAmount));

			if($adj_stock_qnty>0){
				$adj_stock_rate = $adj_stock_value/$adj_stock_qnty;
			}else{
				$adj_stock_rate =0;
				$adj_stock_value=0;
			}

			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*'".$adj_stock_value."'*'".$adj_stock_rate."'*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty-$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_value = $before_stock_value-$before_issue_amnt;

			if($adj_before_stock_qnty>0)
			{
				$adj_before_stock_rate = $adj_before_stock_value/$adj_before_stock_qnty;
			}
			else
			{
				$adj_before_stock_rate =0;
				$adj_before_stock_value=0;
			}

			$updateIdprod_array[]=$before_prod_id;
			$update_dataProd[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$adj_before_stock_qnty."*'".$adj_before_stock_value."'*'".$adj_before_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));

			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty+$txt_return_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_value = $curr_stock_value+str_replace("'","",$currentAmount);
			if($adj_curr_stock_qnty>0){
				$adj_curr_stock_rate = $adj_curr_stock_value/$adj_curr_stock_qnty;
			}else{
				$adj_curr_stock_rate =0;
				$adj_curr_stock_value=0;
			}

			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*'".$adj_curr_stock_value."'*'".$adj_curr_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));
		}
		// echo "10**string";die;
  		$id=str_replace("'","",$issue_mst_id);
		//yarn master table UPDATE here START----------------------//
		$field_array_mst="receive_basis*receive_purpose*booking_id*booking_no*booking_without_order*receive_date*location_id*knitting_source*knitting_company*issue_id*challan_no*updated_by*update_date";
		$data_array_mst=$cbo_basis."*".$cbo_issue_purpose."*".$txt_booking_id."*".$txt_booking_no."*".$book_without_order."*".$txt_return_date."*".$cbo_location."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$txt_issue_id."*".$txt_return_challan_no."*'".$user_id."'*'".$pc_date_time."'";

		//,rack,self yarn_count*txt_yarn_lot
 		$field_array_trans="company_id*prod_id*item_category*transaction_type*transaction_date*order_uom*order_qnty*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*remarks*issue_id*issue_challan_no*batch_lot*yarn_count*stitch_length*updated_by*update_date";
 		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*13*4*".$txt_return_date."*".$cbo_uom."*".$txt_return_qnty."*".$cbo_uom."*".$txt_return_qnty."*".$txt_hdn_consRate."*".$currentAmount."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_remarks."*".$txt_issue_id."*".$txt_issue_challan_no."*".$txt_yarn_lot."*".$yarn_count."*".$stitch_length."*'".$user_id."'*'".$pc_date_time."'";
		//echo $field_array."<br>".$data_array;die;
		$update_id = str_replace("'","",$update_id);
		//order_wise_pro_detail table insert here

		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,inserted_by,insert_date";

		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",4,51,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",'".$user_id."','".$pc_date_time."')";
					//$proportion_id++;
				}
			}

			if($variable_setting_production==1)
			{
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,qnty,inserted_by,insert_date";

				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);

					if($order_roll_arr[2]>0)
					{
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$update_id.",".$order_roll_arr[0].",51,".$order_roll_arr[1].",".$order_roll_arr[2].",'".$user_id."','".$pc_date_time."')";
						//$roll_id++;
					}
				}
			}
		}

 		$query1=$query4=$query5=$rID=$transID=$propoId=$rollId=true;

		if($before_prod_id==$txt_prod_id)
		{
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
		}
		else
		{
			//echo "10**".bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array);die;
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
		}
		// echo "10**string";die;
		$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		if($data_array_proportion!="")
		{
			$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=51");
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			if($variable_setting_production==1)
			{
				$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=51");
				$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			}
		}

		// echo "5**".$rID ."**". $transID ."**".  $query1 ."**".  $propoId ."**".  $rollId;die;

		if($db_type==0)
		{
			if($query1 && $query4 && $query5 && $rID && $transID && $propoId && $rollId)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_return_no)."**".$issue_mst_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query4 && $query5 && $rID && $transID && $propoId && $rollId)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_return_no)."**".$issue_mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		 //no operation
	}
}


if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
		var all_data=mrr.split("_");
 		$("#hidden_return_number").val(all_data[0]); // mrr number
		$('#hidden_posted_account').val(all_data[1]);
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="170">Search By</th>
                    <th width="270" align="center" id="search_by_td_up">Enter Return Number</th>
                    <th width="220">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?
                            $search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'grey_fabric_issue_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_return_number" value="" />
                     <input type="hidden" name="hidden_posted_account" id="hidden_posted_account"  value="">
                    <!-- END -->
                </td>
            </tr>
            </tbody>
         </tr>
        </table>
        <div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_return_search_list_view")
{

	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and recv_number like '%$search_common'";
	}
	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if($company!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql = "select a.id as mst_id,a.recv_number_prefix_num, a.recv_number, a.company_id, a.supplier_id, a.receive_date, a.item_category, a.recv_number, $year_field b.id, b.cons_quantity, b.cons_reject_qnty, b.cons_uom,b.cons_rate,b.cons_amount,c.product_name_details, c.id as prod_id,c.lot, a.is_posted_account
			from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
			where a.id=b.mst_id and b.item_category=13 and a.entry_form=51 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 $sql_cond order by a.id DESC";
 	//echo $sql;
	$arr=array(5=>$unit_of_measurement);
 	echo create_list_view("list_view", "Return No, Year, Item Description, Return Qnty, Rejected Qnty, UOM, Lot No","80,60,250,100,100,80,100","850","260",0, $sql , "js_set_value", "mst_id,is_posted_account", "", 1, "0,0,0,0,0,cons_uom,0", $arr, "recv_number_prefix_num,year,product_name_details,cons_quantity,cons_reject_qnty,cons_uom,lot","","",'0,0,0,2,2,0,0') ;
 	exit();
}


if($action=="populate_master_from_data")
{

 	$sql = "select id,recv_number,entry_form,item_category,company_id,receive_basis,receive_purpose,receive_date,booking_id,booking_no,knitting_source,knitting_company,yarn_issue_challan_no,challan_no,store_id,location_id,buyer_id,exchange_rate,currency_id,supplier_id,lc_no,source
			from inv_receive_master
			where id='$data'";
	//echo $sql;die;
	$res = sql_select($sql);
	foreach($res as $row)
	{
 		echo "set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);";
		echo "$('#txt_return_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		//echo "load_drop_down( 'requires/grey_fabric_issue_rtn_controller', ".$row[csf("company_id")].", 'load_drop_down_location', 'location_td' );\n";
		//echo "load_drop_down( 'requires/grey_fabric_issue_rtn_controller', ".$row[csf("company_id")].", 'load_drop_down_store', 'store_td' );\n";
 		echo "$('#cbo_basis').val('".$row[csf("receive_basis")]."');\n";
		echo "$('#cbo_issue_purpose').val('".$row[csf("receive_purpose")]."');\n";
		echo "active_inactive('".$row[csf("receive_basis")]."');\n";
		echo "$('#txt_booking_no').val('".$row[csf("booking_no")]."');\n";
		echo "$('#txt_booking_id').val('".$row[csf("booking_id")]."');\n";
		echo "$('#cbo_location').val('".$row[csf("location_id")]."');\n";
		echo "$('#cbo_knitting_source').val('".$row[csf("knitting_source")]."');\n";
		echo "load_drop_down( 'requires/grey_fabric_issue_rtn_controller', ".$row[csf("knitting_source")]."+'**'+".$row[csf("company_id")]."+'**'+".$row[csf("knitting_company")].", 'load_drop_down_knit_com', 'knitting_company_td' );\n";
		//echo "$('#cbo_knitting_company').val('".$row[csf("knitting_company")]."');\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_return_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "disable_enable_fields( 'cbo_company_id', 1, '', '' );\n"; // disable true

   	}
	exit();
}



if($action=="show_dtls_list_view")
{

/*	$ex_data = explode("**",$data);
	$return_number = $ex_data[0];
	$ret_mst_id = $ex_data[1];

	$cond="";
	if($return_number!="") $cond .= " and a.recv_number='$return_number'";
	if($ret_mst_id!="") $cond .= " and a.id='$ret_mst_id'";*/

	$sql = "select a.recv_number,a.company_id,a.supplier_id,a.receive_date,a.item_category,a.recv_number,b.id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id
			from  inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
			where a.id=b.mst_id and b.item_category=13 and b.transaction_type=4 and a.entry_form=51 and a.id=$data and b.status_active=1 and b.is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$rejtotalQnty=0;
	$totalAmount=0;
	?>
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:980px" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Return No</th>
                    <th>Item Description</th>
                    <th>Product ID</th>
                    <th>Return Qty</th>
                    <th>UOM</th>
                    <th>Rate</th>
                    <th>Return Value</th>
                </tr>
            </thead>
            <tbody>
            	<?
				foreach($result as $row){
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$rettotalQnty +=$row[csf("cons_quantity")];
 					$totalAmount +=$row[csf("cons_amount")];

 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/grey_fabric_issue_rtn_controller")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="250"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td width="80" align="right" style="padding-right:3px;"><p><? echo $row[csf("cons_quantity")]; ?></p></td>
                        <td width="60"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo $row[csf("cons_rate")]; ?></p></td>
                        <td width="100" align="right"><p><? echo $row[csf("cons_amount")]; ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="4">Total</th>
                        <th><? echo $rettotalQnty; ?></th>
                        <th colspan="2"></th>
                        <th><? echo $totalAmount; ?></th>
                   </tfoot>
            </tbody>
        </table>
    <?
	exit();
}


if($action=="child_form_input_data")
{
	//$data // transaction id
	$store_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id","store_name");

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	 	where b.status_active=1 and b.is_deleted=0 "; //and b.company_id=$company_id
	 	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
 	foreach ($lib_floor_arr as $room_rack_shelf_row)
 	{
 		$company  = $room_rack_shelf_row[csf("company_id")];
 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
 		$room_id  = $room_rack_shelf_row[csf("room_id")];
 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
 			$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
 			$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
 		}
 	}

	$sql = "SELECT a.company_id,b.id as prod_id, b.product_name_details, a.id as tr_id, a.store_id, a.floor_id, a.room, a.rack, a.self, a.issue_id, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.issue_challan_no,a.remarks, a.batch_lot, a.yarn_count, a.stitch_length, a.issue_id
	from inv_transaction a, product_details_master b
	where a.id=$data and a.status_active=1 and a.item_category=13 and transaction_type=4 and a.prod_id=b.id and b.status_active=1";
 	//echo $sql;die;
	$result = sql_select($sql);

	foreach($result as $row)
	{
		$issue_purpose=return_field_value("issue_purpose","inv_issue_master","id='".$row[csf("issue_id")]."'");

		echo "return_qnty_basis(".$issue_purpose.");\n";

 		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_yarn_lot').val('".$row[csf("lot")]."');\n";
		//echo "load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller*13', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		//echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		//echo "load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		//echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		//echo "load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		//echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		//echo "load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		//echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		//echo "load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		//echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";

		$floor_name = $lib_floor_arr[$row[csf("floor_id")]];
 		$room_name = $lib_room_arr[$row[csf("floor_id")]][$row[csf("room")]];
 		$rack_name = $lib_rack_arr[$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]];
 		$self_name = $lib_shelf_arr[$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]];

		echo "$('#cbo_store_name').val('".$row[csf('store_id')]."');\n";
		echo "$('#cbo_store_name_show').val('".$store_arr[$row[csf('store_id')]]."');\n";

		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "$('#cbo_floor_show').val('".$floor_name."');\n";

		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "$('#cbo_room_show').val('".$room_name."');\n";

		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "$('#txt_rack_show').val('".$rack_name."');\n";

		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "$('#txt_shelf_show').val('".$self_name."');\n";

		echo "$('#cbo_store_name_show').attr('disabled','true')".";\n";
		echo "$('#cbo_floor_show').attr('disabled','true')".";\n";
		echo "$('#cbo_room_show').attr('disabled','true')".";\n";
		echo "$('#txt_rack_show').attr('disabled','true')".";\n";
		echo "$('#txt_shelf_show').attr('disabled','true')".";\n";

		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
 		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
		echo "$('#txt_yarn_lot').val('".$row[csf("batch_lot")]."');\n";
		echo "$('#yarn_count').val('".$row[csf("yarn_count")]."');\n";
		echo "$('#stitch_length').val('".$row[csf("stitch_length")]."');\n";
		//echo "$('#txt_rack_issue').val('".$row[csf("rack")]."');\n";
		//echo "$('#txt_self_issue').val('".$row[csf("self")]."');\n";

		$propotion_sql=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where trans_id='".$row[csf("tr_id")]."'");
		$po_wise_qnty="";$po_id_all="";
		foreach($propotion_sql as $row_order)
		{
			if($po_wise_qnty!="") $po_wise_qnty .="_";
			$po_wise_qnty .=$row_order[csf("po_breakdown_id")]."**".$row_order[csf("quantity")];
			if($po_id_all!="") $po_id_all .=",";
			$po_id_all .=$row_order[csf("po_breakdown_id")];
		}
		if($variable_setting_production==1)
		{
			$roll_sql=sql_select("select po_breakdown_id, roll_no, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$row[csf("tr_id")]."'");
			$roll_ref="";
			foreach($roll_sql as $row_roll)
			{
				if($roll_ref!="") $roll_ref .="_";
				$roll_ref .=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("qnty")];
			}
		}

		echo "$('#txt_break_qnty').val('$po_wise_qnty');\n";
		echo "$('#txt_break_roll').val('$roll_ref');\n";
		echo "$('#txt_order_id_all').val('$po_id_all');\n";


		$totalIssued = return_field_value("sum(b.cons_quantity)","inv_transaction b, inv_grey_fabric_issue_dtls c","b.id=c.trans_id and b.mst_id='".$row[csf("issue_id")]."' and b.prod_id='".$row[csf("prod_id")]."' and c.yarn_lot='".$row[csf("batch_lot")]."' and c.yarn_count='".$row[csf("yarn_count")]."' and c.stitch_length='".$row[csf("stitch_length")]."' and b.store_id='".$row[csf('store_id')]."' and b.floor_id='".$row[csf("floor_id")]."' and b.room='".$row[csf("room")]."' and b.rack='".$row[csf("rack")]."' and b.self='".$row[csf("self")]."' and b.item_category=13 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		if($totalIssued=="") $totalIssued=0;
		echo "$('#txt_issue_qnty').val('".$totalIssued."');\n";


		//$totalReturn = return_field_value("sum(cons_quantity)","inv_transaction","issue_id='".$row[csf("issue_id")]."' and prod_id='".$row[csf("prod_id")]."' and item_category=13 and transaction_type=4");
		$totalReturn = return_field_value("sum(cons_quantity)","inv_transaction","issue_id='".$row[csf("issue_id")]."' and prod_id='".$row[csf("prod_id")]."' and batch_lot='".$row[csf("batch_lot")]."' and yarn_count='".$row[csf("yarn_count")]."' and stitch_length='".$row[csf("stitch_length")]."' and store_id='".$row[csf('store_id')]."' and floor_id='".$row[csf("floor_id")]."' and room='".$row[csf("room")]."' and rack='".$row[csf("rack")]."' and self='".$row[csf("self")]."' and item_category=13 and transaction_type=4 and status_active=1 and is_deleted=0");
		echo "$('#txt_total_return_display').val('".$totalReturn."');\n";
		// echo $totalIssued.'+'.$row[csf("cons_quantity")].'-'.$totalReturn;die;
		$netUsed = (($totalIssued+$row[csf("cons_quantity")])-$totalReturn);
		echo "$('#txt_net_used').val('".$netUsed."');\n";
		echo "$('#hide_net_used').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_amount').val(".$row[csf("cons_amount")].");\n";
		echo "$('#txt_hdn_consRate').val('".$row[csf("cons_rate")]."');\n";
		// echo "$('#txt_rtn_amount').val(".$row[csf("cons_amount")].");\n";
		echo "$('#txt_issue_challan_no').val('".$row[csf("issue_challan_no")]."');\n";
		echo "$('#update_id').val(".$row[csf("tr_id")].");\n";

	}
 	echo "set_button_status(1, permission, 'fnc_yarn_issue_return_entry',1,1);\n";
  	exit();
}

if ($action=="issue_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company=$data[0];
	$location=$data[4];

	$sql=" select id, recv_number, receive_basis, booking_no, booking_id, knitting_source, knitting_company, challan_no, receive_date from  inv_receive_master where id='$data[3]' and entry_form=51 and item_category=13";

	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
        <table width="900" cellspacing="0" align="right">
            <tr>
                <td colspan="6" align="center" style="font-size:22px"><strong><? echo $com_dtls[0]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?
                    	echo $com_dtls[1];
                       /* $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]");
                        foreach ($nameArray as $result)
                        {
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?>
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?>
                            Block No: <? echo $result[csf('block_no')];?>
                            City No: <? echo $result[csf('city')];?>
                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                            Province No: <? echo $result[csf('province')];?>
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                            Email Address: <? echo $result[csf('email')];?>
                            Website No: <? echo $result[csf('website')];
                        }*/
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
            </tr>
            <tr>
                <td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
                <td width="130"><strong>Basis:</strong></td> <td width="175px"><? echo $issue_basis[$dataArray[0][csf('receive_basis')]]; ?></td>
                <td width="125"><strong>Book/Prog. No:</strong></td><td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Return Source:</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
                <td><strong>Knitting Com :</strong></td><td><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; elseif ($dataArray[0][csf('knitting_source')]==3) echo $supplier_library[$dataArray[0][csf('knitting_company')]]; ?></td>
                <td><strong>Return Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Return Challan:</strong></td> <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td><strong>&nbsp;</strong></td><td><? //echo $dataArray[0][csf('challan_no')]; ?></td>
                <td><strong>&nbsp;</strong></td><td><? //echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
     <br>
        <div style="width:100%;">
        <table align="right" cellspacing="0" cellpadding="0" width="900" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="300">Item Description</th>
                <th width="80">Lot</th>
                <th width="70">UOM</th>
                <th width="100">Returned Qty.</th>
                <th width="120">Store</th>
                <th>Remarks</th>
            </thead>
            <tbody>
        <?
            $store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

            $i=1;
            $mst_id=$dataArray[0][csf('id')];

            $sql_dtls="Select a.id as pd_id, a.product_name_details, a.lot, b.id, b.cons_uom, b.cons_quantity, b.store_id, b.cons_reject_qnty, b.remarks from product_details_master a, inv_transaction b where a.id=b.prod_id and b.transaction_type=4 and b.item_category=13 and b.mst_id='$data[3]' and b.status_active=1 and b.is_deleted=0";
            //echo $sql_dtls;
            $sql_result = sql_select($sql_dtls);
            foreach($sql_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td><p><? echo $row[csf("product_name_details")]; ?></p></td>
                    <td><? echo $row[csf("lot")]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_quantity")],2,'.',''); ?></td>
                    <td><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <?
                $cons_quantity_sum+=$row[csf('cons_quantity')];
                $i++;
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" align="right">Total :</td>
                    <td align="right"><? echo number_format($cons_quantity_sum,2,'.',''); ?></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <br>
         <?
            echo signature_table(87, $data[0], "900px");
         ?>
        </div>
	</div>
	<?
    exit();
}
?>