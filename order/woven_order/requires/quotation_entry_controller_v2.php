<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];

$permission=$_SESSION['page_permission'];

$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_id = $userCredential[0][csf('location_id')];
$company_credential_cond = "";
if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

//echo $location_credential_cond.'TT';
//---------------------------------------------------- Start---------------------------------------------------------------
//Master Table=============================================================================================================
function diff_in_weeks_and_days($from, $to,$outputType="week") {
	$day   = 24 * 3600;
	$from  = strtotime($from);
	$to    = strtotime($to) + $day;
	$diff  = abs($to - $from);
	$weeks = floor($diff / $day / 7);
	$days  = $diff / $day - $weeks * 7;
	if($outputType=="week"){
		$out   = array();
		if ($weeks) $out[] = "$weeks Week" . ($weeks > 1 ? 's' : '');
		if ($days)  $out[] = "$days Day" . ($days > 1 ? 's' : '');
		return implode(', ', $out);
	}else{
		$totdays=$diff / $day ;
		return "$totdays Day" . ($totdays > 1 ? 's' : '');
	}
}

function get_company_config($data){
	//========== user credential start ========
$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id";
$company_id = $userCredential[0][csf('company_id')];
$location_id = $userCredential[0][csf('location_id')];
$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
	//echo $location_id.'DDD';
}

	global $buyer_cond;
	//echo $location_credential_cond ."=select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name";
	$cbo_location_id= create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-Select Location-", $selected, "" );

	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "get_buyer_config(this.value);" );

	$cbo_agent= create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	echo "document.getElementById('location_td').innerHTML = '".$cbo_location_id."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	

	$cm_cost_method=0; $set_smv_id=0;
	$sql_variable=sql_select("select variable_list,cm_cost_compulsory, cm_cost_method_quata, publish_shipment_date, style_from_library from variable_order_tracking where company_name='$data' and variable_list in (36,47) and status_active=1 and is_deleted=0");
	$cm_cost_compulsory=0;
	foreach($sql_variable as $result)
	{
		if($result[csf('variable_list')]==36 && $result[csf('cm_cost_method_quata')]!='')
		{ 
		 $cm_cost_method=$result[csf('cm_cost_method_quata')];
		 $cm_cost_compulsory=$result[csf('cm_cost_compulsory')];
		}
		else if($result[csf('variable_list')]==47){
            if($result[csf('publish_shipment_date')]!=''){
                $set_smv_id=$result[csf('publish_shipment_date')];
            }
            if($result[csf('style_from_library')]!=''){
               $style_from_library=$result[csf('style_from_library')];
            }
        }
	}
	echo "document.getElementById('cm_cost_editable').value = '".$cm_cost_compulsory."';\n";
	echo "cm_cost_predefined_method($data,$cm_cost_method);\n";
	echo "fnc_smv_integration($data,$set_smv_id);\n";
	echo "style_from_library($style_from_library);\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");
	//if($print_report_format!='') $print_report_format=$print_report_format;else $print_report_format=0;
	echo "print_report_button_setting('$print_report_format');\n";
}

function set_conversion_rate_quatation( $cid, $cdate,$company )
{
	global $db_type;

	if($company=='' || $company==0) $companyCond="";else $companyCond="and company_id=$company";
	if($cdate=='')
	{
		if( $db_type==0) $cdate=date("Y-m-d",time()); else $cdate=date("d-M-y",time());
	}
	if($cid==1)
	{
		return "1";
	}
	else
	{
		if($db_type==0) $cdate=change_date_format($cdate, "yyyy-mm-dd", "-",1);
		else $cdate=change_date_format($cdate, "d-M-y", "-",1);
		//echo $cdate;die;
		$queryText="select marketing_rate as conversion_rate from currency_conversion_rate where con_date<='".$cdate."' and currency=$cid and status_active=1 and is_deleted=0 $companyCond  order by con_date desc";
		//echo $queryText; die;
		$nameArray=sql_select( $queryText, '',$new_conn );
		if(count($nameArray)>0)
		{
			foreach ($nameArray as $result)
				if ($result[csf('conversion_rate')]!="") return  $result[csf("conversion_rate")]; else return "0";
		}
		else
			return "0";
	}
}

if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 130, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
	exit();
}
if($action=="style_ref_popup")
{
	echo load_html_head_contents("Style Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			document.getElementById('txt_all_data').value=data;
 			var data=data.split('***');
			document.getElementById('txt_style_ref').value=data[1];
			document.getElementById('txt_style_ref_id').value=data[0];
			
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		function check_quatation(data){
			var r=confirm("If you want to continue press Ok, otherwise press Cancel");
			if(r==false)
			{
				$('#txt_style_ref').val('');
				$('#txt_style_ref_id').val('');
				parent.emailwindow.hide();
				return;
			}
			else
			{
				js_set_value(data);
			}
		}
		function quotation_insert(data)
		{
			var data_org=data;
			var data=data.split('***');
			var txt_style_ref=data[1];
			var txt_quotation_id=data[0];
			var quotation_data=return_global_ajax_value( txt_style_ref+"**"+txt_quotation_id, 'check_style_ref', '', 'quotation_entry_controller_v2');
			var quotation_data=trim(quotation_data) ;
			if(quotation_data)
			{
				$("tbody#quotation_date").html('');
				$("tbody#quotation_date").append(quotation_data);
			}
			else
			{
				alert("No Quotation Found Aganist This Style");
				js_set_value(data_org);
			}
		}
</script>
    </head>
   <body>
		<div align="center">
			    <form>
			        <input type="hidden" id="txt_style_ref" name="txt_style_ref" />
			        <input type="hidden" id="txt_style_ref_id" name="txt_style_ref_id" />
			        <input type="hidden" id="txt_all_data" name="txt_all_data" />

			    </form>

				<?
		 		$sql="SELECT id, style_ref_name,buyer_id,buyer_brand_id,product_department_id,division,order_uom,gmts_item_id,department_id,design_type,short_name,level_type_id,style_no from  lib_style_ref  where status_active=1 and is_deleted=0 and buyer_id='$buyer_name' order by style_ref_name ASC";
				$data_array=sql_select($sql);
				$department_arr=return_library_array( "select id,department_name from lib_department_name where status_active=1 and is_deleted=0", "id", "department_name"  );	
				$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1", "id", "buyer_name"  );	
				$division_name_arr=return_library_array( "select id, division_name from lib_division_name where  status_active=1 and is_deleted=0", "id", "division_name"  );	
				$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand where  status_active=1 and is_deleted=0", "id", "brand_name"  );	
				$level_type_arr=return_library_array( "select level_type,id from lib_complexity_level where status_active=1", "id", "level_type"  );	
				$design_type=array(1=>"Custom Design",2=>"In House");

				?>
				<table>
					<tr>
						<td>
							<table class="rpt_table" width="895" cellspacing="0" cellpadding="0" border="0" rules="all">
								<thead>
									<tr>
										<th width="40">SI</th>
										<th width="130">Style Ref.</th>
										<th width="110">Style Id</th>
										<th width="100">Buyer</th>
										<th width="100">Brand</th>
										<th width="90">Division</th>
										<th width="85">Department</th>
										<th width="80">Fashion/Order Type</th>
										<th width="80">Design Type</th>
										<th width="80">Style Nick Name</th>
									</tr>
								</thead>
			                </table>
			                <table class="rpt_table" width="895" cellspacing="0" cellpadding="0" border="0" rules="all"  id="style_ref_data">
								<tbody>
									<?
									$i=1;
									foreach($data_array as $row)
									{
										if ($i%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";

											$data_string=$row[csf('id')]."***".$row[csf('style_ref_name')]."***".$row[csf('order_uom')]."***".$row[csf('product_department_id')]."***".$row[csf('gmts_item_id')]."***".$row[csf('buyer_brand_id')]."***".$row[csf('division')]."***".$row[csf('department_id')]."***".$row[csf('level_type_id')]."***".$row[csf('design_type')]."***".$row[csf('short_name')]."***".$row[csf('style_no')];
											?>
											<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="quotation_insert('<? echo $data_string; ?>')">
												<td width="40"><? echo $i; ?></td>
												<td width="130" align="left"><? echo $row[csf('style_ref_name')]; ?></td>
												<td width="110" align="left"><? echo $row[csf('style_no')]; ?></td>
												<td width="100" align="left"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>
												<td width="100" align="left"><? echo $brand_name_arr[$row[csf('buyer_brand_id')]]; ?></td>
												<td width="90" align="left"><? echo $division_name_arr[$row[csf('division')]]; ?></td>
												<td width="85" align="left"><? echo $department_arr[$row[csf('department_id')]]; ?></td>
												<td width="80" align="left"><? echo $level_type_arr[$row[csf('level_type_id')]]; ?></td>
												<td width="80" align="left"><? echo $design_type[$row[csf('design_type')]]; ?></td>
												<td width="80" align="left"><? echo $row[csf('short_name')]; ?></td>

											</tr>

										<?
										$i++;
									}
										?>
								</tbody>
							</table>
						</td>
						<td style="margin-top: 0px;float: left;">
							<table class="rpt_table" id="quotation_table" width="180" cellspacing="0" cellpadding="0" border="0" rules="all"  align="left">
								<thead>
									<tr>
										<th width="40">Quotation Id</th>
										<th width="140">Date</th>
									</tr>
								</thead>
								<tbody id="quotation_date">
								</tbody>
							</table>
						</td>
					</tr>
				</table>
				
				
				<script>
				setFilterGrid("list_view",-1);
                setFilterGrid('style_ref_data',-1)
				</script>

		</div>

	</body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

function cm_cost_predefined_method ($data)
{
	$cm_cost_method=return_field_value("cm_cost_method_quata", "variable_order_tracking", "company_name=$data  and variable_list=36 and status_active=1 and is_deleted=0");
	if($cm_cost_method=="")
	{
		$cm_cost_method=0;
	}
	return $cm_cost_method;
	//die;
}

if($action=="get_company_config"){
	$action($data);
}

if($action=="check_style_ref")
{
	list($name,$id)=explode("**",$data);

	$cond="";
	if($id!="") $cond= " and id !=$id"; else $cond="";
	$idArr=array();
	$sql=sql_select("select id,insert_date,style_ref,lib_style_id from wo_price_quotation where  style_ref='$name'  and lib_style_id='$id' and status_active=1 and is_deleted=0 order by id");
	foreach($sql as $row)
	{
		?>
		<tr onClick="check_quatation('<? echo $row[csf('lib_style_id')]."***".$row[csf('style_ref')]; ?>')" bgcolor="#FFFFFF" height="20" style="cursor:pointer;">
			<td width="40"><? echo $row[csf("id")];?></td>
			<td width="140"><? echo change_date_format($row[csf("insert_date")]);?></td>

		</tr>


		<?

	}

	exit();
}

/*if($action=="check_style_ref")
{
	$data=explode("**",$data);
	$style_ref=trim($data[0]);
	$id=trim($data[1]);
	$cond="";
	if($id!="") $cond= " and id !=$id"; else $cond="";
	$idArr=array();
	$sql=sql_select("select id from wo_price_quotation where  style_ref='$style_ref' $cond and status_active=1 and is_deleted=0 order by id");
	foreach($sql as $row){
		$idArr[$row[csf('id')]]=$row[csf('id')];
	}
	if(count($idArr)) echo "1**".implode(",",$idArr); else echo "0**";
	exit();
}*/

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/quotation_entry_controller_v2',this.value, 'load_drop_down_buyer_brand', 'brand_td' );load_drop_down( 'requires/quotation_entry_controller_v2',this.value, 'load_drop_down_buyer_division', 'division_td' );" );
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id, a.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}
if($action=="load_drop_down_buyer_brand")
{
	$sql= "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active=1 and is_deleted=0 order by brand_name";
	//echo $sql;die;
	echo create_drop_down( "cbo_buyer_brand_id", 130,$sql,"id,brand_name", 1, "-- Select Brand --", $selected, "",1 );   
}
if($action=="load_drop_down_buyer_division")
{
	$sql= "select id, division_name from lib_division_name where buyer_id='$data' and status_active=1 and is_deleted=0 order by division_name";
	//echo $sql;die;
	echo create_drop_down( "txt_division", 130,$sql,"id,division_name", 1, "-- Select Division --", $selected, "load_drop_down( 'requires/quotation_entry_controller_v2',this.value, 'load_drop_down_division_department', 'department_td' );",1 );   
}
if($action=="load_drop_down_division_department")
{
	$sql= "select id, department_name from lib_department_name where division_id='$data' and status_active=1 and is_deleted=0 order by department_name";
	//echo $sql;die;
	echo create_drop_down( "cbo_department_id", 130,$sql,"id,department_name", 1, "-- Select Department --", $selected, "",1 );   
}


if ($action=="cm_cost_predefined_method")
{
	$cm_cost_method=return_field_value("cm_cost_method_quata", "variable_order_tracking", "company_name=$data  and variable_list=36 and status_active=1 and is_deleted=0");
	if($cm_cost_method=="")
	{
		$cm_cost_method=0;
	}
	echo $cm_cost_method;
	die;
}

if($action=="check_exchange_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "yyyy-mm-dd", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate_quatation( $data[0], $conversion_date,$data[2] );
	echo "1"."_".$currency_rate;
	exit();
}

if ($action=="asking_profit_percent")
{
	$data=explode("_",$data);
	if($data[1]=="" || $data[1]==0)
	{
		if($db_type==0) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");
		else if($db_type==2) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
	}
	else
	{
		if($db_type==0) $txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-");
		else if($db_type==2) $txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
	}

	$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$data[0]  and '$txt_quotation_date' between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0");
	if($asking_profit=="") $asking_profit=0; echo $asking_profit;
	die;
}

if ($action=="cost_per_minute")
{
	$data=explode("_",$data);

	$location_cpm_cost=0;
	$sql_data=sql_select("select yarn_iss_with_serv_app from variable_order_tracking where company_name='$data[0]' and variable_list=67 and status_active=1 and is_deleted=0");
	foreach($sql_data as $sql_row)
	{
		$location_cpm_cost=$sql_row[csf('yarn_iss_with_serv_app')];
	}

	if($data[1]=="" || $data[1]==0)
	{
		if($db_type==0) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-"); else if($db_type==2) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
	}
	else
	{
		if($db_type==0) $txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-"); else if($db_type==2)$txt_quotation_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
	}

	$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0; $depreciation_amorti=0; $operating_expn=0; $interest_expense=0; $income_tax=0;

	// MySql
	 /*$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id=$data  and status_active=1 and is_deleted=0 LIMIT 1";*/
	if($db_type==0) $limit_cond="LIMIT 1"; else if($db_type==2) $limit_cond="";
	if($location_cpm_cost==1 && $data[2]!=0)//Location Wse variable Yes
	{
		$sql="select a.depreciation_amorti, a.operating_expn, a.interest_expense, a.income_tax, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a,lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.company_id=$data[0] and b.location_id='$data[2]' and '$txt_quotation_date' between b.applying_period_date and b.applying_period_to_date  and b.status_active=1 and b.is_deleted=0 $limit_cond";
	}
	else
	{
		$sql="select  depreciation_amorti, operating_expn, interest_expense, income_tax, monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute from lib_standard_cm_entry where company_id=$data[0] and '$txt_quotation_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit_cond";
	}
	//echo $sql;
	//$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id=$data[0] and '$txt_quotation_date' between applying_period_date and applying_period_to_date   and status_active=1 and is_deleted=0";

	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
		if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
		if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
		if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
		if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
		if($row[csf("operating_expn")] !="") $operating_expn=$row[csf("operating_expn")];
		if($row[csf("interest_expense")] !="") $interest_expense=$row[csf("interest_expense")];
		if($row[csf("income_tax")] !="") $income_tax=$row[csf("income_tax")];
	}
	$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn."_".$interest_expense."_".$income_tax;
	echo $data;
	exit();
}

if($action=="txt_commission_pre_cost")
{
	$total_amount=0;
	//$data=explode("_",$data);
	$sql="select sum(commission_amount) as commission_amount from wo_pri_quo_commiss_cost_dtls where quotation_id=$data and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
	  $total_amount	=$row[csf("commission_amount")];
	}
	echo $total_amount;
	exit();
}

if($action=="cofirm_price_commision")
{
	$total_amount=0;
	$data=explode("_",$data);
	$sql="select commission_base_id,commision_rate from wo_pri_quo_commiss_cost_dtls where quotation_id=$data[0] and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$costing_per=0;
	if($data[1]==1) $costing_per=12;
	else if($data[1]==2) $costing_per=1;
	else if($data[1]==3) $costing_per=24;
	else if($data[1]==4) $costing_per=36;
	else if($data[1]==5) $costing_per=48;
	foreach($data_array as $row)
	{
		$amount=0;
		if($row[csf("commission_base_id")]==1)
		{
			 $txtcommissionrate_percent=$row[csf("commision_rate")]/100;
			 $amount=($data[2]/(1-$txtcommissionrate_percent))-$data[2];
			 $total_amount+=$amount;
		}
		if($row[csf("commission_base_id")]==2)
		{
			$amount=$row[csf("commision_rate")]*$costing_per;
			$total_amount+=$amount;
		}
		if($row[csf("commission_base_id")]==3)
		{
			if($data[1]==1) $amount=$row[csf("commision_rate")]*1*1;
			if($data[1]==2) $amount=$row[csf("commision_rate")]/12;
			if($data[1]==3) $amount=$row[csf("commision_rate")]*1*2;
			if($data[1]==4) $amount=$row[csf("commision_rate")]*1*3;
			if($data[1]==5) $amount=$row[csf("commision_rate")]*1*4;
			$total_amount+=$amount;
		}
	}
	echo $total_amount;
	exit();
}

if($action=="detail_part_save_check")
{
	//$data=explode("_",$data);
	$update_id=$data;
	$sql_fab=sql_select("select quotation_id,sum(avg_cons) as avg_cons from wo_pri_quo_fabric_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
	$fabQtyPri=0;
	foreach($sql_fab as $row_fab){
		$fabQtyPri=$row_fab[csf('avg_cons')];
	}

	$sql_yar=sql_select("select quotation_id,sum(cons_qnty) as cons_qnty from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$update_id group by quotation_id");
	$yarQtyPri=0;
	foreach($sql_yar as $row_yarn){
		$yarQtyPri=$row_yarn[csf('cons_qnty')];
	}
	$sql_tri=sql_select("select quotation_id,sum(cons_dzn_gmts) as cons_dzn_gmts from wo_pri_quo_trim_cost_dtls where quotation_id=$update_id group by quotation_id");
	$triQtyPri=0;
	foreach($sql_tri as $row_tri){
		$triQtyPri=$row_tri[csf('cons_dzn_gmts')];
	}
	$sql_emb=sql_select("select quotation_id,sum(cons_dzn_gmts) as cons_dzn_gmts from wo_pri_quo_embe_cost_dtls where quotation_id=$update_id and emb_name!=3 group by quotation_id");
	$embQtyPri=0;
	foreach($sql_emb as $row_emb){
		$embQtyPri=$row_emb[csf('cons_dzn_gmts')];
	}
	$sql_was=sql_select("select quotation_id,sum(cons_dzn_gmts) as cons_dzn_gmts from wo_pri_quo_embe_cost_dtls where quotation_id=$update_id and emb_name =3 group by quotation_id");
	$wasQtyPri=0;
	foreach($sql_was as $row_was){
		$wasQtyPri=$row_was[csf('cons_dzn_gmts')];
	}
	$sql_comm=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_comarcial_cost_dtls where quotation_id=$update_id  group by quotation_id");
	$commQtyPri=0;
	foreach($sql_comm as $row_comm){
		$commQtyPri=$row_comm[csf('amount')];
	}
	if($fabQtyPri==0 || $yarQtyPri==0 || $triQtyPri==0 || $embQtyPri==0 || $wasQtyPri==0 || $commQtyPri==0)
	{
		echo "DetailPartNotEntryFound"."**".$fabQtyPri."**".$yarQtyPri."**".$triQtyPri."**".$embQtyPri."**".$wasQtyPri."**".$commQtyPri;
		die;
	}
	exit();
}

if($action=="lead_time_calculate")
{
	$data=explode("_",$data);
	$txt_est_ship_date=gmdate('Y-m-d',strtotime( $data[0]));
	$txt_op_date=gmdate('Y-m-d',strtotime( $data[1]));

	echo diff_in_weeks_and_days($txt_est_ship_date, $txt_op_date,'week');
}

if ($action=="quotation_id_popup")
{
	//inquery_id_popup
  	echo load_html_head_contents("Quotation Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="1100" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="9" align="center"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="130">Buyer Name</th>
                            <th width="100">Quotation ID</th>
                            <th width="100">Inquiry ID</th>
                            <th width="100">Buyer Request</th>
                            <th width="100">Style Reff.</th>
                            <th width="100">Approval Type</th>
                            <th width="180">Quot. Date Range</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><input type="hidden" id="selected_id">
                            <? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3)  $company_credential_cond order by company_name","id,company_name",1, "-- Select Company --",$company_id,"load_drop_down( 'quotation_entry_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                        <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
                        <td><input type="text" style="width:70px" class="text_boxes"  name="txt_inquery_no" id="txt_inquery_no" /></td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_buyer_request" id="txt_buyer_request" /></td>
                        <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                        <td>
                        	<?
                        	echo create_drop_down( "cbo_approval_type", 100, $approval_type_arr,"", 0, "", $selected,"","", "" );
                        	?>
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_inquery_no').value+'_'+document.getElementById('txt_buyer_request').value+'_'+document.getElementById('cbo_approval_type').value, 'create_quotation_id_list_view', 'search_div', 'quotation_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                    </tr>
                    <tr class="general">
                        <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="search_div"></div>
    </body>

    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
      <script type="text/javascript">
    	$("#cbo_approval_type").val(0);

		var company_id= <? echo $company_id; ?>;
		if(company_id !=0){
			load_drop_down( 'quotation_entry_controller_v2', company_id, 'load_drop_down_buyer', 'buyer_td' );
		}
     </script>
    </html>
    <?
    exit();
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
 	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0){
		$buyer=" and a.buyer_id='$data[1]'";
	}
	else
	{
	    $buyer=""; $bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_id in ($bu_arr_str)";
	}// { echo "Please Select Buyer First."; die; }
	$quotation_id_cond=""; $style_cond=""; $inquery_cond=""; $buyer_request_cond="";
	if($data[4]==1)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id='$data[5]'";
		if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
		if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num='$data[7]'";
		if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request='$data[8]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
		if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num like '%$data[7]%' ";
		if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request like '%$data[8]%' ";
	}
	else if($data[4]==2)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
		if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num like '$data[7]%' ";
		if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request like '$data[8]%' ";
	}
	else if($data[4]==3)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' ";
		if (trim($data[7])!="") $inquery_cond=" and b.system_number_prefix_num like '%$data[7]' ";
		if (trim($data[8])!="") $buyer_request_cond=" and b.buyer_request like '%$data[8]' ";
	}

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.quot_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.quot_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}
	if($data[9]==0)
	{
		$approval_cond=" and a.approved in(0,2)";
	}
	else
	{
		$approval_cond=" and a.approved in(1,3)";
	}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,7=>$pord_dept);
	 $sql= "select a.id,a.company_id, a.buyer_id,b.system_number_prefix_num,b.buyer_request, a.style_ref,a.style_desc,a.pord_dept,a.offer_qnty,a.est_ship_date from  wo_price_quotation a left join wo_quotation_inquery b on a.inquery_id=b.id and b.status_active=1  and b.is_deleted=0 where a.status_active=1 and a.garments_nature=2 and a.is_deleted=0  $company $buyer $est_ship_date $quotation_id_cond $style_cond $inquery_cond $buyer_request_cond  $approval_cond order by id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Inquiry ID,Buyer Req.,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "80,120,100,70,80,100,140,100,80","1050","290",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,system_number_prefix_num,buyer_request,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,0,0,2,3') ;
	exit();
}

if ($action=="inquery_id_popup")
{
	//inquery_id_popup
  	echo load_html_head_contents("Inquiry Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

?>

	<script>
	function js_set_value( quotation_id )
	{
		document.getElementById('selected_id').value=quotation_id;
		parent.emailwindow.hide();
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                        <thead>
                        	<th  colspan="7">
                              <?

							  //  $string_search_type=array(1=>"Exact",2=>"Starts with",3=>"Ends with",4=>"Contents");
                               echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>

                            </th>

                        </thead>
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Inquiry ID</th>
                        <th width="100">Style Reff.</th>
                        <th width="80">Season</th>
                        <th width="200">Shipment Date Range</th><th></th>
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_id">
							<?
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'quotation_entry_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                   	<td id="buyer_td">
                     <?
						echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                     <td width="80">
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_no" id="txt_inquery_no"  />
                    </td>
                    <td width="100" align="center">
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style"  />
                    </td>
                     <td width="80">
								<input type="text" style="width:80px" class="text_boxes"  name="txt_season" id="txt_season"  />
                            </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_inquery_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_season').value, 'create_inquery_id_list_view', 'search_div', 'quotation_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div">

            </td>
        </tr>
    </table>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_inquery_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="" ;
	if (trim($data[7])!="") $season_cond=" and season='$data[7]'"; else $season_cond="" ;
	if($data[6]==1)
		{
		   if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num='$data[4]'"; else $style_id_cond="";
		   if (trim($data[5])!="") $style_cond=" and style_refernce='$data[5]'"; else $style_cond="";
		}

	if($data[6]==4 || $data[6]==0)
		{
		  if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$data[4]%' "; else $style_id_cond="";
		  if (trim($data[5])!="") $style_cond=" and style_refernce like '%$data[5]%' "; else $style_cond="";
		}

	if($data[6]==2)
		{
		  if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$data[4]%' "; else $style_id_cond="";
		  if (trim($data[5])!="") $style_cond=" and style_refernce like '$data[5]%' "; else $style_cond="";
		}

	if($data[6]==3)
		{
		  if (trim($data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$data[4]' "; else $style_id_cond="";
		  if (trim($data[5])!="") $style_cond=" and style_refernce like '%$data[5]' "; else $style_cond="";
		}


	if($db_type==0)
	{
	  if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and inquery_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	if($db_type==2)
		{
		  if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and inquery_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
		}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select id,system_number_prefix_num,company_id, buyer_id,season_buyer_wise,inquery_date,buyer_request,style_refernce from  wo_quotation_inquery where status_active=1  and is_deleted=0 $company $buyer $est_ship_date $inquery_id_cond $style_cond $season_cond and id  not in (select inquery_id from wo_price_quotation where company_id=1 and status_active=1 and is_deleted=0 and inquery_id!=0)
	order by id";
	//echo $sql;
	echo  create_list_view("list_view", "Inquiry ID,Company,Buyer Name,Style Ref,Season,Inquiry Date, Buyer Request", "70,120,100,130,100,100","800","280",0, $sql , "js_set_value", "id,company_id,buyer_id,style_refernce,system_number_prefix_num,Season", "", 1, "0,company_id,buyer_id,0,0,0,0", $arr , "system_number_prefix_num,company_id,buyer_id,style_refernce,season_buyer_wise,inquery_date,buyer_request", "",'','0,0,0,0,0,3,0') ;
	exit();
}

if ($action=="populate_sampledevelopment_data_taged_with_inquery")
{
	//echo "select a.id,a.buyer_name,a.style_ref_no,a.season,a.item_name,a.product_dept,a.region,a.estimated_shipdate,b.sample_name,b.sent_to_factory_date,b.factory_dead_line,b.sent_to_buyer_date,b.fabrication from sample_development_mst a,sample_development_dtls b where  a.quotation_id='$data' and  a.id=b.sample_mst_id";
	//echo "select a.id,a.buyer_name,a.style_ref_no,a.season_buyer_wise,a.item_name,a.product_dept,a.region,a.estimated_shipdate,b.est_ship_date,b.season_buyer_wise as qu_season_buyer_wise,b.offer_qty from sample_development_mst a right join wo_quotation_inquery b on a.quotation_id=b.id where  a.quotation_id='$data'";
		 $sql=sql_select("select b.id,b.buyer_name,b.style_ref_no,b.season_buyer_wise,b.item_name,b.product_dept,b.region,b.estimated_shipdate
,a.est_ship_date,a.season_buyer_wise as qu_season_buyer_wise,a.offer_qty,a.company_id, a.buyer_id from wo_quotation_inquery
 a left join sample_development_mst b on b.quotation_id=a.id where a.id='$data'");
	foreach ($sql as $row)
	{
		$season_id=$row[csf("season_buyer_wise")];
		if($season_id=="" || $season_id==0){
			$season_id=$row[csf("qu_season_buyer_wise")];
		}
		$estimated_shipdate=change_date_format($row[csf("estimated_shipdate")],'dd-mm-yyyy','-');
		$est_ship_date=change_date_format($row[csf("est_ship_date")],'dd-mm-yyyy','-');
		if($estimated_shipdate=="" || $estimated_shipdate==0 || $estimated_shipdate=='00-00-0000'){
			$estimated_shipdate=$est_ship_date;
		}
		echo "load_drop_down( 'requires/quotation_entry_controller_v2','".$row[csf("buyer_id")].'_'.$row[csf("company_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";

		echo "document.getElementById('cbo_pord_dept').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('txt_est_ship_date').value = '".$estimated_shipdate."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$season_id."';\n";
		echo "document.getElementById('txt_offer_qnty').value = '".$row[csf("offer_qty")]."';\n";
	}
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$cbo_approved_status="";
	/*echo "select id, inquery_id, company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty ,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season_buyer_wise,ready_to_approved, approved,m_list_no,bh_marchant,inserted_by, insert_date, status_active, is_deleted from wo_price_quotation where id='$data'";*/
	$data_array=sql_select("select id, inquery_id, company_id, location_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season_buyer_wise, dealing_merchant, ready_to_approved, approved, m_list_no, bh_marchant, inserted_by, insert_date, status_active, is_deleted, mkt_no,lib_style_id,buyer_brand_id from wo_price_quotation where id='$data'");
	foreach ($data_array as $row)
	{
		//echo "load_drop_down( 'requires/quotation_entry_controller_v2', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_controller_v2', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );cm_cost_predefined_method('".$row[csf("company_id")]."') ;\n";
		$company_id=$row[csf("company_id")];
		get_company_config($row[csf("company_id")]);

		//$cost_per_minute=cost_per_minute($row[csf("company_id")]."_".$row[csf("quot_date")]."_".$row[csf("location_id")]);
		//echo "document.getElementById('cost_per_minute').value = '".$cost_per_minute."';\n";

		//echo "show_hide_button('".$row[csf(order_uom)]."')\n";
		echo "change_caption_cost_dtls('".$row[csf('costing_per')]."','change_caption_dzn')\n";
		echo "change_caption_cost_dtls('".$row[csf('order_uom')]."','change_caption_pcs')\n";
		echo "is_manual_approved('".$row[csf("company_id")]."')\n";
       	$inquery_id_prifix=return_field_value("system_number_prefix_num", "wo_quotation_inquery", "id=".$row[csf("inquery_id")]."");
		echo "document.getElementById('txt_inquery_prifix').value = '".$inquery_id_prifix."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_inquery_id').value = '".$row[csf("inquery_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_id').value = '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_buyer_brand_id').value = '".$row[csf("buyer_brand_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('txt_revised_no').value = '".$row[csf("revised_no")]."';\n";
		echo "document.getElementById('cbo_pord_dept').value = '".$row[csf("pord_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";
		echo "document.getElementById('txt_offer_qnty').value = '".$row[csf("offer_qnty")]."';\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('cbo_color_range').value = '".$row[csf("color_range")]."';\n";
		echo "document.getElementById('cbo_inco_term').value = '".$row[csf("incoterm")]."';\n";
		echo "document.getElementById('txt_incoterm_place').value = '".$row[csf("incoterm_place")]."';\n";
		echo "document.getElementById('txt_machine_line').value = '".$row[csf("machine_line")]."';\n";
		echo "document.getElementById('txt_prod_line_hr').value = '".$row[csf("prod_line_hr")]."';\n";
		echo "document.getElementById('cbo_costing_per').value = '".$row[csf("costing_per")]."';\n";
		echo "document.getElementById('txt_quotation_date').value = '".change_date_format($row[csf("quot_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_op_date').value = '".change_date_format($row[csf("op_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_factory').value = '".$row[csf("factory")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_mkt_no').value = '".$row[csf("mkt_no")]."';\n";
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		if($row[csf("approved")] == 3){
			$cbo_approved_status = 1;
		}else{
			$cbo_approved_status = $row[csf("approved")];
		}
		echo "document.getElementById('cbo_approved_status').value = '".$cbo_approved_status."';\n";
		//echo "document.getElementById('cm_cost_predefined_method_id').value = '".$row[csf("cm_cost_predefined_method_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("sew_smv")]."';\n";
		echo "document.getElementById('txt_cut_smv').value = '".$row[csf("cut_smv")]."';\n";
		echo "document.getElementById('txt_sew_efficiency_per').value = '".$row[csf("sew_effi_percent")]."';\n";
		echo "document.getElementById('txt_cut_efficiency_per').value = '".$row[csf("cut_effi_percent")]."';\n";
		echo "document.getElementById('txt_efficiency_wastage').value = '".$row[csf("efficiency_wastage_percent")]."';\n";
		echo "load_drop_down( 'requires/quotation_entry_controller_v2','".$row[csf("buyer_id")].'_'.$row[csf("company_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_merchant")]."';\n";

		//echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";

		echo "document.getElementById('txt_m_list_no').value = '".$row[csf("m_list_no")]."';\n";

		echo "document.getElementById('txt_bh_marchant').value = '".$row[csf("bh_marchant")]."';\n";

		echo "calculate_lead_time();\n";
		echo "document.getElementById('app_sms').innerHTML = '';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";

		if($cbo_approved_status==1)
		{
			$approval_cause='';
			$menu_id=$_SESSION['menu_id'];
			$user_id=$_SESSION['logic_erp']['user_id'];
			$sql_request="select MAX(id) as id, approval_cause from fabric_booking_approval_cause where entry_form=10 and user_id='$user_id' and booking_id=".$row[csf("id")]." and approval_type=2 and status_active=1 and is_deleted=0 group by approval_cause";//page_id='$menu_id' and

			$nameArray_request=sql_select($sql_request);
			foreach($nameArray_request as $approw)
			{
				$approval_cause=$approw[csf("approval_cause")];
			}
			unset($nameArray_request);

			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
			echo "document.getElementById('txt_un_appv_request').value = '".$approval_cause."';\n";

			echo "document.getElementById('approve1').value = 'Un-Approved';\n";
			//echo "$('#txt_quotation_id').attr('disabled','true')".";\n";
			//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
			echo "$('#txt_style_ref').attr('disabled','true')".";\n";
			echo "$('#txt_revised_no').attr('disabled','true')".";\n";
			echo "$('#cbo_pord_dept').attr('disabled','true')".";\n";
			echo "$('#txt_style_desc').attr('disabled','true')".";\n";
			echo "$('#cbo_currercy').attr('disabled','true')".";\n";
			echo "$('#cbo_agent').attr('disabled','true')".";\n";
			echo "$('#txt_offer_qnty').attr('disabled','true')".";\n";
			echo "$('#cbo_region').attr('disabled','true')".";\n";
			echo "$('#cbo_color_range').attr('disabled','true')".";\n";
			echo "$('#cbo_inco_term').attr('disabled','true')".";\n";
			echo "$('#txt_incoterm_place').attr('disabled','true')".";\n";
			echo "$('#txt_machine_line').attr('disabled','true')".";\n";
			echo "$('#txt_prod_line_hr').attr('disabled','true')".";\n";
			echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
			echo "$('#txt_quotation_date').attr('disabled','true')".";\n";
			echo "$('#txt_est_ship_date').attr('disabled','true')".";\n";
			echo "$('#txt_factory').attr('disabled','true')".";\n";
			echo "$('#txt_remarks').attr('disabled','true')".";\n";
			echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
			echo "$('#garments_nature').attr('disabled','true')".";\n";
			echo "$('#cbo_order_uom').attr('disabled','true')".";\n";
			echo "$('#image_button').attr('disabled','true')".";\n";
			echo "$('#txt_mkt_no').attr('disabled','true')".";\n";
			echo "$('#set_button').attr('disabled','true')".";\n";
			echo "document.getElementById('app_sms').innerHTML = 'This Quotation Is Approved';\n";
		}
		else
		{
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
			echo "document.getElementById('txt_un_appv_request').value = '';\n";
			echo "document.getElementById('approve1').value = 'Approved';\n";
			echo "$('#txt_quotation_id').removeAttr('disabled')".";\n";
			//echo "$('#cbo_company_name').removeAttr('disabled')".";\n";
			echo "$('#cbo_buyer_name').removeAttr('disabled')".";\n";
			echo "$('#txt_style_ref').removeAttr('disabled')".";\n";
			echo "$('#txt_revised_no').removeAttr('disabled')".";\n";
			echo "$('#cbo_pord_dept').removeAttr('disabled')".";\n";
			echo "$('#txt_style_desc').removeAttr('disabled')".";\n";
			echo "$('#cbo_currercy').removeAttr('disabled')".";\n";
			echo "$('#cbo_agent').removeAttr('disabled')".";\n";
			echo "$('#txt_offer_qnty').removeAttr('disabled')".";\n";
			echo "$('#cbo_region').removeAttr('disabled')".";\n";
			echo "$('#cbo_color_range').removeAttr('disabled')".";\n";
			echo "$('#cbo_inco_term').removeAttr('disabled')".";\n";
			echo "$('#txt_incoterm_place').removeAttr('disabled')".";\n";
			echo "$('#txt_machine_line').removeAttr('disabled')".";\n";
			echo "$('#txt_prod_line_hr').removeAttr('disabled')".";\n";
			echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
			echo "$('#txt_quotation_date').removeAttr('disabled')".";\n";
			echo "$('#txt_est_ship_date').removeAttr('disabled')".";\n";
			echo "$('#txt_factory').removeAttr('disabled')".";\n";
			echo "$('#txt_remarks').removeAttr('disabled')".";\n";
			echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
			echo "$('#garments_nature').removeAttr('disabled')".";\n";
			echo "$('#cbo_order_uom').removeAttr('disabled')".";\n";
			echo "$('#image_button').removeAttr('disabled')".";\n";
			echo "$('#txt_mkt_no').removeAttr('disabled')".";\n";
			echo "$('#set_button').removeAttr('disabled')".";\n";
			echo "document.getElementById('app_sms').innerHTML = '';\n";
		}
	}
	//currier_pre_cost 	currier_percent 	certificate_pre_cost 	certificate_percent
	$data_array=sql_select("select id, quotation_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent,wash_cost,wash_cost_percent, comm_cost, comm_cost_percent, lab_test,lab_test_percent,inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent,currier_pre_cost,currier_percent,	certificate_pre_cost,certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent, common_oh, common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price, total_cost, total_cost_percent, commission,commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs,final_cost_set_pcs_rate, a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date, revised_price,revised_price_date, confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, 	confirm_price_dzn_percent,  margin_dzn, margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs,confirm_date,asking_quoted_price,asking_quoted_price_percent, terget_qty,inserted_by, insert_date, updated_by,update_date,status_active,is_deleted from wo_price_quotation_costing_mst where quotation_id='$data' and status_active=1 and is_deleted=0");
	foreach ($data_array as $row)
	{
		echo "reset_form('quotationdtls_2','','');\n";
		echo "document.getElementById('txt_fabric_pre_cost').value = '".$row[csf("fabric_cost")]."';\n";
		echo "$('#txt_fabric_pre_cost').attr('pre_fab_cost','".$row[csf("fabric_cost")]."');\n";

		echo "document.getElementById('txt_fabric_po_price').value = '".$row[csf("fabric_cost_percent")]."';\n";
		echo "document.getElementById('txt_trim_pre_cost').value = '".$row[csf("trims_cost")]."';\n";
		echo "$('#txt_trim_pre_cost').attr('pre_trim_cost','".$row[csf("trims_cost")]."');\n";

		echo "document.getElementById('txt_trim_po_price').value = '".$row[csf("trims_cost_percent")]."';\n";
		echo "document.getElementById('txt_embel_pre_cost').value = '".$row[csf("embel_cost")]."';\n";
		echo "$('#txt_embel_pre_cost').attr('pre_embl_cost','".$row[csf("embel_cost")]."');\n";

		echo "document.getElementById('txt_embel_po_price').value = '".$row[csf("embel_cost_percent")]."';\n";
		echo "document.getElementById('txt_wash_pre_cost').value = '".$row[csf("wash_cost")]."';\n";
		echo "$('#txt_wash_pre_cost').attr('pre_wash_cost','".$row[csf("wash_cost")]."');\n";

		echo "document.getElementById('txt_wash_po_price').value = '".$row[csf("wash_cost_percent")]."';\n";
		echo "document.getElementById('txt_comml_pre_cost').value = '".$row[csf("comm_cost")]."';\n";
		echo "$('#txt_comml_pre_cost').attr('pre_comml_cost','".$row[csf("comm_cost")]."');\n";

		echo "document.getElementById('txt_comml_po_price').value = '".$row[csf("comm_cost_percent")]."';\n";
		echo "document.getElementById('txt_lab_test_pre_cost').value = '".$row[csf("lab_test")]."';\n";
		echo "document.getElementById('txt_lab_test_po_price').value = '".$row[csf("lab_test_percent")]."';\n";
		echo "document.getElementById('txt_inspection_pre_cost').value = '".$row[csf("inspection")]."';\n";
		echo "document.getElementById('txt_inspection_po_price').value = '".$row[csf("inspection_percent")]."';\n";
		echo "document.getElementById('txt_cm_pre_cost').value = '".$row[csf("cm_cost")]."';\n";
		echo "document.getElementById('txt_cm_po_price').value = '".$row[csf("cm_cost_percent")]."';\n";
		echo "document.getElementById('txt_freight_pre_cost').value = '".$row[csf("freight")]."';\n";
		echo "document.getElementById('txt_freight_po_price').value = '".$row[csf("freight_percent")]."';\n";

		echo "document.getElementById('txt_currier_pre_cost').value = '".$row[csf("currier_pre_cost")]."';\n";
		echo "document.getElementById('txt_currier_po_price').value = '".$row[csf("currier_percent")]."';\n";
		echo "document.getElementById('txt_certificate_pre_cost').value = '".$row[csf("certificate_pre_cost")]."';\n";
		echo "document.getElementById('txt_certificate_po_price').value = '".$row[csf("certificate_percent")]."';\n";

		echo "document.getElementById('txt_design_pre_cost').value = '".$row[csf("design_pre_cost")]."';\n";
		echo "document.getElementById('txt_design_po_price').value = '".$row[csf("design_percent")]."';\n";
		echo "document.getElementById('txt_studio_pre_cost').value = '".$row[csf("studio_pre_cost")]."';\n";
		echo "document.getElementById('txt_studio_po_price').value = '".$row[csf("studio_percent")]."';\n";


		echo "document.getElementById('txt_common_oh_pre_cost').value = '".$row[csf("common_oh")]."';\n";
		echo "document.getElementById('txt_common_oh_po_price').value = '".$row[csf("common_oh_percent")]."';\n";

		echo "document.getElementById('txt_depr_amor_pre_cost').value = '".$row[csf("depr_amor_pre_cost")]."';\n";
		echo "document.getElementById('txt_depr_amor_po_price').value = '".$row[csf("depr_amor_po_price")]."';\n";

		echo "document.getElementById('txt_interest_pre_cost').value = '".$row[csf("interest_pre_cost")]."';\n";
		echo "document.getElementById('txt_interest_po_price').value = '".$row[csf("interest_po_price")]."';\n";

		echo "document.getElementById('txt_income_tax_pre_cost').value = '".$row[csf("income_tax_pre_cost")]."';\n";
		echo "document.getElementById('txt_income_tax_po_price').value = '".$row[csf("income_tax_po_price")]."';\n";

		echo "document.getElementById('txt_total_pre_cost').value = '".$row[csf("total_cost")]."';\n";
		echo "document.getElementById('txt_total_po_price').value = '".$row[csf("total_cost_percent")]."';\n";
		echo "document.getElementById('txt_commission_pre_cost').value = '".$row[csf("commission")]."';\n";
		echo "$('#txt_commission_pre_cost').attr('pre_commis_cost','".$row[csf("commission")]."');\n";

		echo "document.getElementById('txt_commission_po_price').value = '".$row[csf("commission_percent")]."';\n";
		echo "document.getElementById('txt_final_cost_dzn_pre_cost').value = '".$row[csf("final_cost_dzn")]."';\n";
		echo "document.getElementById('txt_final_cost_dzn_po_price').value = '".$row[csf("final_cost_dzn_percent")]."';\n";
		echo "document.getElementById('txt_final_cost_pcs_po_price').value = '".$row[csf("final_cost_pcs")]."';\n";
		echo "document.getElementById('txt_final_cost_set_pcs_rate').value = '".$row[csf("final_cost_set_pcs_rate")]."';\n";
		echo "document.getElementById('txt_1st_quoted_price_pre_cost').value = '".$row[csf("a1st_quoted_price")]."';\n";
		echo "document.getElementById('txt_1st_quoted_po_price').value = '".$row[csf("a1st_quoted_price_percent")]."';\n";
		echo "document.getElementById('txt_first_quoted_price_date').value = '".change_date_format($row[csf("a1st_quoted_price_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_revised_price_pre_cost').value = '".$row[csf("revised_price")]."';\n";
		echo "document.getElementById('txt_revised_price_date').value = '".change_date_format($row[csf("revised_price_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_confirm_price_pre_cost').value = '".$row[csf("confirm_price")]."';\n";
		echo "document.getElementById('txt_confirm_price_set_pcs_rate').value = '".$row[csf("confirm_price_set_pcs_rate")]."';\n";
		echo "document.getElementById('txt_confirm_price_pre_cost_dzn').value = '".$row[csf("confirm_price_dzn")]."';\n";
		echo "document.getElementById('txt_confirm_price_po_price_dzn').value = '".$row[csf("confirm_price_dzn_percent")]."';\n";

		echo "document.getElementById('txt_cost_dzn').value = '".$row[csf("total_cost")]."';\n";
		echo "document.getElementById('txt_cost_dzn_po_price').value = '".$row[csf("total_cost_percent")]."';\n";

		echo "document.getElementById('txt_margin_dzn_pre_cost').value = '".$row[csf("margin_dzn")]."';\n";
		echo "document.getElementById('txt_margin_dzn_po_price').value = '".$row[csf("margin_dzn_percent")]."';\n";

		echo "document.getElementById('txt_with_commission_pre_cost_dzn').value = '".$row[csf("price_with_commn_dzn")]."';\n";
		echo "document.getElementById('txt_with_commission_po_price_dzn').value = '".$row[csf("price_with_commn_percent_dzn")]."';\n";
		echo "document.getElementById('txt_with_commission_pre_cost_pcs').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('txt_with_commission_po_price_pcs').value = '".$row[csf("price_with_commn_percent_pcs")]."';\n";
		echo "document.getElementById('txt_confirm_date_pre_cost').value = '".change_date_format($row[csf("confirm_date")],'dd-mm-yyyy','-')."';\n";

		//echo "document.getElementById('txt_asking_profit_from_lib').value = '".$row[csf("asking_profit_from_lib")]."';\n";
		echo "document.getElementById('txt_asking_quoted_price').value = '".$row[csf("asking_quoted_price")]."';\n";
		echo "document.getElementById('txt_asking_quoted_po_price').value = '".$row[csf("asking_quoted_price_percent")]."';\n";
		echo "document.getElementById('txt_terget_qty').value = '".$row[csf("terget_qty")]."';\n";
		echo "document.getElementById('update_id_dtls').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_mode').value = '1';\n";
		if($cbo_approved_status==1)
		{
			echo "$('#txt_lab_test_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_inspection_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_cm_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_freight_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_common_oh_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_1st_quoted_price_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_first_quoted_price_date').attr('disabled','true')".";\n";
			echo "$('#txt_revised_price_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_revised_price_date').attr('disabled','true')".";\n";
			echo "$('#txt_confirm_price_pre_cost').attr('disabled','true')".";\n";
			echo "$('#txt_confirm_date_pre_cost').attr('disabled','true')".";\n";
			//echo "$('#save2').attr('disabled','true')".";\n";
			//echo "$('#update2').attr('disabled','true')".";\n";
			//echo "$('#Delete2').attr('disabled','true')".";\n";
		}
		else
		{
			echo "$('#txt_lab_test_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_inspection_pre_cost').removeAttr('disabled')".";\n";
			//echo "$('#txt_cm_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_freight_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_common_oh_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_1st_quoted_price_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_first_quoted_price_date').removeAttr('disabled')".";\n";
			echo "$('#txt_revised_price_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_revised_price_date').removeAttr('disabled')".";\n";
			echo "$('#txt_confirm_price_pre_cost').removeAttr('disabled')".";\n";
			echo "$('#txt_confirm_date_pre_cost').removeAttr('disabled')".";\n";
			//echo "$('#save2').removeAttr('disabled')".";\n";
			//echo "$('#update2').removeAttr('disabled')".";\n";
			//echo "$('#Delete2').removeAttr('disabled')".";\n";
		}
		if($_SESSION['logic_erp']['data_arr'][314][$company_id]['txt_common_oh_pre_cost']['is_disable']==1){
			echo "$('#txt_common_oh_pre_cost').attr('disabled','true')".";\n";
		}
		if($_SESSION['logic_erp']['data_arr'][314][$company_id]['txt_income_tax_pre_cost']['is_disable']==1){
			echo "$('#txt_income_tax_pre_cost').attr('disabled','true')".";\n";
		}
	}
}

if($action=="open_set_list_view")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
extract($_REQUEST);
//echo $set_smv_id;
?>
<script>

var set_smv_id='<? echo $set_smv_id; ?>';
function add_break_down_set_tr( i )
{
	var unit_id= document.getElementById('unit_id').value;
	if(unit_id==1)
	{
		alert('Only One Item');
		return false;
	}
	var row_num=$('#tbl_set_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}

	if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
	{
		return;
	}
	else
	{
		i++;

		 $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_set_details");

		  $('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);check_smv_set("+i+");check_smv_set_popup("+i+");");

		  $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
		  $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");

		  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
		  $('#cboitem_'+i).val('');
		  set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		  set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
	}
}

function fn_delete_down_tr(rowNo,table_id)
{
	if(table_id=='tbl_set_details')
	{
		var numRow = $('table#tbl_set_details tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_set_details tbody tr:last').remove();
		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		 set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		 set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
		  //set_sum_value( 'cons_sum', 'cons_'  );
		  //set_sum_value( 'processloss_sum', 'processloss_'  );
		  //set_sum_value( 'requirement_sum', 'requirement_');
          //set_sum_value( 'pcs_sum', 'pcs_');
	}
}

function calculate_set_smv(i)
{
	var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
	var smv=document.getElementById('smv_'+i).value;
	var set_smv=txtsetitemratio*smv;
	document.getElementById('smvset_'+i).value=set_smv;
	set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
	set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
}

function set_sum_value_set(des_fil_id,field_id)
{
	var rowCount = $('#tbl_set_details tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount );
}

function set_sum_value_smv(des_fil_id,field_id)
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var ddd={ dec_type:1, comma:0, currency:1}
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	//math_operation( des_fil_id, field_id, '+', rowCount );
}

function js_set_value_set()
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var set_breck_down="";
	var item_id=""
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio*Smv')==false)
		{
			return;
		}
		if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0);
		if(set_breck_down=="")
		{
			set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
			item_id+=$('#cboitem_'+i).val();
		}
		else
		{
			set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
			item_id+=","+$('#cboitem_'+i).val();
		}

	}
	document.getElementById('set_breck_down').value=set_breck_down;
	document.getElementById('item_id').value=item_id;

	parent.emailwindow.hide();
}

	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cboitem_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('cboitem_'+k).value)
				{
					alert("Same Gmts Item Duplication Not Allowed.");
					document.getElementById(td).value="0";
					document.getElementById(td).focus();
				}
			}
		}
	}

	function check_smv_set(id)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		//alert(item_id);
		var txt_style_ref='<? echo $txt_style_ref ?>';

		var item_id=$('#cboitem_'+id).val();
		//alert(td);
		//get_php_form_data(company_id,'set_smv_work_study','requires/woven_order_entry_controller' );
		var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'quotation_entry_controller_v2');
		var response=response.split("_");
		if(response[0]==1)
		{
			if(set_smv_id==1)
			{
				$('#smv_'+id).val(response[1]);
				$('#tot_smv_qnty').val(response[1]);
				/*for (var k=1;k<=row_num; k++)
				{
					$('#smv_'+k).val(response[1]);
				}*/
			}
		}
	}

	function check_smv_set_popup(id)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;

		var txt_style_ref='<? echo $txt_style_ref ?>';
		var cbo_company_name='<? echo $cbo_company_name ?>';
		var cbo_buyer_name='<? echo $cbo_buyer_name ?>';
		var item_id=$('#cboitem_'+id).val();
			//alert(set_smv_id);
		if(set_smv_id==4 || set_smv_id==6)
		{
			$('#smv_'+id).val('');
			$('#tot_smv_qnty').val('');
			$('#hidquotid_'+id).val('');

			var page_link="quotation_entry_controller_v2.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
		}
		else
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
			var smv_data=selected_smv_data.split("_");
			var row_id=smv_data[3];

			$("#smv_"+row_id).val(smv_data[0]);
			$("#smv_"+row_id).attr('readonly','readonly');
			$("#hidquotid_"+row_id).val(smv_data[4]);

			calculate_set_smv(row_id);
		}
	}
</script>
</head>
<body>
       <div id="set_details"  align="center">
    	<fieldset>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />
            <input type="hidden" id="item_id" />
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />

            <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="250">Item</th><th width="80">Set Item Ratio</th><th width="80">SMV/Pcs</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?

					$data_array=explode("__",$set_breck_down);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							$gmt_item_id_s=$data[0];
							if(empty($gmt_item_id_s))
							{
								$gmt_item_id_s=$item_id;
							}
							

							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<?
									echo create_drop_down( "cboitem_".$i, 250, get_garments_item_array(2), "",1," -- Select Item --", $gmt_item_id_s, "check_duplicate(".$i.",this.id ); check_smv_set(".$i."); check_smv_set_popup(".$i.");",'','' );
									?>

                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> />
                                    </td>

                                   <td>
                                    <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" />
                                    <input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" />
                                    </td>
                                    <td>
                                    <input type="hidden" id="hidquotid_<? echo $i;?>" name="hidquotid_<? echo $i;?>" style="width:30px" class="text_boxes_numeric" value="<? echo $data[4]; ?>" readonly/>
                                    <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
                                     </td>
                                </tr>
                            <?
						}
					}
					else
					{
						 //$sql=sql_select("select a.id,a.item_name from sample_development_mst a,sample_development_dtls b where  a.quotation_id='$txt_inquery_id' and  a.id=b.sample_mst_id");

						 $item_name = return_field_value("item_name" ," sample_development_mst","quotation_id='$txt_inquery_id'");
						 $gmt_item_id_s=$item_name;
						if(empty($gmt_item_id_s))
						{
							$gmt_item_id_s=$item_id;
						}

						?>
						<tr id="settr_1" align="center">
                           <td>
                            <?
                            echo create_drop_down( "cboitem_1", 240, get_garments_item_array(2), "",1,"--Select--", $gmt_item_id_s, 'check_duplicate(1,this.id ); check_smv_set(1); check_smv_set_popup(1);','','' );
                            ?>
                            </td>
                             <td>
                            <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:70px" class="text_boxes_numeric" onChange="calculate_set_smv(1)" value="<? if ($unit_id==1) {echo "1";} else{echo "";}?>"  <? if ($unit_id==1){echo "readonly";} else{echo "";}?>  />
                             </td>
                             <td>
                            <input type="text" id="smv_1"   name="smv_1" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<? //echo $smv_pcs_precost; ?>" />
                            <input type="hidden" id="smvset_1"   name="smvset_1" style="width:70px"  class="text_boxes_numeric"  value="<? //echo $smv_set_precost; ?>" />
                            </td>
                            <td>
                            <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                            <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                            <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                            </td>
                        </tr>
                    <?
					}
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="250">Total</th>
                            <th  width="80"><input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:70px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly  /></th>
                              <th  width="80">
                                <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:70px"  value="<? //if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 1;} ?>" readonly />
                            </th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="800" cellspacing="0" class="" border="0">

                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/>

                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>
 </body>
<script>
	set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
	set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if($action=="open_smv_list")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$item_id=$item_id;
	$style_id=$txt_style_ref;
	$set_smv_id=$set_smv_id;
	$row_id=$id;
	$set_smv_id=$set_smv_id;
	$cbo_buyer_name=$cbo_buyer_name;
	$cbo_company_name=$cbo_company_name;
	//echo $cbo_company_name;
	?>
	<script type="text/javascript">
      function js_set_value(id)
      { 	//alert(id);
		  document.getElementById('selected_smv').value=id;
		  parent.emailwindow.hide();
      }
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="item_id" value="<?  echo $item_id;?>">
                    <input type="hidden" id="row_id" value="<?  echo $row_id;?>">
                    <input type="hidden" id="company_id" value="<?  echo $cbo_company_name;?>">
                &nbsp;</th>
            </thead>
            <tr>
                <td id=""><? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value, 'create_item_smv_search_list_view', 'search_div', 'quotation_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
        </table>
    	<div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if($action=="create_item_smv_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$row_id=$data[4];

	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
	if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" />
	<?
	$sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where a.is_deleted=0 $gmts_item_con2  order by a.id Desc";
	$result = sql_select($sewing_sql);
	foreach($result as $row)
	{
		$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
		$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
	}
	// print_r($code_smv_arr);b.lib_sewing_id
	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}

	$sql="select a.id, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 $gmts_item_con $style_con $buyer_id_con
	order by id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		//$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
		$smv_dtls_arr['str']['style_ref']=$row[csf('style_ref')];
		$smv_dtls_arr['str']['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr['str']['id'].=$row[csf('id')].',';
		//$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$smv_dtls_arr['str']['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		//$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
		//$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
		$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];

		$smv_sewing_arr[$code_id][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	//print_r($smv_dtls_arr);
	?>
	<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Sys. ID.</th>
                <th width="200">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($smv_dtls_arr as $arrdata)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
			$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

			$finish_smv=$cut_smv=$sewing_smv=0;
			foreach($lib_sewing_ids as $lsid)
			{
				$finish_smv+=$smv_sewing_arr[4][$lsid]['operator_smv'];
				$cut_smv+=$smv_sewing_arr[7][$lsid]['operator_smv'];
				$sewing_smv+=$smv_sewing_arr[8][$lsid]['operator_smv'];
			}
			$sys_id=rtrim($arrdata['id'],',');
			$ids=array_filter(array_unique(explode(",",$sys_id)));
			//print_r($ids);
			$id_str=""; $k=0;
			foreach($ids as $idstr)
			{
				if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;
				$k++;
			}
			$finish_smv=$finish_smv/$k;
			$cut_smv=$cut_smv/$k;
			$sewing_smv=$sewing_smv/$k;

			$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
                <td width="30"><? echo $i;//.'='.$k ?></td>
                <td width="140" style="word-break:break-all"><? echo $id_str; ?></td>
                <td width="160" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
                <td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
                <td><p><? echo $arrdata['operation_count']; ?></p></td>
			</tr>
			<?
			$i++;
		}
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");

		//echo "10**".$data_component; die;

		$id=return_next_id( "id", "wo_price_quotation", 1) ;
		$field_array="id, inquery_id, company_id, location_id, buyer_id, style_ref,lib_style_id, revised_no, pord_dept,product_code, style_desc, m_list_no, bh_marchant, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr,  costing_per, quot_date, est_ship_date, op_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season_buyer_wise, dealing_merchant, ready_to_approved,mkt_no,buyer_brand_id,level_type_id,design_type,division,department_id,style_no,style_short_name,inserted_by, insert_date, status_active, is_deleted";


		$data_array="(".$id.",".$txt_inquery_id.",".$cbo_company_name.",".$cbo_location_id.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_style_ref_id.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$txt_m_list_no.",".$txt_bh_marchant.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$txt_quotation_date.",".$txt_est_ship_date.",".$txt_op_date.",".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$cbo_season_name.",".$cbo_dealing_merchant.",".$cbo_ready_to_approved.",".$txt_mkt_no.",'".str_replace("'", "", $cbo_buyer_brand_id)."','".str_replace("'", "", $cbo_level_type_id)."','".str_replace("'", "", $cbo_design_type)."','".str_replace("'", "", $txt_division)."','".str_replace("'", "", $cbo_department_id)."','".str_replace("'", "", $txt_style_id)."','".str_replace("'", "", $txt_style_short_name)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

		$field_array1="id, quotation_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$add_comma=0;
		$id1=return_next_id( "id", "  wo_price_quotation_set_details", 1 );
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$dtlsid=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
		$flag=1; //echo "10**";
		//echo "0**insert into wo_price_quotation (".$field_array.") values ".$data_array;
		$rID=sql_insert("wo_price_quotation",$field_array,$data_array,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("wo_price_quotation_set_details",$field_array1,$data_array1,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;

		$dtls_id=0; $dtls_respone=0;
		if($flag==1)
		{
			$res_data_component=fnc_quotation_entry_component($data_component.'***'.$id.'***'.$dtlsid.'***0');
			//echo $id; die;

			$ex_dataComponent=explode("__",$res_data_component);
			$dtls_respone=$ex_dataComponent[0];
			$dtls_id=$ex_dataComponent[1];
			if($dtls_respone==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo $flag."**".$rID."**".$rID1; die;
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".$id."**".$dtls_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id."**".$dtls_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".$dtls_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".$dtls_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$ready_to_approved=str_replace("'","",$cbo_ready_to_approved);
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;

		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
		/*$quot_id="";
		$quot_sql=sql_select("select id from wo_price_quotation_costing_mst where quotation_id=$update_id and status_active=1 and is_deleted=0");
		foreach($quot_sql as $row){
			$quot_id.=$row[csf('id')].',';
		}
		$quot_ids=chop($quot_id,',');*/

		//Quotation Detal Part ready to approve Valitation end
		$field_array="inquery_id*location_id*buyer_id*style_ref*lib_style_id*revised_no*pord_dept*product_code*style_desc*m_list_no*bh_marchant*currency*agent*offer_qnty*region*color_range*incoterm*incoterm_place* machine_line*prod_line_hr*costing_per*quot_date*est_ship_date*op_date*factory*remarks*garments_nature*order_uom*gmts_item_id*set_break_down*total_set_qnty* cm_cost_predefined_method_id*exchange_rate*sew_smv*cut_smv*sew_effi_percent*cut_effi_percent*efficiency_wastage_percent*season_buyer_wise*dealing_merchant*ready_to_approved*mkt_no*buyer_brand_id*level_type_id*design_type*division*department_id*style_no*style_short_name*updated_by*update_date*status_active* is_deleted";
		$data_array="".$txt_inquery_id."*".$cbo_location_id."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_style_ref_id."*".$txt_revised_no."*".$cbo_pord_dept."*".$txt_product_code."*".$txt_style_desc."*".$txt_m_list_no."*".$txt_bh_marchant."*".$cbo_currercy."*".$cbo_agent."*".$txt_offer_qnty."*".$cbo_region."*".$cbo_color_range."*".$cbo_inco_term."*".$txt_incoterm_place."*".$txt_machine_line."*".$txt_prod_line_hr."*".$cbo_costing_per."*".$txt_quotation_date."*".$txt_est_ship_date."*".$txt_op_date."*".$txt_factory."*".$txt_remarks."*".$garments_nature."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$cm_cost_predefined_method_id."*".$txt_exchange_rate."*".$txt_sew_smv."*".$txt_cut_smv."*".$txt_sew_efficiency_per."*".$txt_cut_efficiency_per."*".$txt_efficiency_wastage."*".$cbo_season_name."*".$cbo_dealing_merchant."*".$cbo_ready_to_approved."*".$txt_mkt_no."*'".str_replace("'", "", $cbo_buyer_brand_id)."'*'".str_replace("'", "", $cbo_level_type_id)."'*'".str_replace("'", "", $cbo_design_type)."'*'".str_replace("'", "", $txt_division)."'*'".str_replace("'", "", $cbo_department_id)."'*'".str_replace("'", "", $txt_style_id)."'*'".str_replace("'", "", $txt_style_short_name)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";

		$field_array1="id, quotation_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$add_comma=0;
		$id1=return_next_id( "id", "  wo_price_quotation_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.", ".$update_id.", '".$set_breck_down_arr[0]."', '".$set_breck_down_arr[1]."', '".$set_breck_down_arr[2]."', '".$set_breck_down_arr[3]."', '".$set_breck_down_arr[4]."')";
			$add_comma++;
			$id1=$id1+1;
			//$item_ids.=$set_breck_down_arr[0].',';
		}

		$flag=1;

		$copy_quot=return_field_value("copy_quotation", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=20 and status_active=1 and is_deleted=0");
		if($copy_quot==1)
		{
			$data_int=$cbo_currercy.'****'.$set_breck_down;
			$set_smv_id=str_replace("'","",$set_smv_id);
			if($set_smv_id==2 || $set_smv_id==4 || $set_smv_id==6) fnc_smv_style_integration($db_type,$cbo_company_name,$update_id,$data_int,$txt_sew_smv,$txt_cut_smv,2);
		}

		$dtls_id=0; $dtls_respone=0; //echo "10**";
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		//echo $res_data_component; die;
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1 && $flag==1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$rID=sql_update("wo_price_quotation",$field_array,$data_array,"id","".$update_id."",0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			$rID1=execute_query( "delete from wo_price_quotation_set_details where quotation_id =".$update_id."",0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			$rID2=sql_insert("wo_price_quotation_set_details",$field_array1,$data_array1,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}

		//echo "10**".$dat; die;
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".$dtls_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id)."**".$dtls_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$dtls_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$dtls_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$ready_to_approved=str_replace("'","",$cbo_ready_to_approved);
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;

		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
		$rID=1;
		if($zero_value==1)
		{
			$rID=execute_query( "update wo_price_quotation set status_active=0, is_deleted=1 where id =".$update_id."",0);
			$rID=execute_query( "update wo_price_quotation_costing_mst set status_active=0, is_deleted=1 where quotation_id=".$update_id."",0);
			//$rID=execute_query( "delete from wo_price_quotation_set_details where  quotation_id =".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_comarcial_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_commiss_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_embe_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_fabric_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_fab_conv_cost_dtls set status_active=0, is_deleted=1 where quotation_id=".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_fab_yarn_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id."",0);

			//$rID=execute_query( "delete from wo_pri_quo_fab_yarn_cost_dtls where quotation_id =".$update_id."",0);

			$rID=execute_query( "update wo_pri_quo_sum_dtls set status_active=0, is_deleted=1 where quotation_id=".$update_id."",0);
			$rID=execute_query( "update wo_pri_quo_trim_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id."",0);
		}

		//$field_array="updated_by*update_date*status_active*is_deleted";
		//$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//$rID=sql_delete("wo_price_quotation",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$rID."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$rID."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".$rID."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==3)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'","",$cbo_approved_status)==1){
			$approved=0;
			$sql=sql_select("select a.approved from wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and b.quotation_id=$update_id");
			foreach($sql as $row){
				$approved=$row[csf('approved')];
			}
			if($approved==3) $approved=1; else $approved=$approved;

			if($approved==1){
				echo "approvedPre**".str_replace("'","",$update_id);
				disconnect($con);die;
			}
		}

		$field_array="approved*approved_by*approved_date";
		if(trim(str_replace("'","",$cbo_approved_status))==2)
		{
			$data_array="'1'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date."'";
		}
		else
		{
			$data_array="'2'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date."'";
		}
		//echo "0**insert into  wo_price_quotation (".$field_array.") values".$data_array;
		//die;
		$rID=sql_update("wo_price_quotation",$field_array,$data_array,"id",$update_id,1);
	   //die;
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "3**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "3**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
			}
			else{
				oci_rollback($con);
				echo "10**".$rID."**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
			}
		}
		disconnect($con);
		die;
	}
}

function fnc_quotation_entry_component($data)
{
	global $pc_date_time;
	global $db_type;
	$com_data=explode("***",$data);
	//echo $data;
	$ex_data=explode("_",$com_data[0]);
	//echo $ex_data[66].'kk'; die;
	$update_id_dtls=$txt_fabric_pre_cost=$txt_fabric_po_price=$txt_trim_pre_cost=$txt_trim_po_price=$txt_embel_pre_cost=$txt_embel_po_price=$txt_wash_pre_cost=$txt_wash_po_price=$txt_comml_pre_cost=$txt_comml_po_price=$txt_lab_test_pre_cost=$txt_lab_test_po_price=$txt_inspection_pre_cost=$txt_inspection_po_price=$txt_cm_pre_cost=$txt_cm_po_price=$txt_freight_pre_cost=$txt_freight_po_price=$txt_currier_pre_cost=$txt_currier_po_price=$txt_certificate_pre_cost=$txt_certificate_po_price=$txt_common_oh_pre_cost=$txt_common_oh_po_price=$txt_depr_amor_pre_cost=$txt_depr_amor_po_price=$txt_interest_pre_cost=$txt_interest_po_price=$txt_income_tax_pre_cost=$txt_income_tax_po_price=$txt_total_pre_cost=$txt_total_po_price=$txt_commission_pre_cost=$txt_commission_po_price=$txt_final_cost_dzn_pre_cost=$txt_final_cost_dzn_po_price=$txt_final_cost_pcs_po_price=$txt_final_cost_set_pcs_rate=$txt_1st_quoted_price_pre_cost=$txt_1st_quoted_po_price=$txt_first_quoted_price_date=$txt_revised_price_pre_cost=$txt_revised_price_date=$txt_confirm_price_pre_cost=$txt_confirm_price_set_pcs_rate=$txt_confirm_price_pre_cost_dzn=$txt_confirm_price_po_price_dzn=$txt_margin_dzn_pre_cost=$txt_margin_dzn_po_price=$txt_confirm_date_pre_cost=$txt_asking_quoted_price=$txt_asking_quoted_po_price=$txt_with_commission_pre_cost_dzn=$txt_with_commission_po_price_dzn=$txt_with_commission_pre_cost_pcs=$txt_with_commission_po_price_pcs=$txt_terget_qty=$txt_studio_pre_cost=$txt_design_pre_cost=$txt_studio_po_price=$txt_design_po_price=$update_id=$cbo_costing_per=$cbo_order_uom=$quotation_id=$tot_set_qnty=$txt_quotation_date=$cbo_company_name=0;

	if($ex_data[0]=="" ) $update_id_dtls="'".$com_data[2]."'"; else $update_id_dtls="'".$ex_data[0]."'";
	
	//$update_id_dtls="'".$ex_data[0]."'";
	$txt_fabric_pre_cost="'".$ex_data[1]."'";
	$txt_fabric_po_price="'".$ex_data[2]."'";
	$txt_trim_pre_cost="'".$ex_data[3]."'";
	$txt_trim_po_price="'".$ex_data[4]."'";
	$txt_embel_pre_cost="'".$ex_data[5]."'";
	$txt_embel_po_price="'".$ex_data[6]."'";
	$txt_wash_pre_cost="'".$ex_data[7]."'";
	$txt_wash_po_price="'".$ex_data[8]."'";
	$txt_comml_pre_cost="'".$ex_data[9]."'";
	$txt_comml_po_price="'".$ex_data[10]."'";
	$txt_lab_test_pre_cost="'".$ex_data[11]."'";
	$txt_lab_test_po_price="'".$ex_data[12]."'";
	$txt_inspection_pre_cost="'".$ex_data[13]."'";
	$txt_inspection_po_price="'".$ex_data[14]."'";
	$txt_cm_pre_cost="'".$ex_data[15]."'";
	$txt_cm_po_price="'".$ex_data[16]."'";
	$txt_freight_pre_cost="'".$ex_data[17]."'";
	$txt_freight_po_price="'".$ex_data[18]."'";
	$txt_currier_pre_cost="'".$ex_data[19]."'";
	$txt_currier_po_price="'".$ex_data[20]."'";
	$txt_certificate_pre_cost="'".$ex_data[21]."'";
	$txt_certificate_po_price="'".$ex_data[22]."'";
	$txt_common_oh_pre_cost="'".$ex_data[23]."'";
	$txt_common_oh_po_price="'".$ex_data[24]."'";
	$txt_depr_amor_pre_cost="'".$ex_data[25]."'";
	$txt_depr_amor_po_price="'".$ex_data[26]."'";
	$txt_interest_pre_cost="'".$ex_data[27]."'";
	$txt_interest_po_price="'".$ex_data[28]."'";
	$txt_income_tax_pre_cost="'".$ex_data[29]."'";
	$txt_income_tax_po_price="'".$ex_data[30]."'";
	$txt_total_pre_cost="'".$ex_data[31]."'";
	$txt_total_po_price="'".$ex_data[32]."'";
	$txt_commission_pre_cost="'".$ex_data[33]."'";
	$txt_commission_po_price="'".$ex_data[34]."'";
	$txt_final_cost_dzn_pre_cost="'".$ex_data[35]."'";
	$txt_final_cost_dzn_po_price="'".$ex_data[36]."'";
	$txt_final_cost_pcs_po_price="'".$ex_data[37]."'";
	$txt_final_cost_set_pcs_rate="'".$ex_data[38]."'";
	$txt_1st_quoted_price_pre_cost="'".$ex_data[39]."'";
	$txt_1st_quoted_po_price="'".$ex_data[40]."'";
	if($db_type==0) $ex_data[41]=change_date_format($ex_data[41], "yyyy-mm-dd", "-"); else if ($db_type==2) $ex_data[41]=change_date_format($ex_data[41], "yyyy-mm-dd", "-",1);
	$txt_first_quoted_price_date="'".$ex_data[41]."'";
	$txt_revised_price_pre_cost="'".$ex_data[42]."'";
	if($db_type==0) $ex_data[43]=change_date_format($ex_data[43], "yyyy-mm-dd", "-"); else if ($db_type==2) $ex_data[43]=change_date_format($ex_data[43], "yyyy-mm-dd", "-",1);
	$txt_revised_price_date="'".$ex_data[43]."'";
	$txt_confirm_price_pre_cost="'".$ex_data[44]."'";
	$txt_confirm_price_set_pcs_rate="'".$ex_data[45]."'";
	$txt_confirm_price_pre_cost_dzn="'".$ex_data[46]."'";
	$txt_confirm_price_po_price_dzn="'".$ex_data[47]."'";
	$txt_margin_dzn_pre_cost="'".$ex_data[48]."'";
	$txt_margin_dzn_po_price="'".$ex_data[49]."'";

	if($db_type==0) $ex_data[50]=change_date_format($data[3], "yyyy-mm-dd", "-"); else if ($db_type==2) $ex_data[50]=change_date_format($ex_data[50], "yyyy-mm-dd", "-",1);

	$txt_confirm_date_pre_cost="'".$ex_data[50]."'";
	$txt_asking_quoted_price="'".$ex_data[51]."'";
	$txt_asking_quoted_po_price="'".$ex_data[52]."'";
	$txt_with_commission_pre_cost_dzn="'".$ex_data[53]."'";
	$txt_with_commission_po_price_dzn="'".$ex_data[54]."'";
	$txt_with_commission_pre_cost_pcs="'".$ex_data[55]."'";
	$txt_with_commission_po_price_pcs="'".$ex_data[56]."'";
	$txt_terget_qty="'".$ex_data[57]."'";
	$txt_studio_pre_cost="'".$ex_data[58]."'";
	$txt_design_pre_cost="'".$ex_data[59]."'";
	$txt_studio_po_price="'".$ex_data[60]."'";
	$txt_design_po_price="'".$ex_data[61]."'";
	$cbo_costing_per="'".$ex_data[63]."'";
	$cbo_order_uom="'".$ex_data[64]."'";
	$tot_set_qnty="'".$ex_data[65]."'";
	$txt_quotation_date="'".$ex_data[66]."'";
	$cbo_company_name="'".$ex_data[67]."'";
	$operation=$com_data[3];

	if($ex_data[62]=="") $update_id="'".$com_data[1]."'"; else $update_id="'".$ex_data[62]."'";
	
	if(str_replace("'","",$update_id_dtls)=="")
	{
		$sql=sql_select("select id, quotation_id from wo_price_quotation_costing_mst where quotation_id=$update_id status_active=1 and is_deleted=0");
		foreach($sql as $row){
			$update_id_dtls="'".$row[csf('id')]."'";
		}
	}
	
	$txt_confirm_price_quto_cost_dzn=str_replace("'","",$txt_confirm_price_pre_cost_dzn);
	$txt_confirm_price_quto_percent_dzn=str_replace("'","",$txt_confirm_price_po_price_dzn);
	
	$sql_price=sql_select("select confirm_price_dzn, confirm_price_dzn_percent from wo_price_quotation_costing_mst where quotation_id=$update_id ");
	$tot_confirm_price_dzn=0; $tot_confirm_price_dzn_percent=0;
	foreach($sql_price as $row_fab){
		$tot_confirm_price_dzn=$row_fab[csf('confirm_price_dzn')];
		$tot_confirm_price_dzn_percent=$row_fab[csf('confirm_price_dzn_percent')];
	}
	
	$confirm_price_dzn=($txt_confirm_price_quto_cost_dzn+$tot_confirm_price_dzn)-$tot_confirm_price_dzn;
	$confirm_price_dzn_percent=$txt_confirm_price_quto_percent_dzn;
	//echo $update_id;
	//echo $operation; die;
	if ( $operation==0)  // Insert Here
	{
		$quot_id="";
		$quot_sql=sql_select("select id from wo_price_quotation_costing_mst where quotation_id=$update_id and status_active=1 and is_deleted=0");
		foreach($quot_sql as $row){
			$quot_id.=$row[csf('id')].',';
		}
		$quot_ids=chop($quot_id,',');

		$field_array="id, quotation_id, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, 	inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, design_pre_cost, design_percent, studio_pre_cost, studio_percent, common_oh, common_oh_percent, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, terget_qty, inserted_by, insert_date, status_active, is_deleted ";
		$data_array="(".$update_id_dtls.", ".$update_id.", ".$cbo_costing_per.", ".$cbo_order_uom.", ".$txt_fabric_pre_cost.", ".$txt_fabric_po_price.", ".$txt_trim_pre_cost.", ".$txt_trim_po_price.", ".$txt_embel_pre_cost.", ".$txt_embel_po_price.", ".$txt_wash_pre_cost.", ".$txt_wash_po_price.", ".$txt_comml_pre_cost.", ".$txt_comml_po_price.", ".$txt_lab_test_pre_cost.", ".$txt_lab_test_po_price.", ".$txt_inspection_pre_cost.", ".$txt_inspection_po_price.", ".$txt_cm_pre_cost.", ".$txt_cm_po_price.", ".$txt_freight_pre_cost.", ".$txt_freight_po_price.", ".$txt_currier_pre_cost.", ".$txt_currier_po_price.", ".$txt_certificate_pre_cost.", ".$txt_certificate_po_price.", ".$txt_design_pre_cost.", ".$txt_design_po_price.", ".$txt_studio_pre_cost.", ".$txt_studio_po_price.", ".$txt_common_oh_pre_cost.", ".$txt_common_oh_po_price.", ".$txt_depr_amor_pre_cost.", ".$txt_depr_amor_po_price.", ".$txt_interest_pre_cost.", ".$txt_interest_po_price.", ".$txt_income_tax_pre_cost.", ".$txt_income_tax_po_price.", ".$txt_total_pre_cost.", ".$txt_total_po_price.", ".$txt_commission_pre_cost.", ".$txt_commission_po_price.", ".$txt_final_cost_dzn_pre_cost.", ".$txt_final_cost_dzn_po_price.", ".$txt_final_cost_pcs_po_price.", ".$txt_final_cost_set_pcs_rate.", ".$txt_1st_quoted_price_pre_cost.", ".$txt_1st_quoted_po_price.", ".$txt_first_quoted_price_date.", ".$txt_revised_price_pre_cost.", ".$txt_revised_price_date.", ".$txt_confirm_price_pre_cost.", ".$txt_confirm_price_set_pcs_rate.", ".$txt_confirm_price_pre_cost_dzn." ,".$txt_confirm_price_po_price_dzn.", ".$txt_margin_dzn_pre_cost.", ".$txt_margin_dzn_po_price.", ".$txt_with_commission_pre_cost_dzn.", ".$txt_with_commission_po_price_dzn.", ".$txt_with_commission_pre_cost_pcs.", ".$txt_with_commission_po_price_pcs.", ".$txt_confirm_date_pre_cost.", ".$txt_asking_quoted_price.", ".$txt_asking_quoted_po_price.", ".$txt_terget_qty.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', '1', '0')";
		$flag=1;
		$return_component=sql_insert("wo_price_quotation_costing_mst",$field_array,$data_array,1);
		if($return_component==1 && $flag==1) $flag=1; else $flag=0;

		if($quot_ids!=0)
		{
			if($quot_ids!="")
			{
				$rIDmst_de1=execute_query( "delete from wo_price_quotation_costing_mst where id in ($quot_ids)",0);
				if($rIDmst_de1==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo $pc_date_time; die;
		//echo "0**insert into wo_price_quotation_costing_mst (".$field_array.") values ".$data_array;
		return $flag.'__'.str_replace("'","",$update_id_dtls);
	}
	else if ($operation==1)   // Update Here
	{
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}else{
			$approved=$approved;
		}
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
		$txt_confirm_price_quto_cost_dzn=str_replace("'","",$txt_confirm_price_pre_cost_dzn);
		$txt_confirm_price_quto_percent_dzn=str_replace("'","",$txt_confirm_price_po_price_dzn);
		$sql_price=sql_select("select confirm_price_dzn,confirm_price_dzn_percent from wo_price_quotation_costing_mst where quotation_id=$update_id and status_active=1 and is_deleted=0");
		$tot_confirm_price_dzn=0;$tot_confirm_price_dzn_percent=0;
		foreach($sql_price as $row_fab){
			$tot_confirm_price_dzn=$row_fab[csf('confirm_price_dzn')];
			$tot_confirm_price_dzn_percent=$row_fab[csf('confirm_price_dzn_percent')];
		}
		$confirm_price_dzn=($txt_confirm_price_quto_cost_dzn+$tot_confirm_price_dzn)-$tot_confirm_price_dzn;
		$confirm_price_dzn_percent=$txt_confirm_price_quto_percent_dzn;
		$sql_yar=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$yarn_amount=0;
		foreach($sql_yar as $row_yarn){
			$yarn_amount=$row_yarn[csf('amount')];
			//$fabric_cost_percent=($yarn_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		$sql_conv=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$conv_amount=0;
		foreach($sql_conv as $conv){
			$conv_amount=$conv[csf('amount')];
		}
		//wo_pri_quo_fab_conv_cost_dtls
		$sql_fab=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$update_id and amount>0 and status_active=1 and is_deleted=0 group by quotation_id");
		$fab_amount=0;$fabric_cost_percent=0;
		foreach($sql_fab as $row_fab){
			$fab_amount=$row_fab[csf('amount')];
			//$fabric_cost_percent=($fab_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		$tot_fab_amount=$fab_amount+$yarn_amount+$conv_amount;
		$fabric_cost_percent=($tot_fab_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		$sql_tri=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_trim_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$trim_amount=0;	$trim_cost_percent=0;
		foreach($sql_tri as $row_tri){
			$trim_amount=$row_tri[csf('amount')];
			$trim_cost_percent=($trim_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		$sql_emb=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_embe_cost_dtls where quotation_id=$update_id and emb_name!=3 and status_active=1 and is_deleted=0 group by quotation_id");
		$emb_amount=0;$emb_cost_percent=0;
		foreach($sql_emb as $row_emb){

			$emb_amount=$row_emb[csf('amount')];
			$emb_cost_percent=($emb_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		$sql_was=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_embe_cost_dtls where quotation_id=$update_id and emb_name =3 and status_active=1 and is_deleted=0 group by quotation_id");
		$wash_amount=0;$wash_cost_percent=0;
		foreach($sql_was as $row_was){
			$wash_amount=$row_was[csf('amount')];
			$wash_cost_percent=($wash_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		
		$sql_comm=sql_select("select quotation_id, sum(amount) as amount from wo_pri_quo_comarcial_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$comarcial_amount=0;$commer_cost_percent=0;
		foreach($sql_comm as $row_comm){
			$comarcial_amount=$row_comm[csf('amount')];
			$commer_cost_percent=($comarcial_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		//	echo "10**";$commer_cost_percent.'F';
			//if($commer_cost_percent) $commer_cost_percent=0;else $commer_cost_percent=$commer_cost_percent;
		}
		
		$sql_commiss=sql_select("select quotation_id, sum(commission_amount) as amount from wo_pri_quo_commiss_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$commiss_amount=0;$commiss_cost_percent=0;
		foreach($sql_commiss as $row_comm){
			$commiss_amount=$row_comm[csf('amount')];
			$commiss_cost_percent=($commiss_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		
		//echo "10**".$tot_fab_amount.'='.$fabric_cost_percent.'='.$trim_amount.'='.$trim_cost_percent;die;
		
		$currency=str_replace("'","",$cbo_currercy)*1;
		$dblTot_fa=$tot_fab_amount+$trim_amount+$emb_amount+$wash_amount+$comarcial_amount+(str_replace("'","",$txt_lab_test_pre_cost)*1)+(str_replace("'","",$txt_inspection_pre_cost)*1)+(str_replace("'","",$txt_cm_pre_cost)*1)+(str_replace("'","",$txt_freight_pre_cost)*1)+(str_replace("'","",$txt_currier_pre_cost)*1)+(str_replace("'","",$txt_certificate_pre_cost)*1)+(str_replace("'","",$txt_common_oh_pre_cost)*1)+(str_replace("'","",$txt_depr_amor_pre_cost)*1)+(str_replace("'","",$txt_interest_pre_cost)*1)+(str_replace("'","",$txt_income_tax_pre_cost)*1)+(str_replace("'","",$txt_design_pre_cost)*1)+(str_replace("'","",$txt_studio_pre_cost)*1);
		
		$dblTot_fa_cost_percent=($dblTot_fa*$confirm_price_dzn_percent)/$confirm_price_dzn;
		
		$final_cost_psc=0;
		if(str_replace("'","",$cbo_costing_per)==1) $final_cost_psc=$dblTot_fa/12;
		else if(str_replace("'","",$cbo_costing_per)==2) $final_cost_psc=$dblTot_fa/1;
		else if(str_replace("'","",$cbo_costing_per)==3) $final_cost_psc=$dblTot_fa/(2*12);
		else if(str_replace("'","",$cbo_costing_per)==4) $final_cost_psc=$dblTot_fa/(3*12);
		else if(str_replace("'","",$cbo_costing_per)==5) $final_cost_psc=$dblTot_fa/(4*12);
		$final_cost_set_pcs_rate=($final_cost_psc*$confirm_price_dzn_percent)/$confirm_price_dzn;
		
		/*if($confirm_price_dzn>0)
		{*/
			$txt_fabric_pre_cost="'".$tot_fab_amount."'"; $txt_fabric_po_price="'".$fabric_cost_percent."'";
			$txt_trim_pre_cost="'".$trim_amount."'"; $txt_trim_po_price="'".$trim_cost_percent."'";
			$txt_embel_pre_cost="'".$emb_amount."'"; $txt_embel_po_price="'".$emb_cost_percent."'";
			$txt_wash_pre_cost="'".$wash_amount."'"; $txt_wash_po_price="'".$wash_cost_percent."'";
			$txt_comml_pre_cost="'".$comarcial_amount."'"; $txt_comml_po_price="'".$commer_cost_percent."'";
			$txt_commission_pre_cost="'".$commiss_amount."'"; $txt_commission_po_price="'".$commiss_cost_percent."'";
			$txt_total_pre_cost="'".$dblTot_fa."'"; $txt_total_po_price="'".$dblTot_fa_cost_percent."'";
			
			
			$txt_cost_dzn="'".$dblTot_fa."'"; $txt_cost_dzn_po_price="'".$dblTot_fa_cost_percent."'";
			$txt_final_cost_pcs_po_price="'".$final_cost_psc."'"; $txt_final_cost_set_pcs_rate="'".$final_cost_set_pcs_rate."'";
			
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57){
				$txt_final_cost_set_pcs_rate=number_format(($final_cost_psc/(str_replace("'","",$tot_set_qnty)*1)), 4);
			}
			else if(str_replace("'","",$cbo_order_uom)==1){
				$txt_final_cost_set_pcs_rate="";
			}
			$txt_final_cost_set_pcs_rate="'".$txt_final_cost_set_pcs_rate."'";
			$asking_profit_percent=0;
			
			if((str_replace("'","",$txt_final_cost_dzn_po_price)*1)==0){
				if($db_type==0) $txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date), "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date), "yyyy-mm-dd", "-",1);

				$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$cbo_company_name  and $txt_quotation_date between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0");
				if($asking_profit=="") $asking_profit=0;
				$txt_final_cost_dzn_po_price="'".number_format($asking_profit,4)."'";

			}
			else{
				$asking_profit_percent=str_replace("'",'',$txt_final_cost_dzn_po_price);
			}
			
			$margin_method=1-($asking_profit_percent/100);
			$asking_profit=(number_format($final_cost_psc, 4)/$margin_method)-number_format($final_cost_psc, 4);
			$txt_final_cost_dzn_pre_cost="'".number_format($asking_profit,4)."'";
			$txt_asking_quoted_price="'".(str_replace("'",'',$txt_final_cost_pcs_po_price)+str_replace("'",'',$txt_final_cost_dzn_pre_cost))."'";
			$txt_1st_quoted_price_pre_cost="'".number_format((str_replace("'",'',$txt_final_cost_pcs_po_price)+str_replace("'",'',$txt_final_cost_dzn_pre_cost)),4)."'";
			$txt_1st_quoted_po_price=$txt_final_cost_dzn_po_price;

			$margin_method=1-(str_replace("'",'',$txt_1st_quoted_po_price)/100);
			$txt_1st_quoted_price_pre_cost=number_format(($final_cost_psc/$margin_method),4);
			$txt_1st_quoted_price_pre_cost="'".$txt_1st_quoted_price_pre_cost."'";

			$percent=((str_replace("'",'',$txt_1st_quoted_price_pre_cost)-$final_cost_psc)/str_replace("'",'',$txt_1st_quoted_price_pre_cost))*100;
			$txt_1st_quoted_po_price="'".$percent."'";

			$txt_margin_dzn_pre_cost=number_format((str_replace("'",'',$txt_confirm_price_pre_cost_dzn)-str_replace("'",'',$txt_total_pre_cost)), 2);
		//}

		$field_array="costing_per_id*order_uom_id*fabric_cost*fabric_cost_percent*trims_cost*trims_cost_percent*embel_cost*embel_cost_percent*wash_cost*wash_cost_percent* comm_cost*comm_cost_percent*lab_test*lab_test_percent*inspection*inspection_percent*cm_cost*cm_cost_percent*freight*freight_percent*currier_pre_cost*currier_percent*certificate_pre_cost*certificate_percent*design_pre_cost*design_percent*studio_pre_cost*studio_percent*common_oh*common_oh_percent*depr_amor_pre_cost*depr_amor_po_price*interest_pre_cost*interest_po_price*income_tax_pre_cost*income_tax_po_price*total_cost*total_cost_percent*commission*commission_percent*final_cost_dzn*final_cost_dzn_percent*final_cost_pcs*final_cost_set_pcs_rate*a1st_quoted_price*a1st_quoted_price_percent*a1st_quoted_price_date*revised_price*revised_price_date*confirm_price*confirm_price_set_pcs_rate*confirm_price_dzn* confirm_price_dzn_percent* margin_dzn*margin_dzn_percent*price_with_commn_dzn*price_with_commn_percent_dzn*price_with_commn_pcs*price_with_commn_percent_pcs* confirm_date*asking_quoted_price*asking_quoted_price_percent*terget_qty*updated_by*update_date* status_active* is_deleted ";
		$data_array="".$cbo_costing_per." * ".$cbo_order_uom." * ".$txt_fabric_pre_cost." * ".$txt_fabric_po_price." * ".$txt_trim_pre_cost." * ".$txt_trim_po_price." * ".$txt_embel_pre_cost." * ".$txt_embel_po_price." * ".$txt_wash_pre_cost." * ".$txt_wash_po_price." * ".$txt_comml_pre_cost." * ".$txt_comml_po_price." * ".$txt_lab_test_pre_cost." * ".$txt_lab_test_po_price." * ".$txt_inspection_pre_cost." * ".$txt_inspection_po_price." * ".$txt_cm_pre_cost." * ".$txt_cm_po_price." * ".$txt_freight_pre_cost." * ".$txt_freight_po_price." * ".$txt_currier_pre_cost." * ".$txt_currier_po_price." * ".$txt_certificate_pre_cost." * ".$txt_certificate_po_price." * ".$txt_design_pre_cost." * ".$txt_design_po_price." * ".$txt_studio_pre_cost." * ".$txt_studio_po_price." * ".$txt_common_oh_pre_cost." * ".$txt_common_oh_po_price." * ".$txt_depr_amor_pre_cost." * ".$txt_depr_amor_po_price." * ".$txt_interest_pre_cost." * ".$txt_interest_po_price." * ".$txt_income_tax_pre_cost." * ".$txt_income_tax_po_price." * ".$txt_total_pre_cost." * ".$txt_total_po_price." * ".$txt_commission_pre_cost." * ".$txt_commission_po_price." * ".$txt_final_cost_dzn_pre_cost." * ".$txt_final_cost_dzn_po_price." * ".$txt_final_cost_pcs_po_price." * ".$txt_final_cost_set_pcs_rate." * ".$txt_1st_quoted_price_pre_cost." * ".$txt_1st_quoted_po_price." * ".$txt_first_quoted_price_date." * ".$txt_revised_price_pre_cost." * ".$txt_revised_price_date." * ".$txt_confirm_price_pre_cost." * ".$txt_confirm_price_set_pcs_rate." * ".$txt_confirm_price_pre_cost_dzn." * ".$txt_confirm_price_po_price_dzn." * ".$txt_margin_dzn_pre_cost." * ".$txt_margin_dzn_po_price." * ".$txt_with_commission_pre_cost_dzn." * ".$txt_with_commission_po_price_dzn." * ".$txt_with_commission_pre_cost_pcs." * ".$txt_with_commission_po_price_pcs." * ".$txt_confirm_date_pre_cost." * ".$txt_asking_quoted_price." * ".$txt_asking_quoted_po_price." * ".$txt_terget_qty." * ".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
		//echo $data_array;
		//print_r($data_array);die;
		$return_component=sql_update("wo_price_quotation_costing_mst",$field_array,$data_array,"id","".$update_id_dtls."",0);
		//echo
		return $return_component.'__'.str_replace("'","",$update_id_dtls);
	}
}

if ($action=="copy_quatation")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo  $operation."_".$rID1."_".$id_costing_mst."mmmm"; die;
	if ($operation==5)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$currentdate = date("d-m-Y",time());
		$current_save_date = date("d-M-Y",time());
		$id=return_next_id( "id", "wo_price_quotation", 1 ) ;
		$field_array="id, company_id, buyer_id, style_ref, lib_style_id, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season_buyer_wise, dealing_merchant, copy_from, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_style_ref_id.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",'".$current_save_date."',".$txt_est_ship_date.",".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$cbo_season_name.",".$cbo_dealing_merchant.",".$txt_quotation_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		$field_array1="id,quotation_id, gmts_item_id,set_item_ratio,smv_pcs,smv_set";
		$add_comma=0;
		$id1=return_next_id( "id", "  wo_price_quotation_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		//echo "10**Insert into wo_price_quotation ($field_array) values $data_array"; die;
		$rID=sql_insert("wo_price_quotation",$field_array,$data_array,0);
		$rID1=sql_insert("wo_price_quotation_set_details",$field_array1,$data_array1,1);
		$id_costing_mst=save_fabric_cost($id,$txt_quotation_id);

		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57)
			{
				if($rID==1 && $rID1==1){
					mysql_query("COMMIT");
					echo "0**".$rID."**".$id."**".$id_costing_mst."**".$currentdate;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID."**".$id."**".$id_costing_mst."**".$currentdate;
				}
			}
			else if(str_replace("'","",$cbo_order_uom)==1)
			{
				if($rID==1){
					mysql_query("COMMIT");
					echo "0**".$rID."**".$id."**".$id_costing_mst."**".$currentdate;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID."**".$id."**".$id_costing_mst."**".$currentdate;
				}
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1){
					oci_commit($con);
					echo "0**".$rID."**".$id."**".$id_costing_mst."**".$currentdate;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID."**".$id."**".$id_costing_mst."**".$currentdate;
				}
		}
		disconnect($con);
		die;
	}
}

function save_fabric_cost($newid,$txt_quotation_id_old)
{
	    global $pc_date_time;
		$conversion_cost_headarr=array();
		$id=return_next_id( "id", "wo_pri_quo_fabric_cost_dtls", 1 ) ;
		$id1=return_next_id( "id", "wo_pri_quo_fab_co_avg_con_dtls", 1 ) ;
		$field_array="id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,	construction, composition,fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,fab_cons_in_quotat_varia,process_loss_method,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type";

		$field_array1="id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs,rate,amount, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";

	   $sql_data=sql_select("Select id, item_number_id,body_part_id,fab_nature_id,color_type_id,lib_yarn_count_deter_id,construction,composition,fabric_description,gsm_weight,avg_cons,fabric_source,rate,amount,avg_finish_cons,avg_process_loss,inserted_by,insert_date,status_active,is_deleted,company_id,costing_per,fab_cons_in_quotat_varia,process_loss_method,cons_breack_down,msmnt_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pri_quo_fabric_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and is_deleted=0");
	   $add_comma=0;
	   $i=1;
	foreach($sql_data as $row)
	{
		    if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$newid."','".$row[csf("item_number_id")]."','".$row[csf("body_part_id")]."','".$row[csf("fab_nature_id")]."','".$row[csf("color_type_id")]."','".$row[csf("lib_yarn_count_deter_id")]."','".$row[csf("construction")]."','".$row[csf("composition")]."','".$row[csf("fabric_description")]."','".$row[csf("gsm_weight")]."','".$row[csf("avg_cons")]."','".$row[csf("fabric_source")]."','".$row[csf("rate")]."','".$row[csf("amount")]."','".$row[csf("avg_finish_cons")]."','".$row[csf("avg_process_loss")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row[csf("status_active")]."',0,'".$row[csf("company_id")]."','".$row[csf("costing_per")]."','".$row[csf("fab_cons_in_quotat_varia")]."','".$row[csf("process_loss_method")]."','".$row[csf("cons_breack_down")]."','".$row[csf("msmnt_break_down")]."','".$row[csf("yarn_breack_down")]."','".$row[csf("marker_break_down")]."','".$row[csf("width_dia_type")]."')";

			$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs,rate,amount, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");

			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("rate")]."','".$row_cons[csf("amount")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }
			$conversion_cost_headarr[$row[csf("id")]]=$id;
			$id=$id+1;
			$i++;
	}
	    $rID1=sql_insert("wo_pri_quo_fabric_cost_dtls",$field_array,$data_array,0);
		$rID=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);

		//---Yarn Cost--------------
		 $iy=1;
		 $id_yarn=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 $field_array_yarn="id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		 $sql_data_yarn=sql_select("Select id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_yarn as $row_yarn)
	     {
			if ($iy!=1) $data_array_yarn .=",";
			$data_array_yarn .="(".$id_yarn.",".$newid.",'".$row_yarn[csf("count_id")]."','".$row_yarn[csf("copm_one_id")]."','".$row_yarn[csf("percent_one")]."','".$row_yarn[csf("copm_two_id")]."','".$row_yarn[csf("percent_two")]."','".$row_yarn[csf("type_id")]."','".$row_yarn[csf("cons_ratio")]."','".$row_yarn[csf("cons_qnty")]."','".$row_yarn[csf("rate")]."','".$row_yarn[csf("amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_yarn[csf("status_active")]."',0)";
			$id_yarn=$id_yarn+1;
			$iy++;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array_yarn,$data_array_yarn,0);
		 //---Yarn Cost End --------------

		 //---Conversion Cost--------------
		 $ifc=1;
		 $idfc=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 $field_array_fc="id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $sql_data_con=sql_select("Select id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted from wo_pri_quo_fab_conv_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_con as $row_con)
	     {
			if ($ifc!=1) $data_array_fc .=",";
			$data_array_fc .="(".$idfc.",".$newid.",'".$conversion_cost_headarr[$row_con[csf("cost_head")]]."','".$row_con[csf("cons_type")]."','".$row_con[csf("req_qnty")]."','".$row_con[csf("charge_unit")]."','".$row_con[csf("amount")]."','".$row_con[csf("charge_lib_id")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_con[csf("status_active")]."',0)";
			$idfc=$idfc+1;
			$ifc++;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array_fc,$data_array_fc,0);
		 //---Conversion Cost End --------------

		 //---Trim Cost--------------
		 $it=1;
		 $idt=return_next_id( "id", "wo_pri_quo_trim_cost_dtls", 1 ) ;
		 $field_array_t="id, quotation_id, trim_group, seq, description, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active, is_deleted";
		 $sql_data_t=sql_select("Select id, quotation_id, trim_group, seq, description, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active, is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_t as $row_t)
	     {
			if ($it!=1) $data_array_t .=",";
			$data_array_t .="(".$idt.",".$newid.",'".$row_t[csf("trim_group")]."','".$row_t[csf("seq")]."','".$row_t[csf("description")]."','".$row_t[csf("cons_uom")]."','".$row_t[csf("cons_dzn_gmts")]."','".$row_t[csf("rate")]."','".$row_t[csf("amount")]."','".$row_t[csf("apvl_req")]."','".$row_t[csf("nominated_supp")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_t[csf("status_active")]."',0)";
			$idt=$idt+1;
			$it++;
		 }
		 $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array_t,$data_array_t,0);
		 //---Trim Cost End --------------


		 //---Embelishment And Wash Cost--------------
		 $iem=1;
		 $idem=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array_em="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";

		 $sql_data_em=sql_select("Select id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_em as $row_em)
	     {
			if ($iem!=1) $data_array_em .=",";
			$data_array_em .="(".$idem.",".$newid.",'".$row_em[csf("emb_name")]."','".$row_em[csf("emb_type")]."','".$row_em[csf("cons_dzn_gmts")]."','".$row_em[csf("rate")]."','".$row_em[csf("amount")]."','".$row_em[csf("charge_lib_id")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_em[csf("status_active")]."',0)";
			$idem=$idem+1;
			$iem++;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array_em,$data_array_em,0);
		 //---Embelishment And Wash Cost End --------------

		 //---Commercial Cost--------------
		 $icmr=1;
		 $idcmr=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 $field_array_cmr="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted";

		 $sql_data_cmr=sql_select("Select id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted  from wo_pri_quo_comarcial_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_cmr as $row_cmr)
	     {
			if ($icmr!=1) $data_array_cmr .=",";
			$data_array_cmr .="(".$idcmr.",".$newid.",'".$row_cmr[csf("item_id")]."','".$row_cmr[csf("rate")]."','".$row_cmr[csf("amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_cmr[csf("status_active")]."',0)";
			$idcmr=$idcmr+1;
			$icmr++;
		 }
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array_cmr,$data_array_cmr,0);
		 //---Commercial Cost End --------------

		 //---Commision Cost--------------
		 $icms=1;
		 $idcms=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 $field_array_cms="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";

		 $sql_data_cms=sql_select("Select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted   from wo_pri_quo_commiss_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_cms as $row_cms)
	     {
			if ($icms!=1) $data_array_cms .=",";
			$data_array_cms .="(".$idcms.",".$newid.",'".$row_cms[csf("particulars_id")]."','".$row_cms[csf("commission_base_id")]."','".$row_cms[csf("commision_rate")]."','".$row_cms[csf("commission_amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_cms[csf("status_active")]."',0)";
			$idcms=$idcms+1;
			$icms++;
		 }
		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array_cms,$data_array_cms,0);
		 //---Commision Cost End --------------

		 //terget_qty, depr_amor_pre_cost, depr_amor_po_price,  interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, design_pre_cost, design_percent, studio_pre_cost, studio_percent

		 //---wo_price_quotation_costing_mst Table--------------
		 $id_costing_mst=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
		$field_array_costing_mst="id, quotation_id, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price,  interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, design_pre_cost, design_percent, studio_pre_cost, studio_percent, inserted_by, insert_date, status_active, is_deleted ";

		 $sql_data_costing_mst=sql_select("Select id, quotation_id, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, design_pre_cost, design_percent, studio_pre_cost, studio_percent, inserted_by, insert_date, status_active, is_deleted from wo_price_quotation_costing_mst where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_costing_mst as $row_costing_mst)
	     {
			$data_array_costing_mst="(".$id_costing_mst.",".$newid.",'".$row_costing_mst[csf("costing_per_id")]."','".$row_costing_mst[csf("order_uom_id")]."','".$row_costing_mst[csf("fabric_cost")]."','".$row_costing_mst[csf("fabric_cost_percent")]."','".$row_costing_mst[csf("trims_cost")]."','".$row_costing_mst[csf("trims_cost_percent")]."','".$row_costing_mst[csf("embel_cost")]."','".$row_costing_mst[csf("embel_cost_percent")]."','".$row_costing_mst[csf("wash_cost")]."','".$row_costing_mst[csf("wash_cost_percent")]."','".$row_costing_mst[csf("comm_cost")]."','".$row_costing_mst[csf("comm_cost_percent")]."','".$row_costing_mst[csf("lab_test")]."','".$row_costing_mst[csf("lab_test_percent")]."','".$row_costing_mst[csf("inspection")]."','".$row_costing_mst[csf("inspection_percent")]."','".$row_costing_mst[csf("cm_cost")]."','".$row_costing_mst[csf("cm_cost_percent")]."','".$row_costing_mst[csf("freight")]."','".$row_costing_mst[csf("freight_percent")]."','".$row_costing_mst[csf("currier_pre_cost")]."','".$row_costing_mst[csf("currier_percent")]."','".$row_costing_mst[csf("certificate_pre_cost")]."','".$row_costing_mst[csf("certificate_percent")]."','".$row_costing_mst[csf("common_oh")]."','".$row_costing_mst[csf("common_oh_percent")]."','".$row_costing_mst[csf("total_cost")]."','".$row_costing_mst[csf("total_cost_percent")]."','".$row_costing_mst[csf("commission")]."','".$row_costing_mst[csf("commission_percent")]."','".$row_costing_mst[csf("final_cost_dzn")]."','".$row_costing_mst[csf("final_cost_dzn_percent")]."','".$row_costing_mst[csf("final_cost_pcs")]."','".$row_costing_mst[csf("final_cost_set_pcs_rate")]."','".$row_costing_mst[csf("a1st_quoted_price")]."','".$row_costing_mst[csf("a1st_quoted_price_percent")]."','".$row_costing_mst[csf("a1st_quoted_price_date")]."','".$row_costing_mst[csf("revised_price")]."','".$row_costing_mst[csf("revised_price_date")]."','".$row_costing_mst[csf("confirm_price")]."','".$row_costing_mst[csf("confirm_price_set_pcs_rate")]."','".$row_costing_mst[csf("confirm_price_dzn")]."','".$row_costing_mst[csf("confirm_price_dzn_percent")]."','".$row_costing_mst[csf("margin_dzn")]."','".$row_costing_mst[csf("margin_dzn_percent")]."','".$row_costing_mst[csf("price_with_commn_dzn")]."','".$row_costing_mst[csf("price_with_commn_percent_dzn")]."','".$row_costing_mst[csf("price_with_commn_pcs")]."','".$row_costing_mst[csf("price_with_commn_percent_pcs")]."','".$row_costing_mst[csf("confirm_date")]."','".$row_costing_mst[csf("asking_quoted_price")]."','".$row_costing_mst[csf("asking_quoted_price_percent")]."','".$row_costing_mst[csf("terget_qty")]."','".$row_costing_mst[csf("depr_amor_pre_cost")]."','".$row_costing_mst[csf("depr_amor_po_price")]."','".$row_costing_mst[csf("interest_pre_cost")]."','".$row_costing_mst[csf("interest_po_price")]."','".$row_costing_mst[csf("income_tax_pre_cost")]."','".$row_costing_mst[csf("income_tax_po_price")]."','".$row_costing_mst[csf("design_pre_cost")]."','".$row_costing_mst[csf("design_percent")]."','".$row_costing_mst[csf("studio_pre_cost")]."','".$row_costing_mst[csf("studio_percent")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 }
		 $rID=sql_insert("wo_price_quotation_costing_mst",$field_array_costing_mst,$data_array_costing_mst,0);
		 //---wo_price_quotation_costing_mst Cost End --------------



		 //---wo_pri_quo_sum_dtls Table--------------
		 $id_sum=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
		$field_array_sum="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount, 	trim_cons,trim_rate,trim_amount,emb_amount,wash_amount,comar_rate,comar_amount,commis_rate,commis_amount,inserted_by,insert_date,status_active,is_deleted";

		 $sql_data_sum=sql_select("Select id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount, 	trim_cons,trim_rate,trim_amount,emb_amount,wash_amount,comar_rate,comar_amount,commis_rate,commis_amount,inserted_by,insert_date,status_active,is_deleted   from wo_pri_quo_sum_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_sum as $row_sum)
	     {
			$data_array_sum="(".$id_sum.",".$newid.",'".$row_sum[csf("fab_yarn_req_kg")]."','".$row_sum[csf("fab_woven_req_yds")]."','".$row_sum[csf("fab_knit_req_kg")]."','".$row_sum[csf("fab_amount")]."','".$row_sum[csf("yarn_cons_qnty")]."','".$row_sum[csf("yarn_amount")]."','".$row_sum[csf("conv_req_qnty")]."','".$row_sum[csf("conv_charge_unit")]."','".$row_sum[csf("conv_amount")]."','".$row_sum[csf("trim_cons")]."','".$row_sum[csf("trim_rate")]."','".$row_sum[csf("trim_amount")]."','".$row_sum[csf("emb_amount")]."','".$row_sum[csf("wash_amount")]."','".$row_sum[csf("comar_rate")]."','".$row_sum[csf("comar_amount")]."','".$row_sum[csf("commis_rate")]."','".$row_sum[csf("commis_amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 }
		 $rID=sql_insert("wo_pri_quo_sum_dtls",$field_array_sum,$data_array_sum,0);

		 return $id_costing_mst;
		 //---wo_pri_quo_sum_dtls Cost End --------------
}

//Master Table End ====================================================================================================================================================
//Dtls Table===========================================================================================================================================================
if ($action=="save_update_delete_quotation_entry_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) { $approved=1; }else{ $approved=$approved; }
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
	}
	
	if(str_replace("'","",$update_id_dtls)=="")
	{
		$sql=sql_select("select id, quotation_id from wo_price_quotation_costing_mst where quotation_id=$update_id status_active=1 and is_deleted=0");
		foreach($sql as $row){
			$update_id_dtls="'".$row[csf('id')]."'";
		}
	}

	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//txt_confirm_price_pre_cost_dzn
			$id=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
			
			$field_array="id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,a1st_quoted_price,a1st_quoted_price_percent,a1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent,terget_qty, inserted_by, insert_date, status_active, is_deleted ";
			$data_array="(".$id.",".$update_id.",".$cbo_costing_per.",".$cbo_order_uom.",".$txt_fabric_pre_cost.",".$txt_fabric_po_price.",".$txt_trim_pre_cost.",".$txt_trim_po_price.",".$txt_embel_pre_cost.",".$txt_embel_po_price.",".$txt_wash_pre_cost.",".$txt_wash_po_price.",".$txt_comml_pre_cost.",".$txt_comml_po_price.",".$txt_lab_test_pre_cost.",".$txt_lab_test_po_price.",".$txt_inspection_pre_cost.",".$txt_inspection_po_price.",".$txt_cm_pre_cost.",".$txt_cm_po_price.",".$txt_freight_pre_cost.",".$txt_freight_po_price.",".$txt_currier_pre_cost.",".$txt_currier_po_price.",".$txt_certificate_pre_cost.",".$txt_certificate_po_price.",".$txt_design_pre_cost.",".$txt_design_po_price.",".$txt_studio_pre_cost.",".$txt_studio_po_price.",".$txt_common_oh_pre_cost.",".$txt_common_oh_po_price.",".$txt_depr_amor_pre_cost.",".$txt_depr_amor_po_price.",".$txt_interest_pre_cost.",".$txt_interest_po_price.",".$txt_income_tax_pre_cost.",".$txt_income_tax_po_price.",".$txt_total_pre_cost.",".$txt_total_po_price.",".$txt_commission_pre_cost.",".$txt_commission_po_price.",".$txt_final_cost_dzn_pre_cost.",".$txt_final_cost_dzn_po_price.",".$txt_final_cost_pcs_po_price.",".$txt_final_cost_set_pcs_rate.",".$txt_1st_quoted_price_pre_cost.",".$txt_1st_quoted_po_price.",".$txt_first_quoted_price_date.",".$txt_revised_price_pre_cost.",".$txt_revised_price_date.",".$txt_confirm_price_pre_cost.",".$txt_confirm_price_set_pcs_rate.",".$txt_confirm_price_pre_cost_dzn.",".$txt_confirm_price_po_price_dzn.",".$txt_margin_dzn_pre_cost.",".$txt_margin_dzn_po_price.",".$txt_with_commission_pre_cost_dzn.",".$txt_with_commission_po_price_dzn.",".$txt_with_commission_pre_cost_pcs.",".$txt_with_commission_po_price_pcs.",".$txt_confirm_date_pre_cost.",".$txt_asking_quoted_price.",".$txt_asking_quoted_po_price.",".$txt_terget_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			$rID=sql_insert("wo_price_quotation_costing_mst",$field_array,$data_array,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");
					echo "0**".$rID."**".$id;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID."**".$id;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID ){
					oci_commit($con);
					echo "0**".$rID."**".$id;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID."**".$id;
				}
			}
			disconnect($con);
			die;
		//}
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$txt_confirm_price_quto_cost_dzn=str_replace("'","",$txt_confirm_price_pre_cost_dzn);
		$txt_confirm_price_quto_percent_dzn=str_replace("'","",$txt_confirm_price_po_price_dzn);
		$txt_revised_price_pre_cost=str_replace("'","",$txt_revised_price_pre_cost);
		if($txt_revised_price_pre_cost=''){
			$txt_revised_price_pre_cost=0;
		}
		$sql_price=sql_select("select confirm_price_dzn, confirm_price_dzn_percent from wo_price_quotation_costing_mst where quotation_id=$update_id ");
		$tot_confirm_price_dzn=0; $tot_confirm_price_dzn_percent=0;
		foreach($sql_price as $row_fab){
			$tot_confirm_price_dzn=$row_fab[csf('confirm_price_dzn')];
			$tot_confirm_price_dzn_percent=$row_fab[csf('confirm_price_dzn_percent')];
		}

		$confirm_price_dzn=($txt_confirm_price_quto_cost_dzn+$tot_confirm_price_dzn)-$tot_confirm_price_dzn;

		$confirm_price_dzn_percent=$txt_confirm_price_quto_percent_dzn;
		$sql_yar=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$yarn_amount=0;
		foreach($sql_yar as $row_yarn){
			$yarn_amount=$row_yarn[csf('amount')];
			//$fabric_cost_percent=($yarn_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}

		$sql_conv=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$conv_amount=0;
		foreach($sql_conv as $conv){
			$conv_amount=$conv[csf('amount')];
		}
		//wo_pri_quo_fab_conv_cost_dtls
		$sql_fab=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 and amount>0 group by quotation_id");
		$fab_amount=0;$fabric_cost_percent=0;
		foreach($sql_fab as $row_fab){
			$fab_amount=$row_fab[csf('amount')];
			//$fabric_cost_percent=($fab_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		$tot_fab_amount=$fab_amount+$yarn_amount+$conv_amount;
		$fabric_cost_percent=0;
		if($confirm_price_dzn>0){
			$fabric_cost_percent=($tot_fab_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		$sql_tri=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_trim_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$trim_amount=0;	$trim_cost_percent=0;
		foreach($sql_tri as $row_tri){
			$trim_amount=$row_tri[csf('amount')];
			if($confirm_price_dzn>0){
				$trim_cost_percent=($trim_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
			}
		}
		$sql_emb=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_embe_cost_dtls where quotation_id=$update_id and emb_name!=3 and status_active=1 and is_deleted=0 group by quotation_id");
		$emb_amount=0;$emb_cost_percent=0;
		foreach($sql_emb as $row_emb){
			$emb_amount=$row_emb[csf('amount')];
			if($confirm_price_dzn>0){
				$emb_cost_percent=($emb_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
			}
		}
		$sql_was=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_embe_cost_dtls where quotation_id=$update_id and emb_name =3 and status_active=1 and is_deleted=0 group by quotation_id");
		$wash_amount=0;$wash_cost_percent=0;
		foreach($sql_was as $row_was){
			$wash_amount=$row_was[csf('amount')];
			if($confirm_price_dzn>0){
				$wash_cost_percent=($wash_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
			}
			
		}
		$sql_comarcial=sql_select("select quotation_id,sum(amount) as amount from wo_pri_quo_comarcial_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$comarcial_amount=0;$comarcial_cost_percent=0;
		foreach($sql_comarcial as $row_comm){
			$comarcial_amount=$row_comm[csf('amount')];
			if($confirm_price_dzn>0){
				$comarcial_cost_percent=($comarcial_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
			}
		}

		$sql_commiss=sql_select("select quotation_id, sum(commission_amount) as amount from wo_pri_quo_commiss_cost_dtls where quotation_id=$update_id and status_active=1 and is_deleted=0 group by quotation_id");
		$commiss_amount=0;$commiss_cost_percent=0;
		foreach($sql_commiss as $row_comm){
			$commiss_amount=$row_comm[csf('amount')];
			if($confirm_price_dzn>0){
				$commiss_cost_percent=($commiss_amount*$confirm_price_dzn_percent)/$confirm_price_dzn;
			}
			
		}
		//echo "10**".$tot_fab_amount.'='.$fabric_cost_percent.'='.$trim_amount.'='.$trim_cost_percent;die;

		$currency=str_replace("'","",$cbo_currercy)*1;
		$dblTot_fa=$tot_fab_amount+$trim_amount+$emb_amount+$wash_amount+$comarcial_amount+(str_replace("'","",$txt_lab_test_pre_cost)*1)+(str_replace("'","",$txt_inspection_pre_cost)*1)+(str_replace("'","",$txt_cm_pre_cost)*1)+(str_replace("'","",$txt_freight_pre_cost)*1)+(str_replace("'","",$txt_currier_pre_cost)*1)+(str_replace("'","",$txt_certificate_pre_cost)*1)+(str_replace("'","",$txt_common_oh_pre_cost)*1)+(str_replace("'","",$txt_depr_amor_pre_cost)*1)+(str_replace("'","",$txt_interest_pre_cost)*1)+(str_replace("'","",$txt_income_tax_pre_cost)*1)+(str_replace("'","",$txt_design_pre_cost)*1)+(str_replace("'","",$txt_studio_pre_cost)*1);
		
		$dblTot_fa_cost_percent=0;
		if($confirm_price_dzn>0){
			$dblTot_fa_cost_percent=($dblTot_fa*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		

		$final_cost_psc=0;
		if(str_replace("'","",$cbo_costing_per)==1) $final_cost_psc=$dblTot_fa/12;
		else if(str_replace("'","",$cbo_costing_per)==2) $final_cost_psc=$dblTot_fa/1;
		else if(str_replace("'","",$cbo_costing_per)==3) $final_cost_psc=$dblTot_fa/(2*12);
		else if(str_replace("'","",$cbo_costing_per)==4) $final_cost_psc=$dblTot_fa/(3*12);
		else if(str_replace("'","",$cbo_costing_per)==5) $final_cost_psc=$dblTot_fa/(4*12);
		$final_cost_set_pcs_rate=0;
		if($confirm_price_dzn>0){
			$final_cost_set_pcs_rate=($final_cost_psc*$confirm_price_dzn_percent)/$confirm_price_dzn;
		}
		

		/*if($confirm_price_dzn>0)
		{*/
			$txt_fabric_pre_cost="'".$tot_fab_amount."'"; $txt_fabric_po_price="'".$fabric_cost_percent."'";
			$txt_trim_pre_cost="'".$trim_amount."'"; $txt_trim_po_price="'".$trim_cost_percent."'";
			$txt_embel_pre_cost="'".$emb_amount."'"; $txt_embel_po_price="'".$emb_cost_percent."'";
			$txt_wash_pre_cost="'".$wash_amount."'"; $txt_wash_po_price="'".$wash_cost_percent."'";
			$txt_comml_pre_cost="'".$comarcial_amount."'"; $txt_comml_po_price="'".$comarcial_cost_percent."'";
			$txt_commission_pre_cost="'".$commiss_amount."'"; $txt_commission_po_price="'".$commiss_cost_percent."'";
			$txt_total_pre_cost="'".$dblTot_fa."'"; $txt_total_po_price="'".$dblTot_fa_cost_percent."'";

			$txt_cost_dzn="'".$dblTot_fa."'"; $txt_cost_dzn_po_price="'".$dblTot_fa_cost_percent."'";
			$txt_final_cost_pcs_po_price="'".$final_cost_psc."'"; $txt_final_cost_set_pcs_rate="'".$final_cost_set_pcs_rate."'";

			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57){
				$txt_final_cost_set_pcs_rate=number_format(($final_cost_psc/(str_replace("'","",$tot_set_qnty)*1)), 4);
			}
			else if(str_replace("'","",$cbo_order_uom)==1){
				$txt_final_cost_set_pcs_rate="";
			}
			$txt_final_cost_set_pcs_rate="'".$txt_final_cost_set_pcs_rate."'";
			$asking_profit_percent=0;

			if((str_replace("'","",$txt_final_cost_dzn_po_price)*1)==0){
				if($db_type==0) $txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date), "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date), "yyyy-mm-dd", "-",1);

				$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$cbo_company_name  and $txt_quotation_date between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0");
				if($asking_profit=="") $asking_profit=0;
				$txt_final_cost_dzn_po_price="'".number_format($asking_profit,4)."'";

			}
			else{
				$asking_profit_percent=str_replace("'",'',$txt_final_cost_dzn_po_price);
			}
			$margin_method=1-($asking_profit_percent/100);
			$asking_profit=(number_format($final_cost_psc, 4)/$margin_method)-number_format($final_cost_psc, 4);
			$txt_final_cost_dzn_pre_cost="'".number_format($asking_profit,4)."'";
			$txt_asking_quoted_price="'".(str_replace("'",'',$txt_final_cost_pcs_po_price)+str_replace("'",'',$txt_final_cost_dzn_pre_cost))."'";
			$txt_1st_quoted_price_pre_cost="'".number_format((str_replace("'",'',$txt_final_cost_pcs_po_price)+str_replace("'",'',$txt_final_cost_dzn_pre_cost)),4)."'";
			$txt_1st_quoted_po_price=$txt_final_cost_dzn_po_price;

			$margin_method=1-(str_replace("'",'',$txt_1st_quoted_po_price)/100);
			$txt_1st_quoted_price_pre_cost=number_format(($final_cost_psc/$margin_method),4);
			$txt_1st_quoted_price_pre_cost="'".$txt_1st_quoted_price_pre_cost."'";

			$percent=0;
			if(str_replace("'",'',$txt_1st_quoted_price_pre_cost)>0){
				$percent=((str_replace("'",'',$txt_1st_quoted_price_pre_cost)-$final_cost_psc)/str_replace("'",'',$txt_1st_quoted_price_pre_cost))*100;
			}
			$txt_1st_quoted_po_price="'".$percent."'";

			$txt_margin_dzn_pre_cost=number_format((str_replace("'",'',$txt_confirm_price_pre_cost_dzn)-str_replace("'",'',$txt_total_pre_cost)), 2);
		//}

		if(str_replace("'",'',$update_id_dtls)!="")
		{
			$field_array="costing_per_id*order_uom_id*fabric_cost*fabric_cost_percent*trims_cost*trims_cost_percent*embel_cost*embel_cost_percent*wash_cost*wash_cost_percent*comm_cost*comm_cost_percent*lab_test*lab_test_percent*inspection*inspection_percent*cm_cost*cm_cost_percent*freight*freight_percent*currier_pre_cost*currier_percent*certificate_pre_cost*certificate_percent*design_pre_cost*design_percent*studio_pre_cost*studio_percent*common_oh*common_oh_percent*depr_amor_pre_cost*depr_amor_po_price*interest_pre_cost*interest_po_price*income_tax_pre_cost*income_tax_po_price*total_cost*total_cost_percent*commission*commission_percent*final_cost_dzn*final_cost_dzn_percent*final_cost_pcs*final_cost_set_pcs_rate*a1st_quoted_price*a1st_quoted_price_percent*a1st_quoted_price_date*revised_price*revised_price_date*confirm_price*confirm_price_set_pcs_rate*confirm_price_dzn* confirm_price_dzn_percent* margin_dzn*margin_dzn_percent*price_with_commn_dzn*price_with_commn_percent_dzn*price_with_commn_pcs*price_with_commn_percent_pcs* confirm_date*asking_quoted_price*asking_quoted_price_percent*terget_qty*updated_by* update_date* status_active* is_deleted ";
			$data_array="".$cbo_costing_per."*".$cbo_order_uom."*".$txt_fabric_pre_cost."*".$txt_fabric_po_price."*".$txt_trim_pre_cost."*".$txt_trim_po_price."*".$txt_embel_pre_cost."*".$txt_embel_po_price."*".$txt_wash_pre_cost."*".$txt_wash_po_price."*".$txt_comml_pre_cost."*".$txt_comml_po_price."*".$txt_lab_test_pre_cost."*".$txt_lab_test_po_price."*".$txt_inspection_pre_cost."*".$txt_inspection_po_price."*".$txt_cm_pre_cost."*".$txt_cm_po_price."*".$txt_freight_pre_cost."*".$txt_freight_po_price."*".$txt_currier_pre_cost."*".$txt_currier_po_price."*".$txt_certificate_pre_cost."*".$txt_certificate_po_price."*".$txt_design_pre_cost."*".$txt_design_po_price."*".$txt_studio_pre_cost."*".$txt_studio_po_price."*".$txt_common_oh_pre_cost."*".$txt_common_oh_po_price."*".$txt_depr_amor_pre_cost."*".$txt_depr_amor_po_price."*".$txt_interest_pre_cost."*".$txt_interest_po_price."*".$txt_income_tax_pre_cost."*".$txt_income_tax_po_price."*".$txt_total_pre_cost."*".$txt_total_po_price."*".$txt_commission_pre_cost."*".$txt_commission_po_price."*".$txt_final_cost_dzn_pre_cost."*".$txt_final_cost_dzn_po_price."*".$txt_final_cost_pcs_po_price."*".$txt_final_cost_set_pcs_rate."*".$txt_1st_quoted_price_pre_cost."*".$txt_1st_quoted_po_price."*".$txt_first_quoted_price_date."*".$txt_revised_price_pre_cost."*".$txt_revised_price_date."*".$txt_confirm_price_pre_cost."*".$txt_confirm_price_set_pcs_rate."*".$txt_confirm_price_pre_cost_dzn."*".$txt_confirm_price_po_price_dzn."*".$txt_margin_dzn_pre_cost."*".$txt_margin_dzn_po_price."*".$txt_with_commission_pre_cost_dzn."*".$txt_with_commission_po_price_dzn."*".$txt_with_commission_pre_cost_pcs."*".$txt_with_commission_po_price_pcs."*".$txt_confirm_date_pre_cost."*".$txt_asking_quoted_price."*".$txt_asking_quoted_po_price."*".$txt_terget_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
			//print_r($data_array);die;
			$rID=sql_update("wo_price_quotation_costing_mst",$field_array,$data_array,"id","".$update_id_dtls."",1);
		}	

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$rID."**".str_replace("'","",$update_id_dtls);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID."**".str_replace("'","",$update_id_dtls);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$rID."**".str_replace("'","",$update_id_dtls);
			}
			else{
				oci_rollback($con);
				echo "10**".$rID."**".str_replace("'","",$update_id_dtls);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Update Here
	{
		/*if (is_duplicate_field( "b.tag_company", "lib_buyer a, lib_buyer_tag_company b", "a.id=b.buyer_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "b.tag_company", "lib_supplier a, lib_supplier_tag_company b", "a.id=b.supplier_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)

		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_location", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_profit_center", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_prod_floor", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_standard_cm_entry", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}*/

		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

			$rID=sql_delete("wo_price_quotation",$field_array,$data_array,"id","".$update_id."",1);

			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");
					echo "2**".$rID;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID ){
					oci_commit($con);
					echo "2**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		//}
	}
}

//Dtls Table End=======================================================================================================================================================
// Fabric Cost=========================================================================================================================================================
if ($action=="show_fabric_cost_listview")
{
	$data=explode("_",$data);
	?>
       <h3 align="left" class="accordion_h" onClick="show_hide_content('fabric_cost', '')"> +Fabric Cost </h3>
       <div id="content_fabric_cost" style="display:none;">
    	<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" />
            	<table width="1500" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="100">Gmts Item</th>
                            <th width="80">Body Part</th>
                            <th width="60">Body Part Type</th>
                            <th width="90">Fab Nature</th>
                            <th width="90">Color Type</th>
                            <th width="220">Fabric Description</th>
                            <th width="80">Fabric Code</th>
                            <th width="90">Fabric Source</th>
                            <th width="60" id="">Width/Dia Type</th>
                            <th width="75" id="gsmweight_caption">GSM/ Weight</th>
                            <th width="100">Consumption Basis</th>
                            <th width="75">Fabric Cons</th>
                           
                            <th width="73">Rate</th>
                            <th width="90">Amount</th>
                            <th width="95">Status</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$gmts_item_id=return_field_value("gmts_item_id", "wo_price_quotation", "id='$data[0]'");
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data[0]'");
					$body_part_type_arr=return_library_array("select id, body_part_type from lib_body_part","id","body_part_type");
					if($approved==3){
						$approved=1;
					}
					if($approved==1) $disabled=1; else $disabled=0;

					$data_array=sql_select("select id, quotation_id, item_number_id, body_part_id, body_part_type, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, fab_cons_in_quotat_varia, status_active, cons_breack_down, msmnt_break_down, yarn_breack_down, marker_break_down, width_dia_type,yarn_color_id,yarn_code from wo_pri_quo_fabric_cost_dtls where quotation_id='$data[0]' and status_active=1 and is_deleted=0");
					if (count($data_array)>0)
					{
						$yarn_det_ids=array();
						foreach( $data_array as $row )
						{
							array_push($yarn_det_ids, $row[csf('lib_yarn_count_deter_id')]);
						}
						$yarn_cond=where_con_using_array($yarn_det_ids,0,"id");
						$yarn_system_arr=return_library_array("select id, system_no from lib_yarn_count_determina_mst","id","system_no");

						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							if($row[csf("body_part_type")]==0) $row[csf("body_part_type")]=$body_part_type_arr[$row[csf("body_part_id")]];
							?>
                            	<tr id="fabriccosttbltr_<? echo $i; ?>" align="center">
                                    <td><?  echo create_drop_down( "cbogmtsitem_".$i, 95, $garments_item,"", 1, "-- Select Item --", $row[csf("item_number_id")], "",$disabled,$gmts_item_id ); ?></td>
                                    <td>
                                        <input type="text" id="txtbodyparttext_<? echo $i; ?>" name="txtbodyparttext_<? echo $i; ?>" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(<? echo $i; ?>)" value="<? echo $body_part[$row[csf("body_part_id")]]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>  title="<? echo $row[csf("body_part_id")]; ?>" readonly/>
                                        <input type="hidden" id="txtbodypart_<? echo $i; ?>" name="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:60px" value="<? echo $row[csf("body_part_id")]; ?>" readonly/>
                                    </td>
                                    <td><? echo create_drop_down( "txtbodyparttype_".$i, 60, $body_part_type, "", 1, "-Display-", $row[csf("body_part_type")], "",1,"" );  ?></td>
                                    <td><? echo create_drop_down( "cbofabricnature_".$i, 80, $item_category,"", 0, "", $row[csf("fab_nature_id")], "change_caption( this.value, 'gsmweight_caption' );",$disabled,"2,3" ); ?></td>
                                    <td><? echo create_drop_down( "cbocolortype_".$i, 80, $color_type,"", 1, "-- Select --", $row[csf("color_type_id")], "",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="hidden" id="libyarncountdeterminationid_<? echo $i; ?>"  name="libyarncountdeterminationid_<? echo $i; ?>" value="<? echo $row[csf("lib_yarn_count_deter_id")];  ?>"  />
                                    <input type="hidden" id="oldlibyarncountdeterminationid_<? echo $i; ?>"  name="oldlibyarncountdeterminationid_<? echo $i; ?>" value="<? echo $row[csf("lib_yarn_count_deter_id")];  ?>"  />
                                    <input type="hidden" id="txtconstruction_<? echo $i; ?>"  name="txtconstruction_<? echo $i; ?>" value="<? echo $row[csf("construction")];  ?>" />
                                    <input type="hidden" id="txtcomposition_<? echo $i; ?>"    name="txtcomposition_<? echo $i; ?>" value="<? echo $row[csf("composition")];  ?>"/>

                                    <input type="text" id="fabricdescription_<? echo $i; ?>"    name="fabricdescription_<? echo $i; ?>"  class="text_boxes" style="width:220px" onDblClick="open_fabric_decription_popup(<? echo $i; ?>)" value="<? echo $row[csf("fabric_description")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>   title="<? echo $row[csf("fabric_description")];  ?>" readonly/>
                                    </td>
                                    <td>
                                    	 <input type="text" id="cbofabriccode_<? echo $i; ?>"    name="cbofabriccode_<? echo $i; ?>"  class="text_boxes" style="width:80px"  value="<? echo $yarn_system_arr[$row[csf("lib_yarn_count_deter_id")]];  ?>"  readonly/>
                                    </td>
                                    <td>
									 <?

									 echo create_drop_down( "cbofabricsource_".$i, 80, $fabric_source, "", 0, "", $row[csf("fabric_source")], "enable_disable( this.value,'txtrate_*txtamount_', $i );",$disabled,"" );
									 ?>
                                     </td>
                                     <td>
                                     <?  echo create_drop_down( "cbowidthdiatype_".$i, 100, $fabric_typee,"", 1, "-- Select --", $row[csf("width_dia_type")], "",$disabled,"" ); ?>

                                    </td>
                                    <td>
                                    <input type="text" id="txtgsmweight_<? echo $i; ?>" name="txtgsmweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onBlur="sum_yarn_required()" value="<? echo $row[csf("gsm_weight")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                   <?
									echo create_drop_down( "consumptionbasis_".$i, 100, $consumtion_basis,'', 0, '', $row[csf('fab_cons_in_quotat_varia')], "","","" );
								   ?>
                                   </td>
                                    <td>
                                    <input type="text" id="txtconsumption_<? echo $i; ?>" name="txtconsumption_<? echo $row[csf("id")]; ?>" onBlur="math_operation( 'txtamount_<? echo $i; ?>', 'txtconsumption_<? echo $i; ?>*txtrate_<? echo $i; ?>', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required();set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "  onClick="open_consumption_popup('requires/quotation_entry_controller_v2.php?action=consumption_popup', 'Consumption Entry Form','txtbodypart_<? echo $i; ?>','cbofabricnature_<? echo $i; ?>','txtgsmweight_<? echo $i; ?>','<? echo $i; ?>','updateid_<? echo $i; ?>')"   class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_cons")]; ?>" readonly/>
                                     </td>

                                    

                                    <td>
                                    <input type="text" id="txtrate_<? echo $i; ?>" name="txtrate_<? echo $i; ?>" onBlur="math_operation( 'txtamount_<? echo $i; ?>', 'txtconsumption_<? echo $i; ?>*txtrate_<? echo $i; ?>', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "   class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("rate")]; ?>" <? if($row[csf("fabric_source")]==2 ){echo "";}else{echo "disabled";}?>  />
                                    </td>
                                    <td>
                                    <input type="text" id="txtamount_<? echo $i; ?>"  onBlur="set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "  readonly  name="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")];  ?> " <? if($row[csf("fabric_source")]==2 || $disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>

                                    <td width="95"><? echo create_drop_down( "cbostatus_".$i, 80, $row_status, "", 0, "", $row[csf("status_active")], "",$disabled,"" );  ?></td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_fabric_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="txtfinishconsumption_<? echo $i; ?>"  name="txtfinishconsumption_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_finish_cons")]; ?>" readonly/>
                                    <input type="hidden" id="txtavgprocessloss_<? echo $i; ?>"  name="txtavgprocessloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_process_loss")]; ?>" readonly/>

                                     <input type="hidden" id="consbreckdown_<? echo $i; ?>" name="consbreckdown_<? echo $i; ?>"   class="text_boxes" style="width:90px" value="<? echo $row[csf("cons_breack_down")]; ?>" />
                                    <input type="hidden" id="msmntbreackdown_<? echo $i; ?>" name="msmntbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("msmnt_break_down")]; ?>" />
                                     <input type="hidden" id="markerbreackdown_<? echo $i; ?>" name="markerbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("marker_break_down")]; ?>" />
                                    <input type="hidden" id="yarnbreackdown_<? echo $i; ?>" name="yarnbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("yarn_breack_down")]; ?>" />
                                    <input type="hidden" id="processlossmethod_<? echo $i; ?>" name="processlossmethod_<? echo $i; ?>"/>

                                    <input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                                     </td>
                                </tr>

                            <?

						}
					}
					else
					{
						$selected_item=0;
						$gmts_item_id_arr=explode(",",$gmts_item_id);
						if(count($gmts_item_id_arr)==1)
						{
							$selected_item=	$gmts_item_id;
						}
						?>
						<tr id="fabriccosttbltr_1" align="center">
                            <td><?  echo create_drop_down( "cbogmtsitem_1", 95, $garments_item,"", 1, "-- Select Item --", $selected_item, "" ,"",$gmts_item_id); ?></td>
                            <td><input type="text" id="txtbodyparttext_1" name="txtbodyparttext_1" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(1);"  placeholder="DblClick" readonly/>

                            	<input type="hidden" id="txtbodypart_1" name="txtbodypart_1" class="text_boxes" style="width:60px" value="" title="" readonly/>
                            </td>
                            <td><? echo create_drop_down("txtbodyparttype_1", 60, $body_part_type, "", 1, "-Display-", "", "",1,"" );  ?></td>
                            <td><? echo create_drop_down("cbofabricnature_1", 80, $item_category,"", 0, "", 2, "change_caption( this.value, 'gsmweight_caption' )","","2,3" ); ?></td>
                            <td><? echo create_drop_down("cbocolortype_1", 80, $color_type,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                            <td>
	                            <input type="hidden" id="libyarncountdeterminationid_1" name="libyarncountdeterminationid_1" />
	                            <input type="hidden" id="oldlibyarncountdeterminationid_1" name="oldlibyarncountdeterminationid_1" />
	                            <input type="hidden" id="txtconstruction_1" name="txtconstruction_1">
	                            <input type="hidden" id="txtcomposition_1" value=""  name="txtcomposition_1" >
	                            <input type="text" id="fabricdescription_1" placeholder="Dobule Click To Search"  name="fabricdescription_1"  class="text_boxes" style="width:220px" onDblClick="open_fabric_decription_popup(1)" readonly />
                            </td>
                            <td>
                                <input type="text" id="cbofabriccode_1"    name="cbofabriccode_1"  class="text_boxes" style="width:80px"  value=""  readonly/>
                            </td>
                             <td><? echo create_drop_down( "cbofabricsource_1", 80, $fabric_source, "", 0, "", "", "enable_disable( this.value,'txtrate_*txtamount_', 1 );","","" );  ?></td>
                             <td><?  echo create_drop_down( "cbowidthdiatype_1", 100, $fabric_typee,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                            <td><input type="text" id="txtgsmweight_1" name="txtgsmweight_1" class="text_boxes_numeric" style="width:60px" onBlur="sum_yarn_required()"> </td>
                            <td>
                           <?
                            echo create_drop_down( "consumptionbasis_1", 100, $consumtion_basis,'', 0, '', $row[csf('fab_cons_in_quotat_varia')], "","","" );
                           ?>
                           </td>
                            <td>
                            <input type="text" id="txtconsumption_1" name="txtconsumption_1" onBlur="math_operation( 'txtamount_1', 'txtconsumption_1*txtrate_1', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required();set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' )" onClick="open_consumption_popup('requires/quotation_entry_controller_v2.php?action=consumption_popup', 'Consumption Entry Form','txtbodypart_1','cbofabricnature_1','txtgsmweight_1','1','updateid_1')"  value=""  class="text_boxes_numeric" style="width:60px" readonly  />
                            </td>
                            
                            <td><input type="text" id="txtrate_1" onBlur="math_operation( 'txtamount_1', 'txtconsumption_1*txtrate_1', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_' ,'tbl_fabric_cost') "  name="txtrate_1" class="text_boxes_numeric" style="width:60px" disabled /> </td>
                            <td><input type="text" id="txtamount_1"  onBlur="set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) " readonly  name="txtamount_1" class="text_boxes_numeric" style="width:80px" disabled /></td>

                            <td width="95"><? echo create_drop_down( "cbostatus_1", 80, $row_status, "", 0, "", "", "","","" );  ?></td>
                            <td>
                            <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                            <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_fabric_cost');" />
                            <input type="hidden" id="txtfinishconsumption_1"  name="txtfinishconsumption_1" class="text_boxes_numeric" style="width:60px" readonly/>
                            <input type="hidden" id="txtavgprocessloss_1"  name="txtavgprocessloss_1" class="text_boxes_numeric" style="width:60px"  readonly/>
                            <input type="hidden" id="consbreckdown_1" name="consbreckdown_1" value="" class="text_boxes" style="width:90px" />
                            <input type="hidden" id="yarnbreackdown_1" name="yarnbreackdown_1" value="" class="text_boxes" style="width:90px" />
                            <input type="hidden" id="markerbreackdown_1" name="markerbreackdown_1" value="" class="text_boxes" style="width:90px" />
                            <input type="hidden" id="msmntbreackdown_1" name="msmntbreackdown_1" value="" class="text_boxes" style="width:90px" />
                            <input type="hidden" id="processlossmethod_1" name="processlossmethod_1"/>
                            <input type="hidden" id="updateid_1" name="updateid_1" value="" class="text_boxes" style="width:20px" />
                            </td>
                        </tr>
                    <? } ?>
                </tbody>
                </table>
                <table width="1500" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th>&nbsp;</th>
                        </tr>
                    	<tr>
                        	<th  width="201">Yarn Req(Kg):<input type="text" id="tot_yarn_needed" name="tot_yarn_needed" class="text_boxes_numeric" style="width:75px;" readonly/>
							</th>
                            <th  width="415">
                            Woven Fabric Req. (Yds):<input type="text" id="txtwoven_sum" name="txtwoven_sum" class="text_boxes_numeric" style="width:95px" readonly>
                            </th>

                            <th width="225">Knit Fabric Req. (Kg):<input type="text" id="txtknit_sum"  name="txtknit_sum" class="text_boxes_numeric" style="width:60px" readonly></th>

                            <th colspan="4" width="150"></th>
                            <th width="90"><input type="text" id="txtamount_sum"    name="txtamount_sum" class="text_boxes_numeric" style="width:80px" readonly></th>
                           
                            <th width="95"></th>
                            <th width="35"></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="1500" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_cost_dtls", 1,0,"reset_form('fabriccost_3','','','cbofabricnature_,3,$i')",3) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_cost_dtls", 0,0,"reset_form('fabriccost_3','','','cbofabricnature_,3,$i')",3) ;
						}
						?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>

       <h3 align="left" id="accordion_h_yarn" class="accordion_h" onClick="show_hide_content('yarn_cost', '');sum_yarn_required()">+Yarn Cost &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Yarn Needed:&nbsp;<span id="tot_yarn_needed_span"></span></h3>
       <div id="content_yarn_cost" style="display:none;">
    	<fieldset>
        	<form id="yarnccost_4" autocomplete="off">
            	<table width="1400" cellspacing="0" class="rpt_table" border="0" id="tbl_yarn_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="100">Count</th>
                        	<th  width="100">Comp 1</th>
                        	<th  width="80">Yarn Id</th>
                        	<th  width="90">%</th>
                        	<th width="110">Type</th>
                        	<th width="100">Yarn Finish</th>
                        	<th width="100">Yarn Spinning System</th>
                        	<th width="100">Certification</th>
                        	<th width="75">Cons Ratio</th>
                        	<th width="75">Cons Qnty</th>
                        	<th width="75">Supplier</th>

							<th width="80">Yarn Color</th>
							<th width="120">Yarn Code</th>
                        	<th width="73">Rate</th>
                        	<th width="90">Amount</th>
                        	<th width="95">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, supplier_id, rate, amount,status_active,yarn_finish,yarn_spinning_system,certification,yarn_id,yarn_color_id,yarn_code from wo_pri_quo_fab_yarn_cost_dtls where quotation_id='$data[0]' and status_active=1 and is_deleted=0");// quotation_id='$data'
					$total_yarn_cost = 0;
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$total_yarn_cost += $row[csf("amount")];
							?>
                            	<tr id="yarncost_1" align="center">
                                    <td>
									<?
									echo create_drop_down( "cbocount_".$i, 95, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $row[csf("count_id")], "",$disabled,"" );
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocompone_".$i, 80, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "control_composition($i,this.id,'percent_one')",$disabled,"" ); ?></td>
                                    <td>
                                    	 <input type="text" id="cboyarnid_<? echo $i; ?>"  name="cboyarnid_<? echo $i; ?>" class="text_boxes" style="width:80px"  value="<?=$row[csf('yarn_id')]?>"   readonly />
                                    </td>
                                   <td>
                                    <input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:80px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $row[csf("percent_one")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                                    </td>

                                    <td><?  echo create_drop_down( "cbotype_".$i, 80, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"" ); ?></td>
                                    <td><?  echo create_drop_down( "cboyarnfinish_".$i, 80, $yarn_finish_arr,"", 1, "-- Select --", $row[csf("yarn_finish")], "",$disabled,"" ); ?></td>
                                    <td><?  echo create_drop_down( "cboyarnsnippingsystem_".$i, 80, $yarn_spinning_system_arr,"", 1, "-- Select --", $row[csf("yarn_spinning_system")], "",$disabled,"" ); ?></td>
                                    <td><? echo create_drop_down( "cbocertification_".$i, 80, $certification_arr,"", 1, "-- Select --", $row[csf("certification")], '','','','','' ); ?></td>
                                    <td>
                                    

                                    <input type="text" id="consratio_<? echo $i; ?>" name="consratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_<? echo $i;?>','consqnty_<? echo $i; ?>','txtrateyarn_<? echo $i; ?>','txtamountyarn_<? echo $i; ?>','calculate_consumption')"  value="<? echo $row[csf("cons_ratio")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_<? echo $i;?>','consqnty_<? echo $i; ?>','txtrateyarn_<? echo $i; ?>','txtamountyarn_<? echo $i; ?>','calculate_ratio')" value="<? echo $row[csf("cons_qnty")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly/>
                                     </td>
                                    <td>
										<?
										echo create_drop_down( "supplier_".$i, 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data[1]  and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name",1," -- Select --", $row[csf("supplier_id")], "set_yarn_rate($i)",'','' );
										?>
									</td>

									 <td><? echo create_drop_down( "cboyarncolor_".$i, 80, "Select id,color_name from lib_color where status_active=1 and is_deleted=0 group by id,color_name","id,color_name", 1, "-- Select --", $row[csf("yarn_color_id")], '','','','','' ); ?></td>

									<td><input type="text" id="txtyarncode_<?=$i?>"  name="txtyarncode_<?=$i?>"  class="text_boxes" style="width:120px" value="<?=$row[csf("yarn_code")]?>" placeholder="Yarn Code" /> </td>


                                    <td>
                                    <input type="text" id="txtrateyarn_<? echo $i; ?>" name="txtrateyarn_1<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_<? echo $i;?>','consqnty_<? echo $i; ?>','txtrateyarn_<? echo $i; ?>','txtamountyarn_<? echo $i; ?>','calculate_amount')" value="<? echo $row[csf("rate")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountyarn_<? echo $i; ?>"  name="txtamountyarn_1<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")]; ?>"  readonly/>
                                    </td>

                                    <td width="95"><? echo create_drop_down( "cbostatusyarn_".$i, 80, $row_status, "", 0, "", $row[csf("status_active")], "",$disabled,"" );  ?>
                                    	<input type="hidden" id="updateidyarncost_<? echo $i; ?>" name="updateidyarncost_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                                    </td>
                                    <!-- <td>
                                    <input type="button" id="increaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_yarn_cost' );"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    
                                     </td> -->
                                </tr>

                            <?

						}
					}
					else
					{
					?>
                    <tr id="yarncost_1" align="center">
                       <td>
						<?
						echo create_drop_down( "cbocount_1", 95, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Item --", '', '','','' );
						?>
                        </td>
                        <td><?  echo create_drop_down( "cbocompone_1", 80, $composition,"", 1, "-- Select --", '', "control_composition(1,this.id,'comp_one')",'','' ); ?></td>
                         <td>
                                <input type="text" id="cboyarnid_<? echo $i; ?>"  name="cboyarnid_1" class="text_boxes" style="width:80px"  value=""   readonly />
                         </td>
                       <td>
                        <input type="text" id="percentone_1"  name="percentone_1" class="text_boxes" style="width:80px" onChange="control_composition(1,this.id,'percent_one')" value="" readonly/>
                        </td>

                        <td><?  echo create_drop_down( "cbotype_1", 80, $yarn_type,"", 1, "-- Select --", '', '','','' ); ?></td>
                        <td><?  echo create_drop_down( "cboyarnfinish_1", 80, $yarn_finish_arr,"", 1, "-- Select --", '', '','','' ); ?></td>
                        <td><?  echo create_drop_down( "cboyarnsnippingsystem_1", 80, $yarn_spinning_system_arr,"", 1, "-- Select --", '', '','','' ); ?></td>
                        <td><? echo create_drop_down( "cbocertification_1", 80, $certification_arr,"", 1, "-- Select --", '', '','','','','' ); ?></td>
                        <td>
                        <input type="text" id="consratio_1" name="consratio_1" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_1','consqnty_1','txtrateyarn_1','txtamountyarn_1','calculate_consumption')" value="" readonly>
                        </td>
                        <td>
                        <input type="text" id="consqnty_1" name="consqnty_1" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_1','consqnty_1','txtrateyarn_1','txtamountyarn_1','calculate_ratio')" value="" readonly/>
                        </td>
                       	<td>
							<?
							echo create_drop_down( "supplier_1", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data[1]  and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name",1," -- Select --", "", "set_yarn_rate(1)",'','' );
							?>
						</td>
						 <td><? echo create_drop_down( "cboyarncolor_1", 80, "Select id,color_name from lib_color where status_active=1 and is_deleted=0 group by id,color_name","id,color_name", 1, "-- Select --", '', '','','','','' ); ?></td>
                         <td>
                          <input type="text" id="txtyarncode_1"  name="txtyarncode_1"  class="text_boxes" style="width:120px" value="" placeholder="Yarn Code" />
                         </td>

                        <td>
                        <input type="text" id="txtrateyarn_1"  name="txtrateyarn_1" class="text_boxes_numeric" style="width:60px" onChange="calculate_yarn_consumption_ratio('consratio_1','consqnty_1','txtrateyarn_1','txtamountyarn_1','calculate_amount')" value="" />
                        </td>
                        <td>
                        <input type="text" id="txtamountyarn_1" name="txtamountyarn_1" class="text_boxes_numeric" style="width:80px" value=""  readonly/>
                        </td>

                        <td width="95"><? echo create_drop_down( "cbostatusyarn_1", 80, $row_status,"", 0, "0", '', '','','' );  ?>
                        	<input type="hidden" id="updateidyarncost_1" name="updateidyarncost_1"  class="text_boxes" style="width:20px" value=""  />   
                        </td>                     
                        <!-- <input type="button" id="increaseyarn_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(1)" />
                        <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_yarn_cost' );" /> -->              
                        
						</tr>
                    <? } ?>
                </tbody>
                </table>
                <table width="1400" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                    		<th width="100"></th>
                        	<th  width="90"></th>
                        	<th  width="80"></th>
                        	<th  width="90"></th>
                        	<th width="60"></th>
                        	<th width="80"></th>
                        	<th width="50"></th>
                        	<th width="50">SUM</th>
                        
                    		
                        	
                            <th width="60"><input type="text" id="txtconsratio_sum" name="txtconsratio_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="60"><input type="text" id="txtconsumptionyarn_sum" name="txtconsumptionyarn_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="75"></th>

							<th width="80"></th>
							<th width="120"></th>
                        	<th width="100"></th>
                        	<th width="80"><input type="text" id="txtamountyarn_sum" name="txtamountyarn_sum" class="text_boxes_numeric" style="width:80px" readonly value="<? echo $total_yarn_cost; ?>"></th>
                        	<th width="30"></th>
                    		
                    		

                           
                        </tr>
                    </tfoot>
                </table>

                <table width="1300" cellspacing="0" class="" border="0">

                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						<?
						if (count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_yarn_cost_dtls", 1,0,"reset_form('yarnccost_4','','',0)",4) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_yarn_cost_dtls", 0,0,"reset_form('yarnccost_4','','',0)",4) ;
						}
						?>
                        </td>
                    </tr>
                </table>


            </form>
        </fieldset>
        </div>
       <h3 align="left" id="accordion_h_conversion" class="accordion_h" onClick="show_hide_content('conversion_cost', '')">+Conversion Cost</h3>
       <div id="content_conversion_cost" style="display:none;" align="left">
    	<fieldset>
        	<form id="conversionccost_5" autocomplete="off">
            	<table width="965" cellspacing="0" class="rpt_table" border="0" id="tbl_conversion_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="380">Fabric Description</th><th  width="155">Process</th><th width="50">Process Loss</th><th  width="50">Req. Qnty</th><th width="50">Charge/ Unit</th><th width="80">Amount</th><th width="80">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data[1]'  and variable_list=21 and status_active=1 and is_deleted=0");
					if($conversion_from_chart=="")
					{
						$conversion_from_chart=2;
					}

					//echo $conversion_from_chart;
					$fab_description=array();
					$fab_description_array=sql_select("select id, body_part_id, color_type_id,construction,composition, lib_yarn_count_deter_id from wo_pri_quo_fabric_cost_dtls where quotation_id='$data[0]' and fabric_source in(1,2) and status_active=1 and is_deleted=0");
					foreach( $fab_description_array as $row_fab_description_array )
					{
						$fab_description[$row_fab_description_array[csf("id")]]=	$body_part[$row_fab_description_array[csf("body_part_id")]].', '.$color_type[$row_fab_description_array[csf("color_type_id")]].', '.$row_fab_description_array[csf("construction")].', '.$row_fab_description_array[csf("composition")];
						$yarn_count_id_arr[$row_fab_description_array[csf("id")]] = $row_fab_description_array[csf("lib_yarn_count_deter_id")];
					}
					$fabric_determination_id = implode(",", $yarn_count_id_arr);
					$process_loss_arr = sql_select("SELECT process_id as PROCESS_ID, process_loss as PROCESS_LOSS, rate as RATE, mst_id as MST_ID from conversion_process_loss where mst_id in ($fabric_determination_id) and status_active=1 and is_deleted=0 and rate <>0");
					$process_loss_data_arr =array();
					foreach ($process_loss_arr as $row) {
						$process_loss_data_arr[$row['MST_ID']][$row['PROCESS_ID']]['PROCESS_ID'] = $row['PROCESS_ID'];
						$process_loss_data_arr[$row['MST_ID']][$row['PROCESS_ID']]['PROCESS_LOSS'] = $row['PROCESS_LOSS'];
						$process_loss_data_arr[$row['MST_ID']][$row['PROCESS_ID']]['RATE'] = $row['RATE'];
					}

					$data_array=sql_select("select id, quotation_id, cost_head, cons_type,process_loss, req_qnty, charge_unit, amount,charge_lib_id, status_active from  wo_pri_quo_fab_conv_cost_dtls where quotation_id='$data[0]'  and status_active=1 and is_deleted=0");
					$total_conversion_amount = 0;
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$total_conversion_amount += $row[csf('amount')];
							$onclick="";
							if($conversion_from_chart==1)
							{
							$onclick="set_conversion_charge_unit_pop_up(".$i.")";
							}
							?>
                            	<tr id="conversion_1" align="center">
                                    <td>
									<?
									echo create_drop_down( "cbocosthead_".$i, 380, $fab_description, "",1," -- All Fabrics --", $row[csf("cost_head")], "set_conversion_qnty(".$i.")",$disabled,"" );
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbotypeconversion_".$i, 155, $conversion_cost_head_array,"", 1, "-- Select --", $row[csf("cons_type")], "set_conversion_charge_unit(".$i.",".$conversion_from_chart.")",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="text" id="txtprocessloss_<? echo $i; ?>"  name="txtprocessloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf("process_loss")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                                    </td>
                                   <td>
                                    <input type="text" id="txtreqqnty_<? echo $i; ?>"  name="txtreqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )"  value="<? echo $row[csf("req_qnty")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>
                                   <td>
                                    <input type="text" id="txtchargeunit_<? echo $i; ?>"  name="txtchargeunit_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )"  value="<? echo $row[csf("charge_unit")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> onClick="<? if($conversion_from_chart==1){ echo "set_conversion_charge_unit_pop_up('".$i."')";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountconversion_<? echo $i; ?>"  name="txtamountconversion_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")];  ?>"  readonly/>
                                    </td>

                                    <td><? echo create_drop_down( "cbostatusconversion_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>
                                    <td>
                                    <input type="button" id="increaseconversion_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreaseconversion_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_conversion_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="updateidcoversion_<? echo $i; ?>" name="updateidcoversion_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  readonly />
                                    <input type="hidden" id="coversionchargelibraryid_<? echo $i; ?>" name="coversionchargelibraryid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("charge_lib_id")]; ?>"   readonly />
                                     </td>
                                </tr>
                            <?
						}
					}
					else
					{
						$i=1;
						
						foreach ($yarn_count_id_arr as $fab_id => $yarn_id) 
						{
							if(count($process_loss_data_arr[$yarn_id])>0)
							{
								foreach ($process_loss_data_arr[$yarn_id] as $process_id=>$process_data) 
								{

									if($process_id !=30)
									{
										$avg_cons = $fabric_cost_data_arr[$fab_id]['avg_cons'];
										$processloss = $process_data['PROCESS_LOSS'];
										$avg_cons_yarn=$avg_cons-($avg_cons*$processloss)/100;
										if($avg_cons_yarn == '')
										{
											$avg_cons_yarn = $avg_cons;
										}
									}
									?>
									<tr id="conversion_<?= $i ?>" align="center">
			                           <td><? echo create_drop_down( "cbocosthead_".$i, 380, $fab_description, "",1," -- All Fabrics --", $fab_id, "set_conversion_qnty(<?= $i ?>)","","" );
			                            ?>
			                            </td>
			                            <td><?  echo create_drop_down( "cbotypeconversion_".$i, 155, $conversion_cost_head_array,"", 1, "-- Select --", $process_id, "set_conversion_charge_unit(<?= $i ?>,".$conversion_from_chart.")","","" ); ?></td>
			                             <td>
			                            <input type="text" id="txtprocessloss_<?= $i ?>"  name="txtprocessloss_<?= $i ?>" class="text_boxes_numeric" style="width:50px; background: grey none repeat scroll 0% 0%;"  value="<?= $process_data['PROCESS_LOSS'] ?>" title="Process Loss Found In Library" readonly  />
			                            </td>
			                           <td>
			                            <input type="text" id="txtreqqnty_<?= $i ?>"  name="txtreqqnty_<?= $i ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost(<?= $i ?>)" value="" />
			                            </td>
			                           <td>
			                            <input type="text" id="txtchargeunit_<?= $i ?>"  name="txtchargeunit_<?= $i ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost(<?= $i ?>)" value="<?=$process_data['RATE']?>" onClick="<? if($conversion_from_chart==1){ echo "set_conversion_charge_unit_pop_up(<?= $i ?>)";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?> />
			                            </td>
			                            <td>
			                            <input type="text" id="txtamountconversion_<?= $i ?>"  name="txtamountconversion_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="" readonly />
			                            </td>

			                            <td><? echo create_drop_down( "cbostatusconversion_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
			                            <td>
			                            <input type="button" id="increaseconversion_<?= $i ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(<?= $i ?>,<? echo $conversion_from_chart; ?>)" />
			                            <input type="button" id="decreaseconversion_<?= $i ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?= $i ?>,'tbl_conversion_cost' );" />
			                            <input type="hidden" id="updateidcoversion_<?= $i ?>" name="updateidcoversion_<?= $i ?>"  class="text_boxes" style="width:20px" value="" readonly  />
			                            <input type="hidden" id="coversionchargelibraryid_<?= $i ?>" name="coversionchargelibraryid_<?= $i ?>"  class="text_boxes" style="width:20px" value="" readonly  />

			                            </td>
			                        </tr>
									<?
									$i++;
								}
								
							}
							else{
								?>
								<tr id="conversion_<?= $i ?>" align="center">
		                           <td>
		                            <?
		                            echo create_drop_down( "cbocosthead_".$i, 380, $fab_description, "",1," -- All Fabrics --", $fab_id, "set_conversion_qnty(<?= $i ?>)","","" );
		                            ?>
		                            </td>
		                            <td><?  echo create_drop_down( "cbotypeconversion_".$i, 155, $conversion_cost_head_array,"", 1, "-- Select --", "", "set_conversion_charge_unit(<?= $i ?>,".$conversion_from_chart.")","","" ); ?></td>
		                             <td>
		                            <input type="text" id="txtprocessloss_<?= $i ?>"  name="txtprocessloss_<?= $i ?>" class="text_boxes_numeric" style="width:50px"  value="" title="Process Loss Not Found In Library" readonly  />
		                            </td>
		                           <td>
		                            <input type="text" id="txtreqqnty_<?= $i ?>"  name="txtreqqnty_<?= $i ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <?= $i ?> )" value="" />
		                            </td>
		                           <td>
		                            <input type="text" id="txtchargeunit_<?= $i ?>"  name="txtchargeunit_<?= $i ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <?= $i ?> )" value="" onClick="<? if($conversion_from_chart==1){ echo "set_conversion_charge_unit_pop_up(<?= $i ?>)";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?> />
		                            </td>
		                            <td>
		                            <input type="text" id="txtamountconversion_<?= $i ?>"  name="txtamountconversion_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="" readonly />
		                            </td>

		                            <td><? echo create_drop_down( "cbostatusconversion_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
		                            <td>
		                            <input type="button" id="increaseconversion_<?= $i ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(<?= $i ?>,<? echo $conversion_from_chart; ?>)" />
		                            <input type="button" id="decreaseconversion_<?= $i ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?= $i ?>,'tbl_conversion_cost' );" />
		                            <input type="hidden" id="updateidcoversion_<?= $i ?>" name="updateidcoversion_<?= $i ?>"  class="text_boxes" style="width:20px" value="" readonly  />
		                            <input type="hidden" id="coversionchargelibraryid_<?= $i ?>" name="coversionchargelibraryid_<?= $i ?>"  class="text_boxes" style="width:20px" value="" readonly  />

		                            </td>
		                        </tr>
								<?
								$i++;
							}
						}
						?>
						
                    <? } ?>
                </tbody>
                </table>
                <table width="965" cellspacing="0" class="rpt_table" border="0" rules="all">
                	<tfoot>
                    	<tr>
                            <th width="600">Sum</th>
                            <th  width="50">
                            <input type="text" id="txtconreqnty_sum"  name="txtconreqnty_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th width="50">
                            <input type="text" id="txtconchargeunit_sum"  name="txtconchargeunit_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th width="80">
                             <input type="text" id="txtconamount_sum"  name="txtconamount_sum" class="text_boxes_numeric" style="width:80px"  readonly value="<? echo $total_conversion_amount ?>"/>
                            </th>
                            <th width="80"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="965" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_conversion_cost_dtls", 1,0,"reset_form('fabriccost_3','','',0)",5) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_conversion_cost_dtls", 0,0,"reset_form('fabriccost_3','','',0)",5) ;
						}
						?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>
	<?
    exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			//alert(data)
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'quotation_entry_controller_v2');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
			document.getElementById('fab_des_id').value=data[0];
			document.getElementById('fab_nature_id').value=data[1];
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('fab_gsm').value=trim(data[3]);
			document.getElementById('process_loss').value=trim(data[4]);
			document.getElementById('cbo_certification_pop').value=trim(data[7]);
			document.getElementById('fab_desctiption').value=trim(fabric_description);
			document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
			document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
			document.getElementById('cbo_fabric_code_pop').value=trim(data[8]);
			document.getElementById('cbo_yarn_color_id').value=trim(data[9]);
			document.getElementById('cbo_yarn_code').value=trim(data[10]);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}


</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
            		<tr>
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
                    </tr>
                    <tr>
						<th>Construction</th>
	                    <th>GSM/Weight</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" />
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+'<? echo $cbo_buyer_name; ?>', 'fabric_description_popup_search_list_view', 'search_div', 'quotation_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)');toggle( 'tr_'+'<? echo $libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>

	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	//echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$lib_buyer=return_library_array( "select buyer_name,id from lib_buyer", "id", "buyer_name"  );
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$cbo_buyer_name)=explode('**',$data);
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
	}

	$buyer_cond='';
	if(!empty($cbo_buyer_name))
	{
		$buyer_cond=" and a.buyer_id='$cbo_buyer_name' ";
	}
	//if($construction!=''){$search_con = " and a.construction like('%".trim($construction)."%')";}
	//if($gsm_weight!=''){$search_con  .= " and a.gsm_weight like('%".trim($gsm_weight)."%')";}

	?>

	</head>
	<body>
	<div align="center">
	    <form>
	        <input type="hidden" id="fab_des_id" name="fab_des_id" />
	        <input type="hidden" id="fab_nature_id" name="fab_nature_id" />
	        <input type="hidden" id="fab_desctiption" name="fab_desctiption" />
	        <input type="hidden" id="fab_gsm" name="fab_gsm" />
	        <input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
	        <input type="hidden" id="process_loss" name="process_loss" />
	        <input type="hidden" id="construction" name="construction" />
	        <input type="hidden" id="composition" name="composition" />
	        <input type="hidden" id="cbo_certification_pop" name="cbo_certification_pop" />
	        <input type="hidden" id="cbo_fabric_code_pop" name="cbo_fabric_code_pop" />
	        <input type="hidden" id="cbo_yarn_code" name="cbo_yarn_code" />
	        <input type="hidden" id="cbo_yarn_color_id" name="cbo_yarn_color_id" />
	    </form>

	<?
		$composition_arr=array();
		//$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
		 $sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond order by a.id,b.id";
		 //echo $sql;
		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}

		//$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id order by a.id";
		//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$lib_yarn_count,9=>$yarn_type);
		//echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/Weight,Color Range,Stich Length,Process Loss,Composition", "100,100,100,100,90,50,300","950","350",0, $sql, "js_set_value", "id,fab_nature_id,construction,gsm_weight,process_loss", "",1, "fab_nature_id,0,0,color_range_id,0,0,id", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0') ;
	?>
	<table class="rpt_table" width="1150" cellspacing="0" cellpadding="0" border="0" rules="all">
		<thead>
			<tr>
				<th style="word-break: break-all;" width="50">SL No</th>
				<th style="word-break: break-all;" width="70">Sequence<br>No</th>
				<th style="word-break: break-all;" width="80">Determination<br>ID</th>
				<th style="word-break: break-all;" width="80">Fabric Code</th>
				<th style="word-break: break-all;" width="100">Fab Nature</th>
				<th style="word-break: break-all;" width="100">Construction</th>
				<th style="word-break: break-all;" width="100">GSM/Weight</th>
				<th style="word-break: break-all;" width="100">Color Range</th>
				<th style="word-break: break-all;" width="90">Stich Length</th>
				<th style="word-break: break-all;" width="50">Process Loss</th>
				<th style="word-break: break-all;" width="100">Buyer</th>
				<th style="word-break: break-all;">Composition</th>
			</tr>
		</thead>
	</table>
	<div id="" style="max-height:350px; width:1148px; overflow-y:scroll">
	<table id="list_view" class="rpt_table" width="1180" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
		<tbody>
		<?
			/*$sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id order by a.id")*/;
			$sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id,a.sequence_no,a.buyer_id,b.yarn_finish,b.yarn_spinning_system,b.certification,a.system_no,b.yarn_color_id,b.yarn_code from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' $search_con and  a.is_deleted=0 $buyer_cond group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.sequence_no,a.buyer_id,b.yarn_finish,b.yarn_spinning_system,b.certification,a.system_no,b.yarn_color_id,b.yarn_code order by a.id");


		$i=1;
		foreach($sql_data as $row)
		{
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
		?>
		<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')]."_".$row[csf('yarn_finish')]."_".$row[csf('yarn_spinning_system')]."_".$row[csf('certification')]."_".$row[csf('system_no')]."_".$row[csf('yarn_color_id')]."_".$row[csf('yarn_code')] ?>')">
			<td style="word-break: break-all;" width="50"><? echo $i; ?></td>
			<td style="word-break: break-all;"  width="70"><? echo $row[csf('id')]; ?></td>
			<td style="word-break: break-all;"  width="80" align="left"><? echo $row[csf('sequence_no')]; ?></td>
			<td style="word-break: break-all;"  width="80" align="left"><? echo $row[csf('system_no')]; ?></td>
			<td style="word-break: break-all;"  width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
			<td style="word-break: break-all;"  width="100" align="left"><? echo $row[csf('construction')]; ?></td>
			<td style="word-break: break-all;"  width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
			<td style="word-break: break-all;"  width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
			<td style="word-break: break-all;"  width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
			<td style="word-break: break-all;"  width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
			 <td style="word-break: break-all;"  width="100" align="right"><? echo $lib_buyer[$row[csf('buyer_id')]]; ?></td>
			<td style="word-break: break-all;" ><? echo $composition_arr[$row[csf('id')]]; ?></td>
		</tr>

		<?
		$i++;
		}
		?>
		</tbody>
	</table>
	<script>
	//setFilterGrid("list_view",-1);
	 //toggle( "tr_"+"<? //echo $libyarncountdeterminationid; ?>", '#FFFFCC');
	</script>
	</div>
	</div>

	</body>
	</html>
	<?
}

if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.yarn_finish,b.yarn_spinning_system,b.certification,yarn_color_id,yarn_code from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and  a.is_deleted=0 order by a.id,b.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			

			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				//".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].","
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				//.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].","
			}
		if($row[csf('yarn_spinning_system')]=='') $row[csf('yarn_spinning_system')]=0;
		if($row[csf('certification')]=='') $row[csf('certification')]=0;
		if($row[csf('process_loss')]=='') $row[csf('process_loss')]=0;
		if($row[csf('yarn_color_id')]=='') $row[csf('yarn_color_id')]=0;
		if($row[csf('yarn_color_id')]=='') $row[csf('yarn_color_id')]=0;
			if($yarn_description!="")
			{
				//$yarn_description=$yarn_description."__".$lib_yarn_count[$row[csf('count_id')]]."_".$composition[$row[csf('copmposition_id')]]."_100_".$yarn_type[$row[csf('type_id')]]."_".$row[csf('percent')];
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')]."_".$row[csf('yarn_finish')]."_".$row[csf('yarn_spinning_system')]."_".$row[csf('certification')]."_".$row[csf('process_loss')]."_".$row[csf('yarn_color_id')]."_".$row[csf('yarn_code')];

			}
			else
			{
				//$yarn_description=$lib_yarn_count[$row[csf('count_id')]]."_".$composition[$row[csf('copmposition_id')]]."_100_".$yarn_type[$row[csf('type_id')]]."_".$row[csf('percent')];
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')]."_".$row[csf('yarn_finish')]."_".$row[csf('yarn_spinning_system')]."_".$row[csf('certification')]."_".$row[csf('process_loss')]."_".$row[csf('yarn_color_id')]."_".$row[csf('yarn_code')];

			}
		}
	}
	echo $fab_description."**".$yarn_description;

}

if ($action=="consumption_popup")
{
  	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
<script>
var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
function add_break_down_tr( i )
{
	var body_part_id=document.getElementById('body_part_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	var row_num=$('#tbl_consmption_cost tr').length-1;
	if (i==0)
	{
		i=1;
		 $("#gmtssizes_"+i).autocomplete({
			 source: str_gmtssizes
		  });
		   $("#diawidth_"+i).autocomplete({
			 source:  str_diawidth
		  });
		  return;
	}
	if (row_num!=i)
	{
		return false;
	}

	if (form_validation('gmtssizes_'+i+'*diawidth_'+i+'*cons_'+i+'*requirement_'+i+'*pcs_'+i,'Gmts Sizes*Width*Cons*Requirement*Pcs')==false)
	{
		//alert("Fill Up all field");
		return;
	}

	if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2 && form_validation('bodylength_'+i+'*bodysewingmargin_'+i+'*bodyhemmargin_'+i+'*sleevelength_'+i+'*sleevesewingmargin_'+i+'*sleevehemmargin_'+i+'*chestlenght_'+i+'*chestsewingmargin_'+i,'Body Length*Body Sewing Margin*Body Hem Margin*Sleeve Length*Sleeve Sewing Margin*Sleeve Hem Margin*Chest Length*Chest Sewing Margin')==false)
	{
		//alert("Fill Up all field");
		 return;
	}
	if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2 && form_validation('frontriselength_'+i+'*frontrisesewingmargin_'+i+'*westbandlength_'+i+'*westbandsewingmargin_'+i+'*inseamlength_'+i+'*inseamsewingmargin_'+i+'*inseamhemmargin_'+i+'*halfthailength_'+i+'*halfthaisewingmargin_'+i,'Front Rise Length*Front Rise Sewing Margin*West Band Length*West Band Sewing Margin*Inseam Length*Inseam Sewing Margin*Inseam Hem Margin*Half Thai Length* Half Thai Sewing Margin')==false)
	{
		   //alert("Fill Up all field");
		   return;
	}
	else
	{
		i++;

		 $("#tbl_consmption_cost tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_consmption_cost");

		  $('#addrow_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+")");
		  $('#decreaserow_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_consmption_cost')");
		  $('#cons_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'cons_sum', 'cons_' )");
		  $('#cons_'+i).removeAttr("onChange").attr("onChange","set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement( "+i+")");
		  $('#cons_'+i).removeAttr("onDblClick").attr("onDblClick","open_marker_popup( "+i+","+hid_fab_cons_in_quotation_variable+")");


		  $('#diawidth_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
		  $('#processloss_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'processloss_sum', 'processloss_' )");
		  $('#processloss_'+i).removeAttr("onChange").attr("onChange","calculate_requirement( "+i+");set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_')");
          $('#requirement_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'requirement_sum', 'requirement_')");
		  $('#requirement_'+i).removeAttr("onChange").attr("onChange","calculate_requirement( "+i+");set_sum_value( 'requirement_sum', 'requirement_')");
		   $('#rate_'+i).removeAttr("onChange").attr("onChange","claculate_amount( "+i+");");
		  $('#pcs_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'pcs_sum', 'pcs_')");

		  var j=i-1;
		  $('#gmtssizes_'+i).val('');
		  $('#diawidth_'+i).val($('#diawidth_'+j).val());
		  //$('#msmnt_'+i).val($('#msmnt_'+j).val());
		  if(hid_fab_cons_in_quotation_variable==3 )
		  {
		  $('#cons_'+i).val($('#cons_'+j).val());
		  $('#requirement_'+i).val($('#requirement_'+j).val());
		  }
		  else
		  {
		  $('#cons_'+i).val('');
		  $('#requirement_'+i).val('');
		  }

		  $('#processloss_'+i).val($('#processloss_'+j).val());

		  $('#pcs_'+i).val($('#pcs_'+j).val());
		  $('#updateidcb_'+i).val('');

		  //-----------------------

		  $("#tbl_msmnt_cost tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_msmnt_cost");
		  if(body_part_id==1)
		  {
			  $('#bodylength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodysewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodyhemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevelength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevesewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevehemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#chestlenght_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#chestsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");

			  $('#bodylength_'+i).val('');
			  $('#bodysewingmargin_'+i).val($('#bodysewingmargin_'+j).val());
			  $('#bodyhemmargin_'+i).val($('#bodyhemmargin_'+j).val());
			  $('#sleevelength_'+i).val('');
			  $('#sleevesewingmargin_'+i).val($('#sleevesewingmargin_'+j).val());
			  $('#sleevehemmargin_'+i).val($('#sleevehemmargin_'+j).val());
			  $('#chestlenght_'+i).val('');
			  $('#chestsewingmargin_'+i).val($('#chestsewingmargin_'+j).val());
		  }
		  if(body_part_id==20)
		  {
			  $('#frontriselength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#frontrisesewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#westbandlength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#westbandsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamlength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamhemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#halfthailength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#halfthaisewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");

			  $('#frontriselength_'+i).val($('#frontriselength_'+j).val());
			  $('#frontrisesewingmargin_'+i).val($('#frontrisesewingmargin_'+j).val());
			  $('#westbandlength_'+i).val($('#westbandlength_'+j).val());
			  $('#westbandsewingmargin_'+i).val($('#westbandsewingmargin_'+j).val());
			  $('#inseamlength_'+i).val($('#inseamlength_'+j).val());
			  $('#inseamsewingmargin_'+i).val($('#inseamsewingmargin_'+j).val());
			  $('#inseamhemmargin_'+i).val($('#inseamhemmargin_'+j).val());
			  $('#halfthailength_'+i).val($('#halfthailength_'+j).val());
			  $('#halfthaisewingmargin_'+i).val($('#halfthaisewingmargin_'+j).val());
		  }
		  //------------------
		  set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
		  set_sum_value( 'processloss_sum', 'processloss_'  );
		  set_sum_value( 'requirement_sum', 'requirement_');
		  //set_sum_value( 'amount_sum', 'amount_');
          set_sum_value( 'pcs_sum', 'pcs_');

		  $("#gmtssizes_"+i).autocomplete({
			 source: str_gmtssizes
		  });
		   $("#diawidth_"+i).autocomplete({
			 source:  str_diawidth
		  });
	}
}


function fn_delete_down_tr(rowNo,table_id)
{
	if(table_id=='tbl_consmption_cost')
	{
		var numRow = $('table#tbl_consmption_cost tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_consmption_cost tbody tr:last').remove();
			$('#tbl_msmnt_cost tbody tr:last').remove();
		}
		set_sum_value( 'cons_sum', 'cons_'  );
		set_sum_value( 'processloss_sum', 'processloss_'  );
		set_sum_value( 'requirement_sum', 'requirement_');
		set_sum_value( 'amount_sum', 'amount_');
		set_sum_value( 'rate_sum', 'rate_' )
		set_sum_value( 'pcs_sum', 'pcs_');
	}
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='cons_sum') var ddd={dec_type:5,comma:0};
	if(des_fil_id=='processloss_sum') var ddd={dec_type:5,comma:0};
	if(des_fil_id=='requirement_sum') var ddd={dec_type:5,comma:0};
	if(des_fil_id=='pcs_sum') var ddd={dec_type:1,comma:0};
	if(des_fil_id=='amount_sum') var ddd={dec_type:5,comma:0};
	if(des_fil_id=='rate_sum') var ddd={dec_type:5,comma:0};

	var rowCount = $('#tbl_consmption_cost tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd);
	claculate_avg();
}

function js_set_value()
{
	var body_part_id=document.getElementById('body_part_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	var cons_breck_down="";
	var msmnt_breack_down="";
	var marker_breack_down="";
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('gmtssizes_'+i+'*diawidth_'+i+'*cons_'+i+'*requirement_'+i+'*pcs_'+i,'Gmts Sizes*Width*Cons*Requirement*Pcs')==false)
		{
			//alert("Fill Up all field");
			return;
		}

		if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2 && form_validation('bodylength_'+i+'*bodysewingmargin_'+i+'*bodyhemmargin_'+i+'*sleevelength_'+i+'*sleevesewingmargin_'+i+'*sleevehemmargin_'+i+'*chestlenght_'+i+'*chestsewingmargin_'+i,'Body Length*Body Sewing Margin*Body Hem Margin*Sleeve Length*Sleeve Sewing Margin*Sleeve Hem Margin*Chest Length*Chest Sewing Margin')==false)
		{
			//alert("Fill Up all field");
			return;
		 }
		 if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2 && form_validation('frontriselength_'+i+'*frontrisesewingmargin_'+i+'*westbandlength_'+i+'*westbandsewingmargin_'+i+'*inseamlength_'+i+'*inseamsewingmargin_'+i+'*inseamhemmargin_'+i+'*halfthailength_'+i+'*halfthaisewingmargin_'+i,'Front Rise Length*Front Rise Sewing Margin*West Band Length*West Band Sewing Margin*Inseam Length*Inseam Sewing Margin*Inseam Hem Margin*Half Thai Length* Half Thai Sewing Margin')==false )
		{
			///alert("Fill Up all field");
			 return;
		}
		var rate=$('#rate_'+i).val();
		if(rate==""){
			rate=0;
		}
		var amount=$('#amount_'+i).val();
		if(amount==""){
			amount=0;
		}
		var gmtssizes=$('#gmtssizes_'+i).val();
		if(gmtssizes=='') gmtssizes=0;
		var diawidth=$('#diawidth_'+i).val();
		if(diawidth=='') diawidth=0;
		var cons=$('#cons_'+i).val();
		if(cons=='') cons=0;
		var processloss=$('#processloss_'+i).val();
		if(processloss=='') processloss=0;
		var requirement=$('#requirement_'+i).val();
		if(requirement=='') requirement=0;
		var pcs=$('#pcs_'+i).val();
		if(pcs=='') pcs=0;


		if(cons_breck_down=="")
		{
			cons_breck_down+=gmtssizes+'_'+diawidth+'_'+cons+'_'+processloss+'_'+requirement+'_'+pcs+'_'+rate+'_'+amount;
		}
		else
		{
			cons_breck_down+="__"+gmtssizes+'_'+diawidth+'_'+cons+'_'+processloss+'_'+requirement+'_'+pcs+'_'+rate+'_'+amount;
		}

		if(hid_fab_cons_in_quotation_variable==3)
		{
			var marker=$('#marker_'+i).val()
			if(marker=='') marker=0;
			if(marker_breack_down=="") marker_breack_down+=marker;
			else marker_breack_down+="**"+marker;
		}

		if(hid_fab_cons_in_quotation_variable==2)
		{
			if(msmnt_breack_down=="")
			{
				if(body_part_id==1)
				{
				msmnt_breack_down+=$('#bodylength_'+i).val()+'_'+$('#bodysewingmargin_'+i).val()+'_'+$('#bodyhemmargin_'+i).val()+'_'+$('#sleevelength_'+i).val()+'_'+$('#sleevesewingmargin_'+i).val()+'_'+$('#sleevehemmargin_'+i).val()+'_'+$('#chestlenght_'+i).val()+'_'+$('#chestsewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
				if(body_part_id==20)
				{
				msmnt_breack_down+=$('#frontriselength_'+i).val()+'_'+$('#frontrisesewingmargin_'+i).val()+'_'+$('#westbandlength_'+i).val()+'_'+$('#westbandsewingmargin_'+i).val()+'_'+$('#inseamlength_'+i).val()+'_'+$('#inseamsewingmargin_'+i).val()+'_'+$('#inseamhemmargin_'+i).val()+'_'+$('#halfthailength_'+i).val()+'_'+$('#halfthaisewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
			}
			else
			{
				if(body_part_id==1)
				{
				msmnt_breack_down+="__"+$('#bodylength_'+i).val()+'_'+$('#bodysewingmargin_'+i).val()+'_'+$('#bodyhemmargin_'+i).val()+'_'+$('#sleevelength_'+i).val()+'_'+$('#sleevesewingmargin_'+i).val()+'_'+$('#sleevehemmargin_'+i).val()+'_'+$('#chestlenght_'+i).val()+'_'+$('#chestsewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
				if(body_part_id==20)
				{
				msmnt_breack_down+="__"+$('#frontriselength_'+i).val()+'_'+$('#frontrisesewingmargin_'+i).val()+'_'+$('#westbandlength_'+i).val()+'_'+$('#westbandsewingmargin_'+i).val()+'_'+$('#inseamlength_'+i).val()+'_'+$('#inseamsewingmargin_'+i).val()+'_'+$('#inseamhemmargin_'+i).val()+'_'+$('#halfthailength_'+i).val()+'_'+$('#halfthaisewingmargin_'+i).val()+'_'+$('#totalcons_'+i).val();
				}
			}
		}
	}
	//alert(cons_breck_down);
	//alert(msmnt_breack_down);
	document.getElementById('cons_breck_down').value=cons_breck_down;
	document.getElementById('msmnt_breack_down').value=msmnt_breack_down;
	document.getElementById('marker_breack_down').value=marker_breack_down;
	claculate_avg();
	parent.emailwindow.hide();
}

function calculate_measurement_top(i)
{
	var body_part_id=document.getElementById('body_part_id').value;
	var cbofabricnature_id=document.getElementById('cbofabricnature_id').value;
	var cbo_costing_per_id=document.getElementById('cbo_costing_per_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	if(hid_fab_cons_in_quotation_variable==2)
	{
		if (cbo_costing_per_id==1) var dzn_mult=1*12;
		else if (cbo_costing_per_id==2) var dzn_mult=1*1;
		else if (cbo_costing_per_id==3) var dzn_mult=2*12;
		else if (cbo_costing_per_id==4) var dzn_mult=3*12;
		else if (cbo_costing_per_id==5) var dzn_mult=4*12;
		else var dzn_mult=0;

		//------------------------------------Knit------------------------------------
		if(cbofabricnature_id==2)//Knit
		{
			var txt_required_gsm_top=document.getElementById('txt_gsm').value;
			if (body_part_id==1)//main fabric top
			{
				var txt_body_length_measurement_top=0;
				var txt_body_length_sewing_top=0;
				var txt_body_length_hem_top=0;
				var txt_sleeve_length_measurement_top=0;
				var txt_sleeve_length_sewing_top=0;
				var txt_sleeve_length_hem_top=0;
				var txt_chest_measurement_top=0;
				var txt_chest_sew_top=0;
				txt_body_length_measurement_top=document.getElementById('bodylength_'+i).value;
				txt_body_length_sewing_top=document.getElementById('bodysewingmargin_'+i).value;
				txt_body_length_hem_top=document.getElementById('bodyhemmargin_'+i).value;
				txt_sleeve_length_measurement_top=document.getElementById('sleevelength_'+i).value;
				txt_sleeve_length_sewing_top=document.getElementById('sleevesewingmargin_'+i).value;
				txt_sleeve_length_hem_top=document.getElementById('sleevehemmargin_'+i).value;
				txt_chest_measurement_top=document.getElementById('chestlenght_'+i).value;
				txt_chest_sew_top=document.getElementById('chestsewingmargin_'+i).value;

				var dbl_total=(((txt_body_length_measurement_top*1)+(txt_body_length_sewing_top*1)+(txt_body_length_hem_top*1)+(txt_sleeve_length_measurement_top*1)+(txt_sleeve_length_sewing_top*1)+(txt_sleeve_length_hem_top*1))*((txt_chest_measurement_top*1)+(txt_chest_sew_top*1))*2*(dzn_mult*1)*(txt_required_gsm_top*1))/10000000;
			}
			if (body_part_id==20)//main fabric bottom
			{
				var txt_front_rise_measurement_bottom=0;
				var txt_front_rise_sewing_bottom=0;
				var txt_west_band_measurement_bottom=0;
				var txt_west_band_sewing_bottom=0;
				var txt_in_seam_measurement_bottom=0;
				var txt_in_seam_sew_bottom=0;
				var txt_in_seam_hem_bottom=0;
				var txt_half_thai_measurement_bottom=0;
				var txt_half_thai_sew_bottom=0;

				var txt_front_rise_measurement_bottom=document.getElementById('frontriselength_'+i).value;
				var txt_front_rise_sewing_bottom=document.getElementById('frontrisesewingmargin_'+i).value;
				var txt_west_band_measurement_bottom=document.getElementById('westbandlength_'+i).value;
				var txt_west_band_sewing_bottom=document.getElementById('westbandsewingmargin_'+i).value;
				var txt_in_seam_measurement_bottom=document.getElementById('inseamlength_'+i).value;
				var txt_in_seam_sew_bottom=document.getElementById('inseamsewingmargin_'+i).value;
				var txt_in_seam_hem_bottom=document.getElementById('inseamhemmargin_'+i).value;
				var txt_half_thai_measurement_bottom=document.getElementById('halfthailength_'+i).value;
				var txt_half_thai_sew_bottom=document.getElementById('halfthaisewingmargin_'+i).value;

				var dbl_total=(((txt_front_rise_measurement_bottom*1)+(txt_front_rise_sewing_bottom*1)+(txt_west_band_measurement_bottom*1)+(txt_west_band_sewing_bottom*1)+(txt_in_seam_measurement_bottom*1)+(txt_in_seam_sew_bottom*1)+(txt_in_seam_hem_bottom*1))*((txt_half_thai_measurement_bottom*1)+(txt_half_thai_sew_bottom*1))*4*(dzn_mult*1)*(txt_required_gsm_top*1))/10000000;
			}
		}
	//------------------------------------End Knit------------------------------------
	//----------------------------------- Woven---------------------------------------
		if(cbofabricnature_id==3)//woven
		{
			var txt_required_weight_top=document.getElementById('diawidth_'+i).value;
			if (body_part_id==1)//main fabric top
			{
				//alert('www');
				var txt_body_length_measurement_top=0;
				var txt_body_length_sewing_top=0;
				var txt_body_length_hem_top=0;
				var txt_sleeve_length_measurement_top=0;
				var txt_sleeve_length_sewing_top=0;
				var txt_sleeve_length_hem_top=0;
				var txt_chest_measurement_top=0;
				var txt_chest_sew_top=0;
				txt_body_length_measurement_top=document.getElementById('bodylength_'+i).value;
				txt_body_length_sewing_top=document.getElementById('bodysewingmargin_'+i).value;
				txt_body_length_hem_top=document.getElementById('bodyhemmargin_'+i).value;
				txt_sleeve_length_measurement_top=document.getElementById('sleevelength_'+i).value;
				txt_sleeve_length_sewing_top=document.getElementById('sleevesewingmargin_'+i).value;
				txt_sleeve_length_hem_top=document.getElementById('sleevehemmargin_'+i).value;
				txt_chest_measurement_top=document.getElementById('chestlenght_'+i).value;
				txt_chest_sew_top=document.getElementById('chestsewingmargin_'+i).value;

				var dbl_total=(((txt_body_length_measurement_top*1)+(txt_body_length_sewing_top*1)+(txt_body_length_hem_top*1)+(txt_sleeve_length_measurement_top*1)+(txt_sleeve_length_sewing_top*1)+(txt_sleeve_length_hem_top*1))*((txt_chest_measurement_top*1)+(txt_chest_sew_top*1))*2*(dzn_mult*1))/((txt_required_weight_top*1)*36);

			}
			if (body_part_id==20)//main fabric bottom
			{
				var txt_front_rise_measurement_bottom=0;
				var txt_front_rise_sewing_bottom=0;
				var txt_west_band_measurement_bottom=0;
				var txt_west_band_sewing_bottom=0;
				var txt_in_seam_measurement_bottom=0;
				var txt_in_seam_sew_bottom=0;
				var txt_in_seam_hem_bottom=0;
				var txt_half_thai_measurement_bottom=0;
				var txt_half_thai_sew_bottom=0;

				var txt_front_rise_measurement_bottom=document.getElementById('frontriselength_'+i).value;
				var txt_front_rise_sewing_bottom=document.getElementById('frontrisesewingmargin_'+i).value;
				var txt_west_band_measurement_bottom=document.getElementById('westbandlength_'+i).value;
				var txt_west_band_sewing_bottom=document.getElementById('westbandsewingmargin_'+i).value;
				var txt_in_seam_measurement_bottom=document.getElementById('inseamlength_'+i).value;
				var txt_in_seam_sew_bottom=document.getElementById('inseamsewingmargin_'+i).value;
				var txt_in_seam_hem_bottom=document.getElementById('inseamhemmargin_'+i).value;
				var txt_half_thai_measurement_bottom=document.getElementById('halfthailength_'+i).value;
				var txt_half_thai_sew_bottom=document.getElementById('halfthaisewingmargin_'+i).value;

				//[{(Front Rise + In Seam + West Band + Sewing Margin + Hem) x (Half Thai + Sewing Margin)} x 4] x 12  / Width x 36
				var dbl_total=(((txt_front_rise_measurement_bottom*1)+(txt_front_rise_sewing_bottom*1)+(txt_west_band_measurement_bottom*1)+(txt_west_band_sewing_bottom*1)+(txt_in_seam_measurement_bottom*1)+(txt_in_seam_sew_bottom*1)+(txt_in_seam_hem_bottom*1))*((txt_half_thai_measurement_bottom*1)+(txt_half_thai_sew_bottom*1))*4*(dzn_mult*1))/((txt_required_weight_top*1)*36);

			}
		}
		//----------------------------------- End Woven---------------------------------------
		dbl_total= number_format_common( dbl_total, 1, 5) ;
		document.getElementById('totalcons_'+i).value=dbl_total;
		document.getElementById('cons_'+i).value=dbl_total;
		set_sum_value( 'cons_sum', 'cons_'  );
		set_sum_value( 'requirement_sum', 'requirement_' );
		calculate_requirement(i);
		claculate_avg();
	}
}
function calculate_requirement(i)
{
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var cons=(document.getElementById('cons_'+i).value)*1;
	var processloss=(document.getElementById('processloss_'+i).value)*1;
	var WastageQty='';
	if(process_loss_method_id==1) WastageQty=cons+cons*(processloss/100);
	else if(process_loss_method_id==2)
	{
		var devided_val = 1-(processloss/100);
		var WastageQty=parseFloat(cons/devided_val);
	}
	else WastageQty=0;
	document.getElementById('requirement_'+i).value= number_format_common(WastageQty, 5, 0);
	set_sum_value( 'requirement_sum', 'requirement_' );
	claculate_amount(i);
	claculate_avg();
}

function claculate_avg()
{
	var rowCount = $('#tbl_consmption_cost tr').length-1;

	var calculated_cons=(document.getElementById('requirement_sum').value*1)/rowCount;
	var avg_cons=(document.getElementById('cons_sum').value*1)/rowCount;
	var calculated_procloss=(document.getElementById('processloss_sum').value*1)/rowCount;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/rowCount;
	var calculated_amount=(document.getElementById('amount_sum').value*1)/rowCount;
	//alert(calculated_amount)

	document.getElementById('calculated_cons').value=number_format_common(calculated_cons, 5, 0);
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 5, 0);
	document.getElementById('calculated_procloss').value=number_format_common(calculated_procloss, 5, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;
	document.getElementById('calculated_amount').value=number_format_common(calculated_amount, 5, 0);
	document.getElementById('calculated_rate').value=number_format_common(calculated_amount/calculated_cons, 5, 0);
}

function calculate_marker_length()
{
	var cbo_costing_per_id=document.getElementById('cbo_costing_per_id').value;
	if (cbo_costing_per_id==1) var dzn_mult=1*12;
	else if (cbo_costing_per_id==2) var dzn_mult=1*1;
	else if (cbo_costing_per_id==3) var dzn_mult=2*12;
	else if (cbo_costing_per_id==4) var dzn_mult=3*12;
	else if (cbo_costing_per_id==5) var dzn_mult=4*12;
	else var dzn_mult=0;

	var txt_marker_yds= (document.getElementById('txt_marker_yds').value)*1;
	var txt_marker_inch= (document.getElementById('txt_marker_inch').value)*1;
	var txt_gmt_pcs= (document.getElementById('txt_gmt_pcs').value)*1;
	var txt_marker_dia= (document.getElementById('txt_marker_dia').value)*1;
	var txt_marker_gsm= (document.getElementById('txt_marker_gsm').value)*1;

	var txt_marker_length_yds=(txt_marker_inch/36)+txt_marker_yds;
	//alert(txt_marker_length_yds);
	var txt_marker_length_yds2=(txt_marker_length_yds/txt_gmt_pcs)*dzn_mult;
	txt_marker_length_yds3= number_format_common( txt_marker_length_yds2, 1, 0) ;
	document.getElementById('txt_marker_length_yds').value=txt_marker_length_yds3;
	var txt_marker_net_fab_cons=((txt_marker_length_yds3*36*2.54)/dzn_mult)*(txt_marker_dia*2*2.54*dzn_mult*txt_marker_gsm);
	var txt_marker_net_fab_cons2=txt_marker_net_fab_cons/10000000;
	document.getElementById('txt_marker_net_fab_cons').value=number_format_common(txt_marker_net_fab_cons2,1,0);
	document.getElementById('cons_1').value=number_format_common(txt_marker_net_fab_cons2,1,0);
	document.getElementById('requirement_1').value=number_format_common(txt_marker_net_fab_cons2,1,0);
	set_sum_value( 'cons_sum', 'cons_'  );
	set_sum_value( 'requirement_sum', 'requirement_');
}

function open_marker_popup(i,ConsumptionBasis){
	if(ConsumptionBasis==3){
		var marker=document.getElementById('marker_'+i).value;
		var page_link='quotation_entry_controller_v2.php?action=open_marker_popup&marker='+marker;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Marker Info', 'width=800px,height=200px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			//marker_arr[i]=this.contentDoc;
			//var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			document.getElementById('cons_'+i).value=this.contentDoc.getElementById("txt_marker_net_fab_cons").value;
			document.getElementById('marker_'+i).value=this.contentDoc.getElementById("marker_breack_down").value;
			calculate_requirement(i)
			copy_value(number_format_common(document.getElementById('cons_'+i).value,5,0),'cons_',i)
			copy_value(document.getElementById('marker_'+i).value,'marker_',i)
		}
	}
}

function claculate_amount(i){
	var rate=document.getElementById('rate_'+i).value;
	var requirement=document.getElementById('requirement_'+i).value;
	var amount= requirement*rate;
	document.getElementById('amount_'+i).value=number_format_common(amount,5,0);
	set_sum_value( 'amount_sum', 'amount_' );
	set_sum_value( 'rate_sum', 'rate_' );
	claculate_avg();
}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<fieldset>
            <legend><? echo $body_part_id.'.'.$body_part[$body_part_id].'   Costing '.$costing_per[$cbo_costing_per] ;?></legend>
        	<form id="consumptionform_1" autocomplete="off">
            <input type="hidden" id="cbo_company_id" name="cbo_company_id" value="<? echo $cbo_company_id; ?>"/>
            <input type="hidden" id="cbo_costing_per_id" name="cbo_costing_per_id" value="<? echo $cbo_costing_per; ?>"/>
            <input type="hidden" id="hid_fab_cons_in_quotation_variable" name="hid_fab_cons_in_quotation_variable" value="<? echo $hid_fab_cons_in_quotation_variable; ?>" width="500" />
            <input type="hidden" id="body_part_id" name="body_part_id" value="<? echo $body_part_id; ?>"/>
            <input type="hidden" id="cbofabricnature_id" name="cbofabricnature_id" value="<? echo $cbofabricnature_id; ?>"/>
            <input type="hidden" id="cons_breck_down" name="cons_breck_down"  width="500"  value="<? echo $cons_breck_downn;?>"/>
            <input type="hidden" id="marker_breack_down" name="marker_breack_down"  value="<? echo $marker_breack_down;?>"/>
            <input type="hidden" id="msmnt_breack_down" name="msmnt_breack_down"  value="<? echo $msmnt_breack_downn;?>"/>
            <input type="hidden" id="txt_gsm" name="txt_gsm" value="<? echo $txtgsmweight; ?>"/>
			<?
			$pcs_value=0;
			//select set_item_ratio
			$set_item_ratio=return_field_value("set_item_ratio", "wo_price_quotation_set_details", "quotation_id='$update_id'  and gmts_item_id='$cbogmtsitem'");
			if($set_item_ratio==0 || $set_item_ratio=="") $set_item_ratio=1;

			if($cbo_costing_per==1) $pcs_value=1*12*$set_item_ratio;
			else if($cbo_costing_per==2) $pcs_value=1*1*$set_item_ratio;
			else if($cbo_costing_per==3) $pcs_value=2*12*$set_item_ratio;
			else if($cbo_costing_per==4) $pcs_value=3*12*$set_item_ratio;
			else if($cbo_costing_per==5) $pcs_value=4*12*$set_item_ratio;
			if($body_part_id==3) $pcs_value=$pcs_value*2;

			$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_id  and variable_list=18 and item_category_id=$cbofabricnature_id and status_active=1 and is_deleted=0");
            ?>
           <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
           <?
		   if($hid_fab_cons_in_quotation_variable==3){
		   ?>
          <!--<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_marker_cost" rules="all">
              <thead>
                    <tr>
                        <th  width="100"  rowspan="2">Marker Dia (Inch)</th><th  width="100" colspan="2">Marker Length</th><th  width="110"  rowspan="2">Gmts. Size Ratio (Pcs)</th><th width="90"  rowspan="2">Marker Length -Yds (1Dzn Gmts)</th><th width="110"  rowspan="2">GSM</th><th width="110"  rowspan="2">Net Fab Cons</th><th></th>

                    </tr>
                    <tr>
                        <th  width="100">Yds</th><th  width="100">Inch</th><th></th>

                    </tr>
              </thead>
              <tbody>
              <?
			  //$marker_breack_down_arr=explode("_",$marker_breack_down);
			  ?>
              <tr>
              <td><input type="text" id="txt_marker_dia"  name="txt_marker_dia" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? // echo $marker_breack_down_arr[0];  ?>"> </td>
              <td><input type="text" id="txt_marker_yds"  name="txt_marker_yds" class="text_boxes_numeric" style="width:90px"  onChange="calculate_marker_length()" value="<? //echo $marker_breack_down_arr[1];  ?>"></td>
              <td><input type="text" id="txt_marker_inch"  name="txt_marker_inch" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? //echo $marker_breack_down_arr[2];  ?>"></td>
              <td><input type="text" id="txt_gmt_pcs"  name="txt_gmt_pcs" class="text_boxes_numeric" style="width:110px" onChange="calculate_marker_length()"  value="<? //echo $marker_breack_down_arr[3];  ?>"></td>
              <td><input type="text" id="txt_marker_length_yds"  name="txt_marker_length_yds" class="text_boxes_numeric" style="width:110px" readonly  value="<? //echo $marker_breack_down_arr[4];  ?>"></td>
              <td><input type="text" id="txt_marker_gsm"  name="txt_marker_gsm" class="text_boxes_numeric" readonly style="width:110px"  value="<? //echo $txtgsmweight; ?>"></td>
              <td><input type="text" id="txt_marker_net_fab_cons"  name="txt_marker_net_fab_cons" class="text_boxes_numeric" style="width:110px"  value="<? //echo $marker_breack_down_arr[5];  ?>"></td>
              <td></td>
              </tr>
              </tbody>
          </table>-->
           <?
		   }
		   ?>
<br/>

            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">SL</th><th  width="100">Gmts sizes</th><th  width="110"><? if($cbofabricnature_id==2){echo "Dia"; }else{ echo "Width";}?></th><th width="70">Cons<? if($cbofabricnature_id==2){echo ""; }else{ echo "/Yds";}?></th><th width="70">Process Loss %</th><th width="70">Requirment</th><th width="60">Rate</th><th width="60">Amount</th><th width="60">Pcs</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?


					//echo "select id,wo_pri_quo_fab_co_dtls_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs,marker_dia,marker_yds ,marker_inch , gmts_pcs ,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'";

					if($updateid_fc != "")
						{
							$row_marker=array();
							$wo_pre_cos_fab_co_avg_con_dtls_data=sql_select("select id,wo_pri_quo_fab_co_dtls_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs,marker_dia,marker_yds ,marker_inch , gmts_pcs ,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");
							foreach($wo_pre_cos_fab_co_avg_con_dtls_data as $wo_pre_cos_fab_co_avg_con_dtls_data_row)
							{
								$row_marker[]=$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('marker_dia')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('marker_yds')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('marker_inch')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('gmts_pcs')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('marker_length')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('net_fab_cons')];
							}
						}

					//$data_array=sql_select("select id,wo_pri_quo_fab_co_dtls_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");
					//id,wo_pri_quo_fab_co_dtls_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs
					$data_array=explode("__",$cons_breck_downn);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					//print_r($data_array);
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$data=explode('_',$row);
							$marker=$row_marker[$i];
							$i++;
							?>
                            	<tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $data[0]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="diawidth_<? echo $i;?>"    name="diawidth_<? echo $i;?>"  class=" <? if($cbofabricnature_id==2){echo "text_boxes"; }else{ echo "text_boxes_numeric";}?>" style="width:95px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[1]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="cons_<? echo $i;?>" onBlur="set_sum_value( 'cons_sum', 'cons_' )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(<? echo $i;?>)" onDblClick="open_marker_popup(<? echo $i;?>,<? echo $hid_fab_cons_in_quotation_variable?>)" name="cons_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" <? if(($hid_fab_cons_in_quotation_variable==2 || $hid_fab_cons_in_quotation_variable==3) && ($body_part_id==1 || $body_part_id==20)){ echo "readonly";} else{ echo "";} ?> value="<? echo $data[2]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="processloss_<? echo $i;?>" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' )" value="<? if($data[3]=='' ) echo '0';else echo $data[3]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="requirement_<? echo $i;?>" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) "  onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" readonly value="<? echo $data[4]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>"  class="text_boxes_numeric" style="width:50px" value="<? echo $data[6]; ?>" onChange="claculate_amount(<? echo $i;?>)">
                                    </td>
                                     <td>
                                    <input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  class="text_boxes_numeric" style="width:50px" value="<? echo $data[7]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) "  class="text_boxes_numeric" style="width:50px" value="<? echo $data[5]; ?>">
                                    <input type="hidden" id="marker_<? echo $i;?>"  name="marker_<? echo $i;?>"   class="text_boxes_numeric" style="width:75px"  value="<? echo $marker; ?>">
                                    </td>
                                     <td>
                                     <input type="button" id="addrow_<? echo $i;?>"  name="addrow_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i;?> )" />
                                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i;?> ,'tbl_consmption_cost' );" />



                                     </td>
                                </tr>

                            <?

						}

					}
					else
					{
						?>
						<tr id="break_1" align="center">
							<td>
							  <? echo $i;?>
							</td>
							 <td>
							<input type="text" id="gmtssizes_1"  name="gmtssizes_1" class="text_boxes" style="width:85px"  />
							</td>
							<td>
							<input type="text" id="diawidth_1"  value=""  name="diawidth_1"  class=" <? if($cbofabricnature_id==2){echo "text_boxes"; }else{ echo "text_boxes_numeric";}?>" style="width:95px" onChange="calculate_measurement_top(1)">
							</td>

							<td>
							<input type="text" id="cons_1" onBlur="set_sum_value( 'cons_sum', 'cons_' )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(1)" onDblClick="open_marker_popup(1,<? echo $hid_fab_cons_in_quotation_variable?>)"  name="cons_1" class="text_boxes_numeric" style="width:60px" <? if(($hid_fab_cons_in_quotation_variable==2 || $hid_fab_cons_in_quotation_variable==3) && ($body_part_id==1 || $body_part_id==20)){ echo "readonly";} else{ echo "";} ?>>
							</td>
							<td>
							<input type="text" id="processloss_1" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_1" class="text_boxes_numeric" style="width:60px" value="0" onChange="calculate_requirement(1);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' ) ">
							</td>
							<td>
							<input type="text" id="requirement_1" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) " onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_1" class="text_boxes_numeric" style="width:60px" readonly>
							</td>
							 <td>
							<input type="text" id="rate_1"  name="rate_1"  class="text_boxes_numeric" style="width:50px" value="0" onChange="claculate_amount(1)">
							</td>
							<td>
							<input type="text" id="amount_1"  name="amount_1"  class="text_boxes_numeric" style="width:50px" value="0">
							</td>
							<td>
							<input type="text" id="pcs_1"  name="pcs_1"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $pcs_value; ?>" />
							<input type="hidden" id="marker_1"  name="marker_1"   class="text_boxes_numeric" style="width:75px"  value="<? //echo $marker; ?>">

							</td>
							<td id="add_1">
							 <input type="button" id="addrow_1"  name="addrow_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
							 <input type="button" id="decreaserow_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1,'tbl_consmption_cost' );" />
						   <!-- <a href="## " id="addrow_1"  name="addrow_1" style="cursor:pointer;  text-decoration:none;" onClick="add_break_down_tr( 1 )"><b>+</b></a>-->
							<!--<input type="text" id="updateidcb_1" name="updateidcb_1" value="" class="text_boxes" style="width:20px" />-->
							</td>
						</tr>
                    <? } ?>
                </tbody>
                </table>

                <table width="810" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th style="width:262px;">SUM</th>
                            <th width="70"><input type="text" id="cons_sum" name="cons_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="70"><input type="text" id="processloss_sum"  name="processloss_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="70"><input type="text" id="requirement_sum"  name="requirement_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                            <th width="60"><input type="text" id="rate_sum"    name="rate_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width="60"><input type="text" id="amount_sum"    name="amount_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width="60"><input type="text" id="pcs_sum"    name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width=""></th>
                        </tr>
                        <tr>
                        	<th style="width:263px;">AVG</th>
                            <th width="70"><input type="text" id="avg_cons" name="avg_cons" class="text_boxes_numeric" style="width:60px" value="<? //echo $calculated_conss;?>" readonly></th>
                            <th width="70"><input type="text" id="calculated_procloss"  name="calculated_procloss" class="text_boxes_numeric" style="width:60px" readonly></th>
                           <th width="70"><input type="text" id="calculated_cons" name="calculated_cons" class="text_boxes_numeric" style="width:60px" value="<? echo $calculated_conss;?>" readonly></th>
                            <th width="60"><input type="text" id="calculated_rate"    name="calculated_rate" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width="60"><input type="text" id="calculated_amount"    name="calculated_amount" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width="60"><input type="text" id="calculated_pcs"    name="calculated_pcs" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

             				 <script>
							set_sum_value( 'cons_sum', 'cons_'  );
		                    set_sum_value( 'processloss_sum', 'processloss_'  );
		                    set_sum_value( 'requirement_sum', 'requirement_');
                            set_sum_value( 'pcs_sum', 'pcs_');
							 set_sum_value( 'amount_sum', 'amount_');
							 set_sum_value( 'rate_sum', 'rate_');
                            </script>


            </form>

        </fieldset>
   </div>
<?
if ($hid_fab_cons_in_quotation_variable==2)
{
	if ($body_part_id==1)
    {
?>


<div align="center" style="width:100%;" >
<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_msmnt_cost" rules="all">
                	<thead>
                        <tr>
                        	<th  colspan="3">Body</th><th colspan="3">Sleeve </th><th colspan="2">1/2 Chest</th><th width="">Total</th>

                        </tr>
                    	<tr>
                        	<th width="80">Length</th><th  width="80">Sewing Margin</th><th  width="80">Hem Margin</th><th width="80"> Length</th><th width="80">Sewing Margin</th><th width="80">Hem Margin</th><th width="80">Length</th><th width="80">Sewing Margin</th> <th width="">Total</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?

					//$data_array=sql_select("select id,wo_pri_quo_fab_co_dtls_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length,half_chest_sewing_margin, total from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");
					//body_length,body_sewing_margin,body_hem_margin,sleeve_length,sleeve_sewing_margin,sleeve_hem_margin,half_chest_length,half_chest_sewing_margin
					$data_array=explode('__',$msmnt_breack_downn);
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
                            	<tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="bodylength_<? echo $i;?>"  name="bodylength_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[0]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="bodysewingmargin_<? echo $i;?>"    name="bodysewingmargin_<? echo $i;?>"  class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[1]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="bodyhemmargin_<? echo $i;?>" name="bodyhemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value=" <? echo $data[2]; ?> "/>
                                    </td>
                                    <td>
                                    <input type="text" id="sleevelength_<? echo $i;?>"  name="sleevelength_<? echo $i;?>" class="text_boxes_numeric" style="width:65px"  onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[3]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="sleevesewingmargin_<? echo $i;?>"  name="sleevesewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[4]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="sleevehemmargin_<? echo $i;?>"  name="sleevehemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[5]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="chestlenght_<? echo $i;?>"  name="chestlenght_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[6]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="chestsewingmargin_<? echo $i;?>"  name="chestsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[7]; ?>" />
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_<? echo $i;?>"  name="totalcons_<? echo $i;?>" class="text_boxes_numeric" style="width:150px" readonly value="<? echo $data[8]; ?>" />
                                    </td>
                                </tr>

                            <?

						}
					}
					else
					{
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="bodylength_1"  name="bodylength_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="bodysewingmargin_1"    name="bodysewingmargin_1"  class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="bodyhemmargin_1" name="bodyhemmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="sleevelength_1"  name="sleevelength_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="sleevesewingmargin_1"  name="sleevesewingmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="sleevehemmargin_1"  name="sleevehemmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                     <td>
                                    <input type="text" id="chestlenght_1"  name="chestlenght_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="chestsewingmargin_1"  name="chestsewingmargin_1" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_1"  name="totalcons_1" class="text_boxes_numeric" style="width:150px" readonly>
                                    </td>
                                </tr>
                    <? } ?>
                </tbody>
                </table>


            </form>

        </fieldset>
   </div>

<?
	}
	if($body_part_id==20)
	{
	?>
		<div align="center" style="width:100%;" >
<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_msmnt_cost" rules="all">
                	<thead>
                        <tr>
                        	<th  colspan="2">Front Rise</th><th colspan="2">West Band</th><th colspan="3">In Seam</th><th colspan="2"> Half Thai</th><th >Total</th>

                        </tr>
                    	<tr>
                        	<th width="70">Length</th><th  width="70">Sewing Margin</th><th  width="70">Length</th><th width="70"> Sewing Margin</th><th width="70">Length</th><th width="70">Sewing Margin</th><th width="70">Hem Margin</th><th width="70">Length</th> <th width="70">Sewing Margin</th><th width="">Total</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?

					//$data_array=sql_select("select id,wo_pri_quo_fab_co_dtls_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length,half_chest_sewing_margin, total from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id='$updateid_fc'");

					$data_array=explode('__',$msmnt_breack_downn);
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
                            	<tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="frontriselength_<? echo $i;?>"  name="frontriselength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[0]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="frontrisesewingmargin_<? echo $i;?>"    name="frontrisesewingmargin_<? echo $i;?>"  class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[1]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="westbandlength_<? echo $i;?>" name="westbandlength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[2]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="westbandsewingmargin_<? echo $i;?>"  name="westbandsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" value="<? echo $data[3]; ?>" onChange="calculate_measurement_top(<? echo $i;?>)"/ >
                                    </td>
                                    <td>
                                    <input type="text" id="inseamlength_<? echo $i;?>"  name="inseamlength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[4]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="inseamsewingmargin_<? echo $i;?>"  name="inseamsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[5]; ?>">

                                    </td>
                                    <td>
                                    <input type="text" id="inseamhemmargin_<? echo $i;?>"  name="inseamhemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[6]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthailength_<? echo $i;?>"  name="halfthailength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>)" value="<? echo $data[7]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthaisewingmargin_<? echo $i;?>"  name="halfthaisewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px"  value="<? echo $data[8]; ?>" onChange="calculate_measurement_top(<? echo $i;?>)">
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_<? echo $i;?>"  name="totalcons_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" readonly value="<? echo $data[9]; ?>">
                                    </td>
                                </tr>

                            <?

						}
					}
					else
					{
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="frontriselength_1"  name="frontriselength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="frontrisesewingmargin_1"    name="frontrisesewingmargin_1"  class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="westbandlength_1" name="westbandlength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="westbandsewingmargin_1"   name="westbandsewingmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>

                                    <td>
                                    <input type="text" id="inseamlength_1"  name="inseamlength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="inseamsewingmargin_1"  name="inseamsewingmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                     <td>
                                    <input type="text" id="inseamhemmargin_1"  name="inseamhemmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthailength_1"  name="halfthailength_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)">
                                    </td>
                                    <td>
                                    <input type="text" id="halfthaisewingmargin_1"  name="halfthaisewingmargin_1" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(1)" >
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_1"  name="totalcons_1" class="text_boxes_numeric" style="width:55px" readonly>
                                    </td>
                                </tr>
                    <? } ?>
                </tbody>
                </table>


            </form>

        </fieldset>
   </div>
   <?
	}
	?>

	<?
}
?>
<div align="center" style="width:100%;" >
<fieldset>
                <table width="810" cellspacing="0" class="" border="0" rules="all">
                	 <tr>
                        <td align="center" width="100%" class="button_container"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td>
                    </tr>
                </table>
                </fieldset>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="open_marker_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
<script>
var aa=parent;
function calculate_marker_length()
{
	var cbo_costing_per_id=parent.document.getElementById('cbo_costing_per_id').value;
	if (cbo_costing_per_id==1) // knit type
	{
		var dzn_mult=1*12;
	}
	else if (cbo_costing_per_id==2) // knit type
	{
		var dzn_mult=1*1;
	}
	else if (cbo_costing_per_id==3) // knit type
	{
		var dzn_mult=2*12;
	}
	else if (cbo_costing_per_id==4) // knit type
	{
		var dzn_mult=3*12;
	}
	else if (cbo_costing_per_id==5) // knit type
	{
		var dzn_mult=4*12;
	}
	else
	{
		dzn_mult=0;
	}
	var txt_marker_yds= (document.getElementById('txt_marker_yds').value)*1;
	var txt_marker_inch= (document.getElementById('txt_marker_inch').value)*1;
	var txt_gmt_pcs= (document.getElementById('txt_gmt_pcs').value)*1;
	var txt_marker_dia= (document.getElementById('txt_marker_dia').value)*1;
	var txt_marker_gsm= (document.getElementById('txt_marker_gsm').value)*1;

	var txt_marker_length_yds=(txt_marker_inch/36)+txt_marker_yds;
	var txt_marker_length_yds2=(txt_marker_length_yds/txt_gmt_pcs)*dzn_mult;
	txt_marker_length_yds3= number_format_common( txt_marker_length_yds2, 5, 0) ;
	document.getElementById('txt_marker_length_yds').value=txt_marker_length_yds3;
	var txt_marker_net_fab_cons=((txt_marker_length_yds3*36*2.54)/dzn_mult)*(txt_marker_dia*2*2.54*dzn_mult*txt_marker_gsm);
	var txt_marker_net_fab_cons2=txt_marker_net_fab_cons/10000000;
	document.getElementById('txt_marker_net_fab_cons').value=number_format_common(txt_marker_net_fab_cons2,5,0);
	//aa.document.getElementById('cons_1').value=number_format_common(txt_marker_net_fab_cons2,1,0);
	//aa.copy_value(number_format_common(txt_marker_net_fab_cons2,5,0),'cons_',1)
}

function js_set_value()
{
	var marker_breack_down="";
	if(form_validation('txt_marker_dia*txt_marker_yds*txt_marker_inch*txt_gmt_pcs*txt_marker_length_yds*txt_marker_gsm*txt_marker_net_fab_cons','Marker Dia*Marker Yds*Marker Inch*Gmt Pcs*Marker Length*Marker Gsm*Marker Net Fabric')==false){
		return;
	}
	else{
		var txt_marker_dia=$('#txt_marker_dia').val();
		var txt_marker_yds=$('#txt_marker_yds').val();
		var txt_marker_inch=$('#txt_marker_inch').val();
		var txt_gmt_pcs=$('#txt_gmt_pcs').val();

		var txt_marker_length_yds=$('#txt_marker_length_yds').val();
		var txt_marker_net_fab_cons=$('#txt_marker_net_fab_cons').val();
		marker_breack_down+=txt_marker_dia+'_'+txt_marker_yds+'_'+txt_marker_inch+'_'+txt_gmt_pcs+'_'+txt_marker_length_yds+'_'+txt_marker_net_fab_cons;
	}
	document.getElementById('marker_breack_down').value=marker_breack_down;
	parent.emailwindow.hide();
}
</script>
</head>
<body>
<input type="hidden" id="marker_breack_down" name="marker_breack_down"  value="<? echo $marker;?>"/>
<table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_marker_cost" rules="all">
              <thead>
                    <tr>
                        <th  width="100"  rowspan="2">Marker Dia (Inch)</th><th  width="100" colspan="2">Marker Length</th><th  width="110"  rowspan="2">Gmts. Size Ratio (Pcs)</th><th width="90"  rowspan="2">Marker Length -Yds (1Dzn Gmts)</th><th width="110"  rowspan="2">GSM</th><th width="110"  rowspan="2">Net Fab Cons</th><th></th>

                    </tr>
                    <tr>
                        <th  width="100">Yds</th><th  width="100">Inch</th><th></th>

                    </tr>
              </thead>
              <tbody>
              <?
			  $marker_breack_down_arr=explode("_",$marker);
			  ?>
              <tr>
              <td><input type="text" id="txt_marker_dia"  name="txt_marker_dia" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[0];  ?>"> </td>
              <td><input type="text" id="txt_marker_yds"  name="txt_marker_yds" class="text_boxes_numeric" style="width:90px"  onChange="calculate_marker_length()" value="<? echo $marker_breack_down_arr[1];  ?>"></td>
              <td><input type="text" id="txt_marker_inch"  name="txt_marker_inch" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[2];  ?>"></td>
              <td><input type="text" id="txt_gmt_pcs"  name="txt_gmt_pcs" class="text_boxes_numeric" style="width:110px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[3];  ?>"></td>
              <td><input type="text" id="txt_marker_length_yds"  name="txt_marker_length_yds" class="text_boxes_numeric" style="width:110px" readonly  value="<? echo $marker_breack_down_arr[4];  ?>"></td>
              <td><input type="text" id="txt_marker_gsm"  name="txt_marker_gsm" class="text_boxes_numeric" readonly style="width:110px"  value="<? //echo $txtgsmweight; ?>"></td>
              <td><input type="text" id="txt_marker_net_fab_cons"  name="txt_marker_net_fab_cons" class="text_boxes_numeric" style="width:110px"  value="<? echo $marker_breack_down_arr[5];  ?>"></td>
              <td></td>
              </tr>
              </tbody>
          </table>
             <table width="780" cellspacing="0" class="" border="0" rules="all">
                	 <tr>
                        <td align="center" width="100%" class="button_container"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td>
                    </tr>
                </table>
</body>
<script>
document.getElementById('txt_marker_gsm').value=parent.document.getElementById('txt_gsm').value;
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="delete_row_fabric_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$ex_data=explode("***",$data);
	$fabupdateid=$ex_data[0];
	$data_component=$ex_data[1];
	 //echo "10**";
	//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
	$flag=1;
	/*if($flag==1)
	{
		$sql_quo_fab_dtls_del="insert into wo_pri_quo_fab_cost_dtls_del( id, quotation_id, quo_fab_dtls_id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down, deleted_by, delete_date)
	select
		 '', quotation_id, id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_fabric_cost_dtls where id=".$fabupdateid."";

		$rID_quo_fab_dtls_del=execute_query($sql_quo_fab_dtls_del,0);
		if($rID_quo_fab_dtls_del==1) $flag=1; else $flag=0;
	}*/
	//die;
	if($flag==1)
	{
		$rID_dtls=execute_query( "update wo_pri_quo_fabric_cost_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'  where id =".$fabupdateid." and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
	}
	if($flag==1)
	{
		$rID_co=execute_query( "delete from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id =".$fabupdateid."",0);
		if($rID_co==1 && $flag==1) $flag=1; else $flag=0;
	}

	if($flag==1 && $fabupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];

		//echo $dtls_id;
		if($dtls_respone==1) $flag=1; else $flag=0;
	}
	//echo  '10**'.$flag.'=='.$rID_quo_fab_dtls_del.'=='.$rID_dtls.'=='.$rID_co.'=='.$dtls_respone; die;
	//echo $sql_quo_fab_dtls_del; die;

	//echo $rID_quo_fab_dtls_del.'='.$rID_dtls.'='.$rID_co.'='.$dtls_respone.'='.$flag.'='.$db_type; die;

	if($db_type==0)
	{
		if($flag==1) mysql_query("COMMIT"); else mysql_query("ROLLBACK");
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) oci_commit($con); else oci_rollback($con);
	}
	disconnect($con);
}

if($action=="delete_row_yarn_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$ex_data=explode("***",$data);
	$yarnupdateid=$ex_data[0];
	$data_component=$ex_data[1];
	/*$rID_de1=execute_query( "INSERT INTO wo_pri_quo_fab_yarn_cost_dtls_bc (id,quotation_id,	count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
    SELECT a.id,a.quotation_id,	a.count_id,a.copm_one_id,a.percent_one,a.copm_two_id,a.percent_two,a.type_id,a.cons_ratio,a.cons_qnty,a.rate,a.amount,a.inserted_by,	a.insert_date,a.updated_by,a.update_date,a.status_active,a.is_deleted
    FROM wo_pri_quo_fab_yarn_cost_dtls a WHERE a.id =".$data."",0);*/
	//echo "select id from  wo_pri_quo_fab_yarn_cost_dtls where  id =".$data."";
	$flag=1;

	/*if($flag==1)
	{
		$sql_quo_yarn_dtls_del="insert into wo_pri_quo_fab_yarn_dtls_del( id, quotation_id, quo_yarn_dtls_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id, deleted_by, delete_date)
	select
		'', quotation_id,id,  count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_fab_yarn_cost_dtls where id=".$yarnupdateid."";

		$sql_quo_yarn_dtls_del=execute_query($sql_quo_yarn_dtls_del,0);
		if($sql_quo_yarn_dtls_del==1) $flag=1; else $flag=0;
	}*/
	$rID_de1=execute_query( "update wo_pri_quo_fab_yarn_cost_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'  where id =".$yarnupdateid." and status_active=1 and is_deleted=0",1);
	//$rID_de1=execute_query("delete from wo_pri_quo_fab_yarn_cost_dtls where id =".$yarnupdateid."",0);
	if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;

	if($flag==1 && $yarnupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1 && $flag==1) $flag=1; else $flag=0;
	}

	if($db_type==0)
	{
		if($flag==1) mysql_query("COMMIT"); else mysql_query("ROLLBACK");
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) oci_commit($con); else oci_rollback($con);
	}
	disconnect($con);
}

if($action=="conversion_from_chart")
{

	$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data'  and variable_list=21 and status_active=1 and is_deleted=0");
	if($conversion_from_chart=="")
	{
		$conversion_from_chart=2;
	}
	echo trim($conversion_from_chart);

}

if($action=="delete_row_conversion_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$ex_data=explode("***",$data);
	$conupdateid=$ex_data[0];
	$data_component=$ex_data[1];

	$flag=1;

	/*if($flag==1)
	{
		$sql_quo_conv_dtls_del="insert into wo_pri_quo_fab_conv_dtls_del( id, quotation_id, quo_conv_dtls_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss, deleted_by, delete_date)
	select
		'', quotation_id, id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_fab_conv_cost_dtls where id=".$conupdateid."";

		$sql_quo_conv_dtls_del=execute_query($sql_quo_conv_dtls_del,0);
		if($sql_quo_conv_dtls_del==1) $flag=1; else $flag=0;
	}*/
	$rID_de1=execute_query( "update wo_pri_quo_fab_conv_cost_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'  where id =".$conupdateid."  and status_active=1 and is_deleted=0",1);
	//$rID_de1=execute_query("delete from wo_pri_quo_fab_conv_cost_dtls where id =".$conupdateid."",0);
	if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;

	if($flag==1 && $yarnupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1) $flag=1; else $flag=0;
	}
	if($db_type==0)
	{
		if($flag==1) mysql_query("COMMIT"); else mysql_query("ROLLBACK");
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) oci_commit($con); else oci_rollback($con);
	}
	disconnect($con);
}

if($action=="delete_row_trim_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$ex_data=explode("***",$data);
	$trimupdateid=$ex_data[0];
	$data_component=$ex_data[1];

	$flag=1; $dtls_respone=0; $dtls_id=0;

	/*if($flag==1)
	{
		$sql_quo_trim_dtls_del="insert into wo_pri_quo_trim_dtls_del( id, quotation_id, quo_trim_dtls_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, seq, description, deleted_by, delete_date)
	select
		 '', quotation_id, id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, seq, description, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_trim_cost_dtls where id=".$trimupdateid."";

		$sql_quo_trim_dtls_del=execute_query($sql_quo_trim_dtls_del,0);
		if($sql_quo_trim_dtls_del==1) $flag=1; else $flag=0;
	}*/

	if($flag==1)
	{
		$rID_de1=execute_query( "update wo_pri_quo_trim_cost_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'  where id =".$trimupdateid."  and status_active=1 and is_deleted=0",1);
		//$rID_de1=execute_query( "delete from  wo_pri_quo_trim_cost_dtls where id =".$trimupdateid."",0);
		if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
	}
	//echo $flag;
	if($flag==1 && $trimupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		//echo $res_data_component;
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=trim($ex_dataComponent[0]);
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1 && $flag==1) $flag=1; else $flag=0;
	}

	//echo $flag;
	if($db_type==0)
	{
		if($flag==1) { mysql_query("COMMIT"); } else { mysql_query("ROLLBACK"); }
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) { oci_commit($con);} else { oci_rollback($con); }
	}
	disconnect($con);
}

if($action=="delete_row_embellishment_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$ex_data=explode("***",$data);
	$embupdateid=$ex_data[0];
	$data_component=$ex_data[1];

	$flag=1;

	/*if($flag==1)
	{
		$sql_quo_emb_dtls_del="insert into wo_pri_quo_embe_dtls_del( id, quotation_id, quo_emb_dtls_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, deleted_by, delete_date)
	select
		'', quotation_id, id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_embe_cost_dtls where id=".$embupdateid."";

		$sql_quo_emb_dtls_del=execute_query($sql_quo_emb_dtls_del,0);
		if($sql_quo_emb_dtls_del==1) $flag=1; else $flag=0;
	}*/
	if($flag==1)
	{
		$rID_de1=execute_query( "update wo_pri_quo_embe_cost_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'  where id =".$embupdateid."  and status_active=1 and is_deleted=0",1);
		//$rID_de1=execute_query( "delete from wo_pri_quo_embe_cost_dtls where id =".$embupdateid."",0);
		if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
	}

	if($flag==1 && $embupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1) $flag=1; else $flag=0;
	}

	if($db_type==0)
	{
		if($flag==1) mysql_query("COMMIT"); else mysql_query("ROLLBACK");
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) oci_commit($con); else oci_rollback($con);
	}
	disconnect($con);
}

if($action=="delete_row_wash_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$ex_data=explode("***",$data);
	$washupdateid=$ex_data[0];
	$data_component=$ex_data[1];

	$flag=1;

	/*if($flag==1)
	{
		$sql_quo_wash_dtls_del="insert into wo_pri_quo_embe_dtls_del( id, quotation_id, quo_emb_dtls_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, deleted_by, delete_date)
	select
		'', quotation_id, id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_embe_cost_dtls where id=".$washupdateid."";

		$sql_quo_wash_dtls_del=execute_query($sql_quo_wash_dtls_del,0);
		if($sql_quo_wash_dtls_del==1) $flag=1; else $flag=0;
	}*/
	$rID_de1=execute_query( "update wo_pri_quo_embe_cost_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =".$washupdateid."  and status_active=1 and is_deleted=0",1);
	//$rID_de1=execute_query( "delete from wo_pri_quo_embe_cost_dtls where  id =".$washupdateid."",0);
	if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;

	if($flag==1 && $washupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1) $flag=1; else $flag=0;
	}

	if($db_type==0)
	{
		if($flag==1) mysql_query("COMMIT"); else mysql_query("ROLLBACK");
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) oci_commit($con); else oci_rollback($con);
	}
	disconnect($con);
}

if($action=="delete_row_comarcial_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$ex_data=explode("***",$data);
	$comarcialupdateid=$ex_data[0];
	$data_component=$ex_data[1];

	$flag=1;

	/*if($flag==1)
	{
		$sql_quo_coml_dtls_del="insert into wo_pri_quo_comarcial_dtls_del( id, quotation_id, quo_coma_dtls_id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, deleted_by, delete_date)
	select
		'', quotation_id, id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_pri_quo_comarcial_cost_dtls where id=".$comarcialupdateid."";

		$sql_quo_coml_dtls_del=execute_query($sql_quo_coml_dtls_del,0);
		if($sql_quo_coml_dtls_del==1) $flag=1; else $flag=0;
	}*/
	$rID_de1=execute_query( "update wo_pri_quo_comarcial_cost_dtls set status_active=0, is_deleted=1 where id =".$comarcialupdateid."  and status_active=1 and is_deleted=0",1);
	//$rID_de1=execute_query( "delete from wo_pri_quo_comarcial_cost_dtls where  id =".$comarcialupdateid."",0);
	if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;

	if($flag==1 && $comarcialupdateid!="")
	{
		$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
		$ex_dataComponent=explode("__",$res_data_component);
		$dtls_respone=$ex_dataComponent[0];
		$dtls_id=$ex_dataComponent[1];
		if($dtls_respone==1) $flag=1; else $flag=0;
	}

	if($db_type==0)
	{
		if($flag==1) mysql_query("COMMIT"); else mysql_query("ROLLBACK");
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1) oci_commit($con); else oci_rollback($con);
	}
	disconnect($con);
}

if ($action=="save_update_delet_fabric_cost_dtls")
{
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}
		else{
			$approved=$approved;
		}
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
	}
	if($operation==0)
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		$id=return_next_id( "id", "wo_pri_quo_fabric_cost_dtls", 1 ) ;
		$field_array="id, quotation_id, item_number_id, body_part_id, body_part_type, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, cons_breack_down ,msmnt_break_down, yarn_breack_down, width_dia_type";

	    $field_array1="id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs,rate,amount, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
	    $field_array3="id, quotation_id,fabric_cost_dtls_id, count_id, copm_one_id, percent_one, type_id,yarn_finish,yarn_spinning_system, cons_ratio, cons_qnty,certification,yarn_color_id,yarn_code,yarn_id, inserted_by, insert_date, status_active, is_deleted";
		 $add_comma=0;
		 $flag=1;
		 $id1=return_next_id( "id", "wo_pri_quo_fab_co_avg_con_dtls", 1 ) ;
		 $fab_yarn_cost_dtls_id=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtbodypart="txtbodypart_".$i;
			 $txtbodyparttype="txtbodyparttype_".$i;
			 $cbofabricnature="cbofabricnature_".$i;
			 $cbocolortype="cbocolortype_".$i;
			 $libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			 $txtconstruction="txtconstruction_".$i;
			 $txtcomposition="txtcomposition_".$i;
			 $fabricdescription="fabricdescription_".$i;
			 $txtgsmweight="txtgsmweight_".$i;
			 $txtconsumption="txtconsumption_".$i;
			 $cbofabricsource="cbofabricsource_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtfinishconsumption="txtfinishconsumption_".$i;
			 $txtavgprocessloss="txtavgprocessloss_".$i;
			 $cbostatus="cbostatus_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $msmntbreackdown="msmntbreackdown_".$i;
			 $yarnbreackdown="yarnbreackdown_".$i;
			 $markerbreackdown="markerbreackdown_".$i;
			 $processlossmethod="processlossmethod_".$i;
			 $consumptionbasis="consumptionbasis_".$i;
			 $cbowidthdiatype="cbowidthdiatype_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$txtbodyparttype.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$txtconstruction.",".$$txtcomposition.",".$$fabricdescription.",".$$txtgsmweight.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$yarnbreackdown.",".$$cbowidthdiatype.")";
			$new_array_size=array();
		    $new_array_color=array();
			$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
			$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
			//$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
			$markerbreackdown_array=explode('**',str_replace("'",'',$$markerbreackdown));
			$counter=0;
			for($c=0;$c < count($consbreckdown_array);$c++)
			{
				$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
				$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
				$markerbreackdownarr=explode('_',$markerbreackdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[0])!="")
					{
						if (!in_array($$consbreckdownarr[0],$new_array_size))
						{
							$size_id = return_id($consbreckdownarr[0], $size_library, "lib_size", "id,size_name","314");
							$new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
						}
						else $size_id =  array_search($consbreckdownarr[0], $new_array_size);
					}
					else $size_id =0;

				if ($add_comma!=0) $data_array1 .=",";
				if(str_replace("'",'',$$txtbodypart)*1==1)
				{
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				}
				else if(str_replace("'",'',$$txtbodypart)*1==20)
				{
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				}
				else
				{
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				}
				$id1=$id1+1;
				$add_comma++;
			}
			//Yarn break down			
			if(str_replace("'",'',$$cbofabricsource)==1 && str_replace("'",'',$$yarnbreackdown)!="")
			{
				$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
				for($c=0;$c < count($yarnbreckdown_array);$c++)
				{
					$counter++;
					$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
					if(str_replace("'",'',$$cbofabricnature)==2)

					{
						$cons=def_number_format(((str_replace("'",'',$$txtconsumption)*$yarnbreckdownarr[4])/100),5,"");

					}
					if(str_replace("'",'',$$cbofabricnature)==3)
					{
						$cons=def_number_format(((str_replace("'",'',$$txtgsmweight)*$yarnbreckdownarr[4])/100),5,"");

					}
					if ($add_comma_yarn!=0)
					{
						$data_array3 .=",";
						//$data_array4 .=",";
					}


					// Yarn Id created dynamically from bellow data
					$yarn_id_new='';
					$yarn_color_id='';
					$yarn_code='';

					if(count($yarnbreckdownarr)>9)
					{
						$yarn_color_id=$yarnbreckdownarr[9];
					}
					if(count($yarnbreckdownarr)>10)
					{
						$yarn_code=$yarnbreckdownarr[10];
					}

					// Yarn Id end


					$data_array3 .="(".$fab_yarn_cost_dtls_id.",".$update_id.",".$id.",'".$yarnbreckdownarr[0]."','".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[5]."','".$yarnbreckdownarr[6]."','".$yarnbreckdownarr[4]."','".$cons."','".$yarnbreckdownarr[7]."','".$yarn_color_id."','".$yarn_code."','".$yarn_id_new."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					if ($data_array3!="" && $counter==100)
					{
						$rID_3=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
						$counter=0;
						$data_array3="";
						$add_comma_yarn=0;
						if( $rID_3 && $flag==1) { $flag=1;  } else { $flag=0;}
					}
					$fab_yarn_cost_dtls_id=$fab_yarn_cost_dtls_id+1;
					$add_comma_yarn++;
				}
			}
			if ($data_array3!="" && $counter!=100)
			{
				//echo "10**INSERT INTO wo_pri_quo_fab_yarn_cost_dtls ($field_array3) values $data_array3"; die;
				$rID_3_1=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
				$counter=0;
				$data_array3="";
				$add_comma_yarn=0;
				if( $rID_3_1 && $flag==1) { $flag=1;  } else { $flag=0;}
			}
			$id=$id+1;
		}
		$rID1=sql_insert("wo_pri_quo_fabric_cost_dtls",$field_array,$data_array,0);
		if( $rID1 && $flag==1) { $flag=1;  } else { $flag=0;}
		$rID=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
		if( $rID && $flag==1) { $flag=1;  } else { $flag=0;}
		//echo $rID1."======".$rID; die;
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount";
			$data_array5="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$tot_yarn_needed.",".$txtwoven_sum.",".$txtknit_sum.",".$txtamount_sum.")";
			$rID_id5=sql_insert("wo_pri_quo_sum_dtls",$field_array5,$data_array5,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			//$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="fab_yarn_req_kg*fab_woven_req_yds*fab_knit_req_kg*fab_amount";
			$data_array5 ="".$tot_yarn_needed."*".$txtwoven_sum."*".$txtknit_sum."*".$txtamount_sum."";
			$rID_in5_1=sql_update("wo_pri_quo_sum_dtls",$field_array5,$data_array5,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID."**$rID1**$rID_3**$rID_3_1**$rID_id5**$rID_in5_1";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID."**".$rID."**$rID1**$rID_3**$rID_3_1**$rID_id5**$rID_in5_1";;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID."**".$rID."**$rID1**$rID_3**$rID_3_1**$rID_id5**$rID_in5_1";;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
	    $con = connect();
	    $flag=1;
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$yarnString=array(); $yarn_rate_array=array(); $yarn_color_array=array(); $yarn_sup_array=array();
		$sql_yarn_rate=sql_select("select fabric_cost_dtls_id, quotation_id, count_id, copm_one_id,percent_one,type_id,cons_ratio,rate,supplier_id,certification from wo_pri_quo_fab_yarn_cost_dtls where  quotation_id =".$update_id." and status_active=1 and is_deleted=0");
		foreach($sql_yarn_rate as $row_yarn_rate)
		{
			$yarn_rate_array[$row_yarn_rate[csf('fabric_cost_dtls_id')]][$row_yarn_rate[csf('quotation_id')]][$row_yarn_rate[csf('count_id')]][$row_yarn_rate[csf('copm_one_id')]][$row_yarn_rate[csf('type_id')]]=$row_yarn_rate[csf('rate')];
			//$yarn_color_array[$row_yarn_rate[csf('fabric_cost_dtls_id')]][$row_yarn_rate[csf('quotation_id')]][$row_yarn_rate[csf('count_id')]][$row_yarn_rate[csf('copm_one_id')]][$row_yarn_rate[csf('type_id')]]=$row_yarn_rate[csf('color')];
			$yarn_sup_array[$row_yarn_rate[csf('fabric_cost_dtls_id')]][$row_yarn_rate[csf('quotation_id')]][$row_yarn_rate[csf('count_id')]][$row_yarn_rate[csf('copm_one_id')]][$row_yarn_rate[csf('type_id')]]=$row_yarn_rate[csf('supplier_id')];
			$certification=0;
			if(!empty($row_yarn_rate[csf('certification')]))
			{
				$certification=$row_yarn_rate[csf('certification')];
			}

			if(isset($yarnString[$row_yarn_rate[csf('fabric_cost_dtls_id')]]))
			{
				$yarnString[$row_yarn_rate[csf('fabric_cost_dtls_id')]].= "__".$row_yarn_rate[csf('count_id')]."_".$row_yarn_rate[csf('copm_one_id')]."_".$row_yarn_rate[csf('percent_one')]."_".$row_yarn_rate[csf('type_id')]."_".$row_yarn_rate[csf('cons_ratio')]."_".$certification;
			}
			else
			{
				$yarnString[$row_yarn_rate[csf('fabric_cost_dtls_id')]]= $row_yarn_rate[csf('count_id')]."_".$row_yarn_rate[csf('copm_one_id')]."_".$row_yarn_rate[csf('percent_one')]."_".$row_yarn_rate[csf('type_id')]."_".$row_yarn_rate[csf('cons_ratio')]."_".$certification;
			}
		}
		unset($sql_yarn_rate);
		$rID_de2=execute_query( "delete from wo_pri_quo_fab_yarn_cost_dtls where  quotation_id =".$update_id."",0);

		$field_array="id, quotation_id, item_number_id, body_part_id, body_part_type, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, cons_breack_down, msmnt_break_down, yarn_breack_down, width_dia_type";
		$field_array_up="quotation_id*item_number_id*body_part_id*body_part_type*fab_nature_id*color_type_id*lib_yarn_count_deter_id*construction*composition*fabric_description*gsm_weight*avg_cons*fabric_source*rate*amount*avg_finish_cons*avg_process_loss*updated_by*update_date*status_active*is_deleted*company_id*costing_per*fab_cons_in_quotat_varia*process_loss_method*cons_breack_down*msmnt_break_down*yarn_breack_down*width_dia_type";

		$field_array1="id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs,rate,amount, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
		$field_array3="id, quotation_id, fabric_cost_dtls_id, count_id, copm_one_id, percent_one, type_id,yarn_finish,yarn_spinning_system, cons_ratio, cons_qnty, supplier_id, rate, amount,certification,yarn_color_id,yarn_code,yarn_id, inserted_by, insert_date, status_active, is_deleted";
		 $add_co=0;
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_fabric_cost_dtls", 1 ) ;
		 $id1=return_next_id( "id", "wo_pri_quo_fab_co_avg_con_dtls", 1 ) ;
		 $fab_yarn_cost_dtls_id=return_next_id( "id", " wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtbodypart="txtbodypart_".$i;
			 $txtbodyparttype="txtbodyparttype_".$i;
			 $cbofabricnature="cbofabricnature_".$i;
			 $cbocolortype="cbocolortype_".$i;
			 $libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			 $oldlibyarncountdeterminationid="oldlibyarncountdeterminationid_".$i;
			 $txtconstruction="txtconstruction_".$i;
			 $txtcomposition="txtcomposition_".$i;
			 $fabricdescription="fabricdescription_".$i;
			 $txtgsmweight="txtgsmweight_".$i;
			 $txtconsumption="txtconsumption_".$i;
			 $cbofabricsource="cbofabricsource_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtfinishconsumption="txtfinishconsumption_".$i;
			 $txtavgprocessloss="txtavgprocessloss_".$i;
			 $cbostatus="cbostatus_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $msmntbreackdown="msmntbreackdown_".$i;
			 $yarnbreackdown="yarnbreackdown_".$i;
			 $markerbreackdown="markerbreackdown_".$i;
			 $updateid="updateid_".$i;
			 $processlossmethod="processlossmethod_".$i;
			 $consumptionbasis="consumptionbasis_".$i;
			 $cbowidthdiatype="cbowidthdiatype_".$i;

			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up[str_replace("'",'',$$updateid)] =explode("*",("".$update_id."*".$$cbogmtsitem."*".$$txtbodypart."*".$$txtbodyparttype."*".$$cbofabricnature."*".$$cbocolortype."*".$$libyarncountdeterminationid."*".$$txtconstruction."*".$$txtcomposition."*".$$fabricdescription."*".$$txtgsmweight."*".$$txtconsumption."*".$$cbofabricsource."*".$$txtrate."*".$$txtamount."*".$$txtfinishconsumption."*".$$txtavgprocessloss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cbostatus."*0*".$cbo_company_name."*".$cbo_costing_per."*".$$consumptionbasis."*".$$processlossmethod."*".$$consbreckdown."*".$$msmntbreackdown."*".$$yarnbreackdown."*".$$cbowidthdiatype.""));

				$rID=execute_query( "delete from wo_pri_quo_fab_co_avg_con_dtls where  wo_pri_quo_fab_co_dtls_id =".$$updateid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
				//$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
				$markerbreackdown_array=explode('**',str_replace("'",'',$$markerbreackdown));
				$counter=0;
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$new_array_size=array();
		            $new_array_color=array();
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
					$markerbreackdownarr=explode('_',$markerbreackdown_array[$c]);
					
					 if(str_replace("'","",$consbreckdownarr[0])!="")
					{
						if (!in_array($$consbreckdownarr[0],$new_array_size))
						{
							$size_id = return_id($consbreckdownarr[0], $size_library, "lib_size", "id,size_name","314");
							$new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
						}
						else $size_id =  array_search($consbreckdownarr[0], $new_array_size);
					}
					else $size_id =0;
					
					if ($add_comma!=0) $data_array1 .=",";
					if(str_replace("'",'',$$txtbodypart)*1==1)
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else if(str_replace("'",'',$$txtbodypart)*1==20)
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					$id1=$id1+1;
					$add_comma++;
				}

				// yarn break down =======================================================================================================
                if(str_replace("'",'',$$cbofabricsource)==1 && str_replace("'",'',$$yarnbreackdown)!="")
				{
					$yarnbreckdown_array=array();
					$counter++;
					if(str_replace("'",'',$$oldlibyarncountdeterminationid)!=str_replace("'",'',$$libyarncountdeterminationid))
					{
						$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					}
					if(str_replace("'",'',$$oldlibyarncountdeterminationid)==str_replace("'",'',$$libyarncountdeterminationid))
					{
						$yarnbreckdown_array=explode('__',$yarnString[str_replace("'","",$$updateid)]);
					}
					if($yarnString[str_replace("'","",$$updateid)]=="" && str_replace("'",'',$$oldlibyarncountdeterminationid)==str_replace("'",'',$$libyarncountdeterminationid)){
						$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					}
					//$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					for($c=0;$c < count($yarnbreckdown_array);$c++)
					{
						$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
						if(str_replace("'",'',$$cbofabricnature)==2)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtconsumption)*$yarnbreckdownarr[4])/100),5,"");
						}
						if(str_replace("'",'',$$cbofabricnature)==3)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
						}
						if ($add_comma_yarn!=0)
						{
							$data_array3 .=",";
						}

						$rate=$yarn_rate_array[str_replace("'","",$$updateid)][str_replace("'","",$update_id)][$yarnbreckdownarr[0]][$yarnbreckdownarr[1]][$yarnbreckdownarr[3]];
						$sup=$yarn_sup_array[str_replace("'","",$$updateid)][str_replace("'","",$update_id)][$yarnbreckdownarr[0]][$yarnbreckdownarr[1]][$yarnbreckdownarr[3]];
						$amount=$cons*$rate;


						// Yarn Id created dynamically from bellow data
						$yarn_id_new='';
						$yarn_color_id='';
						$yarn_code='';

						if(count($yarnbreckdownarr)>9)
						{
							$yarn_color_id=$yarnbreckdownarr[9];
						}
						if(count($yarnbreckdownarr)>10)
						{
							$yarn_code=$yarnbreckdownarr[10];
						}

						// Yarn Id end


						$data_array3 .="(".$fab_yarn_cost_dtls_id.",".$update_id.",".$$updateid.",'".$yarnbreckdownarr[0]."','".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[5]."','".$yarnbreckdownarr[6]."','".$yarnbreckdownarr[4]."','".$cons."','".$sup."','".$rate."','".$amount."','".$yarnbreckdownarr[7]."','".$yarn_color_id."','".$yarn_code."','".$yarn_id_new."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

						if ( $data_array3!="" && $counter==100 )
						{
							$counter=0;
							$rID_3_0=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
							$data_array3="";
							$add_comma_yarn=0;
							if( $rID_3_0 && $flag==1) { $flag=1;  } else { $flag=0;}
						}
						$fab_yarn_cost_dtls_id=$fab_yarn_cost_dtls_id+1;
						$add_comma_yarn++;
					}// end for
					if ($data_array3!="" &&  $counter!=100 )
					{
						$counter=0;
						//echo "10**insert into wo_pri_quo_fab_yarn_cost_dtls "
						//echo "10**INSERT INTO wo_pri_quo_fab_yarn_cost_dtls ($field_array3) values $data_array3"; die;
						$rID_3_1=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
						$data_array3="";
						$add_comma_yarn=0;
						if( $rID_3_1 && $flag==1) { $flag=1;  } else { $flag=0;}
					}
				}
			}
			if(str_replace("'",'',$$updateid)=="")
			{
				if ($add_co!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$txtbodyparttype.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$txtconstruction.",".$$txtcomposition.",".$$fabricdescription.",".$$txtgsmweight.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$yarnbreackdown.",".$$cbowidthdiatype.")";
				// msmnt break down=================================================================================
				$new_array_size=array();
				$new_array_color=array();
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
				//$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
				$markerbreackdown_array=explode('**',str_replace("'",'',$$markerbreackdown));
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
					$markerbreackdownarr=explode('_',$markerbreackdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[0])!="")
					{
						if (!in_array($$consbreckdownarr[0],$new_array_size))
						{
							$size_id = return_id($consbreckdownarr[0], $size_library, "lib_size", "id,size_name","314");
							$new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
						}
						else $size_id =  array_search($consbreckdownarr[0], $new_array_size);
					}
					else $size_id =0;
					 
					if ($add_comma!=0) $data_array1 .=",";
					if(str_replace("'",'',$$txtbodypart)*1==1)
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else if(str_replace("'",'',$$txtbodypart)*1==20)
					{

						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$size_id."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					$id1=$id1+1;
					$add_comma++;
				}				
				// msmnt break down end =================================================================================
				// Yarn break down ==================================================================================================
                if(str_replace("'",'',$$cbofabricsource)==1 && str_replace("'",'',$$yarnbreackdown)!="")
				{
					$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					for($c=0;$c < count($yarnbreckdown_array);$c++)
					{
						$counter++;
						$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
						if(str_replace("'",'',$$cbofabricnature)==2)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtconsumption)*$yarnbreckdownarr[4])/100),5,"");
							//$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtconsumption)*$yarnbreckdownarr[4])/100),5,"");
						}
						if(str_replace("'",'',$$cbofabricnature)==3)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
							//$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
						}
						if ($add_comma_yarn!=0)
						{
							$data_array3 .=",";
						}

						// Yarn Id created dynamically from bellow data
						$yarn_id_new='';

						// Yarn Id end


						$data_array3 .="(".$fab_yarn_cost_dtls_id.",".$update_id.",".$id.",'".$yarnbreckdownarr[0]."','".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[5]."','".$yarnbreckdownarr[6]."','".$yarnbreckdownarr[4]."','".$cons."',0,0,0,'".$yarnbreckdownarr[7]."','".$yarnbreckdownarr[8]."','".$yarnbreckdownarr[9]."','".$yarn_id_new."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";						
						if ($data_array3!="" && $counter==100)
						{
							$counter=0;
							$rID_3_2=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
							$data_array3="";
							$data_array4="";
							$add_comma_yarn=0;
							//if( $rID_3 && $fail3==0) { $rID_in3=1;  } else { $rID_in3=0; $fail3=1; }
							if( $rID_3_2 && $flag==1) { $flag=1;  } else { $flag=0;}
						}
						$fab_yarn_cost_dtls_id=$fab_yarn_cost_dtls_id+1;
						$add_comma_yarn++;
					}
					if ($data_array3!="" && $counter!=100)
					{
						$counter=0;
						//echo "10**INSERT into wo_pri_quo_fab_yarn_cost_dtls ($field_array3) values $data_array3"; die;
						$rID_3_3=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
						$data_array3="";
						$data_array4="";
						$add_comma_yarn=0;
						//if( $rID_3 && $fail3==0) { $rID_in3=1;  } else { $rID_in3=0; $fail3=1; }
						if( $rID_3_3 && $flag==1) { $flag=1;  } else { $flag=0;}
					}
				}
			  $id=$id+1;
			  $add_co++;
			}
		 }
		$rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_fabric_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($data_array!="")
		{
			$rID=sql_insert("wo_pri_quo_fabric_cost_dtls",$field_array,$data_array,0);
			if( $rID && $flag==1) { $flag=1;  } else { $flag=0;}
		}
		$rID1=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
			if( $rID1 && $flag==1) { $flag=1;  } else { $flag=0;}
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount";
			$data_array5="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$tot_yarn_needed.",".$txtwoven_sum.",".$txtknit_sum.",".$txtamount_sum.")";
			$rID_id5=sql_insert("wo_pri_quo_sum_dtls",$field_array5,$data_array5,1);
			if( $rID_id5 && $flag==1) { $flag=1;  } else { $flag=0;}
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			//$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array5="fab_yarn_req_kg*fab_woven_req_yds*fab_knit_req_kg*fab_amount";
			$data_array5 ="".$tot_yarn_needed."*".$txtwoven_sum."*".$txtknit_sum."*".$txtamount_sum."";
			$rID_in5=sql_update("wo_pri_quo_sum_dtls",$field_array5,$data_array5,"quotation_id","".$update_id."",1);
			if( $rID_in5 && $flag==1) { $flag=1;  } else { $flag=0;}
		}
		update_comarcial_cost_q($update_id);
		//=======================sum End =================
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID."**$rID_up**$rID1**$rID_id5**$rID_in5**$rID_3_0**$rID_3_1**$rID_3_2**$rID_3_3**$rID_3**$data_array3";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$ex_data=explode("***",$data);
		$fabupdateid=$ex_data[0];
		$data_component=$ex_data[1];
		 //echo "10**";
		//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_fabric_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1) $flag=1; else $flag=0;
		if($flag==1)
		{
			$rID_co=execute_query( "delete from wo_pri_quo_fab_co_avg_con_dtls where quotation_id =".$update_id."",0);
			if($rID_co==1) $flag=1; else $flag=0;
		}
		if($flag==1)
		{
			$rID_de2=execute_query( "delete from wo_pri_quo_fab_yarn_cost_dtls where  quotation_id =".$update_id."",0);
			if($rID_de2==1) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delet_fabric_yarn_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
		$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}else{
			$approved=$approved;
		}
		if($approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
		}
	}
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id, count_id, copm_one_id, percent_one, type_id,yarn_finish,yarn_spinning_system, cons_ratio, cons_qnty,supplier_id, rate, amount,certification,yarn_color_id,yarn_code, inserted_by, insert_date, status_active, is_deleted";


		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $percentone="percentone_".$i;
			 //$cbocomptwo="cbocomptwo_".$i;
			// $percenttwo="percenttwo_".$i;
			 $cbotype="cbotype_".$i;
			 $consratio="consratio_".$i;
			 $consqnty="consqnty_".$i;
			 $txtrateyarn="txtrateyarn_".$i;
			 $txtamountyarn="txtamountyarn_".$i;
			 $cbostatusyarn="cbostatusyarn_".$i;

			 $cboyarnfinish="cboyarnfinish_".$i;
			 $cboyarnsnippingsystem="cboyarnsnippingsystem_".$i;
			 $cbocertification="cbocertification_".$i;
			 $cboyarncolor="cboyarncolor_".$i;
			 $txtyarncode="txtyarncode_".$i;

			 $updateidyarncost="updateidyarncost_".$i;
			 $supplier="supplier_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbotype.",'".str_replace("'", "", $$cboyarnfinish)."','".str_replace("'", "", $$cboyarnsnippingsystem)."',".$$consratio.",".$$consqnty.",".$$supplier.",".$$txtrateyarn.",".$$txtamountyarn.",'".str_replace("'", "", $$cbocertification)."','".str_replace("'", "", $$cboyarncolor)."','".str_replace("'", "", $$txtyarncode)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,yarn_cons_qnty,yarn_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsumptionyarn_sum.",".$txtamountyarn_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="yarn_cons_qnty*yarn_amount";
			$data_array2 ="".$txtconsumptionyarn_sum."*".$txtamountyarn_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}

	if($operation==1)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1";disconnect($con); die;}
		 $field_array_up="quotation_id*count_id*copm_one_id*percent_one*type_id*yarn_finish*yarn_spinning_system*cons_ratio*cons_qnty*supplier_id*rate*amount*certification*yarn_color_id*yarn_code*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id, count_id, copm_one_id, percent_one,  type_id,yarn_finish,yarn_spinning_system, cons_ratio, cons_qnty,supplier_id, rate, amount,certification,yarn_color_id,yarn_code, inserted_by, insert_date, status_active, is_deleted";
		 $add_comma=0;
         $id=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $percentone="percentone_".$i;
			// $cbocomptwo="cbocomptwo_".$i;
			// $percenttwo="percenttwo_".$i;
			 $cbotype="cbotype_".$i;
			 $consratio="consratio_".$i;
			 $consqnty="consqnty_".$i;
			 $txtrateyarn="txtrateyarn_".$i;
			 $txtamountyarn="txtamountyarn_".$i;
			 $cbostatusyarn="cbostatusyarn_".$i;
			 $updateidyarncost="updateidyarncost_".$i;
			 $supplier="supplier_".$i;

			 $cboyarnfinish="cboyarnfinish_".$i;
			 $cboyarnsnippingsystem="cboyarnsnippingsystem_".$i;
			 $cbocertification="cbocertification_".$i;
			 $cboyarncolor="cboyarncolor_".$i;
			 $txtyarncode="txtyarncode_".$i;

			if(str_replace("'",'',$$updateidyarncost)!="")
			{
			$id_arr[]=str_replace("'",'',$$updateidyarncost);
			$data_array_up[str_replace("'",'',$$updateidyarncost)] =explode(",",("".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbotype.",'".str_replace("'", "", $$cboyarnfinish)."','".str_replace("'", "", $$cboyarnsnippingsystem)."',".$$consratio.",".$$consqnty.",".$$supplier.",".$$txtrateyarn.",".$$txtamountyarn.",'".str_replace("'", "", $$cbocertification)."','".str_replace("'", "", $$cboyarncolor)."','".str_replace("'", "", $$txtyarncode)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0"));
			}

			if(str_replace("'",'',$$updateidyarncost)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array ="(".$id.",".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbotype.",'".str_replace("'", "", $$cboyarnfinish)."','".str_replace("'", "", $$cboyarnsnippingsystem)."',".$$consratio.",".$$consqnty.",".$$supplier.",".$$txtrateyarn.",".$$txtamountyarn.",'".str_replace("'", "", $$cbocertification)."','".str_replace("'", "", $$cboyarncolor)."','".str_replace("'", "", $$txtyarncode)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0)";
				$id=$id+1;
			    $add_comma++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_fab_yarn_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 $rID=1;
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array,$data_array,0);
		 }
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		$rID2=1;
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,yarn_cons_qnty,yarn_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsumptionyarn_sum.",".$txtamountyarn_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}
		$rID3=1;
		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="yarn_cons_qnty*yarn_amount";
			$data_array2 ="".$txtconsumptionyarn_sum."*".$txtamountyarn_sum."";
			$rID3=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		update_comarcial_cost_q($update_id);
		//=======================sum End =================
 		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID_up && $rID && $rID2 && $rID3){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID."**".$rID_up."**".$rID2."**".$rID3;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID_up && $rID && $rID2 && $rID3){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID."**".$rID_up."**".$rID2."**".$rID3;
			}
		}
		disconnect($con);
		die;
	}



}

if($action=="get_yarn_rate")
{
	$data=explode("_",$data);
	if($db_type==0)
	{
		$effective_date=change_date_format($data[5],'yyyy-mm-dd','-');
		$sql="select  rate from lib_yarn_rate where supplier_id=$data[4] and yarn_count=$data[0] and composition=$data[1] and  percent=$data[2] and yarn_type=$data[3] and effective_date='$effective_date' and status_active=1 and is_deleted=0  order by id desc limit 1";

	}
	if($db_type==2)
	{
		$effective_date=change_date_format($data[5],'yyyy-mm-dd','-',1);
		 $sql="select  rate from lib_yarn_rate where supplier_id=$data[4] and yarn_count=$data[0] and composition=$data[1] and  percent=$data[2] and yarn_type=$data[3] and effective_date='$effective_date' and status_active=1 and is_deleted=0 and rownum <=1 order by id desc";

	}
	$sql_data=sql_select($sql);
	//print_r($sql_data);
    echo trim($sql_data[0][csf('rate')]);
}

if ($action=="set_conversion_charge")
{
	$rate=return_field_value("rate", "lib_cost_component", "cost_component_name=$data");
	echo $rate;disconnect($con); die;
}

if($action=="conversion_chart_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script>
function js_set_value(id,rate)
{
	//var data=data.split("_");
	document.getElementById('charge_id').value=id;
	document.getElementById('charge_value').value=rate;
	parent.emailwindow.hide();

}
function toggle( x, origColor )
{

			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
}
</script>
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="charge_id" name="charge_id" />
<input type="hidden" id="charge_value" name="charge_value" />



<?
if($cbotypeconversion==1)
{
	 $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 //$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');

	 $arr=array (0=>$company_arr,1=>$body_part,5=>$unit_of_measurement,7=>$row_status);
	// echo  create_list_view ( "list_view", "Company Name,Body Part,Construction & Composition,GSM,Yarn Description,UOM,In-House Rate,Status", "150,120,180,60,150,70,100,60","980","220",1, "select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,body_part,0,0,0,uom_id,0,status_active", $arr , "comapny_id,body_part,const_comp,gsm,yarn_description,uom_id,in_house_rate,status_active", "../sub_contract_bill/requires/lib_subcontract_knitting_controller", 'setFilterGrid("list_view",-1);','0,0,0,2,0,0,2,0' ) ;
	?>
     <table width="963" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="150">Company Name</th>
     <th width="120">Body Part</th>
     <th width="180">Construction & Composition</th>
     <th width="60">GSM</th>
     <th width="150">Yarn Description</th>
     <th width="70">UOM</th>
     <th width="100">In-House Rate</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:980; overflow:scroll-y; max-height:300px">
     <table width="963" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";

	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="150"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="120"><? echo $body_part[$row[csf("body_part")]]; ?></td>
     <td width="180"><? echo $row[csf("const_comp")]; ?></td>
     <td width="60"><? echo $row[csf("gsm")]; ?></td>
     <td width="150"><? echo $row[csf("yarn_description")]; ?></td>
     <td width="70"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="100"><? echo $row[csf("in_house_rate")]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
     toggle( "tr_"+"<? echo $coversionchargelibraryid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>

     <?
}
else
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,9=>$row_status);
	//echo "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo"select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Width/Dia type,In House Rate,UOM,Rate type,Status", "100,150,70,70,70,80,60,80,60,50","900","250",1, "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,0,process_type_id,process_id,color_id,width_dia_id,0,uom_id,rate_type_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,0,0' );
	?>
    <table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="100">Company Name</th>
     <th width="150">Const. Compo.</th>
     <th width="70">Process Type</th>
     <th width="70">Process Name</th>
     <th width="70">Color</th>
     <th width="80">Width/Dia type</th>

     <th width="60">In House Rate</th>
     <th width="80">UOM</th>
     <th width="60">Rate type</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:917; overflow:scroll-y; max-height:300px">
     <table width="900" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";

	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="100"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="150"><? echo $row[csf("const_comp")]; ?></td>
     <td width="70"><? echo $process_type[$row[csf("process_type_id")]]; ?></td>
      <td width="70"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
     <td width="70"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
     <td width="80"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>

     <td width="60"><? echo $row[csf("in_house_rate")]; ?></td>
     <td width="80"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="60"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
     toggle( "tr_"+"<? echo $coversionchargelibraryid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
    <?
}
?>
</form>
</div>
</body>
</html>
<?
exit();
}

if ($action=="set_conversion_qnty")
{
	$data=explode("_",$data);
	$lib_yarn_count_deter_id=0;
	$avg_cons=0;
	$sql=sql_select("select lib_yarn_count_deter_id,avg_cons from wo_pri_quo_fabric_cost_dtls where id= $data[0] and status_active=1 and is_deleted=0");
	foreach($sql as $sql_row){
		$lib_yarn_count_deter_id=$sql_row[csf('lib_yarn_count_deter_id')];
		$avg_cons=$sql_row[csf('avg_cons')];
	}
	$processloss=return_field_value("process_loss", "conversion_process_loss", "mst_id=$lib_yarn_count_deter_id and process_id=$data[1]");
	$avg_cons=$avg_cons-($avg_cons*$processloss)/100;
	echo $avg_cons."_".$processloss; die;
}


if ($action=="save_update_delet_fabric_conversion_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;

		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
			disconnect($con);die;
		}
	}
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,cost_head,cons_type,process_loss,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocosthead="cbocosthead_".$i;
			 $cbotypeconversion="cbotypeconversion_".$i;
			 $txtprocessloss="txtprocessloss_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtchargeunit="txtchargeunit_".$i;
			 $txtamountconversion="txtamountconversion_".$i;
			 $cbostatusconversion="cbostatusconversion_".$i;
			 $updateidcoversion="updateidcoversion_".$i;
			 $coversionchargelibraryid="coversionchargelibraryid_".$i;
			 if ($i!=1) $data_array .=",";
			 $data_array .="(".$id.",".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtprocessloss.",".$$txtreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0)";
			 $id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,conv_req_qnty,conv_charge_unit,conv_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconreqnty_sum.",".$txtconchargeunit_sum.",".$txtconamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="conv_req_qnty*conv_charge_unit*conv_amount";
		    $data_array2 ="".$txtconreqnty_sum."*".$txtconchargeunit_sum."*".$txtconamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
 		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1";disconnect($con); die;}
		 $field_array_up="quotation_id*cost_head*cons_type*process_loss*req_qnty*charge_unit*amount*charge_lib_id*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id,cost_head,cons_type,process_loss,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocosthead="cbocosthead_".$i;
			 $cbotypeconversion="cbotypeconversion_".$i;
			 $txtprocessloss="txtprocessloss_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtchargeunit="txtchargeunit_".$i;
			 $txtamountconversion="txtamountconversion_".$i;
			 $cbostatusconversion="cbostatusconversion_".$i;
			 $updateidcoversion="updateidcoversion_".$i;
			 $coversionchargelibraryid="coversionchargelibraryid_".$i;
			if(str_replace("'",'',$$updateidcoversion)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateidcoversion);
				$data_array_up[str_replace("'",'',$$updateidcoversion)] =explode(",",("".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtprocessloss.",".$$txtreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0"));
			}
			if(str_replace("'",'',$$updateidcoversion)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtprocessloss.",".$$txtreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }

		 $rID=execute_query(bulk_update_sql_statement( "wo_pri_quo_fab_conv_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array,$data_array,0);
		 }
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,conv_req_qnty,conv_charge_unit,conv_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconreqnty_sum.",".$txtconchargeunit_sum.",".$txtconamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="conv_req_qnty*conv_charge_unit*conv_amount";
		    $data_array2 ="".$txtconreqnty_sum."*".$txtconchargeunit_sum."*".$txtconamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0)
		 {

			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		 }

		 if($db_type==2 || $db_type==1 )
		 {
			if($rID ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		 }
		 disconnect($con);
		 die;
	}
	else if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "10**";

		$sqljob=sql_select("select a.job_no from wo_po_details_master a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.quotation_id=".$update_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); //die;
		if(count($sqljob)>0){
			echo "bomFound**".$sqljob[0][csf('job_no')];
			disconnect($con);die;
		}

		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_fab_conv_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and status_active=1 and is_deleted=0",1);
		if($rID_dtls==1) $flag=1; else $flag=0;
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}
// Fabric Cost End =========================================================================================================================================================
if ($action=="load_drop_down_supplier_rate"){
	$supplier_library=return_library_array( "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
	$data=explode("_",$data);
	if($data[2]==2){
		$supplier_library_with_rate=array();
		$sql_data_supplier_rate_sql=sql_select("select a.id, b.supplier_id, b.rate  from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.id='".$data[0]."' and    b.rate>0 and a.status_active=1 and	a.is_deleted=0 group by a.id, b.supplier_id, b.rate order by b.rate");
		foreach($sql_data_supplier_rate_sql as $sql_data_supplier_rate_row){
			$supplier_library_with_rate[$sql_data_supplier_rate_row[csf('supplier_id')]]=$supplier_library[$sql_data_supplier_rate_row[csf('supplier_id')]]."--Rate--[".$sql_data_supplier_rate_row[csf('rate')]."]";
		}
		echo create_drop_down( "cbonominasupplier_".$data[1],90, $supplier_library_with_rate, "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$data[1].",'supplier_change')",$disabled,"" );
	}
	else if($data[2]==3){
		$supplier_library_with_rate=array();
		$sql_data_supplier_rate_sql=sql_select("select a.id, b.supplier_id, b.rate  from lib_item_group a, lib_item_group_rate b, lib_supplier_tag_buyer c where a.id=b.mst_id and b.supplier_id=c.supplier_id and a.item_category=4 and a.id='".$data[0]."' and c.tag_buyer=$data[3] and    b.rate>0 and a.status_active=1 and	a.is_deleted=0 group by a.id, b.supplier_id, b.rate order by b.rate");
		foreach($sql_data_supplier_rate_sql as $sql_data_supplier_rate_row){
			$supplier_library_with_rate[$sql_data_supplier_rate_row[csf('supplier_id')]]=$supplier_library[$sql_data_supplier_rate_row[csf('supplier_id')]]."--Rate--[".$sql_data_supplier_rate_row[csf('rate')]."]";
		}
		echo create_drop_down( "cbonominasupplier_".$data[1],90, $supplier_library_with_rate, "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$data[1].",'supplier_change')",$disabled,"" );
	}
	else{
		echo create_drop_down( "cbonominasupplier_".$data[1],90, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1  group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$data[1].",'supplier_change')",$disabled,"" );
	}
	exit();
}
// Trim Cost ==============================================================================================
if($action=="openpopup_itemgroup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_name = new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txttrimgroupdata_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txttrimgroupdata_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txttrimgroupdata_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + ',';
			}
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

			$('#itemdata').val( trimgroupdata );
		}

	/*function js_set_value(id, name)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		parent.emailwindow.hide();
	}*/
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="itemdata" name="itemdata"/>
        <? $sql_tgroup=sql_select( "select id, item_name, trim_uom from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name"); ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th>Item Group</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row_tgroup)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row_tgroup[csf('id')].'***'.$row_tgroup[csf('item_name')].'***'.$row_tgroup[csf('trim_uom')];
					?>
					<tr id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td><td><? echo $row_tgroup[csf('item_name')]; ?>
                        	<input type="hidden" name="txttrimgroupdata_<? echo $i; ?>" id="txttrimgroupdata_<? echo $i; ?>" value="<? echo $str; ?>"/>
                        </td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('item_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if ($action=="rate_from_library")
{
	$data=explode("_",$data);

	$rate=0;
	if($data[1]==0)
	{
		$trim_rate_from_library=sql_select( "select a.id, min(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.id=$data[0]  and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 group by a.id");
	}
	else
	{
		$trim_rate_from_library=sql_select( "select a.id, b.rate as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.id=$data[0] and b.supplier_id=$data[1] and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 ");
	}

	foreach($trim_rate_from_library as $trim_rate_from_library_row)
	{
	    if($trim_rate_from_library_row[csf('rate')] !="")
		{
	    	$rate=$trim_rate_from_library_row[csf('rate')];
		}
	}
	echo trim($rate); die;
}


if ($action=="show_trim_cost_listview")
{
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");

	$supplier_library=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
	$data=explode("*",$data);
	//print_r($data);
	$trim_variable=1;
	$trim_variable_sql=sql_select( "select trim_rate from  variable_order_tracking where company_name='$data[2]' and variable_list=35 order by id" );
	foreach($trim_variable_sql as $trim_variable_row)
	{
		$trim_variable=	$trim_variable_row[csf('trim_rate')];
	}

	if( $trim_variable==2) $readonly_rate="readonly"; else if($trim_variable==3) $readonly_rate="readonly"; else $readonly_rate="";

	$approved=return_field_value("approved", "wo_price_quotation", "id='$data[0]'");
	if($approved==3){
		$approved=1;
	}
	if($approved==1)
	{
		$disabled=1;
		$txt_disabled="disabled";
	}
	else
	{
		$disabled=0;
		$txt_disabled="";
	}
	$supplier_lib_with_rate_arr=array();
	if($trim_variable==2)
	{
		$sql_data_supplier_rate_sql=sql_select("select a.id, b.supplier_id, b.rate  from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and b.rate>0 and a.status_active=1 and a.is_deleted=0 group by a.id, b.supplier_id, b.rate order by b.rate");
		foreach($sql_data_supplier_rate_sql as $srow)
		{
			$supplier_lib_with_rate_arr[$srow[csf('id')]][$srow[csf('supplier_id')]]=$supplier_library[$srow[csf('supplier_id')]]."--Rate--[".$srow[csf('rate')]."]";
		}
	}
	else if($trim_variable==3)
	{
		$sql_data_supplier_rate_sql=sql_select("select a.id, b.supplier_id, b.rate  from lib_item_group a, lib_item_group_rate b, lib_supplier_tag_buyer c where a.id=b.mst_id and b.supplier_id=c.supplier_id and a.item_category=4 and c.tag_buyer=$data[1] and b.rate>0 and a.status_active=1 and	a.is_deleted=0 group by a.id, b.supplier_id, b.rate order by b.rate");
		foreach( $sql_data_supplier_rate_sql as $srow )
		{
			$supplier_lib_with_rate_arr[$srow[csf('id')]][$srow[csf('supplier_id')]]=$supplier_library[$srow[csf('supplier_id')]]."--Rate--[".$srow[csf('rate')]."]";
		}
	}

	unset($sql_data_supplier_rate_sql);

	?>
	<h3 style="width:930px;" align="left" class="accordion_h">+Trim Cost</h3>
	<div id="content_trim_cost"  align="left">
	<fieldset style="width:930px;">
	<input type="hidden" id="trim_rate_variable" value="<? echo $trim_variable; ?>" />
	<form id="trimccost_6" autocomplete="off">
        <table width="910" cellspacing="0" class="rpt_table" border="0" id="tbl_trim_cost" rules="all">
            <thead>
                <tr>
                	<th width="30">Seq</th>
                    <th width="100">Group</th>
                    <th width="150">Description</th>
                    <th width="95">Nominated Supp</th>
                    <th width="60">Cons UOM</th>
                    <th width="60">Cons/Dzn Gmts</th>
                    <th width="60">Rate</th>
                    <th width="70">Amount</th>
                    <th width="70">Apvl Req.</th>
                    <th width="70">Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?
            $save_update=1;
            $data_array=sql_select("select id, quotation_id, trim_group, description, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, seq, status_active from  wo_pri_quo_trim_cost_dtls where quotation_id='$data[0]'  and status_active=1 and is_deleted=0 order by seq,id");// quotation_id='$data'

            if ( count($data_array)>0)
            {
				$i=0;
				foreach( $data_array as $row )
				{
					$i++;
					$seq='';
					if($row[csf("seq")]=="") $seq=$i; else $seq=$row[csf("seq")];
					?>
					<tr id="trim_1" align="center">
                        <td><input type="text" id="seq_<? echo $i; ?>" name="seq_<? echo $i; ?>" class="text_boxes" style="width:25px" value="<? echo $seq; ?>" /></td>
                        <td><input type="text" id="cbogrouptext_<? echo $i; ?>"  name="cbogrouptext_<? echo $i; ?>" title= "<? echo  $lib_item_group_arr[$row[csf("trim_group")]]; ?>" readonly class="text_boxes" style="width:90px" value="<? echo $lib_item_group_arr[$row[csf("trim_group")]];  ?>" <? echo $txt_disabled; ?> onDblClick="openpopup_itemgroup(<? echo $i; ?>)"/>
                        	<input type="hidden" id="cbogroup_<? echo $i; ?>" name="cbogroup_<? echo $i; ?>" class="text_boxes" style="width:95px" value="<? echo $row[csf("trim_group")]; ?>" /></td>
                        <td><input type="text" id="txtdescription_<? echo $i; ?>" name="txtdescription_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("description")];  ?>" <? echo $txt_disabled; ?> onDblClick="trims_description_popup(<? echo $i; ?>)"/></td>
                        <td id="tdsupplier_<? echo $i; ?>">
                        <?
                        if($trim_variable==1)
                        {
                       		echo create_drop_down( "cbonominasupplier_".$i,95, $supplier_library, "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')",$disabled,"" );
                        }
                        else
                        {
                        	echo create_drop_down( "cbonominasupplier_".$i,95, $supplier_lib_with_rate_arr[$row[csf('trim_group')]], "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')",$disabled,"" );
                        }
                        ?>
                        </td>
                        <td><? echo create_drop_down( "cboconsuom_".$i, 60, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?></td>
                        <td><input type="text" id="txtconsdzngmts_<? echo $i; ?>" name="txtconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("cons_dzn_gmts")]; ?>" onChange="calculate_trim_cost( <? echo $i;?> )" <? echo $txt_disabled; ?> />
                        </td>
                        <td><input type="text" id="txttrimrate_<? echo $i; ?>" name="txttrimrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_trim_cost( <? echo $i;?> )" <? echo $txt_disabled; ?>  <? echo $readonly_rate; ?> onDblClick="trim_rate_popup(<? echo $i; ?>)"/>
                        </td>
                        <td><input type="text" id="txttrimamount_<? echo $i; ?>" name="txttrimamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("amount")]; ?>" readonly  />
                        </td>
                        <td><? echo create_drop_down( "cboapbrequired_".$i, 70, $yes_no,"", 0, "0", $row[csf("apvl_req")], '',$disabled,'' );  ?></td>
                        <td><? echo create_drop_down( "cbotrimstatus_".$i, 70, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>
                        <td>
                            <input type="button" id="increasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                            <input type="button" id="decreasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_trim_cost' );"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />

                            <input type="hidden" id="updateidtrim_<? echo $i; ?>" name="updateidtrim_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                        </td>
					</tr>
					<?
				}
            }
            else
            {
				$trim_rate_from_library=return_library_array( "select a.id, min(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 group by a.id", "id", "rate");

				$data_array=sql_select("select a.trims_group, a.cons_uom, a.cons_dzn_gmts, a.purchase_rate, a.amount, a.apvl_req, a.supplyer from lib_trim_costing_temp a, lib_trim_costing_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.id, a.trims_group, a.cons_uom, a.cons_dzn_gmts, a.purchase_rate, a.amount, a.apvl_req, a.supplyer");// quotation_id='$data'
				if ( count($data_array)>0 && $data[2]==1)
				{
					$save_update=0;
					$i=0;
					foreach( $data_array as $row )
					{
						$rate=$trim_rate_from_library[$row[csf('trims_group')]];
						$amount=$row[csf('cons_dzn_gmts')]*$trim_rate_from_library[$row[csf('trims_group')]];
						if($rate=="" || $rate==0)
						{
							$rate=$row[csf('purchase_rate')];
							$amount=$row[csf('amount')];
						}
						$i++; $seq=''; $seq=$i;
						?>
						<tr id="trim_1" align="center">
                            <td><input type="text" id="seq_<? echo $i; ?>" name="seq_<? echo $i; ?>" class="text_boxes" style="width:25px" value="<? echo $seq; ?>" /></td>
                            <td><input title= "<? echo  $lib_item_group_arr[$row[csf("trims_group")]]; ?>" readonly type="text" id="cbogrouptext_<? echo $i; ?>" name="cbogrouptext_<? echo $i; ?>" class="text_boxes" style="width:90px" value="<? echo $lib_item_group_arr[$row[csf("trims_group")]]; ?>" <? echo $txt_disabled; ?> onDblClick="openpopup_itemgroup(<? echo $i; ?>)"/>
                                <input type="hidden" id="cbogroup_<? echo $i; ?>"  name="cbogroup_<? echo $i; ?>" class="text_boxes" style="width:55px" value="<? echo $row[csf("trims_group")];  ?>" />
                            </td>
                            <td><input type="text" id="txtdescription_<? echo $i; ?>"  name="txtdescription_<? echo $i; ?>" class="text_boxes" style="width:140px" value="" <? echo $txt_disabled; ?> onDblClick="trims_description_popup(<? echo $i; ?>)"/></td>
                            <td id="tdsupplier_<? echo $i; ?>">
                            <?
								if($trim_variable==1)
								{
									echo create_drop_down( "cbonominasupplier_".$i,95, $supplier_library, "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')",$disabled,"" );
								}
								else
								{
									echo create_drop_down( "cbonominasupplier_".$i,95, $supplier_lib_with_rate_arr[$row[csf('trim_group')]], "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')",$disabled,"" );
								}
                            ?>
                            </td>
                            <td><?  echo create_drop_down( "cboconsuom_".$i, 60, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?></td>
                            <td><input type="text" id="txtconsdzngmts_<? echo $i; ?>"  name="txtconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo $row[csf("cons_dzn_gmts")];?>" onChange="calculate_trim_cost( <? echo $i;?> )" /><!--onDblClick="open_calculator(<?// echo $i;?> )"-->
                            </td>
                            <td>
                            	<input type="text" id="txttrimrate_<? echo $i; ?>"  name="txttrimrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $rate;?>" onChange="calculate_trim_cost( <? echo $i;?> )" <? echo $readonly_rate; ?> onDblClick="trim_rate_popup(<? echo $i; ?>)"/>
                            </td>
                            <td>
                            	<input type="text" id="txttrimamount_<? echo $i; ?>"  name="txttrimamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $amount;?>"  readonly />
                            </td>
                            <td><? echo create_drop_down( "cboapbrequired_".$i, 70, $yes_no,"", 0, "0", $row[csf("apvl_req")], '','','' );  ?></td>
                            <td><? echo create_drop_down( "cbotrimstatus_".$i, 70, $row_status,"", 0, "0", '', '','','' ); ?></td>
                            <td>
                                <input type="button" id="increasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i; ?> )" />
                                <input type="button" id="decreasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_trim_cost' );" />
                                <input type="hidden" id="updateidtrim_<? echo $i; ?>" name="updateidtrim_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="" />
                            </td>
						</tr>
						<?
					}
				}
				else
				{
					$save_update=0;
					?>
					<tr id="trim_1" align="center">
                        <td><input type="text" id="seq_1" name="seq_1" class="text_boxes" style="width:25px" value="1" /></td>
                        <td><input title= "<? echo $lib_item_group_arr[$row[csf("trim_group")]]; ?>" readonly type="text" id="cbogrouptext_1"  name="cbogrouptext_1" class="text_boxes" style="width:90px" value="<? echo $lib_item_group_arr[$row[csf("trim_group")]];  ?>" <? echo $txt_disabled; ?> onDblClick="openpopup_itemgroup(1)"/>
                        	<input type="hidden" id="cbogroup_1" name="cbogroup_1" class="text_boxes" style="width:55px" value="<? echo $row[csf("trim_group")];  ?>" />
                        </td>
                        <td><input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes" style="width:140px" value="" onDblClick="trims_description_popup(1)"/></td>
                        <td id="tdsupplier_<? echo $i; ?>"><? echo create_drop_down( "cbonominasupplier_1",95, $supplier_library, "", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')",$disabled,"" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cboconsuom_1", 60, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?></td>
                        <td><input type="text" id="txtconsdzngmts_1" name="txtconsdzngmts_1" class="text_boxes_numeric" style="width:55px" value="<? echo $row[csf("cons_dzn_gmts")];?>" onChange="calculate_trim_cost( 1 )" /><!--onDblClick="open_calculator(1)"-->
                        </td>
                        <td><input type="text" id="txttrimrate_1" name="txttrimrate_1" class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("purchase_rate")];?>" onChange="calculate_trim_cost( 1 )" <? echo $readonly_rate; ?> onDblClick="trim_rate_popup(1)"/>
                        </td>
                        <td><input type="text" id="txttrimamount_1" name="txttrimamount_1" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("amount")]; ?>"  readonly />
                        </td>
                        <td><? echo create_drop_down( "cboapbrequired_1", 70, $yes_no,"", 0, "0", $row[csf("apvl_req")], '','','' ); ?></td>
                        <td><? echo create_drop_down( "cbotrimstatus_1", 70, $row_status,"", 0, "0", '', '','','' ); ?></td>
                        <td>
                            <input type="button" id="increasetrim_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(1 )" />
                            <input type="button" id="decreasetrim_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_trim_cost' );" />
                            <input type="hidden" id="updateidtrim_1" name="updateidtrim_1"  class="text_boxes" style="width:20px" value=""  />
                        </td>
					</tr>
					<?
				}
            }
            ?>
            </tbody>
        </table>
        <table width="910" cellspacing="0" class="rpt_table" border="0" rules="all">
            <tfoot>
                <tr>
                    <th width="350">Sum</th>
                    <th width="80"><input type="text" id="txtconsdzntrim_sum" name="txtconsdzntrim_sum" class="text_boxes_numeric" style="width:70px" readonly /></th>
                    <th width="70"><input type="text" id="txtratetrim_sum" name="txtratetrim_sum" class="text_boxes_numeric" style="width:60px" readonly /></th>
                    <th width="100"><input type="text" id="txttrimamount_sum" name="txttrimamount_sum" class="text_boxes_numeric" style="width:90px" readonly /></th>
                    <th width="90">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>

        <table width="910" cellspacing="0" class="" border="0">
            <tr>
                <td align="center" width="100%" class="button_container">
                <?
					if ( count($data_array)>0)
					{
						echo load_submit_buttons( $permission, "fnc_trim_cost_dtls", $save_update,0,"reset_form('trimccost_6','','',0)",6) ;
					}
					else
					{
						echo load_submit_buttons( $permission, "fnc_trim_cost_dtls", $save_update,0,"reset_form('trimccost_6','','',0)",6) ;
					}
                ?>
                </td>
            </tr>
        </table>
	</form>
	</fieldset>
	</div>
	<?
	exit();
}

if ($action=="set_cons_uom")
{
	$cons_uom=return_field_value("trim_uom", "lib_item_group", "id=$data");
	echo $cons_uom; die;
}

if ($action=="rate_from_library")
{
	$data=explode("_",$data);
	$rate=0;
	if($data[1]==0)
	{
	$trim_rate_from_library=sql_select( "select a.id, min(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.id=$data[0]  and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 group by a.id");
	}
	else
	{
	$trim_rate_from_library=sql_select( "select a.id, b.rate as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.id=$data[0] and b.supplier_id=$data[1] and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 ");
	}

	foreach($trim_rate_from_library as $trim_rate_from_library_row)
	{
	    if($trim_rate_from_library_row[csf('rate')] !="")
		{
	     $rate=$trim_rate_from_library_row[csf('rate')];
		}
	}
	echo trim($rate); die;
}

if($action=="calculator_parameter")
{
	$cal_parameter_type=return_field_value("cal_parameter", "lib_item_group", "id='$data'");
	echo trim($cal_parameter_type); die;
}


if($action=="trim_rate_popup_page")
{
echo load_html_head_contents("Country","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script>
function js_set_value(data)
{
	var data=data.split('_');
	document.getElementById('txt_selected_supllier').value=data[0];
	document.getElementById('txt_selected_rate').value=data[1];
    parent.emailwindow.hide();
}
</script>
</head>
<body>
<body>
<div align="center">
<form>
<input type="hidden" id="txt_selected_supllier" name="txt_selected_supllier" value=" " />
<input type="hidden" id="txt_selected_rate" name="txt_selected_rate" value="" />
<?
$supplier_library=return_library_array( "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
$sql_data=sql_select("select a.id, b.supplier_id, b.rate  from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.id='$cbogroup' and    b.rate>0 and a.status_active=1 and	a.is_deleted=0 group by a.id, b.supplier_id, b.rate order by b.rate");

?>
<table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="50">SL</th>
					<th width="100">Supplier</th>
					<th width="100">Rate</th>
				</thead>
			</table>
<table width="500" cellspacing="0" class="rpt_table" border="0" id="tbl_list_search" rules="all">
<?
$i=1;
foreach($sql_data as $row)
{
	if ($i%2==0)
	$bgcolor="#E9F3FF";
else
	$bgcolor="#FFFFFF";
?>
<tr bgcolor="<? echo $bgcolor;  ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('supplier_id')]."_".$row[csf('rate')];?>')">
<td width="50"><? echo $i ?></td>
<td width="100">
<? echo $supplier_library[$row[csf('supplier_id')]]; ?>
</td>
<td width="100">
<? echo $row[csf('rate')]; ?>
</td>
</tr>
<?
$i++;
}
?>
</table>
</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
 <?
 exit();
}

if ($action=="trims_description_popup")
{
	echo load_html_head_contents("Description","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=$data;
	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("description").value='<? echo $data; ?>';
		});
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form>
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="description" id="description" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		$("#description" ).focus();
	</script>
    </html>
    <?
	exit();
}

if($action=="calculator_type")
{
   echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
   extract($_REQUEST);

?>
<script>
function clculate_cons_for_mtr()
{
  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
  var txt_costing_per=document.getElementById('txt_costing_per').value;
  if(txt_costing_per==1)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*12;
  }
  if(txt_costing_per==2)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*1;
  }
  if(txt_costing_per==3)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*2*12;
  }
  if(txt_costing_per==4)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*3*12;
  }
  if(txt_costing_per==5)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*4*12;
  }
  var txt_cons_for_mtr= (document.getElementById('txt_cons_for_mtr').value)*1
  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
  document.getElementById('txt_cons_for_cone').value=txt_cons_for_mtr/txt_cons_length;
}
function js_set_value_calculator(type)
{
	if(type=='sewing_thread')
	{
		var clacolator_param_value=document.getElementById('txt_cons_per_gmts').value+'*'+document.getElementById('txt_cons_for_mtr').value+'*'+document.getElementById('txt_cons_length').value+'*'+document.getElementById('txt_cons_for_cone').value+'*'+document.getElementById('txt_costing_per').value;

		document.getElementById('txt_clacolator_param_value').value=clacolator_param_value;

	}

		parent.emailwindow.hide();


}
</script>
</head>
<body>
<?
	if($calculator_parameter==1)
	{
		?>
        <fieldset>
        <legend>Sewing Thread</legend>
        <table cellpadding="0" cellspacing="2" align="center" width="300">
        <tr>
        <td width="120">
        Cons Per Garment
        </td>
        <td width="">
        <input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_cons_for_mtr()" /> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cons <? echo $costing_per[$cbo_costing_per];?>
        </td>
        <td>
        <input type="text" id="txt_cons_for_mtr" name="txt_cons_for_mtr" class="text_boxes_numeric"  readonly/> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cone Length
        </td>
        <td>
        <input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_cons_for_mtr()" value="4000"/> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cons  <? echo $costing_per[$cbo_costing_per];?>
        </td>
        <td>
        <input type="text" id="txt_cons_for_cone" name="txt_cons_for_cone" class="text_boxes_numeric" readonly /> Cone
        </td>
        </tr>
         <tr>
        <td colspan="3" align="center">
        <input type="button" class="formbutton" value="Close" onClick="js_set_value_calculator('sewing_thread')"/>
        <input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly />
        <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly />
        </td>
        </tr>
        </table>
        </fieldset>
        <?
	}
	?>
 </body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="save_update_delet_trim_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
		$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}else{
			$approved=$approved;
		}
		if($approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
		}
	}
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id=return_next_id( "id", "wo_pri_quo_trim_cost_dtls", 1 ) ;
		 $field_array="id, quotation_id, trim_group, description, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,seq, inserted_by, insert_date, status_active,	is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogroup="cbogroup_".$i;
			 $cboconsuom="cboconsuom_".$i;
			 $txtdescription="txtdescription_".$i;
			 $txtconsdzngmts="txtconsdzngmts_".$i;
			 $txttrimrate="txttrimrate_".$i;
			 $txttrimamount="txttrimamount_".$i;
			 $cboapbrequired="cboapbrequired_".$i;
			 $cbonominasupplier="cbonominasupplier_".$i;
			 $cbotrimstatus="cbotrimstatus_".$i;
			 $updateidtrim="updateidtrim_".$i;
			 $seq="seq_".$i;
			 if ($i!=1) $data_array .=",";
			 $data_array .="(".$id.",".$update_id.",".$$cbogroup.",".$$txtdescription.",".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$$seq.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0)";
			 $id=$id+1;
		 }
		 //echo "10**insert into wo_pri_quo_trim_cost_dtls (".$field_array.") values ".$data_array;
		 $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,trim_cons,trim_rate,trim_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsdzntrim_sum.",".$txtratetrim_sum.",".$txttrimamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="trim_cons*trim_rate*trim_amount";
			$data_array2 ="".$txtconsdzntrim_sum."*".$txtratetrim_sum."*".$txttrimamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}

	else if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="quotation_id*trim_group*description*cons_uom*cons_dzn_gmts*rate*amount*apvl_req*nominated_supp*seq*updated_by*update_date*status_active*is_deleted";
		 $field_array="id, quotation_id, trim_group, description, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,seq, inserted_by, insert_date, status_active, is_deleted";
		 $add_co=0;
		 $id=return_next_id( "id","wo_pri_quo_trim_cost_dtls", 1 );
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogroup="cbogroup_".$i;
			 $cboconsuom="cboconsuom_".$i;
			 $txtdescription="txtdescription_".$i;
			 $txtconsdzngmts="txtconsdzngmts_".$i;
			 $txttrimrate="txttrimrate_".$i;
			 $txttrimamount="txttrimamount_".$i;
			 $cboapbrequired="cboapbrequired_".$i;
			 $cbonominasupplier="cbonominasupplier_".$i;
			 $cbotrimstatus="cbotrimstatus_".$i;
			 $updateidtrim="updateidtrim_".$i;
			 $seq="seq_".$i;
			 
			$description=str_replace("'",'',$$txtdescription);
			$descriptions=str_replace(",",'-',$description);

			if(str_replace("'",'',$$updateidtrim)!="")
			{
                $id_arr[]=str_replace("'",'',$$updateidtrim);
				$data_array_up[str_replace("'",'',$$updateidtrim)] =explode(",",("".$update_id.",".$$cbogroup.",'".$descriptions."',".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$$seq.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0"));			}
			if(str_replace("'",'',$$updateidtrim)=="")
			{
				if ($add_co!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cbogroup.",'".$descriptions."',".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$$seq.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0)";
			   $id=$id+1;
		       $add_co++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_trim_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		  $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array,$data_array,0);
		 }
		  //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,trim_cons,trim_rate,trim_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtconsdzntrim_sum.",".$txtratetrim_sum.",".$txttrimamount_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="trim_cons*trim_rate*trim_amount";
			$data_array2 ="".$txtconsdzntrim_sum."*".$txtratetrim_sum."*".$txttrimamount_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		update_comarcial_cost_q($update_id);
		//=======================sum End =================
 		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_up ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$ex_data=explode("***",$data);
		$fabupdateid=$ex_data[0];
		$data_component=$ex_data[1];
		 //echo "10**";
		//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_trim_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1) $flag=1; else $flag=0;


		/*if($flag==1)
		{
			$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
			$ex_dataComponent=explode("__",$res_data_component);
			$dtls_respone=$ex_dataComponent[0];
			$dtls_id=$ex_dataComponent[1];

			//echo $dtls_id;
			if($dtls_respone==1) $flag=1; else $flag=0;
		}*/
		//echo  '10**'.$flag.'=='.$rID_quo_fab_dtls_del.'=='.$rID_dtls.'=='.$rID_co.'=='.$dtls_respone; die;
		//echo $sql_quo_fab_dtls_del; die;

		//echo $rID_quo_fab_dtls_del.'='.$rID_dtls.'='.$rID_co.'='.$dtls_respone.'='.$flag.'='.$db_type; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}
// Trim Cost End =========================================================================================================================================================

// Embellisment Cost  =========================================================================================================================================================
if ($action=="show_embellishment_cost_listview")
{
	$header_td="";
	$costing_per=return_field_value("costing_per", "wo_price_quotation", "id='$data'");
	if($costing_per==1) $header_td="Cons/ 1 Dzn Gmts";
	else if($costing_per==2) $header_td="Cons/ 1 Pcs Gmts";
	else if($costing_per==3) $header_td="Cons/ 2 Dzn Gmts";
	else if($costing_per==4) $header_td="Cons/ 3 Dzn Gmts";
	else if($costing_per==5) $header_td="Cons/ 4 Dzn Gmts";

?>
<h3 style="width:820px;" align="left" class="accordion_h" >+Embellishment Cost</h3>
       <div id="content_embellishment_cost" align="left">
    	<fieldset style="width:820px;">
        	<form id="embellishment_7" autocomplete="off">
            <!--<input type="text" id="cons_breck_down" name="cons_breck_down" value="" width="500" />
            <input type="text" id="msmnt_breack_down" name="msmnt_breack_down"/>-->
           <!-- <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" />
            <input type="text" id="hid_fab_cons_in_quotation_variable" name="hid_fab_cons_in_quotation_variable" value="2" width="500" />
 -->
            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_embellishment_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Name</th><th width="100">Type</th><th width="90"><? echo $header_td; ?></th><th width="90">Rate</th><th width="110">Amount</th><th width="95">Status</th><th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data'");
					if($approved==3){
						$approved=1;
					}
					if($approved==1) $disabled=1; else $disabled=0;

					$data_array=sql_select("select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from  wo_pri_quo_embe_cost_dtls where emb_name!=3 and quotation_id='$data'  and status_active=1 and is_deleted=0");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);


						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="embellishment_1" align="center">
                                    <td id="cboembnametd_<? echo $i;?>"><? echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1," -- Select--", $row[csf("emb_name")], "cbotype_loder(".$i.")",$disabled,'1,2,4,5' ); ?></td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $type_array[$row[csf("emb_name")]],"", 1, "-- Select --", $row[csf("emb_type")], "check_duplicate(".$i.")",$disabled,"" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_emb_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_emb_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];  ?>"   <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_embellishment_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_embellishment_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                                     </td>
                                </tr>
                            <?
						}
					}
					else
					{
						$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);

						$i=0;
						foreach( $emblishment_name_array as $row => $value )
						{
							$i++;
							if($i==3)
							{
								continue;
							}
							else
							{
								if($i>3)$i=$i-1;

								//echo  $row;
								?>
								<tr id="embellishment_1" align="center">
                                   <td id="cboembnametd_<? echo $i;?>">
									<?
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1,"--Select--", $row, "cbotype_loder(".$i.")",'','1,2,4,5,99' );
									?>
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $type_array[$row],"", 1, "-- Select --", "", "check_duplicate(".$i.")","","" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_emb_cost( <? echo $i;?> )"/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_emb_cost( <? echo $i;?> )"/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value=""   />
                                    </td>

                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_embellishment_cost(<? echo $i; ?> )" />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_embellishment_cost' );" />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />                                    </td>
                                </tr>
                    <?
					            if($i==3 || $i==4)
								{
									$i++;
								}
							}
						}
					}
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>
                            <th  width="92">
                           <!-- <input type="text" id="txtconsdznemb_sum"  name="txtconsdznemb_sum" class="text_boxes" style="width:80px"  readonly />-->
                            </th>
                            <th width="92">
                            <!--<input type="text" id="txtrateemb_sum"  name="txtrateemb_sum" class="text_boxes" style="width:80px"  readonly />-->
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountemb_sum"  name="txtamountemb_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="800" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						<?
						if ( count($data_array)>0)
					    {
							echo load_submit_buttons( $permission, "fnc_embellishment_cost_dtls", 1,0,"reset_form('embellishment_7','','',0)",7) ;
					    }
						else
						{
							echo load_submit_buttons( $permission, "fnc_embellishment_cost_dtls", 0,0,"reset_form('embellishment_7','','',0)",7) ;
						}
						?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>
	<?
	exit();
}

if ($action=="load_drop_down_embtype")
{
	$data=explode('_',$data);
	if($data[0]==1)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_print_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" );
		die;
	}
	if($data[0]==2)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_embroy_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" );
		die;
	}
	if($data[0]==3)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_wash_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" );
		die;
	}
	if($data[0]==4)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_spwork_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" );
		die;
	}

	if($data[0]==5)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$emblishment_gmts_type,"", 1, "-- Select --", "", "","","" );
		die;
	}
	if($data[0]==99)
	{
		echo create_drop_down( "cboembtype_".$data[1], 100,$blank_array,"", 1, "-- Select --", "", "","","" );
		die;
	}
}

if ($action=="save_update_delet_embellishment_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
		$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}else{
			$approved=$approved;
		}
		if($approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
		}
	}
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		  //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,emb_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="emb_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con);die;}
		 $field_array_up="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {

			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			if(str_replace("'",'',$$embupdateid)!="")
			{
				/*$field_array="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
				$data_array ="".$update_id."*".$$cboembname."*".$$cboembtype."*".$$txtembconsdzngmts."*".$$txtembrate."*".$$txtembamount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cboembstatus."*0";
				$rID=sql_update("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,"id","".$$embupdateid."",0);*/
				$id_arr[]=str_replace("'",'',$$embupdateid);
				$data_array_up[str_replace("'",'',$$embupdateid)] =explode(",",("".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0"));

			}
			if(str_replace("'",'',$$embupdateid)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array ="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_embe_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,emb_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="emb_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID_up ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$ex_data=explode("***",$data);
		$fabupdateid=$ex_data[0];
		$data_component=$ex_data[1];
		 //echo "10**";
		//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_embe_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and emb_name!=3 and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1) $flag=1; else $flag=0;

		/*if($flag==1)
		{
			$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
			$ex_dataComponent=explode("__",$res_data_component);
			$dtls_respone=$ex_dataComponent[0];
			$dtls_id=$ex_dataComponent[1];

			//echo $dtls_id;
			if($dtls_respone==1) $flag=1; else $flag=0;
		}*/
		//echo  '10**'.$flag.'=='.$rID_quo_fab_dtls_del.'=='.$rID_dtls.'=='.$rID_co.'=='.$dtls_respone; die;
		//echo $sql_quo_fab_dtls_del; die;

		//echo $rID_quo_fab_dtls_del.'='.$rID_dtls.'='.$rID_co.'='.$dtls_respone.'='.$flag.'='.$db_type; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}
// Embellisment Cost End   =========================================================================================================================================================
// Wash Cost  =========================================================================================================================================================
if ($action=="show_wash_cost_listview")
{
	$data=explode("_",$data);
	$header_td="";
	$costing_per=return_field_value("costing_per", "wo_price_quotation", "id='$data[0]'");
	if($costing_per==1) $header_td="Cons/ 1 Dzn Gmts";
	else if($costing_per==2) $header_td="Cons/ 1 Pcs Gmts";
	else if($costing_per==3) $header_td="Cons/ 2 Dzn Gmts";
	else if($costing_per==4) $header_td="Cons/ 3 Dzn Gmts";
	else if($costing_per==5) $header_td="Cons/ 4 Dzn Gmts";

	$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data[1]'  and variable_list=21 and status_active=1 and is_deleted=0");
	if($conversion_from_chart=="") $conversion_from_chart=2;

?>
<h3 style="width:820px;" align="left" class="accordion_h" >+Wash Cost</h3>
       <div id="content_wash_cost" align="left">
    	<fieldset style="width:820px;" >
        	<form id="wash_7" autocomplete="off">
            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_wash_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Name</th><th width="100">Type</th><th width="90"><? echo $header_td; ?></th><th width="90">Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$disabled=0;
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data[0]'");
					if($approved==3){
						$approved=1;
					}
					if($approved==1) $disabled=1; else $disabled=0;

					/*if($conversion_from_chart==1)
					{
						$disabled=1;
						$select_smg="NO Need";
					}
					if($conversion_from_chart==2)
					{
						$disabled=0;
						$select_smg="-Select-";
					}*/

					$data_array=sql_select("select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,charge_lib_id,status_active from  wo_pri_quo_embe_cost_dtls where emb_name=3 and quotation_id='$data[0]'  and status_active=1 and is_deleted=0");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
					//$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$blank_array);


						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="embellishment_1" align="center">
                                    <td id="cboembnametd_<? echo $i;?>">
									<?
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1," -- Select--", $row[csf("emb_name")], "",1,'' );
									?>

                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $emblishment_wash_type,"", 1, "-Select-", $row[csf("emb_type")], "check_duplicate(".$i.")",$disabled,"" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_wash_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_wash_cost( <? echo $i;?> )"  <? if($disabled==0){echo "";}else{echo "disabled";}?> onClick="<? if($conversion_from_chart==1){ echo "set_wash_charge_unit_pop_up('".$i."')";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?>/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")];  ?>"   <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                                    </td>

                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_wash_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_wash_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                                     <input type="hidden" id="embratelibid_<? echo $i; ?>" name="embratelibid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("charge_lib_id")]; ?>"  />
                                     </td>
                                </tr>
                            <?
						}
					}
					else
					{
						$i=0;
						$i++;
					?>
                    <tr id="embellishment_1" align="center">
                                   <td id="cboembnametd_<? echo $i;?>">
									<?
									echo create_drop_down( "cboembname_".$i, 135, $emblishment_name_array, "",1,"--Select--", 3, "",1,'' );
									?>
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 80, $emblishment_wash_type,"", 1, "-Select-", "", "check_duplicate(".$i.")","","" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_wash_cost( <? echo $i;?> )"/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_wash_cost( <? echo $i;?> )" onClick="<? if($conversion_from_chart==1){ echo "set_wash_charge_unit_pop_up(1)";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?> />
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" readonly value=""   />
                                    </td>

                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_wash_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?> )" />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_wash_cost' );" />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />
                                    <input type="hidden" id="embratelibid_<? echo $i; ?>" name="embratelibid_<? echo $i; ?>"  class="text_boxes" style="width:20px"  readonly/>
                                 </td>
                                </tr>
                    <?
					}
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>
                            <th  width="92">
                            </th>
                            <th width="92">
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountemb_sum"  name="txtamountemb_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="800" cellspacing="0" class="" border="0">

                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_wash_cost_dtls", 1,0,"reset_form('wash_7','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_wash_cost_dtls", 0,0,"reset_form('wash_7','','',0)",7) ;
						}
						?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>
	<?
    exit();
}

if($action=="wash_chart_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id,rate)
	{
		document.getElementById('charge_id').value=id;
		document.getElementById('charge_value').value=rate;
		parent.emailwindow.hide();
	}
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
	}
	</script>
	</head>
	<body>
	<div align="center">
	<form>
	<input type="hidden" id="charge_id" name="charge_id" />
	<input type="hidden" id="charge_value" name="charge_value" />
	<?
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,9=>$row_status);
		?>
		<table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
		 <thead>
		 <th width="50">SL</th>
		 <th width="100">Company Name</th>
		 <th width="150">Const. Compo.</th>
		 <th width="70">Process Type</th>
		 <th width="70">Process Name</th>
		 <th width="70">Color</th>
		 <th width="80">Width/Dia type</th>

		 <th width="60">In House Rate</th>
		 <th width="80">UOM</th>
		 <th width="60">Rate type</th>
		 <th>Status</th>
		 </thead>
		 </table>
		 <div style=" width:917; overflow:scroll-y; max-height:300px">
		 <table width="900" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
		 <?
		 $sql_data=sql_select("select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id =7  and comapny_id=$cbo_company_name");
		 $i=1;
		 foreach($sql_data as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		 ?>
		 <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
		 <td width="50"><? echo $i; ?></td>
		 <td width="100"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
		 <td width="150"><? echo $row[csf("const_comp")]; ?></td>
		 <td width="70"><? echo $process_type[$row[csf("process_type_id")]]; ?></td>
		  <td width="70"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
		 <td width="70"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
		 <td width="80"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>

		 <td width="60"><? echo $row[csf("in_house_rate")]; ?></td>
		 <td width="80"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
		 <td width="60"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
		 <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
		 </tr>
		 <?
		  $i++;
		 }
		 ?>
		 <script>
		  setFilterGrid("list_view",-1)
		 toggle( "tr_"+"<? echo $embratelibid ?>", '#FFFFCC');
		 </script>
		 </table>
		 </div>
	</form>
	</div>
	</body>
	</html>
	<?
	exit();
}

if ($action=="save_update_delet_wash_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
		$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}else{
			$approved=$approved;
		}
		if($approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
		}
	}
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			 $embratelibid="embratelibid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		  //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,wash_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="wash_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con);die;}
		 $field_array_up="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*charge_lib_id*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {

			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			 $embratelibid="embratelibid_".$i;
			if(str_replace("'",'',$$embupdateid)!="")
			{
				/*$field_array="quotation_id*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
				$data_array ="".$update_id."*".$$cboembname."*".$$cboembtype."*".$$txtembconsdzngmts."*".$$txtembrate."*".$$txtembamount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cboembstatus."*0";
				$rID=sql_update("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,"id","".$$embupdateid."",0);*/
				$id_arr[]=str_replace("'",'',$$embupdateid);
				$data_array_up[str_replace("'",'',$$embupdateid)] =explode(",",("".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0"));

			}
			if(str_replace("'",'',$$embupdateid)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_embe_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		// echo 	$data_array;
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,wash_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="wash_amount";
		    $data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID_up ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$ex_data=explode("***",$data);
		$fabupdateid=$ex_data[0];
		$data_component=$ex_data[1];
		 //echo "10**";
		//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_embe_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and emb_name=3 and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1) $flag=1; else $flag=0;

		/*if($flag==1)
		{
			$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
			$ex_dataComponent=explode("__",$res_data_component);
			$dtls_respone=$ex_dataComponent[0];
			$dtls_id=$ex_dataComponent[1];

			//echo $dtls_id;
			if($dtls_respone==1) $flag=1; else $flag=0;
		}*/
		//echo  '10**'.$flag.'=='.$rID_quo_fab_dtls_del.'=='.$rID_dtls.'=='.$rID_co.'=='.$dtls_respone; die;
		//echo $sql_quo_fab_dtls_del; die;

		//echo $rID_quo_fab_dtls_del.'='.$rID_dtls.'='.$rID_co.'='.$dtls_respone.'='.$flag.'='.$db_type; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$rID_dtls."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID_dtls."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$rID_dtls."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID_dtls."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}
//Wash Cost End======================================================================================================================================================
// Commision Cost  =========================================================================================================================================================
if ($action=="show_commission_cost_listview")
{

?>
<h3 style="width:720px;" align="left" class="accordion_h">+Commission Cost</h3>
       <div id="content_commission_cost" align="left">
    	<fieldset style="width:720px;">
        	<form id="commission_8" autocomplete="off">
            	<table width="700" cellspacing="0" class="rpt_table" border="0" id="tbl_commission_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Particulars</th><th  width="100">Commn. Base</th><th width="90">Commn Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data'");
					if($approved==3){
						$approved=1;
					}
					if($approved==1)
					{
					$disabled=1;
					}
					else
					{
					$disabled=0;
					}
					$data_array=sql_select("select id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, status_active from  wo_pri_quo_commiss_cost_dtls where quotation_id='$data' and status_active=1 and is_deleted=0");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="commissiontr_1" align="center">
                                    <td>
									<?
									echo create_drop_down( "cboparticulars_".$i, 135, $commission_particulars, "",1," -- Select Item --", $row[csf("particulars_id")], "",$disabled,'' );
									//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index )
									?>

                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_".$i, 80, $commission_base_array,"", 1, "-- Select --", $row[csf("commission_base_id")], "calculate_commission_cost(".$i.")",$disabled,"" ); ?></td>

                                   <td>
                                    <input type="text" id="txtcommissionrate_<? echo $i; ?>"  name="txtcommissionrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("commision_rate")];  ?>" onChange="calculate_commission_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_<? echo $i; ?>"  name="txtcommissionamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("commission_amount")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>

                                    <td width="95"><? echo create_drop_down( "cbocommissionstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>
                                    <td>

                                    <input type="hidden" id="commissionupdateid_<? echo $i; ?>" name="commissionupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                                     </td>
                                </tr>

                            <?

						}
					}
					else
					{
					?>
                    	<tr id="commissiontr_1" align="center">
                           <td>
                            <?
                            echo create_drop_down( "cboparticulars_1", 135, $commission_particulars, "",0,"", 1, '','','' );
                            ?>
                            </td>
                            <td><?  echo create_drop_down( "cbocommissionbase_1", 80, $commission_base_array,"", 1, "-- Select --", "", "calculate_commission_cost(1)","","" ); ?></td>

                           <td>
                            <input type="text" id="txtcommissionrate_1"  name="txtcommissionrate_1" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_commission_cost(1 )"/>
                            </td>
                            <td>
                            <input type="text" id="txtcommissionamount_1"  name="txtcommissionamount_1" class="text_boxes_numeric" style="width:95px" value=""   />
                            </td>

                            <td width="95"><? echo create_drop_down( "cbocommissionstatus_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
                            <td>
                            <!--<input type="button" id="increasecommission_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_commission_cost(1 )" />
                            <input type="button" id="decreasecommission_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_commission_cost' );" />-->
                            <input type="hidden" id="commissionupdateid_1" name="commissionupdateid_1"  class="text_boxes" style="width:20px" value=""  />                                    </td>
                        </tr>
                        <tr id="commissiontr_2" align="center">
                           <td>
                            <?
                            echo create_drop_down( "cboparticulars_2", 135, $commission_particulars, "",0,"", 2, '','','' );
                            ?>
                            </td>
                            <td><?  echo create_drop_down( "cbocommissionbase_2", 80, $commission_base_array,"", 1, "-- Select --", "", "calculate_commission_cost(2)","","" ); ?></td>

                           <td>
                            <input type="text" id="txtcommissionrate_2"  name="txtcommissionrate_2" class="text_boxes_numeric" style="width:80px" value="" onChange="calculate_commission_cost(2)"/>
                            </td>
                            <td>
                            <input type="text" id="txtcommissionamount_2"  name="txtcommissionamount_2" class="text_boxes_numeric" style="width:95px" value=""   />
                            </td>

                            <td width="95"><? echo create_drop_down( "cbocommissionstatus_2", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
                            <td>
                            <!--<input type="button" id="increasecommission_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_commission_cost(2 )" />
                            <input type="button" id="decreasecommission_2" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(2 ,'tbl_commission_cost' );" />-->
                            <input type="hidden" id="commissionupdateid_2" name="commissionupdateid_2"  class="text_boxes" style="width:20px" value=""  />                            </td>
                        </tr>
                    <?

					}
					?>
                </tbody>
                </table>
                <table width="700" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>

                            <th width="92">
                            <input type="text" id="txtratecommission_sum"  name="txtratecommission_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountcommission_sum"  name="txtamountcommission_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="700" cellspacing="0" class="" border="0">

                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_commission_cost_dtls", 1,0,"reset_form('commission_8','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_commission_cost_dtls", 0,0,"reset_form('commission_8','','',0)",7) ;
						}
						?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>
	<?
	exit();
}

if ($action=="save_update_delet_commission_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
		$approved=$row[csf('approved')];
		}
		if($approved==3){
		 $approved==1;
		}else{
			 $approved= $approved;
		}
		if($approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
		}
	}
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboparticulars="cboparticulars_".$i;
			 $cbocommissionbase="cbocommissionbase_".$i;
			 $txtcommissionrate="txtcommissionrate_".$i;
			 $txtcommissionamount="txtcommissionamount_".$i;
			 $cbocommissionstatus="cbocommissionstatus_".$i;
			 $commissionupdateid="commissionupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0)";
			$id=$id+1;
		 }

		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,commis_rate,commis_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecommission_sum.",".$txtamountcommission_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="commis_rate*commis_amount";
		    $data_array2 ="".$txtratecommission_sum."*".$txtamountcommission_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con);die;}
		 $field_array_up="particulars_id*commission_base_id*commision_rate*commission_amount*updated_by*update_date*status_active*is_deleted ";
		 $field_array="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {

			 $cboparticulars="cboparticulars_".$i;
			 $cbocommissionbase="cbocommissionbase_".$i;
			 $txtcommissionrate="txtcommissionrate_".$i;
			 $txtcommissionamount="txtcommissionamount_".$i;
			 $cbocommissionstatus="cbocommissionstatus_".$i;
			 $commissionupdateid="commissionupdateid_".$i;
			if(str_replace("'",'',$$commissionupdateid)!="")
			{
				/*$field_array="particulars_id*commission_base_id*commision_rate*commission_amount*updated_by*update_date*status_active*is_deleted ";
			    $data_array="".$$cboparticulars."*".$$cbocommissionbase."*".$$txtcommissionrate."*".$$txtcommissionamount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cbocommissionstatus."*0";
				$rID=sql_update("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,"id","".$$commissionupdateid."",0);*/
				$id_arr[]=str_replace("'",'',$$commissionupdateid);
			    $data_array_up[str_replace("'",'',$$commissionupdateid)]=explode(",",("".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0"));

			}
			if(str_replace("'",'',$$commissionupdateid)=="")
			{
				//$id=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		        //$field_array="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
			    $data_array="(".$id.",".$update_id.",".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0)";
		       // $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,0);
			}

		 }
         $rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_commiss_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array,$data_array,0);
		 }
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,commis_rate,commis_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecommission_sum.",".$txtamountcommission_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="commis_rate*commis_amount";
		    $data_array2 ="".$txtratecommission_sum."*".$txtamountcommission_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
 		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_up ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$ex_data=explode("***",$data);
		$fabupdateid=$ex_data[0];
		$data_component=$ex_data[1];
		 //echo "10**";
		//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_commiss_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1) $flag=1; else $flag=0;

		/*if($flag==1)
		{
			$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
			$ex_dataComponent=explode("__",$res_data_component);
			$dtls_respone=$ex_dataComponent[0];
			$dtls_id=$ex_dataComponent[1];

			//echo $dtls_id;
			if($dtls_respone==1) $flag=1; else $flag=0;
		}*/
		//echo  '10**'.$flag.'=='.$rID_quo_fab_dtls_del.'=='.$rID_dtls.'=='.$rID_co.'=='.$dtls_respone; die;
		//echo $sql_quo_fab_dtls_del; die;

		//echo $rID_quo_fab_dtls_del.'='.$rID_dtls.'='.$rID_co.'='.$dtls_respone.'='.$flag.'='.$db_type; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}
// Commision Cost End  =========================================================================================================================================================

/*if($action=="sum_fab_yarn_trim_emblish_value")
{
	$data=explode("_",$data);
	$quotation_id=$data[0];
	$commercial_cost_method=$data[1];
	$amount=0;//wo_pri_quo_fabric_cost_dtls // 	fabric_source=2
	if($commercial_cost_method==1) $field_cond="(trims_cost) as amount";
	else if($commercial_cost_method==4) $field_cond="(trims_cost+embel_cost+wash_cost) as amount";
	$sql_fab="select amount as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$quotation_id and fabric_source in (1,2) and status_active=1 and is_deleted=0";
	$data_fab=sql_select($sql_fab);
	$fab_amount=0;
	foreach($data_fab as $row )
	{
		$fab_amount+=$row[csf("amount")];
	}
	$sql_yarn="select amount as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$quotation_id and status_active=1 and is_deleted=0";
	$data_yarn=sql_select($sql_yarn);
	$yarn_amount=0;
	foreach($data_yarn as $row )
	{
		$yarn_amount+=$row[csf("amount")];
	}

	$data_array=sql_select("select  $field_cond from wo_price_quotation_costing_mst where quotation_id=$quotation_id and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$ft_amount=$row[csf("amount")];
	}
	$amount=$ft_amount+$yarn_amount+$fab_amount;
	echo $amount;
	die;
}*/
if($action=="sum_fab_yarn_trim_emblish_value")
{
	$data=explode("_",$data);
	$quotation_id=$data[0];
	$commercial_cost_method=$data[1];
	$amount=0;
    $sql_fab="select amount as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$quotation_id and fabric_source in (1,2) and status_active=1 and is_deleted=0";
	$data_fab=sql_select($sql_fab);
	$fab_amount=0;
	foreach($data_fab as $row )
	{
		$fab_amount+=$row[csf("amount")];
	}
	$sql_yarn="select amount as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$quotation_id and status_active=1 and is_deleted=0";
	$data_yarn=sql_select($sql_yarn);
	$yarn_amount=0;
	foreach($data_yarn as $row )
	{
		$yarn_amount+=$row[csf("amount")];
	}

	$data_array=sql_select("select trims_cost, embel_cost, wash_cost, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_pre_cost, studio_pre_cost, common_oh from wo_price_quotation_costing_mst where quotation_id=$quotation_id and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
        if($commercial_cost_method==1)
        {
          $ft_amount=$row[csf("trims_cost")];
        }
        if($commercial_cost_method==4)
        {
            $ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")];
        }
        if($commercial_cost_method==5)
        {
            $ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")]+$row[csf("common_oh")];
        }

	}
	$amount=$ft_amount+$yarn_amount+$fab_amount;
	echo $amount;
	disconnect($con);die;
}

// Comarcial Cost End  =========================================================================================================================================================
if ($action=="show_comarcial_cost_listview")
{
	$company_id=return_field_value("company_id as company_id", "wo_price_quotation", "id='$data'","company_id");

$sql_commercial_data="select commercial_cost_method,commercial_cost_percent,editable from variable_order_tracking where company_name=".$company_id."  and variable_list=58 and status_active=1 and is_deleted=0";
$commercial_result=sql_select($sql_commercial_data);
/*foreach($commercial_result as $row)
{
	$commercial_cost_method=$row[csf("commercial_cost_method")];
	if($commercial_cost_method=="" || $commercial_cost_method==0) $commercial_cost_method=1;else $commercial_cost_method=$commercial_cost_method;
	$commercial_cost_percent=$row[csf("commercial_cost_percent")];
	if($commercial_cost_percent=="" || $commercial_cost_percent==0) $commercial_cost_percent=0;else  $commercial_cost_percent=$commercial_cost_percent;

	$editable=$row[csf("editable")];
	if($editable==1) $readonly=0; else $readonly=1	;
	if($commercial_cost_method==1)
		{
			//wo_pri_quo_fab_yarn_cost_dtls
			$sql_fab="select amount as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$data and fabric_source in (1,2) and status_active=1 and is_deleted=0";
			$data_fab=sql_select($sql_fab);
			$fab_amount=0;
			foreach($data_fab as $row )
			{
				$fab_amount+=$row[csf("amount")];
			}

			$sql_yarn="select amount as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$data and status_active=1 and is_deleted=0";
			$data_yarn=sql_select($sql_yarn);
			$yarn_amount=0;
			foreach( $data_yarn as $row )
			{
				$yarn_amount+=def_number_format($row[csf("amount")],5,"");
			}

			$sql_comm="select trims_cost as amount from wo_price_quotation_costing_mst where quotation_id=$data and status_active=1 and is_deleted=0";
			$data_array=sql_select($sql_comm);
			foreach( $data_array as $row )
			{
				$t_amount=def_number_format($row[csf("amount")],5,"");
			}
			$amount=$t_amount+$yarn_amount+$fab_amount;
		}
		else if($commercial_cost_method==4)
		{
			$sql_fab="select sum(amount) as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$data and fabric_source in (1,2) and status_active=1 and is_deleted=0";
			$data_fab=sql_select($sql_fab);
			$fab_amount=0;
			foreach($data_fab as $row )
			{
				$fab_amount+=$row[csf("amount")];
			}
			$sql_yarn="select sum(amount) as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$data and status_active=1 and is_deleted=0";
			$data_yarn=sql_select($sql_yarn);
			$yarn_amount=0;
			foreach( $data_yarn as $row )
			{
				$yarn_amount+=def_number_format($row[csf("amount")],5,"");
			}

			$sql_comm="select sum(trims_cost+embel_cost+wash_cost) as amount from wo_price_quotation_costing_mst where quotation_id=$data and status_active=1 and is_deleted=0";
			$data_array=sql_select($sql_comm);
			foreach( $data_array as $row )
			{
				$te_amount=def_number_format($row[csf("amount")],5,"");
			}
			$amount=$te_amount+$yarn_amount+$fab_amount;
		}
		$com_amount=def_number_format(($amount*($commercial_cost_percent/100)),5,"");
		//echo $te_amount.'='.$yarn_amount.'='.$fab_amount;

	//if($editable)


}*/
foreach($commercial_result as $row)
{
	$commercial_cost_method=$row[csf("commercial_cost_method")];
	if($commercial_cost_method=="" || $commercial_cost_method==0) $commercial_cost_method=1;else $commercial_cost_method=$commercial_cost_method;
	$commercial_cost_percent=$row[csf("commercial_cost_percent")];
	if($commercial_cost_percent=="" || $commercial_cost_percent==0) $commercial_cost_percent=0;else  $commercial_cost_percent=$commercial_cost_percent;

	$editable=$row[csf("editable")];
	if($editable==1) $readonly=0; else $readonly=1;
        $sql_fab="select amount as amount from wo_pri_quo_fabric_cost_dtls where quotation_id=$data and fabric_source in (1,2) and status_active=1 and is_deleted=0";
        $data_fab=sql_select($sql_fab);
        $fab_amount=0;
        foreach($data_fab as $row )
        {
            $fab_amount+=$row[csf("amount")];
        }
        $sql_yarn="select amount as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$data and status_active=1 and is_deleted=0";
        $data_yarn=sql_select($sql_yarn);
        $yarn_amount=0;
        foreach($data_yarn as $row )
        {
            $yarn_amount+=$row[csf("amount")];
        }

        $data_array=sql_select("select  trims_cost ,embel_cost ,wash_cost ,lab_test ,inspection,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,design_pre_cost,studio_pre_cost,common_oh from wo_price_quotation_costing_mst where quotation_id=$data and status_active=1 and is_deleted=0");
        foreach( $data_array as $row )
        {
            if($commercial_cost_method==1)
            {
              $ft_amount=$row[csf("trims_cost")];
            }
            if($commercial_cost_method==4)
            {
                $ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")];
            }
            if($commercial_cost_method==5)
            {
                $ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")]+$row[csf("common_oh")];
            }

        }

        $amount=$ft_amount+$yarn_amount+$fab_amount;
        //echo $amount; die;

}
$com_amount=def_number_format(($amount*($commercial_cost_percent/100)),5,"");
?>
<h3 style="width:820px;" align="left" class="accordion_h" >+Commercial Cost</h3>
       <div id="content_comarcial_cost"  align="left">
    	<fieldset style="width:820px;">
        	<form id="comarcial_9" autocomplete="off">
            <input type="hidden" id="txt_commercial_cost_method" value="<? echo $commercial_cost_method;?>"/>
            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_comarcial_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Item</th><th width="90">Comml Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$approved=return_field_value("approved", "wo_price_quotation", "id='$data'");
					if($approved==3){
						$approved=1;
					}
					if($approved==1) $disabled=1; else $disabled=0;
					$data_array=sql_select("select id, quotation_id, item_id, base_id, rate,amount,status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id='$data' and status_active=1 and is_deleted=0");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="comarcialtr_1" align="center">
                                    <td>
									<?
									echo create_drop_down( "cboitem_".$i, 135, $camarcial_items, "",1," -- Select Item --", $row[csf("item_id")], "",$disabled,'' );
									?>

                                    </td>


                                   <td>
                                    <input type="text" id="txtcomarcialrate_<? echo $i; ?>"  name="txtcomarcialrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("rate")];  ?>" <? if($readonly==0){echo "";}else{echo "readonly";}?> onChange="calculate_comarcial_cost( <? echo $i;?>,'rate' )"   />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcomarcialamount_<? echo $i; ?>"  name="txtcomarcialamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:95px" value="<? echo $row[csf("amount")]; ?>" onChange="calculate_comarcial_cost( <? echo $i;?>,'amount' )" <? if($disabled==0){echo "";}else{echo "disabled";}?>  />
                                    </td>

                                    <td width="95" style="display:none"><? echo create_drop_down( "cbocomarcialstatus_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>
                                    <td>
                                    <input type="button" id="increasecomarcial_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_comarcial_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreasecomarcial_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_comarcial_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="comarcialupdateid_<? echo $i; ?>" name="comarcialupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />
                                     </td>
                                </tr>

                            <?

						}
					}
					else

					{

					?>
                    <tr id="comarcialtr_1" align="center">
                                   <td>
									<?
									echo create_drop_down( "cboitem_1", 135, $camarcial_items, "",0,"", 4, '','','' );
									?>
                                    </td>


                                   <td>
                                    <input type="text" id="txtcomarcialrate_1"  name="txtcomarcialrate_1" class="text_boxes_numeric" style="width:80px" value="<? echo $commercial_cost_percent; ?>" <? if($readonly==0){echo "";}else{echo "readonly";}?> onChange="calculate_comarcial_cost(1,'rate' )"  />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcomarcialamount_1"  name="txtcomarcialamount_1" class="text_boxes_numeric" style="width:95px"  value="<? echo $com_amount; ?>" <? if($readonly==0){echo "";}else{echo "readonly";}?>  onChange="calculate_comarcial_cost(1,'amount' )"   />
                                    </td>

                                    <td width="95" style="display:none"><? echo create_drop_down( "cbocomarcialstatus_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>
                                    <td>
                                    <input type="button" id="increasecomarcial_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_comarcial_cost(1 )" />
                                    <input type="button" id="decreasecomarcial_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_comarcial_cost' );" />
                                    <input type="hidden" id="comarcialupdateid_1" name="comarcialupdateid_1"  class="text_boxes" style="width:20px" value=""  />                                    </td>
                                </tr>
                    <?

					}
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="151">Sum</th>

                            <th width="92">
                            <input type="text" id="txtratecomarcial_sum"  name="txtratecomarcial_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountcomarcial_sum"  name="txtamountcomarcial_sum" class="text_boxes_numeric" style="width:95px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>

                <table width="800" cellspacing="0" class="" border="0">

                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">

						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_comarcial_cost_dtls", 1,0,"reset_form('comarcial_9','','',0)",9) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_comarcial_cost_dtls", 0,0,"reset_form('comarcial_9','','',0)",9) ;
						}
						?>
                        </td>
                    </tr>
                </table>

            </form>
        </fieldset>
        </div>
	<?
	exit();
}

if($action=="sum_fab_yarn_trim_value")
{
	$amount=0;
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pri_quo_sum_dtls where quotation_id=$data and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=$row[csf("amount")];
	}
	echo $amount;
	die;
}

function update_comarcial_cost_q($quatation_id)
{
	$data_currier_per = sql_select("select commercial_cost_method, commercial_cost_percent from variable_order_tracking where company_name=" . $company_name . "  and variable_list=57 and status_active=1 and is_deleted=0");
	$amount=0;
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pri_quo_sum_dtls where quotation_id=$quatation_id and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=def_number_format($row[csf("amount")],5,"");
	}

	$data_array1=sql_select("select id,rate from wo_pri_quo_comarcial_cost_dtls where quotation_id=$quatation_id and status_active=1 and is_deleted=0");
	foreach( $data_array1 as $row1 )
	{
		$com_amount=def_number_format(($amount*($row1[csf("rate")]/100)),5,"");
		$rID_de=execute_query( "update  wo_pri_quo_comarcial_cost_dtls set amount=$com_amount where id='".$row1[csf("id")]."'",1 );
	}
}

if ($action=="save_update_delet_comarcial_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($update_id){
		$approved=0;
		$sql=sql_select("select approved from wo_price_quotation where id=$update_id");
		foreach($sql as $row){
		$approved=$row[csf('approved')];
		}
		if($approved==3){
			$approved=1;
		}else{
			$approved=$approved;
		}
		if($approved==1){
		echo "approved**".str_replace("'","",$update_id);
		disconnect($con);die;
		}
	}
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 $field_array="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboitem="cboitem_".$i;
			 $txtcomarcialrate="txtcomarcialrate_".$i;
			 $txtcomarcialamount="txtcomarcialamount_".$i;
			 $cbocomarcialstatus="cbocomarcialstatus_".$i;
			 $comarcialupdateid="comarcialupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0)";
			$id=$id+1;

		 }
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,comar_rate,comar_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecomarcial_sum.",".$txtamountcomarcial_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="comar_rate*comar_amount";
			$data_array2 ="".$txtratecomarcial_sum."*".$txtamountcomarcial_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1) // Update
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con);die;}
		 $field_array_up="item_id*rate*amount*updated_by*update_date*status_active*is_deleted ";
		 $field_array="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboitem="cboitem_".$i;
			 $txtcomarcialrate="txtcomarcialrate_".$i;
			 $txtcomarcialamount="txtcomarcialamount_".$i;
			 $cbocomarcialstatus="cbocomarcialstatus_".$i;
			 $comarcialupdateid="comarcialupdateid_".$i;
			if(str_replace("'",'',$$comarcialupdateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$comarcialupdateid);
			    $data_array_up[str_replace("'",'',$$comarcialupdateid)] = explode(",",("".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0"));
			}
			if(str_replace("'",'',$$comarcialupdateid)=="")
			{
			    if ($add_comma!=0) $data_array .=",";
			    $data_array .="(".$id.",".$update_id.",".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0)";
				$id=$id+1;
			    $add_comma++;
			}
		 }
 		$rID_up=execute_query(bulk_update_sql_statement( "wo_pri_quo_comarcial_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($data_array != "")
		{
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array,$data_array,0);
		}
		//=======================sum=================
		$wo_pri_quo_sum_dtls_quotation_id="";
		$queryText= "select quotation_id from  wo_pri_quo_sum_dtls where quotation_id =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pri_quo_sum_dtls_quotation_id= $result[csf('quotation_id')];
		}
		if($wo_pri_quo_sum_dtls_quotation_id=="")
		{
			$wo_pri_quo_sum_dtls_id=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
			$field_array2="id,quotation_id,comar_rate,comar_amount";
			$data_array2="(".$wo_pri_quo_sum_dtls_id.",".$update_id.",".$txtratecomarcial_sum.",".$txtamountcomarcial_sum.")";
			$rID2=sql_insert("wo_pri_quo_sum_dtls",$field_array2,$data_array2,1);
		}

		if($wo_pri_quo_sum_dtls_quotation_id!="")
		{
			$field_array2="comar_rate*comar_amount";
			$data_array2 ="".$txtratecomarcial_sum."*".$txtamountcomarcial_sum."";
			$rID2=sql_update("wo_pri_quo_sum_dtls",$field_array2,$data_array2,"quotation_id","".$update_id."",1);
		}
		//=======================sum End =================
		//echo '10**'.$rID_up.'**'.$rID2;die;
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID_up ){
				mysql_query("COMMIT");
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID_up ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID_up;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID_up;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$ex_data=explode("***",$data);
		$fabupdateid=$ex_data[0];
		$data_component=$ex_data[1];
		 //echo "10**";
		//echo "select id from  wo_pri_quo_fabric_cost_dtls where  id =".$data."";
		$flag=1;
		$rID_dtls=execute_query( "update wo_pri_quo_comarcial_cost_dtls set status_active=0, is_deleted=1 where quotation_id =".$update_id." and status_active=1 and is_deleted=0",1);

		//execute_query( "delete from wo_pri_quo_fabric_cost_dtls where  id =".$fabupdateid."",0);
		if($rID_dtls==1) $flag=1; else $flag=0;

		/*if($flag==1)
		{
			$res_data_component=fnc_quotation_entry_component($data_component.'***0***0***1');
			$ex_dataComponent=explode("__",$res_data_component);
			$dtls_respone=$ex_dataComponent[0];
			$dtls_id=$ex_dataComponent[1];

			//echo $dtls_id;
			if($dtls_respone==1) $flag=1; else $flag=0;
		}*/
		//echo  '10**'.$flag.'=='.$rID_quo_fab_dtls_del.'=='.$rID_dtls.'=='.$rID_co.'=='.$dtls_respone; die;
		//echo $sql_quo_fab_dtls_del; die;

		//echo $rID_quo_fab_dtls_del.'='.$rID_dtls.'='.$rID_co.'='.$dtls_respone.'='.$flag.'='.$db_type; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$new_job_no[0]."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
}

// Comarcial Cost End  =========================================================================================================================================================
//=======================================================END END END====================================================================================
// report generate start here

if($action=="generate_report" && $type=="preCostRpt")
{
	extract($_REQUEST);
 	 $txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	  $txt_quotationdate=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id=''; else $quotation_id=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref=".$txt_style_ref."";

 	$path = str_replace("'", "", $path);
	if ($path != "") {
		$path = $path;
	} else {
		$path = "../../";
	}

	//array for display name
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');//body_part_id
	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=1 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=20 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	//$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pri_quo_fabric_cost_dtls", "quotation_id=$txt_quotation_id and body_part_id=20");
	if($db_type==0)
		{
		 if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-')."'";
		 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.sew_smv,a.remarks,a.sew_effi_percent,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.	est_ship_date,a.op_date,a.approved,DATEDIFF(est_ship_date,op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
				from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c
				where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1  and b.status_active=1  $quotation_id $company_name $cbo_buyer_name $txt_quotation_date
				order by a.id";
		}

	if($db_type==2)
	  {
	  if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-',1)."'";
	     $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.sew_smv,a.remarks,a.sew_effi_percent,a.gmts_item_id ,a.order_uom ,a.offer_qnty,
		a.est_ship_date,a.op_date,a.approved,(est_ship_date - op_date ) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1  and b.status_active=1  $quotation_id $company_name $cbo_buyer_name $txt_quotation_date
			group by a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.sew_smv,a.remarks,a.sew_effi_percent,a.gmts_item_id ,a.order_uom ,a.offer_qnty,		a.est_ship_date,a.op_date,a.approved,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			order by a.id";
	  }


		$data_array=sql_select($sql);
		$first_day_this_month = date('01-m-Y',strtotime($txt_quotationdate));
		$last_date  = date("t-m-Y", strtotime($txt_quotationdate));
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$first_day_this_month),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$last_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$first_day_this_month),"","",1);
				$end_date=change_date_format(str_replace("'","",$last_date),"","",1);
			}
			$date_quote_cond=" and applying_period_to_date between '$start_date' and '$end_date'";

	 $cpm_cpm_sql="select id,company_id,cost_per_minute from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $date_quote_cond order by id desc";
	$cpm_cpm=sql_select($cpm_cpm_sql);
			foreach($cpm_cpm as $row)
			{
				$tot_cost_per_minute=$row[csf('cost_per_minute')];
			} //var_dump($asking_profit_arr);
			//echo $tot_cost_per_minute;
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Quotation</div>
	<?
	foreach ($data_array as $row)
	{
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=$row[csf("price_with_commn_pcs")];
		$sew_effi_percent=$row[csf("sew_effi_percent")];
		$sew_smv=$row[csf("sew_smv")];
		$remarks=$row[csf("remarks")];
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("revised_price")];
		}
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("a1st_quoted_price")];
		}


		$order_values = $row[csf("offer_qnty")]*$avg_unit_price;

		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $row[csf("id")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <?
							if($row[csf("order_uom")]==1)
							{
							  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];
							}
							else
							{
								$gmt_item=explode(',',$row[csf("gmts_item_id")]);
								foreach($gmt_item as $key=>$val)
								{
									$grmnt_items .=$garments_item[$val].", ";
								}

							}

						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref")]; ?></b></td>
                        <td>Order UOM </td>
                        <td><b><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                        <td>Offer Qnty</td>
                        <td><b><? echo $row[csf("offer_qnty")]; ?></b></td>
                    </tr>

                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_knit_req_kg")],4); ?> (Kg)</b></td>
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_woven_req_yds")],4); ?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($avg_unit_price,4); ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo number_format($row[csf("fab_yarn_req_kg")],4) ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per_id")]]; ?></b></td>
                        <td>Target Price </td>
                        <td><b><? echo $row[csf("terget_qty")]; ?></b></td>
                    </tr>
                     <tr>
                    	<td>GSM</td>
                        <td><b><? $gsm_weights_top=implode(",",array_unique(explode(",",$gsm_weight_top)));
						$gsm_weight_bottom=implode(",",array_unique(explode(",",$gsm_weight_bottom)));
						if($gsm_weights_top!='') $gsm_weightTop=$gsm_weights_top;else $gsm_weightTop='';
						if($gsm_weight_bottom!='' && $gsm_weights_top!='') $gsm_weightBottom=" ,".$gsm_weight_bottom;
						else if($gsm_weight_bottom!='' && $gsm_weights_top=='') $gsm_weightBottom=$gsm_weight_bottom;
						else $gsm_weightBottom='';
						echo $gsm_weightTop .$gsm_weightBottom; ?></b></td>
                        <td>Style Desc</td>
                        <td><b><? echo $row[csf("style_desc")]; ?></b></td>
                        <td>Season</td>
                        <td><b><? echo $season_name_arr[$row[csf("season_buyer_wise")]]; ?></b></td>

                    </tr>
                    <tr>
                    	<td> OP Date </td>
                        <td><b><? echo change_date_format($row[csf("op_date")]); ?></b></td>
                    	<td> Est.Ship Date </td>
                        <td><b><? echo change_date_format($row[csf("est_ship_date")]); ?></b></td>
                        <td> Lead Time </td>
                        <td>
						<?
						if($row[csf("op_date")]!="" &&  $row[csf("est_ship_date")]!="")
                       		echo diff_in_weeks_and_days($row[csf("est_ship_date")], $row[csf("op_date")],'days');
						else
							echo "";

						?>
                        </td>
                    </tr>
                      <tr>
                    <td align="center" height="10" colspan="6" valign="top" id="app_sms" style="font-size:18px;"><font color="#FF0000"><? if( $row[csf("approved")]==1 || $row[csf("approved")]==3){echo "This Quotation is Approved ";} else {echo "";} ?> </font> </td>
                    </tr>
                </table>
            <?

			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_val=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$row[csf("offer_qnty")];


	}//end first foearch
	//start	all summary report here -------------------------------------------
	if($db_type==0) $group_cond="";
	if($db_type==2)
	  {
	$group_cond="group by fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn";
	   }
	$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 $group_cond";
	$data_array=sql_select($sql);
	$others_cost_value=0;
 	?>
    <div style="margin-top:15px">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <tr style="font-weight:bold">
            <td width="80">SL</td>
            <td width="300">Particulars</td>
            <td width="100">Cost</td>
            <td width="100">Amount (USD)</td>
            <td width="180">% to Ord. Value</td>
            </tr>
            <?
            $percent=0;
            $price_dzn=0;
            $sl=0;
            foreach( $data_array as $row )
            {
				$sl=$sl+1;
				$price_dzn=$row[csf("confirm_price_dzn")];
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];

				$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				if($commission_base_id==1) $commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				if($commission_base_id==2) $commision=$commision_rate*$order_price_per_dzn;
				if($commission_base_id==3) $commision=($commision_rate/12)*$order_price_per_dzn;

				if($zero_value==1)
				{
				?>
					<tr>
                        <td><? echo $sl;?></td>
                        <td align="left"><b>Order Price/<? echo $costing_val; ?></b></td>
                        <td></td>
                        <td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                        <td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
                        <td><? echo ++$sl;?></td>
                        <td align="left"><b>Less Commision/<? echo $costing_val; ?></b></td>
                        <td></td>
                        <td align="right"><b><? echo number_format($row[csf("commission")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                        <td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left"><b>Net Quoted Price</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?  $less_commision_cost_dzn=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]); echo number_format($less_commision_cost_dzn,4); ?></b></td>
                        <td align="center"><b><? echo "100.00%"; ?></b></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">All Fabric Cost</td>
                        <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                        <td align="right" rowspan="17">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); $percent+=$row[csf("fabric_cost_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Trims Cost</td>
                        <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2);  $percent+=$row[csf("trims_cost_percent")];?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Embellishment Cost</td>
                        <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); $percent+=$row[csf("embel_cost_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Commercial Cost</td>
                        <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); $percent+=$row[csf("comm_cost_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Washing Cost (Gmt.)</td>
                        <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); $percent+=$row[csf("wash_cost_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Lab Test</td>
                        <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); $percent+=$row[csf("lab_test_percent")];  ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Inspection Cost</td>
                        <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("inspection_percent")],2); $percent+=$row[csf("inspection_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">CM Cost</td>
                        <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); $percent+=$row[csf("cm_cost_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Freight Cost</td>
                        <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("freight_percent")],2); $percent+=$row[csf("freight_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Currier Cost</td>
                        <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("currier_percent")],2); $percent+=$row[csf("currier_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Certificate Cost</td>
                        <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("certificate_percent")],2); $percent+=$row[csf("certificate_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Design Cost</td>
                        <td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("design_percent")],2); $percent+=$row[csf("design_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Studio Cost</td>
                        <td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("studio_percent")],2); $percent+=$row[csf("studio_percent")]; ?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left"> Operating Expenses </td><!--Office OH-->
                        <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); $percent+=$row[csf("common_oh_percent")];?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Deprec. & Amort.</td>
                        <td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("depr_amor_po_price")],2); $percent+=$row[csf("depr_amor_po_price")];?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Interest</td>
                        <td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("interest_po_price")],2); $percent+=$row[csf("interest_po_price")];?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Income Tax </td>
                        <td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
                        <td align="center"><? echo number_format($row[csf("income_tax_po_price")],2); $percent+=$row[csf("income_tax_po_price")];?>%</td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left"><b>Total Cost (4-21)</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?  $final_cost_dzn=$row[csf("total_cost")]; echo number_format($final_cost_dzn,4); ?></b></td>
                        <td align="center"><b><?  echo number_format($percent,4);//echo number_format(($final_cost_dzn/$row[csf("total_cost")])*100,2);  ?>%</b></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Margin/<? echo $costing_val; ?> </td>
                        <td>&nbsp;</td>
                        <td align="right"><? $margin_dzn=$less_commision_cost_dzn-$final_cost_dzn; echo number_format($margin_dzn,4); ?></td>
                        <td align="center"><? echo  number_format(($margin_dzn/$less_commision_cost_dzn*100),4); ?></td>
					</tr>
					<tr>
						<?
                        $net_quoted_price=number_format($row[csf("confirm_price")],4);
                        if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("revised_price")],4);
                        if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("a1st_quoted_price")],4);

                        $cost_pcs=number_format($final_cost_dzn/$order_price_per_dzn,4);
                        if($cost_pcs>$net_quoted_price)
                        {
                            $bgcolor_net_quoted_price="#FF0000";
                            $smg="Cost is hiegher than quoted price.";
                        }
                        ?>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Net Quoted Price/ Pcs</td>
                        <td>&nbsp;</td>
                        <td align="right" bgcolor="<? echo $bgcolor_net_quoted_price;  ?>"><? echo number_format($net_quoted_price,4); ?></td>
                        <td align="right"></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Cost /Pcs</td>
                        <td>&nbsp;</td>
                        <td align="right"><? $cost_per_pice=number_format($final_cost_dzn/$order_price_per_dzn,4); echo number_format($final_cost_dzn/$order_price_per_dzn,4); ?></td>
                        <td align="center"></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Margin/Pcs</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format(($net_quoted_price-$cost_per_pice),4); ?></td>
                        <td align="center"></td>
					</tr>
					<?
				}
				else
				{
				?>
					<tr>
                        <td><? echo $sl;?></td>
                        <td align="left"><b>Order Price/<? echo $costing_val; ?></b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                        <td align="center"><? //echo "100.00%"; ?></td>
					</tr>

					<tr>
                        <td><? echo ++$sl;?></td>
                        <td align="left"><b>Less Commision/<? echo $costing_val; ?></b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><? echo number_format($row[csf("commission")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                        <td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left"><b>Net Quoted Price</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?  $less_commision_cost_dzn=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]); echo number_format($less_commision_cost_dzn,4); ?></b></td>
                        <td align="center"><b><? echo "100.00%"; ?></b></td>
					</tr>
                    <?
					if($row[csf("fabric_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">All Fabric Cost</td>
                        <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); $percent+=$row[csf("fabric_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("trims_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Trims Cost</td>
                        <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2);  $percent+=$row[csf("trims_cost_percent")];?>%</td>
					</tr>
                    <?
					}
					if($row[csf("embel_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Embellishment Cost</td>
                        <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); $percent+=$row[csf("embel_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("comm_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Commercial Cost</td>
                        <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); $percent+=$row[csf("comm_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("wash_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Washing Cost (Gmt.)</td>
                        <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); $percent+=$row[csf("wash_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("lab_test")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Lab Test</td>
                        <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); $percent+=$row[csf("lab_test_percent")];  ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("inspection")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Inspection Cost</td>
                        <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("inspection_percent")],2); $percent+=$row[csf("inspection_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("cm_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">CM Cost</td>
                        <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); $percent+=$row[csf("cm_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("freight")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Freight Cost</td>
                        <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("freight_percent")],2); $percent+=$row[csf("freight_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("currier_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Currier Cost</td>
                        <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("currier_percent")],2); $percent+=$row[csf("currier_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("certificate_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Certificate Cost</td>
                        <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("certificate_percent")],2); $percent+=$row[csf("certificate_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("design_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Design Cost</td>
                        <td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("design_percent")],2); $percent+=$row[csf("design_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("studio_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Studio Cost</td>
                        <td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("studio_percent")],2); $percent+=$row[csf("studio_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("common_oh")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left"> Operating Expenses </td><!--Office OH-->
                        <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); $percent+=$row[csf("common_oh_percent")];?>%</td>
					</tr>
                    <?
					}
					if($row[csf("depr_amor_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Deprec. & Amort.</td>
                        <td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("depr_amor_po_price")],2); $percent+=$row[csf("depr_amor_po_price")];?>%</td>
					</tr>
                    <?
					}
					if($row[csf("interest_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Interest</td>
                        <td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("interest_po_price")],2); $percent+=$row[csf("interest_po_price")];?>%</td>
					</tr>
                    <?
					}
					if($row[csf("income_tax_pre_cost")]!=0)
					{
					?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Income Tax </td>
                        <td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo number_format($row[csf("income_tax_po_price")],2); $percent+=$row[csf("income_tax_po_price")];?>%</td>
					</tr>
                    <? } ?>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left"><b>Total Cost (4-<? echo $sl-1;?>)</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?  $final_cost_dzn=$row[csf("total_cost")]; echo number_format($final_cost_dzn,4); ?></b></td>
                        <td align="center"><b><?  echo number_format($percent,4);//echo number_format(($final_cost_dzn/$row[csf("total_cost")])*100,2);  ?>%</b></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Margin/<? echo $costing_val; ?> </td>
                        <td>&nbsp;</td>
                        <td align="right"><? $margin_dzn=$less_commision_cost_dzn-$final_cost_dzn; echo number_format($margin_dzn,4); ?></td>
                        <td align="center"><? echo  number_format(($margin_dzn/$less_commision_cost_dzn*100),4); ?></td>
					</tr>
					<tr>
						<?
                        $net_quoted_price=number_format($row[csf("confirm_price")],4);
                        if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("revised_price")],4);
                        if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("a1st_quoted_price")],4);

                        $cost_pcs=number_format($final_cost_dzn/$order_price_per_dzn,4);
                        if($cost_pcs>$net_quoted_price)
                        {
                            $bgcolor_net_quoted_price="#FF0000";
                            $smg="Cost is hiegher than quoted price.";
                        }
                        ?>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Net Quoted Price/ Pcs</td>
                        <td>&nbsp;</td>
                        <td align="right" bgcolor="<? echo $bgcolor_net_quoted_price;  ?>"><? echo number_format($net_quoted_price,4); ?></td>
                        <td align="right"></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Cost /Pcs</td>
                        <td>&nbsp;</td>
                        <td align="right"><? $cost_per_pice=number_format($final_cost_dzn/$order_price_per_dzn,4); echo number_format($final_cost_dzn/$order_price_per_dzn,4); ?></td>
                        <td align="center"></td>
					</tr>
					<tr>
                        <td><? echo ++$sl; ?></td>
                        <td align="left">Margin/Pcs</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format(($net_quoted_price-$cost_per_pice),4); ?></td>
                        <td align="center"></td>
					</tr>
					<?
				}
            }
            ?>
        </table>
    </div>
    <?
	//End all summary report here -------------------------------------------

	//2	All Fabric Cost part here-------------------------------------------
	$sql = "select id, quotation_id, body_part_id, fab_nature_id, color_type_id, construction, composition, avg_cons, fabric_source, gsm_weight, rate, amount, avg_finish_cons, status_active from wo_pri_quo_fabric_cost_dtls where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";

		$knit_subtotal_avg_cons=0; $knit_subtotal_amount=0; $woven_subtotal_avg_cons=0; $woven_subtotal_amount=0; $grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")].", ".$row[csf("gsm_weight")];
				$i++;
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';

				$knit_subtotal_avg_cons += $row[csf("avg_cons")];
				$knit_subtotal_amount += $row[csf("amount")];
			}

			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")].", ".$row[csf("gsm_weight")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';

				$woven_subtotal_avg_cons += $row[csf("avg_cons")];
				$woven_subtotal_amount += $row[csf("amount")];
			}
        }

		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/'.$costing_val.'</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="6">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;

		//knit fabrics table here
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;

		//woven fabrics table here
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
         echo $woven_fab;
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------


		//Start	Yarn Cost part report here -------------------------------------------

		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 group by id,count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate,cons_ratio";

		$data_array=sql_select($sql);
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            {
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];

			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	//End Yarn Cost part report here -------------------------------------------



  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id=".$txt_quotation_id." and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];

				if(str_replace(",","",$item_descrition)=="")
				{
					$item_descrition="All Fabrics";
				}
			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_type")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------



  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pri_quo_trim_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );

			?>
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------



	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pri_quo_embe_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 and emb_name !=3";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------


  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, item_id, rate, amount, status_active
			from  wo_pri_quo_comarcial_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);

	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
  			?>
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------


  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
			$commission_amount=0;
			if($row[csf("commission_base_id")]==1)
				{
					$commission_amount=($row[csf("commision_rate")]*$price_dzn)/100;
				}
				if($row[csf("commission_base_id")]==2)
				{
					$commission_amount=$row[csf("commision_rate")]*$order_price_per_dzn;
				}
				if($row[csf("commission_base_id")]==3)
				{
					$commission_amount=($row[csf("commision_rate")]/12)*$order_price_per_dzn;
				}
  			?>
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><?  echo number_format($commission_amount,4); ?></td>
                </tr>
            <?
                 $total_amount += $commission_amount;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commission Cost Part report here -------------------------------------------


	//start	Other Components part report here -------------------------------------------
	if($db_type==0)
		{
		$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,wash_cost,wash_cost_percent,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent ,common_oh,common_oh_percent,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,design_pre_cost,design_percent,studio_pre_cost,studio_percent,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,confirm_price
				from wo_price_quotation_costing_mst
				where quotation_id=".$txt_quotation_id." and status_active=1 and  is_deleted=0";
		}
		else if($db_type==2)
		{
			$sql = "select fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, wash_cost, wash_cost_percent, embel_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, design_pre_cost, design_percent, studio_pre_cost, studio_percent, common_oh, common_oh_percent, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, total_cost, total_cost_percent, final_cost_dzn, final_cost_dzn_percent, confirm_price_dzn, confirm_price_dzn_percent, final_cost_pcs, margin_dzn, margin_dzn_percent, confirm_price from wo_price_quotation_costing_mst where quotation_id=".$txt_quotation_id." and status_active=1 and  is_deleted=0";
		}
	$data_array=sql_select($sql);
	?>
    <div style="margin-top:15px">
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
        <label><b>Others Components</b></label>
        <tr style="font-weight:bold">
            <td width="150">Particulars</td>
            <td width="100">Amount (USD)</td>
        </tr>
        <?
        $total_amount=0;
        foreach( $data_array as $row )
        {
			if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")]) $smg2="Margin percentage is less than standard percentage"	;
			if($zero_value==1)
			{
				?>
				<tr>
                    <td align="left"s>Gmts Wash </td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Design Cost</td>
                    <td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Studio Cost</td>
                    <td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Operating Expenses</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Deprec. & Amort.</td>
                    <td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Interest</td>
                    <td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
				</tr>
				<tr>
                    <td align="left">Income Tax </td>
                    <td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
				</tr>
				<?
				$total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")]+$row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
			}
			else
			{
				if($row[csf("wash_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left"s>Gmts Wash </td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("lab_test")]!=0)
				{
				?>
				<tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("inspection")]!=0)
				{
				?>
				<tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("cm_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("freight")]!=0)
				{
				?>
				<tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("currier_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("certificate_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("design_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Design Cost</td>
                    <td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("studio_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Studio Cost</td>
                    <td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("common_oh")]!=0)
				{
				?>
				<tr>
                    <td align="left">Operating Expenses</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("depr_amor_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Deprec. & Amort.</td>
                    <td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("interest_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Interest</td>
                    <td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
				</tr>
                <?
				}
				if($row[csf("income_tax_pre_cost")]!=0)
				{
				?>
				<tr>
                    <td align="left">Income Tax </td>
                    <td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
				</tr>
				<?
				}
				$total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")]+$row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
			}
        }
        ?>
        <tr class="rpt_bottom" style="font-weight:bold">
        <td>Total</td>
        <td align="right"><? echo number_format($total_amount,4); ?></td>
        </tr>
    </table>
    </div>
      <?
	 //End Other Components  Part report here -------------------------------------------

	 // image show here  -------------------------------------------
	$sql = "select id,master_tble_id,image_location
			from common_photo_library
			where master_tble_id='".str_replace("'","",$txt_quotation_id)."' and form_name='quotation_entry'";

	$data_array=sql_select($sql);
    ?>
    <div style="margin:15px 5px;float:left;width:500px" >
		<? foreach($data_array AS $inf){ ?>

			<img  src='<? echo $path.$inf[csf("image_location")]; ?>' height='97' width='89' />
        <? } ?>
    </div>

    <div style="clear:both"></div>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:300px;text-align:center;" rules="all">
            <label><b>CM Details</b></label>
            <tr style="font-weight:bold">
                <td width="150">CPM</td>
                <td width="50">SMV </td>
                <td width="100"> Eff%</td>
            </tr>
            <tr>
                <td><? echo $tot_cost_per_minute;?></td>
                <td><? echo $sew_smv;?></td>
                <td><? echo $sew_effi_percent;?></td>
            </tr>
        </table>
    </div>
	<?
    // Issue-id=5729
    //echo "<br /><b>Note:".$smg." ".$smg2.".</b>"; ?>
    <? echo 'Note: &nbsp;'.$remarks; ?>

	 <br/> <br/>
         <?

		 $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
		 $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		 $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_price_quotation","id=$txt_quotation_id","mst_id");

	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=10  group by  b.approved_by order by b.approved_by asc");
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=10  order by b.approved_date,b.approved_by");

 	if(count($approve_data_array)>0)
	{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){


			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
    <br/>
	<?
	$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=10 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
	//	echo  "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id";
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	if(count($unapprove_data_array)>0)
	{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '' ;?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
				<tr style="border:1px solid black;">
	                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
					<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $row[csf('un_approved_reason')]; //$unapproved_request_arr[$mst_id];?></td>
					<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              	</tr>

                <?
				$i++;
				}

			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
  		<br>
	<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:800px;text-align:center;" rules="all">
		<tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>
 	<?
	exit();
}

if($action=="generate_report" && $type=="preCostRpt2")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id=''; else $quotation_id=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref=".$txt_style_ref."";
	$txt_quotationdate=$txt_quotation_date;
	//if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";

	//echo $zero_value.'DD';
	//array for display name
	$path = str_replace("'", "", $path);
	if ($path != "") {
		$path = $path;
	} else {
		$path = "../../";
	}
	$zero_value=str_replace("'","",$zero_value);
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=1 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=20 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	//$gsm_weights=implode(",",array_unique(explode(",",$gsm_weight_top)));
	if($db_type==0)
	   {
	        if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
	        $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.remarks,a.sew_smv,a.sew_effi_percent,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,DATEDIFF(est_ship_date,op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1  and b.status_active=1 $quotation_id $company_name $cbo_buyer_name $txt_style_ref $txt_quotation_date order by a.id desc";
	  }
	//echo $txt_quotation_date;die;
	 if($db_type==2)
	   {

	       if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-',1)."'";
	        $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.sew_smv,a.remarks,a.sew_effi_percent,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,(est_ship_date-op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1 and b.status_active=1 $quotation_id $company_name $cbo_buyer_name $txt_style_ref $txt_quotation_date group by a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.remarks,a.sew_effi_percent,a.sew_smv,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg order by a.id ";
	  }
			//echo $sql;
	$data_array=sql_select($sql);


	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Quotation</div>
	<?
	foreach ($data_array as $row)
	{
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=$row[csf("price_with_commn_pcs")];
		$sew_smv=$row[csf("sew_smv")];
		$sew_effi_percent=$row[csf("sew_effi_percent")];
		$remarks=$row[csf("remarks")];
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("revised_price")];
		}
		if($avg_unit_price==0)
		{
			$avg_unit_price=$row[csf("a1st_quoted_price")];
		}


		$order_values = $row[csf("offer_qnty")]*$avg_unit_price;
		/*$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val['po_number'].", ";
			$pulich_ship_date = $val['pub_shipment_date'];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);*/
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $row[csf("id")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <?
							/*$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{

								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}*/
							if($row[csf("order_uom")]==1)
							{
							  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];
							}
							else
							{
								$gmt_item=explode(',',$row[csf("gmts_item_id")]);
								foreach($gmt_item as $key=>$val)
								{
									$grmnt_items .=$garments_item[$val].", ";
								}

							}

						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref")]; ?></b></td>
                        <td>Order UOM </td>
                        <td><b><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                        <td>Offer Qnty</td>
                        <td><b><? echo $row[csf("offer_qnty")]; ?></b></td>
                    </tr>

                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_knit_req_kg")],4); ?> (Kg)</b></td>
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_woven_req_yds")],4); ?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($avg_unit_price,4); ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo number_format($row[csf("fab_yarn_req_kg")],4) ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per_id")]]; ?></b></td>
                        <td> Target Price </td>
                        <td><b><? echo $row[csf("terget_qty")]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>GSM</td>
                        <td><b><? $gsm_weights_top=implode(",",array_unique(explode(",",$gsm_weight_top)));
						$gsm_weight_bottom=implode(",",array_unique(explode(",",$gsm_weight_bottom)));
						if($gsm_weights_top!='') $gsm_weightTop=$gsm_weights_top;else $gsm_weightTop='';
						if($gsm_weight_bottom!='' && $gsm_weights_top!='') $gsm_weightBottom=" ,".$gsm_weight_bottom;
						else if($gsm_weight_bottom!='' && $gsm_weights_top=='') $gsm_weightBottom=$gsm_weight_bottom;
						else $gsm_weightBottom='';
						echo $gsm_weightTop .$gsm_weightBottom; ?></b></td>
                        <td>Style Desc</td>
                        <td><b><? echo $row[csf("style_desc")]; ?></b></td>
                        <td>Season</td>
                        <td><b><? echo $season_name_arr[$row[csf("season_buyer_wise")]]; ?></b></td>

                    </tr>
                    <tr>
                         <td> OP Date </td>
                        <td><b><? echo change_date_format($row[csf("op_date")]); ?></b></td>
                    	<td> Est.Ship Date </td>
                        <td><b><? echo change_date_format($row[csf("est_ship_date")]); ?></b></td>
                        <td> Lead Time </td>
                        <td>
						<?

						if($row[csf("op_date")]!="" &&  $row[csf("est_ship_date")]!="")
                       		echo diff_in_weeks_and_days($row[csf("est_ship_date")], $row[csf("op_date")],'days');
						else
							echo "";
						//echo diff_in_weeks_and_days($row[csf("est_ship_date")], $row[csf("op_date")],'days');

						/*$dayes=$row[csf("date_diff")]+1;
						if($dayes >= 7)
						{
						$day=$dayes%7;
						$week=($dayes-$day)/7;
							if($week>1)
							{
								$week_string="Weeks";
							}
							else
							{
								$week_string="Week";
							}
							if($day>1)
							{
								$day_string=$dayes." Days";
							}
							else
							{
								$day_string=$dayes." Day";
							}
							if($day != 0)
							{
							echo $week." ".$week_string." ".$day." ".$day_string;
							}
							else
							{
							echo $week." ".$week_string;
							}
						}
						else
						{
						if($dayes>1)
							{
								$day_string=$dayes." Days";
							}
							else
							{
								$dayes=$dayes." Day";
							}
							echo $dayes." ".$day_string;
						}*/

						?>
                        </td>


                    </tr>
                </table>
            <?

			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$row[csf("offer_qnty")];
			//$avg_unit_price=$row[csf("confirm_price")];

	}//end first foearch
	//start	all summary report here -------------------------------------------
	if($db_type==0)
		{
		$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,design_pre_cost,design_percent,studio_pre_cost,studio_percent,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
				from wo_price_quotation_costing_mst
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
		}
	if($db_type==2)
		{
		$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
				from wo_price_quotation_costing_mst
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0  group by fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
 ";
		}
	$data_array=sql_select($sql);
	$others_cost_value=0;
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80">SL</td>
                    <td width="300">Particulars</td>
                    <td width="100">Cost</td>
                    <td width="100">Amount (USD)</td>
                    <td width="180">% to Ord. Value</td>
                </tr>
            <?
			$order_price_summ=0;
			$less_commision_summ=0;
			$total_cost_summ=0;
			$margin_summ=0;
			$margin_percent_summ=0;
			$percent=0;
			$price_dzn=0;
            $sl=0;
            foreach( $data_array as $row )
            {
				$sl=$sl+1;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];

				$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				if($commission_base_id==1) $commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				if($commission_base_id==2) $commision=$commision_rate*$order_price_per_dzn;
				if($commission_base_id==3) $commision=($commision_rate/12)*$order_price_per_dzn;

				if($zero_value==1)
				{
					?>
					<tr>
						<td><? echo $sl;?></td>
						<td align="left"><b>Order Price/<? echo $costing_val; ?></b></td>
						<td></td>
						<td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4); $order_price_summ=$row[csf("price_with_commn_dzn")];//$avg_unit_price*$order_price_per_dzn ?></b></td>
						<td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
						<td><? echo ++$sl;?></td>
						<td align="left"><b>Less Commision/<? echo $costing_val; ?></b></td>
						<td></td>
						<td align="right"><b><? echo number_format($row[csf("commission")],4);$less_commision_summ=$row[csf("commission")];//$avg_unit_price*$order_price_per_dzn ?></b></td>
						<td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left"><b>Net Quoted Price</b></td>
						<td>&nbsp;</td>
						<td align="right"><b><?  $less_commision_cost_dzn=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]); echo number_format($less_commision_cost_dzn,4); ?></b></td>
						<td align="center"><b><? echo "100.00%"; ?></b></td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">All Fabric Cost</td>
						<td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
						<td align="right" rowspan="15">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); $percent+=$row[csf("fabric_cost_percent")]; ?>%</td>
					</tr>

					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Trims Cost</td>
						<td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("trims_cost_percent")],2);  $percent+=$row[csf("trims_cost_percent")];?>%</td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Embellishment Cost</td>
						<td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); $percent+=$row[csf("embel_cost_percent")]; ?>%</td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Commercial Cost</td>
						<td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); $percent+=$row[csf("comm_cost_percent")]; ?>%</td>
					</tr>
				   <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Washing Cost (Gmt.)</td>
						<td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); $percent+=$row[csf("wash_cost_percent")]; ?>%</td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Lab Test</td>
						<td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("lab_test_percent")],2); $percent+=$row[csf("lab_test_percent")];  ?>%</td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Inspection Cost</td>
						<td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("inspection_percent")],2); $percent+=$row[csf("inspection_percent")]; ?>%</td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">CM Cost</td>
						<td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); $percent+=$row[csf("cm_cost_percent")]; ?>%</td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Freight Cost</td>
						<td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("freight_percent")],2); $percent+=$row[csf("freight_percent")]; ?>%</td>
					 </tr>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Currier Cost</td>
						<td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("currier_percent")],2); $percent+=$row[csf("currier_percent")]; ?>%</td>
					 </tr>
					  <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Certificate Cost</td>
						<td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("certificate_percent")],2); $percent+=$row[csf("certificate_percent")]; ?>%</td>
					 </tr>
					  <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Design Cost</td>
						<td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("design_percent")],2); $percent+=$row[csf("design_percent")]; ?>%</td>
					 </tr>
					  <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Studio Cost</td>
						<td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("studio_percent")],2); $percent+=$row[csf("studio_percent")]; ?>%</td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Operating Expenses </td>
						<td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("common_oh_percent")],2); $percent+=$row[csf("common_oh_percent")];?>%</td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Deprec. & Amort.</td>
						<td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("depr_amor_po_price")],2); $percent+=$row[csf("depr_amor_po_price")];?>%</td>
					 </tr>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Interest</td>
						<td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("interest_po_price")],2); $percent+=$row[csf("interest_po_price")];?>%</td>
					 </tr>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Income Tax </td>
						<td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
						<td align="center"><? echo number_format($row[csf("income_tax_po_price")],2); $percent+=$row[csf("income_tax_po_price")];?>%</td>
					 </tr>

						<td><? echo ++$sl; ?></td>
						<td align="left"><b>Total Cost (4-18)</b></td>
						<td>&nbsp;</td>
						<td align="right"><b><?  $final_cost_dzn=$row[csf("total_cost")]; echo number_format($final_cost_dzn,4); $total_cost_summ=$final_cost_dzn; ?></b></td>
						<td align="center"><b><?  echo number_format($percent,4);//echo number_format(($final_cost_dzn/$row[csf("total_cost")])*100,2);  ?>%</b></td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Margin/<? echo $costing_val; ?> </td>
						<td>&nbsp;</td>
						<td align="right"><? $margin_dzn=$less_commision_cost_dzn-$final_cost_dzn; echo number_format($margin_dzn,4); $margin_summ=$margin_dzn; ?></td>
						<td align="center"><? echo  number_format(($margin_dzn/$less_commision_cost_dzn*100),4); 	$margin_percent_summ=($margin_dzn/$less_commision_cost_dzn*100);?></td>
					</tr>
					<tr>
					<?
					$net_quoted_price=number_format($row[csf("confirm_price")],4);
					if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("revised_price")],4);
					if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("a1st_quoted_price")],4);

					$cost_pcs=number_format($final_cost_dzn/$order_price_per_dzn,4);
					if($cost_pcs>$net_quoted_price)
					{
						$bgcolor_net_quoted_price="#FF0000";
						$smg="Cost is hiegher than quoted price.";
					}
					?>
						<td><? echo ++$sl; ?></td>
						<td align="left">Net Quoted Price/ Pcs</td>
						<td>&nbsp;</td>
						<td align="right" bgcolor="<? echo $bgcolor_net_quoted_price;  ?>"><? echo number_format($net_quoted_price,4); ?></td>
						<td align="right"></td>
					</tr>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Cost /Pcs</td>
						<td>&nbsp;</td>
						<td align="right"><? $cost_per_pice=number_format($final_cost_dzn/$order_price_per_dzn,4); echo number_format($final_cost_dzn/$order_price_per_dzn,4); ?></td>
						<td align="center"></td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Margin/Pcs</td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format(($net_quoted_price-$cost_per_pice),4); ?></td>
						<td align="center"></td>
					</tr>
				<?
				}
				else
				{

					?>
					<tr>
						<td><? echo $sl;?></td>
						<td align="left"><b>Order Price/<? echo $costing_val; ?></b></td>
						<td></td>
						<td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4); $order_price_summ=$row[csf("price_with_commn_dzn")];//$avg_unit_price*$order_price_per_dzn ?></b></td>
						<td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
						<td><? echo ++$sl;?></td>
						<td align="left"><b>Less Commision/<? echo $costing_val; ?></b></td>
						<td></td>
						<td align="right"><b><? echo number_format($row[csf("commission")],4);$less_commision_summ=$row[csf("commission")];//$avg_unit_price*$order_price_per_dzn ?></b></td>
						<td align="center"><? //echo "100.00%"; ?></td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left"><b>Net Quoted Price</b></td>
						<td>&nbsp;</td>
						<td align="right"><b><?  $less_commision_cost_dzn=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]); echo number_format($less_commision_cost_dzn,4); ?></b></td>
						<td align="center"><b><? echo "100.00%"; ?></b></td>
					 </tr>
                   <?
					if($row[csf("fabric_cost")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">All Fabric Cost</td>
						<td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
						<td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); $percent+=$row[csf("fabric_cost_percent")]; ?>%</td>
					</tr>
				  	<?
					}
					if($row[csf("trims_cost")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Trims Cost</td>
						<td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("trims_cost_percent")],2);  $percent+=$row[csf("trims_cost_percent")];?>%</td>
					</tr>
                    <?
					}
					if($row[csf("embel_cost")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Embellishment Cost</td>
						<td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); $percent+=$row[csf("embel_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("comm_cost")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Commercial Cost</td>
						<td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); $percent+=$row[csf("comm_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("wash_cost")]!=0)
					{
					?>
				   <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Washing Cost (Gmt.)</td>
						<td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); $percent+=$row[csf("wash_cost_percent")]; ?>%</td>
					</tr>
                    <?
					}
					if($row[csf("lab_test")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Lab Test</td>
						<td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("lab_test_percent")],2); $percent+=$row[csf("lab_test_percent")];  ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("inspection")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Inspection Cost</td>
						<td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("inspection_percent")],2); $percent+=$row[csf("inspection_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("cm_cost")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">CM Cost</td>
						<td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); $percent+=$row[csf("cm_cost_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("freight")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Freight Cost</td>
						<td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("freight_percent")],2); $percent+=$row[csf("freight_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("currier_pre_cost")]!=0)
					{
					?>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Currier Cost</td>
						<td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("currier_percent")],2); $percent+=$row[csf("currier_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("certificate_pre_cost")]!=0)
					{
					?>
					  <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Certificate Cost</td>
						<td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("certificate_percent")],2); $percent+=$row[csf("certificate_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("design_pre_cost")]!=0)
					{
					?>
					  <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Design Cost</td>
						<td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("design_percent")],2); $percent+=$row[csf("design_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("studio_pre_cost")]!=0)
					{
					?>
					  <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Studio Cost</td>
						<td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("studio_percent")],2); $percent+=$row[csf("studio_percent")]; ?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("common_oh")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Operating Expenses </td>
						<td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("common_oh_percent")],2); $percent+=$row[csf("common_oh_percent")];?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("depr_amor_pre_cost")]!=0)
					{
					?>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Deprec. & Amort.</td>
						<td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("depr_amor_po_price")],2); $percent+=$row[csf("depr_amor_po_price")];?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("interest_pre_cost")]!=0)
					{
					?>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Interest</td>
						<td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("interest_po_price")],2); $percent+=$row[csf("interest_po_price")];?>%</td>
					 </tr>
                     <?
					}
					if($row[csf("income_tax_pre_cost")]!=0)
					{
					?>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Income Tax </td>
						<td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
                        <td align="right">&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("income_tax_po_price")],2); $percent+=$row[csf("income_tax_po_price")];?>%</td>
					 </tr>
                     <? } ?>
                     <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left"><b>Total Cost (4-<? echo $sl-1;?>)</b></td>
						<td>&nbsp;</td>
						<td align="right"><b><?  $final_cost_dzn=$row[csf("total_cost")]; echo number_format($final_cost_dzn,4); $total_cost_summ=$final_cost_dzn; ?></b></td>
						<td align="center"><b><?  echo number_format($percent,4);//echo number_format(($final_cost_dzn/$row[csf("total_cost")])*100,2);  ?>%</b></td>
					 </tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Margin/<? echo $costing_val; ?> </td>
						<td>&nbsp;</td>
						<td align="right"><? $margin_dzn=$less_commision_cost_dzn-$final_cost_dzn; echo number_format($margin_dzn,4); $margin_summ=$margin_dzn; ?></td>
						<td align="center"><? echo  number_format(($margin_dzn/$less_commision_cost_dzn*100),4); 	$margin_percent_summ=($margin_dzn/$less_commision_cost_dzn*100);?></td>
					</tr>
					<tr>
					<?
					$net_quoted_price=number_format($row[csf("confirm_price")],4);
					if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("revised_price")],4);
					if($net_quoted_price=="" || $net_quoted_price==0.0000) $net_quoted_price=number_format($row[csf("a1st_quoted_price")],4);

					$cost_pcs=number_format($final_cost_dzn/$order_price_per_dzn,4);
					if($cost_pcs>$net_quoted_price)
					{
						$bgcolor_net_quoted_price="#FF0000";
						$smg="Cost is hiegher than quoted price.";
					}
					?>
						<td><? echo ++$sl; ?></td>
						<td align="left">Net Quoted Price/ Pcs</td>
						<td>&nbsp;</td>
						<td align="right" bgcolor="<? echo $bgcolor_net_quoted_price;  ?>"><? echo number_format($net_quoted_price,4); ?></td>
						<td align="right"></td>
					</tr>
					 <tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Cost /Pcs</td>
						<td>&nbsp;</td>
						<td align="right"><? $cost_per_pice=number_format($final_cost_dzn/$order_price_per_dzn,4); echo number_format($final_cost_dzn/$order_price_per_dzn,4); ?></td>
						<td align="center"></td>
					</tr>
					<tr>
						<td><? echo ++$sl; ?></td>
						<td align="left">Margin/Pcs</td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format(($net_quoted_price-$cost_per_pice),4); ?></td>
						<td align="center"></td>
					</tr>
				<?
				}
            }
            ?>

            </table>
      </div>
      <?
	//End all summary report here -------------------------------------------



	//2	All Fabric Cost part here-------------------------------------------
	$sql = "select id, quotation_id, body_part_id, fab_nature_id, color_type_id,construction,composition, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active
			from wo_pri_quo_fabric_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";

		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")];
				$i++;
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';

				$knit_subtotal_avg_cons += $row[csf("avg_cons")];
				$knit_subtotal_amount += $row[csf("amount")];
			}

			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';

				$woven_subtotal_avg_cons += $row[csf("avg_cons")];
				$woven_subtotal_amount += $row[csf("amount")];
			}
        }


		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/'.$costing_val.'</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="6">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;

		//knit fabrics table here
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
		if($zero_value==1)
		{
  			echo $knit_fab;
		}
		else
		{
			if($knit_subtotal_avg_cons>0)
			{
				echo $knit_fab;
			}

		}

		//woven fabrics table here
		if($zero_value==1)
		{
			$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>
					';
		}
		else
		{
			 if($woven_subtotal_avg_cons>0)
				 {
				 	$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>';

				 }

				  if($knit_subtotal_amount>0 && $woven_subtotal_avg_cons<=0)
				 {
					$woven_fab .=' <tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
				}
		}





		if($zero_value==1)
		{
         echo $woven_fab;
		}
		else
		{
			 if($woven_subtotal_avg_cons>0 || $knit_subtotal_avg_cons>0)
			 { echo $woven_fab;  }
		}
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------


		//Start	Yarn Cost part report here -------------------------------------------
			if($zero_value==0) $yarn_cond="and cons_qnty>0";else $yarn_cond="";
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		 $sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 $yarn_cond group by  id,cons_ratio,count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
	if($zero_value==0)
	{
		if(count($data_array)>0)
		{
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            {
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];

			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	 	 	$grand_total_amount +=$total_amount;
		 }
	  }
	  else
	  {
	  ?>
	  	<div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            {
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];

			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
	  <?
	  	 $grand_total_amount +=$total_amount;
	  }
	//End Yarn Cost part report here -------------------------------------------



  	//start	Conversion Cost to Fabric report here -------------------------------------------


	if($zero_value==0)
	{
			$sql = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id=".$txt_quotation_id." and a.status_active=1 and a.is_deleted=0 and a.req_qnty>0";
	$data_array=sql_select($sql);
		if(count($data_array)>0)
		{
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];

				if(str_replace(",","",$item_descrition)=="")
				{
					$item_descrition="All Fabrics";
				}
			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_type")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	  	}
	  }
	  else //With Zero
	  {
	  	$sql = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id=".$txt_quotation_id." and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql);
	  ?>
	  		<div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];

				if(str_replace(",","",$item_descrition)=="")
				{
					$item_descrition="All Fabrics";
				}
			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_type")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>

	 <? }
	//End Conversion Cost to Fabric report here -------------------------------------------



  	//start	Trims Cost part report here -------------------------------------------

	if($zero_value==0)
	{
		$sql = "select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pri_quo_trim_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 and cons_dzn_gmts>0";
	$data_array=sql_select($sql);

		if(count($data_array)>0)
		{
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );

			?>
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------
	  }
	 }
	 else //With Zero
	 {
	 $sql = "select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pri_quo_trim_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 ";
	$data_array=sql_select($sql);
	 ?>
	 	<div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );

			?>
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
	 <?
	 }



	//start	Embellishment Details part report here -------------------------------------------


	if($zero_value==0)
	{
		$sql = "select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pri_quo_embe_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 and emb_name !=3 and cons_dzn_gmts>0";
	$data_array=sql_select($sql);
		if(count($data_array)>0)
		{
	?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------
	 	}
	 }
	 else //with Zero
	 {
	 	$sql = "select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pri_quo_embe_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 and emb_name !=3";
	$data_array=sql_select($sql);
	 ?>
	 	  <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
	 <?
	 }


  	//start	Commercial Cost part report here -------------------------------------------
 $zero_value.'='.$sql = "select id, quotation_id, item_id, rate, amount, status_active
			from  wo_pri_quo_comarcial_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 ";
	$data_array_commer=sql_select($sql);
	if($zero_value==0)
	{
		
		if(count($data_array_commer)>0)
		{
	?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array_commer as $row )
            {
  			?>
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------
	   }
	 }
	 else
	 {
	 ?>
	 	<div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
			
            $total_amount=0;
            foreach( $data_array_commer as $row )
            {
  			?>
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right">DD<? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
 <? }


  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array_commi=sql_select($sql);
	if($zero_value==0)
	{
		if(count($data_array_commi)>0)
		{
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array_commi as $row )
            {
			$commission_amount=0;
			if($row[csf("commission_base_id")]==1)
				{
					$commission_amount=($row[csf("commision_rate")]*$price_dzn)/100;
				}
				if($row[csf("commission_base_id")]==2)
				{
					$commission_amount=$row[csf("commision_rate")]*$order_price_per_dzn;
				}
				if($row[csf("commission_base_id")]==3)
				{
					$commission_amount=($row[csf("commision_rate")]/12)*$order_price_per_dzn;
				}
  			?>
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><?  echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commission Cost Part report here -------------------------------------------
	   }
	 }
	 else
	 {
	 ?>
	 	<div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array_commi as $row )
            {
			$commission_amount=0;
			if($row[csf("commission_base_id")]==1)
				{
					$commission_amount=($row[csf("commision_rate")]*$price_dzn)/100;
				}
				if($row[csf("commission_base_id")]==2)
				{
					$commission_amount=$row[csf("commision_rate")]*$order_price_per_dzn;
				}
				if($row[csf("commission_base_id")]==3)
				{
					$commission_amount=($row[csf("commision_rate")]/12)*$order_price_per_dzn;
				}
  			?>
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><?  echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
	<? }


	//start	Other Components part report here -------------------------------------------
	$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,wash_cost,wash_cost_percent,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent ,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,confirm_price
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id." and status_active=1 and  is_deleted=0";
	$data_array=sql_select($sql);

	?>

        <div style="margin-top:15px">
        <table>
        <tr>
        <td>
            <?
			if($zero_value==1)
			{
			?>
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
			}
			else
			{
				if(count($data_array)>0)
				{
				?>
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
				}
			}

            $total_amount=0;
            foreach( $data_array as $row )
            {
				if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")]) $smg2="Margin percentage is less than standard percentage"	;
				if($zero_value==1)
				{
					?>
					<tr>
						<td align="left"s>Gmts Wash </td>
						<td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
					</tr>
					<tr>
						<td align="left"s>Lab Test </td>
						<td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Inspection Cost</td>
						<td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
					</tr>
					<tr>
						<td align="left">CM Cost - IE</td>
						<td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Freight Cost</td>
						<td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Currier Cost</td>
						<td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
					</tr>
					 <tr>
						<td align="left">Certificate Cost</td>
						<td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
					</tr>
					 <tr>
						<td align="left">Design Cost</td>
						<td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
					</tr>
					 <tr>
						<td align="left">Studio Cost</td>
						<td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Office OH</td>
						<td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Deprec. & Amort.</td>
						<td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Interest</td>
						<td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
					</tr>
					<tr>
						<td align="left">Income Tax</td>
						<td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
					</tr>
					<?
					 $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")]+$row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
				}
				else
				{
					if($row[csf("wash_cost")]!=0)
					{
					?>
					<tr>
						<td align="left"s>Gmts Wash </td>
						<td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("lab_test")]!=0)
					{
					?>
					<tr>
						<td align="left"s>Lab Test </td>
						<td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("inspection")]!=0)
					{
					?>
					<tr>
						<td align="left">Inspection Cost</td>
						<td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("cm_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">CM Cost - IE</td>
						<td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("freight")]!=0)
					{
					?>
					<tr>
						<td align="left">Freight Cost</td>
						<td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("currier_pre_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">Currier Cost</td>
						<td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("certificate_pre_cost")]!=0)
					{
					?>
					 <tr>
						<td align="left">Certificate Cost</td>
						<td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("design_pre_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">Design Cost</td>
						<td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("studio_pre_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">Studio Cost</td>
						<td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("common_oh")]!=0)
					{
					?>
					<tr>
						<td align="left">Office OH</td>
						<td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("depr_amor_pre_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">Deprec. & Amort.</td>
						<td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("interest_pre_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">Interest</td>
						<td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
					</tr>
                    <?
					}
                    if($row[csf("income_tax_pre_cost")]!=0)
					{
					?>
					<tr>
						<td align="left">Income Tax</td>
						<td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
					</tr>
                    <?
					}

					 $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")]+$row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
				}
            }

			if($zero_value==1)
			{
			?>

                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
			<?
			}
			else
			{
				if($total_amount>0)
				{
			?>
				  <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
			<?
			  }
			}
			?>

            </td>
            <td rowspan="2">
            <?
             // image show here  -------------------------------------------
			  if($db_type==0)
				 {
					$sql_img = "select id,master_tble_id,image_location
					from common_photo_library
					where master_tble_id='".str_replace("'","",$txt_quotation_id)."' and form_name='quotation_entry' limit 1";
				 }
			  if($db_type==2)
				 {
			$sql_img = "select id,master_tble_id,image_location
					from common_photo_library
					where master_tble_id='".str_replace("'","",$txt_quotation_id)."'  and form_name='quotation_entry' ";
				 }

		$data_array_img=sql_select($sql_img);
 	  ?>
          <div style="margin:15px 5px; margin-top:-135px;float:left;width:500px">
          	<? foreach($data_array_img AS $inf_img){ ?>
            <img  src='<? echo $path.$inf_img[csf("image_location")]; ?>' height='97' width='89' />
            <?  } ?>
          </div>
            </td>
            </tr>
            <tr>
            <td>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
                <label><b>Price Summary :Quotation Id:<? echo trim($txt_quotation_id,"'");?></b></label>
                    <tr style="font-weight:bold">
                        <td width="150">Particulars</td>
                        <td width="100">Amount (USD)</td>
                     </tr>
                <?

				if($zero_value==1)
				{
					 $total_amount=0;
                foreach( $data_array as $row )
                {
                    if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
                    {
                    $smg2="Margin percentage is less than standard percentage"	;
                    }
                ?>
                <tr>
                        <td align="left"s>Price <? echo $costing_val; ?> </td>
                        <td align="right"><? echo number_format($order_price_summ,4); ?></td>
                    </tr>

                    <tr>
                        <td align="left"s>Less Commision/ <? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($less_commision_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Net Quoted Price/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($order_price_summ-$less_commision_summ,4); ?></td>
                    </tr>

                    <tr>
                        <td align="left">Total cost/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format( $total_cost_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Margin/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($margin_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Margin %</td>
                        <td align="right"><? echo number_format($margin_percent_summ,4); ?></td>
                    </tr>
                <?
                     $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
					 }
                  }
					else
					{
						 $total_amount=0;
						foreach( $data_array as $row )
						{
							if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
							{
								$smg2="Margin percentage is less than standard percentage"	;
							}
					?>
						 <tr>
                        <td align="left"s>Price <? echo $costing_val; ?> </td>
                        <td align="right"><? echo number_format($order_price_summ,4); ?></td>
                    </tr>
    				<?
					if($less_commision_summ>0)
					{
					?>
                    <tr>
                        <td align="left"s>Less Commision/ <? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($less_commision_summ,4); ?></td>
                    </tr>
					<?
					}
					?>
                    <tr>
                        <td align="left">Net Quoted Price/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($order_price_summ-$less_commision_summ,4); ?></td>
                    </tr>
                    <?
					if($total_cost_summ>0)
					{
					?>
                    <tr>
                        <td align="left">Total cost/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format( $total_cost_summ,4); ?></td>
                    </tr>
					<?
					}
					if($margin_summ>0)
					{
					?>
                    <tr>
                        <td align="left">Margin/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($margin_summ,4); ?></td>
                    </tr>
					<?
					}
					if($margin_percent_summ>0)
					{
					?>
                    <tr>
                        <td align="left">Margin %</td>
                        <td align="right"><? echo number_format($margin_percent_summ,4); ?></td>
                    </tr>
					<?
						 $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
						 }
					}
					}
                ?>
                </table>
                </td>

                </tr>
            </table>
			<br/>
			<div style="clear:both"></div>
			<?
		$first_day_this_month = date('01-m-Y',strtotime($txt_quotationdate));
		$last_date  = date("t-m-Y", strtotime($txt_quotationdate));
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$first_day_this_month),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$last_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$first_day_this_month),"","",1);
				$end_date=change_date_format(str_replace("'","",$last_date),"","",1);
			}
			$date_quote_cond=" and applying_period_to_date between '$start_date' and '$end_date'";

	$cpm_cpm_sql="select id,company_id,cost_per_minute from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $date_quote_cond order by id desc";
	$cpm_cpm=sql_select($cpm_cpm_sql);
			foreach($cpm_cpm as $row)
			{
				$tot_cost_per_minute=$row[csf('cost_per_minute')];
			} //var_dump($asking_profit_arr);
			//echo $tot_cost_per_minute;
			?>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:300px;text-align:center;" rules="all">
            <label><b>CM Details</b></label>
            <tr style="font-weight:bold">
                <td width="150">CPM</td>
                <td width="50">SMV </td>
                <td width="100"> Eff%</td>
            </tr>
            <tr>
                <td><? echo $tot_cost_per_minute;?></td>
                <td><? echo $sew_smv;?></td>
                <td><? echo $sew_effi_percent;?></td>
            </tr>
        </table>
    </div>
	<?
    // Issue-id=5729
    //echo "<br /><b>Note:".$smg." ".$smg2.".</b>"; ?>
    <? if($remarks!="") echo '<b>Remarks:</b>&nbsp;'.$remarks;else echo ""; ?>
      </div>
      <div style="clear:both"></div>
      </div>
      <? echo "<br /><b>Note:".$smg." ".$smg2.".</b>"; ?>
    <!--End CM on Net Order Value Part report here ------------------------------------------->
	<? //echo "<br /><b>Note: Other Cost =  Fabric Cost + Trims Cost + Embellishment Cost + Lab Test + Inspection + Office OH</b><br /><br />"; ?>

	<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:800px;text-align:center;" rules="all">
		<tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>
 	<?
	exit();
}//end master if condition-------------------------------------------------------

if($action=="generate_report" && $type=="preCostRpt3")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id=''; else $quotation_id=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref=".$txt_style_ref."";
	//if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";


	//array for display name
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=1 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=20 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	if($db_type==0)
	   {
	        if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
	        $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,DATEDIFF(est_ship_date,op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1 and b.status_active=1 $quotation_id $company_name $cbo_buyer_name $txt_style_ref $txt_quotation_date order by a.id";
	  }
	//echo $txt_quotation_date;die;
	 if($db_type==2)
	   {

	       if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-',1)."'";
	        $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,(est_ship_date-op_date) as date_diff,MAX(b.id) as bid,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg
			from wo_price_quotation a, wo_price_quotation_costing_mst b, wo_pri_quo_sum_dtls c
			where a.id=b.quotation_id  and b.quotation_id =c.quotation_id  and a.status_active=1 and b.status_active=1 $quotation_id $company_name $cbo_buyer_name $txt_style_ref $txt_quotation_date group by a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.est_ship_date,a.op_date,b.costing_per_id,b.a1st_quoted_price,b.revised_price,b.confirm_price,b.price_with_commn_pcs,b.terget_qty,c.fab_knit_req_kg,c.fab_woven_req_yds,c.fab_yarn_req_kg order by a.id ";
	  }
			//echo $sql;
	$data_array=sql_select($sql);


	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Quotation</div>
	<?
	$order_price_per_dzn=0;
    $order_job_qnty=0;
	foreach ($data_array as $row)
	{

		$avg_unit_price=$row[csf("price_with_commn_pcs")];
		if($avg_unit_price==0) $avg_unit_price=$row[csf("revised_price")];
		if($avg_unit_price==0) $avg_unit_price=$row[csf("a1st_quoted_price")];

		$order_values = $row[csf("offer_qnty")]*$avg_unit_price;
		/*$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val['po_number'].", ";
			$pulich_ship_date = $val['pub_shipment_date'];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);*/
		?>
            	<table border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $row[csf("id")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <?
							/*$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{

								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}*/
							if($row[csf("order_uom")]==1)
							{
							  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];
							}
							else
							{
								$gmt_item=explode(',',$row[csf("gmts_item_id")]);
								foreach($gmt_item as $key=>$val)
								{
									$grmnt_items .=$garments_item[$val].", ";
								}

							}

						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref")]; ?></b></td>
                        <td>Order UOM </td>
                        <td><b><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                        <td>Offer Qnty</td>
                        <td><b><? echo $row[csf("offer_qnty")]; ?></b></td>
                    </tr>

                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_knit_req_kg")],4); ?> (Kg)</b></td>
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo number_format($row[csf("fab_woven_req_yds")],4); ?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($avg_unit_price,4); ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo number_format($row[csf("fab_yarn_req_kg")],4) ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per_id")]]; ?></b></td>
                        <td> Target Price </td>
                        <td><b><? echo $row[csf("terget_qty")]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>GSM</td>
                        <td><b><? $gsm_weights_top=implode(",",array_unique(explode(",",$gsm_weight_top)));
						$gsm_weight_bottom=implode(",",array_unique(explode(",",$gsm_weight_bottom)));
						if($gsm_weights_top!='') $gsm_weightTop=$gsm_weights_top;else $gsm_weightTop='';
						if($gsm_weight_bottom!='' && $gsm_weights_top!='') $gsm_weightBottom=" ,".$gsm_weight_bottom;
						else if($gsm_weight_bottom!='' && $gsm_weights_top=='') $gsm_weightBottom=$gsm_weight_bottom;
						else $gsm_weightBottom='';
						echo $gsm_weightTop . $gsm_weightBottom; ?></b></td>
                        <td>Style Desc</td>
                        <td><b><? echo $row[csf("style_desc")]; ?></b></td>
                        <td>Season</td>
                        <td><b><? echo $season_name_arr[$row[csf("season_buyer_wise")]]; ?></b></td>

                    </tr>
                    <tr>
                         <td> OP Date </td>
                        <td><b><? echo change_date_format($row[csf("op_date")]); ?></b></td>
                    	<td> Est.Ship Date </td>
                        <td><b><? echo change_date_format($row[csf("est_ship_date")]); ?></b></td>
                        <td> Lead Time </td>
                        <td>
						<?

						if($row[csf("op_date")]!="" &&  $row[csf("est_ship_date")]!="")
                       		echo diff_in_weeks_and_days($row[csf("est_ship_date")], $row[csf("op_date")],'days');
						else
							echo "";
						//echo diff_in_weeks_and_days($row[csf("est_ship_date")], $row[csf("op_date")],'days');

						/*$dayes=$row[csf("date_diff")]+1;
						if($dayes >= 7)
						{
						$day=$dayes%7;
						$week=($dayes-$day)/7;
							if($week>1)
							{
								$week_string="Weeks";
							}
							else
							{
								$week_string="Week";
							}
							if($day>1)
							{
								$day_string=$dayes." Days";
							}
							else
							{
								$day_string=$dayes." Day";
							}
							if($day != 0)
							{
							echo $week." ".$week_string." ".$day." ".$day_string;
							}
							else
							{
							echo $week." ".$week_string;
							}
						}
						else
						{
						if($dayes>1)
							{
								$day_string=$dayes." Days";
							}
							else
							{
								$dayes=$dayes." Days";
							}
							echo $dayes." ".$day_string;
						}*/

						?>
                        </td>


                    </tr>
                </table>
            <?

			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$row[csf("offer_qnty")];
			//$avg_unit_price=$row[csf("confirm_price")];

	}//end first foearch
	//start	all summary report here -------------------------------------------


	$yarn_amount_dzn=0;
	$sql_yarn = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 group by  id,cons_ratio,count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array_yarn=sql_select($sql_yarn);
		foreach($data_array_yarn as $data_array_yarn_row)
		{
		 $yarn_amount_dzn+=	$data_array_yarn_row[csf('amount')];
		}

		$conversion_cost_arr=array();
		$conversion_cost_dzn=0;
		$sql_conversion = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id=".$txt_quotation_id." and a.status_active=1 and a.is_deleted=0";
	$data_array_conversion=sql_select($sql_conversion);
	foreach($data_array_conversion as $data_array_conversion_row)
	{
	  $conversion_cost_dzn+=$data_array_conversion_row[csf('amount')];

	  $conversion_cost_arr[$data_array_conversion_row[csf('cons_type')]]+=$data_array_conversion_row[csf('amount')];
	}

	if($db_type==0)
		{
		$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
				from wo_price_quotation_costing_mst
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
		}
	if($db_type==2)
		{
		$sql = "select MAX(id),fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
				from wo_price_quotation_costing_mst
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0  group by fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn
 ";
		}
	$data_array=sql_select($sql);
	$others_cost_value=0;
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80">Line Items</td>
                    <td width="300">Particulars</td>
                    <td width="100">Amount (USD)/ DZN</td>
                    <td width="100">Total Value( Offer Qnty)</td>
                    <td width="180">%</td>
                </tr>
            <?
			$order_price_summ=0;
			$less_commision_summ=0;
			$total_cost_summ=0;
			$margin_summ=0;
			$margin_percent_summ=0;
			$percent=0;
			$price_dzn=0;
            $sl=0;
            foreach( $data_array as $row )
            {
				$sl=$sl+1;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];

				$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id=$txt_quotation_id");
				if($commission_base_id==1)
				{
					$commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				}
				if($commission_base_id==2)
				{
					$commision=$commision_rate*$order_price_per_dzn;
				}
				if($commission_base_id==3)
				{
					$commision=($commision_rate/12)*$order_price_per_dzn;
				}
				$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
				$other_direct_expense=number_format($row[csf("wash_cost")],4)+number_format($row[csf("lab_test")],4)+number_format($row[csf("inspection")],4)+number_format($row[csf("freight")],4)+number_format($row[csf("currier_pre_cost")],4)+number_format($row[csf("certificate_pre_cost")],4)+number_format($row[csf("design_pre_cost")],4)+number_format($row[csf("studio_pre_cost")],4);
				$LessCostOfMaterialServices =number_format($yarn_amount_dzn,4)+number_format($conversion_cost_dzn,4)+number_format($row[csf("trims_cost")],4)+number_format($row[csf("embel_cost")],4)+number_format($other_direct_expense,4);
				$Contribution_Margin=$NetFOBValue-$LessCostOfMaterialServices;
				$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
				$OperatingProfitLoss=$Gross_Profit-($row[csf("comm_cost")]+$row[csf("common_oh")]);
				$Netprofit=$OperatingProfitLoss-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);



?>
                <tr>
                    <td><? echo $sl;?></td>
                    <td align="left"><b>Gross FOB Value/<? echo $costing_val; ?></b></td>
                    <td align="right"><b><? echo number_format($row[csf("price_with_commn_dzn")],4); $order_price_summ=$row[csf("price_with_commn_dzn")];?></b></td>
                    <td align="right"><? echo number_format(($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><? echo "100.0000"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl;?></td>
                    <td align="left" style="padding-left:15px">Less: Commision/<? echo $costing_val; ?></td>
                    <td align="right"><b><? echo number_format($row[csf("commission")],4);$less_commision_summ=$row[csf("commission")];?></b></td>
                    <td align="right"><? echo number_format(($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><? echo number_format(($row[csf("commission")]/$row[csf("price_with_commn_dzn")])*100,4)?></td>
                </tr>



                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Net FOB Value (1-2)</b></td>
                    <td align="right"><b><? echo number_format($NetFOBValue,4); ?></b></td>
                    <td align="right"><? echo number_format(($NetFOBValue/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><b><? echo number_format(($NetFOBValue/$row[csf("price_with_commn_dzn")])*100,4)?></b></td>
                 </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Less: Cost of Material & Services (5+6+7+8+9) </b></td>
                    <td align="right"><b><?   echo number_format($LessCostOfMaterialServices,4); ?></b></td>
                    <td align="right"><? echo number_format(($LessCostOfMaterialServices/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><b><? echo number_format(($LessCostOfMaterialServices/$row[csf("price_with_commn_dzn")])*100,4)?></b></td>
                 </tr>

                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Yarn Cost</strong></td>
                    <td align="right"><? echo number_format($yarn_amount_dzn,4); ?></td>
                    <td align="right"><? echo number_format(($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><? echo number_format(($yarn_amount_dzn/$row[csf("price_with_commn_dzn")])*100,4)?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px">
                    <strong>Conversion Cost</strong>
                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="180"> <? echo $conversion_cost_head_array[$key]; ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                    <td align="right">
					<? echo number_format($conversion_cost_dzn,4); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"> <? echo number_format($value,4); ?></td></tr>
                    <? }?>
                    </table>

                    </td>
                    <td align="right">
                    <? echo number_format(($conversion_cost_dzn/$order_price_per_dzn)*$order_job_qnty,4); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"> <? echo number_format(($value/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                    <td align="right">
					<? echo number_format(($conversion_cost_dzn/$row[csf("price_with_commn_dzn")])*100,4)?>
                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"> <? echo number_format(($value/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Trims Cost</strong></td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="right"><? echo number_format(($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><? echo number_format($row[csf("trims_cost_percent")],4);?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Embellishment Cost</strong></td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="right"><? echo number_format(($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td align="right"><? echo number_format($row[csf("embel_cost_percent")],4);?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px">
                    <strong>Other Direct Expenses</strong>

                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="180"> Washing Cost (Gmt.)</td></tr>
                    <tr><td width="180"> Lab Test</td></tr>
                    <tr><td width="180"> Inspection Cost</td></tr>
                    <tr><td width="180"> Freight Cost</td></tr>
                    <tr><td width="180"> Currier Cost</td></tr>
                    <tr><td width="180"> Certificate Cost</td></tr>
                    <tr><td width="180"> Design Cost</td></tr>
                    <tr><td width="180"> Studio Cost</td></tr>
                    </table>

                    </td>
                    <td align="right">
					<?
					echo  number_format($other_direct_expense,4);
					?>

                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="98" align="right"> <? echo number_format($row[csf("wash_cost")],4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($row[csf("lab_test")],4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($row[csf("inspection")],4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($row[csf("freight")],4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($row[csf("currier_pre_cost")],4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($row[csf("certificate_pre_cost")],4); ?></td></tr>
                     <tr><td width="98" align="right"> <? echo number_format($row[csf("design_pre_cost")],4); ?></td></tr>
                     <tr><td width="98" align="right"> <? echo number_format($row[csf("studio_pre_cost")],4); ?></td></tr>
                    </table>

                    </td>
                    <td align="right">
                   <? echo number_format(($other_direct_expense/$order_price_per_dzn)*$order_job_qnty,4); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                     <tr><td width="98" align="right"> <? echo number_format(($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                      <tr><td width="98" align="right"> <? echo number_format(($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td></tr>
                    </table>
                    </td>
                    <td align="right">
					<? echo number_format(($other_direct_expense/$row[csf("price_with_commn_dzn")])*100,4); ?>
                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("wash_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("lab_test")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("inspection")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("freight")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("currier_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("certificate_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("design_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($row[csf("studio_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td></tr>
                    </table>
                    </td>
                </tr>
                <tr>
                    <td width="80">10</td>
                    <td width="380" align="left" style="font-weight:bold">Contributions/Value Additions (3-4)</td>
                    <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Contribution_Margin,4); ?></td>
                    <td width="100" align="right" style="font-weight:bold"><? echo number_format(($Contribution_Margin/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Contribution_Margin/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
               </tr>
                <tr>
                    <td width="80">11</td>
                    <td width="380" align="left" style=" padding-left:15px">Less: CM Cost </td>
                    <td width="100" align="right"><? echo number_format($row[csf("cm_cost")],4); ?> </td>
                    <td width="100" align="right"><? echo number_format(($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td width="180" align="right"><? echo number_format(($row[csf("cm_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
              </tr>
               <tr>
                    <td width="80">12</td>
                    <td width="380" align="left" style="font-weight:bold">Gross Profit (10-11)</td>
                    <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Gross_Profit,4); ?></td>
                    <td width="100" align="right" style="font-weight:bold"><? echo number_format(($Gross_Profit/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Gross_Profit/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
                </tr>

                <tr>
                    <td width="80">13</td>
                    <td width="380" align="left" style=" padding-left:15px">Less: Commercial Cost</td>

                    <td width="100" align="right"> <? echo number_format( $row[csf("comm_cost")],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td width="180" align="right"><? echo number_format(($row[csf("comm_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
                </tr>
                <tr>
                    <td width="80">14</td>
                    <td width="380" align="left" style=" padding-left:15px">Less: Operating Expensees</td>

                    <td width="100" align="right"><? echo number_format( $row[csf("common_oh")],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td width="180" align="right"><? echo number_format(($row[csf("common_oh")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
                </tr>

                  <tr>
                        <td width="80">15</td>
                        <td width="380" align="left" style="font-weight:bold">Operating Profit/ Loss (12-(13+14))</td>
                        <td width="100" align="right" style="font-weight:bold"> <? echo number_format($OperatingProfitLoss,4); ?></td>
                        <td width="100" align="right" style="font-weight:bold"><? echo number_format(($OperatingProfitLoss/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                        <td width="180" align="right" style="font-weight:bold"><? echo number_format(($OperatingProfitLoss/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
                </tr>
                <tr>
                    <td width="80">16</td>
                    <td width="380" align="left" style=" padding-left:15px">Less: Depreciation & Amortization </td>
                    <td width="100" align="right"> <? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
                    <td width="100" align="right"><? echo number_format(($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                    <td width="180" align="right"><? echo number_format(($row[csf("depr_amor_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
                </tr>

               <tr>

                        <td width="80">17</td>
                        <td width="380" align="left" style=" padding-left:15px">Less: Interest </td>

                        <td width="100" align="right"> <? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
                        <td width="100" align="right"><? echo number_format(($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                        <td width="180" align="right"><? echo number_format(($row[csf("interest_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
               </tr>
               <tr>
                        <td width="80">18</td>
                        <td width="380" align="left" style=" padding-left:15px">Less: Income Tax</td>

                        <td width="100" align="right"> <? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
                        <td width="100" align="right"><? echo number_format(($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                        <td width="180" align="right"><? echo number_format(($row[csf("income_tax_pre_cost")]/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
                </tr>
                <tr>
				<?
                //$Netprofit_job=$OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);
                ?>
                <td width="80">19</td>
                <td width="380" align="left" style="font-weight:bold">Net Profit (15-(16+17+18))</td>

                <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit,4); ?> </td>
                <td width="100" align="right" style="font-weight:bold"><? echo number_format(($Netprofit/$order_price_per_dzn)*$order_job_qnty,4); ?></td>
                <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Netprofit/$row[csf("price_with_commn_dzn")])*100,4); ?></td>
               </tr>
            <?

            }
            ?>

            </table>
      </div>
      <?
	//End all summary report here -------------------------------------------



	//2	All Fabric Cost part here-------------------------------------------
	$sql = "select id, quotation_id, body_part_id, fab_nature_id, color_type_id,construction,composition, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active
			from wo_pri_quo_fabric_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";

		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")];
				$i++;
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';

				$knit_subtotal_avg_cons += $row[csf("avg_cons")];
				$knit_subtotal_amount += $row[csf("amount")];
			}

			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("construction")].", ".$row[csf("composition")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';

				$woven_subtotal_avg_cons += $row[csf("avg_cons")];
				$woven_subtotal_amount += $row[csf("amount")];
			}
        }

		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/'.$costing_val.'</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="6">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;

		//knit fabrics table here
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;

		//woven fabrics table here
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
         echo $woven_fab;
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------


		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls
				where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 group by  id,cons_ratio,count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            {
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];

			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	//End Yarn Cost part report here -------------------------------------------



  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id=".$txt_quotation_id." and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];

				if(str_replace(",","",$item_descrition)=="")
				{
					$item_descrition="All Fabrics";
				}
			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_type")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------



  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, trim_group, description, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, status_active from wo_pri_quo_trim_cost_dtls where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name"  );
	?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				//$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );

			?>
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------



	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pri_quo_embe_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 and emb_name !=3";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_val; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------


  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, quotation_id, item_id, rate, amount, status_active
			from  wo_pri_quo_comarcial_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);

	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
  			?>
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------


  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls
			where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
			$commission_amount=0;
			if($row[csf("commission_base_id")]==1)
				{
					$commission_amount=($row[csf("commision_rate")]*$price_dzn)/100;
				}
				if($row[csf("commission_base_id")]==2)
				{
					$commission_amount=$row[csf("commision_rate")]*$order_price_per_dzn;
				}
				if($row[csf("commission_base_id")]==3)
				{
					$commission_amount=($row[csf("commision_rate")]/12)*$order_price_per_dzn;
				}
  			?>
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><?  echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commission Cost Part report here -------------------------------------------


	//start	Other Components part report here -------------------------------------------
	$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,wash_cost,wash_cost_percent,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent ,design_pre_cost,design_percent,studio_pre_cost,studio_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,confirm_price
			from wo_price_quotation_costing_mst
			where quotation_id=".$txt_quotation_id." and status_active=1 and  is_deleted=0";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
        <table>
        <tr>
        <td>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            {
				if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
				{
				$smg2="Margin percentage is less than standard percentage"	;
				}
  			?>
            <tr>
                    <td align="left"s>Gmts Wash </td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                </tr>

                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Design Cost</td>
                    <td align="right"><? echo number_format($row[csf("design_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Studio Cost</td>
                    <td align="right"><? echo number_format($row[csf("studio_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>

                <tr>
                    <td align="left">Deprec. & Amort.</td>
                    <td align="right"><? echo number_format($row[csf("depr_amor_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Interest</td>
                    <td align="right"><? echo number_format($row[csf("interest_pre_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Income Tax</td>
                    <td align="right"><? echo number_format($row[csf("income_tax_pre_cost")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("common_oh")]+$row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
            </table>
            </td>
            <td rowspan="2">
            <?
             // image show here  -------------------------------------------
			  if($db_type==0)
				 {
					$sql_img = "select id,master_tble_id,image_location
					from common_photo_library
					where master_tble_id='".str_replace("'","",$txt_quotation_id)."' and form_name='quotation_entry' limit 1";
				 }
			  if($db_type==2)
				 {
			$sql_img = "select id,master_tble_id,image_location
					from common_photo_library
					where master_tble_id='".str_replace("'","",$txt_quotation_id)."' and form_name='quotation_entry' ";
				 }

		$data_array_img=sql_select($sql_img);
 	  ?>
          <div style="margin:15px 5px;float:left;width:500px">
          	<? foreach($data_array_img AS $inf_img){ ?>
                <img  src='../../<? echo $inf_img[csf("image_location")]; ?>' height='400' width='300'/>
            <?  } ?>
          </div>
            </td>
            </tr>
            <!--<tr>
            <td>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:250px;text-align:center;" rules="all">
                <label><b>Price Summary :Quotation Id:<? echo trim($txt_quotation_id,"'");?></b></label>
                    <tr style="font-weight:bold">
                        <td width="150">Particulars</td>
                        <td width="100">Amount (USD)</td>
                     </tr>
                <?
                $total_amount=0;
                foreach( $data_array as $row )
                {
                    if($row[csf("margin_dzn_percent")]<$row[csf("final_cost_dzn_percent")])
                    {
                    $smg2="Margin percentage is less than standard percentage"	;
                    }
                ?>
                <tr>
                        <td align="left"s>Price <? echo $costing_val; ?> </td>
                        <td align="right"><? echo number_format($order_price_summ,4); ?></td>
                    </tr>

                    <tr>
                        <td align="left"s>Less Commision/ <? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($less_commision_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Net Quoted Price/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($order_price_summ-$less_commision_summ,4); ?></td>
                    </tr>

                    <tr>
                        <td align="left">Total cost/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format( $total_cost_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Margin/<? echo $costing_val; ?></td>
                        <td align="right"><? echo number_format($margin_summ,4); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Margin %</td>
                        <td align="right"><? echo number_format($margin_percent_summ,4); ?></td>
                    </tr>
                <?
                     $total_amount += $row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
                }
                ?>
                </table>
                </td>

                </tr>-->
            </table>
      </div>

      <div style="clear:both"></div>
      </div>
      <? echo "<br /><b>Note:".$smg." ".$smg2.".</b>"; ?>
    <!--End CM on Net Order Value Part report here ------------------------------------------->
	<? //echo "<br /><b>Note: Other Cost =  Fabric Cost + Trims Cost + Embellishment Cost + Lab Test + Inspection + Office OH</b><br /><br />"; ?>

	<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:800px;text-align:center;" rules="all">
		<tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>
 	<?
	exit();
}

if($action=="generate_report" && $type=="preCostRpt4")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $qoutation_id_cond=''; else $qoutation_id_cond=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name_cond=''; else $company_name_cond=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref_cond=''; else $txt_style_ref_cond=" and a.style_ref=".$txt_style_ref."";
	//if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";


	//array for display name
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	  $sql="select a.id  as quotation_id,a.gmts_item_id,a.company_id, a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id from wo_price_quotation a,wo_price_quotation_costing_mst b  where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_name_cond $txt_style_ref_cond  $qoutation_id_cond order  by a.id ";
			$sql_quot_result=sql_select($sql);
			$all_quot_id="";
			foreach($sql_quot_result as $row)
			{
				if($all_quot_id=="") $all_quot_id=$row[csf("quotation_id")]; else $all_quot_id.=",".$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref")]]['costing_per']=$row[csf("costing_per")];

				$style_wise_arr[$row[csf("style_ref")]]['gmts_item_id']=$row[csf("gmts_item_id")];
				$style_wise_arr[$row[csf("style_ref")]]['shipment_date'].=$row[csf('est_ship_date')].',';
				$style_wise_arr[$row[csf("style_ref")]]['quotation_id']=$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref")]]['buyer_name']=$row[csf("buyer_id")];
				$offer_qnty_pcs=$row[csf('offer_qnty')]*$row[csf('ratio')];
				$style_wise_arr[$row[csf("style_ref")]]['qty_pcs']+=$row[csf('offer_qnty')]*$row[csf('ratio')];
				$style_wise_arr[$row[csf("style_ref")]]['qty']+=$row[csf('offer_qnty')];
				$style_wise_arr[$row[csf("style_ref")]]['final_cost_pcs']=$row[csf('price_with_commn_pcs')];
				$style_wise_arr[$row[csf("style_ref")]]['total_cost']+=$offer_qnty_pcs*$row[csf('price_with_commn_pcs')];
				$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty']=$row[csf("offer_qnty")];
				$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id']=$row[csf("costing_per_id")];
				$quot_wise_arr[$row[csf("quotation_id")]]['quot_date']=$row[csf("quot_date")];
				//$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')];
			}

			$company_con="";
			if($cbo_company_name!=0) $company_con="and company_id=$cbo_company_name";
			//else if($cbo_style_owner!=0) $company_con="and company_id=$cbo_style_owner";
			//else if($cbo_company_name==0) $company_con=$cbo_company_name;
			$financial_para=array();
			$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date,applying_period_to_date,operating_expn from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 $company_con order by id");
			foreach($sql_std_para as $sql_std_row)
			{
				$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
				$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
				$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];

				$applying_period_date=change_date_format($sql_std_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($sql_std_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);

					$financial_para_arr[$newdate]['operating_expn']=$sql_std_row[csf('operating_expn')];
					if($sql_std_row[csf("income_tax")]>0)
					{
						$financial_para_arr[$newdate]['income_tax']=$sql_std_row[csf('income_tax')];
					}
					if($sql_std_row[csf("interest_expense")]>0)
					{
						$financial_para_arr[$newdate]['interest_expense']=$sql_std_row[csf('interest_expense')];
					}

					//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			//print_r($financial_para_arr);
				$all_quot_ids=array_unique(explode(",",$all_quot_id));


			$sql_fab = "select quotation_id,sum(avg_cons) as cons_qnty, sum(amount) as amount
			from wo_pri_quo_fabric_cost_dtls
			where quotation_id in(".$all_quot_id.") and status_active=1 and is_deleted=0 and fabric_source=2 group by  quotation_id";
			$data_array_fab=sql_select($sql_fab);
			foreach($data_array_fab as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$fab_order_price_per_dzn=12;}
				else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
				else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
				else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
				else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

				$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				 $fab_summary_data[$row[csf("quotation_id")]]['fab_amount_dzn']+=$row[csf("amount")];
				 $fab_summary_data[$row[csf("quotation_id")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
				//$yarn_amount_dzn+=$row[csf('amount')];
			}
			$sql_yarn = "select quotation_id,sum(cons_qnty) as cons_qnty, sum(amount) as amount
			from wo_pri_quo_fab_yarn_cost_dtls
			where quotation_id in(".$all_quot_id.") and status_active=1 group by  quotation_id";
			$data_array_yarn=sql_select($sql_yarn);
			foreach($data_array_yarn as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
				else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
				else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
				else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
				else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
				$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				 $yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn']+=$row[csf("amount")];
				// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
				 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
			}

			$conversion_cost_arr=array();
			$sql_conversion = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id in(".$all_quot_id.") and a.status_active=1  ";
			$data_array_conversion=sql_select($sql_conversion);
			foreach($data_array_conversion as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$conv_order_price_per_dzn=12;}
				else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
				else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
				else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
				else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
				$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$summary_data['conversion_cost_dzn']+=$row[csf("amount")];
				//$summary_data['conversion_cost_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
				 $conv_summary_data[$row[csf("quotation_id")]]['conv_amount_dzn']+=$row[csf("amount")];

				$conversion_cost_arr[$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
				$conversion_cost_arr[$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
			}

			if($db_type==0)
			{
			$sql = "select MAX(id),quotation_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent,offer_qnty
					from wo_price_quotation_costing_mst
					where quotation_id in(".$all_quot_id.") and status_active=1 ";
			}
			if($db_type==2)
			{
			$sql = "select MAX(id),fabric_cost,quotation_id,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent
					from wo_price_quotation_costing_mst
					where quotation_id in(".$all_quot_id.") and status_active=1   group by fabric_cost,quotation_id,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent";
			}
			//echo $sql;
			$data_array=sql_select($sql);
			$order_price_summ=0;
			$less_commision_summ=0;
			$total_cost_summ=0;
			$margin_summ=0;
			$margin_percent_summ=0;
			$percent=0;
			$price_dzn=0;
            $sl=1;
            foreach( $data_array as $row )
            {
				//$sl=$sl+1;
				if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
				else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				/*$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id in($all_quot_id)");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id in($all_quot_id)");
				if($commission_base_id==1)
				{
					$commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				}
				if($commission_base_id==2)
				{
					$commision=$commision_rate*$order_price_per_dzn;
				}
				if($commission_base_id==3)
				{
					$commision=($commision_rate/12)*$order_price_per_dzn;
				}*/
				//echo $row[csf("price_with_commn_dzn")].'='.$order_price_per_dzn.'='.$order_job_qnty.'<br>';
				$summary_data['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
				$summary_data['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['price_dzn']+=$row[csf("confirm_price_dzn")];
				//$summary_data[price_dzn_job]+=$job_wise_arr[$row[csf("job_no")]]['po_amount'];//($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			    $summary_data['commission_dzn']+=$row[csf("commission")];
				$summary_data['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['trims_cost_dzn']+=$row[csf("trims_cost")];
				$summary_data['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['embel_cost_dzn']+=$row[csf("embel_cost")];
				$summary_data['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("commission")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

				$summary_data['other_direct_dzn']+=$other_direct_expenses;
				$summary_data['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['wash_cost_dzn']+=$row[csf("wash_cost")];
				$summary_data['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['lab_test_dzn']+=$row[csf("lab_test")];
				$summary_data['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['inspection_dzn']+=$row[csf("inspection")];
				$summary_data['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['freight_dzn']+=$row[csf("freight")];
				$summary_data['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				$freight_cost_data[$row[csf("quotation_id")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
				$summary_data['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
				$summary_data['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
				$summary_data['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
				$summary_data['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$quot_studio_cost_dzn_arr[$row[csf("quotation_id")]]['studio_dzn_cost']=$row[csf("studio_percent")];
				$quot_studio_cost_dzn_arr[$row[csf("quotation_id")]]['common_oh']=$row[csf("common_oh")];

				$fab_amount_dzn=$fab_summary_data[$row[csf("quotation_id")]]['fab_amount_dzn'];
				$summary_data['fab_amount_dzn']+=$fab_amount_dzn;
				$summary_data['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				$yarn_amount_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
				$summary_data['yarn_amount_dzn']+=$yarn_amount_dzn;
				$summary_data['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				 $conv_amount_dzn=$conv_summary_data[$row[csf("quotation_id")]]['conv_amount_dzn'];
				 $summary_data['conversion_cost_dzn']+=$conv_amount_dzn;
				 $summary_data['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

				//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
				$net_value_dzn=$row[csf("price_with_commn_dzn")];

				$summary_data['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
				$summary_data['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

				//yarn_amount_total_value
				$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
				//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
				$summary_data['cost_of_material_service']+=$all_cost_dzn;
				$summary_data['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
				$summary_data['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
				$summary_data['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['cm_cost_dzn']+=$row[csf("cm_cost")];
				$summary_data['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['comm_cost_dzn']+=$row[csf("comm_cost")];
				$summary_data['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['common_oh_dzn']+=$row[csf("common_oh")];
				$summary_data['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
				//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
				$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
				$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
				$summary_data['gross_profit_dzn']+=$tot_gross_profit_dzn;
				$summary_data['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

				//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
				$operate_profit_loss_dzn=$tot_gross_profit_dzn-($row[csf("comm_cost")]+$row[csf("common_oh")]);
				$summary_data['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
				$summary_data['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
				$summary_data['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
				$summary_data['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
				$summary_data['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
				$summary_data['net_profit_dzn']+=$net_profit_dzn;
				$summary_data['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
			}
			$sql_commi = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls
			where  quotation_id in(".$all_quot_id.") and status_active=1";
			$result_commi=sql_select($sql_commi);$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
			foreach($result_commi as $row){

				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

				if($row[csf("particulars_id")]==1) //Foreign
					{
						$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
						$CommiData_foreign_quot_cost_arr[$row[csf("quotation_id")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						$local_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_local_quot_cost_arr[$row[csf("quotation_id")]]=$row[csf("commision_rate")];
					}
			}


			$sql_comm="select item_id,quotation_id,sum(amount) as amount from wo_pri_quo_comarcial_cost_dtls where  quotation_id in(".$all_quot_id.") and status_active=1 group by quotation_id,item_id";
			$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;$summary_data['comm_cost_dzn']=0;$summary_data['comm_cost_total_value']=0;
			$result_comm=sql_select($sql_comm);
			foreach($result_comm as $row){

			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				$comm_amtPri=$row[csf('amount')];
				$item_id=$row[csf('item_id')];
				if($item_id==1)//LC
					{
						$commer_lc_cost+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$tot_lc_dzn_Commer+=$row[csf("amount")];
						$commer_lc_cost_quot_arr[$row[csf("quotation_id")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						$commer_without_lc_cost+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$tot_without_lc_dzn_Commer+=$row[csf("amount")];
					}
			}
					$summary_data['comm_cost_dzn']=$tot_without_lc_dzn_Commer;
					$summary_data['comm_cost_total_value']=$commer_without_lc_cost;

					$summary_data['other_direct_total_value']+=$CommiData_lc_cost;
					$summary_data['other_direct_dzn']+=$local_dzn_commission_amount;

	?>
	<div style="width:950px;">

	<table width="850px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="9" align="center"><strong style=" font-size:xx-large"><? echo $company_library[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align="center"  colspan="9" class="form_caption"><strong style=" font-size:x-large"><? echo 'Final Cost Sheet';//$report_title; ?></strong></td>
                </tr>
            </table>
            <table width="auto"  style="margin-left:10px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <caption>
               		 <strong>SUMMARY:</strong>
                 </caption>
                    <thead>
                     	<th width="120"><b>Buyer</b> </th>
                        <th width="120"><b>Item</b> </th>
                        <th width="100"><b>Ship Date</b> </th>
                        <th width="120"><b>Style</b> </th>
                        <th width="100"><b>Quotation No</b> </th>
                        <th width="80"><b>Qty.</b> </th>
                        <th width="80"><b>Qty.(PCS)</b> </th>
                        <th width="60"><b>FOB</b> </th>
                        <th width="80"><b>Total Amount</b> </th>
                    </thead>
                    <?
					$k=1;$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=0;
					$all_last_shipdates='';
                    foreach($style_wise_arr as $style_key=>$val)
					{
						 $gmts_item_id=$val[('gmts_item_id')];
						 $shipment_date=rtrim($val[('shipment_date')],',');
						  $shipment_dates=array_unique(explode(",",$shipment_date));
						   $last_shipmentdates=max($shipment_dates);
						   $all_last_shipdates.=$last_shipmentdates.',';
							$gmts_item=''; $gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}
					?>
                	<tr>
                        <td width="120"><p> <? echo $buyer_arr[$val[('buyer_name')]];?></p></td>
                        <td  width="120"><p>  <? echo $gmts_item;?></p></td>
                        <td width="100"><p> <? echo change_date_format($last_shipmentdates);?></p></td>
                        <td width="120"><p> <? echo $style_key;?></p></td>
                        <td width="100"><p> <? echo $val[('quotation_id')];?></p></td>
                        <td width="80" align="right"><p> <? echo number_format($val[('qty')],0);?></p></td>
                        <td  width="80" align="right">  <? echo  number_format($val[('qty_pcs')],0);?></td>
                        <td width="60" align="right"><p> <? echo number_format($val[('final_cost_pcs')],4);?></p></td>
                        <td width="80" align="right"><p> <? echo number_format($val[('total_cost')],2);?></p></td>
                    </tr>
                    <?
					$k++;
					$total_quot_qty+=$val[('qty')];
					$total_quot_pcs_qty+=$val[('qty_pcs')];
					$total_quot_amount+=$val[('total_cost')];
					$total_quot_amount_arr[$val[('quotation_id')]]+=$val[('total_cost')];
					}
					?>
                    <tfoot>
                     <tr>
                    <td align="right" colspan="2">  <b>Qty DZN </b></td>
                    <td align="right"> &nbsp; <? echo number_format($total_quot_pcs_qty/12,2);?></td>
					<td align="right">&nbsp;</td>
					<td align="right">  <b>Total</b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_quot_qty,0);?> </b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_quot_pcs_qty,0);?> </b></td>
					<td align=""><b> &nbsp;</b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_quot_amount,2);?> </b></td>
                    </tr>

                    <tr>
                    <td align="right" colspan="2">  <b>Last Shipment Date </b></td>
                    <td align="right"> &nbsp; <?
					$all_last_ship_dates=rtrim($all_last_shipdates,',');
					 $all_last_ship_dates=array_unique(explode(",",$all_last_ship_dates));
					 $last_shipment_dates=max($all_last_ship_dates);
					 echo change_date_format($last_shipment_dates);?></td>
					 <td align="right">&nbsp;</td>
					 <td align="right">&nbsp;</td>

					 <td align="right" colspan="3"><b>Foreign Commission &nbsp; <? $foreign_percent_rate=($CommiData_foreign_cost/$total_quot_amount)*100;
					 echo number_format($foreign_percent_rate,2).'%';?> </b></td>
					<td align="right"><b> &nbsp; <? echo number_format($CommiData_foreign_cost,2);?> </b></td>
                    </tr>

                     <tr>
                    <td align="right" colspan="2" title="Total Quotation Value-Commission">  <b>Maximum BTB LC-70% </b></td>
                    <td align="right" title="Commission=<? echo $summary_data['commission_dzn'];?>"> &nbsp; <?
					$net_fob_value=$total_quot_amount-$summary_data['commission_dzn'];
					 echo number_format(($net_fob_value*70)/100,2);?></td>
					  <td align="right">&nbsp;</td>
					 <td align="right">&nbsp;</td>

					  <td align="right" colspan="3"><b>Freight Cost &nbsp; <?
					 $freight_percent_rate=($summary_data['freight_total_value']/$total_quot_amount)*100;
					 echo number_format($freight_percent_rate,2).'%';?> </b></td>
					<td align="right"><b> &nbsp; <?
					$pri_freight_cost_per=$summary_data['freight_total_value'];
					echo number_format($pri_freight_cost_per,2);?> </b></td>

                    </tr>
					<tr>
					  	 <td align="right" colspan="5"> </td>
						  <td align="right" colspan="3" title="<? echo $CommiData_foreign_cost;?>"><b>LC Cost &nbsp; <?
						  $commar_rate_percent=($commer_lc_cost/$total_quot_amount)*100;
						  echo number_format($commar_rate_percent,2).'%';?> </b></td>
						<td align="right"><b> &nbsp; <?
						$pri_commercial_per=$commer_lc_cost;echo number_format($pri_commercial_per,2);
						$tot_quot_sum_amount=$total_quot_amount-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
						//echo '='.$tot_quot_sum_amount;
						?> </b></td>
					  </tr>

                    <tr>
                    <td colspan="3" align="right"> <b style="float:left"> &nbsp; <? //echo implode(",",array_unique(explode(",",$all_file_no))); ?> </b></td>
                    <td colspan="5" align="right"> <b> Total </b></td>

                    <td align="right"> <b> <? echo number_format($tot_quot_sum_amount,2);?> </b></td>
                    </tr>
                    </tfoot>

           </table>
           <br>
		 <div style="width:100%">
            <? $tot_operating_expense=$total_quot_income_tax_val=$total_quot_interest_exp_val=$total_quot_amount_val=$tot_qout_studio_cost=$total_quot_commision_local_val=$total_quot_net_amount_cal=0;
			foreach($all_quot_ids as $qid)
			{
				$quot_date=$quot_wise_arr[$qid]['quot_date'];
				$pri_quot_date=change_date_format($quot_date,'','',1);
				$freight_total_value=$freight_cost_data[$qid]['freight_total_value'];
				$total_quot_amount_cal=$total_quot_amount_arr[$qid];
				$studio_dzn_cost=$quot_studio_cost_dzn_arr[$qid]['studio_dzn_cost'];
				$common_oh_dzn_cost=$quot_studio_cost_dzn_arr[$qid]['common_oh'];
				$commision_quot_local=$commision_local_quot_cost_arr[$qid];
				//echo $studio_dzn_cost.'<br>';
				$CommiData_foreign_quot_cost=$CommiData_foreign_quot_cost_arr[$qid];
				$commer_lc_cost_quot=$commer_lc_cost_quot_arr[$qid];

				$operating_expn=0;
				$operating_expn=$financial_para_arr[$pri_quot_date]['operating_expn'];
				$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost+$commer_lc_cost_quot+$freight_total_value);
					//echo $total_quot_amount_cal.'='.$freight_total_value.',';
				$tot_operating_expense+=($tot_sum_amount_quot_calc*$operating_expn)/100;
				$tot_qout_studio_cost+=($tot_sum_amount_quot_calc*$studio_dzn_cost)/100;

				$income_tax=$financial_para_arr[$pri_quot_date]['income_tax'];
				$interest_expense=$financial_para_arr[$pri_quot_date]['interest_expense'];
				//echo $total_quot_amount_cal.'='.$commision_quot_local.',';
				$total_quot_income_tax_val+=($total_quot_amount_cal*$income_tax)/100;
				$total_quot_interest_exp_val+=($total_quot_amount_cal*$interest_expense)/100;
				$total_quot_commision_local_val+=($tot_sum_amount_quot_calc*$commision_quot_local)/100;
				$total_quot_amount_val+=$total_quot_amount_arr[$qid];
				$total_quot_net_amount_cal+=$tot_sum_amount_quot_calc;

			}
			//echo $tot_operating_expense;
			$tot_income_tax_dzn=($total_quot_income_tax_val/$total_quot_amount_val)*12;
			$tot_interest_exp_dzn=($total_quot_interest_exp_val/$total_quot_amount_val)*12;

			$tot_commision_local_dzn=($total_quot_commision_local_val/$total_quot_net_amount_cal)*12;
			//echo $total_quot_income_tax_val.'=='.$tot_income_tax_dzn;
			$summary_data['other_direct_total_value']+=$tot_qout_studio_cost;
			?>
			<div>
           <div style="margin-left:10px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            	<caption> <strong> Quotation Profitability </strong></caption>
                <tr style="font-weight:bold">
                    <td width="80">Line Items</td>
                    <td width="300">Particulars</td>
                    <td width="100">Amount (USD)/ DZN</td>
                    <td width="100">Total Value( Offer Qnty)</td>
                    <td width="180">%</td>
                </tr>
                    <tr>
                    <td><? echo $sl;?></td>
                    <td align="left"><b>Net FOB Value</b></td>
                    <td align="right"><b><? echo number_format($summary_data['price_with_commn_dzn'],4); $order_price_summ=$summary_data['price_with_commn_dzn'];?></b></td>
                    <td align="right"><? $NetFOBValue_job=$tot_quot_sum_amount;
					$summary_data['price_with_total_value']=$tot_quot_sum_amount;
					echo number_format($summary_data['price_with_total_value'],2); ?></td>
                    <td align="right"><? echo "100.00"; ?></td>
                </tr>

                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost of Material & Services (3+4+5+6+7) </b></td>
                    <td align="right"><b><?   $LessCostOfMaterialServices=$summary_data['cost_of_material_service'];echo number_format($LessCostOfMaterialServices,4); ?></b></td>
                    <td align="right"><? $cost_of_material_service_total_value=$summary_data['cost_of_material_service_total_value'];echo number_format($cost_of_material_service_total_value,2); ?></td>
                    <td align="right"><b><? echo number_format(($summary_data['cost_of_material_service_total_value']/$summary_data['price_with_total_value'])*100,2)?></b></td>
                 </tr>
                  <tr>
                            <td rowspan="2"><? echo ++$sl; ?></td>
                            <td align="left" style=" padding-left:100px;font-weight:bold">Fabric Purchase Cost</td>
                            <td  align="right" style="font-weight:bold"> <? echo number_format( $summary_data['fab_amount_dzn'],4); ?></td>
                            <td  align="right" style="font-weight:bold"> <? echo number_format( $summary_data['fab_amount_total_value'],2); ?></td>
                            <td  align="right" style="font-weight:bold"><? echo number_format(($summary_data['fab_amount_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                  </tr>
                <tr>

                    <td align="left" style=" padding-left:100px"><strong>Yarn Cost</strong></td>
                    <td align="right"><? $yarn_amount_dzn=$summary_data['yarn_amount_dzn'];echo number_format($yarn_amount_dzn,4); ?></td>
                    <td align="right"><? echo number_format($summary_data['yarn_amount_total_value'],2); ?></td>
                    <td align="right"><? echo number_format(($summary_data['yarn_amount_total_value']/$summary_data['price_with_total_value'])*100,2)?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px">
                    <strong>Conversion Cost</strong>
                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="180"> <? echo $conversion_cost_head_array[$key]; ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                    <td align="right">
					<? $conversion_cost_dzn=$summary_data['conversion_cost_dzn'];echo number_format($conversion_cost_dzn,4); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"> <? echo number_format($value['conv_amount_dzn'],4); ?></td></tr>
                    <? }?>
                    </table>

                    </td>
                    <td align="right">
                    <? echo number_format($summary_data['conversion_cost_total_value'],2); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <?
					$tot_dye_chemi_process_amount=$tot_yarn_dye_process_amount=$tot_aop_process_amount=0;
					foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"><?
						if($key==101) //Dye/Chemical
						{
						$tot_dye_chemi_process_amount+=$value['conv_amount_total_value'];
						}
						else if($key==30) //Y/D
						{
						$tot_yarn_dye_process_amount+=$value['conv_amount_total_value'];
						}
						else if($key==35) //AOP
						{
						$tot_aop_process_amount+=$value['conv_amount_total_value'];
						}
						echo number_format($value['conv_amount_total_value'],2); ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                    <td align="right">
					<? echo number_format(($summary_data['conversion_cost_total_value']/$summary_data['price_with_total_value'])*100,2)?>
                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"> <? echo number_format(($value['conv_amount_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Trims Cost</strong></td>
                    <td align="right"><? echo number_format($summary_data['trims_cost_dzn'],4); ?></td>
                    <td align="right"><? echo number_format($summary_data['trims_cost_total_value'],2); ?></td>
                    <td align="right"><? echo number_format(($summary_data['trims_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Embellishment Cost</strong></td>
                    <td align="right"><? echo number_format($summary_data['embel_cost_dzn'],4); ?></td>
                    <td align="right"><? echo number_format($summary_data['embel_cost_total_value'],2); ?></td>
                    <td align="right"><? echo number_format(($summary_data['embel_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px">
                    <strong>Other Direct Expenses</strong>

                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="180"> Washing Cost (Gmt.)</td></tr>
                    <tr><td width="180"> Lab Test</td></tr>
                    <tr><td width="180"> Inspection Cost</td></tr>
                 <!--   <tr><td width="180"> Freight Cost</td></tr>-->
                    <tr><td width="180"> Currier Cost</td></tr>
                    <tr><td width="180"> Commision Cost(Local)</td></tr>
                    <tr><td width="180"> Certificate Cost</td></tr>
                     <tr><td width="180"> S.Cost</td></tr>
                    <tr><td width="180"> Design Cost</td></tr>

                    </table>
                    </td>
                    <td align="right">
					<?
					$other_direct_expense=$summary_data['other_direct_dzn'];echo  number_format($other_direct_expense,4);
					?>
                    <table class="rpt_table" border="1" rules="all">
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['wash_cost_dzn'],4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['lab_test_dzn'],4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['inspection_dzn'],4); ?></td></tr>
                        <!--<tr><td width="98" align="right"> <? //echo number_format($summary_data['freight_dzn'],4); ?></td></tr>-->
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['currier_pre_cost_dzn'],4); ?></td></tr>
                        <tr>
                            <td width="98" align="right">
								<?
									$summary_data['commission_total_value']=$total_quot_commision_local_val;
									$summary_data['commission_dzn']=$tot_commision_local_dzn;
									if($summary_data['commission_total_value']>0) echo number_format($summary_data['commission_dzn'],4);else echo "&nbsp;";
								?>
                            </td>
                        </tr>
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['certificate_pre_cost_dzn'],4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['studio_pre_cost_dzn'],4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format($summary_data['design_pre_cost_dzn'],4); ?></td></tr>

                    </table>
                    </td>
                    <td align="right">
                   <? echo number_format($summary_data['other_direct_total_value'],2); ?>
                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['wash_cost_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['lab_test_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['inspection_total_value'],2); ?></td></tr>
                   <!-- <tr><td width="98" align="right"> <? //echo number_format($summary_data['freight_total_value'],4); ?></td></tr>-->
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['currier_pre_cost_total_value'],2); ?></td></tr>
                     <tr><td width="98" align="right"><? echo number_format($summary_data['commission_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['certificate_pre_cost_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"><? $summary_data['studio_pre_cost_total_value']=0;
					$summary_data['studio_pre_cost_total_value']=$tot_qout_studio_cost;
					 echo number_format($summary_data['studio_pre_cost_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['design_pre_cost_total_value'],2); ?></td></tr>

                    </table>
                    </td>
                    <td align="right">
					<? echo number_format(($other_direct_expense/$summary_data['price_with_total_value'])*100,2); ?>
                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="98" align="right"> <? echo number_format(($summary_data['wash_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($summary_data['lab_test_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($summary_data['inspection_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                   <!-- <tr><td width="98" align="right"> <? //echo number_format(($summary_data['freight_total_value']/$summary_data['price_with_total_value'])*100,4); ?></td></tr>-->
                    <tr><td width="98" align="right"> <? echo number_format(($summary_data['currier_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                     <tr><td width="98" align="right"> <? echo number_format(($summary_data['commission_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($summary_data['certificate_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format(($summary_data['studio_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="98" align="right">  <? echo number_format(($summary_data['design_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>

                    </table>
                    </td>
                </tr>
                <tr>
                     <td width="80"><? echo ++$sl; ?></td>
                    <td width="380" align="left" style="font-weight:bold">Contributions/Value Additions (1-2)</td>
                    <td width="100" align="right" style="font-weight:bold"> <? $Contribution_Margin=$summary_data['contribution_margin_dzn'];echo number_format($Contribution_Margin,4); ?></td>
                    <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['contribution_margin_total_value'],2); ?></td>
                    <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data['contribution_margin_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
               </tr>
                <tr>
                    <td width="80"><? echo ++$sl; ?></td>
                    <td width="380" align="left" style=" padding-left:15px">Less: CM Cost </td>
                    <td width="100" align="right"><? echo number_format($summary_data['cm_cost_dzn'],4); ?> </td>
                    <td width="100" align="right"><? echo number_format($summary_data['cm_cost_total_value'],2); ?></td>
                    <td width="180" align="right"><? echo number_format(($summary_data['cm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
              </tr>
               <tr>
                    <td width="80"><? echo ++$sl; ?></td>
                    <td width="380" align="left" style="font-weight:bold">Gross Profit (8-9)</td>
                    <td width="100" align="right" style="font-weight:bold"> <? $Gross_Profit=$summary_data['gross_profit_dzn']; echo number_format($Gross_Profit,4); ?></td>
                    <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['gross_profit_total_value'],2); ?></td>
                    <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data['gross_profit_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>

                <tr>
                   <td width="80"><? echo ++$sl; ?></td>
                    <td width="380" align="left" style=" padding-left:15px">Less: Commercial Cost(Without LC Cost)</td>
                    <td width="100" align="right"><?
					echo number_format($summary_data['comm_cost_dzn'],4); ?></td>
                    <td width="100" align="right"><? echo number_format($summary_data['comm_cost_total_value'],2); ?></td>
                    <td width="180" align="right"><? echo number_format(($summary_data['comm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
                <tr>
                    <td width="80"><? echo ++$sl; ?></td>
                    <td width="380" align="left" style=" padding-left:15px">Less: Operating Expensees/Maintance</td>

                    <td width="100" align="right"><?
					$summary_data['common_oh_total_value']=0;
					$summary_data['common_oh_total_value']=$tot_operating_expense;
					echo number_format( $summary_data['common_oh_dzn'],4); ?></td>
                    <td width="100" align="right"><? echo number_format( $summary_data['common_oh_total_value'],2); ?></td>
                    <td width="180" align="right"><? echo number_format(( $summary_data['common_oh_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>

                  <tr>
                       <td width="80"><? echo ++$sl; ?></td>
                        <td width="380" align="left" style="font-weight:bold">Operating Profit/ Loss (10-(11+12))</td>
                        <td width="100" align="right" style="font-weight:bold"> <? $OperatingProfitLoss=$summary_data['operating_profit_loss_dzn'];echo number_format($OperatingProfitLoss,4); ?></td>
                        <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['operating_profit_loss_total_value'],2); ?></td>
                        <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data['operating_profit_loss_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
                <tr>
                    <td width="80"><? echo ++$sl; ?></td>
                    <td width="380" align="left" style=" padding-left:15px">Less: Depreciation & Amortization </td>
                    <td width="100" align="right"> <? echo number_format($summary_data['depr_amor_pre_cost_dzn'],4); ?></td>
                    <td width="100" align="right"><? echo number_format($summary_data['depr_amor_pre_cost_total_value'],2); ?></td>
                    <td width="180" align="right"><? echo number_format(($summary_data['depr_amor_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
               <tr>
                        <td width="80"><? echo ++$sl; ?></td>
                        <td width="380" align="left" style=" padding-left:15px">Less: Interest </td>
                        <td width="100" align="right"> <? $summary_data['interest_pre_cost_dzn']=0;$summary_data['interest_pre_cost_total_value']=0;
						$summary_data['interest_pre_cost_dzn']=$tot_interest_exp_dzn;$summary_data['interest_pre_cost_total_value']=$total_quot_interest_exp_val;
						echo number_format($summary_data['interest_pre_cost_dzn'],4); ?></td>
                        <td width="100" align="right"><? echo number_format($summary_data['interest_pre_cost_total_value'],2); ?></td>
                        <td width="180" align="right"><? echo number_format(($summary_data['interest_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>               </tr>
               <tr>
                       <td width="80"><? echo ++$sl; ?></td>
                        <td width="380" align="left" style=" padding-left:15px">Less: Income Tax</td>
                        <td width="100" align="right"> <?
						$summary_data['income_tax_pre_cost_dzn']=0;$summary_data['income_tax_pre_cost_total_value']=0;
						$summary_data['income_tax_pre_cost_dzn']=$tot_income_tax_dzn;$summary_data['income_tax_pre_cost_total_value']=$total_quot_income_tax_val;
						echo number_format($summary_data['income_tax_pre_cost_dzn'],4); ?></td>
                        <td width="100" align="right"><? echo number_format($summary_data['income_tax_pre_cost_total_value'],2); ?></td>
                        <td width="180" align="right"><? echo number_format(($summary_data['income_tax_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
                <tr>

               <td width="80"><? echo ++$sl; ?></td>
                <td width="380" align="left" style="font-weight:bold">Net Profit (13-(14+15+16))</td>
                <td width="100" align="right" style="font-weight:bold"><? $Netprofit=$summary_data['net_profit_dzn'];echo number_format($Netprofit,4); ?> </td>
                <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['net_profit_dzn_total_value'],2); ?></td>
                <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data['net_profit_dzn_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
               </tr>

            </table>
      </div> <!--Quotation End-->
                        <br/>
                         <table width="470px" style="margin-left:100px;" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  rules="all">
                         <caption> <strong><? echo 'Quotation Summary';?></strong></caption>
                          <thead>
                               <th  align="center"><strong>Line</strong></th>
							    <th  align="center"><strong>Particulars</strong></th>
                                <th  align="center"><strong>Total Value</strong></th>
                                <th  align="center"><strong>%</strong></th>
                           </thead>
                            <tr>
                                 <td width="20" align="center">1</td>
								<td width="230" align="left">Yarn Cost</td>
                                <td width="100" align="right"><? echo number_format($summary_data['yarn_amount_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['yarn_amount_total_value']/$summary_data['price_with_total_value'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">2</td>
								<td width="230" align="left">Fabric Purchase</td>
                                <td width="100" align="right"><? echo number_format( $summary_data['fab_amount_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['fab_amount_total_value']/$summary_data['price_with_total_value'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                               	 <td width="20" align="center">3</td>
							    <td width="230" align="left">Dyes & Chemical</td>
                                <td width="100" align="right"><? echo number_format( $tot_dye_chemi_process_amount,2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($tot_dye_chemi_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">4</td>
								<td width="230" align="left">Y/D.</td>
                                <td width="100" align="right"><? echo number_format( $tot_yarn_dye_process_amount,2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($tot_yarn_dye_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">5</td>
								<td width="230" align="left">AOP</td>
                                <td width="100" align="right"><? echo number_format($tot_aop_process_amount,2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_aop_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                            <tr>
                                 <td width="20" align="center">6</td>
								<td width="230" align="left">Accessories</td>
                                <td width="100" align="right"><? echo number_format($summary_data['trims_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['trims_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                            <tr>
                                 <td width="20" align="center">7</td>
								<td width="230" align="left">Commercial[without LC]</td>
                                <td width="100" align="right"><? echo number_format($summary_data['comm_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['comm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">8</td>
								<td width="130" align="left">Print/Emb/GMT Dye/Wash</td>
                                <td width="100" align="right"><? $tot_emblish_cost=$summary_data['embel_cost_total_value'];echo number_format($tot_emblish_cost,2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_emblish_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">9</td>
							    <td width="230" align="left">Lab Test</td>
                                <td width="100" align="right"><? echo number_format($summary_data['lab_test_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($summary_data['lab_test_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							 <tr>
                                <td width="20" align="center">10</td>
							    <td width="230" align="left">Operating Expensees/Maintance</td>
                                <td width="100" align="right"><? echo number_format($summary_data['common_oh_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($summary_data['common_oh_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							<tr>
                                <td width="20" align="center">11</td>
							    <td width="230" align="left">S.Cost</td>
                                <td width="100" align="right"><? echo number_format($summary_data['studio_pre_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($summary_data['studio_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							  <tr>
                                 <td width="20" align="center">12</td>
								<td width="230" align="left">Inspection/Courier/Commission(Local)/Certificate/Design</td>
                                <td width="100" align="right" title=""><?
								$tot_inspect_cour_certi_cost=$summary_data['inspection_total_value']+$summary_data['currier_pre_cost_total_value']+$summary_data['certificate_pre_cost_total_value']+$summary_data['commission_total_value']+$summary_data['design_pre_cost_total_value'];
								echo number_format($tot_inspect_cour_certi_cost,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_inspect_cour_certi_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>

                             <tr>
                                 <td width="20" align="center">13</td>
								<td width="230" align="left">Total for BTB</td>
                                <td width="100" align="right" title="Lab Test+Emblish Cost+Cmmercial Cost+Trims Cost+Yarn Dye Process Cost+Dye Chemical+Yarn Cost+AOP Cost"><?
									$total_btb=$summary_data['lab_test_total_value']+$tot_emblish_cost+$summary_data['comm_cost_total_value']+$summary_data['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data['common_oh_total_value']+$summary_data['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
								 echo number_format($total_btb,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($total_btb/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">14</td>
								<td width="230" align="left">CM for Fabrics (Knitting & Dyeing Charge )</td>
                                <td width="100" align="right" title="Tot Conversion Cost-(Y/D+Dye & Chemical+AOP)"><?
								$tot_cm_for_fab_cost=$summary_data['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
								echo number_format($tot_cm_for_fab_cost,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_cm_for_fab_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>


                             <tr>
                             <?
                             $total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);
							 ?>
                                 <td width="20" align="center">15</td>
								<td width="230" align="left">CM for Garments&nbsp;(CM Dzn=<? echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2);?>)</td>
                                <td width="100" align="right" title="Gross FOB Value-Tot CM Fab Cost Cost-Total BTB-Inspect-Freight-Courier-Certificate-Commission"><?

								 echo number_format($total_cm_for_gmt,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($total_cm_for_gmt/$NetFOBValue_job)*100,2);?></td>
                            </tr>
                             <tr style="background-color:#999999; font-size:large">
							  <td width="20" align="center">16</td>
                                <td width="230" align="left"><b>Net Quotation Value</b></td>
                                <td width="100" align="right"><b><? echo number_format($NetFOBValue_job,2);?></b></td>
                                <td width="50"  align="right"><b><? echo number_format(($NetFOBValue_job/$summary_data['price_with_total_value'])*100,2);?></b></td>
                            </tr>
                             <tr>
                             <td colspan="4" style="border:hidden">&nbsp;</td>
                             </tr>
                        </table>
                        <br/>
                        <div id="" style="width:870px;">
                         <br/><br/>
				    <?
                      		echo signature_table(109, $cbo_company_name, "870px");
                        ?>
                        </div>
						</div>
	</div>
	<?
	exit();
}

if($action=="generate_report" && $type=="preCostRpt5")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $qoutation_id_cond=''; else $qoutation_id_cond=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name_cond=''; else $company_name_cond=" and a.company_id=".$cbo_company_name."";

	//array for display name
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sizeArr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$dealMarchentArr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	?>
    <div style="width:930px;">
        <table width="930px">
            <tr class="form_caption">
                <td colspan="6" align="center" class="form_caption"><strong style="font-size:18px"><? echo $comp[str_replace("'",'',$cbo_company_name)];?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" class="form_caption"><strong style="font-size:16px"><? echo 'PRICE QUOTATION'; ?></strong></td>
            </tr>
        </table>
		<?

		$quot_size_arr=array();
		$size_sql="Select gmts_sizes from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=".$txt_quotation_id."";
		$size_sql_result=sql_select($size_sql);
		foreach($size_sql_result as $sizeRow)
		{
			$quot_size_arr[$sizeArr[$sizeRow[csf("gmts_sizes")]]]=$sizeArr[$sizeRow[csf("gmts_sizes")]];
		}
		unset($size_sql_result);

        $sql="select a.id, a.gmts_item_id, a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.order_uom, a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,  a.pord_dept, a.season_buyer_wise, a.sew_smv, a.dealing_merchant, a.costing_per, a.remarks, 
		b.price_with_commn_pcs, b.total_cost, b.terget_qty, b.cm_cost, b.trims_cost, b.wash_cost, b.lab_test, b.comm_cost, b.freight, b.currier_pre_cost, b.certificate_pre_cost, b.design_pre_cost, b.studio_pre_cost, b.interest_pre_cost, b.income_tax_pre_cost, b.commission

		from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $qoutation_id_cond order by a.id ";
        $sql_quot_result=sql_select($sql);
       	$mkt_profit=$costing_per_id=$cm_cost=$trims_cost=$wash_cost=$lab_test=$comm_cost=$freight=$currier_pre_cost=$certificate_pre_cost=$design_pre_cost=$studio_pre_cost=$interest_pre_cost=$income_tax_pre_cost=$commission="";
        foreach($sql_quot_result as $row)
        {
			$cm_cost=$row[csf("cm_cost")];
			$trims_cost=$row[csf("trims_cost")];
			$wash_cost=$row[csf("wash_cost")];
			$lab_test=$row[csf("lab_test")];
			$comm_cost=$row[csf("comm_cost")];
			$freight=$row[csf("freight")];
			$currier_pre_cost=$row[csf("currier_pre_cost")];
			$certificate_pre_cost=$row[csf("certificate_pre_cost")];
			$design_pre_cost=$row[csf("design_pre_cost")];
			$studio_pre_cost=$row[csf("studio_pre_cost")];
			$interest_pre_cost=$row[csf("interest_pre_cost")];
			$income_tax_pre_cost=$row[csf("income_tax_pre_cost")];
			$commission=$row[csf("commission")];

			$costing_per_id=$row[csf("costing_per")];
			$mkt_profit=$row[csf("terget_qty")]-$row[csf("total_cost")];
			$gmts_item="";
			$ex_item=explode(",",$row[csf("gmts_item_id")]);
			foreach($ex_item as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=','.$garments_item[$item_id];
			}
            ?>
            <table class="rpt_table" width="930px" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="120">Quotation Date</td>
                    <td width="130"><?=change_date_format($row[csf("quot_date")]); ?></td>
                    <td width="130">Total Cost:</td>
                    <td width="130" align="right"><?=number_format($row[csf("total_cost")],4); ?></td>
                    <td width="120">Quotation ID</td>
                    <td width="130" align="center"><?=$row[csf("id")]; ?></td>
                    <td width="120">Costing Per</td>
                    <td style="word-break:break-all" align="center"><?=$costing_per[$costing_per_id]; ?></td>
                 </tr>
                 <tr>
                    <td>Style Ref.</td>
                    <td style="word-break:break-all"><?=$row[csf("style_ref")]; ?></td>
                    <td>Target Cost :</td>
                    <td align="right"><?=number_format($row[csf("terget_qty")],4); ?></td>
                    <td>Buyer</td>
                    <td style="word-break:break-all"><?=$buyer_arr[$row[csf("buyer_id")]]; ?></td>
                    <td>Season</td>
                    <td style="word-break:break-all"><?=$season_name_arr[$row[csf("season_buyer_wise")]]; ?></td>
                 </tr>
                 <tr>
                 	<td>Dealing Merchant</td>
                    <td style="word-break:break-all"><?=$dealMarchentArr[$row[csf("dealing_merchant")]]; ?></td>
                    <td>Prod. Dept.</td>
                    <td style="word-break:break-all"><?=$product_dept[$row[csf("pord_dept")]]; ?></td>
                    <td>MKT. Profit :</td>
                    <td align="right"><?=number_format($mkt_profit,4); ?></td>
                    <td>SMV</td>
                    <td align="right"><?=number_format($row[csf("sew_smv")],4); ?></td>
                 </tr>
                 <tr>
                 	<td>Gmts. Size</td>
                    <td style="word-break:break-all"><?=implode(",",$quot_size_arr); ?></td>
                    <td>Style Description</td>
                    <td style="word-break:break-all"><?=$row[csf("style_desc")]; ?></td>
                    <td>Gmts. Item</td>
                    <td style="word-break:break-all" colspan="3"><?=$gmts_item; ?></td>
                 </tr>
                 <tr>
                    <td>Remarks</td>
                    <td colspan="7" style="word-break:break-all"><?=$row[csf("remarks")]; ?></td>
                 </tr>
             </table>
             <br>&nbsp;
            <?
		}

		$fabric_sql="select body_part_id, body_part_type, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, rate, amount from wo_pri_quo_fabric_cost_dtls where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0 order by id ASC";
		$fabric_sql_res=sql_select($fabric_sql); $fabric_cost_total=0;
		if(count($fabric_sql_res)>0)
		{
			?>
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd">
                	<tr>
                    	<th colspan="10">FABRIC COST</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Body Part</th>
                        <th width="70">Body Part Type</th>
                        <th width="80">Color Type</th>
                        <th width="200">Fabric Description</th>
                        <th width="60">GSM</th>
                        <th width="60">Cons [<?=$costing_per[$costing_per_id]; ?>]</th>
                        <th width="60">Price [KG]</th>
                        <th width="70">Total</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            <? $k=1;
			foreach($fabric_sql_res as $frow)
			{
				?>
                <tr>
                    <td align="center"><?=$k; ?></td>
                    <td style="word-break:break-all"><?=$body_part[$frow[csf('body_part_id')]]; ?></td>
                    <td style="word-break:break-all"><?=$body_part_type[$frow[csf('body_part_type')]]; ?>&nbsp;</td>
                    <td style="word-break:break-all"><?=$color_type[$frow[csf('color_type_id')]]; ?>&nbsp;</td>
                    <td style="word-break:break-all"><?=$frow[csf('fabric_description')]; ?>&nbsp;</td>
                    <td style="word-break:break-all"><?=$frow[csf('gsm_weight')]; ?>&nbsp;</td>
                    <td align="right"><?=number_format($frow[csf('avg_cons')], 4, '.', ''); ?>&nbsp;</td>
                    <td align="right"><?=number_format($frow[csf('rate')], 4, '.', ''); ?>&nbsp;</td>
                    <td align="right"><?=number_format($frow[csf('amount')], 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?
                $k++;
				$fabric_cost_total+=$frow[csf('amount')];
			}
			?>
            <tr bgcolor="#828282">
                <td colspan="8" align="right">Fabric Cost:</td>
                <td align="right"><?=number_format($fabric_cost_total, 4, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?
		}
		unset($fabric_sql_res);

		$embl_sql="select emb_name, amount from wo_pri_quo_embe_cost_dtls where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
		$embl_sql_res=sql_select($embl_sql); $printing_cost=$embroidery=$special_work=0;
		foreach($embl_sql_res as $erow)
		{
			if($erow[csf("emb_name")]==1) $printing_cost+=$erow[csf("amount")];
			if($erow[csf("emb_name")]==2) $embroidery+=$erow[csf("amount")];
			if($erow[csf("emb_name")]==4) $special_work+=$erow[csf("amount")];
		}
		unset($embl_sql_res);

		?>
            <tr bgcolor="#C0C0C0">
                <td colspan="8" align="center" >OTHERS COST</td>
            </tr>
            <tr>
                <td colspan="6">CM</td>
                <td align="right"><? echo number_format($cm_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Trims</td>
                <td align="right"><? echo number_format($trims_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Printing</td>
                <td align="right"><? echo number_format($printing_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Embroidery</td>
                <td align="right"><? echo number_format($embroidery, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Gmts. Wash</td>
                <td align="right"><? echo number_format($wash_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Special Work</td>
                <td align="right"><? echo number_format($special_work, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Lab Test</td>
                <td align="right"><? echo number_format($lab_test, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Commercial Cost / Bank Charge</td>
                <td align="right"><? echo number_format($comm_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Freight</td>
                <td align="right"><? echo number_format($freight, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Courier Cost</td>
                <td align="right"><? echo number_format($currier_pre_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Certificate Cost</td>
                <td align="right"><? echo number_format($certificate_pre_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Design Cost</td>
                <td align="right"><? echo number_format($design_pre_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Studio Cost</td>
                <td align="right"><? echo number_format($studio_pre_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Interest</td>
                <td align="right"><? echo number_format($interest_pre_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Income Tax</td>
                <td align="right"><? echo number_format($income_tax_pre_cost, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Commission</td>
                <td align="right"><? echo number_format($commission, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">Marketting Profit (Target Cost - Total Cost)</td>
                <td align="right"><? echo number_format($mkt_profit, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?
            $price_dzn=$fabric_cost_total+$cm_cost+$trims_cost+$printing_cost+$embroidery+$wash_cost+$special_work+$lab_test+$comm_cost+$freight+$currier_pre_cost+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$interest_pre_cost+$income_tax_pre_cost+$commission+$mkt_profit;
            $price_pcs=$price_dzn/12;
            $price_dzn_without_target=$price_dzn-$mkt_profit;
            $price_pcs_without_target=$price_dzn_without_target/12;
            ?>
            <tr bgcolor="#828282">
                <td colspan="6">PRICE/Dzn TOTAL</td>
                <td align="right"><? echo number_format($price_dzn, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr bgcolor="#CACACA">
                <td colspan="6">PRICE/Pcs TOTAL</td>
                <td align="right"><? echo number_format($price_pcs, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr bgcolor="#828282">
                <td colspan="6">PRICE/Dzn TOTAL Without Target Cost</td>
                <td align="right"><? echo number_format($price_dzn_without_target, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr bgcolor="#CACACA">
                <td colspan="6">PRICE/Pcs TOTAL Without Target Cost</td>
                <td align="right"><? echo number_format($price_pcs_without_target, 2, '.', ''); ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <br>&nbsp;
        <?
		$yarn_sql="select count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=".$txt_quotation_id." and status_active=1 and is_deleted=0";
		$yarn_sql_res=sql_select($yarn_sql); $j=1;
		if(count($yarn_sql_res)>0)
		{
			?>
			<table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd">
                	<tr>
                    	<th colspan="6">Yarn Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="450">Yarn Description</th>
                        <th width="70">Cons (<? echo $costing_per[$costing_per_id]; ?>)</th>
                        <th width="70">Price (KG)</th>
                        <th width="80">Total</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
				<?
                $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
                foreach($yarn_sql_res as $yrow)
                {
                    $item_descrition ="";
                    if($yrow[csf("percent_one")]==100)
                        $item_descrition = $lib_yarn_count[$yrow[csf("count_id")]]." ".$composition[$yrow[csf("copm_one_id")]]." ".$yrow[csf("percent_one")]."% ".$yarn_type[$yrow[csf("type_id")]];
                    else
                        $item_descrition = $lib_yarn_count[$yrow[csf("count_id")]]." ".$composition[$yrow[csf("copm_one_id")]]." ".$yrow[csf("percent_one")]."% ".$composition[$yrow[csf("copm_two_id")]]." ".$yrow[csf("percent_two")]."% ".$yarn_type[$yrow[csf("type_id")]];
                    ?>
                    <tr>
                        <td align="center"><? echo $j; ?></td>
                        <td style="word-break:break-all"><? echo $item_descrition; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($yrow[csf('cons_qnty')], 4, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($yrow[csf('rate')], 4, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($yrow[csf('amount')], 4, '.', ''); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $j++;
                    $yarn_tot+=$yrow[csf('amount')];
                }
                ?>
                <tr bgcolor="#828282">
                    <td colspan="4" align="right">Yarn Cost:</td>
                    <td align="right"><? echo number_format($yarn_tot, 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <?
		}

		$conv_sql = "select b.fabric_description, a.cost_head, a.cons_type, a.req_qnty, a.charge_unit, a.amount from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id where a.quotation_id=".$txt_quotation_id." and a.status_active=1 and a.is_deleted=0";
		$conv_sql_res=sql_select($conv_sql); $y=1;
		if(count($conv_sql_res)>0)
		{
			?>
			<table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd">
                	<tr>
                    	<th colspan="7">Conversion Cost to Fabric</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="370">Fabric Description</th>
                        <th width="80">Process</th>
                        <th width="70">Cons (<? echo $costing_per[$costing_per_id]; ?>)</th>
                        <th width="70">Price (KG)</th>
                        <th width="80">Total</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <?
                foreach($conv_sql_res as $crow)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $y; ?></td>
                        <td style="word-break:break-all"><? echo $crow[csf('fabric_description')]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $conversion_cost_head_array[$crow[csf('cons_type')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($crow[csf('req_qnty')], 4, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($crow[csf('charge_unit')], 4, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($crow[csf('amount')], 4, '.', ''); ?>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $y++;
                    $conv_tot+=$crow[csf('amount')];
                }
                ?>
                <tr bgcolor="#828282">
                    <td colspan="5" align="right">Conversion Cost:</td>
                    <td align="right"><? echo number_format($conv_tot, 4, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <?
		}
		?>
    </div>
    <?
    echo signature_table(109, $cbo_company_name, "870px");
    exit();
}

if($action=="generate_report" && $type=="costingSheetRpt")
{
	extract($_REQUEST);
	//echo $txt_quotation_date."_".$txt_quotation_id."_".$cbo_company_name."_".$cbo_buyer_name."_".$txt_style_ref;die;
	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	$txt_quotationdate=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id_cond=''; else $quotation_id_cond=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name_cond=''; else $company_name_cond=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $buyer_name_cond=''; else $buyer_name_cond=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $style_ref_cond=''; else $style_ref_cond=" and a.style_ref=".$txt_style_ref."";
	if($db_type==0) {
		$group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	}
	if($db_type==2)
	{
		$group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
	}



	//array for display name
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');//body_part_id
	$country_arr=return_library_array( "select id, country_name   from lib_country  where status_active=1 and is_deleted =0",'id','country_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=1 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pri_quo_fabric_cost_dtls b", "a.id=b.body_part_id and b.quotation_id=$txt_quotation_id and a.body_part_type=20 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	$season_name_arr=return_library_array( "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id=$cbo_buyer_name and b.company_name=$cbo_company_name and b.season_mandatory=1 and b.variable_list=44",'id','season_name');
	//$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pri_quo_fabric_cost_dtls", "quotation_id=$txt_quotation_id and body_part_id=20");

	//==Start = For Company Data//
	$comp_info=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, contact_no, email, website  from lib_company where status_active=1 and is_deleted =0");
	foreach($comp_info as $row)
	{
		$company_info_arr[$row[csf('id')]]['company_name']=$row[csf('company_name')];

		if($row[csf('plot_no')] != "") 		$company_info_arr[$row[csf('id')]]['address'] .= $row[csf('plot_no')];
		if($row[csf('level_no')] != "") 	$company_info_arr[$row[csf('id')]]['address'] .= ", ".$row[csf('level_no')];
		if($row[csf('road_no')] != "") 		$company_info_arr[$row[csf('id')]]['address'] .= ", ".$row[csf('road_no')];
		if($row[csf('block_no')] != "") 	$company_info_arr[$row[csf('id')]]['address'] .= ", ".$row[csf('block_no')];
		if($row[csf('province')] != "") 	$company_info_arr[$row[csf('id')]]['address'] .= ", ".$row[csf('province')];
		if($row[csf('city')] != "") 		$company_info_arr[$row[csf('id')]]['address'] .= ", ".$row[csf('city')];
		if($row[csf('zip_code')] != "") 	$company_info_arr[$row[csf('id')]]['address'] .= "-".$row[csf('zip_code')];
		if($row[csf('country_id')] != 0) 	$company_info_arr[$row[csf('id')]]['address'] .= ", ".$country_arr[$row[csf('country_id')]].".";
	}
	unset($comp_info);


	$sql_quot=sql_select("select a.id, a.company_id, a.buyer_id, a.quot_date, a.style_ref, a.season, a.offer_qnty, a.pord_dept, a.order_uom,
	listagg(b.gmts_item_id ,',') within group (order by b.gmts_item_id) AS gmts_item_id
	from wo_price_quotation a, wo_price_quotation_set_details b
	where a.id=b.quotation_id and a.status_active=1 and a.is_deleted =0 $quotation_id_cond $company_name_cond
	group by a.id, a.company_id, a.buyer_id, a.quot_date, a.style_ref, a.season, a.offer_qnty, a.pord_dept, a.order_uom
	");

	?>
    <style>
    	.textPadding{
			 padding-left:10px;
		}
		.wordBreakAll{
			word-break:break-all;
		}
    </style>
    <div style="width:1600px;">
        <div style="width:20%;float:left" >
            <table class="rpt_table wordBreakAll" border="1" cellpadding="1" cellspacing="1" style="width:100%;font-size:12px;" rules="all">
                <tbody>
                    <tr>
                        <td width=100>Buyer:</td>
                        <td width=220 class="textPadding"><? echo $buyer_arr[$sql_quot[0][csf('buyer_id')]] ;?></td>
                    </tr>
                    <tr>
                        <td>Quotation Date:</td>
                        <td  class="textPadding"><? echo change_date_format($sql_quot[0][csf('quot_date')]) ;?></td>
                    </tr>
                    <tr>
                        <td>Product Name:</td>
                        <td class="textPadding"><?
						$item_ids_arr = array_unique(explode(",",$sql_quot[0][csf('gmts_item_id')]));
						$itemNameSting= "";
						foreach($item_ids_arr as $itemId){
							if($itemNameSting != "") $itemNameSting .= ", ";
							$itemNameSting .=$garments_item[$itemId];
						}
						echo $itemNameSting ;
						?></td>
                    </tr>
                    <tr>
                        <td>Style#</td>
                        <td class="textPadding"><? echo $sql_quot[0][csf('style_ref')] ;?></td>
                    </tr>
                    <tr>
                        <td>Season:</td>
                        <td class="textPadding"><? echo $season_name_arr[$sql_quot[0][csf('season')]] ;?></td>
                    </tr>
                    <tr>
                        <td>Company Name</td>
                        <td class="textPadding"><? echo $company_info_arr[$sql_quot[0][csf('company_id')]]['company_name'] ;?></td>
                    </tr>
                    <tr>
                        <td>Order qty in Pcs</td>
                        <td class="textPadding"><?
						if($sql_quot[0][csf('order_uom')]==1)// Order Uom Set
						{
							echo $sql_quot[0][csf('offer_qnty')] ;
						}
						?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:60%;float:left">
            <table class="rpt_table wordBreakAll" border="0" cellpadding="1" cellspacing="1" style="width:100%;font-size:12px;" rules="all">
                <tbody>
                    <tr>
                        <td rowspan=7  align="center" style="border-top:none; border-bottom:none">
                            <span style="font-size:24px;">Costing Sheet </span><br>
                            <span style="font-size:22px;"><? echo $company_info_arr[str_replace("'","",$cbo_company_name)]['company_name']; ?></span> <br>
                            <span style="font-size:12px;"><? //echo $company_info_arr[str_replace("'","",$cbo_company_name)]['address']; ?></span> <br>
                         </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:20%;float:left">
            <table class="rpt_table wordBreakAll" border="1" cellpadding="1" cellspacing="1" style="width:100%;font-size:12px;" rules="all">
                <tbody>
                    <tr>
                        <td width=100>Product Dept.</td>
                        <td width=220  class="textPadding"><? echo $product_dept[$sql_quot[0][csf('pord_dept')]] ;?></td>
                    </tr>
                    <tr>
                        <td>Production unit:</td>
                        <td class="textPadding"><? //echo $sql_quot[0][csf('style_ref')] ;?></td>
                    </tr>
                    <tr>
                        <td>Size (Qtd):</td>
                        <td class="textPadding"><? //echo $sql_quot[0][csf('style_ref')] ;?></td>
                    </tr>
                    <tr>
                        <td>MOQ :</td>
                        <td class="textPadding"><? //echo $sql_quot[0][csf('style_ref')] ;?></td>
                    </tr>
                    <tr>
                        <td>MCQ :</td>
                        <td class="textPadding"><? //echo $sql_quot[0][csf('style_ref')] ;?></td>
                    </tr>
                    <tr>
                        <td>Color :</td>
                        <td class="textPadding"><? //echo $sql_quot[0][csf('style_ref')] ;?></td>
                    </tr>
                    <tr>
                        <td>Order qty in Packs</td>
                        <td class="textPadding"><?
						if($sql_quot[0][csf('order_uom')]==58)// Order Uom Set
						{
							echo $sql_quot[0][csf('offer_qnty')] ;
						}
						?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="width:1600px;">&nbsp;<br></div>

    <div style="width:1600px;">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1600px;font-size:12px;" rules="all">
            <thead>
                <tr>
                    <th width="100">Material</th>
                    <th width="130">Category</th>
                    <th width="180">Placement</th>
                    <th width="80">Cost</th>
                    <th width="100">Consumption</th>
                    <th width="80">Unit Price</th>
                    <th width="100">UOM</td>
                    <th width="100">Cuttable width / Meterial With</th>
                    <th width="80">Wastage %</th>
                    <th width="100">Type</th>
                    <th width="100">Description</th>
                    <th width="100">Composition</th>
                    <th width="100">Construction</th>
                    <th width="80">Weight</th>
                    <th width="100">Source</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="15" style="background-color:#CCCCCC;">Fabrics :</td>
                </tr>
                <tr>
                    <td>&nbsp <br><br> </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                </tr>
                <tr>
                    <td colspan="15" style="background-color:#CCCCCC;">Sewing Trims:</td>
                </tr>
                <tr>
                    <td>&nbsp <br><br> </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                </tr>
                <tr>
                    <td colspan="15" style="background-color:#CCCCCC;">CM</td>
                </tr>
                <tr>
                    <td>&nbsp <br><br></td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                </tr>
                <tr>
                    <td colspan="15" style="background-color:#CCCCCC;">Finishing :</td>
                </tr>
                <tr>
                    <td>&nbsp <br><br></td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                </tr>
                <tr>
                    <td colspan="15" style="background-color:#CCCCCC;">Packing Trims and Materials :</td>
                </tr>
                <tr>
                    <td>&nbsp <br><br></td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                </tr>
                <tr>
                    <td colspan="15" style="background-color:#CCCCCC;">Others Cost:</td>
                </tr>
                <tr>
                    <td>&nbsp <br><br></td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                    <td>&nbsp </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="width:1600px;">&nbsp;<br></div>

    <div style="width:1600px;">
        <div style="width:35%;float:left">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:100%;font-size:12px;" rules="all">
                <tbody>
                    <tr>
                        <td width="410" colspan="3">Total Price :</td>
                        <td width="80" align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="2">Commercial Expences :</td>
                        <td width=50 align="right">&nbsp; %</td>
                        <td width="80" align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Insurance &amp; Freight Charges :</td>
                        <td align="right">&nbsp</td>
                    </tr>
                    <tr>
                        <td colspan="3">Total FOB Price</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="2">With 5% Commision</td>
                        <td align=right>&nbsp %</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">CPM</td>
                        <td align="right">&nbsp</td>
                    </tr>
                    <tr>
                        <td colspan="3">SMV:</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">EPM Stands on:</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Line Manpower</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3" >Line Target/Hour</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Plan Effi: %</td>
                        <td align="right">&nbsp </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:30%;float:left">
            <table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:100%;font-size:12px;" rules="all">
                <tbody>
                    <tr>
                        <td rowspan="11" valign="top" style="padding-left:20px;">Notes: </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:35%;float:left">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:100%;font-size:12px;" rules="all">
                <tbody>
                    <tr>
                        <td width="490" colspan="4" align="center">Costing summary :</td>
                    </tr>
                    <tr>
                        <td width="410" colspan="3">Fabric cost</td>
                        <td width="80" align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Sewing Trim Cost</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Packing material cost</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Finishing Accessories</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">CM Per Pcs</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Commercial Expance</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Over head &amp; others</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Insurance &amp; Freight</td>
                        <td align="right">&nbsp </td>
                    </tr>
                    <tr>
                        <td colspan="3">Buying commission</td>
                        <td align="right"></td>
                    </tr>
                    <tr>
                        <td colspan="3">Total FOB/Pcs</td>
                        <td align="right">&nbsp </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

	<?
	exit();
}

if($action=="generate_report" && $type=="buyerSubmitSummery")
{
	//echo "Test Ok.................";die;
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $qoutation_id_cond=''; else $qoutation_id_cond=" and a.id=".$txt_quotation_id."";
	if($cbo_company_name=="") $company_name_cond=''; else $company_name_cond=" and a.company_id=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_id=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref_cond=''; else $txt_style_ref_cond=" and a.style_ref=".$txt_style_ref."";
	//if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";


	//array for display name
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	  $sql="select a.id  as quotation_id,a.gmts_item_id,a.company_id, a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id from wo_price_quotation a,wo_price_quotation_costing_mst b  where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_name_cond $txt_style_ref_cond  $qoutation_id_cond order  by a.id ";
			$sql_quot_result=sql_select($sql);
			$all_quot_id="";
			foreach($sql_quot_result as $row)
			{
				if($all_quot_id=="") $all_quot_id=$row[csf("quotation_id")]; else $all_quot_id.=",".$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref")]]['costing_per']=$row[csf("costing_per")];

				$style_wise_arr[$row[csf("style_ref")]]['gmts_item_id']=$row[csf("gmts_item_id")];
				$style_wise_arr[$row[csf("style_ref")]]['shipment_date'].=$row[csf('est_ship_date')].',';
				$style_wise_arr[$row[csf("style_ref")]]['quotation_id']=$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref")]]['buyer_name']=$row[csf("buyer_id")];
				$offer_qnty_pcs=$row[csf('offer_qnty')]*$row[csf('ratio')];
				$style_wise_arr[$row[csf("style_ref")]]['qty_pcs']+=$row[csf('offer_qnty')]*$row[csf('ratio')];
				$style_wise_arr[$row[csf("style_ref")]]['qty']+=$row[csf('offer_qnty')];
				$style_wise_arr[$row[csf("style_ref")]]['final_cost_pcs']=$row[csf('price_with_commn_pcs')];
				$style_wise_arr[$row[csf("style_ref")]]['total_cost']+=$offer_qnty_pcs*$row[csf('price_with_commn_pcs')];
				$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty']=$row[csf("offer_qnty")];
				$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id']=$row[csf("costing_per_id")];
				$quot_wise_arr[$row[csf("quotation_id")]]['quot_date']=$row[csf("quot_date")];
				//$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')];
			}

			$company_con="";
			if($cbo_company_name!=0) $company_con="and company_id=$cbo_company_name";
			//else if($cbo_style_owner!=0) $company_con="and company_id=$cbo_style_owner";
			//else if($cbo_company_name==0) $company_con=$cbo_company_name;
			$financial_para=array();
			$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date,applying_period_to_date,operating_expn from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 $company_con order by id");
			foreach($sql_std_para as $sql_std_row)
			{
				$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
				$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
				$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];

				$applying_period_date=change_date_format($sql_std_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($sql_std_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);

					$financial_para_arr[$newdate]['operating_expn']=$sql_std_row[csf('operating_expn')];
					if($sql_std_row[csf("income_tax")]>0)
					{
						$financial_para_arr[$newdate]['income_tax']=$sql_std_row[csf('income_tax')];
					}
					if($sql_std_row[csf("interest_expense")]>0)
					{
						$financial_para_arr[$newdate]['interest_expense']=$sql_std_row[csf('interest_expense')];
					}

					//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			//print_r($financial_para_arr);
				$all_quot_ids=array_unique(explode(",",$all_quot_id));


			$sql_fab = "select quotation_id,sum(avg_cons) as cons_qnty, sum(amount) as amount
			from wo_pri_quo_fabric_cost_dtls
			where quotation_id in(".$all_quot_id.") and status_active=1 and is_deleted=0 and fabric_source=2 group by  quotation_id";
			$data_array_fab=sql_select($sql_fab);
			foreach($data_array_fab as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$fab_order_price_per_dzn=12;}
				else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
				else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
				else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
				else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

				$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				 $fab_summary_data[$row[csf("quotation_id")]]['fab_amount_dzn']+=$row[csf("amount")];
				 $fab_summary_data[$row[csf("quotation_id")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
				//$yarn_amount_dzn+=$row[csf('amount')];
			}
			$sql_yarn = "select quotation_id,sum(cons_qnty) as cons_qnty, sum(amount) as amount
			from wo_pri_quo_fab_yarn_cost_dtls
			where quotation_id in(".$all_quot_id.") and status_active=1 group by  quotation_id";
			$data_array_yarn=sql_select($sql_yarn);
			foreach($data_array_yarn as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
				else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
				else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
				else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
				else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
				$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				 $yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn']+=$row[csf("amount")];
				// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
				 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
			}

			$conversion_cost_arr=array();
			$sql_conversion = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id and b.status_active=1 and b.is_deleted=0
			where a.quotation_id in(".$all_quot_id.") and a.status_active=1  ";
			$data_array_conversion=sql_select($sql_conversion);
			foreach($data_array_conversion as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$conv_order_price_per_dzn=12;}
				else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
				else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
				else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
				else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
				$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$summary_data['conversion_cost_dzn']+=$row[csf("amount")];
				//$summary_data['conversion_cost_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
				 $conv_summary_data[$row[csf("quotation_id")]]['conv_amount_dzn']+=$row[csf("amount")];

				$conversion_cost_arr[$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
				$conversion_cost_arr[$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
			}

			if($db_type==0)
			{
			$sql = "select MAX(id),quotation_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent,offer_qnty
					from wo_price_quotation_costing_mst
					where quotation_id in(".$all_quot_id.") and status_active=1 ";
			}
			if($db_type==2)
			{
			$sql = "select MAX(id),fabric_cost,quotation_id,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent
					from wo_price_quotation_costing_mst
					where quotation_id in(".$all_quot_id.") and status_active=1   group by fabric_cost,quotation_id,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent";
			}
			//echo $sql;
			$data_array=sql_select($sql);
			$order_price_summ=0;
			$less_commision_summ=0;
			$total_cost_summ=0;
			$margin_summ=0;
			$margin_percent_summ=0;
			$percent=0;
			$price_dzn=0;
            $sl=1;
            foreach( $data_array as $row )
            {
				//$sl=$sl+1;
				if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}

				else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				/*$commission_base_id=return_field_value("commission_base_id", "wo_pri_quo_commiss_cost_dtls", "quotation_id in($all_quot_id)");
				$commision_rate=return_field_value("sum(commision_rate)", "wo_pri_quo_commiss_cost_dtls", "quotation_id in($all_quot_id)");
				if($commission_base_id==1)
				{
					$commision=($commision_rate*$row[csf("confirm_price_dzn")])/100;
				}
				if($commission_base_id==2)
				{
					$commision=$commision_rate*$order_price_per_dzn;
				}
				if($commission_base_id==3)
				{
					$commision=($commision_rate/12)*$order_price_per_dzn;
				}*/
				//echo $row[csf("price_with_commn_dzn")].'='.$order_price_per_dzn.'='.$order_job_qnty.'<br>';
				$summary_data['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
				$summary_data['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['price_dzn']+=$row[csf("confirm_price_dzn")];
				//$summary_data[price_dzn_job]+=$job_wise_arr[$row[csf("job_no")]]['po_amount'];//($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			    $summary_data['commission_dzn']+=$row[csf("commission")];
				$summary_data['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['trims_cost_dzn']+=$row[csf("trims_cost")];
				$summary_data['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['embel_cost_dzn']+=$row[csf("embel_cost")];
				$summary_data['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("commission")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

				$summary_data['other_direct_dzn']+=$other_direct_expenses;
				$summary_data['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['wash_cost_dzn']+=$row[csf("wash_cost")];
				$summary_data['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['lab_test_dzn']+=$row[csf("lab_test")];
				$summary_data['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['inspection_dzn']+=$row[csf("inspection")];
				$summary_data['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['freight_dzn']+=$row[csf("freight")];
				$summary_data['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				$freight_cost_data[$row[csf("quotation_id")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
				$summary_data['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
				$summary_data['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
				$summary_data['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
				$summary_data['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$quot_studio_cost_dzn_arr[$row[csf("quotation_id")]]['studio_dzn_cost']=$row[csf("studio_percent")];
				$quot_studio_cost_dzn_arr[$row[csf("quotation_id")]]['common_oh']=$row[csf("common_oh")];

				$fab_amount_dzn=$fab_summary_data[$row[csf("quotation_id")]]['fab_amount_dzn'];
				$summary_data['fab_amount_dzn']+=$fab_amount_dzn;
				$summary_data['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				$yarn_amount_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
				$summary_data['yarn_amount_dzn']+=$yarn_amount_dzn;
				$summary_data['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				 $conv_amount_dzn=$conv_summary_data[$row[csf("quotation_id")]]['conv_amount_dzn'];
				 $summary_data['conversion_cost_dzn']+=$conv_amount_dzn;
				 $summary_data['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

				//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
				$net_value_dzn=$row[csf("price_with_commn_dzn")];

				$summary_data['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
				$summary_data['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

				//yarn_amount_total_value
				$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
				//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
				$summary_data['cost_of_material_service']+=$all_cost_dzn;
				$summary_data['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
				$summary_data['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
				$summary_data['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['cm_cost_dzn']+=$row[csf("cm_cost")];
				$summary_data['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['comm_cost_dzn']+=$row[csf("comm_cost")];
				$summary_data['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['common_oh_dzn']+=$row[csf("common_oh")];
				$summary_data['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
				//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
				$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
				$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
				$summary_data['gross_profit_dzn']+=$tot_gross_profit_dzn;
				$summary_data['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

				//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
				$operate_profit_loss_dzn=$tot_gross_profit_dzn-($row[csf("comm_cost")]+$row[csf("common_oh")]);
				$summary_data['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
				$summary_data['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
				$summary_data['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
				$summary_data['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
				$summary_data['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
				$summary_data['net_profit_dzn']+=$net_profit_dzn;
				$summary_data['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
			}
			$sql_commi = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls
			where  quotation_id in(".$all_quot_id.") and status_active=1";
			$result_commi=sql_select($sql_commi);$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
			foreach($result_commi as $row){

				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

				if($row[csf("particulars_id")]==1) //Foreign
					{
						$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
						$CommiData_foreign_quot_cost_arr[$row[csf("quotation_id")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						$local_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_local_quot_cost_arr[$row[csf("quotation_id")]]=$row[csf("commision_rate")];
					}
			}


			$sql_comm="select item_id,quotation_id,sum(amount) as amount from wo_pri_quo_comarcial_cost_dtls where  quotation_id in(".$all_quot_id.") and status_active=1 group by quotation_id,item_id";
			$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;$summary_data['comm_cost_dzn']=0;$summary_data['comm_cost_total_value']=0;
			$result_comm=sql_select($sql_comm);
			foreach($result_comm as $row){

			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				$comm_amtPri=$row[csf('amount')];
				$item_id=$row[csf('item_id')];
				if($item_id==1)//LC
					{
						$commer_lc_cost+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$tot_lc_dzn_Commer+=$row[csf("amount")];
						$commer_lc_cost_quot_arr[$row[csf("quotation_id")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						$commer_without_lc_cost+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$tot_without_lc_dzn_Commer+=$row[csf("amount")];
					}
			}
					$summary_data['comm_cost_dzn']=$tot_without_lc_dzn_Commer;
					$summary_data['comm_cost_total_value']=$commer_without_lc_cost;

					$summary_data['other_direct_total_value']+=$CommiData_lc_cost;
					$summary_data['other_direct_dzn']+=$local_dzn_commission_amount;

	?>
	<div style="width:950px;">

		 <div style="width:100%">
            <? $tot_operating_expense=$total_quot_income_tax_val=$total_quot_interest_exp_val=$total_quot_amount_val=$tot_qout_studio_cost=$total_quot_commision_local_val=$total_quot_net_amount_cal=0;
			foreach($all_quot_ids as $qid)
			{
				$quot_date=$quot_wise_arr[$qid]['quot_date'];
				$pri_quot_date=change_date_format($quot_date,'','',1);
				$freight_total_value=$freight_cost_data[$qid]['freight_total_value'];
				$total_quot_amount_cal=$total_quot_amount_arr[$qid];
				$studio_dzn_cost=$quot_studio_cost_dzn_arr[$qid]['studio_dzn_cost'];
				$common_oh_dzn_cost=$quot_studio_cost_dzn_arr[$qid]['common_oh'];
				$commision_quot_local=$commision_local_quot_cost_arr[$qid];
				//echo $studio_dzn_cost.'<br>';
				$CommiData_foreign_quot_cost=$CommiData_foreign_quot_cost_arr[$qid];
				$commer_lc_cost_quot=$commer_lc_cost_quot_arr[$qid];

				$operating_expn=0;
				$operating_expn=$financial_para_arr[$pri_quot_date]['operating_expn'];
				$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost+$commer_lc_cost_quot+$freight_total_value);
					//echo $total_quot_amount_cal.'='.$freight_total_value.',';
				$tot_operating_expense+=($tot_sum_amount_quot_calc*$operating_expn)/100;
				$tot_qout_studio_cost+=($tot_sum_amount_quot_calc*$studio_dzn_cost)/100;

				$income_tax=$financial_para_arr[$pri_quot_date]['income_tax'];
				$interest_expense=$financial_para_arr[$pri_quot_date]['interest_expense'];
				//echo $total_quot_amount_cal.'='.$commision_quot_local.',';
				$total_quot_income_tax_val+=($total_quot_amount_cal*$income_tax)/100;
				$total_quot_interest_exp_val+=($total_quot_amount_cal*$interest_expense)/100;
				$total_quot_commision_local_val+=($tot_sum_amount_quot_calc*$commision_quot_local)/100;
				$total_quot_amount_val+=$total_quot_amount_arr[$qid];
				$total_quot_net_amount_cal+=$tot_sum_amount_quot_calc;

			}
			//echo $tot_operating_expense;
			$tot_income_tax_dzn=($total_quot_income_tax_val/$total_quot_amount_val)*12;
			$tot_interest_exp_dzn=($total_quot_interest_exp_val/$total_quot_amount_val)*12;

			$tot_commision_local_dzn=($total_quot_commision_local_val/$total_quot_net_amount_cal)*12;
			//echo $total_quot_income_tax_val.'=='.$tot_income_tax_dzn;
			$summary_data['other_direct_total_value']+=$tot_qout_studio_cost;
			?>
			<div>
                         <table width="470px" style="margin-left:100px;" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  rules="all">
                         <strong style="color:red;"><? echo 'Under Construction';?></strong>
                         <caption> <strong><? echo 'Buyer Submit Quotation ';?></strong></caption>
                            <thead>
                                <th  align="center"><strong>SL</strong></th>
                                <th  align="center"><strong>Buyer Submit Quotation <br>(Auto mail Attached)</strong></th>
                                <th  align="center"><strong>Total Value</strong></th>
                                <th  align="center"><strong>%</strong></th>
                            </thead>
                            <tr>
                                 <td width="20" align="center">1</td>
								<td width="230" align="left">Company</td>
                                <td width="100" align="right"><? //echo number_format($summary_data['yarn_amount_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format( ($summary_data['yarn_amount_total_value']/$summary_data['price_with_total_value'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">2</td>
								<td width="230" align="left">Qtd Id</td>
                                <td width="100" align="right"><? //echo number_format( $summary_data['fab_amount_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format( ($summary_data['fab_amount_total_value']/$summary_data['price_with_total_value'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                               	 <td width="20" align="center">3</td>
							    <td width="230" align="left">Qtd Date</td>
                                <td width="100" align="right"><? //echo number_format( $tot_dye_chemi_process_amount,2); ?></td>
                                <td width="50"  align="right"><? //echo number_format( ($tot_dye_chemi_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">4</td>
								<td width="230" align="left">Ship Date</td>
                                <td width="100" align="right"><? //echo number_format( $tot_yarn_dye_process_amount,2); ?></td>
                                <td width="50"  align="right"><? //echo number_format( ($tot_yarn_dye_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">5</td>
								<td width="230" align="left">Buyer</td>
                                <td width="100" align="right"><? //echo number_format($tot_aop_process_amount,2); ?></td>
                                <td width="50"  align="right"><? //echo number_format(($tot_aop_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                            <tr>
                                 <td width="20" align="center">6</td>
								<td width="230" align="left">Style Ref:</td>
                                <td width="100" align="right"><? //echo number_format($summary_data['trims_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format( ($summary_data['trims_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                            <tr>
                                 <td width="20" align="center">7</td>
								<td width="230" align="left">Style Description</td>
                                <td width="100" align="right"><? //echo number_format($summary_data['comm_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format( ($summary_data['comm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">8</td>
								<td width="130" align="left">Product Dept</td>
                                <td width="100" align="right"><? //$tot_emblish_cost=$summary_data['embel_cost_total_value'];echo number_format($tot_emblish_cost,2); ?></td>
                                <td width="50"  align="right"><? //echo number_format(($tot_emblish_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">9</td>
							    <td width="230" align="left">Item</td>
                                <td width="100" align="right"><? //echo number_format($summary_data['lab_test_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format(($summary_data['lab_test_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							 <tr>
                                <td width="20" align="center">10</td>
							    <td width="230" align="left">Item Image</td>
                                <td width="100" align="right"><? //echo number_format($summary_data['common_oh_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format(($summary_data['common_oh_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							<tr>
                                <td width="20" align="center">11</td>
							    <td width="230" align="left">Order UOM</td>
                                <td width="100" align="right"><? //echo number_format($summary_data['studio_pre_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? //echo number_format(($summary_data['studio_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							  <tr>
                                 <td width="20" align="center">12</td>
								<td width="230" align="left">Fabrication</td>
                                <td width="100" align="right" title=""><?
								//$tot_inspect_cour_certi_cost=$summary_data['inspection_total_value']+$summary_data['currier_pre_cost_total_value']+$summary_data['certificate_pre_cost_total_value']+$summary_data['commission_total_value']+$summary_data['design_pre_cost_total_value'];
								//echo number_format($tot_inspect_cour_certi_cost,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($tot_inspect_cour_certi_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>

                             <tr>
                                 <td width="20" align="center">13</td>
								<td width="230" align="left">Offer Qty</td>
                                <td width="100" align="right" title="Lab Test+Emblish Cost+Cmmercial Cost+Trims Cost+Yarn Dye Process Cost+Dye Chemical+Yarn Cost+AOP Cost"><?
									//$total_btb=$summary_data['lab_test_total_value']+$tot_emblish_cost+$summary_data['comm_cost_total_value']+$summary_data['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data['common_oh_total_value']+$summary_data['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
								 //echo number_format($total_btb,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($total_btb/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">14</td>
								<td width="230" align="left">Final Cost (Asking Quoted Price)</td>
                                <td width="100" align="right" title="Tot Conversion Cost-(Y/D+Dye & Chemical+AOP)"><?
								$tot_cm_for_fab_cost=$summary_data['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
								//echo number_format($tot_cm_for_fab_cost,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($tot_cm_for_fab_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>


                             <tr>
                             <?
                             $total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);
							 ?>
                                 <td width="20" align="center">15</td>
								<td width="230" align="left">1st Qtd Price</td>
                                <td width="100" align="right" title="Gross FOB Value-Tot CM Fab Cost Cost-Total BTB-Inspect-Freight-Courier-Certificate-Commission"><?

								//echo number_format($total_cm_for_gmt,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($total_cm_for_gmt/$NetFOBValue_job)*100,2);?></td>
                            </tr>
                             <tr>
							  <td width="20" align="center">16</td>
                                <td width="230" align="left">2nd Revised Price</td>
                                <td width="100" align="right"><? //echo number_format($NetFOBValue_job,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($NetFOBValue_job/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                            <tr>
							  <td width="20" align="center">17</td>
                                <td width="230" align="left">Buyer Target Price</td>
                                <td width="100" align="right"><? //echo number_format($NetFOBValue_job,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($NetFOBValue_job/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                            <tr>
							  <td width="20" align="center">18</td>
                                <td width="230" align="left">Offer Price (2nd Revised Price)</td>
                                <td width="100" align="right"><? //echo number_format($NetFOBValue_job,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($NetFOBValue_job/$summary_data['price_with_total_value'])*100,2);?></b></td>
                            </tr>
                            <tr>
							  <td width="20" align="center">19</td>
                                <td width="230" align="left">Payment Mode (New Customization)</td>
                                <td width="100" align="right"><? //echo number_format($NetFOBValue_job,2);?></td>
                                <td width="50"  align="right"><? //echo number_format(($NetFOBValue_job/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                             <tr>
                             <td colspan="4" style="border:hidden">&nbsp;</td>
                             </tr>
                        </table>
                        <br/>
                        <div id="" style="width:870px;">
                         <br/><br/>
				    <?
                      		echo signature_table(109, $cbo_company_name, "870px");
                        ?>
                        </div>
						</div>
	</div>
	<?
	exit();
}

function fnc_smv_style_integration($db_type,$cbo_company_name,$update_id,$data_int,$sewSmv,$cutSmv,$page)
{
	if($page==2)
	{
		$company_id=str_replace("'","",$cbo_company_name);
		$update_id=str_replace("'","",$update_id);
		$ex_data=explode("****",$data_int);
		$currercy=str_replace("'","",$ex_data[0]);
		$set_breck_down_arr=explode('__',str_replace("'",'',$ex_data[1]));
		$item_wise_arr=array(); $itm_arr=array();
		for($c=0; $c<count($set_breck_down_arr); $c++)
		{
			$set_breck_downdata_arr=explode('_',$set_breck_down_arr[$c]);
			$itm_arr[]=$set_breck_downdata_arr[0];
			$item_wise_arr[$set_breck_downdata_arr[0]]['ratio']=$set_breck_downdata_arr[1];
			$item_wise_arr[$set_breck_downdata_arr[0]]['smv_pcs']=$set_breck_downdata_arr[2];
			$item_wise_arr[$set_breck_downdata_arr[0]]['smv_set']=$set_breck_downdata_arr[3];
		}
		//print_r($itm_arr);
		$gmts_item=str_replace("'","",$ex_data[1]);
		$sewSmv=str_replace("'","",$sewSmv);
		$cutSmv=str_replace("'","",$cutSmv);
		if($db_type==0) $job_concat="group_concat(job_no)";
		else if($db_type==2) $job_concat="listagg((cast(job_no as varchar2(4000))),',') within group (order by job_no)";
		//return "select $job_concat as job_no from wo_po_details_master where quotation_id='$update_id' and company_name='$company_id' and currency_id='$currercy' and is_deleted=0 and status_active=1"; die;
		$pq_mapping_id=return_field_value("$job_concat as job_no", "wo_po_details_master", "quotation_id='$update_id' and company_name='$company_id' and currency_id='$currercy' and is_deleted=0 and status_active=1","job_no");
		//return $currercy.'='; die;
		//return $pq_mapping_id.'='.$update_id.'='.$gmts_item.'='.$currercy.'='.$sewSmv.'='.$cutSmv.'='.$page; die;

		if($pq_mapping_id!='')
		{
			$job_no_all=array_unique(explode(",",$pq_mapping_id));
			$job_str="";
			foreach($job_no_all as $job)
			{
				if($job_str=="") $job_str="'".$job."'"; else $job_str.=",'".$job."'";
			$txt_job_no=$job;
			/*return "select a.id, a.job_no, a.gmts_item_id, a.set_item_ratio, a.smv_pcs, a.smv_set, a.smv_pcs_precost, a.smv_set_precost, a.complexity, a.embelishment, a.cutsmv_pcs, a.cutsmv_set, a.finsmv_pcs, a.finsmv_set, a.printseq, a.embro, a.embroseq, a.wash, a.washseq, a.spworks, a.spworksseq, a.gmtsdying, a.gmtsdyingseq, a.quot_id, b.set_break_down, b.total_set_qnty, b.set_smv, b.company_name, currency_id from wo_po_details_mas_set_details a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in ( $job_str ) and b.is_deleted=0 and b.status_active=1"; die;*/
			$wo_po_set=sql_select("select a.id, a.job_no, a.gmts_item_id, a.set_item_ratio, a.smv_pcs, a.smv_set, a.smv_pcs_precost, a.smv_set_precost, a.complexity, a.embelishment, a.cutsmv_pcs, a.cutsmv_set, a.finsmv_pcs, a.finsmv_set, a.printseq, a.embro, a.embroseq, a.wash, a.washseq, a.spworks, a.spworksseq, a.gmtsdying, a.gmtsdyingseq, a.quot_id, b.set_break_down, b.total_set_qnty, b.set_smv, b.company_name, currency_id from wo_po_details_mas_set_details a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in ( $job_str ) and b.is_deleted=0 and b.status_active=1");

			$set_breck_down_sql=""; $job_arr=array(); $break_down_data='';
			$job_data_arr=array(); $add=0; $add_set=0;
			foreach($wo_po_set as $row)
			{
				if($row[csf("cutsmv_pcs")]=='') $row[csf("cutsmv_pcs")]=0;
				if($row[csf("cutsmv_set")]=='') $row[csf("cutsmv_set")]=0;
				if($row[csf("finsmv_pcs")]=='') $row[csf("finsmv_pcs")]=0;
				if($row[csf("finsmv_set")]=='') $row[csf("finsmv_set")]=0;

				if($row[csf("printseq")]=='') $row[csf("printseq")]=1;
				if($row[csf("embroseq")]=='') $row[csf("embroseq")]=2;
				if($row[csf("washseq")]=='') $row[csf("washseq")]=3;
				if($row[csf("spworksseq")]=='') $row[csf("spworksseq")]=4;
				if($row[csf("gmtsdyingseq")]=='') $row[csf("gmtsdyingseq")]=5;
				$smv_set=0; $smv=0;
				if(in_array($row[csf("gmts_item_id")],$itm_arr))
				{
					//if($row[csf("gmts_item_id")]==$gmts_item) $smv=$sewSmv;
					$smvpcs=$item_wise_arr[$row[csf("gmts_item_id")]]['smv_pcs'];
					$smvset=$item_wise_arr[$row[csf("gmts_item_id")]]['smv_set'];
					$smv_set=$smvpcs*$item_wise_arr[$row[csf("gmts_item_id")]]['ratio'];
				}
				else
				{
					$smvpcs=$row[csf("smv_pcs")];
					$smvset=$row[csf("smv_set")];
					$smv_set=$row[csf("smv_set")]*$row[csf("set_item_ratio")];
				}

				$pre_smv=$row[csf("total_set_qnty")]*$row[csf("smv_pcs")];
				//$smv_set=$row[csf("set_smv")]*$row[csf("set_item_ratio")];
				$jobset_smv=$smv_set;
				//echo $row[csf("set_smv")]."=".$smv_set;

				if(!in_array($row[csf('job_no')],$job_arr))
				{
					$add=0;
					$job_arr[]=$row[csf('job_no')];
					$break_down_data='';
				}
				//echo $k; //die;
				if ($add!=0) $break_down_data.="__";
				$break_down_data.=$row[csf("gmts_item_id")].'_'.$row[csf("set_item_ratio")].'_'.$smvpcs.'_'.$smvset.'_'.$row[csf("complexity")].'_'.$row[csf("embelishment")].'_'.$row[csf("cutsmv_pcs")].'_'.$row[csf("cutsmv_set")].'_'.$row[csf("finsmv_pcs")].'_'.$row[csf("finsmv_set")].'_'.$row[csf("printseq")].'_'.$row[csf("embro")].'_'.$row[csf("embroseq")].'_'.$row[csf("wash")].'_'.$row[csf("washseq")].'_'.$row[csf("spworks")].'_'.$row[csf("spworksseq")].'_'.$row[csf("gmtsdying")].'_'.$row[csf("gmtsdyingseq")].'_'.$row[csf("quot_id")];
				$add++;

				$job_data_arr[$row[csf('job_no')]]['str']=$break_down_data;//explode("*",("'".$break_down_data."'*'".$jobset_smv."'"));
				$job_data_arr[$row[csf('job_no')]]['smv']+=$smv_set;

				if ($add_set!=0) $set_breck_down_sql.="***";
				$set_breck_down_sql.=$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("smv_pcs_precost")].'**'.$row[csf("smv_set_precost")].'**'.$row[csf("quot_id")].'**'.$row[csf("job_no")].'**'.$smv_set.'**'.$smvpcs.'**'.$jobset_smv;
				$add_set++;
			}

			//return $set_breck_down_sql; die;
			//print_r($job_data_arr); die;

			$field_arr_set="smv_pcs*smv_set*smv_pcs_precost*smv_set_precost";
			$set_breck_down_array=explode('***',str_replace("'",'',$set_breck_down_sql));
			for($c=0; $c < count($set_breck_down_array); $c++)
			{
				$set_breck_downins_arr=explode('**',$set_breck_down_array[$c]);
				$idSet_arr[]=$set_breck_downins_arr[0];

				$data_arr_set[$set_breck_downins_arr[0]] =explode("*",("'".$set_breck_downins_arr[10]."'*'".$set_breck_downins_arr[9]."'*'".$set_breck_downins_arr[10]."'*'".$set_breck_downins_arr[9]."'"));
			}
			$update_pq_to_ord=execute_query(bulk_update_sql_statement("wo_po_details_mas_set_details", "id",$field_arr_set,$data_arr_set,$idSet_arr ));

			$field_arr_job="set_break_down*set_smv";
			foreach($job_data_arr as $jobno=>$data)
			{
				execute_query( "update wo_po_details_master set set_break_down='".$data['str']."', set_smv='".$data['smv']."' where  job_no ='".$jobno."'",0);
			}
			//print_r($cbo_company_name);
			//echo bulk_update_sql_statement("wo_po_details_master", "job_no",$field_arr_job,$data_arrjob,$jobSet_arr );



			$is_pre_cost="";
			//echo "select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1";die;
			$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
			$cm_cost=0;

			$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
			$txt_sew_smv=str_replace("'","",$sewSmv)*1;//$pre_cost_data[0][csf("sew_smv")];
			$txt_cut_smv=$pre_cost_data[0][csf("cut_smv")];
			$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
			$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
			//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);

			$cbo_currercy=str_replace("'","",$currercy);
			$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
			$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
			$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
			$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
			$costing_date= $pre_cost_data[0][csf("costing_date")];
			//var txt_job_no= document.getElementById('txt_job_no').value;

			$cbo_costing_per_value=0;
			if($cbo_costing_per==1) $cbo_costing_per_value=12;
			else if($cbo_costing_per==2) $cbo_costing_per_value=1;
			else if($cbo_costing_per==3) $cbo_costing_per_value=24;
			else if($cbo_costing_per==4) $cbo_costing_per_value=36;
			else if($cbo_costing_per==5) $cbo_costing_per_value=48;

			$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name='$company_id' and variable_list=22 and status_active=1 and is_deleted=0");
			if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;

			if($cm_cost_method_based_on==1)
			{
				if($costing_date=="" || $costing_date==0)
				{
					if($db_type==0) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");
					else if($db_type==2) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
				}
				else
				{
					if($db_type==0) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
					else if($db_type==2) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
			}
			else if($cm_cost_method_based_on==2)
			{
				$min_shipment_sql=sql_select("select job_no_mst, min(shipment_date) as min_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$min_shipment_date="";
				foreach($min_shipment_sql as $row){ $min_shipment_date=$row[csf('min_shipment_date')]; }
				if($db_type==0) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==3)
			{
				$max_shipment_sql=sql_select("select job_no_mst, max(shipment_date) as max_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$max_shipment_date="";
				foreach($max_shipment_sql as $row){ $max_shipment_date=$row[csf('max_shipment_date')]; }

				if($db_type==0) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==4)
			{
				$max_shipment_sql=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$min_pub_shipment_date="";
				foreach($max_shipment_sql as $row){ $min_pub_shipment_date=$row[csf('min_pub_shipment_date')]; }

				if($db_type==0) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==4)
			{
				$max_shipment_sql=sql_select("select job_no_mst, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$max_pub_shipment_date="";
				foreach($max_shipment_sql as $row){ $max_pub_shipment_date=$row[csf('max_pub_shipment_date')]; }

				if($db_type==0) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
			}

			$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0; $depreciation_amorti=0; $operating_expn=0;
			$limit="";
			if($db_type==0) $limit="LIMIT 1"; else if($db_type==2) $limit="";
			$sqlstnd_cm="select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$cbo_company_name and '$txt_costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit";
			$sqlstnd_cm_arr=sql_select($sqlstnd_cm);
			foreach ($sqlstnd_cm_arr as $row)
			{
				if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
				if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
				if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
				if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
				if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
				if($row[csf("operating_expn")] !="")$operating_expn=$row[csf("operating_expn")];
			}
			//$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn;

			$sql_pre_cost_dtls="select max(cm_cost) as cm_cost, sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
			$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
			$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0; $prev_cm_cost=0;

			$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
			$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
			$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
			$prev_cm_cost=$sql_pre_cost_dtls_arr[0][csf("cm_cost")]*1;

			if (count($pre_cost_data)>0)
			{
				execute_query( "update wo_pre_cost_mst set sew_smv='$txt_sew_smv', cut_smv='$txt_cut_smv' where job_no ='".$txt_job_no."'",1);

				if($cm_cost_predefined_method_id==1)
				{
					$txt_efficiency_wastage=100-$txt_sew_efficiency_per;
					//document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
					$cm_cost=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)+(($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)*($txt_efficiency_wastage/100));
					//alert(txt_exchange_rate)
					$cm_cost=$cm_cost/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==2)
				{
					$cu=0; $su=0;
					$cut_per=$txt_cut_efficiency_per/100;
					$sew_per=$txt_sew_efficiency_per/100;
					$cu=($txt_cut_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($cut_per*1);
					if($cu=="") $cu=0;

					$su=($txt_sew_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($sew_per*1);
					if($su=='') $su=0;
					$cm_cost=($cu+$su)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==3)
				{
					//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
					$per_day_cost=$monthly_cm_expense/26;
					$per_machine_cost=$per_day_cost/$no_factory_machine;
					$per_line_cost=$per_machine_cost*$txt_machine_line;
					$total_production_per_line=$txt_prod_line_hr*$working_hour;
					$per_product_cost=$per_line_cost/$total_production_per_line;

					$cm_cost=($per_product_cost*$cbo_costing_per_value)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==4)
				{
					$sew_per=$txt_sew_efficiency_per/100;
					$su=((trim(($cost_per_minute*1))/$sew_per)*($txt_sew_smv*$cbo_costing_per_value));
					$cm_cost=$su/$txt_exchange_rate;
				}
				else
				{
					$cm_cost=$prev_cm_cost;
				}

				$dec_type=0;
				if (str_replace("'","",$currercy)==1) $dec_type=4; else $dec_type=5;

				$cm_cost=number_format($cm_cost,4,'.','');
				$cm_cost_per=number_format((($cm_cost/$price_dzn)*100),2,'.','');

				$tot_cost=number_format(($prev_tot_cost+$cm_cost),4,'.','');
				$tot_cost_per=number_format((($tot_cost/$price_dzn)*100),2,'.','');

				$margin_dzn=number_format(($price_dzn-$tot_cost),4,'.','');
				$margin_dzn_per=number_format((100-$tot_cost_per),2,'.','');

				$cost_pcs_set=number_format(($tot_cost/$cbo_costing_per_value),4,'.','');
				$cost_pcs_set_percent=number_format((($cost_pcs_set/$price_pcs_set)*100),2,'.','');

				$margin_pcs_set=number_format(($price_pcs_set-$cost_pcs_set),4,'.','');
				$margin_pcs_set_per=number_format((100-$cost_pcs_set_percent),2,'.','');


				$field_arr_pre_cost="cm_cost*cm_cost_percent*total_cost*total_cost_percent*margin_dzn*margin_dzn_percent*cost_pcs_set*cost_pcs_set_percent*margin_pcs_set*margin_pcs_set_percent";
				$data_arr_pre_cost="'".$cm_cost."'*'".$cm_cost_per."'*'".$tot_cost."'*'".$tot_cost_per."'*'".$margin_dzn."'*'".$margin_dzn_per."'*'".$cost_pcs_set."'*'".$cost_pcs_set_percent."'*'".$margin_pcs_set."'*'".$margin_pcs_set_per."'";
				//echo $field_arr_pre_cost."==".$data_arr_pre_cost;
				$rID2=sql_update("wo_pre_cost_dtls",$field_arr_pre_cost,$data_arr_pre_cost,"job_no","'".$txt_job_no."'",1);
			}
			else
			{
				//return;
			}
		}
		}
		//return $field_arr_pre_cost.'='.$data_arr_pre_cost;
	}
}

if($action=="validate_is_job_pre_cost")
{
	$ready_to_approved=return_field_value("ready_to_approved", "wo_price_quotation", "id='$data'");
	$job_no="";
	if($ready_to_approved==1)
	{
		$job_sql="select a.job_no from wo_po_details_master a, wo_pre_cost_mst b where a.job_no=b.job_no and a.quotation_id='$data' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";// and (b.approved=1 || b.approved=3)
		$job_sql_res=sql_select($job_sql);
		foreach($job_sql_res as $row)
		{
			if($job_no=="") $job_no=$row[csf("job_no")]; else $job_no.=', '.$row[csf("job_no")];
		}
		if (count($job_sql_res)>0) echo "1***".$job_no; else echo "0***".$job_no;
	}
	else echo "0***".$job_no;
	die;
}
if($action=="is_manual_approved")
{
    $is_manula_approved=0;
    $sql_data=sql_select("select price_quo_approval from variable_order_tracking where company_name='$data' and variable_list=61 and status_active=1 and is_deleted=0");
    foreach($sql_data as $sql_row)
    {
        $is_manula_approved=$sql_row[csf('price_quo_approval')];
    }
    echo $is_manula_approved;
    exit();
}

if($action=="body_part_popup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id, name,type)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		document.getElementById('gtype').value=type;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="gname" name="gname"/>
        <input type="hidden" id="gtype" name="gtype"/>
        <?
        $sql_tgroup=sql_select( "select body_part_full_name, body_part_short_name, body_part_type, id from lib_body_part where is_deleted=0 and status_active=1 order by body_part_short_name ASC");
        ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th>
                <th width="300">Item Group</th>
                <th>Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
            <?
            $i=1;
            foreach($sql_tgroup as $row_tgroup)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr onClick="js_set_value(<? echo $row_tgroup[csf('id')]; ?>, '<? echo $row_tgroup[csf('body_part_full_name')]; ?>', '<? echo $row_tgroup[csf('body_part_type')]; ?>')" bgcolor="<? echo $bgcolor; ?>">
					<td width="40"><? echo $i; ?></td>
                    <td width="300"><? echo $row_tgroup[csf('body_part_full_name')]; ?></td>
                    <td><? echo $body_part_type[$row_tgroup[csf('body_part_type')]]; ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
        </table>
        </div>
	</body>
	<script>
	setFilterGrid('item_table',-1)
	</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if ($action=="unapp_request_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$quotation_id=$data_all[0];
	$unapp_request=$data_all[1];

	if($unapp_request=="")
	{
		$sql_request="select MAX(id) as id from fabric_booking_approval_cause where entry_form=10 and user_id='$user_id' and booking_id='$quotation_id' and approval_type=2 and status_active=1 and is_deleted=0";//page_id='$menu_id' and

		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row)
		{
			$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
		}
	}
	?>
    <script>

		$( document ).ready(function() {
			document.getElementById("unappv_request").value='<? echo preg_replace('/[\r\n]+/','\n',$unapp_request); ?>';
		});

		var permission='<? echo $permission; ?>';

		function fnc_appv_entry(operation)
		{
			var unappv_request = $('#unappv_request').val();

			if (form_validation('unappv_request','Un Approval Request')==false)
			{
				if (unappv_request=='')
				{
					alert("Please write request.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*quotation_id*page_id*user_id',"../../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","quotation_entry_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
			}
		}

		function fnc_close()
		{
			unappv_request= $("#unappv_request").val();
			document.getElementById('hidden_appv_cause').value=unappv_request;
			parent.emailwindow.hide();
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="quotation_id" class="text_boxes" ID="quotation_id" value="<? echo $quotation_id; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_unappv_request")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=10 and mst_id=$quotation_id","approved_no")+1;

		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","entry_form=10 and user_id=$user_id and booking_id=$quotation_id and approval_type=2 and approval_no='$approved_no'");//page_id=$page_id and

		if($unapproved_request=="")
		{
			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",10,".$user_id.",".$quotation_id." ,2,'".$approved_no."',".$unappv_request.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			//echo "10**";
			//echo "INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			//echo $rID; die;

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$quotation_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "0**".$rID."**".str_replace("'","",$quotation_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*10*".$user_id."*".$quotation_id."*2*'".$approved_no."'*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID."**".str_replace("'","",$quotation_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$quotation_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}

			disconnect($con);
			die;
		}
	}
	else if ($operation==1)  // Update Here
	{

	}
}
?>