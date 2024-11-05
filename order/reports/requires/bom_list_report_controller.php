<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
//include('../../../includes/class4/class.fabrics2.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
    ?>
    <script>
	    var selected_id = new Array;
		var selected_name = new Array;
		var selected_style_name = new Array();

	    function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{ 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var selectStyle = splitSTR[3];
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
					
			if( jQuery.inArray( selectID, selected_id, selectStyle ) == -1 )
			{
			    selected_id.push( selectID );
			    selected_name.push( selectDESC );					
			    selected_style_name.push( selectStyle );					
			}
			else
		    {
				for( var i = 0; i < selected_id.length; i++ )
				{
				    if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_style_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var style = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
			    id += selected_id[i] + ',';
			    name += selected_name[i] + ','; 
			    style += selected_style_name[i] + ','; 
			}
			id 	 = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 ); 
			style = style.substr( 0, style.length - 1 ); 
			$('#txt_job_id').val( id );
		    $('#txt_job_no').val( name );
		}
	</script>
    <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");

	$sql= "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC ";

	//echo $sql;die;

	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','',1) ;
	echo "<input type='hidden' id='txt_job_id' />";
	echo "<input type='hidden' id='txt_job_no' />";
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_id=str_replace("'","",$hidd_job_id);
	$appstatus=str_replace("'","",$cbo_appstatus);
	$marginupto=str_replace("'","",$cbo_marginupto);
	$marginvalue=str_replace("'","",$txt_marginvalue)*1;
	$datetype=str_replace("'","",$cbo_datetype);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$garments_nature=str_replace("'","",$garments_nature);
	$budget_type=str_replace("'","",$budget_type);

	if($garments_nature==''){
		$garments_nature=2;
	}
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyerCond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyerCond="";
		}
		else $buyerCond="";
	}
	else $buyerCond=" and a.buyer_name=$cbo_buyer";
	
	if($db_type==0) 
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	$jobCond="";
	if ($job_id!="") 
	{
		$jobCond=" and a.id in ($job_id)";
	}
	else if($job_no!="")
	{
		$jobCond=" and a.job_no_prefix_num in ($job_no)";
	}
	
	if ($appstatus==1) $appstatusCond=" and b.approved in (1,3)"; else if ($appstatus==2) $appstatusCond=" and b.approved in (0,2)"; else $appstatusCond="";
	
	if ($main_booking=="") $main_booking_cond=""; else $main_booking_cond=" and b.booking_no_prefix_num='$main_booking'";
	if ($short_booking=="") $short_booking_cond=""; else $short_booking_cond=" and b.booking_no_prefix_num='$short_booking'";
	if ($cause_type==0) $cause_type_cond=""; else $cause_type_cond=" and d.cause_id='$cause_type'";
	
	if($db_type==0)
	{
		if($datetype==1)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and b.costing_date between '".$date_from."' and '".$date_to."'";
		}
		else if($datetype==2)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and c.shipment_date between '".$date_from."' and '".$date_to."'";
		}
	}
	else if($db_type==2)
	{
		if($datetype==1)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and b.costing_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
		else if($datetype==2)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and c.shipment_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$factoryMarArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");

	if($budget_type==2){
		if($garments_nature==2){
			$report_id=43;
		}
		elseif($garments_nature==3){
			$report_id=122;
		}
		else{
			$report_id=43;
		}
		$print_report_format2=return_field_value("format_id","lib_report_template","template_name =".$cbo_company." and module_id=2 and report_id=$report_id and is_deleted=0 and status_active=1");//Pre-Costing V2-Woven
		$print_button2=explode(",",$print_report_format2);
		if($print_button2[0]==50) $action_v3="preCostRpt";
		else if($print_button2[0]==51) $action_v3="preCostRpt2";
		else if($print_button2[0]==63) $action_v3="bomRpt2";
		else if($print_button2[0]==171) $action_v3="preCostRpt4";
		else if($print_button2[0]==173) $action_v3="preCostRpt5";
		else if($print_button2[0]==405) $action_v3="materialSheet2";
		else if($print_button2[0]==52) $action_v3="bomRpt";
		else if($print_button2[0]==156) $action_v3="accessories_details";
		else if($print_button2[0]==157) $action_v3="accessories_details2";
		else if($print_button2[0]==158) $action_v3="preCostRptWoven";
		else if($print_button2[0]==159) $action_v3="bomRptWoven";
		else if($print_button2[0]==170) $action_v3="preCostRpt3";
		else if($print_button2[0]==142) $action_v3="preCostRptBpkW";
		else if($print_button2[0]==192) $action_v3="checkListRpt";
		else if($print_button2[0]==197) $action_v3="bomRpt3";
		else if($print_button2[0]==211) $action_v3="mo_sheet";
		else if($print_button2[0]==221) $action_v3="fabric_cost_detail";
		else if($print_button2[0]==238) $action_v3="summary";
		else if($print_button2[0]==215) $action_v3="budget3_details";
		else if($print_button2[0]==270) $action_v3="preCostRpt6";
		else if($print_button2[0]==761) $action_v3="bom_pcs_woven";
		else if($print_button2[0]==770) $action_v3="bom_pcs_woven2";
		else if($print_button2[0]==445) $action_v3="preCostRpt8";
		else if($print_button2[0]==120) $action_v3="budgetsheet3";
		else  $action_v3="";
	}
	
	if($budget_type==3){
		$print_report_format_v3 = return_field_value("format_id", "lib_report_template", "template_name =" . $cbo_company . " and module_id=2 and report_id=161 and is_deleted=0 and status_active=1");
		$print_button_v3 = explode(",", $print_report_format_v3);
		
		if ($print_button_v3[0] == 50) $action_v3 = "preCostRpt";
		if ($print_button_v3[0] == 51) $action_v3 = "preCostRpt2";
		if ($print_button_v3[0] == 52) $action_v3 = "bomRpt";
		if ($print_button_v3[0] == 63) $action_v3 = "bomRpt2";
		if ($print_button_v3[0] == 156) $action_v3 = "accessories_details";
		if ($print_button_v3[0] == 157) $action_v3 = "accessories_details2";
		if ($print_button_v3[0] == 158) $action_v3 = "preCostRptWoven";
		if ($print_button_v3[0] == 159) $action_v3 = "bomRptWoven";
		if ($print_button_v3[0] == 170) $action_v3 = "preCostRpt3";
		if ($print_button_v3[0] == 171) $action_v3 = "preCostRpt4";
		if ($print_button_v3[0] == 142) $action_v3 = "preCostRptBpkW";
		if ($print_button_v3[0] == 192) $action_v3 = "checkListRpt";
		if ($print_button_v3[0] == 197) $action_v3 = "bomRpt3";
		if ($print_button_v3[0] == 211) $action_v3 = "mo_sheet";
		if ($print_button_v3[0] == 221) $action_v3 = "fabric_cost_detail";
		if ($print_button_v3[0] == 173) $action_v3 = "preCostRpt5";
		if ($print_button_v3[0] == 238) $action_v3 = "summary";
		if ($print_button_v3[0] == 215) $action_v3 = "budget3_details";
		if ($print_button_v3[0] == 270) $action_v3 = "preCostRpt6";
		if ($print_button_v3[0] == 581) $action_v3 = "costsheet";
		if ($print_button_v3[0] == 730) $action_v3 = "budgetsheet";
		if ($print_button_v3[0] == 759) $action_v3 = "materialSheet";
		if ($print_button_v3[0] == 351) $action_v3 = "bomRpt4";
		if ($print_button_v3[0] == 268) $action_v3 = "budget_4";
		if ($print_button_v3[0] == 381) $action_v3 = "mo_sheet_2";
		if ($print_button_v3[0] == 405) $action_v3 = "materialSheet2";
		if ($print_button_v3[0] == 765) $action_v3 = "bomRpt5";
		if ($print_button_v3[0] == 403) $action_v3 = "mo_sheet_3";
		if ($print_button_v3[0] == 769) $action_v3 = "preCostRpt7";
	}
	


	ob_start();
	?>
    <div>
    <table width="1200px" cellspacing="0">
    	<tr class="form_caption" style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:16px; font-weight:bold"><?=$companyArr[$cbo_company]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:12px; font-weight:bold"><?=show_company($cbo_company,'',''); ?></td>
        </tr>
        <tr class="form_caption" style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                <?="From ".change_date_format($date_from)." To ".change_date_format($date_to); ?>
            </td>
        </tr>
    </table>
    <?
		$sql="select a.id, a.job_no, a.style_ref_no, a.buyer_name, a.factory_marchant, b.costing_date, b.approved, b.approved_date, b.id as poid, c.po_total_price,c.po_quantity,b.costing_per,a.company_name,c.id as po_break_down_id,a.quotation_id,c.grouping from wo_po_details_master a, wo_pre_cost_mst b, wo_po_break_down c where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name='$cbo_company' $buyerCond $year_cond $jobCond $appstatusCond $dateCond order by a.id DESC";
		//echo $sql; die;
		$sqlRes=sql_select($sql); $jobidsArr=array();
		$dataArr=array(); $job_no_arrs=array();
		foreach($sqlRes as $row)
		{
			$jobidsArr[$row[csf('id')]]=$row[csf('id')];
			array_push($job_no_arrs, $row[csf('job_no')]);
		}
		//unset($sqlRes);
		
		$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where file_type=1 ".where_con_using_array($job_no_arrs,1,'master_tble_id')."",'master_tble_id','image_location');
		$total_cost_arr=return_library_array( "select job_no, total_cost from wo_pre_cost_dtls where status_active=1 ".where_con_using_array($job_no_arrs,1,'job_no')."",'job_no','total_cost');
		
		$condition= new condition();
		$jobids=implode(",",$jobidsArr);
		 
		if($jobids!='')
		{
			$condition->jobid_in("$jobids"); 
		}
		$condition->init();
		
		$fabric= new fabric($condition);
		//echo $fabric->getQuery(); die;
		$fabric_amount_arr=$fabric->getAmountArray_by_JobAndGmtscolor_knitAndwoven_greyAndfinish();
		// echo "<pre>";
		//print_r($fabric_amount_arr);die;
	   	$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
		$conversion= new conversion($condition);
		$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		$conv_amount_job_process=$conversion->getAmountArray_by_jobAndProcess();
		//print_r($conv_amount_job_process);
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_job();
		//print_r($trims_costing_arr);
		$emblishment= new emblishment($condition);
		$emb_amount_job_name_arr=$emblishment->getAmountArray_by_jobAndEmbname();
		//print_r($emb_amount_job_name_arr);
		$wash= new wash($condition);
		$emblishment_costing_arr_wash=$wash->getAmountArray_by_jobAndEmbname();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_job();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_job();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_job();
		
		$other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
		//$finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142','193','158');
		$finishing_arr = array(
			'2','3','4','25','26','32','33','34','36','37','38','39','40','60','61','62','63','64','65','66','67','68','69','70','71','72','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','100','101','120','121','122','123','124','125','127','128','129','130','131','132','133','134','135','136','137','138','139','140','141','142','143','144','145','146','147','148','149','150','151','152','153','154','155','156','157','158','159','160','161','162','163','164','165','166','167','168','169','170','171','172','173','174','175','176','177','178','179','180','181','182','183','184','185','186','187','188','189','190','191','192','193','194','195','196','197','198','199','200','201','202','203','204','205','206','207','208','209','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256','257','258','259','260','261','262','263','264','265','266','267','268','269','270','271','272','273','274','275','276','277','278','279','280','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299','300','303','304','305','306','307','308','309','310','311','312','313','314','315','316','317','318','319','320','321','322','323','324','325','326','327','328','329','330','331','332','333','334','335','336','337','338','339','340','341','342','343','344','345','346','347','348','349','350','351','352','353','354','355','356','357','358','359','360','361','362','363','364','365','366','367','368','369','370','371','372','373','374','375','376','377','378','379','380','381','382','383','384','385','386','387','388','389','390','391','392','393','394','395','396','397','398','399','400','401','402','403','404','405','406','407','408','409','410','411'		
		);
		$tmpjobArr=array();
		foreach($sqlRes as $row)
		{
			$dataArr[$row[csf('job_no')]]['str']=$row[csf('style_ref_no')].'__'.$row[csf('buyer_name')].'__'.$row[csf('factory_marchant')].'__'.$row[csf('costing_date')].'__'.$row[csf('approved')].'__'.$row[csf('approved_date')].'__'.$row[csf('company_name')].'__'.$row[csf('po_break_down_id')].'__'.$row[csf('costing_per')].'__'.$row[csf('quotation_id')].'__'.$row[csf('grouping')];
		
			$dataArr[$row[csf('job_no')]]['poVal']+=$row[csf('po_total_price')];
			$dataArr[$row[csf('job_no')]]['poQnty']+=$row[csf('po_quantity')];
			
			
			if (!in_array($row[csf("job_no")],$tmpjobArr) )
			{ 

				$costing_per=$row[csf('costing_per')];
				if($costing_per==1) { $order_price_per_dzn=12;  }
				else if($costing_per==2) { $order_price_per_dzn=1;  }
				else if($costing_per==3) { $order_price_per_dzn=24; }
				else if($costing_per==4) { $order_price_per_dzn=36; }
				else if($costing_per==5) { $order_price_per_dzn=48;}
				$dataArr[$row[csf('job_no')]]['costing_per']=$order_price_per_dzn;
				$dataArr[$row[csf('job_no')]]['total_cost']=$total_cost_arr[$row[csf('job_no')]];

				$yarn_amount_summ[$row[csf('job_no')]] = $yarn_costing_arr[$row[csf('job_no')]];
				foreach ($finishing_arr as $fid) {
					$total_finishing_amount[$row[csf('job_no')]] += array_sum($conv_amount_job_process[$row[csf('job_no')]][$fid]);
				}
				$purchase_amount =0;
				foreach($fabric_amount_arr['knit']['grey'][$row[csf('job_no')]] as $gmtscoloramt=>$coloramtdata)
				{
					foreach($coloramtdata as $sourcedata=>$uomdata)
					{
						$purchase_amount +=array_sum($uomdata);
					}
				}
				foreach($fabric_amount_arr['woven']['grey'][$row[csf('job_no')]] as $gmtscoloramt=>$coloramtdata)
				{
					foreach($coloramtdata as $sourcedata=>$uomdata)
					{
						$purchase_amount +=array_sum($uomdata);
					}
				}
				
				//$purchase_amount = array_sum($fabric_amount_arr['knit']['grey'][$row[csf('job_no')]])+array_sum($fabric_amount_arr['woven']['grey'][$row[csf('job_no')]]);
				$print_amount_summ[$row[csf('job_no')]] = $emb_amount_job_name_arr[$row[csf('job_no')]][1];
				$emb_amount_summ[$row[csf('job_no')]]= $emb_amount_job_name_arr[$row[csf('job_no')]][2];
				$wash_amount_summ[$row[csf('job_no')]] = $emblishment_costing_arr_wash[$row[csf('job_no')]][3];
				
				$ather_emb_attr = array(4,5,6,99);
				foreach ($ather_emb_attr as $att) {
					$others_emb_amount[$row[csf('job_no')]] += $emb_amount_job_name_arr[$row[csf('job_no')]][$att];
				}
				
				if(count($conv_amount_job_process[$row[csf('job_no')]][30])>0) {
					$yds_amount_summ[$row[csf('job_no')]] = array_sum($conv_amount_job_process[$row[csf('job_no')]][30]);
				}
				if(count($conv_amount_job_process[$row[csf('job_no')]][35])>0) {
					$aop_amount_summ[$row[csf('job_no')]] = array_sum($conv_amount_job_process[$row[csf('job_no')]][35]);
				}
				if(count($conv_amount_job_process[$row[csf('job_no')]][1])>0) {
					$knitting_amount_summ[$row[csf('job_no')]] = fn_number_format(array_sum($conv_amount_job_process[$row[csf('job_no')]][1]),2);
				}
				if(count($conv_amount_job_process[$row[csf('job_no')]][31])>0) {
					$dyeing_amount_summ[$row[csf('job_no')]]=  array_sum($conv_amount_job_process[$row[csf('job_no')]][31]);
				}
				
				foreach ($other_cost_attr as $attr) {
					$total_other_cost[$row[csf('job_no')]]+=$other_costing_arr[$row[csf('job_no')]][$attr];
				}
				//$misc_cost=$other_costing_arr[$row[csf('job_no')]]['lab_test']+$commercial_costing_arr[$row[csf('job_no')]]+$commission_costing_arr[$row[csf('job_no')]]+$total_other_cost;
				$misc_cost[$row[csf('job_no')]]=$other_costing_arr[$row[csf('job_no')]]['lab_test']+$other_costing_arr[$row[csf('job_no')]]['currier_pre_cost']+$commercial_costing_arr[$row[csf('job_no')]]+$commission_costing_arr[$row[csf('job_no')]]+$total_other_cost[$row[csf('job_no')]];

				//echo "10**".$yarn_amount_summ[$row[csf('job_no')]].'-2-'.$total_finishing_amount[$row[csf('job_no')]].'-3-'.$print_amount_summ[$row[csf('job_no')]].'-4-'.$trims_costing_arr[$row[csf('job_no')]].'-5-'.$yds_amount_summ[$row[csf('job_no')]].'-6-'.$aop_amount_summ[$row[csf('job_no')]].'-7-'.$emb_amount_summ[$row[csf('job_no')]].'-8-'.$knitting_amount_summ[$row[csf('job_no')]].'-9-'.$purchase_amount.'-10-'.$wash_amount_summ[$row[csf('job_no')]].'-11-'.$other_costing_arr[$row[csf('job_no')]]['cm_cost'].'-12-'.$dyeing_amount_summ[$row[csf('job_no')]].'-13-'.$others_emb_amount[$row[csf('job_no')]].'-14-'.$misc_cost[$row[csf('job_no')]]; die;
				
				$total_budget_value[$row[csf('job_no')]] = $yarn_amount_summ[$row[csf('job_no')]]+$total_finishing_amount[$row[csf('job_no')]]+$print_amount_summ[$row[csf('job_no')]]+$trims_costing_arr[$row[csf('job_no')]]+$yds_amount_summ[$row[csf('job_no')]]+$aop_amount_summ[$row[csf('job_no')]]+$emb_amount_summ[$row[csf('job_no')]]+$knitting_amount_summ[$row[csf('job_no')]]+$purchase_amount+$wash_amount_summ[$row[csf('job_no')]]+$other_costing_arr[$row[csf('job_no')]]['cm_cost']+$dyeing_amount_summ[$row[csf('job_no')]]+$others_emb_amount[$row[csf('job_no')]]+$misc_cost[$row[csf('job_no')]];
				
				$dataArr[$row[csf('job_no')]]['bomamt']+=$total_budget_value[$row[csf('job_no')]];
				$tmpjobArr[]=$row[csf('job_no')]; 
			}
		}
		/* echo '<pre>';
		print_r($total_budget_value); die; */
		?>
		<table width="1400px" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="30">SL.</th>
                    <th width="50">IMG</th>
                    <th width="70">Costing /Budget Date</th>     
                    <th width="100">Job No</th>
					<th width="100">Internal Booking No</th>
                    <th width="120">Style Ref.</th>
                    <th width="100">Buyer</th>
                    <th width="100">Order Qty Pcs</th>
                    <th width="90">Order Value $</th>
                    
                    <th width="100">BOM Amt $</th>
                    <th width="70">BOM Amt %</th>
                    <th width="80">Margin Amt $</th>     
                    <th width="70">Margin %</th>
                    <th width="90">Approval Status</th>
                    <th width="80">App. Date & Time</th>
                    <th>Factory Merchandiser</th>
                 </tr>
            </thead>
        </table>
        <div style="width:1420px; max-height:400px; overflow-y:scroll" id="scroll_body"> 
        	<table width="1400px" border="1" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<? $i=1;
			foreach($dataArr as $jobNo=>$jobData)
			{
				$jobDataStr=explode("__",$jobData['str']);
				$style_ref=$buyer_name=$factory_marchant=$costing_date=$approved=$approved_date="";
				$style_ref=$jobDataStr[0];
				$buyer_name=$jobDataStr[1];
				$factory_marchant=$jobDataStr[2];
				$costing_date=$jobDataStr[3];
				$approved=$jobDataStr[4];
				$approved_date=explode(" ",$jobDataStr[5]);
				$company_name=$jobDataStr[6];
				$po_break_down_id=$jobDataStr[7];	
				$costing_per=$jobDataStr[8];
				$quotation_id=$jobDataStr[9];
				$grouping=$jobDataStr[10];

				
				$orderVal=$bomAmt=$bomAmtPer=$marginAmt=$marginPer=0;
				
				$orderVal=$jobData['poVal'];
				//$bomAmt=($jobData['total_cost']/$jobData['costing_per'])*$jobData['poQnty'];
				$bomAmt=$jobData['bomamt'];
				$bomAmtPer=($bomAmt/$orderVal)*100;
				$marginAmt=($orderVal-$bomAmt)*1;
				$marginPer=($marginAmt/$orderVal)*100;
				$marginPer=number_format($marginPer,2,".","");
				$appStr=""; $appTdColor=""; $appDate="";

				//$action_type="budgetsheet";  
				$variable="'".$company_name.'_'.$buyer_name.'_'.$style_ref.'_'.$jobNo.'_'.$costing_date.'_'.$quotation_id.'_'.$po_break_down_id.'_'.$costing_per."'";


				if($approved==1 || $approved==3) 
				{
					$appStr="APPROVED";
					$appTdColor="color:#009F00";
					$appDate=change_date_format($approved_date[0], "d-M-y", "-", 1).'<br>'.$approved_date[1].' '.$approved_date[2];
				}
				else 
				{
					$appStr="UNAPPROVED";
					$appTdColor="color:#F05";
					$appDate="";
				}
				if($marginPer<=5) $tdMargincolor='style="color:#F05"'; else $tdMargincolor="";
				$order_qty_pcs=$dataArr[$jobNo]['poQnty'];
				
				if($marginupto==1 && $marginvalue!=0)//Greater Than
				{
					if($marginPer>$marginvalue)
					{
						if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
						?>
						 <tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolors; ?>');" id="tr_<?=$i; ?>" style="font-size:13px">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="50" onClick="openmypage_image('requires/bom_list_report_controller.php?action=show_image&job_no=<?=$jobNo; ?>','Image View')"><img src='../../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td> 
							
							<td width="70" style="word-break:break-all"><p><?=change_date_format($costing_date, "d-M-y", "-", 1); ?></p></td>     
							<td width="100" style="word-break:break-all">
							<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $jobNo; ?>','<? echo $style_ref; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_break_down_id; ?>','<? echo $action_v3; ?>');">
											<? echo $jobNo; ?>
										</a></td>
							<td width="100" style="word-break:break-all" align="right"><p><?=$grouping; ?></p></td>
							<td width="120" style="word-break:break-all" align="center"><p><?=$style_ref; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><p><?=$buyerArr[$buyer_name]; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><p><?=$order_qty_pcs; ?></p></td>
							<td width="90" align="right"><p><?=number_format($orderVal,2); ?></p></td>
							
							<td width="100" align="right"><p><?=number_format($bomAmt,2); ?></p></td>
							<td width="70" align="right"><p><?=number_format($bomAmtPer,2); ?></p></td>
							<td width="80" align="right"><p><?=number_format($marginAmt,2); ?></p></td>     
							<td width="70" align="right" <?=$tdMargincolor; ?>><p><?=$marginPer; ?></p></td>
							<td width="90" style="word-break:break-all; <?=$appTdColor; ?>"><p><?=$appStr; ?></p></td>
							<td width="70" style="word-break:break-all" align="right"><p><?=$appDate; ?></p></td>
							<td style="word-break:break-all"><p><?=$factoryMarArr[$factory_marchant]; ?></p></td>
						</tr>
						<?
						$i++;
						$gOrderVal+=$orderVal;
						$gBomAmt+=$bomAmt;
						$gMarginAmt+=$marginAmt;
					}
				}
				else if($marginupto==2 && $marginvalue!=0)//Less Than
				{
					if($marginPer<$marginvalue)
					{
						if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
						?>
						 <tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolors; ?>');" id="tr_<?=$i; ?>" style="font-size:13px">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="50" onClick="openmypage_image('requires/bom_list_report_controller.php?action=show_image&job_no=<?=$jobNo; ?>','Image View')"><img src='../../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td> 
							
							<td width="70" style="word-break:break-all"><?=change_date_format($costing_date, "d-M-y", "-", 1); ?></td>     
							<td width="100" style="word-break:break-all">
							<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $jobNo; ?>','<? echo $style_ref; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_break_down_id; ?>','<? echo $action_v3; ?>');">
											<? echo $jobNo; ?>
										</a></td>
							<td width="100" style="word-break:break-all" align="right"><p><?=$grouping; ?></p></td>
							<td width="120" style="word-break:break-all" align="center"><p><?=$style_ref; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><?=$buyerArr[$buyer_name]; ?></td>
							<td width="100" style="word-break:break-all" align="center"><p><?=$order_qty_pcs; ?></p></td>
							<td width="90" align="right"><?=number_format($orderVal,2); ?></td>
							
							<td width="100" align="right"><?=number_format($bomAmt,2); ?></td>
							<td width="70" align="right"><?=number_format($bomAmtPer,2); ?></td>
							<td width="80" align="right"><?=number_format($marginAmt,2); ?></td>     
							<td width="70" align="right" <?=$tdMargincolor; ?>><?=$marginPer; ?></td>
							<td width="90" style="word-break:break-all; <?=$appTdColor; ?>"><?=$appStr; ?></td>
							<td width="80" style="word-break:break-all" align="right"><p><?=$appDate; ?></p></td>
							<td style="word-break:break-all"><?=$factoryMarArr[$factory_marchant]; ?></td>
						</tr>
						<?
						$i++;
						$gOrderVal+=$orderVal;
						$gBomAmt+=$bomAmt;
						$gMarginAmt+=$marginAmt;
					}
				}
				else if($marginupto==3 && $marginvalue!=0)//Greater Equal
				{
					if($marginPer>=$marginvalue)
					{
						if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
						?>
						 <tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolors; ?>');" id="tr_<?=$i; ?>" style="font-size:13px">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="50" onClick="openmypage_image('requires/bom_list_report_controller.php?action=show_image&job_no=<?=$jobNo; ?>','Image View')"><img src='../../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td> 
							
							<td width="70" style="word-break:break-all"><?=change_date_format($costing_date, "d-M-y", "-", 1); ?></td>     
							<td width="100" style="word-break:break-all">
							<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $jobNo; ?>','<? echo $style_ref; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_break_down_id; ?>','<? echo $action_v3; ?>');">
											<? echo $jobNo; ?>
										</a></td>
							<td width="100" style="word-break:break-all" align="right"><p><?=$grouping; ?></p></td>
							<td width="120" style="word-break:break-all" align="center"><p><?=$style_ref; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><?=$buyerArr[$buyer_name]; ?></td>
							<td width="100" style="word-break:break-all" align="center"><p><?=$order_qty_pcs; ?></p></td>
							<td width="90" align="right"><?=number_format($orderVal,2); ?></td>
							
							<td width="100" align="right"><?=number_format($bomAmt,2); ?></td>
							<td width="70" align="right"><?=number_format($bomAmtPer,2); ?></td>
							<td width="80" align="right"><?=number_format($marginAmt,2); ?></td>     
							<td width="70" align="right" <?=$tdMargincolor; ?>><?=$marginPer; ?></td>
							<td width="90" style="word-break:break-all; <?=$appTdColor; ?>"><?=$appStr; ?></td>
							<td width="80" style="word-break:break-all" align="right"><p><?=$appDate; ?></p></td>
							<td style="word-break:break-all"><?=$factoryMarArr[$factory_marchant]; ?></td>
						</tr>
						<?
						$i++;
						$gOrderVal+=$orderVal;
						$gBomAmt+=$bomAmt;
						$gMarginAmt+=$marginAmt;
					}
				}
				else if($marginupto==4 && $marginvalue!=0)//Less Equal
				{
					if($marginPer<=$marginvalue)
					{
						if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
						?>
						 <tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolors; ?>');" id="tr_<?=$i; ?>" style="font-size:13px">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="50" onClick="openmypage_image('requires/bom_list_report_controller.php?action=show_image&job_no=<?=$jobNo; ?>','Image View')"><img src='../../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td> 
							
							<td width="70" style="word-break:break-all"><?=change_date_format($costing_date, "d-M-y", "-", 1); ?></td>     
							<td width="100" style="word-break:break-all">
							<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $jobNo; ?>','<? echo $style_ref; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_break_down_id; ?>','<? echo $action_v3; ?>');">
											<? echo $jobNo; ?>
										</a></td>
							<td width="100" style="word-break:break-all" align="right"><p><?=$grouping; ?></p></td>
							<td width="120" style="word-break:break-all" align="center"><p><?=$style_ref; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><?=$buyerArr[$buyer_name]; ?></td>
							<td width="100" style="word-break:break-all" align="center"><p><?=$order_qty_pcs; ?></p></td>
							<td width="90" align="right"><?=number_format($orderVal,2); ?></td>
							
							<td width="100" align="right"><?=number_format($bomAmt,2); ?></td>
							<td width="70" align="right"><?=number_format($bomAmtPer,2); ?></td>
							<td width="80" align="right"><?=number_format($marginAmt,2); ?></td>     
							<td width="70" align="right" <?=$tdMargincolor; ?>><?=$marginPer; ?></td>
							<td width="90" style="word-break:break-all; <?=$appTdColor; ?>"><?=$appStr; ?></td>
							<td width="80" style="word-break:break-all" align="right"><p><?=$appDate; ?></p></td>
							<td style="word-break:break-all"><?=$factoryMarArr[$factory_marchant]; ?></td>
						</tr>
						<?
						$i++;
						$gOrderVal+=$orderVal;
						$gBomAmt+=$bomAmt;
						$gMarginAmt+=$marginAmt;
					}
				}
				else if($marginupto==5 && $marginvalue!=0)//Equal
				{
					if($marginPer==$marginvalue)
					{
						if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
						?>
						 <tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolors; ?>');" id="tr_<?=$i; ?>" style="font-size:13px">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="50" onClick="openmypage_image('requires/bom_list_report_controller.php?action=show_image&job_no=<?=$jobNo; ?>','Image View')"><img src='../../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td> 
							
							<td width="70" style="word-break:break-all"><?=change_date_format($costing_date, "d-M-y", "-", 1); ?></td>     
							<td width="100" style="word-break:break-all">
							<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $jobNo; ?>','<? echo $style_ref; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_break_down_id; ?>','<? echo $action_v3; ?>');">
											<? echo $jobNo; ?>
										</a></td>
							<td width="100" style="word-break:break-all" align="right"><p><?=$grouping; ?></p></td>
							<td width="120" style="word-break:break-all" align="center"><p><?=$style_ref; ?></p></td>
							<td width="100" style="word-break:break-all" align="center"><?=$buyerArr[$buyer_name]; ?></td>
							<td width="100" style="word-break:break-all" align="center"><p><?=$order_qty_pcs; ?></p></td>
							<td width="90" align="right"><?=number_format($orderVal,2); ?></td>
							
							<td width="100" align="right"><?=number_format($bomAmt,2); ?></td>
							<td width="70" align="right"><?=number_format($bomAmtPer,2); ?></td>
							<td width="80" align="right"><?=number_format($marginAmt,2); ?></td>     
							<td width="70" align="right" <?=$tdMargincolor; ?>><?=$marginPer; ?></td>
							<td width="90" style="word-break:break-all; <?=$appTdColor; ?>"><?=$appStr; ?></td>
							<td width="80" style="word-break:break-all" align="right"><p><?=$appDate; ?></p></td>
							<td style="word-break:break-all"><?=$factoryMarArr[$factory_marchant]; ?></td>
						</tr>
						<?
						$i++;
						$gOrderVal+=$orderVal;
						$gBomAmt+=$bomAmt;
						$gMarginAmt+=$marginAmt;
					}
				}
				else// All
				{
					if($i%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
					?>
					 <tr bgcolor="<?=$bgcolors; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolors; ?>');" id="tr_<?=$i; ?>" style="font-size:13px">
						<td width="30" align="center"><?=$i; ?></td>
						<td width="50" onClick="openmypage_image('requires/bom_list_report_controller.php?action=show_image&job_no=<?=$jobNo; ?>','Image View')"><img src='../../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td> 
						
						<td width="70" style="word-break:break-all"><?=change_date_format($costing_date, "d-M-y", "-", 1); ?></td>     
						<td width="100" style="word-break:break-all">
							<a href='#report_details' onclick="generate_report_v3('<? echo $company_name; ?>','<? echo $jobNo; ?>','<? echo $style_ref; ?>','<? echo $buyer_name; ?>','<? echo $costing_date; ?>','<? echo $po_break_down_id; ?>','<? echo $action_v3; ?>');">
											<? echo $jobNo; ?>
										</a></td>
						<td width="100" style="word-break:break-all" align="right"><p><?=$grouping; ?></p></td>
						<td width="120" style="word-break:break-all" align="center"><p><?=$style_ref; ?></p></td>
						<td width="100" style="word-break:break-all" align="center"><?=$buyerArr[$buyer_name]; ?></td>
						<td width="100" style="word-break:break-all" align="center"><p><?=$order_qty_pcs; ?></p></td>
						<td width="90" align="right"><?=number_format($orderVal,2); ?></td>
						
						<td width="100" align="right"><?=number_format($bomAmt,2); ?></td>
						<td width="70" align="right"><?=number_format($bomAmtPer,2); ?></td>
						<td width="80" align="right"><?=number_format($marginAmt,2); ?></td>     
						<td width="70" align="right" <?=$tdMargincolor; ?>><?=$marginPer; ?></td>
						<td width="90" style="word-break:break-all; <?=$appTdColor; ?>"><?=$appStr; ?></td>
						<td width="80" style="word-break:break-all" align="right"><p><?=$appDate; ?></p></td>
						<td style="word-break:break-all"><?=$factoryMarArr[$factory_marchant]; ?></td>
					</tr>
					<?
					$i++;
					$gOrderVal+=$orderVal;
					$gBomAmt+=$bomAmt;
					$gMarginAmt+=$marginAmt;
				}
			}
			$totRow=$i-1;
		?>
        </table>
    </div>
    <table width="1400px" cellspacing="0" border="1" class="tbl_bottom" rules="all">
        <tr style="font-size:13px">
            <td width="30">&nbsp;</td> 
            <td width="50">&nbsp;</td>
            <td width="70">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="120">&nbsp;</td> 
            <td width="100">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="90" align="right" id="value_tdpo"><?=number_format($gOrderVal,2); ?></td>
            <td width="100" align="right" id="value_tdbom"><?=number_format($gBomAmt,2); ?></td>
            
            <td width="70">&nbsp;</td>
            <td width="80" align="right" id="value_tdmargin"><?=number_format($gMarginAmt,2); ?></td>
            <td width="70">&nbsp;</td>
            <td width="90">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td>&nbsp;</td>
         </tr>
    </table>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$totRow";
	exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Image PopUp","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$jobNos="'".implode(",",explode(',',$job_no))."'";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id in ($jobNos) and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			if($row[csf('image_location')]!="")
			{
				?>
				<td><img src='../../../<?=$row[csf('image_location')]; ?>' height='250' width='300' /></td>
				<?
			}
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}
if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=286 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
}
if ($action=="report_generate2")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_id=str_replace("'","",$hidd_job_id);
	$appstatus=str_replace("'","",$cbo_appstatus);
	$marginupto=str_replace("'","",$cbo_marginupto);
	$marginvalue=str_replace("'","",$txt_marginvalue)*1;
	$datetype=str_replace("'","",$cbo_datetype);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$garments_nature=str_replace("'","",$garments_nature);
	$budget_type=str_replace("'","",$budget_type);
	$txt_internal_file=str_replace("'","",$txt_internal_file);

	if($garments_nature==''){
		$garments_nature=2;
	}
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyerCond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyerCond="";
		}
		else $buyerCond="";
	}
	else $buyerCond=" and a.buyer_name=$cbo_buyer";
	
	if($db_type==0) 
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	$jobCond="";
	if ($job_id!="") 
	{
		$jobCond=" and a.id in ($job_id)";
	}
	else if($job_no!="")
	{
		$jobCond=" and a.job_no_prefix_num in ($job_no)";
	}
	
	if ($appstatus==1) $appstatusCond=" and b.approved in (1,3)"; else if ($appstatus==2) $appstatusCond=" and b.approved in (0,2)"; else $appstatusCond="";
	
	if ($main_booking=="") $main_booking_cond=""; else $main_booking_cond=" and b.booking_no_prefix_num='$main_booking'";
	if ($short_booking=="") $short_booking_cond=""; else $short_booking_cond=" and b.booking_no_prefix_num='$short_booking'";
	if ($cause_type==0) $cause_type_cond=""; else $cause_type_cond=" and d.cause_id='$cause_type'";
	
	if($db_type==0)
	{
		if($datetype==1)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and b.costing_date between '".$date_from."' and '".$date_to."'";
		}
		else if($datetype==2)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and c.shipment_date between '".$date_from."' and '".$date_to."'";
		}
	}
	else if($db_type==2)
	{
		if($datetype==1)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and b.costing_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
		else if($datetype==2)
		{
			if( $date_from=="" && $date_to=="" ) $dateCond=""; else $dateCond= " and c.shipment_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		}
	}
	$filecond='';
	if($txt_internal_file!=''){
		$filecond="and c.file_no like '%$txt_internal_file'";
	}
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$factoryMarArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");

	ob_start();
	?>
    <div>
    <table width="1200px" cellspacing="0">
    	<tr class="form_caption" style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:16px; font-weight:bold"><?=$companyArr[$cbo_company]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:12px; font-weight:bold"><?=show_company($cbo_company,'',''); ?></td>
        </tr>
        <tr class="form_caption" style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
        </tr>
    </table>
    <?
		$sql="select a.id, a.job_quantity, a.job_no, a.style_ref_no, a.buyer_name, a.factory_marchant, a.order_uom, a.avg_unit_price, b.costing_date, b.approved, b.approved_date, b.id as poid, c.sc_lc, c.grouping, c.po_total_price,c.po_quantity,b.costing_per,a.company_name,c.id as po_break_down_id,a.quotation_id,c.grouping, d.fabric_cost, d.trims_cost, d.embel_cost, d.wash_cost, d.cm_cost, d.comm_cost, d.lab_test, d.inspection, d.commission, d.studio_cost, d.freight, d.currier_pre_cost, d.certificate_pre_cost, d.deffdlc_cost, d.design_cost, d.common_oh, d.interest_cost, d.incometax_cost, d.depr_amor_pre_cost  from wo_po_details_master a, wo_pre_cost_mst b, wo_po_break_down c, wo_pre_cost_dtls d where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and a.id=d.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name='$cbo_company' $buyerCond $year_cond $jobCond $appstatusCond $dateCond $filecond order by a.id DESC";

		$others_cost_attribute=array('freight', 'currier_pre_cost', 'certificate_pre_cost', 'deffdlc_cost', 'design_cost', 'common_oh', 'interest_cost', 'incometax_cost', 'depr_amor_pre_cost');
		//echo $sql; die;
		$sqlRes=sql_select($sql); $jobidsArr=array();
		$dataArr=array();

		foreach($sqlRes as $row){
			$buyer_arr[$row[csf('buyer_name')]]=$buyerArr[$row[csf('buyer_name')]];
			$sclc_arr[$row[csf('sc_lc')]]=$row[csf('sc_lc')];
			$grouping_arr[$row[csf('grouping')]]=$row[csf('grouping')];

			$bom_rate=$row[csf('fabric_cost')]+$row[csf('trims_cost')]+$row[csf('embel_cost')]+$row[csf('wash_cost')];

			$others_pcs=$row[csf('freight')]+$row[csf('currier_pre_cost')]+$row[csf('certificate_pre_cost')]+$row[csf('deffdlc_cost')]+$row[csf('design_cost')]+$row[csf('common_oh')]+$row[csf('interest_cost')]+$row[csf('incometax_cost')]+$row[csf('depr_amor_pre_cost')];
			
			$total_cost=$bom_rate+$others_pcs+$row[csf('cm_cost')]+$row[csf('comm_cost')]+$row[csf('lab_test')]+$row[csf('inspection')]+$row[csf('commission')]+$row[csf('studio_cost')];
			

			$dataArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$dataArr[$row[csf('id')]]['job_quantity']=$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$dataArr[$row[csf('id')]]['avg_unit_price']=$row[csf('avg_unit_price')];
			$dataArr[$row[csf('id')]]['po_total_price']+=$row[csf('po_total_price')];
			$dataArr[$row[csf('id')]]['bom_rate']=$bom_rate;
			$dataArr[$row[csf('id')]]['bom_amt']=fn_number_format($bom_rate,4)*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['cm_cost']=fn_number_format($row[csf('cm_cost')],4)*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['cm_pcs']=$row[csf('cm_cost')];
			$dataArr[$row[csf('id')]]['comm_cost']=(fn_number_format($row[csf('comm_cost')],4)+fn_number_format($row[csf('studio_cost')],4))*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['comm_cost_pcs']=$row[csf('comm_cost')]+$row[csf('studio_cost')];
			$dataArr[$row[csf('id')]]['lab_test']=fn_number_format($row[csf('lab_test')],4)*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['lab_test_pcs']=$row[csf('lab_test')];
			$dataArr[$row[csf('id')]]['inspection_cost']=fn_number_format($row[csf('inspection')],4)*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['inspection_pcs']=$row[csf('inspection')];
			$dataArr[$row[csf('id')]]['commission_cost']=fn_number_format($row[csf('commission')],4)*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['commission']=$row[csf('commission')];
			$dataArr[$row[csf('id')]]['others_cost']=fn_number_format($others_pcs,4)*$row[csf('job_quantity')];
			$dataArr[$row[csf('id')]]['others_pcs']=$others_pcs;
			$dataArr[$row[csf('id')]]['total_cost']=$total_cost;
			$dataArr[$row[csf('id')]]['total_cost_amt']=fn_number_format($total_cost,4)*$row[csf('job_quantity')];
		}
		$others_cost_title=array('Freight', 'Courier', 'Certificate', 'Deffd. LC', 'Design', 'Opert. Exp.', 'Interest', 'Income Tax', 'DD&A');
		?>
		<table width="1650px" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
				<tr>
					<td colspan="24"><b>Buyer:</b><?= implode(",",$buyer_arr) ?>,<b>SC/LC No:</b><?= implode(",",$sclc_arr) ?>,<b>File No:</b> <?= implode(",",$grouping_arr) ?></td>
				</tr>
                <tr>
                    <th width="30">SL.</th>
                    <th width="80">Style Ref.</th>
                    <th width="70" title="Total Po Qty">Order Qty.</th>
                    <th width="70" title="Order Unit">Order Uom.</th>
                    <th width="70" title="Total Po Avg Rate.">FOB (Pcs/Set)</th>
                    <th width="70" title="Total Po Qty* Total Po Avg Rate">Order Value</th>
                    <th width="70" title="BOM Rate*Total Po Qty">BOM Amt.</th>
                    <th width="70" title="PO total (trims + fabric + wash + embellishment) cost">BOM Rate (Pcs/Set)</th>
                    <th width="70" title="Total PO Qty * CM">CM</th>
                    <th width="70" title="CM">CM (Pcs/Set)</th>
                    <th width="70" title="Total Po QTy * Commercial Cost">Com. Cost</th>
                    <th width="70" title="Commercial Cost">Com. Cost (Pcs/Set)</th>
                    <th width="70" title="Total Po qty*  Lab Test Cost">Test Cost</th>
                    <th width="70" title="Lab Test Cost">Test Cost  (Pcs/Set)</th>
                    <th width="70" title="Total Po Qty * Inspection Cost">Inspection Cost</th>
                    <th width="70" title="Inspection Cost">Inspection Cost (Pcs/Set)</th>
                    <th width="70" title="Total Po Qty* BHC">BHC</th>
                    <th width="70" title="Buying House Charge">BHC (Pcs/Set)</th>
                    <th width="70" title="Total Po Qty * sum of (<?= implode(",", $others_cost_title) ?>) cost">Other's Cost</th>
                    <th width="70" title="Sum Of (<?= implode(",", $others_cost_title) ?>) Cost">Other's Cost (Pcs/Set)</th>
                    <th width="70" title="Total Po Qty * Ttl Cost">Ttl. Cost</th>
                    <th width="70" title="All Cost">Ttl. Cost (Pcs/Set)</th>
                    <th width="70" title="Mergin Cost * Total Po Qty">Margin/Balance</th>
                    <th width="70" title="Total PO AVG FOB -Ttl Cost">Margin (Pcs/Set)</th>
                </tr>				
            </thead>
			<?
			$i=1;
			foreach($dataArr as $value){ 
				$margin_pcs=$value['avg_unit_price']-$value['total_cost'];					
				?>
				<tr>
					<td><?= $i; ?></td>
					<td><?= $value['style_ref_no'] ?></td>
					<td align="right"><?= $value['job_quantity'] ?></td>
					<td align="center"><?= $unit_of_measurement[$value['order_uom']] ?></td>
					<td align="right"><?= $value['avg_unit_price'] ?></td>
					<td align="right"><?= $value['po_total_price'] ?></td>
					<td align="right"><?= fn_number_format($value['bom_amt'],4) ?></td>
					<td align="right"><?= fn_number_format($value['bom_rate'],4) ?></td>
					<td align="right"><?= fn_number_format($value['cm_cost'],4) ?></td>
					<td align="right"><?= fn_number_format($value['cm_pcs'],4) ?></td>
					<td align="right"><?= fn_number_format($value['comm_cost'],4) ?></td>
					<td align="right"><?= fn_number_format($value['comm_cost_pcs'],4) ?></td>
					<td align="right"><?= fn_number_format($value['lab_test'],4) ?></td>
					<td align="right"><?= fn_number_format($value['lab_test_pcs'],4) ?></td>
					<td align="right"><?= fn_number_format($value['inspection_cost'],4) ?></td>
					<td align="right"><?= fn_number_format($value['inspection_pcs'],4) ?></td>
					<td align="right"><?= fn_number_format($value['commission_cost'],4) ?></td>
					<td align="right"><?= fn_number_format($value['commission'],4) ?></td>
					<td align="right"><?= fn_number_format($value['others_cost'],4) ?></td>
					<td align="right"><?= fn_number_format($value['others_pcs'],4) ?></td>
					<td align="right"><?= fn_number_format($value['total_cost_amt'],4) ?></td>
					<td align="right"><?= fn_number_format($value['total_cost'],4) ?></td>
					<td align="right"><?= fn_number_format($margin_pcs*$value['job_quantity'],4) ?></td>
					<td align="right"><?= fn_number_format($margin_pcs,4) ?></td>
				</tr>
				<? 
				$i++;
			}
			?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
       	</table>
 
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$totRow";
	exit();
}
if($action=="internal_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_name,$buyer_name)=explode('_',$data);
	$buyer_disabled=0;
	if($buyer_name!=0){
		$buyer_disabled=1;
	}
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#hide_buyer_id").val(splitData[0]);
			$("#hide_file_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter File No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                    </th>
                    <input type="hidden" name="hide_buyer_id" id="hide_buyer_id" value=""/>
                    <input type="hidden" name="hide_file_no" id="hide_file_no" value=""/>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",$buyer_disabled );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"SC No",2=>"File No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--",2,$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'job_no_popup_list', 'search_div', 'bom_list_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="job_no_popup_list")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($companyID,$buyer_name,$search_by,$search_common)=explode('**',$data);
 
 	if ($buyer_name!=0) $where_con.=" and b.buyer_name=$buyer_name";
	
	if($search_by==1 && $search_common!='')
	{
		$where_con.=" and a.sc_lc like('%".$search_common."%')";
	}
	else if($search_by==2 && $search_common!='')
	{
		$where_con.=" and a.file_no like('%".$search_common."%')";
	}
	

	$sql="select a.sc_lc, a.file_no,  b.buyer_name from wo_po_details_master b, wo_po_break_down a, wo_pre_cost_mst c where b.id=a.job_id and b.id=c.job_id and c.status_active=1 and c.is_deleted=0 and b.company_name=$companyID and b.is_deleted=0 $where_con  group by a.sc_lc, a.file_no,  b.buyer_name";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(0=>$buyer);

	echo  create_list_view("list_view", "Buyer,SC/LC,File No", "100,150,150","450","450",0, $sql, "js_set_value", "buyer_name,file_no", "", 1, "buyer_name,0,0", $arr , "buyer_name,sc_lc,file_no", "budget_breakdown_report_controller",'','0,0,0','') ;
	disconnect($con);
	exit();
}
?>
