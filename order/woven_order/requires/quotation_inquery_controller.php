<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select color_from_library from variable_order_tracking where company_name=$data and variable_list = 23 and status_active=1 and is_deleted=0 order by variable_list ASC");
	$color_from_lib=0;
 	foreach($sql_result as $result)
	{
		$color_from_lib=$result[csf('color_from_library')];
	}

	$sql_style = sql_select("select style_from_library from variable_order_tracking where variable_list=47 and company_name=$data  and status_active=1 and is_deleted=0 order by id desc");
	$style_from_lib=0;
 	foreach($sql_style as $result)
	{
		$style_from_lib=$result[csf('style_from_library')];
		break;
	}

	echo $color_from_lib."**".$style_from_lib;
 	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 130, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );
	exit();
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );
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

if($action=="style_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var value = data.split('_')
			document.getElementById('style_name').value=value[1];
			document.getElementById('style_id').value=value[0];
			document.getElementById('all_data').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="style_name" name="style_name" />
            <input type="hidden" id="style_id" name="style_id" />
            <input type="hidden" name="all_data" id="all_data">

            <?
            $buyer_cond='';
           if(!empty($cbo_buyer_name))
           {
           		 $buyer_cond=" and buyer_id=$cbo_buyer_name";
           }
            $sql="select * FROM lib_style_ref  WHERE status_active=1 and is_deleted=0 $buyer_cond";
            //echo $sql;
            echo  create_list_view("list_view", "Style Ref Name", "160","340","320",0, $sql , "js_set_value", "id,style_ref_name,gmts_item_id,department_id,product_department_id,buyer_id", "", 1, "0", $arr , "style_ref_name", "",'setFilterGrid("list_view",-1);','0') ;
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
	 $season_mandatory_arr=sql_select( "select id, season_mandatory,company_name from variable_order_tracking where company_name='$data' and variable_list=44 order by id" );
	if($season_mandatory_arr[0]['season_mandatory'] == 1)
	{
	echo create_drop_down( "cbo_season_name", 130, "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ","id,season_name", 1, "-- Select Season --", $selected,"" );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", 130, " ","", 1, "-- Select Season --", $selected,"" );
	}

	exit();
}
if ($action=="load_drop_down_sew_location")
{
	$sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name";
	$result=sql_select($sql);
	$index=$selected;
	if(count($result)==1)
	{
		$index=$result[0][csf('id')];
	}
	//echo $sql."**".$index;
	echo create_drop_down( "cbo_location_name", 130, $sql,"id,location_name", 1, "-- Select --", $index, "" );	
	exit();		 
}
if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	//echo create_drop_down( "cbo_season_name", 130, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );//Dont Open if need pls contract with Kausar
	echo create_drop_down( "cbo_season_name", 130, "select id,season_name from lib_buyer_season where status_active =1 and is_deleted=0 and buyer_id='$datas[0]' order by season_name ASC","id,season_name", 1, "-- Select Season --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=130; else $width=130;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	//echo $data;
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/quotation_inquery_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/quotation_inquery_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
	exit();
}



