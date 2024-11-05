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
  extract($_REQUEST);
  echo create_drop_down( "cbo_location", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
  exit();
}

if ($action=="load_drop_down_floor")
{
  extract($_REQUEST);
  echo create_drop_down( "cbo_floor", 130, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 and production_process in (4,5) group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
  exit();
  // production_process in(1,4,5,8,9,10,11,13)
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	// print_r($explode_data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id in($explode_data[0])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id in($explode_data[0])";
		 if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number");
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line", 110,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
	exit();
}

 
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  ); 
	// $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$garments_item=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
	$size_library=return_library_array("SELECT id,size_name from lib_size ","id","size_name");  
	
	$type=str_replace("'","",$type);
	if($type==1) // show button
	{
		if($db_type==2)
		{
			$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
		}
		else if($db_type==0)
		{
			$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
		}

		$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
			order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		//echo $txt_date;cbo_floor
		$cbo_floor=str_replace("'","",$cbo_floor);
		$company_name=str_replace("'","",$cbo_company_name);
		$cbo_location=str_replace("'","",$cbo_location);
		$cbo_line=str_replace("'","",$cbo_line);
		$txt_style_no=str_replace("'","",$txt_style_no);
		// echo $txt_style_no;die;
		$txt_internal_ref=str_replace("'","",$txt_internal_ref);
		$prod_date = $txt_date;

		$prod_con = "";
		$prod_con .= ($company_name==0) ? "" : " and a.serving_company in($company_name)";
		$prod_con .= ($cbo_location==0) ? "" : " and a.location in ($cbo_location)";
		$prod_con .= ($cbo_floor==0) ? "" : " and a.floor_id in($cbo_floor)";
		$prod_con .= ($cbo_line==0) ? "" : " and a.sewing_line=".$cbo_line;
		$prod_con .= ($hidden_po_id=="") ? "" : " and e.id in($hidden_po_id)";
		$prod_con .=($txt_style_no=="") ? "" : " and d.style_ref_no in('$txt_style_no')";
		$prod_con .= ($txt_internal_ref=="") ? "" : " and e.grouping='$txt_internal_ref'";

		// $prod_con .= (str_replace("'","",$txt_style_no)!= "") ? " and d.style_ref_no in($txt_style_no)" : "";
		// $prod_con .= ($prod_date=="") ? "" : " and a.production_date=".$prod_date;

		// echo $prod_con;
		
		$prod_resource_array=array();
		$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id in($company_name)");

		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
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
		
                <div style="width:1100px;"> 
                    <table width="1090" cellspacing="0" style="margin: 20px 0"> 
                        <tr style="border:none;">
                            <td align="right" style="border:none; font-size:14px; font-weight: bold;" width="40%">                                	
                                Company Name                  
                            </td>
                            <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">                                	
                                 :               
                            </td>
                            <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">                                	
                                <? 
								$cbo_company_name_arr=explode(",",$company_name);
								$companyName="";
								foreach ($cbo_company_name_arr as $cmp_name)
								{
									$companyName.= $company_library[$cmp_name].', ';

								}
								echo chop($companyName,',');

								?>     
								
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
                                <? $cbo_location_arr=explode(",",$cbo_location);
								$LocatioName="";
								foreach ($cbo_location_arr as $location_name)
								{
									$LocatioName.= $location_library[$location_name].', ';

								}
								echo chop($LocatioName,',');

								?>     
								
							             
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
					if(str_replace("'","",$prod_date)!="")
					{
						$po_sql= "SELECT DISTINCT a.po_break_down_id
							from 
							pro_garments_production_mst a,pro_garments_production_dtls b
							where a.production_date=$prod_date and a.production_type in(4,5) and b.production_type in(4,5) and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 order by a.po_break_down_id";	
						$production_po_ids="";
						$production_po_id_arr = [];
						$po_res = sql_select($po_sql);
						foreach ($po_res as $key => $val) 
						{	
							$production_po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];					 			
						}
						$production_po_ids=implode(",", $production_po_id_arr);
						if(count($production_po_id_arr)>999 && $db_type==2)
						{
							$po_chunk=array_chunk($production_po_id_arr, 999);
							$prod_po_ids= "";
							foreach($po_chunk as $vals)
							{
								$imp_ids=implode(",", $vals);
								if($prod_po_ids=="") $prod_po_ids.=" and ( a.po_break_down_id in ($imp_ids) ";
								else $prod_po_ids.=" or   a.po_break_down_id in ($imp_ids) ";

							}
							$prod_po_ids.=" )";

						}
						else
						{
							$prod_po_ids= " and a.po_break_down_id in($production_po_ids) ";
						}
					}
					// print_r($prod_po_ids);die;
				
	               
					 $production_mst_sql= "SELECT a.floor_id,a.sewing_line,a.prod_reso_allo,d.buyer_name,c.color_number_id,c.item_number_id,a.po_break_down_id,f.FLOOR_SERIAL_NO,c.order_quantity,a.serving_company,
         				d.style_ref_no,e.po_number,e.grouping,d.style_ref_no,			
						sum(CASE WHEN a.production_type =4 and production_date=$prod_date THEN b.production_qnty ELSE 0 END) AS today_sewingin_qnty,
						sum(CASE WHEN a.production_type =5 and production_date=$prod_date THEN b.production_qnty ELSE 0 END) AS today_sewing_out_qnty,
						sum(CASE WHEN a.production_type =4 and production_date<=$prod_date THEN b.production_qnty ELSE 0 END) AS sewingin_qnty_pre,
						sum(CASE WHEN a.production_type =5 and production_date<=$prod_date THEN b.production_qnty ELSE 0 END) AS sewingout_qnty_pre
						from 
						pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e, lib_prod_floor f
						where a.production_type=b.production_type and a.production_type in(4,5) and c.id=b.color_size_break_down_id $prod_po_ids and b.production_type in(4,5) and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.floor_id = f.id and 
						a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_con and d.job_no=e.job_no_mst AND e.id=a.po_break_down_id AND e.id=c.po_break_down_id and d.status_active=1 and d.is_deleted=0
						group by a.floor_id,a.sewing_line,a.prod_reso_allo,d.buyer_name,c.color_number_id,c.item_number_id,a.po_break_down_id,a.serving_company,
         				d.style_ref_no,e.po_number, f.FLOOR_SERIAL_NO,e.grouping ,c.order_quantity
						order by f.FLOOR_SERIAL_NO, a.floor_id,a.sewing_line,d.buyer_name,e.po_number";
						//echo $production_mst_sql;die;
	                $prod_res = sql_select($production_mst_sql);
					$production_data_arr=array(); 
	                foreach ($prod_res as $key => $val) 
	                {
	                	if($val[csf("prod_reso_allo")]==1)
	                	{
	                		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
							$line_name="";
							foreach($line_resource_mst_arr as $resource_id)
							{
								$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
							}
							//$line_name=chop($line_name," , ");
							
							$sewing_line_id = $line_resource_mst_arr[0]; // always 1st line id will take
						}
						else
						{
							$line_name=$lineArr[$val[csf('sewing_line')]];
							$sewing_line_id=$val[csf('sewing_line')];
						}

						// for line serial
						if($lineSerialArr[$sewing_line_id]=="")
						{
							$lastSlNo++;
							$slNo=$lastSlNo;
							$lineSerialArr[$sewing_line_id]=$slNo;
						}
						else 
						{
							$slNo=$lineSerialArr[$sewing_line_id];
						}
						$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['grouping'] = $val[csf('grouping')];

						$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['order_quantity'] += $val[csf('order_quantity')];

                		$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['today_sewing_in'] += $val[csf('today_sewingin_qnty')];

	                	$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['today_sewing_out'] += $val[csf('today_sewing_out_qnty')];

	                	$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['pre_sewing_in'] += $val[csf('sewingin_qnty_pre')];

	                	$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['pre_sewing_out'] += $val[csf('sewingout_qnty_pre')];

	                	$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['buyer_name'] = $val[csf('buyer_name')];

	                	$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['style_ref_no'] = $val[csf('style_ref_no')];

	                	$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['po_number'] = $val[csf('po_number')];

						$production_data_arr[$val['SERVING_COMPANY']][$val[csf('floor_id')]][$slNo][$line_name][$val[csf('buyer_name')]][$val[csf('color_number_id')]][$val[csf('po_break_down_id')]]['item_number_id'] = $val[csf('item_number_id')];
		                
	                		                	

	                }
					// echo "<pre>";print_r($production_data_arr);echo "</pre>";die();
					$rowspan_arr=array();
					$line_total_arr =array();
					$grand_line_total_arr =array();
                	
					foreach ($production_data_arr as $com_id => $com_data) 
                    { 
						foreach ($com_data as $floor_id => $floor_data) 
                   	   { 
							foreach ($floor_data as $slNo => $sl_data) 
							{                	
								foreach ($sl_data as $line_id => $line_data) 
								{	
									$sewing_in = 0;
									$sewing_out = 0;	                    		
									foreach ($line_data as $buyer_id => $buyer_data) 
									{
										
										foreach ($buyer_data as $color_id => $color_data) 
										{
											foreach ($color_data as $po_id => $val) 
											{	
											
												
												if($val['today_sewing_in'] !=0 || $val['today_sewing_out'] !=0)
												{
													$sewing_in += $val['pre_sewing_in'];
													$sewing_out +=$val['pre_sewing_out'];		                    				
													if(isset($rowspan_arr[$floor_id][$line_id]))
													{
														$rowspan_arr[$floor_id][$line_id] +=1;
													}
													else
													{
														$rowspan_arr[$floor_id][$line_id] = 1;
													}
												}
												
											}
										}
									}
									$line_total_arr[$floor_id][$line_id]['sewing_in_total'] += $sewing_in;
									$line_total_arr[$floor_id][$line_id]['sewing_out_total'] += $sewing_out;
									$grand_line_total_arr[$floor_id][$line_id]['line_grand_total'] += ($line_total_arr[$floor_id][$line_id]['sewing_in_total'] - $line_total_arr[$floor_id][$line_id]['sewing_out_total']);
								}
							}
                       }
					}   
                    // echo "<pre>"; print_r($production_data_arr);die();

             
					// =================================================================
												// Order Qty
					// =================================================================
					$order_sql= "SELECT a.color_number_id,b.id as po_id,a.order_quantity from wo_po_color_size_breakdown a, wo_po_break_down b where b.id=a.po_break_down_id  $prod_po_ids and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
					$order_sql_res = sql_select($order_sql);
					$order_array = array();
					foreach ($order_sql_res as  $v) 
					{ 
						$order_array[$v['PO_ID']][$v['COLOR_NUMBER_ID']] ['PO_QTY'] += $v['ORDER_QUANTITY']; 
					}
					// echo $order_sql; die;
					?>
		
                    <div>
                        <table width="1600" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                            <thead> 	 	 	 	 	 	
                                <tr>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="20">Sl.</th>  
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="90">Line No</th>  
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="150">Buyer</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80">PO.No.</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">Style</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="110">IR/IB</th> 
									<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100">Item Name</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100">Color</th>
									<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100">Order Qnty</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" colspan="3" width="300">Input</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" colspan="3" width="300">Output</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="70">WIP</th>
                                    <th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="70">WIP GT</th>
                                 </tr>
                                 <tr>
                                 	<th style="word-wrap: break-word;word-break: break-all;" width="100">Prev. Total</th>
                                 	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today Input</th>
                                 	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total Input</th>
                                 	<th style="word-wrap: break-word;word-break: break-all;" width="100">Prev. Total</th>
                                 	<th style="word-wrap: break-word;word-break: break-all;" width="100">Today Output</th>
                                 	<th style="word-wrap: break-word;word-break: break-all;" width="100">Total Output</th>
                                 </tr>
                            </thead>
                        </table>
                        <div style="max-height:425px; overflow-y:auto; width:1600px" id="scroll_body">
                            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="1600" rules="all" id="table_body" >
	                            <?
	                            $floor_arr = [];
	                            $floor_total_arr = [];
                        		$grand_in = 0;
                            	$grand_out = 0;
                            	$grand_in_pre = 0;
                            	$grand_out_pre = 0;
                            	$grand_wip = 0;
                            	$grand_wip_gt = 0;
	                            $i=1;
	                            foreach ($production_data_arr as $com_id => $com_data) 
	                            {
									foreach ($com_data as $floor_id => $floor_data) 
									{

										   
										$floor_arr[] = $floor_library[$floor_id];
										?>
											<tr class="gd-color" bgcolor="#dccdcd" style="font-weight: bold; text-align: left;">
												<td colspan="17" align="left" width="1600"><strong><?=$company_library[$com_id]?>&nbsp;&nbsp;&nbsp;<?=$floor_library[$floor_id];?></strong></td>
											</tr>
										<?
										$sub_in = 0;
										$sub_out = 0;
										$sub_in_pre = 0;
										$sub_out_pre = 0;
										$sub_wip = 0;
										$sub_wip_gt = 0;
										ksort($floor_data);
										foreach ($floor_data as $slNo => $sl_data) 
										{	   
																	
											foreach ($sl_data as $line_id => $line_data) 
											{   
												$r=0;
												
												foreach ($line_data as $buyer_id => $buyer_data) 
												{
													
													foreach ($buyer_data as $color_id => $color_data) 
													{
														
														
														foreach ($color_data as $po_id => $val) 
														{  
															$order_qty = $order_array[$po_id][$color_id]['PO_QTY'];
												
															// echo "<pre>";
															// print_r($val);
															// echo "</pre>"; 
															if($val['today_sewing_in'] !=0 || $val['today_sewing_out'] !=0)
															{ 
																
															?> 
																<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
																	<td width="20"><? echo $i; ?></td>  
																	<? if($r==0){?>  
																	<td align="center" valign="middle" rowspan="<? echo $rowspan_arr[$floor_id][$line_id];?>" width="90"><p><? echo $line_id; //$line_library[$line_id]; ?></p></td>
																	<?}?>
																	<td valign="middle" width="150"><p><? echo $buyer_library[$buyer_id]; ?></p></td>

																	<td valign="middle" width="80"><p>
																		<? echo $val['po_number']; ?></p>
																	</td>

																	<td valign="middle" width="110" align="center"><p><? echo $val['style_ref_no']; ?></p></td>
																	<td valign="middle" width="110" align="center"><p><? echo $val['grouping']; ?></p></td>  
																	<td valign="middle" width="100">
																		<p>
																			<? 
																			echo $garments_item[$val["item_number_id"]];
																			?>
																		</p>
																	</td>
																	<td valign="middle" width="100"><p><? echo $color_library[$color_id]; ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format($order_qty); ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format(($val['pre_sewing_in'] - $val['today_sewing_in'])); ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format($val['today_sewing_in']); ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format($val['pre_sewing_in']); ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format(($val['pre_sewing_out'] - $val['today_sewing_out'])); ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format($val['today_sewing_out']); ?></p></td>
																	<td valign="middle" align="right" width="100"><p><? echo number_format($val['pre_sewing_out']); ?></p></td>
																	<td valign="middle" align="right" width="70"><p><? $wip = ($val['pre_sewing_in'] - $val['pre_sewing_out']); echo number_format($wip);?></p></td> 
																	<? if($r==0){?>  
																	<td align="right" valign="middle" rowspan="<? echo $rowspan_arr[$floor_id][$line_id];?>" width="70"><p><? $line_total=$grand_line_total_arr[$floor_id][$line_id]['line_grand_total']; echo number_format($line_total); ?></p></td>  
																	<?}?> 
																	
																</tr>
																<?
																$r++;
																$i++;
																// sum sub total
																$sub_order += $order_qty;
																$sub_in += $val['today_sewing_in'];
																$sub_out += $val['today_sewing_out'];
																$sub_in_pre += $val['pre_sewing_in'];
																$sub_out_pre += $val['pre_sewing_out'];
																$sub_wip += $wip;
																$sub_wip_gt = $sub_wip;	
															}											
															
														}
													}
												}
											}
										}
										?>
											<tr class="gd-color2">
												<td colspan="8" align="right" style="font-weight: bold;">Sub Total </td>
												<td align="right" valign="middle"><? echo number_format($sub_order);?></td>
												<td align="right" valign="middle"><? echo number_format(($sub_in_pre - $sub_in));?></td>
												<td align="right" valign="middle"><? echo number_format($sub_in);?></td>
												<td align="right" valign="middle"><? echo number_format($sub_in_pre);?></td>
												<td align="right" valign="middle"><? echo number_format(($sub_out_pre - $sub_out));?></td>
												<td align="right" valign="middle"><? echo number_format($sub_out);?></td>
												<td align="right" valign="middle"><? echo number_format($sub_out_pre);?></td>
												<td align="right" valign="middle"><? echo number_format($sub_wip);?></td>
												<td align="right" valign="middle"><? echo number_format($sub_wip_gt);?></td>
											</tr>
										<?
										$floor_total_arr[] = $sub_wip_gt;
										// sum grand total
										$gr_order += $sub_order;
										$grand_in += $sub_in;
										$grand_out += $sub_out;
										$grand_in_pre += $sub_in_pre;
										$grand_out_pre += $sub_out_pre;
										$grand_wip += $sub_wip;
										$grand_wip_gt += $sub_wip_gt;
	                                }	
								}											
								unset($rowspan_arr);
								// echo "<pre>";
								// print_r($floor_arr);
								// echo "<pre>";
								// print_r($floor_total_arr);
                                ?>
                           </table>
                           <table width="1600" cellspacing="0" border="1" class="rpt_tables gd-color3" rules="all" id="table_header_1">
                                <tfoot class="gd-color3">
                                	<th width="20">&nbsp;</th>    
                                    <th width="90">&nbsp;</th>
                                    <th width="150">&nbsp;</th>
                                    <th width="80">&nbsp;</th>
                                    <th width="110">&nbsp;</th>
									<th width="110">&nbsp;</th>
									<th width="100">&nbsp;</th>
                                    <th width="100">Grand Total :</th>
                                    <th valign="middle" align="right" width="100"><? echo number_format($gr_order);?></th>
									<th valign="middle" align="right" width="100"><? echo number_format(($grand_in_pre - $grand_in));?></th>
                                    <th valign="middle" align="right" width="100"><? echo number_format($grand_in);?></th>
                                    <th valign="middle" align="right" width="100"><? echo number_format($grand_in_pre);?></th>
                                    <th valign="middle" align="right" width="100"><? echo number_format(($grand_out_pre - $grand_out));?></th>
                                    <th valign="middle" align="right" width="100"><? echo number_format($grand_out);?></th>
                                    <th valign="middle" align="right" width="100"><? echo number_format($grand_out_pre);?></th>
                                    <th valign="middle" align="right" width="70"><? echo number_format($grand_wip);?></th>
                                    <th valign="middle" align="right" width="70"><? echo number_format($grand_wip_gt);?></th>
                            </table>	
                        </div>    
                    </div>
                    <br />
        </div><!-- end main div -->
		<?
	}
	
     ?>    
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
	echo "####".$name.'####show_chart####'.$floor_name.'####'.$floor_wise_total;
	exit();
}



