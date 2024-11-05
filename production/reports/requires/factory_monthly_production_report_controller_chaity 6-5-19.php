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
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id  in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/floor_wise_sewing_wip_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/floor_wise_sewing_wip_report_controller' );",0 );     	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}
if ($action=="load_drop_down_floor")
{
	 
	
	
	echo create_drop_down( "cbo_floor", 110, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($data) and production_process in (1,5,11) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
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
	if(str_replace("'","",$cbo_floor)==0)
	{
		$floor_cond=""; 
		$floor_cond_subcon="";

	}
	else 
	{
		$floor_cond=" and c.floor_id in(".str_replace("'","",$cbo_floor).")";
		$floor_cond_subcon=" and a.floor_id in(".str_replace("'","",$cbo_floor).")";
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
	else
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
			$sum_width=1470;
			$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";
			 
			ob_start(); 

			?>
			<style type="text/css">
				.alignment_css
				{
					word-wrap: break-word;
					word-break: break-all;
				}
			</style>

		<div  style="width:<? echo $sum_width+30;?>px;  ruby-align:center" >
			<fieldset style="width:<? echo $sum_width+30;?>px;">
                <table width="<? echo $sum_width;?>"  cellspacing="0">
                    <tr style="border:none;">
                        <td colspan="18" align="center" style="border:none; font-size:16px; font-weight:bold">
                        <? echo  'Buyer wise summary '.' ('.$company_names.')'; ?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". change_date_format($start_date).' To '. change_date_format($end_date);?>
                        </td>
                    </tr>
                </table>
                <br />	
                <table class="rpt_table" width="<? echo $sum_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                	<thead>
                		<tr>
                			<td colspan="16"></td>
                			<td colspan="2" align="center"><b>Waiting For Shipment</b></td>
                		</tr>
                		<tr>
                			<th class='alignment_css' width="30">SL</th>
                			<th class='alignment_css' width="120">Buyer</th>
                			<th class='alignment_css' width="100">PO Qty</th>
                			<th class='alignment_css' width="100"><p>Target</p></th>
                			<th class='alignment_css' width="80">Cut Qty</th>
                			<th class='alignment_css' width="80">Cut Qty %</th>
                			<th class='alignment_css' width="80">Cutting QC</th>
                			<th class='alignment_css' width="80">Cutting QC %</th>
                			<th class='alignment_css' width="80">Input.</th>
                			<th class='alignment_css' width="80">Sew Output</th>
                			<th class='alignment_css' width="80">Sewing CM Value</th>
                			<th class='alignment_css' width="80">Sewing FOB Value</th>                           
                			<th class='alignment_css' width="80">Finishing</th> 
                			<th class='alignment_css' width="80">Export Pcs</th>
                			<th class='alignment_css' width="80">Export CM Value</th>
                			<th class='alignment_css' width="80">Export FOB Value</th>
                			<th class='alignment_css' width="80">Inhand Sew. Qty</th> 
                			<th class='alignment_css' width="80">Inhand FOB Value</th> 

                		</tr>
                	</thead>
                </table>
                 <div style="width:<? echo $sum_width+20;?>px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
                    <table class="rpt_table" width="<? echo $sum_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <?
					
				
					 
			//cut and lay qty.....................................
				$buyer_data_arr=array();
				$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
				$sql_cut_lay = "SELECT b.po_total_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				  (d.size_qty) as prod_qty
				 FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
				WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_cut_lay $floor_cond $prod_date_cond7 order by b.id,d.country_id ";
				//echo $sql_cut_lay;die;
				$cut_lay_result_arr=sql_select($sql_cut_lay);
				foreach($cut_lay_result_arr as $rows){
					$buyer_data_arr[$rows[csf('buyer_name')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
					
					$prod_dtls_data_job_arr[$rows[csf('job_no')]] =$rows[csf('job_no')];	
					$prod_dtls_data_po_arr[$rows[csf('po_id')]] =$rows[csf('po_id')];	
					$prod_dtls_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['style']=$rows[csf('style_ref_no')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_name']=$rows[csf('buyer_name')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['buyer_client']=$rows[csf('buyer_client')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['po_number']=$rows[csf('po_number')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['ratio']=$rows[csf('ratio')];
					$prod_dtls_data_arr[$rows[csf('po_id')]]['job_no']=$rows[csf('job_no')];
					$total_po_amount_arr[$rows[csf('job_no')]]+=$rows[csf('po_total_price')];
					
					$tmp_count=$rows[csf('country_id')];
					$tmp_po[$rows[csf('po_id')]]=$rows[csf('po_id')];
					//$cut_lay_qty_data_arr[$rows[csf('po_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];

				}
				// echo "<pre>";
				// print_r($prod_dtls_data_po_arr);die;
				// echo "</pre>";
				//cut and lay qty.....................................	
					  
					  				  
					$sql_prod_sum = "SELECT b.unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty,
							  (CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty,
							  (CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty,
							  (CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty,
							  (CASE WHEN c.production_type=11 and d.production_type=11 THEN d.production_qnty ELSE 0 END)  as poly_qty,
							  (CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty
							 FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
					WHERE  a.job_no=b.job_no_mst and a.job_no=e.job_no_mst and c.po_break_down_id=b.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,4,5,11,8) and d.production_type in(1,4,5,11,8)  and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)  $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";

					$subcon_sql_prod="SELECT  c.rate as unit_price,b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client, b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio,  
							  (CASE WHEN a.production_type=1 and d.production_type=1 THEN d.prod_qnty ELSE 0 END)  as cut_qty,

							  (CASE WHEN a.production_type=7  and d.production_type=7 THEN d.prod_qnty ELSE 0 END)  as sew_in_qty, 
							  (CASE WHEN a.production_type=2  and d.production_type=2 THEN d.prod_qnty ELSE 0 END)  as sew_out_qty,
							  (CASE WHEN a.production_type=5 and d.production_type=5 THEN d.prod_qnty ELSE 0 END)  as poly_qty,
							  (CASE WHEN a.production_type=4 and d.production_type=4  THEN d.prod_qnty ELSE 0 END)  as finish_qty
							   from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(1,2,4,5,7) and d.production_type in(1,2,4,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";

						
					$sum_result=sql_select($sql_prod_sum);
					$sum_result_subcon=sql_select($subcon_sql_prod);
 					
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
						
					 } 

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
						
					 } 


					
				$location_cond_ex = str_replace("c.location", "c.location_id", $location_cond);
				$floor_cond2=str_replace("c.floor_id", "c.delivery_floor_id", $floor_cond);
				$sql_prod_exf = "SELECT b.unit_price, a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.job_no,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			  	(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty
					 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
				WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_ex $floor_cond2  $prod_date_cond2 order by b.id,d.country_id ";

				$subcon_sql_ex="SELECT  c.rate as unit_price, b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0       $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";


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
					$prod_dtls_data_job_arr[$row[csf('job_no')]] =$row[csf('job_no')];		
					$prod_dtls_data_po_arr[$row[csf('po_id')]] =$row[csf('po_id')];		
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
					if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
					if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
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
					$prod_dtls_data_arr[$row[csf('po_id')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
					$prod_dtls_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];	
					//$prod_dtls_data_job_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];	
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
					if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
					if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
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
					 $condition= new condition();
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

					 //echo $all_jobs.;die;
					 if(str_replace("'","",$all_jobs)!='')
					 {
					 	$condition->job_no("in($all_jobs)");
					 }
					 if($txt_order_id!='' || $txt_order_id!=0)
					 {
					 	//$condition->po_id("in($txt_order_id)"); 
					 }
					 
					 $sql_job="SELECT a.job_no_prefix_num as job_prefix,a.job_no, a.gmts_item_id,a.avg_unit_price,a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.file_no,b.po_quantity,b.plan_cut,b.po_total_price, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in($all_jobs)";
					 foreach(sql_select( $sql_job) as $rows)
					 {
					 	$total_po_amount_arr[$rows[csf('job_no')]] +=$rows[csf('po_total_price')];
					 	$total_po_amount_arr2[$rows[csf('job_no')]][$rows[csf('po_id')]]+=$rows[csf('po_total_price')];
					 	$job_wise_po_arr[$rows[csf('job_no')]][$rows[csf('po_id')]]=$rows[csf('job_no')];
					 	$job_wise_arr[$rows[csf("job_no")]][$rows[csf("po_id")]]['po_amount']+=$rows[csf('po_total_price')];
					 }
					 //print_r($job_wise_po_arr);die;


					 //class info start

					 $condition->init();
					 $fabric= new fabric($condition);
					 $fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
					 $fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

					 $yarn= new yarn($condition);
					$conversion= new conversion($condition);
					 $trim= new trims($condition);
					 $emblishment= new emblishment($condition);
					 $wash= new wash($condition);
					 $other= new other($condition);
					 $other_cost=$other->getAmountArray_by_job();
					 //print_r($other_cost);die;
					 $commercial= new commercial($condition);
					 $commision= new commision($condition);
					 $commision_cost_arr=$commision->getAmountArray_by_job();
					 $commision_item_cost_arr=$commision->getAmountArray_by_jobAndItemid();
 					 $po_qty=0;
					 $po_plun_cut_qty=0;
					 $total_set_qnty=0;
					   

					 $pro_sql=sql_select("SELECT  a.production_type, b.unit_price,a.id,a.po_break_down_id, a.sewing_line,sum(case WHEN a.production_type=5 then a.production_quantity else 0 end ) as sew,  sum(case WHEN a.production_type=8 then production_quantity else 0 end ) as fin  FROM pro_garments_production_mst a,wo_po_break_down b  where a.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 and  a.production_type in(5,8) and b.id in($txt_order_id) group by  a.production_type,  b.unit_price,  a.id, a.po_break_down_id, a.sewing_line ORDER BY a.ID asc  ");
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

					 /*
					 $sql_po="SELECT a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty 
					 from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id 
					 and a.job_no in(".$all_jobs.")   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
					 order by b.id";
					 $sql_po_data=sql_select($sql_po);
					 foreach($sql_po_data as $sql_po_row)
					 {
					 	$po_qty+=$sql_po_row[csf('order_quantity')];
					 	$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
					 	$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
					 }*/

					 $pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					 foreach($pre_cost as $row)
					 {
					 	$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					 	$costing_date_arr[$row[csf('job_no')]]=$row[csf('costing_date')];
					 }

					 $company_cons=" and company_id in($company)" ;

					 $sql_std_para=sql_select("select id,interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date,operating_expn from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 and cost_per_minute>0 $company_cons order by id desc");	

					$financial_para_arr=array();
					foreach($sql_std_para as $row )
					{
						$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
						$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
						$diff=datediff('d',$applying_period_date,$applying_period_to_date);
						for($j=0;$j<$diff;$j++)
						{
							//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
								$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
								$financial_para_arr[$newdate]['operating_expn']=$row[csf('operating_expn')];
							if($row[csf("income_tax")]>0)
							{
								$financial_para_arr[$newdate]['income_tax']=$row[csf('income_tax')];
							}
							if($row[csf("interest_expense")]>0)
							{
								$financial_para_arr[$newdate]['interest_expense']=$row[csf('interest_expense')];
							}
						}
						
						$cost_per_minute=$row[csf("cost_per_minute")];
				
					}



					 $sql_new = "SELECT job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,studio_cost,studio_percent, total_cost , total_cost_percent, price_dzn,price_dzn_percent, margin_dzn,margin_dzn_percent, price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
					 from wo_pre_cost_dtls
					 where job_no in(".$all_jobs.") and status_active=1 and is_deleted=0";
					 $data_array_new=sql_select($sql_new);
					 $summary_data=array();

					 foreach( $data_array_new as $row_new )
					 {

					 	$costing_per=$costing_per_arr[$row_new[csf('job_no')]];

					 	if($costing_per==1)
					 	{
					 		$order_price_per_dzn=12;
					 		$costing_for=" DZN";
					 	}
					 	else if($costing_per==2)
					 	{
					 		$order_price_per_dzn=1;
					 		$costing_for=" PCS";
					 	}
					 	else if($costing_per==3)
					 	{
					 		$order_price_per_dzn=24;
					 		$costing_for=" 2 DZN";
					 	}
					 	else if($costing_per==4)
					 	{
					 		$order_price_per_dzn=36;
					 		$costing_for=" 3 DZN";
					 	}
					 	else if($costing_per==5)
					 	{
					 		$order_price_per_dzn=48;
					 		$costing_for=" 4 DZN";
					 	}
					 	$order_job_qnty=$row[csf("job_quantity")];
					 	$avg_unit_price=$row[csf("avg_unit_price")];


					 	$summary_data[$row_new[csf('job_no')]][price_dzn]=$row_new[csf("price_dzn")];
					 	$summary_data[$row_new[csf('job_no')]][commission]=$row_new[csf("commission")];

					 	$summary_data[$row_new[csf('job_no')]][trims_cost]=$row_new[csf("trims_cost")];
					 	$summary_data[$row_new[csf('job_no')]][emb_cost]=$row_new[csf("embel_cost")];

					 	$summary_data[$row_new[csf('job_no')]][lab_test]=$row_new[csf("lab_test")];
					 	$summary_data[$row_new[csf('job_no')]][lab_test_job]=$other_cost[$row_new[csf("job_no")]]['lab_test'];

					 	$summary_data[$row_new[csf('job_no')]][inspection]=$row_new[csf("inspection")];
					 	$summary_data[$row_new[csf('job_no')]][inspection_job]=$other_cost[$row_new[csf("job_no")]]['inspection'];

					 	$summary_data[$row_new[csf('job_no')]][freight]=$row_new[csf("freight")];
					 	$summary_data[$row_new[csf('job_no')]][freight_job]=$other_cost[$row_new[csf("job_no")]]['freight'];

					 	$summary_data[$row_new[csf('job_no')]][currier_pre_cost]=$row_new[csf("currier_pre_cost")];
					 	$summary_data[$row_new[csf('job_no')]][currier_pre_cost_job]=$other_cost[$row_new[csf("job_no")]]['currier_pre_cost'];

					 	$summary_data[$row_new[csf('job_no')]][certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
					 	$summary_data[$row_new[csf('job_no')]][certificate_pre_cost_job]=$other_cost[$row_new[csf("job_no")]]['certificate_pre_cost'];

					 	$summary_data[$row_new[csf('job_no')]][studio_cost]=$row_new[csf("studio_cost")];
					 	$summary_data[$row_new[csf('job_no')]][studio_cost_job]=$other_cost[$row_new[csf("job_no")]]['studio_cost'];
					 	$studio_job_cost_arr[$row_new[csf("job_no")]]['studio_cost']=$row_new[csf("studio_percent")];
					 	$studio_job_cost_arr[$row_new[csf("job_no")]]['common_oh']=$row_new[csf("common_oh_percent")];

					 	$summary_data[$row_new[csf('job_no')]][wash_cost]=$row_new[csf("wash_cost")];

					 	$summary_data[$row_new[csf('job_no')]][OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")]+$row_new[csf("studio_cost")];

					 	$summary_data[$row_new[csf('job_no')]][OtherDirectExpenses_job]=$summary_data[$row_new[csf('job_no')]][lab_test_job]+$summary_data[$row_new[csf('job_no')]][inspection_job]+$summary_data[$row_new[csf('job_no')]][currier_pre_cost_job]+$summary_data[$row_new[csf('job_no')]][certificate_pre_cost_job];

					 	$summary_data[$row_new[csf('job_no')]][cm_cost]=$row_new[csf("cm_cost")];
					 	$summary_data[$row_new[csf('job_no')]][cm_cost_job]=$other_cost[$row_new[csf("job_no")]]['cm_cost'];

					 	$summary_data[$row_new[csf('job_no')]][comm_cost]=$row_new[csf("comm_cost")]; 

					 	$summary_data[$row_new[csf('job_no')]][common_oh]=$row_new[csf("common_oh")];
					 	$summary_data[$row_new[csf('job_no')]][common_oh_job]=$other_cost[$row_new[csf("job_no")]]['common_oh'];
					 	$summary_data[$row_new[csf('job_no')]][depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
					 	$summary_data[$row_new[csf('job_no')]][depr_amor_pre_cost_job]=$other_cost[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
					 }
		 
					 $fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

					 $fabric_amount_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

					 $sql_fabric = "select a.job_no,a.uom, sum(a.amount) as amount,b.po_break_down_id  from wo_pre_cost_fabric_cost_dtls a ,wo_pre_cos_fab_co_avg_con_dtls b where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no in(".$all_jobs.")  and a.fabric_source=2 and a.status_active=1 group by  a.job_no, a.uom,b.po_break_down_id";
					 $data_arr_fabric=sql_select($sql_fabric);
					 foreach($data_arr_fabric as $fab_row)
					 {
					 	$plan_cut=$po_wise_arr[$fab_row[csf("po_break_down_id")]]['plan_cut'];
					 	$tot_fabric_amount=$fabric_amount_arr['knit']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
					 	$tot_fabric_amount2=$fabric_amount_arr['woven']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
					 	$tot_fabric_amountFb=$tot_fabric_amount+$tot_fabric_amount2;
					 	$dzn_fabric_amount=($tot_fabric_amountFb/$plan_cut)*12;

					 	if($tot_fabric_amount2>0 || $tot_fabric_amount>0)
					 	{
					 	}
					 	$summary_data[$fab_row[csf('job_no')]][fabric_cost]+=$dzn_fabric_amount;
					 	$summary_data[$fab_row[csf('job_no')]][fabric_cost_job]+=$tot_fabric_amount2+$tot_fabric_amount;
					 }


					 $totYarn=0;
					 /*$YarnData=array();

					 $yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

					 $sql_yarn="select f.job_no , f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,color,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where   f.job_no in(".$all_jobs.") and f.is_deleted=0 and f.status_active=1  order by f.id";
					 $data_arr_yarn=sql_select($sql_yarn);
					 foreach($data_arr_yarn as $yarn_row)
					 {
					 	$yarnrate=$yarn_row[csf("rate")];
					 	$summary_data[$yarn_row[csf('job_no')]][yarn_cost][$yarn_row[csf("yarn_id")]]+=$yarn_row[csf("amount")];
					 	$summary_data[$yarn_row[csf('job_no')]][yarn_cost_job]+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];

					 	$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
					 	$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
					 	$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
					 	$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
					 	$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
					 	$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
					 }*/
				
			 
					
			
			
			//print_r($job_wise_po_arr) ;echo "555"; 
			foreach($job_wise_po_arr  as $jobkey=>$ord_data)
				{
					foreach($ord_data as $ord_id=>$vals)
					{

							$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in('".$jobkey."') and emb_name in(1,2,4,5) and status_active=1";
							$data_array=sql_select($sql);
							$condition= new condition();
							$condition->init();
							$condition->po_id("in($ord_id)"); 

							$emblishment= new emblishment($condition);

							$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
							$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
							$totEmb=0;
							$EmbData=array();
							foreach( $data_array as $row )
							{
								$embqty=$emblishment_qty[$jobkey][$row[csf("id")]];
								$embamount=$emblishment_amount[$jobkey][$row[csf("id")]];
								$summary_data[$jobkey][emb_cost_job]+=$embamount;
								$summary_data2[$jobkey][$ord_id][emb_cost_job]+=$embamount;
								$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
								$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
								$EmbData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
								$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
								$EmbData[$row[csf("id")]]['amount']=$row[csf("amount")];
								$EmbData[$row[csf("id")]]['tot_cons']=$embqty;
								$EmbData[$row[csf("id")]]['tot_amount']=$embamount;
								$totEmb+=$row[csf("cons_dzn_gmts")];
							}

							//echo "23";
							$trim= new trims($condition);
							//echo "string";die;
							//echo "string";die;

							$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
							from wo_pre_cost_trim_cost_dtls  
							where job_no in('".$jobkey."') and status_active=1  order by id";
							$data_array_trim=sql_select($sql_trim);
							$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
							$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
							$totTrim=0;
							$TrimData=array();
							foreach( $data_array_trim as $row_trim )
							{ 
								$trim_qty=$trim_qty_arr[$row_trim[csf("id")]];
								$trim_amount=$trim_amount_arr[$row_trim[csf("id")]];
								$summary_data2[$jobkey][$ord_id][trims_cost_job]+=$trim_amount;
								$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
								$TrimData[$row_trim[csf('id')]]['description']=$row_trim[csf('description')];
								$TrimData[$row_trim[csf('id')]]['brand_sup_ref']=$row_trim[csf('brand_sup_ref')];
								$TrimData[$row_trim[csf('id')]]['remark']=$row_trim[csf('remark')];
								$TrimData[$row_trim[csf('id')]]['cons_uom']=$row_trim[csf('cons_uom')];
								$TrimData[$row_trim[csf('id')]]['cons_dzn_gmts']=$row_trim[csf('cons_dzn_gmts')];
								$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
								$TrimData[$row_trim[csf('id')]]['amount']=$row_trim[csf('amount')];
								$TrimData[$row_trim[csf('id')]]['apvl_req']=$row_trim[csf('apvl_req')];
								$TrimData[$row_trim[csf('id')]]['nominated_supp']=$row_trim[csf('nominated_supp')];
								$TrimData[$row_trim[csf('id')]]['tot_cons']=$trim_qty;
								$TrimData[$row_trim[csf('id')]]['tot_amount']=$trim_amount;
								$totTrim+=$row_trim[csf('cons_dzn_gmts')];
							}  


							$other= new other($condition);
							$other_cost=$other->getAmountArray_by_job();
							//echo"<pre>";print_r($other_cost);echo"</pre>"."<br>";
							$summary_data2[$jobkey][$ord_id][lab_test_job]=$other_cost[$jobkey]['lab_test'];
							$summary_data2[$jobkey][$ord_id][inspection_job]=$other_cost[$jobkey]['inspection'];
							$summary_data2[$jobkey][$ord_id][freight_job]=$other_cost[$jobkey]['freight'];
							$summary_data2[$jobkey][$ord_id][currier_pre_cost_job]=$other_cost[$jobkey]['currier_pre_cost'];
							$summary_data2[$jobkey][$ord_id][certificate_pre_cost_job]=$other_cost[$jobkey]['certificate_pre_cost'];
							//$summary_data2[$jobkey][$ord_id][studio_cost_job]=$other_cost[$jobkey]['studio_cost'];
							$summary_data2[$jobkey][$ord_id][cm_cost_job]=$other_cost[$jobkey]['cm_cost'];
							//$summary_data2[$jobkey][$ord_id][common_oh_job]=$other_cost[$jobkey]['common_oh'];
							$summary_data2[$jobkey][$ord_id][depr_amor_pre_cost_job]=$other_cost[$jobkey]['depr_amor_pre_cost'];
							$jno=$jobkey;

							$costing_date=$costing_date_arr[$jno];
							$total_po_amount_cal=$total_po_amount_arr2[$jno][$ord_id];
							//$condition= new condition();
							//$condition->init();
							//$condition->po_id("in($ord_id)");
							//$other= new other($condition);
							//$other_cost=$other->getAmountArray_by_job();

							$commision_local=$commision_local_job_cost_arr[$jno];
							//echo $commision_local.'F';
							$commision_job_cost_foreign=$commision_job_cost_arr[$jno];
							$commarcial_job_lc=0;
							$commarcial_job_lc=$commarcial_job_amount[$jno];
							$other_job=$other_cost[$jno]['freight'];
							$studio_job_cost=$studio_job_cost_arr[$jno]['studio_cost'];
							$common_oh=$studio_job_cost_arr[$jno]['common_oh'];

							$tot_sum_amount_job_calc=$total_po_amount_cal-($commision_job_cost_foreign+$commarcial_job_lc+$other_job);
							//echo $total_po_amount_cal.'<br>';
							$costing_date=change_date_format($costing_date,'','',1);
							//$operating_expn=0;
							$operating_expn=$financial_para_arr[$costing_date]['operating_expn'];
							//echo $tot_sum_amount_job_calc.'dfdd'.$operating_expn.'<br>';
						    $tot_operating_expense=($tot_sum_amount_job_calc*$operating_expn)/100;
							$tot_studio_job_wise_cost=($tot_sum_amount_job_calc*$studio_job_cost)/100;
							
							$income_tax=$financial_para_arr[$costing_date]['income_tax'];
							$interest_expense=$financial_para_arr[$costing_date]['interest_expense'];
							//echo $tot_sum_amount_job_calc.'='.$commision_local.'<br/>';
							$total_job_income_tax_val+=($total_po_amount_cal*$income_tax)/100;
							$total_job_interest_exp_val+=($total_po_amount_cal*$interest_expense)/100;
							$total_job_commision_local_val+=($tot_sum_amount_job_calc*$commision_local)/100;
							$total_job_amount_cal+=$total_po_amount_arr[$jno];
							$total_job_net_amount_cal+=$tot_sum_amount_job_calc;
							$tot_income_tax_dzn=($total_job_income_tax_val/$total_job_amount_cal)*12;
							$tot_interest_exp_dzn=($total_job_interest_exp_val/$total_job_amount_cal)*12;
							$tot_studio_dzn=($tot_studio_job_wise_cost/$total_job_net_amount_cal)*12;
							$tot_commision_local_dzn=($total_job_commision_local_val/$total_job_net_amount_cal)*12;

							$summary_data[$jno][OtherDirectExpenses_job]+=$tot_studio_job_wise_cost;
							//$summary_data[OtherDirectExpenses]+=$tot_commision_local_dzn;
							$summary_data2[$jno][$ord_id][studio_cost]+=$tot_studio_dzn;
							$summary_data2[$jno][$ord_id][common_oh_job]+=$tot_operating_expense;
							$summary_data2[$jno][$ord_id][studio_cost_job]=$tot_studio_job_wise_cost;






							$totConv=0;
							$ConvData=array();			
							$conv_data=array();
							$conversion= new conversion($condition);
							$conv_amount_arr=$conversion->getAmountArray_by_conversionid();
							$conv_qty_arr=$conversion->getQtyArray_by_conversionid();
							$conv_process_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
							$sql_conv = "select a.id as con_id, a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty,a.avg_req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.uom  from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id where a.job_no in('".$jobkey."') and a.status_active=1 ";
							$data_arr_conv=sql_select($sql_conv);
							foreach($data_arr_conv as $conv_row)
							{
								$convamount=$conv_amount_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
								$convQty=$conv_qty_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
								$conv_data[cons_process][$conv_row[csf('cons_process')]]=$conv_row[csf('cons_process')];
								$conv_data[job_no][$conv_row[csf('cons_process')]].=$conv_row[csf('job_no')].',';
								$conv_data[amount][$conv_row[csf('cons_process')]]=$conv_row[csf('amount')];
								$conv_data[amount_job][$conv_row[csf('con_id')]]=$convamount;
								$summary_data2[$jobkey][$ord_id][conver_cost_job]+=$convamount;
								$index=$conv_row[csf('con_id')];
								$ConvData[$index]['item_descrition']=$body_part[$conv_row[csf("body_part_id")]].", ".$color_type[$conv_row[csf("color_type_id")]].", ".$conv_row[csf("fabric_description")];
								$ConvData[$index]['cons_process']=$conv_row[csf("cons_process")];
								$ConvData[$index]['req_qnty']=$conv_row[csf("req_qnty")];
								$ConvData[$index]['uom']=$conv_row[csf("uom")];
								$ConvData[$index]['charge_unit']=$conv_row[csf("charge_unit")];
								$ConvData[$index]['amount']=$conv_row[csf("amount")];
								$ConvData[$index]['tot_req_qnty']=$convQty;
								$ConvData[$index]['tot_amount']=$convamount;
								$totConv=$conv_row[csf("req_qnty")];
							}



							 $YarnData=array();
					 		 $yarn= new yarn($condition);
							 $yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

							 $sql_yarn="select f.job_no , f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,color,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where   f.job_no in('".$jobkey."') and f.is_deleted=0 and f.status_active=1  order by f.id";
							 $data_arr_yarn=sql_select($sql_yarn);
							 foreach($data_arr_yarn as $yarn_row)
							 {
							 	$yarnrate=$yarn_row[csf("rate")];
							 	$summary_data2[$jobkey][$ord_id][yarn_cost][$yarn_row[csf("yarn_id")]]+=$yarn_row[csf("amount")];
							 	$summary_data2[$jobkey][$ord_id][yarn_cost_job]+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];

							 	$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
							 	$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
							 	$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
							 	$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
							 	$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
							 	$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
							 }
							 $sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in('".$jobkey."') and emb_name =3 and status_active=1";
							 $data_array=sql_select($sql);
							 //$condition= new condition();
							 //$condition->init();
							 //$condition->po_id("in($ord_id)"); 
							 $wash= new wash($condition);
							 $wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
							 $wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
							//print_r($wash_amount);
							//echo "aa <br>";
							 foreach( $data_array as $row )
							 {
							 	$washqty=$wash_qty[$row[csf("job_no")]][$row[csf("id")]];
							 	$washamount=$wash_amount[$jobkey][$row[csf("id")]];
							 	$summary_data2[$jobkey][$ord_id][wash_cost_job]+=$washamount;
							 	$summary_data[$row[csf('job_no')]][OtherDirectExpenses_job]+=$washamount;
							 	$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
							 	$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
							 	$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
							 	$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
							 	$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
							 	$EmbData[$row[csf("id")]]['tot_cons']+=$washqty;
							 	$EmbData[$row[csf("id")]]['tot_amount']+=$washamount;
							 	$totEmb+=$row[csf("cons_dzn_gmts")];
							 }


							 $sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where commision_rate>0 and job_no in('".$jobkey."') and status_active=1 ";
							 $data_array=sql_select($sql);
							 $commision= new commision($condition);
							 $commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
							 $totCommi=$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
							 $CommiData=array();
							 foreach( $data_array as $row )
							 { 
							 	$commisionamount=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];

							 	$summary_data2[$row[csf('job_no')]][$ord_id][commission_job]+=$commisionamount;
							 	$CommiData[$row[csf("id")]]['particulars_id']=$row[csf("particulars_id")];
							 	$CommiData[$row[csf("id")]]['commission_base_id']=$row[csf("commission_base_id")];
							 	$CommiData[$row[csf("id")]]['commision_rate']=$row[csf("commision_rate")];
							 	$CommiData[$row[csf("id")]]['commission_amount']=$row[csf("commission_amount")];
							 	$CommiData[$row[csf("id")]]['tot_commission_amount']=$commisionamount;

							 	$totCommi+=$row[csf("commission_amount")];
							 	if($row[csf("particulars_id")]==1)  
							 	{
							 		$CommiData_foreign_cost=$commision_item_cost_arr[$row[csf("job_no")]][1];
							 		$foreign_dzn_commission_amount=$row[csf("commission_amount")];
							 		$commision_job_cost_arr[$row[csf("job_no")]]+=$commision_item_cost_arr[$row[csf("job_no")]][1];

							 	}
							 	else
							 	{
							 		$CommiData_lc_cost=$commision_item_cost_arr[$row[csf("job_no")]][2];

							 		$local_dzn_commission_amount=$row[csf("commission_amount")];
							 		$commision_local_job_cost_arr[$row[csf("job_no")]]+=$row[csf("commision_rate")];
							 	}	
							 }



							 $commercial= new commercial($condition);
							 $sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where rate>0 and job_no in('".$jobkey."') and status_active=1 ";
							 $data_array=sql_select($sql);
							 $commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
							 $commarcial_item_amount=$commercial->getAmountArray_by_jobAndItemid();
							 $totCommar=$commer_lc_cost=$commer_without_lc_cost=0;
							 $CommarData=array();
							 foreach( $data_array as $row )
							 { 
							 	$item_id=$row[csf("item_id")];
							 	$commarcialamount=$commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
							 	$summary_data2[$row[csf('job_no')]][$ord_id][comm_cost_job]+=$commarcialamount;
							 	$CommarData[$row[csf("id")]]['item_id']=$row[csf("item_id")];
							 	$CommarData[$row[csf("id")]]['rate']=$row[csf("rate")];
							 	$CommarData[$row[csf("id")]]['amount']=$row[csf("amount")];
							 	$CommarData[$row[csf("id")]]['tot_amount']=$commarcialamount;
							 	$totCommar=$row[csf("amount")];
							 	if($item_id==1)
							 	{
							 		$commer_lc_cost=$commarcial_item_amount[$row[csf("job_no")]][$item_id];
							 		$tot_lc_dzn_Commar=$row[csf("amount")];
							 		$commarcial_job_amount[$row[csf("job_no")]]+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];

							 	}
							 	else
							 	{
							 		$commer_without_lc_cost=$commarcial_item_amount[$row[csf("job_no")]][$item_id];
							 		$totCommar=$row[csf("amount")];
							 		$tot_without_lc_dzn_Commar=$row[csf("amount")];

							 	}
							 }
							 $summary_data2[$row[csf('job_no')]][$ord_id][OtherDirectExpenses_job]=+$CommiData_lc_cost;
							 $summary_data2[$row[csf('job_no')]][$ord_id][OtherDirectExpenses]+=$local_dzn_commission_amount;
							 $summary_data2[$row[csf('job_no')]][$ord_id][comm_cost]+=$tot_without_lc_dzn_Commar;

							 foreach($conv_data[cons_process] as $key => $value)
							 { 

							 	$job_no=rtrim($conv_data[job_no][$key],',');
							 	$job_nos=array_unique(explode(",",$job_no));
							 	$process_amount=0; 
							 	foreach($job_nos as $jno)
							 	{
							 		if($key==101)
							 		{
							 			$tot_dye_chemi_process_amount[$jno][$ord_id]+=array_sum($conv_process_amount_arr[$jno][$key]);
							 		}
							 		else if($key==30)
							 		{
							 			$tot_yarn_dye_process_amount[$jno][$ord_id]+=array_sum($conv_process_amount_arr[$jno][$key]);
							 		}
							 		else if($key==35)  
							 		{

							 			$tot_aop_process_amount[$jno][$ord_id]+=array_sum($conv_process_amount_arr[$jno][$key]);
							 		}
							 		$process_amount[$jno][$ord_id]+=array_sum($conv_process_amount_arr[$jno][$key]);
							 	}
							 }






					}
				}

				 
				//print_r($summary_data2);die;
				
				 

				


				//End Wash cost Cost part report here -------------------------------------------
				//Commision cost Cost part report here -------------------------------------------
			
			//	echo $foreign_dzn_commission_amount.'='.$local_dzn_commission_amount;
				//End Commision cost Cost part report here -------------------------------------------
				//Commarcial cost Cost part report here -------------------------------------------
				


				$all_jobs_no="";$tot_operating_expense=$tot_sum_amount_calc=$total_job_income_tax_val=$total_job_amount_cal=$total_job_interest_exp_val=$tot_studio_job_wise_cost=$total_job_commision_local_val=0;
				/*foreach($job_wise_po_arr  as $jno=>$ord_data)
				{
					foreach($ord_data as $ord_id=>$vals)
					{
					 
							//if($all_jobs_no=="") $all_jobs_no=$jno; else $all_jobs_no.=",".$jno;
							$costing_date=$costing_date_arr[$jno];
							$total_po_amount_cal=$total_po_amount_arr2[$jno][$ord_id];
							$condition= new condition();
							$condition->init();
							$condition->po_id("in($ord_id)");
							$other= new other($condition);
							$other_cost=$other->getAmountArray_by_job();

							$commision_local=$commision_local_job_cost_arr[$jno];
							//echo $commision_local.'F';
							$commision_job_cost_foreign=$commision_job_cost_arr[$jno];
							$commarcial_job_lc=0;
							$commarcial_job_lc=$commarcial_job_amount[$jno];
							$other_job=$other_cost[$jno]['freight'];
							$studio_job_cost=$studio_job_cost_arr[$jno]['studio_cost'];
							$common_oh=$studio_job_cost_arr[$jno]['common_oh'];

							$tot_sum_amount_job_calc=$total_po_amount_cal-($commision_job_cost_foreign+$commarcial_job_lc+$other_job);
							//echo $total_po_amount_cal.'<br>';
							$costing_date=change_date_format($costing_date,'','',1);
							//$operating_expn=0;
							$operating_expn=$financial_para_arr[$costing_date]['operating_expn'];
							//echo $tot_sum_amount_job_calc.'dfdd'.$operating_expn.'<br>';
						    $tot_operating_expense=($tot_sum_amount_job_calc*$operating_expn)/100;
							$tot_studio_job_wise_cost=($tot_sum_amount_job_calc*$studio_job_cost)/100;
							
							$income_tax=$financial_para_arr[$costing_date]['income_tax'];
							$interest_expense=$financial_para_arr[$costing_date]['interest_expense'];
							//echo $tot_sum_amount_job_calc.'='.$commision_local.'<br/>';
							$total_job_income_tax_val+=($total_po_amount_cal*$income_tax)/100;
							$total_job_interest_exp_val+=($total_po_amount_cal*$interest_expense)/100;
							$total_job_commision_local_val+=($tot_sum_amount_job_calc*$commision_local)/100;
							$total_job_amount_cal+=$total_po_amount_arr[$jno];
							$total_job_net_amount_cal+=$tot_sum_amount_job_calc;
							$tot_income_tax_dzn=($total_job_income_tax_val/$total_job_amount_cal)*12;
							$tot_interest_exp_dzn=($total_job_interest_exp_val/$total_job_amount_cal)*12;
							$tot_studio_dzn=($tot_studio_job_wise_cost/$total_job_net_amount_cal)*12;
							$tot_commision_local_dzn=($total_job_commision_local_val/$total_job_net_amount_cal)*12;

							$summary_data[$jno][OtherDirectExpenses_job]+=$tot_studio_job_wise_cost;
							//$summary_data[OtherDirectExpenses]+=$tot_commision_local_dzn;
							$summary_data2[$jno][$ord_id][studio_cost]+=$tot_studio_dzn;
							$summary_data2[$jno][$ord_id][common_oh_job]+=$tot_operating_expense;
							$summary_data2[$jno][$ord_id][studio_cost_job]=$tot_studio_job_wise_cost;

					}
				 
				}*/



				$buyer_wise_all_info=array();
				foreach($prod_dtls_data_arr as $po_id=>$val)
				{
					$jobs= $val['job_no'];
					$set_ratio=$val['ratio'];
					$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
					$cnty_qnty=$po_country_qty*$set_ratio;
					$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
					$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
					 //$summary_data2[$jobs][$po_id]
					$tot_emblish_cost=$summary_data2[$jobs][$po_id][wash_cost_job]+$summary_data2[$jobs][$po_id][emb_cost_job] ;
					$tot_inspect_cour_certi_cost=$summary_data2[$jobs][$po_id][inspection_job]+$summary_data2[$jobs][$po_id][currier_pre_cost_job]+$summary_data2[$jobs][$po_id][certificate_pre_cost_job]+$summary_data2[$jobs][$po_id][commission_job];


					 $total_btb=$summary_data2[$jobs][$po_id][lab_test_job]+$tot_emblish_cost+$summary_data2[$jobs][$po_id][comm_cost_job]+$summary_data2[$jobs][$po_id][trims_cost_job]+$tot_yarn_dye_process_amount[$jobs][$ord_id]+$tot_dye_chemi_process_amount[$jobs][$ord_id]+$summary_data2[$jobs][$po_id][yarn_cost_job]+$tot_aop_process_amount[$jobs][$po_id]+$summary_data2[$jobs][$ord_id][common_oh_job]+$summary_data2[$jobs][$po_id][studio_cost_job]+$tot_inspect_cour_certi_cost; 

					$tot_cm_for_fab_cost=$summary_data2[$jobs][$po_id][conver_cost_job]-($tot_yarn_dye_process_amount[$jobs][$ord_id]+$tot_dye_chemi_process_amount[$jobs][$ord_id]+$tot_aop_process_amount[$jobs][$po_id]); 
					$NetFOBValue_job=$job_wise_arr[$jobs][$po_id]['po_amount'];   
					$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);
					$cm_dzn_rate=($total_cm_for_gmt/$cnty_qnty)*12;
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
							?>

							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
								<td width="30"><? echo $k;?></td>
								<td width="120"><div style="word-break:break-all"><? echo  $buyer_short_library[$buyer_id];  ?></div></td>
								 
								<td width="100"  align="right"><p><? echo $po_buyer_qty;?></p></td>
								<td width="100"  align="right"><p><? echo  number_format($buyer_wise_all_info[$buyer_id]['target'],0);?></p></td> 
								<td width="80"  align="right"><p><? echo $val['cut_lay_qty'];?></p></td>
								<td width="80"  align="right"><p><? $lay_percent = ($val['cut_lay_qty']*100)/$po_buyer_qty; echo number_format($lay_percent,2); ?>%</p></td>
								<td width="80" align="right" title="<? echo $last_cut_qty;?>"><p><? echo number_format($val['cut_qty'],0);?></p></td>
								<td width="80" align="right" title="<? echo $last_cut_qty;?>"><p><? $cut_percent = ($val['cut_qty']*100)/$po_buyer_qty; echo number_format($cut_percent,2); ?>%</p></td>
								<td width="80" align="right" title="<? echo $last_sew_in_qty;?>"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
								<td width="80" align="right" title="<? echo $last_sew_out_qty;?>"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
								<td width="80" align="right"  ><p><? echo number_format($buyer_wise_all_info[$buyer_id]['sew_cm'],3);?></p></td>
								<td width="80" align="right"  ><p><? echo number_format($buyer_wise_all_info[$buyer_id]['sew_fob'],3);?></p></td>
								 
								 
								<td width="80" align="right" title="<? echo $last_finish_qty;?>"><p><? echo number_format($val['finish_qty'],0);?></p></td>
								<td width="80"  align="right" title="<? echo $last_ex_fact_sum_qty;?>"><p><? echo number_format($ex_fact_qty,0);?></p></td>
								<td width="80" align="right"  ><p><? echo number_format($buyer_wise_all_info[$buyer_id]['ex_cm'],3);?></p></td>
								<td width="80" align="right"  ><p><? echo number_format($buyer_wise_all_info[$buyer_id]['ex_fob'],3);?></p></td>
								 
								<td width="80" align="right"  ><p><? echo number_format($buyer_wise_all_info[$buyer_id]['inhand_qty'],0);?></p></td>
								<td width="80" align="right"  ><p><? echo number_format($buyer_wise_all_info[$buyer_id]['inhand_val'],0);?></p></td>
								 
							</tr>
							<?
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
						
						
					?>
                    
                    </table>
                    <table style="width:<? echo $sum_width;?>px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    	<tfoot>
                    		<tr>
                    			<th class='alignment_css' width="30">&nbsp;</th>
                    			<th class='alignment_css' width="120">Grand Total</th>
                    			
                    			
                    			<th class='alignment_css' width="100" align="right"><? echo number_format($grand_sum_country_qty,0); ?></th>
                    			<th class='alignment_css' width="100"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'target')),0 ) ; ?></th>
                    			 
                    			<th class='alignment_css' width="80" align="right"><? echo number_format($grand_sum_cut_lay_qty,0); ?></th>
                    			<th class='alignment_css' width="80" align="right"></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format($grand_sum_cut_qty,0); ?></th>
                    			<th class='alignment_css' width="80" align="right"></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format($grand_sum_in_qty,0); ?></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format($grand_sum_out_qty,0); ?></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'sew_cm')),3 ) ; ?></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'sew_fob')),3 ) ; ?></th>
                    			

                    			<th class='alignment_css' width="80" align="right"><? echo number_format($grand_sum_finished_qty,0); ?></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format($grand_sum_exfact_qty,0); ?></th>
                    			 <th class='alignment_css' width="80" align="right"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'ex_cm')),3 ) ; ?></th>
                    			<th class='alignment_css' width="80" align="right"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'ex_fob')),3 ) ; ?></th>
                    			 <th class='alignment_css' width="80" align="right"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'inhand_qty')),0 ) ; ?></th>
                    			  <th class='alignment_css' width="80" align="right"><? echo number_format( array_sum(array_column($buyer_wise_all_info,'inhand_val')),0 ) ; ?></th>

                    		</tr>

                    	</tfoot>
                    </table>
                    </div>
                 </fieldset>
                 </div>
                 <?  $dtls_width=1450; ?>
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
                            <th class="alignment_css" width="80">Input.</th>
                            <th class="alignment_css" width="80">Sew Output</th>
                            <th class="alignment_css" width="80">CM DZN Rate</th>
                            <th class="alignment_css" width="80">Unit Price</th>
                            <th class="alignment_css" width="80">Sew CM Value</th>
                            <th class="alignment_css" width="80">Sew FOB Value</th>
                            <th class="alignment_css" width="80">Finishing</th> 
                            <th class="alignment_css" width="80">Export Pcs</th>
                            <th class="alignment_css" width="80">Export CM Value</th>
                            <th class="alignment_css" width="80">Export FOB Value</th> 
                        </tr>
                    </thead>
                </table>
                 <div style="width:<? echo $dtls_width+20;?>px; max-height:245px; float:left; overflow-y:scroll" id="scroll_body">
                    <table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <tr>
                    	<th colspan="18" align="left"><?echo $floor_name .' '.$company_name; ?></th>
                    </tr>
                    <?
                     $j=1;$grand_cut_qty=$grand_in_qty=$grand_out_qty=$grand_poly_qty=$grand_finished_qty=$grand_exfact_qty=$grand_country_qty=$grand_country_qty_pcs=0;
                      

					foreach($prod_dtls_data_arr as $po_id=>$val)
					{
						 
							// echo "<pre>";
							// print_r($val);
						$jobs= $val['job_no'];
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$set_ratio=$val['ratio'];
						
						$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty']/$set_ratio;
						$cnty_qnty=$po_country_qty*$set_ratio;
						// echo $jobs.' '.$summary_data2[$jobs][$po_id][cm_cost_job]/($cnty_qnty/12);echo "<br>";
						$cm_part_one=$summary_data2[$jobs][$po_id][cm_cost_job]/($cnty_qnty/12); 
						$po_country_qty_pcs=$po_country_dtls_qty[$po_id]['po_qty']; 
						$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id]['prod_qty'];	
						//$cut_lay_qty=$cut_lay_qty_data_arr[$po_id]['cut_lay_qty'];
						$tot_emblish_cost=$summary_data2[$jobs][$po_id][wash_cost_job]+$summary_data2[$jobs][$po_id][emb_cost_job] ;
					$tot_inspect_cour_certi_cost=$summary_data2[$jobs][$po_id][inspection_job]+$summary_data2[$jobs][$po_id][currier_pre_cost_job]+$summary_data2[$jobs][$po_id][certificate_pre_cost_job]+$summary_data2[$jobs][$po_id][commission_job];


					 $total_btb=$summary_data2[$jobs][$po_id][lab_test_job]+$tot_emblish_cost+$summary_data2[$jobs][$po_id][comm_cost_job]+$summary_data2[$jobs][$po_id][trims_cost_job]+$tot_yarn_dye_process_amount[$jobs][$ord_id]+$tot_dye_chemi_process_amount[$jobs][$ord_id]+$summary_data2[$jobs][$po_id][yarn_cost_job]+$tot_aop_process_amount[$jobs][$po_id]+$summary_data2[$jobs][$ord_id][common_oh_job]+$summary_data2[$jobs][$po_id][studio_cost_job]+$tot_inspect_cour_certi_cost; 

					$tot_cm_for_fab_cost=$summary_data2[$jobs][$po_id][conver_cost_job]-($tot_yarn_dye_process_amount[$jobs][$ord_id]+$tot_dye_chemi_process_amount[$jobs][$ord_id]+$tot_aop_process_amount[$jobs][$po_id]); 
					$NetFOBValue_job=$job_wise_arr[$jobs][$po_id]['po_amount'];   
					$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);
					$cm_dzn_rate=($total_cm_for_gmt/$cnty_qnty)*12;
					$unit_price=$val['unit_price'];
					  $sewing_cm=($val['sew_out_qty']*$cm_dzn_rate)/12;
					  $sewing_fob=$val['sew_out_qty']*$unit_price;

					  $exfac_cm=($ex_fact_country_qty*$cm_dzn_rate)/12;
					  $exfac_fob=$ex_fact_country_qty*$unit_price;
						
						if($country_id=="00")
						{
						 	$po_country_qty=$po_country_dtls_qty[$po_id]['po_qty'];
						 	$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
						}
						else
						{
							$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
						}
					?>
                     
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>">
                    <td class="alignment_css" width="30"><? echo $j;?></td>
                    <td class="alignment_css" width="100"><div style="word-break:break-all"><? echo  $buyer_short_library[$val['buyer_name']];  ?></div></td>
                    
                     
                    <td class="alignment_css" width="100"><div style="word-break:break-all"><? echo $val['po_number'];echo $sub_self=($country_id=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
                    
                    <td class="alignment_css" width="100"  align="right" title="<? echo 'S '.$set_ratio;?>"><p><? echo $po_button_qty;//$po_country_qty;?></p></td>
                     
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['cut_lay_qty'],0);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? $cut_lay_p = ($val['cut_lay_qty']*100)/$po_country_qty_pcs; echo number_format($cut_lay_p,2);?>%</p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['cut_qty'],0);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? $cut_qty_p = ($val['cut_qty']*100)/$po_country_qty_pcs; echo number_format($cut_qty_p,2);?>%</p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>

                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($cm_dzn_rate,3);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($unit_price,0);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_cm,0);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($sewing_fob,0);?></p></td>


                     
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($val['finish_qty'],0);?></p></td>
                    <td class="alignment_css" width="80"  align="right"><p><? echo number_format($ex_fact_country_qty,0);?></p></td>
                    <td class="alignment_css" width="80" align="right"><p><? echo number_format($exfac_cm,0);?></p></td>
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
						$gr_exfac_fob+=$exfac_fob ;
						 
					}
					?>
                    </table>
                    <table style="width:<? echo $dtls_width;?>px" class="tbl_bottom" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all">

                    	<tr>
                    		<td class="alignment_css" width="30">&nbsp;</td>
                    		<td class="alignment_css" width="100">&nbsp;</td>

                    		<td class="alignment_css" width="100">Grand Total:</td>
                    		<td class="alignment_css" width="100" align="right"><? echo number_format($grand_country_qty,0); ?></td>
                    		 
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_in_qty,0); ?></td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_out_qty,0); ?></td>

                    		<td class="alignment_css" width="80" align="right"> </td>
                    		<td class="alignment_css" width="80" align="right"> </td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_cm,0); ?></td>
                    		<td class="alignment_css" width="80" align="right"><? echo number_format($gr_sewing_fob,0); ?></td>


                    		<td class="alignment_css" width="80" align="right"><? echo number_format($grand_finished_qty,0); ?></td>
                    		<td class="alignment_css" width="80"  align="right"><? echo number_format($grand_exfact_qty,0); ?></td>
                    		<td class="alignment_css" width="80"  align="right"><? echo number_format($gr_exfac_cm,0); ?></td>
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
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$process_rpt_type"; 
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