if($action=="check_style_ref")
{
	$data=explode("**",$data);
	$style_ref=trim($data[0]);
	$id=trim($data[1]);
	$cond="";
	if($id!=""){
		$cond= " and id !=$id";
	}else{
		$cond="";
	}
	//echo "select id from wo_quotation_inquery where  style_refernce='$style_ref' $cond and status_active=1 and is_deleted=0 order by id";
	$idArr=array();
	$sql=sql_select("select id from wo_quotation_inquery where  style_refernce='$style_ref' $cond and status_active=1 and is_deleted=0 order by id");
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
        <table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                	<th colspan="8"><?=create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
                </tr>
                <tr>
                    <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">Inquiry ID</th>
                    <th width="80">Year</th>
                    <th width="150" >Style Reff.</th>
                    <th width="100" >Buyer Inquery No</th>
                    <th width="100">Inquiry Date </th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /><input type="hidden" id="hidden_issue_number" value="" /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1); ?></td>
                    <td><?=create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                    <td><input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
                    <td><?=create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>
                    <td><input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                    <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="  Date" /></td>
                    <td align="center">
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_mrr_search_list_view', 'search_div', 'quotation_inquery_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    </td>
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
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
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
	$arr=array(0=>$company_arr, 1=>$buyer_arr, 10=>$season_buyer_wise_arr, 11=>$inquery_status_arr);
	$sql = "select system_number_prefix_num, system_number, buyer_request, company_id, buyer_id,buyer_submit_price,set_smv,offer_qty, season_buyer_wise, inquery_date, style_refernce,est_ship_date, quot_status, extract(year from insert_date) as year, id from wo_quotation_inquery where is_deleted=0 and entry_form=0 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date  order by id DESC ";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Buyer Inquery No,Style Ref.,Bulk Est. Ship Date, Inquery Date,Bulk Offer Qty,Buyer Submit Price,SMV/Pcs,Season,Status","120,120,70,50,70,120,90,120,100,100,80,120,100","1320","260",0, $sql , "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,0,0,0,0,0,0,0,0,0,season_buyer_wise,quot_status", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,est_ship_date,inquery_date,offer_qty,buyer_submit_price,set_smv,season_buyer_wise,quot_status", "",'','0,0,0,0,0,0,3,3,0,0') ;
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
	$sql = sql_select("select id, company_id, buyer_id, season_buyer_wise, inquery_date, team_leader, style_refernce, actual_sam_send_date, actual_req_quot_date, buyer_request, quot_status, status_active, remarks, dealing_marchant, gmts_item, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, department_name, buyer_target_price, buyer_submit_price, bh_merchant, color_type, possible_order_con, lead_time, price_info_break_down, sample_info_break_down, product_dept, pro_sub_dep, factory_marchant, style_ref_id, set_break_down, total_set_qnty, set_smv, order_uom, location_name, style_description, brand_id, currency_id, season_year, design_source_id, qlty_label,customer_year,week_no from wo_quotation_inquery where id='$data[0]' order by id");
	$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");

	foreach($sql as $row)
	{
		$com_sql="select a.id, a.construction, a.gsm_weight, a.fabric_composition_id from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.id in(".$row[csf("fabrication")].") order by a.id ASC";
		$sql_com=sql_select($com_sql);
		$text_fab="";
		foreach ($sql_com as $val) {
			
			if($text_fab=="") $text_fab.=$val[csf("construction")]."_".$lib_fabric_composition[$val[csf("fabric_composition_id")]]."_".$val[csf("gsm_weight")]."_".$val[csf("id")];
			else $text_fab.=",".$val[csf("construction")]."_".$lib_fabric_composition[$val[csf("fabric_composition_id")]]."_".$val[csf("gsm_weight")]."_".$val[csf("id")];
		}
		
		$text_fab=chop($text_fab,",");
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		
		echo "load_drop_down( 'requires/quotation_inquery_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		echo "load_drop_down( 'requires/quotation_inquery_controller', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		
		echo "document.getElementById('cbo_dealing_merchant').value = ".$row[csf("dealing_marchant")].";\n";
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n";
		
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_buyer_target_price').value = '".$row[csf("buyer_target_price")]."';\n";
		echo "document.getElementById('txt_buyer_submit_price').value = '".$row[csf("buyer_submit_price")]."';\n";
		
		echo "load_drop_down( 'requires/quotation_inquery_controller','".$row[csf("buyer_id")].'_'.$row[csf("company_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		echo "load_drop_down( 'requires/quotation_inquery_controller','".$row[csf("buyer_id")]."*1"."', 'load_drop_down_brand', 'brand_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";	
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_design_source_id').value = '".$row[csf("design_source_id")]."';\n";
		echo "document.getElementById('cbo_qltyLabel').value = '".$row[csf("qlty_label")]."';\n";
		
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		

		echo "sub_dept_load(".$row[csf("buyer_id")].",".$row[csf("product_dept")].");";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('txt_style_id').value = '".$row[csf("style_ref_id")]."';\n";

		//echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('txt_inquery_date').value = '".change_date_format($row[csf("inquery_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_request_no').value = '".$row[csf("buyer_request")]."';\n";
		echo "document.getElementById('cbo_status').value = '".$row[csf("quot_status")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";

		echo "document.getElementById('cbo_gmt_item').value = '".$row[csf("gmts_item")]."';\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$row[csf("fabrication")]."';\n";
		echo "document.getElementById('save_text_data').value = '".$text_fab."';\n";
		echo "document.getElementById('txt_offer_qty').value = '".$row[csf("offer_qty")]."';\n";
		echo "document.getElementById('txt_color').value = '".$row[csf("color")]."';\n";
		echo "document.getElementById('txt_color_id').value = '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_department').value = '".$row[csf("department_name")]."';\n";
		echo "document.getElementById('txt_req_quot_date').value = '".change_date_format($row[csf("req_quotation_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_target_samp_date').value = '".change_date_format($row[csf("target_sam_sub_date")],"dd-mm-yyyy","-")."';\n";

		echo "document.getElementById('txt_actual_sam_send_date').value = '".change_date_format($row[csf("actual_sam_send_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_actual_req_quot_date').value = '".change_date_format($row[csf("actual_req_quot_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_bh_merchant').value = '".$row[csf("bh_merchant")]."';\n";
		echo "document.getElementById('cbo_color_type').value = '".$row[csf("color_type")]."';\n";
		echo "document.getElementById('txt_possible_order_con_date').value = '".change_date_format($row[csf("possible_order_con")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_lead_time').value = '".$row[csf("lead_time")]."';\n";
		echo "document.getElementById('price_info_break_down').value = '".$row[csf("price_info_break_down")]."';\n";
		echo "document.getElementById('sample_info_break_down').value = '".$row[csf("sample_info_break_down")]."';\n";

		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('cbo_customer_year').value = '".$row[csf("customer_year")]."';\n";
		echo "document.getElementById('cbo_week').value = '".$row[csf("week_no")]."';\n";
		echo "$('#cbo_company_name').attr('disabled',true);\n";
		echo "$('#cbo_buyer_name').attr('disabled',true);\n";
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
			$color_id=return_next_id( "id", "lib_color", 1 ) ;
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
		
		$id=return_next_id( "id", "wo_quotation_inquery", 1) ;
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'QIN', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from wo_quotation_inquery where company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "system_number_prefix", "system_number_prefix_num" ));
		
		
		$season=str_replace("'","",$cbo_season_name);
		$field_array="id, system_number_prefix, system_number_prefix_num, system_number, company_id, buyer_id, style_ref_id, product_dept, pro_sub_dep, factory_marchant, season_buyer_wise, inquery_date, team_leader, style_refernce, buyer_request, remarks, dealing_marchant, gmts_item, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, bh_merchant, color_type, possible_order_con, lead_time, price_info_break_down, sample_info_break_down, order_uom, set_break_down, total_set_qnty, set_smv, location_name, style_description, brand_id, currency_id, season_year, design_source_id, qlty_label, customer_year, week_no, quot_status, insert_by, insert_date, status_active, is_deleted";
		  $data_array ="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_id.",".$cbo_product_department.",".$cbo_sub_dept.",".$cbo_factory_merchant.",'".$season."',".$txt_inquery_date.",".$cbo_team_leader.",".$txt_style_ref.",".$txt_request_no.",".$txt_remarks.",".$cbo_dealing_merchant.",'".str_replace("'", "", $cbo_gmt_item)."',".$txt_est_ship_date.",".$txt_fabrication.",".$txt_offer_qty.",".$txt_color.",".$txt_color_id.",".$txt_req_quot_date.",".$txt_target_samp_date.",".$txt_actual_req_quot_date.",".$txt_actual_sam_send_date.",".$txt_department.",".$txt_buyer_target_price.",".$txt_buyer_submit_price.",".$txt_bh_merchant.",".$cbo_color_type.",".$txt_possible_order_con_date.",".$txt_lead_time.",".$price_info_break_down.",".$sample_info_break_down.",'".str_replace("'", "", $cbo_order_uom)."','".str_replace("'", "", $set_breck_down)."','".str_replace("'", "", $tot_set_qnty)."','".str_replace("'", "", $txt_sew_smv)."',".$cbo_location_name.",".$txt_style_description.",".$cbo_brand_id.",".$cbo_currercy.",".$cbo_season_year.",".$cbo_design_source_id.",".$cbo_qltyLabel.",".$cbo_customer_year.",".$cbo_week.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";


		$id_dtls_inq=return_next_id( "id","wo_quotation_inquery_fab_dtls", 1 ) ;
		$field_array_dtls_inq="id, mst_id, constraction, composition, gsm, determination_id, inserted_by, insert_date, status_active, is_deleted";
		//$save_string=explode(",",str_replace("'","",$txt_fabrication));
		$save_string=explode(",",str_replace("'","",$save_text_data));
		//echo "10**".str_replace("'","",$txt_fabrication).'ssd';die;

		for($i=0;$i<count($save_string);$i++)
		{
			$data=explode("_",$save_string[$i]);
			$constraction=$data[0];
			$composition=$data[1];
			$gsm=$data[2];
			$determination_id=$data[3];
			/*$determination_id=0;
			if(count($data)>1)
			{
				$determination_id=$data[3];
			}*/
			
			if(str_replace("'","",$txt_fabrication)!='')
			{
				if ($data_array_dtls_inq!="") $data_array_dtls_inq.=",";
				$data_array_dtls_inq.="(".$id_dtls_inq.",".$id.",'".$constraction."','".$composition."','".$gsm."','".$determination_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls_inq=$id_dtls_inq+1;
			}
		}

		$id_price_info=return_next_id( "id","wo_quo_inq_price_info", 1 ) ;
		$field_array_price_info="id,mst_id,price_stage,stage,price_date,price,remarks,inserted_by, insert_date,status_active,is_deleted";
		$price_info_string=explode("__",str_replace("'","",$price_info_break_down));
		//echo "10**".str_replace("'","",$txt_fabrication).'ssd';die;
		$data_array_price_info='';
		for($i=0;$i<count($price_info_string);$i++)
		{
			$data=explode("_",$price_info_string[$i]);
			$price_stage=$data[0];
			$stage=$data[1];
			$price_date=$data[2];
			$price=$data[3];
			$remarks=$data[4];
			if(str_replace("'","",$price_info_break_down)!='')
			{
				$price_date =  date('d-M-Y h:i:s',strtotime($price_date));
				if ($data_array_price_info!="") $data_array_price_info.=",";
				$data_array_price_info.="(".$id_price_info.",".$id.",'".$price_stage."','".$stage."','".$price_date."',".$price.",'".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_price_info=$id_price_info+1;
			}
		}



		$id_sample_info=return_next_id( "id","wo_quo_inq_sample_info", 1 ) ;
		$field_array_sample_info="id,mst_id,stage_id,sample_date,remarks,inserted_by, insert_date,status_active,is_deleted";
		$sample_info_string=explode("__",str_replace("'","",$sample_info_break_down));
		//echo "10**".str_replace("'","",$txt_fabrication).'ssd';die;
		$data_array_sample_info='';
		for($i=0;$i<count($sample_info_string);$i++)
		{
			$data=explode("_",$sample_info_string[$i]);
			$sample_stage=$data[0];
			$samle_date=$data[1];
			$remarks=$data[2];
			if(str_replace("'","",$sample_info_break_down)!='')
			{
				$samle_date =  date('d-M-Y h:i:s',strtotime($samle_date));
				if ($data_array_sample_info!="") $data_array_sample_info.=",";
				$data_array_sample_info.="(".$id_sample_info.",".$id.",'".$sample_stage."','".$samle_date."','".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_sample_info=$id_sample_info+1;
			}
		}



		//echo "10**insert into wo_quotation_inquery (".$field_array.") values ".$data_array;die;
		 $rID=sql_insert("wo_quotation_inquery",$field_array,$data_array,0);

		if($rID) $flag=1; else $flag=0;
		if($data_array_dtls_inq!="")
		{
			//echo "insert into wo_quotation_inquery_fab_dtls (".$field_array_dtls_inq.") values ".$data_array_dtls_inq;die;
			$rID2=sql_insert("wo_quotation_inquery_fab_dtls",$field_array_dtls_inq,$data_array_dtls_inq,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}
		if($data_array_price_info!="")
		{
			//echo "10**insert into wo_quo_inq_price_info (".$field_array_price_info.") values ".$data_array_price_info;die;
			$rID3=sql_insert("wo_quo_inq_price_info",$field_array_price_info,$data_array_price_info,1);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		if($data_array_sample_info!="")
		{
			//echo "10**insert into wo_quo_inq_price_info (".$field_array_price_info.") values ".$data_array_price_info;die;
			$rID4=sql_insert("wo_quo_inq_sample_info",$field_array_sample_info,$data_array_sample_info,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
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
		$rID5=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($flag==1)
		{
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'_'.$rID2.'__'.$rID3; die;
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
		$txt_color_id=str_replace("'","",$txt_color_id);
		$field_array="company_id*buyer_id*style_ref_id*season_buyer_wise*inquery_date*team_leader*style_refernce*product_dept*pro_sub_dep*factory_marchant*buyer_request*remarks*dealing_marchant*gmts_item*est_ship_date*fabrication*offer_qty*color*color_id*req_quotation_date*target_sam_sub_date*actual_req_quot_date*actual_sam_send_date*department_name*buyer_target_price*buyer_submit_price*bh_merchant*color_type*possible_order_con*lead_time*price_info_break_down*sample_info_break_down*order_uom*set_break_down*total_set_qnty*set_smv*location_name*style_description*brand_id*currency_id*season_year*design_source_id*qlty_label*customer_year*week_no*quot_status*update_by*update_date*status_active";
		 $data_array ="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_style_id."*".$cbo_season_name."*".$txt_inquery_date."*".$cbo_team_leader."*".$txt_style_ref."*".$cbo_product_department."*".$cbo_sub_dept."*".$cbo_factory_merchant."*".$txt_request_no."*".$txt_remarks."*".$cbo_dealing_merchant."*'".str_replace("'", "", $cbo_gmt_item)."'*".$txt_est_ship_date."*".$txt_fabrication."*".$txt_offer_qty."*".$txt_color."*".$txt_color_id."*".$txt_req_quot_date."*".$txt_target_samp_date."*".$txt_actual_req_quot_date."*".$txt_actual_sam_send_date."*".$txt_department."*".$txt_buyer_target_price."*".$txt_buyer_submit_price."*".$txt_bh_merchant."*".$cbo_color_type."*".$txt_possible_order_con_date."*".$txt_lead_time."*".$price_info_break_down."*".$sample_info_break_down."*'".str_replace("'", "", $cbo_order_uom)."'*'".str_replace("'", "", $set_breck_down) ."'*'".str_replace("'", "", $tot_set_qnty)."'*'".str_replace("'", "", $txt_sew_smv)."'*'".str_replace("'", "", $cbo_location_name)."'*'".str_replace("'", "", $txt_style_description)."'*'".str_replace("'", "",$cbo_brand_id)."'*'".str_replace("'", "", $cbo_currercy)."'*'".str_replace("'", "", $cbo_season_year)."'*'".str_replace("'", "", $cbo_design_source_id)."'*'".str_replace("'", "", $cbo_qltyLabel)."'*'".str_replace("'", "", $cbo_customer_year)."'*'".str_replace("'", "", $cbo_week)."'*'".str_replace("'", "", $cbo_status)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
		 
	
		$id_dtls_inq=return_next_id( "id","wo_quotation_inquery_fab_dtls", 1 ) ;
		$field_array_dtls_inq="id,mst_id,constraction,composition,gsm,determination_id,inserted_by, insert_date,status_active,is_deleted";
		//$save_string=explode(",",str_replace("'","",$txt_fabrication));
		$save_string=explode(",",str_replace("'","",$save_text_data));
		for($i=0;$i<count($save_string);$i++)
		{
			$data=explode("_",$save_string[$i]);
			$constraction=$data[0];
			$composition=$data[1];
			$gsm=$data[2];
			$determination_id=$data[3];
			/*$determination_id=0;
			if(count($data)>2)
			{
				$determination_id=$data[3];
			}*/
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
			if($constraction)
			{
				if ($data_array_dtls_inq!="") $data_array_dtls_inq.=",";
				$data_array_dtls_inq.="(".$id_dtls_inq.",".$update_id.",'".$constraction."','".$composition."','".$gsm."','".$determination_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls_inq=$id_dtls_inq+1;
			}
		}

		$id_price_info=return_next_id( "id","wo_quo_inq_price_info", 1 ) ;
		$field_array_price_info="id,mst_id,price_stage,stage,price_date,price,remarks,inserted_by, insert_date,status_active,is_deleted";
		$price_info_string=explode("__",str_replace("'","",$price_info_break_down));
		//echo "10**".str_replace("'","",$txt_fabrication).'ssd';die;
		$data_array_price_info='';
		for($i=0;$i<count($price_info_string);$i++)
		{
			$data=explode("_",$price_info_string[$i]);
			$price_stage=$data[0];
			$stage=$data[1];
			$price_date=$data[2];
			$price=$data[3];
			$remarks=$data[4];
			if(str_replace("'","",$price_info_break_down)!='')
			{
				$price_date =  date('d-M-Y h:i:s',strtotime($price_date));
				if($price_stage >0 && $stage >0){
				if ($data_array_price_info!="") $data_array_price_info.=",";
				$data_array_price_info.="(".$id_price_info.",".$update_id.",'".$price_stage."','".$stage."','".$price_date."','".$price."','".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_price_info=$id_price_info+1;
				}
			}
		}

		$id_sample_info=return_next_id( "id","wo_quo_inq_sample_info", 1 ) ;
		$field_array_sample_info="id,mst_id,stage_id,sample_date,remarks,inserted_by, insert_date,status_active,is_deleted";
		$sample_info_string=explode("__",str_replace("'","",$sample_info_break_down));
		//echo "10**".str_replace("'","",$txt_fabrication).'ssd';die;
		$data_array_sample_info='';
		for($i=0;$i<count($sample_info_string);$i++)
		{
			$data=explode("_",$sample_info_string[$i]);
			$sample_stage=$data[0];
			$samle_date=$data[1];
			$remarks=$data[2];
			if(str_replace("'","",$sample_info_break_down)!='')
			{
				$samle_date =  date('d-M-Y h:i:s',strtotime($samle_date));
				if ($data_array_sample_info!="") $data_array_sample_info.=",";
				$data_array_sample_info.="(".$id_sample_info.",".$update_id.",'".$sample_stage."','".$samle_date."','".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_sample_info=$id_sample_info+1;
			}
		}

		 //print_r( $data_array);die;
		$rID=sql_update("wo_quotation_inquery",$field_array,$data_array,"id","".$update_id."",0);
		//echo "10**".$rID; die;
		if($rID) $flag=1; else $flag=0;
		$delete_fab_inq_dtls=execute_query( "delete from wo_quotation_inquery_fab_dtls where mst_id=$update_id",0);
		if($flag==1)
		{
			if($delete_fab_inq_dtls) $flag=1; else $flag=0;
		}

		if($data_array_dtls_inq!="")
		{
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID2=sql_insert("wo_quotation_inquery_fab_dtls",$field_array_dtls_inq,$data_array_dtls_inq,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		$delete_price_info_dtls=execute_query( "delete from wo_quo_inq_price_info where mst_id=$update_id",0);

		$delete_sample_info_dtls=execute_query( "delete from wo_quo_inq_sample_info where mst_id=$update_id",0);

		if($flag==1)
		{
			if($delete_price_info_dtls) $flag=1; else $flag=0;
		}

		if($data_array_price_info!="")
		{
			//echo "10**insert into wo_quotation_inquery_price_info (".$field_array_price_info.") values ".$data_array_price_info;die;
			$rID3=sql_insert("wo_quo_inq_price_info",$field_array_price_info,$data_array_price_info,1);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		if($data_array_sample_info!="")
		{
			//echo "10**insert into wo_quo_inq_price_info (".$field_array_price_info.") values ".$data_array_price_info;die;
			$rID4=sql_insert("wo_quo_inq_sample_info",$field_array_sample_info,$data_array_sample_info,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}
		
		//$txt_system_id=str_replace("'","",$txt_system_id);
		
		
	 	//echo "10**".$rID.'-'.$rID2.'-'.$rID3; die;
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
		$rID5=execute_query( "delete from WO_QUOTATION_SET_DETAILS where quot_id =".$update_id."",0);
		if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		$rID6=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($rID6==1 && $flag==1) $flag=1; else $flag=0;
		
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
				echo "10**".$txt_system_id."**".$update_id."**".$txt_color_id."**".$rID."**".$rID2."** ".$rID3."**".$rID4;
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
		$sql=sql_select("select id from wo_price_quotation where inquery_id=$update_id");
		foreach($sql as $row){
			if($is_price_quot=="") $is_price_quot=$row[csf('id')]; else $is_price_quot.=', '.$row[csf('id')];
		}
		if($is_price_quot!=""){
			echo "pricequotation**".$is_price_quot;
			disconnect($con);die;
		}


		$field_arrmst="update_by*update_date*status_active*is_deleted";
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_quotation_inquery",$field_arrmst,$data_array,"id","".$update_id."",1);
		$rID1=sql_delete("wo_quotation_inquery_fab_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		$rID3=sql_delete("wo_quo_inq_price_info",$field_array,$data_array,"mst_id","".$update_id."",1);
		$rID4=sql_delete("wo_quo_inq_sample_info",$field_array,$data_array,"mst_id","".$update_id."",1);
		//echo $rID."**".$rID1; die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID3 && $rID4){
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
			if($rID && $rID1 && $rID3 && $rID4){
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

if($action=="copy_quotation")
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

		$id=return_next_id( "id", "wo_quotation_inquery", 1 ) ;
		
		
		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'QIN', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from wo_quotation_inquery where company_id=$cbo_company_name  and $date_cond=".date('Y',time())." order by id DESC ", "system_number_prefix", "system_number_prefix_num" ));
		
		$season=str_replace("'","",$cbo_season_name);

		$sql_jobInst="insert into wo_quotation_inquery( id, system_number_prefix, system_number_prefix_num, system_number, company_id, buyer_id,style_ref_id, product_dept, pro_sub_dep, factory_marchant, season_buyer_wise, inquery_date, style_refernce, buyer_request, remarks, dealing_marchant, gmts_item, order_uom, set_break_down, total_set_qnty, set_smv, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, bh_merchant, color_type, possible_order_con, lead_time, price_info_break_down, sample_info_break_down, quot_status, status_active, is_deleted, insert_by, insert_date)
	select
	$id, '".$new_system_id[1]."', '".$new_system_id[2]."', '".$new_system_id[0]."', company_id, buyer_id,style_ref_id, product_dept, pro_sub_dep, factory_marchant, season_buyer_wise, inquery_date, style_refernce, buyer_request, remarks, dealing_marchant, gmts_item, order_uom, set_break_down, total_set_qnty, set_smv, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, bh_merchant, color_type, possible_order_con, lead_time, price_info_break_down, sample_info_break_down, quot_status, status_active, is_deleted, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."' from wo_quotation_inquery where id=$update_id";

	$mst_id=$id;
	//echo "10**".$sql_jobInst; die;

	//echo "10**"."select company_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and system_number=$txt_system_id order by id desc =".$sql_jobInst;die;
		$rID = $rID1= $rID2 = $rID3=$rID4=true;
		$rID=execute_query($sql_jobInst,0);
		

		$sql_dtls=sql_select("select id from wo_quotation_inquery_fab_dtls  where mst_id=$update_id order by id ASC");
		foreach($sql_dtls as $row_se_set)
		{
			$id_set=return_next_id( "id", "wo_quotation_inquery_fab_dtls", 1 ) ;

			$sql_insert_set="insert into  wo_quotation_inquery_fab_dtls(id,mst_id,constraction,composition,gsm,determination_id,inserted_by, insert_date,status_active,is_deleted)
			select $id_set, $mst_id, constraction,composition,gsm,determination_id,inserted_by, insert_date,status_active,is_deleted from  wo_quotation_inquery_fab_dtls where  id=".$row_se_set[csf('id')]."";
			$rID1=execute_query($sql_insert_set,0);
		}

		$sql_price=sql_select("select id from wo_quo_inq_price_info  where mst_id=$update_id order by id ASC");
		foreach($sql_price as $row_se_set)
		{
			$id_set=return_next_id( "id", "wo_quo_inq_price_info", 1 ) ;

			$sql_insert_set="insert into  wo_quo_inq_price_info(id,mst_id,price_stage,stage,price_date,price,remarks,inserted_by, insert_date,status_active,is_deleted)
			select $id_set, $mst_id, price_stage,stage,price_date,price,remarks,inserted_by, insert_date,status_active,is_deleted from  wo_quo_inq_price_info where  id=".$row_se_set[csf('id')]."";
			$rID2=execute_query($sql_insert_set,0);
		}

		$sql_sample=sql_select("select id from wo_quo_inq_sample_info  where mst_id=$update_id order by id ASC");
		foreach($sql_sample as $row_se_set)
		{
			$id_set=return_next_id( "id", "wo_quo_inq_sample_info", 1 ) ;

			$sql_insert_set="insert into  wo_quo_inq_sample_info(id,mst_id,stage_id,sample_date,remarks,inserted_by, insert_date,status_active,is_deleted)
			select $id_set, $mst_id, stage_id,sample_date,remarks,inserted_by, insert_date,status_active,is_deleted from  wo_quo_inq_sample_info where  id=".$row_se_set[csf('id')]."";
			$rID3=execute_query($sql_insert_set,0);
		}
		
		$sql_item=sql_select("select id from WO_QUOTATION_SET_DETAILS  where mst_id=$update_id order by id ASC");
		foreach($sql_item as $row_set)
		{
			$id_item=return_next_id( "id", "WO_QUOTATION_SET_DETAILS", 1 ) ;

			$sql_insert_setitem="insert into  WO_QUOTATION_SET_DETAILS(id, quot_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id)
			select $id_item, $mst_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id from  WO_QUOTATION_SET_DETAILS where id=".$row_set[csf('id')]."";
			$rID4=execute_query($sql_insert_setitem,0);
		}
		
		
		
		//echo "10**".$rID.'_'.$rID1.'_'.$rID2.'_'.$rID3; die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3){
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$id."**".$cbo_company_name;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**". $rID ."**". $rID1. "**". $rID2 . "**". $rID3."**".$cbo_company_name;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3){
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$id."**".$cbo_company_name;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID ."**". $rID1 ."**". $rID2 . "**". $rID3."**".$cbo_company_name;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="price_info_popup")
{
	echo load_html_head_contents("Fabric Detail Entry", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
	function fn_addRow_fab(i)
	{
			//var row_num=$('#tbl_item_details tbody tr').length;
			//var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
			//alert(lastTrId);
			//var row_num=lastTrId[1];
			var row_num=$('#tbl_list tbody tr').length;
			
			//alert(i);
			//alert(lastTrId[1]);
			if (row_num!=i)
			{
				return false;
			}
			if (form_validation('cbostage_'+i+'*txtdate_'+i+'*txtprice_'+i,'Stage*Date*Price')==false)
			{
				return;
			}
			else
			{
				i++;

				$("#tbl_list tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) {
				  	var inputPartialId = $(this).attr('id').split("_")[0];
				  	if(inputPartialId == 'increase' ||  inputPartialId == 'decrease' || inputPartialId == 'cbopricestage' || inputPartialId =="cbostage" || inputPartialId == 'txtprice'  || inputPartialId == 'txtremark'){
							return value;
						}
						if(inputPartialId == 'txtdate'){
							return '';
						}
				 		return 0;
				  		//return value 
				  }
				});

				}).end().appendTo("#tbl_list");
				$("#txtdate_"+i).removeClass("hasDatepicker");
				$("#txtdate_"+i).val('');
				$("#txtprice_"+i).val('');
				$("#txtremark_"+i).val('');
				$('#slTd_'+i).val('');
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

			if(rowNo==row_num && row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}

		function window_close()
		{
			var save_data=''; var tot_trims_qnty='';
			var row_count = $('#tbl_list tbody tr').length;
			for(var i=1; i<=row_count; i++)
			{
				var cbopricestage=$('#cbopricestage_'+i).val();
				var cbostage=$('#cbostage_'+i).val();
				var txtdate=$('#txtdate_'+i).val();
				var txtprice=$('#txtprice_'+i).val();
				var txtremark=$('#txtremark_'+i).val();
				if(txtremark=='')
				{
					txtremark='No remark';
				}

				if(cbopricestage)
				{
					if(save_data=="")
					{
						save_data=cbopricestage+"_"+cbostage+"_"+txtdate+"_"+txtprice+"_"+txtremark;
					}
					else
					{
						save_data+="__"+cbopricestage+"_"+cbostage+"_"+txtdate+"_"+txtprice+"_"+txtremark;
					}

				}

			};
			/*alert(save_data);
			return;*/
			$('#save_data').val( save_data );
			parent.emailwindow.hide();
		}



    </script>

	</head>

	<body>
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission,1); ?>
		<form name="trimsWeight_1" id="trimsWeight_1">

	        <fieldset style="width:750px; margin-top:10px">
	            <legend>Price Info Pop Up</legend>
	            <?
	            if($break_down!="")
				{
				?>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" id="tbl_list">
	            	<thead>
	                    <th width="30">SL</th>
	                    <th width="150">Price Stage</th>
	                    <th width="150">Stage</th>
	                    <th width="80">Date</th>
	                    <th width="60">Price</th>
	                    <th width="160">Remark</th>
	                    <th>&nbsp;</th>
	                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

	                </thead>
	                <tbody id="tbl_list_data">

	                <?
					$tot_trims_wgt=0;$k=0;
	                $explSaveData = explode("__",$break_down);
					for($z=0;$z<count($explSaveData);$z++)
					{
						$data_all=explode("_",$explSaveData[$z]);
						$pricestage=$data_all[0];
						$stage=$data_all[1];
						$date=$data_all[2];
						$price=$data_all[3];
						$remark=$data_all[4];
						$k++;

					?>
	                    <tr id="tr_<? echo $k;?>">
	                    	<td id="slTd_<? echo $k;?>" width="30"><? echo $k;?></td>
	                        <td>
	                        <?   echo create_drop_down( "cbopricestage_".$k, 150, $inquery_price_arr,"", 0, "-- Select price stage --", $pricestage , "" );
                       		?>
	                        </td>
	                        <td>
	                        <?   echo create_drop_down( "cbostage_".$k, 150, $inquery_stage_arr,"", 1, "-- Select price stage --", $stage, "" );
                       		?>
	                        </td>
	                        <td>
	                        	<input  type="text" style="width:80px" class="datepicker" placeholder="Select Date"  name="txtdate[]" id="txtdate_<? echo $k;?>" value="<? echo $date ?>"/>
	                        </td>
	                        <td>
	                        	<input  type="text" style="width:60px" class="text_boxes_numeric"  name="txtprice_<? echo $k;?>" id="txtprice_<? echo $k;?>" value="<? echo $price ?>" />
	                        </td>
	                        <td>
	                        	<input  type="text" style="width:160px" class="text_boxes"  name="txtremark_<? echo $k;?>" id="txtremark_<? echo $k;?>" value="<? echo $remark ?>" />
	                        </td>

	                        <td>
	                        <input type="button" id="increase_<? echo $k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<? echo $k;?>)" />
	                        <input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k;?>);"/>
	                        </td>
	        			</tr>
	                    <?
					}
						?>

	                </tbody>
	                <tfoot class="tbl_bottom">
	                        <td colspan="5">&nbsp;</td>

	                    </tfoot>
	            </table>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500">

	                <tr>
	                    <td colspan="5" align="center">

	                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

	                    </td>
	                </tr>
				</table>

	            <?
				}
				else
				{ ?>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" id="tbl_list">
	            	<thead>
	                    <th width="30">SL</th>
	                    <th width="150">Price Stage</th>
	                    <th width="150">Stage</th>
	                    <th width="80">Date</th>
	                    <th width="60">Price</th>
	                    <th width="160">Remark</th>
	                    <th>&nbsp;</th>
	                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

	                </thead>
	                <tbody>
	                    <tr id="tr_1">
	                    <td id="slTd_1" width="30">1</td>
	                        <td>
	                        <?   echo create_drop_down( "cbopricestage_1", 150, $inquery_price_arr,"", 0, "-- Select price stage --", "", "" );
                       		?>
	                        </td>
	                        <td>
	                        <?   echo create_drop_down( "cbostage_1", 150, $inquery_stage_arr,"", 1, "-- Select price stage --", "", "" );
                       		?>
	                        </td>
	                        <td>
	                        	<input  type="text" style="width:80px" class="datepicker" placeholder="Select Date"  name="txtdate[]" id="txtdate_1"/>
	                        </td>
	                        <td>
	                        	<input  type="text" style="width:60px" class="text_boxes_numeric"  name="txtprice_1" id="txtprice_1" value="0" />
	                        </td>
	                        <td>
	                        	<input  type="text" style="width:160px" class="text_boxes"  name="txtremark_1" id="txtremark_1" value="" />
	                        </td>
	                        <td>
	                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1)" />
	                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
	                        </td>
	        			</tr>

	                </tbody>
	                <tfoot class="tbl_bottom">
	                        <td colspan="7">&nbsp;</td>

	                    </tfoot>
	            </table>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750">

	                <tr>
	                    <td colspan="7" align="center">

	                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

	                    </td>
	                </tr>
				</table>
			<? } ?>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="sample_info_popup")
{
	echo load_html_head_contents("Sample Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
	function fn_addRow_fab(i)
	{
			//var row_num=$('#tbl_item_details tbody tr').length;
			//var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
			//alert(lastTrId);
			//var row_num=lastTrId[1];
			var row_num=$('#tbl_list tbody tr').length;
			
			//alert(i);
			//alert(lastTrId[1]);
			if (row_num!=i)
			{
				return false;
			}
			if (form_validation('cbopricestage_'+i+'*txtdate_'+i,'Stage*Date')==false)
			{
				return;
			}
			else
			{
				i++;

				$("#tbl_list tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) {
				  	var inputPartialId = $(this).attr('id').split("_")[0];
				  	if(inputPartialId == 'increase' ||  inputPartialId == 'decrease' || inputPartialId == 'cbopricestage'  || inputPartialId == 'txtremark'){
							return value;
						}
						if(inputPartialId == 'txtdate'){
							return '';
						}
				 		return 0;
				  		//return value 
				  }
				});

				}).end().appendTo("#tbl_list");
				$("#txtdate_"+i).removeClass("hasDatepicker");
				$("#txtdate_"+i).val('');
				
				$("#txtremark_"+i).val('');
				$('#slTd_'+i).val('');
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

			if(rowNo==row_num && row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}

		function window_close()
		{
			var save_data=''; var tot_trims_qnty='';
			var row_count = $('#tbl_list tbody tr').length;
			for(var i=1; i<=row_count; i++)
			{
				var cbopricestage=$('#cbopricestage_'+i).val();
				
				var txtdate=$('#txtdate_'+i).val();
				
				var txtremark=$('#txtremark_'+i).val();
				if(txtremark=='')
				{
					txtremark='No remark';
				}

				if(cbopricestage)
				{
					if(save_data=="")
					{
						save_data=cbopricestage+"_"+txtdate+"_"+txtremark;
					}
					else
					{
						save_data+="__"+cbopricestage+"_"+txtdate+"_"+txtremark;
					}

				}

			};
			/*alert(save_data);
			return;*/
			$('#save_data').val( save_data );
			parent.emailwindow.hide();
		}



    </script>

	</head>

	<body>
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission,1); ?>
		<?php $inquery_sample_arr=array(1=>"1st Submission",2=>"2nd Submission",3=>"3rd Submission"); ?>
		<form name="trimsWeight_1" id="trimsWeight_1">

	        <fieldset style="width:550px; margin-top:10px">
	            <legend>Sample Info Pop Up</legend>
	            <?
	            if($break_down!="")
				{
				?>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550" id="tbl_list">
	            	<thead>
	                    <th width="30">SL</th>
	                    <th width="150">Stage</th>
	                    
	                    <th width="80">Date</th>
	                    
	                    <th width="160">Remark</th>
	                    <th>&nbsp;</th>
	                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

	                </thead>
	                <tbody id="tbl_list_data">

	                <?
					$tot_trims_wgt=0;$k=0;
	                $explSaveData = explode("__",$break_down);
					for($z=0;$z<count($explSaveData);$z++)
					{
						$data_all=explode("_",$explSaveData[$z]);
						$pricestage=$data_all[0];
						
						$date=$data_all[1];
						
						$remark=$data_all[2];
						$k++;

					?>
	                    <tr id="tr_<? echo $k;?>">
	                    	<td id="slTd_<? echo $k;?>" width="30"><? echo $k;?></td>
	                        <td>
		                        <?   echo create_drop_down( "cbopricestage_".$k, 150, $inquery_sample_arr,"", 0, "-- Select price stage --", $pricestage , "" );
	                       		?>
	                        </td>
	                        
	                        <td>
	                        	<input  type="text" style="width:80px" class="datepicker" placeholder="Select Date"  name="txtdate[]" id="txtdate_<? echo $k;?>" value="<? echo $date ?>"/>
	                        </td>
	                       
	                        <td>
	                        	<input  type="text" style="width:160px" class="text_boxes"  name="txtremark_<? echo $k;?>" id="txtremark_<? echo $k;?>" value="<? echo $remark ?>" />
	                        </td>

	                        <td>
		                        <input type="button" id="increase_<? echo $k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<? echo $k;?>)" />
		                        <input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k;?>);"/>
	                        </td>
	        			</tr>
	                    <?
					}
						?>

	                </tbody>
	                <tfoot class="tbl_bottom">
	                        <td colspan="5">&nbsp;</td>

	                    </tfoot>
	            </table>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500">

	                <tr>
	                    <td colspan="5" align="center">

	                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

	                    </td>
	                </tr>
				</table>

	            <?
				}
				else
				{ ?>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550" id="tbl_list">
	            	<thead>
	                    <th width="30">SL</th>
	                    <th width="150">Stage</th>
	                    
	                    <th width="80">Date</th>
	                    
	                    <th width="160">Remark</th>
	                    <th>&nbsp;</th>
	                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

	                </thead>
	                <tbody>
	                    <tr id="tr_1">
	                    <td id="slTd_1" width="30">1</td>
	                        <td>
	                        <?  

	                         echo create_drop_down( "cbopricestage_1", 150, $inquery_sample_arr,"", 1, "-- Select --", "", "" );
                       		?>
	                        </td>
	                       
	                        <td>
	                        	<input  type="text" style="width:80px" class="datepicker" placeholder="Select Date"  name="txtdate[]" id="txtdate_1"/>
	                        </td>
	                       
	                        <td>
	                        	<input  type="text" style="width:160px" class="text_boxes"  name="txtremark_1" id="txtremark_1" value="" />
	                        </td>
	                        <td>
	                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1)" />
	                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
	                        </td>
	        			</tr>

	                </tbody>
	                <tfoot class="tbl_bottom">
	                        <td colspan="5">&nbsp;</td>

	                    </tfoot>
	            </table>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550">

	                <tr>
	                    <td colspan="5" align="center">

	                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

	                    </td>
	                </tr>
				</table>
			<? } ?>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="buyer_inquery_fab_popup1")
{
	echo load_html_head_contents("Fabric Detail Entry", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $save_data;

?>
	<script>
		var permission='<? echo $permission; ?>';
	function fn_addRow_fab(i)
	{
			//var row_num=$('#tbl_item_details tbody tr').length;
			//var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
			//alert(lastTrId);
			//var row_num=lastTrId[1];
			var txt_style_from_lib=$("#txt_style_from_lib").val();
			var row_num=$('#tbl_list tbody tr').length;
			
			//alert(i);
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
				if(txt_style_from_lib==1)
				{
					$('#txtconstraction_'+i).removeAttr("ondblclick").attr("ondblclick","openmypagecomp("+i+");");
					$('#txtconstraction_'+i).removeAttr('disabled','disabled');
				}
				$('#txtyarncountdeterminationid_'+i).val('');
				$('#txtconstraction_'+i).val('');
				$('#txtcomposition_'+i).val('');
				$('#txtgsm_'+i).val('');
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
				//alert(row_num);
				$("#tr_"+rowNo).remove();
			}
		}

		function window_close()
		{
			var save_data=''; var tot_trims_qnty='';

			$("#tbl_list").find('tr').each(function()
			{
				var txtconstraction=$(this).find('input[name="txtconstraction[]"]').val();
				var txtcomposition=$(this).find('input[name="txtcomposition[]"]').val();
				var txtgsm=$(this).find('input[name="txtgsm[]"]').val();
				var txt_style_from_lib=$("#txt_style_from_lib").val();
				var determination_id='';
				if(txt_style_from_lib==1)
				{
					determination_id=$(this).find('input[name="txtyarncountdeterminationid[]"]').val();
				}
				

				if(txtconstraction)
				{
					//alert(txtconstraction);
					if(save_data=="")
					{
						save_data=txtconstraction+"_"+txtcomposition+"_"+txtgsm+"_"+determination_id;
					}
					else
					{
						save_data+=","+txtconstraction+"_"+txtcomposition+"_"+txtgsm+"_"+determination_id;
					}
					//tot_trims_qnty=tot_trims_qnty*1+trimsWeight*1;

				}

			});
			//alert(save_data);
			$('#save_data').val( save_data );
			//$('#tot_trims_qnty').val(tot_trims_qnty);
			parent.emailwindow.hide();
		}

	 function openmypagecomp(sl)
	 {
	 	var cbofabricnature=0;
		var libyarncountdeterminationid =0;
		var page_link='quotation_inquery_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=930px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			//var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			//var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			//var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			document.getElementById('txtyarncountdeterminationid_'+sl).value=fab_des_id.value;
			document.getElementById('txtconstraction_'+sl).value=construction.value;
			document.getElementById('txtcomposition_'+sl).value=composition.value;
			document.getElementById('txtgsm_'+sl).value=fab_gsm.value;
			
			$('#txtyarncountdeterminationid_'+sl).attr('disabled','disabled');
			$('#txtconstraction_'+sl).attr('disabled','disabled');
			$('#txtcomposition_'+sl).attr('disabled','disabled');
			$('#txtgsm_'+sl).attr('disabled','disabled');
			console.log(composition.value);
			// document.getElementById('txtgsmweight_'+sl).value=fab_gsm.value;
			// document.getElementById('yarnbreackdown_'+sl).value=yarn_desctiption.value;
			// document.getElementById('construction_'+sl).value=construction.value;
			// document.getElementById('composition_'+sl).value=composition.value;
			//sum_yarn_required()
		}
	 }

    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="trimsWeight_1" id="trimsWeight_1">

        <fieldset style="width:500px; margin-top:10px">
            <legend>Fabrication Details Pop Up</legend>
            <input type="hidden" name="txt_style_from_lib" id="txt_style_from_lib" value="<?=$txt_style_from_lib?>">
            <?
            if($save_data!="")
			{
				if($txt_style_from_lib==1)
				{


					?>
		            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="880" id="tbl_list">
		            	<thead>
		                    <th width="30">SL</th>
		                    
		                    <th width="200">Constraction</th>
		                    <th width="400">Composition</th>
		                    <th width="100">GSM</th>
		                    <th>&nbsp;</th>
		                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

		                </thead>
		                <tbody>

		                <?
						$tot_trims_wgt=0;$k=0;
		                $explSaveData = explode(",",$save_data);
						for($z=0;$z<count($explSaveData);$z++)
						{
							$data_all=explode("_",$explSaveData[$z]);
							$item_des=$data_all[0];
							$trims_wgt=$data_all[1];
							$remark=$data_all[2];
							$determination_id=$data_all[3];
							$tot_trims_wgt+=$trims_wgt;
							$k++;

							?>
		                    <tr id="tr_<? echo $k;?>">
		                   		 <td id="slTd_<? echo $k;?>" width="30"><? echo $k;?></td>
		                   		

		                        <td>
		                        	<input type="hidden" name="txtyarncountdeterminationid[]" id="txtyarncountdeterminationid_1" class="text_boxes"   placeholder="Browse" value="<? echo $determination_id;?>" readonly />

		                       		 <input type="text" name="txtconstraction[]" id="txtconstraction_<? echo $k;?>" class="text_boxes" style="width:200px;"  value="<? echo $item_des;?>" readonly onDblClick="openmypagecomp(1)" placeholder="Browse" />
		                        </td>
		                        <td>
		                       		 <input type="text" name="txtcomposition[]" id="txtcomposition_<? echo $k;?>" class="text_boxes" style="width:400px;"   value="<? echo $trims_wgt;?>" readonly/>
		                        </td>
		                        <td>
		                       		 <input type="text" name="txtgsm[]" id="txtgsm_<? echo $k;?>" class="text_boxes" style="width:100px;"  value="<? echo $remark;?>" readonly/>
		                        </td>

		                        <td>
		                        	<input type="button" id="increase_<? echo $k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<? echo $k;?>)" />
		                       		 <input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k;?>);"/>
		                        </td>
		        			</tr>
		                    <?
						}
							?>

		                </tbody>
		                <tfoot class="tbl_bottom">
		                        <td colspan="5">&nbsp;</td>

		                    </tfoot>
		            </table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="880">

		                <tr>
		                    <td colspan="5" align="center">

		                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

		                    </td>
		                </tr>
					</table>

		            <?
		        }
		        else
		        {
		        	?>
		            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550" id="tbl_list">
		            	<thead>
		                    <th width="30">SL</th>
		                    <th width="150">Constraction</th>
		                    <th width="150">Composition</th>
		                    <th width="60">GSM</th>
		                    <th>&nbsp;</th>
		                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

		                </thead>
		                <tbody>

		                <?
						$tot_trims_wgt=0;$k=0;
		                $explSaveData = explode(",",$save_data);
						for($z=0;$z<count($explSaveData);$z++)
						{
							$data_all=explode("_",$explSaveData[$z]);
							$item_des=$data_all[0];
							$trims_wgt=$data_all[1];
							$remark=$data_all[2];
							
							$tot_trims_wgt+=$trims_wgt;
							$k++;

						?>
		                    <tr id="tr_<? echo $k;?>">
		                    <td id="slTd_<? echo $k;?>" width="30"><? echo $k;?></td>
		                        <td>
		                        <input type="text" name="txtconstraction[]" id="txtconstraction_<? echo $k;?>" class="text_boxes" style="width:150px;"  value="<? echo $item_des;?>"/>
		                        </td>
		                        <td>
		                        <input type="text" name="txtcomposition[]" id="txtcomposition_<? echo $k;?>" class="text_boxes" style="width:150px;"   value="<? echo $trims_wgt;?>"/>
		                        </td>
		                        <td>
		                        <input type="text" name="txtgsm[]" id="txtgsm_<? echo $k;?>" class="text_boxes" style="width:60px;"  value="<? echo $remark;?>"/>
		                        </td>

		                        <td>
		                        <input type="button" id="increase_<? echo $k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<? echo $k;?>)" />
		                        <input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k;?>);"/>
		                        </td>
		        			</tr>
		                    <?
						}
							?>

		                </tbody>
		                <tfoot class="tbl_bottom">
		                        <td colspan="5">&nbsp;</td>

		                    </tfoot>
		            </table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550">

		                <tr>
		                    <td colspan="5" align="center">

		                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

		                    </td>
		                </tr>
					</table>

		            <?
		        }
			}
			else
			{ 
				if($txt_style_from_lib==1)
				{
					?>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="880" id="tbl_list">
		            	<thead>
		                    <th width="30">SL</th>
		                    
		                    <th width="200">Constraction</th>
		                    <th width="400">Composition</th>
		                    <th width="100">GSM</th>
		                    <th>&nbsp;</th>
		                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

		                </thead>
		                <tbody>
		                    <tr id="tr_1">
		                   		<td id="slTd_1" width="30">1</td>
		                   		
		                        <td>
		                        	<input type="hidden" name="txtyarncountdeterminationid[]" id="txtyarncountdeterminationid_1" class="text_boxes"   placeholder="Browse" readonly />
		                        	<input type="text" name="txtconstraction[]" id="txtconstraction_1" onDblClick="openmypagecomp(1)" class="text_boxes" style="width:200px;" readonly  placeholder="Browse" />
		                        </td>
		                        <td>
		                        	<input type="text" name="txtcomposition[]" id="txtcomposition_1" class="text_boxes" style="width:400px;" readonly />
		                        </td>
		                        <td>
		                        	<input type="text" name="txtgsm[]" id="txtgsm_1" class="text_boxes" style="width:100px;" readonly/>
		                        </td>

		                        <td>
		                       	 	<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1)" />
		                        	<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
		                        </td>
		        			</tr>

		                </tbody>
		                <tfoot class="tbl_bottom">
		                        <td colspan="6">&nbsp;</td>

		                    </tfoot>
		            </table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="880">

		                <tr>
		                    <td colspan="6" align="center">

		                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

		                    </td>
		                </tr>
					</table>
					<? 
				}
				else
				{
					?>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550" id="tbl_list">
		            	<thead>
		                    <th width="30">SL</th>
		                    <th width="150">Constraction</th>
		                    <th width="150">Composition</th>
		                    <th width="60">GSM</th>
		                    <th>&nbsp;</th>
		                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">

		                </thead>
		                <tbody>
		                    <tr id="tr_1">
		                    <td id="slTd_1" width="30">1</td>
		                        <td>
		                        <input type="text" name="txtconstraction[]" id="txtconstraction_1" class="text_boxes" style="width:150px;"  />
		                        </td>
		                        <td>
		                        <input type="text" name="txtcomposition[]" id="txtcomposition_1" class="text_boxes" style="width:150px;" />
		                        </td>
		                        <td>
		                        <input type="text" name="txtgsm[]" id="txtgsm_1" class="text_boxes" style="width:60px;"/>
		                        </td>

		                        <td>
		                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1)" />
		                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
		                        </td>
		        			</tr>

		                </tbody>
		                <tfoot class="tbl_bottom">
		                        <td colspan="5">&nbsp;</td>

		                </tfoot>
		            </table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550">

		                <tr>
		                    <td colspan="5" align="center">

		                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />

		                    </td>
		                </tr>
					</table>
					<? 
				}
			} 
		?>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="fabric_description_popup1")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'quotation_inquery_controller');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
			document.getElementById('fab_des_id').value=data[0];
			//document.getElementById('fab_nature_id').value=data[1];
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('fab_gsm').value=trim(data[3]);
			//document.getElementById('process_loss').value=trim(data[4]);
			//document.getElementById('fab_desctiption').value=trim(fabric_description);
			document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
			//document.getElementById('composition').value=trim(data[5]);
			var yarn =fabric_yarn_description_arr[1].split("_");
			if(yarn[1]*1==0 || yarn[1]==""){
				alert("Composition not set in yarn count determination");
				return;
			}
			//document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
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
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $fabric_nature; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value, 'fabric_description_popup_search_list_view', 'search_div', 'quotation_inquery_controller', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<? echo $libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
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

if($action=="fabric_description_popup_search_list_view1")
{
	//echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$lib_buyer=return_library_array( "select buyer_name,id from lib_buyer", "id", "buyer_name"  );
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

		//if($construction!='') {$search_con = " and a.construction like ('%".trim($construction)."%')";}
		//if($gsm_weight!='') {$search_con  .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
	?>
	<script>
	/*function js_set_value(data)
	{
		var data=data.split('_');
		var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'pre_cost_entry_controller_v2');
		var fabric_yarn_description_arr=fabric_yarn_description.split("**");
		var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
	    document.getElementById('fab_des_id').value=data[0];
		document.getElementById('fab_nature_id').value=data[1];
		document.getElementById('construction').value=trim(data[2]);
		document.getElementById('fab_gsm').value=trim(data[3]);
		document.getElementById('process_loss').value=trim(data[4]);
		document.getElementById('fab_desctiption').value=trim(fabric_description);
		document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
		document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
	    parent.emailwindow.hide();
	}
	function toggle( x, origColor )
	{
				var newColor = 'yellow';
				document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
	}
	*/
	</script>
	</head>
	<body>

	    <div align="center">
	        <form>
	            <input type="hidden" id="fab_des_id" name="fab_des_id" />
	            <input type="hidden" id="fab_nature_id" name="fab_des_id" />
	            <input type="hidden" id="construction" name="construction" />
	            <input type="hidden" id="composition" name="composition" />
	            <input type="hidden" id="fab_gsm" name="fab_gsm" />
	            <input type="hidden" id="process_loss" name="process_loss" />
	            <input type="hidden" id="fab_desctiption" name="fab_desctiption" />
	            <input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
	        </form>
		<?
		$composition_arr=array();
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
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
	    <table class="rpt_table" width="920" cellspacing="0" cellpadding="0" border="0" rules="all">
	        <thead>
	            <tr>
	                <th width="50">SL No</th>
	                <th width="80">Sequence No</th>
	                <th width="100">Fab Nature</th>
	                <th width="100">Construction</th>
	                <th width="70">GSM/Weight</th>
	                <th width="100">Color Range</th>
	                <th width="50">Stich Length</th>
	                <th width="100">Buyer</th>
	                <th width="50">Process Loss</th>
	                <th>Composition</th>
	            </tr>
	       </thead>
	   </table>
	   <div id="" style="max-height:300px; width:918px; overflow-y:scroll">
	   <table id="list_view" class="rpt_table" width="900" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
	        <tbody>
		<?
		$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.id, a.sequence_no,a.buyer_id  from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_con group by a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.sequence_no,a.buyer_id order by a.id";
		//echo $sql;
		$sql_data=sql_select($sql);
		$i=1;
		foreach($sql_data as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comp=$composition_arr[$row[csf('id')]];
			?>
	        <tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')]."_".$comp?>')">
	            <td width="50"><? echo $i; ?></td>
	            <td width="80" align="left"><? echo $row[csf('sequence_no')]; ?></td>
	            <td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
	            <td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
	            <td width="70" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
	            <td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
	            <td width="50" align="right"><? echo $row[csf('stich_length')]; ?></td>
	            <td width="100" align="right"><? echo $lib_buyer[$row[csf('buyer_id')]]; ?></td>
	            <td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
	            <td><p style="word-wrap: break-word;"><? echo $composition_arr[$row[csf('id')]]; ?></p></td>
	        </tr>
			<?
	        $i++;
	    }
	    ?>
	        </tbody>
	    </table>
	</div>
	</div>
	</body>
	</html>
	<?
	exit();
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
			$('#txtsuppref_'+i).val('');
			$('#txtconstraction_'+i).val('');
			$('#txtfabconstraction_'+i).val('');
			$('#txtstichlength_'+i).val('');
			$('#txtweight_'+i).val('');
			$('#txtcolorrange_'+i).val('');
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
			var txtconstraction=$(this).find('input[name="txtconstraction[]"]').val();
			var txtweight=$(this).find('input[name="txtweight[]"]').val();
			var txtcomposition=$(this).find('input[name="txtcomposition[]"]').val();
			if(hiddFabDeterId)
			{
				//alert(txtconstraction);
				if(save_data=="") save_data=hiddFabDeterId;
				else save_data+=","+hiddFabDeterId;
				if(save_text_data=="") save_text_data=txtconstraction+"_"+txtcomposition+"_"+txtweight+"_"+hiddFabDeterId;
				else save_text_data+=" , "+txtconstraction+"_"+txtcomposition+"_"+txtweight+"_"+hiddFabDeterId;
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=380px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];

			var fabdata=this.contentDoc.getElementById("hid_libDes").value; 
			//alert(fabdata);
			var exfabdata = fabdata.split("_");
			
			$("#hiddFabDeterId_"+incid).val(exfabdata[0]);
			$("#txtrdno_"+incid).val(exfabdata[1]);
			$("#txtsuppref_"+incid).val(exfabdata[2]);
			$("#txtconstraction_"+incid).val(exfabdata[3]);
			$("#txtfabconstraction_"+incid).val(exfabdata[4]);
			$("#txtstichlength_"+incid).val(exfabdata[5]);
			$("#txtweight_"+incid).val(exfabdata[6]);
			$("#txtcolorrange_"+incid).val(exfabdata[7]);
			$("#txtcomposition_"+incid).val(exfabdata[8]);
		}
	}
    </script>

</head>

<body>
<div align="center">
	<? //echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="trimsWeight_1" id="trimsWeight_1">
        <fieldset style="width:950px;">
            <legend>Fabrication Details Pop Up</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="950" id="tbl_list">
            	<thead>
                    <th style="word-break: break-all;" width="20">SL</th>
                    <th style="word-break: break-all;" width="80">RD No</th>
                    <th style="word-break: break-all;" width="80">Supplier Ref</th>
                    <th style="word-break: break-all;" width="110">Constraction</th>
                    <th style="word-break: break-all;" width="150">Fabric Composition</th>
                    <th style="word-break: break-all;" width="80">Stitch Length</th>
                    <th style="word-break: break-all;" width="70">GSM</th>
                    
                    <th style="word-break: break-all;" width="80">Color Range</th>
                    
                    <th style="word-break: break-all;" width="160">Composition</th>
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
                                <td style="word-break: break-all;" ><input type="text" name="txtsuppref[]" id="txtsuppref_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtconstraction[]" id="txtconstraction_<?=$k; ?>" class="text_boxes" style="width:100px;" value="" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;"><input type="text" name="txtfabconstraction[]" id="txtfabconstraction_<?=$k; ?>" class="text_boxes" style="width:140px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;"><input type="text" name="txtstichlength[]" id="txtstichlength_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtweight[]" id="txtweight_<?=$k; ?>" class="text_boxes" style="width:60px;" readonly placeholder="Display"/></td>
                                
                                <td style="word-break: break-all;" ><input type="text" name="txtcolorrange[]" id="txtcolorrange_<?=$k; ?>" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" ><input type="text" name="txtcomposition[]" id="txtcomposition_<?=$k;?>" class="text_boxes" style="width:150px;" value="" readonly placeholder="Display"/></td>
                                <td style="word-break: break-all;" >
                                    <input type="button" id="increase_<?=$k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<?=$k; ?>);" readonly/>
                                    <input type="button" id="decrease_<?=$k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$k; ?>);" readonly/>
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
                            </td>
                            <td style="word-break: break-all;"><input type="text" name="txtsuppref[]" id="txtsuppref_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtconstraction[]" id="txtconstraction_1" class="text_boxes" style="width:100px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtfabconstraction[]" id="txtfabconstraction_1" class="text_boxes" style="width:140px;" readonly placeholder="Display"/></td>
                            
                            <td style="word-break: break-all;"><input type="text" name="txtstichlength[]" id="txtstichlength_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtweight[]" id="txtweight_1" class="text_boxes" style="width:60px;" readonly placeholder="Display"/></td>
                            
                            <td style="word-break: break-all;"><input type="text" name="txtcolorrange[]" id="txtcolorrange_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td style="word-break: break-all;"><input type="text" name="txtcomposition[]" id="txtcomposition_1" class="text_boxes" style="width:150px;" readonly placeholder="Display" /></td>
                            <td style="word-break: break-all;">
                                <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1);" />
                                <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                            </td>
                        </tr>
                <? } ?>
            </tbody>
            <tfoot class="tbl_bottom">
            	<td colspan="10">&nbsp;</td>
            </tfoot>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="950">
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
	
	$lib_fabric_composition=return_library_array("select id, fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
	
	$sqlRd="select id, stich_length, construction, gsm_weight, fabric_composition_id, supplier_reference, rd_no, color_range_id from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 $rdCond";
	$sqlRdData=sql_select($sqlRd);
	
	if(count($sqlRdData)>0)
	{
		echo "$('#txtrdno_".$ex_data[1]."').val('".$sqlRdData[0][csf('rd_no')]."');\n";
		echo "$('#hiddFabDeterId_".$ex_data[1]."').val('".$sqlRdData[0][csf('id')]."');\n"; 
		echo "$('#txtsuppref_".$ex_data[1]."').val('".$sqlRdData[0][csf('supplier_reference')]."');\n"; 
		echo "$('#txtconstraction_".$ex_data[1]."').val('".$sqlRdData[0][csf('construction')]."');\n"; 
		echo "$('#txtfabconstraction_".$ex_data[1]."').val('".$lib_fabric_composition[$sqlRdData[0][csf('fabric_composition_id')]]."');\n"; 
		echo "$('#txtstichlength_".$ex_data[1]."').val('".$sqlRdData[0][csf('stich_length')]."');\n"; 
		echo "$('#txtweight_".$ex_data[1]."').val('".$sqlRdData[0][csf('gsm_weight')]."');\n"; 
		echo "$('#txtcolorrange_".$ex_data[1]."').val('".$color_range[$sqlRdData[0][csf('color_range_id')]]."');\n"; 
		echo "$('#txtcomposition_".$ex_data[1]."').val('".$composition_arr[$sqlRdData[0][csf('id')]]."');\n";
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
                    	<td><?=create_drop_down( "cbo_fabric_nature",100, $business_nature_arr,"", 0, "", '2', "",$disabled,"2,3,100" ); ?></td>
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
    <table class="rpt_table" width="950px" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
        <thead>
        	<tr>
        		<th width="25">SL</th>
	            <th width="90">Fab Nature</th>
                <th width="60">RD No</th>
	            <th width="80">Supplier Ref</th>
	            <th width="130">Construction</th>
                <th width="150">Fabric Composition</th>
	            <th width="80">Stitch Length</th>
	            <th width="50">GSM</th>
	            <th width="70">Color Range</th>
	            <th>Composition</th>
        	</tr>
       </thead>
   </table>
   <div style="max-height:250px; width:950px; overflow-y:scroll">
       <table id="list_view" class="rpt_table" width="930px" height="" cellspacing="0" cellpadding="0" border="1" rules="all" >
            <tbody>
        <?
			$lib_fabric_composition=return_library_array("select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
			
            $sql_data=sql_select("select a.id, a.fab_nature_id, a.supplier_reference, a.construction, a.fabric_composition_id, a.stich_length, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a where a.is_deleted=0 and status_active=1 and  a.fab_nature_id= '$fabric_nature' $search_con order by a.id ASC");
            $i=1;
            foreach($sql_data as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr id="tr_<?=$row[csf('id')] ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('rd_no')]."_".$row[csf('supplier_reference')]."_".$row[csf('construction')]."_".$lib_fabric_composition[$row[csf('fabric_composition_id')]]."_".$row[csf('stich_length')]."_".$row[csf('gsm_weight')]."_".$color_range[$row[csf('color_range_id')]]."_".$composition_arr[$row[csf('id')]]; ?>')">
                        <td width="25" align="center"><?=$i; ?></td>
                        <td width="90" style="word-break:break-all"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
                        <td width="60" style="word-break:break-all"><?=$row[csf('rd_no')]; ?></td>
                        <td width="80" style="word-break:break-all"><?=$row[csf('supplier_reference')]; ?></td>
                        <td width="130" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
                        <td width="150" style="word-break:break-all"><?=$lib_fabric_composition[$row[csf('fabric_composition_id')]]; ?></td>
                        <td width="80" style="word-break:break-all"><?=$row[csf('stich_length')]; ?></td>
                        <td width="50" style="word-break:break-all"><?=$row[csf('gsm_weight')]; ?></td>
                        <td width="70" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
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

if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";

	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}

			if($yarn_description!="")
			{
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
			}
			else
			{
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
			}
		}
	}
	echo $fab_description."**".$yarn_description;
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
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
		$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");

	 $sql = "select system_number_prefix,system_number_prefix_num,system_number,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,buyer_request,remarks,dealing_marchant,gmts_item,est_ship_date,fabrication,offer_qty,color,req_quotation_date,target_sam_sub_date,actual_req_quot_date,actual_sam_send_date,department_name,buyer_target_price,buyer_submit_price,insert_by,insert_date,status_active,is_deleted from wo_quotation_inquery  where id=$mst_id and status_active=1  order by id";
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
                                <?php echo $result[csf('province')]; ?>
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
                <td align="center" style="font-size:20px;" colspan="4"> <strong>System ID :</strong> &nbsp; &nbsp;<?php echo $system_number; ?> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong> Buyer</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $buyer_arr[$buyer_id]; ?></td>
                <td align="left" style="font-size:20px;"><strong>Style Ref.</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $style_refernce; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Inq.Rcvd Date</strong></td>
                <td align="left" style="font-size:20px;"><?php echo change_date_format($inquery_date); ?> </td>
                <td align="left" style="font-size:20px;"><strong> Buyer Inquiry No </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $buyer_request; ?> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Dealing Merchandiser</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $marchentrArr[$dealing_marchant]; ?> </td>
                <td align="left" style="font-size:20px;"> <strong>Bulk Est. Ship Date</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($est_ship_date); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Gmts Item</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $garments_item[$gmts_item]; ?></td>
                <td align="left" style="font-size:20px;"><strong> Bulk Offer Qty</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $offer_qty; ?> </td>
            </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Fabrication</strong></td>
                <td align="left" style="font-size:20px;" colspan="3"> <p><?php echo $fabrication; ?></p> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong> Body Color </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $color; ?></td>
                <td align="left" style="font-size:20px;"> <strong> Season</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $season_name_library[$season_buyer_wise]; ?></td>
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
                <td align="left" style="font-size:20px;">  <strong>Department </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $department_name; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Actual Quot. Date</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($actual_req_quot_date); ?></td>
                <td align="left" style="font-size:20px;">  <strong>Buyer Target Price </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo number_format($buyer_target_price, 2); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Buyer Submit Price </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo number_format($buyer_submit_price, 2); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;">  <strong>Remarks </strong>  </td>
                <td align="left" style="font-size:20px;" colspan="3"> <?php echo $remarks; ?></td>
            </tr>
            <tr>
                <td colspan="4"><?  echo signature_table(126, $company_name, "850px"); ?></td>
            </tr>
        </table>
   	</div>
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
if($action=="populate_data_target_submit_price")
{
	$data_array=explode("__",$data);
	foreach ($data_array as $row)
	{
		$dataArr=explode("_",$row);
		$dates=date("Y-m-d",strtotime($dataArr[2]));
		// $date=date_format(strtotime(),"Y/m/d");
		
		$target_date_arr[$dataArr[0]][$dates]=$dates;
		$target_arr[$dataArr[0]][$dates]=$dataArr[3];
	
	}
	$max_date1=max($target_date_arr[1]);
	$max_date2=max($target_date_arr[2]);

		echo "document.getElementById('txt_buyer_submit_price').value = '".$target_arr[1][$max_date1]."';\n";
		echo "document.getElementById('txt_buyer_target_price').value = '".$target_arr[2][$max_date2]."';\n";
	exit();
}


if($action == 'app_notification'){

	
	
	include('../../auto_mail/setting/mail_setting.php');

	extract($_REQUEST);
	list($update_id,$sys_id,$email,$mail_body)=explode('__',$data);

	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$color_arr = return_library_array("select id,color_name from  lib_color ","id","color_name");
	$company_library = return_library_array("select id,company_name from lib_company","id","company_name");
	$season_name_library = return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');
	
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$marchentrMaillArr = return_library_array("select id,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","TEAM_MEMBER_EMAIL");
	
 
 
 	
	$sql = "select ID,system_number_prefix, system_number_prefix_num, system_number, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, buyer_request, remarks, dealing_marchant, gmts_item, est_ship_date, fabrication, offer_qty, color, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, insert_by, insert_date, status_active, is_deleted, season_year, brand_id,style_description,con_rec_target_date,concern_marchant,TEAM_LEADER,PRIORITY,COPY_SYSTEM_NUMBER,INSERT_BY from wo_quotation_inquery where id='".$sys_id."' and status_active=1  order by id";
	//echo $sql;die;
	
	$sql_result=sql_select($sql);
	$mstRow=$sql_result[0];
	$INSERTED_BY=$mstRow[csf('INSERT_BY')];
	$ID=$mstRow[ID];
	$company_name=$mstRow[csf('company_id')];
	 //echo $sql;die;
	
	
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}
	
	
	//print_r($composition_arr);die;

	
	
	
	$sqlRd="select ID, RD_NO, CONSTRUCTION, GSM_WEIGHT, WEIGHT_TYPE, DESIGN, FABRIC_REF, RD_NO, COLOR_RANGE_ID from lib_yarn_count_determina_mst where entry_form=426 and status_active=1 and is_deleted=0 and id in (".$mstRow[FABRICATION].") order by id";
	$sqlRdData=sql_select($sqlRd); 


	$imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME from common_photo_library where form_name in('quotation_inquery','quotation_inquery_front_image','quotation_inquery_back_image') and is_deleted=0  ".where_con_using_array(array($mstRow['ID']),1,'MASTER_TBLE_ID')."";//'quotation_entry',
	$imgSqlResult=sql_select($imgSql);
	foreach($imgSqlResult as $rows){
		$att_file_arr[]='../../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
	}
	//echo $imgSql;
		


	//-----------------------	
	$sql_team_mail="
	SELECT c.CAD_USER_NAME,d.USER_EMAIL, b.TEAM_LEADER_EMAIL  FROM WO_QUOTATION_INQUERY a,  LIB_MARKETING_TEAM b,   LIB_MKT_TEAM_MEMBER_INFO c,  USER_PASSWD d WHERE a.INSERT_BY = c.USER_TAG_ID  AND b.id = c.TEAM_ID   AND c.USER_TAG_ID = d.id  AND a.id = $ID and c.STATUS_ACTIVE=1 and c.IS_DELETED=0";
	//echo $sql_team_mail;die;
	$sql_team_mail_result=sql_select($sql_team_mail);
	$toArr=array();
	foreach($sql_team_mail_result as $rows){
		$toArr[0]=$rows[USER_EMAIL];
		$toArr[1]=$rows[TEAM_LEADER_EMAIL];
		$CAD_USER_NAME=$rows[CAD_USER_NAME];
	}

	if($CAD_USER_NAME!=''){$whereCon=" or d.id in(".$CAD_USER_NAME.")";}



	$sql_team_mail="SELECT d.USER_EMAIL from USER_PASSWD d WHERE d.id = $INSERTED_BY $whereCon";
	$sql_team_mail_result=sql_select($sql_team_mail);
	foreach($sql_team_mail_result as $rows){
		$toArr[]=$rows[USER_EMAIL];
	}

	$toArr[]=$marchentrMaillArr[$mstRow[csf(dealing_marchant)]];
	if($email){$toArr[]=$email;}

	ob_start();	

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
								foreach ($sql_result as $row)
								{
									$system_number= $row[csf('system_number')];
									$buyer_id= $row[csf('buyer_id')];
									$season_buyer_wise= $row[csf('season_buyer_wise')];
									$inquery_date= $row[csf('inquery_date')];
									$style_refernce= $row[csf('style_refernce')];
									$dealing_marchant= $row[csf('dealing_marchant')]; 
									$CONCERN_MARCHANT= $row[csf('CONCERN_MARCHANT')];
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
								$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width from lib_yarn_count_determina_mst where entry_form=426 and status_active=1 and is_deleted=0 and id in ($fabrication)";
								$sqlRdData=sql_select($sqlRd); $fabricationData="";
								foreach($sqlRdData as $row)
								{
									if($fabricationData=="") $fabricationData="* ".$row[csf('rd_no')].', '.$row[csf('fabric_ref')].', '.$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$row[csf('full_width')].', '.$row[csf('cutable_width')].', '.$composition_arr[$row[csf('id')]];
									else $fabricationData.="<br> * ".$row[csf('rd_no')].', '.$row[csf('fabric_ref')].', '.$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$row[csf('full_width')].', '.$row[csf('cutable_width')].', '.$composition_arr[$row[csf('id')]];
								}
								?>
								</td>
							</tr>
							<tr>
								<td align="center" style="font-size:20px">
									<strong> Buyer Inquiry Woven </strong>
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
					<td align="left" style="font-size:20px;"><?php echo $marchentrArr[$CONCERN_MARCHANT]; ?> </td>
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
					<td align="left" style="font-size:20px;" colspan="3">
					<? $sql = "select id,master_tble_id,image_location from common_photo_library where master_tble_id='$ID' and FORM_NAME in('quotation_inquery_back_image','quotation_inquery_front_image')"; 
					$data_array=sql_select($sql);
				?>
						<? foreach($data_array as $inf){ ?>
							<img  src='../../<? echo $inf[csf("image_location")]; ?>' height='100' width='100' style="float:left;" />
						<?  } ?>
			</td>
				</tr>
			</table>
			<?  //echo signature_table(126, $company_name, "850px"); ?>
		</div>       
			

			<?
			//$mstRow[BRAND_ID]
			
		$message=ob_get_contents();
		ob_clean();
		

		$to='';
		$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=40 and b.mail_user_setup_id=c.id and a.company_id =".$mstRow[COMPANY_ID]."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $sql;die;
		
		
		//$mail_sql=sql_select($sql);
		$receverMailArr=array();
		foreach($mail_sql____ as $row)
		{
			$buyerArr=explode(',',$row[BUYER_IDS]);
			$brandArr=explode(',',$row[BRAND_IDS]);
			foreach($buyerArr as $buyerid){
				foreach($brandArr as $brandid){
					$receverMailArr[$buyerid][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
				}
			}
			
		}

		$to=implode(',',array_unique($toArr));
		echo $message; die;
		
		$subject="Buyer Inquiry Woven";
		$header=mailHeader();
		if($to!=""){
			echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );
			//--------------------------last mail send update info---------------------------------------------------
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
			
				$rID=sql_update("wo_quotation_inquery",'mail_send_date',"'".$pc_date_time."'","id","".$ID."",0,0);
				//echo $rID;die;
				if($db_type==0)
				{
					if($rID==1){
						mysql_query("COMMIT");
						//echo "1**".str_replace("'","",$update_id);
					}
					else{
						mysql_query("ROLLBACK");
						//echo "10**".str_replace("'","",$update_id);
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID==1){
						oci_commit($con);
						//echo "1**".$update_id;
					}
					else{
						oci_rollback($con);
						//echo "10**".$update_id;
					}
				}
				disconnect($con);
				die;
				
			//-------------------------------------------------------------------------
		
		}
		else{echo "Mail Not Send";}
	
	
}
?>