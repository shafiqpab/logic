<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$userCredential = sql_select("SELECT unit_id as company_id, brand_id FROM user_passwd where id=$user_id");

$brand_id = $userCredential[0][csf('brand_id')];
$brand_cond="";

if ($brand_id !='') {
    $brand_cond = " and id in ( $brand_id)";
}

if($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 140, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where b.team_id='$data' and a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "--Select Merchant--", $selected, "" );
	exit();
}

if($action=="load_drop_down_sample_marchant")
{
	echo create_drop_down( "cbo_concern_marchant", 140, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where b.team_id='$data' and a.id=b.team_id and  a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "--Select Merchant--", $selected, "" );
	exit();
}
 
if($action=="company_wise_report_button_setting")
{
	//echo $data;
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=277 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==115){echo "$('#print').show();\n";}
			if($id==116){echo "$('#print2').show();\n";}
		}
	}
	exit();
}

//---------------------------------------------------- Start---------------------------------------------------------------------------
if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select color_from_library from variable_order_tracking where company_name=$data and variable_list = 23 and status_active=1 and is_deleted=0 order by variable_list ASC");
	$color_from_lib=0;
 	foreach($sql_result as $result)
	{
		$color_from_lib=$result[csf('color_from_library')];
	}
	echo $color_from_lib;
 	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var value = data.split('_')
			document.getElementById('color_name').value=value[1];
			document.getElementById('color_id').value=value[0];
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <input type="hidden" id="color_id" name="color_id" />

            <?
            if($buyer_name=="" || $buyer_name==0)
            {
            	$sql="select id, color_name FROM lib_color  WHERE status_active=1 and is_deleted=0";
            }
            else
            {
            	$sql="select a.id, a.color_name FROM lib_color a, lib_color_tag_buyer b WHERE a.id=b.color_id and b.buyer_id=$buyer_name and status_active=1 and is_deleted=0";
            }
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "id,color_name", "", 1, "0", $arr , "color_name", "",'setFilterGrid("list_view",-1);','0') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if ($action=="load_drop_down_season_com")
{
	 $season_mandatory_arr=sql_select( "select id, season_mandatory, company_name from variable_order_tracking where company_name='$data' and variable_list=44 order by id" );
	if($season_mandatory_arr[0]['season_mandatory'] == 1)
	{
		echo create_drop_down( "cbo_season_name", 70, "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ","id,season_name", 1, "-Season-", $selected,"" );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", 70, " ","", 1, "-Season-", $selected,"" );
	}
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	$buyer_cond = "";
	if(!empty($datas[0]))
	{
		$buyer_cond = " and buyer_id='$datas[0]'";
	}
	echo create_drop_down( "cbo_season_name", 140, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0 $buyer_cond order by season_name ASC","id,season_name", 1, "-Season-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	list($buyer_id,$width)=explode('_',$data);
	$width=($width)?$width:140;
	$buyer_cond = "";
	if(!empty($buyer_id))
	{
		$buyer_cond = " and buyer_id='$buyer_id'";
	}
	$sql = "select id, brand_name from lib_buyer_brand where  status_active =1 and is_deleted=0 $buyer_cond  order by brand_name ASC";
	//echo $sql;
	echo create_drop_down( "cbo_brand", $width, $sql,"id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_change_brand")
{
	list($buyer_id,$selected)=explode('**',$data);
	echo create_drop_down( "cbo_change_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}


if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($data[0] == 1) {
			echo create_drop_down("cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond group by comp.id, comp.company_name order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 0);
		} else if ($data[0] == 2) {
			echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/buyer_inquery_for_textile_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/buyer_inquery_for_textile_controller', this.value, 'load_drop_down_brand', 'brand_td');", 0);

		}
	}
	exit();
}


if($action=="check_style_ref")
{
	$data=explode("**",$data);
	$style_ref=trim($data[0]);
	$id=trim($data[1]);
	$cond="";
	if($id!="") $cond= " and id !=$id"; else $cond="";
	
	//echo "select id from wo_quotation_inquery where  style_refernce='$style_ref' $cond and status_active=1 and is_deleted=0 order by id";
	$idArr=array();
	$sql=sql_select("select id from wo_quotation_inquery where style_refernce='$style_ref' $cond and status_active=1 and is_deleted=0 and entry_form=434 order by id");
	foreach($sql as $row){
		$idArr[$row[csf('id')]]=$row[csf('id')];
	}
	if(count($idArr)){
		echo "1**".implode(",",$idArr);
	}else{
		echo "0**";
	}
	exit();
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
	$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
?>

<script>
	function js_set_value(mrr)
	{
 		$("#hidden_issue_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="700" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
            </tr>
            <tr>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Inquiry ID</th>
                <th width="100">Season</th>
                <th width="80">Season Year</th>
                <th width="100">Brand</th>
                <th width="150">M.Style Ref/Name.</th>
                <th width="100">Buyer Inquiry No</th>
                <th width="100">Inquiry Date </th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1); ?></td>
                <td><? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'buyer_inquery_for_textile_controller', this.value, 'load_drop_down_season_buyer', 'season_td'); load_drop_down( 'buyer_inquery_for_textile_controller', this.value+'_70', 'load_drop_down_brand', 'brand_td'); " ); ?></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
                
                <td id="season_td"><? echo create_drop_down( "cbo_season_name", 70, $season_buyer_wise_arr,"", 1, "- Select- ", "", "" ); ?></td>
                <td><? echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ","", "" ); ?></td>
                <td id="brand_td"><? echo create_drop_down( "cbo_brand", 70, $brandArr,"", 1, "- Select- ", "", "" ); ?></td>
                
                <td><input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('cbo_brand').value, 'create_mrr_search_list_view', 'search_div', 'buyer_inquery_for_textile_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="7"><input type="hidden" id="hidden_issue_number" value="" /></td>
            </tr>
        </tbody>
    </table>
    <div align="center" valign="top" id="search_div"> </div>
    </form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
	$season_id = $ex_data[8];
	$brand_id = $ex_data[9];
	$season_year = $ex_data[5];
	
		
	if($season_year>0){$year_cond=" and SEASON_YEAR=$season_year";}
	
	if($season_id==0) $seson_con=""; else $seson_con=" and SEASON=$season_id";
	if($brand_id==0) $brand_con=""; else $brand_con=" and BRAND_ID=$brand_id";
	
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	
	//if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	//if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	
	
	if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";

	$sql_cond='';
	$inquery_id_cond='';
	$request_no='';
	if($ex_data[7]==1)
		{

		   if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
		   if (trim($ex_data[4])!="")  $inquery_id_cond=" and system_number_prefix_num='$ex_data[4]'  $year_cond";
		   if (trim($ex_data[6])!="") $request_no=" and buyer_request='$ex_data[6]'";
		}

	if($ex_data[7]==4 || $ex_data[7]==0)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
		}

	if($ex_data[7]==2)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' ";
		}

	if($ex_data[7]==3)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' ";
		}
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
	
	$arr=array(0=>$company_arr,1=>$buyer_arr,3=>$season_buyer_wise_arr,9=>$brandArr,10=>$row_status);
	 $sql = "select system_number_prefix_num,system_number,buyer_request, company_id,buyer_id,season,inquery_date,style_refernce,status_active,extract(year from insert_date) as year, season_year, id, brand_id from wo_buyer_inquery where is_deleted=0 $company_name $buyer_name $sql_cond $where_con $inquery_id_cond $request_no $inquery_date $year_cond $seson_con $brand_con order by id DESC ";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquiry ID,Season,Season Year,Buyer Inquiry No,M.Style Ref/Name., Inquiry Date,Brand,Status","120,120,70,80,60,80,110,80,80,100","1050","260",0, $sql , "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,0,season,0,0,0,0,brand_id,status_active", $arr, "company_id,buyer_id,system_number_prefix_num,season,season_year,buyer_request,style_refernce,inquery_date,brand_id,status_active", "",'','0,0,0,0,0,0,0,0,3,0,0') ;
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_data")
{
	$data=explode("**",$data);
	//txt_buyer_submit_price*txt_buyer_target_price
	$sql="select  id, system_number_prefix, system_number_prefix_num, system_number, company_id,within_group,buyer_id,style_refernce,style_description,inquery_date,season, season_year, brand_id, buyer_request,team_leader,dealing_marchant,est_ship_date,req_quotation_date,target_sam_sub_date,actual_sam_send_date,actual_req_quot_date,priority,concern_marchant,age_range,end_use,wash,light_source,remarks,insert_by, insert_date, status_active, is_deleted ,attention,sales_account,ready_to_approved,approved from WO_BUYER_INQUERY where system_number='$data[0]'  order by id";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	
	foreach($sql_result as $row)
	{
		
		
		
		$text_fab=chop($text_fab,",");
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value = '".$row[csf("within_group")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('txt_inquery_date').value = '".change_date_format($row[csf("inquery_date")],"dd-mm-yyyy","-")."';\n";
		echo "load_drop_down( 'requires/buyer_inquery_for_textile_controller','".$row[csf("buyer_id")].'_'.$row[csf("company_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		echo "load_drop_down( 'requires/buyer_inquery_for_textile_controller','".$row[csf("team_leader")]."', 'load_drop_down_dealing_merchant', 'div_marchant') ;";
		echo "load_drop_down( 'requires/buyer_inquery_for_textile_controller','".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_request_no').value = '".$row[csf("buyer_request")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_req_quot_date').value = '".change_date_format($row[csf("req_quotation_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_target_samp_date').value = '".change_date_format($row[csf("target_sam_sub_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_actual_sam_send_date').value = '".change_date_format($row[csf("actual_sam_send_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_actual_req_quot_date').value = '".change_date_format($row[csf("actual_req_quot_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('cbo_priority').value = '".$row[csf("priority")]."';\n";
		echo "document.getElementById('cbo_concern_marchant').value = '".$row[csf("concern_marchant")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_sales_account').value = '".$row[csf("sales_account")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_approved').value = '".$row[csf("approved")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		$ap_msg="";
		if($row[csf("approved")]==1){
			$ap_msg="This Inquiry Is Approved.";

		}

		echo "document.getElementById('app_sms').innerHTML = '".$ap_msg."';\n";
	}
	
	exit();
}

if($action == "populate_data_from_determination")
{
	$data=explode("_",$data);
	$determination_id = $data[0];
	$sl_no = $data[1];
	//txt_buyer_submit_price*txt_buyer_target_price
	 $sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id = $determination_id and  a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=581 order by a.id,b.id";
	$data_array=sql_select($sql);
	$composition_arr = array();
	$composition_ids = array();
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
            $composition_ids[$row[csf('id')]][$row[csf('bid')]] = $row[csf('bid')];
        }
	}
	$sql="select id, fab_nature_id, type, construction,fabric_construction_id, gsm_weight, weight_type, design, rd_no, color_range_id, entry_form, full_width, cutable_width,shrinkage_l,shrinkage_w from  lib_yarn_count_determina_mst   where  entry_form=581 and id = $determination_id ";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	
	foreach($sql_result as $row)
	{

		$composition_ids = implode(",",$composition_ids[$row[csf('id')]]);
		$composition_name = $composition_arr[$row[csf('id')]];
		echo "document.getElementById('txtcompone_".$sl_no."').value 		= '".$composition_name."';\n";
		echo "document.getElementById('cbocompone_".$sl_no."').value 		= '".$composition_ids."';\n";
		echo "document.getElementById('txtWeaveDesign_".$sl_no."').value 	= '".$row[csf('design')]."';\n";
		echo "document.getElementById('txtFabricWeight_".$sl_no."').value 	= '".$row[csf('gsm_weight')]."';\n";
		echo "document.getElementById('cboweighttype_".$sl_no."').value 	= '".$row[csf('weight_type')]."';\n";
		echo "document.getElementById('txtFinishedWidth_".$sl_no."').value 	= '".$row[csf('full_width')]."';\n";
		echo "document.getElementById('txtCutableWidth_".$sl_no."').value 	= '".$row[csf('cutable_width')]."';\n";
		
		
	}
	
	exit();
}

if ($action=="save_update_delete")
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

		$id=return_next_id( "id", "wo_buyer_inquery", 1 ) ;
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'AR', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from wo_buyer_inquery where company_id=$cbo_company_name and extract(year from insert_date)=".date('Y',time())." order by id desc ", "system_number_prefix", "system_number_prefix_num" ));
		$season=str_replace("'","",$cbo_season_name);
		$txt_light_source=str_replace("'","",$txt_light_source);
		$txt_style_description=str_replace("'","",$txt_style_description);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_remarks=str_replace("'","",$txt_remarks);
		$txt_request_no=str_replace("'","",$txt_request_no);
		$txt_attention=str_replace("'","",$txt_attention);
		$txt_sales_account=str_replace("'","",$txt_sales_account);
		$field_array="id, system_number_prefix, system_number_prefix_num, system_number, company_id,within_group,buyer_id,style_refernce,style_description,inquery_date,season, season_year, brand_id, buyer_request,team_leader,dealing_marchant,est_ship_date,req_quotation_date,target_sam_sub_date,actual_sam_send_date,actual_req_quot_date,priority,concern_marchant,remarks,attention,sales_account,ready_to_approved,insert_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",".$cbo_within_group.",".$cbo_buyer_name.",'".$txt_style_ref."','".$txt_style_description."',".$txt_inquery_date.",'".$season."',".$cbo_season_year.",".$cbo_brand.",'".$txt_request_no."',".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_est_ship_date.",".$txt_req_quot_date.",".$txt_target_samp_date.",".$txt_actual_sam_send_date.",".$txt_actual_req_quot_date.",".$cbo_priority.",".$cbo_concern_marchant.",'".$txt_remarks."','".$txt_attention."','".$txt_sales_account."',".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";


		
		
		$rID=sql_insert("wo_buyer_inquery",$field_array,$data_array,0);

		
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_system_id[0]."**".$id."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1){
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_system_id[0]."**".$id."**".$rID."**INSERT INTO wo_buyer_inquery(".$field_array.") VALUES ".$data_array;die;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$update_id=str_replace("'","",$update_id);
		$season=str_replace("'","",$cbo_season_name);
		$txt_light_source=str_replace("'","",$txt_light_source);
		$txt_style_description=str_replace("'","",$txt_style_description);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_remarks=str_replace("'","",$txt_remarks);
		$txt_request_no=str_replace("'","",$txt_request_no);
		$cbo_concern_marchant=str_replace("'","",$cbo_concern_marchant);
		$txt_attention=str_replace("'","",$txt_attention);
		$txt_sales_account=str_replace("'","",$txt_sales_account);
		
		$field_array="within_group*buyer_id*style_refernce*style_description*inquery_date*season* season_year*brand_id*buyer_request*team_leader*dealing_marchant*est_ship_date*req_quotation_date*target_sam_sub_date*actual_sam_send_date*actual_req_quot_date*priority*concern_marchant*remarks*attention*sales_account*ready_to_approved*update_by*update_date*status_active*is_deleted";
		$data_array ="".$cbo_within_group."*".$cbo_buyer_name."*'".$txt_style_ref."'*'".$txt_style_description."'*".$txt_inquery_date."*'".$season."'*".$cbo_season_year."*".$cbo_brand."*'".$txt_request_no."'*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_est_ship_date."*".$txt_req_quot_date."*".$txt_target_samp_date."*".$txt_actual_sam_send_date."*".$txt_actual_req_quot_date."*".$cbo_priority."*'".$cbo_concern_marchant."'*'".$txt_remarks."'*'".$txt_attention."'*'".$txt_sales_account."'*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

		$rID=sql_update("wo_buyer_inquery",$field_array,$data_array,"id","".$update_id."",0);
		
		

		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_id)."**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id)."**".$update_id."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1){
				oci_commit($con);
				echo "1**".$txt_system_id."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_system_id."**".$update_id."**".$rID."**".sql_update("wo_buyer_inquery",$field_array,$data_array,"id","".$update_id."",0,1);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$update_id=str_replace("'","",$update_id);
	
		$field_arrmst="update_by*update_date*status_active*is_deleted";
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_buyer_inquery",$field_arrmst,$data_array,"id","".$update_id."",1);
		$rID1=sql_delete("wo_buyer_inquery_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		//echo $rID."**".$rID1; die;
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_system_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_dtls=return_next_id( "id", "wo_buyer_inquery_dtls", 1 ) ;
		$field_array= "id, mst_id, constuction_id,determination_id, grey_constuction_id,grey_determination_id, product_type, composition_id, warp_yarn_type,weft_yarn_type,weave,design, finish_type, color_id, fabric_weight, fabric_weight_type, finish_width, cutable_width,grey_width, wash_type, offer_qnty, uom,buyer_target_price,amount, insert_by, insert_date, status_active, is_deleted";
		$fab_con_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
		$new_array_color = array();
		$flag = true;
		for ($i=1;$i<=$total_row;$i++)
		{
			$txtColor="txtColor_".$i;
			$cboColorId_="cboColorId_".$i;
			$cboColorId= str_replace("'","",$$cboColorId_);
			$txtRemark="txtRemark_".$i;
			$fabConstructionId="fabConstructionId_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$fabConstruction_="fabConstruction_".$i;
			$greyConstructionId="greyConstructionId_".$i;
			$txtgreyconstruction_="txtgreyconstruction_".$i;
			$greyConstruction_="greyConstruction_".$i;
			$cboProductType="cboProductType_".$i;
			$cbocompone="cbocompone_".$i;
			$cboFinishType="cboFinishType_".$i;
			$txtFabricWeight="txtFabricWeight_".$i;
			$cboweighttype="cboweighttype_".$i;
			$txtFinishedWidth="txtFinishedWidth_".$i;
			$txtCutableWidth="txtCutableWidth_".$i;
			$txtGreyWidth="txtGreyWidth_".$i;
			$cboWashType="cboWashType_".$i;
			$txtOfferQty="txtOfferQty_".$i;
			$cboUom="cboUom_".$i;
			$txtBuyerTgtPrice="txtBuyerTgtPrice_".$i;
			$txtAmount="txtAmount_".$i;
			$yarnCountDeterminationId_="yarnCountDeterminationId_".$i;
			$yarnCountDeterminationId=str_replace("'",'',$$yarnCountDeterminationId_);

			$hiddWarpYarnTypeId_="hiddWarpYarnTypeId_".$i;
			$hiddWarpYarnTypeId=str_replace("'",'',$$hiddWarpYarnTypeId_);	

			$hiddWeftYarnTypeId_="hiddWeftYarnTypeId_".$i;
			$hiddWeftYarnTypeId=str_replace("'",'',$$hiddWeftYarnTypeId_);	

			$txtWeave_="txtWeave_".$i;
			$txtWeave=str_replace("'",'',$$txtWeave_);	

			$txtDesign_="txtDesign_".$i;
			$txtDesign=str_replace("'",'',$$txtDesign_);	


			$fab_construction_id=str_replace("'",'',$$fabConstructionId);
			if(empty($fab_construction_id))
			{
				$txtconstruction=str_replace("'",'',$$txtconstruction_);
				$fab_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
				$fabConstruction = str_replace("'",'',$$fabConstruction_);
				if(empty($fab_construction_id) && !empty($fabConstruction))
				{
					$fab_construction=explode("*",$fabConstruction);

					$fab_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

					$field_array1="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
					$data_array1="(".$fab_construction_id.",'".$txtconstruction."','".$fab_construction[0]."','".$fab_construction[1]."','".$fab_construction[2]."','".$fab_construction[4]."','".$fab_construction[3]."','".$fab_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					// echo "10**INSERT INTO lib_fabric_construction(".$field_array1.") VALUES ".$data_array1;die;
					$rIDCon=sql_insert("lib_fabric_construction",$field_array1,$data_array1,1);
					if($rIDCon == false ) $flag = false;

					$wrap_details = explode(",",$fab_construction[2]);
					$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
					$field_details="id,mst_id,type,counts,count_type,yarn_composition_id";
					$data_details="";
					foreach($wrap_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$fab_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."','".$wr_exp[2]."')";
						
						$fab_dts_id++;
					}
					$weft_details = explode(",",$fab_construction[4]);
					foreach($weft_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$fab_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."','".$wr_exp[2]."')";
						$fab_dts_id++;
					}

					$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
					if($rIDCon1 == false)
					{
						$flag = false;
					}
				}
			}else{
						$fab_construction=explode("*",str_replace("'",'',$$fabConstruction_));
						$wrap_details = explode(",",$fab_construction[2]);
						$fields="warp_count*weft_count";
						$dtls_fields="yarn_composition_id";
						 
						foreach($wrap_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							 
							$delDtls=sql_multirow_update("lib_fab_construction_dtls",$dtls_fields,$wr_exp[2],"mst_id",$fab_construction_id,0);
						}
						$weft_details = explode(",",$fab_construction[4]);
						foreach($weft_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							$delDtls2=sql_multirow_update("lib_fab_construction_dtls",$dtls_fields,$wr_exp[2],"mst_id",$fab_construction_id,0);
						}
						$delDtls3=sql_multirow_update("lib_fabric_construction",$fields,"'".$fab_construction[2]."'*'".$fab_construction[4]."'","id",$fab_construction_id,0);
						 
			}
			$grey_construction_id=str_replace("'",'',$$greyConstructionId);
			if(empty($grey_construction_id))
			{
				$txtgreyconstruction=str_replace("'",'',$$txtgreyconstruction_);
				$grey_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtgreyconstruction'",  "id");
				$greyConstruction=str_replace("'",'',$$greyConstruction_);
				if(empty($grey_construction_id) && !empty($greyConstruction))
				{
					$grey_construction=explode("*",$greyConstruction);

					$grey_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

					$field_array1="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,construction_type,status_active,inserted_by,insert_date";
					$data_array1="(".$grey_construction_id.",'".$txtgreyconstruction."','".$grey_construction[0]."','".$grey_construction[1]."','".$grey_construction[2]."','".$grey_construction[4]."','".$grey_construction[3]."','".$grey_construction[5]."',2,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rIDCon=sql_insert("lib_fabric_construction",$field_array1,$data_array1,1);
					if($rIDCon == false ) $flag = false;

					$wrap_details = explode(",",$grey_construction[2]);
					$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
					$field_details="id,mst_id,type,counts,count_type,construction_type";
					$data_details="";
					foreach($wrap_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$grey_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
						
						$fab_dts_id++;
					}
					$weft_details = explode(",",$grey_construction[4]);
					foreach($weft_details as $wrap_d)
					{
						$wr_exp = explode("_",$wrap_d);
						if(!empty($data_details))
						{
							$data_details .=",";
						}
						$data_details.="(".$fab_dts_id.",".$grey_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
						$fab_dts_id++;
					}

					$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
					if($rIDCon1 == false)
					{
						$flag = false;
					}
				}
			}
			//$updateIdDtls="updateidsampledtl_".$i;

			if(empty($cboColorId))
			{
				if(str_replace("'","",$$txtColor)!="")
				{
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","582");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
				}
				else $color_id=0;
			}
			else{
				$color_id=$cboColorId;
			}
			

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",'".str_replace("'","",$fab_construction_id)."','".str_replace("'","",$yarnCountDeterminationId)."','".str_replace("'","",$grey_construction_id)."','".str_replace("'","",$greyYarnCountDeterminationId)."','".str_replace("'","",$$cboProductType)."','".str_replace("'","",$$cbocompone)."','".str_replace("'","",$hiddWarpYarnTypeId)."','".str_replace("'","",$hiddWeftYarnTypeId)."','".str_replace("'","",$txtWeave)."','".str_replace("'","",$txtDesign)."','".str_replace("'","",$$cboFinishType)."','".str_replace("'","",$color_id)."','".str_replace("'","",$$txtFabricWeight)."','".str_replace("'","",$$cboweighttype)."','".str_replace("'","",$$txtFinishedWidth)."','".str_replace("'","",$$txtCutableWidth)."','".str_replace("'","",$$txtGreyWidth)."','".str_replace("'","",$$cboWashType)."','".str_replace("'","",$$txtOfferQty)."','".str_replace("'","",$$cboUom)."','".str_replace("'","",$$txtBuyerTgtPrice)."','".str_replace("'","",$$txtAmount)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";

			$countsize=0; $ex_data="";
			$id_dtls=$id_dtls+1;
		}

		$rID_1=sql_insert("wo_buyer_inquery_dtls",$field_array,$data_array,1);
		

		if($db_type==0)
		{
			if($rID_1 && $flag){
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_1 && $flag)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$update_id)."**INSERT INTO wo_buyer_inquery_dtls(".$field_array.")VALUES ".$data_array;
			}
			else
			{
				oci_rollback($con);
				echo "10**INSERT INTO wo_buyer_inquery_dtls(".$field_array.")VALUES ".$data_array;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$prev_ids_array=array();
			$prev_ids="SELECT id from wo_buyer_inquery_dtls where status_active=1 and is_deleted=0 and mst_id=$update_id";
			$prev_ids_array=array();
			foreach(sql_select($prev_ids) as $key_id=>$key_val)
			{
				$prev_ids_array[$key_val[csf("id")]]=$key_val[csf("id")];
			}

			
		    $id_dtls=return_next_id( "id", "wo_buyer_inquery_dtls", 1 ) ;
			$field_array= "id, mst_id, constuction_id,determination_id,grey_constuction_id,grey_determination_id, product_type, composition_id, warp_yarn_type,weft_yarn_type,weave,design, finish_type, color_id, fabric_weight, fabric_weight_type, finish_width, cutable_width,grey_width, wash_type, offer_qnty, uom,buyer_target_price,amount, insert_by, insert_date, status_active, is_deleted";
			$field_array_up= "constuction_id*determination_id*grey_constuction_id*grey_determination_id*product_type*composition_id*warp_yarn_type*weft_yarn_type*weave*design*finish_type*color_id*fabric_weight*fabric_weight_type*finish_width*cutable_width*grey_width*wash_type*offer_qnty*uom*buyer_target_price*amount*update_by*update_date";
			$fab_con_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
			$new_array_color = array();
			$flag = true;
			for ($i=1;$i<=$total_row;$i++)
			{
				$txtColor="txtColor_".$i;
				$cboColorId_="cboColorId_".$i;
				$cboColorId= str_replace("'","",$$cboColorId_);
				$txtRemark="txtRemark_".$i;
				$fabConstructionId="fabConstructionId_".$i;
				$txtconstruction_="txtconstruction_".$i;
				$greyConstruction_="greyConstruction_".$i;
				$greyConstructionId="greyConstructionId_".$i;
				$txtgreyconstruction_="txtgreyconstruction_".$i;
				$greyConstruction_="greyConstruction_".$i;
				$fabConstruction_="fabConstruction_".$i;
				$cboProductType="cboProductType_".$i;
				$cbocompone="cbocompone_".$i;
				$txtWeaveDesign="txtWeaveDesign_".$i;
				$cboFinishType="cboFinishType_".$i;
				$txtFabricWeight="txtFabricWeight_".$i;
				$cboweighttype="cboweighttype_".$i;
				$txtFinishedWidth="txtFinishedWidth_".$i;
				$txtCutableWidth="txtCutableWidth_".$i;
				$txtGreyWidth="txtGreyWidth_".$i;
				$cboWashType="cboWashType_".$i;
				$txtOfferQty="txtOfferQty_".$i;
				$cboUom="cboUom_".$i;
				$txtBuyerTgtPrice="txtBuyerTgtPrice_".$i;
				$txtAmount="txtAmount_".$i;
				$updateDtlsId="updateDtlsId_".$i;
				$yarnCountDeterminationId_="yarnCountDeterminationId_".$i;
				$yarnCountDeterminationId=str_replace("'",'',$$yarnCountDeterminationId_);
				$greyYarnCountDeterminationId_="yarnCountDeterminationId_".$i;
				$greyYarnCountDeterminationId=str_replace("'",'',$$greyYarnCountDeterminationId_);

				$hiddWarpYarnTypeId_="hiddWarpYarnTypeId_".$i;
				$hiddWarpYarnTypeId=str_replace("'",'',$$hiddWarpYarnTypeId_);	

				$hiddWeftYarnTypeId_="hiddWeftYarnTypeId_".$i;
				$hiddWeftYarnTypeId=str_replace("'",'',$$hiddWeftYarnTypeId_);	

				$txtWeave_="txtWeave_".$i;
				$txtWeave=str_replace("'",'',$$txtWeave_);	

				$txtDesign_="txtDesign_".$i;
				$txtDesign=str_replace("'",'',$$txtDesign_);	

				unset($prev_ids_array[str_replace("'","",$$updateDtlsId)]);

				$fab_construction_id=str_replace("'",'',$$fabConstructionId);
				if(empty($fab_construction_id))
				{
					$txtconstruction=str_replace("'",'',$$txtconstruction_);
					$fab_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
					$fabConstruction = str_replace("'",'',$$fabConstruction_);
					if(empty($fab_construction_id) && !empty($fabConstruction))
					{
						$fab_construction=explode("*",$fabConstruction);

						$fab_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

						$field_array1="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
						$data_array1="(".$fab_construction_id.",'".$txtconstruction."','".$fab_construction[0]."','".$fab_construction[1]."','".$fab_construction[2]."','".$fab_construction[4]."','".$fab_construction[3]."','".$fab_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$rIDCon=sql_insert("lib_fabric_construction",$field_array1,$data_array1,1);
						if($rIDCon == false ) $flag = false;

						$wrap_details = explode(",",$fab_construction[2]);
						$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
						$field_details="id,mst_id,type,counts,count_type,yarn_composition_id";
						$data_details="";
						foreach($wrap_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							if(!empty($data_details))
							{
								$data_details .=",";
							}
							$data_details.="(".$fab_dts_id.",".$fab_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."','".$wr_exp[2]."')";
							
							$fab_dts_id++;
						}
						$weft_details = explode(",",$fab_construction[4]);
						foreach($weft_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							if(!empty($data_details))
							{
								$data_details .=",";
							}
							$data_details.="(".$fab_dts_id.",".$fab_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."','".$wr_exp[2]."')";
							$fab_dts_id++;
						}

						$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						if($rIDCon1 == false)
						{
							$flag = false;
						}
					}
				}else{
					    $fab_construction=explode("*",str_replace("'",'',$$fabConstruction_));
						$wrap_details = explode(",",$fab_construction[2]);
						$fields="warp_count*weft_count";
						$dtls_fields="yarn_composition_id";
						 
						foreach($wrap_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							 
							$delDtls=sql_multirow_update("lib_fab_construction_dtls",$dtls_fields,$wr_exp[2],"mst_id",$fab_construction_id,0);
						
						}
						$weft_details = explode(",",$fab_construction[4]);
						foreach($weft_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							$delDtls2=sql_multirow_update("lib_fab_construction_dtls",$dtls_fields,$wr_exp[2],"mst_id",$fab_construction_id,0);
							
						}
						$delDtls3=sql_multirow_update("lib_fabric_construction",$fields,"'".$fab_construction[2]."'*'".$fab_construction[4]."'","id",$fab_construction_id,0);
				}
				$grey_construction_id=str_replace("'",'',$$greyConstructionId);
				if(empty($grey_construction_id))
				{
					$txtgreyconstruction=str_replace("'",'',$$txtgreyconstruction_);
					$grey_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtgreyconstruction'",  "id");
					$greyConstruction=str_replace("'",'',$$greyConstruction_);
					if(empty($grey_construction_id) && !empty($greyConstruction))
					{
						$grey_construction=explode("*",$greyConstruction);

						$grey_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

						$field_array1="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,construction_type,inserted_by,insert_date";
						$data_array1="(".$grey_construction_id.",'".$txtgreyconstruction."','".$grey_construction[0]."','".$grey_construction[1]."','".$grey_construction[2]."','".$grey_construction[4]."','".$grey_construction[3]."','".$grey_construction[5]."',2,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$rIDCon=sql_insert("lib_fabric_construction",$field_array1,$data_array1,1);
						if($rIDCon == false ) $flag = false;

						$wrap_details = explode(",",$grey_construction[2]);
						$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
						$field_details="id,mst_id,type,counts,count_type,construction_type";
						$data_details="";
						foreach($wrap_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							if(!empty($data_details))
							{
								$data_details .=",";
							}
							$data_details.="(".$fab_dts_id.",".$grey_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
							
							$fab_dts_id++;
						}
						$weft_details = explode(",",$grey_construction[4]);
						foreach($weft_details as $wrap_d)
						{
							$wr_exp = explode("_",$wrap_d);
							if(!empty($data_details))
							{
								$data_details .=",";
							}
							$data_details.="(".$fab_dts_id.",".$grey_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."',2)";
							$fab_dts_id++;
						}

						$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
						if($rIDCon1 == false)
						{
							$flag = false;
						}
					}
				}
				//$updateIdDtls="updateidsampledtl_".$i;

				if(empty($cboColorId))
				{
					if(str_replace("'","",$$txtColor)!="")
					{
						if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$$txtColor), $color_arr, "lib_color", "id,color_name","582");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_color[$color_id]=str_replace("'","",$$txtColor);
						}
						else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color);
					}
					else $color_id=0;
				}
				else
				{
					$color_id=$cboColorId;
				}

				if (str_replace("'",'',$$updateDtlsId)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateDtlsId);

					$data_array_up[str_replace("'",'',$$updateDtlsId)]=explode("*",("'".str_replace("'","",$fab_construction_id)."'*'".str_replace("'","",$yarnCountDeterminationId)."'*'".str_replace("'","",$grey_construction_id)."'*'".str_replace("'","",$greyYarnCountDeterminationId)."'*'".str_replace("'","",$$cboProductType)."'*'".str_replace("'","",$$cbocompone)."'*'".str_replace("'","",$hiddWarpYarnTypeId)."'*'".str_replace("'","",$hiddWeftYarnTypeId)."'*'".str_replace("'","",$txtWeave)."'*'".str_replace("'","",$txtDesign)."'*'".str_replace("'","",$$cboFinishType)."'*'".str_replace("'","",$color_id)."'*'".str_replace("'","",$$txtFabricWeight)."'*'".str_replace("'","",$$cboweighttype)."'*'".str_replace("'","",$$txtFinishedWidth)."'*'".str_replace("'","",$$txtCutableWidth)."'*'".str_replace("'","",$$txtGreyWidth)."'*'".str_replace("'","",$$cboWashType)."'*'".str_replace("'","",$$txtOfferQty)."'*'".str_replace("'","",$$cboUom)."'*'".str_replace("'","",$$txtBuyerTgtPrice)."'*'".str_replace("'","",$$txtAmount)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					if ($i!=1) $data_array .=",";
					$data_array .="(".$id_dtls.",".$update_id.",'".str_replace("'","",$fab_construction_id)."','".str_replace("'","",$yarnCountDeterminationId)."','".str_replace("'","",$grey_construction_id)."','".str_replace("'","",$greyYarnCountDeterminationId)."','".str_replace("'","",$$cboProductType)."','".str_replace("'","",$$cbocompone)."','".str_replace("'","",$hiddWarpYarnTypeId)."','".str_replace("'","",$hiddWeftYarnTypeId)."','".str_replace("'","",$txtWeave)."','".str_replace("'","",$txtDesign)."','".str_replace("'","",$$cboFinishType)."','".str_replace("'","",$color_id)."','".str_replace("'","",$$txtFabricWeight)."','".str_replace("'","",$$cboweighttype)."','".str_replace("'","",$$txtFinishedWidth)."','".str_replace("'","",$$txtCutableWidth)."','".str_replace("'","",$$txtGreyWidth)."','".str_replace("'","",$$cboWashType)."','".str_replace("'","",$$txtOfferQty)."','".str_replace("'","",$$cboUom)."','".str_replace("'","",$$txtBuyerTgtPrice)."','".str_replace("'","",$$txtAmount)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
					$id_dtls=$id_dtls+1;
				}
			}
			
			$del_ids=implode(",",$prev_ids_array );
			$delDtls = true;
			if($del_ids!="" || $del_ids!=0)
			{

				$fields="status_active*is_deleted";
				$delDtls=sql_multirow_update("wo_buyer_inquery_dtls",$fields,"0*1","id",$del_ids,0);
 			}
 			$rID1 = true;
 			if($data_array_up!="")
			{
				
				$rID1=execute_query(bulk_update_sql_statement("wo_buyer_inquery_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				//echo "10**".bulk_update_sql_statement("wo_buyer_inquery_dtls", "id",$field_array_up,$data_array_up,$id_arr );die;
				if($rID1) $flag=$flag; else $flag=false;
			}
			$rID_1 = true;
			if(!empty($data_array))
			{
				$rID_1=sql_insert("wo_buyer_inquery_dtls",$field_array,$data_array,1);
				if($rID_1) $flag=$flag; else $flag=false;
			}
			
			//echo "10**".$rID_dtls.'='.$rIDs.'='.$rID1.'='.$rID_size;die;

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'",'',$update_id)."**1";
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**$delDtls&&$rID1&&$rID_1";
				}
			}
			elseif($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".str_replace("'",'',$update_id)."**1";

				}
				else
				{
					oci_rollback($con);
					echo "10**$delDtls&&$rID1&&$rID_1**".bulk_update_sql_statement("wo_buyer_inquery_dtls", "id",$field_array_up,$data_array_up,$id_arr );
				}
			}
			disconnect($con);
			die;
	}

	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

		$rID=sql_delete("wo_buyer_inquery_dtls",$field_array,$data_array,"mst_id","".$update_id,0);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID  )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}


if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$rdno)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($rdno!='') {$search_con .= " and a.rd_no='".trim($rdno)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('".trim($rdno)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	}
	
	

	?>
	</head>
	<body>
		<?
			$composition_arr=array();
			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
								
			$data_array=sql_select($sql_q);
			if (count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					$compo_per="";
					if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
					if(array_key_exists($row[csf('mst_id')],$composition_arr))
					{
						$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					else
					{
						$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
					}
				}
			}
			unset($data_array);
		?>
	    <table class="rpt_table" width="1050px" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
	        <thead>
	        	<tr>
	        		<th width="25">SL</th>
		            <th width="75">Fab Nature</th>
	                <th width="60">RD No</th>
		            <th width="80">Fabric Ref</th>
		            <th width="60">Type</th>
		            <th width="100">Construction</th>
		            <th width="80">Design</th>
		            <th width="50">Weight</th>
		            <th width="50">Weight Type</th>
		            <th width="50">Color Range</th>
	                <th width="50">Full Width</th>
	                <th width="50">Cutable Width</th>
	                <th width="50">Shrinkage L</th>
	                <th width="50">Shrinkage W</th>
		            <th>Composition</th>
	        	</tr>
	       </thead>
	   </table>
	   <div style="max-height:230px; width:1050px; overflow-y:scroll">
	       <table id="list_view" class="rpt_table" width="1030px" height="" cellspacing="0" cellpadding="0" border="1" rules="all" >
	            <tbody>
	        <?

	            $sql_data=sql_select("select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a where a.is_deleted=0 and status_active=1 and  a.fab_nature_id= '$fabric_nature' $search_con order by a.id ASC");
	            $i=1;
	            foreach($sql_data as $row)
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                    <tr id="tr_<?=$row[csf('id')] ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('rd_no')]."_".$row[csf('fabric_ref')]."_".$row[csf('type')]."_".$row[csf('construction')]."_".$row[csf('design')]."_".$row[csf('gsm_weight')]."_".$fabric_weight_type[$row[csf('weight_type')]]."_".$color_range[$row[csf('color_range_id')]]."_".$composition_arr[$row[csf('id')]]."_".$row[csf('full_width')]."_".$row[csf('cutable_width')]."_".$row[csf('shrinkage_l')]."_".$row[csf('shrinkage_w')]; ?>')">
	                        <td width="25" align="center"><?=$i; ?></td>
	                        <td width="75" style="word-break:break-all"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
	                        <td width="60" style="word-break:break-all"><?=$row[csf('rd_no')]; ?></td>
	                        <td width="80" style="word-break:break-all"><?=$row[csf('fabric_ref')]; ?></td>
	                        <td width="60" style="word-break:break-all"><?=$row[csf('type')]; ?></td>
	                        <td width="100" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
	                        <td width="80" style="word-break:break-all"><?=$row[csf('design')]; ?></td>
	                        <td width="50" style="word-break:break-all"><?=$row[csf('gsm_weight')]; ?></td>
	                        <td width="50" style="word-break:break-all"><?=$fabric_weight_type[$row[csf('weight_type')]]; ?></td>
	                        <td width="50" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
	                        
	                        <td width="50" style="word-break:break-all"><?=$row[csf('full_width')]; ?></td>
	                        <td width="50" style="word-break:break-all"><?=$row[csf('cutable_width')]; ?></td>
	                        <td width="50" style="word-break:break-all"><?=$row[csf('shrinkage_l')]; ?></td>
	                        <td width="50" style="word-break:break-all"><?=$row[csf('shrinkage_w')]; ?></td>
	                        
	                        <td style="word-break:break-all"><?=$composition_arr[$row[csf('id')]]; ?></td>
	                    </tr>
	                <?
	                $i++;
	            }
	        ?>
	            </tbody>
	        </table>
	    </div>
	</body>
	</html>
	<?
	exit();
}

if($action=="inquery_entry_print")
{
	extract($_REQUEST);
	$data=explode("**",$data);
	$company_name=$data[0];
	$txt_system_id=$data[1];
	$mst_id=$data[2];
	$report_title=$data[3];
	
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$color_arr = return_library_array("select id,color_name from  lib_color ","id","color_name");
	$company_library = return_library_array("select id,company_name from lib_company","id","company_name");
	$season_name_library = return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$sql = "select system_number_prefix, system_number_prefix_num, system_number, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, buyer_request, remarks, dealing_marchant, gmts_item, est_ship_date, fabrication, offer_qty, color, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, insert_by, insert_date, status_active, is_deleted, season_year, brand_id,style_description,con_rec_target_date,concern_marchant,TEAM_LEADER,PRIORITY,COPY_SYSTEM_NUMBER from wo_quotation_inquery where id=$mst_id and status_active=1  order by id";
	$data_array=sql_select($sql);

	?>

	<div style="width:850px; font-size:20px; font-weight:bold" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td width="80">
                    <img  src='../../<? echo $imge_arr[$company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="450">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><?php echo $company_library[$company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name");
                            foreach ($nameArray as $result)
                            {
                                ?>
                                <? echo $result[csf('plot_no')]; ?>
                                <? echo $result[csf('level_no')]?>
                                <? echo $result[csf('road_no')]; ?>
                                <? echo $result[csf('block_no')];?>
                                <? echo $result[csf('city')];?>
                                <? echo $result[csf('zip_code')]; ?>
                                <? echo $result[csf('province')]; ?>
                                <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                <? echo $result[csf('email')];?>
                                <? echo $result[csf('website')];
                            }
                            foreach ($data_array as $row)
                            {
                                $system_number= $row[csf('system_number')];
                                $buyer_id= $row[csf('buyer_id')];
                                $season_buyer_wise= $row[csf('season_buyer_wise')];
                                $inquery_date= $row[csf('inquery_date')];
                                $style_refernce= $row[csf('style_refernce')];
                                $dealing_marchant= $row[csf('dealing_marchant')]; 
								$TEAM_LEADER= $row[csf('TEAM_LEADER')];
								$PRIORITY= $row[csf('PRIORITY')];
								$COPY_SYSTEM_NUMBER= $row[csf('COPY_SYSTEM_NUMBER')];
                                $concern_marchant= $row[csf('concern_marchant')];
                                $gmts_item= $row[csf('gmts_item')];
                                $est_ship_date= $row[csf('est_ship_date')];
                                $fabrication= $row[csf('fabrication')];
                                $offer_qty= $row[csf('offer_qty')];
                                $color= $row[csf('color')];
                                $req_quotation_date= $row[csf('req_quotation_date')];
                                $target_sam_sub_date= $row[csf('target_sam_sub_date')];
								$actual_sam_send_date= $row[csf('actual_sam_send_date')];
                                $actual_req_quot_date= $row[csf('actual_req_quot_date')];
                                $department_name= $row[csf('department_name')];
                                $buyer_target_price= $row[csf('buyer_target_price')];
                                $remarks= $row[csf('remarks')];$buyer_request= $row[csf('buyer_request')];
                                $buyer_submit_price= $row[csf('buyer_submit_price')];
								$style_description= $row[csf('style_description')];
								$season_year= $row[csf('season_year')];
								$brand_id= $row[csf('brand_id')];
								$rec_target_date=change_date_format($row[csf("con_rec_target_date")],"dd-mm-yyyy","-");
                            }
							
							$composition_arr=array();
							$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
							$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication) order by id";
												
							$data_array=sql_select($sql_q);
							if (count($data_array)>0)
							{
								foreach( $data_array as $row )
								{
									$compo_per="";
									if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
									if(array_key_exists($row[csf('mst_id')],$composition_arr))
									{
										$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
									}
									else
									{
										$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
									}
								}
							}
							unset($data_array);
							$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width,shrinkage_l,shrinkage_w from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 and id in ($fabrication)";
							$sqlRdData=sql_select($sqlRd); $fabricationData="";
							foreach($sqlRdData as $row)
							{
								if($fabricationData=="") $fabricationData="* ".$row[csf('rd_no')].', '.$row[csf('fabric_ref')].', '.$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$row[csf('full_width')].', '.$row[csf('cutable_width')].', '.$row[csf('shrinkage_l')].', '.$row[csf('shrinkage_w')].', '.$composition_arr[$row[csf('id')]];
								else $fabricationData.="<br> * ".$row[csf('rd_no')].', '.$row[csf('fabric_ref')].', '.$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$row[csf('full_width')].', '.$row[csf('cutable_width')].', '.$row[csf('shrinkage_l')].', '.$row[csf('shrinkage_w')].', '.$composition_arr[$row[csf('id')]];
							}
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
                                <strong><? echo $report_title;?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td align="center" style="font-size:20px;" colspan="4"> <strong>System ID :</strong>&nbsp;<?php echo $system_number; ?>  &nbsp;  &nbsp;  <strong>Copy System ID :</strong>&nbsp;<?php echo $COPY_SYSTEM_NUMBER; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;" width="150"><strong> Buyer</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $buyer_arr[$buyer_id]; ?></td>
                <td align="left" style="font-size:20px;" width="150"><strong>Style Ref.</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $style_refernce; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Inq.Rcvd Date</strong></td>
                <td align="left" style="font-size:20px;"><?php echo change_date_format($inquery_date); ?> </td>
                <td align="left" style="font-size:20px;"><strong> Buyer Inquiry No </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $buyer_request; ?> </td>
            </tr>
            
            <tr>
                <td align="left" style="font-size:20px;"><strong>Team Leader</strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $team_leader_arr[$TEAM_LEADER]; ?> </td>
                <td align="left" style="font-size:20px;"><strong>Bulk Est. Ship Date</strong></td>
                <td align="left" style="font-size:20px;"><?php echo change_date_format($est_ship_date); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Gmts Item</strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $garments_item[$gmts_item]; ?></td>
                <td align="left" style="font-size:20px;"><strong> Bulk Offer Qty</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $offer_qty; ?> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Fabrication</strong></td>
                <td align="left" style="font-size:20px;" colspan="3"> <p><?=$fabricationData; ?></p> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong> Body Color </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $color; ?></td>
                <td align="left" style="font-size:20px;"> <strong> Season</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $season_name_library[$season_buyer_wise]; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Brand</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $brandArr[$brand_id]; ?></td>
                <td align="left" style="font-size:20px;"> <strong>Season Year</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $season_year; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Target Req. Quot. Date </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($req_quotation_date); ?></td>
                <td align="left" style="font-size:20px;"> <strong> Target Samp Sub:Date</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($target_sam_sub_date); ?></td>
            </tr>
            
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Actual Samp.Send Date </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($actual_sam_send_date); ?></td>
                <td align="left" style="font-size:20px;"> <strong>Actual Quot. Date</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($actual_req_quot_date); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Sample Merchant</strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $marchentrArr[$concern_marchant]; ?> </td>
                <td align="left" style="font-size:20px;">  <strong>Dealing Merchant </strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $marchentrArr[$dealing_marchant]; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Buyer Submit Price </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo number_format($buyer_submit_price, 2); ?></td>
                <td align="left" style="font-size:20px;"> <strong>Style Description </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $style_description; ?></td>
            </tr>
            <tr>
               
                <td align="left" style="font-size:20px;"><strong> Consumption Rec.Tgt.Date</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $rec_target_date; ?></td>
                <td align="left" style="font-size:20px;"><strong>Priority</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $priority_arr[$PRIORITY]; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Remarks </strong>  </td>
                <td align="left" style="font-size:20px;" colspan="3"> <?php echo $remarks; ?></td>
            </tr>
            
            
            <tr>
                <td align="left" style="font-size:20px;"><strong>Image</strong>  </td>
                <td align="left" style="font-size:20px;" colspan="3"><? $sql = "select id,master_tble_id,image_location from common_photo_library where master_tble_id='$mst_id' and FORM_NAME in('quotation_inquery_back_image','quotation_inquery_front_image')"; $data_array=sql_select($sql);
			   ?>
					<? foreach($data_array as $inf){ ?>
						<img  src='../../<? echo $inf[csf("image_location")]; ?>' height='100' width='100' style="float:left;" />
					<?  } ?>
          </td>
            </tr>
        </table>
        <?  echo signature_table(126, $company_name, "850px"); ?>
   	</div>
	<?
    exit();
}
if($action=="inquery_entry_print2") //11310
{
 	extract($_REQUEST);
	$data=explode("**",$data);
	$company_name=$data[0];
	$txt_system_id=$data[1];
	$mst_id=$data[2];
	$report_title=$data[3];
	$buyer_id=$data[4];
	
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$color_arr = return_library_array("select id,color_name from  lib_color ","id","color_name");
	$company_library = return_library_array("select id,company_name from lib_company","id","company_name");
	$season_name_library = return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");

	$sql="select  id, system_number_prefix, system_number_prefix_num, system_number, company_id,within_group,buyer_id,style_refernce,style_description,inquery_date,season, season_year, brand_id, buyer_request,team_leader,dealing_marchant,est_ship_date,req_quotation_date,target_sam_sub_date,actual_sam_send_date,actual_req_quot_date,priority,concern_marchant,age_range,end_use,wash,light_source,remarks,attention,sales_account from wo_buyer_inquery where system_number='$txt_system_id'  order by id";
	//echo $sql;die;
	 $data_array=sql_select($sql);

	$sql_dtls = "SELECT id, mst_id, constuction_id,determination_id, grey_constuction_id,grey_determination_id, product_type, composition_id, warp_yarn_type,weft_yarn_type,weave,design, finish_type, color_id, fabric_weight, fabric_weight_type, finish_width, cutable_width,grey_width, wash_type, offer_qnty, uom,buyer_target_price,amount, insert_by, insert_date, status_active, is_deleted
  		FROM wo_buyer_inquery_dtls where mst_id = ".$data_array[0][csf('id')];

  	$res_dtls = sql_select($sql_dtls);

  	$data_dtls = array();
  	$const_id = array();
  	$determination_id = array();

  	foreach($res_dtls as $row)
  	{
  		$const_id[$row[csf('constuction_id')]] 				= $row[csf('constuction_id')];
  		$const_id[$row[csf('grey_constuction_id')]] 		= $row[csf('grey_constuction_id')];
  		if(!empty($row[csf('determination_id')]))
		{
  			$deter_ids[$row[csf('determination_id')]] 	= $row[csf('determination_id')];
  		}
  	}

	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=581 and a.id in (".implode(",", $deter_ids).") order by a.id,b.id";
	//echo $sql;
			  
	$data_deter=sql_select($sql);
	if (count($data_deter)>0)
	{
        foreach( $data_deter as $row )
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



  	$fabric_construction_name_arr = return_library_array("select id,fabric_construction_name from lib_fabric_construction  where id in(".implode(",", $const_id).")","id","fabric_construction_name");
  	
	 
	foreach($res_dtls as $row)
  	{
  		$constuction_id=$row[csf("constuction_id")];
		$fabric_construction=$fabric_construction_name_arr[$constuction_id];
		$proposed_grey_construction=$fabric_construction_name_arr[$row[csf('grey_constuction_id')]];
		if(!empty($row[csf('determination_id')]))
		{
			$composition_str = $composition_arr[$row[csf('determination_id')]];
		}
		else
		{
			$composition_str = $composition[$row[csf('composition_id')]];
		}

		//Fabric Construction, Prodcut Type,  Fab. Color, Fab. Composition,  Weave/Design, Finished Width,  Wash Type

		$index = $fabric_construction . "," . $color_type[$row[csf('product_type')]]  .",". $composition_str .",". $row[csf('weave')] .",". $row[csf('design')]  .",". $finish_types[$row[csf('finish_type')]] .",". $row[csf('fabric_weight')] ."[". $fabric_weight_type[$row[csf('fabric_weight_type')]] ."],". $row[csf('finish_width')] .",".  $row[csf('cutable_width')] .",". $wash_types[$row[csf('wash_type')]];

		$swatch_construction = $fabric_construction . "," . $color_type[$row[csf('product_type')]] .",". $color_arr[$row[csf('color_id')]]  .",". $composition_str .",". $row[csf('weave')] .",". $row[csf('design')]  .",". $row[csf('finish_width')]  .",". $wash_types[$row[csf('wash_type')]];

		$data_dtls[$index]['swatch_construction'] = $swatch_construction;
		$data_dtls[$index]['swatch_dyeing_method'] = $finish_types[$row[csf('finish_type')]];
		$data_dtls[$index]['swatch_weight_in_gsm'] = $row[csf('fabric_weight')] ."[". $fabric_weight_type[$row[csf('fabric_weight_type')]]."]";
		$data_dtls[$index]['proposed_grey_construction'] = $proposed_grey_construction;
		$data_dtls[$index]['proposed_grey_width'] = $row[csf('grey_width')];
  	}
 
 	?>
 	<style type="text/css">
 		.page_break_div{
 			page-break-after: always;
 		}
 	</style>
 	<?

  	foreach($data_dtls as $key => $val)
  	{
  		?>
		<div style="width:850px; font-size:20px; font-weight:bold" class="page_break_div" align="center">
	        
	        <table border="1" align="left"    cellpadding="0" width="100%" cellspacing="0" rules="all" >
	             
	            <tr style="border-left:none">
	                <td align="left" style="font-size:16px; border-left:none" width="450" colspan="2"><p>Date :&nbsp;<?=change_date_format($data_array[0][csf('inquery_date')]);?></p></td>                 
	                <td align="center" style="font-size:16px;" width="450" colspan="2"><p>Construction Check Report</p></td>
	            </tr>
	            <tr>
	                <td align="left" style="font-size:16px;" width="450" colspan="2"><p>Kind Attention :&nbsp;<?=$data_array[0][csf('attention')];?></p></td>                
	                <td align="left" style="font-size:16px;" width="150"><p> Advice No </p></td>
	                <td align="left" style="font-size:16px;" width="350"> &nbsp;<?=$txt_system_id;?> </td>
	            </tr>
	            <tr>

	                <td align="left" style="font-size:16px;" width="450" colspan="2"><p>Copy To :&nbsp;<?=$marchentrArr[$data_array[0][csf('dealing_marchant')]];?></p> </td>          
	                <td align="left" style="font-size:16px;"><p>Customer Name</p></td>
	                <td align="left" style="font-size:16px;">&nbsp;<?=$data_array[0][csf('within_group')] == 1 ? $company_library[$data_array[0][csf('buyer_id')]] :  $buyer_arr[$data_array[0][csf('buyer_id')]];?></td>
	            </tr>
	            <tr>
	                <td align="left" style="font-size:16px;" colspan="2"> &nbsp; </td>              
	                <td align="left" style="font-size:16px;"><p> Sales Account</p> </td>
	                <td align="left" style="font-size:16px;">&nbsp; <?=$data_array[0][csf('sales_account')];?></td>
	            </tr>
	        </table>
			<table border="1" align="left"    cellpadding="0" width="100%" cellspacing="0" rules="all"  style="margin-top: 20px;">
	             
				 <tr>
					 <td align="left" style="font-size:16px; border-left:none" width="650" height="400px"  rowspan="3">&nbsp;</td>                 
					 <td align="left" style="font-size:16px; border-left:none" width="250"  height="400px"   colspan="3">&nbsp;</td>        
				 </tr>
				 <tr>
					 <td align="left" style="font-size:16px; border-left:none" width="60">&nbsp;</td>                 
					 <td align="center" style="font-size:16px; border-left:none"   width="150"  colspan="2" >Lab Manager</td>        
				 </tr>
				 <tr>
			                
					 <td align="left" style="font-size:16px; border-left:none"     colspan="3">&nbsp;</td>        
				 </tr>
				 
				 
			 </table>
			<table   align="left"    cellpadding="0" width="100%" cellspacing="0"  style="margin-top: 20px;" >
				
				<tr style="border-left:none">
					<td align="left" style="font-size:16px; border-left:none" width="200"  ><p>Swatch Constraction </p></td>   
					<td align="left" style="font-size:16px; border-left:none" width="450" colspan="3">:<?=$val['swatch_construction'];?></td>                       
				</tr>
				<tr>
					<td align="left" style="font-size:16px; border-left:none" width="200" ><p>Swatch Dyeing Method </p></td> 
					<td align="left" style="font-size:16px; border-left:none" width="450" colspan="3">:<?=$val['swatch_dyeing_method'];?></td>
				</tr>
				<tr>
					<td align="left" style="font-size:16px; border-left:none" width="200"><p>Swatch Weight In GSM </p></td>
					<td align="left" style="font-size:16px; border-left:none" width="450" colspan="3">:<?=$val['swatch_weight_in_gsm'];?></td>
				</tr>
				<tr>
					<td align="left" style="font-size:16px; border-left:none" width="200"><p>Proposed Grey Constraction </p></td>
					<td align="left" style="font-size:16px; border-left:none" width="450" colspan="3">:<?=$val['proposed_grey_construction'];?></td>
				</tr>
				<tr>
					<td align="left" style="font-size:16px; border-left:none" width="200"><p>Proposed Grey Width </p></td>
					<td align="left" style="font-size:16px; border-left:none" width="450" colspan="3">:<?=$val['proposed_grey_width'];?></td>
				</tr>
			</table>
	        <?  echo signature_table(126, $company_name, "850px"); ?>
	   	</div>
		<?
  	}
	
    exit();
}


if($action=="copy_data_change_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	 
	?>
    <script>
	function js_set_value(){
		
		parent.emailwindow.hide();
		
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
                    	<th class="must_entry_caption">M.Style Ref/Name</th>
                        <th class="must_entry_caption">Body/Wash Color</th>
                        <th>Season</th>
                        <th>Season Year</th>
                        <th>Brand</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td><input type="text" style="width:120px" class="text_boxes" name="txt_change_style_ref" id="txt_change_style_ref" /></td>
                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_change_wash_color" id="txt_change_wash_color" /></td>
                        <td><? echo create_drop_down( "cbo_change_season_name", 70, "select id, season_name from lib_buyer_season where buyer_id='$buyer_name' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", $season_name, "" ); ?></td>
                        <td><? echo create_drop_down( "cbo_change_season_year", 70, create_year_array(),"", 1,"-Select-", $season_year, "",0,"" ); ?></td>
                        <td id="load_change_brand"><? echo create_drop_down( "cbo_change_brand_id", 70,array(),"", 1,"-Select-", $season_year, "",0,"" ); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="30" valign="middle" align="center"><button onClick="js_set_value()" class="formbutton" style="width:60px;">OK</button></td>
					</tr>            	
				</tbody>
           	</table>
            <div id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
	load_drop_down( 'buyer_inquery_for_textile_controller','<?=$buyer_name.'**'.$cbo_brand;?>', 'load_drop_down_change_brand', 'load_change_brand');
	</script>
    </html>
    <?
	exit();
}


if($action=="fabric_construction_popup")
{
	echo load_html_head_contents("Material Construction Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	$lib_composition=return_library_array( "select id,composition_name from lib_composition_array where   status_active in(1,2)", "id", "composition_name");
	?>
	<script>
		var spandexarr = "";var compositionArr = "";
		<?
			$data_array= json_encode( $spandex_arr );
			echo "spandexarr = ". $data_array . ";\n";
			if(count($lib_composition)>0){
				$data_array2= json_encode( $lib_composition );
				echo "compositionArr = ". $data_array2 . ";\n";
			}
		?>
		function js_set_value(id,name,epi,ppi,warp_count,warp_spandex,weft_count,weft_spandex,row_id)
		{
			console.log(id +'='+ name);
			document.getElementById('hidfabconspid').value=id;
			document.getElementById('hidfabconsname').value=name;
			document.getElementById('txt_epi').value=epi;
			document.getElementById('txt_ppi').value=ppi;
			document.getElementById('txt_wrap_spandex').value=warp_spandex;
			document.getElementById('txt_weft_spandex').value=weft_spandex;

			warp_count = warp_count.split(",");
			var j = 1;
			for( let i = 0; i < warp_count.length; i++)
			{
				var wrap = warp_count[i].split("_");
				$("#txtWarpCount_"+j).val(wrap[0]);
				$("#cboWarpType_"+j).val(wrap[1]);
				$("#cboWarpCom_"+j).val(wrap[2]);
				if(j < warp_count.length)
				{
					add_wrap(j);
				}
				j++;

			}
			
			weft_count = weft_count.split(",");
			var j = 1;
			for( let i = 0; i < weft_count.length; i++)
			{
				var wrap = weft_count[i].split("_");
				$("#txtWeftCount_"+j).val(wrap[0]);
				$("#cboWeftType_"+j).val(wrap[1]);
				$("#cboWeftCom_"+j).val(wrap[2]);
				if(j < weft_count.length)
				{
					add_weft(j);
				}
				j++;
			}

			
			toggle( row_id, "#E9F3FF" );
			//parent.emailwindow.hide();
		}
		function ClosePopup()
		{
			var txt_epi = document.getElementById('txt_epi').value ;
			var txt_ppi = document.getElementById('txt_ppi').value ;
			var calculated_gsm = 0;
			var row_num=$('#tbl_warp_list tbody tr').length;
			var wrap_str = "";
			var wrap_id_str = "";
			var str_composition = "";
			var s1 = "<sub>";
			var s2 = "</sub>";
			for(var i = 1; i <= row_num; i++)
			{
				var txtWarpCount = $("#txtWarpCount_"+i).val() * 1;
				var cboWarpType  = $("#cboWarpType_"+i).val() * 1;
				var cboWarpCom  = $("#cboWarpCom_"+i).val() * 1;
				if( i > 1)
				{
					wrap_str += '+';
					wrap_id_str += ',';
					str_composition += ',';
				}
				wrap_str += txtWarpCount + 'x' + spandexarr[cboWarpType]+ "_"+compositionArr[cboWarpCom];
				wrap_id_str += txtWarpCount + "_"+cboWarpType+ "_"+cboWarpCom;
				str_composition += compositionArr[cboWarpCom];
				calculated_gsm += ( ( txt_epi * 1 ) / ( txtWarpCount * 1) ) * 23.25;
			}

			row_num=$('#tbl_weft_list tbody tr').length;
			var weft_str = "";
			var weft_id_str = "";

			for(var i = 1; i <= row_num; i++)
			{
				var txtWeftCount = $("#txtWeftCount_"+i).val() * 1;
				var cboWeftType  = $("#cboWeftType_"+i).val() * 1;
				var cboWeftCom  = $("#cboWeftCom_"+i).val() * 1;
				if( i > 1)
				{
					weft_str += '+';
					weft_id_str += ',';
				}
				if(str_composition !=""){
					str_composition += ',';
				}
				weft_str += txtWeftCount + 'x' + spandexarr[cboWeftType]+ "_"+compositionArr[cboWeftCom];;
				weft_id_str += txtWarpCount + "_"+cboWeftType+ "_"+cboWeftCom;;
				str_composition += compositionArr[cboWeftCom];
				calculated_gsm += ( ( txt_ppi * 1 ) / ( txtWeftCount * 1) ) * 23.25;
			}

			var txt_wrap_spandex = document.getElementById('txt_wrap_spandex').value;
			var wrap_spn = '';
			if(txt_wrap_spandex.length > 0 )
			{ 
				wrap_spn = "+" +txt_wrap_spandex + "D" ;
			}

			var txt_weft_spandex = document.getElementById('txt_weft_spandex').value;
			var weft_spn = '';
			if(txt_weft_spandex.length > 0 )
			{
				weft_spn = "+" +txt_weft_spandex + "D" ;
			}

			document.getElementById('hidfabconsname').value = txt_epi + "x" + txt_ppi + "/" + wrap_str + wrap_spn + "x" + weft_str + weft_spn;
			console.log(txt_epi + "x" + txt_ppi + "/" + wrap_str + wrap_spn + "x" + weft_str + weft_spn);
			document.getElementById('fab_construction').value = txt_epi + "*" + txt_ppi + "*" + wrap_id_str + "*" + txt_wrap_spandex + "*" + weft_id_str + "*" + txt_weft_spandex;
			document.getElementById('txt_calculated_gsm').value = calculated_gsm;
			document.getElementById('txt_composition').value = str_composition;
			parent.emailwindow.hide();
		}

		function add_wrap(i) 
		{
			var row_num=$('#tbl_warp_list tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				 $("#tbl_warp_list tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_warp_list tbody");
				$('#wrapInc_'+i).removeAttr("onClick").attr("onClick","add_wrap("+i+");");
				$('#wrapDecre_'+i).removeAttr("onClick").attr("onClick","delete_wrap("+i+");");
			}
		}

		function delete_wrap(rowNo)
		{
			var index=rowNo-1;
			$("#tbl_warp_list tbody tr:eq("+index+")").remove();
			var numRow = $('#tbl_warp_list tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_warp_list tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});

					$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
					$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
				});
	        }
		}

		function add_weft(i)
		{

			var row_num=$('#tbl_weft_list tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				 $("#tbl_weft_list tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_weft_list tbody");
				$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
				$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
			}
		}

		function delete_weft(rowNo)
		{
			var index=rowNo-1;
			$("#tbl_weft_list tbody tr:eq("+index+")").remove();
			var numRow = $('#tbl_weft_list tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_weft_list tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});

					$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
					$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
				});
	        }
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(`search${x}`).style.backgroundColor = ( newColor == document.getElementById(`search${x}`).style.backgroundColor )? origColor : newColor;
		}
		


    </script>
	</head>
	<body>
		
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="850" class="rpt_table">
	        		<thead>
	        			<tr>
	        				<th>EPI</th>
	        				<th>PPI</th>
	        				<th>Warp Count</th>
	        				<th>Weft Count</th>
	        					<input type="hidden" name="hidfabconspid" id="hidfabconspid" value="" >
	                            <input type="hidden" name="hidfabconsname" id="hidfabconsname" value="" >
	                            <input type="hidden" name="fab_construction" id="fab_construction" value="" >
	                            <input type="hidden" name="txt_calculated_gsm" id="txt_calculated_gsm" value="" >
								<input type="hidden" name="txt_composition" id="txt_composition" value="" >
	        			</tr>
	        		</thead>
	        		<tbody>
					
	        			<tr>
	        				<td>
	        					<input type="text" name="txt_epi" class="text_boxes" id="txt_epi" value="" style="width:70px">
	        				</td>
	        				<td>
	        					<input type="text" name="txt_ppi" class="text_boxes" id="txt_ppi" value="" style="width:70px">
	        				</td>
	        				<td width="430">
	        					<table>
	        						<tr>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="330" class="rpt_table" id="tbl_warp_list">
	        									<thead>
	        										<tr>
	        											<th>Count</th>
	        											<th>Type</th>
	        											<th>Composition</th>       
	        											<th>Action</th>
	        										</tr>
	        									</thead>	
	        									<tbody>
	        										<tr>
	        											<td>
	        												<input type="text" name="txtWarpCount_1" class="text_boxes" id="txtWarpCount_1" value="" style="width:70px">
	        											</td>
	        											<td>
	        												
	        												<? echo create_drop_down( "cboWarpType_1", 70, $spandex_arr, "",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											<td>
	        												
	        												<? echo create_drop_down( "cboWarpCom_1", 100, "select id, composition_name, yarn_category_type, status_active,is_fabric from lib_composition_array where status_active in(1,2) and is_deleted=0", "id,composition_name",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											<td>
	        												<input type="button" name="wrapInc_1" id="wrapInc_1" class="formbutton" value="+" onclick="add_wrap(1)" style="width:30px;">
	        												<input type="button" name="wrapDecre_1" id="wrapDecre_1" class="formbutton" value="-" onclick="delete_wrap(1)" style="width:30px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="90" class="rpt_table" >
	        									<thead>
	        										<tr>
	        											<th>Spandex</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												
	        												<input type="text" name="txt_wrap_spandex" id="txt_wrap_spandex" class="text_boxes" style="width:70px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        						</tr>
	        					</table>
	        					
	        					
	        				</td>
	        				<td width="430">
	        					<table>
	        						<tr>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="330" class="rpt_table" id="tbl_weft_list">
	        									<thead>
	        										<tr>
	        											<th>Count</th>
	        											<th>Type</th>
	        											<th>Composition</th>       
	        											<th>Action</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												<input type="text" name="txtWeftCount_1" class="text_boxes" id="txtWeftCount_1" value="" style="width:70px">
	        											</td>
	        											<td>
	        												<? echo create_drop_down( "cboWeftType_1", 70, $spandex_arr, "",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											<td>
	        												
	        												<? echo create_drop_down( "cboWeftCom_1", 100, "select id, composition_name, yarn_category_type, status_active,is_fabric from lib_composition_array where status_active in(1,2) and is_deleted=0", "id,composition_name",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											<td>
	        												<input type="button" name="weftInc_1" id="weftInc_1" class="formbutton" value="+" onclick="add_weft(1)" style="width:30px;">
	        												<input type="button" name="weftDecre_1" id="weftDecre_1" class="formbutton" value="-" onclick="delete_weft(1)" style="width:30px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="90" class="rpt_table" >
	        									<thead>
	        										<tr>
	        											<th>Spandex</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												
	        												<input type="text" name="txt_weft_spandex" id="txt_weft_spandex" class="text_boxes" style="width:70px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        						</tr>
	        					</table>
	        					
		        			</td>
		        			
	        			</tr>
	        			
	        		</tbody>
	        	</table>  
	        </form>
	    </fieldset>
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" >
        	<thead>
                <tr>
                	<th width="30">SL</th>
                	<th>Material Construction</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:240px;overflow-y: scroll;width: 850px;">
	        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" id="fab_cons_tbl">
	            <tbody >

	                <? 
	                
	                $fabric_construction = sql_select("select id, fabric_construction_name,epi,ppi,warp_count,weft_count,lakra,wrap_spandex,weft_spandex from  lib_fabric_construction where status_active=1 and is_deleted=0 order by id desc");
	                $i=1; 
	                $epi = '';
	                $ppi = '';
	                $warp_count = '';
	                $weft_count = '';
	                $wrap_spandex = '';
	                $weft_spandex = '';
	                $fab_cons_name = '';
					$row_id = 1;
	                foreach($fabric_construction as $row) 
	                { 
	                	if($i%2==0) $bgcolor="#E9F3FF"; 
	                	else $bgcolor="#FFFFFF";
	                	$id= $row[csf('id')];
	                	
	           
	                	if($fab_construction_id == $id)
	                	{
	                		$bgcolor        ="yellow";
	                		$fab_cons_name  = $row[csf('fabric_construction_name')];
		                	$epi 			= $row[csf('epi')];
		                	$ppi 			= $row[csf('ppi')];
		                	$warp_count 	= $row[csf('warp_count')];
		                	$weft_count 	= $row[csf('weft_count')];
		                	$wrap_spandex 	= $row[csf('wrap_spandex')];
		                	$weft_spandex 	= $row[csf('weft_spandex')];
							$row_id = $i;
	                	} 
	                	$fab_cons 			= $row[csf('fabric_construction_name')];
	                	$repi 				= $row[csf('epi')];
	                	$rppi 				= $row[csf('ppi')];
	                	$rwarp_count 		= $row[csf('warp_count')];
	                	$rweft_count 		= $row[csf('weft_count')];
	                	$rwrap_spandex 		= $row[csf('wrap_spandex')];
	                	$rweft_spandex 		= $row[csf('weft_spandex')];
	                	?>
	                	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $fab_cons; ?>','<? echo $repi; ?>','<? echo $rppi; ?>','<? echo $rwarp_count; ?>','<? echo $rwrap_spandex; ?>','<? echo $rweft_count; ?>','<? echo $rweft_spandex; ?>','<? echo $i; ?>')">
	                        <td width="30"><? echo $i; ?></td>
	                        <td><? echo $fab_cons; ?> </td> 						
	                    </tr>
	                	<? 
	                	$i++; 
	            	} 
	            	?>
	            </tbody>
	    	</table>
	    </div>
    	<center><input type="button" value="Close" class="formbutton" onclick="ClosePopup()"></center>
    	
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	setFilterGrid('fab_cons_tbl',-1);
    	<?
    		if(!empty($fab_construction_id))
    		{
    			?>
    			js_set_value('<? echo $fab_construction_id; ?>','<? echo $fab_cons_name; ?>','<? echo $epi; ?>','<? echo $ppi; ?>','<? echo $warp_count; ?>','<? echo $wrap_spandex; ?>','<? echo $weft_count; ?>','<? echo $weft_spandex; ?>','<? echo $row_id; ?>');
    			<?
    		}
    	?>
    </script>
	</html>
	<?
	exit();
}

if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:430px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th>Composition
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
                    <tbody>

                    <? 
                    $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $comp_name; ?> </td> 						
                        </tr>
                    <? $i++; } ?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}


if($action=="load_php_dtls_form")
{
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
	$fabric_composition_arr=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name" );
	
	$sql="SELECT id, mst_id, constuction_id, product_type, composition_id,grey_constuction_id, weave_design, finish_type, color_id, fabric_weight, fabric_weight_type, finish_width, cutable_width, wash_type, offer_qnty, uom,buyer_target_price,amount,determination_id,warp_yarn_type,weft_yarn_type,weave,design,grey_width from wo_buyer_inquery_dtls where mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC";
	//echo $sql;
	$sql_result =sql_select($sql);  $i=1;

	$deter_ids = array();
	foreach($sql_result as $row)
	{
		if(!empty($row[csf('determination_id')]))
		{
			$deter_ids[$row[csf('determination_id')]] = $row[csf('determination_id')];
		}
		
	}

	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=581 and a.id in (".implode(",", $deter_ids).") order by a.id,b.id";
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


	if(count($sql_result)>0)
	{
		$yarnComArr=array();
		foreach($sql_result as $row)
		{
			$constuction_id=$row[csf("constuction_id")];
			$fabric_construction=return_field_value("epi || '*' || ppi ||'*' || warp_count || '*' || wrap_spandex || '*' || weft_count || '*' || weft_spandex as fab_con", "lib_fabric_construction", "id =$constuction_id", "fab_con");
			$fabric_warp_count=return_field_value("warp_count ", "lib_fabric_construction", "id =$constuction_id", "warp_count");
			$fabric_weft_count=return_field_value("weft_count ", "lib_fabric_construction", "id =$constuction_id", "weft_count");
			list($count,$type,$comId)=explode("_",$fabric_warp_count);
			list($count,$type,$comIds)=explode("_",$fabric_weft_count);
			$com=$fabric_composition_arr[$comId];
			$coms=$fabric_composition_arr[$comIds];
			$yarnComArr[$com] = $com;
			$yarnComArr[$coms] = $coms;
			$composition_str = "";
			if(!empty($row[csf('determination_id')]))
			{
				$composition_str = $composition_arr[$row[csf('determination_id')]];
			}
			else
			{
				$compos = explode(",",$row[csf('composition_id')]);
				foreach($compos as $comp)
				{
					if($composition_str != "") $composition_str .= ",";
					$composition_str .= $composition[$comp];
				}
			}
			$warp_yarn_type_arr=explode(",",$row[csf('warp_yarn_type')]);
			foreach($warp_yarn_type_arr as $val){
				$warp_yarn_type.=$yarn_type[$val].",";
			}
			$weft_yarn_type_arr=explode(",",$row[csf('weft_yarn_type')]);
			foreach($weft_yarn_type_arr as $val){
				$weft_yarn_type.=$yarn_type[$val].",";
			}
			
			?>
			
			<tr id="tr_<?=$i;?>" style="height:10px;" class="general">
				<td> 
					<input type="checkbox" id="checkBox_<?=$i;?>"  name="checkBox_<?=$i;?>" <? if(!empty($row[csf('determination_id')])){echo "checked";}?>  />
				</td>
				<td >
					<input type="text" id="txtconstruction_<?=$i;?>"  name="txtconstruction_<?=$i;?>" class="text_boxes" style="width:90px" placeholder="Browse" ondblclick="openmypage_fabric_cons(1,<?=$i;?>)" readonly value="<?=$fabric_construction_name_arr[$row[csf('constuction_id')]];?>" />
					<input type="hidden" id="fabConstructionId_<?=$i;?>"  name="fabConstructionId_<?=$i;?>"  value="<?=$constuction_id;?>" />
					<input type="hidden" id="fabConstruction_<?=$i;?>"  name="fabConstruction_<?=$i;?>"  value="<?=$fabric_construction;?>" />
					<input type="hidden" id="yarnCountDeterminationId_<?=$i;?>"  name="yarnCountDeterminationId_<?=$i;?>"  value="" />
				</td>
				<td> 
					<input type="text" id="yarnComposition_<?=$i;?>"  name="yarnComposition_<?=$i;?>" class="text_boxes" value="<?=implode(",",$yarnComArr);?>" disabled />
				</td>
				<td >
					<input type="text" id="txtgreyconstruction_<?=$i;?>"  name="txtgreyconstruction_<?=$i;?>" class="text_boxes" style="width:90px" placeholder="Browse" ondblclick="openmypage_fabric_cons(2,<?=$i;?>)" readonly value="<?=$fabric_construction_name_arr[$row[csf('grey_constuction_id')]];?>"  disabled/>
					<input type="hidden" id="greyConstructionId_<?=$i;?>"  name="greyConstructionId_<?=$i;?>"  value="<?=$constuction_id;?>" />
					<input type="hidden" id="greyConstruction_<?=$i;?>"  name="greyConstruction_<?=$i;?>"  value="<?=$fabric_construction;?>" />
					<input type="hidden" id="greyYarnCountDeterminationId_<?=$i;?>"  name="greyYarnCountDeterminationId_<?=$i;?>"  value="" />
				</td>
				<td >
					<? 

						echo create_drop_down("cboProductType_".$i, 100, $color_type, "", 1, "Select", $row[csf('product_type')], "");
					?>
						
				</td>
				<td >
					<input type="text" id="txtcompone_<?=$i;?>"  name="txtcompone_<?=$i;?>"  class="text_boxes" style="width:90px" value="<? echo $composition_str; ?>" readonly placeholder="Browse" onDblClick="openmypage_comp(<?=$i;?>);" />
	        		<input type="hidden" id="cbocompone_<?=$i;?>"  name="cbocompone_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$row[csf('composition_id')];?>" />
				</td>
				<td>
					<input type="text" id="txtWarpYarnType_<?=$i;?>"  name="txtWarpYarnType_<?=$i;?>"  class="text_boxes" style="width:90px" value="<?=rtrim($warp_yarn_type,",");?>" readonly placeholder="Browse" onDblClick="openmypage_yarnType(1,<?=$i;?>);" />
					<input type="hidden" id="hiddWarpYarnTypeId_<?=$i;?>"  name="hiddWarpYarnTypeId_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$row[csf('warp_yarn_type')];?>" />
					
				</td>
				<td>
					<input type="text" id="txtWeftYarnType_<?=$i;?>"  name="txtWeftYarnType_<?=$i;?>"  class="text_boxes" style="width:90px" value="<?=rtrim($weft_yarn_type,",");?>" readonly placeholder="Browse" onDblClick="openmypage_yarnType(2,<?=$i;?>);" />
					<input type="hidden" id="hiddWeftYarnTypeId_<?=$i;?>"  name="hiddWeftYarnTypeId_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$row[csf('weft_yarn_type')];?>" />
					
				</td>

				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txtWeave_<?=$i;?>" id="txtWeave_<?=$i;?>" placeholder="Write" value="<?=$row[csf('weave')];?>"/>
				</td>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txtDesign_<?=$i;?>" id="txtDesign_<?=$i;?>" placeholder="Write"  value="<?=$row[csf('design')];?>"/>
				</td>
				<td >
					<? 
						$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
						echo create_drop_down("cboFinishType_".$i, 70, $finish_types, "", 1, "Select", $row[csf('finish_type')], "");
					?>
						
				</td>
				<td id="color_<?=$i;?>">
					<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_<?=$i;?>" id="txtColor_<?=$i;?>" onkeyup="show_color(<?=$i;?>)" placeholder="Write" value="<?=$color_arr[$row[csf('color_id')]];?>" />
					<input  type="hidden"  name="cboColorId_<?=$i;?>" id="cboColorId_<?=$i;?>" value="<?=$row[csf('color_id')];?>"  />
				</td>
				<td >
					<input style="width:70px;" type="text" class="text_boxes"  name="txtFabricWeight_<?=$i;?>" id="txtFabricWeight_<?=$i;?>" value="<?=$row[csf('fabric_weight')];?>" placeholder="Write" />
				</td>
				<td >
					<? echo create_drop_down( "cboweighttype_".$i, 80, $fabric_weight_type,"", 1, "-- Select --", $row[csf('fabric_weight_type')], "",$disabled,"" ); ?>
				</td>
				
				<td >
					<input style="width:70px;" type="text" class="text_boxes"  name="txtFinishedWidth_<?=$i;?>" id="txtFinishedWidth_<?=$i;?>" placeholder="Write" value="<?=$row[csf('finish_width')];?>"  />
				</td>
				<td >
					<input style="width:70px;" type="text" class="text_boxes" placeholder="Write"  name="txtCutableWidth_<?=$i;?>" id="txtCutableWidth_<?=$i;?>" value="<?=$row[csf('cutable_width')];?>" />
				</td>
				<td >
					<input style="width:70px;" type="text" class="text_boxes" placeholder="Write"  name="txtGreyWidth_<?=$i;?>" id="txtGreyWidth_<?=$i;?>" value="<?=$row[csf('grey_width')];?>" />
				</td>
				<td >
					<? 
						$wash_types = array(1=>"Wash",2=>"Non-Wash",3=>"Garmnets Wash",4=>"Enzyme Wash");
						echo create_drop_down("cboWashType_".$i, 70, $wash_types, "", 1, "Select", $row[csf('wash_type')], "");
					?>
				</td>
	            
				<td>
					<input type="text" class="text_boxes_numeric" name="txtOfferQty_<?=$i;?>" id="txtOfferQty_<?=$i;?>" placeholder="Write"  style="width:70px;" value="<?=$row[csf('offer_qnty')];?>" onkeyup="calculate_amount(<?=$i;?>)">
				</td>
				<td >
					<?=create_drop_down("cboUom_".$i, 60, $unit_of_measurement, "", "", "", $row[csf('uom')], "", "", "23,27");?>	
				</td>
	            
				
				<td>
					<input type="text" class="text_boxes_numeric" name="txtBuyerTgtPrice_<?=$i;?>" id="txtBuyerTgtPrice_<?=$i;?>" placeholder="Write"  style="width:70px;" value="<?=$row[csf('buyer_target_price')];?>" onkeyup="calculate_amount(<?=$i;?>)">
				</td>
				<td>
					<input type="text" class="text_boxes_numeric" name="txtAmount_<?=$i;?>" id="txtAmount_<?=$i;?>" placeholder="Write"  style="width:70px;" value="<?=$row[csf('amount')];?>">
				</td>
				<td>
					<input type="button" class="image_uploader" style="width:90px" value="CLICK TO ADD IMAGE" onClick="dtls_image(<?=$i;?>)"  id="imagebutton_1" name="imageuploader_1"  />
				</td>
				<td>
					<input type="hidden" id="updateDtlsId_<?=$i;?>" name="updateDtlsId_<?=$i;?>" class="text_boxes" style="width:20px" value="<?=$row[csf('id')];?>"/>
					<input type="button" id="increase_<?=$i;?>" name="increase_<?=$i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i;?>);" />
					<input type="button" id="decrease_<?=$i;?>" name="decrease_<?=$i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i;?>);" />
				</td>
			</tr>
			<?
			$i++;
		}
	}
	exit();
}

if($action=="fabric_determination_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "testing";die;
	?>

	<script>
		function js_set_value(data)
		{
			//alert(data)
			var data=data.split('_');
			document.getElementById('determination_id').value=data[0];
			document.getElementById('construction_id').value=trim(data[1]);
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('grey_construction').value=trim(data[3]);
			document.getElementById('grey_construction_id').value=trim(data[4]);
			document.getElementById('grey_width').value=trim(data[5]);
			document.getElementById('cutable_width').value=trim(data[6]);
			document.getElementById('full_width').value=trim(data[7]);
			document.getElementById('gsm_weight').value=trim(data[8]);
			document.getElementById('weight_type').value=trim(data[9]);
			
			

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
	                        <th>Construction</th>
	                        <th>GSM/Weight</th>
	                        <th>
	                        	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
	                        	
	                        </th>
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $yarnCountDeterminationId; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value, 'determination_search_list_view', 'search_div', 'buyer_inquery_for_textile_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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

if($action=="determination_search_list_view")
{
	
	extract($_REQUEST);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
    $user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$composition_data)=explode('**',$data);
	if($construction!=''){$search_con = " and a.construction like('%".trim($construction)."%')";}
    if($gsm_weight!=''){$search_con  .= " and a.gsm_weight like('%".trim($gsm_weight)."%')";}

	?>

	</head>
	<body>
		<div align="center">
		    <form>
		        <input type="hidden" id="determination_id">
	            <input type="hidden" id="construction">
	            <input type="hidden" id="construction_id">
				<input type="hidden" id="grey_construction">

	            <input type="hidden" id="grey_construction_id">
				<input type="hidden" id="grey_width">
				<input type="hidden" id="cutable_width">
				<input type="hidden" id="full_width">
				<input type="hidden" id="gsm_weight">
				<input type="hidden" id="weight_type">
		    </form>

			<?
				$composition_arr=array();
			   
			    $sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=581 order by a.id,b.id";
			    //echo $sql;die;
			    $table_width='930';
			    $table_width2='950';
			    

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

			    

			?>
			<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					
					    <tr>
					        <th width="50">SL No</th>
					        <th width="100">Fab Nature</th>
							<th width="100">Fabric Ref(Mill)</th>
					        <th width="100">Construction</th>
					        <th width="100">GSM/Weight</th>
					        <th width="100">Color Range</th>
					        <th width="90">Stich Length</th>
					        <th width="50">Process Loss</th>
					        <th>Composition</th>
					    </tr>
					
				</thead>
			</table>
			<!-- <div id="" style="max-height:350px; width:948px; overflow-y:scroll"> -->
			<table id="list_view" class="rpt_table" width="<? echo $table_width; ?>" height="" cellspacing="0" cellpadding="0" border="1" rules="all" style="max-height:350px; width:948px; overflow-y:scroll">
				<tbody>
					<?
					    
				        $sql_data=sql_select("select a.fab_nature_id, a.construction,a.grey_construction,a.grey_construction_id, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.id,a.fabric_construction_id as construction_id,a.fabric_ref,a.grey_width,a.cutable_width,a.full_width,a.weight_type from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.entry_form=581   $search_con and  a.is_deleted=0 group by a.id,a.fab_nature_id,a.construction,a.grey_construction,a.grey_construction_id,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.fabric_construction_id,a.fabric_ref,a.grey_width,a.cutable_width,a.full_width,a.weight_type order by a.id");
						 

				        $i=1;
				        foreach($sql_data as $row)
				        {
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";S
					        ?>
					        <tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('construction_id')]."_".$row[csf('construction')]."_".$row[csf('grey_construction')]."_".$row[csf('grey_construction_id')]."_".$row[csf('grey_width')]."_".$row[csf('cutable_width')]."_".$row[csf('full_width')]."_".$row[csf('gsm_weight')]."_".$row[csf('weight_type')] ?>')">
					        <td width="50"><? echo $i; ?></td>
					        <td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
							<td width="100" align="left"><? echo $row[csf('fabric_ref')]; ?></td>
					        <td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
					        <td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
					        <td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
					        <td width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
					        <td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
					        <td><? echo $composition_arr[$row[csf('id')]]; ?></td>
					        </tr>

					        <?
					        $i++;
					    }
					    
					?>
				</tbody>
			</table>
		</div>
	</body>
	</html>
	<?
}

if($action=="yarn_type_popup")
{
  	echo load_html_head_contents("Buyer Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
 ?>
	<script>
		var is_disable='<?=$is_disable; ?>';

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

				
			tbl_row_count = tbl_row_count-1;
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

		function set_all()
		{
			var old=document.getElementById('txt_buyer_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{
			 
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_buyer_id').val(id);
			$('#hidden_buyer_name').val(name);
		}
    </script>

 </head>
 <body>
 <div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Yarn Type Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
					<?
                    $sql_buyer=sql_select("select yarn_type_id,yarn_type_short_name from lib_yarn_type where is_deleted=0 and status_active=1 order by yarn_type_short_name");
                    foreach($sql_buyer as $row)
                    {
                        $buyer_arr[$row[csf('yarn_type_id')]]=$row[csf('yarn_type_short_name')];
                    }
                    $i=1; $buyer_row_id="";
                    $hidden_buyer_id=explode(",",$txt_tag_buyer_id);
                    asort($buyer_arr);
                    foreach($buyer_arr as $id=>$name)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$is_remove=0;
                        // if(in_array($id,$hidden_buyer_id))
                        // {
                        //     if($buyer_row_id=="") $buyer_row_id=$i; else $buyer_row_id.=",".$i;
						// 	$is_remove=1;
                        // }
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value('<?=$i; ?>');">
                            <td width="50" align="center"><?=$i; ?>
                                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$id; ?>"/>
                                <input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$name; ?>"/>
                                <input type="hidden" name="txt_mandatory" id="txt_mandatory<?=$i; ?>" value="<?=$mandatory; ?>"/>
                            </td>
                            <td style="word-break:break-all"><?=$name; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <input type="hidden" name="txt_buyer_row_id" id="txt_buyer_row_id" value="<?=$buyer_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
 </div>
 </body>
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 <script>set_all();</script>
 </html>
 <?
 exit();
}
?>