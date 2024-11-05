<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gmts Shipment Schedule Report po and style wise
Functionality	:
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	25/10/2018
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id = $_SESSION['logic_erp']['user_id'];
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where file_type=1",'master_tble_id','image_location');
$commission_for_shipment_schedule_arr=return_library_array( "select job_no,commission from  wo_pre_cost_dtls",'job_no','commission');
$country_name_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
$lib_buyer_season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($choosenCompany)  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_location")
{	
	$data=str_replace("'", "", $data);
	echo create_drop_down( "cbo_location_name", 125, "SELECT id,location_name from lib_location where company_id in($data) and  status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );  
	
	exit();
}
if($action=="company_wise_report_button_setting")
{

	extract($_REQUEST);
	
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name in('".$data."')  and module_id=11 and report_id=120 and is_deleted=0 and status_active=1");
	//echo $print_report_format;
	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#search').hide();\n";
	echo "$('#search1').hide();\n";
	

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==195){echo "$('#search').show();\n";}
			if($id==222){echo "$('#search1').show();\n";}
			
		}
	}
	else
	{
		echo "$('#search').show();\n";
		echo "$('#search1').show();\n";
		
	}
	exit();

}
if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;
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
                    <th id="search_by_td_up" width="170">Please Enter <? if($type==4) echo "Booking No";else echo "Job No";?></th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		if($type==4) 
							{
								//echo $type.'A';
								$type_cond=4;
								$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No",4=>"Booking No");
							}
							else 
							{
								$type_cond="";
								$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							}
							
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $type_cond,$dd,0 );//txt_job_no
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'weekly_capacity_and_order_booking_status_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$type_id=$data[5];
	$job=$data[6];
	
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	if($job!='') $job_cond="and a.job_no_prefix_num='$job'";else $job_cond="";
	$search_by=$data[2];
	//$search_string="%".trim($data[3])."%";
	$search_value=$data[3];
	//if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	if($search_by==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_by==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	else if($search_by==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
	}
	/*else if($search_by==4 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
	}*/
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
	if($type_id==4) 
	{
		if($search_by==4 && $search_value!=''){
		$search_con=" and c.booking_no like('%$search_value%')";	
		}
		if($search_value=="" &&  $search_by==4)
		{
			if($data[1]==0) 
			{ 
				echo "Please select Buyer";die;
			}
		}
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1)
	{
		$field_type="id,job_no_prefix_num";
	}
	else if($type_id==2)
	{
		$field_type="id,style_ref_no";
	}
	else if($type_id==3)
	{
		$field_type="id,po_number";
	}
	else if($type_id==4)
	{
		$field_type="id,booking_no";
	}
	  if($type_id==4)
	  {
		//  echo  $type_id;die;
		   $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.pub_shipment_date,c.booking_no, $year_field from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c where  b.job_no_mst=a.job_no and c.po_break_down_id=b.id  and c.job_no=a.job_no and c.booking_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) $search_con $buyer_id_cond $year_cond $job_cond group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.pub_shipment_date,c.booking_no,a.insert_date  order by a.job_no";
		   echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Booking No,Year,Style Ref.,PO No,Ship Date", "120,130,80,110,50,120,80","860","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,booking_no,year,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,0,3','') ;
	  }
	  else
	  {
	 $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.pub_shipment_date, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) $search_con $buyer_id_cond $year_cond $job_cond  order by a.job_no";
	 echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,Ship Date", "120,130,80,50,120,80","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','') ;
	  }
	
	//echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,Ship Date", "120,130,80,50,120,80","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','') ;
	exit(); 
} // Job Search end

