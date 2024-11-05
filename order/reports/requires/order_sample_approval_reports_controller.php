<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	05-02-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$group_concat="group_concat";
	$defalt_date_format="0000-00-00";
}
else
{
	$group_concat="wm_concat";
	$defalt_date_format="";
}
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  ); 
$color_library = return_library_array( "select id,color_name from lib_color order by id", "id", "color_name"  );
$sample_library = return_library_array( "select id, sample_name from lib_sample order by id", "id", "sample_name"  ); 
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info", "id", "team_member_name"  );
connect();

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}
if ($action=="load_drop_down_team_member")
{
	if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 120, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 	else
	{
		 echo create_drop_down( "cbo_team_member", 120, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
	}
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	//print_r($_REQUEST);die;
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_name=$cbo_company_name";
	//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name=""; else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		
	$job_cond="";
	if(str_replace("'","",$txt_job_no)!="") 
	{  
		$job_cond=" and a.job_no_prefix_num=".str_replace("'","",$txt_job_no)." ";
		if($db_type==2) $job_cond.=" and extract(year from a.insert_date)=".str_replace("'","",$cbo_year)."";
		if($db_type==0) $job_cond.=" and year(a.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	$style_cond=""; $order_cond=""; $team_cond="";
	if(str_replace("'","",$txt_order_no)!="") $order_cond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'  ";
	if(str_replace("'","",$txt_style)!="") $style_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style)."%'  ";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date=""; else $txt_date=" and b.shipment_date between $txt_date_from and $txt_date_to";
	?>
    <div style="width:2200px">
        <fieldset style="width:2150px">
            <table width="100%">
            <tr>
            <td width="980" valign="top">
            <div id="data_panel1" align="center" style="width:100%">
                 <input type="button" style="width:100px" value="Print" class="formbutton" name="print" id="print" onclick="new_window(1)" />
            </div>
            <div id="print_report_samp" style="width:950px">                
            <div align="center" style="width:950px"><b>Sample Approval Report </b></div>
                <?
                $arr=array (0=>$company_library,1=>$buyer_library);
                $sql="select a.company_name,a.buyer_name,
                    COUNT(c.sample_type_id) AS total_sample,
                    COUNT(CASE WHEN c.send_to_factory_date!='$defalt_date_format' THEN 1 END) AS sample_dept,
                    COUNT(CASE WHEN c.submitted_to_buyer!='$defalt_date_format' THEN 1 END) AS submitted_to_buyer,
                    COUNT(CASE WHEN c.approval_status='3' THEN 1 END) AS approved_quantity,
                    COUNT(CASE WHEN c.approval_status='2' THEN 1 END) AS rejected_quantity,
                    COUNT(CASE WHEN c.approval_status='3' THEN 1 END)/COUNT(c.sample_type_id)*100 as approved_percent
                    from wo_po_details_master a, wo_po_break_down b, wo_po_sample_approval_info c
                    where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and (c.entry_form_id is null or c.entry_form_id=0) $company_name $buyer_name $file_cond $ref_cond $txt_date group by a.company_name, a.buyer_name order by a.company_name, a.buyer_name";
                //echo $sql;die;
                echo  create_list_view("list_view", "Company Name,Buyer Name,Total Sample,Sample Dept.,Submitted To Buyer,Approved Quantity,Rejected Quantity,Approval %", "150,150,100,100,100,100,100,100","950","380",0, $sql , "", "", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,total_sample,sample_dept,submitted_to_buyer,approved_quantity,rejected_quantity,approved_percent", "",'','0,0,1,1,1,1,1,1',"3,total_sample,sample_dept,submitted_to_buyer,approved_quantity,rejected_quantity,''");
                ?>
                </div>                    
                </td>
                <td valign="top" width="680">
                    <div id="data_panel1" align="center" style="width:700px">
                         <input type="button" style="width:100px" value="Print" class="formbutton" name="print" id="print" onclick="new_window(2)" />
                    </div>
                    <div id="print_report_pp" style="width:660px">
                    <div align="center" style="width:660px"><b>PP Sample Summary</b></div>
                        <?
                        $pp_sample_id='';
                        $sql_pp=sql_select("select id from lib_sample where sample_type=2 and status_active=1 and is_deleted=0");
                        foreach($sql_pp as $row_pp)
                        {
                            if($pp_sample_id=="") $pp_sample_id=$row_pp[csf('id')]; else $pp_sample_id.=",".$row_pp[csf('id')];	
                        }
                        
                        $arr=array (0=>$buyer_library);
                        $sql="select a.buyer_name,
                                COUNT(b.po_number) AS no_of_order,
                                COUNT(CASE WHEN c.sample_type_id!='' or c.sample_type_id!=0 THEN 1 END) AS approval_req,
                                COUNT(CASE WHEN c.approval_status='1' THEN 1 END) AS no_of_submitted,
                                COUNT(CASE WHEN c.approval_status='3' THEN 1 END) AS no_of_approved,
                                COUNT(CASE WHEN c.approval_status='3' THEN 1 END)/COUNT(c.sample_type_id)*100 as approved_percent
                              from 
                                wo_po_details_master a, wo_po_break_down b, wo_po_sample_approval_info c
                              where 
                                a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.sample_type_id in ($pp_sample_id) $company_name $buyer_name $file_cond $ref_cond $txt_date group by a.buyer_name order by a.buyer_name";
                                //echo $sql;die;				
                                echo  create_list_view("list_view", "Buyer Name,No. Of order,Approval Req.,No. Of Submitted,No. Of Approved,Approved %", "120,100,100,100,100,100","660","380",0, $sql , "", "", "", 1, "buyer_name,0,0,0,0,0", $arr, "buyer_name,no_of_order,approval_req,no_of_submitted,no_of_approved,approved_percent", "",'','0,1,1,1,1,1',"2,no_of_order,approval_req,no_of_submitted,no_of_approved,''");
                    ?>
                    </div>                
               </td>
            </tr>
       </table>                 
     
     <?
       // $color_id_arr=return_library_array( "select color_mst_id, color_number_id from wo_po_color_size_breakdown", "color_mst_id", "color_number_id"  );
        
        if($shipingStatus==0) $shipingStatus_cond=""; else $shipingStatus_cond=" and b.shiping_status='$shipingStatus'";
        
        if($db_type==0) 
		{
			$sampleCond="concat(c.sample_type_id,'**',c.approval_status,'**',c.sample_comments)";
			$yearCond="year(a.insert_date)";
		}
		else if($db_type==2) 
		{
			$sampleCond="listagg((cast(c.sample_type_id || '**' || c.approval_status || '**' || c.sample_comments as varchar2(4000))),',') within group (order by c.id)";
			$yearCond="TO_CHAR(a.insert_date,'YYYY')";
		}
		
        $sqlSam = "select distinct a.order_uom, a.style_ref_no, a.buyer_name, a.total_set_qnty, a.job_no_prefix_num, a.team_leader, a.dealing_marchant, $yearCond as year, b.id as po_break_down_id, b.job_no_mst, b.po_number, b.grouping as ref_no, b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shipment_date, b.shiping_status, d.color_number_id, $sampleCond as sample_app_data
			from wo_po_details_master a, wo_po_break_down b, wo_po_sample_approval_info c, wo_po_color_size_breakdown d
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.color_number_id=d.color_mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $buyer_name $job_cond $style_cond $team_cond $order_cond $file_cond $ref_cond $txt_date $shipingStatus_cond
		group by  b.id, b.job_no_mst, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shipment_date, c.color_number_id, a.order_uom, a.style_ref_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.insert_date, a.total_set_qnty, a.job_no_prefix_num, b.shiping_status, d.color_number_id
		order by b.shipment_date, b.id";
		//echo $sqlSam;
        $master_sql = sql_select($sqlSam);
        $tagSample_arr=array(); $sampleData_arr=array();
        foreach($master_sql as $row)
        {
			$sampleId=explode(",",$row[csf('sample_app_data')]);
			foreach($sampleId as $sid)
			{
				$exSam=explode("**",$sid);
				$tagSample_arr[$exSam[0]]=$exSam[0].'**'.$exSam[1].'**'.$exSam[2];
				$sampleData_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$exSam[0]]=$exSam[0].'**'.$exSam[1].'**'.$exSam[2];
			}
        }
		sort($tagSample_arr);
		//print_r($tagSample_arr); die;
                            
        $total_sample    = count($tagSample_arr); 
        $sample_td_width = $total_sample*80;
        $div_width       = 1660+$sample_td_width;
        $table_width     = 1660+$sample_td_width;	
        $total_qty       = 0;
        ob_start();
    ?>
        <br/>
        <fieldset>
            <div style="width:<? echo $div_width; ?>" align="left">				
                    <table width="<? echo $table_width; ?>" border="1" rules="all"  class="rpt_table">
                    <label style="alignment-adjust:central"><strong>Sample Details Report</strong></label>
                        <thead>
                            <tr> 
                                <th width="40" rowspan="2">SL</th>
                                <th width="150" rowspan="2">Order Number</th>  
                                <th width="80" rowspan="2">File No</th>  
                                <th width="80" rowspan="2">Ref. No</th>                                     
                                <th width="100" rowspan="2">Buyer</th>
                                <th width="140" rowspan="2">Team</th>
                                <th width="170" rowspan="2">Team Member</th>
                                <th width="50" rowspan="2">Job No</th>
                                <th width="60" rowspan="2">Year</th>
                                <th width="100" rowspan="2">Style </th> 
                                <th width="80" rowspan="2">PO Quantity</th>
                                <th width="70" rowspan="2">Order UOM</th>
                                <th width="70" rowspan="2">PO Qnty (PCS)</th>
                                <th width="90" rowspan="2">Shipment Date</th>
                                <th width="110" rowspan="2">Status</th>
                                <th width="120" rowspan="2" >Color</th>
                                <th width="<? echo $sample_td_width; ?>" colspan="<? echo $total_sample; ?>">Sample Details</th>
                                <th width="" rowspan="2">Remarks</th>
                             </tr>
                            <tr>
                                <?
                                    foreach($tagSample_arr as $key=>$val)
                                    {
										$exval=explode("**",$val);
                                         ?><th width="80"><? echo split_string($sample_library[$exval[0]],80,10);//split_string($val,80,10);?></th><?
                                    }
                                ?> 
                          </tr>
                    </thead>
                 </table>
                <div id="print_rpt_4" style="width:<? echo $table_width+20;?>px; max-height:300px; overflow-y:scroll" >	
                <table id="tbl_details" width="<? echo $table_width; ?>" border="1"  class="rpt_table" rules="all">
                <?	
                    $k=0;$j=0;$po_array=array();
                    foreach($master_sql as $row)  // Master Job  table queery ends here
                    {										 
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$j=$j+1;
						
						$pcs_qnty = $row[csf('total_set_qnty')]; 
						$st=0;
						if(!in_array($row[csf('po_number')],$po_array))
						{
							$total_qty+=$row[csf('po_quantity')]; 
							$total_pcs_qnty+=$row[csf('po_quantity')]*$pcs_qnty; 
							$po_array[$row[csf('po_number')]] = $row[csf('po_number')];
							$st=1;
							$k++;
						}
						
						if($st==0) $row_vanish = "style='color:$bgcolor'";else $row_vanish=""; 
						?>
                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr3_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr3_<? echo $j; ?>" >
                            <td width="40"> <? if($st==1) echo $k;?> </td>
                            <td width="150" align="center" <? echo $row_vanish; ?> ><? echo $row[csf('po_number')]; ?></td>	
                            <td width="80" align="center" <? echo $row_vanish; ?> ><? echo $row[csf('file_no')]; ?></td>	
                            <td width="80" align="center" <? echo $row_vanish; ?> ><? echo $row[csf('ref_no')]; ?></td>	
                            <td width="100" align="center" <? echo $row_vanish; ?>><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                            <td width="140" align="center" <? echo $row_vanish; ?>><? echo $team_library[$row[csf('team_leader')]]; ?></td>
                            <td width="170" align="center" <? echo $row_vanish; ?>><? echo $team_member_library[$row[csf('dealing_marchant')]]; ?></td>
                            <td width="50" align="center" <? echo $row_vanish; ?>><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="60" align="center" <? echo $row_vanish; ?>><? echo $row[csf('year')]; ?></td>
                            <td width="100" align="center" <? echo $row_vanish; ?>><p><? echo $row[csf('style_ref_no')]; ?></p></td>                                
                            <td width="80" align="right" <? echo $row_vanish; ?> > <? echo $row[csf('po_quantity')]; ?></td>
                            <td width="70" align="center" <? echo $row_vanish; ?>><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td> 
                            <td width="70" align="right" <? echo $row_vanish; ?>><? echo $row[csf('po_quantity')]*$pcs_qnty; ?></td> 
                            <td width="90" <? echo $row_vanish; ?>> <? echo change_date_format($row[csf('shipment_date')]); ?> </td>
                            <td width="110" align="center" <? echo $row_vanish; ?>><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                            <?
                            $color_mst_id = $row[csf('color_number_id')];		
                            echo "<td width='120' align='left'><p>".$color_library[$color_mst_id]."</p></td>";
                            
                            foreach($tagSample_arr as $key=>$data)
						    //foreach($samData as $samId=>$data)
                            {
								$exdata=explode("**",$data);
								$samData='';
								$samData=$sampleData_arr[$row[csf('po_break_down_id')]][ $color_mst_id][$exdata[0]];
								$exval="";
                                $exval=explode("**",$samData);
                                //echo "<td width='80' align='left'>".$exval[0].'='.$exval[1].'='.$exval[2]."</td>";
								
								if($exval[1]==0) 
                                    echo "<td width='80' align='left'>&nbsp;</td>";		
                                else if($exval[1]>0) 
                                    echo "<td width='80' align='left'>".$approval_status[$exval[1]]."</td>";	
                                else
                                    echo "<td width='80' align='left'>&nbsp;</td>";	
                            }                                         
                        ?>
                            <td align="center" width=""><a href="javascript:void(0)" onclick="show_comment_info('<? echo $row[csf('job_no_mst')]; ?>')" >View</a> </td>
                        </tr>
                        <? 		
                     }// Master Job  table queery ends here
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="10" align="right"><b>Total</b></th>
                            <th align="right"><b><? echo $total_qty;?></b></th>
                            <th align="left"></th>
                            <th align="right"><b><? echo $total_pcs_qnty;?></b></th>
                            <th colspan="<? echo $total_sample+4; ?>">&nbsp;</th>
                        </tr>
                    </tfoot>    
              </table>
              </div>						
          </div>
    </fieldset>
    <?		
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}
disconnect($con);
?>