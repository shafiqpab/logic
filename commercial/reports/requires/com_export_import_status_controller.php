<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];




if ($action=="file_popup")
{
	
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
?>
<script>
	/*function js_set_value(str)
	{
		$("#hide_file_no").val(str);
		parent.emailwindow.hide(); 
	}*/
	function set_caption(id)
	{
		if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
		if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Buyer Name';
		if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter Lein Bank';
	}
	
	var selected_id = new Array;
	var selected_no = new Array;
	
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) { 
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value( strCon ) 
	{
		//alert(strCon);
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			//alert(str);
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );
			
			if( jQuery.inArray( str, selected_no ) == -1 ) 
			{
				selected_id.push( selectID );
				selected_no.push( str );				
			}
			else 
			{
				for( var i = 0; i < selected_no.length; i++ ) 
				{
					if( selected_no[i] == str ) break;
				}
				selected_id.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			
			var id =num='';
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num); 
			$('#hide_file_no').val( id );
			$('#file_serial_no').val( num );
	}
	function fnc_close()
	{
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
    <div style="width:530px">
    <form name="search_order_frm"  id="search_order_frm">
    <fieldset style="width:530px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
            	<th>Year</th>
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
					$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$company_id' and status_active=1 and is_deleted=0");
					foreach($sql as $row)
					{
						$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
					}
					echo create_drop_down( "cbo_year", 100,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
					?>
                    </td>
                    <td>
                    <?
					$sarch_by_arr=array(1=>"File No",2=>"Buyer",3=>"Lien Bank"); 
					echo create_drop_down( "cbo_search_by", 130,$sarch_by_arr,"", 1, "-- Select Search --", 1,"load_drop_down( 'file_wise_export_import_status_controller',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
					?>
                    </td>
                    <td align="center" id="search_by_td">
                    <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />
                    </td>
                    <td>
                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?  echo $company_id; ?>+'_'+<?  echo $buyer_id; ?>+'_'+<?  echo $lien_bank;?>+'_'+document.getElementById('cbo_year').value,'search_file_info','search_div_file','com_export_import_status_controller','setFilterGrid(\'list_view\',-1)');" class="formbutton" style="width:100px;" value="Show" />
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
	$cbo_year = $ex_data[5];
	//echo $cbo_year; die;
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
	if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	if($cbo_year!=0)
	{ 
		$year_cond_sc="and sc_year='$cbo_year'"; 
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else  
	{
		$year_cond_sc=""; 
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'"; 
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	if($txt_search_common==0)$txt_search_common="";
	
	if($txt_search_common!="" && $cbo_search_by==1)
	{
		
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and internal_file_no like '%$txt_search_common%' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and internal_file_no like '%$txt_search_common%' $buy_query $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	else if($txt_search_common!="" && $cbo_search_by==2)
	{
		
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and buyer_name='$txt_search_common' and status_active=1 and is_deleted=0 $lien_bank_id  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common' $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and buyer_name='$txt_search_common' $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	else if($txt_search_common!="" && $cbo_search_by==3)
	{
		//echo $txt_search_common; die;
		
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and lien_bank='$txt_search_common' and status_active=1 and is_deleted=0 $buy_query  $year_cond_lc  group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common'  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 and lien_bank='$txt_search_common' $buy_query $year_cond_sc  group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	else
	{
		$sql="select beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id  $year_cond_lc group by internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year from com_sales_contract where internal_file_no not in(select internal_file_no from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  $year_cond_lc) and beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc group by internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank";
	}
	//echo $sql;
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
         <input type="hidden" id="hide_file_no" name="hide_file_no"  />
         <input type="hidden" id="file_serial_no" name="file_serial_no"  />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="540">
            <thead>
                <th width="60">Sl NO.</th>
                <th width="100">File NO</th>
                <th width="100">Year</th>
                <th width="140"> Buyer</th>
                <th> Lein Bank</th>
            </thead>
        </table>
        <div id="scroll_body" style="width:560px; overflow:scroll; max-height:280px;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="540" id="list_view">
            <tbody>
            <?
            $sll_result=sql_select($sql);
            $i=1;
            foreach($sll_result as $row)
            {
                if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                //echo $row[csf("internal_file_no")];die;
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $i."_".$row[csf("internal_file_no")];?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                    <td align="center" width="60"><p> <? echo $i;?>&nbsp;</p></td>
                    <td align="center" width="100"><p><? echo $row[csf("internal_file_no")];  ?>&nbsp;</p></td>
                    <td align="center" width="100"><p><? echo $row[csf("lc_sc_year")];  ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?>&nbsp;</p></td>
                    <td><p><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>
        </table>
        </div>
        <table width="520" id="table_id">
             <tr>
                <td align="center" >
                    <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                </td>
            </tr>
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
        
        <?
}




if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}
	

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$hide_year=str_replace("'","",$hide_year);
	$str_cond="";
	if($cbo_buyer_name!=0)  $str_cond=" and buyer_name=$cbo_buyer_name";
	if($cbo_lein_bank!=0) $str_cond.=" and lien_bank=$cbo_lein_bank";
	if($txt_file_no!="") $str_cond.=" and internal_file_no in($txt_file_no)";
	
	$sql_file=sql_select("select id, internal_file_no, lien_bank as lien_bank, replacement_lc, lc_value as lc_sc_value, 1 as type from com_export_lc  where beneficiary_name='$cbo_company_name'  and lc_year='$hide_year' and status_active=1 and is_deleted=0 $str_cond  
	union all
		select id, internal_file_no, lien_bank as lien_bank, 2 as replacement_lc, contract_value as lc_sc_value, 2 as type from com_sales_contract   where beneficiary_name='$cbo_company_name' and sc_year='$hide_year' and status_active=1 and is_deleted=0 $str_cond");
	
	$file_wise_data=array();$file_lc_id=$file_sc_id="";
	foreach($sql_file as $row)
	{
		if($row[csf("type")]==1) $file_lc_id.=$row[csf("id")].","; else $file_sc_id.=$row[csf("id")].",";
		$file_wise_data[$row[csf("internal_file_no")]]["internal_file_no"]=$row[csf("internal_file_no")];
		$file_wise_data[$row[csf("internal_file_no")]]["lien_bank"]=$row[csf("lien_bank")];
		if($row[csf("replacement_lc")]==2) $file_wise_data[$row[csf("internal_file_no")]]["lc_sc_value"]+=$row[csf("lc_sc_value")];
	}
	$file_lc_id=chop($file_lc_id," , ");$file_sc_id=chop($file_sc_id," , ");
	if($file_lc_id=="") $file_lc_id=0;if($file_sc_id=="") $file_sc_id=0;
	
	$sql_invoice=sql_select("select a.internal_file_no, b.id, b.commission, b.invoice_value, b.net_invo_value, 1 as type from com_export_lc a, com_export_invoice_ship_mst b  where  a.id=b.lc_sc_id and b.is_lc=1 and b.lc_sc_id in($file_lc_id) and b.status_active=1 and b.is_deleted=0  
	union all
		select a.internal_file_no, b.id, b.commission, b.invoice_value, b.net_invo_value, 2 as type from com_sales_contract a, com_export_invoice_ship_mst b where  a.id=b.lc_sc_id and b.is_lc=2 and b.lc_sc_id in($file_sc_id) and b.status_active=1 and b.is_deleted=0 ");
	$lc_invoice_id=$sc_invoice_id="";
	foreach($sql_invoice as $row)
	{
		if($row[csf("type")]==1) $lc_invoice_id.=$row[csf("id")].","; else $sc_invoice_id.=$row[csf("id")].",";
		$file_wise_data[$row[csf("internal_file_no")]]["commission"]+=$row[csf("commission")];
		$file_wise_data[$row[csf("internal_file_no")]]["invoice_value"]+=$row[csf("invoice_value")];
		$file_wise_data[$row[csf("internal_file_no")]]["net_invo_value"]+=$row[csf("net_invo_value")];
	}
	$lc_invoice_id=chop($lc_invoice_id," , ");$sc_invoice_id=chop($sc_invoice_id," , ");
	if($lc_invoice_id=="") $lc_invoice_id=0;if($sc_invoice_id=="") $sc_invoice_id=0;
	$sql_btb=sql_select("select a.internal_file_no, b.import_mst_id, b.current_distribution, 1 as type from com_export_lc a, com_btb_export_lc_attachment b  where  a.id=b.lc_sc_id and b.is_lc_sc=0 and b.lc_sc_id in($file_lc_id) and b.status_active=1 and b.is_deleted=0  
	union all
		select a.internal_file_no, b.import_mst_id, b.current_distribution, 2 as type from com_sales_contract a, com_btb_export_lc_attachment b  where  a.id=b.lc_sc_id and b.is_lc_sc=1 and b.lc_sc_id in($file_sc_id) and b.status_active=1 and b.is_deleted=0 ");
	$lc_btb_id=$sc_btb_id="";
	foreach($sql_btb as $row)
	{
		if($row[csf("type")]==1) $lc_btb_id.=$row[csf("import_mst_id")].","; else $sc_btb_id.=$row[csf("import_mst_id")].",";
		$file_wise_data[$row[csf("internal_file_no")]]["btb_amt"]+=$row[csf("current_distribution")];
	}
	$lc_btb_id=chop($lc_btb_id," , ");$sc_btb_id=chop($sc_btb_id," , ");
	if($lc_btb_id=="") $lc_btb_id=0;if($sc_btb_id=="") $sc_btb_id=0;
	
	/*echo "select a.internal_file_no, c.invoice_id, d.id as submission_id, (case when d.submit_type=1 then c.net_invo_value else 0 end) as sub_collection, (case when d.submit_type=2 then c.net_invo_value else 0 end) as sub_purchase, (case when d.submit_type=2 then d.total_negotiated_amount else 0 end) as sub_negotiate, 1 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and c.is_lc=1 and c.lc_sc_id in($file_lc_id) and c.invoice_id in($lc_invoice_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=40 
	union all
		select a.internal_file_no, c.invoice_id, d.id as submission_id, (case when d.submit_type=1 then c.net_invo_value else 0 end) as sub_collection, (case when d.submit_type=2 then c.net_invo_value else 0 end) as sub_purchase, (case when d.submit_type=2 then d.total_negotiated_amount else 0 end) as sub_negotiate, 2 as type from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and c.is_lc=2 and c.lc_sc_id in($file_sc_id) and c.invoice_id in($sc_invoice_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=40";*/
		
	//***************submission id retrive for realize query*************	
	$sql_submission=sql_select("select a.internal_file_no, c.invoice_id, d.id as submission_id, 1 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and c.is_lc=1 and c.lc_sc_id in($file_lc_id) and c.invoice_id in($lc_invoice_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=40 
	union all
		select a.internal_file_no, c.invoice_id, d.id as submission_id, 2 as type from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and c.is_lc=2 and c.lc_sc_id in($file_sc_id) and c.invoice_id in($sc_invoice_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=40");
	$lc_submission_id=$sc_submission_id=$lc_submit_invoice_id=$sc_submit_invoice_id="";
	foreach($sql_submission as $row)
	{
		if($row[csf("type")]==1) $lc_submission_id.=$row[csf("submission_id")].","; else $sc_submission_id.=$row[csf("submission_id")].",";//submission id retrive for realize check
		if($row[csf("type")]==1) $lc_submit_invoice_id.=$row[csf("invoice_id")].","; else $sc_submit_invoice_id.=$row[csf("invoice_id")].",";//submission id retrive for realize check
	}
	$lc_submission_id=chop($lc_submission_id," , ");$sc_submission_id=chop($sc_submission_id," , ");
	if($lc_submission_id=="") $lc_submission_id=0;if($sc_submission_id=="") $sc_submission_id=0;
	
	$lc_submit_invoice_id=chop($lc_submit_invoice_id," , ");$sc_submit_invoice_id=chop($sc_submit_invoice_id," , ");
	if($lc_submit_invoice_id=="") $lc_submit_invoice_id=0;if($sc_submit_invoice_id=="") $sc_submit_invoice_id=0;
	
	/*echo "select a.internal_file_no, b.id, b.commission, b.invoice_value, b.net_invo_value, 1 as type from com_export_lc a, com_export_invoice_ship_mst b  where  a.id=b.lc_sc_id and b.is_lc=1 and b.lc_sc_id in($file_lc_id) and b.status_active=1 and b.is_deleted=0 and b.id not in($lc_submit_invoice_id) 
	union all
		select a.internal_file_no, b.id, b.commission, b.invoice_value, b.net_invo_value, 2 as type from com_sales_contract a, com_export_invoice_ship_mst b where  a.id=b.lc_sc_id and b.is_lc=2 and b.lc_sc_id in($file_sc_id) and b.status_active=1 and b.is_deleted=0 and b.id not in($sc_submit_invoice_id)  ";die;*/
	
	$sql_unsubmit_invoice=sql_select("select a.internal_file_no, b.id, b.commission, b.invoice_value, b.net_invo_value, 1 as type from com_export_lc a, com_export_invoice_ship_mst b  where  a.id=b.lc_sc_id and b.is_lc=1 and b.lc_sc_id in($file_lc_id) and b.status_active=1 and b.is_deleted=0 and b.id not in($lc_submit_invoice_id) 
	union all
		select a.internal_file_no, b.id, b.commission, b.invoice_value, b.net_invo_value, 2 as type from com_sales_contract a, com_export_invoice_ship_mst b where  a.id=b.lc_sc_id and b.is_lc=2 and b.lc_sc_id in($file_sc_id) and b.status_active=1 and b.is_deleted=0 and b.id not in($sc_submit_invoice_id)  ");
	foreach($sql_unsubmit_invoice as $row)
	{
		$file_wise_data[$row[csf("internal_file_no")]]["un_sub_inv_value"]+=$row[csf("net_invo_value")];
	}
	
	$sql_realization=sql_select("select a.internal_file_no, d.id as realize_id, c.doc_submission_mst_id as sub_id, e.document_currency, (case when e.account_head=5 then e.document_currency else 0 end) as dfc_document_currency, 1 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_proceed_realization d,  com_export_proceed_rlzn_dtls e  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.invoice_bill_id and d.id=e.mst_id and c.is_lc=1 and c.lc_sc_id in($file_lc_id) and d.is_invoice_bill=1 and d.invoice_bill_id in($lc_submission_id) and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
	union all
		select a.internal_file_no, d.id as realize_id, c.doc_submission_mst_id as sub_id, e.document_currency, (case when e.account_head=5 then e.document_currency else 0 end) as dfc_document_currency, 2 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_proceed_realization d,  com_export_proceed_rlzn_dtls e  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.invoice_bill_id and d.id=e.mst_id and c.is_lc=2 and c.lc_sc_id in($file_sc_id) and d.is_invoice_bill=1 and d.invoice_bill_id in($sc_submission_id) and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0");
	$lc_realize_id=$sc_realize_id="";
	foreach($sql_realization as $row)
	{
		if($row[csf("type")]==1) $lc_realize_id.=$row[csf("realize_id")].","; else $sc_realize_id.=$row[csf("realize_id")].",";
		if($row[csf("type")]==1) $reduce_lc_sub_id.=$row[csf("sub_id")].","; else $reduce_sc_sub_id.=$row[csf("sub_id")].",";
		$file_wise_data[$row[csf("internal_file_no")]]["realize_amt"]+=$row[csf("document_currency")];
		$file_wise_data[$row[csf("internal_file_no")]]["dfc_document_currency"]+=$row[csf("dfc_document_currency")];
	}
	$reduce_lc_sub_id=chop($reduce_lc_sub_id," , ");$reduce_sc_sub_id=chop($reduce_sc_sub_id," , ");
	if($reduce_lc_sub_id=="") $reduce_lc_sub_id=0;if($reduce_sc_sub_id=="") $reduce_sc_sub_id=0;
	
	
	
	$sql_realization_fc=sql_select("select a.internal_file_no, c.id as realize_id, d.document_currency, (case when d.account_head=5 then d.document_currency else 0 end) as dfc_document_currency, 1 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_proceed_realization c,  com_export_proceed_rlzn_dtls d  where  a.id=b.lc_sc_id and b.id=c.invoice_bill_id  and c.id=d.mst_id and b.is_lc=1 and b.lc_sc_id in($file_lc_id) and c.is_invoice_bill=2 and c.invoice_bill_id in($lc_invoice_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	union all
		select a.internal_file_no, c.id as realize_id, d.document_currency, (case when d.account_head=5 then d.document_currency else 0 end) as dfc_document_currency, 2 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_proceed_realization c,  com_export_proceed_rlzn_dtls d  where  a.id=b.lc_sc_id and b.id=c.invoice_bill_id  and c.id=d.mst_id and b.is_lc=2 and b.lc_sc_id in($file_sc_id) and c.is_invoice_bill=2 and c.invoice_bill_id in($sc_invoice_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");
	$lc_realize_id=$sc_realize_id="";
	foreach($sql_realization_fc as $row)
	{
		if($row[csf("type")]==1) $lc_realize_id.=$row[csf("realize_id")].","; else $sc_realize_id.=$row[csf("realize_id")].",";
		$file_wise_data[$row[csf("internal_file_no")]]["realize_amt"]+=$row[csf("document_currency")];
		$file_wise_data[$row[csf("internal_file_no")]]["dfc_document_currency"]+=$row[csf("dfc_document_currency")];
	}
	
	
	//***************submission data reducing realize *************	
	$sql_submission=sql_select("select a.internal_file_no, c.invoice_id, d.id as submission_id, (case when d.submit_type=1 then c.net_invo_value else 0 end) as sub_collection, (case when d.submit_type=2 then c.net_invo_value else 0 end) as sub_purchase, (case when d.submit_type=2 then d.total_negotiated_amount else 0 end) as sub_negotiate, 1 as type from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and c.is_lc=1 and c.lc_sc_id in($file_lc_id) and c.invoice_id in($lc_invoice_id) and d.id not in($reduce_lc_sub_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=40 
	union all
		select a.internal_file_no, c.invoice_id, d.id as submission_id, (case when d.submit_type=1 then c.net_invo_value else 0 end) as sub_collection, (case when d.submit_type=2 then c.net_invo_value else 0 end) as sub_purchase, (case when d.submit_type=2 then d.total_negotiated_amount else 0 end) as sub_negotiate, 2 as type from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  where  a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and c.is_lc=2 and c.lc_sc_id in($file_sc_id) and c.invoice_id in($sc_invoice_id) and d.id not in($reduce_sc_sub_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=40");
	foreach($sql_submission as $row)
	{
		$file_wise_data[$row[csf("internal_file_no")]]["sub_collection"]+=$row[csf("sub_collection")];
		$file_wise_data[$row[csf("internal_file_no")]]["sub_purchase"]+=$row[csf("sub_purchase")];
		$file_wise_data[$row[csf("internal_file_no")]]["sub_negotiate"]+=$row[csf("sub_negotiate")];
	}
	
	
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
	ob_start();
	?>
    <div style="width:1388px">
        <table width="1388" cellpadding="0" cellspacing="0" id="caption">
        <tr>
        <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
        </tr> 
        <tr>  
        <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr>
        </table>
        <br />
        <table width="1360" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="130">Bank Name</th>
                    <th width="70">File No</th>
                    <th width="80">LC/SC Value</th>
                    <th width="80">TTL Shipped Value</th>
                    <th width="80">Due Shipment</th>
                    <th width="80">Realize Amount</th>
                    <th width="80">UN-Realize Amount</th>
                    <th width="80">TTL B TO LC</th>
                    <th width="80">BTB Open%</th>
                    <th width="80">Kept Fc Amount</th>
                    <th width="80">Sub Under Purchase</th>
                    <th width="80">Sub Under Collection</th>
                    <th width="80">Pending / Inhand</th>
                    <th width="80">BH</th>
                    <th width="80">Negotiate Value</th>
                    <th >Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1388px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
        <table width="1360" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <tbody>
            <?
           
            //echo $sql;
            $k=1;
            foreach($file_wise_data as $file_no=>$row)
            {
                if ($k%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                
                $due_ship=$btb_open_percent=0;
				$due_ship=$row["lc_sc_value"]-$row["net_invo_value"];
				$btb_open_percent=(($row["btb_amt"]/$row["lc_sc_value"])*100)
                
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                    <td width="30" align="center"><p><? echo $k; ?></p></td>
                    <td width="130"><p><? echo $lein_bank_arr[$row["lien_bank"]]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row["internal_file_no"]; ?></p></td>
                    <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $file_no; ?>','LC SC Info','lc_sc_popup');" ><? echo number_format($row["lc_sc_value"],2); $total_lc_vlaue+=$row["lc_sc_value"]; ?></a></p></td>
                    <td width="80" align="right"><p><? echo number_format($row["net_invo_value"],2); $total_ship_value+=$row["net_invo_value"]; ?></p></td>
                    <td width="80" align="right" title="LC Value-Invoice Value"><p><? echo number_format($due_ship,2); $total_due_ship+=$due_ship; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row["realize_amt"],2); $total_realize+=$row["realize_amt"];?></p></td>
                    <td width="80" align="right" title="Sub Under Purchase+Sub Under Collection"><p><? $un_realize_amt=$row["sub_purchase"]+$row["sub_collection"]; echo number_format($un_realize_amt,2); $total_un_realize+=$un_realize_amt;?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row["btb_amt"],2); $total_btb+=$row["btb_amt"]; ?></p></td>
                    <td width="80" align="right" title="BTB Value/LC Value*100"><p><? echo number_format($btb_open_percent,2);  ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row["dfc_document_currency"],2); $total_dfc+=$row["dfc_document_currency"]; ?></p></td>
                    <td width="80" align="right" title="auto reduce when Bill realized"><p><? echo number_format($row["sub_purchase"],2); $total_sub_purchase+=$row["sub_purchase"]; ?></p></td>
                    <td width="80" align="right" title="auto reduce when Bill realized"><p><? echo number_format($row["sub_collection"],2); $total_sub_collection+=$row["sub_collection"]; ?></p></td>
                    <td width="80" align="right" title="Un-Submitted Invoice Value"><p><? echo number_format($row["un_sub_inv_value"],2); $total_un_sub_value+=$row["un_sub_inv_value"]; ?></p></td>
                    <td width="80" align="right" title="Buying House Commission"><p><? echo number_format($row["commission"],2); $total_commission+=$row["commission"]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row["sub_negotiate"],2); $total_sub_negotiate+=$row["sub_negotiate"]; ?></p></td>
                    <td ><p></p></td>
                </tr>
                <?
                $k++;
            }
            //print_r($sc_value_1_3);
            
            ?>
            </tbody>
        </table>
     </div>
         <table width="1360" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
            <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="70" align="right">Total</th>
                <th width="80" align="right" id="value_total_lc_vlaue"><? echo number_format($total_lc_vlaue,2); ?></th>
                <th width="80" align="right" id="value_total_ship_value"><? echo number_format($total_ship_value,2); ?></th>
                <th width="80" align="right" id="value_total_due_ship"><? echo number_format($total_due_ship,2); ?></th>
                <th width="80" align="right" id="value_total_realize"><? echo number_format($total_realize,2); ?></th>
                <th width="80" align="right" id="value_total_un_realize"><? echo number_format($total_un_realize,2); ?></th>
                <th width="80" align="right" id="value_total_btb"><? echo number_format($total_btb,2); ?></th>
                <th width="80" align="right">&nbsp;</th>
                <th width="80" align="right" id="value_total_dfc"><? echo number_format($total_dfc,2); ?></th>
                <th width="80" align="right" id="value_total_sub_purchase"><? echo number_format($total_sub_purchase,2); ?></th>
                <th width="80" align="right" id="value_total_sub_collection"><? echo number_format($total_sub_collection,2); ?></th>
                <th width="80" align="right" id="value_total_un_sub_value"><? echo number_format($total_un_sub_value,2); ?></th>
                <th width="80" align="right" id="value_total_commission"><? echo number_format($total_commission,2); ?></th>
                <th width="80" align="right" id="value_total_sub_negotiate"><? echo number_format($total_sub_negotiate,2); ?></th>
                <th >&nbsp;</th>
            </tr>
            </tfoot>
        </table>
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

if($action=="lc_sc_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$file_no=str_replace("'","",$file_no);
	?>
	<script>
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	*/
	</script>	
	<!--<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>-->
    <div id="report_container" align="center" style="width:500px">
	<fieldset style="width:500px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="300">Particulars</th>
                    <th >Value</th>
                </thead>
                <tbody>
					<?
                    $sql_sc=sql_select("select  internal_file_no, sum(case when convertible_to_lc in(1,3) then contract_value else 0 end) as fin_lc_sc_value, sum(case when convertible_to_lc=2 then contract_value else 0 end) as derect_sc_value, sum(contract_value) as sc_value from com_sales_contract  where status_active=1 and is_deleted=0 and internal_file_no='$file_no' group by  internal_file_no");
                    $sql_lc=sql_select("select internal_file_no, sum(case when replacement_lc=1 then lc_value else 0 end) as replace_lc_value, sum(case when replacement_lc=2 then lc_value else 0 end) as derect_lc_value from com_export_lc  where status_active=1 and is_deleted=0 and internal_file_no='$file_no'  group by  internal_file_no");
                    ?>
                    <tr>
                        <td align="right">Sales Contact (Finance/Lc-Sc) : &nbsp;</td>
                        <td align="right"><p><? echo number_format($sql_sc[0][csf('fin_lc_sc_value')],2); ?></p></td>
                    </tr>
                    <tr>
                        <td align="right">Replacement(Lc/Sc) :&nbsp;</td>
                        <td align="right"><p><? echo number_format($sql_lc[0][csf('replace_lc_value')],2); ?></p></td>
                    </tr>
                    <tr style="font-size:14px; font-weight:bold;">
                        <td align="right">Balance :&nbsp;</td>
                        <td align="right"><p><? $balance=$sql_sc[0][csf('fin_lc_sc_value')]-$sql_lc[0][csf('replace_lc_value')]; echo number_format($balance,2); ?></p></td>
                    </tr>
                    <tr>
                        <td align="right">Salse Contact(Direct) :&nbsp;</td>
                        <td align="right"><p><? echo number_format($sql_sc[0][csf('derect_sc_value')],2); ?></p></td>
                    </tr>
                    <tr>
                        <td align="right">Lc value (Direct) :&nbsp;</td>
                        <td align="right"><p><? echo number_format($sql_lc[0][csf('derect_lc_value')],2); ?></p></td>
                    </tr>
                    <tr style="font-size:15px; font-weight:bold;">
                        <td align="right">Total File Value :&nbsp;</td>
                        <td align="right"><p><? $file_value=$sql_lc[0][csf('replace_lc_value')]+$balance+$sql_sc[0][csf('derect_sc_value')]+$sql_lc[0][csf('derect_lc_value')]; echo number_format($file_value,2); ?></p></td>
                    </tr>
                </tbody>   
            </table>
        </fieldset>
    </div>
	<?
    exit();

}










if ($action=="btb_open")
{
	
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$hidden_btb_id=str_replace("'","",$hidden_btb_id);
	$file_buyer=explode("*",str_replace("'","",$file_buyer));
	$file_no=$file_buyer[0];
	$buyer_name=$buyer_name_arr[$file_buyer[1]];
	//echo $hidden_btb_id;die;
	
	$sql= "select 
				a.id,
				a.lc_number,
				a.lc_date,
				a.lc_value,
				a.pi_id,
				a.supplier_id,
				a.item_category_id
			from
				 com_btb_lc_master_details a
			where
				a.id in($hidden_btb_id) and a.is_deleted=0 and a.status_active=1 ";
				//echo $sql;
	$sql_result=sql_select($sql);
	?>
<script>
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title>BTB Open</title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body><div style="width:820px; margin-top:20px;"><? echo "<b>File No: " .$file_no."&nbsp;&nbsp;&nbsp;&nbsp; Buyer Name: ".$buyer_name."</b><br>&nbsp;<br>"; ?></div>'+document.getElementById('popup_body').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="320px";
	
	}
	
	
</script>
    
    <table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr><td align="center"><input type="button" class="formbutton" onClick="new_window()" style="width:100px;" value="Print" ></td></tr>
    </table><br>
    <div id="popup_body" style="width:820px;">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
        	<tr>
            	<th width="50">SL</th>
                <th width="130">BTB Lc No</th>
                <th width="80">Lc Date</th>
                <th width="100">Amount</th>
                <th width="150">PI No.</th>
                <th width="150">Supplier</th>
                <th >Item Cetagory</th>	
            </tr>
        </thead>
    </table>
    <div style="width:820px; max-height:320px; overflow-y:scroll" id="scroll_body">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <tbody>
        <?
		$i=1;
		foreach($sql_result as $row)
		{
		if ($i%2==0)
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";
		
		?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td width="50"><?  echo $i; ?></td>
                <td width="130"><?  echo $row[csf("lc_number")]; ?></td>
                <td width="80" align="center"><? if($row[csf("lc_date")]!='0000-00-00')  echo change_date_format($row[csf("lc_date")]); else echo ""; ?></td>
                <td  width="100" align="right"><?  echo number_format($row[csf("lc_value")],2);  $total_val+=$row[csf("lc_value")];?></td>
                <td width="150">
				<p><?
				  $po_id=explode(",",$row[csf("pi_id")]);
				  $k=1;
				  foreach($po_id as $row_po_id)
				  {
					  if($k!=1) echo ", ";
					  echo  $pi_no_arr[$row_po_id];
					$k++;  
				  }
				?></p>
                </td>
                <td width="150"><?  echo $suplier_name_arr[$row[csf("supplier_id")]]; ?></td>
                <td><?  echo $item_category[$row[csf("item_category_id")]]; ?></td>	
            </tr>
        <?
		$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th><? echo number_format($total_val,2); ?> </th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>	
            </tr>
        </tfoot>
	</table>
    </div>
    </div>
	<?
exit(); 
}
disconnect($con);
?>