if($action=="report_generate")
{

	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed"); 
	$data=explode("_",$data);
	// print_r($data);die;
	// GETTING DATA
	$company_name=$data[0];
	$working_company_name=$data[1];
	$buyer_name=$data[2];
	$location_name=$data[3];
	$style_ref=$data[4];
	$job_no=$data[5];
	$po_no=$data[6];
	$country_name=$data[7];
	$start_date=$data[8];
	$end_date=$data[9];
	$cbo_year=$data[10];
	$shiping_status=$data[11];
	$order_status=$data[12];
	$booking_no=str_replace("'","",$data[13]);
	$type=str_replace("'","",$data[14]);
	//echo $type.' helal';die;

	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}

	// QUERY CONDITION
	$company_cond="";
	$working_company_cond="";
	$location_cond="";
	$buyer_cond="";
	$style_cond ="";
	$job_cond="";
	$po_cond="";
	$country_cond="";
	$date_cond="";
	$shiping_cond="";
	$order_cond="";

	if($company_name!=0) $company_cond.=" and a.company_name in($company_name)";		
	if($working_company_name!=0) $working_company_cond.=" and d.serving_company in($working_company_name)";		
	if($location_name !=0) $location_cond.=" and d.location in($location_name)"; 
	if($buyer_name !=0) $buyer_cond.=" and a.buyer_name in($buyer_name)"; 
	if($style_ref !="") $style_cond.=" and a.style_ref_no ='".trim($style_ref)."'"; 
	if($job_no !="") $job_cond.=" and a.job_no_prefix_num in($job_no)"; 
	if($po_no !="") $po_cond.=" and b.po_number in($po_no)"; 
	if($country_name !=0) $country_cond.=" and c.country_id in($country_name)"; 
	if($shiping_status !=0) $shiping_cond.=" and b.shiping_status in($shiping_status)"; 
	if($order_status !=0) $order_cond.=" and b.is_confirmed in($order_status)";
	if($start_date!="" && $end_date!="") $date_cond.=" and c.country_ship_date between '$start_date' and  '$end_date'"; 


	if ($start_date!="" && $end_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($start_date));
		$ey = date('Y',strtotime($end_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
		 $year.= $sy+$i.",";
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="")
			{
			$tot_year.=",".$ey;
			}
			else
			{
			$tot_year.=$ey;
			}
		}
		$year_cond="and a.year_id in($tot_year)";
	}


	
	if($type==1)
	{

		// ========================= GETTING WEEK =======================
		$week_for_arr=array();
		$no_of_week_for_header=array();
		$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$start_date' and  '$end_date' order by week_date asc");
		$week_check_head=array();
		$sl =1;
		foreach ($sql_week_header as $row_week_header)
		{
			if($week_check_head[$row_week_header[csf("week")]]=='')
			{
				$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
				$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
			}
			$tmp=add_date($row_week_header[csf("week_date")],0);
			if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
			else $tmp_cond=date("d-M-y",strtotime($tmp));
						
			$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
			$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
			$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$sl++;
		}
		// print_r($no_of_week_for_header_calc);
		unset($sql_week_header);

		// =============================== FOR FAB. BOOKING NO ===================================	
		//$po_cond_booking = str_replace("d.po_break_down_id", "a.po_break_down_id", $po_ids_cond);
		//echo $po_cond_booking.'D';
		$wo_color_data_arr=array();
		$week_wise_order_qty=array();
		$po_id_arr=array();
		$week_wise_order_qty_arr=array();
		$wo_color_data_arr2=array();$all_po_id="";
		$po_cond_for_week_po_qty = str_replace("d.po_break_down_id", "b.id", $po_ids_cond);
		$sql_data_c="SELECT b.id AS po_id,c.country_id, c.country_ship_date,c.shiping_status,b.is_confirmed,a.bh_merchant, SUM (c.order_quantity) AS order_quantity,SUM (c.plan_cut_qnty) AS plan_cut_qnty,c.country_ship_date AS conf_ship_date
			FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  c.job_no_mst=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0  $company_cond $working_company_cond $buyer_cond $location_cond $style_cond $job_cond $po_cond $country_cond $date_cond  
			Group by b.id, b.is_confirmed,a.bh_merchant, c.country_id,c.country_ship_date,c.shiping_status";//$po_cond_for_week_po_qty
		$sql_data_c_r = sql_select($sql_data_c);
		// $sql_result_c=sql_select($sql_data_c);
			foreach($sql_data_c_r as $rowc)
			{
				
					if($rowc[csf('is_confirmed')]==1 || $rowc[csf('is_confirmed')]==2) //Confirm/Project
					{
						if($db_type==0) $date_week_cond=date("d-m-y",strtotime($rowc[csf('conf_ship_date')]));
						else $date_week_cond=date("d-M-y",strtotime($rowc[csf('conf_ship_date')]));
						//echo  $rowc[csf("plan_cut_qnty")].'X'.$no_of_week_for_header_calc[$date_week_cond].'<br>';
						$wo_color_data_arr2[$rowc[csf('po_id')]]['shipdate'].=$rowc[csf('conf_ship_date')].',';
						
						$po_wise_qty_arr[$rowc[csf("po_id")]]['order_quantity']+= $rowc[csf("order_quantity")];
						$po_wise_qty_arr[$rowc[csf("po_id")]]['plan_cut_qnty']+= $rowc[csf("plan_cut_qnty")];
						
						$week_wise_order_qty_arr['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]] += $rowc[csf("order_quantity")];
						
						$week_wise_order_qty_arr_t['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header[$date_week_cond]] += $rowc[csf("order_quantity")]; 
					 //echo $date_week_cond.'='.$fabric_qty_req.'<br>';
						//$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$rowc[csf("po_id")]])+array_sum($fabric_qty_arr['woven']['finish'][$rowc[csf("po_id")]]);
						//$week_wise_fab_req_qty_arr['fab_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]] += $fabric_qty_req;
						$week_wise_plan_qty_arr['plan_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]] += $rowc[csf("plan_cut_qnty")];
					}
					//$booking_po_id_arr[$rowc[csf('po_break_down_id')]] = $rowc[csf('po_break_down_id')];
					 $all_po_id.= $rowc[csf('po_id')].',';
			}
			$all_po_id=rtrim($all_po_id,',');
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" a.po_break_down_id in($ids) or"; 
					$po_cond_for_in2.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
			}
			else
			{
				$poIds=implode(",",array_unique(explode(",",$all_po_id)));
				$po_cond_for_in=" and a.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and b.id in($poIds)";
			}
			
				//print_r($week_wise_plan_qty_arr);
			/*echo "<pre>";
			print_r($week_wise_order_qty_arr_t);
			echo "</pre>";*/
		
		$poCond2 = str_replace("d.po_break_down_id", "a.po_break_down_id", $po_ids_cond);
		if($db_type==2) $po_concat="LISTAGG(CAST(a.po_break_down_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.po_break_down_id) as po_id";
		else $po_concat="group_concat(a.po_break_down_id) as po_id";
		
		
		$booking_no_cond="";$po_no_cond="";
		if($booking_no!="") 
		{
			$booking_no_cond="and a.booking_no like '%$booking_no%' ";
			$poids = return_field_value("$po_concat", "wo_booking_dtls a", "a.status_active =1 $booking_no_cond","po_id");
			$poids = implode(",", array_unique(explode(",", $poids)));
			//echo "select $po_concat from  wo_booking_dtls a where a.status_active =1 $booking_no_cond";
			$po_no_cond="and a.po_break_down_id in($poids)";
		}
		//echo $poids.'DDDD';
		
		
		$booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id ,c.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown c  where c.id=a.color_size_table_id  and  a.status_active=1 and a.booking_type=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $booking_no_cond $country_cond  $po_cond_for_in   ");
		//echo "SELECT a.po_break_down_id ,c.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown c  where c.id=a.color_size_table_id  and  a.status_active=1 and a.booking_type=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $booking_no_cond $country_cond  $po_cond_for_in   ";
		
		foreach($booking_sql as $vals) 
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]]["booking_no"]=$vals[csf("booking_no")];
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];
			$booking_po_id_arr[$vals[csf('po_break_down_id')]] = $vals[csf('po_break_down_id')];
		}
		
		if($booking_no!="")
		{
			$booking_po_ids =trim(implode(',', $booking_po_id_arr),",");
			if($booking_po_ids!="") $booking_po_ids_cond="and b.id in($booking_po_ids)";else  $booking_po_ids_cond="";
		}
		
		// ====================== MAIN QUERY ==============================
		$date=date('d-m-Y');
		$sql = "SELECT a.job_no_prefix_num,a.job_no, a.style_ref_no,a.total_set_qnty,b.id as po_break_down_id,a.order_uom,sum(b.po_quantity) as po_quantity,sum(b.po_quantity*total_set_qnty) as po_quantity_pcs,sum(c.plan_cut_qnty) as plan_cut_qnty,sum(c.order_quantity) as order_quantity,b.unit_price,b.po_number,b.update_date,b.po_received_date,c.country_ship_date,b.is_confirmed, c.country_id,b.shiping_status
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $location_cond $style_cond $job_cond $po_cond $country_cond $date_cond $shiping_cond $order_cond $booking_po_ids_cond 
		GROUP BY a.job_no_prefix_num, a.job_no,a.style_ref_no,a.total_set_qnty,b.id,a.order_uom,b.unit_price,b.po_number,b.update_date,b.po_received_date,c.country_ship_date,b.is_confirmed,c.country_id,b.shiping_status
		ORDER BY b.update_date,b.po_received_date";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		$main_array = [];
		$po_id_arr = [];
		foreach($sql_res as $key=>$val)
		{
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_number'] = $val[csf('po_number')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['job_no'] = $val[csf('job_no_prefix_num')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['job_no_full'] = $val[csf('job_no')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['sewing_line'] = $val[csf('sewing_line')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_quantity'] = $val[csf('po_quantity')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_quantity_pcs'] += $val[csf('order_quantity')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['plan_cut_qnty'] += $val[csf('plan_cut_qnty')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['unit_price'] = $val[csf('unit_price')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['update_date'] = $val[csf('update_date')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_received_date'] = $val[csf('po_received_date')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['is_confirmed'] = $val[csf('is_confirmed')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['order_uom'] = $val[csf('order_uom')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['total_set_qnty'] = $val[csf('total_set_qnty')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['shiping_status'] = $val[csf('shiping_status')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['order_uom'] = $val[csf('order_uom')];
			$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			
		}
		
		$po_ids =trim(implode(',', $po_id_arr),",");
		// ========================= FOR PO ID ARRAY ==========================
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";	$po_ids_cond2= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( d.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or   d.po_break_down_id in ($imp_ids) ";
	     		}

	     	}
	     	 $po_ids_cond.=" )";

		}
		else
		{
			$po_ids_cond= " and d.po_break_down_id in($po_ids) ";
		}
		
		
	     // print_r($po_ids);die();
		 $condition= new condition();
			$condition->company_name("in($company_name)");
		  if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
		 if(str_replace("'","",$job_no) !=''){
					  $condition->job_no_prefix_num("='$job_no'");
			}
		 if(str_replace("'","",$po_no)!='')
		 {
			$condition->po_number("='$po_no'"); 
		 }
		 if(str_replace("'","",$style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			$condition->style_ref_no("='$style_ref'");
		 }
		  if(str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
					  $condition->country_ship_date(" between '$start_date' and '$end_date'");
		 }
				 
		$condition->init();
		$fabric= new fabric($condition);
		//echo $fabric->getQuery(); die;
		$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$costPerArr=$condition->getCostingPerArr();
		

	     // =============================== FOR PO QUANTITY ==========================
	     $poCond = str_replace("d.po_break_down_id", "b.id", $po_ids_cond);
	    $po_sql = "SELECT b.id AS po_break_down_id, SUM (b.po_quantity) AS po_quantity
	    FROM wo_po_break_down b
	  	WHERE b.status_active = 1 AND b.is_deleted = 0 $poCond
	   	GROUP BY b.id";
	   	$poQntyArr = array();
	   	$po_sql_res = sql_select($po_sql);
	   	foreach ($po_sql_res as $key => $val) 
	   	{
	   		$poQntyArr[$val[csf('po_break_down_id')]] = $val[csf('po_quantity')];
	   	}
		// ======================= PRODUCTION QUERY ===================================
		$prod_sql = "SELECT d.prod_reso_allo,d.sewing_line,d.po_break_down_id,
		sum(case when d.production_type=1 and e.production_type=1 then e.production_qnty else 0 end ) as cutting_qty,
		sum(case when d.production_type in(5) and e.production_type in(5) then e.production_qnty else 0 end ) as sewing_qty,
		sum(case when d.production_type=8 and e.production_type=8 then e.production_qnty else 0 end ) as finish_qty
		FROM pro_garments_production_mst d,pro_garments_production_dtls e
		WHERE  d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 $po_ids_cond
		GROUP BY d.prod_reso_allo,d.sewing_line,d.po_break_down_id
		";
		// echo $sql;die();
		$prod_sql_res = sql_select($prod_sql);
		$prod_array = [];
		foreach($prod_sql_res as $key=>$val)
		{		
			if($val[csf("prod_reso_allo")]==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
				$prod_array[$val[csf('po_break_down_id')]]['sewing_line'] = $line_name;
			}
			else
			{
				$prod_array[$val[csf('po_break_down_id')]]['sewing_line'] = $val[csf('sewing_line')];
			}
			
			$prod_array[$val[csf('po_break_down_id')]]['cutting_qty'] += $val[csf('cutting_qty')];
			$prod_array[$val[csf('po_break_down_id')]]['sewing_qty'] += $val[csf('sewing_qty')];
			$prod_array[$val[csf('po_break_down_id')]]['finish_qty'] += $val[csf('finish_qty')];
		}
		// echo "<pre>";
		// print_r($prod_array);
		
		// ======================================== WEEK DATE ========================================
		$week_start_day=array();
		$week_end_day=array();
		$week_day_arr=array();
		
		$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$start_date' and  '$end_date' group by week");
		foreach ($sql_week_start_end_date as $row_week)
		{
			$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
			$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
			//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
			$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
		}
		unset($sql_week_start_end_date);
		
		// ===================== FOR EX FACTORY ==========================
		$exfactory_data_array=array();
		$po_cond_for_ex = str_replace("d.po_break_down_id", "po_break_down_id", $po_ids_cond);
		$exfactory_data=sql_select("SELECT po_break_down_id,country_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where status_active=1 and is_deleted=0 $po_cond_for_ex group by po_break_down_id,country_id");
		foreach($exfactory_data as $exfatory_row)
		{	
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_qnty'] += ($exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')]);
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_date']=$exfatory_row[csf('ex_factory_date')];
		}
		// ============================ WEEK WISE PO QUANTITY =====================

		// ======================================== FOR FINISH FAB. RECEIVE ======================================
		$po_cond_for_fin_fab = str_replace("d.po_break_down_id", "b.order_id", $po_ids_cond);	
		$buyer_cond_for_fin_fab = str_replace("a.buyer_name", "b.buyer_id", $buyer_cond);	
		
		$fab_sql = "SELECT po_breakdown_id, quantity FROM order_wise_pro_details where trans_type=1 and entry_form in (2,22,58) and status_active=1 and is_deleted=0 and trans_id!=0";
		$fab_sql_res = sql_select($fab_sql);
		$rcv_qty_arr = [];
		foreach ($fab_sql_res as $key => $val) 
		{
			$rcv_qty_arr[$val[(csf('po_breakdown_id'))]]['qnty'] += $val[(csf('quantity'))];
		}
		
					
		ob_start();
		?>
		<style type="text/css">

	        .block_div { 
	            width:auto;
	            height:auto;
	            text-wrap:normal;
	            vertical-align:bottom;
	            display: block;
	            position: !important; 
	            -webkit-transform: rotate(-90deg);
	            -moz-transform: rotate(-90deg);
	        }
	        hr {
	            border: 0; 
	            background-color: #000;
	            height: 1px;
	        }  
	        .gd-color
	        {
				background: #f0f9ff; /* Old browsers */
				background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
			}
			.gd-color2
			{
				background: rgb(247,251,252); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
				font-weight: bold;
				background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				border: 1px solid #8DAFDA;
				color: #444;
			}
			.gd-color2 td
			{
				border: 1px solid #777;
				text-align: right;
			}
			.gd-color3
			{
				background: rgb(254,255,255); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
				border: 1px solid #dccdcd;
				font-weight: bold;
			}

   		 </style>
		<?
	
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}	
		// echo $rowcount;
		$tbl_width = 2040+($rowcount*160);
		?>
		<div>
			
			<table width="<? echo $tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr>
						
						<th align="center" rowspan="2" width="20">SI</th>
						<th align="center" rowspan="2" width="120">Order No.</th>
						<th align="center" rowspan="2" width="120">Style</th>
						<th align="center" rowspan="2" width="80">Order Update Date</th>
						<th align="center" rowspan="2" width="80">Job No.</th>
						<th align="center" rowspan="2" width="100">Fab. Booking No.</th>
						<th align="center" rowspan="2" width="80">Total Order Qnty</th>
						<th align="center" rowspan="2" width="80">UOM</th>
	                    <th align="center" rowspan="2" width="80">Order Qty Pcs</th>
						<th align="center" rowspan="2" width="80">Unit Price</th>
						<th align="center" rowspan="2" width="80">Total FOB Val.</th>
						<th align="center" rowspan="2" width="80">Fabric con/pcs</th>
						<th align="center" rowspan="2" width="80">OPD</th>
						<th align="center" colspan="3">Fabric</th>					
						<th width="80" align="center" rowspan="2">Line No.</th>
						<th align="center" colspan="5">Production & Shipment</th>
						<th align="center" width="80" rowspan="2">Shiping Status</th>
						<th colspan="<? echo $rowcount*2; ?>" align="center">Weekly Order Qty.</th>
						<th align="center" width="80" rowspan="2">Total</th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Fab. Req. Qty.</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Rcvd.</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Rcvd. Bal.</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Cutting</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing Out</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Finishing</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Ex-Factory</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Inhand</th>
						<?
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								$start_week_date=$week_start_day[$week_key][week_start_day];
								$end_week_date=$week_end_day[$week_key][week_end_day];
								if($db_type==2)
								{
									$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
									$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
								}
								else
								{
									$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
									$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
								}
								$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
								$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
								?>
								<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="80"  align="center">
								<? 
								echo 'W'.$week_key.'<br/>'.$weekstart_date;
								?>
								</th>
	                            <th title="From Budget, <? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="80"  align="center">
								<? 
									echo 'Fab Req Qty';
								?>
								</th>
								<?
							}
						}
	            		?>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px" id="scroll_body">
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_filter" >
					<?
					// print_r($week_for_header);
					$i=1;
					$jj=1;
					$gr_po_qty = 0;
					$gr_fob_qty = 0;
					$gr_fab_req = 0;
					$gr_fab_tot_rcv = 0;
					$gr_fab_rcv_bal = 0;
					$gr_cutting = 0;
					$gr_sewing = 0;
					$gr_finish = 0;
					$gr_ex_fact = 0;
					$gr_inhand = 0;				

					foreach ($main_array as $style_id => $style_data) 
					{
						$style_wise_po_qty = 0;
						$style_wise_fob_qty = 0;
						$style_wise_fab_req = 0;
						$style_wise_fab_tot_rcv = 0;
						$style_wise_fab_rcv_bal = 0;
						$style_wise_cutting = 0;
						$style_wise_sewing = 0;
						$style_wise_finish = 0;
						$style_wise_ex_fact = 0;
						$style_wise_inhand = 0;
						foreach ($style_data as $po_id => $row) 
						{
							$conf_country_ship_date=rtrim($wo_color_data_arr2[$po_id]['shipdate'],',');
							$conf_country_ship_date=array_unique(explode(",",$conf_country_ship_date));
							$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$po_id])+array_sum($fabric_qty_arr['woven']['finish'][$po_id]);
							$costPerQty=$costPerArr[$row['job_no_full']];
							$fabric_qty_req_cons=($fabric_qty_req/$row['plan_cut_qnty']);//*$costPerQty;
							//echo $costPerQty.'DD';
							// print_r($conf_country_ship_date); 

							if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
								<td style="word-wrap: break-word;word-break: break-all;" width="20"><? echo $i;?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $row['po_number']; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $style_id; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo ($row['update_date'] != "") ? change_date_format($row['update_date']) : ""; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['job_no']; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $booking_no_fin_qnty_array[$po_id]['booking_no'];?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="PO Wise Qnty,po no=<? echo $po_id;?>">
									<?
									$poQnty = $poQntyArr[$po_id];
									// $poQnty=($row['order_uom'] == 58) ? $row['po_quantity']*$row['total_set_qnty'] : $row['po_quantity'];
									 echo number_format($poQnty,0); 
									?>									
									</td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $unit_of_measurement[$row['order_uom']];?></td>
	                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['po_quantity_pcs'];?></td>
	                            
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
									<? 
									$unitPrice = ($row['order_uom'] == 58) ? $row['unit_price']/2 : $row['unit_price'];
									echo $unitPrice; 
									?>									
									</td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $fob_val = ($poQnty * $unitPrice); echo number_format($fob_val,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="Cons Qty=Req Qty <? echo $fabric_qty_req.'/Plan Cut('.$row['plan_cut_qnty'].')';?>">
								<? echo number_format($fabric_qty_req_cons,2);//$fabric_qty_req.'='.$row['plan_cut_qnty'];
								
								?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo ($row['po_received_date'] != "") ? change_date_format($row['po_received_date']) : ""; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $bo_qnty = $booking_no_fin_qnty_array[$po_id]['qnty']; echo number_format($bo_qnty,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $rcv_qnty = $rcv_qty_arr[$po_id]['qnty']; echo number_format($rcv_qnty,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $bal = $bo_qnty - $rcv_qnty;echo number_format(($bal)); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $prod_array[$po_id]['sewing_line'];?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'1','cutting_sewing_action');" ><? echo number_format($prod_array[$po_id]['cutting_qty'],0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'5','cutting_sewing_action');"><? echo number_format($prod_array[$po_id]['sewing_qty'],0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'8','cutting_sewing_action');"><? $fin_qty = $prod_array[$po_id]['finish_qty']; echo number_format($fin_qty,0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'1','total_exfac_action');"><? $ex_data = $exfactory_data_array[$po_id]['ex_factory_qnty']; echo number_format($ex_data,0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $inhand_qty = $fin_qty - $ex_data; echo number_format($inhand_qty,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo $ship_status_arr[$row['shiping_status']];?></td>
								<?
								$qnty_array=array();$fab_qnty_array=array();$plan_qnty_array=array();
								foreach($conf_country_ship_date as $c_date)
								{
										if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
										else $c_date_con=date("d-M-y",strtotime($c_date));
										//echo $no_of_week_for_header_calc[$c_date_con].'X';
									$qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty_arr['po_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									$plan_qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_plan_qty_arr['plan_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									//$fab_qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_fab_req_qty_arr['fab_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									//echo $week_wise_plan_qty_arr['plan_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]].'-G-';
								}							
								
								// print_r($qnty_array); die;
								$week_qty=0;$fab_week_qty=0;
								$week_check=array();
								$v_total = 0;$fab_v_total = 0;
			                   	foreach($week_counter_header as $mon_key=>$week)
								{
									
									foreach($week as $week_key)
									{
									?>
									<td  style="word-wrap:break-word; word-break: break-all;" width="80" align="right">
									<? 
										 	$week_plan_qty=$plan_qnty_array[$week_key];
											$week_qty=$qnty_array[$week_key];
											if($week_qty>0) echo number_format($week_qty,0);else echo ' ';
											$tot_week_qty[$mon_key][$week_key]+=$week_qty;									
											$tot_style_wise_week_qty[$style_id][$mon_key][$week_key]+=$week_qty;	
											$v_total +=	$week_qty;	
											$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$po_id])+array_sum($fabric_qty_arr['woven']['finish'][$po_id]);	
											//$po_wise_qty=$po_wise_qty_arr[$po_id]['order_quantity'];
											$po_wise_qty=$po_wise_qty_arr[$po_id]['plan_cut_qnty'];					
									?>
									</td>
	                                <td  style="word-wrap:break-word; word-break: break-all;" title="Avg Req Qty=Req Qty(<? echo number_format($fabric_qty_req,2);?>)/Tot Plan Qty(<? echo number_format($po_wise_qty,2);?>)*Week Plan Qty(<? echo number_format($week_plan_qty,2);?>)" width="80" align="right">
									<? 
											$avg_fab_week_qty=($fabric_qty_req/$po_wise_qty)*$week_plan_qty;
											//$fab_week_qty=$fab_qnty_array[$week_key];
											if($avg_fab_week_qty>0) echo number_format($avg_fab_week_qty,0);else echo ' ';
											$tot_fab_week_qty[$mon_key][$week_key]+=$avg_fab_week_qty;									
											$tot_style_wise_fab_week_qty[$style_id][$mon_key][$week_key]+=$avg_fab_week_qty;	
											$fab_v_total +=	$avg_fab_week_qty;							
									?>
									</td>
									<?
									}
									?>
									<?
								}
								?>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo $v_total; ?></td>
							</tr>
							<?
							$i++;
							$jj++;
							
							$style_wise_po_qty += $poQnty;
							$style_wise_fob_qty += $fob_val;
							$style_wise_fab_req += $bo_qnty;
							$style_wise_fab_tot_rcv += $rcv_qnty;
							$style_wise_fab_rcv_bal += $bal;
							$style_wise_cutting += $prod_array[$po_id]['cutting_qty'];
							$style_wise_sewing += $prod_array[$po_id]['sewing_qty'];
							$style_wise_finish += $fin_qty;
							$style_wise_ex_fact += $ex_data;
							$style_wise_inhand += $inhand_qty;

							$gr_po_qty += $poQnty;
							$gr_fob_qty += $fob_val;
							$gr_fab_req += $bo_qnty;
							$gr_fab_tot_rcv += $rcv_qnty;
							$gr_fab_rcv_bal += $bal;
							$gr_cutting += $prod_array[$po_id]['cutting_qty'];
							$gr_sewing += $prod_array[$po_id]['sewing_qty'];
							$gr_finish += $fin_qty;
							$gr_ex_fact += $ex_data;
							$gr_inhand += $inhand_qty;
						}

						?>
						<tr bgcolor="#CCCCCC">
							<td style="word-wrap: break-word;word-break: break-all;" width="20"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="120" align="right">Style Wise Total</td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_po_qty,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	                        <td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fob_qty,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fab_req,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fab_tot_rcv,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fab_rcv_bal,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_cutting,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_sewing,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_finish,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_ex_fact,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_inhand,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"></td>
							<?					
							$style_wise_total_week_qty=0; $style_wise_total_fab_week_qty=0; 
							$style_v_total = 0;$style_v_fab_total = 0;
							foreach($week_counter_header as $mon_key=>$week)
							{
								
								foreach($week as $week_key)
								{
								?>
								<td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
								<? 
								$style_wise_total_week_qty=$tot_style_wise_week_qty[$style_id][$mon_key][$week_key];
								echo number_format($style_wise_total_week_qty,0);
								$style_v_total += $style_wise_total_week_qty;
								?>
								</td>
	                            <td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
								<? 
								$style_wise_total_fab_week_qty=$tot_style_wise_fab_week_qty[$style_id][$mon_key][$week_key];
								echo number_format($style_wise_total_fab_week_qty,0);
								$style_v_fab_total += $style_wise_total_fab_week_qty;
								?>
								</td>
								<?
								}
								?>	
								<?
							}
			            	?>						
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_v_total,0); ?></td>			
						</tr>
						<?
					}
					?>
				</table>
			</div>
			<table  border="0" class=""  width="<? echo $tbl_width;?>" rules="all" id="" cellpadding="0" cellspacing="0">
				<tr bgcolor="#CCCCCC">
					<td style="word-wrap: break-word;word-break: break-all;" width="20"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="120" align="right">Grand Total</td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_po_qty,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fob_qty,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fab_req,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fab_tot_rcv,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fab_rcv_bal,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_cutting,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_sewing,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_finish,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_ex_fact,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_inhand,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"></td>
					<?					
					$total_week_qty=0;$total_fab_week_qty=0; $total_week_amt=0; 
					$gr_v_total = 0;
					foreach($week_counter_header as $mon_key=>$week)
					{
						
						foreach($week as $week_key)
						{
						?>
						<td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$gr_v_total += $total_week_qty;
						?>
						</td>
	                    <td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
						<? 
						$total_fab_week_qty=$tot_fab_week_qty[$mon_key][$week_key];
						echo number_format($total_fab_week_qty,0);
						$tot_mon_fab_qty[$mon_key]+=$total_fab_week_qty;
						$gr_v_fab_total += $total_fab_week_qty;
						?>
						</td>
						<?
						$w++;
						}
						?>
						<?
					}
	            	?>						
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_v_total,0); ?></td>
				</tr>	
			</table>
		</div>
		<?
	}
	else
	{
		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer order by id","id","buyer_name"); 
		
		// ========================= GETTING WEEK =======================
		$week_for_arr=array();
		$no_of_week_for_header=array();
		$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$start_date' and  '$end_date' order by week_date asc");
		$week_check_head=array();
		$sl =1;
		foreach ($sql_week_header as $row_week_header)
		{
			if($week_check_head[$row_week_header[csf("week")]]=='')
			{
				$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
				$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
			}
			$tmp=add_date($row_week_header[csf("week_date")],0);
			if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
			else $tmp_cond=date("d-M-y",strtotime($tmp));
						
			$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
			$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
			$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$sl++;
		}
		// print_r($no_of_week_for_header_calc);
		unset($sql_week_header);

		// =============================== FOR FAB. BOOKING NO ===================================	
		//$po_cond_booking = str_replace("d.po_break_down_id", "a.po_break_down_id", $po_ids_cond);
		//echo $po_cond_booking.'D';
		$wo_color_data_arr=array();
		$week_wise_order_qty=array();
		$po_id_arr=array();
		$week_wise_order_qty_arr=array();
		$wo_color_data_arr2=array();$all_po_id="";
		$po_cond_for_week_po_qty = str_replace("d.po_break_down_id", "b.id", $po_ids_cond);
		$sql_data_c="SELECT b.id AS po_id,c.country_id, c.country_ship_date,c.shiping_status,b.is_confirmed,a.bh_merchant, SUM (c.order_quantity) AS order_quantity,SUM (c.plan_cut_qnty) AS plan_cut_qnty,c.country_ship_date AS conf_ship_date
			FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  c.job_no_mst=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0  $company_cond $working_company_cond $buyer_cond $location_cond $style_cond $job_cond $po_cond $country_cond $date_cond  
			Group by b.id, b.is_confirmed,a.bh_merchant, c.country_id,c.country_ship_date,c.shiping_status";//$po_cond_for_week_po_qty
			//echo $sql_data_c; die;
		$sql_data_c_r = sql_select($sql_data_c);
		// $sql_result_c=sql_select($sql_data_c);
			foreach($sql_data_c_r as $rowc)
			{
				
					if($rowc[csf('is_confirmed')]==1 || $rowc[csf('is_confirmed')]==2) //Confirm/Project
					{
						if($db_type==0) $date_week_cond=date("d-m-y",strtotime($rowc[csf('conf_ship_date')]));
						else $date_week_cond=date("d-M-y",strtotime($rowc[csf('conf_ship_date')]));
						//echo  $rowc[csf("plan_cut_qnty")].'X'.$no_of_week_for_header_calc[$date_week_cond].'<br>';
						$wo_color_data_arr2[$rowc[csf('po_id')]]['shipdate'].=$rowc[csf('conf_ship_date')].',';
						
						$po_wise_qty_arr[$rowc[csf("po_id")]]['order_quantity']+= $rowc[csf("order_quantity")];
						$po_wise_qty_arr[$rowc[csf("po_id")]]['plan_cut_qnty']+= $rowc[csf("plan_cut_qnty")];
						
						$week_wise_order_qty_arr['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]] += $rowc[csf("order_quantity")];
						
						$week_wise_order_qty_arr_t['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header[$date_week_cond]] += $rowc[csf("order_quantity")]; 
					 //echo $date_week_cond.'='.$fabric_qty_req.'<br>';
						//$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$rowc[csf("po_id")]])+array_sum($fabric_qty_arr['woven']['finish'][$rowc[csf("po_id")]]);
						//$week_wise_fab_req_qty_arr['fab_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]] += $fabric_qty_req;
						$week_wise_plan_qty_arr['plan_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]] += $rowc[csf("plan_cut_qnty")];
					}
					//$booking_po_id_arr[$rowc[csf('po_break_down_id')]] = $rowc[csf('po_break_down_id')];
					 $all_po_id.= $rowc[csf('po_id')].',';
			}
			$all_po_id=rtrim($all_po_id,',');
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";
			$po_ids=count(array_unique(explode(",",$all_po_id)));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" a.po_break_down_id in($ids) or"; 
					$po_cond_for_in2.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
			}
			else
			{
				$poIds=implode(",",array_unique(explode(",",$all_po_id)));
				$po_cond_for_in=" and a.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and b.id in($poIds)";
			}
			
				//print_r($week_wise_plan_qty_arr);
			/*echo "<pre>";
			print_r($week_wise_order_qty_arr_t);
			echo "</pre>";*/
		
		$poCond2 = str_replace("d.po_break_down_id", "a.po_break_down_id", $po_ids_cond);
		if($db_type==2) $po_concat="LISTAGG(CAST(a.po_break_down_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY a.po_break_down_id) as po_id";
		else $po_concat="group_concat(a.po_break_down_id) as po_id";
		
		
		$booking_no_cond="";$po_no_cond="";
		if($booking_no!="") 
		{
			$booking_no_cond="and a.booking_no like '%$booking_no%' ";
			$poids = return_field_value("$po_concat", "wo_booking_dtls a", "a.status_active =1 $booking_no_cond","po_id");
			$poids = implode(",", array_unique(explode(",", $poids)));
			//echo "select $po_concat from  wo_booking_dtls a where a.status_active =1 $booking_no_cond";
			$po_no_cond="and a.po_break_down_id in($poids)";
		}
		//echo $poids.'DDDD';
		
		
		$booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id ,c.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown c  where c.id=a.color_size_table_id  and  a.status_active=1 and a.booking_type=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $booking_no_cond $country_cond  $po_cond_for_in   ");
		//echo "SELECT a.po_break_down_id ,c.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown c  where c.id=a.color_size_table_id  and  a.status_active=1 and a.booking_type=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $booking_no_cond $country_cond  $po_cond_for_in   ";
		
		foreach($booking_sql as $vals) 
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]]["booking_no"]=$vals[csf("booking_no")];
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];
			$booking_po_id_arr[$vals[csf('po_break_down_id')]] = $vals[csf('po_break_down_id')];
		}
		
		if($booking_no!="")
		{
			$booking_po_ids =trim(implode(',', $booking_po_id_arr),",");
			if($booking_po_ids!="") $booking_po_ids_cond="and b.id in($booking_po_ids)";else  $booking_po_ids_cond="";
		}
		
		// ====================== MAIN QUERY ==============================
		$date=date('d-m-Y');
		$sql = "SELECT a.job_no_prefix_num,a.job_no, a.style_ref_no,a.total_set_qnty,b.id as po_break_down_id,a.order_uom,sum(b.po_quantity) as po_quantity,sum(b.po_quantity*total_set_qnty) as po_quantity_pcs,sum(c.plan_cut_qnty) as plan_cut_qnty,sum(c.order_quantity) as order_quantity,b.unit_price,b.po_number,b.update_date,b.po_received_date,c.country_ship_date,b.is_confirmed, c.country_id,b.shiping_status,a.buyer_name
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $location_cond $style_cond $job_cond $po_cond $country_cond $date_cond $shiping_cond $order_cond $booking_po_ids_cond 
		GROUP BY a.job_no_prefix_num, a.job_no,a.style_ref_no,a.total_set_qnty,b.id,a.order_uom,b.unit_price,b.po_number,b.update_date,b.po_received_date,c.country_ship_date,b.is_confirmed,c.country_id,b.shiping_status,a.buyer_name
		ORDER BY b.update_date,b.po_received_date";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		$main_array = [];
		$po_id_arr = [];
		foreach($sql_res as $key=>$val)
		{
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_number'] = $val[csf('po_number')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['job_no'] = $val[csf('job_no_prefix_num')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['job_no_full'] = $val[csf('job_no')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['sewing_line'] = $val[csf('sewing_line')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_quantity'] = $val[csf('po_quantity')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_quantity_pcs'] += $val[csf('order_quantity')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['plan_cut_qnty'] += $val[csf('plan_cut_qnty')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['unit_price'] = $val[csf('unit_price')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['update_date'] = $val[csf('update_date')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['po_received_date'] = $val[csf('po_received_date')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['is_confirmed'] = $val[csf('is_confirmed')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['order_uom'] = $val[csf('order_uom')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['total_set_qnty'] = $val[csf('total_set_qnty')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['shiping_status'] = $val[csf('shiping_status')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['order_uom'] = $val[csf('order_uom')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['country_ship_date'] = $val[csf('country_ship_date')];
			$main_array[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]['buyer_name'] = $val[csf('buyer_name')];
			$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			
		}
		
		$po_ids =trim(implode(',', $po_id_arr),",");
		// ========================= FOR PO ID ARRAY ==========================
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";	$po_ids_cond2= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( d.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or   d.po_break_down_id in ($imp_ids) ";
	     		}

	     	}
	     	 $po_ids_cond.=" )";

		}
		else
		{
			$po_ids_cond= " and d.po_break_down_id in($po_ids) ";
		}
		
		
	     // print_r($po_ids);die();
		 $condition= new condition();
			$condition->company_name("in($company_name)");
		  if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
		 if(str_replace("'","",$job_no) !=''){
					  $condition->job_no_prefix_num("='$job_no'");
			}
		 if(str_replace("'","",$po_no)!='')
		 {
			$condition->po_number("='$po_no'"); 
		 }
		 if(str_replace("'","",$style_ref)!='')
		 {
			//echo "in($txt_order_id)".'dd';die;
			$condition->style_ref_no("='$style_ref'");
		 }
		  if(str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
					  $condition->country_ship_date(" between '$start_date' and '$end_date'");
		 }
				 
		$condition->init();
		$fabric= new fabric($condition);
		//echo $fabric->getQuery(); die;
		$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$costPerArr=$condition->getCostingPerArr();
		

	     // =============================== FOR PO QUANTITY ==========================
	     $poCond = str_replace("d.po_break_down_id", "b.id", $po_ids_cond);
	    $po_sql = "SELECT b.id AS po_break_down_id, SUM (b.po_quantity) AS po_quantity
	    FROM wo_po_break_down b
	  	WHERE b.status_active = 1 AND b.is_deleted = 0 $poCond
	   	GROUP BY b.id";
	   	$poQntyArr = array();
	   	$po_sql_res = sql_select($po_sql);
	   	foreach ($po_sql_res as $key => $val) 
	   	{
	   		$poQntyArr[$val[csf('po_break_down_id')]] = $val[csf('po_quantity')];
	   	}
		// ======================= PRODUCTION QUERY ===================================
		$prod_sql = "SELECT d.prod_reso_allo,d.sewing_line,d.po_break_down_id,
		sum(case when d.production_type=1 and e.production_type=1 then e.production_qnty else 0 end ) as cutting_qty,
		sum(case when d.production_type in(5) and e.production_type in(5) then e.production_qnty else 0 end ) as sewing_qty,
		sum(case when d.production_type=8 and e.production_type=8 then e.production_qnty else 0 end ) as finish_qty
		FROM pro_garments_production_mst d,pro_garments_production_dtls e
		WHERE  d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 $po_ids_cond
		GROUP BY d.prod_reso_allo,d.sewing_line,d.po_break_down_id
		";
		// echo $sql;die();
		$prod_sql_res = sql_select($prod_sql);
		$prod_array = [];
		foreach($prod_sql_res as $key=>$val)
		{		
			if($val[csf("prod_reso_allo")]==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
				$prod_array[$val[csf('po_break_down_id')]]['sewing_line'] = $line_name;
			}
			else
			{
				$prod_array[$val[csf('po_break_down_id')]]['sewing_line'] = $val[csf('sewing_line')];
			}
			
			$prod_array[$val[csf('po_break_down_id')]]['cutting_qty'] += $val[csf('cutting_qty')];
			$prod_array[$val[csf('po_break_down_id')]]['sewing_qty'] += $val[csf('sewing_qty')];
			$prod_array[$val[csf('po_break_down_id')]]['finish_qty'] += $val[csf('finish_qty')];
		}
		// echo "<pre>";
		// print_r($prod_array);
		
		// ======================================== WEEK DATE ========================================
		$week_start_day=array();
		$week_end_day=array();
		$week_day_arr=array();
		
		$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$start_date' and  '$end_date' group by week");
		foreach ($sql_week_start_end_date as $row_week)
		{
			$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
			$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
			//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
			$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
		}
		unset($sql_week_start_end_date);
		
		// ===================== FOR EX FACTORY ==========================
		$exfactory_data_array=array();
		$po_cond_for_ex = str_replace("d.po_break_down_id", "po_break_down_id", $po_ids_cond);
		$exfactory_data=sql_select("SELECT po_break_down_id,country_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where status_active=1 and is_deleted=0 $po_cond_for_ex group by po_break_down_id,country_id");
		foreach($exfactory_data as $exfatory_row)
		{	
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_qnty'] += ($exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')]);
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_date']=$exfatory_row[csf('ex_factory_date')];
		}
		// ============================ WEEK WISE PO QUANTITY =====================

		// ======================================== FOR FINISH FAB. RECEIVE ======================================
		$po_cond_for_fin_fab = str_replace("d.po_break_down_id", "b.order_id", $po_ids_cond);	
		$buyer_cond_for_fin_fab = str_replace("a.buyer_name", "b.buyer_id", $buyer_cond);	
		
		$fab_sql = "SELECT po_breakdown_id, quantity FROM order_wise_pro_details where trans_type=1 and entry_form in (2,22,58) and status_active=1 and is_deleted=0 and trans_id!=0";
		$fab_sql_res = sql_select($fab_sql);
		$rcv_qty_arr = [];
		foreach ($fab_sql_res as $key => $val) 
		{
			$rcv_qty_arr[$val[(csf('po_breakdown_id'))]]['qnty'] += $val[(csf('quantity'))];
		}
		
					
		ob_start();
		?>
		<style type="text/css">

	        .block_div { 
	            width:auto;
	            height:auto;
	            text-wrap:normal;
	            vertical-align:bottom;
	            display: block;
	            position: !important; 
	            -webkit-transform: rotate(-90deg);
	            -moz-transform: rotate(-90deg);
	        }
	        hr {
	            border: 0; 
	            background-color: #000;
	            height: 1px;
	        }  
	        .gd-color
	        {
				background: #f0f9ff; /* Old browsers */
				background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
			}
			.gd-color2
			{
				background: rgb(247,251,252); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
				font-weight: bold;
				background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				border: 1px solid #8DAFDA;
				color: #444;
			}
			.gd-color2 td
			{
				border: 1px solid #777;
				text-align: right;
			}
			.gd-color3
			{
				background: rgb(254,255,255); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
				border: 1px solid #dccdcd;
				font-weight: bold;
			}

    	</style>
		<?
		
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}	
		// echo $rowcount;
		$tbl_width = 2220+($rowcount*160);

		$total_po_quantity_pcs=0;
		$total_fab_req_qnty=0;
		$total_order_qnty_arr=array();
		$total_fab_req_qnty_arr=array();
			?>
		<div>
			<table width="" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_summary">
				<thead>
					<tr>
						<th rowspan="4">Sl No</th>
						<th rowspan="4">Buyer</th>
						<th rowspan="4">Order Qty Pcs</th>
						<th rowspan="4">Fabric con/pcs</th>
						<th rowspan="4">Fab Req Qty</th>
						<th colspan="<? echo $rowcount*2; ?>" align="center">Fab Req Qty</th>
					</tr>
					<tr>
						<?
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								
								?>
								<th  width="80"  colspan="2">
									<? 
									echo 'W'.$week_key;
									?>
								</th>
	                           
								<?
							}
						}
	            		?>
					</tr>
					<tr>
						<?
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								$start_week_date=$week_start_day[$week_key][week_start_day];
								$end_week_date=$week_end_day[$week_key][week_end_day];
								if($db_type==2)
								{
									$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
									$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
								}
								else
								{
									$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
									$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
								}
								$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
								$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
								?>
								<th  width="80"  colspan="2">
								<? 
									echo $weekstart_date;
								?>
								</th>
	                            
								<?
							}
						}
	            		?>
					</tr>
					<tr>
						<?
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									
									?>
									<th width="100">
										Order Qty pcs
									</th>
		                            <th width="100">
		                            	Fab Req Qty
									</th>
									<?
								}
							}
	            		?>
					</tr>
				</thead>
				<tbody>
					<?php 

						$buyer_wise_data=array();
						$buyer_weeckly_data=array();
						foreach ($main_array as $style_id => $style_data) 
						{
							
							foreach ($style_data as $po_id => $row) 
							{
								
								$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$po_id])+array_sum($fabric_qty_arr['woven']['finish'][$po_id]);
								
								
								$buyer_wise_data[$row['buyer_name']]['buyer_id']=$row['buyer_name'];
								$buyer_wise_data[$row['buyer_name']]['po_quantity_pcs']+=$row['po_quantity_pcs'];
								

								$conf_country_ship_date=rtrim($wo_color_data_arr2[$po_id]['shipdate'],',');
								$conf_country_ship_date=array_unique(explode(",",$conf_country_ship_date));
								
								$qnty_array=array();$fab_qnty_array=array();$plan_qnty_array=array();
								foreach($conf_country_ship_date as $c_date)
								{
										if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
										else $c_date_con=date("d-M-y",strtotime($c_date));
										//echo $no_of_week_for_header_calc[$c_date_con].'X';
									$qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty_arr['po_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									$plan_qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_plan_qty_arr['plan_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									//$fab_qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_fab_req_qty_arr['fab_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									//echo $week_wise_plan_qty_arr['plan_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]].'-G-';
								}	
								
				                   	foreach($week_counter_header as $mon_key=>$week)
									{
										
										foreach($week as $week_key)
										{
										 
										 	$week_plan_qty=$plan_qnty_array[$week_key];
											$po_wise_qty=$po_wise_qty_arr[$po_id]['plan_cut_qnty'];					
											$avg_fab_week_qty=($fabric_qty_req/$po_wise_qty)*$week_plan_qty;
											$buyer_weeckly_data[$row['buyer_name']][$week_key]['order_qnty']+=$qnty_array[$week_key];
											$buyer_weeckly_data[$row['buyer_name']][$week_key]['fab_req_qnty']+=$avg_fab_week_qty;

											$buyer_wise_data[$row['buyer_name']]['fab_req_qnty']+=$avg_fab_week_qty;
											
											
										}
										
									}
									
								
							}

						
						}
						$sl=1;
						//print_r($buyer_weeckly_data);die;
						
						foreach ($buyer_wise_data as $data) 
						{
							$total_po_quantity_pcs+=$data['po_quantity_pcs'];
							$total_fab_req_qnty+=$data['fab_req_qnty'];
							if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_buyer_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_buyer_<? echo $sl; ?>">
									<td><?php echo $sl; ?></td>
									<td><?php echo $buyer_arr[$data['buyer_id']]; ?></td>
									<td ><?php echo number_format($data['po_quantity_pcs']); ?></td>
									<td align="center"><?php echo number_format($data['fab_req_qnty']/$data['po_quantity_pcs'],2); ?></td>
									<td><?php echo number_format($data['fab_req_qnty']); ?></td>

									<?
										foreach($week_counter_header as $mon_key=>$week)
										{
											
											foreach($week as $week_key)
											{
											 
											 	$total_order_qnty_arr[$week_key]+=$buyer_weeckly_data[$data['buyer_id']][$week_key]['order_qnty'];
												$total_fab_req_qnty_arr[$week_key]+=$buyer_weeckly_data[$data['buyer_id']][$week_key]['fab_req_qnty'];
												
												?>

												 <td><?php echo number_format($buyer_weeckly_data[$data['buyer_id']][$week_key]['order_qnty']) ?></td>
												 <td><?php echo number_format($buyer_weeckly_data[$data['buyer_id']][$week_key]['fab_req_qnty']) ?></td>

												<?
												
											}
											
										}
									?>
								</tr>
							<?
							$sl++;
						}

					 ?>
				</tbody>
				<tfoot>
					<tr>
						

						<td colspan="2" style="justify-content: right;text-align: center;">Total</td>
						<td><?php echo number_format($total_po_quantity_pcs,2); ?></td>
						<td><?php echo number_format($total_fab_req_qnty/$total_po_quantity_pcs,2); ?></td>
						<td><?php echo number_format($total_fab_req_qnty,2); ?></td>

						<?
							foreach($week_counter_header as $mon_key=>$week)
							{
								
								foreach($week as $week_key)
								{
								 
								 	
									
									?>


									 <td><?php echo number_format($total_order_qnty_arr[$week_key],2) ?></td>
									 <td><?php echo number_format($total_fab_req_qnty_arr[$week_key],2) ?></td>


									<?
									
								}
								
							}
							unset($total_fab_req_qnty);
							unset($total_po_quantity_pcs);
							unset($total_order_qnty);
							unset($total_fab_req_qnty);
						?>
						
					</tr>
				</tfoot>
			</table>
			<table width="<? echo $tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" style="margin-top: 10px;">
				<thead>
					<tr>
						
						<th align="center" rowspan="2" width="20">SI</th>
						<th align="center" rowspan="2" width="100">Buyer</th>
						<th align="center" rowspan="2" width="120">Order No.</th>
						<th align="center" rowspan="2" width="120">Style</th>
						<th align="center" rowspan="2" width="80">Order Update Date</th>
						<th align="center" rowspan="2" width="80">Job No.</th>
						<th align="center" rowspan="2" width="100">Fab. Booking No.</th>
						<th align="center" rowspan="2" width="80">Total Order Qnty</th>
						<th align="center" rowspan="2" width="80">UOM</th>
						<th align="center" rowspan="2" width="80">Country Ship Date</th>
	                    <th align="center" rowspan="2" width="80">Order Qty Pcs</th>
						<th align="center" rowspan="2" width="80">Unit Price</th>
						<th align="center" rowspan="2" width="80">Total FOB Val.</th>
						<th align="center" rowspan="2" width="80">Fabric con/pcs</th>
						<th align="center" rowspan="2" width="80">OPD</th>
						<th align="center" colspan="3">Fabric</th>					
						<th width="80" align="center" rowspan="2">Line No.</th>
						<th align="center" colspan="5">Production & Shipment</th>
						<th align="center" width="80" rowspan="2">Shiping Status</th>
						<th colspan="<? echo $rowcount*2; ?>" align="center">Weekly Order Qty.</th>
						<th align="center" width="80" rowspan="2">Total</th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Fab. Req. Qty.</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Rcvd.</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Rcvd. Bal.</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Cutting</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing Out</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Finishing</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Ex-Factory</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80">Inhand</th>
						<?
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								$start_week_date=$week_start_day[$week_key][week_start_day];
								$end_week_date=$week_end_day[$week_key][week_end_day];
								if($db_type==2)
								{
									$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
									$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
								}
								else
								{
									$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
									$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
								}
								$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
								$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
								?>
								<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="80"  align="center">
								<? 
								echo 'W'.$week_key.'<br/>'.$weekstart_date;
								?>
								</th>
	                            <th title="From Budget, <? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="80"  align="center">
								<? 
									echo 'Fab Req Qty';
								?>
								</th>
								<?
							}
						}
	            		?>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px" id="scroll_body">
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_filter" >
					<?
					// print_r($week_for_header);
					$i=1;
					$jj=1;
					$gr_po_qty = 0;
					$gr_fob_qty = 0;
					$gr_fab_req = 0;
					$gr_fab_tot_rcv = 0;
					$gr_fab_rcv_bal = 0;
					$gr_cutting = 0;
					$gr_sewing = 0;
					$gr_finish = 0;
					$gr_ex_fact = 0;
					$gr_inhand = 0;				

					foreach ($main_array as $style_id => $style_data) 
					{
						$style_wise_po_qty = 0;
						$style_wise_fob_qty = 0;
						$style_wise_fab_req = 0;
						$style_wise_fab_tot_rcv = 0;
						$style_wise_fab_rcv_bal = 0;
						$style_wise_cutting = 0;
						$style_wise_sewing = 0;
						$style_wise_finish = 0;
						$style_wise_ex_fact = 0;
						$style_wise_inhand = 0;
						foreach ($style_data as $po_id => $row) 
						{
							$conf_country_ship_date=rtrim($wo_color_data_arr2[$po_id]['shipdate'],',');
							$conf_country_ship_date=array_unique(explode(",",$conf_country_ship_date));
							$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$po_id])+array_sum($fabric_qty_arr['woven']['finish'][$po_id]);
							$costPerQty=$costPerArr[$row['job_no_full']];
							$fabric_qty_req_cons=($fabric_qty_req/$row['plan_cut_qnty']);//*$costPerQty;
							//echo $costPerQty.'DD';
							// print_r($conf_country_ship_date); 

							if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
								<td style="word-wrap: break-word;word-break: break-all;" width="20"><? echo $i;?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $row['po_number']; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="120"><? echo $style_id; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo ($row['update_date'] != "") ? change_date_format($row['update_date']) : ""; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['job_no']; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $booking_no_fin_qnty_array[$po_id]['booking_no'];?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="PO Wise Qnty,po no=<? echo $po_id;?>">
									<?
									$poQnty = $poQntyArr[$po_id];
									// $poQnty=($row['order_uom'] == 58) ? $row['po_quantity']*$row['total_set_qnty'] : $row['po_quantity'];
									 echo number_format($poQnty,0); 
									?>									
									</td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $unit_of_measurement[$row['order_uom']];?></td>
							
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['country_ship_date']);?></td>
	                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['po_quantity_pcs'];?></td>
	                            
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
									<? 
									$unitPrice = ($row['order_uom'] == 58) ? $row['unit_price']/2 : $row['unit_price'];
									echo $unitPrice; 
									?>									
									</td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $fob_val = ($poQnty * $unitPrice); echo number_format($fob_val,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="Cons Qty=Req Qty <? echo $fabric_qty_req.'/Plan Cut('.$row['plan_cut_qnty'].')';?>">
								<? echo number_format($fabric_qty_req_cons,2);//$fabric_qty_req.'='.$row['plan_cut_qnty'];
								
								?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo ($row['po_received_date'] != "") ? change_date_format($row['po_received_date']) : ""; ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $bo_qnty = $booking_no_fin_qnty_array[$po_id]['qnty']; echo number_format($bo_qnty,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $rcv_qnty = $rcv_qty_arr[$po_id]['qnty']; echo number_format($rcv_qnty,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $bal = $bo_qnty - $rcv_qnty;echo number_format(($bal)); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $prod_array[$po_id]['sewing_line'];?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'1','cutting_sewing_action');" ><? echo number_format($prod_array[$po_id]['cutting_qty'],0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'5','cutting_sewing_action');"><? echo number_format($prod_array[$po_id]['sewing_qty'],0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'8','cutting_sewing_action');"><? $fin_qty = $prod_array[$po_id]['finish_qty']; echo number_format($fin_qty,0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><a href="javascript:void(0)" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,'1','total_exfac_action');"><? $ex_data = $exfactory_data_array[$po_id]['ex_factory_qnty']; echo number_format($ex_data,0); ?></a></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? $inhand_qty = $fin_qty - $ex_data; echo number_format($inhand_qty,0); ?></td>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo $ship_status_arr[$row['shiping_status']];?></td>
								<?
								$qnty_array=array();$fab_qnty_array=array();$plan_qnty_array=array();
								foreach($conf_country_ship_date as $c_date)
								{
										if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
										else $c_date_con=date("d-M-y",strtotime($c_date));
										//echo $no_of_week_for_header_calc[$c_date_con].'X';
									$qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty_arr['po_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									$plan_qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_plan_qty_arr['plan_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									//$fab_qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_fab_req_qty_arr['fab_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]];
									//echo $week_wise_plan_qty_arr['plan_qty'][$po_id][$no_of_week_for_header_calc[$c_date_con]].'-G-';
								}							
								
								// print_r($qnty_array); die;
								$week_qty=0;$fab_week_qty=0;
								$week_check=array();
								$v_total = 0;$fab_v_total = 0;
			                   	foreach($week_counter_header as $mon_key=>$week)
								{
									
									foreach($week as $week_key)
									{
									?>
									<td  style="word-wrap:break-word; word-break: break-all;" width="80" align="right">
									<? 
										 	$week_plan_qty=$plan_qnty_array[$week_key];
											$week_qty=$qnty_array[$week_key];
											if($week_qty>0) echo number_format($week_qty,0);else echo ' ';
											$tot_week_qty[$mon_key][$week_key]+=$week_qty;									
											$tot_style_wise_week_qty[$style_id][$mon_key][$week_key]+=$week_qty;	
											$v_total +=	$week_qty;	
											$fabric_qty_req=array_sum($fabric_qty_arr['knit']['finish'][$po_id])+array_sum($fabric_qty_arr['woven']['finish'][$po_id]);	
											//$po_wise_qty=$po_wise_qty_arr[$po_id]['order_quantity'];
											$po_wise_qty=$po_wise_qty_arr[$po_id]['plan_cut_qnty'];					
									?>
									</td>
	                                <td  style="word-wrap:break-word; word-break: break-all;" title="Avg Req Qty=Req Qty(<? echo number_format($fabric_qty_req,2);?>)/Tot Plan Qty(<? echo number_format($po_wise_qty,2);?>)*Week Plan Qty(<? echo number_format($week_plan_qty,2);?>)" width="80" align="right">
									<? 
											$avg_fab_week_qty=($fabric_qty_req/$po_wise_qty)*$week_plan_qty;
											//$fab_week_qty=$fab_qnty_array[$week_key];
											if($avg_fab_week_qty>0) echo number_format($avg_fab_week_qty,0);else echo ' ';
											$tot_fab_week_qty[$mon_key][$week_key]+=$avg_fab_week_qty;									
											$tot_style_wise_fab_week_qty[$style_id][$mon_key][$week_key]+=$avg_fab_week_qty;	
											$fab_v_total +=	$avg_fab_week_qty;							
									?>
									</td>
									<?
									}
									?>
									<?
								}
								?>
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo $v_total; ?></td>
							</tr>
							<?
							$i++;
							$jj++;
							
							$style_wise_po_qty += $poQnty;
							$style_wise_fob_qty += $fob_val;
							$style_wise_fab_req += $bo_qnty;
							$style_wise_fab_tot_rcv += $rcv_qnty;
							$style_wise_fab_rcv_bal += $bal;
							$style_wise_cutting += $prod_array[$po_id]['cutting_qty'];
							$style_wise_sewing += $prod_array[$po_id]['sewing_qty'];
							$style_wise_finish += $fin_qty;
							$style_wise_ex_fact += $ex_data;
							$style_wise_inhand += $inhand_qty;

							$gr_po_qty += $poQnty;
							$gr_fob_qty += $fob_val;
							$gr_fab_req += $bo_qnty;
							$gr_fab_tot_rcv += $rcv_qnty;
							$gr_fab_rcv_bal += $bal;
							$gr_cutting += $prod_array[$po_id]['cutting_qty'];
							$gr_sewing += $prod_array[$po_id]['sewing_qty'];
							$gr_finish += $fin_qty;
							$gr_ex_fact += $ex_data;
							$gr_inhand += $inhand_qty;
						}

						?>
						<tr bgcolor="#CCCCCC">
							<td style="word-wrap: break-word;word-break: break-all;" width="20"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="120" align="right">Style Wise Total</td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_po_qty,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	                        <td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	                        <td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fob_qty,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fab_req,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fab_tot_rcv,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_fab_rcv_bal,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_cutting,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_sewing,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_finish,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_ex_fact,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_wise_inhand,0); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"></td>
							<?					
							$style_wise_total_week_qty=0; $style_wise_total_fab_week_qty=0; 
							$style_v_total = 0;$style_v_fab_total = 0;
							foreach($week_counter_header as $mon_key=>$week)
							{
								
								foreach($week as $week_key)
								{
								?>
								<td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
								<? 
								$style_wise_total_week_qty=$tot_style_wise_week_qty[$style_id][$mon_key][$week_key];
								echo number_format($style_wise_total_week_qty,0);
								$style_v_total += $style_wise_total_week_qty;
								?>
								</td>
	                            <td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
								<? 
								$style_wise_total_fab_week_qty=$tot_style_wise_fab_week_qty[$style_id][$mon_key][$week_key];
								echo number_format($style_wise_total_fab_week_qty,0);
								$style_v_fab_total += $style_wise_total_fab_week_qty;
								?>
								</td>
								<?
								}
								?>	
								<?
							}
			            	?>						
								<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($style_v_total,0); ?></td>			
						</tr>
						<?
					}
					?>
				</table>
			</div>
			<table  border="0" class=""  width="<? echo $tbl_width;?>" rules="all" id="" cellpadding="0" cellspacing="0">
				<tr bgcolor="#CCCCCC">
					<td style="word-wrap: break-word;word-break: break-all;" width="20"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="120" align="right">Grand Total</td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_po_qty,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fob_qty,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fab_req,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fab_tot_rcv,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_fab_rcv_bal,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_cutting,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_sewing,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_finish,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_ex_fact,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_inhand,0); ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"></td>
					<?					
					$total_week_qty=0;$total_fab_week_qty=0; $total_week_amt=0; 
					$gr_v_total = 0;
					foreach($week_counter_header as $mon_key=>$week)
					{
						
						foreach($week as $week_key)
						{
						?>
						<td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$gr_v_total += $total_week_qty;
						?>
						</td>
	                    <td width="80" style="word-wrap:break-word; word-break: break-all;font-size: smaller" align="right">
						<? 
						$total_fab_week_qty=$tot_fab_week_qty[$mon_key][$week_key];
						echo number_format($total_fab_week_qty,0);
						$tot_mon_fab_qty[$mon_key]+=$total_fab_week_qty;
						$gr_v_fab_total += $total_fab_week_qty;
						?>
						</td>
						<?
						$w++;
						}
						?>
						<?
					}
	            	?>						
					<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_v_total,0); ?></td>
				</tr>	
			</table>
		</div>
		<?
	}
	?>
	
	<?
	$html = ob_get_contents();
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	echo "####".$name;
	exit();
}

