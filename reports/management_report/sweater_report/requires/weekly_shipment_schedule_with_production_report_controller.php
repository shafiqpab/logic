<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$country_code_arr=return_library_array( "select id, short_name from   lib_country", "id", "short_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "load_drop_down( 'requires/weekly_shipment_schedule_with_production_report_controller',this.value, 'load_drop_down_brand', 'brand_td')","" );
  exit();	 
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}

if ($action=="load_drop_down_week")
{
	echo create_drop_down( "cbo_week", 80, "select week from week_of_year where year='$data' group by week order by week","week,week", 1, "-- Select --", 0, "fnc_date_clear(2);","" );
  	exit();	 
}


if($action=="job_search")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
				
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var selectID = splitSTR[0];
			var str = splitSTR[1];

			$('#hide_job_id').val( selectID );
			$('#hide_job_no').val( str ); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="job_form" id="job_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('job_form','search_div','','','','');"></th> 					
					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
							 if($buyer_name!=0) $buyer_name_cond="and  buy.id in($buyer_name) ";else $buyer_name_cond="";
														 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_name_cond $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_list_style_search', 'search_div', 'weekly_shipment_schedule_with_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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

//style search------------------------------//
if($action=="create_list_style_search")
{
	extract($_REQUEST);
	list($company,$buyer,$search_type,$search_value,$cbo_year)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if($cbo_year!=0) 
	{
		if($db_type==0) $job_cond=" and year(a.insert_date)='$cbo_year'";
		else if($db_type==2) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
		
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
	else if ($db_type==0) $select_date=" year(a.insert_date)";
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	 $sql = "select a.id,b.buyer_name,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a,lib_buyer b where a.buyer_name=b.id and a.company_name=$company $buyer_cond $job_cond $search_con and a.is_deleted=0 order by a.job_no_prefix_num"; 
	// echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year,Buyer","130,90,50,110","450","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year,buyer_name", "","setFilterGrid('list_view',-1)","0","",0) ;
	
	exit();
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";


if($action=="generate_report")
{ 
    $process = array( &$_POST );
	ob_start();
    extract(check_magic_quote_gpc( $process ));
	// ================================= GETTING FORM DATA ====================================
	$rpt_type=str_replace("'","",$type);
	$company_name	    = str_replace("'","",$cbo_company_name);

	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$brand_name 		= str_replace("'","",$cbo_brand_name);
	$job_year 		    = str_replace("'","",$cbo_year);
	$job_no 	    	= str_replace("'","",$txt_job_no);
	$merch_style_ref    = str_replace("'","",$txt_merch_style_ref);
	$master_style_ref   = str_replace("'","",$txt_master_style_ref);

	$cbo_working_company_name = str_replace("'","",$cbo_working_company_name);
	$txt_job_id 	    = str_replace("'","",$txt_job_id);
	$cbo_order_status   = str_replace("'","",$cbo_order_status);
	$cbo_date_type    	= str_replace("'","",$cbo_date_type);
	$txt_date_from    	= str_replace("'","",$txt_date_from);
	$txt_date_to   	 	= str_replace("'","",$txt_date_to);
	$cbo_ship_status    = str_replace("'","",$cbo_ship_status);
	$cbo_week    		= str_replace("'","",$cbo_week);
	
	$sqlCond = "";
	$sqlCond .= ($company_name != 0) ? " and a.company_name=$company_name" : "";
	$sqlCond .= ($buyer_name != 0) ? " and a.buyer_name=$buyer_name" : "";
	$sqlCond .= ($brand_name != 0) ? " and a.brand_id=$brand_name" : "";
	$sqlCond .= ($merch_style_ref != "") ? " and a.style_ref_no='$merch_style_ref'" : "";
	$sqlCond .= ($master_style_ref != "") ? " and b.grouping='$master_style_ref'" : "";
	$sqlCond .= ($job_year !=0) ? " and extract( year from a.insert_date)=$job_year" : "";
	$sqlCond .= ($cbo_working_company_name != 0) ? " and a.style_owner='$cbo_working_company_name'" : "";
	if($txt_job_id != "")
	{
		$sqlCond .= " and a.id='$txt_job_id' ";
	}
	else
	{
		$sqlCond .= ($job_no != "") ? " and a.job_no='$job_no' " : "";
	}
	$sqlCond .= ($cbo_order_status != 0) ? " and b.is_confirmed=$cbo_order_status " : "";
	if($cbo_ship_status==1)
	{
		$sqlCond .= " and b.shiping_status=3 " ;
	}
	else if($cbo_ship_status==2)
	{
		$sqlCond .= " and b.shiping_status in (1,2) ";
	}

	if($txt_date_from!='' && $txt_date_to!='')
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($txt_date_from,"","",1);
			$end_date=change_date_format($txt_date_to,"","",1);
		}

		if($cbo_date_type==1)
		{
			$sqlCond .=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else if($cbo_date_type==2)
		{
			$sqlCond .=" and b.po_received_date between '$start_date' and '$end_date'";
		}
		else if($cbo_date_type==3)
		{
			$sqlCond .=" and b.insert_date between '$start_date' and '$end_date'";
		}
	}
	elseif($cbo_week!=0 && $job_year!=0)
	{
		$week_date_sql="SELECT YEAR, MIN (WEEK_DATE) AS min_week_date, MAX(WEEK_DATE) AS MAX_WEEK_DATE, WEEK
		FROM week_of_year where WEEK=$cbo_week and year=$job_year GROUP BY year, week ORDER BY year, week";

		$week_date_result=sql_select($week_date_sql);
		if($db_type==0)
		{
			$start_date=change_date_format($week_date_result[0]['MIN_WEEK_DATE'],"yyyy-mm-dd","");
			$end_date=change_date_format($week_date_result[0]['MAX_WEEK_DATE'],"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($week_date_result[0]['MIN_WEEK_DATE'],"","",1);
			$end_date=change_date_format($week_date_result[0]['MAX_WEEK_DATE'],"","",1);
		}
		$sqlCond .=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$buyer_brand_library=return_library_array( "select id,brand_name from lib_buyer_brand", "id", "brand_name"  );
	
	if($rpt_type==1) // SHOW
	{
		// ================================= Week ===============================================
		$dateFormat="d-M-Y";
		$weekDateArray=array();
		$weekMaxDateArray=array();
		$week_sql="SELECT  YEAR, MIN (WEEK_DATE) AS min_week_date,MAX(WEEK_DATE) AS MAX_WEEK_DATE, WEEK
		FROM week_of_year GROUP BY   year, week ORDER BY  year, week";

		$week_sql_result=sql_select($week_sql);
		foreach ($week_sql_result as $rows)
		{
			$week_date=date($dateFormat,strtotime($rows['MIN_WEEK_DATE']));
			$max_week_date=date($dateFormat,strtotime($rows['MAX_WEEK_DATE']));
			$shipment_month = date('m',strtotime($rows['MIN_WEEK_DATE']));
            
			$week_year = $rows['WEEK']."***".$rows['YEAR'];
			$weekDateArray[$shipment_month."***".$rows['YEAR']][$rows['WEEK']]=$week_date;
			$weekMaxDateArray[$week_year]['MAX']=$max_week_date;
			$weekMaxDateArray[$week_year]['MIN']=$week_date;

		}
		unset($week_sql_result);
		
		
		 //var_dump($weekMaxDateArray['18***2022']);die;
			
	
        // ================================= Main Query ===============================================
		$sql="  SELECT a.company_name,
		a.style_owner,
		a.buyer_name,
		a.brand_id,
		a.job_no,
		a.job_no_prefix_num,
		a.style_ref_no,
		a.gauge,
		b.id
			AS po_id,
		b.po_number,
		b.po_quantity,
		b.pub_shipment_date
        FROM wo_po_details_master a,
		wo_po_break_down b
        WHERE     a.id = b.job_id
		AND b.shiping_status IN (1, 2)
		AND a.status_active = '1'
		AND a.is_deleted = '0'
		AND b.status_active = '1'
		AND b.is_deleted = '0'
		$sqlCond
        GROUP BY a.company_name,
		a.style_owner,
		a.buyer_name,
		a.brand_id,
		a.job_no,
		a.job_no_prefix_num,
		a.style_ref_no,
		a.gauge,
		b.id,
		b.po_number,
		b.po_quantity,
		b.pub_shipment_date
        ORDER BY b.pub_shipment_date ASC";
		$sql_result = sql_select($sql);
         
		$data_array = array();
		foreach($sql_result as $row){

           
		   $shipment_year = date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
		   $shipment_month = date('m',strtotime($row['PUB_SHIPMENT_DATE']));
		   $shipment_week = $weekDateArray[$shipment_month."***".$shipment_year];
		   $week =10000;
		   foreach( $shipment_week as $week_key=> $week_value){
                $week = min($week,$week_key);
		   }

			if(strtotime($shipment_week[$week]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+1]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week;
			}
			else if (strtotime($shipment_week[$week+1]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+2]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+1;
			}
			else if (strtotime($shipment_week[$week+2]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+3]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+2;
			}
			else if (strtotime($shipment_week[$week+3]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+4]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+3;
			}
			else if(count($shipment_week)>4)
			{
				if (strtotime($shipment_week[$week+4]) <= strtotime($row['PUB_SHIPMENT_DATE']))
				{
					$week = $week+4;
				}
				else
				{
					$week = $week+5;
				}
			}
			else
			{
				$week = $week+4;
			}

		  
           $week_year = $week."***".date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['COMPANY_NAME'] = $row['COMPANY_NAME'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['STYLE_OWNER'] = $row['STYLE_OWNER'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['BRAND_ID'] = $row['BRAND_ID'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['JOB_NO'] = $row['JOB_NO'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PO_NUMBER'] = $row['PO_NUMBER'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['GAUGE'] = $row['GAUGE'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PO_QUANTITY'] = $row['PO_QUANTITY'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PUB_SHIPMENT_DATE'] = $row['PUB_SHIPMENT_DATE'];

		}	

		// =============================================== Production SQL ========================
		$production_sql=" SELECT 
		a.job_no,
        b.id
             AS po_id,
        b.pub_shipment_date,
		SUM (CASE WHEN c.production_type = '1' THEN c.production_quantity ELSE 0 END) AS knitting_complete,
		SUM (CASE WHEN c.production_type = '100' THEN c.production_quantity ELSE 0 END) AS knitting_qc,
		SUM (CASE WHEN c.production_type = '4' THEN c.production_quantity ELSE 0 END) AS linking_complete,
		SUM (CASE WHEN c.production_type = '111' THEN c.production_quantity ELSE 0 END) AS trimming_complete,
		SUM (CASE WHEN c.production_type = '112' THEN c.production_quantity ELSE 0 END) AS mending_complete,
		SUM (CASE WHEN c.production_type = '3' THEN c.production_quantity ELSE 0 END) AS wash_complete,
		SUM (CASE WHEN c.production_type = '5' THEN c.production_quantity ELSE 0 END) AS sewing_complete,
		SUM (CASE WHEN c.production_type = '114' THEN c.production_quantity ELSE 0 END) AS pqc_complete,
		SUM (CASE WHEN c.production_type = '67' THEN c.production_quantity ELSE 0 END) AS iron_complete,
		SUM (CASE WHEN c.production_type = '8' THEN c.production_quantity ELSE 0 END) AS pack_complete
        FROM wo_po_details_master       a,
		wo_po_break_down           b,
		pro_garments_production_mst c
        WHERE     a.id = b.job_id
		AND b.id = c.po_break_down_id
		AND c.production_type IN (1,3,4,5,8,67,100,111,112,114)
		AND b.shiping_status IN (1, 2)
		AND a.status_active = '1'
		AND a.is_deleted = '0'
		AND b.status_active = '1'
		AND b.is_deleted = '0'
		AND c.status_active = '1'
		AND c.is_deleted = '0'
		$sqlCond
        GROUP BY 
		a.job_no,
        b.id,
        b.pub_shipment_date
		ORDER BY b.pub_shipment_date asc";
		//echo $production_sql;//die;
		$production_sql_result = sql_select($production_sql);
         
		$production_data_array = array();
		foreach($production_sql_result as $row){
           
			$shipment_year = date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
			$shipment_month = date('m',strtotime($row['PUB_SHIPMENT_DATE']));
			$shipment_week = $weekDateArray[$shipment_month."***".$shipment_year];

		   $week =10000;
		   foreach( $shipment_week as $week_key=> $week_value){
                $week = min($week,$week_key);
		   }
		 
			if(strtotime($shipment_week[$week]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+1]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week;
			}
			else if (strtotime($shipment_week[$week+1]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+2]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+1;
			}
			else if (strtotime($shipment_week[$week+2]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+3]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+2;
			}
			else if (strtotime($shipment_week[$week+3]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+4]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+3;
			}
			else if(count($shipment_week)>4)
			{
				if (strtotime($shipment_week[$week+4]) < strtotime($row['PUB_SHIPMENT_DATE']))
				{
					$week = $week+4;
				}
				else
				{
					$week = $week+5;
				}
			}
			else
			{
				$week = $week+4;
			}
			$week_year = $week."***".date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['KNITTING_COMPLETE'] += $row['KNITTING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['KNITTING_QC'] += $row['KNITTING_QC'];
		   $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['LINKING_COMPLETE'] += $row['LINKING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['TRIMMING_COMPLETE'] += $row['TRIMMING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['MENDING_COMPLETE'] += $row['MENDING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['WASH_COMPLETE'] += $row['WASH_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['SEWING_COMPLETE'] += $row['SEWING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PQC_COMPLETE'] += $row['PQC_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['IRON_COMPLETE'] += $row['IRON_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PACK_COMPLETE'] += $row['PACK_COMPLETE'];
		}


		?>
			<fieldset style="width:1830px">
				<table width="1830" cellpadding="0" cellspacing="0"> 
					<tr class="form_caption">
						<td align="center"><p style="font-size:25px; font-weight:bold;"><? echo $companyArr[$comapny_id]; ?><p></td> 
					</tr>
					<tr class="form_caption" width="1830px">
						<td align="center" colspan="21" style="font-size:21px; font-weight:bold; width:1830px;">Weekly Shipment Schedule With Production Report</td> 
					</tr>
				</table>
				<br />
				<?
				$j=1;
				foreach($data_array as $pub_ship_date=>$date)
				{
				 $i=1;
				 
				 ?>
					<table id="table_header_1" class="rpt_table" width="1830" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
							<th colspan="3" float="left" title="<?=$pub_ship_date;?>"><b>Shipment Schedule from <? echo $weekMaxDateArray[$pub_ship_date]['MIN']. " to " .$weekMaxDateArray[$pub_ship_date]['MAX']; ?></b></th>
							<th colspan="18"></th>
							</tr>
							<tr height="50">
								<th width="40">Sl.</th>
								<th width="150">Company Name</th>
								<th width="150">Working Company</th>
								<th width="100">Buyer Name</th>
								<th width="100">Brand</th>
								<th width="100">Job No</th>
								<th width="100">Style No</th>
								<th width="100">PO No</th>
								<th width="50">GG</th>
								<th width="70">PO Qty. [Pcs]</th>

								<th width="70">Pub. Ship Date</th>
								<th width="70">Knitting Complete</th>
								<th width="70">Knitting QC</th>
								<th width="70">Linking Complete</th>
								<th width="70">Trimming Complete</th>
								<th width="70"><b>Mending Complete</b></th>
								<th width="70">Wash Complete</th>
								<th width="70">Sewing Complete</th>
								<th width="70">PQC Complete</th>
								<th width="70">Iron Complete</th>

								<th width="70">Pack Complete</th>
							</tr>
						</thead>
					</table>
					<table class="rpt_table" width="1830" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id='scroll_body'>
							<?
							$total_knitting_complete = 0;
							$total_knitting_qc       = 0;
							$total_linking_complete  = 0;
							$total_trimming_complete = 0;
							$total_mending_complete  = 0;
							$total_wash_complete     = 0;
							$total_sewing_complete   = 0;
							$total_pqc_complete      = 0;
							$total_iron_complete     = 0;
							$total_pack_complete     = 0;

							foreach($date as $job_key=>$job_value)
							{
								foreach($job_value as $po_key=>$po_value) 
								{
									if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_2nd<? echo $j ?>','<? echo $bgcolor;?>')" id="tr_2nd<? echo $j; ?>">
											<td width="40" align="left"><? echo $i; ?></td>
											<td width="150" align="left"><? echo $company_library[$po_value['COMPANY_NAME']];?></td>
											<td width="150" align="left"><? echo $company_library[$po_value['STYLE_OWNER']];?></td>
											<td width="100" align="left"><? echo $buyer_library[$po_value['BUYER_NAME']];?></td>
											<td width="100" align="left"><? echo $buyer_brand_library[$po_value['BRAND_ID']];?></td>
											<td width="100" align="left"><? echo $po_value['JOB_NO'];?></td>
											<td width="100" align="left"><? echo $po_value['STYLE_REF_NO'];?></td>
											<td width="100" align="left"><? echo $po_value['PO_NUMBER'];?></td>
											<td width="50" align="right"><? echo $gauge_arr[$po_value['GAUGE']];?></td>
											<td width="70" align="right"><? echo $po_value['PO_QUANTITY'];?></td>

											<td width="70">&nbsp;<? echo change_date_format($po_value['PUB_SHIPMENT_DATE']);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_QC'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['LINKING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['TRIMMING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['MENDING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['WASH_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['SEWING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['PQC_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['IRON_COMPLETE'],0);?></td>

											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['PACK_COMPLETE'],0);?></td>
										</tr>
									<?
									$i++;
									$j++;
									$total_po_quantity         += $po_value['PO_QUANTITY'];
									$total_knitting_complete   += $production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_COMPLETE'];
									$total_knitting_qc         += $production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_QC'];
									$total_linking_complete    += $production_data_array[$pub_ship_date][$job_key][$po_key]['LINKING_COMPLETE'];
									$total_trimming_complete   += $production_data_array[$pub_ship_date][$job_key][$po_key]['TRIMMING_COMPLETE'];
									$total_mending_complete    += $production_data_array[$pub_ship_date][$job_key][$po_key]['MENDING_COMPLETE'];
									$total_wash_complete       += $production_data_array[$pub_ship_date][$job_key][$po_key]['WASH_COMPLETE'];
									$total_sewing_complete     += $production_data_array[$pub_ship_date][$job_key][$po_key]['SEWING_COMPLETE'];
									$total_pqc_complete        += $production_data_array[$pub_ship_date][$job_key][$po_key]['PQC_COMPLETE'];
									$total_iron_complete       += $production_data_array[$pub_ship_date][$job_key][$po_key]['IRON_COMPLETE'];
									$total_pack_complete       += $production_data_array[$pub_ship_date][$job_key][$po_key]['PACK_COMPLETE'];
								}
							}
							?>	
										<tr>
											<td colspan="9" align="right"><b>Total</b></td>
											<td width="70">&nbsp;</td>

											<td width="70">&nbsp;</td>
											<td width="70" align="right"><b><? echo number_format($total_knitting_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_knitting_qc,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_linking_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_trimming_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_mending_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_wash_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_sewing_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_pqc_complete,0);?></b></td>
											<td width="70" align="right"><b><? echo number_format($total_iron_complete,0);?></b></td>

											<td width="70" align="right"><b><? echo number_format($total_pack_complete,0);?></b></td>
										</tr> 
						</tbody>                   
					</table>
				 <?
				}
				?>
			</fieldset>  
		<?
	}
	
	if($rpt_type==2) // SHOW 2
	{
		// ================================= Week ===============================================
		$dateFormat="d-M-Y";
		$weekDateArray=array();
		$weekMaxDateArray=array();
		$week_sql="SELECT YEAR, MIN (WEEK_DATE) AS min_week_date, MAX(WEEK_DATE) AS MAX_WEEK_DATE, WEEK
		FROM week_of_year GROUP BY year, week ORDER BY year, week";

		$week_sql_result=sql_select($week_sql);
		foreach ($week_sql_result as $rows)
		{
			$week_date=date($dateFormat,strtotime($rows['MIN_WEEK_DATE']));
			$max_week_date=date($dateFormat,strtotime($rows['MAX_WEEK_DATE']));
			$shipment_month = date('m',strtotime($rows['MIN_WEEK_DATE']));
            
			$week_year = $rows['WEEK']."***".$rows['YEAR'];
			$weekDateArray[$shipment_month."***".$rows['YEAR']][$rows['WEEK']]=$week_date;
			$weekMaxDateArray[$week_year]['MAX']=$max_week_date;
			$weekMaxDateArray[$week_year]['MIN']=$week_date;

		}
		unset($week_sql_result);
		
		
		 //var_dump($weekMaxDateArray['18***2022']);die;
	
        // ================================= Main Query ===============================================
		$sql=" SELECT a.company_name, a.style_owner, a.buyer_name, a.brand_id, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.gauge,
		b.id as po_id, b.po_number, b.po_quantity, b.pub_shipment_date,b.shiping_status
        FROM wo_po_details_master a, wo_po_break_down b
        WHERE a.id = b.job_id and a.status_active = '1' and a.is_deleted = '0'
		and b.status_active = '1' and b.is_deleted = '0' $sqlCond
        GROUP BY a.company_name, a.style_owner, a.buyer_name, a.brand_id, a.job_no, a.job_no_prefix_num, a.style_ref_no,
		a.gauge, b.id, b.po_number, b.po_quantity, b.pub_shipment_date,b.shiping_status
        ORDER BY b.pub_shipment_date ASC";
		// echo $sql;die;
		$sql_result = sql_select($sql);
         
		$data_array = array();
		foreach($sql_result as $row){
		   $shipment_year = date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
		   $shipment_month = date('m',strtotime($row['PUB_SHIPMENT_DATE']));
		   $shipment_week = $weekDateArray[$shipment_month."***".$shipment_year];
		   $week =10000;
		   foreach( $shipment_week as $week_key=> $week_value){
                $week = min($week,$week_key);
		   }

			if(strtotime($shipment_week[$week]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+1]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week;
			}
			else if (strtotime($shipment_week[$week+1]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+2]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+1;
			}
			else if (strtotime($shipment_week[$week+2]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+3]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+2;
			}
			else if (strtotime($shipment_week[$week+3]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+4]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+3;
			}
			else if (strtotime($shipment_week[$week]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week-1;
			}
			else if(count($shipment_week)>4)
			{
				if (strtotime($shipment_week[$week+4]) <= strtotime($row['PUB_SHIPMENT_DATE']))
				{
					$week = $week+4;
				}
				else
				{
					$week = $week+5;
				}
			}
			else if (strtotime($shipment_week[$week+3]) <= strtotime($row['PUB_SHIPMENT_DATE']) && $shipment_week[$week+4]=="")
			{
				$week = $week+3;
			}
			else
			{
				$week = $week+4;
			}

           $week_year = $week."***".date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['COMPANY_NAME'] = $row['COMPANY_NAME'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['STYLE_OWNER'] = $row['STYLE_OWNER'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['BRAND_ID'] = $row['BRAND_ID'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['JOB_NO'] = $row['JOB_NO'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PO_NUMBER'] = $row['PO_NUMBER'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['GAUGE'] = $row['GAUGE'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PO_QUANTITY'] = $row['PO_QUANTITY'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PUB_SHIPMENT_DATE'] = $row['PUB_SHIPMENT_DATE'];
           $data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['SHIPING_STATUS'] = $row['SHIPING_STATUS'];
		}	

		// =============================================== Production SQL ========================
		$production_sql=" SELECT a.job_no, b.id AS po_id, b.pub_shipment_date,
		SUM (CASE WHEN c.production_type = '1' THEN c.production_quantity ELSE 0 END) AS knitting_complete,
		SUM (CASE WHEN c.production_type = '52' THEN c.production_quantity ELSE 0 END) AS knitting_qc,
		SUM (CASE WHEN c.production_type = '4' THEN c.production_quantity ELSE 0 END) AS linking_complete,
		SUM (CASE WHEN c.production_type = '111' THEN c.production_quantity ELSE 0 END) AS trimming_complete,
		SUM (CASE WHEN c.production_type = '112' THEN c.production_quantity ELSE 0 END) AS mending_complete,
		SUM (CASE WHEN c.production_type = '3' THEN c.production_quantity ELSE 0 END) AS wash_complete,
		SUM (CASE WHEN c.production_type = '5' THEN c.production_quantity ELSE 0 END) AS sewing_complete,
		SUM (CASE WHEN c.production_type = '114' THEN c.production_quantity ELSE 0 END) AS pqc_complete,
		SUM (CASE WHEN c.production_type = '67' THEN c.production_quantity ELSE 0 END) AS iron_complete,
		SUM (CASE WHEN c.production_type = '8' THEN c.production_quantity ELSE 0 END) AS pack_complete
        FROM wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c
        WHERE a.id = b.job_id and b.id = c.po_break_down_id and c.production_type in (1,3,4,5,8,67,52,111,112,114) and a.status_active = '1' and a.is_deleted = '0' and b.status_active = '1' and b.is_deleted = '0' and c.status_active = '1' and c.is_deleted = '0' $sqlCond
        GROUP BY a.job_no, b.id, b.pub_shipment_date
		ORDER BY b.pub_shipment_date asc";
		//echo $production_sql;//die;
		$production_sql_result = sql_select($production_sql);
         
		$production_data_array = array();
		$production_job_array=array();
		foreach($production_sql_result as $row){
           
			$shipment_year = date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
			$shipment_month = date('m',strtotime($row['PUB_SHIPMENT_DATE']));
			$shipment_week = $weekDateArray[$shipment_month."***".$shipment_year];

		   $week =10000;
		   foreach( $shipment_week as $week_key=> $week_value){
                $week = min($week,$week_key);
		   }
		 
			if(strtotime($shipment_week[$week]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+1]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week;
			}
			else if (strtotime($shipment_week[$week+1]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+2]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+1;
			}
			else if (strtotime($shipment_week[$week+2]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+3]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+2;
			}
			else if (strtotime($shipment_week[$week+3]) <= strtotime($row['PUB_SHIPMENT_DATE']) && strtotime($shipment_week[$week+4]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week+3;
			}
			else if (strtotime($shipment_week[$week]) > strtotime($row['PUB_SHIPMENT_DATE']))
			{
				$week = $week-1;
			}
			else if(count($shipment_week)>4)
			{
				if (strtotime($shipment_week[$week+4]) <= strtotime($row['PUB_SHIPMENT_DATE']))
				{
					$week = $week+4;
				}
				else
				{
					$week = $week+5;
				}
			}
			else if (strtotime($shipment_week[$week+3]) <= strtotime($row['PUB_SHIPMENT_DATE']) && $shipment_week[$week+4]=="")
			{
				$week = $week+3;
			}
			else
			{
				$week = $week+4;
			}
			
			$week_year = $week."***".date('Y',strtotime($row['PUB_SHIPMENT_DATE']));
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['KNITTING_COMPLETE'] += $row['KNITTING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['KNITTING_QC'] += $row['KNITTING_QC'];
		   $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['LINKING_COMPLETE'] += $row['LINKING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['TRIMMING_COMPLETE'] += $row['TRIMMING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['MENDING_COMPLETE'] += $row['MENDING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['WASH_COMPLETE'] += $row['WASH_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['SEWING_COMPLETE'] += $row['SEWING_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PQC_COMPLETE'] += $row['PQC_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['IRON_COMPLETE'] += $row['IRON_COMPLETE'];
           $production_data_array[$week_year][$row['JOB_NO']][$row['PO_ID']]['PACK_COMPLETE'] += $row['PACK_COMPLETE'];

           $production_job_array[$week_year][$row['JOB_NO']]['KNITTING_COMPLETE'] += $row['KNITTING_COMPLETE'];
		}


		?>
			<fieldset style="width:1970px">
				<table width="1970" cellpadding="0" cellspacing="0"> 
					<tr class="form_caption">
						<td align="center"><p style="font-size:25px; font-weight:bold;"><? echo $companyArr[$comapny_id]; ?><p></td> 
					</tr>
					<tr class="form_caption" width="1830px">
						<td align="center" colspan="21" style="font-size:21px; font-weight:bold; width:1830px;">Weekly Shipment Schedule With Production Report</td> 
					</tr>
				</table>
				<br />
				<?
				$j=1;
				$job_chk=array();
				foreach($data_array as $pub_ship_date=>$date)
				{
				 $i=1;
				 $pub_ship_date_arr=explode("***",$pub_ship_date);
				 ?>
					<table id="table_header_1" class="rpt_table" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
							<th colspan="3" float="left" title="<?=$pub_ship_date;?>"><b>Shipment Schedule from <? echo $weekMaxDateArray[$pub_ship_date]['MIN']. " to " .$weekMaxDateArray[$pub_ship_date]['MAX'].", Week ".$pub_ship_date_arr[0]; ?></b></th>
							<th colspan="20"></th>
							</tr>
							<tr height="50">
								<th width="40">Sl.</th>
								<th width="150">Company Name</th>
								<th width="150">Working Company</th>
								<th width="100">Buyer Name</th>
								<th width="100">Brand</th>
								<th width="100">Job No</th>
								<th width="100">Style No</th>
								<th width="100">PO No</th>
								<th width="50">GG</th>
								<th width="70">PO Qty. [Pcs]</th>

								<th width="70">Pub. Ship Date</th>
								<th width="70">Knitting Complete</th>
								<th width="70">Styl Knit Qty</th>
								<th width="70">Knitting QC</th>
								<th width="70">Linking Complete</th>
								<th width="70">Trimming Complete</th>
								<th width="70"><b>Mending Complete</b></th>
								<th width="70">Wash Complete</th>
								<th width="70">Sewing Complete</th>
								<th width="70">PQC Complete</th>
								<th width="70">Iron Complete</th>

								<th width="70">Pack Complete</th>
								<th width="70">Ship Status</th>
							</tr>
						</thead>
					</table>
					<table class="rpt_table" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id='scroll_body'>
							<?
							$total_knitting_complete = 0;
							$total_knitting_qc       = 0;
							$total_linking_complete  = 0;
							$total_trimming_complete = 0;
							$total_mending_complete  = 0;
							$total_wash_complete     = 0;
							$total_sewing_complete   = 0;
							$total_pqc_complete      = 0;
							$total_iron_complete     = 0;
							$total_pack_complete     = 0;

							foreach($date as $job_key=>$job_value)
							{
								$job_count=count($job_value);
								foreach($job_value as $po_key=>$po_value) 
								{
									if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_2nd<? echo $j ?>','<? echo $bgcolor;?>')" id="tr_2nd<? echo $j; ?>">
											<td width="40" align="left"><? echo $i; ?></td>
											<td width="150" align="left"><? echo $company_library[$po_value['COMPANY_NAME']];?></td>
											<td width="150" align="left"><? echo $company_library[$po_value['STYLE_OWNER']];?></td>
											<td width="100" align="left"><? echo $buyer_library[$po_value['BUYER_NAME']];?></td>
											<td width="100" align="left"><? echo $buyer_brand_library[$po_value['BRAND_ID']];?></td>
											<td width="100" align="left"><? echo $po_value['JOB_NO'];?></td>
											<td width="100" align="left"><? echo $po_value['STYLE_REF_NO'];?></td>
											<td width="100" align="left"><? echo $po_value['PO_NUMBER'];?></td>
											<td width="50" align="right"><? echo $gauge_arr[$po_value['GAUGE']];?></td>
											<td width="70" align="right"><? echo $po_value['PO_QUANTITY'];?></td>

											<td width="70">&nbsp;<? echo change_date_format($po_value['PUB_SHIPMENT_DATE']);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_COMPLETE'],0);?></td>
											<?
												if(!in_array($pub_ship_date."__".$po_value['JOB_NO'],$job_chk))
												{
													$job_chk[]=$pub_ship_date."__".$po_value['JOB_NO'];
													?>
														<td width="70" align="right" rowspan="<?=$job_count;?>"><? echo number_format($production_job_array[$pub_ship_date][$job_key]['KNITTING_COMPLETE'],0);?></td>
													<?

												}
											?>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_QC'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['LINKING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['TRIMMING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['MENDING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['WASH_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['SEWING_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['PQC_COMPLETE'],0);?></td>
											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['IRON_COMPLETE'],0);?></td>

											<td width="70" align="right"><? echo number_format($production_data_array[$pub_ship_date][$job_key][$po_key]['PACK_COMPLETE'],0);?></td>
											<td width="70" ><? echo $shipment_status[$po_value['SHIPING_STATUS']];?></td>
										</tr>
									<?
									$i++;
									$j++;
									$total_po_quantity         += $po_value['PO_QUANTITY'];
									$total_knitting_complete   += $production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_COMPLETE'];
									$total_knitting_qc         += $production_data_array[$pub_ship_date][$job_key][$po_key]['KNITTING_QC'];
									$total_linking_complete    += $production_data_array[$pub_ship_date][$job_key][$po_key]['LINKING_COMPLETE'];
									$total_trimming_complete   += $production_data_array[$pub_ship_date][$job_key][$po_key]['TRIMMING_COMPLETE'];
									$total_mending_complete    += $production_data_array[$pub_ship_date][$job_key][$po_key]['MENDING_COMPLETE'];
									$total_wash_complete       += $production_data_array[$pub_ship_date][$job_key][$po_key]['WASH_COMPLETE'];
									$total_sewing_complete     += $production_data_array[$pub_ship_date][$job_key][$po_key]['SEWING_COMPLETE'];
									$total_pqc_complete        += $production_data_array[$pub_ship_date][$job_key][$po_key]['PQC_COMPLETE'];
									$total_iron_complete       += $production_data_array[$pub_ship_date][$job_key][$po_key]['IRON_COMPLETE'];
									$total_pack_complete       += $production_data_array[$pub_ship_date][$job_key][$po_key]['PACK_COMPLETE'];
								}
							}
							?>	
							<tr>
								<td colspan="9" align="right"><b>Total</b></td>
								<td width="70">&nbsp;</td>

								<td width="70">&nbsp;</td>
								<td width="70" align="right"><b><? echo number_format($total_knitting_complete,0);?></b></td>
								<td width="70" align="right"></td>
								<td width="70" align="right"><b><? echo number_format($total_knitting_qc,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_linking_complete,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_trimming_complete,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_mending_complete,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_wash_complete,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_sewing_complete,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_pqc_complete,0);?></b></td>
								<td width="70" align="right"><b><? echo number_format($total_iron_complete,0);?></b></td>

								<td width="70" align="right"><b><? echo number_format($total_pack_complete,0);?></b></td>
								<td width="70" align="right"></td>
							</tr> 
						</tbody>                   
					</table>
				 <?
				}
				?>
			</fieldset>  
		<?
	}
	
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
	echo "$total_data####$filename";
	exit(); 
}