if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );				
				
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			// alert(selected_id.join(','));

			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );

			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'floor_wise_sewing_wip_report_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>   
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
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
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}
	else
	{
		
		//<div class="alert alert-danger">Please enter job or style no to search.</div>
		
		//die;
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];
	
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";	
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name in ($company_id) $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by b.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number order by a.job_no desc"; 
    // echo $sql;
    $result = sql_select($sql);     
		
	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No,Order No", "100,100,50,100,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number","",'','0,0,0,0,0,0','',1) ;
	?>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="660" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="50">SL No</th>
				<th width="100">Company</th>
				<th width="100">Buyer Name</th>
				<th width="50">Year</th>
				<th width="100">Job No</th>
				<th width="100">Style Ref. No</th>
				<th width="100">Order No</th>
			</tr>
		</thead>
	</table>
	<div style="max-height:220px; width:668px; overflow-y:scroll" id="">
		<table style="word-break: break-all;" class="rpt_table" id="tbl_list_search" rules="all" width="660" cellpadding="0" border="1">
			<tbody>
				<?
				$i = 1;
				foreach ($result as $val) 
				{
					$set_data = $i."_".$val['ID']."_".$val['JOB_NO']."_".$val['STYLE_REF_NO'];
					?>
					<tr onClick="js_set_value('<?=$set_data;?>')" style="cursor: pointer;" id="tr_<?=$i;?>" height="20" bgcolor="#FFFFFF">

						<td width="50"><?=$i;?></td>
						<td width="100"><?=$company_library[$val['COMPANY_NAME']];?></td>
						<td width="100"><?=$buyer_arr[$val['BUYER_NAME']];?></td>
						<td width="50"><?=$val['YEAR'];?></td>
						<td width="100"><?=$val['JOB_NO'];?></td>
						<td width="100"><?=$val['STYLE_REF_NO'];?></td>
						<td width="100"><?=$val['PO_NUMBER'];?></td>
					</tr>
					<?
					$i++;					
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="check_all_container">
		<div style="width:100%">
			<div style="width:50%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all"  onClick="check_all_data()"> Check / Uncheck All
			</div>
			<div style="width:50%; float:left" align="left">
				<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
			</div>
		</div>
	</div>
	<?
   exit(); 
}