if($action=="cutting_sewing_action")
{
	extract($_REQUEST);
	list($po,$type)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
	<div id="data_panel" align="center" style="width:100%">
		<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	</div>
	<?
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	$production_sql="SELECT a.serving_company, c.color_number_id,c.size_number_id,sum(b.production_qnty) as qntys,c.order_quantity  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.production_type='$type'  group by  a.serving_company, c.color_number_id,c.size_number_id,c.order_quantity";
	 $color_size_wise_qnty=array();
	 $size_all_arr=array();
	 foreach(sql_select($production_sql) as $keys=>$vals)
	 {
	 	if($po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=="")
	 	{
	 		$color_size_wise_qnty[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
	 		$po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=1;
	 	}
	 	
	 	$working_comp_color_size_wise_qnty[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["qntys"]+=$vals[csf("qntys")];
	 	$details_part_array[$vals[csf("serving_company")]][$vals[csf("color_number_id")]]=$vals[csf("serving_company")];
	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 	$color_all_arr[$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];
	 }
	  
	 $size_count=count($size_all_arr)*45;
	 $tbl_width=200+$size_count;
	?>
     

    </head>
    <body>
        <div id="details_reports" style="width:<? echo $tbl_width+150 ;?>px; margin: 10px auto">
            
            
             	<table width="<? echo $tbl_width ;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="table_body" rules="all">
             		<caption> <strong>Size</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>
             					<?
             				}
             				?>
             				<th width="80">Total</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;margin-bottom: 20px;">
	             	<table  width="<? echo $tbl_width ;?>" border="1" rules="all" class="rpt_table" cellspacing="0" cellpadding="0" >
	             	<?
	             	$p=1;
	              	$gr_size_total=array();
	              	$size_total=0;
	             	foreach($color_all_arr as  $keys=> $rows)            		 
	             	{
	             		$total_sizeqnty=0;
	             		
	             		?>
	             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$keys] ;?></td>
         						<?
         						
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo  $value= $color_size_wise_qnty[$keys][$size_key]["order_quantity"] ;?></td>

         							<?
         							$gr_size_total[$size_key]+=$value;
         							$total_sizeqnty+=$value;        							 
         							
         						}         					 
         						?>
	         					<td align="right"  width="80"><b><? echo $total_sizeqnty;?></b></td>	         						 
	             						 
	             			</tr>  
						<?
						}
						?>   
							<tr>
								<td colspan="2" align="right"><b>Total</b></td>
								<?
								$gr_all_size=0;
	             				foreach($size_all_arr as $key=>$val)
	             				{
	             					?>
	             					<td width="45" align="right"><b><? echo $vals=$gr_size_total[$key];?></b></td>

	             					<?
	             					$gr_all_size+=$vals;
	             				}

	             				?>
	             				<td align="right"><b><? echo $gr_all_size?></b></td>
							</tr>            		 
	             		</table>
             		</div>

             		<table width="<? echo $tbl_width+120 ;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
             		<caption> <strong>Details</strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="120">Working Company</th>             				 
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45" align="right"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Total</th>
             				 
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width+120 ;?>" border="1" rules="all" class="rpt_table" id="table_body2" cellspacing="0" cellpadding="0" >
             	<?
             	$p=1;
             	$detail_grand_total = 0;
             	$dtls_gr_size=array();
             	foreach($details_part_array as  $company_id=> $color_data)            		 
             	{             		
             		foreach($color_data as  $color_id=> $rows)
             		{
             			?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="120"><? echo $company_library[$company_id] ;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
         						<?
         						$size_total=0;
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo $size_qn=  $working_comp_color_size_wise_qnty[$company_id][$color_id][$size_key]["qntys"] ;?></td>

         							<?
         							$size_total+=$size_qn;
         							$dtls_gr_size[$size_key]+=$size_qn;
         							
         						}
         						$detail_grand_total += $size_total;
         						?>
         						<td align="right"  width="80"><b><? echo $size_total;?></b></td>         						 
             						 
             			</tr>  
					<?
					}
				}
				?>
					<tr>
						<td colspan="3" align="right"><b>Total</b></td>
						<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<td width="45" align="right"><b><? echo $dtls_gr_size[$key];?></b></td>

         					<?
         				}

         				?>
         				<td align="right"><b><?php echo $detail_grand_total;?></b></td>
					</tr>               		 
             	</table>
             </div>
             
        </div>
      
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> setFilterGrid("table_body",-1);  </script> 
    <script> setFilterGrid("table_body2",-1);  </script> 
    </html>
    
    <?
	
	exit();
}


