<?
/*-------------------------------------------- Comments----------------
Version                  :   V2
Purpose			         : 	This form will create  Capacity and Order Booking Status Report V2
Functionality	         :
JS Functions	         :
Created by				 :	Md Mamun Ahmed Sagor 
Creation date 			 : 	15/03/2023 
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :  Oracle Compatible Version
-----------------------------------------------------------------------*/
session_start();
include('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');

extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;



$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id";

$location_id = $userCredential[0][csf('location_id')];
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
$location_credential_cond="";

if ($location_id) {
    $location_credential_cond = " and id in($location_id)";
}

if($action=="get_company_config"){
	$action($data);
}

function get_company_config($data)
{
	global $location_credential_cond;
	global $selected;
	global $buyer_cond;
	$loc="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name";
	// echo $loc;
	$result_loc=sql_select($loc);
	$index=$selected;
	if(count($result_loc)==1)
	{
		$index=$result_loc[0][csf('id')];
	}
	
	$cbo_location_name= create_drop_down( "cbo_location_id", 100, $loc,"id,location_name", 1, "-- Select Location --", $index, "" ); 
	
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "" );
	
	$cbo_agent= create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	
	echo "document.getElementById('location_td').innerHTML = '".$cbo_location_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	
	$report_date_catagory=return_field_value("report_date_catagory", "variable_order_tracking", "company_name='$data' and variable_list=42 and status_active=1 and is_deleted=0");
	if($report_date_catagory=="")
	{
		$report_date_catagory=1;
	}
	if($report_date_catagory=="") $report_date_catagory=0;
	echo "$('#cbo_category_by').val('".$report_date_catagory."');\n";
	
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='$data'  and module_id=11 and report_id=46 and is_deleted=0 and status_active=1");
	//echo $print_report_format;
	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#search').hide();\n";
	echo "$('#search1').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#show2').hide();\n";
	echo "$('#summary').hide();\n";
	echo "$('#summary3').hide();\n";
	echo "$('#summary2').hide();\n";
	echo "$('#summary4').hide();\n";
	echo "$('#summary5').hide();\n";
	echo "$('#summary6').hide();\n";
	echo "$('#button2').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==147){echo "$('#search').show();\n";}
			if($id==148){echo "$('#search1').show();\n";}
			if($id==149){echo "$('#summary').show();\n";}
			if($id==150){echo "$('#summary2').show();\n";}
			if($id==276){echo "$('#search2').show();\n";}			
			if($id==277){echo "$('#summary3').show();\n";}
			if($id==689){echo "$('#summary4').show();\n";}
			if($id==691){echo "$('#summary5').show();\n";}
			if($id==690){echo "$('#summary6').show();\n";}
			if($id==340){echo "$('#button2').show();\n";}
			if($id==305){echo "$('#show2').show();\n";}
		}
	}
	else
	{
		echo "$('#search').show();\n";
		echo "$('#search1').show();\n";
		echo "$('#search2').show();\n";
		echo "$('#summary').show();\n";
		echo "$('#summary2').show();\n";
		echo "$('#summary3').show();\n";
		echo "$('#summary4').show();\n";
		echo "$('#summary5').show();\n";
		echo "$('#summary6').show();\n";
		echo "$('#button2').show();\n";
	}
	exit();
}

if($action=="style_owner_config"){
	$action($data);
}

function style_owner_config($data)
{
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "" );
	
	$cbo_agent= create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	
	$report_date_catagory=return_field_value("report_date_catagory", "variable_order_tracking", "company_name='$data' and variable_list=42 and status_active=1 and is_deleted=0");
	if($report_date_catagory=="")
	{
		$report_date_catagory=1;
	}
	if($report_date_catagory=="") $report_date_catagory=0;
	
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	echo "$('#cbo_category_by').val('".$report_date_catagory."');\n";
	exit();
}


$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "load_drop_down( 'requires/capacity_and_order_booking_status_report_v2_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );
}
if ($action=="load_drop_down_season_buyer")
{
	
	echo create_drop_down( "cbo_season_name", 140, "select id, season_name from lib_buyer_season where buyer_id in ($data) and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 100, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Dealing Merchant-", $selected, "" );
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --", $selected, "" );
}




if ($action=="get_defult_date")
{
	$report_date_catagory=return_field_value("report_date_catagory", "variable_order_tracking", "company_name=$data  and variable_list=42 and status_active=1 and is_deleted=0");
	if($report_date_catagory=="")
	{
		$report_date_catagory=1;
	}
	echo $report_date_catagory;
	die;
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data'
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company_name,$buyer_name)=explode('_',$data);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#job_no_id").val(splitData[0]);
			$("#job_no_val").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
        <input type="hidden" id="job_no_id" />
        <input type="hidden" id="job_no_val" />
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
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                    </th>
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Order No",3=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'job_no_popup_list', 'search_div', 'capacity_and_order_booking_status_report_v2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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

if ($action=="job_no_popup_list")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($companyID,$buyer_name,$search_by,$search_common)=explode('**',$data);
 
 	if ($buyer_name!=0) $where_con.=" and b.buyer_name=$buyer_name";
	
	if($search_by==1 && $search_common!=''){
		$where_con.=" and b.job_no like('%".$search_common."%')";
	}
	else if($search_by==2 && $search_common!='')
	{
		$where_con.=" and a.po_number like('%".$search_common."%')";
	}
	else if($search_by==3 && $search_common!='')
	{
		$where_con.=" and b.style_ref_no like('%".$search_common."%')";
	}
	

	$sql="select b.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$companyID and b.is_deleted=0 $where_con ORDER BY b.job_no";
	  //echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,job_no_prefix_num", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "budget_breakdown_report_controller",'','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}


