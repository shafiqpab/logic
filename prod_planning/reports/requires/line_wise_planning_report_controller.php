<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All -", $selected, "" );     	 
	exit();
}

if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and company_id in($data)","id,location_name", 0, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="load_drop_down_floor")
{
	$data_arr = explode("_", $data);
	$company_id = $data_arr[0];
	$location_id = $data_arr[1];
	echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor  where status_active=1  and company_id in($company_id) and location_id in($location_id) and production_process=5","id,floor_name", 0, "-- Select --", $selected, "" );   	 
	exit();
}

 

if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_style_id").val(splitData[0]); 
			$("#hide_style_no").val(splitData[1]); 
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
                    <th id="search_by_td_up" width="170">Please Enter PO No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"PO");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_style_no_search_list_view', 'search_div', 'line_wise_planning_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_style_no_search_list_view")
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
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string=trim($data[3]);
	
	if($search_string!=''){
		if($search_by==1){$search_con=" and a.job_no like('%$search_string')";}
		else if($search_by==2){$search_con=" and a.style_ref_no like('%$search_string')";}
		else if($search_by==3){ $search_con=" and b.po_number like('%$search_string')";}
	}
 	
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	 
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "SELECT b.po_number, a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.company_name in($company_id) $search_con  $buyer_id_cond $year_cond  order by job_no";
	//echo $sql;die;
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO", "120,130,80,60,80","600","240",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0','') ;
	exit(); 
}  

 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	 
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$style_no=str_replace("'","",$txt_style_no);
	$style_id=str_replace("'","",$txt_style_id);  
	$location_id=str_replace("'","",$cbo_location_id);  
	$floor_id=str_replace("'","",$cbo_floor_id);  
	$loc_cond="";	//if($location_id)$loc_cond.=" and c.location_id in($location_id)";
	$loc_cond2="";	if($location_id)$loc_cond2.=" location_name in($location_id)";
	$floor_cond="";	if($floor_id)$floor_cond.="  floor_name in($floor_id)";
	$floor_cond2="";	if($floor_id)$floor_cond2.="  and floor_name in($floor_id)";
	
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$line_wise_floor=return_library_array( "select id, floor_name from lib_sewing_line",'id','floor_name');
	$all_line=return_library_array( "select id, id from lib_sewing_line where $floor_cond ",'id','id');
	$all_line2= sql_select( "select id, id from lib_sewing_line where $loc_cond2 $floor_cond2 ");
	foreach($all_line2 as $ll)
	{
		$all_line[$ll[csf("id")]]=$ll[csf("id")];
	}
	$all_line_ids=implode(",", array_unique($all_line));
	$line_conds="";
	if($all_line_ids)$line_conds=" and c.line_id in($all_line_ids)";
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	$style_cond="";
	if ($style_no)
		$style_cond.=" and a.style_ref_no like'%$style_no%'  ";
	if($style_id)$style_cond.=" and a.id=$style_id ";
	 
	 
	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	
	if($db_type==0)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
	}
	else if($db_type==2)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	}
	
	if($db_type==0)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
	}
	else if($db_type==2)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		$loop_st_date= date("d-M-y",strtotime($start_date));
		$loop_end_date= date("d-M-y",strtotime($end_date));
	}
	$startTime = strtotime( $start_date );
	$endTime = strtotime( $end_date);

 	$row_count=0;
	for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 )
	{
 		$row_count++;
	}
	 
	
	
	$date_cond="";
	$date_cond.=" and d.plan_date between '$start_date' and '$end_date'"; 
	$extra_sel="";
	if($type==2)
	{
		$extra_sel.=",d.plan_date,d.plan_qnty as date_wise_qty ";
	}

	$sql_data="SELECT a.job_no, b.unit_price,c.terget, a.set_smv as smv, b.plan_cut, c.company_id,c.location_id,c.line_id,e.plan_qnty,c.plan_id,  b.id as po_id,b.pub_shipment_date,b.po_quantity,b.plan_cut,c.start_date,c.end_date, b.po_number,a.buyer_name,a.style_ref_no as style,e.item_number_id $extra_sel
	from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e  where  a.job_no=b.job_no_mst and a.job_no=e.job_no and b.id=c.po_break_down_id and c.plan_id=d.plan_id and d.plan_id=e.plan_id  and c.company_id in($company_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $loc_cond $line_conds  $buyer_id_cond  $date_cond $style_cond order by c.line_id asc	";
	
	$data_result=sql_select($sql_data);
	$line_wise_arr=array();
	$rowcount=count($data_result);
	$styleArr=array();
	$line_total=array();
	$all_po=array();
	$date_wise_arr=array();
	$ttl_qnty_arr=array();
	$month_arr=array();
	$month_wise_qty_arr=array();
	//$pre_cost_arr = sql_select("select job_no, cm_cost, cm_for_sipment_sche,margin_pcs_set from wo_pre_cost_dtls"); 
	$pre_cost_arr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );

	foreach( $data_result as $row)
	{
		  
		$all_po[$row[csf("po_id")]]=$row[csf("po_id")];
		$month_val=explode("-",$row[csf("pub_shipment_date")]);
		$month_val=$month_val[1];

		$month_arr[$month_val]=$month_val;

		$plan_month_val=explode("-",$row[csf("plan_date")]);
		$plan_month_val=$plan_month_val[1];

		//$month_arr[$plan_month_val]=$plan_month_val;

		$month_wise_qty_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$plan_month_val] +=$row[csf("date_wise_qty")];

		$date_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("plan_date")]] =$row[csf("date_wise_qty")];

		$ttl_qnty_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]+=$row[csf("date_wise_qty")];

		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['buyer_name'] =$buyer_arr[$row[csf("buyer_name")]];

		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['job_no'] = $row[csf("job_no")];

		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['unit_price'] =$row[csf("unit_price")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['po_number'] =$row[csf("po_number")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['target'] =$row[csf("terget")];

		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['floor'] =$floor_library[$line_wise_floor[$row[csf("line_id")]]];

		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['smv'] =$row[csf("smv")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['plan_qnty']=$row[csf("plan_qnty")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['po_quantity'] =$row[csf("po_quantity")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['plan_cut'] =$row[csf("plan_cut")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['pub_shipment_date'] =$row[csf("pub_shipment_date")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['start_date'] =$row[csf("start_date")];
		$line_wise_arr[$row[csf("line_id")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['end_date'] =$row[csf("end_date")];
		 
		 
	}
	//echo "<pre>";print_r($month_wise_qty_arr);die;
	$all_po_ids=implode(",", $all_po);

	$production_sql="SELECT b.line_number,a.po_break_down_id,a.item_number_id,sum(a.production_quantity) as qnty from pro_garments_production_mst a,prod_resource_mst b where a.sewing_line=b.id   and  a.status_active=1 and a.production_type=5 and a.po_break_down_id in($all_po_ids) group by  b.line_number,a.po_break_down_id,a.item_number_id ";
	$production_arr=array(); 
	foreach(sql_select($production_sql) as $p)
	{
		$val=array_unique(explode(",",$p[csf("line_number")]));
 		foreach($val as $line_key)
		{
			$production_arr[$p[csf("po_break_down_id")]][$p[csf("item_number_id")]][$line_key]+=$p[csf("qnty")];
		}

		
	}
	 


	$booking_sql="SELECT po_break_down_id,booking_no from wo_booking_dtls where status_active=1  and po_break_down_id in($all_po_ids) and booking_type=1 ";
	$booking_arr=array();
	$dup_booking_arr=array();
	foreach(sql_select($booking_sql) as $b)
	{
		if($dup_booking_arr[$b[csf("booking_no")]]=="")
		{
			if($booking_arr[$b[csf("po_break_down_id")]])
				$booking_arr[$b[csf("po_break_down_id")]].=','.$b[csf("booking_no")];
			else $booking_arr[$b[csf("po_break_down_id")]]=$b[csf("booking_no")];
			$dup_booking_arr[$b[csf("booking_no")]]=$b[csf("booking_no")];

		}
		
	}



	$resource_sql="SELECT a.line_number,b.active_machine,b.helper,b.working_hour,b.target_efficiency from prod_resource_mst a ,prod_resource_dtls_mast b where a.id=b.mst_id group by a.line_number,b.active_machine,b.helper,b.working_hour,b.target_efficiency ";
	$resource_arr=array();
	foreach(sql_select($resource_sql) as $v)
	{
		$lines=explode(",",$v[csf("line_number")]);
		foreach($lines as $l)
		{
			$resource_arr[$l]["mc"]=$v[csf("active_machine")];
			$resource_arr[$l]["hp"]=$v[csf("helper")];
			$resource_arr[$l]["wh"]=$v[csf("working_hour")];
			$resource_arr[$l]["tgt"]=$v[csf("target_efficiency")];
		}
	}
	 
	ob_start();	
	if($type==1)
	{
		?>
		<div><br>
			<fieldset>
				<table width="1700" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >
					<thead>

						<td  colspan="13" width="1060"> </td>
						<td  width="80" align="right" id="ttl_plancut_qty"></td>
						<td  width="80"  align="right" id="ttl_out_qty"></td>
						<td  width="80" align="right"  id="ttl_plan_qty"></td>
						<td colspan="5"  width="400"> </td>


					</thead>
				</table>
				<table width="1700" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >

					<thead>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Floor</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Line</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Buyer</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">PO</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Style</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Booking</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Item</th> 
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">SMV</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">M/C</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">HP</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">WH</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">EXP. Efficiency</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Order Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Plan Cut Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Output</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Planned Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">TGT/Day</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Ship Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Sewing Start</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Sewing End</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Late/Early By</th>

					</thead>
				</table>


				<div style="width:1720px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table width="1700" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
						<?
						$j=1;
						$gr_plan_cut=0;
						$gr_plan_output=0;
						$gr_plan=0;
						foreach($line_wise_arr as $line_id=>$style_data)
						{
							$line_wise_order_qty=0;
							foreach($style_data as $style_id=>$po_data)
							{

								foreach($po_data as $po_id=>$item_data)
								{
									foreach($item_data as $item_id=>$pdata)
									{
										$line_wise_order_qty+=$pdata['po_quantity'];
										if ($k%2==0)  
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"><? echo   $pdata['floor']; ?></td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"><? echo $line_name_library[$line_id]; ?> 
											</td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  ><? echo $pdata['buyer_name'];?></td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  ><? echo $pdata['po_number'];?></td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100" align="center"  ><? 
												echo $style_id; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" ><? echo $booking_arr[$po_id];?> </td>

												<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" ><? echo $garments_item[$item_id];?> </td>


												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $pdata['smv']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["mc"]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["hp"]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["wh"]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["tgt"]; ?> </td>


												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $pdata['po_quantity']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $plan_cut=$pdata['plan_cut']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $output=$production_arr[$po_id][$item_id][$line_id]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $plan_qty=$pdata['plan_qnty']; ?> </td>

												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $pdata['target']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? echo $pdata['pub_shipment_date']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? echo $pdata['start_date']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? echo $pdata['end_date']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? 
													$diff = abs(strtotime($pdata['end_date']) - strtotime($pdata['pub_shipment_date']));
													echo ($diff/ (60*60*24))
													?> </td>



												</tr>
												<?
												$gr_plan_cut+=$plan_cut ;
												$gr_plan_output+=$output;
												$gr_plan+=$plan_qty;
												$j++;
											}
										}

									}

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j++; ?>"> 
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center">				</td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  > </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  ></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100" align="center"  ></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" >  </td>

										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" >  </td>



										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>

										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"><strong>Sub Total</strong></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   <strong> <? echo $line_wise_order_qty; ?> </strong></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>



									</tr>



									<?
								}
								?>
							</table>
						</div>
					</fieldset>
					<script type="text/javascript">
						var gr_plan_cut='<? echo $gr_plan_cut; ?>';
						var gr_plan_output='<? echo $gr_plan_output; ?>';
						var gr_plan='<? echo $gr_plan; ?>';
						$("#ttl_plancut_qty").html(gr_plan_cut);
						$("#ttl_out_qty").html(gr_plan_output);
						$("#ttl_plan_qty").html(gr_plan);
					</script>
				</div>


		<?

	}

	else if($type==2)
	{
		$month_cnt=count($month_arr);

		?>
		<div><br>
			<fieldset>
			 <table width="<? echo 2100+(50*$row_count)+(150*$month_cnt);?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >
					
					<thead>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>

						<th  style="word-wrap:break-word;word-break: break-all;" id="" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" id="" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;"  id="" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<?
						$ind=0;
						for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 )
						{
		 					//$thisDate = date( 'd-M-y', $i );    
		 					?>
		 					<th  style="word-wrap:break-word;word-break: break-all;" width="50" id=""></th>
		 					<?
		 					$ind++;
		 					
						}
						$loop_end=$ind;
						 
							foreach($month_arr as $kk){

								?>
			 					<th   style="word-wrap:break-word;word-break: break-all;" width="50" id=""></th>
			 					<th  style="word-wrap:break-word;word-break: break-all;" width="50" id=""><?echo $kk;?></th>
			 					<th  style="word-wrap:break-word;word-break: break-all;" width="50" id=""></th>
			 					<?
			 				}
						 

						?>

					</thead>
				</table>

					 


				 <table width="<? echo 2100+(50*$row_count)+(150*$month_cnt);?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >
					
					<thead>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>

						<th  style="word-wrap:break-word;word-break: break-all;" id="ttl_plancut_qty" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" id="ttl_out_qty" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;"  id="ttl_plan_qty" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80"></th>
						<?
						$ind=0;
						for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 )
						{
		 					//$thisDate = date( 'd-M-y', $i );    
		 					?>
		 					<th  style="word-wrap:break-word;word-break: break-all;" width="50" id="dayTTl_<? echo $ind;?>"></th>
		 					<?
		 					$ind++;
		 					
						}
						$loop_end=$ind;
						for($i=0;$i<$month_cnt;$i++)
						{
							for($j=0;$j<3;$j++)
							{


								?>
			 					<th    style="word-wrap:break-word;word-break: break-all;" width="50" id="monthTTl_<? echo $i.'-'.$j;?>"></th>
			 					<?
			 				}
						}

						?>

					</thead>
				</table>
				<table width="<? echo 2100+(50*$row_count)+(150*$month_cnt);?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >
					
					<thead>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Floor</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Line</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Buyer</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">PO</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Style</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Booking</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Item</th> 
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">SMV</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">M/C</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">HP</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="50">WH</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">EXP. Efficiency</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Order Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Plan Cut Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Output</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Planned Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">TGT/Day</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Ship Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Sewing Start</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Sewing End</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Late/Early Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Total</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">CM DZN</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">FOB Price</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Total CM</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Total FOB</th>
						<?
						for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 )
						{
		 					//$thisDate = date( 'd-M-y', $i );    
		 					?>
		 					<th  style="word-wrap:break-word;word-break: break-all;" width="50"><?   echo date( 'd-M', $i );?></th>
		 					<?
		 					
						}

						for($i=0;$i<$month_cnt;$i++)
						{
							for($j=0;$j<3;$j++)
							{
								$head="";
								if($j==0)$head="Total";
								else if($j==1)$head="CM Val.";
								else $head="FOB Val.";

								?>
			 					<th    style="word-wrap:break-word;word-break: break-all;" width="50"><? echo $head;?></th>
			 					<?
			 				}
						}


						?>


					</thead>
				</table>


				<div style="width:<? echo 2120+(50*$row_count)+(150*$month_cnt);?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table width="<? echo 2100+(50*$row_count)+(150*$month_cnt);?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
						<?
						$j=1;
						$gr_plan_cut=0;
						$gr_plan_output=0;
						$gr_plan=0;
						foreach($line_wise_arr as $line_id=>$style_data)
						{
							$line_wise_order_qty=0;
							foreach($style_data as $style_id=>$po_data)
							{

								foreach($po_data as $po_id=>$item_data)
								{
									foreach($item_data as $item_id=>$pdata)
									{
										$line_wise_order_qty+=$pdata['po_quantity'];
										if ($k%2==0)  
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
										
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"><? echo   $pdata['floor']; ?></td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"><? echo $line_name_library[$line_id]; ?> 
											</td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  ><? echo $pdata['buyer_name'];?></td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  ><? echo $pdata['po_number'];?></td>
											<td  style="word-wrap:break-word;word-break: break-all;"  width="100" align="center"  ><? 
												echo $style_id; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" ><? echo $booking_arr[$po_id];?> </td>

												<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" ><? echo $garments_item[$item_id];?> </td>


												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $pdata['smv']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["mc"]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["hp"]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["wh"]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right"> <? echo $resource_arr[$line_id]["tgt"]; ?> </td>

												
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $pdata['po_quantity']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $plan_cut=$pdata['plan_cut']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $output=$production_arr[$po_id][$item_id][$line_id]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $plan_qty=$pdata['plan_qnty']; ?> </td>
												
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $pdata['target']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? echo $pdata['pub_shipment_date']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? echo $pdata['start_date']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? echo $pdata['end_date']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center"> <? 
													$diff = abs(strtotime($pdata['end_date']) - strtotime($pdata['pub_shipment_date']));
													echo ($diff/ (60*60*24))
													?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $ttl_qty=$ttl_qnty_arr[$line_id][$style_id][$po_id][$item_id]; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $cm= $pre_cost_arr[$pdata['job_no']] ; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $unit_price=$pdata['unit_price']; ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $ttl_qty*($cm/12); ?> </td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"> <? echo $ttl_qty*$unit_price; ?> </td>


													<?
													$index=0;
													for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 )
													{
		 												 $dates = strtoupper(date( 'd-M-y', $i ));  
		 												 $day_val=$date_wise_arr[$line_id][$style_id][$po_id][$item_id][$dates];
		 												 $day_wise_total[$index]+=  $day_val;
														?>
														<td  style="word-wrap:break-word;word-break: break-all;" width="50" align="right">
														<?  
														 echo $day_val;?></td>
														<?
														$index++;

													}
													$pp=0;
													foreach($month_arr as $vv)
													{

														for($j=0;$j<3;$j++)
														{
															if($j==0)
															{

																$values=$month_wise_qty_arr[$line_id][$style_id][$po_id][$item_id][$vv];
																$bkup=$values;
															}
															else if($j==1)$values=$bkup*($cm/12);
															else if($j==2)$values=$bkup*$unit_price;


															?>
															<td align="right"   style="word-wrap:break-word;word-break: break-all;" width="50"><? echo $values;?></td>
															<?
															$month_ttl_json[$pp][$j]+=$values;
														}
														$pp++;
													}


													?>



												</tr>
												<?
												$gr_plan_cut+=$plan_cut ;
												$gr_plan_output+=$output;
												$gr_plan+=$plan_qty;
												$j++;
											}
										}

									}

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j++; ?>"> 
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center">				</td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  > </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center"  ></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100" align="center"  ></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" >  </td>

										<td  style="word-wrap:break-word;word-break: break-all;"  width="100"  align="center" >  </td>



										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="50" align="right">  </td>

										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right"><strong>Sub Total</strong></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   <strong> <? echo $line_wise_order_qty; ?> </strong></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="right">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">   </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="80" align="center">  </td>

										<?
										for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 )
										{
		 									//$thisDate = date( 'd-M-y', $i );    
											?>
											<td  style="word-wrap:break-word;word-break: break-all;" width="50">&nbsp;</td>
											<?

										}
										for($i=0;$i<$month_cnt;$i++)
										{
											for($j=0;$j<3;$j++)
											{


												?>
												<td    style="word-wrap:break-word;word-break: break-all;" width="50"><? echo "";?></td>
												<?
											}
										}

										?>

									</tr>



									<?
								}
								$ttl_json_arr=json_encode($day_wise_total);
								$month_ttl_json=json_encode($month_ttl_json);
								?>
							</table>
						</div>
					</fieldset>
					<script type="text/javascript">
						var ttl_json_arr = JSON.parse('<? echo $ttl_json_arr ?>');
						var month_ttl_json = JSON.parse('<? echo $month_ttl_json ?>');

						 
						var gr_plan_cut='<? echo $gr_plan_cut; ?>';
						var loop_end='<? echo $loop_end; ?>';
						var month_cnt='<? echo $month_cnt; ?>';
						//alert(month_ttl_json);
						var gr_plan_output='<? echo $gr_plan_output; ?>';
						var gr_plan='<? echo $gr_plan; ?>';
						$("#ttl_plancut_qty").html(gr_plan_cut);
						$("#ttl_out_qty").html(gr_plan_output);
						$("#ttl_plan_qty").html(gr_plan);
						var i;
						for(i=0;i<loop_end;i++)
						{
							$("#dayTTl_"+i).html(ttl_json_arr[i]);
						}

						for(i=0;i<month_cnt;i++)
						{
							for(j=0;j<3;j++)
							{
 								$("#monthTTl_"+i+"-"+j).html(month_ttl_json[i][j]);
							}
						}

					</script>
				</div>


		<?

	}
	 
	foreach (glob("$user_name*.xls") as $filename) 
	{
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}



?>
      
 