if($action=="total_exfac_action")
{
	extract($_REQUEST);
	list($po)=explode('**', $data);
	$work_comp=$_SESSION["work_comp"];
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
	<div id="data_panel" align="center" style="width:100%">
		<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('xfact_details_reports').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	</div>
	<?
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$po_arr=return_library_array("select id,po_number from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_number");
	$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, a.ex_factory_date,  sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id and  a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id=$po and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   group by a.po_break_down_id, a.item_number_id,c.color_number_id , a.ex_factory_date");

	 $tbl_width = 310;
	?> 
    </head>
    <body>
        <div align="center" style="width:100%;" id="xfact_details_reports">
            
            
             	<table width="<? echo $tbl_width ;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
             		<caption> <strong>Ex-Factory</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">Order No</th>            				 
             				<th width="90">Date</th>            				 
              				<th width="80">Qnty</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
	             	<table  width="<? echo $tbl_width ;?>" border="1" rules="all" class="rpt_table" id="xfact_filter" cellspacing="0" cellpadding="0">
	             	<?
	             	$p=1;	              	 
	              	$gr_total=0;
	             	foreach($ex_factory_data as  $keys=> $rows)            		 
	             	{    
	             		?>
	         			<tr>                	 
	 						<td align="center" width="30" ><? echo $p++;?></td>
	  						<td align="center"  width="110"><? echo $po_arr[$rows[csf("po_break_down_id")]];?></td>
	  						<td align="right"  width="90"><b><? echo change_date_format($rows[csf("ex_factory_date")]);?></b></td>
	  						<td align="right"  width="80"><b><? echo $qntys=$rows[csf("total_ex_fac")];?></b></td> 
	         			</tr>  
						<?
						$gr_total+=$qntys;
					}
					?>  
						<tr bgcolor="#E4E4E4">
							<td align="right" colspan="3">Total</td>
							<td align="right"><strong><? echo $gr_total;?></strong></td>					
						</tr> 							      		 
	             	</table>
             	</div>             		 
             </div>             
        </div>
      
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> setFilterGrid("xfact_filter",-1);  </script> 
    </html>
    
    <?
	
	exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
                //$ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
                //echo $ex_fac_sql;
				$exfac_sql=("SELECT b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}
?>
