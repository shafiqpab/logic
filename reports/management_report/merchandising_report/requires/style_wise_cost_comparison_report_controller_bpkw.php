<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../../../includes/common.php');
	require_once('../../../../includes/class3/class.conditions.php');
	require_once('../../../../includes/class3/class.reports.php');
	require_once('../../../../includes/class3/class.yarns.php');
	require_once('../../../../includes/class3/class.conversions.php');
	require_once('../../../../includes/class3/class.trims.php');
	require_once('../../../../includes/class3/class.emblishments.php');
	require_once('../../../../includes/class3/class.commisions.php');
	require_once('../../../../includes/class3/class.commercials.php');
	require_once('../../../../includes/class3/class.others.php');
	require_once('../../../../includes/class3/class.washes.php');
	require_once('../../../../includes/class3/class.fabrics.php');

	$_SESSION['page_permission']=$permission;
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

		$user_name = $_SESSION['logic_erp']["user_id"];

		$data=$_REQUEST['data'];
		$action=$_REQUEST['action'];

		if ($action=="load_drop_down_buyer"){
			echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
			exit();
		}
		if ($action=="order_popup"){
			echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
			extract($_REQUEST);
			?>
			<script>
				function set_checkvalue(){
					if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
					else document.getElementById('chk_job_wo_po').value=0;
				}
				function js_set_value( str ){
					var data=str.split("_");
					document.getElementById('selected_job').value=data[0];
					document.getElementById('selected_year').value=data[1];
					document.getElementById('selected_company').value=data[2];

					parent.emailwindow.hide();
				}
			</script>
		</head>
		<body>
			<div align="center" style="width:100%;" >
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="1200" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
						<tr>
							<td align="center" width="100%">
								<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
									<thead>
										<th width="150" colspan="4"> </th>
										<th>
											<?
											echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
											?>
										</th>
										<th width="150" colspan="5"> </th>
									</thead>
									<thead>                	 
										<th width="120">Company Name</th>
										<th width="120">Buyer Name</th>
										<th width="80">Job No</th>
										<th width="100">Style Ref </th>
										<th width="100">Internal Ref</th>
										<th width="100">File No</th>
										<th width="120">Order No</th>
										<th>Shipment Status</th>
                                         <th width="100">Date Category</th>
										<th align="center"  width="200" id="search_by_td_up">Enter Ship Date</th>
										<th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
									</thead>
									<tr>
										<td> 
											<input type="hidden" id="selected_job">
											<input type="hidden" id="selected_year">
											<input type="hidden" id="selected_company">
											<input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
											<? 
											echo create_drop_down( "cbo_company_mst", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'style_wise_cost_comparison_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
											?>
										</td>
										<td id="buyer_td">
											<? 
											echo create_drop_down( "cbo_buyer_name", 120, $blank_array,'', 1, "-- Select Buyer --" );
											?>	</td>
											<td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
											<td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
											<td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
											<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
											<td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
											<td>
												<?
												echo create_drop_down( "shipping_status", 120, $shipment_status,"", 0, "-- Select --", 3, "",0,'','','','','' );
												$search_by = array(1 => 'Country Ship Date', 2 => 'Ship Date', 3 => 'Ex Factory');
 												$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../../')";
												?>
											</td>
                                             <td>
                                            	<?
                                            		echo create_drop_down("cbo_search_by", 100, $search_by, "", 0, "--Select--", "", $dd, 0);
                                            	?>
                                            </td>
											<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
												<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
											</td> 
											<td align="center">
												<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('shipping_status').value+'_'+document.getElementById('cbo_search_by').value, 'create_po_search_list_view', 'search_div', 'style_wise_cost_comparison_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td  align="center" height="40" valign="middle">
										<? 
										echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
										?>
										<? echo load_month_buttons();  ?>
									</td>
								</tr>
								<tr>
									<td align="center" valign="top" id="search_div"> 

									</td>
								</tr>
							</table>    

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
		$data=explode('_',$data);
		$cbo_search_by = str_replace("'","",$data[14]);
		if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
		if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		//if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		//if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	if(str_replace("'","",$data[3])!="" && str_replace("'","",$data[4])!="")
	{
	 if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$data[3]),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$data[4]),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$data[3]),"","",1);
			$end_date=change_date_format(str_replace("'","",$data[4]),"","",1);
		}
		
		
		$date_cond="";
		if($cbo_search_by==1)
		{
			$date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			//country_ship_date
		}
		else if($cbo_search_by==2)
		{
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else if($cbo_search_by==3)
		{
			$date_cond=" and e.ex_factory_date between '$start_date' and '$end_date'";
		}
	}
	
	$order_cond="";
	$job_cond=""; 
	$style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		  if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond=""; 
		}

		if($data[8]==4 || $data[8]==0)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond=""; 

		}

		if($data[8]==2)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond=""; 

		}

		if($data[8]==3)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond=""; 

		}

		$internal_ref = str_replace("'","",$data[11]);
		$file_no = str_replace("'","",$data[12]);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
		$shiping_status=0;
		if($data[13]!=0){
			$shiping_status=" and b.shiping_status=$data[13]";
		}else{
			$shiping_status=" ";
		}

		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
		if ($data[2]==0)
		{
			/*if($db_type==0)
			{
				$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." order by a.job_no";
			}
			if($db_type==2)
			{
				$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond order by a.job_no";
			}
		//echo $sql;
			echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "40,30,120,100,100,70,90,70,60,60,70,70,50","1020","320",0, $sql , "js_set_value", "job_no_prefix_num,year,company_name", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,0,1,0,1,3,0,0,0,0');*/
	if($cbo_search_by==1 || $cbo_search_by==2)
		{		
				if($db_type==0)
				{
				$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c, production_logicsoft d where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and c.po_break_down_id=b.id and a.job_no=d.jobNo and c.job_no_mst=d.jobNo and b.job_no_mst=d.jobNo and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $company $buyer $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond  ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no";
				}
			 if($db_type==2)
				{
				$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,production_logicsoft d where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and c.po_break_down_id=b.id and a.job_no=d.jobNo and c.job_no_mst=d.jobNo  and b.job_no_mst=d.jobNo and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond  group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no";
				}
				
				
		}
		else
		{
			if($db_type==0)
				{
				$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c, production_logicsoft d,pro_ex_factory_mst e where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and c.po_break_down_id=b.id and a.job_no=d.jobNo and c.job_no_mst=d.jobNo and b.job_no_mst=d.jobNo  and c.po_break_down_id=e.po_break_down_id and b.id=e.po_break_down_id   and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $company $buyer $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond  ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no";
				}
			 if($db_type==2)
				{
				$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,production_logicsoft d,pro_ex_factory_mst e where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and c.po_break_down_id=b.id and a.job_no=d.jobNo and c.job_no_mst=d.jobNo  and b.job_no_mst=d.jobNo and c.po_break_down_id=e.po_break_down_id and b.id=e.po_break_down_id  and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond  group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no";
				}	
		}
		//echo $sql;
		 echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "40,30,120,100,100,70,90,70,80,110,70,70,50","1120","320",0, $sql , "js_set_value", "job_no_prefix_num,year,company_name", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,0,1,0,1,3,0,0,0,0');
	
 }
	else
	{
			/*
			$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
			if($db_type==0)
			{
				$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer $job_cond $style_cond  order by a.job_no";
			}
			if($db_type==2)
			{
				$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no";
			}
			echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
		*/
		
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a,production_logicsoft d where  a.job_no=d.jobNo and a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer $job_cond $style_cond  order by a.job_no";
		}
		if($db_type==2)
		{
		 $sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a ,production_logicsoft d where  a.job_no=d.jobNo and a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	
		}
	} 
	if($action=="report_generate")
	{ 
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($company_name==0){
			echo "Select Company";
			die;
		}
		if($job_no==''){
			echo "Select Job";
			die;
		}
		if($cbo_year==0){
			echo "Select Year";
			die;
		}

		if($db_type==0){ 
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		if($db_type==2) {
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}

		
		/*$sql="select a.job_no_prefix_num, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,b.id, b.po_number,b.grouping,b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.item_number_id,c.order_quantity , c.order_rate,c.order_total , c.plan_cut_qnty,c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";*/
		 $sql="select a.job_no_prefix_num, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,b.id, b.po_number,b.grouping,b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.item_number_id,c.order_quantity , c.order_rate,c.order_total , c.plan_cut_qnty,c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,production_logicsoft d where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  a.job_no=d.jobNo  and c.job_no_mst=d.jobNo and b.job_no_mst=d.jobNo  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
		 
		$jobNumber="";$po_ids="";
		$buyerName="";
		$styleRefno="";
		$uom="";
		$totalSetQnty="";
		$currencyId="";
		$quotationId="";
		$poNumberArr=array();
		$poIdArr=array();
		$poQtyArr=array();
		$poPcutQtyArr=array();
		$poValueArr=array();
		$ShipDateArr=array();
		$gmtsItemArr=array();
		$shipingStatus="";
		$result=sql_select($sql);
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotationId=$row[csf('quotation_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$garments_item [$row[csf('item_number_id')]];
			$shipingStatus=$row[csf('shiping_status')];
			$po_ids.=$row[csf('id')].',';
		}
		if($jobNumber=="") 
		{ 
			echo "<div style='width:1000px;' align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0){
			$po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; 
		}else{
			$po_cond_for_in="";
		}
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty 
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessValue=$shortExcessQty*($unitPrice);
	//$quotationId=1;
		if($quotationId){
			$quaOfferQnty=0;
			$quaConfirmPrice=0;
			$quaConfirmPriceDzn=0;
			$quaPriceWithCommnPcs=0;
			$quaCostingPer=0;
			$quaCostingPerQty=0;
			$sqlQua="select a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQua=sql_select($sqlQua);
			foreach($dataQua as $rowQua){
				$quaOfferQnty=$rowQua[csf('offer_qnty')];
				$quaConfirmPrice=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer=$rowQua[csf('costing_per')];
				$quaCostingPerQty=0;
				if($quaCostingPer==1){
					$quaCostingPerQty=12;
				}
				if($quaCostingPer==2){
					$quaCostingPerQty=1;
				}
				if($quaCostingPer==3){
					$quaCostingPerQty=24;
				}
				if($quaCostingPer==4){
					$quaCostingPerQty=36;
				}
				if($quaCostingPer==5){
					$quaCostingPerQty=48;
				}
			}
		}

		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}

		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);

		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	// Yarn ============================
		$totYarn=0;
		$YarnData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	//print_r($yarn_data_array);
		$sql_yarn="select count_id,copm_one_id,percent_one,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where     f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id,copm_one_id,percent_one,type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row){
		//$yarnrate=$yarn_row[csf("rate")];
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		if($quotationId){
			$sql_yarn_pri="select f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,type_id   from wo_pri_quo_fab_yarn_cost_dtls f where     f.quotation_id =".$quotationId." and f.is_deleted=0 and f.status_active=1  order by f.id";
			$data_arr_yarn_pri=sql_select($sql_yarn_pri);
			foreach($data_arr_yarn_pri as $yarn_row_pri){
				$yarnrate=$yarn_row_pri[csf("rate")];
		//$consQnty=($sql_yarn_pri[csf("cons_qnty")]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$consQnty=($yarn_row_pri[csf("cons_qnty")]/$quaCostingPerQty)*($quaOfferQnty);
		//echo "(".$sql_yarn_pri[csf("cons_qnty")]."/".$quaCostingPerQty.")*(".$quaOfferQnty.")";
				$amount=$consQnty*$yarnrate;
				$index="'".$yarn_row_pri[csf("count_id")]."_".$yarn_row_pri[csf("copm_one_id")]."_".$yarn_row_pri[csf("percent_one")]."_".$yarn_row_pri[csf("type_id")]."'";

				$YarnData[$index]['mkt']['qty']+=$consQnty;
				$YarnData[$index]['mkt']['amount']+=$amount;
			}
		}
	 //print_r($YarnData);
		$YarnIssue=array();
//	 $sql="select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st,  b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=2 and a.entry_form=3 and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and  b.status_active=1";

		/*$sql="select x.*,(x.quantity*x.cons_rate) cons_amount from (select  a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,b.lot,(select d.cons_rate from inv_transaction d where a.trans_id=d.id)cons_rate from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=2 and a.entry_form=3 and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and  b.status_active=1)x";*/
		 $sql="select x.*,(x.quantity*x.cons_rate) cons_amount from ( select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id, a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type, c.cons_rate,d.issue_basis,d.booking_no,b.lot from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d,wo_booking_mst e where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and d.booking_no=e.booking_no and a.trans_type in(2) and a.entry_form in(3) and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and e.booking_type in(1,4) )x";
		$data_Yac=sql_select($sql);
		foreach($data_Yac as $row){
			$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
			$YarnIssue[$index]['qty']+=$row[csf("quantity")];
			$YarnIssue[$index]['amount']+=$row[csf("cons_amount")];
			$YarnIssue_avg_rate[$row[csf("lot")]]['avg_rate']+=$row[csf("cons_amount")]/$row[csf("quantity")];
			$yarn_lots.=$row[csf("lot")].',';
		}
		$yarn_lots=rtrim($yarn_lots,',');
		$yarn_lots=array_unique(explode(",",$yarn_lots));
		$lot_tmp='';
		foreach($yarn_lots as $lot)
		{
			if($lot_tmp=='') $lot_tmp="'".$lot."'";else $lot_tmp.=","."'".$lot."'";
		}
		
		//echo $lot_tmp;
		$grey_fab_array=array();  //Knitting Cost actual 
		$sql_knit_prod="select c.yarn_lot,
		 sum(CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty
		  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(13)  and b.trans_type in(1) and c.yarn_lot in(".$lot_tmp.") and b.po_breakdown_id in(".implode(",",$poIdArr).") group by c.yarn_lot";
		$result_knit=sql_select( $sql_knit_prod );
		$knit_grey_qty=$knit_grey_amt=0;
		foreach($result_knit as $row){
			$avg_rate=$YarnIssue_avg_rate[$row[csf("yarn_lot")]]['avg_rate']/$g_exchange_rate;
			//$grey_fab_array[$row[csf("yarn_lot")]]['knit_grey_qty']+=$row[csf("grey_qnty")];
			//$grey_fab_array[$row[csf("yarn_lot")]]['knit_grey_amt']+=$row[csf("grey_qnty")]*$avg_rate;
			//echo $row[csf("grey_qnty")].'='.$avg_rate;
			$knit_grey_qty+=$row[csf("grey_qnty")];
			$knit_grey_amt+=$row[csf("grey_qnty")]*$avg_rate;
		}
		//echo $lot_tmp;
		//echo $knit_grey_amt; // and a.yarn_lot in(".$lot_tmp.") 
		$grey_fab_trans_array=array(); 
		$sql_grey_trans="select c.from_order_id,
		 sum(CASE WHEN b.entry_form in(83,13) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(83,13) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(83,13) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by c.from_order_id";//
		$result_grey_trans=sql_select( $sql_grey_trans );
		$grey_fab_trans_qty_acl=0;$from_order_id='';
		foreach ($result_grey_trans as $row)
		{
			//$grey_fab_trans_array['gtt'][$row[csf('yarn_lot')]]+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
		}
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
		
		$subconOutBillData="select b.order_id, 
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
			//$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')]/$g_exchange_rate;
		}
		//echo $knit_grey_amt.'='.$tot_knit_charge.'='.$knit_grey_qty;
		//$grey_fab_cost=$knit_grey_amt+$tot_knit_charge;
		//$tot_grey_fab_cost_acl=($grey_fab_cost/$knit_grey_qty)*$grey_fab_trans_qty_acl;
		
		
		// Transfer Fin Actual
				$fin_fab_trans_array=array(); 
				 $sql_fin_trans="select b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty,c.from_order_id
				  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(15) and a.item_category=2 and a.transaction_type in(5,6) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by b.trans_type, b.po_breakdown_id,c.from_order_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				$fin_from_order_id='';
				foreach ($result_fin_trans as $row)
				{
					$fin_from_order_id.=$row[csf('from_order_id')].',';
					$fin_fab_trans_array[$row[csf('trans_type')]]+=$row[csf('qnty')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				//echo $fin_from_order_id.'AA';
				unset($result_fin_trans);
				$fin_from_order_id=rtrim($fin_from_order_id,',');
				$fin_from_order_ids=array_unique(explode(",",$fin_from_order_id));
				
				$fin_trans_in_qty=$fin_fab_trans_array[5];
				$fin_trans_out_qty=$fin_fab_trans_array[6];
				$tot_fin_fab_transfer_qty=$fin_trans_in_qty-$fin_trans_out_qty;
				if($from_order_id!="") //Grey Transfer
				{
					$condition1= new condition();
					$condition1->po_id("in($from_order_id)");
					$condition1->init();
					$conversion1= new conversion($condition1);
					//echo $conversion->getQuery(); die;
					$conversion_costing_arr_process=$conversion1->getAmountArray_by_orderAndProcess();
					$conversion1= new conversion($condition1);
				 	$conversion_costing_arr_process_qty=$conversion1->getQtyArray_by_orderAndProcess();
					
					 $knit_cost=$knit_qty=0;
					 foreach($from_order_ids as $po_id)
					 {
						$knit_cost+=$conversion_costing_arr_process[$po_id][1];
						 $knit_qty+=$conversion_costing_arr_process_qty[$po_id][1];
					 }
					 
				}
				if($fin_from_order_id!="") //Finish Transfer
				{
					$condition2= new condition();
					$condition2->po_id("in($fin_from_order_id)");
					$condition2->init();
					$conversion2= new conversion($condition2);
					//echo $conversion2->getQuery(); die;
					$fin_conversion_costing_arr_process=$conversion2->getAmountArray_by_orderAndProcess();
					//print_r($fin_conversion_costing_arr_process);
					$conversion2= new conversion($condition2);
				 	$fin_conversion_costing_arr_process_qty=$conversion2->getQtyArray_by_orderAndProcess();
					 foreach($fin_from_order_ids as $po_id)
					 {
						 $tot_dye_finish_cost_pre=0;$tot_dye_finish_cost_pre_qty=0;
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
							{
								$tot_dye_finish_cost_pre+=$fin_conversion_costing_arr_process[$po_id][$process_id];
								
								$tot_dye_finish_cost_pre_qty+=$fin_conversion_costing_arr_process_qty[$po_id][$process_id];
							}
						}
						
					 }
				}
				 	$knit_charge=$knit_cost/$knit_qty;
					//echo $knit_cost.'='.$knit_qty;
					$dye_fin_avg_rate=$tot_dye_finish_cost_pre/$tot_dye_finish_cost_pre_qty;	
					$tot_fin_fab_transfer_cost=$tot_fin_fab_transfer_qty*$dye_fin_avg_rate;
					$tot_grey_fab_cost_acl=$grey_fab_trans_qty_acl*$knit_charge;
					//echo $dye_fin_avg_rate.'f';
			
		$YarnIssueReturn=array();
		  $sql="select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st,  b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=4 and a.entry_form=9 and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and  b.status_active=1";
		$data_Yac=sql_select($sql);
		foreach($data_Yac as $row){
			$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
			$YarnIssueReturn[$index]['qty']+=$row[csf("quantity")];
			$YarnIssueReturn[$index]['amount']+=$row[csf("cons_amount")];
		}
		foreach($YarnIssue as $ind=>$value){
			$YarnData[$ind]['acl']['qty']+=$value['qty']-$YarnIssueReturn[$ind]['qty'];
			$YarnData[$ind]['acl']['amount']+=($value['amount']-$YarnIssueReturn[$ind]['amount'])/$g_exchange_rate;
			//echo $value['amount'].'-'.$YarnIssueReturn[$ind]['amount'].'/'.$g_exchange_rate.'<br>';
		}
		
		 $sql_conv_yarn="select b.id as po_id,c.job_no, d.composition  from   wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d  where  b.job_no_mst=c.job_no and c.job_no=d.job_no  and c.fabric_description=d.id  and b.status_active=1 and c.cons_process in(30) and  c.job_no ='".$jobNumber."' and b.is_deleted=0 and b.id in(".implode(",",$poIdArr).") and c.amount>0 ";
		$result_conv_yarn=sql_select($sql_conv_yarn);
		$conv_yarn_arr=array();
		foreach($result_conv_yarn as $row){
			$index="".$row[csf("composition")]."";
			$conv_yarn_arr[$index]['preCost']['amount']+= $conversion_costing_arr_process[$row[csf("po_id")]][30];
			$conv_yarn_arr[$index]['preCost']['qty']+= $conversion_costing_arr_process_qty[$row[csf("po_id")]][30];
			//$conv_yarn_arr[$index]['amount']+=$row[csf("cons_amount")];
		}
		if($quotationId){
			 $pq_conv_yarn_data="select a.composition,b.quotation_id,b.cost_head,b.charge_unit,
				(CASE WHEN b.cons_type=30 THEN b.amount END) AS yarn_dyeing_cost,
				(CASE WHEN b.cons_type=30 THEN  b.req_qnty END) AS req_qnty
				from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.id=b.cost_head and b.quotation_id =".$quotationId." and b.cons_type=30 and b.status_active=1  and b.is_deleted=0";
				$result_price_conv_yarn=sql_select($pq_conv_yarn_data);
				foreach($result_price_conv_yarn as $p_row)
				{
					$pri_yarnrate=$p_row[csf("charge_unit")];
					$index="".$p_row[csf("composition")]."";
					$mktcons=($p_row[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$amount=$mktcons*$pri_yarnrate;
					$conv_yarn_arr[$index]['mkt']['amount']+=$amount;	
					$conv_yarn_arr[$index]['mkt']['qty']+=$mktcons;	
				}
			}



	//print_r($YarnData);
// Yarn End============================
// Fabric Purch ============================
		$totPrecons=0;
		$totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		$fabPurArr=array();
	//print_r($fabPur);
		$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){

			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=array_sum($fabPur['knit']['grey'][$fabPur_row[csf('id')]]);
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['knit']['qty']+=$Precons;
				$fabPurArr['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=array_sum($fabPur['woven']['grey'][$fabPur_row[csf('id')]]);
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['woven']['qty']+=$Precons;
				$fabPurArr['pre']['woven']['amount']+=$Preamt;
			}
		//$totPrecons+=$Precons;
		//$totPreAmt+=$Preamt;
		}


		if($quotationId){
			$totMktcons=0;
			$totMktAmt=0;
			$sql = "select id, item_number_id, body_part_id, fab_nature_id, color_type_id, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='".$quotationId."' and fabric_source=2";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
			//$mktcons=($fabPur_row[csf('avg_cons')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($fabPur_row[csf('avg_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$fabPur_row[csf('rate')];
				if($fabPur_row[csf('fab_nature_id')]==2){
					$fabPurArr['mkt']['knit']['qty']+=$mktcons;
					$fabPurArr['mkt']['knit']['amount']+=$mktamt;
				}
				if($fabPur_row[csf('fab_nature_id')]==3){
					$fabPurArr['mkt']['woven']['qty']+=$mktcons;
					$fabPurArr['mkt']['woven']['amount']+=$mktamt;
				}
			}
		}

		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
				}
			}
		}

	// Fabric Purch End ============================
	// Fabric Kniting  ==============================
		//$knitData=array();
		$conversion= new conversion($condition);
		//$knitQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$knitQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$conversion= new conversion($condition);
		$knitAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
		//print_r($knitQtyArr);
	//$knitDesArr=array();
		 $sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process,c.id as po_id from wo_po_break_down c, wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no  and a.job_no='".$jobNumber."' and b.cons_process=1 and a.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$index="'".$row_knit[csf('body_part_id')]."_".$row_knit[csf('color_type_id')]."_".$row_knit[csf('fabric_description')]."_".$row_knit[csf('gsm_weight')]."'";
			if($row_knit[csf('cons_process')]==1){
				//echo $knitQtyArr[$row_knit[csf('po_id')]][$row_knit[csf('cons_process')]].'=';
				$knitData[$index]['pre']['qty']=$knitQtyArr[$jobNumber][$row_knit[csf('id')]][$row_knit[csf('cons_process')]];
				$knitData[$index]['pre']['amount']=$knitAmtArr[$jobNumber][$row_knit[csf('id')]][$row_knit[csf('cons_process')]];
			}

		}
		if($quotationId){
			$sql = "select a.id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id='".$quotationId."' and b.cons_type=1";
			$data_knit=sql_select($sql);
			foreach($data_knit as $row_knit){
				$index="'".$row_knit[csf('body_part_id')]."_".$row_knit[csf('color_type_id')]."_".$row_knit[csf('fabric_description')]."_".$row_knit[csf('gsm_weight')]."'";
				if($row_knit[csf('cons_type')]==1){
				//$mktcons=($row_knit[csf('req_qnty')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
					$mktcons=($row_knit[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$row_knit[csf('charge_unit')];
					$knitData[$index]['mkt']['qty']+=$mktcons;
					$knitData[$index]['mkt']['amount']+=$mktamt;
				}
			}
		}

		$sql = "select  b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$index=$row_knit[csf('product_name_details')];
			$knitData[$index]['acl']['qty']+=$row_knit[csf('receive_qty')];
			$knitData[$index]['acl']['amount']+=$row_knit[csf('amount')]/$g_exchange_rate;
		}

		$sql = "select  b.delivery_qty,b.rate,b.amount,c.product_name_details from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$index=$row_knit[csf('product_name_details')];
			$knitData[$index]['acl']['qty']+=$row_knit[csf('delivery_qty')];
			$knitData[$index]['acl']['amount']+=$row_knit[csf('amount')]/$g_exchange_rate;
		}
	// Fabric Kniting  End============================
	// Fabric Dye Finish  ==============================
		$finishData=array();
		$conversion= new conversion($condition);
		$finishQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$conversion= new conversion($condition);
		$finishAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
	//$finishDesArr=array();
		$sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  a.job_no='".$jobNumber."' and b.cons_process not in(1,2,30,35)";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$index="'".$row_finish[csf('body_part_id')]."_".$row_finish[csf('color_type_id')]."_".$row_finish[csf('fabric_description')]."_".$row_finish[csf('gsm_weight')]."'";
			$finishData[$index]['pre']['qty']=$finishQtyArr[$jobNumber][$row_finish[csf('id')]][$row_finish[csf('cons_process')]];
			$finishData[$index]['pre']['amount']=$finishAmtArr[$jobNumber][$row_finish[csf('id')]][$row_finish[csf('cons_process')]];
		}
		if($quotationId){
			$sql = "select a.id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id='".$quotationId."' and b.cons_type not in(1,2,30,35)";
			$data_finish=sql_select($sql);
			foreach($data_finish as $row_finish){
				$index="'".$row_finish[csf('body_part_id')]."_".$row_finish[csf('color_type_id')]."_".$row_finish[csf('fabric_description')]."_".$row_finish[csf('gsm_weight')]."'";
				//$mktcons=($row_finish[csf('req_qnty')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row_finish[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row_finish[csf('charge_unit')];
				$finishData[$index]['mkt']['qty']+=$mktcons;
				$finishData[$index]['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select  b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4 and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$index=$row_finish[csf('product_name_details')];
			$finishData[$index]['acl']['qty']+=$row_finish[csf('receive_qty')];
			$finishData[$index]['acl']['amount']+=$row_finish[csf('amount')]/$g_exchange_rate;
		}
	// Fabric Dye Finish  End============================
	// Fabric AOP  ==============================
		$aopData=array();
		$conversion= new conversion($condition);
		$aopQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$conversion= new conversion($condition);
		$aopAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
	//$aopDesArr=array();
	$sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  a.job_no='".$jobNumber."' and b.cons_process=35";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('fabric_description')]."_".$row_aop[csf('gsm_weight')]."'";
			if($row_aop[csf('cons_process')]==35){
				$finishData[$index]['pre']['qty']+=$aopQtyArr[$jobNumber][$row_aop[csf('id')]][$row_aop[csf('cons_process')]];
				$finishData[$index]['pre']['amount']+=$aopAmtArr[$jobNumber][$row_aop[csf('id')]][$row_aop[csf('cons_process')]];
			}

		}

		if($quotationId){
			$sql = "select a.id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id='".$quotationId."' and b.cons_type=35";
			$data_aop=sql_select($sql);
			foreach($data_aop as $row_aop){
				$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('fabric_description')]."_".$row_aop[csf('gsm_weight')]."'";
				if($row_aop[csf('cons_type')]==35){
				//$mktcons=($row_aop[csf('req_qnty')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
					$mktcons=($row_aop[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$row_aop[csf('charge_unit')];
					$finishData[$index]['mkt']['qty']+=$mktcons;
					$finishData[$index]['mkt']['amount']+=$mktamt;
				}
			}
		}

		/*$sql = "select  b.batch_issue_qty, b.rate, b.amount, b.currency_id, b.exchange_rate,b.process_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.process_id=35 and b.job_no ='".$jobNumber."' and  b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			if($row_aop[csf('process_id')]==35){
				$exchange_rate=$row_aop[csf('batch_issue_qty')];
				if(!$exchange_rate){
					$exchange_rate=1;
				}
				$aopData['acl']['qty']+=$row_aop[csf('batch_issue_qty')];
				$aopData['acl']['amount']+=$row_aop[csf('amount')]/$exchange_rate;
			}
		}*/
		
		$sql = "select  b.body_part_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			//$index=$row_aop[csf('product_name_details')];
			//$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('febric_description_id')]."'";
			$index="'".$row_aop[csf('body_part_id')]."'";
			$finishData[$index]['acl']['qty']+=$row_aop[csf('receive_qty')];
			$finishData[$index]['acl']['amount']+=$row_aop[csf('amount')]/$g_exchange_rate;
		}

	// Fabric AOP  End============================
	
					
				
	// Trim Cost ============================

	//$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" ); 
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
	//print_r($trim_groupArr);
		$trimData=array();
	//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

		$trim= new trims($condition);
	//$trimAmtArr=$trim->getAmountArray_by_jobAndItemid();
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$jobNumber][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$jobNumber][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		if($quotationId){
			$sql = "select id,  trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from wo_pri_quo_trim_cost_dtls  where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$trimData[$row[csf('trim_group')]]['mkt']['qty']+=$mktcons;
				$trimData[$row[csf('trim_group')]]['mkt']['amount']+=$mktamt;
				$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
			}
		}
		$trimsRecArr=array();
	//echo "select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,a.rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in(".implode(",",$poIdArr).") ";
		$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,a.rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in(".implode(",",$poIdArr).") ");
		foreach($receive_qty_data as $row){
			$trimsRecArr[$row[csf('item_group_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			$trimsRecArr[$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
			$trimsRecArr[$row[csf('item_group_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$row[csf('rate')];
		//echo $row[csf('quantity')]."*".$trim_groupArr[$row[csf('item_group_id')]]['con_factor']."*".$row[csf('rate')]."<br/>";
			$trimsRecArr[$row[csf('item_group_id')]]['cons_uom']=$trim_groupArr[$row[csf('item_group_id')]]['cons_uom'];
		}
	//print_r($trimsRecArr);
		$trimsRecReArr=array();
		$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,d.quantity as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.po_breakdown_id in(".implode(",",$poIdArr).")");
		foreach($receive_rtn_qty_data as $row){
			$trimsRecReArr[$row[csf('item_group_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			$trimsRecReArr[$row[csf('item_group_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$trimsRecArr[$row[csf('item_group_id')]]['rate'];
		}
		foreach($trimsRecArr as $ind=>$value){
			$trimData[$ind]['acl']['qty']+=$trimsRecArr[$ind]['qty']-$trimsRecReArr[$ind]['qty'];
			$trimData[$ind]['acl']['amount']+=$trimsRecArr[$ind]['amount']-$trimsRecReArr[$ind]['amount'];
			$trimData[$ind]['cons_uom']=$trimsRecArr[$ind]['cons_uom'];
		}

	//print_r($trimData);

	// Trim Cost End============================
	// Embl Cost ============================
		$embData=array();
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and emb_name !=3";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
			$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
		}
	//print_r($embData);
		if($quotationId){
			$sql = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name !=3";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$embData['mkt']['qty']+=$mktcons;
				$embData['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name !=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
			
		}
	// Embl Cost End ============================
	// Wash Cost ============================
		$washData=array();
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and emb_name =3";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$washData['pre']['qty']+=$washQtyArr[$jobNumber][$row[csf('id')]];
			$washData['pre']['amount']+=$washAmtArr[$jobNumber][$row[csf('id')]];
		}
		if($quotationId){
			$sql = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name =3";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$washData['mkt']['qty']+=$mktcons;
				$washData['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$washData['acl']['qty']+=$row[csf('wo_qnty')];
			$washData['acl']['amount']+=$row[csf('amount')];
			
		}
	// Wash Cost End ============================
	// Commision Cost  ============================
		$commiData=array();
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}

		if($quotationId){
			$sql = "select id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pri_quo_commiss_cost_dtls  where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktamt=($row[csf('commission_amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('commission_amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['qty']=$quaOfferQnty;
				$commiData['mkt']['amount']+=$mktamt;
				$commiData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
		}
	// Commision Cost  End ============================

	// Commarcial Cost  ============================
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;

			//echo $commaAmtArr[$jobNumber][$row[csf('id')]].'/'.$exfactory_qty;
		}
		if($quotationId){
			$sql = "select id, item_id, rate, amount, status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id=".$quotationId."";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktamt=($row[csf('amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktamt;
				$commaData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
		}
	// Commarcial Cost  End ============================
	// Other Cost  ============================
		$otherData=array();
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_pre_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;

			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;

			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;

			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;

			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
		}
		if($quotationId){
			$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['qty']=$quaOfferQnty;
				$otherData['mkt']['freight']['amount']=$freightAmt;
				$otherData['mkt']['freight']['rate']=$freightAmt/$quaOfferQnty;

				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['qty']=$quaOfferQnty;
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
				$otherData['mkt']['lab_test']['rate']=$labTestAmt/$quaOfferQnty;

				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['qty']=$quaOfferQnty;
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
				$otherData['mkt']['inspection']['rate']=$inspectionAmt/$quaOfferQnty;

				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['qty']=$quaOfferQnty;

				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
				$otherData['mkt']['currier_pre_cost']['rate']=$currierPreCostAmt/$quaOfferQnty;

				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
				$otherData['mkt']['cm_cost']['rate']=$cmCostAmt/$quaOfferQnty;
			}
		}
		$financial_para=array();
	$sql_std_para=sql_select("select cost_per_minute,applying_period_date as from_period_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0  order by id desc");	
	foreach($sql_std_para as $row)
	{
		$period_date=date("m-Y", strtotime($row[csf('from_period_date')]));
		$financial_para[$period_date]['cost_per_minute']=$row[csf('cost_per_minute')];
	}
	$sql_cm_cost="select  jobNo, available_min,production_date from production_logicsoft where  jobNo='".$jobNumber."'";
	$cm_data_array=sql_select($sql_cm_cost);
	foreach($cm_data_array as $row)
	{
		$production_date=date("m-Y", strtotime($row[csf('production_date')]));
		$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
		$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
	}
	
		$sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['freight']['rate']=$row[csf('freight')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['lab_test']['rate']=$row[csf('lab_test')];
			}
			if($row[csf('cost_head')]==3){
				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['inspection']['rate']=$row[csf('inspection')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['currier_pre_cost']['rate']=$row[csf('currier_pre_cost')];
			}
			if($row[csf('cost_head')]==5){
				//$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['cm_cost']['rate']=$row[csf('cm_cost')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
		//$commaData['acl']['cm_cost']['rate']=$row[csf('cm_cost')];
			}
		}
	// Other Cost End ============================
		ob_start();
		?>
		<div style="width:1202px; margin:0 auto">
			<div style="width:1200px; font-size:20px; font-weight:bold" align="center"><? echo $company_arr[str_replace("'","",$company_name)]; ?></div>
			<div style="width:1200px; font-size:14px; font-weight:bold" align="center">Style wise Cost Comparison</div>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px" rules="all">
				<tr>
					<td width="80">Job Number</td>
					<td width="80"><? echo $jobNumber; ?></td>
					<td width="90">Buyer</td>
					<td width="100"><? echo $buyer_arr[$buyerName]; ?></td>
					<td>Style Ref. No</td>
					<td><? echo $styleRefno; ?></td>
					<td>Job Qty</td>
					<td align="right"><? echo $jobQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td width="80">Order Number</td>
					<td width="80" colspan="5"><? echo implode(",",$poNumberArr); ?></td>
					<td>Total Value</td>
					<td align="right"><? echo $jobValue." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td width="80">Ship Date</td>
					<td width="80" colspan="5"><? echo implode(",",$ShipDateArr); ?></td>
					<td>Unit Price</td>
					<td align="right"><? echo $unitPrice." ".$currency[$currencyId]; ?></td>
				</tr>
				<tr>
					<td width="80">Garments Item</td>
					<td width="80" colspan="5"><? echo implode(",",$gmtsItemArr); ?></td>
					<td>Ship Qty</td>
					<td align="right"> <? echo $exfactoryQty." Pcs"//.$unit_of_measurement[$uom]; ?></td>
				</tr>
				<tr>
					<td width="80">Shipment  Value</td>
					<td width="80" align="right"><? echo $exfactoryValue; ?></td>
					<td width="90">Short/Excess Qty</td>
					<td width="100" align="right"><? echo $shortExcessQty." Pcs"//.$unit_of_measurement[$uom];; ?></td>
					<td>Short/Excess Value</td>
					<td align="right"><? echo $shortExcessValue; ?></td>
					<td>Ship. Status</td>
					<td><? echo $shipment_status[$shipingStatus]; ?></td>
				</tr>
			</table>

			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr align="center" style="font-weight:bold">
					<td width="200">Item Description</td>
					<td width="60">UOM</td>
					<td width="100">Marketing Qty</td>
					<td width="100">Marketing Price</td>
					<td width="100">Marketing Value</td>
					<td width="100">Pre-Cost Qty</td>
					<td width="100">Pre-Cost Price</td>
					<td width="100">Pre-Cost Value</td>
					<td width="100">Actual Qty</td>
					<td width="100">Actual Price</td>
					<td width="100">Actual Value</td>
				</tr>
				<tr style="font-weight:bold" class="yarn">
					<td colspan="12">Yarn including yarn dyed</td>
				</tr>
				<?
				$GrandTotalMktValue=0;
				$GrandTotalPreValue=0;
				$GrandTotalAclValue=0;

				$yarnTrimMktValue=0;
				$yarnTrimPreValue=0;
				$yarnTrimAclValue=0;

				$totalMktValue=0;
				$totalPreValue=0;
				$totalAclValue=0;

				$totalMktQty=0;
				$totalPreQty=0;
				$totalAclQty=0;
				$y=1;
				foreach($YarnData as $index=>$row ){
					$des=explode("_",str_replace("'","",$index));
					$item_descrition = $lib_yarn_count[$des[0]]." ".$composition[$des[1]]." ".$des[2]."% ".$yarn_type[$des[3]];

					$totalMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalPreValue+=number_format($row['preCost']['amount'],4,".","");
					$totalAclValue+=number_format($row['acl']['amount'],4,".","");

					$totalMktQty+=number_format($row['mkt']['qty'],4,".","");
					$totalPreQty+=number_format($row['preCost']['qty'],4,".","");
					$totalAclQty+=number_format($row['acl']['qty'],4,".","");
					if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					 <tr class="yarn" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tryarn_<? echo $y; ?>','<? echo $bgcolor;?>')" id="tryarn_<? echo $y; ?>">
						<td width="200"><? echo $item_descrition ?></td>
						<td width="60">Kg</td>
						<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['preCost']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['preCost']['amount']/$row['preCost']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['preCost']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount']/$row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount'],4); ?></td>
					</tr>
					<?
					$y++;
				}
				foreach($conv_yarn_arr as $index=>$row ){
					$totalMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalPreValue+=number_format($row['preCost']['amount'],4,".","");
					
					$totalPreQty+=number_format($row['preCost']['qty'],4,".","");
					$totalMktQty+=number_format($row['mkt']['qty'],4,".","");
					?>
				
                 <tr class="yarn" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tryarn_<? echo $y; ?>','<? echo $bgcolor;?>')" id="tryarn_<? echo $y; ?>">
					
						<td width="200" title="Yarn Dyeing"><? echo $index ?></td>
						<td width="60">Kg</td>
						<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['preCost']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['preCost']['amount']/$row['preCost']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['preCost']['amount'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['acl']['amount']/$row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['acl']['amount'],4); ?></td>
					</tr>
                <?	
				$y++;
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" ><span id="yarntotal" class="adl-signs" onClick="yarnT(this.id,'.yarn')">+</span>&nbsp;&nbsp;Yarn Total</td>
					<td width="60"></td>
					<td width="100" align="right">
						<? 
						echo number_format($totalMktQty,4); 
						?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right">
						<? 
						echo number_format($totalMktValue,4); 
						$yarnTrimMktValue+=number_format($totalMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right">
						<? 
						echo number_format($totalPreQty,4);
						?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right" bgcolor=" <? if($totalPreValue>$totalMktValue){echo "yellow";} else{echo "";}?>">
						<? 
						echo number_format($totalPreValue,4); 
						$yarnTrimPreValue+=number_format($totalPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalPreValue,4,".","");
						?>
					</td>
					<td width="100" align="right">
						<a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','1')"><? echo number_format($totalAclQty,4); ?></a>
					</td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($totalAclValue>$totalPreValue){echo "red";} else{echo "";}?>">
						<? 
						echo number_format($totalAclValue,4);
						$yarnTrimAclValue+=number_format($totalAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalAclValue,4,".","");
						?>
					</td>
				</tr>
                 <?
                  $fab_bgcolor1="#E9F3FF"; $fab_bgcolor2="#FFFFFF";
				  $f=1;
				?>
				<tr class="fbpur" bgcolor="<? echo $fab_bgcolor1;?>" onClick="change_color('trfab_<? echo $f; ?>','<? echo $fab_bgcolor1;?>')" id="trfab_<? echo $f; ?>">
					<td width="200" >Fabric Purchase Cost (Knit)</td>
					<td width="60">Kg</td>
					<td width="100" align="right"><? echo number_format($fabPurArr['mkt']['knit']['qty'],4) ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['mkt']['knit']['amount']/$fabPurArr['mkt']['knit']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['mkt']['knit']['amount'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['pre']['knit']['qty'],4) ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['pre']['knit']['amount']/$fabPurArr['pre']['knit']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['pre']['knit']['amount'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['acl']['knit']['qty'],4) ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['acl']['knit']['amount']/$fabPurArr['acl']['knit']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['acl']['knit']['amount'],4); ?></td>
				</tr>
                <?
				  $f1=2;
				?>
				 <tr class="fbpur" bgcolor="<? echo $fab_bgcolor2;?>" onClick="change_color('trfab_<? echo $f1; ?>','<? echo $fab_bgcolor2;?>')" id="trfab_<? echo $f1; ?>">
					<td width="200" >Fabric Purchase Cost (Woven)</td>
					<td width="60">Yds</td>
					<td width="100" align="right"><? echo number_format($fabPurArr['mkt']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['mkt']['woven']['amount']/$fabPurArr['mkt']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['mkt']['woven']['amount'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['pre']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['pre']['woven']['amount']/$fabPurArr['pre']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['pre']['woven']['amount'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['acl']['woven']['qty'],4) ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['acl']['woven']['amount']/$fabPurArr['acl']['woven']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($fabPurArr['acl']['woven']['amount'],4); ?></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" ><span id="fbpurtotal"  class="adl-signs" onClick="yarnT(this.id,'.fbpur')">+</span>&nbsp;&nbsp;Fabric Purchase Cost Total</td>
					<td width="60"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right">
						<? 
						$fabPurMkt= number_format($fabPurArr['mkt']['woven']['amount'],4,".","")+number_format($fabPurArr['mkt']['knit']['amount'],4,".",""); 
						echo  number_format($fabPurMkt,4);
						$GrandTotalMktValue+=number_format($fabPurMkt,4,".","");
						$fabPurPre= number_format($fabPurArr['pre']['woven']['amount'],4,".","")+number_format($fabPurArr['pre']['knit']['amount'],4,".",""); 
						$fabPurAcl= number_format($fabPurArr['acl']['woven']['amount'],4,".","")+number_format($fabPurArr['acl']['knit']['amount'],4,".",""); 

						?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right" bgcolor=" <? if($fabPurPre>$fabPurMkt){echo "yellow";} else{echo "";}?>">
						<? 
						echo  number_format($fabPurPre,4);
						$GrandTotalPreValue+=number_format($fabPurPre,4,".","");
						?>
					</td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($fabPurAcl>$fabPurPre){echo "red";} else{echo "";}?>">
						<? 
						echo  number_format($fabPurAcl,4);
						$GrandTotalAclValue+=number_format($fabPurAcl,4,".","");
						?>
					</td>
				</tr>
				<tr style="font-weight:bold" class="knit">
					<td colspan="12">Knitting Cost</td>
				</tr>
				<?
				$totalKniMktValue=0;
				$totalKnitPreValue=0;
				$totalKnitAclValue=0;

				$totalKniMktQty=0;
				$totalKnitPreQty=0;
				$totalKnitAclQty=0;
				$k=1;
				foreach($knitData as $index=>$row ){
					$des=explode("_",str_replace("'","",$index));
					$item_descrition = $body_part[$des[0]]." ".$color_type[$des[1]]." ".$des[2]." ".$yarn_type[$des[3]];
					if(!trim($item_descrition)){
						$item_descrition=$index;
					}
					$totalKniMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalKnitPreValue+=number_format($row['pre']['amount'],4,".","");
					$totalKnitAclValue+=number_format($row['acl']['amount'],4,".","");

					$totalKniMktQty+=number_format($row['mkt']['qty'],4,".","");
					$totalKnitPreQty+=number_format($row['pre']['qty'],4,".","");
					$totalKnitAclQty+=number_format($row['acl']['qty'],4,".","");
				//$knitDesArr[$index]=$body_part[$row_knit[csf('body_part_id')]].", ".$color_type[$row_knit[csf('color_type_id')]].", ".$row_knit[csf('fabric_description')].", ".$row_knit[csf('gsm_weight')];
				 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					 <tr class="knit" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trknit_<? echo $k; ?>','<? echo $bgcolor;?>')" id="trknit_<? echo $k; ?>">
						<td width="200"><? echo $item_descrition ?></td>
						<td width="60">Kg</td>
						<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount']/$row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount'],4); ?></td>
					</tr>
					<?
					$k++;
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" > <span id="knittotal"  class="adl-signs" onClick="yarnT(this.id,'.knit')">+</span>&nbsp;&nbsp;Kniting Total</td>
					<td width="60"></td>
					<td width="100" align="right">
						<? echo number_format($totalKniMktQty,4);?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right">
						<? echo number_format($totalKniMktValue,4);
						$GrandTotalMktValue+=number_format($totalKniMktValue,4,".",""); 
						?>
					</td>
					<td width="100" align="right"><? echo number_format($totalKnitPreQty,4);?></td>
					<td width="100" align="right"></td>
					<td width="100" align="right" bgcolor=" <? if($totalKnitPreValue>$totalKniMktValue){echo "yellow";} else{echo "";}?>">
						<? 
						echo number_format($totalKnitPreValue,4);
						$GrandTotalPreValue+=number_format($totalKnitPreValue,4,".",""); 
						?>
					</td>
					<td width="100" align="right"><? echo number_format($totalKnitAclQty,4);?></td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($totalKnitAclValue>$totalKnitPreValue){echo "red";} else{echo "";}?>">
						<? 
						echo number_format($totalKnitAclValue,4);
						$GrandTotalAclValue+=number_format($totalKnitAclValue,4,".",""); 
						?>
					</td>
				</tr>
				<tr style="font-weight:bold" class="dyfi">
					<td colspan="12">Dye , Fin & Aop Cost</td>
				</tr>
				<?
				$totalFinMktValue=0;
				$totalFinPreValue=0;
				$totalFinAclValue=0;

				$totalFinMktQty=0;
				$totalFinPreQty=0;
				$totalFinAclQty=0;
				$d=1;
				foreach($finishData as $index=>$row ){
					$des=explode("_",str_replace("'","",$index));
					$item_descrition = $body_part[$des[0]]." ".$color_type[$des[1]]." ".$des[2]." ".$yarn_type[$des[3]];
					if(!trim($item_descrition)){
						$item_descrition=$index;
					}
					$totalFinMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalFinPreValue+=number_format($row['pre']['amount'],4,".","");
					$totalFinAclValue+=number_format($row['acl']['amount'],4,".","");

					$totalFinMktQty+=number_format($row['mkt']['qty'],4,".","");
					$totalFinPreQty+=number_format($row['pre']['qty'],4,".","");
					$totalFinAclQty+=number_format($row['acl']['qty'],4,".","");
					  if($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr class="dyfi" bgcolor="<? echo $bgcolor;?>" onClick="change_color('trdye_<? echo $d; ?>','<? echo $bgcolor;?>')" id="trdye_<? echo $d; ?>">
						<td width="200"><? echo $item_descrition ?></td>
						<td width="60">Kg</td>
						<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount']/$row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount'],4); ?></td>
					</tr>
					<?
					$d++;
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" > <span id="dyfitotal"  class="adl-signs" onClick="yarnT(this.id,'.dyfi')">+</span>&nbsp;&nbspDyeing, Finish & AOP Cost Total</td>
					<td width="60">Kg</td>
					<td width="100" align="right">
						<? echo number_format($totalFinMktQty,4);?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right" >
						<? 
						echo number_format($totalFinMktValue,4); 
						$GrandTotalMktValue+=number_format($totalFinMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right"><? echo number_format($totalFinPreQty,4);?></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"  bgcolor=" <? if($totalFinPreValue>$totalFinMktValue){echo "yellow";} else{echo "";}?>">
						<? 
						echo number_format($totalFinPreValue,4); 
						$GrandTotalPreValue+=number_format($totalFinPreValue,4,".","");
						?>
					</td>
					<td width="100" align="right"><? echo number_format($totalFinAclQty,4);?></td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($totalFinAclValue>$totalFinPreValue){echo "red";} else{echo "";}?>">
						<? 
						echo number_format($totalFinAclValue,4); 
						$GrandTotalAclValue+=number_format($totalFinAclValue,4,".","");
						?>
					</td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC;display:none">
					<td width="200" >&nbsp;&nbsp;&nbsp;AOP & Others Cost</td>
					<td width="60">Kg</td>
					<td width="100" align="right"><? echo number_format($aopData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($aopData['mkt']['amount']/$aopData['mkt']['qty'],4)?></td>
					<td width="100" align="right"><? echo number_format($aopData['mkt']['amount'],4); //$GrandTotalMktValue+=number_format($aopData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($aopData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($aopData['pre']['amount']/$aopData['pre']['qty'],4)?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($aopData['pre']['amount'],4,".","")>number_format($aopData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($aopData['pre']['amount'],4); //$GrandTotalPreValue+=number_format($aopData['pre']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($aopData['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($aopData['acl']['amount']/$aopData['acl']['qty'],4)?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($aopData['acl']['amount'],4,".","")>number_format($aopData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($aopData['acl']['amount'],4); //$GrandTotalAclValue+=number_format($aopData['acl']['amount'],4,".","");?></td>
				</tr>
                
                 <tr style="font-weight:bold" class="transc">
					<td colspan="12">Transfer Cost</td>
				</tr>
              <?
               
                  $trans_bgcolor1="#E9F3FF"; $trans_bgcolor2="#FFFFFF";
				  $tt=1;//$tot_grey_fab_cost=($grey_fab_cost/$knit_grey_qty)*$grey_fab_trans_qty;;
				?>
                 <tr class="transc" bgcolor="<? echo $trans_bgcolor1;?>" onClick="change_color('trtrans_<? echo $tt; ?>','<? echo $trans_bgcolor1;?>')" id="trtrans_<? echo $tt; ?>">
						<td width="200">Grey Fabric Cost<? //echo $item_descrition ?></td>
						<td width="60">Kg</td>
						<td width="100" align="right"><? //echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['mkt']['amount'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? //echo number_format($row['pre']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><? echo number_format($tot_grey_fab_cost_acl/$grey_fab_trans_qty_acl,4); ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','2')"><? echo number_format($tot_grey_fab_cost_acl,4); ?></a><? //echo number_format($tot_grey_fab_cost_acl,4); ?></td>
					</tr>
                    
				<?
				 $tt2=1;
				?>
                <tr class="transc" bgcolor="<? echo $trans_bgcolor2;?>" onClick="change_color('trtrans2_<? echo $tt2; ?>','<? echo $trans_bgcolor2;?>')" id="trtrans2_<? echo $tt2; ?>">
						<td width="200">Finished Fabric Cost</td>
						<td width="60">Kg</td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><? echo number_format($tot_fin_fab_transfer_cost/$tot_fin_fab_transfer_qty,4); ?></td>
						<td width="100" align="right"><a href='##' onClick="openmypage_issue('<? echo implode("_",array_keys($poNumberArr)); ?>','3')"><? echo number_format($tot_fin_fab_transfer_cost,4); ?></a><? //echo number_format($tot_fin_fab_transfer_cost,4); ?></td>
					</tr>
					<?
				
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" ><span id="transtotal"  class="adl-signs" onClick="yarnT(this.id,'.transc')">+</span>&nbsp;&nbsp;Transfer Cost Total</td>
					<td width="60">Kg</td>
					<td width="100" align="right">
						
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right" >
						
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right">
						
					</td>
					<td width="100" align="right"><? echo number_format($grey_fab_trans_qty_acl+$tot_fin_fab_transfer_qty,4);?></td>
					<td width="100"  align="right"><? echo number_format(($tot_fin_fab_transfer_cost+$tot_grey_fab_cost_acl)/($tot_fin_fab_transfer_qty+$grey_fab_trans_qty_acl),4);?></td>
					<td width="100" align="right">
						<? 
						echo number_format($tot_fin_fab_transfer_cost+$tot_grey_fab_cost_acl,4); 
						$total_grey_fin_fab_transfer_actl_cost=$tot_fin_fab_transfer_cost+$tot_grey_fab_cost_acl;
						$GrandTotalAclValue+=number_format($total_grey_fin_fab_transfer_actl_cost,4,".","");
						?>
					</td>
				</tr>
                
				<tr>
					<td colspan="12" style="font-weight:bold">Trims Cost</td>
				</tr>
				<?
				$t=1;
				$totalTrimMktValue=0;
				$totalTrimPreValue=0;
				$totalTrimAclValue=0;
				foreach($trimData as $index=>$row ){
					$item_descrition = $trim_groupArr[$index]['item_name'];
					$totalTrimMktValue+=number_format($row['mkt']['amount'],4,".","");
					$totalTrimPreValue+=number_format($row['pre']['amount'],4,".","");
					$totalTrimAclValue+=number_format($row['acl']['amount'],4,".","");
					if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrims_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtrims_<? echo $t; ?>">
						<td width="200"><? echo $item_descrition ?></td>
						<td width="60"><? echo $unit_of_measurement[$row['cons_uom']];?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount']/$row['mkt']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['mkt']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['amount']/$row['pre']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['pre']['amount'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount']/$row['acl']['qty'],4); ?></td>
						<td width="100" align="right"><? echo number_format($row['acl']['amount'],4); ?></td>
					</tr>
					<?
					$t++;
				}
				?>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" >Trims Total</td>
					<td width="60"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right">
						<? 
						echo number_format($totalTrimMktValue,4); 
						$yarnTrimMktValue+=number_format($totalTrimMktValue,4,".","");
						$GrandTotalMktValue+=number_format($totalTrimMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right" bgcolor=" <? if($totalTrimPreValue>$totalTrimMktValue){echo "yellow";} else{echo "";}?>">
						<? 
						echo number_format($totalTrimPreValue,4);
						$yarnTrimPreValue+=number_format($totalTrimPreValue,4,".","");
						$GrandTotalPreValue+=number_format($totalTrimPreValue,4,".",""); 
						?>
					</td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($totalTrimAclValue>$totalTrimPreValue){echo "red";} else{echo "";}?>">
						<? 
						echo number_format($totalTrimAclValue,4);
						$yarnTrimAclValue+=number_format($totalTrimAclValue,4,".","");
						$GrandTotalAclValue+=number_format($totalTrimAclValue,4,".",""); 
						?>
					</td>
				</tr>
				<?
				$totalOtherMktValue=0;
				$totalOtherPreValue=0;
				$totalOtherAclValue=0;
				$other_bgcolor="#E9F3FF"; $emb_bgcolor2="#FFFFFF";
				$embl=1;$w=1;$comi=1;$comm=1;$fc=1;$tc=1;
				?>
				 <tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('emblish_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="emblish_<? echo $embl; ?>">
					<td width="200" >Embellishment Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['amount']/$embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($embData['mkt']['amount'],4); $totalOtherMktValue+=number_format($embData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($embData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($embData['pre']['amount']/$embData['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['pre']['amount'],4,".","")>number_format($embData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($embData['pre']['amount'],4); $totalOtherPreValue+=number_format($embData['pre']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($embData['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($embData['acl']['amount']/$embData['acl']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($embData['acl']['amount'],4,".","")>number_format($embData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($embData['acl']['amount'],4); $totalOtherAclValue+=number_format($embData['acl']['amount'],4,".","");?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('wash_<? echo $w; ?>','<? echo $other_bgcolor;?>')" id="wash_<? echo $w; ?>">
					<td width="200" >Wash Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($washData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($washData['mkt']['amount']/$embData['mkt']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($washData['mkt']['amount'],4); $totalOtherMktValue+=number_format($washData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($washData['pre']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($washData['pre']['amount']/$embData['pre']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($washData['pre']['amount'],4,".","")>number_format($washData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($washData['pre']['amount'],4);$totalOtherPreValue+=number_format($washData['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($washData['acl']['qty'],4); ?></td>
					<td width="100" align="right"><? echo number_format($washData['acl']['amount']/$embData['acl']['qty'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($washData['acl']['amount'],4,".","")>number_format($washData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($washData['acl']['amount'],4);$totalOtherAclValue+=number_format($washData['acl']['amount'],4,".",""); ?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commi_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commi_<? echo $embl; ?>">
					<td width="200" >Commission Cost</td>
					<td width="60"><? echo $costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commiData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commiData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($commiData['pre']['amount'],4,".","")>number_format($commiData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commiData['pre']['amount'],4); $totalOtherPreValue+=number_format($commiData['pre']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right">
						<?
			//$aclCommiAmt=($commiData['pre']['rate']/($costPerQty*$totalSetQnty))*$exfactoryQty;
						$aclCommiAmt=($commiData['pre']['rate'])*$exfactoryQty;
			//echo "(".$commiData['pre']['rate']."/(".$costPerQty*$totalSetQnty."))*".$exfactoryQty;
						echo number_format($aclCommiAmt/$exfactoryQty,4); 

						?>
					</td>
					<td width="100" align="right" bgcolor="<? if(number_format($aclCommiAmt,4,".","")>number_format($commiData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>">
						<? 
						echo number_format($aclCommiAmt,4); 
						$totalOtherAclValue+=$aclCommiAmt;
						?>
					</td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('commer_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="commer_<? echo $embl; ?>">
					<td width="200" >Commercial Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['mkt']['amount'],4); $totalOtherMktValue+=number_format($commaData['mkt']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($commaData['pre']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($commaData['pre']['amount'],4,".","")>number_format($commaData['mkt']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($commaData['pre']['amount'],4); $totalOtherPreValue+=number_format($commaData['pre']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? echo  number_format($commaData['pre']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($exfactoryQty*$commaData['pre']['rate'],4,".","")>number_format($commaData['pre']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($exfactoryQty*$commaData['pre']['rate'],4); $totalOtherAclValue+=number_format($exfactoryQty*$commaData['pre']['rate'],4,".","");?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trfc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trfc_<? echo $embl; ?>">
					<td width="200" >Freight Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['freight']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['freight']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['freight']['amount'],4,".","")>number_format($otherData['mkt']['freight']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['freight']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['freight']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo $exfactoryQty; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['acl']['freight']['amount']/$exfactoryQty,4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['acl']['freight']['amount'],4,".","")>number_format($otherData['pre']['freight']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['freight']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['freight']['amount'],4,".","");?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trtc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trtc_<? echo $embl; ?>">
					<td width="200" >Testing Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['lab_test']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['lab_test']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['lab_test']['amount'],4,".","")>number_format($otherData['mkt']['lab_test']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['lab_test']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['lab_test']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? echo number_format($otherData['acl']['lab_test']['amount']/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['acl']['lab_test']['amount'],4,".","")>number_format($otherData['pre']['lab_test']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['lab_test']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['lab_test']['amount'],4,".","");?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('tric_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="tric_<? echo $embl; ?>">
					<td width="200" >Inspection Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['inspection']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['inspection']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['inspection']['amount'],4,".","")>number_format($otherData['mkt']['inspection']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['inspection']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['inspection']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? echo number_format($otherData['acl']['inspection']['amount']/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['acl']['inspection']['amount'],4,".","")>number_format($otherData['pre']['inspection']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['inspection']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['inspection']['amount'],4,".","");?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trcc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trcc_<? echo $embl; ?>">
					<td width="200" >Courier Cost</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['currier_pre_cost']['amount'],4);$totalOtherMktValue+=number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['currier_pre_cost']['rate'],4); ?></td>
					<td width="100" align="right"  bgcolor="<? if(number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['mkt']['currier_pre_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['currier_pre_cost']['amount'],4);$totalOtherPreValue+=number_format($otherData['pre']['currier_pre_cost']['amount'],4,".",""); ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? echo number_format($otherData['acl']['currier_pre_cost']['amount']/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","")>number_format($otherData['pre']['currier_pre_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><? echo number_format($otherData['acl']['currier_pre_cost']['amount'],4); $totalOtherAclValue+=number_format($otherData['acl']['currier_pre_cost']['amount'],4,".","");?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trmc_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trmc_<? echo $embl; ?>">
					<td width="200" >Making Cost (CM)</td>
					<td width="60"><? echo 'Pcs';//$costPerUom; ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['mkt']['cm_cost']['amount'],4); $totalOtherMktValue+=number_format($otherData['mkt']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['qty'],0); ?></td>
					<td width="100" align="right"><? echo number_format($otherData['pre']['cm_cost']['rate'],4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['pre']['cm_cost']['amount'],4,".","")>number_format($otherData['mkt']['cm_cost']['amount'],4,".","")){echo "yellow";}else{ echo "";} ?>"><? echo number_format($otherData['pre']['cm_cost']['amount'],4); $totalOtherPreValue+=number_format($otherData['pre']['cm_cost']['amount'],4,".","");?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? echo number_format($otherData['acl']['cm_cost']['amount']/$exfactoryQty,4); ?></td>
					<td width="100" align="right" bgcolor="<? if(number_format($otherData['acl']['cm_cost']['amount'],4,".","")>number_format($otherData['pre']['cm_cost']['amount'],4,".","")){echo "red";}else{ echo "";} ?>"><?  $totalOtherAclValue+=number_format($otherData['acl']['cm_cost']['amount'],4,".","");?><a href="#report_details" onClick="openmypage_mkt_cm_popup('<? echo $jobNumber; ?>','mkt_cm_cost_popup','Making CM Cost Details','2','1000px')"><? echo number_format($otherData['acl']['cm_cost']['amount'],4); ?></a></td>
				</tr>
				<tr style="font-weight:bold;background-color:#CCC">
					<td width="200" >Others Total</td>
					<td width="60"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right">
						<? 
						echo number_format($totalOtherMktValue,4); 
						$GrandTotalMktValue+=number_format($totalOtherMktValue,4,".","");
						?>
					</td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherPreValue>$totalOtherMktValue){echo "yellow";} else{echo "";}?>">
						<? 
						echo number_format($totalOtherPreValue,4); 
						$GrandTotalPreValue+=number_format($totalOtherPreValue,4,".","");
						?>
					</td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($totalOtherAclValue>$totalOtherPreValue){echo "red";} else{echo "";}?>">
						<? 
						echo number_format($totalOtherAclValue,4); 
						$GrandTotalAclValue+=number_format($totalOtherAclValue,4,".","");
						?>
					</td>
				</tr>
				<tr style="font-weight:bold;background-color:#C60">
					<td width="200" >Grand Total</td>
					<td width="60"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalPreValue>$GrandTotalMktValue){echo "yellow";} else{echo "";}?>"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100" align="right" bgcolor=" <? if($GrandTotalAclValue>$GrandTotalPreValue){echo "red";} else{echo "";}?>"><? echo number_format($GrandTotalAclValue,4);?></td>
				</tr>
				<tr  bgcolor="<? echo $other_bgcolor;?>" onClick="change_color('trsv_<? echo $embl; ?>','<? echo $other_bgcolor;?>')" id="trsv_<? echo $embl; ?>">
					<td width="200" >Shipment Value</td>
					<td width="60"></td>
					<td width="100" align="right"><? echo $quaOfferQnty; ?></td>
					<td width="100" align="right"><? echo number_format($quaPriceWithCommnPcs,4) ?></td>
					<td width="100" align="right">
						<? 
						$quaOfferValue=$quaOfferQnty *$quaPriceWithCommnPcs;
						echo number_format($quaOfferValue,4) 
						?>
					</td>
					<td width="100" align="right"><? echo $jobQty ?></td>
					<td width="100" align="right"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="100" align="right"><? echo $exfactoryQty ?></td>
					<td width="100" align="right"><? echo number_format($unitPrice,4) ?></td>
					<td width="100" align="right"><? echo number_format($exfactoryValue,4) ?></td>
				</tr>
			</table>
			<strong>Profit Summary </strong>
			<table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1200px; margin-top:5px" rules="all">
				<tr align="center" style="font-weight:bold">
					<td width="200" >Type</td>
					<td width="160">Total P.O/Ex-Fact. Value</td>
					<td width="150">Total Expenditure</td>
					<td width="100">Expend %</td>
					<td width="150">Yarn & Trims</td>
					<td width="100">Yarn & Trims %</td>
					<td width="200">Profit Margin</td>
					<td width="100">Profit Margin %</td>
				</tr>
				<tr>
					<td width="200" >Marketing Cost</td>
					<td width="160" align="right"><? echo number_format($quaOfferValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"><? echo number_format(($GrandTotalMktValue/$quaOfferValue)*100,4); ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimMktValue,4) ?></td>
					<td width="100" align="right"><? echo number_format(($yarnTrimMktValue/$quaOfferValue)*100,4); ?></td>
					<td width="200" align="right"><? echo number_format($quaOfferValue-$GrandTotalMktValue,4); ?></td>
					<td width="100" align="right"><? echo number_format((($quaOfferValue-$GrandTotalMktValue)/$quaOfferValue)*100,4); ?></td>
				</tr>
				<tr>
					<td width="200" >Pre Costing</td>
					<td width="160" align="right"><? echo number_format($jobValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalPreValue,4);?></td>
					<td width="100" align="right"><? echo number_format(($GrandTotalPreValue/$jobValue)*100,4); ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimPreValue,4) ?></td>
					<td width="100" align="right"><? echo number_format(($yarnTrimPreValue/$jobValue)*100,4); ?></td>
					<td width="200" align="right"><? echo number_format($jobValue-$GrandTotalPreValue,4);?></td>
					<td width="100" align="right"><? echo number_format((($jobValue-$GrandTotalPreValue)/$jobValue)*100,4);?></td>
				</tr>
				<tr>
					<td width="200" >Actual Costing</td>
					<td width="160" align="right"><? echo number_format($exfactoryValue,4) ?></td>
					<td width="150" align="right"><? echo number_format($GrandTotalAclValue,4);?></td>
					<td width="100" align="right"><? echo number_format(($GrandTotalAclValue/$exfactoryValue)*100,4); ?></td>
					<td width="150" align="right"><? echo number_format($yarnTrimAclValue,4) ?></td>
					<td width="100" align="right"><? echo number_format(($yarnTrimAclValue/$exfactoryValue)*100,4); ?></td>
					<td width="200" align="right"><? echo number_format($exfactoryValue-$GrandTotalAclValue,4);?></td>
					<td width="100" align="right"><? echo number_format((($exfactoryValue-$GrandTotalAclValue)/$exfactoryValue)*100,4);?></td>
				</tr>
			</table>
		</div>
		<?
		foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename){
			if( @filemtime($filename) < (time()-$seconds_old) )
				@unlink($filename);
		}

		$html=ob_get_contents();
		$name=time();
		$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$html);
		$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
		echo "$total_data****$filename";
		exit();
	}

	if($action=="issue_popup")
	{
		echo load_html_head_contents("Popup Info","../../../../", 1, 1);
		extract($_REQUEST);
		?>
		<script type="text/javascript">
			function generate_worder_report(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, entry_form, is_short) {
				var action_method = "action=show_fabric_booking_report3";

				if (type == 1) {

					if(is_short==2){

						if (entry_form == 108) {
							report_title = "&report_title=Partial Fabric Booking";
							http.open("POST", "../../../../order/woven_order/requires/partial_fabric_booking_controller.php", true);
						} else {
							report_title = "&report_title=Main Fabric Booking";
							http.open("POST", "../../../../order/woven_order/requires/fabric_booking_controller.php", true);
						}
					}
					if(is_short==1){
						report_title = "&report_title=Short Fabric Booking";
						http.open("POST", "../../../../order/woven_order/requires/short_fabric_booking_controller.php", true);
					}
				}else {
					report_title = "&report_title=Sample Fabric Booking Urmi";
					http.open("POST", "../../../../order/woven_order/requires/sample_booking_controller.php", true);
				}

				var data = action_method + report_title +
				'&txt_booking_no=' + "'" + booking_no + "'" +
				'&cbo_company_name=' + "'" + company_id + "'" +
				'&txt_order_no_id=' + "'" + order_id + "'" +
				'&cbo_fabric_natu=' + "'" + fabric_nature + "'" +
				'&cbo_fabric_source=' + "'" + fabric_source + "'" +
				'&id_approved_id=' + "'" + approved + "'" +
				'&txt_job_no=' + "'" + job_no + "'";
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
					d.close();
				}
			}
		</script>
		<?
		$po_id = implode(",",explode("_",$po_id));
		$requisition_details = sql_select("select a.booking_no,c.requisition_no from ppl_planning_info_entry_mst a,ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.dtls_id=c.knit_id and b.po_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		$requisition_arr = array();
		foreach ($requisition_details as $row) {
			$requisition_arr[$row[csf("requisition_no")]] = $row[csf("booking_no")];
		}

		$issue_return=sql_select("select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty, a.is_sales, b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount,c.issue_id,d.booking_no,c.receive_basis from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.issue_id=d.id and a.trans_type=4 and a.entry_form=9 and a.po_breakdown_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		$issue_return_arr = $booking_req_return = array();
		foreach ($issue_return as $row) {
			if($row[csf("receive_basis")] == 1){
				$issue_return_arr[$row[csf("booking_no")]]["issue_qnty"] += $row[csf("quantity")];
			}else{
				$issue_return_arr[$row[csf("requisition_no")]]['req_qnty'] += $row[csf("quantity")];
				$booking_req_return[$requisition_arr[$row[csf("requisition_no")]]] = $row[csf("requisition_no")];
			}
		}

		$issue_details = sql_select("select a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,b.product_name_details,c.receive_basis,d.booking_no, c.requisition_no from order_wise_pro_details a,product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=2 and a.entry_form=3 and a.po_breakdown_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id");

		$issue_arr = $booking_req = array();
		foreach ($issue_details as $row) {
			$issue_arr[$row[csf("booking_no")]]["prod_id"] = $row[csf("product_name_details")];
			if($row[csf("receive_basis")] == 1){
				$issue_arr[$row[csf("booking_no")]]["issue_qnty"] += $row[csf("quantity")];
			}else{
				$issue_arr[$row[csf("requisition_no")]]['req_qnty'] += $row[csf("quantity")];
				$booking_req[$requisition_arr[$row[csf("requisition_no")]]] = $row[csf("requisition_no")];
			}
		}

		$booking_details=sql_select("select c.booking_no,c.booking_type,c.is_short,d.entry_form,d.company_id,d.item_category,d.fabric_source, c.job_no,d.is_approved from wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where b.id=c.po_break_down_id and c.booking_no=d.booking_no and b.id in($po_id) and c.booking_type in(1,4) group by c.booking_no,c.booking_type,c.is_short,d.entry_form,d.company_id,d.item_category, d.fabric_source,c.job_no,d.is_approved");
		?>
		<table class="rpt_table" border="1" cellpadding="5" cellspacing="2" width="100%" rules="all">
			<tr>
				<th>Yarn Discription</th>
				<th>Booking Type</th>
				<th>Booking NO</th>
				<th>Yarn Issue Qty</th>
			</tr>
			<?php
			$issue_total = 0;
			foreach ($booking_details as $row) {
				?>			
				<tr>
					<td align="center"><?php echo $issue_arr[$row[csf("booking_no")]]["prod_id"];?></td>
					<td align="center">
						<?php
						if($row[csf("booking_type")] == 1){
							if($row[csf("is_short")] == 1){
								echo "Short Fabric Booking";
							}else{
								echo "Main Fabric Booking";
							}
						} else {
							echo "Sample Fabric Booking";
						}
						?>							
					</td>
					<td align="center">
						<?php
						if($row[csf('booking_type')] == 1 && $row[csf('is_short')] == 2){
							?>
							<span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_worder_report(<?php echo $row[csf('booking_type')];?>,'<?php echo $row[csf('booking_no')];?>',<?php echo $row[csf('company_id')];?>,'<?php echo $po_id;?>',<?php echo $row[csf('item_category')];?>,<?php echo $row[csf('fabric_source')];?>,'<?php echo $row[csf('job_no')];?>',<?php echo $row[csf('is_approved')];?>,<?php echo $row[csf('entry_form')];?>,<?php echo $row[csf('is_short')];?>)"><?php echo $row[csf("booking_no")];?></span>
							<?php
						}else{
							echo $row[csf("booking_no")];
						}
						?>
					</td>
					<td align="right">
						<?php 
						$issue_qnty = $issue_arr[$booking_req[$row[csf("booking_no")]]]['req_qnty'] + $issue_arr[$row[csf("booking_no")]]["issue_qnty"];
						$issue_return_qnty = $issue_return_arr[$booking_req[$row[csf("booking_no")]]]['req_qnty'] + $issue_return_arr[$row[csf("booking_no")]]["issue_qnty"];
						echo $issue_qnty = number_format(($issue_qnty - $issue_return_qnty),2,'.','');
						?>							
					</td>
				</tr>
				<?php 
				$issue_total += $issue_qnty;
			} 
			?>
			<tr>
				<th colspan="3" align="right">Grand Total</th>
				<th align="right"><?php echo number_format($issue_total,2,'.','');?></th>
			</tr>
		</table>
		<?
	}
	
	if($action=="show_transfer_popup")
	{
		echo load_html_head_contents("Transfer Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		?>
		<script type="text/javascript">
			
		</script>
		<?
		$product_arr_details=return_library_array( "select id, product_name_details from product_details_master",'id','product_name_details');
		//$product_arr_details=return_library_array( "select id, product_name_details from product_details_master",'id','product_name_details');
		$sql_wopo="select id,po_number from wo_po_break_down where  is_deleted=0 and status_active=1";
		$data_result=sql_select($sql_wopo);
		foreach($data_result as $row){
			$po_dtls_arr[$row[csf("id")]]['po_no']=$row[csf("po_number")];
		}

		$po_id = implode(",",explode("_",$po_id));
		
		
		?>
         <script>

        function print_window()
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>	
        <fieldset style="width:770px; margin-left:7px">
        <div id="report_container" align="center">
         <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
            <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="7">Transfer In</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th  width="100">Transfer Qnty</th>
                        <th>Transfer Amount</th>
                    </tr>
                </thead>
                <?
                $i = 1;
                $total_trans_in_qnty =$total_trans_in_amt= 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id,a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type =5 and c.entry_form in (13,83) and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, a.to_order_id,b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
						
						$from_order_ids=$row[csf("from_order_id")];
						$condition1= new condition();
						$condition1->po_id("in($from_order_ids)");
						$condition1->init();
						$conversion1= new conversion($condition1);
						//echo $conversion1->getQuery(); die;
						$conversion_costing_arr_process=$conversion1->getAmountArray_by_orderAndProcess();
						$conversion1= new conversion($condition1);
						$conversion_costing_arr_process_qty=$conversion1->getQtyArray_by_orderAndProcess();
						
						$knit_cost_out=$conversion_costing_arr_process[$from_order_ids][1];
						$knit_qty_out=$conversion_costing_arr_process_qty[$from_order_ids][1];
						$knit_charge_out=$knit_cost_out/$knit_qty_out;
						//echo $knit_qty.'=='.$knit_qty;
					 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_dtls_arr[$row[csf("from_order_id")]]['po_no']; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td  width="100" align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td> 
                        <td align="right" title="<? echo number_format($knit_charge_out, 4); ?>"><? echo number_format($row[csf('transfer_qnty')]*$knit_charge_out, 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_in_qnty += $row[csf('transfer_qnty')];
					$total_trans_in_amt += $row[csf('transfer_qnty')]*$knit_charge_out;
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                   <td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
                    <td align="right"><? echo number_format($total_trans_in_amt, 2); ?></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="7">Transfer Out</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th  width="100">Transfer Qnty</th>
                        <th>Transfer Amount</th>
                    </tr>
                </thead>
                <?
                $total_trans_out_qnty=$total_trans_out_amt=0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id,a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form in (83,13) and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.from_order_id,c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
						
					 	$out_from_order_id=$row[csf('from_order_id')];
						 //echo $from_order_id.'d';
					 	$condition2= new condition();
						$condition2->po_id("in($out_from_order_id)");
						
						$condition2->init();
						$conversion2= new conversion($condition2);
						//echo $conversion2->getQuery(); die;
						$conversion_costing_arr_process=$conversion2->getAmountArray_by_orderAndProcess();
						$conversion2= new conversion($condition2);
						$conversion_costing_arr_process_qty=$conversion2->getQtyArray_by_orderAndProcess();
						//print_r($conversion_costing_arr_process);
						$knit_cost=$conversion_costing_arr_process[$out_from_order_id][1];
						$knit_qty=$conversion_costing_arr_process_qty[$out_from_order_id][1];
						$knit_charge=$knit_cost/$knit_qty;
						
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_dtls_arr[$row[csf("to_order_id")]]['po_no'] ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td  width="100" align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                         <td align="right" title="<? echo $knit_charge; ?>"><? echo number_format($row[csf('transfer_qnty')]*$knit_charge, 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_out_qnty += $row[csf('transfer_qnty')];
					 $total_trans_out_amt += $row[csf('transfer_qnty')]*$knit_charge;
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                   <td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
                    <td align="right"><? echo number_format($total_trans_out_amt, 2); ?></td>
                </tr>
                <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Net Transfer</th>
                <th align="right"><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
                <th align="right"><?  echo number_format($total_trans_in_amt  - $total_trans_out_amt , 2); ?></th>
                </tfoot>
            </table>	
        </div>
    </fieldset>
		<?
	}
	if ($action == "show_finish_trans_popup") {
    echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
    $po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
	
    ?>
    <script>

        function print_window()
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>	
    <div style="width:775px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:770px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="7">Transfer In</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                         <th width="100">Transfer Qnty</th>
                        <th>Transfer Amount</th>
                    </tr>
                </thead>
                <?
				$po_id = implode(",",explode("_",$po_id));
                $i = 1;
                $total_trans_in_qnty = $total_trans_in_amt=0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id,a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in(5) and c.entry_form in (15) and c.po_breakdown_id in ($po_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id,a.from_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
					$fin_from_order_id=$row[csf("to_order_id")];
					$condition1= new condition();
					$condition1->po_id("in($fin_from_order_id)");
					$condition1->init();
					$conversion1= new conversion($condition1);
					//echo $conversion->getQuery(); die;
					$fin_conversion_costing_arr_process=$conversion1->getAmountArray_by_orderAndProcess();
					$conversion1= new conversion($condition1);
				 	$fin_conversion_costing_arr_process_qty=$conversion1->getQtyArray_by_orderAndProcess();
					// $knit_cost=$knit_qty=0;
						 $tot_dye_finish_cost_pre=0;$tot_dye_finish_cost_pre_qty=0;
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
							{
								$tot_dye_finish_cost_pre+=$fin_conversion_costing_arr_process[$fin_from_order_id][$process_id];
								
								$tot_dye_finish_cost_pre_qty+=$fin_conversion_costing_arr_process_qty[$fin_from_order_id][$process_id];
							}
						}
						
						$finish_charge=$tot_dye_finish_cost_pre/$tot_dye_finish_cost_pre_qty;
						//echo $tot_dye_finish_cost_pre.'='.$tot_dye_finish_cost_pre_qty.'<br>';
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                        <td align="right" title="<? echo $finish_charge; ?>"><? echo number_format($row[csf('transfer_qnty')]*$finish_charge, 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_in_qnty += $row[csf('transfer_qnty')];
					 $total_trans_in_amt += $row[csf('transfer_qnty')]*$finish_charge;
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
                    <td align="right"><? echo number_format($total_trans_in_amt, 2); ?></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="7">Transfer Out</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                         <th width="100">Transfer Qnty</th>
                        <th>Transfer Amount</th>
                    </tr>
                </thead>
                <?
                $total_trans_out_qnty=$total_trans_out_amt=0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no,a.from_order_id, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form in (15) and c.po_breakdown_id in ($po_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no,a.from_order_id, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
						$fin_from_order_id=$row[csf("from_order_id")];
					$condition2= new condition();
					$condition2->po_id("in($fin_from_order_id)");
					$condition2->init();
					$conversion2= new conversion($condition2);
					//echo $conversion->getQuery(); die;
					$fin_conversion_costing_arr_process=$conversion2->getAmountArray_by_orderAndProcess();
					$conversion2= new conversion($condition2);
				 	$fin_conversion_costing_arr_process_qty=$conversion2->getQtyArray_by_orderAndProcess();
					// $knit_cost=$knit_qty=0;
						 $tot_dye_finish_cost_pre_out=0;$tot_dye_finish_cost_pre_qty_out=0;
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
							{
								$tot_dye_finish_cost_pre_out+=$fin_conversion_costing_arr_process[$fin_from_order_id][$process_id];
								
								$tot_dye_finish_cost_pre_qty_out+=$fin_conversion_costing_arr_process_qty[$fin_from_order_id][$process_id];
							}
						}
						$finish_charge_out=$tot_dye_finish_cost_pre_out/$tot_dye_finish_cost_pre_qty_out;
						
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                        <td align="right" title="<? echo $finish_charge_out; ?>"><? echo number_format($row[csf('transfer_qnty')]*$finish_charge_out, 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_out_qnty += $row[csf('transfer_qnty')];
					$total_trans_out_amt += $row[csf('transfer_qnty')]*$finish_charge_out;
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
                     <td align="right"><? echo number_format($total_trans_out_amt, 2); ?></td>
                </tr>
                <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Net Transfer</th>
                <th align="right"><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
                 <th align="right"><? echo number_format($total_trans_in_amt - $total_trans_out_amt, 2); ?></th>
                </tfoot>
            </table>	
        </div>
    </fieldset>  
    <?
    exit();
}

if($action=="mkt_cm_cost_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1);
	extract($_REQUEST);
	

?>
	<fieldset style="width:950px; margin-left:7px">
        <table class="rpt_table" width="950px" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption> <b>Making Cost (CM) Actaul</b></caption>
            <thead>
                <th width="30">SL</th>
                <th width="110">Order Closing Date</th>
                <th width="100">Buyer</th>
                <th width="180">Style</th>
                <th width="110">Job No.</th>
                <th width="80">Available Min.</th>
                <th width="80">Produce Min</th>
                <th width="60">Effi%</th>
                <th width="60">CPM</th>
                <th>CM</th>
            </thead>
        </table>
        <div style="width:970px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="950px" cellpadding="0" cellspacing="0">  
				<?
				$buyer_array = return_library_array("select id, buyer_name from lib_buyer ", "id", "buyer_name");
				$job_effi_array = return_library_array("select job_no, sew_effi_percent from wo_pre_cost_mst ", "job_no", "sew_effi_percent");
				
				$financial_para=array();
				$sql_std_para=sql_select("select cost_per_minute,applying_period_date as from_period_date from lib_standard_cm_entry where company_id=$company and status_active=1 and is_deleted=0  order by id desc");	
				foreach($sql_std_para as $row)
				{
					$period_date=date("m-Y", strtotime($row[csf('from_period_date')]));
					$financial_para[$period_date]['cost_per_minute']=$row[csf('cost_per_minute')];
				}
                $i=1;  
				$sql_job="select  a.job_no,a.buyer_name,a.style_ref_no,b.production_date,b.produce_min,b.available_min from wo_po_details_master a,production_logicsoft b where a.job_no=b.jobNo and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 ";
				$result_job=sql_select( $sql_job );
                foreach($result_job as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$production_date=date("m-Y", strtotime($row[csf('production_date')]));
					$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
					$cm_cost=($row[csf('available_min')]*$cost_per_minute)/$exchange_rate;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="110"><p><? echo change_date_format($row[csf('production_date')]) ?></p></td>
                        <td width="100" align="center"><? echo $buyer_array[$row[csf('buyer_name')]]; ?></td>
                        <td width="180"><p><? echo $row[csf('style_ref_no')]; ?></p></td> 
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td align="right" width="80"><? echo $row[csf('available_min')]; ?></td>
                        <td align="right" width="80"><? echo $row[csf('produce_min')]; ?>&nbsp;</td>
                        <td align="right" width="60"><? echo number_format(($row[csf('produce_min')]/$row[csf('available_min')])*100,2);//$job_effi_array[$row[csf('job_no')]]; ?>&nbsp;</td>
                        <td align="right" width="60"><? echo $cost_per_minute; ?>&nbsp;</td>
                        <td align="right">
                            <?
                                echo number_format($cm_cost,4); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <th width="30">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="180">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
                
              
			</table>
        </div>	
	</fieldset>  
<?
exit();
}
	?>

