<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );   
	exit();  	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 110, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "",0 );     	 
	exit();
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getCompanyId();') ,3000)];\n";
    exit();
}
 
if($action=="report_generate")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	
	//echo $txt_date;cbo_floor
	$company_name=str_replace("'","",$cbo_company_name);
	$location_name=str_replace("'","",$cbo_location);
	$cbo_buyer=str_replace("'","",$cbo_buyer);
	$prod_date = $txt_date;

	$prod_con = "";
	$prod_con .= ($company_name==0) ? "" : " and a.company_id=".$company_name;
	// $prod_con .= ($company_name==0) ? "" : " and a.serving_company=".$company_name;
	$prod_con .= ($location_name==0) ? "" : " and a.location=".$location_name;
	$prod_con .= ($cbo_buyer==0) ? "" : " and d.buyer_name=".$cbo_buyer;
	//$prod_con .= ($cbo_floor==0) ? "" : " and a.floor_id in($cbo_floor)";
	//$prod_con .= ($cbo_line==0) ? "" : " and a.sewing_line=".$cbo_line;
	// $prod_con .= ($prod_date=="") ? "" : " and a.production_date=".$prod_date;

	// echo $prod_con;
	 
	$prod_resource_array=array();
	$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company_name");

	foreach($dataArray as $row)
	{
		$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
		$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
	}
	//var_dump($prod_resource_array);//change_date_format($txt_date,'yyyy-mm-dd')
	
	$prod_qnty_data=sql_select("select floor_id, location, prod_reso_allo, sewing_line, po_break_down_id, sum(production_quantity) as prod_qnty from pro_garments_production_mst where  production_type in(4,5) group by floor_id, location, prod_reso_allo, sewing_line, po_break_down_id");
	foreach($prod_qnty_data as $row)
	{
		$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]=$row[csf("prod_qnty")];
	}
	
	//var_dump($prod_qnty_data_arr);die;
	//echo $prod_qnty_data_arr[1][1][1][88][3533].jahid;die;
	
		 
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
			}
			.gd-color3
			{
				background: rgb(254,255,255); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
				border: 1px solid #dccdcd;
			}

        </style> 
                <div style="width:1100px; margin: 0 auto"> 
                    <table width="1090" cellspacing="0" style="margin: 20px 0"> 
                        <tr style="border:none;">
                            <td align="right" style="border:none; font-size:14px; font-weight: bold;" width="40%">                                	
                                Company Name                  
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">                                	
                                 :               
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">                                	
                                <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                 
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td align="right" style="border:none; font-size:14px;font-weight: bold;" width="40%"> 
                                Location                    
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%"> 
                                 :                     
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%"> 
                                <? echo $location_library[str_replace("'","",$cbo_location)]; ?>                     
                            </td>
                        </tr> 
                        <tr style="border:none;">
                            <td align="right" style="border:none; font-size:14px;font-weight: bold;" width="40%">                        
                                Date                       
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">                        
                                :                       
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">                        
                                <? echo change_date_format(str_replace("'", "", $prod_date)); ?>                         
                            </td>
                        </tr>  
                    </table>                     	
					<?
					$production_data_arr=array(); 
					$buyer_data_arr=array(); 
	                $production_mst_sql= "SELECT a.id,a.company_id, a.country_id, a.production_date,d.job_no, d.job_no_prefix_num,e.id as po_break_down_id,d.gmts_item_id,a.floor_id,a.prod_reso_allo,a.sewing_line,c.color_number_id,d.buyer_name,a.insert_date,a.update_date,	d.style_ref_no,e.po_number,	
         				sum(CASE WHEN a.production_type ='1' and a.production_date=".$prod_date." THEN b.production_qnty ELSE 0 END) AS cutting_qnty,		
						sum(CASE WHEN a.production_type ='4' and a.production_date=".$prod_date." and a.production_source=1 THEN b.production_qnty ELSE 0 END) AS sewingin_inhouse_qnty,
						sum(CASE WHEN a.production_type ='4' and a.production_date=".$prod_date." and a.production_source=3 THEN b.production_qnty ELSE 0 END) AS sewingin_outbound_qnty
						FROM 
						pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e
						WHERE a.production_date=$prod_date and a.production_type in(1,4) and b.production_type in(1,4) and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and
						a.is_deleted=0 and a.status_active in(1,2,3) and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $prod_con and d.job_no=e.job_no_mst AND e.id=a.po_break_down_id AND e.id=c.po_break_down_id and d.status_active in(1,2,3) and d.is_deleted=0
						GROUP BY a.id,a.company_id,a.country_id,a.production_date,d.job_no, d.job_no_prefix_num,e.id,d.gmts_item_id,a.floor_id,a.prod_reso_allo,a.sewing_line,c.color_number_id,d.buyer_name,a.insert_date,a.update_date,
         				d.style_ref_no,e.po_number 
         				ORDER BY a.update_date,a.insert_date,a.floor_id,a.sewing_line";
	                $prod_res = sql_select($production_mst_sql);
	                foreach ($prod_res as $key => $val) 
	                {
	                	$insert_date = $val[csf('insert_date')];
	                	$update_date = $val[csf('update_date')];
	                	$time = "";	                	
	                	$time = (isset($update_date)) ? date("H",strtotime($update_date)) : date("H",strtotime($insert_date));                	

	                	if($val[csf("prod_reso_allo")]==1)
	                	{
	                		$line_resource_mst_arr=array_unique(explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]));
							$line_name="";
							foreach($line_resource_mst_arr as $resource_id)
							{
								$line_name.=($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
							}
								// echo $line_name.'=';
		                		$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['mst_id'] = $val[csf('id')];
		                		$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['company_id'] = $val[csf('company_id')];
		                		$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['job_no'] = $val[csf('job_no_prefix_num')];
				                $production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['po_number'] = $val[csf('po_number')];
			                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['style_ref_no'] = $val[csf('style_ref_no')];
			                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['buyer_name'] = $val[csf('buyer_name')];
			                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['insert_date'] = $val[csf('insert_date')];
			                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['sewingin_inhouse_qnty'] += $val[csf('sewingin_inhouse_qnty')];
			                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['sewingin_outbound_qnty'] += $val[csf('sewingin_outbound_qnty')];
			                	$country_id_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name][$val[csf('country_id')]] = $val[csf('country_id')];
			                	$mst_id_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name][$val[csf('id')]] = $val[csf('id')];
			                
	                	}
	                	else
	                	{
	                		$line_name=$lineArr[$val[csf('sewing_line')]];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['mst_id'] = $val[csf('id')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['company_id'] = $val[csf('company_id')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['job_no'] = $val[csf('job_no_prefix_num')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['po_number'] = $val[csf('po_number')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['style_ref_no'] = $val[csf('style_ref_no')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['buyer_name'] = $val[csf('buyer_name')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['insert_date'] = $val[csf('insert_date')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['sewingin_inhouse_qnty'] = $val[csf('sewingin_inhouse_qnty')];
		                	$production_data_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name]['sewingin_outbound_qnty'] += $val[csf('sewingin_outbound_qnty')];
			                $country_id_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name][$val[csf('country_id')]] = $val[csf('country_id')];
			                $mst_id_arr[$time][$val[csf('po_break_down_id')]][$val[csf('gmts_item_id')]][$val[csf('color_number_id')]][$val[csf('production_date')]][$val[csf('floor_id')]][$line_name][$val[csf('id')]] = $val[csf('id')];
	                	}	
	                	// for summary section
	                	$buyer_data_arr[$val[csf('buyer_name')]]['cutting_qnty'] += $val[csf('cutting_qnty')];
	                	$buyer_data_arr[$val[csf('buyer_name')]]['sewingin_inhouse_qnty'] += $val[csf('sewingin_inhouse_qnty')];
	                	$buyer_data_arr[$val[csf('buyer_name')]]['sewingin_outbound_qnty'] += $val[csf('sewingin_outbound_qnty')];              	
	                	// echo $val[csf('buyer_name')].'='.$val[csf('sewingin_inhouse_qnty')].'<br>';
	                }
					// echo "<pre>";
					// print_r($country_id_arr);
	                $po_wise_country_name = [];
        			foreach ($country_id_arr as $time => $time_data)
        			{
		                foreach ($time_data as $po_id => $po_data) 
		                {
		                	foreach ($po_data as $item_id => $item_data) 
		                	{
		                		foreach ($item_data as $color_id => $color_data) 
		                		{
		                			foreach ($color_data as $prod_date => $prod_data) 
		                			{
		                				foreach ($prod_data as $floor_id => $floor_data) 
		                				{
		                					foreach ($floor_data as $line_id => $c_data) 
		                					{
		                						foreach ($c_data as $key => $cid) 
		                						{	                							
		                						
		                							$po_wise_country_name[$time][$po_id][$item_id][$color_id][$prod_date][$floor_id][$line_id] .= $country_arr[$cid].",";
		                						}
		                					}
		                				}
		                			}
	                			}
	                		}
	                	}
	                }
	                $po_wise_mst_id = [];
	                foreach ($mst_id_arr as $time => $time_data)
	                {
		                foreach ($time_data as $po_id => $po_data) 
		                {
		                	foreach ($po_data as $item_id => $item_data) 
		                	{
		                		foreach ($item_data as $color_id => $color_data) 
		                		{	
		                			foreach ($color_data as $prod_date => $date_data) 
		                			{  
		                				foreach ($date_data as $floor_id => $floor_data) 
		                				{
		                					foreach ($floor_data as $line_id => $mst_data) 
		                					{
		                						foreach ($mst_data as $key => $mst_id) {	                							
		                						
		                							$po_wise_mst_id[$time][$po_id][$item_id][$color_id][$prod_date][$floor_id][$line_id] .= $mst_id.",";
		                						}
		                					}
		                				}
		                			}
	                			}
	                		}
	                	}
	                }
					
					// echo "<pre>";
					// print_r($po_wise_mst_id);
					?>
					<div class="summary-data" style="margin-bottom: 30px;">
						<table width="440" cellspacing="0" border="1" class="rpt_table" rules="all">
							<thead>
								<tr class="gd-color">
									<td colspan="6"><b>Summary</b></td>
								</tr>
								<tr>
									<th width="20">Si.</th>
									<th style="word-wrap: break-word;word-break: break-all;" width="120">Buyer</th>
									<th style="word-wrap: break-word;word-break: break-all;" width="80">Cutting Qty</th>
									<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Input (Inbound)</th>
									<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Input (Outbound)</th>
									<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Sew Input</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1;
								$jj=1;
								//foreach($buyer_data_arr as $job_no=>$job_data)
								//{
									foreach($buyer_data_arr as $buyer_name=>$val)
									{
										if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                            						?>
	                            		<tr bgcolor="<? echo $bgcolor; ?>">
											<td width="20"><? echo $i;?></td>
											<td style="word-wrap: break-word;word-break: break-all; text-align: left;" width="120"><? echo $buyer_library[$buyer_name]; ?></td>
											<td style="word-wrap: break-word;word-break: break-all; text-align: right;" width="80"><? echo number_format($val['cutting_qnty'],0); ?></td>
											<td style="word-wrap: break-word;word-break: break-all; text-align: right;" width="80"><? echo number_format($val['sewingin_inhouse_qnty'],0); ?></td>
											<td style="word-wrap: break-word;word-break: break-all; text-align: right;" width="80"><? echo number_format($val['sewingin_outbound_qnty'],0); ?></td>
											<td style="word-wrap: break-word;word-break: break-all; text-align: right;" width="80"><? echo number_format(($val['sewingin_inhouse_qnty'] + $val['sewingin_outbound_qnty'])); ?></td>
										</tr>
										<?
										$i++;
										$jj++;
									}
								//}
								?>
								
							</tbody>
						</table>				
					</div>
					<!-- ************************************* Detaild Part Start ******************************************* -->
                    <div>
                        <table width="1690" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                            <thead> 	 	 	 	 	 	
                                <tr>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="20">Sl.</th>  
                                    <th style="word-wrap: break-word;word-break: break-all;" width="90">Job No.</th>  
                                    <th style="word-wrap: break-word;word-break: break-all;" width="150">Order No.</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="120">Buyer</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="110">Style</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="150">Item Name</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="150">Color</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Input Date</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="70">Input Time</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="100">Unit Name</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="100">Line</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="150">Country</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing In (Inhouse)</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing In (Out Bound)</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Total Sewing Input</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Remarks</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" width="80">Remarks2</th>
                                 </tr>
                            </thead>
                        </table>
                        <div style="max-height:425px; overflow-y:auto; width:1710px" id="scroll_body">
                            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="1690" rules="all" id="table_body" >
	                            <?
	                            $i = 1;
	                            $jj=1;
	                            ksort($production_data_arr);
	                            foreach($production_data_arr as $time=>$time_data)
	                            {
		                            foreach ($time_data as $po_id => $po_data) 
		                            {
		                            	foreach($po_data as $item_id=>$item_data)
		                            	{
		                            		foreach ($item_data as $color_id=>$color_data)
		                            		{
			                            		foreach($color_data as $date=>$date_data)
			                            		{		                            			
		                            				foreach($date_data as $floor_id=>$floor_data)
		                            				{
		                            					foreach($floor_data as $line_id=>$row)
		                            					{
		                            						if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                            						if($row['sewingin_inhouse_qnty'] || $row['sewingin_outbound_qnty'] != 0)
		                            						{
			                            						?>
			                            						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
								                                    <td style="word-wrap: break-word;word-break: break-all;" width="20"><? echo $i;?></td>  
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="90"><? echo $row['job_no']; ?></td>  
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="150"><? echo $row['po_number']; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="120"><? echo $buyer_library[$row['buyer_name']]; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="110"><? echo $row['style_ref_no']; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="150"><? echo $garments_item[$item_id]; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="150"><? echo $color_library[$color_id]; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: center;" width="80"><? echo $date; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: center;" width="70"><? echo $time; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="100"><? echo $floor_library[$floor_id]; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="100"><? echo $line_id; ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: left;" width="150">
								                                    	<? 
								                                    		// echo chop($po_wise_country_name[$po_id][$time],",");							
								                                    		echo chop($po_wise_country_name[$time][$po_id][$item_id][$color_id][$date][$floor_id][$line_id],",");							

								                                    	?>							                                    		
								                                    	</td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: right;" width="80"><? echo number_format($row['sewingin_inhouse_qnty'],0); ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: right;" width="80"><? echo number_format($row['sewingin_outbound_qnty'],0); ?></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: right;" width="80"><? echo number_format(($row['sewingin_inhouse_qnty'] + $row['sewingin_outbound_qnty']),0); ?></td>
								                                    <?
								                                    	$mstId = $po_wise_mst_id[$time][$po_id][$item_id][$color_id][$date][$floor_id][$line_id];
								                                    ?>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: center;" width="80"><a target="_blank" href="requires/sewing_input_report_controller.php?action=print&po_id=<? echo $po_id;?>&mst_id=<? echo $row['mst_id'];?>&company=<? echo $row['company_id'];?>&floor=<? echo $floor_id; ?>&line=<? echo $line_id; ?>&mstId=<? echo chop($mstId,",");?>">View</a></td>
								                                    <td style="word-wrap: break-word;word-break: break-all;text-align: center;" width="80"><a target="_blank" href="requires/sewing_input_report_controller.php?action=print2&po_id=<? echo $po_id;?>&mst_id=<? echo $row['mst_id'];?>&company=<? echo $row['company_id'];?>&floor=<? echo $floor_id; ?>&line=<? echo $line_id; ?>&mstId=<? echo chop($mstId,",");?>">View2</a></td>
								                                 </tr>
			                            						<?
								                                $i++;
								                                $jj++;	
							                             	}
		                            					}
		                            				}
		                            			}
		                            		}
	                            		}	
	                            	}
	                            	                            	
	                           	}	                            
                            		
                                ?>
                           </table>	
                        </div>    
                    </div>
                    <br />
        </div><!-- end main div -->
         
	<?
	$floor_name = implode(',', $floor_arr);
	$floor_wise_total = implode(',', $floor_total_arr);
		
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}

if($action=="print")
{
	echo load_html_head_contents("Print", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$country_code_arr=return_library_array( "select id,short_name from lib_country", "id", "short_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name'); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$season_nameArr = return_library_array("select id,season_name from  lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");
	
	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}

	$order_wise_dtls=array();
	$sql="SELECT a.id, a.production_type, a.location, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type,a.remarks, sum(b.production_qnty) as production_qnty, d.buyer_name,d.season_buyer_wise, d.job_no, d.style_ref_no, e.po_number, a.item_number_id,d.job_quantity,e.grouping 
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e 
	where a.company_id='$company' and a.id in($mst_id) and a.id=b.mst_id and a.production_type='4' and b.production_type='4' and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	group by a.id, a.production_type,a.location, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type,a.remarks, d.buyer_name,d.season_buyer_wise, d.job_no, d.style_ref_no, e.po_number,a.item_number_id,d.job_quantity,e.grouping    order by a.id desc";

	$dataArray=sql_select($sql);
	foreach ($dataArray as $value) 
	{
		$order_wise_dtls[$value[csf('po_break_down_id')]]['buyer']=$value[csf('buyer_name')];
		$order_wise_dtls[$value[csf('po_break_down_id')]]['style']=$value[csf('style_ref_no')];
		$order_wise_dtls[$value[csf('po_break_down_id')]]['order']=$value[csf('po_number')];
	}
	?>
	<style type="text/css">
		table.info td{ font-weight: bold;font-size: 13px; }
	</style>
	<div style="width:1420px; margin: 30px auto">
		<!-- ******************************** HEADER PARY START *****************************-->	
	    <table width="1420" cellspacing="0" align="center" class="info">
	        <tr>
	            <td colspan="4" align="center" style="font-size:20px"><strong><? echo $company_library[$company]; ?></strong></td>
	        </tr>
	        <tr class="">
	        	<td colspan="4" align="center" style="font-size:14px">  
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
						foreach ($nameArray as $result)
						{ 
							?>
							<? echo $result[csf('plot_no')]; ?> &nbsp;
							<? echo $result[csf('level_no')]?>&nbsp;
							<? echo $result[csf('road_no')]; ?> &nbsp;
							<? echo $result[csf('block_no')];?> &nbsp;
							<? echo $result[csf('city')];?> &nbsp;
							<? echo $result[csf('zip_code')]; ?> &nbsp;
							<? echo $result[csf('province')];?> &nbsp;
							<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							<? echo $result[csf('email')];?> &nbsp;
							<? echo $result[csf('website')];
						}
	                ?> 
	            </td>  
	        </tr>
	        <tr><td colspan="4" align="center" style="font-size:18px"><strong> Sewing Input Report </strong></td></tr>
	        <tr><td colspan="4">&nbsp;  </td></tr>
	        <tr>
	        	<td width="15%"></td>
	        	<td width="35%"></td>
	        	<td width="15%"><strong>Job No. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td width="35%"><? echo $dataArray[0][csf('job_no')]; ?></td>
	        </tr>
	        <tr>
	        	<td width="15%"></td>
	        	<td width="35%"></td>
	        	<td width="15%"><strong>Style Ref. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td width="35%"><? echo $dataArray[0][csf('style_ref_no')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong> To</strong></td>
	        	<td><strong>Internal ref. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('grouping')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong> <? echo $company_library[$company]; ?></strong></td>
	        	
	        	<td><strong>Buyer <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $buyer_arr[$dataArray[0][csf('buyer_name')]]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><? echo $location_arr[$dataArray[0][csf('location')]]; ?></td>
	        	
	        	<td><strong>Order No.  <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('po_number')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong>Location :</strong><? echo $location_arr[$dataArray[0][csf('location')]]; ?></td>
	        	<td><strong>Order Quantity <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('job_quantity')]; ?></td>
	        </tr>
	        <tr>
	        	<td></td>
	        	<td></td>
	        	<td><strong>Item Name <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>	        	
	        </tr>
	        <tr>
	        	<td></td>
	        	<td></td>
	        	<td><strong>Season<span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $season_nameArr[$dataArray[0][csf('season_buyer_wise')]]; ?></td>
	        </tr>
	        <tr><td colspan="4">&nbsp;  </td></tr>
	        
	    </table> 
	    <!-- ******************************** BODY PARY START *****************************-->	
	    <?
			unset($sql);
			$sql="SELECT a.id, a.embel_name, a.embel_type, a.po_break_down_id, a.item_number_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
				where a.production_type=4 and b.production_type=4 and a.po_break_down_id in($po_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
				order by c.color_number_id, c.id";

			$result=sql_select($sql);
			$size_array=array ();
			$color_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
		<div style="width:100%;">
		    <table align="right" cellspacing="0" width="1420"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="60">Input Date</th>
		            <th width="100">Time</th>
		            <th width="80">Chalan No.</th>
		            <th width="60">Floor</th>
		            <th width="150">Line</th>
		            <th width="150">Country</th>
		            <th width="70">Cut Off</th>
		            <th width="70">Country Ship Date</th>
		            <th width="80" align="center"> Color</th>
						<?
		                foreach ($size_array as $sizid)
		                {
		                    ?>
		                        <th width="40"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
		                    <?
		                }
		                ?>
		            <th width="80" align="center">Total Issue Qty.</th>
		            <th width="100">Remarks</th>
		        </thead>
		        <tbody align="center">
		        	<?
		        		$embl_dtls=array(); $embl_arr=array();
		        		$sql_prod="SELECT a.id,a.country_id,c.cutup,c.country_ship_date, a.embel_name,a.production_date,a.challan_no,a.floor_id,a.sewing_line, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks,a.insert_date, a.update_date,c.color_number_id 
		        		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		        		where a.production_type=4 and b.production_type=4 and a.id in($mstId) and a.po_break_down_id in($po_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
		        		group by a.id, a.country_id,c.cutup,c.country_ship_date,a.embel_name,a.production_date,a.challan_no,a.floor_id,a.sewing_line, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks,a.insert_date,a.update_date, c.color_number_id 
		        		order by a.update_date,a.insert_date";
		        		// echo $sql_prod;
		        		$result_prod=sql_select($sql_prod);

		        		foreach ($result_prod as $embl_val) 
		        		{
		        			$embl_dtls[$embl_val[csf('embel_name')]][$embl_val[csf('id')]]=$embl_val[csf('id')];
		        		}
		        		$cutUpArray = array(1 => '1st Cut-Off',2 => '2nd Cut-Off',3 => '3rd Cut-Off');
						$i=1;
						$tot_specific_size_qnty=array();

						foreach ($result_prod as $val) 
						{
							$insert_date = $val[csf('insert_date')];
		                	$update_date = $val[csf('update_date')];
		                	$time = "";	                	
		                	$time = (isset($update_date)) ? date("H",strtotime($update_date)) : date("H",strtotime($insert_date));
							$tot_color_size_qty=0;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($val[csf("embel_name")]==1){ $embel_type=$emblishment_print_type; }
							elseif($val[csf("embel_name")]==2){ $embel_type=$emblishment_embroy_type; }
							elseif($val[csf("embel_name")]==3){ $embel_type=$emblishment_wash_type; }	
							elseif($val[csf("embel_name")]==4){ $embel_type=$emblishment_spwork_type; }
							elseif($val[csf("embel_name")]==5){ $embel_type=$emblishment_gmts_type; }
							?>
							<tr>
		                        <td> <? echo $i;  ?> </td>
		                        <td> <? echo $val[csf("production_date")];?> </td>
		                        <td> <? echo $time; ?> </td>
		                        <td> <? echo $val[csf('challan_no')];?> </td>
		                        <td> <? echo $floor_library[$val[csf('floor_id')]]; ?> </td>
		                        <td> <? echo $line; ?> </td>
		                        <td align="left"> <? echo $country_arr[$val[csf("country_id")]].",".$country_code_arr[$val[csf("country_id")]] ; ?> </td>
		                        <td align="center"><b><? echo $cutUpArray[$val[csf("cutup")]]; ?> </b></td>
		                        <td align="center"> <? echo change_date_format($val[csf("country_ship_date")]); ?> </td>
		                        <td align="left"> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
		                        <?
		                        foreach ($size_array as $sizval)
		                        {
		                        ?>
		                            <td align="right">
		                            <? 
		                            	echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; 

		                            	$tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
		                           		$tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
		                            ?>	
		                            </td>
		                        <?  
		                        }
		                        ?>
		                        <td align="right"> 
		                        	<? 
		                        	echo $tot_color_size_qty; 
		                        	?> 
		                        </td>
		                        <td align="left"> <? echo $val[csf("remarks")]; ?> </td>
		                     </tr>
		            <?
						$i++;	}
					?>
		        </tbody>
		        <tr>
		            <td colspan="10" align="right"><strong>Grand Total : &nbsp;</strong></td>
		            <?
						foreach ($size_array as $sizval)
						{
							?>
		                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?> </td>
		                    <?
						}
					?>
		            <td align="right"><?php echo array_sum($tot_specific_size_qnty); ?> </td>
		            <td>&nbsp;</td>
		        </tr>                           
		    </table>

	        <br>
			 <?
	            echo signature_table(26, $company, "1420px");
	         ?>
		</div>
	</div>
  <p style="border-top: 1px dotted black; height:1px;"></p>
  <!-- Report Duplicate Copy Start -->

    <div style="width:1420px; margin: 30px auto">
		<!-- ******************************** HEADER PARY START *****************************-->	
	    <table width="1420" cellspacing="0" align="center" class="info">
	        <tr>
	            <td colspan="4" align="center" style="font-size:20px"><strong><? echo $company_library[$company]; ?></strong></td>
	        </tr>
	        <tr class="">
	        	<td colspan="4" align="center" style="font-size:14px">  
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
						foreach ($nameArray as $result)
						{ 
							?>
							<? echo $result[csf('plot_no')]; ?> &nbsp;
							<? echo $result[csf('level_no')]?>&nbsp;
							<? echo $result[csf('road_no')]; ?> &nbsp;
							<? echo $result[csf('block_no')];?> &nbsp;
							<? echo $result[csf('city')];?> &nbsp;
							<? echo $result[csf('zip_code')]; ?> &nbsp;
							<? echo $result[csf('province')];?> &nbsp;
							<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							<? echo $result[csf('email')];?> &nbsp;
							<? echo $result[csf('website')];
						}
	                ?> 
	            </td>  
	        </tr>
	        <tr><td colspan="4" align="center" style="font-size:18px"><strong> Sewing Input Report </strong></td></tr>
	        <tr><td colspan="4">&nbsp;  </td></tr>
	        <tr>
	        	<td width="15%"></td>
	        	<td width="35%"></td>
	        	<td width="15%"><strong>Job No. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td width="35%"><? echo $dataArray[0][csf('job_no')]; ?></td>
	        </tr>
	        <tr>
	        	<td width="15%"></td>
	        	<td width="35%"></td>
	        	<td width="15%"><strong>Style Ref. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td width="35%"><? echo $dataArray[0][csf('style_ref_no')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong> To</strong></td>
	        	<td><strong>Internal ref. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('grouping')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong> <? echo $company_library[$company]; ?></strong></td>
	        	
	        	<td><strong>Buyer <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $buyer_arr[$dataArray[0][csf('buyer_name')]]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><? echo $location_arr[$dataArray[0][csf('location')]]; ?></td>
	        	
	        	<td><strong>Order No.  <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('po_number')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong>Location :</strong><? echo $location_arr[$dataArray[0][csf('location')]]; ?></td>
	        	<td><strong>Order Quantity <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('job_quantity')]; ?></td>
	        </tr>
	        <tr>
	        	<td></td>
	        	<td></td>
	        	<td><strong>Item Name <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>	        	
	        </tr>
	        <tr>
	        	<td></td>
	        	<td></td>
	        	<td><strong>Season<span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $season_nameArr[$dataArray[0][csf('season_buyer_wise')]]; ?></td>
	        </tr>
	        <tr><td colspan="4">&nbsp;  </td></tr>
	        
	    </table> 
	    <!-- ******************************** BODY PARY START *****************************-->	
	    <?
			unset($sql);
			$sql="SELECT a.id, a.embel_name, a.embel_type, a.po_break_down_id, a.item_number_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
				where a.production_type=4 and b.production_type=4 and a.po_break_down_id in($po_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
				order by c.color_number_id, c.id";

			$result=sql_select($sql);
			$size_array=array ();
			$color_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
		<div style="width:100%;">
		    <table align="right" cellspacing="0" width="1420"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="60">Input Date</th>
		            <th width="100">Time</th>
		            <th width="80">Chalan No.</th>
		            <th width="60">Floor</th>
		            <th width="150">Line</th>
		            <th width="150">Country</th>
		            <th width="70">Cut Off</th>
		            <th width="70">Country Ship Date</th>
		            <th width="80" align="center"> Color</th>
						<?
		                foreach ($size_array as $sizid)
		                {
		                    ?>
		                        <th width="40"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
		                    <?
		                }
		                ?>
		            <th width="80" align="center">Total Issue Qty.</th>
		            <th width="100">Remarks</th>
		        </thead>
		        <tbody align="center">
		        	<?
		        		$embl_dtls=array(); $embl_arr=array();
		        		$sql_prod="SELECT a.id,a.country_id,c.cutup,c.country_ship_date, a.embel_name,a.production_date,a.challan_no,a.floor_id,a.sewing_line, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks,a.insert_date, a.update_date,c.color_number_id 
		        		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		        		where a.production_type=4 and b.production_type=4 and a.id in($mstId) and a.po_break_down_id in($po_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
		        		group by a.id, a.country_id,c.cutup,c.country_ship_date,a.embel_name,a.production_date,a.challan_no,a.floor_id,a.sewing_line, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks,a.insert_date,a.update_date, c.color_number_id 
		        		order by a.update_date,a.insert_date";
		        		// echo $sql_prod;
		        		$result_prod=sql_select($sql_prod);

		        		foreach ($result_prod as $embl_val) 
		        		{
		        			$embl_dtls[$embl_val[csf('embel_name')]][$embl_val[csf('id')]]=$embl_val[csf('id')];
		        		}
		        		$cutUpArray = array(1 => '1st Cut-Off',2 => '2nd Cut-Off',3 => '3rd Cut-Off');
						$i=1;
						$tot_specific_size_qnty=array();

						foreach ($result_prod as $val) 
						{
							$insert_date = $val[csf('insert_date')];
		                	$update_date = $val[csf('update_date')];
		                	$time = "";	                	
		                	$time = (isset($update_date)) ? date("H",strtotime($update_date)) : date("H",strtotime($insert_date));
							$tot_color_size_qty=0;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($val[csf("embel_name")]==1){ $embel_type=$emblishment_print_type; }
							elseif($val[csf("embel_name")]==2){ $embel_type=$emblishment_embroy_type; }
							elseif($val[csf("embel_name")]==3){ $embel_type=$emblishment_wash_type; }	
							elseif($val[csf("embel_name")]==4){ $embel_type=$emblishment_spwork_type; }
							elseif($val[csf("embel_name")]==5){ $embel_type=$emblishment_gmts_type; }
							?>
							<tr>
		                        <td> <? echo $i;  ?> </td>
		                        <td> <? echo $val[csf("production_date")];?> </td>
		                        <td> <? echo $time; ?> </td>
		                        <td> <? echo $val[csf('challan_no')];?> </td>
		                        <td> <? echo $floor_library[$val[csf('floor_id')]]; ?> </td>
		                        <td> <? echo $line; ?> </td>
		                        <td align="left"> <? echo $country_arr[$val[csf("country_id")]].",".$country_code_arr[$val[csf("country_id")]] ; ?> </td>
		                        <td align="center"><b><? echo $cutUpArray[$val[csf("cutup")]]; ?> </b></td>
		                        <td align="center"> <? echo change_date_format($val[csf("country_ship_date")]); ?> </td>
		                        <td align="left"> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
		                        <?
		                        foreach ($size_array as $sizval)
		                        {
		                        ?>
		                            <td align="right">
		                            <? 
		                            	echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; 

		                            	$tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
		                           		$tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
		                            ?>	
		                            </td>
		                        <?  
		                        }
		                        ?>
		                        <td align="right"> 
		                        	<? 
		                        	echo $tot_color_size_qty; 
		                        	?> 
		                        </td>
		                        <td align="left"> <? echo $val[csf("remarks")]; ?> </td>
		                     </tr>
		            <?
						$i++;	}
					?>
		        </tbody>
		        <tr>
		            <td colspan="10" align="right"><strong>Grand Total : &nbsp;</strong></td>
		            <?
						foreach ($size_array as $sizval)
						{
							?>
		                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?> </td>
		                    <?
						}
					?>
		            <td align="right"><?php echo array_sum($tot_specific_size_qnty); ?> </td>
		            <td>&nbsp;</td>
		        </tr>                           
		    </table>

	        <br>
			 <?
	            echo signature_table(26, $company, "1420px");
	         ?>
		</div>
    </div>
 
  <!-- Report Duplicate Copy End -->

	<?
}

if($action=="print2")
{
	echo load_html_head_contents("Print", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$country_code_arr=return_library_array( "select id,short_name from lib_country", "id", "short_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name'); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$season_nameArr = return_library_array("select id,season_name from  lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");
	
	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}

	$order_wise_dtls=array();
	$sql="SELECT a.id, a.production_type, a.location, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type,a.remarks, sum(b.production_qnty) as production_qnty, d.buyer_name,d.season_buyer_wise, d.job_no, d.style_ref_no, e.po_number, a.item_number_id,d.job_quantity,e.grouping 
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e 
	where a.company_id='$company' and a.id in($mst_id) and a.id=b.mst_id and a.production_type='4' and b.production_type='4' and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	group by a.id, a.production_type,a.location, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type,a.remarks, d.buyer_name,d.season_buyer_wise, d.job_no, d.style_ref_no, e.po_number,a.item_number_id,d.job_quantity,e.grouping    order by a.id desc";

	$dataArray=sql_select($sql);
	foreach ($dataArray as $value) 
	{
		$order_wise_dtls[$value[csf('po_break_down_id')]]['buyer']=$value[csf('buyer_name')];
		$order_wise_dtls[$value[csf('po_break_down_id')]]['style']=$value[csf('style_ref_no')];
		$order_wise_dtls[$value[csf('po_break_down_id')]]['order']=$value[csf('po_number')];
	}
	unset($sql);
	$sql="SELECT a.id, a.embel_name, a.embel_type, a.po_break_down_id, a.item_number_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		where a.production_type=4 and b.production_type=4 and a.po_break_down_id in($po_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
		order by c.color_number_id, c.id";

	$result=sql_select($sql);
	$size_array=array ();
	$color_array=array ();
	$qun_array=array ();
	foreach ( $result as $row )
	{
		$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
	}

	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		
	$tbl_width = 1030+(count($size_array)*80);
	?>
	<style type="text/css">
		table.info td{ font-weight: bold;font-size: 13px; }
	</style>
	<div style="width:<? echo $tbl_width;?>px; margin: 30px auto">
		<!-- ******************************** HEADER PARY START *****************************-->	
	    <table width="<? echo $tbl_width;?>" cellspacing="0" align="center" class="info">
	        <tr>
	            <td colspan="4" align="center" style="font-size:20px"><strong><? echo $company_library[$company]; ?></strong></td>
	        </tr>
	        <tr class="">
	        	<td colspan="4" align="center" style="font-size:14px">  
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
						foreach ($nameArray as $result)
						{ 
							?>
							<? echo $result[csf('plot_no')]; ?> &nbsp;
							<? echo $result[csf('level_no')]?>&nbsp;
							<? echo $result[csf('road_no')]; ?> &nbsp;
							<? echo $result[csf('block_no')];?> &nbsp;
							<? echo $result[csf('city')];?> &nbsp;
							<? echo $result[csf('zip_code')]; ?> &nbsp;
							<? echo $result[csf('province')];?> &nbsp;
							<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							<? echo $result[csf('email')];?> &nbsp;
							<? echo $result[csf('website')];
						}
	                ?> 
	            </td>  
	        </tr>
	        <tr><td colspan="4" align="center" style="font-size:18px"><strong> Sewing Input Report </strong></td></tr>
	        <tr><td colspan="4">&nbsp;  </td></tr>
	        <tr>
	        	<td width="15%"></td>
	        	<td width="35%"></td>
	        	<td width="15%"><strong>Job No. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td width="35%"><? echo $dataArray[0][csf('job_no')]; ?></td>
	        </tr>
	        <tr>
	        	<td width="15%"></td>
	        	<td width="35%"></td>
	        	<td width="15%"><strong>Style Ref. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td width="35%"><? echo $dataArray[0][csf('style_ref_no')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong> To</strong></td>
	        	<td><strong>Internal ref. <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('grouping')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong> <? echo $company_library[$company]; ?></strong></td>
	        	
	        	<td><strong>Buyer <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $buyer_arr[$dataArray[0][csf('buyer_name')]]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><? echo $location_arr[$dataArray[0][csf('location')]]; ?></td>
	        	
	        	<td><strong>Order No.  <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('po_number')]; ?></td>
	        </tr>
	        <tr>
	        	<td colspan="2"><strong>Location :</strong><? echo $location_arr[$dataArray[0][csf('location')]]; ?></td>
	        	<td><strong>Order Quantity <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $dataArray[0][csf('job_quantity')]; ?></td>
	        </tr>
	        <tr>
	        	<td></td>
	        	<td></td>
	        	<td><strong>Item Name <span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>	        	
	        </tr>
	        <tr>
	        	<td></td>
	        	<td></td>
	        	<td><strong>Season<span style="float: right;">: &nbsp;</span> </strong></td>
	        	<td><? echo $season_nameArr[$dataArray[0][csf('season_buyer_wise')]]; ?></td>
	        </tr>
	        <tr><td colspan="4">&nbsp;  </td></tr>
	        
	    </table> 
		<div style="width:100%;">
		    <table align="right" cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="60">Input Date</th>
		            <th width="100">Time</th>
		            <th width="80">Chalan No.</th>
		            <th width="60">Floor</th>
		            <th width="150">Line</th>
		            <th width="150">Country</th>
		            <th width="70">Cut Off</th>
		            <th width="70">Country Ship Date</th>
		            <th width="80" align="center"> Color</th>
						<?
		                foreach ($size_array as $sizid)
		                {
		                    ?>
		                        <th width="80"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
		                    <?
		                }
		                ?>
		            <th width="80" align="center">Total Issue Qty.</th>
		            <th width="100">Remarks</th>
		        </thead>
		        <tbody align="center">
		        	<?
		        		$embl_dtls=array(); $embl_arr=array();
		        		$sql_prod="SELECT a.id,a.country_id,c.cutup,c.country_ship_date, a.embel_name,a.production_date,a.challan_no,a.floor_id,a.sewing_line, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks,a.insert_date, a.update_date,c.color_number_id 
		        		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		        		where a.production_type=4 and b.production_type=4 and a.id in($mstId) and a.po_break_down_id in($po_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
		        		group by a.id, a.country_id,c.cutup,c.country_ship_date,a.embel_name,a.production_date,a.challan_no,a.floor_id,a.sewing_line, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks,a.insert_date,a.update_date, c.color_number_id 
		        		order by a.update_date,a.insert_date";
		        		// echo $sql_prod;
		        		$result_prod=sql_select($sql_prod);

		        		foreach ($result_prod as $embl_val) 
		        		{
		        			$embl_dtls[$embl_val[csf('embel_name')]][$embl_val[csf('id')]]=$embl_val[csf('id')];
		        		}
		        		$cutUpArray = array(1 => '1st Cut-Off',2 => '2nd Cut-Off',3 => '3rd Cut-Off');
						$i=1;
						$tot_specific_size_qnty=array();

						foreach ($result_prod as $val) 
						{
							$insert_date = $val[csf('insert_date')];
		                	$update_date = $val[csf('update_date')];
		                	$time = "";	                	
		                	$time = (isset($update_date)) ? date("H",strtotime($update_date)) : date("H",strtotime($insert_date));
							$tot_color_size_qty=0;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($val[csf("embel_name")]==1){ $embel_type=$emblishment_print_type; }
							elseif($val[csf("embel_name")]==2){ $embel_type=$emblishment_embroy_type; }
							elseif($val[csf("embel_name")]==3){ $embel_type=$emblishment_wash_type; }	
							elseif($val[csf("embel_name")]==4){ $embel_type=$emblishment_spwork_type; }
							elseif($val[csf("embel_name")]==5){ $embel_type=$emblishment_gmts_type; }
							?>
							<tr>
		                        <td> <? echo $i;  ?> </td>
		                        <td> <? echo $val[csf("production_date")];?> </td>
		                        <td> <? echo $time; ?> </td>
		                        <td> <? echo $val[csf('challan_no')];?> </td>
		                        <td> <? echo $floor_library[$val[csf('floor_id')]]; ?> </td>
		                        <td> <? echo $line; ?> </td>
		                        <td align="left"> <? echo $country_arr[$val[csf("country_id")]].",".$country_code_arr[$val[csf("country_id")]] ; ?> </td>
		                        <td align="center"><b><? echo $cutUpArray[$val[csf("cutup")]]; ?> </b></td>
		                        <td align="center"> <? echo change_date_format($val[csf("country_ship_date")]); ?> </td>
		                        <td align="left"> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
		                        <?
		                        foreach ($size_array as $sizval)
		                        {
		                        ?>
		                            <td align="right">
		                            <? 
		                            	echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; 

		                            	$tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
		                           		$tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
		                            ?>	
		                            </td>
		                        <?  
		                        }
		                        ?>
		                        <td align="right"> 
		                        	<? 
		                        	echo $tot_color_size_qty; 
		                        	?> 
		                        </td>
		                        <td align="left"> <? echo $val[csf("remarks")]; ?> </td>
		                     </tr>
		            <?
						$i++;	}
					?>
		        </tbody>
		        <tfoot>
			        <tr>
			            <td colspan="10" align="right"><strong>Grand Total : &nbsp;</strong></td>
			            <?
							foreach ($size_array as $sizval)
							{
								?>
			                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?> </td>
			                    <?
							}
						?>
			            <td align="right"><?php echo array_sum($tot_specific_size_qnty); ?> </td>
			            <td>&nbsp;</td>.
			        </tr> 

			        <tr>
			            <td colspan="10" rowspan="14" align="right"><strong>&nbsp;</strong></td>
			        </tr>
			        <?
			        
			        for ($i=0; $i <= 12 ; $i++) 
			        {
			        	?>
			        	<tr height="40">
				            <?
								foreach ($size_array as $sizval)
								{
									?>
				                    <td align="right"><?php //echo $tot_specific_size_qnty[$sizval]; ?> &nbsp;</td>
				                    <?
								}
							?> 
							</tr>
			        <? } ?>   

			        <tr height="40">
			            <td colspan="10" align="right"><strong>Short : &nbsp;</strong></td>
			            <?
							foreach ($size_array as $sizval)
							{
								?>
			                    <td align="right"><?php //echo $tot_specific_size_qnty[$sizval]; ?> </td>
			                    <?
							}
						?>
			        </tr>
			        <tr height="40">
			            <td colspan="10" align="right"><strong>Excess : &nbsp;</strong></td>
			            <?
							foreach ($size_array as $sizval)
							{
								?>
			                    <td align="right"><?php //echo $tot_specific_size_qnty[$sizval]; ?> </td>
			                    <?
							}
						?>
			        </tr>
					<tr height="40">
			            <td colspan="10" align="right"><strong>Reject : &nbsp;</strong></td>
			            <?
							foreach ($size_array as $sizval)
							{
								?>
			                    <td align="right"><?php //echo $tot_specific_size_qnty[$sizval]; ?> </td>
			                    <?
							}
						?>
			        </tr>
			        <tr height="40">
			            <td colspan="10" align="right"><strong>Total : &nbsp;</strong></td>
			            <?
							foreach ($size_array as $sizval)
							{
								?>
			                    <td align="right"><?php //echo $tot_specific_size_qnty[$sizval]; ?> </td>
			                    <?
							}
						?>
			        </tr>
		        </tfoot>                      
		    </table>

	        <br>
			 <?
	            echo signature_table(26, $company, "1420px");
	         ?>
		</div>
	</div>

	<?
}
?>