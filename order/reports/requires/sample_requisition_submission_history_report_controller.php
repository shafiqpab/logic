<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0)
{
	$group_concat="group_concat";
	$select_year="year";
	$year_con="";
	$defalt_date_format="0000-00-00";
}
else
{
	$group_concat="wm_concat";
	$select_year="to_char";
	$year_con=",'YYYY'";
	$defalt_date_format="";
}
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info", "id", "team_member_name"  );
$sample_arr=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
$team_leader_arr=return_library_array( "select b.id as deaaling_id, a.team_leader_name as leader_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.status_active=1 and b.is_deleted=0", "deaaling_id", "leader_name"  );
$dealing_marchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
$season_library=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name"  );

$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
$imageBack_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='samplereqbackimage_1' and file_type=1",'master_tble_id','image_location');
$imageFront_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='samplereqfrontimage_1' and file_type=1",'master_tble_id','image_location');
$imagetrims_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='required_accessories_1' and file_type=1",'master_tble_id','image_location');
$file_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_requisition_1' and file_type=2",'master_tble_id','image_location');

//--------------------------------------------------------------------------------------------------------------------

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_season', 'season_td');");
		exit();
	}
}
if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}

if($action=="image_view_popup")
{
extract($_REQUEST);
echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
$imge_data=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='samplereqfrontimage_1' and file_type=1 and master_tble_id='$id'");
?>
<table>
<tr>
<?
foreach($imge_data as $row)
{
?>
<td><img   src='../../../<? echo $row[csf('image_location')]; ?>' height='100%' width='100%' /></td>
<?
}
?>

</tr>

</table>

<?

}

if($action=="image_view_popup2")
{
extract($_REQUEST);
echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
$imge_data2=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='samplereqbackimage_1' and file_type=1 and master_tble_id='$id'");
?>
<table>
<tr>
<?
foreach($imge_data2 as $row)
{
?>
<td><img   src='../../../<? echo $row[csf('image_location')]; ?>' height='100%' width='100%' /></td>
<?
}
?>

</tr>

</table>

<?

}

