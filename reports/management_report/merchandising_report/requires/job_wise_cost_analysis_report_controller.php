<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$job_year)=explode('_',$data);
	if($buyer_id!=0){$whereCon=" and buy.id=$buyer_id";}
	?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_style_name = new Array();
	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value( strcon )
	{
		$('#txt_job_no').val( strcon );
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
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond $whereCon and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $job_year; ?>', 'job_no_list_view', 'search_div', 'job_wise_cost_analysis_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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


if($action=="job_no_list_view")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_by,$search_common,$year_id,)=explode('**',$data);
	//print_r ($data);
	?>	
 
     <input type="hidden" id="txt_job_no" />
 <?
	if ($company_id!=0) $where_con.=" and company_name=$company_id";
	if ($buyer_id!=0) $where_con.=" and buyer_name=$buyer_id";

	if($db_type==0)
	{
		if(str_replace("'","",$year_id)!=0) $where_con.=" and year(insert_date)=".str_replace("'","",$year_id).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$year_id)!=0) $where_con.=" and to_char(insert_date,'YYYY')=".str_replace("'","",$year_id).""; else $year_cond="";
	}
	
	if ($search_by==1 && $search_common!='') $where_con.=" and job_no like('%".trim($search_common)."')";
	if ($search_by==2 && $search_common!='') $where_con.=" and style_ref_no like('%".trim($search_common)."%')";

	
	
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	$sql= "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $where_con group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC ";
	
	//echo $sql;die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "job_no", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','',"") ;
	exit();
}



