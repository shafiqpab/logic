<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$taskArr=array(
	1=> "Yarn In Hand Date",
	2=> "Accessories In Hand Date",
	3=> "Knitting Complete Date",
	4=> "Linking Complete Date",
	5=> "Embelishment",
	6=> "Washing Completion Date",
	7=> "Sample Completion Date",
	8=> "Sample Delivery Date",
	11=> "Time",
	12=> "Sample Weight [Lbs]",
	21=> "Complete Date",

);
$taskArr2=array(
	1=> "Yarn In Hand Date",
	2=> "Accessories In Hand Date",
	3=> "Knitting Complete Date",
	4=> "Linking To Mending Complete",
	5=> "Embelishment",
	6=> "Special Washing Completion",
	7=> "Washing to finishing Completion",
	8=> "Sample QC",
	9=> "Sample Delivery Date",
);
	$taskArr3=array(
		1=> "Yarn In Hand Date",
		2=> "Knitting Complete Date",
		3=> "Linking Complete Date",
		4=> "Accessories In Hand Date",
		5=> "Embelishment",
		6=> "Washing Completion Date",
		7=> "Sample Completion Date",
		8=> "Sample Delivery Date",
	);






if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/sweater_sample_programmer_followup_report_controller',this.value, 'load_drop_down_buyer_season', 'td_season' );load_drop_down( 'requires/sweater_sample_programmer_followup_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/sweater_sample_programmer_followup_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 80, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}