if($action=="image_view_popup3")
{
extract($_REQUEST);
echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
$imge_data3=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='required_accessories_1' and file_type=1 and master_tble_id='$id'");
?>
<table>
<tr>
<?
foreach($imge_data3 as $row)
{
?>
<td><img   src='../../../<? echo $row[csf('image_location')]; ?>' height='100%' width='100%' /></td>
<?
}
?>

</tr>

</table>

<?

}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//if(str_replace("'","",$cbo_company_name)==0) $company_name="%%"; else $company_name=str_replace("'","",$cbo_company_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	if($cbo_company_name) $company_cond=" and a.company_id in($cbo_company_name)"; else $company_cond="";
	if($cbo_company_name) $company_cond2=" and c.company_name in($cbo_company_name)"; else $company_cond2="";
	//echo $company_cond;
	if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$style_cond="";
	if(str_replace("'","",$txt_style)!="") $style_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style)."%'  ";
	$req_cond="";
	if(str_replace("'","",$txt_req)!="") $req_cond=" and a.requisition_number_prefix_num like '%".str_replace("'","",$txt_req)."%'  ";
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	$cbo_year=str_replace("'","",$cbo_year);
	$year_field=" and to_char(a.insert_date,'YYYY')";
	if($cbo_year!=0) $year_cond=" $year_field=$cbo_year"; else $year_cond="";

	$cbo_brand_id = str_replace("'", "", $cbo_brand_id);
	if ($cbo_brand_id > 0) $brand_id_cond = "and a.brand_id in ($cbo_brand_id)";
	else $brand_id_cond = "";

	$txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
	$based_on=str_replace("'","",$cbo_based_on);
	if($based_on==1){
		if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and a.requisition_date between '".$txt_date_from."' and '".$txt_date_to."'"; else $date_cond="";
	}else if($based_on==2){
		if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and his.ready_to_approved=1 and his.update_date  between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'"; else $date_cond="";
	}else if($based_on==3){
		if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and s.checklist_date between '".$txt_date_from."' and '".$txt_date_to."'"; else $date_cond="";
	}else if($based_on==4){
		if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and h.insert_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'"; else $date_cond="";
	}else{
		if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and h.unacknowledge_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'"; else $date_cond="";
	}
	/* if($template==1)
	{ */
		ob_start();
	?>
		<div style="width:3630px">
		<fieldset style="width:100%;">	
			<table width="3630">
				<tr class="form_caption">
					<td colspan="37" align="center">Sample Requisition Submission History Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="37" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
            
            <br />
            <table class="rpt_table" width="3630" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">Sl</th>
					<th width="100">Company</th>
					<th width="100">Requisition No</th>
					<th width="60">Requisition Year</th>
					<th width="100">Job No</th>
                    <th width="120">Requisition Date</th>
					<th width="100">Marchent Submission Date</th>
					<th width="100">Team Leader</th>
                    <th width="90">Merchandiser</th>
					<th width="100">Product. Dept</th>
                    <th width="100">Buyer</th>
					<th width="100">Brand</th>
                    <th width="100">Season</th>
                    <th width="100">Season Year</th>
                    <th width="100">Style Ref</th>
					<th width="100">Fab Ref</th>
					<th width="150">Fabrication</th>
                    <th width="100">Fabric Color</th>
					<th width="100">Trims Details</th>
					<th width="100">Sample Stage </th>
					<th width="120">Sample Type</th>
					<th width="100">Sample Color</th>
					<th width="100">Garments Item</th>
					<th width="80">Sample Qty</th>
                    <th width="100">Sample Size</th>
					<th width="100">Image Front</th>
					<th width="100">Image Back</th>
					<th width="100">Required Wash</th>
					<th width="100">Required Print</th>
					<th width="100">Required Emb</th>  
					<th width="100">File</th>    
					<th width="100">Checklist Id</th>
					<th width="100">Checklist Date</th>    
                    <th width="100">Req. Ackn Date</th>
                    <th width="100">Req. Un Ackn Date</th>
                    <th width="85">Refusing Cause</th>
                    <th width="85">Remarks</th>
				</thead>
			</table>
			<div style="width:3650px; max-height:600px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="3630" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
					$sql_size=sql_select("select b.sample_mst_id,c.size_id from sample_development_mst a, sample_development_dtls b left join sample_development_size c on b.sample_mst_id=c.mst_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0 where a.id=b.sample_mst_id $company_cond $buyer_id_cond $sample_stages $brand_id_cond $req_cond $style_cond $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=449 ");
					foreach($sql_size as $row)
					{
						$size_reference_arr[$row[csf("sample_mst_id")]]["size_ids"].=$size_arr[$row[csf("size_id")]].',';
					}
					unset($sql_size);
					$sql_fabric_ref=sql_select("select b.id,b.sample_mst_id,d.fabric_ref from sample_development_mst a left join sample_checklist_mst s on a.id=s.requisition_id and s.status_active=1 left join sample_requisition_acknowledge h on a.id=h.sample_mst_id and h.status_active=1, sample_development_dtls b, sample_development_fabric_acc c left join lib_yarn_count_determina_mst d on c.determination_id=d.id and d.status_active=1 where a.id=b.sample_mst_id and a.id=c.sample_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form_id=449 $company_cond $buyer_id_cond $style_cond $team_cond $sample_stages $brand_id_cond $req_cond");
					foreach($sql_fabric_ref as $row)
					{
						if($row[csf("fabric_ref")]!=''){
							$fabric_ref_arr[$row[csf("sample_mst_id")]]["fabric_ref"].=$row[csf("fabric_ref")].',';
						}
					}
					$sql_required_fabric=sql_select("select b.id,b.sample_mst_id,c.fabric_description,c.fabric_source,d.color_id from sample_development_mst a left join sample_checklist_mst s on a.id=s.requisition_id and s.status_active=1 left join sample_requisition_acknowledge h on a.id=h.sample_mst_id and h.status_active=1, sample_development_dtls b, sample_development_fabric_acc c,sample_development_rf_color d where a.id=b.sample_mst_id and a.id=c.sample_mst_id and a.id=d.mst_id and c.id=d.dtls_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.form_type = 1 and a.entry_form_id=449 $company_cond $buyer_id_cond $style_cond $team_cond $sample_stages $brand_id_cond $req_cond $year_cond");
					foreach($sql_required_fabric as $row)
					{
						$fabric_desc_arr[$row[csf("sample_mst_id")]]["fabric_description"].=$row[csf("fabric_description")].',';
						$fabric_color_arr[$row[csf("sample_mst_id")]]["fabric_color"].=$color_name_library[$row[csf("color_id")]].',';
					}
					unset($sql_required_fabric);
					$wash_yes_no=sql_select("select a.id as chk_id,c.sample_mst_id as wash_chk from sample_development_mst a, sample_development_fabric_acc c where a.id=c.sample_mst_id and form_type=3 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form_id=449 $company_cond $buyer_id_cond $style_cond $team_cond $sample_stages $brand_id_cond $req_cond $year_cond");
					foreach($wash_yes_no as $row)
					{
						$wash_chk_arr[$row[csf("chk_id")]]["wash_chk"]=$row[csf("wash_chk")];
					}
					unset($wash_yes_no);
					$print_yes_no=sql_select("select a.id as chk_id,c.sample_mst_id as print_chk from sample_development_mst a, sample_development_fabric_acc c where a.id=c.sample_mst_id and form_type=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form_id=449 $company_cond $buyer_id_cond $style_cond $team_cond $sample_stages $brand_id_cond $req_cond $year_cond");
					foreach($print_yes_no as $row)
					{
						$print_chk_arr[$row[csf("chk_id")]]["print_chk"]=$row[csf("print_chk")];
					}
					unset($print_yes_no);
					$emb_yes_no=sql_select("select a.id as chk_id,c.sample_mst_id as embellishment_chk from sample_development_mst a, sample_development_fabric_acc c where a.id=c.sample_mst_id and form_type=5 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form_id=449 $company_cond $buyer_id_cond $style_cond $team_cond $sample_stages $brand_id_cond $req_cond $year_cond");
					foreach($emb_yes_no as $row)
					{
						$embellishment_chk_arr[$row[csf("chk_id")]]["embellishment_chk"]=$row[csf("embellishment_chk")];
					}
					unset($emb_yes_no);
					$sql_mst="select a.id as mst_id,a.remarks,a.buyer_name,a.sample_stage_id,a.style_ref_no,a.company_id,a.requisition_number,a.requisition_date,a.dealing_marchant,to_char (a.insert_date, 'yyyy') as job_year,a.product_dept,a.season_buyer_wise as season,a.season_year,a.brand_id,c.job_no,b.gmts_item_id,b.sample_name,b.sample_color,b.sample_prod_qty,a.entry_form_id,a.quotation_id from  sample_development_mst a left join wo_po_details_master c on a.style_ref_no=c.style_ref_no and c.status_active=1 $company_cond2 left join sample_checklist_mst s on a.id=s.requisition_id and s.status_active=1 left join sample_requisition_acknowledge h on a.id=h.sample_mst_id and h.status_active=1 left join ready_to_approved_his his on a.id=his.mst_id, sample_development_dtls b where a.id=b.sample_mst_id $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=449 $buyer_id_cond $date_cond $style_cond $team_cond $sample_stages $brand_id_cond $req_cond $year_cond group by a.id,a.remarks,a.buyer_name,a.style_ref_no,a.company_id,a.requisition_number,a.requisition_date,a.dealing_marchant,a.product_dept,a.insert_date,a.brand_id,a.season_buyer_wise,a.sample_stage_id,a.season_year,c.job_no,b.gmts_item_id,b.sample_name,b.sample_color,b.sample_prod_qty,a.entry_form_id,a.quotation_id order by a.requisition_number"; 
					//echo $sql_mst;die;
					$nameArray_mst=sql_select($sql_mst);
					$tot_rows=count($nameArray_mst);
					$reference_arr=array();
					foreach($nameArray_mst as $row)
					{
						$size_ids=implode(",",array_unique(explode(",",chop($size_reference_arr[$row[csf("mst_id")]]["size_ids"],","))));
						$fabric_ref=implode(",",array_unique(explode(",",chop($fabric_ref_arr[$row[csf("mst_id")]]["fabric_ref"],","))));
						$fabric_description=implode(",",array_unique(explode(",",chop($fabric_desc_arr[$row[csf("mst_id")]]["fabric_description"],","))));
						$fabric_color=implode(",",array_unique(explode(",",chop($fabric_color_arr[$row[csf("mst_id")]]["fabric_color"],","))));
						$reference_arr[$row[csf("mst_id")]]["mst_id"]=$row[csf("mst_id")];
						$reference_arr[$row[csf("mst_id")]]["buyer_name"]=$row[csf("buyer_name")];
						$reference_arr[$row[csf("mst_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$reference_arr[$row[csf("mst_id")]]["company_id"]=$row[csf("company_id")];
						$reference_arr[$row[csf("mst_id")]]["requisition_number"]=$row[csf("requisition_number")];
						$reference_arr[$row[csf("mst_id")]]["requisition_date"]=$row[csf("requisition_date")];
						$reference_arr[$row[csf("mst_id")]]["job_year"]=$row[csf("job_year")];
						$reference_arr[$row[csf("mst_id")]]["season"]=$row[csf("season")];
						$reference_arr[$row[csf("mst_id")]]["season_year"]=$row[csf("season_year")];
						$reference_arr[$row[csf("mst_id")]]["brand_id"]=$row[csf("brand_id")];
						$reference_arr[$row[csf("mst_id")]]["job_no"]=$row[csf("job_no")];
						$reference_arr[$row[csf("mst_id")]]["product_dept"]=$row[csf("product_dept")];
						$reference_arr[$row[csf("mst_id")]]["sample_stage_ids"]=$row[csf("sample_stage_id")];
						$reference_arr[$row[csf("mst_id")]]["sample_stage"]=$sample_stage[$row[csf("sample_stage_id")]];
						$reference_arr[$row[csf("mst_id")]]["gmts_item"].=$garments_item[$row[csf("gmts_item_id")]].',';
						$reference_arr[$row[csf("mst_id")]]["sample_name"].=$sample_arr[$row[csf('sample_name')]].',';
						$reference_arr[$row[csf("mst_id")]]["sample_color"].=$color_name_library[$row[csf("sample_color")]].',';
						$reference_arr[$row[csf("mst_id")]]["dealing_marchant"]=$dealing_marchant_arr[$row[csf("dealing_marchant")]];
						$reference_arr[$row[csf("mst_id")]]["team_leader"]=$team_leader_arr[$row[csf("dealing_marchant")]];
						$reference_arr[$row[csf("mst_id")]]["remarks"]=$row[csf("remarks")];
						$reference_arr[$row[csf("mst_id")]]["sample_prod_qty"]+=$row[csf("sample_prod_qty")];
						$reference_arr[$row[csf("mst_id")]]["size_id"]=$size_ids;
						$reference_arr[$row[csf("mst_id")]]["fabric_reference"]=$fabric_ref;
						$reference_arr[$row[csf("mst_id")]]["fabric_descrip"]=$fabric_description;
						$reference_arr[$row[csf("mst_id")]]["fabric_color"]=$fabric_color;
						$reference_arr[$row[csf("mst_id")]]["wash_yes_no"]=$wash_chk_arr[$row[csf("mst_id")]]["wash_chk"];
						$reference_arr[$row[csf("mst_id")]]["print_yes_no"]=$print_chk_arr[$row[csf("mst_id")]]["print_chk"];
						$reference_arr[$row[csf("mst_id")]]["emb_yes_no"]=$embellishment_chk_arr[$row[csf("mst_id")]]["embellishment_chk"];
						$reference_arr[$row[csf("mst_id")]]["entry_form_id"]=$row[csf("entry_form_id")];
						$reference_arr[$row[csf("mst_id")]]["quotation_id"]=$row[csf("quotation_id")];
					}
					
					
					$checklist_status_sql=sql_select("SELECT requisition_id ,completion_status,checklist_number,checklist_date from sample_checklist_mst where status_active=1 and is_deleted=0");
					foreach($checklist_status_sql as $checklist_status_value)
					{
						$checklist_status_arr[$checklist_status_value[csf("requisition_id")]]=$checklist_status_value[csf("completion_status")];
						$checklist_no_arr[$checklist_status_value[csf("requisition_id")]]=$checklist_status_value[csf("checklist_number")];
						$checklist_date_arr[$checklist_status_value[csf("requisition_id")]]=$checklist_status_value[csf("checklist_date")];
					}
					unset($checklist_status_sql);
					$booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
					foreach($booking_without_order_sql as $vals)
					{
						$booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
					}
					unset($booking_without_order_sql);
                    $i=1;
					foreach ($reference_arr as $req_id=>$row_mst) 
					{
						if($row_mst[('wash_yes_no')]!=""){ $wash_chk='YES';}
						else{ $wash_chk='NO'; }

						if($row_mst[('print_yes_no')]!=""){ $print_chk='YES'; }
						else{ $print_chk='NO'; }

						if($row_mst[('emb_yes_no')]!=""){ $embellishment_chk='YES';}
						else{ $embellishment_chk='NO'; }
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($row_mst['sample_stage_ids']==1) $booking_no= $booking_arr[$row_mst["quotation_id"]];
						else $booking_no= $booking_without_order_arr[$row_mst["id"]];

						$entry_form_id=$row_mst['entry_form_id'] ;
						$link_format=""; $buttonAction="";
						$page_path='';

						$link_format="'../../order/woven_gmts/requires/sample_requisition_with_booking_controller'";
                    	$buttonAction="sample_requisition_print1";
                    	$page_path="'".$booking_no."'+'*'+1+'*'+0";
						
						?>
                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_d<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_d<? echo $s; ?>">
                        
                            <td width="40" align="center"><? echo $i;?></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $company_library[$row_mst[('company_id')]]; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><a href='##' onClick="print_report(<? echo $row_mst[('company_id')]; ?>+'*'+<? echo $row_mst[('mst_id')]; ?>+'*'+<? echo $page_path; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $row_mst[('requisition_number')]; ?></a></td>
							<td width="60" align="center" style="word-break:break-all"><p><? echo $row_mst[('job_year')]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('job_no')]; ?></p></td>
                            <td width="120" align="center" style="word-break:break-all"><p><? echo $row_mst[('requisition_date')]; ?></p></td>
							<td width="100" align="center"><? $req_id=$row_mst[('mst_id')];echo "<p><a href='##'  onclick=\"openmypage_submission( '$req_id' ,'submission_popup');\" >View</a></p>"; ?> </td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('team_leader')];?></p></td>
							<td width="90" align="center" style="word-break:break-all"><p><? echo $row_mst[('dealing_marchant')];?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $product_dept[$row_mst[('product_dept')]]; ?></p></td>
                            <td width="100" align="center" style="word-break:break-all"><p><? echo $buyer_short_name_library[$row_mst[('buyer_name')]]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $brand_name_arr[$row_mst[('brand_id')]]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $season_library[$row_mst[('season')]]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('season_year')]; ?></p></td>
                            <td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('style_ref_no')]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('fabric_reference')]; ?></p></td>
							<td width="150" align="center" style="word-break:break-all"><p><? echo $row_mst[('fabric_descrip')]; ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('fabric_color')]; ?></p></td>
							<td width="100" align="center"><? $req_id=$row_mst[('mst_id')];echo "<p><a href='##'  onclick=\"openmypage_trims( '$req_id' ,'trims_popup');\" >View</a></p>"; ?> </td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $row_mst[('sample_stage')]; ?></p></td> 
							<td width="120" align="center" style="word-break:break-all"><? echo implode(",",array_unique(explode(",",chop($row_mst[('sample_name')],",")))); ?></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo implode(",",array_unique(explode(",",chop($row_mst[('sample_color')],",")))); ?></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo implode(",",array_unique(explode(",",chop($row_mst[('gmts_item')],",")))); ?></p></td>
							<td width="80" align="center" style="word-break:break-all"><? echo $row_mst[('sample_prod_qty')]; ?></td>
							<td width="100" align="center" style="word-break:break-all"><? echo $row_mst[('size_id')]; ?></td>
							<td width="100" align="center" style="word-break:break-all"><p><img onclick="openImageWindow( <? echo $row_mst[('mst_id')]; ?> )"  src='../../<? echo $imageFront_arr[$row_mst[('mst_id')]]; ?>' height='30' width='50' /></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><img onclick="openImageWindow2( <? echo $row_mst[('mst_id')]; ?> )"  src='../../<? echo $imageBack_arr[$row_mst[('mst_id')]]; ?>' height='30' width='50' /></p></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo $wash_chk; ?></p></td>  
							<td width="100" align="center" style="word-break:break-all"><p><? echo $print_chk; ?></p></td> 
							<td width="100" align="center" style="word-break:break-all"><p><? echo $embellishment_chk; ?></p></td> 
							<td width="100" align="center" style="word-break:break-all"><p>
							<?
							if($file_arr[$row_mst[('mst_id')]] !="")
							{
							?>
                            <input type="button" id="image_button" class="image_uploader" style="width:90px" value="Attachment" onClick="file_uploader ( '../../', <? echo $row_mst[('mst_id')]; ?>,'', 'sample_requisition_1', 2 ,1,2)" />
                            <?
							}
							?>
							</p></td>
							<td width="100" align="center" style="word-break:break-all"><? $req_id=$row_mst[('mst_id')];$checklist_id=$checklist_no_arr[$row_mst[('mst_id')]]; if($checklist_status_arr[$row_mst[('mst_id')]]==1){echo "<p><a href='##'  onclick=\"openmypage_checklist( '$req_id' ,'checklist_popup','YES');\" >$checklist_id</a></p>";}
                    		else{ echo "<p><a href='##'  onclick=\"openmypage_checklist( '$req_id' ,'checklist_popup','NO');\" >$checklist_id</a></p>";} ?></td>
							<td width="100" align="center" style="word-break:break-all"><p><? echo change_date_format($checklist_date_arr[$row_mst[('mst_id')]]); ?></p></td>
							<td width="100" align="center"><? $req_id=$row_mst[('mst_id')];echo "<p><a href='##'  onclick=\"openmypage_ack( '$req_id' ,'ack_date_popup');\" >View</a></p>"; ?> </td>
							<td width="100" align="center"><? $req_id=$row_mst[('mst_id')];echo "<p><a href='##'  onclick=\"openmypage_unack( '$req_id' ,'unack_date_popup');\" >View</a></p>"; ?> </td>
							<td width="85" align="center" style="word-break:break-all"><? $req_id=$row_mst[('mst_id')];echo "<p><a href='##'  onclick=\"openmypage_refusingCause( '$req_id' ,'refusing_popup');\" >View</a></p>"; ?></td>
							<td width="85" align="center" style="word-break:break-all"><? echo $row_mst[('remarks')]; ?></td>
                        </tr>
                        <?	
				$i++;
				}
				
				?>
				</table>
				<table class="rpt_table" width="3630" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="40">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>  
						<th width="100">&nbsp;</th>    
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>    
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="85">&nbsp;</th>
						<th width="85">&nbsp;</th>
					</tfoot>
				</table>
			</div>
			</fieldset>
		</div>
	<?
	//}
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}
disconnect($con);