if($action=="internal_ref_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_internal_ref = new Array;var selected_id_arr = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_internal_ref.push( str[3] );				
				
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_internal_ref.splice( i, 1 );
			}
			// alert(selected_id.join(','));

			var id = ''; var name = '';var internal_ref = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				internal_ref += selected_internal_ref[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			internal_ref = internal_ref.substr( 0, internal_ref.length - 1 );

			//alert(id)
			$('#hide_job_id').val( id );
			
			$('#hide_job_no').val( name );
			$('#hide_internal_ref').val( internal_ref );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_internal_ref" id="hide_internal_ref" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"IR/IB");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'internal_ref_list_view', 'search_div', 'floor_wise_sewing_wip_report_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>   
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="internal_ref_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//print_r($data) ;
	
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
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}
	else
	{
		
		//<div class="alert alert-danger">Please enter job or style no to search.</div>
		
		//die;
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="b.grouping";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];
	
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";	
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.grouping from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name in($company_id) $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by b.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,grouping order by a.job_no desc"; 
     //echo $sql;
    $result = sql_select($sql);     
		
	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No,Order No", "100,100,50,100,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number","",'','0,0,0,0,0,0','',1) ;
	?>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="660" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="50">SL No</th>
				<th width="100">Company</th>
				<th width="100">Buyer Name</th>
				<th width="50">Year</th>
				<th width="100">Job No</th>
				<th width="100">IR/IB No</th>
				<th width="100">Order No</th>
			</tr>
		</thead>
	</table>
	<div style="max-height:220px; width:668px; overflow-y:scroll" id="">
		<table style="word-break: break-all;" class="rpt_table" id="tbl_list_search" rules="all" width="660" cellpadding="0" border="1">
			<tbody>
				<?
				$i = 1;
				foreach ($result as $val) 
				{
					$set_data = $i."_".$val['ID']."_".$val['JOB_NO']."_".$val['GROUPING'];
					?>
					<tr onClick="js_set_value('<?=$set_data;?>')" style="cursor: pointer;" id="tr_<?=$i;?>" height="20" bgcolor="#FFFFFF">

						<td width="50"><?=$i;?></td>
						<td width="100"><?=$company_library[$val['COMPANY_NAME']];?></td>
						<td width="100"><?=$buyer_arr[$val['BUYER_NAME']];?></td>
						<td width="50"><?=$val['YEAR'];?></td>
						<td width="100"><?=$val['JOB_NO'];?></td>
						<td width="100"><?=$val['GROUPING'];?></td>
						<td width="100"><?=$val['PO_NUMBER'];?></td>
					</tr>
					<?
					$i++;					
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="check_all_container">
		<div style="width:100%">
			<div style="width:50%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all"  onClick="check_all_data()"> Check / Uncheck All
			</div>
			<div style="width:50%; float:left" align="left">
				<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
			</div>
		</div>
	</div>
	<?
   exit(); 
}
?>