if( $action=="report_generate" )
{
	
	require_once('../../../../mailer/class.phpmailer.php');
	require_once('../../../../includes/class4/class.conditions.php');
	require_once('../../../../includes/class4/class.reports.php');
	require_once('../../../../includes/class4/class.fabrics.php');
	require_once('../../../../includes/class4/class.yarns.php');
	require_once('../../../../includes/class4/class.washes.php');
	require_once('../../../../includes/class4/class.emblishments.php');
	require_once('../../../../includes/class4/class.trims.php');
	require_once('../../../../includes/class4/class.commercials.php');


	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	
	
	
		$condition= new condition();
		if($company_name>0){
			$condition->company_name("=$company_name");
		}
		if($cbo_buyer_name>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if($tna_process_start_date !=''){
			$condition->pub_shipment_date(" > '".$tna_process_start_date."'");
		}
		
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no(" = '".str_replace("'","",$txt_job_no)."'");
		}
	
		
		if(trim($txt_ponumber_id) !=''){
			$condition->po_id(" in($txt_ponumber_id) ");
		}
		$condition->init();
		
		
		
		$fabric= new fabric($condition);
		$fabricdata=$fabric->getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
 		
		$trims= new trims($condition);
		$trimsdata=$trims->getAmountArray_precostdtlsid();
		 //print_r($trimsdata);die;
		
		$emblishment= new emblishment($condition);
		$emblishmentdata=$emblishment->getAmountArray_by_emblishmentid();
		
		$wash= new wash($condition);
		//$washdata=$wash->getAmountArray_by_emblishmentid();
		//$emblishmentdata=$emblishmentdata+$washdata;
		
		$washdata=$wash->getAmountArray_by_orderAndEmblishmentid();
		// print_r($washdata);die;
 	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $where_con.=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	elseif($cbo_buyer_name!=0) $where_con.=" and a.buyer_name=$cbo_buyer_name";
	
	
	if($db_type==0)
	{
		if($cbo_year!=0) $where_con.=" and year(a.insert_date)=".$cbo_year.""; 
	}
	else
	{
		if($cbo_year!=0) $where_con.=" and to_char(a.insert_date,'YYYY')=".$cbo_year."";
	}
	
	if($txt_job_no!=""){
		$where_con.="and b.job_no='".$txt_job_no."'";
		$where_con2.="and a.job_no='".$txt_job_no."'";
	}
	

	if($db_type==0){
		$PO_ID="group_concat( distinct b.ID) AS PO_ID";
		$PO_NUMBER="group_concat( distinct b.PO_NUMBER) AS PO_NUMBER";
	}
	if($db_type==2){
		$PO_ID="listagg(b.ID ,',') within group (order by b.ID) AS PO_ID";
		$PO_NUMBER="listagg(b.PO_NUMBER ,',') within group (order by b.PO_NUMBER) AS PO_NUMBER";
	}
	
	//JObo..........
	$job_sql="select  a.SET_SMV,A.JOB_NO,A.BUYER_NAME,a.STYLE_REF_NO,a.GMTS_ITEM_ID,$PO_ID,$PO_NUMBER,SUM(B.PLAN_CUT) AS PLAN_CUT, SUM(A.TOTAL_SET_QNTY*B.PO_QUANTITY) as PO_QUANTITY_PCS,SUM(B.PO_TOTAL_PRICE) AS AMOUNT,MAX(PUB_SHIPMENT_DATE) AS SHIPMENT_DATE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  and a.is_deleted=0 and b.is_deleted=0 $where_con2 GROUP BY A.JOB_NO,a.SET_SMV,A.BUYER_NAME,a.STYLE_REF_NO,a.GMTS_ITEM_ID";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $rows){
		$jobDataArr=$rows;
	}
	
	//Exfactory
	$ex_factory_arr=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id in(".$jobDataArr[PO_ID].") group by po_break_down_id", "po_break_down_id", "qnty");	
	
	
    //Precost........................
   //$pre_cost_sql="select b.ID as FB_COST_DTLS_ID,A.GARMENTS_NATURE,b.JOB_NO,b.CONSTRUCTION,b.COMPOSITION,B.GSM_WEIGHT,B.BODY_PART_ID,B.UOM, B.RATE,B.AMOUNT,B.AVG_CONS  from WO_PRE_COST_MST a,WO_PRE_COST_FABRIC_COST_DTLS b where A.JOB_ID=B.JOB_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 $where_con";
   
	if($db_type==0){
		$DIA_WIDTH="group_concat( distinct c.DIA_WIDTH) AS DIA_WIDTH";
		$PO_BREAK_DOWN_ID="group_concat( distinct c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
		$COLOR_NUMBER_ID="group_concat( distinct c.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID";
	}
	if($db_type==2){
		$DIA_WIDTH="listagg(c.DIA_WIDTH ,',') within group (order by c.DIA_WIDTH) AS DIA_WIDTH";
		$PO_BREAK_DOWN_ID="listagg(c.PO_BREAK_DOWN_ID ,',') within group (order by c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
		$COLOR_NUMBER_ID="listagg(c.COLOR_NUMBER_ID ,',') within group (order by c.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID";
	}

   $pre_cost_sql="SELECT b.ID AS FB_COST_DTLS_ID,
         A.GARMENTS_NATURE,
         b.JOB_NO,
		 b.BODY_PART_ID,
		 b.LIB_YARN_COUNT_DETER_ID,
         b.CONSTRUCTION,
         b.COMPOSITION,
         B.GSM_WEIGHT,
         B.BODY_PART_ID,
         B.UOM,
         $DIA_WIDTH,
		 $PO_BREAK_DOWN_ID,
		 $COLOR_NUMBER_ID,
         b.AMOUNT as AMOUNT,
         b.AVG_FINISH_CONS as CONS
    FROM WO_PRE_COST_MST a,
         WO_PRE_COST_FABRIC_COST_DTLS b,
         WO_PRE_COS_FAB_CO_AVG_CON_DTLS c
   WHERE     A.JOB_ID = B.JOB_ID and b.id=c.PRE_COST_FABRIC_COST_DTLS_ID
         AND b.JOB_ID = c.JOB_ID
         AND A.IS_DELETED = 0
         AND A.STATUS_ACTIVE = 1
         AND b.IS_DELETED = 0
         AND b.STATUS_ACTIVE = 1
         AND c.IS_DELETED = 0
         AND c.STATUS_ACTIVE = 1 and c.CONS !=0
         $where_con
	GROUP BY b.ID,
         A.GARMENTS_NATURE,
         b.JOB_NO,
		 b.BODY_PART_ID,
		 b.LIB_YARN_COUNT_DETER_ID,
         b.CONSTRUCTION,
         b.COMPOSITION,
         B.GSM_WEIGHT,
         B.BODY_PART_ID,
         B.UOM,
         b.AMOUNT,b.AVG_FINISH_CONS";
   
   
	  //echo $pre_cost_sql;die; 
	$pre_cost_sql_result=sql_select($pre_cost_sql);
	$fbCostIdArr=array();
	foreach($pre_cost_sql_result as $rows){
		$fbCostIdArr[$rows[FB_COST_DTLS_ID]]=$rows[FB_COST_DTLS_ID];
		$fbIdArr[$rows[FB_COST_DTLS_ID]]=$rows[FB_COST_DTLS_ID];
	}
	//echo $pre_cost_sql;
	
	if($db_type==0){
		$PLACE="group_concat( distinct c.PLACE) AS PLACE";
		$PO_BREAK_DOWN_ID="group_concat( distinct c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
	}
	if($db_type==2){
		$PLACE="listagg(c.PLACE ,',') within group (order by c.PLACE) AS PLACE";
		$PO_BREAK_DOWN_ID="listagg(c.PO_BREAK_DOWN_ID ,',') within group (order by c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
	}
	//Trims........................
   $trims_sql="SELECT a.ID,a.TRIM_GROUP,
         a.DESCRIPTION,
         a.CONS_UOM,
		 b.ITEM_NAME,
         b.TRIM_TYPE,
         a.CONS_DZN_GMTS AS CONS,
         a.AMOUNT,
		 $PLACE,
		 $PO_BREAK_DOWN_ID
    FROM WO_PRE_COST_TRIM_COST_DTLS a,
         LIB_ITEM_GROUP b,
         WO_PRE_COST_TRIM_CO_CONS_DTLS c
   WHERE     A.TRIM_GROUP = b.id
         AND A.JOB_NO = C.JOB_NO
         AND a.id = c.WO_PRE_COST_TRIM_COST_DTLS_ID
         AND A.JOB_NO ='".$txt_job_no."'
         AND a.IS_DELETED = 0
         AND b.IS_DELETED = 0
         AND a.STATUS_ACTIVE = 1
         AND b.STATUS_ACTIVE = 1  and C.CONS !=0
	GROUP BY a.ID,
		 a.TRIM_GROUP,
         a.DESCRIPTION,
         a.CONS_UOM,
		 a.CONS_DZN_GMTS,
         a.AMOUNT,
		 b.ITEM_NAME,
         b.TRIM_TYPE";
	    //echo $trims_sql;die;
	$trims_sql_result=sql_select($trims_sql);
	foreach($trims_sql_result as $rows){
		 $fbCostIdArr[$rows[ID]]=$rows[ID];
	}
 
	//Emblishment........................
   $emb_sql="select a.ID,a.EMB_NAME,a.EMB_TYPE,a.BODY_PART_ID,a.CONS_DZN_GMTS,a.RATE,a.AMOUNT  from WO_PRE_COST_EMBE_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'";
	// echo $pi_sql;
	$emb_sql_result=sql_select($emb_sql);	
	foreach($emb_sql_result as $rows){
		 $fbCostIdArr[$rows[ID]]=$rows[ID];
	}
	
	$post_trim_sql="select  a.PRE_COST_FABRIC_COST_DTLS_ID, sum(A.AMOUNT) as AMOUNT from  WO_BOOKING_DTLS a where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].") group by a.PRE_COST_FABRIC_COST_DTLS_ID";
    // echo $post_trim_sql;
	$post_trim_result=sql_select($post_trim_sql);
    $trimsPiDataArr=array();
	foreach($post_trim_result as $rows){
		$trimsPiDataArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]][AMOUNT]=$rows[AMOUNT];
	}

	$post_emb_sql="select  a.PRE_COST_FABRIC_COST_DTLS_ID, sum(B.AMOUNT) as AMOUNT from  WO_BOOKING_DTLS a,WO_EMB_BOOK_CON_DTLS b  where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.id=b.wo_booking_dtls_id and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].") group by a.PRE_COST_FABRIC_COST_DTLS_ID";
    // echo $post_emb_sql;
	$post_emb_result=sql_select($post_emb_sql);
    $embPiDataArr=array();
	foreach($post_emb_result as $rows){
		$embPiDataArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]][AMOUNT]=$rows[AMOUNT];
	}
	//pi......................
    //    $pi_sql="select  b.PI_ID,a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,A.JOB_NO,b.FABRIC_CONSTRUCTION as CONSTRUCTION,b.FABRIC_COMPOSITION as COPMPOSITION,b.DIA_WIDTH,B.UOM,B.RATE,B.AMOUNT,B.COLOR_ID, b.ITEM_GROUP, b.GMTS_ITEM_ID,  b.EMBELL_NAME,  b.EMBELL_TYPE,b.PI_ID from  WO_BOOKING_DTLS a,COM_PI_ITEM_DETAILS  b where  A.BOOKING_NO=B.WORK_ORDER_NO  and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].")";
	
	 // echo $pi_sql;
	// $pi_sql_result=sql_select($pi_sql);
	// $piDataArr=array();
    // $embPiDataArr=array();$trimsPiDataArr=array();
	// foreach($pi_sql_result as $rows){

		//$piDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[DIA_WIDTH].'**'.$rows[PRE_COST_FABRIC_COST_DTLS_ID].'**'.$rows[CONSTRUCTION].'**'.$rows[COPMPOSITION]][AMOUNT][$rows[COLOR_ID]]=$rows[AMOUNT];
		
		// $embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][AMOUNT][$rows[COLOR_ID]]=$rows[AMOUNT];
		// $trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][AMOUNT][$rows[COLOR_ID]]=$rows[AMOUNT];
		
	// 	if($fbIdArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]]){
	// 		$fbPIArr[$rows[PI_ID]]=$rows[PI_ID];
	// 	}
		
	// }
  
 
 
    //pi value for fabric......................
    // $pi_sql="select  b.FABRIC_CONSTRUCTION as CONSTRUCTION,b.FABRIC_COMPOSITION as COPMPOSITION,b.DIA_WIDTH,B.UOM,B.RATE,B.AMOUNT,B.COLOR_ID, b.ITEM_GROUP,   b.GMTS_ITEM_ID,  b.EMBELL_NAME,  b.EMBELL_TYPE,b.PI_ID from  COM_PI_ITEM_DETAILS  b where  B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and b.PI_ID in(".implode(',',$fbPIArr).")";

    $pi_sql="select  A.CONSTRUCTION,A.COPMPOSITION,A.DIA_WIDTH,A.PRE_COST_FABRIC_COST_DTLS_ID,B.BODY_PART_ID, sum(A.AMOUNT) as AMOUNT from  WO_BOOKING_DTLS a,WO_PRE_COST_FABRIC_COST_DTLS b where a.PRE_COST_FABRIC_COST_DTLS_ID=b.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].") group by A.CONSTRUCTION,A.COPMPOSITION,A.DIA_WIDTH,a.PRE_COST_FABRIC_COST_DTLS_ID,B.BODY_PART_ID";
	 // echo $pi_sql;
	$pi_sql_result=sql_select($pi_sql);
	$piDataArr=array(); 
	foreach($pi_sql_result as $rows){
		$piDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COPMPOSITION].'**'.$rows[BODY_PART_ID]][AMOUNT]+=$rows[AMOUNT];
	}
 
 //..............................end;
 
 
   //Precost dtls..........................
	$pre_dtls="select a.PRICE_DZN,a.MARGIN_PCS_SET,a.FABRIC_COST,a.TRIMS_COST,a.EMBEL_COST,a.WASH_COST from WO_PRE_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'"; 
	$pre_dtls_result=sql_select($pre_dtls);


   //commision rate..........................
	$pre_commision_rate_arr=return_library_array( "select a.COMMISSION_BASE_ID,a.COMMISION_RATE from WO_PRE_COST_COMMISS_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'", "COMMISSION_BASE_ID", "COMMISION_RATE");	
	
	
   //commision rate..........................
	$commercial_cost_rate_arr=return_library_array( "select a.ITEM_ID,a.RATE from WO_PRE_COST_COMARCI_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'", "ITEM_ID", "RATE");	
	
   //Image..........................
   	$img_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where  MASTER_TBLE_ID ='".$txt_job_no."'  and FORM_NAME='knit_order_entry'", "ID", "IMAGE_LOCATION");	
 	
	$width=1100;
	
		ob_start();
	?>
        <div style="width:<?= $width;?>px">
        <fieldset style="width:100%;">
          <table width="<?= $width;?>">
              <tr class="form_caption">
                    <td colspan="14" align="center" style="font-size:18px;"><?= $report_title;?></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="14" align="center" style="font-size:15px;"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
            <br />
            <table width="" cellpadding="5" cellspacing="5" border="0">
                <tr>
                    <td width="70"><strong>Job No</strong></td><th>:</th>
                    <td width="120"><?= $jobDataArr[JOB_NO];?></td>
                    <td width="110"><strong>Shipment Date</strong></td><th>:</th>
                    <td width="120"><?= change_date_format($jobDataArr[SHIPMENT_DATE]);?></td>
                    <td width="80"><strong>Costing SMV</strong></td><th>:</th>
                    <td width="120"><?= $jobDataArr[SET_SMV];?></td>
               </tr>
                <tr>
                    <td><strong>Buyer</strong></td><th>:</th>
                    <td><?= $buyer_lib[$jobDataArr[BUYER_NAME]];?></td>
                    <td><strong>Order QTY (PCS)</strong></td><th>:</th>
                    <td><?= $jobDataArr[PO_QUANTITY_PCS];?></td>
                    <td><strong>Prod SMV</strong></td><th>:</th>
                    <td></td>
               </tr>
               
                <tr>
                    <td><strong>Style</strong></td><th>:</th>
                    <td><?= $jobDataArr[STYLE_REF_NO];?></td>
                    <td><strong>Shipment QTY (PCS)</strong></td><th>:</th>
                    <td><?= $ship_qty=array_sum($ex_factory_arr)*1;?></td>
                    <td><strong>Plan Cut Qty</strong></td><th>:</th>
                    <td><?= $jobDataArr[PLAN_CUT];?></td>
               </tr>
               
                <tr>
                    <td><strong>PO Number</strong></td><th>:</th>
                    <td><p><?= $jobDataArr[PO_NUMBER];?></p></td>
                    <td><strong>Unit Price</strong></td><th>:</th>
                    <td><?= number_format($unite_price=($jobDataArr[AMOUNT]/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                    <td><strong>Garments Item</strong></td><th>:</th>
                    <td><?= $garments_item[$jobDataArr[GMTS_ITEM_ID]];?></td>
               </tr>
            </table>
            <br />
            
            
            <div style="width:<?= $width+20;?>px;" id="scroll_body">
            
            <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Department</th>
                    <th width="120">Construction</th>
                    <th width="120">Composition</th>
                    <th width="60">Width/Dia</th>
                    <th width="120">Usage (Body Part)</th>
                    <th width="60">UOM</th>
                    <th width="80">Consumption</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Percentage</th>
                    <th width="60">Pre-cost Value</th>
                    <th width="60">Post-cost Value</th>
                    <th>Balance ($)</th>
            	</thead>
                <?
				$total_fab_amount=0;
				$total_pre_cost_value=0;
				$total_post_cost_value=0;
				$grand_total_pre_cost_value=0;
				$i=1;
                foreach($pre_cost_sql_result as $row )
                {
					$row[DIA_WIDTH]=implode(',',array_unique(explode(',',$row[DIA_WIDTH])));
					
					
					$post_cost_value=0;
					/*foreach(array_unique(explode(',',$row[PO_BREAK_DOWN_ID])) as $po_id){
						foreach(array_unique(explode(',',$row[COLOR_NUMBER_ID])) as $color_id){
							$post_cost_value=$piDataArr[$po_id.'**'.$row[DIA_WIDTH].'**'.$row[FB_COST_DTLS_ID].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][AMOUNT][$color_id];
						}
					}*/
					
					$post_cost_value=$piDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION].'**'.$row[BODY_PART_ID]][AMOUNT];
					
					
					
					$total_post_cost_value+=$post_cost_value;
					
					
					$pre_cost_value=0;
					foreach(explode(',',$jobDataArr[PO_ID]) as $po_id){
						$pre_cost_value+=array_sum($fabricdata[woven][grey][$po_id][$row[FB_COST_DTLS_ID]]);
						// $pre_cost_value+=array_sum($fabricdata[woven][finish][$po_id][$row[FB_COST_DTLS_ID]]);
					}
					$total_pre_cost_value+=$pre_cost_value;
					$total_fab_amount+=$row[AMOUNT];
					$grand_total_pre_cost_value+=$pre_cost_value; 
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td><?= $i; ?></td>
                    <td>Fabric Cost <? //echo implode(',',array_unique(explode(',',$row[COLOR_NUMBER_ID]))); ?></td>
                    <td><?= $row[CONSTRUCTION];?></td>
                    <td><?= $row[COMPOSITION];?></td>
                    <td align="center"><?= $row[DIA_WIDTH];?></td>
                    <td><?= $body_part[$row[BODY_PART_ID]]; ?></td>
                    <td align="center"><?= $unit_of_measurement[$row[UOM]]; ?></td>
                    <td align="center"><?= number_format($row[CONS],2);?></td>
                    <td align="center"><?= number_format($row[AMOUNT]/$row[CONS],4);?></td>
                    <td align="right"><?= $row[AMOUNT];?></td>
                    <td align="right"><?= number_format($percentage=($row[AMOUNT]/$unite_price)*100,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value,2);?></td>
                    <td align="right"><?= number_format($post_cost_value,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value-$post_cost_value,2);?></td>
                 </tr>
                <?
                $i++;
                }
                ?>
                  <tfoot>
                    <th colspan="9" align="right">Total Fabric Cost</th>
                    <th><?= number_format($total_fab_amount,2);?></th>
                    <th></th>
                    <th><?= number_format($total_pre_cost_value,2);?></th>
                    <th><?= number_format($total_post_cost_value,2);?></th>
                    <th><?= number_format($total_pre_cost_value-$total_post_cost_value,2);?></th>
                  </tfoot>
             </table>
                
               <!--FB part end--> 
                
            <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Department</th>
                    <th width="120">Accessories Type</th>
                    <th width="120">Item Group</th>
                    <th width="60">Item Description</th>
                    <th width="120">Placement</th>
                    <th width="60">UOM</th>
                    <th width="80">Consumption</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Percentage</th>
                    <th width="60">Pre-cost Value</th>
                    <th width="60">Post-cost Value</th>
                    <th>Balance ($)</th>
            	</thead>
                <?
				$total_trims_amount=0;$total_trims_pre_cost_value=0;$total_trims_post_cost_value=0;
				$i=1;
                foreach($trims_sql_result as $row )
                {
                    
					//$post_cost_value=array_sum($trimsPiDataArr[$row[ID].'**'.$row[TRIM_GROUP]][AMOUNT]);
					
					$post_cost_value=0;
					// foreach(array_unique(explode(',',$row[PO_BREAK_DOWN_ID])) as $po_id){
					// 	$post_cost_value+=array_sum($trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][AMOUNT]);
					// }

					
					$pre_cost_value=$trimsdata[$row[ID]];
					$post_cost_value=$trimsPiDataArr[$row[ID]][AMOUNT];
					
					$total_trims_pre_cost_value+=$pre_cost_value;
					$total_trims_amount+=$row[AMOUNT];
 					$total_trims_post_cost_value+=$post_cost_value;
					$grand_total_pre_cost_value+=$pre_cost_value;
					
					$row[PLACE]=implode(',',array_unique(explode(',',$row[PLACE])));
					
					
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trr_<? echo $i; ?>">
                    <td><?= $i; ?></td>
                    <td>Accessories </td>
                    <td><?= $trim_type[$row[TRIM_TYPE]];?></td>
                    <td><?= $row[ITEM_NAME];?></td>
                    <td align="center"><?= $row[DESCRIPTION];?></td>
                    <td><?= $row[PLACE];?></td>
                    <td align="center"><?= $unit_of_measurement[$row[CONS_UOM]]; ?></td>
                    <td align="center"><?= number_format($row[CONS],2);?></td>
                    <td align="center"><?= number_format($row[AMOUNT]/$row[CONS],4);?></td>
                    <td align="right"><?= $row[AMOUNT];?></td>
                    <td align="right"><?= number_format($percentage=($row[AMOUNT]/$unite_price)*100,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value,2);?></td>
                    <td align="right"><?= number_format($post_cost_value,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value-$post_cost_value,2);?></td>
                    </tr>
                <?
                $i++;
                }
                ?>
                  
                  <tfoot>
                    <th colspan="9" align="right">Total Accessoris Cost</th>
                    <th><?= number_format($total_trims_amount,2);?></th>
                    <th></th>
                    <th><?= number_format($total_trims_pre_cost_value,2);?></th>
                    <th><?= number_format($total_trims_post_cost_value,2);?></th>
                    <th><?= number_format($total_trims_pre_cost_value-$total_trims_post_cost_value,2);?></th>
                  </tfoot>
                
                </table>
                <!--Trims end-->
                
                <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Department</th>
                    <th width="120">Embellishment Name</th>
                    <th width="120">Type</th>
                    <th width="60">Description</th>
                    <th width="120">Body Part</th>
                    <th width="60">UOM</th>
                    <th width="80">Consumption</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Percentage</th>
                    <th width="60">Pre-cost Value</th>
                    <th width="60">Post-cost Value</th>
                    <th>Balance ($)</th>
                </thead>
                <?
				$total_emb_amount=0;$total_emb_pre_cost_value=0;$total_emb_post_cost_value=0;
				$i=1;
                foreach($emb_sql_result as $row )
                {
                    
					
					$post_cost_value=0;
					// foreach(explode(',',$jobDataArr[PO_ID]) as $po_id){
					// 	$post_cost_value+=array_sum($embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][AMOUNT]);
						
					// }

					$post_cost_value=$embPiDataArr[$row[ID]][AMOUNT];
					
					$pre_cost_value=$emblishmentdata[$row[ID]];
					
					if($pre_cost_value==''){
						$pre_cost_value=0;
						foreach(explode(',',$jobDataArr[PO_ID]) as $po_id){
							$pre_cost_value+=$washdata[$po_id][$row[ID]];
							
						}
					}
					
					
					
					
					$total_emb_pre_cost_value+=$pre_cost_value;
					$total_emb_amount+=$row[AMOUNT];
 					$total_emb_post_cost_value+=$post_cost_value;
					$grand_total_pre_cost_value+=$pre_cost_value;
					
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trrr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trrr_<? echo $i; ?>">
                    <td><?= $i; ?></td>
                    <td>Embellishment</td>
                    <td><?= $emblishment_name_array[$row[EMB_NAME]];?></td>
                    <td><?= $emblishment_print_type[$row[EMB_TYPE]];?></td>
                    <td align="center"></td>
                    <td><?= $body_part[$row[BODY_PART_ID]];?></td>
                    <td align="center"><?= $unit_of_measurement[1]; ?></td>
                    <td align="center"><?= number_format($row[CONS_DZN_GMTS],2);?></td>
                    <td align="center"><?= number_format($row[RATE],4);?></td>
                    <td align="right"><?= $row[AMOUNT];?></td>
                    <td align="right"><?= number_format($percentage=($row[AMOUNT]/$unite_price)*100,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value,2);?></td>
                    <td align="right"><?= number_format($post_cost_value,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value-$post_cost_value,2);?></td>
                    </tr>
                <?
                $i++;
                }
                ?>
                  
                  <tfoot>
                    <th colspan="9" align="right">Total Embellishment Cost</th>
                    <th><?= number_format($total_emb_amount,2);?></th>
                    <th></th>
                    <th><?= number_format($total_emb_pre_cost_value,2);?></th>
                    <th><?= number_format($total_emb_post_cost_value,2);?></th>
                    <th><?= number_format($total_emb_pre_cost_value-$total_emb_post_cost_value,2);?></th>
                  </tfoot>
                
                </table> 
                              
               </div> 
                
                <table class="rpt_table" width="<?= $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                  <tfoot>
                    <th width="35"></th>
                    <th width="120"></th>
                    <th width="120"></th>
                    <th width="120"></th>
                    <th width="60"></th>
                    <th width="120"></th>
                    <th width="60"></th>
                    <th width="80"></th>
                    <th width="60"></th>
                    <th width="60"><?= number_format($total_fab_amount+$total_trims_amount+$total_emb_amount,2);?></th>
                    <th width="60"></th>
                    <th width="60"><?= number_format($grand_total_pre_cost_value,2);?></th>
                    <th width="60"><?= number_format(($total_post_cost_value+$total_trims_post_cost_value+$total_emb_post_cost_value),2);?></th>
                    <th><?= number_format($grand_total_pre_cost_value-($total_post_cost_value+$total_trims_post_cost_value+$total_emb_post_cost_value),2);?></th>
                  </tfoot>
               </table>
            
            
            
            <br>
        <table><tr><td valign="top">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
            	<thead>
                	<th colspan="2">Summary</th>
                	<th>Pre Cost</th>
                	<th>Percent</th>
                	<th>Post Cost</th>
                	<th>Percent</th>
                </thead>
                <tbody> 
            	<tr>
                	
                	<td>Total Fabric cost</td>
                	<td width="35" align="right"></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][FABRIC_COST],2);?></td>
                	
                    <td align="right"><?= number_format((($pre_dtls_result[0][FABRIC_COST]/$pre_dtls_result[0][PRICE_DZN])*100),2);?></td>
                	<td align="right"><?= number_format($total_fb_post_cost=($total_post_cost_value/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                	<td align="right"><?= number_format($total_fb_post_cost/$pre_dtls_result[0][PRICE_DZN]*100,2);?> </td>
                </tr>
            	<tr>
                	<td>Total Acc Cost</td>
                	<td></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][TRIMS_COST],2);?></td>
                    <td align="right"><?= number_format((($pre_dtls_result[0][TRIMS_COST]/$pre_dtls_result[0][PRICE_DZN])*100),2);?></td>
                	<td align="right"><?= number_format($total_trims_post_cost=($total_trims_post_cost_value/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                	<td align="right"><?= number_format($total_trims_post_cost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Total Embellishment Cost</td>
                	<td></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][EMBEL_COST]+$pre_dtls_result[0][WASH_COST],2);?></td>
                    <td align="right"><?= number_format(((($pre_dtls_result[0][EMBEL_COST]+$pre_dtls_result[0][WASH_COST])/$pre_dtls_result[0][PRICE_DZN])*100),2);?></td>
                	<td align="right"><?= number_format($total_emb_post_cost=($total_emb_post_cost_value/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                	<td align="right"><?= number_format($total_emb_post_cost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Total Cost</td>
                	<td></td>
                	<td align="right"><?= number_format($grand_total_pre_cost=($pre_dtls_result[0][FABRIC_COST]+$pre_dtls_result[0][TRIMS_COST]+$pre_dtls_result[0][EMBEL_COST]+$pre_dtls_result[0][WASH_COST]),2);?></td>
                    <td align="right"></td>
                	<td align="right"><?= number_format($total_post_cost=($total_fb_post_cost+$total_trims_post_cost+$total_emb_post_cost),2);?></td>
                	<td align="right"></td>
                </tr>
            	<tr>
                	<td>Commercial Cost [All Together]</td>
                	<td align="right"><?= $commercial_cost_rate_arr[4];?> %</td>
                    <td align="right"><?= number_format($CommercialCost=($grand_total_pre_cost*$commercial_cost_rate_arr[4])/100,2);?></td>
                	<td align="right"><?= number_format($CommercialCost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><?= number_format($CommercialCost,2);?></td>
                	<td align="right"><?= number_format($CommercialCost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Profit Margin</td>
                	<td align="right"><?= number_format(($grand_total_pre_cost*$pre_dtls_result[0][MARGIN_PCS_SET]/100),2);?> %</td>
                	<td align="right"><?= $pre_dtls_result[0][MARGIN_PCS_SET];?></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][MARGIN_PCS_SET]/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][MARGIN_PCS_SET],4);?></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][MARGIN_PCS_SET]/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>CM</td>
                	<td align="right"></td>
                	<td align="right"><?
					 $NetPrice=$pre_dtls_result[0][PRICE_DZN]-((($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[1])+(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[2]));
					
					echo number_format($CM=$NetPrice-($pre_dtls_result[0][MARGIN_PCS_SET]+$CommercialCost+$grand_total_pre_cost),2);?></td>
                	<td align="right"><?= number_format($CM/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><?   $post_CM=$NetPrice-($pre_dtls_result[0][MARGIN_PCS_SET]+$CommercialCost+$total_post_cost); echo number_format($post_CM,2);?></td>
                	<td align="right"><?= number_format($post_CM/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Net Price</td>
                	<td align="right"></td>
                	<td align="right"><?= number_format($NetPrice,2);?></td>
                    <td></td>
                	<td align="right"><?= number_format($NetPrice,2);?></td>
                	<td align="right"></td>
                </tr>
            	<tr>
                	<td>L. Comm</td>
                	<td align="right"><?= number_format($pre_commision_rate_arr[2],2);?> %</td>
                	<td align="right"><?= number_format($l_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[2]),2);?></td>
                	<td align="right"><?= number_format($l_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><?= number_format($l_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[2]),2);?></td>
                	<td align="right"><?= number_format($l_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>F. Comm</td>
                	<td align="right"><?= number_format($pre_commision_rate_arr[1],2);?> %</td>
                	<td align="right"><?= number_format($f_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[1]),2);?></td>
                	<td align="right"><?= number_format($f_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><?= number_format($f_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[1]),2);?></td>
                	<td align="right"><?= number_format($f_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td height="25">FOB Price</td>
       	      		<td align="right"></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][PRICE_DZN],2);?></td>
                	<td align="right">100%</td>
                	<td align="right"><?= number_format($pre_dtls_result[0][PRICE_DZN],2);?></td>
                	<td align="right">100%</td>
                </tr>
              </tbody>
            </table>
            <br>
            
            <table cellspacing="5" cellpadding="5" width="300" border="1" rules="all" class="rpt_table" id="table_body">
                <tr>
                    <td>Sales Value (shipment qty x FOB)</td>
                    <td align="right">$ <?= $CM_earned=$ship_qty*$pre_dtls_result[0][PRICE_DZN];?></td>
                </tr>
                <tr>
                    <td>CM earned (sales value - PI value)</td>
                    <td align="right">$ <?= $ship_qty*$CM;?></td>
                </tr> 
                <tr>
                    <td>Post CM (CM earned / Order qty)</td>
                    <td align="right">$ <?= $CM_earned/$jobDataArr[PO_QUANTITY_PCS];?></td>
                </tr> 
            </table>     
        </td>
        <td width="30"></td>
        <td valign="top">
            
            <table><tr><td>
            <?php foreach($img_arr as $img){?>
                <img src="../../../<?=$img;?>" width="200">
            <?php } ?>
            </td></tr></table>           
       </td></tr></table>
            </fieldset>
        </div>
	<?
	
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	ob_clean();
	
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	
	$filename="../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	echo "$html****$filename";
	
	disconnect($con);
	exit();	
}

if( $action=="report_generate_show2" )
{
	
	require_once('../../../../mailer/class.phpmailer.php');
	require_once('../../../../includes/class4/class.conditions.php');
	require_once('../../../../includes/class4/class.reports.php');
	require_once('../../../../includes/class4/class.fabrics.php');
	require_once('../../../../includes/class4/class.yarns.php');
	require_once('../../../../includes/class4/class.washes.php');
	require_once('../../../../includes/class4/class.emblishments.php');
	require_once('../../../../includes/class4/class.trims.php');
	require_once('../../../../includes/class4/class.commercials.php');


	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	
	
	
		$condition= new condition();
		if($company_name>0){
			$condition->company_name("=$company_name");
		}
		if($cbo_buyer_name>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if($tna_process_start_date !=''){
			$condition->pub_shipment_date(" > '".$tna_process_start_date."'");
		}
		
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no(" = '".str_replace("'","",$txt_job_no)."'");
		}
	
		
		if(trim($txt_ponumber_id) !=''){
			$condition->po_id(" in($txt_ponumber_id) ");
		}
		$condition->init();
		
		
		
		$fabric= new fabric($condition);
		$fabricdata=$fabric->getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
 		
		$trims= new trims($condition);
		$trimsdata=$trims->getAmountArray_precostdtlsid();
		 //print_r($trimsdata);die;
		
		$emblishment= new emblishment($condition);
		$emblishmentdata=$emblishment->getAmountArray_by_emblishmentid();
		
		$wash= new wash($condition);
		//$washdata=$wash->getAmountArray_by_emblishmentid();
		//$emblishmentdata=$emblishmentdata+$washdata;
		
		$washdata=$wash->getAmountArray_by_orderAndEmblishmentid();
		// print_r($washdata);die;
 	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$bank_lib=return_library_array( "select id, bank_name from lib_bank", "id", "bank_name"  );
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $where_con.=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	elseif($cbo_buyer_name!=0) $where_con.=" and a.buyer_name=$cbo_buyer_name";
	
	
	if($db_type==0)
	{
		if($cbo_year!=0) $where_con.=" and year(a.insert_date)=".$cbo_year.""; 
	}
	else
	{
		if($cbo_year!=0) $where_con.=" and to_char(a.insert_date,'YYYY')=".$cbo_year."";
	}
	
	if($txt_job_no!=""){
		$where_con.="and b.job_no='".$txt_job_no."'";
		$where_con2.="and a.job_no='".$txt_job_no."'";
	}
	

	if($db_type==0){
		$PO_ID="group_concat( distinct b.ID) AS PO_ID";
		$PO_NUMBER="group_concat( distinct b.PO_NUMBER) AS PO_NUMBER";
	}
	if($db_type==2){
		$PO_ID="listagg(b.ID ,',') within group (order by b.ID) AS PO_ID";
		$PO_NUMBER="listagg(b.PO_NUMBER ,',') within group (order by b.PO_NUMBER) AS PO_NUMBER";
	}
	
	//JObo..........
	$job_sql="select  a.SET_SMV,A.JOB_NO,A.BUYER_NAME,a.STYLE_REF_NO,a.GMTS_ITEM_ID,$PO_ID,$PO_NUMBER,SUM(B.PLAN_CUT) AS PLAN_CUT, SUM(A.TOTAL_SET_QNTY*B.PO_QUANTITY) as PO_QUANTITY_PCS,SUM(B.PO_TOTAL_PRICE) AS AMOUNT,MAX(PUB_SHIPMENT_DATE) AS SHIPMENT_DATE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  and a.is_deleted=0 and b.is_deleted=0 $where_con2 GROUP BY A.JOB_NO,a.SET_SMV,A.BUYER_NAME,a.STYLE_REF_NO,a.GMTS_ITEM_ID";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $rows){
		$jobDataArr=$rows;
	}
	
	//Exfactory
	$ex_factory_arr=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id in(".$jobDataArr[PO_ID].") group by po_break_down_id", "po_break_down_id", "qnty");	
	
	
    //Precost........................
   //$pre_cost_sql="select b.ID as FB_COST_DTLS_ID,A.GARMENTS_NATURE,b.JOB_NO,b.CONSTRUCTION,b.COMPOSITION,B.GSM_WEIGHT,B.BODY_PART_ID,B.UOM, B.RATE,B.AMOUNT,B.AVG_CONS  from WO_PRE_COST_MST a,WO_PRE_COST_FABRIC_COST_DTLS b where A.JOB_ID=B.JOB_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 $where_con";
   
	if($db_type==0){
		$DIA_WIDTH="group_concat( distinct c.DIA_WIDTH) AS DIA_WIDTH";
		$PO_BREAK_DOWN_ID="group_concat( distinct c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
		$COLOR_NUMBER_ID="group_concat( distinct c.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID";
	}
	if($db_type==2){
		$DIA_WIDTH="listagg(c.DIA_WIDTH ,',') within group (order by c.DIA_WIDTH) AS DIA_WIDTH";
		$PO_BREAK_DOWN_ID="listagg(c.PO_BREAK_DOWN_ID ,',') within group (order by c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
		$COLOR_NUMBER_ID="listagg(c.COLOR_NUMBER_ID ,',') within group (order by c.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID";
	}

   $pre_cost_sql="SELECT b.ID AS FB_COST_DTLS_ID,
         A.GARMENTS_NATURE,
         b.JOB_NO,
		 b.BODY_PART_ID,
		 b.LIB_YARN_COUNT_DETER_ID,
         b.CONSTRUCTION,
         b.COMPOSITION,
         B.GSM_WEIGHT,
         B.BODY_PART_ID,
         B.UOM,
         $DIA_WIDTH,
		 $PO_BREAK_DOWN_ID,
		 $COLOR_NUMBER_ID,
         b.AMOUNT as AMOUNT,
         b.AVG_FINISH_CONS as CONS
    FROM WO_PRE_COST_MST a,
         WO_PRE_COST_FABRIC_COST_DTLS b,
         WO_PRE_COS_FAB_CO_AVG_CON_DTLS c
   WHERE     A.JOB_ID = B.JOB_ID and b.id=c.PRE_COST_FABRIC_COST_DTLS_ID
         AND b.JOB_ID = c.JOB_ID
         AND A.IS_DELETED = 0
         AND A.STATUS_ACTIVE = 1
         AND b.IS_DELETED = 0
         AND b.STATUS_ACTIVE = 1
         AND c.IS_DELETED = 0
         AND c.STATUS_ACTIVE = 1 and c.CONS !=0
         $where_con
	GROUP BY b.ID,
         A.GARMENTS_NATURE,
         b.JOB_NO,
		 b.BODY_PART_ID,
		 b.LIB_YARN_COUNT_DETER_ID,
         b.CONSTRUCTION,
         b.COMPOSITION,
         B.GSM_WEIGHT,
         B.BODY_PART_ID,
         B.UOM,
         b.AMOUNT,b.AVG_FINISH_CONS";
   
   
	  //echo $pre_cost_sql;die; 
	$pre_cost_sql_result=sql_select($pre_cost_sql);
	$fbCostIdArr=array();
	foreach($pre_cost_sql_result as $rows){
		$fbCostIdArr[$rows[FB_COST_DTLS_ID]]=$rows[FB_COST_DTLS_ID];
		$fbIdArr[$rows[FB_COST_DTLS_ID]]=$rows[FB_COST_DTLS_ID];
	}
	//echo $pre_cost_sql;
	
	if($db_type==0){
		$PLACE="group_concat( distinct c.PLACE) AS PLACE";
		$PO_BREAK_DOWN_ID="group_concat( distinct c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
	}
	if($db_type==2){
		$PLACE="listagg(c.PLACE ,',') within group (order by c.PLACE) AS PLACE";
		$PO_BREAK_DOWN_ID="listagg(c.PO_BREAK_DOWN_ID ,',') within group (order by c.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID";
	}
	//Trims........................
   $trims_sql="SELECT a.ID,a.TRIM_GROUP,
         a.DESCRIPTION,
         a.CONS_UOM,
		 b.ITEM_NAME,
         b.TRIM_TYPE,
         a.CONS_DZN_GMTS AS CONS,
         a.AMOUNT,
		 $PLACE,
		 $PO_BREAK_DOWN_ID
    FROM WO_PRE_COST_TRIM_COST_DTLS a,
         LIB_ITEM_GROUP b,
         WO_PRE_COST_TRIM_CO_CONS_DTLS c
   WHERE     A.TRIM_GROUP = b.id
         AND A.JOB_NO = C.JOB_NO
         AND a.id = c.WO_PRE_COST_TRIM_COST_DTLS_ID
         AND A.JOB_NO ='".$txt_job_no."'
         AND a.IS_DELETED = 0
         AND b.IS_DELETED = 0
         AND a.STATUS_ACTIVE = 1
         AND b.STATUS_ACTIVE = 1  and C.CONS !=0
	GROUP BY a.ID,
		 a.TRIM_GROUP,
         a.DESCRIPTION,
         a.CONS_UOM,
		 a.CONS_DZN_GMTS,
         a.AMOUNT,
		 b.ITEM_NAME,
         b.TRIM_TYPE";
	    //echo $trims_sql;die;
	$trims_sql_result=sql_select($trims_sql);
	foreach($trims_sql_result as $rows){
		 $fbCostIdArr[$rows[ID]]=$rows[ID];
	}
 
	//Emblishment........................
   $emb_sql="select a.ID,a.EMB_NAME,a.EMB_TYPE,a.BODY_PART_ID,a.CONS_DZN_GMTS,a.RATE,a.AMOUNT from WO_PRE_COST_EMBE_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'";
	// echo $pi_sql;
	$emb_sql_result=sql_select($emb_sql);	
	foreach($emb_sql_result as $rows){
		 $fbCostIdArr[$rows[ID]]=$rows[ID];
	}
	$post_trim_sql="select  a.PRE_COST_FABRIC_COST_DTLS_ID, sum(A.AMOUNT) as AMOUNT from  WO_BOOKING_DTLS a where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].") group by a.PRE_COST_FABRIC_COST_DTLS_ID";
    // echo $post_trim_sql;
	$post_trim_result=sql_select($post_trim_sql);
    $trimsPiDataArr=array();
	foreach($post_trim_result as $rows){
		$trimsPiDataArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]][AMOUNT]=$rows[AMOUNT];
	}

	$post_emb_sql="select  a.PRE_COST_FABRIC_COST_DTLS_ID, sum(B.AMOUNT) as AMOUNT from  WO_BOOKING_DTLS a,WO_EMB_BOOK_CON_DTLS b  where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.id=b.wo_booking_dtls_id and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].") group by a.PRE_COST_FABRIC_COST_DTLS_ID";
    // echo $post_emb_sql;
	$post_emb_result=sql_select($post_emb_sql);
    $embPiDataArr=array();
	foreach($post_emb_result as $rows){
		$embPiDataArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]][AMOUNT]=$rows[AMOUNT];
	}
	
	
	//pi......................
    $pi_sql="select  b.PI_ID,a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,A.JOB_NO,b.FABRIC_CONSTRUCTION as CONSTRUCTION,b.FABRIC_COMPOSITION as COMPOSITION,b.DIA_WIDTH,B.UOM,B.RATE,B.AMOUNT,B.COLOR_ID, b.ITEM_GROUP, b.GMTS_ITEM_ID,  b.EMBELL_NAME,  b.EMBELL_TYPE,b.PI_ID , c.PI_NUMBER,c.SUPPLIER_ID
    from WO_BOOKING_DTLS a,COM_PI_ITEM_DETAILS  b, COM_PI_MASTER_DETAILS c
    where  A.BOOKING_NO=B.WORK_ORDER_NO and c.ID=b.PI_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].")";
	
	//  echo $pi_sql;
	$pi_sql_result=sql_select($pi_sql);
	// $piDataArr=array();$embPiDataArr=array();$trimsPiDataArr=array();
    $noPiDataArr=array();
	foreach($pi_sql_result as $rows){
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][PI_NUMBER]=$rows[PI_NUMBER];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][SUPPLIER_ID]=$rows[SUPPLIER_ID];

		// $trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][AMOUNT][$rows[COLOR_ID]]=$rows[AMOUNT];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][PI_NUMBER]=$rows[PI_NUMBER];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][SUPPLIER_ID]=$rows[SUPPLIER_ID];

        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][PI_NUMBER]=$rows[PI_NUMBER];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][SUPPLIER_ID]=$rows[SUPPLIER_ID];

	}

    $btb_sql="select  b.PI_ID,a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,A.JOB_NO,b.FABRIC_CONSTRUCTION as CONSTRUCTION,b.FABRIC_COMPOSITION as COMPOSITION,b.DIA_WIDTH,B.UOM,B.RATE,B.AMOUNT,B.COLOR_ID, b.ITEM_GROUP, b.GMTS_ITEM_ID,  b.EMBELL_NAME,  b.EMBELL_TYPE,b.PI_ID , c.PI_NUMBER,c.SUPPLIER_ID, d.ISSUING_BANK_ID,d.LC_DATE as B2B_LC_DATE, d.LC_NUMBER as B2B_LC_NUMBER, d.LC_VALUE as B2B_LC_VALUE, d.REMARKS as B2B_REMARKS, g.EXPORT_LC_NO as SC_LC_NO, g.PAY_TERM, g.SHIPPING_MODE, g.INCO_TERM
    from WO_BOOKING_DTLS a,COM_PI_ITEM_DETAILS  b, COM_PI_MASTER_DETAILS c,COM_BTB_LC_MASTER_DETAILS d,COM_BTB_LC_PI e,COM_BTB_EXPORT_LC_ATTACHMENT f, COM_EXPORT_LC g
    where  A.BOOKING_NO=B.WORK_ORDER_NO and c.ID=b.PI_ID and c.ID=e.PI_ID and d.ID=e.COM_BTB_LC_MASTER_DETAILS_ID and d.ID=f.IMPORT_MST_ID and  f.LC_SC_ID=g.ID and f.IS_LC_SC=0 and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].")
    UNION ALL
    select  b.PI_ID,a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,A.JOB_NO,b.FABRIC_CONSTRUCTION as CONSTRUCTION,b.FABRIC_COMPOSITION as COMPOSITION,b.DIA_WIDTH,B.UOM,B.RATE,B.AMOUNT,B.COLOR_ID, b.ITEM_GROUP, b.GMTS_ITEM_ID,  b.EMBELL_NAME,  b.EMBELL_TYPE,b.PI_ID , c.PI_NUMBER,c.SUPPLIER_ID, d.ISSUING_BANK_ID,d.LC_DATE as B2B_LC_DATE, d.LC_NUMBER as B2B_LC_NUMBER, d.LC_VALUE as B2B_LC_VALUE, d.REMARKS as B2B_REMARKS, g.CONTRACT_NO as SC_LC_NO, g.PAY_TERM, g.SHIPPING_MODE, g.INCO_TERM
    from WO_BOOKING_DTLS a,COM_PI_ITEM_DETAILS  b, COM_PI_MASTER_DETAILS c,COM_BTB_LC_MASTER_DETAILS d,COM_BTB_LC_PI e,COM_BTB_EXPORT_LC_ATTACHMENT f, COM_SALES_CONTRACT g
    where  A.BOOKING_NO=B.WORK_ORDER_NO  and c.ID=b.PI_ID and c.ID=e.PI_ID and d.ID=e.COM_BTB_LC_MASTER_DETAILS_ID and d.ID=f.IMPORT_MST_ID   and f.LC_SC_ID=g.ID and f.IS_LC_SC=1 and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].")";
	
	//  echo $pi_sql;
	$btb_sql_result=sql_select($btb_sql);
	// $piDataArr=array();$embPiDataArr=array();$trimsPiDataArr=array();
    // $noPiDataArr=array();
	foreach($btb_sql_result as $rows){
		// $embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][PI_NUMBER]=$rows[PI_NUMBER];
		// $embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][ISSUING_BANK_ID]=$rows[ISSUING_BANK_ID];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][B2B_LC_NUMBER]=$rows[B2B_LC_NUMBER];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][B2B_LC_VALUE]=$rows[B2B_LC_VALUE];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][B2B_LC_DATE]=$rows[B2B_LC_DATE];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][B2B_REMARKS]=$rows[B2B_REMARKS];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][SC_LC_NO]=$rows[SC_LC_NO];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][SHIPPING_MODE]=$rows[SHIPPING_MODE];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][INCO_TERM]=$rows[INCO_TERM];
		$embPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE]][PAY_TERM]=$rows[PAY_TERM];

		// $trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][AMOUNT][$rows[COLOR_ID]]=$rows[AMOUNT];
		// $trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][PI_NUMBER]=$rows[PI_NUMBER];
		// $trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][ISSUING_BANK_ID]=$rows[ISSUING_BANK_ID];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][B2B_LC_NUMBER]=$rows[B2B_LC_NUMBER];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][B2B_LC_VALUE]=$rows[B2B_LC_VALUE];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][B2B_LC_DATE]=$rows[B2B_LC_DATE];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][B2B_REMARKS]=$rows[B2B_REMARKS];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][SC_LC_NO]=$rows[SC_LC_NO];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][SHIPPING_MODE]=$rows[SHIPPING_MODE];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][INCO_TERM]=$rows[INCO_TERM];
		$trimsPiDataArr[$rows[PO_BREAK_DOWN_ID].'**'.$rows[ITEM_GROUP]][PAY_TERM]=$rows[PAY_TERM];

        // $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][PI_NUMBER]=$rows[PI_NUMBER];
        // $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][SUPPLIER_ID]=$rows[SUPPLIER_ID];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][ISSUING_BANK_ID]=$rows[ISSUING_BANK_ID];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][B2B_LC_NUMBER]=$rows[B2B_LC_NUMBER];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][B2B_LC_VALUE]=$rows[B2B_LC_VALUE];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][B2B_LC_DATE]=$rows[B2B_LC_DATE];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][B2B_REMARKS]=$rows[B2B_REMARKS];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][SC_LC_NO]=$rows[SC_LC_NO];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][SHIPPING_MODE]=$rows[SHIPPING_MODE];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][INCO_TERM]=$rows[INCO_TERM];
        $noPiDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COMPOSITION]][PAY_TERM]=$rows[PAY_TERM];
		
		if($fbIdArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]]){
			$fbPIArr[$rows[PI_ID]]=$rows[PI_ID];
		}
		
	}
  
 
 
    //pi value for fabric......................
    //    $pi_sql="select  b.FABRIC_CONSTRUCTION as CONSTRUCTION,b.FABRIC_COMPOSITION as COPMPOSITION,b.DIA_WIDTH,B.UOM,B.RATE,B.AMOUNT,B.COLOR_ID, b.ITEM_GROUP, b.GMTS_ITEM_ID, b.EMBELL_NAME, b.EMBELL_TYPE,b.PI_ID from COM_PI_ITEM_DETAILS b where B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and b.PI_ID in(".implode(',',$fbPIArr).")";
    $pi_sql="select  A.CONSTRUCTION,A.COPMPOSITION,A.DIA_WIDTH,A.PRE_COST_FABRIC_COST_DTLS_ID,B.BODY_PART_ID, sum(A.AMOUNT) as AMOUNT from  WO_BOOKING_DTLS a,WO_PRE_COST_FABRIC_COST_DTLS b where a.PRE_COST_FABRIC_COST_DTLS_ID=b.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".$jobDataArr[PO_ID].") group by A.CONSTRUCTION,A.COPMPOSITION,A.DIA_WIDTH,a.PRE_COST_FABRIC_COST_DTLS_ID,B.BODY_PART_ID";
	 // echo $pi_sql;
	$pi_sql_result=sql_select($pi_sql);
	$piDataArr=array(); 
	foreach($pi_sql_result as $rows){
		$piDataArr[$rows[DIA_WIDTH].'**'.$rows[CONSTRUCTION].'**'.$rows[COPMPOSITION].'**'.$rows[BODY_PART_ID]][AMOUNT]+=$rows[AMOUNT];
	}
 //..............................end;
 
 
   //Precost dtls..........................
	$pre_dtls="select a.PRICE_DZN,a.MARGIN_PCS_SET,a.FABRIC_COST,a.TRIMS_COST,a.EMBEL_COST,a.WASH_COST,a.CM_COST from WO_PRE_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'"; 
	$pre_dtls_result=sql_select($pre_dtls);


   //commision rate..........................
	$pre_commision_rate_arr=return_library_array( "select a.PARTICULARS_ID,a.COMMISION_RATE from WO_PRE_COST_COMMISS_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'", "PARTICULARS_ID", "COMMISION_RATE");	
	$pre_commision_amount_arr=return_library_array( "select a.PARTICULARS_ID,a.COMMISSION_AMOUNT from WO_PRE_COST_COMMISS_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'", "PARTICULARS_ID", "COMMISSION_AMOUNT");	
	
   //commision rate..........................
	$commercial_cost_rate_arr=return_library_array( "select a.ITEM_ID,a.RATE from WO_PRE_COST_COMARCI_COST_DTLS a where  A.STATUS_ACTIVE=1 and a.IS_DELETED=0 and JOB_NO ='".$txt_job_no."'", "ITEM_ID", "RATE");	
	
   //Image..........................
   	$img_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where  MASTER_TBLE_ID ='".$txt_job_no."'  and FORM_NAME='knit_order_entry'", "ID", "IMAGE_LOCATION");	
 	
	$width=2000;
	
		ob_start();
	?>
        <div style="width:<?= $width;?>px">
        <fieldset style="width:100%;">
          <table width="<?= $width;?>">
              <tr class="form_caption">
                    <td colspan="14" align="center" style="font-size:18px;"><?= $report_title;?></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="14" align="center" style="font-size:15px;"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
            <br />
            <table width="" cellpadding="5" cellspacing="5" border="0">
                <tr>
                    <td width="70"><strong>Job No</strong></td><th>:</th>
                    <td width="120"><?= $jobDataArr[JOB_NO];?></td>
                    <td width="110"><strong>Shipment Date</strong></td><th>:</th>
                    <td width="120"><?= change_date_format($jobDataArr[SHIPMENT_DATE]);?></td>
                    <td width="80"><strong>Costing SMV</strong></td><th>:</th>
                    <td width="120"><?= $jobDataArr[SET_SMV];?></td>
               </tr>
                <tr>
                    <td><strong>Buyer</strong></td><th>:</th>
                    <td><?= $buyer_lib[$jobDataArr[BUYER_NAME]];?></td>
                    <td><strong>Order QTY (PCS)</strong></td><th>:</th>
                    <td><?= $jobDataArr[PO_QUANTITY_PCS];?></td>
                    <td><strong>Prod SMV</strong></td><th>:</th>
                    <td></td>
               </tr>
               
                <tr>
                    <td><strong>Style</strong></td><th>:</th>
                    <td><?= $jobDataArr[STYLE_REF_NO];?></td>
                    <td><strong>Shipment QTY (PCS)</strong></td><th>:</th>
                    <td><?= $ship_qty=array_sum($ex_factory_arr)*1;?></td>
                    <td><strong>Plan Cut Qty</strong></td><th>:</th>
                    <td><?= $jobDataArr[PLAN_CUT];?></td>
               </tr>
               
                <tr>
                    <td><strong>PO Number</strong></td><th>:</th>
                    <td><p><?= $jobDataArr[PO_NUMBER];?></p></td>
                    <td><strong>Unit Price</strong></td><th>:</th>
                    <td><?= number_format($unite_price=($jobDataArr[AMOUNT]/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                    <td><strong>Garments Item</strong></td><th>:</th>
                    <td><?= $garments_item[$jobDataArr[GMTS_ITEM_ID]];?></td>
               </tr>
            </table>
            <br />
            
            
            <div style="width:<?= $width+20;?>px;" id="scroll_body">
            
            <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Department</th>
                    <th width="120">Construction</th>
                    <th width="120">Composition</th>
                    <th width="60">Width/Dia</th>
                    <th width="120">Usage (Body Part)</th>
                    <th width="60">UOM</th>
                    <th width="80">Consumption</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Percentage</th>
                    <th width="60">Order Qty</th>
                    <th width="60">Pre-cost Value</th>
                    <th width="60">Post-cost Value</th>
                    <th width="60">Balance ($)</th>
                    <th width="80">PI No</th>
                    <th width="80">Supplier Name</th>
                    <th width="80">Payment Terms</th>
                    <th width="80">Shipment Terms</th>
                    <th width="80">Master LC No</th>
                    <th width="80">Bank Name</th>
                    <th width="80">B2B LC No</th>
                    <th width="90">B2B LC Value ($)</th>
                    <th width="80">BB LC Open Date</th>
                    <th >Remarks On Add Req</th>
            	</thead>
                <?
				$total_fab_amount=0;
				$total_pre_cost_value=0;
				$total_post_cost_value=0;
				$grand_total_pre_cost_value=0;
				$i=1;
                foreach($pre_cost_sql_result as $row )
                {
					$row[DIA_WIDTH]=implode(',',array_unique(explode(',',$row[DIA_WIDTH])));
					
					
					$post_cost_value=0;
					/*foreach(array_unique(explode(',',$row[PO_BREAK_DOWN_ID])) as $po_id){
						foreach(array_unique(explode(',',$row[COLOR_NUMBER_ID])) as $color_id){
							$post_cost_value=$piDataArr[$po_id.'**'.$row[DIA_WIDTH].'**'.$row[FB_COST_DTLS_ID].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][AMOUNT][$color_id];
						}
					}*/
					
					$post_cost_value=$piDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION].'**'.$row[BODY_PART_ID]][AMOUNT];
					$pi_number=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][PI_NUMBER];
					$supp_nam=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][SUPPLIER_ID];
					$bank_id=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][ISSUING_BANK_ID];
					$b2b_lc_number=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][B2B_LC_NUMBER];
					$b2b_lc_value=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][B2B_LC_VALUE];
					$b2b_lc_date=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][B2B_LC_DATE];
					$sc_lc_no=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][SC_LC_NO];
					$shipping_id=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][SHIPPING_MODE];
					$inco_term=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][INCO_TERM];
                    $pay_id=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][PAY_TERM];
                    $remarks=$noPiDataArr[$row[DIA_WIDTH].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION]][B2B_REMARKS];

					$total_post_cost_value+=$post_cost_value;
				
					$pre_cost_value=0;
					foreach(explode(',',$jobDataArr[PO_ID]) as $po_id){
						$pre_cost_value+=array_sum($fabricdata[woven][finish][$po_id][$row[FB_COST_DTLS_ID]]);
						// $pre_cost_value+=array_sum($fabricdata[woven][grey][$po_id][$row[FB_COST_DTLS_ID]]);
						
					}
					$total_pre_cost_value+=$pre_cost_value;
					$total_fab_amount+=$row[AMOUNT];
					$grand_total_pre_cost_value+=$pre_cost_value; 
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td><?= $i; ?></td>
                    <td>Fabric Cost <? //echo implode(',',array_unique(explode(',',$row[COLOR_NUMBER_ID]))); ?></td>
                    <td><?= $row[CONSTRUCTION];?></td>
                    <td><?= $row[COMPOSITION];?></td>
                    <td align="center"><?= $row[DIA_WIDTH];?></td>
                    <td><?= $body_part[$row[BODY_PART_ID]]; ?></td>
                    <td align="center"><?= $unit_of_measurement[$row[UOM]]; ?></td>
                    <td align="center"><?= number_format($row[CONS],2);?></td>
                    <td align="center"><?= number_format($row[AMOUNT]/$row[CONS],4);?></td>
                    <td align="right"><?= $row[AMOUNT];?></td>
                    <td align="right"><?= number_format($percentage=($row[AMOUNT]/$unite_price)*100,2);?></td>
                    <td align="right"><?= $jobDataArr[PO_QUANTITY_PCS];?></td>
                    <td align="right"><?= number_format($pre_cost_value,2);?></td>
                    <td align="right"><?= number_format($post_cost_value,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value-$post_cost_value,2);?></td>
                    <td ><? echo $pi_number;?></td>
                    <td ><? echo $supplier_lib[$supp_nam];?></td>
                    <td ><? echo $pay_term[$pay_id];?></td>
                    <td ><? echo $shipment_mode[$shipping_id].', '.$incoterm[$inco_term];?></td>
                    <td ><? echo $sc_lc_no;?></td>
                    <td ><? echo $bank_lib[$bank_id];?></td>
                    <td align="center"><? echo $b2b_lc_number;?></td>
                    <td align="right" ><? echo number_format($b2b_lc_value,2);?></td>
                    <td align="center"><? echo change_date_format($b2b_lc_date);?></td>
                    <td ><? echo $remarks;?></td>
                 </tr>
                <?
                $i++;
                }
                ?>
                  <tfoot>
                    <th colspan="9" align="right">Total Fabric Cost</th>
                    <th><?= number_format($total_fab_amount,2);?></th>
                    <th></th>
                    <th></th>
                    <th><?= number_format($total_pre_cost_value,2);?></th>
                    <th><?= number_format($total_post_cost_value,2);?></th>
                    <th><?= number_format($total_pre_cost_value-$total_post_cost_value,2);?></th>
                  </tfoot>
             </table>
                
               <!--FB part end--> 
                
            <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Department</th>
                    <th width="120">Accessories Type</th>
                    <th width="120">Item Group</th>
                    <th width="60">Item Description</th>
                    <th width="120">Placement</th>
                    <th width="60">UOM</th>
                    <th width="80">Consumption</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Percentage</th>
                    <th width="60">Order Qty</th>
                    <th width="60">Pre-cost Value</th>
                    <th width="60">Post-cost Value</th>
                    <th width="60">Balance ($)</th>
					<th width="80">PI No</th>
                    <th width="80">Supplier Name</th>
                    <th width="80">Payment Terms</th>
                    <th width="80">Shipment Terms</th>
                    <th width="80">Master LC No</th>
                    <th width="80">Bank Name</th>
                    <th width="80">B2B LC No</th>
                    <th width="90">B2B LC Value ($)</th>
                    <th width="80">BB LC Open Date</th>
                    <th >Remarks On Add Req</th>
            	</thead>
                <?
				$total_trims_amount=0;$total_trims_pre_cost_value=0;$total_trims_post_cost_value=0;
				$i=1;
                foreach($trims_sql_result as $row )
                {
                    
					//$post_cost_value=array_sum($trimsPiDataArr[$row[ID].'**'.$row[TRIM_GROUP]][AMOUNT]);
					
					$post_cost_value=0;
					foreach(array_unique(explode(',',$row[PO_BREAK_DOWN_ID])) as $po_id){
						// $post_cost_value+=array_sum($trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][AMOUNT]);
						$pi_number=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][PI_NUMBER];
						$supp_nam=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][SUPPLIER_ID];
						$pay_id=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][PAY_TERM];
						$bank_id=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][ISSUING_BANK_ID];
						$b2b_lc_number=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][B2B_LC_NUMBER];
						$b2b_lc_value=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][B2B_LC_VALUE];
						$b2b_lc_date=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][B2B_LC_DATE];
						$sc_lc_no=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][SC_LC_NO];
						$shipping_id=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][SHIPPING_MODE];
						$inco_term=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][INCO_TERM];
						$remarks=$trimsPiDataArr[$po_id.'**'.$row[TRIM_GROUP]][B2B_REMARKS];
					}
                    $post_cost_value=$trimsPiDataArr[$row[ID]][AMOUNT];
					$pre_cost_value=$trimsdata[$row[ID]];
					
					$total_trims_pre_cost_value+=$pre_cost_value;
					$total_trims_amount+=$row[AMOUNT];
 					$total_trims_post_cost_value+=$post_cost_value;
					$grand_total_pre_cost_value+=$pre_cost_value;
					
					$row[PLACE]=implode(',',array_unique(explode(',',$row[PLACE])));
					
					
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trr_<? echo $i; ?>">
                    <td><?= $i; ?></td>
                    <td>Accessories </td>
                    <td><?= $trim_type[$row[TRIM_TYPE]];?></td>
                    <td><?= $row[ITEM_NAME];?></td>
                    <td align="center"><?= $row[DESCRIPTION];?></td>
                    <td><?= $row[PLACE];?></td>
                    <td align="center"><?= $unit_of_measurement[$row[CONS_UOM]]; ?></td>
                    <td align="center"><?= number_format($row[CONS],2);?></td>
                    <td align="center"><?= number_format($row[AMOUNT]/$row[CONS],4);?></td>
                    <td align="right"><?= $row[AMOUNT];?></td>
                    <td align="right"><?= number_format($percentage=($row[AMOUNT]/$unite_price)*100,2);?></td>
                    <td align="right"><?= $jobDataArr[PO_QUANTITY_PCS];?></td>
                    <td align="right"><?= number_format($pre_cost_value,2);?></td>
                    <td align="right"><?= number_format($post_cost_value,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value-$post_cost_value,2);?></td>
                    <td ><? echo $pi_number;?></td>
                    <td ><? echo $supplier_lib[$supp_nam];?></td>
                    <td ><? echo $pay_term[$pay_id];?></td>
                    <td ><? echo $shipment_mode[$shipping_id].', '.$incoterm[$inco_term];?></td>
                    <td ><? echo $sc_lc_no;?></td>
                    <td ><? echo $bank_lib[$bank_id];?></td>
                    <td align="center"><? echo $b2b_lc_number;?></td>
                    <td align="right" ><? echo number_format($b2b_lc_value,2);?></td>
                    <td align="center"><? echo change_date_format($b2b_lc_date);?></td>
                    <td ><? echo $remarks;?></td>
                    </tr>
                <?
                $i++;
                }
                ?>
                  
                  <tfoot>
                    <th colspan="9" align="right">Total Accessoris Cost</th>
                    <th><?= number_format($total_trims_amount,2);?></th>
                    <th></th>
                    <th></th>
                    <th><?= number_format($total_trims_pre_cost_value,2);?></th>
                    <th><?= number_format($total_trims_post_cost_value,2);?></th>
                    <th><?= number_format($total_trims_pre_cost_value-$total_trims_post_cost_value,2);?></th>
                  </tfoot>
                
                </table>
                <!--Trims end-->
                
                <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Department</th>
                    <th width="120">Embellishment Name</th>
                    <th width="120">Type</th>
                    <th width="60">Description</th>
                    <th width="120">Body Part</th>
                    <th width="60">UOM</th>
                    <th width="80">Consumption</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Percentage</th>
                    <th width="60">Order Qty</th>
                    <th width="60">Pre-cost Value</th>
                    <th width="60">Post-cost Value</th>
                    <th width="60">Balance ($)</th>
					<th width="80">PI No</th>
                    <th width="80">Supplier Name</th>
                    <th width="80">Payment Terms</th>
                    <th width="80">Shipment Terms</th>
                    <th width="80">Master LC No</th>
                    <th width="80">Bank Name</th>
                    <th width="80">B2B LC No</th>
                    <th width="90">B2B LC Value ($)</th>
                    <th width="80">BB LC Open Date</th>
                    <th >Remarks On Add Req</th>
                </thead>
                <?
				$total_emb_amount=0;$total_emb_pre_cost_value=0;$total_emb_post_cost_value=0;
				$i=1;
                foreach($emb_sql_result as $row )
                {
                    
					$post_cost_value=0;
					foreach(explode(',',$jobDataArr[PO_ID]) as $po_id){
						// $post_cost_value+=array_sum($embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][AMOUNT]);
						$pi_number=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][PI_NUMBER];
						$supp_nam=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][SUPPLIER_ID];
						$pay_id=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][PAY_TERM];						
						$bank_id=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][ISSUING_BANK_ID];						
						$b2b_lc_number=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][B2B_LC_NUMBER];						
						$b2b_lc_value=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][B2B_LC_VALUE];						
						$b2b_lc_date=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][B2B_LC_DATE];						
						$sc_lc_no=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][SC_LC_NO];						
						$shipping_id=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][SHIPPING_MODE];						
						$inco_term=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][INCO_TERM];						
						$remarks=$embPiDataArr[$po_id.'**'.$row[EMB_NAME].'**'.$row[EMB_TYPE]][B2B_REMARKS];						
					}		
                    $post_cost_value=$embPiDataArr[$row[ID]][AMOUNT];
					$pre_cost_value=$emblishmentdata[$row[ID]];
					
					if($pre_cost_value==''){
						$pre_cost_value=0;
						foreach(explode(',',$jobDataArr[PO_ID]) as $po_id){
							$pre_cost_value+=$washdata[$po_id][$row[ID]];
							
						}
					}

					$total_emb_pre_cost_value+=$pre_cost_value;
					$total_emb_amount+=$row[AMOUNT];
 					$total_emb_post_cost_value+=$post_cost_value;
					$grand_total_pre_cost_value+=$pre_cost_value;
					
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trrr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trrr_<? echo $i; ?>">
                    <td><?= $i; ?></td>
                    <td>Embellishment</td>
                    <td><?= $emblishment_name_array[$row[EMB_NAME]];?></td>
                    <td><?= $emblishment_print_type[$row[EMB_TYPE]];?></td>
                    <td align="center"></td>
                    <td><?= $body_part[$row[BODY_PART_ID]];?></td>
                    <td align="center"><?= $unit_of_measurement[1]; ?></td>
                    <td align="center"><?= number_format($row[CONS_DZN_GMTS],2);?></td>
                    <td align="center"><?= number_format($row[RATE],4);?></td>
                    <td align="right"><?= $row[AMOUNT];?></td>
                    <td align="right"><?= number_format($percentage=($row[AMOUNT]/$unite_price)*100,2);?></td>
					<td align="right"><?= $jobDataArr[PO_QUANTITY_PCS];?></td>
                    <td align="right"><?= number_format($pre_cost_value,2);?></td>
                    <td align="right"><?= number_format($post_cost_value,2);?></td>
                    <td align="right"><?= number_format($pre_cost_value-$post_cost_value,2);?></td>
                    <td ><? echo $pi_number;?></td>
                    <td ><? echo $supplier_lib[$supp_nam];?></td>
                    <td ><? echo $pay_term[$pay_id];?></td>
                    <td ><? echo $shipment_mode[$shipping_id].', '.$incoterm[$inco_term];?></td>
                    <td ><? echo $sc_lc_no;?></td>
                    <td ><? echo $bank_lib[$bank_id];?></td>
                    <td align="center"><? echo $b2b_lc_number;?></td>
                    <td align="right" ><? echo number_format($b2b_lc_value,2);?></td>
                    <td align="center"><? echo change_date_format($b2b_lc_date);?></td>
                    <td ><? echo $remarks;?></td>
                    </tr>
                <?
                $i++;
                }
                ?>
                  
                  <tfoot>
                    <th colspan="9" align="right">Total Embellishment Cost</th>
                    <th><?= number_format($total_emb_amount,2);?></th>
                    <th></th>
                    <th></th>
                    <th><?= number_format($total_emb_pre_cost_value,2);?></th>
                    <th><?= number_format($total_emb_post_cost_value,2);?></th>
                    <th><?= number_format($total_emb_pre_cost_value-$total_emb_post_cost_value,2);?></th>
					<th colspan="10"></th>
                  </tfoot>
                
                </table> 
                              
               </div> 
                
                <table class="rpt_table" width="<?= $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                  <tfoot>
                    <th width="35"></th>
                    <th width="120"></th>
                    <th width="120"></th>
                    <th width="120"></th>
                    <th width="60"></th>
                    <th width="120"></th>
                    <th width="60"></th>
                    <th width="80"></th>
                    <th width="60"></th>
                    <th width="60"><?= number_format($total_fab_amount+$total_trims_amount+$total_emb_amount,2);?></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="60"><?= number_format($grand_total_pre_cost_value,2);?></th>
                    <th width="60"><?= number_format(($total_post_cost_value+$total_trims_post_cost_value+$total_emb_post_cost_value),2);?></th>
                    <th width="60"><?= number_format($grand_total_pre_cost_value-($total_post_cost_value+$total_trims_post_cost_value+$total_emb_post_cost_value),2);?></th>
					<th colspan="10"></th>
                  </tfoot>
               </table>
            
            
            
            <br>
        <table>
            <tr>
                <td valign="top">
                    
                    <table><tr><td>
                    <?php foreach($img_arr as $img){?>
                        <img src="../../../<?=$img;?>" width="200">
                    <?php } ?>
                    </td></tr></table>
                </td>
                <td width="30"></td>
            <td valign="top">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
            	<thead>
                	<th colspan="2">Summary</th>
                	<th>Pre Cost</th>
                	<th>Percent</th>
                	<th>Post Cost</th>
                	<th>Percent</th>
                </thead>
                <tbody> 
            	<tr>
                	
                	<td>Total Fabric cost</td>
                	<td width="35" align="right"></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][FABRIC_COST],2);?></td>
                	
                    <td align="right"><?= number_format((($pre_dtls_result[0][FABRIC_COST]/$pre_dtls_result[0][PRICE_DZN])*100),2);?></td>
                	<td align="right"><?= number_format($total_fb_post_cost=($total_post_cost_value/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                	<td align="right"><?= number_format($total_fb_post_cost/$pre_dtls_result[0][PRICE_DZN]*100,2);?> </td>
                </tr>
            	<tr>
                	<td>Total Acc Cost</td>
                	<td></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][TRIMS_COST],2);?></td>
                    <td align="right"><?= number_format((($pre_dtls_result[0][TRIMS_COST]/$pre_dtls_result[0][PRICE_DZN])*100),2);?></td>
                	<td align="right"><?= number_format($total_trims_post_cost=($total_trims_post_cost_value/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                	<td align="right"><?= number_format($total_trims_post_cost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Total Embellishment Cost</td>
                	<td></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][EMBEL_COST]+$pre_dtls_result[0][WASH_COST],2);?></td>
                    <td align="right"><?= number_format(((($pre_dtls_result[0][EMBEL_COST]+$pre_dtls_result[0][WASH_COST])/$pre_dtls_result[0][PRICE_DZN])*100),2);?></td>
                	<td align="right"><?= number_format($total_emb_post_cost=($total_emb_post_cost_value/$jobDataArr[PO_QUANTITY_PCS]),2);?></td>
                	<td align="right"><?= number_format($total_emb_post_cost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Total Cost</td>
                	<td></td>
                	<td align="right"><?= number_format($grand_total_pre_cost=($pre_dtls_result[0][FABRIC_COST]+$pre_dtls_result[0][TRIMS_COST]+$pre_dtls_result[0][EMBEL_COST]+$pre_dtls_result[0][WASH_COST]),2);?></td>
                    <td align="right"></td>
                	<td align="right"><?= number_format($total_post_cost=($total_fb_post_cost+$total_trims_post_cost+$total_emb_post_cost),2);?></td>
                	<td align="right"></td>
                </tr>
            	<tr>
                	<td>Commercial Cost [All Together]</td>
                	<td align="right"><?= $commercial_cost_rate_arr[4];?> %</td>
                    <td align="right"><?= number_format($CommercialCost=($grand_total_pre_cost*$commercial_cost_rate_arr[4])/100,2);?></td>
                	<td align="right"><?= number_format($CommercialCost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><? //number_format($CommercialCost,2);?></td>
                	<td align="right"><?= number_format($CommercialCost/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Profit Margin</td>
                	<td align="right"><?= number_format(($grand_total_pre_cost*$pre_dtls_result[0][MARGIN_PCS_SET]/100),2);?> %</td>
                	<td align="right"><?= $pre_dtls_result[0][MARGIN_PCS_SET];?></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][MARGIN_PCS_SET]/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><? //number_format($pre_dtls_result[0][MARGIN_PCS_SET],4);?></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][MARGIN_PCS_SET]/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>CM</td>
                	<td align="right"></td>
                	<td align="right"><?
					 $NetPrice=$pre_dtls_result[0][PRICE_DZN]-((($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[1])+(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[2]));
					// echo number_format($CM=$NetPrice-($pre_dtls_result[0][MARGIN_PCS_SET]+$CommercialCost+$grand_total_pre_cost),2);
                    echo number_format($pre_dtls_result[0][CM_COST],2);
                    ?></td>
                	<td align="right"><?= number_format($CM/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                	<td align="right"><? 
                    $post_CM=$NetPrice-($pre_dtls_result[0][MARGIN_PCS_SET]+$CommercialCost+$total_post_cost); 
                    // echo number_format($post_CM,2);
                    $CM_POST=$NetPrice-$total_post_cost;
                    echo number_format($CM_POST,2);
                    ?></td>
                	<td align="right"><?= number_format($post_CM/$pre_dtls_result[0][PRICE_DZN]*100,2);?></td>
                </tr>
            	<tr>
                	<td>Net Price</td>
                	<td align="right"></td>
                	<td align="right"><?= number_format($NetPrice,2);?></td>
                    <td></td>
                	<td align="right"><?= number_format($NetPrice,2);?></td>
                	<td align="right"></td>
                </tr>
            	<tr>
                	<td>L. Comm</td>
                	<td align="right"><? //number_format($pre_commision_rate_arr[2],2);?> </td>
                	<td align="right"><?= //number_format($l_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[2]),2);
                            number_format($pre_commision_amount_arr[2],2);?></td>
                	<td align="right"><?= //number_format($l_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);
                                            number_format($pre_commision_rate_arr[2],2);?></td>
                	<td align="right"><?= //number_format($l_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[2]),2);
                            number_format($pre_commision_amount_arr[2],2);?></td>
                	<td align="right"><?= //number_format($l_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);
                            number_format($pre_commision_rate_arr[2],2);?></td>
                </tr>
            	<tr>
                	<td>F. Comm</td>
                	<td align="right"><?  //number_format($pre_commision_rate_arr[1],2);?> </td>
                	<td align="right"><?= //number_format($f_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[1]),2);
                        number_format($pre_commision_amount_arr[1],2);?></td>
                	<td align="right"><?= //number_format($f_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);
                        number_format($pre_commision_rate_arr[1],2);?></td>
                	<td align="right"><?= //number_format($f_comm=(($pre_dtls_result[0][PRICE_DZN]/100)*$pre_commision_rate_arr[1]),2);
                        number_format($pre_commision_amount_arr[1],2);?></td>
                	<td align="right"><?= //number_format($f_comm/$pre_dtls_result[0][PRICE_DZN]*100,2);
                        number_format($pre_commision_rate_arr[1],2);?></td>
                </tr>
            	<tr>
                	<td height="25">FOB Price</td>
       	      		<td align="right"></td>
                	<td align="right"><?= number_format($pre_dtls_result[0][PRICE_DZN],2);?></td>
                	<td align="right">100%</td>
                	<td align="right"><?= number_format($pre_dtls_result[0][PRICE_DZN],2);?></td>
                	<td align="right">100%</td>
                </tr>
              </tbody>
            </table>
            <br>
            
            <table cellspacing="5" cellpadding="5" width="300" border="1" rules="all" class="rpt_table" id="table_body">
                <tr>
                    <td>Sales Value </td>
                    <td align="right" title="(shipment qty x FOB)">$ <?= $CM_earned=$ship_qty*$pre_dtls_result[0][PRICE_DZN];?></td>
                </tr>
                <tr>
                    <td>CM earned </td>
                    <td align="right" title="(sales value - PI value)">$ <?= $ship_qty*$CM;?></td>
                </tr> 
                <tr>
                    <td>Post CM </td>
                    <td align="right">$ <?= //$CM_earned/$jobDataArr[PO_QUANTITY_PCS];
                        number_format($CM_POST,2);?></td>
                </tr> 
            </table>

            </td>
            </tr>
        </table>

            </fieldset>
        </div>
	<?
	
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	ob_clean();
	
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	
	$filename="../../../ext_resource/tmp_report/".$user_name."_".time().".xls";
	echo "$html****$filename";
	
	disconnect($con);
	exit();	
}
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=11 and report_id=148 and is_deleted=0 and status_active=1");
	//echo $print_report_format.jahid;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#show2').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==195){echo "$('#show2').show();\n";}
	
            //195,
		}
	}
	// else
	// {
	// 	echo "$('#show2').show();\n";
	// }
	exit();
}
?>