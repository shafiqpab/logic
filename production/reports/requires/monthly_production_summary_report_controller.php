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
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/monthly_production_summary_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/monthly_production_summary_report_controller' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 0, "", $selected, "",0 ); 
	exit();    	 
}


if ($action == "eval_multi_select") 
{
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}
 
if($action=="report_generate")
{ 	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//====================== load library ======================== 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

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
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$prod_date = $txt_date;

	$sql_cond = "";
	$sql_cond .= ($company_name==0) ? "" : " and d.serving_company=$company_name";
	$sql_cond .= ($cbo_location==0) ? "" : " and d.location=$cbo_location";
	$sql_cond .= ($cbo_floor==0) ? "" : " and d.floor_id in($cbo_floor)";
	if(str_replace("'", "", $txt_date_from) !="" && str_replace("'", "", $txt_date_to) !="")
	{
		$sql_cond.=" and d.production_date between $txt_date_from and $txt_date_to";
	}
	// echo $sql_cond;die(); 	
	$sql= "SELECT a.job_no_prefix_num as job_no,a.buyer_name,a.style_ref_no as style,b.id as po_id,b.po_number,c.item_number_id as item_id,d.floor_id,d.sewing_line,d.prod_reso_allo,sum(e.production_qnty) as sewing_out_qty from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type in(5) $sql_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 group by a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.id,b.po_number,c.item_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo order by d.floor_id,d.sewing_line";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	if(count($sql_res)==0)
	{
		?>
		<div style="color: red;font-size: 16px;font-weight: bold;text-align: center;">Data not found!</div>
		<?
		die();
	}
	$data_array = array();
	$po_id_array = array();
	foreach ($sql_res as $val) 
	{
		

		if($val[csf("prod_reso_allo")]==1)
    	{
    		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				if($lineSerialArr[$resource_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$resource_id]=$slNo;
				}
				else $slNo=$lineSerialArr[$resource_id];
			}
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['style'] = $val[csf('style')];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['po_number'] = $val[csf('po_number')];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['buyer_name'] = $val[csf('buyer_name')];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['sewing_out_qty'] += $val[csf('sewing_out_qty')];
		}
		else
		{
			$line_name=$lineArr[$val[csf('sewing_line')]];
			if($lineSerialArr[$line_name]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$line_name]=$slNo;
			}
			else $slNo=$lineSerialArr[$resource_id];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['style'] = $val[csf('style')];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['po_number'] = $val[csf('po_number')];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['buyer_name'] = $val[csf('buyer_name')];
			$data_array[$val[csf('floor_id')]][$slNo][$line_name][$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]]['sewing_out_qty'] += $val[csf('sewing_out_qty')];
		}
		$po_id_array[$val[csf('po_id')]] = $val[csf('po_id')];
	}
	// echo "<pre>";	
	// print_r($data_array);
	// echo "</pre>";	
	$po_id = implode(",", $po_id_array);
    if(count($po_id_array)>999 && $db_type==2)
    {
     	$po_chunk=array_chunk($po_id_array, 999);
     	$po_id_cond= "";
     	foreach($po_chunk as $vals)
     	{
     		$imp_ids=implode(",", $vals);
     		if($po_id_cond=="") $po_id_cond.=" and ( b.id in ($imp_ids) ";
     		else $po_id_cond.=" or   b.id in ($imp_ids) ";

     	}
     	$po_id_cond.=" )";

    }
    else
    {
     	$po_id_cond= " and b.id in($po_id) ";
    }
	// ========================================== for item wise orser qnty ===============================
	$sql_item_qty = "SELECT a.job_no_prefix_num as job_no,b.id as po_id,c.item_number_id as item_id,sum(c.order_quantity*a.total_set_qnty) as order_quantity from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id $po_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.job_no_prefix_num,b.id,c.item_number_id";
	// echo $sql_item_qty;die();
	$sql_item_qty_res = sql_select($sql_item_qty);
	$item_qnty_array=array();
	foreach ($sql_item_qty_res as $val) 
	{
		$item_qnty_array[$val[csf('job_no')]][$val[csf('po_id')]][$val[csf('item_id')]] = $val[csf('order_quantity')];
	}
	
	ob_start();
		
	?>
	<style type="text/css">      
		table tr th,table tr td{word-break: break-all;word-wrap: break-word;}
    </style> 
    <div style="width:880px; margin: 0 auto"> 
        <table width="850" cellspacing="0" style="margin: 20px 0"> 
            <tr style="border:none;">
                <td colspan="9" align="center" style="border:none; font-size:20px;font-weight: bold;" width="100%">                                	
                    <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                 
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="9" align="center" style="border:none; font-size:16px;font-weight: bold;" width="100%"> 
                    Monthly Production Summary Report                    
                </td>                
            </tr> 
            <tr style="border:none;">
                <td colspan="9" align="center" style="border:none; font-size:14px;font-weight: bold;" width="100%">For Date:                         
                    <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?>&nbsp; To &nbsp;<? echo change_date_format(str_replace("'", "", $txt_date_to)); ?>                          
                </td>
            </tr>  
        </table> 
        <div>
            <table width="850" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header_1">
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th width="30">Sl.</th>  
                        <th width="90">Line No</th>  
                        <th width="120">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Style</th>
                        <th width="100">Order No</th>
                        <th width="150">Item Name</th>
                        <th width="80">Order Qty Pcs</th>
                        <th width="80">Prod. Qty</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:300px; overflow-y:auto; width:870px" id="scroll_body">
                <table border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table"  width="850" rules="all" id="table_body">
                	<tbody>
	                    <?
	                    $i=1;
	                    $gr_order_qty = 0;
	                    $gr_prod_qty = 0;
	                    foreach ($data_array as $floor_id => $floor_data) 
	                    {
	                    	?>
	                    		<tr class="gd-color" bgcolor="#dccdcd" style="font-weight: bold; text-align: left;">
	                    			<td colspan="9" align="left" width="100%">Floor :  <? echo $floor_library[$floor_id];?> </td>
	                    		</tr>
	                    	<?
	                    	$floor_wise_order_qty = 0;	                            	
	                    	$floor_wise_prod_qty  = 0;	
							ksort($floor_data);
							foreach ($floor_data as $sl_no => $sl_data) 
							{
								foreach ($sl_data as $line_name => $line_data) 
								{  
									foreach ($line_data as $job_no => $job_data) 
									{
										foreach ($job_data as $po_id => $po_data) 
										{
											foreach ($po_data as $item_id => $val) 
											{   
												$item_qty = $item_qnty_array[$job_no][$po_id][$item_id];                      				
												?> 
												<tr height="20" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
													<td width="30"><? echo $i; ?></td>  
													<td width="90"><? echo $line_name; ?></td>  
													<td width="120"><? echo $buyer_library[$val['buyer_name']]; ?></td>  
													<td width="80"><? echo $job_no; ?></td>  
													<td width="120"><? echo $val['style']; ?></td>  
													<td width="100"><? echo $val['po_number']; ?></td>  
													<td width="150"><? echo $garments_item[$item_id]; ?></td>  
													<td width="80" align="right"><? echo number_format($item_qty,0); ?></td>  
													<td width="80" align="right"><? echo number_format($val['sewing_out_qty'],0); ?></td>											
												</tr>
												<?
												$i++;	
												$gr_order_qty 			+= $item_qty;
												$gr_prod_qty  			+= $val['sewing_out_qty'];	
												$floor_wise_order_qty 	+= $item_qty;	                            	
												$floor_wise_prod_qty  	+= $val['sewing_out_qty'];	                    											
											}
										}
									}
								}
							}
	                    	?>
	                    	<tr>
	                    		<td colspan="7" align="right"><b>Floor Total&nbsp; </b> </td>
	                    		<td align="right"><b><? echo number_format($floor_wise_order_qty,0); ?></b></td>
	                    		<td align="right"><b><? echo number_format($floor_wise_prod_qty,0); ?></b></td>
	                    	</tr>
	                    	<?
	                    }	
	                    ?>
                	</tbody>
               	</table>
               	<table width="850" cellspacing="0" border="1" align="left" class="rpt_table gd-color3" rules="all" id="table_header_1">
                    <tfoot class="gd-color3">
                    	<th width="30"></th>    
                        <th width="90"></th>
                        <th width="120"></th>
                        <th width="80"></th>
                        <th width="120"></th>
                        <th width="100"></th>
                        <th width="150" align="right">Grand Total</th>
                        <th width="80" align="right"><? echo number_format($gr_order_qty,0); ?></th>
                        <th width="80" align="right"><? echo number_format($gr_prod_qty,0); ?></th>
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
?>