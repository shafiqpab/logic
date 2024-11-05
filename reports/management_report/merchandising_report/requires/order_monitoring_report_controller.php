<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for Monitoring report
Functionality	:
JS Functions	:
Created by		:	Md. Aziz
Creation date 	: 	13-03-2018
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
include('../../../../includes/common.php');

extract($_REQUEST);

$pc_time= add_time(date("H:i:s",time()),360);
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
//---------------------------------------------------- Start

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
	$print_report_format=fnc_report_button($data,11,232,0);
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}
if ($action=="load_drop_down_buyer")
{

	$data=str_replace("'", "",$data);
	echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_agent")
{
	$data=str_replace("'", "",$data);
	echo create_drop_down( "cbo_agent_name", 100, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in($data) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name ","id,buyer_name", 1, "-- All --", $selected, "" );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;

	if($type==1)
	{
		$selected_cond="1";
	}
	else if($type==2)
	{
		$selected_cond="2";
	}
	else $selected_cond="3";
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
                        	 $companyID=str_replace("'","",$companyID);
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected_cond,$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'order_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$month_id=$data[5];
	$type_id=$data[6];
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
	if($search_by==1)
	{
		$search_field="a.job_no";
	}
	else if($search_by==2)
	{
		$search_field="a.style_ref_no";
	}
	else
	{
	$search_field="b.po_number";
	}

	if($type_id==1) //Job
	{
		$select_filed="id,job_no_prefix_num";
	}
	else if($type_id==2) //Style
	{
		$select_filed="id,job_no_prefix_num";
	}
	else
	{
		$select_filed="po_id,po_number";
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
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.id as po_id, $year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "$select_filed", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit();
} // Job Search end

if ($action=="report_generate")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	$type=str_replace("'","",$type);
	//echo $date_type.'DDD';die;

	$status_active=str_replace("'", "",$cbo_active_status);
	$_SESSION["status_active"]="";
	$_SESSION["status_active"]=$status_active;
	if($status_active==1)
	{
		$po_tbl_cond_a=" and a.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";

	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and a.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";

	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and a.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";


	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and a.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";


	}
	if($date_type==4)$date_caption="Prev Ship Date"; else $date_caption="Ship Date";

	if($cbo_company_name==0 || $cbo_company_name=="")
	{
		$company_name="";$company_name2="";
	}
	else
	{
		$company_name = "and a.company_name in($cbo_company_name) ";$company_name2 = " and a.company_id in($cbo_company_name)";
	}//fabric_source//item_category

	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and a.team_leader  = $team_leader";else $team_leader_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and a.id=$txt_job_id";else $job_no_cond="";
	if($txt_po_id!='') $po_no_cond="and b.id=$txt_po_id";else $po_no_cond="";

	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}


	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	$startdate=strtotime(str_replace("'","",trim($txt_date_from)));
	$enddate=strtotime(str_replace("'","",trim($txt_date_to)));
	//echo $cal_date_to.'='.$cal_date_from;die;
	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from));
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$end_date = date('Y-m-d',strtotime($txt_date_to));
				//$search_text .=" and b.closing_date between '".$start_date."' and '".$end_date."'";
				$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");

				foreach($ref_closing_po_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($poArr,0,"c.id");
				 $ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");
				 $ship_cond="and b.shiping_status=3";

			}

			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{

			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(b.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{

				//$search_text .=" and b.closing_date between '".$start_date."' and '".$end_date."'";
				$ref_closing_po_arr2=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");



				foreach($ref_closing_po_arr2 as $po_id=>$ids){
					$ref_poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($ref_poArr,0,"c.id");
				// $ref_po_cond_for_in2=where_con_using_array($ref_poArr,0,"b.id");
				 $ref_po_cond_for_in3=where_con_using_array($ref_poArr,0,"b.inv_pur_req_mst_id");
				  $ship_cond="and b.shiping_status=3";

				  $ref_closing_date_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0   $ref_po_cond_for_in3 group by b.inv_pur_req_mst_id", "po_id", "closing_date");



				foreach($ref_closing_date_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;

				}
				$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");


			}

			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}

		}
	}

	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);


	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );

	$team_leader_lib =return_library_array( "SELECT id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );
	$agent_lib =return_library_array( "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in($cbo_company_name) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name", "id", "buyer_name"  );

	$dealing_merchant_lib =return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$department_lib=return_library_array( "select id, department_name from lib_department where  status_active=1 and is_deleted=0", "id", "department_name"  );
	$sub_department_lib=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment where  status_active=1 and is_deleted=0", "id", "sub_department_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');

	 $fab_sql="select b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,e.color_number_id,a.booking_no_prefix_num,a.booking_no,
	(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,
	(case when b.booking_type=1 then b.fin_fab_qnty end) as fin_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d,wo_po_color_size_breakdown e
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id
	and c.id=e.po_break_down_id and b.color_size_table_id=e.id and d.id=e.job_id and e.status_active=1
	and b.booking_type=1 and b.status_active=1
	and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text2 $ref_po_cond_for_in";

	// echo $fab_sql;die;
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];
		if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
		 {
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		 }

	 }
	 unset($fab_result);
	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")";

		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";

		   }
		   $po++;
		}
	 }
	/*  if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond=""; */

	if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	{
	 if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond=0;
	}
	//echo $po_cond_for_in;die;
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

  $sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as  po_quantity, b.po_quantity as po_qty,b.plan_cut as plan_cut,(b.unit_price/a.total_set_qnty) as unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price as po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep  from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id   $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $fnc_cond $job_no_cond $team_leader_cond $po_no_cond $ref_po_cond_for_in2 $ship_cond  order by b.pub_shipment_date desc";
		  //echo $sql_po;die;

		$order_sql_result = sql_select($sql_po);
		foreach($order_sql_result as $rows){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			if(str_replace("'","",$cbo_date_type)==7)
			{
				//cal_date_to cal_date_from
				$ref_closing_min_date=strtotime($ref_closing_date_arr[$rows[csf("id")]]);
				//echo $ref_closing_min_date.'D=';
				if($startdate!='' && $enddate!='' && $ref_closing_min_date>=$startdate && $ref_closing_min_date<=$enddate)
				{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				}

			}
			else
			{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				$tot_order_quantity+= $rows[csf('po_quantity')];
			}
		}
		unset($order_sql_result);
		$po_id_list_arr=array_chunk($order_id_arr,999);
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";$poCon6 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")";
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";

			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";

			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";

			if($p==1) $poCon6 .="  ( c.id in(".implode(',',$po_process).")";
			else  $poCon6 .=" or c.id in(".implode(',',$po_process).")";

			if($p==1) $poCon7 .="  ( c.po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon7 .=" or c.po_breakdown_id in(".implode(',',$po_process).")";
			$p++;
		}
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon6 .=")";$poCon7 .=")";

		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  $poCon3  ";
		$yarn_allo_arr=array();

		foreach(sql_select($yarn_allo) as $v)$yarn_allo_arr[$v[csf("po_break_down_id")]][0]+=$v[csf("qnty")];

		/* $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")]; */

		$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       	and  c.status_active = 1 and a.issue_purpose!=2 $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];


		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $poCon5"); //and a.receive_basis = 3

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];

		}

		$plan_cut_data = sql_select("SELECT c.id, SUM(b.plan_cut_qnty) as plan_cut from  wo_po_color_size_breakdown b, wo_po_break_down c  where b.po_break_down_id = c.id and b.job_no_mst = c.job_no_mst and b.job_id=c.job_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $poCon6 group by c.id"); 
		$plan_cut_arr=array();
		foreach ($plan_cut_data as $row) {
			$plan_cut_arr[$row[csf('id')]]['plan_cut'] += $row[csf('plan_cut')];

		}

		//################################### Fabric Available Purchase, Production, Transfer In, Transfer Out #################//

		$availableSQL = "SELECT C.PO_BREAKDOWN_ID, C.QUANTITY, A.RECEIVE_BASIS
						FROM 
							INV_RECEIVE_MASTER A,
							INV_TRANSACTION B,
							ORDER_WISE_PRO_DETAILS C

						WHERE
							A.ID=B.MST_ID AND 
							B.ID=C.TRANS_ID AND 
							A.RECEIVE_BASIS IN(1,2,4,6,10,9,11) AND 
							A.ENTRY_FORM IN(17,37) AND
							A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND
							B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND
							C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND
							$poCon7	
								";
		//echo $availableSQL; exit();
		$availablePurchaseArray = array();
		$availableProductionArray = array();
		$receive_basis_purchase = [1,2];
		$receive_basis_production = [4,6,10,9,11];
		foreach(sql_select($availableSQL) as $data){
			
			if( in_array($data['RECEIVE_BASIS'], $receive_basis_purchase)){
				$availablePurchaseArray[$data['PO_BREAKDOWN_ID']] = $data['QUANTITY'];
			}else if(in_array($data['RECEIVE_BASIS'], $receive_basis_production)){
				$availableProductionArray[$data['PO_BREAKDOWN_ID']] = $data['QUANTITY'];
			}
			
		}

		//Inventory Transaction.
		$transSQL = "SELECT C.PO_BREAKDOWN_ID, C.QUANTITY, A.FROM_ORDER_ID, A.TO_ORDER_ID, B.TRANSACTION_TYPE
						FROM 
							INV_ITEM_TRANSFER_MST A,
							INV_TRANSACTION B,
							ORDER_WISE_PRO_DETAILS C

						WHERE
							A.ID=B.MST_ID AND 
							B.ID=C.TRANS_ID AND 
							A.TRANSFER_CRITERIA IN(4) AND
							A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND
							B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND
							C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND
							$poCon7	
								";
		$fromOrderArray = array();
		$toOrderArray = array();
		foreach(sql_select($transSQL) as $data){
			$toOrderArray[$data['TO_ORDER_ID']] = $data['QUANTITY'];
			$fromOrderArray[$data['FROM_ORDER_ID']] = $data['QUANTITY'];
		}
		






		//################################### Fabric Available Purchase, Production, Transfer In, Transfer Out #################//





		//print_r($yarn_allo_arr);

		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		//$sqlsTrans="SELECT  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date group by  b.po_breakdown_id";

		 $sqlsTrans="SELECT a.knit_dye_source, c.po_breakdown_id, sum(c.quantity) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knit_dye_source,c.po_breakdown_id  ";

		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			 if($row[csf('knit_dye_source')]!=3)
				$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			 if($row[csf('knit_dye_source')]==3)$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['out']+=$row[csf('qnty')];
		}
		$sql_ret="SELECT  c.po_breakdown_id, sum(c.quantity) as quantity  from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.po_breakdown_id ";
		foreach (sql_select($sql_ret) as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['return']+=$row[csf('quantity')];
		}

		//print_r( $dataArrayYarnIssuesQty);
		$daying_qnty_array=return_library_array( "SELECT sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and a.entry_form=0 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");

		//print_r($daying_qnty_array);

		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);

		$InspectData=sql_select("select a.po_break_down_id, a.inspection_status,a.inspection_date,a.inspection_status,a.inspection_qnty from pro_buyer_inspection a, wo_po_break_down b where a.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 $po_tbl_cond2 $poCon ");
		foreach($InspectData as $row)
		{
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_status'].=$inspection_status[$row[csf('inspection_status')]].',';
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_date'].=change_date_format($row[csf('inspection_date')]).',';
			if($row[csf('inspection_status')]==1)
			{
				$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_qnty']+=$row[csf('inspection_qnty')];
			}
		}
		unset($InspectData);

		$sql="SELECT po_break_down_id,
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut,
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing,
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish,
		sum(CASE WHEN production_type =2 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembissue,
		sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembrec,
		sum(CASE WHEN production_type =2 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_rcv_qnty,
		sum(CASE WHEN production_type =2 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_rcv_qnty,
        sum(CASE WHEN production_type =2 and embel_name=3 THEN production_quantity ELSE 0 END) AS wash_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=3 THEN production_quantity ELSE 0 END) AS wash_rcv_qnty
		from pro_garments_production_mst WHERE status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id";
		// echo $sql;
		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];

			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_issue_qnty']=$row[csf('print_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_rcv_qnty']=$row[csf('print_rcv_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_issue_qnty']=$row[csf('sp_work_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_rcv_qnty']=$row[csf('sp_work_rcv_qnty')];
            $emb_qnty_array[$row[csf('po_break_down_id')]]['wash_issue_qnty']=$row[csf('wash_issue_qnty')];
            $emb_qnty_array[$row[csf('po_break_down_id')]]['wash_rcv_qnty']=$row[csf('wash_rcv_qnty')];

		}
		unset($dataArray);
		$ex_qnty_array=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
		$sqls="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
        foreach(sql_select($sqls) as $vals)
        {
            $fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
        }

		$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
		// print_r($convrsn_rate_library);
		$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
		// ====================================== trims rcv ===================================
		$poCon6 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
		$trims_rcv_val_array = array();
		$receive_qty_data=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity, a.rate,c.currency_id   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $poCon6  group by b.po_breakdown_id,a.rate,c.currency_id ");

        foreach($receive_qty_data as $row)
        {
        	if($row['CURRENCY_ID']==1)
        	{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate;
			}
			else
			{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')];
			}
        }
        // print_r($trims_rcv_val_array);
        $poCon7 = str_replace("b.po_breakdown_id", "b.to_po_id", $poCon6);
        // echo $poCon7;
        // ========================== Transfer In =======================
		$sql_trns_in = "SELECT b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7   and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.to_po_id";
		// echo $sql_trns_in;
		$trans_in_res = sql_select($sql_trns_in);
		$trans_in_qty_array = array();
		foreach ($trans_in_res as  $val)
		{
			$trans_in_qty_array[$val['TO_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_in_qty_array);
		$poCon7 = str_replace("b.po_breakdown_id", "b.from_po_id", $poCon6);
		// ========================== Transfer Out =======================
		$sql_trns_out = "SELECT b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7 and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.from_po_id";
		// echo $sql_trns_out;
		$trans_out_res = sql_select($sql_trns_out);
		$trans_out_qty_array = array();
		foreach ($trans_out_res as  $val)
		{
			$trans_out_qty_array[$val['FROM_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_out_qty_array);
		//------------------------------issue qty--------------------------
		$poCon9 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
		$sql_cuml="select b.po_breakdown_id,sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 then b.quantity end) as finish_fabric_recv,sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2 then b.quantity end) as finish_fabric_issue,sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3 THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4 THEN b.quantity ELSE 0 END) AS iss_retn_qnty,sum(case when b.entry_form in(14,15,306) and b.trans_type=5 and a.transaction_type=5  then b.quantity end) as finish_fabric_trans_recv,sum(case when b.entry_form in(14,15,306) and b.trans_type=6 and a.transaction_type=6 then b.quantity end) as finish_fabric_trans_issued from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id $poCon9 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_breakdown_id";
			$sql_result_cuml=sql_select($sql_cuml);
				foreach($sql_result_cuml as $row)
				{
					$cumu_rec_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_recv')]+$row[csf('finish_fabric_trans_recv')])-$row[csf('recv_rtn_qnty')];
					$cumu_iss_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_issue')]+$row[csf('finish_fabric_trans_issued')])-$row[csf('iss_retn_qnty')];
				}
		//------------------------------issue qty--------------------------
	ob_start();
	?>
    <fieldset>
	<div style="width:6190px" align="left">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="50" align="center" style="font-size:22px;"><? echo $report_title;?></td></tr>
            <tr>
                <td colspan="50" align="center" style="font-size:16px; font-weight:bold;">
					<?
					$comp_names="";
					$comp_arr=explode(",", $cbo_company_name);
					foreach($comp_arr as $v)
					{
						if($comp_names)$comp_names.=','.$company_lib[$v];
						else $comp_names.= $company_lib[$v];
					}
					 echo $comp_names;
					 ?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="50" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?>
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="300"  cellpadding="0" cellspacing="0" border="0" align="left" >
        	<tr>
				<td width="20" bgcolor="white"> </td><td> &nbsp;&nbsp;Full Pending&nbsp; </td>
				<td width="20" bgcolor="yellow"> </td><td>&nbsp;&nbsp;Partial&nbsp; </td>
				<td width="20" bgcolor="cyan"> </td><td>&nbsp;&nbsp;Full Shipment/Closed&nbsp; </td>
        	</tr>
        </table>
		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="6190" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">

            <thead>
                <tr style="font-size:12px">
                    <th class="alignment_css" width="30">Sl</th>
                    <th class="alignment_css" width="100">Company</th>
                    <th class="alignment_css" width="100">Buyer</th>
                    <th class="alignment_css" width="100">Agent</th>
                    <th class="alignment_css" width="80">Job No</th>
                    <th class="alignment_css" width="100">Order No</th>
					<th class="alignment_css" width="100">Style No</th>
					<th class="alignment_css" width="100">Prod. Dept.</th>
					<th class="alignment_css" width="100">Prod. Sub. Dept.</th>
                    <th class="alignment_css" width="100">Item Name</th>
                    <th class="alignment_css" width="60">File</th>
                    <th class="alignment_css" width="60">Picture</th>
					<th class="alignment_css" width="100">Order QTY(Pcs)</th>
					<th class="alignment_css" width="100">Plan Cut Qty(Pcs) </th>
					<th class="alignment_css" width="60">Unit Price(Pcs)</th>
					<th class="alignment_css" width="100">Order Value</th>
					<th class="alignment_css" width="100">PO Insert Date</th>
					<th class="alignment_css" width="100">PO Receive Date</th>
                    <th class="alignment_css" width="80"><? echo  $date_caption;?></th>
					<th class="alignment_css" width="80">Orig.Ship Date </th>
					<th class="alignment_css" width="80">Ext.Ship Date </th>
					<th class="alignment_css" width="80">Ref.Close Date </th>
                    <th class="alignment_css" width="100">Cancel Date </th>
					<th class="alignment_css" width="100">Delete Date </th>

					<th class="alignment_css" width="80">FRI Date</th>
					<th class="alignment_css" width="80">FRI Result</th>

                    <th class="alignment_css" width="60">SMV</th>
                    <th class="alignment_css" width="80">Total SMV</th>
					<th class="alignment_css" width="100">Booking No</th>
					<th class="alignment_css" width="100">Yarn Allocation</th>
					<th class="alignment_css" width="100">Yarn Issue</th>
					<th class="alignment_css" width="100">Yet to Issue</th>
                    <th class="alignment_css" width="60">Grey Req</th>
                    <th class="alignment_css" width="60">Grey Prod.</th>
                    <th class="alignment_css" width="60">Grey to Dye</th>
                    <th class="alignment_css" width="60">G2D (%)</th>
                    <th class="alignment_css" width="60">Dyeing Qty</th>
                    <th class="alignment_css" width="60">Fabrics Req.</th>
                    <th class="alignment_css" width="60">Fab. Avl. Pur.</th>
                    <th class="alignment_css" width="60">Fab. Avl. Pro.</th>
                    <th class="alignment_css" width="60">Fab. Avl. Trns In.</th>
                    <th class="alignment_css" width="60">Fab. Trns Out.</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Total Fab. Avl.</th>
                    <th class="alignment_css" width="60">Fab. Bal</th>
					<th class="alignment_css" width="60">Fab Issue To Cut </th>
                    <th class="alignment_css" width="60">Trims Rcv Val</th>
                    <th class="alignment_css" width="60">Cutting</th>
                    <th class="alignment_css" width="60">Cut (%)</th>
					<th class="alignment_css" width="60">Print Sent</th>
					<th class="alignment_css" width="60">Print Rcv</th>

                    <th class="alignment_css" width="50">Embr Sent</th>
                    <th class="alignment_css" width="60">Embr Rcv</th>

					<th class="alignment_css" width="60">Sp.Wo Sent</th>
					<th class="alignment_css" width="60">Sp.Wo Rcv</th>

                    <th class="alignment_css" width="60">Wash Sent</th>
					<th class="alignment_css" width="60">Wash Rcv</th>

                    <th class="alignment_css" width="60">Sew. Input</th>
                    <th class="alignment_css" width="60">Sew. Output</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
                    <th class="alignment_css" width="60">Inspection Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Val</th>

					<th class="alignment_css" width="80">Ship Bal. Qty</th>
					<th class="alignment_css" width="100">Ship Bal. Value</th>
					<th class="alignment_css" width="80">Short Ship Qty</th>
					<th class="alignment_css" width="100">Short Ship Value</th>
                    <th class="alignment_css" width="80">Excs. Ship Qty</th>
                    <th class="alignment_css" width="100">Excs. Ship Value</th>
                    <th class="alignment_css" width="100">Shipping Status</th>
                    <th class="alignment_css" width="100">Order Status</th>
                    <th class="alignment_css" width="100">Active Status</th>
                    <th class="alignment_css" width="100">Extended Ship Mode</th>
                    <th class="alignment_css" width="100">Sea Discount On FOB</th>
                    <th class="alignment_css" width="100">Air Discount On FOB</th>
                    <th class="alignment_css" width="100">Team Leader</th>
                    <th class="alignment_css" width="100">Dealing Merchant</th>
                    <th class="alignment_css" width="100">Dealy Reason</th>

                    <th class="alignment_css" width="150">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:6210px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body" >
            <table  width="6190"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
              <tbody>
				<?php
                $i=1;$tot_po_total_price=$tot_print_issue_qnty=$tot_print_rcv_qnty=$tot_sp_work_issue_qnty=$tot_sp_work_rcv_qnty=$tot_short_ship_qty=$tot_excess_short_ship_qty=$tot_short_ship_bal_qty=$tot_yarn_allocation_qntyShow=$tot_yarn_issue_qnty=$tot_fin_qty=$tot_inspection_qnty=0;

                foreach($order_data_arr as $row)
                {
					//$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}

					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}

					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					$yarn_iss_qnty=$yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
					$plan_cut_qty=$plan_cut_arr[$row[csf('id')]]['plan_cut'];
					$cumul_balance=$cumu_rec_qty[$row[csf('id')]]-$cumu_iss_qty[$row[csf('id')]];
					$yarn_allocation_qnty=0;
					foreach($gmts_items as $gmts_id)
					{
						// $yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					$yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
					 //var_dump($itemText);die;
					$issQty=($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return'];

					$booking_full_for="";
					foreach($booking_no_array[$row[csf('id')]] as $key=>$val)$booking_full_for=$key;
					$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_full_for]['requ_no'],",")));




					$inspection_qnty=$inspect_data_arr[$row[csf('id')]]['inspection_qnty'];
					$inspection_status=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_status'],',');
					$inspection_date=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_date'],',');
					$inspection_status=implode(',',array_unique(explode(",",$inspection_status)));
					$inspection_date=implode(',',array_unique(explode(",",$inspection_date)));

					$print_issue_qnty=$emb_qnty_array[$row[csf('id')]]['print_issue_qnty'];
					$print_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['print_rcv_qnty'];
					$sp_work_issue_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_issue_qnty'];
					$sp_work_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_rcv_qnty'];
                    $wash_issue_qnty=$emb_qnty_array[$row[csf('id')]]['wash_issue_qnty'];
                    $wash_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['wash_rcv_qnty'];
					$extended_ship_date=$row[csf('extended_ship_date')];


					if($row[csf('shiping_status')]==3 && $row[csf('po_quantity')]>$ex_qnty_array[$row[csf('id')]]) //Full Shipment/Closed
					{
						$short_ship_qty=$row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]];
					}
					else
					{
						$short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2 ) //Partial
					{
						$short_ship_bal_qty=$row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]];
					}
					else
					{
						$short_ship_bal_qty=0;
					}

					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2 ) //Partial
					{
						$excess_short_ship_qnty=0;

					}
					else
					{
						if($ex_qnty_array[$row[csf('id')]]>$row[csf('po_quantity')])
						{
							$excess_short_ship_qnty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')];
						}
						
					}


					$finQty = ($sewing_finish_qnty_array[$row[csf('id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];

					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					if($row[SHIPING_STATUS]==2){$bgcolor="#FFFF00";}
					elseif($row[SHIPING_STATUS]==3){$bgcolor="#00FFFF";}
					else{$row[SHIPING_STATUS]=1;}
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;"" valign="middle">
                    <td class="alignment_css" width="30"><? echo $i;?></td>
                    <td class="alignment_css" width="100"><p><? echo $company_lib[$row[csf('company_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $agent_lib[$row[csf('agent_name')]];?></p></td>
                    <td class="alignment_css" width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $row[csf('po_number')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $row[csf('style_ref_no')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $product_dept[$row[csf('product_dept')]];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $sub_department_lib[$row[csf('pro_sub_dep')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo implode(',',$itemText);?></p></td>
					<td class="alignment_css" width="60"><A href="javascript:void()" onClick="openPopup('<? echo $row[csf('job_no')];?>','Job File Pop up','job_file_popup')">File</A></td>
                    <td class="alignment_css" width="60"  align="center">
                    <?
                    if($imge_arr[$row[csf('job_no')]]!='')
					{
					?>
                    <p onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></p>
                    <?
					}
					else
					{
						echo " ";
					}
					?>
                    </td>
                    <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
					 <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($plan_cut_qty,0));?></td>
					 <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($row[csf('unit_price')],3));?></td>
					 <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_total_price')],2));?></td>

                    <td class="alignment_css" width="100" align="center"><? $insert_dates=$row[csf('insert_date')]; echo date("d-M-Y", strtotime($insert_dates));?></td>
                    <td class="alignment_css" width="100" align="center"><? $po_received_date=$row[csf('po_received_date')]; echo date("d-M-Y", strtotime($po_received_date));?></td>
                    <?
                    if(str_replace("'","",$cbo_date_type)==6 || str_replace("'","",$cbo_date_type)==5){
                    	$originalDate = $row[csf('update_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==1){
                    	$originalDate = $row[csf('pub_shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==2){
                    	$originalDate = $row[csf('shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==3){
                    	$originalDate = $row[csf('insert_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==4){
                    	$originalDate = $row[csf('extended_ship_date')];
                    }
                    $originalDate = $row[csf('pub_shipment_date')];//ref_closing_date_arr
					$ref_closing_date=$ref_closing_date_arr[$row[csf('id')]];
                    ?>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($originalDate));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($row[csf('shipment_date')]));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
					<td class="alignment_css" width="80" align="center"><? echo ($extended_ship_date =="") ? "" : date("d-M-Y", strtotime($extended_ship_date)); ?></td>

                    <td class="alignment_css" width="80" align="center"><? echo ($ref_closing_date =="") ? "" : date("d-M-Y", strtotime($ref_closing_date)); ?></td>

					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==3) echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==2)  echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="80" align="center"><? echo $inspection_date;?></td>
					<td class="alignment_css" width="80" align="center"><? echo $inspection_status;?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>

                    <td class="alignment_css" width="80" align="right"><? $totalSetSMV= $row[csf('po_qty')]*omitZero($row[csf('set_smv')]); echo number_format($totalSetSMV); ?></td>

					 <td class="alignment_css" width="100"><p>
					<?
						$html=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						//$html[] ="<a href='$booking_no'>$booking_num</a>";
						$html[] =$booking_num;
						}
						echo implode(',',$html);

						if($cbo_fabric_source==2) //purchase
						{
							$yarn_allocation_qnty=0;
							$yarn_iss_qnty=0;
							$gf_qnty_array[$row[csf('id')]]=0;
							$gp_qty_array[$row[csf('id')]]=0;
							$issQty=0;$gp_per=0;
							$daying_qnty_array[$row[csf('id')]]=0;
						}
					?>
                    </p></td>
                     <td class="alignment_css" width="100" align="right"><?  echo  omitZero(number_format($yarn_allocation_qnty,0));?></td>
                     <td class="alignment_css" width="100"  align="right"><? echo  omitZero(number_format($yarn_iss_qnty,0));?></td>
                     <td class="alignment_css" width="100"  align="right">
						<? 
						$yet_to_issue = $yarn_allocation_qnty-$yarn_iss_qnty;
						echo  omitZero(number_format($yet_to_issue,0));?>
					</td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($gf_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($gp_qty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($issQty,0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(fn_number_format($gp_per),0);?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($daying_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($ff_qnty_array[$row[csf('id')]],0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($availablePurchaseArray[$row[csf('id')]],0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($availableProductionArray[$row[csf('id')]],0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($toOrderArray[$row[csf('id')]],0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($fromOrderArray[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? 
							$fav_balance = $availablePurchaseArray[$row[csf('id')]] + $availableProductionArray[$row[csf('id')]] + $toOrderArray[$row[csf('id')]] - $fromOrderArray[$row[csf('id')]];
							 
							echo omitZero(number_format($fav_balance),0);
							//echo omitZero(number_format($fabrics_avl_qnty_array[$row[csf('id')]]),0);
						?>
                    </td>
                    <td class="alignment_css" width="60" align="right">
                    	<? $blance= $ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]];
                    	echo omitZero(number_format($blance,0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo number_format($cumu_rec_qty[$row[csf('id')]]-$cumu_iss_qty[$row[csf('id')]],2);?></td>
                    <td class="alignment_css" width="60" align="right">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row['ID'];?>','Trims Value Popup','trims_value_popup')">
                    		<? echo number_format($trims_rcv_val_array[$row['ID']],2);?>
                    	</A>
                    </td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_per));?></td>
					<td class="alignment_css" width="60" title='Print sent' align="right"><? echo omitZero(number_format($print_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><?  echo omitZero(number_format($print_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($emb_issue_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($emb_rec_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($wash_issue_qnty,0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($wash_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></td>

                    <td class="alignment_css" width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row["ID"];?>_<? echo $row["GMTS_ITEM_ID"];?>','Finish Qty Popup','fin_qnty_popup');">
                    		<? echo omitZero(number_format($finQty,0));?>
                    	</A>
                    </td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($inspection_qnty,0));?></td>
					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="50" align="right">
						<? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')],0));?>
					</td>
					<td class="alignment_css" width="80" align="right"><? echo omitZero(number_format($short_ship_bal_qty,0));?></td>
					<td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($short_ship_bal_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right"><? echo omitZero(number_format($short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" title="Order Qty Pcs-Ship Qty" align="right"><? $short_ship_qnty=$row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]];echo omitZero(number_format($short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right" title="<? echo $ex_qnty_array[$row[csf('id')]].'='.$row[csf('po_quantity')];?>">
						<?
						if($ex_qnty_array[$row[csf('id')]]>$row[csf('po_quantity')])
						{
							echo omitZero(number_format($excess_short_ship_qnty,0));
						}
						 ?>
					</td>
					<td class="alignment_css" width="100" align="right" title="Ship Qty-Order Qty Pcs">
						<?
						if($ex_qnty_array[$row[csf('id')]]>$row[csf('po_quantity')])
						{
							echo omitZero(number_format($excess_short_ship_qnty*$row[csf('unit_price')],0));
						}
						 ?>
					</td>
					
					<td class="alignment_css" width="100" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $order_status[$row[csf('order_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $row_status[$row[csf('status_active')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $extend_shipment_mode[$row[csf('extend_ship_mode')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $sea_discount= ($row[csf('po_total_price')]*( $row[csf('sea_discount')]/100));echo number_format($sea_discount,2); ?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $air_discount= ($row[csf('po_total_price')]*( $row[csf('air_discount')]/100));echo number_format($air_discount,2);?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $team_leader_lib[$row[csf('team_leader')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $dealing_merchant_lib[$row[csf('dealing_marchant')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p>
					<?
						//$delay_for[$row[csf('delay_for')]];
					$delay_reason = false;
					foreach (explode(",", $row['DELAY_FOR']) as $val)
					{
						$delay_reason .= $delay_reason ? ", ".$delay_for[$val] : $delay_for[$val];
					}
					echo $delay_reason;
					?>
					</p></td>
                    <td class="alignment_css" width="150" align="left"><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?

				$tot_po_quantity+=$row[csf('po_quantity')];
				$tot_plan_cut+=$plan_cut_qty;
				$tot_unit_price+=$row[csf('unit_price')];
				$tot_yarn_allocation_qntyShow+=$yarn_allocation_qnty;
				$tot_yarn_issue_qnty+=$yarn_iss_qnty;
				$tot_yet_to_issue+=$$yet_to_issue;
				$tot_po_total_price+=$row[csf('po_total_price')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]]);
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_array[$row[csf('id')]];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
				$tot_cumul_balance+=round($cumu_rec_qty[$row[csf('id')]]-$cumu_iss_qty[$row[csf('id')]]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$finQty;
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				$tot_print_issue_qnty+=$print_issue_qnty;
				$tot_print_rcv_qnty+=$print_rcv_qnty;
				$tot_sp_work_issue_qnty+=$sp_work_issue_qnty;
				$tot_sp_work_rcv_qnty+=$sp_work_rcv_qnty;
                $tot_wash_issue_qnty+=$wash_issue_qnty;
                $tot_wash_rcv_qnty+=$wash_rcv_qnty;
				$tot_short_ship_qty+=$short_ship_qty;
				$tot_excess_short_ship_qty+=$excess_short_ship_qty;
				$tot_short_ship_bal_qty+=$short_ship_bal_qty;
				$tot_fin_qty+=$finQty;
				$tot_inspection_qnty+=$inspection_qnty;
				$tot_short_ship_qnty+=$short_ship_qnty;
				$tot_short_ship_value+=$short_ship_bal_qty*$row[csf('unit_price')];
				$tot_excess_short_ship_qnty+=$excess_short_ship_qnty;
				$tot_excess_short_ship_value+=$excess_short_ship_qnty*$row[csf('unit_price')];
				$totSetSMV+=$totalSetSMV;
				$tot_short_ship_bal_val+=$short_ship_bal_qty*$row[csf('unit_price')];
				$tot_ttl_ship_bal_val+=$ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')];


				$i++;
			}
            ?>
         </tbody>
		</table>
        </div>
        <table width="6190" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" id="report_table_footer" align="left">
         	<tr>
                <td class="alignment_css" width="30">&nbsp; </td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="60">&nbsp;</td>

                <td class="alignment_css" width="60">&nbsp;</td>

				<td class="alignment_css" width="100"  id="td_po_quantity"> <? echo number_format ($tot_po_quantity,0);?></td>
				<td class="alignment_css" width="100"  id="td_plan_quantity"> <? echo number_format ($tot_plan_cut,0);?></td>
				<td title="Total Order Value/Total Order QTY(Pcs)"class="alignment_css" width="60"><? echo number_format ($tot_po_total_price/$tot_po_quantity,3);?></td>
				<td class="alignment_css" width="100"  id="value_td_po_total"><? echo number_format ($tot_po_total_price,0);?></td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="60" title="Total SMV/Total Order QTY(Pcs)"><? echo number_format($totSetSMV/$tot_po_quantity,0);?></td>
                <td class="alignment_css" width="80" id="smv_tdshow"><? echo number_format($totSetSMV,0);?></td>


				<td class="alignment_css" width="100" ></td>
				<td  class="alignment_css" width="100" id="yarn_issue_td"><? echo number_format($tot_yarn_allocation_qntyShow,0);?></td>
				<td class="alignment_css" width="100" id="td_gf_qnty" ><? echo number_format($tot_yarn_issue_qnty,0);?></td>
				<td class="alignment_css" width="100" id="yet_to_issue" >
				<? echo number_format($tot_yet_to_issue,0);?>
				</td>
				<td class="alignment_css" width="60" id="td_gp_qty" ><? echo number_format($tot_gf_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_gp_to_qty" ><? echo number_format($tot_gp_qty,0);?></td>

				<td class="alignment_css" width="60" id="td_daying_qnty"><? echo number_format($tot_grey_to_dye,0);?></td>




                <td class="alignment_css" width="60" ><? echo number_format(($tot_grey_to_dye/$tot_gf_qnty)*100,0);?></td>
                <td class="alignment_css" width="60" id="td_ff_qnty" ><? echo number_format($tot_ff_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_fabrics_avl_qnty"><? echo number_format($tot_fabrics_avl_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_total_fav_avl_pur" ><? //echo number_format($total_fav_avl_pur,0);?></td>
                <td class="alignment_css" width="60" id="td_total_fav_avl_pro" ><? //echo number_format($total_fav_avl_pro,0);?></td>
                <td class="alignment_css" width="60" id="td_total_fav_avl_trns_in" ><? //echo number_format($total_fav_avl_trns_in,0);?></td>
                <td class="alignment_css" width="60" id="td_total_fav_trns_out" ><? //echo number_format($total_fav_trns_out,0);?></td>
                <td class="alignment_css" width="60" id="td_blance"><? echo number_format($tot_blance,0);?></td>
				<td class="alignment_css" width="60"  id="td_tot_cumul_balance"><? echo number_format($tot_cumul_balance,0);?></td>
                <td class="alignment_css" width="60" id="td_trim_blance"></td>
				<td class="alignment_css" width="60">&nbsp;</td>
                <td class="alignment_css" width="60" id="td_cut_qnty"><? echo number_format($tot_cut_qnty,0);?></td>

				<td class="alignment_css" width="60" title="Cutting/Total Order QTY(Pcs)*100"><? echo number_format(($tot_cut_qnty/$tot_po_quantity)*100,0);?></td>
				<td class="alignment_css" width="60" id="td_print_recv"><? echo number_format($tot_print_rcv_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_emb_issue_qnty"><? echo number_format($tot_emb_issue_qnty,0);?></td>
                <td class="alignment_css" width="50" id="td_emb_rec_qnty"><? echo number_format($tot_emb_rec_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_sp_issue_qnty"><? echo number_format($tot_sp_work_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_sp_rec_qnty" ><? echo number_format($tot_sp_work_rcv_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_wash_issue_qnty"><? echo number_format($tot_wash_issue_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_wash_rec_qnty" ><? echo number_format($tot_wash_rcv_qnty,0);?></td>

                <td class="alignment_css" width="60" id="td_sewing_in_qnty"><? echo number_format($tot_sewing_in_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_out_qnty"><? echo number_format($tot_sewing_out_qnty,0);?></td>
                <td class="alignment_css" width="60"><? echo number_format($tot_sewing_out_qnty,0);?></td>
                <td class="alignment_css" width="60"><? echo number_format($tot_fin_qty,0);?></td>
				<td class="alignment_css" width="60"><? echo number_format($tot_inspection_qnty,0);?></td>
                <td class="alignment_css" width="50" id="td_ex_qnty"><? echo number_format($tot_ex_qnty,0);?></td>



				 <td class="alignment_css" width="50" ><? echo number_format($tot_ttl_ship_bal_val,0);?></td>
				 <td class="alignment_css" width="80" id="td_ship_bal_qnty"><? echo number_format($tot_short_ship_bal_qty,0);?></p></td>
				 <td class="alignment_css" width="100" id="td_ship_bal_val"><? echo number_format($tot_short_ship_bal_val,0);?></p></td>
				 <td class="alignment_css" width="80" id="td_short_ship_qntys"><? echo number_format($tot_short_ship_qty,0);?></td>
				 <td class="alignment_css" width="100" id="td_short_ship_val"><? echo number_format($tot_short_ship_value,0);?></td>
				<td class="alignment_css" width="80" id="td_excess_ship_qnty"><? echo number_format($tot_excess_short_ship_qnty,0);?></td>
				 <td class="alignment_css" width="100" id="td_excess_ship_val"><? echo number_format($tot_excess_short_ship_value,0);?></td>
				
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
      		</tr>
        </table>
	</div>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_clean();

	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename)
	{
		@unlink($filename);
	}

	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename****$type";
	exit();
}

 if ($action=="report_generate_show3")
{	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	$type=str_replace("'","",$type);
	//echo $date_type.'DDD';die;

	$status_active=str_replace("'", "",$cbo_active_status);
	$_SESSION["status_active"]="";
	$_SESSION["status_active"]=$status_active;
	if($status_active==1) 
	{
		$po_tbl_cond_a=" and a.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";
		$po_tbl_cond3=" and status_active=1 ";
		 
	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and a.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";
		$po_tbl_cond3=" and status_active=0 ";
		 
	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and a.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";
 
	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and a.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";
		
		 
	}
	if($date_type==4)$date_caption="Prev Ship Date"; else $date_caption="Ship Date";
	
	if($cbo_company_name==0 || $cbo_company_name=="")
	{ 
		$company_name="";$company_name2="";
	}
	else 
	{ 
		$company_name = "and a.company_name in($cbo_company_name) ";$company_name2 = " and a.company_id in($cbo_company_name)";
	}//fabric_source//item_category
	
	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and a.team_leader  = $team_leader";else $team_leader_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and a.id=$txt_job_id";else $job_no_cond="";
	if($txt_po_id!='') $po_no_cond="and b.id=$txt_po_id";else $po_no_cond="";
	
	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}
	
	
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	
	$startdate=strtotime(str_replace("'","",trim($txt_date_from)));
	$enddate=strtotime(str_replace("'","",trim($txt_date_to)));
	//echo $cal_date_to.'='.$cal_date_from;die;
	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from)); 
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$end_date = date('Y-m-d',strtotime($txt_date_to)); 
				//$search_text .=" and b.closing_date between '".$start_date."' and '".$end_date."'";
				$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");	
				
				foreach($ref_closing_po_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($poArr,0,"c.id"); 
				 $ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");
				 $ship_cond="and b.shiping_status=3"; 
		
			}

			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{
			
			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(b.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				 
				//$search_text .=" and b.closing_date between '".$start_date."' and '".$end_date."'";
				$ref_closing_po_arr2=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");
				 
				 
			 
				foreach($ref_closing_po_arr2 as $po_id=>$ids){
					$ref_poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($ref_poArr,0,"c.id"); 
				// $ref_po_cond_for_in2=where_con_using_array($ref_poArr,0,"b.id");
				 $ref_po_cond_for_in3=where_con_using_array($ref_poArr,0,"b.inv_pur_req_mst_id");
				  $ship_cond="and b.shiping_status=3"; 
				  
				  $ref_closing_date_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0   $ref_po_cond_for_in3 group by b.inv_pur_req_mst_id", "po_id", "closing_date");
				 
				 
				
				foreach($ref_closing_date_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;
					
				}
				$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");
		
		
			}

			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		
		}
	}
	
	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);
	
	
	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );

	$team_leader_lib =return_library_array( "SELECT id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );
	$agent_lib =return_library_array( "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in($cbo_company_name) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name", "id", "buyer_name"  );

	$dealing_merchant_lib =return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$department_lib=return_library_array( "select id, department_name from lib_department where  status_active=1 and is_deleted=0", "id", "department_name"  );
	$sub_department_lib=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment where  status_active=1 and is_deleted=0", "id", "sub_department_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');
	 
	 $fab_sql="select b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,e.color_number_id,a.booking_no_prefix_num,a.booking_no, 
	(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,
	(case when b.booking_type=1 then b.fin_fab_qnty end) as fin_fab_qnty 
	from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d,wo_po_color_size_breakdown e
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id 
	and c.id=e.po_break_down_id and b.color_size_table_id=e.id and d.id=e.job_id and e.status_active=1
	and b.booking_type=1 and b.status_active=1 
	and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text2 $ref_po_cond_for_in";
	
	//echo $fab_sql;die;
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];
		$booking_no_arr[$row[csf('po_id')]]['booking_mst_no']=$row[csf('booking_no')];
		if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
		 {
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		 }
		 
	 }
	 unset($fab_result);
	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")"; 
		
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";
			
		   }
		   $po++;
		}	
	 }
	 /* if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond=""; */
	if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	{
	 if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond=0;
	}
	//echo $po_cond_for_in;die;
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

		/* $sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep  from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $fnc_cond $job_no_cond $team_leader_cond $po_no_cond $ref_po_cond_for_in2 $ship_cond order by b.pub_shipment_date desc"; */
		  /*$sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,sum(c.order_quantity) as  po_qty,sum(c.plan_cut_qnty) as plan_cut,(b.unit_price/a.total_set_qnty) as unit_price,sum(c.order_quantity) as po_quantity,sum(c.order_total) as po_total_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id  and c.po_break_down_id=b.id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $fnc_cond $job_no_cond $team_leader_cond $po_no_cond $ref_po_cond_for_in2 $ship_cond group by b.is_confirmed, b.shipment_date,b.pub_shipment_date, b.po_received_date, b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,b.unit_price,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount,
 b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep order by b.pub_shipment_date desc"; */
  $sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as  po_quantity, b.po_quantity as po_qty,b.plan_cut as plan_cut,(b.unit_price/a.total_set_qnty) as unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price as po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep  from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id   $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $fnc_cond $job_no_cond $team_leader_cond $po_no_cond $ref_po_cond_for_in2 $ship_cond  order by b.pub_shipment_date desc"; 
		  // echo $sql_po;
		 
		$order_sql_result = sql_select($sql_po);$tot_poQty=0;
		foreach($order_sql_result as $rows){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			if(str_replace("'","",$cbo_date_type)==7)
			{
				//cal_date_to cal_date_from
				$ref_closing_min_date=strtotime($ref_closing_date_arr[$rows[csf("id")]]);
				//echo $ref_closing_min_date.'D=';
				if($startdate!='' && $enddate!='' && $ref_closing_min_date>=$startdate && $ref_closing_min_date<=$enddate)
				{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				}
				
			}
			else
			{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				$order_qty_arr[$rows[csf('id')]]['po_quantity']+=$rows[csf('po_quantity')];
				
				$tot_poQty+= $rows[csf('po_quantity')];
			}
		}
		//echo $tot_poQty.'=T';
		unset($order_sql_result);	
		$po_id_list_arr=array_chunk($order_id_arr,999);
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";$poCon8 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")"; 
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")"; 
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")"; 
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";

			if($p==1) $poCon8 .="  ( po_number_id in(".implode(',',$po_process).")"; 
			else  $poCon8 .=" or po_number_id in(".implode(',',$po_process).")";
			$p++;
		}
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon8 .=")";
		
		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  $poCon3  ";
		$yarn_allo_arr=array();

		foreach(sql_select($yarn_allo) as $v)$yarn_allo_arr[$v[csf("po_break_down_id")]][0]+=$v[csf("qnty")];

		$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];

		 $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       and b.receive_basis = 3  and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];


		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $poCon5");

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];
			 	
		}





		//print_r($yarn_allo_arr);
		
		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		//$sqlsTrans="SELECT  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date group by  b.po_breakdown_id";

		 $sqlsTrans="SELECT a.knit_dye_source, c.po_breakdown_id, sum(c.quantity) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knit_dye_source,c.po_breakdown_id  ";

		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			 if($row[csf('knit_dye_source')]!=3)
				$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			 if($row[csf('knit_dye_source')]==3)$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['out']+=$row[csf('qnty')];
		}
		$sql_ret="SELECT  c.po_breakdown_id, sum(c.quantity) as quantity  from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.po_breakdown_id ";
		foreach (sql_select($sql_ret) as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['return']+=$row[csf('quantity')];
		}

		//print_r( $dataArrayYarnIssuesQty);
		$daying_qnty_array=return_library_array( "SELECT sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and a.entry_form=0 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");

		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);
	
		$InspectData=sql_select("select a.po_break_down_id, a.inspection_status,a.inspection_date,a.inspection_status,a.inspection_qnty from pro_buyer_inspection a, wo_po_break_down b where a.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 $po_tbl_cond2 $poCon ");
		foreach($InspectData as $row)
		{
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_status'].=$inspection_status[$row[csf('inspection_status')]].',';
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_date'].=change_date_format($row[csf('inspection_date')]).',';
			if($row[csf('inspection_status')]==1)
			{
				$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_qnty']+=$row[csf('inspection_qnty')];
			}
		}
		unset($InspectData);

		$data_array_sample_table=sql_select("Select MIN(approval_status_date) as sample_submission_date,po_break_down_id from wo_po_sample_approval_info where approval_status=1 $poCon3 $po_tbl_cond3 group by po_break_down_id");
		foreach($data_array_sample_table as $row)
		{
			$sample_submission_date_arr[$row[csf('po_break_down_id')]]['sample_submission_date']=change_date_format($row[csf('sample_submission_date')]);
		}
		unset($data_array_sample_table);

		$data_array_pp_table=sql_select("Select MAX(approval_status_date) as sample_approved_date,po_break_down_id from wo_po_sample_approval_info where approval_status=3 $poCon3 $po_tbl_cond3 group by po_break_down_id");
		foreach($data_array_pp_table as $row)
		{
			$sample_approved_date_arr[$row[csf('po_break_down_id')]]['sample_approved_date']=change_date_format($row[csf('sample_approved_date')]);
		}
		unset($data_array_pp_table);

	$tna_start_sql=sql_select( "SELECT id,po_number_id,(case when task_number=131 then MIN(task_start_date) else null end) as file_handover_start_date,(case when task_number=131 then MAX(task_finish_date) else null end) as file_handover_end_date,(case when task_number=84 then MIN(task_start_date) else null end) as cutting_start_date,(case when task_number=84 then MAX(task_finish_date) else null end) as cutting_end_date from tna_process_mst where status_active=1 $poCon8 group by id,po_number_id,task_number");

	foreach($tna_start_sql as $row)
	{
		$tna_date_arr[$row[csf('po_number_id')]]['file_handover_start_date']=change_date_format($row[csf('file_handover_start_date')]);
		$tna_date_arr[$row[csf('po_number_id')]]['file_handover_end_date']=change_date_format($row[csf('file_handover_end_date')]);
		$tna_date_arr[$row[csf('po_number_id')]]['cutting_start_date']=change_date_format($row[csf('cutting_start_date')]);
		$tna_date_arr[$row[csf('po_number_id')]]['cutting_end_date']=change_date_format($row[csf('cutting_end_date')]);
	}
	unset($data_array_pp_table);
	
		$sql="SELECT po_break_down_id, 
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut, 
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput, 
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing, 
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish, 
		sum(CASE WHEN production_type =2 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembissue, 
		sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembrec, 
		sum(CASE WHEN production_type =2 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty, 
		sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_rcv_qnty, 
		sum(CASE WHEN production_type =2 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_issue_qnty, 
		sum(CASE WHEN production_type =3 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_rcv_qnty,
        sum(CASE WHEN production_type =2 and embel_name=3 THEN production_quantity ELSE 0 END) AS wash_issue_qnty, 
		sum(CASE WHEN production_type =3 and embel_name=3 THEN production_quantity ELSE 0 END) AS wash_rcv_qnty 
		from pro_garments_production_mst WHERE status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id"; 
		// echo $sql;
		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{  
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];
			
			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_issue_qnty']=$row[csf('print_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_rcv_qnty']=$row[csf('print_rcv_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_issue_qnty']=$row[csf('sp_work_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_rcv_qnty']=$row[csf('sp_work_rcv_qnty')];
            $emb_qnty_array[$row[csf('po_break_down_id')]]['wash_issue_qnty']=$row[csf('wash_issue_qnty')];
            $emb_qnty_array[$row[csf('po_break_down_id')]]['wash_rcv_qnty']=$row[csf('wash_rcv_qnty')];
		
		}
		unset($dataArray);
		$ex_qnty_array=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
		$sqls="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
        foreach(sql_select($sqls) as $vals)
        {
            $fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
        }

		$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
		// print_r($convrsn_rate_library);
		$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
		// ====================================== trims rcv ===================================
		$poCon6 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
		$trims_rcv_val_array = array();
		$receive_qty_data=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity, a.rate,c.currency_id   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $poCon6  group by b.po_breakdown_id,a.rate,c.currency_id ");

        foreach($receive_qty_data as $row)
        {  
        	if($row['CURRENCY_ID']==1)
        	{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate;
			}
			else
			{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')];	
			}
        }
        // print_r($trims_rcv_val_array);
        $poCon7 = str_replace("b.po_breakdown_id", "b.to_po_id", $poCon6);
        // echo $poCon7;
        // ========================== Transfer In =======================
		$sql_trns_in = "SELECT b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7   and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.to_po_id";
		// echo $sql_trns_in;
		$trans_in_res = sql_select($sql_trns_in);
		$trans_in_qty_array = array();
		foreach ($trans_in_res as  $val) 
		{
			$trans_in_qty_array[$val['TO_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_in_qty_array);
		$poCon7 = str_replace("b.po_breakdown_id", "b.from_po_id", $poCon6);
		// ========================== Transfer Out =======================
		$sql_trns_out = "SELECT b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7 and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.from_po_id";
		// echo $sql_trns_out;
		$trans_out_res = sql_select($sql_trns_out);
		$trans_out_qty_array = array();
		foreach ($trans_out_res as  $val) 
		{
			$trans_out_qty_array[$val['FROM_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_out_qty_array);
	ob_start();	
	?>
    <fieldset>
	<div style="width:6290px" align="left">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="56" align="center" style="font-size:22px;"><? echo $report_title;?></td></tr>
            <tr>
                <td colspan="56" align="center" style="font-size:16px; font-weight:bold;">
					<?
					$comp_names="";
					$comp_arr=explode(",", $cbo_company_name);
					foreach($comp_arr as $v)
					{
						if($comp_names)$comp_names.=','.$company_lib[$v];
						else $comp_names.= $company_lib[$v];
					}
					 echo $comp_names;
					 ?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="56" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?> 
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="300"  cellpadding="0" cellspacing="0" border="0" align="left" > 
        	<tr> 
				<td width="20" bgcolor="white"> </td><td> &nbsp;&nbsp;Full Pending&nbsp; </td>
				<td width="20" bgcolor="yellow"> </td><td>&nbsp;&nbsp;Partial&nbsp; </td>
				<td width="20" bgcolor="cyan"> </td><td>&nbsp;&nbsp;Full Shipment/Closed&nbsp; </td>
        	</tr>
        </table>
		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="6290" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
        	
            <thead>
                <tr style="font-size:12px"> 
                    <th class="alignment_css" width="30">Sl</th>	
                    <th class="alignment_css" width="100">Company</th> 
                    <th class="alignment_css" width="100">Buyer</th> 
                    <th class="alignment_css" width="100">Agent</th> 
                   
                    <th class="alignment_css" width="80">Job No</th>
                    <th class="alignment_css" width="100">Order No</th>
					<th class="alignment_css" width="100">Style No</th>
					<th class="alignment_css" width="100">Prod. Dept.</th>
					<th class="alignment_css" width="100">Prod. Sub. Dept.</th>
                    
                    <th class="alignment_css" width="100">Item Name</th>
                    <th class="alignment_css" width="60">File</th>	
                    <th class="alignment_css" width="60">Picture</th>	
                    <th class="alignment_css" width="100">QTY(Pcs)</th>
					
					<th class="alignment_css" width="60">Unit Price(Pcs)</th>	
					<th class="alignment_css" width="100">Order Value</th>	
					<th class="alignment_css" width="100">PO Insert Date</th>	
					<th class="alignment_css" width="100">PO Receive Date</th>	
                    <th class="alignment_css" width="80"><? echo  $date_caption;?></th>
					
					<th class="alignment_css" width="80">Orig.Ship Date </th>
					<th class="alignment_css" width="80">Ext.Ship Date </th>
					<th class="alignment_css" width="80">Ref.Close Date </th>
                    <th class="alignment_css" width="100">Cancel Date </th>
					<th class="alignment_css" width="100">Delete Date </th>

					<th class="alignment_css" width="100">Fabric booking Date</th>
					<th class="alignment_css" width="100">File Handover<br>Plan Date</th>
					<th class="alignment_css" width="100">File Handover<br>Finish Date</th>
                    <th class="alignment_css" width="100">Cutting Plan Date</th>
					<th class="alignment_css" width="100">Cutting Finish Date</th>
					<th class="alignment_css" width="100">PP Sample<br>Submission Date</th>
					<th class="alignment_css" width="100">PP Sample<br>Approved Date</th>
					
					<th class="alignment_css" width="80">FRI Date</th>
					<th class="alignment_css" width="80">FRI Result</th>
					
                    <th class="alignment_css" width="60">SMV</th>	
                    <th class="alignment_css" width="80">Total SMV</th>	
					<th class="alignment_css" width="100">Booking No</th>
					<th class="alignment_css" width="100">Yarn Allocation</th>
					<th class="alignment_css" width="100">Yarn Issue</th>
                    <th class="alignment_css" width="60">Grey Req</th>
                    <th class="alignment_css" width="60">Grey Prod.</th>	
                    <th class="alignment_css" width="60">Grey to</th>	
                    <th class="alignment_css" width="60">G2D (%)</th>	
                    <th class="alignment_css" width="60">Dyeing Qty</th>	
                    <th class="alignment_css" width="60">Fabrics Req.</th>	
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Fab. Avl.</th>	
                    <th class="alignment_css" width="60">Fab. Bal</th>	
                    <th class="alignment_css" width="60">Trims Rcv Val</th>	
                    <th class="alignment_css" width="60">Cutting</th>	
                    <th class="alignment_css" width="60">Cut (%)</th>	
					<th class="alignment_css" width="60">Print Sent</th>	
					<th class="alignment_css" width="60">Print Rcv</th>	
					  
                    <th class="alignment_css" width="50">Embr Sent</th>	
                    <th class="alignment_css" width="60">Embr Rcv</th>
					
					<th class="alignment_css" width="60">Sp.Wo Sent</th>
					<th class="alignment_css" width="60">Sp.Wo Rcv</th>

                    <th class="alignment_css" width="60">Wash Sent</th>
					<th class="alignment_css" width="60">Wash Rcv</th>
						
                    <th class="alignment_css" width="60">Sew. Input</th>	
                    <th class="alignment_css" width="60">Sew. Output</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
                    <th class="alignment_css" width="60">Inspection Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Qty</th>	
                    <th class="alignment_css" width="50">TTL Ship Val</th>	
					
					<th class="alignment_css" width="80">Short Ship Qty</th>	
					<th class="alignment_css" width="100">Short Ship Value</th>	
                    <th class="alignment_css" width="80">Excs. Ship Qty</th>
                    <th class="alignment_css" width="100">Excs. Ship Value</th>
					<th class="alignment_css" width="80">Ship Bal. Qty</th>	
                    <th class="alignment_css" width="100">Shipping Status</th>
                    <th class="alignment_css" width="100">Order Status</th>
                    <th class="alignment_css" width="100">Active Status</th>
                    <th class="alignment_css" width="100">Extended Ship Mode</th>
                    <th class="alignment_css" width="100">Sea Discount On FOB</th>
                    <th class="alignment_css" width="100">Air Discount On FOB</th>
                    <th class="alignment_css" width="100">Team Leader</th>
                    <th class="alignment_css" width="100">Dealing Merchant</th>
                    <th class="alignment_css" width="100">Dealy Reason</th>
					
                    <th class="alignment_css" width="150">Remarks</th>
                </tr>                            	
            </thead>
        </table>
        <div style="width:6310px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body" >
            <table  width="6290"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
              <tbody>
				<?php
                $i=1;$tot_po_total_price=$tot_print_issue_qnty=$tot_print_rcv_qnty=$tot_sp_work_issue_qnty=$tot_sp_work_rcv_qnty=$tot_short_ship_qty=$tot_excess_short_ship_qty=$tot_short_ship_bal_qty=$tot_yarn_allocation_qntyShow=$tot_yarn_issue_qnty=$tot_fin_qty=$tot_inspection_qnty=$tot_po_quantity=0;
                
                foreach($order_data_arr as $row)
                {
					//$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}
					
					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}
					
					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					$yarn_iss_qnty=$yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
					$yarn_allocation_qnty=0;
					foreach($gmts_items as $gmts_id)
					{
						// $yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					$yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
					 //var_dump($itemText);die;
					$issQty=($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return']; 

					$booking_full_for="";
					foreach($booking_no_array[$row[csf('id')]] as $key=>$val)$booking_full_for=$key;
					$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_full_for]['requ_no'],",")));

					 


					$inspection_qnty=$inspect_data_arr[$row[csf('id')]]['inspection_qnty'];
					$booking_mst_no=$booking_no_arr[$row[csf('id')]]['booking_mst_no'];
					$sample_submission_date=$sample_submission_date_arr[$row[csf('id')]]['sample_submission_date'];
					$sample_approved_date=$sample_approved_date_arr[$row[csf('id')]]['sample_approved_date'];
					$file_handover_start_date=$tna_date_arr[$row[csf('id')]]['file_handover_start_date'];
					$file_handover_end_date=$tna_date_arr[$row[csf('id')]]['file_handover_end_date'];
					$cutting_start_date=$tna_date_arr[$row[csf('id')]]['cutting_start_date'];
					$cutting_end_date=$tna_date_arr[$row[csf('id')]]['cutting_end_date'];
					$inspection_status=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_status'],',');
					$inspection_date=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_date'],',');
					$inspection_status=implode(',',array_unique(explode(",",$inspection_status)));
					$inspection_date=implode(',',array_unique(explode(",",$inspection_date)));
					
					$print_issue_qnty=$emb_qnty_array[$row[csf('id')]]['print_issue_qnty'];
					$print_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['print_rcv_qnty'];
					$sp_work_issue_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_issue_qnty'];
					$sp_work_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_rcv_qnty'];
                    $wash_issue_qnty=$emb_qnty_array[$row[csf('id')]]['wash_issue_qnty'];
                    $wash_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['wash_rcv_qnty'];
					$extended_ship_date=$row[csf('extended_ship_date')];

					
					if($row[csf('shiping_status')]==3 && $row[csf('po_quantity')]>$ex_qnty_array[$row[csf('id')]]) //Full Shipment/Closed
					{
						$short_ship_qty=$row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]];	
					}
					else
					{
						$short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==3 && $ex_qnty_array[$row[csf('id')]]>$row[csf('po_quantity')]) //Full Shipment/Closed
					{
						$excess_short_ship_qty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')];
					}
					else
					{
						$excess_short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2 ) //Partial
					{
						$short_ship_bal_qty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')]; 
					}
					else
					{
						$short_ship_bal_qty=0;
					}

					$finQty = ($sewing_finish_qnty_array[$row[csf('id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];

					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					if($row[SHIPING_STATUS]==2){$bgcolor="#FFFF00";}
					elseif($row[SHIPING_STATUS]==3){$bgcolor="#00FFFF";}
					else{$row[SHIPING_STATUS]=1;}
            ?>	
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    <td class="alignment_css" width="30"><? echo $i;?></td>	
                    <td class="alignment_css" width="100"><p><? echo $company_lib[$row[csf('company_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $agent_lib[$row[csf('agent_name')]];?></p></td>
                    <td class="alignment_css" width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>	
                    <td class="alignment_css" width="100"><p><? echo $row[csf('po_number')];?></p></td>	
					<td class="alignment_css" width="100"><p><? echo $row[csf('style_ref_no')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $product_dept[$row[csf('product_dept')]];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $sub_department_lib[$row[csf('pro_sub_dep')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo implode(',',$itemText);?></p></td>
					<td class="alignment_css" width="60"><a href="javascript:void()" onClick="openPopup('<? echo $row[csf('job_no')];?>','Job File Pop up','job_file_popup')">File</a></td>
                    <td class="alignment_css" width="60"  align="center">
                    <?
                    if($imge_arr[$row[csf('job_no')]]!='')
					{
					?>
                    <p onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></p>
                    <?
					}
					else
					{
						echo " ";	
					}
					//$row[csf('po_quantity')]=0;
				//	$po_quantity=$order_qty_arr[$row[csf('id')]]['po_quantity'];
					//$row[csf('po_quantity')]=$order_qty_arr[$row[csf('id')]]['po_quantity'];
					?>
                    </td>	
                    <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
					 <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($row[csf('unit_price')],3));?></td>
					 <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_total_price')],2));?></td>
                   
                    <td class="alignment_css" width="100" align="center"><? $insert_dates=$row[csf('insert_date')]; echo date("d-M-Y", strtotime($insert_dates));?></td>
                    <td class="alignment_css" width="100" align="center"><? $po_received_date=$row[csf('po_received_date')]; echo date("d-M-Y", strtotime($po_received_date));?></td>
                    <? 
                    if(str_replace("'","",$cbo_date_type)==6 || str_replace("'","",$cbo_date_type)==5){
                    	$originalDate = $row[csf('update_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==1){
                    	$originalDate = $row[csf('pub_shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==2){
                    	$originalDate = $row[csf('shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==3){
                    	$originalDate = $row[csf('insert_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==4){
                    	$originalDate = $row[csf('extended_ship_date')];
                    }
                    $originalDate = $row[csf('pub_shipment_date')];//ref_closing_date_arr
					$ref_closing_date=$ref_closing_date_arr[$row[csf('id')]];
                    ?>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($originalDate));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($row[csf('shipment_date')]));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
					<td class="alignment_css" width="80" align="center"><? echo ($extended_ship_date =="") ? "" : date("d-M-Y", strtotime($extended_ship_date)); ?></td>
                    
                    <td class="alignment_css" width="80" align="center"><? echo ($ref_closing_date =="") ? "" : date("d-M-Y", strtotime($ref_closing_date)); ?></td>	

					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==3) echo change_date_format($row[csf('update_date')]);?></td>	
					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==2)  echo change_date_format($row[csf('update_date')]);?></td>	

					<td class="alignment_css" width="100" align="center"><? echo $booking_mst_no;?></td>	
					<td class="alignment_css" width="100" align="center"><? echo $file_handover_start_date;?></td>	
					<td class="alignment_css" width="100" align="center"><? echo $file_handover_end_date;?></td>	
					<td class="alignment_css" width="100" align="center"><? echo $cutting_start_date;?></td>	
					<td class="alignment_css" width="100" align="center"><? echo $cutting_end_date;?></td>	
					<td class="alignment_css" width="100" align="center"><? echo $sample_submission_date;?></td>	
					<td class="alignment_css" width="100" align="center"><? echo $sample_approved_date;?></td>	
					
					<td class="alignment_css" width="80" align="center"><? echo $inspection_date;?></td>	
					<td class="alignment_css" width="80" align="center"><? echo $inspection_status;?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>

                    <td class="alignment_css" width="80" align="right"><? $totalSetSMV= $row[csf('po_qty')]*omitZero($row[csf('set_smv')]); echo number_format($totalSetSMV,4); ?></td>	

					 <td class="alignment_css" width="100"><p>
					<?
						$html=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						//$html[] ="<a href='$booking_no'>$booking_num</a>";	
						$html[] =$booking_num;	
						}
						echo implode(',',$html);
						
						if($cbo_fabric_source==2) //purchase
						{
							$yarn_allocation_qnty=0;
							$yarn_iss_qnty=0;
							$gf_qnty_array[$row[csf('id')]]=0;
							$gp_qty_array[$row[csf('id')]]=0;
							$issQty=0;$gp_per=0;
							$daying_qnty_array[$row[csf('id')]]=0;
						}
					?>
                    </p></td>	
                     <td class="alignment_css" width="100" align="right"><?  echo  omitZero(number_format($yarn_allocation_qnty,0));?></td>
                     <td class="alignment_css" width="100"  align="right"><? echo  omitZero(number_format($yarn_iss_qnty,0));?></td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($gf_qnty_array[$row[csf('id')]],0));?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($gp_qty_array[$row[csf('id')]],0));?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($issQty,0));?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero($gp_per);?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($daying_qnty_array[$row[csf('id')]],0));?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($ff_qnty_array[$row[csf('id')]],0));?></td>	
                    <td class="alignment_css" width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? echo omitZero(number_format($fabrics_avl_qnty_array[$row[csf('id')]]),0);?>
                    </td>	
                    <td class="alignment_css" width="60" align="right">
                    	<? $blance= $ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]; 
                    	echo omitZero(number_format($blance,0));?></td>

                    <td class="alignment_css" width="60" align="right">
                    	<a href="javascript:void()" onClick="openPopup('<? echo $row['ID'];?>','Trims Value Popup','trims_value_popup')">
                    		<? echo number_format($trims_rcv_val_array[$row['ID']],2);?>
                    	</a>
                    </td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_per));?></td>	
					<td class="alignment_css" width="60" title='Print sent' align="right"><? echo omitZero(number_format($print_issue_qnty,0));?></td>	
					<td class="alignment_css" width="60" align="right"><?  echo omitZero(number_format($print_rcv_qnty,0));?></td>	
					
                    <td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($emb_issue_qnty_array[$row[csf('id')]],0));?></td>	
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($emb_rec_qnty_array[$row[csf('id')]],0));?></td>
					
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($wash_issue_qnty,0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($wash_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></td>	

                    <td class="alignment_css" width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<a href="javascript:void()" onClick="openPopup('<? echo $row["ID"];?>_<? echo $row["GMTS_ITEM_ID"];?>','Finish Qty Popup','fin_qnty_popup');">
                    		<? echo omitZero(number_format($finQty,0));?>
                    	</a>
                    </td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($inspection_qnty,0));?></td>
					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')],0));?></td>
					
					<td class="alignment_css" width="80" title="Order Qty Pcs-Ship Qty" align="right"><? echo omitZero(number_format($short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" title="Order Qty Pcs-Ship Qty" align="right"><? echo omitZero(number_format($short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right" title="Ship Qty-Order Qty Pcs"><?  echo omitZero(number_format($excess_short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" align="right" title="Ship Qty-Order Qty Pcs"><?  echo omitZero(number_format($excess_short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right"><? echo omitZero(number_format($short_ship_bal_qty,0));?></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $order_status[$row[csf('order_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $row_status[$row[csf('status_active')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $extend_shipment_mode[$row[csf('extend_ship_mode')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $sea_discount= ($row[csf('po_total_price')]*( $row[csf('sea_discount')]/100));echo number_format($sea_discount,2); ?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $air_discount= ($row[csf('po_total_price')]*( $row[csf('air_discount')]/100));echo number_format($air_discount,2);?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $team_leader_lib[$row[csf('team_leader')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $dealing_merchant_lib[$row[csf('dealing_marchant')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p>
					<? 
						//$delay_for[$row[csf('delay_for')]];
					$delay_reason = false;
					foreach (explode(",", $row['DELAY_FOR']) as $val)
					{
						$delay_reason .= $delay_reason ? ", ".$delay_for[$val] : $delay_for[$val];
					}
					echo $delay_reason;
					?>						
					</p></td>
                    <td class="alignment_css" width="150" align="left"><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?
            	
				$tot_po_quantity+=$row[csf('po_quantity')];
				$tot_yarn_allocation_qntyShow+=$yarn_allocation_qnty;
				$tot_yarn_issue_qnty+=$yarn_iss_qnty; 
				$tot_po_total_price+=$row[csf('po_total_price')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]]);
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_array[$row[csf('id')]];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$finQty;
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				$tot_print_issue_qnty+=$print_issue_qnty;
				$tot_print_rcv_qnty+=$print_rcv_qnty;
				$tot_sp_work_issue_qnty+=$sp_work_issue_qnty;
				$tot_sp_work_rcv_qnty+=$sp_work_rcv_qnty;
                $tot_wash_issue_qnty+=$wash_issue_qnty;
                $tot_wash_rcv_qnty+=$wash_rcv_qnty;
				$tot_short_ship_qty+=$short_ship_qty;
				$tot_excess_short_ship_qty+=$excess_short_ship_qty;
				$tot_short_ship_bal_qty+=$short_ship_bal_qty;
				$tot_fin_qty+=$finQty;
				$tot_inspection_qnty+=$inspection_qnty;
				
				$i++;
			} 
            ?> 
         </tbody>
		</table>
        </div>
        <table width="6290" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" id="report_table_footer" align="left">
         	<tr>
                <td class="alignment_css" width="30">&nbsp; </td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="80">&nbsp;</td>	
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="60">&nbsp;</td>	
                
                <td class="alignment_css" width="60">&nbsp;</td>
				
				<td class="alignment_css" width="100" id="td_po_quantity"> <? echo number_format ($tot_po_quantity,0);?></td>
				<td class="alignment_css" width="60">&nbsp;</td>
				<td class="alignment_css" width="100" id="td_order_value"></td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="60">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100" id="yarn_allocation_tdshow"><? echo number_format($tot_yarn_allocation_qntyShow,0);?></td>
				<td  class="alignment_css" width="100" id="yarn_issue_td" ><? echo number_format($tot_yarn_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_gp_qty"><? echo number_format($tot_gp_qty,0);?></td>
                <td class="alignment_css" width="60" id="td_gf_qnty"><? echo number_format($tot_gf_qnty,0);?></td>
                
                <td class="alignment_css" width="60" id="td_gp_to_qty"><? echo number_format($tot_grey_to_dye,0);?></td>	
                
                <td class="alignment_css" width="60" id="td_daying_qnty"><? echo number_format($tot_daying_qnty,0);?></td>	
                <td class="alignment_css" width="60" id="td_ff_qnty" ><? echo number_format($tot_ff_qnty,0);?></td>	
                <td class="alignment_css" width="60" id="td_fabrics_avl_qnty"><? echo number_format($tot_fabrics_avl_qnty,0);?></td>	
                <td class="alignment_css" width="60" id="td_blance"><? echo number_format($tot_blance,0);?></td>	
					
                <td class="alignment_css" width="60" id="td_trim_blance"></td>	
				<td class="alignment_css" width="60" id="td_trim_val"></td>
                <td class="alignment_css" width="60" id="td_cut_qnty"><? echo number_format($tot_cut_qnty,0);?></td>	
               
				<td class="alignment_css" width="60" id="td_print_sent"><? echo number_format($tot_print_issue_qnty,0);?></td>	
				<td class="alignment_css" width="60" id="td_print_recv"><? echo number_format($tot_print_rcv_qnty,0);?></td>	
                <td class="alignment_css" width="60" id="td_emb_issue_qnty"><? echo number_format($tot_emb_issue_qnty,0);?></td>	
                <td class="alignment_css" width="50" id="td_emb_rec_qnty"><? echo number_format($tot_emb_rec_qnty,0);?></td>	
				<td class="alignment_css" width="60" id="td_sp_issue_qnty"><? echo number_format($tot_sp_work_issue_qnty,0);?></td>	
				<td class="alignment_css" width="60" id="td_sp_rec_qnty" ><? echo number_format($tot_sp_work_rcv_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_wash_issue_qnty"><? echo number_format($tot_wash_issue_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_wash_rec_qnty" ><? echo number_format($tot_wash_rcv_qnty,0);?></td>

                <td class="alignment_css" width="60" id="td_sewing_in_qnty"><? echo number_format($tot_sewing_in_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_out_qnty"><? echo number_format($tot_sewing_out_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_finish_qnty"><? echo number_format($tot_fin_qty,0);?></td>
                <td class="alignment_css" width="60" id="td_inspection_qnty"><? echo number_format($tot_inspection_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_ex_qnty"><? echo number_format($tot_ex_qnty,0);?></td>	

                <td class="alignment_css" width="50" id="td_ex_qnty_val"></td>	

				 <td class="alignment_css" width="50" id="td_short_ship_qnty"><? echo number_format($tot_short_ship_qty,0);?></td>
				 <td class="alignment_css" width="80" id="td_short_ship_val"><? echo number_format($tot_short_ship_qty_val,0);?></td>
				 <td class="alignment_css" width="100" id="td_excess_ship_qnty"><? echo number_format($tot_excess_short_ship_qty,0);?></td>
				<td class="alignment_css" width="80" id="td_excess_ship_qty"></td>	
				 <td class="alignment_css" width="100" id="td_excess_ship_val"><? echo number_format($tot_excess_short_ship_val,0);?></td>
				 <td class="alignment_css" width="80" id="td_ship_bal_qnty"><? echo number_format($tot_short_ship_bal_qty,0);?></p></td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>	
      		</tr>
        </table>   
	</div>
	</fieldset>	
	<?
	echo "****1****".$type;
	/*$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	*/
    exit();
} 


if ($action=="report_generate_gmts_color_wise")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	//echo $date_type.'DDD';die;

	$status_active=str_replace("'", "",$cbo_active_status);
	$_SESSION["status_active"]="";
	$_SESSION["status_active"]=$status_active;
	if($status_active==1)
	{
		$po_tbl_cond_a=" and a.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";

	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and a.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";

	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and a.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";


	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and a.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";


	}


	/*if($date_type==1)
	{
		$date_caption="Ship Date";
	}
	else if($date_type==2)
	{
		$date_caption="Orgi. Ship Date";
	}
	else if($date_type==4)
	{
		$date_caption="Prev Ship Date";

	}
	else if($date_type==5)
	{
		$date_caption="In-Active Date";
	}
	else if($date_type==6)
	{
		$date_caption="Cancel Date";
	}
	else
		 $date_caption="Ship Date";*/
	if($date_type==4)$date_caption="Prev Ship Date"; else $date_caption="Ship Date";

	if($cbo_company_name==0 || $cbo_company_name=="")
	{
		$company_name="";$company_name2="";
	}
	else
	{
		$company_name = "and a.company_name in($cbo_company_name) ";$company_name2 = " and a.company_id in($cbo_company_name)";
	}//fabric_source//item_category

	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and a.team_leader  = $team_leader";else $team_leader_cond="";
	if($team_leader!=0) 	  $team_leader_cond2= " and d.team_leader  = $team_leader";else $team_leader_cond2="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and a.id=$txt_job_id";else $job_no_cond="";
	if($txt_po_id!='') $po_no_cond="and b.id=$txt_po_id";else $po_no_cond="";

	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}


	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	$startdate=strtotime($txt_date_from);
	$enddate=strtotime($txt_date_to);

	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from));
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}


			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{

			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$date_from=strtotime($start_date);
				$date_to=strtotime($end_date);

				$ref_closing_po_arr2=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");


				foreach($ref_closing_po_arr2 as $po_id=>$ids){
					$ref_poArr[$po_id]=$po_id;
				}
				// $ref_po_cond_for_in=where_con_using_array($ref_poArr,0,"c.id");
				// $ref_po_cond_for_in2=where_con_using_array($ref_poArr,0,"b.id");
				 $ref_po_cond_for_in3=where_con_using_array($ref_poArr,0,"b.inv_pur_req_mst_id");
				//  $ship_cond="and b.shiping_status=3";

				  $ref_closing_date_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0   $ref_po_cond_for_in3 group by b.inv_pur_req_mst_id", "po_id", "closing_date");




				foreach($ref_closing_date_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;

				}
				$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");

			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(b.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}

			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}

		}
	}

	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);


	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );

	$team_leader_lib =return_library_array( "SELECT id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );
	$agent_lib =return_library_array( "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in($cbo_company_name) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name", "id", "buyer_name"  );

	$dealing_merchant_lib =return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$department_lib=return_library_array( "select id, department_name from lib_department where  status_active=1 and is_deleted=0", "id", "department_name"  );
	$colorArr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sub_department_lib=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment where  status_active=1 and is_deleted=0", "id", "sub_department_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');


	/*$fab_sql="SELECT b.po_break_down_id as po_id,b.fabric_color_id,(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,a.booking_no_prefix_num,a.booking_no,
	(CASE WHEN b.booking_type=1 THEN b.fin_fab_qnty END) as fin_fab_qnty
	 from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and  b.po_break_down_id=c.id and d.id=c.job_id and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond2 $fab_nature_cond $fab_source_cond $search_text2";*/
	$fab_sql="SELECT b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,e.color_number_id,
	(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,a.booking_no_prefix_num,a.booking_no,
	(case when b.booking_type=1 then b.fin_fab_qnty end) as fin_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d,wo_po_color_size_breakdown e
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id
	and c.id=e.po_break_down_id and b.color_size_table_id=e.id and d.id=e.job_id and e.status_active=1
	and b.booking_type=1 and b.status_active=1
	and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond2 $fab_nature_cond $fab_source_cond $search_text2";
	// echo $fab_sql;die;
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]][$row[csf('color_number_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]][$row[csf('color_number_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];

	 }
	 unset($fab_result);
	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")";

		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";

		   }
		   $po++;
		}
	}
	//echo $po_cond_for_in;die;
	if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
	else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}
	if($db_type==0){$date_diff_2="DATEDIFF(b.shipment_date,b.po_received_date),";}
	else{$date_diff_2=" (b.shipment_date - b.po_received_date),";}

	 $sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,sum(b.po_quantity) as  po_qty,sum(b.plan_cut) as plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,sum(c.order_total) as po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept, c.color_number_id,a.pro_sub_dep,sum(c.order_quantity) as order_quantity,avg(c.order_rate) as order_rate
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  c.po_break_down_id=b.id and a.id=b.job_id $company_name and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 $ref_po_cond_for_in2 $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $job_no_cond $team_leader_cond $po_no_cond
	group by b.is_confirmed , $date_diff_2 b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.unit_price/a.total_set_qnty) ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept, c.color_number_id,a.pro_sub_dep  order by b.id asc,c.color_number_id asc";
	 //echo $sql_po;

	$order_sql_result = sql_select($sql_po);
	$order_qty_array = array();$tot_order_quantity=0;
	foreach($order_sql_result as $rows){
		//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
	if(str_replace("'","",$cbo_date_type)==7) //ref Closeing
		{
			$ref_closing_min_date=strtotime($ref_closing_date_arr[$rows[csf("id")]]);
				//  echo $rows[csf("id")].'='.$ref_closing_date_arr[$rows[csf("id")]].'='.$ref_closing_min_date.'='.$startdate.'='.$enddate.'<br>';
			if($startdate!='' && $enddate!='' && $ref_closing_min_date>=$startdate && $ref_closing_min_date<=$enddate)
			{
			$order_data_arr[]=$rows;
			$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
			$string=$rows[csf('company_name')]."**".$rows[csf('job_no_prefix_num')]."**".$rows[csf('style_ref_no')]."**".$row[csf('color_number_id')];
			$order_id_qty_array[$rows[csf('id')]][$rows[csf('color_number_id')]]['order_quantity'] +=$rows[csf('order_quantity')];
			$order_qty_array[$rows[csf('id')]] += $rows[csf('order_quantity')];
			}
		}
		else
		{
		$order_data_arr[]=$rows;
		$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
		$string=$rows[csf('company_name')]."**".$rows[csf('job_no_prefix_num')]."**".$rows[csf('style_ref_no')]."**".$row[csf('color_number_id')];
		$order_id_qty_array[$rows[csf('id')]][$rows[csf('color_number_id')]]['order_quantity'] +=$rows[csf('order_quantity')];
		$order_qty_array[$rows[csf('id')]] += $rows[csf('order_quantity')];

		$tot_order_quantity+= $rows[csf('order_quantity')];

		}

	}
	echo $tot_order_quantity.'=C';
	// echo "<pre>";print_r($order_data_arr);die();
	unset($order_sql_result);

	$po_id_list_arr=array_chunk($order_id_arr,999);
	$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";$poCon20 = " and ";
	$p=1;
	foreach($po_id_list_arr as $po_process)
	{
		if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")";
		else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";

		if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")";
		else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";

		if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")";
		else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";
		if($p==1) $poCon20 .="  ( a.po_break_down_id in(".implode(',',$po_process).")";
		else  $poCon20 .=" or a.po_break_down_id in(".implode(',',$po_process).")";


		if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")";
		else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";

		if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")";
		else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";
		$p++;
	}
	$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon20 .=")";

	$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls  Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  $poCon3";
	$yarn_allo_arr=array();

	foreach(sql_select($yarn_allo) as $v)$yarn_allo_arr[$v[csf("po_break_down_id")]][0]+=$v[csf("qnty")];

	/*$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5";
	$yarn_issue_arr=array();

	foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];*/

	$yarn_issue="SELECT po_breakdown_id as po_break_down_id, c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 AND b.transaction_type=2 and c.trans_type=2 and b.receive_basis = 3  and c.status_active = 1  $poCon5  ";
	$yarn_issue_arr=array();

	foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];

	$return_qty_arr=array();
	$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $poCon5");

	foreach ($sql_return_data as $row)
	{
		$return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];
	}


	//print_r($return_qty_arr);

	$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
	//$sqlsTrans="SELECT  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date group by  b.po_breakdown_id";

	 $sqlsTrans="SELECT a.knit_dye_source, c.po_breakdown_id, sum(c.quantity) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knit_dye_source,c.po_breakdown_id  ";

	$results_trans=sql_select( $sqlsTrans );
	foreach ($results_trans as $row)
	{
		 if($row[csf('knit_dye_source')]!=3)
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
		 if($row[csf('knit_dye_source')]==3)$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['out']+=$row[csf('qnty')];
	}
	$sql_ret="SELECT  c.po_breakdown_id, sum(c.quantity) as quantity  from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.po_breakdown_id ";
	foreach (sql_select($sql_ret) as $row)
	{
		$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['return']+=$row[csf('quantity')];
	}

	//print_r( $dataArrayYarnIssuesQty);

	// $daying_qnty_array=return_library_array( "SELECT sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");

	$daying_qnty_data=sql_select("SELECT a.color_id,sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by a.color_id,b.po_id");
	$daying_qnty_data_arr=array();
	if(!empty($daying_qnty_data))
	{
		foreach($daying_qnty_data as $row)
		{
			$daying_qnty_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]+=$row[csf('batch_qnty')];
		}
	}

	$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");

	foreach($recvData as $row)
	{
		$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
	}
	unset($recvData);

	$fabric_color_by_gmts_color=sql_select("SELECT a.po_break_down_id,a.fabric_color_id,b.color_number_id from wo_booking_dtls a,wo_po_color_size_breakdown b where a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id and a.job_no=b.job_no_mst $poCon and a.status_active=1 and b.status_active=1");
	$fabric_color_by_gmts_color_arr=array();
	if(!empty($fabric_color_by_gmts_color))
	{
		foreach($fabric_color_by_gmts_color as $row)
		{
			$fabric_color_by_gmts_color_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('fabric_color_id')]]=$row[csf('fabric_color_id')];
		}
	}
	//print_r($daying_qnty_data_arr);
	unset($fabric_color_by_gmts_color);

	$InspectData=sql_select("SELECT a.po_break_down_id,c.color_id, a.inspection_status,a.inspection_date,a.inspection_status,c.ins_qty as inspection_qnty from pro_buyer_inspection a, wo_po_break_down b,pro_buyer_inspection_breakdown c where a.id=c.mst_id and a.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.is_deleted=0 $po_tbl_cond2 $poCon ");
	foreach($InspectData as $row)
	{
		$inspect_data_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['inspection_status'].=$inspection_status[$row[csf('inspection_status')]].',';
		$inspect_data_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['inspection_date'].=change_date_format($row[csf('inspection_date')]).',';
		if($row[csf('inspection_status')]==1)
		{
			$inspect_data_arr[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['inspection_qnty']+=$row[csf('inspection_qnty')];
		}
	}
	unset($InspectData);

	$sql2="SELECT a.po_break_down_id,c.production_type,a.embel_name, b.color_number_id,c.production_qnty from pro_garments_production_mst a,wo_po_color_size_breakdown b,pro_garments_production_dtls c  WHERE a.status_active=1 and b.status_active=1  $poCon $company_name2 and a.is_deleted=0 and b.is_deleted=0  and a.po_break_down_id=b.po_break_down_id  and  a.id=c.mst_id and c.color_size_break_down_id=b.id";
	// echo $sql2;
	$dataArray_20=sql_select($sql2);

	foreach($dataArray_20 as $row)
	{

			 if($row[csf('production_type')]==1){

				$cutting_qnty_array[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]] +=$row[csf('production_qnty')];

			 }elseif($row[csf('production_type')]==4){
				$sewing_input_qnty[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]] +=$row[csf('production_qnty')];
			 }elseif($row[csf('production_type')]==5){

				$sewing_out_qnty[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]] +=$row[csf('production_qnty')];
			 }elseif($row[csf('production_type')]==8){
				$sewing_finish_qnty[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]] +=$row[csf('production_qnty')];

			 }elseif($row[csf('production_type')]==2 && $row[csf('embel_name')]==2){
				$emb_issue_qnty[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]] +=$row[csf('production_qnty')];
			 }elseif($row[csf('production_type')]==2 && $row[csf('embel_name')]==1){
				$emb_qnty_array[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['print_issue_qnty'] +=$row[csf('production_qnty')];
				// $emb_print_issue[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['print_issue_qnty'] +=$row[csf('production_qnty')];
			 }elseif($row[csf('production_type')]==3 && $row[csf('embel_name')]==1){
				$emb_qnty_array[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['print_rcv_qnty'] +=$row[csf('production_qnty')];

			 }elseif($row[csf('production_type')]==3 && $row[csf('embel_name')]==2){
				$emb_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]] +=$row[csf('production_qnty')];

			 }elseif($row[csf('production_type')]==2 && $row[csf('embel_name')]==4){
				$emb_qnty_array[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['sp_work_issue_qnty'] +=$row[csf('production_qnty')];

			 }elseif($row[csf('production_type')]==3 && $row[csf('embel_name')]==4){
				$emb_qnty_array[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['sp_work_rcv_qnty'] +=$row[csf('production_qnty')];

			 }

	}
	unset($dataArray_20);

	// print_r($emb_print_issue)."<br>";
	$ex_qnty_array=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");

	// if(return_field_value("auto_update","variable_settings_production","company_name in($cbo_company_name ) and variable_list=15 and item_category_id=2 order by id","auto_update")==2)
	// {
		//$fabrics_avl_qnty_array=return_library_array( "SELECT po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
	// }
	// else
	// {
		//$fabrics_avl_qnty_array=return_library_array( "SELECT po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");

	    $sqls="select b.po_breakdown_id,c.color_id, sum(b.quantity) as quantity
		from inv_transaction a,pro_finish_fabric_rcv_dtls c,order_wise_pro_details b
		where a.id=c.trans_id and c.id=b.dtls_id and a.item_category in(2,3) and b.entry_form in(7,37,17) $poCon5
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1
		group by b.po_breakdown_id,c.color_id ";// and a.receive_basis in(1,2,4,9)

	    foreach(sql_select($sqls) as $vals)
	    {
	    	$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]][$vals[csf("color_id")]]+=$vals[csf("quantity")];
	    }
	// }

    // ================================ conversion rate ==========================
	$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
	// print_r($convrsn_rate_library);
	$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
	// ====================================== trims rcv ===================================
	$poCon6 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
	$trims_rcv_val_array = array();
	$receive_qty_data=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity, a.rate,c.currency_id   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $poCon6  group by b.po_breakdown_id,a.rate,c.currency_id ");

    foreach($receive_qty_data as $row)
    {
    	if($row['CURRENCY_ID']==1)
    	{
			$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate;
		}
		else
		{
			$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')];
		}
    }

    $poCon7 = str_replace("b.po_breakdown_id", "b.to_po_id", $poCon6);
    // echo $poCon7;
    // ========================== Transfer In =======================
	$sql_trns_in = "SELECT b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7   and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.to_po_id";
	// echo $sql_trns_in;
	$trans_in_res = sql_select($sql_trns_in);
	$trans_in_qty_array = array();
	foreach ($trans_in_res as  $val)
	{
		$trans_in_qty_array[$val['TO_PO_ID']] = $val['QTY'];
	}
	// print_r($trans_in_qty_array);
	$poCon7 = str_replace("b.po_breakdown_id", "b.from_po_id", $poCon6);
	// ========================== Transfer Out =======================
	$sql_trns_out = "SELECT b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7 and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.from_po_id";
	// echo $sql_trns_out;
	$trans_out_res = sql_select($sql_trns_out);
	$trans_out_qty_array = array();
	foreach ($trans_out_res as  $val)
	{
		$trans_out_qty_array[$val['FROM_PO_ID']] = $val['QTY'];
	}

	ob_start();

	?>
    <fieldset>

	<div style="width:4870px" align="left">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="48" align="center" style="font-size:22px;"><? echo $report_title;?></td></tr>
            <tr>
                <td colspan="48" align="center" style="font-size:16px; font-weight:bold;">
					<?
					$comp_names="";
					$comp_arr=explode(",", $cbo_company_name);
					foreach($comp_arr as $v)
					{
						if($comp_names)$comp_names.=','.$company_lib[$v];
						else $comp_names.= $company_lib[$v];
					}
					 echo $comp_names;
					 ?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="41" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?>
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="300"  cellpadding="0" cellspacing="0" border="0" align="left" >
        	<tr>
				<td width="20" bgcolor="white"> </td><td> &nbsp;&nbsp;Full Pending&nbsp; </td>
				<td width="20" bgcolor="yellow"> </td><td>&nbsp;&nbsp;Partial&nbsp; </td>
				<td width="20" bgcolor="cyan"> </td><td>&nbsp;&nbsp;Full Shipment/Closed&nbsp; </td>
        	</tr>
        </table>
		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="4870" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">

            <thead>
                <tr style="font-size:12px">
                    <th class="alignment_css" width="30">Sl</th>
                    <th class="alignment_css" width="100">Company</th>
                    <th class="alignment_css" width="100">Buyer</th>
                    <th class="alignment_css" width="100">Agent</th>
                    <th class="alignment_css" width="80">Job No</th>
					<th class="alignment_css" width="100">Order No</th>
					<th class="alignment_css" width="100">Style No</th>
					<th class="alignment_css" width="100">Prod. Dept.</th>
					<th class="alignment_css" width="100">Prod. Sub. Dept.</th>
                    <th class="alignment_css" width="100">Item Name</th>



					<th class="alignment_css" width="60">Picture</th>
					<th class="alignment_css" width="100">PO Insert Date</th>
					<th class="alignment_css" width="80"><? echo  $date_caption;?></th>

					<th class="alignment_css" width="80">Orig.Ship Date </th>
					<th class="alignment_css" width="80">Ext.Ship Date </th>
					<th class="alignment_css" width="100">Cancel Date </th>
					<th class="alignment_css" width="100">Delete Date </th>
					<th class="alignment_css" width="60">SMV</th>
                    <th class="alignment_css" width="80">Total SMV</th>
					<th class="alignment_css" width="100">Booking No</th>
					<th class="alignment_css" width="100">Yarn Allocation</th>
					<th class="alignment_css" width="100">Yarn Issue</th>
                    <th class="alignment_css" width="60">Grey Req</th>
                    <th class="alignment_css" width="60">Grey Prod.</th>

                    <th class="alignment_css" width="60">Grey to</th>
                    <th class="alignment_css" width="60">G2D (%)</th>

					<th class="alignment_css" width="100">Gmts Color</th>
					<th class="alignment_css" width="100">QTY(Pcs)</th>

					<th class="alignment_css" width="60">Unit Price(Pcs)</th>
					<th class="alignment_css" width="100">Order Value</th>


                    <th class="alignment_css" width="60">Dyeing Qty</th>
                    <th class="alignment_css" width="60">Fabrics Req.</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Fab. Avl.</th>
                    <th class="alignment_css" width="60">Fab. Bal</th>

					<th class="alignment_css" width="60">Cutting</th>
                    <th class="alignment_css" width="60">Cut (%)</th>
					<th class="alignment_css" width="60">Print Sent</th>
					<th class="alignment_css" width="60">Print Rcv</th>

                    <th class="alignment_css" width="50">Embr Sent</th>
                    <th class="alignment_css" width="60">Embr Rcv</th>

					<th class="alignment_css" width="60">Sp.Wo Sent</th>
					<th class="alignment_css" width="60">Sp.Wo Rcv</th>

                    <th class="alignment_css" width="60">Sew. Input</th>
                    <th class="alignment_css" width="60">Sew. Output</th>

					<th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
                    <th class="alignment_css" width="60">Inspection Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Val</th>

					<th class="alignment_css" width="80">Short Ship Qty</th>
					<th class="alignment_css" width="100">Short Ship Value</th>
                    <th class="alignment_css" width="80">Excs. Ship Qty</th>
                    <th class="alignment_css" width="100">Excs. Ship Value</th>
					<th class="alignment_css" width="80">Ship Bal. Qty</th>
                    <th class="alignment_css" width="100">Shipping Status</th>
                    <th class="alignment_css" width="100">Order Status</th>
                    <th class="alignment_css" width="100">Active Status</th>

					<th class="alignment_css" width="100">Team Leader</th>
                    <th class="alignment_css" width="100">Dealing Merchant</th>
                    <th class="alignment_css" width="100">Dealy Reason</th>
                    <th class="alignment_css">Remarks</th>


                </tr>
            </thead>
        </table>
        <div style="width:4900px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body" >
            <table  width="4870"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_2">
              <tbody>
				<?php
                $i=1;$tot_po_total_price=$tot_print_issue_qnty=$tot_print_rcv_qnty=$tot_sp_work_issue_qnty=$tot_sp_work_rcv_qnty=$tot_short_ship_qty=$tot_excess_short_ship_qty=$tot_short_ship_bal_qty=$tot_yarn_allocation_qnty=$tot_yarn_issue_qnty=$tot_fin_qty=$tot_inspection_qnty=0;
                $old_ttl_ship=0;$old_ship_bal_qty=0;$old_short_ship_qty=0;$old_ssqty_p=0;$old_excess_short_ship_qty=0;$old_ess_qty=0;$old_eqty=0;


				$old_company=0;
				$old_po_number=0;
				$old_color_id=0;
				$old_shipment_date=0;$tot_po_quantity=0;
				$order_chk_array = array();

                foreach($order_data_arr as $row)
                {
					//$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=($gf_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]>0) ? round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]])*100) : 0;
					$cut_per=round(($cutting_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]/$row[csf('order_quantity')])*100);
					$finFactoryPer=round(($sewing_finish_qnty[$row[csf('id')]][$row[csf('color_number_id')]]/$row[csf('order_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}

					$fabric_color_ids = $fabric_color_by_gmts_color_arr[$row[csf('id')]][$row[csf('color_number_id')]];
					if(!empty($fabric_color_ids))
					{
						foreach($fabric_color_ids as $fabric_color_id)
						{
							$fabAvlPer+=round(($fabrics_avl_qnty_array[$row[csf('id')]][$fabric_color_id]/$ff_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]])*100);
						}
					}

					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}

					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					$yarn_iss_qnty=$yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
					$yarn_allocation_qnty=0;
					foreach($gmts_items as $gmts_id)
					{
						// $yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					$yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
					 //var_dump($itemText);die;
					$issQty=($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return'];

					$booking_full_for="";
					foreach($booking_no_array[$row[csf('id')]] as $key=>$val)$booking_full_for=$key;
					$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_full_for]['requ_no'],",")));




					$inspection_qnty=$inspect_data_arr[$row[csf('id')]][$row[csf('color_number_id')]]['inspection_qnty'];
					$inspection_status=rtrim($inspect_data_arr[$row[csf('id')]][$row[csf('color_number_id')]]['inspection_status'],',');
					$inspection_date=rtrim($inspect_data_arr[$row[csf('id')]][$row[csf('color_number_id')]]['inspection_date'],',');
					$inspection_status=implode(',',array_unique(explode(",",$inspection_status)));
					$inspection_date=implode(',',array_unique(explode(",",$inspection_date)));

					$print_issue_qnty=$row[csf('color_number_id')];
					$print_rcv_qnty=$emb_print_issue[$row[csf('id')]][$row[csf('color_number_id')]]['print_rcv_qnty'];
					$sp_work_issue_qnty=$emb_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]['sp_work_issue_qnty'];
					$sp_work_rcv_qnty=$emb_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]['sp_work_rcv_qnty'];

					$extended_ship_date=$row[csf('extended_ship_date')];


					if($row[csf('shiping_status')]==3 && $order_qty_array[$row[csf('id')]]>$ex_qnty_array[$row[csf('id')]]) //Full Shipment/Closed
					{
						$short_ship_qty=$order_qty_array[$row[csf('id')]]-$ex_qnty_array[$row[csf('id')]];
					}
					else
					{
						$short_ship_qty=0;
					}
					// $short_ship_qty=$order_qty_array[$row[csf('id')]]-$ex_qnty_array[$row[csf('id')]];

					if($row[csf('shiping_status')]==3 && $ex_qnty_array[$row[csf('id')]]>$order_qty_array[$row[csf('id')]]) //Full Shipment/Closed
					{
						$excess_short_ship_qty=$ex_qnty_array[$row[csf('id')]]-$order_qty_array[$row[csf('id')]];
						// echo $ex_qnty_array[$row[csf('id')]]."-".$order_qty_array[$row[csf('id')]]."<br>";
					}
					else
					{
						$excess_short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2 ) //Partial
					{
						$short_ship_bal_qty=$ex_qnty_array[$row[csf('id')]]-$order_qty_array[$row[csf('id')]];
					}
					else
					{
						$short_ship_bal_qty=0;
					}

					$finQty = ($sewing_finish_qnty[$row[csf('id')]][$row[csf('color_number_id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];

					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					if($row[SHIPING_STATUS]==2){$bgcolor="#FFFF00";}
					elseif($row[SHIPING_STATUS]==3){$bgcolor="#00FFFF";}
					else{$row[SHIPING_STATUS]=1;}




					if($old_company==$row[csf('company_name')] && $old_po_number==$row[csf('po_number')] && $old_color_id==$row[csf('color_number_id')] && $old_shipment_date==$row[csf('shipment_date')])
					{

					}
					else
					{

						$daying_qnty_val=$fabric_available=0;
						if(!empty($fabric_color_ids))
						{
							foreach($fabric_color_ids as $fabric_color_id)
							{
								$daying_qnty_val+=$daying_qnty_data_arr[$row[csf('id')]][$fabric_color_id];
								$fabric_available += $fabrics_avl_qnty_array[$row[csf('id')]][$fabric_color_id];

								if($cbo_fabric_source==2) //purchase
								{
									$daying_qnty_val=0;
								}
							}
						}
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;"" valign="middle">
                    <td class="alignment_css" width="30"><? echo $i;?></td>
                    <td class="alignment_css" width="100"><p><? echo $company_lib[$row[csf('company_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $agent_lib[$row[csf('agent_name')]];?></p></td>

                    <td class="alignment_css" width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $row[csf('po_number')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $row[csf('style_ref_no')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $product_dept[$row[csf('product_dept')]];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $sub_department_lib[$row[csf('pro_sub_dep')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo implode(',',$itemText);?></p></td>
					<td class="alignment_css" width="60"  align="center">
	                    <?
	                    if($imge_arr[$row[csf('job_no')]]!='')
						{
						?>
	                    <p onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></p>
	                    <?
						}
						else
						{
							echo " ";
						}
						?>
                    </td>
					<td class="alignment_css" width="100" align="center">
						<? $insert_dates=$row[csf('insert_date')]; echo date("d-M-Y", strtotime($insert_dates));?>

					</td>
						<?
		                    if(str_replace("'","",$cbo_date_type)==6 || str_replace("'","",$cbo_date_type)==5){
		                    	$originalDate = $row[csf('update_date')];
		                    }
		                    if(str_replace("'","",$cbo_date_type)==1){
		                    	$originalDate = $row[csf('pub_shipment_date')];
		                    }
		                    if(str_replace("'","",$cbo_date_type)==2){
		                    	$originalDate = $row[csf('shipment_date')];
		                    }
		                    if(str_replace("'","",$cbo_date_type)==3){
		                    	$originalDate = $row[csf('insert_date')];
		                    }
		                    if(str_replace("'","",$cbo_date_type)==4){
		                    	$originalDate = $row[csf('extended_ship_date')];
		                    }
		                    $originalDate = $row[csf('pub_shipment_date')];
	                    ?>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($originalDate));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($row[csf('shipment_date')]));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
					<td class="alignment_css" width="80" align="right"><? echo ($extended_ship_date =="") ? "" : date("d-M-Y", strtotime($extended_ship_date));?></td>

					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==3) echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==2)  echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>

					<td class="alignment_css" width="80" align="right" title="<? echo $order_qty_array[$row[csf('id')]]."*".omitZero($row[csf('set_smv')]);?>">
						<?
							if($total_smv !==$order_qty_array[$row[csf('id')]]*omitZero($row[csf('set_smv')]))
							{
								echo $order_qty_array[$row[csf('id')]]*omitZero($row[csf('set_smv')]);
								$total_smv=$order_qty_array[$row[csf('id')]]*omitZero($row[csf('set_smv')]);
							}
							?>
					</td>

					<td class="alignment_css" width="100">
						<p>
							<?
								$html=array();
								foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
								//$html[] ="<a href='$booking_no'>$booking_num</a>";
								$html[] =$booking_num;
								}
								echo implode(',',$html);
								if($cbo_fabric_source==2) //purchase
								{
									$yarn_allocation_qnty=0;
									$yarn_iss_qnty=0;
									$gf_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]=0;
									$gp_qty_array[$row[csf('id')]]=0;
									$issQty=0;$gp_per=0;
									//$daying_qnty_data_arr[$row[csf('id')]][$fabric_color_id]=0;
									//$yarn_allocation_qnty=0;

								}
							?>
						</p>
					</td>

                    <td class="alignment_css" width="100" align="right"><? if($checkDupliPOArr[$row[csf('id')]]=="") { echo omitZero(number_format($yarn_allocation_qnty,0));$single_y_a_q=$yarn_allocation_qnty;}?></td>

					<td class="alignment_css" width="100"  align="right"><? if($checkDupliPOArr[$row[csf('id')]]=="") { echo omitZero(number_format($yarn_iss_qnty,0));} ?></td>

					<td class="alignment_css" width="60" align="right"><? //if($checkDupliPOArr[$row[csf('id')]]=="") { echo omitZero(number_format($gf_qnty_array[$row[csf('id')]],0));$single_g_q=$row[csf('id')];}
					echo omitZero(number_format($gf_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]],0))
					?></td>

					<td class="alignment_css" width="60" align="right"><? if($checkDupliPOArr[$row[csf('id')]]=="") { echo omitZero(number_format($gp_qty_array[$row[csf('id')]],0));}?></td>

					<td class="alignment_css" width="60" align="right"><? if($checkDupliPOArr[$row[csf('id')]]==""){ echo omitZero(number_format($issQty,0));$s_row_issQty=$issQty;}?></td>
                    <td class="alignment_css" width="60" align="right"><? if($checkDupliPOArr[$row[csf('id')]]==""){ echo omitZero($gp_per);$s_row_gp_per =$gp_per;} $checkDupliPOArr[$row[csf('id')]] =$row[csf('id')]; ?></td>


					<td class="alignment_css" width="100" align="left"><? echo $colorArr[$row[csf('color_number_id')]];?></td>
					<td class="alignment_css" width="100" align="right"><?  //
							$order_qty=$row[csf('order_quantity')];//$order_id_qty_array[$row[csf('id')]][$row[csf('color_number_id')]]['order_quantity'];
							 echo 	$order_qty;

						?>
						</td>
				    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($row[csf('order_rate')],3))//omitZero(number_format($row[csf('unit_price')],3));?></td>
				    <td class="alignment_css" width="100" align="right"><? echo $order_qty*$row[csf('order_rate')];?></td>


                    <td class="alignment_css" width="60" align="right">
					<? echo $daying_qnty_val; ?>
					</td>
                    <td class="alignment_css" width="60" align="right">
						<?
						echo omitZero(number_format($ff_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]],0));
						$s_row_ff_q=$row[csf('id')];
						?>
					</td>
                    <td class="alignment_css" width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
					<?
						echo omitZero(number_format($fabric_available),0);
						$s_row_f_avl=$row[csf('id')];
					?>
                    </td>
                    <td class="alignment_css" width="60" align="right">
                    	<?
                    	// if($s_row_bal!==$blance)
                    	// {
                    		echo $blance=omitZero(number_format($ff_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]-$fabric_available,0));
                    		$s_row_bal=$blance;
                    	// }
                    	?>
                    </td>

					<td class="alignment_css" width="60" align="right"><? echo $cutting_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]];?></td>
                    <td class="alignment_css" width="60" align="right"><? echo $cut_per;?></td>
					<td class="alignment_css" width="60" title='Print sent' align="right"><? echo $emb_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]['print_issue_qnty'];?></td>
					<td class="alignment_css" width="60" align="right"><?  echo $emb_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]['print_rcv_qnty'];;?></td>

                    <td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($emb_issue_qnty[$row[csf('id')]][$row[csf('color_number_id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($emb_rec_qnty[$row[csf('id')]][$row[csf('color_number_id')]],0));?></td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_rcv_qnty,0));?></td>

				    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_input_qnty[$row[csf('id')]][$row[csf('color_number_id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty[$row[csf('id')]][$row[csf('color_number_id')]],0));?></td>

					<td class="alignment_css" width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row["ID"];?>_<? echo $row["GMTS_ITEM_ID"];?>','Finish Qty Popup','fin_qnty_popup');">
                    		<? echo omitZero(number_format($finQty,0));?>
                    	</A>
                    </td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($inspection_qnty,0));?></td>

					<td class="alignment_css" width="50" align="right">
					<?
					if($old_ttl_ship !==$row[csf('id')])
					{
						echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));
						$old_ttl_ship=$row[csf('id')];
					};
					?>
					</td>

					<td class="alignment_css" width="50" align="right">
						<?
						if($order_chk_array[$row[csf('id')]]['ex']=="")
						{
							echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')],0));
							$old_eqty=$ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')];
							$order_chk_array[$row[csf('id')]]['ex'] = $row[csf('id')];
						}
						/*if($old_eqty !==$ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')])
						{
							echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')],0));
							$old_eqty=$ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')];
						}*/
						?>

					</td>

					<td class="alignment_css" width="80" title="Order Qty Pcs-Ship Qty" align="right">
					<?
					if($order_chk_array[$row[csf('id')]]['short_ex']=="")
					{
						echo omitZero(number_format($short_ship_qty,0));
						$old_short_ship_qty=$short_ship_qty;
						$order_chk_array[$row[csf('id')]]['short_ex'] = $row[csf('id')];
					}

					/*if($short_ship_qty !==$old_short_ship_qty)
					{
						echo omitZero(number_format($short_ship_qty,0));
						$old_short_ship_qty=$short_ship_qty;
					}*/
					?>
					</td>
					<td class="alignment_css" width="100" title="Order Qty Pcs-Ship Qty" align="right">
					<?
					if($order_chk_array[$row[csf('id')]]['short_ex_val']=="")
					{
						echo omitZero(number_format($short_ship_qty*$row[csf('unit_price')],0));
						$old_ssqty_p=$short_ship_qty*$row[csf('unit_price')];
						$order_chk_array[$row[csf('id')]]['short_ex_val'] = $row[csf('id')];
					}

					/*if($old_ssqty_p !==$short_ship_qty*$row[csf('unit_price')])
					{
						echo omitZero(number_format($short_ship_qty*$row[csf('unit_price')],0));
						$old_ssqty_p=$short_ship_qty*$row[csf('unit_price')];
					}*/
					?>
					</td>

					<td class="alignment_css" width="80" align="right" title="Ship Qty-Order Qty Pcs">
					<?
					if($order_chk_array[$row[csf('id')]]['exces_short_ex_qty']=="")
					{
						echo omitZero(number_format($excess_short_ship_qty,0));
						$old_excess_short_ship_qty=$excess_short_ship_qty ;
						$order_chk_array[$row[csf('id')]]['exces_short_ex_qty'] = $row[csf('id')];
					}
					/*if($excess_short_ship_qty !==$old_excess_short_ship_qty)
					{
						echo omitZero(number_format($excess_short_ship_qty,0));
						$old_excess_short_ship_qty=$excess_short_ship_qty ;
					}*/
					?>
					</td>
					<td class="alignment_css" width="100" align="right" title="Ship Qty-Order Qty Pcs">
					<?
					if($order_chk_array[$row[csf('id')]]['exces_short_ex_val']=="")
					{
						echo omitZero(number_format($excess_short_ship_qty*$row[csf('unit_price')],0));
						$old_ess_qty=$excess_short_ship_qty*$row[csf('unit_price')];
						$order_chk_array[$row[csf('id')]]['exces_short_ex_val'] = $row[csf('id')];
					}

					/*if($excess_short_ship_qty*$row[csf('unit_price')] !==$old_ess_qty)
					{
						echo omitZero(number_format($excess_short_ship_qty*$row[csf('unit_price')],0));
						$old_ess_qty=$excess_short_ship_qty*$row[csf('unit_price')];
					}*/
					?>
					</td>
					<td class="alignment_css" width="80" align="right"><? if($old_ship_bal_qty !==$short_ship_bal_qty){echo omitZero(number_format($short_ship_bal_qty,0));$old_ship_bal_qty=$short_ship_bal_qty;}; ?></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $order_status[$row[csf('order_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $row_status[$row[csf('status_active')]];?></p></td>

					<td class="alignment_css" width="100" align="center"><p><? echo $team_leader_lib[$row[csf('team_leader')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $dealing_merchant_lib[$row[csf('dealing_marchant')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p>
					<?
						//$delay_for[$row[csf('delay_for')]];
					$delay_reason = false;
					foreach (explode(",", $row['DELAY_FOR']) as $val)
					{
						$delay_reason .= $delay_reason ? ", ".$delay_for[$val] : $delay_for[$val];
					}
					echo $delay_reason;
					?>
					</p></td>
                    <td class="alignment_css" ><p><? echo $row[csf('details_remarks')];?></p></td>



                </tr>
            <?

				$tot_po_quantity+=$row[csf('order_quantity')];
				$tot_yarn_allocation_qnty+=$yarn_allocation_qnty;
				$tot_yarn_issue_qnty+=$yarn_iss_qnty;
				$tot_po_total_price+=$row[csf('po_total_price')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]);
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_data_arr[$row[csf('id')]][$fabric_color_id];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]-$fabrics_avl_qnty_array[$row[csf('id')]][$fabric_color_id]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]][$fabric_color_id];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]][$row[csf('color_number_id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$finQty;
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				$tot_print_issue_qnty+=$print_issue_qnty;
				$tot_print_rcv_qnty+=$print_rcv_qnty;
				$tot_sp_work_issue_qnty+=$sp_work_issue_qnty;
				$tot_sp_work_rcv_qnty+=$sp_work_rcv_qnty;
				$tot_short_ship_qty+=$short_ship_qty;
				$tot_excess_short_ship_qty+=$excess_short_ship_qty;
				$tot_short_ship_bal_qty+=$short_ship_bal_qty;
				$tot_fin_qty+=$finQty;
				$tot_inspection_qnty+=$inspection_qnty;

					$old_company=$row[csf('company_name')];
					$old_po_number=$row[csf('po_number')];
					$old_color_id=$row[csf('color_number_id')];
					$old_shipment_date=$row[csf('shipment_date')];



				$i++;

					}

			}
            ?>
         </tbody>
		</table>
        </div>
        <table width="4870" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" id="report_table_footer">
         	<tr>
                <td class="alignment_css" width="30">&nbsp; </td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="60">&nbsp;</td>


				<td class="alignment_css" width="100" >&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="80" >&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>


				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="60">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>


                <td class="alignment_css" width="100" id="yarn_allocation_td_1"></td>

				<td class="alignment_css" width="100" id="yarn_issue_td_1"></td>
				<td class="alignment_css" width="60" id="td_gf_qnty_1"></td>
				<td class="alignment_css" width="60" id="td_gp_qty_1"></td>


                <td class="alignment_css" width="60" id="td_gp_to_qty_1"></td>
                <td class="alignment_css" width="60"  id=""></td>

                <td class="alignment_css" width="100" >&nbsp;</td>
				<td class="alignment_css" width="100" id="td_po_quantity_1" ></td>
                <td class="alignment_css" width="60" id="td_blance_1"></td>
                <td class="alignment_css" width="100" id="value_td_po_total_1"></td>

                <td class="alignment_css" width="60" id="td_daying_qnty_1"></td>
                <td class="alignment_css" width="60" id="td_ff_qnty_1"></td>

                <td class="alignment_css" width="60" id="td_fabrics_avl_qnty_1"></td>

                <td class="alignment_css" width="60" id="td_fabrics_blance_1"></td>
                <td class="alignment_css" width="60" id="td_tot_cutting"></td>
				<td class="alignment_css" width="60" ></td>
				<td class="alignment_css" width="60" id="td_print_sent_1"></td>
                <td class="alignment_css" width="50" id="td_print_recv_1"></td>
                <td class="alignment_css" width="60" id="td_emb_issue_qnty_1"></td>
				<td class="alignment_css" width="60" id="td_emb_rec_qnty_1"></td>
				<td class="alignment_css" width="60"  id="td_sp_issue_qnty_1"></td>

				<td class="alignment_css" width="60" id="td_sp_rec_qnty_1"></td>
                <td class="alignment_css" width="60" id="td_sewing_in_qnty_1"></td>
                <td class="alignment_css" width="60" id="td_sewing_out_qnty_1"></td>
                <td class="alignment_css" width="60" id="td_sewing_finish_qnty_1"></td>
                <td class="alignment_css" width="60" id="td_inspection_qnty_1"></td>

                <td class="alignment_css" width="50" id="td_ex_qnty_1"></td>

				 <td class="alignment_css" width="50" id="td_ex_qnty_val_1"></td>
				 <td class="alignment_css" width="80" id="td_short_ship_qnty_1"></td>
				 <td class="alignment_css" width="100" id="td_short_ship_val_1"></td>
				 <td class="alignment_css" width="80" id="td_excess_ship_qnty_1"></td>
				 <td class="alignment_css" width="100" id="td_excess_ship_val_1">&nbsp;</p></td>
				 <td class="alignment_css" width="80" id="td_ship_bal_qnty_1"></td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css">&nbsp;</td>
      		</tr>
        </table>
	</div>
	</fieldset>
	<?
	echo "****2";
	/*$html = ob_get_contents();
	ob_clean();

	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}

	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	*/
}

if ($action=="report_generate_summary")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	//echo $date_type.'DDD';die;

	$status_active=str_replace("'", "",$cbo_active_status);
	$_SESSION["status_active"]="";
	$_SESSION["status_active"]=$status_active;
	if($status_active==1)
	{
		$po_tbl_cond_a=" and a.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";
	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and a.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";
	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and a.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";
	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and a.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";
	}

	if($date_type==4)
		$date_caption="Prev Ship Date";
	else
		$date_caption="Ship Date";

	if($cbo_company_name==0 || $cbo_company_name=="")
	{
		$company_name="";$company_name2="";
	}
	else
	{
		$company_name = "and a.company_name in($cbo_company_name) ";
		$company_name2 = " and a.company_id in($cbo_company_name)";
	}

	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and a.team_leader  = $team_leader";else $team_leader_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and a.id=$txt_job_id";else $job_no_cond="";
	if($txt_po_id!='') $po_no_cond="and b.id=$txt_po_id";else $po_no_cond="";

	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}

	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from));
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}


			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{

			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(b.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}

			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}

		}
	}
	$startdate=strtotime(str_replace("'","",trim($txt_date_from)));
	$enddate=strtotime(str_replace("'","",trim($txt_date_to)));

		if(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$ref_closing_po_arr2=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");


				foreach($ref_closing_po_arr2 as $po_id=>$ids){
					$ref_poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($ref_poArr,0,"c.id");
				// $ref_po_cond_for_in2=where_con_using_array($ref_poArr,0,"b.id");
				 $ref_po_cond_for_in3=where_con_using_array($ref_poArr,0,"b.inv_pur_req_mst_id");
				  $ship_cond="and b.shiping_status=3";

				  $ref_closing_date_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0   $ref_po_cond_for_in3 group by b.inv_pur_req_mst_id", "po_id", "closing_date");



				foreach($ref_closing_date_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;

				}
				$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");

			}

	//function for omit zero value
	function omitZero($value)
	{
		if($value==0){
			return "";
		}
		else {
			return $value;
		}
	}

	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );

	$fab_sql="SELECT b.po_break_down_id as po_id ,(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,a.booking_no_prefix_num,a.booking_no,
	(CASE WHEN b.booking_type=1 THEN b.fin_fab_qnty END) as fin_fab_qnty
	 from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and  b.po_break_down_id=c.id and d.job_no=c.job_no_mst and d.job_no=b.job_no  and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text2";
	 //echo $fab_sql;die;
	$po_ids='';
	$po_id_arr=array();
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];
		 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
		 {
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		 }
	 }
	 unset($fab_result);

	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		$all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")";

		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";

		   }
		   $po++;
		}
	 }
	// echo where_con_using_array($po_id_arr,0,'b.id');die;
	if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond="";

		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

		/*$sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as po_qty,b.plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 $po_tbl_cond2 $ref_po_cond_for_in2 $search_text $buyer_id_cond $shipping_status_cond  $job_no_cond $team_leader_cond $po_no_cond   $fnc_cond  order by a.buyer_name ASC"; */
	/*	$sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(c.order_quantity) as po_quantity,c.order_quantity as po_qty,c.plan_cut_qnty as plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,c.order_total as po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and  a.id=c.job_id and c.po_break_down_id=b.id  $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_tbl_cond2 $ref_po_cond_for_in2 $search_text $buyer_id_cond $shipping_status_cond  $job_no_cond $team_leader_cond $po_no_cond   $fnc_cond  order by a.buyer_name ASC"; */
		$sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as po_qty,b.plan_cut as plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price as po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0   $po_tbl_cond2 $ref_po_cond_for_in2 $search_text $buyer_id_cond $shipping_status_cond  $job_no_cond $team_leader_cond $po_no_cond   $fnc_cond  order by a.buyer_name ASC";
		//echo $sql_po;//die;

		$order_sql_result = sql_select($sql_po);
		foreach($order_sql_result as $rows){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			if(str_replace("'","",$cbo_date_type)==7)
			{
				//cal_date_to cal_date_from
				$ref_closing_min_date=strtotime($ref_closing_date_arr[$rows[csf("id")]]);
				//echo $ref_closing_min_date.'D=';
				if($startdate!='' && $enddate!='' && $ref_closing_min_date>=$startdate && $ref_closing_min_date<=$enddate)
				{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				}

			}
			else
			{
			$order_data_arr[]=$rows;
			$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
			}
		}
		//die;
		unset($order_sql_result);
	//echo $po_cond_for_in.'DDDDD';die;

		$po_id_list_arr=array_chunk($order_id_arr,999);
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")";
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";

			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";

			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";
			$p++;
		}
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";

		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null) $poCon3  ";
		$yarn_allo_arr=array();
		foreach(sql_select($yarn_allo) as $v)$yarn_allo_arr[$v[csf("po_break_down_id")]][0]+=$v[csf("qnty")];


		$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];

		$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
		and  c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];


		/* $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();
		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];

		 $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       and b.receive_basis = 3  and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();
		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")]; */

		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $poCon5");
		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];
		}
		//print_r($yarn_allo_arr);

		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		//$sqlsTrans="SELECT  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date group by  b.po_breakdown_id";

		 $sqlsTrans="SELECT a.knit_dye_source, c.po_breakdown_id, sum(c.quantity) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knit_dye_source,c.po_breakdown_id  ";
		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			 if($row[csf('knit_dye_source')]!=3)
				$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			 if($row[csf('knit_dye_source')]==3)$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['out']+=$row[csf('qnty')];
		}
		$sql_ret="SELECT  c.po_breakdown_id, sum(c.quantity) as quantity  from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.po_breakdown_id ";
		foreach (sql_select($sql_ret) as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['return']+=$row[csf('quantity')];
		}

		//print_r( $dataArrayYarnIssuesQty);
		$daying_qnty_array=return_library_array( "SELECT sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");

		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);

		$InspectData=sql_select("select a.po_break_down_id, a.inspection_status,a.inspection_date from pro_buyer_inspection a, wo_po_break_down b where a.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 $po_tbl_cond2 $poCon ");
		foreach($InspectData as $row)
		{
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_status'].=$inspection_status[$row[csf('inspection_status')]].',';
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_date'].=change_date_format($row[csf('inspection_date')]).',';
		}
		unset($InspectData);

		$sql="SELECT po_break_down_id,
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut,
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing,
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish,
		sum(CASE WHEN production_type =2 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembissue,
		sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembrec,
		sum(CASE WHEN production_type =2 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_rcv_qnty,
		sum(CASE WHEN production_type =2 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_rcv_qnty
		from pro_garments_production_mst WHERE status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id";
		// echo $sql;
		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];

			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_issue_qnty']=$row[csf('print_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_rcv_qnty']=$row[csf('print_rcv_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_issue_qnty']=$row[csf('sp_work_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_rcv_qnty']=$row[csf('sp_work_rcv_qnty')];
		}
		unset($dataArray);
		$ex_qnty_array=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
		// if(return_field_value("auto_update","variable_settings_production","company_name in($cbo_company_name ) and variable_list=15 and item_category_id=2 order by id","auto_update")==2)
		// {
			/*$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");*/
		// }
		// else
		// {
			/*$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
		    $sqls="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.receive_basis in(1,2) and a.item_category in(2,3) and b.entry_form in(37,17)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";
		    foreach(sql_select($sqls) as $vals)
		    {
		    	$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
		    }*/

		    $sqls="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
		    foreach(sql_select($sqls) as $vals)
		    {
		    	$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
		    }
		// }
        // ================================ conversion rate ==========================
		$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
		// print_r($convrsn_rate_library);
		$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
		// ====================================== trims rcv ===================================
		$poCon6 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
		$trims_rcv_val_array = array();
		$receive_qty_data=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity, a.rate,c.currency_id   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $poCon6  group by b.po_breakdown_id,a.rate,c.currency_id ");

        foreach($receive_qty_data as $row)
        {
        	if($row['CURRENCY_ID']==1)
        	{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate;
			}
			else
			{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')];
			}
        }
        // print_r($trims_rcv_val_array);
        $poCon7 = str_replace("b.po_breakdown_id", "b.to_po_id", $poCon6);
        // echo $poCon7;
        // ========================== Transfer In =======================
		$sql_trns_in = "SELECT b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7   and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.to_po_id";
		// echo $sql_trns_in;
		$trans_in_res = sql_select($sql_trns_in);
		$trans_in_qty_array = array();
		foreach ($trans_in_res as  $val)
		{
			$trans_in_qty_array[$val['TO_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_in_qty_array);
		$poCon7 = str_replace("b.po_breakdown_id", "b.from_po_id", $poCon6);
		// ========================== Transfer Out =======================
		$sql_trns_out = "SELECT b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7 and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.from_po_id";
		// echo $sql_trns_out;
		$trans_out_res = sql_select($sql_trns_out);
		$trans_out_qty_array = array();
		foreach ($trans_out_res as  $val)
		{
			$trans_out_qty_array[$val['FROM_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_out_qty_array);

	$printData = array();
	$totalArr = array();
	foreach($order_data_arr as $row)
    {
		$printData[$row[csf('buyer_name')]]['buyer_name'] 				= $row[csf('buyer_name')];
		$printData[$row[csf('buyer_name')]]['qty'] 						+= $row[csf('po_quantity')];
		$printData[$row[csf('buyer_name')]]['order_value'] 				+= $row[csf('po_total_price')];
		$printData[$row[csf('buyer_name')]]['avg_smv'] 					+= $row[csf('set_smv')]*$row[csf('po_qty')];
		$printData[$row[csf('buyer_name')]]['grey_required']			+= round($gf_qnty_array[$row[csf('id')]]);
		$printData[$row[csf('buyer_name')]]['yarn_allocation'] 			+= $yarn_allo_arr[$row[csf('id')]][0];
		$printData[$row[csf('buyer_name')]]['yarn_issue'] 				+= $yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
		$printData[$row[csf('buyer_name')]]['grey_production'] 			+= $gp_qty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['grey_to_dye'] 				+= ($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return'];
		$printData[$row[csf('buyer_name')]]['dyeing_qty'] 				+= $daying_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['fabric_required'] 			+= round($ff_qnty_array[$row[csf('id')]]);
		$printData[$row[csf('buyer_name')]]['fabric_available'] 		+= $fabrics_avl_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['balance'] 					+= round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
		$printData[$row[csf('buyer_name')]]['cutting_qty'] 				+= $cut_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['emb_issue'] 				+= $emb_issue_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['emb_receive'] 				+= $emb_rec_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['sewing_in'] 				+= $sewing_in_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['sewing_out'] 				+= $sewing_out_qnty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['finish_qty'] 				+= ($sewing_finish_qnty_array[$row[csf('id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];
		$printData[$row[csf('buyer_name')]]['ex_factory'] 				+= $ex_qnty_array[$row[csf('id')]];

		//toal
		$totalArr['qty'] 					+= $row[csf('po_quantity')];
		$totalArr['order_value'] 			+= $row[csf('po_total_price')];
		$totalArr['avg_smv'] 				+= $row[csf('set_smv')]*$row[csf('po_qty')];
		$totalArr['grey_required']			+= round($gf_qnty_array[$row[csf('id')]]);
		$totalArr['yarn_allocation'] 		+= $yarn_allo_arr[$row[csf('id')]][0];
		$totalArr['yarn_issue'] 			+= $yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
		$totalArr['grey_production'] 		+= $gp_qty_array[$row[csf('id')]];
		$totalArr['grey_to_dye'] 			+= ($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return'];
		$totalArr['dyeing_qty'] 			+= $daying_qnty_array[$row[csf('id')]];
		$totalArr['fabric_required'] 		+= round($ff_qnty_array[$row[csf('id')]]);
		$totalArr['fabric_available'] 		+= $fabrics_avl_qnty_array[$row[csf('id')]];
		$totalArr['balance'] 				+= round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
		$totalArr['cutting_qty'] 			+= $cut_qnty_array[$row[csf('id')]];
		$totalArr['emb_issue'] 				+= $emb_issue_qnty_array[$row[csf('id')]];
		$totalArr['emb_receive'] 			+= $emb_rec_qnty_array[$row[csf('id')]];
		$totalArr['sewing_in'] 				+= $sewing_in_qnty_array[$row[csf('id')]];
		$totalArr['sewing_out'] 			+= $sewing_out_qnty_array[$row[csf('id')]];
		$totalArr['finish_qty'] 			+= ($sewing_finish_qnty_array[$row[csf('id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];
		$totalArr['ex_factory'] 			+= $ex_qnty_array[$row[csf('id')]];
	}
	//echo "<pre>";
	//print_r($printData); die;

	ob_start();
	?>
	<style type="text/css">
		table tr td{word-break: break-all;word-wrap: break-word;}
    </style>
    <fieldset>
	<div style="width:2470px" align="left">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="25" align="center" style="font-size:22px;">Buyer Wise Summary</td></tr>
            <tr>
                <td colspan="25" align="center" style="font-size:16px; font-weight:bold;">
					<?
					$comp_names="";
					$comp_arr=explode(",", $cbo_company_name);
					foreach($comp_arr as $v)
					{
						if($comp_names)$comp_names.=','.$company_lib[$v];
						else $comp_names.= $company_lib[$v];
					}
					echo $comp_names;
					?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="25" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?>
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <?
			}
			?>
        </table>
        <table width="2470" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2">
            <thead>
                <tr style="font-size:12px">
                    <th width="50">Sl</th>
                    <th width="100">Buyer</th>
                    <th width="100">Order QTY</th>
                    <th width="100">Order Value</th>
                    <th width="100">Avg. SMV</th>
                    <th width="100">Grey Required</th>
					<th width="100">Yarn Allocation</th>
                    <th width="100">Yarn Issue</th>
                    <th width="100">Grey Prod.</th>
                    <th width="100">Grey to Dye</th>
					<th width="100">G2D (%)</th>
					<th width="100">Dyeing</th>
					<th width="100">Fabrics Required</th>
                    <th width="100">Fabrics Available</th>
					<th width="100">Balance</th>
					<th width="100">Cut Qty</th>
					<th width="100">Cut %</th>
					<th width="100">Emb. Issue</th>
					<th width="100">Emb. Receive</th>
					<th width="100">Sewing Input</th>
                    <th width="100">Sewing Output</th>
                    <th width="100">Finish Qty</th>
					<th width="100">Finish %</th>
					<th width="100">Ex-Factory Qty</th>
					<th>Ex-Factory %</th>
                </tr>
            </thead>
        </table>
        <div style="width:2470px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;"" id="scroll_body" >
            <table  width="2450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tbody>
				<?php
				$i = 0;
                foreach($printData as $buyerId=>$row)
                {
                	$i++;
					$bgcolor = ($i%2==0)?"#E9F3FF":"#FFFFFF";

					//for avg_smv
					$avg_smv = ($row['avg_smv']/$row['qty']);

					//g2d_percentage
					$g2d_percentage = $row['grey_to_dye']/$row['grey_required']*100;

					//cut percentage
					$cutting_percentage = $row['cutting_qty']/$row['qty']*100;

					//finish_precentage
					$finish_precentage = $row['finish_qty']/$row['qty']*100;

					//ex_factory_percentage
					$ex_factory_percentage = $row['ex_factory']/$row['qty']*100;

						if($cbo_fabric_source==2) //purchase
						{
							$row['yarn_allocation']=0;
							$row['yarn_issue']=0;
							$row['grey_production']=0;
							$row['grey_to_dye']=0;
							$issQty=0;$g2d_percentage=0;
							$row['dyeing_qty']=0;
							$totalArr['yarn_allocation']=0;
							$totalArr['yarn_issue']=0;
							$totalArr['grey_production']=0;
							$totalArr['grey_to_dye']=0;
							//$g2d_percentage=0;
							$totalArr['dyeing_qty']=0;


						}

					?>
                    <tr bgcolor="<?php echo $bgcolor; ?>" align="right" onClick="change_color('str_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="str_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    	<td width="50" align="center"><?php echo $i; ?></td>
                        <td width="100" align="left"><?php echo $buyer_library[$row['buyer_name']]; ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['qty'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['order_value'],0)); ?></td>
                    	<td width="100" title="<? echo $row['avg_smv'];?>"><?php echo omitZero(number_format($avg_smv,2)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['grey_required'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['yarn_allocation'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['yarn_issue'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['grey_production'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['grey_to_dye'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($g2d_percentage,2)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['dyeing_qty'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['fabric_required'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['fabric_available'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['balance'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['cutting_qty'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($cutting_percentage,2)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['emb_issue'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['emb_receive'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['sewing_in'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['sewing_out'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['finish_qty'],0)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($finish_precentage,2)); ?></td>
                    	<td width="100"><?php echo omitZero(number_format($row['ex_factory'],0)); ?></td>
                    	<td><?php echo omitZero(number_format($ex_factory_percentage,2)); ?></td>
                    </tr>
					<?php
                }
                ?>
                </tbody>
                <tfoot>
                	<tr align="right">
                    	<th colspan="2">Total</th>
                    	<th><?php echo omitZero(number_format($totalArr['qty'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['order_value'],0)); ?></th>
                    	<th>&nbsp;</th>
                    	<th><?php echo omitZero(number_format($totalArr['grey_required'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['yarn_allocation'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['yarn_issue'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['grey_production'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['grey_to_dye'],0)); ?></th>
                    	<th>&nbsp;</th>
                    	<th><?php echo omitZero(number_format($totalArr['dyeing_qty'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['fabric_required'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['fabric_available'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['balance'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['cutting_qty'],0)); ?></th>
                    	<th>&nbsp;</th>
                    	<th><?php echo omitZero(number_format($totalArr['emb_issue'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['emb_receive'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['sewing_in'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['sewing_out'],0)); ?></th>
                    	<th><?php echo omitZero(number_format($totalArr['finish_qty'],0)); ?></th>
                    	<th>&nbsp;</th>
                    	<th><?php echo omitZero(number_format($totalArr['ex_factory'],0)); ?></th>
                    	<th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?php
	$html = ob_get_contents();
	ob_clean();

	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename)
	{
		@unlink($filename);
	}

	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename****$type";
	exit();
}



if ($action=="report_generate_show2")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	$type=str_replace("'","",$type);
	//echo $date_type.'DDD';die;

	$status_active=str_replace("'", "",$cbo_active_status);
	$_SESSION["status_active"]="";
	$_SESSION["status_active"]=$status_active;
	if($status_active==1)
	{
		$po_tbl_cond_a=" and a.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";

	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and a.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";

	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and a.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";


	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and a.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";


	}
	if($date_type==4)$date_caption="Prev Ship Date"; else $date_caption="Ship Date";

	if($cbo_company_name==0 || $cbo_company_name=="")
	{
		$company_name="";$company_name2="";
	}
	else
	{
		$company_name = "and a.company_name in($cbo_company_name) ";$company_name2 = " and a.company_id in($cbo_company_name)";
	}//fabric_source//item_category

	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and a.team_leader  = $team_leader";else $team_leader_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and a.id=$txt_job_id";else $job_no_cond="";
	if($txt_job_id!='') $job_no_cond3="and d.id=$txt_job_id";else $job_no_cond3="";
	if($txt_po_id!='') $po_no_cond="and b.id=$txt_po_id";else $po_no_cond="";
	if($txt_po_id!='') $po_no_cond3="and c.id=$txt_po_id";else $po_no_cond3="";

	$search_text='';$search_text2='';$search_text3='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}

	if(str_replace("'","",$txt_style_ref)!=""){$search_text3 = " and d.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text3 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text3 .= " and c.po_number like '%".str_replace("'","",$txt_order_no)."%'";}



	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	$startdate=strtotime(str_replace("'","",trim($txt_date_from)));
	$enddate=strtotime(str_replace("'","",trim($txt_date_to)));
	//echo $cal_date_to.'='.$cal_date_from;die;
	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from));
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$end_date = date('Y-m-d',strtotime($txt_date_to));
				//$search_text .=" and b.closing_date between '".$start_date."' and '".$end_date."'";
				$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");

				foreach($ref_closing_po_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($poArr,0,"c.id");
				 $ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");
				 $ship_cond="and b.shiping_status=3";

			}

			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{

			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(b.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{

				//$search_text .=" and b.closing_date between '".$start_date."' and '".$end_date."'";
				$ref_closing_po_arr2=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");



				foreach($ref_closing_po_arr2 as $po_id=>$ids){
					$ref_poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($ref_poArr,0,"c.id");
				// $ref_po_cond_for_in2=where_con_using_array($ref_poArr,0,"b.id");
				 $ref_po_cond_for_in3=where_con_using_array($ref_poArr,0,"b.inv_pur_req_mst_id");
				  $ship_cond="and b.shiping_status=3";

				  $ref_closing_date_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0   $ref_po_cond_for_in3 group by b.inv_pur_req_mst_id", "po_id", "closing_date");



				foreach($ref_closing_date_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;

				}
				$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");


			}

			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}

		}
	}

	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);


	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );

	$team_leader_lib =return_library_array( "SELECT id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );
	$agent_lib =return_library_array( "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in($cbo_company_name) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name", "id", "buyer_name"  );

	$dealing_merchant_lib =return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$department_lib=return_library_array( "select id, department_name from lib_department where  status_active=1 and is_deleted=0", "id", "department_name"  );
	$sub_department_lib=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment where  status_active=1 and is_deleted=0", "id", "sub_department_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');


	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (111,112,113)");
	if($r_id)
	{
		oci_commit($con);
	}

	 $fab_sql="select a.id as booking_id,b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,e.color_number_id,a.booking_no_prefix_num,a.booking_no,
	(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,
	(case when b.booking_type=1 then b.fin_fab_qnty end) as fin_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d,wo_po_color_size_breakdown e
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id
	and c.id=e.po_break_down_id and b.color_size_table_id=e.id and d.id=e.job_id and e.status_active=1
	and b.booking_type=1 and b.status_active=1
	and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text2 $ref_po_cond_for_in $job_no_cond3 $po_no_cond3";

	// echo $fab_sql;die;
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];
		if($cbo_fabric_nature!=0 || $cbo_fabric_nature!=0)
		 {
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		 }

	 }


	unset($fab_result);


 	$fab_sql_info="select a.id as booking_id,b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,a.booking_no_prefix_num,a.booking_no,
	(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,
	(case when b.booking_type=1 then b.fin_fab_qnty end) as fin_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id
	and b.booking_type=1 and b.status_active=1
	and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text3 $ref_po_cond_for_in";

	$fab_result_info = sql_select($fab_sql_info);// or die(mysql_error());
	 foreach($fab_result_info as $row)
	 {
	 	$bookingIdArr[$row[csf('booking_id')]]=$row[csf('booking_id')];
	 }
	$bookingIdArr = array_filter($bookingIdArr);
	$bookingIdArr = array_unique(explode(",",implode(",", $bookingIdArr)));
	if(!empty($bookingIdArr))
	{
	 	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 111, 1,$bookingIdArr, $empty_arr);//booking no
	}
	unset($fab_result_info);

	$sql_tex_fso_recv=sql_select("SELECT a.id as sales_id,a.sales_booking_no,a.booking_id,a.po_company_id,a.job_no as sales_no,c.batch_id,c.prod_id,c.receive_qnty
	from fabric_sales_order_mst a,GBL_TEMP_ENGINE b,PRO_FINISH_FABRIC_RCV_DTLS c,inv_transaction d
	where a.booking_id=b.ref_val and b.ref_val=c.booking_id and a.sales_booking_no=c.booking_no and c.trans_id=d.id and d.TRANSACTION_TYPE=1 and d.item_category=2 and b.user_id=$user_id and b.entry_form=111");
	foreach($sql_tex_fso_recv as $row)
	{
		$revQntyArr[$row[csf('sales_booking_no')]]['receive_qnty']=$row[csf('receive_qnty')];
	 	$batchIdArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	 	$prodIdArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
	}
	$batchIdArr = array_filter($batchIdArr);
	$batchIdArr = array_unique(explode(",",implode(",", $batchIdArr)));
	if(!empty($batchIdArr))
	{
	 	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 112, 1,$batchIdArr, $empty_arr);//booking no
	}
	$prodIdArr = array_filter($prodIdArr);
	$prodIdArr = array_unique(explode(",",implode(",", $prodIdArr)));
	if(!empty($prodIdArr))
	{
	 	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 113, 1,$prodIdArr, $empty_arr);//booking no
	}
	unset($sql_tex_fso_recv);

	//echo $sql_sales_po="SELECT a.id as sales_id,a.sales_booking_no,a.booking_id,a.po_company_id,a.job_no as sales_no from fabric_sales_order_mst a,GBL_TEMP_ENGINE b,PRO_FINISH_FABRIC_RCV_DTLS c  where a.booking_id=b.ref_val and b.ref_val=c.batch_id and a.sales_booking_no=c.booking_no and b.user_id=$user_id and b.entry_form=112";






 


	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")";

		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";

		   }
		   $po++;
		}
	 }
	 if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond="";
	//echo $po_cond_for_in;die;
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

		 $sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep  from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $fnc_cond $job_no_cond $team_leader_cond $po_no_cond $ref_po_cond_for_in2 $ship_cond order by b.pub_shipment_date desc";
		 // echo $sql_po;

		$order_sql_result = sql_select($sql_po);
		foreach($order_sql_result as $rows){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			if(str_replace("'","",$cbo_date_type)==7)
			{
				//cal_date_to cal_date_from
				$ref_closing_min_date=strtotime($ref_closing_date_arr[$rows[csf("id")]]);
				//echo $ref_closing_min_date.'D=';
				if($startdate!='' && $enddate!='' && $ref_closing_min_date>=$startdate && $ref_closing_min_date<=$enddate)
				{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				}

			}
			else
			{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
			}
		}
		unset($order_sql_result);
		$po_id_list_arr=array_chunk($order_id_arr,999);
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")";
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";

			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";

			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";
			$p++;
		}
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";

		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  $poCon3  ";
		$yarn_allo_arr=array();

		foreach(sql_select($yarn_allo) as $v)$yarn_allo_arr[$v[csf("po_break_down_id")]][0]+=$v[csf("qnty")];

		$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];

		 $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       and b.receive_basis = 3  and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];


		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $poCon5");

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];

		}





		//print_r($yarn_allo_arr);

		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		//$sqlsTrans="SELECT  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date group by  b.po_breakdown_id";

		 $sqlsTrans="SELECT a.knit_dye_source, c.po_breakdown_id, sum(c.quantity) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knit_dye_source,c.po_breakdown_id  ";

		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			 if($row[csf('knit_dye_source')]!=3)
				$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			 if($row[csf('knit_dye_source')]==3)$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['out']+=$row[csf('qnty')];
		}
		$sql_ret="SELECT  c.po_breakdown_id, sum(c.quantity) as quantity  from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.po_breakdown_id ";
		foreach (sql_select($sql_ret) as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['return']+=$row[csf('quantity')];
		}

		//print_r( $dataArrayYarnIssuesQty);
		$daying_qnty_array=return_library_array( "SELECT sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and a.entry_form=0 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");

		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);

		$InspectData=sql_select("select a.po_break_down_id, a.inspection_status,a.inspection_date,a.inspection_status,a.inspection_qnty from pro_buyer_inspection a, wo_po_break_down b where a.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 $po_tbl_cond2 $poCon ");
		foreach($InspectData as $row)
		{
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_status'].=$inspection_status[$row[csf('inspection_status')]].',';
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_date'].=change_date_format($row[csf('inspection_date')]).',';
			if($row[csf('inspection_status')]==1)
			{
				$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_qnty']+=$row[csf('inspection_qnty')];
			}
		}
		unset($InspectData);

		$sql="SELECT po_break_down_id,
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut,
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing,
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish,
		sum(CASE WHEN production_type =2 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembissue,
		sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembrec,
		sum(CASE WHEN production_type =2 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_rcv_qnty,
		sum(CASE WHEN production_type =2 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_rcv_qnty
		from pro_garments_production_mst WHERE status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id";
		// echo $sql;
		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];

			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_issue_qnty']=$row[csf('print_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_rcv_qnty']=$row[csf('print_rcv_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_issue_qnty']=$row[csf('sp_work_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_rcv_qnty']=$row[csf('sp_work_rcv_qnty')];

		}
		unset($dataArray);
		$ex_qnty_array=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
		// if(return_field_value("auto_update","variable_settings_production","company_name in($cbo_company_name ) and variable_list=15 and item_category_id=2 order by id","auto_update")==2)
		// {
			//$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
			//echo "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id";


		// }
		// else
		// {
			//$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
			//echo "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id";
		     $sqls="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
		    foreach(sql_select($sqls) as $vals)
		    {
		    	$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
		    }
		// }

		/*echo "<pre>";
		print_r($fabrics_avl_qnty_array);
		echo "</pre>";*/
        // ================================ conversion rate ==========================
		$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
		// print_r($convrsn_rate_library);
		$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
		// ====================================== trims rcv ===================================
		$poCon6 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
		$trims_rcv_val_array = array();
		$receive_qty_data=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity, a.rate,c.currency_id   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $poCon6  group by b.po_breakdown_id,a.rate,c.currency_id ");

        foreach($receive_qty_data as $row)
        {
        	if($row['CURRENCY_ID']==1)
        	{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate;
			}
			else
			{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')];
			}
        }
        // print_r($trims_rcv_val_array);
        $poCon7 = str_replace("b.po_breakdown_id", "b.to_po_id", $poCon6);
        // echo $poCon7;
        // ========================== Transfer In =======================
		$sql_trns_in = "SELECT b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7   and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.to_po_id";
		// echo $sql_trns_in;
		$trans_in_res = sql_select($sql_trns_in);
		$trans_in_qty_array = array();
		foreach ($trans_in_res as  $val)
		{
			$trans_in_qty_array[$val['TO_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_in_qty_array);
		$poCon7 = str_replace("b.po_breakdown_id", "b.from_po_id", $poCon6);
		// ========================== Transfer Out =======================
		$sql_trns_out = "SELECT b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7 and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.from_po_id";
		// echo $sql_trns_out;
		$trans_out_res = sql_select($sql_trns_out);
		$trans_out_qty_array = array();
		foreach ($trans_out_res as  $val)
		{
			$trans_out_qty_array[$val['FROM_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_out_qty_array);
	ob_start();
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (111,112,113)");
	if($r_id)
	{
		oci_commit($con);
	}
	
	?>
    <fieldset>
	<div style="width:5710px" align="left">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="49" align="center" style="font-size:22px;"><? echo $report_title;?></td></tr>
            <tr>
                <td colspan="49" align="center" style="font-size:16px; font-weight:bold;">
					<?
					$comp_names="";
					$comp_arr=explode(",", $cbo_company_name);
					foreach($comp_arr as $v)
					{
						if($comp_names)$comp_names.=','.$company_lib[$v];
						else $comp_names.= $company_lib[$v];
					}
					 echo $comp_names;
					 ?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="49" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?>
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="300"  cellpadding="0" cellspacing="0" border="0" align="left" >
        	<tr>
				<td width="20" bgcolor="white"> </td><td> &nbsp;&nbsp;Full Pending&nbsp; </td>
				<td width="20" bgcolor="yellow"> </td><td>&nbsp;&nbsp;Partial&nbsp; </td>
				<td width="20" bgcolor="cyan"> </td><td>&nbsp;&nbsp;Full Shipment/Closed&nbsp; </td>
        	</tr>
        </table>
		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="5770" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">

            <thead>
                <tr style="font-size:12px">
                    <th class="alignment_css" width="30">Sl</th>
                    <th class="alignment_css" width="100">Company</th>
                    <th class="alignment_css" width="100">Buyer</th>
                    <th class="alignment_css" width="100">Agent</th>

                    <th class="alignment_css" width="80">Job No</th>
                    <th class="alignment_css" width="100">Order No</th>
					<th class="alignment_css" width="100">Style No</th>
					<th class="alignment_css" width="100">Prod. Dept.</th>
					<th class="alignment_css" width="100">Prod. Sub. Dept.</th>

                    <th class="alignment_css" width="100">Item Name</th>
                    <th class="alignment_css" width="60">File</th>
                    <th class="alignment_css" width="60">Picture</th>
                    <th class="alignment_css" width="100">QTY(Pcs)</th>

					<th class="alignment_css" width="60">Unit Price(Pcs)</th>
					<th class="alignment_css" width="100">Order Value</th>
					<th class="alignment_css" width="100">PO Insert Date</th>
					<th class="alignment_css" width="100">PO Receive Date</th>
                    <th class="alignment_css" width="80"><? echo  $date_caption;?></th>

					<th class="alignment_css" width="80">Orig.Ship Date </th>
					<th class="alignment_css" width="80">Ext.Ship Date </th>
					<th class="alignment_css" width="80">Ref.Close Date </th>
                    <th class="alignment_css" width="100">Cancel Date </th>
					<th class="alignment_css" width="100">Delete Date </th>

					<th class="alignment_css" width="80">FRI Date</th>
					<th class="alignment_css" width="80">FRI Result</th>

                    <th class="alignment_css" width="60">SMV</th>
                    <th class="alignment_css" width="80">Total SMV</th>
					<th class="alignment_css" width="100">Booking No</th>
					<th class="alignment_css" width="100">Yarn Allocation</th>
					<th class="alignment_css" width="100">Yarn Issue</th>
                    <th class="alignment_css" width="60">Grey Req</th>
                    <th class="alignment_css" width="60">Grey Prod.</th>
                    <th class="alignment_css" width="60">Grey to</th>
                    <th class="alignment_css" width="60">G2D (%)</th>
                    <th class="alignment_css" width="60">Dyeing Qty</th>
                    <th class="alignment_css" width="60">Fabrics Req.</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Fab. Avl.</th>
                    <th class="alignment_css" width="60">Fab. Bal</th>
                    <th class="alignment_css" width="60">Trims Rcv Val</th>
                    <th class="alignment_css" width="60">Cutting</th>
                    <th class="alignment_css" width="60">Cut (%)</th>
					<th class="alignment_css" width="60">Print Sent</th>
					<th class="alignment_css" width="60">Print Rcv</th>

                    <th class="alignment_css" width="50">Embr Sent</th>
                    <th class="alignment_css" width="60">Embr Rcv</th>

					<th class="alignment_css" width="60">Sp.Wo Sent</th>
					<th class="alignment_css" width="60">Sp.Wo Rcv</th>

                    <th class="alignment_css" width="60">Sew. Input</th>
                    <th class="alignment_css" width="60">Sew. Output</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
                    <th class="alignment_css" width="60">Inspection Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Val</th>

					<th class="alignment_css" width="80">Short Ship Qty</th>
					<th class="alignment_css" width="100">Short Ship Value</th>
                    <th class="alignment_css" width="80">Excs. Ship Qty</th>
                    <th class="alignment_css" width="100">Excs. Ship Value</th>
					<th class="alignment_css" width="80">Ship Bal. Qty</th>
                    <th class="alignment_css" width="100">Shipping Status</th>
                    <th class="alignment_css" width="100">Order Status</th>
                    <th class="alignment_css" width="100">Active Status</th>
                    <th class="alignment_css" width="100">Extended Ship Mode</th>
                    <th class="alignment_css" width="100">Sea Discount On FOB</th>
                    <th class="alignment_css" width="100">Air Discount On FOB</th>
                    <th class="alignment_css" width="100">Team Leader</th>
                    <th class="alignment_css" width="100">Dealing Merchant</th>
                    <th class="alignment_css" width="100">Dealy Reason</th>

                    <th class="alignment_css" width="150">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:5790px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;"" id="scroll_body" >
            <table  width="5770"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <tbody>
				<?php
                $i=1;$tot_po_total_price=$tot_print_issue_qnty=$tot_print_rcv_qnty=$tot_sp_work_issue_qnty=$tot_sp_work_rcv_qnty=$tot_short_ship_qty=$tot_excess_short_ship_qty=$tot_short_ship_bal_qty=$tot_yarn_allocation_qntyShow=$tot_yarn_issue_qnty=$tot_fin_qty=$tot_inspection_qnty=0;

                foreach($order_data_arr as $row)
                {
					//$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}

					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}

					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					$yarn_iss_qnty=$yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
					$yarn_allocation_qnty=0;
					foreach($gmts_items as $gmts_id)
					{
						// $yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					$yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
					 //var_dump($itemText);die;
					$issQty=($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return'];

					$booking_full_for="";
					foreach($booking_no_array[$row[csf('id')]] as $key=>$val)$booking_full_for=$key;
					$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_full_for]['requ_no'],",")));




					$inspection_qnty=$inspect_data_arr[$row[csf('id')]]['inspection_qnty'];
					$inspection_status=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_status'],',');
					$inspection_date=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_date'],',');
					$inspection_status=implode(',',array_unique(explode(",",$inspection_status)));
					$inspection_date=implode(',',array_unique(explode(",",$inspection_date)));

					$print_issue_qnty=$emb_qnty_array[$row[csf('id')]]['print_issue_qnty'];
					$print_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['print_rcv_qnty'];
					$sp_work_issue_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_issue_qnty'];
					$sp_work_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_rcv_qnty'];
					$extended_ship_date=$row[csf('extended_ship_date')];
					//echo $extended_ship_date.'DDS';
					/*if($row[csf('shiping_status')]==1) //Full Pending
					{
						//$ship_status_td="white";
						$ship_status_td='bgcolor="#FFFFFF"';
					}
					else if($row[csf('shiping_status')]==2) //Partial
					{
						//$ship_status_td="yellow";
						$ship_status_td='bgcolor="#FFFF00"';
					}
					elseif($row[csf('shiping_status')]==3) //Full Shipment/Closed
					{
						//$ship_status_td="cyan";
						$ship_status_td='bgcolor="#00FFFF"';
					}
					else
					{
						if($i%2==0) $ship_status_td='bgcolor="#E9F3FF"';else  $ship_status_td='bgcolor="#FFFFFF"';
					}

					if($row[csf('shiping_status')]==1) //Full Pending
					{
						//$ship_status_td="white";
						$ship_status_td='bgcolor="#FFFFFF"';
					}
					else if($row[csf('shiping_status')]==2) //Partial
					{
						//$ship_status_td="yellow";
						$ship_status_td='bgcolor="#FFFF00"';
					}*/

					if($row[csf('shiping_status')]==3 && $row[csf('po_quantity')]>$ex_qnty_array[$row[csf('id')]]) //Full Shipment/Closed
					{
						$short_ship_qty=$row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]];
					}
					else
					{
						$short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==3 && $ex_qnty_array[$row[csf('id')]]>$row[csf('po_quantity')]) //Full Shipment/Closed
					{
						$excess_short_ship_qty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')];
					}
					else
					{
						$excess_short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2 ) //Partial
					{
						$short_ship_bal_qty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')];
					}
					else
					{
						$short_ship_bal_qty=0;
					}

					$finQty = ($sewing_finish_qnty_array[$row[csf('id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];

					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					if($row[SHIPING_STATUS]==2){$bgcolor="#FFFF00";}
					elseif($row[SHIPING_STATUS]==3){$bgcolor="#00FFFF";}
					else{$row[SHIPING_STATUS]=1;}



            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;"" valign="middle">
                    <td class="alignment_css" width="30"><? echo $i;?></td>
                    <td class="alignment_css" width="100"><p><? echo $company_lib[$row[csf('company_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $agent_lib[$row[csf('agent_name')]];?></p></td>
                    <td class="alignment_css" width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $row[csf('po_number')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $row[csf('style_ref_no')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $product_dept[$row[csf('product_dept')]];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $sub_department_lib[$row[csf('pro_sub_dep')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo implode(',',$itemText);?></p></td>
					<td class="alignment_css" width="60"><A href="javascript:void()" onClick="openPopup('<? echo $row[csf('job_no')];?>','Job File Pop up','job_file_popup')">File</A></td>
                    <td class="alignment_css" width="60"  align="center">
                    <?
                    if($imge_arr[$row[csf('job_no')]]!='')
					{
					?>
                    <p onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></p>
                    <?
					}
					else
					{
						echo " ";
					}
					?>
                    </td>
                    <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
					 <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($row[csf('unit_price')],3));?></td>
					 <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_total_price')],2));?></td>

                    <td class="alignment_css" width="100" align="center"><? $insert_dates=$row[csf('insert_date')]; echo date("d-M-Y", strtotime($insert_dates));?></td>
                    <td class="alignment_css" width="100" align="center"><? $po_received_date=$row[csf('po_received_date')]; echo date("d-M-Y", strtotime($po_received_date));?></td>
                    <?
                    if(str_replace("'","",$cbo_date_type)==6 || str_replace("'","",$cbo_date_type)==5){
                    	$originalDate = $row[csf('update_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==1){
                    	$originalDate = $row[csf('pub_shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==2){
                    	$originalDate = $row[csf('shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==3){
                    	$originalDate = $row[csf('insert_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==4){
                    	$originalDate = $row[csf('extended_ship_date')];
                    }
                    $originalDate = $row[csf('pub_shipment_date')];//ref_closing_date_arr
					$ref_closing_date=$ref_closing_date_arr[$row[csf('id')]];
                    ?>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($originalDate));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($row[csf('shipment_date')]));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
					<td class="alignment_css" width="80" align="center"><? echo ($extended_ship_date =="") ? "" : date("d-M-Y", strtotime($extended_ship_date)); ?></td>

                    <td class="alignment_css" width="80" align="center"><? echo ($ref_closing_date =="") ? "" : date("d-M-Y", strtotime($ref_closing_date)); ?></td>

					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==3) echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==2)  echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="80" align="center"><? echo $inspection_date;?></td>
					<td class="alignment_css" width="80" align="center"><? echo $inspection_status;?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>

                    <td class="alignment_css" width="80" align="right"><? echo $row[csf('po_qty')]*omitZero($row[csf('set_smv')]);?></td>

					 <td class="alignment_css" width="100"><p>
					<?
						$html=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						//$html[] ="<a href='$booking_no'>$booking_num</a>";
						$html[] =$booking_num;
						}
						echo implode(',',$html);

						if($cbo_fabric_source==2) //purchase
						{
							$yarn_allocation_qnty=0;
							$yarn_iss_qnty=0;
							$gf_qnty_array[$row[csf('id')]]=0;
							$gp_qty_array[$row[csf('id')]]=0;
							$issQty=0;$gp_per=0;
							$daying_qnty_array[$row[csf('id')]]=0;
						}
					?>
                    </p></td>
                     <td class="alignment_css" width="100" align="right"><?  echo  omitZero(number_format($yarn_allocation_qnty,0));?></td>
                     <td class="alignment_css" width="100"  align="right"><? echo  omitZero(number_format($yarn_iss_qnty,0));?></td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($gf_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($gp_qty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($issQty,0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero($gp_per);?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($daying_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($ff_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? echo omitZero(number_format($fabrics_avl_qnty_array[$row[csf('id')]]),0);?>
                    </td>
                    <td class="alignment_css" width="60" align="right">
                    	<? $blance= $ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]];
                    	echo omitZero(number_format($blance,0));?></td>

                    <td class="alignment_css" width="60" align="right">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row['ID'];?>','Trims Value Popup','trims_value_popup')">
                    		<? echo number_format($trims_rcv_val_array[$row['ID']],2);?>
                    	</A>
                    </td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_per));?></td>
					<td class="alignment_css" width="60" title='Print sent' align="right"><? echo omitZero(number_format($print_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><?  echo omitZero(number_format($print_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($emb_issue_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($emb_rec_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_rcv_qnty,0));?></td>

				    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></td>

                    <td class="alignment_css" width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row["ID"];?>_<? echo $row["GMTS_ITEM_ID"];?>','Finish Qty Popup','fin_qnty_popup');">
                    		<? echo omitZero(number_format($finQty,0));?>
                    	</A>
                    </td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($inspection_qnty,0));?></td>
					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')],0));?></td>

					<td class="alignment_css" width="80" title="Order Qty Pcs-Ship Qty" align="right"><? echo omitZero(number_format($short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" title="Order Qty Pcs-Ship Qty" align="right"><? echo omitZero(number_format($short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right" title="Ship Qty-Order Qty Pcs"><?  echo omitZero(number_format($excess_short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" align="right" title="Ship Qty-Order Qty Pcs"><?  echo omitZero(number_format($excess_short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right"><? echo omitZero(number_format($short_ship_bal_qty,0));?></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $order_status[$row[csf('order_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $row_status[$row[csf('status_active')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $extend_shipment_mode[$row[csf('extend_ship_mode')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $sea_discount= ($row[csf('po_total_price')]*( $row[csf('sea_discount')]/100));echo number_format($sea_discount,2); ?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $air_discount= ($row[csf('po_total_price')]*( $row[csf('air_discount')]/100));echo number_format($air_discount,2);?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $team_leader_lib[$row[csf('team_leader')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $dealing_merchant_lib[$row[csf('dealing_marchant')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p>
					<?
						//$delay_for[$row[csf('delay_for')]];
					$delay_reason = false;
					foreach (explode(",", $row['DELAY_FOR']) as $val)
					{
						$delay_reason .= $delay_reason ? ", ".$delay_for[$val] : $delay_for[$val];
					}
					echo $delay_reason;
					?>
					</p></td>
                    <td class="alignment_css" width="150" align="left"><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?

				$tot_po_quantity+=$row[csf('po_quantity')];
				$tot_yarn_allocation_qntyShow+=$yarn_allocation_qnty;
				$tot_yarn_issue_qnty+=$yarn_iss_qnty;
				$tot_po_total_price+=$row[csf('po_total_price')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]]);
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_array[$row[csf('id')]];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$finQty;
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				$tot_print_issue_qnty+=$print_issue_qnty;
				$tot_print_rcv_qnty+=$print_rcv_qnty;
				$tot_sp_work_issue_qnty+=$sp_work_issue_qnty;
				$tot_sp_work_rcv_qnty+=$sp_work_rcv_qnty;
				$tot_short_ship_qty+=$short_ship_qty;
				$tot_excess_short_ship_qty+=$excess_short_ship_qty;
				$tot_short_ship_bal_qty+=$short_ship_bal_qty;
				$tot_fin_qty+=$finQty;
				$tot_inspection_qnty+=$inspection_qnty;

				$i++;
			}
            ?>
         </tbody>
		</table>
        </div>
        <table width="5770" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" id="report_table_footer">
         	<tr>
                <td class="alignment_css" width="30">&nbsp; </td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="60">&nbsp;</td>

                <td class="alignment_css" width="60">&nbsp;</td>

				<td class="alignment_css" width="100"> <? echo number_format ($tot_po_quantity,0);?></td>
				<td class="alignment_css" width="60">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="60">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100" id="yarn_allocation_tdshow"><? echo number_format($tot_yarn_allocation_qntyShow,0);?></td>
				<td  class="alignment_css" width="100" id="yarn_issue_td" ><? echo number_format($tot_yarn_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_gp_qty"><? echo number_format($tot_gp_qty,0);?></td>
                <td class="alignment_css" width="60" id="td_gf_qnty"><? echo number_format($tot_gf_qnty,0);?></td>

                <td class="alignment_css" width="60" id="td_gp_to_qty"><? echo number_format($tot_grey_to_dye,0);?></td>

                <td class="alignment_css" width="60" id="td_daying_qnty"><? echo number_format($tot_daying_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_ff_qnty" ><? echo number_format($tot_ff_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_fabrics_avl_qnty"><? echo number_format($tot_fabrics_avl_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_blance"><? echo number_format($tot_blance,0);?></td>

                <td class="alignment_css" width="60" id="td_trim_blance"></td>
				<td class="alignment_css" width="60">&nbsp;</td>
                <td class="alignment_css" width="60" id="td_cut_qnty"><? echo number_format($tot_cut_qnty,0);?></td>

				<td class="alignment_css" width="60" id="td_print_sent"><? echo number_format($tot_print_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_print_recv"><? echo number_format($tot_print_rcv_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_emb_issue_qnty"><? echo number_format($tot_emb_issue_qnty,0);?></td>
                <td class="alignment_css" width="50" id="td_emb_rec_qnty"><? echo number_format($tot_emb_rec_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_sp_issue_qnty"><? echo number_format($tot_sp_work_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_sp_rec_qnty" ><? echo number_format($tot_sp_work_rcv_qnty,0);?></td>

				<td class="alignment_css" width="60" id="td_sewing_in_qnty"><? echo number_format($tot_sewing_in_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_out_qnty"><? echo number_format($tot_sewing_out_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_finish_qnty"><? echo number_format($tot_fin_qty,0);?></td>
                <td class="alignment_css" width="60" id="td_inspection_qnty"><? echo number_format($tot_inspection_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_ex_qnty"><? echo number_format($tot_ex_qnty,0);?></td>

                <td class="alignment_css" width="50" id="td_ex_qnty_val"></td>

				 <td class="alignment_css" width="50" id="td_short_ship_qnty"><? echo number_format($tot_short_ship_qty,0);?></td>
				 <td class="alignment_css" width="80" id="td_short_ship_val"><? echo number_format($tot_short_ship_qty_val,0);?></td>
				 <td class="alignment_css" width="100" id="td_excess_ship_qnty"><? echo number_format($tot_excess_short_ship_qty,0);?></td>
				<td class="alignment_css" width="80">&nbsp;</td>
				 <td class="alignment_css" width="100" id="td_excess_ship_val"><? echo number_format($tot_excess_short_ship_val,0);?></td>
				 <td class="alignment_css" width="80" id="td_ship_bal_qnty"><? echo number_format($tot_short_ship_bal_qty,0);?></p></td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
      		</tr>
        </table>
	</div>
	</fieldset>
	<?
	echo "****1****".$type;
	/*$html = ob_get_contents();
	ob_clean();

	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}

	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	*/
}

if($action=="report_generate_fso")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	$type=str_replace("'","",$type);
	//echo $date_type.'DDD';die;

	$status_active=str_replace("'", "",$cbo_active_status);
	$_SESSION["status_active"]="";
	$_SESSION["status_active"]=$status_active;
	if($status_active==1)
	{
		$po_tbl_cond_a=" and a.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";
	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and a.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";
	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and a.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";
	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and a.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";
	}
	if($date_type==4)$date_caption="Prev Ship Date"; else $date_caption="Ship Date";

	if($cbo_company_name==0 || $cbo_company_name=="")
	{
		$company_name="";$company_name2="";
	}
	else
	{
		$company_name = "and a.company_name in($cbo_company_name) ";$company_name2 = " and a.company_id in($cbo_company_name)";
	}//fabric_source//item_category

	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and a.team_leader  = $team_leader";else $team_leader_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and a.id=$txt_job_id";else $job_no_cond="";
	if($txt_po_id!='') $po_no_cond="and b.id=$txt_po_id";else $po_no_cond="";

	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}

	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	$startdate=strtotime(str_replace("'","",trim($txt_date_from)));
	$enddate=strtotime(str_replace("'","",trim($txt_date_to)));
	//echo $cal_date_to.'='.$cal_date_from;die;
	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from));
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$end_date = date('Y-m-d',strtotime($txt_date_to));

				$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");

				foreach($ref_closing_po_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($poArr,0,"c.id");
				 $ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");
				 $ship_cond="and b.shiping_status=3";
			}
			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{

			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and b.extended_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.extended_ship_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(b.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==7) //Ref Closeing
			{
				$ref_closing_po_arr2=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");

				foreach($ref_closing_po_arr2 as $po_id=>$ids){
					$ref_poArr[$po_id]=$po_id;
				}
				 $ref_po_cond_for_in=where_con_using_array($ref_poArr,0,"c.id");
				// $ref_po_cond_for_in2=where_con_using_array($ref_poArr,0,"b.id");
				 $ref_po_cond_for_in3=where_con_using_array($ref_poArr,0,"b.inv_pur_req_mst_id");
				  $ship_cond="and b.shiping_status=3";

				  $ref_closing_date_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0   $ref_po_cond_for_in3 group by b.inv_pur_req_mst_id", "po_id", "closing_date");

				foreach($ref_closing_date_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;

				}
				$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.id");
			}
			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}

		}
	}

	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);

	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );

	$team_leader_lib =return_library_array( "SELECT id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );
	$agent_lib =return_library_array( "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in($cbo_company_name) and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name", "id", "buyer_name"  );

	$dealing_merchant_lib =return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$department_lib=return_library_array( "select id, department_name from lib_department where  status_active=1 and is_deleted=0", "id", "department_name"  );
	$sub_department_lib=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment where  status_active=1 and is_deleted=0", "id", "sub_department_name"  );
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');

	 $fab_sql="SELECT b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,a.booking_no_prefix_num,a.booking_no,a.pay_mode,a.supplier_id,
	(case when a.fabric_source=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty,
	(case when b.booking_type=1 then b.fin_fab_qnty end) as fin_fab_qnty, e.job_no as SALES_ORDER_NO
	from wo_booking_mst a
	left join fabric_sales_order_mst e on a.id=e.booking_id and e.status_active=1,
	wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id
	and b.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text2 $ref_po_cond_for_in";

	// echo $fab_sql;die;
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]['booking_no']=$row[csf('booking_no_prefix_num')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]['sales_order_no']=$row['SALES_ORDER_NO'];
		if($cbo_fabric_nature!=0 || $cbo_fabric_nature!=0)
		{
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		}
		if($row[csf('pay_mode')]==5)
		{
			$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]['working_company']=$row[csf('supplier_id')];
		}
	 }
	 unset($fab_result);
	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")";

		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";

		   }
		   $po++;
		}
	 }
	 if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.id') ;
	}
	else $fnc_cond="";
	//echo $po_cond_for_in;die;
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

		 $sql_po="SELECT b.is_confirmed as order_status, $date_diff b.update_date, b.shipment_date, a.company_name,a.team_leader, a.dealing_marchant,a.agent_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,(b.unit_price/a.total_set_qnty) as unit_price ,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.shiping_status,b.inserted_by,b.po_total_price,b.details_remarks,b.extended_ship_date,b.status_active,b.extend_ship_mode, b.sea_discount, b.air_discount,b.delay_for,a.product_dept,a.pro_sub_dep
		 from wo_po_details_master a, wo_po_break_down b
		 where a.id=b.job_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 $po_tbl_cond2 $search_text $buyer_id_cond $shipping_status_cond $fnc_cond $job_no_cond $team_leader_cond $po_no_cond $ref_po_cond_for_in2 $ship_cond
		 order by b.pub_shipment_date desc";
		 // echo $sql_po;

		$order_sql_result = sql_select($sql_po);
		foreach($order_sql_result as $rows){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			if(str_replace("'","",$cbo_date_type)==7)
			{
				$ref_closing_min_date=strtotime($ref_closing_date_arr[$rows[csf("id")]]);
				if($startdate!='' && $enddate!='' && $ref_closing_min_date>=$startdate && $ref_closing_min_date<=$enddate)
				{
					$order_data_arr[]=$rows;
					$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
				}
			}
			else
			{
				$order_data_arr[]=$rows;
				$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
			}
		}
		unset($order_sql_result);
		$po_id_list_arr=array_chunk($order_id_arr,999);
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")";
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";

			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")";
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";

			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";

			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")";
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";
			$p++;
		}
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";

		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  $poCon3  ";
		$yarn_allo_arr=array();

		foreach(sql_select($yarn_allo) as $v)$yarn_allo_arr[$v[csf("po_break_down_id")]][0]+=$v[csf("qnty")];

		$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];

		 $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       and b.receive_basis = 3  and c.status_active = 1  $poCon5  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];


		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $poCon5");

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];

		}

		//print_r($yarn_allo_arr);

		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		 $sqlsTrans="SELECT a.knit_dye_source, c.po_breakdown_id, sum(c.quantity) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knit_dye_source,c.po_breakdown_id  ";

		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			 if($row[csf('knit_dye_source')]!=3)
				$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			 if($row[csf('knit_dye_source')]==3)$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['out']+=$row[csf('qnty')];
		}
		$sql_ret="SELECT  c.po_breakdown_id, sum(c.quantity) as quantity  from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (51,84) and c.entry_form in (51,84) $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.po_breakdown_id ";
		foreach (sql_select($sql_ret) as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['return']+=$row[csf('quantity')];
		}

		//print_r( $dataArrayYarnIssuesQty);
		$daying_qnty_array=return_library_array( "SELECT sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and a.entry_form=0 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");

		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);

		$InspectData=sql_select("select a.po_break_down_id, a.inspection_status,a.inspection_date,a.inspection_status,a.inspection_qnty from pro_buyer_inspection a, wo_po_break_down b where a.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 $po_tbl_cond2 $poCon ");
		foreach($InspectData as $row)
		{
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_status'].=$inspection_status[$row[csf('inspection_status')]].',';
			$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_date'].=change_date_format($row[csf('inspection_date')]).',';
			if($row[csf('inspection_status')]==1)
			{
				$inspect_data_arr[$row[csf('po_break_down_id')]]['inspection_qnty']+=$row[csf('inspection_qnty')];
			}
		}
		unset($InspectData);

		$sql="SELECT po_break_down_id,
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut,
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing,
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish,
		sum(CASE WHEN production_type =2 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembissue,
		sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity ELSE 0 END) AS totaembrec,
		sum(CASE WHEN production_type =2 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity ELSE 0 END) AS print_rcv_qnty,
		sum(CASE WHEN production_type =2 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_issue_qnty,
		sum(CASE WHEN production_type =3 and embel_name=4 THEN production_quantity ELSE 0 END) AS sp_work_rcv_qnty
		from pro_garments_production_mst WHERE status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id";
		// echo $sql;
		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];

			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_issue_qnty']=$row[csf('print_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['print_rcv_qnty']=$row[csf('print_rcv_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_issue_qnty']=$row[csf('sp_work_issue_qnty')];
			$emb_qnty_array[$row[csf('po_break_down_id')]]['sp_work_rcv_qnty']=$row[csf('sp_work_rcv_qnty')];

		}
		unset($dataArray);
		$ex_qnty_array=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");

		$sqls="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
		foreach(sql_select($sqls) as $vals)
		{
			$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
		}

		/*echo "<pre>";
		print_r($fabrics_avl_qnty_array);
		echo "</pre>";*/
        // ================================ conversion rate ==========================
		$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
		// print_r($convrsn_rate_library);
		$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
		// ====================================== trims rcv ===================================
		$poCon6 = str_replace("po_breakdown_id", "b.po_breakdown_id", $poCon5);
		$trims_rcv_val_array = array();
		$receive_qty_data=sql_select("SELECT b.po_breakdown_id, sum(b.quantity) as quantity, a.rate,c.currency_id   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $poCon6  group by b.po_breakdown_id,a.rate,c.currency_id ");

        foreach($receive_qty_data as $row)
        {
        	if($row['CURRENCY_ID']==1)
        	{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate;
			}
			else
			{
				$trims_rcv_val_array[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')]*$row[csf('rate')];
			}
        }
        // print_r($trims_rcv_val_array);
        $poCon7 = str_replace("b.po_breakdown_id", "b.to_po_id", $poCon6);
        // echo $poCon7;
        // ========================== Transfer In =======================
		$sql_trns_in = "SELECT b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7   and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.to_po_id";
		// echo $sql_trns_in;
		$trans_in_res = sql_select($sql_trns_in);
		$trans_in_qty_array = array();
		foreach ($trans_in_res as  $val)
		{
			$trans_in_qty_array[$val['TO_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_in_qty_array);
		$poCon7 = str_replace("b.po_breakdown_id", "b.from_po_id", $poCon6);
		// ========================== Transfer Out =======================
		$sql_trns_out = "SELECT b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and c.trans_type=6  $poCon7 and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.from_po_id";
		// echo $sql_trns_out;
		$trans_out_res = sql_select($sql_trns_out);
		$trans_out_qty_array = array();
		foreach ($trans_out_res as  $val)
		{
			$trans_out_qty_array[$val['FROM_PO_ID']] = $val['QTY'];
		}
		// print_r($trans_out_qty_array);
	ob_start();
	?>
    <fieldset>
	<div style="width:5410px" align="left">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="49" align="center" style="font-size:22px;"><? echo $report_title;?></td></tr>
            <tr>
                <td colspan="49" align="center" style="font-size:16px; font-weight:bold;">
					<?
					$comp_names="";
					$comp_arr=explode(",", $cbo_company_name);
					foreach($comp_arr as $v)
					{
						if($comp_names)$comp_names.=','.$company_lib[$v];
						else $comp_names.= $company_lib[$v];
					}
					 echo $comp_names;
					 ?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="49" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?>
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="300"  cellpadding="0" cellspacing="0" border="0" align="left" >
        	<tr>
				<td width="20" bgcolor="white"> </td><td> &nbsp;&nbsp;Full Pending&nbsp; </td>
				<td width="20" bgcolor="yellow"> </td><td>&nbsp;&nbsp;Partial&nbsp; </td>
				<td width="20" bgcolor="cyan"> </td><td>&nbsp;&nbsp;Full Shipment/Closed&nbsp; </td>
        	</tr>
        </table>
		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="5470" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
            <thead>
                <tr style="font-size:12px">
                    <th class="alignment_css" width="30">Sl</th>
                    <th class="alignment_css" width="100">Company</th>
                    <th class="alignment_css" width="100">Buyer</th>
                    <th class="alignment_css" width="100">Working Company</th>

                    <th class="alignment_css" width="80">Job No</th>
                    <th class="alignment_css" width="100">Order No</th>
					<th class="alignment_css" width="100">Style No</th>
					<th class="alignment_css" width="100">Prod. Dept.</th>
					<th class="alignment_css" width="100">Prod. Sub. Dept.</th>

                    <th class="alignment_css" width="100">Item Name</th>
                    <th class="alignment_css" width="60">File</th>
                    <th class="alignment_css" width="60">Picture</th>
                    <th class="alignment_css" width="100">QTY(Pcs)</th>

					<th class="alignment_css" width="60">Unit Price(Pcs)</th>
					<th class="alignment_css" width="100">Order Value</th>
					<th class="alignment_css" width="100">PO Insert Date</th>
					<th class="alignment_css" width="100">PO Receive Date</th>
                    <th class="alignment_css" width="80"><? echo  $date_caption;?></th>

					<th class="alignment_css" width="80">Orig.Ship Date </th>
					<th class="alignment_css" width="80">Ext.Ship Date </th>
					<th class="alignment_css" width="80">Ref.Close Date </th>
                    <th class="alignment_css" width="100">Cancel Date </th>
					<th class="alignment_css" width="100">Delete Date </th>

					<th class="alignment_css" width="80">FRI Date</th>
					<th class="alignment_css" width="80">FRI Result</th>

                    <th class="alignment_css" width="60">SMV</th>
                    <th class="alignment_css" width="80">Total SMV</th>
					<th class="alignment_css" width="100">Booking No</th>
					<th class="alignment_css" width="100">Sales Order No</th>
                    <th class="alignment_css" width="60">Trims Rcv Val</th>
                    <th class="alignment_css" width="60">Cutting</th>
                    <th class="alignment_css" width="60">Cut (%)</th>
					<th class="alignment_css" width="60">Print Sent</th>
					<th class="alignment_css" width="60">Print Rcv</th>

                    <th class="alignment_css" width="50">Embr Sent</th>
                    <th class="alignment_css" width="60">Embr Rcv</th>

					<th class="alignment_css" width="60">Sp.Wo Sent</th>
					<th class="alignment_css" width="60">Sp.Wo Rcv</th>

                    <th class="alignment_css" width="60">Sew. Input</th>
                    <th class="alignment_css" width="60">Sew. Output</th>
                    <th class="alignment_css" width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
                    <th class="alignment_css" width="60">Inspection Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Qty</th>
                    <th class="alignment_css" width="50">TTL Ship Val</th>

					<th class="alignment_css" width="80">Short Ship Qty</th>
					<th class="alignment_css" width="100">Short Ship Value</th>
                    <th class="alignment_css" width="80">Excs. Ship Qty</th>
                    <th class="alignment_css" width="100">Excs. Ship Value</th>
					<th class="alignment_css" width="80">Ship Bal. Qty</th>
                    <th class="alignment_css" width="100">Shipping Status</th>
                    <th class="alignment_css" width="100">Order Status</th>
                    <th class="alignment_css" width="100">Active Status</th>
                    <th class="alignment_css" width="100">Extended Ship Mode</th>
                    <th class="alignment_css" width="100">Sea Discount On FOB</th>
                    <th class="alignment_css" width="100">Air Discount On FOB</th>
                    <th class="alignment_css" width="100">Team Leader</th>
                    <th class="alignment_css" width="100">Dealing Merchant</th>
                    <th class="alignment_css" width="100">Dealy Reason</th>

                    <th class="alignment_css" width="150">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:5490px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;"" id="scroll_body" >
            <table  width="5470"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <tbody>
				<?php
                $i=1;$tot_po_total_price=$tot_print_issue_qnty=$tot_print_rcv_qnty=$tot_sp_work_issue_qnty=$tot_sp_work_rcv_qnty=$tot_short_ship_qty=$tot_excess_short_ship_qty=$tot_short_ship_bal_qty=$tot_yarn_allocation_qntyShow=$tot_yarn_issue_qnty=$tot_fin_qty=$tot_inspection_qnty=0;

                foreach($order_data_arr as $row)
                {
					//$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}

					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}

					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					$yarn_iss_qnty=$yarn_issue_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]]['returned_qnty'];
					$yarn_allocation_qnty=0;
					foreach($gmts_items as $gmts_id)
					{
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					$yarn_allocation_qnty+=$yarn_allo_arr[$row[csf('id')]][0];
					 //var_dump($itemText);die;
					$issQty=($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'] + $dataArrayYarnIssuesQty[$row[csf('id')]]['out'])-$dataArrayYarnIssuesQty[$row[csf('id')]]['return'];

					$booking_full_for="";
					foreach($booking_no_array[$row[csf('id')]] as $key=>$val)$booking_full_for=$val['booking_no'];
					$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_full_for]['requ_no'],",")));

					$inspection_qnty=$inspect_data_arr[$row[csf('id')]]['inspection_qnty'];
					$inspection_status=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_status'],',');
					$inspection_date=rtrim($inspect_data_arr[$row[csf('id')]]['inspection_date'],',');
					$inspection_status=implode(',',array_unique(explode(",",$inspection_status)));
					$inspection_date=implode(',',array_unique(explode(",",$inspection_date)));

					$print_issue_qnty=$emb_qnty_array[$row[csf('id')]]['print_issue_qnty'];
					$print_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['print_rcv_qnty'];
					$sp_work_issue_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_issue_qnty'];
					$sp_work_rcv_qnty=$emb_qnty_array[$row[csf('id')]]['sp_work_rcv_qnty'];
					$extended_ship_date=$row[csf('extended_ship_date')];

					if($row[csf('shiping_status')]==3 && $row[csf('po_quantity')]>$ex_qnty_array[$row[csf('id')]]) //Full Shipment/Closed
					{
						$short_ship_qty=$row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]];
					}
					else
					{
						$short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==3 && $ex_qnty_array[$row[csf('id')]]>$row[csf('po_quantity')]) //Full Shipment/Closed
					{
						$excess_short_ship_qty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')];
					}
					else
					{
						$excess_short_ship_qty=0;
					}
					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2 ) //Partial
					{
						$short_ship_bal_qty=$ex_qnty_array[$row[csf('id')]]-$row[csf('po_quantity')];
					}
					else
					{
						$short_ship_bal_qty=0;
					}

					$finQty = ($sewing_finish_qnty_array[$row[csf('id')]]+$trans_in_qty_array[$row[csf('id')]])-$trans_out_qty_array[$row[csf('id')]];

					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					if($row["SHIPING_STATUS"]==2){$bgcolor="#FFFF00";}
					elseif($row["SHIPING_STATUS"]==3){$bgcolor="#00FFFF";}
					else{$row["SHIPING_STATUS"]=1;}



            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    <td class="alignment_css" width="30"><? echo $i;?></td>
                    <td class="alignment_css" width="100"><p><? echo $company_lib[$row[csf('company_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td class="alignment_css" width="100">
						<?
							$working_company=array();
							foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
							$working_company[] =$company_lib[$booking_num['working_company']];
							}
							echo implode(',',$working_company);
						?>
					</td>
                    <td class="alignment_css" width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $row[csf('po_number')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $row[csf('style_ref_no')];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $product_dept[$row[csf('product_dept')]];?></p></td>
					<td class="alignment_css" width="100"><p><? echo $sub_department_lib[$row[csf('pro_sub_dep')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo implode(',',$itemText);?></p></td>
					<td class="alignment_css" width="60"><A href="javascript:void()" onClick="openPopup('<? echo $row[csf('job_no')];?>','Job File Pop up','job_file_popup')">File</A></td>
                    <td class="alignment_css" width="60"  align="center">
                    <?
                    if($imge_arr[$row[csf('job_no')]]!='')
					{
					?>
                    <p onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></p>
                    <?
					}
					else
					{
						echo " ";
					}
					?>
                    </td>
                    <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
					 <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($row[csf('unit_price')],3));?></td>
					 <td class="alignment_css" width="100" align="right"><? echo omitZero(number_format($row[csf('po_total_price')],2));?></td>

                    <td class="alignment_css" width="100" align="center"><? $insert_dates=$row[csf('insert_date')]; echo date("d-M-Y", strtotime($insert_dates));?></td>
                    <td class="alignment_css" width="100" align="center"><? $po_received_date=$row[csf('po_received_date')]; echo date("d-M-Y", strtotime($po_received_date));?></td>
                    <?
                    if(str_replace("'","",$cbo_date_type)==6 || str_replace("'","",$cbo_date_type)==5){
                    	$originalDate = $row[csf('update_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==1){
                    	$originalDate = $row[csf('pub_shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==2){
                    	$originalDate = $row[csf('shipment_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==3){
                    	$originalDate = $row[csf('insert_date')];
                    }
                    if(str_replace("'","",$cbo_date_type)==4){
                    	$originalDate = $row[csf('extended_ship_date')];
                    }
                    $originalDate = $row[csf('pub_shipment_date')];//ref_closing_date_arr
					$ref_closing_date=$ref_closing_date_arr[$row[csf('id')]];
                    ?>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($originalDate));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
                    <td class="alignment_css" width="80" align="center"><? echo date("d-M-Y", strtotime($row[csf('shipment_date')]));
                    //$originalDate=$row[csf('pub_shipment_date')]; ?></td>
					<td class="alignment_css" width="80" align="center"><? echo ($extended_ship_date =="") ? "" : date("d-M-Y", strtotime($extended_ship_date)); ?></td>

                    <td class="alignment_css" width="80" align="center"><? echo ($ref_closing_date =="") ? "" : date("d-M-Y", strtotime($ref_closing_date)); ?></td>

					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==3) echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="100" align="right"><? if($row[csf("status_active")]==2)  echo change_date_format($row[csf('update_date')]);?></td>
					<td class="alignment_css" width="80" align="center"><? echo $inspection_date;?></td>
					<td class="alignment_css" width="80" align="center"><? echo $inspection_status;?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>

                    <td class="alignment_css" width="80" align="right"><? $totalSetSMV= $row[csf('po_qty')]*omitZero($row[csf('set_smv')]); echo number_format($totalSetSMV,4); ?></td>

					 <td class="alignment_css" width="100"><p>
					<?
						$html=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						//$html[] ="<a href='$booking_no'>$booking_num</a>";
						$html[] =$booking_num['booking_no'];
						}
						echo implode(',',$html);

						if($cbo_fabric_source==2) //purchase
						{
							$yarn_allocation_qnty=0;
							$yarn_iss_qnty=0;
							$gf_qnty_array[$row[csf('id')]]=0;
							$gp_qty_array[$row[csf('id')]]=0;
							$issQty=0;$gp_per=0;
							$daying_qnty_array[$row[csf('id')]]=0;
						}
					?>
                    </p></td>
                     <td class="alignment_css" width="100" >
						<?
							$sales_order_no=array();
							foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
								$sales_order_no[] =$booking_num['sales_order_no'];
							}
							echo implode(',',$sales_order_no);
					  ?>
					</td>

                    <td class="alignment_css" width="60" align="right">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row['ID'];?>','Trims Value Popup','trims_value_popup')">
                    		<? echo number_format($trims_rcv_val_array[$row['ID']],2);?>
                    	</A>
                    </td>

                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($cut_per));?></td>
					<td class="alignment_css" width="60" title='Print sent' align="right"><? echo omitZero(number_format($print_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><?  echo omitZero(number_format($print_rcv_qnty,0));?></td>

                    <td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($emb_issue_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($emb_rec_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_issue_qnty,0));?></td>
					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sp_work_rcv_qnty,0));?></td>

				    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></td>
                    <td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></td>

                    <td class="alignment_css" width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<A href="javascript:void()" onClick="openPopup('<? echo $row["ID"];?>_<? echo $row["GMTS_ITEM_ID"];?>','Finish Qty Popup','fin_qnty_popup');">
                    		<? echo omitZero(number_format($finQty,0));?>
                    	</A>
                    </td>

					<td class="alignment_css" width="60" align="right"><? echo omitZero(number_format($inspection_qnty,0));?></td>
					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));?></td>

					<td class="alignment_css" width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]*$row[csf('unit_price')],0));?></td>

					<td class="alignment_css" width="80" title="Order Qty Pcs-Ship Qty" align="right"><? echo omitZero(number_format($short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" title="Order Qty Pcs-Ship Qty" align="right"><? echo omitZero(number_format($short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right" title="Ship Qty-Order Qty Pcs"><?  echo omitZero(number_format($excess_short_ship_qty,0));?></td>
					<td class="alignment_css" width="100" align="right" title="Ship Qty-Order Qty Pcs"><?  echo omitZero(number_format($excess_short_ship_qty*$row[csf('unit_price')],0));?></td>
					<td class="alignment_css" width="80" align="right"><? echo omitZero(number_format($short_ship_bal_qty,0));?></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $order_status[$row[csf('order_status')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $row_status[$row[csf('status_active')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $extend_shipment_mode[$row[csf('extend_ship_mode')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $sea_discount= ($row[csf('po_total_price')]*( $row[csf('sea_discount')]/100));echo number_format($sea_discount,2); ?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? $air_discount= ($row[csf('po_total_price')]*( $row[csf('air_discount')]/100));echo number_format($air_discount,2);?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $team_leader_lib[$row[csf('team_leader')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p><? echo $dealing_merchant_lib[$row[csf('dealing_marchant')]];?></p></td>
					<td class="alignment_css" width="100" align="center"><p>
					<?
						//$delay_for[$row[csf('delay_for')]];
					$delay_reason = false;
					foreach (explode(",", $row['DELAY_FOR']) as $val)
					{
						$delay_reason .= $delay_reason ? ", ".$delay_for[$val] : $delay_for[$val];
					}
					echo $delay_reason;
					?>
					</p></td>
                    <td class="alignment_css" width="150" align="left"><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?

				$tot_po_quantity+=$row[csf('po_quantity')];
				$tot_yarn_allocation_qntyShow+=$yarn_allocation_qnty;
				$tot_yarn_issue_qnty+=$yarn_iss_qnty;
				$tot_po_total_price+=$row[csf('po_total_price')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]]);
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_array[$row[csf('id')]];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$finQty;
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				$tot_print_issue_qnty+=$print_issue_qnty;
				$tot_print_rcv_qnty+=$print_rcv_qnty;
				$tot_sp_work_issue_qnty+=$sp_work_issue_qnty;
				$tot_sp_work_rcv_qnty+=$sp_work_rcv_qnty;
				$tot_short_ship_qty+=$short_ship_qty;
				$tot_excess_short_ship_qty+=$excess_short_ship_qty;
				$tot_short_ship_bal_qty+=$short_ship_bal_qty;
				$tot_fin_qty+=$finQty;
				$tot_inspection_qnty+=$inspection_qnty;

				$i++;
			}
            ?>
         </tbody>
		</table>
        </div>
        <table width="5470" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" id="report_table_footer">
         	<tr>
                <td class="alignment_css" width="30">&nbsp; </td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="100">&nbsp;</td>

                <td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="60">&nbsp;</td>

                <td class="alignment_css" width="60">&nbsp;</td>

				<td class="alignment_css" width="100" id="td_po_quantity"> <? echo number_format ($tot_po_quantity,0);?></td>
				<td class="alignment_css" width="60">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="80">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>
				<td class="alignment_css" width="60">&nbsp;</td>
                <td class="alignment_css" width="80">&nbsp;</td>

				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="100">&nbsp;</td>
				<td class="alignment_css" width="60">&nbsp;</td>
                <td class="alignment_css" width="60" id="td_cut_qnty"><? echo number_format($tot_cut_qnty,0);?></td>

				<td class="alignment_css" width="60" id="td_print_sent"><? echo number_format($tot_print_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_print_recv"><? echo number_format($tot_print_rcv_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_emb_issue_qnty"><? echo number_format($tot_emb_issue_qnty,0);?></td>
                <td class="alignment_css" width="50" id="td_emb_rec_qnty"><? echo number_format($tot_emb_rec_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_sp_issue_qnty"><? echo number_format($tot_sp_work_issue_qnty,0);?></td>
				<td class="alignment_css" width="60" id="td_sp_rec_qnty" ><? echo number_format($tot_sp_work_rcv_qnty,0);?></td>

				<td class="alignment_css" width="60" id="td_sewing_in_qnty"><? echo number_format($tot_sewing_in_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_out_qnty"><? echo number_format($tot_sewing_out_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_sewing_finish_qnty"><? echo number_format($tot_fin_qty,0);?></td>
                <td class="alignment_css" width="60" id="td_inspection_qnty"><? echo number_format($tot_inspection_qnty,0);?></td>
                <td class="alignment_css" width="60" id="td_ex_qnty"><? echo number_format($tot_ex_qnty,0);?></td>

                <td class="alignment_css" width="50" id="td_ex_qnty_val"></td>

				 <td class="alignment_css" width="50" id="td_short_ship_qnty"><? echo number_format($tot_short_ship_qty,0);?></td>
				 <td class="alignment_css" width="80" id="td_short_ship_val"><? echo number_format($tot_short_ship_qty_val,0);?></td>
				 <td class="alignment_css" width="100" id="td_excess_ship_qnty"><? echo number_format($tot_excess_short_ship_qty,0);?></td>
				<td class="alignment_css" width="80">&nbsp;</td>
				 <td class="alignment_css" width="100" id="td_excess_ship_val"><? echo number_format($tot_excess_short_ship_val,0);?></td>
				 <td class="alignment_css" width="80" id="td_ship_bal_qnty"><? echo number_format($tot_short_ship_bal_qty,0);?></p></td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
				 <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
      		</tr>
        </table>
	</div>
	</fieldset>
	<?
	echo "****5****".$type;
	/*$html = ob_get_contents();
	ob_clean();

	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}

	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	*/
}


if($action=="fin_qnty_popup")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
	$dataEx = explode("_", $data);
	$poId = $dataEx[0];
	$itemId = $dataEx[1];
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name");
	$order_library=return_library_array( "select id, po_number from wo_po_break_down where  status_active=1 and is_deleted=0", "id", "po_number");

	$location_library=return_library_array( "select id, location_name from lib_location where  status_active=1 and is_deleted=0", "id", "location_name");

	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor where  status_active=1 and is_deleted=0", "id", "floor_name");

	$order_info_sql = "SELECT a.buyer_name,a.job_no,a.style_ref_no,b.po_number,b.grouping,b.pub_shipment_date,c.item_number_id,sum(c.order_quantity) as qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.status_active in(1,2,3) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=$poId and c.item_number_id=$itemId group by a.buyer_name,a.job_no,a.style_ref_no,b.po_number,b.grouping,b.pub_shipment_date,c.item_number_id";
	// echo $order_info_sql;
	$order_info = sql_select($order_info_sql);

	// ========================== production =======================
	$sql_prod = "SELECT a.serving_company, a.production_date,a.location, a.floor_id,a.production_source,sum(case when a.production_source=1 then a.production_quantity else 0 end) as qtyin,sum(case when a.production_source=3 then a.production_quantity else 0 end) as qtyout from pro_garments_production_mst a where a.po_break_down_id=$poId and a.item_number_id=$itemId and a.production_type=8 and a.status_active=1 and a.is_deleted=0 group by  a.serving_company, a.production_date,a.location, a.floor_id,a.production_source";
	// echo $sql_prod;
	$prod_res = sql_select($sql_prod);

	// ========================== Transfer In =======================
	$sql_trns_in = "SELECT a.delivery_date,b.from_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and trans_type=6 and b.to_po_id=$poId and b.item_number_id=$itemId and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.delivery_date,b.from_po_id order by a.delivery_date";
	// echo $sql_trns_in;
	$trans_in_res = sql_select($sql_trns_in);
	// ========================== Transfer Out =======================
	$sql_trns_out = "SELECT a.delivery_date,b.to_po_id,sum(b.production_quantity) as qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b,pro_garments_production_mst c where a.id=b.mst_id and a.id=c.delivery_mst_id and trans_type=6 and b.from_po_id=$poId and b.item_number_id=$itemId and a.production_type=10 and c.production_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.delivery_date,b.to_po_id order by a.delivery_date";
	// echo $sql_trns_out;
	$trans_out_res = sql_select($sql_trns_out);
	?>
	<div>
		<!-- =================================== header =============================== -->
		<div class="heaader_part">
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="740">
				<thead>
					<tr>
						<th width="100">Buyer</th>
						<th width="100">Job Number</th>
						<th width="100">Style Name</th>
						<th width="100">Order Number</th>
						<th width="100">Int. Ref.</th>
						<th width="60">Ship Date</th>
						<th width="100">Item Name</th>
						<th width="80">Order Qty.</th>
					</tr>
					<tr>
						<td><? echo $buyer_library[$order_info[0]['BUYER_NAME']]; ?></td>
						<td><? echo $order_info[0]['JOB_NO']; ?></td>
						<td><? echo $order_info[0]['STYLE_REF_NO']; ?></td>
						<td><? echo $order_info[0]['PO_NUMBER']; ?></td>
						<td><? echo $order_info[0]['GROUPING']; ?></td>
						<td><? echo change_date_format($order_info[0]['PUB_SHIPMENT_DATE']); ?></td>
						<td><? echo $garments_item[$order_info[0]['GMTS_ITEM_ID']]; ?></td>
						<td align="right"><? echo $order_info[0]['QTY']; ?></td>
					</tr>
				</thead>
			</table>
		</div>
		<!-- ====================================== production part ================================ -->
		<div class="prduction_part" style="padding-top: 15px;">
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="570">
				<caption><b>Details</b></caption>
				<thead>
					<tr>
						<th width="30" rowspan="2">Sl</th>
						<th width="60" rowspan="2">Finish Date</th>
						<th width="120" rowspan="2">Unit Name</th>
						<th width="120" colspan="2">Finish Qty</th>
						<th width="120" rowspan="2">Finish Company</th>
						<th width="120" rowspan="2">Location</th>
					</tr>
					<tr>
						<th width="60">In House</th>
						<th width="60">Outside</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$totalIn = 0;
					$totalOut = 0;
					foreach ($prod_res as  $val)
					{
						?>
						<tr>
							<td><? echo $i;?></td>
							<td><? echo change_date_format($val['PRODUCTION_DATE']);?></td>
							<td><? echo $floor_library[$val['FLOOR_ID']];?></td>
							<td align="right"><? echo $val['QTYIN'];?></td>
							<td align="right"><? echo $val['QTYOUT'];?></td>
							<td><? echo $company_library[$val['SERVING_COMPANY']];?></td>
							<td><? echo $location_library[$val['LOCATION']];?></td>
						</tr>
						<?
						$i++;
						$totalIn += $val['QTYIN'];
						$totalOut += $val['QTYOUT'];
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3" align="right">Total</th>
						<th align="right"><? echo $totalIn;?></th>
						<th align="right"><? echo $totalOut;?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<!-- ====================================== transfer in ===================================== -->
		<div class="transfer_in_part" style="padding-top: 15px;">
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="270">
				<caption><b>Transfer In</b></caption>
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="60">Transfer Date</th>
						<th width="120">From Order</th>
						<th width="60">Transfer Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$totalTransIn = 0;
					foreach ($trans_in_res as  $val)
					{
						?>
						<tr>
							<td><? echo $i;?></td>
							<td><? echo change_date_format($val['DELIVERY_DATE']);?></td>
							<td><? echo $order_library[$val['FROM_PO_ID']];?></td>
							<td align="right"><? echo $val['QTY'];?></td>
						</tr>
						<?
						$i++;
						$totalTransIn += $val['QTY'];
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3" align="right">Transfer In Total</th>
						<th align="right"><? echo $totalTransIn;?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<!-- ======================================= transfer out =================================== -->
		<div class="transfer_out_part" style="padding-top: 15px;">
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="270">
				<caption><b>Transfer Out</b></caption>
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="60">Transfer Date</th>
						<th width="120">To Order</th>
						<th width="60">Transfer Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$totalTransOut = 0;
					foreach ($trans_out_res as  $val)
					{
						?>
						<tr>
							<td><? echo $i;?></td>
							<td><? echo change_date_format($val['DELIVERY_DATE']);?></td>
							<td><? echo $order_library[$val['TO_PO_ID']];?></td>
							<td align="right"><? echo $val['QTY'];?></td>
						</tr>
						<?
						$i++;
						$totalTransOut += $val['QTY'];
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3" align="right">Transfer Out Total</th>
						<th align="right"><? echo $totalTransOut;?></th>
					</tr>
					<tr>
						<th colspan="3" align="right">Total Finish Avl.</th>
						<th align="right"><? echo ($totalIn+$totalOut+$totalTransIn)-$totalTransOut;?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?
}
if($action=="job_file_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$job_file=sql_select("SELECT id, master_tble_id, image_location, real_file_name from common_photo_library where is_deleted=0 and form_name = 'knit_order_entry'	and file_type = 2 and master_tble_id='$data'");
	?>
	<fieldset style="width:670px; margin-left:3px">
		<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th>SL</th>
					<th>File Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?
				$i=1;
				foreach($job_file as $row){
					$filename_arr=explode(".", $row[csf('real_file_name')]);
				?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $filename_arr[0]; ?></td>
						<td><A href="../../../../<?= $row[csf('image_location')];  ?>" download>download</A></td>
					</tr>
				<?
				$i++;
				}
			?>
			</tbody>
		</table>
	</fieldset>
	<?
}
if($action=="trims_value_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$dataEx = explode("_", $data);
	$po_id = $dataEx[0];
	$itemId = $dataEx[1];
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <!-- <th width="50">Prod. ID</th> -->
                    <th width="90">Recv. ID</th>
                    <th width="100">WO/PI No</th>
                    <th width="80">Recv. Date</th>
                    <!-- <th width="150">Item Description.</th> -->
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount (USD)</th>
                    <!-- <th>Reject Qty.</th> -->
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
					// $dt = date('d-M-y');
					$convrsn_rate_library=sql_select( "select currency,conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 order by con_date DESC");
					// print_r($convrsn_rate_library);
					$convrsn_rate = $convrsn_rate_library[0]['CONVERSION_RATE'];
					$i=1;

					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("SELECT a.issue_number,a.issue_date,d.po_breakdown_id,c.id as prod_id,c.product_name_details, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id  and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id)   group by a.issue_number,a.issue_date,d.po_breakdown_id,c.id,c.product_name_details, c.item_group_id order by c.item_group_id");//and c.item_group_id='$item_name'


					foreach($receive_rtn_qty_data as $row)
					{
					$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];
					$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];
					$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}

					$receive_qty_data="SELECT a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty,b.rate,e.po_number,b.booking_no,a.currency_id,a.exchange_rate
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d,wo_po_break_down e
					where e.id=c.po_breakdown_id and a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date,b.rate,e.po_number,b.booking_no,a.currency_id,a.exchange_rate";//and b.item_group_id='$item_name'
					// echo $receive_qty_data;

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <!-- <td width="50"><p><? //echo $row[csf('prod_id')]; ?></p></td> -->
                            <td width="90" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="80" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <!-- <td width="150" align="center"><p><? //echo $row[csf('item_group_id')]; ?></p></td> -->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                            <td width="80" align="right"><p><? echo $row[csf('currency_id')] == 1 ? number_format($row[csf('rate')]/$convrsn_rate,2) : number_format($row[csf('rate')],2); ?></p></td>
                            <td width="80" align="right"><p>
                            <?
                            echo $amt = $row[csf('currency_id')] == 1 ? number_format($row[csf('quantity')]*$row[csf('rate')]/$convrsn_rate,2) : number_format($row[csf('quantity')]*$row[csf('rate')],2)
                            // echo number_format($row[csf('quantity')]*$row[csf('rate')],2);
                            ?>

                            </p></td>

                            <!-- <td align="right"><p><? //echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td> -->
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						// $tot_amount+=$row[csf('quantity')]*$row[csf('rate')];
						$tot_amount+=$amt;
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_amount,2); ?></td>
                        <!-- <td><? //echo number_format($tot_rej_qty,2); ?></td> -->
                    </tr>
                </tfoot>
            </table>

            <!-- <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>

				</thead>
                <tbody>
                <?
					// in rec return page not found recv rate,thats why amount miss match in rev return page.
					//foreach($receive_rtn_qty_data as $row)
					{
						//if ($i%2==0)
							//$bgcolor="#E9F3FF";
						//else
							//$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? //echo  $bgcolor; ?>" onClick="change_color('tr_<? //echo $i; ?>','<? //echo $bgcolor;?>')" id="tr_<? //echo $i;?>">
							<td width="30"><p><? //echo $i; ?></p></td>
                            <td width="80"><p><? //echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? //echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? //echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? //echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="160" align="right"><p><? //echo number_format($row[csf('quantity')],2); ?></p></td>

                        </tr>
						<?
						//$tot_rtn_qty+=$row[csf('quantity')];
						//$i++;

					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? //echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Qty Balance</td>
                        <td><? //echo number_format($tot_qty-$tot_rtn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table> -->
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{
	?>
    <td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>

    <?
}
?>
