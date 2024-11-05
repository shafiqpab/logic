<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for Style Wise Extra Fabrics Monitoring Report
Functionality	:
JS Functions	:
Created by		:	Md. Sakibul Islam
Creation date 	: 	19-09-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
include('../../../includes/common.php');

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



if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_extra_fabrics_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		$po_tbl_cond_a=" and d.status_active=1 ";
		$po_tbl_cond=" and c.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";

	}
	else if($status_active==2)
	{
		$po_tbl_cond_a=" and d.status_active=2 ";
		$po_tbl_cond=" and c.status_active=2 ";
		$po_tbl_cond2=" and c.status_active=2 ";

	}
	else if($status_active==3)
	{
		$po_tbl_cond_a=" and d.status_active=3 ";
		$po_tbl_cond=" and c.status_active=3 ";
		$po_tbl_cond2=" and c.status_active=3 ";


	}
	else if($status_active==4)
	{
		$po_tbl_cond_a=" and d.status_active in(1,2,3) ";
		$po_tbl_cond=" and c.status_active in(1,2,3) ";
		$po_tbl_cond2=" and c.status_active in(1,2,3) ";


	}
	if($date_type==4)$date_caption="Prev Ship Date"; else $date_caption="Ship Date";

	if($cbo_company_name==0 || $cbo_company_name=="")
	{
		$company_name="";$company_name2="";
	}
	else
	{
		$company_name = "and d.company_name in($cbo_company_name) ";$company_name2 = " and a.company_id in($cbo_company_name)";
	}//fabric_source//item_category

	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";
	if($team_leader!=0) 	  $team_leader_cond= " and d.team_leader  = $team_leader";else $team_leader_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and c.shiping_status in($cbo_ship_status)";
	}
	if($txt_job_id!='') $job_no_cond="and d.id=$txt_job_id";else $job_no_cond="";
	if($txt_po_id!='') $po_no_cond="and c.id=$txt_po_id";else $po_no_cond="";

	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and d.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and c.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and d.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(d.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(d.insert_date,'YYYY')=$cbo_year";}
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
				$search_text .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and a.booking_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and a.booking_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and date_format(b.update_date, '%Y-%m-%d')   between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and date_format(c.update_date, '%Y-%m-%d') between '".$start_date."' and '".$end_date."'";
			}
			

			else
			{
				$start_date = date('Y-m-d',strtotime($txt_date_from));
				$search_text .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{

			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and a.booking_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and a.booking_date between '".$start_date."' and '".$end_date."'";
			}

			elseif(str_replace("'","",$cbo_date_type)==5 || str_replace("'","",$cbo_date_type)==6)
			{
				$search_text .=" and to_date(to_char(c.update_date,'dd-Mon-yyyy'))  between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and to_char(c.update_date,'dd-Mon-yyyy') '".$start_date."' and '".$end_date."'";
			}
			else
			{
				$search_text .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}

		}
	}

	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}


	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	

	$fab_sql="SELECT b.po_break_down_id as po_id,b.fabric_color_id,d.job_no,d.company_name,a.booking_no_prefix_num,a.booking_no,a.booking_date, b.grey_fab_qnty, b.fin_fab_qnty,d.style_ref_no,d.buyer_name
	from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.id=c.job_id AND b.booking_type=1 and b.is_short=2
	and b.booking_type=1 and b.status_active=1
	and b.is_deleted=0 $po_tbl_cond $company_name2 $buyer_id_cond2 $team_leader_cond $fab_nature_cond $fab_source_cond $search_text2 $ref_po_cond_for_in $search_text ";



	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$order_arr[$rows[csf('id')]]=$rows[csf('id')];
		$data_str=$row[csf('job_no')].'_'.$row[csf('booking_no')];
		$booking_no_array[$data_str]['booking_no']=$row[csf('booking_no')];
		$booking_no_array[$data_str]['company_name']=$row[csf('company_name')];
		$booking_no_array[$data_str]['booking_date']=$row[csf('booking_date')];
		$booking_no_array[$data_str]['buyer_name']=$row[csf('buyer_name')];
		$booking_no_array[$data_str]['style_ref_no']=$row[csf('style_ref_no')];
		$booking_no_array[$data_str]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
		$order_number=$row[csf('po_number')];
		$fabric_source=$row[csf('fabric_source')];
		
		 
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];

	 }
	 unset($fab_result);
	
	 if(count($po_id_arr)>0)
	{
		$fnc_cond=where_con_using_array($po_id_arr,0,'b.po_break_down_id') ;
	}
	else $fnc_cond="";
	//=================Booking Qty from BOOKING print report=======

				 
					$wo_sql_qnty="SELECT d.booking_no,d.job_no,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,d.fabric_color_id, sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
					WHERE a.job_no=b.job_no and
					a.id=b.pre_cost_fabric_cost_dtls_id and
					c.job_no_mst=a.job_no and
					c.id=b.color_size_table_id and
					b.po_break_down_id=d.po_break_down_id and
					b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id
					 and
					d.status_active=1
					and d.booking_type=1
         			and d.is_short=2 and
					d.is_deleted=0 $fnc_cond  group by d.booking_no,d.job_no,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,d.fabric_color_id ";
					  $sql_book=sql_select($wo_sql_qnty); 
					  $extra_qty_sum=0;
					foreach($sql_book as $row)
					{
						$fin_fab_qnty=$row[csf('fin_fab_qnty')];
						if($fin_fab_qnty<=50)
						{
							$extra_qty_sum=4;
							$extra_booking_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]]+=$extra_qty_sum;
						}
						
					}
		 

		 
		 
 
		//------------------------------issue qty--------------------------
	ob_start();
	?>
    <fieldset>
	<div style="width:1330" align="left">
        <table width="1330" cellpadding="0" cellspacing="0">
            <tr><td colspan="10" align="center" style="font-size:22px;"><? echo $report_title;?></td></tr>
            <tr>
                <td colspan="10" align="center" style="font-size:16px; font-weight:bold;">
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
             	<td colspan="10" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?>
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>

		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="1330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">

            <thead>
                <tr style="font-size:12px">
                    <th class="alignment_css" width="30">Sl</th>
                    <th class="alignment_css" width="150">Company</th>
                    <th class="alignment_css" width="150">Buyer</th>
                    <th class="alignment_css" width="100">Job No</th>
					<th class="alignment_css" width="150">Style No</th>
					<th class="alignment_css" width="150">Booking Date</th>
					<th class="alignment_css" width="150">Booking No</th>
                    <th class="alignment_css" width="100">Grey Req</th>
					<th class="alignment_css" width="100">Extra Qty</th>
                    <th class="alignment_css" width="100">Grey With Extra</th>
                    <th class="alignment_css" width="150">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1330px;overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body" >
            <table  width="1330"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
              <tbody>
				<?php
                $i=1;$tot_po_total_price=$tot_print_issue_qnty=$tot_print_rcv_qnty=$tot_sp_work_issue_qnty=$tot_sp_work_rcv_qnty=$tot_short_ship_qty=$tot_excess_short_ship_qty=$tot_short_ship_bal_qty=$tot_yarn_allocation_qntyShow=$tot_yarn_issue_qnty=$tot_fin_qty=$tot_inspection_qnty=0;

				
                foreach($booking_no_array as $job_str=>$row)
                {
					$job_str=explode("_",$job_str);
					$job_no=$job_str[0];
					$booking_no=$job_str[1];
					//generate_fabric_report(type,txt_booking_no,cbo_company_name,txt_order_no,cbo_fabric_natu,cbo_fabric_source,id_approved_id,txt_job_no)$order_number

					$function="generate_fabric_report('show_fabric_booking_report_jk','".$booking_no."',".$row[('company_name')].",'".$order_number."',2,1,1,'".$job_no."');";
				 
		
 
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;"" valign="middle">
                    <td class="alignment_css" width="30"><? echo $i;?></td>
                    <td class="alignment_css" width="150"><p><? echo $company_lib[$row[('company_name')]];?></p></td>
                    <td class="alignment_css" width="150"><p><? echo $buyer_library[$row[('buyer_name')]];?></p></td>
                    <td class="alignment_css" width="100"><p><? echo $job_no;?></p></td>
					<td class="alignment_css" width="150"><p><? echo $row[('style_ref_no')];?></p></td>
					<td class="alignment_css" width="150"><p>
						<? 
						 echo $row[('booking_date')];
						?></p>
					</td>
					<td class="alignment_css" width="150" align="center"><a href='##' onclick="<?=$function; ?>"><?= $booking_no; ?></td>
					<td class="alignment_css" width="100" align="right"><p> <?=$row[('grey_fab_qnty')]; ?></p></td>
					<td class="alignment_css" width="100" align="right"><p><?=number_format($extra_booking_qty_arr[$job_no][$booking_no]); ?></p></td>
					<td class="alignment_css" width="100" align="right"><p><?=$row[('grey_fab_qnty')]+$extra_booking_qty_arr[$job_no][$booking_no]; ?></p></td>
                    <td class="alignment_css" width="150" align="left"><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?
				$tot_grey_qnty+=$row[('grey_fab_qnty')];
				$tot_extra_qnty+=$extra_booking_qty_arr[$job_no][$booking_no];
				$tot_grey_extra_qnty+=$row[('grey_fab_qnty')]+$extra_booking_qty_arr[$job_no][$booking_no];
				$i++;
			}
            ?>
         </tbody>
		</table>
        </div>
        <table width="1330" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" id="report_table_footer" align="left">
         	<tr>
                <td class="alignment_css" width="30">&nbsp; </td>
                <td class="alignment_css" width="150">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
                <td class="alignment_css" width="150">&nbsp;</td>
                <td class="alignment_css" width="100">&nbsp;<?=$tot_grey_qnty ?></td>
                <td class="alignment_css" width="100">&nbsp;<?=$tot_extra_qnty ?></td>
				<td class="alignment_css" width="100">&nbsp;<?=$tot_grey_extra_qnty ?></td>
                <td class="alignment_css" width="150">&nbsp;</td>
      		</tr>
        </table>
	</div>
	</fieldset>
	<?
	echo "****1****".$type;
    exit();
}
if($action=="fin_qnty_popup")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
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
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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
						<td><A href="../../../<?= $row[csf('image_location')];  ?>" download>download</A></td>
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
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
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
    <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>

    <?
}
?>
