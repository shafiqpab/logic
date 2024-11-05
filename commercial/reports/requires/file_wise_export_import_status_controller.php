<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier where is_deleted=0  and status_active=1 order by supplier_name",'id','supplier_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}
if ($action=="load_drop_down_search")
{
	$data=explode('_',$data);
	if($data[1]==1) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	if($data[1]==2) echo create_drop_down( "txt_search_common", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	if($data[1]==3) echo create_drop_down( "txt_search_common", 170, "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lein Bank --", $selected, "",0,"" );
	exit();
}

if ($action=="file_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;  
?>
<script>
	function js_set_value(str)
	{
		$("#hide_file_id_no").val(str);
		parent.emailwindow.hide(); 
	}
	function set_caption(id)
	{
	if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
	if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Buyer Name';
	if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter Lein Bank';
	}
</script>
</head>
<body>
    <div style="width:530px">
    <form name="search_order_frm"  id="search_order_frm">
    <fieldset style="width:530px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th>Search By</th>
                <th id="search_by_td_up">Enter File No</th>
                <th> 
                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/> 
                <input type="hidden" name="txt_buyer_id" id="txt_buyer_id" value="<?  echo $buyer_id; ?>"/>
                <input type="hidden" name="txt_lien_bank_id" id="txt_lien_bank_id" value="<?  echo $lien_bank; ?>"/> 
                <input type="hidden" name="txt_selected_file" id="txt_selected_file" value=""/> 
                </th>
            </thead>
            <tbody>
            
                <tr class="general">
                    <td>
                    <?
					$sarch_by_arr=array(1=>"File No",2=>"Buyer",3=>"Lien Bank"); 
					echo create_drop_down( "cbo_search_by", 170,$sarch_by_arr,"", 1, "-- Select Search --", 1,"load_drop_down( 'file_wise_export_import_status_controller',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
					?>
                    </td>
                    <td align="center" id="search_by_td">
                    <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />
                    </td>
                    <td>
                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?  echo $company_id; ?>+'_'+<?  echo $buyer_id; ?>+'_'+<?  echo $lien_bank;?>,'search_file_info','search_div_file','file_wise_export_import_status_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="100%">
            <tr>
                <td>
                <div style="width:560px; margin-top:5px" id="search_div_file" align="left"></div>
                </td>
            </tr>
        </table>
    </fieldset>
    </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

	exit();
}

if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	$buyer_id = $ex_data[3];
	$lien_bank_id = $ex_data[4];
	//echo $cbo_search_by; die;
	if($txt_search_common==0)$txt_search_common="";
	if($db_type==0)
	{
		$id_group="group_concat(id) as id";
	}
	else
	{
		$id_group="listagg(cast(id as varchar(4000)),',') within group (order by id) as id";
	}
	if($txt_search_common!="" && $cbo_search_by==1)
	{
		
		$sql="select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and internal_file_no like '%$txt_search_common%' and status_active=1 and is_deleted=0 group by beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year
		union all
		select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%') and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' group by beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year  order by internal_file_no,lc_sc_year";
	}
	else if($txt_search_common!="" && $cbo_search_by==2)
	{
		
		$sql="select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and buyer_name like'%$txt_search_common%' and status_active=1 and is_deleted=0 group by beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year
		union all
		select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common') and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name like'%$txt_search_common%' group by beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year order by internal_file_no,lc_sc_year";
	}
	else if($txt_search_common!="" && $cbo_search_by==3)
	{
		//echo $txt_search_common; die;
		
		$sql="select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and lien_bank='$txt_search_common' and status_active=1 and is_deleted=0 group by beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year
		union all
		select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common') and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common' group by beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year order by internal_file_no,lc_sc_year";
	}
	else
	{
		$sql="select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year , 1 as type from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 group by beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year
		union all
		select $id_group, beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year , 2 as type from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 group by beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year order by internal_file_no, lc_sc_year ";
	}
	//echo $sql;die;
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</th>
                <th width="100">File NO</th>
                <th width="100">Year</th>
                <th width="140"> Buyer</th>
                <th> Lein Bank</th>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sll_result=sql_select($sql);
			$i=1;
			foreach($sll_result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
			?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("id")];?>,<? echo $row[csf("lc_sc_year")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="100"><? echo $row[csf("internal_file_no")];  ?></td>
                    <td align="center" width="100"><? echo $row[csf("lc_sc_year")];  ?></td>
                    <td width="140"><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></td>
                    <td><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_id_no" name="hide_file_id"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
    <?
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	 $cbo_based_on=str_replace("'","",$cbo_based_on);
	 $cbo_company_name=str_replace("'","",$cbo_company_name); $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); $cbo_lein_bank=str_replace("'","",$cbo_lein_bank); $txt_file_no=str_replace("'","",$txt_file_no);	 $file_id=str_replace("'","",$file_id);$lc_sc_year=str_replace("'","",$lc_sc_year);
	 //echo $lc_sc_year;die;

?>
<div style="width:1480px;" id="scroll_body">
<fieldset>
    <table width="900" cellpadding="0" cellspacing="0" id="caption" align="">
        <tr>
        	<td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
        </tr> 
        <tr>  
        	<td align="center" width="100%" colspan="9" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr> 
        <tr>
               <th width="100" align="left">File No:</th>
               <th colspan="8" width="1100" align="left"><? echo $txt_file_no; ?></th>
        </tr> 
    </table>
	<br />
    <table width="900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
        <thead>
            <tr>
            <th width="120">SC Number</th>
            <th width="120">LC Number</th>
            <th width="120">SC Value(Lc/Sc,Finance)</th>
            <th width="120">Rep.(LC,Sc)Value</th>
            <th width="120">Balance</th>
            <th width="120">SC Value(Direct)</th>
            <th >LC Value(Direct)</th>
            </tr>
        </thead>
        <tbody>
        
        <?
        if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
        if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
        if($cbo_lein_bank == 0) $cbo_lein_bank="%%"; else $cbo_lein_bank = $cbo_lein_bank;
        if(trim($txt_file_no)!="") $txt_file_no =$txt_file_no; else $txt_file_no="%%";
		if(trim($lc_sc_year)!="") $lc_sc_year =$lc_sc_year; else $lc_sc_year="%%";
        
        
        $sql="select id,internal_file_no,export_lc_no as lc_sc_no,'' as convertible_to_lc ,lc_value as lc_sc_value,replacement_lc,'' as converted_from,lien_bank,buyer_name,1 as type
        from  com_export_lc
        where  beneficiary_name='$cbo_company_name' and lien_bank like '$cbo_lein_bank' and buyer_name like '$cbo_buyer_name'  and internal_file_no='$txt_file_no' and lc_year like '$lc_sc_year' and status_active=1 and is_deleted=0
        
        UNION ALL
        
        select id,internal_file_no,contract_no as lc_sc_no,convertible_to_lc,contract_value as lc_sc_value,'' as replacement_lc,converted_from ,lien_bank,buyer_name,2 as type
        from com_sales_contract
        where  beneficiary_name='$cbo_company_name' and lien_bank like '$cbo_lein_bank' and buyer_name like '$cbo_buyer_name' and internal_file_no='$txt_file_no' and sc_year like '$lc_sc_year' and status_active=1 and is_deleted=0";
        
        //echo $sql;die;
        $sql_re=sql_select($sql);
		$lien_bank_lc="";$lien_bank_sales="";$buyer_lc="";$buyer_sales="";$export_lc_id=0;$sales_contract_id=0;$export_lc_sc_id=0;
		$i=1;
		//var_dump($sql_re);die;
        foreach($sql_re as $row_result)
        {
        
        
        if ($i%2==0)
        $bgcolor="#E9F3FF";
        else
        $bgcolor="#FFFFFF";
        
        ?>
        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            <td align="center"><p><? if($row_result['type'] == 2) echo $row_result[csf('lc_sc_no')];?>&nbsp;</p></td>
        	<td align="center"><p><? if($row_result['type'] == 1 ) echo  $row_result[csf('lc_sc_no')]; else if($row_result['type'] == 2 && $row_result['converted_from']!=0 && ($row_result['convertible_to_lc'] ==1 || $row_result['convertible_to_lc'] ==3) ) echo number_format( $row_result[csf('lc_sc_no')],2);?>&nbsp;</p></td>
            <td align="right"><p><? if($row_result['type'] == 2 && $row_result['converted_from'] ==0 && ($row_result['convertible_to_lc'] ==1 || $row_result['convertible_to_lc'] ==3)  ) {$sc_value_fn=$row_result[csf('lc_sc_value')]; echo number_format($sc_value_fn,2); $total_sc_fn +=$sc_value_fn;} ?>&nbsp;</p></td>
            <td align="right"><p><? if($row_result['replacement_lc'] == 1 && $row_result['type'] == 1 ) {$lc_value=$row_result[csf('lc_sc_value')]; echo number_format($lc_value ,2); $total_rep_lc +=$lc_value;} else if($row_result['type'] == 2 && $row_result['converted_from']!=0) {$lc_value=$row_result[csf('lc_sc_value')];echo number_format($lc_value ,2); $total_rep_lc +=$lc_value;} ?>&nbsp;</p></td>
            
            <td align="center"><p>&nbsp;</p></td>
            <td align="right" ><p><? if($row_result['type'] == 2 && $row_result['convertible_to_lc'] ==2 && $row_result['converted_from']==0){ $sc_value_direct=$row_result[csf('lc_sc_value')]; echo number_format($sc_value_direct ,2); $total_sc_value_direct += $sc_value_direct; }?>&nbsp;</p></td>
            <td align="right"><p><? if($row_result['replacement_lc'] == 2 && $row_result['type'] == 1 ){ $lc_value_direct=$row_result[csf('lc_sc_value')]; echo number_format($lc_value_direct,2); $total_lc_value_direct+=$lc_value_direct; } ?>&nbsp;</p></td>
        </tr>
        <?
		//$file_value+=$row_result[csf('lc_sc_value')];
		if($row_result['type'] == 1) $lien_bank_lc=$row_result['lien_bank'];
		if($row_result['type'] == 2) $lien_bank_sales=$row_result['lien_bank'];
		if($row_result['type'] == 1) $buyer_lc=$row_result['buyer_name'];
		if($row_result['type'] == 2) $buyer_sales=$row_result['buyer_name'];
		if($row_result['type'] == 1){ if($export_lc_id==0) $export_lc_id =$row_result['id']; else $export_lc_id=$export_lc_id.",".$row_result['id'];}
		if($row_result['type'] == 2){ if($sales_contract_id==0)$sales_contract_id=$row_result['id']; else $sales_contract_id=$sales_contract_id.",".$row_result['id'];}
		if($export_lc_sc_id==0) $export_lc_sc_id =$row_result['id']; else $export_lc_sc_id=$export_lc_sc_id.",".$row_result['id'];
		
		
		$i++;
        }
		//echo $export_lc_sc_id."#".$export_lc_id."*".$sales_contract_id;
		if($export_lc_sc_id=="")$export_lc_sc_id=0;
		if($export_lc_id=="")$export_lc_id=0;
		if($sales_contract_id=="")$sales_contract_id=0;
        ?>
        </tbody>
        <tfoot>
        <tr>


            <th align="right" colspan="2"> Total:</th>
            <th ><? echo number_format($total_sc_fn ,2); ?></th>
            <th align="right"><? echo number_format($total_rep_lc,2); ?></th>
            <th ><? $balance=$total_sc_fn-$total_rep_lc; echo  number_format($balance ,2); if($balance<0) $balance=0;  ?></th> 
            <th ><? echo number_format($total_sc_value_direct ,2); ?></th>
            <th ><? echo number_format($total_lc_value_direct ,2); $file_value=$total_rep_lc+$balance+$total_sc_value_direct+$total_lc_value_direct;?></th>
        </tr>
        </tfoot>
    </table>
    <br /><br>
    <table width="1350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
    	<thead>
        	<tr>
                <th width="120" rowspan="2">Lien Bank</th>
                <th width="120">Buyer</th>
                <th width="90" rowspan="2">File Value</th>
                <th width="90">BTB Value</th>
                <th width="90">Order Qty</th>
                <th width="90" rowspan="2">Shipped Value</th>
                <th width="80" rowspan="2">Shipped Status</th>
                <th width="90" rowspan="2">Sub. Bill Value</th>
                <th width="90">Nego. Value</th>
                <th width="90" rowspan="2">Realized Value</th>
                <th width="90">BTB Accp</th>
                <th width="80" rowspan="2">Local Chrg.</th>
                <th width="80" rowspan="2">Foreign Chrg.</th>
                <th rowspan="2">Variance With Bank</th>
            </tr>
            <tr>
                <th width="110">Agent</th>
                <th width="90">BTB %</th>
                <th width="80">Ship Qty</th>
                <th width="80">Nego. %</th>
                <th width="80">BTB Paid</th>
            </tr>
        </thead>
        <? /*echo "select a.id as btb_id, sum(a.lc_value) as lc_value, 1 as type  
		from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
		where  a.id=b.import_mst_id and b.is_lc_sc=0 and b.lc_sc_id in($export_lc_id) AND a.importer_id = '$cbo_company_name'
					union all
					select a.id as btb_id, sum(a.lc_value) as lc_value, 2 as type from com_btb_lc_master_details a, com_btb_export_lc_attachment b where  a.id=b.import_mst_id and b.is_lc_sc=1 and b.lc_sc_id in($sales_contract_id) AND a.importer_id = '$cbo_company_name' 
					group by a.id "; echo "<br><br>";
					echo "select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.supplier_id, a.item_category_id, a.lc_type_id , 1 as type
					from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
					where  a.id=b.import_mst_id and b.lc_sc_id in($export_lc_id) and b.is_lc_sc=0 AND a.importer_id = '$cbo_company_name'
			union all
			select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.supplier_id, a.item_category_id, a.lc_type_id, 2 as type 
			from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
			where  a.id=b.import_mst_id and b.lc_sc_id in($sales_contract_id) and b.is_lc_sc=1  AND a.importer_id = '$cbo_company_name'"*/
					
					?>
        <tr bgcolor="<? echo "#FFFFFF"; ?>">
        	<td width="120" rowspan="2">
				<? 
					if($lien_bank_lc!="") echo $lein_bank_arr[$lien_bank_lc]; else echo $lein_bank_arr[$lien_bank_sales];
				?>
            </td>
            <td width="120">
				<? 
					if($buyer_lc!="") echo $buyer_name_arr[$buyer_lc]; else echo $buyer_name_arr[$buyer_sales];
				?>
            </td>
            <td width="90" rowspan="2" align="right">
            	<? echo number_format($file_value ,2); ?>
            </td>
            <td width="90" align="right">
            	<? 
					$btb_id="";$btb_value=0;
					if($export_lc_sc_id=="")$export_lc_sc_id=0;
					$btb_sql = sql_select("select a.id as btb_id, sum(a.lc_value) as lc_value from com_btb_lc_master_details a, com_btb_export_lc_attachment b where  a.id=b.import_mst_id and b.is_lc_sc=0 and b.lc_sc_id in($export_lc_id) AND a.importer_id = '$cbo_company_name'
					union all
					select a.id as btb_id, sum(a.lc_value) as lc_value from com_btb_lc_master_details a, com_btb_export_lc_attachment b where  a.id=b.import_mst_id and b.is_lc_sc=1 and b.lc_sc_id in($sales_contract_id) AND a.importer_id = '$cbo_company_name' 
					group by a.id ");
				
					foreach($btb_sql as $row_btb_value)
					{
						if($btb_id=="") $btb_id= $row_btb_value['btb_id']; else  $btb_id=$btb_id.",".$row_btb_value['btb_id'];
						$btb_value+=$row_btb_value['lc_value'];
						
					}
				//echo $btb_id;
					echo number_format($btb_value,2);
		     	?> 
            </td>
            
            <td width="90" align="right">
			<?
			$order_qty = return_field_value("sum(attached_qnty) as attached_qnty"," com_export_lc_order_info ","com_export_lc_id in($export_lc_id) and status_active=1","attached_qnty");
			echo number_format($order_qty,2,".","");
			?>
            </td>
            <td width="90" rowspan="2" align="right">
			<?
				$sql_lc=sql_select("SELECT a.id as id, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value
				from com_export_invoice_ship_mst a
				where a.is_lc=1 and a.lc_sc_id in($export_lc_id) and a.benificiary_id='$cbo_company_name' and a.status_active=1 
				union all
				SELECT a.id as id, a.invoice_quantity as current_invoice_qnty, a.invoice_value as current_invoice_value
				from com_export_invoice_ship_mst a
				where a.is_lc=2 and a.lc_sc_id in($sales_contract_id) and a.benificiary_id='$cbo_company_name' and a.status_active=1 
				group by a.id");
			 $total_shipment_qnty=0;$total_shipment_val=0;$shp_inv_id="";
			 foreach($sql_lc as $row_lc_result)
			 {
				 if($shp_inv_id=="") $shp_inv_id=$row_lc_result[csf("id")]; else $shp_inv_id=$shp_inv_id.",".$row_lc_result[csf("id")];
				$total_shipment_qnty += $row_lc_result[csf("current_invoice_qnty")];
				$total_shipment_val += $row_lc_result[csf("current_invoice_value")];
			 }
			 
			 echo number_format($total_shipment_val,2);
			 if($shp_inv_id=="")$shp_inv_id=0;
			 //echo $shp_inv_id;
			?>
            </td>
            <td width="80" rowspan="2" align="center">
			
            </td>
           
            <td width="90" rowspan="2" align="right">
			<?
			$sub_lc_id=0;$sub_lc_val=0;
			$sub_lc_sql=sql_select("SELECT b.id as id, sum(a.net_invo_value) as net_invo_value from com_export_doc_submission_invo a,  com_export_doc_submission_mst b where b.id=a.doc_submission_mst_id and a.invoice_id in($shp_inv_id) and b.company_id='$cbo_company_name' group by b.id");
			foreach($sub_lc_sql as $row_sub_lc)
			{
				if($sub_lc_id==0) $sub_lc_id=$row_sub_lc['id']; else $sub_lc_id=$sub_lc_id.",".$row_sub_lc['id'];
				$sub_lc_val=$sub_lc_val+$row_sub_lc['net_invo_value'];
			}
			
			echo number_format($sub_lc_val,2);
			//echo $sub_lc_id;
			?> </td>
            
            <td width="90" align="right">
			<?
			if($sub_lc_id=="") $sub_lc_id=0;
			$nago_value = return_field_value("sum(lc_sc_curr) as lc_sc_curr"," com_export_doc_sub_trans ","doc_submission_mst_id in($sub_lc_id)","lc_sc_curr");
			 echo number_format($nago_value,2); 
			?></td>
            <td width="90" rowspan="2" align="right">
			<?
            	$realization_value = return_field_value("sum(b.document_currency) as document_currency","com_export_proceed_realization a, com_export_proceed_rlzn_dtls b"," a.id=b.mst_id AND a.is_invoice_bill=1 and  b.type=1 and a.invoice_bill_id in($sub_lc_id)","document_currency");
            	echo number_format($realization_value,2); 
            ?>
            </td>
            <td width="90" align="right">
			<?
				
				$btb_amount_lc = return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls ","btb_lc_id in($export_lc_id) and is_lc=1 and status_active=1","current_acceptance_value");
			$btb_amount_sc = return_field_value("sum(current_acceptance_value) as current_acceptance_value","com_import_invoice_dtls ","btb_lc_id in($sales_contract_id) and is_lc=2 and status_active=1","current_acceptance_value");
			echo number_format($btb_amount_lc+$btb_amount_sc,2,".",""); 
            ?>
            </td>
            <td width="80" rowspan="2">&nbsp;</td>
            <td width="80" rowspan="2">&nbsp;</td>
            <td rowspan="2" align="right"><? echo number_format($tot_variance_bank_val,2); ?></td>
        </tr>
        <tr bgcolor="<? echo "#FFFFFF"; ?>">
            <td width="110">&nbsp;</td>
            <td width="90" align="right">
			<? 
					$btb_value_percent=0; 
					$btb_value_percent=($btb_value*100)/$file_value; 
               	    echo number_format($btb_value_percent,2)."%"; 
             ?>	
            </td>
            <td width="90" align="right">
			<?
			 echo number_format($total_shipment_qnty,2,".","");
			 ?></td>
            <td width="80" align="right">
            <?
			$nago_parcentage=($nago_value*100)/$sub_lc_val;
			 echo number_format($nago_parcentage,2)."%";
			 ?>
            </td>
            <td width="80" align="right">
            <?
			
			if($btb_id=="") $btb_id=0;
			$btb_accp_value = return_field_value("sum(document_value) as document_value"," com_import_invoice_mst "," id in($btb_id) and status_active=1 and is_deleted=0","document_value");
			echo number_format($btb_accp_value,2);
			?>	
            </td>
        </tr>
	</table>
     <table width="1470" cellpadding="0" cellspacing="0" align="left">
    	<tr>
        	<td width="" valign="top">&nbsp;
            </td>
        </tr>
        <tr>
        	<td width="" valign="top">&nbsp;
            </td>
        </tr>
     </table>
    <table width="1470" cellpadding="0" cellspacing="0" align="left">
    	<tr>
        	<td width="750" valign="top">
       <div style="width:750px">
        <table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
            <thead>
            	<th width="35">SL</th>
                <th width="100">BBLC No.</th>
                <th width="75">LC Date</th>
                <th width="105">LC Value</th>
                <th width="110">Supplier name</th>
                <th width="110">Item Category</th>
                <th width="80">Yarn Qnty</th>
                <th >LC Type</th>
            </thead>
    	</table>
    </div>
    <div style="width:750px; overflow-y:scroll; overflow-x:hidden; max-height:250px" id="btb_import_list_view" align="left">
    	<table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
        <?
			$sql_re=sql_select("select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.supplier_id, a.item_category_id, a.lc_type_id from com_btb_lc_master_details a, com_btb_export_lc_attachment b where  a.id=b.import_mst_id and b.lc_sc_id in($export_lc_id) and b.is_lc_sc=0 AND a.importer_id = '$cbo_company_name'
			union all
			select a.id as btb_id, a.lc_number, a.lc_date, a.lc_value, a.supplier_id, a.item_category_id, a.lc_type_id from com_btb_lc_master_details a, com_btb_export_lc_attachment b where  a.id=b.import_mst_id and b.lc_sc_id in($sales_contract_id) and b.is_lc_sc=1  AND a.importer_id = '$cbo_company_name'");
			$k=1;$i=1;
			foreach($sql_re as $row)
			{	
		?>
          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3rd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3rd<? echo $i; ?>">
                	<td width="35"><? echo $k; ?></td>
                    <td width="100"><p><? echo $row[csf("lc_number")]; ?></p></td>
                    <td width="75" align="center"><? echo $row[csf("lc_date")]; ?></td>
                    <td width="105" align="right"><? $lc_val=$row[csf("lc_value")]; echo number_format($lc_val,2,".",""); $total_lc_val+=$lc_val; ?></td>
                    <td width="110"><? echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
                    <td width="110"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td width="80" align="right">
					<?
						$btb_mst_id=$row[csf("btb_id")];
						if($row[csf("item_category_id")]==1)
						{
							 $po_qty= return_field_value("sum(b.quantity) as quantity","com_btb_lc_pi a, com_pi_item_details b","a.pi_id=b.pi_id and a.com_btb_lc_master_details_id='$btb_mst_id'","quantity");
							 echo number_format($po_qty,0,"","");
						}
					?></td>
                    <td><? echo $lc_type[$row[csf("lc_type_id")]];?></td>
                </tr>
            <?
			$k++;$i++;
			}
			
		?>
        	<tfoot>
            	<th colspan="3">Total</th>
                <th><? echo number_format($total_lc_val,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table> 
    </div>
    </td>
    <td width="30" valign="top">&nbsp;</td>
    <td valign="top"><div style="width:680px">
        <table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
            <thead>
            	<th width="35">SL</th>
                <th width="120">Export Bill No.</th>
                <th width="75">Bill Date</th>
                <th width="85">Inv/Bill Qty/Pcs</th>
                <th width="110">Bill Value</th>
                <th width="140">Invoice No.</th>
                <th>Ship Date</th>
            </thead>
    	</table>
    </div>
    <div style="width:680px; overflow-y:scroll; overflow-x:hidden; max-height:250px" id="btb_export_list_view" align="left">
    	<table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
        
                    <?
					
				$sql_re=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, group_concat(distinct c.invoice_no) as invoice_no, sum(c.invoice_quantity) as net_inv_qty
 FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c
WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='1'AND b.lc_sc_id in($export_lc_id) and a.company_id='$cbo_company_name' AND a.status_active='1' group by a.id");
 				$i=1;$total_net_lc_value=0;$total_order_qty=0;$m=0;$invoice_id=0;$lc_num_check=array();$sc_num_check=array();
				foreach($sql_re as $row)
				{
					$lc_contract_id_arr=explode(",",$export_lc_id);
					$lc_number= return_field_value("export_lc_no"," com_export_lc ","id in($export_lc_id) and status_active=1","export_lc_no");
					for($q=0;$q<count($lc_contract_id_arr);$q++)
					{
						if(!in_array($lc_contract_id_arr[$q],$lc_num_check))
						{
							$lc_num_check[]=$lc_contract_id_arr[$q];
							?>
                            <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                <td colspan="7" style="background-color:#FDF4EF"><b><? echo "Export L/C No."." - ".$lc_number; ?></b></td>
                            </tr>
                            <?
						}
					}
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_4th<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_4th<? echo $i; ?>">
					   <td width="35"><? echo $i;?>&nbsp;</td>
					   <td width="120"><? echo $row[csf("bank_ref_no")]; ?>&nbsp;</td>
					   <td width="75" align="center"><? echo $row[csf("bank_ref_date")];?>&nbsp;</td>
					   <td width="85" align="right">
						<?
						$inv_qty= $row[csf("net_inv_qty")];
						echo $inv_qty;$total_order_qty+=$inv_qty
						?>&nbsp;
						</td>
						<td width="110" align="right">
						<?
						 $net_lc_value=$row[csf("net_invo_value")]; echo number_format($net_lc_value,2,".",""); $total_net_lc_value+=$net_lc_value;
						?>&nbsp;
					   </td>
					   <td width="140" align="center">
						<? 
						 $inv_number=$row[csf("invoice_no")]; echo  $inv_number;
						 ?>&nbsp;
						</td>
						<td align="center">
						<?
							
						?>
						</td>
					</tr>
					 <? 
					 $i++;$m++;
				}
					
				$sql_re=sql_select("SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, b.net_invo_value as net_invo_value, a.bank_ref_no, a.bank_ref_date, group_concat(distinct c.invoice_no) as invoice_no, sum(c.invoice_quantity) as net_inv_qty
 FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b , com_export_invoice_ship_mst c
WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc='2'AND b.lc_sc_id in($sales_contract_id) and a.company_id='$cbo_company_name' AND a.status_active='1' group by a.id ");

 				$i=1;$m=0;$id=0;$invoice_id_sc=0;
				//echo $sales_contract_id_arr[0]; die;
				foreach($sql_re as $row)
				{
					$sales_contract_id_arr=explode(",",$sales_contract_id);
					$sc_number= return_field_value("contract_no","com_sales_contract ","id in($sales_contract_id) and status_active=1","contract_no");
					for($p=0;$p<count($sales_contract_id_arr);$p++)
					{
						if(!in_array($sales_contract_id_arr[$p],$sc_num_check))
						{
							$sc_num_check[]=$sales_contract_id_arr[$p];
							?>
                            <tr bgcolor="<? echo "#FFFFFF"; ?>">
                            	<td colspan="7" style="background-color:#FDF4EF"><b><? echo "Sales Contact Reference."." - ".$sc_number; ?></b></td>
                            </tr>

                            <?
						}
					}
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_4th<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_4th<? echo $i; ?>">
					   <td width="35"><? echo $i;?>&nbsp;</td>
					   <td width="120"><? echo $row[csf("bank_ref_no")]; ?>&nbsp;</td>
					   <td width="75" align="center"><? echo $row[csf("bank_ref_date")];?>&nbsp;</td>
					   <td width="85" align="right">
						<?
						$id=$row["inv_id"];
						$inv_qty_sc= $row[csf("net_inv_qty")];
						echo $inv_qty_sc;$total_order_qty+= $inv_qty_sc;
						?>&nbsp;
						</td>
						<td width="110" align="right">
						<?
						 $net_sc_value=$row[csf("net_invo_value")]; echo number_format($net_sc_value,2,".",""); $total_net_sc_value+=$net_sc_value;
						?>&nbsp;
					   </td>
					   <td width="140" align="center">
						<? 
						echo $inv_number= $row[csf("invoice_no")];
						?>&nbsp;
						</td>
						<td align="center">
						<?
							
						?>
						</td>
					</tr>
					 <? 
				 $i++;$m++;
				}
			?>
            <tfoot>
            	<th colspan="3">Total</th>
                <th><? echo $total_order_qty; ?></th>
                <th><? $total_net_ls_sc_value=$total_net_lc_value+$total_net_sc_value; echo number_format( $total_net_ls_sc_value,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
    </div>
    </td>
</tr>
</table>
<div style="float:right">
	<b>Un-Submited Invoice</b>
    <div style="width:680px">
        <table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="160">Invoice No</th>
                <th width="80">Invoice Date</th>
                <th width="85">Invoice Qnty</th>
                <th width="110">Invoice Value</th>
                <th>LC/SC No</th>
            </thead>
        </table>
    </div>
    <div style="width:680px; overflow-y:scroll; overflow-x:hidden; max-height:250px" id="unsubmit_list_view">
    	<table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
			 <?php 
			 if($invoice_id=="") $invoice_id=0; if($invoice_id_sc=="") $invoice_id_sc=0;
			 
               $sql_re=sql_select("SELECT a.is_lc,a.lc_sc_id, a.invoice_no,a.invoice_date,SUM(a.invoice_quantity) as inv_qnty ,SUM(a.net_invo_value) as inv_value 
			   FROM com_export_invoice_ship_mst a 
			   WHERE a.id NOT IN ($invoice_id) AND a.lc_sc_id IN ($export_lc_id) and a.is_lc=1 and  a.benificiary_id='$cbo_company_name' and a.is_deleted='0' and a.status_active='1'
			   union all
			   SELECT a.is_lc,a.lc_sc_id, a.invoice_no,a.invoice_date,SUM(a.invoice_quantity) as inv_qnty ,SUM(a.net_invo_value) as inv_value 
			   FROM com_export_invoice_ship_mst a 
			   WHERE a.id NOT IN ($invoice_id_sc) AND a.lc_sc_id IN ($sales_contract_id) and a.is_lc=2 and  a.benificiary_id='$cbo_company_name' and a.is_deleted='0' and a.status_active='1'");
				$inv_unsubmited=mysql_query($inv_unsubmited_sql);
                $lc_sc='';
				foreach( $sql_re as $row)
				{
					if($row[csf("lc_sc_id")]!=0)
					{
				?>
					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td width="160">&nbsp;<? echo $row[csf("invoice_no")]; ?></td>
						<td width="80">&nbsp;<?  echo $row[csf("invoice_date")]; ?></td>
						<td width="85" align="right">&nbsp;<? echo number_format($row[csf("inv_qnty")],0); ?></td>
						<td width="110" align="right"><?  echo number_format($row[csf("inv_value")],2); ?></td> 
						<td>&nbsp;<? echo $row[csf("lc_sc_id")]; ?></td>
					</tr>
				<?
				
					$total_unsubmited_invoice_value += $row[csf("inv_value")];
					$total_unsubmited_invoice_qnty += $row[csf("inv_qnty")];
					}
					}
				 ?>
              <tfoot>
                <th colspan="2">Total</th>
                <th>&nbsp;<? echo $total_unsubmited_invoice_qnty; ?></th>
                <th><? echo number_format($total_unsubmited_invoice_value,2); ?></th>
                <th>&nbsp;</th>
              </tfoot>
         </table>
     </div>  
        <table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
         	<tfoot>
                <th width="232" colspan="2" align="right">Grand Total</th>
                <th width="85" align="right">&nbsp;<? echo $total_unsubmited_invoice_qnty+$total_order_qty; ?></th>
                <th width="110" align="right"><? echo number_format(($total_net_ls_sc_value+$total_unsubmited_invoice_value),2); ?></th>
                <th width="238">&nbsp;</th>
            </tfoot>
        </table>
</div>
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
disconnect($con);
?>
