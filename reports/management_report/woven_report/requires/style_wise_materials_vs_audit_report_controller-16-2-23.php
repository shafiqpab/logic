<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

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
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

//if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$brand_arr=return_library_array( "select id,brand_name from lib_buyer_brand", "id", "brand_name"  );//*lib_buyer_brand*
$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
	


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $type;
	
	 
	  

	
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
			document.getElementById('txt_job_id').value=data[3];

			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
         <input type="hidden" id="selected_job">
         <input type="hidden" id="selected_year">
         <input type="hidden" id="selected_company">
          <input type="hidden" id="txt_job_id">
            <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
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
                        <th width="80" colspan="2">Shipment Date</th>
                        <th width="80"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                       
                     </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_mst", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_name,"load_drop_down( 'style_wise_materials_vs_audit_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                   
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+'<? echo $type?>', 'create_po_search_list_view', 'search_div', 'style_wise_materials_vs_audit_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
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
        load_drop_down( 'style_wise_materials_vs_audit_report_controller', <? echo $company_name;?>, 'load_drop_down_buyer', 'buyer_td' );
    </script>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
			
if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	 
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	$date_cond="";
	if(str_replace("'","",$data[2])!="" && str_replace("'","",$data[3])!="")
	{
	 	if($db_type==0)
		{
			$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
			$start_date=change_date_format(str_replace("'","",$data[2]),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$data[3]),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
			$start_date=change_date_format(str_replace("'","",$data[2]),"","",1);
			$end_date=change_date_format(str_replace("'","",$data[3]),"","",1);
		}
	   $date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}

	$typeId= $data[11];

	 
	 
	$internal_ref = str_replace("'","",$data[9]); $internal_ref_cond="";$file_no_cond="";
	$file_no = str_replace("'","",$data[10]);
	
	$order_cond=""; $job_cond=""; $style_cond="";
	//echo  $data[8];die;
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[7]'  "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[8]'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no ='".trim($file_no)."' ";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no like '%$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[7]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '%".trim($file_no)."%' ";
		//echo $internal_ref_cond.'FGG';
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no like '$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[7]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping like '".trim($internal_ref)."%' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '".trim($file_no)."%' ";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no like '%$data[4]'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[7]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[8]'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '%".trim($file_no)."' ";
	}
	 

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
		
			if($db_type==0)
			{
				$sql="select a.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id as po_id, b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst f  where a.job_no=b.job_no_mst and a.job_no=f.job_no and b.job_no_mst=f.job_no and c.job_no_mst=f.job_no and c.job_no_mst=a.job_no and c.po_break_down_id=b.id  and a.status_active=1 and b.status_active=1  $company $buyer $job_cond $order_cond $style_cond $date_cond $shiping_status $file_no_cond $internal_ref_cond  $budget_version_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  group by a.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no DESC";
			}
			if($db_type==2)
			{
				$sql="select a.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.id as po_id,b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst f where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and c.po_break_down_id=b.id  and a.job_no=f.job_no and b.job_no_mst=f.job_no and c.job_no_mst=f.job_no  and a.status_active=1 and b.status_active=1  $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond $budget_version_cond group by a.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no DESC";
			}
		 
	
	//echo $sql;
	 
	$result=sql_select($sql);
	foreach ($result as $row)
	{
		
		$style_po_arr[$row[csf("job_no")]]["job_id"]= $row[csf("id")];
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
		$style_po_arr[$row[csf("job_no")]]["shipment_date"]=$row[csf("shipment_date")];// $row[csf("ex_factory_date")];
		$style_po_arr[$row[csf("job_no")]]["grouping"].= $row[csf("grouping")].',';
		$style_po_arr[$row[csf("job_no")]]["date_diff"]+= $row[csf("date_diff")];
		$style_po_arr[$row[csf("job_no")]]["file_no"].= $row[csf("file_no")].',';
				
	}
	//print_r($style_po_arr);
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
			<th width="70"><? echo "Shipment Date";?></th>
			<th width="70">Ref no</th>
			<th width="70">File no</th>
			<th>Lead time</th>
		</thead>
	</table>
	<div style="width:1180px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" id="list_view">
		<?

		$k=1;
		foreach($style_po_arr as $job_no=>$row)
		{
			if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		//	$exchange_rate=$exchange_rate_arr[$job_no];
			if($typeId==1)
			{
				$style_data=$row[("job_prefix")].'_'.$row[("year")].'_'.$row[('company_name')].'_'.$row[("job_id")];
			}
			else
			{
				$style_data=$row[("style_ref_no")].'_'.$row[("year")].'_'.$row[('company_name')].'_'.$row[("job_id")];
			}
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

	if($budget_version==1) $budget_version_cond="and f.entry_from=111"; else $budget_version_cond="and f.entry_from=158";

	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	
	$sql_data=sql_select("select b.job_no,b.exchange_rate from wo_po_details_master a,wo_pre_cost_mst b,wo_pre_cost_mst f where  a.job_no=b.job_no and a.job_no=f.job_no and a.company_name=$company_id and a.job_no_prefix_num=$job_no $year_cond $budget_version_cond");
	

	 $exchange_rate= $sql_data[0][csf('exchange_rate')];
	 echo $exchange_rate;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo str_replace("'","",$cbo_costcontrol_source);die;

		$company_name=str_replace("'","",$cbo_company_name);
		$buyer_id=str_replace("'","",$cbo_buyer_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$txt_style=str_replace("'","",$txt_style_ref);
		$txt_job_id=str_replace("'","",$txt_job_id);
		if($job_no!="") $job_cond="and a.job_no_prefix_num in($job_no)";else $job_cond="";
		if($txt_job_id!="") $job_idcond="and a.id in($txt_job_id)";else $job_idcond="";
		if($txt_style!="") $style_cond="and a.style_ref_no in('$txt_style')";else $style_cond="";
		if($buyer_id!=0) $buyers_cond="and a.buyer_name in('$buyer_id')";else $buyers_cond="";
		
		if($company_name==0){ echo "Select Company"; die; }
		//if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		
		 
		  $sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,a.brand_id, a.team_leader, a.dealing_marchant, b.id, b.po_number, b.grouping, b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.item_number_id, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty, c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_pre_cost_mst d where a.id=b.job_id and  a.id=c.job_id and b.id=c.po_break_down_id  and  a.id=d.job_id and  b.job_id=d.job_id and  c.job_id=d.job_id  and a.company_name='$company_name'   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond $job_idcond $style_cond $buyers_cond $year_cond order by  b.id";
		$result=sql_select($sql);
		 
		//echo $sql;
		 $ref_no="";
		$jobNumber=""; $po_ids=""; $buyerName=""; $styleRefno=""; $uom=""; $totalSetQnty=""; $currencyId=""; $quotationId=""; $shipingStatus=""; 
		$poNumberArr=array(); $poIdArr=array(); $poQtyArr=array(); $poPcutQtyArr=array(); $poValueArr=array(); $ShipDateArr=array(); $gmtsItemArr=array();
	$shipArry=array(0,1,2);
	//$shiping_status=1;$shiping_status_id="";
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			 
			$JobIdArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			 
			//echo $shiping_status.'D';
			 $job_wiseArr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
			 $job_wiseArr[$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
			 $job_wiseArr[$row[csf('job_no')]]['ratio']=$row[csf('ratio')];
			$order_uom=$row[csf('order_uom')];
			$ratio=$row[csf('ratio')];
			 
			 $job_wiseArr[$row[csf('job_no')]]['ratio']=$row[csf('ratio')];
			 $job_wiseArr[$row[csf('job_no')]]['style']=$row[csf('style_ref_no')];
			 $job_wiseArr[$row[csf('job_no')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
			 $job_wiseArr[$row[csf('job_no')]]['order_quantity']+=$row[csf('order_quantity')];
			 $job_wiseArr[$row[csf('job_no')]]['order_total']+=$row[csf('order_total')];
			 $job_wiseArr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
			 $job_wiseArr[$row[csf('job_no')]]['brand']=$row[csf('brand_id')];
			 $job_wiseArr[$row[csf('job_no')]]['poQty']+=$row[csf("order_quantity")];//order_job_qnty
			 $job_wiseArr[$row[csf('job_no')]]['smv']=$row[csf("set_smv")];
			 $job_wiseArr[$row[csf('job_no')]]['brand']=$brand_arr[$row[csf("brand_id")]];
			// $job_wiseArr[$job_key]['brand'];
		}
		
		
	
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		 
	    //$quotationId=1;
		$po_ids=rtrim($po_ids,',');
		$all_jobId=implode(",",$JobIdArr);
		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$all_jobId) !=''){
			$condition->jobid_in("$all_jobId");
		}
	
		$condition->init();
		//$costPerArr=$condition->getCostingPerArr();
		//$costPerQty=$costPerArr[$jobNumber];
		/*if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}*/
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
	
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
		//trim_amountSourcing_arr
		$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
		$trim_amount_sourceing_arr=$trim->getAmountArray_precostdtlsidSourcing();
		$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
		$emblishment_qtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment_amountArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		$wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		//	print_r($wash_qtyArr);
		$wash_amountArr=$wash->getAmountArray_by_jobAndEmblishmentid();
			
		//$trim_amountSourcing_arr=$trim->getAmountArray_precostdtlsidSourcing();
		//print_r($fabric_qty_arr);
	    // Yarn ============================
	
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			//$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			//$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]=$row_f[csf('item_name')];
		}
		$job_id_cond_in=where_con_using_array($JobIdArr,0,'b.job_id');
		$job_id_cond_in3=where_con_using_array($JobIdArr,0,'c.job_id');
		$job_id_cond_in2=where_con_using_array($JobIdArr,0,'job_id');
		
		  
                    
					
		       $wvn_booking_partial="select  c.id as booking_id,c.booking_no,c.supplier_id,c.pay_mode,a.fin_fab_qnty,a.grey_fab_qnty,a.rate,a.amount,b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id,  b.fabric_description as fab_desc,b.uom,b.avg_finish_cons,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,b.id as  pre_dtls_id from wo_booking_mst c,wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls  b where b.id=a.pre_cost_fabric_cost_dtls_id and c.booking_no=a.booking_no and a.job_no=b.job_no  and a.booking_type=1 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $job_id_cond_in order by b.id";
		  $wvn_booking_partial_result=sql_select($wvn_booking_partial);
		   foreach($wvn_booking_partial_result as $row)
			{
				$fab_desc=$row[csf('fab_desc')];
				$job_no=$row[csf('job_no')];
				$supplier_id=$row[csf('supplier_id')];
				$pay_modeId=$row[csf('pay_mode')];
				if($pay_modeId==3 || $pay_modeId==5)
				{
					//supplier_arr
					$booking_supplier=$company_arr[$supplier_id];
				}
				else
				{
					$booking_supplier=$supplier_arr[$supplier_id];
				}
				$style_desc_arr[$job_no][$fab_desc]['fin_fab_qnty']+=$row[csf('grey_fab_qnty')];
				$style_desc_arr[$job_no][$fab_desc]['booking_amount']+=$row[csf('grey_fab_qnty')]*$row[csf('rate')];
				$style_desc_arr[$job_no][$fab_desc]['fabric_booking_no']=$row[csf('booking_no')];
				$style_desc_arr[$job_no][$fab_desc]['pre_dtls_id'].=$row[csf('pre_dtls_id')].',';
				//$style_desc_arr[$job_no][$fab_desc]['booking_amount']+=$row[csf('amount')];
				$style_desc_arr[$job_no][$fab_desc]['booking_supplier'].=$booking_supplier.',';
				
				$booking_NoArr[$row[csf('booking_no')]]=$row[csf('job_no')];
				$booking_idArr[$row[csf('booking_id')]]=$row[csf('booking_id')];
				
			}
			//print_r($style_desc_arr);
			
			$booking_cond_in=where_con_using_array($booking_idArr,0,'b.work_order_id');
			//for test
				  $sql_pi_chk = "SELECT b.id as dtls_id,a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_no,b.color_id,b.work_order_id,b.body_part_id,b.cutable_width,b.fab_weight as gsm_weight,b.fab_weight_type as gsm_weight_type,b.width_dia_type as width_dia_type,b.dia_width as dia_width,b.fabric_construction as construction,b.fabric_composition as composition,b.uom,b.determination_id,(b.quantity) as qty,b.rate,b.amount,b.item_group,b.item_description from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.pi_basis_id=1  and b.item_category_id in(2,3) and b.status_active=1 and b.is_deleted=0 $booking_cond_in ";//wo_id_arr
				$Pi_result = sql_select($sql_pi_chk);
				$fabricWorkId=array();
				foreach ($Pi_result as $row)
				 {
					$job_no=$booking_NoArr[$row[csf('work_order_no')]];
					$cat_id=$row[csf('cat_id')];
					if($cat_id==3)
					{
						$all_data=$row[csf('work_order_id')].'_'.$row[csf('color_id')].'_'.$row[csf('construction')].'_'.$row[csf('composition')].'_'.$row[csf('gsm_weight')].'_'.$row[csf('gsm_weight_type')].'_'.$row[csf('width_dia_type')].'_'.$row[csf('dia_width')].'_'.$row[csf('cutable_width')].'_'.$row[csf('determination_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('uom')];
					}
					else
					{
						$all_data=$row[csf('work_order_id')].'_'.$row[csf('color_id')].'_'.$row[csf('construction')].'_'.$row[csf('composition')].'_'.$row[csf('dia_width')].'_'.$row[csf('determination_id')].'_'.$row[csf('uom')];
					}
					$piQtyArr[$job_no][$all_data]['qty']+=$row[csf('qty')];
					$piQtyArr[$job_no][$all_data]['amt']+=$row[csf('amount')];
					$piQtyArr[$job_no][$all_data]['dtls_id'].=$row[csf('dtls_id')].',';
					$piQtyArr[$job_no][$all_data]['pi_mst_id'].=$row[csf('pi_mst_id')].',';
					
					if($row[csf('work_order_id')]){$fabricWorkId[$row[csf('work_order_id')]]=$row[csf('work_order_id')];}
					 
				}
				//print_r($piQtyArr);
				/*$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.supplier_id,rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') AS dtls_id , 1 as type
					from wo_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 and c.transaction_type=1 $buyer_id_cond $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.supplier_id
					";*/
					//$sql ="select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.supplier_id, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id, sum(b.fin_fab_qnty) as wo_qnty, 1 as type,a.is_approved, max(b.fabric_color_id) as fabric_color_id, max(b.construction) as construction, max(b.copmposition) as copmposition, max(b.gsm_weight) as gsm_weight, max(b.dia_width) as dia_width, max(b.uom) as uom from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and a.item_category=2 and a.supplier_id like '64' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in(26165,8624,8779) and a.pay_mode=2 group by a.id, a.booking_type, a.is_short,a.is_approved, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.supplier_id";
					$fabricWorkId_cond=where_con_using_array($fabricWorkId,0,'a.id');
					// $job_id_cond_in3
				 	$wpr_data_array=sql_select("SELECT a.id,a.item_category,b.job_no,c.fabric_description,a.id, a.booking_no, b.fabric_color_id, c.construction as construction, c.composition as copmposition, b.gsm_weight as weight, c.gsm_weight_type as fab_weight_type, c.width_dia_type as dia_or_width, b.dia_width as width, b.item_size as cutable_width, sum(b.grey_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty*b.rate) as amount, c.lib_yarn_count_deter_id as deter_id, c.body_part_id, c.uom, 0 as dtls_id
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.job_no=b.job_no $fabricWorkId_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
					group by a.id,a.item_category, a.booking_no, b.fabric_color_id, c.fabric_description,b.job_no,c.construction, c.composition, b.gsm_weight, c.gsm_weight_type, c.width_dia_type, b.dia_width, b.item_size, c.lib_yarn_count_deter_id, c.body_part_id, c.uom
					order by a.booking_no, c.body_part_id");
					 
	
					$finish_req_qnty_arr=$finish_req_qnty_arr2=$finish_req_amount_arr=$finish_req_amount_arr2=array();			 
					foreach($wpr_data_array as $row)
					{
						if($row[csf('item_category')]==3)
						{
							$alls_data=$row[csf('id')].'_'.$row[csf('fabric_color_id')].'_'.$row[csf('construction')].'_'.$row[csf('copmposition')].'_'.$row[csf('weight')].'_'.$row[csf('fab_weight_type')].'_'.$row[csf('dia_or_width')].'_'.$row[csf('width')].'_'.$row[csf('cutable_width')].'_'.$row[csf('deter_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('uom')];
						}
						else
						{
							$alls_data=$row[csf('id')].'_'.$row[csf('fabric_color_id')].'_'.$row[csf('construction')].'_'.$row[csf('copmposition')].'_'.$row[csf('width')].'_'.$row[csf('deter_id')].'_'.$row[csf('uom')];	
						}
						$finish_req_qnty_arr[$alls_data]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
						$finish_req_qnty_arr2[$row[csf('job_no')]][$alls_data]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
						$finish_req_amount_arr[$alls_data]["finish_reqt_amount"]+=$row[csf('amount')];
						$finish_req_amount_arr2[$row[csf('job_no')]][$alls_data]["finish_reqt_amount"]+=$row[csf('amount')];
					}
	
					$grp_data_array=sql_select("SELECT a.id,a.item_category,b.job_no,c.fabric_description,a.id, a.booking_no, b.fabric_color_id, c.construction as construction, c.composition as copmposition, b.gsm_weight as weight, c.gsm_weight_type as fab_weight_type, c.width_dia_type as dia_or_width, b.dia_width as width, b.item_size as cutable_width, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.fin_fab_qnty*b.rate) as amount, c.lib_yarn_count_deter_id as deter_id, c.body_part_id, c.uom, 0 as dtls_id
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.job_no=b.job_no $job_id_cond_in3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
					group by a.id,a.item_category, a.booking_no, b.fabric_color_id, c.fabric_description,b.job_no,c.construction, c.composition, b.gsm_weight, c.gsm_weight_type, c.width_dia_type, b.dia_width, b.item_size, c.lib_yarn_count_deter_id, c.body_part_id, c.uom
					order by a.booking_no, c.body_part_id");
					foreach($grp_data_array as $row)
					{
						$fab_desc=$row[csf('fabric_description')];
						$job_no=$row[csf('job_no')];
						$booking_no=$row[csf('booking_no')];
						$cat_id=$row[csf('item_category')];
						if($cat_id==3)
						{
							$alls_data=$row[csf('id')].'_'.$row[csf('fabric_color_id')].'_'.$row[csf('construction')].'_'.$row[csf('copmposition')].'_'.$row[csf('weight')].'_'.$row[csf('fab_weight_type')].'_'.$row[csf('dia_or_width')].'_'.$row[csf('width')].'_'.$row[csf('cutable_width')].'_'.$row[csf('deter_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('uom')];
						}
						else
						{
							$alls_data=$row[csf('id')].'_'.$row[csf('fabric_color_id')].'_'.$row[csf('construction')].'_'.$row[csf('copmposition')].'_'.$row[csf('width')].'_'.$row[csf('deter_id')].'_'.$row[csf('uom')];	
						}
						//echo $piQtyArr[$job_no][$alls_data]['dtls_id'].',';
						$pi_mst_id=rtrim($piQtyArr[$job_no][$alls_data]['pi_mst_id'],',');
						if($pi_mst_id){$pi_style_desc_arr[$job_no][$fab_desc]['pi_mst_id'].=$pi_mst_id.',';}
						
						$requiredQnty_pi=$finish_req_qnty_arr[$alls_data]["finish_reqt_qnty"];
						$job_req=$finish_req_qnty_arr2[$job_no][$alls_data]["finish_reqt_qnty"];
						$requiredAmnt_pi=$finish_req_amount_arr[$alls_data]["finish_reqt_amount"];
						$job_amnt=$finish_req_amount_arr2[$job_no][$alls_data]["finish_reqt_amount"];
						$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_qty']+=($job_req*1/$requiredQnty_pi*1)*$piQtyArr[$job_no][$alls_data]['qty'];
						$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_amt']+=($job_amnt*1/$requiredAmnt_pi*1)*$piQtyArr[$job_no][$alls_data]['amt'];
						// $pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_qty']+=$piQtyArr[$job_no][$alls_data]['qty'];
						// $pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_amt']+=$piQtyArr[$job_no][$alls_data]['amt'];
						//$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_data']=$alls_data;
						//echo $booking_no.'='.$piQtyArr[$job_no][$alls_data]['qty'].'<br>';
						$piDtlsId=rtrim($piQtyArr[$job_no][$alls_data]['dtls_id'],',');
						if($piDtlsId)
						{
						$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_data'].=$piDtlsId.',';
						}
						
						//$piQtyArr[$job_no][$all_data]+=$row[csf('qty')];
					}
				// print_r($pi_style_desc_arr);
				
				//print_r($piQtyArr);
				//cutable_width,fab_weight_type,width_dia_type,dia_width,fabric_construction,fabric_composition,uom
	 $sql_pi = "select a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_no,b.color_id,b.work_order_id,b.determination_id,(b.quantity) as qty,b.rate,b.amount,b.item_group,b.item_description from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.pi_basis_id=1  and b.item_category_id in(2,3) and b.status_active=1 and b.is_deleted=0 $booking_cond_in ";//wo_id_arr
		
		$PiSQLresult = sql_select($sql_pi);
		 
		foreach ($PiSQLresult as $row) {
			$job_no=$booking_NoArr[$row[csf('work_order_no')]];
			//if($row[csf('cat_id')]==3) //Woven Fabric
			//{
			
			$PiArr[$job_no][$row[csf('determination_id')]]['pi_number'] .= $row[csf('pi_number')].',';
			//$PiArr[$job_no][$row[csf('determination_id')]]['pi_mst_id'] .= $row[csf('pi_mst_id')].',';
			$PiArr[$job_no][$row[csf('determination_id')]]['pi_qty'] += $row[csf('qty')];
			$PiArr[$job_no][$row[csf('determination_id')]]['pi_amt'] += $row[csf('amount')];
			//}
			
			
			
		}
		//print_r($PiArr2);
		unset($PiSQLresult);

		   
		     $pre_fab_arr="select  b.costing_per,b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls  b where  b.status_active=1 and b.is_deleted=0 $job_id_cond_in order by b.id";
			$pre_fab_result=sql_select($pre_fab_arr);
			
			//$summ_fob_pcs=0;$summ_fob_gross_value_amt=$summ_sourcing_tot_budget_dzn_val=0;
			foreach($pre_fab_result as $row)
			{
				
				$fab_desc=$row[csf('fab_desc')];
				$costing_perArr[$row[csf('job_no')]]=$row[csf('costing_per')];
				
				$job_no=$row[csf('job_no')];
				//echo $determin_type.'d';
				$tot_amt=$row[csf('avg_cons')]*$row[csf('rate')];
				$fab_req_qty=$fabric_qty_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_qty_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				$fab_req_amount=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				$fab_req_sourcing_amount=$fab_req_qty*$row[csf('sourcing_rate')];

				$fab_cost_dtls_id=$row[csf('id')];
				
				$style_desc_arr[$job_no][$fab_desc]['req_qty']+=$fab_req_qty;
				$style_desc_arr[$job_no][$fab_desc]['req_amount']+=$fab_req_amount;
				$style_desc_arr[$job_no][$fab_desc]['cons']+=$row[csf('avg_cons')];
				//$style_desc_arr[$job_no][$fab_desc]['cons']+=$row[csf('avg_cons')];
				$style_desc_arr[$job_no][$fab_desc]['amount']+=$row[csf('amount')];
				//$style_desc_arr[$job_no][$fab_desc]['req_qty']=$fab_req_qty;
				//$style_desc_arr[$job_no][$fab_desc]['req_amount']=$fab_req_amount;
				$style_desc_arr2[$job_no][$fab_desc]['req_amount']+=$fab_req_amount;
				$style_desc_arr[$job_no][$fab_desc]['sourcing_req_amount']+=$fab_req_sourcing_amount;
				$style_desc_arr[$job_no][$fab_desc]['sourcing_amount']+=$row[csf('sourcing_amount')];
				$style_desc_arr[$job_no][$fab_desc]['type']='fabric';
				$style_desc_arr[$job_no][$fab_desc]['sourcing_rate']=$row[csf('sourcing_rate')];
				//$style_desc_arr[$job_no][$fab_desc]['fab_desc']=$row[csf('construction')].','.$row[csf('composition')];
				$style_desc_arr[$job_no][$fab_desc]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$style_desc_arr[$job_no][$fab_desc]['fab_deter_min_id'].=$row[csf('deter_min_id')].',';
				$style_desc_arr[$job_no][$fab_desc]['pre_dtls_id'].=$row[csf('id')].',';
				$fab_pi_data=rtrim($pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_data'].',');
				//echo $pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_qty'].'='.$fab_pi_data.'<br>';
				$style_desc_arr[$job_no][$fab_desc]['pi_qty']=$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_qty'];//$PiArr[$job_no][$row[csf('deter_min_id')]]['pi_qty'];
				$style_desc_arr[$job_no][$fab_desc]['pi_amt']=$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_amt'];//$PiArr[$job_no][$row[csf('deter_min_id')]]['pi_amt'];
				$style_desc_arr[$job_no][$fab_desc]['pi_no'].=$PiArr[$job_no][$row[csf('deter_min_id')]]['pi_number'].',';
				$style_desc_arr[$job_no][$fab_desc]['pi_mst_id']=$pi_style_desc_arr[$job_no][$fab_desc]['pi_mst_id'];
				
				$style_desc_arr[$job_no][$fab_desc]['fab_pi_data'].=$fab_pi_data.',';
			//	$pi_style_desc_arr[$job_no][$fab_desc]['fab_pi_data'];
				$p_fab_precost_tot_row+=1;	
			
				//Summary
				//$summ_fob_pcs+=$row[csf('amount')];
			//	$summ_sourcing_tot_budget_dzn_val+=$fab_req_qty*$row[csf('sourcing_rate')];
				
				//$summ_fob_gross_value_amt+=$fab_req_amount;
			}
			//print_r($style_desc_arr2);
			   $booking_trim_sql="select d.id as booking_id,a.id as booking_dtls_id,d.booking_no,d.supplier_id,d.pay_mode,b.seq,b.id,c.trim_type,c.item_name,b.description,b.job_no,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_mst d,wo_booking_dtls a,wo_pre_cost_trim_cost_dtls b,lib_item_group c where  b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and c.id=b.trim_group  and b.job_no=a.job_no  and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.trim_type in(1,2) $job_id_cond_in order by b.seq";
			 //and c.trim_type in(1,2)
			$booking_trim_result=sql_select($booking_trim_sql);
			foreach($booking_trim_result as $row)
			{
				$supplier_id=$row[csf('supplier_id')];
				$pay_modeId=$row[csf('pay_mode')];
				if($pay_modeId==3 || $pay_modeId==5)
				{
					//supplier_arr
					$booking_supplier=$company_arr[$supplier_id];
				}
				else
				{
					$booking_supplier=$supplier_arr[$supplier_id];
				}
				
				$job_no=$row[csf('job_no')];
				$description=$row[csf('description')];
				if($description=='0' || $description=='') 
				{ 
					$description='';
				}
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				
				$style_desc_arr[$job_no][$item_id]['fin_fab_qnty']+=$row[csf('wo_qnty')];
				$style_desc_arr[$job_no][$item_id]['booking_amount']+=$row[csf('amount')];
				$style_desc_arr[$job_no][$item_id]['booking_supplier'].=$booking_supplier.',';
				$style_desc_arr[$job_no][$item_id]['trim_booking_no']=$row[csf('booking_no')];
				$style_desc_arr[$job_no][$item_id]['trim_booking_id']=$row[csf('booking_id')];
				$style_desc_arr[$job_no][$item_id]['pre_dtls_id'].=$row[csf('id')].',';
				$style_desc_arr[$job_no][$item_id]['booking_dtls_id'].=$row[csf('booking_dtls_id')].',';
				
				$trim_desc_booking_idArr[$job_no][$row[csf('booking_dtls_id')]]=$row[csf('description')];
				$trim_booking_idArr[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
				$trim_booking_NoArr[$row[csf('booking_no')]]=$row[csf('job_no')];
			}
			//print_r($trim_booking_NoArr);
			$trim_booking_cond_in=where_con_using_array($trim_booking_idArr,0,'b.work_order_dtls_id');
		   $trim_sql_pi = "select a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_no,b.determination_id,(b.quantity) as qty,b.rate,b.amount,b.item_group,b.item_description from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.pi_basis_id=1  and b.item_category_id in(4) and b.status_active=1 and b.is_deleted=0  $trim_booking_cond_in ";//wo_id_arr
		
		$TrimiSQLresult = sql_select($trim_sql_pi);
		 
		foreach ($TrimiSQLresult as $row) {
			$job_no=$trim_booking_NoArr[$row[csf('work_order_no')]];
			$trim_name=$trim_groupArr[$row[csf('item_group')]];
			//echo $trim_group.'DD';
			$pi_description=$trim_desc_booking_idArr[$job_no][$row[csf('work_order_dtls_id')]];//$row[csf('item_description')];
			if($pi_description=='0' || $pi_description=='') 
			{ 
				$pi_description='';
			}
			//if($pi_description==0) $pi_description='';
			if($pi_description!="") $descriptionCond=", ".$pi_description; else $descriptionCond="";
			//echo $pi_description.'=<br>';
			$item_id=$trim_name.$descriptionCond;
			$PiArr2[$job_no][$item_id]['pi_number'] .= $row[csf('pi_number')].',';
			$PiArr2[$job_no][$item_id]['pi_mst_id'] .= $row[csf('pi_mst_id')].',';
			$PiArr2[$job_no][$item_id]['pi_qty'] += $row[csf('qty')];
			$PiArr2[$job_no][$item_id]['pi_amt'] += $row[csf('amount')];
			
		}
		unset($TrimiSQLresult);
			  $pre_trim_sql="select b.seq,b.id,c.trim_type,c.item_name,b.description,b.job_no,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.trim_type in(1,2) $job_id_cond_in order by b.seq";
			//and c.trim_type in(1,2)
			$pre_trim_result=sql_select($pre_trim_sql);
			
			$p_sew_trim_precost_arr=$p_fin_trim_precost_arr=array();
			foreach($pre_trim_result as $row)
			{
				$job_no=$row[csf('job_no')];
				$description=$row[csf('description')];
				if($description=='0' || $description=='') 
				{ 
					$description='';
				}
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				//$item_name_arr[$item_id]=$row[csf('item_name')].$descriptionCond;
				$req_amt=$row[csf('cons_dzn_gmts')]*$row[csf('rate')];
				$p_sew_loss=$row[csf('ex_per')];
				$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_sew_loss)/100);
				$trim_req_qty=$trim_qty_arr[$row[csf('id')]];
				$trim_req_sourcing_amount=$trim_req_qty*$row[csf('sourcing_rate')];
					//$trim_amountSourcing_arr
					//echo $item_id.'='.$PiArr2[$job_no][$item_id]['pi_qty'].'<br>';
				$trim_req_amount=$trim_amount_arr[$row[csf('id')]];
				 $trim_req_amountSourcing2=$trim_amount_sourceing_arr[$row[csf('id')]];
				//  echo $trim_req_amountSourcing2.'='.$row[csf('sourcing_rate')].'<br>';
				$style_desc_arr[$job_no][$item_id]['req_qty']+=$trim_req_qty;
				$style_desc_arr[$job_no][$item_id]['req_amount']+=$trim_req_amount;
				$style_desc_arr[$job_no][$item_id]['sourcing_req_amount']+=$trim_req_sourcing_amount;
				$style_desc_arr[$job_no][$item_id]['cons']+=$row[csf('cons_dzn_gmts')];
				$style_desc_arr[$job_no][$item_id]['trims_tot_cons']+=$row[csf('cons_dzn_gmts')];
				$style_desc_arr[$job_no][$item_id]['amount']+=$row[csf('amount')];
				$style_desc_arr[$job_no][$item_id]['sourcing_amount']+=$row[csf('sourcing_amount')];
				$style_desc_arr[$job_no][$item_id]['type']='trim';
				$style_desc_arr[$job_no][$item_id]['pre_dtls_id'].=$row[csf('id')].',';
				$style_desc_arr[$job_no][$item_id]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$style_desc_arr[$job_no][$item_id]['trim_group']=$row[csf('trim_group')];
				$style_desc_arr[$job_no][$item_id]['pi_qty']=$PiArr2[$job_no][$item_id]['pi_qty'];
				$style_desc_arr[$job_no][$item_id]['pi_amt']=$PiArr2[$job_no][$item_id]['pi_amt'];
				$style_desc_arr[$job_no][$item_id]['pi_no'].=$PiArr2[$job_no][$item_id]['pi_number'].',';
				$style_desc_arr[$job_no][$item_id]['pi_mst_id'].=$PiArr2[$job_no][$item_id]['pi_mst_id'].',';
				
				//$p_sew_trim_precost_arr[$item_id]['tot_row']+=1;
				$p_sew_trim_tot_row+=1;
				
				$summ_fob_pcs+=$row[csf('amount')];	
				$summ_sourcing_tot_budget_dzn_val+=$trim_req_amountSourcing;
				$summ_fob_gross_value_amt+=$trim_req_amount;
				//$summ_fob_value_pcs+=$row[csf('amount')]*$order_price_per_dzn;
			}
			//////////=========Emblishment====//////////////
			  $booking_emblish_sql="select a.id as booking_dtls_id, d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,b.id,b.job_no,b.emb_name,b.emb_type, b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_mst d,wo_booking_dtls a,wo_pre_cost_embe_cost_dtls b where  b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and  a.booking_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0  $job_id_cond_in order by d.id";
			 //and c.trim_type in(1,2)
			$booking_embl_result=sql_select($booking_emblish_sql);
			foreach($booking_embl_result as $row)
			{
				$supplier_id=$row[csf('supplier_id')];
				$pay_modeId=$row[csf('pay_mode')];
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
				$job_no=$row[csf('job_no')];
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==3) //Wash type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_wash_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				if($pay_modeId==3 || $pay_modeId==5)
				{
					//supplier_arr
					$booking_supplier=$company_arr[$supplier_id];
				}
				else
				{
					$booking_supplier=$supplier_arr[$supplier_id];
				}
				
				$style_desc_arr[$job_no][$emb_name]['fin_fab_qnty']+=$row[csf('wo_qnty')];
				$style_desc_arr[$job_no][$emb_name]['booking_amount']+=$row[csf('amount')];
				$style_desc_arr[$job_no][$emb_name]['booking_supplier'].=$booking_supplier.',';
				
				$style_desc_arr[$job_no][$emb_name]['pre_dtls_id'].=$row[csf('id')].',';
				$style_desc_arr[$job_no][$emb_name]['booking_dtls_id'].=$row[csf('booking_dtls_id')].',';
				//$style_desc_arr2[$job_no][$emb_name]['booking_dtls_id'].=$row[csf('booking_dtls_id')].',';
				
				
				
				$embl_booking_idArr[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
				$embl_trim_booking_NoArr[$row[csf('booking_no')]]=$row[csf('job_no')];
			}
			//print_r($style_desc_arr2);
			$embl_booking_cond_in=where_con_using_array($embl_booking_idArr,0,'b.work_order_dtls_id');
		  $embl_sql_pi = "select a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_no,b.work_order_dtls_id,b.determination_id,(b.quantity) as qty,b.rate,b.amount,b.embell_name,b.embell_type from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.pi_basis_id=1  and b.item_category_id in(25) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $embl_booking_cond_in ";//wo_id_arr
		
		$EmblSQLresult = sql_select($embl_sql_pi);
		 
		foreach ($EmblSQLresult as $row) {
			$job_no=$embl_trim_booking_NoArr[$row[csf('work_order_no')]];
			$emb_name_id=$row[csf('embell_name')];
			$emb_type=$row[csf('embell_type')];
			//$job_no=$row[csf('job_no')];
			
			 if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('embell_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('embell_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('embell_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('embell_name')]].$emb_typeCond;
				}
				else if($emb_name_id==3) //Wash type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_wash_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('embell_name')]].$emb_typeCond;
				}
				
				
			$PiArr2[$job_no][$emb_name]['pi_number'] .= $row[csf('pi_number')].',';
			$PiArr2[$job_no][$emb_name]['pi_mst_id'] .= $row[csf('pi_mst_id')].',';
			$PiArr2[$job_no][$emb_name]['pi_qty'] += $row[csf('qty')];
			$PiArr3[$job_no][$emb_name]['pi_qty'] += $row[csf('qty')];
			$PiArr2[$job_no][$emb_name]['pi_amt'] += $row[csf('amount')];
			
		}
	//print_r($PiArr3);
		unset($EmblSQLresult);
		
			
			 $pre_wash_arr="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp,b.sourcing_amount from wo_pre_cost_embe_cost_dtls  b where  b.status_active=1 and b.is_deleted=0  $job_id_cond_in  order by b.emb_name";//and b.fabric_source=2
			$pre_wash_result=sql_select($pre_wash_arr);
			foreach($pre_wash_result as $row)
			{
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
				$job_no=$row[csf('job_no')];
			
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				 
				$wash_req_amount=$emb_req_amount=0;
				
				if($row[csf('emb_name')]==3) //Wash
				{
						if($row[csf('emb_type')]>0) $wash_emb_typeCond=", ".$emblishment_wash_type[$row[csf('emb_type')]];else $wash_emb_typeCond="";
						$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$wash_emb_typeCond;
						$wash_req_qty=$wash_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
						$wash_req_amount=$wash_amountArr[$row[csf('job_no')]][$row[csf('id')]];
					//	$amt=sourcing_rate
					$wash_sourcing_amount=$wash_req_qty*$row[csf('sourcing_rate')];
				$style_desc_arr[$job_no][$emb_name]['type']='embl';
				$style_desc_arr[$job_no][$emb_name]['pre_dtls_id'].=$row[csf('id')].',';
				$style_desc_arr[$job_no][$emb_name]['req_qty']+=$wash_req_qty;
				$style_desc_arr[$job_no][$emb_name]['req_amount']+=$wash_req_amount;
				$style_desc_arr[$job_no][$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
				$style_desc_arr[$job_no][$emb_name]['sourcing_amount']+=$row[csf('sourcing_amount')];
				//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$style_desc_arr[$job_no][$emb_name]['amount']+=$row[csf('amount')];
				$style_desc_arr[$job_no][$emb_name]['p_loss']=$row[csf('p_loss')];
				$style_desc_arr[$job_no][$emb_name]['sourcing_req_amount']+=$wash_sourcing_amount;
				//$style_desc_arr[$job_no][$emb_name]['pre_rate']=$row[csf('rate')];
			//	$style_desc_arr[$job_no][$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$style_desc_arr[$job_no][$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$wash_req_qty*$row[csf('sourcing_rate')];
				 $style_desc_arr[$job_no][$emb_name]['pi_qty']+=$PiArr2[$job_no][$emb_name]['pi_qty'];
				$style_desc_arr[$job_no][$emb_name]['pi_amt']+=$PiArr2[$job_no][$emb_name]['pi_amt'];
				$style_desc_arr[$job_no][$emb_name]['pi_no'].=$PiArr2[$job_no][$emb_name]['pi_number'].',';
				$style_desc_arr[$job_no][$emb_name]['pi_mst_id'].=$PiArr2[$job_no][$emb_name]['pi_mst_id'].',';
				}
				else
				{
				
				$emb_req_qty=$emblishment_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
				$emb_req_amount=$emblishment_amountArr[$row[csf('job_no')]][$row[csf('id')]];
				$embl_sourcing_amount=$emb_req_qty*$row[csf('sourcing_rate')];
				$style_desc_arr[$job_no][$emb_name]['type']='embl';
				$style_desc_arr[$job_no][$emb_name]['req_qty']+=$emb_req_qty;
				$style_desc_arr[$job_no][$emb_name]['req_amount']+=$emb_req_amount;
				$style_desc_arr[$job_no][$emb_name]['sourcing_amount']+=$row[csf('sourcing_amount')];
				$style_desc_arr[$job_no][$emb_name]['sourcing_req_amount']+=$embl_sourcing_amount;
				$style_desc_arr[$job_no][$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
				//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$style_desc_arr[$job_no][$emb_name]['amount']+=$row[csf('amount')];
			//	$style_desc_arr[$job_no][$emb_name]['pre_rate']+=$row[csf('rate')];
				//$style_desc_arr[$job_no][$emb_name]['p_loss']=$row[csf('p_loss')];
				
				//$style_desc_arr[$job_no][$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$style_desc_arr[$job_no][$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$emb_req_qty*$row[csf('sourcing_rate')];
				 $style_desc_arr[$job_no][$emb_name]['pi_qty']=$PiArr2[$job_no][$emb_name]['pi_qty'];
				$style_desc_arr[$job_no][$emb_name]['pi_amt']=$PiArr2[$job_no][$emb_name]['pi_amt'];
				$style_desc_arr[$job_no][$emb_name]['pi_no'].=$PiArr2[$job_no][$emb_name]['pi_number'].',';
				$style_desc_arr[$job_no][$emb_name]['pi_mst_id'].=$PiArr2[$job_no][$emb_name]['pi_mst_id'].',';
				}
				//$summ_fob_value_pcs+=$row[csf('amount')]/$order_price_per_dzn;
				//$summ_fob_gross_value_amt+=$emb_req_amount+$wash_req_amount;
			}
			
			$sql_other = "select job_no,fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost from wo_pre_cost_dtls where   status_active=1 and  is_deleted=0 $job_id_cond_in2";
			$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;
			 foreach( $pre_other_result as $row )
			{
				$costing_per=$costing_perArr[$row[csf('job_no')]];
				 if($costing_per==1){$order_price_per_dzn=12;$costing_val=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_val=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
					
				$order_job_qnty=$job_wiseArr[$row[csf('job_no')]]['poQty'];
				
				$lab_test=($row[csf('lab_test')]/$order_price_per_dzn)*$order_job_qnty/$ratio; 
				$currier_pre_cost=($row[csf('currier_pre_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$inspection=($row[csf('inspection')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				//$comarcial=($row[csf('comm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$freight=($row[csf('freight')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$certificate_pre_cost=($row[csf('certificate_pre_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$design_pre_cost=($row[csf('design_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$studio_pre_cost=($row[csf('studio_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$common_oh=($row[csf('common_oh')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$depr_amor_pre_cost=($row[csf('depr_amor_pre_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$interest_pre_cost=($row[csf('interest_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$income_tax_pre_cost=($row[csf('incometax_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$commercial_cost=($row[csf('comm_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				
				$tot_other_for_fob_value=$lab_test+$currier_pre_cost+$inspection+$comarcial+$freight+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$common_oh+$interest_pre_cost+$income_tax_pre_cost+$depr_amor_pre_cost;
				//echo $tot_other_for_fob_value;
				$lab_test_dzn=$row[csf('lab_test')];
			//	$fob_pcs=$row[csf('price_with_commn_pcs')];
				$currier_pre_cost_dzn=$row[csf('currier_pre_cost')];
				$inspection_dzn=$row[csf('inspection')];
				$comarcial_dzn=$row[csf('comm_cost')];
				
				$common_oh_dzn=$row[csf('common_oh')];
				$studio_pre_cost_dzn=$row[csf('studio_cost')];
				$design_pre_cost_dzn=$row[csf('design_cost')];
				$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];
				$freight_dzn=$row[csf('freight')];
				//$comm_cost_dzn=$row[csf('comm_cost')];
				$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
				$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
				$interest_pre_cost_dzn=$row[csf('interest_cost')];
				
				$cm_cost_dzn=$row[csf('cm_cost')];
				$cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
				$cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$tot_cm_qty_dzn=$row[csf('cm_cost')]*$offer_qty_dzn;
				
				$tot_other_cost_dzn=$common_oh_dzn+$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;
				$tot_other_cost=($tot_other_cost_dzn/$order_price_per_dzn)*$order_job_qnty/$ratio;
				//$summ_fob_gross_value_amt+=$tot_other_for_fob_value+$tot_cm_qty_dzn ;
				//$summ_fob_pcs+=$tot_other_cost_dzn+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comarcial_dzn+$cm_cost_dzn;
				//$summ_sourcing_tot_budget_dzn_val+=$tot_other_for_fob_value;
				// =========Other Cost===================
				$lab_desc='Test Charge';
				$inspection_desc='Inspection Charge';
				$comm_cost_desc='Commercial Charge';
				$currier_desc='Currier Charge';
				
				$other_desc='Other Charge';
				$other_dzn=$tot_other_cost_dzn;
				
				$freight_desc='freight';
				$certificate_desc='certificate';
				$design_desc='design';
				$studio_desc='studio';
				$common_oh_desc='common_oh';
				$depr_amor_desc='depr_amor';
				$interest_desc='interest';
				$income_desc='incometax';
				
				$cm_text='CM Amount/Dzn(USD)';
				
				$style_desc_arr[$job_no][$lab_desc]['type']='Other';
				
				$style_desc_arr[$job_no][$lab_desc]['amount']+=$lab_test_dzn;
				$style_desc_arr[$job_no][$lab_desc]['req_amount']+=$lab_test;
				$style_desc_arr[$job_no][$lab_desc]['sourcing_amount']+=$lab_test_dzn;
				$style_desc_arr[$job_no][$lab_desc]['sourcing_req_amount']+=$lab_test;
				
				$style_desc_arr[$job_no][$inspection_desc]['amount']+=$inspection_dzn;
				$style_desc_arr[$job_no][$inspection_desc]['req_amount']+=$inspection;
				$style_desc_arr[$job_no][$inspection_desc]['sourcing_amount']+=$inspection_dzn;
				$style_desc_arr[$job_no][$inspection_desc]['sourcing_req_amount']+=$inspection;
				
				$style_desc_arr[$job_no][$currier_desc]['amount']+=$currier_pre_cost_dzn;
				$style_desc_arr[$job_no][$currier_desc]['req_amount']+=$currier_pre_cost;
				$style_desc_arr[$job_no][$currier_desc]['sourcing_amount']+=$currier_pre_cost_dzn;
				$style_desc_arr[$job_no][$currier_desc]['sourcing_req_amount']+=$currier_pre_cost;
				
				//$style_desc_arr[$job_no][$currier_desc]['amount']+=$currier_pre_cost_dzn;
				//$style_desc_arr[$job_no][$currier_desc]['req_amount']+=$currier_pre_cost;
				//$style_desc_arr[$job_no][$currier_desc]['sourcing_amount']+=$currier_pre_cost_dzn;
				//$style_desc_arr[$job_no][$currier_desc]['sourcing_req_amount']+=$currier_pre_cost;
				
				
				$style_desc_arr[$job_no][$comm_cost_desc]['amount']+=$comarcial_dzn;
				$style_desc_arr[$job_no][$comm_cost_desc]['req_amount']+=$commercial_cost;
				$style_desc_arr[$job_no][$comm_cost_desc]['sourcing_amount']+=$comarcial_dzn;
				$style_desc_arr[$job_no][$comm_cost_desc]['sourcing_req_amount']+=$commercial_cost;
			
				$style_desc_arr[$job_no][$other_desc]['amount']+=$tot_other_cost_dzn;
				$style_desc_arr[$job_no][$other_desc]['req_amount']+=$tot_other_cost;
				$style_desc_arr[$job_no][$other_desc]['sourcing_amount']+=$tot_other_cost_dzn;
				$style_desc_arr[$job_no][$other_desc]['sourcing_req_amount']+=$tot_other_cost;
				
				$style_cm_dzn_arr[$job_no]['amount']+=$cm_cost_dzn;
				//$style_desc_arr[$job_no][$cm_text]['req_amount']+=($cm_cost_dzn*$order_job_qnty)/$order_price_per_dzn;
				//$style_desc_arr[$job_no][$cm_text]['sourcing_amount']+=$cm_cost_dzn;
				//$style_desc_arr[$job_no][$cm_text]['sourcing_req_amount']+=($cm_cost_dzn*$order_job_qnty)/$order_price_per_dzn;
				 
			}
			//print_r($p_sew_trim_precost_arr2);
			$sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where   status_active=1  $job_id_cond_in2";
		$result_commi=sql_select($sql_commi);
		 foreach( $result_commi as $row )
			{
				$job_no=$row[csf('job_no')];
				$costing_per=$costing_perArr[$job_no];
				$commission_type_id=$row[csf('particulars_id')];
				 if($costing_per==1){$order_price_per_dzn=12;$costing_val=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_val=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
					
					if($commission_type_id==2) //Local
					{
					$commision_type="UK Office Commission";	
					}
					else
					{
					$commision_type="Buying Commission";	 
					}
				$order_job_qnty=$job_wiseArr[$job_no]['poQty'];
				$style_desc_arr[$job_no][$commision_type]['type']='commission';
				$style_desc_arr[$job_no][$commision_type]['amount']+=$row[csf('commission_amount')];
				$style_desc_arr[$job_no][$commision_type]['req_amount']+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
				$style_desc_arr[$job_no][$commision_type]['sourcing_amount']+=$row[csf('commission_amount')];
				$style_desc_arr[$job_no][$commision_type]['sourcing_req_amount']+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty/$ratio;
			} 
			unset($result_commi);
		
			 
		//print_r($style_desc_arr2);	
		
	  
		// Other Cost End ============================
		ob_start();
		$width_td=2140;
		?>
		<div style="width:1302px; margin:0 auto">
			<div style="width:1300px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			
			<table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
             <tr>
               	<th colspan="7"></th>
                <th colspan="2">As Per Costing (Merchant)</th>
                <th colspan="2">As Per Budget (Sourcing)</th>
                <th colspan="2">As Per Consu(CAD/Merchant)</th>
                <th colspan="3">As Per Booking</th>
                <th colspan="2">Balance/ Excess	</th>
                <th colspan="3">As Per PI</th>
                <th colspan="2">Balance/ Excess</th>
                <th colspan="2">&nbsp;</th>
                 
              </tr>
               <tr style="font-size:13px">
                       	<th width="20" >SL</th>
                       	<th width="110">Buyer<br>Brand Name</th>
                        <th width="100" >Job No</th>
                       	<th width="100">Style</th>
                       	<th width="100" >Style Order<br> Qty (<?=$unit_of_measurement[$order_uom];?>)</th>
                      	<th width="150" >Item Description</th>
                       	<th width="60" >Unit</th>
                        
                       	<th width="80"> Amnt/Dzn </th>
                        <th width="80"> Value (USD)</th>
                      	<th width="80"> Amnt/Dzn </th>
                        <th width="80"> Value (USD)</th>
                       	<th width="80">Cons.</th>
                       	<th width="80">Required Qty </th>
                        
                       <th width="80"> Booking Qty</th>
                       <th width="80">U.Price</th> 
                       <th width="80"> Booking Value </th>
                       
                       	<th width="80"> Qty. </th>
                       	<th width="80"> Value(USD)</th>
                        
                       <th width="80">PI QTY. </th>    
                       <th width="80"> U. Price </th>
                       <th width="80" title="">PI Value </th> 
                        
                       <th width="80">Qty.</th>  
                       <th width="80"> Value (USD) </th>
                       <th width="100"  title=""> PI NO </th>  
                       <th width="">Supplier Nmae</th> 
                       
                        
                    </tr>
            </thead>
        </table>
        <div style="width:<? echo $width_td+20;?>px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		$job_row_span_arr=array();
		foreach($style_desc_arr as $job_key=>$job_data)
		{
			 $job_row_span=0;
			 foreach($job_data as $desc_key=>$row)
			{
				$job_row_span++;
			}
			$job_row_span_arr[$job_key]=$job_row_span;
			
		}
		//print_r($job_row_span_arr);
		
		$i=1;$tot_pi_value=$tot_pi_qty=$tot_fab_value_balance=$tot_fab_qty_balance=$tot_booking_amount=$tot_booking_qty=$tot_budget_req_qty=$tot_budget_cons_dzn=$tot_sourcing_req_amount=$tot_sourcing_amount_dzn=$tot_budget_amount_dzn=$tot_budget_req_amount=$tot_pi_balance_value=$tot_pi_balance_qty=0;
        $tot_style_cm_dzn=0;$tot_po_qty=0;$tot_job_smv=0;
		foreach($style_desc_arr as $job_key=>$job_data)
		{
			$tot_style_cm_dzn+=$style_cm_dzn_arr[$job_key]['amount'];
			$tot_job_smv+=$job_wiseArr[$job_key]['smv'];
			$m=1; 
			 foreach($job_data as $desc_key=>$row)
			{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$typeId=$row[('type')];
					if($typeId=='fabric') $color_msg="#009933";
					else if($typeId=='trim') $color_msg="#FF9933";
					else if($typeId=='embl') $color_msg="#6699CC";
					else if($typeId=='Other') $color_msg="#FFFFFF";
					else if($typeId=='commission') $color_msg="#FFFF00";
				if($typeId=='fabric')
				{	
					$fabric_booking_no=$style_desc_arr[$job_key][$desc_key]['fabric_booking_no'];
					$pre_dtls_id=rtrim($style_desc_arr[$job_no][$desc_key]['pre_dtls_id'],',');
					//$fab_pi_dtlsid=rtrim($pi_style_desc_arr[$job_key][$desc_key]['fab_pi_data'],',');
					$fab_pi_dtlsid=rtrim($row[('fab_pi_data')],',');
					//echo $fab_pi_data.'d'; row[('fab_pi_data')]
					//$pre_dtls_id=explode(",",$pre_dtls_id);
					$all_pre_dtls_ids=implode(",",array_unique(explode(",",$pre_dtls_id)));
					$all_pi_dtls_ids=implode(",",array_unique(explode(",",$fab_pi_dtlsid)));
					$pi_mst_id=rtrim($row[('pi_mst_id')],',');
					$pi_mst_ids=implode(",",array_unique(explode(",",$pi_mst_id)));
					$fab_deter_min_idArr=rtrim($row[('fab_deter_min_id')],',');
					$fab_deter_min_id=implode(",",array_unique(explode(",",$fab_deter_min_idArr)));
					//$fab_deter_min_id=$row[('fab_deter_min_id')];
				}
				else if($typeId=='trim')
				{
					$trim_booking_no=$style_desc_arr[$job_key][$desc_key]['trim_booking_no'];
					$pre_dtls_id=rtrim($style_desc_arr[$job_no][$desc_key]['pre_dtls_id'],',');
					$booking_dtls_id=rtrim($style_desc_arr[$job_no][$desc_key]['booking_dtls_id'],',');
					//$style_desc_arr[$job_no][$item_id]['booking_dtls_id'].=$row[csf('booking_dtls_id')].',';
					//echo $booking_dtls_id.'<br>';
					$all_pre_dtls_ids=implode(",",array_unique(explode(",",$booking_dtls_id)));
					$pi_mst_id=rtrim($row[('pi_mst_id')],',');
					$pi_mst_ids=implode(",",array_unique(explode(",",$pi_mst_id)));
				}
				else if($typeId=='embl')
				{
					$trim_booking_no=$style_desc_arr[$job_key][$desc_key]['trim_booking_no'];
					$pre_dtls_id=rtrim($style_desc_arr[$job_no][$desc_key]['pre_dtls_id'],',');
					$booking_dtls_id=rtrim($style_desc_arr[$job_no][$desc_key]['booking_dtls_id'],',');
					//$style_desc_arr[$job_no][$item_id]['booking_dtls_id'].=$row[csf('booking_dtls_id')].',';
					//echo $booking_dtls_id.'<br>';
					$all_pre_dtls_ids=implode(",",array_unique(explode(",",$booking_dtls_id)));
					$pi_mst_id=rtrim($row[('pi_mst_id')],',');
					$pi_mst_ids=implode(",",array_unique(explode(",",$pi_mst_id)));
				}
			//	echo $pi_mst_ids.'d';
			//	$trim_booking_no=$style_desc_arr[$job_key][$desc_key]['trim_booking_no'];
				//$trim_booking_id=$style_desc_arr[$job_no][$desc_key]['trim__booking_id'];
				
					 
					$brandName=$job_wiseArr[$job_key]['brand'];
					$job_row_span=$job_row_span_arr[$job_key];
					
					$pi_no=rtrim($row[('pi_no')],',');
					$pre_dtls_id=rtrim($row[('pre_dtls_id')],',');
					$pre_dtls_ids=implode(",",array_unique(explode(",",$pre_dtls_id)));
					
					//$pi_mst_id=rtrim($row[('pi_mst_id')],',');
					
					
					$pi_number=$pi_no;
					$pi_numbers=implode(", ",array_unique(explode(",",$pi_number)));
					
					$booking_supplier=rtrim($row[('booking_supplier')],',');
					$booking_suppliers=implode(",",array_unique(explode(",",$booking_supplier)));
					
					if($typeId=='fabric')
					{
						$fab_deter_min_id=$fab_deter_min_id;
					}
					else if($typeId=='trim')
					{
						$fab_deter_min_id=$row[('trim_group')];
					}
					else if($typeId=='embl')
					{
						$fab_deter_min_id=$row[('trim_group')];
					}
				
		?> 
       			
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<? 
				if($m==1) 
				{
					$action="app_final_cost";
				$job_button="generate_worder_report_pre_cost('".$action."','".$job_key."',".str_replace("'","",$company_name).",".$job_wiseArr[$job_key]['buyer'].",'".$job_wiseArr[$job_key]['style']."','','','','1');"; 
				$style_action="mkt_source_cost";
				$style_button="generate_worder_report_pre_cost('".$style_action."','".$job_key."',".str_replace("'","",$company_name).",".$job_wiseArr[$job_key]['buyer'].",'".$job_wiseArr[$job_key]['style']."','','','','2');"; 
				 $job_wiseArr[$job_key]['ratio'];
				?>
            	<td  rowspan="<? echo $job_row_span;?>" width="20"><? echo $i; ?></td>
				<td width="110" rowspan="<? echo $job_row_span;?>"><p style="word-break:break-all"><? echo $buyer_library[$job_wiseArr[$job_key]['buyer']].'<br>'.$brandName; ?></p></td>
				<td width="100" rowspan="<? echo $job_row_span;?>"><p style="word-break:break-all"><a href='##'  onclick="<? echo $job_button; ?>"><? echo $job_key; ?></a></td>
				<td width="100" rowspan="<? echo $job_row_span;?>"><p style="word-break:break-all"><a href='##'  onclick="<? echo $style_button; ?>"><? echo $job_wiseArr[$job_key]['style']; ?></a></p></td>
                <td width="100" rowspan="<? echo $job_row_span;?>"><p><? echo  $job_wiseArr[$job_key]['order_quantity']/$job_wiseArr[$job_key]['ratio']; ?></p></td>
                <?
					$tot_po_qty+=$job_wiseArr[$job_key]['order_quantity']/$job_wiseArr[$job_key]['ratio'];
				}
				?>
				<td width="150" bgcolor="<? echo $color_msg;?>"><p style="word-break:break-all"><? echo $desc_key;?></p></td>
				
                <td width="60" align="center"><p><? echo $row[('uom')]; ?></p></td>
				
                <td width="80" align="right"><p><? echo number_format($row[('amount')],4);; ?></p></td>
				<td width="80" align="right"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
				<td width="80"  title="<? ?>" align="right"><p> <? echo  number_format($row[('sourcing_amount')],4);; ?> 	</p></td>
				
				<td width="80" align="right" title="Sourcing Amount"><p><? echo number_format($row[('sourcing_req_amount')],4); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
				
                <td width="80" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($row[('fin_fab_qnty')],4); ?></p></td>
				<td width="80" align="right" title="Booking amount/FinQty "><p><? if($row[('booking_amount')]) echo number_format($row[('booking_amount')]/$row[('fin_fab_qnty')],4);else echo ""; ?></p></td>
				 
                <td width="80" align="right"><p><a href="##" onClick="report_generate_popup('<? echo $company_name; ?>','<? echo $job_key; ?>','<? echo $typeId; ?>','<? echo $pre_dtls_ids; ?>','<? echo $fab_deter_min_id; ?>','<? echo $all_pre_dtls_ids; ?>','','report_generate_woven',1)"><? echo number_format($row[('booking_amount')],4); ?></a><? //echo number_format($row[('booking_amount')],4); ?></p></td>
				<td width="80" align="right" title="Fab Req-Booking Req Qty"><p><? $fab_bal_qty=$row[('req_qty')]-$row[('fin_fab_qnty')];echo number_format($fab_bal_qty,4); 
				 $fab_value_balance=$row[('sourcing_req_amount')]-$row[('booking_amount')];
				?></p></td>
				<td width="80" align="right" title="Sourcing Value-Booking Value"><p><? 
				echo number_format($fab_value_balance,4); ?></p></td>
				<td width="80" align="right" title=" Pi Qty"><p><a href="##" onClick="report_generate_popup('<? echo $company_name; ?>','<? echo $job_key; ?>','<? echo $typeId; ?>','<? echo $pi_mst_ids; ?>','<? echo $fab_deter_min_id; ?>','<? echo $all_pre_dtls_ids; ?>','<? echo $all_pi_dtls_ids; ?>','report_generate_woven_pi',2)"><? echo number_format($row[('pi_qty')],4); ?></a><? //$fabric_bal_after_cut_qty=$wv_recv_fin-$fabric_used_qty;
				//echo number_format($row[('pi_qty')],4); ?></p></td>
				<td width="80" align="right" title="PI Amount/PI Qty"><p><? if($row[('pi_amt')]) echo number_format($row[('pi_amt')]/$row[('pi_qty')],4); else echo "";?></p></td>
				<td width="80" align="right" title="PI Amount"><p><?  echo number_format($row[('pi_amt')],4);
				 ?></p></td>
				<td width="80" align="right" title="Booking Qty-PI Qty"><p>
                <?
				$pi_balance_qty=$row[('fin_fab_qnty')]-$row[('pi_qty')];
				$pi_balance_amount=$row[('booking_amount')]-$row[('pi_amt')];
				 echo number_format($pi_balance_qty,4); ?>   </p></td>
                 
				<td width="80" align="right" title="Booking Amount-PI Amount"><p><? echo number_format($pi_balance_amount,4); ?></p></td>
                <td width="100" align="center"><p style="word-break:break-all"><? echo $pi_numbers; ?></p></td>
				<td width="" align="center"  title="Booking supplier"><p style="word-break:break-all"><? echo $booking_suppliers; ?></p></td>
				 
			</tr>
            <?
			$i++;$m++;
			$tot_pi_balance_qty+=$pi_balance_qty;
			$tot_pi_balance_value+=$pi_balance_amount;
			
			$tot_pi_value+=$row[('pi_amt')];
			$tot_pi_qty+=$row[('pi_qty')];
			$tot_fab_value_balance+=$fab_value_balance;
			$tot_fab_qty_balance+=$fab_bal_qty;
			$tot_booking_amount+=$row[('booking_amount')];
			$tot_booking_qty+=$row[('fin_fab_qnty')];
			$tot_budget_cons_dzn+=$row[('cons')];
			$tot_budget_req_qty+=$row[('req_qty')];
			$tot_sourcing_amount_dzn+=$row[('sourcing_amount')];
			$tot_sourcing_req_amount+=$row[('sourcing_req_amount')];
			$tot_budget_amount_dzn+=$row[('amount')];
			$tot_budget_req_amount+=$row[('req_amount')];
			
		
			
			
			}
		}
			
			//tot_style_cm_dzn
			?>
        
        </table>
        </div>
         <table class="tbl_bottom" width="<? echo $width_td;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="150">Total Cost(Without CM)</td>
                 <td width="60"></td>
                <td width="80"><? echo number_format($tot_budget_amount_dzn,4);?></td>
                <td width="80"><? echo number_format($tot_budget_req_amount,4);?></td>
                <td width="80"><? echo number_format($tot_sourcing_amount_dzn,4);?></td>
                <td width="80"><? echo number_format($tot_sourcing_req_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_cons_dzn,4);?></td>
                <td width="80"><? echo number_format($tot_budget_req_qty,4);?></td>
                <td width="80"><? echo number_format($tot_booking_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,2);?></td>
                <td width="80"><? echo number_format($tot_booking_amount,4);?></td>
                <td width="80"><? echo number_format($tot_fab_qty_balance,4);?></td>
                <td width="80"><? echo number_format($tot_fab_value_balance,4);?></td>
                <td width="80"><? echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? echo number_format($tot_pi_value,4);?></td>
                <td width="80"><? echo number_format($tot_pi_balance_qty,4);?></td>
                <td width="80"><? echo number_format($tot_pi_balance_value,4);?></td>
                <td width="100"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                <td width=""><? //echo number_format($total_other_cost_conv,2);?></td>
                
            </tr>
            <?
            $tot_cm_dzn=($tot_style_cm_dzn*$tot_po_qty)/12;
			$fob_pcs_usd_dzn=($tot_budget_amount_dzn+$tot_style_cm_dzn)/12;
			
			$grnd_tot_cm_dzn=$tot_cm_dzn+$tot_budget_amount_dzn;
			$grnd_tot_budget_amt=$tot_cm_dzn+$tot_budget_req_amount;
			$cm_amt_sourcing_amt=$grnd_tot_budget_amt-$tot_sourcing_req_amount;
			
			$grnd_tot_sourcing_amt=$tot_sourcing_req_amount+$cm_amt_sourcing_amt;
			  
			?>
            <tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="150">CM Amount/Dzn(USD)</td>
                 <td width="60"></td>
                <td width="80"><? echo number_format($tot_style_cm_dzn,4);?></td>
                <td width="80" title="CM Amount/Dzn*PoQty(<? echo $tot_po_qty;?>)/12"><? echo number_format($tot_cm_dzn,4);?></td>
                <td width="80" title="FOB Pcs Usd Dzn*12-Total Cost(Without CM) Sourcing Amt Dzn"><?  $cm_amt_sourcing_amt_dzn=($fob_pcs_usd_dzn*12)-$tot_sourcing_amount_dzn;
				echo number_format($cm_amt_sourcing_amt_dzn,4);?></td>
                <td width="80" title="Grand Total Com Budget amt-Sourcing Amt"><? 
				//cm_amt_sourcing_amt
				echo number_format($cm_amt_sourcing_amt,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_cons_dzn,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_req_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,2);?></td>
                <td width="80"><? //echo number_format($tot_booking_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_qty_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_value_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_value,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_value,4);?></td>
                <td width="100"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                <td width=""><? //echo number_format($total_other_cost_conv,2);?></td>
                
            </tr>
            <?
           
			?>
             <tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="150">Grand Total Cost</td>
                 <td width="60"></td>
                <td width="80"><? //echo number_format($tot_style_cm_dzn,4);?></td>
                <td width="80" title="Total Value -Tot CM Value"><? echo number_format($grnd_tot_budget_amt,4);?></td>
                <td width="80"><? //echo number_format($tot_sourcing_amount_dzn,4);?></td>
                <td width="80"><? echo number_format($grnd_tot_sourcing_amt,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_cons_dzn,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_req_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,2);?></td>
                <td width="80"><? //echo number_format($tot_booking_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_qty_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_value_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_value,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_value,4);?></td>
                <td width="100"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                <td width=""><? //echo number_format($total_other_cost_conv,2);?></td>
                
            </tr>
            <?
          
			?>
             <tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="150">FOB/<?=$unit_of_measurement[$order_uom];?>(USD)</td>
                 <td width="60"></td>
               
                <td width="80" title="CM Amount/Dzn+Total Cost(Without CM)/12"><? echo number_format($fob_pcs_usd_dzn,4);?></td>
                  <td width="80"><? //echo number_format($tot_sourcing_amount_dzn,4);?></td>
                 <td width="80" title="Grand Tot Cost(<? echo $grnd_tot_sourcing_amt;?>)/Po Qty(<? echo $tot_po_qty;?>)"><? echo number_format($grnd_tot_sourcing_amt/$tot_po_qty,4);?></td>
              
                <td width="80"><? //echo number_format($tot_sourcing_req_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_cons_dzn,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_req_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,2);?></td>
                <td width="80"><? //echo number_format($tot_booking_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_qty_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_value_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_value,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_value,4);?></td>
                <td width="100"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                <td width=""><? //echo number_format($total_other_cost_conv,2);?></td>
                
            </tr>
             <tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="150">SMV/<?=$unit_of_measurement[$order_uom];?></td>
                 <td width="60"></td>
                <td width="80"><? //echo number_format($tot_style_cm_dzn,4);?></td>
                <td width="80" ><? echo number_format($tot_job_smv,4);?></td>
                <td width="80"><? //echo number_format($tot_sourcing_amount_dzn,4);?></td>
                <td width="80"><? //echo number_format($tot_sourcing_req_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_cons_dzn,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_req_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,2);?></td>
                <td width="80"><? //echo number_format($tot_booking_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_qty_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_value_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_value,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_value,4);?></td>
                <td width="100"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                <td width=""><? //echo number_format($total_other_cost_conv,2);?></td>
                
            </tr>
              <tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="150">EPM(USD)</td>
                 <td width="60"></td>
                <td width="80"><? //echo number_format($tot_style_cm_dzn,4);?></td>
                <td width="80" title="CM Amount Dzn/12/SMV"><? echo number_format(($tot_style_cm_dzn/12)/$tot_job_smv,4);?></td>
                <td width="80"><? //echo number_format($tot_sourcing_amount_dzn,4);?></td>
                <td width="80" title="CM Amount Sourcing /12/SMV"><? echo number_format(($cm_amt_sourcing_amt_dzn/12)/$tot_job_smv,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_cons_dzn,4);?></td>
                <td width="80"><? //echo number_format($tot_budget_req_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_booking_qty,2);?></td>
                <td width="80"><? //echo number_format($tot_booking_amount,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_qty_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_fab_value_balance,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_value,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_qty,4);?></td>
                <td width="80"><? //echo number_format($tot_pi_balance_value,4);?></td>
                <td width="100"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                <td width=""><? //echo number_format($total_other_cost_conv,2);?></td>
                
            </tr>
            
        </table>
	
	</div>
		<?
	

	
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

if($action=="report_generate_woven")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	$job_no =$job_no;
	 $sql_job=sql_select("select a.quotation_id,b.job_no_mst from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and b.job_no_mst in('$job_no')");
	// echo "select a.quotation_id,b.job_no_mst from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and b.job_no_mst in('$job_no')";die;
		foreach($sql_job as $row)
		{
			$quotationId=$row[csf('quotation_id')];
			$job_no=$row[csf('job_no_mst')];
		}
	
	// $job_no = return_field_value("b.job_no_mst as job_no_mst", "wo_po_break_down b", "b.id in($po_id)", "job_no_mst");

	?>
	<script type="text/javascript">
		
	</script>
	<?
	 
	  $typeId=$row[('type')];
					if($typeId=='fabric') $color_msg="#009933";
					else if($typeId=='trim') $color_msg="#FF9933";
					else if($typeId=='embl') $color_msg="#6699CC";
					else if($typeId=='Other') $color_msg="#FFFFFF";
					else if($typeId=='commission') $color_msg="#FFFF00";
					

	//echo '<pre>';print_r($issue_dtls_arr);
	if($wo_type=='fabric')
	{
     $sql_booking="select  a.booking_type,a.uom,c.inserted_by, c.insert_date,c.booking_date,c.item_category,c.id as booking_id,c.booking_no,c.is_short,c.supplier_id,c.pay_mode,a.fin_fab_qnty,a.rate,a.grey_fab_qnty,a.amount,b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id,  b.fabric_description as fab_desc,b.uom,b.avg_finish_cons,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_booking_mst c,wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls  b where b.id=a.pre_cost_fabric_cost_dtls_id and c.booking_no=a.booking_no and a.booking_type=1 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and b.job_no ='".$job_no."' and a.pre_cost_fabric_cost_dtls_id in($booking_id) and b.id in($descrip)  order by b.id";
	//echo $sql_booking="select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.job_no, b.booking_no, b.booking_type, b.is_short, b.description,a.supplier_id, b.uom, b.wo_qnty, b.rate, b.amount,b.inserted_by, b.insert_date  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no ='".$job_no."'   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$result_booking =sql_select($sql_booking);

		foreach ($result_booking as $row) 
		{
			
			$supplier_id=$row[csf('supplier_id')];
			$pay_modeId=$row[csf('pay_mode')];
			$fab_dess=$row[csf("fab_desc")];
			if($pay_modeId==3 || $pay_modeId==5)
			{
				//supplier_arr
				$booking_supplier=$company_arr[$supplier_id];
			}
			else
			{
				$booking_supplier=$supplier_arr[$supplier_id];
			}
			//echo $supplier_id.'sds';
			//
			if($row[csf("is_short")]==1)
			{
				$is_short_msg="Short";
			}
			else if($row[csf("is_short")]==2)
			{
				$is_short_msg="Main";
			}
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['wo_qnty']+=$row[csf("grey_fab_qnty")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['amount']+=$row[csf("grey_fab_qnty")]*$row[csf("rate")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['job_no']=$row[csf("job_no")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['item_category']=$row[csf("item_category")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['supplier']=$booking_supplier;
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['booking_type']=$is_short_msg;
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['booking_date']=$row[csf("booking_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['uom']=$row[csf("uom")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['inserted_by']=$row[csf("inserted_by")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['insert_date']=$row[csf("insert_date")];
		 }
	}
	if($wo_type=='trim')
	{
     
	    $sql_booking="select a.booking_type,a.uom,d.is_short,d.inserted_by, d.insert_date,d.booking_date,d.item_category,d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,b.seq,b.id,c.trim_type,c.item_name,b.description,b.job_no,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_mst d,wo_booking_dtls a,wo_pre_cost_trim_cost_dtls b,lib_item_group c where  b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and c.id=b.trim_group  and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.trim_type in(1,2) and a.id in($booking_id) and b.job_no ='".$job_no."' and b.id in($descrip)  order by b.seq";
			
	$result_booking =sql_select($sql_booking);

		foreach ($result_booking as $row)
		 {
			
			if($row[csf("is_short")]==1)
			{
				$is_short_msg="Short";
			}
			else if($row[csf("is_short")]==2)
			{
				$is_short_msg="Main";
			}
			
			$supplier_id=$row[csf('supplier_id')];
			$pay_modeId=$row[csf('pay_mode')];
			$fab_dess=$row[csf("fab_desc")];
			if($pay_modeId==3 || $pay_modeId==5)
			{
				//supplier_arr
				$booking_supplier=$company_arr[$supplier_id];
			}
			else
			{
				$booking_supplier=$supplier_arr[$supplier_id];
			}
			 $description=$row[csf('description')];
			if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
			$item_id=$row[csf('item_name')].$descriptionCond;
			
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['wo_qnty']+=$row[csf("wo_qnty")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['amount']+=$row[csf("amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['job_no']=$row[csf("job_no")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['item_category']=$row[csf("item_category")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['supplier']=$booking_supplier;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_type']=$is_short_msg;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_date']=$row[csf("booking_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['uom']=$row[csf("uom")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['inserted_by']=$row[csf("inserted_by")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['insert_date']=$row[csf("insert_date")];
		}
	}
	if($wo_type=='embl')
	{
     
	    $sql_booking="select a.booking_type,a.uom,d.is_short,d.inserted_by, d.insert_date,d.booking_date,d.item_category,d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts,a.uom as uom, b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_mst d,wo_booking_dtls a,wo_pre_cost_embe_cost_dtls b where  b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no   and a.booking_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0  and a.id in($booking_id) and b.job_no ='".$job_no."' and b.id in($descrip)  order by b.emb_name";
			
	$result_booking =sql_select($sql_booking);
 
		foreach ($result_booking as $row)
		 {
			
			if($row[csf("is_short")]==1)
			{
				$is_short_msg="Short";
			}
			else if($row[csf("is_short")]==2)
			{
				$is_short_msg="Main";
			}
			
			$supplier_id=$row[csf('supplier_id')];
			$pay_modeId=$row[csf('pay_mode')];
			//echo $pay_modeId.'dD';
			$fab_dess=$row[csf("fab_desc")];
			if($pay_modeId==3 || $pay_modeId==5)
			{
				
				$booking_supplier=$company_arr[$supplier_id];
			}
			else
			{
				$booking_supplier=$supplier_arr[$supplier_id];
			}
			$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
				$job_no=$row[csf('job_no')];
			//echo $emb_name_id.'d';
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==3) //Wash type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_wash_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				
			// $description=$row[csf('description')];
			//if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
			 $item_id=$emb_name;
			//echo $booking_supplier.'d';
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['wo_qnty']+=$row[csf("wo_qnty")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['amount']+=$row[csf("amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['job_no']=$row[csf("job_no")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['item_category']=$row[csf("item_category")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['supplier']=$booking_supplier;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_type']=$is_short_msg;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_date']=$row[csf("booking_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['uom']=$row[csf("uom")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['inserted_by']=$row[csf("inserted_by")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['insert_date']=$row[csf("insert_date")];
		}
	}
	
	
	
	$lib_user_arr=return_library_array( "select user_name,id from user_passwd", "id", "user_name");	
	echo load_freeze_divs ("../../",$permission);  
	 $width=1000;
	?>
	<table class="rpt_table" border="1" cellpadding="5" cellspacing="2" width="<? echo $width;?>" rules="all">
		<caption> <b>Booking  Summary </b> </caption>
		<thead>
			<th width="20">SL#</th>
            <th width="100">Item Category</th>
			<th width="70">Booking Type</th>
			<th width="100">Booking NO</th>
			<th width="70">Booking Date</th>
            <th width="100">Item Description</th>
            <th width="60">UOM</th>
            <th width="70">Booking  Qty</th>
            <th width="70">Rate</th>
            <th width="70">Booking Value</th>
            <th width="100">Supplier</th>
            <th width="70">Booking Insert Date</th>
            <th width="">Insert User Name</th>
		</thead>
		<?php
		$issue_total = 0;$m=1;$tot_fin_fab_qnty=$tot_amount=0;
		foreach ($wo_booking_arr as $booking_no=>$bookingData) 
		{
			foreach ($bookingData as $fab_des => $row) 
			{
				if($m%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
			
				$booking=$row['booking'];
				//$detarmination_id=$issue_arr2[$prod_id][$booking]['detarmination_id'];
				//$des=explode("_",str_replace("'","",$desc));
				//echo $row['inserted_by'].'df';

				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $m; ?>" style="font-size:11px">
					 <td align="center" ><? echo $m;?></td>
                    <td align="center" ><? echo $item_category[$row['item_category']];?></td>
					<td align="center" ><? echo $row['booking_type'];?></td>
					<td align="center" ><? echo $booking_no;?></td>
                    <td align="center" ><? echo $row['booking_date'];?></td>
                    <td align="center" ><? echo $fab_des;?></td>
                    <td align="center" ><? echo $unit_of_measurement[$row['uom']];?></td>
                   
                    <td align="right" ><? echo number_format($row['wo_qnty'],4);?></td>
                    <td align="right" ><? echo number_format($row['amount']/$row['wo_qnty'],4);?></td>
                    <td align="right" ><? echo number_format($row['amount'],4);?></td>
                    <td align="center" ><? echo $row['supplier'];?></td>
                    <td align="center" ><? echo $row['insert_date'];?></td>
                    <td align="center" ><? echo $lib_user_arr[$row['inserted_by']];?></td>
                    
					 
				</tr>
				<?
				$tot_fin_fab_qnty += $row['wo_qnty'];
				$tot_amount += $row['amount'];
				
				$m++;
			}
		}
		?>
		<tfoot>
		<tr>
			<th colspan="7" align="right">Grand Total</th>
			<th align="right"><? echo number_format($tot_fin_fab_qnty,4,'.','');?></th>
            <th align="right"><? //echo number_format($tot_amount,2,'.','');?></th>
            <th align="right"><? echo number_format($tot_amount,4,'.','');?></th>
            <th align="right"><? //echo number_format($issue_total,2,'.','');?></th>
            <th align="right"><? //echo number_format($issue_total,2,'.','');?></th>
            <th align="right"><? //echo number_format($issue_total,2,'.','');?></th>
		</tr>
		</tfoot>
	</table>
	 
	
	<?
	exit();
}

 if($action=="report_generate_woven_pi")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	$job_no =$job_no;
	// echo $descrip.'ddddddd';
	

	?>
	<script type="text/javascript">
		
	</script>
	<?
	 
	$typeId=$row[('type')];
	if($typeId=='fabric') $color_msg="#009933";
	else if($typeId=='trim') $color_msg="#FF9933";
	else if($typeId=='embl') $color_msg="#6699CC";
	else if($typeId=='Other') $color_msg="#FFFFFF";
	else if($typeId=='commission') $color_msg="#FFFF00";
					

	//echo $wo_type;
	if($wo_type=='fabric')
	{
		$wpr_data_array=sql_select("SELECT b.job_no,c.fabric_description,b.fabric_color_id, c.construction as construction, c.composition as copmposition, b.gsm_weight as weight,  b.dia_width as width, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.fin_fab_qnty*b.rate) as amount
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c, com_pi_item_details d
		where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.job_no=b.job_no and d.pi_id in ($descrip) and a.id=d.work_order_id and b.fabric_color_id=d.color_id  and b.dia_width=d.dia_width and c.construction=d.fabric_construction and c.composition=d.fabric_composition and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
		group by a.booking_no, b.fabric_color_id, c.fabric_description,b.job_no,c.construction, c.composition, b.gsm_weight, b.dia_width");

		$finish_req_qnty_arr=$finish_req_qnty_arr2=$finish_req_amount_arr=$finish_req_amount_arr2=array();			 
		foreach($wpr_data_array as $row)
		{
			$key=$row[csf('fabric_color_id')].'_'.$row[csf('construction')].'_'.$row[csf('copmposition')].'_'.$row[csf('width')].'_'.$row[csf('weight')];	
			$finish_req_qnty_arr[$key]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
			$finish_req_qnty_arr2[$row[csf('job_no')]][$key]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
			$finish_req_amount_arr[$key]["finish_reqt_amount"]+=$row[csf('amount')];
			$finish_req_amount_arr2[$row[csf('job_no')]][$key]["finish_reqt_amount"]+=$row[csf('amount')];
		}

		$pi_dtls_id=rtrim($pi_dtls_id,',');
		
	    $sql_pi = "SELECT a.id as pi_mst_id,a.inserted_by, a.insert_date,a.supplier_id,a.pi_number,a.pi_date,a.item_category_id as cat_id,a.pi_number,b.work_order_no as booking_no,b.work_order_id,b.determination_id,b.fabric_composition,b.uom,(b.quantity) as qty,b.net_pi_amount,b.amount,b.rate,b.amount,b.item_group,b.item_description,c.booking_date,c.booking_type,c.is_short, b.color_id, b.fab_weight, b.dia_width, b.fabric_construction from com_pi_master_details a, com_pi_item_details b,wo_booking_mst c where a.id=b.pi_id and c.booking_no=b.work_order_no and a.pi_basis_id=1  and b.item_category_id in(2,3) and b.status_active=1 and b.is_deleted=0  and a.id in($descrip)  and b.determination_id in($deter_min) and b.id in($pi_dtls_id)  ";//wo_id_arr
		// echo $sql_pi;
		$result_pi =sql_select($sql_pi);
		foreach ($result_pi as $row) 
		{
			if($row[csf("is_short")]==1)
			{
				$is_short_msg="Short";
			}
			else if($row[csf("is_short")]==2)
			{
				$is_short_msg="Main";
			}
			
			$supplier_id=$row[csf('supplier_id')];
			$pay_modeId=$row[csf('pay_mode')];
			$fab_dess=$row[csf('fabric_construction')];
			$all_data=$row[csf('color_id')].'_'.$row[csf('fabric_construction')].'_'.$row[csf("fabric_composition")].'_'.$row[csf('dia_width')].'_'.$row[csf('fab_weight')];

			$requiredQnty_pi=$finish_req_qnty_arr[$all_data]["finish_reqt_qnty"];
			$job_req=$finish_req_qnty_arr2[$job_no][$all_data]["finish_reqt_qnty"];
			$requiredAmnt_pi=$finish_req_amount_arr[$all_data]["finish_reqt_amount"];
			$job_amnt=$finish_req_amount_arr2[$job_no][$all_data]["finish_reqt_amount"];

			$pi_qty=($job_req*1/$requiredQnty_pi*1)*$row[csf("qty")];
			$pi_amount=($job_amnt*1/$requiredAmnt_pi*1)*$row[csf("amount")];

			$booking_supplier=$supplier_arr[$supplier_id];
			
			//echo $supplier_id.'sds';
			
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['pi_qty']+=$pi_qty;
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['pi_amount']+=$pi_amount;
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['net_pi_amount']+=$row[csf("net_pi_amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['pi_number']=$row[csf("pi_number")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['item_category']=$row[csf("cat_id")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['determination_id']=$row[csf("determination_id")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['supplier']=$booking_supplier;
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['booking_type']=$is_short_msg;
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['booking_date']=$row[csf("booking_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['pi_date']=$row[csf("pi_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['uom']=$row[csf("uom")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['inserted_by']=$row[csf("inserted_by")];
			$wo_booking_arr[$row[csf("booking_no")]][$fab_dess]['insert_date']=$row[csf("insert_date")];
			
			$work_order_id_arr[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
		}
		$sql_booking="select  a.booking_type,a.uom,c.inserted_by, c.insert_date,c.booking_date,c.item_category,c.id as booking_id,c.booking_no,c.supplier_id,c.pay_mode,a.fin_fab_qnty,a.grey_fab_qnty,a.amount,b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id,  b.fabric_description as fab_desc,b.uom,b.avg_finish_cons,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_booking_mst c,wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls  b where b.id=a.pre_cost_fabric_cost_dtls_id and c.booking_no=a.booking_no and a.booking_type=1 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id in($deter_min) and a.pre_cost_fabric_cost_dtls_id in($booking_id)  and c.id in(".implode(",",$work_order_id_arr).")  and b.job_no ='".$job_no."' order by b.id";
		$bookresult = sql_select($sql_booking);
		foreach ($bookresult as $row) 
		{
			$booking_no=$row[csf('booking_no')];
			//if($row[csf('item_category')]==3) //Woven Fabric
			//{
			//$PiArr[$booking_no][$row[csf('determination_id')]]['pi_number'] .= $row[csf('pi_number')].',';
			//$PiArr[$booking_no][$row[csf('determination_id')]]['pi_mst_id'] .= $row[csf('pi_mst_id')].',';
			$BookingArr[$booking_no][$row[csf('deter_min_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$BookingArr[$booking_no][$row[csf('deter_min_id')]]['booking_amount'] += $row[csf('amount')];
			//$PiArr[$booking_no][$row[csf('determination_id')]]['pi_amt'] += $row[csf('amount')];
			//}
		
		}
		  // print_r($BookingArr);
	}
	if($wo_type=='trim')
	{
     
	  // $sql_booking="select a.booking_type,a.uom,d.inserted_by, d.insert_date,d.booking_date,d.item_category,d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,b.seq,b.id,c.trim_type,c.item_name,b.description,b.job_no,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_mst d,wo_booking_dtls a,wo_pre_cost_trim_cost_dtls b,lib_item_group c where  b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and c.id=b.trim_group  and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.trim_type in(1,2) and b.job_no ='".$job_no."' and b.id='$descrip'  order by b.seq"; 
	  $descrip=rtrim($descrip,',');
	    $trim_sql_pi = "select a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_id,b.work_order_no,b.determination_id,(b.quantity) as qty,b.rate,b.amount,b.item_group,b.item_description,c.booking_type from com_pi_master_details a, com_pi_item_details b,wo_booking_mst c where a.id=b.pi_id and c.id=b.work_order_id and a.pi_basis_id=1  and b.item_category_id in(4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.id in($descrip)  and b.item_group in($deter_min) and b.work_order_dtls_id in($booking_id)   ";//wo_id_arr
		
		$TrimiSQLresult = sql_select($trim_sql_pi);
		 
		foreach ($TrimiSQLresult as $row) {
			$job_nos=$trim_booking_NoArr[$row[csf('work_order_no')]];
			
			$trim_group=$trim_groupArr[$row[csf('item_group')]];
			//echo $trim_group.'DD';
			$description=$row[csf('item_description')];
			if($description==0) $description='';
			if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
			$item_id=$trim_group.$descriptionCond;
				
			$PiArr2[$job_nos][$item_id]['pi_number'] .= $row[csf('pi_number')].',';
			$PiArr2[$job_nos][$item_id]['pi_mst_id'] .= $row[csf('pi_mst_id')].',';
			$PiArr2[$job_nos][$item_id]['pi_qty'] += $row[csf('qty')];
			$PiArr2[$job_nos][$item_id]['pi_amt'] += $row[csf('amount')];
			
		}
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			//$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			//$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]=$row_f[csf('item_name')];
		}
	   $sql_pi = "select a.id as pi_mst_id,a.supplier_id,a.inserted_by, a.insert_date,a.supplier_id,a.pi_number,a.pi_date,a.item_category_id as cat_id,a.pi_number,b.work_order_id,b.work_order_no as booking_no,b.work_order_id,b.determination_id,b.fabric_composition,b.uom,(b.quantity) as qty,b.net_pi_amount,b.amount,b.rate,b.item_group,b.item_description,c.booking_date,c.booking_type,c.is_short from com_pi_master_details a, com_pi_item_details b,wo_booking_mst c where a.id=b.pi_id and c.booking_no=b.work_order_no and a.pi_basis_id=1  and c.booking_type=2 and b.item_category_id in(4) and b.status_active=1 and b.is_deleted=0  and a.id in($descrip)  and b.item_group in($deter_min)  and b.work_order_dtls_id in($booking_id) ";
			
		$result_pi =sql_select($sql_pi);

		foreach ($result_pi as $row)
		 {
			
			if($row[csf("is_short")]==1)
			{
				$is_short_msg="Short";
			}
			else if($row[csf("is_short")]==2)
			{
				$is_short_msg="Main";
			}
			$supplier_id=$row[csf('supplier_id')];
			$pay_modeId=$row[csf('pay_mode')];
			$fab_dess=$row[csf("fab_desc")];
			 
				$booking_supplier=$supplier_arr[$supplier_id];
			 
			 $description=$row[csf('description')];
			if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
			$item_id=$trim_groupArr[$row[csf('item_group')]].$descriptionCond;
			
			 $wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_qty']+=$row[csf("qty")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_amount']+=$row[csf("amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['net_pi_amount']+=$row[csf("net_pi_amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_number']=$row[csf("pi_number")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['item_category']=$row[csf("cat_id")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['determination_id']=$row[csf("determination_id")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['supplier']=$booking_supplier;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_type']=$is_short_msg;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_date']=$row[csf("booking_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_date']=$row[csf("pi_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['uom']=$row[csf("uom")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['inserted_by']=$row[csf("inserted_by")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['insert_date']=$row[csf("insert_date")];
			
			$work_order_id_arr[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
		}
		    $sql_booking="select  a.booking_type,a.uom,c.inserted_by, c.insert_date,c.booking_date,c.item_category,c.id as booking_id,c.booking_no,c.supplier_id,c.pay_mode,a.wo_qnty,a.grey_fab_qnty,a.amount,b.id, b.job_no,b.description as fab_desc,b.trim_group,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_booking_mst c,wo_booking_dtls a,wo_pre_cost_trim_cost_dtls  b where b.id=a.pre_cost_fabric_cost_dtls_id and c.booking_no=a.booking_no and a.booking_type=2 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and b.trim_group in($deter_min)  and a.job_no ='".$job_no."' and a.id in($booking_id) order by b.id";
			$bookresult = sql_select($sql_booking);
			foreach ($bookresult as $row) 
			{
			$booking_no=$row[csf('booking_no')];
			$BookingArr[$booking_no][$row[csf('item_group')]]['fin_fab_qnty'] += $row[csf('wo_qnty')];
			$BookingArr[$booking_no][$row[csf('item_group')]]['booking_amount'] += $row[csf('amount')];
			 
		   }
	}
	
	if($wo_type=='embl')
	{
     
	  // $sql_booking="select a.booking_type,a.uom,d.inserted_by, d.insert_date,d.booking_date,d.item_category,d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,b.seq,b.id,c.trim_type,c.item_name,b.description,b.job_no,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_mst d,wo_booking_dtls a,wo_pre_cost_trim_cost_dtls b,lib_item_group c where  b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and c.id=b.trim_group  and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.trim_type in(1,2) and b.job_no ='".$job_no."' and b.id='$descrip'  order by b.seq"; 
	  $descrip=rtrim($descrip,',');
	     $trim_sql_pi = "select a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_id,b.work_order_no,b.determination_id,(b.quantity) as qty,b.rate,b.amount,b.item_group,b.item_description,c.booking_type from com_pi_master_details a, com_pi_item_details b,wo_booking_mst c where a.id=b.pi_id and c.id=b.work_order_id and a.pi_basis_id=1  and b.item_category_id in(25) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.id in($descrip)   and b.work_order_dtls_id in($booking_id)   ";//wo_id_arr
		
		$TrimiSQLresult = sql_select($trim_sql_pi);
		 
		foreach ($TrimiSQLresult as $row) {
			$job_nos=$trim_booking_NoArr[$row[csf('work_order_no')]];
			
			$trim_group=$trim_groupArr[$row[csf('item_group')]];
			//echo $trim_group.'DD';
			$description=$row[csf('item_description')];
			if($description==0) $description='';
			if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
			$item_id=$trim_group.$descriptionCond;
				
			$PiArr2[$job_nos][$item_id]['pi_number'] .= $row[csf('pi_number')].',';
			$PiArr2[$job_nos][$item_id]['pi_mst_id'] .= $row[csf('pi_mst_id')].',';
			$PiArr2[$job_nos][$item_id]['pi_qty'] += $row[csf('qty')];
			$PiArr2[$job_nos][$item_id]['pi_amt'] += $row[csf('amount')];
			
		}
		
	   $sql_pi = "select a.id as pi_mst_id,a.supplier_id,a.inserted_by, a.insert_date,a.supplier_id,a.pi_number,a.pi_date,a.item_category_id as cat_id,a.pi_number,b.work_order_id,b.work_order_no as booking_no,b.embell_name as emb_name,b.embell_type as emb_type ,b.work_order_id,b.determination_id,b.fabric_composition,b.uom,(b.quantity) as qty,b.net_pi_amount,b.amount,b.rate,b.item_group,b.item_description,c.booking_date,c.booking_type,c.is_short from com_pi_master_details a, com_pi_item_details b,wo_booking_mst c where a.id=b.pi_id and c.booking_no=b.work_order_no and a.pi_basis_id=1  and c.booking_type=6 and b.item_category_id in(25) and b.status_active=1 and b.is_deleted=0  and a.id in($descrip)    and b.work_order_dtls_id in($booking_id) ";
			
		$result_pi =sql_select($sql_pi);

		foreach ($result_pi as $row)
		 {
			
			if($row[csf("is_short")]==1)
			{
				$is_short_msg="Short";
			}
			else if($row[csf("is_short")]==2)
			{
				$is_short_msg="Main";
			}
			$supplier_id=$row[csf('supplier_id')];
			$pay_modeId=$row[csf('pay_mode')];
			$fab_dess=$row[csf("fab_desc")];
			 
				$booking_supplier=$supplier_arr[$supplier_id];
			 
			 $description=$row[csf('description')];
			if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$supplier_id=$row[csf('supplier_id')];
				$pay_modeId=$row[csf('pay_mode')];
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
				$job_no=$row[csf('job_no')];
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==3) //Wash type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_wash_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				
			$item_id=$emb_name;
			
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_qty']+=$row[csf("qty")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_amount']+=$row[csf("amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['net_pi_amount']+=$row[csf("net_pi_amount")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_number']=$row[csf("pi_number")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['item_category']=$row[csf("cat_id")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['determination_id']=$item_id;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['supplier']=$booking_supplier;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_type']=$is_short_msg;
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['booking_date']=$row[csf("booking_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['pi_date']=$row[csf("pi_date")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['uom']=$row[csf("uom")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['inserted_by']=$row[csf("inserted_by")];
			$wo_booking_arr[$row[csf("booking_no")]][$item_id]['insert_date']=$row[csf("insert_date")];
			
			$work_order_id_arr[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
		}
		    $sql_booking="select  a.booking_type,b.emb_name,b.emb_type,a.uom,c.inserted_by, c.insert_date,c.booking_date,c.item_category,c.id as booking_id,c.booking_no,c.supplier_id,c.pay_mode,a.wo_qnty,a.grey_fab_qnty,a.amount,b.id, b.job_no,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp from wo_booking_mst c,wo_booking_dtls a,wo_pre_cost_embe_cost_dtls  b where b.id=a.pre_cost_fabric_cost_dtls_id and c.booking_no=a.booking_no and a.booking_type=6 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.id in($booking_id) order by b.id";
			$bookresult = sql_select($sql_booking);
			foreach ($bookresult as $row) 
			{
				$pay_modeId=$row[csf('pay_mode')];
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
				$job_no=$row[csf('job_no')];
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==3) //Wash type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_wash_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				
				
			$booking_no=$row[csf('booking_no')];
			$BookingArr[$booking_no][$emb_name]['fin_fab_qnty'] += $row[csf('wo_qnty')];
			$BookingArr[$booking_no][$emb_name]['booking_amount'] += $row[csf('amount')];
			 
		   }
	}
	
	
	$lib_user_arr=return_library_array( "select user_name,id from user_passwd", "id", "user_name");	
	echo load_freeze_divs ("../../",$permission);  
	 $width=1230;
	?>
	<table class="rpt_table" border="1" cellpadding="5" cellspacing="2" width="<? echo $width;?>" rules="all">
		<caption> <b>PI  Summary </b> </caption>
		<thead>
        <tr>
        <th colspan="15"> </th>
        <th colspan="2">Balance/ Excess(Qty/value)</th>
        <th colspan="3"> </th>
        
        </tr>
        <tr>
			<th width="20">SL#</th>
            <th width="100">Item Category</th>
			<th width="70">Booking Type</th>
			<th width="100">Booking No</th>
            <th width="100">PI No</th>
			<th width="70">Booking Date</th>
            <th width="70">PI Date</th>
            <th width="100">Item Description</th>
            <th width="60">UOM</th>
            <th width="70">Booking  Qty</th>
            <th width="70">Rate</th>
            <th width="70">Booking Value</th>
            
            <th width="70">PI Qty</th>
            <th width="70">Rate</th>
            <th width="70">PI Value</th>
            
            <th width="70"> Qty</th>
            <th width="70">Value</th>
            
            <th width="100">Supplier</th>
            <th width="70">PI Insert Date</th>
            <th width="">Insert User Name</th>
            </tr>
            
		</thead>
		<?php
		$issue_total = 0;$m=1;$tot_booking_amount=$tot_amount=$tot_pi_qty=$tot_pi_amount=$tot_ex_balanceQty=$tot_ex_balanceAmt=0;
		foreach ($wo_booking_arr as $booking_no=>$bookingData) 
		{
			foreach ($bookingData as $fab_des => $row) 
			{
				if($m%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
			
				$booking=$row['booking'];
				$determination_id=$row['determination_id'];
				$booking_qnty=$BookingArr[$booking_no][$row[('determination_id')]]['fin_fab_qnty'];
				$booking_amount=$BookingArr[$booking_no][$row[('determination_id')]]['booking_amount'];
				//$detarmination_id=$issue_arr2[$prod_id][$booking]['detarmination_id'];
				//$des=explode("_",str_replace("'","",$desc));
				//echo $row['inserted_by'].'df';

				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $m; ?>" style="font-size:11px">
					 <td align="center" ><? echo $m;?></td>
                    <td align="center" ><? echo $item_category[$row['item_category']];?></td>
					<td align="center" ><? echo $row['booking_type'];?></td>
					<td align="center" ><? echo $booking_no;?></td>
                    <td align="center" ><? echo $row['pi_number'];?></td>
                    <td align="center" ><? echo $row['booking_date'];?></td>
                    <td align="center" ><? echo $row['pi_date'];?></td>
                    <td align="center" ><? echo $fab_des;?></td>
                    <td align="center" ><? echo $unit_of_measurement[$row['uom']];?></td>
                   
                    <td align="right" ><? echo number_format($booking_qnty,4);?></td>
                    <td align="right" ><? echo number_format($booking_amount/$booking_qnty,4);?></td>
                    <td align="right" ><? echo number_format($booking_amount,4);?></td>
                    
                    <td align="right" ><? echo number_format($row['pi_qty'],4);?></td>
                    <td align="right" ><? echo number_format($row['pi_amount']/$row['pi_qty'],4);?></td>
                    <td align="right" ><? echo number_format($row['pi_amount'],4);?></td>
                    
                    <td align="right" title="BookingQty-PI Qty" ><? echo number_format($booking_qnty-$row['pi_qty'],4);?></td>
                    <td align="right" title="BookingAmount-PI Amount" ><? echo number_format($booking_amount-$row['pi_amount'],2);?></td>
                    <td align="center" ><? echo $row['supplier'];?></td>
                    <td align="center" ><? echo $row['insert_date'];?></td>
                    <td align="center" ><? echo $lib_user_arr[$row['inserted_by']];?></td>
                    
					 
				</tr>
				<?
				$tot_booking_qnty += $booking_qnty;
				$tot_booking_amount += $booking_amount;
				$tot_pi_amount += $row['pi_amount'];
				$tot_pi_qty += $row['pi_qty'];
				$tot_ex_balanceQty += $booking_qnty-$row['pi_qty'];
				$tot_ex_balanceAmt += $booking_amount-$row['pi_amount'];
				
				$m++;
			}
		}
		?>
		<tfoot>
		<tr>
			<th colspan="9" align="right">Grand Total</th>
            <th align="right"><? echo number_format($tot_booking_qnty,4,'.','');?></th>
             <th align="right"><? //echo number_format($tot_booking_amount,2,'.','');?></th>
            <th align="right"><? echo number_format($tot_booking_amount,4,'.','');?></th>
            <th align="right"><? echo number_format($tot_pi_qty,4,'.','');?></th>
            <th align="right"><? //echo number_format($tot_amount,2,'.','');?></th>
			<th align="right"><? echo number_format($tot_pi_amount,4,'.','');?></th>
          
            <th align="right"><? echo number_format($tot_ex_balanceQty,4,'.','');?></th>
            <th align="right"><? echo number_format($tot_ex_balanceAmt,4,'.','');?></th>
            <th align="right"><? //echo number_format($tot_amount,2,'.','');?></th>
            <th align="right"><? //echo number_format($issue_total,2,'.','');?></th>
            <th align="right"><? //echo number_format($issue_total,2,'.','');?></th>
		</tr>
		</tfoot>
	</table>
	 
	
	<?
	exit();
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
			
		/*	$fin_fab_trans_array=array();
		  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
		 	 sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, 
			 sum(CASE WHEN b.trans_type=5 THEN a.cons_amount END) AS in_amt,
			sum(CASE WHEN b.trans_type=6 THEN a.cons_amount END) AS out_amt, 
			c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258,15) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
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
		*/
			$i = 1;
			$total_trans_in_qnty = $total_trans_in_amt=0;
			 $sql_fin_trans="select  c.transfer_system_id, c.transfer_date, c.challan_no, c.from_order_id,c.to_order_id, b.prod_id, 
			 (b.quantity) as transfer_qnty,a.cons_amount
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258) and b.po_breakdown_id in ($po_id) and a.item_category=3 and a.transaction_type in(5) and b.trans_type in(5) ";
		  
			//$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id,a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in(5) and c.entry_form in (258) and c.po_breakdown_id in ($po_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id,a.from_order_id, b.from_prod_id, d.product_name_details";
			$result_in = sql_select($sql_fin_trans);
			foreach ($result_in as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				//$avg_rate=$avg_rate_array[$row[csf("from_prod_id")]];

				/*$fin_from_order_id=$row[csf("to_order_id")];
				$condition1= new condition();
				$condition1->po_id("in($fin_from_order_id)");
				$condition1->init();
				$conversion1= new conversion($condition1);
				//echo $conversion->getQuery(); die;
				$fin_conversion_costing_arr_process=$conversion1->getAmountArray_by_orderAndProcess();
				$conversion1= new conversion($condition1);
				$fin_conversion_costing_arr_process_qty=$conversion1->getQtyArray_by_orderAndProcess();
				// $knit_cost=$knit_qty=0;
					 $tot_dye_finish_cost_pre=0;$tot_dye_finish_cost_pre_qty=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
						{
							$tot_dye_finish_cost_pre+=$fin_conversion_costing_arr_process[$fin_from_order_id][$process_id];

							$tot_dye_finish_cost_pre_qty+=$fin_conversion_costing_arr_process_qty[$fin_from_order_id][$process_id];
						}
					}

					$finish_charge=$tot_dye_finish_cost_pre/$tot_dye_finish_cost_pre_qty;*/
					//echo $tot_dye_finish_cost_pre.'='.$tot_dye_finish_cost_pre_qty.'<br>';
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
				//$avg_rate=$avg_rate_array[$row[csf("from_prod_id")]];


					/*$fin_from_order_id=$row[csf("from_order_id")];
				$condition2= new condition();
				$condition2->po_id("in($fin_from_order_id)");
				$condition2->init();
				$conversion2= new conversion($condition2);
				//echo $conversion->getQuery(); die;
				$fin_conversion_costing_arr_process=$conversion2->getAmountArray_by_orderAndProcess();
				$conversion2= new conversion($condition2);
				$fin_conversion_costing_arr_process_qty=$conversion2->getQtyArray_by_orderAndProcess();
				// $knit_cost=$knit_qty=0;
					 $tot_dye_finish_cost_pre_out=0;$tot_dye_finish_cost_pre_qty_out=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
						{
							$tot_dye_finish_cost_pre_out+=$fin_conversion_costing_arr_process[$fin_from_order_id][$process_id];

							$tot_dye_finish_cost_pre_qty_out+=$fin_conversion_costing_arr_process_qty[$fin_from_order_id][$process_id];
						}
					}
					$finish_charge_out=$tot_dye_finish_cost_pre_out/$tot_dye_finish_cost_pre_qty_out;*/

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
			   WHERE     a.booking_no = b.booking_no
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