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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
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
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond $whereCon and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $job_year; ?>', 'job_no_list_view', 'search_div', 'comparative_post_cost_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

	
	
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	$sql= "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and GARMENTS_NATURE=2 and is_deleted=0 $where_con group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC ";
	
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
	require_once('../../../../includes/class4/class.conversions.php');
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
		
		
/*		
		
		$fabricdata=$fabric->getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
		$trims= new trims($condition);
		$trimsdata=$trims->getAmountArray_precostdtlsid();
		$emblishment= new emblishment($condition);
		$emblishmentdata=$emblishment->getAmountArray_by_emblishmentid();
		$wash= new wash($condition);
		$washdata=$wash->getAmountArray_by_orderAndEmblishmentid();
*/
		
		$fabric= new fabric($condition);
		$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		
		//print_r($fabric_qty_arr['knit']['finish']);die;
		
		
		$trims= new trims($condition);
		$trim_amount=$trims->getAmountArray_precostdtlsid();
		$trim_qty_arr=$trims->getQtyArray_by_precostdtlsid();

		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		
		$commercial= new commercial($condition);
		$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//echo "<pre>";
		//print_r($yarn_data_array);die;
		$conversion= new conversion($condition);
		$conv_data_qty=$conversion->getQtyArray_by_conversionid();
		$conv_data_amt=$conversion->getAmountArray_by_conversionid();
		
		
		$emblishment= new emblishment($condition);
		$emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();

		$wash= new wash($condition);
		$wash_qty_arr=$wash->getQtyArray_by_jobAndEmblishmentid();

		
 	// print_r($conv_data_qty);die;
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
    $supplier_library=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");//and b.party_type in(4,5)
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	
	
	
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
	
	
	
	
	//JObo..........
	$job_sql="select  A.AVG_UNIT_PRICE,A.BUYER_NAME,a.STYLE_REF_NO,b.ID as PO_ID,b.PO_NUMBER,a.GMTS_ITEM_ID,a.SET_SMV,A.JOB_NO,a.FACTORY_MARCHANT,a.ORDER_UOM,b.FILE_NO,SUM(B.PLAN_CUT) AS PLAN_CUT, SUM(A.TOTAL_SET_QNTY*B.PO_QUANTITY) as PO_QUANTITY_PCS,SUM(B.PO_TOTAL_PRICE) AS AMOUNT,MAX(PUB_SHIPMENT_DATE) AS SHIPMENT_DATE, SUM (B.PO_QUANTITY) AS PO_QUANTITY 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.GARMENTS_NATURE=2 and a.is_deleted=0 and b.is_deleted=0 $where_con2 GROUP BY A.AVG_UNIT_PRICE,a.SET_SMV,A.JOB_NO,A.BUYER_NAME,a.STYLE_REF_NO,a.GMTS_ITEM_ID,a.FACTORY_MARCHANT,a.ORDER_UOM,b.FILE_NO,b.ID,b.PO_NUMBER";
	 //echo $job_sql;die;
	$job_sql_result=sql_select($job_sql);
	$jobDataArr=array();
	foreach($job_sql_result as $row){
		$jobDataArr[PO_ID][$row[PO_ID]]=$row[PO_ID];
		$jobDataArr[JOB_NO][$row[JOB_NO]]=$row[JOB_NO];
		
		$avg_unite_price=$row[AVG_UNIT_PRICE];
		$job_quantity+=$row[PLAN_CUT];
		$order_quantity+=$row[PO_QUANTITY];
		
	}
 //-------------------------------------------
 	$lc_sql="select a.EXPORT_LC_NO as EXPORT_LC_NO,a.INTERNAL_FILE_NO,b.WO_PO_BREAK_DOWN_ID 
	from COM_EXPORT_LC a,COM_EXPORT_LC_ORDER_INFO b where a.id=B.COM_EXPORT_LC_ID and b.WO_PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).")
	union all
	select a.CONTRACT_NO as EXPORT_LC_NO,a.INTERNAL_FILE_NO,b.WO_PO_BREAK_DOWN_ID 
	from COM_SALES_CONTRACT a, COM_SALES_CONTRACT_ORDER_INFO b where a.id=B.COM_SALES_CONTRACT_ID and b.WO_PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).")";
	//echo $lc_sql;die;
	$lc_sql_result=sql_select($lc_sql);
	$lcScDataArr=array();
	foreach($lc_sql_result as $row){
		$lcScDataArr[$row[WO_PO_BREAK_DOWN_ID]][EXPORT_LC_NO]=$row[EXPORT_LC_NO];
		$lcScDataArr[$row[WO_PO_BREAK_DOWN_ID]][INTERNAL_FILE_NO]=$row[INTERNAL_FILE_NO];
	}

//------------------------------------------
	$export_invoice_sql="select a.IS_LC, a.LC_SC_ID, a.UPCHARGE, a.DISCOUNT_AMMOUNT, a.INVOICE_VALUE, b.CURRENT_INVOICE_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE  
	from COM_EXPORT_INVOICE_SHIP_MST a,COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=b.mst_id and b.PO_BREAKDOWN_ID  in(".implode(',',$jobDataArr[PO_ID]).")";
	$export_invoice_sql_result=sql_select($export_invoice_sql);
	$exportInvoiceDataArr=array();
	foreach($export_invoice_sql_result as $row)
	{
		$propo_upcharge_value=(($row[UPCHARGE]/$row[INVOICE_VALUE])*$row[CURRENT_INVOICE_VALUE]);
		$propo_discount_value=(($row[DISCOUNT_AMMOUNT]/$row[INVOICE_VALUE])*$row[CURRENT_INVOICE_VALUE]);
		$exportInvoiceDataArr[$row[PO_BREAKDOWN_ID]][UPCHARGE]=$propo_upcharge_value;
		$exportInvoiceDataArr[$row[PO_BREAKDOWN_ID]][DISCOUNT_AMMOUNT]=$propo_discount_value;
	}

	
	
//Exfactory------------------------------------
	$ex_factory_sql="select PO_BREAK_DOWN_ID,SHIPING_STATUS,max(EX_FACTORY_DATE) as SHIP_DATE, sum(EX_FACTORY_QNTY) as EX_FACTORY_QNTY from pro_ex_factory_mst where status_active=1 and is_deleted=0 and PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).") group by PO_BREAK_DOWN_ID,SHIPING_STATUS";
	  //echo $ex_factory_sql;die;
	$ex_factory_sql_result=sql_select($ex_factory_sql);
	$exfDataArr=array();
	foreach($ex_factory_sql_result as $row){
		$exfDataArr[$row[PO_BREAK_DOWN_ID]][SHIPING_STATUS]=$row[SHIPING_STATUS];
		$exfDataArr[$row[PO_BREAK_DOWN_ID]][SHIP_DATE]=$row[SHIP_DATE];
		$exfDataArr[$row[PO_BREAK_DOWN_ID]][EX_FACTORY_QNTY]+=$row[EX_FACTORY_QNTY];
	}
	
		
	
	
    //Precost fb........................
   

   $pre_cost_fb_sql="SELECT b.ID AS FB_COST_DTLS_ID,
   		 B.UOM,
         b.AMOUNT,
		 b.RATE,
         b.AVG_FINISH_CONS as CONS,
         A.GARMENTS_NATURE,
		 b.FABRIC_SOURCE,
		 a.COSTING_PER,
         b.JOB_NO,
		 b.BODY_PART_ID,
		 b.LIB_YARN_COUNT_DETER_ID,
         b.CONSTRUCTION,
         b.COMPOSITION,
         B.GSM_WEIGHT,
		 b.FABRIC_DESCRIPTION,
		 b.COLOR_TYPE_ID,
		 listagg(cast(c.FABRIC_COLOR_ID as varchar (4000)),',') within group(order by c.FABRIC_COLOR_ID) as FABRIC_COLOR_ID,
		 listagg(cast(c.DIA_WIDTH as varchar (4000)),',') within group(order by c.DIA_WIDTH) as DIA_WIDTH
         
    FROM WO_PRE_COST_MST a, WO_PRE_COST_FABRIC_COST_DTLS b left join WO_BOOKING_DTLS c on b.id=c.PRE_COST_FABRIC_COST_DTLS_ID
   WHERE A.JOB_ID = B.JOB_ID
         AND A.IS_DELETED = 0
         AND A.STATUS_ACTIVE = 1
         AND b.IS_DELETED = 0
         AND b.STATUS_ACTIVE = 1
		 and b.fab_nature_id=2
         $where_con
		 group by b.ID, B.UOM, b.AMOUNT, b.RATE,
         b.AVG_FINISH_CONS,
         A.GARMENTS_NATURE,
		 b.FABRIC_SOURCE,
		 a.COSTING_PER,
         b.JOB_NO,
		 b.BODY_PART_ID,
		 b.LIB_YARN_COUNT_DETER_ID,
         b.CONSTRUCTION,
         b.COMPOSITION,
         B.GSM_WEIGHT,
		 b.FABRIC_DESCRIPTION,
		 b.COLOR_TYPE_ID";
	//echo $pre_cost_fb_sql;die; 
	 
	$pre_cost_fb_sql_result=sql_select($pre_cost_fb_sql);
	$fbCostIdArr=array();$yarnCountDeterIdArr=array();
	foreach($pre_cost_fb_sql_result as $rows){
		$fbCostIdArr[$rows[FB_COST_DTLS_ID]]=$rows[FB_COST_DTLS_ID];
		$yarnCountDeterIdArr[$rows[LIB_YARN_COUNT_DETER_ID]]=$rows[LIB_YARN_COUNT_DETER_ID];
		if($rows[COSTING_PER]!=''){$COSTING_PER=$rows[COSTING_PER];}
	}
	//echo $pre_cost_sql;
	//print_r($pre_cost_fb_sql_result);die;
	
	
