<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for daily order recap report info.
Functionality	:	
JS Functions	:
Created by		:	Md. Sakibul Islam  
Creation date 	: 	23-11-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.yarns.php');
include('../../../../includes/class4/class.others.php');
include('../../../../includes/class4/class.conversions.php');
include('../../../../includes/class4/class.trims.php');
include('../../../../includes/class4/class.emblishments.php');
include('../../../../includes/class4/class.washes.php');
include('../../../../includes/class4/class.commercials.php');
include('../../../../includes/class4/class.commisions.php');
extract($_REQUEST);
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
//---------------------------------------------------- Start
?>

<?
if($action=="load_drop_down_buyer")
{
	if($data!="")
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
	}   	 
	exit();
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');

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
    <div align="center" >
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
					<th> Company Name</th>
					<th> Year</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>

					<td>
                        <?
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select  --", $selected, "" );
	                     ?>
                        </td>

						<td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>

                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_working_company_name').value + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + '<?php echo $sytle_ref_no; ?>' + '**' + document.getElementById('cbo_year_selection').value, 'create_job_no_search_list_view', 'search_div', 'order_recap_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
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
	// echo $data;
	$data=explode('**',$data);
	// echo "<pre>";
	// print_r($data);die;
	// echo "</pre>";
    $company_id=$data[0];
	$year_id=$data[5];
	$buyer_arr	= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr = return_library_array( "select id, company_name from lib_company",'id','company_name');

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
	if($search_by==2) $search_field="b.po_number"; else $search_field="a.job_no";
	$year="year(a.insert_date)";
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


	$sql= "SELECT a.id, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no,b.po_number, $year_field from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id where  a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";
    // echo $sql;die();
	?>
	<div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4,"","","1,2,3,4" ); ?></div>
	<?

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Order No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,year,po_number", "",'','','') ;
	exit();
} // Job Search end

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0 order by sequence_no",'id','short_name');
$buyer_full_name_arr=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0 order by sequence_no",'id','buyer_name');
$colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
$lib_brand = return_library_array("select id , brand_name from lib_brand","id","brand_name");
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
$sub_dep_arr=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0",'id','sub_department_name');
$season_arr=return_library_array( "select id,season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
$team_name_arr=return_library_array( "select id, team_name from lib_marketing_team where status_active=1 and is_deleted=0", "id", "team_name");
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "team_member_name");
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$shipment_status = array(1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");
if($action=="show_file") 
{
	echo load_html_head_contents("Booking File","../../../../", 1, 1, $unicode);
    extract($_REQUEST);

	if($type==2)
	{
	 $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no'  and is_deleted=0 and file_type=2");

	 //echo "select image_location  from common_photo_library  where master_tble_id='$job_no'  and is_deleted=0 and file_type=2";
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			//echo  $row[csf('image_location')].'azzz';
        ?>
        <td><a href="../../../../<? echo $row[csf('image_location')] ?>" target="_new">
        <img src="../../../../file_upload/blank_file.png" width="80" height="60"> </a>
        </td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
	}
	exit();
}
if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";

	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and is_deleted=0 and file_type=1");

	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";die;

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
if($action=="report_generate") 
{
    extract($_REQUEST);
	$data=explode("_",$data);

	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_working_company=str_replace("'","",$cbo_working_company);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $txt_job_no=str_replace("'","",$txt_job_no);
    $txt_job_no_id=str_replace("'","",$txt_job_no_id);
    $start_date=str_replace("'","",$txt_date_from);
    $end_date=str_replace("'","",$txt_date_to);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);
	$date_category=str_replace("'","",$cbo_date_category);
	$buyer_name=str_replace("'","",$cbo_buyer_name);

	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	

	$company_id==$cbo_company_name;
	$company_cond=0;
	$company_field= "a.company_name as company_name" ;
	$company_field_caption= "Company Name" ;
	$company_group_by= "a.company_name" ;
	if($cbo_company_name ==0){
		echo "Please Select Company Name";
		die;
	}
	if($txt_job_no_id!=''){$job_id_con=" and a.id=$txt_job_no_id";} else echo $job_id_con="";
    if($txt_job_no!=''){$job_con=" and a.job_no like('%$txt_job_no%')";} else $job_con="";
    if($cbo_company_name!=0){$company_con=" and a.company_name in(".$cbo_company_name.")";}else echo $company_con="";
	if($cbo_working_company!=0){$working_company_con=" and a.working_company_id in(".$cbo_working_company.")";}
	if($buyer_name) $buyerCond=" and a.buyer_name in($buyer_name)"; else $buyerCond="";
    if($txt_style_ref!="") $style_ref_cond="and LOWER(a.style_ref_no) like LOWER('%".trim($txt_style_ref)."%')"; else $style_ref_cond="";
	
	$start_date=change_date_format($start_date,'','-',1);
	$end_date=change_date_format($end_date,'','-',1);

    $year_field=" and to_char(a.insert_date,'YYYY')";
	if($cbo_year_selection!=0) $year_cond=" $year_field=$cbo_year_selection"; else $year_cond="";

		if ($start_date!="" && $end_date!="")
		{
			if($date_category==1) // Country Ship Date 
			{
				$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";
			}
			else if($date_category==2) // Pub Ship Date
			{
				$date_cond=" and b.pub_shipment_date between '$start_date' and  '$end_date'";
			}
			else if($date_category==3) // original Ship Date
			{
				$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
			}
			else if($date_category==4) // Po insert Date
			{
				$date_cond=" and b.insert_date between '$start_date' and  '$end_date'";
			}
		}
		else
		{
			$date_cond="";
		}

	$target_basic_qnty=array();
	$total_target_basic_qnty=0;
	$sm = date('m',strtotime($start_date));
	$em = date('m',strtotime($end_date));

	$po_total_price_tot=0; $quantity_tot=0; $exfactory_tot=0; $po_total_price_tot_c=0; $quantity_tot_c=0; $po_total_price_tot_p=0; $quantity_tot_p=0; $booked_basic_qnty_tot_c=0; $booked_basic_qnty_tot_p=0;
	
	$colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$date=date('d-m-Y');

		$sqlQuery="SELECT a.id as job_id, a.job_no_prefix_num, a.job_no_prefix_num,to_char(a.insert_date,'YYYY') as job_year, a.job_no, $company_field, a.buyer_name, a.style_ref_no, a.style_description, a.product_category, a.product_dept, b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.pack_handover_date, c.color_number_id, c.country_id, c.item_number_id, sum(c.order_quantity) as poqtypcs, sum(c.order_total) as poamt,a.working_company_id
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id $company_con $working_company_con $style_ref_cond $job_id_con $job_con $date_cond $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by a.id, a.job_no_prefix_num, a.job_no_prefix_num,a.insert_date, a.job_no, $company_group_by, a.buyer_name, a.style_ref_no, a.style_description, a.product_category, a.product_dept, b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.pack_handover_date, c.color_number_id, c.country_id, c.item_number_id,a.working_company_id order by b.po_number ASC";

	//echo $sqlQuery; die;
	$data_array=sql_select($sqlQuery);
	$job_no=$data_array[0][csf('job_no')];
	$job_id=$data_array[0][csf('job_id')];
	$sqlSet=sql_select("Select job_id,job_no, gmts_item_id, smv_set, embro, embelishment, wash, spworks from wo_po_details_mas_set_details where job_no='$job_no'");

	$itemDtlsArr=array();
	foreach($sqlSet as $srow)
	{
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['smv']=$srow[csf('smv_set')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['embro']=$srow[csf('embro')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['emb']=$srow[csf('embelishment')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['wash']=$srow[csf('wash')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['spworks']=$srow[csf('spworks')];
	}
	unset($sqlSet);
	
	$sqlConfirm=sql_select("select job_id, po_id, confirm_status from gmts_production_confirmation where job_id=$job_id and status_active=1 and is_deleted=0 ");
	$confirmstatusArr=array();
	foreach($sqlConfirm as $crow)
	{
		$confirmstatusArr[$crow[csf('job_id')]][$crow[csf('po_id')]]['confirm']=$crow[csf('confirm_status')];
	}
	unset($sqlConfirm);
	
	$gmtsPartNamrArr=return_library_array("select id, product_type_id from lib_garment_item", "id", "product_type_id");

	//========================================================================================
	
	//   $con = connect();
	//   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=19");
	//   oci_commit($con);
	//   //disconnect($con);
	//   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 19, 1, $po_id_array, $empty_arr);
	//   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 19, 2, $job_id_array, $empty_arr);
 
	ob_start();
	?>
    <div>
        <h3 width="1760" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
        <div id="content_report_panel">
            <table width="1760" id="table_header" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60" ><? //echo $company_field_caption; ?>Company</th>
                        <th width="60">BOM NO</th>
                        <th width="60">Buyer</th>
                        <th width="100">Style</th>
                        <th width="100">Color</th>
                        <th width="100">Order No</th>
                        <th width="100">Destination</th>
						<th width="100">Order Status</th>
                        <th width="100">Garments Item</th>
                        <th width="60">Part Name</th>
                        <th width="70">Ex-Factory</th>
                        <th width="100">Order Qnty</th>
                        <th width="70">Actual File Handover</th>
                        <th width="70">File Hand Over Status</th>
                        <th width="100">Department</th>
                        <th width="100">Description</th>
                        <th width="70">Printing</th>
                        <th width="70">Embroidery</th>
                        <th width="70">Smoking</th>
                        <th width="70">Washing</th>
                        <th  width="70">Sewing SMV</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:1760px"  align="left" id="scroll_body">
                <table width="1760" border="1" class="rpt_table" rules="all" id="table_body">
					<?
                    $i=1; $order_qnty_pcs_tot=0;  $order_qntytot=0;
                    foreach ($data_array as $row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($cbo_category_by==1)
						{
							$country_name=$country_name_arr[$row[csf('country_id')]];
							$country_ship_date=$row[csf('country_ship_date')];
						}
						else
						{
							$country_name="";
							$country_id=array_unique(explode(",",$row[csf('country_id')]));
							foreach($country_id as $c_id)
							{
								if($country_name=="") $country_name=$country_name_arr[$c_id]; else $country_name.=",".$country_name_arr[$c_id];
							}
							$ship_date=$row[csf('pub_shipment_date')];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" id="tr_<? echo $i; ?>">
                            <td width="30" align="center" style="word-wrap: break-word;word-break: break-all;"> <? echo $i; ?> </td>
                            <td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $company_short_name_arr[$row[csf('company_name')]];?></td>
                            <td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('job_no_prefix_num')];?></td>
                            <td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];//buyer?></td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_ref_no')];//style?></td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $colorArr[$row[csf('color_number_id')]];//color?></td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><font style="color:<? echo $color_font; ?>"><? echo $row[csf('po_number')];  ?></font></td>
                            <td width="100" style="word-break:break-all"><? echo $country_name; //Destination?></td>
							<td width="100" style="word-break:break-all"><?if ($row[csf('is_confirmed')]==1) echo "confirm"; else echo "";//Order Status?></td>
							
                            <td width="100" style="word-break:break-all"><? echo $garments_item[$row[csf('item_number_id')]]; //Part Name?></td>
                            <td width="60" style="word-break:break-all"><? echo $product_types[$gmtsPartNamrArr[$row[csf('item_number_id')]]]; //Part Name?></td>
                            <td width="70">
                            <? if($cbo_category_by==1) echo change_date_format($country_ship_date,'dd-mm-yyyy','-');  
                            else echo change_date_format($ship_date,'dd-mm-yyyy','-'); 
                            ?>
                            </td>
                            <td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" title="Order Number : <? echo $row[csf('po_number')];//Order Quantity ?>"><?=number_format( $row[csf('poqtypcs')],0); ?></td>
                            <td width="70"><? echo change_date_format($row[csf('pack_handover_date')],'dd-mm-yyyy','-'); //Actual File Handover?></td>
                            <td width="70"><? 
								$confirmStatus=0;
								$confirmStatus=$confirmstatusArr[$row[csf('job_id')]][$row[csf('id')]]['confirm'];
								if($confirmStatus==1) echo 'Yes'; else if($confirmStatus==2) echo 'No'; else echo ''; //File Hand Over Status?></td>
                            <td width="100" style="word-break:break-all"><? echo $product_dept[$row[csf('product_dept')]]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $row[csf('style_description')]; //Description?></td>
                            <?php 
							$smv=$embro=$emb=$wash=$spworks=0;
							$smv=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['smv'];
							$embro=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['embro'];
							$emb=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['emb'];
							$wash=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['wash'];
							$spworks=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['spworks'];
                            ?>
                            <td width="70" align="center"><? echo $yes_no[$emb]; //Printing?></td>
                            <td width="70" align="center"><? echo $yes_no[$embro]; //Embroidery?></td>
                            <td width="70" align="center"><? echo $yes_no[$spworks]; //Smoking?></td>
                            <td width="70" align="center"><? echo $yes_no[$wash]; //Washing?></td>
                            <td width="70" align="center"><? echo $smv; //Sewing SMV?></td>
						</tr>
						<?
						$totpoqtypcs+=$row[csf('poqtypcs')];
						$i++;
                    }
                    unset($data_array);
                    ?>
                </table>
            </div>
            <table width="1760" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
                    <!-- <tr>
                        <th width="30">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100" id="total_tdpoqtypcs"><?//=number_format($totpoqtypcs,0); ?></th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                    </tr> -->
                </tfoot>
            </table>
        </div>
    </div>
	<?
	//   $con = connect();
	//   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=19");
	//   oci_commit($con);
	//   disconnect($con);

	foreach (glob("$user_id*.xls") as $filename)
	{
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "****$filename****show3";
	ob_end_flush();
	exit();
}
if($action=="report_generate2")
{
	
    extract($_REQUEST);
	$data=explode("_",$data);

	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_working_company=str_replace("'","",$cbo_working_company);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $txt_job_no=str_replace("'","",$txt_job_no);
    $txt_job_no_id=str_replace("'","",$txt_job_no_id);
    $start_date=str_replace("'","",$txt_date_from);
    $end_date=str_replace("'","",$txt_date_to);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);
	$date_category=str_replace("'","",$cbo_date_category);
	$buyer_name=str_replace("'","",$cbo_buyer_name);

	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	

	$company_id=$cbo_company_name;
	$company_cond=0;
	$company_field= "a.company_name as company_name" ;
	$company_field_caption= "Company Name" ;
	$company_group_by= "a.company_name" ;
	if($cbo_company_name ==0){
		echo "Please Select Company Name";
		die;
	}
	if($txt_job_no_id!=''){$job_id_con=" and a.id=$txt_job_no_id";} else echo $job_id_con="";
    if($txt_job_no!=''){$job_con=" and a.job_no like('%$txt_job_no%')";} else $job_con="";
    if($cbo_company_name!=0){$company_con=" and a.company_name in(".$cbo_company_name.")";}else echo $company_con="";
	if($cbo_working_company!=0){$working_company_con=" and a.working_company_id in(".$cbo_working_company.")";}
	if($buyer_name) $buyerCond=" and a.buyer_name in($buyer_name)"; else $buyerCond="";
    if($txt_style_ref!="") $style_ref_cond="and LOWER(a.style_ref_no) like LOWER('%".trim($txt_style_ref)."%')"; else $style_ref_cond="";
	
	$start_date=change_date_format($start_date,'','-',1);
	$end_date=change_date_format($end_date,'','-',1);

    $year_field=" and to_char(a.insert_date,'YYYY')";
	

	if ($start_date!="" && $end_date!="")
		{
			if($date_category==1) // Country Ship Date 
			{
				$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";
			}
			else if($date_category==2) // Pub Ship Date
			{
				$date_cond=" and b.pub_shipment_date between '$start_date' and  '$end_date'";
			}
			else if($date_category==3) // original Ship Date
			{
				$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
			}
			else if($date_category==4) // Po insert Date
			{
				$date_cond=" and b.insert_date between '$start_date' and  '$end_date'";
			}
		}
		else
		{
			$date_cond="";
			if($cbo_year_selection!=0) $year_cond=" $year_field=$cbo_year_selection"; else $year_cond="";
		}

	$target_basic_qnty=array();
	$total_target_basic_qnty=0;
	$sm = date('m',strtotime($start_date));
	$em = date('m',strtotime($end_date));

	$po_total_price_tot=0; $quantity_tot=0; $exfactory_tot=0; $po_total_price_tot_c=0; $quantity_tot_c=0; $po_total_price_tot_p=0; $quantity_tot_p=0; $booked_basic_qnty_tot_c=0; $booked_basic_qnty_tot_p=0;
	
	$colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$date=date('d-m-Y');

		$sqlQuery="SELECT a.id as job_id, a.job_no_prefix_num, a.company_name as company_name, to_char (a.insert_date, 'yyyy') as job_year, a.season_year, a.season_buyer_wise, a.buyer_name, a.brand_id, a.product_dept, a.job_no, a.style_ref_no, b.po_number, b.id, c.color_number_id, c.country_id, a.style_description, c.item_number_id, a.product_category,  b.is_confirmed, sum (c.order_quantity) as poqtypcs, sum (c.order_total) as poamt, a.order_uom, b.po_received_date, b.insert_date as po_insert_date, a.working_company_id,b.actual_po_no,b.unit_price,a.team_leader,a.inserted_by,b.shiping_status,c.po_break_down_id,c.country_ship_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id = b.job_id and a.id = c.job_id and b.id = c.po_break_down_id $company_con $working_company_con $style_ref_cond $job_id_con $job_con $date_cond $year_cond $buyerCond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.id , a.job_no_prefix_num, a.company_name, a.insert_date, a.season_year, a.season_buyer_wise, a.buyer_name, a.brand_id, a.product_dept, a.job_no, a.style_ref_no, b.po_number, b.id, c.color_number_id, c.country_id, a.style_description, c.item_number_id, a.product_category,  b.is_confirmed, a.order_uom, b.po_received_date, b.insert_date, a.working_company_id,b.actual_po_no,b.unit_price,a.team_leader,a.inserted_by,b.shiping_status,c.po_break_down_id,c.country_ship_date order by b.po_number,a.job_no_prefix_num asc";

	// echo $sqlQuery;die;
	$data_array=sql_select($sqlQuery);

	$datesqlQuery="SELECT a.id,b.id as po_id,min(b.pub_shipment_date) as min_pub_shipment_date, max(b.pub_shipment_date) as max_pub_shipment_date, min(b.pack_handover_date) as min_pcd_date, max(b.pack_handover_date) as max_pcd_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id = b.job_id and a.id = c.job_id and b.id = c.po_break_down_id $company_con $working_company_con $style_ref_cond $job_id_con $job_con $date_cond $year_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.id,b.id order by a.id";
	$shipDate_array=sql_select($datesqlQuery);
	$revisedDateArr=array();
	foreach($shipDate_array as $srow)
	{
		$revisedDateArr[$srow[csf('id')]][$srow[csf('po_id')]]['min_pub_shipment_date']=$srow[csf('min_pub_shipment_date')];
		$revisedDateArr[$srow[csf('id')]][$srow[csf('po_id')]]['max_pub_shipment_date']=$srow[csf('max_pub_shipment_date')];
		$revisedDateArr[$srow[csf('id')]][$srow[csf('po_id')]]['min_pcd_date']=$srow[csf('min_pcd_date')];
		$revisedDateArr[$srow[csf('id')]][$srow[csf('po_id')]]['max_pcd_date']=$srow[csf('max_pcd_date')];
	}
	unset($shipDate_array);

	$gmtsPartNamrArr=return_library_array("select id, product_type_id from lib_garment_item", "id", "product_type_id");

	$po_array_for_cond=array();
	$job_array_for_cond=array();
	$job_id_array_for_cond=array();
	$order_val_arr=array();
	$order_val=0;$order_qty_arr=array();
	foreach ($data_array as $row_po_job){
	 $po_array_for_cond[$row_po_job[csf("po_break_down_id")]]=$row_po_job[csf("po_break_down_id")];
	 $job_array_for_cond[$row_po_job[csf("job_no")]]="'".$row_po_job[csf("job_no")]."'";
	 $job_id_array_for_cond[$row_po_job[csf("job_id")]]="'".$row_po_job[csf("job_id")]."'";
	 $order_val_arr[csf("job_no")]['order_val']+=$row_po_job[csf('poamt')];
	 $order_qty_arr[csf("job_no")]['order_qty']+=$row_po_job[csf('poqtypcs')];
	 $order_values= $order_val+=$row_po_job[csf('poamt')];
	 $poQty=$poQty_val+=$row_po_job[csf('poqtypcs')];
	}
	//print_r($order_val_arr);die;
	$po_arr_cond=array_chunk($po_array_for_cond,1000, true);
	$job_arr_cond=array_chunk($job_array_for_cond,1000, true);
	$job_id_arr_cond=array_chunk($job_id_array_for_cond,1000, true);
	//print_r($job_arr_cond);
		$job_cond_for_in="";
		$ji=0;
		foreach($job_arr_cond as $key=> $value)
		{
		   if($ji==0){
			$job_cond_for_in=" and ( job_no  in(".implode(",",$value).")";
		   }
		   else{
			$job_cond_for_in.=" or job_no  in(".implode(",",$value).")";
		   }
		   $ji++;
		}
		$job_cond_for_in.=" )";
		$job_id_cond_for_in="";
		$jii=0;
		foreach($job_id_arr_cond as $key=> $value)
		{
		   if($jii==0){
			$job_id_cond_for_in=" and ( job_id  in(".implode(",",$value).")";
		   }
		   else{
			$job_id_cond_for_in.=" or job_id  in(".implode(",",$value).")";
		   }
		   $jii++;
		}
		$job_id_cond_for_in.=" )";
		$job_id_cond_for_in2="";
		$jii=0;
		foreach($job_id_arr_cond as $key=> $value)
		{
		   if($jii==0){
			$job_id_cond_for_in2=" and ( b.job_id  in(".implode(",",$value).")";
		   }
		   else{
			$job_id_cond_for_in2.=" or b.job_id  in(".implode(",",$value).")";
		   }
		   $jii++;
		}
		$job_id_cond_for_in2.=" )";
		$po_cond_for_in="";
		$pji=0;
		foreach($po_arr_cond as $key=> $value)
		{
		   if($pji==0){
			$po_cond_for_in=" and ( po_break_down_id  in(".implode(",",$value).")";
		   }
		   else{
			$po_cond_for_in.=" or po_break_down_id  in(".implode(",",$value).")";
		   }
		   $pji++;
		}
		$po_cond_for_in.=" )";

		$po_cond_for_in2="";
		$pji2=0;
		foreach($po_arr_cond as $key=> $value)
		{
		   if($pji2==0){
			$po_cond_for_in2=" and ( po_id  in(".implode(",",$value).")";
		   }
		   else{
			$po_cond_for_in2.=" or po_id  in(".implode(",",$value).")";
		   }
		   $pji2++;
		}
		$po_cond_for_in2.=" )";
	$sql_pre=sql_select("select id,job_no,costing_per from  wo_pre_cost_mst where 1=1 $job_cond_for_in");
	foreach($sql_pre as $job)
	{
		$costing_per_arr[$job[csf('job_no')]]=$job[csf('costing_per')];
	}

	//Ex_Factory_Date
	$sql_ex_factory_date=sql_select("select po_break_down_id, ex_factory_date  from pro_ex_factory_mst  where is_deleted=0 and status_active=1 $po_cond_for_in group by po_break_down_id,ex_factory_date");
	foreach ($sql_ex_factory_date as $rowex)
	{
		$ex_factory_date_arr[$rowex[csf('po_break_down_id')]]['ex_factory_date']=$rowex[csf('ex_factory_date')];
	}
	unset($sql_ex_factory_date);
	
	//Done_Date
	$sql_phd_done_sql=sql_select("select job_id, po_id,insert_date  from gmts_production_confirmation  where is_deleted=0 and status_active=1 $po_cond_for_in2 group by job_id, po_id,insert_date");
	foreach ($sql_phd_done_sql as $rowphp)
	{
		$php_done_date_arr[$rowphp[csf('job_id')]][$rowphp[csf('po_id')]]['insert_date']=$rowphp[csf('insert_date')];
	}
	unset($sql_phd_done_sql);
	//Item Details Part (Sewing,Cutting, Finishing, Printing, Embro, Wash, Sp)
	$sqlSet=sql_select("Select job_id,job_no, gmts_item_id, smv_set,embro,embelishment,wash,spworks,cutsmv_pcs,finsmv_pcs,smv_pcs from wo_po_details_mas_set_details  where 1=1 $job_cond_for_in");
	$itemDtlsArr=array();
	foreach($sqlSet as $srow)
	{
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['smv']=$srow[csf('smv_set')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['embro']=$srow[csf('embro')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['emb']=$srow[csf('embelishment')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['wash']=$srow[csf('wash')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['spworks']=$srow[csf('spworks')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['cutsmv_pcs']=$srow[csf('cutsmv_pcs')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['finsmv_pcs']=$srow[csf('finsmv_pcs')];
		$itemDtlsArr[$srow[csf('job_id')]][$srow[csf('gmts_item_id')]]['smv_pcs']=$srow[csf('smv_pcs')];
	}
	unset($sqlSet);
	//Order Status
	$sqlConfirm=sql_select("select job_id, po_id, confirm_status from gmts_production_confirmation where 1=1 $job_id_cond_for_in and status_active=1 and is_deleted=0 ");

	$confirmstatusArr=array();
	foreach($sqlConfirm as $crow)
	{
		$confirmstatusArr[$crow[csf('job_id')]][$crow[csf('po_id')]]['confirm']=$crow[csf('confirm_status')];
	}
	unset($sqlConfirm); 
	$pre_fabric_arr="select  b.job_id,b.item_number_id,b.lib_yarn_count_deter_id as deter_min_id, b.fabric_description as fab_desc from wo_pre_cost_fabric_cost_dtls b where  b.status_active=1 and b.is_deleted=0 $job_id_cond_for_in2 group by  b.job_id,b.item_number_id,b.lib_yarn_count_deter_id, b.fabric_description  order by b.job_id,b.item_number_id";
	$pre_fabric_result=sql_select($pre_fabric_arr);
	$fabricdescArray=array();
	foreach($pre_fabric_result as $frow)
	{
		$fabricdescArray[$frow[csf('job_id')]][$frow[csf('item_number_id')]]['fabric_desc'].=$frow[csf('fab_desc')].'.';
	}
	unset($pre_fabric_result); 

	$pre_fab_arr="select  b.job_id,b.item_number_id,c.type_id from wo_pre_cost_fabric_cost_dtls b,lib_yarn_count_determina_dtls c where  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_id_cond_for_in2 and b.lib_yarn_count_deter_id=c.mst_id order by b.job_id,b.item_number_id ";//and b.fabric_source=2
	$pre_fab_result=sql_select($pre_fab_arr);
	$typedescArray=array();
	foreach($pre_fab_result as $frow)
	{
		$typedescArray[$frow[csf('job_id')]][$frow[csf('item_number_id')]]['type_id'].=$yarn_type[$frow[csf('type_id')]].',';
	}
	unset($pre_fab_result); 
	$job_no=str_replace("'","",$txt_job_no);
	$cbo_company_na=str_replace("'","",$cbo_company_name);

	
	$condition= new condition();
	$condition->company_name("=$cbo_company_na");
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no_prefix_num("=$job_no");
	}
	 if(str_replace("'",'',$txt_style_ref) !=""){
		$condition->style_ref_no("=$txt_style_ref");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to);
		$condition->pub_shipment_date(" between '$start_date' and '$end_date'");

	} 
	$condition->init();
	$fabric= new fabric($condition);
	 //echo $fabric->getQuery(); die;
	$trim= new trims($condition);
	$emblishment= new emblishment($condition);
	$wash= new wash($condition);
	$other= new other($condition);
	$commercial= new commercial($condition);
	$commision= new commision($condition);
	
	$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$trims_costing_arr=$trim->getAmountArray_by_job();
	$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
	$commercial_costing_arr=$commercial->getAmountArray_by_job();
	$commission_costing_arr=$commision->getAmountArray_by_job();
	$other_costing_arr=$other->getAmountArray_by_job();
	$ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];

	$sql_dtls = "select job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche
	from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $job_cond_for_in";
	$pre_cost_dtls_data=sql_select($sql_dtls);
	$summary_data=array();
	foreach($pre_cost_dtls_data as $row)
	{
		$job_wise_data[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_cost')];
		$summary_data[price_dzn]=$row_new[csf("price_dzn")];
		$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
		$summary_data[commission]=$row_new[csf("commission")];
		$summary_data[trims_cost]=$row_new[csf("trims_cost")];
		$summary_data[emb_cost]=$row_new[csf("embel_cost")];

		$summary_data[lab_test]=$row_new[csf("lab_test")];
		$summary_data[lab_test_job]=$other_costing_arr[$row_new[csf("job_no")]]['lab_test'];

		$summary_data[inspection]=$row_new[csf("inspection")];
		$summary_data[inspection_job]=$other_costing_arr[$row_new[csf("job_no")]]['inspection'];

		$summary_data[freight]=$row_new[csf("freight")];
		$summary_data[freight_job]=$other_costing_arr[$row_new[csf("job_no")]]['freight'];

		$summary_data[currier_pre_cost]=$row_new[csf("currier_pre_cost")];
		$summary_data[currier_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['currier_pre_cost'];

		$summary_data[certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
		$summary_data[certificate_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['certificate_pre_cost'];
		$summary_data[wash_cost]=$row_new[csf("wash_cost")];

		$summary_data[OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
		$summary_data[OtherDirectExpenses_job]=$summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job];

		$summary_data[cm_cost]=$row_new[csf("cm_cost")];
		$summary_data[cm_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['cm_cost'];
		$summary_data[comm_cost]=$row_new[csf("comm_cost")];
		$summary_data[common_oh]=$row_new[csf("common_oh")];
		$summary_data[common_oh_job]=$other_costing_arr[$row_new[csf("job_no")]]['common_oh'];
		$summary_data[depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
		$summary_data[depr_amor_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
		$summary_data[margindzn]=$row_new[csf("margin_dzn")];
		$summary_data[fabric_percent]=$row_new[csf("fabric_cost_percent")];
		$summary_data[trims_percent]=$row_new[csf("trims_cost_percent")];
		$summary_data[wash_percent]=$row_new[csf("wash_cost_percent")];
		$summary_data[emb_percent]=$row_new[csf("embel_cost_percent")];
		$summary_data[commercial_percent]=$row_new[csf("comm_cost_percent")];
		$summary_data[currier_percent]=$row_new[csf("currier_percent")];
		$summary_data[commission_percent]=$row_new[csf("commission_percent")];
		$summary_data[lab_test_percent]=$row_new[csf("lab_test_percent")];
		$summary_data[freight_percent]=$row_new[csf("freight_percent")];
		$summary_data[margin_dzn_percent]=$row_new[csf("margin_dzn_percent")];
		$summary_data[cm_cost_percent]=$row_new[csf("cm_cost_percent")];
	}
	unset($pre_cost_dtls_data); 


	//========================================================================================
 
	ob_start();
	?>
    <div>
        <h3 width="4390" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
        <div id="content_report_panel">
            <table width="4370" id="table_header" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
						<th width="100">LC Company</th>
                        <th width="60" >Company Short Name</th>
						<th width="100">Working Company</th>
                        <th width="60">Year</th>
                        <th width="60">Season Year</th>
                        <th width="100">Season</th>
                        <th width="100">Buyer</th>
                        <th width="100">Brand</th>
                        <th width="120">Product Dept.</th>
                        <th width="80">Job No</th>
                        <th width="80">Style Ref</th>
                        <th width="80">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">Color</th>
                        <th width="100">Country</th>
                        <th width="100">Style Description</th>
                        <th width="100">Item</th>
                        <th width="80">Plan Ex-Factory Date</th>
						<th width="80">Revised(Plan<br>Ex-Factory Date)</th>
						<th width="100">Country Ship Date</th>
						<th width="80">Ex-Factory Date</br></th>
                        <th width="100">Part Name</th>
                        <th width="80">PHD. Date</th>
						<th width="80">Revised Plan PHD<br>Status(Done Date)</th>
						<th width="80">PHD Status (Done Date)</th>
						<th width="80">Order Status</th>
						<th width="60">Img</th>
						<th width="240">Fabric Description</th>
						<th width="120">Fabric Type</th>
						<th width="60">Order Qty</th>
						<th width="60">Uom</th>
						<th width="60">FOB</th>
						<th width="60">Order Qty in Pcs</th>
						<th width="80">Sewing SMV</th>
						<th width="80">Cutting SAM</th>
						<th width="80">Finishing SAM</th>
						<th width="80">Total Minute</th>
						<th width="80">Order Value</th>
						<th width="80">PO Rec. Date</th>
						<th width="80">PO Insert Date</th>
						<th width="80">PO Lead Time</th>
						<th width="80">Printing</th>
						<th width="80">Embroidery</th>
						<th width="80">Smoking</th>
						<th width="80">Wash Type</th>
						<th width="100">Shipping Status</th>
						<th width="80">File</th>
						<th width="100">Team Leader</th>
						<th width="80">Team Name</th>
						<th width="80">User Name</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:4390px"  align="left" id="scroll_body">
                <table width="4370" border="1" class="rpt_table" rules="all" id="table_body">
					<?
                    $i=1; $order_qnty_pcs_tot=0;  $order_qntytot=0; 
                    foreach ($data_array as $row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($cbo_category_by==1)
						{
							$country_name=$country_name_arr[$row[csf('country_id')]];
							$country_ship_date=$row[csf('country_ship_date')];
						}
						else
						{
							$country_name="";
							$country_id=array_unique(explode(",",$row[csf('country_id')]));
							foreach($country_id as $c_id)
							{
								if($country_name=="") $country_name=$country_name_arr[$c_id]; else $country_name.=",".$country_name_arr[$c_id];
							}
							$ship_date=$row[csf('pub_shipment_date')];
						}
						$brand_arr = return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
						$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
						$order_qty=$row[csf('poqtypcs')];
						$costing_per_pcs=0;						
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per ==1) $costing_per_pcs=1*12;
						else if($costing_per==2) $costing_per_pcs=1*1;
						else if($costing_per==3) $costing_per_pcs=2*12;
						else if($costing_per==4) $costing_per_pcs=3*12;
						else if($costing_per==5) $costing_per_pcs=4*12;
						//echo "OrdrQTy=".$order_qty."costingPerPcs=".$costing_per_pcs."CM=".$job_wise_data[$row[csf('job_no')]]['cm_cost'];
						 $cmpss=$job_wise_data[$row[csf('job_no')]]['cm_cost']/$costing_per_pcs;
						$cm_cost=$cmpss*$order_qty;

						$date1=change_date_format($row[csf("po_received_date")]);
						$date2=change_date_format($row[csf("pub_shipment_date")]);
              
					
						$diff = abs(strtotime($date1) - strtotime($date2));
						$days = floor($diff / (60*60*24));

						$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('job_no')]]);
							$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('job_no')]]);
							
							$fabricCost=$fab_purchase_knit+$fab_purchase_woven;
							$cpmCal=($financial_para[$pre_costing_date][cost_per_minute]/$row[csf("exchange_rate")])/($sew_effi_percent/100);
							$totMaterialCost=$fabricCost+$trims_costing_arr[$row[csf('job_no')]]+$emblishment_costing_arr_wash[$row[csf('job_no')]]+$emblishment_costing_arr[$row[csf('job_no')]];
							$otherCost=$commercial_costing_arr[$job_no]+$other_costing_arr[$row[csf('job_no')]]['currier_pre_cost']+$commission_costing_arr[$row[csf('job_no')]]+$other_costing_arr[$row[csf('job_no')]]['lab_test']+$other_costing_arr[$row[csf('job_no')]]['freight'];
							$breakevencm=$cpmCal*$sew_smv*$poQty;
							$order_val=$order_val_arr[csf("job_no")]['order_val'];
							$poQty=$order_qty_arr[csf("job_no")]['order_qty'];
							$calCM=$order_val-($totMaterialCost+$otherCost);
							//echo $order_val."=".$totMaterialCost."=".$otherCost."=".$poQty."=".$row[csf('job_no')]."<br>";
							
							$cmPcs=$calCM/$poQty;
							//echo $cmPcs;die;
							$totalMargin=$calCM-$breakevencm;
							$marginPcs=$totalMargin/$poQty;

						    
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" id="tr_<? echo $i; ?>">
							<td width="30" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$i;?> </td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$company_arr[$row[csf('company_name')]];  ?> </td>
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$company_short_name_arr[$row[csf('company_name')]]; ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$company_arr[$row[csf('working_company_id')]]; ?> </td>
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('job_year')];?> </td>
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('season_year')];?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$season_library[$row[csf('season_buyer_wise')]];  ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$buyer_full_name_arr[$row[csf('buyer_name')]];  ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$brand_arr[$row[csf('brand_id')]]; ?> </td>
							<td width="120" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$product_dept[$row[csf('product_dept')]]; ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('job_no')]; ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('style_ref_no')]; ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;" title="<?=$row[csf('po_break_down_id')];?>"> <?=$row[csf('po_number')]; ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('actual_po_no')]; ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;" title="<?=$row[csf('color_number_id')];?>"> <?=$colorArr[$row[csf('color_number_id')]];  ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$country_arr[$row[csf('country_id')]];  ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('style_description')];   ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$garments_item[$row[csf('item_number_id')]];  ?> </td>


							<?php 
							$smv=$embro=$emb=$wash=$spworks=0;$cutsmv_pcs=0;$finsmv_pcs=0;$smv_pcs=0;$team_name=0;$ex_factory_date=0;
							$smv=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['smv'];
							$embro=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['embro'];
							$emb=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['emb'];
							$wash=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['wash'];
							$spworks=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['spworks'];
							$cutsmv_pcs=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['cutsmv_pcs'];
							$finsmv_pcs=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['finsmv_pcs'];
							$smv_pcs=$itemDtlsArr[$row[csf('job_id')]][$row[csf('item_number_id')]]['smv_pcs'];
							$ex_factory_date=$ex_factory_date_arr[$row[csf('po_break_down_id')]]['ex_factory_date'];
							$min_pub_shipment_date=$revisedDateArr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['min_pub_shipment_date'];
							$max_pub_shipment_date=$revisedDateArr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['max_pub_shipment_date'];
							$min_pcd_date=$revisedDateArr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['min_pcd_date'];
							$max_pcd_date=$revisedDateArr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['max_pcd_date'];
							$done_date=$php_done_date_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['insert_date'];
							$fabric_description=implode(",",array_filter(array_unique(explode(",",chop($fabricdescArray[$row[csf('job_id')]][$row[csf('item_number_id')]]['fabric_desc'],",")))));
							$fabric_type=implode(",",array_filter(array_unique(explode(",",chop($typedescArray[$row[csf('job_id')]][$row[csf('item_number_id')]]['type_id'],",")))));
                            ?>


							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($min_pub_shipment_date,'dd-mm-yyyy','-'); ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($max_pub_shipment_date,'dd-mm-yyyy','-'); ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($row[csf('country_ship_date')],'dd-mm-yyyy','-'); ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($ex_factory_date,'dd-mm-yyyy','-'); ?> </td>
							<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"> <?echo $product_types[$gmtsPartNamrArr[$row[csf('item_number_id')]]]; ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($min_pcd_date,'dd-mm-yyyy','-'); ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($max_pcd_date,'dd-mm-yyyy','-'); ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($done_date,'dd-mm-yyyy','-'); ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?if ($row[csf('is_confirmed')]==1) echo "confirm"; else echo "";  ?> </td>
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;" onClick="openmypage_image('requires/order_recap_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /> </td>
							<td width="240" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$fabric_description;   ?> </td> 
							<td width="120" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$fabric_type?> </td> 
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=number_format( $row[csf('poqtypcs')],0);   ?> </td> 
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$unit_of_measurement[$row[csf('order_uom')]]; ?> </td>
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$row[csf('unit_price')]; ?> </td>
							<td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=number_format( $row[csf('poqtypcs')],0); ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$smv_pcs;  ?> </td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=$cutsmv_pcs;  ?> </td>
                            <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$finsmv_pcs;?></td>
                            <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><? $tot_minute=$smv_pcs*$order_qty; echo number_format( $tot_minute,0);  ?></td>
                            <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><?=number_format( $row[csf('poamt')],2);?></td>
                            <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');?></td>
							<td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"> <?=change_date_format($row[csf('po_insert_date')],'dd-mm-yyyy','-'); ?> </td>
                            <td width="80" style="word-break:break-all" align="center" ><?=$days;?></td>
                            <td width="80" style="word-break:break-all" align="center"><?=$yes_no[$emb];  ?></td> 
                            <td width="80" style="word-break:break-all" align="center"><?=$yes_no[$embro]; ?></td>
                            <td width="80" style="word-break:break-all" align="center">  <?=$yes_no[$spworks]; ?> </td>
                            <td width="80" style="word-break:break-all" align="center"><?=$yes_no[$wash];?></td>
                            <td width="100" style="word-break:break-all" align="center"><?=$shipment_status[$row[csf('shiping_status')]];?></td>
                            <td width="80" style="word-break:break-all" align="center"><input type="button" class="image_uploader" id="system_id" style="width:50px" value="File"  onClick="openmypage_file('show_file','<? echo $row[csf("job_no")]; ?>','2')"/></td>
                            <td width="100" style="word-break:break-all" align="center"><?=$team_leader_name_arr[$row[csf('team_leader')]];?></td>
							<td width="80" style="word-break:break-all" align="center"><?=$company_team_name_arr[$row[csf('team_leader')]];?></td>
                            <td  width="80" style="word-break:break-all" align="center"><?=$user_arr[$row[csf('inserted_by')]];?></td>
						</tr>
						<?
						$totpoqtypcs+=$row[csf('poqtypcs')];
						$i++;
                    }
                    unset($data_array);
                    ?>
                </table>
            </div>
            <table width="4370" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="60" >&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="240">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60" align="left" id="tot_order_qty">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60" align="left" id="tot_order_qty_pcs">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="left" id="tot_minute">&nbsp;</th>
						<th width="80" align="left" id="tot_order_val">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
					</tr>
                </tfoot>
            </table>
        </div>
    </div>
	<?

	foreach (glob("$user_id*.xls") as $filename)
	{
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "****$filename****show";
	ob_end_flush();
	exit();
}

?>