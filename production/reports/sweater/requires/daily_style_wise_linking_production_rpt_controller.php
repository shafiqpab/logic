<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			$("#hide_style_no").val(splitData[2]); 
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
                    <th>Job Year</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                </thead>
                <tbody>
                	<tr class="general">
                        <td>
							<? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?>
						</td>    
						<td width="60">
                        	<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date('Y'), "",0,"" ); ?>
                        </td>             
                        <td>	
							<?
								$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
                        </td>     
                        <td id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
						</td> 	
                        <td>
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_job_no_search_list_view', 'search_div', 'daily_style_wise_linking_production_rpt_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="create_job_no_search_list_view")
{
	list($company_id,$buyer_id,$year_id,$search_by,$search_string)=explode('**',$data);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else 
	{ 
		$buyer_id_cond=" and buyer_name=$buyer_id"; 
	}

	if(trim($search_string)!='')
	{
		if($search_by==1){
			$search_field=" and job_no like '%".trim($search_string)."%'";
		}else {
			$search_field=" and style_ref_no like '%".trim($search_string)."%'";
		}
	}

	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $search_field $buyer_id_cond $year_cond  order by id Desc";
	// echo $sql;
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} 

if($action=="report_generate") 
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr 	= return_library_array("select id,buyer_name from lib_buyer","id","buyer_name"); 
	// ================================= GETTING FORM DATA ====================================
	$lc_company_id 		= str_replace("'","",$cbo_company_id);
	$wo_company_name	= str_replace("'","",$cbo_wo_company_name);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$hdn_job_id 		= str_replace("'","",$hdn_job_id);
	$report_title 		= str_replace("'","",$report_title);
	$txt_date			= str_replace("'","",$txt_date);
	$cbo_ship_status	= str_replace("'","",$cbo_ship_status);
	$rpt_type	= str_replace("'","",$rpt_type);

	if($rpt_type==1)
	{
		$search_cond="";	
		if($lc_company_id) {$search_cond.=" and a.company_id=$lc_company_id ";}
		if($wo_company_name) {$search_cond.=" and a.working_company_id=$wo_company_name ";}	
		if($hdn_job_id!=""){$search_cond.=" and e.id=$hdn_job_id"; }

		if($buyer_id==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $search_cond.=" and e.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; 
			}
		}
		else 
		{ 
			$search_cond.=" and e.buyer_name=$buyer_id"; 
		}

		if($cbo_ship_status==1){ $search_cond.=" and d.shiping_status in (1,2)"; } 
		else if($cbo_ship_status==2){ $search_cond.=" and d.shiping_status in (3)"; }

		if($db_type==0) $select_date=change_date_format($txt_date,'yyyy-mm-dd');
		if($db_type==2) $select_date=change_date_format($txt_date,'','',1);

		$sql="SELECT a.working_company_id as WORKING_COMPANY_ID, e.id as JOB_ID, e.buyer_name as BUYER_NAME, e.job_no_prefix_num as JOB_NO_NUM, e.style_ref_no as STYLE_REF_NO, e.gauge as GAUGE, d.id as PO_ID, d.po_quantity as PO_QUANTITY, d.plan_cut as PLAN_CUT,
		sum(case when a.production_type=55 and a.delivery_date<'$select_date' then c.production_qnty else 0 end) as LINKING_INPUT_BEFORE,
		sum(case when a.production_type=56 and a.delivery_date<'$select_date' then c.production_qnty else 0 end) as LINKING_OUTPUT_BEFORE,
		sum(case when a.production_type=55 and a.delivery_date = '$select_date' then c.production_qnty else 0 end) as LINKING_INPUT_TODAY,
		sum(case when a.production_type=56 and a.delivery_date = '$select_date' then c.production_qnty else 0 end) as LINKING_OUTPUT_TODAY
		from pro_gmts_delivery_mst a, pro_garments_production_mst b, pro_garments_production_dtls c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.delivery_mst_id and a.id=c.delivery_mst_id and b.id=c.mst_id and b.po_break_down_id=d.id and d.job_id=e.id and a.production_type in (55,56) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $search_cond 
		group by a.working_company_id,e.id,e.buyer_name,e.job_no_prefix_num,e.style_ref_no,e.gauge,d.id,d.po_quantity,d.plan_cut ";
		// echo $sql;die;
		$sql_result = sql_select($sql);
		$all_data_arr=$po_id_arr=array();
		foreach($sql_result as $row)
		{
			$all_data_arr[$row["JOB_ID"]]["working_company_id"]=$row["WORKING_COMPANY_ID"];
			$all_data_arr[$row["JOB_ID"]]["buyer_name"]=$row["BUYER_NAME"];
			$all_data_arr[$row["JOB_ID"]]["job_no_num"]=$row["JOB_NO_NUM"];
			$all_data_arr[$row["JOB_ID"]]["style_ref_no"]=$row["STYLE_REF_NO"];
			$all_data_arr[$row["JOB_ID"]]["gauge"]=$row["GAUGE"];
			$all_data_arr[$row["JOB_ID"]]["linking_input_before"]+=$row["LINKING_INPUT_BEFORE"];
			$all_data_arr[$row["JOB_ID"]]["linking_output_before"]+=$row["LINKING_OUTPUT_BEFORE"];
			$all_data_arr[$row["JOB_ID"]]["linking_input_today"]+=$row["LINKING_INPUT_TODAY"];
			$all_data_arr[$row["JOB_ID"]]["linking_output_today"]+=$row["LINKING_OUTPUT_TODAY"];
			if(!in_array($row["PO_ID"],$po_id_arr))
			{
				$po_id_arr[]=$row["PO_ID"];
				$all_data_arr[$row["JOB_ID"]]["po_quantity"]+=$row["PO_QUANTITY"];
				$all_data_arr[$row["JOB_ID"]]["plan_cut"]+=$row["PLAN_CUT"];
			}
		}
		$table_width="1350";
		$i=1;
		ob_start();
		?>
		<div>
			<table style="width:<? echo $table_width+18; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left" >
				<thead>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							<b><? echo $companyArr[$lc_company_id]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							<b><?=$report_title;?></b>
						</td>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="120">Working Company</th>
						<th width="120">Buyer</th>
						<th width="80">Job No</th>
						<th width="120">Style no</th>
						<th width="80">Guage</th>
						<th width="80">Order Qty</th>
						<th width="80">Plan Knit</th>
						<th width="80">Prev. Input Qty</th>
						<th width="80">Today Input Qty</th>
						<th width="80">Total Input Qty</th>
						<th width="80">Input Bal. Qty</th>
						<th width="80">Prev. Output Qty</th>
						<th width="80">Today Output Qty</th>
						<th width="80">Total Output Qty</th>
						<th >Link. Prod. Bal. Qty</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+18; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body" >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
					<?
					foreach($all_data_arr as $row)
					{
						if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						$total_linking_input=$row["linking_input_before"]+$row[("linking_input_today")];
						$total_linking_output=$row["linking_output_before"]+$row[("linking_output_today")];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><p><? echo $i; ?></p></td>
							<td width="120" align="center"><p><? echo $companyArr[$row["working_company_id"]];?></p></td>
							<td width="120"><p><? echo $buyerArr[$row["buyer_name"]]; ?></p></td>
							<td width="80"><p><? echo $row["job_no_num"]; ?></p></td>
							<td width="120"><p><? echo $row["style_ref_no"]; ?></p></td>
							<td width="80" align="center"><p><? echo $gauge_arr[$row["gauge"]]; ?></p></td>
							<td width="80" align="right"><? echo number_format($row["po_quantity"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["plan_cut"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["linking_input_before"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["linking_input_today"],2); ?></td>
							<td width="80" align="right"><? echo number_format($total_linking_input,2); ?></td>
							<td width="80" align="right"><? echo number_format($row["plan_cut"]-$total_linking_input,2); ?></td>
							<td width="80" align="right"><? echo number_format($row["linking_output_before"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["linking_output_today"],2); ?></td>
							<td width="80" align="right"><? echo number_format($total_linking_output,2); ?></td>
							<td align="right"><? echo number_format($row["plan_cut"]-$total_linking_output,2); ?></td>
						</tr>
						<?
						$tot_po_qnty+=$row["po_quantity"];
						$tot_plan_cut_qnty+=$row["plan_cut"];
						$tot_linking_input_before+=$row["linking_input_before"];
						$tot_linking_input_today+=$row["linking_input_today"];
						$tot_linking_input+=$total_linking_input;
						$tot_balance_linking_input+=$row["plan_cut"]-$total_linking_input;
						$tot_linking_output_before+=$row["linking_output_before"];
						$tot_linking_output_today+=$row["linking_output_today"];
						$tot_linking_output+=$total_linking_output;
						$tot_balance_linking_output+=$row["plan_cut"]-$total_linking_output;
						$i++;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="6">Total:</th>
							<th><? echo number_format($tot_po_qnty,2); ?></th>
							<th><? echo number_format($tot_plan_cut_qnty,2); ?></th>
							<th><? echo number_format($tot_linking_input_before,2); ?></th>
							<th><? echo number_format($tot_linking_input_today,2); ?></th>
							<th><? echo number_format($tot_linking_input,2); ?></th>
							<th><? echo number_format($tot_balance_linking_input,2); ?></th>
							<th><? echo number_format($tot_linking_output_before,2); ?></th>
							<th><? echo number_format($tot_linking_output_today,2); ?></th>
							<th><? echo number_format($tot_linking_output,2); ?></th>
							<th><? echo number_format($tot_balance_linking_output,2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<?	
		$html = ob_get_contents();
		ob_clean();
	}

    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename####$rpt_type";
    exit();
}
?>