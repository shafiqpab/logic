<?

header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.trims.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//---------------------------------------------------- Start---------------------------------------------------------------------------

if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 152, "select id, location_name from lib_location where company_id in($data) and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected,"Floor_load(this.value)" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data_ref=explode("*",$data);
	$location_id=$data_ref[0];
	$company_id=$data_ref[1];
	echo create_drop_down( "cbo_floor_name", 152, "SELECT id,floor_name from lib_prod_floor where company_id in($company_id) and location_id=$location_id and production_process=5 and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "Swine_load(this.value)" );
	exit();
}

if($action=="load_drop_down_sewing_line")
{
	$explode_data = explode("_",$data);
	$location = $explode_data[2];
	$company = $explode_data[1];
	$floor = $explode_data[0];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id in($company) and a.location_id='$location' and a.floor_id='$floor' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	echo create_drop_down( "cbo_sewing_line", 152,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
}

if ($action=="load_drop_down_buyer")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
	exit();
}


if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo date("Y");die;
	//echo "p__". $floor_name."__".$sewing_line; die();
	?> 

	<script>
		 function js_set_value(job_no) {
            $("#hidden_job_no").val(job_no);       
            parent.emailwindow.hide();
        }
    </script>

	</head>

	<body onLoad="set_hotkey()">
	<div align="center" style="width:1190px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1190px;">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1150" class="rpt_table">
					<thead>
						<th width="130">Company</th>
						<th width="130">Buyer</th>
						<th width="130">Job Year</th>
						<th width="130">Search By</th>
						<th width="130">Search</th>
                        <th width="180"><? if($cbo_trim_type==1) echo "Cutting Date Range"; else echo "Input Date Range"; ?></th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />				
							<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">							
						</th> 
					</thead>
					<tr class="general">
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  and comp.id=$cbo_company_id order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                            ?>                            
                        </td>
						<td align="center">	
							<?
							echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
							?> 
						</td>
						<td align="center"><? echo create_drop_down( "cbo_job_year", 120, $year,"", 1, "-- Select Year --", date("Y"), "" );?></td>
						<td align="center">	
							<?
								$search_by_arr=array(1=>"Req No",2=>"Job No",3=>"Style Ref.",4=>"Po No");
								echo create_drop_down( "cbo_search_by", 140, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td align="center">				
							<input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" />&nbsp;To&nbsp;
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" />
                        </td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $floor_name ?>+'_'+<? echo $sewing_line ?>, 'create_po_search_list_view', 'search_div', 'date_wise_requisition_and_style_wise_requisition_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
                    <tr>                  
                        <td align="center" height="40" valign="middle" colspan="6"> <? echo load_month_buttons(1);  ?></td>
                    </tr> 
			</table>
				<div style="margin-top:10px;" id="search_div" align="left"></div> 
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data = explode("_",$data);
	// print_r($data);die;
	$cbo_buyer_name=trim(str_replace("'","",$data[0]));
	$cbo_job_year=trim(str_replace("'","",$data[1]));
	$cbo_search_by =trim(str_replace("'","",$data[2]));
	$txt_search_common=trim(str_replace("'","",$data[3]));
	$cbo_company_id =trim(str_replace("'","",$data[4]));
	$date_form =trim(str_replace("'","",$data[5]));
	$date_to =trim(str_replace("'","",$data[6]));
	$floor_name =trim(str_replace("'","",$data[7]));
	$sewing_line =trim(str_replace("'","",$data[8]));

	$buyer_arr = return_library_array("SELECT buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id",'id','short_name');
	$company_arr = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');

	if($cbo_buyer_name==0 && $txt_search_common=="") 
	{
		if( $date_form=="" && $date_to=="")
		{
			echo "Please Select Specific Reference.";
			die;
		}
	}
	$sql_cond="";
	if($cbo_buyer_name>0) $sql_cond.=" and d.BUYER_NAME=$cbo_buyer_name";
	if($floor_name>0) $sql_cond.=" and a.FLOOR_ID=$floor_name";
	if($sewing_line>0) $sql_cond.=" and a.SEWING_LINE=$sewing_line";
	if($txt_search_common!="")
	{
		if($cbo_search_by==1) $sql_cond.=" and a.REQ_NO like '%$txt_search_common'";
		else if($cbo_search_by==2) $sql_cond.=" and d.JOB_NO like '%$txt_search_common'";
		else if($cbo_search_by==3) $sql_cond.=" and d.STYLE_REF_NO ='$txt_search_common'";
		else if($cbo_search_by==4) $sql_cond.=" and c.PO_NUMBER='$txt_search_common'";
		 
	}
	if($date_form!="" && $date_to!="") $sql_cond.=" and a.REQUISITION_DATE between '" . change_date_format($date_form, '', '', 1) . "' and '" . change_date_format($date_to, '', '', 1) . "'";
	if($cbo_job_year>0)
	{
		if($db_type==0) $sql_cond.=" and year(a.insert_date)='$cbo_job_year'";
		else $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
	}

	 $po_sql="SELECT a.COMPANY_ID, a.WORKING_COMPANY_ID, a.REQ_NO, a.REQUISITION_DATE,d.JOB_NO, d.STYLE_REF_NO, d.BUYER_NAME FROM ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b, wo_po_break_down c, wo_po_details_master d WHERE a.id=b.mst_id and d.id=b.job_id and d.id=c.job_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.WORKING_COMPANY_ID=$cbo_company_id $sql_cond group by  a.COMPANY_ID, a.WORKING_COMPANY_ID, a.REQ_NO, a.REQUISITION_DATE,d.JOB_NO, d.STYLE_REF_NO, d.BUYER_NAME";

	$result = sql_select($po_sql);
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="100">Buyer</th>
                <th width="120">Job No</th>			
                <th width="150">Style Ref. No.</th>
                <th width="80">Req Date</th>
                <th width="120">Req No</th>               
                <th width="100">Floor</th>
                <th>Sewing Line</th>
            </thead>
        </table>
        <div style="width:1010px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="tbl_list_search">  
            <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                if(in_array($selectResult[csf('id')],$hidden_po_id)) 
                {
                    if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  id="search<? echo $i;?>" onClick="js_set_value('<? echo $row['JOB_NO']; ?>');"> 
                    <td width="30" align="center"><? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['PO_ID']; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['ITEM_GROUP_ID']; ?>"/>
                    </td>
                    <td width="120"><p><? echo $company_arr[$row['WORKING_COMPANY_ID']]; ?>&nbsp;</p></td>
                    <td width="100" title="<?= $row['BUYER_NAME'];?>"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row['JOB_NO']; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $row['STYLE_REF_NO']; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row['REQUISITION_DATE']) ?>&nbsp;</p></td>
                    <td width="120"><? echo $row['REQ_NO']; ?></td>               
                    <td width="100" align="center"><p><? echo $row['FLOOR_ID'];  ?>&nbsp;</p></td>
                    <td><p><? echo $color_arr[$row['SEWING_LINE']]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
            </table>
        </div>
        <?
	?>
	
    <table width="990" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%"> 
                    <div style="width:50%; float:left" align="left">&nbsp;     
                    </div>
                    <div style="width:50%; float:left" align="left">
                    </div>
                </div>
            </td>
        </tr>
    </table>
	<?
	exit();
}


