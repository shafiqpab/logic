﻿<?
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

if ($brand_id !='' && $brand_id!=0) {
    $brand_cond = " and id in ( $brand_id)";
}

if($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where b.team_id='$data' and a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "--Select Merchant--", $selected, "" );
	exit();
}

if($action=="load_drop_down_sample_marchant")
{
	echo create_drop_down( "cbo_concern_marchant", 130, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where b.team_id='$data' and a.id=b.team_id and  a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "--Select Merchant--", $selected, "" );
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
		echo create_drop_down( "cbo_season_name", 130, "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ","id,season_name", 1, "-Season-", $selected,"" );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", 130, " ","", 1, "-Season-", $selected,"" );
	}
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 130, "select id, season_name from lib_buyer_season where buyer_id='$datas[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	list($buyer_id,$width)=explode('_',$data);
	$width=($width)?$width:130;
	echo create_drop_down( "cbo_brand", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_change_brand")
{
	list($buyer_id,$selected)=explode('**',$data);
	echo create_drop_down( "cbo_change_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/quotation_inquery_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td'); load_drop_down( 'requires/quotation_inquery_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
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
                <th width="130">Buyer Name</th>
                <th width="100">Inquiry ID</th>
                <th width="130">Season</th>
                <th width="50">Season Year</th>
                <th width="70">Brand</th>
                <th width="100">M.Style Ref/Name.</th>
                <th width="8">Buyer Inquiry No</th>
                <th width="70">Inquiry Date </th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1); ?></td>
                <td><? echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'quotation_inquery_controller', this.value, 'load_drop_down_season_buyer', 'season_td'); load_drop_down( 'quotation_inquery_controller', this.value+'_70', 'load_drop_down_brand', 'brand_td'); " ); ?></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
                
                <td id="season_td"><? echo create_drop_down( "cbo_season_name", 130, $season_buyer_wise_arr,"", 1, "- Select- ", "", "" ); ?></td>
                <td><? echo create_drop_down( "cbo_year", 50, $year,"", 1, "- Select- ","", "" ); ?></td>
                <td id="brand_td"><? echo create_drop_down( "cbo_brand", 70, $brandArr,"", 1, "- Select- ", "", "" ); ?></td>
                
                <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                <td><input type="text" style="width:70px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('cbo_brand').value, 'create_mrr_search_list_view', 'search_div', 'quotation_inquery_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
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
	
	if($_SESSION['logic_erp']['user_level']==1 && $_SESSION['logic_erp']['single_user']==1){
		$where_con=" and INSERT_BY=".$_SESSION['logic_erp']['user_id'];
		echo "<b style='color:red;'>Note: As per user credential you are only eligible to view the data those are enter by using your ID.</b>";
	}
		
	if($season_year>0){$year_cond=" and SEASON_YEAR=$season_year";}
	
	if($season_id==0) $seson_con=""; else $seson_con=" and SEASON_BUYER_WISE=$season_id";
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
	 $sql = "select system_number_prefix_num,system_number,buyer_request, company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,extract(year from insert_date) as year, season_year, color, id, BRAND_ID from wo_quotation_inquery where is_deleted=0 and entry_form=434 $company_name $buyer_name $sql_cond $where_con $inquery_id_cond $request_no $inquery_date $year_cond $seson_con $brand_con order by id DESC ";
	   //echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquiry ID,Season,Season Year,Buyer Inquiry No,M.Style Ref/Name.,Color, Inquiry Date,Brand,Status","120,120,70,80,60,80,110,80,70,80,100","1050","260",0, $sql , "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,0,season_buyer_wise,0,0,0,00,,BRAND_ID,status_active", $arr, "company_id,buyer_id,system_number_prefix_num,season_buyer_wise,season_year,buyer_request,style_refernce,color,inquery_date,BRAND_ID,status_active", "",'','0,0,0,0,0,0,0,0,3,0,0') ;
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
	$sql="select  id, company_id, buyer_id, season_buyer_wise, season_year, brand_id, inquery_date, style_refernce, actual_sam_send_date, actual_req_quot_date, buyer_request, status_active, remarks, team_leader, dealing_marchant, gmts_item, set_break_down, total_set_qnty, set_smv, order_uom, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, department_name, buyer_target_price, buyer_submit_price, priority, con_rec_target_date, cutable_width, style_description, concern_marchant, mail_send_date, COPY_SYSTEM_NUMBER from wo_quotation_inquery where system_number='$data[0]' and entry_form=434 order by id";
	//echo $sql;die;
	$sql_result = sql_select($sql);
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
	
	//print_r($sql_result);die;
	foreach($sql_result as $row)
	{
		if($row[csf("mail_send_date")]){
			echo "document.getElementById('text_mail_send_date').value = '".$row[csf("mail_send_date")]."';\n";
			echo "document.getElementById('text_mail_send_status').innerHTML = 'Last Mail Send : ".$row[csf("mail_send_date")]."';\n";
		}
		
		$com_sql="select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.id in(".$row[csf("fabrication")].") order by a.id ASC";
		$sql_com=sql_select($com_sql);
		$text_fab="";
		foreach ($sql_com as $val) {
			$text_fab.=$val[csf("type")]." ".$val[csf("construction")]." ".$val[csf("design")]." ".$val[csf("id")] ." , ";
		}
		
		$text_fab=chop($text_fab,",");
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_buyer_target_price').value = '".$row[csf("buyer_target_price")]."';\n";
		echo "document.getElementById('txt_buyer_submit_price').value = '".$row[csf("buyer_submit_price")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "load_drop_down( 'requires/quotation_inquery_controller','".$row[csf("team_leader")]."', 'load_drop_down_dealing_merchant', 'div_marchant') ;";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "load_drop_down( 'requires/quotation_inquery_controller','".$row[csf("buyer_id")].'_'.$row[csf("company_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		
		echo "load_drop_down( 'requires/quotation_inquery_controller','".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_inquery_date').value = '".change_date_format($row[csf("inquery_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_request_no').value = '".$row[csf("buyer_request")]."';\n";
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";


		echo "document.getElementById('cbo_gmt_item').value = '".$row[csf("gmts_item")]."';\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$row[csf("fabrication")]."';\n";
		echo "document.getElementById('save_text_data').value = '".$text_fab."';\n";
		echo "document.getElementById('txt_offer_qty').value = '".$row[csf("offer_qty")]."';\n";
		echo "document.getElementById('txt_color').value = '".$row[csf("color")]."';\n";
		echo "document.getElementById('txt_color_id').value = '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_department').value = '".$row[csf("department_name")]."';\n";
		echo "document.getElementById('cbo_priority').value = '".$row[csf("priority")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('txt_req_quot_date').value = '".change_date_format($row[csf("req_quotation_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_target_samp_date').value = '".change_date_format($row[csf("target_sam_sub_date")],"dd-mm-yyyy","-")."';\n";

		echo "document.getElementById('txt_actual_sam_send_date').value = '".change_date_format($row[csf("actual_sam_send_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_actual_req_quot_date').value = '".change_date_format($row[csf("actual_req_quot_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_con_rec_target_date').value = '".change_date_format($row[csf("con_rec_target_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_cutable_width').value = '".$row[csf("cutable_width")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('cbo_concern_marchant').value = '".$row[csf("concern_marchant")]."';\n";
		echo "document.getElementById('txt_copy_sys_id').value = '".$row[COPY_SYSTEM_NUMBER]."';\n";
	}
	
	$is_add_file=return_library_array( "select id,id from COMMON_PHOTO_LIBRARY where FORM_NAME in('quotation_inquery','quotation_inquery_front_image','quotation_inquery_back_image') and master_tble_id='".$sql_result[0][csf(id)]."'", "id", "id");

	if(count($is_add_file)>0){
		echo "document.getElementById('txt_is_file_uploaded').value = 1;\n";
	}
	else{
		echo "document.getElementById('txt_is_file_uploaded').value = '';\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$txt_color_id) == ''){
		$lib_color = sql_select("select id from lib_color where LOWER(color_name)=LOWER($txt_color) and is_deleted=0 and status_active=1");
		if(count($lib_color)>0){
			$txt_color_id = $lib_color[0][csf('id')];
		}
		else{
			$color_id=return_next_id( "id", "lib_color", 1) ;
			$field_array="id,color_name,tag_buyer,inserted_by,insert_date,status_active,is_deleted";

			$data_array="(".$color_id.",".trim(strtoupper($txt_color)).",".$cbo_buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";

			//Insert Data in lib_color_tag_buyer Table----------------------------------------

			$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
			$data_array_buyer="";
			$tag_buyer=explode(',',str_replace("'","",$cbo_buyer_name));
			for($i=0; $i<count($tag_buyer); $i++)
			{
				//if($id_lib_color_tag_buyer=="") $id_lib_color_tag_buyer=return_next_id( "id", "lib_buyer_party_type", 1 ); else $id_lib_color_tag_buyer=$id_lib_color_tag_buyer+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$color_id.",".$tag_buyer[$i].")";
				$id_lib_color_tag_buyer++;
			}
			$field_array_buyer="id, color_id, buyer_id";
			$color_rID=sql_insert("lib_color",$field_array,$data_array,0);
			$color_tag_rID_1=sql_insert("lib_color_tag_buyer",$field_array_buyer,$data_array_buyer,1);
			$txt_color_id = $color_id;
		}
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_quotation_inquery", 1 ) ;
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'QIN', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from wo_quotation_inquery where company_id=$cbo_company_name and entry_form=434 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "system_number_prefix", "system_number_prefix_num" ));
		$season=str_replace("'","",$cbo_season_name);
		$field_array="id, system_number_prefix, system_number_prefix_num, system_number, company_id, entry_form, buyer_id, season_buyer_wise, season_year, brand_id, inquery_date, style_refernce, buyer_request, remarks, team_leader, dealing_marchant, gmts_item, order_uom, set_break_down, total_set_qnty, set_smv, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, priority, con_rec_target_date, cutable_width, concern_marchant, style_description, insert_by, insert_date, status_active, is_deleted";
		  $data_array ="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",434,".$cbo_buyer_name.",'".$season."',".$cbo_season_year.",".$cbo_brand.",".$txt_inquery_date.",".$txt_style_ref.",".$txt_request_no.",".$txt_remarks.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_gmt_item.",".$cbo_order_uom.",".$set_breck_down.",".$tot_set_qnty.",".$txt_sew_smv.",".$txt_est_ship_date.",".$txt_fabrication.",".$txt_offer_qty.",".$txt_color.",".$txt_color_id.",".$txt_req_quot_date.",".$txt_target_samp_date.",".$txt_actual_req_quot_date.",".$txt_actual_sam_send_date.",".$txt_department.",".$txt_buyer_target_price.",".$txt_buyer_submit_price.",".$cbo_priority.",".$txt_con_rec_target_date.",".$txt_cutable_width.",".$cbo_concern_marchant.",".$txt_style_description.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";


		$id_dtls_inq=return_next_id( "id","wo_quotation_inquery_fab_dtls", 1 ) ;
		$field_array_dtls_inq="id,mst_id,constraction,composition,gsm,inserted_by, insert_date,status_active,is_deleted";
		$save_string=explode(",",str_replace("'","",$txt_fabrication));
		//echo "10**".str_replace("'","",$txt_fabrication).'ssd';die;

		for($i=0;$i<count($save_string);$i++)
		{
			$data=explode("_",$save_string[$i]);
			$constraction=$data[0];
			$composition=$data[1];
			$gsm=$data[2];
			//echo "10**".$constraction.'='.$composition;die;
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
			if(str_replace("'","",$txt_fabrication)!='')
			{
				if ($data_array_dtls_inq!="") $data_array_dtls_inq.=",";
				$data_array_dtls_inq.="(".$id_dtls_inq.",".$id.",'".$constraction."','".$composition."','".$gsm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls_inq=$id_dtls_inq+1;
			}
		}
		
		$field_array1="id, quot_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$id1=return_next_id( "id", "  WO_QUOTATION_SET_DETAILS", 1 );
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		
		//print_r($data_array);die;
		//echo "10**insert into wo_quotation_inquery_fab_dtls($field_array_dtls_inq) values".$data_array_dtls_inq;die;
		//cbo_gmt_item*txt_est_ship_date*txt_fabrication*txt_offer_qty*txt_color*txt_req_quot_date*txt_target_samp_date
		$flag=1;
		$rID=sql_insert("wo_quotation_inquery",$field_array,$data_array,0);

		if($rID==1) $flag=1; else $flag=0;
		
		$rID5=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_dtls_inq!="")
		{
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID2=sql_insert("wo_quotation_inquery_fab_dtls",$field_array_dtls_inq,$data_array_dtls_inq,1);
			if($flag==1)
			{
				if($rID2==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'_'.$rID2.'__'.$flag; die;
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$id."**".$txt_color_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_system_id[0]."**".$id."**".$txt_color_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$id."**".$txt_color_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_system_id[0]."**".$id."**".$txt_color_id;
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
		$is_price_quot="";
		$sql=sql_select("select id from wo_price_quotation where inquery_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($is_price_quot=="") $is_price_quot=$row[csf('id')]; else $is_price_quot.=', '.$row[csf('id')];
		}
		if($is_price_quot!=""){
			echo "pricequotation**".$is_price_quot;
			disconnect($con);die;
		}
		$jobno="";
		$sql=sql_select("select job_no from wo_po_details_master where inquiry_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($jobno=="") $jobno=$row[csf('job_no')]; else $jobno.=', '.$row[csf('job_no')];
		}
		if($jobno!=""){
			echo "jobno**".$jobno;
			disconnect($con);die;
		}
		$costsheet="";
		$sql=sql_select("select cost_sheet_no from qc_mst where inquery_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($costsheet=="") $costsheet=$row[csf('cost_sheet_no')]; else $costsheet.=', '.$row[csf('cost_sheet_no')];
		}
		if($costsheet!=""){
			echo "costsheet**".$costsheet;
			disconnect($con);die;
		}

		$update_id=str_replace("'","",$update_id);
		$txt_color_id=str_replace("'","",$txt_color_id);
		
		
		/*if (is_duplicate_field( "sample_type_id", "wo_po_sample_approval_info", "job_no_mst=$txt_job_no and sample_type_id=$cbo_sample_type and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0";
			die;
		}*/

		$field_array="buyer_id*season_buyer_wise*season_year*brand_id*inquery_date*style_refernce*buyer_request*remarks*team_leader*dealing_marchant*gmts_item*order_uom*set_break_down*total_set_qnty*set_smv*est_ship_date*fabrication*offer_qty*color*color_id*req_quotation_date*target_sam_sub_date*actual_req_quot_date*actual_sam_send_date*department_name*buyer_target_price*buyer_submit_price*priority*con_rec_target_date*cutable_width*concern_marchant*style_description*update_by*update_date*status_active";
		 $data_array ="".$cbo_buyer_name."*".$cbo_season_name."*".$cbo_season_year."*".$cbo_brand."*".$txt_inquery_date."*".$txt_style_ref."*".$txt_request_no."*".$txt_remarks."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_gmt_item."*".$cbo_order_uom."*".$set_breck_down."*".$tot_set_qnty."*".$txt_sew_smv."*".$txt_est_ship_date."*".$txt_fabrication."*".$txt_offer_qty."*".$txt_color."*".$txt_color_id."*".$txt_req_quot_date."*".$txt_target_samp_date."*".$txt_actual_req_quot_date."*".$txt_actual_sam_send_date."*".$txt_department."*".$txt_buyer_target_price."*".$txt_buyer_submit_price."*".$cbo_priority."*".$txt_con_rec_target_date."*".$txt_cutable_width."*".$cbo_concern_marchant."*".$txt_style_description."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";

		$id_dtls_inq=return_next_id( "id","wo_quotation_inquery_fab_dtls", 1 ) ;
		$field_array_dtls_inq="id,mst_id,constraction,composition,gsm,inserted_by, insert_date,status_active,is_deleted";
		$save_string=explode(",",str_replace("'","",$txt_fabrication));
		for($i=0;$i<count($save_string);$i++)
		{
			$data=explode("_",$save_string[$i]);
			$constraction=$data[0];
			$composition=$data[1];
			$gsm=$data[2];
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
			if($constraction)
			{
				if ($data_array_dtls_inq!="") $data_array_dtls_inq.=",";
				$data_array_dtls_inq.="(".$id_dtls_inq.",".$update_id.",'".$constraction."','".$composition."','".$gsm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls_inq=$id_dtls_inq+1;
			}
		}
		
		$field_array1="id, quot_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$add_comma=0;
	 	$id1=return_next_id( "id", "  WO_QUOTATION_SET_DETAILS", 1 ) ;
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
		$rID5=execute_query( "delete from WO_QUOTATION_SET_DETAILS where quot_id =".$update_id."",0);
		if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		
		
		 //print_r( $data_array);die;
		$rID=sql_update("wo_quotation_inquery",$field_array,$data_array,"id","".$update_id."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$delete_fab_inq_dtls=execute_query( "delete from wo_quotation_inquery_fab_dtls where mst_id=$update_id",0);
		if($delete_fab_inq_dtls==1 && $flag==1) $flag=1; else $flag=0;

		if($data_array_dtls_inq!="")
		{
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID2=sql_insert("wo_quotation_inquery_fab_dtls",$field_array_dtls_inq,$data_array_dtls_inq,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		$rID6=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($rID6==1 && $flag==1) $flag=1; else $flag=0;
		
		//$txt_system_id=str_replace("'","",$txt_system_id);
		
		
	 //echo "10**".$rID."**".$delete_fab_inq_dtls."**".$rID2."**".$flag;oci_rollback($con);die;
		
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_id)."**".$update_id."**".$txt_color_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id)."**".$update_id."**".$txt_color_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".$txt_system_id."**".$update_id."**".$txt_color_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_system_id."**".$update_id."**".$txt_color_id;
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
		$is_price_quot="";
		$sql=sql_select("select id from wo_price_quotation where inquery_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($is_price_quot=="") $is_price_quot=$row[csf('id')]; else $is_price_quot.=', '.$row[csf('id')];
		}
		if($is_price_quot!=""){
			echo "pricequotation**".$is_price_quot;
			disconnect($con);die;
		}
		$jobno="";
		$sql=sql_select("select job_no from wo_po_details_master where inquiry_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($jobno=="") $jobno=$row[csf('job_no')]; else $jobno.=', '.$row[csf('job_no')];
		}
		if($jobno!=""){
			echo "jobno**".$jobno;
			disconnect($con);die;
		}
		$costsheet="";
		$sql=sql_select("select cost_sheet_no from qc_mst where inquery_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($costsheet=="") $costsheet=$row[csf('cost_sheet_no')]; else $costsheet.=', '.$row[csf('cost_sheet_no')];
		}
		if($costsheet!=""){
			echo "costsheet**".$costsheet;
			disconnect($con);die;
		}

		$field_arrmst="update_by*update_date*status_active*is_deleted";
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_quotation_inquery",$field_arrmst,$data_array,"id","".$update_id."",1);
		$rID1=sql_delete("wo_quotation_inquery_fab_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
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

if ($action=="buyer_inquery_fab_popup")
{
	echo load_html_head_contents("Fabric Details Entry", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $save_data;
?>
	<script>
	var permission='<? echo $permission; ?>';
	function fn_addRow_fab(i)
	{
		var row_num=$('#tbl_list tbody tr').length;
		
		//alert(row_num);
		//alert(lastTrId[1]);
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_list tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_list");

			$('#slTd_'+i).val('');
			$('#txtrdno_'+i).val('');
			$('#txtrdno_'+i).removeAttr("onBlur").attr("onBlur","fnc_librdno("+i+','+0+");");
			$('#txtrdno_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_newfab("+i+");");
			$('#txtfabref_'+i).val('');
			$('#txtfabtype_'+i).val('');
			$('#txtconstraction_'+i).val('');
			$('#txtdesign_'+i).val('');
			$('#txtweight_'+i).val('');
			$('#txtweighttype_'+i).val('');
			$('#txtcolorrange_'+i).val('');
			$('#txtfullwidth_'+i).val('');
			$('#txtcutablewidth_'+i).val('');
			$('#txtcomposition_'+i).val('');
			
			$("#tbl_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
			$('#tr_' + i).find("td:eq(0)").text(i);

			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","fn_addRow_fab("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
		}
		set_all_onclick();
	}

	function fn_deleteRow(rowNo)
	{
		var row_num=$('#tbl_list tbody tr').length;

		if(row_num!=1)
		{
			$("#tr_"+rowNo).remove();
		}
	}

	function window_close()
	{
		var save_data=''; var tot_trims_qnty='';
		var save_text_data='';
		$("#tbl_list").find('tr').each(function()
		{
			var hiddFabDeterId=$(this).find('input[name="hiddFabDeterId[]"]').val();
			var txtfabtype=$(this).find('input[name="txtfabtype[]"]').val();
			var txtconstraction=$(this).find('input[name="txtconstraction[]"]').val();
			var txtdesign=$(this).find('input[name="txtdesign[]"]').val();
			var txtcomposition=$(this).find('input[name="txtcomposition[]"]').val();
			if(hiddFabDeterId)
			{
				//alert(txtconstraction);
				if(save_data=="") save_data=hiddFabDeterId;
				else save_data+=","+hiddFabDeterId;
				if(save_text_data=="") save_text_data=txtfabtype+" "+txtconstraction+" "+txtdesign+" "+txtcomposition;
				else save_text_data+=" , "+txtfabtype+" "+txtconstraction+" "+txtdesign+" "+txtcomposition;
			}
		});
		//alert(save_data);
		$('#save_data').val( save_data );
		$('#save_text_data').val( save_text_data );
		parent.emailwindow.hide();
	}
	
	function fnc_librdno(incid, type)
	{
		var rdno=$('#txtrdno_'+incid).val();
		if(type==0) var libid=0;
		else if(type==1) var libid=$('#hiddFabDeterId_'+incid).val();
		//alert(libid);
		/*if(trim(rdno)!="" || libid!=0)
		{*/
			get_php_form_data(trim(rdno)+'___'+incid+'___'+libid, "populate_data_from_rdnolib", "quotation_inquery_controller" );
		//}
	}
	
	function openmypage_newfab(incid)
	{
		var page_link='quotation_inquery_controller.php?action=fabpopup&incid='+incid;
		var title="Fabric Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=350px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];

			var fabdata=this.contentDoc.getElementById("hid_libDes").value; // mrr number
			var exfabdata = fabdata.split("_");
			
			//1090_d_66CW_2X Twill S/D_14X12_86X50_245___
			
			$("#hiddFabDeterId_"+incid).val(exfabdata[0]);
			$("#txtrdno_"+incid).val(exfabdata[1]);
			$("#txtfabref_"+incid).val(exfabdata[2]);
			$("#txtfabtype_"+incid).val(exfabdata[3]);
			$("#txtconstraction_"+incid).val(exfabdata[4]);
			$("#txtdesign_"+incid).val(exfabdata[5]);
			$("#txtweight_"+incid).val(exfabdata[6]);
			$("#txtweighttype_"+incid).val(exfabdata[7]);
			$("#txtcolorrange_"+incid).val(exfabdata[8]);
			$("#txtcomposition_"+incid).val(exfabdata[9]);
			$('#txtfullwidth_'+incid).val(exfabdata[10]);
			$('#txtcutablewidth_'+incid).val(exfabdata[11]);
			$('#txtshrinkagel_'+incid).val(exfabdata[12]);
			$('#txtshrinkagew_'+incid).val(exfabdata[13]);
		}
	}
    </script>

</head>

<body>
<div align="center">
	<? //echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="trimsWeight_1" id="trimsWeight_1">
        <fieldset style="width:1220px;">
            <legend>Fabrication Details Pop Up</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1220" id="tbl_list">
            	<thead>
                    <th style="word-break: break-all;" width="20">SL</th>
                    <th style="word-break: break-all;" width="80">RD No</th>
                    <th style="word-break: break-all;" width="80">Fabric Ref</th>
                    <th style="word-break: break-all;" width="80">Type</th>
                    <th style="word-break: break-all;" width="90">Constraction</th>
                    <th style="word-break: break-all;" width="80">Design</th>
                    <th style="word-break: break-all;" width="70">Weight</th>
                    <th style="word-break: break-all;" width="70">Weight Type</th>
                    <th style="word-break: break-all;" width="80">Color Range</th>
                    <th style="word-break: break-all;" width="50">Full Width</th>
                    <th style="word-break: break-all;" width="50">Cutable Width</th>
                    <th style="word-break: break-all;" width="50">Shrinkage L</th>
                    <th style="word-break: break-all;" width="50">Shrinkage W</th>
                    <th style="word-break: break-all;" width="140">Composition</th>
                    <th style="word-break: break-all;">&nbsp;</th>
                    	<input type="hidden" name="save_data" id="save_data" class="text_boxes">
                    	<input type="hidden" name="save_text_data" id="save_text_data" class="text_boxes">
                </thead>
                <tbody>
					<?
                    if($save_data!="")
                    {
                        $tot_trims_wgt=0;$k=0;
                        $explSaveData = explode(",",$save_data);
                        for($z=0; $z<count($explSaveData); $z++)
                        {
                            $k++;
                        ?>
                            <tr id="tr_<?=$k;?>">
                                <td style="word-break: break-all;"  width="20" id="slTd_<?=$k;?>" align="center"><?=$k;?></td>
                                <td style="word-break: break-all;" >
                                    <input type="text" name="txtrdno[]" id="txtrdno_<?=$k; ?>" class="text_boxes" style="width:70px;" placeholder="Wr/Br" onBlur="fnc_librdno(<?=$k; ?>,0);" onDblClick="openmypage_newfab(<?=$k; ?>);"/>
                                    <input type="hidden" name="hiddFabDeterId[]" id="hiddFabDeterId_<?=$k; ?>" class="text_boxes" style="width:40px;" value="<?=$explSaveData[$z]; ?>"/>
                                    <input type="hidden" id="btnnewfab_<?=$k; ?>" class="formbutton" style="width:20px; font-style:italic" value="N" />
                                </td>
                                <td style="word-break: break-all;" ><input type="text" name="txtfabref[]" id="txtfabref_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            	<td style="word-break: break-all;" ><input type="text" name="txtfabtype[]" id="txtfabtype_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display" /></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtconstraction[]" id="txtconstraction_<?=$k; ?>" class="text_boxes" style="width:80px;" value="" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtdesign[]" id="txtdesign_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtweight[]" id="txtweight_<?=$k; ?>" class="text_boxes" style="width:60px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtweighttype[]" id="txtweighttype_<?=$k; ?>" class="text_boxes" style="width:60px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtcolorrange[]" id="txtcolorrange_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtfullwidth[]" id="txtfullwidth_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtcutable_width[]" id="txtcutablewidth_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>

                                <td style="word-break: break-all;" ><input type="text" name="txtshrinkagel[]" id="txtshrinkagel_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>

                                <td style="word-break: break-all;" ><input type="text" name="txtshrinkagew[]" id="txtshrinkagew_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>

                                <td style="word-break: break-all;" ><input type="text" name="txtcomposition[]" id="txtcomposition_<?=$k;?>" class="text_boxes" style="width:130px;" value="" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" >
                                    <input type="button" id="increase_<?=$k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<?=$k; ?>);" readonly/>
                                    <input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$k; ?>);" readonly/>
                                </td>
                            </tr>
                            <script>fnc_librdno(<?=$k; ?>,1);</script>
                            <?
                        }
                    }
                    else
                    { 
                    	?>
                        <tr id="tr_1">
                            <td style="word-break: break-all;" width="20" id="slTd_1" align="center">1</td>
                            <td style="word-break: break-all;">
                            	<input type="text" name="txtrdno[]" id="txtrdno_1" class="text_boxes" style="width:70px;" placeholder="Wr/Br" onBlur="fnc_librdno(1,0);" onDblClick="openmypage_newfab(1);" />
                                <input type="hidden" name="hiddFabDeterId[]" id="hiddFabDeterId_1" class="text_boxes" style="width:40px;"/>
                            	<input type="hidden" id="btnnewfab_1" class="formbutton" style="width:20px; font-style:italic" value="N" />
                                <!--'quotation_inquery_controller.php?action=newfabrication_popup','Fabric Determination',-->
                            </td>
                            <td style="word-break: break-all;"><input type="text" name="txtfabref[]" id="txtfabref_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtfabtype[]" id="txtfabtype_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtconstraction[]" id="txtconstraction_1" class="text_boxes" style="width:80px;" readonly placeholder="Display"/></td>
                            
                            <td style="word-break: break-all;"><input type="text" name="txtdesign[]" id="txtdesign_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtweight[]" id="txtweight_1" class="text_boxes" style="width:60px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtweighttype[]" id="txtweighttype_1" class="text_boxes" style="width:60px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtcolorrange[]" id="txtcolorrange_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtfullwidth[]" id="txtfullwidth_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtcutable_width[]" id="txtcutablewidth_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>

                            <td style="word-break: break-all;"><input type="text" name="txtshrinkagel[]" id="txtshrinkagel_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>

                            <td style="word-break: break-all;"><input type="text" name="txtshrinkagew[]" id="txtshrinkagew_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            
                            <td style="word-break: break-all;"><input type="text" name="txtcomposition[]" id="txtcomposition_1" class="text_boxes" style="width:130px;" readonly placeholder="Display" /></td>
                            <td style="word-break: break-all;">
                                <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1);" />
                                <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                            </td>
                        </tr>
                <? } ?>
            </tbody>
            <tfoot class="tbl_bottom">
            	<td colspan="15">&nbsp;</td>
            </tfoot>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1220">
            <tr>
            	<td align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" /></td>
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

if($action=="populate_data_from_rdnolib")
{
	$ex_data=explode("___",$data);
	
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
	
	if($ex_data[2]==0) $rdCond="and rd_no='$ex_data[0]'";
	else if($ex_data[2]!=0) $rdCond="and id in($ex_data[2]) ";
	
	$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width,shrinkage_l,shrinkage_w from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 $rdCond";
	$sqlRdData=sql_select($sqlRd);
	
	if(count($sqlRdData)>0)
	{
		echo "$('#txtrdno_".$ex_data[1]."').val('".$sqlRdData[0][csf('rd_no')]."');\n";
		echo "$('#hiddFabDeterId_".$ex_data[1]."').val('".$sqlRdData[0][csf('id')]."');\n"; 
		echo "$('#txtfabref_".$ex_data[1]."').val('".$sqlRdData[0][csf('fabric_ref')]."');\n"; 
		echo "$('#txtfabtype_".$ex_data[1]."').val('".$sqlRdData[0][csf('type')]."');\n"; 
		echo "$('#txtconstraction_".$ex_data[1]."').val('".$sqlRdData[0][csf('construction')]."');\n"; 
		echo "$('#txtdesign_".$ex_data[1]."').val('".$sqlRdData[0][csf('design')]."');\n"; 
		echo "$('#txtweight_".$ex_data[1]."').val('".$sqlRdData[0][csf('gsm_weight')]."');\n"; 
		echo "$('#txtweighttype_".$ex_data[1]."').val('".$fabric_weight_type[$sqlRdData[0][csf('weight_type')]]."');\n"; 
		echo "$('#txtcolorrange_".$ex_data[1]."').val('".$color_range[$sqlRdData[0][csf('color_range_id')]]."');\n"; 
		echo "$('#txtcomposition_".$ex_data[1]."').val('".$composition_arr[$sqlRdData[0][csf('id')]]."');\n";
		echo "$('#txtfullwidth_".$ex_data[1]."').val('".$sqlRdData[0][csf('full_width')]."');\n";
		echo "$('#txtcutablewidth_".$ex_data[1]."').val('".$sqlRdData[0][csf('cutable_width')]."');\n";
		echo "$('#txtshrinkagel_".$ex_data[1]."').val('".$sqlRdData[0][csf('shrinkage_l')]."');\n";
		echo "$('#txtshrinkagew_".$ex_data[1]."').val('".$sqlRdData[0][csf('shrinkage_w')]."');\n";
	}	
	exit();
}

if($action=="fabpopup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			document.getElementById('hid_libDes').value=trim(data);
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
                    	<th colspan="5" align="center"><?=create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                    	<th class="must_entry_caption">Fabric Nature</td>
                    	<th>RD No</th>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"><input type="hidden" id="hid_libDes" name="hid_libDes" /></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td><?=create_drop_down( "cbo_fabric_nature",100, $business_nature_arr,"", 0, "", '3', "",$disabled,"2,3,100" ); ?></td>
                    	<td><input type="text" style="width:80px" class="text_boxes" name="txt_rdno" id="txt_rdno" /></td>
                        <td><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td><input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td>
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_fabric_nature').value+'**'+'<?=$libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('txt_rdno').value, 'fabric_description_popup_search_list_view', 'search_div', 'quotation_inquery_controller', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<?=$libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div id="search_div"></div>
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

if ($action=="save_update_delete_copy")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_color_id=str_replace("'","",$txt_color_id);
	
	if($txt_color_id == ''){
		$lib_color = sql_select("select id from lib_color where LOWER(color_name)=LOWER($txt_color) and is_deleted=0 and status_active=1");
		if(count($lib_color)>0){
			$txt_color_id = $lib_color[0][csf('id')];
		}
		else{
			$color_id=return_next_id( "id", "lib_color", 1) ;
			$field_array="id,color_name,tag_buyer,inserted_by,insert_date,status_active,is_deleted";

			$data_array="(".$color_id.",".trim(strtoupper($txt_color)).",".$cbo_buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";

			//Insert Data in lib_color_tag_buyer Table----------------------------------------

			$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
			$data_array_buyer="";
			$tag_buyer=explode(',',str_replace("'","",$cbo_buyer_name));
			for($i=0; $i<count($tag_buyer); $i++)
			{
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$color_id.",".$tag_buyer[$i].")";
				$id_lib_color_tag_buyer++;
			}
			$field_array_buyer="id, color_id, buyer_id";
			$color_rID=sql_insert("lib_color",$field_array,$data_array,0);
			$color_tag_rID_1=sql_insert("lib_color_tag_buyer",$field_array_buyer,$data_array_buyer,1);
			$txt_color_id = $color_id;
		}
	}

	if ($operation==5)  // copy Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_quotation_inquery", 1 ) ;
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'QIN', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from wo_quotation_inquery where company_id=$cbo_company_name and entry_form=434 and extract(year from insert_date)=".date('Y',time())." order by id desc ", "system_number_prefix", "system_number_prefix_num" ));
		$season=str_replace("'","",$cbo_season_name);
		$field_array="id, system_number_prefix, system_number_prefix_num, system_number, company_id, entry_form, buyer_id, season_buyer_wise, season_year, brand_id, inquery_date, style_refernce, buyer_request, remarks, team_leader, dealing_marchant, gmts_item, order_uom, set_break_down, total_set_qnty, set_smv, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, priority,con_rec_target_date,cutable_width,concern_marchant,style_description, insert_by, insert_date, status_active, is_deleted,COPY_SYSTEM_NUMBER";
		  $data_array ="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",434,".$cbo_buyer_name.",'".$season."',".$cbo_season_year.",".$cbo_brand.",".$txt_inquery_date.",".$txt_style_ref.",".$txt_request_no.",".$txt_remarks.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_gmt_item.",".$cbo_order_uom.",".$set_breck_down.",".$tot_set_qnty.",".$txt_sew_smv.",".$txt_est_ship_date.",".$txt_fabrication.",".$txt_offer_qty.",".$txt_color.",'".$txt_color_id."',".$txt_req_quot_date.",".$txt_target_samp_date.",".$txt_actual_req_quot_date.",".$txt_actual_sam_send_date.",".$txt_department.",".$txt_buyer_target_price.",".$txt_buyer_submit_price.",".$cbo_priority.",".$txt_con_rec_target_date.",".$txt_cutable_width.",".$cbo_concern_marchant.",".$txt_style_description.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0,".$txt_system_id.")";

		 //echo "10**".$data_array;die;

		$id_dtls_inq=return_next_id( "id","wo_quotation_inquery_fab_dtls", 1 ) ;
		$field_array_dtls_inq="id,mst_id,constraction,composition,gsm,inserted_by, insert_date,status_active,is_deleted";
		$save_string=explode(",",str_replace("'","",$txt_fabrication));

		for($i=0;$i<count($save_string);$i++)
		{
			$data=explode("_",$save_string[$i]);
			$constraction=$data[0];
			$composition=$data[1];
			$gsm=$data[2];

			if(str_replace("'","",$txt_fabrication)!='')
			{
				if ($data_array_dtls_inq!="") $data_array_dtls_inq.=",";
				$data_array_dtls_inq.="(".$id_dtls_inq.",".$id.",'".$constraction."','".$composition."','".$gsm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls_inq=$id_dtls_inq+1;
			}
		}
		
		$field_array1="id, quot_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$id1=return_next_id( "id", "  WO_QUOTATION_SET_DETAILS", 1);
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		
		$rID=sql_insert("wo_quotation_inquery",$field_array,$data_array,0);

		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_dtls_inq!="")
		{
			$rID2=sql_insert("wo_quotation_inquery_fab_dtls",$field_array_dtls_inq,$data_array_dtls_inq,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		$rID5=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "36**".$new_system_id[0]."**".$id."**".$txt_color_id."**".str_replace("'","",$txt_system_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_system_id[0]."**".$id."**".$txt_color_id."**".str_replace("'","",$txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "36**".$new_system_id[0]."**".$id."**".$txt_color_id."**".str_replace("'","",$txt_system_id);
			}
			else{
				oci_rollback($con);
				echo "10**".$new_system_id[0]."**".$id."**".$txt_color_id."**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
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
	load_drop_down( 'quotation_inquery_controller','<?=$buyer_name.'**'.$cbo_brand;?>', 'load_drop_down_change_brand', 'load_change_brand');
	</script>
    </html>
    <?
	exit();
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
    //get_php_form_data(company_id,'set_smv_work_study','requires/style_ref_controller' );
    var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'quotation_inquery_controller');
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

      var page_link="quotation_inquery_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
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
                          <td><?=create_drop_down( "cboitem_".$i, 250, get_garments_item_array(3), "",1," -- Select Item --", $gmt_item_id_s, "check_duplicate(".$i.",this.id ); check_smv_set(".$i."); check_smv_set_popup(".$i.");",'','' ); ?>

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
                      <?=create_drop_down( "cboitem_1", 240, get_garments_item_array(3), "",1,"--Select--", $gmt_item_id_s, 'check_duplicate(1,this.id ); check_smv_set(1); check_smv_set_popup(1);','','' ); ?>
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
      {   //alert(id);
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value, 'create_item_smv_search_list_view', 'search_div', 'quotation_inquery_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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

if($action=="populate_data_from_style_popup")
{
	$ex_data=explode("_",$data);

	$data_array=sql_select("select set_break_down,total_set_qnty,set_smv,order_uom from lib_style_ref where id='$ex_data[0]'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
	}
	exit();
}
if($action=='appSubmission_withoutanyChange')
{
	$con = connect();
	$update_by=$_SESSION['logic_erp']['user_id'];
	$flag=1;
	$exdata=explode("**",$data);
	$update_id=$exdata[0];
	$est_ship_date=$exdata[1];
	//$est_ship_date=change_date_format(str_replace("'","",$est_ship_date),'yyyy-mm-dd','-');
	$offer_qty=$exdata[2];
	if($offer_qty==''){
		$offer_qty=0;
	}
	
	$company_id=$exdata[5];
	$query="UPDATE wo_quotation_inquery SET est_ship_date='$est_ship_date', offer_qty=$offer_qty, update_by=$update_by, update_date='$pc_date_time' WHERE id='$update_id' and status_active=1 and is_deleted=0";
	//echo "10**".$query; die;
	$rID1=execute_query($query,1);

	if($rID1)
	{  
		oci_commit($con);
		echo "1***".$rID1;
	}
	else
	{
		oci_rollback($con);
		echo "10**".$rID1;
	}
	disconnect($con);
	die;
}
?>