<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/cm_gmt_class.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");

$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
$color_Arr_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
$production_process=array(1=>'Cutting',2=>'Sewing Input',3=>'Sewing Output',4=>'Poly Output',5=>'Packing & Finishing',6=>'Ex-factory');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id  in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/factory_monthly_production_report_controller_chaity', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/factory_monthly_production_report_controller_chaity' );",0 );     	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}

if ($action=="load_drop_down_floor_group")
{ 
	$data_ex = explode("_", $data);
	$company_id = $data_ex[0];
	$location_id = $data_ex[1];
	echo create_drop_down( "cbo_floor_group_name", 130, "SELECT a.group_name from lib_prod_floor a where a.status_active=1 and a.is_deleted=0 and company_id in($company_id) and location_id in($location_id) and a.group_name is not null group by a.group_name  order by a.group_name","group_name,group_name", 1, "Select Floor Group", $selected, "",0,"","" ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{ 
	$data_ex = explode("_", $data);
	// $group_name = $data_ex[0];
	$company_id = $data_ex[0];
	$location_id = $data_ex[1];

	echo create_drop_down( "cbo_floor", 130, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($company_id) and location_id in($location_id) and production_process in (1,5,11) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();    	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}

if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//txt_job_no
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'factory_monthly_production_report_controller_chaity', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//if($job!='') $job_cond="and a.job_no_prefix_num='$job'";else $job_cond="";
	$search_by=$data[2];
	$search_value=$data[3];
	//$search_string="%".trim($data[3])."%";
	//if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	
	if($search_by==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_by==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
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
	  $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and a.company_name=$company_id $search_con $buyer_id_cond $year_cond   order by a.job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No", "120,130,80,50,120","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_working_company_id=str_replace("'","",$cbo_working_company_id);
	if(str_replace("'","",$cbo_working_company_id)==0)
	{
		$working_company="";
		$working_company7="";
	}
	else
	{
		$working_company=" and c.serving_company in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company_subcon=" and a.company_id in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company7=" and c.working_company_id in(".str_replace("'","",$cbo_working_company_id).")";
	}
	 
	if(str_replace("'","",$txt_job_no)=="")
	{ 
		$job_cond=""; 
		$job_cond_subcon=""; 
	}
	else
	{
		$job_cond="and a.job_no_prefix_num=".trim($txt_job_no)."";
		$job_cond_subcon="and b.job_no_prefix_num=".trim($txt_job_no)."";
	} 
	if (str_replace("'","",$txt_style_ref)=="") 
	{
		$style_cond="";
	}
	else
	{
		$style_cond="and a.style_ref_no=".trim($txt_style_ref)."";
	} 
	if (str_replace("'","",$txt_po_no)=="") 
	{
		$order_cond="";
		$order_cond_subcon=""; 

	}
	else
	{
		$order_cond="and b.po_number=".trim($txt_po_no).""; 
		$order_cond_subcon=" and c.order_no=".trim($txt_po_no).""; 
	} 

	$group_name = (str_replace("'", "", $cbo_floor_group_name)==0) ? str_replace("'", "", $cbo_floor_group_name) : $cbo_floor_group_name;
	// echo gettype($cbo_floor_group_name);
	// $group_name = $cbo_floor_group_name;
	if($group_name !== '0' && str_replace("'","",$cbo_floor) =="")
	{
		if(str_replace("'","",$cbo_location)!=0 || str_replace("'","",$cbo_location)!="")
		{
			$locationId = str_replace("'","",$cbo_location);
			$locationId_cond = " and a.location_id in($locationId) ";
		}
		$group_cond="";
		$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.company_id in($cbo_working_company_id) $locationId_cond and a.status_active=1 and a.group_name=$cbo_floor_group_name order by a.id");
		foreach ($group_sql as $value) 
		{
			if($group_cond=="")
			{
				$group_cond = $value[csf('id')];
			}
			else
			{
				$group_cond .= ",".$value[csf('id')];
			}
		}
		$floor_cond=" and c.floor_id in($group_cond)";
		$floor_cond_subcon=" and a.floor_id in($group_cond)";
	}
	else if(str_replace("'","",$cbo_floor) !="" && $group_name != '0')
	{
		$floor_cond=" and c.floor_id in(".str_replace("'","",$cbo_floor).")";
		$floor_cond_subcon=" and a.floor_id in(".str_replace("'","",$cbo_floor).")";
	}
	else if(str_replace("'","",$cbo_floor) !="" && $group_name == '0')
	{
		$floor_cond=" and c.floor_id in(".str_replace("'","",$cbo_floor).")";
		$floor_cond_subcon=" and a.floor_id in(".str_replace("'","",$cbo_floor).")";
	}
	else
	{
		$floor_cond=""; 
		$floor_cond_subcon="";
	}
	// echo $floor_cond;

	/*if(str_replace("'","",$cbo_floor)==0)
	{
		$floor_cond=""; 
		$floor_cond_subcon="";

	}
	else 
	{
		$floor_cond=" and c.floor_id in(".str_replace("'","",$cbo_floor).")";
		$floor_cond_subcon=" and a.floor_id in(".str_replace("'","",$cbo_floor).")";
	}*/

	if(str_replace("'","",$cbo_location)==0 || str_replace("'","",$cbo_location)=="")
	{
		$location_cond=""; 
		$location_cond_subcon="";

	}
	else 
	{
		$cbo_location = str_replace("'","",$cbo_location);
		$location_cond_subcon = str_replace("'","",$cbo_location);

		$location_cond=" and c.location in($cbo_location)";
		$location_cond_subcon=" and a.location_id in($cbo_location)";
	}

	$process_rpt_type=str_replace("'","",$cbo_production_process);
	$cbo_year=str_replace("'","",$cbo_year);
	$report_type=str_replace("'","",$type);
	$working_company_id=str_replace("'","",$cbo_working_company_id);
	$year_cond="";
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
		
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
		$buyer_id_cond_subcon=" and b.party_id=$cbo_buyer_name";
	}
		
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($end_date,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($txt_date_from,"","",1);
			$end_date=change_date_format($txt_date_to,"","",1);
		}
		$prod_date_cond=" and c.production_date between '$start_date' and '$end_date'";
		$ex_factory_date_cond=" and a.ex_factory_date between '$start_date' and '$end_date'";
		$prod_to_date_comd="'$start_date'";
		$prod_date_cond_subcon=" and a.production_date between '$start_date' and '$end_date'";
		$prod_date_cond2=" and d.ex_factory_date between '$start_date' and '$end_date'";
		$prod_date_cond2_subcon=" and a.delivery_date between '$start_date' and '$end_date'";
		$prod_date_cond7=" and c.entry_date between '$start_date' and '$end_date'";
	}
		//$month=date("m",strtotime($start_date))-1;
		//echo date('Y-m', strtotime('-1 month', time()));
		$start_date=date("d-m-Y",strtotime($start_date));
		$start_date_tmp=date("Y-m",strtotime($start_date));
		$mon_data=explode("-",$start_date);
		$mon_id=$mon_data[1];
		$year_id=$mon_data[2];
		//echo $mon_id.'='.$year_id;
		$num_days = cal_days_in_month(CAL_GREGORIAN, $mon_id, $year_id);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		} //echo $year_cond.'assss';

		$company_names="";
		foreach(explode(",",$working_company_id) as $key=>$vals)
		{
			if($company_names=="")
			{
				$company_names=$company_library[$vals];
			}
			else
			{
				$company_names .=' , '.$company_library[$vals];
			}
		}
	$client_array = array();
	$sql_client=sql_select("SELECT a.id, a.buyer_name
	FROM lib_buyer a, lib_buyer_tag_company b
		WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.buyer_id = a.id AND a.id IN 
		(SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (7)) 
		group by a.id, a.buyer_name
	ORDER BY buyer_name");

	foreach ($sql_client as $key => $value) {
		$client_array[$value[csf('id')]] = $value[csf('buyer_name')];
	}
	// echo "<pre>";
	// print_r($client_array);
			
	if($report_type==1)
	{
		if($process_rpt_type==1) //Cutting
		{
			$prod_type=1;
			$subcon_prod_type=1;
			$th_caption="Cutting QC Qty";
		}
		else if($process_rpt_type==2) //sewing Input
		{
			$prod_type=4;
			$subcon_prod_type=7;
			$th_caption="Input Qty";
		}
		else if($process_rpt_type==3) //sewing Out
		{
			$prod_type=5;
			$subcon_prod_type=2;
			$th_caption="Sew Output";
			
		}
		else if($process_rpt_type==4) //Poly
		{
			$prod_type=11;
			$subcon_prod_type=5;
			$th_caption="Poly Output Qty";
		}
		else if($process_rpt_type==5) //Finished
		{
			$prod_type=8;
			$subcon_prod_type=4;
			$th_caption="Finishing Qty";
		}
		else if($process_rpt_type==6) //Ship Qty/ Ex-F
		{
			$prod_type=0;
			$th_caption="Ship Qty";
		}
		else if($process_rpt_type==7) //Cut and Lay
		{
			$prod_type=0;
			$th_caption="Cut and Lay Qty";
		}
 
		if($process_rpt_type==1 || $process_rpt_type==2 || $process_rpt_type==3 || $process_rpt_type==4 || $process_rpt_type==5) //Cutting
		{
			$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			(d.production_qnty) as prod_qty
			FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
			WHERE  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  c.id=d.mst_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type=$prod_type   and d.production_type=$prod_type and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)   $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";

			$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,b.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.prod_qnty) as prod_qty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type='$subcon_prod_type' and d.production_type='$subcon_prod_type' and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";
		}
		else if($process_rpt_type==7) //Cut and Lay
		{
			$location_cond = str_replace("c.location", "c.location_id", $location_cond);
			$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			(d.size_qty) as prod_qty
			FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
			WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond7 order by b.id,d.country_id ";
		}
		else //Ex-Factory 
		{
			$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty
			FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
			WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $prod_date_cond2 order by b.id,d.country_id ";

			$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,b.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<> 4       $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";



		}
		

		$sql_result=sql_select($sql_prod);
		$sql_result_subcon=sql_select($subcon_sql_prod);
		$prod_data_arr=array();
		$po_id='';
		foreach($sql_result as $row)
		{
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['job']=$row[csf('job_no')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['pre_job']=$row[csf('job_no_prefix_num')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['client_id']=$row[csf('client_id')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_no']=$row[csf('po_number')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['company']=$row[csf('company_name')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['country_id']=$row[csf('country_id')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
			$tmp_arr[$row[csf('po_id')]][$row[csf('country_id')]][date("Y-m-d",strtotime($row[csf("prod_date")]))]['prod_qty']+=$row[csf('prod_qty')];
			if($po_id=='') $po_id=$row[csf('po_id')];else $po_id.=",".$row[csf('po_id')];
		}
	 	// echo "<pre>";
	 	// print_r($prod_data_arr);die();

		foreach($sql_result_subcon as $row)
		{
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['job']=$row[csf('job_no')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['pre_job']=$row[csf('job_no_prefix_num')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_no']=$row[csf('po_number')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['company']=$row[csf('company_name')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_client']=$row[csf('buyer_client')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['country_id']=$row[csf('country_id')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_quantity')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
			$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
			$tmp_arr[$row[csf('po_id')]][$row[csf('country_id')]][date("Y-m-d",strtotime($row[csf("prod_date")]))]['prod_qty']+=$row[csf('prod_qty')];
			if($po_id=='') $po_id=$row[csf('po_id')];else $po_id.=",".$row[csf('po_id')];
			$all_subcon_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		}
	    $all_sub_po=implode(",", $all_subcon_po_id_arr);  


	    $po_id=implode(",",array_unique(explode(",",$po_id)));
	    $po_id_cond = "";
	    if ($po_id != "")
	    {
       		 $po_id =$po_id; //substr($po_id, 0, -1);
       		 if ($db_type == 0)
       		 	$po_id_cond = "and b.id in(" . $po_id . ")";
       		 else 
       		 {
       		 	$po_ids = explode(",", $po_id);
       		 	if (count($po_ids) > 1000) {
       		 		$po_id_cond = "and (";
       		 		$po_ids = array_chunk($po_ids, 1000);
       		 		$z = 0;
       		 		foreach ($po_ids as $id) {
       		 			$id = implode(",", $id);
       		 			if ($z == 0)
       		 				$po_id_cond .= " b.id in(" . $id . ")";
       		 			else
       		 				$po_id_cond .= " or b.id in(" . $id . ")";
       		 			$z++;
       		 		}
       		 		$po_id_cond .= ")";
       		 	} else
       		 	$po_id_cond = "and b.id in(" . $po_id . ")";
       		 }
       	}
        $sql_color="SELECT b.id as po_id,c.order_quantity as po_qty,c.country_id from   wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) $po_id_cond $buyer_id_cond $year_cond $job_cond $style_cond $order_cond";
        $po_country_qty=array();
        $sql_result_color=sql_select($sql_color);
        foreach($sql_result_color as $row)
        {
        	$po_country_qty[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
        }
		 $sql_sub_po="SELECT id,order_quantity from subcon_ord_dtls where status_active=1 and is_deleted=0 and id in($all_sub_po)";
		foreach(sql_select($sql_sub_po) as $row)
		{
			$po_country_qty_sub[$row[csf('id')]]['po_qty']+=$row[csf('order_quantity')];
		}
 

			$tot_width=1120+(50*$num_days);
			ob_start(); 
			
			?>
            <div>
            <div style="width:<? echo $tot_width+30;?>px; ruby-align:center">
			<fieldset style="width:<? echo $tot_width;?>px;">
                <table width="<? echo $tot_width;?>"  cellspacing="0"   >
                   
                    <tr style="border:none;">
                        <td colspan="10" align="center" style="border:none; font-size:16px; font-weight:bold">
                        <? echo  $production_process[$process_rpt_type].' ('.$company_names.')'; ?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". change_date_format($start_date).' To '. change_date_format($end_date);?>
                        </td>
                    </tr>
                </table>
                <br />	
                <table class="rpt_table" width="<? echo $tot_width+20;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr >
                            <th width="30">SL</th>
                            <th width="130">PO Company</th>
                            <th width="120">Buyer Name</th>
                            <th width="120">Buyer Client</th>
                            <th width="120">Style Ref.</th>
                            <th width="60">Job No</th>
                            <th width="100">PO No.</th>
                            <th width="100">Country</th>
                            <th width="80">Country Qty</th>
                            <th width="80">Country Qty Pcs</th>
                            <th width="80"><p><? echo $th_caption;?></p></th>
                            <?
                             for($m=1;$m<=$num_days;$m++)
                              {
                                  ?>
                            	<th width="50"> <? echo  ($m<=9)? '0'.$m:$m; ?></th>
                          		 <?
								}
								?>
                            <th  width="">Total Qty.</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo $tot_width+20;?>px; max-height:425px; overflow-y:scroll"   id="scroll_body">
                    <table class="rpt_table" width="<? echo $tot_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
                        $grand_country_qty=$grand_cutting_qty=$grand_total_prod=$grand_country_qty_pcs=0;	
                        $tot_rows=count($sql);
                        $i=1;	   

                        foreach($prod_data_arr as $po_id=>$po_val)	
                        {
							 foreach($po_val as $country_id=>$val)	
                       		 {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									$cutting_prod_qty=0;
									 for($k=1;$k<=$num_days;$k++)
									  {
										  $dayss=($k<=9)? '0'.$k:$k;
										  $prod_date_key=$start_date_tmp."-".$dayss;
										$cutting_prod_qty+=$tmp_arr[$po_id][$prod_date_key]['prod_qty'];
									  }//$process_rpt_type
									 $po_button="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_prod_country_size_report','$prod_type')\"> ".number_format($cutting_prod_qty,0)." </a>";
									 
									
									
									$set_ratio=$val['ratio']; 
									$po_qty=$po_country_qty[$po_id]['po_qty']/$set_ratio;
									$po_qty_pcs=$po_country_qty[$po_id]['po_qty'];
									
									if($val['country_id']=="00")
									{
										// $po_button_qty=0;
										 $po_qty=$po_country_qty_sub[$po_id]['po_qty'];
										 $po_qty_pcs=$po_qty;
										 $po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_qty,0)." </a>";
									}
									else
									{
										$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_qty,0)." </a>";

									}
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td width="30"><? echo $i;?></td>
                                    <td width="130"><p><? echo  $company_library[$val['company']];  ?></p></td>
                                    <td width="120"><div style="word-break:break-all"><? echo $buyer_short_library[$val['buyer_name']];?>&nbsp;</div></td>
                                    <td width="120"><div style="word-break:break-all"><? echo $client_array[$val['client_id']];?>&nbsp;</div></td>
                                    <td width="120"><div style="word-break:break-all"><? echo $val['style'];?></div></td>
                                    <td width="60"><p><? echo $val['pre_job'];?></p></td>
                                    <td width="100" align="center"><div style="word-break:break-all"><? echo $val['po_no'];echo $sub_self=($val['country_id']=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
                                    <td width="100"><div style="word-break:break-all"><? echo $country_arr;?></div></td>
                                    <td width="80"  align="right"><p><? echo $po_button_qty;//number_format($po_qty,0);?></p></td>
                                    <td width="80"  align="right" title="<? echo $set_ratio;?>"><p><? echo number_format($po_qty_pcs,0);?></p></td>
                                    <td width="80"  align="right"><p><? echo $po_button;?></p></td>
                                     <?
									 $tot_prod_qty=0;
								 for($m=1;$m<=$num_days;$m++)
								  {
									  $days=($m<=9)? '0'.$m:$m;
									  $prod_date_key=$start_date_tmp."-".$days;
									$prod_qty=$tmp_arr[$po_id][$prod_date_key]['prod_qty'];
									$tot_prod_qty+=$prod_qty;
									$friday=date('D',strtotime($prod_date_key));
									$total_prod_qty_arr[$m]+=$prod_qty;
									if($friday=='Fri')
									{
									 $td_color="red";	
									}
									else  $td_color="";	
											?>
									<td width="50" bgcolor="<? echo $td_color; ?>" title="<? echo $friday;?>"  align="right"> <? echo  number_format($prod_qty,0); ?></td>
								<?
									}
								?>
                                <td  width="" align="right"><p><? echo  number_format($tot_prod_qty,0); ?></p></td>
                                </tr>    
							<?
                            $i++;
							$grand_total_prod+=$tot_prod_qty;
							$grand_country_qty+=$po_qty;
							$grand_country_qty_pcs+=$po_qty_pcs;
							$grand_cutting_qty+=$cutting_prod_qty;
							 }
					}
					?>
                </table>
                 <table style="width:<? echo $tot_width;?>px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    	<tfoot>
                        <th width="30">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100"><strong>Grand Total:</strong></th>
                        <th width="80"  align="right"><? echo number_format($grand_country_qty,0); ?></th>
                        <th width="80"  align="right"><? echo number_format($grand_country_qty_pcs,0); ?></th>
                        <th width="80"  align="right"><? echo number_format($grand_cutting_qty,0); ?></th>
						 <?
                             for($m=1;$m<=$num_days;$m++)
                              {
								  $day=($m<=9)? '0'.$m:$m;
                                        ?>
                                <th width="50" align="right"> <? echo number_format($total_prod_qty_arr[$m],0); ?></th>
                            <?
                                }
                            ?>
                     <th width="" align="right"><? echo number_format($grand_total_prod,0); ?></th>
                   </tfoot>
                </table>
                </div>
			</fieldset>
             </div>
			<?	
	}
	elseif($report_type==3) // for summary button
	{
		$company= str_replace("'","", $cbo_working_company_id);
		$floor= str_replace("'","", $cbo_floor);
		$floor_name="";
		foreach(explode(",", $floor) as $v ) 
		{
			if($floor_name)$floor_name.=','.$floor_library[$v];else $floor_name=$floor_library[$v];
		}
		$company_name="";
		foreach(explode(",", $company) as $v )
		{
			if($company_name)$company_name.=','.$company_library[$v];else $company_name=$company_library[$v];
		}
		$all_companies_id=implode(",",$company_library);

		$current_month=date("m",strtotime($start_date));
		//$current_month=date('m');
		$current_year=date("Y",strtotime($start_date));
		$lastmonth=$current_month-1;
		if($current_month==1)
		{
			$lastmonth2=12;
			$current_year2=$current_year-1;
		}

		$firstdate= "01-".$lastmonth."-".$current_year ;
		// $lastdateofmonth=date('t',$lastmonth);// 	
		$lastdateofmonth=cal_days_in_month(CAL_GREGORIAN, $lastmonth, $current_year); 
		$lastdateofmonth2=cal_days_in_month(CAL_GREGORIAN, $lastmonth2, $current_year2);
		$lastdate=$lastdateofmonth."-".$lastmonth."-".$current_year ;
		$lastdate2=$lastdateofmonth2."-".$lastmonth2."-".$current_year2 ;
		//echo $firstdate ."==".$lastdate ;
					
		if($db_type==0)
		{
			$firstdate=change_date_format($firstdate,"yyyy-mm-dd","");
			$lastdate=change_date_format($lastdate,"yyyy-mm-dd","");
			$lastdate2=change_date_format($lastdate2,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$firstdate=change_date_format($firstdate,"","",1);
			$lastdate=change_date_format($lastdate,"","",1);
			$lastdate2=change_date_format($lastdate2,"","",1);
		}
		$sum_prod_date_cond=" and c.production_date between '$firstdate' and '$lastdate'";
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		if($current_month==1) $sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		$sum_width=1370;
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";
		 
		ob_start(); 
		$summaryHTML = "";
		?>
		<style type="text/css">.alignment_css{word-wrap: break-word;word-break: break-all;}</style>
	
		<? $summaryHTML.='<div id="buyer_wise_summary"  style="width:'.($sum_width+30).'px;  ruby-align:center" >
		<fieldset style="width:'.($sum_width+30).'px;">
			<table width="'.$sum_width.'"  cellspacing="0">
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none; font-size:16px; font-weight:bold">
					'.$company_names.'
					<br>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
					'."Date: ". change_date_format($start_date).' To '. change_date_format($end_date).'
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center"  > <strong>Buyer wise summary </strong></td>
				</tr>
				  
			</table>
			<br />	
			<table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<!-- <tr>
						<td colspan="16"></td>
						<td colspan="2" align="center"><b>Waiting For Shipment</b></td>
					</tr> -->
					<tr>
						<th class="alignment_css" width="30">SL</th>
						<th class="alignment_css" width="120">Buyer</th>
						<th class="alignment_css" width="100">PO Qty</th>
						<th class="alignment_css" width="80">Cut Qty</th>
						<th class="alignment_css" width="80">Cut Qty%</th>
						<th class="alignment_css" width="80">Cutting QC</th>
						<th class="alignment_css" width="80">Cutting QC%</th>
						<th class="alignment_css" width="80">Sewing<br> Input</th>
						<th class="alignment_css" width="80">Sewing<br> Output</th>
						<th class="alignment_css" width="80">Sewing CM <br>Value BOM</th>
						<th class="alignment_css" width="80">Sewing CM <br>Value LC</th>
						<th class="alignment_css" width="80">Sewing FOB<br> Value</th>                           
						<th class="alignment_css" width="80">Finishing</th> 
						<th class="alignment_css" width="80">Export Pcs</th>
						<th class="alignment_css" width="80">Export CM <br>Value BOM</th>
						<th class="alignment_css" width="80">Export CM <br>Value LC</th>
						<th class="alignment_css" width="80">Export FOB<br> Value</th>
					</tr>
				</thead>
			</table>
			<div style="width: '.($sum_width+20).'px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
				<table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">';					
								 
			//cut and lay qty.....................................
			$buyer_data_arr=array();
			$lc_company_arr=array();
			$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
			$sql_cut_lay = "SELECT b.po_total_price,(b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio, (d.size_qty) as prod_qty FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_cut_lay $floor_cond $prod_date_cond7 order by b.id,d.country_id "; 
			//echo $sql_cut_lay; die;
			$cut_lay_result_arr=sql_select($sql_cut_lay);
			foreach($cut_lay_result_arr as $rows)
			{
				$buyer_data_arr[$rows[csf('buyer_name')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
				$buyer_data_arr[$rows[csf('buyer_name')]]['job_no']+=$rows[csf('job_no')];
				
				$prod_dtls_data_job_arr[$rows[csf('job_no')]] =$rows[csf('job_no')];	
				$prod_dtls_data_po_arr[$rows[csf('po_id')]] =$rows[csf('po_id')];	
				$prod_dtls_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['style']=$rows[csf('style_ref_no')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_name']=$rows[csf('buyer_name')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_client']=$rows[csf('buyer_client')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['po_number']=$rows[csf('po_number')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['ratio']=$rows[csf('ratio')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['job_no']=$rows[csf('job_no')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['unit_price']=$rows[csf('unit_price')];
				$total_po_amount_arr[$rows[csf('job_no')]]+=$rows[csf('po_total_price')];
				
				$tmp_count=$rows[csf('country_id')];
				$tmp_po[$rows[csf('po_id')]]=$rows[csf('po_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				//$cut_lay_qty_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
			}
			
			if(empty($cut_lay_result_arr))
			{
				echo get_empty_data_msg();
				die;
			}
			unset($cut_lay_result_arr);
			//echo "<pre>";
			//print_r($prod_dtls_data_arr); die;
			//cut and lay qty.....................................
				
			$sql_prod_sum = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty, (CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty, (CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty, (CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty, (CASE WHEN c.production_type=11 and d.production_type=11 THEN d.production_qnty ELSE 0 END)  as poly_qty, (CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e WHERE  a.job_no=b.job_no_mst  and c.po_break_down_id=b.id and c.id=d.mst_id  and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,4,5,11,8) and d.production_type=c.production_type  and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3) $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";//and c.po_break_down_id=e.po_break_down_id and a.job_no=e.job_no_mst

			$subcon_sql_prod="SELECT  c.rate as unit_price,b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client, b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (CASE WHEN a.production_type=1 and d.production_type=1 THEN d.prod_qnty ELSE 0 END)  as cut_qty, (CASE WHEN a.production_type=7  and d.production_type=7 THEN d.prod_qnty ELSE 0 END)  as sew_in_qty, (CASE WHEN a.production_type=2  and d.production_type=2 THEN d.prod_qnty ELSE 0 END)  as sew_out_qty, (CASE WHEN a.production_type=5 and d.production_type=5 THEN d.prod_qnty ELSE 0 END)  as poly_qty, (CASE WHEN a.production_type=4 and d.production_type=4  THEN d.prod_qnty ELSE 0 END)  as finish_qty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(1,2,4,5,7) and d.production_type in(1,2,4,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";
				
			// echo $sql_prod_sum; die();	
			$sum_result=sql_select($sql_prod_sum);
			// die('GO TO HELL!');
			// echo $subcon_sql_prod;die();
			// $po_id='';	$country_id='';$buyer_ids='';
			foreach($sum_result as $row)
			 {
					
				$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];
				$buyer_data_arr[$row[csf('buyer_name')]]['cut_qty']+=$row[csf('cut_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['poly_qty']+=$row[csf('poly_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['finish_qty']+=$row[csf('finish_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['buyer_client']=$row[csf('buyer_client')];
				$buyer_data_arr[$row[csf('buyer_name')]]['job_no']=$row[csf('job_no')];
				
				$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];	
				$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	
				$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['cut_qty']+=$row[csf('cut_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['poly_qty']+=$row[csf('poly_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['finish_qty']+=$row[csf('finish_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];

				$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
				$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];	
				
			 }
			 unset($sum_result);
			 // die('GO TO HELL!');	

			$sum_result_subcon=sql_select($subcon_sql_prod);
			 foreach($sum_result_subcon as $row)
			 {
				$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];	
				$buyer_data_arr[$row[csf('buyer_name')]]['cut_qty']+=$row[csf('cut_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['poly_qty']+=$row[csf('poly_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['finish_qty']+=$row[csf('finish_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['buyer_client']=$row[csf('buyer_client')];
				$buyer_data_arr[$row[csf('buyer_name')]]['job_no']=$row[csf('job_no')];
				
				$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];	
				$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	
				$prod_dtls_data_arr[$row[csf('po_id')]]['cut_qty']+=$row[csf('cut_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['poly_qty']+=$row[csf('poly_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['finish_qty']+=$row[csf('finish_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				
				$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
				$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];	
				$all_subcon_po_id_arrs[$row[csf('po_id')]]=$row[csf('po_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
			 }
			 unset($sum_result_subcon);

			$location_cond_ex = str_replace("c.location", "c.delivery_location_id", $location_cond);
			$floor_cond2=str_replace("c.floor_id", "c.delivery_floor_id", $floor_cond);
			$sql_prod_exf = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.job_no,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio, (CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_ex $floor_cond2  $prod_date_cond2 order by b.id,d.country_id ";

			$subcon_sql_ex="SELECT  c.rate as unit_price, b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0       $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";

			//echo $sql_prod_exf;die;
			//echo $subcon_sql_ex;die;

			$result_prod_exf=sql_select($sql_prod_exf);
			$result_prod_exf_subcon=sql_select($subcon_sql_ex);
			$po_no_id="";$buyer_ids2='';
			$po_country_id='';
			foreach($result_prod_exf as $row)
			{
				$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				$po_ex_fact_dtls_qty[$row[csf('po_id')]]['prod_qty']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_fac_fob']+=$row[csf('prod_qty')]*$row[csf('unit_price')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];		
				$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];		
				$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
				if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
				if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
				$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
			}
			unset($result_prod_exf);
			// echo "<pre>";
			// print($po_ex_fact_sum_qty);
			// echo "</pre>";
			foreach($result_prod_exf_subcon as $row)
			{
				$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				$po_ex_fact_dtls_qty[$row[csf('po_id')]]['prod_qty']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_fac_fob']+=$row[csf('prod_qty')]*$row[csf('unit_price')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];	
				$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];	
				//$prod_dtls_data_job_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
				$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
				if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
				if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
				$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
			}
			unset($result_prod_exf_subcon);
							
			$po_country_id=implode(",",array_unique(explode(",",$po_country_id)));
			$po_no_id=implode(",",array_unique(explode(",",$po_no_id)));
							
			//echo $po_cond_for_in;
			$poIds=implode(",",$tmp_po);
			$poIds=chop($poIds,','); $po_cond_for_in="";// $order_cond1=""; 
			$po_ids=count(array_unique(explode(",",$poIds)));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
			}
			else
			{
				$poIds=implode(",",array_unique(explode(",",$poIds)));
				$po_cond_for_in=" and b.id in($poIds)";
			}
			
			$lc_company = implode(",", $lc_company_arr);
			// Country
			$country_Id=implode(",",$tmp_count);
			$poIds_c=chop($country_Id,','); $po_cond_for_in2="";// $order_cond1=""; 
			$po_ids_c=count(array_unique(explode(",",$country_Id)));
			if($db_type==2 && $po_ids_c>1000)
			{
				$po_cond_for_in2=" and (";
				$poIdsArr_c=array_chunk(explode(",",$poIds_c),999);
				foreach($poIdsArr_c as $ids)
				{
					$ids_c=implode(",",$ids);
					$po_cond_for_in2.=" c.country_id in($ids_c) or"; 
				}
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
			}
			else
			{
				$poIds_c=implode(",",array_unique(explode(",",$poIds_c)));
				$po_cond_for_in2=" and c.country_id in($poIds_c)";
			}
					
			$sql_prod_exf_last = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity, (CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty,a.client_id as buyer_client FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $sum_prod_date_cond2 order by b.id,d.country_id "; 
			// echo $sql_prod_exf_last;die();
			$result_prod_exf_last=sql_select($sql_prod_exf_last);
			foreach($result_prod_exf_last as $row)
			{
				$po_ex_fact_sum_qty_lastmon[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				//$po_ex_fact_sum_qty_lastmon[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
			}
		 	unset($result_prod_exf_last);
			
			$sql_color="SELECT a.buyer_name,b.po_quantity as po_qty,b.id as po_id, a.client_id as buyer_client from   wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,2,3)  $po_cond_for_in   $buyer_id_cond  ";
			$po_country_sum_qty=array();
			$po_country_dtls_qty=array();
			$sql_result_color=sql_select($sql_color);
			foreach($sql_result_color as $row)
			{
				$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
				$po_country_dtls_qty[$row[csf('po_id')]]['po_qty']+=$row[csf('po_qty')];
				$summ+=$row[csf('po_qty')];
				 //echo $row[csf('po_qty')]."=";
			}
			unset($sql_result_color);
			
			$sub_po_cond=implode(",",$all_subcon_po_id_arrs);
			$sql_color_subcon ="SELECT a.party_id as buyer_name ,c.qnty as po_qty,b.id as po_id,'00' as country_id,b.cust_buyer as buyer_client from   subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where  a.subcon_job=b.job_no_mst and c.order_id=b.id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.id in($sub_po_cond) order by b.id ";
			foreach(sql_select($sql_color_subcon) as $row)
			{
				$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
				$po_country_dtls_qty[$row[csf('po_id')]]['po_qty']+=$row[csf('po_qty')];
				$summ+=$row[csf('po_qty')];
			}
			
			$target_sql=sql_select("SELECT   mst_id, target_per_hour  FROM PROD_RESOURCE_DTLS ORDER BY ID asc  ");
			$line_wise_tg=array();
			foreach($target_sql as $v)
			{
				$line_wise_tg[$v[csf("mst_id")]]=$v[csf("target_per_hour")];
			}
			unset($target_sql);
			
			$k=1;$grand_sum_cut_qty=$grand_sum_in_qty=$grand_sum_out_qty=$grand_sum_poly_qty=$grand_sum_finished_qty=$grand_sum_exfact_qty=$grand_sum_country_qty=$grand_sum_last_month_qty=$grand_sum_sewfob=$grand_sum_exfob=0;
			 // echo "<pre>";
			 // print_r($buyer_data_arr);die;
			// $condition= new condition();
			 $all_jobs="";
			 foreach($prod_dtls_data_job_arr as $v)
			 {
				if($v && $v!=",")
				{
					if($all_jobs)$all_jobs.=","."'".$v."'";
					else $all_jobs="'".$v."'";
				}
			 }
			  $txt_order_id="";
			 foreach($prod_dtls_data_po_arr as $v)
			 {
				if($v && $v!=",")
				{
					if($txt_order_id)$txt_order_id.=",".$v;
					else $txt_order_id=$v;
				}
			 }

			// echo $txt_order_id; 
			$po_ids=count(explode(",",$txt_order_id));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_=" and (";
				$poIdsArr=array_chunk(explode(",",$txt_order_id),999);
				foreach($poIdsArr as $ids)
				{
					$ids_c=implode(",",$ids);
					$po_cond_.=" b.id in($ids_c) or"; 
				}
				$po_cond_=chop($po_cond_,'or ');
				$po_cond_.=")";
			}
			else
			{						
				$po_cond_=" and b.id in($txt_order_id)";
			}
			 // die('GO TO HELL!');	
			 
			$po_qty=0;
			$po_plun_cut_qty=0;
			$total_set_qnty=0;
			 
			$pro_sql=sql_select("SELECT  a.production_type, b.unit_price,a.id,a.po_break_down_id, a.sewing_line,sum(case WHEN a.production_type=5 then a.production_quantity else 0 end ) as sew,  sum(case WHEN a.production_type=8 then production_quantity else 0 end ) as fin  FROM pro_garments_production_mst a,wo_po_break_down b  where a.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 and  a.production_type in(5,8) $po_cond_  group by  a.production_type,  b.unit_price,  a.id, a.po_break_down_id, a.sewing_line ORDER BY a.ID asc  ");//and b.id in($txt_order_id)
			$po_wise_tg=array();
			foreach($pro_sql as $v)
			{
				if($v[csf("production_type")]==5)
				{
					$po_wise_tg[$v[csf("po_break_down_id")]]["line"]=$v[csf("sewing_line")];
				}
				$po_wise_tg[$v[csf("po_break_down_id")]]["qnty"]+=$v[csf("sew")]-$v[csf("fin")];
				$po_wise_tg[$v[csf("po_break_down_id")]]["value"]+=($v[csf("sew")]-$v[csf("fin")])*$v[csf("unit_price")];
			}
			unset($pro_sql);

			//print_r($po_wise_tg);die;
			// echo $all_companies_id."<br>";
			// echo $poIds;
			$cm_gmt_cost_dzn_arr=array();
			$cm_gmt_cost_dzn_arr_new=array();
			if($poIds!="")
			{
				//$cm_gmt_cost_dzn_arr=array();
				// $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_companies_id,$poIds); 
				//$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($company,$poIds); 
				// print_r($cm_gmt_cost_dzn_arr);die;
			}
			$new_arr=array_unique(explode(",", $poIds));
			$chnk_arr=array_chunk($new_arr,50);
			foreach($chnk_arr as $vals )
			{
				$p_ids=implode(",", $vals);
				//if(!empty($cm_gmt_cost_dzn_arr))
				//$cm_gmt_cost_dzn_arr.=fnc_po_wise_cm_gmt_class($company,$p_ids); 
				//else
				 $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($lc_company,$p_ids); 
				 foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
				 {
					$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"] =$vv["dzn"] ;
				 }
			}
			
			/*
			|--------------------------------------------------------------------------
			| getting price quotation wise cm valu
			| start
			|--------------------------------------------------------------------------
			*/
			foreach($prod_dtls_data_arr as $po_id=>$val)
			{
				$job_no_arr[$val['job_no']]= $val['job_no'];
			}
			
			$jobNoCondition = '';
			$noOfjobNo = count($job_no_arr);
			if($db_type == 2 && $noOfjobNo > 1000)
			{
				$jobNoCondition = " and (";
				$jobNoArr = array_chunk($job_no_arr,999);
				foreach($jobNoArr as $job)
				{
					$jobNoCondition.=" c.job_no in('".implode("','",$job)."') or";
				}
				$jobNoCondition = chop($jobNoCondition,'or');
				$jobNoCondition .= ")";
			}
			else
			{
				$jobNoCondition=" and c.job_no in('".implode("','",$job_no_arr)."')";
			}
			
			//echo $jobNoCondition; die;
			//$all_job = "'".implode("','", $jobArr)."'";
			$all_job = $all_jobs;
			$quotation_qty_sql="
				SELECT
					a.id as quotation_id, a.mkt_no, a.sew_smv, a.sew_effi_percent, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.total_cost, b.costing_per_id, c.job_no
				FROM
					wo_price_quotation a,
					wo_price_quotation_costing_mst b,
					wo_po_details_master c
				WHERE
					a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 ".$jobNoCondition."
				ORDER BY
					a.id
			";
			//and c.job_no in(".$all_job."
			//echo $quotation_qty_sql; die();
			$quotation_qty_sql_res = sql_select($quotation_qty_sql);
			$quotation_qty_array = array();
			$quotation_id_array = array();
			
			//don't use
			$all_jobs_array = array();
			//don't use
			$jobs_wise_quot_array = array();
			$quot_wise_arr = array();
			foreach ($quotation_qty_sql_res as $val)
			{
				$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
				$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
				$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
				//$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
				//$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];
			
				$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
				$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];
			
				//$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
				//$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
				//$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
				//$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
				//$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
				//$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
				
				$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
				$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
				$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
				$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
				$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
				//$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
			}
			unset($quotation_qty_sql_res);
			$all_quot_id = implode(",", $quotation_id_array);
			//echo "<pre>";
			//print_r($quot_wise_arr); die;
			
			// print_r($style_wise_arr);die();
			// ===============================================================================
			$sql_fab = "
				SELECT
					a.quotation_id, sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount, b.job_no
				from
					wo_pri_quo_fabric_cost_dtls a,
					wo_po_details_master b
				where
					a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.fabric_source=2 and a.status_active=1 and b.status_active=1
				group by
					a.quotation_id, b.job_no
			";
			//echo $sql_fab; die();
			$data_array_fab=sql_select($sql_fab);
			$fab_summary_data = array();
			$fab_order_price_per_dzn = 1;
			foreach($data_array_fab as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1)
				{
					$fab_order_price_per_dzn=12;
				}
				else if($costing_per_id==2)
				{
					$fab_order_price_per_dzn=1;
				}
				else if($costing_per_id==3)
				{
					$fab_order_price_per_dzn=24;
				}
				else if($costing_per_id==4)
				{
					$fab_order_price_per_dzn=36;
				}
				else if($costing_per_id==5)
				{
					$fab_order_price_per_dzn=48;
				}
			
				$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
				$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			}
			unset($data_array_fab);
			//echo "<pre>";
			//print_r($fab_summary_data); die;
			
			// ==================================================================================
			$sql_yarn = "
				SELECT
					a.quotation_id, sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount, b.job_no 
				from
					wo_pri_quo_fab_yarn_cost_dtls a, wo_po_details_master b 
				where
					a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 
				group by
					a.quotation_id,b.job_no
			";
			//echo $sql_yarn; die();
			$data_array_yarn=sql_select($sql_yarn);
			$yarn_summary_data = array();
			foreach($data_array_yarn as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
				else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
				else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
				else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
				else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
				//$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
				// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
				//$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
			}
			unset($data_array_yarn);
			
			// ===================================================================================
			$sql_conversion = "
				SELECT
					a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active, b.body_part_id, b.fab_nature_id, b.color_type_id, b.construction, b.composition, c.job_no
				from
					wo_po_details_master c,
					wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
				where
					a.quotation_id in(".$all_quot_id.") and a.quotation_id=c.quotation_id and a.status_active=1
			";
			//echo $sql_conversion; die();
			$data_array_conversion=sql_select($sql_conversion);
			$conv_order_price_per_dzn = 1;
			$conv_summary_data = array();
			$conversion_cost_arr = array();
			foreach($data_array_conversion as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$conv_order_price_per_dzn=12;}
				else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
				else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
				else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
				else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
				$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];
				$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
				$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			}
			unset($data_array_conversion);
			//echo "<pre>";
			//print_r($conversion_cost_arr); die();
			
			if($db_type==0)
			{
				$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
				from wo_price_quotation_costing_mst a,wo_po_details_master b
				where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
			}
			if($db_type==2)
			{
				$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
				from wo_price_quotation_costing_mst a,wo_po_details_master b
				where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
			}
			//echo $sql; die();
			$data_array=sql_select($sql);
			foreach( $data_array as $row )
			{
				//$sl=$sl+1;
				if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
				else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//used
				$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
				
				$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
				$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				
				/*
				//$price_dzn=$row[csf("confirm_price_dzn")];
				//$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
				$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
				$summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
				$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
				$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
				//$row[csf("commission")]
				$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
				$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
				$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
				$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
				$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
				$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
				$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
				$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
				$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
				$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
				$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
				$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];
				$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
				$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
				$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
				//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
				$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
				$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
				$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
				//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
				$net_value_dzn=$row[csf("price_with_commn_dzn")];
				$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
				$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;
				//yarn_amount_total_value
				$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
				//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
				$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
				$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
				$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
				$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
				$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
				$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
				//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
				$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
				$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
				$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
				$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;
			
				//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
				$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
				$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
				$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
				$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
				$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
				$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
				$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
				$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
				*/
			}
			unset($data_array);
			//echo "<pre>";
			//print_r($summary_data);
			//die();
			
			//======================================================================
			$sql_commi = "
				SELECT
					a.id,a.quotation_id, a.particulars_id, a.commission_base_id, a.commision_rate, a.commission_amount, a.status_active, b.job_no
				from
					wo_pri_quo_commiss_cost_dtls a, wo_po_details_master b
				where
					a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.status_active=1 and a.commission_amount>0 and b.status_active=1
			";
			//echo $sql_commi; die();
			$result_commi=sql_select($sql_commi);
			$CommiData_foreign_cost=0;
			//$CommiData_lc_cost=0;
			//$foreign_dzn_commission_amount=0;
			//$local_dzn_commission_amount=0;
			$CommiData_foreign_quot_cost_arr = array();
			$commision_local_quot_cost_arr = array();
			foreach($result_commi as $row)
			{
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			
				if($row[csf("particulars_id")]==1) //Foreign
				{
					$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					//$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
					$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				}
				else
				{
					//$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					//$local_dzn_commission_amount+=$row[csf("commission_amount")];
					$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
				}
			}
			unset($result_commi);
			//echo "<pre>su..re";
			//print_r($CommiData_foreign_quot_cost_arr); die();
			
			//=====================================================================================
			$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
			//echo $sql_comm; die();
			$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
			// $summary_data['comm_cost_dzn']=0;
			// $summary_data['comm_cost_total_value']=0;
			$result_comm=sql_select($sql_comm);
			$commer_lc_cost = array();
			$commer_lc_cost_quot_arr = array();
			//$commer_without_lc_cost = array();
			foreach($result_comm as $row)
			{
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
				//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				//$comm_amtPri=$row[csf('amount')];
				//$item_id=$row[csf('item_id')];
				if($row[csf('item_id')] == 1)//LC
				{
					$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				}
				/*
				else
				{
					$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				}
				*/
			}
			unset($result_comm);
			//echo "<pre>";
			//print_r($commer_lc_cost_quot_arr); die();
			
			/*
			|--------------------------------------------------------------------------
			| getting price quotation wise cm valu
			| end
			|--------------------------------------------------------------------------
			*/			

			//print_r($cm_gmt_cost_dzn_arr_new);die;
			//die('GO TO HELL!');	
			$buyer_wise_all_info=array();
			foreach($prod_dtls_data_arr as $po_id=>$val)
			{
				$jobs= $val['job_no'];
				$set_ratio=$val['ratio'];
				$cm_dzn_rate=$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"];
				$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
				$cnty_qnty=$po_country_qty*$set_ratio;
				$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
				$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
				
				$unit_price=$val['unit_price'];
				$sewing_cm=($val['sew_out_qty']*$cm_dzn_rate)/12;
				$sewing_fob=$val['sew_out_qty']*$unit_price;

				$exfac_cm=($ex_fact_country_qty*$cm_dzn_rate)/12;
				$exfac_fob=$ex_fact_country_qty*$unit_price;

				$buyer_wise_all_info[$val['buyer_name']]['sew_cm']+=$sewing_cm;
				$buyer_wise_all_info[$val['buyer_name']]['sew_fob']+=$sewing_fob;
				$buyer_wise_all_info[$val['buyer_name']]['ex_cm']+=$exfac_cm;
				$buyer_wise_all_info[$val['buyer_name']]['ex_fob']+=$exfac_fob;
				$target=$line_wise_tg[$po_wise_tg[$po_id]["line"]];
				$buyer_wise_all_info[$val['buyer_name']]['target']+=$target;
				$buyer_wise_all_info[$val['buyer_name']]['inhand_qty']+=$po_wise_tg[$po_id]["qnty"];
				$buyer_wise_all_info[$val['buyer_name']]['inhand_val']+=$po_wise_tg[$po_id]["value"];
				//echo $po_id.'ins'.$summary_data2[$jobs][$po_id][inspection_job].'cu'.$summary_data2[$jobs][$po_id][currier_pre_cost_job].' cer'.$summary_data2[$jobs][$po_id][certificate_pre_cost_job].' '.$summary_data2[$jobs][$po_id][commission_job];

				//echo $po_id."total_btb= $total_btb  lab=".$summary_data2[$jobs][$po_id][lab_test_job]." trims=".$summary_data2[$jobs][$po_id][trims_cost_job]." comm cost ".$summary_data2[$jobs][$po_id][comm_cost_job] ." studio".$summary_data[$jobs][studio_cost_job];
				
				//echo $val['sew_out_qty']."<br>";
			}

			foreach($prod_dtls_data_arr as $po_id=>$row)
			{
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| start
				|--------------------------------------------------------------------------
				*/

				$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row['job_no']][101]['conv_amount_total_value']*1;
				$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row['job_no']][30]['conv_amount_total_value']*1;
				$tot_aop_process_amount 		= $conversion_cost_arr[$row['job_no']][35]['conv_amount_total_value']*1;
				
				foreach($style_wise_arr as $style_key=>$val)
				{
					$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
					$total_quot_qty+=$val[('qty')];
					$total_quot_pcs_qty+=$val[('qty_pcs')];
					$total_sew_smv+=$val[('sew_smv')];
					$total_quot_amount+=$total_cost;
					$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
				}
				$total_quot_amount_cal = $style_wise_arr[$row['job_no']]['qty']*$style_wise_arr[$row['job_no']]['final_cost_pcs'];
				$tot_cm_for_fab_cost=$summary_data[$row['job_no']]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
				$commision_quot_local=$commision_local_quot_cost_arr[$row['job_no']];
				$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row['job_no']]+$commer_lc_cost_quot_arr[$row['job_no']]+$freight_cost_data[$row['job_no']]['freight_total_value']);
				$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
				$tot_inspect_cour_certi_cost=$summary_data[$row['job_no']]['inspection_total_value']+$summary_data[$row['job_no']]['currier_pre_cost_total_value']+$summary_data[$row['job_no']]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row['job_no']]['design_pre_cost_total_value'];
				
				$tot_emblish_cost=$summary_data[$row['job_no']]['embel_cost_total_value'];
				$pri_freight_cost_per=$summary_data[$row['job_no']]['freight_total_value'];
				$pri_commercial_per=$commer_lc_cost[$row['job_no']];
				$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$row['job_no']];
				
				$total_btb = $summary_data[$row['job_no']]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row['job_no']]['comm_cost_total_value']+$summary_data[$row['job_no']]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row['job_no']]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row['job_no']]['common_oh_total_value']+$summary_data[$row['job_no']]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
				$tot_quot_sum_amount = $total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
				$total_cm_for_gmt = ($tot_quot_sum_amount-$tot_cm_for_fab_cost-$total_btb);
				$total_quot_pcs_qty = $quotation_qty_array[$row['job_no']]['QTY_PCS'];

				$zs[$row['buyer_name']]['total_cm_for_gmt'] += $total_cm_for_gmt;
				$zs[$row['buyer_name']]['total_quot_pcs_qty'] += $total_quot_pcs_qty;
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| end
				|--------------------------------------------------------------------------
				*/
				//echo $quotation_qty_array[$row['job_no']]['QTY_PCS']."<br>";
			}
			//echo "<pre>";
			//print_r($zs);
			
			foreach($buyer_data_arr as $buyer_id=>$row)
			{
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| start
				|--------------------------------------------------------------------------
				*/
				$cm_lc_pcs = ($zs[$buyer_id]['total_cm_for_gmt']/$zs[$buyer_id]['total_quot_pcs_qty']);
				$cm_lc_value = ($row['sew_out_qty'])*($cm_lc_pcs);
				$cm_lc_value_export = ($buyer_data_arr[$buyer_id]['ex_factory'])*($cm_lc_pcs);

				$buyer_wise_all_info[$buyer_id]['sew_cm_lc'] += $cm_lc_value;
				$buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'] += $cm_lc_value_export;
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| end
				|--------------------------------------------------------------------------
				*/
			}
			
			foreach($buyer_data_arr as $buyer_id=>$val)
			{
				if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$po_buyer_qty=$po_country_sum_qty[$buyer_id]['po_qty'];
				$sew_fob=$val['sew_fob'];
				$ex_fact_qty=$buyer_data_arr[$buyer_id]['ex_factory'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
				$ex_fac_fob=$buyer_data_arr[$buyer_id]['ex_fac_fob'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
				$grand_sum_sewfob+=$sew_fob;
				$grand_sum_exfob+=$ex_fac_fob;
				$last_cut_qty=$lastmon_buyer_data_arr[$buyer_id]['cut_qty'];
				$last_sew_in_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_in_qty'];
				$last_sew_out_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_out_qty'];
				$last_poly_qty=$lastmon_buyer_data_arr[$buyer_id]['poly_qty'];
				$last_finish_qty=$lastmon_buyer_data_arr[$buyer_id]['finish_qty'];
				$last_ex_fact_sum_qty=$po_ex_fact_sum_qty_lastmon[$buyer_id]['prod_qty'];
				//$last_month_qty=$last_cut_qty+$last_sew_in_qty+$last_sew_out_qty+$last_poly_qty+$last_finish_qty+$last_ex_fact_sum_qty;
				$last_month_qty=$last_ex_fact_sum_qty;
				$lay_percent = ($val['cut_lay_qty']*100)/$po_buyer_qty;
				$cut_percent = ($val['cut_qty']*100)/$po_buyer_qty;

				$summaryHTML.='<tr bgcolor="'.$bgcolor.'">
					<td width="30">'.$k.'</td>
					<td width="120"><div style="word-break:break-all">'.$buyer_short_library[$buyer_id].'</div></td>
					<td width="100" align="right"><p>'.$po_buyer_qty.'</p></td>
					<td width="80" align="right"><p>'.$val['cut_lay_qty'].'</p></td>
					<td width="80" align="right"><p>'.number_format($lay_percent,2).'%</p></td>
					<td width="80" align="right" title="'.$last_cut_qty.'"><p>'.number_format($val['cut_qty'],0).'</p></td>
					<td width="80" align="right" title="'.$last_cut_qty.'"><p>'.number_format($cut_percent,2).'%</p></td>
					<td width="80" align="right" title="'.$last_sew_in_qty.'"><p>'.number_format($val['sew_in_qty'],0).'</p></td>
					<td width="80" align="right" title="'.$last_sew_out_qty.'"><p>'.number_format($val['sew_out_qty'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm_lc'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_fob'],0).'</p></td>
					<td width="80" align="right" title="'.$last_finish_qty.'"><p>'.number_format($val['finish_qty'],0).'</p></td>
					<td width="80" align="right" title="'.$last_ex_fact_sum_qty.'"><p>'.number_format($ex_fact_qty,0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['ex_cm'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['ex_fob'],0).'</p></td>
				</tr>';

				$k++;
				$grand_sum_exfact_qty+=$ex_fact_qty;
				$grand_sum_finished_qty+=$val['finish_qty'];
				 
				$grand_sum_out_qty+=$val['sew_out_qty'];
				$grand_sum_in_qty+=$val['sew_in_qty'];
				$grand_sum_cut_lay_qty+=$val['cut_lay_qty'];
				$grand_sum_cut_qty+=$val['cut_qty'];
				$grand_sum_country_qty+=$po_buyer_qty;
				$grand_sum_last_month_qty+=$last_month_qty;
				
				//$grand_total_cm_lc_value+=$buyer_wise_all_info[$buyer_id]['sew_cm_lc'];
				//$grand_total_cm_lc_value_export+=$buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'];
						
			}
								
			$summaryHTML.='</table>
			<table style="width:'.$sum_width.'px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<tfoot>
					<tr>
						<th class="alignment_css" width="30">&nbsp;</th>
						<th class="alignment_css" width="120">Grand Total</th>
						<th class="alignment_css" width="100" align="right">'.number_format($grand_sum_country_qty,0).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_cut_lay_qty,0).'</th>
						<th class="alignment_css" width="80" align="right"></th>
						<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_cut_qty,0).'</th>
						<th class="alignment_css" width="80" align="right"></th>
						<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_in_qty,0).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_out_qty,0).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm")),0 ).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm_lc")),0 ).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_fob")),0 ).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_finished_qty,0).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_exfact_qty,0).'</th>
						 <th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"ex_cm")),0 ).'</th>
						 <th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm_lc_export")),0 ).'</th>
						<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"ex_fob")),0 ).'</th>
					</tr>
				</tfoot>
			</table>
			</div>
		 </fieldset>
		 </div>';?>
		 <? echo $summaryHTML;
	}
	elseif($report_type==4) //show 2
	{
		$company= str_replace("'","", $cbo_working_company_id);
		$floor= str_replace("'","", $cbo_floor);
		$floor_name="";
		foreach(explode(",", $floor) as $v ) 
		{
			if($floor_name)$floor_name.=','.$floor_library[$v];else $floor_name=$floor_library[$v];
		}
		$company_name="";
		foreach(explode(",", $company) as $v )
		{
			if($company_name)$company_name.=','.$company_library[$v];else $company_name=$company_library[$v];
		}
		$all_companies_id=implode(",",$company_library);

		$current_month=date("m",strtotime($start_date));
		//$current_month=date('m');
		$current_year=date("Y",strtotime($start_date));
		$lastmonth=$current_month-1;
		if($current_month==1)
		{
			$lastmonth2=12;
			$current_year2=$current_year-1;
		}

		$firstdate= "01-".$lastmonth."-".$current_year ;
		// $lastdateofmonth=date('t',$lastmonth);// 	
		$lastdateofmonth=cal_days_in_month(CAL_GREGORIAN, $lastmonth, $current_year); 
		$lastdateofmonth2=cal_days_in_month(CAL_GREGORIAN, $lastmonth2, $current_year2);
		$lastdate=$lastdateofmonth."-".$lastmonth."-".$current_year ;
		$lastdate2=$lastdateofmonth2."-".$lastmonth2."-".$current_year2 ;
		//echo $firstdate ."==".$lastdate ;
					
		if($db_type==0)
		{
			$firstdate=change_date_format($firstdate,"yyyy-mm-dd","");
			$lastdate=change_date_format($lastdate,"yyyy-mm-dd","");
			$lastdate2=change_date_format($lastdate2,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$firstdate=change_date_format($firstdate,"","",1);
			$lastdate=change_date_format($lastdate,"","",1);
			$lastdate2=change_date_format($lastdate2,"","",1);
		}
		$sum_prod_date_cond=" and c.production_date between '$firstdate' and '$lastdate'";
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		if($current_month==1) $sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		$sum_width=1040;
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";
		 
		ob_start(); 
		$summaryHTML = "";
		?>
		<style type="text/css">
			.alignment_css
			{
				word-wrap: break-word;
				word-break: break-all;
			}
		</style>

		<? $summaryHTML.='<div id="buyer_wise_summary"  style="width:'.($sum_width+30).'px;  ruby-align:center" >
		<fieldset style="width:'.($sum_width+30).'px;">
			<table width="'.$sum_width.'"  cellspacing="0">
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none; font-size:16px; font-weight:bold">
					'.$company_names.'
					<br>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
					'."Date: ". change_date_format($start_date).' To '. change_date_format($end_date).'
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center"  > <strong>Buyer wise summary </strong></td>
				</tr>
				  
			</table>
			<br />	
			<table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<!-- <tr>
						<td colspan="16"></td>
						<td colspan="2" align="center"><b>Waiting For Shipment</b></td>
					</tr> -->
					<tr>
						<th class="alignment_css" width="30">SL</th>
						<th class="alignment_css" width="120">Buyer</th>
						<th class="alignment_css" width="100">PO Qty</th>
						<th class="alignment_css" width="120">Previous sewing output</th>
						<th class="alignment_css" width="120">Current Sewing Output</th>

						<th class="alignment_css" width="120">Current Export Pcs</th>
						<th class="alignment_css" width="120">Export Pending[Pcs]</th>
						<th class="alignment_css" width="120">Current Export FOB Value</th>
					</tr>
				</thead>
			</table>
			<div style="width: '.($sum_width+20).'px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
				<table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">';

				$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

				$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
			
                // =========================================================== PO and Production Data ========================================================

				$sql = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,b.po_number,b.id as po_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				sum(CASE WHEN c.production_type=5 THEN c.production_quantity ELSE 0 END)  as sew_out_qty
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c
				WHERE  a.id=b.job_id  and c.po_break_down_id=b.id and c.production_type in(5)  and  b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond group by a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.client_id,b.po_number,b.id,c.production_date,b.po_quantity ,a.total_set_qnty,b.unit_price order by b.id";
				// echo $sql;//die();	
				$sum_result=sql_select($sql);

				foreach($sum_result as $row)
				{
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];					
					$buyer_data_arr[$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')];	
					$buyer_data_arr[$row[csf('buyer_name')]]['unit_price']=$row[csf('unit_price')];				
					// $buyer_data_arr[$row[csf('buyer_name')]]['fob_value']=$row[csf('unit_price')];				
					
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	

					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')];
					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					$prod_dtls_data_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
				
				} 
				// echo "<pre>";print_r($prod_dtls_data_arr);die;
				
                    //echo $po_cond_for_in;
					// $poIds=implode(",",$tmp_po);
					// $poIds=chop($poIds,','); $po_cond_for_in="";// $order_cond1=""; 
					// $po_ids=count(array_unique(explode(",",$poIds)));
					// if($db_type==2 && $po_ids>1000)
					// {
					// 	$po_cond_for_in=" and (";
					// 	$poIdsArr=array_chunk(explode(",",$poIds),999);
					// 	foreach($poIdsArr as $ids)
					// 	{
					// 		$ids=implode(",",$ids);
					// 		$po_cond_for_in.=" b.id in($ids) or"; 
					// 	}
					// 	$po_cond_for_in=chop($po_cond_for_in,'or ');
					// 	$po_cond_for_in.=")";
					// }
					// else
					// {
					// 	$poIds=implode(",",array_unique(explode(",",$poIds)));
					// 	$po_cond_for_in=" and b.id in($poIds)";
					// }
				$all_po_id=implode(',',$prod_dtls_data_po_arr);

                //  ============================================================== Previous Sewing Output Total Qty ==================================

				$sql_previous_sewing_output = "SELECT a.job_no,a.buyer_name,b.po_number,b.id as po_id,c.production_date as prod_date,
				sum(CASE WHEN c.production_type=5 THEN c.production_quantity ELSE 0 END)  as sew_out_qty
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c
				WHERE  a.id=b.job_id  and c.po_break_down_id=b.id and c.production_type in(5)  and  b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and c.production_date < $prod_to_date_comd and c.po_break_down_id in ($all_po_id) group by a.job_no,a.buyer_name,b.po_number,b.id,c.production_date order by b.id";
                
				// echo $sql_previous_sewing_output;

				$sum_result=sql_select($sql_previous_sewing_output);
				$buyer_wise_prev_qty_arr=array();
				$po_wise_prev_qty_arr=array();
				foreach($sum_result as $row)
				{
					$po_wise_prev_qty_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]+=$row[csf('sew_out_qty')];
					$buyer_wise_prev_qty_arr[$row[csf('buyer_name')]]+=$row[csf('sew_out_qty')];
				}  
                // echo "<pre>";
				// print_r($po_wise_prev_qty_arr);

				// ================================================================= Current Ex-Factory Qnty ====================================================

				$exfact_qty_sql="SELECT a.po_break_down_id          AS po_id,
				b.buyer_name,c.unit_price,
				SUM (case  when a.entry_form != 85 then a.ex_factory_qnty else 0 end) - SUM (case  when a.entry_form = 85 then a.ex_factory_qnty else 0 end) AS ex_factory_qnty
		        FROM pro_ex_factory_mst a, wo_po_details_master b, wo_po_break_down c
		        WHERE a.entry_form != 85 AND a.status_active = 1 AND b.id=c.job_id AND a.po_break_down_id=c.id AND a.po_break_down_id in ($all_po_id)
	            GROUP BY  a.po_break_down_id , b.buyer_name, c.unit_price";
				// echo $exfact_qty_sql;

				$exfact_qty_sql_res=sql_select($exfact_qty_sql);
				$buyer_exfact_qty_arr=array();
				$buyer_exfact_qty_total_arr=array();
				$po_exfact_qty_total_arr=array();
				$po_exfact_qty_arr=array();
				foreach($exfact_qty_sql_res as $row)
				{
					$po_exfact_qty_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
					$po_exfact_qty_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]['ex_factory_fob']+=$row[csf('ex_factory_qnty')]*$row[csf('unit_price')];
					$buyer_exfact_qty_arr[$row[csf('buyer_name')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
					$buyer_exfact_qty_arr[$row[csf('buyer_name')]]['ex_factory_fob']+=$row[csf('ex_factory_qnty')]*$row[csf('unit_price')];
				} 
                // echo "<pre>";
				// print_r($po_exfact_qty_arr);

			
			  //  =========================================== SUMMERY PART START ============================================================================
            $k=1;
			foreach($buyer_data_arr as $buyer_id=>$val)
			{
				if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

                $current_sewing_output=$val['sew_out_qty'];
				$buyer_previous_sewing_out=$buyer_wise_prev_qty_arr[$buyer_id];
				$ex_fact_qty=$buyer_exfact_qty_arr[$buyer_id]['ex_factory_qnty']; 

				$total_sewing_output=$buyer_previous_sewing_out+$current_sewing_output;
				$export_pending_pcs = $total_sewing_output - $ex_fact_qty;
				$ex_fac_fob=$buyer_exfact_qty_arr[$buyer_id]['ex_factory_fob'];


				// if($total_sewing_output > 0)
				// {
					$summaryHTML.='<tr bgcolor="'.$bgcolor.'">
						<td width="30">'.$k.'</td>
						<td width="120"><div style="word-break:break-all">'.$buyer_short_library[$buyer_id].'</div></td>
						<td width="100" align="right"><p>'.$val['po_quantity'].'</p></td>
						<td width="120" align="right"><p>'.$buyer_previous_sewing_out.'</p></td>
						<td width="120" align="right" style=""><p>'.number_format($current_sewing_output,0).'</p></td>
						<td width="120" align="right" title="'.$ex_fact_qty.'"><p>'.number_format($ex_fact_qty,0).'</p></td>
						<td width="120" align="right" style="" title="'.$export_pending_pcs.'"><p>'.number_format($export_pending_pcs,0).'</p></td>
						<td width="120" align="right" title="'.$ex_fac_fob.'"><p>'.number_format($ex_fac_fob,2).'</p></td>
					</tr>';
					
					$k++;
					
					$grand_po_qnty_pcs+=$val['po_quantity'];
					$grand_last_previous_sewing_out+=$buyer_previous_sewing_out;
					$grand_sum_out_qty+=$current_sewing_output;
					$grand_sum_current_ex_fact_qty+=$ex_fact_qty;
					$grand_sum_export_pending_pcs+=$export_pending_pcs;
					$grand_sum_ex_fac_fob_pcs+=$ex_fac_fob;

			    // }
			}
			$summaryHTML.='</table>
			<table style="width:'.$sum_width.'px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<tfoot>
					<tr>
					<th class="alignment_css" width="30">&nbsp;</th>
					<th class="alignment_css" width="120">Grand Total</th>
					<th class="alignment_css" width="100" align="right">'.number_format($grand_po_qnty_pcs,0).'</th>
					<th class="alignment_css" width="120" align="right">'.number_format($grand_last_previous_sewing_out,0).'</th>
					<th class="alignment_css" width="120" align="right">'.number_format($grand_sum_out_qty,0).'</th>
					<th class="alignment_css" width="120" align="right">'.number_format($grand_sum_current_ex_fact_qty,0).'</th>
					<th class="alignment_css" width="120" align="right">'.number_format($grand_sum_export_pending_pcs,0).'</th>
					<th class="alignment_css" width="120" align="right">'.number_format($grand_sum_ex_fac_fob_pcs,2).'</th>
					</tr>
				</tfoot>
			</table>
		</div>
	 </fieldset>
	 </div>';?>
	 <? echo $summaryHTML;?>
	 <? $dtls_width=1270; ?>
		<br/>
		 <!-- =========================================== DETAILS PART START ================================================================================  -->

		<div style="width:<? echo $dtls_width+25;?>px;">
		<table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
		   <caption><strong>Detail Report</strong></caption>
			<thead>
				<tr>
					<th class="alignment_css" width="30">SL</th>
					<th class="alignment_css" width="80">Company</th>
					<th class="alignment_css" width="100">Buyer</th>                                                      
					<th class="alignment_css" width="100">PO No</th>   

					<th class="alignment_css" width="100"><p>PO Qty</p></th>
					<th class="alignment_css" width="80">Previous<br>Sewing Out</th>
					<th class="alignment_css" width="80">Current <br>Sewing Output</th>
					<th class="alignment_css" width="80">Total <br>Sewing Out</th>

					<th class="alignment_css" width="80">FOB Price</th>
					<th class="alignment_css" width="80">Current Sewing <br>FOB Value</th>
					<th class="alignment_css" width="100">Previous Sewing <br>FOB Value </th>
					<th class="alignment_css" width="80">Total Sew FOB Value</th> 

					<th class="alignment_css" width="100">Current Ex-Factory <br> Qnty. [Pcs]</th>
					<th class="alignment_css" width="100">Current Ex-Factory <br> FOB Value</th>
					<th class="alignment_css" width="80">Ex-Factory <br>Pending [Pcs]</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $dtls_width+20;?>px; max-height:245px; float:left; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="tableBody">
			<?
			$j=1;$grand_in_qty=$grand_out_qty=$grand_poly_qty=$grand_exfact_qty=$grand_country_qty=$grand_country_qty_pcs=0;
		
			$cm_dzn_rate=0;
            foreach($prod_dtls_data_arr as $buyer_id=>$buyer_data)
			{
				// echo"<pre>";
				// print_r($buyer_id);die;
				foreach($buyer_data as $po_id=>$val)
				{
					// echo"<pre>";
				    // print_r($po_id);die;
					$jobs= $val['job_no'];
					if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
					
					$total_sewing_out=$val['sew_out_qty']+$previous_sewing_out;
					$unit_price=$val['unit_price'];
					$sewing_fob=$val['sew_out_qty']*$unit_price;
					$previous_sewing_out=$po_wise_prev_qty_arr[$buyer_id][$po_id];
					// $previous_sewing_out=$buyer_id.'eee'.$po_id;
					$previous_sewing_fob=$previous_sewing_out*$unit_price;
					$total_sewing_fob_value=$sewing_fob+$previous_sewing_fob;
					$ex_fact_current_qty=$po_exfact_qty_arr[$buyer_id][$po_id]['ex_factory_qnty'];
					$current_ex_factory_fob_value=$po_exfact_qty_arr[$buyer_id][$po_id]['ex_factory_qnty']*$unit_price;
					$ex_factory_pending_pcs=$total_sewing_out-$ex_fact_current_qty;
					
					// if($total_sewing_out>0)
					// {
							?>
							
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>">
									<td class="alignment_css" width="30"><? echo $j;?></td>
									<td class="alignment_css" width="80"><p><? echo $company_arr[$val['company_name']]; ?></p></td>
									<td class="alignment_css" width="100"><div style="word-break:break-all"><? echo  $buyer_short_library[$val['buyer_name']];  ?></div></td>
									<td class="alignment_css" width="100"><div style="word-break:break-all"><? echo implode(PHP_EOL, str_split($val["po_number"],15));echo $sub_self=($country_id=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>

									<td class="alignment_css" width="100"  align="right" title="<? echo 'S '.$set_ratio;?>"><p><? echo $val['po_quantity'];//$po_country_qty;?></p></td>
									<td class="alignment_css" width="80" align="right"><p><? echo $previous_sewing_out;?></p></td>
									<td class="alignment_css" width="80" style="background:green;"  align="right"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
									<td class="alignment_css" width="80" align="right"><p> <? echo number_format($total_sewing_out,0);?></p></td>

								
									<td class="alignment_css" width="80" align="right"><p><? echo implode(PHP_EOL, str_split($unit_price,12));?></p></td>
									<td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_fob,0);?></p></td>
									<td class="alignment_css" width="100" align="right"><p><? echo number_format($previous_sewing_fob,0);?></p></td>
									<td class="alignment_css" width="80" align="right"><p><? echo number_format($total_sewing_fob_value,0);?></p></td>

									<td class="alignment_css" width="100" style="background:green;" align="right"><p><? echo $ex_fact_current_qty;?></p></td>
									<td class="alignment_css" width="100" align="right"><p><? echo number_format($current_ex_factory_fob_value,2);?></p></td>
									<td class="alignment_css" width="80" style="background:green;" align="right"><p><? echo number_format($ex_factory_pending_pcs,0);?></p></td>
								</tr>
							<?
							$j++;

						    $grand_previous_sewing_out+=$previous_sewing_out;
							$grand_out_qty+=$val['sew_out_qty'];
							$grand_total_sewing_out+=$total_sewing_out;
							$gr_sewing_fob+=$sewing_fob;

							$grand_previous_sewing_fob+=$previous_sewing_fob;
							$grand_total_sewing_fob_value+=$total_sewing_fob_value;
							$grand_current_ex_factory_fob_value+=$current_ex_factory_fob_value;
							$grand_exfact_qty+=$ex_fact_current_qty;
							$grand_ex_factory_pending_pcs+=$ex_factory_pending_pcs;
							$gr_sewing_cm_lc += $cm_lc_value;
						// }
				}

			}
			
			?>
			</table>
		</div>
		<div style="width:<? echo $dtls_width+20;?>px;">
			<table style="width:<? echo $dtls_width;?>px" class="tbl_bottom" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all" align="left">
				<tr>
					<td class="alignment_css" width="30">&nbsp;</td>
					<td class="alignment_css" width="80"></td>
					<td class="alignment_css" width="100">&nbsp;</td>
					<td class="alignment_css" width="100">Grand Total:</td>

					<td class="alignment_css" width="100" align="right"><? //echo number_format($grand_country_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_previous_sewing_out,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_out_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"> <? echo number_format($grand_total_sewing_out,0); ?> </td>

					<td class="alignment_css" width="80" align="right"> </td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_fob,0); ?></td>
					<td class="alignment_css" width="100" align="right"><? echo number_format($grand_previous_sewing_fob,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_total_sewing_fob_value,0); ?></td>

					<td class="alignment_css" width="100" align="right"><? echo number_format($grand_exfact_qty,0); ?></td>
					<td class="alignment_css" width="100" align="right"><? echo number_format($grand_current_ex_factory_fob_value,2); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_ex_factory_pending_pcs,0); ?></td>
				</tr>
			</table>
			</div>
		</div>
		<?		
	}
	// ================================================================== Show 2 End Here=============================================================================
	else //show
	{
		$company= str_replace("'","", $cbo_working_company_id);
		$floor= str_replace("'","", $cbo_floor);
		$floor_name="";
		foreach(explode(",", $floor) as $v ) 
		{
			if($floor_name)$floor_name.=','.$floor_library[$v];else $floor_name=$floor_library[$v];
		}
		$company_name="";
		foreach(explode(",", $company) as $v )
		{
			if($company_name)$company_name.=','.$company_library[$v];else $company_name=$company_library[$v];
		}
		$all_companies_id=implode(",",$company_library);

		$current_month=date("m",strtotime($start_date));
		//$current_month=date('m');
		$current_year=date("Y",strtotime($start_date));
		$lastmonth=$current_month-1;
		if($current_month==1)
		{
			$lastmonth2=12;
			$current_year2=$current_year-1;
		}

		$firstdate= "01-".$lastmonth."-".$current_year ;
		// $lastdateofmonth=date('t',$lastmonth);// 	
		$lastdateofmonth=cal_days_in_month(CAL_GREGORIAN, $lastmonth, $current_year); 
		$lastdateofmonth2=cal_days_in_month(CAL_GREGORIAN, $lastmonth2, $current_year2);
		$lastdate=$lastdateofmonth."-".$lastmonth."-".$current_year ;
		$lastdate2=$lastdateofmonth2."-".$lastmonth2."-".$current_year2 ;
		//echo $firstdate ."==".$lastdate ;
					
		if($db_type==0)
		{
			$firstdate=change_date_format($firstdate,"yyyy-mm-dd","");
			$lastdate=change_date_format($lastdate,"yyyy-mm-dd","");
			$lastdate2=change_date_format($lastdate2,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$firstdate=change_date_format($firstdate,"","",1);
			$lastdate=change_date_format($lastdate,"","",1);
			$lastdate2=change_date_format($lastdate2,"","",1);
		}
		$sum_prod_date_cond=" and c.production_date between '$firstdate' and '$lastdate'";
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		if($current_month==1) $sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		$sum_width=1370;
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";
		 
		ob_start(); 
		$summaryHTML = "";
		?>
		<style type="text/css">
			.alignment_css
			{
				word-wrap: break-word;
				word-break: break-all;
			}
		</style>

		<? $summaryHTML.='<div id="buyer_wise_summary"  style="width:'.($sum_width+30).'px;  ruby-align:center" >
		<fieldset style="width:'.($sum_width+30).'px;">
			<table width="'.$sum_width.'"  cellspacing="0">
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none; font-size:16px; font-weight:bold">
					'.$company_names.'
					<br>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
					'."Date: ". change_date_format($start_date).' To '. change_date_format($end_date).'
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center"  > <strong>Buyer wise summary </strong></td>
				</tr>
				  
			</table>
			<br />	
			<table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<!-- <tr>
						<td colspan="16"></td>
						<td colspan="2" align="center"><b>Waiting For Shipment</b></td>
					</tr> -->
					<tr>
						<th class="alignment_css" width="30">SL</th>
						<th class="alignment_css" width="120">Buyer</th>
						<th class="alignment_css" width="100">PO Qty</th>
						<th class="alignment_css" width="80">Cut Qty</th>
						<th class="alignment_css" width="80">Cut Qty%</th>
						<th class="alignment_css" width="80">Cutting QC</th>
						<th class="alignment_css" width="80">Cutting QC%</th>
						<th class="alignment_css" width="80">Sewing<br> Input</th>
						<th class="alignment_css" width="80">Sewing<br> Output</th>
						<th class="alignment_css" width="80">Sewing CM <br>Value BOM</th>
						<th class="alignment_css" width="80">Sewing CM <br>Value LC</th>
						<th class="alignment_css" width="80">Sewing FOB<br> Value</th>                           
						<th class="alignment_css" width="80">Finishing</th> 
						<th class="alignment_css" width="80">Export Pcs</th>
						<th class="alignment_css" width="80">Export CM <br>Value BOM</th>
						<th class="alignment_css" width="80">Export CM <br>Value LC</th>
						<th class="alignment_css" width="80">Export FOB<br> Value</th>
					</tr>
				</thead>
			</table>
			<div style="width: '.($sum_width+20).'px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
				<table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">';
				//cut and lay qty.....................................
				$buyer_data_arr=array();
				$lc_company_arr=array();
				$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
				$sql_cut_lay = "SELECT b.po_total_price,(b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(d.size_qty) as prod_qty
				FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
				WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_cut_lay $floor_cond $prod_date_cond7 order by b.id,d.country_id ";
				// echo $sql_cut_lay;die;
				$cut_lay_result_arr=sql_select($sql_cut_lay);
				foreach($cut_lay_result_arr as $rows)
				{
					$buyer_data_arr[$rows[csf('buyer_name')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
					$buyer_data_arr[$rows[csf('buyer_name')]]['job_no'] = $rows[csf('job_no')];
					
					$prod_dtls_data_job_arr[$rows[csf('job_no')]] =$rows[csf('job_no')];	
					$prod_dtls_data_po_arr[$rows[csf('po_id')]] =$rows[csf('po_id')];	
					$prod_dtls_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['style']=$rows[csf('style_ref_no')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_name']=$rows[csf('buyer_name')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_client']=$rows[csf('buyer_client')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['po_number']=$rows[csf('po_number')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['ratio']=$rows[csf('ratio')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['job_no']=$rows[csf('job_no')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['unit_price']=$rows[csf('unit_price')];
					$total_po_amount_arr[$rows[csf('job_no')]]+=$rows[csf('po_total_price')];
					
					$tmp_count=$rows[csf('country_id')];
					$tmp_po[$rows[csf('po_id')]]=$rows[csf('po_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
					//$cut_lay_qty_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
	
				}
				// die('GO TO HELL!');
				// echo "<pre>";
				// print_r($prod_dtls_data_po_arr);die;
				// echo "</pre>";
				//cut and lay qty.....................................	

				$sql_prod_sum = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty,
				(CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty,
				(CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty,
				(CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty,
				(CASE WHEN c.production_type=11 and d.production_type=11 THEN d.production_qnty ELSE 0 END)  as poly_qty,
				(CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.job_no=b.job_no_mst  and c.po_break_down_id=b.id and c.id=d.mst_id  and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,4,5,11,8) and d.production_type=c.production_type  and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)  $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";//and c.po_break_down_id=e.po_break_down_id and a.job_no=e.job_no_mst
				
				$subcon_sql_prod="SELECT  c.rate as unit_price,b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client, b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio,  
				(CASE WHEN a.production_type=1 and d.production_type=1 THEN d.prod_qnty ELSE 0 END)  as cut_qty,
				(CASE WHEN a.production_type=7  and d.production_type=7 THEN d.prod_qnty ELSE 0 END)  as sew_in_qty, 
				(CASE WHEN a.production_type=2  and d.production_type=2 THEN d.prod_qnty ELSE 0 END)  as sew_out_qty,
				(CASE WHEN a.production_type=5 and d.production_type=5 THEN d.prod_qnty ELSE 0 END)  as poly_qty,
				(CASE WHEN a.production_type=4 and d.production_type=4  THEN d.prod_qnty ELSE 0 END)  as finish_qty
				from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(1,2,4,5,7) and d.production_type in(1,2,4,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";

				// echo $sql_prod_sum;die();	
				$sum_result=sql_select($sql_prod_sum);
				// die('GO TO HELL!');
				// echo $subcon_sql_prod;die();
				//$po_id='';	$country_id='';$buyer_ids='';
				foreach($sum_result as $row)
				{
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['cut_qty']+=$row[csf('cut_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['poly_qty']+=$row[csf('poly_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['finish_qty']+=$row[csf('finish_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['buyer_client']=$row[csf('buyer_client')];
					$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
					
					$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];	
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['cut_qty']+=$row[csf('cut_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['poly_qty']+=$row[csf('poly_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['finish_qty']+=$row[csf('finish_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];

					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];	
				} 
				// die('GO TO HELL!');	

				$sum_result_subcon=sql_select($subcon_sql_prod);
				foreach($sum_result_subcon as $row)
				{
					$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];	
					$buyer_data_arr[$row[csf('buyer_name')]]['cut_qty']+=$row[csf('cut_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['poly_qty']+=$row[csf('poly_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['finish_qty']+=$row[csf('finish_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['buyer_client']=$row[csf('buyer_client')];
					$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
					
					$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];	
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['cut_qty']+=$row[csf('cut_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['poly_qty']+=$row[csf('poly_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['finish_qty']+=$row[csf('finish_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];	
					$all_subcon_po_id_arrs[$row[csf('po_id')]]=$row[csf('po_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				} 


			// die('GO TO HELL!');	
			$location_cond_ex = str_replace("c.location", "c.delivery_location_id", $location_cond);
			$floor_cond2=str_replace("c.floor_id", "c.delivery_floor_id", $floor_cond);
			$sql_prod_exf = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.job_no,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty
				 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
			WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_ex $floor_cond2  $prod_date_cond2 order by b.id,d.country_id ";

			$subcon_sql_ex="SELECT  c.rate as unit_price, b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0       $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";

			//echo $sql_prod_exf;die;
			//echo $subcon_sql_ex;die;

			$result_prod_exf=sql_select($sql_prod_exf);
			$result_prod_exf_subcon=sql_select($subcon_sql_ex);
			$po_no_id="";$buyer_ids2='';
			$po_country_id='';
			foreach($result_prod_exf as $row)
			{
				$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				$po_ex_fact_dtls_qty[$row[csf('po_id')]]['prod_qty']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_fac_fob']+=$row[csf('prod_qty')]*$row[csf('unit_price')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
				
				$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];		
				$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];		
				$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
				if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
				if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
				$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
			}
			// echo "<pre>";
			// print($po_ex_fact_sum_qty);
			// echo "</pre>";
			foreach($result_prod_exf_subcon as $row)
			{
				$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				$po_ex_fact_dtls_qty[$row[csf('po_id')]]['prod_qty']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
				$buyer_data_arr[$row[csf('buyer_name')]]['ex_fac_fob']+=$row[csf('prod_qty')]*$row[csf('unit_price')];
				$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
				
				$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
				$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];	
				$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];	
				//$prod_dtls_data_job_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
				$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
				if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
				if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
				$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
			}

			 $po_country_id=implode(",",array_unique(explode(",",$po_country_id)));
			 $po_no_id=implode(",",array_unique(explode(",",$po_no_id)));

			//echo $po_cond_for_in;
			$poIds=implode(",",$tmp_po);
			$poIds=chop($poIds,','); $po_cond_for_in="";// $order_cond1=""; 
			$po_ids=count(array_unique(explode(",",$poIds)));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
			}
			else
			{
				$poIds=implode(",",array_unique(explode(",",$poIds)));
				$po_cond_for_in=" and b.id in($poIds)";
			}
			
			$lc_company = implode(",", $lc_company_arr);
			// Country
			$country_Id=implode(",",$tmp_count);
			$poIds_c=chop($country_Id,','); $po_cond_for_in2="";// $order_cond1=""; 
			$po_ids_c=count(array_unique(explode(",",$country_Id)));
			if($db_type==2 && $po_ids_c>1000)
			{
				$po_cond_for_in2=" and (";
				$poIdsArr_c=array_chunk(explode(",",$poIds_c),999);
				foreach($poIdsArr_c as $ids)
				{
					$ids_c=implode(",",$ids);
					$po_cond_for_in2.=" c.country_id in($ids_c) or"; 
				}
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
			}
			else
			{
				$poIds_c=implode(",",array_unique(explode(",",$poIds_c)));
				$po_cond_for_in2=" and c.country_id in($poIds_c)";
			}
			
			$sql_prod_exf_last = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,
			(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty,a.client_id as buyer_client
			FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
			WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $sum_prod_date_cond2 order by b.id,d.country_id ";
			// echo $sql_prod_exf_last;die();
			$result_prod_exf_last=sql_select($sql_prod_exf_last);
			foreach($result_prod_exf_last as $row)
			{
				$po_ex_fact_sum_qty_lastmon[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
				//$po_ex_fact_sum_qty_lastmon[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
			}

			$sql_color="SELECT a.buyer_name,b.po_quantity as po_qty,b.id as po_id, a.client_id as buyer_client from   wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,2,3)  $po_cond_for_in   $buyer_id_cond  ";
			$po_country_sum_qty=array();
			$po_country_dtls_qty=array();
			$sql_result_color=sql_select($sql_color);
			foreach($sql_result_color as $row)
			{
				$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
				$po_country_dtls_qty[$row[csf('po_id')]]['po_qty']+=$row[csf('po_qty')];
				$summ+=$row[csf('po_qty')];
				 //echo $row[csf('po_qty')]."=";
			}
			$sub_po_cond=implode(",",$all_subcon_po_id_arrs);
			$sql_color_subcon ="SELECT a.party_id as buyer_name ,c.qnty as po_qty,b.id as po_id,'00' as country_id,b.cust_buyer as buyer_client from   subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where  a.subcon_job=b.job_no_mst and c.order_id=b.id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.id in($sub_po_cond) order by b.id ";
			foreach(sql_select($sql_color_subcon) as $row)
			{
				$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
				$po_country_dtls_qty[$row[csf('po_id')]]['po_qty']+=$row[csf('po_qty')];
				$summ+=$row[csf('po_qty')];
			}
			$target_sql=sql_select("SELECT   mst_id, target_per_hour  FROM PROD_RESOURCE_DTLS ORDER BY ID asc  ");
			$line_wise_tg=array();
			foreach($target_sql as $v)
			{
				$line_wise_tg[$v[csf("mst_id")]]=$v[csf("target_per_hour")];
			}
			
			 $k=1;$grand_sum_cut_qty=$grand_sum_in_qty=$grand_sum_out_qty=$grand_sum_poly_qty=$grand_sum_finished_qty=$grand_sum_exfact_qty=$grand_sum_country_qty=$grand_sum_last_month_qty=$grand_sum_sewfob=$grand_sum_exfob=0;
			 // echo "<pre>";
			 // print_r($buyer_data_arr);die;
			// $condition= new condition();
			 $all_jobs="";
			 foreach($prod_dtls_data_job_arr as $v)
			 {
				if($v && $v!=",")
				{
					if($all_jobs)$all_jobs.=","."'".$v."'";
					else $all_jobs="'".$v."'";
				}
			 }
			  $txt_order_id="";
			 foreach($prod_dtls_data_po_arr as $v)
			 {
				if($v && $v!=",")
				{
					if($txt_order_id)$txt_order_id.=",".$v;
					else $txt_order_id=$v;
				}
			 }

			// echo $txt_order_id; 
			$po_ids=count(explode(",",$txt_order_id));
			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_=" and (";
				$poIdsArr=array_chunk(explode(",",$txt_order_id),999);
				foreach($poIdsArr as $ids)
				{
					$ids_c=implode(",",$ids);
					$po_cond_.=" b.id in($ids_c) or"; 
				}
				$po_cond_=chop($po_cond_,'or ');
				$po_cond_.=")";
			}
			else
			{						
				$po_cond_=" and b.id in($txt_order_id)";
			}
			 // die('GO TO HELL!');	
			 
				 $po_qty=0;
			 $po_plun_cut_qty=0;
			 $total_set_qnty=0;
			 
			 $pro_sql=sql_select("SELECT  a.production_type, b.unit_price,a.id,a.po_break_down_id, a.sewing_line,sum(case WHEN a.production_type=5 then a.production_quantity else 0 end ) as sew,  sum(case WHEN a.production_type=8 then production_quantity else 0 end ) as fin  FROM pro_garments_production_mst a,wo_po_break_down b  where a.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 and  a.production_type in(5,8) $po_cond_  group by  a.production_type,  b.unit_price,  a.id, a.po_break_down_id, a.sewing_line ORDER BY a.ID asc  ");//and b.id in($txt_order_id)
			 $po_wise_tg=array();
			 foreach($pro_sql as $v)
			 {
				if($v[csf("production_type")]==5)
				{
					$po_wise_tg[$v[csf("po_break_down_id")]]["line"]=$v[csf("sewing_line")];
				}
				$po_wise_tg[$v[csf("po_break_down_id")]]["qnty"]+=$v[csf("sew")]-$v[csf("fin")];
				$po_wise_tg[$v[csf("po_break_down_id")]]["value"]+=($v[csf("sew")]-$v[csf("fin")])*$v[csf("unit_price")];
			 }

			 //print_r($po_wise_tg);die;
			 // echo $all_companies_id."<br>";
			 // echo $poIds;
			 $cm_gmt_cost_dzn_arr=array();
			 $cm_gmt_cost_dzn_arr_new=array();
			if($poIds!="")
			{
				//$cm_gmt_cost_dzn_arr=array();
				// $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_companies_id,$poIds); 
				//$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($company,$poIds); 
				// print_r($cm_gmt_cost_dzn_arr);die;
			}
			$new_arr=array_unique(explode(",", $poIds));
			$chnk_arr=array_chunk($new_arr,50);
			foreach($chnk_arr as $vals )
			{
				$p_ids=implode(",", $vals);
				//if(!empty($cm_gmt_cost_dzn_arr))
				//$cm_gmt_cost_dzn_arr.=fnc_po_wise_cm_gmt_class($company,$p_ids); 
				//else
				 $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($lc_company,$p_ids); 
				 foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
				 {
					$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"] =$vv["dzn"] ;
				 }
			}
			//print_r($cm_gmt_cost_dzn_arr_new);die;
			// die('GO TO HELL!');	
			
			/*
			|--------------------------------------------------------------------------
			| getting price quotation wise cm valu
			| start
			|--------------------------------------------------------------------------
			*/
			foreach($prod_dtls_data_arr as $po_id=>$val)
			{
				$job_no_arr[$val['job_no']]= $val['job_no'];
			}
			
			$jobNoCondition = '';
			$noOfjobNo = count($job_no_arr);
			if($db_type == 2 && $noOfjobNo > 1000)
			{
				$jobNoCondition = " and (";
				$jobNoArr = array_chunk($job_no_arr,999);
				foreach($jobNoArr as $job)
				{
					$jobNoCondition.=" c.job_no in('".implode("','",$job)."') or";
				}
				$jobNoCondition = chop($jobNoCondition,'or');
				$jobNoCondition .= ")";
			}
			else
			{
				$jobNoCondition=" and c.job_no in('".implode("','",$job_no_arr)."')";
			}
			
			//echo $jobNoCondition; die;
			//$all_job = "'".implode("','", $jobArr)."'";
			$all_job = $all_jobs;
			$quotation_qty_sql="
				SELECT
					a.id as quotation_id, a.mkt_no, a.sew_smv, a.sew_effi_percent, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.total_cost, b.costing_per_id, c.job_no
				FROM
					wo_price_quotation a,
					wo_price_quotation_costing_mst b,
					wo_po_details_master c
				WHERE
					a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 ".$jobNoCondition."
				ORDER BY
					a.id
			";
			//and c.job_no in(".$all_job."
			//echo $quotation_qty_sql; die();
			$quotation_qty_sql_res = sql_select($quotation_qty_sql);
			$quotation_qty_array = array();
			$quotation_id_array = array();
			
			//don't use
			$all_jobs_array = array();
			//don't use
			$jobs_wise_quot_array = array();
			$quot_wise_arr = array();
			foreach ($quotation_qty_sql_res as $val)
			{
				$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
				$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
				$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
				//$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
				//$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];
			
				$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
				$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];
			
				//$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
				//$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
				//$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
				//$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
				//$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
				//$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
				
				$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
				$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
				$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
				$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
				$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
				//$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
			}
			unset($quotation_qty_sql_res);
			$all_quot_id = implode(",", $quotation_id_array);
			//echo "<pre>";
			//print_r($quot_wise_arr); die;
			
			// print_r($style_wise_arr);die();
			// ===============================================================================
			$sql_fab = "
				SELECT
					a.quotation_id, sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount, b.job_no
				from
					wo_pri_quo_fabric_cost_dtls a,
					wo_po_details_master b
				where
					a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.fabric_source=2 and a.status_active=1 and b.status_active=1
				group by
					a.quotation_id, b.job_no
			";
			//echo $sql_fab; die();
			$data_array_fab=sql_select($sql_fab);
			$fab_summary_data = array();
			$fab_order_price_per_dzn = 1;
			foreach($data_array_fab as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1)
				{
					$fab_order_price_per_dzn=12;
				}
				else if($costing_per_id==2)
				{
					$fab_order_price_per_dzn=1;
				}
				else if($costing_per_id==3)
				{
					$fab_order_price_per_dzn=24;
				}
				else if($costing_per_id==4)
				{
					$fab_order_price_per_dzn=36;
				}
				else if($costing_per_id==5)
				{
					$fab_order_price_per_dzn=48;
				}
			
				$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
				$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			}
			unset($data_array_fab);
			//echo "<pre>";
			//print_r($fab_summary_data); die;
			
			// ==================================================================================
			$sql_yarn = "
				SELECT
					a.quotation_id, sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount, b.job_no 
				from
					wo_pri_quo_fab_yarn_cost_dtls a, wo_po_details_master b 
				where
					a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 
				group by
					a.quotation_id,b.job_no
			";
			//echo $sql_yarn; die();
			$data_array_yarn=sql_select($sql_yarn);
			$yarn_summary_data = array();
			foreach($data_array_yarn as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
				else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
				else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
				else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
				else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
				//$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
				// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
				//$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
			}
			unset($data_array_yarn);
			
			// ===================================================================================
			$sql_conversion = "
				SELECT
					a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active, b.body_part_id, b.fab_nature_id, b.color_type_id, b.construction, b.composition, c.job_no
				from
					wo_po_details_master c,
					wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
				where
					a.quotation_id in(".$all_quot_id.") and a.quotation_id=c.quotation_id and a.status_active=1
			";
			//echo $sql_conversion; die();
			$data_array_conversion=sql_select($sql_conversion);
			$conv_order_price_per_dzn = 1;
			$conv_summary_data = array();
			$conversion_cost_arr = array();
			foreach($data_array_conversion as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$conv_order_price_per_dzn=12;}
				else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
				else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
				else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
				else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
				$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];
				$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
				$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			}
			unset($data_array_conversion);
			//echo "<pre>";
			//print_r($conversion_cost_arr); die();
			
			if($db_type==0)
			{
				$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
				from wo_price_quotation_costing_mst a,wo_po_details_master b
				where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
			}
			if($db_type==2)
			{
				$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
				from wo_price_quotation_costing_mst a,wo_po_details_master b
				where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
			}
			//echo $sql; die();
			$data_array=sql_select($sql);
			foreach( $data_array as $row )
			{
				//$sl=$sl+1;
				if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
				else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//used
				$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//used
				$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
				
				$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
				$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				
				/*
				//$price_dzn=$row[csf("confirm_price_dzn")];
				//$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
				$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
				$summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
				$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
				$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
				//$row[csf("commission")]
				$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
				$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
				$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
				$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
				$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
				$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
				$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
				$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
				$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
				$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
				$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
				$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];
				$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
				$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
				$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
				//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
				$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
				$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
				$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
				//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
				$net_value_dzn=$row[csf("price_with_commn_dzn")];
				$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
				$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;
				//yarn_amount_total_value
				$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
				//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
				$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
				$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
				$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
				$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
				$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
				$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
				//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
				$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
				$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
				$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
				$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;
			
				//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
				$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
				$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
				$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
				$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
				$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
				$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
				$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
				$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
				*/
			}
			unset($data_array);
			//echo "<pre>";
			//print_r($summary_data);
			//die();
			
			//======================================================================
			$sql_commi = "
				SELECT
					a.id,a.quotation_id, a.particulars_id, a.commission_base_id, a.commision_rate, a.commission_amount, a.status_active, b.job_no
				from
					wo_pri_quo_commiss_cost_dtls a, wo_po_details_master b
				where
					a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.status_active=1 and a.commission_amount>0 and b.status_active=1
			";
			//echo $sql_commi; die();
			$result_commi=sql_select($sql_commi);
			$CommiData_foreign_cost=0;
			//$CommiData_lc_cost=0;
			//$foreign_dzn_commission_amount=0;
			//$local_dzn_commission_amount=0;
			$CommiData_foreign_quot_cost_arr = array();
			$commision_local_quot_cost_arr = array();
			foreach($result_commi as $row)
			{
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			
				if($row[csf("particulars_id")]==1) //Foreign
				{
					$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					//$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
					$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				}
				else
				{
					//$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					//$local_dzn_commission_amount+=$row[csf("commission_amount")];
					$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
				}
			}
			unset($result_commi);
			//echo "<pre>su..re";
			//print_r($CommiData_foreign_quot_cost_arr); die();
			
			//=====================================================================================
			$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
			//echo $sql_comm; die();
			$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
			// $summary_data['comm_cost_dzn']=0;
			// $summary_data['comm_cost_total_value']=0;
			$result_comm=sql_select($sql_comm);
			$commer_lc_cost = array();
			$commer_lc_cost_quot_arr = array();
			//$commer_without_lc_cost = array();
			foreach($result_comm as $row)
			{
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
				//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				//$comm_amtPri=$row[csf('amount')];
				//$item_id=$row[csf('item_id')];
				if($row[csf('item_id')] == 1)//LC
				{
					$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				}
				/*
				else
				{
					$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				}
				*/
			}
			unset($result_comm);
			//echo "<pre>";
			//print_r($commer_lc_cost_quot_arr); die();
			
			/*
			|--------------------------------------------------------------------------
			| getting price quotation wise cm valu
			| end
			|--------------------------------------------------------------------------
			*/

			$buyer_wise_all_info=array();
			foreach($prod_dtls_data_arr as $po_id=>$val)
			{
				$jobs= $val['job_no'];
				$set_ratio=$val['ratio'];
				$cm_dzn_rate=$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"];
				$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
				$cnty_qnty=$po_country_qty*$set_ratio;
				$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
				$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
				
				$unit_price=$val['unit_price'];
				$sewing_cm=($val['sew_out_qty']*$cm_dzn_rate)/12;
				$sewing_fob=$val['sew_out_qty']*$unit_price;

				$exfac_cm=($ex_fact_country_qty*$cm_dzn_rate)/12;
				$exfac_fob=$ex_fact_country_qty*$unit_price;

				$buyer_wise_all_info[$val['buyer_name']]['sew_cm']+=$sewing_cm;
				$buyer_wise_all_info[$val['buyer_name']]['sew_fob']+=$sewing_fob;
				$buyer_wise_all_info[$val['buyer_name']]['ex_cm']+=$exfac_cm;
				$buyer_wise_all_info[$val['buyer_name']]['ex_fob']+=$exfac_fob;
				$target=$line_wise_tg[$po_wise_tg[$po_id]["line"]];
				$buyer_wise_all_info[$val['buyer_name']]['target']+=$target;
				$buyer_wise_all_info[$val['buyer_name']]['inhand_qty']+=$po_wise_tg[$po_id]["qnty"];
				$buyer_wise_all_info[$val['buyer_name']]['inhand_val']+=$po_wise_tg[$po_id]["value"];
				//echo $po_id.'ins'.$summary_data2[$jobs][$po_id][inspection_job].'cu'.$summary_data2[$jobs][$po_id][currier_pre_cost_job].' cer'.$summary_data2[$jobs][$po_id][certificate_pre_cost_job].' '.$summary_data2[$jobs][$po_id][commission_job];

				//echo $po_id."total_btb= $total_btb  lab=".$summary_data2[$jobs][$po_id][lab_test_job]." trims=".$summary_data2[$jobs][$po_id][trims_cost_job]." comm cost ".$summary_data2[$jobs][$po_id][comm_cost_job] ." studio".$summary_data[$jobs][studio_cost_job];
			}
			
			foreach($prod_dtls_data_arr as $po_id=>$row)
			{
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| start
				|--------------------------------------------------------------------------
				*/

				$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row['job_no']][101]['conv_amount_total_value']*1;
				$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row['job_no']][30]['conv_amount_total_value']*1;
				$tot_aop_process_amount 		= $conversion_cost_arr[$row['job_no']][35]['conv_amount_total_value']*1;
				
				foreach($style_wise_arr as $style_key=>$val)
				{
					$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
					$total_quot_qty+=$val[('qty')];
					$total_quot_pcs_qty+=$val[('qty_pcs')];
					$total_sew_smv+=$val[('sew_smv')];
					$total_quot_amount+=$total_cost;
					$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
				}
				$total_quot_amount_cal = $style_wise_arr[$row['job_no']]['qty']*$style_wise_arr[$row['job_no']]['final_cost_pcs'];
				$tot_cm_for_fab_cost=$summary_data[$row['job_no']]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
				$commision_quot_local=$commision_local_quot_cost_arr[$row['job_no']];
				$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row['job_no']]+$commer_lc_cost_quot_arr[$row['job_no']]+$freight_cost_data[$row['job_no']]['freight_total_value']);
				$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
				$tot_inspect_cour_certi_cost=$summary_data[$row['job_no']]['inspection_total_value']+$summary_data[$row['job_no']]['currier_pre_cost_total_value']+$summary_data[$row['job_no']]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row['job_no']]['design_pre_cost_total_value'];
				
				$tot_emblish_cost=$summary_data[$row['job_no']]['embel_cost_total_value'];
				$pri_freight_cost_per=$summary_data[$row['job_no']]['freight_total_value'];
				$pri_commercial_per=$commer_lc_cost[$row['job_no']];
				$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$row['job_no']];
				
				$total_btb = $summary_data[$row['job_no']]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row['job_no']]['comm_cost_total_value']+$summary_data[$row['job_no']]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row['job_no']]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row['job_no']]['common_oh_total_value']+$summary_data[$row['job_no']]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
				$tot_quot_sum_amount = $total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
				$total_cm_for_gmt = ($tot_quot_sum_amount-$tot_cm_for_fab_cost-$total_btb);
				$total_quot_pcs_qty = $quotation_qty_array[$row['job_no']]['QTY_PCS'];

				$zs[$row['buyer_name']]['total_cm_for_gmt'] += $total_cm_for_gmt;
				$zs[$row['buyer_name']]['total_quot_pcs_qty'] += $total_quot_pcs_qty;
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| end
				|--------------------------------------------------------------------------
				*/
				//echo $quotation_qty_array[$row['job_no']]['QTY_PCS']."<br>";
			}
			//echo "<pre>";
			//print_r($zs);
			
			foreach($buyer_data_arr as $buyer_id=>$row)
			{
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| start
				|--------------------------------------------------------------------------
				*/
				$cm_lc_dzn = ($total_cm_for_gmt/$total_quot_pcs_qty)*12;
				$cm_lc_pcs = ($zs[$buyer_id]['total_cm_for_gmt']/$zs[$buyer_id]['total_quot_pcs_qty']);
				$cm_lc_value = ($row['sew_out_qty'])*($cm_lc_pcs);
				$cm_lc_value_export = ($buyer_data_arr[$buyer_id]['ex_factory'])*($cm_lc_pcs);

				$buyer_wise_all_info[$buyer_id]['sew_cm_lc'] += $cm_lc_value;
				$buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'] += $cm_lc_value_export;
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| end
				|--------------------------------------------------------------------------
				*/
			}
			
			//print_r($buyer_wise_all_info);die;
			foreach($buyer_data_arr as $buyer_id=>$val)
			{
				if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$po_buyer_qty=$po_country_sum_qty[$buyer_id]['po_qty'];
				$sew_fob=$val['sew_fob'];
				$ex_fact_qty=$buyer_data_arr[$buyer_id]['ex_factory'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
				$ex_fac_fob=$buyer_data_arr[$buyer_id]['ex_fac_fob'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
				$grand_sum_sewfob+=$sew_fob;
				$grand_sum_exfob+=$ex_fac_fob;
				$last_cut_qty=$lastmon_buyer_data_arr[$buyer_id]['cut_qty'];
				$last_sew_in_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_in_qty'];
				$last_sew_out_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_out_qty'];
				$last_poly_qty=$lastmon_buyer_data_arr[$buyer_id]['poly_qty'];
				$last_finish_qty=$lastmon_buyer_data_arr[$buyer_id]['finish_qty'];
				$last_ex_fact_sum_qty=$po_ex_fact_sum_qty_lastmon[$buyer_id]['prod_qty'];
				//$last_month_qty=$last_cut_qty+$last_sew_in_qty+$last_sew_out_qty+$last_poly_qty+$last_finish_qty+$last_ex_fact_sum_qty;
				$last_month_qty=$last_ex_fact_sum_qty;
				$lay_percent = ($val['cut_lay_qty']*100)/$po_buyer_qty;
				$cut_percent = ($val['cut_qty']*100)/$po_buyer_qty;
				
				$summaryHTML.='<tr bgcolor="'.$bgcolor.'">
					<td width="30">'.$k.'</td>
					<td width="120"><div style="word-break:break-all">'.$buyer_short_library[$buyer_id].'</div></td>
					<td width="100" align="right"><p>'.$po_buyer_qty.'</p></td>
					<td width="80" align="right"><p>'.$val['cut_lay_qty'].'</p></td>
					<td width="80" align="right"><p>'.number_format($lay_percent,2).'%</p></td>
					<td width="80" align="right" title="'.$last_cut_qty.'"><p>'.number_format($val['cut_qty'],0).'</p></td>
					<td width="80" align="right" title="'.$last_cut_qty.'"><p>'.number_format($cut_percent,2).'%</p></td>
					<td width="80" align="right" title="'.$last_sew_in_qty.'"><p>'.number_format($val['sew_in_qty'],0).'</p></td>
					<td width="80" align="right" title="'.$last_sew_out_qty.'"><p>'.number_format($val['sew_out_qty'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm_lc'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_fob'],0).'</p></td>
					<td width="80" align="right" title="'.$last_finish_qty.'"><p>'.number_format($val['finish_qty'],0).'</p></td>
					<td width="80" align="right" title="'.$last_ex_fact_sum_qty.'"><p>'.number_format($ex_fact_qty,0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['ex_cm'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'],0).'</p></td>
					<td width="80" align="right"><p>'.number_format($buyer_wise_all_info[$buyer_id]['ex_fob'],0).'</p></td>
				</tr>';
				
				$k++;
				$grand_sum_exfact_qty+=$ex_fact_qty;
				$grand_sum_finished_qty+=$val['finish_qty'];
				
				$grand_sum_out_qty+=$val['sew_out_qty'];
				$grand_sum_in_qty+=$val['sew_in_qty'];
				$grand_sum_cut_lay_qty+=$val['cut_lay_qty'];
				$grand_sum_cut_qty+=$val['cut_qty'];
				$grand_sum_country_qty+=$po_buyer_qty;
				$grand_sum_last_month_qty+=$last_month_qty;
			}
			$summaryHTML.='</table>
			<table style="width:'.$sum_width.'px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<tfoot>
					<tr>
					<th class="alignment_css" width="30">&nbsp;</th>
					<th class="alignment_css" width="120">Grand Total</th>
					<th class="alignment_css" width="100" align="right">'.number_format($grand_sum_country_qty,0).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_cut_lay_qty,0).'</th>
					<th class="alignment_css" width="80" align="right"></th>
					<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_cut_qty,0).'</th>
					<th class="alignment_css" width="80" align="right"></th>
					<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_in_qty,0).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_out_qty,0).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm")),0 ).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm_lc")),0 ).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_fob")),0 ).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_finished_qty,0).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_exfact_qty,0).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"ex_cm")),0 ).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm_lc_export")),0 ).'</th>
					<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"ex_fob")),0 ).'</th>
					</tr>
				</tfoot>
			</table>
		</div>
	 </fieldset>
	 </div>';?>
	 <? echo $summaryHTML;?>
	 <? $dtls_width=1690; ?>
		<br/>
		<!-- =========================================== DETAILS PART START ==================================== -->
		<div style="width:<? echo $dtls_width+25;?>px;">
		<table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
		<caption><strong>Detail Report</strong></caption>
			<thead>
				<tr>
					<th class="alignment_css" width="30">SL</th>
					<th class="alignment_css" width="100">Buyer</th>                                                      
					<th class="alignment_css" width="100">PO No</th>                            
					<th class="alignment_css" width="100"><p>PO Qty</p></th>
					<th class="alignment_css" width="80">Cut Qty</th>
					<th class="alignment_css" width="80">Cut Qty %</th>
					<th class="alignment_css" width="80">Cut. QC</th>
					<th class="alignment_css" width="80">Cut. QC %</th>
					<th class="alignment_css" width="80">Sewing <br>Input</th>
					<th class="alignment_css" width="80">Sewing <br>Output</th>
					<th class="alignment_css" width="80">CM Dzn <br>Rate BOM</th>
					<th class="alignment_css" width="80">CM Dzn <br>Rate LC</th>
					<th class="alignment_css" width="80">Unit Price</th>
					<th class="alignment_css" width="80">Sew CM <br>Value BOM</th>
					<th class="alignment_css" width="80">Sew CM <br>Value LC</th>
					<th class="alignment_css" width="80">Sew FOB <br>Value</th>
					<th class="alignment_css" width="80">Finishing</th> 
					<th class="alignment_css" width="80">Export Pcs</th>
					<th class="alignment_css" width="80">Export CM <br> Value BOM</th>
					<th class="alignment_css" width="80">Export CM <br> Value LC</th>
					<th class="alignment_css" width="80">Export FOB <br>Value</th> 
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $dtls_width+20;?>px; max-height:245px; float:left; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="tableBody">
			<!-- <tr>
			<th colspan="18" align="left"><?echo $floor_name .' '.$company_name; ?></th>
			</tr> -->
			<?
			$j=1;$grand_cut_qty=$grand_in_qty=$grand_out_qty=$grand_poly_qty=$grand_finished_qty=$grand_exfact_qty=$grand_country_qty=$grand_country_qty_pcs=0;
			// echo $working_company_id;die;
			/*if($poIds!="")
			{
			$cm_gmt_cost_dzn_arr=array();
			$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_companies_id,$poIds); 
			//print_r($cm_gmt_cost_dzn_arr);die;
			}*/
			$cm_dzn_rate=0;
			
			foreach($prod_dtls_data_arr as $po_id=>$val)
			{
				// echo "<pre>";
				// print_r($val);
				$cm_dzn_rate=$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"];
				$jobs= $val['job_no'];
				if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$set_ratio=$val['ratio'];
				
				$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
				$cnty_qnty=$po_country_qty*$set_ratio;
				// echo $jobs.' '.$summary_data2[$jobs][$po_id][cm_cost_job]/($cnty_qnty/12);echo "<br>";
				//$cm_part_one=$summary_data2[$jobs][$po_id][cm_cost_job]/($cnty_qnty/12); 
				$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
				$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
				//$cut_lay_qty=$cut_lay_qty_data_arr[$po_id]['cut_lay_qty'];
				
				$unit_price=$val['unit_price'];
				$sewing_cm=($val['sew_out_qty']*$cm_dzn_rate)/12;
				$sewing_fob=$val['sew_out_qty']*$unit_price;

				$exfac_cm=($ex_fact_country_qty*$cm_dzn_rate)/12;
				$exfac_fob=$ex_fact_country_qty*$unit_price;
				
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| start
				|--------------------------------------------------------------------------
				*/
				$cm_lc_rate = ($zs[$val['buyer_name']]['total_cm_for_gmt']/$zs[$val['buyer_name']]['total_quot_pcs_qty'])*12;
				$cm_lc_pcs = ($zs[$val['buyer_name']]['total_cm_for_gmt']/$zs[$val['buyer_name']]['total_quot_pcs_qty']);
				$cm_lc_value = ($val['sew_out_qty'])*($cm_lc_pcs);
				$cm_lc_value_export = ($ex_fact_country_qty)*($cm_lc_pcs);

				//$buyer_wise_all_info[$val['buyer_name']]['cm_lc_rate']  = $cm_lc_rate;
				//$buyer_wise_all_info[$val['buyer_name']]['sew_cm_lc'] += $cm_lc_value;
				//$buyer_wise_all_info[$val['buyer_name']]['sew_cm_lc_export'] += $cm_lc_value_export;
				/*
				|--------------------------------------------------------------------------
				| for price quotation wise cm value LC
				| calculate cm value
				| end
				|--------------------------------------------------------------------------
				*/

				if($country_id=="00")
				{
					$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty'];
					$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
				}
				else
				{
					$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
				}
				//$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($working_company_id,$po_id); 
				//print_r($cm_gmt_cost_dzn_arr);
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>">
				<td class="alignment_css" width="30"><? echo $j;?></td>
				<td class="alignment_css" width="100"><div style="word-break:break-all"><? echo  $buyer_short_library[$val['buyer_name']];  ?></div></td>
				<td class="alignment_css" width="100"><div style="word-break:break-all"><? echo implode(PHP_EOL, str_split($val["po_number"],15));echo $sub_self=($country_id=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
				
				<td class="alignment_css" width="100"  align="right" title="<? echo 'S '.$set_ratio;?>"><p><? echo $po_button_qty;//$po_country_qty;?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($val['cut_lay_qty'],0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? $cut_lay_p = ($val['cut_lay_qty']*100)/$po_country_qty_pcs; echo number_format($cut_lay_p,2);?>%</p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($val['cut_qty'],0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? $cut_qty_p = ($val['cut_qty']*100)/$po_country_qty_pcs; echo number_format($cut_qty_p,2);?>%</p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
				<td class="alignment_css" width="80" title="From Class=<? echo number_format($cm_dzn_rate,4);?>"  align="right"><p><? echo number_format($cm_dzn_rate,2);?></p></td>
				<td class="alignment_css" width="80" title="From Class=<? echo number_format($cm_lc_rate,4);?>"  align="right"><p><? echo number_format($cm_lc_rate,2);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo implode(PHP_EOL, str_split($unit_price,12));?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_cm,0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($cm_lc_value,0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_fob,0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($val['finish_qty'],0);?></p></td>
				<td class="alignment_css" width="80"  align="right"><p><? echo number_format($ex_fact_country_qty,0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($exfac_cm,0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($cm_lc_value_export,0);?></p></td>
				<td class="alignment_css" width="80" align="right"><p><? echo number_format($exfac_fob,0);?></p></td>
			</tr>
			<?
			$j++;
			$grand_exfact_qty+=$ex_fact_country_qty;
			$grand_finished_qty+=$val['finish_qty'];
			$grand_poly_qty+=$val['poly_qty'];
			$grand_out_qty+=$val['sew_out_qty'];
			$grand_in_qty+=$val['sew_in_qty'];
			$grand_cut_lay_qty+=$val['cut_lay_qty'];
			$grand_cut_qty+=$val['cut_qty'];
			$grand_country_qty+=$po_country_qty;
			$grand_country_qty_pcs+=$po_country_qty_pcs;
			$gr_sewing_cm+=$sewing_cm;
			$gr_sewing_fob+=$sewing_fob;
			$gr_exfac_cm+=$exfac_cm ;
			$gr_exfac_fob+=$exfac_fob;
			
			$gr_sewing_cm_lc += $cm_lc_value;
			$gr_sewing_cm_lc_export += $cm_lc_value_export;
			}
			?>
			</table>
		</div>
		<div style="width:<? echo $dtls_width+20;?>px;">
			<table style="width:<? echo $dtls_width;?>px" class="tbl_bottom" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all" align="left">
				<tr>
					<td class="alignment_css" width="30">&nbsp;</td>
					<td class="alignment_css" width="100">&nbsp;</td>
					<td class="alignment_css" width="100">Grand Total:</td>
					<td class="alignment_css" width="100" align="right"><? //echo number_format($grand_country_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><?// echo number_format($grand_cut_lay_qty_perc,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><?// echo number_format($grand_cut_qty_per,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_in_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_out_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"> </td>
					<td class="alignment_css" width="80" align="right"> </td>
					<td class="alignment_css" width="80" align="right"> </td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_cm,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_cm_lc,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_fob,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_finished_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($grand_exfact_qty,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_exfac_cm,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_cm_lc_export,0); ?></td>
					<td class="alignment_css" width="80" align="right"><? echo number_format($gr_exfac_fob,0); ?></td>
				</tr>
			</table>
			</div>
		</div>
		<?		
	}
	?>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
   
    foreach (glob("$user_id*.xls") as $summary_filename) {
    @unlink($summary_filename);
    }
    //---------end------------//
    $name2=time();
    $summary_filename=$user_id."_"."summary_".$name2.".xls";
    $create_new_doc2 = fopen($summary_filename, 'w');	
    $is_created2 = fwrite($create_new_doc2, $summaryHTML);

    // ===================================

    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$process_rpt_type****$summary_filename****$report_type"; 
	exit();	
}

if($action=="report_generate_in_excel")
{
	//echo 'su..re'; die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_working_company_id=str_replace("'","",$cbo_working_company_id);
	if(str_replace("'","",$cbo_working_company_id)==0)
	{
		$working_company="";
		$working_company7="";
	}
	else
	{
		$working_company=" and c.serving_company in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company_subcon=" and a.company_id in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company7=" and c.working_company_id in(".str_replace("'","",$cbo_working_company_id).")";
	}
	 
	if(str_replace("'","",$txt_job_no)=="")
	{ 
		$job_cond=""; 
		$job_cond_subcon=""; 
	}
	else
	{
		$job_cond="and a.job_no_prefix_num=".trim($txt_job_no)."";
		$job_cond_subcon="and b.job_no_prefix_num=".trim($txt_job_no)."";
	} 
	if (str_replace("'","",$txt_style_ref)=="") 
	{
		$style_cond="";
	}
	else
	{
		$style_cond="and a.style_ref_no=".trim($txt_style_ref)."";
	} 
	if (str_replace("'","",$txt_po_no)=="") 
	{
		$order_cond="";
		$order_cond_subcon=""; 

	}
	else
	{
		$order_cond="and b.po_number=".trim($txt_po_no).""; 
		$order_cond_subcon=" and c.order_no=".trim($txt_po_no).""; 
	} 

	$group_name = (str_replace("'", "", $cbo_floor_group_name)==0) ? str_replace("'", "", $cbo_floor_group_name) : $cbo_floor_group_name;
	// echo gettype($cbo_floor_group_name);
	// $group_name = $cbo_floor_group_name;
	if($group_name !== '0' && str_replace("'","",$cbo_floor) =="")
	{
		if(str_replace("'","",$cbo_location)!=0 || str_replace("'","",$cbo_location)!="")
		{
			$locationId = str_replace("'","",$cbo_location);
			$locationId_cond = " and a.location_id in($locationId) ";
		}
		$group_cond="";
		$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.company_id in($cbo_working_company_id) $locationId_cond and a.status_active=1 and a.group_name=$cbo_floor_group_name order by a.id");
		foreach ($group_sql as $value) 
		{
			if($group_cond=="")
			{
				$group_cond = $value[csf('id')];
			}
			else
			{
				$group_cond .= ",".$value[csf('id')];
			}
		}
		$floor_cond=" and c.floor_id in($group_cond)";
		$floor_cond_subcon=" and a.floor_id in($group_cond)";
	}
	else if(str_replace("'","",$cbo_floor) !="" && $group_name != '0')
	{
		$floor_cond=" and c.floor_id in(".str_replace("'","",$cbo_floor).")";
		$floor_cond_subcon=" and a.floor_id in(".str_replace("'","",$cbo_floor).")";
	}
	else if(str_replace("'","",$cbo_floor) !="" && $group_name == '0')
	{
		$floor_cond=" and c.floor_id in(".str_replace("'","",$cbo_floor).")";
		$floor_cond_subcon=" and a.floor_id in(".str_replace("'","",$cbo_floor).")";
	}
	else
	{
		$floor_cond=""; 
		$floor_cond_subcon="";
	}

	if(str_replace("'","",$cbo_location)==0 || str_replace("'","",$cbo_location)=="")
	{
		$location_cond=""; 
		$location_cond_subcon="";

	}
	else 
	{
		$cbo_location = str_replace("'","",$cbo_location);
		$location_cond_subcon = str_replace("'","",$cbo_location);

		$location_cond=" and c.location in($cbo_location)";
		$location_cond_subcon=" and a.location_id in($cbo_location)";
	}

	$process_rpt_type=str_replace("'","",$cbo_production_process);
	$cbo_year=str_replace("'","",$cbo_year);
	$report_type=str_replace("'","",$type);
	$working_company_id=str_replace("'","",$cbo_working_company_id);
	$year_cond="";
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
		
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
		$buyer_id_cond_subcon=" and b.party_id=$cbo_buyer_name";
	}
		
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($end_date,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($txt_date_from,"","",1);
			$end_date=change_date_format($txt_date_to,"","",1);
		}
		$prod_date_cond=" and c.production_date between '$start_date' and '$end_date'";
		$prod_date_cond_subcon=" and a.production_date between '$start_date' and '$end_date'";
		$prod_date_cond2=" and d.ex_factory_date between '$start_date' and '$end_date'";
		$prod_date_cond2_subcon=" and a.delivery_date between '$start_date' and '$end_date'";
		$prod_date_cond7=" and c.entry_date between '$start_date' and '$end_date'";
	}
		//$month=date("m",strtotime($start_date))-1;
		//echo date('Y-m', strtotime('-1 month', time()));
		$start_date=date("d-m-Y",strtotime($start_date));
		$start_date_tmp=date("Y-m",strtotime($start_date));
		$mon_data=explode("-",$start_date);
		$mon_id=$mon_data[1];
		$year_id=$mon_data[2];
		//echo $mon_id.'='.$year_id;
		$num_days = cal_days_in_month(CAL_GREGORIAN, $mon_id, $year_id);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		} //echo $year_cond.'assss';

		$company_names="";
		foreach(explode(",",$working_company_id) as $key=>$vals)
		{
			if($company_names=="")
			{
				$company_names=$company_library[$vals];
			}
			else
			{
				$company_names .=' , '.$company_library[$vals];
			}
		}
	$client_array = array();
	$sql_client=sql_select("SELECT a.id, a.buyer_name FROM lib_buyer a, lib_buyer_tag_company b WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.buyer_id = a.id AND a.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (7)) group by a.id, a.buyer_name ORDER BY buyer_name");
	foreach ($sql_client as $key => $value) 
	{
		$client_array[$value[csf('id')]] = $value[csf('buyer_name')];
	}
	// echo "<pre>";
	// print_r($client_array);
			
	if($report_type==10)
	{
 
		$company= str_replace("'","", $cbo_working_company_id);
		$floor= str_replace("'","", $cbo_floor);
		$floor_name="";
		foreach(explode(",", $floor) as $v ) 
		{
			if($floor_name)$floor_name.=','.$floor_library[$v];else $floor_name=$floor_library[$v];
		}
		$company_name="";
		foreach(explode(",", $company) as $v )
		{
			if($company_name)$company_name.=','.$company_library[$v];else $company_name=$company_library[$v];
		}
		$all_companies_id=implode(",",$company_library);

		$current_month=date("m",strtotime($start_date));
		//$current_month=date('m');
		$current_year=date("Y",strtotime($start_date));
		$lastmonth=$current_month-1;
		if($current_month==1)
		{
			$lastmonth2=12;
			$current_year2=$current_year-1;
		}

		$firstdate= "01-".$lastmonth."-".$current_year ;
		// $lastdateofmonth=date('t',$lastmonth);// 	
		$lastdateofmonth=cal_days_in_month(CAL_GREGORIAN, $lastmonth, $current_year); 
		$lastdateofmonth2=cal_days_in_month(CAL_GREGORIAN, $lastmonth2, $current_year2);
		$lastdate=$lastdateofmonth."-".$lastmonth."-".$current_year ;
		$lastdate2=$lastdateofmonth2."-".$lastmonth2."-".$current_year2 ;
		//echo $firstdate ."==".$lastdate ;
					
		if($db_type==0)
		{
			$firstdate=change_date_format($firstdate,"yyyy-mm-dd","");
			$lastdate=change_date_format($lastdate,"yyyy-mm-dd","");
			$lastdate2=change_date_format($lastdate2,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$firstdate=change_date_format($firstdate,"","",1);
			$lastdate=change_date_format($lastdate,"","",1);
			$lastdate2=change_date_format($lastdate2,"","",1);
		}
		$sum_prod_date_cond=" and c.production_date between '$firstdate' and '$lastdate'";
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		if($current_month==1) $sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		$sum_width=1370;
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";
		 
		ob_start(); 
		$summaryHTML = "";
		?>
		<style type="text/css">
			.alignment_css
			{
				word-wrap: break-word;
				word-break: break-all;
			}
			table tr td{border:1px solid;}
		</style>

		
        <? $summaryHTML.='<div id="buyer_wise_summary"  style="width:'.($sum_width+30).'px;  ruby-align:center" >
		<fieldset style="width:'.($sum_width+30).'px;">
            <table width="'.$sum_width.'"  cellspacing="0">
                <tr style="border:none;">
                    <td colspan="18" align="center" style="border:none; font-size:16px; font-weight:bold">
                    '.$company_names.'
                    <br>                                
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
                    '."Date: ". change_date_format($start_date).' To '. change_date_format($end_date).'
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="18" align="center"  > <strong>Buyer wise summary </strong></td>
                </tr>
                  
            </table>
            <br />	
            <table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            	<thead>
            		<!-- <tr>
            			<td colspan="16"></td>
            			<td colspan="2" align="center"><b>Waiting For Shipment</b></td>
            		</tr> -->
            		<tr>
            			<th class="alignment_css" width="30">SL</th>
            			<th class="alignment_css" width="120">Buyer</th>
            			<th class="alignment_css" width="100">PO Qty</th>
            			<th class="alignment_css" width="80">Cut Qty</th>
            			<th class="alignment_css" width="80">Cut Qty%</th>
            			<th class="alignment_css" width="80">Cutting QC</th>
            			<th class="alignment_css" width="80">Cutting QC%</th>
            			<th class="alignment_css" width="80">Sewing<br> Input</th>
            			<th class="alignment_css" width="80">Sewing<br> Output</th>
            			<th class="alignment_css" width="80">Sewing CM <br>Value BOM</th>
            			<th class="alignment_css" width="80">Sewing CM <br>Value LC</th>
            			<th class="alignment_css" width="80">Sewing FOB<br> Value</th>                           
            			<th class="alignment_css" width="80">Finishing</th> 
            			<th class="alignment_css" width="80">Export Pcs</th>
            			<th class="alignment_css" width="80">Export CM <br>Value BOM</th>
            			<th class="alignment_css" width="80">Export CM <br>Value LC</th>
            			<th class="alignment_css" style="border:1px solid;" width="80">Export FOB<br> Value</th>

            		</tr>
            	</thead>
            </table>
            <div style="width: '.($sum_width+20).'px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
                <table class="rpt_table" width="'.$sum_width.'" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">';?>
                <?
				
			
				 
			//cut and lay qty.....................................
			$buyer_data_arr=array();
			$lc_company_arr=array();
			$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
			$sql_cut_lay = "SELECT b.po_total_price,(b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			  (d.size_qty) as prod_qty
			 FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
			WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_cut_lay $floor_cond $prod_date_cond7 order by b.id,d.country_id ";
			// echo $sql_cut_lay;die;
			$cut_lay_result_arr=sql_select($sql_cut_lay);
			foreach($cut_lay_result_arr as $rows)
			{
				$buyer_data_arr[$rows[csf('buyer_name')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
				$buyer_data_arr[$rows[csf('buyer_name')]]['job_no'] = $rows[csf('job_no')];
				
				$prod_dtls_data_job_arr[$rows[csf('job_no')]] =$rows[csf('job_no')];	
				$prod_dtls_data_po_arr[$rows[csf('po_id')]] =$rows[csf('po_id')];	
				$prod_dtls_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['style']=$rows[csf('style_ref_no')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_name']=$rows[csf('buyer_name')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_client']=$rows[csf('buyer_client')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['po_number']=$rows[csf('po_number')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['ratio']=$rows[csf('ratio')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['job_no']=$rows[csf('job_no')];
				$prod_dtls_data_arr[$rows[csf('po_id')]]['unit_price']=$rows[csf('unit_price')];
				$total_po_amount_arr[$rows[csf('job_no')]]+=$rows[csf('po_total_price')];
				
				$tmp_count=$rows[csf('country_id')];
				$tmp_po[$rows[csf('po_id')]]=$rows[csf('po_id')];
				$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
				//$cut_lay_qty_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];

			}
			// die('GO TO HELL!');
			// echo "<pre>";
			// print_r($prod_dtls_data_po_arr);die;
			// echo "</pre>";
			//cut and lay qty.....................................	
				  
				  				  
				$sql_prod_sum = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty,
						  (CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty,
						  (CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty,
						  (CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty,
						  (CASE WHEN c.production_type=11 and d.production_type=11 THEN d.production_qnty ELSE 0 END)  as poly_qty,
						  (CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty
						 FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.job_no=b.job_no_mst  and c.po_break_down_id=b.id and c.id=d.mst_id  and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,4,5,11,8) and d.production_type=c.production_type  and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)  $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";//and c.po_break_down_id=e.po_break_down_id and a.job_no=e.job_no_mst

				$subcon_sql_prod="SELECT  c.rate as unit_price,b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client, b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio,  
						  (CASE WHEN a.production_type=1 and d.production_type=1 THEN d.prod_qnty ELSE 0 END)  as cut_qty,

						  (CASE WHEN a.production_type=7  and d.production_type=7 THEN d.prod_qnty ELSE 0 END)  as sew_in_qty, 
						  (CASE WHEN a.production_type=2  and d.production_type=2 THEN d.prod_qnty ELSE 0 END)  as sew_out_qty,
						  (CASE WHEN a.production_type=5 and d.production_type=5 THEN d.prod_qnty ELSE 0 END)  as poly_qty,
						  (CASE WHEN a.production_type=4 and d.production_type=4  THEN d.prod_qnty ELSE 0 END)  as finish_qty
						   from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(1,2,4,5,7) and d.production_type in(1,2,4,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";

				
					// echo $sql_prod_sum;die();	
				$sum_result=sql_select($sql_prod_sum);
				// die('GO TO HELL!');
					// echo $subcon_sql_prod;die();
				//$po_id='';	$country_id='';$buyer_ids='';
				foreach($sum_result as $row)
				 {
						
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['cut_qty']+=$row[csf('cut_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['poly_qty']+=$row[csf('poly_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['finish_qty']+=$row[csf('finish_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['buyer_client']=$row[csf('buyer_client')];
					$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
					
					$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];	
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['cut_qty']+=$row[csf('cut_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['poly_qty']+=$row[csf('poly_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['finish_qty']+=$row[csf('finish_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					
					
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];	
					
				 } 
				 // die('GO TO HELL!');	

				$sum_result_subcon=sql_select($subcon_sql_prod);
				 foreach($sum_result_subcon as $row)
				 {
				 	$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_fob']+=$row[csf('sew_out_qty')]*$row[csf('unit_price')];	
					$buyer_data_arr[$row[csf('buyer_name')]]['cut_qty']+=$row[csf('cut_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['poly_qty']+=$row[csf('poly_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['finish_qty']+=$row[csf('finish_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['buyer_client']=$row[csf('buyer_client')];
					$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
					
					$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];	
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['cut_qty']+=$row[csf('cut_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['poly_qty']+=$row[csf('poly_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['finish_qty']+=$row[csf('finish_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];	
					$all_subcon_po_id_arrs[$row[csf('po_id')]]=$row[csf('po_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
					
				 } 


				// die('GO TO HELL!');	
				$location_cond_ex = str_replace("c.location", "c.delivery_location_id", $location_cond);
				$floor_cond2=str_replace("c.floor_id", "c.delivery_floor_id", $floor_cond);
				$sql_prod_exf = "SELECT (b.unit_price/a.total_set_qnty) as unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.job_no,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			  	(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty
					 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
				WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_ex $floor_cond2  $prod_date_cond2 order by b.id,d.country_id ";

				$subcon_sql_ex="SELECT  c.rate as unit_price, b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0       $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";

				//echo $sql_prod_exf;die;
				//echo $subcon_sql_ex;die;

				$result_prod_exf=sql_select($sql_prod_exf);
				$result_prod_exf_subcon=sql_select($subcon_sql_ex);
				$po_no_id="";$buyer_ids2='';
				$po_country_id='';
				foreach($result_prod_exf as $row)
				{
					$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$po_ex_fact_dtls_qty[$row[csf('po_id')]]['prod_qty']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['ex_fac_fob']+=$row[csf('prod_qty')]*$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
					
					$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];		
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];		
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
					if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
					if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
					//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
				}
				// echo "<pre>";
				// print($po_ex_fact_sum_qty);
				// echo "</pre>";
				foreach($result_prod_exf_subcon as $row)
				{
					$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$po_ex_fact_dtls_qty[$row[csf('po_id')]]['prod_qty']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
					$buyer_data_arr[$row[csf('buyer_name')]]['ex_fac_fob']+=$row[csf('prod_qty')]*$row[csf('unit_price')];
					$buyer_data_arr[$row[csf('buyer_name')]]['job_no'] = $row[csf('job_no')];
					
					$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];	
					//$prod_dtls_data_job_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
					if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
					if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
					$lc_company_arr[$rows[csf('company_name')]]=$rows[csf('company_name')];
					//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
				}
				
				
				 $po_country_id=implode(",",array_unique(explode(",",$po_country_id)));
				 $po_no_id=implode(",",array_unique(explode(",",$po_no_id)));
				 
				
						//echo $po_cond_for_in;
						$poIds=implode(",",$tmp_po);
						$poIds=chop($poIds,','); $po_cond_for_in="";// $order_cond1=""; 
						$po_ids=count(array_unique(explode(",",$poIds)));
						if($db_type==2 && $po_ids>1000)
						{
							$po_cond_for_in=" and (";
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$po_cond_for_in.=" b.id in($ids) or"; 
							}
							$po_cond_for_in=chop($po_cond_for_in,'or ');
							$po_cond_for_in.=")";
						}
						else
						{
							$poIds=implode(",",array_unique(explode(",",$poIds)));
							$po_cond_for_in=" and b.id in($poIds)";
						}
						
						$lc_company = implode(",", $lc_company_arr);
						// Country
						$country_Id=implode(",",$tmp_count);
						$poIds_c=chop($country_Id,','); $po_cond_for_in2="";// $order_cond1=""; 
						$po_ids_c=count(array_unique(explode(",",$country_Id)));
						if($db_type==2 && $po_ids_c>1000)
						{
							$po_cond_for_in2=" and (";
							$poIdsArr_c=array_chunk(explode(",",$poIds_c),999);
							foreach($poIdsArr_c as $ids)
							{
								$ids_c=implode(",",$ids);
								$po_cond_for_in2.=" c.country_id in($ids_c) or"; 
							}
							$po_cond_for_in2=chop($po_cond_for_in2,'or ');
							$po_cond_for_in2.=")";
						}
						else
						{
							$poIds_c=implode(",",array_unique(explode(",",$poIds_c)));
							$po_cond_for_in2=" and c.country_id in($poIds_c)";
						}
						
					$sql_prod_exf_last = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,
			  		(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty,a.client_id as buyer_client
					 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
					WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $sum_prod_date_cond2 order by b.id,d.country_id ";
					// echo $sql_prod_exf_last;die();
				$result_prod_exf_last=sql_select($sql_prod_exf_last);
				foreach($result_prod_exf_last as $row)
				{
					$po_ex_fact_sum_qty_lastmon[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					//$po_ex_fact_sum_qty_lastmon[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
				}
			 
				
				$sql_color="SELECT a.buyer_name,b.po_quantity as po_qty,b.id as po_id, a.client_id as buyer_client from   wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,2,3)  $po_cond_for_in   $buyer_id_cond  ";
				$po_country_sum_qty=array();
				$po_country_dtls_qty=array();
				$sql_result_color=sql_select($sql_color);
				foreach($sql_result_color as $row)
				{
					$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
					$po_country_dtls_qty[$row[csf('po_id')]]['po_qty']+=$row[csf('po_qty')];
					$summ+=$row[csf('po_qty')];
					 //echo $row[csf('po_qty')]."=";
				}
				$sub_po_cond=implode(",",$all_subcon_po_id_arrs);
			    $sql_color_subcon ="SELECT a.party_id as buyer_name ,c.qnty as po_qty,b.id as po_id,'00' as country_id,b.cust_buyer as buyer_client from   subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where  a.subcon_job=b.job_no_mst and c.order_id=b.id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.id in($sub_po_cond) order by b.id ";
				foreach(sql_select($sql_color_subcon) as $row)
				{
					$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
					$po_country_dtls_qty[$row[csf('po_id')]]['po_qty']+=$row[csf('po_qty')];
					$summ+=$row[csf('po_qty')];
 				}
 				$target_sql=sql_select("SELECT   mst_id, target_per_hour  FROM PROD_RESOURCE_DTLS ORDER BY ID asc  ");
 				$line_wise_tg=array();
 				foreach($target_sql as $v)
 				{
					$line_wise_tg[$v[csf("mst_id")]]=$v[csf("target_per_hour")];
 				}
				
				 $k=1;$grand_sum_cut_qty=$grand_sum_in_qty=$grand_sum_out_qty=$grand_sum_poly_qty=$grand_sum_finished_qty=$grand_sum_exfact_qty=$grand_sum_country_qty=$grand_sum_last_month_qty=$grand_sum_sewfob=$grand_sum_exfob=0;
				 // echo "<pre>";
				 // print_r($buyer_data_arr);die;
				// $condition= new condition();
				 $all_jobs="";
				 foreach($prod_dtls_data_job_arr as $v)
				 {
				 	if($v && $v!=",")
				 	{
				 		if($all_jobs)$all_jobs.=","."'".$v."'";
				 		else $all_jobs="'".$v."'";
				 	}
				 }
				  $txt_order_id="";
				 foreach($prod_dtls_data_po_arr as $v)
				 {
				 	if($v && $v!=",")
				 	{
				 		if($txt_order_id)$txt_order_id.=",".$v;
				 		else $txt_order_id=$v;
				 	}
				 }

				// echo $txt_order_id; 
				$po_ids=count(explode(",",$txt_order_id));
				if($db_type==2 && $po_ids>1000)
				{
					$po_cond_=" and (";
					$poIdsArr=array_chunk(explode(",",$txt_order_id),999);
					foreach($poIdsArr as $ids)
					{
						$ids_c=implode(",",$ids);
						$po_cond_.=" b.id in($ids_c) or"; 
					}
					$po_cond_=chop($po_cond_,'or ');
					$po_cond_.=")";
				}
				else
				{						
					$po_cond_=" and b.id in($txt_order_id)";
				}
				 // die('GO TO HELL!');	
				 
					 $po_qty=0;
				 $po_plun_cut_qty=0;
				 $total_set_qnty=0;
				 
				 $pro_sql=sql_select("SELECT  a.production_type, b.unit_price,a.id,a.po_break_down_id, a.sewing_line,sum(case WHEN a.production_type=5 then a.production_quantity else 0 end ) as sew,  sum(case WHEN a.production_type=8 then production_quantity else 0 end ) as fin  FROM pro_garments_production_mst a,wo_po_break_down b  where a.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 and  a.production_type in(5,8) $po_cond_  group by  a.production_type,  b.unit_price,  a.id, a.po_break_down_id, a.sewing_line ORDER BY a.ID asc  ");//and b.id in($txt_order_id)
				 $po_wise_tg=array();
				 foreach($pro_sql as $v)
				 {
				 	if($v[csf("production_type")]==5)
				 	{
				 		$po_wise_tg[$v[csf("po_break_down_id")]]["line"]=$v[csf("sewing_line")];
				 	}
				 	$po_wise_tg[$v[csf("po_break_down_id")]]["qnty"]+=$v[csf("sew")]-$v[csf("fin")];
				 	$po_wise_tg[$v[csf("po_break_down_id")]]["value"]+=($v[csf("sew")]-$v[csf("fin")])*$v[csf("unit_price")];
				 }

				 //print_r($po_wise_tg);die;
				 // echo $all_companies_id."<br>";
				 // echo $poIds;
				 $cm_gmt_cost_dzn_arr=array();
				 $cm_gmt_cost_dzn_arr_new=array();
				if($poIds!="")
				{
					//$cm_gmt_cost_dzn_arr=array();
					// $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_companies_id,$poIds); 
					//$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($company,$poIds); 
					// print_r($cm_gmt_cost_dzn_arr);die;
				}
				$new_arr=array_unique(explode(",", $poIds));
				$chnk_arr=array_chunk($new_arr,50);
				foreach($chnk_arr as $vals )
				{
					$p_ids=implode(",", $vals);
					//if(!empty($cm_gmt_cost_dzn_arr))
					//$cm_gmt_cost_dzn_arr.=fnc_po_wise_cm_gmt_class($company,$p_ids); 
					//else
					 $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($lc_company,$p_ids); 
					 foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
					 {
					 	$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"] =$vv["dzn"] ;
					 }
				}
				
				/*
				|--------------------------------------------------------------------------
				| getting price quotation wise cm valu
				| start
				|--------------------------------------------------------------------------
				*/
				foreach($prod_dtls_data_arr as $po_id=>$val)
				{
					$job_no_arr[$val['job_no']]= $val['job_no'];
				}
				
				$jobNoCondition = '';
				$noOfjobNo = count($job_no_arr);
				if($db_type == 2 && $noOfjobNo > 1000)
				{
					$jobNoCondition = " and (";
					$jobNoArr = array_chunk($job_no_arr,999);
					foreach($jobNoArr as $job)
					{
						$jobNoCondition.=" c.job_no in('".implode("','",$job)."') or";
					}
					$jobNoCondition = chop($jobNoCondition,'or');
					$jobNoCondition .= ")";
				}
				else
				{
					$jobNoCondition=" and c.job_no in('".implode("','",$job_no_arr)."')";
				}
				
				//echo $jobNoCondition; die;
				//$all_job = "'".implode("','", $jobArr)."'";
				$all_job = $all_jobs;
				$quotation_qty_sql="
					SELECT
						a.id as quotation_id, a.mkt_no, a.sew_smv, a.sew_effi_percent, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.total_cost, b.costing_per_id, c.job_no
					FROM
						wo_price_quotation a,
						wo_price_quotation_costing_mst b,
						wo_po_details_master c
					WHERE
						a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 ".$jobNoCondition."
					ORDER BY
						a.id
				";
				//and c.job_no in(".$all_job."
				//echo $quotation_qty_sql; die();
				$quotation_qty_sql_res = sql_select($quotation_qty_sql);
				$quotation_qty_array = array();
				$quotation_id_array = array();
				
				//don't use
				$all_jobs_array = array();
				//don't use
				$jobs_wise_quot_array = array();
				$quot_wise_arr = array();
				foreach ($quotation_qty_sql_res as $val)
				{
					$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
					$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
					$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
					//$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
					//$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];
				
					$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
					$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];
				
					//$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
					//$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
					//$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
					//$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
					//$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
					//$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
					
					$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
					$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
					$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
					$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
					$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
					//$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
				}
				unset($quotation_qty_sql_res);
				$all_quot_id = implode(",", $quotation_id_array);
				//echo "<pre>";
				//print_r($quot_wise_arr); die;
				
				// print_r($style_wise_arr);die();
				// ===============================================================================
				$sql_fab = "
					SELECT
						a.quotation_id, sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount, b.job_no
					from
						wo_pri_quo_fabric_cost_dtls a,
						wo_po_details_master b
					where
						a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.fabric_source=2 and a.status_active=1 and b.status_active=1
					group by
						a.quotation_id, b.job_no
				";
				//echo $sql_fab; die();
				$data_array_fab=sql_select($sql_fab);
				$fab_summary_data = array();
				$fab_order_price_per_dzn = 1;
				foreach($data_array_fab as $row)
				{
					$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
					if($costing_per_id==1)
					{
						$fab_order_price_per_dzn=12;
					}
					else if($costing_per_id==2)
					{
						$fab_order_price_per_dzn=1;
					}
					else if($costing_per_id==3)
					{
						$fab_order_price_per_dzn=24;
					}
					else if($costing_per_id==4)
					{
						$fab_order_price_per_dzn=36;
					}
					else if($costing_per_id==5)
					{
						$fab_order_price_per_dzn=48;
					}
				
					$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
					$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				}
				unset($data_array_fab);
				//echo "<pre>";
				//print_r($fab_summary_data); die;
				
				// ==================================================================================
				$sql_yarn = "
					SELECT
						a.quotation_id, sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount, b.job_no 
					from
						wo_pri_quo_fab_yarn_cost_dtls a, wo_po_details_master b 
					where
						a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 
					group by
						a.quotation_id,b.job_no
				";
				//echo $sql_yarn; die();
				$data_array_yarn=sql_select($sql_yarn);
				$yarn_summary_data = array();
				foreach($data_array_yarn as $row)
				{
					$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
					if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
					else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
					else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
					else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
					else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
					//$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
					//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
					$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
					// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
					//$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
				}
				unset($data_array_yarn);
				
				// ===================================================================================
				$sql_conversion = "
					SELECT
						a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active, b.body_part_id, b.fab_nature_id, b.color_type_id, b.construction, b.composition, c.job_no
					from
						wo_po_details_master c,
						wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
					where
						a.quotation_id in(".$all_quot_id.") and a.quotation_id=c.quotation_id and a.status_active=1
				";
				//echo $sql_conversion; die();
				$data_array_conversion=sql_select($sql_conversion);
				$conv_order_price_per_dzn = 1;
				$conv_summary_data = array();
				$conversion_cost_arr = array();
				foreach($data_array_conversion as $row)
				{
					$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
					if($costing_per_id==1){$conv_order_price_per_dzn=12;}
					else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
					else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
					else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
					else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
					$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];
					$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
					$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				}
				unset($data_array_conversion);
				//echo "<pre>";
				//print_r($conversion_cost_arr); die();
				
				if($db_type==0)
				{
					$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
					from wo_price_quotation_costing_mst a,wo_po_details_master b
					where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
				}
				if($db_type==2)
				{
					$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
					from wo_price_quotation_costing_mst a,wo_po_details_master b
					where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
				}
				//echo $sql; die();
				$data_array=sql_select($sql);
				foreach( $data_array as $row )
				{
					//$sl=$sl+1;
					if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
					else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
					else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
					else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
					else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
					$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
					//used
					$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;
					//used
					$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
					
					$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
					$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
					
					/*
					//$price_dzn=$row[csf("confirm_price_dzn")];
					//$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
					$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
					$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
					$summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
					$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
					$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
					//$row[csf("commission")]
					$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
					$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
					$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
					$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
					$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
					$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
					$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
					$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
					$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
					$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
					$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
					$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];
					$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
					$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
					$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
					$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
					//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
					$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
					$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
					$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
					//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
					$net_value_dzn=$row[csf("price_with_commn_dzn")];
					$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
					$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;
					//yarn_amount_total_value
					$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
					//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
					$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
					$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
					$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
					$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
					$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
					$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
					$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
					//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
					$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
					$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
					$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
					$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;
				
					//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
					$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
					$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
					$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
					$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
					$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
					$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
					$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
					$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
					$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
					*/
				}
				unset($data_array);
				//echo "<pre>";
				//print_r($summary_data);
				//die();
				
				//======================================================================
				$sql_commi = "
					SELECT
						a.id,a.quotation_id, a.particulars_id, a.commission_base_id, a.commision_rate, a.commission_amount, a.status_active, b.job_no
					from
						wo_pri_quo_commiss_cost_dtls a, wo_po_details_master b
					where
						a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.status_active=1 and a.commission_amount>0 and b.status_active=1
				";
				//echo $sql_commi; die();
				$result_commi=sql_select($sql_commi);
				$CommiData_foreign_cost=0;
				//$CommiData_lc_cost=0;
				//$foreign_dzn_commission_amount=0;
				//$local_dzn_commission_amount=0;
				$CommiData_foreign_quot_cost_arr = array();
				$commision_local_quot_cost_arr = array();
				foreach($result_commi as $row)
				{
					$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
					$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
				
					if($row[csf("particulars_id")]==1) //Foreign
					{
						$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						//$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
						$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						//$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						//$local_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
					}
				}
				unset($result_commi);
				//echo "<pre>su..re";
				//print_r($CommiData_foreign_quot_cost_arr); die();
				
				//=====================================================================================
				$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
				//echo $sql_comm; die();
				$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
				// $summary_data['comm_cost_dzn']=0;
				// $summary_data['comm_cost_total_value']=0;
				$result_comm=sql_select($sql_comm);
				$commer_lc_cost = array();
				$commer_lc_cost_quot_arr = array();
				//$commer_without_lc_cost = array();
				foreach($result_comm as $row)
				{
					$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
					$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
					//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					//$comm_amtPri=$row[csf('amount')];
					//$item_id=$row[csf('item_id')];
					if($row[csf('item_id')] == 1)//LC
					{
						$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					/*
					else
					{
						$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					*/
				}
				unset($result_comm);
				//echo "<pre>";
				//print_r($commer_lc_cost_quot_arr); die();
				
				/*
				|--------------------------------------------------------------------------
				| getting price quotation wise cm valu
				| end
				|--------------------------------------------------------------------------
				*/

				//print_r($cm_gmt_cost_dzn_arr_new);die;
				//die('GO TO HELL!');	
				$buyer_wise_all_info=array();
				foreach($prod_dtls_data_arr as $po_id=>$val)
				{
					$jobs= $val['job_no'];
					$set_ratio=$val['ratio'];
					$cm_dzn_rate=$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"];
					$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
					$cnty_qnty=$po_country_qty*$set_ratio;
					$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
					$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
					
					$unit_price=$val['unit_price'];
					$sewing_cm=($val['sew_out_qty']*$cm_dzn_rate)/12;
					$sewing_fob=$val['sew_out_qty']*$unit_price;

					$exfac_cm=($ex_fact_country_qty*$cm_dzn_rate)/12;
					$exfac_fob=$ex_fact_country_qty*$unit_price;

					$buyer_wise_all_info[$val['buyer_name']]['sew_cm']+=$sewing_cm;
					$buyer_wise_all_info[$val['buyer_name']]['sew_fob']+=$sewing_fob;
					$buyer_wise_all_info[$val['buyer_name']]['ex_cm']+=$exfac_cm;
					$buyer_wise_all_info[$val['buyer_name']]['ex_fob']+=$exfac_fob;
					$target=$line_wise_tg[$po_wise_tg[$po_id]["line"]];
					$buyer_wise_all_info[$val['buyer_name']]['target']+=$target;
					$buyer_wise_all_info[$val['buyer_name']]['inhand_qty']+=$po_wise_tg[$po_id]["qnty"];
					$buyer_wise_all_info[$val['buyer_name']]['inhand_val']+=$po_wise_tg[$po_id]["value"];					 
				}
				
				foreach($prod_dtls_data_arr as $po_id=>$row)
				{
					/*
					|--------------------------------------------------------------------------
					| for price quotation wise cm value LC
					| calculate cm value
					| start
					|--------------------------------------------------------------------------
					*/
	
					$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row['job_no']][101]['conv_amount_total_value']*1;
					$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row['job_no']][30]['conv_amount_total_value']*1;
					$tot_aop_process_amount 		= $conversion_cost_arr[$row['job_no']][35]['conv_amount_total_value']*1;
					
					foreach($style_wise_arr as $style_key=>$val)
					{
						$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
						$total_quot_qty+=$val[('qty')];
						$total_quot_pcs_qty+=$val[('qty_pcs')];
						$total_sew_smv+=$val[('sew_smv')];
						$total_quot_amount+=$total_cost;
						$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
					}
					$total_quot_amount_cal = $style_wise_arr[$row['job_no']]['qty']*$style_wise_arr[$row['job_no']]['final_cost_pcs'];
					$tot_cm_for_fab_cost=$summary_data[$row['job_no']]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
					$commision_quot_local=$commision_local_quot_cost_arr[$row['job_no']];
					$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row['job_no']]+$commer_lc_cost_quot_arr[$row['job_no']]+$freight_cost_data[$row['job_no']]['freight_total_value']);
					$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
					$tot_inspect_cour_certi_cost=$summary_data[$row['job_no']]['inspection_total_value']+$summary_data[$row['job_no']]['currier_pre_cost_total_value']+$summary_data[$row['job_no']]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row['job_no']]['design_pre_cost_total_value'];
					
					$tot_emblish_cost=$summary_data[$row['job_no']]['embel_cost_total_value'];
					$pri_freight_cost_per=$summary_data[$row['job_no']]['freight_total_value'];
					$pri_commercial_per=$commer_lc_cost[$row['job_no']];
					$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$row['job_no']];
					
					$total_btb = $summary_data[$row['job_no']]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row['job_no']]['comm_cost_total_value']+$summary_data[$row['job_no']]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row['job_no']]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row['job_no']]['common_oh_total_value']+$summary_data[$row['job_no']]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
					$tot_quot_sum_amount = $total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
					$total_cm_for_gmt = ($tot_quot_sum_amount-$tot_cm_for_fab_cost-$total_btb);
					$total_quot_pcs_qty = $quotation_qty_array[$row['job_no']]['QTY_PCS'];
	
					$zs[$row['buyer_name']]['total_cm_for_gmt'] += $total_cm_for_gmt;
					$zs[$row['buyer_name']]['total_quot_pcs_qty'] += $total_quot_pcs_qty;
					/*
					|--------------------------------------------------------------------------
					| for price quotation wise cm value LC
					| calculate cm value
					| end
					|--------------------------------------------------------------------------
					*/
					//echo $quotation_qty_array[$row['job_no']]['QTY_PCS']."<br>";
				}
				//echo "<pre>";
				//print_r($zs);
				
				foreach($buyer_data_arr as $buyer_id=>$row)
				{
					/*
					|--------------------------------------------------------------------------
					| for price quotation wise cm value LC
					| calculate cm value
					| start
					|--------------------------------------------------------------------------
					*/
					$cm_lc_dzn = ($total_cm_for_gmt/$total_quot_pcs_qty)*12;
					$cm_lc_pcs = ($zs[$buyer_id]['total_cm_for_gmt']/$zs[$buyer_id]['total_quot_pcs_qty']);
					$cm_lc_value = ($row['sew_out_qty'])*($cm_lc_pcs);
					$cm_lc_value_export = ($buyer_data_arr[$buyer_id]['ex_factory'])*($cm_lc_pcs);
	
					$buyer_wise_all_info[$buyer_id]['sew_cm_lc'] += $cm_lc_value;
					$buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'] += $cm_lc_value_export;
					/*
					|--------------------------------------------------------------------------
					| for price quotation wise cm value LC
					| calculate cm value
					| end
					|--------------------------------------------------------------------------
					*/
				}

				foreach($buyer_data_arr as $buyer_id=>$val)
				{
					 
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						$po_buyer_qty=$po_country_sum_qty[$buyer_id]['po_qty'];
						$sew_fob=$val['sew_fob'];
						$ex_fact_qty=$buyer_data_arr[$buyer_id]['ex_factory'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
						$ex_fac_fob=$buyer_data_arr[$buyer_id]['ex_fac_fob'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
						$grand_sum_sewfob+=$sew_fob;
						$grand_sum_exfob+=$ex_fac_fob;
						$last_cut_qty=$lastmon_buyer_data_arr[$buyer_id]['cut_qty'];
						$last_sew_in_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_in_qty'];
						$last_sew_out_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_out_qty'];
						$last_poly_qty=$lastmon_buyer_data_arr[$buyer_id]['poly_qty'];
						$last_finish_qty=$lastmon_buyer_data_arr[$buyer_id]['finish_qty'];
						$last_ex_fact_sum_qty=$po_ex_fact_sum_qty_lastmon[$buyer_id]['prod_qty'];
						//$last_month_qty=$last_cut_qty+$last_sew_in_qty+$last_sew_out_qty+$last_poly_qty+$last_finish_qty+$last_ex_fact_sum_qty;
						$last_month_qty=$last_ex_fact_sum_qty;
						$lay_percent = ($val['cut_lay_qty']*100)/$po_buyer_qty;
						$cut_percent = ($val['cut_qty']*100)/$po_buyer_qty;

						$summaryHTML.='<tr bgcolor="'.$bgcolor.'">
							<td width="30">'.$k.'</td>
							<td width="120"><div style="word-break:break-all">'.$buyer_short_library[$buyer_id].'</div></td>
							<td style="border:1px solid;" width="100"  align="right">'.$po_buyer_qty.'</td>
							<td width="80" align="right"><p>'.$val['cut_lay_qty'].'</p></td>
							<td width="80" align="right"><p>'.number_format($lay_percent,2).'%</p></td>
							<td width="80" align="right" title="'.$last_cut_qty.'"><p>'.number_format($val['cut_qty'],0).'</p></td>
							<td width="80" align="right" title="'.$last_cut_qty.'"><p>'.number_format($cut_percent,2).'%</p></td>
							<td width="80" align="right" title="'.$last_sew_in_qty.'"><p>'.number_format($val['sew_in_qty'],0).'</p></td>
							<td width="80" align="right" title="'.$last_sew_out_qty.'"><p>'.number_format($val['sew_out_qty'],0).'</p></td>
							<td width="80" align="right"  ><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm'],0).'</p></td>
							<td width="80" align="right"  ><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm_lc'],0).'</p></td>
							<td width="80" align="right"  ><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_fob'],0).'</p></td>
							<td width="80" align="right" title="'.$last_finish_qty.'"><p>'.number_format($val['finish_qty'],0).'</p></td>
							<td width="80" align="right" title="'.$last_ex_fact_sum_qty.'"><p>'.number_format($ex_fact_qty,0).'</p></td>
							<td width="80" align="right"  ><p>'.number_format($buyer_wise_all_info[$buyer_id]['ex_cm'],0).'</p></td>
							<td width="80" align="right"  ><p>'.number_format($buyer_wise_all_info[$buyer_id]['sew_cm_lc_export'],0).'</p></td>
							<td width="80" align="right"  ><p>'.number_format($buyer_wise_all_info[$buyer_id]['ex_fob'],0).'</p></td>
						</tr>';

						$k++;
						$grand_sum_exfact_qty+=$ex_fact_qty;
						$grand_sum_finished_qty+=$val['finish_qty'];
						 
						$grand_sum_out_qty+=$val['sew_out_qty'];
						$grand_sum_in_qty+=$val['sew_in_qty'];
						$grand_sum_cut_lay_qty+=$val['cut_lay_qty'];
						$grand_sum_cut_qty+=$val['cut_qty'];
						$grand_sum_country_qty+=$po_buyer_qty;
						$grand_sum_last_month_qty+=$last_month_qty;
						
				}
						
						
					
                    
                $summaryHTML.='</table>
                <table style="width:'.$sum_width.'px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                	<tfoot>
                		<tr>
                			<th class="alignment_css" width="30">&nbsp;</th>
                			<th class="alignment_css" width="120">Grand Total</th>
                			<th class="alignment_css" width="100" align="right">'.number_format($grand_sum_country_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_cut_lay_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right"></th>
                			<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_cut_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right"></th>
                			<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_in_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_out_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm")),0 ).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm_lc")),0 ).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_fob")),0 ).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_finished_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format($grand_sum_exfact_qty,0).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"ex_cm")),0 ).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"sew_cm_lc_export")),0 ).'</th>
                			<th class="alignment_css" width="80" align="right">'.number_format( array_sum(array_column($buyer_wise_all_info,"ex_fob")),0 ).'</th>
                		</tr>
                	</tfoot>
                </table>
                </div>
             </fieldset>
             </div>';?>
             <? echo $summaryHTML;?>
             <?  $dtls_width=1690; ?>
             <br/>
             <!-- =========================================== DETAILS PART START ==================================== -->
             <div style="width:<? echo $dtls_width+25;?>px;">
             <table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
              <caption><strong>Detail Report</strong></caption>
                <thead>
                    <tr>
                        <th class="alignment_css" width="30">SL</th>
                        <th class="alignment_css" width="100">Buyer</th>                                                      
                        <th class="alignment_css" width="100">PO No</th>                            
                        <th class="alignment_css" width="100"><p>PO Qty</p></th>
                        <th class="alignment_css" width="80">Cut Qty</th>
                        <th class="alignment_css" width="80">Cut Qty %</th>
                        <th class="alignment_css" width="80">Cut. QC</th>
                        <th class="alignment_css" width="80">Cut. QC %</th>
                        <th class="alignment_css" width="80">Sewing <br>Input</th>
                        <th class="alignment_css" width="80">Sewing <br>Output</th>
                        <th class="alignment_css" width="80">CM Dzn <br>Rate BOM</th>
                        <th class="alignment_css" width="80">CM Dzn <br>Rate LC</th>
                        <th class="alignment_css" width="80">Unit Price</th>
                        <th class="alignment_css" width="80">Sew CM <br>Value BOM</th>
                        <th class="alignment_css" width="80">Sew CM <br>Value LC</th>
                        <th class="alignment_css" width="80">Sew FOB <br>Value</th>
                        <th class="alignment_css" width="80">Finishing</th> 
                        <th class="alignment_css" width="80">Export Pcs</th>
                        <th class="alignment_css" width="80">Export CM <br> Value BOM</th>
                        <th class="alignment_css" width="80">Export CM <br> Value LC</th>
                        <th class="alignment_css" width="80">Export FOB <br>Value</th> 
                    </tr>
                </thead>
            </table>

             <div style="width:<? echo $dtls_width+20;?>px; max-height:245px; float:left; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="tableBody">
                <!-- <tr>
                	<th colspan="18" align="left"><?echo $floor_name .' '.$company_name; ?></th>
                </tr> -->
                <?
                 $j=1;$grand_cut_qty=$grand_in_qty=$grand_out_qty=$grand_poly_qty=$grand_finished_qty=$grand_exfact_qty=$grand_country_qty=$grand_country_qty_pcs=0;
                // echo $working_company_id;die;
				/*if($poIds!="")
				{
					$cm_gmt_cost_dzn_arr=array();
					$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_companies_id,$poIds); 
					//print_r($cm_gmt_cost_dzn_arr);die;
				}*/
				$cm_dzn_rate=0;
				 
				foreach($prod_dtls_data_arr as $po_id=>$val)
				{
					 
						// echo "<pre>";
						// print_r($val);
					$cm_dzn_rate=$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"];
					$jobs= $val['job_no'];
					if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$set_ratio=$val['ratio'];
					
					$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
					$cnty_qnty=$po_country_qty*$set_ratio;
					// echo $jobs.' '.$summary_data2[$jobs][$po_id][cm_cost_job]/($cnty_qnty/12);echo "<br>";
					//$cm_part_one=$summary_data2[$jobs][$po_id][cm_cost_job]/($cnty_qnty/12); 
					$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
					$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
					//$cut_lay_qty=$cut_lay_qty_data_arr[$po_id]['cut_lay_qty'];
					
					$unit_price=$val['unit_price'];
				  	$sewing_cm=($val['sew_out_qty']*$cm_dzn_rate)/12;
				  	$sewing_fob=$val['sew_out_qty']*$unit_price;

				  	$exfac_cm=($ex_fact_country_qty*$cm_dzn_rate)/12;
				  	$exfac_fob=$ex_fact_country_qty*$unit_price;
					
					/*
					|--------------------------------------------------------------------------
					| for price quotation wise cm value LC
					| calculate cm value
					| start
					|--------------------------------------------------------------------------
					*/
					$cm_lc_rate = ($zs[$val['buyer_name']]['total_cm_for_gmt']/$zs[$val['buyer_name']]['total_quot_pcs_qty'])*12;
					$cm_lc_pcs = ($zs[$val['buyer_name']]['total_cm_for_gmt']/$zs[$val['buyer_name']]['total_quot_pcs_qty']);
					$cm_lc_value = ($val['sew_out_qty'])*($cm_lc_pcs);
					$cm_lc_value_export = ($ex_fact_country_qty)*($cm_lc_pcs);
		
					//$buyer_wise_all_info[$val['buyer_name']]['cm_lc_rate']  = $cm_lc_rate;
					//$buyer_wise_all_info[$val['buyer_name']]['sew_cm_lc'] += $cm_lc_value;
					//$buyer_wise_all_info[$val['buyer_name']]['sew_cm_lc_export'] += $cm_lc_value_export;
					/*
					|--------------------------------------------------------------------------
					| for price quotation wise cm value LC
					| calculate cm value
					| end
					|--------------------------------------------------------------------------
					*/
					
					if($country_id=="00")
					{
					 	$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty'];
					 	$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
					}
					else
					{
						$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
					}
					//$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($working_company_id,$po_id); 
					
					//print_r($cm_gmt_cost_dzn_arr);
	
				?>
                 
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>">
                <td class="alignment_css" width="30"><? echo $j;?></td>
                <td class="alignment_css" width="100"><div style="word-break:break-all"><? echo  $buyer_short_library[$val['buyer_name']];  ?></div></td>
                
                 
                <td class="alignment_css" width="100"><div style="word-break:break-all"><? echo implode(PHP_EOL, str_split($val["po_number"],15));echo $sub_self=($country_id=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
                
                <td class="alignment_css" width="100"  align="right" title="<? echo 'S '.$set_ratio;?>"><p><? echo $po_button_qty;//$po_country_qty;?></p></td>
                 
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['cut_lay_qty'],0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? $cut_lay_p = ($val['cut_lay_qty']*100)/$po_country_qty_pcs; echo number_format($cut_lay_p,2);?>%</p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['cut_qty'],0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? $cut_qty_p = ($val['cut_qty']*100)/$po_country_qty_pcs; echo number_format($cut_qty_p,2);?>%</p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>

                <td class="alignment_css" width="80" title="From Class=<? echo number_format($cm_dzn_rate,4);?>"  align="right"><p><? echo number_format($cm_dzn_rate,2);?></p></td>
                <td class="alignment_css" width="80" title="From Class=<? echo number_format($cm_lc_rate,4);?>"  align="right"><p><? echo number_format($cm_lc_rate,2);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo implode(PHP_EOL, str_split($unit_price,12));?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_cm,0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($cm_lc_value,0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_fob,0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['finish_qty'],0);?></p></td>
                <td class="alignment_css" width="80"  align="right"><p><? echo number_format($ex_fact_country_qty,0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($exfac_cm,0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($cm_lc_value_export,0);?></p></td>
                <td class="alignment_css" width="80" align="right"><p><? echo number_format($exfac_fob,0);?></p></td>
                </tr>
                <?
                	$j++;
					$grand_exfact_qty+=$ex_fact_country_qty;
					$grand_finished_qty+=$val['finish_qty'];
					$grand_poly_qty+=$val['poly_qty'];
					$grand_out_qty+=$val['sew_out_qty'];
					$grand_in_qty+=$val['sew_in_qty'];
					$grand_cut_lay_qty+=$val['cut_lay_qty'];
					$grand_cut_qty+=$val['cut_qty'];
					$grand_country_qty+=$po_country_qty;
					$grand_country_qty_pcs+=$po_country_qty_pcs;

					$gr_sewing_cm+=$sewing_cm;
					$gr_sewing_fob+=$sewing_fob;

					$gr_exfac_cm+=$exfac_cm ;
					$gr_exfac_fob+=$exfac_fob;
					
					$gr_sewing_cm_lc+=$cm_lc_value;
					$gr_sewing_cm_lc_export+=$cm_lc_value_export;
				}
				?>
                </table>
            </div>
            <div style="width:<? echo $dtls_width+20;?>px;">
                <table style="width:<? echo $dtls_width;?>px" class="tbl_bottom" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all" align="left">

                	<tr>
                		<td class="alignment_css" width="30">&nbsp;</td>
                		<td class="alignment_css" width="100">&nbsp;</td>
                		<td class="alignment_css" width="100">Grand Total:</td>
                		<td class="alignment_css" width="100" align="right"><? //echo number_format($grand_country_qty,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><?// echo number_format($grand_cut_lay_qty_perc,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><?// echo number_format($grand_cut_qty_per,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_in_qty,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_out_qty,0); ?></td>
                		<td class="alignment_css" width="80" align="right"> </td>
                		<td class="alignment_css" width="80" align="right"> </td>
                		<td class="alignment_css" width="80" align="right"> </td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_cm,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_cm_lc,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_fob,0); ?></td>
                		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_finished_qty,0); ?></td>
                		<td class="alignment_css" width="80"  align="right"><? echo number_format($grand_exfact_qty,0); ?></td>
                		<td class="alignment_css" width="80"  align="right"><? echo number_format($gr_exfac_cm,0); ?></td>
                		<td class="alignment_css" width="80"  align="right"><? echo number_format($gr_sewing_cm_lc_export,0); ?></td>
                		<td class="alignment_css" width="80"  align="right"><? echo number_format($gr_exfac_fob,0); ?></td>
                	</tr>
                </table>
                </div>
            </div>
		<?		
	}
		?>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();

    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
	exit();	
}

if ($action=="show_prod_country_size_report")  // All Production Data popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	 $po_break_down_id=str_replace("'","",$po_id);
	 $country_id=str_replace("'","",$country_id);
	 $prod_popup_type=str_replace("'","",$type);
	 $company_name=str_replace("'","",$company_name);
	 if($prod_popup_type=="1"){$subcon_prod_type="1";}
	 else if($prod_popup_type=="4"){$subcon_prod_type="7";}
	 else if($prod_popup_type=="5"){$subcon_prod_type="2";}
	 else if($prod_popup_type=="11"){$subcon_prod_type="5";}
	 else if($prod_popup_type=="8"){$subcon_prod_type="4";}
 	 $process_rpt_type=str_replace("'","",$process_rpt_type);
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	if($country_id=="00")
	{  
		$sizearr_order=return_library_array("SELECT size_id as size_number_id,size_id as size_number_id from subcon_ord_breakdown where order_id=$po_break_down_id","size_number_id","size_number_id");
	}
 	$country_cond="";
	if($country_id>0) $country_cond=" and c.country_id=$country_id";
	$color_cond="";
	if($color_id>0) $color_cond=" and c.color_number_id=$color_id";
	
	$sql_order_size= sql_select("SELECT c.size_number_id, sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty
		from wo_po_color_size_breakdown c 
		where c.status_active in(1,2,3) and c.is_deleted=0 and c.po_break_down_id=$po_break_down_id  $country_cond  
		group by  c.size_number_id order by c.size_number_id");
	foreach($sql_order_size as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]]=$row[csf('order_quantity')];
		$plan_order_size_qnty[$row[csf('size_number_id')]]=$row[csf('plan_cut_qnty')];
	}
	$country_cond="";
	if($country_id>0) $country_cond=" and a.country_id=$country_id";
	$color_size_sql=sql_select( "SELECT a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3)  and a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.") $country_cond");

	$color_size_data=array(); $allcolor_id_arr=array();
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
	}
	if($country_id=="00")
	{
		$color_size_sql=sql_select( "SELECT id, color_id as color_number_id, size_id as size_number_id from subcon_ord_breakdown  where color_id>0 and size_id>0 and order_id in (".$po_break_down_id.") ");

		$color_size_data=array(); $allcolor_id_arr=array();
		foreach($color_size_sql as $row)
		{
			$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
			$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
		}

	}

		
		if($process_rpt_type==7)
		{
 			$country_cond="";
			if($country_id>0) $country_cond=" and c.country_id=$country_id";
			if($color_number_id>0) $color_number_id_cond=" and b.color_id='$color_number_id' ";
			/*$sql_colsize="SELECT b.color_id,b.size_id, sum(b.marker_qty) as production_qnty
			from ppl_cut_lay_mst a, ppl_cut_lay_size b c
			where a.id=b.mst_id and b.order_id=$po_break_down_id and a.entry_date between '$from_date' and '$to_date' $country_cond and a.working_company_id=$company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_id,b.size_id";*/
			 $sql_colsize="select b.color_id,c.size_id, sum(c.size_qty) as production_qnty from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and  c.order_id=$po_break_down_id and a.entry_date between '$from_date' and '$to_date' $country_cond and a.working_company_id in($company_name)    $color_number_id_cond   group by b.color_id,c.size_id";

		}
		 

		else if($process_rpt_type!=6)
		{ 
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			 $sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id and a.po_break_down_id=e.po_break_down_id and e.id=b.color_size_break_down_id and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and  a.production_date between '$from_date' and '$to_date' $country_cond and a.serving_company in($company_name) and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			if($country_id=="00")
			{
				  $sql_colsize="SELECT  b.ord_color_size_id as color_size_break_down_id, (b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where   a.order_id=$po_break_down_id and  a.production_type='$subcon_prod_type' and b.production_type='$subcon_prod_type' and a.company_id in($company_name) and  a.production_date between '$from_date' and '$to_date'  and a.id=b.dtls_id and a.order_id=c.order_id and a.status_active=1 and a.is_deleted=0   and c.id=b.ord_color_size_id";

			}
		}

		else //Ex-Factory
		{
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			$sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_ex_factory_mst a, pro_ex_factory_dtls b 
			where a.id=b.mst_id  and a.po_break_down_id=$po_break_down_id and  	a.ex_factory_date between '$from_date' and '$to_date'  $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			if($country_id=="00")
			{
				$sql_colsize="SELECT c.id as color_size_break_down_id, sum(b.delivery_qty) as production_qnty from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_breakdown c where a.id=b.mst_id and b.order_id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.order_id=$po_break_down_id group by c.id";
			}
		}
		
		    //echo $sql_colsize;
		
		$sql_color_size=sql_select($sql_colsize);
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			if($process_rpt_type==7){
				$prod_color_size_data[$row[csf('color_id')]][$row[csf('size_id')]]["qty"]+=$row[csf('production_qnty')];
			}
			else
			{
				$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["qty"]+=$row[csf('production_qnty')];
			}
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
        <caption> <strong>Details<? echo '  '.$production_process[$process_rpt_type];?></strong></caption>
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? 
				$k=1;
				foreach($allcolor_id_arr as $inc=>$color_id_val) 
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					 ?>
                
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
               
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_prod_poly_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["qty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["qty"]; ?></td> 
                        <?
						$tot_size_qty_arr[$size_id]+=$prod_color_size_data[$color_id_val][$size_id]["qty"];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_poly_qnty,0); ?></td>
                </tr>
                <? 
				$k++;
				} ?>
            </tbody>
            <tfoot>
            <th>Total</th>
            <?
			 $total_size_qty=0;
         	 foreach($sizearr_order as $size_id)
                    {
                        ?>
                     <th align="right"> <? echo number_format($tot_size_qty_arr[$size_id],0); ?></th>
                       <?
					   
					   $total_size_qty+=$tot_size_qty_arr[$size_id];
					}
					 ?>
            <th align="right"> <? echo number_format($total_size_qty,0);?></th>
            </tfoot>
		</table>
    	</div>
    	<?
	exit(); 
}


if ($action=="show_po_country_size_report")  
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');	
	$po_break_down_id=str_replace("'","",$po_id);
	$country_id=str_replace("'","",$country_id);
	$prod_popup_type=str_replace("'","",$type);
	$process_rpt_type=str_replace("'","",$process_rpt_type);

	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name" );
	$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name" );
	
	// ========================== job info ==========================
	$sql_job = "SELECT a.buyer_name,a.company_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, listagg(c.item_number_id,',') WITHIN GROUP (ORDER BY c.item_number_id) AS item_id, SUM(c.order_quantity) AS po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id=$po_break_down_id group by a.buyer_name,a.company_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date";
	$sql_job_res = sql_select($sql_job);

	$total_set_qnty=return_field_value("total_set_qnty","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and b.id in($po_break_down_id)","total_set_qnty");
	//$po_job_Arr_library=return_library_array( "select id,total_set_qnty from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_break_down_id)", "id", "size_name" );
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	if($country_id=="00")
	{
		$sizearr_order=return_library_array("SELECT size_id as size_number_id,size_id as size_number_id from subcon_ord_breakdown where order_id=$po_break_down_id","size_number_id","size_number_id");
	}
	
	$country_cond="";
	if($country_id>0) $country_cond=" and c.country_id=$country_id";
	$color_cond="";
	if($color_id>0) $color_cond=" and c.color_number_id=$color_id";
	if($country_id!="00")
	{
		$sql_order_size= sql_select("SELECT c.size_number_id, c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty
		from wo_po_color_size_breakdown c 
		where c.status_active in(1,2,3) and c.is_deleted=0 and c.po_break_down_id=$po_break_down_id  $country_cond  
		group by  c.color_number_id,c.size_number_id order by c.size_number_id");
	}
	else 
	{
		$sql_order_size_sub= sql_select("SELECT size_id as  size_number_id, color_id as color_number_id,sum(qnty) as order_quantity, sum(plan_cut) as plan_cut_qnty
		from subcon_ord_breakdown  
		where order_id=$po_break_down_id   
		group by  size_id,color_id order by size_id");
	}

	foreach($sql_order_size as $row)
	{
		$order_size_qnty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')]/$total_set_qnty;
		//$plan_order_size_qnty[$row[csf('size_number_id')]]=$row[csf('plan_cut_qnty')];
	}
	foreach($sql_order_size_sub as $row)
	{
		$order_size_qnty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')] ;
		//$plan_order_size_qnty[$row[csf('size_number_id')]]=$row[csf('plan_cut_qnty')];
	}

	$country_cond="";
	if($country_id>0) $country_cond=" and a.country_id=$country_id";
	$color_size_sql=sql_select( "SELECT a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3)  and a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.") $country_cond");
	$color_size_data=array(); $allcolor_id_arr=array();
	if($country_id=="00")
	{
		$color_size_sql=sql_select( "SELECT a.id, a.color_id as color_number_id , a.size_id as size_number_id from subcon_ord_breakdown a where   a.color_id>0 and a.size_id>0 and a.order_id in (".$po_break_down_id.") ");
	}
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
	}
		$table_width=(200+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%;text-align: center;">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        </div>
        <div style="width: 700px;" align="center" id="details_reports">
        	<div style="padding: 15px 0">
        		<?
        		$item_id_arr = array_unique(explode(",", $sql_job_res[0][csf('item_id')]));
        		$item_name = "";
        		foreach ($item_id_arr as $val) 
        		{
        			$item_name .= ($item_name=="") ? $garments_item[$val] : " , ".$garments_item[$val];
        		}
        		?>
        		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="660">
        			<thead>
        				<tr>
        					<th width="80" align="center">Company Name</th>
        					<th width="80" align="center">Buyer Name</th>
        					<th width="80" align="center">Job Number</th>
        					<th width="80" align="center">Style Ref.No</th>
        					<th width="80" align="center">Order No</th>
        					<th width="80" align="center">Ship Date</th>
        					<th width="100" align="center">Item Name</th>
        					<th width="80" align="center">Order Qty.</th>
        				</tr>
        			</thead>
        			<tbody>
        				<tr>
        					<td align="center"><? echo $company_library[$sql_job_res[0][csf('company_name')]] ;?></td>
        					<td align="center"><? echo $buyer_library[$sql_job_res[0][csf('buyer_name')]] ;?></td>
        					<td align="center"><? echo $sql_job_res[0][csf('job_no')] ;?></td>
        					<td align="center"><? echo $sql_job_res[0][csf('style_ref_no')] ;?></td>
        					<td align="center"><? echo $sql_job_res[0][csf('po_number')] ;?></td>
        					<td align="center"><? echo change_date_format($sql_job_res[0][csf('pub_shipment_date')]);?></td>
        					<td align="center"><? echo $item_name ;?></td>
        					<td align="center"><? echo $sql_job_res[0][csf('po_qty')] ;?></td>
        				</tr>
        			</tbody>
        		</table>
        	</div>

			<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	        <caption> <strong>Color Size Details<? //echo '  '.$production_process[$process_rpt_type];?></strong></caption>
	            <thead>
	                <tr>
	                    <th width="100" >Color Name</th>
	                    <?
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
	                        <?
	                    }
	                    ?>
	                    <th width="100" >Total</th>
	                </tr>
	            </thead>
	            <tbody>
	            	<? 
					$k=1;
					foreach($allcolor_id_arr as $inc=>$color_id_val) 
					{
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						 ?>
	                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
	                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
	                    <?
						$total_size_qnty_sum=0;
	                    foreach($sizearr_order as $size_id)
	                    {
							$color_size_qty=$order_size_qnty[$color_id_val][$size_id]['qty'];
	                        ?>
	                        <td align="right"><? echo number_format($color_size_qty,0); $total_size_qnty_sum+=$color_size_qty; ?></td> 
	                        <?
							$tot_size_qty_arr[$size_id]+=$color_size_qty;
	                    }
	                    ?>
	                    <td align="right"><? echo number_format($total_size_qnty_sum,0); ?></td>
	                </tr>
	                <? 
					$k++;
					} ?>
	            </tbody>
	            <tfoot>
	            <th>Total</th>
	            <?
				 $total_size_qty=0;
	         	 foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                     <th align="right"> <? echo number_format($tot_size_qty_arr[$size_id],0); ?></th>
	                       <?
						   
						   $total_size_qty+=$tot_size_qty_arr[$size_id];
						}
						 ?>
	            <th align="right"> <? echo number_format($total_size_qty,0);?></th>
	            </tfoot>
			</table>
    	</div>
    	<?
	exit(); 
}

if($action=="create_summary_excel_view")
{
	print_r($_REQUEST);
}