if($action=='checklist_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> <? if($type=="NO"){echo "Not";} ?> Checklist Info</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="50" >SL</th>
                <th width="" >Name</th>
             </thead>

        <?
             $checklist_arr=$sample_checklist_set;
             $sql= sql_select("select checklist_id,requisition_id from sample_checklist_dtls where status_active=1 and is_deleted=0 and requisition_id='$req_id'");
             if($type=="NO")
             {
                 foreach($sql as $val)
                 {
                    unset($checklist_arr[$val[csf("checklist_id")]]);
                 }
             }

             if($type=="YES")
             {
                foreach($sql as $val)
                 {
                   $checklist_arrs[$val[csf("checklist_id")]]= $checklist_arr[$val[csf("checklist_id")]];
                 }
             }

              $i=1;
              if($type=="YES"){$checklist_arr=$checklist_arrs;}
             foreach($checklist_arr as $name)
             {

                ?>
                <tr>
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="" align="center"><strong><? echo  $name; ?> </strong></td>

                </tr>
                 <?
                 $i++;
             }
         ?>
        </table>
     </div>
    </fieldset>
	<?
    exit();
}

if($action=='refusing_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>  Refusing Cause Info</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
        <?
              $sql= "select id,refusing_cause from sample_development_mst where status_active=1 and is_deleted=0 and id='$req_id' and entry_form_id in(449)";
              $sql_sel=sql_select($sql);
              $i=1;

                 foreach($sql_sel as $val)
                  {
                   if($val[csf("refusing_cause")]!="")
                   {
                    ?>
                    <thead>
                <th width="50" >SL</th>
                <th width="" >Cause</th>
             </thead>
                <tr>
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="" align="center"><strong style="font-size: 16px;"><? echo  $val[csf("refusing_cause")]; ?> </strong></td>

                </tr>
                 <?
                 $i++;
                   }
                 }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='ack_date_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../../", 1, 1,$unicode,'','');
    $user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
 ?>
    <fieldset>
    <legend>Acknowledge Date</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
        <?
            $sql= "SELECT a.id, b.insert_date as req_acknowledge_date,b.inserted_by from sample_development_mst a,sample_requisition_acknowledge b where a.entry_form_id in (449) and a.id='$req_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.sample_mst_id order by a.id desc";
            $sql_sel=sql_select($sql);
            $i=1;

            foreach($sql_sel as $val)
            {
                if($val[csf("req_acknowledge_date")]!="")
                {
                ?>
                    <thead>
						<th width="50" >SL</th>
						<th width="70" >Ack. Date.</th>
						<th width="50" >Ack . Time</th>
						<th width="70" >Insert User</th>
             		</thead>
                	<tr>
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="70" align="center" ><? echo date('d-m-Y',strtotime($val[csf("req_acknowledge_date")])); ?></td>
						<td width="50" align="center"><? echo date('h:i',strtotime($val[csf("req_acknowledge_date")])); ?></td>
						<td width="70" align="center"><? echo  $user_name_arr[$val[csf("inserted_by")]]; ?> </td>
                	</tr>
                	<?
                	$i++;
                }
            }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='unack_date_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../../", 1, 1,$unicode,'','');
    $user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
 ?>
    <fieldset>
    <legend>Unacknowledge Date</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
		<thead>
			<th width="50" >SL</th>
			<th width="70" >Unack. Date.</th>
			<th width="50" >Unack . Time</th>
			<th width="70" >Insert User</th>
		</thead>
        <?
            $sql= "SELECT a.id, b.unacknowledge_date as unacknowledge_date,b.inserted_by from sample_development_mst a,sample_requisition_acknowledge b where a.entry_form_id in (449) and a.id='$req_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.sample_mst_id order by a.id desc";
            $sql_sel=sql_select($sql);
            $i=1;

            foreach($sql_sel as $val)
            {
                if($val[csf("unacknowledge_date")]!="")
                {
                ?>
                	<tr>
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="70" align="center" ><? echo date('d-m-Y',strtotime($val[csf("unacknowledge_date")])); ?></td>
						<td width="50" align="center"><? echo date('h:i',strtotime($val[csf("unacknowledge_date")])); ?></td>
						<td width="70" align="center"><? echo  $user_name_arr[$val[csf("inserted_by")]]; ?> </td>
                	</tr>
                	<?
                	$i++;
                }
            }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='submission_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../../", 1, 1,$unicode,'','');
    $user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
 ?>
    <fieldset>
    <legend>Submission Date</legend>
     <div style="width:420px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
		<thead>
			<th width="30" >SL</th>
			<th width="70" >Sub. Date.</th>
			<th width="50" >Sub . Time</th>
			<th width="50" >Sub . Statu</th>
			<th width="70" >Insert User</th>
		</thead>
        <?
            $sql= "SELECT a.id,b.updated_by,b.update_date,b.ready_to_approved from sample_development_mst a,ready_to_approved_his b where a.entry_form_id in (449) and a.id='$req_id' and a.id=b.mst_id and a.entry_form_id=b.entry_form and a.status_active=1 and a.is_deleted=0  order by a.id";
            $sql_sel=sql_select($sql);
            $i=1;

            foreach($sql_sel as $val)
            { 
                ?>
                	<tr>
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="70" align="center" ><? echo date('d-m-Y',strtotime($val[csf("update_date")])); ?></td>
						<td width="50" align="center"><? echo date('h:i',strtotime($val[csf("update_date")])); ?></td>
						<td width="50" align="center"><? echo $yes_no[$val[csf("ready_to_approved")]]; ?></td>
						<td width="70" align="center"><? echo  $user_name_arr[$val[csf("updated_by")]]; ?> </td>
                	</tr>
                	<?
                	$i++;
            }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='trims_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../../", 1, 1,$unicode,'','');
	$itemGroupArr=return_library_array("select id, item_name from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
 ?>
    <fieldset>
    <legend>Trims Details</legend>
     <div style="width:930px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
		<thead>
			<th width="30" >SL</th>
			<th width="100" >Trims Group</th>
			<th width="100" >Description</th>
			<th width="100" >Brand/ Supp. Ref</th>
			<th width="50" >UOM</th>
			<th width="50" >Req/Dzn</th>
			<th width="70" >Req. Qty.</th>
			<th width="100" >Acc. Del. Date</th>
			<th width="100" >Acc. Source</th>
			<th width="100" >Remarks</th>
			<th width="100" >Image</th>
		</thead>
        <?
            $sql= "SELECT a.id, b.id as trims_id,b.trims_group_ra,b.description_ra,b.brand_ref_ra,uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra,delivery_date, fabric_source from sample_development_mst a,sample_development_fabric_acc b where a.entry_form_id in (449) and b.form_type=2 and a.id='$req_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.sample_mst_id order by a.id desc";
            $sql_trims=sql_select($sql);
            $i=1;

            foreach($sql_trims as $val)
            {
               
                ?>
                	<tr>
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100" align="center" ><? echo $itemGroupArr[$val[csf("trims_group_ra")]]; ?></td>
						<td width="100" align="center" ><? echo $val[csf("description_ra")]; ?></td>
						<td width="100" align="center" ><? echo $val[csf("brand_ref_ra")]; ?></td>
						<td width="50" align="center" ><? echo $unit_of_measurement[$val[csf("uom_id_ra")]]; ?></td>
						<td width="50" align="center" ><? echo $val[csf("req_dzn_ra")]; ?></td>
						<td width="70" align="center" ><? echo $val[csf("req_qty_ra")]; ?></td>
						<td width="100" align="center" ><? echo $val[csf("delivery_date")]; ?></td>
						<td width="100" align="center" ><? echo $fabric_source[$val[csf("fabric_source")]]; ?></td>
						<td width="100" align="center" ><? echo $val[csf("remarks_ra")]; ?></td>
						<td width="100" align="center" ><p><img onclick="openImageWindow3( <? echo $val[csf('trims_id')]; ?> )"  src='../../../<? echo $imagetrims_arr[$val[csf('trims_id')]]; ?>' height='15' width='30' /></p></td>
                	</tr>
                	<?
                	$i++;
            }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}
?>