if($action=="file_dtls")
{
	$sql="select file_type, form_name, master_tble_id, IMAGE_LOCATION from common_photo_library where form_name='sweater_sample_requisition_1' and file_type=2 and master_tble_id='$sys_id'";
	$dataArray=sql_select( $sql );
	foreach ($dataArray as $row){
		?>
        <a href="../../../../includes/common_functions_for_js.php?filename=../<? echo trim($row[IMAGE_LOCATION]);?>&action=download_file"> <img src="../../../../file_upload/blank_file.png" height="97px" width="89px" /></a>
        <?
	}
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	function add_date_without_offday($date,$addDay){
		
		for($i=1;$i<=$addDay;$i++){
			if(date('l', strtotime($date. " + $i day"))=='Friday'){
				$addDay+=1;
			}
		}
		
		return date('d-m-Y', strtotime($date. " + $addDay day"));
	}
	
	
	$company_arr=return_library_array( "select id, company_name from lib_company where id=$cbo_company_name",'id','company_name');	
	$dealing_mar_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name", "id", "team_member_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$team_name_arr=return_library_array( "select id, team_name from lib_sample_production_team", "id", "team_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');

	$txt_sample_name=str_replace("'","",$txt_sample_name);
	if($txt_sample_name!=""){$sample_name_cond =" and a.sample_name like('%$txt_sample_name%')";}
	
	
	$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample  and b.sequ is not null and a.status_active=1 and a.is_deleted=0 $sample_name_cond  group by  a.id,a.sample_name,b.sequ order by b.sequ";
	$sample_name_arr=return_library_array($sql,'id','sample_name');
	
	
	$txt_garments_item=trim(str_replace("'","",$txt_garments_item));
	if($txt_garments_item!=""){
		$garments_item_cond =" and ITEM_NAME like('%$txt_garments_item%')";
		$sql="select id,ITEM_NAME from LIB_GARMENT_ITEM where 1=1 $garments_item_cond";
		$garments_item_arr=return_library_array($sql,'id','ITEM_NAME');
	}	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_sample_team=str_replace("'","",$cbo_sample_team);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$txt_garments_item=str_replace("'","",$txt_garments_item);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_comp_status=str_replace("'","",$cbo_comp_status);
	$cbo_type=str_replace("'","",$cbo_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_programmer=str_replace("'","",$cbo_programmer);

	if($cbo_buyer_name>0){$where_cond .=" and a.buyer_name=$cbo_buyer_name";}
	if($cbo_brand_id>0){$where_cond .=" and a.brand_id=$cbo_brand_id";}
	if($cbo_season_id>0){$where_cond .=" and a.season_buyer_wise=$cbo_season_id";}
	if($cbo_season_year>0){$where_cond .=" and a.season_year=$cbo_season_year";}
	if($txt_style_ref!=""){$where_cond .=" and a.style_ref_no like('%$txt_style_ref%')";}
	if($cbo_programmer>0){$where_cond .=" and e.order_id like('%$cbo_programmer%')";}
	if($txt_req_no!=""){$where_cond .=" and a.requisition_number like('%$txt_req_no')";}
	if($cbo_sample_team>0){$where_cond .=" and a.TEAM_LEADER=$cbo_sample_team";}

	if($txt_sample_name!=""){$where_cond .=" and b.SAMPLE_NAME in(".implode(',',array_flip($sample_name_arr)).")";}
	if($txt_garments_item!=""){$where_cond .=" and b.GMTS_ITEM_ID in(".implode(',',array_flip($garments_item_arr)).")";}


	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_from)));
			$end_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_to)));
		}
		
		if($cbo_type==2){
			$where_cond .=" and to_date(to_char(a.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' and '$end_date'";
		}
		else if($cbo_type==1){
			$where_cond .=" and to_date(to_char(a.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' and '$end_date'";
		}
		else if($cbo_type==3){
			$where_cond .=" and to_date(to_char(a.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' and '$end_date'";
		}
		$date_cond='';
		$date_cond =" and to_date(to_char(a.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' and '$end_date'";
	}

		if($cbo_comp_status==2){
			$joinTable=" tna_process_mst d,";
			$joinWhere=" and a.id=d.po_number_id and d.task_type=5 and d.task_number = 8 and d.actual_start_date is not null";
		}
		
		
		if($db_type==0){
			$LISTAGG_SAMPLE_NAME="group_concat(b.SAMPLE_NAME) as SAMPLE_NAME";
			$LISTAGG_GAUGE="group_concat(c.GAUGE) as GAUGE";
			$LISTAGG_noofend="group_concat(c.NO_OF_ENDS) as NO_OF_ENDS";
			
			$LISTAGG_FABRIC_DESCRIPTION="group_concat(c.FABRIC_DESCRIPTION) as FABRIC_DESCRIPTION";
			$LISTAGG_GMTS_ITEM_ID="group_concat(c.GMTS_ITEM_ID) as GMTS_ITEM_ID";
			$LISTAGG_COLOR_DATA="group_concat(c.COLOR_DATA) as COLOR_DATA";
			$LISTAGG_ID="group_concat(b.ID) as SAMPLE_DTLS_ID";
			$LISTAGG_SIZE_DATA="group_concat( b.SIZE_DATA) as SIZE_DATA";
			$LISTAGG_EMBELLISHMENT_STATUS_ID="group_concat(b.EMBELLISHMENT_STATUS_ID) as EMBELLISHMENT_STATUS_ID";
			
		}
		else if($db_type==2){
			$LISTAGG_SAMPLE_NAME="rtrim(xmlagg(xmlelement(e,b.SAMPLE_NAME,',').extract('//text()') order by b.SAMPLE_NAME).GetClobVal(),',') as SAMPLE_NAME";
			$LISTAGG_GAUGE="rtrim(xmlagg(xmlelement(e,c.GAUGE,',').extract('//text()') order by c.GAUGE).GetClobVal(),',') as GAUGE";
			$LISTAGG_noofend="rtrim(xmlagg(xmlelement(e,c.NO_OF_ENDS,',').extract('//text()') order by c.NO_OF_ENDS).GetClobVal(),',') as NO_OF_ENDS";
			$LISTAGG_FABRIC_DESCRIPTION="rtrim(xmlagg(xmlelement(e,c.FABRIC_DESCRIPTION,',').extract('//text()') order by c.FABRIC_DESCRIPTION).GetClobVal(),',') as FABRIC_DESCRIPTION";
			$LISTAGG_GMTS_ITEM_ID="rtrim(xmlagg(xmlelement(e,c.GMTS_ITEM_ID,',').extract('//text()') order by c.GMTS_ITEM_ID).GetClobVal(),',') as GMTS_ITEM_ID";
			$LISTAGG_COLOR_DATA="rtrim(xmlagg(xmlelement(e,c.COLOR_DATA,',').extract('//text()') order by c.COLOR_DATA).GetClobVal(),',') as COLOR_DATA";
			$LISTAGG_ID="rtrim(xmlagg(xmlelement(e,b.ID,',').extract('//text()') order by b.ID).GetClobVal(),',') as SAMPLE_DTLS_ID";
			$LISTAGG_SIZE_DATA="rtrim(xmlagg(xmlelement(e,b.SIZE_DATA,',').extract('//text()') order by b.SIZE_DATA).GetClobVal(),',') as SIZE_DATA";
			$LISTAGG_EMBELLISHMENT_STATUS_ID="rtrim(xmlagg(xmlelement(e,b.EMBELLISHMENT_STATUS_ID,',').extract('//text()') order by b.EMBELLISHMENT_STATUS_ID).GetClobVal(),',') as EMBELLISHMENT_STATUS_ID";
		}
		
	
		$sql="SELECT 
		a.ID AS SAMPLE_MST_ID,a.REQUISITION_NUMBER,A.BRAND_ID, A.SEASON, A.SEASON_YEAR, a.INSERT_DATE,
		$LISTAGG_SAMPLE_NAME,
		a.BUYER_NAME,a.STYLE_REF_NO,max(b.DELV_START_DATE) as DELV_START_DATE, min(b.DELV_END_DATE) as DELV_END_DATE,
		a.DEALING_MARCHANT,a.TEAM_LEADER,a.REMARKS,a.STYLE_DESC,
		$LISTAGG_GAUGE,
		$LISTAGG_FABRIC_DESCRIPTION,
		$LISTAGG_GMTS_ITEM_ID,
		$LISTAGG_COLOR_DATA,
		a.REQUISITION_DATE,
		$LISTAGG_ID,
		$LISTAGG_SIZE_DATA,
		$LISTAGG_noofend,
		
		sum(b.SAMPLE_PROD_QTY) as SAMPLE_QTY,max(d.CONFIRM_DEL_END_DATE) as CONFIRM_DEL_END_DATE,
		$LISTAGG_EMBELLISHMENT_STATUS_ID,
		a.COMPANY_ID,a.SEASON,a.REFUSING_CAUSE,
		sum(c.REQUIRED_QTY) AS REQUIRED_QTY
		 FROM $joinTable SAMPLE_DEVELOPMENT_DTLS b,SAMPLE_DEVELOPMENT_FABRIC_ACC c,TNA_PROGRESS_COMMENTS e,SAMPLE_DEVELOPMENT_MST a 
		 LEFT JOIN SAMPLE_REQUISITION_ACKNOWLEDGE d ON a.id=d.SAMPLE_MST_ID
		 
		 WHERE a.id=b.SAMPLE_MST_ID and b.SAMPLE_MST_ID=c.SAMPLE_MST_ID and c.SAMPLE_MST_ID=e.ORDER_ID and a.entry_form_id = 341 and c.FORM_TYPE=1 and e.task_type=5 and e.task_ID=10 and a.company_id=$cbo_company_name $where_cond $joinWhere $date_cond 
		 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0
		group by a.ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.INSERT_DATE,a.DEALING_MARCHANT,a.BUYER_NAME,a.SEASON,a.STYLE_REF_NO,a.TEAM_LEADER,a.REFUSING_CAUSE,a.REMARKS,a.STYLE_DESC,A.BRAND_ID, A.SEASON, A.SEASON_YEAR order by a.REQUISITION_NUMBER
		";
		 //echo $sql;die;
        $dataArray=sql_select( $sql );
		foreach ($dataArray as $row){
			if($db_type==2){
				$row[SAMPLE_DTLS_ID] = $row[SAMPLE_DTLS_ID]->load();
			}
			$sample_id_arr[$row[SAMPLE_MST_ID]]=$row[SAMPLE_MST_ID];
			$sampl_dtls_id_arr[$row[SAMPLE_DTLS_ID]]=$row[SAMPLE_DTLS_ID];
		}
		
		  //print_r($sampl_dtls_id_arr);
		
		$sampl_dtls_id_arr=array_unique(explode(',',implode(',',$sampl_dtls_id_arr)));
	
		$smp_dtls_sql="select SAMPLE_MST_ID,sum(SAMPLE_PROD_QTY) as SAMPLE_PROD_QTY from SAMPLE_DEVELOPMENT_DTLS where is_deleted=0 and status_active=1";	
        
		$sampl_dtls_id_chunk_arr=array_chunk($sampl_dtls_id_arr,999);
		$p=1;
		foreach($sampl_dtls_id_chunk_arr as $process_id)
		{
			if($p==1) $smp_dtls_sql .="  and ( id in(".implode(',',$process_id).")"; 
			else  $smp_dtls_sql .=" or id in(".implode(',',$process_id).")";
			
			$p++;
		}
		$smp_dtls_sql .=") group by SAMPLE_MST_ID";
		
		   //echo $smp_dtls_sql;
		$smpDtlsDataArray=sql_select( $smp_dtls_sql );
		foreach ($smpDtlsDataArray as $row){
			$sample_pro_qty_arr[$row[SAMPLE_MST_ID]]=$row[SAMPLE_PROD_QTY];
		}	
	
	
	  	$sql="select ORDER_ID,TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where   task_type=5 ".where_con_using_array($sample_id_arr,0,'ORDER_ID')."";
	  	//echo $sql; die;
	  	$tnaDataArray=sql_select( $sql );
		foreach ($tnaDataArray as $rows){
			
			if($rows[TASK_ID] ==9 || $rows[TASK_ID]==10 || $rows[TASK_ID]==11 || $rows[TASK_ID]==12 || $rows[TASK_ID]==16 || $rows[TASK_ID]==18 || $rows[TASK_ID]==19 || $rows[TASK_ID]==20)
			{
				$saveComments_arr_task_id[$rows[ORDER_ID]][$rows[TASK_ID]][$rows[COMMENTS]]=$rows[COMMENTS];	
			}
			else{
				$saveComments_arr[$rows[ORDER_ID]][$rows[COMMENTS]]=$rows[COMMENTS];
			}
			
		}
		$sql="select ORDER_ID,TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where   task_type=6 ".where_con_using_array($sample_id_arr,0,'ORDER_ID')."";
	  	//echo $sql; die;
	  	$tnaDataArray=sql_select( $sql );
		foreach ($tnaDataArray as $rows){
			
			if($rows[TASK_ID]==16 || $rows[TASK_ID]==18)
			{
				$saveOrder_arr_task_id[$rows[ORDER_ID]][$rows[TASK_ID]][$rows[COMMENTS]]=$rows[COMMENTS];	
			}
			else{
				$saveOrder_arr[$rows[ORDER_ID]][$rows[COMMENTS]]=$rows[COMMENTS];
			}
			
		}
		//print_r($saveComments_arr[2878]);
	
	$comp_status_arr=array(1=>"Pending",2=>"Complete");
	
	//$width=(count($taskArr3)*205)+2610;	
	$width=1500;
		
			
        
		$sample_id_list_arr=array_chunk($sample_id_arr,999);
		$p=1;
		foreach($sample_id_list_arr as $sample_id_process)
		{
			if($p==1) $tna_cond .="  and ( po_number_id in(".implode(',',$sample_id_process).")"; 
			else  $tna_cond .=" or po_number_id in(".implode(',',$sample_id_process).")";
			
			$p++;
		}
		$tna_cond .=")";

		$tna_sql="select PO_NUMBER_ID,TASK_NUMBER,MAX(ACTUAL_START_DATE) as ACTUAL_START_DATE from tna_process_mst where task_type=5 and is_deleted=0 and status_active=1 $tna_cond group by PO_NUMBER_ID,TASK_NUMBER";
		
		 //echo $tna_sql;
		$tnaDataArray=sql_select( $tna_sql );
		foreach ($tnaDataArray as $row){
			$actual_start_date_arr[$row[PO_NUMBER_ID]][$row[TASK_NUMBER]]=$row[ACTUAL_START_DATE];
		}
	
	ob_start();
	?>
    <form name="sample_acknowledgement_2" id="sample_acknowledgement_2">
        <div style="width:<? echo $width+30;?>px; float:left;">
        <fieldset style="width:<? echo $width+20;?>px; margin-top:10px">
        <legend>
        Programmer Followup Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span style="background-color:#5ED05A">&nbsp;&nbsp;&nbsp;</span> [Green color] On Time Done
        <span style="background-color:#F00">&nbsp;&nbsp;&nbsp;</span> [Red color] Late Done
        <span style="background-color:#FF0">&nbsp;&nbsp;&nbsp;</span> [Yellow color] Not Done
        </legend>
            <table cellspacing="0" align="left" cellpadding="0" border="1" rules="all" width="<? echo $width+20;?>" class="rpt_table" >
                <thead>
                     <tr>                   
                        <th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="120">Buyer</th>
						<th rowspan="2" width="80">Style Ref</th>
						<th rowspan="2" width="80">Season</th>
						<th rowspan="2" width="80">Programmer</th>
						<th rowspan="2" width="120">Sample Nature</th>
                        <th rowspan="2" width="80">Req. No</th>
						<th rowspan="2" width="80">Gauge</th>
						<th rowspan="2" width="80">Image</th>
						<th rowspan="2" width="100">Style Description</th>
						<th rowspan="2" width="100">Nature of Work</th>
						<th rowspan="2" width="100">Sample Grade</th>
                        <th rowspan="2" width="100">Cause of Correction</th>
                        <th rowspan="2" width="100">Category of  Work</th>
                        <th rowspan="2" width="80">Complete Date</th>
                        <th rowspan="2" width="80">COMP STATUS</th>
                        <th rowspan="2">REMARKS</th>
                    </tr>
                </thead>
             </table> 
            
            <div style="width:<?=$width+20;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                 <table cellspacing="0"  align="left" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" id="tbl_list_search"> 
                    <tbody>
                        <?
                         $i=1;
                            foreach ($dataArray as $row){
								if($db_type==2){
									$row[SAMPLE_NAME] = $row[SAMPLE_NAME]->load();
									$row[GAUGE] = $row[GAUGE]->load();
									$row[NO_OF_ENDS] = $row[NO_OF_ENDS]->load();
									$row[FABRIC_DESCRIPTION] = $row[FABRIC_DESCRIPTION]->load();
									$row[GMTS_ITEM_ID] = $row[GMTS_ITEM_ID]->load();
									$row[COLOR_DATA] = $row[COLOR_DATA]->load();
									$row[SAMPLE_DTLS_ID] = $row[SAMPLE_DTLS_ID]->load();
									$row[SIZE_DATA] = $row[SIZE_DATA]->load();
									$row[EMBELLISHMENT_STATUS_ID] = $row[EMBELLISHMENT_STATUS_ID]->load();
								}
								
								$sizeArr=array();
								foreach(explode(',',$row[SIZE_DATA]) as $sizeStr){
									foreach(explode('__',$sizeStr) as $sizeStr2){
										list($size)=explode('_',$sizeStr2);
										$sizeArr[$size]=$size;
									}
								}
								$size=implode(',',$sizeArr);
								
								$itemArr=array();
								foreach(explode(',',$row[GMTS_ITEM_ID]) as $item_id){
									$itemArr[$item_id]=$garments_item[$item_id];
								}
								$item=implode(',',$itemArr);								
								
								$countStrArr=array();
								foreach(explode('-----',$row[COLOR_DATA]) as $cdr){
									$colorPopupDataArr=explode('__',$cdr);
									$countStrArr[$colorPopupDataArr[4]]=$colorPopupDataArr[4];
								}
								
								$plan_start_date[1]=add_date_without_offday($row[DELV_START_DATE],1);
								$plan_start_date[2]=add_date_without_offday($plan_start_date[1],1);

								$plan_start_date[3]=add_date_without_offday($plan_start_date[1],3);
								$plan_start_date[4]=add_date_without_offday($plan_start_date[3],1);
								
								if($row[EMBELLISHMENT_STATUS_ID]){
									$plan_start_date[5]=add_date_without_offday($plan_start_date[4],3);
									$plan_start_date[6]=add_date_without_offday($plan_start_date[5],1);
								}
								else
								{
									$plan_start_date[5]='';
									$plan_start_date[6]=add_date_without_offday($plan_start_date[4],1);
								}

								$plan_start_date[7]=$row[DELV_END_DATE];
								$plan_start_date[8]=$row[CONFIRM_DEL_END_DATE];
								if(($actual_start_date_arr[$row[SAMPLE_MST_ID]][8]  && $cbo_comp_status==1 )){continue;}
								
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								$sample_dtls_id_arr=explode(',',$row[SAMPLE_DTLS_ID]);

								$sample_id_arr=explode(',',$row[SAMPLE_NAME]);	
								$gauseName="";
								$exgauseid=array_unique(explode(',',$row[GAUGE]));
								foreach($exgauseid as $gid)
								{
									if($gauseName=="") $gauseName=$gauge_arr[$gid]; else $gauseName.=','.$gauge_arr[$gid];
								}
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" align="center">
									<td width="30" align="center"><?=$i; ?></td>
									<td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[BUYER_NAME]]; ?></td>
									<td width="80" style="word-break:break-all"><? echo $row[STYLE_REF_NO]; ?></td>
									<td width="80" style="word-break:break-all"><? echo $season_arr[$row[SEASON]]; ?></td>
									<td width="80" style="word-break:break-all" onClick="tna_process_comments('<? echo $row[SAMPLE_MST_ID].'_0_10_1';?>')" id="td_programmer_<? echo $row[SAMPLE_MST_ID];?>" ><? echo implode(',',$saveComments_arr_task_id[$row[SAMPLE_MST_ID]][10]);?></td>
                                    <td width="120" style="word-break:break-all" align="center"><? echo $sample_name_arr[$sample_id_arr[0]]; ?></td>
									<td width="80" style="word-break:break-all"><?=$row[REQUISITION_NUMBER]; ?></td>
									<td width="80" style="word-break:break-all"><?=$gauseName; ?></td>
									<td width="80" style="word-break:break-all" valign="middle">
                                    	<img src="../../../<? echo $imge_arr[$sample_dtls_id_arr[0]]; ?>" height="25" /></td>
                                    <td width="100" style="word-break:break-all"><? echo $row[STYLE_DESC]; ?></td>
									<?
									$work_of_nature=implode(',',$saveComments_arr_task_id[$row[SAMPLE_MST_ID]][16]);
									?>
                                    <td width="100" style="word-break:break-all" onClick="tna_process_comments('<? echo $row[SAMPLE_MST_ID].'_0_16_1';?>')" id="td_nature_<? echo $row[SAMPLE_MST_ID];?>" ><? echo $nature_of_workArr[$work_of_nature];?></td>
									<? 
									$sample_grade=implode(',',$saveComments_arr_task_id[$row[SAMPLE_MST_ID]][18])
									?>
                                    <td width="100" style="word-break:break-all" onClick="tna_process_comments('<? echo $row[SAMPLE_MST_ID].'_0_18_1';?>')" id="td_grade_<? echo $row[SAMPLE_MST_ID];?>" ><? echo $sample_gradeArr[$sample_grade];?></td>
                                  
									<td width="100" style="word-break:break-all" onClick="tna_process_comments('<? echo $row[SAMPLE_MST_ID].'_0_19_1';?>')" id="td_cause_<? echo $row[SAMPLE_MST_ID];?>" ><? echo implode(',',$saveComments_arr_task_id[$row[SAMPLE_MST_ID]][19]);?></td>
                                    <td width="100" style="word-break:break-all" onClick="tna_process_comments('<? echo $row[SAMPLE_MST_ID].'_0_20_1';?>')" id="td_category_<? echo $row[SAMPLE_MST_ID];?>" ><? echo implode(',',$saveComments_arr_task_id[$row[SAMPLE_MST_ID]][20]);?></td>


									<td width="80" style="word-break:break-all" onClick="update_tna_process('<? echo $row[SAMPLE_MST_ID].'_0_21_1';?>')" id="td_date_<? echo $row[SAMPLE_MST_ID];?>" style="cursor:pointer;"><? echo change_date_format($actual_start_date_arr[$row[SAMPLE_MST_ID]][21]); ?></td>
                                    <td width="80" style="word-break:break-all" align="center"><strong><? echo ($actual_start_date_arr[$row[SAMPLE_MST_ID]][8])?$comp_status_arr[2]:$comp_status_arr[1]; ?></strong></td>
									<td style="word-break:break-all"><? //echo change_date_format($row[CONFIRM_DEL_END_DATE]); ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
        </fieldset>
        </div>
    </form>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) 
    {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo $html."####".$filename."####1"; 
	exit();
}


