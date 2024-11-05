<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if($action=='get_report_id'){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=275 and is_deleted=0 and status_active=1");
	echo $print_report_format; die;
}
 
if ($action=="order_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);

	
	?>
	<script>
		function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value( str ){
			var data=str.split("_");
		//	alert(data[3]);
			document.getElementById('selected_job').value=data[0];
			document.getElementById('selected_year').value=data[1];
			document.getElementById('selected_company').value=data[2];
			document.getElementById('selected_exchange_rate').value=data[3];

			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1110" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="12"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="120">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Internal Ref</th>
                        <th width="80">File No</th>
                        <th width="80">Order No</th>
                        <th width="100">Shipment Status</th>
                        <th width="100">Date Category</th>
                        <th colspan="2" width="130" id="search_by_td_up">Enter Ship Date</th>
                        <th>
                            <input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">
                            <input type="hidden" id="selected_job">
                            <input type="hidden" id="selected_year">
                            <input type="hidden" id="selected_company">
                            <input type="hidden" id="selected_exchange_rate">
                            <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
                            <input type="hidden" id="budget_version" value="<? echo $cbo_budget_version; ?>">
                            <input type="hidden" id="cost_control" value="<? echo $cbo_costcontrol_source; ?>">
                            Job Without PO
                        </th>
                     </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_mst", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_name,"load_drop_down( 'style_wise_cost_comparison_report_controller2', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td>
                        <?
                        echo create_drop_down( "shipping_status", 100, $shipment_status,"", 0, "-- Select --", 3, "",0,'','','','','' );
                        $search_by = array(1 => 'Country Ship Date', 2 => 'Ship Date', 3 => 'Ex Factory');
                        $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../../')";
                        ?>
                    </td>
                    <td><? echo create_drop_down("cbo_search_by", 100, $search_by, "", 0, "--Select--", "", $dd, 0); ?></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('shipping_status').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('budget_version').value, 'create_po_search_list_view', 'search_div', 'style_wise_cost_comparison_report_controller2', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div id="search_div"></div>
            </form>
        </div>
    </body>
     <script>
        load_drop_down( 'style_wise_cost_comparison_report_controller2', <? echo $company_name;?>, 'load_drop_down_buyer', 'buyer_td' );
    </script>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
			
if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	$cbo_search_by = str_replace("'","",$data[14]);
	$budget_version = str_replace("'","",$data[15]);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if(str_replace("'","",$data[3])!="" && str_replace("'","",$data[4])!="")
	{
	 	if($db_type==0)
		{
			$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
			$start_date=change_date_format(str_replace("'","",$data[3]),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$data[4]),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
			$start_date=change_date_format(str_replace("'","",$data[3]),"","",1);
			$end_date=change_date_format(str_replace("'","",$data[4]),"","",1);
		}

		$date_cond="";
		if($cbo_search_by==1) $date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
		else if($cbo_search_by==2) $date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		else if($cbo_search_by==3) $date_cond=" and d.ex_factory_date between '$start_date' and '$end_date'";
	}

	if($budget_version==1) $budget_version_cond="and f.entry_from=111";
	else $budget_version_cond="and f.entry_from in(158,425,521,520)";

	//echo $date_cond.'dssssssssss';
	if($cbo_search_by==3 && $data[13]==3)
	{
		if(str_replace("'","",$data[3])=="" && str_replace("'","",$data[4])=="")
		{
			echo "Please Select Ex-Factory Date Range"; die;
		}
	}
	
	$order_cond=""; $job_cond=""; $style_cond="";
	//echo  $data[8];die;
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no like '%$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no like '$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no like '%$data[6]'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[11]);

	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	$shiping_status=0;
	if($data[13]!=0) $shiping_status=" and b.shiping_status=$data[13]"; else $shiping_status=" ";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$exchange_rate_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst",'job_no','exchange_rate');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
		
	if ($data[2]==0)
	{
		if($cbo_search_by==1 || $cbo_search_by==2)
		{
			$sql="select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.id as po_id,b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst f where a.id=b.job_id and c.job_id=a.id and c.po_break_down_id=b.id  and a.id=f.job_id and b.job_id=f.job_id and c.job_id=f.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1  $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond $budget_version_cond group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no DESC";
		}
		else
		{
			$sql="select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.id as po_id,b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_ex_factory_mst d,wo_pre_cost_mst f where a.id=b.job_id and c.job_id=a.id and c.po_break_down_id=b.id  and c.po_break_down_id=d.po_break_down_id and b.id=d.po_break_down_id and a.id=f.job_id and b.job_id=f.job_id and c.job_id=f.job_id and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1  $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $date_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond $budget_version_cond group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.shipment_date,a.garments_nature,b.id,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no DESC";
		}
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);

		$sql="select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a,wo_pre_cost_mst f where   a.id=f.job_id and a.id not in( select distinct job_id from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $budget_version_cond $job_cond $style_cond order by a.job_no DESC";
	}
	$result=sql_select($sql);
	if($cbo_search_by==3 && $data[13]==3)
	{
		$maxDateChkRes = sql_select("select a.job_no,a.id as job_id, b.id,c.ex_factory_date from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.id = b.job_id and b.id = c.po_break_down_id  and a.status_active = 1 and b.status_active = 1 and c.status_active=1  $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond ");
		foreach ($maxDateChkRes as $md)
		{
			if($maxDateChkArr[$md[csf("job_no")]]["ex_date"]=="")
			{
				$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];
			}else if($maxDateChkArr[$md[csf("job_no")]]["ex_date"] < $md[csf("ex_factory_date")])
			{
				$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];
			}
		}
	}
	$to_date=str_replace("'","",$data[4]);
	$po_chk = array();

	foreach ($result as $row)
	{
		if($cbo_search_by==3 && $data[13]==3)
		{
			if(strtotime($maxDateChkArr[$row[csf("job_no")]]["ex_date"]) <= strtotime($to_date))
			{
				if($po_chk[$row[("po_id")]] == "")
				{
					$style_po_arr[$row[csf("job_no")]]["job_prefix"]= $row[csf("job_no_prefix_num")];
					$style_po_arr[$row[csf("job_no")]]["job_no"]= $row[csf("job_no")];
					$style_po_arr[$row[csf("job_no")]]["po_number"].= $row[csf("po_number")].',';
					$style_po_arr[$row[csf("job_no")]]["company_name"]= $row[csf("company_name")];
					$style_po_arr[$row[csf("job_no")]]["buyer_name"]= $row[csf("buyer_name")];
					$style_po_arr[$row[csf("job_no")]]["style_ref_no"]= $row[csf("style_ref_no")];
					$style_po_arr[$row[csf("job_no")]]["garments_nature"]= $row[csf("garments_nature")];
					$style_po_arr[$row[csf("job_no")]]["year"]= $row[csf("year")];
					$style_po_arr[$row[csf("job_no")]]["job_quantity"]= $row[csf("job_quantity")];
					$style_po_arr[$row[csf("job_no")]]["po_quantity"]+= $row[csf("po_quantity")];
					$style_po_arr[$row[csf("job_no")]]["shipment_date"]=$maxDateChkArr[$row[csf("job_no")]]["ex_date"];// $row[csf("ex_factory_date")];
					$style_po_arr[$row[csf("job_no")]]["grouping"].= $row[csf("grouping")].',';
					$style_po_arr[$row[csf("job_no")]]["date_diff"]+= $row[csf("date_diff")];
					$style_po_arr[$row[csf("job_no")]]["file_no"].= $row[csf("file_no")].',';
				}
			}
		}
		else
		{
			$style_po_arr[$row[csf("po_id")]]["job_prefix"]= $row[csf("job_no_prefix_num")];
			$style_po_arr[$row[csf("po_id")]]["job_no"]= $row[csf("job_no")];
			$style_po_arr[$row[csf("po_id")]]["po_number"]= $row[csf("po_number")];
			$style_po_arr[$row[csf("po_id")]]["company_name"]= $row[csf("company_name")];
			$style_po_arr[$row[csf("po_id")]]["buyer_name"]= $row[csf("buyer_name")];
			$style_po_arr[$row[csf("po_id")]]["style_ref_no"]= $row[csf("style_ref_no")];
			$style_po_arr[$row[csf("po_id")]]["garments_nature"]= $row[csf("garments_nature")];
			$style_po_arr[$row[csf("po_id")]]["year"]= $row[csf("year")];
			$style_po_arr[$row[csf("po_id")]]["job_quantity"]= $row[csf("job_quantity")];
			$style_po_arr[$row[csf("po_id")]]["po_quantity"]= $row[csf("po_quantity")];
			$style_po_arr[$row[csf("po_id")]]["shipment_date"]= $row[csf("shipment_date")];
			$style_po_arr[$row[csf("po_id")]]["grouping"]= $row[csf("grouping")];
			$style_po_arr[$row[csf("po_id")]]["date_diff"]= $row[csf("date_diff")];
			$style_po_arr[$row[csf("po_id")]]["file_no"]= $row[csf("file_no")];
		}
	}
	?>
	<div>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="80">	Job No</th>
			<th width="60">Year</th>
			<th width="120">Company</th>
			<th width="110">Buyer</th>
			<th width="110">Style Ref. No</th>
			<th width="100">Gmts Nature</th>
			<th width="80">Job Qty.</th>
			<th width="100">PO number</th>
			<th width="80">PO Qty</th>
			<th width="70"><? if($cbo_search_by==3 && $data[13]==3) echo "Ex-Factory";else echo "Shipment Date";?></th>
			<th width="70">Ref no</th>
			<th width="70">File no</th>
			<th>Lead time</th>
		</thead>
	</table>
	<div style="width:1180px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" id="list_view">
		<?

		$k=1;
		foreach($style_po_arr as $po_id=>$row)
		{
			if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$exchange_rate=$exchange_rate_arr[$row[('job_no')]];
			$style_data=$row[("job_prefix")].'_'.$row[("year")].'_'.$row[('company_name')].'_'.$exchange_rate;
			$po_number=rtrim($row[("po_number")],",");
			$po_number=implode(",",array_unique(explode(",",$po_number)));
			$grouping=rtrim($row[("grouping")],",");
			$grouping=implode(",",array_unique(explode(",",$grouping)));
			$file_no=rtrim($row[("file_no")],",");
			$file_no=implode(",",array_unique(explode(",",$file_no)));

		?>
			<tr bgcolor="<? echo $bgcolor;?>"   style="cursor:pointer;" id="tr_<? echo $k; ?>" onClick="js_set_value('<? echo $style_data; ?>');change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')">
                <td width="30"> <? echo $k; ?></td>
                <td width="80"><? echo $row[('job_prefix')]; ?></td>
                <td width="60"><? echo $row[('year')];; ?></td>
                <td width="120"><? echo $comp[$row[('company_name')]];//$garments_item[$row[csf('company_name')]]; ?></td>
                <td width="110"><p><? echo $buyer_arr[$row[('buyer_name')]]; ?></p></td>
                <td width="110"><p><? echo $row[('style_ref_no')]; ?></p></td>
                <td width="100"><? echo $item_category[$row[('garments_nature')]]; ?></p></td>
                <td width="80"><? echo $row[('job_quantity')]; ?></td>
                <td width="100"><p><? echo $po_number; ?></p></td>
                <td width="80"><? echo $row[('po_quantity')]; ?></td>
                <td width="70"><p><? echo change_date_format($row[('shipment_date')]); ?></td>
                <td width="70"><p><? echo $grouping; ?></p></td>
                <td width="70"><p><? echo $file_no; ?></p></td>
                <td width=""><? echo $row[('date_diff')]; ?></td>
			</tr>
			<?
			$k++;
		}
			?>
		</table>
	</div>
    </div>
    <?
	exit();
}

