<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------

if($action=="report_generate")
{ 
	extract($_REQUEST);
	$company_name=str_replace("'","",$cbo_company_name);
	if($company_name!=0){$company_cond=" and comp.id=$company_name";}
	if($company_name!=0){$com_con=" and company_id=$company_name";}else{$com_con="";}

	$company_library=return_library_array( "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name ASC", "id", "company_name");
	
	$total_company=count($company_library);
	$width=($total_company*260)+450;
	$colspan=$total_company+5;
	

	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date_cond="";
	else $txt_date_cond=" and production_date between $txt_date_from and $txt_date_to"; 



	$sql="select id,company_id,production_date, production_quantity,production_source,remarks from pro_garments_production_mst where production_type='5' $com_con ".set_user_lavel_filtering(' and serving_company','company_id')." $txt_date_cond and status_active=1 and is_deleted=0 order by production_date ASC";
	$sqlResult =sql_select($sql);
	$sewingData=array();
	foreach($sqlResult as $rows)
	{ 
		$sewingData[$rows[csf('production_date')]][$rows[csf('company_id')]][$rows[csf('production_source')]]+=$rows[csf('production_quantity')];
		//$remarks_arr[$rows[csf('production_date')]]=$rows[csf('remarks')];
	}

ob_start();
?>

 <fieldset style="width:<? echo $width; ?>px; margin-top:10px;">
    <table cellpadding="0" cellspacing="0" width="<? echo $width-18; ?>" align="left">
         <tr>
           <td align="center" width="100%" colspan="<? echo $colspan; ?>" class="form_caption">
               <strong>Consolidated Sewing Production Report</strong><br />
               <strong style="font-size:12px;">From <? echo change_date_format(str_replace("'","",trim($txt_date_from))); ?> To <? echo change_date_format(str_replace("'","",trim($txt_date_to))); ?></strong>
           </td>
        </tr>
    </table>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-18; ?>" class="rpt_table" align="left" >
        <thead>
            <tr>
                <th width="40" rowspan="2" align="center">SL</th>
                <th width="80" rowspan="2">Sewing Date</th>
                <?
                foreach($company_library as $company_id=>$company_name)
                {
                ?>
                <th colspan="3"><p><? echo $company_name; ?></p></th>
                <?	
                }
                ?>
                <th colspan="2">Total</th>
                <th rowspan="2">Grand Total</th>
            </tr>
            <tr>
                <?
                foreach($company_library as $company_id=>$company_name)
                {
                    ?>
                    <th width="80">In-house</th>
                    <th width="80">Sub-Con</th>
                    <th width="100">Total</th>
                    <?	
                }
                ?>
                <th width="100">In-house</th>
                <th width="100">Sub-Con</th>
            </tr>
        </thead>
    </table>
	<div style="width:<? echo $width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-18; ?>" class="rpt_table" id="table_body" >
                <? 
                   $i=1;
                   foreach($sewingData as $sewing_date=>$com_data_arr)
                    {
                       $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
                    ?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="80" align="center"><? echo change_date_format($sewing_date); ?></td>
						<?
                        $z=1;
                        $in_house_tot=array();$out_bound_tot=array();$grand_tot=array();
						foreach($company_library as $company_id=>$company_name)
                        {
							$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
							$in_house_tot[$sewing_date] += $com_data_arr[$company_id][1];
							$out_bound_tot[$sewing_date] += $com_data_arr[$company_id][3];
							$total = ($com_data_arr[$company_id][1]+$com_data_arr[$company_id][3]);
							
							$com_in_house_tot[$company_id] += $com_data_arr[$company_id][1];
							$com_out_bound_tot[$company_id] += $com_data_arr[$company_id][3];
							
							
							?>
                            <td width="80" align="right" bgcolor="<? echo $bgc; ?>"><? echo $com_data_arr[$company_id][1];//1=in house ?></td>
                            <td width="80" align="right" bgcolor="<? echo $bgc; ?>"><? echo $com_data_arr[$company_id][3];//3=Oun Bound ?></td>
                            <td width="100" align="right" bgcolor="<? echo $bgc; ?>"><? echo $total;?></td>
                            <?
                        $z++;
						}
                        ?>
                        <td width="100" align="right" bgcolor="#FFFFCC"><? echo $in_house_tot[$sewing_date];?></td>
                        <td width="100" align="right" bgcolor="#FFFFCC"><? echo $out_bound_tot[$sewing_date];?></td>
                        <td align="right" bgcolor="#FFFFCC"><? echo $in_house_tot[$sewing_date]+$out_bound_tot[$sewing_date];?></td>
                    </tr>
                    
				   <? 
                    $i++;
                	}
                ?>   
            </table>
			</div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-18; ?>" class="rpt_table"  align="left">
                <tfoot>
                    <th width="40"></th>
                    <th width="80">Total</th>
					<?
                    foreach($company_library as $company_id=>$company_name)
                    {
                        $grand_in_house+=$com_in_house_tot[$company_id];
                        $grand_out_bound+=$com_out_bound_tot[$company_id];
                        ?>
                        <th width="80"><? echo $com_in_house_tot[$company_id];?></th>
                        <th width="80"><? echo $com_out_bound_tot[$company_id];?></th>
                        <th width="100"><? echo $com_in_house_tot[$company_id]+$com_out_bound_tot[$company_id];?></th>
                        <?	
                    }
                    ?>
                    <th width="100" align="right"><? echo $grand_in_house; ?></th>
                    <th width="100" align="right"><? echo $grand_out_bound; ?></th>
                    <th align="right"><? echo $grand_in_house+$grand_out_bound; ?></th>
                </tfoot>
                
                </table>
      	</fieldset> 
        
        
        
        
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
	$total_data=ob_get_contents();
	ob_end_clean();
	$is_created = fwrite($create_new_doc,$total_data);
	$filename=$user_id."_".$name.".xls";
	echo $total_data.'####'.$filename;
	exit();	
}