if($type=="report_generate")
{
	$data=explode("_",$data);
	$cbo_company_name=$data[0];
	$cbo_style_owner=$data[14];
	$cbo_location_id=$data[15];
	$txt_job_no=trim($data[16]);
	$txt_job_id=trim($data[17]);
	$company_id==0;
	$company_cond=0;
	$company_field= "a.company_name as company_name" ;
	$company_field_caption= "Company Name" ;
	$company_group_by= "a.company_name" ;
	if($cbo_company_name ==0 &&  $cbo_style_owner == 0){
		echo "Please Select Company Name or Style Owner";
		die;
	}

		//********************************************liabrary******************************/
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0 order by sequence_no",'id','short_name');
		$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');


	if($txt_job_id!=''){$job_con=" and a.id=$txt_job_id";}
	else if($txt_job_no!=''){$job_con=" and a.job_no like('%$txt_job_no%')";}
	$txt_internal_ref=str_replace("'", "", $txt_internal_ref);
	$internal_ref_cond='';
	if(!empty($txt_internal_ref))
	{
		$internal_ref_cond=" and b.grouping='".$txt_internal_ref."'";
	}
	//echo $job_con;
	//if($job_no!='') {$job_id_con=" and a.job_no like('$job_id')";} else $job_con="";
     
	// if($data[2] =="" &&  $data[3]== ""){
	// 	echo "Please Select Date Range";
	// 	die;
	// }
	//echo $job_con.'system';die;
	if($cbo_company_name >0 &&  $cbo_style_owner > 0){
		$company_id=$cbo_company_name;
		$company_cond= "and a.company_name=$cbo_company_name";
		$company_field= "a.company_name as company_name" ;
		$company_group_by= "a.company_name" ;
		$company_field_caption= "Company Name" ;
	}
	if($cbo_company_name ==0 &&  $cbo_style_owner > 0){
		$company_id=$cbo_style_owner;
		$company_cond= "and a.style_owner=$cbo_style_owner";
		$company_field= "a.style_owner as company_name" ;
		$company_group_by= "a.style_owner" ;
		$company_field_caption= "Style Owner" ;
	}
	if($cbo_company_name >0 &&  $cbo_style_owner == 0){
		$company_id=$cbo_company_name;
		$company_cond= "and a.company_name=$cbo_company_name";
		$company_field= "a.company_name as company_name" ;
		$company_group_by= "a.company_name" ;
		$company_field_caption= "Company Name" ;
	}

	if($company_id==0) $company_name="%%"; else $company_name=$company_id;

	if($data[1]!=''){$buyer_name_con=" and a.buyer_name in(".$data[1].")";}
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	if(trim($data[4])=="0") $team_leader="%%"; else $team_leader="$data[4]";
	if(trim($data[5])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[5]";
	if(trim($data[11])=="0") $factory_marchant="%%"; else $factory_marchant="$data[11]";
	if(trim($data[12])=="0") $agent_con=""; else $agent_con=" and a.agent_name=$data[12]";

	if(trim($data[13])=="0") $product_category_con=""; else $product_category_con=" and a.product_category=$data[13]";
	if(trim($data[11])>0){$factory_marchant_con=" and a.factory_marchant=".$data[11].""; }
	
	
	
	if($cbo_location_id>0) echo "<font style='color:#F00; font-size:14px; font-weight:bold'>Location is not considerable for this report.</font>";

	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'','-',1);
		$end_date=change_date_format($end_date,'','-',1);
    }



	$cbo_category_by=$data[6];
	$zero_value=$data[7];
	$cbo_capacity_source=$data[8];
	$cbo_year=$data[9];

	if(trim($data[10])!="") $style_ref_cond="%".trim($data[10])."%"; else $style_ref_cond="%%";

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond_2=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond_2=" $year_field_con=$cbo_year"; else $year_cond="";
	}

	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond="and c.country_ship_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.pub_shipment_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else if($cbo_category_by==3)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else
	{
		if ($start_date!="" && $end_date!="")
		{
			if($db_type==0){
			$date_cond=" and date(b.insert_date) between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
			}
			else if($db_type==2){
				$date_cond=" and TRUNC(b.insert_date) between '$start_date' and  '$end_date'";
				$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
			}
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	//echo $date_cond.'=='.$cbo_category_by;

	if($cbo_capacity_source==1){
		$capacity_source_cond="and c.capacity_source=1";
	}
	else if($cbo_capacity_source==3){
		$capacity_source_cond="and c.capacity_source=3";
	}
	else{
		$capacity_source_cond="";
	}

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
    $target_basic_qnty=array();
	$total_target_basic_qnty=0;
    $sm = date('m',strtotime($start_date));
	$em = date('m',strtotime($end_date));

	if($cbo_location_id!=0){
		$locationCon_c=" and c.location_id =$cbo_location_id";
		$locationCon_a=" and a.location_id =$cbo_location_id";
	}
	

	
	$color_lib_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');


	$po_total_price_tot=0;
	$quantity_tot=0;
	$exfactory_tot=0;


	$po_total_price_tot_c=0;
	$quantity_tot_c=0;

	$po_total_price_tot_p=0;
	$quantity_tot_p=0;

	$booked_basic_qnty_tot_c=0;
	$booked_basic_qnty_tot_p=0;
		
	

	$date=date('d-m-Y');

	// sum(c.order_quantity/a.total_set_qnty) as po_quantity

	$sql="select a.SET_BREAK_DOWN,a.job_no_prefix_num, a.job_no, $company_field, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.factory_marchant, a.product_code, b.id as po_id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, sum(c.order_total) as order_total, b.pub_shipment_date, b.po_received_date,min(TRUNC(b.insert_date)) as insert_date, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.delay_for, b.grouping, b.file_no, c.shiping_status, c.country_remarks, c.country_ship_date, c.country_id, b.t_year, b.t_month,a.season_buyer_wise,a.pro_sub_dep,a.product_dept, a.ship_mode,c.color_number_id,b.shipment_date
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c , lib_buyer d 
	where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.buyer_name=d.id and d.status_active=1 $company_cond $buyer_name_con and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $factory_marchant_con $agent_con $date_cond $year_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $product_category_con $job_con $internal_ref_cond
	group by a.SET_BREAK_DOWN,a.job_no_prefix_num, a.job_no, $company_group_by, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.factory_marchant, a.product_code, a.ship_mode, b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.delay_for, b.grouping, b.file_no, b.t_year, b.t_month, c.country_id, c.country_remarks, c.country_ship_date, c.shiping_status,a.season_buyer_wise,a.pro_sub_dep,a.product_dept,c.color_number_id,b.shipment_date order by b.shipment_date,a.buyer_name";

			//	echo $sql;
		$data_array=sql_select($sql);




	 

	//========================================================================================
	$po_array_for_cond=array();
	$job_array_for_cond=array();
	foreach ($data_array as $row_po_job){
	 $po_array_for_cond[$row_po_job[csf("id")]]=$row_po_job[csf("id")];
	 $job_array_for_cond[$row_po_job[csf("job_no")]]="'".$row_po_job[csf("job_no")]."'";
	}
	//echo '<pre>';print_r($po_array_for_cond);

	if($cbo_category_by==1)
	{
		$job_arr_cond=array_chunk($job_array_for_cond,1000, true);
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

	}
	else $job_cond_for_in='';

	$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
	$sql_pre=sql_select("select id,job_no,costing_per,prod_line_hr,machine_line from  wo_pre_cost_mst where 1=1 $job_cond_for_in");
	foreach($sql_pre as $job)
	{
		$costing_per_arr[$job[csf('job_no')]]=$job[csf('costing_per')];
		$costing_pre_mstID_arr[$job[csf('job_no')]]=$job[csf('id')];
		$job_wise_data[$job[csf('job_no')]]['prod_line_hr']=$job[csf('prod_line_hr')];
		$job_wise_data[$job[csf('job_no')]]['machine_line']=$job[csf('machine_line')];
	}

	 
 
	$pre_cost_dtls_data=sql_select( "select job_no,total_cost,cm_cost from  wo_pre_cost_dtls where status_active=1 $job_cond_for_in");

 

	foreach($pre_cost_dtls_data as $row)
	{
		$job_wise_data[$row[csf('job_no')]]['total_cost']=$row[csf('total_cost')];
		$job_wise_data[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_cost')];
		// $costing_pre_mstID_arr[$row[csf('job_no')]]=$row[csf('id')];
	}

	  
	
	$po_arr_cond=array();
	$po_array_for_cond=array();
	$exfactory_tot_yet=0;
	$exfactory_tot_over=0;

	foreach ($data_array as $row){
		$y = date('Y',strtotime($row[csf("shipment_date")]));
		$m = date('m',strtotime($row[csf("shipment_date")]));

		$m =(int)$m;

		$po_total_price_tot+=$row[csf("order_total")];
		$quantity_tot+=$row[csf("po_quantity_pcs")];
		$company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]]['order_qty_pcs']+=$row[csf("po_quantity_pcs")];
		$company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]]['order_value']+=$row[csf("order_total")];

		$main_data_arr[$m][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("gmts_item_id")]][$row[csf("color_number_id")]]['order_qty_pcs']+=$row[csf("po_quantity_pcs")];
		$main_data_arr[$m][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("gmts_item_id")]][$row[csf("color_number_id")]]['unit_price']=$row[csf("unit_price")];
		$main_data_arr[$m][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("gmts_item_id")]][$row[csf("color_number_id")]]['fob_value']+=($row[csf("unit_price")]*$row[csf("po_quantity_pcs")]);
		$main_data_arr[$m][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("gmts_item_id")]][$row[csf("color_number_id")]]['po_number']=$row[csf("po_number")];
		$main_data_arr[$m][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("gmts_item_id")]][$row[csf("color_number_id")]]['shipment_date']=$row[csf("shipment_date")];
		$main_data_arr[$m][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("gmts_item_id")]][$row[csf("color_number_id")]]['job_no']=$row[csf("job_no")]; 


		$monthsId[$m]=(int)$m;
		$years[$y]=(int)$y;
		

		
		
		$po_ids .=$row[csf("po_id")].",";
	}

	   $po_ids=rtrim($po_ids,",");




		$monthsIds=implode(",",$monthsId); 
		$years=implode(",",$years);
		
		$capacity_data=sql_select("SELECT  b.month_id , sum(b.capacity_min) capacity_min, sum(b.capacity_pcs) capacity_pcs,count(b.id) as monthly_day
		from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.day_status=1 and a.location_id=0 and a.year in ($years)	 and b.month_id in ($monthsIds) and a.capacity_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		 group by b.month_id");
		  

		 foreach($capacity_data as $row){

				$month_wise_data[$row[csf("month_id")]]['monthly_day']=$row[csf("monthly_day")];

		 }



		 	$sales_contact_data=sql_select("select wb.id, ci.id as idd, wb.po_number,wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, ci.com_sales_contract_id, ci.category_no, st.contract_no from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci, com_sales_contract st   where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and wb.id in($po_ids) and ci.com_sales_contract_id=st.id and ci.status_active = '1' and ci.is_deleted = '0' and wb.is_deleted = 0 and wb.status_active = 1 and wm.is_deleted = 0 and wm.status_active = 1 order by ci.id");
			 
			foreach($sales_contact_data as $row){

				$po_item_wise_data[$row[csf("id")]][$row[csf("gmts_item_id")]]['contract_no']=$row[csf("contract_no")];

			 }


		//  echo "<pre>";
		//  print_r($month_wise_data);

	ob_start();
	?>
	<div>
	

	<table width="100%">
	<tr>
	
	<td width="30%" valign="top">
	
	<br/>                                                                                                                                                                                                                                                                                  
	<table>
	
	</table>
	<!-- id="accordion_h4" class="accordion_h" -->
	    <h3 width="2410" align="left"  onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
	    <div id="content_report_panel">
	        <table width="2410" id="table_header_1" border="1" class="rpt_table" rules="all">
	            <thead>
	                <tr>
	                    <th width="50">SL</th>
	                    <th width="50">Buyer</th>
	                    <th width="150">Order No</th>
	                    <th width="90">Item Details</th>
						<th width="100">Color</th>
	                   
						<th width="90">Order Qnty(Pcs)</th>
	                    <th width="50">Per Unit Price</th>
	                    <th width="100">FOB Value</th>	     
						<th width="100">Meterial Bill</th>              
	                    <th width="80">CM</th>
	                    <th width="90">Meterial Bill %</th>
	                    <th width="90">Original Ship Date</th>
						<th width="100" title="come from Sales Contact Page">Sales Contract</th>
						<th width="100" title="come from budget">Production Per Hour</th>
						<th width="100" title="Fixed 10">Working Hour</th>
						<th width="100" title="Production Per Hour*Working Hour">Production Per Day</th>
						<th width="100" title="come from capacity calculation">Monthly W/Day</th>
						<th width="100" title="total qnty/Production Per Day">Required Day</th>
						<th width="100" title="Required Day/Monthly W/Day">Number Of Line</th>
						<th width="100" title="come from budget">Machine Per Line</th>
						<th width="100">Machine Required</th>
						<th width="100">Daily Machine Required</th>
						<th width="100">Machine  Cost</th>
						<th width="100">Cost Per PC</th>
	                    <th>Merchant CM Per PC</th>
	                </tr> 
	            </thead>
	        </table>
	                <div style=" max-height:400px; overflow-y:scroll; width:2410px"  align="left" id="scroll_body">
	                    <table width="2390" border="1" class="rpt_table" rules="all" id="table_body">
	                    <?
 
	                    $i=1;
	                    $order_qnty_pcs_tot=0;
	                    $order_qntytot=0;
	                    $oreder_value_tot=0;
	                    $total_ex_factory_qnty=0;
	                    $total_short_access_qnty=0;
	                    $total_short_access_value=$total_ex_fact_value=0;
	                    $yarn_req_for_po_total=0;

				foreach ($main_data_arr as $month_id => $buyer_data){
				  foreach ($buyer_data as $buyer_id  => $po_data){
					foreach ($po_data as  $po_id => $item_data){
					  foreach ($item_data as $item_id => $color_data){
						foreach ($color_data as $color_id => $row){

							 
							$costing_pre_mstID=$costing_pre_mstID_arr[$row[csf('job_no')]];
							//echo $costing_pre_mstID.'system';
							if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";

				 

							 
							$cons=0;
							$costing_per_pcs=0;							
	                        $costing_per=$costing_per_arr[$row['job_no']];
							if($costing_per ==1) $costing_per_pcs=1*12;
							else if($costing_per==2) $costing_per_pcs=1*1;
							else if($costing_per==3) $costing_per_pcs=2*12;
							else if($costing_per==4) $costing_per_pcs=3*12;
							else if($costing_per==5) $costing_per_pcs=4*12;

							$matarial_cost_with_cm=$row['order_qty_pcs']*($job_wise_data[$row['job_no']]['total_cost']/$costing_per_pcs);
							$cm_cost=$row['order_qty_pcs']*($job_wise_data[$row['job_no']]['cm_cost']/$costing_per_pcs);
							$matarial_cost=$matarial_cost_with_cm-$cm_cost;
							$fob_value=$row['order_qty_pcs']*$row['unit_price'];

							$contract_no=$po_item_wise_data[$po_id][$item_id]['contract_no'];
							$production_per_day=$row['order_qty_pcs']/($job_wise_data[$row['job_no']]['prod_line_hr']*10);
							$req_day=$row['order_qty_pcs']/$production_per_day;
							$number_of_line=$req_day/$month_wise_data[$month_id]['monthly_day'];
							$mc_req=$job_wise_data[$row['job_no']]['machine_line']*$month_wise_data[$month_id]['monthly_day']*$number_of_line;
							$daily_mc_req=$mc_req/$month_wise_data[$month_id]['monthly_day'];
							$merchant_cm_per_pcs=$cm_cost/$row['order_qty_pcs'];

							?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >
	                           
							<td width="50" align="center" bgcolor="<? echo $color; ?>" style="word-wrap: break-word;word-break: break-all;" title="<?=$month_id;?>"> <? echo $i; ?> </td>
	                           
	                            <td  width="50" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_short_name_arr[$buyer_id];?></td>

	                            <td  width="150" align="center" style="word-wrap: break-word;word-break: break-all;"><font style="color:<? echo $color_font; ?>">
	                            
								<? echo $row['po_number'];  ?>
	                            </td>
								
	                            <td width="90" align="center" style="word-wrap: break-word;word-break: break-all;"></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$color_lib_arr[$color_id]?></td>
	                           
	                            <td width="90" align="right"><? echo fn_number_format($row['order_qty_pcs'],0); ?> </td>
	                          
	                            <td  width="50" align="right" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($row['unit_price'],4);?></td>
	                            <td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" >
	                            <? echo number_format($fob_value,2); ?>
	                            </td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=number_format($matarial_cost-$cm_cost,4);?></td>
	                           
	                            <td width="80" align="center"><?=number_format($cm_cost,4);?></td>
	                            <td width="90" align="right" style="word-wrap: break-word;word-break: break-all;"><?=number_format(($matarial_cost/$fob_value)*100,2);?></td>
	                            <td width="90" align="right"><? echo change_date_format($row['shipment_date']);?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$contract_no;?></td>

								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$job_wise_data[$row['job_no']]['prod_line_hr'];?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;">10</td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$job_wise_data[$row['job_no']]['prod_line_hr']*10; ?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$month_wise_data[$month_id]['monthly_day'];?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=number_format($req_day,4); ?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=fn_number_format($number_of_line,0); ?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=$job_wise_data[$row['job_no']]['machine_line'];?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=fn_number_format($mc_req,0);?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=fn_number_format($daily_mc_req,0); ?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=fn_number_format($mc_req*40,0); ?></td>
								<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><?=fn_number_format(($mc_req*40)/$row['order_qty_pcs'],0); ?></td>
	                            <td style="word-wrap: break-word;word-break: break-all;"><?=fn_number_format($merchant_cm_per_pcs,0); ?></td>
	                        </tr>
	                  			  <?
								 
							$i++;
				//===========================================Monthly Total=============================================
							$monthly_total[$month_id]['order_qty_pcs']+=$row['order_qty_pcs'];
							$monthly_total[$month_id]['fob_value']+=$fob_value;
							$monthly_total[$month_id]['material_without_cm']+=$matarial_cost-$cm_cost;
							$monthly_total[$month_id]['cm_cost']+=$cm_cost;
							$monthly_total[$month_id]['prod_line_hr']+=$job_wise_data[$row['job_no']]['prod_line_hr'];
							$monthly_total[$month_id]['prod_per_day']+=$job_wise_data[$row['job_no']]['prod_line_hr']*10;
							$monthly_total[$month_id]['req_day']+=$req_day;
							$monthly_total[$month_id]['mc_req']+=$mc_req;
							$monthly_total[$month_id]['daily_mc_req']+=$daily_mc_req;
							$monthly_total[$month_id]['machine_cost']+=$mc_req*40;
							$monthly_total[$month_id]['cost_per_pcs']+=($mc_req*40)/$row['order_qty_pcs'];
							$monthly_total[$month_id]['merchant_cm_per_pcs']+=$merchant_cm_per_pcs;

				//===========================================Grand Total=============================================
							$grand_total['order_qty_pcs']+=$row['order_qty_pcs'];
							$grand_total['fob_value']+=$fob_value;
							$grand_total['material_without_cm']+=$matarial_cost-$cm_cost;
							$grand_total['cm_cost']+=$cm_cost;
							$grand_total['prod_line_hr']+=$job_wise_data[$row['job_no']]['prod_line_hr'];
							$grand_total['prod_per_day']+=$job_wise_data[$row['job_no']]['prod_line_hr']*10;
							$grand_total['req_day']+=$req_day;
							$grand_total['mc_req']+=$mc_req;
							$grand_total['daily_mc_req']+=$daily_mc_req;
							$grand_total['machine_cost']+=$mc_req*40;
							$grand_total['cost_per_pcs']+=($mc_req*40)/$row['order_qty_pcs'];
							$grand_total['merchant_cm_per_pcs']+=$merchant_cm_per_pcs;

							$buyer_wise_summary[$buyer_id][$month_id]['order_qty_pcs']+=$row['order_qty_pcs'];
							$buyer_wise_summary[$buyer_id][$month_id]['fob_value']+=$fob_value;
							$buyer_wise_summary[$buyer_id][$month_id]['material_without_cm']+=$matarial_cost-$cm_cost;
							$buyer_wise_summary[$buyer_id][$month_id]['cm_cost']+=$cm_cost;
							 
							
							
						}
					}
				}
			}
			?>
								<tr bgcolor="skyblue" style="vertical-align:middle" height="25"  >                           
								   <th width="100" align="right" colspan="5"><b>Total <?=$months[$month_id];?>:</b></th>								  
								   <th width="90" align="center"><? echo fn_number_format($monthly_total[$month_id]['order_qty_pcs'],0); ?> </th>
								   <th  width="50" align="right"></td>
								   <th width="100" align="center"><? echo number_format($monthly_total[$month_id]['fob_value'],2); ?></th>
								   <th width="100" align="center"><?=number_format($monthly_total[$month_id]['material_without_cm'],4);?></th>
								   <th width="80" align="center"><?=number_format($monthly_total[$month_id]['cm_cost'],4);?></th>
								   <th width="90" align="right"></td>
								   <th width="90" align="right"></td>
								   <th width="100" align="center"></td>
								   <th width="100" align="center"><?=$monthly_total[$month_id]['prod_line_hr'];?></th>
								   <th width="100" align="center"></td>
								   <th width="100" align="center"><?=$monthly_total[$month_id]['prod_per_day']; ?></th>
								   <th width="100" align="center"></td>
								   <th width="100" align="center"><?=number_format($monthly_total[$month_id]['req_day'],4); ?></th>
								   <th width="100" align="center"></td>
								   <th width="100" align="center"></td>
								   <th width="100" align="center"><?=fn_number_format($monthly_total[$month_id]['mc_req'],0);?></th>
								   <th width="100" align="center"><?=fn_number_format($monthly_total[$month_id]['daily_mc_req'],0); ?></th>
								   <th width="100" align="center"><?=fn_number_format($monthly_total[$month_id]['machine_cost'],0); ?></th>
								   <th width="100" align="center"><?=fn_number_format($monthly_total[$month_id]['cost_per_pcs'],0); ?></th>
								   <th align="center"><?=fn_number_format($monthly_total[$month_id]['merchant_cm_per_pcs'],0); ?></th>
							   </tr>

			<?
		}
						unset($data_array);
	                    ?>
						<tfoot>
						<tr  style="vertical-align:middle" height="25"  >
	                           
							   
								   <td width="100" align="center" colspan="5"><b>Grand Total:</b></td>								  
								   <td width="90" align="center"><b><? echo fn_number_format($grand_total['order_qty_pcs'],0); ?> </b></td>
								   <td  width="50" align="right"></td>
								   <td width="100" align="center"><b><? echo number_format($grand_total['fob_value'],2); ?></b></td>
								   <td width="100" align="center"><b><?=number_format($grand_total['material_without_cm'],4);?></b></td>
								   <td width="80" align="center"><b><?=number_format($grand_total['cm_cost'],4);?></b></td>
								   <td width="90" align="right"></td>
								   <td width="90" align="right"></td>
								   <td width="100" align="center"></td>
								   <td width="100" align="center"><b><?=$grand_total['prod_line_hr'];?></b></td>
								   <td width="100" align="center"></td>
								   <td width="100" align="center"><b><?=$grand_total['prod_per_day']; ?></b></td>
								   <td width="100" align="center"></td>
								   <td width="100" align="center"><b><?=number_format($grand_total['req_day'],4); ?></b></td>
								   <td width="100" align="center"></td>
								   <td width="100" align="center"></td>
								   <td width="100" align="center"><b><?=fn_number_format($grand_total['mc_req'],0);?><b></td>
								   <td width="100" align="center"><b><?=fn_number_format($grand_total['daily_mc_req'],0); ?></b></td>
								   <td width="100" align="center"><b><?=fn_number_format($grand_total['machine_cost'],0); ?></b></td>
								   <td width="100" align="center"><b><?=fn_number_format($grand_total['cost_per_pcs'],0); ?></b></td>
								   <td align="center"><b><?=fn_number_format($grand_total['merchant_cm_per_pcs'],0); ?></b></td>
							   </tr>

	                    </tfoot>
	                    </table>
	                </div>
	              
	               
	            </div>
	     </div>
					<br>
		 <div id="content_report_panel">
	        <table width="700" id="table_header_1" border="1" class="rpt_table" rules="all">
				<caption><h3>Buyer Wise Summary</h3></caption>
	            <thead>
	                <tr>
	                    
	                    <th width="100">Buyer</th>                  
						<th width="100">Month</th>
						<th width="100">Order Qnty(Pcs)</th>
	                    <th width="100">FOB Value</th>	     
						<th width="100">Meterial Bill</th>              
	                    <th width="100">CM</th>
	                    <th width="100">Meterial Bill %</th>
	                </tr> 
	            </thead>
	        </table>
	                <div style=" max-height:400px; overflow-y:scroll; width:710px"  align="left" id="scroll_body">
	                    <table width="700" border="1" class="rpt_table" rules="all" id="table_body">

						<?php
						foreach($buyer_wise_summary as $buyerId=>$buyerData){
							foreach($buyerData as $monthId=>$val){
							
								?>
								<tr  style="vertical-align:middle" height="25"  >
									<td width="100" align="center"><?=$buyer_short_name_arr[$buyerId];?></td>								  
									<td width="100" align="center"><?=$months[$monthId]; ?></td>
									<td width="100" align="right"><?=number_format($val['order_qty_pcs'],2); ?></td>
									<td width="100" align="center"><?=number_format($val['fob_value'],2); ?></td>
									<td width="100" align="center"><?=number_format($val['material_without_cm'],4);?></td>
									<td width="100" align="center"><?=number_format($val['cm_cost'],4);?></td>
									<td width="100" align="right"><?=number_format(($val['material_without_cm']/$val['fob_value'])*100,2);?></td>
								</tr>

							   <?
							  $tot_order_qty_pcs+=$val['order_qty_pcs'];
							  $tot_fob_value+=$val['fob_value'];
							  $tot_material_without_cm+=$val['material_without_cm'];
							  $tot_cm_cost+=$val['cm_cost'];
						    }
						
						}
						?>
							    <tr  style="vertical-align:middle" height="25"  >
									<td width="100" align="center" colspan="2"><b>Total:</b></td>								  
									<td width="100" align="right"><b><?=number_format($tot_order_qty_pcs,2); ?></b></td>
									<td width="100" align="center"><b><?=number_format($tot_fob_value,2); ?></b></td>
									<td width="100" align="center"><b><?=number_format($tot_material_without_cm,4);?></b></td>
									<td width="100" align="center"><b><?=number_format($tot_cm_cost,4);?></b></td>
									<td width="100" align="right"><b></b></td>
								</tr>
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
	echo "****$filename****6";
	ob_end_flush();
	exit();
}
if($type=="report_generate_summaery")
{
	$data=explode("_",$data);
	$cbo_company_name=$data[0];
	$cbo_style_owner=$data[14];
	$cbo_location_id=$data[15];
	$txt_job_no=trim($data[16]);
	$txt_job_id=trim($data[17]);
	$company_id==0;
	$company_cond=0;
	$company_field= "a.company_name as company_name" ;
	$company_field_caption= "Company Name" ;
	$company_group_by= "a.company_name" ;




	/**********************************************liabrary*************************************/
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0 order by sequence_no",'id','short_name');






	if($cbo_company_name ==0 &&  $cbo_style_owner == 0){
		echo "Please Select Company Name or Style Owner";
		die;
	}

	if($txt_job_id!=''){$job_con=" and a.id=$txt_job_id";}
	else if($txt_job_no!=''){$job_con=" and a.job_no like('%$txt_job_no%')";}
	$txt_internal_ref=str_replace("'", "", $txt_internal_ref);
	$internal_ref_cond='';
	if(!empty($txt_internal_ref))
	{
		$internal_ref_cond=" and b.grouping='".$txt_internal_ref."'";
	}
	
	if($cbo_company_name >0 &&  $cbo_style_owner > 0){
		$company_id=$cbo_company_name;
		$company_cond= "and a.company_name=$cbo_company_name";
		$company_field= "a.company_name as company_name" ;
		$company_group_by= "a.company_name" ;
		$company_field_caption= "Company Name" ;
	}
	if($cbo_company_name ==0 &&  $cbo_style_owner > 0){
		$company_id=$cbo_style_owner;
		$company_cond= "and a.style_owner=$cbo_style_owner";
		$company_field= "a.style_owner as company_name" ;
		$company_group_by= "a.style_owner" ;
		$company_field_caption= "Style Owner" ;
	}
	if($cbo_company_name >0 &&  $cbo_style_owner == 0){
		$company_id=$cbo_company_name;
		$company_cond= "and a.company_name=$cbo_company_name";
		$company_field= "a.company_name as company_name" ;
		$company_group_by= "a.company_name" ;
		$company_field_caption= "Company Name" ;
	}

	if($company_id==0) $company_name="%%"; else $company_name=$company_id;

	if($data[1]!=''){$buyer_name_con=" and a.buyer_name in(".$data[1].")";}
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	if(trim($data[4])=="0") $team_leader="%%"; else $team_leader="$data[4]";
	if(trim($data[5])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[5]";
	if(trim($data[11])=="0") $factory_marchant="%%"; else $factory_marchant="$data[11]";
	if(trim($data[12])=="0") $agent_con=""; else $agent_con=" and a.agent_name=$data[12]";

	if(trim($data[13])=="0") $product_category_con=""; else $product_category_con=" and a.product_category=$data[13]";
	if(trim($data[11])>0){$factory_marchant_con=" and a.factory_marchant=".$data[11].""; }
	
	
	
	if($cbo_location_id>0) echo "<font style='color:#F00; font-size:14px; font-weight:bold'>Location is not considerable for this report.</font>";

	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'','-',1);
		$end_date=change_date_format($end_date,'','-',1);
    }



	$cbo_category_by=$data[6];
	$zero_value=$data[7];
	$cbo_capacity_source=$data[8];
	$cbo_year=$data[9];

	if(trim($data[10])!="") $style_ref_cond="%".trim($data[10])."%"; else $style_ref_cond="%%";

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond_2=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond_2=" $year_field_con=$cbo_year"; else $year_cond="";
	}

	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond="and c.country_ship_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.pub_shipment_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else if($cbo_category_by==3)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else
	{
		if ($start_date!="" && $end_date!="")
		{
			if($db_type==0){
			$date_cond=" and date(b.insert_date) between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
			}
			else if($db_type==2){
				$date_cond=" and TRUNC(b.insert_date) between '$start_date' and  '$end_date'";
				$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
			}
		}
		else
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	//echo $date_cond.'=='.$cbo_category_by;

	if($cbo_capacity_source==1){
		$capacity_source_cond="and c.capacity_source=1";
	}
	else if($cbo_capacity_source==3){
		$capacity_source_cond="and c.capacity_source=3";
	}
	else{
		$capacity_source_cond="";
	}

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
    $target_basic_qnty=array();
	$total_target_basic_qnty=0;
    $sm = date('m',strtotime($start_date));
	$em = date('m',strtotime($end_date));

	if($cbo_location_id!=0){
		$locationCon_c=" and c.location_id =$cbo_location_id";
		$locationCon_a=" and a.location_id =$cbo_location_id";
	}
	
 


	$po_total_price_tot=0;
	$quantity_tot=0;
	$exfactory_tot=0;


	$po_total_price_tot_c=0;
	$quantity_tot_c=0;

	$po_total_price_tot_p=0;
	$quantity_tot_p=0;

	$booked_basic_qnty_tot_c=0;
	$booked_basic_qnty_tot_p=0;
		
	
	 
		$date=date('d-m-Y');
		if($cbo_category_by==1)
		{ 
			// sum(c.order_quantity/a.total_set_qnty) as po_quantity

			$data_array=sql_select("select a.SET_BREAK_DOWN,a.job_no_prefix_num, a.job_no, $company_field, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.factory_marchant, a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, sum(c.order_total) as order_total, b.pub_shipment_date, b.po_received_date,min(TRUNC(b.insert_date)) as insert_date, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.delay_for, b.grouping, b.file_no, c.shiping_status, c.country_remarks, c.country_ship_date, c.country_id, b.t_year, b.t_month,a.season_buyer_wise,a.pro_sub_dep,a.product_dept, a.ship_mode,b.shipment_date
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c , lib_buyer d 
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.buyer_name=d.id and d.status_active=1 $company_cond $buyer_name_con and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $factory_marchant_con $agent_con $date_cond $year_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $product_category_con $job_con $internal_ref_cond
			group by a.SET_BREAK_DOWN,a.job_no_prefix_num, a.job_no, $company_group_by, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.factory_marchant, a.product_code, a.ship_mode, b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.delay_for, b.grouping, b.file_no, b.t_year, b.t_month, c.country_id, c.country_remarks, c.country_ship_date, c.shiping_status,a.season_buyer_wise,a.pro_sub_dep,a.product_dept,b.shipment_date order by a.buyer_name,c.country_ship_date, a.job_no_prefix_num, b.id");


		}
		else
		{
			$data_array=sql_select("select a.SET_BREAK_DOWN,a.job_no_prefix_num, a.job_no, $company_field, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(b.po_quantity) as po_quantity, sum(b.po_quantity*a.total_set_qnty) as po_quantity_pcs,b.shiping_status, b.pub_shipment_date, b.po_received_date, min(TRUNC(b.insert_date)) as insert_date,(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price as order_total, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.shiping_status as shiping_status2, b.t_year, b.t_month,a.season_buyer_wise,a.pro_sub_dep,a.product_dept, a.ship_mode,b.shipment_date
			from wo_po_details_master a, wo_po_break_down b, lib_buyer d 
			where a.job_no=b.job_no_mst and a.buyer_name=d.id and d.status_active=1 $company_cond $buyer_name_con and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $factory_marchant_con $agent_con $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $product_category_con $job_con $internal_ref_cond
			group by a.SET_BREAK_DOWN,a.job_no_prefix_num, a.job_no, $company_group_by, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code, a.ship_mode,b.id,b.is_confirmed,b.po_number,b.shiping_status,b.pub_shipment_date,b.po_received_date,b.unit_price,b.po_total_price,b.details_remarks, b.delay_for,b.grouping,b.file_no,b.t_year, b.t_month,a.season_buyer_wise,a.pro_sub_dep,a.product_dept,b.shipment_date order by a.buyer_name,b.pub_shipment_date,a.job_no_prefix_num,b.id");
			//$data_array=sql_select($sql); //a.job_no_prefix_num,b.id country_id
		
		
		}
	



		

	

	//========================================================================================
	$po_array_for_cond=array();
	$job_array_for_cond=array();
	foreach ($data_array as $row_po_job){
	 $po_array_for_cond[$row_po_job[csf("id")]]=$row_po_job[csf("id")];
	 $job_array_for_cond[$row_po_job[csf("job_no")]]="'".$row_po_job[csf("job_no")]."'";
	}
	//echo '<pre>';print_r($po_array_for_cond);

	if($cbo_category_by==1)
	{
		$job_arr_cond=array_chunk($job_array_for_cond,1000, true);
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

	}
	else $job_cond_for_in='';




	$job_arr_cond=array();
	$job_array_for_cond=array();
	 
	//$costing_per_arr=return_library_array( "select job_no,costing_per from  wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','costing_per');
	$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
	$sql_pre=sql_select("select id,job_no,costing_per from  wo_pre_cost_mst where 1=1 $job_cond_for_in");
	foreach($sql_pre as $job)
	{
		$costing_per_arr[$job[csf('job_no')]]=$job[csf('costing_per')];
		$costing_pre_mstID_arr[$job[csf('job_no')]]=$job[csf('id')];
	}

	$pre_cost_dtls_data=sql_select( "select job_no,total_cost,cm_cost from  wo_pre_cost_dtls where status_active=1 $job_cond_for_in");

	foreach($pre_cost_dtls_data as $row)
	{
		$job_wise_data[$row[csf('job_no')]]['total_cost']=$row[csf('total_cost')];
		$job_wise_data[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_cost')];
		// $costing_pre_mstID_arr[$row[csf('job_no')]]=$row[csf('id')];
	}

	//echo '<pre>';print_r($costing_pre_mstID_arr);
	//echo $job_cond_for_in;die;



	

	
	//===========================================================================================

	$exfactory_tot_yet=0;
	$exfactory_tot_over=0;

	foreach ($data_array as $row){
		$y = date('Y',strtotime($row[csf("shipment_date")]));
		$po_total_price_tot+=$row[csf("order_total")];
		$quantity_tot+=$row[csf("po_quantity_pcs")];

		$month = date('M',strtotime($row[csf("shipment_date")]));
		$month_buyer_wise_data[$month][$row[csf("buyer_name")]]['order_qnty']+=$row[csf("po_quantity_pcs")];
		$month_buyer_wise_data[$month][$row[csf("buyer_name")]]['order_value']+=$row[csf("order_total")];
		// $month_buyer_wise_data[$month][$row[csf("buyer_name")]]['mer_cm']+=$job_wise_data[$row[csf('job_no')]]['cm_cost']*$row[csf("po_quantity_pcs")];

		$m = date('m',strtotime($row[csf("shipment_date")]));

		 
		$monthsId[$m]=(int)$m;
		$years[$y]=(int)$y;
						$costing_per_pcs=0;							
	                        $costing_per=$costing_per_arr[$row[csf('job_no')]];
							if($costing_per ==1) $costing_per_pcs=1*12;
							else if($costing_per==2) $costing_per_pcs=1*1;
							else if($costing_per==3) $costing_per_pcs=2*12;
							else if($costing_per==4) $costing_per_pcs=3*12;
							else if($costing_per==5) $costing_per_pcs=4*12;

			$cm_cost=$row[csf("po_quantity_pcs")]*($job_wise_data[$row[csf('job_no')]]['cm_cost']/$costing_per_pcs);
			$merchant_cm_per_pcs=$cm_cost/$row[csf("po_quantity_pcs")];
			$month_buyer_wise_data[$month][$row[csf("buyer_name")]]['mer_cm']+=$merchant_cm_per_pcs;
		
	}
			
		$monthsIds=implode(",",$monthsId); 
		$years=implode(",",$years);
		
		$capacity_data=sql_select("SELECT  b.month_id , sum(b.capacity_min) capacity_min, sum(b.capacity_pcs) capacity_pcs,count(b.id) as monthly_day
		from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.day_status=1 and a.location_id=0 and a.year in ($years)	 and b.month_id in ($monthsIds) and a.capacity_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		 group by b.month_id");
		  

		 foreach($capacity_data as $row){

				$month_wise_data[$row[csf("month_id")]]['monthly_day']=$row[csf("monthly_day")];

		 }


	ob_start();
	?>
	<div>
	<table width="100%">
	<tr>
	<td align="center" id="projected_and_confirmed_order">
	<table width="1420" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" >
	
	<tbody>
	
	
	
	</table>

	<table width="100%">
	<tr>
	<td width="70%" id="confirmed_order">
	<table width="760" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
	
	
	<?

	$chart_cap="";
	$chart_data="";
	$i=0;
	$total_avg_sam_confrim=0;
	 

		
		foreach ($month_buyer_wise_data as $key=>$buyerData){ $m=1;?>
						<thead>
							<tr>
								<th width="60">Month</th>
								<th width="70">Buyer Name</th>
								<th width="100">Ord. Qty. (Pcs)</th>
								<th width="100">FOB Value</th>         
								<th width="60">Daily Mc Book</th>
								<th width="60">Mer CM</th>
								
							</tr>
						</thead>
					<? 
			  foreach ($buyerData as $buyerId=>$value){					
						$i++;
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $i; ?>">
							<?
							if($m==1){?>
							<td align="center" rowspan="<?=count($month_buyer_wise_data[$key]);?>"><?=$key; ?></td>
							<?}?>
							<td align="center"><?=$buyer_short_name_arr[$buyerId];?></td>
							<td align="right"><?=number_format($value['order_qnty'],0);?></td>
							<td align="right"><?=number_format($value['order_value'],2);?></td>        
							<td align="right"><?=number_format($value['mer_cm'],2);?></td>
							<td align="right"><?=number_format($value['mer_cm'],2);?></td>
						</tr>
					<?
					$total_order_qnty+=$value['order_qnty'];
					$total_order_value+=$value['order_value'];
					$total_mer_cm+=$value['mer_cm'];
			$m++;  }
				  ?>
					<tr>
						<td width="60" align="right" colspan="2"><b>Total</b></td>
						<td width="100" align="right"><b><?=number_format($total_order_qnty,2);;?> </b></td>
						<td width="100" align="right"><b><?=number_format($total_order_value,2);?></b> </td>         
						<td width="60" align="right"><b><?=number_format($total_mer_cm,2);?></b></td>
						<td width="60" align="right"> <b><?=number_format($total_mer_cm,2);?></b></td>
					</tr>
			 
	<?}?>
	 
	</table>
	</td>
	
	</tr>
	</table>


	<table width="100%">
	<tr>
	
	<td width="30%" valign="top">
	
	

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
	echo "****$filename****6";
	ob_end_flush();
	exit();
}
 
 



if($action=="yarn_req")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_full_name_arr=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0 order by sequence_no",'id','buyer_name');
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:850px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:845px; margin-left:10px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="8"><b>Required Qnty Info</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="120">Order No.</th>
                    <th width="120">Buyer Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="110">Order Qnty</th>
                    <th width="110">Plan Cut Qnty</th>
                    <th width="110">Required Qnty</th>
                    <th>Shipment Date</th>
                </thead>
             </table>
             <div style="width:830px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                    <?
					$costing_per_id_library=array(); $costing_date_library=array();
					$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
					foreach($costing_sql as $row)
					{
						$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')];
						$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
					}



				    $i=1; $tot_req_qnty=0;
					$sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.avg_cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id) and c.count_id='$yarn_count' and c.copm_one_id='$yarn_comp_type1st' and c.percent_one='$yarn_comp_percent1st' and c.copm_two_id='$yarn_comp_type2nd' and c.percent_two='$yarn_comp_percent2nd' and c.type_id='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut";//sum(c.cons_qnty)
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

						$dzn_qnty=0; $required_qnty=0; $order_qnty=0;
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$order_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						$required_qnty=$plan_cut_qnty*($row[csf('qnty')]/$dzn_qnty);
                        $tot_req_qnty+=$required_qnty;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_full_name_arr[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty,2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th align="right" colspan="6">Total</th>
                        <th align="right"><? echo number_format($tot_req_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
<?
exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if($row[csf('knit_dye_source')]==1)
					{
						$issue_to=$company_library[$row[csf('knit_dye_company')]];
					}
					else if($row['knit_dye_source']==3)
					{
						$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					}
					else
						$issue_to="&nbsp;";

                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<?
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<?
								if($row[csf('knit_dye_source')]==3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if($row[csf('knitting_source')]==1)
					{
						$return_from=$company_library[$row[csf('knitting_company')]];
					}
					else if($row['knitting_source']==3)
					{
						$return_from=$supplier_details[$row[csf('knitting_company')]];
					}
					else
						$return_from="&nbsp;";

                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<?
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<?
								if($row[csf('knitting_source')]==3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tfoot>
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
<?
exit();
}

 

?>