<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

// if ($action=="load_drop_down_location")
// {
// 	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and company_id in($data)","id,location_name", 1, "-- Select Location --", $selected, "" );     	 
// 	exit();
// }

// if ($action=="load_drop_down_floor")
// {
// 	echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor  where status_active=1  and company_id in($data)","id,floor_name", 1, "-- Select Floor --", $selected, "" );   	 
// 	exit();
// }

 

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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_style_no_search_list_view', 'search_div', 'buyer_wise_planning_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="b.po_number";
	if($search_by==3) $search_field="b.po_number"; else $search_field="b.po_number";
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
	$sql= "SELECT b.po_number, a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.company_name in($company_id) and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
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
	$loc_cond="";	if($location_id)$loc_cond.=" and c.location_id in($location_id)";
	$floor_cond="";	if($floor_id)$floor_cond.="  floor_name in($floor_id)";
	
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$line_wise_floor=return_library_array( "select id, floor_name from lib_sewing_line",'id','floor_name');
	$actual_resource=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$all_line=return_library_array( "select id, id from lib_sewing_line where $floor_cond ",'id','id');
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
	 
	if($db_type==0) $grp_concat="group_concat(c.line_id) AS line_id,";
	else if($db_type==2) $grp_concat="listagg(cast(c.line_id as varchar2(4000)),',') within group (order by c.line_id) AS line_id,";
	$sql_data="SELECT  c.notes as remarks, a.buyer_name,  $grp_concat e.plan_qnty,   b.id as po_id,b.pub_shipment_date,b.po_quantity, min(c.start_date) as start_date ,max(c.end_date) as end_date, b.po_number, a.style_ref_no as style,e.item_number_id 
	from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e  where  a.job_no=b.job_no_mst and a.job_no=e.job_no and b.id=c.po_break_down_id and c.plan_id=d.plan_id and d.plan_id=e.plan_id  and c.company_id in($company_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $loc_cond $line_conds  $buyer_id_cond  $date_cond $style_cond  group by  c.notes, a.buyer_name,  e.plan_qnty,  b.id ,b.pub_shipment_date,b.po_quantity,  b.po_number, a.style_ref_no ,e.item_number_id  order by a.buyer_name asc";
	
	$data_result=sql_select($sql_data);
	$buyer_wise_arr=array();
	$rowcount=count($data_result);
	 
	$all_po=array();
	 
	foreach( $data_result as $row)
	{
		  
		$all_po[$row[csf("po_id")]]=$row[csf("po_id")];		  

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['buyer_name'] =$buyer_arr[$row[csf("buyer_name")]];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['po_number'] =$row[csf("po_number")];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['style'] =$row[csf("style")];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['remarks'] =$row[csf("remarks")];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['item'] =$garments_item[$row[csf("item_number_id")]];

		
		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['po_quantity'] =$row[csf("po_quantity")];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['plan_qnty'] +=$row[csf("plan_qnty")];
		
		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['pub_shipment_date'] =$row[csf("pub_shipment_date")];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['start_date'] =$row[csf("start_date")];

		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['end_date'] =$row[csf("end_date")];
		if($buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['line_id']=="")
		$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['line_id'] =$row[csf("line_id")];
		else 
			$buyer_wise_arr[$row[csf("buyer_name")]][$row[csf("style")]][$row[csf("po_id")]][$row[csf("item_number_id")]]['line_id'] .=','.$row[csf("line_id")];
		 
		 
	}
	$all_po_ids=implode(",",$all_po);
	if(!$all_po_ids)$all_po_ids=0;
	$tna_arr=array();
	$tna_sql="SELECT  po_number_id,  task_number, task_finish_date as finish_date from tna_process_mst where  po_number_id in($all_po_ids) and task_number in(88,101)  ";
	foreach(sql_select($tna_sql) as $v)
	{
		if($v[csf("task_number")]==88)
		$tna_arr[$v[csf("po_number_id")]]["finish"]=$v[csf("finish_date")];
		if($v[csf("task_number")]==101)
		$tna_arr[$v[csf("po_number_id")]]["inspection"]=$v[csf("finish_date")];
	}
	 //echo "<pre>";print_r($tna_arr);die;
	ob_start();	
	if($type==1)
	{
		?>
		<div><br>
			<fieldset>
				
				<table width="1610" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >

					<thead>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Buyer</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">PO</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Style</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Item</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Order Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Plan Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Planning Line</th> 
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">No of Lines</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">CDD Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Sewing Start Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Sewing End Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Finishing Complete Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Inspection Offer Date</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="115">Remarks</th>
						 

					</thead>
				</table>


				<div style="width:1630px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table width="1610" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
						<?
						$j=1;
						 
						
						$gr_wise_po_qty=0;
						$gr_wise_plan_qty=0;
						foreach($buyer_wise_arr as $buyer_id=>$style_data)
						{
							$buyer_wise_po_qty=0;
							$buyer_wise_plan_qty=0;
							//foreach($line_data as $line_id=>$style_data)
							//{

								foreach($style_data as $style_id=>$po_data)
								{

									foreach($po_data as $po_id=>$item_data)
									{
										foreach($item_data as $item_id=>$pdata)
										{
											$buyer_wise_po_qty+=$pdata['po_quantity'];
											$buyer_wise_plan_qty+=$pdata['plan_qnty'];
											 
											if ($k%2==0)  
												$bgcolor="#E9F3FF";
											else
												$bgcolor="#FFFFFF";
											$lines=array_unique(explode(",", $pdata['line_id']));
											$line_name="";
											$counts=0;
											if($lines)
											{


												foreach($lines as $v)
												{
													if(isset($line_name_library[$v]))
													{



													 
														if($line_name=="")
														{

															$line_name.=$line_name_library[$v];
														}
														else
														{
															$line_name.=','.$line_name_library[$v];
														}
													   $counts++;
													}

												}
											}

											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"><? echo   $pdata['buyer_name']; ?></td>

												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"><? echo   $pdata['po_number']; ?></td>
												 
												</td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['style'];?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['item'];?></td>

												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['po_quantity'];?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['plan_qnty'];?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $line_name;?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $counts;?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['pub_shipment_date'];?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['start_date'];?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['end_date'];?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $tna_arr[$po_id]["finish"] ;?></td>
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $tna_arr[$po_id]["inspection"] ;?></td>
												 
												<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"  ><? echo $pdata['remarks'];?></td>
												 
												 



													</tr>
													<?
													$gr_wise_po_qty+=$pdata['po_quantity'];
													$gr_wise_plan_qty+=$pdata['plan_qnty'];													 
													$j++;
												}
											}

										}
									//}

									?>
									<tr bgcolor="gray" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j++; ?>"> 
										 
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"><strong><?echo $buyer_arr[$buyer_id]; ?></strong></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"><? echo $buyer_wise_po_qty ?></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"><? echo $buyer_wise_plan_qty ?></td>
										 
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										 



									</tr>



									<?
								}
								?>
								<tr bgcolor="gray" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j++; ?>"> 
										 
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"><strong>Grand Total</strong></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"><? echo $gr_wise_po_qty ?></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"><? echo $gr_wise_plan_qty ?></td>
										 
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										<td  style="word-wrap:break-word;word-break: break-all;"  width="115"  align="center"></td>
										 



									</tr>
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
      
 