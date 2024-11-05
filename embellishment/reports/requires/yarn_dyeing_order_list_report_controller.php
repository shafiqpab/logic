<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "SELECT id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
    $data=explode("_",$data);
	// print_r($data);
	if($data[0]==""){

	}
	 else if($data[0]==1)
    {
        echo create_drop_down( "cbo_party_id", 100, "select comp.id, comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", "", "");
    }
    else
    {
        echo create_drop_down( "cbo_party_id", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]'and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",'', "" );
    }   
    exit();  
} 

if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_pro_type=str_replace("'","", $cbo_pro_type);
	$cbo_order_type=str_replace("'","", $cbo_order_type);
	$cbo_party_id=str_replace("'","", $cbo_party_id);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	
	$buyer_library=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  ); 
	$team_leader=return_library_array( "SELECT id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name"  ); 
	$team_member_name=return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0", "id", "team_member_name"  );
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
	$count_name=return_library_array( "SELECT id,yarn_count from lib_yarn_count where status_active =1 and is_deleted=0", "id", "yarn_count"  );
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');


	// $cond="";
	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";	

	if($cbo_pro_type==0)$prod_type="";else $prod_type=" and a.pro_type=$cbo_pro_type";	
	if($cbo_order_type==0)$order_typ=""; else $order_typ=" and a.order_type=$cbo_order_type";
	if($cbo_party_id==0)$party_typ=""; else $party_typ=" and a.party_id=$cbo_party_id";
	if($cbo_within_group==0)$within_group=""; else $within_group=" and a.within_group=$cbo_within_group";

	if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; 
    else $date_con=" and a.receive_date between $txt_date_from and $txt_date_to";

	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.job_no_prefix_num = $txt_job_no"; 
	
	//============================= MAIN QUERY ================================

	$sql_pi=" SELECT  b.work_order_id, a.pi_number, a.status_active FROM  com_export_pi_mst a, com_export_pi_dtls b
    where  b.pi_id = a.id";
	$sql_data=sql_select($sql_pi);
	$pi_array=array();
	foreach($sql_data as $row){
		$pi_array[$row[csf('work_order_id')]]['pi_number']=$row[csf("pi_number")];
		$pi_array[$row[csf('work_order_id')]]['status_active']=$row[csf("status_active")];
	}

	 $sql=" SELECT a.id, a.company_id, a.yd_job, a.job_no_prefix_num, a.within_group, a.party_id, a.receive_date, a.order_type, a.pro_type, a.team_member, b.yarn_composition_id,sum(b.order_quantity) as order_quantity, b.uom 
	FROM yd_ord_mst a, yd_ord_dtls b
    where  a.id = b.mst_id $company_name $date_con $job_cond $party_typ $prod_type $order_typ $within_group
    group by a.id, a.company_id, a.yd_job, a.job_no_prefix_num, a.within_group, a.party_id, a.receive_date, a.order_type, a.pro_type, a.team_member, b.yarn_composition_id, b.uom order by a.id";
	// echo $sql;
	// AND b.mst_id = d.work_order_id AND d.pi_id = c.id
	$sql_result=sql_select($sql);

	ob_start();
	?>
	<style type="text/css">
		table tr td{word-break: break-all;word-wrap: break-word;}
	</style>
    <div align="center" style="width:1115px;padding-left: 40px;"> 
    
		<table width="1115" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="9" align="center" style="font-size:20px;"><?php echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="9" align="center" style="font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="9" align="center" style="font-size:14px; font-weight:bold">
							<?php echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
						</td>
					</tr>
				</thead>
			</table>
			
            <table width="1115" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="70">Date</th>
                        <th width="100">Job Number</th>
                        <th width="100">Pi No</th>
                        <th width="100">Buyer Name</th>                       
                        <th width="100">Production Type</th>                                
                        <th width="80">Order Type</th>                                
                        <th width="80">UMO</th>                                
                        <th width="80">Status</th>                                
                        <th width="70">Quantity</th>                                
                        <th width="100">Merchandiser</th>                                
                    </tr>
                </thead>
            </table>
           
            <div style="max-height:350px; width:1115px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1115" rules="all" id="table_body" cellpadding="0">
					<tbody>
						<?
						$i=1;
						foreach ($sql_result as $val) 
						{
							$within_group=$val[csf('within_group')];
							if($within_group==1)
							{
								$com_buyer=$company_library[$val[csf('party_id')]];
							}
							else
							{
								$com_buyer=$buyer_arr[$val[csf('party_id')]];
							}
							
								$bgcolor 	= ($i%2==0)?"#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                        <td align="center" width="35"><? echo $i;?></td>		                       
			                        <td align="center" width="70" align="center"><? echo change_date_format($val[csf('receive_date')]); ?></td>			                        
			                        <td align="center" width="100" align="center"> <a href="#"  onClick="yd_print_report('<? echo $val[csf("id")]; ?>','<? echo $val[csf("company_id")]; ?>','<? echo $val[csf("within_group")]; ?>')"> <? echo $val[csf('yd_job')]; ?></a> </td>			                        
			                        <td align="center" width="100" align="center"><? echo  $pi_array[$val[csf('id')]]['pi_number']; ?></td>			                        		                        
			                        <td align="center" width="100" align="center"><? echo $com_buyer; ?></td>			                        		                        
			                        <td align="center" width="100" align="center"><? echo $w_pro_type_arr[$val[csf('pro_type')]];  ?></td>			                        
			                        <td align="center" width="80" align="center"><? echo $w_order_type_arr[$val[csf('order_type')]]; ?></td>			                        
			                        <td align="center" width="80" align="center"><? echo $unit_of_measurement[$val[csf('uom')]]; ?></td>			                        
			                        <td align="center" width="80" align="center"><? echo $row_status[$pi_array[$val[csf('id')]]['status_active']]; ?></td>			                        
			                        <td align="center" width="70" align="center"><? echo number_format($val[csf("order_quantity")],2); ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo $team_member_name[$val[csf('team_member')]]; ?></td>			                        
			                    </tr>
			                    <?
			                    $i++;
			                  
	                	}
	                    ?>
					</tbody>
                </table>
                </div>                
                <table width="1115" border="1" class="rpt_table" rules="all" id="report_table_footer" cellpadding="0" cellspacing="0">
                    <tfoot>	                    
	                    <tr>
	                        <th width="35"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>    
	                        <th width="80"></th>    
	                        <th width="80"></th>    
	                        <th width="80"></th>    
	                        <th width="70"></th>    
	                        <th width="100"></th>    
	                    </tr>
                    </tfoot>
                </table> 	
	        </div><!-- end main div -->
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
	echo "$html**$filename"; 
	exit();
}

?>