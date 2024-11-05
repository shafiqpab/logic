<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| file_no_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="file_popup")
{

  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hide_file_no").val(str);
			parent.emailwindow.hide();
		}
	</script>
	<?

	if($file_year!=0)
	{
		$year_cond_sc="and sc_year='$file_year'";
		$year_cond_lc="and lc_year='$file_year'";
	}
		
	$sql = "SELECT a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year ,a.is_lc_sc from (
	select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, 'export' as type, 1 as is_lc_sc
	from com_export_lc
	where beneficiary_name=$companyID and status_active=1 and is_deleted=0 $year_cond_lc
	group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
	union all
	select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, 'import' as type, 2 as is_lc_sc
	from com_sales_contract		
	where beneficiary_name=$companyID
	and status_active=1 and is_deleted=0 $year_cond_sc
	group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
	) a
	group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year, a.is_lc_sc
	order by a.id desc";
    // echo $sql;
	
	?>
   	<div style="width:160px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="">File NO</td>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sql_results=sql_select($sql);
			$i=1;
			//echo count($sql_results);
			foreach($sql_results as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
				//if($is_lc_sc==$row[csf('is_lc_sc')] && $lc_sc_id==$row[csf('id')]){$bgcolor="#FFFF00";}else{$bgcolor=$bgcolor;};
				?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf('internal_file_no')];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width=""><p><? echo $row[csf("internal_file_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
	<?
	?>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| file_no_list_view
|--------------------------------------------------------------------------
|
*/
if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	//$buyer_id = $ex_data[3];
	//$lien_bank_id = $ex_data[4];
	$cbo_year = $ex_data[3];
	$is_lc_sc = str_replace("'","",$ex_data[4]);
	$lc_sc_id = str_replace("'","",$ex_data[5]);
	//echo $cbo_year; die;
	//if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	//if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else
	{
		$year_cond_sc="";
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'";
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	//if($txt_search_common==0)$txt_search_common="";

    $txt_search_common = trim($txt_search_common);
    $search_cond ="";$search_cond_lc="";$search_cond_sc="";
    if($txt_search_common!="")
    {
        if($cbo_search_by==1)
        {
            $search_cond .= " and internal_file_no like '%$txt_search_common%'";
        }
        else if($cbo_search_by==2)
        {
            $search_cond .= " and buyer_name='$txt_search_common'";
        }
        else if($cbo_search_by==3)
        {
            $search_cond .= " and lien_bank='$txt_search_common'";
        }
        else if($cbo_search_by==4)
        {
            $search_cond_lc .= " and export_lc_no='$txt_search_common'";
            $search_cond_sc .= " and contract_no='$txt_search_common'";
        }
    }
    //echo $cbo_search_by."**".$txt_search_common; die;
    //echo $cbo_search_by."**".$search_cond_lc."**".$search_cond_sc; die;
    if($db_type == 0)
    {
		$sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , group_concat(a.export_lc_no) as export_lc_no,a.is_lc_sc
		from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, group_concat(export_lc_no) as export_lc_no, 'export' as type, 1 as is_lc_sc
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,group_concat(contract_no) as export_lc_no, 'import' as type, 2 as is_lc_sc
		from com_sales_contract
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year,a.is_lc_sc";
    }
    else
    {
    	/*$sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , rtrim(xmlagg(xmlelement(e,a.export_lc_no,',').extract('//text()') order by a.export_lc_no).GetClobVal(),',') AS export_lc_no
		from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, listagg(cast(export_lc_no as varchar(4000)),',') within group(order by export_lc_no) as export_lc_no, 'export' as type
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,listagg(cast(contract_no as varchar(4000)),',') within group(order by contract_no) as export_lc_no, 'import' as type
		from com_sales_contract
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";*/
		
		$sql = "SELECT a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , listagg(cast(a.export_lc_no as varchar2(4000)),',') within group (order by a.export_lc_no) as export_lc_no ,a.is_lc_sc from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year,  listagg(cast(export_lc_no as varchar2(4000)),',') within group (order by export_lc_no) as export_lc_no, 'export' as type, 1 as is_lc_sc
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, listagg(cast(contract_no as varchar2(4000)),',') within group (order by contract_no) as export_lc_no, 'import' as type, 2 as is_lc_sc
		from com_sales_contract		
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year, a.is_lc_sc
		order by a.id desc";
		// echo $sql;
    }
    $lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
	//echo $sql;
	?>
   	<div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sql_results=sql_select($sql);
			$i=1;
			//echo count($sql_results);
			foreach($sql_results as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
				//if($db_type==2) $row[csf('export_lc_no')] = $row[csf('export_lc_no')]->load();
				if($is_lc_sc==$row[csf('is_lc_sc')] && $lc_sc_id==$row[csf('id')]){$bgcolor="#FFFF00";}else{$bgcolor=$bgcolor;};
				?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf('internal_file_no')].'_'.$row[csf('is_lc_sc')].'_'.$row[csf('id')].'_'.$row[csf('export_lc_no')].'_'.$row[csf('lc_sc_year')];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><p><? echo $row[csf("internal_file_no")];  ?></p></td>
                    <td align="center" width="80"><p><? echo $row[csf("lc_sc_year")];  ?></p></td>
                    <td width="130"><p><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></p></td>
                    <td width="100"><p><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></p></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
	<?
}

/*
|--------------------------------------------------------------------------
| pi_search_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "pi_search_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var data = data.split("_");
			var id = data[0];
			var pi_no = data[1];
			var file_no = data[2];
			$('#hidden_pi_id').val(id);
			$('#hidden_pi_no').val(pi_no);
			$('#txt_file_no').val(file_no);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:600px;">
					<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Supplier</th>
						<th>PI No</th>
						<th>Date Range</th>
						<th>
	                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;">
	                        <input type="hidden" name="hidden_pi_no" id="hidden_pi_no" value=""/>
	                        <input type="hidden" name="hidden_pi_id" id="hidden_pi_id" value=""/>
	                        <input type="hidden" name="txt_file_no" id="txt_file_no" value=""/>
	                    </th>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_supplier_name", 140, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- All Supplier--", 0, "", 0);
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Write" >
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px;"/>			
	                           	<input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:55px;"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_supplier_name').value+'**'+document.getElementById('txt_pi_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'bulk_yarn_allocation_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:100px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| create_pi_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_pi_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$supplier_id = $data[1];
	$txt_pi_no = $data[2];
	$date_from = str_replace("'", "", trim($data[3]));
	$date_to = str_replace("'", "", trim($data[4]));

	$pi_number_cond=$pi_date_cond='';
	if (trim($txt_pi_no) !='') $pi_number_cond=" and pi_number like '%".trim($txt_pi_no)."%'";
	if ($date_from != '' && $date_to != '')
	{
		if ($db_type == 0)
		{
			$pi_date_cond = "and pi_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$pi_date_cond = "and pi_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$supplier_id_cond='';
	if($supplier_id !=0) $supplier_id_cond=" and supplier_id=$supplier_id";
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sql= "SELECT a.id, a.pi_number, a.pi_date, a.item_category_id, a.importer_id, a.supplier_id, a.last_shipment_date, a.import_pi, a.export_pi_id, a.internal_file_no, sum(b.quantity) as quantity
	from com_pi_master_details a, com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$company_id and a.item_category_id=1 and a.version=1 and a.import_pi=0 and a.status_active=1 and a.is_deleted=0 $pi_number_cond $pi_date_cond $supplier_id_cond group by a.id, a.pi_number, a.pi_date, a.item_category_id, a.importer_id, a.supplier_id, a.last_shipment_date, a.import_pi, a.export_pi_id, a.internal_file_no order by id desc";
	// echo $sql;die;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="120">Supplier</th>
				<th width="120">PI No</th>
				<th width="80">Date</th>
				<th>Quentity</th>
			</thead>
		</table>
	</div>
	<div style="width:500px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="480" class="rpt_table" id="tbl_list_search">
			<?
			$sql_res=sql_select($sql);
			$i=1;
			foreach($sql_res as $supplierId=>$row )
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				if($row[csf("import_pi")] == 1)
				{
					$supplier_name = $comp[$row[csf("supplier_id")]];
				}else{
					$supplier_name = $supplier[$row[csf("supplier_id")]];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("pi_number")].'_'.$row[csf("internal_file_no")]; ?>');" >
					<td width="30" align="center"><?php echo $i; ?></td>
					<td width="120"><?php echo $supplier_name; ?></td>
					<td width="120" align="center"><?php echo $row[csf("pi_number")]; ?></td>
					<td width="80" align="center"><?php echo change_date_format($row[csf("pi_date")]); ?></td>
					<td align="right"><?php echo $row[csf("quantity")]; ?></td>
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

/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	$hidden_pi_id=trim(str_replace("'","",$hidden_pi_id));

	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');

	if ($txt_file_no!=""){$where_cond.=" and a.internal_file_no='$txt_file_no' ";}
	if ($txt_pi_no!=""){$where_cond.=" and a.pi_number like'%$txt_pi_no%' ";}
	// if ($hidden_pi_id!=""){$where_cond.=" and a.id=$hidden_pi_id";}

	// Main query
	$sql= "SELECT A.ID, A.PI_NUMBER, A.INTERNAL_FILE_NO, A.ITEM_CATEGORY_ID, A.IMPORTER_ID, A.SUPPLIER_ID, A.IMPORT_PI, A.EXPORT_PI_ID, B.ID AS DTLS_ID, B.QUANTITY AS PI_QTY, B.COUNT_NAME, B.COLOR_ID, B.YARN_COMPOSITION_ITEM1, B.YARN_COMPOSITION_PERCENTAGE1, B.YARN_TYPE, C.QUANTITY AS ALLOCATION_QTY, C.BUYER_ID AS ALLOCATED_BUYER, C.FILE_NO AS ALLOCATED_FILE, C.REMARKS
	from com_pi_master_details a, com_pi_item_details b, stock_yarn_allocation c
	where a.id=b.pi_id and a.id=c.pi_id and b.id=c.pi_dtls_id and a.importer_id=$cbo_company_name and a.item_category_id=1 and a.version=1 and a.import_pi=0 and a.status_active=1 and a.is_deleted=0 $where_cond";
	// echo $sql;die;
				
	$width=1070;
	$sql_result=sql_select($sql);
	$data_arr = array(); $allocation_qty_arr = array();
	foreach ($sql_result as $key => $row) 
	{
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['INTERNAL_FILE_NO']=$row[INTERNAL_FILE_NO];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['PI_NUMBER']=$row[PI_NUMBER];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['PI_QTY']=$row[PI_QTY];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['ALLOCATION_QTY']+=$row[ALLOCATION_QTY];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['REMARKS']=$row[REMARKS];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['COUNT_NAME']=$row[COUNT_NAME];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['YARN_TYPE']=$row[YARN_TYPE];
		$data_arr[$row[ID]][$row[COLOR_ID]][$row[YARN_COMPOSITION_ITEM1]][$row[ALLOCATED_FILE]][$row[ALLOCATED_BUYER]]['YARN_COMPOSITION_PERCENTAGE1']=$row[YARN_COMPOSITION_PERCENTAGE1];
		$allocation_qty_arr[$row[ID]]+=$row[ALLOCATION_QTY];
	}

	// Pi wise total qty
	$pi_qty_arr = return_library_array("SELECT a.id, sum(b.quantity) as pi_qty from com_pi_master_details a, com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id=1 and a.version=1 and a.import_pi=0 and a.status_active=1 and a.is_deleted=0 $where_cond group by a.id", 'id', 'pi_qty');
	// print_r($pi_qty_arr);die;

	// Sales Contract Entry -> Internal File No wise buyer
	$sc_file_buyer_arr = return_library_array("SELECT internal_file_no, buyer_name from com_sales_contract where   internal_file_no='$txt_file_no' and beneficiary_name=$cbo_company_name and status_active=1 and is_deleted=0", 'internal_file_no', 'buyer_name');

	ob_start();
	?>

	<table cellspacing="0"  width="<?= $width;?>">
        <tr class="form_caption">
            <td align="center" colspan="11">
                <span style="font-size:18px;">Bulk Yarn Allocation Report</span> <br />
                <? echo $company_arr[$cbo_company_name]; ?>                               
            </td>
        </tr>
    </table>

    <div style="width:<?= $width+20;?>px;">
    <table width="<?= $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">    
    <thead>  
        <tr style="font-size:12px;">
            <th width="100">Buyer</th>
            <th width="100">File No</th>
            <th width="60">PI NO</th>
            <th width="60">Color</th>
            <th width="150">Yarn Description</th>
            <th width="100">Allocate File</th>
            <th width="100">Allocate Buyer</th>
            <th width="80">PI Qty</th>
            <th width="80">Allocate Qty</th>
            <th width="80">Balance Qty </th>	
            <th>Remarks</th>
        </tr>
    </thead>	
    </table>   
    </div>	
    <div style="width:<?= $width+18;?>px; max-height:350px; overflow-y:scroll; clear:both" id="scroll_body">	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" align="left"  id="tbl_list">  
		<?
		// row_span start==============
		foreach ($data_arr as $pi_id => $pi_id_value) 
        {
        	$pi_id_row_span=0;
        	foreach ($pi_id_value as $colorId => $color_id_value) 
        	{
        		$color_row_span=0;
        		foreach ($color_id_value as $yarn_comp => $yarn_comp_value) 
        		{
        			foreach ($yarn_comp_value as $allocated_file => $allocated_file_value) 
        			{
        				foreach ($allocated_file_value as $allocated_buyer => $rows) 
        				{
        					$pi_id_row_span++; $color_row_span++;
        					$file_no_count_row_arr[$rows[INTERNAL_FILE_NO]]++;
        				}
        				$pi_id_rowspan_arr[$pi_id]=$pi_id_row_span;
						$color_rowspan_arr[$pi_id][$colorId]=$color_row_span;
        			}
        		}
        	}
        }
        // print_r($file_no_count_row_arr);
        // row_span End==============

        // Data show start-----------------------------------------------
        $i=1;$tot_pi_qty=0;$tot_balance_qty=0;$tot_allocation_qty=0;
		foreach ($data_arr as $pi_id => $pi_id_value) 
        {
        	$b=1;
        	foreach ($pi_id_value as $colorId => $color_id_value) 
        	{
        		$c=1;
        		foreach ($color_id_value as $yarn_comp => $yarn_comp_value) 
        		{
        			foreach ($yarn_comp_value as $allocated_file => $allocated_file_value) 
        			{
        				foreach ($allocated_file_value as $allocated_buyer => $rows) 
        				{
        					$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
        					$file_row_count = $file_no_count_row_arr[$rows[INTERNAL_FILE_NO]];
        					$pi_id_rowspan=$pi_id_rowspan_arr[$pi_id];
        					$color_rowspan=$color_rowspan_arr[$pi_id][$colorId];

        					$balance_qty=$pi_qty_arr[$pi_id]-$allocation_qty_arr[$pi_id];
        					$yarn_desc=$composition[$yarn_comp].' '.$rows[YARN_COMPOSITION_PERCENTAGE1].'% '.$count_arr[$rows[COUNT_NAME]].' '.$yarn_type[$rows[YARN_TYPE]];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
								<?
								if($batch_row_chk[$rows[INTERNAL_FILE_NO]] == "")
								{									
								?>
				                <td width="100" style="vertical-align: middle;" align="center" rowspan="<?= $file_row_count;?>"><?= $buyer_arr[$sc_file_buyer_arr[$rows[INTERNAL_FILE_NO]]];?></td>
				                <td width="100" style="vertical-align: middle;" align="center" title="<?= $file_row_count;?>" rowspan="<? echo $file_row_count;?>"><p><?= $rows[INTERNAL_FILE_NO];?></p></td>
				                <?
				                $batch_row_chk[$rows[INTERNAL_FILE_NO]] = $rows[INTERNAL_FILE_NO];
				            	}

								if($b==1)
								{
								?>	
				                <td width="60" style="vertical-align: middle;" align="center" title="<? echo $pi_id; ?>" rowspan="<? echo $pi_id_rowspan;?>"><p><?= $rows[PI_NUMBER];?></p></td>	
				                <?
								}
								if($c==1)
								{
								?>
				                <td width="60" style="vertical-align: middle;" align="center" title="<?= $colorId; ?>" rowspan="<?= $color_rowspan;?>"><?= $color_library[$colorId];?></td>
				                <?
								}
								?>
				                <td width="150" style="vertical-align: middle;" align="center"><?= $yarn_desc;?></td>
				                <td width="100" style="vertical-align: middle;" align="center"><?= $allocated_file;?></td>	
				                <td width="100" style="vertical-align: middle;" align="center"><?= $buyer_arr[$allocated_buyer];?></td>
				                <?
								if($b==1)
								{
								?>
				                <td width="80" style="vertical-align: middle;" align="right" rowspan="<?= $pi_id_rowspan;?>"><? $pi_qty=$pi_qty_arr[$pi_id]; echo number_format($pi_qty,2,'.','');
				                $tot_pi_qty+=$pi_qty_arr[$pi_id];

				                ?></td>
				                <?
								}
								?>
				                <td width="80" style="vertical-align: middle;" align="right"><?= number_format($rows[ALLOCATION_QTY],2,'.','');?></td>
				                <?
								if($b==1)
								{
								?>
				                <td width="80" style="vertical-align: middle;" align="right" rowspan="<?= $pi_id_rowspan;?>"><?= number_format($balance_qty,2,'.',''); $tot_balance_qty+=$balance_qty;?></td>
				                <?
								}
								?>	
				                <td style="vertical-align: middle;" align="center"><?= $rows[REMARKS];?></td>
				            </tr>
							<?
							$tot_allocation_qty+=$rows[ALLOCATION_QTY];						    
						    $i++; $b++; $c++;
        				}
        			}
        		}
        	}
        }
        ?>
    </table>

    <!-- foot start -->
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="report_table_footer">
		<tfoot>
			<th width="100"></th>
            <th width="100"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="150"></th>
            <th width="100"></th>
            <th width="100" align="right">Total:</th>
            <th width="80" align="right"><strong><?= number_format($tot_pi_qty,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($tot_allocation_qty,2,'.',''); ?></strong></th>
            <th width="80" align="right"><strong><?= number_format($tot_balance_qty,2,'.',''); ?></strong></th>	
            <th></th>
		</tfoot>
	</table>
	<!-- foot End-->

    </div>
    <!-- Data show End- -->
    <?
	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html####$filename";
	
	exit();	
}

?>
      
 