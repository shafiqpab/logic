<? 
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Form Will Create Pending Bill Status Report
Functionality   :   
JS Functions    :
Created by      :   Jahid 
Creation date   :   19-03-2016
Updated by      :   Md: Didarul Alam       
Update date     :   26-12-2017  
QC Performed BY :       
QC Date         :   
Comments        :
*/
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];

$sql = "select id,supplier_name from lib_supplier where status_active = 1 and is_deleted = 0";

if($action=="load_drop_down_party_name")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_party_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select a party--", 0, "","");
	}
	else if($data[0]==2)
	{	
		echo create_drop_down( "cbo_party_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select a party--", 0, "","");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 152, $blank_array,"",1, "--Select a party--", 0, "" );
	}

	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_id);
	$cbo_bill_type=str_replace("'","",$cbo_bill_type);
	$start_chln=str_replace("'","",$txt_chln_from);
	$end_chln=str_replace("'","",$txt_chln_to);
	$cbo_year=str_replace("'","",$cbo_year);
    $party_name = str_replace("'","",$cbo_party_name);

    if($db_type==0)
    {
        $booking_without_order="IFNULL(a.booking_without_order,0)";
    }
    else if($db_type==2) 
    {
        $booking_without_order="nvl(a.booking_without_order,0)";
    }

    if($party_name!="0")
    {
        $partyCond = "and a.knitting_company=$party_name";
        $partyCond2 = "and a.dyeing_company=$party_name";
    }

    if($start_chln!="" && $end_chln)
    {
       $challandCond = "and a.challan_no between '$start_chln' and '$end_chln'";
    }

	if($db_type==0) $year_cond=" and year(a.receive_date)='$cbo_year'"; else $year_cond=" and to_char(a.receive_date,'YYYY')='$cbo_year'";

	$company_arr=return_library_array( "select id, company_name from lib_company where status_active = 1 and is_deleted = 0", "id", "company_name");
    $party_arr=return_library_array( "select id,supplier_name from lib_supplier where status_active = 1 and is_deleted = 0", "id", "supplier_name");
	
	ob_start();
	if($cbo_bill_type==1)
	{

        $sql="SELECT a.id,a.knitting_company,a.recv_number,a.challan_no, a.receive_date,a.remarks,d.cons_quantity as challan_qty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,inv_transaction d
            WHERE a.id=b.mst_id AND a.id=d.mst_id AND b.id=c.dtls_id AND a.knitting_source=3 AND a.company_id=$company_name $partyCond $year_cond  AND c.trans_type=1 AND a.entry_form in (2,22,58) AND c.entry_form in (2,22,58) AND a.item_category=13 AND a.receive_basis in (0,1,2,4,9,10,11) AND c.trans_id!=0 AND $booking_without_order=0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $date_cond $challandCond ORDER BY a.id  DESC";
                //echo $sql;	sum(c.quantity) as quantity		
                // GROUP BY a.id,a.knitting_company, a.recv_number,a.challan_no, a.receive_date,a.remarks,d.cons_quantity 
	}
	else if($cbo_bill_type==2)
	{

    $sql="SELECT a.id,a.knitting_company, a.recv_number, a.challan_no, a.receive_date,a.remarks, e.cons_quantity as challan_qty
                FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d,inv_transaction e
                WHERE a.id=b.mst_id  AND a.id=e.mst_id AND b.id=c.dtls_id AND d.id=b.batch_id AND c.trans_type=1 AND c.entry_form IN (7,37,66,68) AND c.trans_id!=0 AND a.entry_form IN (7,37,66,68) AND a.knitting_source=3 AND a.company_id=$company_name $partyCond $year_cond AND a.item_category=2 
                AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0  
                ORDER BY a.id DESC";

                // GROUP BY a.id, a.knitting_company, a.recv_number, a.challan_no, a.receive_date ,a.remarks,e.cons_quantity

   /*   $sql= "SELECT a.id,a.challan_no, a.receive_date,sum(b.batch_issue_qty) as rec_qnty
        FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b WHERE a.id=b.mst_id and a.entry_form=92 AND a.company_id=$company_name $partyCond2 $year_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        and b.process_id not in(35)
        group by a.id, a.entry_form,a.challan_no, a.receive_date order by a.id DESC" ;*/
	}
	else 
	{
        $sql="";
		//$sql= "select group_concat(distinct(challan_no) order by challan_no) as challan_no from  trims_bill_issue_dtls where  challan_no between '$start_chln' and '$end_chln'";
	}
	//echo $sql;

	?>
    <div style="width:920px; margin: 0 auto;">
        <fieldset style="width:920px;">
            <table cellpadding="0" cellspacing="0" width="900">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="3" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="3" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
            </table>
            <table width="900" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="200">System No</th>
                    <th width="100">Pending Challan No</th>
                    <th width="100">Challan Date</th>
                    <th width="100">Challan Qty</th>
                    <th width="200">Party Name</th>
                    <th width="100">Remarks</th>
                </thead>
            </table>
            <div style="max-height:300px; overflow-y:scroll; width:920px" id="scroll_body" >
                <table width="900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <?
                    $sql_result=sql_select($sql); 
                    $i=1;
                    foreach ($sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                             <td width="50" align="center"><? echo $i; ?></td>
                             <td width="200" align="center"><? echo $row[csf('recv_number')]; ?></td>
                             <td width="100" align="center"><? echo $row[csf('challan_no')]; ?></td>
                             <td width="100" align="center"><? echo $row[csf('receive_date')]; ?></td>
                             <td width="100" align="center"><? echo $row[csf('challan_qty')]; ?></td>
                             <td width="200" align="center"><? echo $party_arr[$row[csf('knitting_company')]]; ?></td>
                             <td width="100" align="center"><? echo $row[csf('remarks')]; ?></td>
                        </tr>
                        <?	
                        $i++;
                    }
                    ?>
                </table>
            </div>
        </fieldset>
    </div>
    <?
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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