if($action=="load_exchange_rate")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$cbo_year=$data[1];
	$job_no=$data[2];
	$budget_version=$data[3];

	if($budget_version==1) $budget_version_cond="and f.entry_from=111"; else $budget_version_cond="and f.entry_from in(158,425,521,520)";

	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	
	$sql_data=sql_select("select b.job_no,b.exchange_rate from wo_po_details_master a,wo_pre_cost_mst b where  a.id=b.job_id and a.company_name=$company_id and a.job_no_prefix_num=$job_no $year_cond $budget_version_cond");
	

	 $exchange_rate= $sql_data[0][csf('exchange_rate')];
	 echo $exchange_rate;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo str_replace("'","",$cbo_costcontrol_source);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");

	if(str_replace("'","",$cbo_costcontrol_source)==1)
	{
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){ echo "Select Company"; die; }
		if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		$check_data=sql_select("select a.job_no from wo_po_details_master a,wo_pre_cost_mst f where  a.id=f.job_id  and a.company_name=$company_name and a.garments_nature=3 and a.job_no_prefix_num like '$job_no' $year_cond group by a.job_no");
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		if($chk_job_nos!='') 
		{
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.inquiry_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
			$result=sql_select($sql);
		}
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
	$shipArry=array(0,1,2);
	$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$setSmv=$row[csf('set_smv')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotationId=$row[csf('inquiry_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$ShipDateArrall[date("d-m-Y",strtotime($row[csf('shipment_date')]))]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			/* if($row[csf('shiping_status')]==3) //Full
			{
			 $shiping_status=2; 
			} */
			//echo $shiping_status.'D';
			//$shipingStatus=$shiping_status;
			$shipingStatus=$row[csf('shiping_status')];
			$shiping_status_id.=$row[csf('shiping_status')].',';
			$po_ids.=$row[csf('id')].',';$ref_no.=$row[csf('grouping')].',';
			$team_leaders.=$team_leader_library[$row[csf('team_leader')]].',';
			$dealing_marchants.=$dealing_merchant_array[$row[csf('dealing_marchant')]].',';
		}
		$sql_budget="select machine_line,prod_line_hr,sew_effi_percent from wo_pre_cost_mst a where status_active = 1 and is_deleted = 0 and entry_from=425 and job_no='$jobNumber'";
	    $res_budget=sql_select($sql_budget);
		foreach($res_budget as $row){
			$machine_line=$row[csf('machine_line')];
			$prod_line_hr=$row[csf('prod_line_hr')];
			$sew_effi_percent=$row[csf('sew_effi_percent')];
		}

		$sql_sample="SELECT sum(d.required_qty) as qnty FROM wo_po_details_master a, sample_development_mst c,sample_development_fabric_acc d WHERE a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 and d.sample_mst_id=c.id and c.entry_form_id=117 and a.style_ref_no=c.style_ref_no AND c.quotation_id = a.id AND d.form_type = 1 AND d.is_deleted = 0 AND d.status_active = 1 AND a.job_no='$jobNumber'";
	    $res_sample=sql_select($sql_sample);

	    $sql_sample_fab="SELECT SUM (fin_fab_qnty) AS qnty FROM wo_booking_dtls WHERE status_active = 1 AND is_deleted = 0 AND job_no='$jobNumber' AND entry_form_id = 440";
	    $res_sample_fab=sql_select($sql_sample_fab);

	    $booking_sql3=sql_select("SELECT SUM (a.fin_fab_qnty) AS qnty FROM wo_booking_dtls a, wo_booking_mst b WHERE a.booking_mst_id = b.id AND a.is_short = 2 AND b.booking_type = 4 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.job_no = '$jobNumber'");
				
	    $sample_booking_all_qnty=$res_sample[0][csf('qnty')]+$res_sample_fab[0][csf('qnty')]+$booking_sql3[0][csf('qnty')];
		$shiping_status_id=rtrim($shiping_status_id,',');
		
		$shiping_status_idAll=array_unique(explode(",",$shiping_status_id));
		//print_r($shiping_status_idAll);
		$shiping_status=3;
		foreach($shiping_status_idAll as $sid)
		{
			//echo $sid."D,";
			if(in_array($sid,$shipArry))
			{
				$shiping_status=2;
			}
		}
		//echo $shiping_status;
		$team_leaders=rtrim($team_leaders,',');
		$team_leaders=implode(",",array_unique(explode(",",$team_leaders)));
		$dealing_marchants=rtrim($dealing_marchants,',');
		$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchants)));
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0) $po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; else $po_cond_for_in="";
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessValue=$shortExcessQty*($unitPrice);
	    //$quotationId=1;
		
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	    // Yarn ============================
		$totYarn=0;
		$fabPurArr=array(); $YarnData=array(); $qYarnData=array(); $knitData=array(); $finishData=array(); $washData=array(); $embData=array(); $trimData=array(); $commiData=array(); $otherData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	    //print_r($yarn_data_array);
		$sql_yarn="select count_id, copm_one_id, percent_one, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id, copm_one_id, percent_one, type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row)
		{
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		unset($data_arr_yarn);
		/*echo "<pre>";
		print_r($YarnData); die;*/
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		if($quotationId)
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			$sqlQc="select a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b, wo_po_details_master c where a.qc_no=b.cost_sheet_id and c.quotation_id=b.cost_sheet_id and a.inquery_id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQc=sql_select($sqlQc);
			//print_r($dataQc);
			foreach($dataQc as $qcrow)
			{
				//echo $qcrow[csf('offer_qty')].'=';
				$quaOfferQnty=$jobQty;//$qcrow[csf('offer_qty')];
				$quaConfirmPrice=$qcrow[csf('confirm_fob')];
				$quaConfirmPriceDzn=$qcrow[csf('confirm_fob')];
				$quaPriceWithCommnPcs=$qcrow[csf('confirm_fob')];
				$quaCostingPer=$qcrow[csf('costing_per')];
				$qc_no=$qcrow[csf('qc_no')];
				$quaCostingPerQty=0; 
				//if($quaCostingPer==1) 
				$quaCostingPerQty=1;
			}
			unset($dataQc);
		//echo $quaOfferQnty; die;
			
			 $sql_cons_rate="select id, mst_id, fab_id,item_id, type,description, particular_type_id, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where status_active=1 and is_deleted=0 and mst_id =".$qc_no." order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate);
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				if($rowConsRate[csf("type")]==1) //Fabric
				{
					if(($rowConsRate[csf("rate")]*1)>0)
					{
						$yarnrate=$edata[3]+$edata[7]+$edata[11];
						$consQnty=($rowConsRate[csf("tot_cons")]/12)*($quaOfferQnty);
						$amount=$consQnty*$yarnrate;
						$index="";
						$index=$edata[0].','.$edata[4].','.$edata[8];				
						$index=""; $mktcons=$mktamt=0;
						$index="'".$rowConsRate[csf('particular_type_id')]."'";						
						$mktcons=$mktamt=0;						
						$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
						$mktamt=$mktcons*$rowConsRate[csf('rate')];						
						$fabStri=$rowConsRate[csf('description')].'_'.$rowConsRate[csf('fab_id')];
						$fabPurArr[$fabStri]['mkt']['woven']['fabric_description']=$rowConsRate[csf('description')];
						$fabPurArr[$fabStri]['mkt']['woven']['qty']+=$mktcons;
						$fabPurArr[$fabStri]['mkt']['woven']['amount']+=$mktamt;
					}
					
				}
				//print_r($fabPurArr);
				if($rowConsRate[csf("type")]==3 ) //Wash
				{
					$index="'".$emblishment_wash_type_arr[$rowConsRate[csf("particular_type_id")]]."'";
					
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					if($rowConsRate[csf('rate')]>0)
					{
					$washData[$index]['mkt']['qty']+=$mktcons;
					$washData[$index]['mkt']['amount']+=$ConsRate*$mktcons;
					}
				}
				if($rowConsRate[csf("type")]==2)
				{
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					$embData['mkt']['qty']+=$mktcons;
					$embData['mkt']['amount']+=$ConsRate*$mktcons;
				}
				if($rowConsRate[csf("type")]==4) //Acceoosries
				{
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					//$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					$trimData[$rowConsRate[csf('particular_type_id')]]['mkt']['qty']+=$mktcons;
					$trimData[$rowConsRate[csf('particular_type_id')]]['mkt']['amount']+=$mktamt;
					$trimData[$rowConsRate[csf('particular_type_id')]]['cons_uom']=$rowConsRate[csf('cons_uom')];
				}
			}
			//echo "<pre>";
			//print_r($washData); 
			//die;
			
			 $sql_item_summ="select mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost,courier_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$sql_result_item_summ=sql_select($sql_item_summ);
			foreach($sql_result_item_summ as $rowItemSumm)
			{
				$consQnty=$amount=$mktamt=0;
				$mktamt=($rowItemSumm[csf("commission_cost")]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['amount']+=$mktamt;
				$freightAmt=($rowItemSumm[csf('frieght_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['amount']+=$freightAmt;
				$labTestAmt=($rowItemSumm[csf('lab_test_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['amount']+=$labTestAmt;
				$courier_costAmt=($rowItemSumm[csf('courier_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['amount']+=$courier_costAmt;
				$cmCostAmt=($rowItemSumm[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['amount']+=$cmCostAmt;
			}
		}
		
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
		$fin_fab_trans_array=array();
		$sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty, sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty, sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt, sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, c.from_order_id from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$wvn_fin_fab_trans_amt_acl=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			$tot_fin_fab_transfer_qty+=$row[csf('in_qty')]-$row[csf('out_qty')];
			$tot_trans_amt=$row[csf('in_amt')]-$row[csf('out_amt')];
			$wvn_fin_fab_trans_amt_acl+=$tot_trans_amt/$g_exchange_rate;
		}
		unset($result_fin_trans);
		$tot_wvn_fin_fab_transfer_cost=$wvn_fin_fab_trans_amt_acl;
		
		$trim_fab_trans_array=array();
		$sql_trim_trans="select c.from_order_id,a.from_prod_id, sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in, sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out, sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity*a.rate ELSE 0 END) AS grey_in_amt, sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity*a.rate ELSE 0 END) AS grey_out_amt from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_trim_trans=sql_select( $sql_trim_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_trim_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in_amt')]-$row[csf('grey_out_amt')])/$g_exchange_rate;
		}
		unset($result_trim_trans);
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
	
		$subconOutBillData="select b.order_id, sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill, sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
	
		$totPrecons=0; $totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

		$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id,uom, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2 and status_active=1 and is_deleted=0";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
		//	$fabStri=$fabPur_row[csf('id')];
			$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
			$fabDescArr[$fabPur_row[csf('id')]]['fabric_description']=$fabPur_row[csf('fabric_description')];
			//$fabPurArr[$fabStri]['pre']['knit']['qty']=$fabPur_row[csf('uom')];
			$fabDescArr[$fabPur_row[csf('id')]]['uom']=$fabPur_row[csf('uom')];
			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				
				$fabPurArr[$fabStri]['pre']['knit']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['woven']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['woven']['amount']+=$Preamt;
			}
		} 
		
		 $sql = "select a.item_category, a.exchange_rate, b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,b.uom,b.grey_fab_qnty, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				
				$fabric_description=$fabDescArr[$fabPur_row[csf('fab_dtls_id')]]['fabric_description'];
				$fabStri=$fabric_description.'_'.$fabPur_row[csf('fab_dtls_id')];
				if($fabPur_row[csf('item_category')]==2){
					$fabPurArr[$fabStri]['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
					}
				}
				if($fabPur_row[csf('item_category')]==3){
					$fabPurArr[$fabStri]['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
					}
				}
			}
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$trim= new trims($condition);
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	
		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		//print_r($trimData);
		
		$trimsRecArr=array();
		$general_item_issue_sql="select a.item_group_id as trim_group, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d where a.id=b.prod_id and b.order_id=c.id and c.job_id=d.id and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in(".implode(",",$poIdArr).") ";
		$gen_result=sql_select($general_item_issue_sql);
		foreach($gen_result as $row)
		{
			$con_factor=$trim_groupArr[$row[csf('trim_group')]]['con_factor'];
			$gen_item_arr[$row[csf('trim_group')]]['issue_qty']+=$row[csf('cons_quantity')];
			$gen_item_arr[$row[csf('trim_group')]]['cons_amount']+=($row[csf('cons_amount')]*$con_factor)/$g_exchange_rate;
		}
		foreach($gen_item_arr as $ind=>$value)
		{
		$trimData[$ind]['acl']['qty']+=$value['issue_qty'];
		$trimData[$ind]['acl']['amount']+=$value['cons_amount'];
		}
		unset($gen_result);
		
		 
		$sql_trim_wo = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.trim_group from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_trim_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=2  and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_wo=sql_select($sql_trim_wo);
		foreach($data_array_wo as $row){
			$index=$row[csf('trim_group')];
			$trimData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$trimData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		unset($data_array_wo);
	
		//print_r($trimData);
	
		// Trim Cost End============================
		// Embl Cost ============================
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		
		$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			if($row[csf('emb_name')]==3)
			{
				$washData[$index]['pre']['qty']+=$washQtyArr[$row[csf("job_no")]][$row[csf('id')]];
				$washData[$index]['pre']['amount']+=$washAmtArr[$row[csf("job_no")]][$row[csf('id')]];
			}
			else
			{
				$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
				$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
			}
		}
		$sql_emb = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_emb=sql_select($sql_emb);
		foreach($data_array_emb as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
		}
		// Embl Cost End ============================
		// Wash Cost ============================
		
		 
	$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$data_array=sql_select($sql);
	foreach($data_array as $row){
		$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
		$washData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
		$washData[$index]['acl']['amount']+=$row[csf('amount')];

	}
		
		// Wash Cost End ============================
		// Commision Cost  ============================
		
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}
		
		// Commision Cost  End ============================
	
		// Commarcial Cost  ============================
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			if($commaAmtArr[$jobNumber][$row[csf('id')]]>0 && $exfactory_qty>0)
			{
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;
			}
			else
			{
				$commaData['pre']['rate']+=0;
			}
			
		}
		
		// Commarcial Cost  End ============================
		// Other Cost  ============================
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id, lab_test,  inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost from  wo_pre_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;
	
			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;
	
			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;
	
			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;
	
			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
		}
		
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	
	 $sql_cm_cost="select jobNo, available_min, production_date from production_logicsoft where jobNo='".$jobNumber."' and is_self_order=1";
		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$production_date=change_date_format($row[csf('production_date')],'','',1);
			$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
			$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		}
	
		$sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==5){
				//$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
			}
		}
		// Other Cost End ============================
		ob_start();
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1300px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px" rules="all">
				<tr>
					<td width="100">Job Number</td>
					<td width="200"><? echo $jobNumber; ?></td>
					<td width="100">Buyer</td>
					<td width="200"><? echo $buyer_arr[$buyerName]; ?></td>
                    <td width="100">Internal Ref. No</td>
					<td width="100"><? $ref_nos=rtrim($ref_no,',');echo implode(",",array_unique(explode(",",$ref_nos))); ?></td>
					<td width="100">Style Ref. No</td>
					<td width="200"><? echo $styleRefno; ?></td>
					<td width="100">Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Order Number</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo number_format($jobValue,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td style="word-break:break-all;"><? 
					 echo implode(",",$ShipDateArrall);
					 ?></td>
					<td>Team Leader</td> 
                    <td style="word-break:break-all;"><? echo $team_leaders;?></td>
					<td>Dealing Marchant</td>
                    <td style="word-break:break-all;" colspan="3"><? echo $dealing_marchants;?></td>
					<td>Unit Price</td>
					<td align="right"><? echo number_format($unitPrice,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Garments Item</td>
					<td style="word-break:break-all;"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Sew SMV</td>
					<td align="left"><? echo $setSmv; ?></td>
					<td>Sew Ewff.%</td>
					<td align="left"><? echo $sew_effi_percent; ?></td>
					<td>Prod/Line/Hour</td>
					<td align="left"><? echo $prod_line_hr; ?></td>
					<td>SAH</td>
					<td align="right"><? echo number_format($jobQty/$prod_line_hr,4); ?></td>
				</tr>
				<tr>
					<td>Ship Qty</td>
					<td align="right"><? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
					<td>MC/Line</td>
					<td><? echo $machine_line; ?></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Shipment Value</td>
					<td align="right"><? echo number_format($exfactoryValue,4); ?></td>
					<td>Short/Excess Qty</td>
					<td align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right" colspan="3"><? echo number_format($shortExcessValue,4); ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>
	
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px; margin-top:10px" rules="all">
				<thead>
                	<tr>
                    <td> </td>
                    <td> </td>
                     <td colspan="3" align="center"> <b>Buyer Cost</b></td>
                     <td colspan="4" align="center"> <b>Booking Cost</b></td>
                     <td colspan="4" align="center"> <b>Post Cost</b></td>
                    </tr>
					<tr align="center" style="font-weight:bold">
						<td width="300">Item Description</td>
						<td width="60">UOM</td>
						<td width="100">Marketing Qty</td>
						<td width="100">Marketing Price</td>
						<td width="100">Marketing Value</td>
                        
						<td width="100">Pre-Cost Qty</td>
						<td width="100">Pre-Cost Price</td>
						<td width="100">Pre-Cost Value</td>
                        <td width="100">Cont. % On FOB Value</td>
                        
						<td width="100">Actual Qty</td>
						<td width="100">Actual Price</td>
						<td width="100">Actual Value</td>
                        <td width="100">Cont. % On FOB Value</td>
					</tr>
				</thead>
				
				<?
				$GrandTotalMktValue=0; $GrandTotalPreValue=0; $GrandTotalAclValue=0; $yarnTrimMktValue=0; $yarnTrimPreValue=0; $yarnTrimAclValue=0; $totalMktValue=0; $totalPreValue=0; $totalAclValue=0; $totalMktQty=0; $totalPreQty=0; $totalAclQty=0;
				 
				  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				 
				?>
				
				<?
				  $f=2;$mkt_amount=$pre_amount=$acl_amount=0;
				foreach($fabPurArr as $fab_index=>$row ){
					if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$fabric_description=explode("_",str_replace("'","",$fab_index));
					//$fabric_description=$fabPurArr[$fab_index]['mkt']['woven']['fabric_description'];
					$uom_name=$unit_of_measurement[$fabDescArr[$fabric_description[1]]['uom']];
				?>
				 <tr class="fbpur" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $bgcolor;?>')" id="trfab_<? echo $f; ?>">
					<td width="300" title="Fabric-> <? echo $fab_index;?>" > <? echo $fabric_description[0];?></td>
					<td width="60"><? echo $uom_name;?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['qty'],4) ?></td> 
					<td width="100" align="right"  title="Amount/Qty"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']/$fabPurArr[$fab_index]['mkt']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount'],4); ?></td>
                    
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right" title="Amount/Qty"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount']/$fabPurArr[$fab_index]['pre']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount'],4); ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right" title="SampleBookingQty=<?=$sample_booking_all_qnty?>">
						
							<a href="#" onClick="openmypage_sample_fab('<? echo implode("_",array_keys($poNumberArr)); ?>','<?=$jobNumber?>')">
								<? echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['qty']+$sample_booking_all_qnty,4) ?>
							</a>
						</td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['acl']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount']/$fabPurArr[$fab_index]['acl']['woven']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['acl']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
                <?
				$f++;
				$mkt_amount+=$fabPurArr[$fab_index]['mkt']['woven']['amount'];
				$pre_amount+=$fabPurArr[$fab_index]['pre']['woven']['amount'];
				$acl_amount+=$fabPurArr[$fab_index]['acl']['woven']['amount'];
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						$fabPurMkt= number_format($mkt_amount,4,".","");
						echo number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($pre_amount,4,".","");
						$fabPurAcl= number_format($acl_amount,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor="<? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
                      <td width="100" align="right"><? echo number_format(($fabPurPre*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<?
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($fabPurAcl*100/$jobValue),2).'%'; ?></td>
				</tr>
				
	
				 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
			  <?
	
				  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;
				?>
				 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="300"> Cost<? //echo $item_descrition ?></td>
						<td width="60"></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount'],4); ?></td>
                        
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        
						
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? if($grey_fab_trans_amt_acl>0) echo number_format($grey_fab_trans_amt_acl/$grey_fab_trans_qty_acl,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($grey_fab_trans_amt_acl,4); ?></a>&nbsp;<? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
                       <td width="100" align="right"><? echo number_format(($grey_fab_trans_amt_acl*100/$jobValue),2).'%'; ?></td>
					</tr>
				<?
				 $tt2=1;
				?>
				<tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="300">Finished Fabric Cost</td>
						<td width="60">Yds</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost>0) echo number_format($tot_wvn_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_wvn_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
                         <td width="100" align="right"><? echo number_format(($tot_wvn_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
					</tr>
					<?
	
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    <td width="100" align="right"><? //echo number_format(($tot_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl>0) echo number_format(($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);else echo "";?></td>
					<td width="100" align="right">
						<?
						echo number_format($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl,4);
						$total_grey_fin_fab_transfer_actl_cost=$tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format((($tot_wvn_fin_fab_transfer_cost+$tot_grey_fab_cost_acl)*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr>
					<td colspan="12" style="font-weight:bold" class="trims">Trims Cost</td>
				</tr>
				<?
				$t=1; $totalTrimMktValue=0; $totalTrimPreValue=0; $totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					if($index>0)
					{
						$item_descrition = $trim_groupArr[$index]['item_name'];
						$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
						$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
						$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
						if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr class="trims" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
							<td width="300"><? echo $item_descrition ?></td>
							<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
                            
							<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
							<td width="100" align="right"  bgcolor=" <? if(number_format($row['pre']['amount'],4,'.','')>number_format($row['mkt']['amount'],4,'.','')){echo "yellow";} else{echo "";}?>"><? echo number_format($row['pre']['amount'],4); ?></td>
                             <td width="100" align="right"><? echo number_format(($row['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                             
							<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
							<td width="100" align="right"><? if($row['acl']['amount']>0) echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
							<td width="100" align="right" title="<? echo 'Act='.$row['acl']['amount'].', Pre='.$row['pre']['amount'];?>" bgcolor=" <? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}  else{echo " ";}?>"><? echo number_format($row['acl']['amount'],4); ?></td>
                            <td width="100" align="right"><? echo number_format(($row['acl']['amount']*100/$jobValue),2).'%'; ?></td>
						</tr>
						<?
						$t++;
					}
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300"><span id="trimstotal" class="adl-signs" onClick="yarnT(this.id,'.trims')">+</span>&nbsp;&nbspTrims Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						echo number_format($totalTrimMktValue,4);
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($totalTrimPreValue*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if(number_format($totalTrimPreValue,4,".","")>number_format($totalTrimMktValue,4,".","")){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalTrimAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<?
				$totalOtherMktValue=0; $totalOtherPreValue=0; $totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 
				  <?
				$w=1;$total_mkt_qty=$total_mkt_amt=$total_pre_qty=$total_pre_amt=$total_acl_qty=$total_acl_amt=0;
                foreach($washData as $index=>$row ){
					$index=str_replace("'","",$index);
					$item_des=explode("_",$index);
					$item_des=implode(",",$item_des);
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr  class="wash" bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="300" ><? echo  $item_des;	?></td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); $GrandTotalMktValue+=number_format($row['mkt']['amount'],4,".","");?></td>
                    
					<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['pre']['amount'],4,".","")>number_format($row['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($row['pre']['amount'],4);$GrandTotalPreValue+=number_format($row['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format(($row['pre']['amount']/$jobValue)*100,4); ?></td>
                     <td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['acl']['amount']>0) echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($row['acl']['amount'],4);$GrandTotalAclValue+=number_format($row['acl']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($row['acl']['amount']/$jobValue)*100,4); ?></td>
				</tr>
                <?
				$w++;
				$total_mkt_qty+=$row['mkt']['qty'];
				$total_mkt_amt+=$row['mkt']['amount'];
				$total_pre_qty+=$row['pre']['qty'];
				$total_pre_amt+=$row['pre']['amount'];
				$total_acl_qty+=$row['acl']['qty'];
				$total_acl_amt+=$row['acl']['amount'];
				}
				?>
                 <tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="washtotal"  class="adl-signs" onClick="yarnT(this.id,'.wash')">+</span>&nbsp;&nbsp;Wash Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo number_format($total_mkt_qty,4); ?></td>
					<td width="100" align="right"><? if($total_mkt_amt>0) echo number_format($total_mkt_amt/$total_mkt_qty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($total_mkt_amt,4); //$totalOtherMktValue+=number_format($total_mkt_amt,4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($total_pre_qty,4); ?></td>
					<td width="100" align="right"><? if($total_pre_amt>0) echo number_format($total_pre_amt/$total_pre_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_pre_amt,4,".","")>number_format($total_pre_amt,4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($total_pre_amt,4);//$totalOtherPreValue+=number_format($total_pre_amt,4,".",""); ?></td>
                      <td width="100" align="right"><? echo number_format(($total_pre_amt/$jobValue)*100,4); ?></td>
                      
					<td width="100" align="right"><? echo number_format($total_acl_qty,4); ?></td>
                   
					<td width="100" align="right"><? if($total_acl_amt>0) echo number_format($total_acl_amt/$total_acl_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_acl_amt,4,".","")>number_format($total_acl_amt,4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($total_acl_amt,4);//$totalOtherAclValue+=number_format($total_acl_amt,4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($total_acl_amt/$jobValue)*100,4); ?></td>
				</tr>
                <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="300" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['mkt']['amount']>0) echo number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=number_format($embData['mkt']['amount'],4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($embData['pre']['amount']/$embData['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100" align="right"><? echo number_format($embData['acl']['qty'],4); ?></td>

					<td width="100" align="right"><? if($embData['acl']['amount']>0) echo number_format($embData['acl']['amount']/$embData['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['acl']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
                
                
                
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="300" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"  title="Amount/Qty"><? echo number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=number_format($commiData['pre']['amount'],4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commiData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? //echo $exfactoryQty ?></td>
					<td width="100" align="right"><? //$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty; echo number_format($aclCommiAmt/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? //echo number_format($aclCommiAmt,4); $totalOtherAclValue+=$aclCommiAmt; ?></td>
                    <td width="100" align="right"><? //echo number_format(($aclCommiAmt*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="300" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? //echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? //echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? //echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? if($commaData['pre']['rate']) echo number_format($commaData['pre']['rate'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($commaData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; //$commer_act_cost=($unitPrice*$exfactoryQty)*(0.5/100); ?></td>
					<td width="100" align="right"><? if($commer_act_cost>0  && $exfactoryQty>0) echo  number_format($commer_act_cost/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" align="right" title="" bgcolor="<? //if(number_format($exfactoryQty*$unitPrice)>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					$commer_act_cost=$commaData['acl']['amount'];
					echo number_format($commer_act_cost,4); $totalOtherAclValue+=number_format($commer_act_cost,4,".",""); ?></td>
                    <td width="100" align="right"><?  echo number_format(($commer_act_cost*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="300" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['freight']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
                     <td width="100" align="right"><?  echo number_format(($otherData['acl']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                     
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="300" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['ore']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? 
					//$test_act_cost=$unitPrice*$exfactoryQty*(0.05/100);
					if($test_act_cost>0  && $exfactoryQty>0) echo number_format($test_act_cost/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" title="" bgcolor="<? //if(number_format($test_act_cost,4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					//$otherData['acl']['lab_test']['amount'];
					$test_act_cost=$otherData['acl']['lab_test']['amount'];
					echo number_format($test_act_cost,4); $totalOtherAclValue+=number_format($test_act_cost,4,".","");?></td>	
                     <td width="100" align="right"><? echo number_format(($test_act_cost*100/$jobValue),2).'%'; ?></td>				
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="300" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? //echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? //echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? //echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['inspection']['amount']>0  && $exfactoryQty>0) echo number_format($otherData['acl']['inspection']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
				</tr>
				<tr bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="300" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['currier_pre_cost']['amount']>0  && $exfactoryQty>0) echo number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="300" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['cm_cost']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<?
					if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")) $font_color="white";
					else $font_color="black";
					?>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['cm_cost']['amount']>0  && $exfactoryQty>0) echo number_format($otherData['acl']['cm_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<!--<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  
						$making_cost_act=$otherData['pre']['cm_cost']['rate']*$exfactoryQty*(80/100);
					//$totalOtherAclValue+=number_format($otherData['acl']['cm_cost']['amount'],4,".",""); //echo number_format($otherData['acl']['cm_cost']['amount'],4);?></td>-->
                    
					<!-- <a href="#report_details" style="color:<? //echo $font_color;?>" onClick="openmypage_mkt_cm_popup('<? //echo $jobNumber; ?>','mkt_cm_cost_popup','Making CM Cost Details','2','1000px')"></a> -->

					<td width="100" title="(Pre Cost Rate*Ex Fact Qty*80)/100)"  align="right"  bgcolor="<? if(number_format($making_cost_act,4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=number_format($making_cost_act,4,".",""); echo number_format($making_cost_act,4); ?>
                    </td>
                     <td width="100" align="right"><? echo number_format(($making_cost_act*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" >Others Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($totalOtherMktValue,4); $GrandTotalMktValue+=number_format($totalOtherMktValue,4,".",""); ?></td>
					
                    <td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? echo number_format($totalOtherPreValue,4); $GrandTotalPreValue+=number_format($totalOtherPreValue,4,".",""); ?>
					</td>
                   <td width="100" align="right"><? echo number_format(($totalOtherPreValue*100/$jobValue),2).'%'; ?></td>
                   
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? echo number_format($totalOtherAclValue,4); $GrandTotalAclValue+=number_format($totalOtherAclValue,4,".",""); ?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalOtherAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="300" >Grand Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
                     <td width="100" align="right"><? echo number_format(($GrandTotalPreValue*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
                    <td width="100" align="right"><? echo number_format(($GrandTotalAclValue*100/$jobValue),2).'%'; ?></td>
                    
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="300" >Shipment Value</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right"><? echo $quaOfferQnty;  ?></td>
					<td width="100" align="right" title="<? echo $quaPriceWithCommnPcs;?>"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right"><? $quaOfferValue=$quaOfferQnty*$quaPriceWithCommnPcs; echo number_format($quaOfferValue,4) ?></td>
					
                    <td width="100" align="right"><? echo $jobQty ?></td>
					<td width="100" align="right" title="<? echo $unitPrice;?>"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
                    <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right" title="<? echo $exfactoryValue/$exfactoryQty;?>"><? if( $exfactoryValue>0 && $exfactoryQty>0) echo number_format($exfactoryValue/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4) ?></td>
                      <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
				</tr>
			</table>
			<table style="width:120px;float: left; margin-left: 5px; margin-top: 10px;">
				<tr>
					
                    <?
                    $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in('$jobNumber') and form_name='knit_order_entry' and file_type=1");
					if(count( $nameArray_img)>0)
					{
					    ?>
						    
                            
							<? 
							foreach($nameArray_img AS $inf) { 
								?>
                                 <td width="120">
                                <div style="float:left;width:120px" >
								<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='75' width='95' />
                                 </td>
                               </div>
							    <?  
							}
							?>
							
						<?								
					}
					else echo ""; ?>						    
                   
				</tr>
            </table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="10"><p style="font-weight: bold;">Profit Summary</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"  title="Total Mkt Value/Offer Value*100"><? if($GrandTotalMktValue>0 && $quaOfferValue>0) echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right" title="Total Expand/Offer Value*100"><? if($yarnTrimMktValue>0) echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
                    <td width="150" align="right"><? echo number_format($total_mkt_amt,4) ?></td>
					<td width="100" align="right"  title="Total Mkt Wash/Offer Value*100"><? if($total_mkt_amt>0) echo number_format(($total_mkt_amt/$quaOfferValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Offer Value-Grand Total Mkt Value"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right" title="Offer Value-Grand Total Mkt Value/Offer Value*100"><? if($quaOfferValue-$GrandTotalMktValue>0) echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Total Pre Value/Job Value*100"><? if($GrandTotalPreValue>0) echo number_format(($GrandTotalPreValue/$jobValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? if($yarnTrimPreValue>0) echo number_format(($yarnTrimPreValue/$jobValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_pre_amt,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><? if($total_pre_amt>0) echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Job Value-Grand Total Pre Value"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Job Value-Grand Total Pre Value/Ex-Fact Value*100"><? if($jobValue-$GrandTotalPreValue>0) echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);else echo "";?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Total Actual Value/Ex-Fact Value*100"><? if($GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Ex-fact Value*10"><? if($yarnTrimAclValue>0) echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_acl_amt,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Ex-Fact Value*100"><? if($total_acl_amt>0) echo number_format(($total_acl_amt/$exfactoryValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value-Grand Total Acl Value"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Ex-Fact Value-Grand Total Ex Value/Ex-Fact Value*100"><? if($exfactoryValue-$GrandTotalAclValue>0) echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);else echo "";?></td>
				</tr>
			</table>
            <br>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="11"><p style="font-weight: bold;">Profit Summary 1 Pcs</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">FOB/Pcs</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    <td width="100">CM Cost</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right" title="Mkt Cost/Job Qty"><? $tot_offer_val_pcs=$unitPrice;//$quaOfferValue/$jobQty;
					echo number_format($tot_offer_val_pcs,4) ?></td>
					<td width="150"  title="Grand Total Mkt Value/Job Qty" align="right"><? $tot_mkt_val_pcs=$GrandTotalMktValue/$jobQty;echo number_format($tot_mkt_val_pcs,4); ?></td>
					<td width="100" align="right"  title="Total Expenditure/FOB Pcs*100"><? if($tot_mkt_val_pcs>0) echo number_format(($tot_mkt_val_pcs/$tot_offer_val_pcs)*100,4); else echo "";?></td>
					<td width="150" align="right" title="Total Trims Mkt value(<? echo $yarnTrimMktValue?>)/Job Qty(<? echo $jobQty?>)"><? $tot_trim_val_pcs=$yarnTrimMktValue/$jobQty;echo number_format($tot_trim_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Trims(<? echo $tot_trim_val_pcs?>)/FOB Pcs(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_trim_val_pcs>0) echo number_format(($tot_trim_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                    <td width="150" align="right" title="Total Mkt Wash(<? echo $total_mkt_amt?>)/Job Qty(<? echo $jobQty?>)"><? $tot_wash_val_pcs=$total_mkt_amt/$jobQty; echo number_format($tot_wash_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash/Offer Value*100"><? if($tot_wash_val_pcs>0) echo number_format(($tot_wash_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Cost(<? echo $otherData['mkt']['cm_cost']['amount'];?>)/Job Qty"><? $tot_mkt_cm_pcs=$otherData['mkt']['cm_cost']['amount']/$jobQty; echo number_format($tot_mkt_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Qua.Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $profit_mkt_margin_pcs=($quaOfferValue-$GrandTotalMktValue)/$jobQty;echo number_format($profit_mkt_margin_pcs,4); ?></td>
					<td width="100" align="right" title="Profit margin(<? echo $profit_mkt_margin_pcs;?>)/Mkt FOB/Pcs (<? echo $tot_offer_val_pcs;?>)*100)"><? if($profit_mkt_margin_pcs>0) echo number_format(($profit_mkt_margin_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right" title="Pre Cost/Job Qty"><? $tot_preJob_value=$unitPrice;//$jobValue/$jobQty;
					echo number_format($tot_preJob_value,4) ?></td>
					<td width="150" align="right" title="Pre Cost/JobQty"><? $tot_pre_costing_expnd=$GrandTotalPreValue/$jobQty; echo number_format($tot_pre_costing_expnd,4);?></td>
					<td width="100" align="right"  title="Total Expenditure/Job Value*100"><? 
					if($tot_pre_costing_expnd>0)
					{
						$tot_pre_costing_expnd_per=$tot_pre_costing_expnd/$tot_preJob_value*100;
					}
					else {$tot_pre_costing_expnd_per=0;}
					echo number_format($tot_pre_costing_expnd_per,4); ?></td>
					<td width="150" align="right"  title="Total Trims Value/Job Qty"><? $tot_preTrim_costing=$yarnTrimPreValue/$jobQty;echo number_format($tot_preTrim_costing,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? 
					if($tot_preTrim_costing>0)
					{
						$tot_preTrim_costing_per=($tot_preTrim_costing/$tot_preJob_value)*100;
					}
					else {$tot_preTrim_costing_per=0;}
					echo number_format($tot_preTrim_costing_per,4); ?></td>
                     <td width="150" align="right" title="Pre Wash Cost/Job Qty"><?  $tot_preWash_value=$total_pre_amt/$jobQty;echo number_format($tot_preWash_value,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><?  $tot_preWash_value_pre=$tot_preWash_value/$tot_preJob_value*100;echo number_format($tot_preWash_value_pre,4); ?></td>
                     <td width="150" align="right"  title="Cm Cost(<? echo $otherData['pre']['cm_cost']['amount'];?>)/Job Qty"><? $tot_pre_cm_pcs=$otherData['pre']['cm_cost']['amount']/$jobQty; echo number_format($tot_pre_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Job Qty"><? $profit_pre_margin_pcs=($jobValue-$GrandTotalPreValue)/$jobQty;echo number_format($profit_pre_margin_pcs,4);?></td>
					<td width="100" align="right"  title="Profit Margin(<? echo $profit_pre_margin_pcs;?>)/Pre FOB Pcs (<? echo $tot_preJob_value;?>)*100)"><? echo number_format(($profit_pre_margin_pcs/$tot_preJob_value)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right" title="Acl Job Value/Job Qty"><? $tot_AclJob_valuePcs=$unitPrice;//$exfactoryValue/$jobQty;
					echo number_format($tot_AclJob_valuePcs,4) ?></td>
					<td width="150" align="right" align="right" title="Grand total Acl value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><? $tot_AclJob_ExpndPcs=$GrandTotalAclValue/$jobQty; echo number_format($tot_AclJob_ExpndPcs,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? if($tot_AclJob_ExpndPcs>0 && $tot_AclJob_valuePcs>0) echo number_format(($tot_AclJob_ExpndPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right"  title="Total Acl Trims Value/Job Qty"><? $tot_AcltrimValue=$yarnTrimAclValue/$jobQty;echo number_format($tot_AcltrimValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Job Value*100"><? if($tot_AcltrimValue>0) echo number_format(($tot_AcltrimValue/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? $tot_AclJobWashPcs=$total_acl_amt/$jobQty; echo number_format($tot_AclJobWashPcs,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Job Value*100"><? if($tot_AclJobWashPcs>0) echo number_format(($tot_AclJobWashPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="Making Cost(<? echo $making_cost_act;?>)/Job Qty"><?  $tot_cmAcl_pcs=$making_cost_act/$jobQty;; echo number_format($tot_cmAcl_pcs,4) ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value(<? echo $exfactoryValue;?>)-Grand Total Acl Value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><?  $profit_acl_margin_pcs=($exfactoryValue-$GrandTotalAclValue)/$jobQty; echo number_format($profit_acl_margin_pcs,4);?></td>

					<td width="100" align="right" title="Profit margin(<? echo $profit_acl_margin_pcs;?>)/Actual FOB/Pcs (<? echo $tot_AclJob_valuePcs;?>)*100)"><? if($profit_acl_margin_pcs>0) echo number_format(($profit_acl_margin_pcs/$tot_AclJob_valuePcs)*100,4);else echo "";?></td>
				</tr>
			</table>
             <br>
              <br>
		</div>
		<?
	}
	if(str_replace("'","",$cbo_costcontrol_source)==2) //Price Quotation
	{
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){ echo "Select Company"; die; }
		if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		$check_data=sql_select("select a.job_no from wo_po_details_master a,wo_pre_cost_mst f where  a.job_no=f.job_no  and a.company_name=$company_name and a.job_no_prefix_num like '$job_no' and a.garments_nature=3 $year_cond group by a.job_no");
		 
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		//$chk_job_nos=$job_no;
		if($chk_job_nos!='') 
		{
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.inquiry_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
			$result=sql_select($sql);
		}
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
	$shipArry=array(0,1,2);
	$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotationId=$row[csf('quotation_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$ShipDateArrall[date("d-m-Y",strtotime($row[csf('shipment_date')]))]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			/* if($row[csf('shiping_status')]==3) //Full
			{
			 $shiping_status=2; 
			}
			//echo $shiping_status.'D';
			$shipingStatus=$shiping_status; */
			$shipingStatus=$row[csf('shiping_status')];
			$shiping_status_id.=$row[csf('shiping_status')].',';
			$po_ids.=$row[csf('id')].',';$ref_no.=$row[csf('grouping')].',';
			$team_leaders.=$team_leader_library[$row[csf('team_leader')]].',';
			$dealing_marchants.=$dealing_merchant_array[$row[csf('dealing_marchant')]].',';
		}
	//	echo $quotationId.'D';

		$sql_sample="SELECT sum(d.required_qty) as qnty
			    FROM wo_po_details_master a, sample_development_mst c,sample_development_fabric_acc d
			    WHERE     a.status_active = 1
			         AND a.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         and d.sample_mst_id=c.id
			         AND c.quotation_id = a.id
			         AND d.form_type = 1
			         AND d.is_deleted = 0
			         AND d.status_active = 1
			         AND a.job_no='$jobNumber'

	       ";
	  //  echo "<pre>$sql_sample</pre>";
	    $res_sample=sql_select($sql_sample);
	    $sql_sample_fab="SELECT SUM (fin_fab_qnty) AS qnty
						  FROM wo_booking_dtls
						 WHERE     status_active = 1
						       AND is_deleted = 0
						       AND job_no='$jobNumber'
						       AND entry_form_id = 440";
		//echo "<pre>$sql_sample_fab</pre>";
	    $res_sample_fab=sql_select($sql_sample_fab);

	    $booking_sql3=sql_select("SELECT 
			         SUM (a.fin_fab_qnty) AS qnty
			        
			    FROM wo_booking_dtls a, wo_booking_mst b
			   WHERE     a.booking_mst_id = b.id
			         AND a.is_short = 2
			         AND b.booking_type = 4
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND a.job_no = '$jobNumber'
			
				 ");
	    $sample_booking_all_qnty=$res_sample[0][csf('qnty')]+$res_sample_fab[0][csf('qnty')]+$booking_sql3[0][csf('qnty')];
		 //print_r($ShipDateArrall);
		$shiping_status_id=rtrim($shiping_status_id,',');
		
		$shiping_status_idAll=array_unique(explode(",",$shiping_status_id));
		//print_r($shiping_status_idAll);
		$shiping_status=3;
		foreach($shiping_status_idAll as $sid)
		{
			//echo $sid."D,";
			if(in_array($sid,$shipArry))
			{
				$shiping_status=2;
			}
		}
		//echo $shiping_status;
		$team_leaders=rtrim($team_leaders,',');
		$team_leaders=implode(",",array_unique(explode(",",$team_leaders)));
		$dealing_marchants=rtrim($dealing_marchants,',');
		$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchants)));
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0) $po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; else $po_cond_for_in="";
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessValue=$shortExcessQty*($unitPrice);
	    //$quotationId=1;
		
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	    // Yarn ============================
		$totYarn=0;
		$fabPurArr=array(); $YarnData=array(); $qYarnData=array(); $knitData=array(); $finishData=array(); $washData=array(); $embData=array(); $trimData=array(); $commiData=array(); $otherData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	    //print_r($yarn_data_array);
		$sql_yarn="select count_id, copm_one_id, percent_one, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id, copm_one_id, percent_one, type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row)
		{
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		unset($data_arr_yarn);
		/*echo "<pre>";
		print_r($YarnData); die;*/
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		if($quotationId)
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;

			$sqlQua="select a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQuot=sql_select($sqlQua);
			//print_r($dataQc);
			foreach($dataQuot as $rowQua)
			{
				$quaOfferQnty=$jobQty;
				$quaConfirmPrice=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer=$rowQua[csf('costing_per')];
				$quaCostingPerQty=0;
				if($quaCostingPer==1) $quaCostingPerQty=12;
				if($quaCostingPer==2) $quaCostingPerQty=1;
				if($quaCostingPer==3) $quaCostingPerQty=24;
				if($quaCostingPer==4) $quaCostingPerQty=36;
				if($quaCostingPer==5) $quaCostingPerQty=48;
				$quaCostingPerQty=1;
				
			}
			unset($dataQc);
		 //echo $quaOfferQnty; die;
						
			if($quotationId){
			$totMktcons=0; $totMktAmt=0;
			$sql = "select id, item_number_id, body_part_id, fabric_description,fab_nature_id, color_type_id, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='".$quotationId."' and fabric_source=2";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
			//$mktcons=($fabPur_row[csf('avg_cons')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($fabPur_row[csf('avg_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				//echo $fabPur_row[csf('avg_cons')].'='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
				$mktamt=$mktcons*$fabPur_row[csf('rate')];
				$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
				if($fabPur_row[csf('fab_nature_id')]==2){
					$fabPurArr[$fabStri]['mkt']['knit']['qty']+=$mktcons;
					$fabPurArr[$fabStri]['mkt']['knit']['amount']+=$mktamt;
				}
				if($fabPur_row[csf('fab_nature_id')]==3){
					$fabPurArr[$fabStri]['mkt']['woven']['qty']+=$mktcons;
					$fabPurArr[$fabStri]['mkt']['woven']['amount']+=$mktamt;
				}
			}
			
			 $sql_trim = "select id,  trim_group, cons_uom, total_cons, rate, amount, apvl_req, nominated_supp,status_active from wo_pri_quo_trim_cost_dtls  where quotation_id='".$quotationId."' and status_active=1";
			$trim_data_array=sql_select($sql_trim);
			foreach($trim_data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktcons=($row[csf('total_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
			// echo $row[csf('total_cons')].'Pri='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
				$trimData[$row[csf('trim_group')]]['mkt']['qty']+=$mktcons;
				$trimData[$row[csf('trim_group')]]['mkt']['amount']+=$mktamt;
				$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
			}
			unset($trim_data_array);
			//========Emblishmnet=====
			$sql_embl = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name !=3 and status_active=1";
			$embl_data_array=sql_select($sql_embl);
			foreach($embl_data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$embData['mkt']['qty']+=$mktcons;
				$embData['mkt']['amount']+=$mktamt;
			}
			unset($embl_data_array);
			 $sql_wash = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name =3 and status_active=1";
			$wash_data_array=sql_select($sql_wash);
			foreach($wash_data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
					
					//echo $mktcons.'='.$row[csf('cons_dzn_gmts')].'='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
					
				$washData[$index]['mkt']['qty']+=$mktcons;
				$washData[$index]['mkt']['amount']+=$mktamt;
			}
			unset($wash_data_array);
			$commi_sql = "select id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pri_quo_commiss_cost_dtls  where quotation_id='".$quotationId."' and status_active=1";
			$commi_data_array=sql_select($commi_sql);
			foreach($commi_data_array as $row){
		//$mktamt=($row[csf('commission_amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('commission_amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['qty']=$quaOfferQnty;
				$commiData['mkt']['amount']+=$mktamt;
				$commiData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
			unset($commi_data_array);
			 $commer_sql = "select id, item_id, rate, amount, status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id=".$quotationId." and status_active=1";
			$commer_data_array=sql_select($commer_sql);
			foreach($commer_data_array as $row){
		//$mktamt=($row[csf('amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktamt;
				if($mktam>0 && $quaOfferQnty>0)
				{
				$commaData['mkt']['rate']+=$mktamt/$quaOfferQnty;
				}
				else
				{
					$commaData['mkt']['rate']+=0;
				}
			}
			unset($commer_data_array);
			
			$other_sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh,design_pre_cost as design_cost ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id='".$quotationId."'";
			$other_data_array=sql_select($other_sql);
			foreach($other_data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['qty']=$quaOfferQnty;
				$otherData['mkt']['freight']['amount']=$freightAmt;
				$otherData['mkt']['freight']['rate']=$freightAmt/$quaOfferQnty;
	
				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['qty']=$quaOfferQnty;
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
				$otherData['mkt']['lab_test']['rate']=$labTestAmt/$quaOfferQnty;
	
				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['qty']=$quaOfferQnty;
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
				$otherData['mkt']['inspection']['rate']=$inspectionAmt/$quaOfferQnty;
				
				$design_costAmt=($row[csf('design_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['design_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['design_cost']['amount']=$design_costAmt;
				$otherData['mkt']['design_cost']['rate']=$design_costAmt/$quaOfferQnty;
	
				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['qty']=$quaOfferQnty;
	
				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
				$otherData['mkt']['currier_pre_cost']['rate']=$currierPreCostAmt/$quaOfferQnty;
	
				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
				$otherData['mkt']['cm_cost']['rate']=$cmCostAmt/$quaOfferQnty;
			}
		
			
		} //Qout Id End
			
			
		}
		
	 
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
	
		//echo $lot_tmp;
		//echo $knit_grey_amt; // and a.yarn_lot in(".$lot_tmp.")
		$fin_fab_trans_array=array();
		  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
		 	 sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, 
			 sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt,
			sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, 
			c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$wvn_fin_fab_trans_amt_acl=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			//$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['in']=$row[csf('in_qty')];
			//$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['out']=$row[csf('out_qty')];
			$tot_fin_fab_transfer_qty+=$row[csf('in_qty')]-$row[csf('out_qty')];
			$tot_trans_amt=$row[csf('in_amt')]-$row[csf('out_amt')];
			$wvn_fin_fab_trans_amt_acl+=$tot_trans_amt/$g_exchange_rate;
		}
		unset($result_fin_trans);
		$tot_wvn_fin_fab_transfer_cost=$wvn_fin_fab_trans_amt_acl;
		
		$trim_fab_trans_array=array();
		 $sql_trim_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity*a.rate ELSE 0 END) AS grey_in_amt,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity*a.rate ELSE 0 END) AS grey_out_amt
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_trim_trans=sql_select( $sql_trim_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_trim_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in_amt')]-$row[csf('grey_out_amt')])/$g_exchange_rate;
		}
		//echo $grey_fab_trans_amt_acl.'d';
		unset($result_trim_trans);
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
	
		$subconOutBillData="select b.order_id,
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
	
		 

	    	
	
		// Yarn End============================
		// Fabric Purch ============================
		$totPrecons=0; $totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		
		//print_r($fabPur);
		 $sql = "select id, job_no,item_number_id,uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2 and status_active=1 and is_deleted=0";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
			$fabDescArr[$fabPur_row[csf('id')]]['fabric_description']=$fabPur_row[csf('fabric_description')];
			$fabDescArr[$fabPur_row[csf('id')]]['uom']=$fabPur_row[csf('uom')];
			
			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['knit']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['woven']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['woven']['amount']+=$Preamt;
			}
		} 
		
		$sql = "select a.item_category, a.exchange_rate, b.grey_fab_qnty,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				$fabric_description=$fabDescArr[$fabPur_row[csf('fab_dtls_id')]]['fabric_description'];
				$fabStri=$fabric_description.'_'.$fabPur_row[csf('fab_dtls_id')];
				
				if($fabPur_row[csf('item_category')]==2){
					$fabPurArr[$fabStri]['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
					}
				}
				if($fabPur_row[csf('item_category')]==3){
					$fabPurArr[$fabStri]['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					//alc_woven_qnty
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
					}
				}
			}
		
		
	
		// Fabric AOP  End============================
		// Trim Cost ============================
		//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$trim= new trims($condition);
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	
		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			//echo $trimAmtArr[$row[csf('job_no')]][$row[csf('id')]].'d';
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		//print_r($trimData);
		
		$trimsRecArr=array();
		
		 
		  $sql_trim_wo = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.trim_group from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_trim_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=2  and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_wo=sql_select($sql_trim_wo);
		foreach($data_array_wo as $row){
			$index=$row[csf('trim_group')];
			$trimData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$trimData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		unset($data_array_wo);
		$general_item_issue_sql="select a.item_group_id as trim_group, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_id=d.id and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in(".implode(",",$poIdArr).") ";
			 $gen_result=sql_select($general_item_issue_sql);
			foreach($gen_result as $row)
            {
				$con_factor=$trim_groupArr[$row[csf('trim_group')]]['con_factor'];
				$gen_item_arr[$row[csf('trim_group')]]['issue_qty']+=$row[csf('cons_quantity')];
				$gen_item_arr[$row[csf('trim_group')]]['cons_amount']+=($row[csf('cons_amount')]*$con_factor)/$g_exchange_rate;
            }
			//print_r($gen_item_arr);
			foreach($gen_item_arr as $ind=>$value)
			{
			$trimData[$ind]['acl']['qty']+=$value['issue_qty'];
			$trimData[$ind]['acl']['amount']+=$value['cons_amount'];
		    }
		//print_r($trimsRecArr);
		unset($gen_result);
		
	
		//print_r($trimData);
	
		// Trim Cost End============================
		// Embl Cost ============================
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		
		 $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			if($row[csf('emb_name')]==3)
			{
				$washData[$index]['pre']['qty']+=$washQtyArr[$row[csf("job_no")]][$row[csf('id')]];
				$washData[$index]['pre']['amount']+=$washAmtArr[$row[csf("job_no")]][$row[csf('id')]];
			}
			else
			{
				$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
				$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
			}
		}
		 $sql_emb = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_emb=sql_select($sql_emb);
		foreach($data_array_emb as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
		}
		// Embl Cost End ============================
		// Wash Cost ============================
		
		 
	 $sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			$washData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$washData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		
		// Wash Cost End ============================
		// Commision Cost  ============================
		
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}
		
		// Commision Cost  End ============================
	
		// Commarcial Cost  ============================
		//$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			if($commaAmtArr[$jobNumber][$row[csf('id')]]>0 && $exfactory_qty>0)
			{
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;
			}
			else
			{
				$commaData['pre']['rate']+=0;
			}
		}
		
		// Commarcial Cost  End ============================
		// Other Cost  ============================
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id, lab_test,  inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost from  wo_pre_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;
	
			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;
	
			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;
	
			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;
	
			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			if($other_cost[$jobNumber]['cm_cost']>0)
			{
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
			}
			else
			{
				$otherData['pre']['cm_cost']['rate']=0;
			}
		}
		
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	
	 $sql_cm_cost="select jobNo, available_min, production_date from production_logicsoft where jobNo='".$jobNumber."' and is_self_order=1";
		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$production_date=change_date_format($row[csf('production_date')],'','',1);
			$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
			$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		}
	
		 $sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==5){
				//$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
			}
		}
		// Other Cost End ============================
			 
		ob_start();
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1300px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px" rules="all">
				<tr>
					<td width="100">Job Number</td>
					<td width="200"><? echo $jobNumber; ?></td>
					<td width="100">Buyer</td>
					<td width="200"><? echo $buyer_arr[$buyerName]; ?></td>
                    <td width="100">Internal Ref. No</td>
					<td width="100"><? $ref_nos=rtrim($ref_no,',');echo implode(",",array_unique(explode(",",$ref_nos))); ?></td>
					<td width="100">Style Ref. No</td>
					<td width="200"><? echo $styleRefno; ?></td>
					<td width="100">Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Order Number</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo number_format($jobValue,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td style="word-break:break-all;"><? 
					 echo implode(",",$ShipDateArrall);
					 ?></td>
					<td>Team Leader</td> 
                    <td style="word-break:break-all;"><? echo $team_leaders;?></td>
					<td>Dealing Marchant</td>
                    <td style="word-break:break-all;" colspan="3"><? echo $dealing_marchants;?></td>
					<td>Unit Price</td>
					<td align="right"><? echo number_format($unitPrice,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Garments Item</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Ship Qty</td>
					<td align="right"><? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Shipment Value</td>
					<td align="right"><? echo number_format($exfactoryValue,4); ?></td>
					<td>Short/Excess Qty</td>
					<td align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right" colspan="3"><? echo number_format($shortExcessValue,4); ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>
	
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px; margin-top:10px" rules="all">
				<thead>
                	<tr>
                    <td> </td>
                    <td> </td>
                     <td colspan="3" align="center" title="Price Quotation ID=<? echo $quotationId;?>"> <b>Price Quot. Cost</b></td>
                     <td colspan="4" align="center"> <b>Budget Cost</b></td>
                     <td colspan="4" align="center"> <b>Post Cost</b></td>
                    </tr>
					<tr align="center" style="font-weight:bold">
						<td width="300">Item Description</td>
						<td width="60">UOM</td>
						<td width="100">Marketing Qty</td>
						<td width="100">Marketing Price</td>
						<td width="100">Marketing Value</td>
                        
						<td width="100">Pre-Cost Qty</td>
						<td width="100">Pre-Cost Price</td>
						<td width="100">Pre-Cost Value</td>
                        <td width="100">Cont. % On FOB Value</td>
                        
						<td width="100">Actual Qty</td>
						<td width="100">Actual Price</td>
						<td width="100">Actual Value</td>
                        <td width="100">Cont. % On FOB Value</td>
					</tr>
				</thead>
				
				<?
				$GrandTotalMktValue=0; $GrandTotalPreValue=0; $GrandTotalAclValue=0; $yarnTrimMktValue=0; $yarnTrimPreValue=0; $yarnTrimAclValue=0; $totalMktValue=0; $totalPreValue=0; $totalAclValue=0; $totalMktQty=0; $totalPreQty=0; $totalAclQty=0;
				 
				  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				 
				?>
				
				<?
				  $f1=2;
				  $f=2;$mkt_amount=$pre_amount=$acl_amount=0;
				foreach($fabPurArr as $fab_index=>$row ){
					if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$fabric_description=explode("_",str_replace("'","",$fab_index));
					//$fabric_description=$fabPurArr[$fab_index]['mkt']['woven']['fabric_description'];
					$uom_name=$unit_of_measurement[$fabDescArr[$fabric_description[1]]['uom']];
				?>
				 <tr class="fbpur" bgcolor="<? echo $fab_bgcolor2;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $fab_bgcolor2;?>')" id="trfab_<? echo $f; ?>">
					<td width="300" title="Fabric-> <? echo $fab_index;?>" > <? echo $fabric_description[0];?></td>
					<td width="60"><? echo $uom_name;?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['mkt']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']/$fabPurArr[$fab_index]['mkt']['woven']['qty'],4);else echo " "; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount'],4); ?></td>
                    
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['pre']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount']/$fabPurArr[$fab_index]['pre']['woven']['qty'],4);else echo " "; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right" title="<?=$sample_booking_all_qnty?>">
						
							<a href="#" onClick="openmypage_sample_fab('<? echo implode("_",array_keys($poNumberArr)); ?>','<?=$jobNumber?>')">
								<? echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['qty']+$sample_booking_all_qnty,4) ?>
							</a>
						</td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['acl']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount']/$fabPurArr[$fab_index]['acl']['woven']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['acl']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
               <?
			   $f++;
			 	 $mkt_amount+=$fabPurArr[$fab_index]['mkt']['woven']['amount'];
				$pre_amount+=$fabPurArr[$fab_index]['pre']['woven']['amount'];
				$acl_amount+=$fabPurArr[$fab_index]['acl']['woven']['amount'];
				}
			   ?>
                
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						$fabPurMkt= number_format($mkt_amount,4,".","");
						echo number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($pre_amount,4,".","");
						$fabPurAcl= number_format($acl_amount,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor="<? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
                      <td width="100" align="right"><? echo number_format(($fabPurPre*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<?
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($fabPurAcl*100/$jobValue),2).'%'; ?></td>
				</tr>
				
	
				 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
			  <?
	
				  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;
				?>
				 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="300">Cost<? //echo $item_descrition ?></td>
						<td width="60"></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount'],4); ?></td>
                        
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        
						
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? if($grey_fab_trans_amt_acl>0) echo number_format($grey_fab_trans_amt_acl/$grey_fab_trans_qty_acl,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($grey_fab_trans_amt_acl,4); ?></a>&nbsp;<? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
                       <td width="100" align="right"><? echo number_format(($grey_fab_trans_amt_acl*100/$jobValue),2).'%'; ?></td>
					</tr>
				<?
				 $tt2=1;
				?>
				<tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="300">Finished Fabric Cost</td>
						<td width="60">Yds</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost>0) echo number_format($tot_wvn_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_wvn_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
                         <td width="100" align="right"><? echo number_format(($tot_wvn_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
					</tr>
					<?
	
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    <td width="100" align="right"><? //echo number_format(($tot_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"> <? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl>0) echo number_format(($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);else "";?></td>
					<td width="100" align="right">
						<?
						echo number_format($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl,4);
						$total_grey_fin_fab_transfer_actl_cost=$tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format((($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr>
					<td colspan="12" style="font-weight:bold" class="trims">Trims Cost</td>
				</tr>
				<?
				$t=1; $totalTrimMktValue=0; $totalTrimPreValue=0; $totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					if($index>0)
					{
						$item_descrition = $trim_groupArr[$index]['item_name'];
						$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
						$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
						$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
						if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr class="trims" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
							<td width="300"><? echo $item_descrition ?></td>
							<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
                            
							<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
							<td width="100" align="right"  bgcolor=" <? if(number_format($row['pre']['amount'],4,'.','')>number_format($row['mkt']['amount'],4,'.','')){echo "yellow";} else{echo "";}?>"><? echo number_format($row['pre']['amount'],4); ?></td>
                             <td width="100" align="right"><? echo number_format(($row['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                             
							<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
							<td width="100" align="right"><? if($row['acl']['amount']>0)  echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
							<td width="100" align="right" title="<? echo 'Act='.$row['acl']['amount'].', Pre='.$row['pre']['amount'];?>" bgcolor=" <? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}  else{echo " ";}?>"><? echo number_format($row['acl']['amount'],4); ?></td>
                            <td width="100" align="right"><? echo number_format(($row['acl']['amount']*100/$jobValue),2).'%'; ?></td>
						</tr>
						<?
						$t++;
					}
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300"><span id="trimstotal" class="adl-signs" onClick="yarnT(this.id,'.trims')">+</span>&nbsp;&nbsp Trims Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						echo number_format($totalTrimMktValue,4);
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($totalTrimPreValue*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if(number_format($totalTrimPreValue,4,".","")>number_format($totalTrimMktValue,4,".","")){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalTrimAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<?
				$totalOtherMktValue=0; $totalOtherPreValue=0; $totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 
				  <?
				$w=1;$total_mkt_qty=$total_mkt_amt=$total_pre_qty=$total_pre_amt=$total_acl_qty=$total_acl_amt=0;
                foreach($washData as $index=>$row ){
					$index=str_replace("'","",$index);
					$item_des=explode("_",$index);
					$item_des=implode(",",$item_des);
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr  class="wash" bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="300" ><? echo  $item_des;	?></td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['mkt']['amount']>0) echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); $GrandTotalMktValue+=number_format($row['mkt']['amount'],4,".","");?></td>
                    
					<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['pre']['amount']>0) echo number_format($row['pre']['amount']/$row['pre']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['pre']['amount'],4,".","")>number_format($row['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($row['pre']['amount'],4);$GrandTotalPreValue+=number_format($row['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format(($row['pre']['amount']/$jobValue)*100,4); ?></td>
                     <td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['acl']['amount']>0) echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($row['acl']['amount'],4);$GrandTotalAclValue+=number_format($row['acl']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($row['acl']['amount']/$jobValue)*100,4); ?></td>
				</tr>
                <?
				$w++;
				$total_mkt_qty+=$row['mkt']['qty'];
				$total_mkt_amt+=$row['mkt']['amount'];
				$total_pre_qty+=$row['pre']['qty'];
				$total_pre_amt+=$row['pre']['amount'];
				$total_acl_qty+=$row['acl']['qty'];
				$total_acl_amt+=$row['acl']['amount'];
				}
				?>
                 <tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="washtotal"  class="adl-signs" onClick="yarnT(this.id,'.wash')">+</span>&nbsp;&nbsp;Wash Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo number_format($total_mkt_qty,4); ?></td>
					<td width="100" align="right"><? if($total_mkt_amt>0) echo number_format($total_mkt_amt/$total_mkt_qty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($total_mkt_amt,4); //$totalOtherMktValue+=number_format($total_mkt_amt,4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($total_pre_qty,4); ?></td>
					<td width="100" align="right"><? if($total_pre_amt>0) echo number_format($total_pre_amt/$total_pre_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_pre_amt,4,".","")>number_format($total_pre_amt,4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($total_pre_amt,4);//$totalOtherPreValue+=number_format($total_pre_amt,4,".",""); ?></td>
                      <td width="100" align="right"><?  if($total_pre_amt>0)  echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                      
					<td width="100" align="right"><? echo number_format($total_acl_qty,4); ?></td>
                   
					<td width="100" align="right"><? if($total_acl_amt>0) echo number_format($total_acl_amt/$total_acl_qty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_acl_amt,4,".","")>number_format($total_acl_amt,4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($total_acl_amt,4);//$totalOtherAclValue+=number_format($total_acl_amt,4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($total_acl_amt/$jobValue)*100,4); ?></td>
				</tr>
                <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="300" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['mkt']['amount']>0) echo number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=number_format($embData['mkt']['amount'],4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['pre']['amount']>0) echo number_format($embData['pre']['amount']/$embData['pre']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100" align="right"><? echo number_format($embData['acl']['qty'],4); ?></td>

					<td width="100" align="right"><? if($embData['acl']['amount']) echo number_format($embData['acl']['amount']/$embData['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['acl']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
                
                
                
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="300" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=number_format($commiData['pre']['amount'],4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commiData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? //echo $exfactoryQty ?></td>
					<td width="100" align="right"><? //$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty; echo number_format($aclCommiAmt/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? //echo number_format($aclCommiAmt,4); $totalOtherAclValue+=$aclCommiAmt; ?></td>
                    <td width="100" align="right"><? //echo number_format(($aclCommiAmt*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="300" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($commaData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; //$commer_act_cost=($unitPrice*$exfactoryQty)*(0.5/100); ?></td>
					<td width="100" align="right"><?  if($commaData['acl']['amount']>0 && $exfactoryQty>0) echo  number_format($commaData['acl']['amount']/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" align="right" title="" bgcolor="<? //if(number_format($exfactoryQty*$unitPrice)>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					$commer_act_cost=$commaData['acl']['amount'];
					echo number_format($commer_act_cost,4); $totalOtherAclValue+=number_format($commer_act_cost,4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commer_act_cost*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="300" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['freight']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                     
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="300" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['ore']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty ?></td>
					<td width="100" align="right"><? 
					$test_act_cost=$otherData['acl']['lab_test']['amount'];
					//$test_act_cost=$unitPrice*$exfactoryQty*(0.05/100);
					 if($test_act_cost>0 && $exfactoryQty>0) echo number_format($test_act_cost/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" title="" bgcolor="<? //if(number_format($test_act_cost,4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					
					echo number_format($test_act_cost,4); $totalOtherAclValue+=number_format($test_act_cost,4,".","");?></td>	
                     <td width="100" align="right"><? echo number_format(($test_act_cost*100/$jobValue),2).'%'; ?></td>				
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="300" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? if($otherData['acl']['inspection']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['inspection']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
				</tr>
				<tr bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="300" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['currier_pre_cost']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="300" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['cm_cost']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<?
					if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")) $font_color="white";
					else $font_color="black";
					$making_cost_act=$otherData['pre']['cm_cost']['rate']*$exfactoryQty*(80/100); 
					?>
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? if($making_cost_act>0 && $exfactoryQty>0) echo number_format($making_cost_act/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" title="(Pre Cost Rate(<? echo $otherData['pre']['cm_cost']['rate'];?>)*Ex Fact Qty(<? echo $exfactoryQty;?>)*80)/100)"  align="right"  bgcolor="<? if(number_format($making_cost_act,4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=number_format($making_cost_act,4,".",""); echo number_format($making_cost_act,4); ?>
                    </td>
                     <td width="100" align="right" title="Making Cost Actual/Job Value*100"><? echo number_format(($making_cost_act*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" >Others Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($totalOtherMktValue,4); $GrandTotalMktValue+=number_format($totalOtherMktValue,4,".",""); ?></td>
					
                    <td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? echo number_format($totalOtherPreValue,4); $GrandTotalPreValue+=number_format($totalOtherPreValue,4,".",""); ?>
					</td>
                   <td width="100" align="right"><? echo number_format(($totalOtherPreValue*100/$jobValue),2).'%'; ?></td>
                   
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? echo number_format($totalOtherAclValue,4); $GrandTotalAclValue+=number_format($totalOtherAclValue,4,".",""); ?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalOtherAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="300" >Grand Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
                     <td width="100" align="right"><? echo number_format(($GrandTotalPreValue*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
                    <td width="100" align="right"><? echo number_format(($GrandTotalAclValue*100/$jobValue),2).'%'; ?></td>
                    
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="300" >Shipment Value</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right"><? echo $quaOfferQnty;  ?></td>
					<td width="100" align="right" title="<? echo $quaPriceWithCommnPcs;?>"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right"><? $quaOfferValue=$quaOfferQnty*$quaPriceWithCommnPcs; echo number_format($quaOfferValue,4) ?></td>
					
                    <td width="100" align="right"><? echo $jobQty; ?></td>
					<td width="100" align="right" title="<? echo $unitPrice;?>"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
                    <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? echo $exfactoryQty ; ?></td>
					<td width="100" align="right" title="<? echo $exfactoryValue/$exfactoryQty;?>"><? if($exfactoryValue>0 && $exfactoryQty>0) echo number_format($exfactoryValue/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4); ?></td>
                      <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
				</tr>
			</table>
			<table style="width:120px;float: left; margin-left: 5px; margin-top: 10px;">
				<tr>
					
                    <?
                    $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in('$jobNumber') and form_name='knit_order_entry' and file_type=1");
					if(count( $nameArray_img)>0)
					{
					    ?>
						    
                            
							<? 
							foreach($nameArray_img AS $inf) { 
								?>
                                 <td width="120">
                                <div style="float:left;width:120px" >
								<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='75' width='95' />
                                 </td>
                               </div>
							    <?  
							}
							?>
							
						<?								
					}
					else echo ""; ?>						    
                   
				</tr>
            </table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="10"><p style="font-weight: bold;">Profit Summary</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"  title="Total Mkt Value/Offer Value*100"><? if($GrandTotalMktValue>0) echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right" title="Total Expand/Offer Value*100"><? if($yarnTrimMktValue>0 && $quaOfferValue>0) echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
                    <td width="150" align="right"><? echo number_format($total_mkt_amt,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash/Offer Value*100"><? if($total_mkt_amt>0 && $quaOfferValue>0) echo number_format(($total_mkt_amt/$quaOfferValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Offer Value-Grand Total Mkt Value"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right" title="Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Offer Value(<? echo $quaOfferValue;?>)*100" ><? if($quaOfferValue-$GrandTotalMktValue>0 && $quaOfferValue>0) echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Total Pre Value/Job Value*100"><? if($GrandTotalPreValue>0 && $jobValue>0) echo number_format(($GrandTotalPreValue/$jobValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? if($yarnTrimPreValue>0 && $jobValue>0) echo number_format(($yarnTrimPreValue/$jobValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_pre_amt,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><? if($total_pre_amt>0 && $jobValue>0) echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Job Value-Grand Total Pre Value"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Ex-Fact Value(<? echo $jobValue;?>)*100"><?  if($jobValue-$GrandTotalPreValue>0 && $jobValue>0)  echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);else echo "";?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Total Actual Value/Ex-Fact Value*100"><? if($GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Ex-fact Value*100"><?  if($yarnTrimAclValue>0 && $exfactoryValue>0) echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_acl_amt,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Ex-Fact Value*100"><? if($total_acl_amt>0 && $exfactoryValue>0) echo number_format(($total_acl_amt/$exfactoryValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value-Grand Total Acl Value"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Ex-fact Value(<? echo $exfactoryValue;?>)-GrandTotal Ex Value(<? echo $GrandTotalAclValue;?>)/Ex-Fact Value(<? echo $exfactoryValue;?>)*100"><? if($exfactoryValue-$GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);else echo "";?></td>
				</tr>
			</table>
            <br>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="11"><p style="font-weight: bold;">Profit Summary 1 Pcs……</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">FOB/Pcs</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    <td width="100">CM Cost</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right" title="Mkt Cost/Job Qty"><? $tot_offer_val_pcs=$unitPrice;//$quaOfferValue/$jobQty;
					echo number_format($tot_offer_val_pcs,4) ?></td>
					<td width="150" align="right" title="Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $tot_mkt_val_pcs=$GrandTotalMktValue/$jobQty;echo number_format($tot_mkt_val_pcs,4); ?></td>
					<td width="100" align="right"  title="Total Expenditure(<? echo $tot_mkt_val_pcs;?>)/FOB Pcs(<? echo $tot_offer_val_pcs;?>)*100"><? if($tot_mkt_val_pcs>0) echo number_format(($tot_mkt_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right" title="Total Trims Mkt value(<? echo $yarnTrimMktValue?>)/Job Qty(<? echo $jobQty?>)"><? 
					if($yarnTrimMktValue>0)
					{
					$tot_trim_val_pcs=$yarnTrimMktValue/$jobQty;
					}
					else $tot_trim_val_pcs=0;
					echo number_format($tot_trim_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Trims(<? echo $tot_trim_val_pcs?>)/FOB Pcs(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_trim_val_pcs>0) echo number_format(($tot_trim_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                    <td width="150" align="right" title="Total Mkt Trims(<? echo $total_mkt_amt?>)/Job Qty(<? echo $jobQty?>)"><? $tot_wash_val_pcs=$total_mkt_amt/$jobQty; echo number_format($tot_wash_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash(<? echo $tot_wash_val_pcs?>)/Offer Value(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_wash_val_pcs>0) echo number_format(($tot_wash_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Cost(<? echo $otherData['mkt']['cm_cost']['amount'];?>)/Job Qty"><? $tot_mkt_cm_pcs=$otherData['mkt']['cm_cost']['amount']/$jobQty; echo number_format($tot_mkt_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Qua.Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $profit_mkt_margin_pcs=($quaOfferValue-$GrandTotalMktValue)/$jobQty;echo number_format($profit_mkt_margin_pcs,4); ?></td>
					<td width="100" align="right" title="Profit margin(<? echo $profit_mkt_margin_pcs;?>)/Mkt FOB/Pcs (<? echo $tot_offer_val_pcs;?>)*100)"><? echo number_format(($profit_mkt_margin_pcs/$tot_offer_val_pcs)*100,4); ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right" title="Pre Cost/Job Qty"><? $tot_preJob_value=$unitPrice;//$jobValue/$jobQty;
					echo number_format($tot_preJob_value,4) ?></td>
					<td width="150" align="right"><? $tot_pre_costing_expnd=$GrandTotalPreValue/$jobQty; echo number_format($tot_pre_costing_expnd,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? $tot_pre_costing_expnd_per=$tot_pre_costing_expnd/$tot_preJob_value*100;echo number_format($tot_pre_costing_expnd_per,4); ?></td>
					<td width="150" align="right" title="Total Trims Value/Job Qty"><? $tot_preTrim_costing=$yarnTrimPreValue/$jobQty;echo number_format($tot_preTrim_costing,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? $tot_preTrim_costing_per=($tot_preTrim_costing/$tot_preJob_value)*100;echo number_format($tot_preTrim_costing_per,4); ?></td>
                     <td width="150" align="right" title="Pre Wash Cost/Job Qty"><?  $tot_preWash_value=$total_pre_amt/$jobQty;echo number_format($tot_preWash_value,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><?  $tot_preWash_value_pre=$tot_preWash_value/$tot_preJob_value*100;echo number_format($tot_preWash_value_pre,4); ?></td>
                     <td width="150" align="right" title="Cm Cost(<? echo $otherData['pre']['cm_cost']['amount'];?>)/Job Qty"><? $tot_pre_cm_pcs=$otherData['pre']['cm_cost']['amount']/$jobQty; echo number_format($tot_pre_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Job Qty"><? $profit_pre_margin_pcs=($jobValue-$GrandTotalPreValue)/$jobQty;echo number_format($profit_pre_margin_pcs,4);?></td>
					<td width="100" align="right" title="Profit Margin(<? echo $profit_pre_margin_pcs;?>)/Pre FOB Pcs (<? echo $tot_preJob_value;?>)*100)"><? echo number_format(($profit_pre_margin_pcs/$tot_preJob_value)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right" title="Acl Job Value/Job Qty"><? $tot_AclJob_valuePcs=$unitPrice;//$exfactoryValue/$jobQty;
					echo number_format($tot_AclJob_valuePcs,4) ?></td>
					<td width="150" align="right" title="Grand total Acl value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><? $tot_AclJob_ExpndPcs=$GrandTotalAclValue/$jobQty; echo number_format($tot_AclJob_ExpndPcs,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? if($tot_AclJob_ExpndPcs>0) echo number_format(($tot_AclJob_ExpndPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right"  title="Total Acl Trims Value/Job Qty"><? $tot_AcltrimValue=$yarnTrimAclValue/$jobQty;echo number_format($tot_AcltrimValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Job Value*100"><? if($tot_AcltrimValue>0) echo number_format(($tot_AcltrimValue/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? $tot_AclJobWashPcs=$total_acl_amt/$jobQty; echo number_format($tot_AclJobWashPcs,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Job Value*100"><? if($tot_AclJobWashPcs>0) echo number_format(($tot_AclJobWashPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="Making Cost(<? echo $making_cost_act;?>)/Job Qty"><?  $tot_cmAcl_pcs=$making_cost_act/$jobQty;; echo number_format($tot_cmAcl_pcs,4) ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value(<? echo $exfactoryValue;?>)-Grand Total Acl Value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><?  $profit_acl_margin_pcs=($exfactoryValue-$GrandTotalAclValue)/$jobQty; echo number_format($profit_acl_margin_pcs,4);?></td>

					<td width="100" align="right" title="Profit margin(<? echo $profit_acl_margin_pcs;?>)/Actual FOB/Pcs (<? echo $tot_AclJob_valuePcs;?>)*100)"><? if($profit_acl_margin_pcs>0) echo number_format(($profit_acl_margin_pcs/$tot_AclJob_valuePcs)*100,4);else echo "";?></td>
				</tr>
			</table>
             <br>
              <br>
		</div>
		<?
	}
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
	
	exit();
}
if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo str_replace("'","",$cbo_costcontrol_source);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");


	//echo $cbo_costcontrol_source.'dd';

	if(str_replace("'","",$cbo_costcontrol_source)==1)
	{
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){ echo "Select Company"; die; }
		if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		$check_data=sql_select("select a.job_no from wo_po_details_master a,wo_pre_cost_mst f where  a.id=f.job_id  and a.company_name=$company_name and a.garments_nature=3 and a.job_no_prefix_num like '$job_no' $year_cond group by a.job_no");
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		//$chk_job_nos=$job_no;
		if($chk_job_nos!='') 
		{
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.inquiry_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and  a.id=c.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
			$result=sql_select($sql);
		}
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
		$shipArry=array(0,1,2);
		$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotation_id=$row[csf('quotation_id')];
			$quotationId=$row[csf('inquiry_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$ShipDateArrall[date("d-m-Y",strtotime($row[csf('shipment_date')]))]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			/* if($row[csf('shiping_status')]==3) //Full
			{
			 $shiping_status=2; 
			}
			//echo $shiping_status.'D';
			$shipingStatus=$shiping_status; */
			$shipingStatus=$row[csf('shiping_status')];
			$shiping_status_id.=$row[csf('shiping_status')].',';
			$po_ids.=$row[csf('id')].',';$ref_no.=$row[csf('grouping')].',';
			$team_leaders.=$team_leader_library[$row[csf('team_leader')]].',';
			$dealing_marchants.=$dealing_merchant_array[$row[csf('dealing_marchant')]].',';
		}

		$sql_inspection="SELECT sum(b.net_amount) as inspection_qnty FROM wo_po_details_master a, wo_inspection_dtls b WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and a.id=b.job_id and b.entry_form=605 AND a.job_no='$jobNumber'";
		$data_inspection=sql_select($sql_inspection);
		foreach($data_inspection as $row){
			$inspection_qnty=$row[csf('inspection_qnty')];
		}
		unset($data_inspection);
		$sql_labtest="SELECT sum(b.wo_value) as labtest_qnty FROM wo_po_details_master a, wo_labtest_dtls b WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and a.job_no=b.job_no  and b.entry_form=274 AND a.job_no='$jobNumber'";
		$data_labtest=sql_select($sql_labtest);
		foreach($data_labtest as $row){
			$labtest_qnty=$row[csf('labtest_qnty')];
		}
		unset($data_labtest);
		  $sql_sample="SELECT sum(d.required_qty) as qnty
			    FROM wo_po_details_master a, sample_development_mst c,sample_development_fabric_acc d
			    WHERE     a.status_active = 1
			         AND a.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         and d.sample_mst_id=c.id and c.entry_form_id=117 and a.style_ref_no=c.style_ref_no
			         AND c.quotation_id = a.id
			         AND d.form_type = 1
			         AND d.is_deleted = 0
			         AND d.status_active = 1
			         AND a.job_no='$jobNumber'

	       ";

	  //  echo "<pre>$sql_sample</pre>";
	    $res_sample=sql_select($sql_sample);
	     $sql_sample_fab="SELECT SUM (fin_fab_qnty) AS qnty
						  FROM wo_booking_dtls
						 WHERE     status_active = 1
						       AND is_deleted = 0
						       AND job_no='$jobNumber'
						       AND entry_form_id = 440";
		//echo "<pre>$sql_sample_fab</pre>";
	    $res_sample_fab=sql_select($sql_sample_fab);

	    $booking_sql3=sql_select("SELECT 
			         SUM (a.fin_fab_qnty) AS qnty
			        
			    FROM wo_booking_dtls a, wo_booking_mst b
			   WHERE     a.booking_mst_id = b.id
			         AND a.is_short = 2
			         AND b.booking_type = 4
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND a.job_no = '$jobNumber'
			
				 ");
				
	    $sample_booking_all_qnty=$res_sample[0][csf('qnty')]+$res_sample_fab[0][csf('qnty')]+$booking_sql3[0][csf('qnty')];
		 //print_r($ShipDateArrall);
		$shiping_status_id=rtrim($shiping_status_id,',');
		
		$shiping_status_idAll=array_unique(explode(",",$shiping_status_id));
		//print_r($shiping_status_idAll);
		$shiping_status=3;
		foreach($shiping_status_idAll as $sid)
		{
			//echo $sid."D,";
			if(in_array($sid,$shipArry))
			{
				$shiping_status=2;
			}
		}
		//echo $shiping_status;
		$team_leaders=rtrim($team_leaders,',');
		$team_leaders=implode(",",array_unique(explode(",",$team_leaders)));
		$dealing_marchants=rtrim($dealing_marchants,',');
		$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchants)));
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$budget_on_with_job=sql_select("SELECT budget_on from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 and job_no = '$jobnumber' group by budget_on");
		$budget_on=2;
		if(count($budget_on_with_job)>0){
			foreach($budget_on_with_job as $row){
				$budget_on=$row[csf('budget_on')];
			}
		}

		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		if($budget_on==2){
			$fabricjobQty=array_sum($poPcutQtyArr);
		}
		else{
			$fabricjobQty=array_sum($poQtyArr);
		}

		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0) $po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; else $po_cond_for_in="";
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		//$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessQty=$exfactoryQty-array_sum($poQtyArr);
		$shortExcessValue=$shortExcessQty*($unitPrice);
	    //$quotationId=1;
		
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	    // Yarn ============================
		$totYarn=0;
		$fabPurArr=array(); $YarnData=array(); $qYarnData=array(); $knitData=array(); $finishData=array(); $washData=array(); $embData=array(); $trimData=array(); $commiData=array(); $otherData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	    //print_r($yarn_data_array);
		$sql_yarn="select count_id, copm_one_id, percent_one, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id, copm_one_id, percent_one, type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row)
		{
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		unset($data_arr_yarn);
		/*echo "<pre>";
		print_r($YarnData); die;*/
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		if($quotationId)
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			   $sqlQc="select a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.inquery_id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQc=sql_select($sqlQc);
			//print_r($dataQc);
			foreach($dataQc as $qcrow)
			{
				//echo $qcrow[csf('offer_qty')].'=';
				$quaOfferQnty=$fabricjobQty;//$qcrow[csf('offer_qty')];
				$quaConfirmPrice=$qcrow[csf('confirm_fob')];
				$quaConfirmPriceDzn=$qcrow[csf('confirm_fob')];
				$quaPriceWithCommnPcs=$qcrow[csf('confirm_fob')];
				$quaCostingPer=$qcrow[csf('costing_per')];
				$qc_no=$qcrow[csf('qc_no')];
				$quaCostingPerQty=0; 
				//if($quaCostingPer==1) 
				$quaCostingPerQty=1;
			}
			unset($dataQc);
			$qc_no=$quotation_id;//issue id= 76,Yr-24
			$sql_cons_rate="select id, mst_id, fab_id,item_id, type,description, particular_type_id, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where status_active=1 and is_deleted=0 and mst_id =".$qc_no." order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate);
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				if($rowConsRate[csf("type")]==1) //Fabric
				{
					if(($rowConsRate[csf("rate")]*1)>0)
					{
						$yarnrate=$edata[3]+$edata[7]+$edata[11];
						$consQnty=($rowConsRate[csf("tot_cons")]/12)*($quaOfferQnty);
						$amount=$consQnty*$yarnrate;
						$index="";
						$index=$edata[0].','.$edata[4].','.$edata[8];						
						$index=""; $mktcons=$mktamt=0;
						$index="'".$rowConsRate[csf('particular_type_id')]."'";
						$mktcons=$mktamt=0;						
						$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
						$mktamt=$mktcons*$rowConsRate[csf('rate')];						
						$fabStri=$rowConsRate[csf('description')].'_'.$rowConsRate[csf('fab_id')];
						$fabPurArr[$fabStri]['mkt']['woven']['fabric_description']=$rowConsRate[csf('description')];
						$fabPurArr[$fabStri]['mkt']['woven']['qty']+=$mktcons;
						$fabPurArr[$fabStri]['mkt']['woven']['amount']+=$mktamt;
					}
					
				}
				//print_r($fabPurArr);
				if($rowConsRate[csf("type")]==3 ) //Wash
				{
					$index="'".$emblishment_wash_type_arr[$rowConsRate[csf("particular_type_id")]]."'";
					
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					if($rowConsRate[csf('rate')]>0)
					{
					$washData[$index]['mkt']['qty']+=$mktcons;
					$washData[$index]['mkt']['amount']+=$ConsRate*$mktcons;
					}
				}
				if($rowConsRate[csf("type")]==2)
				{
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					$embData['mkt']['qty']+=$mktcons;
					if($ConsRate && $mktcons)
					{
						$embData['mkt']['amount']+=$ConsRate*$mktcons;
					}
					
					
				}
				if($rowConsRate[csf("type")]==4) //Acceoosries
				{
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					$trimData[$rowConsRate[csf('particular_type_id')]]['mkt']['qty']+=$mktcons;
					$trimData[$rowConsRate[csf('particular_type_id')]]['mkt']['amount']+=$mktamt;
					$trimData[$rowConsRate[csf('particular_type_id')]]['cons_uom']=$rowConsRate[csf('cons_uom')];
				}
			}

			$sql_item_summ="select mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost,courier_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs,commercial_cost from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$sql_result_item_summ=sql_select($sql_item_summ);
			foreach($sql_result_item_summ as $rowItemSumm)
			{
				$consQnty=$amount=$mktamt=0;
				$mktamt=($rowItemSumm[csf("commission_cost")]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['amount']+=$mktamt;
				$freightAmt=($rowItemSumm[csf('frieght_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['amount']+=$freightAmt;
				$labTestAmt=($rowItemSumm[csf('lab_test_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['amount']+=$labTestAmt;
				$courier_costAmt=($rowItemSumm[csf('courier_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['amount']+=$courier_costAmt;
				$cmCostAmt=($rowItemSumm[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['amount']+=$cmCostAmt;
			}
		}

		
		
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
		
		$fin_fab_trans_array=array();
		  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
		 	 sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, 
			 sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt,
			sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, 
			c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$wvn_fin_fab_trans_amt_acl=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			$tot_fin_fab_transfer_qty+=$row[csf('in_qty')]-$row[csf('out_qty')];
			$tot_trans_amt=$row[csf('in_amt')]-$row[csf('out_amt')];
			$wvn_fin_fab_trans_amt_acl+=$tot_trans_amt/$g_exchange_rate;
		}
		unset($result_fin_trans);
		$tot_wvn_fin_fab_transfer_cost=$wvn_fin_fab_trans_amt_acl;
		
		$trim_fab_trans_array=array();
		$sql_trim_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity*a.rate ELSE 0 END) AS grey_in_amt,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity*a.rate ELSE 0 END) AS grey_out_amt
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_trim_trans=sql_select( $sql_trim_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_trim_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in_amt')]-$row[csf('grey_out_amt')])/$g_exchange_rate;
		}
		//echo $grey_fab_trans_amt_acl;
		unset($result_trim_trans);
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
	
		$subconOutBillData="select b.order_id,
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
		$totPrecons=0; $totPreAmt=0;
		//echo $fabric->getQuery();die;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		
		//print_r($fabPur);
		  $sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id,uom, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2 and status_active=1 and is_deleted=0";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
		//	$fabStri=$fabPur_row[csf('id')];
			$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
			$fabDescArr[$fabPur_row[csf('id')]]['fabric_description']=$fabPur_row[csf('fabric_description')];
			//$fabPurArr[$fabStri]['pre']['knit']['qty']=$fabPur_row[csf('uom')];
			$fabDescArr[$fabPur_row[csf('id')]]['uom']=$fabPur_row[csf('uom')];
			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				
				$fabPurArr[$fabStri]['pre']['knit']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['woven']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['woven']['amount']+=$Preamt;
			}
		} 
		
		 $sql = "select a.item_category, a.exchange_rate, b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,b.uom,b.grey_fab_qnty, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				
				$fabric_description=$fabDescArr[$fabPur_row[csf('fab_dtls_id')]]['fabric_description'];
				$fabStri=$fabric_description.'_'.$fabPur_row[csf('fab_dtls_id')];
				if($fabPur_row[csf('item_category')]==2){
					$fabPurArr[$fabStri]['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
					}
				}
				if($fabPur_row[csf('item_category')]==3){
					$fabPurArr[$fabStri]['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					//alc_woven_qnty
					//echo $fabPur_row[csf('grey_fab_qnty')].'f';
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
					}
				}
			}
		
		//	print_r($fabPurArr);
	
		// Fabric AOP  End============================
		// Trim Cost ============================
		//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$trim= new trims($condition);
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	
		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		//print_r($trimData);
		
		$trimsRecArr=array();
	
		$general_item_issue_sql="select a.item_group_id as trim_group, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_id=d.id and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in(".implode(",",$poIdArr).") ";
			 $gen_result=sql_select($general_item_issue_sql);
			foreach($gen_result as $row)
            {
				$con_factor=$trim_groupArr[$row[csf('trim_group')]]['con_factor'];
				$gen_item_arr[$row[csf('trim_group')]]['issue_qty']+=$row[csf('cons_quantity')];
				$gen_item_arr[$row[csf('trim_group')]]['cons_amount']+=($row[csf('cons_amount')]*$con_factor)/$g_exchange_rate;
            }
			//print_r($gen_item_arr);
			foreach($gen_item_arr as $ind=>$value)
			{
			$trimData[$ind]['acl']['qty']+=$value['issue_qty'];
			$trimData[$ind]['acl']['amount']+=$value['cons_amount'];
		    }
		//print_r($trimsRecArr);
		unset($gen_result);
		
		 
		 $sql_trim_wo = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.trim_group from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_trim_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=2  and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_wo=sql_select($sql_trim_wo);
		foreach($data_array_wo as $row){
			$index=$row[csf('trim_group')];
			$trimData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$trimData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		unset($data_array_wo);
	
		//print_r($trimData);
	
		// Trim Cost End============================
		// Embl Cost ============================
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		
		 $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			if($row[csf('emb_name')]==3)
			{
				$washData[$index]['pre']['qty']+=$washQtyArr[$row[csf("job_no")]][$row[csf('id')]];
				$washData[$index]['pre']['amount']+=$washAmtArr[$row[csf("job_no")]][$row[csf('id')]];
			}
			else
			{
				$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
				$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
			}
		}
		 $sql_emb = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_emb=sql_select($sql_emb);
		foreach($data_array_emb as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
		}
		// Embl Cost End ============================
		// Wash Cost ============================
		
		 
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			$washData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$washData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		
		// Wash Cost End ============================
		// Commision Cost  ============================
		
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}
		
		// Commision Cost  End ============================
	
		// Commarcial Cost  ============================
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			if($commaAmtArr[$jobNumber][$row[csf('id')]]>0 && $exfactory_qty>0)
			{
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;
			}
			else
			{
				$commaData['pre']['rate']+=0;
			}
			
		}
		
		// Commarcial Cost  End ============================
		// Other Cost  ============================
		$other_cost=$other->getAmountArray_by_job();
		 $sql = "select id, lab_test,  inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost from  wo_pre_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;
	
			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;
	
			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;
	
			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;
	
			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
		}
		
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	
	 /* $sql_cm_cost="select jobNo, available_min, production_date from production_logicsoft where jobNo='".$jobNumber."' and is_self_order=1";
		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$production_date=change_date_format($row[csf('production_date')],'','',1);
			$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
			$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		} */
	
		$sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==5){
				$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
			}
		}
		// Other Cost End ============================
		if($quotationId){
			$sql_item_summ="select commercial_cost from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$data_array=sql_select($sql_item_summ);
			foreach($data_array as $row){
				$mktCommaamt=($row[csf('commercial_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktCommaamt;
				$commaData['mkt']['rate']+=$mktCommaamt/$quaOfferQnty;
			}
		}
		/* echo '<pre>';
		print_r($commaData);die; */
		// Commarcial Cost  End ============================
		ob_start();
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1300px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px" rules="all">
				<tr>
					<td width="100">Job Number</td>
					<td width="200"><? echo $jobNumber; ?></td>
					<td width="100">Buyer</td>
					<td width="200"><? echo $buyer_arr[$buyerName]; ?></td>
                    <td width="100">Internal Ref. No</td>
					<td width="100"><? $ref_nos=rtrim($ref_no,',');echo implode(",",array_unique(explode(",",$ref_nos))); ?></td>
					<td width="100">Style Ref. No</td>
					<td width="200"><? echo $styleRefno; ?></td>
					<td width="100">Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Order Number</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo number_format($jobValue,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td style="word-break:break-all;"><? 
					 echo implode(",",$ShipDateArrall);
					 ?></td>
					<td>Team Leader</td> 
                    <td style="word-break:break-all;"><? echo $team_leaders;?></td>
					<td>Dealing Marchant</td>
                    <td style="word-break:break-all;" colspan="3"><? echo $dealing_marchants;?></td>
					<td>Unit Price</td>
					<td align="right"><? echo number_format($unitPrice,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Garments Item</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Ship Qty</td>
					<td align="right"><? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Shipment Value</td>
					<td align="right"><? echo number_format($exfactoryValue,4); ?></td>
					<td>Short/Excess Qty</td>
					<td align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right"><? echo number_format($shortExcessValue,4); ?></td>
					<td>Plan Cut Qty</td>
					<td align="right"><? echo $jobPcutQty; ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>
	
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px; margin-top:10px" rules="all">
				<thead>
                	<tr>
                    <td> </td>
                    <td> </td>
                     <td colspan="3" align="center"> <b>Buyer Cost</b></td>
                     <td colspan="5" align="center"> <b>Budget Cost</b></td>
                     <td colspan="5" align="center"> <b>Post Cost</b></td>
					 <td rowspan="2" align="center"> <b>Total Profit/Loss</b></td>
                    </tr>
					<tr align="center" style="font-weight:bold">
						<td width="300">Item Description</td>
						<td width="60">UOM</td>
						<td width="100">Marketing Qty</td>
						<td width="100">Marketing Price</td>
						<td width="100">Marketing Value</td>
                        
						<td width="100">Pre-Cost Qty</td>
						<td width="100">Pre-Cost Price</td>
						<td width="100">Pre-Cost Value</td>
                        <td width="100">Cont. % On FOB Value</td>
						<td width="100">Profit/Loss</td>
                        
						<td width="100">Actual Qty</td>
						<td width="100">Actual Price</td>
						<td width="100">Actual Value</td>
                        <td width="100">Cont. % On FOB Value</td>
						<td width="100">Profit/Loss</td>
					</tr>
				</thead>
				
				<?
				$GrandTotalMktValue=0; $GrandTotalPreValue=0; $GrandTotalAclValue=0; $yarnTrimMktValue=0; $yarnTrimPreValue=0; $yarnTrimAclValue=0; $totalMktValue=0; $totalPreValue=0; $totalAclValue=0; $totalMktQty=0; $totalPreQty=0; $totalAclQty=0;
				 
				  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				 
				?>
				
				<?
				  $f=2;$mkt_amount=$pre_amount=$acl_amount=0;
				foreach($fabPurArr as $fab_index=>$row ){
					if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$fabric_description=explode("_",str_replace("'","",$fab_index));
					//$fabric_description=$fabPurArr[$fab_index]['mkt']['woven']['fabric_description'];
					$uom_name=$unit_of_measurement[$fabDescArr[$fabric_description[1]]['uom']];
				?>
				 <tr class="fbpur" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $bgcolor;?>')" id="trfab_<? echo $f; ?>">
					<td width="300" title="Fabric-> <? echo $fab_index;?>" > <? echo $fabric_description[0];?></td>
					<td width="60"><? echo $uom_name;?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['qty'],4) ?></td> 
					<td width="100" align="right"  title="Amount/Qty"><? echo fn_number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']/$fabPurArr[$fab_index]['mkt']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount'],4); ?></td>
                    
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right" title="Amount/Qty"><? echo fn_number_format($fabPurArr[$fab_index]['pre']['woven']['amount']/$fabPurArr[$fab_index]['pre']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo fn_number_format($fabPurArr[$fab_index]['pre']['woven']['amount'],4); ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format(($fabPurArr[$fab_index]['mkt']['woven']['amount'])-($fabPurArr[$fab_index]['pre']['woven']['amount']),2);$fab_total_budget_cost+=($fabPurArr[$fab_index]['mkt']['woven']['amount'])-($fabPurArr[$fab_index]['pre']['woven']['amount']); ?></td>
					<td width="100" align="right" title="SampleBookingQty=<?=$sample_booking_all_qnty?>">
						
							<a href="#" onClick="openmypage_sample_fab('<? echo implode("_",array_keys($poNumberArr)); ?>','<?=$jobNumber?>')">
								<? echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['qty']+$sample_booking_all_qnty,4) ?>
							</a>
						</td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['acl']['woven']['amount']>0) echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['amount']/$fabPurArr[$fab_index]['acl']['woven']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount'],4); ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($fabPurArr[$fab_index]['acl']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=" Pre Cost Value-Actual value"><? echo fn_number_format($fabPurArr[$fab_index]['pre']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount'],2);$fab_booking_cost+=$fabPurArr[$fab_index]['pre']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount']; ?></td>
					<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo fn_number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount'],2);$fab_total_cost+=$fabPurArr[$fab_index]['mkt']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount']; ?></td>
				</tr>
                <?
				$f++;
				$mkt_amount+=$fabPurArr[$fab_index]['mkt']['woven']['amount'];
				$pre_amount+=$fabPurArr[$fab_index]['pre']['woven']['amount'];
				$acl_amount+=$fabPurArr[$fab_index]['acl']['woven']['amount'];
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						$fabPurMkt= number_format($mkt_amount,4,".","");
						echo number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($pre_amount,4,".","");
						$fabPurAcl= number_format($acl_amount,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor="<? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
                      <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($fabPurPre*100/$jobValue),2).'%'; ?></td>
					  <td width="100" align="right" title="Market Value-Pre Cost Value" ><? echo number_format($fab_total_budget_cost,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<?
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
					<td width="100" align="right"  title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($fabPurAcl*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value - Actual Value" ><? echo number_format($fab_booking_cost,2); ?></td>
					<td width="100" align="right" title=""><? echo number_format($fab_total_cost,2); ?></td>
				</tr>
				
	
				 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
			  <?
	
				  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;
				?>
				 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="300"> Cost<? //echo $item_descrition ?></td>
						<td width="60"></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount'],4); ?></td>
                        
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
                        <td width="100" align="right" title="Pre Cost Value/JobValue*100">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right" title="Market Value-Pre Cost Value"><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
						
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? if($grey_fab_trans_amt_acl>0) echo fn_number_format($grey_fab_trans_amt_acl/$grey_fab_trans_qty_acl,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($grey_fab_trans_amt_acl,4); ?></a>&nbsp;<? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
                       <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($grey_fab_trans_amt_acl*100/$jobValue),2).'%'; ?></td>
					   <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					   <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					</tr>
				<?
				 $tt2=1;
				?>
				<tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="300">Finished Fabric Cost</td>
						<td width="60">Yds</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        <td width="100" align="right" title="Pre Cost Value/JobValue*100"></td>
                        <td width="100" align="right" title="Market Value-Pre Cost Value"><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost>0) echo fn_number_format($tot_wvn_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_wvn_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
                         <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($tot_wvn_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
						 <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
						 <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					</tr>
					<?
	
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? //echo number_format(($tot_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl>0) echo number_format(($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);else echo "";?></td>
					<td width="100" align="right">
						<?
						echo number_format($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl,4);
						$total_grey_fin_fab_transfer_actl_cost=$tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo number_format((($tot_wvn_fin_fab_transfer_cost+$tot_grey_fab_cost_acl)*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr>
					<td colspan="12" style="font-weight:bold" class="trims">Trims Cost</td>
				</tr>
				<?
				$t=1; $totalTrimMktValue=0; $totalTrimPreValue=0; $totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					if($index>0)
					{
						$item_descrition = $trim_groupArr[$index]['item_name'];
						$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
						$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
						$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
						if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr class="trims" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
							<td width="300"><? echo $item_descrition ?></td>
							<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo fn_number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
                            
							<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
							<td width="100" align="right"><? echo fn_number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
							<td width="100" align="right"  bgcolor=" <? if(number_format($row['pre']['amount'],4,'.','')>number_format($row['mkt']['amount'],4,'.','')){echo "yellow";} else{echo "";}?>"><? echo number_format($row['pre']['amount'],4); ?></td>
                             <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($row['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                             <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($row['mkt']['amount']-$row['pre']['amount'],2);$trim_booking_cost+=$row['mkt']['amount']-$row['pre']['amount']; ?></td>
							<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
							<td width="100" align="right"><? if($row['acl']['amount']>0) echo fn_number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
							<td width="100" align="right" title="<? echo 'Act='.$row['acl']['amount'].', Pre='.$row['pre']['amount'];?>" bgcolor=" <? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}  else{echo " ";}?>"><? echo number_format($row['acl']['amount'],4); ?></td>
                            <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($row['acl']['amount']*100/$jobValue),2).'%'; ?></td>
							<td width="100" align="right" title="Pre Cost Value-Actual value"><? 
							echo number_format($row['pre']['amount']-$row['acl']['amount'],2);$trim_post_cost+=$row['pre']['amount']-$row['acl']['amount'];; ?></td>
							<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo fn_number_format($row['mkt']['amount']-$row['acl']['amount'],2);$trim_total_cost+=$row['mkt']['amount']-$row['acl']['amount']; ?></td>
						</tr>
						<?
						$t++;
					}
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300"><span id="trimstotal" class="adl-signs" onClick="yarnT(this.id,'.trims')">+</span>&nbsp;&nbspTrims Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						echo number_format($totalTrimMktValue,4);
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" title="If PrecostValue(<?=$totalTrimPreValue;?>) greater than Mkt(<?=$totalTrimMktValue;?>)" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".","");
						?>
					</td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($totalTrimPreValue*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($trim_booking_cost,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" title="If Acl Value(<?=$totalTrimAclValue;?>) greater than Mkt(<?=$totalTrimMktValue;?>)"  bgcolor=" <? if(number_format($totalTrimAclValue,4,".","")>number_format($totalTrimMktValue,4,".","")){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".","");
						?>
					</td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($totalTrimAclValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value - Actual Value"><? echo number_format($trim_post_cost,2); ?></td>
					<td width="100" align="right" title=""><? echo number_format($trim_total_cost,2); ?></td>
				</tr>
				<?
				$totalOtherMktValue=0; $totalOtherPreValue=0; $totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 
				  <?
				$w=1;$total_mkt_qty=$total_mkt_amt=$total_pre_qty=$total_pre_amt=$total_acl_qty=$total_acl_amt=0;
                foreach($washData as $index=>$row ){
					$index=str_replace("'","",$index);
					$item_des=explode("_",$index);
					$item_des=implode(",",$item_des);
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr  class="wash" bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="300" ><? echo  $item_des;	?></td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); $GrandTotalMktValue+=number_format($row['mkt']['amount'],4,".","");?></td>
                    
					<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['pre']['amount'],4,".","")>number_format($row['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($row['pre']['amount'],4);$GrandTotalPreValue+=number_format($row['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($row['pre']['amount']/$jobValue)*100,4); ?></td>
					<td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($row['mkt']['amount']-$row['pre']['amount'],2);$wash_booking_cost+=$row['mkt']['amount']-$row['pre']['amount']; ?></td>
                     <td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['acl']['amount']>0) echo fn_number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($row['acl']['amount'],4);$GrandTotalAclValue+=number_format($row['acl']['amount'],4,".",""); ?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($row['acl']['amount']/$jobValue)*100,4); ?></td>
					 <td width="100" align="right" title=" Pre Cost Value-Actual value"><? echo number_format($row['pre']['amount']-$row['acl']['amount'],2);$wash_post_cost+=$row['pre']['amount']-$row['acl']['amount']; ?></td>
					 <td width="100" align="right" title=" Marketing Value-Post Cost "><? echo number_format($row['mkt']['amount']-$row['acl']['amount'],2);$wash_total_cost+=$row['mkt']['amount']-$row['acl']['amount']; ?></td>
				</tr>
                <?
				$w++;
				$total_mkt_qty+=$row['mkt']['qty'];
				$total_mkt_amt+=$row['mkt']['amount'];
				$total_pre_qty+=$row['pre']['qty'];
				$total_pre_amt+=$row['pre']['amount'];
				$total_acl_qty+=$row['acl']['qty'];
				$total_acl_amt+=$row['acl']['amount'];
				}
				?>
                 <tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="washtotal"  class="adl-signs" onClick="yarnT(this.id,'.wash')">+</span>&nbsp;&nbsp;Wash Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo number_format($total_mkt_qty,4); ?></td>
					<td width="100" align="right"><? if($total_mkt_amt>0) echo number_format($total_mkt_amt/$total_mkt_qty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($total_mkt_amt,4); //$totalOtherMktValue+=number_format($total_mkt_amt,4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($total_pre_qty,4); ?></td>
					<td width="100" align="right"><? if($total_pre_amt>0) echo number_format($total_pre_amt/$total_pre_qty,4);else echo ""; ?></td>
					<td width="100" align="right"  title="If Acl Value(<?=$total_pre_amt;?>) greater than Mkt(<?=$total_mkt_amt;?>)"  bgcolor="<? if(number_format($total_pre_amt,4,".","")>number_format($total_mkt_amt,4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($total_pre_amt,4);//$totalOtherPreValue+=number_format($total_pre_amt,4,".",""); ?></td>
                      <td width="100" align="right"><? echo number_format(($total_pre_amt/$jobValue)*100,4).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($wash_booking_cost,2); ?></td>
					<td width="100" align="right" ><? echo number_format($total_acl_qty,4); ?></td>
					<td width="100" align="right"><? if($total_acl_amt>0) echo number_format($total_acl_amt/$total_acl_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_acl_amt,4,".","")>number_format($total_acl_amt,4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($total_acl_amt,4);//$totalOtherAclValue+=number_format($total_acl_amt,4,".",""); ?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo number_format(($total_acl_amt/$jobValue)*100,4); ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format($wash_post_cost,2); ?></td>
					 <td width="100" align="right" title=""><? echo number_format($wash_total_cost,2); ?></td>
				</tr>
                <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="300" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['mkt']['amount']>0) echo fn_number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo fn_number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=fn_number_format($embData['mkt']['amount'],4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo fn_number_format($embData['pre']['amount']/$embData['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($embData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                     <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($embData['mkt']['amount']-$embData['pre']['amount'],2);$emb_booking_cost+=$embData['mkt']['amount']-$embData['pre']['amount']; ?></td>
					<td width="100" align="right"><? echo fn_number_format($embData['acl']['qty'],4); ?></td>

					<td width="100" align="right"><? if($embData['acl']['amount']>0) echo fn_number_format($embData['acl']['amount']/$embData['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo fn_number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($embData['acl']['amount']*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($embData['pre']['amount']-$embData['acl']['amount'],2);$emb_post_cost+=$embData['pre']['amount']-$embData['acl']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($embData['mkt']['amount']-$embData['acl']['amount'],2);$emb_total_cost+=$embData['mkt']['amount']-$embData['acl']['amount']; ?></td>
				</tr>
                
                
                
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="300" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"  title="Amount/Qty"><? echo fn_number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo fn_number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo fn_number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=fn_number_format($commiData['pre']['amount'],4,".",""); ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($commiData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($commiData['mkt']['amount']-$commiData['pre']['amount'],2);$commi_booking_cost+=$commiData['mkt']['amount']-$commiData['pre']['amount']; ?></td>
					<td width="100" align="right"><? //echo $exfactoryQty ?></td>
					<td width="100" align="right"><? //$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty; echo number_format($aclCommiAmt/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? //echo number_format($aclCommiAmt,4); $totalOtherAclValue+=$aclCommiAmt; ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? //echo number_format(($aclCommiAmt*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=" "><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=" "><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="300" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? if($commaData['pre']['rate']) echo number_format($commaData['pre']['rate'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($commaData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo fn_number_format($commaData['mkt']['amount']-$commaData['pre']['amount'],2);$comma_booking_cost+=$commaData['mkt']['amount']-$commaData['pre']['amount']; ?></td>
					<td width="100" align="right"><?  echo $exfactoryQty; //$commer_act_cost=($unitPrice*$exfactoryQty)*(0.5/100); ?></td>
					<td width="100" align="right"><? if($commer_act_cost>0  && $exfactoryQty>0) echo  fn_number_format($commer_act_cost/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" align="right" title="" bgcolor="<? //if(number_format($exfactoryQty*$unitPrice)>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					$commer_act_cost=$commaData['acl']['amount'];
					echo number_format($commer_act_cost,4); $totalOtherAclValue+=fn_number_format($commer_act_cost,4,".",""); ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><?  echo fn_number_format(($commer_act_cost*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($commaData['mkt']['amount']-$commaData['acl']['amount'],2);$comma_post_cost+=$commaData['pre']['amount']-$commaData['acl']['amount']; ?></td>
					<td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($commaData['mkt']['amount']-$commaData['acl']['amount'],2);$comma_total_cost+=$commaData['mkt']['amount']-$commaData['acl']['amount']; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="300" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['freight']['amount']-$otherData['pre']['freight']['amount'],2);$other_booking_cost+=$otherData['mkt']['freight']['amount']-$otherData['pre']['freight']['amount']; ?></td>
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['freight']['amount']>0 && $exfactoryQty>0) echo fn_number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo fn_number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=fn_number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><?  echo fn_number_format(($otherData['acl']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title=" Pre Cost Value-Actual value"><? echo number_format($otherData['pre']['freight']['amount']-$otherData['acl']['freight']['amount'],2);$other_post_cost+=$otherData['pre']['freight']['amount']-$otherData['acl']['freight']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($otherData['mkt']['freight']['amount']-$otherData['acl']['freight']['amount'],2);$other_total_cost+=$otherData['mkt']['freight']['amount']-$otherData['acl']['freight']['amount']; ?></td>
                     
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="300" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo number_format(($otherData['pre']['lab_test']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo number_format($otherData['mkt']['lab_test']['amount']-$otherData['pre']['lab_test']['amount'],2);$other_lab_total_cost+=$otherData['acl']['lab_test']['amount']-$otherData['pre']['lab_test']['amount']; ?></td>
					<td width="100" align="right"><? //echo $labtest_qnty; ?></td>
					<td width="100" align="right"><? 
					//$test_act_cost=$unitPrice*$exfactoryQty*(0.05/100);
					if($test_act_cost>0  && $labtest_qnty>0) echo fn_number_format($test_act_cost/$labtest_qnty,4);else echo ""; ?></td>
					<td width="100" align="right" title="" bgcolor="<? //if(number_format($test_act_cost,4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					//$otherData['acl']['lab_test']['amount'];
					//$test_act_cost=$otherData['acl']['lab_test']['amount'];
					//echo number_format($test_act_cost,4); $totalOtherAclValue+=number_format($test_act_cost,4,".","");?><? $totalOtherAclValue+=number_format($labtest_qnty,4,".",""); echo $labtest_qnty; ?></td>	
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($test_act_cost*100/$jobValue),2).'%'; ?></td>	
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format($otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount'],2);$other_lab_total_cost+=$otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount'],2);$other_total_cost+=$otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount']; ?></td>			
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="300" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
                     <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['inspection']['amount']-$otherData['pre']['inspection']['amount'],2);$ins_booking_data+=$otherData['mkt']['inspection']['amount']-$otherData['pre']['inspection']['amount']; ?></td>
					<td width="100" align="right"><? echo $inspection_qnty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['inspection']['amount']>0  && $inspection_qnty>0) echo fn_number_format($otherData['acl']['inspection']['amount']/$inspection_qnty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo fn_number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format($otherData['pre']['inspection']['amount']-$otherData['acl']['inspection']['amount'],2);$ins_post_data+=$otherData['pre']['inspection']['amount']-$otherData['acl']['inspection']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost "><? echo number_format($otherData['mkt']['inspection']['amount']-$otherData['acl']['inspection']['amount'],2);$ins_total_data+=$otherData['mkt']['inspection']['amount']-$otherData['acl']['inspection']['amount']; ?></td>
				</tr>
				<tr bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="300" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>	
                     <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['currier_pre_cost']['amount']-$otherData['pre']['currier_pre_cost']['amount'],2);$curr_booking_cost+=$otherData['mkt']['currier_pre_cost']['amount']-$otherData['pre']['currier_pre_cost']['amount']; ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['currier_pre_cost']['amount']>0  && $exfactoryQty>0) echo fn_number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($otherData['acl']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($otherData['pre']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount'],2);$curr_post_cost+=$otherData['pre']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount'],2);$curr_total_cost+=$otherData['mkt']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount']; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="300" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? $otherData['mkt']['cm_cost']['qty']='';$otherData['mkt']['cm_cost']['qty']=$quaOfferQnty;
					echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount']/$otherData['mkt']['cm_cost']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['cm_cost']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['cm_cost']['amount']-$otherData['pre']['cm_cost']['amount'],2); $cm_booking_cost+=$otherData['mkt']['cm_cost']['amount']-$otherData['pre']['cm_cost']['amount']; ?></td>
					<?
					if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")) $font_color="white";
					else $font_color="black";
					?>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right" title="Actual Value/Actual Qty"><? if($otherData['acl']['cm_cost']['amount']>0  && $exfactoryQty>0) echo fn_number_format($otherData['acl']['cm_cost']['amount']/$exfactoryQty,4);else echo ""; $cm_acl_rate=$otherData['acl']['cm_cost']['amount']/$exfactoryQty; ?></td>

					<td width="100" title="<?= $otherData['acl']['cm_cost']['amount'] ?>"  align="right"  bgcolor="<? if(number_format($making_cost_act,4,".","")>number_format($otherData['acl']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=fn_number_format($otherData['acl']['cm_cost']['amount'],4,".",""); echo number_format($otherData['acl']['cm_cost']['amount'],4); ?>
                    </td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($making_cost_act*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value "><? echo fn_number_format($otherData['pre']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount'],2); $cm_post_cost+=$otherData['pre']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($otherData['mkt']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount'],2); $cm_total_cost+=$otherData['mkt']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount']; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" >Others Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($totalOtherMktValue,4); $GrandTotalMktValue+=fn_number_format($totalOtherMktValue,4,".",""); ?></td>
					
                    <td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? echo number_format($totalOtherPreValue,4); $GrandTotalPreValue+=fn_number_format($totalOtherPreValue,4,".",""); ?>
					</td>
                   <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($totalOtherPreValue*100/$jobValue),2).'%'; ?></td>
                   <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo fn_number_format($totalOtherMktValue-$totalOtherPreValue,2);$oth_booking_cost+=$totalOtherMktValue-$totalOtherPreValue; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? echo number_format($totalOtherAclValue,4); $GrandTotalAclValue+=fn_number_format($totalOtherAclValue,4,".",""); ?>
					</td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($totalOtherAclValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($totalOtherPreValue-$totalOtherAclValu,2);$oth_post_cost+=$totalOtherPreValue-$totalOtherAclValu; ?></td>
					<td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($totalOtherPreValue-$totalOtherAclValu,2);$oth_total_cost+=$totalOtherPreValue-$totalOtherAclValu; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="300" >Grand Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($GrandTotalPreValue*100/$jobValue),2).'%'; ?></td>
                     <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo fn_number_format($GrandTotalMktValue-$GrandTotalPreValue,2) ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($GrandTotalAclValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($GrandTotalPreValue-$GrandTotalAclValue,2) ?></td>
					<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo fn_number_format($GrandTotalMktValue-$GrandTotalAclValue,2) ?></td>
                    
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="300" >Shipment Value</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right"><? echo $quaOfferQnty;  ?></td>
					<td width="100" align="right" title="<? echo $quaPriceWithCommnPcs;?>"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right"><? $quaOfferValue=$quaOfferQnty*$quaPriceWithCommnPcs; echo number_format($quaOfferValue,4) ?></td>
					
                    <td width="100" align="right"><? echo $jobQty ?></td>
					<td width="100" align="right" title="<? echo $unitPrice;?>"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($quaOfferValue-$jobValue,2); ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right" title="<? echo $exfactoryValue/$exfactoryQty;?>"><? if( $exfactoryValue>0 && $exfactoryQty>0) echo number_format($exfactoryValue/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4) ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format(($jobValue-$exfactoryValue),2); ?></td>
					<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo number_format(($quaOfferValue-$exfactoryValue),2); ?></td>
				</tr>
			</table>
			<table style="width:120px;float: left; margin-left: 5px; margin-top: 10px;">
				<tr>
					
                    <?
                    $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in('$jobNumber') and form_name='knit_order_entry' and file_type=1");
					if(count( $nameArray_img)>0)
					{
					    ?>
						    
                            
							<? 
							foreach($nameArray_img AS $inf) { 
								?>
                                 <td width="120">
                                <div style="float:left;width:120px" >
								<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='75' width='95' />
                                 </td>
                               </div>
							    <?  
							}
							?>
							
						<?								
					}
					else echo ""; ?>						    
                   
				</tr>
            </table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="10"><p style="font-weight: bold;">Profit Summary</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"  title="Total Mkt Value/Offer Value*100"><? if($GrandTotalMktValue>0 && $quaOfferValue>0) echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right" title="Total Expand/Offer Value*100"><? if($yarnTrimMktValue>0) echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
                    <td width="150" align="right"><? echo number_format($total_mkt_amt,4) ?></td>
					<td width="100" align="right"  title="Total Mkt Wash/Offer Value*100"><? if($total_mkt_amt>0) echo number_format(($total_mkt_amt/$quaOfferValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Offer Value-Grand Total Mkt Value"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right" title="Offer Value-Grand Total Mkt Value/Offer Value*100"><? if($quaOfferValue-$GrandTotalMktValue>0) echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Total Pre Value/Job Value*100"><? if($GrandTotalPreValue>0) echo number_format(($GrandTotalPreValue/$jobValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? if($yarnTrimPreValue>0) echo number_format(($yarnTrimPreValue/$jobValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_pre_amt,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><? if($total_pre_amt>0) echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Job Value-Grand Total Pre Value"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Job Value-Grand Total Pre Value/Ex-Fact Value*100"><? if($jobValue-$GrandTotalPreValue>0) echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);else echo "";?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Total Actual Value/Ex-Fact Value*100"><? if($GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Ex-fact Value*10"><? if($yarnTrimAclValue>0) echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_acl_amt,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Ex-Fact Value*100"><? if($total_acl_amt>0) echo number_format(($total_acl_amt/$exfactoryValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value-Grand Total Acl Value"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Ex-Fact Value-Grand Total Ex Value/Ex-Fact Value*100"><? if($exfactoryValue-$GrandTotalAclValue>0) echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);else echo "";?></td>
				</tr>
			</table>
            <br>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="11"><p style="font-weight: bold;">Profit Summary 1 Pcs</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">FOB/Pcs</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    <td width="100">CM Cost</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right" title="Mkt Value/Mkt Qty"><? $tot_offer_val_pcs=$quaOfferValue/$quaOfferQnty;//$tot_offer_val_pcs=$unitPrice;
					echo number_format($tot_offer_val_pcs,4) ?></td>
					<td width="150"  title="Grand Total Mkt Value/Job Qty" align="right"><? $tot_mkt_val_pcs=$GrandTotalMktValue/$jobQty;echo number_format($tot_mkt_val_pcs,4); ?></td>
					<td width="100" align="right"  title="Total Expenditure/FOB Pcs*100"><? if($tot_mkt_val_pcs>0) echo number_format(($tot_mkt_val_pcs/$tot_offer_val_pcs)*100,4); else echo "";?></td>
					<td width="150" align="right" title="Total Trims Mkt value(<? echo $yarnTrimMktValue?>)/Job Qty(<? echo $jobQty?>)"><? $tot_trim_val_pcs=$yarnTrimMktValue/$jobQty;echo number_format($tot_trim_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Trims(<? echo $tot_trim_val_pcs?>)/FOB Pcs(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_trim_val_pcs>0) echo number_format(($tot_trim_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                    <td width="150" align="right" title="Total Mkt Wash(<? echo $total_mkt_amt?>)/Job Qty(<? echo $jobQty?>)"><? $tot_wash_val_pcs=$total_mkt_amt/$jobQty; echo number_format($tot_wash_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash/Offer Value*100"><? if($tot_wash_val_pcs>0) echo number_format(($tot_wash_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Cost(<? echo $otherData['mkt']['cm_cost']['amount'];?>)/Job Qty"><? $tot_mkt_cm_pcs=$otherData['mkt']['cm_cost']['amount']/$jobQty; echo number_format($tot_mkt_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Qua.Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $profit_mkt_margin_pcs=($quaOfferValue-$GrandTotalMktValue)/$jobQty;echo number_format($profit_mkt_margin_pcs,4); ?></td>
					<td width="100" align="right" title="Profit margin(<? echo $profit_mkt_margin_pcs;?>)/Mkt FOB/Pcs (<? echo $tot_offer_val_pcs;?>)*100)"><? if($profit_mkt_margin_pcs>0) echo number_format(($profit_mkt_margin_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right" title="Pre Cost/Job Qty"><? $tot_preJob_value=$unitPrice;//$jobValue/$jobQty;
					echo number_format($tot_preJob_value,4) ?></td>
					<td width="150" align="right" title="Pre Cost/JobQty"><? $tot_pre_costing_expnd=$GrandTotalPreValue/$jobQty; echo number_format($tot_pre_costing_expnd,4);?></td>
					<td width="100" align="right"  title="Total Expenditure/Job Value*100"><? 
					if($tot_pre_costing_expnd>0)
					{
						$tot_pre_costing_expnd_per=$tot_pre_costing_expnd/$tot_preJob_value*100;
					}
					else {$tot_pre_costing_expnd_per=0;}
					echo number_format($tot_pre_costing_expnd_per,4); ?></td>
					<td width="150" align="right"  title="Total Trims Value/Job Qty"><? $tot_preTrim_costing=$yarnTrimPreValue/$jobQty;echo number_format($tot_preTrim_costing,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? 
					if($tot_preTrim_costing>0)
					{
						$tot_preTrim_costing_per=($tot_preTrim_costing/$tot_preJob_value)*100;
					}
					else {$tot_preTrim_costing_per=0;}
					echo number_format($tot_preTrim_costing_per,4); ?></td>
                     <td width="150" align="right" title="Pre Wash Cost/Job Qty"><?  $tot_preWash_value=$total_pre_amt/$jobQty;echo number_format($tot_preWash_value,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><?  $tot_preWash_value_pre=$tot_preWash_value/$tot_preJob_value*100;echo number_format($tot_preWash_value_pre,4); ?></td>
                     <td width="150" align="right"  title="Cm Cost(<? echo $otherData['pre']['cm_cost']['amount'];?>)/Job Qty"><? $tot_pre_cm_pcs=$otherData['pre']['cm_cost']['amount']/$jobQty; echo number_format($tot_pre_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Job Qty"><? $profit_pre_margin_pcs=($jobValue-$GrandTotalPreValue)/$jobQty;echo number_format($profit_pre_margin_pcs,4);?></td>
					<td width="100" align="right"  title="Profit Margin(<? echo $profit_pre_margin_pcs;?>)/Pre FOB Pcs (<? echo $tot_preJob_value;?>)*100)"><? echo number_format(($profit_pre_margin_pcs/$tot_preJob_value)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right" title="Acl Job Value/Job Qty"><? $tot_AclJob_valuePcs=$unitPrice;//$exfactoryValue/$jobQty;
					echo number_format($tot_AclJob_valuePcs,4) ?></td>
					<td width="150" align="right" align="right" title="Grand total Acl value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><? $tot_AclJob_ExpndPcs=$GrandTotalAclValue/$jobQty; echo number_format($tot_AclJob_ExpndPcs,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? if($tot_AclJob_ExpndPcs>0 && $tot_AclJob_valuePcs>0) echo number_format(($tot_AclJob_ExpndPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right"  title="Total Acl Trims Value/Job Qty"><? $tot_AcltrimValue=$yarnTrimAclValue/$jobQty;echo number_format($tot_AcltrimValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Job Value*100"><? if($tot_AcltrimValue>0) echo number_format(($tot_AcltrimValue/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? $tot_AclJobWashPcs=$total_acl_amt/$jobQty; echo number_format($tot_AclJobWashPcs,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Job Value*100"><? if($tot_AclJobWashPcs>0) echo number_format(($tot_AclJobWashPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Actual Price"><?  $tot_cmAcl_pcs=$cm_acl_rate; echo number_format($tot_cmAcl_pcs,4) ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value(<? echo $exfactoryValue;?>)-Grand Total Acl Value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><?  $profit_acl_margin_pcs=($exfactoryValue-$GrandTotalAclValue)/$jobQty; echo number_format($profit_acl_margin_pcs,4);?></td>

					<td width="100" align="right" title="Profit margin(<? echo $profit_acl_margin_pcs;?>)/Actual FOB/Pcs (<? echo $tot_AclJob_valuePcs;?>)*100)"><? if($profit_acl_margin_pcs>0) echo number_format(($profit_acl_margin_pcs/$tot_AclJob_valuePcs)*100,4);else echo "";?></td>
				</tr>
			</table>
             <br>
              <br>
		</div>
		<?
	}
	if(str_replace("'","",$cbo_costcontrol_source)==2) //Price Quotation
	{
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){ echo "Select Company"; die; }
		if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		$check_data=sql_select("select a.job_no from wo_po_details_master a,wo_pre_cost_mst f where  a.id=f.job_id  and a.company_name=$company_name and a.job_no_prefix_num like '$job_no' and a.garments_nature=3 $year_cond group by a.job_no");
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		//$chk_job_nos=$job_no;
		if($chk_job_nos!='') 
		{
			 $sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.inquiry_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and  a.id=c.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
			$result=sql_select($sql);
		}
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
		$shipArry=array(0,1,2);
		$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotationId=$row[csf('quotation_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$ShipDateArrall[date("d-m-Y",strtotime($row[csf('shipment_date')]))]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			/* if($row[csf('shiping_status')]==3) //Full
			{
			 $shiping_status=2; 
			}
			//echo $shiping_status.'D';
			$shipingStatus=$shiping_status; */
			$shipingStatus=$row[csf('shiping_status')];
			$shiping_status_id.=$row[csf('shiping_status')].',';
			$po_ids.=$row[csf('id')].',';$ref_no.=$row[csf('grouping')].',';
			$team_leaders.=$team_leader_library[$row[csf('team_leader')]].',';
			$dealing_marchants.=$dealing_merchant_array[$row[csf('dealing_marchant')]].',';
		}
		//	echo $quotationId.'D';

		$sql_sample="SELECT sum(d.required_qty) as qnty
			    FROM wo_po_details_master a, sample_development_mst c,sample_development_fabric_acc d
			    WHERE     a.status_active = 1
			         AND a.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         and d.sample_mst_id=c.id
			         AND c.quotation_id = a.id
			         AND d.form_type = 1
			         AND d.is_deleted = 0
			         AND d.status_active = 1
			         AND a.job_no='$jobNumber'

	       ";
	  //  echo "<pre>$sql_sample</pre>";
	    $res_sample=sql_select($sql_sample);
	    $sql_sample_fab="SELECT SUM (fin_fab_qnty) AS qnty
						  FROM wo_booking_dtls
						 WHERE     status_active = 1
						       AND is_deleted = 0
						       AND job_no='$jobNumber'
						       AND entry_form_id = 440";
		//echo "<pre>$sql_sample_fab</pre>";
	    $res_sample_fab=sql_select($sql_sample_fab);

	    $booking_sql3=sql_select("SELECT 
			         SUM (a.fin_fab_qnty) AS qnty
			        
			    FROM wo_booking_dtls a, wo_booking_mst b
			   WHERE     a.booking_no = b.booking_no
			         AND a.is_short = 2
			         AND b.booking_type = 4
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND a.job_no = '$jobNumber'
			
				 ");
	    $sample_booking_all_qnty=$res_sample[0][csf('qnty')]+$res_sample_fab[0][csf('qnty')]+$booking_sql3[0][csf('qnty')];
		 //print_r($ShipDateArrall);
		$shiping_status_id=rtrim($shiping_status_id,',');
		
		$shiping_status_idAll=array_unique(explode(",",$shiping_status_id));
		//print_r($shiping_status_idAll);
		$shiping_status=3;
		foreach($shiping_status_idAll as $sid)
		{
			//echo $sid."D,";
			if(in_array($sid,$shipArry))
			{
				$shiping_status=2;
			}
		}
		//echo $shiping_status;
		$team_leaders=rtrim($team_leaders,',');
		$team_leaders=implode(",",array_unique(explode(",",$team_leaders)));
		$dealing_marchants=rtrim($dealing_marchants,',');
		$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchants)));
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0) $po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; else $po_cond_for_in="";
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessValue=$shortExcessQty*($unitPrice);
	    //$quotationId=1;
		
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	    // Yarn ============================
		$totYarn=0;
		$fabPurArr=array(); $YarnData=array(); $qYarnData=array(); $knitData=array(); $finishData=array(); $washData=array(); $embData=array(); $trimData=array(); $commiData=array(); $otherData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	    //print_r($yarn_data_array);
		$sql_yarn="select count_id, copm_one_id, percent_one, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id, copm_one_id, percent_one, type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row)
		{
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		unset($data_arr_yarn);
		/*echo "<pre>";
		print_r($YarnData); die;*/
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		if($quotationId)
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			 // $sqlQc="select a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.inquery_id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			  $sqlQua="select a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQuot=sql_select($sqlQua);
			//print_r($dataQc);
			foreach($dataQuot as $rowQua)
			{
				$quaOfferQnty=$jobQty;
				$quaConfirmPrice=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer=$rowQua[csf('costing_per')];
				$quaCostingPerQty=0;
				if($quaCostingPer==1) $quaCostingPerQty=12;
				if($quaCostingPer==2) $quaCostingPerQty=1;
				if($quaCostingPer==3) $quaCostingPerQty=24;
				if($quaCostingPer==4) $quaCostingPerQty=36;
				if($quaCostingPer==5) $quaCostingPerQty=48;
				$quaCostingPerQty=1;
				
			}
			unset($dataQc);
		 //echo $quaOfferQnty; die;
						
			if($quotationId){
			$totMktcons=0; $totMktAmt=0;
			$sql = "select id, item_number_id, body_part_id, fabric_description,fab_nature_id, color_type_id, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='".$quotationId."' and fabric_source=2";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
			//$mktcons=($fabPur_row[csf('avg_cons')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($fabPur_row[csf('avg_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				//echo $fabPur_row[csf('avg_cons')].'='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
				$mktamt=$mktcons*$fabPur_row[csf('rate')];
				$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
				if($fabPur_row[csf('fab_nature_id')]==2){
					$fabPurArr[$fabStri]['mkt']['knit']['qty']+=$mktcons;
					$fabPurArr[$fabStri]['mkt']['knit']['amount']+=$mktamt;
				}
				if($fabPur_row[csf('fab_nature_id')]==3){
					$fabPurArr[$fabStri]['mkt']['woven']['qty']+=$mktcons;
					$fabPurArr[$fabStri]['mkt']['woven']['amount']+=$mktamt;
				}
			}
			
			 $sql_trim = "select id,  trim_group, cons_uom, total_cons, rate, amount, apvl_req, nominated_supp,status_active from wo_pri_quo_trim_cost_dtls  where quotation_id='".$quotationId."' and status_active=1";
			$trim_data_array=sql_select($sql_trim);
			foreach($trim_data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktcons=($row[csf('total_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
			// echo $row[csf('total_cons')].'Pri='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
				$trimData[$row[csf('trim_group')]]['mkt']['qty']+=$mktcons;
				$trimData[$row[csf('trim_group')]]['mkt']['amount']+=$mktamt;
				$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
			}
			unset($trim_data_array);
			//========Emblishmnet=====
			$sql_embl = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name !=3 and status_active=1";
			$embl_data_array=sql_select($sql_embl);
			foreach($embl_data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$embData['mkt']['qty']+=$mktcons;
				$embData['mkt']['amount']+=$mktamt;
			}
			unset($embl_data_array);
			 $sql_wash = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name =3 and status_active=1";
			$wash_data_array=sql_select($sql_wash);
			foreach($wash_data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
					
					//echo $mktcons.'='.$row[csf('cons_dzn_gmts')].'='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
					
				$washData[$index]['mkt']['qty']+=$mktcons;
				$washData[$index]['mkt']['amount']+=$mktamt;
			}
			unset($wash_data_array);
			$commi_sql = "select id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pri_quo_commiss_cost_dtls  where quotation_id='".$quotationId."' and status_active=1";
			$commi_data_array=sql_select($commi_sql);
			foreach($commi_data_array as $row){
		//$mktamt=($row[csf('commission_amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('commission_amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['qty']=$quaOfferQnty;
				$commiData['mkt']['amount']+=$mktamt;
				$commiData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
			unset($commi_data_array);
			 $commer_sql = "select id, item_id, rate, amount, status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id=".$quotationId." and status_active=1";
			$commer_data_array=sql_select($commer_sql);
			foreach($commer_data_array as $row){
		//$mktamt=($row[csf('amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktamt;
				if($mktam>0 && $quaOfferQnty>0)
				{
				$commaData['mkt']['rate']+=$mktamt/$quaOfferQnty;
				}
				else
				{
					$commaData['mkt']['rate']+=0;
				}
			}
			unset($commer_data_array);
			
			$other_sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh,design_pre_cost as design_cost ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id='".$quotationId."'";
			$other_data_array=sql_select($other_sql);
			foreach($other_data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['qty']=$quaOfferQnty;
				$otherData['mkt']['freight']['amount']=$freightAmt;
				$otherData['mkt']['freight']['rate']=$freightAmt/$quaOfferQnty;
	
				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['qty']=$quaOfferQnty;
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
				$otherData['mkt']['lab_test']['rate']=$labTestAmt/$quaOfferQnty;
	
				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['qty']=$quaOfferQnty;
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
				$otherData['mkt']['inspection']['rate']=$inspectionAmt/$quaOfferQnty;
				
				$design_costAmt=($row[csf('design_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['design_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['design_cost']['amount']=$design_costAmt;
				$otherData['mkt']['design_cost']['rate']=$design_costAmt/$quaOfferQnty;
	
				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['qty']=$quaOfferQnty;
	
				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
				$otherData['mkt']['currier_pre_cost']['rate']=$currierPreCostAmt/$quaOfferQnty;
	
				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
				$otherData['mkt']['cm_cost']['rate']=$cmCostAmt/$quaOfferQnty;
			}
		
			
		} //Qout Id End
			
			
		}
		
	 
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
	
		//echo $lot_tmp;
		//echo $knit_grey_amt; // and a.yarn_lot in(".$lot_tmp.")
		$fin_fab_trans_array=array();
		  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
		 	 sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, 
			 sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt,
			sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, 
			c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$wvn_fin_fab_trans_amt_acl=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			//$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['in']=$row[csf('in_qty')];
			//$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['out']=$row[csf('out_qty')];
			$tot_fin_fab_transfer_qty+=$row[csf('in_qty')]-$row[csf('out_qty')];
			$tot_trans_amt=$row[csf('in_amt')]-$row[csf('out_amt')];
			$wvn_fin_fab_trans_amt_acl+=$tot_trans_amt/$g_exchange_rate;
		}
		unset($result_fin_trans);
		$tot_wvn_fin_fab_transfer_cost=$wvn_fin_fab_trans_amt_acl;
		
		$trim_fab_trans_array=array();
		 $sql_trim_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity*a.rate ELSE 0 END) AS grey_in_amt,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity*a.rate ELSE 0 END) AS grey_out_amt
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_trim_trans=sql_select( $sql_trim_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_trim_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in_amt')]-$row[csf('grey_out_amt')])/$g_exchange_rate;
		}
		//echo $grey_fab_trans_amt_acl.'d';
		unset($result_trim_trans);
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
	
		$subconOutBillData="select b.order_id,
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
	
		 

	    	
	
		// Yarn End============================
		// Fabric Purch ============================
		$totPrecons=0; $totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		
		//print_r($fabPur);
		 $sql = "select id, job_no,item_number_id,uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2 and status_active=1 and is_deleted=0";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
			$fabDescArr[$fabPur_row[csf('id')]]['fabric_description']=$fabPur_row[csf('fabric_description')];
			$fabDescArr[$fabPur_row[csf('id')]]['uom']=$fabPur_row[csf('uom')];
			
			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['knit']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['woven']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['woven']['amount']+=$Preamt;
			}
		} 
		
		$sql = "select a.item_category, a.exchange_rate, b.grey_fab_qnty,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				$fabric_description=$fabDescArr[$fabPur_row[csf('fab_dtls_id')]]['fabric_description'];
				$fabStri=$fabric_description.'_'.$fabPur_row[csf('fab_dtls_id')];
				
				if($fabPur_row[csf('item_category')]==2){
					$fabPurArr[$fabStri]['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
					}
				}
				if($fabPur_row[csf('item_category')]==3){
					$fabPurArr[$fabStri]['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					//alc_woven_qnty
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
					}
				}
			}
		
		
	
		// Fabric AOP  End============================
		// Trim Cost ============================
		//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$trim= new trims($condition);
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	
		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			//echo $trimAmtArr[$row[csf('job_no')]][$row[csf('id')]].'d';
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		//print_r($trimData);
		
		$trimsRecArr=array();
		
		 
		  $sql_trim_wo = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.trim_group from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_trim_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=2  and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_wo=sql_select($sql_trim_wo);
		foreach($data_array_wo as $row){
			$index=$row[csf('trim_group')];
			$trimData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$trimData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		unset($data_array_wo);
		$general_item_issue_sql="select a.item_group_id as trim_group, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_id=d.id and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in(".implode(",",$poIdArr).") ";
			 $gen_result=sql_select($general_item_issue_sql);
			foreach($gen_result as $row)
            {
				$con_factor=$trim_groupArr[$row[csf('trim_group')]]['con_factor'];
				$gen_item_arr[$row[csf('trim_group')]]['issue_qty']+=$row[csf('cons_quantity')];
				$gen_item_arr[$row[csf('trim_group')]]['cons_amount']+=($row[csf('cons_amount')]*$con_factor)/$g_exchange_rate;
            }
			//print_r($gen_item_arr);
			foreach($gen_item_arr as $ind=>$value)
			{
			$trimData[$ind]['acl']['qty']+=$value['issue_qty'];
			$trimData[$ind]['acl']['amount']+=$value['cons_amount'];
		    }
		//print_r($trimsRecArr);
		unset($gen_result);
		
	
		//print_r($trimData);
	
		// Trim Cost End============================
		// Embl Cost ============================
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		
		 $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			if($row[csf('emb_name')]==3)
			{
				$washData[$index]['pre']['qty']+=$washQtyArr[$row[csf("job_no")]][$row[csf('id')]];
				$washData[$index]['pre']['amount']+=$washAmtArr[$row[csf("job_no")]][$row[csf('id')]];
			}
			else
			{
				$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
				$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
			}
		}
		//print_r($washData);	
		//echo $sql = "select a.item_category, a.exchange_rate, b.grey_fab_qnty, b.wo_qnty, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		 $sql_emb = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_emb=sql_select($sql_emb);
		foreach($data_array_emb as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
		}
		// Embl Cost End ============================
		// Wash Cost ============================
		
		 
	 $sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			$washData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$washData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		
		// Wash Cost End ============================
		// Commision Cost  ============================
		
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}
		
		// Commision Cost  End ============================
	
		// Commarcial Cost  ============================
		//$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			if($commaAmtArr[$jobNumber][$row[csf('id')]]>0 && $exfactory_qty>0)
			{
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;
			}
			else
			{
				$commaData['pre']['rate']+=0;
			}
		}
		
		// Commarcial Cost  End ============================
		// Other Cost  ============================
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id, lab_test,  inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost from  wo_pre_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;
	
			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;
	
			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;
	
			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;
	
			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			if($other_cost[$jobNumber]['cm_cost']>0)
			{
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
			}
			else
			{
				$otherData['pre']['cm_cost']['rate']=0;
			}
		}
		
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	
	 $sql_cm_cost="select jobNo, available_min, production_date from production_logicsoft where jobNo='".$jobNumber."' and is_self_order=1";
		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$production_date=change_date_format($row[csf('production_date')],'','',1);
			$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
			$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		}
	
		 $sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==5){
				//$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
			}
		}
		// Other Cost End ============================
		if($quotationId){
			$sql_item_summ="select commercial_cost from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$data_array=sql_select($sql_item_summ);
			foreach($data_array as $row){
				$mktCommaamt=($row[csf('commercial_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktCommaamt;
				$commaData['mkt']['rate']+=$mktCommaamt/$quaOfferQnty;
			}
		}
		/* echo '<pre>';
		print_r($commaData);die; */
		// Commarcial Cost  End ============================
			 
		ob_start();
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1300px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px" rules="all">
				<tr>
					<td width="100">Job Number</td>
					<td width="200"><? echo $jobNumber; ?></td>
					<td width="100">Buyer</td>
					<td width="200"><? echo $buyer_arr[$buyerName]; ?></td>
                    <td width="100">Internal Ref. No</td>
					<td width="100"><? $ref_nos=rtrim($ref_no,',');echo implode(",",array_unique(explode(",",$ref_nos))); ?></td>
					<td width="100">Style Ref. No</td>
					<td width="200"><? echo $styleRefno; ?></td>
					<td width="100">Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Order Number</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo number_format($jobValue,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td style="word-break:break-all;"><? 
					 echo implode(",",$ShipDateArrall);
					 ?></td>
					<td>Team Leader</td> 
                    <td style="word-break:break-all;"><? echo $team_leaders;?></td>
					<td>Dealing Marchant</td>
                    <td style="word-break:break-all;" colspan="3"><? echo $dealing_marchants;?></td>
					<td>Unit Price</td>
					<td align="right"><? echo number_format($unitPrice,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Garments Item</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Ship Qty</td>
					<td align="right"><? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Shipment Value</td>
					<td align="right"><? echo number_format($exfactoryValue,4); ?></td>
					<td>Short/Excess Qty</td>
					<td align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right" colspan="3"><? echo number_format($shortExcessValue,4); ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>
	
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px; margin-top:10px" rules="all">
				<thead>
                	<tr>
                    <td> </td>
                    <td> </td>
                     <td colspan="3" align="center" title="Price Quotation ID=<? echo $quotationId;?>"> <b>Price Quot. Cost</b></td>
                     <td colspan="4" align="center"> <b>Budget Cost</b></td>
                     <td colspan="4" align="center"> <b>Post Cost</b></td>
                    </tr>
					<tr align="center" style="font-weight:bold">
						<td width="300">Item Description</td>
						<td width="60">UOM</td>
						<td width="100">Marketing Qty</td>
						<td width="100">Marketing Price</td>
						<td width="100">Marketing Value</td>
                        
						<td width="100">Pre-Cost Qty</td>
						<td width="100">Pre-Cost Price</td>
						<td width="100">Pre-Cost Value</td>
                        <td width="100">Cont. % On FOB Value</td>
                        
						<td width="100">Actual Qty</td>
						<td width="100">Actual Price</td>
						<td width="100">Actual Value</td>
                        <td width="100">Cont. % On FOB Value</td>
					</tr>
				</thead>
				
				<?
				$GrandTotalMktValue=0; $GrandTotalPreValue=0; $GrandTotalAclValue=0; $yarnTrimMktValue=0; $yarnTrimPreValue=0; $yarnTrimAclValue=0; $totalMktValue=0; $totalPreValue=0; $totalAclValue=0; $totalMktQty=0; $totalPreQty=0; $totalAclQty=0;
				 
				  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				 
				?>
				
				<?
				  $f1=2;
				  $f=2;$mkt_amount=$pre_amount=$acl_amount=0;
				foreach($fabPurArr as $fab_index=>$row ){
					if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$fabric_description=explode("_",str_replace("'","",$fab_index));
					//$fabric_description=$fabPurArr[$fab_index]['mkt']['woven']['fabric_description'];
					$uom_name=$unit_of_measurement[$fabDescArr[$fabric_description[1]]['uom']];
				?>
				 <tr class="fbpur" bgcolor="<? echo $fab_bgcolor2;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $fab_bgcolor2;?>')" id="trfab_<? echo $f; ?>">
					<td width="300" title="Fabric-> <? echo $fab_index;?>" > <? echo $fabric_description[0];?></td>
					<td width="60"><? echo $uom_name;?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['mkt']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']/$fabPurArr[$fab_index]['mkt']['woven']['qty'],4);else echo " "; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount'],4); ?></td>
                    
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['pre']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount']/$fabPurArr[$fab_index]['pre']['woven']['qty'],4);else echo " "; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right" title="<?=$sample_booking_all_qnty?>">
						
							<a href="#" onClick="openmypage_sample_fab('<? echo implode("_",array_keys($poNumberArr)); ?>','<?=$jobNumber?>')">
								<? echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['qty']+$sample_booking_all_qnty,4) ?>
							</a>
						</td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['acl']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount']/$fabPurArr[$fab_index]['acl']['woven']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['acl']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
               <?
			   $f++;
			 	 $mkt_amount+=$fabPurArr[$fab_index]['mkt']['woven']['amount'];
				$pre_amount+=$fabPurArr[$fab_index]['pre']['woven']['amount'];
				$acl_amount+=$fabPurArr[$fab_index]['acl']['woven']['amount'];
				}
			   ?>
                
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						$fabPurMkt= number_format($mkt_amount,4,".","");
						echo number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($pre_amount,4,".","");
						$fabPurAcl= number_format($acl_amount,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor="<? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
                      <td width="100" align="right"><? echo number_format(($fabPurPre*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<?
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($fabPurAcl*100/$jobValue),2).'%'; ?></td>
				</tr>
				
	
				 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
			  <?
	
				  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;
				?>
				 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="300">Cost<? //echo $item_descrition ?></td>
						<td width="60"></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount'],4); ?></td>
                        
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        
						
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? if($grey_fab_trans_amt_acl>0) echo number_format($grey_fab_trans_amt_acl/$grey_fab_trans_qty_acl,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($grey_fab_trans_amt_acl,4); ?></a>&nbsp;<? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
                       <td width="100" align="right"><? echo number_format(($grey_fab_trans_amt_acl*100/$jobValue),2).'%'; ?></td>
					</tr>
				<?
				 $tt2=1;
				?>
				<tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="300">Finished Fabric Cost</td>
						<td width="60">Yds</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost>0) echo number_format($tot_wvn_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_wvn_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
                         <td width="100" align="right"><? echo number_format(($tot_wvn_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
					</tr>
					<?
	
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    <td width="100" align="right"><? //echo number_format(($tot_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"> <? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl>0) echo number_format(($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);else "";?></td>
					<td width="100" align="right">
						<?
						echo number_format($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl,4);
						$total_grey_fin_fab_transfer_actl_cost=$tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format((($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr>
					<td colspan="12" style="font-weight:bold" class="trims">Trims Cost</td>
				</tr>
				<?
				$t=1; $totalTrimMktValue=0; $totalTrimPreValue=0; $totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					if($index>0)
					{
						$item_descrition = $trim_groupArr[$index]['item_name'];
						$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
						$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
						$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
						if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr class="trims" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
							<td width="300"><? echo $item_descrition ?></td>
							<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
                            
							<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
							<td width="100" align="right"  bgcolor=" <? if(number_format($row['pre']['amount'],4,'.','')>number_format($row['mkt']['amount'],4,'.','')){echo "yellow";} else{echo "";}?>"><? echo number_format($row['pre']['amount'],4); ?></td>
                             <td width="100" align="right"><? echo number_format(($row['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                             
							<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
							<td width="100" align="right"><? if($row['acl']['amount']>0)  echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
							<td width="100" align="right" title="<? echo 'Act='.$row['acl']['amount'].', Pre='.$row['pre']['amount'];?>" bgcolor=" <? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}  else{echo " ";}?>"><? echo number_format($row['acl']['amount'],4); ?></td>
                            <td width="100" align="right"><? echo number_format(($row['acl']['amount']*100/$jobValue),2).'%'; ?></td>
						</tr>
						<?
						$t++;
					}
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300"><span id="trimstotal" class="adl-signs" onClick="yarnT(this.id,'.trims')">+</span>&nbsp;&nbsp Trims Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						echo number_format($totalTrimMktValue,4);
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($totalTrimPreValue*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if(number_format($totalTrimPreValue,4,".","")>number_format($totalTrimMktValue,4,".","")){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalTrimAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<?
				$totalOtherMktValue=0; $totalOtherPreValue=0; $totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 
				  <?
				$w=1;$total_mkt_qty=$total_mkt_amt=$total_pre_qty=$total_pre_amt=$total_acl_qty=$total_acl_amt=0;
                foreach($washData as $index=>$row ){
					$index=str_replace("'","",$index);
					$item_des=explode("_",$index);
					$item_des=implode(",",$item_des);
					/*$item_descrition = $trim_groupArr[$index]['item_name'];
					$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
					$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");*/
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr  class="wash" bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="300" ><? echo  $item_des;	?></td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['mkt']['amount']>0) echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); $GrandTotalMktValue+=number_format($row['mkt']['amount'],4,".","");?></td>
                    
					<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['pre']['amount']>0) echo number_format($row['pre']['amount']/$row['pre']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['pre']['amount'],4,".","")>number_format($row['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($row['pre']['amount'],4);$GrandTotalPreValue+=number_format($row['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format(($row['pre']['amount']/$jobValue)*100,4); ?></td>
                     <td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['acl']['amount']>0) echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($row['acl']['amount'],4);$GrandTotalAclValue+=number_format($row['acl']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($row['acl']['amount']/$jobValue)*100,4); ?></td>
				</tr>
                <?
				$w++;
				$total_mkt_qty+=$row['mkt']['qty'];
				$total_mkt_amt+=$row['mkt']['amount'];
				$total_pre_qty+=$row['pre']['qty'];
				$total_pre_amt+=$row['pre']['amount'];
				$total_acl_qty+=$row['acl']['qty'];
				$total_acl_amt+=$row['acl']['amount'];
				}
				?>
                 <tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="washtotal"  class="adl-signs" onClick="yarnT(this.id,'.wash')">+</span>&nbsp;&nbsp;Wash Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo number_format($total_mkt_qty,4); ?></td>
					<td width="100" align="right"><? if($total_mkt_amt>0) echo number_format($total_mkt_amt/$total_mkt_qty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($total_mkt_amt,4); //$totalOtherMktValue+=number_format($total_mkt_amt,4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($total_pre_qty,4); ?></td>
					<td width="100" align="right"><? if($total_pre_amt>0) echo number_format($total_pre_amt/$total_pre_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_pre_amt,4,".","")>number_format($total_pre_amt,4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($total_pre_amt,4);//$totalOtherPreValue+=number_format($total_pre_amt,4,".",""); ?></td>
                      <td width="100" align="right"><?  if($total_pre_amt>0)  echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                      
					<td width="100" align="right"><? echo number_format($total_acl_qty,4); ?></td>
                   
					<td width="100" align="right"><? if($total_acl_amt>0) echo number_format($total_acl_amt/$total_acl_qty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_acl_amt,4,".","")>number_format($total_acl_amt,4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($total_acl_amt,4);//$totalOtherAclValue+=number_format($total_acl_amt,4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($total_acl_amt/$jobValue)*100,4); ?></td>
				</tr>
                <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="300" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['mkt']['amount']>0) echo number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=number_format($embData['mkt']['amount'],4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['pre']['amount']>0) echo number_format($embData['pre']['amount']/$embData['pre']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100" align="right"><? echo number_format($embData['acl']['qty'],4); ?></td>

					<td width="100" align="right"><? if($embData['acl']['amount']) echo number_format($embData['acl']['amount']/$embData['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['acl']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
                
                
                
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="300" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=number_format($commiData['pre']['amount'],4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commiData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? //echo $exfactoryQty ?></td>
					<td width="100" align="right"><? //$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty; echo number_format($aclCommiAmt/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? //echo number_format($aclCommiAmt,4); $totalOtherAclValue+=$aclCommiAmt; ?></td>
                    <td width="100" align="right"><? //echo number_format(($aclCommiAmt*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="300" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($commaData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; //$commer_act_cost=($unitPrice*$exfactoryQty)*(0.5/100); ?></td>
					<td width="100" align="right"><?  if($commaData['acl']['amount']>0 && $exfactoryQty>0) echo  number_format($commaData['acl']['amount']/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" align="right" title="" bgcolor="<? //if(number_format($exfactoryQty*$unitPrice)>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					$commer_act_cost=$commaData['acl']['amount'];
					echo number_format($commer_act_cost,4); $totalOtherAclValue+=number_format($commer_act_cost,4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commer_act_cost*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="300" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['freight']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                     
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="300" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['ore']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty ?></td>
					<td width="100" align="right"><? 
					$test_act_cost=$otherData['acl']['lab_test']['amount'];
					//$test_act_cost=$unitPrice*$exfactoryQty*(0.05/100);
					 if($test_act_cost>0 && $exfactoryQty>0) echo number_format($test_act_cost/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" title="" bgcolor="<? //if(number_format($test_act_cost,4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					
					echo number_format($test_act_cost,4); $totalOtherAclValue+=number_format($test_act_cost,4,".","");?></td>	
                     <td width="100" align="right"><? echo number_format(($test_act_cost*100/$jobValue),2).'%'; ?></td>				
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="300" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? if($otherData['acl']['inspection']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['inspection']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
				</tr>
				<tr bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="300" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['currier_pre_cost']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="300" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['cm_cost']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<?
					if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")) $font_color="white";
					else $font_color="black";
					$making_cost_act=$otherData['pre']['cm_cost']['rate']*$exfactoryQty*(80/100); 
					?>
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? if($making_cost_act>0 && $exfactoryQty>0) echo number_format($making_cost_act/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" title="(Pre Cost Rate(<? echo $otherData['pre']['cm_cost']['rate'];?>)*Ex Fact Qty(<? echo $exfactoryQty;?>)*80)/100)"  align="right"  bgcolor="<? if(number_format($making_cost_act,4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=number_format($making_cost_act,4,".",""); echo number_format($making_cost_act,4); ?>
                    </td>
                     <td width="100" align="right" title="Making Cost Actual/Job Value*100"><? echo number_format(($making_cost_act*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" >Others Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($totalOtherMktValue,4); $GrandTotalMktValue+=number_format($totalOtherMktValue,4,".",""); ?></td>
					
                    <td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? echo number_format($totalOtherPreValue,4); $GrandTotalPreValue+=number_format($totalOtherPreValue,4,".",""); ?>
					</td>
                   <td width="100" align="right"><? echo number_format(($totalOtherPreValue*100/$jobValue),2).'%'; ?></td>
                   
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? echo number_format($totalOtherAclValue,4); $GrandTotalAclValue+=number_format($totalOtherAclValue,4,".",""); ?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalOtherAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="300" >Grand Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
                     <td width="100" align="right"><? echo number_format(($GrandTotalPreValue*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
                    <td width="100" align="right"><? echo number_format(($GrandTotalAclValue*100/$jobValue),2).'%'; ?></td>
                    
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="300" >Shipment Value</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right"><? echo $quaOfferQnty;  ?></td>
					<td width="100" align="right" title="<? echo $quaPriceWithCommnPcs;?>"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right"><? $quaOfferValue=$quaOfferQnty*$quaPriceWithCommnPcs; echo number_format($quaOfferValue,4) ?></td>
					
                    <td width="100" align="right"><? echo $jobQty; ?></td>
					<td width="100" align="right" title="<? echo $unitPrice;?>"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
                    <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? echo $exfactoryQty ; ?></td>
					<td width="100" align="right" title="<? echo $exfactoryValue/$exfactoryQty;?>"><? if($exfactoryValue>0 && $exfactoryQty>0) echo number_format($exfactoryValue/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4); ?></td>
                      <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
				</tr>
			</table>
			<table style="width:120px;float: left; margin-left: 5px; margin-top: 10px;">
				<tr>
					
                    <?
                    $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in('$jobNumber') and form_name='knit_order_entry' and file_type=1");
					if(count( $nameArray_img)>0)
					{
					    ?>
						    
                            
							<? 
							foreach($nameArray_img AS $inf) { 
								?>
                                 <td width="120">
                                <div style="float:left;width:120px" >
								<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='75' width='95' />
                                 </td>
                               </div>
							    <?  
							}
							?>
							
						<?								
					}
					else echo ""; ?>						    
                   
				</tr>
            </table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="10"><p style="font-weight: bold;">Profit Summary</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"  title="Total Mkt Value/Offer Value*100"><? if($GrandTotalMktValue>0) echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right" title="Total Expand/Offer Value*100"><? if($yarnTrimMktValue>0 && $quaOfferValue>0) echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
                    <td width="150" align="right"><? echo number_format($total_mkt_amt,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash/Offer Value*100"><? if($total_mkt_amt>0 && $quaOfferValue>0) echo number_format(($total_mkt_amt/$quaOfferValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Offer Value-Grand Total Mkt Value"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right" title="Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Offer Value(<? echo $quaOfferValue;?>)*100" ><? if($quaOfferValue-$GrandTotalMktValue>0 && $quaOfferValue>0) echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Total Pre Value/Job Value*100"><? if($GrandTotalPreValue>0 && $jobValue>0) echo number_format(($GrandTotalPreValue/$jobValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? if($yarnTrimPreValue>0 && $jobValue>0) echo number_format(($yarnTrimPreValue/$jobValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_pre_amt,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><? if($total_pre_amt>0 && $jobValue>0) echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Job Value-Grand Total Pre Value"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Ex-Fact Value(<? echo $jobValue;?>)*100"><?  if($jobValue-$GrandTotalPreValue>0 && $jobValue>0)  echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);else echo "";?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Total Actual Value/Ex-Fact Value*100"><? if($GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Ex-fact Value*100"><?  if($yarnTrimAclValue>0 && $exfactoryValue>0) echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_acl_amt,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Ex-Fact Value*100"><? if($total_acl_amt>0 && $exfactoryValue>0) echo number_format(($total_acl_amt/$exfactoryValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value-Grand Total Acl Value"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Ex-fact Value(<? echo $exfactoryValue;?>)-GrandTotal Ex Value(<? echo $GrandTotalAclValue;?>)/Ex-Fact Value(<? echo $exfactoryValue;?>)*100"><? if($exfactoryValue-$GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);else echo "";?></td>
				</tr>
			</table>
            <br>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="11"><p style="font-weight: bold;">Profit Summary 1 Pcs……</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">FOB/Pcs</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    <td width="100">CM Cost</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right" title="Mkt Cost/Job Qty"><? $tot_offer_val_pcs=$unitPrice;//$quaOfferValue/$jobQty;
					echo number_format($tot_offer_val_pcs,4) ?></td>
					<td width="150" align="right" title="Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $tot_mkt_val_pcs=$GrandTotalMktValue/$jobQty;echo number_format($tot_mkt_val_pcs,4); ?></td>
					<td width="100" align="right"  title="Total Expenditure(<? echo $tot_mkt_val_pcs;?>)/FOB Pcs(<? echo $tot_offer_val_pcs;?>)*100"><? if($tot_mkt_val_pcs>0) echo number_format(($tot_mkt_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right" title="Total Trims Mkt value(<? echo $yarnTrimMktValue?>)/Job Qty(<? echo $jobQty?>)"><? 
					if($yarnTrimMktValue>0)
					{
					$tot_trim_val_pcs=$yarnTrimMktValue/$jobQty;
					}
					else $tot_trim_val_pcs=0;
					echo number_format($tot_trim_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Trims(<? echo $tot_trim_val_pcs?>)/FOB Pcs(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_trim_val_pcs>0) echo number_format(($tot_trim_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                    <td width="150" align="right" title="Total Mkt Trims(<? echo $total_mkt_amt?>)/Job Qty(<? echo $jobQty?>)"><? $tot_wash_val_pcs=$total_mkt_amt/$jobQty; echo number_format($tot_wash_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash(<? echo $tot_wash_val_pcs?>)/Offer Value(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_wash_val_pcs>0) echo number_format(($tot_wash_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Cost(<? echo $otherData['mkt']['cm_cost']['amount'];?>)/Job Qty"><? $tot_mkt_cm_pcs=$otherData['mkt']['cm_cost']['amount']/$jobQty; echo number_format($tot_mkt_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Qua.Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $profit_mkt_margin_pcs=($quaOfferValue-$GrandTotalMktValue)/$jobQty;echo number_format($profit_mkt_margin_pcs,4); ?></td>
					<td width="100" align="right" title="Profit margin(<? echo $profit_mkt_margin_pcs;?>)/Mkt FOB/Pcs (<? echo $tot_offer_val_pcs;?>)*100)"><? echo number_format(($profit_mkt_margin_pcs/$tot_offer_val_pcs)*100,4); ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right" title="Pre Cost/Job Qty"><? $tot_preJob_value=$unitPrice;//$jobValue/$jobQty;
					echo number_format($tot_preJob_value,4) ?></td>
					<td width="150" align="right"><? $tot_pre_costing_expnd=$GrandTotalPreValue/$jobQty; echo number_format($tot_pre_costing_expnd,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? $tot_pre_costing_expnd_per=$tot_pre_costing_expnd/$tot_preJob_value*100;echo number_format($tot_pre_costing_expnd_per,4); ?></td>
					<td width="150" align="right" title="Total Trims Value/Job Qty"><? $tot_preTrim_costing=$yarnTrimPreValue/$jobQty;echo number_format($tot_preTrim_costing,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? $tot_preTrim_costing_per=($tot_preTrim_costing/$tot_preJob_value)*100;echo number_format($tot_preTrim_costing_per,4); ?></td>
                     <td width="150" align="right" title="Pre Wash Cost/Job Qty"><?  $tot_preWash_value=$total_pre_amt/$jobQty;echo number_format($tot_preWash_value,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><?  $tot_preWash_value_pre=$tot_preWash_value/$tot_preJob_value*100;echo number_format($tot_preWash_value_pre,4); ?></td>
                     <td width="150" align="right" title="Cm Cost(<? echo $otherData['pre']['cm_cost']['amount'];?>)/Job Qty"><? $tot_pre_cm_pcs=$otherData['pre']['cm_cost']['amount']/$jobQty; echo number_format($tot_pre_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Job Qty"><? $profit_pre_margin_pcs=($jobValue-$GrandTotalPreValue)/$jobQty;echo number_format($profit_pre_margin_pcs,4);?></td>
					<td width="100" align="right" title="Profit Margin(<? echo $profit_pre_margin_pcs;?>)/Pre FOB Pcs (<? echo $tot_preJob_value;?>)*100)"><? echo number_format(($profit_pre_margin_pcs/$tot_preJob_value)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right" title="Acl Job Value/Job Qty"><? $tot_AclJob_valuePcs=$unitPrice;//$exfactoryValue/$jobQty;
					echo number_format($tot_AclJob_valuePcs,4) ?></td>
					<td width="150" align="right" title="Grand total Acl value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><? $tot_AclJob_ExpndPcs=$GrandTotalAclValue/$jobQty; echo number_format($tot_AclJob_ExpndPcs,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? if($tot_AclJob_ExpndPcs>0) echo number_format(($tot_AclJob_ExpndPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right"  title="Total Acl Trims Value/Job Qty"><? $tot_AcltrimValue=$yarnTrimAclValue/$jobQty;echo number_format($tot_AcltrimValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Job Value*100"><? if($tot_AcltrimValue>0) echo number_format(($tot_AcltrimValue/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? $tot_AclJobWashPcs=$total_acl_amt/$jobQty; echo number_format($tot_AclJobWashPcs,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Job Value*100"><? if($tot_AclJobWashPcs>0) echo number_format(($tot_AclJobWashPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="Making Cost(<? echo $making_cost_act;?>)/Job Qty"><?  $tot_cmAcl_pcs=$making_cost_act/$jobQty;; echo number_format($tot_cmAcl_pcs,4) ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value(<? echo $exfactoryValue;?>)-Grand Total Acl Value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><?  $profit_acl_margin_pcs=($exfactoryValue-$GrandTotalAclValue)/$jobQty; echo number_format($profit_acl_margin_pcs,4);?></td>

					<td width="100" align="right" title="Profit margin(<? echo $profit_acl_margin_pcs;?>)/Actual FOB/Pcs (<? echo $tot_AclJob_valuePcs;?>)*100)"><? if($profit_acl_margin_pcs>0) echo number_format(($profit_acl_margin_pcs/$tot_AclJob_valuePcs)*100,4);else echo "";?></td>
				</tr>
			</table>
             <br>
              <br>
		</div>
		<?
	}
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
	
	exit();
}

if($action=="report_generate2_old") //Not use
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo str_replace("'","",$cbo_costcontrol_source);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");


	//echo $cbo_costcontrol_source.'dd';

	if(str_replace("'","",$cbo_costcontrol_source)==1)
	{
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){ echo "Select Company"; die; }
		if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		$check_data=sql_select("select a.job_no from wo_po_details_master a,wo_pre_cost_mst f where  a.id=f.job_id  and a.company_name=$company_name and a.garments_nature=3 and a.job_no_prefix_num like '$job_no' $year_cond group by a.job_no");
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		//$chk_job_nos=$job_no;
		if($chk_job_nos!='') 
		{
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.inquiry_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and  a.id=c.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
			$result=sql_select($sql);
		}
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
		$shipArry=array(0,1,2);
		$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotation_id=$row[csf('quotation_id')];
			$quotationId=$row[csf('inquiry_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$ShipDateArrall[date("d-m-Y",strtotime($row[csf('shipment_date')]))]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			/* if($row[csf('shiping_status')]==3) //Full
			{
			 $shiping_status=2; 
			}
			//echo $shiping_status.'D';
			$shipingStatus=$shiping_status; */
			$shipingStatus=$row[csf('shiping_status')];
			$shiping_status_id.=$row[csf('shiping_status')].',';
			$po_ids.=$row[csf('id')].',';$ref_no.=$row[csf('grouping')].',';
			$team_leaders.=$team_leader_library[$row[csf('team_leader')]].',';
			$dealing_marchants.=$dealing_merchant_array[$row[csf('dealing_marchant')]].',';
		}

		$sql_inspection="SELECT sum(b.net_amount) as inspection_qnty FROM wo_po_details_master a, wo_inspection_dtls b WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and a.id=b.job_id and b.entry_form=605 AND a.job_no='$jobNumber'";
		$data_inspection=sql_select($sql_inspection);
		foreach($data_inspection as $row){
			$inspection_qnty=$row[csf('inspection_qnty')];
		}
		unset($data_inspection);
		$sql_labtest="SELECT sum(b.wo_value) as labtest_qnty FROM wo_po_details_master a, wo_labtest_dtls b WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and a.job_no=b.job_no  and b.entry_form=274 AND a.job_no='$jobNumber'";
		$data_labtest=sql_select($sql_labtest);
		foreach($data_labtest as $row){
			$labtest_qnty=$row[csf('labtest_qnty')];
		}
		unset($data_labtest);
		  $sql_sample="SELECT sum(d.required_qty) as qnty
			    FROM wo_po_details_master a, sample_development_mst c,sample_development_fabric_acc d
			    WHERE     a.status_active = 1
			         AND a.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         and d.sample_mst_id=c.id and c.entry_form_id=117 and a.style_ref_no=c.style_ref_no
			         AND c.quotation_id = a.id
			         AND d.form_type = 1
			         AND d.is_deleted = 0
			         AND d.status_active = 1
			         AND a.job_no='$jobNumber'

	       ";

	  //  echo "<pre>$sql_sample</pre>";
	    $res_sample=sql_select($sql_sample);
	     $sql_sample_fab="SELECT SUM (fin_fab_qnty) AS qnty
						  FROM wo_booking_dtls
						 WHERE     status_active = 1
						       AND is_deleted = 0
						       AND job_no='$jobNumber'
						       AND entry_form_id = 440";
		//echo "<pre>$sql_sample_fab</pre>";
	    $res_sample_fab=sql_select($sql_sample_fab);

	    $booking_sql3=sql_select("SELECT 
			         SUM (a.fin_fab_qnty) AS qnty
			        
			    FROM wo_booking_dtls a, wo_booking_mst b
			   WHERE     a.booking_mst_id = b.id
			         AND a.is_short = 2
			         AND b.booking_type = 4
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND a.job_no = '$jobNumber'
			
				 ");
				
	    $sample_booking_all_qnty=$res_sample[0][csf('qnty')]+$res_sample_fab[0][csf('qnty')]+$booking_sql3[0][csf('qnty')];
		 //print_r($ShipDateArrall);
		$shiping_status_id=rtrim($shiping_status_id,',');
		
		$shiping_status_idAll=array_unique(explode(",",$shiping_status_id));
		//print_r($shiping_status_idAll);
		$shiping_status=3;
		foreach($shiping_status_idAll as $sid)
		{
			//echo $sid."D,";
			if(in_array($sid,$shipArry))
			{
				$shiping_status=2;
			}
		}
		//echo $shiping_status;
		$team_leaders=rtrim($team_leaders,',');
		$team_leaders=implode(",",array_unique(explode(",",$team_leaders)));
		$dealing_marchants=rtrim($dealing_marchants,',');
		$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchants)));
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0) $po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; else $po_cond_for_in="";
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		//$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessQty=$exfactoryQty-array_sum($poQtyArr);
		$shortExcessValue=$shortExcessQty*($unitPrice);
	    //$quotationId=1;
		
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	    // Yarn ============================
		$totYarn=0;
		$fabPurArr=array(); $YarnData=array(); $qYarnData=array(); $knitData=array(); $finishData=array(); $washData=array(); $embData=array(); $trimData=array(); $commiData=array(); $otherData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	    //print_r($yarn_data_array);
		$sql_yarn="select count_id, copm_one_id, percent_one, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id, copm_one_id, percent_one, type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row)
		{
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		unset($data_arr_yarn);
		/*echo "<pre>";
		print_r($YarnData); die;*/
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		if($quotationId)
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			   $sqlQc="select a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.inquery_id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQc=sql_select($sqlQc);
			//print_r($dataQc);
			foreach($dataQc as $qcrow)
			{
				//echo $qcrow[csf('offer_qty')].'=';
				$quaOfferQnty=$jobQty;//$qcrow[csf('offer_qty')];
				$quaConfirmPrice=$qcrow[csf('confirm_fob')];
				$quaConfirmPriceDzn=$qcrow[csf('confirm_fob')];
				$quaPriceWithCommnPcs=$qcrow[csf('confirm_fob')];
				$quaCostingPer=$qcrow[csf('costing_per')];
				$qc_no=$qcrow[csf('qc_no')];
				$quaCostingPerQty=0; 
				//if($quaCostingPer==1) 
				$quaCostingPerQty=1;
			}
			unset($dataQc);
			$qc_no=$quotation_id;//issue id= 76,Yr-24
			$sql_cons_rate="select id, mst_id, fab_id,item_id, type,description, particular_type_id, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where status_active=1 and is_deleted=0 and mst_id =".$qc_no." order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate);
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				if($rowConsRate[csf("type")]==1) //Fabric
				{
					if(($rowConsRate[csf("rate")]*1)>0)
					{
						$yarnrate=$edata[3]+$edata[7]+$edata[11];
						$consQnty=($rowConsRate[csf("tot_cons")]/12)*($quaOfferQnty);
						$amount=$consQnty*$yarnrate;
						$index="";
						$index=$edata[0].','.$edata[4].','.$edata[8];						
						$index=""; $mktcons=$mktamt=0;
						$index="'".$rowConsRate[csf('particular_type_id')]."'";
						$mktcons=$mktamt=0;						
						$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
						$mktamt=$mktcons*$rowConsRate[csf('rate')];						
						$fabStri=$rowConsRate[csf('description')].'_'.$rowConsRate[csf('fab_id')];
						$fabPurArr[$fabStri]['mkt']['woven']['fabric_description']=$rowConsRate[csf('description')];
						$fabPurArr[$fabStri]['mkt']['woven']['qty']+=$mktcons;
						$fabPurArr[$fabStri]['mkt']['woven']['amount']+=$mktamt;
					}
					
				}
				//print_r($fabPurArr);
				if($rowConsRate[csf("type")]==3 ) //Wash
				{
					$index="'".$emblishment_wash_type_arr[$rowConsRate[csf("particular_type_id")]]."'";
					
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					if($rowConsRate[csf('rate')]>0)
					{
					$washData[$index]['mkt']['qty']+=$mktcons;
					$washData[$index]['mkt']['amount']+=$ConsRate*$mktcons;
					}
				}
				if($rowConsRate[csf("type")]==2)
				{
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					$embData['mkt']['qty']+=$mktcons;
					if($ConsRate && $mktcons)
					{
						$embData['mkt']['amount']+=$ConsRate*$mktcons;
					}
					
					
				}
				if($rowConsRate[csf("type")]==4) //Acceoosries
				{
					$mktcons=$mktamt=0;
					$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$rowConsRate[csf('rate')];
					$trimData[$rowConsRate[csf('particular_type_id')]]['mkt']['qty']+=$mktcons;
					$trimData[$rowConsRate[csf('particular_type_id')]]['mkt']['amount']+=$mktamt;
					$trimData[$rowConsRate[csf('particular_type_id')]]['cons_uom']=$rowConsRate[csf('cons_uom')];
				}
			}

			$sql_item_summ="select mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost,courier_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs,commercial_cost from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$sql_result_item_summ=sql_select($sql_item_summ);
			foreach($sql_result_item_summ as $rowItemSumm)
			{
				$consQnty=$amount=$mktamt=0;
				$mktamt=($rowItemSumm[csf("commission_cost")]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['amount']+=$mktamt;
				$freightAmt=($rowItemSumm[csf('frieght_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['amount']+=$freightAmt;
				$labTestAmt=($rowItemSumm[csf('lab_test_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['amount']+=$labTestAmt;
				$courier_costAmt=($rowItemSumm[csf('courier_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['amount']+=$courier_costAmt;
				$cmCostAmt=($rowItemSumm[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['amount']+=$cmCostAmt;
			}
		}

		
		
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
		
		$fin_fab_trans_array=array();
		  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
		 	 sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, 
			 sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt,
			sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, 
			c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$wvn_fin_fab_trans_amt_acl=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			$tot_fin_fab_transfer_qty+=$row[csf('in_qty')]-$row[csf('out_qty')];
			$tot_trans_amt=$row[csf('in_amt')]-$row[csf('out_amt')];
			$wvn_fin_fab_trans_amt_acl+=$tot_trans_amt/$g_exchange_rate;
		}
		unset($result_fin_trans);
		$tot_wvn_fin_fab_transfer_cost=$wvn_fin_fab_trans_amt_acl;
		
		$trim_fab_trans_array=array();
		$sql_trim_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity*a.rate ELSE 0 END) AS grey_in_amt,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity*a.rate ELSE 0 END) AS grey_out_amt
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_trim_trans=sql_select( $sql_trim_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_trim_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in_amt')]-$row[csf('grey_out_amt')])/$g_exchange_rate;
		}
		//echo $grey_fab_trans_amt_acl;
		unset($result_trim_trans);
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
	
		$subconOutBillData="select b.order_id,
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
		$totPrecons=0; $totPreAmt=0;
		//echo $fabric->getQuery();die;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		
		//print_r($fabPur);
		  $sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id,uom, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2 and status_active=1 and is_deleted=0";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
		//	$fabStri=$fabPur_row[csf('id')];
			$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
			$fabDescArr[$fabPur_row[csf('id')]]['fabric_description']=$fabPur_row[csf('fabric_description')];
			//$fabPurArr[$fabStri]['pre']['knit']['qty']=$fabPur_row[csf('uom')];
			$fabDescArr[$fabPur_row[csf('id')]]['uom']=$fabPur_row[csf('uom')];
			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				
				$fabPurArr[$fabStri]['pre']['knit']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['woven']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['woven']['amount']+=$Preamt;
			}
		} 
		
		 $sql = "select a.item_category, a.exchange_rate, b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,b.uom,b.grey_fab_qnty, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				
				$fabric_description=$fabDescArr[$fabPur_row[csf('fab_dtls_id')]]['fabric_description'];
				$fabStri=$fabric_description.'_'.$fabPur_row[csf('fab_dtls_id')];
				if($fabPur_row[csf('item_category')]==2){
					$fabPurArr[$fabStri]['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
					}
				}
				if($fabPur_row[csf('item_category')]==3){
					$fabPurArr[$fabStri]['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					//alc_woven_qnty
					//echo $fabPur_row[csf('grey_fab_qnty')].'f';
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
					}
				}
			}
		
		//	print_r($fabPurArr);
	
		// Fabric AOP  End============================
		// Trim Cost ============================
		//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$trim= new trims($condition);
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	
		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		//print_r($trimData);
		
		$trimsRecArr=array();
	
		$general_item_issue_sql="select a.item_group_id as trim_group, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_id=d.id and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in(".implode(",",$poIdArr).") ";
			 $gen_result=sql_select($general_item_issue_sql);
			foreach($gen_result as $row)
            {
				$con_factor=$trim_groupArr[$row[csf('trim_group')]]['con_factor'];
				$gen_item_arr[$row[csf('trim_group')]]['issue_qty']+=$row[csf('cons_quantity')];
				$gen_item_arr[$row[csf('trim_group')]]['cons_amount']+=($row[csf('cons_amount')]*$con_factor)/$g_exchange_rate;
            }
			//print_r($gen_item_arr);
			foreach($gen_item_arr as $ind=>$value)
			{
			$trimData[$ind]['acl']['qty']+=$value['issue_qty'];
			$trimData[$ind]['acl']['amount']+=$value['cons_amount'];
		    }
		//print_r($trimsRecArr);
		unset($gen_result);
		
		 
		 $sql_trim_wo = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.trim_group from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_trim_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=2  and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_wo=sql_select($sql_trim_wo);
		foreach($data_array_wo as $row){
			$index=$row[csf('trim_group')];
			$trimData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$trimData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		unset($data_array_wo);
	
		//print_r($trimData);
	
		// Trim Cost End============================
		// Embl Cost ============================
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		
		 $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			if($row[csf('emb_name')]==3)
			{
				$washData[$index]['pre']['qty']+=$washQtyArr[$row[csf("job_no")]][$row[csf('id')]];
				$washData[$index]['pre']['amount']+=$washAmtArr[$row[csf("job_no")]][$row[csf('id')]];
			}
			else
			{
				$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
				$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
			}
		}
		 $sql_emb = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_emb=sql_select($sql_emb);
		foreach($data_array_emb as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
		}
		// Embl Cost End ============================
		// Wash Cost ============================
		
		 
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			$washData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$washData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		
		// Wash Cost End ============================
		// Commision Cost  ============================
		
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}
		
		// Commision Cost  End ============================
	
		// Commarcial Cost  ============================
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			if($commaAmtArr[$jobNumber][$row[csf('id')]]>0 && $exfactory_qty>0)
			{
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;
			}
			else
			{
				$commaData['pre']['rate']+=0;
			}
			
		}
		
		// Commarcial Cost  End ============================
		// Other Cost  ============================
		$other_cost=$other->getAmountArray_by_job();
		 $sql = "select id, lab_test,  inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost from  wo_pre_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;
	
			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;
	
			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;
	
			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;
	
			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
		}
		
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	
	 /* $sql_cm_cost="select jobNo, available_min, production_date from production_logicsoft where jobNo='".$jobNumber."' and is_self_order=1";
		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$production_date=change_date_format($row[csf('production_date')],'','',1);
			$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
			$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		} */
	
		$sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==5){
				$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
			}
		}
		// Other Cost End ============================
		if($quotationId){
			$sql_item_summ="select commercial_cost from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$data_array=sql_select($sql_item_summ);
			foreach($data_array as $row){
				$mktCommaamt=($row[csf('commercial_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktCommaamt;
				$commaData['mkt']['rate']+=$mktCommaamt/$quaOfferQnty;
			}
		}
		/* echo '<pre>';
		print_r($commaData);die; */
		// Commarcial Cost  End ============================
		ob_start();
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1300px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px" rules="all">
				<tr>
					<td width="100">Job Number</td>
					<td width="200"><? echo $jobNumber; ?></td>
					<td width="100">Buyer</td>
					<td width="200"><? echo $buyer_arr[$buyerName]; ?></td>
                    <td width="100">Internal Ref. No</td>
					<td width="100"><? $ref_nos=rtrim($ref_no,',');echo implode(",",array_unique(explode(",",$ref_nos))); ?></td>
					<td width="100">Style Ref. No</td>
					<td width="200"><? echo $styleRefno; ?></td>
					<td width="100">Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Order Number</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo number_format($jobValue,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td style="word-break:break-all;"><? 
					 echo implode(",",$ShipDateArrall);
					 ?></td>
					<td>Team Leader</td> 
                    <td style="word-break:break-all;"><? echo $team_leaders;?></td>
					<td>Dealing Marchant</td>
                    <td style="word-break:break-all;" colspan="3"><? echo $dealing_marchants;?></td>
					<td>Unit Price</td>
					<td align="right"><? echo number_format($unitPrice,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Garments Item</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Ship Qty</td>
					<td align="right"><? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Shipment Value</td>
					<td align="right"><? echo number_format($exfactoryValue,4); ?></td>
					<td>Short/Excess Qty</td>
					<td align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right" colspan="3"><? echo number_format($shortExcessValue,4); ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>
	
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px; margin-top:10px" rules="all">
				<thead>
                	<tr>
                    <td> </td>
                    <td> </td>
                     <td colspan="3" align="center"> <b>Buyer Cost</b></td>
                     <td colspan="5" align="center"> <b>Budget Cost</b></td>
                     <td colspan="5" align="center"> <b>Post Cost</b></td>
					 <td rowspan="2" align="center"> <b>Total Profit/Loss</b></td>
                    </tr>
					<tr align="center" style="font-weight:bold">
						<td width="300">Item Description</td>
						<td width="60">UOM</td>
						<td width="100">Marketing Qty</td>
						<td width="100">Marketing Price</td>
						<td width="100">Marketing Value</td>
                        
						<td width="100">Pre-Cost Qty</td>
						<td width="100">Pre-Cost Price</td>
						<td width="100">Pre-Cost Value</td>
                        <td width="100">Cont. % On FOB Value</td>
						<td width="100">Profit/Loss</td>
                        
						<td width="100">Actual Qty</td>
						<td width="100">Actual Price</td>
						<td width="100">Actual Value</td>
                        <td width="100">Cont. % On FOB Value</td>
						<td width="100">Profit/Loss</td>
					</tr>
				</thead>
				
				<?
				$GrandTotalMktValue=0; $GrandTotalPreValue=0; $GrandTotalAclValue=0; $yarnTrimMktValue=0; $yarnTrimPreValue=0; $yarnTrimAclValue=0; $totalMktValue=0; $totalPreValue=0; $totalAclValue=0; $totalMktQty=0; $totalPreQty=0; $totalAclQty=0;
				 
				  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				 
				?>
				
				<?
				  $f=2;$mkt_amount=$pre_amount=$acl_amount=0;
				foreach($fabPurArr as $fab_index=>$row ){
					if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$fabric_description=explode("_",str_replace("'","",$fab_index));
					//$fabric_description=$fabPurArr[$fab_index]['mkt']['woven']['fabric_description'];
					$uom_name=$unit_of_measurement[$fabDescArr[$fabric_description[1]]['uom']];
				?>
				 <tr class="fbpur" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $bgcolor;?>')" id="trfab_<? echo $f; ?>">
					<td width="300" title="Fabric-> <? echo $fab_index;?>" > <? echo $fabric_description[0];?></td>
					<td width="60"><? echo $uom_name;?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['qty'],4) ?></td> 
					<td width="100" align="right"  title="Amount/Qty"><? echo fn_number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']/$fabPurArr[$fab_index]['mkt']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount'],4); ?></td>
                    
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right" title="Amount/Qty"><? echo fn_number_format($fabPurArr[$fab_index]['pre']['woven']['amount']/$fabPurArr[$fab_index]['pre']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo fn_number_format($fabPurArr[$fab_index]['pre']['woven']['amount'],4); ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format(($fabPurArr[$fab_index]['mkt']['woven']['amount'])-($fabPurArr[$fab_index]['pre']['woven']['amount']),2);$fab_total_budget_cost+=($fabPurArr[$fab_index]['mkt']['woven']['amount'])-($fabPurArr[$fab_index]['pre']['woven']['amount']); ?></td>
					<td width="100" align="right" title="SampleBookingQty=<?=$sample_booking_all_qnty?>">
						
							<a href="#" onClick="openmypage_sample_fab('<? echo implode("_",array_keys($poNumberArr)); ?>','<?=$jobNumber?>')">
								<? echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['qty']+$sample_booking_all_qnty,4) ?>
							</a>
						</td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['acl']['woven']['amount']>0) echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['amount']/$fabPurArr[$fab_index]['acl']['woven']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount'],4); ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($fabPurArr[$fab_index]['acl']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=" Pre Cost Value-Actual value"><? echo fn_number_format($fabPurArr[$fab_index]['pre']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount'],2);$fab_booking_cost+=$fabPurArr[$fab_index]['pre']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount']; ?></td>
					<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo fn_number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount'],2);$fab_total_cost+=$fabPurArr[$fab_index]['mkt']['woven']['amount']-$fabPurArr[$fab_index]['acl']['woven']['amount']; ?></td>
				</tr>
                <?
				$f++;
				$mkt_amount+=$fabPurArr[$fab_index]['mkt']['woven']['amount'];
				$pre_amount+=$fabPurArr[$fab_index]['pre']['woven']['amount'];
				$acl_amount+=$fabPurArr[$fab_index]['acl']['woven']['amount'];
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						$fabPurMkt= number_format($mkt_amount,4,".","");
						echo number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($pre_amount,4,".","");
						$fabPurAcl= number_format($acl_amount,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor="<? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
                      <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($fabPurPre*100/$jobValue),2).'%'; ?></td>
					  <td width="100" align="right" title="Market Value-Pre Cost Value" ><? echo number_format($fab_total_budget_cost,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<?
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
                     <td width="100" align="right"  title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($fabPurAcl*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value - Actual Value" ><? echo number_format($fab_booking_cost,2); ?></td>
					 <td width="100" align="right" title=""><? echo number_format($fab_total_cost,2); ?></td>
				</tr>
				
	
				 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
			  <?
	
				  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;
				?>
				 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="300"> Cost<? //echo $item_descrition ?></td>
						<td width="60"></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount'],4); ?></td>
                        
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
                        <td width="100" align="right" title="Pre Cost Value/JobValue*100">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right" title="Market Value-Pre Cost Value"><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
						
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? if($grey_fab_trans_amt_acl>0) echo fn_number_format($grey_fab_trans_amt_acl/$grey_fab_trans_qty_acl,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($grey_fab_trans_amt_acl,4); ?></a>&nbsp;<? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
                       <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($grey_fab_trans_amt_acl*100/$jobValue),2).'%'; ?></td>
					   <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					   <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					</tr>
				<?
				 $tt2=1;
				?>
				<tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="300">Finished Fabric Cost</td>
						<td width="60">Yds</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        <td width="100" align="right" title="Pre Cost Value/JobValue*100"></td>
                        <td width="100" align="right" title="Market Value-Pre Cost Value"><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost>0) echo fn_number_format($tot_wvn_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_wvn_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
                         <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($tot_wvn_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
						 <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
						 <td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					</tr>
					<?
	
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? //echo number_format(($tot_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl>0) echo number_format(($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);else echo "";?></td>
					<td width="100" align="right">
						<?
						echo number_format($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl,4);
						$total_grey_fin_fab_transfer_actl_cost=$tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo number_format((($tot_wvn_fin_fab_transfer_cost+$tot_grey_fab_cost_acl)*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=""><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr>
					<td colspan="12" style="font-weight:bold" class="trims">Trims Cost</td>
				</tr>
				<?
				$t=1; $totalTrimMktValue=0; $totalTrimPreValue=0; $totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					if($index>0)
					{
						$item_descrition = $trim_groupArr[$index]['item_name'];
						$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
						$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
						$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
						if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr class="trims" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
							<td width="300"><? echo $item_descrition ?></td>
							<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo fn_number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
                            
							<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
							<td width="100" align="right"><? echo fn_number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
							<td width="100" align="right"  bgcolor=" <? if(number_format($row['pre']['amount'],4,'.','')>number_format($row['mkt']['amount'],4,'.','')){echo "yellow";} else{echo "";}?>"><? echo number_format($row['pre']['amount'],4); ?></td>
                             <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($row['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                             <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($row['mkt']['amount']-$row['pre']['amount'],2);$trim_booking_cost+=$row['mkt']['amount']-$row['pre']['amount']; ?></td>
							<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
							<td width="100" align="right"><? if($row['acl']['amount']>0) echo fn_number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
							<td width="100" align="right" title="<? echo 'Act='.$row['acl']['amount'].', Pre='.$row['pre']['amount'];?>" bgcolor=" <? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}  else{echo " ";}?>"><? echo number_format($row['acl']['amount'],4); ?></td>
                            <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($row['acl']['amount']*100/$jobValue),2).'%'; ?></td>
							<td width="100" align="right" title="Pre Cost Value-Actual value"><? 
							echo number_format($row['pre']['amount']-$row['acl']['amount'],2);$trim_post_cost+=$row['pre']['amount']-$row['acl']['amount'];; ?></td>
							<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo fn_number_format($row['mkt']['amount']-$row['acl']['amount'],2);$trim_total_cost+=$row['mkt']['amount']-$row['acl']['amount']; ?></td>
						</tr>
						<?
						$t++;
					}
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300"><span id="trimstotal" class="adl-signs" onClick="yarnT(this.id,'.trims')">+</span>&nbsp;&nbspTrims Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						echo number_format($totalTrimMktValue,4);
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" title="If PrecostValue(<?=$totalTrimPreValue;?>) greater than Mkt(<?=$totalTrimMktValue;?>)" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".","");
						?>
					</td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($totalTrimPreValue*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($trim_booking_cost,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" title="If Acl Value(<?=$totalTrimAclValue;?>) greater than Mkt(<?=$totalTrimMktValue;?>)"  bgcolor=" <? if(number_format($totalTrimAclValue,4,".","")>number_format($totalTrimMktValue,4,".","")){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".","");
						?>
					</td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($totalTrimAclValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value - Actual Value"><? echo number_format($trim_post_cost,2); ?></td>
					<td width="100" align="right" title=""><? echo number_format($trim_total_cost,2); ?></td>
				</tr>
				<?
				$totalOtherMktValue=0; $totalOtherPreValue=0; $totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 
				  <?
				$w=1;$total_mkt_qty=$total_mkt_amt=$total_pre_qty=$total_pre_amt=$total_acl_qty=$total_acl_amt=0;
                foreach($washData as $index=>$row ){
					$index=str_replace("'","",$index);
					$item_des=explode("_",$index);
					$item_des=implode(",",$item_des);
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr  class="wash" bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="300" ><? echo  $item_des;	?></td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); $GrandTotalMktValue+=number_format($row['mkt']['amount'],4,".","");?></td>
                    
					<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['pre']['amount'],4,".","")>number_format($row['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($row['pre']['amount'],4);$GrandTotalPreValue+=number_format($row['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($row['pre']['amount']/$jobValue)*100,4); ?></td>
					<td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($row['mkt']['amount']-$row['pre']['amount'],2);$wash_booking_cost+=$row['mkt']['amount']-$row['pre']['amount']; ?></td>
                     <td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['acl']['amount']>0) echo fn_number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($row['acl']['amount'],4);$GrandTotalAclValue+=number_format($row['acl']['amount'],4,".",""); ?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($row['acl']['amount']/$jobValue)*100,4); ?></td>
					 <td width="100" align="right" title=" Pre Cost Value-Actual value"><? echo number_format($row['pre']['amount']-$row['acl']['amount'],2);$wash_post_cost+=$row['pre']['amount']-$row['acl']['amount']; ?></td>
					 <td width="100" align="right" title=" Marketing Value-Post Cost "><? echo number_format($$row['mkt']['amount']-$row['acl']['amount'],2);$wash_total_cost+=$row['mkt']['amount']-$row['acl']['amount']; ?></td>
				</tr>
                <?
				$w++;
				$total_mkt_qty+=$row['mkt']['qty'];
				$total_mkt_amt+=$row['mkt']['amount'];
				$total_pre_qty+=$row['pre']['qty'];
				$total_pre_amt+=$row['pre']['amount'];
				$total_acl_qty+=$row['acl']['qty'];
				$total_acl_amt+=$row['acl']['amount'];
				}
				?>
                 <tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="washtotal"  class="adl-signs" onClick="yarnT(this.id,'.wash')">+</span>&nbsp;&nbsp;Wash Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo number_format($total_mkt_qty,4); ?></td>
					<td width="100" align="right"><? if($total_mkt_amt>0) echo number_format($total_mkt_amt/$total_mkt_qty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($total_mkt_amt,4); //$totalOtherMktValue+=number_format($total_mkt_amt,4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($total_pre_qty,4); ?></td>
					<td width="100" align="right"><? if($total_pre_amt>0) echo number_format($total_pre_amt/$total_pre_qty,4);else echo ""; ?></td>
					<td width="100" align="right"  title="If Acl Value(<?=$total_pre_amt;?>) greater than Mkt(<?=$total_mkt_amt;?>)"  bgcolor="<? if(number_format($total_pre_amt,4,".","")>number_format($total_mkt_amt,4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($total_pre_amt,4);//$totalOtherPreValue+=number_format($total_pre_amt,4,".",""); ?></td>
                      <td width="100" align="right"><? echo number_format(($total_pre_amt/$jobValue)*100,4).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($wash_booking_cost,2); ?></td>
					<td width="100" align="right" ><? echo number_format($total_acl_qty,4); ?></td>
					<td width="100" align="right"><? if($total_acl_amt>0) echo number_format($total_acl_amt/$total_acl_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_acl_amt,4,".","")>number_format($total_acl_amt,4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($total_acl_amt,4);//$totalOtherAclValue+=number_format($total_acl_amt,4,".",""); ?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo number_format(($total_acl_amt/$jobValue)*100,4); ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format($wash_post_cost,2); ?></td>
					 <td width="100" align="right" title=""><? echo number_format($wash_total_cost,2); ?></td>
				</tr>
                <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="300" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['mkt']['amount']>0) echo fn_number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo fn_number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=fn_number_format($embData['mkt']['amount'],4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo fn_number_format($embData['pre']['amount']/$embData['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($embData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                     <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($embData['mkt']['amount']-$embData['pre']['amount'],2);$emb_booking_cost+=$embData['mkt']['amount']-$embData['pre']['amount']; ?></td>
					<td width="100" align="right"><? echo fn_number_format($embData['acl']['qty'],4); ?></td>

					<td width="100" align="right"><? if($embData['acl']['amount']>0) echo fn_number_format($embData['acl']['amount']/$embData['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo fn_number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($embData['acl']['amount']*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($embData['pre']['amount']-$embData['acl']['amount'],2);$emb_post_cost+=$embData['pre']['amount']-$embData['acl']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($embData['mkt']['amount']-$embData['acl']['amount'],2);$emb_total_cost+=$embData['mkt']['amount']-$embData['acl']['amount']; ?></td>
				</tr>
                
                
                
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="300" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"  title="Amount/Qty"><? echo fn_number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo fn_number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo fn_number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=fn_number_format($commiData['pre']['amount'],4,".",""); ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($commiData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($commiData['mkt']['amount']-$commiData['pre']['amount'],2);$commi_booking_cost+=$commiData['mkt']['amount']-$commiData['pre']['amount']; ?></td>
					<td width="100" align="right"><? //echo $exfactoryQty ?></td>
					<td width="100" align="right"><? //$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty; echo number_format($aclCommiAmt/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? //echo number_format($aclCommiAmt,4); $totalOtherAclValue+=$aclCommiAmt; ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? //echo number_format(($aclCommiAmt*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=" "><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title=" "><? //echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="300" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? if($commaData['pre']['rate']) echo number_format($commaData['pre']['rate'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($commaData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo fn_number_format($commaData['mkt']['amount']-$commaData['pre']['amount'],2);$comma_booking_cost+=$commaData['mkt']['amount']-$commaData['pre']['amount']; ?></td>
					<td width="100" align="right"><?  echo $exfactoryQty; //$commer_act_cost=($unitPrice*$exfactoryQty)*(0.5/100); ?></td>
					<td width="100" align="right"><? if($commer_act_cost>0  && $exfactoryQty>0) echo  fn_number_format($commer_act_cost/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" align="right" title="" bgcolor="<? //if(number_format($exfactoryQty*$unitPrice)>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					$commer_act_cost=$commaData['acl']['amount'];
					echo number_format($commer_act_cost,4); $totalOtherAclValue+=fn_number_format($commer_act_cost,4,".",""); ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><?  echo fn_number_format(($commer_act_cost*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($commaData['mkt']['amount']-$commaData['acl']['amount'],2);$comma_post_cost+=$commaData['pre']['amount']-$commaData['acl']['amount']; ?></td>
					<td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($commaData['mkt']['amount']-$commaData['acl']['amount'],2);$comma_total_cost+=$commaData['mkt']['amount']-$commaData['acl']['amount']; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="300" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['freight']['amount']-$otherData['pre']['freight']['amount'],2);$other_booking_cost+=$otherData['mkt']['freight']['amount']-$otherData['pre']['freight']['amount']; ?></td>
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['freight']['amount']>0 && $exfactoryQty>0) echo fn_number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo fn_number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=fn_number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><?  echo fn_number_format(($otherData['acl']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title=" Pre Cost Value-Actual value"><? echo number_format($otherData['pre']['freight']['amount']-$otherData['acl']['freight']['amount'],2);$other_post_cost+=$otherData['pre']['freight']['amount']-$otherData['acl']['freight']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($otherData['mkt']['freight']['amount']-$otherData['acl']['freight']['amount'],2);$other_total_cost+=$otherData['mkt']['freight']['amount']-$otherData['acl']['freight']['amount']; ?></td>
                     
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="300" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo number_format(($otherData['pre']['lab_test']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo number_format($otherData['mkt']['lab_test']['amount']-$otherData['pre']['lab_test']['amount'],2);$other_lab_total_cost+=$otherData['acl']['lab_test']['amount']-$otherData['pre']['lab_test']['amount']; ?></td>
					<td width="100" align="right"><? //echo $labtest_qnty; ?></td>
					<td width="100" align="right"><? 
					//$test_act_cost=$unitPrice*$exfactoryQty*(0.05/100);
					if($test_act_cost>0  && $labtest_qnty>0) echo fn_number_format($test_act_cost/$labtest_qnty,4);else echo ""; ?></td>
					<td width="100" align="right" title="" bgcolor="<? //if(number_format($test_act_cost,4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					//$otherData['acl']['lab_test']['amount'];
					//$test_act_cost=$otherData['acl']['lab_test']['amount'];
					//echo number_format($test_act_cost,4); $totalOtherAclValue+=number_format($test_act_cost,4,".","");?><? echo $labtest_qnty; ?></td>	
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($test_act_cost*100/$jobValue),2).'%'; ?></td>	
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format($otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount'],2);$other_lab_total_cost+=$otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount'],2);$other_total_cost+=$otherData['pre']['lab_test']['amount']-$otherData['acl']['lab_test']['amount']; ?></td>			
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="300" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
                     <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['inspection']['amount']-$otherData['pre']['inspection']['amount'],2);$ins_booking_data+=$otherData['mkt']['inspection']['amount']-$otherData['pre']['inspection']['amount']; ?></td>
					<td width="100" align="right"><? echo $inspection_qnty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['inspection']['amount']>0  && $inspection_qnty>0) echo fn_number_format($otherData['acl']['inspection']['amount']/$inspection_qnty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo fn_number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format($otherData['pre']['inspection']['amount']-$otherData['acl']['inspection']['amount'],2);$ins_post_data+=$otherData['pre']['inspection']['amount']-$otherData['acl']['inspection']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost "><? echo number_format($otherData['mkt']['inspection']['amount']-$otherData['acl']['inspection']['amount'],2);$ins_total_data+=$otherData['mkt']['inspection']['amount']-$otherData['acl']['inspection']['amount']; ?></td>
				</tr>
				<tr bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="300" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>	
                     <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['currier_pre_cost']['amount']-$otherData['pre']['currier_pre_cost']['amount'],2);$curr_booking_cost+=$otherData['mkt']['currier_pre_cost']['amount']-$otherData['pre']['currier_pre_cost']['amount']; ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['currier_pre_cost']['amount']>0  && $exfactoryQty>0) echo fn_number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($otherData['acl']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($otherData['pre']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount'],2);$curr_post_cost+=$otherData['pre']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount'],2);$curr_total_cost+=$otherData['mkt']['currier_pre_cost']['amount']-$otherData['acl']['currier_pre_cost']['amount']; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="300" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo fn_number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($otherData['pre']['cm_cost']['amount']*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo fn_number_format($otherData['mkt']['cm_cost']['amount']-$otherData['pre']['cm_cost']['amount'],2);$cm_booking_cost+=$otherData['mkt']['cm_cost']['amount']-$otherData['pre']['cm_cost']['amount']; ?></td>
					<?
					if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")) $font_color="white";
					else $font_color="black";
					?>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? if($otherData['acl']['cm_cost']['amount']>0  && $exfactoryQty>0) echo fn_number_format($otherData['acl']['cm_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" title="<?= $otherData['acl']['cm_cost']['amount'] ?>"  align="right"  bgcolor="<? if(number_format($making_cost_act,4,".","")>number_format($otherData['acl']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=fn_number_format($making_cost_act,4,".",""); echo number_format($otherData['acl']['cm_cost']['amount'],4); ?>
                    </td>
                     <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($making_cost_act*100/$jobValue),2).'%'; ?></td>
					 <td width="100" align="right" title="Pre Cost Value-Actual value "><? echo fn_number_format($otherData['pre']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount'],2); $cm_post_cost+=$otherData['pre']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount']; ?></td>
					 <td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($otherData['mkt']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount'],2); $cm_total_cost+=$otherData['mkt']['cm_cost']['amount']-$otherData['acl']['cm_cost']['amount']; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" >Others Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($totalOtherMktValue,4); $GrandTotalMktValue+=fn_number_format($totalOtherMktValue,4,".",""); ?></td>
					
                    <td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? echo number_format($totalOtherPreValue,4); $GrandTotalPreValue+=fn_number_format($totalOtherPreValue,4,".",""); ?>
					</td>
                   <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($totalOtherPreValue*100/$jobValue),2).'%'; ?></td>
                   <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo fn_number_format($totalOtherMktValue-$totalOtherPreValue,2);$oth_booking_cost+=$totalOtherMktValue-$totalOtherPreValue; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? echo number_format($totalOtherAclValue,4); $GrandTotalAclValue+=fn_number_format($totalOtherAclValue,4,".",""); ?>
					</td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($totalOtherAclValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($totalOtherPreValue-$totalOtherAclValu,2);$oth_post_cost+=$totalOtherPreValue-$totalOtherAclValu; ?></td>
					<td width="100" align="right" title="Marketing Value-Post Cost"><? echo fn_number_format($totalOtherPreValue-$totalOtherAclValu,2);$oth_total_cost+=$totalOtherPreValue-$totalOtherAclValu; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="300" >Grand Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
                     <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($GrandTotalPreValue*100/$jobValue),2).'%'; ?></td>
                     <td width="100" align="right" title=" Market Value-Pre Cost Value"><? echo fn_number_format($GrandTotalMktValue-$GrandTotalPreValue,2) ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($GrandTotalAclValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo fn_number_format($GrandTotalPreValue-$GrandTotalAclValue,2) ?></td>
					<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo fn_number_format($GrandTotalMktValue-$GrandTotalAclValue,2) ?></td>
                    
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="300" >Shipment Value</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right"><? echo $quaOfferQnty;  ?></td>
					<td width="100" align="right" title="<? echo $quaPriceWithCommnPcs;?>"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right"><? $quaOfferValue=$quaOfferQnty*$quaPriceWithCommnPcs; echo number_format($quaOfferValue,4) ?></td>
					
                    <td width="100" align="right"><? echo $jobQty ?></td>
					<td width="100" align="right" title="<? echo $unitPrice;?>"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
                    <td width="100" align="right" title="Pre Cost Value/JobValue*100"><? echo fn_number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
                    <td width="100" align="right" title="Market Value-Pre Cost Value"><? echo number_format($quaOfferValue-$jobValue,2); ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right" title="<? echo $exfactoryValue/$exfactoryQty;?>"><? if( $exfactoryValue>0 && $exfactoryQty>0) echo number_format($exfactoryValue/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4) ?></td>
                    <td width="100" align="right" title="Actual Cost Value/JobValue*100"><? echo fn_number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
					<td width="100" align="right" title="Pre Cost Value-Actual value"><? echo number_format(($jobValue-$exfactoryValue),2); ?></td>
					<td width="100" align="right" title=" Marketing Value-Post Cost"><? echo number_format(($quaOfferValue-$exfactoryValue),2); ?></td>
				</tr>
			</table>
			<table style="width:120px;float: left; margin-left: 5px; margin-top: 10px;">
				<tr>
					
                    <?
                    $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in('$jobNumber') and form_name='knit_order_entry' and file_type=1");
					if(count( $nameArray_img)>0)
					{
					    ?>
						    
                            
							<? 
							foreach($nameArray_img AS $inf) { 
								?>
                                 <td width="120">
                                <div style="float:left;width:120px" >
								<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='75' width='95' />
                                 </td>
                               </div>
							    <?  
							}
							?>
							
						<?								
					}
					else echo ""; ?>						    
                   
				</tr>
            </table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="10"><p style="font-weight: bold;">Profit Summary</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"  title="Total Mkt Value/Offer Value*100"><? if($GrandTotalMktValue>0 && $quaOfferValue>0) echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right" title="Total Expand/Offer Value*100"><? if($yarnTrimMktValue>0) echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
                    <td width="150" align="right"><? echo number_format($total_mkt_amt,4) ?></td>
					<td width="100" align="right"  title="Total Mkt Wash/Offer Value*100"><? if($total_mkt_amt>0) echo number_format(($total_mkt_amt/$quaOfferValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Offer Value-Grand Total Mkt Value"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right" title="Offer Value-Grand Total Mkt Value/Offer Value*100"><? if($quaOfferValue-$GrandTotalMktValue>0) echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Total Pre Value/Job Value*100"><? if($GrandTotalPreValue>0) echo number_format(($GrandTotalPreValue/$jobValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? if($yarnTrimPreValue>0) echo number_format(($yarnTrimPreValue/$jobValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_pre_amt,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><? if($total_pre_amt>0) echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Job Value-Grand Total Pre Value"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Job Value-Grand Total Pre Value/Ex-Fact Value*100"><? if($jobValue-$GrandTotalPreValue>0) echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);else echo "";?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Total Actual Value/Ex-Fact Value*100"><? if($GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Ex-fact Value*10"><? if($yarnTrimAclValue>0) echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_acl_amt,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Ex-Fact Value*100"><? if($total_acl_amt>0) echo number_format(($total_acl_amt/$exfactoryValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value-Grand Total Acl Value"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Ex-Fact Value-Grand Total Ex Value/Ex-Fact Value*100"><? if($exfactoryValue-$GrandTotalAclValue>0) echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);else echo "";?></td>
				</tr>
			</table>
            <br>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="11"><p style="font-weight: bold;">Profit Summary 1 Pcs</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">FOB/Pcs</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    <td width="100">CM Cost</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right" title="Mkt Cost/Job Qty"><? $tot_offer_val_pcs=$unitPrice;//$quaOfferValue/$jobQty;
					echo number_format($tot_offer_val_pcs,4) ?></td>
					<td width="150"  title="Grand Total Mkt Value/Job Qty" align="right"><? $tot_mkt_val_pcs=$GrandTotalMktValue/$jobQty;echo number_format($tot_mkt_val_pcs,4); ?></td>
					<td width="100" align="right"  title="Total Expenditure/FOB Pcs*100"><? if($tot_mkt_val_pcs>0) echo number_format(($tot_mkt_val_pcs/$tot_offer_val_pcs)*100,4); else echo "";?></td>
					<td width="150" align="right" title="Total Trims Mkt value(<? echo $yarnTrimMktValue?>)/Job Qty(<? echo $jobQty?>)"><? $tot_trim_val_pcs=$yarnTrimMktValue/$jobQty;echo number_format($tot_trim_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Trims(<? echo $tot_trim_val_pcs?>)/FOB Pcs(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_trim_val_pcs>0) echo number_format(($tot_trim_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                    <td width="150" align="right" title="Total Mkt Wash(<? echo $total_mkt_amt?>)/Job Qty(<? echo $jobQty?>)"><? $tot_wash_val_pcs=$total_mkt_amt/$jobQty; echo number_format($tot_wash_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash/Offer Value*100"><? if($tot_wash_val_pcs>0) echo number_format(($tot_wash_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Cost(<? echo $otherData['mkt']['cm_cost']['amount'];?>)/Job Qty"><? $tot_mkt_cm_pcs=$otherData['mkt']['cm_cost']['amount']/$jobQty; echo number_format($tot_mkt_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Qua.Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $profit_mkt_margin_pcs=($quaOfferValue-$GrandTotalMktValue)/$jobQty;echo number_format($profit_mkt_margin_pcs,4); ?></td>
					<td width="100" align="right" title="Profit margin(<? echo $profit_mkt_margin_pcs;?>)/Mkt FOB/Pcs (<? echo $tot_offer_val_pcs;?>)*100)"><? if($profit_mkt_margin_pcs>0) echo number_format(($profit_mkt_margin_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right" title="Pre Cost/Job Qty"><? $tot_preJob_value=$unitPrice;//$jobValue/$jobQty;
					echo number_format($tot_preJob_value,4) ?></td>
					<td width="150" align="right" title="Pre Cost/JobQty"><? $tot_pre_costing_expnd=$GrandTotalPreValue/$jobQty; echo number_format($tot_pre_costing_expnd,4);?></td>
					<td width="100" align="right"  title="Total Expenditure/Job Value*100"><? 
					if($tot_pre_costing_expnd>0)
					{
						$tot_pre_costing_expnd_per=$tot_pre_costing_expnd/$tot_preJob_value*100;
					}
					else {$tot_pre_costing_expnd_per=0;}
					echo number_format($tot_pre_costing_expnd_per,4); ?></td>
					<td width="150" align="right"  title="Total Trims Value/Job Qty"><? $tot_preTrim_costing=$yarnTrimPreValue/$jobQty;echo number_format($tot_preTrim_costing,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? 
					if($tot_preTrim_costing>0)
					{
						$tot_preTrim_costing_per=($tot_preTrim_costing/$tot_preJob_value)*100;
					}
					else {$tot_preTrim_costing_per=0;}
					echo number_format($tot_preTrim_costing_per,4); ?></td>
                     <td width="150" align="right" title="Pre Wash Cost/Job Qty"><?  $tot_preWash_value=$total_pre_amt/$jobQty;echo number_format($tot_preWash_value,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><?  $tot_preWash_value_pre=$tot_preWash_value/$tot_preJob_value*100;echo number_format($tot_preWash_value_pre,4); ?></td>
                     <td width="150" align="right"  title="Cm Cost(<? echo $otherData['pre']['cm_cost']['amount'];?>)/Job Qty"><? $tot_pre_cm_pcs=$otherData['pre']['cm_cost']['amount']/$jobQty; echo number_format($tot_pre_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Job Qty"><? $profit_pre_margin_pcs=($jobValue-$GrandTotalPreValue)/$jobQty;echo number_format($profit_pre_margin_pcs,4);?></td>
					<td width="100" align="right"  title="Profit Margin(<? echo $profit_pre_margin_pcs;?>)/Pre FOB Pcs (<? echo $tot_preJob_value;?>)*100)"><? echo number_format(($profit_pre_margin_pcs/$tot_preJob_value)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right" title="Acl Job Value/Job Qty"><? $tot_AclJob_valuePcs=$unitPrice;//$exfactoryValue/$jobQty;
					echo number_format($tot_AclJob_valuePcs,4) ?></td>
					<td width="150" align="right" align="right" title="Grand total Acl value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><? $tot_AclJob_ExpndPcs=$GrandTotalAclValue/$jobQty; echo number_format($tot_AclJob_ExpndPcs,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? if($tot_AclJob_ExpndPcs>0 && $tot_AclJob_valuePcs>0) echo number_format(($tot_AclJob_ExpndPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right"  title="Total Acl Trims Value/Job Qty"><? $tot_AcltrimValue=$yarnTrimAclValue/$jobQty;echo number_format($tot_AcltrimValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Job Value*100"><? if($tot_AcltrimValue>0) echo number_format(($tot_AcltrimValue/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? $tot_AclJobWashPcs=$total_acl_amt/$jobQty; echo number_format($tot_AclJobWashPcs,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Job Value*100"><? if($tot_AclJobWashPcs>0) echo number_format(($tot_AclJobWashPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="Making Cost(<? echo $making_cost_act;?>)/Job Qty"><?  $tot_cmAcl_pcs=$making_cost_act/$jobQty;; echo number_format($tot_cmAcl_pcs,4) ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value(<? echo $exfactoryValue;?>)-Grand Total Acl Value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><?  $profit_acl_margin_pcs=($exfactoryValue-$GrandTotalAclValue)/$jobQty; echo number_format($profit_acl_margin_pcs,4);?></td>

					<td width="100" align="right" title="Profit margin(<? echo $profit_acl_margin_pcs;?>)/Actual FOB/Pcs (<? echo $tot_AclJob_valuePcs;?>)*100)"><? if($profit_acl_margin_pcs>0) echo number_format(($profit_acl_margin_pcs/$tot_AclJob_valuePcs)*100,4);else echo "";?></td>
				</tr>
			</table>
             <br>
              <br>
		</div>
		<?
	}
	if(str_replace("'","",$cbo_costcontrol_source)==2) //Price Quotation
	{
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){ echo "Select Company"; die; }
		if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		$check_data=sql_select("select a.job_no from wo_po_details_master a,wo_pre_cost_mst f where  a.id=f.job_id  and a.company_name=$company_name and a.job_no_prefix_num like '$job_no' and a.garments_nature=3 $year_cond group by a.job_no");
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		//$chk_job_nos=$job_no;
		if($chk_job_nos!='') 
		{
			 $sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.inquiry_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and  a.id=c.job_id and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
			$result=sql_select($sql);
		}
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
		$shipArry=array(0,1,2);
		$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotationId=$row[csf('quotation_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$ShipDateArrall[date("d-m-Y",strtotime($row[csf('shipment_date')]))]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			/* if($row[csf('shiping_status')]==3) //Full
			{
			 $shiping_status=2; 
			}
			//echo $shiping_status.'D';
			$shipingStatus=$shiping_status; */
			$shipingStatus=$row[csf('shiping_status')];
			$shiping_status_id.=$row[csf('shiping_status')].',';
			$po_ids.=$row[csf('id')].',';$ref_no.=$row[csf('grouping')].',';
			$team_leaders.=$team_leader_library[$row[csf('team_leader')]].',';
			$dealing_marchants.=$dealing_merchant_array[$row[csf('dealing_marchant')]].',';
		}
		//	echo $quotationId.'D';

		$sql_sample="SELECT sum(d.required_qty) as qnty
			    FROM wo_po_details_master a, sample_development_mst c,sample_development_fabric_acc d
			    WHERE     a.status_active = 1
			         AND a.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         and d.sample_mst_id=c.id
			         AND c.quotation_id = a.id
			         AND d.form_type = 1
			         AND d.is_deleted = 0
			         AND d.status_active = 1
			         AND a.job_no='$jobNumber'

	       ";
	  //  echo "<pre>$sql_sample</pre>";
	    $res_sample=sql_select($sql_sample);
	    $sql_sample_fab="SELECT SUM (fin_fab_qnty) AS qnty
						  FROM wo_booking_dtls
						 WHERE     status_active = 1
						       AND is_deleted = 0
						       AND job_no='$jobNumber'
						       AND entry_form_id = 440";
		//echo "<pre>$sql_sample_fab</pre>";
	    $res_sample_fab=sql_select($sql_sample_fab);

	    $booking_sql3=sql_select("SELECT 
			         SUM (a.fin_fab_qnty) AS qnty
			        
			    FROM wo_booking_dtls a, wo_booking_mst b
			   WHERE     a.booking_no = b.booking_no
			         AND a.is_short = 2
			         AND b.booking_type = 4
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND a.job_no = '$jobNumber'
			
				 ");
	    $sample_booking_all_qnty=$res_sample[0][csf('qnty')]+$res_sample_fab[0][csf('qnty')]+$booking_sql3[0][csf('qnty')];
		 //print_r($ShipDateArrall);
		$shiping_status_id=rtrim($shiping_status_id,',');
		
		$shiping_status_idAll=array_unique(explode(",",$shiping_status_id));
		//print_r($shiping_status_idAll);
		$shiping_status=3;
		foreach($shiping_status_idAll as $sid)
		{
			//echo $sid."D,";
			if(in_array($sid,$shipArry))
			{
				$shiping_status=2;
			}
		}
		//echo $shiping_status;
		$team_leaders=rtrim($team_leaders,',');
		$team_leaders=implode(",",array_unique(explode(",",$team_leaders)));
		$dealing_marchants=rtrim($dealing_marchants,',');
		$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchants)));
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0) $po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; else $po_cond_for_in="";
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessValue=$shortExcessQty*($unitPrice);
	    //$quotationId=1;
		
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	    // Yarn ============================
		$totYarn=0;
		$fabPurArr=array(); $YarnData=array(); $qYarnData=array(); $knitData=array(); $finishData=array(); $washData=array(); $embData=array(); $trimData=array(); $commiData=array(); $otherData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	    //print_r($yarn_data_array);
		$sql_yarn="select count_id, copm_one_id, percent_one, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id, copm_one_id, percent_one, type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row)
		{
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		unset($data_arr_yarn);
		/*echo "<pre>";
		print_r($YarnData); die;*/
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		if($quotationId)
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			 // $sqlQc="select a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.inquery_id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			  $sqlQua="select a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQuot=sql_select($sqlQua);
			//print_r($dataQc);
			foreach($dataQuot as $rowQua)
			{
				$quaOfferQnty=$jobQty;
				$quaConfirmPrice=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer=$rowQua[csf('costing_per')];
				$quaCostingPerQty=0;
				if($quaCostingPer==1) $quaCostingPerQty=12;
				if($quaCostingPer==2) $quaCostingPerQty=1;
				if($quaCostingPer==3) $quaCostingPerQty=24;
				if($quaCostingPer==4) $quaCostingPerQty=36;
				if($quaCostingPer==5) $quaCostingPerQty=48;
				$quaCostingPerQty=1;
				
			}
			unset($dataQc);
		 //echo $quaOfferQnty; die;
						
			if($quotationId){
			$totMktcons=0; $totMktAmt=0;
			$sql = "select id, item_number_id, body_part_id, fabric_description,fab_nature_id, color_type_id, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='".$quotationId."' and fabric_source=2";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
			//$mktcons=($fabPur_row[csf('avg_cons')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($fabPur_row[csf('avg_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				//echo $fabPur_row[csf('avg_cons')].'='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
				$mktamt=$mktcons*$fabPur_row[csf('rate')];
				$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
				if($fabPur_row[csf('fab_nature_id')]==2){
					$fabPurArr[$fabStri]['mkt']['knit']['qty']+=$mktcons;
					$fabPurArr[$fabStri]['mkt']['knit']['amount']+=$mktamt;
				}
				if($fabPur_row[csf('fab_nature_id')]==3){
					$fabPurArr[$fabStri]['mkt']['woven']['qty']+=$mktcons;
					$fabPurArr[$fabStri]['mkt']['woven']['amount']+=$mktamt;
				}
			}
			
			 $sql_trim = "select id,  trim_group, cons_uom, total_cons, rate, amount, apvl_req, nominated_supp,status_active from wo_pri_quo_trim_cost_dtls  where quotation_id='".$quotationId."' and status_active=1";
			$trim_data_array=sql_select($sql_trim);
			foreach($trim_data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktcons=($row[csf('total_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
			// echo $row[csf('total_cons')].'Pri='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
				$trimData[$row[csf('trim_group')]]['mkt']['qty']+=$mktcons;
				$trimData[$row[csf('trim_group')]]['mkt']['amount']+=$mktamt;
				$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
			}
			unset($trim_data_array);
			//========Emblishmnet=====
			$sql_embl = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name !=3 and status_active=1";
			$embl_data_array=sql_select($sql_embl);
			foreach($embl_data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$embData['mkt']['qty']+=$mktcons;
				$embData['mkt']['amount']+=$mktamt;
			}
			unset($embl_data_array);
			 $sql_wash = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name =3 and status_active=1";
			$wash_data_array=sql_select($sql_wash);
			foreach($wash_data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
					
					//echo $mktcons.'='.$row[csf('cons_dzn_gmts')].'='.$quaCostingPerQty.'='.$quaOfferQnty.'<br>';
					
				$washData[$index]['mkt']['qty']+=$mktcons;
				$washData[$index]['mkt']['amount']+=$mktamt;
			}
			unset($wash_data_array);
			$commi_sql = "select id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pri_quo_commiss_cost_dtls  where quotation_id='".$quotationId."' and status_active=1";
			$commi_data_array=sql_select($commi_sql);
			foreach($commi_data_array as $row){
		//$mktamt=($row[csf('commission_amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('commission_amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['qty']=$quaOfferQnty;
				$commiData['mkt']['amount']+=$mktamt;
				$commiData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
			unset($commi_data_array);
			 $commer_sql = "select id, item_id, rate, amount, status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id=".$quotationId." and status_active=1";
			$commer_data_array=sql_select($commer_sql);
			foreach($commer_data_array as $row){
		//$mktamt=($row[csf('amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktamt;
				if($mktam>0 && $quaOfferQnty>0)
				{
				$commaData['mkt']['rate']+=$mktamt/$quaOfferQnty;
				}
				else
				{
					$commaData['mkt']['rate']+=0;
				}
			}
			unset($commer_data_array);
			
			$other_sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh,design_pre_cost as design_cost ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id='".$quotationId."'";
			$other_data_array=sql_select($other_sql);
			foreach($other_data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['qty']=$quaOfferQnty;
				$otherData['mkt']['freight']['amount']=$freightAmt;
				$otherData['mkt']['freight']['rate']=$freightAmt/$quaOfferQnty;
	
				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['qty']=$quaOfferQnty;
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
				$otherData['mkt']['lab_test']['rate']=$labTestAmt/$quaOfferQnty;
	
				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['qty']=$quaOfferQnty;
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
				$otherData['mkt']['inspection']['rate']=$inspectionAmt/$quaOfferQnty;
				
				$design_costAmt=($row[csf('design_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['design_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['design_cost']['amount']=$design_costAmt;
				$otherData['mkt']['design_cost']['rate']=$design_costAmt/$quaOfferQnty;
	
				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['qty']=$quaOfferQnty;
	
				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
				$otherData['mkt']['currier_pre_cost']['rate']=$currierPreCostAmt/$quaOfferQnty;
	
				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
				$otherData['mkt']['cm_cost']['rate']=$cmCostAmt/$quaOfferQnty;
			}
		
			
		} //Qout Id End
			
			
		}
		
	 
		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
	
		//echo $lot_tmp;
		//echo $knit_grey_amt; // and a.yarn_lot in(".$lot_tmp.")
		$fin_fab_trans_array=array();
		  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
		 	 sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, 
			 sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt,
			sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, 
			c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$wvn_fin_fab_trans_amt_acl=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			//$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['in']=$row[csf('in_qty')];
			//$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['out']=$row[csf('out_qty')];
			$tot_fin_fab_transfer_qty+=$row[csf('in_qty')]-$row[csf('out_qty')];
			$tot_trans_amt=$row[csf('in_amt')]-$row[csf('out_amt')];
			$wvn_fin_fab_trans_amt_acl+=$tot_trans_amt/$g_exchange_rate;
		}
		unset($result_fin_trans);
		$tot_wvn_fin_fab_transfer_cost=$wvn_fin_fab_trans_amt_acl;
		
		$trim_fab_trans_array=array();
		 $sql_trim_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(5) THEN  b.quantity*a.rate ELSE 0 END) AS grey_in_amt,
		  sum(CASE WHEN b.entry_form in(78,112) and b.trans_type in(6) THEN  b.quantity*a.rate ELSE 0 END) AS grey_out_amt
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_trim_trans=sql_select( $sql_trim_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_trim_trans as $row)
		{
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in_amt')]-$row[csf('grey_out_amt')])/$g_exchange_rate;
		}
		//echo $grey_fab_trans_amt_acl.'d';
		unset($result_trim_trans);
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
	
		$subconOutBillData="select b.order_id,
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
		}
	
		 

	    	
	
		// Yarn End============================
		// Fabric Purch ============================
		$totPrecons=0; $totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		
		//print_r($fabPur);
		 $sql = "select id, job_no,item_number_id,uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2 and status_active=1 and is_deleted=0";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			$fabStri=$fabPur_row[csf('fabric_description')].'_'.$fabPur_row[csf('id')];
			$fabDescArr[$fabPur_row[csf('id')]]['fabric_description']=$fabPur_row[csf('fabric_description')];
			$fabDescArr[$fabPur_row[csf('id')]]['uom']=$fabPur_row[csf('uom')];
			
			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['knit']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr[$fabStri]['pre']['woven']['qty']+=$Precons;
				$fabPurArr[$fabStri]['pre']['woven']['amount']+=$Preamt;
			}
		} 
		
		$sql = "select a.item_category, a.exchange_rate, b.grey_fab_qnty,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
				$fabric_description=$fabDescArr[$fabPur_row[csf('fab_dtls_id')]]['fabric_description'];
				$fabStri=$fabric_description.'_'.$fabPur_row[csf('fab_dtls_id')];
				
				if($fabPur_row[csf('item_category')]==2){
					$fabPurArr[$fabStri]['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					$fabPurArr[$fabStri]['acl']['knit']['fabric_description']=$fabric_description;
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
					}
				}
				if($fabPur_row[csf('item_category')]==3){
					$fabPurArr[$fabStri]['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
					//alc_woven_qnty
					if($fabPur_row[csf('grey_fab_qnty')]>0){
						$fabPurArr[$fabStri]['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
					}
				}
			}
		
		
	
		// Fabric AOP  End============================
		// Trim Cost ============================
		//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$trim= new trims($condition);
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	
		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			//echo $trimAmtArr[$row[csf('job_no')]][$row[csf('id')]].'d';
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$row[csf('job_no')]][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		//print_r($trimData);
		
		$trimsRecArr=array();
		
		 
		  $sql_trim_wo = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.trim_group from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_trim_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=2  and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_wo=sql_select($sql_trim_wo);
		foreach($data_array_wo as $row){
			$index=$row[csf('trim_group')];
			$trimData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$trimData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		unset($data_array_wo);
		$general_item_issue_sql="select a.item_group_id as trim_group, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_id=d.id and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in(".implode(",",$poIdArr).") ";
			 $gen_result=sql_select($general_item_issue_sql);
			foreach($gen_result as $row)
            {
				$con_factor=$trim_groupArr[$row[csf('trim_group')]]['con_factor'];
				$gen_item_arr[$row[csf('trim_group')]]['issue_qty']+=$row[csf('cons_quantity')];
				$gen_item_arr[$row[csf('trim_group')]]['cons_amount']+=($row[csf('cons_amount')]*$con_factor)/$g_exchange_rate;
            }
			//print_r($gen_item_arr);
			foreach($gen_item_arr as $ind=>$value)
			{
			$trimData[$ind]['acl']['qty']+=$value['issue_qty'];
			$trimData[$ind]['acl']['amount']+=$value['cons_amount'];
		    }
		//print_r($trimsRecArr);
		unset($gen_result);
		
	
		//print_r($trimData);
	
		// Trim Cost End============================
		// Embl Cost ============================
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		
		 $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
				$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			if($row[csf('emb_name')]==3)
			{
				$washData[$index]['pre']['qty']+=$washQtyArr[$row[csf("job_no")]][$row[csf('id')]];
				$washData[$index]['pre']['amount']+=$washAmtArr[$row[csf("job_no")]][$row[csf('id')]];
			}
			else
			{
				$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
				$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
			}
		}
		//print_r($washData);	
		//echo $sql = "select a.item_category, a.exchange_rate, b.grey_fab_qnty, b.wo_qnty, b.fin_fab_qnty, b.po_break_down_id, b.rate, b.amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		 $sql_emb = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name!=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array_emb=sql_select($sql_emb);
		foreach($data_array_emb as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
		}
		// Embl Cost End ============================
		// Wash Cost ============================
		
		 
	 $sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount,c.emb_name, c.emb_type from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c where a.id=b.booking_mst_id and c.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and c.emb_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1   and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$index="'".$emblishment_wash_type_arr[$row[csf("emb_type")]]."'";
			$washData[$index]['acl']['qty']+=$row[csf('wo_qnty')];
			$washData[$index]['acl']['amount']+=$row[csf('amount')];
	
		}
		
		// Wash Cost End ============================
		// Commision Cost  ============================
		
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}
		
		// Commision Cost  End ============================
	
		// Commarcial Cost  ============================
		//$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			if($commaAmtArr[$jobNumber][$row[csf('id')]]>0 && $exfactory_qty>0)
			{
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;
			}
			else
			{
				$commaData['pre']['rate']+=0;
			}
		}
		
		// Commarcial Cost  End ============================
		// Other Cost  ============================
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id, lab_test,  inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, common_oh, depr_amor_pre_cost from  wo_pre_cost_dtls where job_no='".$jobNumber."' and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;
	
			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;
	
			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;
	
			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;
	
			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			if($other_cost[$jobNumber]['cm_cost']>0)
			{
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
			}
			else
			{
				$otherData['pre']['cm_cost']['rate']=0;
			}
		}
		
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	
	 $sql_cm_cost="select jobNo, available_min, production_date from production_logicsoft where jobNo='".$jobNumber."' and is_self_order=1";
		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$production_date=change_date_format($row[csf('production_date')],'','',1);
			$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
			$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		}
	
		 $sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==5){
				//$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
			}
		}
		// Other Cost End ============================
		if($quotationId){
			$sql_item_summ="select commercial_cost from qc_item_cost_summary where mst_id =".$qc_no." and status_active=1 and is_deleted=0";
			$data_array=sql_select($sql_item_summ);
			foreach($data_array as $row){
				$mktCommaamt=($row[csf('commercial_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktCommaamt;
				$commaData['mkt']['rate']+=$mktCommaamt/$quaOfferQnty;
			}
		}
		/* echo '<pre>';
		print_r($commaData);die; */
		// Commarcial Cost  End ============================
			 
		ob_start();
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1300px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px" rules="all">
				<tr>
					<td width="100">Job Number</td>
					<td width="200"><? echo $jobNumber; ?></td>
					<td width="100">Buyer</td>
					<td width="200"><? echo $buyer_arr[$buyerName]; ?></td>
                    <td width="100">Internal Ref. No</td>
					<td width="100"><? $ref_nos=rtrim($ref_no,',');echo implode(",",array_unique(explode(",",$ref_nos))); ?></td>
					<td width="100">Style Ref. No</td>
					<td width="200"><? echo $styleRefno; ?></td>
					<td width="100">Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Order Number</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo number_format($jobValue,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Ship Date</td>
					<td style="word-break:break-all;"><? 
					 echo implode(",",$ShipDateArrall);
					 ?></td>
					<td>Team Leader</td> 
                    <td style="word-break:break-all;"><? echo $team_leaders;?></td>
					<td>Dealing Marchant</td>
                    <td style="word-break:break-all;" colspan="3"><? echo $dealing_marchants;?></td>
					<td>Unit Price</td>
					<td align="right"><? echo number_format($unitPrice,4)." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td>Garments Item</td>
					<td colspan="7" style="word-break:break-all;"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Ship Qty</td>
					<td align="right"><? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td>Shipment Value</td>
					<td align="right"><? echo number_format($exfactoryValue,4); ?></td>
					<td>Short/Excess Qty</td>
					<td align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right" colspan="3"><? echo number_format($shortExcessValue,4); ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>
	
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1300px; margin-top:10px" rules="all">
				<thead>
                	<tr>
                    <td> </td>
                    <td> </td>
                     <td colspan="3" align="center" title="Price Quotation ID=<? echo $quotationId;?>"> <b>Price Quot. Cost</b></td>
                     <td colspan="4" align="center"> <b>Budget Cost</b></td>
                     <td colspan="4" align="center"> <b>Post Cost</b></td>
                    </tr>
					<tr align="center" style="font-weight:bold">
						<td width="300">Item Description</td>
						<td width="60">UOM</td>
						<td width="100">Marketing Qty</td>
						<td width="100">Marketing Price</td>
						<td width="100">Marketing Value</td>
                        
						<td width="100">Pre-Cost Qty</td>
						<td width="100">Pre-Cost Price</td>
						<td width="100">Pre-Cost Value</td>
                        <td width="100">Cont. % On FOB Value</td>
                        
						<td width="100">Actual Qty</td>
						<td width="100">Actual Price</td>
						<td width="100">Actual Value</td>
                        <td width="100">Cont. % On FOB Value</td>
					</tr>
				</thead>
				
				<?
				$GrandTotalMktValue=0; $GrandTotalPreValue=0; $GrandTotalAclValue=0; $yarnTrimMktValue=0; $yarnTrimPreValue=0; $yarnTrimAclValue=0; $totalMktValue=0; $totalPreValue=0; $totalAclValue=0; $totalMktQty=0; $totalPreQty=0; $totalAclQty=0;
				 
				  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				 
				?>
				
				<?
				  $f1=2;
				  $f=2;$mkt_amount=$pre_amount=$acl_amount=0;
				foreach($fabPurArr as $fab_index=>$row ){
					if($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$fabric_description=explode("_",str_replace("'","",$fab_index));
					//$fabric_description=$fabPurArr[$fab_index]['mkt']['woven']['fabric_description'];
					$uom_name=$unit_of_measurement[$fabDescArr[$fabric_description[1]]['uom']];
				?>
				 <tr class="fbpur" bgcolor="<? echo $fab_bgcolor2;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $fab_bgcolor2;?>')" id="trfab_<? echo $f; ?>">
					<td width="300" title="Fabric-> <? echo $fab_index;?>" > <? echo $fabric_description[0];?></td>
					<td width="60"><? echo $uom_name;?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['mkt']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount']/$fabPurArr[$fab_index]['mkt']['woven']['qty'],4);else echo " "; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['mkt']['woven']['amount'],4); ?></td>
                    
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['pre']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount']/$fabPurArr[$fab_index]['pre']['woven']['qty'],4);else echo " "; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['pre']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['pre']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right" title="<?=$sample_booking_all_qnty?>">
						
							<a href="#" onClick="openmypage_sample_fab('<? echo implode("_",array_keys($poNumberArr)); ?>','<?=$jobNumber?>')">
								<? echo fn_number_format($fabPurArr[$fab_index]['acl']['woven']['qty']+$sample_booking_all_qnty,4) ?>
							</a>
						</td>
					<td width="100" align="right"><? if($fabPurArr[$fab_index]['acl']['woven']['amount']>0) echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount']/$fabPurArr[$fab_index]['acl']['woven']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr[$fab_index]['acl']['woven']['amount'],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($fabPurArr[$fab_index]['acl']['woven']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
               <?
			   $f++;
			 	 $mkt_amount+=$fabPurArr[$fab_index]['mkt']['woven']['amount'];
				$pre_amount+=$fabPurArr[$fab_index]['pre']['woven']['amount'];
				$acl_amount+=$fabPurArr[$fab_index]['acl']['woven']['amount'];
				}
			   ?>
                
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						$fabPurMkt= number_format($mkt_amount,4,".","");
						echo number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($pre_amount,4,".","");
						$fabPurAcl= number_format($acl_amount,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor="<? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
                      <td width="100" align="right"><? echo number_format(($fabPurPre*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<?
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($fabPurAcl*100/$jobValue),2).'%'; ?></td>
				</tr>
				
	
				 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
			  <?
	
				  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;
				?>
				 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="300">Cost<? //echo $item_descrition ?></td>
						<td width="60"></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['mkt']['amount'],4); ?></td>
                        
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
                        <td width="100" align="right">&nbsp;<? //echo number_format($row['pre']['qty'],4); ?></td>
                        
						
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? if($grey_fab_trans_amt_acl>0) echo number_format($grey_fab_trans_amt_acl/$grey_fab_trans_qty_acl,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($grey_fab_trans_amt_acl,4); ?></a>&nbsp;<? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
                       <td width="100" align="right"><? echo number_format(($grey_fab_trans_amt_acl*100/$jobValue),2).'%'; ?></td>
					</tr>
				<?
				 $tt2=1;
				?>
				<tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="300">Finished Fabric Cost</td>
						<td width="60">Yds</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost>0) echo number_format($tot_wvn_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4);else echo ""; ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_wvn_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
                         <td width="100" align="right"><? echo number_format(($tot_wvn_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
					</tr>
					<?
	
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
                    <td width="100" align="right"><? //echo number_format(($tot_fin_fab_transfer_cost*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"> <? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100" align="right"><? if($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl>0) echo number_format(($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);else "";?></td>
					<td width="100" align="right">
						<?
						echo number_format($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl,4);
						$total_grey_fin_fab_transfer_actl_cost=$tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format((($tot_wvn_fin_fab_transfer_cost+$grey_fab_trans_amt_acl)*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr>
					<td colspan="12" style="font-weight:bold" class="trims">Trims Cost</td>
				</tr>
				<?
				$t=1; $totalTrimMktValue=0; $totalTrimPreValue=0; $totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					if($index>0)
					{
						$item_descrition = $trim_groupArr[$index]['item_name'];
						$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
						$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
						$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
						if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr class="trims" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
							<td width="300"><? echo $item_descrition ?></td>
							<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
                            
							<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
							<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
							<td width="100" align="right"  bgcolor=" <? if(number_format($row['pre']['amount'],4,'.','')>number_format($row['mkt']['amount'],4,'.','')){echo "yellow";} else{echo "";}?>"><? echo number_format($row['pre']['amount'],4); ?></td>
                             <td width="100" align="right"><? echo number_format(($row['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                             
							<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
							<td width="100" align="right"><? if($row['acl']['amount']>0)  echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
							<td width="100" align="right" title="<? echo 'Act='.$row['acl']['amount'].', Pre='.$row['pre']['amount'];?>" bgcolor=" <? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}  else{echo " ";}?>"><? echo number_format($row['acl']['amount'],4); ?></td>
                            <td width="100" align="right"><? echo number_format(($row['acl']['amount']*100/$jobValue),2).'%'; ?></td>
						</tr>
						<?
						$t++;
					}
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300"><span id="trimstotal" class="adl-signs" onClick="yarnT(this.id,'.trims')">+</span>&nbsp;&nbsp Trims Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">
						<?
						echo number_format($totalTrimMktValue,4);
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".","");
						?>
					</td>
                     <td width="100" align="right"><? echo number_format(($totalTrimPreValue*100/$jobValue),2).'%'; ?></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if(number_format($totalTrimPreValue,4,".","")>number_format($totalTrimMktValue,4,".","")){echo "yellow";} else{echo "";}?>">
						<?
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".","");
						?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalTrimAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<?
				$totalOtherMktValue=0; $totalOtherPreValue=0; $totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 
				  <?
				$w=1;$total_mkt_qty=$total_mkt_amt=$total_pre_qty=$total_pre_amt=$total_acl_qty=$total_acl_amt=0;
                foreach($washData as $index=>$row ){
					$index=str_replace("'","",$index);
					$item_des=explode("_",$index);
					$item_des=implode(",",$item_des);
					/*$item_descrition = $trim_groupArr[$index]['item_name'];
					$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
					$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");*/
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr  class="wash" bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="300" ><? echo  $item_des;	?></td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['mkt']['amount']>0) echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); $GrandTotalMktValue+=number_format($row['mkt']['amount'],4,".","");?></td>
                    
					<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['pre']['amount']>0) echo number_format($row['pre']['amount']/$row['pre']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['pre']['amount'],4,".","")>number_format($row['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($row['pre']['amount'],4);$GrandTotalPreValue+=number_format($row['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format(($row['pre']['amount']/$jobValue)*100,4); ?></td>
                     <td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? if($row['acl']['amount']>0) echo number_format($row['acl']['amount']/$row['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($row['acl']['amount'],4,".","")>number_format($row['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($row['acl']['amount'],4);$GrandTotalAclValue+=number_format($row['acl']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($row['acl']['amount']/$jobValue)*100,4); ?></td>
				</tr>
                <?
				$w++;
				$total_mkt_qty+=$row['mkt']['qty'];
				$total_mkt_amt+=$row['mkt']['amount'];
				$total_pre_qty+=$row['pre']['qty'];
				$total_pre_amt+=$row['pre']['amount'];
				$total_acl_qty+=$row['acl']['qty'];
				$total_acl_amt+=$row['acl']['amount'];
				}
				?>
                 <tr style="font-weight:bold;background-color:#CCC">
					<td width="300" ><span id="washtotal"  class="adl-signs" onClick="yarnT(this.id,'.wash')">+</span>&nbsp;&nbsp;Wash Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo number_format($total_mkt_qty,4); ?></td>
					<td width="100" align="right"><? if($total_mkt_amt>0) echo number_format($total_mkt_amt/$total_mkt_qty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($total_mkt_amt,4); //$totalOtherMktValue+=number_format($total_mkt_amt,4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($total_pre_qty,4); ?></td>
					<td width="100" align="right"><? if($total_pre_amt>0) echo number_format($total_pre_amt/$total_pre_qty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_pre_amt,4,".","")>number_format($total_pre_amt,4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($total_pre_amt,4);//$totalOtherPreValue+=number_format($total_pre_amt,4,".",""); ?></td>
                      <td width="100" align="right"><?  if($total_pre_amt>0)  echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                      
					<td width="100" align="right"><? echo number_format($total_acl_qty,4); ?></td>
                   
					<td width="100" align="right"><? if($total_acl_amt>0) echo number_format($total_acl_amt/$total_acl_qty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($total_acl_amt,4,".","")>number_format($total_acl_amt,4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($total_acl_amt,4);//$totalOtherAclValue+=number_format($total_acl_amt,4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($total_acl_amt/$jobValue)*100,4); ?></td>
				</tr>
                <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="300" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['mkt']['amount']>0) echo number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=number_format($embData['mkt']['amount'],4,".","");?></td>
                   
                    
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? if($embData['pre']['amount']>0) echo number_format($embData['pre']['amount']/$embData['pre']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100" align="right"><? echo number_format($embData['acl']['qty'],4); ?></td>

					<td width="100" align="right"><? if($embData['acl']['amount']) echo number_format($embData['acl']['amount']/$embData['acl']['qty'],4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($embData['acl']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
                
                
                
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="300" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=number_format($commiData['pre']['amount'],4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commiData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? //echo $exfactoryQty ?></td>
					<td width="100" align="right"><? //$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty; echo number_format($aclCommiAmt/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? //echo number_format($aclCommiAmt,4); $totalOtherAclValue+=$aclCommiAmt; ?></td>
                    <td width="100" align="right"><? //echo number_format(($aclCommiAmt*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="300" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($commaData['pre']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; //$commer_act_cost=($unitPrice*$exfactoryQty)*(0.5/100); ?></td>
					<td width="100" align="right"><?  if($commaData['acl']['amount']>0 && $exfactoryQty>0) echo  number_format($commaData['acl']['amount']/$exfactoryQty,4);else echo ""; ?></td>

					<td width="100" align="right" title="" bgcolor="<? //if(number_format($exfactoryQty*$unitPrice)>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					$commer_act_cost=$commaData['acl']['amount'];
					echo number_format($commer_act_cost,4); $totalOtherAclValue+=number_format($commer_act_cost,4,".",""); ?></td>
                    <td width="100" align="right"><? echo number_format(($commer_act_cost*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="300" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['freight']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"  bgcolor="<? //if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                     
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="300" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['ore']['freight']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><?  echo $exfactoryQty ?></td>
					<td width="100" align="right"><? 
					$test_act_cost=$otherData['acl']['lab_test']['amount'];
					//$test_act_cost=$unitPrice*$exfactoryQty*(0.05/100);
					 if($test_act_cost>0 && $exfactoryQty>0) echo number_format($test_act_cost/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" title="" bgcolor="<? //if(number_format($test_act_cost,4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? 
					
					echo number_format($test_act_cost,4); $totalOtherAclValue+=number_format($test_act_cost,4,".","");?></td>	
                     <td width="100" align="right"><? echo number_format(($test_act_cost*100/$jobValue),2).'%'; ?></td>				
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="300" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? if($otherData['acl']['inspection']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['inspection']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['inspection']['amount']*100/$jobValue),2).'%'; ?></td>	
				</tr>
				<tr bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="300" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['pre']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>	
                     
					<td width="100" align="right"><?  echo $exfactoryQty; ?></td>
					<td width="100" align="right"><?  if($otherData['acl']['currier_pre_cost']['amount']>0 && $exfactoryQty>0) echo number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right" bgcolor="<? //if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
                     <td width="100" align="right"><? echo number_format(($otherData['acl']['currier_pre_cost']['amount']*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="300" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
                    <td width="100" align="right"><? echo number_format(($otherData['pre']['cm_cost']['amount']*100/$jobValue),2).'%'; ?></td>
                    
					<?
					if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")) $font_color="white";
					else $font_color="black";
					$making_cost_act=$otherData['pre']['cm_cost']['rate']*$exfactoryQty*(80/100); 
					?>
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? if($making_cost_act>0 && $exfactoryQty>0) echo number_format($making_cost_act/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" title="(Pre Cost Rate(<? echo $otherData['pre']['cm_cost']['rate'];?>)*Ex Fact Qty(<? echo $exfactoryQty;?>)*80)/100)"  align="right"  bgcolor="<? if(number_format($making_cost_act,4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=number_format($making_cost_act,4,".",""); echo number_format($making_cost_act,4); ?>
                    </td>
                     <td width="100" align="right" title="Making Cost Actual/Job Value*100"><? echo number_format(($making_cost_act*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="300" >Others Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($totalOtherMktValue,4); $GrandTotalMktValue+=number_format($totalOtherMktValue,4,".",""); ?></td>
					
                    <td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? echo number_format($totalOtherPreValue,4); $GrandTotalPreValue+=number_format($totalOtherPreValue,4,".",""); ?>
					</td>
                   <td width="100" align="right"><? echo number_format(($totalOtherPreValue*100/$jobValue),2).'%'; ?></td>
                   
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? echo number_format($totalOtherAclValue,4); $GrandTotalAclValue+=number_format($totalOtherAclValue,4,".",""); ?>
					</td>
                    <td width="100" align="right"><? echo number_format(($totalOtherAclValue*100/$jobValue),2).'%'; ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="300" >Grand Total</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
                    
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
                     <td width="100" align="right"><? echo number_format(($GrandTotalPreValue*100/$jobValue),2).'%'; ?></td>
                     
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
                    <td width="100" align="right"><? echo number_format(($GrandTotalAclValue*100/$jobValue),2).'%'; ?></td>
                    
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="300" >Shipment Value</td>
					<td width="60">&nbsp;</td>
					<td width="100" align="right"><? echo $quaOfferQnty;  ?></td>
					<td width="100" align="right" title="<? echo $quaPriceWithCommnPcs;?>"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right"><? $quaOfferValue=$quaOfferQnty*$quaPriceWithCommnPcs; echo number_format($quaOfferValue,4) ?></td>
					
                    <td width="100" align="right"><? echo $jobQty; ?></td>
					<td width="100" align="right" title="<? echo $unitPrice;?>"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
                    <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
                    
					<td width="100" align="right"><? echo $exfactoryQty ; ?></td>
					<td width="100" align="right" title="<? echo $exfactoryValue/$exfactoryQty;?>"><? if($exfactoryValue>0 && $exfactoryQty>0) echo number_format($exfactoryValue/$exfactoryQty,4);else echo ""; ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4); ?></td>
                      <td width="100" align="right"><? echo number_format(($jobValue*100/$jobValue),2).'%'; ?></td>
				</tr>
			</table>
			<table style="width:120px;float: left; margin-left: 5px; margin-top: 10px;">
				<tr>
					
                    <?
                    $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in('$jobNumber') and form_name='knit_order_entry' and file_type=1");
					if(count( $nameArray_img)>0)
					{
					    ?>
						    
                            
							<? 
							foreach($nameArray_img AS $inf) { 
								?>
                                 <td width="120">
                                <div style="float:left;width:120px" >
								<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='75' width='95' />
                                 </td>
                               </div>
							    <?  
							}
							?>
							
						<?								
					}
					else echo ""; ?>						    
                   
				</tr>
            </table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="10"><p style="font-weight: bold;">Profit Summary</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"  title="Total Mkt Value/Offer Value*100"><? if($GrandTotalMktValue>0) echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right" title="Total Expand/Offer Value*100"><? if($yarnTrimMktValue>0 && $quaOfferValue>0) echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4);else echo ""; ?></td>
                    <td width="150" align="right"><? echo number_format($total_mkt_amt,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash/Offer Value*100"><? if($total_mkt_amt>0 && $quaOfferValue>0) echo number_format(($total_mkt_amt/$quaOfferValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Offer Value-Grand Total Mkt Value"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right" title="Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Offer Value(<? echo $quaOfferValue;?>)*100" ><? if($quaOfferValue-$GrandTotalMktValue>0 && $quaOfferValue>0) echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4);else echo ""; ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Total Pre Value/Job Value*100"><? if($GrandTotalPreValue>0 && $jobValue>0) echo number_format(($GrandTotalPreValue/$jobValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? if($yarnTrimPreValue>0 && $jobValue>0) echo number_format(($yarnTrimPreValue/$jobValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_pre_amt,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><? if($total_pre_amt>0 && $jobValue>0) echo number_format(($total_pre_amt/$jobValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Job Value-Grand Total Pre Value"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Ex-Fact Value(<? echo $jobValue;?>)*100"><?  if($jobValue-$GrandTotalPreValue>0 && $jobValue>0)  echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);else echo "";?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Total Actual Value/Ex-Fact Value*100"><? if($GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Ex-fact Value*100"><?  if($yarnTrimAclValue>0 && $exfactoryValue>0) echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? echo number_format($total_acl_amt,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Ex-Fact Value*100"><? if($total_acl_amt>0 && $exfactoryValue>0) echo number_format(($total_acl_amt/$exfactoryValue)*100,4);else echo ""; ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value-Grand Total Acl Value"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right" title="Ex-fact Value(<? echo $exfactoryValue;?>)-GrandTotal Ex Value(<? echo $GrandTotalAclValue;?>)/Ex-Fact Value(<? echo $exfactoryValue;?>)*100"><? if($exfactoryValue-$GrandTotalAclValue>0 && $exfactoryValue>0) echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);else echo "";?></td>
				</tr>
			</table>
            <br>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr><td colspan="11"><p style="font-weight: bold;">Profit Summary 1 Pcs……</p></td></tr>
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">FOB/Pcs</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Trims</td>
					<td width="100">Trims %</td>
                    <td width="150">Wash</td>
					<td width="100">Wash %</td>
                    <td width="100">CM Cost</td>
                    
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right" title="Mkt Cost/Job Qty"><? $tot_offer_val_pcs=$unitPrice;//$quaOfferValue/$jobQty;
					echo number_format($tot_offer_val_pcs,4) ?></td>
					<td width="150" align="right" title="Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $tot_mkt_val_pcs=$GrandTotalMktValue/$jobQty;echo number_format($tot_mkt_val_pcs,4); ?></td>
					<td width="100" align="right"  title="Total Expenditure(<? echo $tot_mkt_val_pcs;?>)/FOB Pcs(<? echo $tot_offer_val_pcs;?>)*100"><? if($tot_mkt_val_pcs>0) echo number_format(($tot_mkt_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right" title="Total Trims Mkt value(<? echo $yarnTrimMktValue?>)/Job Qty(<? echo $jobQty?>)"><? 
					if($yarnTrimMktValue>0)
					{
					$tot_trim_val_pcs=$yarnTrimMktValue/$jobQty;
					}
					else $tot_trim_val_pcs=0;
					echo number_format($tot_trim_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Trims(<? echo $tot_trim_val_pcs?>)/FOB Pcs(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_trim_val_pcs>0) echo number_format(($tot_trim_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                    <td width="150" align="right" title="Total Mkt Trims(<? echo $total_mkt_amt?>)/Job Qty(<? echo $jobQty?>)"><? $tot_wash_val_pcs=$total_mkt_amt/$jobQty; echo number_format($tot_wash_val_pcs,4) ?></td>
					<td width="100" align="right" title="Total Mkt Wash(<? echo $tot_wash_val_pcs?>)/Offer Value(<? echo $tot_offer_val_pcs?>)*100"><? if($tot_wash_val_pcs>0) echo number_format(($tot_wash_val_pcs/$tot_offer_val_pcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="CM Cost(<? echo $otherData['mkt']['cm_cost']['amount'];?>)/Job Qty"><? $tot_mkt_cm_pcs=$otherData['mkt']['cm_cost']['amount']/$jobQty; echo number_format($tot_mkt_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Qua.Offer Value(<? echo $quaOfferValue;?>)-Grand Total Mkt Value(<? echo $GrandTotalMktValue;?>)/Job Qty"><? $profit_mkt_margin_pcs=($quaOfferValue-$GrandTotalMktValue)/$jobQty;echo number_format($profit_mkt_margin_pcs,4); ?></td>
					<td width="100" align="right" title="Profit margin(<? echo $profit_mkt_margin_pcs;?>)/Mkt FOB/Pcs (<? echo $tot_offer_val_pcs;?>)*100)"><? echo number_format(($profit_mkt_margin_pcs/$tot_offer_val_pcs)*100,4); ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right" title="Pre Cost/Job Qty"><? $tot_preJob_value=$unitPrice;//$jobValue/$jobQty;
					echo number_format($tot_preJob_value,4) ?></td>
					<td width="150" align="right"><? $tot_pre_costing_expnd=$GrandTotalPreValue/$jobQty; echo number_format($tot_pre_costing_expnd,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? $tot_pre_costing_expnd_per=$tot_pre_costing_expnd/$tot_preJob_value*100;echo number_format($tot_pre_costing_expnd_per,4); ?></td>
					<td width="150" align="right" title="Total Trims Value/Job Qty"><? $tot_preTrim_costing=$yarnTrimPreValue/$jobQty;echo number_format($tot_preTrim_costing,4) ?></td>
					<td width="100" align="right" title="Total Trims/Job Value*100"><? $tot_preTrim_costing_per=($tot_preTrim_costing/$tot_preJob_value)*100;echo number_format($tot_preTrim_costing_per,4); ?></td>
                     <td width="150" align="right" title="Pre Wash Cost/Job Qty"><?  $tot_preWash_value=$total_pre_amt/$jobQty;echo number_format($tot_preWash_value,4) ?></td>
					<td width="100" align="right" title="Total Pre Wash/Job Value*100"><?  $tot_preWash_value_pre=$tot_preWash_value/$tot_preJob_value*100;echo number_format($tot_preWash_value_pre,4); ?></td>
                     <td width="150" align="right" title="Cm Cost(<? echo $otherData['pre']['cm_cost']['amount'];?>)/Job Qty"><? $tot_pre_cm_pcs=$otherData['pre']['cm_cost']['amount']/$jobQty; echo number_format($tot_pre_cm_pcs,4); ?></td>
                    
					<td width="200" align="right" title="Job Value(<? echo $jobValue;?>)-Grand Total Pre Value(<? echo $GrandTotalPreValue;?>)/Job Qty"><? $profit_pre_margin_pcs=($jobValue-$GrandTotalPreValue)/$jobQty;echo number_format($profit_pre_margin_pcs,4);?></td>
					<td width="100" align="right" title="Profit Margin(<? echo $profit_pre_margin_pcs;?>)/Pre FOB Pcs (<? echo $tot_preJob_value;?>)*100)"><? echo number_format(($profit_pre_margin_pcs/$tot_preJob_value)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right" title="Acl Job Value/Job Qty"><? $tot_AclJob_valuePcs=$unitPrice;//$exfactoryValue/$jobQty;
					echo number_format($tot_AclJob_valuePcs,4) ?></td>
					<td width="150" align="right" title="Grand total Acl value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><? $tot_AclJob_ExpndPcs=$GrandTotalAclValue/$jobQty; echo number_format($tot_AclJob_ExpndPcs,4);?></td>
					<td width="100" align="right" title="Total Expenditure/Job Value*100"><? if($tot_AclJob_ExpndPcs>0) echo number_format(($tot_AclJob_ExpndPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
					<td width="150" align="right"  title="Total Acl Trims Value/Job Qty"><? $tot_AcltrimValue=$yarnTrimAclValue/$jobQty;echo number_format($tot_AcltrimValue,4) ?></td>
					<td width="100" align="right" title="Total Trims Acl/Job Value*100"><? if($tot_AcltrimValue>0) echo number_format(($tot_AcltrimValue/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right"><? $tot_AclJobWashPcs=$total_acl_amt/$jobQty; echo number_format($tot_AclJobWashPcs,4) ?></td>
					<td width="100" align="right" title="Total Wash Acl/Job Value*100"><? if($tot_AclJobWashPcs>0) echo number_format(($tot_AclJobWashPcs/$tot_AclJob_valuePcs)*100,4);else echo ""; ?></td>
                     <td width="150" align="right" title="Making Cost(<? echo $making_cost_act;?>)/Job Qty"><?  $tot_cmAcl_pcs=$making_cost_act/$jobQty;; echo number_format($tot_cmAcl_pcs,4) ?></td>
                    
					<td width="200" align="right" title="Ex-Fact Value(<? echo $exfactoryValue;?>)-Grand Total Acl Value(<? echo $GrandTotalAclValue;?>)/Job Qty(<? echo $jobQty;?>)"><?  $profit_acl_margin_pcs=($exfactoryValue-$GrandTotalAclValue)/$jobQty; echo number_format($profit_acl_margin_pcs,4);?></td>

					<td width="100" align="right" title="Profit margin(<? echo $profit_acl_margin_pcs;?>)/Actual FOB/Pcs (<? echo $tot_AclJob_valuePcs;?>)*100)"><? if($profit_acl_margin_pcs>0) echo number_format(($profit_acl_margin_pcs/$tot_AclJob_valuePcs)*100,4);else echo "";?></td>
				</tr>
			</table>
             <br>
              <br>
		</div>
		<?
	}
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
	
	exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	$po_id = implode(",",explode("_",$po_id));
	 $sql_job=sql_select("select a.quotation_id,b.job_no_mst from wo_po_details_master a,wo_po_break_down b where b.job_id=a.id and b.id in($po_id)");
		foreach($sql_job as $row)
		{
			$quotationId=$row[csf('quotation_id')];
			$job_no=$row[csf('job_no_mst')];
		}
	?>
	<script type="text/javascript">
		function generate_worder_report(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, entry_form, is_short) {
			if (entry_form == 108)
			{
				var action_method = "action=print_booking_3";
			}
			else
			{
				var action_method = "action=show_fabric_booking_report3";
			}

			if (type == 1) {

				if(is_short==2){

					if (entry_form == 108) {
						report_title = "&report_title=Partial Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/partial_fabric_booking_controller.php", true);
					}
					else if (entry_form == 118) {
						report_title = "&report_title=Partial Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
					}
					 else {
						report_title = "&report_title=Main Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/fabric_booking_controller.php", true);
					}
				}
				if(is_short==1){
					report_title = "&report_title=Short Fabric Booking";
					http.open("POST", "../../../../order/woven_order/requires/short_fabric_booking_controller.php", true);
				}
			}else {
				report_title = "&report_title=Sample Fabric Booking Urmi";
				http.open("POST", "../../../../order/woven_order/requires/sample_booking_controller.php", true);
			}

			var data = action_method + report_title +
			'&txt_booking_no=' + "'" + booking_no + "'" +
			'&cbo_company_name=' + "'" + company_id + "'" +
			'&txt_order_no_id=' + "'" + order_id + "'" +
			'&cbo_fabric_natu=' + "'" + fabric_nature + "'" +
			'&cbo_fabric_source=' + "'" + fabric_source + "'" +
			'&id_approved_id=' + "'" + approved + "'" +
			'&txt_job_no=' + "'" + job_no + "'";
			freeze_window(5);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}

		function generate_fabric_report_reponse() {
			release_freezing();
			if (http.readyState == 4) {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
				d.close();
			}
		}
	</script>
	<?
	//$po_id = implode(",",explode("_",$po_id));
	
	$requisition_details = sql_select("select a.booking_no, c.requisition_no from ppl_planning_info_entry_mst a,ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.dtls_id=c.knit_id and b.po_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	$requisition_arr = array();
	foreach ($requisition_details as $row) {
		$requisition_arr[$row[csf("requisition_no")]] = $row[csf("booking_no")];
	}

	$booking_details=sql_select("select c.booking_no, c.booking_type, c.is_short, d.entry_form, d.company_id, d.item_category, d.fabric_source, c.job_no, d.is_approved from wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d where b.id=c.po_break_down_id and d.id=c.booking_mst_id and b.id in($po_id) and c.booking_type in(1,4) and c.status_active=1 group by c.booking_no, c.booking_type, c.is_short, d.entry_form, d.company_id, d.item_category, d.fabric_source, c.job_no, d.is_approved");
	foreach($booking_details as $row)
	{
		$booking_arr[$row[csf("booking_no")]]['booking_type']=$row[csf("booking_type")];
		$booking_arr[$row[csf("booking_no")]]['is_short']=$row[csf("is_short")];
		$booking_arr[$row[csf("booking_no")]]['entry_form']=$row[csf("entry_form")];
		$booking_arr[$row[csf("booking_no")]]['company_id']=$row[csf("company_id")];
		$booking_arr[$row[csf("booking_no")]]['item_category']=$row[csf("item_category")];
		$booking_arr[$row[csf("booking_no")]]['job_no']=$row[csf("job_no")];
		$booking_arr[$row[csf("booking_no")]]['is_approved']=$row[csf("is_approved")];
		$booking_arr[$row[csf("booking_no")]]['fabric_source']=$row[csf("fabric_source")];
	}

	$sql_issue_return="SELECT a.id, a.trans_id, a.trans_type, a.entry_form, a.po_breakdown_id, a.prod_id, a.quantity as quantity, a.issue_purpose, a.returnable_qnty, a.is_sales, b.yarn_count_id, b.detarmination_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, c.cons_rate, c.cons_amount, c.issue_id, d.booking_no as requisition_no, d.receive_basis from order_wise_pro_details a, product_details_master b, inv_transaction c, inv_receive_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=4 and a.entry_form in(9) and b.dyed_type!=1 and a.po_breakdown_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id";
	$issue_return=sql_select($sql_issue_return);
	
	$issue_return_arr = $booking_req_return = array();
	foreach ($issue_return as $row) {
		$bookingNo='';
		$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";

		if($row[csf("receive_basis")] == 1)
		{

		}
		else
		{
			//$booking_req_return[$requisition_arr[$row[csf("requisition_no")]]] = $row[csf("requisition_no")];
			$bookingNo=$requisition_arr[$row[csf("requisition_no")]];
			//$issue_return_arr[$row[csf("prod_id")]][$bookingNo]['ret_issue_no'].= $row[csf("issue_number")].',';
			$issue_return_arr[$row[csf("prod_id")]][$bookingNo]["qty"] += $row[csf("quantity")];
		}
		
	}
	//echo '<pre>';print_r($issue_return_arr);
	//print_r($issue_return_arr);
	$sql_issue_details="SELECT a.po_breakdown_id, a.prod_id, a.quantity as quantity, a.issue_purpose, b.detarmination_id, b.product_name_details, a.prod_id, b.yarn_count_id, b.detarmination_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, c.receive_basis, d.booking_no, d.issue_number, c.requisition_no from order_wise_pro_details a, product_details_master b, inv_transaction c, inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=2 and a.entry_form=3 and b.dyed_type!=1 and d.issue_purpose in(1,4) and a.po_breakdown_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by d.issue_number";
	$issue_details = sql_select($sql_issue_details);
	//$issue_arr = $booking_req = array();
	$issue_dtls_arr=array();
	foreach ($issue_details as $row) 
	{
		$bookingNo='';
		$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
		if($row[csf("receive_basis")] == 1)
		{
			$issue_dtls_arr[$row[csf("prod_id")]][$row[csf("booking_no")]]['booking']= $row[csf("booking_no")];
			$issue_dtls_arr[$row[csf("prod_id")]][$row[csf("booking_no")]]['issue_no'].= $row[csf("issue_number")].',';
			$issue_dtls_arr[$row[csf("prod_id")]][$row[csf("booking_no")]]['issue_qnty'] += $row[csf("quantity")];
			$issue_dtls_arr[$row[csf("prod_id")]][$row[csf("booking_no")]]['desc'] = $row[csf("product_name_details")];
		} 
		else 
		{
			//$issue_arr[$index]['issue_qnty'] += $row[csf("quantity")]-$issue_return_arr[$index]["qty"];
			$booking_req[$requisition_arr[$row[csf("requisition_no")]]] = $row[csf("requisition_no")];
			//$issue_dtls_arr[$row[csf("prod_id")]]['booking']=$requisition_arr[$row[csf("requisition_no")]];
			$bookingNo=$requisition_arr[$row[csf("requisition_no")]];
			$issue_dtls_arr[$row[csf("prod_id")]][$bookingNo]['booking']= $bookingNo;
			$issue_dtls_arr[$row[csf("prod_id")]][$bookingNo]['prod_id']= $row[csf("prod_id")];
			$issue_dtls_arr[$row[csf("prod_id")]][$bookingNo]['issue_no'].= $row[csf("issue_number")].',';
			$issue_dtls_arr[$row[csf("prod_id")]][$bookingNo]['issue_qnty'] += $row[csf("quantity")];
			$issue_dtls_arr[$row[csf("prod_id")]][$bookingNo]['desc'] = $row[csf("product_name_details")];
		}
	}

	//echo '<pre>';print_r($issue_dtls_arr);
	
	$sql_yarn_recv="select a.booking_id, b.prod_id, b.job_no, b.cons_amount, b.cons_quantity, c.product_name_details from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.job_no ='".$job_no."' and a.entry_form=1 and a.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$yarn_recv =sql_select($sql_yarn_recv);

	foreach ($yarn_recv as $row) {
		$recv_yarn_arr[$row[csf("prod_id")]][$row[csf("booking_id")]]['recv_qty']+=$row[csf("cons_quantity")];
		$recv_yarn_arr[$row[csf("prod_id")]][$row[csf("booking_id")]]['amt']+=$row[csf("cons_amount")];
		$recv_yarn_arr[$row[csf("prod_id")]][$row[csf("booking_id")]]['job_no']=$row[csf("job_no")];
		$recv_yarn_arr[$row[csf("prod_id")]][$row[csf("booking_id")]]['booking_id']=$row[csf("booking_id")];
		$recv_yarn_arr[$row[csf("prod_id")]][$row[csf("booking_id")]]['desc']=$row[csf("product_name_details")];
	}
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");	
	echo load_freeze_divs ("../../",$permission);  
	?>
	<table class="rpt_table" border="1" cellpadding="5" cellspacing="2" width="100%" rules="all">
		<caption> <b>Yarn Issue </b> </caption>
		<thead>
			<th>Yarn Description</th>
			<th>Booking Type</th>
			<th>Booking NO</th>
			<th>Yarn Issue Qty</th>
		</thead>
		<?php
		$issue_total = 0;$m=1;
		foreach ($issue_dtls_arr as $key=>$prod_id) 
		{
			foreach ($prod_id as $booking_no => $row) 
			{
				if($m%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
			
				$booking=$row['booking'];
				$booking_type=$booking_arr[$booking]['booking_type'];
				$is_short=$booking_arr[$booking]['is_short'];
				$entry_form=$booking_arr[$booking]['entry_form'];
				$company_id=$booking_arr[$booking]['company_id'];
				$item_category=$booking_arr[$booking]['item_category'];
				$job_no=$booking_arr[$booking]['job_no'];
				$is_approved=$booking_arr[$booking]['is_approved'];
				$fabric_source_id=$booking_arr[$booking]['fabric_source'];
				$detarmination_id=$issue_arr2[$prod_id][$booking]['detarmination_id'];

				$des=explode("_",str_replace("'","",$desc));
				$item_descrition =$row['desc'];

				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $m; ?>" style="font-size:11px">
					<td align="center" ><? echo $item_descrition;?></td>
					<td align="center">
						<?
							if($booking_type == 1){
								if($is_short == 1){
									echo "Short Fabric Booking";
								}else{
									echo "Main Fabric Booking";
								}
							} else if($booking_type == 4){
								echo "Sample Fabric Booking";
							}
						?>
					</td>
					<td align="center">
						<?
						if($booking_type == 1 && $is_short == 2)
						{
							?>
							<span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_worder_report(<? echo $booking_type;?>,'<? echo $booking;?>',<? echo $company_id;?>,'<? echo $po_id;?>',<? echo $item_category;?>,<? echo $fabric_source_id;?>,'<? echo $job_no;?>',<? echo $is_approved;?>,<? echo $entry_form;?>,<? echo $is_short;?>)"><? echo $booking;?></span>
							<?
						} else {
							echo $booking;
						}
						?>
					</td>
					<td align="right">
						<?
						$issue_return_qty=$issue_return_arr[$row['prod_id']][$booking_no]["qty"];
						$issue_qnty =$row['issue_qnty']-$issue_return_qty;
						echo $issue_qnty = number_format(($issue_qnty),2,'.','');
						?>
					</td>
				</tr>
				<?
				$issue_total += $issue_qnty;
				$m++;
			}
		}
		?>
		<tfoot>
		<tr>
			<th colspan="3" align="right">Grand Total</th>
			<th align="right"><? echo number_format($issue_total,2,'.','');?></th>
		</tr>
		</tfoot>
	</table>
	<br>
	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
		<caption> <b>Yarn Recv </b> </caption>
		<thead>
			<th>Yarn Description</th>
			<th>Booking Type</th>
			<th>Booking NO</th>
			<th>Yarn Recv Qty</th>
		</thead>
		<?
		$recv_qty_total = 0;$m=1;
		foreach ($recv_yarn_arr as $key=>$prod_id) 
		{
			foreach ($prod_id as $booking_id=>$row) 
			{
				if($m%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
				$ydw_no = return_field_value("ydw_no as ydw_no", "wo_yarn_dyeing_mst", "id=".$row['booking_id']." ", "ydw_no");
				// echo $row['booking_id'].', ';
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trrc_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trrc_<? echo $m; ?>" style="font-size:11px">
					<td align="center"><? echo $row['desc']; ?></td>
					<td align="center">
						<? echo "Yarn Dyeing Booking"; ?>
					</td>
					<td align="center">
						<? echo $ydw_no; ?>

					</td>
					<td align="right">
						<?
						$recv_qty =$row['recv_qty'];
						echo $recv_qty = number_format(($recv_qty),2,'.','');
						?>
					</td>
				</tr>
			    <?
				$recv_qty_total += $recv_qty;
				$m++;
			}
		}
		?>
		<tfoot>
		<tr>
			<th colspan="3" align="right">Grand Total</th>
			<th align="right"><? echo number_format($recv_qty_total,2,'.','');?></th>
		</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if($action=="issue_popup_old")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	$po_id = implode(",",explode("_",$po_id));
	//$sql_job="select job_no_mst from wo_po_break_down where and id in($po_id) ";
	 $job_no = return_field_value("job_no_mst as job_no_mst", "wo_po_break_down", "id in($po_id)", "job_no_mst");

	?>
	<script type="text/javascript">
		function generate_worder_report(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, entry_form, is_short) {
			if (entry_form == 108)
			{
				var action_method = "action=print_booking_3";
			}
			else
			{
				var action_method = "action=show_fabric_booking_report3";
			}

			if (type == 1) {

				if(is_short==2){

					if (entry_form == 108) {
						report_title = "&report_title=Partial Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/partial_fabric_booking_controller.php", true);
					}
					else if (entry_form == 118) {
						report_title = "&report_title=Partial Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
					}
					 else {
						report_title = "&report_title=Main Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/fabric_booking_controller.php", true);
					}
				}
				if(is_short==1){
					report_title = "&report_title=Short Fabric Booking";
					http.open("POST", "../../../../order/woven_order/requires/short_fabric_booking_controller.php", true);
				}
			}else {
				report_title = "&report_title=Sample Fabric Booking Urmi";
				http.open("POST", "../../../../order/woven_order/requires/sample_booking_controller.php", true);
			}

			var data = action_method + report_title +
			'&txt_booking_no=' + "'" + booking_no + "'" +
			'&cbo_company_name=' + "'" + company_id + "'" +
			'&txt_order_no_id=' + "'" + order_id + "'" +
			'&cbo_fabric_natu=' + "'" + fabric_nature + "'" +
			'&cbo_fabric_source=' + "'" + fabric_source + "'" +
			'&id_approved_id=' + "'" + approved + "'" +
			'&txt_job_no=' + "'" + job_no + "'";

			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}

		function generate_fabric_report_reponse() {
			if (http.readyState == 4) {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
				d.close();
			}
		}
	</script>
	<?
	
	$requisition_details = sql_select("select a.booking_no,c.requisition_no from ppl_planning_info_entry_mst a,ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.dtls_id=c.knit_id and b.po_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	$requisition_arr = array();
	foreach ($requisition_details as $row) {
		$requisition_arr[$row[csf("requisition_no")]] = $row[csf("booking_no")];
	}

	$issue_return=sql_select("select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty, a.is_sales, b.yarn_count_id,b.detarmination_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount,c.issue_id,d.booking_no,c.receive_basis,c.requisition_no from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.issue_id=d.id and a.trans_type=4 and a.entry_form in(11,9) and a.po_breakdown_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id");
	$issue_return_arr = $booking_req_return = array();
	foreach ($issue_return as $row) {
	$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";

		if($row[csf("receive_basis")] == 1){

		}else{

			$booking_req_return[$requisition_arr[$row[csf("requisition_no")]]] = $row[csf("requisition_no")];
		}
		$issue_arr[$index]['issue_no'].= $row[csf("issue_number")].',';
		$issue_return_arr[$index]["qty"] += $row[csf("quantity")];
	}

	//print_r($issue_return_arr);
	 $issue_details = sql_select("select a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,b.detarmination_id,b.product_name_details,b.yarn_count_id,b.detarmination_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,c.receive_basis,d.booking_no,d.issue_number, c.requisition_no from order_wise_pro_details a,product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=2 and a.entry_form=3  and d.issue_purpose!=2 and a.po_breakdown_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id");

	$issue_arr = $booking_req = array();
	foreach ($issue_details as $row) {
	//$index="".$row[csf("product_name_details")]."";
	$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
		if($row[csf("receive_basis")] == 1){

			$issue_arr[$index]['booking']= $row[csf("booking_no")];
		}else{
			//$issue_arr[$index]['issue_qnty'] += $row[csf("quantity")]-$issue_return_arr[$index]["qty"];
			$booking_req[$requisition_arr[$row[csf("requisition_no")]]] = $row[csf("requisition_no")];
			$issue_arr[$index]['booking']= $requisition_arr[$row[csf("requisition_no")]];
		}
		$issue_arr[$index]['issue_no'].= $row[csf("issue_number")].',';
		$issue_arr[$index]['issue_qnty'] += $row[csf("quantity")];
		$issue_arr2[$index][$row[csf("booking_no")]]['detarmination_id']= $row[csf("detarmination_id")];


	}

	$booking_details=sql_select("select c.booking_no,c.booking_type,c.is_short,d.entry_form,d.company_id,d.item_category,d.fabric_source, c.job_no,d.is_approved from wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where b.id=c.po_break_down_id and d.id=c.booking_mst_id and b.id in($po_id) and c.booking_type in(1,4) and c.status_active=1 group by c.booking_no,c.booking_type,c.is_short,d.entry_form,d.company_id,d.item_category, d.fabric_source,c.job_no,d.is_approved");
	foreach($booking_details as $row)
	{
		$booking_arr[$row[csf("booking_no")]]['booking_type']=$row[csf("booking_type")];
		$booking_arr[$row[csf("booking_no")]]['is_short']=$row[csf("is_short")];
		$booking_arr[$row[csf("booking_no")]]['entry_form']=$row[csf("entry_form")];
		$booking_arr[$row[csf("booking_no")]]['company_id']=$row[csf("company_id")];
		$booking_arr[$row[csf("booking_no")]]['item_category']=$row[csf("item_category")];
		$booking_arr[$row[csf("booking_no")]]['job_no']=$row[csf("job_no")];
		$booking_arr[$row[csf("booking_no")]]['is_approved']=$row[csf("is_approved")];
		$booking_arr[$row[csf("booking_no")]]['fabric_source']=$row[csf("fabric_source")];

	}
	//
	 $yarn_recv =sql_select("select a.booking_id,b.prod_id,b.job_no,b.cons_amount,b.cons_quantity,c.product_name_details from inv_receive_master a,inv_transaction b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and  b.job_no ='".$job_no."' and a.entry_form=1 and a.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach ($yarn_recv as $row) {
		$recv_yarn_arr[$row[csf("prod_id")]]['recv_qty']+=$row[csf("cons_quantity")];
		$recv_yarn_arr[$row[csf("prod_id")]]['amt']+=$row[csf("cons_amount")];
		$recv_yarn_arr[$row[csf("prod_id")]]['job_no']=$row[csf("job_no")];
		$recv_yarn_arr[$row[csf("prod_id")]]['booking_id']=$row[csf("booking_id")];
		$recv_yarn_arr[$row[csf("prod_id")]]['desc']=$row[csf("product_name_details")];
	}

	?>
	<table class="rpt_table" border="1" cellpadding="5" cellspacing="2" width="100%" rules="all">
		<caption> <b>Yarn Issue </b> </caption>
		<thead>
			<th>Yarn Description</th>
			<th>Booking Type</th>
			<th>Booking NO</th>
			<th>Yarn Issue Qty</th>
		</thead>
		<?php
		$issue_total = 0;$m=1;
		foreach ($issue_arr as $desc=>$row) {
		if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//foreach ($desc_data as $booking=>$row) {
			//echo $desc.'D';
			$booking=$row['booking'];$issue_no=rtrim($row['issue_no'],',');
			$issue_nos=implode(', ',array_unique(explode(",",$issue_no)));
			$booking_type=$booking_arr[$booking]['booking_type'];
			$is_short=$booking_arr[$booking]['is_short'];
			$entry_form=$booking_arr[$booking]['entry_form'];
			$company_id=$booking_arr[$booking]['company_id'];
			$item_category=$booking_arr[$booking]['item_category'];
			$job_no=$booking_arr[$booking]['job_no'];
			$is_approved=$booking_arr[$booking]['is_approved'];
			$fabric_source_id=$booking_arr[$booking]['fabric_source'];
			$detarmination_id=$issue_arr2[$desc][$booking]['detarmination_id'];

			$des=explode("_",str_replace("'","",$desc));
			$item_descrition = $lib_yarn_count[$des[0]]." ".$composition[$des[1]]." ".$des[2]."% ".$yarn_type[$des[3]];


			?>
			 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $m; ?>" style="font-size:11px">
				<td align="center" title="<? echo $issue_nos;?>"><?php echo $item_descrition;?></td>
				<td align="center">
					<?php

						if($booking_type == 1){
							if($is_short == 1){
								echo "Short Fabric Booking";
							}else{
								echo "Main Fabric Booking";
							}
						} else if($booking_type == 4){
							echo "Sample Fabric Booking";
						}

					?>
				</td>
				<td align="center">
					<?php
					if($booking_type == 1 && $is_short == 2){
						?>
						<span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
						onClick="generate_worder_report(<?php echo $booking_type;?>,'<?php echo $booking;?>',<?php echo $company_id;?>,'<?php echo $po_id;?>',<?php echo $item_category;?>,<?php echo $fabric_source_id;?>,'<?php echo $job_no;?>',<?php echo $is_approved;?>,<?php echo $entry_form;?>,<?php echo $is_short;?>)"><?php echo $booking;?></span>
						<?php
					}else{
						echo $booking;
					}
					?>
				</td>
				<td align="right"  title="Ret Qty=<? echo $issue_return_arr[$desc]["qty"];?>">
					<?php
					$issue_qnty =$row['issue_qnty']-$issue_return_arr[$desc]["qty"];//$issue_arr[$booking_req[$booking]]['req_qnty'] + $issue_arr[$booking]["issue_qnty"];
					//$issue_return_qnty = $issue_return_arr[$booking][$detarmination_id]['qty'];
					echo $issue_qnty = number_format(($issue_qnty),2,'.','');
					?>
				</td>
			</tr>
			<?php
				$issue_total += $issue_qnty;
				$m++;
			//}
		}
		?>
		<tfoot>
		<tr>
			<th colspan="3" align="right">Grand Total</th>
			<th align="right"><?php echo number_format($issue_total,2,'.','');?></th>
		</tr>
		</tfoot>
	</table>
	<br>
	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
		<caption> <b>Yarn Recv </b> </caption>
		<thead>
			<th>Yarn Description</th>
			<th>Booking Type</th>
			<th>Booking NO</th>
			<th>Yarn Recv Qty</th>
		</thead>
		<?php
		$recv_qty_total = 0;$m=1;
		foreach ($recv_yarn_arr as $desc=>$row) {
		if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			 $ydw_no = return_field_value("ydw_no as ydw_no", "wo_yarn_dyeing_mst", "id=".$row['booking_id']." ", "ydw_no");
			// echo $row['booking_id'].', ';
			?>
			 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trrc_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trrc_<? echo $m; ?>" style="font-size:11px">
				<td align="center"><?php echo $row['desc'];?></td>
				<td align="center">
					<?php

						echo "Yarn Dyeing Booking";
					?>
				</td>
				<td align="center">
					<?php
							echo $ydw_no;
						?>

				</td>
				<td align="right">
					<?php
					$recv_qty =$row['recv_qty'];//$issue_arr[$booking_req[$booking]]['req_qnty'] + $issue_arr[$booking]["issue_qnty"];
					//$issue_return_qnty = $issue_return_arr[$booking][$detarmination_id]['qty'];
					echo $recv_qty = number_format(($recv_qty),2,'.','');
					?>
				</td>
			</tr>
			<?php
				$recv_qty_total += $recv_qty;
				$m++;

		}
		?>
		<tfoot>
		<tr>
			<th colspan="3" align="right">Grand Total</th>
			<th align="right"><?php echo number_format($recv_qty_total,2,'.','');?></th>
		</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if($action=="show_transfer_popup")
{
	echo load_html_head_contents("Transfer Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">

	</script>
	<?
	$po_ids = implode(",",explode("_",$po_id));
	//echo $po_ids;
	$desc_array=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );

	 $sql_wopo="select id,po_number from wo_po_break_down where  is_deleted=0 and status_active=1 ";
	$data_result=sql_select($sql_wopo);
	foreach($data_result as $row){
		$po_dtls_arr[$row[csf("id")]]['po_no']=$row[csf("po_number")];
	}

	
	$g_exchange_rate=str_replace("'","",$g_exchange_rate);



	?>
	 <script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

		d.close();
	}

	</script>
	<fieldset style="width:770px; margin-left:7px">
	<div id="report_container" align="center">
	 <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th colspan="7">Transfer In</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="115">Transfer Id</th>
					<th width="80">Transfer Date</th>
					<th width="100">From Order</th>
					<th width="170">Item Description</th>
					<th  width="100">Transfer Qnty</th>
					<th>Transfer Amount</th>
				</tr>
			</thead>
			<?
			$i = 1;
			$total_trans_in_qnty =$total_trans_in_amt= 0;
		
	 $sql_trim_trans="select c.transfer_system_id, c.transfer_date, c.challan_no, c.from_order_id,c.to_order_id, b.prod_id,c.from_order_id,b.quantity as transfer_qnty_in, (b.quantity*a.rate) as transfer_amount
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(5) and b.po_breakdown_id in(".$po_ids.")";//
		//$result_trim_trans=sql_select( $sql_trim_trans );
		 
			$result_in = sql_select($sql_trim_trans);
			foreach ($result_in as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
					$transfer_amount=$row[csf('transfer_amount')]/$g_exchange_rate;

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
					<td width="100"><p><? echo $po_dtls_arr[$row[csf("from_order_id")]]['po_no']; ?></p></td>
					<td width="170"><p><? echo $desc_array[$row[csf('prod_id')]]; ?></p></td>
					<td  width="100" align="right"><? echo number_format($row[csf('transfer_qnty_in')], 2); ?> </td>
					<td align="right" title="<? //echo number_format($avg_rate, 4); ?>"><? echo number_format($transfer_amount, 2);
					$transfer_amount=$row[csf('transfer_amount')]/$g_exchange_rate; echo number_format($transfer_amount, 2); ?> </td>
				</tr>
				<?
				$total_trans_in_qnty += $row[csf('transfer_qnty_in')];
				$total_trans_in_amt +=$transfer_amount;// ($row[csf('transfer_qnty')]*$avg_rate)/$g_exchange_rate;
				$i++;
			}
			?>
			<tr style="font-weight:bold">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right">Total</td>
			   <td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
				<td align="right"><? echo number_format($total_trans_in_amt, 2); ?></td>
			</tr>
			<thead>
				<tr>
					<th colspan="7">Transfer Out</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="115">Transfer Id</th>
					<th width="80">Transfer Date</th>
					<th width="100">To Order</th>
					<th width="170">Item Description</th>
					<th  width="100">Transfer Qnty</th>
					<th>Transfer Amount</th>
				</tr>
			</thead>
			<?
			$total_trans_out_qnty=$total_trans_out_amt=0;
			$sql_trim_trans="select c.transfer_system_id, c.transfer_date, c.challan_no, c.from_order_id,c.to_order_id, b.prod_id,c.from_order_id,b.quantity as transfer_qnty_in, (b.quantity*a.rate) as transfer_amount
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78,112) and b.trans_type in(6) and b.po_breakdown_id in(".$po_ids.")";
			$result = sql_select($sql);
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
					$avg_rate=$avg_rate_array[$row[csf("from_prod_id")]];
 				$transfer_amount=$row[csf('transfer_amount')]/$g_exchange_rate;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
					<td width="100"><p><? echo $po_dtls_arr[$row[csf("to_order_id")]]['po_no'] ?></p></td>
					<td width="170"><p><? echo $desc_array[$row[csf('prod_id')]]; ?></p></td>
					<td  width="100" align="right"><? echo number_format($row[csf('transfer_qnty_in')], 2); ?> </td>
					 <td align="right" title="Avg Rate: <? //echo $avg_rate; ?>"><? echo number_format($transfer_amount, 2);
					 
					  ?> </td>
				</tr>
				<?
				$total_trans_out_qnty += $row[csf('transfer_qnty_in')];
				 $total_trans_out_amt +=$transfer_amount	;// ($row[csf('transfer_qnty')]*$avg_rate)/$g_exchange_rate;
				$i++;
			}
			?>
			<tr style="font-weight:bold">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right">Total</td>
			   <td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
				<td align="right"><? echo number_format($total_trans_out_amt, 2); ?></td>
			</tr>
			<tfoot>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th>Net Transfer</th>
			<th align="right"><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
			<th align="right"><?  echo number_format($total_trans_in_amt  - $total_trans_out_amt , 2); ?></th>
			</tfoot>
		</table>
	</div>
	</fieldset>
	<?
}

if ($action == "show_finish_trans_popup") 
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	$po_id = implode(",",explode("_",$po_id));
	$po_no_arr = return_library_array("select id, po_number from wo_po_break_down ", "id", "po_number");
	//echo "select id, po_number from wo_po_break_down where id in($po_id)";
	$fabDesc_array=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
	$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		//echo $g_exchange_rate.'fff';
		//print_r($po_no_arr);
	?>
	<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

		d.close();
	}

	</script>
	<div style="width:775px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:770px; margin-left:7px">
	<div id="report_container">
		<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th colspan="7">Transfer In</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="115">Transfer Id</th>
					<th width="80">Transfer Date</th>
					<th width="100">From Order</th>
					<th width="170">Item Description</th>
					 <th width="100">Transfer Qnty</th>
					<th>Transfer Amount</th>
				</tr>
			</thead>
			<?
			
			$i = 1;
			$total_trans_in_qnty = $total_trans_in_amt=0;
			 $sql_fin_trans="select  c.transfer_system_id, c.transfer_date, c.challan_no, c.from_order_id,c.to_order_id, b.prod_id, 
			 (b.quantity) as transfer_qnty,a.cons_amount
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and b.po_breakdown_id in ($po_id) and a.item_category=3 and a.transaction_type in(5) and b.trans_type in(5) ";
		  
			$result_in = sql_select($sql_fin_trans);
			foreach ($result_in as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
					<td width="100"><p><? echo $po_no_arr[$row[csf('from_order_id')]]; ?></p></td>
					<td width="170"><p><? echo $fabDesc_array[$row[csf('prod_id')]]; ?></p></td>
					<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
					<td align="right" title="Avg  Rate: <? //echo $avg_rate; ?>"><? echo number_format($row[csf('cons_amount')]/$g_exchange_rate, 2); ?> </td>
				</tr>
				<?
				$total_trans_in_qnty += $row[csf('transfer_qnty')];
				 $total_trans_in_amt += $row[csf('cons_amount')]/$g_exchange_rate;
				$i++;
			}
			?>
			<tr style="font-weight:bold">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right">Total</td>
				<td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
				<td align="right"><? echo number_format($total_trans_in_amt, 2); ?></td>
			</tr>
			<thead>
				<tr>
					<th colspan="7">Transfer Out</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="115">Transfer Id</th>
					<th width="80">Transfer Date</th>
					<th width="100">To Order</th>
					<th width="170">Item Description</th>
					 <th width="100">Transfer Qnty</th>
					<th>Transfer Amount</th>
				</tr>
			</thead>
			<?
			$total_trans_out_qnty=$total_trans_out_amt=0;
			$total_trans_in_qnty = $total_trans_in_amt=0;
			 $sql_fin_out_trans="select  c.transfer_system_id, c.transfer_date, c.challan_no, c.from_order_id,c.to_order_id, b.prod_id, 
			 (b.quantity) as transfer_qnty,a.cons_amount,c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and b.po_breakdown_id in ($po_id) and a.item_category=3 and a.transaction_type in(6) and b.trans_type in(6) ";
			$result_out = sql_select($sql_fin_out_trans);
			foreach ($result_out as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
					<td width="100"><p><? echo $po_no_arr[$row[csf('to_order_id')]]; ?></p></td>
					<td width="170"><p><? echo $fabDesc_array[$row[csf('prod_id')]]; ?></p></td>
					<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
					<td align="right" title="Avg Rate: <? //echo $avg_rate; ?>"><? echo number_format($row[csf('cons_amount')]/$g_exchange_rate, 2); ?> </td>
				</tr>
				<?
				$total_trans_out_qnty += $row[csf('transfer_qnty')];
				$total_trans_out_amt += $row[csf('cons_amount')]/$g_exchange_rate;
				$i++;
			}
			?>
			<tr style="font-weight:bold">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right">Total</td>
				<td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
				 <td align="right"><? echo number_format($total_trans_out_amt, 2); ?></td>
			</tr>
			<tfoot>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th>Net Transfer</th>
			<th align="right"><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
			 <th align="right"><? echo number_format($total_trans_in_amt - $total_trans_out_amt, 2); ?></th>
			</tfoot>
		</table>
	</div>
	</fieldset>
	<?
	exit();
}

if ($action == "sample_fab_qnty_popup") 
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	$po_id = implode(",",explode("_",$po_id));
	$po_no_arr = return_library_array("select id, po_number from wo_po_break_down ", "id", "po_number");
	
	$fabDesc_array=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
	$job_no=str_replace("'","",$job_no);
		
	?>
	
	
	<fieldset style="width:470px; margin-left:7px">
	<div id="report_container">
		<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0">
			<thead>
				
				<tr>
					<th width="40">SL</th>
					<th width="125">WO No</th>
					<th width="80">WO Date</th>
					<th >Qty</th>
					
				</tr>
			</thead>
			<?
			
		
			$i = 1;
			$total_qnty = 0;
			 $booking_sql1="SELECT SUM(b.fin_fab_qnty) AS qnty,b.booking_no as booking_no,a.booking_date as booking_date
                          FROM wo_booking_dtls b,wo_booking_mst a
                         WHERE     a.status_active = 1
                               AND a.is_deleted = 0
                               AND b.status_active = 1
                               AND b.is_deleted = 0
                               AND b.job_no='$job_no'
                               AND b.entry_form_id = 440
                               AND b.fin_fab_qnty>0
               group by b.booking_no,a.booking_date 

               	";

             $booking_sql2=" SELECT f.booking_no as booking_no, f.booking_date as booking_date, sum(d.required_qty) as qnty
			  FROM wo_po_details_master a,
			       sample_development_mst c,
			       sample_development_fabric_acc d,
			       wo_booking_mst f
			 WHERE     a.status_active = 1
			       AND a.is_deleted = 0
			       AND c.status_active = 1
			       AND f.job_no = a.job_no
			       AND c.is_deleted = 0
			       AND d.sample_mst_id = c.id
			       AND c.quotation_id = a.id
			       AND d.form_type = 1
			       AND d.is_deleted = 0
			       AND d.status_active = 1
			       AND a.job_no = '$job_no'
			       AND f.entry_form = 440
			       AND d.required_qty>0
			  group by  f.booking_no, f.booking_date";
			    
			 $booking_sql3="SELECT b.booking_no as booking_no,
			         b.booking_date as booking_date,
			         SUM (a.fin_fab_qnty) AS qnty
			        
			    FROM wo_booking_dtls a, wo_booking_mst b
			   WHERE     b.id=a.booking_mst_id
			         AND a.is_short = 2
			         AND b.booking_type = 4
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND a.job_no = '$job_no'
			         AND a.fin_fab_qnty>0
			GROUP BY b.booking_no, b.booking_date
				 "; 
		    $booking_sql4="SELECT 
						       sum(b.grey_fab_qnty) as qnty,a.booking_no,a.booking_date
						  FROM wo_booking_mst a, wo_booking_dtls b
						 WHERE     a.booking_no = b.booking_no
						       AND a.booking_type = 1
						       AND a.fabric_source = 2
						        AND b.job_no='$job_no'
						       AND a.is_deleted = 0
						       AND a.status_active = 1
						       AND b.is_deleted = 0
						       AND b.status_active = 1
						       and a.item_category=3
						group by a.booking_no,a.booking_date
				 ";
		    //echo $booking_sql1;
			
			$result_booking1 = sql_select($booking_sql1);
			$result_booking2 = sql_select($booking_sql2);
			$result_booking3 = sql_select($booking_sql3);
			$result_booking4 = sql_select($booking_sql4);
			$i=1;
			foreach ($result_booking4 as $row) 
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="125"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?> </td>
				
				</tr>
				<?
				$total_qnty += $row[csf('qnty')];
				
				$i++;
			}
			foreach ($result_booking1 as $row) 
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="125"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?> </td>
				
				</tr>
				<?
				$total_qnty += $row[csf('qnty')];
				
				$i++;
			}
			foreach ($result_booking2 as $row) 
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="125"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?> </td>
				
				</tr>
				<?
				$total_qnty += $row[csf('qnty')];
				
				$i++;
			}
			foreach ($result_booking3 as $row) 
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="40"><? echo $i; ?></td>
					<td width="125"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?> </td>
				
				</tr>
				<?
				$total_qnty += $row[csf('qnty')];
				
				$i++;
			}
			
			?>
			<tfoot>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					
					<td align="right">Total</td>
					<td align="right"><? echo number_format($total_qnty, 2); ?></td>
					
				</tr>
			</tfoot>
			
			
		
		</table>
	</div>
	</fieldset>
	<?
	exit();
}
if($action=="mkt_cm_cost_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);


	?>
	<fieldset style="width:950px; margin-left:7px">
        <table class="rpt_table" width="950px" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b>Making Cost (CM) Actaul</b></caption>
            <thead>
                <th width="30">SL</th>
                <th width="110">Order Closing Date</th>
                <th width="100">Buyer</th>
                <th width="180">Style</th>
                <th width="110">Job No.</th>
                <th width="80">Used Min.</th>
                <th width="80">Produced Min</th>
                <th width="60">Effi%</th>
                <th width="60">CPM</th>
                <th>CM</th>
            </thead>
        </table>
        <div style="width:970px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="950px" cellpadding="0" cellspacing="0">
				<?
				$buyer_array = return_library_array("select id, buyer_name from lib_buyer ", "id", "buyer_name");
				$job_effi_array = return_library_array("select job_no, sew_effi_percent from wo_pre_cost_mst ", "job_no", "sew_effi_percent");

				$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company and status_active=1 and is_deleted=0  order by id desc");
			foreach($sql_std_para as $row )
			{
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
				}
			}

                $i=1;  $total_cm=$tot_used_min=$tot_produce_min=0;
				$sql_job="select  a.job_no,a.buyer_name,a.style_ref_no,b.production_date,b.produce_min,b.available_min from wo_po_details_master a,production_logicsoft b where a.job_no=b.jobNo and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.is_self_order=1  order by b.production_date desc";
				$result_job=sql_select( $sql_job );
                foreach($result_job as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$production_date=date("d-m-Y", strtotime($row[csf('production_date')]));
					$production_date=change_date_format($row[csf('production_date')],'','',1);

					$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
					$cm_cost=($row[csf('available_min')]*$cost_per_minute)/$exchange_rate;

                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="110"><p><? echo change_date_format($row[csf('production_date')]) ?></p></td>
                        <td width="100" align="center"><? echo $buyer_array[$row[csf('buyer_name')]]; ?></td>
                        <td width="180"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td align="right" width="80"><? echo number_format($row[csf('available_min')],2,".",""); ?></td>
                        <td align="right" width="80"><? echo $row[csf('produce_min')]; ?>&nbsp;</td>
                        <td align="right" width="60"  title="Produced Min/Used  Min*100"><? echo number_format(($row[csf('produce_min')]/$row[csf('available_min')])*100,2);//$job_effi_array[$row[csf('job_no')]]; ?>&nbsp;</td>
                        <td align="right" width="60"><? echo $cost_per_minute; ?>&nbsp;</td>
                        <td align="right" title="Used Min*CPM/Exchange Rate">
                            <?
                                echo number_format($cm_cost,4);
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_cm+=$cm_cost;
					$tot_used_min+=$row[csf('available_min')];
					$tot_produce_min+=$row[csf('produce_min')];
                }
                ?>
                   <tr bgcolor="#CCCCCC">
                    <th width="30">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="180">&nbsp;</th>
                    <th width="110">&nbsp;</th>
					<th width="80"><?  echo number_format($tot_used_min,2); ?></th>
                    <th width="80"><?  echo number_format($tot_produce_min,2); ?></th>
                   
                   <th width="60"><?  echo number_format((($tot_produce_min/$tot_used_min)*100),2); ?></th>
                    <th width="60">&nbsp;</th>
                    <th align="right" style="color:#FFFFFF"> <?  echo number_format($total_cm,4); ?></th>
                </tr>



			</table>
        </div>
	</fieldset>
	<?
	exit();
}

?>