if($action=="save_update_tna")
{
	echo load_html_head_contents("Task Update","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($sample_id,$date,$task_id,$type)=explode('_',$data);
	?> 
  <script>
	var permission='<? echo $permission; ?>';
	function fn_tna_date_update( operation )
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sample_id*txt_task_id*txt_type*txt_actual_start_date',"../../../");
		freeze_window(operation);
		http.open("POST","sweater_sample_programmer_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_tna_date_update_reponse;
	}

	function fn_tna_date_update_reponse()
	{
		if(http.readyState == 4) 
		{	
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
			else
			{
				alert('Invalid Operation');
			}
			set_button_status(1, permission, 'fn_tna_date_update',1);
			release_freezing();
		}
	}
	
</script>
    
    </head>
    <body onLoad="set_hotkey()">
   
    <div align="center" style="width:100%">
      <? 
	 	 echo load_freeze_divs ("../../../../",$permission,1);
	  ?>
    <form name="date_save_up_1"  id="date_save_up_1" autocomplete="off">
    <table>
    	<?
		if($task_id==21)
		{
			$sql="select TASK_NUMBER,MAX(ACTUAL_START_DATE) as ACTUAL_START_DATE from tna_process_mst where po_number_id=$sample_id and task_type=5 and TASK_NUMBER=21 group by TASK_NUMBER";	
			
		  $tnaDataArray=sql_select( $sql );
		  foreach ($tnaDataArray as $row){
			  $actual_start_date_arr[$row[TASK_NUMBER]]=$row[ACTUAL_START_DATE];
		  }
		  $save_up_status=count($actual_start_date_arr)?1:0;
		}
		if($task_id ==21){
		?>

        <tr>
            <td align="center">Date
            	<input type="text" name="txt_actual_start_date" id="txt_actual_start_date" class="datepicker" style="width:140px"  value="<? echo change_date_format($actual_start_date_arr[$task_id])?>" />
            </td>
			</tr>
			
            <? } ?>
			
        <tr>
        	<td>
            <input type="hidden" id="txt_sample_id" name="txt_sample_id"  value="<? echo $sample_id; ?>" />
            <input type="hidden" id="txt_task_id" name="txt_task_id"  value="<? echo $task_id; ?>" />
            <input type="hidden" id="txt_type" name="txt_type"  value="<? echo $type; ?>" />
            <? echo load_submit_buttons( $permission, "fn_tna_date_update", 1,0 ,"",2) ; ?> 
            </td>
        </tr>
        
    </table>
    </form>
    
    </div>
 </body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	die;
}

if($action=="save_update_delete")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 


	$txt_sample_id=str_replace("'",'',$txt_sample_id);
	$txt_task_id=str_replace("'",'',$txt_task_id);
	$txt_actual_start_date=str_replace("'",'',$txt_actual_start_date);
	
	
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		
			
		$update_id=return_field_value("id","tna_process_mst","po_number_id=$txt_sample_id and task_number=$txt_task_id and task_type=5 and is_deleted=0 and status_active=1");	

		if($update_id>0){
			//update  start-----------------------------------------;
			$field_array_up="task_type*actual_start_date";
			$data_array_up="5*'".$txt_actual_start_date."'";
			$rID=sql_update("tna_process_mst",$field_array_up,$data_array_up,"id",$update_id,1);
		}
		else
		{
			//new inser  start-----------------------------------------;
			$id=return_next_id( "id", "tna_process_mst", 1 ) ;
			$field_array="id, task_number, job_no,po_number_id,actual_start_date,status_active,is_deleted,task_type";
			$data_array="(".$id.",".$txt_task_id.",".$txt_sample_id.",".$txt_sample_id.",'".$txt_actual_start_date."',1,0,5)";
			$rID=sql_insert("tna_process_mst",$field_array,$data_array,1);
		}
				
			
			
		
		
		if($db_type==0)
		{
			  if($rID)
			  {
				  mysql_query("COMMIT");  
				  echo "1**".str_replace("'", '', $id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**";
			  }
		}
		if($db_type==1 || $db_type==2 )
		{
			if($rID)
			{
				  oci_commit($con);
				  echo "1**".str_replace("'", '', $id);
			}
			else
			{
				  oci_rollback($con);
				  echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
}

if($action=="comments_popup")
{ 
	echo load_html_head_contents("Task Update","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($sample_id,$date,$task_id,$type)=explode('_',$data);
	$taskArr=($report_type==3)?$taskArr3:$taskArr;
	 
	?> 
  <script>
	var permission='<?=$permission; ?>';
	var task_id_str='<?=implode(',',array_flip($taskArr));?>';
	var task_id = '<?=$task_id; ?>';

	function fn_tna_comments_update( operation )
	{
		freeze_window(operation);
		var fieldvar='';
		var taskIdArr=task_id_str.split(',');
		if(task_id==9)
		{
			fieldvar+="*txt_comments_9";
			//
		}
		else if(task_id==10)
		{
			fieldvar+="*txt_comments_10";
			//
		}
		else if(task_id==16)
		{
			fieldvar+="*txt_comments_16";
			//
		}
		else if(task_id==18)
		{
			fieldvar+="*txt_comments_18";
		}
		else if(task_id==19)
		{
			fieldvar+="*txt_comments_19";
		}
		else if(task_id==20)
		{
			fieldvar+="*txt_comments_20";
		}
		else
		{
			for(var i=0;i<taskIdArr.length;i++){
				fieldvar+="*txt_comments_"+taskIdArr[i];//"&txt_comments_" + taskIdArr[i] + "='" + $('#txt_comments_'+taskIdArr[i]).val()+"'";
				
				//
			}	
		}
		/*var dataall="&txt_sample_id='" + $('#txt_sample_id').val()+"'"+"&txt_task_id='" + $('#txt_task_id').val()+"'"+"&txt_type='" + $('#txt_type').val()+"'";
		var data2=dataall+fieldvar;
		alert(data2); release_freezing(); return;
		var data="action=save_update_delete_comments&operation="+operation+data2;*/
		var data="action=save_update_delete_comments&operation="+operation+get_submitted_data_string("txt_sample_id*txt_task_id*txt_type"+fieldvar,"../../../../");
		//alert(data);
		//alert(data); release_freezing(); return;
		
		http.open("POST","sweater_sample_programmer_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_tna_comments_update_reponse;
	}
	function fn_tna_comments_update_reponse()
	{
		if(http.readyState == 4) 
		{	
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==1)
			{
				$('#selected_data').val(reponse[1]);
				parent.emailwindow.hide();
				set_button_status(1, permission, 'fn_tna_comments_update',1);
			}
			else
			{
				alert('Invalid Operation');
				//set_button_status(0, permission, 'fn_tna_comments_update',1);
			}			
			release_freezing();
		}
	}
	
	function fnc_userpopup()
	{
		//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'sweater_sample_programmer_followup_report_controller.php?data='+dataStr+'&report_type='+report_type+'&action=user_tag_popup&permission='+permission, "User List", 'width=550px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'sweater_sample_programmer_followup_report_controller.php?action=user_tag_popup', 'User List', 'width=450px,height=250px,center=1,resize=1,scrolling=0','../../../');
		emailwindow.onclose=function()
		{
			var datastr=this.contentDoc.getElementById("hidden_usertag_id").value;
			var dataArr=datastr.split('_');
			$('#txt_comments_'+task_id).val(dataArr[1]); 
		}
	}
	
</script>
    
    </head>
    <body onLoad="set_hotkey()">
   
    <div align="center" style="width:100%">
      <? 
	 	 echo load_freeze_divs ("../../../../",$permission,1);
	  	if($task_id==9)
	  	{
	  		$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=9";	
			$tnaDataArray=sql_select( $sql );
			foreach ($tnaDataArray as $row){
				$comments_arr[$row[TASK_ID]]=$row[COMMENTS];
			}
			$save_up_status=count($comments_arr)?1:0;
	  	}
	  	elseif ($task_id==10) {
	  		$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=10";
	  		$tnaDataArray=sql_select( $sql );
			foreach ($tnaDataArray as $row){
				$comments_arr[$row[TASK_ID]]=$row[COMMENTS];
			}
			$save_up_status=count($comments_arr)?1:0;
	  	}
		  elseif ($task_id==16) {
			$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=16";
			$tnaDataArray=sql_select( $sql );
		  foreach ($tnaDataArray as $row){
			  $comments_arr[$row[TASK_ID]]=$row[COMMENTS];
		  }
		  $save_up_status=count($comments_arr)?1:0;
		}
		elseif ($task_id==18) {
			$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=18";
			$tnaDataArray=sql_select( $sql );
		  foreach ($tnaDataArray as $row){
			  $comments_arr[$row[TASK_ID]]=$row[COMMENTS];
		  }
		  $save_up_status=count($comments_arr)?1:0;
		}
		elseif ($task_id==19) {
			$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=19";
			$tnaDataArray=sql_select( $sql );
		  foreach ($tnaDataArray as $row){
			  $comments_arr[$row[TASK_ID]]=$row[COMMENTS];
		  }
		  $save_up_status=count($comments_arr)?1:0;
		}
		elseif ($task_id==20) {
			$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=20";
			$tnaDataArray=sql_select( $sql );
		  foreach ($tnaDataArray as $row){
			  $comments_arr[$row[TASK_ID]]=$row[COMMENTS];
		  }
		  $save_up_status=count($comments_arr)?1:0;
		}
	  	else{
	  		$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5";
	  		$tnaDataArray=sql_select( $sql );
			foreach ($tnaDataArray as $row){
				$comments_arr[$row[TASK_ID]]=$row[COMMENTS];
			} 
			$save_up_status=count($comments_arr)?1:0;		
	  	}
	  ?>
    <form name="date_save_up_1"  id="date_save_up_1" autocomplete="off">
     <table cellspacing="0" align="center" cellpadding="0" border="1" width="95%" rules="all" class="rpt_table" >
    	
        <thead>
            <th>Task Name</th>
            <th>Comments</th>
        </thead>
        <tbody>

            <? 
            //echo $task_id; die;
            if($task_id ==9){ ?>
	            <tr>
	                <td width="140" align="right"><b>Designer</b></td>
	                <td align="center">
	                    <textarea name="txt_comments_<?=$task_id; ?>" id="txt_comments_<?=$task_id; ?>" class="text_boxes" style="width:90%;" ><?=$comments_arr[$task_id];?></textarea>
	                </td>
	            </tr>
            <? }
            elseif($task_id ==10){ ?>
	            <tr>
	                <td width="140" align="right"><b>Programmer</b></td>
	                <td align="center">
	                    <textarea name="txt_comments_<?=$task_id; ?>" id="txt_comments_<?=$task_id; ?>" class="text_boxes" style="width:90%;" onDblClick="fnc_userpopup();" readonly placeholder="Browse"><?=$comments_arr[$task_id];?></textarea>
	                </td>
	            </tr>
            <? }
			elseif($task_id ==16){ ?>
	            <tr>
	                <td width="140"  align="center"><b>Nature Of Work</b></td>
					<td align="center">
					<? echo create_drop_down( "txt_comments_16", 140, $nature_of_workArr,"", 1, "-- Select --", "", "" );?>
					</td>
	            </tr>
            <? }
			elseif($task_id ==18){ ?>
	            <tr>
	                <td width="140"  align="center"><b>Sample Grade</b></td>
	                <td align="center">
						<? echo create_drop_down( "txt_comments_18", 140, $sample_gradeArr ,"", 1, "-- Select --","", "" );?>
	                </td>
	            </tr>
            <? }
			elseif($task_id ==19){ ?>
	            <tr>
	                <td width="140"  align="center"><b>Cause of Correction </b></td>
					<td align="center">
                    <textarea name=txt_comments_19 id=txt_comments_19 class="text_boxes" style="width:90%;" ><?=$comments_arr[$task_id];?></textarea>
                </td>
	            </tr>
            <? }
			elseif($task_id ==20){ ?>
	            <tr>
	                <td width="140"  align="center"><b>Category of  Work</b></td>
	                <td align="center">
                    <textarea name=txt_comments_20 id=txt_comments_20 class="text_boxes" style="width:90%;" ><?=$comments_arr[$task_id];?></textarea>
                </td>
	            </tr>
            <? }
            else{
            foreach($taskArr as $task_id=>$task_name){ ?>
            <tr>
                <td width="140" align="right"><b><?=$task_name; ?></b></td>
                <td align="center">
                    <textarea name="txt_comments_<?=$task_id; ?>" id="txt_comments_<?=$task_id; ?>" class="text_boxes" style="width:90%;" ><?=$comments_arr[$task_id];?></textarea>
                </td>
            </tr>
            <? }
            } ?>

        </tbody>
    </table>
    <table width="100%">
        <tfoot>
        	<td align="center">
            <input type="hidden" id="txt_sample_id" name="txt_sample_id"  value="<? echo $sample_id; ?>" />
            <input type="hidden" id="txt_task_id" name="txt_task_id"  value="<? echo $task_id; ?>" />
            <input type="hidden" id="txt_type" name="txt_type"  value="<? echo $type; ?>" />
            
            <input type="hidden" id="selected_data" name="selected_data"  value="" />
            
            <? echo load_submit_buttons( $permission, "fn_tna_comments_update",$save_up_status,0,"",1);?> 
            </td>
        </tfoot>
    </table>
    
    </form>
    
    </div>
 </body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	exit();
}

if ($action=="user_tag_popup")
{
	echo load_html_head_contents("User Information", "../../../../", 1, 1,$unicode,'','');
	?> 
	<script>
		function js_set_value( str) 
		{
			$('#hidden_usertag_id').val( str );
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<input type="hidden" name="hidden_usertag_id" id="hidden_usertag_id" class="text_boxes" />
	<?
	$sql="select id,employee_id,user_full_name,designation,department_id,user_email from user_passwd where valid=1";
	
	$lib_designation_arr=return_library_array( "select id,custom_designation from lib_designation", "id","custom_designation");
	$lib_department_arr=return_library_array( "select id,department_name from lib_department", "id","department_name");
	
	$arr=array (1=>$lib_designation_arr,2=>$lib_department_arr);
	
	echo create_list_view("list_view", "User Full Name, Designation, Department", "150,100","430","220",0, $sql, "js_set_value", "id,user_full_name", "", 1, "0,designation,department_id", $arr, "user_full_name,designation,department_id",'','setFilterGrid("list_view",-1);','','','','');
	exit();
}


if($action=="time_weight_popup")
{ 
	echo load_html_head_contents("Task Update","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($sample_id,$date,$task_id,$type)=explode('_',$data);
	 //$permission="1_1_0_0";
	 
	?> 
  <script>
	var permission='<?=$permission; ?>';
	var task_id_str='<?=implode(',',array_flip($taskArr));?>';
	var task_id = '<?=$task_id; ?>';

	function fn_tna_comments_update( operation )
	{
		freeze_window(operation);
		var field='';
		var taskIdArr=task_id_str.split(',');
		field+='*txt_comments_'+task_id;
		
		var data="action=save_update_delete_comments&operation="+operation+get_submitted_data_string('txt_sample_id*txt_task_id*txt_type'+field,"../../../");
		alert(data);return;
		http.open("POST","sweater_sample_programmer_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_tna_comments_update_reponse;
	}
	function fn_tna_comments_update_reponse()
	{
		if(http.readyState == 4) 
		{	
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==1)
			{
				$('#selected_data').val(reponse[1]);
				parent.emailwindow.hide();
				set_button_status(1, permission, 'fn_tna_comments_update',1);
			}
			else
			{
				alert('Invalid Operation');
				//set_button_status(0, permission, 'fn_tna_comments_update',1);
			}			
			release_freezing();
		}
	}
	
</script>
    
    </head>
    <body onLoad="set_hotkey()">
   
    <div align="center" style="width:100%">
      <? 
	 	 echo load_freeze_divs ("../../../../",$permission,1);
 	  		
			$sql="select TASK_ID,COMMENTS from TNA_PROGRESS_COMMENTS where ORDER_ID=$sample_id and task_type=5 and task_id=$task_id";	
			$tnaDataArray=sql_select( $sql );
			foreach ($tnaDataArray as $row){
				$comments_arr[$row[TASK_ID]]=$row[COMMENTS];
			}
			$save_up_status=count($comments_arr)?1:0;
	  ?>
    <form name="date_save_up_1"  id="date_save_up_1" autocomplete="off">
     <table cellspacing="0" align="center" cellpadding="0" border="1" width="95%" rules="all" class="rpt_table" >
    	
        <thead>
            <th>Task Name</th>
            <th>Comments</th>
        </thead>
        <tbody>
	            <tr>
	                <td width="140" align="right"><b><?=($task_id==11)?"Time":"Weight";?></b></td>
	                <td align="center">
	                    <textarea name="txt_comments_<?= $task_id; ?>" id="txt_comments_<?= $task_id; ?>" class="text_boxes" style="width:90%;" ><?= $comments_arr[$task_id];?></textarea>
	                </td>
	            </tr>

        </tbody>
    </table>
    <table width="100%">
        <tfoot>
        	<td align="center">
            <input type="hidden" id="txt_sample_id" name="txt_sample_id"  value="<? echo $sample_id; ?>" />
            <input type="hidden" id="txt_task_id" name="txt_task_id"  value="<? echo $task_id; ?>" />
            <input type="hidden" id="txt_type" name="txt_type"  value="<? echo $type; ?>" />
            
            <input type="hidden" id="selected_data" name="selected_data"  value="" />
            
            <? echo load_submit_buttons( $permission, "fn_tna_comments_update",$save_up_status,0,"",1);?> 
            </td>
        </tfoot>
    </table>
    
    </form>
    
    </div>
 </body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	exit();
}

if($action=="save_update_delete_comments")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$txt_sample_id=str_replace("'",'',$txt_sample_id);
	$txt_task_id=str_replace("'",'',$txt_task_id);
	$taskArr=($report_type==3)?$taskArr3:$taskArr;
	
	if ($operation==0 || $operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		
		if($txt_task_id ==9)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=9",1);
		}
		else if($txt_task_id ==10)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=10",1);
		}
		else if($txt_task_id ==16)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=16",1);
		}
		else if($txt_task_id ==18)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=18",1);
		}
		else if($txt_task_id ==19)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=18",1);
		}
		else if($txt_task_id ==20)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=18",1);
		}
		else if($txt_task_id ==11 || $txt_task_id ==12)
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id=$txt_task_id",1);
		}
		else
		{
			$delete=execute_query("delete TNA_PROGRESS_COMMENTS where order_id=$txt_sample_id and task_id not in (9,10,11,12,16,18,19,20)",1);
		}	
		
		//new inser  start-----------------------------------------;
		$id=return_next_id( "id", "TNA_PROGRESS_COMMENTS", 1 ) ;
		$field_array="id, order_id, task_id,comments,status_active,is_deleted,task_type";
		$allCommentsArr=array(); //echo '10**<pre>';
		/*if($txt_task_id ==11 || $txt_task_id ==12)
		{
			$txt_comments='txt_comments_'.$txt_task_id;
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else */if($txt_task_id ==9)
		{
			$txt_comments='txt_comments_9';
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else if($txt_task_id ==10)
		{
			$txt_comments='txt_comments_10';
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else if($txt_task_id ==16)
		{
			$txt_comments='txt_comments_16';
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else if($txt_task_id ==18)
		{
			$txt_comments='txt_comments_18';
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else if($txt_task_id ==19)
		{
			$txt_comments='txt_comments_19';
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else if($txt_task_id ==20)
		{
			$txt_comments='txt_comments_20';
			$data_array="(".$id.",".$txt_sample_id.",".$txt_task_id.",'".str_replace("'","",$$txt_comments)."',1,0,5)";
			$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
			$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
		}
		else
		{
			foreach($taskArr as $task_id=>$task_name){
				$txt_comments='txt_comments_'.$task_id;
				if(trim(str_replace("'","",$$txt_comments))!='')
				{
					$data_array="(".$id.",".$txt_sample_id.",".$task_id.",".$$txt_comments.",1,0,5)";
					//echo "INSERT INTO TNA_PROGRESS_COMMENTS (".$field_array.") VALUES ".$data_array; //die;
					$rID=sql_insert("TNA_PROGRESS_COMMENTS",$field_array,$data_array,1);
					$id=$id+1;
					$sqr_tt[$id]=$rID;		
					$allCommentsArr[str_replace("'","",$$txt_comments)]=str_replace("'","",$$txt_comments);
				}

			}
		}
		/*echo '10**<pre>';
		echo "INSERT INTO TNA_PROGRESS_COMMENTS (".$field_array.") VALUES ".$data_array; die;
		print_r($data_array); die;*/
		//echo "10**".$rID; die;		
		if($db_type==0)
		{
			  if($rID)
			  {
				  mysql_query("COMMIT");  
				  echo "1**".implode(',',$allCommentsArr);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**".implode(',',$allCommentsArr);
			  }
		}
		if($db_type==1 || $db_type==2 )
		{
			//echo "10**".$rID; die;
			if($rID)
			{
				  oci_commit($con);
				  echo "1**".implode(',',$allCommentsArr);
			}
			else
			{
				  oci_rollback($con);
				  echo "10**".implode(',',$allCommentsArr);
			}
		}
		
		disconnect($con);
		die;
	}
}
?>

