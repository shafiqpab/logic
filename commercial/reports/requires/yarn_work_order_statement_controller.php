<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($db_type==0) $select_concat="group";
if($db_type==2) $select_concat="wm";


if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'yarn_work_order_statement_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($db_type==0) $select_year="year(insert_date) as year";
	else if($db_type==2) $select_year="to_char(insert_date,'YYYY') as year";
	else  $select_year="";

	if($db_type==0) $select_year_search="and year(insert_date)";
	else if($db_type==2) $select_year_search="and to_char(insert_date,'YYYY')";
	else  $select_year_search="";

	if($db_type==0) $select_month_search="and month(insert_date)";
	else if($db_type==2) $select_month_search=" and to_char(insert_date,'MM')";
	else  $select_month_search="";


	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($year_id!=0) $year_cond=" $select_year_search=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $select_month_search=$month_id"; else $month_cond="";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $select_year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
   exit(); 
} 

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
?>	
    <script>
	 
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert (splitData[1]);
		$("#order_no_id").val(splitData[0]); 
		$("#order_no_val").val(splitData[1]); 
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="order_no_id" />
     <input type="hidden" id="order_no_val" />
 <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num in('$data[2]')";
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "yarn_work_order_statement_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if ($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>	
    <script>
	 
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert (splitData[1]);
		$("#work_order_no_id").val(splitData[0]); 
		$("#work_order_no_val").val(splitData[1]); 
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="work_order_no_id" />
     <input type="hidden" id="work_order_no_val" />
 <?
	if($db_type==0) $select_year="year(insert_date) as year";
	else if($db_type==2) $select_year="to_char(insert_date,'YYYY') as year";
	else  $select_year="";

	if($db_type==0) $select_year_search="and year(insert_date)";
	else if($db_type==2) $select_year_search="and to_char(insert_date,'YYYY')";
	else  $select_year_search="";
	
	if($data[1]!=0) $year_cond="$select_year_search=$data[1]"; else $year_cond="";
	//if ($data[2]!='') $order_cond=" and buyer_po in($data[2])"; else  $order_cond="";
	if($db_type==0) $find_inset=" and buyer_po in($data[2])";
	else if($db_type==2) $find_inset=" and ',' ||buyer_po|| ',' LIKE '%,$data[2],%' ";
	else  $find_inset="";

	if ($data[2]!='') $order_cond=$find_inset; else  $order_cond="";
	if ($data[3]!=0) $wo_cond=" and wo_basis_id in($data[3])"; else  $wo_cond="";
	
	
	$sql="select id, wo_number_prefix_num, wo_number, wo_date, supplier_id, delivery_date, $select_year from wo_non_order_info_mst where company_name=$data[0] and is_deleted=0 and status_active=1 $order_cond $year_cond $wo_cond ORDER BY wo_number_prefix_num";
	//echo $sql;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier","id","supplier_name");
	$arr=array(3=>$supplier_arr);
	
	echo  create_list_view("list_view", "WO No,Year,WO Date,Supplier,Delivery Date", "60,80,75,120,75","470","350",0, $sql, "js_set_value", "id,wo_number_prefix_num", "", 1, "0,0,0,supplier_id,0,0", $arr , "wo_number_prefix_num,year,wo_date,supplier_id,delivery_date", "yarn_work_order_statement_controller",'setFilterGrid("list_view",-1);','0,0,3,0,0,3','') ;
	disconnect($con);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$item_category= 1;//str_replace("'","",$cbo_category_id);
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$year_cond = ""; $month_cond = "";
	if(str_replace("'","",$cbo_year) != 0)
	{
		if($db_type==0) $year_cond=" and year(c.wo_date) = $cbo_year";
		if($db_type==2) $year_cond=" and to_char(c.wo_date,'YYYY') = $cbo_year";
	}
	if(str_replace("'","",$cbo_month) != 0)
	{
		if($db_type==0) $month_cond=" and month(c.wo_date) = $cbo_month";
		if($db_type==2) $month_cond=" and to_char(c.wo_date,'MM') = $cbo_month";
	}
	

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	
	$wo_basis=str_replace("'","",$cbo_wo_basis);
	if($wo_basis!=0) $wo_basis_cond=" and c.wo_basis_id='$wo_basis'"; else $wo_basis_cond="";
	
	
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond=" and b.id in ($order_no)";
	
	$wo_no=str_replace("'","",$txt_wo_no);
	if(str_replace("'","",$txt_wo_no)!="" && str_replace("'","",$txt_wo_no)!=0) $wo_cond=" and c.wo_number_prefix_num in ($wo_no)";

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.wo_date between ".$txt_date_from." and ".$txt_date_to."";
	
	//if( $date_from=="") $wo_date=""; else $wo_date= " and c.wo_date <=".$txt_date_from."";
	$company_arr=return_library_array( "select id, company_name from lib_company","id","company_name");
	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr=return_library_array( "select id, color_name from lib_color","id","color_name");
	//$yarn_count_count=count($yarn_count_arr);
	//$tbl_width=1300+($yarn_count_count*60);
	if ($wo_basis==3 || $wo_basis==1)
	{
		$tbl_width=1500;
		$colspan=18;
	}
	else
	{
		$tbl_width=1140;
		$colspan=14;
	}
	
	ob_start();
	?>
    <fieldset style="width:<? echo $tbl_width+20; ?>px;">
        <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="<? echo $colspan; ?>" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="<? echo $colspan; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="<? echo $colspan; ?>" style="font-size:14px"><strong> <? if($date_from!="") echo "Date From : ".change_date_format(str_replace("'","",$txt_date_from)).' To '.change_date_format(str_replace("'","",$txt_date_to)) ;?></strong></td>
            </tr>
        </table>
        <table width="<? echo $tbl_width+16; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <?
						if ($wo_basis==3)
						{
					?>
                    <th width="70">Job</th>
                    <th width="90">Buyer</th>
                    <th width="100">Style</th>
                    <th width="100">Order</th> 
					<?
						}
						else if ($wo_basis==1)
						{
					?>
					<th width="70">Job</th>
                    <th width="90">Buyer</th>
                    <th width="100">Style</th>
					<?
						}
					?>
                    <th width="70">WO No</th>
                    <th width="75">WO Date</th>
                    <th width="75">Delivery Date</th>
                    
                    <th width="100">Supplier</th>
                    <th width="80">Challan/ D/O No</th>
                    <th width="75">Yarn Rcvd Date</th>
                    <th width="130">Yarn Details</th>
                    
                    <th width="70">Color</th>
                    <th width="60">Count</th>
                    <th width="80">Qty</th>
                    <th width="70">Rate</th>
                    <th width="90">Amount </th>
                    <th width="">Remarks </th>
                </tr>
            </thead>
        </table> 
        <div style="width:<? echo $tbl_width+19; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
		<?	
			if ($wo_basis==3)
			{
				if($db_type==0) $select_groby="group by a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num, d.id order by a.buyer_name, a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num";
				else $select_groby=" group by a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num, d.id,a.id, a.company_name,a.buyer_name, a.style_ref_no, b.id,a.job_no,c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks order by a.buyer_name, a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num";
				
				// $sql_query="Select a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, $select_concat"."_concat(distinct(b.po_number)) as po_number,
				// c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks
				// from  wo_po_details_master a, wo_po_break_down b, wo_non_order_info_mst c, wo_non_order_info_dtls d 
				// where a.job_no=b.job_no_mst and b.job_no_mst=c.wo_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				// and c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
				// c.company_name=$cbo_company_id $year_cond $month_cond $wo_basis_cond $wo_date_cond $buyer_id_cond $job_no_cond $order_id_cond $wo_cond $select_groby";
				
				// $sql_query="Select a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id,listagg(cast(b.po_number as varchar(4000)),',') within group (order by b.po_number) as po_number,
				// c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks
				// from  wo_po_details_master a, wo_po_break_down b, wo_non_order_info_mst c, wo_non_order_info_dtls d 
				// where a.job_no=b.job_no_mst and b.job_no_mst=c.wo_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				// and c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
				// c.company_name=$cbo_company_id $year_cond $month_cond $wo_basis_cond $wo_date_cond $buyer_id_cond $job_no_cond $order_id_cond $wo_cond $select_groby";

				$sql_query="SELECT A.ID, A.COMPANY_NAME, A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.BUYER_NAME, A.STYLE_REF_NO, B.ID AS PO_ID,B.PO_NUMBER, A.STYLE_REF_NO, C.WO_NUMBER_PREFIX_NUM, C.WO_NUMBER, C.WO_DATE, C.SUPPLIER_ID, C.DELIVERY_DATE, D.PO_BREAKDOWN_ID, D.ITEM_ID, D.YARN_COUNT, D.YARN_COMP_TYPE1ST, D.YARN_COMP_PERCENT1ST, D.YARN_COMP_TYPE2ND, D.YARN_COMP_PERCENT2ND, D.COLOR_NAME, D.SUPPLIER_ORDER_QUANTITY, D.RATE, D.AMOUNT, D.REMARKS,D.PO_BREAKDOWN_ID FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B, WO_NON_ORDER_INFO_MST C, WO_NON_ORDER_INFO_DTLS D 
				WHERE A.JOB_NO=B.JOB_NO_MST AND  D.PO_BREAKDOWN_ID=B.ID AND C.ID=D.MST_ID  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND D.ITEM_CATEGORY_ID='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
				c.company_name=$cbo_company_id $year_cond $month_cond $wo_basis_cond $wo_date_cond $buyer_id_cond $job_no_cond $order_id_cond $wo_cond $select_groby";

				
				//echo $sql_query;//die;
			}
			elseif($wo_basis==1)
			{
				$new_sql_cond="";
				if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0)
				{
					$new_sql_cond.=" and e.buyer_id=$cbo_buyer_id";
				}
				if($job_no !="")
				{
					$new_sql_cond.=" and e.job_no like '%$job_no%'";
				}
						
				if($db_type==0) $select_grpby="group by c.wo_number_prefix_num, d.id order by c.wo_number_prefix_num";
				if($db_type==2) $select_grpby="group by c.wo_number_prefix_num, d.id,c.id, c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks, d.requisition_dtls_id order by c.wo_number_prefix_num";
				else $select_grpby="";
				
				$sql_query="SELECT c.id, c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.requisition_dtls_id, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks 
					from wo_non_order_info_mst c, wo_non_order_info_dtls d, inv_purchase_requisition_dtls e
					where c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
					and e.id=d.requisition_dtls_id  and e.status_active=1 and e.is_deleted=0
					and c.company_name=$cbo_company_id $new_sql_cond $wo_basis_cond $year_cond $month_cond $wo_date_cond $wo_cond $select_grpby";

				//-------------------  Dtls for job data start-----------------------------
				$requisition_dtls_id_arr=array();
				$result=sql_select($sql_query);
				foreach ($result as $value) 
				{
					$requisition_dtls_id_arr[]=$value[csf('requisition_dtls_id')];
				}
				$req_dtls_ids=implode(",", array_unique($requisition_dtls_id_arr));
				$job_dtls_data_arr=sql_select("select c.id as requisition_dtls_id, a.job_no_prefix_num, b.po_number, a.buyer_name, a.style_ref_no  from wo_po_details_master a, wo_po_break_down b, inv_purchase_requisition_dtls c where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (c.job_id=a.id or a.job_no=c.job_no) and c.id in ($req_dtls_ids) and c.status_active=1 and c.is_deleted=0");

				$job_data_info_arr=array();
				foreach ($job_dtls_data_arr as $rows) 
				{
					$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['job_no_prefix_num']=$rows[csf('job_no_prefix_num')];
					//$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['po_number']=$rows[csf('po_number')];
					$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['buyer_name']=$rows[csf('buyer_name')];
					$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['style_ref_no']=$rows[csf('style_ref_no')];
				}
				//----------------------end---------------------------------------------------------------------
			
			}
			else
			{
				if($db_type==0) $select_grpby="group by c.wo_number_prefix_num, d.id order by c.wo_number_prefix_num";
				if($db_type==2) $select_grpby="group by c.wo_number_prefix_num, d.id,c.id, c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks order by c.wo_number_prefix_num";
				else $select_grpby="";
				
				$sql_query="Select c.id, c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks from wo_non_order_info_mst c, wo_non_order_info_dtls d where c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.company_name=$cbo_company_id $wo_basis_cond $year_cond $month_cond $wo_date_cond $wo_cond $select_grpby";
			}
			//echo $sql_query;
			$i=1; 
			$nameArray=sql_select( $sql_query );  
			foreach ($nameArray as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$yarn_comp_type1st=$row[csf("yarn_comp_type1st")];
				$yarn_comp_percent1st=$row[csf("yarn_comp_percent1st")];
				$yarn_comp_type2nd=$row[csf("yarn_comp_type2nd")];
				$yarn_comp_percent2nd=$row[csf("yarn_comp_percent2nd")];
				
				$yarndtls='';
				if ($yarn_comp_type1st!=0 && $yarn_comp_percent1st!='' && $yarn_comp_type2nd!=0 && $yarn_comp_percent2nd!='')
				{
					$yarndtls=$composition[$yarn_comp_type1st].'  '.$yarn_comp_percent1st.' %, '.$composition[$yarn_comp_type2nd].' '.$yarn_comp_percent2nd.' %'; 
				}
				else if($yarn_comp_type1st!=0 && $yarn_comp_percent1st!='' && $yarn_comp_type2nd!=0)
				{
					$yarndtls=$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st.' %, '.$composition[$yarn_comp_type2nd]; 
				}
				else if($yarn_comp_type1st!=0 && $yarn_comp_percent1st!='' )
				{
					$yarndtls=$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st.' %'; 
				}
				else if($yarn_comp_type1st!=0)
				{
					$yarndtls=$composition[$yarn_comp_type1st]; 
				}
				else
				{
					$yarndtls=''; 
				}				
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    <td width="30"><? echo $i; ?></td>
                    <?
						if ($wo_basis==3)
						{
					?>
                    <td width="70" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
                    <td width="90" align="center"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                    <td width="100" align="center"><p><? echo $row[csf("po_number")]; ?></p></td>
					<?
						}
						else if($wo_basis==1)
						{
					?>
					<td width="70" align="center"><p>
						<? echo $job_data_info_arr[$row[csf('requisition_dtls_id')]]['job_no_prefix_num']; ?>
					</p></td>
                    <td width="90" align="center"><p>
                    	<? echo $buyer_arr[$job_data_info_arr[$row[csf('requisition_dtls_id')]]['buyer_name']]; ?>
                    </p></td>
                    <td width="100" align="center"><p>
                    	<? echo $job_data_info_arr[$row[csf('requisition_dtls_id')]]['style_ref_no']; ?>
                    </p>
                	</td>
					<?  
						}
					?>
                    <td width="70" align="center"><p><? echo $row[csf("wo_number_prefix_num")]; ?></p></td>
                    <td width="75" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?></p></td>
                    <td width="75" align="center"><p><? echo change_date_format($row[csf("delivery_date")]); ?></p></td>
                    <td width="100" align="center"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                    <td width="80" align="center"><p><? //echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                    <td width="75" align="center"><p><? //echo change_date_format($row[csf("delivery_date")]); ?></p></td>
                    <td width="130" align="center"><p><? echo $yarndtls; ?></p></td>
                    <td width="70" align="center"><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
                    <td width="60" align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count")]]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf("supplier_order_quantity")],2,'.',''); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($row[csf("rate")],3,'.',''); ?>&nbsp;</p></td>
                    <td width="90" align="right"><p><? echo number_format($row[csf("amount")],2,'.',''); ?>&nbsp;</p></td>
                    <td width=""><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
			<?
			$total_qty+=$row[csf("supplier_order_quantity")];
			$total_amount+=$row[csf("amount")];
            $i++;
		}
		?>
            </table>
            <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <?
						if ($wo_basis==3)
						{
					?>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<?
						}
						else if ($wo_basis==1)
						{
					?>
					<th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<? 
						}
					?>
                    <th width="70">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <!-- <th width="80" align="right" id="total_qty"><? //echo number_format($total_qty,2,'.',''); ?></th> -->
                    <th width="80" align="right"><? echo number_format($total_qty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="90" align="right"><? echo number_format($total_amount,2,'.',''); ?></th>
                    <!-- <th width="90" align="right" id="total_amount"><? //echo number_format($total_amount,2,'.',''); ?></th> -->
                    <th width="">&nbsp;</th>
                </tfoot>
            </table> 
        </div>
    </fieldset>
    <?
	/*foreach (glob("$user_id*.xls") as $filename) 
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
	echo "$total_data****$filename";
	exit();*/

	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_id*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename"; 
	
}
disconnect($con);
?>
