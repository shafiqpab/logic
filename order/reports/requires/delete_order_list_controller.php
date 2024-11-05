<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	MONZU 
Creation date 	: 	29-03-2014
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
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  ); 
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$user_array=return_library_array( "select id,user_name from  user_passwd",'id','user_name');
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	extract($_REQUEST); 
	if(str_replace("'","",$cbo_company_name)==0) $company_name="%%"; else $company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		 $buyer_name="%%";  
	}
	else
	{
		$buyer_name=str_replace("'","",$cbo_buyer_name);
	}
	if(db_type==0)
	{
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
	else $txt_date=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	if(db_type==2)
	{
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
	else $txt_date=" and b.pub_shipment_date between ".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)." and ".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."";
	}
	$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom,  a.dealing_marchant, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity,  b.pub_shipment_date,b.updated_by,b.update_date,  b.details_remarks  from wo_po_details_master a, wo_po_break_down b  where  a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' $txt_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=1 and b.status_active=0  order by b.pub_shipment_date,b.id");
?>
	<div style="width:1330px">
        <fieldset style="width:1330px">
            <table width="1330" id="table_header_1" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="60">SL</th>
                        <th width="65">Company</th>
                        <th width="60">Job No</th>
                        <th width="50">Buyer</th>
                        <th width="90">Style Ref</th>
                        <th width="150">PO No</th>
                        <th width="80">Ship Date</th>
                        <th width="90">PO Qnty</th>
                        <th width="30">Uom</th>
                        <th width="60">PO Status</th>
                        <th width="150">Team Member</th>
                        <th width="80">Delete By</th>
                        <th width="80">Delete Date</th>
                        <th width="80">Delete Time</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:1330px"  align="left" id="scroll_body">
            <table width="1313" border="1" class="rpt_table" rules="all" id="table-body">
            <tbody>
            <?
			$i=0;
			foreach($data_array as $row)
			{
			$i++;
			if ($i%2==0)  
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			$cst_update_date=date('d-m-Y',strtotime($row[csf("update_date")]));
			$cst_update_time = date('g:i:s A',strtotime($row[csf("update_date")]));
			?>
                <tr bgcolor="<? echo $bgcolor; ?>" align="center">
                <td width="60" align="left"><input type="checkbox" id="pocheck_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>"/>&nbsp;&nbsp;<? echo $i;  ?></td>
                <td width="65"><? echo $company_library[$row[csf('company_name')]]; ?></td>
                <td width="60"><? echo  $row[csf('job_no_prefix_num')];?></td>
                <td width="50"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                <td width="90"><? echo  $row[csf('style_ref_no')];?></td>
                <td width="150"><? echo  $row[csf('po_number')];?></td>
                <td width="80"><? echo  change_date_format($row[csf('pub_shipment_date')],'dd-mm-yyyy','-');?></td>
                <td width="90" align="right"><? echo  $row[csf('po_quantity')];?></td>
                <td width="30"><? echo  $unit_of_measurement[$row[csf('order_uom')]];?></td>
                <td width="60"><? echo  $order_status[$row[csf('is_confirmed')]];?></td>
                <td width="150"><? echo  $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></td>
                <td width="80"><? echo $user_array[$row[csf('updated_by')]];?>&nbsp;</td>
                <td width="80"><? echo $cst_update_date;?></td>
                <td width="80"><? echo $cst_update_time;?></td>
                <td><? echo  $row[csf('details_remarks')];?>&nbsp;</td>
                </tr>
            <?
			}
			?>
            </tbody>
            <tfoot
         		<tr bgcolor="<? echo $bgcolor; ?>" align="center">
                <td width="60" align="center" colspan="15"><input type="button" value="Update" class="formbutton" style="width:100px" onclick="submit_update(<? echo $i; ?>)"/></td>
                </tr>
            </tfoot>
            </table>
            </div>
        </fieldset>
	</div>
<?
}
if($action=="update")
{
   $con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
    extract($_REQUEST); 	
	$rID=execute_query( "update  wo_po_break_down set status_active=1,is_deleted=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  id in(".$po_ids." ) ",1);
	//echo 1;
	if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "1";
			}
			else
			{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	
}
?>