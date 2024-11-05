<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
if($approve_bom_sync!=1) { unset($_SESSION['logic_erp']); unset($_SESSION['page_permission']); }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
			function js_set_value( job_data )
			{
			 	var all_data=job_data.split("_");
			 	document.getElementById('job_id').value=all_data[0];
			 	document.getElementById('job_no').value=all_data[1];
			 	parent.emailwindow.hide();
			}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="970" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="12" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Buyer Name</th>
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="60">Season Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="80">Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="90">Order No</th>
                    <th width="130" colspan="2">Ship Date Range</th>
                    <th>
                        <input type="hidden" value="0" id="chk_job_wo_po">
                        <input type="hidden" id="job_id">
                        <input type="hidden" id="job_no">
                        <input type="hidden" id="garments_nature" value="<?=$garments_nature; ?>">
                    </th>
                </tr>
            </thead>
            <tr class="general">
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name","id,buyer_name",'', 1, "-- Select Buyer --","load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'order_entry_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, $blank_array,'', 1, "Brand",$selected, "" ); ?>
        		<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
        		<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <?=$cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_po_search_list_view', 'search_div', 'approve_bom_sync_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="12"><?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('approve_bom_sync_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<?=$cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'";
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]' $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]' "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]' "; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%' $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%' ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%' "; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%' $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%' ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%' "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]' $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]' ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]' "; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($data[13] !=0) $brand_cond = " and a.brand_id='$data[13]'"; else $brand_cond="";
	if($data[14] !=0) $season_cond = " and a.season_buyer_wise='$data[14]'"; else $season_cond="";
	if($data[15] !=0) $season_year_cond = " and a.season_year='$data[15]'"; else $season_year_cond="";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	//$company_arr=return_library_array( "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name",'id','company_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

	$pre_Cost_approv_status = array(0 =>'No',1=>'Yes',2=>'No',3=>'Yes');
	$sql= "select a.id, a.job_no_prefix_num, a.set_break_down, a.job_no, a.buyer_name, a.style_ref_no, a.quotation_id, a.job_quantity, a.order_uom, a.order_repeat_no, a.brand_id, a.season_year, a.season_buyer_wise, a.body_wash_color, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, to_char(a.insert_date,'YYYY') as year, c.id as pre_id, c.approved from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and a.garments_nature=$data[5] and c.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond $brand_cond $season_cond $season_year_cond order by a.job_no DESC";
	//echo $sql;
	$result=sql_select($sql);
	?> 
 	<div align="left" style=" margin-left:5px;margin-top:10px"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1170" align="left" class="rpt_table" >
 			<thead>
 				<th width="25">SL</th>
 				<th width="40">Job No</th> 
                <th width="40">J.Year</th>             
 				<th width="100">Buyer Name</th>
                <th width="70">Brand</th> 
                <th width="60">Season</th> 
                <th width="40">S. Year</th> 
                <th width="110">Style Ref.</th>
                <th width="70">B/W Color</th>
                <th width="50">Quot. ID</th>
                <th width="80">Job Qty.</th>
                <th width="115">PO Number</th>
                <th width="75">PO Qty.</th>
                <th width="60">Shipment Date</th>
 				<th width="50">Internal Ref</th>
 				<th width="50">File No</th>               
                <th width="50">Approve Status</th>
 				<th>BOM ID</th>               
 			</thead>
 		</table>
    	<div style="width:1170px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" id="list_view">  
 				<?
 				$i=1;
 				foreach($result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<?=$row[csf('id')].'_'.$row[csf('job_no')]; ?>'); "> 
                        <td width="25" align="center"><?=$i; ?></td> 
                        <td width="40" style="word-break:break-all" align="center"><?=$row[csf('job_no_prefix_num')]; ?></td>
                        <td width="40" style="word-break:break-all" align="center"><?=$row[csf('year')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
                        <td width="70" style="word-break:break-all"><?=$brand_arr[$row[csf('brand_id')]]; ?></td>
                        <td width="60" style="word-break:break-all"><?=$season_arr[$row[csf('season_buyer_wise')]]; ?></td>
                        <td width="40" style="word-break:break-all" align="center"><?=$row[csf('season_year')]; ?></td>
                        <td width="110" style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
                        <td width="70" style="word-break:break-all"><?=$color_library[$row[csf('body_wash_color')]]; ?></td>
                        <td width="50" style="word-break:break-all" align="center"><?=$row[csf('quotation_id')]; ?></td>
                        <td width="80" style="word-break:break-all" align="right"><?=$row[csf('job_quantity')]; ?></td>
                        <td width="115" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
                        <td width="75" style="word-break:break-all" align="right"><?=$row[csf('po_quantity')]; ?></td>
                        <td width="60" style="word-break:break-all"><?=change_date_format($row[csf('shipment_date')]); ?></td>
                        <td width="50" style="word-break:break-all"><?=$row[csf('grouping')]; ?></td>
                        <td width="50" style="word-break:break-all"><?=$row[csf('file_no')]; ?></td>
                        <td width="50" style="word-break:break-all"><?=$pre_Cost_approv_status[$row[csf('approved')]]; ?></td>
                        <td style="word-break:break-all"><?=$row[csf('pre_id')]; ?></td>
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

if($action=='report_generate'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_id=str_replace("'",'',$cbo_company_name);
	$buyer_id=str_replace("'",'',$cbo_buyer_name);
	$bomcost_head=str_replace("'",'',$hidden_costhead_id);
	$newadd_type=str_replace("'",'',$hidden_newadd_type_id);
	$job_id=str_replace("'",'',$hidden_job_id);
	$job_no=str_replace("'",'',$txt_job_no);
	
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$colorArr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0","id","size_name"); 
	
	$sqlpo="SELECT B.ID, B.PO_NUMBER, B.PO_QUANTITY, B.SHIPMENT_DATE, MIN(C.ID) AS COLOR_SIZE_TABLE_ID, MIN(COLOR_ORDER) AS COLOR_ORDER, MIN(SIZE_ORDER) AS SIZE_ORDER, C.COLOR_NUMBER_ID, C.SIZE_NUMBER_ID, SUM(C.PLAN_CUT_QNTY) AS PLAN_CUT_QNTY 
	FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B, WO_PO_COLOR_SIZE_BREAKDOWN C 
	WHERE A.ID=B.JOB_ID AND A.ID=C.JOB_ID AND B.ID=C.PO_BREAK_DOWN_ID AND A.ID='$job_id' AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 
	GROUP BY B.ID, B.PO_NUMBER, B.PO_QUANTITY, B.SHIPMENT_DATE, C.COLOR_NUMBER_ID, C.SIZE_NUMBER_ID ORDER BY B.ID, COLOR_ORDER, SIZE_ORDER ASC";
	$sqlpoArr=sql_select($sqlpo); $podataArr=array();
	foreach($sqlpoArr as $prow)
	{
		if($newadd_type==1)//PO
		{
			$podataArr[$prow['ID']]=$prow['PO_NUMBER'];
		}
		else if($newadd_type==2)//Color
		{
			$podataArr[$prow['ID']][$prow['COLOR_NUMBER_ID']]=$prow['PO_NUMBER'];
		}
		else if($newadd_type==3)//Size
		{
			$podataArr[$prow['ID']][$prow['COLOR_NUMBER_ID']][$prow['SIZE_NUMBER_ID']]=$prow['PO_NUMBER'];
		}
	}
	unset($sqlpoArr);
	$appbomdataarr=array(); $isNullPoArr=array(); $dtlsDataArr=array();
	if($bomcost_head==1)//Fabric
	{
		$sqlbom="SELECT A.ID, A.JOB_NO, A.ITEM_NUMBER_ID, A.BODY_PART_ID, A.FAB_NATURE_ID, A.COLOR_TYPE_ID, A.LIB_YARN_COUNT_DETER_ID AS LIB_YARN_COUNT_DETER_ID, A.CONSTRUCTION, A.COMPOSITION, A.FABRIC_DESCRIPTION, A.GSM_WEIGHT, A.AVG_CONS, A.FABRIC_SOURCE, A.RATE, A.AMOUNT, A.AVG_FINISH_CONS, A.AVG_PROCESS_LOSS, A.WIDTH_DIA_TYPE, A.UOM, A.BODY_PART_TYPE, B.ID AS DTLSID, B.PRE_COST_FABRIC_COST_DTLS_ID as FABDTLSID, B.PO_BREAK_DOWN_ID, B.COLOR_NUMBER_ID, B.GMTS_SIZES, B.DIA_WIDTH, B.ITEM_SIZE, B.CONS, B.PROCESS_LOSS_PERCENT, B.REQUIRMENT, B.PCS, B.COLOR_SIZE_TABLE_ID, B.RATE as DTLSRATE, B.AMOUNT as DTLSAMT, B.REMARKS FROM WO_PRE_COST_FABRIC_COST_DTLS A LEFT JOIN WO_PRE_COS_FAB_CO_AVG_CON_DTLS B ON B.JOB_ID='$job_id' AND A.ID=B.PRE_COST_FABRIC_COST_DTLS_ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 WHERE A.JOB_ID='$job_id' AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 ORDER BY A.SEQ ASC";
		//echo $sqlbom; die;
		$sqlbomArr=sql_select($sqlbom);
		foreach($sqlbomArr as $frow)
		{
			$strfabmst=""; $strfabdtls=""; $addedtype="";
			$strfabmst=$frow['ID'].'_'.$frow['ITEM_NUMBER_ID'].'_'.$frow['BODY_PART_ID'].'_'.$frow['FAB_NATURE_ID'].'_'.$frow['COLOR_TYPE_ID'].'_'.$frow['LIB_YARN_COUNT_DETER_ID'].'_'.$frow['FABRIC_SOURCE'].'_'.$frow['SEQ'].'_'.$frow['FABRIC_DESCRIPTION'].'_'.$frow['GSM_WEIGHT'].'_'.$frow['AVG_CONS'].'_'.$frow['RATE'].'_'.$frow['AMOUNT'].'_'.$frow['UOM'].'_'.$frow['BODY_PART_TYPE'];
			
			if($dtlsDataArr[$strfabmst]=="") $dtlsDataArr[$strfabmst]=$frow['DIA_WIDTH'].'_'.$frow['ITEM_SIZE'].'_'.$frow['CONS'].'_'.$frow['PROCESS_LOSS_PERCENT'].'_'.$frow['REQUIRMENT'].'_'.$frow['PCS'].'_'.$frow['DTLSRATE'].'_'.$frow['DTLSAMT'].'_'.$frow['REMARKS'];
			else $dtlsDataArr[$strfabmst].='**'.$frow['DIA_WIDTH'].'_'.$frow['ITEM_SIZE'].'_'.$frow['CONS'].'_'.$frow['PROCESS_LOSS_PERCENT'].'_'.$frow['REQUIRMENT'].'_'.$frow['PCS'].'_'.$frow['DTLSRATE'].'_'.$frow['DTLSAMT'].'_'.$frow['REMARKS'];
			
			if($newadd_type==1)//PO
			{
				$isNullPoArr[$frow['FABDTLSID']][$frow['ITEM_NUMBER_ID']][$frow['PO_BREAK_DOWN_ID']]=$frow['CONS'];
			}
			else if($newadd_type==2)//Color
			{
				$isNullPoArr[$frow['FABDTLSID']][$frow['ITEM_NUMBER_ID']][$frow['PO_BREAK_DOWN_ID']][$frow['COLOR_NUMBER_ID']]=$frow['CONS'];
			}
			else if($newadd_type==3)//Size
			{
				$isNullPoArr[$frow['FABDTLSID']][$frow['ITEM_NUMBER_ID']][$frow['PO_BREAK_DOWN_ID']][$frow['COLOR_NUMBER_ID']][$frow['GMTS_SIZES']]=$frow['CONS'];
			}
		}
		unset($sqlbomArr);
		?>
        <table width="1400" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
            <thead>
                <tr>
                	<th width="30">Seq</th>
                    <th width="100">Gmts Item</th>
                    <th width="100">Body Part</th>
                    <th width="60">Body Part Type</th>
                    <th width="100">Fab Nature</th>
                    <th width="70">Color Type</th>
                    <th width="240">Fabric Description</th>
                    <th width="80">Fabric Source</th>
                    <th width="40">GSM/ Weight</th>
                    <th width="50">Uom</th>
                    <th width="65">Avg. Grey Cons</th>
                    <th width="40">Rate</th>
                    <th width="60">Amount</th>
                    <?
					if($newadd_type==1) {//PO
						?>
                        <th width="120">PO NO</th>
                        <?
					}
					else if($newadd_type==2) {//Color
						?>
                        <th width="120">PO NO</th>
                        <th width="120">Gmts. Color</th>
                        <?
					}
					else if($newadd_type==3) {//Size
						?>
                        <th width="120">PO NO</th>
                        <th width="120">Gmts. Color</th>
                        <th width="70">Gmts. Size</th>
                        <?
					}
					?>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$i=1;
				foreach ($dtlsDataArr as $mstdata=>$dtlsdata)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $dtlsdata.'<br>';
					$exmst=explode("_",$mstdata);
					$mstid=$exmst[0];
					$gmtsitemid=$exmst[1];
					$bodypartid=$exmst[2];
					$fabnatureid=$exmst[3];
					$colortypeid=$exmst[4];
					$libyarndetar_id=$exmst[5];
					$fabricsourceid=$exmst[6];
					$seq=$exmst[7];
					$fabricdescription=$exmst[8];
					$gsmweight=$exmst[9];
					$avgcons=$exmst[10];
					$avgrate=$exmst[11];
					$avgamt=$exmst[12];
					$uom=$exmst[13];
					$bodyparttypeid=$exmst[14];
					
					$exdtls=array_filter(array_unique(explode("**",$dtlsdata)));
					$dtlsuniquedata=implode(",",$exdtls);
					$tdcolor=""; $btnmode="";
					if( count($exdtls)!=1 ) 
					{
						$tdcolor='bgcolor="#FF0000"'; $btnmode="display:none";
					}
					else 
					{
						$tdcolor='bgcolor="#FFFF00"'; $btnmode="";
					}
					if($newadd_type==1)//Po Add
					{
						foreach($podataArr as $poid=>$pono)
						{
							if($isNullPoArr[$mstid][$gmtsitemid][$poid]=="")
							{
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
								<td width="30"><?=$seq; ?></td>
								<td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
								<td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
								<td width="60" style="word-break:break-all"><?=$body_part_type[$bodyparttypeid]; ?></td>
								<td width="100" style="word-break:break-all"><?=$item_category[$fabnatureid]; ?></td>
								<td width="70" style="word-break:break-all"><?=$color_type[$colortypeid]; ?></td>
								<td width="240" style="word-break:break-all"><?=$fabricdescription; ?></td>
								<td width="80" style="word-break:break-all"><?=$fabric_source[$fabricsourceid]; ?></td>
								<td width="40" style="word-break:break-all"><?=$gsmweight; ?></td>
								<td width="50" style="word-break:break-all"><?=$unit_of_measurement[$uom]; ?></td>
								<td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
								<td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
								<td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
								<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
								<td align="center">
									<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$bodypartid.'|-|'.$dtlsuniquedata; ?>');" />
									<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
									<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
								</td>
							</tr>
							<?
							$i++;
							}
						}
					}
					else if($newadd_type==2)//Color Add
					{
						foreach($podataArr as $poid=>$podata)
						{
							foreach($podata as $colorid=>$pono)
							{
								if($isNullPoArr[$mstid][$gmtsitemid][$poid][$colorid]=="")
								{
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
									<td width="30"><?=$seq; ?></td>
									<td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
									<td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
									<td width="60" style="word-break:break-all"><?=$body_part_type[$bodyparttypeid]; ?></td>
									<td width="100" style="word-break:break-all"><?=$item_category[$fabnatureid]; ?></td>
									<td width="70" style="word-break:break-all"><?=$color_type[$colortypeid]; ?></td>
									<td width="240" style="word-break:break-all"><?=$fabricdescription; ?></td>
									<td width="80" style="word-break:break-all"><?=$fabric_source[$fabricsourceid]; ?></td>
									<td width="40" style="word-break:break-all"><?=$gsmweight; ?></td>
									<td width="50" style="word-break:break-all"><?=$unit_of_measurement[$uom]; ?></td>
									<td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
									<td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
									<td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
									<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
                                    <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$colorArr[$colorid]; ?></td>
									<td align="center">
										<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$colorid.'|-|'.$dtlsuniquedata; ?>');" />
										<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
										<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
									</td>
								</tr>
								<?
								$i++;
								}
							}
						}
					}
					else if($newadd_type==3)//Size Add
					{
						foreach($podataArr as $poid=>$podata)
						{
							foreach($podata as $colorid=>$colordata)
							{
								foreach($colordata as $sizeid=>$pono)
								{
									if($isNullPoArr[$mstid][$gmtsitemid][$poid][$colorid][$sizeid]=="")
									{
									?>
									<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
										<td width="30"><?=$seq; ?></td>
										<td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
										<td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
										<td width="60" style="word-break:break-all"><?=$body_part_type[$bodyparttypeid]; ?></td>
										<td width="100" style="word-break:break-all"><?=$item_category[$fabnatureid]; ?></td>
										<td width="70" style="word-break:break-all"><?=$color_type[$colortypeid]; ?></td>
										<td width="240" style="word-break:break-all"><?=$fabricdescription; ?></td>
										<td width="80" style="word-break:break-all"><?=$fabric_source[$fabricsourceid]; ?></td>
										<td width="40" style="word-break:break-all"><?=$gsmweight; ?></td>
										<td width="50" style="word-break:break-all"><?=$unit_of_measurement[$uom]; ?></td>
										<td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
										<td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
										<td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
										<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
										<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$colorArr[$colorid]; ?></td>
                                        <td width="70" style="word-break:break-all" <?=$tdcolor; ?> ><?=$itemSizeArr[$sizeid]; ?></td>
										<td align="center">
											<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$colorid.'|-|'.$sizeid.'|-|'.$dtlsuniquedata; ?>');" />
											<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
											<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
										</td>
									</tr>
									<?
									$i++;
									}
								}
							}
						}
					}
					
				} ?>
            </tbody>
        </table>
		<?
	}
	else if($bomcost_head==2)//Trims
	{
		$sqlbom="";
	}
	else if($bomcost_head==3)//Embellishment
	{
		$sqlbom="SELECT A.ID as EMBID, A.JOB_NO, A.EMB_NAME, A.EMB_TYPE, A.BODY_PART_ID, A.CONS_DZN_GMTS, A.RATE, A.AMOUNT, B.PO_BREAK_DOWN_ID, B.ITEM_NUMBER_ID, B.COLOR_NUMBER_ID, B.SIZE_NUMBER_ID, B.REQUIRMENT, B.RATE as DTLSRATE, B.AMOUNT as DTLSAMT, B.COLOR_SIZE_TABLE_ID, B.COUNTRY_ID FROM WO_PRE_COST_EMBE_COST_DTLS A, WO_PRE_COS_EMB_CO_AVG_CON_DTLS B WHERE A.ID=B.PRE_COST_EMB_COST_DTLS_ID AND A.EMB_NAME!=3 AND A.JOB_ID='$job_id' AND A.CONS_DZN_GMTS!=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ORDER BY A.ID";
		
		//echo $sqlbom; die;
		$sqlbomArr=sql_select($sqlbom);
		foreach($sqlbomArr as $erow)
		{
			$strembmst=""; $strfabdtls=""; $addedtype="";
			$strembmst=$erow['EMBID'].'_'.$erow['EMB_NAME'].'_'.$erow['EMB_TYPE'].'_'.$erow['BODY_PART_ID'].'_'.$erow['CONS_DZN_GMTS'].'_'.$erow['RATE'].'_'.$erow['AMOUNT'].'_'.$erow['ITEM_NUMBER_ID'];
			//echo $strembmst.'<br>';
			
			if($dtlsDataArr[$strembmst]=="") $dtlsDataArr[$strembmst]=$erow['REQUIRMENT'].'_'.$erow['DTLSRATE'].'_'.$erow['DTLSAMT'].'_'.$erow['COUNTRY_ID'];
			else $dtlsDataArr[$strembmst].='**'.$erow['REQUIRMENT'].'_'.$erow['DTLSRATE'].'_'.$erow['DTLSAMT'].'_'.$erow['COUNTRY_ID'];
			
			if($newadd_type==1)//PO
			{
				$isNullPoArr[$erow['EMBID']][$erow['ITEM_NUMBER_ID']][$erow['PO_BREAK_DOWN_ID']]=$erow['REQUIRMENT'];
			}
			else if($newadd_type==2)//Color
			{
				$isNullPoArr[$erow['EMBID']][$erow['ITEM_NUMBER_ID']][$erow['PO_BREAK_DOWN_ID']][$erow['COLOR_NUMBER_ID']]=$erow['REQUIRMENT'];
			}
			else if($newadd_type==3)//Size
			{
				$isNullPoArr[$erow['EMBID']][$erow['ITEM_NUMBER_ID']][$erow['PO_BREAK_DOWN_ID']][$erow['COLOR_NUMBER_ID']][$erow['SIZE_NUMBER_ID']]=$erow['REQUIRMENT'];
			}
		}
		unset($sqlbomArr);
		/*echo "<pre>";
		print_r($dtlsDataArr); die;*/
		?>
        <table width="900" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
            <thead>
                <tr>
                	<th width="30">Seq</th>
                    <th width="100">Emb. Name</th>
                    <th width="100">Emb. Type</th>
                    <th width="100">Body Part</th>
                    <th width="65">Avg. Cons</th>
                    <th width="40">Rate</th>
                    <th width="60">Amount</th>
                    <th width="100">Gmts Item</th>
                    <?
					if($newadd_type==1) {//PO
						?>
                        <th width="120">PO NO</th>
                        <?
					}
					else if($newadd_type==2) {//Color
						?>
                        <th width="120">PO NO</th>
                        <th width="120">Gmts. Color</th>
                        <?
					}
					else if($newadd_type==3) {//Size
						?>
                        <th width="120">PO NO</th>
                        <th width="120">Gmts. Color</th>
                        <th width="70">Gmts. Size</th>
                        <?
					}
					?>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$i=1;
				foreach ($dtlsDataArr as $mstdata=>$dtlsdata)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $mstdata.'<br>';
					//$erow['EMBID'].'_'.$erow['EMB_NAME'].'_'.$erow['EMB_TYPE'].'_'.$erow['BODY_PART_ID'].'_'.$erow['CONS_DZN_GMTS'].'_'.$erow['RATE'].'_'.$erow['AMOUNT'].'_'.$erow['ITEM_NUMBER_ID'];
					$exmst=explode("_",$mstdata);
					$mstid=$exmst[0];
					$embname=$exmst[1];
					$embtype=$exmst[2];
					$bodypartid=$exmst[3];
					
					$avgcons=$exmst[4];
					$avgrate=$exmst[5];
					$avgamt=$exmst[6];
					$gmtsitemid=$exmst[7];
					
					$exdtls=array_filter(array_unique(explode("**",$dtlsdata)));
					$dtlsuniquedata=implode(",",$exdtls);
					$tdcolor=""; $btnmode="";
					if( count($exdtls)!=1 ) 
					{
						$tdcolor='bgcolor="#FF0000"'; $btnmode="display:none";
					}
					else 
					{
						$tdcolor='bgcolor="#FFFF00"'; $btnmode="";
					}
					$emb_typearr="";
					if($embname==1) $emb_typearr=$emblishment_print_type;
					else if($embname==2) $emb_typearr=$emblishment_embroy_type;
					else if($embname==3) $emb_typearr=$emblishment_wash_type;
					else if($embname==4) $emb_typearr=$emblishment_spwork_type;
					else if($embname==5) $emb_typearr=$emblishment_gmts_type;
					else if($embname==99) $emb_typearr=$emblishment_other_type_arr;
					else $emb_typearr=$blank_array;
					if($newadd_type==1)//Po Add
					{
						foreach($podataArr as $poid=>$pono)
						{
							if($isNullPoArr[$mstid][$gmtsitemid][$poid]=="")
							{
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
								<td width="30"><?=$seq; ?></td>
								<td width="100" style="word-break:break-all"><?=$emblishment_name_array[$embname]; ?></td>
								<td width="100" style="word-break:break-all"><?=$emb_typearr[$embtype]; ?></td>
                                <td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
                                <td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
								<td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
								<td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
                                <td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
								
								<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
								<td align="center">
									<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$bodypartid.'|-|'.$dtlsuniquedata; ?>');" />
									<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
									<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
								</td>
							</tr>
							<?
							$i++;
							}
						}
					}
					else if($newadd_type==2)//Color Add
					{
						foreach($podataArr as $poid=>$podata)
						{
							foreach($podata as $colorid=>$pono)
							{
								if($isNullPoArr[$mstid][$gmtsitemid][$poid][$colorid]=="")
								{
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                	<td width="30"><?=$seq; ?></td>
									<td width="100" style="word-break:break-all"><?=$emblishment_name_array[$embname]; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$emb_typearr[$embtype]; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
                                    <td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
                                    <td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
                                    <td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
                                    
                                    <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
                                    <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$colorArr[$colorid]; ?></td>
									<td align="center">
										<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$colorid.'|-|'.$dtlsuniquedata; ?>');" />
										<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
										<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
									</td>
								</tr>
								<?
								$i++;
								}
							}
						}
					}
					else if($newadd_type==3)//Size Add
					{
						foreach($podataArr as $poid=>$podata)
						{
							foreach($podata as $colorid=>$colordata)
							{
								foreach($colordata as $sizeid=>$pono)
								{
									if($isNullPoArr[$mstid][$gmtsitemid][$poid][$colorid][$sizeid]=="")
									{
									?>
									<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                    	<td width="30"><?=$seq; ?></td>
										<td width="100" style="word-break:break-all"><?=$emblishment_name_array[$embname]; ?></td>
                                        <td width="100" style="word-break:break-all"><?=$emb_typearr[$embtype]; ?></td>
                                        <td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
                                        <td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
                                        <td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
                                        <td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
                                        <td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
                                        
                                        <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
										<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$colorArr[$colorid]; ?></td>
                                        <td width="70" style="word-break:break-all" <?=$tdcolor; ?> ><?=$itemSizeArr[$sizeid]; ?></td>
										<td align="center">
											<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$colorid.'|-|'.$sizeid.'|-|'.$dtlsuniquedata; ?>');" />
											<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
											<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
										</td>
									</tr>
									<?
									$i++;
									}
								}
							}
						}
					}
					
				} ?>
            </tbody>
        </table>
		<?
	}
	else if($bomcost_head==4)//Wash
	{
		$sqlbom="SELECT A.ID as EMBID, A.JOB_NO, A.EMB_NAME, A.EMB_TYPE, A.BODY_PART_ID, A.CONS_DZN_GMTS, A.RATE, A.AMOUNT, B.PO_BREAK_DOWN_ID, B.ITEM_NUMBER_ID, B.COLOR_NUMBER_ID, B.SIZE_NUMBER_ID, B.REQUIRMENT, B.RATE as DTLSRATE, B.AMOUNT as DTLSAMT, B.COLOR_SIZE_TABLE_ID, B.COUNTRY_ID FROM WO_PRE_COST_EMBE_COST_DTLS A, WO_PRE_COS_EMB_CO_AVG_CON_DTLS B WHERE A.ID=B.PRE_COST_EMB_COST_DTLS_ID AND A.EMB_NAME=3 AND A.JOB_ID='$job_id' AND A.CONS_DZN_GMTS!=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ORDER BY A.ID";
		
		//echo $sqlbom; die;
		$sqlbomArr=sql_select($sqlbom);
		foreach($sqlbomArr as $erow)
		{
			$strembmst=""; $strfabdtls=""; $addedtype="";
			$strembmst=$erow['EMBID'].'_'.$erow['EMB_NAME'].'_'.$erow['EMB_TYPE'].'_'.$erow['BODY_PART_ID'].'_'.$erow['CONS_DZN_GMTS'].'_'.$erow['RATE'].'_'.$erow['AMOUNT'].'_'.$erow['ITEM_NUMBER_ID'];
			//echo $strembmst.'<br>';
			
			if($dtlsDataArr[$strembmst]=="") $dtlsDataArr[$strembmst]=$erow['REQUIRMENT'].'_'.$erow['DTLSRATE'].'_'.$erow['DTLSAMT'].'_'.$erow['COUNTRY_ID'];
			else $dtlsDataArr[$strembmst].='**'.$erow['REQUIRMENT'].'_'.$erow['DTLSRATE'].'_'.$erow['DTLSAMT'].'_'.$erow['COUNTRY_ID'];
			
			if($newadd_type==1)//PO
			{
				$isNullPoArr[$erow['EMBID']][$erow['ITEM_NUMBER_ID']][$erow['PO_BREAK_DOWN_ID']]=$erow['REQUIRMENT'];
			}
			else if($newadd_type==2)//Color
			{
				$isNullPoArr[$erow['EMBID']][$erow['ITEM_NUMBER_ID']][$erow['PO_BREAK_DOWN_ID']][$erow['COLOR_NUMBER_ID']]=$erow['REQUIRMENT'];
			}
			else if($newadd_type==3)//Size
			{
				$isNullPoArr[$erow['EMBID']][$erow['ITEM_NUMBER_ID']][$erow['PO_BREAK_DOWN_ID']][$erow['COLOR_NUMBER_ID']][$erow['SIZE_NUMBER_ID']]=$erow['REQUIRMENT'];
			}
		}
		unset($sqlbomArr);
		/*echo "<pre>";
		print_r($dtlsDataArr); die;*/
		?>
        <table width="900" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
            <thead>
                <tr>
                	<th width="30">Seq</th>
                    <th width="100">Emb. Name</th>
                    <th width="100">Emb. Type</th>
                    <th width="100">Body Part</th>
                    <th width="65">Avg. Cons</th>
                    <th width="40">Rate</th>
                    <th width="60">Amount</th>
                    <th width="100">Gmts Item</th>
                    <?
					if($newadd_type==1) {//PO
						?>
                        <th width="120">PO NO</th>
                        <?
					}
					else if($newadd_type==2) {//Color
						?>
                        <th width="120">PO NO</th>
                        <th width="120">Gmts. Color</th>
                        <?
					}
					else if($newadd_type==3) {//Size
						?>
                        <th width="120">PO NO</th>
                        <th width="120">Gmts. Color</th>
                        <th width="70">Gmts. Size</th>
                        <?
					}
					?>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$i=1;
				foreach ($dtlsDataArr as $mstdata=>$dtlsdata)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $mstdata.'<br>';
					//$erow['EMBID'].'_'.$erow['EMB_NAME'].'_'.$erow['EMB_TYPE'].'_'.$erow['BODY_PART_ID'].'_'.$erow['CONS_DZN_GMTS'].'_'.$erow['RATE'].'_'.$erow['AMOUNT'].'_'.$erow['ITEM_NUMBER_ID'];
					$exmst=explode("_",$mstdata);
					$mstid=$exmst[0];
					$embname=$exmst[1];
					$embtype=$exmst[2];
					$bodypartid=$exmst[3];
					
					$avgcons=$exmst[4];
					$avgrate=$exmst[5];
					$avgamt=$exmst[6];
					$gmtsitemid=$exmst[7];
					
					$exdtls=array_filter(array_unique(explode("**",$dtlsdata)));
					$dtlsuniquedata=implode(",",$exdtls);
					$tdcolor=""; $btnmode="";
					if( count($exdtls)!=1 ) 
					{
						$tdcolor='bgcolor="#FF0000"'; $btnmode="display:none";
					}
					else 
					{
						$tdcolor='bgcolor="#FFFF00"'; $btnmode="";
					}
					$emb_typearr="";
					if($embname==1) $emb_typearr=$emblishment_print_type;
					else if($embname==2) $emb_typearr=$emblishment_embroy_type;
					else if($embname==3) $emb_typearr=$emblishment_wash_type;
					else if($embname==4) $emb_typearr=$emblishment_spwork_type;
					else if($embname==5) $emb_typearr=$emblishment_gmts_type;
					else if($embname==99) $emb_typearr=$emblishment_other_type_arr;
					else $emb_typearr=$blank_array;
					if($newadd_type==1)//Po Add
					{
						foreach($podataArr as $poid=>$pono)
						{
							if($isNullPoArr[$mstid][$gmtsitemid][$poid]=="")
							{
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
								<td width="30"><?=$seq; ?></td>
								<td width="100" style="word-break:break-all"><?=$emblishment_name_array[$embname]; ?></td>
								<td width="100" style="word-break:break-all"><?=$emb_typearr[$embtype]; ?></td>
                                <td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
                                <td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
								<td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
								<td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
                                <td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
								
								<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
								<td align="center">
									<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$bodypartid.'|-|'.$dtlsuniquedata; ?>');" />
									<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
									<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
								</td>
							</tr>
							<?
							$i++;
							}
						}
					}
					else if($newadd_type==2)//Color Add
					{
						foreach($podataArr as $poid=>$podata)
						{
							foreach($podata as $colorid=>$pono)
							{
								if($isNullPoArr[$mstid][$gmtsitemid][$poid][$colorid]=="")
								{
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                	<td width="30"><?=$seq; ?></td>
									<td width="100" style="word-break:break-all"><?=$emblishment_name_array[$embname]; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$emb_typearr[$embtype]; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
                                    <td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
                                    <td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
                                    <td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
                                    
                                    <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
                                    <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$colorArr[$colorid]; ?></td>
									<td align="center">
										<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$colorid.'|-|'.$dtlsuniquedata; ?>');" />
										<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
										<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
									</td>
								</tr>
								<?
								$i++;
								}
							}
						}
					}
					else if($newadd_type==3)//Size Add
					{
						foreach($podataArr as $poid=>$podata)
						{
							foreach($podata as $colorid=>$colordata)
							{
								foreach($colordata as $sizeid=>$pono)
								{
									if($isNullPoArr[$mstid][$gmtsitemid][$poid][$colorid][$sizeid]=="")
									{
									?>
									<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                    	<td width="30"><?=$seq; ?></td>
										<td width="100" style="word-break:break-all"><?=$emblishment_name_array[$embname]; ?></td>
                                        <td width="100" style="word-break:break-all"><?=$emb_typearr[$embtype]; ?></td>
                                        <td width="100" style="word-break:break-all"><?=$body_part[$bodypartid]; ?></td>
                                        <td width="65" style="word-break:break-all" align="center"><?=$avgcons; ?></td>
                                        <td width="40" style="word-break:break-all" align="center"><?=$avgrate; ?></td>
                                        <td width="60" style="word-break:break-all" align="center"><?=$avgamt; ?></td>
                                        <td width="100" style="word-break:break-all"><?=$garments_item[$gmtsitemid]; ?></td>
                                        
                                        <td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$pono; ?></td>
										<td width="120" style="word-break:break-all" <?=$tdcolor; ?> ><?=$colorArr[$colorid]; ?></td>
                                        <td width="70" style="word-break:break-all" <?=$tdcolor; ?> ><?=$itemSizeArr[$sizeid]; ?></td>
										<td align="center">
											<input type="button" id="btnfab_<?=$i; ?>" class="formbutton" style="width:100px; <?=$btnmode; ?>" value="Sync. & Reload" onClick="fn_bom_sysnc('<?=$i.'|-|'.$mstid.'|-|'.$poid.'|-|'.$colorid.'|-|'.$sizeid.'|-|'.$dtlsuniquedata; ?>');" />
											<input type="hidden" name="hiddfabricid_<?=$i; ?>" id="hiddfabricid_<?=$i; ?>" value="<?=$mstid; ?>">
											<input type="hidden" name="hiddpoid_<?=$i; ?>" id="hiddpoid_<?=$i; ?>" value="<?=$poid; ?>">
										</td>
									</tr>
									<?
									$i++;
									}
								}
							}
						}
					}
					
				} ?>
            </tbody>
        </table>
		<?
	}
	die;
}