//------------------------------	
	
	$pre_cost_yarn_sql = "select min(id) as id,min(SUPPLIER_ID) as SUPPLIER_ID, count_id, copm_one_id, percent_one,  color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount 
	from wo_pre_cost_fab_yarn_cost_dtls 
	where job_no='".$txt_job_no."' and STATUS_ACTIVE=1 and IS_DELETED=0 group by count_id, copm_one_id, percent_one, color,type_id, rate";
	//echo $pre_cost_yarn_sql ;die;
	$pre_cost_yarn_sql_result=sql_select($pre_cost_yarn_sql);
	 //echo $pre_cost_yarn_sql;die;

//-----------------------------	
	//$pre_cost_knit_sql = "select ID,COMPANY_ID, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons,uom, fabric_source, rate, amount, avg_finish_cons, status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$txt_job_no."' and fab_nature_id=2";
	
	$pre_cost_knit_sql = "select a.ID,a.fabric_description as PRE_COST_FABRIC_COST_DTLS_ID, a.job_no, a.cons_process, a.req_qnty, a.charge_unit,a.amount,a.color_break_down, a.status_active,b.body_part_id,b.uom,b.COMPANY_ID ,b.avg_cons,b.fab_nature_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id,b.color_type_id,b.fabric_source from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.job_no=b.job_no and a.fabric_description=b.id and b.status_active=1 and b.is_deleted=0 and a.job_no='".$txt_job_no."' and a.cons_process=1 and a.status_active=1 and a.is_deleted=0 order by  a.cons_process";
	$pre_cost_knit_sql_result=sql_select($pre_cost_knit_sql);
	foreach($pre_cost_knit_sql_result as $rows){
		//$fbCostIdArr[$rows[PRE_COST_FABRIC_COST_DTLS_ID]]=$rows[PRE_COST_FABRIC_COST_DTLS_ID];
		$fbCostIdArr[$rows[ID]]=$rows[ID];
	}

	 //echo $pre_cost_knit_sql;die;
//------------------------------------
	/*$pre_cost_trims_sql = "select ID, JOB_NO, CONS_DZN_GMTS, APVL_REQ, TRIM_GROUP, DESCRIPTION, BRAND_SUP_REF, CONS_UOM, CONS_DZN_GMTS, RATE, AMOUNT, APVL_REQ, NOMINATED_SUPP, STATUS_ACTIVE, SEQ 
	from wo_pre_cost_trim_cost_dtls where job_no='".$txt_job_no."'  and STATUS_ACTIVE=1 and IS_DELETED=0 order by seq";*/
	$pre_cost_trims_sql = "select listagg(cast(ID as varchar(4000)),',') within group (order by id) as ID, max(CONS_UOM) as CONS_UOM, max(CONS_DZN_GMTS) as CONS_DZN_GMTS, TRIM_GROUP
	from wo_pre_cost_trim_cost_dtls where job_no='".$txt_job_no."'  and STATUS_ACTIVE=1 and IS_DELETED=0 group by TRIM_GROUP";
	//echo $pre_cost_trims_sql;die;
	$pre_cost_trims_sql_result=sql_select($pre_cost_trims_sql);
	$trim_group_id_arr=array();
	foreach($pre_cost_trims_sql_result as $rows){
		$trim_group_id_arr[$rows[TRIM_GROUP]]=$rows[TRIM_GROUP];
		$pre_id_arr=array_unique(explode(",",$rows[ID]));
		foreach($pre_id_arr as $pre_id)
		{
			$fbCostIdArr[$pre_id]=$pre_id;
		}
	}

	$trim_group=return_library_array( "select item_name,id from  lib_item_group where id in(".implode(',',$trim_group_id_arr).")", "id", "item_name" );

//--------------------------------
	$pre_cost_emb_sql = "select listagg(cast(ID as varchar(4000)),',') within group (order by id) as ID, JOB_NO, EMB_NAME, EMB_TYPE, max(CONS_DZN_GMTS) as CONS_DZN_GMTS, sum(AMOUNT) as AMOUNT, avg(RATE) as RATE from wo_pre_cost_embe_cost_dtls where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0 group by JOB_NO, EMB_NAME, EMB_TYPE";
	//echo $pre_cost_emb_sql;die;
	$pre_cost_emb_sql_result=sql_select($pre_cost_emb_sql);
	foreach($pre_cost_emb_sql_result as $rows)
	{
		//$fbCostIdArr[$rows[ID]]=$rows[ID];
		$pre_id_arr=array_unique(explode(",",$rows[ID]));
		foreach($pre_id_arr as $pre_id)
		{
			$fbCostIdArr[$pre_id]=$pre_id;
		}
	}

//---------------------------------	
	$pre_cost_others_sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,currier_pre_cost, currier_percent, 	certificate_pre_cost, certificate_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  from wo_pre_cost_dtls where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0";
	$pre_cost_others_sql_result=sql_select($pre_cost_others_sql);
  //echo $pre_cost_others_sql;die;
 
 
//---------------------------------	
   	$pre_cost_commercial_sql = "select JOB_NO, avg(RATE) as RATE, sum(AMOUNT) as AMOUNT from  wo_pre_cost_comarci_cost_dtls where job_no='".$txt_job_no."' group by JOB_NO";
	$pre_cost_commercial_sql_result=sql_select($pre_cost_commercial_sql);
	
//---------------------	
   	$pre_cost_commission_sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$txt_job_no."'";
	$pre_cost_commission_sql_result=sql_select($pre_cost_commission_sql);

