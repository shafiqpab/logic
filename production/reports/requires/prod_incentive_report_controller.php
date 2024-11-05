<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_location_id", 130, $blank_array,"id,location_name", 1, "--Select Location--", 1, "load_drop_down( 'requires/prod_incentive_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_line', 'line_td' );",0 );//select id,location_name from lib_location where status_active=1 and is_deleted=0 and FIND_IN_SET($data[0],company_id) order by location_name
	exit();
}

if ($action=="load_drop_down_line")
{
	$data=explode('_',$data);
	//print_r ($data);
	echo create_drop_down( "cbo_line", 100, "select line_no, line_name from lib_employee where company_id=$data[0] group by line_no","line_no,line_name", 1, "--Select Line--", $selected, "",0 );//select a.id,a.line_name from lib_sewing_line a,lib_employee b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and FIND_IN_SET($data[0],b.company_id) and FIND_IN_SET($data[1],b.location_id) and a.id=b.line_no group by line_name order by a.line_name
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	//$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	//$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$bundleArr =return_library_array("select id,pcs_per_bundle from pro_bundle_dtls","id","pcs_per_bundle"); 
	$operationArr = return_library_array("select id,operation_name from lib_sewing_operation_entry","id","operation_name");
	
	$target_day_arr=array();
	$gsddataArray=sql_select("select id, target_per_day_operation, total_smv from ppl_gsd_entry_dtls");
	foreach($gsddataArray as $row)
	{
		$target_day_arr[$row[csf('id')]]['tg']=$row[csf('target_per_day_operation')];
		$target_day_arr[$row[csf('id')]]['smv']=$row[csf('total_smv')];
	}
	$lib_eff_array=array();
	$dataArray=sql_select("select a.designation_id, b.id, b.lower_limit, b.uper_limit, b.taka_day from lib_incentive_scheme_mst a, lib_incentive_scheme_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id");
	
	foreach($dataArray as $row)
	{
		$lib_eff_array[$row[csf('designation_id')]][$row[csf('id')]]['lower']=$row[csf('lower_limit')];
		$lib_eff_array[$row[csf('designation_id')]][$row[csf('id')]]['upper']=$row[csf('uper_limit')];
		$lib_eff_array[$row[csf('designation_id')]][$row[csf('id')]]['tk']=$row[csf('taka_day')];
	}
	
	$in_out_time_array=array();
	$attdataArray=sql_select("select emp_code, attnd_date, sign_in_time, sign_out_time from prod_attendance where attnd_date between $txt_date_from and $txt_date_to");
	foreach($attdataArray as $row)
	{
		$in_out_time_array[$row[csf('emp_code')]][$row[csf('attnd_date')]]['in']=$row[csf('sign_in_time')];
		$in_out_time_array[$row[csf('emp_code')]][$row[csf('attnd_date')]]['out']=$row[csf('sign_out_time')];
	}
	
	if (trim(str_replace("'","",$txt_emp_code))==''){ $emp_code="";}else{$emp_code=" and c.emp_code=$txt_emp_code";}
	
	if(str_replace("'","",$cbo_location_id)==0) $location="%%"; else $location=str_replace("'","",$cbo_location_id);
    if(str_replace("'","",$cbo_line)==0) $line="%%"; else $line=str_replace("'","",$cbo_line);
	
	$table_width=760+($datediff*160);
	$r=1;
	ob_start();	
	?>
	<div style="width:100%;" id="scroll_body"> 
     <fieldset style="width:100%">
        <table width="<? echo $table_width; ?>" cellpadding="2" cellspacing="0" class=""  id="table_header_1" > 
            <tr class="form_caption" style="border:none;">
                <td colspan="10" align="center" style="border:none;font-size:20px; font-weight:bold" ><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption" style="border:none;">
            	
                <td colspan="9" align="center" style="border:none;font-size:12px; font-weight:bold">
                   <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?>
                </td>
            </tr>
        </table>
        <br />
		<table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <thead>
                <tr>
                    <th width="360" colspan="4" >&nbsp;</th>
                	<?
					$date_data_array=array();
                    for($j=0;$j<$datediff;$j++)
					{
                        $newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$pro_dat= " c.prod_date='".$newdate."'";
						
						$date_data_array[$j]=$pro_dat;
                	?>
                    	<th colspan="4" width="170" ><? echo change_date_format($newdate); ?></th>
                    <?
                    }  
					$cyear=date("Y-m-d",time());
                    ?>
                     <th width="200" colspan="2"><? //echo change_date_format($cyear); ?></th>
               </tr>
               <tr>
                    <th width="80">Emp. Code</th>
                    <th width="100" >Name</th>
                    <th width="150" >Operation</th>
                    <th width="30" ></th>
                <?
                    for($j=0;$j<$datediff;$j++)
					{
					?>
                        <th width="40" >Target</th>
                        <th width="40" >Achive</th>
                        <th width="40" >EFF%</th>
                        <th width="40" >TK</th>
					<?
                    }
                    ?>
                     <th width="40" align="right">Total TK</th>
                     <th width="150">  Sign  </th>
               </tr>
               <?
				$sql_con=" SELECT e.id_card_no,c.emp_code,group_concat(distinct(b.operation_id)) as operation_id, e.company_id, e.location_id, e.line_no, e.line_name, e.first_name, e.middle_name, e.last_name, e.designation_id ";
				for($j=0;$j<$datediff;$j++)
				{
					//,group_concat(distinct(CASE WHEN $date_cond THEN b.operation_id END)) AS 'operation_id$j'
					$date_cond=$date_data_array[$j];
					$sql_con .= ",group_concat(CASE WHEN $date_cond THEN a.bundle_dtls END) AS 'bundle_dtls_id$j'
								 ,group_concat(distinct(CASE WHEN $date_cond THEN a.gsd_dtls END)) AS 'gsd_dtls_id$j'
								 ,group_concat(CASE WHEN $date_cond THEN concat_ws('**',a.gsd_dtls,a.bundle_dtls) END) AS 'gsd_bundle_dtls_id$j' ";
				}
			
				$sql_con .= "FROM lib_employee e, pro_scanning_operation c, pro_operation_bar_code a, ppl_gsd_entry_dtls b
	WHERE c.emp_code=e.emp_code and e.company_id=$cbo_company_id and e.location_id like '$location' and e.line_no like '$line' and c.operation_barcode=a.op_code and a.gsd_dtls=b.id and c.status_active=1 and c.is_deleted=0 and c.prod_date between $txt_date_from and $txt_date_to $emp_code GROUP BY c.emp_code, e.line_no order by e.line_no";
				//echo $sql_con;
               ?>
            </thead>
			<?
			$result=sql_select($sql_con); $line_arr=array(); $i=1; $line_data_array=array(); $total_data_array=array();
			foreach($result as $row)
			{
				$operation='';
				$operation_all_id=explode(",",$row[csf('operation_id')]);
				foreach($operation_all_id as $val)
				{ 
					if($operation=='') $operation=$operationArr[$val]; else $operation.=",".$operationArr[$val];
				}
				
				if(!in_array($row[csf('line_no')],$line_arr))
				{
					if($i!=1)
					{
					?>
                    	<tr bgcolor="#EEEEEE">
                        	<td colspan="4" align="right"><b>Sub Total Qnty</b></td>
							<?
                            for($k=0;$k<$datediff;$k++)
                            {
                            ?>
                                <td>&nbsp;</td>
                                <td align="right"><? echo number_format($line_data_array[$k]['ach'],0,'.',''); ?></td>
                                <td>&nbsp;</td>
                                <td align="right"><? echo $line_data_array[$k]['tk']; ?></td>
                            <?
                            }
                            ?>
                            <td align="right"><? echo number_format($line_total_taka,2,'.',''); ?></td>
                            <td>&nbsp;</td>
                        </tr>
                       <?
					   $line_total_taka=0;
					   unset($line_data_array);
					}
				?>
					<tr><td colspan="<? echo 6+$datediff*4; ?>" style="font-size:14px" bgcolor="#CCCCCC"><b><?php echo $row[csf('line_name')]; ?>&nbsp;</b></td></tr>
				<?	
					$line_arr[$i]=$row[csf('line_no')];
				}
				
				if($i%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="80"><p><? echo $row[csf('emp_code')]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('first_name')].' '.$row[csf('middle_name')].' '.$row[csf('last_name')]; ?></p></td>
                    <td width="150"><p><? echo $operation; ?></p></td>
                    <td width="30" align="center">SMV<br />Qnty</td>
                <?
					$date_data_array=array(); 
					$emp_tot_taka=0; $achive_tot=0;
                    for($j=0;$j<$datediff;$j++)
					{
						$target_acheived=0; $target_per_day=0; $target=0; $eff=0; $smv_target=0; $smv_achv=0;
						
						$newdate =add_date(str_replace("'","",$txt_date_from),$j);
						$smv_target=datediff( 'n',$in_out_time_array[$row[csf('emp_code')]][$newdate]['in'],$in_out_time_array[$row[csf('emp_code')]][$newdate]['out']);
						$smv_target=$smv_target-60;
						if($smv_target<0) $smv_target=0;

						$bundle_all_id=explode(",",$row[csf('bundle_dtls_id'.$j)]);
						foreach($bundle_all_id as $bundle_id)
						{
							$target_acheived+=$bundleArr[$bundle_id];
						}
						
						$gsd_dtls_all_id=explode(",",$row[csf('gsd_dtls_id'.$j)]);
						foreach($gsd_dtls_all_id as $gsd_dtls_id)
						{
							$target_per_day+=$target_day_arr[$gsd_dtls_id]['tg'];
						}
						
						$target=$target_per_day/count($gsd_dtls_all_id);
						
						//$eff=($target_acheived/$target)*100;
						
						$gsd_bundle_dtls_id=explode(",",$row[csf('gsd_bundle_dtls_id'.$j)]);
						foreach($gsd_bundle_dtls_id as $val)
						{
							$gsd_bundle=explode("**",$val);
							$smv_achv+=$target_day_arr[$gsd_bundle[0]]['smv']*$bundleArr[$gsd_bundle[1]];
						}
						
						$taka=0;
						$eff=($smv_achv/$smv_target)*100;
						
						foreach($lib_eff_array[$row[csf('designation_id')]] as $key=>$value)
						{
							//echo $key."<br>";
							$lower_limit=$lib_eff_array[$row[csf('designation_id')]][$key]['lower'];
							$upper_limit=$lib_eff_array[$row[csf('designation_id')]][$key]['upper'];
							
							if($eff>=$lower_limit && $eff<=$upper_limit)
							{
								$taka=$lib_eff_array[$row[csf('designation_id')]][$key]['tk'];
								$subtotal+=$taka;
								break;
							}
							else $taka=0;
						}
						
						$line_data_array[$j]['ach']+=$target_acheived;
						$line_data_array[$j]['tk']+=$taka;
						
						$total_data_array[$j]['ach']+=$target_acheived;
						$total_data_array[$j]['tk']+=$taka;
                	?>
                        <td width="40" align="right"><? echo $smv_target."<br>".number_format($target,0,'.',''); ?></td>
                        <td width="40" align="right"><? echo $smv_achv."<br>".$target_acheived; ?></td>
                        <td width="40" align="right"><? echo number_format($eff,2,'.',''); ?></td>
                        <td width="40" align="right"><? echo $taka; ?></td>
                    <?
						$emp_tot_taka+=$taka;
                    }  
                    ?>
                     <td width="50" align="right"><? echo number_format($emp_tot_taka,2,'.',''); ?></td>
                     <td width="150" align="center">&nbsp;</td>
               </tr>          
			<?
				$line_total_taka+=$emp_tot_taka;
				$total_taka+=$emp_tot_taka;
				
				$i++;
			}
			
			if(count($result>0))
			{
			?>
                <tr bgcolor="#EEEEEE">
                    <td colspan="4" align="right"><b>Sub Total Qnty</b></td>
                    <?
                    for($k=0;$k<$datediff;$k++)
                    {
                    ?>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($line_data_array[$k]['ach'],0,'.',''); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo $line_data_array[$k]['tk']; ?></td>
                    <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_total_taka,2,'.',''); ?></td>
                    <td>&nbsp;</td>
                </tr>
            <?
			}
			?>
            <tfoot>
                <th colspan="4" align="right">Grand Total Qnty</th>
                <?
                for($k=0;$k<$datediff;$k++)
                {
                ?>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_data_array[$k]['ach'],0,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo $total_data_array[$k]['tk']; ?></th>
                <?
                }
                ?>
                <th align="right"><? echo number_format($total_taka,2,'.',''); ?></th>
                <th>&nbsp;</th>
            </tfoot>
		</table>
	</fieldset>
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();      
}