if($action=='save_update_delete_cons_sync'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	//echo "10**".$inc; disconnect($con);die;
	if ($operation==1)
	{
		$hidden_job_id=str_replace("'",'',$hidden_job_id);
		$hidden_costhead_id=str_replace("'",'',$hidden_costhead_id);
		$hidden_newadd_type_id=str_replace("'",'',$hidden_newadd_type_id);
		
		if($hidden_costhead_id==1)//Fabric
		{
			$field_array1="id, job_id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, inserted_by, insert_date, status_active, is_deleted";
			
			$id1=return_next_id("id", "wo_pre_cos_fab_co_avg_con_dtls", 1);
			$datafilterCond="";
			if($hidden_newadd_type_id==1) $datafilterCond="and b.id='$poid'";//PO
			else if($hidden_newadd_type_id==2) $datafilterCond="and b.id='$poid' and c.color_number_id='$colorid'";//Color
			else if($hidden_newadd_type_id==3) $datafilterCond="and b.id='$poid' and c.color_number_id='$colorid' and c.size_number_id='$sizeid'";//Size
			
			$dataSql="select b.id, b.job_no_mst, b.po_number, b.po_quantity, b.shipment_date, min(c.id) as color_size_table_id, min(color_order) as color_order, min(size_order) as size_order, c.color_number_id, c.size_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.job_id='$hidden_job_id' $datafilterCond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 group by b.id, b.job_no_mst, b.po_number, b.po_quantity, b.shipment_date, c.color_number_id, c.size_number_id order by b.id, color_order, size_order";
			//echo "10**".$dataSql; disconnect($con);die;
			$dataSqlRes=sql_select($dataSql);
			$exdtls=explode("_",$consdtlsdata);
			
			$diawidth=$exdtls[0];
			$itemsize=$exdtls[1];
			$cons=$exdtls[2];
			$processloss=$exdtls[3];
			$totcons=$exdtls[4];
			$conspcs=$exdtls[5];
			$dtlsrate=$exdtls[6];
			$dtlsamt=$exdtls[7];
			$dtlsremarks=$exdtls[8];
			$flag=$rID=0;
			if(count($dataSqlRes)>0)
			{
				foreach($dataSqlRes as $row)
				{
					$bodypartid=str_replace("'",'',$bodypartid);
					$data_array1=""; $flag=1;
					//$frow['DIA_WIDTH'].'_'.$frow['ITEM_SIZE'].'_'.$frow['CONS'].'_'.$frow['PROCESS_LOSS_PERCENT'].'_'.$frow['REQUIRMENT'].'_'.$frow['PCS'].'_'.$frow['DTLSRATE'].'_'.$frow['DTLSAMT'].'_'.$frow['REMARKS'];
					
					$data_array1="INSERT INTO wo_pre_cos_fab_co_avg_con_dtls (".$field_array1.") VALUES(".$id1.",'".$hidden_job_id."','".$fabricid."','".$row[csf('job_no_mst')]."','".$row[csf('id')]."','".$row[csf('color_number_id')]."','".$row[csf('size_number_id')]."','".$diawidth."','".$itemsize."','".$cons."','".$processloss."','".$totcons."','".$conspcs."','".$row[csf('color_size_table_id')]."','".($dtlsrate*1)."','".($dtlsamt*1)."','".trim($dtlsremarks)."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
	
					$id1=$id1+1;
					
					$rID=execute_query($data_array1);
					//echo $data_array1;
					if ($rID==1) { $flag=1; }
					else { echo "10**fabcons-".$data_array1; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die; }
				}
			}
		}
		else if($hidden_costhead_id==2)//Trims
		{
			
		}
		else if($hidden_costhead_id==3)//Emb
		{
			$field_array1="id, job_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, color_size_table_id, country_id, inserted_by, insert_date, status_active, is_deleted";
		
			$id1=return_next_id( "id", "wo_pre_cos_emb_co_avg_con_dtls", 1);
			$datafilterCond="";
			if($hidden_newadd_type_id==1) $datafilterCond="and b.id='$poid'";//PO
			else if($hidden_newadd_type_id==2) $datafilterCond="and b.id='$poid' and c.color_number_id='$colorid'";//Color
			else if($hidden_newadd_type_id==3) $datafilterCond="and b.id='$poid' and c.color_number_id='$colorid' and c.size_number_id='$sizeid'";//Size
			
			$dataSql="select b.id, b.job_no_mst, b.po_number, b.po_quantity, b.shipment_date, min(c.id) as color_size_table_id, min(color_order) as color_order, min(size_order) as size_order, c.item_number_id, c.color_number_id, c.size_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.job_id='$hidden_job_id' $datafilterCond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 group by b.id, b.job_no_mst, b.po_number, b.po_quantity, b.shipment_date, c.color_number_id, c.item_number_id, c.size_number_id order by b.id, color_order, size_order";
			//echo "10**".$dataSql; disconnect($con);die;
			$dataSqlRes=sql_select($dataSql);
			$exdtls=explode("_",$consdtlsdata);
			
			$requirment=$exdtls[0];
			$dtlsrate=$exdtls[1];
			$dtlsamt=$exdtls[2];
			$countryid=$exdtls[3];
			$flag=$rID=0;
			if(count($dataSqlRes)>0)
			{
				foreach($dataSqlRes as $row)
				{
					$bodypartid=str_replace("'",'',$bodypartid);
					$data_array1=""; $flag=1;
					//$erow['REQUIRMENT'].'_'.$erow['DTLSRATE'].'_'.$erow['DTLSAMT'].'_'.$erow['COUNTRY_ID']
					
					$data_array1="INSERT INTO wo_pre_cos_emb_co_avg_con_dtls (".$field_array1.") VALUES(".$id1.",'".$hidden_job_id."','".$embid."','".$row[csf('job_no_mst')]."','".$row[csf('id')]."','".$row[csf('item_number_id')]."','".$row[csf('color_number_id')]."','".$row[csf('size_number_id')]."','".$requirment."','".$dtlsrate."','".$dtlsamt."','".$row[csf('color_size_table_id')]."','".$countryid."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
	
					$id1=$id1+1;
					
					$rID=execute_query($data_array1);
					//echo $data_array1;
					if ($rID==1) { $flag=1; }
					else { echo "10**embcons-".$data_array1; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die; }
				}
			}
		}
		else if($hidden_costhead_id==4)//Wash
		{
			$field_array1="id, job_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, color_size_table_id, country_id, inserted_by, insert_date, status_active, is_deleted";
		
			$id1=return_next_id( "id", "wo_pre_cos_emb_co_avg_con_dtls", 1);
			$datafilterCond="";
			if($hidden_newadd_type_id==1) $datafilterCond="and b.id='$poid'";//PO
			else if($hidden_newadd_type_id==2) $datafilterCond="and b.id='$poid' and c.color_number_id='$colorid'";//Color
			else if($hidden_newadd_type_id==3) $datafilterCond="and b.id='$poid' and c.color_number_id='$colorid' and c.size_number_id='$sizeid'";//Size
			
			$dataSql="select b.id, b.job_no_mst, b.po_number, b.po_quantity, b.shipment_date, min(c.id) as color_size_table_id, min(color_order) as color_order, min(size_order) as size_order, c.item_number_id, c.color_number_id, c.size_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.job_id='$hidden_job_id' $datafilterCond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 group by b.id, b.job_no_mst, b.po_number, b.po_quantity, b.shipment_date, c.color_number_id, c.item_number_id, c.size_number_id order by b.id, color_order, size_order";
			//echo "10**".$dataSql; disconnect($con);die;
			$dataSqlRes=sql_select($dataSql);
			$exdtls=explode("_",$consdtlsdata);
			
			$requirment=$exdtls[0];
			$dtlsrate=$exdtls[1];
			$dtlsamt=$exdtls[2];
			$countryid=$exdtls[3];
			$flag=$rID=0;
			if(count($dataSqlRes)>0)
			{
				foreach($dataSqlRes as $row)
				{
					$bodypartid=str_replace("'",'',$bodypartid);
					$data_array1=""; $flag=1;
					//$erow['REQUIRMENT'].'_'.$erow['DTLSRATE'].'_'.$erow['DTLSAMT'].'_'.$erow['COUNTRY_ID']
					
					$data_array1="INSERT INTO wo_pre_cos_emb_co_avg_con_dtls (".$field_array1.") VALUES(".$id1.",'".$hidden_job_id."','".$washid."','".$row[csf('job_no_mst')]."','".$row[csf('id')]."','".$row[csf('item_number_id')]."','".$row[csf('color_number_id')]."','".$row[csf('size_number_id')]."','".$requirment."','".$dtlsrate."','".$dtlsamt."','".$row[csf('color_size_table_id')]."','".$countryid."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
	
					$id1=$id1+1;
					
					$rID=execute_query($data_array1);
					//echo $data_array1;
					if ($rID==1) { $flag=1; }
					else { echo "10**washcons-".$data_array1; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die; }
				}
			}
		}
		
		//echo "10**".$rID.'-'.$flag; oci_rollback($con); disconnect($con);die;
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'",'',$hidden_job_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$hidden_job_id);
			}
		}
		disconnect($con);
		die;
	}
}