if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_working_location=str_replace("'","",$cbo_working_location);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_floor_name=str_replace("'","",$cbo_floor_name);
	$cbo_sewing_line=str_replace("'","",$cbo_sewing_line);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","", $report_type);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("SELECT buy.id, buy.BUYER_NAME from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id",'id','BUYER_NAME');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$line_array_new = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1",'id','line_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5",'id','floor_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	if($report_type==1){
		$sql_cond="";
		if( $txt_date_from!="" && $txt_date_to!="") $req_date= " and e.REQUISITION_DATE between '".$txt_date_from."' and '".$txt_date_to."'"; else $req_date="";

		if($txt_job_no!=""){$sql_cond.=" and a.JOB_NO like '%$txt_job_no'";}
		$sql_cond .= ($cbo_buyer!=0) ? " and a.BUYER_NAME=$cbo_buyer" : "";
		$sql_cond .= ($cbo_working_location!=0) ? " and e.working_location_id=$cbo_working_location" : "";
		$sql_cond .= ($cbo_floor_name!=0) ? " and e.FLOOR_ID=$cbo_floor_name" : "";
		$sql_cond .= ($cbo_sewing_line!=0) ? " and e.SEWING_LINE=$cbo_sewing_line" : "";

		$order_sql="SELECT a.id as job_id, a.job_no, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number, sum(b.po_quantity) as po_quantity, c.id as dtls_id, sum(c.cons) as rcv_qnty, sum(c.stock_qnty) as stock_qnty, sum(c.reqsn_qty) as reqsn_qty, e.sewing_line, e.prod_reso_allo, e.company_id, e.working_company_id, e.working_location_id, c.color_id,c.item_description,c.cons_uom, c.trim_group, c.precost_trim_dtls_id, c.gmts_color_id, sum(c.size_input_qty) as size_input_qty
		from wo_po_details_master a, wo_po_break_down b,ready_to_sewing_reqsn_all_po d, ready_to_sewing_reqsn c,ready_to_sewing_reqsn_mst e where a.id=b.job_id and d.PO_ID=b.id  and d.SEWING_REQSN_DTLS_ID=c.id  and e.id=c.mst_id and c.entry_form in(357) and c.status_active=1 and c.is_deleted=0  and e.working_company_id in($cbo_company) $req_date $sql_cond
		group by a.id, a.job_no, a.style_ref_no, a.buyer_name, b.id , b.po_number, c.id , e.sewing_line, e.prod_reso_allo, e.company_id, e.working_company_id, e.working_location_id, c.color_id,c.item_description,c.cons_uom, c.trim_group, c.precost_trim_dtls_id, c.gmts_color_id order by b.id";

		$order_sql_result = sql_select($order_sql);
		$order_sql = sql_select($order_sql);
		$all_job_id_arr=array();$all_job="";
		foreach($order_sql as $row){
			$all_job_id_arr[$row["JOB_ID"]]=$row["JOB_ID"];
			$all_job.="'".$row["JOB_NO"]."',";
		}
		$unique_job_string = implode(',', array_unique(explode(',', rtrim($all_job, ','))));

		$con = connect();
		$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=163 and user_id=$user_id");
		if($rid) oci_commit($con);
		if(!empty($all_job_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 163, 1, $all_job_id_arr,$empty_arr);
			$cz_sql="SELECT a.ID as TRIMID, b.po_break_down_id, b.color_number_id, a.cons_dzn_gmts as CONS_DZN_GMTS
			from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b, gbl_temp_engine c
			where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and b.JOB_ID=c.ref_val and c.entry_form=163 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.REF_FROM=1 and c.user_id= $user_id";
			$cz_sql_res = sql_select($cz_sql);
			$cons_dz_arr=array();
			foreach($cz_sql_res as $row){
				$cons_dz_arr[$row["TRIMID"]][$row["PO_BREAK_DOWN_ID"]][$row["COLOR_NUMBER_ID"]]["CONS_DZN_GMTS"]=$row["CONS_DZN_GMTS"];
			}
		}

		$gm_sql="SELECT a.JOB_NO, b.GMT_ITEM_ID, b.COLOR_ID from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_no in($unique_job_string) and a.COMPANY_ID in($cbo_company) group by a.JOB_NO, b.GMT_ITEM_ID, b.COLOR_ID";
		$gm_sql_res = sql_select($gm_sql);
		$cons_gm_arr=array();
		foreach($gm_sql_res as $row){
			$cons_gm_arr[$row["JOB_NO"]]["GMT_ITEM_ID"]=$row["GMT_ITEM_ID"];
			$cons_gm_arr[$row["JOB_NO"]]["COLOR_ID"]=$row["COLOR_ID"];
		}
		// print_r($cons_dz_arr);
		$width=2300;
		ob_start();
		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="20" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold" ><? echo "Style Wise Asseccories Requisition Report"; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
	                    <th width="35">SL</th>
	                    <th width="140">Working Company</th>
	                    <th width="100">Working Location</th>
	                    <th width="100">Company Name</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Job Name</th>
	                    <th width="100">Style Referance</th>
	                    <th width="100">Garments Item</th>
	                    <th width="100">Po</th>
	                	<th width="100">Color</th>
	                    <th width="100">Name Of Item</th>
	                    <th width="100">Description</th>
	                    <th width="100">Consumption/DZN</th>
	                    <th width="80">UOM</th>
	                    <th width="100">Requair Qty</th>
	                    <th width="100">TTL Requisition Qty</th>
	                    <th width="60">Requ. Balance</th>
	                    <th width="60">Store Rcv. Qty</th>
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					foreach($order_sql_result as $row)
					{					 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"  align="center"><? echo $i;?></td>
							<td width="140" style="word-break: break-all;" align="center"><? echo $companyArr[$row[csf('working_company_id')]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $location_arr[$row[csf('working_location_id')]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $companyArr[$row[csf('company_id')]];?></td>										
							<td width="100" style="word-break: break-all;" align="center"><? echo $buyer_arr[$row[csf('buyer_name')]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo  $row[csf('job_no')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('style_ref_no')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $garments_item[$cons_gm_arr[$row["JOB_NO"]]["GMT_ITEM_ID"]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><?php echo $row[csf('po_number')] ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?php echo $color_arr[$cons_gm_arr[$row["JOB_NO"]]["COLOR_ID"]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><? echo $item_group_arr[$row[csf('trim_group')]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><? echo $row[csf('item_description')]?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left" style="word-break:break-all;"><? echo $cons_dz_arr[$row["PRECOST_TRIM_DTLS_ID"]][$row["PO_ID"]][$row["GMTS_COLOR_ID"]]["CONS_DZN_GMTS"]; ?></td>
							<td width="80" align="center" style="word-break: break-all;" align="left"><?  echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="right"><? echo number_format($row[csf('rcv_qnty')]) ?></td>
							<td width="100" style="word-break: break-all;" align="center">&nbsp;<? echo number_format($row[csf('reqsn_qty')]) ?></td>  
							<td width="60" style="word-break: break-all;" align="right"><? echo number_format( $row[csf('rcv_qnty')]-$row[csf('reqsn_qty')]) ?></td>
							<td width="60" style="word-break: break-all;" align="right"><? echo $row[csf('size_input_qty')]?></td>
						</tr>
						<?$i++;
					}
					?>
	       		 </table>
	        </div>
	    </div>
	<?
	}
	else
	{ 
		$sql_cond="";
		if( $txt_date_from!="" && $txt_date_to!="") $req_date= " and a.REQUISITION_DATE between '".$txt_date_from."' and '".$txt_date_to."'"; else $req_date="";

		if($txt_job_no!=""){$sql_cond.=" and b.JOB_NO like '%$txt_job_no'";}
		$sql_cond .= ($cbo_buyer!=0) ? " and d.BUYER_NAME=$cbo_buyer" : "";
		$sql_cond .= ($cbo_working_location!=0) ? " and a.working_location_id=$cbo_working_location" : "";
		$sql_cond .= ($cbo_floor_name!=0) ? " and a.FLOOR_ID=$cbo_floor_name" : "";
		$sql_cond .= ($cbo_sewing_line!=0) ? " and a.SEWING_LINE=$cbo_sewing_line" : "";
		
		$dtls_sql="SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, b.PO_ID, b.COLOR_SIZE_TABLE_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.JOB_NO, b.GMTS_COLOR_ID, b.CONS_UOM, b.REMARKS, d.STYLE_REF_NO, d.BUYER_NAME, d.id as JOB_ID, sum(b.cons) as rcv_qnty, sum(b.reqsn_qty) as reqsn_qty, sum(b.size_input_qty) as size_input_qty, a.INSERT_DATE, a.INSERTED_BY, a.UPDATED_BY, a.UPDATE_DATE
		from READY_TO_SEWING_REQSN_MST a, READY_TO_SEWING_REQSN b, wo_po_break_down c, wo_po_details_master d, ready_to_sewing_reqsn_all_po e
		WHERE a.id=b.mst_id and e.SEWING_REQSN_DTLS_ID=b.id and e.PO_ID=c.id and c.job_id=d.id and a.ENTRY_FORM=357 and a.working_company_id in($cbo_company) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and B.STATUS_ACTIVE=1 and B.IS_DELETED=0 $sql_cond $req_date group by a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, b.PO_ID, b.COLOR_SIZE_TABLE_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.JOB_NO, b.GMTS_COLOR_ID, b.CONS_UOM, b.CONS, b.REQSN_QTY, b.REMARKS, d.STYLE_REF_NO, d.BUYER_NAME, d.id, a.INSERT_DATE, a.INSERTED_BY, a.UPDATED_BY, a.UPDATE_DATE";
		
       $date_wish_result = sql_select($dtls_sql);
	   $order_sql = sql_select($dtls_sql);
		$all_job="";
		foreach($order_sql as $row){
			$all_job.="'".$row["JOB_NO"]."',";
			$all_job_arr[$row["JOB_ID"]]=$row["JOB_ID"];
		}
		$unique_job_string = implode(',', array_unique(explode(',', rtrim($all_job, ','))));

		$con = connect();
		$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=163 and user_id=$user_id");
		if($rid) oci_commit($con);
		if(!empty($all_job_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 163, 1, $all_job_arr,$empty_arr);
			$cz_sql="SELECT a.ID as TRIMID, b.po_break_down_id, b.color_number_id, a.cons_dzn_gmts as CONS_DZN_GMTS
			from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b, gbl_temp_engine c
			where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and b.JOB_ID=c.ref_val and c.entry_form=163 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.REF_FROM=1 and c.user_id= $user_id";
			$cz_sql_res = sql_select($cz_sql);
			$cons_dz_arr=array();
			foreach($cz_sql_res as $row){
				$cons_dz_arr[$row["TRIMID"]][$row["PO_BREAK_DOWN_ID"]][$row["COLOR_NUMBER_ID"]]["CONS_DZN_GMTS"]=$row["CONS_DZN_GMTS"];
			}
		}

	   $gm_sql="SELECT a.JOB_NO, b.GMT_ITEM_ID, b.COLOR_ID from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_no in($unique_job_string) and a.COMPANY_ID in($cbo_company) group by a.JOB_NO, b.GMT_ITEM_ID, b.COLOR_ID";
		$gm_sql_res = sql_select($gm_sql);
		$cons_gm_arr=array();
		foreach($gm_sql_res as $row){
			$cons_gm_arr[$row["JOB_NO"]]["GMT_ITEM_ID"]=$row["GMT_ITEM_ID"];
			$cons_gm_arr[$row["JOB_NO"]]["COLOR_ID"]=$row["COLOR_ID"];
		}
		// reset($gm_sql_res);

		$width=3900;?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="20" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold" ><? echo "Date Wise Asseccories Requisition Report"; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
	                    <th width="35">SL</th>
	                    <th width="100">Requisition Date</th>
	                    <th width="100">Requisition No</th>
	                    <th width="140">Working Company</th>
	                    <th width="100">Working Location</th>
	                    <th width="100">Floor</th>
	                    <th width="100">Sewing Line</th>
	                    <th width="100">Delivery Date</th>
	                    <th width="100">Requisition Basis</th>
	                	<th width="100">Company Name</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Job No</th>
	                    <th width="100">Style Reference</th>
	                    <th width="100">Garments Item</th>
	                    <th width="100">System Cut No</th>
	                    <th width="100">Order Cut No</th>
	                    <th width="60">Color</th>
	                    <th width="120">Name Of Item</th>
	                    <th width="100">Description</th>
	                    <th width="100">Consumption/DZN</th>
	                    <th width="100">UOM</th>
	                    <th width="100">Required Qty</th>
	                    <th width="100">Booking Qty</th>
	                    <th width="100">Previous Requisition Qty</th>
	                    <th width="100">Current Requisition Qty</th>
	                    <th width="100">TTL Requisition Qty</th>
	                    <th width="100">Requ. Balance</th>
	                    <th width="100">Store Rcv. Qty</th>
	                    <th width="100">Insert User</th>
	                    <th width="100">Insert Date and Time</th>
	                    <th width="100">Last Update User</th>
	                    <th width="100">Last Update Date and Time</th>
	                    <th width="100">Remarks</th>
					</thead>
				</table>
	        <div style="width:<? echo $width;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
	            <? 
				// echo "DEVELOP RUNNING";die;
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					foreach($date_wish_result as $row)
					{					 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"  align="center"><? echo $i;?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('requisition_date')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('req_no')];?></td>
							<td width="140" style="word-break: break-all;" align="center"><? echo $companyArr[$row[csf('working_company_id')]];;?></td>										
							<td width="100" style="word-break: break-all;" align="center"><? echo $location_arr[$row[csf('working_location_id')]]?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo  $floor_arr[$row[csf('floor_id')]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $line_array_new[$row[csf('sewing_line')]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row["DELIVERY_DATE"] ;?></td>
							<td width="100" style="word-break: break-all;" align="center"><?php echo $trim_type[$row[csf('trim_type')]] ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?php echo $companyArr[$row[csf('company_id')]] ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><? echo $buyer_arr[$row[csf('buyer_name')]];?></td>	
							<td width="100" align="center" style="word-break: break-all;" align="left"><? echo $row[csf('job_no')]?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left" style="word-break:break-all;"><? echo $row["STYLE_REF_NO"]; ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="center"><?  echo $garments_item[$cons_gm_arr[$row["JOB_NO"]]["GMT_ITEM_ID"]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="right"><? echo "need" ?></td>
							<td width="100" style="word-break: break-all;" align="center">&nbsp;<? echo "need" ?></td>  
							<td width="60" style="word-break: break-all;" align="center"><? echo $color_arr[$cons_gm_arr[$row["JOB_NO"]]["COLOR_ID"]]; ?></td>
							<td width="120" style="word-break: break-all;" align="center"><? echo $item_group_arr[$row[csf('trim_group')]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo $row[csf('item_description')];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo $cons_dz_arr[$row["PRECOST_TRIM_DTLS_ID"]][$row["PO_ID"]][$row["GMTS_COLOR_ID"]]["CONS_DZN_GMTS"];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo number_format($row[csf('rcv_qnty')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo number_format($row[csf('rcv_qnty')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo "need";?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo number_format($row[csf('reqsn_qty')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo number_format($row[csf('reqsn_qty')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo number_format($row[csf('rcv_qnty')]-$row[csf('reqsn_qty')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo number_format($row[csf('size_input_qty')])?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo $user_library[$row[csf('inserted_by')]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo change_date_format($row[csf('insert_date')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo $user_library[$row[csf('updated_by')]];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo change_date_format($row[csf('update_date')]);?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?  echo $row[csf('remarks')];?></td>
						</tr>
						<?$i++;
					}
					?>
	       		 </table>
	        </div>
	    </div>
	  <?
	}

    $r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=163");
	oci_commit($con);
	disconnect($con);
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
	
}

?>