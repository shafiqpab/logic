<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");


require_once('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.trims.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
include('../../includes/class4/class.conversions.php');



$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_job_no").val(str);
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
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'order_status_details_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	
	
	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no, to_char(a.insert_date,'YYYY') as year  from wo_po_details_master a where a.company_name=$company_id $buyer_cond $search_con order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_name		=	str_replace("'","",$cbo_company_name);
	$cbo_buyer_name			=	str_replace("'","",$cbo_buyer_name);
	$txt_job_no				=	str_replace("'","",$txt_job_no);
	$txt_style				=	str_replace("'","",$txt_style);
	$txt_internal_ref		=	str_replace("'","",$txt_internal_ref);
	$txt_order_no			=	str_replace("'","",$txt_order_no);
	$txt_date_from			=	str_replace("'","",$txt_date_from);
	$txt_date_to			=	str_replace("'","",$txt_date_to);
	
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $where_cond .=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$where_cond.=" and a.buyer_name=$cbo_buyer_name";
	}
	
	
	if(trim($txt_job_no)!="") {$where_cond.=" and a.job_no like('%$txt_job_no')";}
	if(trim($txt_style)!="") {$where_cond.=" and a.style_ref_no like('%$txt_style')";}
	if(trim($txt_order_no)!="") {$where_cond.=" and b.po_number like('%$txt_order_no')";}
	if(trim($txt_internal_ref)!="") {$where_cond.=" and b.grouping like('%$txt_internal_ref')";}
	
	if($txt_date_from!='' && $txt_date_to!=''){
		$where_cond.=" and b.SHIPMENT_DATE between '$txt_date_from' and '$txt_date_to'";
	}
	
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
		
	 $orderSql="SELECT a.SHIP_MODE,a.BUYER_NAME, a.JOB_NO,b.id as PO_ID,b.INSERT_DATE,b.UPDATE_DATE, b.PO_NUMBER, (a.total_set_qnty*b.po_quantity) as PO_QNTY,b.UNIT_PRICE,b.PO_TOTAL_PRICE,B.SHIPING_STATUS,B.STATUS_ACTIVE,b.SHIPMENT_DATE, b.grouping,a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name $where_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 order by b.pub_shipment_date, b.id";
	   //echo $orderSql;die;
	$orderSqlRes=sql_select($orderSql);
	if(empty($orderSqlRes))
	{
		echo '<div align="left" style="width:100%;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	
	$poDataArr=array();
	foreach($orderSqlRes as $row)
	{
		$poDataArr[$row[PO_ID]]=$row;
		$allPoArr[$row[PO_ID]]=$row[PO_ID];
	}
	//unset($orderSqlRes);
	//$txt_job_no='tttty';
	
	$condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
	 if(str_replace("'","",$txt_job_no) !=''){
		  $condition->job_no("='$txt_job_no'");
	 }
	 if(implode(',',$allPoArr)!='')
	 {
		$condition->po_id_in(implode(',',$allPoArr)); 
	 }

	$condition->init();
	$fabric= new fabric($condition);
	$finish_fabric_req_qty_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();	
	$finish_fabric_req_qty_arr=$fabric->getQuery();	
	//echo $finish_fabric_req_qty_arr;die;
		
	$yarn= new yarn($condition);
	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyAndAmountArray();
		
	
	
	$conversion= new conversion($condition);
	$conversion_cost_arr=$conversion->getAmountArray_by_orderAndGmtsitem();	

	$emblishments= new emblishment($condition);
	$emblishments_cost_arr=$emblishments->getAmountArray_by_orderAndEmbname();	
	
	
	$wash= new wash($condition);
	$wash_cost_arr=$wash->getAmountArray_by_orderAndEmbname();	
	
	
	$trims= new trims($condition);
	$trims_cost_arr=$trims->getAmountArray_by_orderAndItemid();	
	
	
	
	foreach($allPoArr as $po_id)
	{
		$fabricCostByPoArr[$po_id]=array_sum($finish_fabric_req_qty_arr[knit][finish][$po_id]);
		$fabricCostByPoArr[$po_id]=$yarn_req_qty_arr[$po_id][amount];

		foreach($conversion_cost_arr[$po_id] as $conversionArr)
		{
			$fabricCostByPoArr[$po_id]+=array_sum($conversionArr);
		}
		
		$emblishCostByPoArr[$po_id]=array_sum($emblishments_cost_arr[$po_id]);
		$washCostByPoArr[$po_id]=array_sum($wash_cost_arr[$po_id]);
		$trimsCostByPoArr[$po_id]=array_sum($trims_cost_arr[$po_id]);
	}
		

	$gmtsProSql="SELECT  PO_BREAK_DOWN_ID as PO_ID,
				sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS CUTTING_QTY,
				sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS PRINT_ISSUE_QTY_IN,
				sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS PRINT_ISSUE_QTY_OUT,
				sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS PRINT_RECV_QTY_IN,
				sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS PRINT_RECV_QTY_OUT,
				sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS EMB_ISSUE_QTY_IN,
				sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS EMB_ISSUE_QTY_OUT,
				sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS EMB_RECV_QTY_IN,
				sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS EMB_RECV_QTY_OUT,
				sum(CASE WHEN production_type=2 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS WASH_ISSUE_QTY_IN,
				sum(CASE WHEN production_type=2 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS WASH_ISSUE_QTY_OUT,
				sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS WASH_RECV_QTY_IN,
				sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS WASH_RECV_QTY_OUT,
				sum(CASE WHEN production_type=2 and embel_name=4 and production_source=1 THEN production_quantity ELSE 0 END) AS SPL_ISSUE_QTY_IN,
				sum(CASE WHEN production_type=2 and embel_name=4 and production_source=3 THEN production_quantity ELSE 0 END) AS SPL_ISSUE_QTY_OUT,
				sum(CASE WHEN production_type=3 and embel_name=4 and production_source=1 THEN production_quantity ELSE 0 END) AS SPL_RECV_QTY_IN,
				sum(CASE WHEN production_type=3 and embel_name=4 and production_source=3 THEN production_quantity ELSE 0 END) AS SPL_RECV_QTY_OUT,
				sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS SEW_INPUT_QTY_IN,
				sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS SEW_INPUT_QTY_OUT,
				sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS SEW_RECV_QNTY_IN,
				sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS SEW_RECV_QNTY_OUT,
				sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS FINISH_QTY_IN,
				sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS FINISH_QTY_OUT,
							
				sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS PRINT_REJECT_QNTY,
				sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS EMB_REJECT_QNTY,
				sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS SEW_REJECT_QNTY,
				sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS FINISH_REJECT_QNTY,
				sum(CASE WHEN production_type=11 THEN production_quantity ELSE 0 END) AS CARTON_QTY
				from pro_garments_production_mst where  is_deleted=0 and status_active=1 ".where_con_using_array($allPoArr,0,'po_break_down_id')." group by PO_BREAK_DOWN_ID";
			
	//echo $gmtsProSql;die;
	//and production_source=3
	
	$gmtsProSqlRes=sql_select($gmtsProSql);			
	foreach($gmtsProSqlRes as $row)
	{
 		$gmtProDataArr[CUTTING_QTY][$row[PO_ID]]=$row[CUTTING_QTY];
 		$gmtProDataArr[PRINT_ISSUE_QTY_IN][$row[PO_ID]]=$row[PRINT_ISSUE_QTY_IN];
 		$gmtProDataArr[PRINT_ISSUE_QTY_OUT][$row[PO_ID]]=$row[PRINT_ISSUE_QTY_OUT];
 		$gmtProDataArr[PRINT_RECV_QTY_IN][$row[PO_ID]]=$row[PRINT_RECV_QTY_IN];
 		$gmtProDataArr[PRINT_RECV_QTY_OUT][$row[PO_ID]]=$row[PRINT_RECV_QTY_OUT];

		$gmtProDataArr[EMB_ISSUE_QTY_IN][$row[PO_ID]]=$row[EMB_ISSUE_QTY_IN];
		$gmtProDataArr[EMB_ISSUE_QTY_OUT][$row[PO_ID]]=$row[EMB_ISSUE_QTY_OUT];
		$gmtProDataArr[EMB_RECV_QTY_IN][$row[PO_ID]]=$row[EMB_RECV_QTY_IN];
		$gmtProDataArr[EMB_RECV_QTY_OUT][$row[PO_ID]]=$row[EMB_RECV_QTY_OUT];
 		
		$gmtProDataArr[WASH_ISSUE_QTY_IN][$row[PO_ID]]=$row[WASH_ISSUE_QTY_IN];
		$gmtProDataArr[WASH_ISSUE_QTY_OUT][$row[PO_ID]]=$row[WASH_ISSUE_QTY_OUT];
		$gmtProDataArr[WASH_RECV_QTY_IN][$row[PO_ID]]=$row[WASH_RECV_QTY_IN];
		$gmtProDataArr[WASH_RECV_QTY_OUT][$row[PO_ID]]=$row[WASH_RECV_QTY_OUT];
 		
		$gmtProDataArr[SPL_ISSUE_QTY_IN][$row[PO_ID]]=$row[SPL_ISSUE_QTY_IN];
		$gmtProDataArr[SPL_ISSUE_QTY_OUT][$row[PO_ID]]=$row[SPL_ISSUE_QTY_OUT];
		$gmtProDataArr[SPL_RECV_QTY_IN][$row[PO_ID]]=$row[SPL_RECV_QTY_IN];
		$gmtProDataArr[SPL_RECV_QTY_OUT][$row[PO_ID]]=$row[SPL_RECV_QTY_OUT];

		$gmtProDataArr[SEW_INPUT_QTY_IN][$row[PO_ID]]=$row[SEW_INPUT_QTY_IN];
		$gmtProDataArr[SEW_INPUT_QTY_OUT][$row[PO_ID]]=$row[SEW_INPUT_QTY_OUT];		

		$gmtProDataArr[SEW_RECV_QNTY_IN][$row[PO_ID]]=$row[SEW_RECV_QNTY_IN];		
		$gmtProDataArr[SEW_RECV_QNTY_OUT][$row[PO_ID]]=$row[SEW_RECV_QNTY_OUT];		
		
		$gmtProDataArr[FINISH_QTY_OUT][$row[PO_ID]]=$row[FINISH_QTY_OUT];
		$gmtProDataArr[FINISH_QTY_IN][$row[PO_ID]]=$row[FINISH_QTY_IN];
		$gmtProDataArr[CARTON_QTY][$row[PO_ID]]=$row[CARTON_QTY];
 
 	}
	unset($gmtsProSqlRes);		
			
 	
	$insStatusArr=return_library_array("SELECT inspection_status,PO_BREAK_DOWN_ID from PRO_BUYER_INSPECTION where status_active=1 and is_deleted=0 ".where_con_using_array($allPoArr,0,'PO_BREAK_DOWN_ID')." group by INSPECTION_STATUS,PO_BREAK_DOWN_ID","PO_BREAK_DOWN_ID","inspection_status");			
			
	
	$exFactortySql="SELECT PO_BREAK_DOWN_ID,sum ((case when a.entry_form<>85 then a.EX_FACTORY_QNTY else 0 end)-(case when a.entry_form=85 then a.EX_FACTORY_QNTY else 0 end) ) as SHIP_QTY,MAX ( CASE  WHEN a.entry_form <> 85  THEN  TO_CHAR (a.EX_FACTORY_DATE, 'yyyy-mm-dd')   END) AS EX_FACTORY_DATE from pro_ex_factory_mst a where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($allPoArr,0,'PO_BREAK_DOWN_ID')." group by PO_BREAK_DOWN_ID";
	$exFactortySqlRes=sql_select($exFactortySql);			
	foreach($exFactortySqlRes as $row)
	{
		$exFactoryDataArr[SHIP_QTY][$row[PO_BREAK_DOWN_ID]]=$row[SHIP_QTY];
		$exFactoryDataArr[EX_FACTORY_DATE][$row[PO_BREAK_DOWN_ID]]=$row[EX_FACTORY_DATE];
	}
	unset($exFactortySqlRes);
	
	
	$exportInvoiceSql="SELECT a.INVOICE_VALUE,a.NET_INVO_VALUE,b.PO_BREAKDOWN_ID,sum(b.CURRENT_INVOICE_QNTY) as CURRENT_INVOICE_QNTY,sum(b.CURRENT_INVOICE_VALUE) as CURRENT_INVOICE_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a,COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=b.mst_id ".where_con_using_array($allPoArr,0,'B.PO_BREAKDOWN_ID')." group by  a.INVOICE_VALUE,a.NET_INVO_VALUE,b.PO_BREAKDOWN_ID";
	
	//echo $exportInvoiceSql;die;
	$exportInvoiceSqlRes=sql_select($exportInvoiceSql);			
	foreach($exportInvoiceSqlRes as $row)
	{
		
		$orderNetInvoiceVal=($row[INVOICE_VALUE]/$row[NET_INVO_VALUE])*$row[CURRENT_INVOICE_VALUE];
		$netinvoiceRate=$orderNetInvoiceVal/$row[CURRENT_INVOICE_QNTY];
		$exportInvoiceDataArr[INVOICE_RATE][$row[PO_BREAKDOWN_ID]]=$netinvoiceRate;
	}
	unset($exportInvoiceSqlRes);
	
	

	$finishSql="SELECT b.po_breakdown_id as PO_ID,
    sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 and b.is_sales=0 then b.quantity end) as FINISH_FABRIC_RECV,
    sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2  then b.quantity end) as FINISH_FABRIC_ISSUE,
    sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3  THEN b.quantity ELSE 0 END) AS RECV_RTN_QNTY,
    sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4  THEN b.quantity ELSE 0 END) AS ISS_RETN_QNTY,
    sum(case when b.entry_form in(14,15,306) and b.trans_type=5 and a.transaction_type=5  then b.quantity end) as FINISH_FABRIC_TRANS_RECV,
    sum(case when b.entry_form in(14,15,306) and b.trans_type=6 and a.transaction_type=6  then b.quantity end) as FINISH_FABRIC_TRANS_ISSUED
    from inv_transaction a, order_wise_pro_details b
    where a.id=b.trans_id 
    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($allPoArr,0,'b.po_breakdown_id')."
    group by b.po_breakdown_id ";
	$finishSqlRes=sql_select($finishSql);	
	foreach($finishSqlRes as $row)
	{
		$finishDataArr[FINISH_FAB_ISSUE][$row[PO_ID]]=($row[FINISH_FABRIC_ISSUE]+$row[FINISH_FABRIC_TRANS_ISSUED])-($row[ISS_RETN_QNTY]+$row[FINISH_FABRIC_TRANS_RECV]);
	}
	unset($finishSqlRes);
	
	//select  FABRIC_COST,TRIMS_COST,EMBEL_COST,WASH_COST from WO_PRE_COST_DTLS where job_no='FAL-21-00550' and is_deleted=0 and status_active=1
	
 	
	
	
	
	$derictCostSql="SELECT b.PO_BREAK_DOWN_ID,sum(b.AMOUNT) as AMOUNT from WO_BOOKING_MST a,WO_BOOKING_DTLS b where a.booking_no=b.booking_no and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 ".where_con_using_array($allPoArr,0,' b.PO_BREAK_DOWN_ID')." and a.entry_form in(87,118,201) group by b.PO_BREAK_DOWN_ID";			
	$derictCostSqlRes=sql_select($derictCostSql);	
	foreach($derictCostSqlRes as $row)
	{
		$derictCostDataArr[ACTUAL_DIRECT_COST_VALUE][$row[PO_BREAK_DOWN_ID]]=$row[AMOUNT];
	}
	unset($derictCostSqlRes);	
	
	$with=4200;
	
	ob_start();	
	?>
    
    <fieldset style="width:<?=$with+20;?>px; margin-left:5px">	
        <div style="width:100%"> 
        <table width="<?=$with;?>">
            <tr>
                <td align="center" width="<?=$with;?>" colspan="43" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
            </tr>
        </table>
        
            <table width="<?=$with;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="35">SL</th>
                    <th width="70">ORDER ENTRY DATE</th>
                    <th width="100">CUSTOMER</th>
                    <th width="80">JOB NO</th>
					<th width="200">Style Ref.</th>
                    <th width="100">Internal Ref.</th>
                    <th width="100">ORDER/PO NO</th>
                    <th width="100">QTY</th>
                    <th width="60">UNIT PRICE</th>
                    <th width="80">TOTAL VALUE</th>
                    <th width="70">DELIVERY</th>
                    <th width="70">REVISED DELIVERY</th>
                    <th width="100">ORDER STATUS</th>
                    <th width="100">ORDER SHIPMENT STATUS</th>
                    <th width="80">CUT QTY.</th>
                    <th width="80">PRINT SEND QTY.</th>
                    <th width="80">PRINT RECEIVE QTY.</th>
                    <th width="80">EMBROIDERY SEND QTY.</th>
                    <th width="80">EMBROIDERY RECEIVE QTY.</th>
                    <th width="80">SPECIAL WORK SEND</th>
                    <th width="80">SPECIAL WORK RECEIVE</th>
                    <th width="80">SEWING OUTPUT QTY.</th>
                    <th width="80">WASH SENT QTY.</th>
                    <th width="80">WASH RECEIVE QTY.</th>
                    <th width="80">FINISHING QTY.</th>
                    <th width="80">CARTON QTY.</th>
                    <th width="80">INSPECTION STATUS</th>
                    <th width="80">SHIPMENT UNIT PRICE</th>
                    <th width="80">SHIPPED QTY.</th>
                    <th width="80">SHIPMENT DATE</th>
                    <th width="80">EXCESS / SHORT SHIPMENT QTY.</th>
                    <th width="80">SHIPMENT VALUE</th>
                    <th width="80">EXCESS / SHORT SHIPMENT VALUE</th>
                    <th width="80">SHIPMENT MODE</th>
                    <th width="80">EXTRA SHIPMENT COST</th>
                    <th width="80">COSTING CONSUMP TION</th>
                    <th width="80" title="finishFabricIssue/exFactoryQuantity (PO Wise)">ACTUAL CONSUMP TION</th>
                    <th width="80">COSTING YARN PRICE</th>
                    <th width="80">ACTUAL YARN PRICE</th>
                    <th width="100">COSTING DIRECT COST VALUE</th>
                    <th width="100">ACTUAL DIRECT COST VALUE</th>
                    <th width="80">COSTING DIRECT COST VALUE %</th>
                    <th width="80">ACTUAL DIRECT COST VALUE %</th>
                    <th width="120">COSTING CM</th>
                    <th width="120">ACTUAL CM</th>
                    <th width="80">COSTING CM %</th>
                    <th title="(actualCM*shipmentValue)/100">ACTUAL CM %</th>
                </thead>
                </table>
                <div id="scroll_body" style="width:<?=$with+20;?>px; max-height:400px; overflow-y:auto;">
                <table id="table_body" width="<?=$with;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">

                <?	
				
				 
					$i=1;
                    foreach($poDataArr as $row)
                    {
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$costing_direct_cost_val=$fabricCostByPoArr[$row[PO_ID]]+$trimsCostByPoArr[$row[PO_ID]]+$emblishCostByPoArr[$row[PO_ID]]+$washCostByPoArr[$row[PO_ID]];
                        
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="70" align="center"><?= change_date_format($row[INSERT_DATE]); ?></td>
                            <td width="100"><p><?= $buyer_arr[$row[BUYER_NAME]]; ?></p></td>
                            <td width="80"><?= $row[JOB_NO]; ?></td>
							<td width="200"><?= $row [STYLE_REF_NO]?></td>
                            <td width="100"><p><?= $row[GROUPING]; ?></p></td>
                            <td width="100"><p><?= $row[PO_NUMBER]; ?></p></td>
                            <td width="100" align="right"><?= $row[PO_QNTY]; ?></td>
                            <td width="60" align="center"><?= number_format($row[UNIT_PRICE],2); ?></td>
                            <td width="80" align="right"><?= number_format($row[PO_TOTAL_PRICE],2); ?></td>
                            <td width="70" align="center"><?= change_date_format($row[SHIPMENT_DATE]); ?></td>
                            <td width="70" align="center"><?= change_date_format(($row[UPDATE_DATE])?$row[UPDATE_DATE]:$row[INSERT_DATE]); ?></td>
                            <td width="100" align="center"><?= $row_status[$row[STATUS_ACTIVE]]; ?></td>
                            <td width="100" align="center"><?= $shipment_status[$row[SHIPING_STATUS]]; ?></td>
                            
                            <td width="80" align="right"><?= $gmtProDataArr[CUTTING_QTY][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[PRINT_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[PRINT_ISSUE_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[PRINT_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[PRINT_RECV_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[EMB_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[EMB_ISSUE_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[EMB_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[EMB_RECV_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[SPL_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[SPL_ISSUE_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[SPL_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[SPL_RECV_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[SEW_RECV_QNTY_IN][$row[PO_ID]]+$gmtProDataArr[SEW_RECV_QNTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[WASH_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[WASH_ISSUE_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[WASH_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[WASH_RECV_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[FINISH_QTY_IN][$row[PO_ID]]+$gmtProDataArr[FINISH_QTY_OUT][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= $gmtProDataArr[CARTON_QTY][$row[PO_ID]]; ?></td>
                            <td width="80" align="center"><?= $inspection_status[$insStatusArr[$row[PO_ID]]]; ?></td>
                            <td width="80" align="center"><?= $invoiceRate=$row[UNIT_PRICE];//$exportInvoiceDataArr[INVOICE_RATE][$row[PO_ID]];?></td>
                            <td width="80" align="right"><?= $exFactoryDataArr[SHIP_QTY][$row[PO_ID]]; ?></td>
                            <td width="80" align="center"><?= change_date_format($exFactoryDataArr[EX_FACTORY_DATE][$row[PO_ID]]); ?></td>
                            <td width="80" align="right"><?= $shortExcess=$row[PO_QNTY]-$exFactoryDataArr[SHIP_QTY][$row[PO_ID]]; ?></td>
                            <td width="80" align="right"><?= number_format($SHIPMENT_VALUE=$exFactoryDataArr[SHIP_QTY][$row[PO_ID]]*$invoiceRate,2); ?></td>
                            <td width="80" align="right"><?= number_format($invoiceRate*$shortExcess,2);?></td>
                            <td width="80" align="center"><?= $shipment_mode[$row[SHIP_MODE]]; ?></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80" align="right" title="<?=number_format($finishDataArr[FINISH_FAB_ISSUE][$row[PO_ID]], 2) . "/". number_format($exFactoryDataArr[SHIP_QTY][$row[PO_ID]],2);?>"><?=number_format($actual_con=$finishDataArr[FINISH_FAB_ISSUE][$row[PO_ID]]/$exFactoryDataArr[SHIP_QTY][$row[PO_ID]],2);?></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="100" align="right"><?=number_format($costing_direct_cost_val,2);?></td>
                            <td width="100" align="right"><?=number_format($derictCostDataArr[ACTUAL_DIRECT_COST_VALUE][$row[PO_ID]],2);?></td>
                            <td width="80" align="center"><?=number_format(($costing_direct_cost_val/$row[PO_TOTAL_PRICE])*100,2);?></td>
                            <td width="80" align="right"><?=number_format(($derictCostDataArr[ACTUAL_DIRECT_COST_VALUE][$row[PO_ID]]/$row[PO_TOTAL_PRICE])*100,2);?></td>
                            <td width="120" align="right"><?= number_format($COSTING_CM=$row[PO_TOTAL_PRICE]-$costing_direct_cost_val,2);;//$derictCostDataArr[ACTUAL_DIRECT_COST_VALUE][$row[PO_ID]] ?></td>
                            <td width="120" align="right"><?= number_format($ACTUAL_CM=$SHIPMENT_VALUE-$derictCostDataArr[ACTUAL_DIRECT_COST_VALUE][$row[PO_ID]],2); ?></td>
                            <td align="right" width="80"><?=number_format(($COSTING_CM/$row[PO_TOTAL_PRICE])*100,2);?></td>
                            <td align="right" title="<?= number_format($ACTUAL_CM, 2)."/".number_format($SHIPMENT_VALUE,2)."*100";?>"><?=number_format(($ACTUAL_CM/$SHIPMENT_VALUE)*100,2);?></td>
                        </tr>
                    	<?
                        $i++;
						
						$total_po_qty_pcs+=$row[PO_QNTY];
						$total_cutting_qty_pcs+=$gmtProDataArr[CUTTING_QTY][$row[PO_ID]];
						$total_costing_direct_cost_val+=$costing_direct_cost_val;
						$TOTAL_ACTUAL_DIRECT_COST_VALUE+=$derictCostDataArr[ACTUAL_DIRECT_COST_VALUE][$row[PO_ID]];
						
						$TOTAL_PRINT_SEND_QTY+= $gmtProDataArr[PRINT_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[PRINT_ISSUE_QTY_OUT][$row[PO_ID]];
						$TOTAL_PRINT_RECEIVE_QTY+= $gmtProDataArr[PRINT_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[PRINT_RECV_QTY_OUT][$row[PO_ID]];
						
						
						$TOTAL_EMBROIDERY_SEND_QTY+= $gmtProDataArr[EMB_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[EMB_ISSUE_QTY_OUT][$row[PO_ID]];
						$TOTAL_EMBROIDERY_RECEIVE_QTY+= $gmtProDataArr[EMB_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[EMB_RECV_QTY_OUT][$row[PO_ID]];
						$TOTAL_SPECIAL_WORK_SEND+= $gmtProDataArr[SPL_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[SPL_ISSUE_QTY_OUT][$row[PO_ID]];
						$TOTAL_SPECIAL_WORK_RECEIVE+= $gmtProDataArr[SPL_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[SPL_RECV_QTY_OUT][$row[PO_ID]];
						
						$TOTAL_SEWING_OUTPUT_QTY+= $gmtProDataArr[SEW_RECV_QNTY_IN][$row[PO_ID]]+$gmtProDataArr[SEW_RECV_QNTY_OUT][$row[PO_ID]];
						$TOTAL_WASH_SENT_QTY+= $gmtProDataArr[WASH_ISSUE_QTY_IN][$row[PO_ID]]+$gmtProDataArr[WASH_ISSUE_QTY_OUT][$row[PO_ID]];
						$TOTAL_WASH_RECEIVE_QTY+= $gmtProDataArr[WASH_RECV_QTY_IN][$row[PO_ID]]+$gmtProDataArr[WASH_RECV_QTY_OUT][$row[PO_ID]];
						$TOTAL_FINISHING_QTY+= $gmtProDataArr[FINISH_QTY_IN][$row[PO_ID]]+$gmtProDataArr[FINISH_QTY_OUT][$row[PO_ID]];
						$TOTAL_CARTON_QTY+= $gmtProDataArr[CARTON_QTY][$row[PO_ID]];
                   
				   		$TOTAL_SHIPPED_QTY+=$exFactoryDataArr[SHIP_QTY][$row[PO_ID]];
				   		$TOTAL_SHIPMENT_VALUE+=$SHIPMENT_VALUE;
						
						$TOTAL_ACTUAL_CM+=$ACTUAL_CM;
						// $TOTAL_ACTUAL_CM+=$ACTUAL_CM;
						
						$TOTAL_COSTING_CM+=$COSTING_CM;
						// $TOTAL_ACTUAL_CM+=$ACTUAL_CM;
						
						
				   
				    }
					//unset($poDataArr);
					
                ?>
                
                </table>
                </div>
                <table width="<?=$with;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th width="35"><!--SL--></th>
                    <th width="70"><!--ORDER ENTRY DATE--></th>
                    <th width="100"><!--CUSTOMER--></th>
                    <th width="80"><!--Job No.--></th>
					<th width="200"><!--Style Ref.--></th>
                    <th width="100"><!--ORDER/PO NO--></th>
                    <th width="100"><!--ORDER/PO NO--></th>
                    <th width="100" id="total_po_qty_pcs_td"><?=$total_po_qty_pcs;?></th>
                    <th width="60"><!--UNIT PRICE--></th>
                    <th width="80"><!--TOTAL VALUE--></th>
                    <th width="70"><!--DELIVERY--></th>
                    <th width="70"><!--REVISED DELIVERY--></th>
                    <th width="100"><!--ORDER STATUS--></th>
                    <th width="100"><!--ORDER Shipment STATUS--></th>
                    <th width="80" id="total_cutting_qty_pcs_td"><?=$total_cutting_qty_pcs;?></th>
                    <th width="80" id="TOTAL_PRINT_SEND_QTY"><?=$TOTAL_PRINT_SEND_QTY;?></th>
                    <th width="80" id="TOTAL_PRINT_RECEIVE_QTY"><?=$TOTAL_PRINT_RECEIVE_QTY;?></th>
                    <th width="80" id="TOTAL_EMBROIDERY_SEND_QTY"><?=$TOTAL_EMBROIDERY_SEND_QTY;?></th>
                    <th width="80" id="TOTAL_EMBROIDERY_RECEIVE_QTY"><?=$TOTAL_EMBROIDERY_RECEIVE_QTY;?></th>
                    <th width="80" id="TOTAL_SPECIAL_WORK_SEND"><?=$TOTAL_SPECIAL_WORK_SEND?></th>
                    <th width="80" id="TOTAL_SPECIAL_WORK_RECEIVE"><?=$TOTAL_SPECIAL_WORK_RECEIVE;?></th>
                    <th width="80" id="TOTAL_SEWING_OUTPUT_QTY"><?=$TOTAL_SEWING_OUTPUT_QTY;?></th>
                    <th width="80" id="TOTAL_WASH_SENT_QTY"><?=$TOTAL_WASH_SENT_QTY;?></th>
                    <th width="80" id="TOTAL_WASH_RECEIVE_QTY"><?=$TOTAL_WASH_RECEIVE_QTY;?></th>
                    <th width="80" id="TOTAL_FINISHING_QTY"><?=$TOTAL_FINISHING_QTY;?></th>
                    <th width="80" id="TOTAL_CARTON_QTY"><?=$TOTAL_CARTON_QTY;?></th>
                    <th width="80"><!--INSPECTION STATUS--></th>
                    <th width="80"><!--SHIPMENT UNIT PRICE--></th>
                    <th width="80" id="TOTAL_SHIPPED_QTY"><?=$TOTAL_SHIPPED_QTY;?></th>
                    <th width="80"><!--SHIPMENT DATE--></th>
                    <th width="80"><!--EXCESS / SHORT SHIPMENT QTY.--></th>
                    <th width="80" id="TOTAL_SHIPMENT_VALUE"><?=$TOTAL_SHIPMENT_VALUE;?></th>
                    <th width="80"><!--EXCESS / SHORT SHIPMENT VALUE--></th>
                    <th width="80"><!--SHIPMENT MODE--></th>
                    <th width="80"><!--EXTRA SHIPMENT COST--></th>
                    <th width="80"><!--COSTING CONSUMPTION--></th>
                    <th width="80"><!--ACTUAL CONSUMPTION--></th>
                    <th width="80"><!--COSTING YARN PRICE--></th>
                    <th width="80"><!--ACTUAL YARN PRICE--></th>
                    <th width="100" id="total_costing_direct_cost_val"><?=number_format($total_costing_direct_cost_val,2);?></th>
                    <th width="100" id="TOTAL_ACTUAL_DIRECT_COST_VALUE"><?=number_format($TOTAL_ACTUAL_DIRECT_COST_VALUE,2);?></th>
                    <th width="80"><!--COSTING DIRECT COST VALUE %--></th>
                    <th width="80"><!--ACTUAL DIRECT COST VALUE %--></th>
					<th width="120" id="TOTAL_COSTING_CM" ><p><?=number_format($TOTAL_COSTING_CM,2);?></p></th>
                    <th width="120" id="TOTAL_ACTUAL_CM"><?=$TOTAL_ACTUAL_CM;?></th>
                    <th width="80"><!--COSTING CM %--></th>
                    <th><!--ACTUAL CM %--></th>
                </tfoot>
            </table>
        </div>
        
  <?      
 	$user_id=$_SESSION['logic_erp']['user_id'];
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	ob_clean();
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	
	echo "$html####$filename";
	exit();
        
			
			
}


?>
