<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$agent_name=str_replace("'","",$cbo_agent);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
			$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			$buyer_id_cond_2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
			$buyer_id_cond="";
			$buyer_id_cond_2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond_2=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	if(str_replace("'","",$cbo_year_from)!=0 && str_replace("'","",$cbo_month_from)!=0)
	{
		$year_from=str_replace("'","",$cbo_year_from);
		$month_from=str_replace("'","",$cbo_month_from);
		$start_date=$year_from."-".$month_from."-01";
		$year_to=str_replace("'","",$cbo_year_to);
		$month_to=str_replace("'","",$cbo_month_to);
		//echo str_replace("'","",$cbo_fabric_type);die;
		$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
		$end_date=$year_to."-".$month_to."-$num_days";
		if($db_type==0) 
		{
			$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
			$order_by_cond="DATE_FORMAT(c.process_end_date, '%Y%m')";
		}
		if($db_type==2) 
		{
			//echo "sdsdsd";die;
			 $date_cond=" and c.process_end_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
			$order_by_cond="to_char(c.process_end_date,'YYYY-MM')";
		}
	}
	
	
	if(str_replace("'","",$cbo_fabric_type)!=0) $fab_type_cond=" and c.fabric_type in(".str_replace("'","",$cbo_fabric_type).")"; else $fab_type_cond="";
	//echo $fab_type_cond;die;
	ob_start();
	$load_hr_arr=array();
	$load_min_arr=array();
	$load_date_arr=array();
	$load_batch_no_arr=array();
	
	$sql_data=("select a.id, a.batch_no, a.color_id, a.color_range_id, a.batch_weight as batch_qty, c.production_date as process_end_date, c.end_hours, c.end_minutes, c.remarks, c.fabric_type 
	from pro_batch_create_mst a,pro_fab_subprocess c 
	where a.company_id=$company_name  and c.batch_id=a.id and a.entry_form=0 and c.entry_form=35  and c.load_unload_id=2 and c.result in(1)  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $date_cond  $fab_type_cond order by  $order_by_cond ");
	//echo $sql_data;
	
	$result_data=sql_select($sql_data);
	$batch_date_data_arr=array();$no_of_batch_arr=array();$unload_batch_no_arr=array();$unload_remark_arr=array();$unload_time_hr_arr=array();$unload_time_min_arr=array();
	$date_unload_arr=$color_range_unload_arr=$batch_id_arr=array(); 
	foreach($result_data as $row)
	{
		$tot_batch_number=count($row[csf("batch_no")]);
		//$batch_no=$row[csf("id")];
		$batch_date_data_arr[date("Y-m",strtotime($row[csf("process_end_date")]))][$row[csf("fabric_type")]][$row[csf("color_range_id")]]+=$row[csf("batch_qty")];
		$no_of_batch_arr[date("Y-m",strtotime($row[csf("process_end_date")]))][$row[csf("fabric_type")]][$row[csf("color_range_id")]]+=$tot_batch_number;
		$unload_batch_no_arr[date("Y-m",strtotime($row[csf("process_end_date")]))][$row[csf("fabric_type")]][$row[csf("color_range_id")]].=$row[csf("id")].",";
		$date_unload_arr[$row[csf("id")]]=$row[csf("process_end_date")];
		$unload_time_min_arr[$row[csf("id")]]=$row[csf("end_minutes")];
		$unload_time_hr_arr[$row[csf("id")]]=$row[csf("end_hours")];
		$batch_id_arr[$row[csf("id")]]=$row[csf("id")];
		
		
	}
	
	if(!empty($batch_id_arr))
	{ 
		$batch_id_arr_chank=array_chunk(array_unique($batch_id_arr),999);
		$load_time_sql="select a.color_range_id,c.batch_id,c.batch_no,c.load_unload_id,c.process_end_date,c.end_hours,c.end_minutes 
		from pro_fab_subprocess c,pro_batch_create_mst a 
		where a.id=c.batch_id and c.load_unload_id=1 and c.entry_form=35 and a.entry_form=0 and c.company_id=$company_name and c.status_active=1  and c.is_deleted=0 and a.status_active=1  and a.is_deleted=0";
		$p=1;
		foreach($batch_id_arr_chank as $batch_id)
		{
			if($p==1) $load_time_sql .=" and (a.id in(".implode(',',$batch_id).")"; else $load_time_sql .=" or a.id in(".implode(',',$batch_id).")";
			$p++;
		}
		  $load_time_sql .=")  order by c.process_end_date ";
		//echo $load_time_sql."<br>";die;
		$load_time_data=sql_select($load_time_sql);
		foreach($load_time_data as $row_time)// for Loading time
		{
			$load_batch_no_arr[date("Y-m",strtotime($row_time[csf("process_end_date")]))].=$row_time[csf("batch_id")].",";
			$load_hr_arr[$row_time[csf("batch_id")]]=$row_time[csf("end_hours")];
			$load_min_arr[$row_time[csf("batch_id")]]=$row_time[csf("end_minutes")];
			$load_date_arr[$row_time[csf("batch_id")]]=$row_time[csf("process_end_date")];
			//$load_date_arr[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		}
	}
	//var_dump($batch_date_data_arr);die;
	//print_r($batch_date_data_arr);		
	?>
	<br>
    
	<fieldset style="width:640px;">
        <table cellpadding="0" cellspacing="0" width="640">
            <tr>
               <td align="center" colspan="7" width="100%"  class="form_caption" style="font-size:16px;"><? echo $company_library[$company_name]; ?></td>
            </tr>
             <tr>
               <td align="center" colspan="7" width="100%"  class="form_caption"><strong>Color Wise Production and Dyeing Status Report</strong></td>
            </tr>
        </table>
        <?	
        foreach($batch_date_data_arr as $date_key=>$fab_type)
        { 
            $month_value=explode("-",$date_key);
            $num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
            $full_month=$date_key;
            echo  '<b>Month:</b> ' .$months[$month_value[1]*1]."-".$month_value[0].'<br/>'; 
            ?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" >
                <thead>
                    <tr>
                    <th width="40">SL</th>
                    <th width="100">Fabric Type</th>
                    <th width="100">Color Range</th>
                    <th width="100">No OF Batch</th>
                    <th width="100">Batch Qty In Kg</th>
                    <th width="100">Total Duration Time(HRS)</th>
                    <th width="">Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:660px; max-height:450px; overflow-y:scroll" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="table_body" >
            <?
            //print_r($batch_date_data_arr);
            /*foreach($load_batch_no_arr as $row_date_load)
            {
                $tot_date_load=date("Y-m-d",strtotime($row_date_load));
            }*/
			
            $load_time=$tot_hr_load.':'.$tot_min_load;
            $unload_time=$tot_hour_unload.':'.$tot_min_unload;
            $new_date_time_unload=($tot_date_unload.' '.$unload_time.':'.'00');
            $new_date_time_load=($date_key.'-'.$num_days.' '.$load_time.':'.'00');
            $i=1;$total_batch_qty=0;$total_no_of_batch=0;
			
            foreach($fab_type as $fab_key=>$color_range_id)
            {
                //print_r($color_range_id); 
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                foreach($color_range_id as $color_key=>$batch_qty )
                {
                    $tot_no_of_batch=$no_of_batch_arr[date("Y-m",strtotime($date_key))][$fab_key][$color_key];
                    $total_time=0;
					$batch_id_unload=chop($unload_batch_no_arr[$date_key][$fab_key][$color_key]," , ");
                    $batch_num=explode(",",chop($unload_batch_no_arr[$date_key][$fab_key][$color_key]," , "));
					
                    foreach($batch_num as $batch_row)
                    {
                        $process_end_date_unload=$date_unload_arr[$batch_row];
                        $process_min_unload=$unload_time_min_arr[$batch_row];
                        $process_hr_unload=$unload_time_hr_arr[$batch_row];
                        $process_end_date_load=$load_date_arr[$batch_row];
                        $process_min_load=$load_min_arr[$batch_row];
                        $process_hr_load=$load_hr_arr[$batch_row];
                        $new_date_time_unload=($process_end_date_unload.' '.$process_hr_unload.":".$process_min_unload.':'.'00');
                        $new_date_time_load=($process_end_date_load.' '.$process_hr_load.":".$process_min_load.':'.'00');
                        $total_time+=datediff(n,$new_date_time_load,$new_date_time_unload);
                    }
                    $hr=floor($total_time/60).":".$total_time%60;
					//$hr=$total_time;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                        <td width="40"  ><? echo $i; ?></td>
                        <td width="100" ><p><? echo $fabric_type_for_dyeing[$fab_key];//$buyer_arr_library[$buyer_id]; ?>&nbsp;</p></td>
                        <td width="100" ><p><? echo  $color_range[$color_key]; ?>&nbsp;</p></td>
                        <td width="100" align="right"><p><a href="##" onClick="generate_issue_detail('<? echo $batch_id_unload; ?>','<? echo $full_month;?>','<? echo $company_name; ?>','batch_popup_detail')"><? echo  $tot_no_of_batch; ?></a> &nbsp;</p></td>
                        <td align="right" width="100"><? echo number_format($batch_qty); ?></td>
                        <td align="right" width="100"><? echo $hr; ?></td>
                        <td><? //echo $unload_remark; ?></td>
                    </tr>
                    <? 
                    $i++;
                    $total_batch_qty+=$batch_qty;
                    $total_no_of_batch+=$tot_no_of_batch;
                }
                
            }
            ?>
            <tfoot>
                <tr>
                    <th width="100"  colspan="3" align="right">Total</th>
                    <th width="100" ><? echo number_format($total_no_of_batch); ?></th>
                    <th width="100" align="right"><? echo number_format($total_batch_qty); ?></th>
                    <th width="100"></th>
                     <th align="right"><? //echo number_format($total_tot_sales_qnty_val,2,'.',''); ?></th>
                </tr>
            </tfoot>    
            </table> 
            </div>
            <?
        } // first loop end
        ?>
    </fieldset> 
    
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
	exit();	
}
if($action=="batch_popup_detail")
{
		echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);
		$month_value=explode("-",$month);
		$num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
		$full_date_end=$month."-".$num_days;
		//echo $$batch_id;die;
		$batch_id=str_replace("'","",$batch_id);
		$full_date_start=$month."-01";
	 	if($db_type==0) 
		{
			$date_cond=" and a.process_end_date between '$full_date_start' and '$full_date_end'";
		}
		if($db_type==2) 
		{
			 $date_cond=" and a.process_end_date between '".date("j-M-Y",strtotime($full_date_start))."' and '".date("j-M-Y",strtotime($full_date_end))."'";
		}
		$load_hr_arr=array();
		$load_min_arr=array();
		$load_date_arr=array();
		$batch_id_arr=array_unique(explode(",",$batch_id));
		if(!empty($batch_id_arr))
		{
			$batch_id_arr_chank=array_chunk($batch_id_arr,999);
			$load_time_sql="select a.batch_id, a.batch_no, a.load_unload_id, a.process_end_date, a.end_hours, a.end_minutes 
			from pro_fab_subprocess a, pro_batch_create_mst b 
			where a.load_unload_id=1 and b.id=a.batch_id and a.entry_form=35 and b.entry_form=0 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 
			";
			$p=1;
			foreach($batch_id_arr_chank as $batch_id)
			{
				if($p==1) $load_time_sql .=" and (a.batch_id in(".implode(',',$batch_id).")"; else $load_time_sql .=" or a.batch_id in(".implode(',',$batch_id).")";
				$p++;
			}
		  	$load_time_sql .=")  order by a.process_end_date ";
			//echo $load_time_sql;die;
			$load_time_data=sql_select($load_time_sql);
			foreach($load_time_data as $row_time)// for Loading time
			{
				$load_min_arr[$row_time[csf("batch_id")]]=$row_time[csf("end_minutes")];
				$load_hr_arr[$row_time[csf("batch_id")]]=$row_time[csf("end_hours")];
				$load_date_arr[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
				//$load_date_arr[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
			}
		}
		
		
		
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
	<fieldset style="width:750px; margin-left:3px">
     <div style="width:750px;" align="center">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
		<div id="report_div" style="width:750px;" align="center">
        <table  border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
         <tr> 
        <td colspan="8"  align="left"><strong>Batch Details</strong></td>
        </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="left">
            <thead>
                <th width="30">Sl</th>
                <th width="70">Batch No</th>
                <th width="100">Batch Date</th>
                <th width="150"> Color Range</th>
                <th width="100">Loading Time</th>
                <th width="100">Unloading Time</th>
                <th width="100"> Duration(Hr)</th>
                <th width="">Remarks</th>
            </thead>
			 </table>
		<div style=" max-height:380px; overflow-y:scroll; width:770px;" id="scroll_body">
        <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="tbl_list">
                <?
			 $i=1;
		 $trimsArray="select  a.company_id, a.batch_id, a.batch_no, b.batch_date, a.production_date as process_end_date,a.end_hours, a.end_minutes, a.load_unload_id,a.entry_form,a.remarks,b.color_range_id 
		 from pro_fab_subprocess a , pro_batch_create_mst b 
		 where  a.company_id=$company_name and b.id=a.batch_id and a.status_active=1  and  a.is_deleted=0 and b.entry_form=0 and a.entry_form=35 and a.load_unload_id=2 and a.result in(1) $date_cond ";
		if(!empty($batch_id_arr))
		{
			$batch_id_arr_chank=array_chunk($batch_id_arr,999);
			$p=1;
			foreach($batch_id_arr_chank as $batch_id)
			{
				if($p==1) $trimsArray .=" and (a.batch_id in(".implode(',',$batch_id).")"; else $trimsArray .=" or a.batch_id in(".implode(',',$batch_id).")";
				$p++;
			}
		  	$trimsArray .=")  order by a.batch_id ";
		}
					$sql_result=sql_select($trimsArray);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$batch_date=$batch_data_arr[$row[csf('batch_id')]]['date'];
							//$batch_color_range=$batch_data_arr[$row[csf('batch_id')]]['color_range'];
						 	$batch_color_range=$row[csf('color_range_id')];
							$load_min=$load_min_arr[$row[csf("batch_id")]];
							$load_hr=$load_hr_arr[$row[csf("batch_id")]];
							$load_date=$load_date_arr[$row[csf("batch_id")]];
							$load_date_time=change_date_format($load_date).'<br>'.$load_hr.':'.$load_min;
							$load_time=$load_hr.':'.$load_min;
							$unload_time=$row[csf('end_hours')].':'.$row[csf('end_minutes')];
							$unload_date=$row[csf('process_end_date')] ;
							$unload_date_time=change_date_format($unload_date).'<br>'.$unload_time;
							$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date.' '.$load_time.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
							//echo $color_range[$row[csf('color_range_id')]];
								?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td width="100" align="center"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
                            <td width="150"><p><? echo $color_range[$row[csf('color_range_id')]];//$color_range[$batch_color_range]; ?></p></td>
                            <td width="100"><p><? echo $load_date_time; ?></p></td>
                            <td width="100"><p><? echo $unload_date_time; ?></p></td>
                            <td width="100" align="right"><p><? echo   floor($total_time/60).":".$total_time%60; ?></p></td>
							<td width="" title="Unload Remarks" align="right"><p><? echo $row[csf('remarks')]; ?></p></td>
                        </tr>
						<?
						//$tot_req_qty+=$tot_cons_amount;
						//$total_all_over_amount+=$total_amount;
						$i++;
					}
				?>
				 <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right">&nbsp;</td>
                    </tr>
                </tfoot>
                </table>
              </div>  
        </div>
    </fieldset>
    <?
	exit();
}
?>
