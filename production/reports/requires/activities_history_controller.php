<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
//$menu_library=return_library_array( "select m_menu_id, menu_name from  main_menu", "m_menu_id", "menu_name"  );
//$query_type=array(0=>"New Insert",1=>"Update/Edit",2=>"Delete");

if ($action=="report_generate_login_history")  // Item Description wise Search
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$buyer_library=return_library_array( "select id, buyer_name from  lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$company_library=return_library_array( "select id, company_name from  lib_company", "id", "company_name"  );

	//echo $user_name;die;
	ob_start();	
	?>
      <div style="width:1490px;">
   	  <table width="1490" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1490" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="120">receive_date</th>
            <th width="120">buyer_name</th>
            <th width="120">recv_number</th>
            <th width="120">booking_no</th>
            <th width="120">receive_basis</th>
            <th width="120">barcode_no</th>
            <th width="120">roll_no</th>
            <th width="120">qc_pass_qty</th>
            <th width="120">product_name_details</th>
            <th width="120">job_no_mst</th>
            <th width="120">po_number</th>
            <th>supplier_name</th>
        </thead>
     </table>
    <div style="width:1490px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1470" class="rpt_table" id="table_body" >
    <? 
	/* $user_data="SELECT a.receive_date, f.buyer_name, a.recv_number, a.booking_no, a.receive_basis, c.barcode_no, c.roll_no, sum(c.qnty) as qc_pass_qty, d.product_name_details, e.job_no_mst, e.po_number, g.supplier_name
FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d, wo_po_break_down e, lib_buyer f, lib_supplier g
WHERE a.receive_date between '$date_from' and '$date_to' and a.buyer_id=f.id  and a.knitting_company=g.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.entry_form=1 and b.prod_id=d.id and c.po_breakdown_id=e.id and d.item_category_id=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
group by a.receive_date, f.buyer_name, a.recv_number, a.booking_no, a.receive_basis, c.barcode_no, c.roll_no, d.product_name_details, e.job_no_mst, e.po_number, g.supplier_name";*/ 

	 $sql_result="select id, job_no_mst, po_number from wo_po_break_down where status_active=1 and is_deleted=0";
	 $nameResult=sql_select( $sql_result );
	 foreach($nameResult as $row)
	 {
		 $po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
		 $po_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
	 }
	 unset($nameResult);
	 //$prod_sql=sql_select("Select id, ");
	$prod_arr=return_library_array( "select id, item_description from  product_details_master where item_category_id=13", "id", "item_description"  );
	$user_data="SELECT a.receive_date,a.recv_number,a.knitting_source,a.buyer_id as buyer_name,a.knitting_company as supplier_id, a.booking_no, a.receive_basis, b.prod_id, c.barcode_no, c.roll_no, sum(c.qnty) as qc_pass_qty, c.po_breakdown_id as po_id
FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
WHERE a.receive_date between '$date_from' and '$date_to' and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.entry_form=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
group by a.receive_date, a.buyer_id, a.recv_number, a.knitting_source,a.booking_no, a.receive_basis, b.prod_id, c.barcode_no, c.roll_no, a.knitting_company, c.po_breakdown_id";//die;
	$nameArray=sql_select( $user_data );
	$i=1; //$log_status_arr=array( 0=>"success", 1=>"pc ip fail", 2=>"password" , 3=>"user", 4=>"proxy");
	foreach($nameArray as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$knitting_source=$row[csf('knitting_source')];
		if($knitting_source==1)
		{
			$knit_company=$company_library[$row[csf('supplier_id')]];
		}
		else
		{
			$knit_company=$supplier_library[$row[csf('supplier_id')]];
		}
		$po_no=$po_arr[$row[csf('po_id')]]['po'];
		$job_no=$po_arr[$row[csf('po_id')]]['job'];
		?>
        <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
          <td width="30"><? echo $i;?></td>
           <td width="120" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
           <td width="120"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?> </p> </td>
           <td width="120"><p><? echo $row[csf('recv_number')];?> </p></td>
           <td width="120"><p><? echo $row[csf('booking_no')];?> </p></td>
           <td width="120"><p><? echo $row[csf('receive_basis')];?> </p></td>
           <td width="120"><p><? echo $row[csf('barcode_no')];?> </p></td>
           <td width="120"><p><? echo $row[csf('roll_no')];?> </p></td>
           <td width="120" align="right"><? echo number_format($row[csf('qc_pass_qty')]); ?></td>
           <td width="120" style="word-wrap:break-word"><? echo $prod_arr[$row[csf('prod_id')]];?></td>
           <td width="120"><p><? echo $job_no;?> </p></td>
           <td width="120"><p><? echo $po_no;?> </p></td>
           <td><p><? echo $knit_company;?> </p></td>
           
        </tr>
        <?
		$i++;
		}
		?>
    </table>
    </div>
	<?
	unset($nameArray);
	/*$html = ob_get_contents();
	foreach (glob("tmp_report_file/"."*.xls") as $filename) 
	{			
       @unlink($filename);
	}
	
 	$name=time(); 
	$filename="$name".".xls";
	$create_new_doc = fopen('tmp_report_file/'.$filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$name.".xls";
	echo "$total_data####$filename";*/
	exit();
}
?>