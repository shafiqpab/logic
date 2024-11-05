<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	$txt_date=str_replace("'","",trim($txt_date));
    $company_id = return_field_value('id', 'lib_company',"company_name=$cbo_company_name","id");
   	$company_cond=$line=$txt_date_cond='';
	if($company_id !='') $company_cond=" and a.company_id=$company_id";
	if($txt_date !='') {
		$txt_date = date("d-M-Y", strtotime($txt_date));
		$txt_date_cond = " and a.issue_date='$txt_date'";
	}

	$start_time_arr=array();

	$start_time_data_arr=sql_select("SELECT SHIFT_NAME, START_TIME as PROD_START_TIME, END_TIME as PROD_END_TIME from shift_duration_entry where production_type=4 and status_active=1 and is_deleted=0");
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row['SHIFT_NAME']]['FS']=$row['PROD_START_TIME'];
		$start_time_arr[$row['SHIFT_NAME']]['SS']=$row['PROD_END_TIME'];
	}

	$start_hour_shift1_arr=explode(':',$start_time_arr[1]['FS']);
	$start_hour_shift2_arr=explode(':',$start_time_arr[1]['SS']);
	$start_hour_shift1=$start_hour_shift1_arr[0];
	$start_hour_shift2=$start_hour_shift2_arr[0];
	
	$start_hour="00:00";
	$start_time=explode(':',$start_hour);

	$hour=0;
	$last_hour=23;
    $start_hour_arr=array();
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$start_hour_shift2-1;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo '<pre>';print_r($start_hour_arr);
    //$start_hour_arr[$j+1]='23:59';

    $buyer_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
    $sql_table=sql_select("select id as ID, table_name as TABLE_NAME from lib_table_entry where company_name=$company_id and table_type=5 and status_active=1 and is_deleted=0 order by table_name");
    $table_name_arr=array();
    foreach ($sql_table as $val) {
    	$table_name_arr[$val['ID']]=$val['TABLE_NAME'];
    }
    //echo '<pre>';print_r($table_name_arr);die;

    $sql="select a.id as ID, a.entry_form as ENTRY_FORM, a.insert_date as INSERT_DATE, a.table_id as TABLE_ID, a.shift_id as SHIFT_ID, sum(b.quantity) as QUANTITY, b.wo_id as WO_ID, c.buyer_po_no as BPO, c.buyer_buyer as BUYER, c.buyer_style_ref as STYLE, d.party_id as PARTY_ID FROM printing_bundle_issue_mst a, printing_bundle_issue_dtls b, subcon_ord_dtls c, subcon_ord_mst d where a.id=b.mst_id and b.wo_dtls_id=c.id and c.mst_id=d.id and a.entry_form=497 and d.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.within_group=2 $company_cond $txt_date_cond group by a.id, a.entry_form, a.insert_date, a.table_id, a.shift_id, b.wo_id, c.buyer_po_no, c.buyer_buyer, c.buyer_style_ref,d.party_id";
	$sql_res=sql_select($sql);
	$production_data_arr=array();	
	foreach ($sql_res as $row) 
	{
		$prod_hour=date('G',strtotime($row['INSERT_DATE']));
		$production_data_arr[$row['TABLE_ID']][$row['BUYER']][$row['STYLE']][$row['BPO']][$prod_hour]+=$row['QUANTITY'];
		$production_data_arr[$row['TABLE_ID']][$row['BUYER']][$row['STYLE']][$row['BPO']]['PARTY_ID']=$buyer_arr[$row['PARTY_ID']];
		$production_data_arr[$row['TABLE_ID']][$row['BUYER']][$row['STYLE']][$row['BPO']]['BUYER']=$row['BUYER'];
		$production_data_arr[$row['TABLE_ID']][$row['BUYER']][$row['STYLE']][$row['BPO']]['STYLE']=$row['STYLE'];
		$production_data_arr[$row['TABLE_ID']][$row['BUYER']][$row['STYLE']][$row['BPO']]['BPO']=$row['BPO'];	
	}
	//echo '<pre>';print_r($production_data_arr);die;
	//$start_hour_shift2=23;
	$current_hour=date('G');
	for($k=$start_hour_shift1; $k<$start_hour_shift2; $k++)
    {
		$count_hour++;
		$cur_hour=substr($start_hour_arr[$k],0,2);
		if ($current_hour == $cur_hour){
			break;
		}
	}
	$row_num=$start_hour_shift1+$count_hour;
	$width=45/$count_hour;

	if ($current_hour<$start_hour_shift1 || $current_hour>=$start_hour_shift2){
		$width=3.21;
	}
	//echo $row_num;die;	
	
	?>
	<style type="text/css">
		table, tr, th, td{ font-size: 20px; border: 1px solid black;}
		.wrd_brk{word-break:break-all;}
		.center{text-align: center;}
	</style>
    <div width="100%">
    	<table width="100%" cellspacing="0" rules="all" id="table_header">
    		<tr style="background-color: #FFC000">
    			<th width="11%" class="wrd_brk center">Close</th>
    			<th width="5%" class="wrd_brk center">Date</th>
    			<th width="8%" class="wrd_brk center"><? echo date('d-m-Y'); ?></th>
    			<th width="12%" class="wrd_brk center"></th>
    			<th width="5%" class="wrd_brk center">Time</th>
    			<th width="5%" class="wrd_brk center"><? echo date("H:i",time()); ?></th>
    			<th width="54%" class="wrd_brk center">Printing Production Live</th>
    		</tr>
    	</table>
    	<br>		
        <table width="100%" cellspacing="0" rules="all" id="table_header"> 	 	 	 	
            <tr style="background-color: #00B050">
                <th width="5%" class="wrd_brk center">Table</th>
                <th width="6%" class="wrd_brk center">Customer</th>
                <th width="5%" class="wrd_brk center">Buyer</th>
                <th width="8%" class="wrd_brk center">Style</th>
                <th width="8%" class="wrd_brk center">B PO</th>
                <th width="4%" class="wrd_brk center">WH</th>
                <th width="5%" class="wrd_brk center">H Target</th>
                <th width="5%" class="wrd_brk center">D Target</th>
                <?
				
				for($k=$start_hour_shift1; $k<$row_num; $k++)
				{
					$cur_hour=substr($start_hour_arr[$k],0,2);      	
					?>
					<th width="<? echo $width; ?>%" class="wrd_brk center" style="<? if (date('G') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>"><? echo substr($start_hour_arr[$k],0,5); ?>
					</th>
					<?
				}

                if ($current_hour<$start_hour_shift1 || $current_hour>=$start_hour_shift2)
				{               
                	?>
                	<th width="4%" class="wrd_brk center" style="<? if ($current_hour < 8 || $current_hour > 20) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>">Night</th>
					<?
				}
				?>	
                <th width="4%" class="wrd_brk center">Total</th>
                <th width="4%" class="wrd_brk center">Achi%</th>
            </tr>

            <?
			$grand_total_table_qty=$grand_total_table_night_qty=0;
			$grand_total_qty=$grand_total_table_qty_night=0;
			$total_table_qty_arr=array();        
			foreach($production_data_arr as $table_id => $buyer_data)
			{
				foreach($buyer_data as $buyer => $style_data)
				{
					foreach($style_data as $style => $bpo_data)
					{
						foreach($bpo_data as $bpo => $row)
						{							
							$total_production_qty=0;
							?>
							<tr style="background-color: #FCD5B4">
								<td width="5%" class="wrd_brk center"><? echo $table_name_arr[$table_id]; ?></td>
								<td width="6%" class="wrd_brk center"><? echo $row['PARTY_ID']; ?></td>
								<td width="5%" class="wrd_brk center"><? echo $row['BUYER']; ?></td>
								<td width="8%" class="wrd_brk center"><? echo $row['STYLE']; ?></td>
								<td width="8%" class="wrd_brk center"><? echo $row['BPO']; ?></td>
								<td width="4%" class="wrd_brk center"></td>
								<td width="5%" class="wrd_brk center"></td>
								<td width="5%" class="wrd_brk center"></td>
								<?
								$total_productionhour_qty=0;
								for($k=$start_hour_shift1; $k<$row_num; $k++)
								{
									?>
									<td width="<? echo $width; ?>%" class="wrd_brk center"><? echo $row[$k]; ?></td>
									<?
									$total_productionhour_qty+=$row[$k];
									$total_table_qty_arr[$k]+=$row[$k];
								}
								
								if ($current_hour<$start_hour_shift1 || $current_hour>=$start_hour_shift2)
								{
									$total_productionhour_night_qty=0;
									for($k=$hour; $k<=$last_hour; $k++)
									{
										if ($current_hour<$start_hour_shift1 || $current_hour>=$start_hour_shift2)
										{
											$total_productionhour_night_qty+=$row[$k];
											$grand_total_table_qty_night+=$row[$k];
										}	
									}
									?>
									<td width="4%" class="wrd_brk center"><? if ($total_productionhour_night_qty > 0) echo $total_productionhour_night_qty; else echo ""; ?></td>
									<?
								}
								$total_production_qty=$total_productionhour_qty+$total_productionhour_night_qty;
								?>	
								<td width="4%" class="wrd_brk center"><? if ($total_production_qty>0) echo $total_production_qty; else echo ""; ?></td>
								<td width="4%" class="wrd_brk center"></td>
							</tr>    
							<?
						}
					}
				}
			}
            ?>            
            <tr style="background-color: #E26B0A">
            	<td width="5%" class="wrd_brk center"><strong>Total</strong></td>
                <td width="6%" class="wrd_brk center"></td>
                <td width="5%" class="wrd_brk center"></td>
                <td width="8%" class="wrd_brk center"></td>
                <td width="8%" class="wrd_brk center"></td>
                <td width="4%" class="wrd_brk center"></td>
                <td width="5%" class="wrd_brk center"></td>
				<td width="5%" class="wrd_brk center"></td>
                <?
                for($k=$start_hour_shift1; $k<$row_num; $k++)
                {
                	?>
                  	<td width="<? echo $width; ?>%" class="wrd_brk center"><strong><? if ($total_table_qty_arr[$k]>0) echo $total_table_qty_arr[$k]; else echo ""; ?></strong></td>
                	<?
                	$grand_total_table_qty+=$total_table_qty_arr[$k];
                }
                if ($current_hour<$start_hour_shift1 || $current_hour>=$start_hour_shift2)
				{               
					?>
                	<td width="4%" class="wrd_brk center"><strong><? if ($grand_total_table_qty_night>0) echo $grand_total_table_qty_night; else echo ""; ?></strong></td>
					<?
					//$grand_total_table_night_qty+=$total_table_qty_night_arr[$k];
				}
				$grand_total_qty=$grand_total_table_qty+$grand_total_table_qty_night;
				?>	
                <td width="4%" class="wrd_brk center"><strong><? if ($grand_total_qty>0) echo $grand_total_qty; else echo ""; ?></strong></td>
                <td width="4%" class="wrd_brk center"></td>
            </tr>            
        </table>
    </div>	
    <?
	exit();
}

?>