//----------------------
	$pre_cost_conversion_sql = "select a.ID,a.fabric_description as PRE_COST_FABRIC_COST_DTLS_ID, a.job_no, a.cons_process, a.req_qnty, a.charge_unit,a.amount,a.color_break_down, a.status_active,b.body_part_id,b.uom,b.COMPANY_ID ,b.avg_cons,b.fab_nature_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id,b.color_type_id,b.fabric_source 
	from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b 
	where a.job_no=b.job_no and a.fabric_description=b.id and b.status_active=1 and b.is_deleted=0 and a.job_no='".$txt_job_no."' and a.cons_process !=1 and a.status_active=1 and a.is_deleted=0 order by  a.cons_process";
	//echo $pre_cost_conversion_sql."<br>";
	$pre_cost_conversion_sql_result=sql_select($pre_cost_conversion_sql);
	foreach($pre_cost_conversion_sql_result as $rows){
		$fbCostIdArr[$rows[ID]]=$rows[ID];
	}
	 
	//echo $pre_cost_conversion_sql; 
	 
	 
	 //echo $pre_cost_conversion_sql;die;
	 
	 	
	
	//fabric pi......................
   
   $pi_sql="select a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,a.UOM,b.ITEM_GROUP,b.EMBELL_NAME,b.EMBELL_TYPE,b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT,b.QUANTITY,b.FABRIC_COMPOSITION,b.FABRIC_CONSTRUCTION,b.ITEM_DESCRIPTION,c.PI_NUMBER,c.SUPPLIER_ID,c.id as PI_ID,c.SOURCE 
   from WO_BOOKING_DTLS a, COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS c 
   where A.BOOKING_NO = B.WORK_ORDER_NO AND a.id=b.WORK_ORDER_DTLS_ID  and c.id=b.pi_id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and c.ITEM_CATEGORY_ID in(12) and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and a.PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).")";
	//echo $pi_sql; 
	//echo $pi_sql;
	$pi_sql_result=sql_select($pi_sql);
	$dfPiDataArr=array();
	foreach($pi_sql_result as $rows){
		
		//------------Dyeing And Finishing Cost
		$dfKey=$rows[PRE_COST_FABRIC_COST_DTLS_ID].'**'.$rows[UOM];
		$dfPiDataArr[$dfKey][SOURCE]=$rows[SOURCE];
		$dfPiDataArr[$dfKey][AMOUNT]+=$rows[AMOUNT];
 		$dfPiDataArr[$dfKey][QTY]+=$rows[QUANTITY];
 		$dfPiDataArr[$dfKey][RATE]=$rows[RATE];
 		$dfPiDataArr[$dfKey][PI_NUMBER][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
	 	$dfPiDataArr[$dfKey][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		$dfPiDataArr[$dfKey][PI_ID]=$rows[PI_ID];
		$dfPiDataArrs[$dfKey][PI_ID][$rows[PI_ID]]=$rows[PI_ID];
		$allPIIdArr[$rows[PI_ID]]=$rows[PI_ID];
	}
	
/*//------------Dyeing And Finishing Cost	
$dyeing_finishing_pi_sql="select  a.UOM,b.ITEM_GROUP,b.EMBELL_NAME,b.EMBELL_TYPE,b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT,b.QUANTITY,b.FABRIC_COMPOSITION,b.FABRIC_CONSTRUCTION,b.ITEM_DESCRIPTION,c.PI_NUMBER,c.SUPPLIER_ID,c.id as PI_ID,c.SOURCE from WO_YARN_DYEING_MST d,WO_YARN_DYEING_DTLS a,COM_PI_ITEM_DETAILS b,COM_PI_MASTER_DETAILS c where d.id=a.mst_id  and c.id=b.pi_id and a.id=B.WORK_ORDER_DTLS_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 ".where_con_using_array($jobDataArr[JOB_NO],1,'a.JOB_NO')." and c.ITEM_CATEGORY_ID=24";
  //echo $dyeing_finishing_pi_sql;
 
	$dyeing_finishing_pi_sql_result=sql_select($dyeing_finishing_pi_sql);
	$dfPiDataArr=array();
	foreach($dyeing_finishing_pi_sql_result as $rows){
		//------------knit
		$dfKey=$rows[UOM];
		$dfPiDataArr[$dfKey][SOURCE]=$rows[SOURCE];
		$dfPiDataArr[$dfKey][AMOUNT]+=$rows[AMOUNT];
 		$dfPiDataArr[$dfKey][QTY]+=$rows[QUANTITY];
 		$dfPiDataArr[$dfKey][RATE]=$rows[RATE];
 		$dfPiDataArr[$dfKey][PI_NUMBER]=$rows[PI_NUMBER];
	 	$dfPiDataArr[$dfKey][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		$dfPiDataArr[$dfKey][PI_ID]=$rows[PI_ID];
	}*/
	//Fabric.............................................	
	//fb booking......
	$fb_booking_sql="select BOOKING_NO from WO_BOOKING_DTLS where PO_BREAK_DOWN_ID IN (".implode(',',$jobDataArr[PO_ID]).") and PRE_COST_FABRIC_COST_DTLS_ID IN (".implode(',',$fbCostIdArr).") and BOOKING_TYPE=1 group by BOOKING_NO";
	$fb_booking_sql_result=sql_select($fb_booking_sql);
	$fbBookingDataArr=array();
	foreach($fb_booking_sql_result as $rows){
		$fbBookingDataArr[$rows[BOOKING_NO]]=$rows[BOOKING_NO];
	}
	//echo $fb_booking_sql;

	$fb_pi_sql="select c.ITEM_CATEGORY_ID, b.DETERMINATION_ID, b.ITEM_GROUP, b.EMBELL_NAME, b.EMBELL_TYPE, b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT, b.QUANTITY, b.FABRIC_COMPOSITION, b.FABRIC_CONSTRUCTION, b.ITEM_DESCRIPTION, b.COLOR_ID, b.GSM, b.DIA_WIDTH, c.PI_NUMBER, c.SUPPLIER_ID, c.id as PI_ID, c.SOURCE 
	FROM COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS c 
	WHERE c.id=b.pi_id and b.WORK_ORDER_NO IN('".implode("','",$fbBookingDataArr)."') and b.DETERMINATION_ID IN (".implode(',',$yarnCountDeterIdArr).") and c.ITEM_CATEGORY_ID=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
  	//echo $fb_pi_sql;die;

	$fb_pi_sql_result=sql_select($fb_pi_sql);
	$fbPiDataArr=array();
	foreach($fb_pi_sql_result as $rows)
	{
		if($rows[ITEM_CATEGORY_ID]==2){
			//$key=$rows[DETERMINATION_ID].'**'.$rows[FABRIC_CONSTRUCTION].'**'.$rows[FABRIC_COMPOSITION].'**'.$rows[GSM];
			//$key=$rows[DETERMINATION_ID].'**'.$rows[FABRIC_CONSTRUCTION].'**'.$rows[FABRIC_COMPOSITION].'**'.$rows[GSM].'**'.$rows[COLOR_ID].'**'.$rows[DIA_WIDTH];
			$key=$rows[DETERMINATION_ID].'**'.$rows[FABRIC_CONSTRUCTION].'**'.$rows[FABRIC_COMPOSITION].'**'.$rows[GSM].'**'.$rows[COLOR_ID];
			$fbPiDataArr[$key][AMOUNT]+=$rows[AMOUNT];
			$fbPiDataArr[$key][QTY]+=$rows[QUANTITY];
			$fbPiDataArr[$key][RATE]=$rows[RATE];
			
			$fbPiDataArr[$key][SOURCE]=$rows[SOURCE];
			$fbPiDataArr[$key][PI_NUMBER][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
			$fbPiDataArr[$key][SUPPLIER_ID]=$rows[SUPPLIER_ID];
			$fbPiDataArr[$key][PI_ID]=$rows[PI_ID];
			$fbPiDataArrs[$key][PI_ID][$rows[PI_ID]]=$rows[PI_ID];
			$allPIIdArr[$rows[PI_ID]]=$rows[PI_ID];
		}
	
	}
	//echo "<pre>";print_r($fbPiDataArr);die;
	//echo $fb_pi_sql;die;
	
	   
		
	//Knit ---------------	
	$knit_pi_sql="select a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,a.UOM,b.ITEM_GROUP,b.EMBELL_NAME,b.EMBELL_TYPE,b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT,b.QUANTITY,b.FABRIC_COMPOSITION,b.FABRIC_CONSTRUCTION,b.ITEM_DESCRIPTION,c.PI_NUMBER,c.SUPPLIER_ID,c.id as PI_ID,c.SOURCE 
	from WO_BOOKING_DTLS a, COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS c 
	where A.BOOKING_NO = B.WORK_ORDER_NO and a.GMTS_COLOR_ID=B.COLOR_ID and c.id=b.pi_id  and a.id=B.WORK_ORDER_DTLS_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and c.ITEM_CATEGORY_ID=12 and a.PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).") ";
 //a.CONSTRUCTION=B.FABRIC_CONSTRUCTION and A.COPMPOSITION=B.FABRIC_COMPOSITION and a.DIA_WIDTH=B.DIA_WIDTH
 //echo $knit_pi_sql; 
   
	//echo $knit_pi_sql;die;
	$knit_pi_sql_result=sql_select($knit_pi_sql);
	$knitPiDataArr=array();
	foreach($knit_pi_sql_result as $rows){
		//------------knit
		//$knitKey=$rows[ITEM_DESCRIPTION];
		$knitKey=$rows[PRE_COST_FABRIC_COST_DTLS_ID].'**'.$rows[UOM];
		$knitPiDataArr[$knitKey][SOURCE]=$rows[SOURCE];
		$knitPiDataArr[$knitKey][AMOUNT]+=$rows[AMOUNT];
 		$knitPiDataArr[$knitKey][QTY]+=$rows[QUANTITY];
 		$knitPiDataArr[$knitKey][RATE]=$rows[RATE];
 		$knitPiDataArr[$knitKey][PI_NUMBER][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
	 	$knitPiDataArr[$knitKey][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		$knitPiDataArr[$knitKey][PI_ID]=$rows[PI_ID];
		$knitPiDataArrs[$knitKey][PI_ID][$rows[PI_ID]]=$rows[PI_ID];
		$allPIIdArr[$rows[PI_ID]]=$rows[PI_ID];
	}
	//echo "<pre>";print_r($knitPiDataArrs);die;
		
	
	
//Trim ---------------	
	//$trims_pi_sql="select a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,a.UOM,b.ITEM_GROUP,b.EMBELL_NAME,b.EMBELL_TYPE,b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT,b.QUANTITY,b.FABRIC_COMPOSITION,b.FABRIC_CONSTRUCTION,b.ITEM_DESCRIPTION,c.PI_NUMBER,c.SUPPLIER_ID,c.id as PI_ID,c.SOURCE 
//	from WO_BOOKING_DTLS a, COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS c 
//	where A.BOOKING_NO = B.WORK_ORDER_NO and a.GMTS_COLOR_ID=B.COLOR_ID and c.id=b.pi_id  and a.id=B.WORK_ORDER_DTLS_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and c.ITEM_CATEGORY_ID=4 and a.PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).")";
	
	$trims_pi_sql="select a.PO_BREAK_DOWN_ID,a.PRE_COST_FABRIC_COST_DTLS_ID,a.UOM,b.ITEM_GROUP,b.EMBELL_NAME,b.EMBELL_TYPE,b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT,b.QUANTITY,b.FABRIC_COMPOSITION,b.FABRIC_CONSTRUCTION,b.ITEM_DESCRIPTION,c.PI_NUMBER,c.SUPPLIER_ID,c.id as PI_ID,c.SOURCE 
	from WO_BOOKING_DTLS a, COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS c 
	where A.BOOKING_NO = B.WORK_ORDER_NO and a.booking_type=2 and c.id=b.pi_id  and a.id=B.WORK_ORDER_DTLS_ID and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and c.ITEM_CATEGORY_ID=4 and a.PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).")";
 //a.CONSTRUCTION=B.FABRIC_CONSTRUCTION and A.COPMPOSITION=B.FABRIC_COMPOSITION and a.DIA_WIDTH=B.DIA_WIDTH
// echo $knit_pi_sql; 
   
	//echo $trims_pi_sql;
	$trims_pi_sql_result=sql_select($trims_pi_sql);
	$trimsPiDataArr=array();
	foreach($trims_pi_sql_result as $rows){
		//------------knit
		//$knitKey=$rows[ITEM_DESCRIPTION];
		$trimsKey=$rows[ITEM_GROUP];
		$trimsPiDataArr[$trimsKey][SOURCE]=$rows[SOURCE];
		$trimsPiDataArr[$trimsKey][AMOUNT]+=$rows[AMOUNT];
 		$trimsPiDataArr[$trimsKey][QTY]+=$rows[QUANTITY];
 		$trimsPiDataArr[$trimsKey][RATE]=$rows[RATE];
 		$trimsPiDataArr[$trimsKey][PI_NUMBER][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
	 	$trimsPiDataArr[$trimsKey][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		$trimsPiDataArr[$trimsKey][PI_ID]=$rows[PI_ID];	
		$trimsPiDataArrs[$trimsKey][PI_ID][$rows[PI_ID]]=$rows[PI_ID];
		$allPIIdArr[$rows[PI_ID]]=$rows[PI_ID];	
	}
	
	
//Emb ---------------	
	$emb_pi_sql="select a.PO_BREAK_DOWN_ID, a.PRE_COST_FABRIC_COST_DTLS_ID, a.UOM, b.ITEM_GROUP, b.EMBELL_NAME, b.EMBELL_TYPE, b.NET_PI_RATE as RATE, b.NET_PI_AMOUNT as AMOUNT, b.QUANTITY, b.FABRIC_COMPOSITION, b.FABRIC_CONSTRUCTION, b.ITEM_DESCRIPTION, c.PI_NUMBER, c.SUPPLIER_ID, c.id as PI_ID, c.SOURCE 
	from  WO_BOOKING_DTLS a, COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS c 
	where A.BOOKING_NO = B.WORK_ORDER_NO and a.GMTS_COLOR_ID=B.COLOR_ID and c.id=b.pi_id  and a.id=B.WORK_ORDER_DTLS_ID  and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and a.PRE_COST_FABRIC_COST_DTLS_ID in(".implode(',',$fbCostIdArr).") and c.ITEM_CATEGORY_ID=25 and a.PO_BREAK_DOWN_ID in(".implode(',',$jobDataArr[PO_ID]).")";
 //a.CONSTRUCTION=B.FABRIC_CONSTRUCTION and A.COPMPOSITION=B.FABRIC_COMPOSITION and a.DIA_WIDTH=B.DIA_WIDTH
 // echo $emb_pi_sql; 
   
	 //echo $pi_sql;
	$emb_pi_sql_result=sql_select($emb_pi_sql);
	$embPiDataArr=array();
	foreach($emb_pi_sql_result as $rows)
	{
		//------------knit
		//$knitKey=$rows[ITEM_DESCRIPTION];
		$embKey=$rows[EMBELL_NAME].'**'.$rows[EMBELL_TYPE];
		$embPiDataArr[$embKey][SOURCE]=$rows[SOURCE];
		$embPiDataArr[$embKey][SUPPLIER_ID]=$rows[SUPPLIER_ID];
		
		$embPiDataArr[$embKey][AMOUNT]+=$rows[AMOUNT];
 		$embPiDataArr[$embKey][QTY]+=$rows[QUANTITY];
	 	$embPiDataArr[$embKey][PI_ID][$rows[PI_ID]]=$rows[PI_ID];
		$embPiDataArr[$embKey][PI_NUMBER][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
		$allPIIdArr[$rows[PI_ID]]=$rows[PI_ID];
	}
	
//yarn pi......................	work order base [Yarn Purchase Order]
	$yarn_pi_sql="SELECT c.YARN_TYP, C.QUANTITY, c.NET_PI_RATE as RATE, c.NET_PI_AMOUNT as AMOUNT, c.FABRIC_COMPOSITION, c.FABRIC_CONSTRUCTION, d.PI_NUMBER, d.SUPPLIER_ID, d.id as PI_ID, b.YARN_COUNT, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, d.SOURCE
	FROM WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b, COM_PI_ITEM_DETAILS c, COM_PI_MASTER_DETAILS d
	WHERE A.id = B.mst_id AND a.WO_NUMBER = c.WORK_ORDER_NO and b.id=c.WORK_ORDER_DTLS_ID and c.pi_id=d.id and c.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 AND b.JOB_NO = '".$txt_job_no."'";
    //echo $yarn_pi_sql;die;
	$yarn_pi_sql_result=sql_select($yarn_pi_sql);
	$yarnPiDataArr=array();
	foreach($yarn_pi_sql_result as $rows){
 		$key=$rows[YARN_COUNT].'**'.$rows[YARN_COMP_TYPE1ST].'**'.$rows[YARN_COMP_PERCENT1ST].'**'.$rows[YARN_TYPE];
		$yarnPiDataArr[$key][SOURCE]=$rows[SOURCE];
		$yarnPiDataArr[$key][AMOUNT]+=$rows[AMOUNT];
 		$yarnPiDataArr[$key][QTY]+=$rows[QUANTITY];
 		$yarnPiDataArr[$key][RATE]=$rows[RATE];
 		$yarnPiDataArr[$key][PI_NUMBER][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
	 	$yarnPiDataArr[$key][SUPPLIER_ID][$rows[SUPPLIER_ID]]=$rows[SUPPLIER_ID];
		$yarnPiDataArr[$key][PI_ID]=$rows[PI_ID];
		$yarnPiDataArrs[$key][PI_ID][$rows[PI_ID]]=$rows[PI_ID];
		$allPIIdArr[$rows[PI_ID]]=$rows[PI_ID];
	}	
	//echo "<pre>";print_r($yarnPiDataArr);die;
	
	
	
	
	
   $btb_sql="select a.ID,b.PI_ID ,a.LC_NUMBER from  COM_BTB_LC_PI b,COM_BTB_LC_MASTER_DETAILS a
    where a.id=b.COM_BTB_LC_MASTER_DETAILS_ID and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.PI_ID in(".implode(',',$allPIIdArr).")";
 	//echo $btb_sql;die;
	$btb_sql_result=sql_select($btb_sql);
	$btbDataArr=array();
	foreach($btb_sql_result as $rows){
		$btbDataArr[$rows[PI_ID]][PI_ID]=$rows[LC_NUMBER];
		$btbDataArr[$rows[PI_ID]][ID]=$rows[ID];
		$allBtbIdArr[$rows[ID]]=$rows[ID];
	}
	
	
	$btb_attached_sql="
	select a.IMPORT_MST_ID,b.EXPORT_LC_NO from COM_BTB_EXPORT_LC_ATTACHMENT a,COM_EXPORT_LC b where a.LC_SC_ID=b.id and  a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_LC_SC=0 and  a.IMPORT_MST_ID in(".implode(',',$allBtbIdArr).")
	union all
	select a.IMPORT_MST_ID,b.CONTRACT_NO as EXPORT_LC_NO from COM_BTB_EXPORT_LC_ATTACHMENT a,com_sales_contract b where a.LC_SC_ID=b.id and  a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_LC_SC=1 and  a.IMPORT_MST_ID in(".implode(',',$allBtbIdArr).")";
	//echo $btb_attached_sql;
	$btb_attached_sql_result=sql_select($btb_attached_sql);
	$btb_attached_file_arr=array();
	foreach($btb_attached_sql_result as $rows){
		$btb_attached_file_arr[$rows[IMPORT_MST_ID]][$rows[EXPORT_LC_NO]]=$rows[EXPORT_LC_NO];
	
	}
 
 
 //print_r($piDataArr);die;
   
 	
	//.......................................................................
	    $order_job_qnty=$row[csf("job_quantity")];
		$avg_unit_price=$avg_unite_price;
		$order_values = $job_quantity*$avg_unite_price;

 		
		if($COSTING_PER==1){$order_price_per_dzn=12;$costing_for=" DZN";}
		else if($COSTING_PER==2){$order_price_per_dzn=1;$costing_for=" PCS";}
		else if($COSTING_PER==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
		else if($COSTING_PER==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
		else if($COSTING_PER==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}


		foreach($pre_cost_commercial_sql_result as $row){
			$otherCostDataArr[AMOUNT][1] +=$row[AMOUNT]; 
			$otherCostDataArr[RATE][1] =$row[RATE]; 
		}
		
		foreach($pre_cost_others_sql_result as $row){
			$otherCostDataArr[AMOUNT][2] +=($row[csf("freight")]/$order_price_per_dzn)*$order_quantity;
			$otherCostDataArr[AMOUNT][3] +=($row[csf("lab_test")]/$order_price_per_dzn)*$order_quantity;
		}
		
		foreach($pre_cost_commission_sql_result as $row){
			$otherCostDataArr[AMOUNT][4] += ($row[csf("commission_amount")]/$order_price_per_dzn)*$order_quantity; 
		}
		
		$commission_par_pcs=$otherCostDataArr[AMOUNT][4]/$order_quantity;
	
	
	
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
            
            
            <table width="1900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">ORDER STATUS</th>
                       <th colspan="15"></th>
                   </tr>
                   <tr>
                       <th width="100">Buyer</th>
                       <th width="150">Style Name</th>
                       <th width="100">PO No</th>
                       <th width="100">Garments Item</th>
                       <th width="100">LC/SC No</th>
                       <th width="100">File No</th>
                       <th width="100">Concern Merchandiser</th>
                       <th width="100">UOM</th>
                       <th width="100">PO Quantity (Pcs)</th>
                       <th width="100">Factory Unit Price</th>
                       <th width="100">Total Factory Value</th>
                       <th width="100">Commission/Unit</th>
                       <th width="100">Total Commission</th>
                       <th width="100">Unit Price</th>
                       <th width="100">Total PO Value</th>
                       <th width="100">Shipment Date</th>
                       <th width="100">SMV</th>
                       <th>Costing Per</th>
                   </tr>
               </thead>
               <tbody>
                 <?php 
				 $i=1;
				 foreach($job_sql_result as $row){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$FactoryUnitPrice=($row[AMOUNT]/$row[PO_QUANTITY_PCS])-$commission_par_pcs
				 
				 ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td><?= $buyer_lib[$row[BUYER_NAME]];?></td>
                    <td><?= $row[STYLE_REF_NO];?></td>
                    <td><?= $row[PO_NUMBER];?></td>
                    <td align="center"><?= $garments_item[$row[GMTS_ITEM_ID]];?></td>
                    <td><?= $lcScDataArr[$row[PO_ID]][EXPORT_LC_NO]; ?></td>
                    <td align="center"><?= $lcScDataArr[$row[PO_ID]][INTERNAL_FILE_NO];?></td>
                    <td align="center"><?= $marchentrArr[$row[FACTORY_MARCHANT]];?></td>
                    <td align="center"><?= $unit_of_measurement[$row[ORDER_UOM]]; ?></td>
                    <td align="right"><?= $row[PO_QUANTITY_PCS];?></td>
                    <td align="right"><?= number_format($FactoryUnitPrice,2);?></td>
                    <td align="right"><?= number_format($FactoryUnitPrice*$row[PO_QUANTITY_PCS],2);?></td>
                    <td align="right"><?= number_format($commission_par_pcs,2);?></td>
                    <td align="right"><?= number_format($commission_par_pcs*$row[PO_QUANTITY_PCS],2);?></td>
                    <td align="right"><?= number_format(($row[AMOUNT]/$row[PO_QUANTITY_PCS]),2);?></td>
                    <td align="right"><?= number_format($row[AMOUNT],2);?></td>
                    <td align="center"><?= change_date_format($row[SHIPMENT_DATE]);?></td>
                    <td align="center"><?= $row[SET_SMV];?></td>
                    <td align="right"><?= $costing_for;?></td>
                 </tr>
                 <?php 
				 $i++;
				 }
				  ?>
              </tbody>
           </table>
            <br />
            
            
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">SHIPMENT STATUS</th>
                       <th colspan="14"></th>
                   </tr>
                   <tr>
                        <th width="100">Style Name</th>
                        <th width="100">PO No</th>
                        <th width="100">UOM</th>
                        <th width="100">Garments Item</th>
                        <th width="100">Shipped Quantity</th>
                        <th width="100">Unit Price</th>
                        <th width="100">Total Shipped Value</th>
                        <th width="100">Total Commission</th>
                        <th width="100">Actual Shipment Date</th>
                        <th width="100">Shipment Status</th>
                        <th width="100">Short Shipped Quantity</th>
                        <th width="100">Short Shipped Value</th>
                        <th width="100">Excess Shipped Quantity</th>
                        <th width="100">Excess Shipped Value</th>
                        <th width="100">Discount</th>
                        <th>Up-Charge Received</th>
                    </tr>
                </thead>
               <tbody>
                 <?php 
				 $i=1;
				 foreach($job_sql_result as $row){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$unite_price=($row[AMOUNT]/$row[PO_QUANTITY_PCS]);
					
					
					$ShortShippedQuantity='';
					$ShortShippedValue='';
					$ExcessShippedQuantity='';
					$ExcessShippedValue='';
					if($exfDataArr[$row[PO_ID]][SHIPING_STATUS]==3){
						$ShortShippedQuantity=($row[PO_QUANTITY_PCS]-$exfDataArr[$row[PO_ID]][EX_FACTORY_QNTY]);
						$ShortShippedValue=($unite_price*$ShortShippedQuantity);
						$ExcessShippedQuantity=($exfDataArr[$row[PO_ID]][EX_FACTORY_QNTY]-$row[PO_QUANTITY_PCS]);
						$ExcessShippedValue=($unite_price*$ExcessShippedQuantity);
					}
				 
				 ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $i; ?>">
                    <td><?= $row[STYLE_REF_NO];?></td>
                    <td><?= $row[PO_NUMBER];?></td>
                    <td align="center"><?= $unit_of_measurement[$row[ORDER_UOM]]; ?></td>
                    <td><?= $garments_item[$row[GMTS_ITEM_ID]];?></td>
                    <td align="right"><?= $exfDataArr[$row[PO_ID]][EX_FACTORY_QNTY];?></td>
                    <td align="right"><?= number_format($unite_price,2);?></td>
                    <td align="right"><?= number_format(($unite_price*$exfDataArr[$row[PO_ID]][EX_FACTORY_QNTY]),2);?></td>
                    <td align="right"><?= number_format(($commission_par_pcs*$exfDataArr[$row[PO_ID]][EX_FACTORY_QNTY]),2);?></td>
                    <td align="center"><?= $exfDataArr[$row[PO_ID]][SHIP_DATE];?></td>
                    <td align="center"><?= $shipment_status[$exfDataArr[$row[PO_ID]][SHIPING_STATUS]];?></td>
                    
                    <td align="right"><?= ($ShortShippedQuantity>0)? number_format($ShortShippedQuantity,2):'';?></td>
                    
                    <td align="right"><?= ($ShortShippedValue>0)? number_format($ShortShippedValue,2):'';?></td>
                    <td align="right"><? if($ExcessShippedQuantity>0) echo number_format($ExcessShippedQuantity,2);?></td>
                    <td align="right" title="<?= "ex qnty=".$exfDataArr[$row[PO_ID]][EX_FACTORY_QNTY]."po qnty=".$row[PO_QUANTITY_PCS]; ?>"><? if($ExcessShippedQuantity>0) echo number_format($ExcessShippedValue,2);?></td>
                    <td align="right"><?= number_format($exportInvoiceDataArr[$row[PO_ID]][DISCOUNT_AMMOUNT],4);?></td>
                    <td align="right"><?= number_format($exportInvoiceDataArr[$row[PO_ID]][UPCHARGE],4);?></td>
                 </tr>
                 <?php 
				 $i++;
				 }
				  ?>
              </tbody>
            </table>
			
            <br>
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">Fabric Cost</th>
                       <th colspan="12"></th>
                   </tr>
                   <tr>
                        <th width="200">Fabric Description</th>
                        <th width="50">UOM</th>
                        <th width="100">Cons</th>
                        <th width="100">Req(Gray)</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Cost Value</th>
                        <th width="100">Fabric Source</th>
                        <th width="100">Supplier Name</th>
                        <th width="100">PI No</th>
                        <th width="100">PI Quantity</th>
                        <th width="100">PI Rate</th>
                        <th width="100">Total PI Value</th>
                        <th width="100">Surplus / (Deficit)</th>
                        <th width="100">BTB No</th>
                        <th>BTB Opened From</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                     $i=1;
					 $fbTotalCostValue=0;$fbTotalPIValue=0;$fbSurplusDeficit=0;
                     foreach($pre_cost_fb_sql_result as $row){
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$key=$row[LIB_YARN_COUNT_DETER_ID].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION].'**'.$row[GSM_WEIGHT];
						
						
						$row[AMOUNT]=$fabric_amount_arr['knit']['grey'][$row[FB_COST_DTLS_ID]][$row[UOM]];
						//sub total................
						$fbTotalCostValue+=$row[AMOUNT];
						$color_id_arr=array_unique(explode(",",$row[FABRIC_COLOR_ID]));
						$pi_amount=$pi_qnty=$pi_rate=0;
						$supplier_id=$pi_num=$btb_num=$export_lc="";
						foreach($color_id_arr as $book_color_id)
						{
							$key=$row[LIB_YARN_COUNT_DETER_ID].'**'.$row[CONSTRUCTION].'**'.$row[COMPOSITION].'**'.$row[GSM_WEIGHT].'**'.$book_color_id;
							$pi_qnty+=$fbPiDataArr[$key][QTY];
							$pi_amount+=$fbPiDataArr[$key][AMOUNT];
							$supplier_id=$fbPiDataArr[$key][SUPPLIER_ID];
							$pi_num =implode(',',$fbPiDataArr[$key][PI_NUMBER]);
							$pi_id=$fbPiDataArr[$key][PI_ID];
							if($btb_check[$btbDataArr[$pi_id][PI_ID]]=="")
							{
								$btb_check[$btbDataArr[$pi_id][PI_ID]]=$btbDataArr[$pi_id][PI_ID];
								$btb_num.=$btbDataArr[$pi_id][PI_ID].",";
							}
							if(count($btb_attached_file_arr[$btbDataArr[$pi_id][ID]])>0 && $export_lc_check[implode(',',$btb_attached_file_arr[$btbDataArr[$pi_id][ID]])]=="" )
							{
								$export_lc_check[implode(',',$btb_attached_file_arr[$btbDataArr[$pi_id][ID]])]=implode(',',$btb_attached_file_arr[$btbDataArr[$pi_id][ID]]);
								$export_lc.=implode(',',$btb_attached_file_arr[$btbDataArr[$pi_id][ID]]).",";
							}
							
						}
						$btb_num=chop($btb_num,",");
						$export_lc=chop($export_lc,",");
						if($pi_amount>0 && $pi_qnty>0) $pi_rate=$pi_amount/$pi_qnty;
						$fbTotalPIValue+=$pi_amount;
						$fbSurplusDeficit+=$row[AMOUNT]-$pi_amount;
						$fincons=$fabric_qty_arr['knit']['grey'][$row[FB_COST_DTLS_ID]][$row[UOM]];
						$item_descrition = $body_part[$row[BODY_PART_ID]].", ".$color_type[$row[COLOR_TYPE_ID]].", ".$row[csf("FABRIC_DESCRIPTION")];
                     ?>
                     <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr3_<? echo $i; ?>">
                        <td><?= $item_descrition;?></td>
                        <td align="center"><?= $unit_of_measurement[$row[UOM]]; ?></td>
                        <td align="center"><?= $row[CONS];?></td>
                        <td align="right"><?= number_format($fincons,4);?></td>
                        <td align="right"><?= number_format($row[RATE],4);?></td>
                        <td align="right"><?= number_format($row[AMOUNT],4);?></td>
                        <td align="center"><?= $fabric_source[$row[FABRIC_SOURCE]];?></td>
                        <td align="center"><?= $supplier_library[$supplier_id];?></td>
                        <td align="center"><?= $pi_num;?></td>
                        <td align="right"><?= number_format($pi_qnty,4);?></td>
                        <td align="right"><?= number_format($pi_rate,4);?></td>
                        <td align="right"><?= number_format($pi_amount,4);?></td>
                        <td align="right"><?= number_format(($row[AMOUNT]-$pi_amount),2);?></td>
                        <td align="center"><? echo $btb_num; ?></td>
                        <td align="center"><? echo $export_lc;?></td>
                     </tr>
                     <?php 
                     $i++;
                     }
                      ?>
                  </tbody>
                  <tfoot>
                  	<th colspan="5">Sub Total:</th>
                    <th><?= number_format($fbTotalCostValue,2);?></th>
                  	<th colspan="5"></th>
                    <th><?= number_format($fbTotalPIValue,2);?></th>
                    <th><?= number_format($fbSurplusDeficit,2);?></th>
                  	<th colspan="2"></th>
                  </tfoot>
               </table>

            
            <br>
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">Yarn Cost</th>
                       <th colspan="12"></th>
                   </tr>
                   <tr>
                        <th width="200">Yarn Description</th>
                        <th width="50">UOM</th>
                        <th width="50">Cons</th>
                        <th width="100">Req(Gray)</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Cost Value</th>
                        <th width="100">Yarn Source</th>
                        <th width="100">Supplier Name</th>
                        <th width="100">PI No</th>
                        <th width="100">PI Quantity</th>
                        <th width="100">PI Rate</th>
                        <th width="100">Total PI Value</th>
                        <th width="100">Surplus / (Deficit)</th>
                        <th width="100">BTB No</th>
                        <th>BTB Opened From</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                     $i=1;
                     $yarnTotalCostValue=0;$yarnTotalPIValue=0;$yarnSurplusDeficit=0;
					 foreach($pre_cost_yarn_sql_result as $row)
					 {
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						
						$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
						
						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						
						$rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
							
						$key=$row[csf("count_id")].'**'.$row[csf("copm_one_id")].'**'.$row[csf("percent_one")].'**'.$row[csf("type_id")];
						
                        //sub total................
						$yarnTotalCostValue+=$rowamount;
						$yarnTotalPIValue+=$yarnPiDataArr[$key][AMOUNT];
						$yarnSurplusDeficit+=$rowamount-$yarnPiDataArr[$key][AMOUNT];
						
						$supplier_name='';
						foreach($yarnPiDataArr[$key][SUPPLIER_ID] as $sup_id)
						{
							$supplier_name.=$supplier_library[$sup_id].",";
						}
                     
                     ?>
                     <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr4_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr4_<? echo $i; ?>">
                        <td title="<?= $row[csf("type_id")]; ?>"><?= $item_descrition;?></td>
                        <td align="center">Kg <?  //$costing_for;?></td>
                        <td align="center"><?=  $row[csf("cons_qnty")]; ?></td>
                        <td align="right"><?= number_format($rowcons_qnty,4);?></td>
                        <td align="right"><?= number_format($row[csf("rate")],4); ?></td>
                        <td align="right"><?= number_format($rowamount,2); ?></td>
                        <td align="center"><?= $source[$yarnPiDataArr[$key][SOURCE]];?></td>
                        <td align="center"><?= chop($supplier_name,","); ?></td>
                        <td align="center"><?= implode(',',$yarnPiDataArr[$key][PI_NUMBER]);?></td>
                        <td align="right"><?= number_format($yarnPiDataArr[$key][QTY],4);?></td>
                        <td align="right"><?= number_format($yarnPiDataArr[$key][AMOUNT]/$yarnPiDataArr[$key][QTY],4);?></td>
                        <td align="right"><?= number_format($yarnPiDataArr[$key][AMOUNT],4);?></td>
                        <td align="right"><?= number_format(($rowamount-$yarnPiDataArr[$key][AMOUNT]),2);?></td>
                        <td align="center"><?
						//$btbDataArr[$yarnPiDataArr[$key][PI_ID]][PI_ID];
						
						$btbTempArr=array();
						foreach($yarnPiDataArrs[$key][PI_ID] as $piid){
							if($btbDataArr[$piid][PI_ID]){$btbTempArr[$btbDataArr[$piid][PI_ID]]= $btbDataArr[$piid][PI_ID];}
						}
						echo implode(',',$btbTempArr);
						
						?></td>
                        <td align="center"><?
							//implode(',',$btb_attached_file_arr[$btbDataArr[$yarnPiDataArr[$key][PI_ID]][ID]]);
							$btbTempArr=array();
							foreach($yarnPiDataArrs[$key][PI_ID] as $piid){
								if(count($btb_attached_file_arr[$btbDataArr[$piid][ID]])){
									$btbTempArr[implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]])]= implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]]);
								}
							}
							echo implode(',',$btbTempArr);
						
						?></td>
                     </tr>
                     <?php 
                     $i++;
                     }
                      ?>
                  </tbody>
                  <tfoot>
                  	<th colspan="5">Sub Total:</th>
                    <th><?= number_format($yarnTotalCostValue,2);?></th>
                  	<th colspan="5"></th>
                    <th><?= number_format($yarnTotalPIValue,2);?></th>
                    <th><?= number_format($yarnSurplusDeficit,2);?></th>
                  	<th colspan="2"></th>
                  </tfoot>
                  
                  
               </table>

            
            <br>
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">Knitting Cost</th>
                       <th colspan="12"></th>
                   </tr>
                   <tr>
                        <th width="200">Fabric Description</th>
                        <th width="50">UOM</th>
                        <th width="50">Cons</th>
                        <th width="100">Req(Gray)</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Cost Value</th>
                        <th width="100">Knitting Source</th>
                        <th width="100">Knitting Company</th>
                        <th width="100">PI No</th>
                        <th width="100">PI Quantity</th>
                        <th width="100">PI Rate</th>
                        <th width="100">Total PI Value</th>
                        <th width="100">Surplus / (Deficit)</th>
                        <th width="100">BTB No</th>
                        <th>BTB Opened From</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                     $i=1;
					 $knitTotalCostValue=0;$knitTotalPIValue=0;$knitSurplusDeficit=0;
                     foreach($pre_cost_knit_sql_result as $row){
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
						
						//$amount=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
						$convsion_qty=$conv_data_qty[$row[csf('id')]][$row[csf('uom')]];
						
						$row[csf('amount')]=$convsion_qty*$row[csf("charge_unit")];
						
						//$key=$item_descrition;
						$key=$row[csf('id')].'**'.$row[csf('uom')];
                        //sub total................
						$knitTotalCostValue+=$row[csf('amount')];
						$knitTotalPIValue+=$knitPiDataArr[$key][AMOUNT];
						$knitSurplusDeficit+=$row[csf('amount')]-$knitPiDataArr[$key][AMOUNT];
                     
                     ?>
                     <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr5_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr5_<? echo $i; ?>">
                        <td><?= $item_descrition;?></td>
                        <td align="center"><?= $unit_of_measurement[$row[csf("uom")]];?></td>
                        <td align="right"><?= number_format($row[csf("avg_cons")],4);?></td>
                        <td align="right"><?= number_format($convsion_qty,4);?></td>
                        <td align="right"><?= number_format($row[csf("charge_unit")],4);?></td>
                        <td align="right"><?= number_format($row[csf('amount')],4);?></td>
                        <td align="center"><?= ($supplier_library[$knitPiDataArr[$key]["SUPPLIER_ID"]]!="")? "Out-Bound" : ""; ?></td>
                        <td><?= $supplier_library[$knitPiDataArr[$key]["SUPPLIER_ID"]];?></td>
                        <td align="center"><?= implode(',',$knitPiDataArr[$key][PI_NUMBER]);?></td>
                        <td align="right"><?= number_format($knitPiDataArr[$key][QTY],4);?></td>
                        <td align="right"><?= number_format($knitPiDataArr[$key][RATE],4);?></td>
                        <td align="right"><?= number_format($knitPiDataArr[$key][AMOUNT],4);?></td>
                        <td align="right"><?= number_format(($row[csf('amount')]-$knitPiDataArr[$key][AMOUNT]),2);?></td>
                        <td align="center"><?
						//$btbDataArr[$knitPiDataArr[$key][PI_ID]][PI_ID];
						$btbTempArr=array();
						foreach($knitPiDataArrs[$key][PI_ID] as $piid){
							if($btbDataArr[$piid][PI_ID]){$btbTempArr[$btbDataArr[$piid][PI_ID]]= $btbDataArr[$piid][PI_ID];}
						}
						echo implode(',',$btbTempArr);
						?></td>
                        <td align="center"><?
						//implode(',',$btb_attached_file_arr[$btbDataArr[$knitPiDataArr[$key][PI_ID]][ID]]);
							$btbTempArr=array();
							foreach($knitPiDataArrs[$key][PI_ID] as $piid){
								if(count($btb_attached_file_arr[$btbDataArr[$piid][ID]])){
									$btbTempArr[implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]])]= implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]]);
								}
							}
							echo implode(',',$btbTempArr);
						?></td>
                     </tr>
                     <?php 
                     $i++;
                     }
                      ?>
                  </tbody>
                  <tfoot>
                  	<th colspan="5">Sub Total:</th>
                    <th><?= number_format($knitTotalCostValue,2);?></th>
                  	<th colspan="5"></th>
                    <th><?= number_format($knitTotalPIValue,2);?></th>
                    <th><?= number_format($knitSurplusDeficit,2);?></th>
                  	<th colspan="2"></th>
                  </tfoot>
               </table>
               
            <br>
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">Dyeing And Finishing Cost</th>
                       <th colspan="12"></th>
                   </tr>
                   <tr>
                        <th width="200">Fabric Description With Process</th>
                        <th width="50">UOM</th>
                        <th width="50">Cons</th>
                        <th width="100">Req(Finish)</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Cost Value</th>
                        <th width="100">Dyeing/Finishing Source</th>
                        <th width="100">Dyeing Company</th>
                        <th width="100">PI No</th>
                        <th width="100">PI Quantity</th>
                        <th width="100">PI Rate</th>
                        <th width="100">Total PI Value</th>
                        <th width="100">Surplus / (Deficit)</th>
                        <th width="100">BTB No</th>
                        <th>BTB Opened From</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                     $i=1;
					 $dfTotalCostValue=0;$dfTotalPIValue=0;$dfSurplusDeficit=0;
					//foreach( $conversion_data_array as $cons_process_id=>$cons_process_idArr ){
					 
					 foreach($pre_cost_conversion_sql_result as $id=>$row )
					 {
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
						
						//$amount=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
						$convsion_qty=$conv_data_qty[$row[csf('id')]][$row[csf('uom')]];
						
					   $key=$row[csf('id')].'**'.$row[csf('uom')];
					   
					   $row[csf('amount')]=$convsion_qty*$row[csf("charge_unit")];
					   
					    //sub total................
						$dfTotalCostValue+=$row[csf('amount')];
						$dfTotalPIValue+=$dfPiDataArr[$key][AMOUNT];
						$dfSurplusDeficit+=$row[csf('amount')]-$dfPiDataArr[$key][AMOUNT];
                     
                     ?>
                     <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr6_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr6_<? echo $i; ?>">
                        <td title="<?= "process id =". $row[csf("cons_process")]; ?>"><b><?= $conversion_cost_head_array[$row[csf("cons_process")]];?></b> --<?= $item_descrition;?></td>
                        <td align="center"><?= $unit_of_measurement[$row[csf("uom")]];?></td>
                        <td align="right"><?= number_format($row[csf("avg_cons")],4);?></td>
                        <td align="right"><?= number_format($convsion_qty,4);?></td>
                        <td align="right"><?= number_format($row[csf("charge_unit")],4);?></td>
                        <td align="right"><?= number_format($row[csf('amount')],4);?></td>
                        <td align="center"><?= ($supplier_library[$dfPiDataArr[$key][SUPPLIER_ID]]!="")? "Out-Bound":"";?></td>
                        <td><?= $supplier_library[$dfPiDataArr[$key][SUPPLIER_ID]];?></td>
                        <td align="center"><?= implode(',',$dfPiDataArr[$key][PI_NUMBER]);?></td>
                        <td align="right"><?= number_format($dfPiDataArr[$key][QTY],4);?></td>
                        <td align="right"><?= number_format($dfPiDataArr[$key][RATE],4);?></td>
                        <td align="right"><?= number_format($dfPiDataArr[$key][AMOUNT],4);?></td>
                        <td align="right"><?= number_format(($row[csf('amount')]-$dfPiDataArr[$key][AMOUNT]),2);?></td>
                        <td align="center"><?
						 //$btbDataArr[$dfPiDataArr[$key][PI_ID]][PI_ID];
						$btbTempArr=array();
						foreach($dfPiDataArrs[$key][PI_ID] as $piid){
							if($btbDataArr[$piid][PI_ID]){$btbTempArr[$btbDataArr[$piid][PI_ID]]= $btbDataArr[$piid][PI_ID];}
						}
						echo implode(',',$btbTempArr);
						 ?></td>
                        <td align="center"><?
						//implode(',',$btb_attached_file_arr[$btbDataArr[$dfPiDataArr[$key][PI_ID]][ID]]);
							$btbTempArr=array();
							foreach($dfPiDataArrs[$key][PI_ID] as $piid){
								if(count($btb_attached_file_arr[$btbDataArr[$piid][ID]])){
									$btbTempArr[implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]])]= implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]]);
								}
							}
							echo implode(',',$btbTempArr);
						?></td>
                     </tr>
                     <?php 
                     $i++;
                     }
					//}
                      ?>
                  </tbody>
                  
                   <tfoot>
                      <tr>
                            <th colspan="5">Sub Total:</th>
                            <th><?= number_format($dfTotalCostValue,2);?></th>
                            <th colspan="5"></th>
                            <th><?= number_format($dfTotalPIValue,2);?></th>
                            <th><?= number_format($dfSurplusDeficit,2);?></th>
                            <th colspan="2"></th>
                      </tr>
                       <tr>
                            <th colspan="5">Total Fabrication Cost:</th>
                            <th><?= number_format(($fbTotalCostValue+$yarnTotalCostValue+$knitTotalCostValue+$dfTotalCostValue),2);?></th>
                            <th colspan="5"></th>
                            <th><?= number_format(($fbTotalPIValue+$yarnTotalPIValue+$knitTotalPIValue+$dfTotalPIValue),2);?></th>
                            <th><?= number_format(($fbSurplusDeficit+$yarnSurplusDeficit+$knitSurplusDeficit+$dfSurplusDeficit),2);?></th>
                            <th colspan="2"></th>
                         </tr>
                     </tfoot>
                  
               </table>
               
            <br>
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">Trims Cost</th>
                       <th colspan="12"></th>
                   </tr>
                   <tr>
                        <th width="200">Item Group</th>
                        <th width="50">UOM</th>
                        <th width="50">Cons</th>
                        <th width="100">Req</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Value</th>
                        <th width="100">Item Source</th>
                        <th width="100">Supplier Name</th>
                        <th width="100">PI No</th>
                        <th width="100">Quantity</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Value</th>
                        <th width="100">Surplus / (Deficit)</th>
                        <th width="100">BTB No</th>
                        <th>BTB Opened From</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                     $i=1;
					 $trimTotalCostValue=0;$trimTotalPIValue=0;$trimSurplusDeficit=0;
                     foreach($pre_cost_trims_sql_result as $row){
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$key=$row[csf("trim_group")];
                        //sub total................
						$pre_id_array=array_unique(explode(",",$row[csf("id")]));
						$budge_amt=$budge_qnty=0;
						foreach($pre_id_array as $pre_id)
						{
							$budge_amt+=$trim_amount[$pre_id];
							$budge_qnty+=$trim_qty_arr[$pre_id];
						}
						$trimTotalCostValue+=$budge_amt;
						$trimTotalPIValue+=$trimsPiDataArr[$key][AMOUNT];
						$trimSurplusDeficit+=$budge_amt-$trimsPiDataArr[$key][AMOUNT];
						$bud_rate=0;
						if($budge_amt>0 && $budge_qnty>0) $bud_rate=$budge_amt/$budge_qnty;
					
					 
                     ?>
                     <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr7_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr7_<? echo $i; ?>">
                        <td><?= $trim_group[$row[csf("trim_group")]]; ?></td>
                        <td align="center"><?= $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                        <td align="center"><?= $row[CONS_DZN_GMTS]; ?></td>
                        <td align="right"><?= number_format($budge_qnty,4);?></td>
                        <td align="right"><?= number_format($bud_rate,4); ?></td>
                        <td align="right"><?= number_format($budge_amt,4);?></td>
                        <td align="center"><?= $source[$trimsPiDataArr[$key][SOURCE]];?></td>
                        <td align="center"><?= $supplier_library[$trimsPiDataArr[$key][SUPPLIER_ID]];?></td>
                        <td align="center"><?= implode(',',$trimsPiDataArr[$key][PI_NUMBER]);?></td>
                        <td align="right"><?= number_format($trimsPiDataArr[$key][QTY],4);?></td>
                        <td align="right"><?= number_format($trimsPiDataArr[$key][RATE],4);?></td>
                        <td align="right"><?= number_format($trimsPiDataArr[$key][AMOUNT],4);?></td>
                        <td align="right"><?= number_format(($budge_amt-$trimsPiDataArr[$key][AMOUNT]),2);?></td>
                        <td align="center"><?
                        //$btbDataArr[$trimsPiDataArr[$key][PI_ID]][PI_ID];
						$btbTempArr=array();
						foreach($trimsPiDataArrs[$key][PI_ID] as $piid){
							if($btbDataArr[$piid][PI_ID]){$btbTempArr[$btbDataArr[$piid][PI_ID]]= $btbDataArr[$piid][PI_ID];}
						}
						echo implode(',',$btbTempArr);
						?></td>
                        <td align="center"><?
						//implode(',',$btb_attached_file_arr[$btbDataArr[$trimsPiDataArr[$key][PI_ID]][ID]]);
							$btbTempArr=array();
							foreach($trimsPiDataArrs[$key][PI_ID] as $piid){
								if(count($btb_attached_file_arr[$btbDataArr[$piid][ID]])){
									$btbTempArr[implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]])]= implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]]);
								}
							}
							echo implode(',',$btbTempArr);
						?></td>
                     </tr>
                     <?php 
                     $i++;
                     }
                      ?>
                  </tbody>
                  <tfoot>
                  	<th colspan="5">Sub Total:</th>
                    <th><?= number_format($trimTotalCostValue,2);?></th>
                  	<th colspan="5"></th>
                    <th><?= number_format($trimTotalPIValue,2);?></th>
                    <th><?= number_format($trimSurplusDeficit,2);?></th>
                  	<th colspan="2"></th>
                  </tfoot>
               </table>
               
               
            <br>
            <table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th colspan="3">Embellishment Cost</th>
                       <th colspan="12"></th>
                   </tr>
                   <tr>
                        <th width="200">Embellisment Name, Type</th>
                        <th width="50">UOM</th>
                        <th width="50">Cons</th>
                        <th width="100">Req</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Value</th>
                        <th width="100">Item Source</th>
                        <th width="100">Supplier Name</th>
                        <th width="100">PI No</th>
                        <th width="100">Quantity</th>
                        <th width="100">Rate</th>
                        <th width="100">Total Value</th>
                        <th width="100">Surplus / (Deficit)</th>
                        <th width="100">BTB No</th>
                        <th>BTB Opened From</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                     $i=1;
					 $embTotalCostValue=0;$embTotalPIValue=0;$embSurplusDeficit=0;
					//echo "<pre>";print_r($pre_cost_emb_sql_result);die;
                     foreach($pre_cost_emb_sql_result as $row)
					 {
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$key=$row[EMB_NAME].'**'.$row[EMB_TYPE];
						//echo $key."<br>";
						$embl_cons_gmts=0;
						$emb_budg_id_arr=array_unique(explode(",",$row[ID]));
						foreach($emb_budg_id_arr as $budg_id)
						{
							if($row[EMB_NAME] !=3)
							{
								$embl_cons_gmts+=$emblishment_qty_arr[$row[JOB_NO]][$budg_id];
							}
							else
							{
								$embl_cons_gmts+=$wash_qty_arr[$row[JOB_NO]][$budg_id];
							}
						}
						
						
						$row["AMOUNT"]=$embl_cons_gmts*$row["RATE"];
						
						//sub total................
						$embTotalCostValue+=$row[csf("amount")];
						$embTotalPIValue+=$embPiDataArr[$key][AMOUNT];
						$embSurplusDeficit+=$row[csf("amount")]-$embPiDataArr[$key][AMOUNT];
						
						if($row[EMB_NAME]==1)
							$emb_type_arr=$emblishment_print_type;
						else if($row[EMB_NAME]==2)
							$emb_type_arr=$emblishment_embroy_type;
						else if($row[EMB_NAME]==3)
							$emb_type_arr=$emblishment_wash_type;
						else if($row[EMB_NAME]==4)
							$emb_type_arr=$emblishment_spwork_type;
						else
							$emb_type_arr=$blank_array;
						
						if($row["AMOUNT"])
						{
							$emb_rate=$pi_rate=0;
							if($row[csf("amount")]>0 && $embl_cons_gmts>0) $emb_rate=$row[csf("amount")]/$embl_cons_gmts;
							if($embPiDataArr[$key][AMOUNT]>0 && $embPiDataArr[$key][QTY]>0) $pi_rate=$embPiDataArr[$key][AMOUNT]/$embPiDataArr[$key][QTY];
							?>
							 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr8_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr8_<? echo $i; ?>">
								<td><?= $emblishment_name_array[$row[csf("emb_name")]].",". $emb_type_arr[$row[EMB_TYPE]]; ?></td>
								<td align="center"><?= $costing_for;?></td>
								<td align="right"><?= number_format($row[csf("cons_dzn_gmts")],4); ?></td>
								<td align="right"><?= number_format($embl_cons_gmts,4); ?></td>
								<td align="right"><?= number_format($emb_rate,4); ?></td>
								<td align="right"><?= number_format($row["AMOUNT"],4); ?></td>
								<td align="center"><?= $source[$embPiDataArr[$key][SOURCE]];?></td>
								<td align="center"><?= $supplier_library[$embPiDataArr[$key][SUPPLIER_ID]];?></td>
								<td align="center"><?= implode(',',$embPiDataArr[$key][PI_NUMBER]);?></td>
								<td align="right"><?= number_format($embPiDataArr[$key][QTY],4);?></td>
								<td align="right"><?= number_format($pi_rate,4);?></td>
								<td align="right"><?= number_format($embPiDataArr[$key][AMOUNT],4);?></td>
								<td align="right"><?= number_format(($row["AMOUNT"]-$embPiDataArr[$key][AMOUNT]),2);?></td>
								<td align="center"><?
								//$btbDataArr[$embPiDataArr[$key][PI_ID]][PI_ID];
								$btbTempArr=array();
								foreach($embPiDataArr[$key][PI_ID] as $piid){
									if($btbDataArr[$piid][PI_ID]){$btbTempArr[$btbDataArr[$piid][PI_ID]]= $btbDataArr[$piid][PI_ID];}
								}
								echo implode(',',$btbTempArr);
								
								?></td>
								<td align="center"><?
								// implode(',',$btb_attached_file_arr[$btbDataArr[$embPiDataArr[$key][PI_ID]][ID]]);
								$btbTempArr=array();
								foreach($embPiDataArr[$key][PI_ID] as $piid){
									if(count($btb_attached_file_arr[$btbDataArr[$piid][ID]])){
										$btbTempArr[implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]])]= implode(',',$btb_attached_file_arr[$btbDataArr[$piid][ID]]);
									}
								}
								echo implode(',',$btbTempArr);
								?></td>
							 </tr>
							 <? 
							 $i++;
						}
                     }
                      ?>
                  </tbody>
                  <tfoot>
                  	<th colspan="5">Sub Total:</th>
                    <th><?= number_format($embTotalCostValue,2);?></th>
                  	<th colspan="5"></th>
                    <th><?= number_format($embTotalPIValue,2);?></th>
                    <th><?= number_format($embSurplusDeficit,2);?></th>
                  	<th colspan="2"></th>
                  </tfoot>
               </table>
               
               
            <br>
            <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                   <tr>
                       <th width="100">Other Cost</th>
                       <th width="100">Rate</th>
                       <th width="100">TTL VALUE</th>
                       <th width="100">Supplier Name</th>
                       <th width="100">PI No</th>
                       <th width="100">Quantity</th>
                       <th width="100">Rate</th>
                       <th width="100">Total PI Value</th>
                       <th width="100">Surplus / (Deficit)</th>
                       <th width="100">BTB No</th>
                       <th>BTB Opened under </th>
                   </tr>
                </thead>
                   <tbody>
                     <?php 
					 
					 $i=1;
					 $other_cost_head_arr=array(1=>'Commercial Cost',2=>'Freight Cost',3=>'Testing Cost',4=>'Commission'
);
                     foreach($other_cost_head_arr as $indexKey=>$other_cost_head){
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                     
                     ?>
                     <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr9_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr9_<? echo $i; ?>">
                        <td><?= $other_cost_head;?></td>
                        <td align="center"><?= number_format($otherCostDataArr[RATE][$indexKey],4);?></td>
                        <td align="center"><?= number_format($otherCostDataArr[AMOUNT][$indexKey],4);?></td>
                        <td> </td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                     </tr>
                     <?php 
                     $i++;
                     }
                      ?>
                  </tbody>
              </table>  
             <br>    
			<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
              	<thead>
                    <th></th>
                    <th>Pre-Cost/Unit</th>
                    <th>Pre-Cost Total</th>
                    <th>Post Cost/Unit</th>
                    <th>Post Cost Total</th>
               </thead>
              	<tbody>
              	<tr>
                    <td><strong>CM Value (Contribution)</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
               	</tr>
              	<tr bgcolor="#FFFFFF">
                    <td><strong>CM Cost</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
               	</tr>
              	<tr>
                    <td><strong>Margin</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
               	</tr>
                
                
               </tbody>
             </table>

 
               

            </fieldset>
        </div>
<?
	
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
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





?>